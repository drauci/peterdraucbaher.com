<?php
/**
 * Porto Settings Options
 */
if ( ! class_exists( 'Redux_Framework_porto_settings' ) ) {
	class Redux_Framework_porto_settings {
		public $args     = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;
		private $css_var_selectors = array();
		/**
		 * Porto Soft Mode
		 *
		 * @var bool
		 * @since 6.3.0
		 */
		public $legacy_mode;
		public function __construct() {
			// Create the sections and fields
			$this->legacy_mode = apply_filters( 'porto_legacy_mode', true );
			if ( did_action( 'init' ) ) {
				$this->init_redux();
			} else {
				add_action( 'init', array( $this, 'init_redux' ), 2 );
			}
		}

		public function init_redux() {
			$this->setSections();
			if ( ! class_exists( 'ReduxFramework' ) ) {
				return;
			}
			$this->initSettings();
		}

		public function initSettings() {
			$this->theme = wp_get_theme();
			// Set the default arguments
			$this->setArguments();
			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();
			if ( ! isset( $this->args['opt_name'] ) ) {
				// No errors please
				return;
			}
			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
		}
		function compiler_action( $options, $css, $changed_values ) {
		}
		function dynamic_section( $sections ) {
			return $sections;
		}
		function change_arguments( $args ) {
			return $args;
		}
		function change_defaults( $defaults ) {
			return $defaults;
		}
		function remove_demo() {
		}

		private function add_customizer_field( array $sections, $options_style, $first_options = false, $second_options = false ) {
			if ( $options_style ) {
				return array_merge( $sections, array( 'customizer_only' => true ) );
			}
			if ( $second_options ) {
				$sections['fields'] = array_merge( $second_options['fields'], isset( $sections['fields'] ) ? $sections['fields'] : array() );
			}
			if ( $first_options ) {
				$sections['fields'] = array_merge( $first_options['fields'], isset( $sections['fields'] ) ? $sections['fields'] : array() );
			}
			return $sections;
		}

		/**
		 * Get Unlimited Post Type
		 *
		 * @since 6.4.0
		 * @access public
		 */
		public function get_post_ptu() {
			if ( class_exists( 'Post_Types_Unlimited' ) ) {
				$custom_types = get_posts(
					array(
						'numberposts'      => -1,
						'post_type'        => 'ptu',
						'post_status'      => 'publish',
						'suppress_filters' => false,
						'fields'           => 'ids',
					)
				);
				$post_types   = array();
				// If we have custom post types, lets try and register them
				if ( $custom_types ) {
					// Loop through all custom post types and register them
					foreach ( $custom_types as $type_id ) {

						// Get custom post type meta
						$meta = get_post_meta( $type_id, '', false );

						// Check custom post type name
						$name = array_key_exists( '_ptu_name', $meta ) ? $meta['_ptu_name'][0] : '';

						// Custom post type name is required
						if ( ! $name ) {
							continue;
						}
						$post_types[] = $name;
					}
				}
				return $post_types;
			}
			return array();
		}

		/**
		 * Get ACF Post Type
		 *
		 * @since 7.6.0
		 * @access public
		 */
		public function get_post_acf() {
			if ( class_exists( 'ACF' ) ) {
				$custom_types = get_posts(
					array(
						'numberposts'      => -1,
						'post_type'        => 'acf-post-type',
						'post_status'      => 'publish',
						'suppress_filters' => false,
						'fields'           => 'ids',
					)
				);
				$post_types   = array();
				// If we have custom post types, lets try and register them
				if ( $custom_types ) {
					// Loop through all custom post types and register them
					foreach ( $custom_types as $type_id ) {
						$acf_post = get_post( $type_id );
						$name     = '';
						if ( ! empty( $acf_post ) && ! empty( $acf_post->post_content ) ) {
							$name = maybe_unserialize( $acf_post->post_content )['post_type'];
						}
						// Custom post type name is required
						if ( ! $name ) {
							continue;
						}
						$post_types[] = $name;
					}
				}
				return $post_types;
			}
			return array();
		}

		public function setSections() {
			$page_layouts              = porto_options_layouts();
			$sidebars                  = porto_options_sidebars();
			$both_sidebars             = porto_options_both_sidebars();
			$body_wrapper              = porto_options_body_wrapper();
			$banner_wrapper            = porto_options_banner_wrapper();
			$wrapper                   = porto_options_wrapper();
			$porto_banner_pos          = porto_ct_banner_pos();
			$porto_footer_view         = porto_ct_footer_view();
			$porto_banner_type         = porto_ct_banner_type();
			$porto_master_sliders      = porto_ct_master_sliders();
			$porto_rev_sliders         = porto_ct_rev_sliders();
			$porto_categories_orderby  = porto_ct_categories_orderby();
			$porto_categories_order    = porto_ct_categories_order();
			$porto_categories_sort_pos = porto_ct_categories_sort_pos();
			$porto_header_type         = porto_options_header_types();
			$porto_footer_type         = porto_options_footer_types();
			$porto_breadcrumbs_type    = porto_options_breadcrumbs_types();
			$porto_footer_columns      = porto_options_footer_columns();

			global $porto_settings_optimize;
			$block_url = $archive_url = $single_url = $type_url = $header_url = $footer_url = $shop_url = $product_url = $popup_url = admin_url( 'admin.php?page=porto-speed-optimize-wizard&step=general' );

			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'block', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$block_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=block' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'archive', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$archive_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=archive' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'single', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$single_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=single' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'type', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$type_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=type' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'header', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$header_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=header' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'footer', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$footer_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=footer' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'shop', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$shop_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=shop' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'product', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$product_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=product' );
			}
			$popup_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=popup' );

			if ( current_user_can( 'manage_options' ) && is_admin() ) {
				$product_layouts = porto_get_post_type_items(
					'porto_builder',
					array(
						'meta_query' => array(
							array(
								'key'   => 'porto_builder_type',
								'value' => 'product',
							),
						),
					),
					true
				);
			} else {
				$product_layouts = array();
			}

			$options_style = get_theme_mod( 'theme_options_use_new_style', false );

			/* default values for old versions */
			if ( ! get_theme_mod( 'theme_options_saved', false ) ) {
				$porto_settings = get_option( 'porto_settings' );
			}
			$search_layout_default = 'simple';
			$minicart_type         = 'simple';
			if ( isset( $porto_settings ) && ! empty( $porto_settings ) ) {
				if ( ! isset( $porto_settings['search-layout'] ) ) {
					if ( in_array( $porto_settings['header-type'], array( '2', '3', '7', '8', '18', '19' ) ) ) {
						$search_layout_default = 'large';
					} else {
						$search_layout_default = 'advanced';
					}
				}

				if ( isset( $porto_settings['show-minicart'] ) && ! $porto_settings['show-minicart'] ) {
					$minicart_type = 'none';
				} elseif ( ! isset( $porto_settings['minicart-type'] ) ) {
					$header_type = isset( $porto_settings['header-type'] ) ? (int) $porto_settings['header-type'] : '';
					if ( ( $header_type >= 1 && $header_type <= 9 ) || 18 == $header_type || 19 == $header_type || ( isset( $porto_settings['header-type-select'] ) && 'header_builder' == $porto_settings['header-type-select'] ) ) {
						$minicart_type = 'minicart-arrow-alt';
					} else {
						$minicart_type = 'minicart-inline';
					}
				}
			}
			$mainmenu_popup_top_border = array(
				'border-color' => isset( $porto_settings ) && isset( $porto_settings['mainmenu-popup-border-color'] ) && $porto_settings['mainmenu-popup-border-color'] ? $porto_settings['mainmenu-popup-border-color'] : 'var(--porto-primary-color)',
				'border-top'   => isset( $porto_settings ) && isset( $porto_settings['mainmenu-popup-border'] ) && ! $porto_settings['mainmenu-popup-border'] ? '' : '3px',
			);
			/* end */

			// global $wp_version;
			// if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) && version_compare( $wp_version, '6.0', '>=' ) ) {
			// 	$gutenberg_site_option = array(
			// 		'id'      => 'enable-gfse',
			// 		'type'    => 'switch',
			// 		'title'   => __( 'Gutenberg Full Site Editing', 'porto' ),
			// 		'desc'    => __( 'Make this option enable, Porto Template Builders won\'t be available.', 'porto' ),
			// 		'default' => false,
			// 		'on'      => __( 'Yes', 'porto' ),
			// 		'off'     => __( 'No', 'porto' ),
			// 	);
			// }
			// General Settings
			$general_site_options = array();
			// if ( isset( $gutenberg_site_option ) ) {
			// 	$general_site_options[] = $gutenberg_site_option;
			// }
			$general_site_options = array_merge( 
				$general_site_options, 
				array(
					array(
						'id'         => 'show-loading-overlay',
						'type'       => 'switch',
						'title'      => __( 'Loading Overlay', 'porto' ),
						'subtitle'   => __( 'Loading overlay is shown until whole page is loaded.', 'porto' ),
						'default'    => false,
						'on'         => __( 'Show', 'porto' ),
						'off'        => __( 'Hide', 'porto' ),
						'hint'       => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-loading-overlay.gif"/>' ),
						),
						// 'customizer' => false,
					),
					array(
						'id'         => 'is-maintenance',
						'type'       => 'switch',
						'title'      => __( 'Maintenance Mode', 'porto' ),
						'subtitle'   => __( 'This mode is for showing alternative page during the maintenance of the site.', 'porto' ),
						'default'    => false,
						'hint'       => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'is-maintenance.jpg"/>' ),
						),
					),
					array(
						'id'       => 'maintenance-page',
						'type'     => 'select',
						'data'     => 'page',
						'title'    => __( 'Select a Maintenance Page', 'porto' ),
						'subtitle' => __( 'Please note that logged users will still be able to access the site.', 'porto' ),
						'required' => array( 'is-maintenance', 'equals', true ),
					),
					array(
						'id'       => 'button-style',
						'type'     => 'button_set',
						'title'    => __( 'Button Style', 'porto' ),
						'subtitle' => __( 'Select "Borders" to set buttons outline style.', 'porto' ),
						'options'  => array(
							'' => array(
								'label' => __( 'Default', 'porto' ),
								'hint'  => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'button-style.jpg"/>' ),
								),
							),
							'btn-borders' => array(
								'label' => __( 'Borders', 'porto' ),
								'hint'  => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'button-style-btn-borders.jpg"/>' ),
								),
							),
						),
						'default'  => '',
					),
					array(
						'id'        => 'border-radius',
						'type'      => 'switch',
						'title'     => __( 'Border Radius', 'porto' ),
						'subtitle'  => __( 'Constrols if you\'re using rounded style throughout the site.', 'porto' ),
						'default'   => false,
						'compiler'  => true,
						'hint'      => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'border-radius.gif"/>' ),
						),
						'transport' => 'refresh',
					),
					array(
						'id'       => 'disable-builder-tooltip',
						'type'     => 'switch',
						'title'    => __( 'Development Mode', 'porto' ),
						'subtitle' => __( 'Show "Build with Template Builder" button and tooltip to go to the Template Builder', 'porto' ),
						'default'  => false,
						'on'       => __( 'Disable', 'porto' ),
						'off'      => __( 'Enable', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'builder_guide.jpg"/>' ),
						),
					),
					array(
						'id'        => 'thumb-padding',
						'type'      => 'switch',
						'title'     => __( 'Thumbnail Padding', 'porto' ),
						'subtitle'  => __( 'This will display border and spacing for thumbnail images such as product images.', 'porto' ),
						'default'   => false,
						'compiler'  => true,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'thumb-padding.gif"/>' ),
						),
						'transport' => 'refresh',
					),
					array(
						'id'       => 'show-content-type-skin',
						'type'     => 'switch',
						'title'    => __( 'Show Content Type Skin Options', 'porto' ),
						'subtitle' => __( 'Show skin options when edit post, page, product, portfolio, member, event.', 'porto' ),
						'default'  => true,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-content-type-skin.gif"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'show-category-skin',
						'type'     => 'switch',
						'title'    => __( 'Show Category Skin Options', 'porto' ),
						'subtitle' => __( 'Show skin options when edit the category of post, product, portfolio, member, event', 'porto' ),
						'default'  => true,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-category-skin.gif"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'show-skeleton-screen',
						'type'     => 'button_set',
						'title'    => __( 'Show Skeleton Screens (Do not use with the 3rd party plugins)', 'porto' ),
						'subtitle' => __( 'This will show skeleton screens during page load for the selected pages.', 'porto' ),
						'multi'    => true,
						'options'  => array(
							'shop'      => array(
								'label' => __( 'Shop Pages', 'porto' ),
								'hint'  => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-skeleton-screen-shop.jpg"/>' ),
								),
							),
							'product'   => array(
								'label' => __( 'Product Page', 'porto' ),
								'hint'  => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-skeleton-screen-product.jpg"/>' ),
								),
							),
							'quickview' => array(
								'label' => __( 'Product Quickview', 'porto' ),
								'hint'  => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-skeleton-screen-quickview.jpg"/>' ),
								),
							),
							'blog'      => array(
								'label' => __( 'Blog Pages', 'porto' ),
								'hint'  => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-skeleton-screen-blog.jpg"/>' ),
								),
							),
						),
						'default' => array(),
					),
					array(
						'id'       => 'allow-svg-upload',
						'type'     => 'switch',
						'title'    => __( 'Allow SVG upload', 'porto' ),
						'subtitle' => __( 'Allow SVG uploads as well as SVG format for Menu Icon.', 'porto' ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
				)
			);

			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'icon-general',
					'icon_class' => 'porto-icon',
					'title'      => __( 'General', 'porto' ),
				),
				$options_style
			);

			$this->sections[] = array(
				'subsection' => true,
				'title'      => __( 'General', 'porto' ),
				'fields'     => $general_site_options,
			);

			/**
			 * Template Builder
			 *  
			 * @since 6.12.0
			 */ 
			$this->sections[] = array(
				'subsection' => true,
				'title'      => __( 'Popup Builder', 'porto' ),
				'fields'     => array(
					array(
						'id'    => 'desc_info_builder_popup',
						'type'  => 'info',
						'desc'  => wp_kses( 
							__( '
							<span><span style="min-width: 150px;">
								<b>Popup Builder</b>
								<span class="description">You can control the various popup like Newsletter Popup.</span>
							</span>
							<span>
								<span class="flex-row">
									<img src="' . PORTO_OPTIONS_URI . '/builder/popup.svg' . '" style="margin-right: 10px;" />
									<span>
										<a href="' . $popup_url . '" target="_blank">Add or Change Popup Layout</a>
										Popup like Newsletter, promo popup, discount popup is built with pre-made on template builder.
									</span>
								</span>
							</span></span>', 'porto' ), 
							array( 
								'b'    => array(),
								'span' => array(
									'class' => array(),
									'style' => array(),
								),
								'img'  => array(
									'src'   => array(),
									'style' => array(),
								),
								'a'    => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						),
						'class' => 'porto-opt-ux-builder',
					),
				),
			);
			$this->sections[] = array(
				'subsection' => true,
				'title'      => __( 'Google Map API', 'porto' ),
				'fields'     => array(
					array(
						'id'      => 'gmap_api',
						'type'    => 'text',
						'title'   => __( 'Google Map API Key', 'porto' ),
						'default' => '',
					),
				),
			);
			$this->sections[] = array(
				'subsection' => true,
				'title'      => __( 'Scrollbar', 'porto' ),
				'fields'     => array(
					array(
						'id'       => 'smooth-scroll',
						'type'     => 'switch',
						'title'    => __( 'Smooth Scroll', 'porto' ),
						'subtitle' => __( 'Allow smooth scrolling of site pages with the mouse wheel(scroll)', 'porto' ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'change-scrollbar',
						'type'     => 'switch',
						'title'    => __( 'Custom Scrollbar', 'porto' ),
						'subtitle' => __( 'Turn on if you\'d like to change the background and handle color of the scrollbar. Styling and support varies depending on the browser.', 'porto' ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'scrollbar-bg',
						'type'     => 'color',
						'title'    => __( 'Scrollbar Background', 'porto' ),
						'subtitle' => __( 'Controls the background color of the scrollbar.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
						'selector' => array(
							'node' => ':root',
						),
						'required' => array( 'change-scrollbar', 'equals', true ),
					),
					array(
						'id'       => 'scrollbar-handle-clr',
						'type'     => 'color',
						'title'    => __( 'Scrollbar Handle Color', 'porto' ),
						'subtitle' => __( 'Controls the color of the scrollbar handle.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
						'selector' => array(
							'node' => ':root',
						),
						'required' => array( 'change-scrollbar', 'equals', true ),
					),
				),
			);
			$this->sections[] = array(
				'title'      => __( 'SEO', 'porto' ),
				'subsection' => true,
				'fields'     => array(
					array(
						'id'       => 'rich-snippets',
						'type'     => 'switch',
						'title'    => __( 'Microdata Rich Snippets', 'porto' ),
						'subtitle' => __( 'Select "Enable" to make Title, Update Date and Author rich snippets data site wide. For this option, you have to enable "Microdata Rich Snippets" of "Page Meta Options"', 'porto' ),
						'default'  => true,
						'on'       => __( 'Enable', 'porto' ),
						'off'      => __( 'Disable', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'rich-snippets.gif"/>' ),
						),
					),
					array(
						'id'       => 'mobile-menu-item-nofollow',
						'type'     => 'switch',
						'title'    => __( 'Add rel="nofollow" to mobile menu items', 'porto' ),
						'subtitle' => __( 'Select "Yes" to add relationship attribute "nofollow" to the mobile menu items.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mobile-menu-item-nofollow.gif"/>' ),
						),
					),
					array(
						'id'       => 'open-graph',
						'type'     => 'switch',
						'title'    => __( 'Open Graph Meta Tags', 'porto' ),
						'subtitle' => __( 'Turn on to enable open graph meta tags which are mainly used when sharing pages on social networking sites like Facebook and Twitter.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
				),
			);
			// 404
			$this->sections[] = array(
				'title'      => __( '404 Page', 'porto' ),
				'subsection' => true,
				'fields'     => array(
					array(
						'id'       => 'error-block',
						'type'     => 'text',
						'title'    => __( '404 Links Block', 'porto' ),
						'subtitle' => __( 'Input a block slug name. Show the block on the right space of 404 page.', 'porto' ),
						'default'  => 'error-404',
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'error-block.jpg"/>' ),
						),
					),
				),
			);

			// BBPress & BuddyPress
			$this->sections[] = array(
				'title'      => __( 'BBPress & BuddyPress', 'porto' ),
				'subsection' => true,
				'fields'     => array(
					array(
						'id'      => 'bb-layout',
						'type'    => 'image_select',
						'title'   => __( 'BBPress & BuddyPress Page Layout', 'porto' ),
						'options' => $page_layouts,
						'default' => 'fullwidth',
					),
					array(
						'id'       => 'bb-sidebar',
						'type'     => 'select',
						'title'    => __( 'Select Sidebar', 'porto' ),
						'required' => array( 'bb-layout', 'equals', $sidebars ),
						'data'     => 'sidebars',
					),
					array(
						'id'       => 'bb-sidebar2',
						'type'     => 'select',
						'title'    => __( 'Select Sidebar 2', 'porto' ),
						'required' => array( 'bb-layout', 'equals', $both_sidebars ),
						'data'     => 'sidebars',
					),
				),
			);

			// Social Share
			$this->sections[] = array(
				'title'      => __( 'Social Share', 'porto' ),
				'subsection' => true,
				'fields'     => array(
					array(
						'id'       => 'share-enable',
						'type'     => 'switch',
						'title'    => __( 'Show Social Links', 'porto' ),
						'subtitle' => __( 'To show social links, you should check <strong>Default</strong> or <strong>Yes</strong> of Share Meta Option.', 'porto' ),
						'desc'     => __( 'Show social links in post and product, page, portfolio, etc.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'share-enable.gif"/>' ),
						),
					),
					array(
						'id'       => 'share-nofollow',
						'type'     => 'switch',
						'title'    => __( 'Add rel="nofollow" to social links', 'porto' ),
						'subtitle' => __( 'Select "Yes" to add relationship attributes "nofollow" to the mobile menu items.', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-facebook',
						'type'     => 'switch',
						'title'    => __( 'Enable Facebook Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-twitter',
						'type'     => 'switch',
						'title'    => __( 'Enable X(Twitter) Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-linkedin',
						'type'     => 'switch',
						'title'    => __( 'Enable LinkedIn Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-googleplus',
						'type'     => 'switch',
						'title'    => __( 'Enable Google + Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-pinterest',
						'type'     => 'switch',
						'title'    => __( 'Enable Pinterest Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-email',
						'type'     => 'switch',
						'title'    => __( 'Enable Email Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-vk',
						'type'     => 'switch',
						'title'    => __( 'Enable VK Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-xing',
						'type'     => 'switch',
						'title'    => __( 'Enable Xing Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-tumblr',
						'type'     => 'switch',
						'title'    => __( 'Enable Tumblr Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-reddit',
						'type'     => 'switch',
						'title'    => __( 'Enable Reddit Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-whatsapp',
						'type'     => 'switch',
						'title'    => __( 'Enable WhatsApp Share', 'porto' ),
						'subtitle' => __( 'Only For Mobile', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-viber',
						'type'     => 'switch',
						'title'    => __( 'Enable Viber Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
				),
			);

			// Slider Config
			$this->sections[] = array(
				'title'      => __( 'Slider Config', 'porto' ),
				'subsection' => true,
				'fields'     => array(
					array(
						'id'    => 'desc_info_slider_option',
						'type'  => 'info',
						'desc'  => wp_kses(
							/* translators: %s: Builder url */
							sprintf( __( '<strong>Important Note:</strong> Controls the <b>Global Carousel Options</b> throughout the site. These options can be overrided with widget options.', 'porto' ), $archive_url, $type_url ),
							array(
								'strong' => array(),
								'b'      => array(),
							)
						),
						'class' => 'porto-important-note',
					),
					array(
						'id'       => 'slider-loop',
						'type'     => 'switch',
						'title'    => __( 'Loop', 'porto' ),
						'subtitle' => __( 'Enable carousel items to slide infinitely.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-autoplay',
						'type'     => 'switch',
						'title'    => __( 'Auto Play', 'porto' ),
						'subtitle' => __( 'Enable autoslide of carousel items.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-speed',
						'type'     => 'text',
						'title'    => __( 'Play Speed', 'porto' ),
						'subtitle' => __( 'Change carousel item\'s autoplay duration.', 'porto' ),
						'required' => array( 'slider-autoplay', 'equals', true ),
						'desc'     => __( 'unit: millisecond', 'porto' ),
						'default'  => 5000,
					),
					array(
						'id'       => 'slider-autoheight',
						'type'     => 'switch',
						'title'    => __( 'Auto Height', 'porto' ),
						'subtitle' => __( 'Each slides have their own height. Slides can have different height.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'slider-autoheight.gif"/>' ),
						),
					),
					array(
						'id'       => 'slider-nav',
						'type'     => 'switch',
						'title'    => __( 'Show Next/Prev Buttons', 'porto' ),
						'subtitle' => __( 'Determine whether to show/hide slider navigations.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-nav-hover',
						'type'     => 'switch',
						'title'    => __( 'Show Next/Prev Buttons on Hover', 'porto' ),
						'subtitle' => __( 'Hides slider navs automatically and show them only if mouse is over.', 'porto' ),
						'required' => array( 'slider-nav', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-margin',
						'type'     => 'switch',
						'title'    => __( 'Enable Margin', 'porto' ),
						'required' => array( 'slider-nav-hover', 'equals', false ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-dots',
						'type'     => 'switch',
						'title'    => __( 'Show Dots Navigation', 'porto' ),
						'subtitle' => __( 'Determine whether to show/hide slider dots.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-animatein',
						'type'     => 'text',
						'title'    => __( 'Animate In', 'porto' ),
						'subtitle' => __( 'Choose sliding animation when next slides become visible.', 'porto' ),
						'default'  => '',
						'desc'     => __( 'Please input animation. Please reference <a href="http://daneden.github.io/animate.css/">animate.css</a>. ex: fadeIn', 'porto' ),
					),
					array(
						'id'       => 'slider-animateout',
						'type'     => 'text',
						'title'    => __( 'Animate Out', 'porto' ),
						'subtitle' => __( 'Choose sliding animation when previous slides become invisible.', 'porto' ),
						'default'  => '',
						'desc'     => __( 'Please input animation. Please reference <a href="http://daneden.github.io/animate.css/">animate.css</a>. ex: fadeOut', 'porto' ),
					),
				),
			);

			/**
			 * Ai Engine with GPT-3
			 *  
			 * @since 6.8.0
			 * */ 
			$this->sections[] = array(
				'subsection' => true,
				'title'      => __( 'ChatGPT AI Generator', 'porto' ),
				'fields'     => array(
					array(
						'id'    => 'desc_info_ai_option',
						'type'  => 'info',
						'desc'  => wp_kses( __( 'You can generate content and excerpts for various post type using <b>ChatGPT</b>.', 'porto' ), 
							array( 'b' => array() )
						),
						'class' => 'porto-important-note',
					),
					array(
						'id'       => 'ai-gpt-key',
						'type'     => 'text',
						'title'    => __( 'Input API Key', 'porto' ),
						'subtitle' => sprintf( __( 'You can get your API Key in your %1$sOpenAI Acount%2$s. Porto uses the text-davinci-003 model.', 'porto' ), '<a href="https://platform.openai.com/account/api-keys" target="_blank">', '</a>' ),
						'default'  => '',
					),
				),
			);		

			// Layout
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'            => 'Simple-Line-Icons-layers',
					'icon_class'      => '',
					'title'           => __( 'Layout', 'porto' ),
				),
				$options_style
			);

			$this->sections[] = array(
				'subsection' => true,
				'title'      => __( 'Layout', 'porto' ),
				'fields'     => array(
					array(
						'id'        => 'container-width',
						'type'      => 'text',
						'title'     => __( 'Container Max Width (px)', 'porto' ),
						'subtitle'  => 'Controls the overall site width. 960 - 1920',
						'default'   => '1140',
						'compiler'  => true,
						'transport' => 'refresh',
						'selector'  => array(
							'node' => '.container, .wp-block, .col-half-section, .elementor-section',
							'unit' => 'px',
						),
					),
					array(
						'id'        => 'grid-gutter-width',
						'type'      => 'button_set',
						'title'     => __( 'Grid Gutter Width', 'porto' ),
						'subtitle'  => __( 'Controls the space between columns in a row.', 'porto' ),
						'options'   => array(
							'16' => '16px',
							'20' => '20px',
							'24' => '24px',
							'30' => '30px',
						),
						'default'   => '30',
						'compiler'  => true,
						'transport' => 'refresh',
						'selector'  => array(
							'node' => ':root',
							'unit' => 'px',
						),
					),
					array(
						'id'       => 'wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Site Layout Mode', 'porto' ),
						'subtitle' => __( 'Controls the layout of whole site.', 'porto' ),
						'options'  => $body_wrapper,
						'default'  => 'full',
					),
					array(
						'id'       => 'layout',
						'type'     => 'image_select',
						'title'    => __( 'Page Layout', 'porto' ),
						'subtitle' => __( 'Controls the global page layout with sidebars.', 'porto' ),
						'options'  => $page_layouts,
						'default'  => 'right-sidebar',
					),
					array(
						'id'       => 'sidebar',
						'type'     => 'select',
						'title'    => __( 'Select Sidebar', 'porto' ),
						'subtitle' => __( 'Select the global sidebar 1.', 'porto' ),
						'required' => array( 'layout', 'equals', $sidebars ),
						'data'     => 'sidebars',
						'default'  => 'blog-sidebar',
					),
					array(
						'id'       => 'sidebar2',
						'type'     => 'select',
						'title'    => __( 'Select Sidebar 2', 'porto' ),
						'subtitle' => __( 'Select the global sidebar 2.', 'porto' ),
						'required' => array( 'layout', 'equals', $both_sidebars ),
						'data'     => 'sidebars',
						'default'  => 'secondary-sidebar',
					),
					array(
						'id'       => 'header-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Header Wrapper', 'porto' ),
						'subtitle' => __( 'Controls the header layout.', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => array(
							'wide'  => array(
								'title' => __( 'Wide', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/header-full.svg',
							),
							'full'  => array(
								'title' => __( 'Container', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/header-container.svg',
							),
							'boxed' => array(
								'title' => __( 'Boxed', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/header-boxed.svg',
							),
						),
						'default'  => 'full',
					),
					array(
						'id'       => 'banner-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Banner Wrapper', 'porto' ),
						'subtitle' => __( 'Controls the banner layout.', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => array(
							'wide'  => array(
								'title' => __( 'Wide', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/banner-full.svg',
							),
							'boxed' => array(
								'title' => __( 'Boxed', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/banner-boxed.svg',
							),
						),
						'default'  => 'wide',
					),
					array(
						'id'       => 'breadcrumbs-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Breadcrumbs Wrapper', 'porto' ),
						'subtitle' => __( 'Controls the page header layout.', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => array(
							'wide'  => array(
								'title' => __( 'Wide', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/breadcrumb-full.svg',
							),
							'full'  => array(
								'title' => __( 'Container', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/breadcrumb-container.svg',
							),
							'boxed' => array(
								'title' => __( 'Boxed', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/breadcrumb-boxed.svg',
							),
						),
						'default'  => 'full',
					),
					array(
						'id'       => 'main-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Page Content Wrapper', 'porto' ),
						'subtitle' => __( 'Controls the page content layout.', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => array(
							'wide'  => array(
								'title' => __( 'Wide', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/page-content-full.svg',
							),
							'boxed' => array(
								'title' => __( 'Boxed', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/page-content-boxed.svg',
							),
						),
						'default'  => 'wide',
					),
					array(
						'id'       => 'footer-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Footer Wrapper', 'porto' ),
						'subtitle' => __( 'Controls the footer layout.', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => array(
							'wide'  => array(
								'title' => __( 'Wide', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/footer-full.svg',
							),
							'full'  => array(
								'title' => __( 'Container', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/footer-container.svg',
							),
							'boxed' => array(
								'title' => __( 'Boxed', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/layouts/footer-boxed.svg',
							),
						),
						'default'  => 'full',
					),
				),
			);
			$this->sections[] = array(
				'id'         => 'html-blocks',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'HTML Blocks', 'porto' ),
				'desc'       => __( 'Please check "Theme Layout" section to see blocks\' locations.', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'html-top',
						'type'     => 'ace_editor',
						'mode'     => 'html',
						'title'    => __( 'Top', 'porto' ),
						'subtitle' => __( 'Executes at the top of the page', 'porto' ),
						'desc'     => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'html-top.jpg"/>' ),
						),
					),
					array(
						'id'    => 'html-banner',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Banner', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'    => 'html-content-top',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Content Top', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'    => 'html-content-inner-top',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Content Inner Top', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'    => 'html-content-inner-bottom',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Content Inner Bottom', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'    => 'html-content-bottom',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Content Bottom', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'       => 'html-bottom',
						'type'     => 'ace_editor',
						'mode'     => 'html',
						'title'    => __( 'Bottom', 'porto' ),
						'subtitle' => __( 'Executes at the bottom of the page', 'porto' ),
						'desc'     => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'html-bottom.jpg"/>' ),
						),
					),
				),
			);
			
			require_once PORTO_ADMIN . '/theme_options/skin.php';

			// Header Settings
			if ( $this->legacy_mode ) {
				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'header-settings',
						'icon'       => 'Simple-Line-Icons-earphones',
						'icon_class' => '',
						'title'      => __( 'Header', 'porto' ),
					),
					$options_style
				);
				$this->sections[] = array(
					'subsection' => true,
					'title'      => __( 'Header', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'     => 'desc_info_header_skin_setting',
							'type'   => 'info',
							'desc'   => wp_kses(
								/* translators: %s: Header skin settings url */
								sprintf( __( 'Go to <a %s>Header Skin Settings</a>', 'porto' ), 'href="skin-header" class="goto-section"', 'porto' ),
								array(
									'a' => array(
										'href'  => array(),
										'class' => array(),
									),
								)
							),
							'class'  => 'field_move',
							'notice' => false,
						),
						array(
							'id'    => 'desc_info_go_header_builder',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $header_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
									'i'     => array(
										'class'  => array(),
									),
									'span'  => array(),
									'br'    => array(),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'        => 'header-view',
							'type'      => 'button_set',
							'title'     => __( 'Header View', 'porto' ),
							'subtitle'  => __( 'Controls if using default header or fixed header(absolute header), or hiding it.', 'porto' ),
							'options'   => array_merge(
								porto_ct_header_view( true ),
								array(
									'hide' => __( 'Hide', 'porto' ),
								)
							),
							'class'   => 'pt-always-visible',
							'default'   => 'default',
							'transport' => 'refresh',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'        => 'header-side-position',
							'type'      => 'button_set',
							'title'     => __( 'Side Header Position', 'porto' ),
							'subtitle'  => __( 'When your header type is side header, determines where to put it.', 'porto' ),
							'options'   => array(
								''      => array(
									'label' => __( 'Left (Right on RTL)', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-side-pl.jpg"/>' ),
									),
								),
								'right'      => array(
									'label' => __( 'Right (Left on RTL)', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-side-pr.jpg"/>' ),
									),
								),
							),
							'default'   => '',
							'transport' => 'refresh',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'show-header-tooltip',
							'type'     => 'switch',
							'title'    => __( 'Show Tooltip', 'porto' ),
							'subtitle' => __( 'Turn on to display tooltip icon with flash effect and popup content.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-header-tooltip.gif"/>' ),
							),
						),
						array(
							'id'       => 'header-tooltip',
							'type'     => 'textarea',
							'title'    => __( 'Tooltip Content', 'porto' ),
							'required' => array( 'show-header-tooltip', 'equals', true ),
						),
						array(
							'id'     => 'desc_info_header_preset_customize',
							'type'   => 'info',
							'desc'   => __( 'For Header Preset or Customize Header Builder', 'porto' ),
							'notice' => false,
							'class'  => 'porto-redux-section',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'show-header-top',
							'type'     => 'switch',
							'title'    => __( 'Show Header Top', 'porto' ),
							'subtitle' => __( 'Controls if show header top.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
							'required' => array(
								array( 'header-type-select', 'equals', '' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-header-top.jpg"/>' ),
							),
						),
						array(
							'id'       => 'welcome-msg',
							'type'     => 'textarea',
							'title'    => __( 'Welcome Message', 'porto' ),
							'subtitle' => __( 'Inputs the html to be displayed in the header top for preset header types.', 'porto' ),
							'default'  => '',
							'required' => array(
								array( 'header-type-select', 'equals', '' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'welcome-msg.gif"/>' ),
							),
						),
						array(
							'id'       => 'header-contact-info',
							'type'     => 'textarea',
							'title'    => __( 'Contact Info', 'porto' ),
							'subtitle' => __( 'Inputs the html content to be used as contact information in the header.', 'porto' ),
							'default'  => "<ul class=\"nav nav-pills nav-top\">\r\n\t<li class=\"d-none d-sm-block\">\r\n\t\t<a href=\"#\" target=\"_blank\"><i class=\"fas fa-angle-right\"></i>About Us</a> \r\n\t</li>\r\n\t<li class=\"d-none d-sm-block\">\r\n\t\t<a href=\"#\" target=\"_blank\"><i class=\"fas fa-angle-right\"></i>Contact Us</a> \r\n\t</li>\r\n\t<li class=\"phone nav-item-left-border nav-item-right-border\">\r\n\t\t<a href=\"tel:+1234567890\"><i class=\"fas fa-phone\"></i>(123) 456-7890</a>\r\n\t</li>\r\n</ul>\r\n",
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-contact-info.gif"/>' ),
							),
						),
						array(
							'id'      => 'header-copyright',
							'type'    => 'textarea',
							'title'   => __( 'Side Navigation Copyright (Header Type: Side)', 'porto' ),
							/* translators: %s: Current year */
							'default' => sprintf( __( '&copy; Copyright %s. All Rights Reserved.', 'porto' ), date( 'Y' ) ),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'    => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-copyright.gif"/>' ),
							),
						),
					),
				);				
			} else {
				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'header-settings',
						'icon'       => 'Simple-Line-Icons-earphones',
						'icon_class' => '',
						'title'      => __( 'Header', 'porto' ),
					),
					$options_style
				);
				$this->sections[] = array(
					'subsection' => true,
					'title'      => __( 'Header', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'    => 'desc_info_go_header_builder',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily.', 'porto' ), $header_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'        => 'header-view',
							'type'      => 'button_set',
							'title'     => __( 'Header View', 'porto' ),
							'subtitle'  => __( 'Controls if using default header or fixed header(absolute header), or hiding it.', 'porto' ),
							'options'   => array_merge(
								porto_ct_header_view( true ),
								array(
									'hide' => __( 'Hide', 'porto' ),
								)
							),
							'default'   => 'default',
							'transport' => 'refresh',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'        => 'header-side-position',
							'type'      => 'button_set',
							'title'     => __( 'Side Header Position', 'porto' ),
							'subtitle'  => __( 'When your header type is side header, determines where to put it.', 'porto' ),
							'options'   => array(
								''      => array(
									'label' => __( 'Left (Right on RTL)', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-side-pl.jpg"/>' ),
									),
								),
								'right'      => array(
									'label' => __( 'Right (Left on RTL)', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-side-pr.jpg"/>' ),
									),
								),
							),
							'default'   => '',
							'transport' => 'refresh',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
					),
				);
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Header Type', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'      => 'desc_info_header_type',
							'type'    => 'info',
							'required' => array(
								array( 'header-type-select', 'equals', 'header_builder' ),
							),
							'desc'    => wp_kses(
								sprintf(
									/* translators: %s: Header builder url */
									__( 'You can add new header layout using <a href="%s" class="goto-header-builder">Header Builder in customizer panel</a>.', 'porto' ),
									esc_url(
										add_query_arg(
											array(
												'autofocus' => array(
													'section' => 'porto_header_layouts',
												),
												'url' => home_url(),
											),
											admin_url( 'customize.php' )
										)
									)
								),
								array(
									'a' => array(
										'href'  => array(),
										'title' => array(),
										'class' => array(),
									),
								)
							),
						),
						array(
							'id'       => 'header-type-select',
							'type'     => 'button_set',
							'title'    => __( 'Select Header', 'porto' ),
							'subtitle' => __( 'Preset, Header Builder in Customizer or Header Builder in Porto Templates builder.', 'porto' ),
							'options'  => array(
								''               => array(
									'label' => __( 'Header Type (deprecated)', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-type-select.gif"/>' ),
									),
								),
								'header_builder' => array(
									'label' => __( 'Header builder in Customizer (deprecated)', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-type-select-builder.jpg"/>' ),
									),
								),
								'header_builder_p' => array(
									'label' => __( 'Header builder in Porto Templates builder', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-type-select-builder_p.gif"/>' ),
									),
								),
							),
							'default'  => '',
						),
						array(
							'id'       => 'header-woo-icon',
							'type'     => 'button_set',
							'title'    => __( 'Show Wishlist/Account', 'porto' ),
							'desc'     => __( 'Determines to show the icon in header preset.', 'porto' ),
							'multi'    => true,
							'options'  => array(
								'wishlist' => __( 'Wishlist', 'porto' ),
								'account'  => __( 'Account', 'porto' ),
							),
							'required' => array(
								array( 'header-type-select', 'equals', '' ),
								array( 'header-type', 'equals', array( '1', '4', '7', '9', 'side' ) ),
							),
							'default'  => array(),
						),
						array(
							'id'         => 'header-type',
							'type'       => 'image_select',
							'full_width' => true,
							'title'      => __( 'Header Types', 'porto' ),
							'subtitle'   => __( 'Whenever you change header type, related theme options such as Mini Cart Type and Search Layout may be changed together according to it.', 'porto' ),
							'options'    => $porto_header_type,
							'default'    => '10',
							'required'   => array( 'header-type-select', 'equals', '' ),
						),
					),
				);
			} else {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Header Type', 'porto' ),
					'transport'  => 'postMessage',
					'class'      => 'fse-hd-type',
					'fields'     => array(
						array(
							'id'      => 'header-type-select',
							'type'    => 'button_set',
							'title'   => __( 'Select Header', 'porto' ),
							'options' => array(
								'header_builder_p' => array(
									'label' => __( 'Header builder in Porto Templates builder', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-type-select-builder_p.gif"/>' ),
									),
								),
							),
							'default' => 'header_builder_p',
						),
					),
				);
			}
			$this->sections[] = array(
				'id'         => 'header-view-currency-switcher',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Language, Currency Switcher', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'wpml-switcher',
						'type'     => 'switch',
						'title'    => __( 'Show Language Switcher', 'porto' ),
						'subtitle' => __( 'Show language switcher instead of view switcher menu. Compatible with Polylang and qTranslate X plugins.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'wpml-switcher-pos',
						'type'     => 'button_set',
						'title'    => __( 'Language Switcher Position', 'porto' ),
						'required' => array( 'wpml-switcher', 'equals', true ),
						'options'  => array(
							''          => __( 'Default', 'porto' ),
							'top_nav'   => __( 'In Top Navigation', 'porto' ),
							'main_menu' => __( 'In Main Menu', 'porto' ),
						),
						'default'  => '',
					),
					array(
						'id'       => 'wpml-switcher-html',
						'type'     => 'switch',
						'title'    => __( 'Show Language Switcher HTML', 'porto' ),
						'subtitle' => __( 'Show language switcher html code if there isn\'t any switcher.', 'porto' ),
						'default'  => false,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'wpml-switcher-html.jpg"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'wcml-switcher',
						'type'     => 'switch',
						'title'    => __( 'Show Currency Switcher', 'porto' ),
						'subtitle' => __( 'Show currency switcher instead of currency switcher menu.', 'porto' ) . ' ' . __( 'Compatible with WPML Currency Switcher and Woocommerce Currency Switcher plugins.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'wcml-switcher-pos',
						'type'     => 'button_set',
						'title'    => __( 'Currency Switcher Position', 'porto' ),
						'required' => array( 'wcml-switcher', 'equals', true ),
						'options'  => array(
							''          => __( 'Default', 'porto' ),
							'top_nav'   => __( 'In Top Navigation', 'porto' ),
							'main_menu' => __( 'In Main Menu', 'porto' ),
						),
						'default'  => '',
					),
					array(
						'id'       => 'wcml-switcher-html',
						'type'     => 'switch',
						'title'    => __( 'Show Currency Switcher HTML', 'porto' ),
						'subtitle' => __( 'Show currency switcher html code if there isn\'t any switcher.', 'porto' ),
						'default'  => false,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'wcml-switcher-html.jpg"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'desc_info_switcher',
						'type'     => 'info',
						'title'    => __( 'Styling', 'porto' ),
						'notice'   => false,
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'       => 'switcher-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color for language switcher and currency switcher.', 'porto' ),
						'default'  => 'transparent',
						'validate' => 'color',
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'       => 'switcher-hbg-color',
						'type'     => 'color',
						'title'    => __( 'Hover Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color for language switcher and currency switcher on hover.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'       => 'switcher-top-level-hover',
						'type'     => 'switch',
						'title'    => __( 'Change top level on hover', 'porto' ),
						'subtitle' => __( 'Controls if change the text color and background color for the first level item on hover.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'      => 'switcher-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '#777777',
							'hover'   => '#777777',
						),
						'subtitle' => __( 'Regular is the color of top level link and hover is the color of sub menu items.', 'porto' ),
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Social Links', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'show-header-socials',
						'type'     => 'switch',
						'title'    => __( 'Show Social Links', 'porto' ),
						'subtitle' => __( 'Show/Hide the social links in header.', 'porto' ),
						'default'  => false,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-header-socials.gif"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'header-socials-nofollow',
						'type'     => 'switch',
						'title'    => __( 'Add rel="nofollow" to social links', 'porto' ),
						'subtitle' => __( 'Turn on to add "nofollow" attribute to header social links.', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
						'default'  => true,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-socials-nofollow.gif"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'header-social-facebook',
						'type'     => 'text',
						'title'    => __( 'Facebook', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-twitter',
						'type'     => 'text',
						'title'    => __( 'X(Twitter)', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-rss',
						'type'     => 'text',
						'title'    => __( 'RSS', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-pinterest',
						'type'     => 'text',
						'title'    => __( 'Pinterest', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-youtube',
						'type'     => 'text',
						'title'    => __( 'Youtube', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-instagram',
						'type'     => 'text',
						'title'    => __( 'Instagram', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-skype',
						'type'     => 'text',
						'title'    => __( 'Skype', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-linkedin',
						'type'     => 'text',
						'title'    => __( 'LinkedIn', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-vk',
						'type'     => 'text',
						'title'    => __( 'VK', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-xing',
						'type'     => 'text',
						'title'    => __( 'Xing', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-tumblr',
						'type'     => 'text',
						'title'    => __( 'Tumblr', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-reddit',
						'type'     => 'text',
						'title'    => __( 'Reddit', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-vimeo',
						'type'     => 'text',
						'title'    => __( 'Vimeo', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-telegram',
						'type'     => 'text',
						'title'    => __( 'Telegram', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-yelp',
						'type'     => 'text',
						'title'    => __( 'Yelp', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-flickr',
						'type'     => 'text',
						'title'    => __( 'Flickr', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-whatsapp',
						'type'     => 'text',
						'title'    => __( 'WhatsApp', 'porto' ),
						'desc'     => __( 'Only For Mobile', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-wechat',
						'type'     => 'text',
						'title'    => __( 'WeChat', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-spotify',
						'type'     => 'text',
						'title'    => __( 'Spotify', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-tiktok',
						'type'     => 'text',
						'title'    => __( 'Tiktok', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-viber',
						'type'     => 'text',
						'title'    => __( 'Viber', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
				),
			);

			$skin_search_form = array(
				array(
					'id'     => 'desc_info_search_form',
					'type'   => 'info',
					'title'  => __( 'Styling', 'porto' ),
					'notice' => false,
					'required' => array(
						array( 'header-type-select', 'not', 'header_builder_p' ),
					),
				),
				array(
					'id'       => 'searchform-bg-color',
					'type'     => 'color',
					'title'    => __( 'Background Color', 'porto' ),
					'subtitle' => __( 'Controls the background color of search form.', 'porto' ),
					'default'  => '#ffffff',
					'hint'     => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'searchform-bg-color.gif"/>' ),
					),
					'validate' => 'color',
					'required' => array(
						array( 'header-type-select', 'not', 'header_builder_p' ),
					),
				),
				array(
					'id'       => 'searchform-border-color',
					'type'     => 'color',
					'title'    => __( 'Border Color', 'porto' ),
					'subtitle' => __( 'Controls the border color of search form.', 'porto' ),
					'default'  => '#eeeeee',
					'validate' => 'color',
					'hint'     => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'searchform-border-color.gif"/>' ),
					),
					'selector'       => array(
						'node' => '#header .searchform',
					),
					'required' => array(
						array( 'header-type-select', 'not', 'header_builder_p' ),
					),
				),
				array(
					'id'       => 'searchform-popup-border-color',
					'type'     => 'color',
					'title'    => __( 'Popup Border Color', 'porto' ),
					'subtitle' => __( 'Controls the border color of search popup.', 'porto' ),
					'default'  => '#cccccc',
					'validate' => 'color',
					'hint'     => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'searchform-popup-border-color.gif"/>' ),
					),
					'required' => array(
						array( 'search-layout', 'equals', array( 'large', 'advanced' ) ),
						array( 'header-type-select', 'not', 'header_builder_p' ),
					),
				),
				array(
					'id'       => 'searchform-text-color',
					'type'     => 'color',
					'title'    => __( 'Text Color', 'porto' ),
					'subtitle' => __( 'Controls the text color on search form.', 'porto' ),
					'default'  => '#555555',
					'validate' => 'color',
					'hint'     => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'searchform-text-color.gif"/>' ),
					),
					'selector'       => array(
						'node' => '#header .searchform',
					),
					'required' => array(
						array( 'header-type-select', 'not', 'header_builder_p' ),
					),
				),
				array(
					'id'       => 'searchform-hover-color',
					'type'     => 'color',
					'title'    => __( 'Button Text Color', 'porto' ),
					'subtitle' => __( 'Controls the search icon color on search form.', 'porto' ),
					'default'  => '#333333',
					'hint'     => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'searchform-hover-color.gif"/>' ),
					),
					'validate' => 'color',
					'required' => array(
						array( 'header-type-select', 'not', 'header_builder_p' ),
					),
				),
				array(
					'id'     => 'desc_info_search_sticky',
					'type'   => 'info',
					'title'  => __( 'On Sticky Header', 'porto' ),
					'notice' => false,
					'required' => array(
						array( 'header-type-select', 'not', 'header_builder_p' ),
					),
				),
				array(
					'id'       => 'sticky-searchform-popup-border-color',
					'type'     => 'color',
					'title'    => __( 'Popup Border Color on Sticky Header', 'porto' ),
					'subtitle' => __( 'Controls the border color of search popup on sticky header.', 'porto' ),
					'default'  => '',
					'validate' => 'color',
					'required' => array( 
						array( 'search-layout', 'equals', array( 'large', 'advanced' ) ),
						array( 'header-type-select', 'not', 'header_builder_p' ), 
					),
				),
				array(
					'id'       => 'sticky-searchform-toggle-color',
					'type'     => 'link_color',
					'title'    => __( 'Toggle Text Color', 'porto' ),
					'subtitle' => __( 'Controls the toggle color on sticky header.', 'porto' ),
					'required' => array(
						array( 'header-type-select', 'not', 'header_builder_p' ),
					),
					'hint'     => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'searchform-toggle-color.gif"/>' ),
					),
					'active'   => false,
				),
			);
			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'id'         => 'header-search-form',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Search Form', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array_merge(
						array(
							array(
								'id'       => 'search-live',
								'type'     => 'switch',
								'title'    => __( 'Live Search', 'porto' ),
								'subtitle' => __( 'This will display quick search results whenever you input characters in the search box.', 'porto' ),
								'default'  => true,
								'hint'     => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'search-live.gif"/>' ),
								),
							),
							array(
								'id'       => 'search-by',
								'type'     => 'button_set',
								'title'    => __( 'Search By', 'porto' ),
								'subtitle' => __( 'This option is only for product search. Allow search by individual items in live search.', 'porto' ),
								'multi'    => true,
								'options'  => array(
									'sku'         => __( 'Search by SKU', 'porto' ),
									'product_tag' => __( 'Search by Product Tag', 'porto' ),
									// 'ct_taxonomy' => __( 'Custom Taxonomy', 'porto' ),
								),
								'required' => array( 'search-live', 'equals', true ),
								'default'  => array( 'sku', 'product_tag' ),
							),
							array(
								'id'    => 'desc_info_search_notice',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Search Form widget.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $header_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
										'i'     => array(
											'class'  => array(),
										),
										'span'  => array(),
										'br'    => array(),
									)
								),
								'class' => 'porto-important-note',
								'required' => array(
									array( 'header-type-select', 'not', 'header_builder_p' ),
								),
							),
							array(
								'id'       => 'show-searchform',
								'type'     => 'switch',
								'title'    => __( 'Show Search Form', 'porto' ),
								'subtitle' => wp_kses(
									/* translators: %s: Builder url */
									__( 'The search form in the mobile header is hidden when the search box is displayed on the Side Navigation of the Mobile Menu. <br><b>Menu > Mobile Menu > Add Search Box</b>.', 'porto' ),
									array(
										'b'  => array(),
										'br' => array(),
									)
								),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
								'required' => array(
									array( 'header-type-select', 'not', 'header_builder_p' ),
								),
								'class'   => 'pt-always-visible',
							),
							array(
								'id'       => 'search-layout',
								'type'     => 'image_select',
								'title'    => __( 'Search Layout', 'porto' ),
								'subtitle' => __( 'Controls the layout of the search forms.', 'porto' ),
								'required' => array( 'show-searchform', 'equals', true ),
								'options'  => array(
									'simple'     => array(
										'title' => __( 'Popup 1', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-popup1.svg',
									),
									'large'     => array(
										'title' => __( 'Popup 2', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-popup2.svg',
									),
									'reveal'     => array(
										'title' => __( 'Reveal', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-reveal.svg',
									),
									'advanced'     => array(
										'title' => __( 'Form', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-advanced.svg',
									),
									'overlay'     => array(
										'title' => __( 'Overlay Popup', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-overlay.svg',
									),
								),
								'default'  => $search_layout_default,
								'class'    => 'pt-always-visible',
							),
							array(
								'id'       => 'show-searchform-mobile',
								'type'     => 'switch',
								'title'    => __( 'Show Search Form on Mobile', 'porto' ),
								'subtitle' => __( 'Display the full open-text field instead of an icon on mobile.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
								'required' => array( 'search-layout', 'contains', 'advanced' ),
								'class'    => 'pt-always-visible',
							),
							array(
								'id'       => 'search-border-radius',
								'type'     => 'switch',
								'title'    => __( 'Border Radius', 'porto' ),
								'required' => array(
									array( 'search-layout', 'equals', array( 'simple', 'large', 'advanced' ) ),
									array( 'header-type-select', 'not', 'header_builder_p' ),
								),
								'default'  => true,
								'hint'     => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'search-border-radius.jpg"/>' ),
								),
								'on'       => __( 'On', 'porto' ),
								'off'      => __( 'Off', 'porto' ),
							),
							array(
								'id'       => 'search-type',
								'type'     => 'button_set',
								'title'    => __( 'Search Content Type', 'porto' ),
								'subtitle' => __( 'Controls the post types that displays in search results.', 'porto' ),
								'required' => array( 'show-searchform', 'equals', true ),
								'options'  => array(
									'all'       => __( 'All', 'porto' ),
									'post'      => __( 'Post', 'porto' ),
									'product'   => __( 'Product', 'porto' ),
									'portfolio' => __( 'Portfolio', 'porto' ),
									'event'     => __( 'Event', 'porto' ),
								),
								'default'  => 'all',
								'class'    => 'pt-always-visible',
							),
							array(
								'id'       => 'search-cats',
								'type'     => 'switch',
								'title'    => __( 'Show Categories', 'porto' ),
								'required' => array(
									array( 'search-type', 'equals', array( 'post', 'product' ) ),
									array( 'header-type-select', 'not', 'header_builder_p' ),
								),
								'default'  => false,
								'hint'     => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'search-cats.jpg"/>' ),
								),
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'search-cats-mobile',
								'type'     => 'switch',
								'title'    => __( 'Show Categories on Mobile', 'porto' ),
								'desc'     => __( 'This option works for only real mobile devices.', 'porto' ),
								'required' => array( 'search-cats', 'equals', true ),
								'default'  => true,
								'hint'     => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'search-cats-mobile.jpg"/>' ),
								),
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'search-sub-cats',
								'type'     => 'switch',
								'title'    => __( 'Show Sub Categories', 'porto' ),
								'subtitle' => __( 'Show categories including subcategory.', 'porto' ),
								'required' => array( 'search-cats', 'equals', true ),
								'default'  => true,
								'hint'     => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'search-sub-cats.jpg"/>' ),
								),
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'    => 'search-placeholder',
								'type'  => 'text',
								'title' => __( 'Search Placeholder', 'porto' ),
								'hint'  => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'search-placeholder.gif"/>' ),
								),
								'required' => array(
									array( 'header-type-select', 'not', 'header_builder_p' ),
								),
							),
						),
						$skin_search_form
					),
				);
			} else {
				$this->sections[] = array(
					'id'         => 'header-search-form',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Search Form', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array_merge(
						array(
							array(
								'id'       => 'search-live',
								'type'     => 'switch',
								'title'    => __( 'Live Search', 'porto' ),
								'subtitle' => __( 'This will display quick search results whenever you input characters in the search box.', 'porto' ),
								'default'  => true,
								'hint'     => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'search-live.gif"/>' ),
								),
							),
							array(
								'id'       => 'search-by',
								'type'     => 'button_set',
								'title'    => __( 'Search By', 'porto' ),
								'subtitle' => __( 'This option is only for product search. Allow search by individual items in live search.', 'porto' ),
								'multi'    => true,
								'options'  => array(
									'sku'         => __( 'Search by SKU', 'porto' ),
									'product_tag' => __( 'Search by Product Tag', 'porto' ),
									// 'ct_taxonomy' => __( 'Custom Taxonomy', 'porto' ),
								),
								'required' => array( 'search-live', 'equals', true ),
								'default'  => array( 'sku', 'product_tag' ),
							),
							array(
								'id'    => 'desc_info_search_notice',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Search Form widget.', 'porto' ), $header_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'       => 'show-searchform',
								'type'     => 'switch',
								'title'    => __( 'Show Search Form', 'porto' ),
								'subtitle' => wp_kses(
									/* translators: %s: Builder url */
									__( 'The search form in the mobile header is hidden when the search box is displayed on the Side Navigation of the Mobile Menu. <br><b>Menu > Mobile Menu > Add Search Box</b>.', 'porto' ),
									array(
										'b'  => array(),
										'br' => array(),
									)
								),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
								'required' => array(
									array( 'header-type-select', 'not', 'header_builder_p' ),
								),
								'class'   => 'pt-always-visible',
							),
							array(
								'id'       => 'search-layout',
								'type'     => 'image_select',
								'title'    => __( 'Search Layout', 'porto' ),
								'subtitle' => __( 'Controls the layout of the search forms.', 'porto' ),
								'required' => array( 'show-searchform', 'equals', true ),
								'options'  => array(
									'simple'     => array(
										'title' => __( 'Popup 1', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-popup1.svg',
									),
									'large'     => array(
										'title' => __( 'Popup 2', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-popup2.svg',
									),
									'reveal'     => array(
										'title' => __( 'Reveal', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-reveal.svg',
									),
									'advanced'     => array(
										'title' => __( 'Form', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-advanced.svg',
									),
									'overlay'     => array(
										'title' => __( 'Overlay Popup', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/svg/search-overlay.svg',
									),
								),
								'default'  => $search_layout_default,
							),
							array(
								'id'       => 'search-type',
								'type'     => 'button_set',
								'title'    => __( 'Search Content Type on Searchform of Mobile Menu', 'porto' ),
								'subtitle' => __( 'Controls the post types that displays in search results. Please refer to Theme Option > Menu > Mobile Menu(Side Navigation) > Add Search box', 'porto' ),
								// 'required' => array( 'show-searchform', 'equals', true ),
								'options'  => array(
									'all'       => __( 'All', 'porto' ),
									'post'      => __( 'Post', 'porto' ),
									'product'   => __( 'Product', 'porto' ),
									'portfolio' => __( 'Portfolio', 'porto' ),
									'event'     => __( 'Event', 'porto' ),
								),
								'default'  => 'all',
							),
							array(
								'id'       => 'show-searchform-mobile',
								'type'     => 'switch',
								'title'    => __( 'Show Search Form on Mobile', 'porto' ),
								'subtitle' => __( 'Display the full open-text field instead of an icon on mobile.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
								'required' => array( 'search-layout', 'contains', 'advanced' ),
							),
						),
						$skin_search_form
					)
				);
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Sticky Header', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'    => 'desc_info_sticky_header_notice',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $header_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
									'i'     => array(
										'class'  => array(),
									),
									'span'  => array(),
									'br'    => array(),
								)
							),
							'class' => 'porto-important-note',
						),						
						array(
							'id'        => 'enable-sticky-header',
							'type'      => 'switch',
							'title'     => __( 'Enable Sticky Header', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
							'class'     => 'pt-always-visible',
						),
						array(
							'id'        => 'enable-sticky-header-tablet',
							'type'      => 'switch',
							'title'     => __( 'Enable on Tablet (width < 992px)', 'porto' ),
							//'required'  => array( 'enable-sticky-header', 'equals', true ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
							'class'     => 'pt-always-visible',
						),
						array(
							'id'        => 'enable-sticky-header-mobile',
							'type'      => 'switch',
							'title'     => __( 'Enable on Mobile (width <= 480)', 'porto' ),
							//'required'  => array( 'enable-sticky-header-tablet', 'equals', true ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
							'class'     => 'pt-always-visible',
						),
						array(
							'id'      => 'sticky-header-effect',
							'type'    => 'button_set',
							'title'   => __( 'Sticky Header Effect', 'porto' ),
							'class'   => 'pt-always-visible',
							'options' => array(
								''         => array(
									'label' => __( 'None', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-effect-none.gif"/>' ),
									),
								),
								'reveal'   => array(
									'label' => __( 'Reveal', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-effect-reveal.gif"/>' ),
									),
								),
								'scrollup' => array(
									'label' => __( 'Scroll Up', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-effect-scrollup.gif"/>' ),
									),
								),
							),
							'default' => '',
						),
						array(
							'id'      => 'show-sticky-logo',
							'type'    => 'switch',
							'title'   => __( 'Show Logo', 'porto' ),
							//'required' => array( 'enable-sticky-header', 'equals', true ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'change-header-logo',
							'type'    => 'switch',
							'title'   => __( 'Change Logo Size on Sticky Header', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'change-header-logo.gif"/>' ),
							),
							'class'   => 'pt-always-visible',
						),
						array(
							'id'      => 'show-sticky-searchform',
							'type'    => 'switch',
							'title'   => __( 'Show Search Form', 'porto' ),
							'subtitle'    => __( 'If header type is 1, 4, 9, 13, 14 or header builder', 'porto' ),
							//'required' => array( 'enable-sticky-header', 'equals', true ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
							'hint'    => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-sticky-searchform.gif"/>' ),
							),
						),
						array(
							'id'      => 'show-sticky-minicart',
							'type'    => 'switch',
							'title'   => __( 'Show Mini Cart', 'porto' ),
							'subtitle'    => __( 'If header type is 1, 4, 9, 13, 14, 17 or header builder', 'porto' ),
							//'required' => array( 'enable-sticky-header', 'equals', true ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
							'hint'    => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-sticky-minicart.gif"/>' ),
							),
						),
						array(
							'id'      => 'show-sticky-menu-custom-content',
							'type'    => 'switch',
							'title'   => __( 'Show Menu Custom Content', 'porto' ),
							'subtitle'    => __( 'If header type is 1, 4, 13, 14, 17 or header builder', 'porto' ),
							//'required' => array( 'enable-sticky-header', 'equals', true ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
							'hint'    => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-sticky-menu-cc.gif"/>' ),
							),
						),
						array(
							'id'      => 'show-sticky-contact-info',
							'type'    => 'switch',
							'title'   => __( 'Show Wishlist / Account', 'porto' ),
							'subtitle'    => __( 'Determines to show woocommerce icon on sticky header of header preset.', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
							'hint'    => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-sticky-contact-info.gif"/>' ),
							),
						),
						array(
							'id'       => 'desc_info_sticky_header',
							'type'     => 'info',
							'title'    => __( 'Styling', 'porto' ),
							'notice'   => false,
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'sticky-header-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the sticky header\'s background settings', 'porto' ),
							'default'  => array(
								'background-color' => '#ffffff',
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-bg.gif"/>' ),
							),
						),
						array(
							'id'       => 'sticky-header-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Sticky Header Background Gradient', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'sticky-header-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Sticky Header Background Gradient Color', 'porto' ),
							'required' => array( 'sticky-header-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '#f6f6f6',
								'to'   => '#ffffff',
							),
						),
						array(
							'id'       => 'sticky-header-opacity',
							'type'     => 'text',
							'title'    => __( 'Sticky Header Background Opacity', 'porto' ),
							'default'  => '100%',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-opacity.jpg"/>' ),
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'mainmenu-wrap-padding-sticky',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Sticky Header Padding', 'porto' ),
							'subtitle' => __( 'Controls the padding of header left, center and right parts in the sticky header.', 'porto' ),
							// 'default'  => array(
							// 	'padding-top'    => 8,
							// 	'padding-bottom' => 8,
							// 	'padding-left'   => 0,
							// 	'padding-right'  => 0,
							// ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-wrap-ps.gif"/>' ),
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'class'     => 'pt-always-visible',
						),
					),
				);
			} else {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Sticky Header', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'        => 'enable-sticky-header',
							'type'      => 'switch',
							'title'     => __( 'Enable Sticky Header', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'        => 'enable-sticky-header-tablet',
							'type'      => 'switch',
							'title'     => __( 'Enable on Tablet (width < 992px)', 'porto' ),
							//'required'  => array( 'enable-sticky-header', 'equals', true ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'        => 'enable-sticky-header-mobile',
							'type'      => 'switch',
							'title'     => __( 'Enable on Mobile (width <= 480)', 'porto' ),
							//'required'  => array( 'enable-sticky-header-tablet', 'equals', true ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'      => 'sticky-header-effect',
							'type'    => 'button_set',
							'title'   => __( 'Sticky Header Effect', 'porto' ),
							'options' => array(
								''         => array(
									'label' => __( 'None', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-effect-none.gif"/>' ),
									),
								),
								'reveal'   => array(
									'label' => __( 'Reveal', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-effect-reveal.gif"/>' ),
									),
								),
								'scrollup' => array(
									'label' => __( 'Scroll Up', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-effect-scrollup.gif"/>' ),
									),
								),
							),
							'default' => '',
						),
						array(
							'id'       => 'desc_info_sticky_header',
							'type'     => 'info',
							'title'    => __( 'Styling', 'porto' ),
							'notice'   => false,
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'sticky-header-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the sticky header\'s background settings', 'porto' ),
							'default'  => array(
								'background-color' => '#ffffff',
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-bg.gif"/>' ),
							),
						),
						array(
							'id'       => 'sticky-header-opacity',
							'type'     => 'text',
							'title'    => __( 'Sticky Header Background Opacity', 'porto' ),
							'default'  => '100%',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-header-opacity.jpg"/>' ),
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'mainmenu-wrap-padding-sticky',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Sticky Header Padding', 'porto' ),
							'subtitle' => __( 'Controls the padding of header left, center and right parts in the sticky header.', 'porto' ),
							// 'default'  => array(
							// 	'padding-top'    => 8,
							// 	'padding-bottom' => 8,
							// 	'padding-left'   => 0,
							// 	'padding-right'  => 0,
							// ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-wrap-ps.gif"/>' ),
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
					),
				);
			}

			if ( class_exists( 'WooCommerce' ) ) { // Header > cart
				$cart_skin_options = array(
					array(
						'id'       => 'minicart-text',
						'type'     => 'text',
						'title'    => __( 'Mini Cart Text', 'porto' ),
						'subtitle' => __( 'Controls label in the type displayed with the text.', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'minicart-text.gif"/>' ),
						),
					),
					array(
						'id'       => 'minicart-quantity',
						'type'     => 'switch',
						'title'    => __( 'Show Quantity input', 'porto' ),
						'subtitle' => __( 'Control the quantity of products displayed in the mini cart popup or cart off canvas.', 'porto' ),
						'default'  => false,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'minicart-quantity.gif"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'minicart-icon-font-size',
						'type'     => 'text',
						'title'    => __( 'Icon Font Size', 'porto' ),
						'subtitle' => __( 'Controls the font size for the mini cart icon. Enter value including any valid CSS unit, ex: 30px.', 'porto' ),
						'default'  => '',
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'       => 'minicart-icon-color',
						'type'     => 'color',
						'title'    => __( 'Color', 'porto' ),
						'subtitle' => __( 'Controls the color of cart icon and text.', 'porto' ),
						'default'  => '#999',
						'validate' => 'color',
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
						'selector'       => array(
							'node' => '#header',
						),
					),
					array(
						'id'       => 'minicart-item-color',
						'type'     => 'color',
						'title'    => __( 'Count Badge Color', 'porto' ),
						'subtitle' => __( 'Controls the text color for the mini cart item count.', 'porto' ),
						'default'  => '',
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'minicart-item-color.gif"/>' ),
						),
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
						'validate' => 'color',
					),
					array(
						'id'       => 'minicart-item-bg-color',
						'type'     => 'color',
						'title'    => __( 'Count Badge Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color for the mini cart item count.', 'porto' ),
						'default'  => '',
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'minicart-item-bg-color.gif"/>' ),
						),
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
						'validate' => 'color',
					),
					array(
						'id'       => 'minicart-popup-border-color',
						'type'     => 'color',
						'title'    => __( 'Content Border Color', 'porto' ),
						'subtitle' => __( 'Controls the border color of cart content.', 'porto' ),
						'default'  => '',
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'minicart-popup-border-color.gif"/>' ),
						),
						'validate' => 'color',
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'       => 'desc_info_cart_sticky',
						'type'     => 'info',
						'title'    => __( 'On Sticky Header', 'porto' ),
						'notice'   => false,
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'       => 'sticky-minicart-icon-color',
						'type'     => 'color',
						'title'    => __( 'Color', 'porto' ),
						'subtitle' => __( 'Controls the color of cart icon and text on sticky header.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'       => 'sticky-minicart-item-color',
						'type'     => 'color',
						'title'    => __( 'Count Badge Color', 'porto' ),
						'subtitle' => __( 'Controls the text color of mini cart item count on sticky header.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'       => 'sticky-minicart-item-bg-color',
						'type'     => 'color',
						'title'    => __( 'Count Badge Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color of mini cart item count on sticky header.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
					array(
						'id'       => 'sticky-minicart-popup-border-color',
						'type'     => 'color',
						'title'    => __( 'Content Border Color', 'porto' ),
						'subtitle' => __( 'Controls the border color of cart popup on sticky header.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
						'required' => array(
							array( 'header-type-select', 'not', 'header_builder_p' ),
						),
					),
				);
				if ( $this->legacy_mode ) {
					$this->sections[] = array(
						'id'         => 'header-woocommerce',
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'WooCommerce', 'porto' ),
						'fields'     => array_merge(
							array(
								array(
									'id'    => 'desc_info_header_woocommerce_notice',
									'type'  => 'info',
									'desc'  => wp_kses(
										/* translators: %s: Builder url */
										sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Mini-Cart, Wishlist, Account widget.', 'porto' ), $header_url ),
										array(
											'strong' => array(),
											'b'      => array(),
											'a'      => array(
												'href'   => array(),
												'target' => array(),
												'class'  => array(),
											),
										)
									),
									'class' => 'porto-important-note',
								),
								array(
									'id'       => 'wl-offcanvas',
									'type'     => 'switch',
									'title'    => __( 'Show Off-Canvas Wishlist', 'porto' ),
									'subtitle' => __( 'Controls to show the wishlist dropdown as off canvas.', 'porto' ),
									'default'  => false,
									'hint'     => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'wl-offcanvas.jpg"/>' ),
									),
									'on'       => __( 'Yes', 'porto' ),
									'off'      => __( 'No', 'porto' ),
									'required' => array(
										array( 'header-type-select', 'equals', 'header_builder' ),
									),
								),
								array(
									'id'       => 'desc_info_header_account',
									'type'     => 'info',
									'title'    => __( 'Account Menu', 'porto' ),
									'notice'   => false,
									'required' => array(
										array( 'header-type-select', 'equals', 'header_builder' ),
									),
								),
								array(
									'id'        => 'show-account-dropdown',
									'type'      => 'switch',
									'title'     => __( 'Show Account Dropdown', 'porto' ),
									'subtitle'  => __( 'When the user is logged in, Menu that is located in the Account Menu will be shown.', 'porto' ),
									'default'   => false,
									'hint'      => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-account-dropdown.jpg"/>' ),
									),
									'on'        => __( 'Yes', 'porto' ),
									'off'       => __( 'No', 'porto' ),
									'transport' => 'refresh',
									'required'  => array(
										array( 'header-type-select', 'equals', 'header_builder' ),
									),
								),
								array(
									'id'             => 'account-menu-font',
									'type'           => 'typography',
									'title'          => __( 'Account Dropdown Font', 'porto' ),
									'subtitle'       => __( 'Controls the typography for account dropdown menu.', 'porto' ),
									'google'         => true,
									'subsets'        => false,
									'font-style'     => false,
									'text-align'     => false,
									'color'          => false,
									'letter-spacing' => true,
									'compiler'       => true,
									'default'        => array(
										'google'      => true,
										'font-weight' => '400',
										'font-family' => '',
										'font-size'   => '11px',
										'line-height' => '16.5px',
									),
									'required'       => array( 'show-account-dropdown', 'equals', true ),
								),
								array(
									'id'       => 'account-dropdown-bgc',
									'type'     => 'color',
									'title'    => __( 'Background Color', 'porto' ),
									'subtitle' => __( 'Controls the background color for account dropdown.', 'porto' ),
									'default'  => '#ffffff',
									'validate' => 'color',
									'required' => array( 'show-account-dropdown', 'equals', true ),
								),
								array(
									'id'       => 'account-dropdown-hbgc',
									'type'     => 'color',
									'title'    => __( 'Hover Background Color', 'porto' ),
									'subtitle' => __( 'Controls the background color for account dropdown item on hover.', 'porto' ),
									'default'  => '',
									'validate' => 'color',
									'required' => array( 'show-account-dropdown', 'equals', true ),
								),
								array(
									'id'       => 'account-dropdown-lc',
									'type'     => 'link_color',
									'active'   => false,
									'title'    => __( 'Link Color', 'porto' ),
									'default'  => array(
										'regular' => '#777777',
										'hover'   => '#777777',
									),
									'required' => array( 'show-account-dropdown', 'equals', true ),
								),
								array(
									'id'     => 'desc_info_header_cart',
									'type'   => 'info',
									'title'  => __( 'Mini Cart', 'porto' ),
									'notice' => false,
								),
								array(
									'id'      => 'minicart-type',
									'type'    => 'image_select',
									'title'   => __( 'Mini Cart Type', 'porto' ),
									'options' => array(
										'none'     => array(
											'title' => __( 'None', 'porto' ),
											'img'   => PORTO_OPTIONS_URI . '/svg/cart-none.svg',
										),
										'simple'     => array(
											'title' => __( 'Simple', 'porto' ),
											'img'   => PORTO_OPTIONS_URI . '/svg/cart-simple.svg',
										),
										'minicart-arrow-alt'     => array(
											'title' => __( 'Arrow Alt', 'porto' ),
											'img'   => PORTO_OPTIONS_URI . '/svg/cart-arrow-alt.svg',
										),
										'minicart-inline'     => array(
											'title' => __( 'Text', 'porto' ),
											'img'   => PORTO_OPTIONS_URI . '/svg/cart-text.svg',
										),
										'minicart-text'     => array(
											'title' => __( 'Icon & Text', 'porto' ),
											'img'   => PORTO_OPTIONS_URI . '/svg/cart-icon-text.svg',
										),
									),
									'required' => array(
										array( 'header-type-select', 'not', 'header_builder_p' ),
									),
									'default' => $minicart_type,
								),
								array(
									'id'       => 'minicart-icon',
									'type'     => 'text',
									'title'    => __( 'Mini Cart Icon', 'porto' ),
									'subtitle' => wp_kses(
										__( 'Inputs the custom mini cart icon. ex: <a href="https://www.portotheme.com/wordpress/porto/shortcodes/icons/" target="_blank">porto-icon-cart-thick</a>', 'porto' ),
										array(
											'a' => array(
												'href'   => array(),
												'target' => array(),
											),
										)
									),
									'hint'     => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'minicart-icon.gif"/>' ),
									),
									'default'  => 'porto-icon-cart-thick',
									'required' => array( 'minicart-type', 'equals', array( 'simple', 'minicart-arrow-alt', 'minicart-inline', 'minicart-text' ) ),
								),
								array(
									'id'       => 'minicart-content',
									'type'     => 'image_select',
									'title'    => __( 'Mini Cart Content Type', 'porto' ),
									'options'  => array(
										''          => array(
											'title' => __( 'Popup', 'porto' ),
											'img'   => PORTO_OPTIONS_URI . '/svg/cart-popup.svg',
										),
										'offcanvas' => array(
											'title' => __( 'Off Canvas', 'porto' ),
											'img'   => PORTO_OPTIONS_URI . '/svg/cart-offcanvas.svg',
										),
									),
									'default'  => '',
									'required' => array( 'minicart-type', 'equals', array( 'simple', 'minicart-arrow-alt', 'minicart-inline', 'minicart-text' ) ),
								),
							),
							$cart_skin_options
						),
					);
				} else {
					$this->sections[] = array(
						'id'         => 'header-woocommerce',
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'WooCommerce', 'porto' ),
						'fields'     => array_merge(
							array(
								array(
									'id'    => 'desc_info_header_woocommerce_notice',
									'type'  => 'info',
									'desc'  => wp_kses(
										/* translators: %s: Builder url */
										sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Mini-Cart widget.', 'porto' ), $header_url ),
										array(
											'strong' => array(),
											'b'      => array(),
											'a'      => array(
												'href'   => array(),
												'target' => array(),
												'class'  => array(),
											),
										)
									),
									'class' => 'porto-important-note',
								),
							),
							$cart_skin_options
						),
					);
				}
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'id'         => 'skin-header',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Styling', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'     => 'desc_info_header_builder',
							'type'   => 'info',
							'title'  => wp_kses(
								sprintf(
									/* translators: %s: Header Builder url */
									__( 'Go to <a href="%s" class="goto-header-builder">Header Builder</a>', 'porto' ),
									esc_url(
										add_query_arg(
											array(
												'autofocus' => array(
													'section' => 'porto_header_layouts',
												),
												'url' => home_url(),
											),
											admin_url( 'customize.php' )
										)
									)
								),
								array(
									'a' => array(
										'href'  => array(),
										'class' => array(),
									),
								)
							),
							'class'  => 'field_move',
							'notice' => false,
						),
						array(
							'id'    => 'desc_info_skin_header_notice',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $header_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
									'i'     => array(
										'class'  => array(),
									),
									'span'  => array(),
									'br'    => array(),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'     => 'desc_info_header_wrapper',
							'type'   => 'info',
							'title'  => __( 'Header Wrapper', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'header-wrap-bg',
							'type'     => 'background',
							'title'    => __( 'Header Wrapper Background', 'porto' ),
							'subtitle' => __( 'Controls the header wrapper background settings.', 'porto' ),
							'default'  => array(
								'background-color' => '',
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-wrap-bg-color.gif"/>' ),
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'      => 'header-wrap-bg-gradient',
							'type'    => 'switch',
							'title'   => __( 'Header Wrapper Background Gradient', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-wrap-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'required' => array( 'header-wrap-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'     => 'desc_info_header_top',
							'type'   => 'info',
							'desc'   => __( 'Header Top', 'porto' ),
							'notice' => false,
							'class'  => 'porto-redux-section',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-top-bg-color',
							'type'     => 'color',
							'title'    => __( 'Header Top Background Color', 'porto' ),
							'default'  => '#f4f4f4',
							'validate' => 'color',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-top-bg-color.gif"/>' ),
							),
						),
						array(
							'id'       => 'header-top-height',
							'type'     => 'slider',
							'title'    => __( 'Header Top Height', 'porto' ),
							'subtitle' => __( 'Controls the min height of header top.', 'porto' ),
							'default'  => 30,
							'min'      => 25,
							'max'      => 500,
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-top-height.gif"/>' ),
							),
						),
						array(
							'id'       => 'header-top-font-size',
							'type'     => 'text',
							'title'    => __( 'Header Top Font Size (px)', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-top-font-size.gif"/>' ),
							),
							'default'  => '',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-top-bottom-border',
							'type'     => 'border',
							'all'      => true,
							'style'    => false,
							'title'    => __( 'Bottom Border', 'porto' ),
							'subtitle' => __( 'Controls the bottom border width and color for header top section.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-top-bottom-border.gif"/>' ),
							),
							'default'  => array(
								'border-color' => '#ededed',
								'border-top'   => '1px',
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-top-text-color',
							'type'     => 'color',
							'title'    => __( 'Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the header top section.', 'porto' ),
							'default'  => '#777777',
							'validate' => 'color',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-top-text-color.gif"/>' ),
							),
						),
						array(
							'id'       => 'header-top-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls the color of A tag in the header top section.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-top-link-color.gif"/>' ),
							),
							'default'  => array(
								'regular' => 'var(--porto-primary-color)',
								'hover'   => 'var(--porto-primary-light-5)',
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-top-menu-padding',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Top Menu Padding', 'porto' ),
							'subtitle' => __( 'Controls the padding of top links.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-top-menu-padding.gif"/>' ),
							),
							'default'  => array(
								'padding-top'    => 5,
								'padding-bottom' => 5,
								'padding-left'   => 5,
								'padding-right'  => 5,
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-top-menu-hide-sep',
							'type'     => 'switch',
							'title'    => __( 'Hide separator of Top Navigation', 'porto' ),
							'subtitle' => __( 'Controls if hide the separator between top links items.', 'porto' ),
							'default'  => true,
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-top-menu-hide-sep.gif"/>' ),
							),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'     => 'desc_info_header',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Header:</b> If you use <span>header builder</span>, below options <span>aren\'t</span> necessary. Please use the style options of header builder widgets.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section pt-always-visible',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-bg',
							'type'     => 'background',
							'title'    => __( 'Header Main Background', 'porto' ),
							'subtitle' => __( 'Controls the header background settings', 'porto' ),
							'default'  => array(
								'background-color' => '#ffffff',
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'      => 'header-bg-gradient',
							'type'    => 'switch',
							'title'   => __( 'Header Background Gradient', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Header Background Gradient Color', 'porto' ),
							'required' => array( 'header-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '#f6f6f6',
								'to'   => '#ffffff',
							),
						),
						array(
							'id'       => 'header-text-color',
							'type'     => 'color',
							'title'    => __( 'Header Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the header.', 'porto' ),
							'default'  => '',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-text-color.gif"/>' ),
							),
							'validate' => 'color',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Header Link Color', 'porto' ),
							'subtitle' => __( 'Controls the color of A tag including wishlist, account, search toggle in the header.', 'porto' ),
							'default'  => array(
								'regular' => '#999999',
								'hover'   => '#999999',
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-link-color.gif"/>' ),
							),
							'selector'       => array(
								'node' => '#header',
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'class'    => 'pt-always-visible',
						),
						array(
							'id'      => 'header-top-border',
							'type'    => 'border',
							'all'     => true,
							'style'   => false,
							'title'   => __( 'Top Border of the Header', 'porto' ),
							'default' => array(
								'border-color' => '#ededed',
								'border-top'   => '3px',
							),
							'hint'    => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-top-border.gif"/>' ),
							),
						),
						array(
							'id'       => 'header-margin',
							'type'     => 'spacing',
							'mode'     => 'margin',
							'title'    => __( 'Header Margin', 'porto' ),
							'subtitle' => __( 'Controls the margin of header.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-margin.jpg"/>' ),
							),
							'default'  => array(
								'margin-top'    => 0,
								'margin-bottom' => 0,
								'margin-left'   => 0,
								'margin-right'  => 0,
							),
							'class'    => 'pt-always-visible',
						),
						array(
							'id'       => 'header-main-padding',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Header Main Padding', 'porto' ),
							'subtitle' => __( 'Controls padding top and bottom of the left, center and right parts in the header main.', 'porto' ),
							'left'     => false,
							'right'    => false,
							'units'    => 'px',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-main-padding.jpg"/>' ),
							),
							'default'  => array(
								'padding-top'    => '',
								'padding-bottom' => '',
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'class'    => 'pt-always-visible',
						),
						array(
							'id'       => 'header-main-padding-mobile',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Header Main Padding (window width < 992px)', 'porto' ),
							'subtitle' => __( 'Controls padding top and bottom of the left, center and right parts in the header main on mobile.', 'porto' ),
							'left'     => false,
							'right'    => false,
							'default'  => array(
								'padding-top'    => '',
								'padding-bottom' => '',
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'class'    => 'pt-always-visible',
						),
						array(
							'id'     => 'desc_info_header_bottom',
							'type'   => 'info',
							'title'  => wp_kses(
								sprintf(
									/* translators: %s: Header Builder url */
									__( 'Header Bottom (It can be overrided by Menu > Menu Styling > Main Menu Wrap options)', 'porto' ),
									esc_url(
										add_query_arg(
											array(
												'autofocus' => array(
													'section' => 'porto_header_layouts',
												),
												'url' => home_url(),
											),
											admin_url( 'customize.php' )
										)
									)
								),
								array(
									'a' => array(
										'href'  => array(),
										'title' => array(),
										'class' => array(),
									),
								)
							),
							'notice' => false,
							'required' => array(
								array( 'header-type-select', 'equals', 'header_builder' ),
							),
						),
						array(
							'id'       => 'header-bottom-bg-color',
							'type'     => 'color',
							'title'    => __( 'Header Bottom Background Color', 'porto' ),
							'default'  => '',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-bottom-bg-color.gif"/>' ),
							),
							'validate' => 'color',
							'required' => array(
								array( 'header-type-select', 'equals', 'header_builder' ),
							),
						),
						array(
							'id'       => 'header-bottom-container-bg-color',
							'type'     => 'color',
							'title'    => __( 'Header Bottom Container Background Color', 'porto' ),
							'default'  => '',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-bottom-container-bg-color.gif"/>' ),
							),
							'validate' => 'color',
							'required' => array(
								array( 'header-type-select', 'equals', 'header_builder' ),
							),
						),
						array(
							'id'       => 'header-bottom-height',
							'type'     => 'text',
							'title'    => __( 'Header Bottom Height (px)', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-bottom-height.gif"/>' ),
							),
							'default'  => '',
							'required' => array(
								array( 'header-type-select', 'equals', 'header_builder' ),
							),
						),
						array(
							'id'       => 'header-bottom-text-color',
							'type'     => 'color',
							'title'    => __( 'Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the header bottom section.', 'porto' ),
							'default'  => '',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-bottom-text-color.gif"/>' ),
							),
							'validate' => 'color',
							'required' => array(
								array( 'header-type-select', 'equals', 'header_builder' ),
							),
						),
						array(
							'id'       => 'header-bottom-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls the color of A tag in the header bottom section.', 'porto' ),
							'default'  => array(
								'regular' => '',
								'hover'   => '',
							),
							'required' => array(
								array( 'header-type-select', 'equals', 'header_builder' ),
							),
						),
						array(
							'id'     => 'desc_info_behind_header',
							'type'   => 'info',
							'title'  => __( 'Skin option when banner show behind header', 'porto' ),
							'notice' => false,
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-opacity',
							'type'     => 'text',
							'title'    => __( 'Header Opacity', 'porto' ),
							'subtitle' => __( 'Controls the background opacity in the fixed header.', 'porto' ),
							'default'  => '80%',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-opacity.gif"/>' ),
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'searchform-opacity',
							'type'     => 'text',
							'title'    => __( 'Search Form Opacity', 'porto' ),
							'subtitle' => __( 'Controls the search form\'s background opacity in the fixed header.', 'porto' ),
							'default'  => '50%',
							'hint'      => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'searchform-opacity.gif"/>' ),
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'menuwrap-opacity',
							'type'     => 'text',
							'title'    => __( 'Menu Wrap Opacity', 'porto' ),
							'subtitle' => __( 'Controls the main menu section\'s background opacity in the fixed header for some header types.', 'porto' ),
							'default'  => '30%',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-menuwrap_opacity.gif"/>' ),
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'menu-opacity',
							'type'     => 'text',
							'title'    => __( 'Menu Opacity', 'porto' ),
							'subtitle' => __( 'Controls the main menu\'s background opacity in the fixed header.', 'porto' ),
							'default'  => '30%',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-menu_opacity.gif"/>' ),
							),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'header-fixed-show-bottom',
							'type'     => 'switch',
							'title'    => __( 'Show Bottom Border', 'porto' ),
							'subtitle' => __( 'Controls if show bottom border with opacity in the fixed header.', 'porto' ),
							'default'  => false,
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'header-fixed-show-bottom.gif"/>' ),
							),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'     => 'desc_info_side_navigation',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Side Header in Header Type or Customize Header Builder:</b> If you use <span>header builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
						),
						array(
							'id'       => 'side-social-bg-color',
							'type'     => 'color',
							'title'    => __( 'Social Link Background Color', 'porto' ),
							'default'  => '#9e9e9e',
							'validate' => 'color',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'side-social-bg-color.gif"/>' ),
							),
						),
						array(
							'id'       => 'side-social-color',
							'type'     => 'color',
							'title'    => __( 'Social Link Color', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'side-social-color.gif"/>' ),
							),
						),
						array(
							'id'       => 'side-copyright-color',
							'type'     => 'color',
							'title'    => __( 'Copyright Text Color', 'porto' ),
							'default'  => '#777777',
							'validate' => 'color',
							'required' => array(
								array( 'header-type-select', 'not', 'header_builder_p' ),
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'side-copyright-color.gif"/>' ),
							),
						),
					),
				);
			}
			
			require_once PORTO_ADMIN . '/theme_options/menu.php';

			// Logo
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'Simple-Line-Icons-plus',
					'icon_class' => '',
					'title'      => __( 'Logo', 'porto' ),
					'id'         => 'logo-icons',
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'       => 'logo',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Logo', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/logo_black.png',
							),
						),
						array(
							'id'       => 'logo-retina',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Retina Logo', 'porto' ),
							'subtitle' => __( 'It will be displayed for only retina displays which pixel ratio is greater than one.', 'porto' ),
						),
						array(
							'id'       => 'sticky-logo',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Logo on Sticky Header', 'porto' ),
						),
						array(
							'id'       => 'sticky-logo-retina',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Retina Logo on Sticky Header', 'porto' ),
							'subtitle' => __( 'It will be displayed for only retina displays which pixel ratio is greater than one.', 'porto' ),
						),
						array(
							'id'       => 'logo-retina-width',
							'type'     => 'text',
							'title'    => __( 'Default Logo Width', 'porto' ),
							'subtitle' => __( 'If retina logo is uploaded, please input the default logo width. unit: px', 'porto' ),
						),
						array(
							'id'       => 'logo-retina-height',
							'type'     => 'text',
							'title'    => __( 'Default Logo Height', 'porto' ),
							'subtitle' => __( 'If retina logo is uploaded, please input the default logo height. unit: px', 'porto' ),
						),
						array(
							'id'       => 'logo-width',
							'type'     => 'text',
							'title'    => __( 'Logo Max Width (px)', 'porto' ),
							'default'  => '170',
						),
						array(
							'id'      => 'logo-width-wide',
							'type'    => 'text',
							'title'   => __( 'Logo Max Width on Wide Screen', 'porto' ),
							'default' => '250',
						),
						array(
							'id'      => 'logo-width-tablet',
							'type'    => 'text',
							'title'   => __( 'Logo Max Width on Tablet', 'porto' ),
							'default' => '110',
						),
						array(
							'id'      => 'logo-width-mobile',
							'type'    => 'text',
							'title'   => __( 'Logo Max Width on Mobile', 'porto' ),
							'default' => '110',
						),
						array(
							'id'      => 'logo-width-sticky',
							'type'    => 'text',
							'title'   => __( 'Logo Max Width on Sticky Header', 'porto' ),
							'default' => '80',
						),
						array(
							'id'     => 'desc_info_logo_overlay',
							'type'   => 'info',
							'title'  => __( 'Logo Overlay', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'logo-overlay',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Logo Overlay', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'logo-overlay.gif"/>' ),
							),
						),
						array(
							'id'      => 'logo-overlay-width',
							'type'    => 'text',
							'title'   => __( 'Logo Overlay Max Width', 'porto' ),
							'default' => '250',
						),
						array(
							'id'     => 'desc_info_favicon',
							'type'   => 'info',
							'title'  => __( 'Icons', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'favicon',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Favicon', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/favicon.png',
							),
						),
						array(
							'id'       => 'icon-iphone',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Apple iPhone Icon', 'porto' ),
							'desc'     => __( 'Icon for Apple iPhone (60px X 60px)', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/apple-touch-icon.png',
							),
						),
						array(
							'id'       => 'icon-iphone-retina',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Apple iPhone Retina Icon', 'porto' ),
							'desc'     => __( 'Icon for Apple iPhone Retina (120px X 120px)', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/apple-touch-icon_120x120.png',
							),
						),
						array(
							'id'       => 'icon-ipad',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Apple iPad Icon', 'porto' ),
							'desc'     => __( 'Icon for Apple iPad (76px X 76px)', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/apple-touch-icon_76x76.png',
							),
						),
						array(
							'id'       => 'icon-ipad-retina',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Apple iPad Retina Icon', 'porto' ),
							'desc'     => __( 'Icon for Apple iPad Retina (152px X 152px)', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/apple-touch-icon_152x152.png',
							),
						),
					),
				),
				$options_style
			);

			// Breadcrumbs Settings
			$this->sections[] = $this->add_customizer_field(
				array(
					'id'         => 'header-breadcrumb',
					'icon'       => 'Simple-Line-Icons-link',
					'icon_class' => '',
					'title'      => __( 'Breadcrumbs', 'porto' ),
				),
				$options_style
			);

			$this->sections[] = array(
				'subsection' => true,
				'title'      => __( 'Breadcrumbs', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'    => 'desc_info_bredcrumb',
						'type'  => 'info',
						'desc'  => wp_kses(
							sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> You can build the Page Header with <a href="%1$s" target="_blank">Block Builder</a>. Some below options might be overrided because the priority of the <b>Page Header</b> widget option is <b>higher</b>.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $block_url ),
							array(
								'strong' => array(),
								'b'      => array(),
								'a'      => array(
									'href'   => array(),
									'target' => array(),
									'class'  => array(),
								),
								'i'     => array(
									'class'  => array(),
								),
								'span'  => array(),
								'br'    => array(),
							)
						),
						'class' => 'porto-important-note',
					),
					array(
						'id'         => 'breadcrumbs-type',
						'type'       => 'image_select',
						'full_width' => true,
						'title'      => __( 'Breadcrumbs Type', 'porto' ),
						'options'    => $porto_breadcrumbs_type,
						'default'    => '1',
					),
					array(
						'id'       => 'show-pagetitle',
						'type'     => 'switch',
						'title'    => __( 'Show Page Title', 'porto' ),
						'subtitle' => __( 'Please select "YES" to show the page title in the breacrumb.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-pagetitle.jpg"/>' ),
						),
					),
					array(
						'id'       => 'pagetitle-archives',
						'type'     => 'switch',
						'title'    => __( 'Show Content Type Name in Singular', 'porto' ),
						'subtitle' => __( 'Show Content Type Name in the breadcrumb of single content type.', 'porto' ),
						'default'  => false,
						'required' => array( 'show-pagetitle', 'equals', '1' ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'pagetitle-parent',
						'type'     => 'switch',
						'title'    => __( 'Show Parent Page Title in Page', 'porto' ),
						'subtitle' => __( 'Show Parent Page title in the breadcrumb of single page.', 'porto' ),
						'default'  => false,
						'required' => array( 'show-pagetitle', 'equals', '1' ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'     => 'desc_info_breadcrumb',
						'type'   => 'info',
						'title'  => __( 'Breadcrumb Path', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'show-breadcrumbs',
						'type'     => 'switch',
						'title'    => __( 'Show Breadcrumbs', 'porto' ),
						'subtitle' => __( 'Please select "YES" to display the breadcrumb path.', 'porto' ),
						'default'  => true,
						'class'    => 'pt-always-visible',
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-breadcrumbs.jpg"/>' ),
						),
					),
					array(
						'id'       => 'show-yoast-breadcrumbs',
						'type'     => 'switch',
						'title'    => __( 'Show Yoast Breadcrumbs Instead of Default', 'porto' ),
						'subtitle' => __( 'Please select "YES" to display the Yoast breadcrumb path instead of default.', 'porto' ),
						'class'    => 'pt-always-visible',
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
						'default'  => false,
						'required'  => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
						),
					),
					array(
						'id'        => 'breadcrumbs-pos',
						'type'      => 'button_set',
						'title'     => __( 'Breadcrumbs Position', 'porto' ),
						'subtitle'  => __( '"Default" is the below of header and "Inner Top" is the top position of main content.', 'porto' ),
						'required'  => array( 'show-breadcrumbs', 'equals', '1' ),
						'options'   => array(
							''          => array(
								'label' => __( 'Default', 'porto' ),
								'hint'  => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-pos.jpg"/>' ),
								),
							),
							'inner_top' => array(
								'label' => __( 'Inner Top', 'porto' ),
								'hint'  => array(
									'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-pos-it.jpg"/>' ),
								),
							),
						),
						'default'   => '',
						'transport' => 'refresh',
					),
					array(
						'id'       => 'breadcrumbs-prefix',
						'type'     => 'text',
						'title'    => __( 'Breadcrumbs Prefix', 'porto' ),
						'subtitle' => __( 'Input the text before the breadcrumb path.', 'porto' ),
						'desc'     => __( 'It will be appeared on the top of path.', 'porto' ),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
						'hint'      => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-prefix.gif"/>' ),
						),
					),
					array(
						'id'       => 'breadcrumbs-disable-single-leaf',
						'type'     => 'switch',
						'title'    => __( 'Hide the leaf path on Breadcrumbs', 'porto' ),
						'class'   => 'pt-always-visible',
						'subtitle' => __( 'Please select "YES" to hide the leaf path in singular page.', 'porto' ),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-disable-single-leaf.jpg"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-blog-link',
						'type'     => 'switch',
						'title'    => __( 'Show Blog Link', 'porto' ),
						'subtitle' => __( 'Please select "YES" to insert the permalink of the blog page in single post page.', 'porto' ),
						'default'  => true,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-blog-link.gif"/>' ),
						),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-shop-link',
						'type'     => 'switch',
						'title'    => __( 'Show Shop Link', 'porto' ),
						'subtitle' => __( 'Please select "YES" to insert permalink of shop page to breadcrumb path in single product page.', 'porto' ),
						'default'  => true,
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-shop-link.gif"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-archives-link',
						'type'     => 'switch',
						'title'    => __( 'Show Custom Post Type Archives Link', 'porto' ),
						'subtitle' => __( 'Please select "YES" to insert the permalink of "Archive Page" to breadcrumb path in single custom post page.', 'porto' ),
						'default'  => true,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-archives-link.gif"/>' ),
						),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-categories',
						'type'     => 'switch',
						'title'    => __( 'Show Categories Link', 'porto' ),
						'subtitle' => __( 'Please select "YES" to display the categories in single page.', 'porto' ),
						'default'  => true,
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-categories.gif"/>' ),
						),
					),
					array(
						'id'       => 'breadcrumbs-delimiter',
						'type'     => 'button_set',
						'title'    => __( 'Breadcrumbs Delimiter', 'porto' ),
						'class'   => 'pt-always-visible',
						'subtitle' => __( 'Select the type of separator between each breadcrumb.', 'porto' ),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
						'options'  => array(
							''            => __( '/', 'porto' ),
							'delimiter-2' => __( '>', 'porto' ),
						),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-delimiter.gif"/>' ),
						),
						'default'  => '',
					),
					array(
						'id'       => 'breadcrumbs-css-class',
						'type'     => 'text',
						'title'    => __( 'Custom CSS Class', 'porto' ),
						'subtitle' => __( 'Input the class to customize the breadcrumb.', 'porto' ),
						'default'  => '',
					),
					array(
						'id'     => 'desc_info_breadcrumb_seo',
						'type'   => 'info',
						'title'  => __( 'Tag of Page Title for SEO', 'porto' ),
						'notice' => false,
						'class'  => 'pt-always-visible',
					),
					array(
						'id'      => 'breadcrumb_tag',
						'type'    => 'button_set',
						'title'   => __( 'Select Tag for Page Title', 'porto' ),
						'options' => array(
							''    => __( 'Default (H1)', 'porto' ),
							'h2'  => __( 'H2', 'porto' ),
							'h3'  => __( 'H3', 'porto' ),
							'h4'  => __( 'H4', 'porto' ),
							'h5'  => __( 'H5', 'porto' ),
							'h6'  => __( 'H6', 'porto' ),
							'div' => __( 'Div', 'porto' ),
						),
						'default' => '',
						'class'   => 'pt-always-visible',
					),
					array(
						'id'       => 'breadcrumb_pages',
						'type'     => 'button_set',
						'title'    => __( 'Select Pages', 'porto' ),
						'subtitle' => __( 'Selected page titles will be displayed as the top tag.', 'porto' ),
						'multi'    => true,
						'options'  => array(
							'pages'    => __( 'General Pages', 'porto' ),
							'shop'     => __( 'Shop', 'porto' ),
							'sproduct' => __( 'Single Product', 'porto' ),
							'archive'  => __( 'Archive Page', 'porto' ),
							'single'   => __( 'Single Page', 'porto' ),
						),
						'required' => array( 'breadcrumb_tag', '!=', '' ),
						'class'    => 'pt-always-visible',
					),
				),
			);

			$this->sections[] = array(
				'id'         => 'skin-breadcrumb',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Breadcrumb Styling', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'    => 'desc_info_bredcrumb_skin',
						'type'  => 'info',
						'desc'  => wp_kses(
							sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> You can build the Page Header with <a href="%1$s" target="_blank">Block Builder</a>. Some below options might be overrided because the priority of the <b>Page Header</b> widget option is <b>higher</b>.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $block_url ),
							array(
								'strong' => array(),
								'b'      => array(),
								'a'      => array(
									'href'   => array(),
									'target' => array(),
									'class'  => array(),
								),
								'i'     => array(
									'class'  => array(),
								),
								'span'  => array(),
								'br'    => array(),
							)
						),
						'class' => 'porto-important-note',
					),
					array(
						'id'       => 'breadcrumbs-bg',
						'type'     => 'background',
						'title'    => __( 'Background', 'porto' ),
						'subtitle' => __( 'Controls the background settings for the breadcrumbs.', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-bg.jpg"/>' ),
						),
					),
					array(
						'id'       => 'breadcrumbs-bg-gradient',
						'type'     => 'switch',
						'title'    => __( 'Background Gradient', 'porto' ),
						'subtitle' => __( 'Controls the background gradient settings for the breadcrumbs.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'subtitle' => __( 'Controls the top and bottom background color of the breadcrumb.', 'porto' ),
						'required' => array( 'breadcrumbs-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '',
							'to'   => '',
						),
					),
					array(
						'id'       => 'breadcrumbs-parallax',
						'type'     => 'switch',
						'title'    => __( 'Enable Background Image Parallax', 'porto' ),
						'subtitle' => __( 'Select "YES" to use a parallax scrolling effect on the background image.', 'porto' ),
						'default'  => false,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-parallax.gif"/>' ),
						),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-parallax-speed',
						'type'     => 'text',
						'title'    => __( 'Parallax Speed', 'porto' ),
						'subtitle' => __( 'Control the parallax scrolling speed on the background image.', 'porto' ),
						'default'  => '1.5',
						'required' => array( 'breadcrumbs-parallax', 'equals', true ),
					),
					array(
						'id'       => 'breadcrumbs-top-border',
						'type'     => 'border',
						'all'      => true,
						'style'    => false,
						'title'    => __( 'Top Border', 'porto' ),
						'subtitle' => __( 'Controls the width and color of top border for breadcrumb.', 'porto' ),
						'default'  => array(
							'border-color' => '#384045',
							'border-top'   => '',
						),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-top-border.gif"/>' ),
						),
					),
					array(
						'id'       => 'breadcrumbs-bottom-border',
						'type'     => 'border',
						'all'      => true,
						'style'    => false,
						'title'    => __( 'Bottom Border', 'porto' ),
						'subtitle' => __( 'Controls the width and color of bottom border for breadcrumb.', 'porto' ),
						'default'  => array(
							'border-color' => '#cccccc',
							'border-top'   => '5px',
						),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-bottom-border.gif"/>' ),
						),
					),
					array(
						'id'       => 'breadcrumbs-padding',
						'type'     => 'spacing',
						'mode'     => 'padding',
						'title'    => __( 'Content Padding', 'porto' ),
						'desc'     => __( 'default: 15 15', 'porto' ),
						'subtitle' => __( 'Controls the padding of breadcrumb.', 'porto' ),
						'left'     => false,
						'right'    => false,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-padding.gif"/>' ),
						),
						'class'    => 'pt-always-visible',
						'default'  => array(
							'padding-top'    => 15,
							'padding-bottom' => 15,
						),
					),
					array(
						'id'     => 'desc_info_page_title',
						'type'   => 'info',
						'desc'   => wp_kses(
							__( '<b>Page Title:</b> If the title <span>isn\'t</span> displayed, please enable Theme Options/Breadcrumbs/Show Page Title.', 'porto' ),
							array(
								'span' => array(),
								'b'    => array(),
							)
						),
						'notice' => false,
						'class'  => 'porto-redux-section',
					),
					array(
						'id'             => 'breadcrumbs-title-font',
						'type'           => 'typography',
						'title'          => __( 'Page Title Font', 'porto' ),
						'google'         => true,
						'subsets'        => false,
						'font-style'     => false,
						'text-align'     => false,
						'color'          => false,
						'letter-spacing' => true,
						'default'        => array(
							'google'         => true,
							'font-weight'    => '',
							'font-family'    => '',
							'font-size'      => '',
							'line-height'    => '',
							'letter-spacing' => '',
						),
						'hint'           => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-title-font.jpg"/>' ),
						),
						'transport'      => 'postMessage',
						'selector'       => array(
							'node' => '.page-top .page-title',
						),
					),
					array(
						'id'       => 'breadcrumbs-title-color',
						'type'     => 'color',
						'title'    => __( 'Page Title Color', 'porto' ),
						'subtitle' => __( 'Controls the title color of breadcrumb.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'     => 'desc_info_page_subtitle',
						'type'   => 'info',
						'desc'   => wp_kses(
							__( '<b>Page Subtitle:</b> If the subtitle <span>isn\'t</span> displayed, please enable View Options/Page Sub Title of Page Meta Options.', 'porto' ),
							array(
								'span' => array(),
								'b'    => array(),
							)
						),
						'notice' => false,
						'class'  => 'porto-redux-section',
					),
					array(
						'id'             => 'breadcrumbs-subtitle-font',
						'type'           => 'typography',
						'title'          => __( 'Page Subtitle Font', 'porto' ),
						'google'         => true,
						'subsets'        => false,
						'font-style'     => false,
						'text-align'     => false,
						'color'          => false,
						'letter-spacing' => true,
						'default'        => array(
							'google'         => true,
							'font-weight'    => '',
							'font-family'    => '',
							'font-size'      => '',
							'line-height'    => '',
							'letter-spacing' => '',
						),
						'transport'      => 'postMessage',
						'hint'           => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-subtitle-font.jpg"/>' ),
						),
						'selector'       => array(
							'node' => '.page-top .page-subtitle',
						),
					),
					array(
						'id'       => 'breadcrumbs-subtitle-color',
						'type'     => 'color',
						'title'    => __( 'Page Sub Title Color', 'porto' ),
						'subtitle' => __( 'Controls the subtitle color of breadcrumb.', 'porto' ),
						'default'  => '#e6e6e6',
						'validate' => 'color',
					),
					array(
						'id'       => 'breadcrumbs-subtitle-margin',
						'type'     => 'spacing',
						'mode'     => 'margin',
						'title'    => __( 'Page Sub Title Margin', 'porto' ),
						'subtitle' => __( 'Controls the margin of breadcrumb subtitle.', 'porto' ),
						'desc'     => __( 'If the subtitle isn\'t displayed, please input to <strong>View Options/Page Sub Title</strong> of Page Meta Options.', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-subtitle-margin.gif"/>' ),
						),
						'default'  => array(
							'margin-top'    => 0,
							'margin-bottom' => 0,
							'margin-left'   => 0,
							'margin-right'  => 0,
						),
					),
					array(
						'id'     => 'desc_info_breadcrumb_path',
						'type'   => 'info',
						'desc'   => wp_kses(
							__( '<b>Breadcrumb Path:</b> If the breadcrumb path <span>isn\'t</span> displayed, please enable Theme Options/Breadcrumbs/Show Breadcrumbs.', 'porto' ),
							array(
								'span' => array(),
								'b'    => array(),
							)
						),
						'notice' => false,
						'class'  => 'porto-redux-section',
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
					),
					array(
						'id'             => 'breadcrumbs-path-font',
						'type'           => 'typography',
						'title'          => __( 'Breadcrumb Path Font', 'porto' ),
						'google'         => true,
						'subsets'        => false,
						'font-style'     => false,
						'text-align'     => false,
						'color'          => false,
						'letter-spacing' => true,
						'default'        => array(
							'google'         => true,
							'font-weight'    => '',
							'font-family'    => '',
							'font-size'      => '',
							'line-height'    => '',
							'letter-spacing' => '',
						),
						'transport'      => 'postMessage',
						'selector'       => array(
							'node' => '.page-top .breadcrumb',
						),
						'hint'           => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-breadcrumbs.jpg"/>' ),
						),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
					),
					array(
						'id'       => 'breadcrumbs-text-transform',
						'type'     => 'button_set',
						'title'    => __( 'Text Transform', 'porto' ),
						'subtitle' => __( 'Controls the text transform for the Breadcrumb path font.', 'porto' ),
						'options'  => array(
							'none'       => __( 'None', 'porto' ),
							'capitalize' => __( 'Capitalize', 'porto' ),
							'uppercase'  => __( 'Uppercase', 'porto' ),
							'lowercase'  => __( 'Lowercase', 'porto' ),
							'initial'    => __( 'Initial', 'porto' ),
						),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
					),
					array(
						'id'       => 'breadcrumbs-delimiter-font',
						'type'     => 'text',
						'title'    => __( 'Delimiter Font Size', 'porto' ),
						'subtitle' => __( 'Controls the font size of delimiter. Enter value including any valid CSS unit, ex: 30px.', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-delimiter.gif"/>' ),
						),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
					),
					array(
						'id'       => 'breadcrumbs-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'subtitle' => __( 'Controls the text color of breadcrumb.', 'porto' ),
						'default'  => '#777777',
						'validate' => 'color',
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-text-color.jpg"/>' ),
						),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
					),
					array(
						'id'       => 'breadcrumbs-link-color',
						'type'     => 'color',
						'title'    => __( 'Link Color', 'porto' ),
						'subtitle' => __( 'Controls the hyperlink color of breadcrumb.', 'porto' ),
						'default'  => 'var(--porto-primary-color)',
						'validate' => 'color',
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-link-color.jpg"/>' ),
						),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
					),
					array(
						'id'       => 'breadcrumbs-path-margin',
						'type'     => 'spacing',
						'mode'     => 'margin',
						'title'    => __( 'Path Margin', 'porto' ),
						'subtitle' => __( 'Controls the margin of breadcrumb path.', 'porto' ),
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'breadcrumbs-path-margin.jpg"/>' ),
						),
						'required' => array(
							array( 'show-breadcrumbs', 'equals', '1' ),
							array( 'show-yoast-breadcrumbs', 'not', true ),
						),
					),
				),
			);

			if ( $this->legacy_mode ) {
				// Footer Settings
				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'footer-settings',
						'icon'       => 'Simple-Line-Icons-arrow-down-circle',
						'icon_class' => '',
						'title'      => __( 'Footer', 'porto' ),
					),
					$options_style
				);
				$this->sections[] = array(
					'subsection' => true,
					'title'      => __( 'Footer', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'    => 'desc_info_footer_notice',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Footer</a> Builder helps you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $footer_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
									'i'     => array(
										'class'  => array(),
									),
									'span'  => array(),
									'br'    => array(),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'         => 'footer-type',
							'type'       => 'image_select',
							'full_width' => true,
							'title'      => __( 'Footer Type', 'porto' ),
							'subtitle'   => __( 'Determine how to set the layout of the footer main. This option isn\'t available to <strong>Footer Builder</strong>', 'porto' ),
							'options'    => $porto_footer_type,
							'default'    => '1',
						),
						array(
							'id'       => 'footer-customize',
							'type'     => 'switch',
							'title'    => __( 'Customize Footer Columns', 'porto' ),
							'subtitle' => __( 'This setting doesn\'t work for <strong>footer builder</strong>.', 'porto' ),
							'desc'     => __( 'Select "YES" to customize the width of footer widgets.', 'porto' ),
							'default'  => false,
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-customize.jpg"/>' ),
							),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-widget1',
							'type'     => 'select',
							'title'    => __( 'Widget 1', 'porto' ),
							'required' => array( 'footer-customize', 'equals', true ),
							'subtitle' => __( 'Select the custom width of the footer widget 1.', 'porto' ),
							'options'  => $porto_footer_columns,
							'default'  => '',
						),
						array(
							'id'       => 'footer-widget2',
							'type'     => 'select',
							'title'    => __( 'Widget 2', 'porto' ),
							'required' => array( 'footer-customize', 'equals', true ),
							'subtitle' => __( 'Select the custom width of the footer widget 2.', 'porto' ),
							'options'  => $porto_footer_columns,
							'default'  => '',
						),
						array(
							'id'       => 'footer-widget3',
							'type'     => 'select',
							'title'    => __( 'Widget 3', 'porto' ),
							'required' => array( 'footer-customize', 'equals', true ),
							'subtitle' => __( 'Select the custom width of the footer widget 3.', 'porto' ),
							'options'  => $porto_footer_columns,
							'default'  => '',
						),
						array(
							'id'       => 'footer-widget4',
							'type'     => 'select',
							'title'    => __( 'Widget 4', 'porto' ),
							'required' => array( 'footer-customize', 'equals', true ),
							'subtitle' => __( 'Select the custom width of the footer widget 4.', 'porto' ),
							'options'  => $porto_footer_columns,
							'default'  => '',
						),
						array(
							'id'       => 'footer-reveal',
							'type'     => 'switch',
							'title'    => __( 'Show Reveal Effect', 'porto' ),
							'class'   => 'pt-always-visible',
							'desc'     => __( 'Select "YES" to enable reveal effect.', 'porto' ),
							'subtitle' => __( 'This option is allowed to the footer higher than window\'s height.', 'porto' ),
							'default'  => false,
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-reveal.gif"/>' ),
							),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-logo',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Footer Logo', 'porto' ),
							'subtitle' => __( 'This setting doesn\'t work for <strong>footer builder</strong>.', 'porto' ),
							'desc'     => __( 'Upload footer logo which is displayed at the left of footer bottom container.', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/logo_footer.png',
							),
						),
						array(
							'id'       => 'footer-ribbon',
							'type'     => 'text',
							'class'   => 'pt-always-visible',
							'subtitle' => __( 'Please input ribbon text which is displayed at the top and left of the footer container if you want.', 'porto' ),
							'title'    => __( 'Ribbon Text', 'porto' ),
							'default'  => '',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-ribbon.gif"/>' ),
							),
						),
						array(
							'id'       => 'footer-copyright',
							'type'     => 'textarea',
							'title'    => __( 'Copyright', 'porto' ),
							'subtitle' => __( 'This setting doesn\'t work for <strong>footer builder</strong>.', 'porto' ),
							'desc'     => __( 'Input the text that displays in the copyright bar.', 'porto' ),
							/* translators: %s: Current Year */
							'default'  => sprintf( __( '&copy; Copyright %s. All Rights Reserved.', 'porto' ), date( 'Y' ) ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-copyright.gif"/>' ),
							),
						),
						array(
							'id'       => 'footer-copyright-pos',
							'type'     => 'button_set',
							'title'    => __( 'Copyright Position', 'porto' ),
							'subtitle' => __( 'This setting doesn\'t work for <strong>footer builder</strong>.', 'porto' ),
							'desc'     => __( 'Controls the position that shows copyright text at the footer bottom container.', 'porto' ),
							'options'  => array(
								'left'   => __( 'Left', 'porto' ),
								'center' => __( 'Center', 'porto' ),
								'right'  => __( 'Right', 'porto' ),
							),
							'default'  => 'left',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-copyright-pos.gif"/>' ),
							),
						),
						array(
							'id'       => 'show-footer-tooltip',
							'type'     => 'switch',
							'title'    => __( 'Show Tooltip', 'porto' ),
							'subtitle' => __( 'Controls if show tooltip with exclamation mark and tooltip contents on click at the top and right of footer.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-footer-tooltip.gif"/>' ),
							),
						),
						array(
							'id'       => 'footer-tooltip',
							'type'     => 'textarea',
							'title'    => __( 'Tooltip Content', 'porto' ),
							'subtitle' => __( 'Please input tooltip text which is displayed at the footer main container. It you input nothing, you will not see the    tooltip.', 'porto' ),
							'required' => array( 'show-footer-tooltip', 'equals', true ),
						),
						array(
							'id'     => 'desc_info_footer_payment',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Payments:</b> If you use <span>footer builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'      => 'footer-payments',
							'type'    => 'switch',
							'title'   => __( 'Show Payments Logos', 'porto' ),
							'subtitle'    => __( 'Controls if show payment icons at the bottom of the footer.', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
							'hint'    => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-payments.gif"/>' ),
							),
						),
						array(
							'id'       => 'footer-payments-image',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Payments Image', 'porto' ),
							'subtitle' => __( 'Upload the payment image to show.', 'porto' ),
							'required' => array( 'footer-payments', 'equals', '1' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/payments.png',
							),
						),
						array(
							'id'       => 'footer-payments-image-alt',
							'type'     => 'text',
							'title'    => __( 'Payments Image Alt', 'porto' ),
							'subtitle' => __( 'Input the alternative text.', 'porto' ),
							'required' => array( 'footer-payments', 'equals', '1' ),
							'default'  => '',
						),
						array(
							'id'       => 'footer-payments-link',
							'type'     => 'text',
							'title'    => __( 'Payments Link URL', 'porto' ),
							'subtitle' => __( 'Input the permalink of image.', 'porto' ),
							'required' => array( 'footer-payments', 'equals', '1' ),
							'default'  => '',
						),
					),					
				);				
			} else {
				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'footer-settings',
						'icon'       => 'Simple-Line-Icons-arrow-down-circle',
						'icon_class' => '',
						'title'      => __( 'Footer', 'porto' ),
					),
					$options_style
				);
				$this->sections[] = array(
					'subsection' => true,
					'title'      => __( 'Footer', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'    => 'desc_info_footer_notice',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Footer</a> Builder helps you to develop your site easily.', 'porto' ), $footer_url ),
								array(
									'strong' => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'       => 'footer-reveal',
							'type'     => 'switch',
							'title'    => __( 'Show Reveal Effect', 'porto' ),
							'subtitle' => __( 'Select "YES" to enable reveal effect.', 'porto' ),
							'desc'     => __( 'This option is allowed to the footer higher than window\'s height.', 'porto' ),
							'default'  => false,
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-reveal.gif"/>' ),
							),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-ribbon',
							'type'     => 'text',
							'subtitle' => __( 'Please input ribbon text which is displayed at the top and left of the footer container if you want.', 'porto' ),
							'title'    => __( 'Ribbon Text', 'porto' ),
							'default'  => '',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-ribbon.gif"/>' ),
							),
						),
					),					
				);				
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'id'         => 'skin-footer',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Footer Styling', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'    => 'desc_info_skin_footer_notice',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Footer</a> Builder helps you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $footer_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
									'i'     => array(
										'class'  => array(),
									),
									'span'  => array(),
									'br'    => array(),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'     => 'desc_info_footer_top_widget',
							'type'   => 'info',
							'title'  => __( 'For Footer Top Widget', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-top-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the background settings for the footer top widget.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-top-bg.jpg"/>' ),
							),
						),
						array(
							'id'       => 'footer-top-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Enable Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the background gradient settings of the top widget.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-top-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom background color of top widget.', 'porto' ),
							'required' => array( 'footer-top-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'       => 'footer-top-padding',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Padding', 'porto' ),
							'subtitle' => __( 'Controls the padding of top widget.', 'porto' ),
							'left'     => false,
							'right'    => false,
							'hint'      => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-top-padding.gif"/>' ),
							),
						),
						array(
							'id'     => 'desc_info_footer_general_option',
							'type'   => 'info',
							'title'  => __( 'Footer General Options', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the footer background settings.', 'porto' ),
							'default'  => array(
								'background-color' => '#212529',
							),
							'hint'      => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-bg.jpg"/>' ),
							),
						),
						array(
							'id'       => 'footer-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the footer background gradient settings.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom color of background.', 'porto' ),
							'required' => array( 'footer-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'       => 'footer-parallax',
							'type'     => 'switch',
							'title'    => __( 'Enable Background Image Parallax', 'porto' ),
							'subtitle' => __( 'Select "YES" to use a parallax scrolling effect on the background image.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-parallax-speed',
							'type'     => 'text',
							'title'    => __( 'Parallax Speed', 'porto' ),
							'subtitle' => __( 'Control the parallax scrolling speed on the background image.', 'porto' ),
							'default'  => '1.5',
							'required' => array( 'footer-parallax', 'equals', true ),
						),
						array(
							'id'     => 'desc_info_footer_main',
							'type'   => 'info',
							'title'  => __( 'For Footer Main Section which contains footer Widgets', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-main-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the background settings for the footer main section which contains widget areas.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-main-bg.jpg"/>' ),
							),
						),
						array(
							'id'       => 'footer-main-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the footer main background gradient settings.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-main-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom color of footer main background.', 'porto' ),
							'required' => array( 'footer-main-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'       => 'footer-heading-color',
							'type'     => 'color',
							'title'    => __( 'Heading Color', 'porto' ),
							'subtitle' => __( 'Controls the heading color in the footer main section.(h1 - h6)', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-heading-color.jpg"/>' ),
							),
						),
						array(
							'id'       => 'footer-label-color',
							'type'     => 'color',
							'title'    => __( 'Label Color', 'porto' ),
							'subtitle' => __( 'Controls the title color of contact info widget in the footer.', 'porto' ),
							'validate' => 'color',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-label-color.gif"/>' ),
							),
						),
						array(
							'id'       => 'footer-text-color',
							'type'     => 'color',
							'title'    => __( 'Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the footer main section.', 'porto' ),
							'default'  => '#777777',
							'validate' => 'color',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-text-color.jpg"/>' ),
							),
						),
						array(
							'id'       => 'footer-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls normal and hover color of hyperlink in the footer main section.', 'porto' ),
							'default'  => array(
								'regular' => '#777777',
								'hover'   => '#ffffff',
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-link-color.jpg"/>' ),
							),
						),
						array(
							'id'       => 'footer-ribbon-bg-color',
							'type'     => 'color',
							'title'    => __( 'Ribbon Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the footer ribbon.', 'porto' ),
							'desc'     => __( 'This option is useful when <strong>Theme Option/Footer/Ribbon Text</strong> option gets value.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-ribbon.gif"/>' ),
							),
							'default'  => 'var(--porto-primary-color)',
							'validate' => 'color',
							'class'   => 'pt-always-visible',
						),
						array(
							'id'       => 'footer-ribbon-text-color',
							'type'     => 'color',
							'title'    => __( 'Ribbon Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color for the footer ribbon.', 'porto' ),
							'desc'     => __( 'This option is useful when <strong>Theme Option/Footer/Ribbon Text</strong> option gets value.', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
							'class'   => 'pt-always-visible',
						),
						array(
							'id'     => 'desc_info_footer_bottom',
							'type'   => 'info',
							'title'  => __( 'For Footer Bottom', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-bottom-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the background settings for the footer bottom.', 'porto' ),
							'default'  => array(
								'background-color' => '#1c2023',
							),
							'hint'      => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-bottom-bg.jpg"/>' ),
							),
						),
						array(
							'id'       => 'footer-bottom-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the footer bottom background gradient settings.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-bottom-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom background color of the footer bottom.', 'porto' ),
							'required' => array( 'footer-bottom-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'       => 'footer-bottom-text-color',
							'type'     => 'color',
							'title'    => __( 'Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the footer bottom.', 'porto' ),
							'default'  => '#555555',
							'validate' => 'color',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-bottom-text-color.gif"/>' ),
							),
						),
						array(
							'id'       => 'footer-bottom-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls normal and hover color of hyperlink in the footer bottom.', 'porto' ),
							'default'  => array(
								'regular' => '#777777',
								'hover'   => '#ffffff',
							),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-bottom-link-color.gif"/>' ),
							),
						),
						array(
							'id'     => 'desc_info_footer_fixed',
							'type'   => 'info',
							'title'  => __( 'Background Opacity when footer show in fixed position', 'porto' ),
							'notice' => false,
						),
						array(
							'id'      => 'footer-opacity',
							'type'    => 'text',
							'title'   => __( 'Footer Opacity', 'porto' ),
							'default' => '80%',
						),
						array(
							'id'     => 'desc_info_follow_us',
							'type'   => 'info',
							'title'  => __( 'Follow Us Widget', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-social-bg-color',
							'type'     => 'color',
							'title'    => __( 'Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the social links in the footer.', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-social-bg-color.gif"/>' ),
							),
						),
						array(
							'id'       => 'footer-social-link-color',
							'type'     => 'color',
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls the text color for the social links in the footer.', 'porto' ),
							'default'  => '#333333',
							'validate' => 'color',
						),
					),
				);
			} else {
				$this->sections[] = array(
					'id'         => 'skin-footer',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Footer Styling', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'       => 'footer-ribbon-bg-color',
							'type'     => 'color',
							'title'    => __( 'Ribbon Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the footer ribbon.', 'porto' ),
							'desc'     => __( 'This option is useful when <strong>Theme Option/Footer/Ribbon Text</strong> option gets value.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'footer-ribbon.gif"/>' ),
							),
							'default'  => 'var(--porto-primary-color)',
							'validate' => 'color',
						),
						array(
							'id'       => 'footer-ribbon-text-color',
							'type'     => 'color',
							'title'    => __( 'Ribbon Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color for the footer ribbon.', 'porto' ),
							'desc'     => __( 'This option is useful when <strong>Theme Option/Footer/Ribbon Text</strong> option gets value.', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
					),
				);
			}

			// Sidebar
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'Simple-Line-Icons-notebook',
					'icon_class' => 'icon',
					'title'      => __( 'Sidebar', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'       => 'global-sidebar-width',
							'type'     => 'text',
							'title'    => __( 'Sidebar Width ( > 991px )', 'porto' ),
							'subtitle' => __( 'Controls the global sidebar width. Enter value including any valid CSS unit, eg: 25%, 300px.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sidebar-bw.gif"/>' ),
							),
						),
						array(
							'id'       => 'woo-sidebar-width',
							'type'     => 'text',
							'title'    => __( 'Sidebar Width on WooCommerce Pages ( > 991px )', 'porto' ),
							'subtitle' => __( 'Controls the sidebar width of product archive and single product page. Enter value including any valid CSS unit, eg: 25%, 300px.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sidebar-bw.gif"/>' ),
							),
						),
						array(
							'id'      => 'sticky-sidebar',
							'type'    => 'switch',
							'title'   => __( 'Enable Sticky Sidebar', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
							'hint'    => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sticky-sidebar.gif"/>' ),
							),
						),
						array(
							'id'       => 'show-mobile-sidebar',
							'type'     => 'switch',
							'title'    => __( 'Show as Off-Canvas Sidebar on mobile', 'porto' ),
							'subtitle' => __( 'Add toggle button for showing the sidebar on mobile.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-mobile-sidebar.gif"/>' ),
							),
						),
						array(
							'id'       => 'sidebar-bw',
							'type'     => 'text',
							'title'    => __( 'Sidebar Border Width (px)', 'porto' ),
							'subtitle' => __( 'Controls the border size of the sidebar.', 'porto' ),
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sidebar-bw.gif"/>' ),
							),
						),
						array(
							'id'       => 'sidebar-bc',
							'type'     => 'color',
							'title'    => __( 'Sidebar Border Color', 'porto' ),
							'subtitle' => __( 'Controls the border color of the sidebar.', 'porto' ),
							'default'  => '',
							'validate' => 'color',
							'required' => array( 'sidebar-bw', '!=', '' ),
						),
						array(
							'id'       => 'sidebar-pd',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Sidebar Padding (px)', 'porto' ),
							'subtitle' => __( 'Controls the padding of the sidebar.', 'porto' ),
							'units'    => 'px',
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'sidebar-pd.gif"/>' ),
							),
						),
					),
				),
				$options_style
			);

			// Page
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'icon-content',
					'icon_class' => 'porto-icon',
					'title'      => __( 'Page', 'porto' ),
					'fields'     => array(
						array(
							'id'       => 'page-comment',
							'type'     => 'switch',
							'title'    => __( 'Show Comments', 'porto' ),
							'subtitle' => __( 'Show Page Comments and Comments Respond.', 'porto' ),
							'desc'     => __( 'To show comments respond, you should check Page Meta Option <strong>Allow comments</strong>.', 'porto' ),
							'default'  => false,
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'page-comment.jpg"/>' ),
							),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'page-zoom',
							'type'     => 'switch',
							'title'    => __( 'Image Lightbox', 'porto' ),
							'subtitle' => __( 'If you use single & type builder, you should consider the options of builder widgets.', 'porto' ),
							'desc'     => __( 'Turn on to enable the lightbox on single and archive page for the main featured images.', 'porto' ),
							'default'  => true,
							'hint'     => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'page-zoom.jpg"/>' ),
							),
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
						array(
							'id'       => 'page-share',
							'type'     => 'switch',
							'title'    => __( 'Show Social Share Links', 'porto' ),
							'subtitle' => __( 'To show social links, you should check <strong>Default</strong> or <strong>Yes</strong> of <strong>Meta Option Share</strong> and enable <strong>Theme Option/General/Social Share/Show Social Links</strong>.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'page-share-pos',
							'type'     => 'button_set',
							'title'    => __( 'Position', 'porto' ),
							'subtitle' => __( 'Show social links on left or right. To set kind of social links, enable options of <strong>Theme Option/General/Social Share</strong>.', 'porto' ),
							'default'  => '',
							'options'  => array(
								''      => array(
									'label' => __( 'Default', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'page-share-pos.jpg"/>' ),
									),
								),
								'left'  => array(
									'label' => __( 'Float Left', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'page-share-pos-fl.jpg"/>' ),
									),
								),
								'right' => array(
									'label' => __( 'Float Right', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'page-share-pos-fr.jpg"/>' ),
									),
								),
							),
							'required' => array( 'page-share', 'equals', true ),
						),
						array(
							'id'       => 'page-microdata',
							'type'     => 'switch',
							'title'    => __( 'Microdata Rich Snippets', 'porto' ),
							'subtitle' => __( 'To make rich snippets data site wide, you should enable <strong>Microdata Rich Snippets</strong> of <strong>Page Meta Options</strong> and <strong>Microdata Rich Snippets</strong> of <strong>Theme Option/General/Seo</strong>.', 'porto' ),
							'default'  => true,
							'hint'      => array(
								'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'page-microdata.jpg"/>' ),
							),
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
					),
				),
				$options_style
			);

			require_once PORTO_ADMIN . '/theme_options/cpt.php';

			// FAQ
			$faq_options = array(
				'icon'       => 'Simple-Line-Icons-speech',
				'icon_class' => '',
				'title'      => __( 'FAQ', 'porto' ),
				'customizer' => false,
				'fields'     => array(
					array(
						'id'    => 'desc_info_faq',
						'type'  => 'info',
						'desc'  => wp_kses(
							/* translators: %s: Builder url */
							sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $archive_url, $type_url ),
							array(
								'strong' => array(),
								'b'      => array(),
								'a'      => array(
									'href'   => array(),
									'target' => array(),
									'class'  => array(),
								),
							)
						),
						'class' => 'porto-important-note',
					),
					array(
						'id'       => 'enable-faq',
						'type'     => 'switch',
						'title'    => __( 'FAQ Content Type', 'porto' ),
						'default'  => false,
						'hint'     => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'enable-faq.jpg"/>' ),
						),
						'subtitle' => __( 'Please select "Enable" to visit Faq Page.', 'porto' ),
						'on'       => __( 'Enable', 'porto' ),
						'off'      => __( 'Disable', 'porto' ),
					),
					array(
						'id'          => 'faq-slug-name',
						'type'        => 'text',
						'title'       => __( 'Slug Name', 'porto' ),
						'subtitle'    => __( 'If there isn\'t "FAQs Page", Show this slug name as permalink.', 'porto' ),
						'placeholder' => 'faq',
					),
					array(
						'id'          => 'faq-name',
						'type'        => 'text',
						'title'       => __( 'Name', 'porto' ),
						'placeholder' => __( 'FAQs', 'porto' ),
						'subtitle'    => __( 'Show this name in Faqs page and Admin Page.', 'porto' ),
					),
					array(
						'id'          => 'faq-singular-name',
						'type'        => 'text',
						'title'       => __( 'Singular Name', 'porto' ),
						'subtitle'    => __( 'Show individual faqs as this name.', 'porto' ),
						'placeholder' => __( 'FAQ', 'porto' ),
					),
					array(
						'id'          => 'faq-cat-slug-name',
						'type'        => 'text',
						'title'       => __( 'Category Slug Name', 'porto' ),
						'subtitle'    => __( 'Show individual faq categories as this name.', 'porto' ),
						'placeholder' => 'faq_cat',
					),
				),
			);
			if ( $options_style ) {
				$this->sections[] = $faq_options;
			}
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'Simple-Line-Icons-question',
					'icon_class' => '',
					'title'      => __( 'FAQ', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'faq-archive-page',
							'type'  => 'select',
							'data'  => 'page',
							'title' => __( 'FAQs Page', 'porto' ),
						),
						array(
							'id'       => 'faq-title',
							'type'     => 'text',
							'title'    => __( 'Page Title', 'porto' ),
							'subtitle' => __( 'This option isn\'t available to <strong>Archive Builder</strong> Page.', 'porto' ),
							'desc'     => __( 'Only when "FAQ Filter Position" option is the "In Content", this text will be shown.', 'porto' ),
							'default'  => 'Frequently Asked <strong>Questions</strong>',
						),
						array(
							'id'       => 'faq-sub-title',
							'type'     => 'textarea',
							'title'    => __( 'Page Sub Title', 'porto' ),
							'subtitle' => __( 'This option isn\'t available to <strong>Archive Posts Grid</strong> Widget.', 'porto' ),
							'desc'     => __( 'Only when "FAQ Filter Position" option is the "In Content", this text will be shown.', 'porto' ),
							'default'  => '',
						),
						array(
							'id'      => 'faq-archive-layout',
							'type'    => 'image_select',
							'title'   => __( 'FAQ Page Layout', 'porto' ),
							'options' => $page_layouts,
							'default' => 'fullwidth',
						),
						array(
							'id'       => 'faq-archive-sidebar',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar', 'porto' ),
							'required' => array( 'faq-archive-layout', 'equals', $sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'       => 'faq-archive-sidebar2',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar 2', 'porto' ),
							'required' => array( 'faq-archive-layout', 'equals', $both_sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'     => 'desc_info_sort_faq',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Sort Faq Categories:</b> If you use <span>archive builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'       => 'faq-cat-orderby',
							'type'     => 'button_set',
							'title'    => __( 'Sort Categories Order By', 'porto' ),
							'options'  => $porto_categories_orderby,
							'subtitle' => __( 'Sort faq categories by this option.', 'porto' ),
							'default'  => 'name',
						),
						array(
							'id'       => 'faq-cat-order',
							'type'     => 'button_set',
							'title'    => __( 'Sort Order for Categories', 'porto' ),
							'subtitle' => __( 'Sort faq categories ascending or descending by this option.', 'porto' ),
							'options'  => $porto_categories_order,
							'default'  => 'asc',
						),
						array(
							'id'      => 'faq-cat-sort-pos',
							'type'    => 'image_select',
							'title'   => __( 'FAQ Filter Position', 'porto' ),
							'options' => $porto_categories_sort_pos,
							'default' => 'content',
						),
						array(
							'id'     => 'desc_info_sort_faq_item',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Sort Faq Items:</b> If you use <span>archive builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'       => 'faq-orderby',
							'type'     => 'button_set',
							'title'    => __( 'Sort Items Order By', 'porto' ),
							'options'  => array_slice( $porto_categories_orderby, 0, 3 ),
							'subtitle' => __( 'Sort faq items by this option.', 'porto' ),
							'default'  => 'name',
						),
						array(
							'id'       => 'faq-order',
							'type'     => 'button_set',
							'title'    => __( 'Sort Order for Items', 'porto' ),
							'options'  => $porto_categories_order,
							'subtitle' => __( 'Sort faq items ascending or descending by this option.', 'porto' ),
							'default'  => 'asc',
						),
						array(
							'id'     => 'desc_info_faq_pagination',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Faq Pagination:</b> If you use <span>archive builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'       => 'faq-infinite',
							'type'     => 'button_set',
							'title'    => __( 'FAQ Pagination Style', 'porto' ),
							'subtitle' => __( 'Control the pagination type in faq page.', 'porto' ),
							'options'  => array(
								''         => array(
									'label' => __( 'Default', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'faq-infinite.gif"/>' ),
									),
								),
								'ajax'     => array(
									'label' => __( 'Ajax Pagination', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'faq-infinite-ajax.gif"/>' ),
									),
								),
								'infinite' => array(
									'label' => __( 'Infinite Scroll', 'porto' ),
									'hint'  => array(
										'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'faq-infinite-infinite.gif"/>' ),
									),
								),
							),
							'default' => 'infinite',
						),
						array(
							'id'       => 'faq-cat-ft',
							'type'     => 'image_select',
							'title'    => __( 'FAQ Filter Type', 'porto' ),
							'options'  => array(
								''     => array(
									'title' => __( 'Filter using Javascript/CSS', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/filter-css.svg',
								),
								'ajax' => array(
									'title' => __( 'Ajax Loading', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/filter-ajax.svg',
								),
							),
							'subtitle' => __( 'Control filter type in faqs page or faqs archive page.', 'porto' ),
							'default'  => '',
							'required' => array(
								array( 'faq-infinite', '!=', '' ),
								array( 'faq-cat-sort-pos', '!=', 'hide' ),
							),
						),
					),
				),
				$options_style,
				$faq_options
			);

			/**
			 * Unlimited Post Types
			 *
			 * @since 6.4.0
			 */
			$ptus = $this->get_post_ptu();
			if ( ! empty( $ptus ) ) {

				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'ptu-layouts-settings',
						'icon'       => 'Simple-Line-Icons-grid',
						'icon_class' => '',
						'title'      => __( 'Unlimited Post Types', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_ptu_layout',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a>, <a href="%2$s" target="_blank">Single</a> & <a href="%3$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $archive_url, $single_url, $type_url ),
									array(
										'strong' => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'desc_info_go_ptu_sidebar',
								'type'    => 'info',
								'desc'    => wp_kses(
									sprintf(
										/* translators: %s: widgets url */
										__( 'You can create the sidebar in <a href="%s" target="_blank">here</a>.', 'porto' ),
										esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) )
									),
									array(
										'a' => array(
											'href'   => array(),
											'target' => array(),
										),
									)
								),
							),
						),
					),
					$options_style
				);
				foreach ( $ptus as $name ) {
					$this->sections[] = $this->add_customizer_field(
						array(
							'icon_class' => 'icon',
							'subsection' => true,
							'title'      => sprintf( esc_html__( '%s Layouts', 'porto' ), ucfirst( $name ) ),
							'fields'     => array(
								array(
									'id'      => $name . '-ptu-archive-layout',
									'type'    => 'image_select',
									'title'   => __( 'Archive Layout', 'porto' ),
									'options' => $page_layouts,
									'default' => 'fullwidth',
								),
								array(
									'id'       => $name . '-ptu-archive-sidebar',
									'type'     => 'select',
									'title'    => __( 'Select Archive Sidebar', 'porto' ),
									'required' => array( $name . '-ptu-archive-layout', 'equals', $sidebars ),
									'data'     => 'sidebars',
								),
								array(
									'id'       => $name . '-ptu-archive-sidebar2',
									'type'     => 'select',
									'title'    => __( 'Select Archive Sidebar 2', 'porto' ),
									'required' => array( $name . '-ptu-archive-layout', 'equals', $both_sidebars ),
									'data'     => 'sidebars',
								),
								array(
									'id'      => $name . '-ptu-single-layout',
									'type'    => 'image_select',
									'title'   => __( 'Single Layout', 'porto' ),
									'options' => $page_layouts,
									'default' => 'fullwidth',
								),
								array(
									'id'       => $name . '-ptu-single-sidebar',
									'type'     => 'select',
									'title'    => __( 'Select Single Sidebar', 'porto' ),
									'required' => array( $name . '-ptu-single-layout', 'equals', $sidebars ),
									'data'     => 'sidebars',
								),
								array(
									'id'       => $name . '-ptu-single-sidebar2',
									'type'     => 'select',
									'title'    => __( 'Select Single Sidebar 2', 'porto' ),
									'required' => array( $name . '-ptu-single-layout', 'equals', $both_sidebars ),
									'data'     => 'sidebars',
								),
							),
						),
						$options_style
					);
				}
			}

			/**
			 * ACF Post Types
			 *
			 * @since 7.6.0
			 */
			$acfs = $this->get_post_acf();
			if ( ! empty( $acfs ) ) {

				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'acf-layouts-settings',
						'icon'       => 'Simple-Line-Icons-grid',
						'icon_class' => '',
						'title'      => __( 'ACF Post Types', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_acf_layout',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a>, <a href="%2$s" target="_blank">Single</a> & <a href="%3$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $archive_url, $single_url, $type_url ),
									array(
										'strong' => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'desc_info_go_acf_sidebar',
								'type'    => 'info',
								'desc'    => wp_kses(
									sprintf(
										/* translators: %s: widgets url */
										__( 'You can create the sidebar in <a href="%s" target="_blank">here</a>.', 'porto' ),
										esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) )
									),
									array(
										'a' => array(
											'href'   => array(),
											'target' => array(),
										),
									)
								),
							),
						),
					),
					$options_style
				);
				foreach ( $acfs as $name ) {
					$this->sections[] = $this->add_customizer_field(
						array(
							'icon_class' => 'icon',
							'subsection' => true,
							'title'      => sprintf( esc_html__( '%s Layouts', 'porto' ), ucfirst( $name ) ),
							'fields'     => array(
								array(
									'id'      => $name . '-acf-archive-layout',
									'type'    => 'image_select',
									'title'   => __( 'Archive Layout', 'porto' ),
									'options' => $page_layouts,
									'default' => 'fullwidth',
								),
								array(
									'id'       => $name . '-acf-archive-sidebar',
									'type'     => 'select',
									'title'    => __( 'Select Archive Sidebar', 'porto' ),
									'required' => array( $name . '-acf-archive-layout', 'equals', $sidebars ),
									'data'     => 'sidebars',
								),
								array(
									'id'       => $name . '-acf-archive-sidebar2',
									'type'     => 'select',
									'title'    => __( 'Select Archive Sidebar 2', 'porto' ),
									'required' => array( $name . '-acf-archive-layout', 'equals', $both_sidebars ),
									'data'     => 'sidebars',
								),
								array(
									'id'      => $name . '-acf-single-layout',
									'type'    => 'image_select',
									'title'   => __( 'Single Layout', 'porto' ),
									'options' => $page_layouts,
									'default' => 'fullwidth',
								),
								array(
									'id'       => $name . '-acf-single-sidebar',
									'type'     => 'select',
									'title'    => __( 'Select Single Sidebar', 'porto' ),
									'required' => array( $name . '-acf-single-layout', 'equals', $sidebars ),
									'data'     => 'sidebars',
								),
								array(
									'id'       => $name . '-acf-single-sidebar2',
									'type'     => 'select',
									'title'    => __( 'Select Single Sidebar 2', 'porto' ),
									'required' => array( $name . '-acf-single-layout', 'equals', $both_sidebars ),
									'data'     => 'sidebars',
								),
							),
						),
						$options_style
					);
				}
			}

			if ( class_exists( 'WooCommerce' ) ) {
				require_once PORTO_ADMIN . '/theme_options/woocommerce.php';
			}

			$this->sections[] = array(
				'id'         => 'skin-custom-css',
				'icon_class' => 'icon',
				'title'      => __( 'Custom CSS', 'porto' ),
				'customizer' => false,
				'transport'  => 'postMessage',
				'class'      => 'pt-extra-css',
				'fields'     => array(
					array(
						'id'       => 'css-code',
						'type'     => 'ace_editor',
						'title'    => __( 'CSS Code', 'porto' ),
						'subtitle' => __( 'Paste your custom CSS code here.', 'porto' ),
						'mode'     => 'css',
						'theme'    => 'monokai',
						'default'  => '',
						'options'  => array(
							'height'   => 450,
							'minLines' => 40,
							'maxLines' => 50,
						),
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'el-icon-edit',
				'title'      => __( 'Javascript Code', 'porto' ),
				'customizer' => false,
				'fields'     => array(
					array(
						'id'       => 'js-code-head',
						'type'     => 'ace_editor',
						'title'    => __( 'JS Code before &lt;/head&gt;', 'porto' ),
						'subtitle' => __( 'Paste your custom JavaScript code here.', 'porto' ),
						'mode'     => 'javascript',
						'theme'    => 'chrome',
						'default'  => '',
						'options'  => array(
							'height'   => 250,
							'minLines' => 15,
							'maxLines' => 25,
						),
					),
					array(
						'id'       => 'js-code',
						'type'     => 'ace_editor',
						'title'    => __( 'JS Code before &lt;/body&gt;', 'porto' ),
						'subtitle' => __( 'Paste your custom JavaScript code here.', 'porto' ),
						'mode'     => 'javascript',
						'theme'    => 'chrome',
						'default'  => '',
						'options'  => array(
							'height'   => 250,
							'minLines' => 15,
							'maxLines' => 25,
						),
					),
				),
			);


		}
		public function setHelpTabs() {
		}
		public function setArguments() {
			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$header_html  = '<a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto' ) ) . '">' . esc_html__( 'Dashboard', 'porto' ) . '</a>';
			$header_html .= '<a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto-page-layouts' ) ) . '">' . esc_html__( 'Page Layouts', 'porto' ) . '</a>';

			$menu_title   = esc_html__( 'Theme Options', 'porto' );
			
			$header_html .= '<a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto-setup-wizard' ) ) . '">' . esc_html__( 'Setup Wizard', 'porto' ) . '</a><a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto-speed-optimize-wizard' ) ) . '">' . esc_html__( 'Speed Optimize Wizard', 'porto' ) . '</a><a class="porto-theme-link porto-theme-link-last" href="' . esc_url( admin_url( 'admin.php?page=porto-tools' ) ) . '">' . esc_html__( 'Tools', 'porto' ) . '</a>';

			$new_style = get_theme_mod( 'theme_options_use_new_style', false );
			if ( ! $new_style && $this->legacy_mode ) {
				$header_html .= '<a href="#" class="porto-theme-link switch-live-option-panel">' . esc_html__( 'Live Option Panel', 'porto' ) . '</a>';
			}

			$version_html = '<div class="header-left"><h1>' . $menu_title . '</h1><h6>' . __( 'Theme Options panel enables you full control over your website design and settings.', 'porto' ) . '</h6></div>';
			/* translators: theme version */
			$version_html .= '<div class="header-right"><div class="porto-logo"><img src="' . PORTO_URI . '/images/logo/logo_white_small.png" alt="Porto"><div class="version">' . sprintf( __( 'version %s', 'porto' ), PORTO_VERSION ) . '</div></div></div>';

			$this->args = array(
				'opt_name'                  => 'porto_settings',
				'display_name'              => '<div class="porto-admin-nav">' . $header_html . '</div>',
				'display_version'           => '<div class="porto-admin-header">' . $version_html . '</div>',
				'menu_type'                 => 'menu',
				'allow_sub_menu'            => false,
				'menu_title'                => $menu_title,
				'page_title'                => $menu_title,
				'footer_credit'             => __( 'Porto Advanced Options', 'porto' ),
				'google_api_key'            => 'AIzaSyAX_2L_UzCDPEnAHTG7zhESRVpMPS4ssII',
				'disable_google_fonts_link' => true,
				'async_typography'          => false,
				'admin_bar'                 => false,
				'admin_bar_icon'            => 'dashicons-porto-settings',
				'admin_bar_priority'        => 50,
				'global_variable'           => '',
				'dev_mode'                  => false,
				'customizer'                => $new_style,
				'compiler'                  => false,
				'page_priority'             => 55,
				'page_parent'               => 'admin.php',
				'page_permissions'          => 'manage_options',
				'menu_icon'                 => 'dashicons-porto-settings',
				'last_tab'                  => '',
				'page_icon'                 => 'icon-themes',
				'page_slug'                 => 'porto_settings',
				'save_defaults'             => true,
				'default_show'              => false,
				'default_mark'              => '',
				'show_import_export'        => true,
				'show_options_object'       => false,
				'transient_time'            => 60 * MINUTE_IN_SECONDS,
				'output'                    => false,
				'output_tag'                => true,
				'database'                  => '',
				'system_info'               => false,
				'hints'                     => array(
					'icon'          => 'Simple-Line-Icons-question',
					'icon_position' => 'right',
					'icon_color'    => 'lightgray',
					'icon_size'     => 'normal',
					'tip_style'     => array(
						'color'   => 'light',
						'shadow'  => true,
						'rounded' => false,
						'style'   => '',
					),
					'tip_position'  => array(
						'my' => 'top left',
						'at' => 'bottom right',
					),
					'tip_effect'    => array(
						'show' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'mouseover',
						),
						'hide' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'click mouseleave',
						),
					),
				),
				'ajax_save'                 => true,
				'use_cdn'                   => true,
			);
			// Panel Intro text -> before the form
			if ( ! isset( $this->args['global_variable'] ) || false !== $this->args['global_variable'] ) {
				if ( ! empty( $this->args['global_variable'] ) ) {
					$v = $this->args['global_variable'];
				} else {
					$v = str_replace( '-', '_', $this->args['opt_name'] );
				}
			}
		}

		/**
		 * generates css variables
		 *
		 * @since 6.2.0
		 */
		public function get_css_vars() {
			if ( ! empty( $this->css_var_selectors ) ) {
				return $this->css_var_selectors;
			}
			if ( isset( $this->sections ) ) {
				foreach ( $this->sections as $sk => $section ) {
					if ( isset( $section['fields'] ) ) {
						foreach ( $section['fields'] as $k => $field ) {
							if ( empty( $field['id'] ) && empty( $field['type'] ) ) {
								continue;
							}
							if ( empty( $field['selector'] ) ) {
								continue;
							}

							if ( ! isset( $this->css_var_selectors[ $field['selector']['node'] ] ) ) {
								$this->css_var_selectors[ $field['selector']['node'] ] = array();
							}

							$arr = array( $field['id'] );
							if ( 'typography' == $field['type'] ) {
								$arr[] = '';
								$arr[] = 'typography';
							} else {
								if ( isset( $field['selector']['unit'] ) ) {
									$arr[] = $field['selector']['unit'];
								}
								if ( isset( $field['selector']['type'] ) ) {
									$arr[] = $field['selector']['type'];
								}
							}
							$this->css_var_selectors[ $field['selector']['node'] ][] = $arr;
						}
					}
				}
			}

			return $this->css_var_selectors;
		}
	}
	global $reduxPortoSettings;
	$reduxPortoSettings = new Redux_Framework_porto_settings();
}
