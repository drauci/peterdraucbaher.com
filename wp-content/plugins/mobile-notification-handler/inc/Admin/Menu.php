<?php
namespace MNHPush\Admin;

class Menu {
    public function __construct() {
        add_action('admin_menu', [$this, 'init_menu']);
    }

    public function init_menu() {
        add_menu_page('MNH Stats', 'Mobile Push', 'manage_options', 'mnh-stats', [new StatsPage(), 'render'], 'dashicons-rss', 81);
        add_submenu_page('mnh-stats', 'Nastavitve', 'Nastavitve ključev', 'manage_options', 'mnh-settings', [new SettingsPage(), 'render']);
    }
}