<?php
/**
 * AMP Compatibility class
 *
 * @since 6.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_AMP_Compatibility {
	/**
	 * Constructor
	 *
	 * @since 6.3.0
	 */
	public function __construct() {
		add_action(
			'wp',
			function () {
				if ( porto_is_amp_endpoint() ) {
					add_filter( 'porto_skeleton_lazyload', array( $this, 'disable_skeleton_lazyload' ), 10, 1 );
					add_filter( 'the_content', array( $this, 'disable_animation' ), 10, 1 );
					add_filter( 'porto_mobile_toggle_data_attrs', array( $this, 'mobile_toggle_attrs' ), 10 );
					add_filter( 'porto_panel_overlay_data_attrs', array( $this, 'panel_overlay_attrs' ) );
					add_filter( 'porto_searchtoggle_data_attrs', array( $this, 'searchtoggle_attrs' ) );
					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
					add_action( 'wp_footer', array( $this, 'render_amp_states' ) );
					$this->disable_lazyload();
				}
			}
		);
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 6.3.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'porto-theme-amp', PORTO_URI . '/css/theme_amp' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
	}

	/**
	 * Disable skeleton lazyload
	 *
	 * @since 6.3.0
	 */
	public function disable_skeleton_lazyload( $flag ) {
		return false;
	}

	/**
	 * Disable Image and menu lazyload
	 *
	 * @since 6.3.0
	 */
	public function disable_lazyload() {
		global $porto_settings_optimize;
		if ( isset( $porto_settings_optimize ) ) {
			$porto_settings_optimize['lazyload']      = false;
			$porto_settings_optimize['lazyload_menu'] = false;
		}
	}

	/**
	 * Disable appear animation
	 *
	 * @since 6.3.0
	 */
	public function disable_animation( $content ) {
		$content = preg_replace( '/data-appear-animation="[a-zA-Z]+"/', '', $content );
		return $content;
	}

	/**
	 * Add toggle attrs.
	 * 
	 * @since 6.9.0
	 */
	public function mobile_toggle_attrs( $input ) {
		$input .= ' on="tap:AMP.setState( { portoAmpMobileOpen: ! portoAmpMobileOpen } ),htmlAmp.toggleClass(class=panel-opened)" ';
		return $input;
	}

	/**
	 * Add toggle search.
	 * 
	 * @since 6.9.0
	 */
	public function searchtoggle_attrs( $input ) {
		$input .= ' on="tap:AMP.setState( { portoAmpSearchToggleOpen: ! portoAmpSearchToggleOpen } ),htmlAmp.toggleClass(class=search-toggle-opened)" ';
		return $input;
	}

	/**
	 * Panel overlay attrs
	 * 
	 * @since 6.9.0
	 */
	public function panel_overlay_attrs( $input ) {
		$input .= ' [class]="\'panel-overlay\' + ( portoAmpMobileOpen ? \' active\' : \'\' )" ';
		return $input;
	}

	/**
	 * Add amp states to the dom.
	 * 
	 * @since 6.9.0
	 */
	public function render_amp_states() {
		echo '<amp-state id="portoAmpMobileOpen">';
		echo '<script type="application/json">false</script>';
		echo '</amp-state>';
		echo '<amp-state id="portoAmpSearchToggleOpen">';
		echo '<script type="application/json">false</script>';
		echo '</amp-state>';
	}
}

new Porto_AMP_Compatibility();
