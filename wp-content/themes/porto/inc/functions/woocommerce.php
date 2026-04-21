<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Includes integration files
 *
 * @since 7.1.11
 */
if ( defined( 'YITH_WOOCOMPARE' ) ) {
	require_once PORTO_FUNCTIONS . '/integrations/class-yith-compare.php';
}

if ( defined( 'YITH_WCWL' ) ) {
	require_once PORTO_FUNCTIONS . '/integrations/class-yith-wishlist.php';
}

require_once PORTO_FUNCTIONS . '/woo-actions/single-product.php';
require_once PORTO_FUNCTIONS . '/woo-actions/cart.php';

// get woocommerce version number
if ( ! function_exists( 'porto_get_woo_version_number' ) ) :
function porto_get_woo_version_number() {
	if ( WC()->version ) {
		return WC()->version;
	}
	// If get_plugins() isn't available, require it
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	// Create the plugins folder and file variables
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file   = 'woocommerce.php';
	// If the plugin version number is set, return it
	if ( isset( $plugin_folder[ $plugin_file ]['Version'] ) ) {
		return $plugin_folder[ $plugin_file ]['Version'];
	} else {
		// Otherwise return null
		return null;
	}
}
endif;

// remove actions
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );


/**
 * Compatibiity with WooCommerce Germanized plugin
 * to fix the double product images in checkout page
 * 
 * @since 7.1.7
 */
add_filter( 'woocommerce_gzd_checkout_table_needs_thumbnail', '__return_false' );

// add actions
if ( defined( 'ELEMENTOR_VERSION' ) && porto_is_elementor_preview() ) {
	if ( ! has_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' ) ) {
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	}
	if ( ! has_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating' ) ) {
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	}
}

add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div', 11 );
add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div', 80 );
add_action( 'woocommerce_after_shop_loop', 'porto_woocommerce_open_after_clearfix_div', 1 );
add_action( 'woocommerce_after_shop_loop', 'porto_woocommerce_close_after_clearfix_div', 999 );
add_action( 'woocommerce_before_shop_loop', 'porto_grid_list_toggle', 70 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
add_action( 'woocommerce_before_shop_loop_item_title', 'porto_loop_product_thumbnail', 10 );
add_action( 'woocommerce_archive_description', 'porto_woocommerce_category_image', 2 );
add_action( 'woocommerce_shop_loop_item_title', 'porto_woocommerce_shop_loop_item_title_open', 1 );
add_action( 'woocommerce_shop_loop_item_title', 'porto_woocommerce_shop_loop_item_title_close', 100 );
//add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
add_action( 'porto_woocommerce_shop_loop_start', 'porto_woocommerce_shop_loop_item_init_layout' );
add_action( 'porto_woocommerce_shop_loop_end', 'porto_woocommerce_shop_loop_item_reset_layout' );
add_action( 'porto_woocommerce_before_shop_loop_item_title', 'porto_woocommerce_shop_loop_item_category' );
add_action( 'woocommerce_shop_loop_item_title', 'porto_woocommerce_shop_loop_item_title' );
add_action( 'porto_before_content_inner_top', 'porto_wc_print_notices', 10 );

add_action( 'woocommerce_before_shop_loop_item_title', 'porto_woocommerce_sale_product_period', 20 );

add_action( 'porto_woocommerce_loop_links_on_image', 'woocommerce_template_loop_add_to_cart' );
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script( 'wc-cart-fragments' );
}, 11 );

add_action( 'woocommerce_checkout_before_terms_and_conditions', 'porto_woocommerce_add_js_composer_shortcodes', 2 );
function porto_woocommerce_add_js_composer_shortcodes() {
	// Fix for rendering Visual Composer shortcodes.
	if ( class_exists( 'WPBMap' ) && method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
		WPBMap::addAllMappedShortcodes();
	}
}

add_filter( 'woocommerce_available_variation', 'porto_woocommerce_get_sale_end_date', 100, 3 );



// add filters
add_filter( 'woocommerce_show_page_title', 'porto_woocommerce_show_page_title' );
add_filter( 'woocommerce_layered_nav_link', 'porto_layered_nav_link', 10, 2 );
add_filter( 'loop_shop_per_page', 'porto_loop_shop_per_page' );
add_filter( 'woocommerce_available_variation', 'porto_woocommerce_available_variation', 10, 3 );

add_filter( 'woocommerce_add_to_cart_fragments', 'porto_woocommerce_header_add_to_cart_fragment' );
add_filter( 'woocommerce_get_catalog_ordering_args', 'porto_woocommerce_get_catalog_ordering_args', 10, 3 );
add_filter( 'woocommerce_before_widget_product_review_list', 'porto_woocommerce_before_widget_product_review_list' );

//add_filter( 'add_to_cart_fragments', 'porto_woocommerce_header_add_to_cart_fragment' );
//add_filter( 'woocommerce_show_admin_notice', 'porto_woocommerce_hide_update_notice', 10, 2 );

// message
function porto_wc_print_notices() {
	if ( porto_is_product() && function_exists( 'wc_print_notices' ) ) {
		$builder_id = porto_check_builder_condition( 'product' );
		if ( $builder_id  && is_main_query() ) {
			$el_data = get_post_meta( $builder_id, '_elementor_data', true );
			if ( empty( $el_data ) || ! defined( 'ELEMENTOR_VERSION' ) ) {
				if ( $el_data = get_post( $builder_id ) ) {
					$el_data = $el_data->post_content;
				}
			}
			if ( FALSE !== strpos( $el_data, 'porto_cp_notice' ) || FALSE !== strpos( $el_data, '[porto_single_product_notice' ) ) {
				remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices' );
				return;
			}
		}
		wc_print_notices();
	}
}

// rating
function porto_woocommerce_shop_loop_item_init_layout() {

	global $woocommerce_loop, $porto_woocommerce_loop;
	if ( ! isset( $woocommerce_loop['addlinks_pos'] ) ) {
		return;
	}
	if ( ( isset( $porto_woocommerce_loop['widget'] ) && $porto_woocommerce_loop['widget'] ) || ( isset( $porto_woocommerce_loop['use_simple_layout'] ) && $porto_woocommerce_loop['use_simple_layout'] ) || in_array( $woocommerce_loop['addlinks_pos'], array( 'outimage_aq_onimage', 'outimage_aq_onimage2', 'awq_onimage', 'onimage2', 'onimage3' ) ) ) {
		if ( has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' ) ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
			$porto_woocommerce_loop['reset_add_to_cart'] = true;
		}
	}
}

function porto_woocommerce_shop_loop_item_reset_layout() {
	global $porto_woocommerce_loop;
	if ( isset( $porto_woocommerce_loop['reset_add_to_cart'] ) && $porto_woocommerce_loop['reset_add_to_cart'] ) {
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
		unset( $porto_woocommerce_loop['reset_add_to_cart'] );
	}
}

// category
function porto_woocommerce_shop_loop_item_category() {
	global $product, $woocommerce_loop, $porto_settings, $porto_woocommerce_loop;
	$legacy_mode = apply_filters( 'porto_legacy_mode', true );
	$legacy_mode = ( $legacy_mode && ! empty( $porto_settings['product-wishlist'] ) ) || ! $legacy_mode;
	if ( class_exists( 'YITH_WCWL' ) && $legacy_mode && ( ! isset( $porto_woocommerce_loop['use_simple_layout'] ) || ! $porto_woocommerce_loop['use_simple_layout'] ) && ( 'outimage_aq_onimage' == $woocommerce_loop['addlinks_pos'] || 'outimage_aq_onimage2' == $woocommerce_loop['addlinks_pos'] ) ) {
		echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
	}
	if ( ( ! isset( $porto_woocommerce_loop['use_simple_layout'] ) || ! $porto_woocommerce_loop['use_simple_layout'] ) && ( ! isset( $porto_settings['product-categories'] ) || $porto_settings['product-categories'] ) ) {
		echo '<span class="category-list">' . wc_get_product_category_list( $product->get_id(), ', ', '' ) . '</span>';
	}
}


// hide woocommer page title
function porto_woocommerce_show_page_title( $args ) {
	return false;
}

function porto_woocommerce_open_before_clearfix_div() {
	global $porto_shop_filter_layout;
	if ( ( ! isset( $porto_shop_filter_layout ) || 'horizontal2' != $porto_shop_filter_layout ) && ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() ) ) {
		return;
	}
	echo '<div class="shop-loop-before">';
}

function porto_woocommerce_open_after_clearfix_div() {

	if ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() ) {
		return;
	}
	global $porto_settings;
	$class_suffix = '';
	if ( isset( $porto_settings['product-infinite'] ) && 'infinite_scroll' == $porto_settings['product-infinite'] ) {
		$class_suffix = ' d-none';
	} elseif ( isset( $porto_settings['product-infinite'] ) && 'load_more' == $porto_settings['product-infinite'] ) {
		$class_suffix = ' load-more-wrap';
	}
	if ( wc_get_loop_prop( 'is_shortcode' ) ) {
		$class_suffix .= ' is-shortcode';
	}
	echo '<div class="shop-loop-after clearfix' . $class_suffix . '">';
}

function porto_woocommerce_close_before_clearfix_div() {
	global $porto_shop_filter_layout;
	if ( ( ! isset( $porto_shop_filter_layout ) || 'horizontal2' != $porto_shop_filter_layout ) && ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() ) ) {
		return;
	}
	echo '</div>';
}

function porto_woocommerce_close_after_clearfix_div() {
	if ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() ) {
		return;
	}
	echo '</div>';
}

