<?php
/**
 * Elementor Compatibility class
 *
 * @since 5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_Elementor_Compatibility {
	/**
	 * Constructor
	 */
	public function __construct() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'elementor_notice_js' ) );

		add_action( 'wp_ajax_porto_elementor_disable_default_styles', array( $this, 'disable_default_styles' ) );

		add_action( 'porto_admin_save_theme_settings', array( $this, 'init_options' ) );
		add_action( 'customize_save_after', array( $this, 'init_options' ), 99 );
		add_action( 'porto_after_elementor_kit', array( $this, 'init_options' ) );
		add_action( 'porto_import_after_functions', array( $this, 'update_option' ) );

		add_action( 'porto_import_after_functions', array( $this, 'update_elementor_data' ), 99 );
		add_action( 'elementor/core/files/clear_cache', array( $this, 'update_elementor_data' ), 99 );
	}

	public function elementor_notice_js() {
		if ( 'no' == get_option( 'porto_settings_elementor', 'no' ) && $this->is_in_edit() ) {
			wp_enqueue_script( 'porto-elementor-notice', PORTO_JS . '/admin/porto-elementor-notice.js', array( 'jquery' ), PORTO_VERSION );
			wp_localize_script(
				'porto-elementor-notice',
				'portoElementorNotice',
				array(
					'nonce' => wp_create_nonce( 'porto_elementor_notice_nonce' ),
				)
			);
		}
	}

	/**
	 * Disables Elementor default styles
	 */
	public function disable_default_styles() {
		if ( ! check_ajax_referer( 'porto_elementor_notice_nonce', 'nonce' ) ) {
			die();
		}
		$option = sanitize_text_field( $_POST['option'] );
		if ( ! empty( $option ) ) {
			if ( 'yes' == $option ) {
				update_option( 'elementor_disable_color_schemes', 'yes' );
				update_option( 'elementor_disable_typography_schemes', 'yes' );
			}
			update_option( 'porto_settings_elementor', 'yes' );
		}
		die();
	}

	/**
	 * Check if we're in Elementor Edit Page
	 *
	 * @return bool
	 */
	private function is_in_edit() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Intialize Elementor style variables which are able to updated in Elementor -> Settings -> Style.
	 */
	public function init_options( $add_kit = false ) {
		global $porto_settings;
		$changed = false;
		
		$pt_el_settings = ! get_option( '_porto_elementor_settings' );

		if ( version_compare( ELEMENTOR_VERSION, '3.0' ) < 0 ) {
			$general_settings = get_option( '_elementor_general_settings', array() );
			if ( empty( $general_settings ) ) {
				$general_settings = array();
			}
			if ( isset( $porto_settings['body-font'] ) && isset( $porto_settings['body-font']['font-family'] ) && get_option( 'elementor_default_generic_fonts', '' ) != $porto_settings['body-font']['font-family'] ) {
				update_option( 'elementor_default_generic_fonts', esc_html( $porto_settings['body-font']['font-family'] ) );
				$general_settings['default_generic_fonts'] = sanitize_text_field( $porto_settings['body-font']['font-family'] );
				$changed                                   = true;
			}

			if ( ! empty( $porto_settings['container-width'] ) && get_option( 'elementor_container_width', '1140' ) != $porto_settings['container-width'] ) {
				update_option( 'elementor_container_width', (int) $porto_settings['container-width'] );
				$general_settings['container_width'] = (int) $porto_settings['container-width'];
				$changed                             = true;
			}

			if ( ! get_option( '_porto_elementor_settings', false ) && ! empty( $porto_settings['grid-gutter-width'] ) && get_option( 'elementor_space_between_widgets', '20' ) != $porto_settings['grid-gutter-width'] ) {
				update_option( 'elementor_space_between_widgets', (int) $porto_settings['grid-gutter-width'] );
				$general_settings['space_between_widgets'] = (int) $porto_settings['grid-gutter-width'];
				$changed                                   = true;
			}

			if ( 'h1.page-title' != get_option( 'elementor_page_title_selector', '' ) ) {
				update_option( 'elementor_page_title_selector', 'h1.page-title' );
				$general_settings['h1.page-title'] = 'h1.page-title';
				$changed                           = true;
			}
			if ( '992' != get_option( 'elementor_viewport_lg', '1025' ) ) {
				update_option( 'elementor_viewport_lg', '992' );
				$changed = true;
			}

			if ( $changed ) {
				update_option( '_elementor_general_settings', $general_settings );
			}
		} else {
			$default_kit = false;
			$kit         = Elementor\Plugin::$instance->kits_manager->get_active_kit();
			if ( ! $kit->get_id() && $add_kit ) {
				// Create elementor default kit
				$default_kit = Elementor\Plugin::$instance->kits_manager->create_default();
				if ( $default_kit ) {
					update_option( Elementor\Core\Kits\Manager::OPTION_ACTIVE, $default_kit );
				}
			} elseif ( $kit->get_id() ) {
				$default_kit = $kit->get_id();
			}

			if ( $default_kit ) {
				$general_settings = get_post_meta( $default_kit, '_elementor_page_settings', true );
				if ( empty( $general_settings ) ) {
					$general_settings = array();
				}
				if ( isset( $porto_settings['body-font'], $porto_settings['body-font']['font-family'] ) && ( ! isset( $general_settings['default_generic_fonts'] ) || $general_settings['default_generic_fonts'] != $porto_settings['body-font']['font-family'] ) ) {
					$general_settings['default_generic_fonts'] = sanitize_text_field( $porto_settings['body-font']['font-family'] );
					$changed                                   = true;
				}

				if ( ! empty( $porto_settings['container-width'] ) && ( empty( $general_settings['container_width'] ) || ! isset( $general_settings['container_width']['size'] ) || $general_settings['container_width']['size'] != $porto_settings['container-width'] ) ) {
					$general_settings['container_width'] = array(
						'size'  => (int) $porto_settings['container-width'],
						'unit'  => 'px',
						'sizes' => array(),
					);
					$changed                             = true;
				}

				if ( $pt_el_settings && version_compare( ELEMENTOR_VERSION, '3.16.0', '>=' ) ) {
					$general_settings['container_padding'] = array(
						'top'    => 0,
						'left'   => 0,
						'right'  => 0,
						'bottom' => 0,
						'unit'   => 'px',
					);
					$changed = true;
				}
				if ( $pt_el_settings && ! empty( $porto_settings['grid-gutter-width'] ) ) {
					
					if ( version_compare( ELEMENTOR_VERSION, '3.16.0', '>=' ) ) {
						if ( empty( $general_settings['space_between_widgets'] ) || ! isset( $general_settings['space_between_widgets']['size'] ) || $general_settings['space_between_widgets']['size'] != $porto_settings['grid-gutter-width'] || ! isset( $general_settings['space_between_widgets']['column'] ) || $general_settings['space_between_widgets']['column'] != $porto_settings['grid-gutter-width'] ) {
							$general_settings['space_between_widgets'] = array(
								'size'   => (int) $porto_settings['grid-gutter-width'],
								'row'    => (int) $porto_settings['grid-gutter-width'],
								'column' => (int) $porto_settings['grid-gutter-width'],
								'unit'   => 'px',
								'sizes'  => array(),
							);
							$changed = true;
						}
					} else {
						if ( ( empty( $general_settings['space_between_widgets'] ) || ! isset( $general_settings['space_between_widgets']['size'] ) || $general_settings['space_between_widgets']['size'] != $porto_settings['grid-gutter-width'] ) ) {
							$general_settings['space_between_widgets'] = array(
								'size'   => (int) $porto_settings['grid-gutter-width'],
								'unit'   => 'px',
								'sizes'  => array(),
							);
							$changed = true;
						}
					}
				}

				if ( ! isset( $general_settings['page_title_selector'] ) || 'h1.page-title' != $general_settings['page_title_selector'] ) {
					$general_settings['page_title_selector'] = 'h1.page-title';
					$changed                                 = true;
				}

				if ( ! isset( $general_settings['viewport_tablet'] ) || '991' != $general_settings['viewport_tablet'] || '991' != get_option( 'elementor_viewport_lg', '1025' ) ) {
					$general_settings['viewport_tablet'] = '991';
					update_option( 'elementor_viewport_lg', '991' );
					$changed = true;
				}

				if ( ! isset( $general_settings['viewport_lg'] ) || '991' != $general_settings['viewport_lg'] ) {
					$general_settings['viewport_lg'] = '991';
					$changed = true;
				}

				// system colors
				if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][0] ) || $porto_settings['skin-color'] != $general_settings['system_colors'][0]['color'] ) {
					$general_settings['system_colors'][0] = array(
						'color' => $porto_settings['skin-color'],
						'title' => esc_html__( 'Primary', 'elementor' ),
						'_id' => 'primary',
					);
					$changed = true;
				}
				if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][1] ) || $porto_settings['secondary-color'] != $general_settings['system_colors'][1]['color'] ) {
					$general_settings['system_colors'][1] = array(
						'color' => $porto_settings['secondary-color'],
						'title' => esc_html__( 'Secondary', 'elementor' ),
						'_id'   => 'secondary',
					);
					$changed = true;
				}
				if ( ! empty( $general_settings['system_colors'] ) && is_array( $general_settings['system_colors'] ) ) {
					$system_color_count = count( $general_settings['system_colors'] );
					require_once PORTO_LIB . '/lib/color-lib.php';
					$porto_color_lib = PortoColorLib::getInstance();
					$light5_color = esc_html( $porto_color_lib->lighten( $porto_settings['skin-color'], 5 ) );
					if ( isset( $general_settings['system_colors'][$system_color_count - 1]['_id'] ) && 'primary_hover' == $general_settings['system_colors'][$system_color_count - 1]['_id'] ) {
						$system_color_count -= 3;
						if ( isset ( $general_settings['system_colors'][$system_color_count]['color'] ) &&  $general_settings['system_colors'][$system_color_count]['color'] != $porto_settings['tertiary-color'] ) {
							$general_settings['system_colors'][$system_color_count] = array(
								'color' => $porto_settings['tertiary-color'],
								'title' => esc_html__( 'Tertiary', 'porto' ),
								'_id'   => 'tertiary',
							);
							$changed = true;
						}
						if ( isset ( $general_settings['system_colors'][$system_color_count + 1]['color'] ) &&  $general_settings['system_colors'][$system_color_count + 1]['color'] != $porto_settings['quaternary-color'] ) {
							$general_settings['system_colors'][$system_color_count + 1] = array(
								'color' => $porto_settings['quaternary-color'],
								'title' => esc_html__( 'Quaternary', 'porto' ),
								'_id'   => 'quaternary'
							);
							$changed = true;
						}
						if ( isset ( $general_settings['system_colors'][$system_color_count + 2]['color'] ) &&  $general_settings['system_colors'][$system_color_count + 2]['color'] != $light5_color ) {
							$general_settings['system_colors'][$system_color_count + 2] = array(
								'color' => $light5_color,
								'title' => esc_html__( 'Priamry Hover', 'porto' ),
								'_id'   => 'primary_hover'
							);
							$changed = true;
						}
					} else {
						$general_settings['system_colors'][] = array(
							'color' => $porto_settings['tertiary-color'],
							'title' => esc_html__( 'Tertiary', 'porto' ),
							'_id'   => 'tertiary',
						);
						$general_settings['system_colors'][] = array(
							'color' => $porto_settings['quaternary-color'],
							'title' => esc_html__( 'Quaternary', 'porto' ),
							'_id'   => 'quaternary'
						);
						$general_settings['system_colors'][] = array(
							'color' => $light5_color,
							'title' => esc_html__( 'Priamry Hover', 'porto' ),
							'_id'   => 'primary_hover'
						);
						$changed = true;
					}
					
				}
				
				if ( $changed ) {
					update_post_meta( $default_kit, '_elementor_page_settings', $general_settings );
				}
			}
		}

		if ( $changed ) {
			if ( $pt_el_settings ) {
				update_option( '_porto_elementor_settings', true );
			}
			try {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			} catch ( Exception $e ) {
			}
		}

	}

	/**
	 * Delete all meta field.
	 * 
	 * @since 7.5.0
	 */
	public function update_elementor_data() {
		delete_post_meta_by_key( 'porto_elementor_page_assets_saved' );
	}

	/**
	 * Hook After Demo Import
	 * 
	 * @since 7.2.0
	 */
	public function update_option() {
		if ( version_compare( ELEMENTOR_VERSION, '3.16.0', '>=' ) ) {
			$default_kit = false;
			$kit         = Elementor\Plugin::$instance->kits_manager->get_active_kit();
			if ( ! $kit->get_id() ) {
				// Create elementor default kit
				$default_kit = Elementor\Plugin::$instance->kits_manager->create_default();
				if ( $default_kit ) {
					update_option( Elementor\Core\Kits\Manager::OPTION_ACTIVE, $default_kit );
				}
			} elseif ( $kit->get_id() ) {
				$default_kit = $kit->get_id();
			}
			if ( $default_kit ) {
				$general_settings = get_post_meta( $default_kit, '_elementor_page_settings', true );
				if ( empty( $general_settings ) ) {
					$general_settings = array();
				}
				if ( is_array( $general_settings ) ) {
					$general_settings['container_padding'] = array(
						'top'    => 0,
						'left'   => 0,
						'right'  => 0,
						'bottom' => 0,
						'unit'   => 'px',
					);
					update_post_meta( $default_kit, '_elementor_page_settings', $general_settings );
				}
			}

		}	
	}
}

new Porto_Elementor_Compatibility();
