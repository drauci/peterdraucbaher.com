<?php
global $porto_layout, $porto_settings;
if ( empty( $porto_layout ) ) {
	$porto_layout = porto_meta_layout();
	$porto_layout = $porto_layout[0];
}

if ( 'header_builder' == $porto_settings['header-type-select'] ) {
	get_template_part( 'header/header_builder' );
	// Show tooltip to build with Porto Template Builder
	porto_add_block_tooltip();
} elseif ( 'header_builder_p' == $porto_settings['header-type-select'] ) {
	get_template_part( 'header/header_builder_p' );
} else {
	get_template_part( 'header/header_' . porto_get_header_type() );
	// Show tooltip to build with Porto Template Builder
	porto_add_block_tooltip();
}
