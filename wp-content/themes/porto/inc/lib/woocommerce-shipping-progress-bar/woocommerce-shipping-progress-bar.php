<?php
/**
 * Porto Woocommerce Free Shipping - Progress Bar
 *
 * @author     Porto Themes
 * @category   Library
 * @since      7.1.0
 */

if ( ! class_exists( 'Porto_Woocommerce_shipping_pbar' ) ) :
	class Porto_Woocommerce_shipping_pbar {
		
		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
		}

		public function init() {
            // Cart Dropdown & Cart Offcanvas
			add_action( 'porto_before_mini_cart_total', array( $this, 'shipping_progress_bar' ) );

            // before cart table of Cart Page
            add_action( 'woocommerce_before_cart_table', array( $this, 'shipping_progress_bar' ) );

            // After Total Price of Checkout Page
            add_action( 'porto_woocommerce_after_checkout_table', array( $this, 'shipping_progress_bar' ) );
            
		    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 20 );
        }
        /**
         * Enqueue styles
         *
         * @since 7.1.0
         */
        public function enqueue_styles() {
            wp_enqueue_style( 'porto-fs-progress-bar', PORTO_LIB_URI . '/woocommerce-shipping-progress-bar/shipping-progress-bar.css', array(), PORTO_VERSION );
        }

        // Show Progress Bar
        public function shipping_progress_bar() {
            if ( ! WC()->cart->needs_shipping() || ! WC()->cart->show_shipping() ) {
                return;
            }
    
            $free_shipping_threshold = 0;
            $subtotal                = WC()->cart->get_displayed_subtotal();
            $classes                 = 'porto-free-shipping';
            $free_shipping_by_coupon = false;
    
            // Check shipping packages.
            $packages = WC()->cart->get_shipping_packages();
            $package  = reset( $packages );
            $zone     = wc_get_shipping_zone( $package );
    
            foreach ( $zone->get_shipping_methods( true ) as $method ) {
                if ( 'free_shipping' === $method->id ) {
                    $free_shipping_threshold = $method->get_option( 'min_amount' );
                }
            }

            // WPML.
            if ( class_exists( 'woocommerce_wpml' ) && ! class_exists( 'WCML_Multi_Currency_Shipping' ) ) {
                global $woocommerce_wpml;
    
                $multi_currency = $woocommerce_wpml->get_multi_currency();

                if ( ! empty( $multi_currency->prices ) && method_exists( $multi_currency->prices, 'convert_price_amount' ) ) {
                    $free_shipping_threshold = $multi_currency->prices->convert_price_amount( $free_shipping_threshold );
                }
            }
    
            // Check coupons.
            if ( $subtotal && WC()->cart->get_coupons() ) {
                foreach ( WC()->cart->get_coupons() as $coupon ) {
                    $subtotal -= WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
                    if ( $coupon->get_free_shipping() ) {
                        $free_shipping_by_coupon = true;
                        break;
                    }
                }
            }
            $free_shipping_threshold = str_replace( array( get_woocommerce_currency_symbol(), html_entity_decode( get_woocommerce_currency_symbol() ), wc_get_price_thousand_separator() ), '', apply_filters( 'porto_free_shipping_threshold', $free_shipping_threshold ) );
    
            $free_shipping_threshold = (float) wc_format_decimal( $free_shipping_threshold );

            if ( ! $free_shipping_threshold ) {
                return;
            }

            $classes = apply_filters( 'porto_free_shipping_wrap_cls', $classes );

            if ( $subtotal < $free_shipping_threshold && ! $free_shipping_by_coupon ) :
                $percent = floor( ( $subtotal / $free_shipping_threshold ) * 100 );
                ?>
                <div class="<?php echo esc_attr( $classes ); ?>">
                    <div class="porto-free-shipping-notice">
                        <i class="porto-icon-package"></i>
                        <label>
                            <?php
                            $threshold = wc_price( $free_shipping_threshold - $subtotal );
                            printf(
                            /* translators: %s: The threshold */
                                esc_html__( 'Add %s to cart and get free shipping!', 'porto' ),
                                $threshold // phpcs:ignore WordPress.Security.EscapeOutput
                            );
                            ?>
                        </label>
                    </div>
                    <progress class="porto-free-shipping-bar porto-scroll-progress" max="100" value="<?php echo esc_attr( $percent ) ?>"></progress>
                </div>
            <?php else : // Success message. ?>
                <div class="<?php echo esc_attr( $classes ); ?>">
                    <div class="porto-free-shipping-notice fs-success"><?php esc_html_e( 'Your order qualifies for free shipping!', 'porto' ); ?></div>
                    <progress class="porto-free-shipping-bar porto-scroll-progress" max="100" value="100"></progress>
                </div>
                <?php
            endif;
        }
    }
endif;

new Porto_Woocommerce_shipping_pbar();