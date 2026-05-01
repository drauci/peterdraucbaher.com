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

<div class="moove-gdpr-branding-cnt">
	<?php
	if ( $content->is_enabled ) :
		echo apply_filters( 'moove_gdpr_footer_branding_text', $content->text ); // phpcs:ignore
	endif;
	?>
</div>
<!--  .moove-gdpr-branding -->
