<?php

remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );

/* Cart & Checkout - Start */
function porto_cart_version() {
	global $porto_settings;
	$cart_ver = ( isset( $porto_settings['cart-version'] ) && $porto_settings['cart-version'] ) ? $porto_settings['cart-version'] : 'v1';
	return apply_filters( 'porto_filter_cart_version', $cart_ver );
}
add_action( 'init', 'porto_wc_cart_page' );
function porto_wc_cart_page() {
	global $porto_settings;
	if ( porto_cart_version() == 'v2' ) {
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 20 );

		add_action( 'woocommerce_before_cart_totals', 'porto_shipping_calculator', 1 );
	}
}
function porto_shipping_calculator() {
	if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
		do_action( 'woocommerce_cart_totals_before_shipping' );
		wc_cart_totals_shipping_html();
		do_action( 'woocommerce_cart_totals_after_shipping' );
	} elseif ( WC()->cart->needs_shipping() ) {
		woocommerce_shipping_calculator();
	}
}

function porto_checkout_version() {
	global $porto_settings;
	$checkout_ver = ( isset( $porto_settings['checkout-version'] ) && $porto_settings['checkout-version'] ) ? $porto_settings['checkout-version'] : 'v1';
	return apply_filters( 'porto_filter_checkout_version', $checkout_ver );
}
add_action( 'init', 'porto_wc_checkout_page' );
function porto_wc_checkout_page() {
	global $porto_settings;
	if ( porto_checkout_version() == 'v2' ) {
		add_action( 'woocommerce_review_order_before_payment', 'porto_woocommerce_review_order_before_payment' );
	}
}
function porto_woocommerce_review_order_before_payment() {
	echo '</div><div class="col-lg-6">';
}
/* End - Cart & Checkout */



/* Register/Login */
/**
* Add new register fields for WooCommerce registration.
*
* @return string Register fields HTML.
*/
function porto_wooc_extra_register_start_fields() {
	global $porto_settings;
	if ( isset( $porto_settings['reg-form-info'] ) && 'full' == $porto_settings['reg-form-info'] ) :
		?>
		<p class="form-row form-row-first">
			<label for="reg_billing_first_name"><?php esc_html_e( 'First Name', 'porto' ); ?><span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php echo ! empty( $_POST['billing_first_name'] ) ? esc_attr( $_POST['billing_first_name'] ) : ''; ?>" />
		</p>
		<p class="form-row form-row-last">
			<label for="reg_billing_last_name"><?php esc_html_e( 'Last Name', 'porto' ); ?><span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php echo ! empty( $_POST['billing_last_name'] ) ? esc_attr( $_POST['billing_last_name'] ) : ''; ?>" />
		</p>
		<div class="clear"></div>
		<?php
	endif;
}
add_action( 'woocommerce_register_form_start', 'porto_wooc_extra_register_start_fields' );
/**
* Validate the extra register fields.
*
* @param string $username             Current username.
* @param string $email                 Current email.
* @param object $validation_errors     WP_Error object.
*
* @return void
*/
function porto_wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
	global $porto_settings;
	if ( isset( $porto_settings['reg-form-info'] ) && 'full' == $porto_settings['reg-form-info'] ) {
		if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
			$validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'porto' ) );
		}
		if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
			$validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'porto' ) );
		}
	}
}
add_action( 'woocommerce_register_post', 'porto_wooc_validate_extra_register_fields', 10, 3 );
/**
* Save the extra register fields.
*
* @paramint $customer_id Current customer ID.
*
* @return void
*/
function porto_wooc_save_extra_register_fields( $customer_id ) {
	global $porto_settings;
	if ( isset( $porto_settings['reg-form-info'] ) && 'full' == $porto_settings['reg-form-info'] ) {
		if ( isset( $_POST['billing_first_name'] ) ) {
			// WordPress default first name field.
			update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
			// WooCommerce billing first name.
			update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
		}
		if ( isset( $_POST['billing_last_name'] ) ) {
			// WordPress default last name field.
			update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
			// WooCommerce billing last name.
			update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
		}
	}
}
add_action( 'woocommerce_created_customer', 'porto_wooc_save_extra_register_fields' );
// Confirm password field on the register form under My Accounts.
add_filter( 'woocommerce_registration_errors', 'registration_errors_validation', 10, 3 );
function registration_errors_validation( $reg_errors, $sanitized_user_login, $user_email ) {

	global $porto_settings, $woocommerce;
	if ( isset( $porto_settings['reg-form-info'] ) && 'full' == $porto_settings['reg-form-info'] && 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
		if ( isset( $_POST['confirm_password'] ) && strcmp( $_POST['password'], $_POST['confirm_password'] ) !== 0 ) {
			return new WP_Error( 'registration-error', __( 'Passwords do not match.', 'porto' ) );
		}
		return $reg_errors;
	}
	return $reg_errors;
}
/* End - Register/Login */


