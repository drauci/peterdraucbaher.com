<?php
/**
 * Yith Wcan Compatibility class
 *
 * @since 7.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_Yith_Wcan_Compatibility {
	/**
	 * Constructor
	 *
	 * @since 7.5.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999 );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 7.5.0
	 */
	public function enqueue_scripts() {
        global $porto_settings_optimize;
		if ( porto_is_shop() || porto_is_product_archive() ) { // product archive page and single product page
			return;
		}
        if ( ! empty( $porto_settings_optimize['optimize_yith_wcan'] ) ) {
    		wp_dequeue_style( 'yith-wcan-shortcodes' );
			wp_dequeue_script( 'yith-wcan-shortcodes' );
        }
	}

}

new Porto_Yith_Wcan_Compatibility();
