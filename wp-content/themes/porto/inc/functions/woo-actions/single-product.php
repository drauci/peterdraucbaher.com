<?php

remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 26 );
add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_sale_product_period', 15 );
// change action position
add_action( 'woocommerce_share', 'porto_woocommerce_share' );

add_filter( 'woocommerce_related_products_args', 'porto_remove_related_products', 10 );


function porto_woocommerce_share() {
	global $porto_settings;
	$share       = porto_get_meta_value( 'product_share' );
	$legacy_mode = apply_filters( 'porto_legacy_mode', true );
	$legacy_mode = ( $legacy_mode && ! empty( $porto_settings['product-share'] ) ) || ! $legacy_mode;
	if ( $porto_settings['share-enable'] && 'no' !== $share && ( 'yes' === $share || ( 'yes' !== $share && $legacy_mode ) ) ) {
		echo '<div class="product-share">';
			get_template_part( 'share' );
		echo '</div>';
	}
}

// sale product period
function porto_woocommerce_sale_product_period( $dynamic_product = false ) {
	global $product, $porto_woocommerce_loop;
	if ( ( ( ! isset( $porto_woocommerce_loop['widget'] ) || ! $porto_woocommerce_loop['widget'] ) && $product && $product->is_on_sale() ) || ( $dynamic_product && $dynamic_product->is_on_sale() ) ) {
		$product_temp;
		if ( $dynamic_product ) {
			$product_temp = $product;
			$product      = $dynamic_product;
		}
		$is_single   = ( porto_is_product() && is_single( $product->get_id() ) && ! isset( $GLOBALS['porto_woocommerce_loop'] ) ) || ( porto_is_ajax() && isset( $_REQUEST['action'] ) && 'porto_product_quickview' == $_REQUEST['action'] );
		$extra_class = '';
		if ( $product->is_type( 'variable' ) ) {
			$date_diff      = '';
			$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
			if ( $get_variations ) {
				$variations = $product->get_available_variations();
				$sale_date  = '';
				$cur_time   = time();
				foreach ( $variations as $variation ) {
					$variation_obj = wc_get_product( $variation['variation_id'] );
					if ( empty( $variation_obj ) || ! $variation_obj->is_on_sale() ) {
						$date_diff = false;
						continue;
					}

					$new_date = $variation_obj->get_date_on_sale_to();
					if ( empty( $new_date ) || ( $date_diff && $date_diff != $new_date ) ) {
						$date_diff = false;
					} elseif ( $new_date ) {
						if ( false !== $date_diff ) {
							$date_diff = $new_date;
						}
						$sale_date = $new_date;
					}
					if ( false === $date_diff && $sale_date ) {
						break;
					}
				}
				if ( $date_diff ) {
					$date_diff = $date_diff->date( 'Y/m/d H:i:s' );
				} elseif ( $sale_date ) {
					global $porto_settings;
					if ( $is_single || ! empty( $porto_settings['show_swatch'] ) ) {
						$extra_class .= ' for-some-variations';
					}
					$date_diff = $sale_date->date( 'Y/m/d H:i:s' );
				}
			}
		} else {
			$date_diff = $product->get_date_on_sale_to();
			if ( $date_diff ) {
				$date_diff = $date_diff->date( 'Y/m/d H:i:s' );
			}
		}
		if ( $dynamic_product ) {
			$product = $product_temp;
			if ( $date_diff ) {
				return $date_diff;
			}
			return '';
		}
		if ( $date_diff ) {
			echo '<div class="sale-product-daily-deal' . $extra_class . '"' . ( $extra_class ? ' style="display: none"' : '' ) . '>';
				echo '<h5 aria-level="3" class="daily-deal-title">' . esc_html__( 'Offer Ends In:', 'porto' ) . '</h5>';
			if ( $is_single ) {
				echo do_shortcode( '[porto_countdown datetime="' . $date_diff . '" countdown_opts="sday,shr,smin,ssec" string_days="' . esc_attr__( 'Day', 'porto' ) . '" string_days2="' . esc_attr__( 'Days', 'porto' ) . '" string_hours="' . esc_attr__( 'Hour', 'porto' ) . '" string_hours2="' . esc_attr__( 'Hours', 'porto' ) . '" string_minutes="' . esc_attr__( 'Minute', 'porto' ) . '" string_minutes2="' . esc_attr__( 'Minutes', 'porto' ) . '" string_seconds="' . esc_attr__( 'Second', 'porto' ) . '" string_seconds2="' . esc_attr__( 'Seconds', 'porto' ) . '"]' );
			} else {
				echo do_shortcode( '[porto_countdown datetime="' . $date_diff . '" countdown_opts="sday,shr,smin,ssec" string_days="' . esc_attr__( 'Day', 'porto' ) . '" string_days2="' . esc_attr__( 'Days', 'porto' ) . '" string_hours=":" string_hours2=":" string_minutes=":" string_minutes2=":" string_seconds="" string_seconds2=""]' );
			}
			echo '</div>';
		}
	}
}

