<?php

/**
 * Load template when occurs api error.
 *
 * @since 6.7.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<div class="blocks-wrapper mfp-hide">
	<div class="blocks-section">
		<?php echo porto_strip_script_tags( $error_content ); ?>
	</div>
</div>
