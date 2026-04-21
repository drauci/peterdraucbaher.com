<?php

/**
 * Porto Admin Class
 */
defined( 'ABSPATH' ) || exit;

class Porto_Admin {

	private $_checkedPurchaseCode;

	private $activation_url = PORTO_API_URL . 'verify_purchase.php';

	private $doc_path = array(
		'header'  => 'https://www.portotheme.com/wordpress/porto/documentation/header-builder-using-page-builders/',
		'footer'  => 'https://www.portotheme.com/wordpress/porto/documentation/templates-builder/',
		'block'   => 'https://www.portotheme.com/wordpress/porto/documentation/templates-builder/',
		'single'  => 'https://www.portotheme.com/wordpress/porto/documentation/single-builder-elements/',
		'archive' => 'https://www.portotheme.com/wordpress/porto/documentation/archive-builder/',
		'product' => 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/',
		'shop'    => 'https://www.portotheme.com/wordpress/porto/documentation/shop-builder-elements/',
		'type'    => 'https://www.portotheme.com/wordpress/porto/documentation/type-builder-elements/',
		'popup'   => 'https://www.portotheme.com/wordpress/porto/documentation/popup-builder/',
		'menu'    => 'https://www.portotheme.com/wordpress/porto/documentation/menu/',
	);

	public function __construct() {
		if ( is_admin_bar_showing() ) {
			add_action( 'wp_before_admin_bar_render', array( $this, 'add_wp_toolbar_menu' ) );
		}
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
		add_action( 'after_switch_theme', array( $this, 'reset_child_theme_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_theme_update_url' ), 1001 );

		if ( is_admin() ) {
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'pre_set_site_transient_update_themes' ) );
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins' ), 5 );
			add_filter( 'upgrader_pre_download', array( $this, 'upgrader_pre_download' ), 10, 3 );
			add_action( 'wp_ajax_porto_switch_theme_options_panel', array( $this, 'switch_options_panel' ) );
			add_action( 'in_admin_footer', array( $this, 'add_guide_video' ) );
			add_action( 'activate_elementor/elementor.php', array( $this, 'activate_elementor' ), 99 );
			add_action( 'activate_js_composer/js_composer.php', array( $this, 'activate_js_composer' ), 99 );

