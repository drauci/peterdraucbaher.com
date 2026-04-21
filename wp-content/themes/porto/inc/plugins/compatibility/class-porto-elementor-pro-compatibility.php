<?php
/**
 * Elementor Pro Compatibility class
 *
 * @since 5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use ElementorPro\Modules\ThemeBuilder\Module;

if ( ! class_exists( 'Porto_Elementor_Pro_Compatibility' ) ) :
	class Porto_Elementor_Pro_Compatibility {

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );
			add_action( 'elementor/theme/register_locations', array( $this, 'remove_header_footer_locations' ), 105 );

			add_action( 'porto_elementor_pro_header_location', array( $this, 'do_header' ) );
			add_action( 'porto_elementor_pro_footer_location', array( $this, 'do_footer' ) );
		}

		/**
		 * @param Locations_Manager $manager
		 */
		public function register_locations( $manager ) {
			$manager->register_core_location( 'header' );
			$manager->register_core_location( 'footer' );

			$module  = Module::instance();
			$headers = $module->get_conditions_manager()->get_documents_for_location( 'header' );
			$footers = $module->get_conditions_manager()->get_documents_for_location( 'footer' );
			if ( ! empty( $headers ) || ! empty( $footers ) ) {
				global $porto_settings;
				if ( ! empty( $headers ) ) {
					$porto_settings['elementor_pro_header'] = true;
					$porto_settings['header-type-select']   = 'header_builder_p';

					$header_id = porto_check_builder_condition( 'header' );
					if ( $header_id ) {
						$porto_settings['conditions']['header'] = (int) array_key_first( $headers );
					}

					// fix header footer css is enqueued before theme css
					if ( ! Module::is_preview() ) {
						$locations_manager = Module::instance()->get_locations_manager();
						if ( $locations_manager && method_exists( $locations_manager, 'enqueue_styles' ) ) {
							remove_action( 'wp_enqueue_scripts', [ $locations_manager, 'enqueue_styles' ] );
							add_action( 'porto_enqueue_css', [ $locations_manager, 'enqueue_styles' ] );
						}
					}
				}
				if ( ! empty( $footers ) ) {
					$porto_settings['elementor_pro_footer'] = true;
				}
			}
		}

		public function do_header() {
			elementor_theme_do_location( 'header' );
		}

		public function do_footer() {
			elementor_theme_do_location( 'footer' );
		}

		public function remove_header_footer_locations() {
			$module        = ElementorPro\Modules\ThemeBuilder\Module::instance();
			$theme_support = $module->get_component( 'theme_support' );
			if ( $theme_support ) {
				remove_action( 'get_header', array( $theme_support, 'get_header' ) );
				remove_action( 'get_footer', array( $theme_support, 'get_footer' ) );
				remove_filter( 'show_admin_bar', array( $theme_support, 'filter_admin_bar_from_body_open' ) );
			}
		}
	}
endif;

new Porto_Elementor_Pro_Compatibility;
