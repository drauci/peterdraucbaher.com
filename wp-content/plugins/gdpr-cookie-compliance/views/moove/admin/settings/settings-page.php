<?php
/**
 * Settings Page Doc Comment
 *
 * @category  Views
 * @package   gdpr-cookie-compliance
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$plugin_tabs = gdpr_get_admin_submenu_items();

$gdpr_default_content = new Moove_GDPR_Content();
wp_verify_nonce( 'gdpr_nonce', 'gdpr_cookie_compliance_nonce' );
$current_tab  	= isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
$current_gcat  	= isset( $_GET['gcat'] ) ? sanitize_text_field( wp_unslash( $_GET['gcat'] ) ) : ( ! isset( $_GET['tab'] ) && isset( $_GET['page'] ) && esc_attr( $_GET['page'] ) === 'moove-gdpr' ? 'settings' : '' );

$show_tab_nav = true;
if ( isset( $current_tab ) && '' !== $current_tab ) :
	$active_tab = $current_tab;
	if ( isset( $plugin_tabs[ $active_tab ] ) ) :
		$show_tab_nav = false;
	endif;
else :
	$_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$_page = str_replace( 'moove-gdpr_', '', $_page );
	if ( isset( $plugin_tabs[ $_page ] ) ) :
		$active_tab   = $_page;
		$show_tab_nav = false;
	else :
		$active_tab = 'branding';
	endif;

endif; // end if.

$show_tab_nav  = 'licence' === $active_tab ? true : $show_tab_nav;
$option_name   = $gdpr_default_content->moove_gdpr_get_option_name();
$modal_options = get_option( $option_name );
$wpml_lang     = $gdpr_default_content->moove_gdpr_get_wpml_lang( 'label' );

?>
<div class="gdpr-cookie-compliance-header-section">
	<h2><?php esc_html_e( 'GDPR Cookie Compliance Plugin (CCPA ready)', 'gdpr-cookie-compliance' ); ?> <span class="gdpr-plugin-version"><?php echo 'v' . esc_attr( MOOVE_GDPR_VERSION ); ?></span></h2>
	<h4>
		<?php
			$content = __( 'This plugin is useful in preparing your site for cookie compliance, data protection and privacy regulations.', 'gdpr-cookie-compliance' );
			apply_filters( 'gdpr_cc_keephtml', $content, true );
		?>
		<br> 
	</h4>
</div>
<!--  .gdpr-header-section -->
<div class="wrap moove-clearfix <?php echo $show_tab_nav ? 'gdpr-show-submenu' : 'gdpr-hide-submenu'; ?>" id="moove_form_checker_wrap">
	<h1></h1>
	<div id="moove-gdpr-setting-error-settings_updated" class="updated settings-error notice is-dismissible gdpr-cc-notice" style="display:none;">
		<p><strong><?php esc_html_e( 'Settings saved.', 'gdpr-cookie-compliance' ); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'gdpr-cookie-compliance' ); ?></span>
		</button>
	</div>

	<?php do_action( 'gdpr_premium_update_alert' ); ?>

	<div id="moove-gdpr-setting-error-settings_scripts_empty" class="error settings-error notice is-dismissible gdpr-cc-notice" style="display:none;">
		<p>
			<strong><?php esc_html_e( 'You need to insert the relevant script for the settings to be saved.', 'gdpr-cookie-compliance' ); ?></strong>
		</p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'gdpr-cookie-compliance' ); ?></span>
		</button>
	</div>

	<?php
	if ( $wpml_lang ) :
		$_current_user       = wp_get_current_user();
		$show_language_alert = 'display: inline-block;';
		?>
		<div class="gdpr-cookie-alert gdpr-cookie-alert-dark" style="<?php echo esc_attr( $show_language_alert ); ?>">
			<p>
				<?php esc_html_e( 'We have detected that you have a multi-language setup.', 'gdpr-cookie-compliance' ); ?>
				<?php /* translators: %s: version number */ ?>
				<?php printf( esc_html__( 'You are currently editing: %s version', 'gdpr-cookie-compliance' ), '<strong>' . esc_attr( gdpr_get_display_language_by_locale( str_replace( '_', '', $wpml_lang ) ) ) . '</strong>' ); ?>
				<?php do_action( 'gdpr_language_alert_bottom', $wpml_lang ); ?>
		</div>
		<!--  .gdpr-cookie-alert -->
	<?php endif; ?>
	<br />
	<div class="gdpr-tab-section-cnt <?php echo implode( ' ', apply_filters( 'gdpr_tab_section_cnt_class', array() ) ); // phpcs:ignore ?>">
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=branding&gcat=settings' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'settings' === $current_gcat ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Settings', 'gdpr-cookie-compliance' ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=integrations&gcat=integrations' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'integrations' === $current_gcat ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Integrations', 'gdpr-cookie-compliance' ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=strictly-necessary-cookies&gcat=cookie_categories' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'cookie_categories' === $current_gcat ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Cookie Categories', 'gdpr-cookie-compliance' ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr_licence' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'licence' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<span class="dashicons dashicons-admin-network"></span>
				<?php esc_html_e( 'Licence Manager', 'gdpr-cookie-compliance' ); ?>
			</a>
			

			<?php do_action( 'gdpr_settings_tab_nav_extensions', $active_tab ); ?>

		</h2>

		<div class="moove-gdpr-form-container <?php echo esc_attr( $active_tab ); ?>">
			<?php
				do_action( 'gdpr_admin_top_nav_links', $active_tab, $current_gcat );

				$view_cnt = new GDPR_View();
				$tab_data = $view_cnt->load( 'moove.admin.settings.' . $active_tab, array() );
				$content  = apply_filters( 'gdpr_settings_tab_content', $tab_data, $active_tab );
				apply_filters( 'gdpr_cc_keephtml', $content, true );
			?>
		</div>
		<!-- moove-form-container -->
	</div>
	<!--  .gdpr-tab-section-cnt -->
	<?php
		$view_cnt = new GDPR_View();
		$content  = $view_cnt->load( 'moove.admin.settings.plugin-boxes', array() );
		apply_filters( 'gdpr_cc_keephtml', $content, true );
	?>
	<div class="moove-clearfix"></div>
	<!--  .moove-clearfix -->
	<div class="moove-gdpr-settings-branding">
		<hr />
	</div>
	<!--  .moove-gdpr-settings-branding -->
</div>
<!-- .wrap -->


