<?php
/**
 * Branding Styles File Doc Comment
 *
 * @category Branding Styles
 * @package   gdpr-cookie-compliance
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$primary_colour   = $content->primary_colour;
$secondary_colour = $content->secondary_colour;
$button_bg        = $content->button_bg;
$button_hover_bg  = $content->button_hover_bg;
$button_font      = $content->button_font;
$font_family      = $content->font_family;

$moove_gdpr_cnt = new Moove_GDPR_Controller();
echo $moove_gdpr_cnt->get_minified_styles( // phpcs:ignore
	esc_attr( $primary_colour ),
	esc_attr( $secondary_colour ),
	esc_attr( $button_bg ),
	esc_attr( $button_hover_bg ),
	esc_attr( $button_font ),
	esc_attr( $font_family )
); // phpcs:ignore

