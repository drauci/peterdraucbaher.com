<?php
/**
 * Plugin Name: Mobile Notification Handler
 * Description: Sistem za tiho zbiranje žetonov, ročno sprožitev in avtomatsko pošiljanje ob objavi članka.
 * Version: 1.3
 * Author: Peter Draučbaher
 */

if (!defined('ABSPATH')) exit;

class MobileNotificationHandler {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mobile_push_subscriptions';

        register_activation_hook(__FILE__, array($this, 'install'));
        add_action('rest_api_init', array($this, 'register_endpoints'));
        add_action('admin_menu', array($this, 'add_admin_info'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_push_script'));

        // 1. Ročna sprožitev preko gumba
        add_action('admin_post_trigger_push', array($this, 'procesiraj_ročno_sprožitev'));

        // 2. Avtomatska sprožitev ob objavi novega članka
        add_action('publish_post', array($this, 'avtomatsko_poslji_ob_objavi'), 10, 2);
    }

    public function install() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $this->table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            endpoint text NOT NULL,
            p256dh varchar(255) NOT NULL,
            auth varchar(255) NOT NULL,
            user_agent text,
            source varchar(50) DEFAULT 'web',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY endpoint (endpoint(255))
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        if (!get_option('mnh_vapid_public_key')) {
            update_option('mnh_vapid_public_key', 'BCV_STAVI_TUKAJ_SVOJ_JAVNI_KLJUC');
            update_option('mnh_vapid_private_key', 'STAVI_TUKAJ_SVOJ_ZASEBNI_KLJUC');
        }
    }

    public function register_endpoints() {
        register_rest_route('mnh/v1', '/subscribe', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_subscription'),
            'permission_callback' => '__return_true',
        ));
    }

    public function handle_subscription($request) {
        global $wpdb;
        $params = $request->get_json_params();
        if (empty($params['endpoint'])) return new WP_REST_Response(['error' => 'No endpoint'], 400);
        $wpdb->replace($this->table_name, array(
            'endpoint'   => $params['endpoint'],
            'p256dh'     => $params['keys']['p256dh'],
            'auth'       => $params['keys']['auth'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'source'     => $params['source']
        ));
        return new WP_REST_Response(['success' => true], 200);
    }

    public function enqueue_push_script() {
        wp_register_script('mnh-push-collector', false);
        wp_enqueue_script('mnh-push-collector');
        $public_key = get_option('mnh_vapid_public_key');
        $inline_js = "
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(reg) {
                    reg.pushManager.getSubscription().then(function(sub) {
                        if (!sub) {
                            const pubKey = '$public_key';
                            reg.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: pubKey
                            }).then(function(newSub) {
                                const key = btoa(String.fromCharCode.apply(null, new Uint8Array(newSub.getKey('p256dh'))));
                                const auth = btoa(String.fromCharCode.apply(null, new Uint8Array(newSub.getKey('auth'))));
                                fetch('" . esc_url_raw(rest_url('mnh/v1/subscribe')) . "', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({
                                        endpoint: newSub.endpoint,
                                        keys: { p256dh: key, auth: auth },
                                        source: window.navigator.userAgent.includes('Mobile') ? 'app_or_mobile' : 'web'
                                    })
                                });
                            });
                        }
                    });
                });
            });
        }";
        wp_add_inline_script('mnh-push-collector', $inline_js);
    }

    public function add_admin_info() {
        add_menu_page('Mobile Push', 'Mobile Push', 'manage_options', 'mnh-stats', array($this, 'render_admin_page'), 'dashicons-rss');
    }

    public function render_admin_page() {
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $this->table_name");
        echo '<div class="wrap"><h1>Status mobilne vojske</h1>';
        echo '<div class="card" style="max-width: 500px; padding: 20px;">';
        echo '<p style="font-size: 1.2rem;">Trenutno število spečih naprav: <strong>' . esc_html($count) . '</strong></p>';
        echo '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post">';
        echo '<input type="hidden" name="action" value="trigger_push">';
        wp_nonce_field('mnh_trigger_action', 'mnh_nonce');
        echo '<input type="submit" class="button button-primary button-large" value="SPROŽI TESTNO OBVESTILO VSEM" onclick="return confirm(\'Ali si prepričan?\');">';
        echo '</form></div></div>';
    }

    public function procesiraj_ročno_sprožitev() {
        if (!isset($_POST['mnh_nonce']) || !wp_verify_nonce($_POST['mnh_nonce'], 'mnh_trigger_action')) wp_die('Security check failed.');
        if (!current_user_can('manage_options')) wp_die('No permission.');

        // Ročni klic splošne logike
        $this->poslji_push_logika("Ročni test", "To je testno obvestilo sproženo iz admina.");

        wp_redirect(admin_url('admin.php?page=mnh-stats&status=success'));
        exit;
    }

    /**
     * Samodejno se sproži IZKLJUČNO ob objavi novega ČLANKA (post)
     */
    public function avtomatsko_poslji_ob_objavi($ID, $post) {
        // 1. Preveri, če je tip vsebine 'post' (članek). Če je stran (page), prekini.
        if ($post->post_type !== 'post') {
            return;
        }

        // 2. Prepreči pošiljanje ob revizijah ali če je bilo že poslano
        if (wp_is_post_revision($ID) || get_post_meta($ID, '_push_sent', true)) {
            return;
        }

        // 3. Pošlji gole podatke v tvojo glavno logiko
        // Pošljemo naslov in ID, da lahko v logiki izvlečeš karkoli (permalink, kategorije...)
        $this->poslji_push_logika($post->post_title, $ID);

        // 4. Označi, da je bilo poslano, da se ne ponovi ob urejanju
        update_post_meta($ID, '_push_sent', '1');
    }

    /**
     * GLAVNA PRAZNA FUNKCIJA - Tukaj boš sam sprogramiral pošiljanje
     * $podatek1 = Naslov članka
     * $podatek2 = ID članka (uporabiš za get_permalink($podatek2))
     */
    public function poslji_push_logika($naslov, $post_id) {
        global $wpdb;

        // Tvoja koda pride tukaj...
        // Primer: $url = get_permalink($post_id);

        error_log("Push Signal za članek ID $post_id: " . $naslov);
    }
}

new MobileNotificationHandler();