// show grid/list toggle buttons
function porto_grid_list_toggle( $el_class = false, $icon_grid = '', $icon_list = '' ) {
	global $woocommerce_loop, $porto_woocommerce_loop, $porto_shop_filter_layout, $porto_shop_toggle;
	if ( ! porto_is_elementor_preview() && ! porto_vc_is_inline() && ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() || isset( $porto_woocommerce_loop['view'] ) ) ) {
		return;
	}
	//woocommerce_result_count();
	$porto_shop_toggle = true;
	$view_mode         = '';
	if ( isset( $_REQUEST['gridcookie'] ) ) {
		$view_mode = sanitize_text_field( $_REQUEST['gridcookie'] );
	} elseif ( isset( $woocommerce_loop['category-view'] ) && $woocommerce_loop['category-view'] ) {
		$view_mode = $woocommerce_loop['category-view'];
	}
	if ( ! $view_mode ) {
		$view_mode = 'grid';
	}

	if ( ! $icon_grid ) {
		if ( 'offcanvas' === $porto_shop_filter_layout || 'horizontal' === $porto_shop_filter_layout ) {
			$icon_grid = 'fas fa-th';
		} else {
			$icon_grid = 'porto-icon-mode-grid';
		}
	}
	if ( ! $icon_list ) {
		if ( 'offcanvas' === $porto_shop_filter_layout || 'horizontal' === $porto_shop_filter_layout ) {
			$icon_list = 'fas fa-list-ul';
		} else {
			$icon_list = 'porto-icon-mode-list';
		}
	}
	?>
	<div class="gridlist-toggle<?php echo ! $el_class ? '' : ' ' . esc_attr( $el_class ); ?>">
		<a role="button" aria-label="<?php esc_attr_e( 'View as grid', 'porto' ); ?>" href="<?php echo esc_url( remove_query_arg( 'gridcookie' ) );?>" id="grid" class="<?php echo esc_attr( $icon_grid ), 'grid' == $view_mode ? ' active' : ''; ?>" title="<?php esc_attr_e( 'Grid View', 'porto' ); ?>"></a><a role="button" aria-label="<?php esc_attr_e( 'View as list', 'porto' ); ?>" href="<?php echo esc_url( add_query_arg( 'gridcookie', 'list' ) );?>" id="list" class="<?php echo esc_attr( $icon_list ), 'list' == $view_mode ? ' active' : ''; ?>" title="<?php esc_attr_e( 'List View', 'porto' ); ?>"></a>
	</div>
	<?php
}
// get product count per page
function porto_loop_shop_per_page() {
	global $porto_settings;
	// replace it with theme option
	if ( ! empty( $porto_settings['category-item'] ) ) {
		$per_page = explode( ',', $porto_settings['category-item'] );
	} else {
		$per_page = explode( ',', '12,24,36' );
	}
	$item_count = ! empty( $_GET['count'] ) ? (int) $_GET['count'] : (int) $per_page[0];
	return $item_count;
}
// add thumbnail image parameter
function porto_woocommerce_available_variation( $variations, $product, $variation ) {
	$thumbnail_size    = 'woocommerce_thumbnail';
	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	if ( $gallery_thumbnail['width'] !== 150 ) {
		$thumbnail_size = 'woocommerce_gallery_thumbnail';
	}
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', $thumbnail_size );
        $full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	if ( has_post_thumbnail( $variation->get_id() ) ) {
		$attachment_id    = get_post_thumbnail_id( $variation->get_id() );
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
		if ( $image_thumb_link ) {
			$variations       = array_merge( $variations, array( 'image_thumb' => $image_thumb_link[0] ) );
			$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'woocommerce_single' );
			$variations       = array_merge( $variations, array( 'image_src' => $image_thumb_link[0] ) );
			$image_thumb_link = wp_get_attachment_image_src( $attachment_id, $full_size );
			$variations       = array_merge( $variations, array( 'image_link' => $image_thumb_link[0] ) );
		}
	} elseif ( has_post_thumbnail() ) {
		$attachment_id    = get_post_thumbnail_id();
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
		if ( $image_thumb_link ) {
			$variations       = array_merge( $variations, array( 'image_thumb' => $image_thumb_link[0] ) );
			$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'woocommerce_single' );
			$variations       = array_merge( $variations, array( 'image_src' => $image_thumb_link[0] ) );
			$image_thumb_link = wp_get_attachment_image_src( $attachment_id, $full_size );
			$variations       = array_merge( $variations, array( 'image_link' => $image_thumb_link[0] ) );
		}
	}
	return $variations;
}
// add sort order parameter
function porto_layered_nav_link( $link, $term = false ) {
	if ( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) ) {
		$link = add_query_arg( 'orderby', $_GET['orderby'], $link );
	}
	if ( isset( $_GET['count'] ) && ! empty( $_GET['count'] ) ) {
		$link = add_query_arg( 'count', $_GET['count'], $link );
	}
	if ( isset( $_GET['gridcookie'] ) && ! empty( $_GET['gridcookie'] ) ) {
		$link = add_query_arg( 'gridcookie', $_GET['gridcookie'], $link );
	}

	if ( false === $term && ( is_product_category() || is_product_tag() ) ) {
		if ( isset( $_GET['product_cat'] ) || isset( $_GET['product_tag'] ) ) {
			$link = remove_query_arg( 'post_type', $link );
		} else {
			$tax          = is_product_category() ? 'product_cat' : 'product_tag';
			$currnet_page = remove_query_arg( 'add-to-cart' );
			$currnet_page = explode( '?', $currnet_page )[0];
			$currnet_page = preg_replace( '/\/page\/[\w]+/i', '', $currnet_page );
			$link_arr     = explode( '?', remove_query_arg( array( $tax, 'source_id', 'source_tax' ), $link ) );
			$link         = $currnet_page;
			if ( 2 === count( $link_arr ) ) {
				$link .= '?' . $link_arr[1];
				$link  = str_replace( array( '?&#038;', '?#038;', '?&' ), '?', $link );
			}
		}
	}
	$link = esc_url( str_replace( array( '&#038;', '#038;' ), '&amp;', $link ) );
	return $link;
}
// change product thumbnail in products list page
function porto_loop_product_thumbnail() {
	global $porto_settings, $porto_woocommerce_loop, $product;
	$id = get_the_ID();
	if ( isset( $porto_woocommerce_loop['image_size'] ) && $porto_woocommerce_loop['image_size'] ) {
		$image_size = $porto_woocommerce_loop['image_size'];
	} else {
		$image_size = 'woocommerce_thumbnail';
	}

	$gallery          = get_post_meta( $id, '_product_image_gallery', true );
	$attachment_image = '';
	if ( ! empty( $gallery ) && ! empty( $porto_settings['category-image-hover'] ) ) {
		$gallery = explode( ',', $gallery );

		$show_hover_img = get_post_meta( $id, 'product_image_on_hover', true );
		$show_hover_img = empty( $show_hover_img ) || ( 'yes' === $show_hover_img );
		if ( $show_hover_img ) {
			$first_image_id   = $gallery[0];
			$attachment_image = apply_filters( 'porto_lazy_load_images', wp_get_attachment_image( $first_image_id, $image_size, false, array( 'class' => 'hover-image' ) ) );
		}
	}

	$thumb_image = woocommerce_get_product_thumbnail( $image_size );

	if ( ! $thumb_image ) {
		if ( wc_placeholder_img_src() ) {
			$thumb_image = wc_placeholder_img( $image_size );
		}
	}

	echo '<div class="inner' . ( ( $attachment_image ) ? ' img-effect' : '' ) . '">';
	// show images
	echo porto_filter_output( $thumb_image );
	echo porto_filter_output( $attachment_image );
	echo '</div>';
}
// change product thumbnail in products widget
function porto_widget_product_thumbnail() {
	global $porto_settings;
	$id               = get_the_ID();
	$size             = 'widget-thumb-medium';
	$gallery          = get_post_meta( $id, '_product_image_gallery', true );
	$attachment_image = '';
	$thumb_image      = '';
	if ( ! empty( $gallery ) && ! empty( $porto_settings['category-image-hover'] ) ) {
		$gallery = explode( ',', $gallery );

		$show_hover_img = get_post_meta( $id, 'product_image_on_hover', true );
		$show_hover_img = empty( $show_hover_img ) || ( 'yes' === $show_hover_img );
		if ( $show_hover_img ) {
			$first_image_id = $gallery[0];
			$image_data     = wp_get_attachment_image_src( $first_image_id, $size );
			if ( $image_data ) {
				$attachment_image = '<img src="' . esc_url( $image_data[0] ) . '" alt="' . trim( strip_tags( get_post_meta( $first_image_id, '_wp_attachment_image_alt', true ) ) ) . '" width="' . esc_attr( $image_data[1] ) . '" height="' . esc_attr( $image_data[2] ) . '" class="hover-image" />';
			}
		}
	}
	$thumb_image_id = get_post_thumbnail_id( $id );
	if ( $thumb_image_id ) {
		$image_data = wp_get_attachment_image_src( $thumb_image_id, $size );
		if ( $image_data ) {
			$thumb_image = '<img src="' . esc_url( $image_data[0] ) . '" alt="' . trim( strip_tags( get_post_meta( $thumb_image_id, '_wp_attachment_image_alt', true ) ) ) . '" width="' . esc_attr( $image_data[1] ) . '" height="' . esc_attr( $image_data[2] ) . '" />';
		}
	} elseif ( wc_placeholder_img_src() ) {
		$thumb_image = wc_placeholder_img( $size );
	}
	echo '<div class="inner' . ( ( $attachment_image ) ? ' img-effect' : '' ) . '">';
	// show images
	echo porto_filter_output( apply_filters( 'porto_lazy_load_images', $thumb_image ) );
	echo porto_filter_output( apply_filters( 'porto_lazy_load_images', $attachment_image ) );
	echo '</div>';
}

// add ajax cart fragment
function porto_woocommerce_header_add_to_cart_fragment( $fragments ) {
	$minicart_type = porto_get_minicart_type();
	global $porto_settings;
	$_cart_total                            = '<span class="cart-price">' . WC()->cart->get_cart_subtotal() . '</span>';
	$cart_text                              = empty( $porto_settings['minicart-text'] ) ? __( 'Cart', 'porto' ) : $porto_settings['minicart-text'];
	$fragments['#mini-cart .cart-subtotal'] = '<span class="cart-subtotal">' . esc_html( $cart_text ) . ' ' . $_cart_total . '</span>';
	$_cart_qty                              = WC()->cart->cart_contents_count;
	$_cart_qty                              = ( $_cart_qty > 0 ? $_cart_qty : '0' );
	$fragments['#mini-cart .cart-items']    = '<span class="cart-items">' . ( (int) $_cart_qty ) . '</span>';
	/* translators: %s: Cart items */
	$fragments['#mini-cart .cart-items-text'] = '<span class="cart-items-text">' . sprintf( _n( '%d item', '%d items', $_cart_qty, 'porto' ), $_cart_qty ) . '</span>';

	if ( ! empty( $porto_settings['show-icon-menus-mobile'] ) ) {
		$fragments['.porto-sticky-navbar .cart-items'] = $fragments['#mini-cart .cart-items'];
	}
	return $fragments;
}
// remove update notice
function porto_woocommerce_hide_update_notice( $flag, $notice ) {
	if ( 'update' === $notice ) {
		return false;
	}
	return $flag;
}

if ( wp_doing_ajax() ) {
	// ajax add to cart
	add_action( 'wp_ajax_porto_add_to_cart', 'porto_add_to_cart' );
	add_action( 'wp_ajax_nopriv_porto_add_to_cart', 'porto_add_to_cart' );
	function porto_add_to_cart() {
		ob_start();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product           = wc_get_product( $product_id );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		$variation_id      = 0;
		$variation         = array();

		if ( $product && 'variation' === $product->get_type() ) {
			$variation_id = $product_id;
			$product_id   = $product->get_parent_id();
			$variation    = $product->get_variation_attributes();
			if ( ! empty( $variation ) ) {
				foreach ( $variation as $k => $v ) {
					if ( empty( $v ) && ! empty( $_REQUEST[ $k ] ) ) {
						$variation[ $k ] = wp_unslash( $_REQUEST[ $k ] );
					}
				}
			}
		}

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			WC_AJAX::get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
		// phpcs:enable
	}

	// ajax remove cart item
	add_action( 'wp_ajax_porto_cart_item_remove', 'porto_cart_item_remove' );
	add_action( 'wp_ajax_nopriv_porto_cart_item_remove', 'porto_cart_item_remove' );
	function porto_cart_item_remove() {
		//check_ajax_referer( 'porto-nonce', 'nonce' );
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		$cart         = WC()->instance()->cart;
		$cart_id      = sanitize_text_field( $_POST['cart_id'] );
		$cart_item_id = $cart->find_product_in_cart( $cart_id );
		if ( $cart_item_id ) {
			// compatibility with YITH Composite Products for WooCommerce Premium
			if ( defined( 'YITH_WCP' ) && class_exists( 'YITH_WCP_Cart' ) ) {
				new YITH_WCP_Cart();
			}

			$cart->remove_cart_item( $cart_item_id );
		}
		$cart_ajax = new WC_AJAX();
		$cart_ajax->get_refreshed_fragments();
		// phpcs:enable
		exit();
	}
	// refresh cart fragment
	add_action( 'wp_ajax_porto_refresh_cart_fragment', 'porto_refresh_cart_fragment' );
	add_action( 'wp_ajax_nopriv_porto_refresh_cart_fragment', 'porto_refresh_cart_fragment' );
	function porto_refresh_cart_fragment() {
		//check_ajax_referer( 'porto-nonce', 'nonce' );
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		$cart_ajax = new WC_AJAX();
		$cart_ajax->get_refreshed_fragments();
		// phpcs:enable
		exit();

	}

	/**
	 * Compatibility with Wholesale For WooCommerce plugin
	 * 
	 * @since 7.1.14
	 */
	if ( defined( 'WWP_PLUGIN_PATH' ) && isset( $_REQUEST['action'] ) && 'porto_product_quickview' == $_REQUEST['action'] ) {
		include_once WWP_PLUGIN_PATH . '/inc/class-wwp-wholesale-frontend.php';
		add_action( 'init', array( 'Wwp_Wholesale_Pricing', 'include_wholesale_functionality' ) );
	}
}

