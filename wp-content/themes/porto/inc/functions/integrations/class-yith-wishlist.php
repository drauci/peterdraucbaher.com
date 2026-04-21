<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Compatibilty with Yith WooCommerce Wishlist
 *
 * @package porto
 * @since 7.1.11
 */

if ( ! class_exists( 'Porto_Yith_Wishlist' ) ) :
	class Porto_Yith_Wishlist {

		/**
		 * The Instance Object.
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			if ( ! class_exists( 'Woocommerce' ) || ! defined( 'YITH_WCWL' ) ) {
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
			add_action( 'init', array( $this, 'update_actions' ), 50 );

			if ( ! is_admin() ) {
				add_action( 'wp', array( $this, 'print_button' ), 20 );
			}

			add_action( 'yith_wcwl_wishlist_after_wishlist_content', array( $this, 'after_wishlist_content' ) );

			add_action( 'yith_wcwl_before_wishlist_title', array( $this, 'before_wishlist_view' ) );
			add_action( 'yith_wcwl_after_wishlist', array( $this, 'after_wishlist_view' ) );
			add_filter( 'yith_wcwl_edit_title_icon', array( $this, 'edit_title_icon' ) );
			add_filter( 'yith_wcwl_cancel_wishlist_title_icon', array( $this, 'cancel_title_icon' ) );
			add_filter( 'yith_wcwl_template_part_hierarchy', array( $this, 'template_part_hierarchy' ), 10, 5 );
			add_filter( 'yith_wcwl_localize_script', array( $this, 'remove_notice' ) );
			add_filter( 'yith_wcwl_custom_css_rules', array( $this, 'page_share_option' ), 20 );
			// add_filter( 'yith_wcwl_reload_on_found_variation', '__return_false' );
			/**
			 * To don't print the wichlist plugins' default button
			 * 
			 * @since 7.1.11
			 */
			add_filter( 'yith_wcwl_positions', '__return_empty_array' );
			add_filter( 'yith_wcwl_loop_positions', '__return_empty_array' );

			if ( is_admin() ) {
				add_filter( 'yith_wcwl_add_to_wishlist_options', array( $this, 'check_wishlist_option' ), 20 );
			}

			add_filter( 'yith_wcwl_customization_style_options', array( $this, 'customization_style_options' ) );

			if ( wp_doing_ajax() ) {
				// refresh wishlist count
				add_action( 'wp_ajax_porto_refresh_wishlist_count', array( $this, 'refresh_wishlist_count' ) );
				add_action( 'wp_ajax_nopriv_porto_refresh_wishlist_count', array( $this, 'refresh_wishlist_count' ) );

				add_action( 'wp_ajax_porto_load_wishlist', array( $this, 'load_wishlist' ) );
				add_action( 'wp_ajax_nopriv_porto_load_wishlist', array( $this, 'load_wishlist' ) );

				// remove notice after remove product from the offcanvas wishlist
				if ( isset( $_POST['action'] ) && 'remove_from_wishlist' == $_POST['action'] && isset( $_POST['from'] ) && 'theme' == $_POST['from'] ) {
					add_filter( 'yith_wcwl_product_removed_text', '__return_false' );
				}
			}

			/**
			 * Disable "react-components" rendering mode
			 * 
			 * @since 7.2.9
			 */
			if ( is_admin() && class_exists( 'YITH_WCWL_Rendering_Method_Admin_Handler' ) ) {
				remove_action( 'admin_notices', array( YITH_WCWL_Rendering_Method_Admin_Handler::get_instance(), 'add_notices' ) );
			}
			add_action( 'porto_admin_save_theme_settings', array( $this, 'disable_react_rendering_mode' ) );
			add_action( 'customize_save_after', array( $this, 'disable_react_rendering_mode' ), 99 );
			add_action( 'porto_import_after_functions', array( $this, 'disable_react_rendering_mode' ) );
		}

		/**
		 * Remove and Update Yith actions
		 */
		public function update_actions() {
			if ( wp_doing_ajax() ) {
				if ( isset( $_REQUEST['action'] ) && 'porto_product_quickview' == $_REQUEST['action'] ) {
					$wcwl_ins = YITH_WCWL_Frontend();
					if ( has_action( 'woocommerce_single_product_summary', array( $wcwl_ins, 'print_button' ) ) ) {
						remove_action( 'woocommerce_single_product_summary', array( $wcwl_ins, 'print_button' ), 31 );
					}
					add_action( 'woocommerce_single_product_summary', array( $wcwl_ins, 'print_button' ), 65 );
				}
			}
		}

		/**
		 * Print Wishlist Button
		 */
		public function print_button() {
			global $porto_product_layout;

			if ( $porto_product_layout ) {
				$wcwl_ins = YITH_WCWL_Frontend();
				if ( has_action( 'woocommerce_single_product_summary', array( $wcwl_ins, 'print_button' ) ) ) {
					remove_action( 'woocommerce_single_product_summary', array( $wcwl_ins, 'print_button' ), 31 );
				}
				if ( ! porto_check_builder_condition( 'product' ) ) {
					if ( in_array( $porto_product_layout, array( 'extended', 'full_width', 'sticky_info', 'sticky_both_info', 'centered_vertical_zoom' ) ) ) {
						add_action( 'woocommerce_after_add_to_cart_button', array( $wcwl_ins, 'print_button' ), 38 );
					} else {
						add_action( 'woocommerce_single_product_summary', array( $wcwl_ins, 'print_button' ), 65 );
					}
				}
			}

			if ( is_product() && porto_check_builder_condition( 'product' ) && is_main_query() ) {
				if ( empty( $wcwl_ins ) ) {
					$wcwl_ins = YITH_WCWL_Frontend();
				}

				// for old versions only
				remove_action( 'woocommerce_single_product_summary', array( $wcwl_ins, 'print_button' ), 31 );
				remove_action( 'woocommerce_product_thumbnails', array( $wcwl_ins, 'print_button' ), 21 );
				remove_action( 'woocommerce_before_single_product_summary', array( $wcwl_ins, 'print_button' ), 21 );
				remove_action( 'woocommerce_after_single_product_summary', array( $wcwl_ins, 'print_button' ), 11 );
			}
		}

		public function after_wishlist_content( $var ) {
			if ( ! empty( $var['count'] ) ) {
				global $porto_settings;
				$legacy_mode = apply_filters( 'porto_legacy_mode', true );
				$legacy_mode = ( $legacy_mode && ! empty( $porto_settings['product-quickview'] ) ) || ! $legacy_mode;
				if ( $legacy_mode || ! empty( $porto_settings['show_swatch'] ) ) {
					// load wc variation script
					wp_enqueue_script( 'wc-add-to-cart-variation' );
				}
			}
		}

		public function before_wishlist_view() {
			echo '<div class="align-left mt-3"><div class="box-content">';
		}

		public function after_wishlist_view() {
			echo '</div></div>';
		}

		public function edit_title_icon( $icon_html ) {
			return str_replace( 'fa fa-pencil"', 'fas fa-pencil-alt"', $icon_html );
		}

		public function cancel_title_icon( $icon_html ) {
			return str_replace( 'fa fa-remove"', 'fas fa-times"', $icon_html );
		}

		public function template_part_hierarchy( $arr, $template, $template_part, $template_layout, $var ) {
			if ( empty( $template_layout ) ) {
				return array(
					"wishlist-{$template}{$template_layout}{$template_part}.php",
					"wishlist-{$template}{$template_part}.php",
				);
			} else {
				return $arr;
			}
		}

		public function remove_notice( $var ) {
			$var['enable_notices'] = false;
			return $var;
		}

		// Check wishlist( & pro version ) options
		public function check_wishlist_option( $wishlist_options ) {
			if ( ! empty( $wishlist_options['settings-add_to_wishlist'] ) ) {
				$wishlist_options['settings-add_to_wishlist']['loop_position']['desc'] = sprintf( esc_html__( 'Select an icon for the "Add to wishlist" button (optional). %1$s %2$sThis option isn\'t available for %3$sPorto%4$s %5$s.', 'porto' ), '<br>', '<span class="porto-warning-notice">', '<a href="https://www.portotheme.com/wordpress/porto/" target="_blank"><b>', '</b></a>', '</span>' );
				$wishlist_options['settings-add_to_wishlist']['add_to_wishlist_position']['desc'] = sprintf( esc_html__( 'Select an icon for the "Add to wishlist" button (optional). %1$s %2$sThis option isn\'t available for %3$sPorto%4$s %5$s.', 'porto' ), '<br>', '<span class="porto-warning-notice">', '<a href="https://www.portotheme.com/wordpress/porto/" target="_blank"><b>', '</b></a>', '</span>' );
				$wishlist_options['settings-add_to_wishlist']['add_to_wishlist_icon']['desc']    = sprintf( esc_html__( 'Select an icon for the "Add to wishlist" button (optional). %1$s %2$sThis option isn\'t available for %3$sPorto%4$s %5$s.', 'porto' ), '<br>', '<span class="porto-warning-notice">', '<a href="https://www.portotheme.com/wordpress/porto/" target="_blank"><b>', '</b></a>', '</span>' );
				$wishlist_options['settings-add_to_wishlist']['added_to_wishlist_icon']['desc']  = sprintf( esc_html__( 'Select an icon for the "Added to wishlist" button (optional). %1$s %2$sThis option isn\'t available for %3$sPorto%4$s %5$s.', 'porto' ), '<br>', '<span class="porto-warning-notice">', '<a href="https://www.portotheme.com/wordpress/porto/" target="_blank"><b>', '</b></a>', '</span>' );
			}
			return $wishlist_options;
		}

		/**
		 * Change wishlist page share option
		 *
		 * @since 6.6.0
		 */
		public function page_share_option( $share_options ) {
			if ( ! empty( $share_options['color_share_button'] ) ) {
				$share_options['color_share_button']['selector'] = '.yith-wcwl-share .share-links a';
			}

			// custom css rules
			if ( isset( $share_options['add_to_wishlist_icon_color'] ) && empty( $share_options['add_to_wishlist_icon_color']['value'] ) && null === get_option( 'yith_wcwl_add_to_wishlist_icon_color', null ) ) {
				$share_options['add_to_wishlist_icon_color']['value'] = '#222529';
			}
			if ( isset( $share_options['added_to_wishlist_icon_color'] ) && empty( $share_options['added_to_wishlist_icon_color']['value'] ) && null === get_option( 'yith_wcwl_added_to_wishlist_icon_color', null ) ) {
				global $porto_settings;
				$share_options['added_to_wishlist_icon_color']['value'] = empty( $porto_settings['sale-color'] ) ? '#e27c7c' : esc_html( $porto_settings['sale-color'] );
			}
			return $share_options;
		}

		public function refresh_wishlist_count() {
			//check_ajax_referer( 'porto-nonce', 'nonce' );
			// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
			echo yith_wcwl_count_products();
			// phpcs:enable
			exit();
		}

		public function load_wishlist() {
			//check_ajax_referer( 'porto-nonce', 'nonce' );
			// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
			$wishlist       = YITH_WCWL_Wishlist_Factory::get_current_wishlist( array() );
			$wishlist_items = array();
			if ( $wishlist && $wishlist->has_items() ) {
				$wishlist_items = $wishlist->get_items();
			}
			?>

			<h3><?php esc_html_e( 'Wishlist', 'porto' ); ?></h3>

			<?php if ( empty( $wishlist_items ) ) : ?>
			<p class="empty-msg"><?php esc_html_e( 'No products in wishlist.', 'porto' ); ?></p>
		<?php else : ?>
			<ul class="product_list_widget">
			<?php
			foreach ( $wishlist_items as $item ) {
				$product = $item->get_product();
				if ( $product ) {
					$id                = $product->get_ID();
					$product_name      = $product->get_data()['name'];
					$thumbnail         = $product->get_image( 'product-thumbnail', array() );
					$product_price     = $product->get_price_html();
					$product_permalink = $product->is_visible() ? $product->get_permalink() : '';

					if ( ! $product_price ) {
						$product_price = '';
					}

					echo '<li class="wishlist-item">';

					echo '<div class="product-image">';
					if ( empty( $product_permalink ) ) {
						echo porto_filter_output( $thumbnail );
					} else {
						echo '<a aria-label="Go to product page" href="' . esc_url( $product_permalink ) . '">' . porto_filter_output( $thumbnail ) . '</a>';
					}
					echo '</div>';

					echo '<div class="product-details">';

					if ( empty( $product_permalink ) ) {
						echo porto_filter_output( $product_name );
					} else {
						echo '<a aria-label="Go to product page" href="' . esc_url( $product_permalink ) . '" class="text-v-dark">' . porto_filter_output( $product_name ) . '</a>';
					}
					echo '<span class="quantity">' . porto_filter_output( $product_price ) . '</span>';
					echo '<span class="remove_from_wishlist remove" data-product_id="' . absint( $id ) . '"></span>';

					echo '</div>';
					echo '<div class="ajax-loading"></div>';

					echo '</li>';
				}
			}
			?>
			</ul>
			<a href="<?php echo esc_url( YITH_WCWL()->get_wishlist_url() ); ?>" class="btn btn-dark btn-modern btn-block btn-sm"><?php esc_html_e( 'Go To Wishlist', 'porto' ); ?></a>
			<?php
			endif;
			// phpcs:enable
			exit();
		}

		/**
		 * Disable "react-components" rendering mode
		 * 
		 * @since 7.2.9
		 */
		public function disable_react_rendering_mode() {
			if ( 'php-templates' !== get_option( 'yith_wcwl_rendering_method' ) ) {
				update_option( 'yith_wcwl_rendering_method', 'php-templates' );
			}
		}

		/**
		 * Reset default color for add_to_wishlist_icon_color and added_to_wishlist_icon_color
		 * 
		 * @since 7.2.9
		 */
		public function customization_style_options( $options ) {
			if ( isset( $options['customization-style'] ) ) {
				if ( ! empty( $options['customization-style']['add_to_wishlist_icon_color'] ) ) {
					$options['customization-style']['add_to_wishlist_icon_color']['default'] = '#222529';
				}
				if ( ! empty( $options['customization-style']['added_to_wishlist_icon_color'] ) ) {
					global $porto_settings;
					$options['customization-style']['added_to_wishlist_icon_color']['default'] = empty( $porto_settings['sale-color'] ) ? '#e27c7c' : esc_html( $porto_settings['sale-color'] );
				}
			}
			return $options;
		}
	}

	Porto_Yith_Wishlist::get_instance();
endif;
