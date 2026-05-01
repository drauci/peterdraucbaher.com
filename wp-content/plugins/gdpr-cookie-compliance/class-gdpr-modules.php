<?php
/**
 * GDPR_Modules File Doc Comment
 *
 * @category GDPR_Modules
 * @package   gdpr-cookie-compliance
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * GDPR_Modules Class Doc Comment
 *
 * @category Class
 * @package  Moove_Modules
 * @author   Moove Agency
 */
class GDPR_Modules {
	/**
	 * GDPR Options.
	 *
	 * @var array $gdpr_options GDPR Options.
	 */
	protected $gdpr_options;

	/**
	 * Language code.
	 *
	 * @var string $wpml_lang Language code.
	 */
	protected $wpml_lang;

	/**
	 * Construct function
	 */
	public function __construct() {
		$gdpr_default_content = new Moove_GDPR_Content();
		$option_name          = $gdpr_default_content->moove_gdpr_get_option_name();
		$modal_options        = get_option( $option_name );
		$wpml_lang            = $gdpr_default_content->moove_gdpr_get_wpml_lang();
		$this->gdpr_options   = $modal_options;
		$this->wpml_lang      = $wpml_lang;
	}

	/**
	 * GDPR Module - Floating Button view & content
	 */
	public function get_floating_button() {
		$view_controller            = new GDPR_Modules_View();
		$modal_options              = $this->gdpr_options;
		$wpml_lang                  = $this->wpml_lang;
		$floating_button_visibility = 'display: none;';
		$floating_button_class      = '';
		$infobar_hidden             = isset( $modal_options['moove_gdpr_infobar_visibility'] ) && 'hidden' === $modal_options['moove_gdpr_infobar_visibility'] ? true : false;
		if ( $infobar_hidden ) :
			$floating_button_class = 'button-visible';
		endif;
		$floating_button_position = apply_filters( 'gdpr_floating_button_position', false );
		$data                     = new stdClass();
		$data->options            = $modal_options;
		$data->wpml_lang          = $wpml_lang;
		$data->is_enabled         = ( isset( $modal_options['moove_gdpr_floating_button_enable'] ) && 1 === intval( $modal_options['moove_gdpr_floating_button_enable'] ) ) ? true : false;
		if ( defined( 'IFRAME_REQUEST' ) && IFRAME_REQUEST ) :
			$data->is_enabled = false;
		endif;
		$data->is_enabled = apply_filters( 'gdpr_template_html_load', $data->is_enabled );
		$data->styles     = $floating_button_visibility . $floating_button_position;
		$data->class      = $floating_button_class;
		if ( $floating_button_position ) :
			$data->class = $floating_button_class .= ' gdpr-floating-button-custom-position';
		endif;

		$mobile_hide = ( isset( $modal_options['moove_gdpr_floating_mobile'] ) && 1 === intval( $modal_options['moove_gdpr_floating_mobile'] ) ) ? true : false;
		if ( $mobile_hide ) :
			$data->class = $floating_button_class .= ' gdpr-fb-hide-mobile';
		endif;

		$data->label = ( isset( $modal_options[ 'moove_gdpr_floating_button_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_floating_button_label' . $wpml_lang ] ) ? $modal_options[ 'moove_gdpr_floating_button_label' . $wpml_lang ] : __( 'Change cookie settings', 'gdpr-cookie-compliance' );
		$data->label = esc_attr( $data->label );
		return $view_controller->load( 'infobar.floating-button', $data );
	}

	/**
	 * GDPR Module - Base structure
	 */
	public function get_modal_base() {
		$view_controller     = new GDPR_Modules_View();
		$modal_options       = $this->gdpr_options;
		$wpml_lang           = $this->wpml_lang;
		$layout              = isset( $modal_options['moove_gdpr_plugin_layout'] ) ? $modal_options['moove_gdpr_plugin_layout'] : 'v1';
		$tab_title           = isset( $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] : __( 'Privacy Overview', 'gdpr-cookie-compliance' );
		$tab_title           = esc_attr( $tab_title );
		$data                = new stdClass();
		$data->logo_position = apply_filters( 'gdpr_logo_position', 'left' );
		$data->theme         = 'moove_gdpr_modal_theme_' . $layout;
		$modal_theme         = 'moove_gdpr_modal_theme_' . $layout;
		$data->modal_title   = 'v1' === $layout ? false : $tab_title;
		$view_type           = 'v1' === $layout ? 'tabs' : 'onepage';
		$close_button_v      = true;

		$gdpr_gs_buttons_order = isset( $modal_options['gdpr_gs_buttons_order'] ) ? json_decode( $modal_options['gdpr_gs_buttons_order'], true ) : array( 'enable', 'reject', 'save', 'close' );

		if ( is_array( $gdpr_gs_buttons_order ) && in_array( 'close', $gdpr_gs_buttons_order ) && isset( $modal_options['moove_gdpr_cb_close_button_enable'] ) && 0 === intval( $modal_options['moove_gdpr_cb_close_button_enable'] ) ) : // phpcs:ignore
			$close_button_v = false;
		endif;
		$data->show = true;
		if ( defined( 'IFRAME_REQUEST' ) && IFRAME_REQUEST ) :
			$data->show = false;
		endif;
		$data->show  = apply_filters( 'gdpr_template_html_load', $data->show );
		$data->close = $close_button_v;
		return $view_controller->load( 'modal.modal-base-' . $view_type, $data );
	}

	/**
	 * GDPR Module - Infobar structure
	 */
	public function get_infobar_base() {
		$view_controller = new GDPR_Modules_View();
		$modal_options   = $this->gdpr_options;
		$wpml_lang       = $this->wpml_lang;
		$data            = new stdClass();
		$infobar_classes = array(
			'moove-gdpr-info-bar-hidden',
			'moove-gdpr-align-center',
		);
		$modal_scheme    = isset( $modal_options['moove_gdpr_colour_scheme'] ) ? ( ( 1 === intval( $modal_options['moove_gdpr_colour_scheme'] ) || 2 === intval( $modal_options['moove_gdpr_colour_scheme'] ) ) ? intval( $modal_options['moove_gdpr_colour_scheme'] ) : 1 ) : 1;

		$scheme_class      = 2 === $modal_scheme ? 'moove-gdpr-light-scheme' : 'moove-gdpr-dark-scheme';
		$infobar_position  = isset( $modal_options['moove_gdpr_infobar_position'] ) ? $modal_options['moove_gdpr_infobar_position'] : 'bottom';
		$infobar_classes[] = $scheme_class;
		$infobar_classes[] = 'gdpr_infobar_postion_' . $infobar_position;
		$infobar_classes   = apply_filters( 'gdpr_info_bar_class_extension', $infobar_classes );
		$infobar_hidden    = isset( $modal_options['moove_gdpr_infobar_visibility'] ) && 'hidden' === $modal_options['moove_gdpr_infobar_visibility'] ? true : false;
		$data->show        = $infobar_hidden ? false : true;
		if ( defined( 'IFRAME_REQUEST' ) && IFRAME_REQUEST ) :
			$data->show = false;
		endif;

		$data->show = apply_filters( 'gdpr_template_html_load', $data->show );

		$gdpr_cb_show_mobile = isset( $modal_options['gdpr_cb_show_mobile'] ) && intval( $modal_options['gdpr_cb_show_mobile'] ) >= 0 ? intval( $modal_options['gdpr_cb_show_mobile'] ) : apply_filters( 'gdpr_hide_banner_on_mobile', 1 );

		if ( 0 === $gdpr_cb_show_mobile && function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) :
			$data->show = false;
		endif;

		$data->class     = implode( ' ', $infobar_classes );
		$infobar_content = apply_filters( 'gdpr_info_bar_popup_content', $view_controller->load( 'infobar.infobar-base', $data ) );
		return $infobar_content;

	}

	/**
	 * GDPR Module - Infobar Main Content
	 */
	public function get_infobar_content() {
		$view_controller = new GDPR_Modules_View();
		$modal_options   = $this->gdpr_options;
		$wpml_lang       = $this->wpml_lang;
		$_content        = '<p>' . esc_html__( 'We are using cookies to give you the best experience on our website.', 'gdpr-cookie-compliance' ) . '</p>';
		$_content       .= '<p>' . sprintf( esc_html__( 'You can find out more about which cookies we are using or switch them off in [%1$s]settings[/%2$s].', 'gdpr-cookie-compliance' ), 'setting', 'setting' ) . '</p>'; // phpcs:ignore

		$content = isset( $modal_options[ 'moove_gdpr_info_bar_content' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_info_bar_content' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_info_bar_content' . $wpml_lang ] : $_content;

		$content = wp_kses_post( $content );

		$tabindex           = apply_filters( 'gdpr_tabindex_attribute', '', '0' );
		$content            = str_replace( '[setting]', '{setting}', $content );
		$content            = str_replace( '[/setting]', '{/setting}', $content );
		$content            = str_replace( '[accept]', '{accept}', $content );
		$content            = str_replace( '[/accept]', '{/accept}', $content );
		$content            = str_replace( '[reject]', '{reject}', $content );
		$content            = str_replace( '[/reject]', '{/reject}', $content );

		$content            = str_replace( '{setting}', '<button ' . $tabindex . ' aria-haspopup="true" data-href="#moove_gdpr_cookie_modal" class="change-settings-button">', $content );
		$content            = str_replace( '{/setting}', '</button>', $content );
		$content            = apply_filters( 'gdpr_info_bar_notice_content', $content );
		$data               = new stdClass();
		$data->text_content = $content;
		return $view_controller->load( 'infobar.infobar-content', $data );
	}

	/**
	 * GDPR Module - Infobar button & extensions
	 */
	public function get_infobar_buttons() {
		$view_controller    = new GDPR_Modules_View();
		$modal_options      = $this->gdpr_options;
		$wpml_lang          = $this->wpml_lang;
		$data               = new stdClass();
		$has_accept         = isset( $modal_options['moove_gdpr_accept_button_enable'] ) ? ( intval( $modal_options['moove_gdpr_accept_button_enable'] ) === 1 ? true : ( ! isset( $modal_options['moove_gdpr_accept_button_enable'] ) ? true : false ) ) : true;
		$data->has_accept   = $has_accept;
		$data->button_label = isset( $modal_options[ 'moove_gdpr_infobar_accept_button_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_infobar_accept_button_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_infobar_accept_button_label' . $wpml_lang ] : __( 'Accept', 'gdpr-cookie-compliance' );
		$data->button_label = esc_attr( $data->button_label );
		$buttons_order      = isset( $modal_options['gdpr_bs_buttons_order'] ) ? json_decode( $modal_options['gdpr_bs_buttons_order'], true ) : array( 'accept', 'reject', 'settings', 'close' );
		$data->accept_order = in_array( 'accept', $buttons_order ) ? array_search( 'accept', $buttons_order ) : 'auto'; // phpcs:ignore
		return $view_controller->load( 'infobar.infobar-buttons', $data );

	}

	/**
	 * GDPR Module - Company Logo
	 */
	public function get_company_logo() {
		$view_controller   = new GDPR_Modules_View();
		$modal_options     = $this->gdpr_options;
		$wpml_lang         = $this->wpml_lang;
		$data              = new stdClass();
		$data->options     = $modal_options;
		$data->wpml_lang   = $wpml_lang;
		$data->logo_url    = isset( $modal_options['moove_gdpr_company_logo'] ) && $modal_options['moove_gdpr_company_logo'] ? $modal_options['moove_gdpr_company_logo'] : plugin_dir_url( __FILE__ ) . 'dist/images/gdpr-logo.png';
		$data->logo_url    = str_replace( plugin_dir_url( __FILE__ ) . 'dist/images/moove-logo.png', plugin_dir_url( __FILE__ ) . 'dist/images/gdpr-logo.png', $data->logo_url );
		$logo_details      = gdpr_get_logo_details( $data->logo_url, $modal_options );
		$data->logo_alt    = gdpr_get_logo_alt( $data->logo_url, $modal_options );
		$data->logo_width  = isset( $logo_details['width'] ) ? esc_attr( $logo_details['width'] ) : false;
		$data->logo_height = isset( $logo_details['height'] ) ? esc_attr( $logo_details['height'] ) : false;
		$data->logo_url    = isset( $logo_details['logo_url'] ) ? esc_attr( $logo_details['logo_url'] ) : esc_attr( $data->logo_url );
		$data->logo_url    = apply_filters( 'gdpr_cc_modal_logo_url', $data->logo_url );
		return $view_controller->load( 'modal.company-logo', $data );
	}

	/**
	 * GDPR Module - Branding Options
	 */
	public function get_gdpr_branding() {
		$view_controller   = new GDPR_Modules_View();
		$moove_actions_cnt = new Moove_GDPR_Actions();
		$modal_options     = $this->gdpr_options;
		$wpml_lang         = $this->wpml_lang;
		$data              = new stdClass();
		$data->options     = $modal_options;
		$data->wpml_lang   = $wpml_lang;
		$data->text        = $moove_actions_cnt->moove_gdpr_footer_branding_content();
		$data->is_enabled  = isset( $modal_options['moove_gdpr_modal_powered_by_disable'] ) && 1 === intval( $modal_options['moove_gdpr_modal_powered_by_disable'] ) ? false : true;
		return $view_controller->load( 'modal.gdpr-branding', $data );
	}

	/**
	 * GDPR Module - Privacy Overview Content
	 */
	public function get_section_overview() {
		$view_controller      = new GDPR_Modules_View();
		$modal_options        = $this->gdpr_options;
		$wpml_lang            = $this->wpml_lang;
		$gdpr_default_content = new Moove_GDPR_Content();
		$layout               = isset( $modal_options['moove_gdpr_plugin_layout'] ) ? $modal_options['moove_gdpr_plugin_layout'] : 'v1';
		$tab_title            = isset( $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] : __( 'Privacy Overview', 'gdpr-cookie-compliance' );
		$tab_title            = esc_attr( $tab_title );
		$tab_content          = isset( $modal_options[ 'moove_gdpr_privacy_overview_tab_content' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_privacy_overview_tab_content' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_privacy_overview_tab_content' . $wpml_lang ] : $gdpr_default_content->moove_gdpr_get_privacy_overview_content();

		$data              = new stdClass();
		$data->options     = $modal_options;
		$data->wpml_lang   = $wpml_lang;
		$data->tab_title   = 'v1' === $layout ? $tab_title : false;
		$data->tab_content = wp_kses_post( wpautop( $tab_content ) );
		$data->visibility  = 'v1' === $layout ? 'style="display:none"' : '';

		return $view_controller->load( 'modal.content-sections.overview', $data );

	}

	/**
	 * GPDR Module - Strictly Necessary Cookies
	 */
	public function get_section_strictly() {
		$view_controller              = new GDPR_Modules_View();
		$modal_options                = $this->gdpr_options;
		$wpml_lang                    = $this->wpml_lang;
		$gdpr_default_content         = new Moove_GDPR_Content();
		$layout                       = isset( $modal_options['moove_gdpr_plugin_layout'] ) ? $modal_options['moove_gdpr_plugin_layout'] : 'v1';
		$tab_title                    = isset( $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] : __( 'Strictly Necessary Cookies', 'gdpr-cookie-compliance' );
		$tab_content                  = isset( $modal_options[ 'moove_gdpr_strict_necessary_cookies_tab_content' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_strict_necessary_cookies_tab_content' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_strict_necessary_cookies_tab_content' . $wpml_lang ] : $gdpr_default_content->moove_gdpr_get_strict_necessary_content();
		$strictly                     = isset( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) && intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) ? intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) : 1;
		$strictly 										= $strictly && 3 === intval( $strictly ) ? 2 : $strictly;

		$strictly_ccat_enable 				= isset( $modal_options['moove_gdpr_strictly_ccat_enable'] ) ? ( intval( $modal_options['moove_gdpr_strictly_ccat_enable'] ) === 1 ? true : ( ! isset( $modal_options['moove_gdpr_strictly_ccat_enable'] ) ? true : false ) ) : true;

		$data                         = new stdClass();
		$data->options                = $modal_options;
		$data->wpml_lang              = $wpml_lang;
		$data->tab_title              = esc_attr( $tab_title );
		$data->tab_content            = wp_kses_post( wpautop( $tab_content ) );
		$data->show                   = 3 !== $strictly;
		$data->show 									= $strictly_ccat_enable ? $data->show : false;
		$data->is_checked             = 1 !== $strictly ? 'disabled checked="checked" ' : '';
		if ( 4 === $strictly )  :
			$data->is_checked = ' checked="checked" ';
		endif;
		$data->text_enable            = isset( $modal_options[ 'moove_gdpr_modal_enabled_checkbox_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_enabled_checkbox_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_modal_enabled_checkbox_label' . $wpml_lang ] : __( 'Enabled', 'gdpr-cookie-compliance' );
		$data->text_disable           = isset( $modal_options[ 'moove_gdpr_modal_disabled_checkbox_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_disabled_checkbox_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_modal_disabled_checkbox_label' . $wpml_lang ] : __( 'Disabled', 'gdpr-cookie-compliance' );
		$data->warning_message_top    = false;
		$data->warning_message_bottom = false;
		$data->checkbox_state         = 2 === $strictly ? 'gdpr-checkbox-disabled checkbox-selected' : '';
		$data->visibility             = 'v1' === $layout ? 'style="display:none"' : '';
		return $view_controller->load( 'modal.content-sections.strictly', $data );
	}

	/**
	 * GDPR Module - Advanced Cookies
	 */
	public function get_section_advanced() {
		$view_controller      = new GDPR_Modules_View();
		$modal_options        = $this->gdpr_options;
		$wpml_lang            = $this->wpml_lang;
		$gdpr_default_content = new Moove_GDPR_Content();

		$layout                = isset( $modal_options['moove_gdpr_plugin_layout'] ) ? $modal_options['moove_gdpr_plugin_layout'] : 'v1';
		$tab_title             = isset( $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] : __( 'Marketing', 'gdpr-cookie-compliance' );
		$tab_content           = isset( $modal_options[ 'moove_gdpr_advanced_cookies_tab_content' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_advanced_cookies_tab_content' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_advanced_cookies_tab_content' . $wpml_lang ] : $gdpr_default_content->moove_gdpr_get_advanced_cookies_content();
		$strictly              = isset( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) && intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) ? intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) : 1;
		$strictly 						 = $strictly && 3 === intval( $strictly ) ? 2 : $strictly;
		$data                  = new stdClass();
		$data->options         = $modal_options;
		$data->wpml_lang       = $wpml_lang;
		$data->tab_title       = esc_attr( $tab_title );
		$data->tab_content     = wp_kses_post( wpautop( $tab_content ) );
		$data->show            = isset( $modal_options['moove_gdpr_advanced_cookies_enable'] ) && 1 === intval( $modal_options['moove_gdpr_advanced_cookies_enable'] ) ? true : false;
		$data->is_checked      = 1 !== $strictly ? '' : 'disabled';
		$data->fieldset        = 1 !== $strictly ? 'fl-strenabled' : 'fl-disabled';
		$data->text_enable     = isset( $modal_options[ 'moove_gdpr_modal_enabled_checkbox_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_enabled_checkbox_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_modal_enabled_checkbox_label' . $wpml_lang ] : __( 'Enabled', 'gdpr-cookie-compliance' );
		$data->text_enable     = esc_attr( $data->text_enable );
		$data->text_disable    = isset( $modal_options[ 'moove_gdpr_modal_disabled_checkbox_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_disabled_checkbox_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_modal_disabled_checkbox_label' . $wpml_lang ] : __( 'Disabled', 'gdpr-cookie-compliance' );
		$data->text_disable    = esc_attr( $data->text_disable );
		$data->warning_message = '';
		$data->visibility      = 'v1' === $layout ? 'style="display:none"' : '';
		return $view_controller->load( 'modal.content-sections.advanced', $data );
	}

	/**
	 * GDPR Module - Third Party Cookies
	 */
	public function get_section_third_party() {
		$view_controller       = new GDPR_Modules_View();
		$modal_options         = $this->gdpr_options;
		$wpml_lang             = $this->wpml_lang;
		$gdpr_default_content  = new Moove_GDPR_Content();
		$layout                = isset( $modal_options['moove_gdpr_plugin_layout'] ) ? $modal_options['moove_gdpr_plugin_layout'] : 'v1';
		$tab_title             = isset( $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] : __( 'Analytics', 'gdpr-cookie-compliance' );
		$tab_content           = isset( $modal_options[ 'moove_gdpr_performance_cookies_tab_content' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_performance_cookies_tab_content' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_performance_cookies_tab_content' . $wpml_lang ] : $gdpr_default_content->moove_gdpr_get_third_party_content();
		$strictly              = isset( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) && intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) ? intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) : 1;
		$strictly 						 = $strictly && 3 === intval( $strictly ) ? 2 : $strictly;
		$data                  = new stdClass();
		$data->options         = $modal_options;
		$data->wpml_lang       = $wpml_lang;
		$data->tab_title       = esc_attr( $tab_title );
		$data->tab_content     = wp_kses_post( wpautop( $tab_content ) );
		$data->show            = isset( $modal_options['moove_gdpr_third_party_cookies_enable'] ) && 1 === intval( $modal_options['moove_gdpr_third_party_cookies_enable'] ) ? true : false;
		$data->is_checked      = 1 !== $strictly ? '' : 'disabled';
		$data->fieldset        = 1 !== $strictly ? 'fl-strenabled' : 'fl-disabled';
		$data->text_enable     = isset( $modal_options[ 'moove_gdpr_modal_enabled_checkbox_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_enabled_checkbox_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_modal_enabled_checkbox_label' . $wpml_lang ] : __( 'Enabled', 'gdpr-cookie-compliance' );
		$data->text_enable     = esc_attr( $data->text_enable );
		$data->text_disable    = isset( $modal_options[ 'moove_gdpr_modal_disabled_checkbox_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_disabled_checkbox_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_modal_disabled_checkbox_label' . $wpml_lang ] : __( 'Disabled', 'gdpr-cookie-compliance' );
		$data->text_disable    = esc_attr( $data->text_disable );
		$data->warning_message = '';
		$data->visibility      = 'v1' === $layout ? 'style="display:none"' : '';
		return $view_controller->load( 'modal.content-sections.third_party', $data );
	}

	/**
	 * GDPR Module - Custom CSS for branding
	 */
	public function get_branding_styles() {
		$view_controller = new GDPR_Modules_View();
		$modal_options   = $this->gdpr_options;
		$wpml_lang       = $this->wpml_lang;
		$font_family     = false;
		if ( isset( $modal_options['moove_gdpr_plugin_font_type'] ) ) :
			if ( '1' === $modal_options['moove_gdpr_plugin_font_type'] ) :
				$font_family = "'Nunito', sans-serif";
			elseif ( '2' === $modal_options['moove_gdpr_plugin_font_type'] ) :
				$font_family = 'inherit';
			else :
				$font_family = isset( $modal_options['moove_gdpr_plugin_font_family'] ) && $modal_options['moove_gdpr_plugin_font_family'] ? $modal_options['moove_gdpr_plugin_font_family'] : "'Nunito', sans-serif";
			endif;
		endif;
		$font_family            = $font_family ? $font_family : ( isset( $modal_options['moove_gdpr_plugin_font_family'] ) && $modal_options['moove_gdpr_plugin_font_family'] ? $modal_options['moove_gdpr_plugin_font_family'] : "'Nunito', sans-serif" );
		$data                   = new stdClass();
		$data->primary_colour   = isset( $modal_options['moove_gdpr_brand_colour'] ) && $modal_options['moove_gdpr_brand_colour'] ? $modal_options['moove_gdpr_brand_colour'] : '#0C4DA2';
		$data->secondary_colour = '#000000';
		$data->button_bg        = '#373737';
		$data->button_hover_bg  = '#000000';
		$data->button_font      = '#ffffff';
		$data->font_family      = $font_family;
		return $view_controller->load( 'branding-styles', $data );

	}

	/**
	 * GDPR Module - Cookie Policy Page
	 */
	public function get_section_cookiepolicy() {
		$view_controller      = new GDPR_Modules_View();
		$modal_options        = $this->gdpr_options;
		$wpml_lang            = $this->wpml_lang;
		$gdpr_default_content = new Moove_GDPR_Content();
		$layout               = isset( $modal_options['moove_gdpr_plugin_layout'] ) ? $modal_options['moove_gdpr_plugin_layout'] : 'v1';
		$tab_title            = isset( $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] : __( 'Cookie Policy', 'gdpr-cookie-compliance' );
		$tab_content          = isset( $modal_options[ 'moove_gdpr_cookies_policy_tab_content' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_cookies_policy_tab_content' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_cookies_policy_tab_content' . $wpml_lang ] : $gdpr_default_content->moove_gdpr_get_cookie_policy_content();
		$data                 = new stdClass();
		$data->options        = $modal_options;
		$data->wpml_lang      = $wpml_lang;
		$data->tab_title      = esc_attr( $tab_title );
		$data->tab_content    = wp_kses_post( wpautop( $tab_content ) );
		$data->show           = isset( $modal_options['moove_gdpr_cookie_policy_enable'] ) && 1 === intval( $modal_options['moove_gdpr_cookie_policy_enable'] ) ? true : false;
		$data->visibility     = 'v1' === $layout ? 'style="display:none"' : '';
		return $view_controller->load( 'modal.content-sections.cookiepolicy', $data );
	}

	/**
	 * GDPR Module - Footer Button inside the Modal
	 */
	public function get_tab_footer_buttons() {
		$view_controller      = new GDPR_Modules_View();
		$modal_options        = $this->gdpr_options;
		$wpml_lang            = $this->wpml_lang;
		$data                 = new stdClass();
		$data->settings_v     = isset( $modal_options['moove_gdpr_save_settings_button_enable'] ) ? ( intval( $modal_options['moove_gdpr_save_settings_button_enable'] ) === 1 ? true : ( ! isset( $modal_options['moove_gdpr_save_settings_button_enable'] ) ? true : false ) ) : true;
		$data->settings_label = isset( $modal_options[ 'moove_gdpr_modal_save_button_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_save_button_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_modal_save_button_label' . $wpml_lang ] : __( 'Save Settings', 'gdpr-cookie-compliance' );
		$data->settings_label = esc_attr( $data->settings_label );

		$data->allow_v     = isset( $modal_options['moove_gdpr_enable_all_button_enable'] ) ? ( intval( $modal_options['moove_gdpr_enable_all_button_enable'] ) === 1 ? true : ( ! isset( $modal_options['moove_gdpr_enable_all_button_enable'] ) ? true : false ) ) : true;
		$data->allow_label = isset( $modal_options[ 'moove_gdpr_modal_allow_button_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_allow_button_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_modal_allow_button_label' . $wpml_lang ] : __( 'Enable All', 'gdpr-cookie-compliance' );
		$data->allow_label = esc_attr( $data->allow_label );

		$data->reject_v     = isset( $modal_options['moove_gdpr_reject_all_button_enable'] ) ? ( intval( $modal_options['moove_gdpr_reject_all_button_enable'] ) === 1 ? true : ( ! isset( $modal_options['moove_gdpr_reject_all_button_enable'] ) ? false : false ) ) : false;
		$data->reject_label = isset( $modal_options[ 'moove_gdpr_modal_reject_button_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_reject_button_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_modal_reject_button_label' . $wpml_lang ] : __( 'Reject All', 'gdpr-cookie-compliance' );
		$data->reject_label = esc_attr( $data->reject_label );

		$data->buttons_order = isset( $modal_options['gdpr_gs_buttons_order'] ) ? json_decode( $modal_options['gdpr_gs_buttons_order'], true ) : array( 'enable', 'reject', 'save', 'close' );

		return $view_controller->load( 'modal.tab-footer-buttons', $data );
	}

	/**
	 * GDPR Module - Navigation inside the modal
	 */
	public function get_tab_navigation() {
		$view_controller    = new GDPR_Modules_View();
		$modal_options      = $this->gdpr_options;
		$wpml_lang          = $this->wpml_lang;
		$strictly           = isset( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) && intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) ? intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) : 1;
		$strictly 					= $strictly && 3 === intval( $strictly ) ? 2 : $strictly;
		$data               = new stdClass();
		$data->overview     = new stdClass();
		$data->strictly     = new stdClass();
		$data->advanced     = new stdClass();
		$data->third_party  = new stdClass();
		$data->cookiepolicy = new stdClass();

		// OVERVIEW.
		$data->overview->nav_label = isset( $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] : __( 'Privacy Overview', 'gdpr-cookie-compliance' );
		$data->overview->nav_label = esc_attr( $data->overview->nav_label );

		// STRICTLY.
		$strictly_ccat_enable 				= isset( $modal_options['moove_gdpr_strictly_ccat_enable'] ) ? ( intval( $modal_options['moove_gdpr_strictly_ccat_enable'] ) === 1 ? true : ( ! isset( $modal_options['moove_gdpr_strictly_ccat_enable'] ) ? true : false ) ) : true;

		$data->strictly->show = 3 !== $strictly;
		$data->strictly->show = $strictly_ccat_enable ? $data->strictly->show : false;

		$data->strictly->nav_label = isset( $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] : __( 'Strictly Necessary Cookies', 'gdpr-cookie-compliance' );
		$data->strictly->nav_label = esc_attr( $data->strictly->nav_label );

		// THIRD PARTY.
		$data->third_party->show      = isset( $modal_options['moove_gdpr_third_party_cookies_enable'] ) && 1 === intval( $modal_options['moove_gdpr_third_party_cookies_enable'] ) ? true : false;
		$data->third_party->nav_label = isset( $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] : __( 'Analytics', 'gdpr-cookie-compliance' );
		$data->third_party->nav_label = esc_attr( $data->third_party->nav_label );

		// ADVANCED.
		$data->advanced->show      = isset( $modal_options['moove_gdpr_advanced_cookies_enable'] ) && 1 === intval( $modal_options['moove_gdpr_advanced_cookies_enable'] ) ? true : false;
		$data->advanced->nav_label = isset( $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] : __( 'Marketing', 'gdpr-cookie-compliance' );
		$data->advanced->nav_label = esc_attr( $data->advanced->nav_label );

		// COOKIEPOLICY.
		$data->cookiepolicy->show      = isset( $modal_options['moove_gdpr_cookie_policy_enable'] ) && 1 === intval( $modal_options['moove_gdpr_cookie_policy_enable'] ) ? true : false;
		$data->cookiepolicy->nav_label = isset( $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] : __( 'Cookie Policy', 'gdpr-cookie-compliance' );
		$data->cookiepolicy->nav_label = esc_attr( $data->cookiepolicy->nav_label );

		return $view_controller->load( 'modal.tab-navigation', $data );
	}

}
