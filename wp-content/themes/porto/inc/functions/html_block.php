<?php
/**
 * Theme Functions - HTML Blocks
 *
 * @package Porto
 */

add_action( 'porto_wrapper_start', 'porto_add_html_before_wrapper' );
add_action( 'porto_before_banner', 'porto_add_html_before_banner' );
add_action( 'porto_before_content_top', 'porto_add_html_before_content_top' );
add_action( 'porto_before_content_inner_top', 'porto_add_html_content_inner_top' );
add_action( 'porto_after_content_inner_bottom', 'porto_add_html_before_content_inner_bottom' );
add_action( 'porto_after_content_bottom', 'porto_add_html_before_content_bottom' );
add_action( 'porto_after_wrapper', 'porto_add_html_after_wrapper' );

if ( ! function_exists( 'porto_add_html_before_wrapper' ) ) :
	function porto_add_html_before_wrapper() {
		global $porto_settings;
		if ( ! empty( $porto_settings['html-top'] ) ) {
			echo '<div class="porto-html-block porto-block-html-top">';
			echo do_shortcode( preg_replace( '/\[porto_block((.*?)?\])((?:(.+?)?\[\/porto_block\])?)/', '[porto_block tracking="option-html-top"${1}${3}', $porto_settings['html-top'] ) );
			echo '</div>';
		}
		$block_id = porto_check_builder_condition( 'block_top-block' );
		if ( ! empty( $block_id ) ) {
			echo do_shortcode( "[porto_block name=\"{$block_id}\" tracking=\"layout-top-block-{$block_id}\"]" );
		}
	}
endif;

if ( ! function_exists( 'porto_add_html_before_banner' ) ) :
	function porto_add_html_before_banner() {
		global $porto_settings;
		if ( ! empty( $porto_settings['html-banner'] ) ) {
			echo '<div class="porto-html-block porto-block-html-banner">';
			echo do_shortcode( preg_replace( '/\[porto_block((.*?)?\])((?:(.+?)?\[\/porto_block\])?)/', '[porto_block tracking="option-html-banner"${1}${3}', $porto_settings['html-banner'] ) );
			echo '</div>';
		}
		$block_id = porto_check_builder_condition( 'block_banner-block' );
		if ( ! empty( $block_id ) ) {
			$woo_breadcrumb = empty( $porto_settings['woo-show-default-page-header'] ) ? false : $porto_settings['woo-show-default-page-header'];
			if ( is_front_page() || ( class_exists( 'Woocommerce' ) && ( is_cart() || is_checkout() ) && $woo_breadcrumb ) ) { // Cart checkout progressive page header
	
				$el_mode = get_post_meta( $block_id, '_elementor_edit_mode', true );
				$el_data = '';
				if ( ! empty( $el_mode ) ) {
					$el_data = get_post_meta( $block_id, '_elementor_data', true );
				}
				if ( empty( $el_data ) || ! defined( 'ELEMENTOR_VERSION' ) ) {
					if ( $el_data = get_post( $block_id ) ) {
						$el_data = $el_data->post_content;
					}
				}
				if ( FALSE !== strpos( $el_data, 'porto_page_header' ) || FALSE !== strpos( $el_data, '[porto_page_header' ) ) {
					
				} else {
					echo do_shortcode( "[porto_block name=\"{$block_id}\" tracking=\"layout-banner-block-{$block_id}\"]" );
				}
	
			} else {
				echo do_shortcode( "[porto_block name=\"{$block_id}\" tracking=\"layout-banner-block-{$block_id}\"]" );
			}
		}
	}
endif;

if ( ! function_exists( 'porto_add_html_before_content_top' ) ) :
	function porto_add_html_before_content_top() {
		global $porto_settings;
		if ( ! empty( $porto_settings['html-content-top'] ) ) {
			echo '<div class="porto-html-block porto-block-html-content-top">';
			echo do_shortcode( preg_replace( '/\[porto_block((.*?)?\])((?:(.+?)?\[\/porto_block\])?)/', '[porto_block tracking="option-html-content-top"${1}${3}', $porto_settings['html-content-top'] ) );
			echo '</div>';
		}
		$block_id = porto_check_builder_condition( 'block_content-top-block' );
		if ( ! empty( $block_id ) ) {
			echo do_shortcode( "[porto_block name=\"{$block_id}\" tracking=\"layout-content-top-block-{$block_id}\"]" );
		}
	}
