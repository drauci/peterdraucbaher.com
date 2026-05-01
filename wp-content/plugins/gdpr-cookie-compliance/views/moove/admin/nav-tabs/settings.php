<?php
/**
 * Settings Nav Doc Comment
 *
 * @category  Settings Nav View
 * @package   gdpr-cookie-compliance
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$active_tab = isset( $content['active_tab'] ) ? esc_attr( $content['active_tab'] ) : ( isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '' );

$current_gcat = isset( $content['current_gcat'] ) ? $content['current_gcat'] : ( isset( $_GET['gcat'] ) ? sanitize_text_field( wp_unslash( $_GET['gcat'] ) ) : '' );

$gdpr_default_content = new Moove_GDPR_Content();
$option_name   = $gdpr_default_content->moove_gdpr_get_option_name();
$modal_options = get_option( $option_name );
$wpml_lang     = $gdpr_default_content->moove_gdpr_get_wpml_lang( 'label' );
?>
<div class="gdpr-top-menu-links">
	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=branding&gcat=settings' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'branding' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php esc_html_e( 'Branding', 'gdpr-cookie-compliance' ); ?>
	</a>

	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=general-settings&gcat=settings' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'general-settings' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php esc_html_e( 'General', 'gdpr-cookie-compliance' ); ?>
	</a>

	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=banner-settings&gcat=settings' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'banner-settings' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php esc_html_e( 'Cookie Banner', 'gdpr-cookie-compliance' ); ?>
	</a>

	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=screen-settings&gcat=settings' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'screen-settings' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php esc_html_e( 'Cookie Screen', 'gdpr-cookie-compliance' ); ?>
	</a>

	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=floating-button&gcat=settings' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'floating-button' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php esc_html_e( 'Floating Button', 'gdpr-cookie-compliance' ); ?>
	</a>

	<?php
		$nav_label = isset( $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] : __( 'Privacy Overview', 'gdpr-cookie-compliance' );
	?>
	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=privacy-overview&gcat=settings' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'privacy-overview' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php echo esc_attr( $nav_label ); ?>
	</a>

	<?php
	$nav_label = isset( $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] : __( 'Cookie Policy', 'gdpr-cookie-compliance' );
	?>
	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=cookie-policy&gcat=settings' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'cookie-policy' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php echo esc_attr( $nav_label ); ?>
	</a>
</div>
<hr>