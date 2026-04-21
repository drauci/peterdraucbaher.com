<?php
/**
 * Product Video Thumbnail
 *
 * Display video instead of thumbnail images
 * 
 * @since 6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Video_Thumbnail' ) ) :

	class Porto_Video_Thumbnail {
		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
			add_filter( 'porto_single_product_after_thumbnails', array( $this, 'print_video_thumbnails' ), 10, 3 );
			add_filter( 'porto_single_product_gallery_img_after', array( $this, 'print_video_slide' ), 10, 3 );
			add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'wrap_order' ), 99, 5 );
		}

		/**
		 * Load assets for video thumbnails
		 */
		public function enqueue_scripts() {
			wp_register_script( 'porto-video-thumbnail', PORTO_LIB_URI . '/video-thumbnail/video-thumbnail.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		}

		/**
		 * Print the video slide
		 * 
		 * @since 7.6.0
		 */
		public function print_video_slide( $res, $direct = false, $gallery_count = 1 ) {
			$html = '';

			if ( is_product() ) {
				global $porto_scatted_layout, $porto_product_layout, $product;
				$_id = get_the_ID();
				if ( ! $direct ) {
					$video_pos = get_post_meta( $_id, 'porto_video_pos', true );
					if ( 'last' !=  $video_pos ) {
						if ( $video_pos <= $gallery_count ) {
							return;
						}
					}
				}
				$video_sh_type = get_post_meta( $_id, 'porto_video_sh_type', true );
				if ( ! empty( $video_sh_type ) && 'slide' == $video_sh_type ) {
					$video_source = get_post_meta( $_id, 'porto_video_source', true );
					$video_poster = get_post_meta( $_id, 'porto_video_thumbnail_poster', true );
					$featured_id  = method_exists( $product, 'get_image_id' ) ? $product->get_image_id() : get_post_thumbnail_id();
					$featured     = wp_get_attachment_image_src( $featured_id, apply_filters( 'woocommerce_gallery_image_size', ( ! empty( $porto_scatted_layout ) || 'full_width' === $porto_product_layout ) ? 'full' : 'woocommerce_single' ) );
					if ( ! empty( $featured ) && ! empty( $featured[0] ) ) {
						$featured   = $featured[0];
					} else {
						$featured = '';
					}
					$video_html = '';
					if ( '' == $video_source || 'shortcode' == $video_source ) {
						
						$ids          = get_post_meta( $_id, 'porto_product_video_thumbnails' );
						if ( ! empty( $ids ) ) {
							$url    = wp_get_attachment_url( $ids[0] );
							$poster = get_the_post_thumbnail_url( $ids[0] );
							if ( ! $poster ) {
								$poster = $featured;
							}
							$video_html .= do_shortcode( '[video src="' . esc_url( $url ) . '" poster="' . esc_url( $poster ) . '"]' );
						} else {
							// with video thumbnail shortcode
							$video_code = get_post_meta( $_id, 'porto_product_video_thumbnail_shortcode', true );
							if ( false !== strpos( $video_code, '[video ' ) ) {
								preg_match( '/poster="([^\"]*)"/', $video_code, $poster );
								$poster      = empty( $poster ) ? $featured : $poster[1];
								$video_html .= do_shortcode( preg_replace( '/poster="([^\"]*)"/', 'poster="' . esc_url( $poster ) . '"', $video_code ) );
							} else {
								$youtube_id = preg_match( '/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/', $video_code, $matches );
								if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
									$youtube_id = $matches[1];
								} else {
									$youtube_id = '';
								}
								if ( $youtube_id ) {
									wp_enqueue_script( 'porto-video-api' );
									$video_html .= '<div id="ytplayer_' . porto_generate_rand( 4 ) . '" class="porto-video-social video-youtube" data-video="' . esc_attr( $youtube_id ) . '" data-loop="0" data-audio="0" data-controls="1"></div>';
								} else {
									$vimeo_id = preg_match( '/^(?:https?:\/\/)?(?:www|player\.)?(?:vimeo\.com\/)?(?:video\/|external\/)?(\d+)([^.?&#"\'>]?)/', $video_code, $matches );
									if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
										$vimeo_id = $matches[1];
									} else {
										$vimeo_id = '';
									}
									if ( $vimeo_id ) {
										wp_enqueue_script( 'porto-video-api' );
										$video_html .= '<div id="vmplayer_' . porto_generate_rand( 4 ) . '" class="porto-video-social video-vimeo" data-video="' . esc_attr( $vimeo_id ) . '" data-loop="0" data-audio="0" data-controls="1"></div>';
									}
								}
							}
						}
					} else if ( 'mp4' == $video_source ) {
						$ids          = get_post_meta( $_id, 'porto_product_video_thumbnails' );
						if ( ! empty( $ids ) && ! empty( $ids[0] ) ) {
							$url    = wp_get_attachment_url( $ids[0] );
							$poster = get_the_post_thumbnail_url( $ids[0] );
							if ( ! $poster ) {
								$poster = $featured;
							}
							if ( ! $video_poster ) {
								$video_poster = $poster;
							}
							$video_html .= do_shortcode( '[video src="' . esc_url( $url ) . '" poster="' . esc_url( $video_poster ) . '"]' );
						}
					} else if ( 'youtube' == $video_source ) {
						$video_code = get_post_meta( $_id, 'porto_video_youtube', true );
						$youtube_id = preg_match( '/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/', $video_code, $matches );
						if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
							$youtube_id = $matches[1];
						} else {
							$youtube_id = '';
						}
						if ( $youtube_id ) {
							wp_enqueue_script( 'porto-video-api' );
							$video_html .= '<div id="ytplayer_' . porto_generate_rand( 4 ) . '" class="porto-video-social video-youtube" data-video="' . esc_attr( $youtube_id ) . '" data-loop="0" data-audio="0" data-controls="1"></div>';
						} 
					} else if ( 'vimeo' == $video_source ) {
						$video_code = get_post_meta( $_id, 'porto_video_vimeo', true );
						$vimeo_id = preg_match( '/^(?:https?:\/\/)?(?:www|player\.)?(?:vimeo\.com\/)?(?:video\/|external\/)?(\d+)([^.?&#"\'>]?)/', $video_code, $matches );
						if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
							$vimeo_id = $matches[1];
						} else {
							$vimeo_id = '';
						}
						if ( $vimeo_id ) {
							wp_enqueue_script( 'porto-video-api' );
							$video_html .= '<div id="vmplayer_' . porto_generate_rand( 4 ) . '" class="porto-video-social video-vimeo" data-video="' . esc_attr( $vimeo_id ) . '" data-loop="0" data-audio="0" data-controls="1"></div>';
						}
					}
					if ( ! empty( $video_html ) ) {
						$html .= '<div class="vd-image">' . $video_html . '</div>';
					}
				}
			}
			return $html;
		}

		/**
		 * Wrap the order
		 * 
		 * @since 7.6.0
		 */
		public function wrap_order( $html, $attachment_id, $postid = '', $where = '', $key  = 0 ) {

			if ( isset( $where ) && ! empty( $key ) ) { // thumbnail
				global $product;
				if ( ! empty( $product ) ) {
					$_id           = get_the_ID();
					$video_pos     = get_post_meta( $_id, 'porto_video_pos', true );
					$video_sh_type = get_post_meta( $_id, 'porto_video_sh_type', true );
					if (  'slide' == $video_sh_type && ! empty( $video_pos ) && 'last' != $video_pos && (int) $key == (int) $video_pos ) {
						if ( 'large' != $where ) {
							return $this->print_video_thumbnails( '', true ) . $html;
						} else {
							return $this->print_video_slide( '', true ) . $html;
						}
					}
				}
			}
			return $html;
		}

		/**
		 * Print video for product thumbnails.
		 */
		public function print_video_thumbnails( $res, $direct = false, $gallery_count = 1 ) {
			global $product;
			if ( empty( $product ) ) {
				return;
			}
			$_id           = get_the_ID();
			$video_pos     = get_post_meta( $_id, 'porto_video_pos', true );
			$video_sh_type = get_post_meta( $_id, 'porto_video_sh_type', true );
			if ( ! $direct && 'slide' == $video_sh_type ) {
				if ( 'last' !=  $video_pos ) {
					if ( $video_pos <= $gallery_count ) {
						return;
					}
				}
			}
			ob_start();
			
			$featured_id    = method_exists( $product, 'get_image_id' ) ? $product->get_image_id() : get_post_thumbnail_id();
			$featured_thumb = wp_get_attachment_image_src( $featured_id, has_image_size( 'shop_thumbnail' ) ? 'shop_thumbnail' : 'woocommerce_thumbnail' );
			if ( ! empty( $featured_thumb ) && ! empty( $featured_thumb[0] ) ) {
				$featured_thumb = $featured_thumb[0];
			} else {
				$featured_thumb = '';
			}
			$featured_large = wp_get_attachment_image_src( $featured_id, apply_filters( 'woocommerce_gallery_image_size', 'full' ) );
			if ( ! empty( $featured_large ) && ! empty( $featured_large[0] ) ) {
				$featured_large   = $featured_large[0];
			} else {
				$featured_large = '';
			}
			$video_thumb   = get_post_meta( $_id, 'porto_video_thumbnail_img', true );
			$video_poster  = get_post_meta( $_id, 'porto_video_thumbnail_poster', true );
			$video_source  = get_post_meta( $_id, 'porto_video_source', true );
			

			if ( 'popup' == $video_sh_type || empty( $video_sh_type ) ) {
				if ( '' == $video_source || 'shortcode' == $video_source ) {
					if ( '' == $video_source ) {
						$ids = get_post_meta( $_id, 'porto_product_video_thumbnails' );
						if ( ! empty( $ids ) ) {
							wp_enqueue_script( 'jquery-fitvids' );
							wp_enqueue_script( 'porto-theme-fit-vd' );
							wp_enqueue_script( 'porto-video-thumbnail' );
							foreach ( $ids as $id ) {
								$url = wp_get_attachment_url( $id );
								$poster = get_the_post_thumbnail_url( $id ) ? get_the_post_thumbnail_url( $id ) : $featured_large;
								?>
			
								<div class="img-thumbnail" data-vd-pos="<?php echo esc_attr( $video_pos ); ?>">
									<?php porto_enqueue_link_style( 'porto-video-thumbnail', PORTO_CSS . '/part/video-thumbnail.css' ); ?>
									<a role="button" aria-label="<?php esc_attr_e( 'View with Video Thumbnail', 'porto' ); ?>" href="#" class="porto-video-thumbnail-viewer"><img src="<?php echo esc_url( $poster ); ?>" alt="poster image"></a>
									<script type="text/template" class="porto-video-thumbnail-data">
										<figure class="post-media fit-video">
											<?php echo do_shortcode( '[video src="' . esc_url( $url ) . '" poster="' . esc_url( $poster ) . '"]' ); ?>
										</figure>
									</script>
								</div>
			
								<?php
							}
						}
					}
					// with video thumbnail shortcode
					$video_code = get_post_meta( $_id, 'porto_product_video_thumbnail_shortcode', true );
					$video_html = '';
					if ( false !== strpos( $video_code, '[video src="' ) ) {
						wp_enqueue_script( 'jquery-fitvids' );
						wp_enqueue_script( 'porto-video-thumbnail' );

						preg_match( '/poster="([^\"]*)"/', $video_code, $poster );
						$poster_lg    = empty( $poster ) ? $featured_large : $poster[1];
						$poster_thumb = empty( $poster ) ? $featured_thumb : $poster[1];
						$video_html   = do_shortcode( $video_code );
					} else {
						$youtube_id = preg_match( '/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/', $video_code, $matches );
						if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
							$youtube_id = $matches[1];
						} else {
							$youtube_id = '';
						}
						if ( ! $youtube_id ) {
							$vimeo_id = preg_match( '/^(?:https?:\/\/)?(?:www|player\.)?(?:vimeo\.com\/)?(?:video\/|external\/)?(\d+)([^.?&#"\'>]?)/', $video_code, $matches );
							if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
								$vimeo_id = $matches[1];
							} else {
								$vimeo_id = '';
							}
						}
						$poster_lg    = $featured_large;
						$poster_thumb = $featured_thumb;
					}
					if ( ! $video_thumb ) {
						$video_thumb = $poster_thumb;
					}
					if ( ! $video_poster ) {
						$video_poster = $poster_lg;
					}
					if ( $video_html ) {
						wp_enqueue_script( 'jquery-fitvids' );
						wp_enqueue_script( 'porto-theme-fit-vd' );
						wp_enqueue_script( 'porto-video-thumbnail' );
						?>
						<div class="img-thumbnail" data-vd-pos="<?php echo esc_attr( $video_pos ); ?>">
							<?php porto_enqueue_link_style( 'porto-video-thumbnail', PORTO_CSS . '/part/video-thumbnail.css' ); ?>
							<a role="button" aria-label="<?php esc_attr_e( 'View with Video Thumbnail', 'porto' ); ?>" href="#" class="porto-video-thumbnail-viewer popup-video"><img src="<?php echo esc_url( $video_thumb ); ?>" alt="poster image"></a>
							<script type="text/template" class="porto-video-thumbnail-data">
								<figure class="post-media fit-video">
								<?php echo porto_strip_script_tags( $video_html ); ?>
								</figure>
							</script>
						</div>
						<?php
					} else if ( ! empty( $youtube_id ) || ! empty( $vimeo_id ) ) {
						?>
						<div class="img-thumbnail" data-vd-pos="<?php echo esc_attr( $video_pos ); ?>">
							<?php porto_enqueue_link_style( 'porto-video-thumbnail', PORTO_CSS . '/part/video-thumbnail.css' ); ?>
							<a role="button" aria-label="<?php esc_attr_e( 'View with Video Thumbnail', 'porto' ); ?>" href="<?php echo esc_url( $video_code ); ?>" class="porto-video-thumbnail-viewer popup-<?php echo ! empty( $youtube_id ) ? 'youtube' : 'vimeo'; ?>"><img src="<?php echo esc_url( $video_thumb ); ?>" alt="poster image"></a>
						</div>
						<?php
					}
				} else if ( 'mp4' == $video_source ) {
					// from library
					$ids = get_post_meta( $_id, 'porto_product_video_thumbnails' );
					if ( isset( $ids[0] ) ) {
						wp_enqueue_script( 'jquery-fitvids' );
						wp_enqueue_script( 'porto-theme-fit-vd' );
						wp_enqueue_script( 'porto-video-thumbnail' );

						$url          = wp_get_attachment_url( $ids[0] );
						$poster_lg    = get_the_post_thumbnail_url( $ids[0] ) ? get_the_post_thumbnail_url( $ids[0] ) : $featured_large;
						$poster_thumb = get_the_post_thumbnail_url( $ids[0] ) ? get_the_post_thumbnail_url( $ids[0] ) : $featured_thumb;
						if ( ! $video_thumb ) {
							$video_thumb = $poster_thumb;
						}
						if ( ! $video_poster ) {
							$video_poster = $poster_lg;
						}
						?>

						<div class="img-thumbnail" data-vd-pos="<?php echo esc_attr( $video_pos ); ?>">
							<?php porto_enqueue_link_style( 'porto-video-thumbnail', PORTO_CSS . '/part/video-thumbnail.css' ); ?>
							<a role="button" aria-label="<?php esc_attr_e( 'View with Video Thumbnail', 'porto' ); ?>" href="#" class="porto-video-thumbnail-viewer"><img src="<?php echo esc_url( $video_thumb ); ?>" alt="poster image"></a>
							<script type="text/template" class="porto-video-thumbnail-data">
								<figure class="post-media fit-video">
									<?php echo do_shortcode( '[video src="' . esc_url( $url ) . '" poster="' . esc_url( $video_poster ) . '"]' ); ?>
								</figure>
							</script>
						</div>

						<?php
					}
				} else if ( 'youtube' == $video_source ) {
					// with video thumbnail shortcode
					$video_code = get_post_meta( $_id, 'porto_video_youtube', true );
					$youtube_id = preg_match( '/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/', $video_code, $matches );
					if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
						$youtube_id = $matches[1];
					} else {
						$youtube_id = '';
					}
					
					$poster = $featured_thumb;
				
					if ( ! $video_thumb ) {
						$video_thumb = $poster;
					}
					if ( ! empty( $youtube_id ) ) {
						?>
						<div class="img-thumbnail" data-vd-pos="<?php echo esc_attr( $video_pos ); ?>">
							<?php porto_enqueue_link_style( 'porto-video-thumbnail', PORTO_CSS . '/part/video-thumbnail.css' ); ?>
							<a role="button" aria-label="<?php esc_attr_e( 'View with Video Thumbnail', 'porto' ); ?>" href="<?php echo esc_url( $video_code ); ?>" class="porto-video-thumbnail-viewer popup-youtube"><img src="<?php echo esc_url( $video_thumb ); ?>" alt="poster image"></a>
						</div>
						<?php
					}
				}  else if ( 'vimeo' == $video_source ) {
					// with video thumbnail shortcode
					$video_code = get_post_meta( $_id, 'porto_video_vimeo', true );
					$vimeo_id = preg_match( '/^(?:https?:\/\/)?(?:www|player\.)?(?:vimeo\.com\/)?(?:video\/|external\/)?(\d+)([^.?&#"\'>]?)/', $video_code, $matches );
					if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
						$vimeo_id = $matches[1];
					} else {
						$vimeo_id = '';
					}
					
					$poster = $featured_thumb;
				
					if ( ! $video_thumb ) {
						$video_thumb = $poster;
					}
					if ( ! empty( $vimeo_id ) ) {
						?>
						<div class="img-thumbnail" data-vd-pos="<?php echo esc_attr( $video_pos ); ?>">
							<?php porto_enqueue_link_style( 'porto-video-thumbnail', PORTO_CSS . '/part/video-thumbnail.css' ); ?>
							<a role="button" aria-label="<?php esc_attr_e( 'View with Video Thumbnail', 'porto' ); ?>" href="<?php echo esc_url( $video_code ); ?>" class="porto-video-thumbnail-viewer popup-vimeo"><img src="<?php echo esc_url( $video_thumb ); ?>" alt="poster image"></a>
						</div>
						<?php
					}
				}
			} else if ( 'slide' == $video_sh_type ) {
				if ( ! $video_thumb ) {
					$video_thumb = $featured_thumb;
				}
				?>
					<div class="img-thumbnail">
						<img width="300" height="300" src="<?php echo esc_url( $video_thumb ); ?>" alt="thumbnail image">
					</div>
				<?php
			}
			return ob_get_clean();
		}
	}
endif;

new Porto_Video_Thumbnail;
