<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $el_id
 * @var $css_animation
 * @var $css
 * @var $content - shortcode content
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Column_text $this
 */
$el_class = $el_id = $css = $css_animation = '';

$original_atts = $atts;
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$element_class = empty( $this->settings['element_default_class'] ) ? '' : $this->settings['element_default_class'];
$class_to_filter = 'wpb_text_column ' . esc_attr( $element_class ) . $this->getCSSAnimation( $css_animation );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}

// shortcode class
if ( class_exists( 'WPBMap' ) ) {
	$sc = WPBMap::getShortCode( 'vc_column_text' );
	if ( ! empty( $sc['params'] ) && class_exists( 'PortoShortcodesClass' ) && method_exists( 'PortoShortcodesClass', 'get_global_hashcode' ) ) {
		foreach ( $original_atts as $key => $item ) {
			if ( false !== strpos( $item, '"' ) ) {
				$original_atts[ $key ] = str_replace( '"', '``', $item );
			}
		}
		$css_class   .= ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode( $original_atts, 'vc_column_text', $sc['params'] );
		$internal_css = PortoShortcodesClass::generate_wpb_css( 'vc_column_text', $original_atts );
	}
}

$output = '
	<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $wrapper_attributes ) . '>' . ( ! empty( $internal_css ) ? '<style>' . wp_strip_all_tags( $internal_css ) . '</style>' : '' ) . '
		<div class="wpb_wrapper">
			' . wpb_js_remove_wpautop( $content, true ) . '
		</div>
	</div>
';

echo porto_filter_output( $output );
