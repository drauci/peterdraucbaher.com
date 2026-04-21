<?php
/**
 * Variable product add to cart
 *
 * @version     9.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $post, $porto_settings;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

if ( ! empty( $render_shop_swatch ) && empty( $quick_shop ) ) {
	$el_class .= ' porto-general-swatch';
	echo '<div class="' . esc_attr( $el_class ) . '">';
	foreach ( $attributes as $attribute_name => $options ) : 
		$attribute = $attribute_name;
		$name      = 'attribute_' . sanitize_title( $attribute );
	
		$attr_type            = '';
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( wc_attribute_taxonomy_name( $tax->attribute_name ) === $attribute ) {
					if ( in_array( $tax->attribute_type, array( 'color', 'label', 'image', 'select' ) ) ) {
						$attr_type = $tax->attribute_type;
						break;
					}
				}
			}
		}
		$swatches = apply_filters( 'porto_wc_swatch_loop_attrs', array(), $product, $attribute_name, $available_variations );
		$html     = '';
		if ( ! empty( $options ) ) {
			$swatch_options        = $product->get_meta( 'swatch_options', true );
			$key                   = md5( sanitize_title( $attribute ) );
	
			$html .= '<ul class="filter-item-list" data-name="' . esc_attr( $name ) . '">';
			if ( $product ) {

	
				$attribute_terms = array();
	
				if ( taxonomy_exists( $attribute ) ) {
					// Get terms if this is a taxonomy - ordered. We need the names too.
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							$attribute_terms[] = array(
								'id'      => md5( $term->slug ),
								'slug'    => $term->slug,
								'label'   => $term->name,
								'term_id' => $term->term_id,
							);
						}
					}
				} else {
					foreach ( $options as $term ) {
						$attribute_terms[] = array(
							'id'    => ( md5( sanitize_title( strtolower( $term ) ) ) ),
							'slug'  => $term,
							'label' => esc_html( $term ),
						);
					}
				}
	
				if ( isset( $swatch_options[ $key ] ) && isset( $swatch_options[ $key ]['type'] ) ) {
					if ( 'color' != $attr_type && 'color' == $swatch_options[ $key ]['type'] ) {
						$attr_type = 'color';
					} elseif ( 'image' == $swatch_options[ $key ]['type'] ) {
						$attr_type = 'image';
					}
				}
	
				$image_size = apply_filters( 'porto_swatches_image_size', 'swatches_image_size' );
				if ( 'image' == $attr_type && isset( $swatch_options[ $key ] ) && ! empty( $swatch_options[ $key ]['size'] ) ) {
					$image_size = $swatch_options[ $key ]['size'];
				}
	
				foreach ( $attribute_terms as $term ) {
					$color_value = '';
					if ( isset( $term['term_id'] ) ) {
						$color_value = get_term_meta( $term['term_id'], 'color_value', true );
					}
	
					if ( ( ! isset( $color_value ) || ! $color_value ) && isset( $swatch_options[ $key ] ) && isset( $swatch_options[ $key ]['attributes'][ $term['id'] ]['color'] ) ) {
						$color_value = $swatch_options[ $key ]['attributes'][ $term['id'] ]['color'];
					}
					$current_attribute_image_src = '';
					if ( 'image' == $attr_type ) {
						if ( isset( $swatch_options[ $key ] ) && ! empty( $swatch_options[ $key ]['attributes'][ $term['id'] ]['image'] ) ) {
							$current_attribute_image_id = $swatch_options[ $key ]['attributes'][ $term['id'] ]['image'];
						} else {
							$current_attribute_image_id = get_term_meta( $term['term_id'], 'image_value', true );
						}
						if ( $current_attribute_image_id ) {
							$current_attribute_image_src = wp_get_attachment_image_src( $current_attribute_image_id, $image_size );
							if ( is_array( $current_attribute_image_src ) ) {
								$current_attribute_image_src = $current_attribute_image_src[0];
							}
						}
					}
	
					if ( 'color' == $attr_type ) {
						$a_class      = 'filter-color';
						$option_attrs = ' data-color="' . esc_attr( $color_value ) . '"';
						$a_attrs      = ' title="' . esc_attr( apply_filters( 'woocommerce_variation_option_name', $term['label'] ) ) . '" style="background-color: ' . esc_attr( $color_value ) . ';border-color: ' . esc_attr( $color_value ) . '"';
					} elseif ( 'image' == $attr_type ) {
						$a_class      = 'filter-item filter-image';
						$option_attrs = ' data-image="' . esc_url( $current_attribute_image_src ) . '"';
						$a_attrs      = ' title="' . esc_attr( apply_filters( 'woocommerce_variation_option_name', $term['label'] ) ) . '"';
						if ( $current_attribute_image_src ) {
							$a_attrs .= ' style="background-image: url(' . esc_url( $current_attribute_image_src ) . ')"';
						}
					} else {
						$a_class = 'filter-item';
						$a_attrs = '';
					}
					$data   = '';
					$class  = '';
					$swatch = isset( $swatches[$term['slug']] ) ? $swatches[$term['slug']] : array();
					if ( isset( $swatch['image_src'] ) ) {
						$data .= 'data-image-src="' . $swatch['image_src'] . '"';
						$data .= ' data-image-srcset="' . $swatch['image_srcset'] . '"';
						$data .= ' data-image-sizes="' . $swatch['image_sizes'] . '"';
					}
					if ( isset( $swatch['is_in_stock'] ) && ! $swatch['is_in_stock'] ) {
						$class .= ' variation-out-of-stock';
					}
					
					$html     .= '<li ' . wp_kses( $data, true ) . ' class="' . esc_attr( $class ) . '">';
						$html .= '<a role="button" href="#" class="' . $a_class . '" data-value="' . esc_attr( $term['slug'] ) . '" ' . $a_attrs . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', 'label' == $attr_type && $label_value ? $label_value : $term['label'], isset( $term['term_id'] ) ? $term : null, $attribute, $product ) ) . '</a>';
					$html     .= '</li>';
				}
			}
			$html .= '</ul>';
		}
		echo porto_filter_output( $html );

		?>
	<?php
	endforeach;
	echo '</div>';
	return;
}

$show_cart_button = true;
$show_only_price  = false;
if ( isset( $porto_settings['catalog-enable'] ) && $porto_settings['catalog-enable'] ) {
	if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
		if ( ! $porto_settings['catalog-cart'] ) {
			$show_cart_button = false;
			if ( ! $porto_settings['catalog-price'] && ! $porto_settings['catalog-readmore'] ) {
				$no_add_to_cart = true;
			} elseif ( $porto_settings['catalog-price'] && ! $porto_settings['catalog-readmore'] ) {
				$show_only_price = true;
			}
		}
	}
}

do_action( 'woocommerce_before_add_to_cart_form' );

?>

<form class="variations_form cart<?php echo ! empty( $el_class ) ? ' ' . esc_attr( $el_class ) : ''; ?>" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo ! $variations_attr ? '' : $variations_attr; // WPCS: XSS ok. ?>"<?php echo false === $available_variations && ! empty( $render_shop_swatch ) ? ' data-custom_data="porto_render_swatch"' : ''; ?>>
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0" role="presentation">
			<tbody>
				<?php
				$loop = 0;
				$count = count( $attributes );
				foreach ( $attributes as $attribute_name => $options ) :
					$loop++;
					?>
					<tr<?php echo ( ! empty( $quick_shop ) && 2 < $loop ) ? ' class="d-none"' : '' ; ?>>
						<th class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></th>

						<td class="value">
							<?php
							wc_dropdown_variation_attribute_options(
								array(
									'options'   => $options,
									'attribute' => $attribute_name,
									'product'   => $product,
									'no_term_desc' => ! empty( $no_term_desc )
								)
							);
							if ( ! empty( $quick_shop ) && 2 == $loop && $count > 2 ) {
								?>
								<a role="button" aria-labelledby="<?php esc_attr_e( 'More swatch', 'porto' ); ?>" href="#" class="more-swatch">+<?php echo ( $count - $loop ); ?></a>
								<?php
							}

							/**
							 * Filters the reset variation button.
							 *
							 * @since 2.5.0
							 *
							 * @param string  $button The reset variation button HTML.
							 */
							echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a role="button" class="reset_variations" href="#" aria-label="' . esc_attr__( 'Clear options', 'woocommerce' ) . '">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<div class="reset_variations_alert screen-reader-text" role="alert" aria-live="polite" aria-relevant="all"></div>
		<?php do_action( 'woocommerce_after_variations_table' ); ?>

		<?php if ( ! isset( $no_add_to_cart ) || ! $no_add_to_cart ) : ?>
		<div class="single_variation_wrap<?php echo ! $show_only_price ? '' : ' py-0 border-0'; ?>">
			<?php
			/**
			 * Hook: woocommerce_before_single_variation.
			 */
			do_action( 'woocommerce_before_single_variation' );
			
			/**
			 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
			 *
			 * @since 2.4.0
			 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
			 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action( 'woocommerce_single_variation' );

			/**
			 * Hook: woocommerce_after_single_variation.
			 */
			do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
