<?php
/**
 * Mini-cart
 *
 * @version     10.0.0
 */

defined( 'ABSPATH' ) || exit;

global $porto_settings;

$has_items = ( WC()->cart && ! WC()->cart->is_empty() );

?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<?php
$items = sizeof( WC()->cart->get_cart() );
echo '<div class="total-count text-v-dark clearfix">';
	/* translators: %s: Items count */
	echo '<span>' . sprintf( esc_html( _n( '%d ITEM', '%d ITEMS', $items, 'porto' ) ), $items ) . '</span>';
	echo '<a class="text-v-dark pull-right text-uppercase" href="' . esc_url( wc_get_cart_url() ) . '">' . esc_html__( 'View cart', 'woocommerce' ) . '</a>';
echo '</div>';

?>

<ul class="cart_list product_list_widget scrollbar-inner <?php echo esc_attr( $args['list_class'] ); ?>">
	<?php if ( $has_items ) : ?>
		<?php do_action( 'woocommerce_before_mini_cart_contents' ); ?>
		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				/**
				 * This filter is documented in woocommerce/templates/cart/cart.php.
				 *
				 * @since 2.1.0
				 */
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_gallery_thumbnail' ), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

				?>
					<li class="woocommerce-mini-cart-item<?php echo ! empty( $cart_item['variation_id'] ) ? ' porto-variation-' . absint( $cart_item['variation_id'] ) : '', ' ' . esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>" data-key="<?php echo esc_attr( $cart_item_key ); ?>">
						<div class="product-image"><div class="inner">
					<?php if ( ! $_product->is_visible() ) : ?>
							<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>" aria-label="product">
								<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
						<?php
						echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'woocommerce_cart_item_remove_link',
							sprintf(
								'<a role="button" href="%s" class="remove remove-product" aria-label="%s" data-product_id="%s" data-cart_id="%s" data-product_sku="%s" data-success_message="%s"></a>',
								esc_url( function_exists( 'wc_get_cart_remove_url' ) ? wc_get_cart_remove_url( $cart_item_key ) : WC()->cart->get_remove_url( $cart_item_key ) ),
								/* translators: %s is the product name */
								esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
								esc_attr( $product_id ),
								esc_attr( $cart_item_key ),
								esc_attr( $_product->get_sku() ),
								/* translators: %s is the product name */
								esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) )
							),
							$cart_item_key
						);
						?>
						</div></div>
						<div class="product-details">
						<?php if ( ! $_product->is_visible() || empty( $product_permalink ) ) { ?>
							<?php echo wp_kses_post( $product_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php } else { ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>" class="text-v-dark">
								<?php echo wp_kses_post( $product_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php } ?>
						<?php do_action( 'porto_woocommerce_minicart_after_product_name', $_product ); ?>
						<?php echo function_exists( 'wc_get_formatted_cart_item_data' ) ? wc_get_formatted_cart_item_data( $cart_item ) : WC()->cart->get_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php
						$mini_cart = false;
						if ( ! $_product->is_sold_individually() && $_product->is_purchasable() && ! empty( $porto_settings['minicart-quantity'] ) ) {
							$mini_cart = true;
						}
							if ( $mini_cart ) {
								woocommerce_quantity_input(
									array(
										'input_value' => $cart_item['quantity'],
										'min_value' => 0,
										'max_value' => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									),
									$_product
								);
							}
						?>
						<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . ( $mini_cart ? sprintf( '&times; %s', $product_price ) : sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<div class="ajax-loading"></div>
					</li>
					<?php
			}
		}
		?>

		<?php do_action( 'woocommerce_mini_cart_contents' ); ?>

	<?php else : ?>

		<li class="woocommerce-mini-cart__empty-message empty">
			<?php
				esc_html_e( 'No products in the cart.', 'woocommerce' );
			?>
		</li>

	<?php endif; ?>

</ul><!-- end product list -->

<?php if ( $has_items ) : ?>

	<?php do_action( 'porto_before_mini_cart_total' ); ?>

	<p class="woocommerce-mini-cart__total total">
	<?php if ( has_action( 'woocommerce_widget_shopping_cart_total' ) ) : ?>
		<?php
		/**
		 * Woocommerce_widget_shopping_cart_total hook.
		 *
		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
		 */
		do_action( 'woocommerce_widget_shopping_cart_total' );
		?>
	<?php else : ?>
		<strong class="text-v-dark text-uppercase"><?php esc_html_e( 'Total', 'woocommerce' ); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?>
	<?php endif; ?>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>
<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
