<?php
namespace MNHPush\Core;

class Database {
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mnh_push_subscribers';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            endpoint text NOT NULL,
            p256dh varchar(255) NOT NULL,
            auth varchar(255) NOT NULL,
            user_agent text,
            source varchar(50) DEFAULT 'desktop',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY endpoint_idx (endpoint(191))
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Kopiranje SW.js v root
        $sw_src = MNH_PATH . 'sw.js';
        $sw_dest = ABSPATH . 'sw.js';
        if (file_exists($sw_src)) {
            @copy($sw_src, $sw_dest);
        }
    }

    public static function deactivate() {
        if (file_exists(ABSPATH . 'sw.js')) {
            @unlink(ABSPATH . 'sw.js');
        }
        // Opcijsko: Preveri cleanup request za DROP TABLE
    }
}