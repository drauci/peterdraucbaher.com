<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Define variables
 */
define( 'PORTO_DIR', get_parent_theme_file_path() );              // template directory
define( 'PORTO_LIB', PORTO_DIR . '/inc' );                        // library directory
define( 'PORTO_ADMIN', PORTO_LIB . '/admin' );                    // admin directory
define( 'PORTO_PLUGINS', PORTO_LIB . '/plugins' );                // plugins directory
define( 'PORTO_CONTENT_TYPES', PORTO_LIB . '/content_types' );    // content_types directory
define( 'PORTO_MENU', PORTO_LIB . '/menu' );                      // menu directory
define( 'PORTO_FUNCTIONS', PORTO_LIB . '/functions' );            // functions directory
define( 'PORTO_OPTIONS_DIR', PORTO_ADMIN . '/theme_options' );    // options directory
define( 'PORTO_URI', get_parent_theme_file_uri() );               // template directory uri
define( 'PORTO_CSS', PORTO_URI . '/css' );                        // css uri
define( 'PORTO_JS', PORTO_URI . '/js' );                          // javascript uri
define( 'PORTO_PLUGINS_URI', PORTO_URI . '/inc/plugins' );             // plugins uri
define( 'PORTO_OPTIONS_URI', PORTO_URI . '/inc/admin/theme_options' ); // theme options uri
define( 'PORTO_LIB_URI', PORTO_URI . '/inc/lib' );                     // library uri
define( 'PORTO_API_URL', 'https://gpl.pp.ua/porto/activation/' );
define( 'PORTO_WC_103_PREFIX', ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '10.3.0', '>=' ) ) ? 'wc-' : '' ); // WooCommerce 10.3 Compatibility

if ( ! defined( 'PORTO_HINT_URL' ) ) {
	define( 'PORTO_HINT_URL', 'https://sw-themes.com/porto_dummy/wp-content/uploads/hint/' );
}


if ( ! defined( 'PORTO_WIDGET_URL' ) ) {
	define( 'PORTO_WIDGET_URL', 'https://sw-themes.com/porto_dummy/wp-content/uploads/widgets/' );
}

$theme_version = '';
$theme         = wp_get_theme();
if ( is_child_theme() ) {
	$theme = wp_get_theme( $theme->template );
}
$theme_version = $theme->version;
define( 'PORTO_VERSION', $theme_version );                    // set current version
/**
 * WordPress theme check
 */
// set content width
if ( ! isset( $content_width ) ) {
	$content_width = 1140;
}

// Import, filter and save studios
$porto_ajax_studio = wp_doing_ajax() && isset( $_REQUEST['action'] ) && ( 'porto_studio_filter_category' === $_REQUEST['action'] || 'porto_studio_save' === $_REQUEST['action'] || 'porto_studio_import' === $_REQUEST['action'] );
if ( ! $porto_ajax_studio ) {
	/**
	 * Porto content types functions
	 */
	require_once PORTO_FUNCTIONS . '/content_type.php';
}
/**
 * Porto functions
 */
require_once PORTO_FUNCTIONS . '/functions.php';
if ( ! $porto_ajax_studio ) {
	/**
	 * Menu
	 */
	require_once PORTO_MENU . '/menu.php';

	/**
	 * Porto theme options
	 */
	require_once PORTO_ADMIN . '/theme_options.php';

	/**
	 * Porto admin options
	 */
	if ( current_user_can( 'manage_options' ) ) {
		require_once PORTO_ADMIN . '/admin.php';
	}
}
/**
 * Porto Extensions
 */
require_once PORTO_LIB . '/lib/setup.php';
if ( ! $porto_ajax_studio ) {
	/**
	 * Install Plugins
	 */
	require_once PORTO_PLUGINS . '/plugins.php';
	/**
	 * Porto Patcher
	 */
	if ( current_user_can( 'manage_options' ) ) {
		require_once PORTO_ADMIN . '/patcher/patcher.php';
	}
}
/**
 * Theme support & Theme setup
 */
// theme setup
if ( ! function_exists( 'porto_setup' ) ) :
	function porto_setup() {

		// require_once PORTO_FUNCTIONS . '/gutenberg.php';

		add_theme_support( 'title-tag' );
		add_editor_style( array( 'style.css', 'style_rtl.css' ) );
		if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1' ) >= 0 ) {
				add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
			} else {
				define( 'WOOCOMMERCE_USE_CSS', false );
			}
		}
		// translation
		load_theme_textdomain( 'porto', PORTO_DIR . '/languages' );
		load_child_theme_textdomain( 'porto', get_stylesheet_directory() . '/languages' );

		global $porto_settings, $porto_settings_optimize;
		// default rss feed links
		add_theme_support( 'automatic-feed-links' );
		// add support for post thumbnails
		add_theme_support( 'post-thumbnails' );
		// add image sizes
		add_image_size( 'blog-large', 1140, 445, true );
		add_image_size( 'blog-medium', 463, 348, true );
		add_image_size( 'blog-masonry', 640, 9999, false );
		add_image_size( 'blog-masonry-small', 400, 9999, false );
		add_image_size( 'blog-grid', 640, 480, true );
		add_image_size( 'blog-grid-small', 400, 300, true );
		add_image_size( 'related-post', ( isset( $porto_settings['post-related-image-size'] ) && (int) $porto_settings['post-related-image-size']['width'] ) ? (int) $porto_settings['post-related-image-size']['width'] : 450, ( isset( $porto_settings['post-related-image-size'] ) && (int) $porto_settings['post-related-image-size']['height'] ) ? (int) $porto_settings['post-related-image-size']['height'] : 231, true );
		if ( isset( $porto_settings['enable-portfolio'] ) && $porto_settings['enable-portfolio'] ) {
			add_image_size( 'portfolio-grid-one', 1140, 595, true );
			add_image_size( 'portfolio-grid-two', 560, 560, true );
			add_image_size( 'portfolio-grid', 367, 367, true );
			add_image_size( 'portfolio-masonry', 367, 9999, false );
			add_image_size( 'portfolio-full', 1140, 595, true );
			add_image_size( 'portfolio-large', 560, 367, true );
			add_image_size( 'portfolio-medium', 367, 367, true );
			add_image_size( 'portfolio-timeline', 560, 560, true );
			add_image_size( 'related-portfolio', 367, 367, true );
			add_image_size( 'portfolio-cat-stripes', 494, 1080, true );
			add_image_size( 'portfolio-cat-parallax', 1970, 627, true );
			add_image_size( 'portfolio-thumbnail', 200, 150, true );
		}

		if ( isset( $porto_settings['enable-member'] ) && $porto_settings['enable-member'] ) {
			add_image_size( 'member-two', 560, 560, true );
			add_image_size( 'member', 367, 367, true );
		}
		add_image_size( 'widget-thumb-medium', 85, 85, true );
		// add_image_size( 'widget-thumb', 50, 50, true );
		// woocommerce support
		add_theme_support( 'woocommerce', array( 'gallery_thumbnail_image_width' => empty( $porto_settings['product-thumbs-w'] ) ? 150 : (int) $porto_settings['product-thumbs-w'] ) );
        add_theme_support( 'wc-product-gallery-lightbox' );
		// allow shortcodes in widget text
		add_filter( 'widget_text', 'do_shortcode' );
		// register menus
		register_nav_menus(
			array(
				'main_menu'         => __( 'Main Menu', 'porto' ),
				'secondary_menu'    => __( 'Secondary Menu', 'porto' ),
				'sidebar_menu'      => __( 'Sidebar Menu', 'porto' ),
				'top_nav'           => __( 'Top Navigation', 'porto' ),
				'view_switcher'     => __( 'View Switcher', 'porto' ),
				'currency_switcher' => __( 'Currency Switcher', 'porto' ),
				'account_menu'      => __( 'Account Menu', 'porto' ),
			)
		);

		// add post formats
		add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio', 'chat' ) );

		// to output valid HTML5 formats
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		// disable master slider woocommerce product slider
		$options = get_option( 'msp_woocommerce' );
		if ( isset( $options ) && isset( $options['enable_single_product_slider'] ) && 'on' == $options['enable_single_product_slider'] ) {
			$options['enable_single_product_slider'] = '';
			update_option( 'msp_woocommerce', $options );
		}

		if ( empty( $porto_settings_optimize ) && ! is_array( $porto_settings_optimize ) ) {
			if ( ! is_customize_preview() ) {
				$porto_settings_optimize = get_option( 'porto_settings_optimize', array() );
			} else {
				$porto_settings_optimize = array();
			}
		}

		if ( ( isset( $porto_settings['google-webfont-loader'] ) && $porto_settings['google-webfont-loader'] ) || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
			if ( empty( $porto_settings_optimize['merge_stylesheets'] ) ) {
				add_filter( 'wp_head', 'porto_google_webfont_loader' );
			} else {
				add_action( 'wp_print_footer_scripts', 'porto_google_webfont_loader', 8 );
			}
		}
		if ( porto_is_ajax() && isset( $_POST['action'] ) && 'porto_lazyload_menu' == $_POST['action'] ) {
			$action_name = isset( $_POST['porto_lazyload_menu_2'] ) ? 'wp_loaded' : 'template_redirect';
			add_action( $action_name, 'porto_action_lazyload_menu', 20 );
		}

		// add support
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );

		if ( ! empty( $porto_settings ) && ! empty( $porto_settings['skin-color'] ) ) {
			// Editor color palette.
			add_theme_support(
				'editor-color-palette',
				array(
					array(
						'name'  => __( 'Primary', 'porto' ),
						'slug'  => 'primary',
						'color' => 'var(--porto-primary-color)',
					),
					array(
						'name'  => __( 'Secondary', 'porto' ),
						'slug'  => 'secondary',
						'color' => 'var(--porto-secondary-color)',
					),
					array(
						'name'  => __( 'Tertiary', 'porto' ),
						'slug'  => 'tertiary',
						'color' => 'var(--porto-tertiary-color)',
					),
					array(
						'name'  => __( 'Quaternary', 'porto' ),
						'slug'  => 'quaternary',
						'color' => 'var(--porto-quaternary-color)',
					),
					array(
						'name'  => __( 'Dark', 'porto' ),
						'slug'  => 'dark',
						'color' => 'var(--porto-dark-color)',
					),
					array(
						'name'  => __( 'Light', 'porto' ),
						'slug'  => 'light',
						'color' => 'var(--porto-light-color)',
					),
					array(
						'name'  => __( 'Primary Hover Color', 'porto' ),
						'slug'  => 'primary-hover',
						'color' => 'var(--porto-primary-light-5)',
					),
				)
			);
		}
	}
endif;
add_action( 'after_setup_theme', 'porto_setup' );

/**
 * Enqueue css, js files
 */
add_action( 'wp_enqueue_scripts', 'porto_pre_css', 8 );
add_action( 'wp_enqueue_scripts', 'porto_css', 1000 );
add_action( 'wp_enqueue_scripts', 'porto_pre_scripts' );
add_action( 'wp_enqueue_scripts', 'porto_scripts', 1000 );
add_action( 'admin_enqueue_scripts', 'porto_admin_css', 1000 );
add_action( 'admin_enqueue_scripts', 'porto_admin_scripts', 1000 );

