<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Compatibilty with Yith WooCommerce Product Compare
 *
 * @package porto
 * @since 7.1.11
 */

if ( ! class_exists( 'Porto_Yith_Compare' ) ) :
	class Porto_Yith_Compare {

		/**
		 * The Instance Object.
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
				return;
			}

			$this->init();
		}

		public static function get_instance() {
			if( ! self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Init
		 */
		public function init() {
			global $yith_woocompare;

			add_action( 'init', array( $this, 'update_actions' ), 50 );

			if ( isset( $yith_woocompare->obj ) && $yith_woocompare->obj instanceof YITH_Woocompare_Frontend ) {
				if ( ! wp_doing_ajax() && ( ! isset( $_REQUEST['action'] ) || 'yith-woocompare-view-table' != $_REQUEST['action'] ) ) {
					remove_action( 'template_redirect', array( $yith_woocompare->obj, 'compare_table_html' ) );
				}
			}

			if ( is_admin() ) {
				add_filter( 'yith_woocompare_general_settings', array( $this, 'porto_check_compare_option' ) );
			} else {
				add_action( 'wp', array( $this, 'compare_position' ), 20 );
			}

			if ( 'yes' == get_option( 'yith_woocompare_compare_button_in_products_list', 'yes' ) ) {
				add_action( 'porto_template_compare', 'porto_template_loop_compare' );
			}
			
			/**
			 * Remove default button of Compare icon
			 */
			add_filter( 'yith_woocompare_remove_compare_link_by_cat', '__return_true' );
		}

		/**
		 * Remove Yith actions
		 */
		public function update_actions() {
			global $yith_woocompare;

			if ( isset( $yith_woocompare->obj ) && $yith_woocompare->obj instanceof YITH_Woocompare_Frontend ) {
				remove_action( 'woocommerce_after_shop_loop_item', array( $yith_woocompare->obj, 'add_compare_link' ), 20 );
				remove_action( 'woocommerce_single_product_summary', array( $yith_woocompare->obj, 'add_compare_link' ), 35 );
			}

			if ( wp_doing_ajax() ) {
				if ( isset( $_REQUEST['action'] ) && 'porto_product_quickview' == $_REQUEST['action'] ) {
					add_action( 'woocommerce_single_product_summary', 'porto_template_loop_compare', 100 );
				}
			}
		}

		/**
		 * Update compare position
		 */
		public function compare_position() {
			global $porto_product_layout;

			if ( ! porto_check_builder_condition( 'product' ) ) {
				if ( 'yes' == get_option( 'yith_woocompare_compare_button_in_product_page', 'yes' ) ) {
					if ( in_array( $porto_product_layout, array( 'extended', 'full_width', 'sticky_info', 'sticky_both_info', 'centered_vertical_zoom' ) ) ) {
						add_action( 'woocommerce_after_add_to_cart_button', 'porto_template_loop_compare', 40 );
					} else {
						add_action( 'woocommerce_single_product_summary', 'porto_template_loop_compare', 100 );
					}
				}
			}
		}

		/**
		 * Check Compare General options
		 */
		public function porto_check_compare_option( $compare_options ) {
			if ( ! empty( $compare_options['general'] ) ) {
				foreach ( $compare_options['general'] as $key => $option ) {
					if ( 'yith_woocompare_is_button' == $option['id'] ) {
						$option['desc']                     = sprintf( esc_html__( 'Choose if you want to use a link or a button for the compare actions. %1$s %2$sThis option isn\'t available for %3$sPorto%4$s %5$s.', 'porto' ), '<br>', '<span class="porto-warning-notice">', '<a href="https://www.portotheme.com/wordpress/porto/" target="_blank"><b>', '</b></a>', '</span>' );
						$compare_options['general'][ $key ] = $option;
					} elseif ( 'yith_woocompare_button_text' == $option['id'] ) {
						$option['desc']                     = sprintf( esc_html__( 'Type the text to use for the button or the link of the compare. %1$s %2$sThis option isn\'t available for %3$sPorto%4$s %5$s.', 'porto' ), '<br>', '<span class="porto-warning-notice">', '<a href="https://www.portotheme.com/wordpress/porto/" target="_blank"><b>', '</b></a>', '</span>' );
						$compare_options['general'][ $key ] = $option;
					}
				}
			}
			return $compare_options;
		}
	}

	Porto_Yith_Compare::get_instance();
endif;
