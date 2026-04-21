<?php
/**
 * WP Rocket Compatibility class
 *
 * @since 7.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_Rocket_Compatibility {
	/**
	 * Constructor
	 */
	public function __construct() {
        add_filter( 'rocket_delay_js_exclusions', array( $this, 'delay_js_exclusions' ) );
        add_filter( 'rocket_exclude_defer_js', array( $this, 'defer_js_exclusions' ) );
        add_filter( 'rocket_excluded_inline_js_content', array( $this, 'exclude_inline_js' ) ); // Combine js
	}

    public function exclude_inline_js( $inline_js ): array {
		$inline_js[] = 'webfont-queue';
        // if ( wp_doing_ajax() ) {
            $inline_js[] = 'ultimate-carousel';
        // }
		return $inline_js;
    }

    public function defer_js_exclusions( $exclude_defer_js ): array {
        global $porto_settings_optimize;
		$min_suffix = '';
		if ( ! empty( $porto_settings_optimize['minify_css'] ) ) {
			$min_suffix = '.min';
		}
        $exclude_arr = wp_parse_args(
            $exclude_defer_js,
            array(
                'modernizr',
            )
        );

        if ( ! empty( $porto_settings_optimize['rocket_compatibility'] ) ) {
            $is_theme  = false;
            $is_jquery = false;
            if ( wp_script_is( 'isotope' ) ) {
                $exclude_arr[] = 'isotope';
                $exclude_arr[] = 'imagesloaded';
                $is_theme = true;
            }
            if ( wp_script_is( 'porto-price-filter-chart' ) ) {
                $exclude_arr[] = 'ui/slider';
                $exclude_arr[] = 'accounting';
                $exclude_arr[] = 'wc-accounting';
                $exclude_arr[] = 'ui/core';
                $exclude_arr[] = 'ui/mouse';
                $exclude_arr[] = 'price-slider';
                $exclude_arr[] = 'apexcharts';
                $is_jquery     = true;
            } else if ( wp_script_is( 'wc-price-slider' ) ) {
                $exclude_arr[] = 'ui/slider';
                $exclude_arr[] = 'accounting';
                $exclude_arr[] = 'wc-accounting';
                $exclude_arr[] = 'ui/core';
                $exclude_arr[] = 'ui/mouse';
                $exclude_arr[] = 'price-slider';
                $is_jquery     = true;
            }
            if ( $is_theme ) {
                $exclude_arr[] = PORTO_JS . '/theme' . $min_suffix . '.js';
            }
            if ( $is_theme || $is_jquery ) {
                $exclude_arr[] = '/jquery-?[0-9.]*(.min|.slim|.slim.min)?.js';
            }
        }

        return $exclude_arr;
    }

    public function delay_js_exclusions( $exclude_delay_js ): array {
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

        if ( ! empty( $porto_settings_optimize['rocket_compatibility'] ) ) {
            $is_theme      = false;
            $is_jquery     = false;
            if ( wp_script_is( 'porto-marquee' ) ) {
                $exclude_arr[] = 'porto-marquee';
                $is_jquery     = true;
            }
            // if ( wp_script_is( 'porto-appear-animate' ) ) {
            //     $exclude_arr[] = 'porto-appear-animate';
            //     $is_jquery     = true;
            // }
            if ( wp_script_is( 'porto-dm-animate' ) ) {
                $exclude_arr[] = 'porto-dm-animate';
                $is_jquery     = true;
            }
            if ( wp_script_is( 'porto-image-comparison' ) ) {
                $exclude_arr[] = 'image-comparison';
                $is_jquery     = true;
            }
            if ( wp_script_is( 'lazyload' ) ) {
                $exclude_arr[] = PORTO_JS . '/theme-lazyload.min.js';
                $exclude_arr[] = PORTO_JS . '/libs/lazyload.min.js';
                $is_jquery = true;
            }
            if ( wp_script_is( 'isotope' ) ) {
                $exclude_arr[] = 'isotope';
                $exclude_arr[] = 'imagesloaded';
                $is_theme = true;
            }
            if ( wp_script_is( 'porto-price-filter-chart' ) ) {
                $exclude_arr[] = 'ui/slider';
                $exclude_arr[] = 'accounting';
                $exclude_arr[] = 'wc-accounting';
                $exclude_arr[] = 'ui/mouse';
                $exclude_arr[] = 'ui/core';
                $exclude_arr[] = 'price-slider';
                $exclude_arr[] = 'apexcharts';
                $is_jquery     = true;
            } else if ( wp_script_is( 'wc-price-slider' ) ) {
                $exclude_arr[] = 'ui/slider';
                $exclude_arr[] = 'accounting';
                $exclude_arr[] = 'wc-accounting';
                $exclude_arr[] = 'ui/mouse';
                $exclude_arr[] = 'ui/core';
                $exclude_arr[] = 'price-slider';
                $is_jquery     = true;
            }
            if ( $is_theme ) {
                $exclude_arr[] = PORTO_JS . '/theme' . $min_suffix . '.js';
            }
            if ( $is_theme || $is_jquery ) {
                $exclude_arr[] = '/jquery-?[0-9.]*(.min|.slim|.slim.min)?.js';
            }
        }

        return $exclude_arr;
    }

}

new Porto_Rocket_Compatibility();
