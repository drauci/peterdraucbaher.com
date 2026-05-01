<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'peterdra' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ir3,jDd^5[,o63/Dm^@tL6Cqoy@b<AQXe$=,+MxrtU+VXX}bxiuxPg|n.b[z?t;:' );
define( 'SECURE_AUTH_KEY',  '__%/C;S8wx.(<T9xr>xuhd_m*/V2j89uKk]zjtY+vcN<KX) =/d[!|1L2JTzg>3r' );
define( 'LOGGED_IN_KEY',    'A/|I wlK!eSF89)GYnET_y[?UeLH.LewCtmGqZ._2#os-cwgZ6&P)Z.n#c%ytFx-' );
define( 'NONCE_KEY',        '=P E)xFjk`!$U*h.t6Pr4O-J|(kuJTTV[z=4W|t1UHWg{4^O6S46w{RNGr;kM_%%' );
define( 'AUTH_SALT',        'Q-4$[R2Bbuc`%UZqza8jnrA(pT7L-|`X.bvk]y:v:DTlE)o|qu>MZZBRVO8@t;m*' );
define( 'SECURE_AUTH_SALT', '$R0[)I2CV>6!4m;2dH f^i-k`mU%|?+p}*PcT]WY}*:7OPLWx[]yhH9|,aY8H)1^' );
define( 'LOGGED_IN_SALT',   '=|o@^`[Yzk!yMD6{[/rfkt},M-[):qSEui{B9!k|Z4f5NxqBRXJIb:P>jP3[=yy:' );
define( 'NONCE_SALT',       'I)kU^@Za8t3R,w-iyI7`G#j Y7WJuNS+m[Q*`2sJqHiOmrtPSxI:${H#c!PV=[05' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}


/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