if ( is_admin() ) {

	add_action( 'enqueue_block_editor_assets', 'porto_admin_block_css', 1000 );
	if ( function_exists( 'mailpoet_deactivate_plugin' ) ) {
		// fix MailPoet3 style issue
		add_filter( 'mailpoet_conflict_resolver_whitelist_style', 'porto_add_styles_mailpoet_pages' );

		function porto_add_styles_mailpoet_pages( $styles ) {
			$styles[] = PORTO_CSS . '/admin.min.css';
			$styles[] = PORTO_CSS . '/admin_bar.css';
			return $styles;
		}
	}

	function porto_admin_block_css() {
		$required_css = array( 'wp-edit-blocks', 'porto_admin' );
		if ( wp_style_is( 'owl.carousel', 'enqueued' ) ) {
			$required_css[] = 'owl.carousel';
		}
		porto_register_style( 'porto-css-vars', 'theme_css_vars', false, true );
		wp_enqueue_style( 'porto-css-vars' );
		$required_css[] = 'porto-css-vars';

		global $porto_settings;

		$xl = (int) $porto_settings['container-width'];

		if ( is_rtl() ) {
			wp_enqueue_style( 'porto-blocks-editor', PORTO_CSS . '/editor_rtl.css', $required_css, PORTO_VERSION );
			wp_enqueue_style( 'porto-blocks-thumb-info', PORTO_CSS . '/part/thumb-info_rtl.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-blocks-editor-responsive-xl', PORTO_CSS . '/editor_responsive_xl_rtl.css', array( 'porto-blocks-editor' ), PORTO_VERSION, '(min-width: ' . ( (int) $xl + (int) $porto_settings['grid-gutter-width'] + 1 ) . 'px)' );
		} else {
			wp_enqueue_style( 'porto-blocks-editor', PORTO_CSS . '/editor.css', $required_css, PORTO_VERSION );
			wp_enqueue_style( 'porto-blocks-thumb-info', PORTO_CSS . '/part/thumb-info.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-blocks-editor-responsive-xl', PORTO_CSS . '/editor_responsive_xl.css', array( 'porto-blocks-editor' ), PORTO_VERSION, '(min-width: ' . ( (int) $xl + (int) $porto_settings['grid-gutter-width'] + 1 ) . 'px)' );
		}
		wp_enqueue_style( 'porto-blocks-flag', PORTO_CSS . '/part/flag.css', array( 'porto-blocks-editor' ), PORTO_VERSION );

		wp_enqueue_style( 'porto-widget-tag-cloud', PORTO_URI . '/css/part/widget-tag-cloud' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );

		$legacy_mode = apply_filters( 'porto_legacy_mode', true );
		if ( $legacy_mode ) {
			wp_enqueue_style( 'porto-blog-legacy', PORTO_URI . '/css/part/blog-legacy' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
		}

		if ( 'overlay' == $porto_settings['menu-type'] ) {
			wp_enqueue_style( 'porto-popup-menu', PORTO_URI . '/css/part/popup-menu' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
		}
		if ( ! empty( $porto_settings['side-menu-type'] ) ) {
			wp_enqueue_style( 'porto-side-menu', PORTO_URI . '/css/part/side-' . $porto_settings['side-menu-type'] . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style( 'porto-header-shop', PORTO_URI . '/css/part/header-shop' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
		}

		if ( $legacy_mode ) {
			wp_enqueue_style( 'porto-header-legacy', PORTO_URI . '/css/part/header-legacy' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-footer-legacy', PORTO_URI . '/css/part/footer-legacy' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
		}

		if ( ! empty( $porto_settings['logo-overlay'] ) && $porto_settings['logo-overlay']['url'] ) {
			wp_enqueue_style( 'porto-logo-overlay', PORTO_URI . '/css/part/logo-overlay.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
		}

		if ( class_exists( 'RevSlider' ) ) {
			wp_enqueue_style( 'porto-revslider', PORTO_URI . '/css/part/revslider' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
		}
		if ( class_exists( 'Master_Slider' ) ) {
			wp_enqueue_style( 'porto-master-slider', PORTO_URI . '/css/part/master-slider.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
		}
		wp_enqueue_style( 'porto-media-mobile-sidebar', PORTO_URI . '/css/part/media-mobile-sidebar' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION, '(max-width:991px)' );

		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style( 'porto-only-product', PORTO_URI . '/css/part/only-product' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			if ( $legacy_mode ) {
				wp_enqueue_style( 'porto-shop-legacy', PORTO_URI . '/css/part/shop-legacy' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			}
			if ( defined( 'YITH_WOOCOMPARE' ) ) {
				wp_enqueue_style( 'porto-yit-compare', PORTO_URI . '/css/part/yit-compare' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			}
			wp_enqueue_style( 'porto-shop-sidebar-base', PORTO_URI . '/css/part/shop-sidebar-base' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
	
			wp_enqueue_style( 'porto-woo-widget-product-search', PORTO_URI . '/css/part/woo-widget-product-search.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-woo-widget-recent-review-products', PORTO_URI . '/css/part/woo-widget-recent-review-products.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-yith-reset', PORTO_URI . '/css/part/yith-reset.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-woo-widget-price-filter', PORTO_URI . '/css/part/woo-widget-porto-price-filter.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-wooco-widget-price-filter', PORTO_URI . '/css/part/woo-widget-price-filter' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-woo-widget-rating-filter', PORTO_URI . '/css/part/woo-widget-rating-filter' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-woo-widget-active-filters', PORTO_URI . '/css/part/woo-widget-active-filters' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-woo-widget-wcan-select', PORTO_URI . '/css/part/woo-widget-wcan-select' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-woo-widget-brand', PORTO_URI . '/css/part/woo-widget-brand.css', array( 'porto-blocks-editor' ), PORTO_VERSION );
			wp_enqueue_style( 'porto-shop-toolbar', PORTO_URI . '/css/theme/shop/other/toolbar' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );

			if ( defined( 'YITH_WFBT_VERSION' ) ) {
				wp_register_style( 'porto-shop-fbt', PORTO_URI . '/css/theme/shop/other/fbt.css', array(), PORTO_VERSION );
			}
			if ( defined( 'WOOCO_VERSION' ) ) {
				wp_register_style( 'porto-shop-wooco', PORTO_URI . '/css/theme/shop/other/wooco.css', array(), PORTO_VERSION );
			}
	
			if ( defined( 'PWB_PLUGIN_VERSION' ) ) {
				// Perfect WooCommerce Brand Plugin
				wp_enqueue_style( 'porto-pwb-filter', PORTO_URI . '/css/theme/shop/other/pw-brand-filter' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
			}
			if ( class_exists( 'WeDevs_Dokan' ) ) {
				wp_enqueue_style( 'porto-dokan', PORTO_URI . '/css/theme_dokan' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
			}
			if ( class_exists( 'WCFMmp' ) ) {
				wp_enqueue_style( 'porto-wcfm', PORTO_URI . '/css/theme_wcfm' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
			}
			if ( class_exists( 'WOOF' ) ) {
				wp_enqueue_style( 'porto-woof', PORTO_URI . '/css/theme_woof' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
			}
			if ( class_exists( 'WooCommerce_Waitlist_Plugin' ) ) {
				wp_enqueue_style( 'porto-wcwl', PORTO_URI . '/css/theme_wcwl' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
			}
		}

		porto_register_style( 'porto-blocks-editor-dynamic', 'style-editor', false, true, array( 'porto-blocks-editor' ) );
		wp_enqueue_style( 'porto-blocks-editor-dynamic' );

		$dynamic_load = ! ( isset( $GLOBALS['pagenow'] ) && 'widgets.php' == $GLOBALS['pagenow'] );
		if ( $dynamic_load ) {
			if ( is_rtl() ) {
				porto_register_style( 'porto-dynamic-style', 'dynamic_style_rtl', false, false );
			} else {
				porto_register_style( 'porto-dynamic-style', 'dynamic_style', false, false );
			}
			wp_enqueue_style( 'porto-dynamic-style' );
		}

		$theme_options_custom_css = $porto_settings['css-code'];
		if ( $theme_options_custom_css ) {
			wp_add_inline_style( $dynamic_load ? 'porto-dynamic-style' : 'porto-blocks-editor-dynamic', wp_strip_all_tags( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $theme_options_custom_css ) ) );
		}
	}
}

function porto_google_webfont_loader() {
	if ( porto_is_amp_endpoint() ) {
		return;
	}

	global $porto_settings, $porto_settings_optimize;

	$gfont = array();
	$fonts = porto_settings_google_fonts();
	foreach ( $fonts as $option => $weights ) {
		if ( isset( $porto_settings[ $option . '-font' ]['google'] ) && 'false' !== $porto_settings[ $option . '-font' ]['google'] ) {
			$font = isset( $porto_settings[ $option . '-font' ]['font-family'] ) ? urlencode( $porto_settings[ $option . '-font' ]['font-family'] ) : '';
			if ( $font ) {
				$has_italic  = false;
				$font_weight = isset( $porto_settings[ $option . '-font' ]['font-weight'] ) ? $porto_settings[ $option . '-font' ]['font-weight'] : '';
				if ( $font_weight && isset( $porto_settings[ $option . '-font' ]['font-style'] ) && 'italic' == $porto_settings[ $option . '-font' ]['font-style'] ) {
					$font_weight .= 'italic';
					$has_italic   = true;
				}
				if ( $font_weight && ! in_array( $font_weight, $weights ) ) {
					$weights[] = $font_weight;
				}
				if ( isset( $gfont[ $font ] ) ) {
					foreach ( $gfont[ $font ] as $w ) {
						if ( ! in_array( $w, $weights ) ) {
							$weights[] = $w;
						}
					}
				}
				if ( $has_italic && ! in_array( '400italic', $weights ) ) {
					$weights[] = '400italic';
				}
				$gfont[ $font ] = $weights;
			}
		}
	}

	// charset
	$charsets = array();
	$subsets  = '';
	if ( isset( $porto_settings['select-google-charset'] ) && $porto_settings['select-google-charset'] && isset( $porto_settings['google-charsets'] ) && $porto_settings['google-charsets'] ) {
		foreach ( $porto_settings['google-charsets'] as $charset ) {
			if ( $charset && ! in_array( $charset, $charsets ) ) {
				$charsets[] = $charset;
			}
		}
	}
	if ( ! empty( $charsets ) ) {
		$subsets = implode( ',', $charsets );
	}

	$font_family_arr = array();
	foreach ( $gfont as $font => $weights ) {
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			$weights = array( '400' );
			$subsets = false;
		} else {
			sort( $weights );
		}
		$font_family_arr[] = esc_js( str_replace( ' ', '+', $font ) . ( empty( $weights ) ? '' : ':' . implode( ',', $weights ) ) . ( $subsets ? ':' . $subsets : '' ) );
		$subsets           = '';
	}
	if ( ! empty( $font_family_arr ) ) {
		$font_str = implode( "','", $font_family_arr );
		if ( ! empty( $porto_settings_optimize['swap'] ) ) {
			$font_str .= '&display=swap';
		}
		?>
		<script type="text/javascript" id="webfont-queue">
		WebFontConfig = {
			google: { families: [ '<?php echo porto_filter_output( $font_str ); ?>' ] }
		};
		(function(d) {
			var wf = d.createElement('script'), s = d.scripts[d.scripts.length - 1];
			wf.src = '<?php echo PORTO_JS; ?>/libs/webfont.js';
			wf.async = true;
			s.parentNode.insertBefore(wf, s);
		})(document);</script>
		<?php
	}
}

if ( ! function_exists( 'porto_pre_css' ) ) {
	function porto_pre_css() {
		$yith_wcwl = '';
		if ( wp_style_is( 'yith-wcwl-main', 'registered' ) ) {
			$yith_wcwl = 'yith-wcwl-main';
		}

		if ( wp_style_is( 'yith-wcwl-user-main', 'registered' ) ) {
			$yith_wcwl = 'yith-wcwl-user-main';
		}

		if ( $yith_wcwl ) {
			$yith_wcwl_style = wp_styles()->registered[ $yith_wcwl ];
			if ( isset( $yith_wcwl_style->deps ) && ! empty( $yith_wcwl_style->deps ) ) {
				foreach ( $yith_wcwl_style->deps as $index => $dep ) {
					if ( 'yith-wcwl-font-awesome' == $dep ) {
						unset( $yith_wcwl_style->deps[ $index ] );
						break;
					}
				}
			}
		}
	}
}

function porto_css() {
	// deregister plugin styles
	wp_deregister_style( 'font-awesome' );
	wp_dequeue_style( 'font-awesome' );
	wp_deregister_style( 'yith-wcwl-font-awesome' );
	wp_dequeue_style( 'yith-wcwl-font-awesome' );
	wp_dequeue_style( 'bsf-Simple-Line-Icons' );
	wp_deregister_style( 'vc_animate-css' );
	wp_dequeue_style( 'vc_animate-css' );
	wp_deregister_style( 'vc_font_awesome_5' );
	wp_dequeue_style( 'vc_font_awesome_5' );

	if ( class_exists( 'RevSliderFront' ) ) {
		remove_action( 'wp_footer', array( 'RevSliderFront', 'load_icon_fonts' ) );
		add_action( 'wp_footer', 'porto_revslider_icon_fonts' );
	}

	global $porto_settings, $porto_settings_optimize, $post;

	// dequeue elementor font awesome
	if ( ! empty( $porto_settings_optimize['dequeue_elementor_rc'] ) ) {
		wp_deregister_style( 'elementor-icons-fa-regular' );
		wp_deregister_style( 'elementor-icons-fa-solid' );
		wp_deregister_style( 'elementor-icons-fa-brands' );

		if ( ( ! is_user_logged_in() || ( is_user_logged_in() && ! current_user_can( 'administrator' ) ) ) ) {
			wp_deregister_style( 'elementor-icons' );
			wp_dequeue_style( 'elementor-icons' );
		}

	}
	// dokan font awesome
	if ( ! empty( $porto_settings_optimize['dequeue_dokan_rc'] ) ) {
		wp_deregister_style( 'dokan-fontawesome' );
	}

	// import revslider js/css files for only used pages
	if ( class_exists( 'RevSlider' ) && isset( $porto_settings_optimize['optimize_revslider'] ) && $porto_settings_optimize['optimize_revslider'] ) {
		$use_revslider = false;
		$banner_type   = porto_get_meta_value( 'banner_type' );
		$rev_slider    = porto_get_meta_value( 'rev_slider' );
		if ( 'rev_slider' === $banner_type && ! empty( $rev_slider ) ) {
			$use_revslider = true;
		}
		if ( ! $use_revslider && is_singular( 'portfolio' ) ) {
			$portfolio_layout = get_post_meta( $post->ID, 'portfolio_layout', true );
			$portfolio_layout = ( 'default' == $portfolio_layout || ! $portfolio_layout ) ? ( isset( $porto_settings['portfolio-content-layout'] ) ? $porto_settings['portfolio-content-layout'] : 'medium' ) : $portfolio_layout;
			if ( 'carousel' == $portfolio_layout ) {
				$use_revslider = true;
			}
		}
		if ( ! $use_revslider && isset( $porto_settings_optimize['optimize_revslider_pages'] ) ) {
			$rev_pages = $porto_settings_optimize['optimize_revslider_pages'];
			if ( $rev_pages && ! empty( $rev_pages ) ) {
				if ( ! is_search() && ! is_404() && isset( $post->ID ) && in_array( $post->ID, $rev_pages ) ) {
					$use_revslider = true;
				}
			}
		}
		if ( ! $use_revslider && isset( $porto_settings_optimize['optimize_revslider_portfolio'] ) && $porto_settings_optimize['optimize_revslider_portfolio'] && ( ( function_exists( 'is_porto_portfolios_page' ) && is_porto_portfolios_page() ) || is_tax( 'portfolio_cat' ) || is_tax( 'portfolio_skills' ) ) ) {
			$use_revslider = true;
		}
		if ( ! $use_revslider ) {
			wp_dequeue_style( 'rs-plugin-settings' );
			wp_dequeue_script( 'tp-tools' );
			wp_dequeue_script( 'revmin' );
		}
	}

	// css vars
	porto_register_style( 'porto-css-vars', 'theme_css_vars', false, true );
	if ( ! is_customize_preview() ) {
		wp_enqueue_style( 'porto-css-vars' );
	}

	if ( function_exists( 'vc_is_inline' ) && vc_is_inline() && ! ( isset( $_REQUEST['action'] ) && ( 'vc_frontend_load_template' == $_REQUEST['action'] || 'vc_load_shortcode' == $_REQUEST['action'] ) ) ) {
		wp_add_inline_style( 'porto-css-vars', '.vc_vc_column, .vc_vc_column_inner { width: 100%; }' );
	}

	// load wpbakery css
	if ( ! wp_style_is( 'js_composer_front' ) ) {
		wp_enqueue_style( 'js_composer_front' );
	} else {
		// include js composer css after css vars
		wp_dequeue_style( 'js_composer_front' );
		wp_enqueue_style( 'js_composer_front' );
	}
	// load ultimate addons default js
	$bsf_options             = get_option( 'bsf_options' );
	$ultimate_global_scripts = ( isset( $bsf_options['ultimate_global_scripts'] ) ) ? $bsf_options['ultimate_global_scripts'] : false;
	if ( 'enable' !== $ultimate_global_scripts ) {
		$ultimate_css = get_option( 'ultimate_css' );
		if ( 'enable' == $ultimate_css ) {
			if ( ! wp_style_is( 'ultimate-style-min' ) ) {
				wp_enqueue_style( 'ultimate-style-min' );
			}
		} else {
			if ( ! wp_style_is( 'ultimate-style' ) ) {
				wp_enqueue_style( 'ultimate-style' );
			}
		}
	}

	// dequeue block css
	if ( class_exists( 'Woocommerce' ) && ! empty( $porto_settings_optimize['dequeue_wc_block_css'] ) ) {
		wp_dequeue_style( 'wc-blocks-vendors-style' );
		wp_deregister_style( 'wc-blocks-style' );
		wp_dequeue_style( 'wc-blocks-style' );
	}
	if ( ! empty( $porto_settings_optimize['dequeue_wp_block_css'] ) ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
	}

	/*
	 register styles */
	// plugins styles
	wp_deregister_style( 'porto-plugins' );
	$optimized_suffix = '';
	if ( isset( $porto_settings_optimize['optimize_fontawesome'] ) && $porto_settings_optimize['optimize_fontawesome'] ) {
		$optimized_suffix = '_optimized';
	}
	if ( is_rtl() ) {
		wp_register_style( 'porto-plugins', PORTO_URI . '/css/plugins_rtl' . $optimized_suffix . '.css', array(), PORTO_VERSION );
	} else {
		wp_register_style( 'porto-plugins', PORTO_URI . '/css/plugins' . $optimized_suffix . '.css', array(), PORTO_VERSION );
	}

	if ( empty( $porto_settings_optimize['optimize_simpleline'] ) ) {
		wp_enqueue_style( 'simple-line-icons', PORTO_CSS . '/Simple-Line-Icons/Simple-Line-Icons.css', false, PORTO_VERSION, 'all' );
	}

	// default styles
	wp_deregister_style( 'porto-theme' );
	if ( is_rtl() ) {
		wp_register_style( 'porto-theme', PORTO_URI . '/css/theme_rtl.css', array(), PORTO_VERSION );
	} else {
		wp_register_style( 'porto-theme', PORTO_URI . '/css/theme.css', array(), PORTO_VERSION );
	}

	// shortcodes styles
	wp_deregister_style( 'porto-shortcodes' );
	if ( is_rtl() ) {
		porto_register_style( 'porto-shortcodes', 'shortcodes_rtl', false, true );
	} else {
		porto_register_style( 'porto-shortcodes', 'shortcodes', false, true );
	}
	// woocommerce styles
	if ( class_exists( 'WooCommerce' ) ) {
		wp_deregister_style( 'porto-theme-shop' );
		if ( is_rtl() ) {
			wp_register_style( 'porto-theme-shop', PORTO_URI . '/css/theme_rtl_shop.css', array(), PORTO_VERSION );
			wp_register_style( 'porto-theme-woopage', PORTO_URI . '/css/theme/shop/other/woopage_rtl.css', array(), PORTO_VERSION );
			wp_register_style( 'porto-shop-toolbar', PORTO_URI . '/css/theme/shop/other/toolbar_rtl.css', array(), PORTO_VERSION );
		} else {
			wp_register_style( 'porto-theme-shop', PORTO_URI . '/css/theme_shop.css', array(), PORTO_VERSION );
			wp_register_style( 'porto-theme-woopage', PORTO_URI . '/css/theme/shop/other/woopage.css', array(), PORTO_VERSION );
			wp_register_style( 'porto-shop-toolbar', PORTO_URI . '/css/theme/shop/other/toolbar.css', array(), PORTO_VERSION );
		}
		// YITH FREQUENTLY BOUGHT TOGETHER
		wp_register_style( 'porto-shop-fbt', PORTO_URI . '/css/theme/shop/other/fbt.css', array( 'yith-wfbt-style' ), PORTO_VERSION );
		wp_register_style( 'porto-shop-wooco', PORTO_URI . '/css/theme/shop/other/wooco.css', array( 'wooco-frontend' ), PORTO_VERSION );
	}

	// bbpress, buddypress styles
	if ( class_exists( 'bbPress' ) || class_exists( 'BuddyPress' ) ) {
		wp_deregister_style( 'porto-theme-bbpress' );
		if ( is_rtl() ) {
			wp_register_style( 'porto-theme-bbpress', PORTO_URI . '/css/theme_rtl_bbpress.css', array(), PORTO_VERSION );
		} else {
			wp_register_style( 'porto-theme-bbpress', PORTO_URI . '/css/theme_bbpress.css', array(), PORTO_VERSION );
		}
	}

	// custom styles
	wp_deregister_style( 'porto-style' );
	wp_register_style( 'porto-style', PORTO_URI . '/style.css', array(), PORTO_VERSION );
	if ( is_rtl() ) {
		wp_deregister_style( 'porto-style-rtl' );
		wp_register_style( 'porto-style-rtl', PORTO_URI . '/style_rtl.css' );
	}

	// Load Google fonts
	if ( ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) && ( ! isset( $porto_settings['google-webfont-loader'] ) || ! $porto_settings['google-webfont-loader'] ) ) {
		porto_include_google_font();
	}

	// Load custom fonts
	$upload = wp_upload_dir();
	if ( file_exists( $upload['basedir'] . '/redux/custom-fonts/fonts.css' ) && filesize( $upload['basedir'] . '/redux/custom-fonts/fonts.css' ) ) {
		wp_register_style(
			'redux-custom-fonts-css',
			$upload['baseurl'] . '/redux/custom-fonts/fonts.css',
			'',
			filemtime( $upload['basedir'] . '/redux/custom-fonts/fonts.css' ),
			'all'
		);

		wp_enqueue_style( 'redux-custom-fonts-css' );
	}

	/* enqueue styles */
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$used_blocks = porto_check_using_page_builder_block();
		if ( ! empty( $used_blocks ) ) {
			if ( ! wp_style_is( 'elementor-frontend', 'enqueued' ) ) {
				do_action( 'elementor/frontend/before_enqueue_styles' );
				if ( ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' ) || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
					wp_enqueue_style( 'elementor-icons' );
				}
				// wp_enqueue_style( 'elementor-animations' );
				wp_enqueue_style( 'elementor-frontend' );
				// wp_enqueue_style( 'swiper' );
				do_action( 'elementor/frontend/after_enqueue_styles' );

				/*$kit_id = \Elementor\Plugin::$instance->kits_manager->get_active_id();
				if ( $kit_id ) {
					wp_enqueue_style( 'elementor-post-' . $kit_id, wp_upload_dir()['baseurl'] . '/elementor/css/post-' . $kit_id . '.css' );
				}*/
			}

			if ( isset( \Elementor\Plugin::$instance ) ) {
				add_action(
					'wp_footer',
					function() {
						if ( ! wp_script_is( 'elementor-frontend', 'enqueued' ) ) {
							try {
								wp_enqueue_script( 'elementor-frontend' );
								$settings = \Elementor\Plugin::$instance->frontend->get_settings();
								\Elementor\Utils::print_js_config( 'elementor-frontend', 'elementorFrontendConfig', $settings );
							} catch ( Exception $e ) {
							}
						}
					}
				);
			}
		}
	}

	wp_enqueue_style( 'bootstrap' );
	wp_enqueue_style( 'porto-plugins' );
	wp_enqueue_style( 'porto-theme' );

	if ( ! empty( $porto_settings['wrapper'] ) && 'boxed' == $porto_settings['wrapper'] ) {
		wp_enqueue_style( 'porto-body-boxed', PORTO_URI . '/css/part/body-boxed.css', array( 'porto-theme' ), PORTO_VERSION );
	}

	if ( empty( $porto_settings['disable-builder-tooltip'] ) && current_user_can( 'edit_pages' ) && ! is_customize_preview() && (
		( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) &&
		( ! function_exists( 'porto_is_elementor_preview' ) || ! porto_is_elementor_preview() ) &&
		( ! function_exists( 'porto_is_vc_preview' ) || ! porto_is_vc_preview() )
		) ) {
			wp_enqueue_style( 'porto-edit-tooltip', PORTO_URI . '/css/part/edit-tooltip.css', array( 'porto-theme' ), PORTO_VERSION );
	}

	if ( ! empty( $porto_settings['logo-overlay'] ) && $porto_settings['logo-overlay']['url'] ) {
		wp_enqueue_style( 'porto-logo-overlay', PORTO_URI . '/css/part/logo-overlay.css', array( 'porto-theme' ), PORTO_VERSION );
	}

	if ( ! empty( $porto_settings['change-scrollbar'] ) ) {
		wp_enqueue_style( 'porto-scrollbar', PORTO_URI . '/css/part/scrollbar.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, '3.24', '<' ) || ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) ) {
		wp_enqueue_style( 'porto-animate', PORTO_URI . '/css/part/animate.css', array( 'porto-theme' ), PORTO_VERSION );
	}

	if ( is_active_widget( 0, 0, 'calendar' ) ) {
		wp_enqueue_style( 'porto-widget-calendar', PORTO_URI . '/css/part/widget-calendar' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( is_active_widget( 0, 0, 'contact-info-widget' ) ) {
		wp_enqueue_style( 'porto-widget-contact-info', PORTO_URI . '/css/part/widget-contact-info' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( is_active_widget( 0, 0, 'tweets-widget' ) ) {
		wp_enqueue_style( 'porto-widget-tw-tweet', PORTO_URI . '/css/part/widget-tw-tweet' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( is_active_widget( 0, 0, 'text' ) ) {
		wp_enqueue_style( 'porto-widget-text', PORTO_URI . '/css/part/widget-text.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( is_active_widget( 0, 0, 'tag_cloud' ) || is_active_widget( 0, 0, 'woocommerce_product_tag_cloud' ) ) {
		wp_enqueue_style( 'porto-widget-tag-cloud', PORTO_URI . '/css/part/widget-tag-cloud' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( is_active_widget( 0, 0, 'follow-us-widget' ) ) {
		wp_enqueue_style( 'porto-widget-follow-us', PORTO_URI . '/css/part/widget-follow-us.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	$legacy_mode = apply_filters( 'porto_legacy_mode', true );
	if ( $legacy_mode ) {
		wp_enqueue_style( 'porto-blog-legacy', PORTO_URI . '/css/part/blog-legacy' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}

	if ( 'overlay' == $porto_settings['menu-type'] ) {
		wp_enqueue_style( 'porto-popup-menu', PORTO_URI . '/css/part/popup-menu' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( ! empty( $porto_settings['side-menu-type'] ) ) {
		wp_enqueue_style( 'porto-side-menu', PORTO_URI . '/css/part/side-' . $porto_settings['side-menu-type'] . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'porto-header-shop', PORTO_URI . '/css/part/header-shop' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	
	if ( $legacy_mode ) {
		wp_enqueue_style( 'porto-header-legacy', PORTO_URI . '/css/part/header-legacy' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
		wp_enqueue_style( 'porto-footer-legacy', PORTO_URI . '/css/part/footer-legacy' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}

	if ( ! empty( $porto_settings['mobile-panel-type'] ) && 'side' == $porto_settings['mobile-panel-type'] ) {
		wp_enqueue_style( 'porto-side-nav-panel', PORTO_URI . '/css/part/side-nav-panel' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	} else {
		wp_enqueue_style( 'porto-nav-panel', PORTO_URI . '/css/part/nav-panel' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( ! empty( $porto_settings['footer-ribbon'] ) ) {
		wp_enqueue_style( 'porto-footer-ribbon', PORTO_URI . '/css/part/footer-ribbon' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( wp_is_mobile() ) {
		if ( ! empty( $porto_settings_optimize['mobile_disable_slider'] ) ) {
			wp_enqueue_style( 'porto-dm-slider', PORTO_URI . '/css/part/dm-slider' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
		}
		if ( ! empty( $porto_settings_optimize['mobile_disable_animation'] ) ) {
			wp_enqueue_style( 'porto-dm-animate', PORTO_URI . '/css/part/dm-animate.css', array( 'porto-theme' ), PORTO_VERSION, '(max-width: 767px)' );
		}
	}

	if ( ! empty( $porto_settings['sticky-header-effect'] ) && 'scrollup' == $porto_settings['sticky-header-effect'] ) {
		wp_enqueue_style( 'porto-sticky-scroll-up', PORTO_URI . '/css/part/sticky-scroll-up' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}

	if ( $legacy_mode ) {
		$footer_tooltip = apply_filters( 'porto_footer_tooltip', ! empty( $porto_settings['show-footer-tooltip'] ) ? $porto_settings['footer-tooltip'] : '' );
		$header_tooltip = apply_filters( 'porto_header_tooltip', empty( $porto_settings['show-header-tooltip'] ) ? '' : $porto_settings['header-tooltip'] );
		if ( $header_tooltip || $footer_tooltip ) {
			wp_enqueue_style( 'porto-header-footer-tooltip', PORTO_URI . '/css/part/header-footer-tooltip' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
		}
	}
	if ( class_exists( 'RevSlider' ) ) {
		wp_enqueue_style( 'porto-revslider', PORTO_URI . '/css/part/revslider' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( class_exists( 'Master_Slider' ) ) {
		wp_enqueue_style( 'porto-master-slider', PORTO_URI . '/css/part/master-slider.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( defined( 'WSP_PLUGIN_NAME' ) ) {
		wp_enqueue_style( 'porto-sitemap', PORTO_URI . '/css/part/sitemap' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	wp_enqueue_style( 'porto-media-mobile-sidebar', PORTO_URI . '/css/part/media-mobile-sidebar' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme' ), PORTO_VERSION, '(max-width:991px)' );

	if ( ! empty( $porto_settings['enable-portfolio'] ) ) {
		wp_enqueue_style( 'porto-theme-portfolio', PORTO_URI . '/css/theme_' . ( is_rtl() ? 'rtl_' : '' ) . 'portfolio.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( ! empty( $porto_settings['enable-member'] ) ) {
		wp_enqueue_style( 'porto-theme-member', PORTO_URI . '/css/theme_' . ( is_rtl() ? 'rtl_' : '' ) . 'member.css', array( 'porto-theme' ), PORTO_VERSION );
	}
	if ( ! empty( $porto_settings['enable-event'] ) ) {
		wp_enqueue_style( 'porto-theme-event', PORTO_URI . '/css/theme_' . ( is_rtl() ? 'rtl_' : '' ) . 'event.css', array( 'porto-theme' ), PORTO_VERSION );
	}

	wp_enqueue_style( 'porto-shortcodes' );

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'porto-theme-shop' );

		if ( porto_is_product() ) { // load only css on single product
			wp_enqueue_style( 'porto-only-product', PORTO_URI . '/css/part/only-product' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme-shop' ), PORTO_VERSION );
		}

		if ( $legacy_mode ) {
			wp_enqueue_style( 'porto-shop-legacy', PORTO_URI . '/css/part/shop-legacy' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme-shop' ), PORTO_VERSION );
		}

		if ( defined( 'YITH_WOOCOMPARE' ) ) {
			wp_enqueue_style( 'porto-yit-compare', PORTO_URI . '/css/part/yit-compare' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme-shop' ), PORTO_VERSION );
		}

		if ( porto_is_product_archive() || porto_is_shop() || porto_is_product() ) {
			wp_enqueue_style( 'porto-shop-sidebar-base', PORTO_URI . '/css/part/shop-sidebar-base' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme-shop' ), PORTO_VERSION );
		}

		if ( function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) {
			wp_enqueue_style( 'porto-wishlist-page', PORTO_URI . '/css/part/wishlist-page' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme-shop' ), PORTO_VERSION );
		}

		if ( is_active_widget( 0, 0, 'woocommerce_product_search' ) ) {
			wp_enqueue_style( 'porto-woo-widget-product-search', PORTO_URI . '/css/part/woo-widget-product-search.css', array( 'porto-theme-shop' ), PORTO_VERSION );
		}
		if ( is_active_widget( 0, 0, 'woocommerce_recent_reviews' ) ) {
			wp_enqueue_style( 'porto-woo-widget-recent-review-products', PORTO_URI . '/css/part/woo-widget-recent-review-products.css', array( 'porto-theme-shop' ), PORTO_VERSION );
		}

		if ( porto_is_product_archive() || porto_is_shop() ) {
			if ( is_active_widget( 0, 0, 'yith-woo-ajax-reset-navigation' ) ) {
				wp_enqueue_style( 'porto-yith-reset', PORTO_URI . '/css/part/yith-reset.css', array( 'porto-theme-shop' ), PORTO_VERSION );
			}
			if ( is_active_widget( 0, 0, 'porto_woocommerce_price_filter-widget' ) ) {
				wp_enqueue_style( 'porto-woo-widget-price-filter', PORTO_URI . '/css/part/woo-widget-porto-price-filter.css', array( 'porto-theme-shop' ), PORTO_VERSION );
			}
			if ( is_active_widget( 0, 0, 'woocommerce_price_filter' ) ) {
				wp_enqueue_style( 'porto-wooco-widget-price-filter', PORTO_URI . '/css/part/woo-widget-price-filter' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme-shop' ), PORTO_VERSION );
			}
			if ( is_active_widget( 0, 0, 'woocommerce_rating_filter' ) ) {
				wp_enqueue_style( 'porto-woo-widget-rating-filter', PORTO_URI . '/css/part/woo-widget-rating-filter' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme-shop' ), PORTO_VERSION );
			}
			if ( is_active_widget( 0, 0, 'woocommerce_layered_nav_filters' ) ) {
				wp_enqueue_style( 'porto-woo-widget-active-filters', PORTO_URI . '/css/part/woo-widget-active-filters' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme-shop' ), PORTO_VERSION );
			}
			if ( is_active_widget( 0, 0, 'woocommerce_brand_nav' ) ) {
				wp_enqueue_style( 'porto-woo-widget-brand', PORTO_URI . '/css/part/woo-widget-brand.css', array( 'porto-theme-shop' ), PORTO_VERSION );
			}

			if ( is_active_widget( 0, 0, 'yith-woo-ajax-navigation' ) ) {
				$settings = get_option( 'widget_yith-woo-ajax-navigation' );
				if ( ! empty( $settings ) ) {
					$settings = json_encode( $settings );
					if ( FALSE !== strpos( $settings, '"type":"select"' ) ) {
						wp_enqueue_style( 'porto-woo-widget-wcan-select', PORTO_URI . '/css/part/woo-widget-wcan-select' . ( is_rtl() ? '_rtl' : '' ) . '.css', array( 'porto-theme-shop' ), PORTO_VERSION );
					}
				}
			}
		}

		if ( ( function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) || ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ) {
			wp_enqueue_style( 'porto-theme-woopage' );
		}
		if ( ( is_cart() || is_checkout() ) && ( has_block( 'woocommerce/cart' ) || has_block( 'woocommerce/checkout' ) ) ) {
			wp_enqueue_style( 'porto-woo-wc-cart', PORTO_URI . '/css/part/woo-wc-cart.css', array(), PORTO_VERSION );
		}
		if ( is_singular( 'porto_builder' ) ) {
			wp_enqueue_style( 'porto-shop-toolbar' );
		}
		if ( is_singular( 'elementor_library' ) ) {
			wp_enqueue_style( 'porto-shop-toolbar' );
		}
		if ( porto_is_shop() || porto_is_product_archive() || ( function_exists( 'wcfm_is_store_page' ) && wcfm_is_store_page() ) ) {
			wp_enqueue_style( 'porto-shop-toolbar' );
		}
		if ( defined( 'YITH_WFBT_VERSION' ) ) {
			wp_enqueue_style( 'porto-shop-fbt' );
		}
		if ( defined( 'WOOCO_VERSION' ) ) {
			wp_enqueue_style( 'porto-shop-wooco' );
		}
		if ( defined( 'PWB_PLUGIN_VERSION' ) ) {
			// Perfect WooCommerce Brand Plugin
			wp_enqueue_style( 'porto-pwb-filter', PORTO_URI . '/css/theme/shop/other/pw-brand-filter' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
		}
		if ( class_exists( 'WeDevs_Dokan' ) ) {
			wp_enqueue_style( 'porto-dokan', PORTO_URI . '/css/theme_dokan' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
		}
		if ( class_exists( 'WCFMmp' ) ) {
			wp_enqueue_style( 'porto-wcfm', PORTO_URI . '/css/theme_wcfm' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
		}
		if ( class_exists( 'WOOF' ) ) {
			wp_enqueue_style( 'porto-woof', PORTO_URI . '/css/theme_woof' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
		}
		if ( class_exists( 'WooCommerce_Waitlist_Plugin' ) ) {
			wp_enqueue_style( 'porto-wcwl', PORTO_URI . '/css/theme_wcwl' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
		}
	}

	if ( class_exists( 'bbPress' ) || class_exists( 'BuddyPress' ) ) {
		wp_enqueue_style( 'porto-theme-bbpress' );
	}

	if ( defined( 'VCV_VERSION' ) ) {
		wp_enqueue_style( 'porto-theme-vc', PORTO_URI . '/css/theme_vc' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
	}
	if ( defined( 'WPB_VC_VERSION' ) ) {
		wp_enqueue_style( 'porto-theme-wpb', PORTO_URI . '/css/theme_wpb' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
	}
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		wp_enqueue_style( 'porto-theme-elementor', PORTO_URI . '/css/theme_elementor' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
	}


	if ( ! empty( $porto_settings['border-radius'] ) ) {
		wp_enqueue_style( 'porto-theme-radius', PORTO_URI . '/css/theme_radius' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
	}

	wp_enqueue_style( 'porto-dynamic-style' );

	do_action( 'porto_enqueue_css' );

	// before style internal
	porto_after_internal_css();

	if ( ! defined( 'ELEMENTOR_VERSION' ) && ! empty( $porto_settings_optimize['dequeue_wp_block_css'] ) ) {
		wp_add_inline_style( 'porto-style', '.screen-reader-text{border:0;clip-path:inset(50%);height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px;word-wrap:normal !important;}' );
	}

	/* add elementor post css after theme css */
	if ( defined( 'ELEMENTOR_VERSION' ) && is_singular() && get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) && get_post_meta( get_the_ID(), '_elementor_data', true ) ) {
		if ( 'internal' !== get_option( 'elementor_css_print_method' ) ) { // external
			if ( wp_style_is( 'elementor-post-' . intval( get_the_ID() ) ) ) {
				wp_dequeue_style( 'elementor-post-' . intval( get_the_ID() ) );
				wp_enqueue_style( 'elementor-post-' . intval( get_the_ID() ) );
			}
		} elseif ( wp_style_is( 'elementor-frontend' ) ) { // internal
			$inline_styles = wp_styles()->get_data( 'elementor-frontend', 'after' );
			if ( is_array( $inline_styles ) && ! empty( $inline_styles ) ) {
				/*$post_css = array_pop( $inline_styles );
				if ( $post_css ) {
					wp_styles()->add_data( 'elementor-frontend', 'after', $inline_styles );
					wp_add_inline_style( 'porto-style', $post_css );
				}*/
				foreach ( $inline_styles as $index => $post_css ) {
					if ( 0 === strpos( trim( $post_css ), '.elementor-' . get_the_ID() . ' ' ) ) {
						array_splice( $inline_styles, $index, 1 );
						wp_styles()->add_data( 'elementor-frontend', 'after', $inline_styles );
						wp_add_inline_style( 'porto-style', $post_css );
						break;
					}
				}
			}
		}
	}
	$is_category_filter = porto_is_ajax() && ! empty( $_REQUEST['is_category_filter'] ) && 'true' == $_REQUEST['is_category_filter'];
	// enqueue elementor block css
	if ( ! $is_category_filter && defined( 'ELEMENTOR_VERSION' ) && ! empty( $used_blocks ) && 'internal' !== get_option( 'elementor_css_print_method' ) ) {
		$upload_dir = $upload['basedir'];
		$upload_url = $upload['baseurl'];
		foreach ( $used_blocks as $block_id ) {
			$block_id = (int) $block_id;
			if ( ( ! porto_is_elementor_preview() || ! isset( $_REQUEST['elementor-preview'] ) || $_REQUEST['elementor-preview'] != $block_id ) && ! wp_style_is( 'elementor-post-' . $block_id ) ) {
				$page_assets = get_post_meta( $block_id, '_elementor_page_assets', true );
				if ( ! empty( $page_assets ) ) {
					\Elementor\Plugin::$instance->assets_loader->enable_assets( $page_assets );
				} else {
					$document = \Elementor\Plugin::$instance->documents->get( $block_id );
					if ( $document && method_exists( $document, 'update_runtime_elements' ) ) {
						$document->update_runtime_elements();
					}
				}

				$post_css_path = wp_normalize_path( $upload_dir . '/elementor/css/post-' . $block_id . '.css' );
				if ( file_exists( $post_css_path ) ) {
					wp_enqueue_style( 'elementor-post-' . $block_id, $upload_url . '/elementor/css/post-' . $block_id . '.css', array(), PORTO_VERSION );
				}

				$post_css = new Elementor\Core\Files\CSS\Post( $block_id );
				if ( $post_css ) {
					$meta = $post_css->get_meta();
					// Handle fonts.
					if ( ! empty( $meta['fonts'] ) ) {
						foreach ( $meta['fonts'] as $font ) {
							\Elementor\Plugin::$instance->frontend->enqueue_font( $font );
						}
					}

					if ( ! empty( $meta['icons'] ) ) {
						$icons_types = \Elementor\Icons_Manager::get_icon_manager_tabs();
						foreach ( $meta['icons'] as $icon_font ) {
							if ( ! isset( $icons_types[ $icon_font ] ) ) {
								continue;
							}
							\Elementor\Plugin::$instance->frontend->enqueue_font( $icon_font );
						}
					}
				}
				//$post_css = new Elementor\Core\Files\CSS\Post( $block_id );
				//$post_css->enqueue();
			}
		}
	}

	wp_enqueue_style( 'porto-style' );
	if ( is_rtl() ) {
		wp_enqueue_style( 'porto-style-rtl' );
	}

	if ( current_user_can( 'edit_theme_options' ) ) {
		// admin style
		wp_enqueue_style( 'porto_admin_bar', PORTO_CSS . '/admin_bar.css', false, PORTO_VERSION, 'all' );
	}
	porto_enqueue_revslider_css();
}

if ( ! function_exists( 'porto_include_google_font' ) ) :
	function porto_include_google_font() {
		global $porto_settings, $porto_settings_optimize;
		$gfont = array();
		$fonts = porto_settings_google_fonts();
		foreach ( $fonts as $option => $weights ) {
			if ( isset( $porto_settings[ $option . '-font' ]['google'] ) && 'false' !== $porto_settings[ $option . '-font' ]['google'] ) {
				$font = isset( $porto_settings[ $option . '-font' ]['font-family'] ) ? urlencode( $porto_settings[ $option . '-font' ]['font-family'] ) : '';
				if ( $font ) {
					$has_italic  = false;
					$font_weight = isset( $porto_settings[ $option . '-font' ]['font-weight'] ) ? $porto_settings[ $option . '-font' ]['font-weight'] : '';
					if ( $font_weight && isset( $porto_settings[ $option . '-font' ]['font-style'] ) && 'italic' == $porto_settings[ $option . '-font' ]['font-style'] ) {
						$font_weight .= 'italic';
						$has_italic   = true;
					}
					if ( $font_weight && ! in_array( $font_weight, $weights ) ) {
						$weights[] = $font_weight;
					}
					if ( isset( $gfont[ $font ] ) ) {
						foreach ( $gfont[ $font ] as $w ) {
							if ( ! in_array( $w, $weights ) ) {
								$weights[] = $w;
							}
						}
					}
					if ( $has_italic && ! in_array( '400italic', $weights ) ) {
						$weights[] = '400italic';
					}
					$gfont[ $font ] = $weights;
				}
			}
		}
		$font_family     = '';
		$font_family_arr = array();
		$loaded_fonts    = array();
		foreach ( $gfont as $font => $weights ) {
			sort( $weights );
			$font_family_arr[] = str_replace( ' ', '+', $font ) . ( empty( $weights ) ? '' : ':' . implode( ',', $weights ) );
			$loaded_fonts[]    = str_replace( '+', ' ', $font );
		}
		if ( ! empty( $font_family_arr ) ) {
			$font_family = implode( '%7C', $font_family_arr );
		}
		if ( $font_family ) {
			$charsets = array();
			if ( isset( $porto_settings['select-google-charset'] ) && $porto_settings['select-google-charset'] && isset( $porto_settings['google-charsets'] ) && $porto_settings['google-charsets'] ) {
				foreach ( $porto_settings['google-charsets'] as $charset ) {
					if ( $charset && ! in_array( $charset, $charsets ) ) {
						$charsets[] = $charset;
					}
				}
			}

			$custom_font_args = array(
				'family' => $font_family,
			);
			if ( ! empty( $charsets ) ) {
				$custom_font_args['subset'] = implode( ',', $charsets );
			}

			$google_font_url = add_query_arg( $custom_font_args, '//fonts.googleapis.com/css' );
			if ( ! empty( $porto_settings_optimize['swap'] ) ) {
				$google_font_url .= '&display=swap';
			}
			wp_register_style( 'porto-google-fonts', $google_font_url );
			if ( ! isset( $porto_settings['google-webfont-loader'] ) || ! $porto_settings['google-webfont-loader'] ) {
				wp_enqueue_style( 'porto-google-fonts' );
			}
		}

		return $loaded_fonts;
	}
endif;

function porto_register_style( $handle, $filename, $themedir = true, $load_default = true, $deps = array() ) {
	if ( $themedir ) {
		$blog_id  = porto_get_blog_id();
		$css_file = PORTO_DIR . '/css/' . $filename . '_' . $blog_id . '.css';
		$css_uri  = PORTO_URI . '/css/' . $filename . '_' . $blog_id . '.css';
	} else {
		$upload_dir = wp_upload_dir();
		$css_file   = $upload_dir['basedir'] . '/porto_styles/' . $filename . '.css';
		$css_uri    = $upload_dir['baseurl'] . '/porto_styles/' . $filename . '.css';
	}
	if ( file_exists( $css_file ) ) {
		wp_register_style( $handle, $css_uri, $deps, PORTO_VERSION );
	} elseif ( $load_default ) {
		if ( 'style-editor' == $filename ) {
			ob_start();
			require_once PORTO_DIR . '/style-editor.php';
			$css = ob_get_contents();
			ob_end_clean();
			wp_add_inline_style( 'porto-shortcodes', $css );
		} else {
			wp_register_style( $handle, PORTO_URI . '/css/' . $filename . '.css', $deps, PORTO_VERSION );
		}
	}
}

function porto_pre_scripts() {
	if ( porto_is_amp_endpoint() ) {
		return;
	}
	if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
		wp_reset_postdata();
		wp_register_script( 'isotope', PORTO_JS . '/libs/isotope.pkgd.min.js', array(), '3.0.6', true );
		wp_register_script( 'easypiechart', PORTO_JS . '/libs/easypiechart.min.js', array(), '2.1.4', true );
		wp_register_script( 'jquery-mousewheel', PORTO_JS . '/libs/jquery.mousewheel.min.js', array(), '3.1.13', true );
		wp_register_script( 'jquery-vide', PORTO_JS . '/libs/jquery.vide.min.js', array(), '0.5.1', true );
		wp_register_script( 'lazyload', PORTO_JS . '/libs/lazyload.min.js', array(), '1.9.7', true );
		wp_register_script( 'porto-theme-lazyload', PORTO_JS . '/theme-lazyload.min.js', array( 'lazyload' ), PORTO_VERSION, true );
	}
}

function porto_scripts() {
	if ( porto_is_amp_endpoint() ) {
		return;
	}
	global $porto_settings, $porto_settings_optimize;
	if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {

		// comment reply
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// load visual composer default js
		if ( ! wp_script_is( 'wpb_composer_front_js' ) ) {
			wp_enqueue_script( 'wpb_composer_front_js' );
		}
		// load ultimate addons default js
		$bsf_options             = get_option( 'bsf_options' );
		$ultimate_global_scripts = ( isset( $bsf_options['ultimate_global_scripts'] ) ) ? $bsf_options['ultimate_global_scripts'] : false;
		if ( 'enable' !== $ultimate_global_scripts ) {
			$is_ajax             = false;
			$ultimate_ajax_theme = get_option( 'ultimate_ajax_theme' );
			if ( 'enable' == $ultimate_ajax_theme ) {
				$is_ajax = true;
			}
			$ultimate_js  = get_option( 'ultimate_js', 'disable' );
			$bsf_dev_mode = ( isset( $bsf_options['dev_mode'] ) ) ? $bsf_options['dev_mode'] : false;
			if ( ( 'enable' == $ultimate_js || $is_ajax ) && ( 'enable' != $bsf_dev_mode ) ) {
				if ( ! wp_script_is( 'ultimate-script' ) ) {
					wp_enqueue_script( 'ultimate-script' );
				}
			}
		}

		$min_suffix = '';
		if ( isset( $porto_settings_optimize['minify_css'] ) && $porto_settings_optimize['minify_css'] ) {
			$min_suffix = '.min';
		}
		// porto scripts
		$optimize_suffix = '';
		if ( isset( $porto_settings_optimize['optimize_bootstrap'] ) && $porto_settings_optimize['optimize_bootstrap'] ) {
			$optimize_suffix = '.optimized';
		}
		wp_register_script( 'bootstrap', PORTO_JS . '/bootstrap' . $optimize_suffix . $min_suffix . '.js', array(), '5.0.1', true );
		wp_enqueue_script( 'bootstrap' );

		/* plugins */
		//wp_deregister_script( 'isotope' );
		wp_register_script( PORTO_WC_103_PREFIX . 'jquery-cookie', PORTO_JS . '/libs/jquery.cookie.min.js', array( 'jquery-core' ), '1.4.1', true );
		wp_register_script( 'owl.carousel', PORTO_JS . '/libs/owl.carousel.min.js', array( 'jquery-core' ), '2.3.4', true );
		wp_register_script( 'jquery-fitvids', PORTO_JS . '/libs/jquery.fitvids.min.js', array(), '1.1', true );
		wp_register_script( 'jquery-matchHeight', PORTO_JS . '/libs/jquery.matchHeight.min.js', array(), null, true );
		wp_register_script( 'modernizr', PORTO_JS . '/libs/modernizr.js', array(), '2.8.3', true );
		wp_register_script( 'jquery-magnific-popup', PORTO_JS . '/libs/jquery.magnific-popup.min.js', array( 'jquery-core', 'imagesloaded' ), '1.1.0', true );
		wp_register_script( 'jquery-selectric', PORTO_JS . '/libs/jquery.selectric.min.js', array(), '1.9.6', true );
		//wp_register_script( 'jquery-waitforimages', PORTO_JS . '/libs/jquery.waitforimages.min.js', array(), '2.0.2', true );
		wp_register_script( 'skrollr', PORTO_JS . '/libs/skrollr.min.js', array(), '0.6.30', true );
		wp_register_script( 'jquery-parallax', PORTO_JS . '/libs/jquery.parallax.min.js', array(), null, true );
		wp_register_script( 'porto-gsap', 'https://www.portotheme.com/wordpress/porto/wp-content/uploads/assets/vendor/gsap.min.js', array(), null, true );
		wp_register_script( 'porto-scroll-trigger', 'https://www.portotheme.com/wordpress/porto/wp-content/uploads/assets/vendor/ScrollTrigger.min.js', array(), null, true );
		if ( defined('PORTO_FUNC_VERSION' ) ) {
			wp_register_script( 'porto-cursor-effect', PORTO_SHORTCODES_URL . 'assets/js/porto-cursor-effect.min.js', array( 'jquery-core' ), PORTO_FUNC_VERSION, true );
		}
		wp_register_script( 'porto-bg-parallax', PORTO_JS . '/bg-parallax' . $min_suffix . '.js', array( 'jquery', 'porto-theme', 'skrollr' ), PORTO_VERSION, true );
		wp_enqueue_script( PORTO_WC_103_PREFIX . 'jquery-cookie' );
		if ( ! wp_is_mobile() || empty( $porto_settings_optimize['mobile_disable_slider'] ) ) {
			wp_enqueue_script( 'owl.carousel' );
		}
		//wp_enqueue_script( 'jquery-appear' );
		wp_enqueue_script( 'jquery-magnific-popup' );
		//wp_enqueue_script( 'jquery-waitforimages' );

		// if ( $porto_settings['show-searchform'] && isset( $porto_settings['search-cats'] ) && $porto_settings['search-cats'] ) {
		// 	wp_enqueue_script( 'jquery-selectric' );
		// }
		if ( ( isset( $porto_settings['post-layout'] ) && 'masonry' == $porto_settings['post-layout'] && is_home() || ( is_archive() && 'post' == get_post_type() ) || is_search() ) || ( is_archive() && ( 'portfolio' == get_post_type() || 'member' == get_post_type() ) ) ) {
			wp_enqueue_script( 'isotope' );
		}

		if ( class_exists( 'Woocommerce' ) ) {
			wp_register_script( 'jquery-scrollbar', PORTO_JS . '/libs/jquery.scrollbar.min.js', array(), '0.2.10', true );
			wp_register_script( 'jquery-elevatezoom', PORTO_JS . '/libs/jquery.elevatezoom.min.js', array(), '3.0.8', true );
			wp_register_script( 'jquery-fancybox', PORTO_JS . '/libs/jquery.fancybox.min.js', array(), '2.1.5', true );
			wp_register_script( 'easy-responsive-tabs', PORTO_JS . '/libs/easy-responsive-tabs.min.js', array( 'jquery' ), PORTO_VERSION, true );
		}
		if ( 'overlay' == $porto_settings['menu-type'] ) {
			if ( ! class_exists( 'Woocommerce' ) ) {
				wp_register_script( 'jquery-scrollbar', PORTO_JS . '/libs/jquery.scrollbar.min.js', array(), '0.2.10', true );
			}
			wp_enqueue_script( 'jquery-scrollbar' );
		}

		wp_register_script( 'jquery-slick', PORTO_JS . '/libs/jquery.slick.min.js', array( 'jquery' ), PORTO_VERSION, true );
		global $porto_product_layout;
		if ( class_exists( 'Woocommerce' ) && isset( $porto_product_layout ) && $porto_product_layout ) {
			if ( 'transparent' == $porto_product_layout ) {
				wp_enqueue_script( 'jquery-slick' );
			}
			if ( porto_is_product() && porto_is_elementor_preview() ) {
				wp_enqueue_script( 'jquery-slick' );
			}

			wp_enqueue_script( 'jquery-elevatezoom' );
		}

		// load porto theme js file
		wp_register_script( 'porto-theme', PORTO_JS . '/theme' . $min_suffix . '.js', array( 'jquery-core' ), PORTO_VERSION, true );
		wp_enqueue_script( 'porto-theme' );

		wp_register_script( 'porto-bar-tip', PORTO_JS . '/bar-tip.min.js', array( 'porto-theme', 'vc_waypoints' ), PORTO_VERSION, true );
		wp_register_script( 'porto-theme-fit-vd', PORTO_JS . '/theme-fit-vd.min.js', array( 'porto-theme', 'jquery-fitvids' ), PORTO_VERSION, true );
		wp_register_script( 'porto-like', PORTO_JS . '/like.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		wp_register_script( 'porto-sidebar-menu', PORTO_JS . '/sidebar-menu.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		wp_register_script( 'porto-side-nav', PORTO_JS . '/side-nav.min.js', array( 'porto-theme' ), PORTO_VERSION, true ); // side nav for side header
		if ( ! empty( $porto_settings['show-skeleton-screen'] ) ) {
			wp_register_script( 'porto-skeleton', PORTO_JS . '/skeleton.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
			wp_enqueue_script( 'porto-skeleton' );
		}
		if ( 'overlay' == $porto_settings['menu-type'] ) {
			wp_register_script( 'porto-popup-menu', PORTO_JS . '/popup-menu.min.js', array( 'porto-theme', 'jquery-scrollbar' ), PORTO_VERSION, true );
			wp_enqueue_script( 'porto-popup-menu' );
		}
		if ( is_customize_preview() ) {
			wp_enqueue_script( 'porto-side-nav' );
		}
		if ( ! empty( $porto_settings['footer-reveal'] ) ) {
			wp_enqueue_script( 'porto-footer-reveal', PORTO_JS . '/footer-reveal.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		}
		if ( ! empty( $porto_settings['show-icon-menus-mobile'] ) ) {
			wp_enqueue_script( 'porto-sticky-icon-bar', PORTO_JS . '/sticky-icon-bar.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		}
		if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) {
			wp_enqueue_script( 'porto-lazyload-menu', PORTO_JS . '/lazy-menu.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		}
		if ( current_user_can( 'edit_pages' ) ) {
			wp_enqueue_script( 'porto-edit-page', PORTO_JS . '/edit-page.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		}

		if ( wp_is_mobile() ) {
			if ( ! empty( $porto_settings_optimize['mobile_disable_animation'] ) ) {
				wp_enqueue_script( 'porto-dm-animate', PORTO_JS . '/db-animate.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
			}
		}

		wp_register_script( 'porto-sort-filters', PORTO_JS . '/sort-filters.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		wp_register_script( 'porto-advanced-button', PORTO_JS . '/advanced-button.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		wp_register_script( 'porto-circular-bar', PORTO_JS . '/circular-bar.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		wp_register_script( 'porto-loading-overlay', PORTO_JS . '/loading-overlay.min.js', array( 'porto-theme' ), PORTO_VERSION, true );

		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			wp_enqueue_script( 'porto-vc-frontend-editor', PORTO_JS . '/admin/vc-frontend-editor.js', array( 'porto-theme' ), PORTO_VERSION, true );
		}

		wp_enqueue_script( 'porto-appear-animate', PORTO_JS . '/appearanimate' . $min_suffix . '.js', array( 'jquery-core', 'porto-theme' ), PORTO_VERSION, true );
		wp_register_script( 'porto-theme-async', PORTO_JS . '/theme-async' . $min_suffix . '.js', array( 'jquery-core', 'porto-theme' ), PORTO_VERSION, true );
		wp_enqueue_script( 'porto-theme-async' );

		wp_register_script( 'porto-wishlist-offcanvas', PORTO_JS . '/wishlist.min.js', array( 'porto-theme-async' ), PORTO_VERSION, true );
		wp_register_script( 'porto-thumb-gallery', PORTO_JS . '/thumb-gallery.min.js', array( 'porto-theme-async' ), PORTO_VERSION, true );


		wp_register_script( 'porto-video-api', PORTO_JS . '/video-api.min.js', array( 'porto-theme-async' ), PORTO_VERSION, true );
		if ( ! ( empty( $porto_settings['enable-member'] ) && empty( $porto_settings['enable-portfolio'] ) ) ) {
			wp_enqueue_script( 'porto-post-ajax-modal', PORTO_JS . '/post-ajax-modal.min.js', array( 'porto-theme-async' ), PORTO_VERSION, true );
		}

		if ( ! empty( $porto_settings['enable-member'] ) ) {
			wp_enqueue_script( 'porto-member-async', PORTO_JS . '/member-async.min.js', array( 'porto-theme-async' ), PORTO_VERSION, true );
		}
		if ( ! empty( $porto_settings['enable-portfolio'] ) ) {
			wp_enqueue_script( 'porto-portfolio-async', PORTO_JS . '/portfolio-async.min.js', array( 'porto-theme-async' ), PORTO_VERSION, true );
		}

		if ( ! empty( $porto_settings['smooth-scroll'] ) ) {
			wp_enqueue_script( 'porto-smooth-scroll', PORTO_JS . '/libs/smoothscroll.min.js', array(), PORTO_VERSION, true );
		}

		wp_register_script( 'porto-kute', PORTO_JS . '/libs/kute' . $min_suffix . '.js', array( 'jquery', 'porto-theme' ), PORTO_VERSION, true );
		if ( class_exists( 'Woocommerce' ) ) {
			if ( is_product() ) {
				wp_register_script( 'porto-360-gallery', PORTO_LIB_URI . '/threesixty/threesixty.min.js', array( 'jquery' ), PORTO_VERSION, true );
				wp_enqueue_style( 'porto-360-gallery', PORTO_LIB_URI . '/threesixty/threesixty.css', array(), PORTO_VERSION );

				wp_enqueue_script( 'porto-single-product', PORTO_JS . '/shop/single-product.min.js', array( 'jquery-core' ), PORTO_VERSION, true );
			}
			wp_register_script( 'porto-woocommerce-theme', PORTO_JS . '/woocommerce-theme' . $min_suffix . '.js', array( 'imagesloaded', 'porto-theme' ), PORTO_VERSION, true );
			wp_enqueue_script( 'porto-woocommerce-theme' );
			wp_register_script( 'porto-quick-shop', PORTO_JS . '/quick-shop' . $min_suffix . '.js', array( 'porto-theme' ), PORTO_VERSION, true );

			if ( defined( 'YITH_WOOCOMPARE' ) ) {
				wp_enqueue_script( 'porto-compare', PORTO_JS . '/compare.min.js', array( 'porto-woocommerce-theme' ), PORTO_VERSION, true );
			}
			if ( ! empty( $porto_settings['woo-sales-popup'] ) && ( ! wp_is_mobile() || ! empty( $porto_settings['woo-sales-popup-mobile'] ) ) ) {
    			wp_enqueue_script( 'porto-sales-popup', PORTO_LIB_URI . '/woocommerce-sales-popup/sales-popup.min.js', array( 'porto-woocommerce-theme' ), PORTO_VERSION, true );
			}
			wp_register_script( 'porto-woo-widget', PORTO_JS . '/wc-widget.min.js', array( 'porto-woocommerce-theme' ), PORTO_VERSION, true );
			if ( porto_is_shop() || porto_is_product_archive() || ( function_exists( 'wcfm_is_store_page' ) && wcfm_is_store_page() ) ) {
				wp_enqueue_script( 'porto-shop-ajax', PORTO_JS . '/shop-ajax.min.js', array( 'porto-woocommerce-theme' ), PORTO_VERSION, true );
				wp_enqueue_script( 'porto-woo-widget' );
			}
			if ( is_product() ) { // Single product or Product Archive
				wp_enqueue_script( 'porto-woo-widget' );
			}
			wp_register_script( 'porto-one-page-category', PORTO_JS . '/one-page-category.min.js', array( 'porto-woocommerce-theme' ), PORTO_VERSION, true );
		}

		wp_register_script( 'porto-marquee', PORTO_JS . '/marquee.min.js', array( 'jquery', 'porto-theme' ), PORTO_VERSION, true );

		// compatible check with product filter plugin
		$js_wc_prdctfltr = false;
		if ( class_exists( 'WC_Prdctfltr' ) ) {
			$porto_settings['category-ajax'] = false;
			if ( get_option( 'wc_settings_prdctfltr_use_ajax', 'no' ) == 'yes' ) {
				$js_wc_prdctfltr = true;
			}
		}
		$sticky_header      = porto_get_meta_value( 'sticky_header' );
		$show_sticky_header = false;
		if ( 'no' !== $sticky_header ) {
			if ( 'yes' === $sticky_header ) {
				$show_sticky_header = true;
			} elseif ( $porto_settings['enable-sticky-header'] ) {
				$show_sticky_header = true;
			} elseif ( wp_is_mobile() && ( $porto_settings['enable-sticky-header-tablet'] || $porto_settings['enable-sticky-header-mobile'] ) ) {
				$show_sticky_header = true;
			}
		}

		global $porto_product_layout;
		$legacy_mode = apply_filters( 'porto_legacy_mode', true );
		$porto_vars  = array(
			'rtl'                       => esc_js( is_rtl() ? true : false ),
			'theme_url'                 => esc_js( get_theme_file_uri() ),
			'ajax_url'                  => esc_url( admin_url( 'admin-ajax.php' ) ),
			'cart_url'                  => function_exists( 'wc_get_cart_url' ) ? esc_url( wc_get_cart_url() ) : '',
			'change_logo'               => esc_js( empty( $porto_settings['change-header-logo'] ) ? false : true ),
			'container_width'           => esc_js( $porto_settings['container-width'] ),
			'grid_gutter_width'         => esc_js( $porto_settings['grid-gutter-width'] ),
			'show_sticky_header'        => esc_js( $show_sticky_header ),
			'show_sticky_header_tablet' => esc_js( $porto_settings['enable-sticky-header-tablet'] ),
			'show_sticky_header_mobile' => esc_js( $porto_settings['enable-sticky-header-mobile'] ),
			'ajax_loader_url'           => esc_js( str_replace( array( 'http:', 'https:' ), array( '', '' ), PORTO_URI . '/images/ajax-loader@2x.gif' ) ),
			'category_ajax'             => esc_js( isset( $porto_settings['category-ajax'] ) ? $porto_settings['category-ajax'] : false ),
			'compare_popup'             => esc_js( defined( 'YITH_WOOCOMPARE' ) && ( $legacy_mode && ! empty( $porto_settings['product-compare'] ) ) || ! $legacy_mode ),
			'compare_popup_title'       => esc_js( defined( 'YITH_WOOCOMPARE' ) && isset( $porto_settings['product-compare-title'] ) ) ? $porto_settings['product-compare-title'] : '',
			'prdctfltr_ajax'            => esc_js( $js_wc_prdctfltr ),
			'slider_loop'               => esc_js( $porto_settings['slider-loop'] ),
			'slider_autoplay'           => esc_js( $porto_settings['slider-autoplay'] ),
			'slider_autoheight'         => esc_js( $porto_settings['slider-autoheight'] ),
			'slider_speed'              => esc_js( $porto_settings['slider-speed'] ),
			'slider_nav'                => esc_js( $porto_settings['slider-nav'] ),
			'slider_nav_hover'          => esc_js( $porto_settings['slider-nav-hover'] ),
			'slider_margin'             => esc_js( $porto_settings['slider-margin'] ),
			'slider_dots'               => esc_js( $porto_settings['slider-dots'] ),
			'slider_animatein'          => isset( $porto_settings['slider-animatein'] ) ? esc_js( $porto_settings['slider-animatein'] ) : '',
			'slider_animateout'         => isset( $porto_settings['slider-animateout'] ) ? esc_js( $porto_settings['slider-animateout'] ) : '',
			'product_thumbs_count'      => esc_js( isset( $porto_settings['product-thumbs-count'] ) ? $porto_settings['product-thumbs-count'] : '' ),
			'product_zoom'              => esc_js( isset( $porto_settings['product-zoom'] ) ? $porto_settings['product-zoom'] : '' ),
			'product_zoom_mobile'       => esc_js( isset( $porto_settings['product-zoom-mobile'] ) ? $porto_settings['product-zoom-mobile'] : '' ),
			'product_image_popup'       => esc_js( isset( $porto_settings['product-image-popup'] ) ? $porto_settings['product-image-popup'] : '' ),
			'zoom_type'                 => esc_js( isset( $porto_settings['zoom-type'] ) ? $porto_settings['zoom-type'] : '' ),
			'zoom_scroll'               => esc_js( isset( $porto_settings['zoom-scroll'] ) ? $porto_settings['zoom-scroll'] : '' ),
			'zoom_lens_size'            => esc_js( isset( $porto_settings['zoom-lens-size'] ) ? $porto_settings['zoom-lens-size'] : '' ),
			'zoom_lens_shape'           => esc_js( isset( $porto_settings['zoom-lens-shape'] ) ? $porto_settings['zoom-lens-shape'] : '' ),
			'zoom_contain_lens'         => esc_js( isset( $porto_settings['zoom-contain-lens'] ) ? $porto_settings['zoom-contain-lens'] : '' ),
			'zoom_lens_border'          => esc_js( isset( $porto_settings['zoom-lens-border'] ) ? $porto_settings['zoom-lens-border'] : '' ),
			'zoom_border_color'         => esc_js( isset( $porto_settings['zoom-border-color'] ) ? $porto_settings['zoom-border-color'] : '' ),
			'zoom_border'               => esc_js( ( ! class_exists( 'WooCommerce' ) || ( isset( $porto_settings['zoom-type'] ) && 'inner' == $porto_settings['zoom-type'] ) ) ? 0 : ( isset( $porto_settings['zoom-border'] ) ? $porto_settings['zoom-border'] : 0 ) ),
			'screen_xl'                 => porto_get_xl_width(),
			'screen_xxl'                => porto_get_xl_width( false ),
			/* translators: %url%: Magnific Popup Counter Error Url */
			'mfp_counter'               => esc_js( __( '%curr% of %total%', 'porto' ) ),
			/* translators: %url%: Magnific Popup Ajax Error Url */
			'mfp_img_error'             => esc_js( __( '<a href="%url%">The image</a> could not be loaded.', 'porto' ) ),
			/* translators: %url%: Magnific Popup Ajax Error Url */
			'mfp_ajax_error'            => esc_js( __( '<a href="%url%">The content</a> could not be loaded.', 'porto' ) ),
			'popup_close'               => esc_js( __( 'Close', 'porto' ) ),
			'popup_prev'                => esc_js( __( 'Previous', 'porto' ) ),
			'popup_next'                => esc_js( __( 'Next', 'porto' ) ),
			'request_error'             => esc_js( __( 'The requested content cannot be loaded.<br/>Please try again later.', 'porto' ) ),
			'loader_text'               => esc_js( __( 'Loading...', 'porto' ) ),
			'submenu_back'              => esc_js( __( 'Back', 'porto' ) ),
			'porto_nonce'               => wp_create_nonce( 'porto-nonce' ),
			'use_skeleton_screen'       => ! empty( $porto_settings['show-skeleton-screen'] ) ? array_map( 'esc_js', $porto_settings['show-skeleton-screen'] ) : array(),
			'user_edit_pages'           => current_user_can( 'edit_pages' ),
			'quick_access'              => esc_js( __( 'Click to edit this element.', 'porto' ) ),
			'goto_type'                 => esc_js( __( 'Go To the Type Builder.', 'porto' ) ),
			'legacy_mode'               => $legacy_mode,
			'home_url'                  => esc_url( home_url( '/' ) ),
			'is_multisite'              => is_multisite(),
			'current_blog_id'           => get_current_blog_id(),
			'texts'                     => array(
				'search_history_title'     => esc_html__( 'Search History', 'porto' ),
				'search_history_clear_all' => esc_html__( 'Clear', 'porto' ),
			)
		);

		$quickview_mode = ( $legacy_mode && ! empty( $porto_settings['product-quickview'] ) ) || ! $legacy_mode;
		if ( ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( 'quickview', $porto_settings['show-skeleton-screen'] ) && class_exists( 'Woocommerce' ) && $quickview_mode ) {
			$porto_vars['quickview_skeleton'] = '<div class="quickview-wrap skeleton-body product"><div class="row"><div class="col-lg-6 summary-before"></div><div class="col-lg-6 summary entry-summary"></div></div></div>';
		}
		if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) {
			$porto_vars['lazyload_menu'] = esc_js( $porto_settings_optimize['lazyload_menu'] );
		}
		if ( class_exists( 'Woocommerce' ) ) {
			if ( is_product() ) {
				$porto_vars['pre_order'] = ! empty( $porto_settings['woo-pre-order'] );
			}
			$porto_vars['add_to_label'] = __( 'Add to cart', 'woocommerce' );
			if ( ! is_checkout() && ! is_user_logged_in() && ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) ) {
				$porto_vars['login_popup_waiting_msg'] = __( 'Please wait...', 'porto' );
			}

			if ( function_exists( 'wc_placeholder_img_src' ) ) {
				$porto_vars['wc_placeholder_img'] = wc_placeholder_img_src();
			}
			global $porto_shop_filter_layout;
			$porto_vars['shop_filter_layout'] = $porto_shop_filter_layout;
		}
		if ( defined( 'PORTO_FUNC_URL' ) ) {
			$porto_vars['func_url'] = PORTO_FUNC_URL;
		}
		if ( function_exists( 'porto_sales_popup_data' ) && class_exists( 'Woocommerce' ) && isset( $porto_settings['woo-sales-popup'] ) && ( ! wp_is_mobile() || ( isset( $porto_settings['woo-sales-popup-mobile'] ) && $porto_settings['woo-sales-popup-mobile'] ) ) ) {
			$porto_vars['sales_popup'] = porto_sales_popup_data();
		}

		wp_localize_script(
			'porto-theme',
			'js_porto_vars',
			apply_filters( 'porto_frontend_vars', $porto_vars )
		);
	}
}

/**
 * Enqueue after internal css
 *
 * @since 6.3.0
 */
function porto_after_internal_css() {
	global $porto_shop_filter_layout, $porto_settings, $porto_product_layout;

	if ( class_exists( 'Woocommerce' ) ) {
		if ( ! ( porto_vc_is_inline() || porto_is_elementor_preview() ) ) {
			if ( ! empty( $porto_shop_filter_layout ) && 'default' != $porto_shop_filter_layout ) {
				if ( 'horizontal2' == $porto_shop_filter_layout || in_array( $porto_settings['product-archive-layout'], porto_options_sidebars() ) ) {
					wp_enqueue_style( 'porto-shop-filter', PORTO_CSS . '/theme/shop/shop-filter/' . $porto_shop_filter_layout . ( is_rtl() ? '_rtl' : '' ) . '.css', false, PORTO_VERSION, 'all' );
				}
			}
		}
		if ( ! is_user_logged_in() && ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) ) {
			wp_enqueue_style( 'porto-account-login-style', PORTO_CSS . '/theme/shop/login-style/account-login' . ( is_rtl() ? '_rtl' : '' ) . '.css', false, PORTO_VERSION, 'all' );
			wp_enqueue_style( 'porto-theme-woopage' );
		}
		if ( isset( $porto_product_layout ) && ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( 'product', $porto_settings['show-skeleton-screen'] ) ) {
			wp_enqueue_style( 'porto-sp-skeleton', PORTO_CSS . '/theme/shop/single-product/skeleton.css', false, PORTO_VERSION, 'all' );
		}
		if ( porto_check_builder_condition( 'product' )  ) {
			wp_enqueue_style( 'porto-sp-layout', PORTO_CSS . '/theme/shop/single-product/builder' . ( is_rtl() ? '_rtl' : '' ) . '.css', false, PORTO_VERSION, 'all' );
		} else if ( isset( $porto_product_layout ) && 'default' != $porto_product_layout ) {
			wp_enqueue_style( 'porto-sp-layout', PORTO_CSS . '/theme/shop/single-product/' . $porto_product_layout . ( is_rtl() ? '_rtl' : '' ) . '.css', false, PORTO_VERSION, 'all' );
		}
	}
	/* post type woocommerce */
	$post_layout = isset( $porto_settings['post-layout'] ) ? $porto_settings['post-layout'] : 'full';
	if ( is_singular( 'post' ) ) {
		global $post_layout;
		$post_layout = get_post_meta( get_the_ID(), 'post_layout', true );
		$post_layout = ( 'default' == $post_layout || ! $post_layout ) ? ( isset( $porto_settings['post-content-layout'] ) ? $porto_settings['post-content-layout'] : 'large' ) : $post_layout;
	}
	$soft_mode = ! apply_filters( 'porto_legacy_mode', true );
	if ( $soft_mode ) {
		$post_layout = 'modern';
		if ( class_exists( 'PortoBuilders' ) && is_singular( PortoBuilders::BUILDER_SLUG ) ) {
			$builder_mode = get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
			if ( 'product' == $builder_mode || 'shop' == $builder_mode ) { // shop and single product elementor preview
				$post_layout = 'not_load';
			}
		}
	}
	if ( ( $soft_mode && porto_is_elementor_preview() ) || ( ( ! class_exists( 'Woocommerce' ) || ! is_woocommerce() ) && ( is_home() || is_archive() || is_search() || is_singular( 'post' ) || ( is_singular() && $soft_mode ) ) ) ) {
		if ( 'woocommerce' === $post_layout || 'modern' === $post_layout ) {
			wp_enqueue_style( 'porto-post-style', PORTO_CSS . '/theme/blog/type/' . $post_layout . ( is_rtl() ? '_rtl' : '' ) . '.css', false, PORTO_VERSION, 'all' );
		}
	}
	/* single post */
	if ( is_singular( 'post' ) || ( is_singular() && $soft_mode ) || ( $soft_mode && porto_is_elementor_preview() ) ) {
		if ( 'woocommerce' === $post_layout || 'modern' === $post_layout ) {
			wp_enqueue_style( 'porto-single-post-style', PORTO_CSS . '/theme/blog/single-post/' . $post_layout . ( is_rtl() ? '_rtl' : '' ) . '.css', false, PORTO_VERSION, 'all' );
		}
	}

	do_action( 'porto_enqueue_after_internal_css' );
}

function porto_admin_css() {
	wp_deregister_style( 'font-awesome' );
	wp_dequeue_style( 'font-awesome' );
	wp_dequeue_style( 'yith-wcwl-font-awesome' );
	if ( wp_style_is( 'rs-icon-set-fa-icon-', 'enqueued' ) ) {
		// wp_dequeue_style( 'rs-icon-set-fa-icon-' );
	}

	wp_enqueue_style( 'font-awesome', PORTO_CSS . '/font-awesome.min.css', false, PORTO_VERSION, 'all' );

	wp_dequeue_style( 'bsf-Simple-Line-Icons' );
	wp_deregister_style( 'simple-line-icons' );
	wp_dequeue_style( 'simple-line-icons' );

	// simple line icon font
	wp_register_style( 'simple-line-icons', PORTO_CSS . '/Simple-Line-Icons/Simple-Line-Icons.css', false, PORTO_VERSION, 'all' );
	// colorpicker
	porto_register_style( 'porto-css-vars', 'theme_css_vars', false, true );
	wp_enqueue_style( 'porto-css-vars' );
	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		// porto icon font
		wp_enqueue_style( 'porto-font', PORTO_CSS . '/Porto-Font/Porto-Font.css', false, PORTO_VERSION, 'all' );
		wp_enqueue_style( 'simple-line-icons' );
	} elseif ( is_customize_preview() ) {
		wp_enqueue_style( 'porto-customize-fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600%2C700' );
		wp_enqueue_style( 'simple-line-icons' );
	} elseif ( isset( $_GET['page'] ) && 'admin.php' == $pagenow && 'porto_settings' == $_GET['page'] ) {
		wp_enqueue_style( 'porto-admin-fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600%2C700' );
		wp_enqueue_style( 'simple-line-icons' );
		wp_enqueue_style( 'porto_theme_options', PORTO_CSS . '/admin_theme_options.css', false, PORTO_VERSION, 'all' );
	} elseif ( isset( $_GET['page'] ) && 'admin.php' == $pagenow && 0 === strpos( $_GET['page'], 'porto' ) ) {
		wp_enqueue_style( 'porto-admin-fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C700' );
	}

	// wp default styles
	// admin style
	$deps = array();
	if ( is_customize_preview() && get_theme_mod( 'theme_options_use_new_style', false ) ) {
		$deps[] = 'redux-fields-css';
		$deps[] = 'redux-admin-css';
	}
	wp_enqueue_style( 'porto_admin', PORTO_CSS . '/admin.min.css', $deps, PORTO_VERSION, 'all' );
	wp_enqueue_style( 'porto_admin_bar', PORTO_CSS . '/admin_bar.css', $deps, PORTO_VERSION, 'all' );
	porto_enqueue_revslider_css();
}

function porto_admin_scripts() {
	if ( function_exists( 'add_thickbox' ) ) {
		add_thickbox();
	}
	wp_enqueue_media();
	global $pagenow;
	if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'porto_settings' === $_GET['page'] && defined( 'WPB_VC_VERSION' ) && ! wp_script_is( 'vc_waypoints', 'registered' ) ) {
		wp_register_script( 'vc_waypoints', vc_asset_url( 'lib/vc/vc_waypoints/vc-waypoints.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_enqueue_script( 'vc_waypoints' );
	}
	// admin script
	wp_register_script( 'porto-admin', PORTO_JS . '/admin/admin.min.js', array( 'common', 'jquery', 'media-upload', 'thickbox', 'wp-color-picker' ), PORTO_VERSION, true );
	wp_enqueue_script( 'porto-admin' );

	/**
	 * AjaxSave on WPBakery Backend
	 *
	 * @since 6.4.0
	 */
	$wpb_backend_ajax = false;
	if ( class_exists( 'PortoBuilders' ) && defined( 'WPB_VC_VERSION' ) && ! empty( $_REQUEST['post'] ) && ( 'post-new.php' == $GLOBALS['pagenow'] || 'post.php' == $GLOBALS['pagenow'] ) ) {
		$post_type = get_post_type( $_REQUEST['post'] );
		if ( ( 'page' == $post_type ) || ( PortoBuilders::BUILDER_SLUG == $post_type ) ) {
			$wpb_backend_ajax = true;
		}
	}

	$admin_vars = array(
		'ajax_url'           => esc_url( admin_url( 'admin-ajax.php' ) ),
		'import_options_msg' => esc_js( __( 'If you want to import demo, please backup current theme options in "Import / Export" section before import. Do you want to import demo?', 'porto' ) ),
		'theme_option_url'   => esc_url( admin_url( 'admin.php?page=porto_settings' ) ),
		'wpb_backend_ajax'   => $wpb_backend_ajax,
	);
	if ( current_user_can( 'edit_theme_options' ) ) {
		$admin_vars['nonce'] = wp_create_nonce( 'porto-admin-nonce' );
	}

	if ( in_array( $pagenow, array( 'admin.php', 'customize.php' ) ) ) {
		$admin_vars['options_search_text']    = esc_js( __( 'Search for...', 'porto' ) );
		$admin_vars['header_default_options'] = json_encode( porto_header_types_default_options() );
		$admin_vars['menu_default_options']   = json_encode( porto_header_menu_default_options() );
	}

	global $porto_settings_optimize;
	$alert_model = false;
	if ( ! empty( $porto_settings_optimize['shortcodes_to_remove'] ) ) {
		$both_option = true;
		$alert_model = 'shortcode';
	}
	if ( ! empty( $porto_settings_optimize['critical_css'] ) || ! empty( $porto_settings_optimize['merge_stylesheets'] ) ) {
		if ( 'shortcode' == $alert_model ) {
			$alert_model = 'both';
		} else {
			$alert_model = 'critical';
		}
	}
	if ( $alert_model ) {
		$admin_vars['optimize_page_shortcode'] = esc_url( admin_url( 'admin.php?page=porto-speed-optimize-wizard&step=shortcodes' ) );
		$admin_vars['optimize_page_advanced']  = esc_url( admin_url( 'admin.php?page=porto-speed-optimize-wizard&step=advanced' ) );
		$admin_vars['optimize_page']           = esc_url( admin_url( 'admin.php?page=porto-speed-optimize-wizard' ) );
	}
	$admin_vars['alert_model']    = $alert_model;
	$admin_vars['legacy_mode']    = apply_filters( 'porto_legacy_mode', true );
	$admin_vars['max_input_vars'] = ini_get( 'max_input_vars' );
	$admin_vars['page_builder']   = defined( 'ELEMENTOR_VERSION' ) ? 'elementor' : ( defined( 'WPB_VC_VERSION' ) ? 'wpb' : 'gutenberg' );

	// WooCommerce plugins
	$admin_vars['installed_woo']      = defined( 'WOOCOMMERCE_VERSION' ) ? true : false;
	$admin_vars['installed_compare']  = defined( 'YITH_WOOCOMPARE' ) ? true : false;
	$admin_vars['installed_wishlist'] = defined( 'YITH_WCWL' ) ? true : false;
	$admin_vars['installed_wfbt']     = defined( 'YITH_WFBT' ) ? true : false;

	if ( 'post.php' == $GLOBALS['pagenow'] && ( ( isset( $_REQUEST['post'] ) && 'porto_builder' == get_post_type( $_REQUEST['post'] ) ) || ( isset( $_REQUEST['post_id'] ) && 'porto_builder' == get_post_type( $_REQUEST['post_id'] ) ) ) ) {
		if ( isset( $_REQUEST['post'] ) ) {
			$post_id = (int) $_REQUEST['post'];
		} else {
			$post_id = (int) $_REQUEST['post_id'];
		}
		if ( 'block' == get_post_meta( $post_id, 'porto_builder_type', true ) ) {
			$admin_vars['is_block_builder'] = true;
			$admin_vars['page_layout_url']  = admin_url( 'admin.php?page=porto-page-layouts' );
		}
	}

	if ( function_exists( 'wc_get_page_id' ) && isset( $GLOBALS['pagenow'] ) && ( 'post.php' == $GLOBALS['pagenow'] || 'post-new.php' == $GLOBALS['pagenow'] ) ) {
		if ( isset( $_REQUEST['post'] ) && $_REQUEST['post'] == wc_get_page_id( 'shop' ) ) {
			$admin_vars['is_shop_edit'] = true;
			$admin_vars['go_shop_url']  = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=shop' );
			add_filter( 'admin_body_class', function( $classes ) {
				$classes .= ' is-shop-edit';
				return $classes;
			});
		}
	}
	wp_localize_script( 'porto-admin', 'js_porto_admin_vars', apply_filters( 'porto_js_admin_vars', $admin_vars ) );

	/**
	 * Compatibility with dokan plugin
	 * WordPress color picker doesn't work because of dokan chart.
	 */
	// if ( defined( 'DOKAN_PLUGIN_VERSION' ) && ( empty( $_GET['page'] ) || 'dokan' != $_GET['page'] ) ) { // register dokan chart js only in dokan dashboard page
	// 	wp_deregister_script( 'dokan-chart' );
	// }
	// if ( defined( 'WPB_VC_VERSION' ) && ( defined( 'TYPES_VERSION' ) || defined( 'WPV_VERSION' ) ) ) {
	// 	wp_dequeue_script( 'toolset-codemirror-script' );
	// 	wp_deregister_script( 'toolset-codemirror-script' );
	// }

	if ( ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) && defined( 'TYPES_VERSION' ) ) {
		wp_enqueue_style( 'toolset-meta-html-codemirror-css' );
	}
}

function porto_enqueue_revslider_css() {
	global $porto_settings;
	$style = '';
	if ( $porto_settings['skin-color'] ) {
		$style = '.tparrows:before{color:' . esc_html( $porto_settings['skin-color'] ) . ';text-shadow:0 0 3px #fff;}';
	}
	$style .= '.revslider-initialised .tp-loader{z-index:18;}';
	wp_add_inline_style( 'rs-plugin-settings', $style );
}
// retrieves the attachment ID from the file URL
function porto_get_image_id( $image_url ) {
	global $wpdb;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $image_url ) );
	if ( isset( $attachment[0] ) ) {
		return $attachment[0];
	} else {
		return false;
	}
}
// gravityform notifications
add_filter( 'gform_validation_message', 'porto_gform_validation_message', 10, 2 );
function porto_gform_validation_message( $message, $form ) {
	return '<div class="alert alert-danger br-normal">' . porto_strip_script_tags( $message ) . '</div>';
}
add_filter( 'gform_confirmation', 'porto_gform_confirmation', 10, 4 );
function porto_gform_confirmation( $confirmation, $form, $entry, $ajax ) {
	if ( is_array( $confirmation ) ) {
		return $confirmation;
	}
	return '<div class="alert alert-success br-normal">' . $confirmation . '</div>';
}

// Fix for PHP Fatal error:  Call to undefined function YIT_Pointers() in \plugins\yith-woocommerce-wishlist\includes\class.yith-wcwl-admin-init.php
if ( function_exists( 'yith_wishlist_constructor' ) && ! class_exists( 'YIT_Pointers' ) ) {
	require_once PORTO_DIR . '/woocommerce/yit-pointers.php';
}

// Load Revslider Icon Fonts
if ( ! function_exists( 'porto_revslider_icon_fonts' ) ) :
	function porto_revslider_icon_fonts() {
		global $pe_7s_var;
		if ( $pe_7s_var ) {
			echo "<link rel='stylesheet' property='stylesheet' id='rs-icon-set-pe-7s-css' href='" . RS_PLUGIN_URL . "public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css' type='text/css' media='all' />\n";
		}
	}
endif;

/* Elementor Plugin */
if ( defined( 'ELEMENTOR_VERSION' ) ) {
	add_action( 'elementor/editor/after_enqueue_styles', 'porto_elementor_editor_styles' );

	function porto_elementor_editor_styles() {
		// porto icon font
		wp_enqueue_style( 'porto-font', PORTO_CSS . '/Porto-Font/Porto-Font.css', false, PORTO_VERSION, 'all' );
		// simple line icon font
		wp_dequeue_style( 'bsf-Simple-Line-Icons' );
		wp_deregister_style( 'simple-line-icons' );
		wp_dequeue_style( 'simple-line-icons' );
		wp_enqueue_style( 'simple-line-icons', PORTO_CSS . '/Simple-Line-Icons/Simple-Line-Icons.css', false, PORTO_VERSION, 'all' );
	}
}

/* Dequeue duplicated font awesome css */
if ( is_admin() && defined( 'WPB_VC_VERSION' ) ) {
	add_action( 'admin_print_scripts-post.php', 'porto_admin_dequeue_duplicated_css', 20 );
	add_action( 'admin_print_scripts-post-new.php', 'porto_admin_dequeue_duplicated_css', 20 );
	add_action( 'vc_base_register_admin_css', 'porto_admin_dequeue_duplicated_css', 20 );
	add_action( 'vc_frontend_editor_render', 'porto_admin_dequeue_duplicated_css', 20 );

	function porto_admin_dequeue_duplicated_css() {
		if ( wp_style_is( 'vc_font_awesome_5', 'enqueued' ) ) {
			wp_dequeue_style( 'vc_font_awesome_5' );
		}
	}
}

if ( ! function_exists( 'porto_woo_set_default_dummy_content' ) ) {
	/**
	 * Set default Woocommerce shortcode for cart and checkout pages content.
	 *
	 * @param array $pages Woocommerce pages data.
	 * @return array
	 * @since 7.1.0
	 */
	function porto_woo_set_default_dummy_content( $pages ) {
		if ( apply_filters( 'porto_woo_set_default_dummy_content', true ) ) {
			if ( isset( $pages['cart'] ) ) {
				$pages['cart']['content'] = '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->';
			}
			if ( isset( $pages['checkout'] ) ) {
				$pages['checkout']['content'] = '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->';
			}
		}

		return $pages;
	}

	add_filter( 'woocommerce_create_pages', 'porto_woo_set_default_dummy_content' );
}
