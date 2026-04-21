<?php get_header(); ?>

<?php
$post_type = ( isset( $_GET['post_type'] ) && $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : null;
if ( isset( $post_type ) && locate_template( 'archive-' . $post_type . '.php' ) ) {
	get_template_part( 'archive', sanitize_file_name( $post_type ) );
	exit;
}

$builder_id = porto_check_builder_condition( 'archive' );
if ( $builder_id ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '" tracking="layout-archive-' . esc_attr( $builder_id ) . '"]' );
	get_footer();
	exit;
}
global $porto_settings;
$post_layout         = isset( $porto_settings['post-layout'] ) ? $porto_settings['post-layout'] : 'large';
$search_content_type = ! empty( $porto_settings['search-type'] ) ? $porto_settings['search-type'] : 'all';
?>

	<div id="content" role="main">

		<?php if ( have_posts() ) : ?>

			<?php
			if ( 'all' != $search_content_type ) :
				if ( 'timeline' == $post_layout ) {
					global $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count;

					$prev_post_year      = null;
					$prev_post_month     = null;
					$first_timeline_loop = false;
					$post_count          = 1;
					?>

					<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?> <?php
					if ( ! empty( $porto_settings['post-style'] ) ) {
						echo 'blog-posts-' . esc_attr( $porto_settings['post-style'] ); }
					?>
					">
					<section class="timeline">
					<div class="timeline-body posts-container">

				<?php } elseif ( 'grid' == $post_layout || 'masonry' == $post_layout ) { ?>

					<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?> <?php
					if ( ! empty( $porto_settings['post-style'] ) ) {
						echo 'blog-posts-' . esc_attr( $porto_settings['post-style'] ); }
					?>
					">
					<div class="row posts-container">

				<?php } else { ?>

					<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?> posts-container">

				<?php } ?>

				<?php
				while ( have_posts() ) {
					the_post();

					get_template_part( 'content', 'blog-' . sanitize_file_name( $post_layout ) );
				}
				?>

				<?php if ( 'timeline' == $post_layout ) { ?>

					</div>
					</section>

				<?php } elseif ( 'grid' == $post_layout || 'masonry' == $post_layout ) { ?>

					</div>

				<?php } else { ?>

				<?php } ?>

				<?php porto_pagination(); ?>
				</div> <!-- End of .blog-posts -->
				<?php wp_reset_postdata(); ?>
			<?php  else : 
				$post_ids      = array();
				$product_ids   = array();
				$portfolio_ids = array();
				$member_ids    = array();
				$event_ids     = array();
				$other_ids     = array();
				while( have_posts() ) {
					the_post();
					global $post;
					
					switch( $post->post_type ) {
						case 'post':
							$post_ids[] = $post->ID;
							break;
						case 'product':
							$product_ids[] = $post->ID;
							break;
						case 'portfolio': 
							$portfolio_ids[] = $post->ID;
							break;
						case 'member':
							$member_ids[] = $post->ID;
							break;
						case 'event':
							$event_ids[] = $post->ID;
							break;
						default:
							$other_ids[] = $post->ID;
							break;
					}
				}
				ob_start();
				porto_pagination();
				$pagination_html = ob_get_clean();
				wp_reset_postdata();
				
				if ( ! empty( $post_ids ) ) {
					?>
					<div class="align-left heading heading-border heading-middle-border">
						<h3 class="text-uppercase heading-tag font-weight-bold"><?php esc_html_e( 'Results From Blog', 'porto' ); ?></h3>
					</div>
					<?php
					echo do_shortcode( '[porto_tb_posts post_type="post" ids="' . implode( ',', $post_ids ) . '"]');
				}
				if ( ! empty( $product_ids ) ) {
					?>
					<div class="align-left heading heading-border heading-middle-border">
						<h3 class="text-uppercase heading-tag font-weight-bold"><?php esc_html_e( 'Results From Products', 'porto' ); ?></h3>
					</div>
					<?php
					echo do_shortcode( '[porto_tb_posts post_type="product" ids="' . implode( ',', $product_ids ) . '"]');
				}
				if ( ! empty( $portfolio_ids ) ) {
					?>
					<div class="align-left heading heading-border heading-middle-border">
						<h3 class="text-uppercase heading-tag font-weight-bold"><?php esc_html_e( 'Results From Portfolios', 'porto' ); ?></h3>
					</div>
					<?php
					echo do_shortcode( '[porto_tb_posts post_type="portfolio" ids="' . implode( ',', $portfolio_ids ) . '"]');
				}
				if ( ! empty( $member_ids ) ) {
					?>
					<div class="align-left heading heading-border heading-middle-border">
						<h3 class="text-uppercase heading-tag font-weight-bold"><?php esc_html_e( 'Results From Members', 'porto' ); ?></h3>
					</div>
					<?php
					echo do_shortcode( '[porto_tb_posts post_type="member" ids="' . implode( ',', $member_ids ) . '"]');
				}
				if ( ! empty( $event_ids ) ) {
					?>
					<div class="align-left heading heading-border heading-middle-border">
						<h3 class="text-uppercase heading-tag font-weight-bold"><?php esc_html_e( 'Results From Events', 'porto' ); ?></h3>
					</div>
					<?php
					echo do_shortcode( '[porto_tb_posts post_type="event" ids="' . implode( ',', $event_ids ) . '"]');
				}
				if ( ! empty( $other_ids ) ) {
					?>
					<div class="align-left heading heading-border heading-middle-border">
						<h3 class="text-uppercase heading-tag font-weight-bold"><?php esc_html_e( 'Miscellaneous', 'porto' ); ?></h3>
					</div>
					<div class="blog-posts posts-modern posts-container has-ccols ccols-xl-4 ccols-md-3 ccols-sm-2 ccols-1 has-ccols-spacing">
					<?php
						global $post;
						foreach( $other_ids as $id ) {
							$post = get_post( $id );
							setup_postdata( $post );
							
							get_template_part( 'content', 'blog-modern' );
						}
						wp_reset_postdata();
					?>
					</div>
					<?php
				}
				if ( ! empty( $pagination_html ) ) {
					echo porto_filter_output( $pagination_html );
				}
			endif; 
			?>

		<?php else : ?>

			<h2 class="entry-title m-b"><?php esc_html_e( 'Nothing Found', 'porto' ); ?></h2>

			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
				<?php /* translators: %s: Admin post add page url */ ?>
				<p class="alert alert-info"><?php printf( porto_strip_script_tags( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'porto' ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

			<?php elseif ( is_search() ) : ?>

				<p class="alert alert-info"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'porto' ); ?></p>
				<?php get_search_form(); ?>

			<?php else : ?>

				<p class="alert alert-info"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'porto' ); ?></p>
				<?php get_search_form(); ?>

			<?php endif; ?>

		<?php endif; ?>
	</div>

<?php get_footer(); ?>