function porto_woocommerce_output_related_products() {
	if ( porto_is_product() ) {
		woocommerce_output_related_products();
	}
}

// remove related products
function porto_remove_related_products( $args ) {
	global $porto_settings;
	if ( isset( $porto_settings['product-related'] ) && ! $porto_settings['product-related'] ) {
		return array();
	}
	return $args;
}


function porto_woocommerce_single_excerpt() {
	global $post;
	if ( ! $post->post_excerpt ) {
		return;
	}
	?>
	<div class="description">
		<?php //echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
		<?php echo apply_filters( 'woocommerce_short_description', porto_get_excerpt( apply_filters( 'porto_woocommerce_short_description_length', 30 ), false ) ); ?>
	</div>
	<?php
}
if ( ! function_exists( 'porto_woocommerce_next_product' ) ) :
	function porto_woocommerce_next_product( $in_same_cat = false, $excluded_categories = '' ) {
		porto_adjacent_post_link_product( $in_same_cat, $excluded_categories, false );
	}
endif;
if ( ! function_exists( 'porto_woocommerce_prev_product' ) ) :
	function porto_woocommerce_prev_product( $in_same_cat = false, $excluded_categories = '' ) {
		porto_adjacent_post_link_product( $in_same_cat, $excluded_categories, true );
	}
endif;
function porto_adjacent_post_link_product( $in_same_cat = false, $excluded_categories = '', $previous = true ) {
	if ( $previous && is_attachment() ) {
		$post = get_post( get_post()->post_parent );
	} else {
		$post = get_adjacent_post( $in_same_cat, $excluded_categories, $previous, 'product_cat' );
	}
	if ( $previous ) {
		$label = 'prev';
	} else {
		$label = 'next';
	}
	if ( $post ) {
		$product = wc_get_product( $post->ID );
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
		?>
		<div class="product-<?php echo porto_filter_output( $label ); ?>">
			<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
				<span class="product-link"></span>
				<span class="product-popup">
					<span class="featured-box">
						<span class="box-content">
							<span class="product-image">
								<span class="inner">
									<?php
									if ( has_post_thumbnail( $post->ID ) ) {
										echo get_the_post_thumbnail( $post->ID, $thumbnail_size );
									} else {
										echo '<img src="' . wc_placeholder_img_src( $thumbnail_size ) . '" alt="' . esc_html__( 'Awaiting product image', 'woocommerce' ) . '" width="' . absint( $gallery_thumbnail['width'] ) . '" height="' . absint( $gallery_thumbnail['height'] ) . '" />';
									}
									?>
								</span>
							</span>
							<span class="product-details">
								<span class="product-title"><?php echo ( get_the_title( $post ) ) ? get_the_title( $post ) : $post->ID; ?></span>
							</span>
						</span>
					</span>
				</span>
			</a>
		</div>
		<?php
	} else {
		?>
		<div class="product-<?php echo porto_filter_output( $label ); ?>">
			<span class="product-link disabled"></span>
		</div>
		<?php
	}
}


function porto_woocommerce_template_single_add_to_cart() {
	global $product, $porto_settings;
	if ( 'variable' == $product->get_type() ) {
		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );

		if ( ! empty( $porto_settings['catalog-readmore'] ) ) {
			add_action( 'woocommerce_single_variation', 'porto_woocommerce_readmore_button', 20 );
		}
		do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
	} elseif ( ! empty( $porto_settings['catalog-readmore'] ) ) {
		add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_readmore_button', 35 );
	}
}



function porto_woocommerce_template_single_custom_block() {
	global $porto_product_layout;
	$block_slug = get_post_meta( get_the_ID(), 'product_custom_block', true );
	if ( $block_slug ) {
		echo '<div class="single-product-custom-block">';
			echo do_shortcode( '[porto_block name="' . esc_attr( $block_slug ) . '" tracking="meta-product_custom_block"]' );
		echo '</div>';
	}
}

