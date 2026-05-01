<?php
/**
 * Cookie Categories Nav Doc Comment
 *
 * @category  Cookie Categories Nav View
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

$nav_label = isset( $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] : __( 'Necessary', 'gdpr-cookie-compliance' );
?>
<div class="gdpr-top-menu-links">
	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=strictly-necessary-cookies&gcat=cookie_categories' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'strictly-necessary-cookies' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php echo esc_attr( $nav_label ); ?>
	</a>

	<?php
	$nav_label = isset( $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] : __( 'Analytics', 'gdpr-cookie-compliance' );
	?>
	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=third-party-cookies&gcat=cookie_categories' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'third-party-cookies' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php echo esc_attr( $nav_label ); ?>
	</a>

	<?php
	$nav_label = isset( $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] : __( 'Marketing', 'gdpr-cookie-compliance' );
	?>
	<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&tab=advanced-cookies&gcat=cookie_categories' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'advanced-cookies' === $active_tab ? 'nav-tab-active' : ''; ?>">
		<?php echo esc_attr( $nav_label ); ?>
	</a>

	<?php do_action( 'gdpr_settings_category_nav_extensions', $active_tab ); ?>
</div>
<hr>