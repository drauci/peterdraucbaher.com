<?php
/**
 * Porto Woocommerce Image Swatches
 *
 * @author     Porto Themes
 * @category   Library
 * @since      4.7.0
 */

if ( ! class_exists( 'Porto_Woocommerce_Swatches' ) ) :
	class Porto_Woocommerce_Swatches {

		public $product_data_tab;
		
		public function __construct() {

			add_action( 'init', array( $this, 'init' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1001 );
		}

		public function init() {
			global $porto_settings;
			if ( wp_doing_ajax() || ( ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) && ! porto_is_elementor_preview() && current_user_can( 'manage_options' ) && isset( $porto_settings['product_variation_display_mode'] ) && 'button' == $porto_settings['product_variation_display_mode'] ) ) {
				require 'classes/class-product-swatches-tab.php';
				$this->product_data_tab = new Porto_Product_Swatches_Tab();

				add_action( 'wp_ajax_porto_load_swatches', array( $this, 'load_swatches' ) );
				add_action( 'wp_ajax_nopriv_porto_load_swatches', array( $this, 'load_swatches' ) );
			}

			$image_size = get_option( 'swatches_image_size', array() );
			$size       = array();

			$size['width']  = isset( $image_size['width'] ) && ! empty( $image_size['width'] ) ? $image_size['width'] : '32';
			$size['height'] = isset( $image_size['height'] ) && ! empty( $image_size['height'] ) ? $image_size['height'] : '32';
			$size['crop']   = isset( $image_size['crop'] ) ? $image_size['crop'] : 1;

			$image_size = apply_filters( 'woocommerce_get_image_size_swatches_image_size', $size );

			add_image_size( 'swatches_image_size', apply_filters( 'woocommerce_swatches_size_width_default', $image_size['width'] ), apply_filters( 'woocommerce_swatches_size_height_default', $image_size['height'] ), $image_size['crop'] );

			add_filter( 'porto_wc_swatch_loop_attrs', array( $this, 'loop_swatch' ), 10, 5 );

			if ( isset( $_GET['wc-ajax'] ) && 'get_variation' == $_GET['wc-ajax'] && isset( $_POST['custom_data'] ) && 'porto_render_swatch' == $_POST['custom_data'] ) {
				$this->add_non_swatch_attributes();
			}
		}

		public function enqueue_scripts() {
			global $pagenow;
			if ( ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) && ! porto_is_elementor_preview() && is_admin() && ( 'post-new.php' == $pagenow || 'post.php' == $pagenow || 'edit.php' == $pagenow || 'edit-tags.php' == $pagenow ) ) {
				wp_enqueue_media();
				global $post;
				$data = array(
					'placeholder_src' => wc_placeholder_img_src(),
					'wpnonce'         => wp_create_nonce( 'porto_swatch_nonce' ),
					'ajax_url'        => esc_url( admin_url( 'admin-ajax.php' ) ),
				);
				if ( $post ) {
					$data['post_id'] = $post->ID;
				}
				wp_localize_script( 'porto-admin', 'porto_swatches_params', $data );
			}
		}

		public function load_swatches() {
			if ( current_user_can( 'manage_options' ) && wp_verify_nonce( wp_unslash( $_POST['wpnonce'] ), 'porto_swatch_nonce' ) && $this->product_data_tab ) {
				echo porto_filter_output( $this->product_data_tab->render_product_tab_content( (int) $_POST['product_id'] ) );
				die();
			}
		}

		public function loop_swatch( $swatches, $product, $attribute_name, $available_variations = array() ) {
			if ( empty( $available_variations ) ) {
				$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
				$available_variations = $get_variations ? $product->get_available_variations() : array();
			}
			foreach ( $available_variations as $key => $variation ) {
				$attr_key = 'attribute_' . $attribute_name;

				$variation_attributes = $variation['attributes'];

				if ( ! isset( $variation['attributes'][ $attr_key ] ) ) {
					return;
				}

				$val               = $variation['attributes'][ $attr_key ];
				$variation_product = wc_get_product( $variation['variation_id'] );
				$option_variation  = array(
					'variation_id' => $variation['variation_id'],
					'is_in_stock'  => $variation['is_in_stock'],
				);

				if ( ! empty( $variation['image']['src'] ) && $variation_product && $variation_product->get_image_id( 'edit' ) ) {
					$option_variation['image_src'] = $variation['image']['src'];
					$option_variation['image_srcset'] = $variation['image']['srcset'];
					$option_variation['image_sizes'] = $variation['image']['sizes'];
				}

				if ( ! isset( $swatches[ $val ] ) || ! isset( $swatches[ $val ]['image_src'] ) ) {
					$swatches[ $val ] = $option_variation;
				}
			}

			return $swatches;
		}

		public function add_non_swatch_attributes( ) {
			$variable_product = wc_get_product( absint( $_POST['product_id'] ) );
			if ( ! $variable_product ) {
				wp_die();
			}
			$available_variations = $variable_product->get_available_variations();
			foreach ( $available_variations as $key => $variation ) {
				$flag = false;
				foreach( $variation['attributes'] as $attribute_name => $val ) {
					if ( isset( $_POST[ $attribute_name ] ) && $val != $_POST[ $attribute_name ] ) {
						$flag = true;
						break;
					}
				}
				if ( $flag ) {
					continue;
				}
				$non_swatch_attributes = array_diff_key( $variation['attributes'], $_POST );
				if ( empty( $non_swatch_attributes ) ) {
					break;
				}

				$variation_product = wc_get_product( $variation['variation_id'] );

				if ( ( ! empty( $variation['image']['src'] ) && $variation_product && $variation_product->get_image_id( 'edit' ) ) || $key === count( $available_variations ) - 1 ) {
					foreach ( $non_swatch_attributes as $attribute_name => $val ) {
						$_POST[ $attribute_name ] = $val;
					}
					break;
				}
			}
		}
	}
endif;

new Porto_Woocommerce_Swatches();
