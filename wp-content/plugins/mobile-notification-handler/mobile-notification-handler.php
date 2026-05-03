<?php
/**
 * Plugin Name: Mobile Notification Handler
 * Description: Sistem za zbiranje žetonov z avtomatskim upravljanjem sw.js datoteke.
 * Version: 1.4
 * Author: Peter Draučbaher
 */

if (!defined('ABSPATH')) exit;

class MobileNotificationHandler {

    private $table_name;
    private $sw_dest;
    private $sw_src;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mobile_push_subscriptions';
        $this->sw_src = plugin_dir_path(__FILE__) . 'sw.js';
        $this->sw_dest = ABSPATH . 'sw.js';

        // Aktivacija in deaktivacija
        register_activation_hook(__FILE__, array($this, 'activation_logic'));
        register_deactivation_hook(__FILE__, array($this, 'deactivation_logic'));

        add_action('rest_api_init', array($this, 'register_endpoints'));
        add_action('admin_menu', array($this, 'add_admin_info'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_push_script'));
        add_action('admin_post_trigger_push', array($this, 'procesiraj_ročno_sprožitev'));
        add_action('publish_post', array($this, 'avtomatsko_poslji_ob_objavi'), 10, 2);
    }

    /**
     * LOGIKA OB AKTIVACIJI
     */
    public function activation_logic() {
        $this->install_table();
        $this->kopiraj_sw_v_root();
    }

    /**
     * LOGIKA OB DEAKTIVACIJI
     */
    public function deactivation_logic() {
        $this->pobrisi_sw_iz_roota();
    }

    private function install_table() {
        global $wpdb;
        $table_name = $this->table_name; // Uporabi lokalno spremenljivko
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            endpoint text NOT NULL,
            p256dh varchar(255) NOT NULL,
            auth varchar(255) NOT NULL,
            user_agent text,
            source varchar(50) DEFAULT 'web',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;"; // Pazi: dva presledka med PRIMARY KEY in (id)

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        if (!get_option('mnh_vapid_public_key')) {
            update_option('mnh_vapid_public_key', 'BH7HqdsAzZtOSHZ5PG-MSJph5016vBSKuXnkcHhWkJa46uk8rKtI7vXVyzqscIcVErotBLiOSyn95JWk_1UwQXg');
            update_option('mnh_vapid_private_key', '8E4Ka6f3WybZGv-Iu9XVOJSgBSRUgbO9VEsG2f2kc_4');
        }

        // Za vsak primer dodaj še to, da preveriš, če se je koda sploh izvedla
        error_log("MNH install_table poklicana za tabelo: $table_name");
    }

    private function kopiraj_sw_v_root() {
        if (!file_exists($this->sw_src)) {
            wp_die('Napaka: Datoteka sw.js ne obstaja v mapi vtičnika. Prosim, dodaj jo pred aktivacijo.');
        }

        // Poskus kopiranja
        $success = @copy($this->sw_src, $this->sw_dest);

        if (!$success) {
            wp_die('Napaka: Vtičnik ne more kopirati sw.js v korensko mapo (root). Preveri pravice pisanja (permissions) na strežniku za mapo ' . ABSPATH);
        }
    }

    private function pobrisi_sw_iz_roota() {
        if (file_exists($this->sw_dest)) {
            @unlink($this->sw_dest);
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
        echo '</form></div>';
        echo '<p style="margin-top:20px;">Fizična lokacija sw.js: <code>' . (file_exists($this->sw_dest) ? 'V korenski mapi (V REDU)' : 'Manjka!') . '</code></p>';
        echo '</div>';
    }

    public function procesiraj_ročno_sprožitev() {
        if (!isset($_POST['mnh_nonce']) || !wp_verify_nonce($_POST['mnh_nonce'], 'mnh_trigger_action')) wp_die('Security check failed.');
        if (!current_user_can('manage_options')) wp_die('No permission.');
        $this->poslji_push_logika("Ročni test", "Test");
        wp_redirect(admin_url('admin.php?page=mnh-stats&status=success'));
        exit;
    }

    public function avtomatsko_poslji_ob_objavi($ID, $post) {
        if ($post->post_type !== 'post') return;
        if (wp_is_post_revision($ID) || get_post_meta($ID, '_push_sent', true)) return;
        $this->poslji_push_logika($post->post_title, $ID);
        update_post_meta($ID, '_push_sent', '1');
    }

    public function poslji_push_logika($naslov, $post_id) {
        // Tukaj pride tvoja WebPush koda...
        error_log("Push Signal sprožen za: " . $naslov);
    }
}

new MobileNotificationHandler();