<?php
	/**
	 * The template for the panel header area.
	 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
	 *
	 * @author      Redux Framework
	 * @package     ReduxFramework/Templates
	 * @version:    3.5.4.18
	 */

	if ( ! empty( $this->parent->args['display_name'] ) ) {
		if ( post_type_exists( 'porto_builder' ) ) {
			$this->parent->args['display_name'] = str_replace( '>' . esc_html__( 'Tools', 'porto' ) . '</a>', '>' . esc_html__( 'Tools', 'porto' ) . '</a><a class="porto-theme-link" href="' . esc_url( admin_url( 'edit.php?post_type=porto_builder' ) ) . '">' . esc_html__( 'Templates Builder', 'porto' ) . '</a>', $this->parent->args['display_name'] );
		}
	}
?>
<div id="redux-header">
	<?php if ( ! empty( $this->parent->args['display_name'] ) ) { ?>
		<div class="display_header">

			<h2><?php echo wp_kses_post( $this->parent->args['display_name'] ); ?></h2>

			<?php if ( ! empty( $this->parent->args['display_version'] ) ) { ?>
				<span><?php echo wp_kses_post( $this->parent->args['display_version'] ); ?></span>
			<?php } ?>

		</div>
	<?php } ?>

	<div class="clear"></div>
</div>
