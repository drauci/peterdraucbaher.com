<?php
/**
 * Plugin Name: Mobile Notification Handler
 * Description: Napreden sistem za Web Push obvestila z MVC strukturo.
 * Version: 1.5
 * Author: Peter Draučbaher
 */

if (!defined('ABSPATH')) exit;

// Definiranje poti
define('MNH_PATH', plugin_dir_path(__FILE__));
define('MNH_URL', plugin_dir_url(__FILE__));

// Ročni vklop autoloaderja
require_once MNH_PATH . 'inc/Core/Autoloader.php';
MNHPush\Core\Autoloader::register();

// Inicializacija komponent
add_action('plugins_loaded', function() {
    new MNHPush\Core\Database();

    if (is_admin()) {
        new MNHPush\Admin\Menu();
    }

    new MNHPush\Public\Scripts();
    new MNHPush\Public\RestApi();
    new MNHPush\Public\Display();
});

// Hooki za aktivacijo/deaktivacijo (izven razredov zaradi zanesljivosti)
register_activation_hook(__FILE__, [MNHPush\Core\Database::class, 'activate']);
register_deactivation_hook(__FILE__, [MNHPush\Core\Database::class, 'deactivate']);