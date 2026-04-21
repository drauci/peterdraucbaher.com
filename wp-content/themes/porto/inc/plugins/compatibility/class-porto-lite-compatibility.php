<?php
/**
 * Lite Speed Plugin Compatibility class
 *
 * @since 7.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_Lite_Compatibility {
	/**
	 * Constructor
	 */
	public function __construct() {
        add_filter( 'litespeed_optm_js_defer_exc', array( $this, 'delay_js_exclusions' ) );
        add_filter( 'litespeed_optimize_js_excludes', array( $this, 'exclude_combine_js' ) ); // Combine js
	}

    public function exclude_combine_js( $inline_js ) {
		$inline_js[] = 'webfont-queue';
        $inline_js[] = 'image-comparison.min.js';
        $inline_js[] = 'porto-marquee';
        $inline_js[] = PORTO_JS . '/theme-lazyload.min.js';
        $inline_js[] = PORTO_JS . '/libs/lazyload.min.js';
        $inline_js[] = 'isotope';
        $inline_js[] = 'imagesloaded';
        // Shop page
        $inline_js[] = 'ui/slider';
        $inline_js[] = 'accounting';
        $inline_js[] = 'wc-accounting';
        $inline_js[] = 'ui/mouse';
        $inline_js[] = 'ui/core';
        $inline_js[] = 'price-slider';
        $inline_js[] = 'apexcharts';
        $inline_js[] = 'infinite-scroll';
        $inline_js[] = PORTO_JS . '/theme.min.js';
        $inline_js[] = PORTO_JS . '/theme.js';
        $inline_js[] = 'js_porto_vars=';
        $inline_js[] = 'js_porto_vars =';
        $inline_js[] = 'wc_cart_fragments_params =';
        $inline_js[] = 'wc_cart_fragments_params=';
        $inline_js[] = 'jquery.slick';
        $inline_js[] = 'wc_add_to_cart_params=';
        $inline_js[] = 'wc_add_to_cart_params =';
        $inline_js[] = 'woocommerce_params =';
        $inline_js[] = 'woocommerce_params=';
        $inline_js[] = 'wc_add_to_cart_variation_params=';
        $inline_js[] = 'wc_add_to_cart_variation_params =';
        $inline_js[] = 'woocommerce_price_slider_params =';
        $inline_js[] = 'woocommerce_price_slider_params=';
        $inline_js[] = 'wc_single_product_params=';
        $inline_js[] = 'wc_single_product_params =';
        $inline_js[] = 'gsap.min.js';
        $inline_js[] = 'ScrollTrigger.min.js';
        $inline_js[] = 'jquery.hoverdir';

        // if ( wp_doing_ajax() ) {
            $inline_js[] = 'ultimate-carousel';
        // }
		return $inline_js;
    }


    public function delay_js_exclusions( $exclude_delay_js ) {
        global $porto_settings_optimize;
		$min_suffix = '';
		if ( ! empty( $porto_settings_optimize['minify_css'] ) ) {
			$min_suffix = '.min';
		}
        $exclude_arr = wp_parse_args(
            $exclude_delay_js,
            array(
                'modernizr',
                'webfont-queue',
                'ultimate-carousel',
            )
        );

        $exclude_arr[] = 'porto-marquee';
        $exclude_arr[] = 'image-comparison';
        $exclude_arr[] = PORTO_JS . '/theme-lazyload.min.js';
        $exclude_arr[] = PORTO_JS . '/libs/lazyload.min.js';
        $exclude_arr[] = 'isotope';
        $exclude_arr[] = 'imagesloaded';
        // Shop page
        $exclude_arr[] = 'ui/slider';
        $exclude_arr[] = 'accounting';
        $exclude_arr[] = 'wc-accounting';
        $exclude_arr[] = 'ui/mouse';
        $exclude_arr[] = 'ui/core';
        $exclude_arr[] = 'price-slider';
        $exclude_arr[] = 'apexcharts';
        $exclude_arr[] = 'infinite-scroll';
        $exclude_arr[] = PORTO_JS . '/theme' . $min_suffix . '.js';
        $exclude_arr[] = 'js_porto_vars=';
        $exclude_arr[] = 'js_porto_vars =';
        $exclude_arr[] = 'wc_cart_fragments_params =';
        $exclude_arr[] = 'wc_cart_fragments_params=';
        $exclude_arr[] = 'jquery.slick';
        $exclude_arr[] = 'wc_add_to_cart_params=';
        $exclude_arr[] = 'wc_add_to_cart_params =';
        $exclude_arr[] = 'woocommerce_params =';
        $exclude_arr[] = 'woocommerce_params=';
        $exclude_arr[] = 'wc_add_to_cart_variation_params=';
        $exclude_arr[] = 'wc_add_to_cart_variation_params =';
        $exclude_arr[] = 'woocommerce_price_slider_params =';
        $exclude_arr[] = 'woocommerce_price_slider_params=';
        $exclude_arr[] = 'wc_single_product_params=';
        $exclude_arr[] = 'wc_single_product_params =';
        $exclude_arr[] = 'gsap.min.js';
        $exclude_arr[] = 'ScrollTrigger.min.js';
        $exclude_arr[] = 'jquery.hoverdir';

        return $exclude_arr;
    }

}

new Porto_Lite_Compatibility();