function porto_get_products_by_ids( $product_ids ) {
	$product_ids = explode( ',', $product_ids );
	$product_ids = array_map( 'trim', $product_ids );
	$args        = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1,
		'post__in'            => $product_ids,
	);
	// @codingStandardsIgnoreStart
	$args['tax_query'] = WC()->query->get_tax_query();
	// @codingStandardsIgnoreEnd
	$query = new WP_Query( $args );
	return $query;
}
function porto_get_rating_html( $product, $rating = null ) {
	if ( get_option( 'woocommerce_enable_review_rating' ) == 'no' ) {
		return '';
	}
	if ( ! is_numeric( $rating ) ) {
		$rating = $product->get_average_rating();
	}
	$rating_html  = '<div class="star-rating" title="' . esc_attr( $rating ) . '">';
	$rating_html .= '<span style="width:' . ( ( floatval( $rating ) / 5 ) * 100 ) . '%"><strong class="rating">' . esc_html( $rating ) . '</strong> ' . __( 'out of 5', 'porto' ) . '</span>';
	$rating_html .= '</div>';
	return $rating_html;
}
// Wrap gravatar in reviews
add_action( 'woocommerce_review_before', 'porto_woo_review_display_gravatar_wrap_start', 9 );
add_action( 'woocommerce_review_before', 'porto_woo_review_display_gravatar_wrap_end', 11 );
function porto_woo_review_display_gravatar_wrap_start() {
	echo '<div class="img-thumbnail">';
}
function porto_woo_review_display_gravatar_wrap_end() {
	echo '</div>';
}
add_filter( 'woocommerce_review_gravatar_size', 'porto_woo_review_gravatar_size' );
function porto_woo_review_gravatar_size( $size ) {
	return '80';
}
// Quick View Html
add_action( 'wp_ajax_porto_product_quickview', 'porto_product_quickview' );
add_action( 'wp_ajax_nopriv_porto_product_quickview', 'porto_product_quickview' );
function porto_product_quickview() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	global $post, $product;
	$post    = get_post( (int) $_REQUEST['pid'] );

	if ( empty( $post ) || 'publish' != $post->post_status ) {
		die();
		return;
	}
	$product = wc_get_product( $post->ID );
	if ( post_password_required() ) {
		echo get_the_password_form();
		die();
		return;
	}

	if ( $product->is_type( 'variation' ) ) {
		$attrs = wc_get_product_variation_attributes( $post->ID );
		if ( ! empty( $attrs ) ) {
			foreach ( $attrs as $key => $val ) {
				$_REQUEST[ $key ] = $val;
			}
		}
		$parent_id = wp_get_post_parent_id( $post );
		if ( $parent_id ) {
			$post    = get_post( (int) $parent_id );
			$product = wc_get_product( $post->ID );
		}
	}
	porto_woocommerce_add_js_composer_shortcodes();

	/**
	 * Fix the compatibility issues with Free Downloads WooCommerce plugin
	 *
	 * @since 7.1.14
	 */
	if ( defined( 'SOMDN_FILE' ) ) {
		add_filter( 'somdn_is_single_product', '__return_true' );
	}
	?>
	<div class="quickview-wrap quickview-wrap-<?php echo absint( $post->ID ); ?> single-product<?php echo ' product-type-' . esc_attr( $product->get_type() ); ?>">
		<div class="product product-summary-wrap">
			<div class="row">
				<div class="col-lg-6 summary-before">
					<?php
					do_action( 'woocommerce_before_single_product_summary' );
					?>
				</div>
				<div class="col-lg-6 summary entry-summary">
					<div class="quickview-inner">
					<?php

					do_action( 'woocommerce_single_product_summary' );

					if ( ! isset( $_REQUEST['variation_flag'] ) || ! $_REQUEST['variation_flag'] || 'false' == $_REQUEST['variation_flag'] ) :
						wc_get_template( 'single-product/add-to-cart/variation.php' );
						?>
					<script>
						<?php
						$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
						$assets_path          = esc_url( str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) ) . '/assets/';
						$frontend_script_path = $assets_path . 'js/frontend/';
						?>
						var wc_add_to_cart_variation_params = 
						<?php
						echo array2json(
							apply_filters(
								'wc_add_to_cart_variation_params',
								array(
									'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
									'i18n_no_matching_variations_text' => esc_js( __( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ) ),
									'i18n_make_a_selection_text' => esc_js( __( 'Please select some product options before adding this product to your cart.', 'woocommerce' ) ),
									'i18n_unavailable_text' => esc_js( __( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ) ),
								)
							)
						)
						?>
						;
						jQuery(document).ready(function($) {
							$.getScript('<?php echo porto_filter_output( $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js' ); ?>');
						});
					</script>
					<?php endif; ?>
					</div>
				</div><!-- .summary -->
			</div>
		</div>
	</div>
	<?php
	// phpcs:enable
	die();
}
function porto_woocommerce_category_image() {
	if ( is_product_category() ) {
		$term = get_queried_object();
		if ( $term ) {
			$image = get_metadata( $term->taxonomy, $term->term_id, 'category_image', true );
			if ( $image ) {
				echo '<img src="' . esc_url( $image ) . '" class="category-image" alt="' . esc_attr( $term->name ) . '" />';
			}
		}
	}
}
function porto_woocommerce_shop_loop_item_title_open() {
	global $porto_settings;
	$more_link   = apply_filters( 'the_permalink', get_permalink() );
	$more_target = '';
	if ( isset( $porto_settings['catalog-enable'] ) && $porto_settings['catalog-enable'] ) {
		if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
			if ( ! $porto_settings['catalog-cart'] ) {
				if ( $porto_settings['catalog-readmore'] && 'all' === $porto_settings['catalog-readmore-archive'] ) {
					$link = get_post_meta( get_the_id(), 'product_more_link', true );
					if ( $link ) {
						$more_link = $link;
					}
					$more_target = $porto_settings['catalog-readmore-target'] ? 'target="' . esc_attr( $porto_settings['catalog-readmore-target'] ) . '"' : '';
				}
			}
		}
	}
	?>
	<a class="product-loop-title" <?php echo porto_filter_output( $more_target ); ?> href="<?php echo esc_url( $more_link ); ?>">
	<?php
}
function porto_woocommerce_shop_loop_item_title_close() {
	?>
	</a>
	<?php
}
function porto_woocommerce_shop_loop_item_title() {
	echo '<h3 class="woocommerce-loop-product__title">';
	the_title();
	echo '</h3>';
}


add_action( 'woocommerce_init', 'porto_woocommerce_init' );
function porto_woocommerce_init() {
	global $porto_settings;
	// Hide product short description
	if ( isset( $porto_settings['catalog-enable'] ) && apply_filters( 'porto_legacy_mode', true ) && empty( $porto_settings['product-short-desc'] ) ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	}

	add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_product_nav', 5 );
	// Catalog Mode
	if ( isset( $porto_settings['catalog-enable'] ) && $porto_settings['catalog-enable'] ) {
		if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
			if ( ! $porto_settings['catalog-price'] ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
				add_filter( 'woocommerce_get_price_html', 'porto_woocommerce_get_price_html_empty', 100, 2 );
				add_filter( 'woocommerce_cart_item_price', 'porto_woocommerce_get_price_empty', 100, 3 );
				add_filter( 'woocommerce_cart_item_subtotal', 'porto_woocommerce_get_price_empty', 100, 3 );
				add_filter( 'woocommerce_cart_subtotal', 'porto_woocommerce_get_price_empty', 100, 3 );
				add_filter( 'woocommerce_get_variation_price_html', 'porto_woocommerce_get_price_html_empty', 100, 2 );
			}
			if ( ! $porto_settings['catalog-cart'] ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_template_single_add_to_cart', 30 );
				//remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
				remove_filter( 'woocommerce_loop_add_to_cart_link', 'porto_woocommerce_display_quantity_input_on_shop_page', 10, 2 );
			}
			if ( ! $porto_settings['catalog-review'] ) {
				add_filter( 'pre_option_woocommerce_enable_review_rating', 'porto_woocommerce_disable_rating' );
				add_filter( 'woocommerce_product_tabs', 'porto_woocommerce_remove_reviews_tab', 98 );
				function porto_woocommerce_remove_reviews_tab( $tabs ) {
					unset( $tabs['reviews'] );
					return $tabs;
				}
			}
		}
	}
	// change product tabs position
	if ( isset( $porto_settings['product-tabs-pos'] ) && 'below' == $porto_settings['product-tabs-pos'] ) {
		if ( is_customize_preview() || ! porto_is_ajax() ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 25 );
		}
	}

	// add to cart sticky
	if ( isset( $porto_settings['product-sticky-addcart'] ) && $porto_settings['product-sticky-addcart'] ) {
		add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_product_sticky_addcart', 5 );
	}

	// define woocommerce functions if use previous version
	if ( ! function_exists( 'wc_product_class' ) ) :
		function wc_product_class( $arg = array() ) {
			return post_class( $arg );
		}
	endif;
	if ( ! function_exists( 'wc_get_product_class' ) ) :
		function wc_get_product_class( $arg = array() ) {
			return get_post_class( $arg );
		}
	endif;

	// show price for logged in users
	if ( isset( $porto_settings['product-show-price-role'] ) && ! empty( $porto_settings['product-show-price-role'] ) ) {
		$hide_price = false;
		if ( ! is_user_logged_in() ) {
			$hide_price = true;
		} else {
			foreach ( wp_get_current_user()->roles as $role => $val ) {
				if ( ! in_array( $val, $porto_settings['product-show-price-role'] ) ) {
					$hide_price = true;
					break;
				}
			}
		}

		if ( $hide_price ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'porto_woocommerce_single_product_summary2', 'woocommerce_template_single_add_to_cart', 10 );
			remove_action( 'porto_woocommerce_loop_links_on_image', 'woocommerce_template_loop_add_to_cart' );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

			add_filter( 'porto_product_loop_show_price', '__return_false' );
		}
	}
}

