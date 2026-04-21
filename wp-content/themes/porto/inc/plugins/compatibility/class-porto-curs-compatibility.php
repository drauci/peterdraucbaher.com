<?php
/**
 * Customer Reviews Summary Compatibility class
 *
 * @since 7.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_Curs_Compatibility {
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
        if ( ! porto_is_product() && ! empty( $porto_settings_optimize['optimize_curs'] ) ) {
    		wp_dequeue_style( 'ivole-frontend-css' );
			wp_dequeue_style( 'cr-frontend-css' );
            wp_dequeue_style( 'cr-badges-css' );
            wp_dequeue_script( 'cr-colcade' );
            wp_dequeue_script( 'cr-frontend-js' );
        }
	}

}

new Porto_Curs_Compatibility();
