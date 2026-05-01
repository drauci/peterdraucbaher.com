<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="privacy_overview" class="moove-gdpr-tab-main">
	<?php if ( $content->tab_title ) : ?>
	<span class="tab-title"><?php echo esc_attr( $content->tab_title ); ?></span>
	<?php endif; ?>
	<div class="moove-gdpr-tab-main-content">
	<?php echo $content->tab_content; // phpcs:ignore ?>
	<?php do_action( 'gdpr_modules_content_extension', $content, 'overview' ); ?>
	</div>
	<!--  .moove-gdpr-tab-main-content -->

</div>
<!-- #privacy_overview -->