function porto_woocommerce_get_price_html_empty( $price, $product ) {
	return '';
}
function porto_woocommerce_get_price_empty( $price, $param2, $param3 ) {
	return '';
}
function porto_woocommerce_disable_rating( $false ) {
	return 'no';
}
function porto_woocommerce_readmore_button() {
	global $porto_settings, $product;
	$more_link   = get_post_meta( get_the_id(), 'product_more_link', true );
	$more_target = ! empty( $porto_settings['catalog-readmore-target'] ) ? 'target="' . esc_attr( $porto_settings['catalog-readmore-target'] ) . '"' : '';
	if ( ! $more_link ) {
		$more_link = apply_filters( 'the_permalink', get_permalink() );
	}
	if ( 'variable' != $product->get_type() ) :
		?>
		<div class="cart">
	<?php endif; ?>
			<a <?php echo porto_filter_output( $more_target ); ?> href="<?php echo esc_url( $more_link ); ?>" class="single_add_to_cart_button button readmore"><?php echo wp_kses_post( $porto_settings['catalog-readmore-label'] ); ?></a>
	<?php if ( 'variable' != $product->get_type() ) : ?>
		</div>
		<?php
	endif;
}
// ajax products archive display
add_filter( 'pre_option_woocommerce_shop_page_display', 'porto_shop_page_display_ajax' );
function porto_shop_page_display_ajax( $value ) {
	$params = array( 'count', 'orderby', 'min_price', 'max_price' );
	foreach ( $params as $param ) {
		if ( ! empty( $_GET[ $param ] ) ) {
			return '';
		}
	}
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if ( $attribute_taxonomies ) {
		foreach ( $attribute_taxonomies as $tax ) {
			$attribute = wc_sanitize_taxonomy_name( $tax->attribute_name );
			$taxonomy  = wc_attribute_taxonomy_name( $attribute );
			$name      = 'filter_' . $attribute;
			if ( ! empty( $_GET[ $name ] ) && taxonomy_exists( $taxonomy ) ) {
				return '';
			}
		}
	}
	$page_num = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 0;
	if ( $page_num > 1 ) {
		return '';
	}
	return $value;
}
add_filter( 'get_woocommerce_term_metadata', 'porto_woocommerce_term_metadata_ajax', 10, 4 );
function porto_woocommerce_term_metadata_ajax( $value, $object_id, $meta_key, $single ) {
	if ( 'display_type' === $meta_key ) {
		$params = array( 'count', 'orderby', 'min_price', 'max_price' );
		foreach ( $params as $param ) {
			if ( ! empty( $_GET[ $param ] ) ) {
				return 'products';
			}
		}
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $tax ) {
				$attribute = wc_sanitize_taxonomy_name( $tax->attribute_name );
				$taxonomy  = wc_attribute_taxonomy_name( $attribute );
				$name      = 'filter_' . $attribute;
				if ( ! empty( $_GET[ $name ] ) && taxonomy_exists( $taxonomy ) ) {
					return 'products';
				}
			}
		}
		$page_num = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 0;
		if ( $page_num ) {
			return 'products';
		}
	}
	return $value;
}

add_filter( 'woocommerce_get_stock_html', 'porto_woocommerce_stock_html', 10, 2 );
function porto_woocommerce_stock_html( $availability_html, $product ) {
	if ( $product->is_type( 'simple' ) ) {
		return '';
	}
	return $availability_html;
}

add_action( 'woocommerce_product_meta_start', 'porto_woocommerce_add_stock_html', 10 );
function porto_woocommerce_add_stock_html() {
	global $product;
	if ( $product->is_type( 'simple' ) ) {
		$availability      = $product->get_availability();
		$availability_html = empty( $availability['availability'] ) ? '' : '<span class="product-stock ' . esc_attr( $availability['class'] ) . '">' . esc_html__( 'Availability', 'porto' ) . ': <span class="stock">' . esc_html( $availability['availability'] ) . '</span></span>';

		echo apply_filters( 'porto_woocommerce_stock_html', $availability_html, $availability['availability'], $product );
	}
}
add_action( 'wp', 'porto_woocommerce_init_layout' );
if ( porto_is_elementor_preview() ) {
	// For Elementor Preview
	porto_woocommerce_init_layout();
}
function porto_woocommerce_init_layout() {
	if ( is_admin() && ! porto_is_elementor_preview() ) {
		return;
	}

	// woocommerce single product layout
	global $porto_settings, $porto_product_layout, $porto_shop_filter_layout;
	if ( porto_is_product() || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) {
		$porto_product_layout = get_post_meta( get_the_ID(), 'product_layout', true );
		if ( ! $porto_product_layout ) {
			$builder_id = porto_check_builder_condition( 'product' );
			if ( $builder_id ) {
				$porto_product_layout = 'builder';
			}
		}
		$porto_product_layout = ( ! $porto_product_layout && isset( $porto_settings['product-single-content-layout'] ) ) ? $porto_settings['product-single-content-layout'] : $porto_product_layout;
		if ( ! $porto_product_layout ) {
			$porto_product_layout = 'default';
		}
	}
	if ( is_product_taxonomy() || is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) {
		$porto_shop_filter_layout = false;
		$term                     = get_queried_object();
		if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
			$porto_shop_filter_layout = get_metadata( $term->taxonomy, $term->term_id, 'filter_layout', true );
		}
		if ( ! $porto_shop_filter_layout && isset( $porto_settings['product-archive-filter-layout'] ) ) {
			$porto_shop_filter_layout = $porto_settings['product-archive-filter-layout'];
		}
	}

	// show/hide review and price
	if ( ! isset( $porto_settings['catalog-enable'] ) || ! $porto_settings['catalog-enable'] ) {
		if ( isset( $porto_settings['product-review'] ) && ! $porto_settings['product-review'] ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		}
		if ( isset( $porto_settings['product-price'] ) && ! $porto_settings['product-price'] ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		}
	}

	// product layout
	if ( $porto_product_layout ) {
		if ( 'extended' === $porto_product_layout ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 6 );
			add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_template_single_custom_block', 11 );
			remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
			add_action( 'woocommerce_after_variations_form', 'woocommerce_single_variation', 10 );
		} elseif ( 'grid' === $porto_product_layout ) {
			remove_action( 'woocommerce_single_product_summary', 'porto_woocommerce_sale_product_period', 15 );
			add_action( 'woocommerce_before_add_to_cart_button', 'porto_woocommerce_sale_product_period', 15 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 26 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		} elseif ( 'full_width' === $porto_product_layout ) {
			add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_template_single_custom_block', 1 );
			if ( ! isset( $porto_settings['product-tabs-pos'] ) || 'below' != $porto_settings['product-tabs-pos'] ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 50 );
			}
		} elseif ( 'sticky_info' === $porto_product_layout ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_sharing', 9 );
			add_action( 'woocommerce_after_single_product_summary', 'porto_woocommerce_template_single_custom_block', 8 );
		} elseif ( 'sticky_both_info' === $porto_product_layout ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			remove_action( 'woocommerce_single_product_summary', 'porto_woocommerce_product_nav', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_single_product_summary', 'porto_woocommerce_sale_product_period', 15 );
			add_action( 'porto_woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 5 );
			add_action( 'porto_woocommerce_before_single_product_summary', 'woocommerce_template_single_rating', 10 );
			add_action( 'porto_woocommerce_before_single_product_summary', 'woocommerce_template_single_sharing', 6 );
			add_action( 'porto_woocommerce_before_single_product_summary', 'porto_woocommerce_product_nav', 7 );
			add_action( 'porto_woocommerce_single_product_summary2', 'woocommerce_template_single_add_to_cart', 10 );
			add_action( 'woocommerce_before_add_to_cart_button', 'porto_woocommerce_sale_product_period', 15 );
		} elseif ( 'centered_vertical_zoom' === $porto_product_layout ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_single_product_summary', 'porto_woocommerce_sale_product_period', 15 );
			add_action( 'porto_woocommerce_single_product_summary2', 'woocommerce_template_single_add_to_cart', 10 );
			add_action( 'porto_woocommerce_single_product_summary2', 'porto_woocommerce_template_single_custom_block', 5 );
			add_action( 'woocommerce_before_add_to_cart_button', 'porto_woocommerce_sale_product_period', 15 );
		}

		if ( 'left_sidebar' === $porto_product_layout ) {
			add_action( 'woocommerce_after_single_product_summary', 'porto_woocommerce_output_related_products', 20 );
		} elseif ( 'builder' !== $porto_product_layout ) {
			add_action( 'porto_after_content_bottom', 'porto_woocommerce_output_related_products', 8 );
		}

		/* Fix elementor description issue in single product page */
		add_filter(
			'woocommerce_product_tabs',
			function( $tabs = array() ) {
				if ( ! defined( 'ELEMENTOR_VERSION' ) || isset( $tabs['description'] ) ) {
					return $tabs;
				}
				global $post;
				if ( $post->ID && get_post_meta( $post->ID, '_elementor_edit_mode', true ) ) {
					$elements_data = get_post_meta( $post->ID, '_elementor_data', true );

					if ( $elements_data ) {
						$elements_data = json_decode( $elements_data, true );
						if ( ! empty( $elements_data ) ) {
							$tabs['description'] = array(
								'title'    => __( 'Description', 'woocommerce' ),
								'priority' => 10,
								'callback' => 'woocommerce_product_description_tab',
							);
						}
					}
				}
				return $tabs;
			},
			20
		);
	}

	// horizontal filter
	$builder_id = porto_check_builder_condition( 'shop' );
	// Elementor Shop Builder
	if ( ( porto_vc_is_inline() || porto_is_elementor_preview() ) && ! isset( $porto_shop_filter_layout ) && isset( $porto_settings['product-archive-filter-layout'] ) ) {
		$porto_shop_filter_layout = $porto_settings['product-archive-filter-layout'];
	}
	if ( ! $builder_id && ( ( isset( $_REQUEST['elementor-preview'] ) && class_exists( 'PortoBuilders' ) && PortoBuilders::BUILDER_SLUG == get_post_type(  $_REQUEST['elementor-preview'] ) ) || ( porto_vc_is_inline() ) ) ) {
		$builder_id = true;
	}

	if ( $porto_shop_filter_layout && 'horizontal' == $porto_shop_filter_layout && $builder_id ) {
		remove_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div', 11 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		remove_action( 'woocommerce_before_shop_loop', 'porto_grid_list_toggle', 70 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
		remove_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div', 80 );
		/*add_action( 'porto_before_content', 'porto_woocommerce_open_before_clearfix_div', 80 );
		add_action( 'porto_before_content', 'porto_woocommerce_output_horizontal_filter', 82 );
		add_action( 'porto_before_content', 'woocommerce_catalog_ordering', 84 );
		add_action( 'porto_before_content', 'porto_grid_list_toggle', 88 );
		add_action( 'porto_before_content', 'woocommerce_pagination', 86 );
		add_action( 'porto_before_content', 'porto_woocommerce_close_before_clearfix_div', 90 );*/
	} else {
		add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_output_horizontal_filter', 25 );
	}

	// archive page infinite scroll
	if ( isset( $porto_settings['product-infinite'] ) && $porto_settings['product-infinite'] ) {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
		if ( $porto_shop_filter_layout && 'horizontal' == $porto_shop_filter_layout ) {
			remove_action( 'porto_before_content', 'woocommerce_pagination', 88 );
		}
	}
}

// woocommerce multilingual compatibility
add_filter( 'wcml_multi_currency_is_ajax', 'porto_multi_currency_ajax' );
function porto_multi_currency_ajax( $actions ) {
	$actions[] = 'porto_product_quickview';
	return $actions;
}



add_filter( 'body_class', 'porto_wc_body_class' );
function porto_wc_body_class( $classes ) {
	if ( is_cart() && porto_cart_version() == 'v2' ) {
		$classes[] = 'cart-v2';
	} elseif ( is_checkout() && porto_checkout_version() == 'v2' ) {
		$classes[] = 'checkout-v2';
	} elseif ( is_product() ) {
		global $product;
		if ( ! empty( $product ) && method_exists( $product, 'get_id' ) ) {
			$single_product_id = $product->get_id();
		} else {
			$single_product_id = get_the_ID();
		}
		if ( ! empty( $product ) && method_exists( $product, 'is_type' ) && $product->is_type( 'simple' ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				if ( $product_id == $single_product_id ) {
					$classes[] = 'single-add-to-cart';
					break;
				}
			}
		} else {
			if ( ! wp_doing_ajax() && ( ( isset( $_POST['product_id'] ) && $_POST['product_id'] == $single_product_id ) || ( isset( $_POST['add-to-cart'] ) && $_POST['add-to-cart'] == $single_product_id ) ) ) {
				$classes[] = 'single-add-to-cart';
			}
		}
	}

	// login popup
	global $porto_settings;
	if ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) {
		$classes[] = 'login-popup';
	}

	return $classes;
}

