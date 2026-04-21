<?php
/**
 * Displayed when no products are found matching the current query
 *
 * @version     7.8.0
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="woocommerce-no-products-found">
	<?php wc_print_notice( esc_html__( 'No products were found matching your selection.', 'woocommerce' ), 'notice' ); ?>
</div>
