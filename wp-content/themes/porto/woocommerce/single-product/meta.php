<?php
/**
 * Single Product Meta
 *
 * @version     9.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $porto_settings;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( isset( $porto_settings['product-metas'] ) && in_array( 'sku', $porto_settings['product-metas'] ) && wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ! empty( $sku = $product->get_sku() ) ? esc_html( $sku ) : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>

	<?php endif; ?>
	<?php if ( function_exists( 'wc_product_has_global_unique_id' ) && isset( $porto_settings['product-metas'] ) && in_array( 'global_unique_id', $porto_settings['product-metas'] ) && ! empty( $global_unique_id = $product->get_global_unique_id() ) ) : ?>
	<?php
		$global_unique_id_type  = '';
		$global_unique_id_label = '';

		$arr = porto_check_product_code_type( $global_unique_id );
		if ( ! empty( $arr ) ) {
			$global_unique_id_type  = $arr[0];
			$global_unique_id_label = $arr[1];
		}
		if ( $global_unique_id_type ) :
	?>

			<span class="global_unique_id_wrapper"><?php echo esc_html( $global_unique_id_label ); ?><span>:</span> <span class="global_unique_id <?php echo esc_attr( $global_unique_id_type ); ?>"><?php echo esc_html( $global_unique_id ); ?></span></span>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	if ( isset( $porto_settings['product-metas'] ) && in_array( 'cats', $porto_settings['product-metas'] ) ) :
		echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' );
	endif;
	?>

	<?php
	if ( isset( $porto_settings['product-metas'] ) && in_array( 'tags', $porto_settings['product-metas'] ) ) :
		echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' );
	endif;
	?>

	<?php

	if ( ! ( class_exists( 'WC_Brands' ) && isset( $porto_settings['product-metas'] ) && in_array( 'brand', $porto_settings['product-metas'] ) ) ) {
		if ( ! empty( $GLOBALS['WC_Brands'] ) ) {
			remove_action( 'woocommerce_product_meta_end', array( $GLOBALS['WC_Brands'], 'show_brand' ) );
		}
	}

	do_action( 'woocommerce_product_meta_end' );

	if ( ! ( class_exists( 'WC_Brands' ) && isset( $porto_settings['product-metas'] ) && in_array( 'brand', $porto_settings['product-metas'] ) ) ) {
		if ( ! empty( $GLOBALS['WC_Brands'] ) ) {
			add_action( 'woocommerce_product_meta_end', array( $GLOBALS['WC_Brands'], 'show_brand' ) );
		}
	}
	?>

</div>