// woocommerce vendor start
if ( class_exists( 'WC_Vendors' ) ) {
	add_action( 'woocommerce_after_shop_loop_item_title', 'porto_wpvendors_product_seller_name', 2 );
	remove_action( 'woocommerce_product_meta_start', array( 'WCV_Vendor_Cart', 'sold_by_meta' ), 10, 2 );
	add_action( 'woocommerce_product_meta_start', 'porto_wc_vendors_sold_by_meta', 25, 2 );
	remove_action( 'woocommerce_after_shop_loop_item', array( 'WCV_Vendor_Shop', 'template_loop_sold_by' ), 9 );

	add_filter( 'user_contactmethods', 'porto_add_to_author_profile', 10, 1 );
	function porto_add_to_author_profile( $contactmethods ) {

		$contactmethods['phone_number'] = __( 'Phone Number', 'porto' );
		$contactmethods['facebook_url'] = __( 'Facebook Profile URL', 'porto' );
		$contactmethods['gplus_url']    = __( 'Google Plus Profil URL', 'porto' );
		$contactmethods['twitter_url']  = __( 'Twitter Profile URL', 'porto' );
		$contactmethods['linkedin_url'] = __( 'Linkedin Profile URL', 'porto' );
		$contactmethods['youtube_url']  = __( 'Youtube Profile URL', 'porto' );
		$contactmethods['flickr_url']   = __( 'Flickr Profile URL', 'porto' );

		return $contactmethods;
	}

	function porto_wpvendors_product_seller_name() {

		global $porto_settings;
		$product_id = get_the_ID();
		$author     = WCV_Vendors::get_vendor_from_product( $product_id );

		$vendor_display_name = WC_Vendors::$pv_options->get_option( 'vendor_display_name' );

		switch ( $vendor_display_name ) {
			case 'display_name':
				$vendor      = get_userdata( $author );
				$vendor_name = $vendor->display_name;
				break;
			case 'user_login':
				$vendor      = get_userdata( $author );
				$vendor_name = $vendor->user_login;
				break;
			default:
				$vendor_name = '';
				$vendor_name = WCV_Vendors::is_vendor( $author ) ? WCV_Vendors::get_vendor_shop_name( $author ) : get_bloginfo( 'name' );

		}

		$sold_by = ( WCV_Vendors::is_vendor( $author ) ) ? sprintf( '<a href="%s">%s</a>', WCV_Vendors::get_vendor_shop_page( $author ), $vendor_name ) : get_bloginfo( 'name' );
		if ( ! empty( $porto_settings['porto_wcvendors_shop_soldby'] ) ) {
			echo '<p class="product-seller-name">' . apply_filters( 'wcvendors_sold_by_in_loop', __( 'By', 'porto' ) ) . ' <span>' . wp_kses_post( $sold_by ) . '</span> </p>';
		}

	}

	function porto_wc_vendors_sold_by_meta() {
		global $porto_settings;
		$author_id           = get_the_author_meta( 'ID' );
		$vendor_display_name = WC_Vendors::$pv_options->get_option( 'vendor_display_name' );

		switch ( $vendor_display_name ) {
			case 'display_name':
				$vendor      = get_userdata( $author_id );
				$vendor_name = $vendor->display_name;
				break;
			case 'user_login':
				$vendor      = get_userdata( $author_id );
				$vendor_name = $vendor->user_login;
				break;
			default:
				$vendor_name = '';
				$vendor_name = ( WCV_Vendors::is_vendor( $author_id ) ) ? WCV_Vendors::get_vendor_shop_name( $author_id ) : get_bloginfo( 'name' );
		}

		$sold_by = WCV_Vendors::is_vendor( $author_id ) ? sprintf( '<a href="%s">%s</a>', WCV_Vendors::get_vendor_shop_page( $author_id ), $vendor_name ) : get_bloginfo( 'name' );

		if ( ! empty( $porto_settings['porto_wcvendors_product_soldby'] ) ) {
			echo '<ul class="list-item-details"><li class="list-item-details"><span class="data-type">' . esc_html__( 'Sold by : ', 'porto' ) . '</span><span class="value">' . wp_kses_post( $sold_by ) . '</span></li></ul>';
		}
	}

	add_action( 'show_user_profile', 'porto_user_profile_fields' );
	add_action( 'edit_user_profile', 'porto_user_profile_fields' );
	function porto_user_profile_fields( $user ) {
		$r = get_user_meta( $user->ID, 'picture', true );
		?>
		<!-- Artist Photo Gallery -->
		<h3><?php esc_html_e( 'Public Profile - Gallery', 'porto' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Picture', 'porto' ); ?></th>
				<td><input type="file" name="picture" value="" /></td>
			</tr>
			<tr>
				<td>
					<?php
					if ( $r && isset( $r['url'] ) ) {
						$r = $r['url'];
						echo '<img width="200" src="' . esc_url( $r ) . '" alt="profile" />';
					}
					?>
				</td>
			</tr>
		</table> 
		<?php
	}
	add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
	add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

	function save_extra_user_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false; }
		$_POST['action'] = 'wp_handle_upload';
		$r               = wp_handle_upload( $_FILES['picture'] );
		update_user_meta( $user_id, 'picture', $r, get_user_meta( $user_id, 'picture', true ) );
	}
	add_action( 'user_edit_form_tag', 'make_form_accept_uploads' );
	function make_form_accept_uploads() {
		echo ' enctype="multipart/form-data"';
	}
	// Woocommerce Vendor Function More Product
	function porto_more_seller_product() {
		global $product;
		if ( empty( $product ) || ! $product->exists() ) {
			return;
		}
		global $post;
		if ( ! WCV_Vendors::is_vendor( $post->post_author ) ) {
			return;
		}
		$meta_query = WC()->query->get_meta_query();
		$args       = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
			'posts_per_page'      => 4,
			'author'              => get_the_author_meta( 'ID' ),
			'meta_query'          => $meta_query,
			'orderby'             => 'desc',
		);
		$products   = new WP_Query( $args );

		if ( $products->have_posts() ) :
			global $porto_woocommerce_loop;
			$porto_woocommerce_loop['columns'] = isset( $porto_settings['product-related-cols'] ) ? $porto_settings['product-related-cols'] : ( isset( $porto_settings['product-cols'] ) ? $porto_settings['product-cols'] : 3 );
			if ( ! $porto_woocommerce_loop['columns'] ) {
				$porto_woocommerce_loop['columns'] = 4;
			}

			?>
			<div class="related products">
				<h2 class="slider-title"><span class="inline-title"><?php esc_html_e( 'More from this seller&hellip;', 'porto' ); ?></span></h2>

				<?php woocommerce_product_loop_start(); ?>
					<?php
					while ( $products->have_posts() ) :
						$products->the_post();
						?>
						<?php wc_get_template_part( 'content', 'product' ); ?>
					<?php endwhile; // end of the loop. ?>
				<?php woocommerce_product_loop_end(); ?>

			</div>

			<?php
		endif;
		wp_reset_postdata();
	}
}


add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'porto_woocommerce_dropdown_variation_attribute_options_html', 10, 2 );
function porto_woocommerce_dropdown_variation_attribute_options_html( $select_html, $args ) {
	global $porto_settings;

	$args      = wp_parse_args(
		apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
		array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' ),
		)
	);
	$options   = $args['options'];
	$product   = $args['product'];
	$attribute = $args['attribute'];

	// show description of selected attribute
	$attr_description_html = '';
	if ( empty( $args['no_term_desc'] ) && isset( $porto_settings['product-attr-desc'] ) && $porto_settings['product-attr-desc'] && ! empty( $attribute ) && ! empty( $product ) && taxonomy_exists( $attribute ) ) {

		if ( empty( $options ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}
		$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

		/* translators: %s: Attribute title */
		$attr_description_html .= '<div class="product-attr-description' . ( $args['selected'] ? ' active' : '' ) . '"><a href="#"><i class="fas fa-exclamation-circle"></i> ' . sprintf( esc_html__( 'Read More About %s', 'porto' ), '<span>' . ( $args['name'] ? esc_html( $args['name'] ) : wc_attribute_label( $attribute ) . '</span>' ) ) . '</a><div>';
		foreach ( $terms as $term ) {
			if ( in_array( $term->slug, $options ) ) {
				if ( $term->description ) {
					$attr_description_html .= '<div class="attr-desc' . ( sanitize_title( $args['selected'] ) == $term->slug ? '  active' : '' ) . '" data-attrid="' . esc_attr( $term->slug ) . '">' . wp_kses_post( $term->description ) . '</div>';
				} else {
					$attr_description_html .= '<div class="attr-desc' . ( sanitize_title( $args['selected'] ) == $term->slug ? '  active' : '' ) . '" data-attrid="' . esc_attr( $term->slug ) . '">' . esc_html__( 'No description', 'porto' ) . '</div>';
				}
			}
		}
		$attr_description_html .= '</div></div>';
	}

	if ( isset( $porto_settings['product_variation_display_mode'] ) && 'select' === $porto_settings['product_variation_display_mode'] ) {
		return $select_html . $attr_description_html;
	}

	$name             = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id               = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class            = $args['class'];
	$show_option_none = $args['show_option_none'] ? true : false;

	$attr_type            = '';
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if ( $attribute_taxonomies ) {
		foreach ( $attribute_taxonomies as $tax ) {
			if ( wc_attribute_taxonomy_name( $tax->attribute_name ) === $attribute ) {
				if ( in_array( $tax->attribute_type, array( 'color', 'label', 'image', 'select' ) ) ) {
					$attr_type = $tax->attribute_type;
					break;
				}
			}
		}
	}

	if ( 'select' == $attr_type ) {
		$prev_select_html = $select_html;
	}

	if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ];
	}

	$html = '';
	if ( ! empty( $options ) ) {
		$swatch_options        = $product->get_meta( 'swatch_options', true );
		$key                   = md5( sanitize_title( $attribute ) );
		$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		$html .= '<ul class="filter-item-list" data-name="' . esc_attr( $name ) . '">';
		if ( $product ) {
			$select_html  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
			$select_html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

			$attribute_terms = array();

			if ( taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options ) ) {
						$attribute_terms[] = array(
							'id'      => md5( $term->slug ),
							'slug'    => $term->slug,
							'label'   => $term->name,
							'term_id' => $term->term_id,
						);
					}
				}
			} else {
				foreach ( $options as $term ) {
					$attribute_terms[] = array(
						'id'    => ( md5( sanitize_title( strtolower( $term ) ) ) ),
						'slug'  => $term,
						'label' => esc_html( $term ),
					);
				}
			}

			if ( isset( $swatch_options[ $key ] ) && isset( $swatch_options[ $key ]['type'] ) ) {
				if ( 'color' != $attr_type && 'color' == $swatch_options[ $key ]['type'] ) {
					$attr_type = 'color';
				} elseif ( 'image' == $swatch_options[ $key ]['type'] ) {
					$attr_type = 'image';
				}
			}

			if ( 'select' == $attr_type ) {
				return $prev_select_html . $attr_description_html;
			}

			$image_size = apply_filters( 'porto_swatches_image_size', 'swatches_image_size' );
			if ( 'image' == $attr_type && isset( $swatch_options[ $key ] ) && ! empty( $swatch_options[ $key ]['size'] ) ) {
				$image_size = $swatch_options[ $key ]['size'];
			}

			foreach ( $attribute_terms as $term ) {
				$color_value = '';
				if ( isset( $term['term_id'] ) ) {
					$color_value = get_term_meta( $term['term_id'], 'color_value', true );
				}

				if ( ( ! isset( $color_value ) || ! $color_value ) && isset( $swatch_options[ $key ] ) && isset( $swatch_options[ $key ]['attributes'][ $term['id'] ]['color'] ) ) {
					$color_value = $swatch_options[ $key ]['attributes'][ $term['id'] ]['color'];
				}
				$current_attribute_image_src = '';
				if ( 'image' == $attr_type ) {
					if ( isset( $swatch_options[ $key ] ) && ! empty( $swatch_options[ $key ]['attributes'][ $term['id'] ]['image'] ) ) {
						$current_attribute_image_id = $swatch_options[ $key ]['attributes'][ $term['id'] ]['image'];
					} elseif ( isset( $term['term_id'] ) ) {
						$current_attribute_image_id = get_term_meta( $term['term_id'], 'image_value', true );
					}
					if ( ! empty( $current_attribute_image_id ) ) {
						$current_attribute_image_src = wp_get_attachment_image_src( $current_attribute_image_id, $image_size );
						if ( is_array( $current_attribute_image_src ) ) {
							$current_attribute_image_src = $current_attribute_image_src[0];
						}
					}
				}

				if ( 'color' == $attr_type ) {
					$a_class      = 'filter-color';
					$option_attrs = ' data-color="' . esc_attr( $color_value ) . '"';
					$a_attrs      = ' title="' . esc_attr( apply_filters( 'woocommerce_variation_option_name', $term['label'] ) ) . '" style="background-color: ' . esc_attr( $color_value ) . ';border-color: ' . esc_attr( $color_value ) . '"';
				} elseif ( 'image' == $attr_type ) {
					$a_class      = 'filter-item filter-image';
					$option_attrs = ' data-image="' . esc_url( $current_attribute_image_src ) . '"';
					$a_attrs      = ' title="' . esc_attr( apply_filters( 'woocommerce_variation_option_name', $term['label'] ) ) . '"';
					if ( $current_attribute_image_src ) {
						$a_attrs .= ' style="background-image: url(' . esc_url( $current_attribute_image_src ) . ')"';
					}
				} else {
					$a_class = 'filter-item';
					if ( 'label' == $attr_type ) {
						$a_attrs     = ' title="' . esc_attr( apply_filters( 'woocommerce_variation_option_name', $term['label'] ) ) . '"';
						$label_value = get_term_meta( $term['term_id'], 'label_value', true );
					} else {
						$a_attrs = '';
					}
					$option_attrs = '';
				}

				$selected  = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $term['slug'] ), false ) : selected( $args['selected'], $term['slug'], false );
				$is_active = sanitize_title( $args['selected'] ) === $args['selected'] ? ( $args['selected'] == sanitize_title( $term['slug'] ) ) : ( $args['selected'] == $term['slug'] );

				$select_html .= '<option' . $option_attrs . ' value="' . esc_attr( $term['slug'] ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term['label'], isset( $term['term_id'] ) ? $term : null, $attribute, $product ) ) . '</option>';

				$html     .= '<li' . ( sanitize_title( $args['selected'] ) === $term['slug'] ? ' class="active"' : '' ) . '>';
					$html .= '<a role="button" href="#" class="' . $a_class . ( $is_active ? ' active' : '' ) . '" data-value="' . esc_attr( $term['slug'] ) . '"' . $a_attrs . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', 'label' == $attr_type && $label_value ? $label_value : $term['label'], isset( $term['term_id'] ) ? $term : null, $attribute, $product ) ) . '</a>';
				$html     .= '</li>';
			}
			$select_html .= '</select>';
		}
		$html .= '</ul>';
	}
	return $html . $select_html . $attr_description_html;
}

