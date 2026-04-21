<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Compatibilty with WPML plugins
 *
 * @package porto
 * @since 7.2.0
 */

if ( ! class_exists( 'Porto_WPML' ) ) :
	class Porto_WPML {

		/**
		 * Instance
		 */
		public static $instance = null;

		/**
		 * The active language
		 */
		private static $active_language = null;

		/**
		 * Is the site using WPML?
		 */
		private static $is_wpml = false;

		/**
		 * Is the site using PolyLang?
		 */
		private static $is_pll = false;

		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new Porto_WPML();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			self::$is_pll  = self::is_pll();
			self::$is_wpml = self::is_wpml();

			if ( self::$is_wpml ) {
				$this->wpml_init();
			}

			if ( defined( 'WCML_VERSION' ) ) {
				$this->wcml_init();
			}

			if ( self::$is_pll ) {
				add_action( 'init', array( $this, 'pll_init' ) );
			}

			// localize script
			if ( self::$is_pll || self::$is_wpml ) {
				add_filter( 'porto_frontend_vars', array( $this, 'set_language_vars' ) );
			}
			}

		/**
		 * Init WPML
		 */
		public function wpml_init() {

		}

		/**
		 * Init Polylang
		 */
		public function pll_init() {
			if ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX && ! empty( $_COOKIE['porto_active_lang'] ) ) {
				$lang = sanitize_text_field( $_COOKIE['porto_active_lang'] );
				$language = PLL()->model->get_language( $lang );

				if ( $language ) {
					PLL()->curlang = $language;
					do_action( 'pll_language_defined', $language->slug, $language );
				}
			}
		}

		/**
		 * Init WCML
		 */
		public function wcml_init() {
			add_filter( 'wcml_multi_currency_ajax_actions', array( $this, 'ajax_actions' ), 10, 1 );
		}

		public function ajax_actions( $ajax_actions ) {
			$ajax_actions[] = 'porto_add_to_cart';
			$ajax_actions[] = 'porto_product_quickview';
			$ajax_actions[] = 'porto_recent_sale_products';
			$ajax_actions[] = 'porto_ajax_posts';
			$ajax_actions[] = 'porto_cart_item_remove';
			$ajax_actions[] = 'porto_refresh_cart_fragment';
			$ajax_actions[] = 'porto_woocommerce_shortcodes_products';
			$ajax_actions[] = 'porto_update_cart_item';
			$ajax_actions[] = 'porto_refresh_wishlist_count';
			$ajax_actions[] = 'porto_load_wishlist';

			return $ajax_actions;
		}

		/**
		 * Set language variables
		 * 
		 * @since 7.2.4
		 */
		public function set_language_vars( $porto_vars ) {
			$lang = self::get_active_language();
			if ( $lang ) {
				$porto_vars['ajax_url']    = esc_url( add_query_arg( 'lang', $lang, admin_url( 'admin-ajax.php' ) ) );
				$porto_vars['active_lang'] = esc_js( $lang );
				setcookie( 'porto_active_lang', esc_js( $lang ), time() + 4 * HOUR_IN_SECONDS, '/' );
			}
			return $porto_vars;
		}

		/**
		 * Get active language code
		 * 
		 * @since 7.2.4
		 */
		public function get_active_language() {
			if ( null === self::$active_language ) {
				if ( ! self::$is_pll && ! self::$is_wpml ) {
					self::$active_language = '';
					return self::$active_language;
				}

				if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
					self::$active_language = ICL_LANGUAGE_CODE;
					if ( 'all' === ICL_LANGUAGE_CODE ) {
						if ( self::$is_wpml ) {
							global $sitepress;
							self::$active_language = $sitepress->get_default_language();
						} elseif ( self::$is_pll ) {
							self::$active_language = pll_default_language( 'slug' );
						}
					}
					return self::$active_language;
				}

				if ( function_exists( 'PLL' ) ) {
					$pll_obj = PLL();
					if ( is_object( $pll_obj ) && property_exists( $pll_obj, 'curlang' ) ) {
						if ( is_object( $pll_obj->curlang ) && property_exists( $pll_obj->curlang, 'slug' ) ) {
							self::$active_language = $pll_obj->curlang->slug;
						} elseif ( false === $pll_obj->curlang ) {
							self::$active_language = 'all';
						}
					}
				}
			}
			return self::$active_language;
		}

		/**
		 * Determine if the site is using WPML
		 *
		 * @since 7.2.4
		 */
		public static function is_wpml() {
			return ( ( defined( 'WPML_PLUGIN_FILE' ) || defined( 'ICL_PLUGIN_FILE' ) ) && false === self::$is_pll ) ? true : false;
		}

		/**
		 * Determine if the site is using PolyLang
		 *
		 * @since 7.2.4
		 */
		public static function is_pll() {
			if ( function_exists( 'pll_default_language' ) ) {
				return true;
			}

			return false;
		}
	}

	Porto_WPML::get_instance();
endif;
