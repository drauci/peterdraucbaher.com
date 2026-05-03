<?php
namespace MNHPush\Public;

use WP_REST_Response;
use WP_Error;

class RestApi {

    public function __construct() {
        // Registracija poti ob inicializaciji REST API-ja
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Registracija endpointa: /wp-json/mnh/v1/subscribe
     */
    public function register_routes() {
        register_rest_route('mnh/v1', '/subscribe', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_subscription'],
            'permission_callback' => '__return_true', // Javni dostop, saj se naročajo obiskovalci
        ]);
    }

    /**
     * Obdelava prejetih podatkov iz brskalnika
     */
    public function handle_subscription($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mnh_push_subscribers';
        $params = $request->get_json_params();

        // 1. Osnovna validacija
        if (empty($params['endpoint'])) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Manjka endpoint naročnine.'
            ], 400);
        }

        // 2. Sanitizacija in priprava podatkov
        $data = [
            'endpoint'   => sanitize_text_field($params['endpoint']),
            'p256dh'     => sanitize_text_field($params['keys']['p256dh'] ?? ''),
            'auth'       => sanitize_text_field($params['keys']['auth'] ?? ''),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'Neznano',
            'source'     => sanitize_text_field($params['source'] ?? 'desktop'),
            'created_at' => current_time('mysql')
        ];

        // 3. Shranjevanje v bazo (REPLACE uporabi UNIQUE KEY na endpointu, da prepreči podvajanje)
        $result = $wpdb->replace($table_name, $data);

        if ($result !== false) {
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Naročnina uspešno shranjena.'
            ], 200);
        }

        // Če pride do napake pri zapisu v bazo
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Napaka pri shranjevanju v bazo podatkov.'
        ], 500);
    }
}