<?php
global $porto_settings;
$hb_layout = porto_header_builder_layout();
$is_side = porto_header_type_is_side();
if ( empty( $hb_layout ) && empty( $porto_settings['elementor_pro_header'] ) ) {
	get_template_part( 'header/header_10' ); // show header type 1 instead of page header builder
	// Show tooltip to build with Porto Template Builder
	porto_add_block_tooltip();
	return;
}
?>

<header <?php echo ( $is_side ? 'data-plugin-sticky data-plugin-options="' . esc_attr( '{"hasWrap": true, "autoInit": true, "minWidth": 992, "containerSelector": ".page-wrapper","autoFit":true, "paddingOffsetTop": 0}' ) . '"' : '' );?> id="header" class="header-builder header-builder-p<?php echo ! empty( $hb_layout['ID'] ) && has_blocks( $hb_layout['ID'] ) ? ' gutenberg-hb' : '', $is_side ? ' header-side sticky-menu-header' : '', ! empty( $porto_settings['logo-overlay'] ) && $porto_settings['logo-overlay']['url'] ? ' logo-overlay-header' : ''; ?>">
<?php
if ( empty( $porto_settings['elementor_pro_header'] ) ) {
	echo do_shortcode( '[porto_block id="' . $hb_layout['ID'] . '" post_type="porto_builder" tracking="layout-header-' . $hb_layout['ID'] . '"]' );
	get_template_part( 'header/mobile_menu' );
} else {
	do_action( 'porto_elementor_pro_header_location' );
}
?>
</header>
