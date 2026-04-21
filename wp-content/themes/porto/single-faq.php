<?php get_header(); ?>

<?php
global $porto_settings;
$builder_id = porto_check_builder_condition( 'single' );
if ( $builder_id ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '" tracking="layout-single-' . esc_attr( $builder_id ) . '"]' );
} else {
	wp_reset_postdata();
	?>

	<div id="content" role="main" class="porto-single-page">

		<?php
		if ( have_posts() ) :
			the_post();
			?>

			<?php porto_render_rich_snippets(); ?>

			<div class="faq-content">
				<?php the_content(); ?>
			</div>

		<?php endif; ?>

	</div>
<?php
// Show tooltip to build with Porto Template Builder
porto_add_block_tooltip( 'single' );
}
?>
<?php get_footer(); ?>