// account login popup
add_action( 'wp_ajax_porto_account_login_popup', 'porto_account_login_popup' );
add_action( 'wp_ajax_nopriv_porto_account_login_popup', 'porto_account_login_popup' );
function porto_account_login_popup() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	global $porto_settings;
	if ( ! is_checkout() && ! is_user_logged_in() && ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) ) {
		$is_facebook_login = porto_nextend_facebook_login();
		$is_google_login   = porto_nextend_google_login();
		$is_twitter_login  = porto_nextend_twitter_login();
		echo '<div id="login-form-popup" class="lightbox-content">';
		echo wc_get_template_part( 'myaccount/form-login' );
		if ( ( $is_facebook_login || $is_google_login || $is_twitter_login ) && get_option( 'woocommerce_enable_myaccount_registration' ) == 'yes' && ! is_user_logged_in() ) {
			echo wc_get_template_part( 'myaccount/login-social' );
		}
		echo '</div>';
		die();
	}
	// phpcs:enable
}

add_action( 'wp_ajax_porto_account_login_popup_login', 'porto_account_login_popup_login' );
add_action( 'wp_ajax_nopriv_porto_account_login_popup_login', 'porto_account_login_popup_login' );
function porto_account_login_popup_login() {

	$nonce_value = wc_get_var( $_REQUEST['woocommerce-login-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.
	$result      = false;
	if ( wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {
		try {
			$creds = array(
				'user_login'    => trim( $_POST['username'] ),
				'user_password' => $_POST['password'],
				'remember'      => isset( $_POST['rememberme'] ),
			);

			$validation_error = new WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

			if ( $validation_error->get_error_code() ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => '<strong>' . esc_html__(
							'Error:',
							'woocommerce'
						) . '</strong> ' . $validation_error->get_error_message(),
					)
				);
				die();
			}

			if ( empty( $creds['user_login'] ) ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => '<strong>' . esc_html__(
							'Error:',
							'woocommerce'
						) . '</strong> ' . esc_html__(
							'Username is required.',
							'woocommerce'
						),
					)
				);
				die();
			}

			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
				}
			}

			// Perform the login
			$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );
			if ( ! is_wp_error( $user ) ) {
				$result = true;
			}
		} catch ( Exception $e ) {
		}
	}
	if ( $result ) {
		echo json_encode(
			array(
				'loggedin' => true,
				'message'  => esc_html__(
					'Login successful, redirecting...',
					'porto'
				),
			)
		);
	} else {
		echo json_encode(
			array(
				'loggedin' => false,
				'message'  => esc_html__(
					'Wrong username or password.',
					'porto'
				),
			)
		);
	}
	die();
}
add_action( 'wp_ajax_porto_account_login_popup_register', 'porto_account_login_popup_register' );
add_action( 'wp_ajax_nopriv_porto_account_login_popup_register', 'porto_account_login_popup_register' );
function porto_account_login_popup_register() {

	$nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
	$nonce_value = isset( $_POST['woocommerce-register-nonce'] ) ? $_POST['woocommerce-register-nonce'] : $nonce_value;
	$result      = true;

	if ( wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {
		$username = 'no' === get_option( 'woocommerce_registration_generate_username' ) ? $_POST['username'] : '';
		$password = 'no' === get_option( 'woocommerce_registration_generate_password' ) ? $_POST['password'] : '';
		$email    = sanitize_email( $_POST['email'] );

		try {
			$validation_error = new WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );

			if ( $validation_error->get_error_code() ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => $validation_error->get_error_message(),
					)
				);
				die();
			}

			$new_customer = wc_create_new_customer( $email, wc_clean( $username ), $password );

			if ( is_wp_error( $new_customer ) ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => $new_customer->get_error_message(),
					)
				);
				die();
			}

			if ( apply_filters( 'woocommerce_registration_auth_new_customer', true, $new_customer ) ) {
				wc_set_customer_auth_cookie( $new_customer );
			}
		} catch ( Exception $e ) {
			$result = false;
		}
	}
	if ( $result ) {
		echo json_encode(
			array(
				'loggedin' => true,
				'message'  => esc_html__(
					'Register successful, redirecting...',
					'porto'
				),
			)
		);
	} else {
		echo json_encode(
			array(
				'loggedin' => false,
				'message'  => esc_html__(
					'Register failed.',
					'porto'
				),
			)
		);
	}
	die();
}