add_filter( 'woocommerce_layered_nav_term_html', 'porto_woocommerce_layed_nav_color_html', 10, 4 );
function porto_woocommerce_layed_nav_color_html( $term_html, $term, $link, $count ) {
	/**
	 * Compatibility with Variation Swatches for WooCommerce - Pro
	 * 
	 * @since 7.1.11
	 */
	if ( defined( 'WOO_VARIATION_SWATCHES_PRO_PLUGIN_VERSION' ) ) {
		$attribute = woo_variation_swatches()->get_frontend()->get_attribute_taxonomy_by_name( $term->taxonomy );
		if ( woo_variation_swatches()->get_frontend()->is_color_attribute( $attribute ) ) {
			$before_html = ob_get_clean();
			$before_html = str_replace( 'woocommerce-widget-layered-nav-list"', 'woocommerce-widget-layered-nav-list filter-item-list"', $before_html );
			ob_start();
			echo porto_filter_output( $before_html );
			return $term_html;
		}
	}

	$term_html   = '';
	$color_value = get_term_meta( $term->term_id, 'color_value', true );
	$label_value = get_term_meta( $term->term_id, 'label_value', true );
	$image_value = get_term_meta( $term->term_id, 'image_value', true );
	$attrs       = '';
	if ( $color_value ) {
		$attrs = ' class="filter-color" style="background-color: ' . esc_attr( $color_value ) . ';border-color: ' .  esc_attr( $color_value ) . '"';
	}
	if ( $image_value ) {
		$image_size                  = apply_filters( 'porto_swatches_image_size', 'swatches_image_size' );
		$current_attribute_image_src = wp_get_attachment_image_src( $image_value, $image_size );
		if ( is_array( $current_attribute_image_src ) ) {
			$current_attribute_image_src = $current_attribute_image_src[0];
		}
		$attrs = ' class="filter-image" style="min-width: 32px; background-repeat: no-repeat; background-size: cover; background-image: url(' . esc_url( $current_attribute_image_src ) . ');"';
	}

	// fix for yith ajax navigation
	if ( isset( $_GET['product_cat'] ) ) {
		$link = add_query_arg( array( 'product_cat' => $_GET['product_cat'] ), remove_query_arg( 'product_cat', $link ) );
	}

	$title = $label_value ? $label_value : $term->name;

	if ( $count > 0 ) {
		$link      = str_replace( '#038;', '&', $link );
		$link      = str_replace( '&&', '&', $link );
		$term_html = '<a href="' . esc_url( $link ) . '"' . $attrs . '>';
		if ( $color_value ) {
			$term_html .= '<span>';
		}
		$term_html .= esc_html( $title ) . '</span></a>';
		if ( $color_value ) {
			$term_html .= '</span>';
		}
		$term_html .= '</a>';
	} else {
		$link      = false;
		$term_html = '<span' . $attrs . '>';
		if ( $color_value ) {
			$term_html .= '<span>';
		}
		$term_html .= esc_html( $title ) . '</span></a>';
		if ( $color_value ) {
			$term_html .= '</span>';
		}
		$term_html .= '</span>';
	}
	if ( $color_value ) {
		$before_html = ob_get_clean();
		$before_html = str_replace( '"woocommerce-widget-layered-nav-list"', '"woocommerce-widget-layered-nav-list filter-item-list"', $before_html );
		ob_start();
		echo porto_filter_output( $before_html );
	}
	return $term_html;
}

add_filter( 'yith_woocommerce_reset_filter_link', 'porto_yith_woocommerce_reset_filter_link' );
function porto_yith_woocommerce_reset_filter_link( $link ) {
	if ( ! isset( $_GET['source_id'] ) && is_product_category() ) {
		global $wp_query;
		return esc_url( get_term_link( $wp_query->get_queried_object() ) );
	}
	return $link;
}

// horizontal filter
function porto_woocommerce_output_horizontal_filter( $el_class = '' ) {
	global $porto_shop_filter_layout, $porto_settings;
	if ( ( porto_is_elementor_preview() || porto_vc_is_inline() || isset( $_REQUEST['preview_id'] ) ) && empty( $porto_shop_filter_layout ) ) {
		$porto_shop_filter_layout = $porto_settings['product-archive-filter-layout'];
	}

	if ( ! isset( $porto_shop_filter_layout ) || ( function_exists( 'wcfmmp_is_store_page' ) && wcfmmp_is_store_page() ) ) {
		return;
	}

	if ( 'horizontal' === $porto_shop_filter_layout ) {
		if ( porto_is_ajax() && isset( $_COOKIE['porto_horizontal_filter'] ) && 'opened' == $_COOKIE['porto_horizontal_filter'] ) {
			$class = ' opened';
		} else {
			$class = '';
		}
		$class .= ' ' . $el_class;
		echo '<span class="porto-product-filters-toggle d-none d-lg-flex' . $class . '"><span>' . esc_html__( 'Filters:', 'porto' ) . '</span><a aria-label="Toggle the filter sidebar" role="button" href="#">&nbsp;</a></span>';
	} elseif ( 'horizontal2' === $porto_shop_filter_layout ) {
		if ( ! ( porto_is_elementor_preview() || porto_vc_is_inline() || isset( $_REQUEST['preview_id'] ) ) ) {
			echo '<div class="porto-product-filters style2 mobile-sidebar ' . esc_attr( '' !== $el_class ? $el_class: '' ) . '">';
				echo '<div class="porto-product-filters-body">';
					dynamic_sidebar( 'woo-category-filter-sidebar' );
				echo '</div>';
			echo '</div>';
			echo '<div class="sidebar-overlay"></div>';
		}
	}

	if ( 'offcanvas' === $porto_shop_filter_layout ) {
		echo '<a role="button" href="#" class="porto-product-filters-toggle sidebar-toggle d-inline-flex ' . esc_attr( '' !== $el_class ? $el_class: '' ) . '"><i class="fas fa-sliders-h"></i> <span>' . esc_html__( 'Filters', 'porto' ) . '</span></a>';
		$GLOBALS['porto_mobile_toggle'] = false;
	} elseif ( ! empty( $porto_settings['show-mobile-sidebar'] ) || 'horizontal2' === $porto_shop_filter_layout ) {
		echo '<a role="button" href="#" class="porto-product-filters-toggle sidebar-toggle d-flex d-lg-none ' . esc_attr( '' !== $el_class ? $el_class: '' ) . '"><svg data-name="Layer 3" id="Layer_3" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><line class="cls-1" x1="15" x2="26" y1="9" y2="9"/><line class="cls-1" x1="6" x2="9" y1="9" y2="9"/><line class="cls-1" x1="23" x2="26" y1="16" y2="16"/><line class="cls-1" x1="6" x2="17" y1="16" y2="16"/><line class="cls-1" x1="17" x2="26" y1="23" y2="23"/><line class="cls-1" x1="6" x2="11" y1="23" y2="23"/><path class="cls-2" d="M14.5,8.92A2.6,2.6,0,0,1,12,11.5,2.6,2.6,0,0,1,9.5,8.92a2.5,2.5,0,0,1,5,0Z"/><path class="cls-2" d="M22.5,15.92a2.5,2.5,0,1,1-5,0,2.5,2.5,0,0,1,5,0Z"/><path class="cls-3" d="M21,16a1,1,0,1,1-2,0,1,1,0,0,1,2,0Z"/><path class="cls-2" d="M16.5,22.92A2.6,2.6,0,0,1,14,25.5a2.6,2.6,0,0,1-2.5-2.58,2.5,2.5,0,0,1,5,0Z"/></svg> <span>' . esc_html__( 'Filter', 'porto' ) . '</span></a>';
		$GLOBALS['porto_mobile_toggle'] = false;
	}
	if ( 'horizontal2' === $porto_shop_filter_layout ) {
		unset( $porto_shop_filter_layout );
	}
}



// get sale end date
function porto_woocommerce_get_sale_end_date( $vars, $product, $variation ) {
	if ( $variation->is_on_sale() ) {
		$date_diff = $variation->get_date_on_sale_to();
		if ( $date_diff ) {
			$date_diff                     = $date_diff->date( 'Y/m/d H:i:s' );
			$vars['porto_date_on_sale_to'] = $date_diff;
		}
	}
	return $vars;
}



