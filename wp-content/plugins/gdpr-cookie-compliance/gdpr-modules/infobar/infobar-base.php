<?php
/**
 * Infobar Base File Doc Comment
 *
 * @category Infobar Base
 * @package   gdpr-cookie-compliance
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( $content->show ) :
	?>
	<!--copyscapeskip-->
	<aside id="moove_gdpr_cookie_info_bar" class="<?php echo esc_attr( $content->class ); ?>" aria-label="<?php esc_html_e( 'GDPR Cookie Banner', 'gdpr-cookie-compliance' ); ?>" style="display: none;">
	<div class="moove-gdpr-info-bar-container">
		<div class="moove-gdpr-info-bar-content">
		<?php echo gdpr_get_module( 'infobar-content' ); // phpcs:ignore ?>
		<?php echo gdpr_get_module( 'infobar-buttons' ); // phpcs:ignore ?>
		</div>
		<!-- moove-gdpr-info-bar-content -->
	</div>
	<!-- moove-gdpr-info-bar-container -->
	</aside>
	<!-- #moove_gdpr_cookie_info_bar -->
	<!--/copyscapeskip-->
<?php endif; ?>
