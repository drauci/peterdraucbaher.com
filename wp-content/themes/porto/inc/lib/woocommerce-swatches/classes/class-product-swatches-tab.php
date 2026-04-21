<?php
if ( ! class_exists( 'Porto_Product_Swatches_Tab' ) ) {

	class Porto_Product_Swatches_Tab {

		public $tab_class = 'swatches';
		public $tab_id    = 'porto_swatches';
		public $tab_title = 'Swatches';
		public $tab_icon  = '';

		public function __construct() {

			if ( is_admin() && ( ( isset( $_REQUEST['post'] ) && 'product' == get_post_type( $_REQUEST['post'] ) ) || ( ( 'post-new.php' == $GLOBALS['pagenow'] || 'edit.php' == $GLOBALS['pagenow'] ) && ! empty( $_REQUEST['post_type'] ) && 'product' == $_REQUEST['post_type'] ) || 'edit-tags.php' == $GLOBALS['pagenow'] || 'term.php' == $GLOBALS['pagenow'] ) ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1001 );
			}

			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_write_panel_tabs' ), 99 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'product_data_panel_wrap' ), 99 );
			add_action( 'woocommerce_process_product_meta', array( $this, 'process_meta_box' ), 1, 2 );
			foreach ( wc_get_attribute_taxonomies() as $value ) {
				add_filter( 'manage_edit-pa_' . $value->attribute_name . '_columns', array( $this, 'product_attributes_add_thumbnail_column' ) );
				add_filter( 'manage_pa_' . $value->attribute_name . '_custom_column', array( $this, 'product_attributes_thumbnail_column_content' ), 10, 3 );
			}
			add_action( 'woocommerce_after_add_attribute_fields', array( $this, 'add_attribute_type_image_choose' ), 5 );
			add_action( 'woocommerce_after_edit_attribute_fields', array( $this, 'edit_attribute_type_image_choose' ), 5 );
			// add color attribute type
			add_filter( 'product_attributes_type_selector', array( $this, 'add_product_attribute_color' ), 10, 1 );
			add_action( 'woocommerce_product_option_terms', array( $this, 'add_product_attribute_color_variation' ), 10, 2 );

		}
		

		public function add_product_attribute_color_variation( $attribute_taxonomy, $i ) {
			if ( 'color' !== $attribute_taxonomy->attribute_type && 'label' !== $attribute_taxonomy->attribute_type && 'image' !== $attribute_taxonomy->attribute_type ) {
				return;
			}
		
			global $product_object;
			if ( ! $product_object && isset( $_POST['post_id'] ) && isset( $_POST['product_type'] ) ) {
				$product_id     = absint( $_POST['post_id'] );
				$product_type   = ! empty( $_POST['product_type'] ) ? wc_clean( $_POST['product_type'] ) : 'simple';
				$classname      = WC_Product_Factory::get_product_classname( $product_id, $product_type );
				$product_object = new $classname( $product_id );
			}
			if ( $product_object ) {
				$attributes = $product_object->get_attributes( 'edit' );
				if ( ! array_key_exists( 'pa_' . sanitize_title( $attribute_taxonomy->attribute_name ), $attributes ) ) {
					return;
				}
				$options = $attributes[ 'pa_' . sanitize_title( $attribute_taxonomy->attribute_name ) ]->get_options();
			} else {
				$options = array();
			}
			?>
			<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'porto' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr( $i ); ?>][]">
				<?php
				$args      = array(
					'taxonomy'   => 'pa_' . $attribute_taxonomy->attribute_name,
					'orderby'    => ! empty( $attribute_taxonomy->attribute_orderby ) ? $attribute_taxonomy->attribute_orderby : 'name',
					'hide_empty' => 0,
				);
				$all_terms = get_terms( apply_filters( 'woocommerce_product_attribute_terms', $args ) );
				if ( $all_terms ) {
					foreach ( $all_terms as $term ) {
						$options = ! empty( $options ) ? $options : array();
						echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( in_array( $term->term_id, $options ), true, false ) . '>' . esc_html( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
					}
				}
				?>
			</select>
			<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></button>
			<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></button>
			<button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'woocommerce' ); ?></button>
			<?php
		}

		public function enqueue_scripts() {
			wp_enqueue_script( 'porto-swatch-admin', PORTO_LIB_URI . '/woocommerce-swatches/swatch-admin.min.js', array( 'jquery', 'media-upload', 'wp-color-picker' ), PORTO_VERSION, true );
			wp_enqueue_style( 'porto-swatch-admin', PORTO_LIB_URI . '/woocommerce-swatches/swatch-admin.min.css', array(), PORTO_VERSION );
		}

		/**
		 * Add attribute type image choose option
		 *
		 * @since 1.3
		 */
		public function add_attribute_type_image_choose() {
			?>
			<div class="form-field">
				<label for="attribute_type_image"><?php esc_html_e( 'Type', 'porto' ); ?></label>
				<div class="img-btn-set attribute_type_image">
					<div class="img-btn-item active" data-value="select">
						<img src="<?php echo esc_url( PORTO_WIDGET_URL . 'swatch-select.jpg' ); ?>" title="Select" alt="Select">
					</div>
					<div class="img-btn-item" data-value="color">
						<img src="<?php echo esc_url( PORTO_WIDGET_URL . 'swatch-color.jpg' ); ?>" title="Swatch Color" alt="Swatch Color">
					</div>
					<div class="img-btn-item" data-value="image">
						<img src="<?php echo esc_url( PORTO_WIDGET_URL . 'swatch-image.jpg' ); ?>" title="Swatch Image" alt="Swatch Image">
					</div>
					<div class="img-btn-item" data-value="label">
						<img src="<?php echo esc_url( PORTO_WIDGET_URL . 'swatch-label.jpg' ); ?>" title="Swatch Label" alt="Swatch Label">
					</div>										
				</div>
				<p class="description"><?php esc_html_e( 'Determines how this attribute\'s values are displayed.', 'porto' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Edit attribute type image choose option
		 *
		 * @since 1.3
		 */
		public function edit_attribute_type_image_choose() {
			global $wpdb;

			$edit = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;

			$attribute_to_edit = $wpdb->get_row(
				$wpdb->prepare(
					"
					SELECT attribute_type, attribute_label, attribute_name, attribute_orderby, attribute_public
					FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d
					",
					$edit
				)
			);

			?>

			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="attribute_type_image"><?php esc_html_e( 'Type', 'porto' ); ?></label>
				</th>
				<td>
					<div class="img-btn-set attribute_type_image">
						<div class="img-btn-item<?php echo ( empty( $attribute_to_edit ) || 'select' == $attribute_to_edit->attribute_type ) ? ' active' : ''; ?>" data-value="select">
							<img src="<?php echo esc_url( PORTO_WIDGET_URL . 'swatch-select.jpg' ); ?>" title="Select" alt="Select">
						</div>
						<div class="img-btn-item<?php echo ( ! empty( $attribute_to_edit ) && 'color' == $attribute_to_edit->attribute_type ) ? ' active' : ''; ?>" data-value="color">
							<img src="<?php echo esc_url( PORTO_WIDGET_URL . 'swatch-color.jpg' ); ?>" title="Swatch Color" alt="Swatch Color">
						</div>
						<div class="img-btn-item<?php echo ( ! empty( $attribute_to_edit ) && 'image' == $attribute_to_edit->attribute_type ) ? ' active' : ''; ?>" data-value="image">
							<img src="<?php echo esc_url( PORTO_WIDGET_URL . 'swatch-image.jpg' ); ?>" title="Swatch Image" alt="Swatch Image">
						</div>
						<div class="img-btn-item<?php echo ( ! empty( $attribute_to_edit ) && 'label' == $attribute_to_edit->attribute_type ) ? ' active' : ''; ?>" data-value="label">
							<img src="<?php echo esc_url( PORTO_WIDGET_URL . 'swatch-label.jpg' ); ?>" title="Swatch" alt="Swatch label">
						</div>
					</div>
					<p class="description"><?php esc_html_e( 'Determines how this attribute\'s values are displayed.', 'porto' ); ?></p>
				</td>
			</tr>
			<?php
		}

		/**
		 * 
		 * @since 7.2.0
		 */
		public function add_product_attribute_color( $attrs ) {
			return array_merge(
				$attrs,
				array(
					'color' => __( 'Color', 'porto' ),
					'label' => __( 'Label', 'porto' ),
					'image' => __( 'Image', 'porto' ),
				)
			);
		}

		/**
		 * 
		 * 
		 * @since 7.2.0
		 */
		public function product_attributes_add_thumbnail_column( $columns ) {
			unset( $columns['cb'] );
			unset( $columns['name'] );

			$new_columns = array(
				'cb'          => '<input type="checkbox" />',
				'name'        => esc_html__( 'Name', 'porto' ),
				'thumbnail'   => esc_html__( 'Preview', 'porto' ),
			);

			$columns = $new_columns + $columns;
			return $columns;
		}

		/**
		 * Display the preview on product attribute.
		 * 
		 * @since 7.2.0
		 */
		public function product_attributes_thumbnail_column_content( $content, $column_name, $term_id ) {
			if ( 'thumbnail' === $column_name ) {
				$color = get_term_meta( $term_id, 'color_value', true );
				$image = get_term_meta( $term_id, 'image_value', true );
				$label = get_term_meta( $term_id, 'label_value', true );
				$term_name = get_term( $term_id )->name;
				$attributes = wc_get_attribute_taxonomies();
				$taxonomy_id = wc_attribute_taxonomy_id_by_name( get_term( $term_id )->taxonomy );
				$taxonomy_type = $attributes[ 'id:' . $taxonomy_id ]->attribute_type;

				if ( is_numeric( $image ) ) {
					$image = wp_get_attachment_image_url( $image, 'thumbnail' );
				}

				if ( $image && 'image' == $taxonomy_type ) {
					?>
						<div class="porto-attr-preview swatch-img">
							<img src="<?php echo esc_attr( $image ); ?>">
						</div>
					<?php
				} elseif ( $color && 'color' == $taxonomy_type ) {
					?>
						<div class="porto-attr-preview swatch-bg" style="background-color:<?php echo esc_attr( $color ); ?>;"></div>
					<?php
				} elseif ( $label && 'label' == $taxonomy_type ) {
					?>
					<div class="porto-attr-preview swatch-text">
						<span><?php echo esc_attr( $label ); ?></span>
					</div>
					<?php
				}
			}

			return $content;
		}


		public function product_write_panel_tabs() {
			?>
			<li class="<?php echo porto_filter_output( $this->tab_class ); ?>"><a href="#<?php echo porto_filter_output( $this->tab_id ); ?>"><span><?php echo porto_filter_output( $this->tab_title ); ?></span></a></li>
			<?php
		}

		public function product_data_panel_wrap() {
			?>
			<div id="<?php echo porto_filter_output( $this->tab_id ); ?>" class="panel <?php echo porto_filter_output( $this->tab_class ); ?> woocommerce_options_panel wc-metaboxes-wrapper">
			<?php $this->render_product_tab_content(); ?>
			</div>
			<?php
		}

		public function render_product_tab_content( $post_id = false ) {
			global $_wp_additional_image_sizes;

			if ( ! $post_id ) {
				global $post;
				$post_id = $post->ID;
			}
			$product = wc_get_product( $post_id );

			if ( ! $product ) {
				return;
			}

			$product_type_array = array( 'variable', 'variable-subscription' );

			if ( ! in_array( $product->get_type(), $product_type_array ) ) {
				return;
			}

			$swatch_options = $product->get_meta( 'swatch_options', true );

			if ( ! $swatch_options ) {
				$swatch_options = array();
			}

			echo '<div class="options_group">';
			?>

			<div class="fields">

				<?php
				$woocommerce_taxonomies     = wc_get_attribute_taxonomies();
				$woocommerce_taxonomy_infos = array();
				foreach ( $woocommerce_taxonomies as $tax ) {
					$woocommerce_taxonomy_infos[ wc_attribute_taxonomy_name( $tax->attribute_name ) ] = $tax;
				}
				$tax = null;

				$attributes = $product->get_variation_attributes();

				if ( $attributes && count( $attributes ) ) :
					$attribute_names = array_keys( $attributes );
					foreach ( $attribute_names as $name ) :
						$key      = md5( sanitize_title( $name ) );
						$key_attr = md5( str_replace( '-', '_', sanitize_title( $name ) ) );

						$current_type     = 'default';
						$current_size     = 'swatches_image_size';
						$current_label    = 'Unknown';
						$current_options  = false;
						$global_attribute = false;

						if ( isset( $swatch_options[ $key ] ) ) {
							$current_options = ( $swatch_options[ $key ] );
						}

						if ( $current_options ) {
							$current_size = $current_options['size'];
							if ( isset( $current_options['type'] ) ) {
								$current_type = $current_options['type'];
							}
						}

						$attribute_terms = array();
						if ( taxonomy_exists( $name ) ) {
							$tax                  = get_taxonomy( $name );
							$woocommerce_taxonomy = $woocommerce_taxonomy_infos[ $name ];
							$current_label        = isset( $woocommerce_taxonomy->attribute_label ) && ! empty( $woocommerce_taxonomy->attribute_label ) ? $woocommerce_taxonomy->attribute_label : $woocommerce_taxonomy->attribute_name;

							if ( isset( $woocommerce_taxonomy->attribute_type ) && 'color' == $woocommerce_taxonomy->attribute_type ) {
								$current_type     = 'color';
								$global_attribute = true;
							}

							$terms          = get_terms( array( 'taxonomy' => $name, 'hide_empty' => false ) );
							$selected_terms = isset( $attributes[ $name ] ) ? $attributes[ $name ] : array();
							foreach ( $terms as $term ) {
								if ( in_array( $term->slug, $selected_terms ) ) {
									$attribute_terms[] = array(
										'id'      => md5( $term->slug ),
										'label'   => $term->name,
										'term_id' => $term->term_id,
									);
								}
							}
						} else {
							$current_label = $name;
							foreach ( $attributes[ $name ] as $term ) {
								$attribute_terms[] = array(
									'id'    => ( md5( sanitize_title( strtolower( $term ) ) ) ),
									'label' => esc_html( $term ),
								);
							}
						}
						?>
						<div class="porto_swatches_section">
							<p class="form-field-header">
								<span class="swatch_label">
									<strong><a class="wcsap_edit_field row-title" href="javascript:void(0)"><?php echo esc_html( $current_label ); ?></a></strong>
								</span>
							</p>
							<div class="form-field-body">
								<p class="form-field">
									<label for="swatch_option_<?php echo esc_attr( $key_attr ); ?>_type"><?php esc_html_e( 'Type', 'porto' ); ?></label>
									<select class="swatch_option_type" id="swatch_option_<?php echo esc_attr( $key_attr ); ?>_type" name="swatch_options[<?php echo esc_attr( $key ); ?>][type]"<?php echo ! $global_attribute ? '' : ' disabled="disabled"'; ?>>
										<option <?php selected( $current_type, 'default' ); ?> value="default"><?php esc_html_e( 'Default', 'porto' ); ?></option>
										<option <?php selected( $current_type, 'color' ); ?> value="color"><?php esc_html_e( 'Color', 'porto' ); ?></option>
										<option <?php selected( $current_type, 'image' ); ?> value="image"><?php esc_html_e( 'Image', 'porto' ); ?></option>
									</select>
									<?php
									if ( $global_attribute ) {
										echo wc_help_tip( __( 'You can\'t change the type as this is a global color attribute which was selected in Products -> Attributes.', 'porto' ) );
									} else {
										echo wc_help_tip( __( 'If you select default, Woocommerce default select boxes or theme default buttons will be displayed for this type.', 'porto' ) );
									}
									?>
								</p>
								<p class="form-field swatch_field_image" style="<?php echo 'image' != $current_type ? 'display:none;' : ''; ?>">
									<label for="swatch_option_<?php echo esc_attr( $key_attr ); ?>_size"><?php esc_html_e( 'Size', 'porto' ); ?></label>
									<?php $image_sizes = get_intermediate_image_sizes(); ?>
									<select id="swatch_option_pa_color_size" name="swatch_options[<?php echo esc_attr( $key ); ?>][size]">
										<?php foreach ( $image_sizes as $size ) : ?>
											<option <?php selected( $current_size, $size ); ?> value="<?php echo esc_attr( $size ); ?>"><?php echo esc_html( $size ); ?></option>
										<?php endforeach; ?>
									</select>
								</p>

								<div class="form-field swatch_field_color swatch_field_image" style="<?php echo 'image' != $current_type && 'color' != $current_type ? 'display:none;' : ''; ?>">

									<table class="product_custom_swatches">
										<thead>
											<th class="attribute_swatch_preview">
												<?php esc_html_e( 'Preview', 'porto' ); ?>
											</th>
											<th class="attribute_swatch_label">
												<?php esc_html_e( 'Attribute', 'porto' ); ?>
											</th>
											<th class="attribute_swatch_type">
												<?php esc_html_e( 'Type', 'porto' ); ?>
											</th>
										</thead>

										<tbody>
											<?php
											foreach ( $attribute_terms as $attribute_term ) :
												$current_attribute_color     = '';
												$current_attribute_image_src = wc_placeholder_img_src();
												$current_attribute_image_id  = 0;
												$current_attribute_options   = false;
												$global_attribute_item       = false;
												if ( isset( $current_options['attributes'][ $attribute_term['id'] ] ) ) {
													$current_attribute_options = isset( $current_options['attributes'][ $attribute_term['id'] ] ) ? $current_options['attributes'][ $attribute_term['id'] ] : false;
												}

												if ( $global_attribute && isset( $attribute_term['term_id'] ) ) {
													$current_attribute_color = get_term_meta( $attribute_term['term_id'], 'color_value', true );
													if ( $current_attribute_color ) {
														$global_attribute_item = true;
													}
												}
												if ( $current_attribute_options && ! $global_attribute_item ) {
													if ( ! empty( $current_attribute_options['color'] ) ) {
														$current_attribute_color    = $current_attribute_options['color'];
													}
													if ( ! empty( $current_attribute_options['image'] ) ) {
														$current_attribute_image_id = $current_attribute_options['image'];
													}
													if ( $current_attribute_image_id ) {
														$current_attribute_image_src = wp_get_attachment_image_src( $current_attribute_image_id, $current_size );
														if ( is_array( $current_attribute_image_src ) ) {
															$current_attribute_image_src = $current_attribute_image_src[0];
														}
													}
												}

												?>
												<tr>
													<td class="attribute_swatch_preview">
														<div class="select-option">
															<a id="swatch_option_<?php echo esc_attr( $key_attr ); ?>_<?php echo esc_attr( $attribute_term['id'] ); ?>_color_preview_image" href="javascript:void(0)" class="image swatch_field_image" style="<?php echo 'image' == $current_type ? '' : 'display:none;'; ?>">
																<img src="<?php echo esc_url( $current_attribute_image_src ); ?>" class="wp-post-image" />
															</a>
															<a id="swatch_option_<?php echo esc_attr( $key_attr ); ?>_<?php echo esc_attr( $attribute_term['id'] ); ?>_color_preview_swatch" href="javascript:void(0)" class="swatch swatch_field_color" style="background-color:<?php echo esc_attr( $current_attribute_color ); ?>;<?php echo 'color' == $current_type ? '' : 'display:none;'; ?>"><?php echo esc_html( $attribute_term['label'] ); ?>
															</a>
														</div>
													</td>
													<td class="attribute_swatch_label">
														<strong><a class="wcsap_edit_field row-title" href="javascript:void(0)"><?php echo esc_html( $attribute_term['label'] ); ?></a></strong>
													</td>
													<td class="attribute_swatch_input swatch_field_color" style="<?php echo 'color' == $current_type ? '' : 'display:none;'; ?>">
														<div <?php if ( ! $global_attribute_item ) : ?>class="porto-meta-color"<?php endif; ?>>
															<input type="text" <?php if ( ! $global_attribute_item ) : ?>name="swatch_options[<?php echo esc_attr( $key ); ?>][attributes][<?php echo esc_attr( $attribute_term['id'] ); ?>][color]"
																<?php else : ?>
																disabled="disabled"<?php endif; ?> value="<?php echo esc_attr( $current_attribute_color ); ?>" class="porto-color-field" />
														</div>
													</td>

													<td class="attribute_swatch_input swatch_field_image" style="<?php echo 'image' == $current_type ? '' : 'display:none;'; ?>">

														<div>
															<div id="swatch_option_<?php echo esc_attr( $key_attr ); ?>_<?php echo esc_attr( $attribute_term['id'] ); ?>_image_thumbnail" style="float:left;margin-top:3px;margin-right:10px;">
																<img src="<?php echo esc_url( $current_attribute_image_src ); ?>" alt="<?php esc_attr_e( 'Thumbnail Preview', 'porto' ); ?>" class="wp-post-image" width="16" height="16">
															</div>
															<input class="upload_image_id" type="hidden" id="swatch_option_<?php echo esc_attr( $key_attr ); ?>_<?php echo esc_attr( $attribute_term['id'] ); ?>_image" name="swatch_options[<?php echo esc_attr( $key ); ?>][attributes][<?php echo esc_attr( $attribute_term['id'] ); ?>][image]" value="<?php echo esc_attr( $current_attribute_image_id ); ?>" />
															<button type="submit" class="upload_swatch_image_button button" rel="<?php echo esc_attr( $post_id ); ?>"><?php esc_html_e( 'Upload/Add image', 'porto' ); ?></button>
															<button type="submit" class="remove_swatch_image_button button" rel="<?php echo esc_attr( $post_id ); ?>"><?php esc_html_e( 'Remove image', 'porto' ); ?></button>
														</div>

													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>

									</table>
								</div>
							</div>
						</div>
						<?php
					endforeach;
				else :
					echo '<p>' . esc_html__( 'Please add the attributes from the "Attributes" tab and create a variation and save the product. After that you will see the option to configure the image/color swatch.', 'porto' ) . '</p>';
				endif;
				?>

			</div>

			<?php
			echo '</div>';
		}

		public function process_meta_box( $post_id, $post ) {

			$product = wc_get_product( $post_id );

			$swatch_options = isset( $_POST['swatch_options'] ) ? $_POST['swatch_options'] : false;
			if ( $swatch_options && is_array( $swatch_options ) ) {
				$product->update_meta_data( 'swatch_options', porto_sanitize_array( $swatch_options ) );
			}
			$product->save_meta_data();
		}

	}
}
