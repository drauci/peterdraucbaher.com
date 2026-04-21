<?php

/**
 * Show options for ordering
 *
 * @version     9.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $porto_shop_filter_layout;

$id_suffix = wp_unique_id();
?>
<form class="woocommerce-ordering" method="get">
	<label for="woocommerce-orderby-<?php echo esc_attr( $id_suffix ); ?>"<?php echo 'default' == $porto_shop_filter_layout ? ' class="d-none d-lg-inline-block"' : ''; ?>><?php esc_html_e( 'Sort by:', 'woocommerce' ); ?> </label>
	<select name="orderby" class="orderby" aria-label="<?php esc_html_e( 'Sort by:', 'woocommerce' ); ?>" id="woocommerce-orderby-<?php echo esc_attr( $id_suffix ); ?>">
		<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
			<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
		<?php endforeach; ?>
	</select>
	<input type="hidden" name="paged" value="1" />

	<?php
		// Keep query string vars intact
	foreach ( $_GET as $key => $val ) {
		if ( 'orderby' === $key || 'submit' === $key || 'paged' === $key ) {
			continue;
		}
		if ( is_array( $val ) ) {
			foreach ( $val as $innerVal ) {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
			}
		} else {
			echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
		}
	}
	?>
</form>