endif;

if ( ! function_exists( 'porto_add_html_content_inner_top' ) ) :
	function porto_add_html_content_inner_top() {
		global $porto_settings;
		if ( ! empty( $porto_settings['html-content-inner-top'] ) ) {
			echo '<div class="porto-html-block porto-block-html-content-inner-top">';
			echo do_shortcode( preg_replace( '/\[porto_block((.*?)?\])((?:(.+?)?\[\/porto_block\])?)/', '[porto_block tracking="option-html-content-inner-top"${1}${3}', $porto_settings['html-content-inner-top'] ) );
			echo '</div>';
		}
		$block_id = porto_check_builder_condition( 'block_content-inner-top-block' );
		if ( ! empty( $block_id ) ) {
			echo do_shortcode( "[porto_block name=\"{$block_id}\" tracking=\"layout-content-inner-top-block-{$block_id}\"]" );
		}
	}
endif;

if ( ! function_exists( 'porto_add_html_before_content_inner_bottom' ) ) :
	function porto_add_html_before_content_inner_bottom() {
		global $porto_settings;
		if ( ! empty( $porto_settings['html-content-inner-bottom'] ) ) {
			echo '<div class="porto-html-block porto-block-html-content-inner-bottom">';
			echo do_shortcode( preg_replace( '/\[porto_block((.*?)?\])((?:(.+?)?\[\/porto_block\])?)/', '[porto_block tracking="option-html-content-inner-bottom"${1}${3}', $porto_settings['html-content-inner-bottom'] ) );
			echo '</div>';
		}
		$block_id = porto_check_builder_condition( 'block_content-inner-bottom-block' );
		if ( ! empty( $block_id ) ) {
			echo do_shortcode( "[porto_block name=\"{$block_id}\" tracking=\"layout-content-inner-bottom-block-{$block_id}\"]" );
		}
	}
endif;

if ( ! function_exists( 'porto_add_html_before_content_bottom' ) ) :
	function porto_add_html_before_content_bottom() {
		global $porto_settings;
		if ( ! empty( $porto_settings['html-content-bottom'] ) ) {
			echo '<div class="porto-html-block porto-block-html-content-bottom">';
			echo do_shortcode( preg_replace( '/\[porto_block((.*?)?\])((?:(.+?)?\[\/porto_block\])?)/', '[porto_block tracking="option-html-content-bottom"${1}${3}', $porto_settings['html-content-bottom'] ) );
			echo '</div>';
		}

		if ( is_single() && ! empty( $porto_settings[ get_post_type() . '-content_bottom' ] ) ) {
			foreach ( explode( ',', $porto_settings[ get_post_type() . '-content_bottom' ] ) as $block ) {
				echo do_shortcode( '[porto_block name="' . esc_attr( trim( $block ) ) . '" tracking="option-' . get_post_type() . '-content_bottom"]' );
			}
		}

		$block_id = porto_check_builder_condition( 'block_content-bottom-block' );
		if ( ! empty( $block_id ) ) {
			echo do_shortcode( "[porto_block name=\"{$block_id}\" tracking=\"layout-content-bottom-block-{$block_id}\"]" );
		}
	}
endif;

if ( ! function_exists( 'porto_add_html_after_wrapper' ) ) :
	function porto_add_html_after_wrapper() {
		global $porto_settings;
		if ( ! empty( $porto_settings['html-bottom'] ) ) {
			echo '<div class="porto-html-block porto-block-html-bottom">';
			echo do_shortcode( preg_replace( '/\[porto_block((.*?)?\])((?:(.+?)?\[\/porto_block\])?)/', '[porto_block tracking="option-html-bottom"${1}${3}', $porto_settings['html-bottom'] ) );
			echo '</div>';
		}
		$block_id = porto_check_builder_condition( 'block_bottom-block' );
		if ( ! empty( $block_id ) ) {
			echo do_shortcode( "[porto_block name=\"{$block_id}\" tracking=\"layout-bottom-block-{$block_id}\"]" );
		}
	}
endif;
