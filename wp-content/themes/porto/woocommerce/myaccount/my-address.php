<?php
/**
 * My Addresses
 *
 * @version     9.3.0
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

	$shipping_enabled = wc_shipping_enabled();

if ( ! wc_ship_to_billing_address_only() && $shipping_enabled ) {
	$page_title    = apply_filters( 'woocommerce_my_account_my_address_title', __( 'My Addresses', 'porto' ) );
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$page_title    = apply_filters( 'woocommerce_my_account_my_address_title', __( 'My Address', 'porto' ) );
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}

$oldcol = 1;
$col    = 1;
?>

<h2 class="account-sub-title my-2"><i class="porto-icon-pin align-middle m-r-sm"></i><?php esc_html_e( 'Addresses', 'woocommerce' ); ?></h2>
<p class="myaccount_address font-weight-medium mb-0">
	<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); ?>
</p>

<?php
if ( ! wc_ship_to_billing_address_only() && $shipping_enabled ) {
	echo '<div class="u-columns woocommerce-Addresses col2-set addresses">';}
?>

<?php foreach ( $get_addresses as $name => $address_title ) : ?>
	<?php
		$address = wc_get_account_formatted_address( $name );
		$col     = $col * -1;
		$oldcol  = $oldcol * -1;
	?>

	<div class="u-column<?php echo 0 > $col ? 1 : 2; ?> col-<?php echo 0 > $oldcol ? 1 : 2; ?> woocommerce-Address address">
		<header class="woocommerce-Address-title title">
			<h2 class="account-sub-title font-size-xl mb-1"><?php echo esc_html( $address_title ); ?></h2>
		</header>

		<address>
		<?php
			echo ! $address ? esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ) : wp_kses_post( $address );
			/**
			 * Used to output content after core address fields.
			 *
			 * @param string $name Address type.
			 * @since 8.7.0
			 */
			do_action( 'woocommerce_my_account_after_my_address', $name );
		?>
		</address>
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit button wc-action-btn mt-3 px-4">
			<?php
				printf(
					/* translators: %s: Address title */
					$address ? esc_html__( 'Edit %s', 'woocommerce' ) : esc_html__( 'Add %s', 'woocommerce' ),
					esc_html( $address_title )
				);
			?>
		</a>
	</div>

<?php endforeach; ?>

<?php
if ( ! wc_ship_to_billing_address_only() && $shipping_enabled ) {
	echo '</div>';
}
