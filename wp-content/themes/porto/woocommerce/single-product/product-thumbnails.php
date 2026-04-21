<?php
/**
 * Single Product Thumbnails
 *
 * @version     9.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $woocommerce, $product, $porto_product_layout, $porto_settings;

if ( ! $product || ! $product instanceof WC_Product ) {
	return '';
}

if ( 'extended' == $porto_product_layout || 'sticky_info' == $porto_product_layout || 'sticky_both_info' == $porto_product_layout || 'grid' == $porto_product_layout ) {
	return;
}

$attachment_ids     = $product->get_gallery_image_ids();
$thumbnails_classes = '';
$thumb_size         = 'woocommerce_thumbnail';
$gallery_thumbnail  = wc_get_image_size( 'gallery_thumbnail' );
if ( $gallery_thumbnail['width'] !== 150 ) {
	$thumb_size = 'woocommerce_gallery_thumbnail';
}

$thumb_size         = apply_filters( 'woocommerce_gallery_thumbnail_size', $thumb_size );
// $thumb_size         = has_image_size( 'shop_thumbnail' ) ? 'shop_thumbnail' : 'woocommerce_thumbnail';
if ( 'full_width' === $porto_product_layout || 'centered_vertical_zoom' === $porto_product_layout ) {
	$thumbnails_classes = 'product-thumbnails-inner';
} elseif ( 'transparent' === $porto_product_layout ) {
	$thumbnails_classes = 'product-thumbs-vertical-slider';
} else {
	$thumbnails_classes = 'product-thumbs-slider owl-carousel';
}

?>
<div class="product-thumbnails thumbnails">
	<?php
	$html = '<div class="' . esc_attr( $thumbnails_classes ) . ( 'product-thumbs-slider owl-carousel' == $thumbnails_classes ? ' has-ccols-spacing has-ccols ccols-' . intval( $porto_settings['product-thumbs-count'] ) : '' ) . '">';

	$attachment_id = method_exists( $product, 'get_image_id' ) ? $product->get_image_id() : get_post_thumbnail_id();

	if ( $attachment_id ) {

		$image_alt = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
		if ( empty( $image_alt ) && function_exists( 'woocommerce_get_alt_from_product_title_and_position' ) ) {
			$image_alt = woocommerce_get_alt_from_product_title_and_position( $product->get_title(), true, 0 );
		}
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, $thumb_size );

		if ( $image_thumb_link ) {
			$html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', '<div class="img-thumbnail"><img class="woocommerce-main-thumb img-responsive" alt="' . esc_attr( $image_alt ) . '" src="' . esc_url( $image_thumb_link[0] ) . '" width="' . esc_attr( $image_thumb_link[1] ) . '" height="' . esc_attr( $image_thumb_link[2] ) . '" /></div>', $attachment_id, $post->ID, '', 1 ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

	} else {

		$image_thumb_link = wc_placeholder_img_src();
		$html            .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', '<div class="img-thumbnail"><div class="inner"><img class="woocommerce-main-thumb img-responsive" alt="placeholder" src="' . esc_url( $image_thumb_link ) . '" /></div></div>', false, $post->ID, '', 1 ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	if ( $attachment_ids ) {
		foreach ( $attachment_ids as $key => $attachment_id ) {

			$image_thumb_link = wp_get_attachment_image_src( $attachment_id, $thumb_size );

			if ( isset( $image_thumb_link[0] ) ) {
				$image_alt = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
				if ( empty( $image_alt ) && function_exists( 'woocommerce_get_alt_from_product_title_and_position' ) ) {
					$image_alt = woocommerce_get_alt_from_product_title_and_position( $product->get_title(), false, $key );
				}
	
				$html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', '<div class="img-thumbnail"><img class="img-responsive" alt="' . esc_attr( $image_alt ) . '" src="' . esc_url( $image_thumb_link[0] ) . '" width="' . esc_attr( $image_thumb_link[1] ) . '" height="' . esc_attr( $image_thumb_link[1] ) . '" /></div>', $attachment_id, $post->ID, '', ( 2 + $key ) ); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}
	$html .= apply_filters( 'porto_single_product_after_thumbnails', '', false, 1 + count( $attachment_ids ) );

	$html .= '</div>';

	echo porto_filter_output( $html );

	?>
</div>
