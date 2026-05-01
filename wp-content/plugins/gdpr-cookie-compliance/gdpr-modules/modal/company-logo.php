<?php
/**
 * Company Logo File Doc Comment
 *
 * @category Company Logo
 * @package   gdpr-cookie-compliance
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="moove-gdpr-company-logo-holder">
	<img src="<?php echo esc_url( $content->logo_url ); ?>" alt="<?php echo esc_attr( $content->logo_alt ); ?>" <?php echo apply_filters( 'gpdr_logo_extra_atts', '' ); // phpcs:ignore ?> <?php echo $content->logo_width ? ' width="' . esc_attr( $content->logo_width ) . '"' : ''; ?> <?php echo $content->logo_height ? ' height="' . esc_attr( $content->logo_height ) . '"' : ''; ?>  class="img-responsive" />
</div>
<!--  .moove-gdpr-company-logo-holder -->
