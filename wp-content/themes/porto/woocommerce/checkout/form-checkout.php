<?php
/**
 * Checkout Form
 *
 * @version     9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

/**
 * Compatibiity with German Market plugin
 * to fix the issue in add_woocommerce_de_templates function of WGM_Template.php
 * 
 * @since 7.1.5
 */
if ( ! has_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment' ) ) {
    add_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
}

get_template_part( 'woocommerce/checkout/form-checkout', porto_checkout_version() );

do_action( 'woocommerce_after_checkout_form', $checkout );
