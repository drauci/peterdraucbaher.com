<?php
/**
 * Variation Swatch
 *
 * WooCommerce Variation Swatch Plugin
 * 
 * @since 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Variation_Swatch' ) ) :

	class Porto_Variation_Swatch {
		public function __construct() {
            add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ), 1000 );
			add_filter( 'admin_body_class', array( $this, 'body_class' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 20 );
		}

		/**
         * Enqueue styles
         *
         * @since 7.2.0
         */
        public function enqueue_styles() {
			if ( defined( 'WOO_VARIATION_SWATCHES_PRO_PLUGIN_VERSION' ) ) {
				wp_enqueue_style( 'porto-wvs', PORTO_LIB_URI . '/variation-swatch/wvs.css', array(), PORTO_VERSION );
			}
        }

		
        public function body_class( $class ) {
            return $class . ' woo-variation-swatches';
        }

		/**
		 * Load assets for variation swatch thumbnails
		 */
		public function enqueue() {
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_style( 'woo-variation-swatches', woo_variation_swatches()->assets_url( "/css/frontend{$suffix}.css" ), array(), woo_variation_swatches()->assets_version( "/css/frontend{$suffix}.css" ) );
		}

	}
endif;

new Porto_Variation_Swatch;