add_filter( 'wc_add_to_cart_message_html', 'porto_add_to_cart_message_html', 10, 3 );

/**
 * Change cart message
 *
 * Change cart message html in product detail simple
 *
 * @param String $message cart message
 * @param int|array $products Product ID list or single product ID.
 * @param bool $show_qty Should qty's be shown? Added in 2.6.0.
 * @return mixed
 **/
function porto_add_to_cart_message_html( $message, $products, $show_qty ) {

	$titles = array();
	$count  = 0;

	if ( ! is_array( $products ) ) {
		$products = array( $products => 1 );
		$show_qty = false;
	}

	if ( ! $show_qty ) {
		$products = array_fill_keys( array_keys( $products ), 1 );
	}

	foreach ( $products as $product_id => $qty ) {
		/* translators: %s: product name */
		$titles[] = apply_filters( 'woocommerce_add_to_cart_qty_html', ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ), $product_id ) . apply_filters( 'woocommerce_add_to_cart_item_name_in_quotes', sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), strip_tags( get_the_title( $product_id ) ) ), $product_id );
		$count   += $qty;
	}

	$titles = array_filter( $titles );
	/* translators: %s: product name */
	$added_text = sprintf( _n( '<strong class="single-cart-notice">%s</strong> <span class="font-weight-medium">has been added to your cart.</span>', '<strong class="single-cart-notice">%s</strong> <span class="font-weight-medium">have been added to your cart.</span>', $count, 'porto' ), wc_format_list_of_items( $titles ) );
	// Output success messages.
	if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
		$return_to = apply_filters( 'woocommerce_continue_shopping_redirect', wc_get_raw_referer() ? wp_validate_redirect( wc_get_raw_referer(), false ) : wc_get_page_permalink( 'shop' ) );
		$message   = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a> %s', esc_url( $return_to ), esc_html__( 'Continue shopping', 'woocommerce' ), $added_text );
	} else {
		$message = sprintf( '%s', $added_text );
	}
	return $message;
}

