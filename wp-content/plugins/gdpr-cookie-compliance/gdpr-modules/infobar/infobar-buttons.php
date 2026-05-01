<?php
/**
 * Infobar Buttons File Doc Comment
 *
 * @category Infobar Buttons
 * @package   gdpr-cookie-compliance
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="moove-gdpr-button-holder">
	<?php if ( isset( $content->has_accept ) && $content->has_accept ) : ?>
		<button class="mgbutton moove-gdpr-infobar-allow-all <?php echo isset( $content->accept_order ) ? 'gdpr-fbo-' . $content->accept_order : ''; ?>" aria-label="<?php echo esc_attr( $content->button_label ); ?>" <?php echo apply_filters( 'gdpr_tabindex_attribute', '', 1 ); // phpcs:ignore ?>><?php echo esc_attr( $content->button_label ); ?></button>
	<?php endif; ?>
	<?php do_action( 'gdpr_info_bar_button_extensions' ); ?>
</div>
<!--  .button-container -->
