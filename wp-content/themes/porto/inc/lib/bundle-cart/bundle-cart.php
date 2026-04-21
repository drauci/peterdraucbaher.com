<?php
/**
 * Porto Cart Together
 *
 * @author     Porto Themes
 *
 * @since      7.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Bundle_Cart' ) ) :
	class Porto_Bundle_Cart {
        
        private static $instance;

        private function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'wp_ajax_porto_bundle_cart', array( $this, 'cart_together' ) );
            add_action( 'wp_ajax_nopriv_porto_bundle_cart', array( $this, 'cart_together' ) );
		}

		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

        public function enqueue_scripts() {
            // Register Script
            wp_register_script( 'porto-bundle-cart', PORTO_LIB_URI . '/bundle-cart/bundle-cart.js', array(), PORTO_VERSION, true );
        }

        public function cart_together() {
            if ( isset( $_REQUEST['action'] ) && 'porto_bundle_cart' == $_REQUEST['action'] && isset( $_REQUEST['ids'] ) ) {
                $ids = explode( ',', sanitize_text_field( $_REQUEST['ids'] ) );
                foreach ( $ids as $id ) {
                    WC()->cart->add_to_cart( $id );
                }
				wp_send_json_success();
            }
            wp_send_json_error();
            die;
        }
    }
endif;

Porto_Bundle_Cart::get_instance();
