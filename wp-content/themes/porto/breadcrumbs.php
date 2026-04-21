<?php
global $porto_settings;

$page_header_type = porto_get_meta_value( 'porto_page_header_shortcode_type' );
$page_header_type = $page_header_type ? $page_header_type : porto_get_meta_value( 'breadcrumbs_type' );
$page_header_type = $page_header_type ? $page_header_type : ( $porto_settings['breadcrumbs-type'] ? $porto_settings['breadcrumbs-type'] : '1' );

$breadcrumbs    = $porto_settings['show-breadcrumbs'] ? porto_get_meta_value( 'breadcrumbs', true ) : false;
$page_title     = $porto_settings['show-pagetitle'] ? porto_get_meta_value( 'page_title', true ) : false;
$woo_breadcrumb = empty( $porto_settings['woo-show-default-page-header'] ) ? false : $porto_settings['woo-show-default-page-header'];
if ( ( is_front_page() && is_home() ) || is_front_page() ) {
	$breadcrumbs = false;
	$page_title  = false;
}
/**
 * Hide if page header(banner) block is existed
 * 
 * @since 7.6.0
 */
$page_header_id = porto_check_builder_condition( 'block_banner-block' );
if ( $page_header_id ) {
	$el_mode = get_post_meta( $page_header_id, '_elementor_edit_mode', true );
	$el_data = '';
	if ( ! empty( $el_mode ) ) {
		$el_data = get_post_meta( $page_header_id, '_elementor_data', true );
	}
	if ( empty( $el_data ) || ! defined( 'ELEMENTOR_VERSION' ) ) {
		if ( $el_data = get_post( $page_header_id ) ) {
			$el_data = $el_data->post_content;
		}
	}
	if ( FALSE !== strpos( $el_data, 'porto_page_header' ) || FALSE !== strpos( $el_data, '[porto_page_header' ) ) {
		$breadcrumbs    = false;
		$page_title     = false;
		// $woo_breadcrumb = false;
	}
}
?>
<?php
if ( class_exists( 'Woocommerce' ) && ( is_cart() || is_checkout() ) && $woo_breadcrumb ) :
	?>
	<div class="woo-page-header page-header-8">
		<ul class="breadcrumb text-center">
			<li class="<?php echo is_cart() ? esc_attr( 'current' ) : ''; ?>">
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Shopping Cart', 'porto' ); ?></a>
			</li>
			<li class="<?php echo is_checkout() && ! is_order_received_page() ? esc_attr( 'current' ) : ''; ?>">
				<i class="delimiter delimiter-2"></i>
				<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Checkout', 'woocommerce' ); ?></a>
			</li>
			<li class="<?php echo is_order_received_page() ? esc_attr( 'current' ) : esc_attr( 'disable' ); ?>">
				<i class="delimiter delimiter-2 d-block"></i>
				<a href="#" class="nolink"><?php esc_html_e( 'Order Complete', 'porto' ); ?></a>
			</li>
		</ul>
	</div>
	<?php
elseif ( $breadcrumbs || $page_title ) :
	if ( $porto_settings['breadcrumbs-parallax'] ) {
		wp_enqueue_script( 'skrollr' );
		wp_enqueue_script( 'porto-bg-parallax' );
	}
	?>
	<?php if ( 'boxed' != porto_get_wrapper_type() && 'boxed' == $porto_settings['breadcrumbs-wrapper'] ) : ?>
		<div id="breadcrumbs-boxed">
	<?php endif; ?>
	<section class="page-top<?php echo 'wide' == $porto_settings['breadcrumbs-wrapper'] ? ' wide' : ''; ?> page-header-<?php echo esc_attr( $page_header_type ); ?><?php echo isset( $porto_settings['breadcrumbs-css-class'] ) && $porto_settings['breadcrumbs-css-class'] ? ' ' . esc_attr( $porto_settings['breadcrumbs-css-class'] ) : ''; ?>"<?php echo ! $porto_settings['breadcrumbs-parallax'] ? '' : ' data-plugin-parallax data-plugin-options="{&quot;speed&quot;: ' . esc_attr( $porto_settings['breadcrumbs-parallax-speed'] ) . '}"'; ?>>
	<?php get_template_part( 'page_header/page_header_' . sanitize_file_name( $page_header_type ) ); ?>
	</section>
	<?php if ( 'boxed' != porto_get_wrapper_type() && 'boxed' == $porto_settings['breadcrumbs-wrapper'] ) : ?>
		</div>
	<?php endif; ?>
<?php elseif ( is_customize_preview() ) : ?>
	<section class="page-top d-none"></section>
<?php endif; ?>