// shortcodes ajax action
add_action( 'wp_ajax_porto_woocommerce_shortcodes_products', 'porto_woocommerce_shortcodes_products' );
add_action( 'wp_ajax_nopriv_porto_woocommerce_shortcodes_products', 'porto_woocommerce_shortcodes_products' );
function porto_woocommerce_shortcodes_products() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	$atts  = '';
	if ( isset( $_POST['ids'] ) ) {
		$atts .= ' ids="' . esc_attr( $_POST['ids'] ) . '"';
	}
	if ( $_POST['category'] ) {
		$atts .= ' category="' . esc_attr( $_POST['category'] ) . '"';
	}
	$atts .= ' columns="' . esc_attr( $_POST['columns'] ) . '"';
	$atts .= ' count="' . esc_attr( $_POST['count'] ) . '"';
	$atts .= ' orderby="' . esc_attr( $_POST['orderby'] ) . '"';
	if ( isset( $_POST['pagination_style'] ) ) {
		$atts .= ' pagination_style="' . esc_attr( $_POST['pagination_style'] ) . '"';
	}
	if ( isset( $_POST['product-page'] ) ) {
		$_GET['product-page'] = esc_attr( $_POST['product-page'] );
	}
	if ( isset( $_POST['per_page'] ) && $_POST['per_page'] ) {
		$atts .= ' per_page="' . esc_attr( $_POST['per_page'] ) . '"';
	}
	if ( isset( $_POST['order'] ) && $_POST['order'] ) {
		$atts .= ' order="' . esc_attr( $_POST['order'] ) . '"';
	}
	if ( isset( $_POST['view'] ) ) {
		$atts .= ' view="' . esc_attr( $_POST['view'] ) . '"';
	}
	if ( isset( $_POST['navigation'] ) ) {
		$atts .= ' navigation="' . esc_attr( $_POST['navigation'] ) . '"';
	}
	if ( isset( $_POST['nav_type'] ) ) {
		$atts .= ' nav_type="' . esc_attr( $_POST['nav_type'] ) . '"';
	}
	if ( isset( $_POST['nav_pos'] ) ) {
		$atts .= ' nav_pos="' . esc_attr( $_POST['nav_pos'] ) . '"';
	}
	if ( isset( $_POST['nav_pos2'] ) ) {
		$atts .= ' nav_pos2="' . esc_attr( $_POST['nav_pos2'] ) . '"';
	}
	if ( isset( $_POST['show_nav_hover'] ) ) {
		$atts .= ' show_nav_hover="' . esc_attr( $_POST['show_nav_hover'] ) . '"';
	}
	if ( isset( $_POST['pagination'] ) ) {
		$atts .= ' pagination="' . esc_attr( $_POST['pagination'] ) . '"';
	}
	if ( isset( $_POST['dots_pos'] ) ) {
		$atts .= ' dots_pos="' . esc_attr( $_POST['dots_pos'] ) . '"';
	}
	if ( isset( $_POST['dots_style'] ) ) {
		$atts .= ' dots_style="' . esc_attr( $_POST['dots_style'] ) . '"';
	}
	if ( isset( $_POST['autoplay'] ) ) {
		$atts .= ' autoplay="' . esc_attr( $_POST['autoplay'] ) . '"';
	}
	if ( isset( $_POST['autoplay_timeout'] ) ) {
		$atts .= ' autoplay_timeout="' . esc_attr( $_POST['autoplay_timeout'] ) . '"';
	}
	if ( isset( $_POST['grid_layout'] ) && $_POST['grid_layout'] ) {
		$atts .= ' grid_layout="' . esc_attr( $_POST['grid_layout'] ) . '"';
	}
	if ( isset( $_POST['grid_height'] ) && $_POST['grid_height'] ) {
		$atts .= ' grid_height="' . esc_attr( $_POST['grid_height'] ) . '"';
	}
	if ( isset( $_POST['spacing'] ) && $_POST['spacing'] ) {
		$atts .= ' spacing="' . esc_attr( $_POST['spacing'] ) . '"';
	}
	if ( isset( $_POST['use_simple'] ) && $_POST['use_simple'] ) {
		$atts .= ' use_simple="' . esc_attr( $_POST['use_simple'] ) . '"';
	}
	if ( isset( $_POST['shortcode'] ) && $_POST['shortcode'] ) {
		$atts .= ' shortcode="' . esc_attr( $_POST['shortcode'] ) . '"';
	}
	if ( ! empty( $_POST['status'] ) ) {
		$atts .= ' status="' . esc_attr( $_POST['status'] ) . '"';
	}
	if ( isset( $_POST['addlinks_pos'] ) && $_POST['addlinks_pos'] ) {
		$atts .= ' addlinks_pos="' . esc_attr( $_POST['addlinks_pos'] ) . '"';
	}
	if ( isset( $_POST['image_size'] ) && $_POST['image_size'] ) {
		$atts .= ' image_size="' . esc_attr( $_POST['image_size'] ) . '"';
	}
	echo '<div class="porto-products-response">';
	echo do_shortcode( '[porto_products' . $atts . ']' );
	if ( $_POST['category'] && isset( $_POST['category_description'] ) ) {
		$term = get_term_by( 'slug', $_POST['category'], 'product_cat', 'ARRAY_A' );
		if ( $term && isset( $term['description'] ) ) {
			porto_woocommerce_add_js_composer_shortcodes();
			echo '<div class="category-description">';
			echo do_shortcode( $term['description'] );
			echo '</div>';
		}
	}
	echo '</div>';

	// phpcs:enable
	die();
}

// shortcodes image size
remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
add_action( 'woocommerce_before_subcategory_title', 'porto_woocommerce_subcategory_thumbnail', 10 );

if ( ! function_exists( 'porto_woocommerce_subcategory_thumbnail' ) ) :
	function porto_woocommerce_subcategory_thumbnail( $category ) {

		global $porto_woocommerce_loop;
		$dimensions = false;
		if ( isset( $porto_woocommerce_loop['image_size'] ) && $porto_woocommerce_loop['image_size'] ) {
			$small_thumbnail_size = $porto_woocommerce_loop['image_size'];
		} else {
			$small_thumbnail_size = 'woocommerce_thumbnail';
		}
		$small_thumbnail_size = apply_filters( 'subcategory_archive_thumbnail_size', $small_thumbnail_size );
		$thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size );

			if ( $image && is_array( $image ) ) {
				if ( isset( $porto_woocommerce_loop['image_size'] ) && $porto_woocommerce_loop['image_size'] ) {
					$dimensions = array(
						'width'  => $image[1],
						'height' => $image[2],
					);
				}

				$image = $image[0];
			}
			$image_srcset = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $thumbnail_id, $small_thumbnail_size ) : false;
			$image_sizes  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $thumbnail_id, $small_thumbnail_size ) : false;

		} else {
			$image        = wc_placeholder_img_src();
			$image_srcset = false;
			$image_sizes  = false;
		}

		if ( $image ) {
			if ( ! $dimensions ) {
				$dimensions = wc_get_image_size( $small_thumbnail_size );
			}
			// Prevent esc_url from breaking spaces in urls for image embeds.
			// Ref: https://core.trac.wordpress.org/ticket/23605.
			$image = str_replace( ' ', '%20', $image );

			// Add responsive image markup if available.
			if ( $image_srcset && $image_sizes ) {
				echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" srcset="' . esc_attr( $image_srcset ) . '" sizes="' . esc_attr( $image_sizes ) . '" />';
			} else {
				echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
			}
		}
	}
endif;

// add variable product attributes & quantity input on shop pages
if ( class_exists( 'Uni_Cpo' ) ) {
	remove_filter( 'woocommerce_loop_add_to_cart_link', 'uni_cpo_add_to_cart_button', 10 );
	add_filter( 'porto_cpo_add_to_cart_url', 'porto_cpo_add_to_cart_url', 10, 2 );
	add_filter( 'woocommerce_loop_add_to_cart_args', 'porto_cpo_add_to_cart_args', 10, 2 );
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'porto_woocommerce_display_quantity_input_on_shop_page', 10, 2 );
add_action( 'woocommerce_after_shop_loop_item', 'porto_woocommerce_display_variation_on_shop_page' );

function porto_woocommerce_display_quantity_input_on_shop_page( $html, $product ) {

	global $porto_settings, $porto_woocommerce_loop;
	$availability = $product->get_availability();
	$stock_status = ! empty( $availability['class'] ) ? $availability['class'] : '';
	if ( ( ( isset( $porto_settings['category-addlinks-pos'] ) && 'quantity' == $porto_settings['category-addlinks-pos'] ) || ( isset( $porto_woocommerce_loop['addlinks_pos'] ) && 'quantity' == $porto_woocommerce_loop['addlinks_pos'] ) ) && $product->is_type( 'simple' ) && $product->is_purchasable() && 'out-of-stock' !== $stock_status ) {
		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			), $product
		);
	}

	return $html;
}

function porto_cpo_add_to_cart_url( $add_to_cart_url, $product ) {
	if ( class_exists( 'Uni_Cpo' ) ) {
		$product_id   = intval( $product->get_id() );
		$product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
		if ( 'on' === $product_data['settings_data']['cpo_enable'] ) {
			return get_permalink( $product_id );
		}
	}
	return $add_to_cart_url;
}

function porto_cpo_add_to_cart_args( $args, $product ) {

	if ( class_exists( 'Uni_Cpo' ) ) {
		$product_id   = intval( $product->get_id() );
		$product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
		if ( ! empty( $args['class'] ) && 'on' === $product_data['settings_data']['cpo_enable'] ) {
			$args['class'] = str_replace( 'ajax_add_to_cart', '', $args['class'] );
		}
	}
	return $args;
}

function porto_woocommerce_display_variation_on_shop_page( $el_class = '', $quick_shop = false ) {

	global $product, $porto_settings, $porto_woocommerce_loop;
	if ( ( ( defined( 'REST_REQUEST' ) && ! empty( $_REQUEST['context'] ) && 'edit' == $_REQUEST['context'] ) || 
	( ( ! isset( $porto_woocommerce_loop['widget'] ) || ! $porto_woocommerce_loop['widget'] ) && isset( $porto_settings['show_swatch'] ) && $porto_settings['show_swatch'] ) )
	&& $product->is_type( 'variable' ) ) { // block editor or 
		$attributes                 = $product->get_variation_attributes();
		
		$woocommerce_taxonomies     = wc_get_attribute_taxonomies();
		$woocommerce_taxonomy_infos = array();
		foreach ( $woocommerce_taxonomies as $tax ) {
			$woocommerce_taxonomy_infos[ wc_attribute_taxonomy_name( $tax->attribute_name ) ] = $tax;
		}
		$swatch_options = $product->get_meta( 'swatch_options', true );
		if ( ! $quick_shop ) {
			foreach ( $attributes as $key => $attr ) {
				if ( array_key_exists( $key, $woocommerce_taxonomy_infos ) && isset( $woocommerce_taxonomy_infos[ $key ]->attribute_type ) ) {
					if ( 'color' != $woocommerce_taxonomy_infos[ $key ]->attribute_type && 'image' != $woocommerce_taxonomy_infos[ $key ]->attribute_type ) {
						if ( $swatch_options ) {
							$swatch_key = md5( sanitize_title( $key ) );
							if ( ! isset( $swatch_options[ $swatch_key ] ) || ! isset( $swatch_options[ $swatch_key ]['type'] ) || ( 'color' != $swatch_options[ $swatch_key ]['type'] && 'image' != $swatch_options[ $swatch_key ]['type'] ) ) {
								unset( $attributes[ $key ] );
							}
						} else {
							unset( $attributes[ $key ] );
						}
					}
				}
			}
		}
		if ( ! empty( $attributes ) ) {
			
			$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

			wc_get_template(
				'single-product/add-to-cart/variable.php',
				array(
					'available_variations' => $get_variations ? $product->get_available_variations() : false,
					'attributes'           => $attributes,
					'selected_attributes'  => $product->get_default_attributes(),
					'no_add_to_cart'       => true,
					'el_class'             => $el_class,
					'render_shop_swatch'   => true,
					'quick_shop'           => $quick_shop,
					'no_term_desc'         => true,
				)
			);
		}
	}
}