			if ( defined( 'WPB_VC_VERSION' ) ) {
				add_action( 'init', array( $this, 'disable_vc_updater' ), 20 );
			}
		}
	}

	/**
	 * After activate Elementor
	 * 
	 * @since 7.1.0
	 */
	public function activate_elementor() {
		$post_types = get_option( 'elementor_cpt_support' );
		if ( empty( $post_types ) ) {
			$post_types = array();
		}
		$post_types[] = 'porto_builder';
		if ( ! in_array( 'page', $post_types ) ) {
			$post_types[] = 'page';
		}
		update_option ( 'elementor_cpt_support', $post_types );
	}

	/**
	 * After activate Elementor
	 * 
	 * @since 7.1.0
	 */
	public function activate_js_composer() {
		// Enable WPBakery Edior for Porto Template Builder
		if ( function_exists( 'vc_role_access' ) ) {
			require_once vc_path_dir( 'SETTINGS_DIR', 'class-vc-roles.php' );
			$vc_roles = new Vc_Roles();
			if ( 'custom' == vc_role_access()->who( 'administrator' )->part( 'post_types' )->getState() ) {
				$vc_roles->save( array(
					'administrator' => array(
						'post_types' => array(
							'_state'        => 'custom',
							'porto_builder' => '1',
						)
					)
				) );
			} else {
				$vc_roles->save( array(
					'administrator' => array(
						'post_types' => array(
							'_state'        => 'custom',
							'page'          => '1',
							'porto_builder' => '1',
						)
					)
				) );
			}

			if ( 'custom' == vc_role_access()->who( 'editor' )->part( 'post_types' )->getState() ) {
				$vc_roles->save( array(
					'editor' => array(
						'post_types' => array(
							'_state'        => 'custom',
							'porto_builder' => '1',
						)
					)
				) );
			} else {
				$vc_roles->save( array(
					'editor' => array(
						'post_types' => array(
							'_state'        => 'custom',
							'page'          => '1',
							'porto_builder' => '1',
						)
					)
				) );
			}
		}

		
	}

	/**
	 * Reset the child theme options
	 * 
	 * @since 7.1.0
	 */
	public function reset_child_theme_options() {
		if ( is_child_theme() && empty( get_theme_mod( 'builder_conditions', array() ) ) ) {
			update_option( 'theme_mods_' . get_stylesheet(), get_option( 'theme_mods_' . get_template() ) );
		}
	}

	/**
	 * View the footer
	 * 
	 * @since 6.10.0
	 */
	public function view_footer() {
		?>
		<div class="wrap porto-wrap porto-admin-footer">
			<div class="porto-col">
				<a target="_blank" href="https://www.portotheme.com/"><img src="<?php echo PORTO_URI; ?>/images/pthemes.png" alt="P-THEMES logo" /></a>
			</div>
			<div class="porto-col">
				<ul>
					<li>
						<a target="_blank" href="https://www.portotheme.com/wordpress/porto/documentation/"><?php esc_html_e( 'Documentation', 'porto' ); ?></a>
					</li>
					<li>
						<a target="_blank" href="https://www.portotheme.com/forums/forum/porto-multi-purpose-woocommerce-theme/"><?php esc_html_e( 'Support Forum', 'porto' ); ?></a>
					</li>
					<li>
						<a target="_blank" href="https://themeforest.net/downloads/"><?php esc_html_e( 'Rate our theme', 'porto' ); ?></a>
					</li>										
					<li>
						<a target="_blank" href="https://www.portotheme.com/wordpress/porto/documentation/changelog/"><?php esc_html_e( 'Changelog', 'porto' ); ?></a>
					</li>						
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Add video to Porto templates and Appearance/Menu
	 *
	 * @since 6.6.0
	 */
	public function add_guide_video() {
		global $pagenow;
		$path = '';
		$title = '';
		$bg_color = ' bg-white';
		if ( isset( $pagenow ) ) {
			if ( 'nav-menus.php' == $pagenow ) {
				$path = 'menu';
				$title = esc_html__( 'the Menu', 'porto' );
			} elseif ( class_exists( 'PortoBuilders' ) && ( ! empty( $_REQUEST['post_type'] ) && PortoBuilders::BUILDER_SLUG == $_REQUEST['post_type'] ) ) {
				if ( ! empty( $_REQUEST[ PortoBuilders::BUILDER_TAXONOMY_SLUG ] ) ) {
					$path = sanitize_text_field( $_REQUEST[ PortoBuilders::BUILDER_TAXONOMY_SLUG ] );
					$title = sprintf( __('the %s Builder', 'porto'), ucwords( $_REQUEST[ PortoBuilders::BUILDER_TAXONOMY_SLUG ] ) );
				} elseif ( empty( $_REQUEST['post_status'] ) || ( 'publish' == $_REQUEST['post_status'] ) ) {
					$path = 'block';
					$title = esc_html__( 'the Block Builder', 'porto' );
				}
			}
		}

		if ( empty( $path ) ) {
			return;
		}
		ob_start();
		?>
			<div class="guide-video<?php echo porto_filter_output( $bg_color ); ?>">
				<style>
					#wpfooter { position: static; opacity: 1 !important; } 
					#wpbody > .clear, #wpbody ~ .clear { clear: unset; }
					.porto-builder-video { display: block; margin: 44px auto 0; border-radius: 4px; box-shadow: 0 0 5px 3px rgb(99 99 99 / 20%); } 
					.guide-title { font-size: 26px; } 
					#wpfooter .guide-description { font-size: 15px; color: #666; }
					#wpbody-content { padding-bottom: 12px; }
					.guide-video { display: inline-block; width: calc( 100% - 4px ); margin: 0 0 9px 4px; padding: 53px 0 50px; text-align: center; }
					.guide-video.bg-white { background: #fff; }
				</style>
				<h2 class="guide-title"><?php printf( esc_html__( 'How to use %s', 'porto' ), $title ); ?></h2>
				<p class="guide-description">
				<?php
				if ( 'menu' == $path ) {
					printf( esc_html__( 'In this video, we look at creating, assigning and designing menus in %1$sPorto%2$s. This video shows you how to create megamenu too. %3$s To know in full, read this %4$s article%2$s.', 'porto' ), '<a href="https://www.portotheme.com/wordpress/porto_landing/" target="_blank">', '</a>', '<br>', '<a href="' . $this->doc_path[ $path ] . '" target="_blank">' );
				} elseif ( 'critical' == $path ) {
					printf( esc_html__( 'In this video, we look at how to merge %3$sJs/Style%4$s and generate %3$sCritical CSS%4$s in %1$sPorto%2$s.', 'porto' ), '<a href="https://www.portotheme.com/wordpress/porto_landing/" target="_blank">', '</a>', '<b>', '</b>' );
				} elseif ( 'shortcode' == $path ) {
					printf( esc_html__( 'In this video, we look at how to optimize unused %3$s Shortcode Styles%4$s in %1$sPorto%2$s.', 'porto' ), '<a href="https://www.portotheme.com/wordpress/porto_landing/" target="_blank">', '</a>', '<b>', '</b>' );
					echo '';
				} else {
					printf( esc_html__( 'This video looks at how to create %1$s %2$s builder%3$s and how to use in %4$sPorto%3$s.', 'porto' ), '<a href="' . $this->doc_path[ $path ] . '" target="_blank">', $path, '</a>', '<a href="https://www.portotheme.com/wordpress/porto_landing/" target="_blank">' );
				}
				?>
				</p>
				<?php
				if ( ! in_array( $path, array( 'menu', 'critical','shortcode' ) ) ) {
					if ( 'type' != $path ) {
						if ( defined( 'ELEMENTOR_VERSION' ) ) {
							$path = 'elementor/' . $path;
						} else {
							$path = 'wpb/' . $path;
						}
					}
					$path = 'builder/' . $path;
				}
					$path = 'https://sw-themes.com/porto_dummy/wp-content/uploads/videos/' . $path . '.mp4';
				?>
				<video class="porto-builder-video" preload="none" controls="controls" width="800" height="450" poster="<?php echo PORTO_URI; ?>/images/preview-video.jpg"><source type="video/mp4" src="<?php echo esc_attr( $path ); ?>" /></video>
			</div>
		<?php
		echo ob_get_clean();
	}

	public function switch_options_panel() {
		if ( current_user_can( 'edit_theme_options' ) ) {
			if ( isset( $_POST['type'] ) && 'redux' == $_POST['type'] ) {
				set_theme_mod( 'theme_options_use_new_style', false );
			} else {
				set_theme_mod( 'theme_options_use_new_style', true );
			}
		}
	}

	public function add_wp_toolbar_menu() {
		global $porto_settings;
		if ( current_user_can( 'edit_theme_options' ) ) {
			$porto_parent_menu_title = '<span class="ab-icon"></span><span class="ab-label">Porto</span>';
			$this->add_wp_toolbar_menu_item( $porto_parent_menu_title, false, admin_url( 'admin.php?page=porto' ), array( 'class' => 'porto-menu' ), 'porto' );
			$this->add_wp_toolbar_menu_item( __( 'Dashboard', 'porto' ), 'porto', admin_url( 'admin.php?page=porto' ), array(), 'porto-dashboard' );
			
			if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
				$this->add_wp_toolbar_menu_item( __( 'Theme Options', 'porto' ), 'porto', admin_url( 'customize.php' ) );
				$this->add_wp_toolbar_menu_item( __( 'Advanced Options', 'porto' ), 'porto', admin_url( 'admin.php?page=porto_settings' ) );
			} else {
				$this->add_wp_toolbar_menu_item( __( 'Theme Options', 'porto' ), 'porto', admin_url( 'admin.php?page=porto_settings' ) );
			}

			// add wizard menus
			$this->add_wp_toolbar_menu_item( __( 'Prebuilt Websites', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-setup-wizard&step=demo_content' ) );
			$this->add_wp_toolbar_menu_item( __( 'Setup Wizard', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-setup-wizard' ) );
			$this->add_wp_toolbar_menu_item( __( 'Speed Optimize Wizard', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-speed-optimize-wizard' ) );
			if ( $this->is_registered() ) {
				$this->add_wp_toolbar_menu_item( __( 'Version Control', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-version-control' ) );
			}
			$this->add_wp_toolbar_menu_item( __( 'Tools', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-tools' ) );
			$this->add_wp_toolbar_menu_item( __( 'Sidebars', 'porto' ), 'porto', admin_url( 'themes.php?page=multiple_sidebars' ) );

			$this->add_wp_toolbar_menu_item( __( 'Page Layouts (Layout Builder)', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-page-layouts' ) );
			if ( post_type_exists( 'porto_builder' ) ) {
				$this->add_wp_toolbar_menu_item( __( 'Templates Builder', 'porto' ), 'porto', admin_url( 'edit.php?post_type=porto_builder' ) );
			}
			if ( class_exists( 'Porto_Patcher' ) && $this->is_registered() ) {
				$this->add_wp_toolbar_menu_item( __( 'Patcher', 'porto' ), 'porto', admin_url( 'admin.php?page=porto-patcher' ) );
			}

			if ( empty( $porto_settings['disable-builder-tooltip'] ) ) {
				$this->add_wp_toolbar_menu_item( __( 'Development Mode', 'porto' ), 'top-secondary', porto_get_theme_option_url( 'disable-builder-tooltip' ), array( 'class' => 'porto-dev-bar', 'target' => '_blank' ), 'porto-dev-bar' );
			}
		}
	}

	public function add_wp_toolbar_menu_item( $title, $parent = false, $href = '', $custom_meta = array(), $custom_id = '' ) {
		global $wp_admin_bar;
		if ( current_user_can( 'edit_theme_options' ) ) {
			if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
				return;
			}
			// Set custom ID
			if ( $custom_id ) {
				$id = $custom_id;
			} else { // Generate ID based on $title
				$id = strtolower( str_replace( ' ', '-', $title ) );
			}
			// links from the current host will open in the current window
			$meta = strpos( $href, site_url() ) !== false ? array() : array( 'target' => '_blank' ); // external links open in new tab/window

			$meta = array_merge( $meta, $custom_meta );
			$wp_admin_bar->add_node(
				array(
					'parent' => $parent,
					'id'     => $id,
					'title'  => $title,
					'href'   => $href,
					'meta'   => $meta,
				)
			);
		}
	}

	public function admin_menu() {
		if ( is_admin() && current_user_can( 'edit_theme_options' ) ) {
			$title =  __( 'Porto', 'porto' );
			if ( Porto()->is_registered() && class_exists( 'Porto_Patcher' ) && Porto_Patcher::get_instance()->check_patches() ) {
				$title = sprintf( __( 'Porto %sNew%s', 'porto' ), '<span class="update-plugins">', '</span>' );
			}
			$welcome_screen = add_menu_page( 'Porto', $title, 'administrator', 'porto', array( $this, 'welcome_screen' ), 'dashicons-porto-logo', 54 );
			$welcome        = add_submenu_page( 'porto', __( 'Dashboard', 'porto' ), __( 'Dashboard', 'porto' ), 'administrator', 'porto', array( $this, 'welcome_screen' ) );

			if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
				add_submenu_page( 'admin.php?page=porto_settings', __( 'Theme Options', 'porto' ), __( 'Theme Options', 'porto' ), 'administrator', 'customize.php' );
				add_submenu_page( 'admin.php?page=porto_settings', __( 'Advanced Options', 'porto' ), __( 'Advanced Options', 'porto' ), 'administrator', 'porto_settings' );

				add_submenu_page( 'porto', __( 'Theme Options', 'porto' ), __( 'Theme Options', 'porto' ), 'administrator', 'customize.php' );
				add_submenu_page( 'porto', __( 'Advanced Options', 'porto' ), __( 'Advanced Options', 'porto' ), 'administrator', 'admin.php?page=porto_settings' );
			} else {
				add_submenu_page( 'porto', __( 'Theme Options', 'porto' ), __( 'Theme Options', 'porto' ), 'administrator', 'admin.php?page=porto_settings' );
			}
		}
	}

	public function welcome_screen() {
		require_once PORTO_ADMIN . '/admin_pages/welcome.php';
		Porto()->view_footer();
	}

	public function let_to_num( $size ) {
		$l   = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );
		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}

	public function check_purchase_code() {

		if ( ! $this->_checkedPurchaseCode ) {
			$code         = isset( $_POST['code'] ) ? sanitize_text_field( $_POST['code'] ) : '';
			$code_confirm = $this->get_purchase_code();

			if ( isset( $_POST['action'] ) && ! empty( $_POST['action'] ) ) {
				if ( ! $code || $code != $code_confirm ) {
					if ( $code_confirm ) {
						$result = $this->curl_purchase_code( $code_confirm, 'remove' );
					}
					if ( 'unregister' === $_POST['action'] && $result && isset( $result['result'] ) && 3 === (int) $result['result'] ) {
						$this->_checkedPurchaseCode = 'unregister';
						$this->set_purchase_code( '' );
						delete_transient( 'porto_purchase_code_error_msg' );
						if ( isset( $_COOKIE['porto_dismiss_code_error_msg'] ) ) {
							setcookie( 'porto_dismiss_code_error_msg', '', time() - 3600 );
						}
						return $this->_checkedPurchaseCode;
					}
				}
				if ( $code ) {
					$result = $this->curl_purchase_code( $code, 'add' );
					if ( ! $result ) {
						$this->_checkedPurchaseCode = 'invalid';
						$code_confirm               = '';
					} elseif ( isset( $result['result'] ) && 1 === (int) $result['result'] ) {
						$code_confirm               = $code;
						$this->_checkedPurchaseCode = 'verified';
					} else {
						$this->_checkedPurchaseCode = $result['message'];
						$code_confirm               = '';
					}
				} else {
					$code_confirm               = '';
					$this->_checkedPurchaseCode = '';
				}
				$this->set_purchase_code( $code_confirm );
			} else {
				if ( $code && $code_confirm && $code == $code_confirm ) {
					$this->_checkedPurchaseCode = 'verified';
				}
			}
		}
		return $this->_checkedPurchaseCode;
	}

	public function curl_purchase_code( $code, $act ) {
		require_once PORTO_PLUGINS . '/importer/importer-api.php';
		$importer_api = new Porto_Importer_API();

		if ( 'add' == $act || 'remove' == $act ) {
			$site_url  = base64_encode( home_url() );
			$version   = PORTO_VERSION;
			$more_args = "&site_url=$site_url&version=$version";
		}
		$result = $importer_api->get_response( $this->activation_url . "?item=9207399&code=$code&act=$act" . ( isset( $more_args ) ? $more_args : '' ) );

		if ( ! $result ) {
			return false;
		}
		if ( is_wp_error( $result ) ) {
			return array( 'message' => $result->get_error_message() );
		}
		return $result;
	}

	public function get_purchase_code() {
		if ( $this->is_envato_hosted() ) {
			return SUBSCRIPTION_CODE;
		}
		return get_option( 'envato_purchase_code_9207399' );
	}

	public function is_registered() {
		if ( $this->is_envato_hosted() ) {
			return true;
		}
		return get_option( 'porto_registered' );
	}

	public function set_purchase_code( $code ) {
		update_option( 'envato_purchase_code_9207399', $code );
	}

	public function is_envato_hosted() {
		return defined( 'ENVATO_HOSTED_KEY' ) ? true : false;
	}

	public function get_ish() {
		if ( ! defined( 'ENVATO_HOSTED_KEY' ) ) {
			return false;
		}
		return substr( ENVATO_HOSTED_KEY, 0, 16 );
	}

	function get_purchase_code_asterisk() {
		$code = $this->get_purchase_code();
		if ( $code ) {
			$code = substr( $code, 0, 13 );
			$code = $code . '-****-****-************';
		}
		return $code;
	}

	public function pre_set_site_transient_update_themes( $transient ) {
		if ( ! $this->is_registered() ) {
			return $transient;
		}
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		require_once PORTO_PLUGINS . '/importer/importer-api.php';
		$importer_api   = new Porto_Importer_API();
		$new_version    = $importer_api->get_latest_theme_version();
		$theme_template = get_template();
		if ( version_compare( wp_get_theme( $theme_template )->get( 'Version' ), $new_version, '<' ) ) {

			$args = $importer_api->generate_args( false );
			if ( $this->is_envato_hosted() ) {
				$args['ish'] = $this->get_ish();
			}

			$transient->response[ $theme_template ] = array(
				'theme'       => $theme_template,
				'new_version' => $new_version,
				'url'         => $importer_api->get_url( 'changelog' ),
				'package'     => add_query_arg( $args, $importer_api->get_url( 'theme' ) ),
			);

		}
		return $transient;
	}

	/**
	 * Update plugins from Porto repository
	 * 
	 * @since 7.2.6
	 */
	public function pre_set_site_transient_update_plugins( $transient ) {
		if ( ! $this->is_registered() || empty( $transient->checked ) ) {
			return $transient;
		}

		$plugins = get_site_transient( 'porto_plugins' );
		if ( is_array( $plugins ) && ! empty( $transient->response ) && function_exists( 'get_plugin_data' ) ) {
			$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
			foreach ( $plugins as $plugin ) {
				if ( $instance && isset( $plugin['version'], $plugin['url'], $plugin['source'] ) && $instance->is_plugin_installed( $plugin['slug'] ) ) {
					$plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin['url'] );
					if ( $plugin_info && isset( $plugin_info['Version'] ) && version_compare( $plugin_info['Version'], $plugin['version'], '<' ) ) {
						$obj = new stdClass();
						$obj->slug = $plugin['slug'];
						$obj->new_version = $plugin['version'];
						$obj->plugin = $plugin['url'];
						$obj->url = '';
						$obj->package = $plugin['source'];
						$obj->name = $plugin['name'];
						$transient->response[ $plugin['url'] ] = $obj;
					}
				}
			}
		}

		return $transient;
	}

	/**
	 * Disable WPBakery auto updater
	 * 
	 * @since 7.2.6
	 */
	public function disable_vc_updater() {
		if ( has_filter( 'upgrader_pre_download', array( vc_updater(), 'preUpgradeFilter' ), 10, 4 ) ) {
			remove_filter( 'upgrader_pre_download', array(
				vc_updater(),
				'preUpgradeFilter',
			), 10, 4 );
		}
	}

	public function upgrader_pre_download( $reply, $package, $obj ) {
		require_once PORTO_PLUGINS . '/importer/importer-api.php';
		$importer_api = new Porto_Importer_API();
		if ( strpos( $package, $importer_api->get_url( 'theme' ) ) !== false || strpos( $package, $importer_api->get_url( 'plugins' ) ) !== false ) {
			if ( ! $this->is_registered() ) {
				return new WP_Error( 'not_registerd', __( 'Please <a href="admin.php?page=porto">register</a> Porto theme to get access to pre-built demo websites and auto updates.', 'porto' ) );
			}
			$code   = $this->get_purchase_code();
			$result = $this->curl_purchase_code( $code, 'add' );
			if ( ! isset( $result['result'] ) || 1 !== (int) $result['result'] ) {
				$message = isset( $result['message'] ) ? $result['message'] : __( 'Purchase Code is not valid or could not connect to the API server! Please try again later.', 'porto' );
				return new WP_Error(
					'purchase_code_invalid',
					wp_kses(
						$message,
						array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
							),
						)
					)
				);
			}
		}
		return $reply;
	}

	public function add_theme_update_url() {
		global $pagenow;
		if ( 'update-core.php' == $pagenow ) {
			require_once PORTO_PLUGINS . '/importer/importer-api.php';
			$importer_api   = new Porto_Importer_API();
			$new_version    = $importer_api->get_latest_theme_version();
			$theme_template = get_template();
			$url            = $importer_api->get_url( 'changelog' );

			if ( version_compare( PORTO_VERSION, $new_version, '<' ) ) {
				$checkbox_id = md5( wp_get_theme( $theme_template )->get( 'Name' ) );
				wp_add_inline_script( 'porto-admin', 'if (jQuery(\'#checkbox_' . $checkbox_id . '\').length) {jQuery(\'#checkbox_' . $checkbox_id . '\').closest(\'tr\').children().last().append(\'<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_js( __( 'View Details', 'porto' ) ) . '</a>\');}' );
			}

			$checkbox_id = md5( 'porto-functionality/porto-functionality.php' );
			wp_add_inline_script( 'porto-admin', 'if (jQuery(\'#checkbox_' . $checkbox_id . '\').length) {jQuery(\'#checkbox_' . $checkbox_id . '\').closest(\'tr\').find(".open-plugin-details-modal").replaceWith(\'<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_js( __( 'View Details', 'porto' ) ) . '</a>\');}' );
		}
	}

	public function after_switch_theme() {
		if ( $this->is_registered() ) {
			$this->refresh_transients();
		}
	}

	public function refresh_transients() {
		delete_site_transient( 'porto_plugins' );
		delete_site_transient( 'update_themes' );
		unset( $_COOKIE['porto_dismiss_activate_msg'] );
		setcookie( 'porto_dismiss_activate_msg', '', -1, '/' );
		delete_transient( 'porto_purchase_code_error_msg' );
		setcookie( 'porto_dismiss_code_error_msg', '', time() - 3600 );
	}
}

$GLOBALS['porto_admin'] = new Porto_Admin();
function Porto() {
	global $porto_admin;
	if ( ! $porto_admin ) {
		$porto_admin = new Porto_Admin();
	}
	return $porto_admin;
}

if ( is_customize_preview() ) {
	require PORTO_ADMIN . '/customizer/customizer.php';

	if ( apply_filters( 'porto_legacy_mode', true ) ) {
		require PORTO_ADMIN . '/customizer/header-builder.php';
	}

	if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
		require PORTO_ADMIN . '/customizer/selective-refresh.php';
		require PORTO_ADMIN . '/customizer/customizer-reset.php';
	}
}

add_action( 'admin_init', 'porto_compile_css_on_activation' );
function porto_compile_css_on_activation() {
	$bootstrap_css = get_option( 'porto_bootstrap_style' );
	$bootstrap_rtl = get_option( 'porto_bootstrap_rtl_style' );
	$dynamic_style = ! get_option( 'porto_dynamic_style' ) && ( false === get_transient( 'porto_dynamic_style_time' ) );
	if ( ! $bootstrap_css || ! $bootstrap_rtl || $dynamic_style ) {
		require_once( PORTO_ADMIN . '/theme_options/settings.php' );
		require_once( PORTO_ADMIN . '/theme_options/save_settings.php' );
	}
	if ( ! $bootstrap_css ) {
		porto_compile_css( 'bootstrap' );
	}
	if ( ! $bootstrap_rtl ) {
		porto_compile_css( 'bootstrap_rtl' );
	}
	if ( $dynamic_style ) {
		porto_save_theme_settings();
	}
}

if ( is_admin() && ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) && ! porto_is_elementor_preview() ) {
	add_action(
		'admin_init',
		function() {
			if ( isset( $_POST['porto_registration'] ) && check_admin_referer( 'porto-setup' ) ) {
				update_option( 'porto_register_error_msg', '' );
				$result = Porto()->check_purchase_code();
				if ( 'verified' === $result ) {
					update_option( 'porto_registered', true );
					Porto()->refresh_transients();
				} elseif ( 'unregister' === $result ) {
					update_option( 'porto_registered', false );
					Porto()->refresh_transients();
				} elseif ( 'invalid' === $result ) {
					update_option( 'porto_registered', false );
					update_option( 'porto_register_error_msg', __( 'Sorry, it could not connect to the Porto API server. Please try again later.', 'porto' ) );
				} else {
					update_option( 'porto_registered', false );
					update_option( 'porto_register_error_msg', $result );
				}
			}
		}
	);

	add_action(
		'admin_init',
		function() {
			if ( ! Porto()->is_registered() && ( ( 'admin.php' == $GLOBALS['pagenow'] && isset( $_GET['page'] ) && 'porto_settings' == $_GET['page'] ) || empty( $_COOKIE['porto_dismiss_activate_msg'] ) || version_compare( $_COOKIE['porto_dismiss_activate_msg'], PORTO_VERSION, '<' ) ) ) {
				add_action(
					'admin_notices',
					function() {
						?>
				<div class="notice notice-error" style="position: relative;">
					<p><?php printf( esc_html__( 'Please %1$sregister%2$s Porto theme to get access to pre-built demo websites and auto updates.', 'porto' ), '<a href="admin.php?page=porto">', '</a>' ); ?></p>
					<p><?php printf( esc_html__( '%1$sImportant!%2$s One %3$sstandard license%4$s is valid for only %1$s1 website%2$s. Running multiple websites on a single license is a copyright violation.', 'porto' ), '<strong>', '</strong>', '<a target="_blank" href="https://themeforest.net/licenses/standard" rel="noopener noreferrer">', '</a>' ); ?></p>
					<button type="button" class="notice-dismiss porto-notice-dismiss"><span class="screen-reader-text"><?php esc_html__( 'Dismiss this notice.', 'porto' ); ?></span></button>
				</div>
				<script>
					(function($) {
						var setCookie = function (name, value, exdays) {
							var exdate = new Date();
							exdate.setDate(exdate.getDate() + exdays);
							var val = encodeURIComponent(value) + ((null === exdays) ? "" : "; expires=" + exdate.toUTCString());
							document.cookie = name + "=" + val;
						};
						$(document).on('click.porto-notice-dismiss', '.porto-notice-dismiss', function(e) {
							e.preventDefault();
							var $el = $(this).closest('.notice');
							$el.fadeTo( 100, 0, function() {
								$el.slideUp( 100, function() {
									$el.remove();
								});
							});
							setCookie('porto_dismiss_activate_msg', '<?php echo PORTO_VERSION; ?>', 30);
						});
					})(window.jQuery);
				</script>
						<?php
					}
				);
			} elseif ( ! Porto()->is_registered() && 'themes.php' == $GLOBALS['pagenow'] ) {
				add_action(
					'admin_footer',
					function() {
						?>
				<script>
					(function($){
						$(window).on('load', function() {
							$('.themes .theme.active .theme-screenshot').after('<div class="notice update-message notice-error notice-alt"><p>Please <a href="admin.php?page=porto" class="button-link">verify purchase</a> to get updates!</p></div>');
						});
					})(window.jQuery);
				</script>
						<?php
					}
				);
			}

		}
	);

	remove_action( 'vc_activation_hook', 'vc_page_welcome_set_redirect' );
	remove_action( 'admin_init', 'vc_page_welcome_redirect' );

	// Add Advanced Options
	if ( ! is_customize_preview() ) {
		// if ( defined( 'ELEMENTOR_VERSION' ) || defined( 'WPB_VC_VERSION' ) || empty( $porto_settings['enable-gfse'] ) ) {
			// Gutenberg Full Site Editing
		require_once PORTO_ADMIN . '/admin_pages/class-page-layouts.php';
		// }
		require PORTO_ADMIN . '/setup_wizard/setup_wizard.php';
		require PORTO_ADMIN . '/setup_wizard/speed_optimize_wizard.php';
		require_once PORTO_ADMIN . '/admin_pages/class-tools.php';
		require_once PORTO_ADMIN . '/admin_pages/class-version-control.php';
	}
}