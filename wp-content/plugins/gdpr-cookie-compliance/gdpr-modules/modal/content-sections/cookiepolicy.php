<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if ( $content->show ) : ?>
	<div id="cookie_policy_modal" class="moove-gdpr-tab-main" <?php echo $content->visibility; // phpcs:ignore ?>>
	<span class="tab-title"><?php echo esc_attr( $content->tab_title ); ?></span>
	<div class="moove-gdpr-tab-main-content">
		<?php echo $content->tab_content; // phpcs:ignore ?>
		<?php do_action( 'gdpr_modules_content_extension', $content, 'cookiepolicy' ); ?> 
	</div>
	<!--  .moove-gdpr-tab-main-content -->
	</div>
<?php endif; ?>