// add featured product in product categories shortcode
add_action( 'woocommerce_after_subcategory', 'porto_product_categories_add_featured', 20, 1 );
if ( ! function_exists( 'porto_product_categories_add_featured' ) ) :
	function porto_product_categories_add_featured( $category ) {
		global $porto_woocommerce_loop, $woocommerce_loop;
		if ( isset( $porto_woocommerce_loop['product_categories_show_featured'] ) && $porto_woocommerce_loop['product_categories_show_featured'] && $category ) {
			$porto_woocommerce_loop_backup = $porto_woocommerce_loop;
			$woocommerce_loop_backup       = $woocommerce_loop;
			unset( $GLOBALS['porto_woocommerce_loop'] );
			unset( $GLOBALS['woocommerce_loop'] );

			global $porto_woocommerce_loop;
			$porto_woocommerce_loop = array( 'use_simple_layout' => true );

			echo do_shortcode( '[featured_products per_page="1" columns="1" category="' . esc_attr( $category->slug ) . '"]' );

			global $porto_woocommerce_loop, $woocommerce_loop;
			$porto_woocommerce_loop = $porto_woocommerce_loop_backup;
			$woocommerce_loop       = $woocommerce_loop_backup;
		}
	}
endif;

if ( ! function_exists( 'porto_woocommerce_get_catalog_ordering_args' ) ) :
	/**
	 * Add sales order by
	 */
	function porto_woocommerce_get_catalog_ordering_args( $args, $orderby, $order ) {
		if ( 'total_sales' == $orderby ) {
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['order']    = 'DESC';
			$args['orderby']  = 'meta_value_num';
		}
		return $args;
	}
endif;

if ( ! function_exists( 'porto_woocommerce_widget_product_review_item_add_desc' ) ) :

	function porto_woocommerce_widget_product_review_item_add_desc( $args ) {
		if ( isset( $args ) && isset( $args['comment'] ) ) {
			?>
			<div class="description">
				<?php echo wp_kses_post( $args['comment']->comment_content ); ?>
			</div>
			<?php
			return;
		}
	}
endif;

/**
 * Make 2 columns for product reviews widget
 */
function porto_woocommerce_before_widget_product_review_list( $start_wrapper ) {
	return '<ul class="product_list_widget has-ccols ccols-2" role="none">';
}

/**
 * Template of Yith Compare
 *
 * @since 6.1
 */
if ( ! function_exists( 'porto_template_loop_compare' ) ) {
	function porto_template_loop_compare( $extra_class = '', $hide_title = false, $icon_cls = '', $icon_pos = 'left', $icon_cls_added = '' ) {
		global $product;
		if ( defined( 'YITH_WOOCOMPARE' ) && isset( $product ) ) {

			$css_class  = 'compare yith-compare' . ( $extra_class ? ' ' . trim( $extra_class ) : ' button' );
			$product_id = $product->get_id();
			$url_args   = array( 'id' => $product_id );
			if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
				$url_args['lang'] = ICL_LANGUAGE_CODE;
			}

			$has_added = false;
			if ( version_compare( YITH_WOOCOMPARE_VERSION, '3.0.0', '>=' ) ) {
				$has_added = YITH_WooCompare_Products_List::instance()->has( $product_id );
			} else {
				$cookie_name = 'yith_woocompare_list';
				if ( function_exists( 'is_multisite' ) && is_multisite() ) {
					$cookie_name .= '_' . get_current_blog_id();
				}
				$the_list = isset( $_COOKIE[ $cookie_name ] ) ? json_decode( $_COOKIE[ $cookie_name ] ) : array();
				$has_added = in_array( $product_id, $the_list );
			}

			$button_text = '';
			if ( $has_added ) {
				$css_class         .= ' added';
				$url_args['action'] = 'yith-woocompare-view-table';

				$title_text = esc_html( apply_filters( 'yith_woocompare_compare_added_label', __( 'Already Added', 'porto' ) ) );
				if ( ! $hide_title ) {
					$button_text = $title_text;
				}
				
				if ( $icon_cls_added ) {
					if ( 'left' == $icon_pos ) {
						$button_text = '<i class="' . esc_attr( $icon_cls_added ) . '"></i>' . $button_text;
					} else {
						$button_text = $button_text . '<i class="' . esc_attr( $icon_cls_added ) . '"></i>';
					}
				}
			} else {
				$url_args['action'] = 'yith-woocompare-add-product';

				$title_text = esc_html( get_option( 'yith_woocompare_button_text', __( 'Add To Compare', 'porto' ) ) );
				if ( ! $hide_title ) {
					$button_text = $title_text;
				}
				if ( $icon_cls ) {
					if ( 'left' == $icon_pos ) {
						$button_text = '<i class="' . esc_attr( $icon_cls ) . '"></i>' . $button_text;
					} else {
						$button_text = $button_text . '<i class="' . esc_attr( $icon_cls ) . '"></i>';
					}
				}
			}

			$url = esc_url_raw( add_query_arg( $url_args, site_url() ) );
			printf( '<a href="%s" class="%s" title="%s" data-product_id="%d"' . ( $icon_cls ? ' data-add_icon="' . esc_attr( $icon_cls ) . '"' : '' ) . ( $icon_cls_added ? ' data-added_icon="' . esc_attr( $icon_cls_added ) . '"' : '' ) . ( 'right' == $icon_pos ? ' data-icon_pos="right"' : '' ) . ' data-hide_title="' . ( $hide_title ? 'hide' : 'show' ) . '" rel="nofollow">%s</a>', esc_url( $url ), esc_attr( $css_class ), esc_attr( $title_text ), $product_id, $button_text );

			wp_enqueue_script( 'yith-woocompare-main' );
		}
	}
}

/**
 * WCFM
 *
 * Filters args of wcfm sidebars
 */
if ( class_exists( 'WCFMmp' ) ) {
	add_filter( 'wcfmmp_store_sidebar_args', 'porto_store_sidebar_args' );
	add_filter( 'wcfmmp_store_lists_sidebar_args', 'porto_store_sidebar_args' );

	function porto_store_sidebar_args( $args ) {
		if ( ! empty( $args['before_title'] ) ) {
			$args['before_title'] = '<h3 class="widget-title">';
		}
		if ( ! empty( $args['after_title'] ) ) {
			$args['after_title'] = '</h3>';
		}
		return $args;
	}
}


/**
 * Track Product views
 *
 * @since 6.2.0
 */
/**
 * Track product views.
 */
/*
function porto_wc_track_product_view() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}
	$recently_viewed = get_theme_mod( '_recently_viewed', array() );
	if ( empty( $recently_viewed ) ) {
		return;
	}

	global $post;

	if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) { // @codingStandardsIgnoreLine.
		$viewed_products = array();
	} else {
		$viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) ); // @codingStandardsIgnoreLine.
	}

	// Unset if already in viewed products list.
	$keys = array_flip( $viewed_products );

	if ( isset( $keys[ $post->ID ] ) ) {
		unset( $viewed_products[ $keys[ $post->ID ] ] );
	}

	$viewed_products[] = $post->ID;

	if ( count( $viewed_products ) > 15 ) {
		array_shift( $viewed_products );
	}

	// Store for session only.
	wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
}

add_action( 'template_redirect', 'porto_wc_track_product_view', 20 );

if ( is_admin() ) {
	// save info if recently viewed products widget is used in the page
	add_action(
		'save_post',
		function( $post_id, $post = false ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( ! function_exists( 'get_current_screen' ) ) {
				return;
			}
			if ( 'inherit' == get_post_status( $post_id ) ) {
				return;
			}

			$is_used     = false;
			$widget_used = get_theme_mod( '_recently_viewed', array() );
			if ( isset( $_POST['action'] ) && 'elementor_ajax' == $_POST['action'] ) {
				$elementor_data = get_post_meta( $post_id, '_elementor_data', true );
				if ( $elementor_data && ( false !== strpos( $elementor_data, '"status":"viewed"' ) || false !== strpos( $elementor_data, '"widgetType":"wp-widget-woocommerce_recently_viewed_products"' ) ) ) {
					$is_used = true;
				}
			} else {
				$post_content = '';
				$screen       = get_current_screen();
				if ( $screen && 'post' == $screen->base && isset( $_POST['content'] ) ) {
					$post_content = wp_unslash( $_POST['content'] );
				}
				if ( ! $post_content ) {
					return;
				}
				if ( false !== stripos( $post_content, '[porto_widget_woo_recently_viewed ' ) ) {
					$is_used = true;
				} elseif ( false !== stripos( $post_content, '[porto_products ' ) && preg_match( '/\[porto_products[^]]*status="viewed"/', $post_content ) ) {
					$is_used = true;
				}
			}

			if ( $is_used ) {
				if ( ! in_array( $post_id, $widget_used ) ) {
					$widget_used[] = (int) $post_id;
				}
			} else {
				$index = array_search( $post_id, $widget_used );
				if ( false !== $index ) {
					unset( $widget_used[ $index ] );
				}
			}
			set_theme_mod( '_recently_viewed', $widget_used );
		}
	);

	add_action(
		'delete_post',
		function( $post_id ) {
			$widget_used = get_theme_mod( '_recently_viewed', array() );
			if ( in_array( $post_id, $widget_used ) ) {
				$index = array_search( $post_id, $widget_used );
				if ( false !== $index ) {
					unset( $widget_used[ $index ] );
					set_theme_mod( '_recently_viewed', $widget_used );
				}
			}
		}
	);
}
*/


if ( ! function_exists( 'porto_wc_track_product_view' ) ) {
	/**
	 * Track recently viewed products even if recently viewed widget is not active.
	 *
	 * @since 6.8.0
	 * @see wc_track_product_view
	 */
	function porto_wc_track_product_view() {
		if ( ! is_singular( 'product' ) ) {
			return;
		}
	
		global $post;
	
		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) { // @codingStandardsIgnoreLine.
			$viewed_products = array();
		} else {
			$viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) ); // @codingStandardsIgnoreLine.
		}
	
		// Unset if already in viewed products list.
		$keys = array_flip( $viewed_products );
	
		if ( isset( $keys[ $post->ID ] ) ) {
			unset( $viewed_products[ $keys[ $post->ID ] ] );
		}
	
		$viewed_products[] = $post->ID;
	
		if ( count( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}
	
		// Store for session only.
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
	}
	remove_action( 'template_redirect', 'wc_track_product_view', 20 );
	add_action( 'template_redirect', 'porto_wc_track_product_view', 20 );
}

/**
 * Compatibility with WooCommerce 8.3.0
 * 
 * @since 7.0.0
 */

add_filter( 'woocommerce_get_script_data', 'porto_wc_set_script_data', 99, 2 );

if ( ! function_exists( 'porto_wc_set_script_data' ) ) {
	function porto_wc_set_script_data( $params, $handle ) {
		if ( 'wc-cart-fragments' == $handle ) {
			$params['request_timeout'] = 15000;
		}
		return $params;
	}
}

/**
 * Disable the block mode on YITH Wishlist
 * 
 * @since 7.7.0
 */
add_action( 'activated_plugin', 'porto_disable_react_rendering_wrap' );

if ( ! function_exists( 'porto_disable_react_rendering_wrap' ) ) {
	function porto_disable_react_rendering_wrap( $plugin_uri ) {
		if ( 'yith-woocommerce-wishlist/init.php' === $plugin_uri ) {
			if ( 'php-templates' !== get_option( 'yith_wcwl_rendering_method' ) ) {
				update_option( 'yith_wcwl_rendering_method', 'php-templates' );
			}
		}
	}
}
