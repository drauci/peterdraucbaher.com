<?php
/**
 * Theme Core Functions
 *
 * @package Porto
 */

require_once( PORTO_FUNCTIONS . '/class-part-css.php' );

require_once( PORTO_FUNCTIONS . '/general.php' );

if ( defined( 'WPB_VC_VERSION' ) ) {
	require_once PORTO_FUNCTIONS . '/wpb-elements.php';
}

require_once( PORTO_FUNCTIONS . '/shortcodes.php' );
require_once( PORTO_FUNCTIONS . '/widgets.php' );
require_once( PORTO_FUNCTIONS . '/post.php' );

if ( class_exists( 'Woocommerce' ) ) {
	if ( porto_is_elementor_preview() ) {
		if ( version_compare( WC_VERSION, '8.4', '>=' ) && is_admin() && ! wp_doing_ajax() ) {
			add_action( 
				'load-post.php', 
				function() {
					require_once( PORTO_FUNCTIONS . '/woocommerce.php' );
				},
				15 
			);
		} else {
			add_action(
				'init',
				function() {
					require_once( PORTO_FUNCTIONS . '/woocommerce.php' );
				},
				8
			);
		}
	} else {
		require_once( PORTO_FUNCTIONS . '/woocommerce.php' );
	}
}

require_once( PORTO_FUNCTIONS . '/layout.php' );
require_once( PORTO_FUNCTIONS . '/html_block.php' );

require_once( PORTO_FUNCTIONS . '/class-dynamic-style.php' );

require_once( PORTO_FUNCTIONS . '/class-performance.php' );

/**
 * Includes integration files
 *
 * @since 7.2.0
 */
require_once PORTO_FUNCTIONS . '/integrations/class-wpml.php';
