<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     10.0.0
 */

defined( 'ABSPATH' ) || exit;

$registration_at_checkout   = WC_Checkout::instance()->is_registration_enabled();
$login_reminder_at_checkout = 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' );

if ( is_user_logged_in() ) {
	return;
}

if ( $login_reminder_at_checkout ) :
?>
<?php if ( 'v2' == porto_checkout_version() ) : ?>
	<div class="clearfix">
		<a href="#" class="btn btn-primary showlogin pull-left"><?php esc_html_e( 'Login', 'woocommerce' ); ?></a>
	</div><br />
<?php else : ?>
	<div class="woocommerce-form-login-toggle mb-2">
		<?php echo apply_filters( 'woocommerce_checkout_login_message', esc_html__( 'Returning customer?', 'woocommerce' ) ) . // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		' <a href="#" class="showlogin font-weight-bold text-v-dark">' . esc_html__( 'Login', 'woocommerce' ) . '</a>'; ?>
	</div>
<?php endif; ?>
<?php
endif;

if ( $registration_at_checkout || $login_reminder_at_checkout ) :

	// Always show the form after a login attempt.
	$show_form = isset( $_POST['login'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
woocommerce_login_form(
	array(
		'message'  => esc_html__( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'woocommerce' ),
		'redirect' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : wc_get_page_permalink( 'checkout' ),
			'hidden'   => ! $show_form,
	)
);
endif;
