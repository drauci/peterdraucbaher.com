<?php
/**
 * Plugin Name: Mobile Notification Handler
 * Description:
 * Version: 1.0
 * Author: Peter Draučbaher
 */

if (!defined('ABSPATH')) exit;

class MobileNotificationHandler {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mobile_push_subscriptions';

        // Aktivacija: Ustvari bazo
        register_activation_hook(__FILE__, array($this, 'install'));

        // API: Vhodna točka za žetone
        add_action('rest_api_init', array($this, 'register_endpoints'));

        // Admin: Prikaz števila zbranih naslovov (da vidiš svojo vojsko)
        add_action('admin_menu', array($this, 'add_admin_info'));
    }

    /**
     * Ustvari tabelo za shranjevanje žetonov
     */
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
    }

    /**
     * Registracija API poti: /wp-json/mnh/v1/subscribe
     */
    public function register_endpoints() {
        register_rest_route('mnh/v1', '/subscribe', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_subscription'),
            'permission_callback' => '__return_true', // Omogoča dostop aplikaciji in JS
        ));
    }

    /**
     * Sprejme žeton in ga shrani/posodobi
     */
    public function handle_subscription($request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['endpoint'])) {
            return new WP_REST_Response(['error' => 'Manjka endpoint'], 400);
        }

        $result = $wpdb->replace($this->table_name, array(
            'endpoint'   => $params['endpoint'],
            'p256dh'     => isset($params['keys']['p256dh']) ? $params['keys']['p256dh'] : '',
            'auth'       => isset($params['keys']['auth']) ? $params['keys']['auth'] : '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'source'     => isset($params['source']) ? $params['source'] : 'web'
        ));

        if ($result) {
            return new WP_REST_Response(['success' => true, 'id' => $wpdb->insert_id], 200);
        }

        return new WP_REST_Response(['error' => 'Napaka pri shranjevanju'], 500);
    }

    /**
     * Preprost dashboard v WP adminu
     */
    public function add_admin_info() {
        add_menu_page(
            'Mobile Push',
            'Mobile Push',
            'manage_options',
            'mnh-stats',
            array($this, 'render_admin_page'),
            'dashicons-rss'
        );
    }

    public function render_admin_page() {
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $this->table_name");
        $sources = $wpdb->get_results("SELECT source, COUNT(*) as st FROM $this->table_name GROUP BY source");

        echo '<div class="wrap"><h1>Status</h1>';
        echo '<p>Trenutno imaš <strong>' . esc_html($count) . '</strong> zbranih žetonov .</p>';
        echo '<table class="widefat" style="max-width:300px;"><thead><tr><th>Vir</th><th>Število</th></tr></thead><tbody>';
        foreach ($sources as $s) {
            echo "<tr><td>{$s->source}</td><td>{$s->st}</td></tr>";
        }
        echo '</tbody></table></div>';
    }
}

new MobileNotificationHandler();