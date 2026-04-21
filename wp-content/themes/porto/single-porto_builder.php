<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
	global $porto_settings, $porto_layout, $porto_shop_filter_layout;
	wp_reset_postdata();
	$use_theme = false;
if ( have_posts() ) :
	the_post();
	$terms     = wp_get_post_terms( get_the_ID(), 'porto_builder_type', array( 'fields' => 'names' ) );
	$is_header = isset( $terms[0] ) && 'header' == $terms[0];
	$is_footer = isset( $terms[0] ) && 'footer' == $terms[0];
	$use_theme = isset( $terms[0] ) && ( 'product' == $terms[0] || 'shop' == $terms[0] || 'archive' == $terms[0] || 'single' == $terms[0] );
	$container = get_post_meta( get_the_ID(), 'container', true );
	if ( ! $container ) {
		if ( $is_header || $is_footer ) {
			$container = 'fluid';
		}
	}

	if ( ! empty( $terms[0] ) && 'shop' == $terms[0] ) {
		$porto_shop_filter_layout = ! empty( $porto_shop_filter_layout ) ? $porto_shop_filter_layout : $porto_settings['product-archive-filter-layout'];
		if ( ! empty( $porto_shop_filter_layout ) || in_array( $porto_layout, porto_options_sidebars() ) ) {
			echo '<style>';
			if ( in_array( $porto_layout, porto_options_sidebars() ) ) {
				echo '.shop-builder .sidebar .sidebar-content { position: relative; }';
				echo '.shop-builder .sidebar .porto-shop-template-sidebar { position: absolute; display: flex; align-items: center; justify-content: center; font-size: 60px; background-color: #00000011; width: 100%; height: 100%; left: 0; top: 0; z-index: 999; }';
			}
			if ( ! empty( $porto_shop_filter_layout ) ) {
				$left    = is_rtl() ? 'right' : 'left';
				$right   = is_rtl() ? 'left' : 'right';
				$is_wide = ( 'wide' == porto_get_wrapper_type() || porto_is_wide_layout() );
				if ( $is_wide ) :
					echo '@media (min-width: 1500px) {';
						echo '.shop-builder .left-sidebar.col-lg-3,';
						echo '.shop-builder .right-sidebar.col-lg-3 { width: 20%; }';
					if ( ( 'wide-left-sidebar' != $porto_layout && 'wide-right-sidebar' != $porto_layout ) || ( 'offcanvas' != $porto_shop_filter_layout ) ) :
						echo '.shop-builder .main-content.col-lg-9 { width: 80%; }';
						echo '.shop-builder .main-content.col-lg-6 { width: 60%; }';
					endif;
					echo '}';
				endif;

				echo 'div.sidebar-toggle { display: none !important; }';
				if ( 'horizontal' === $porto_shop_filter_layout ) {
					if ( $is_wide && in_array( $porto_layout, porto_options_sidebars() ) ) :
						echo '@media (min-width: 1500px) {';
						echo '.shop-builder .main-content-wrap .left-sidebar {' . porto_filter_output( $left ) . ': -20%; }';
						echo '.shop-builder .main-content-wrap .right-sidebar{' . porto_filter_output( $right ) . ': -20%; }';
						echo '.shop-builder .main-content-wrap:not(.opened) .main-content { margin-' . porto_filter_output( $left ) . ': -20%; }}';
					endif;
				} elseif ( 'horizontal2' === $porto_shop_filter_layout ) {
					echo '@media (min-width: 992px) and (max-width:' . ( (int) $porto_settings['container-width'] + (int) $porto_settings['grid-gutter-width'] - 1 ) . 'px) {';
					echo '.shop-builder .porto-product-filters.widget-title,.shop-builder .woocommerce-ordering select { width: 140px; }}';
				} elseif ( 'offcanvas' === $porto_shop_filter_layout && in_array( $porto_layout, porto_options_sidebars() ) ) {
					if ( in_array( $porto_layout, porto_options_both_sidebars() ) ) {
						echo '@media (min-width: 768px) {';
						echo '.shop-builder .main-content { flex: 0 0 auto; width: 66.6666% }}';
						echo '@media (min-width: 992px) {';
						echo '.shop-builder .main-content { flex: 0 0 auto; width: 75% } }';
					} else {
						echo '.shop-builder .main-content { flex: 0 0 auto; width: 100% }';
					}
					if ( 'wide-both-sidebar' == $porto_layout ) :
						echo '@media (min-width: 1500px) {';
						echo '.shop-builder .main-content.col-lg-6 { width: 80% }';
						echo '.shop-builder .right-sidebar.col-lg-3 { width: 20% } }';
					endif;
				}
			}

			echo '</style>';
		}

		if ( ! empty( $porto_shop_filter_layout ) && 'default' != $porto_shop_filter_layout ) {
			if ( in_array( $porto_layout, porto_options_sidebars() ) || 'horizontal2' == $porto_shop_filter_layout ) {
				wp_enqueue_style( 'porto-shop-filter', PORTO_CSS . '/theme/shop/shop-filter/' . $porto_shop_filter_layout . ( is_rtl() ? '_rtl' : '' ) . '.css', false, PORTO_VERSION, 'all' );
			}
		}
		wp_enqueue_style( 'porto-theme-shop', PORTO_CSS . '/theme_shop.css', array(), PORTO_VERSION );
	}

	if ( $use_theme ) {
		get_template_part( 'header/header_before' );
	}

	if ( $is_header ) {
		wp_enqueue_script( 'porto-side-nav' );
		$is_side = 'side' == get_post_meta( get_the_ID(), 'header_type', true );
		echo '<header id="header" class="header-builder header-builder-p' . ( $is_side ? ' header-side header-side-nav side-nav-wrap' : '' ) . '">';
	} elseif ( $is_footer ) {
		echo '<footer id="footer" class="footer footer-builder">';
	}

	if ( $use_theme ) {
		the_content();
	} else {
		echo '<div class="page-wrapper">';
		if ( 'fluid' == $container ) {
			echo '<div class="container-fluid">';
		} elseif ( 'yes' == $container ) {
			echo '<div class="container">';
		}
		if ( ! empty( $terms[0] ) && 'type' == $terms[0] ) {
			$content_type       = get_post_meta( get_the_ID(), 'content_type', true );
			$content_type_value = $content_type ? get_post_meta( get_the_ID(), 'content_type_' . $content_type, true ) : '';
			$preview_width      = get_post_meta( get_the_ID(), 'preview_width', true );
			if ( ! $preview_width ) {
				$preview_width = 360;
			}
			echo '<div class="porto-tb-preview mx-auto" style="margin-top: 3rem; max-width: ' . absint( $preview_width ) . 'px">';

			if ( function_exists( 'porto_shortcode_template' ) && ( $template = porto_shortcode_template( 'porto_posts_grid' ) ) ) {
				$atts = array(
					'builder_id' => get_the_ID(),
					'count'      => 1,
					'columns'    => 1,
				);
				if ( 'term' == $content_type ) {
					$atts['source'] = 'terms';
					$atts['tax']    = $content_type_value;
				} else {
					$atts['post_type'] = $content_type;
					if ( $content_type_value ) {
						$atts['ids'] = array( (int) $content_type_value );
					}
				}
				include $template;
			}

			echo '</div>';
		} else {
			the_content();
		}
		if ( 'yes' == $container || 'fluid' == $container ) {
			echo '</div>';
		}
		if ( $is_header ) {
			echo '</header>';
		} elseif ( $is_footer ) {
			echo '</footer>';
		}
		echo '</div>';
	}
endif;

if ( $use_theme ) {
	get_footer();
} else {
	if ( $is_header ) {
		if ( isset( $porto_settings['mobile-panel-type'] ) && 'side' === $porto_settings['mobile-panel-type'] ) {
			// navigation panel
			get_template_part( 'panel' );
		}
	}
	wp_footer();
	echo '</body></html>';
}