if ( ! function_exists( 'porto_woocommerce_add_to_cart_notification_html' ) ) :
	function porto_woocommerce_add_to_cart_notification_html() {
		global $porto_settings;
		?>
		<div class="after-loading-success-message style-<?php echo esc_attr( $porto_settings['add-to-cart-notification'] ); ?>">
		<?php if ( isset( $porto_settings['add-to-cart-notification'] ) && 2 === (int) $porto_settings['add-to-cart-notification'] ) : ?>
			<div class="background-overlay"></div>
			<div class="loader success-message-container">
				<div class="msg-box">
					<div class="msg"><?php esc_html_e( "You've just added this product to the cart", 'porto' ); ?>:<p class="product-name text-color-primary"></p></div>
				</div>
				<button class="button btn-primay viewcart" data-link=""><?php esc_html_e( 'View cart', 'woocommerce' ); ?></button>
				<button class="button btn-primay continue_shopping"><?php esc_html_e( 'Continue', 'woocommerce' ); ?></button>
			</div>
		<?php else : ?>
			<div class="success-message-container d-none">
				<div class="msg-box">
					<div class="msg">
						<?php /* translators: product name div */ ?>
						<?php printf( esc_html__( '%s has been added to your cart.', 'porto' ), '<div class="product-name"></div>' ); ?>
					</div>
				</div>
				<button class="btn btn-modern btn-sm btn-gray viewcart btn-sm" data-link=""><?php esc_html_e( 'View cart', 'woocommerce' ); ?></button>
				<a class="btn btn-modern btn-sm btn-dark continue_shopping" href="<?php echo esc_url( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : wc_get_page_permalink( 'checkout' ) ); ?>"><?php esc_html_e( 'Checkout', 'woocommerce' ); ?></a>
				<button aria-label="<?php esc_attr_e( 'Close the added to cart popup', 'porto' ); ?>" class="mfp-close text-color-dark"></button>
			</div>
		<?php endif; ?>
		</div>
		<?php
	}
endif;

/**
 * Add 'View Cart' button after add to cart
 * 
 * @since 6.8.0 Changed the point at which the action is added.
 **/
add_filter( 'woocommerce_add_to_cart_validation', function( $has_passed ) {
	if ( $has_passed ) {
		add_action( 'woocommerce_after_add_to_cart_button', 'porto_view_cart_after_add', defined( 'WC_STRIPE_PLUGIN_NAME' ) ? 8 : 35 );
	}
	return $has_passed;
}, PHP_INT_MAX );


function porto_view_cart_after_add() {
	printf(
		'<a href="%1$s" tabindex="1" class="wc-action-btn view-cart-btn button wc-forward">%2$s</a>',
		esc_url( wc_get_cart_url() ),
		esc_html__( 'View cart', 'woocommerce' )
	);
}

/**
 * Update the cart item.
 * 
 * @since 6.8.0
 */
if ( ! function_exists( 'porto_update_cart_item' ) ) {
	function porto_update_cart_item() {
		if ( ( isset( $_GET['item_id'] ) && $_GET['item_id'] ) && ( isset( $_GET['qty'] ) ) ) {
			global $woocommerce;
			if ( $_GET['qty'] ) {
				$woocommerce->cart->set_quantity( $_GET['item_id'], $_GET['qty'] );
			} else {
				$woocommerce->cart->remove_cart_item( $_GET['item_id'] );
			}
		}

		WC_AJAX::get_refreshed_fragments();
	}
	
	add_action( 'wp_ajax_porto_update_cart_item', 'porto_update_cart_item' );
	add_action( 'wp_ajax_nopriv_porto_update_cart_item', 'porto_update_cart_item' );
}

if ( ! function_exists( 'porto_catalog_mode_pages_redirect' ) ) {
	function porto_catalog_mode_pages_redirect() {
		global $porto_settings;

		// Return if Elementor Preivew or WPBakery Frontend Preview
		if ( porto_is_elementor_preview() || porto_vc_is_inline() ) {
			return;
		}
		// Catalog Mode
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
				$cart     = is_page( wc_get_page_id( 'cart' ) );
				$checkout = is_page( wc_get_page_id( 'checkout' ) );
		
				wp_reset_postdata();
		
				if ( $cart || $checkout ) {
					wp_redirect( home_url() );
					exit;
				}
			}
		}
		
		if ( isset( $porto_settings['catalog-enable'] ) && $porto_settings['catalog-enable'] ) {
			if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
				if ( ! $porto_settings['catalog-cart'] ) {
					$cart     = is_page( wc_get_page_id( 'cart' ) );
					$checkout = is_page( wc_get_page_id( 'checkout' ) );

					wp_reset_postdata();

					if ( $cart || $checkout ) {
						wp_redirect( home_url() );
						exit;
					}
				}
			}
		}
	}

	add_action( 'wp', 'porto_catalog_mode_pages_redirect', 10 );
}