// product custom tabs
add_filter( 'woocommerce_product_tabs', 'porto_woocommerce_custom_tabs' );
add_filter( 'woocommerce_product_tabs', 'porto_woocommerce_global_tab' );
function porto_woocommerce_custom_tabs( $tabs ) {
	global $porto_settings;
	$custom_tabs_count = isset( $porto_settings['product-custom-tabs-count'] ) ? $porto_settings['product-custom-tabs-count'] : '2';
	if ( $custom_tabs_count ) {
		for ( $i = 0; $i < $custom_tabs_count; $i++ ) {
			$index               = $i + 1;
			$custom_tab_title    = get_post_meta( get_the_id(), 'custom_tab_title' . $index, true );
			$custom_tab_priority = (int) get_post_meta( get_the_id(), 'custom_tab_priority' . $index, true );
			if ( ! $custom_tab_priority ) {
				$custom_tab_priority = 40 + $i;
			}
			$custom_tab_content = get_post_meta( get_the_id(), 'custom_tab_content' . $index, true );
			if ( $custom_tab_title && $custom_tab_content ) {
				$tabs[ 'custom_tab' . $index ] = array(
					'title'    => wp_kses_post( $custom_tab_title ),
					'priority' => $custom_tab_priority,
					'callback' => 'porto_woocommerce_custom_tab_content',
					'content'  => porto_output_tagged_content( $custom_tab_content ),
				);
			}
		}
	}
	return $tabs;
}
function porto_woocommerce_global_tab( $tabs ) {
	global $porto_settings;
	$custom_tab_title    = $porto_settings['product-tab-title'];
	$custom_tab_content  = '[porto_block name="' . $porto_settings['product-tab-block'] . '" tracking="option-product-tab-block"]';
	$custom_tab_priority = ( isset( $porto_settings['product-tab-priority'] ) && $porto_settings['product-tab-priority'] ) ? $porto_settings['product-tab-priority'] : 60;
	if ( $custom_tab_title && $custom_tab_content ) {
		$tabs['global_tab'] = array(
			'title'    => wp_kses_post( $custom_tab_title ),
			'priority' => $custom_tab_priority,
			'callback' => 'porto_woocommerce_custom_tab_content',
			'content'  => $custom_tab_content,
		);
	}
	return $tabs;
}
function porto_woocommerce_custom_tab_content( $key, $tab ) {
	echo do_shortcode( $tab['content'] );
}

if ( ! function_exists( 'porto_woocommerce_product_sticky_addcart' ) ) :
	/**
	 *
	 */
	function porto_woocommerce_product_sticky_addcart( $el_class = '', $title_tag = 'h2' ) {
		global $porto_settings;
		if ( ! porto_is_product() || ! isset( $porto_settings['product-sticky-addcart'] ) || ! $porto_settings['product-sticky-addcart'] ) {
			return;
		}
		if ( defined( 'PORTO_STICKY_ADDCART_RENDERED' ) ) {
			return;
		}


		global $product;
		$attachment_id = method_exists( $product, 'get_image_id' ) ? $product->get_image_id() : get_post_thumbnail_id();
		$availability  = $product->get_availability();
		$average       = $product->get_average_rating();

		echo '<div class="sticky-product hide pos-' . esc_attr( $porto_settings['product-sticky-addcart'] ) . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"><div class="container">';
			echo '<div class="sticky-image">';
				echo wp_get_attachment_image( $attachment_id, 'thumbnail' );
			echo '</div>';
			echo '<div class="sticky-detail">';
				echo '<div class="product-name-area">';
					echo '<' . $title_tag . ' class="product-name">' . get_the_title() . '</' . $title_tag . '>';
					echo woocommerce_template_single_price();
				echo '</div>';
				echo '<div class="star-rating" title="' . esc_attr( $average ) . '">';
					echo '<span style="width:' . ( ( (float) $average / 5 ) * 100 ) . '%"></span>';
				echo '</div>';
				echo '<div class="availability"><span>' . ( 'out-of-stock' == $availability['class'] ? esc_html__( 'Out of stock', 'porto' ) : esc_html__( 'In stock', 'porto' ) ) . '</span></div>';
			echo '</div>';
			echo '<div class="add-to-cart">';
				if ( $product->is_type( 'simple' ) ) {
					echo '<button type="submit" class="single_add_to_cart_button button">' . esc_html__( 'Add to cart', 'woocommerce' ) . '</button>';
				} else {
					echo '<button class="single_add_to_cart_button button scroll-to-sticky">' . $product->single_add_to_cart_text() . '</button>';
				}
			echo '</div>';
		echo '</div></div>';

		define( 'PORTO_STICKY_ADDCART_RENDERED', true );
	}
endif;