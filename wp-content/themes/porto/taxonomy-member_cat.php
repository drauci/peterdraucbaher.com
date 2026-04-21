<?php get_header(); ?>

<?php
$builder_id = porto_check_builder_condition( 'archive' );
if ( $builder_id ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '" tracking="layout-archive-' . esc_attr( $builder_id ) . '"]' );
} else {
	global $porto_settings, $wp_query;

	$term    = $wp_query->queried_object;
	$term_id = $term->term_id;

	$member_options = get_metadata( $term->taxonomy, $term->term_id, 'member_options', true ) == 'member_options' ? true : false;
	$member_infinite = isset( $porto_settings['member-infinite'] ) ? $porto_settings['member-infinite'] : true;

	if ( 'ajax' == $member_infinite && ! wp_script_is( 'porto-infinite-scroll' ) ) {
		wp_enqueue_script( 'porto-infinite-scroll' );
	}
	?>

<div id="content" role="main">

	<?php if ( category_description() ) : ?>
		<div class="page-content">
			<?php echo category_description(); ?>
		</div>
	<?php endif; ?>

	<?php if ( have_posts() ) : 
		$wrap_cls   = 'page-members clearfix';
		$wrap_attrs = '';
		if ( $member_infinite &&  ! ( $member_options && get_metadata( $term->taxonomy, $term->term_id, 'member_infinite', true ) == 'member_infinite' ) ) {
			$wrap_cls   .= ' porto-ajax-load';
			$wrap_attrs .= ' data-post_type="member" data-post_layout="' . esc_attr( isset( $porto_settings['member-view-type'] ) ? $porto_settings['member-view-type'] : '' ) . '"';
			if ( 'ajax' == $member_infinite ) {
				$wrap_cls .= ' load-ajax';
			} else {
				$wrap_cls .= ' load-infinite';
			}
		}
	?>

		<div class="<?php echo esc_attr( $wrap_cls ); ?>"<?php echo porto_filter_output( $wrap_attrs ); ?>>
			<?php
				$queried_object = get_queried_object(); 
				if ( ! empty( $queried_object ) ) {
				?>
				<input type="hidden" class="category" value="<?php echo esc_attr( $queried_object->term_id ); ?>"/>
				<input type="hidden" class="taxonomy" value="<?php echo esc_attr( $queried_object->taxonomy ); ?>"/>
				<?php
				}
			?>
			<?php if ( ! empty( $porto_settings['member-archive-ajax'] ) ) : ?>
				<div id="memberAjaxBox" class="ajax-box">
					<div class="bounce-loader">
						<div class="bounce1"></div>
						<div class="bounce2"></div>
						<div class="bounce3"></div>
					</div>
					<div class="ajax-box-content" id="memberAjaxBoxContent"></div>
					<?php if ( function_exists( 'porto_title_archive_name' ) && porto_title_archive_name( 'member' ) ) : ?>
						<?php /* translators: %s: Portfoli archive title */ ?>
						<div class="hide ajax-content-append"><h4 class="m-t-sm m-b-lg"><?php echo sprintf( esc_html__( 'More %s:', 'porto' ), porto_title_archive_name( 'member' ) ); ?></h4></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( isset( $porto_settings['member-view-type'] ) && 'advanced' == $porto_settings['member-view-type'] ) : ?>
				<div class="members-container member-row member-row-advanced">
				<?php
					$counter = 0;
					while ( have_posts() ) {
						the_post();
						porto_get_template_part(
							'content',
							'member',
							array(
								'member_counter' => $counter,
							)
						);
						$counter++;
					}
				?>
				</div>
			<?php else : ?>

			<div class="member-row members-container row ccols-wrap <?php echo porto_generate_column_classes( isset( $porto_settings['member-columns'] ) ? $porto_settings['member-columns'] : 4 ); ?>">
				<?php
				while ( have_posts() ) {
					the_post();

					get_template_part( 'content', 'archive-member' );
				}
				?>
			</div>
			<?php endif; ?>
			<?php porto_pagination(); ?>

		</div>

		<?php wp_reset_postdata(); ?>

	<?php else : ?>

		<p><?php esc_html_e( 'Apologies, but no results were found for the requested archive.', 'porto' ); ?></p>

	<?php endif; ?>
</div>
<?php
// Show tooltip to build with Porto Template Builder
porto_add_block_tooltip( 'archive' );
} ?>
<?php get_footer(); ?>
