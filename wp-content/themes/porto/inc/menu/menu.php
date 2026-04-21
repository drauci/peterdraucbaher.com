<?php

// add custom menu fields to menu
add_filter( 'wp_setup_nav_menu_item', 'porto_add_custom_nav_fields' );

function porto_add_custom_nav_fields( $menu_item ) {
	if ( ! empty( $menu_item ) && ! empty( $menu_item->ID ) ) {
		$menu_item->icon              = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
		$menu_item->icon_type         = get_post_meta( $menu_item->ID, '_menu_item_icon_type', true );
		$menu_item->icon_svg          = get_post_meta( $menu_item->ID, '_menu_item_icon_svg', true );
		$menu_item->icon_img          = get_post_meta( $menu_item->ID, '_menu_item_icon_img', true );
		$menu_item->icon_img_wd       = get_post_meta( $menu_item->ID, '_menu_item_icon_img_wd', true );
		$menu_item->nolink            = get_post_meta( $menu_item->ID, '_menu_item_nolink', true );
		$menu_item->hide              = get_post_meta( $menu_item->ID, '_menu_item_hide', true );
		$menu_item->mobile_hide       = get_post_meta( $menu_item->ID, '_menu_item_mobile_hide', true );
		$menu_item->popup_mobile_hide = get_post_meta( $menu_item->ID, '_menu_item_popup_mobile_hide', true );
		$menu_item->cols              = get_post_meta( $menu_item->ID, '_menu_item_cols', true );
		$menu_item->tip_label         = get_post_meta( $menu_item->ID, '_menu_item_tip_label', true );
		$menu_item->tip_color         = get_post_meta( $menu_item->ID, '_menu_item_tip_color', true );
		$menu_item->tip_bg            = get_post_meta( $menu_item->ID, '_menu_item_tip_bg', true );
		$menu_item->popup_type        = get_post_meta( $menu_item->ID, '_menu_item_popup_type', true );
		$menu_item->popup_pos         = get_post_meta( $menu_item->ID, '_menu_item_popup_pos', true );
		$menu_item->popup_cols        = get_post_meta( $menu_item->ID, '_menu_item_popup_cols', true );
		$menu_item->popup_max_width   = get_post_meta( $menu_item->ID, '_menu_item_popup_max_width', true );
		$menu_item->popup_bg_image    = get_post_meta( $menu_item->ID, '_menu_item_popup_bg_image', true );
		$menu_item->popup_bg_pos      = get_post_meta( $menu_item->ID, '_menu_item_popup_bg_pos', true );
		$menu_item->popup_bg_repeat   = get_post_meta( $menu_item->ID, '_menu_item_popup_bg_repeat', true );
		$menu_item->popup_bg_size     = get_post_meta( $menu_item->ID, '_menu_item_popup_bg_size', true );
		$menu_item->popup_style       = get_post_meta( $menu_item->ID, '_menu_item_popup_style', true );
		$menu_item->block             = get_post_meta( $menu_item->ID, '_menu_item_block', true );
		$menu_item->preview           = get_post_meta( $menu_item->ID, '_menu_item_preview', true );
		$menu_item->preview_fixed     = get_post_meta( $menu_item->ID, '_menu_item_preview_fixed', true );
	}
	return $menu_item;
}

// save menu custom fields
add_action( 'wp_update_nav_menu_item', 'porto_update_custom_nav_fields', 10, 3 );

function porto_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
	$check = array( 'icon_type', 'icon', 'icon_svg', 'icon_img', 'icon_img_wd', 'nolink', 'hide', 'mobile_hide', 'popup_mobile_hide', 'cols', 'popup_type', 'popup_pos', 'popup_cols', 'popup_max_width', 'popup_bg_image', 'popup_bg_pos', 'popup_bg_repeat', 'popup_bg_size', 'popup_style', 'block', 'tip_label', 'tip_color', 'tip_bg', 'preview', 'preview_fixed' );

	foreach ( $check as $key ) {

		if ( ! isset( $_POST[ 'menu-item-' . $key ][ $menu_item_db_id ] ) ) {
			if ( ! isset( $args[ 'menu-item-' . $key ] ) ) {
				$value = '';
			} else {
				$value = $args[ 'menu-item-' . $key ];
			}
		} else {
			$value = sanitize_text_field( $_POST[ 'menu-item-' . $key ][ $menu_item_db_id ] );
		}

		if ( $value ) {
			update_post_meta( $menu_item_db_id, '_menu_item_' . $key, $value );
		} else {
			delete_post_meta( $menu_item_db_id, '_menu_item_' . $key );
		}
	}
}

// edit menu walker
add_filter( 'wp_edit_nav_menu_walker', 'porto_menu_edit_walker', 10, 2 );

function porto_menu_edit_walker( $walker = '', $menu_id = '' ) {
	return 'Porto_Walker_Nav_Menu_Edit';
}

// Create HTML list of nav menu input items.
// Extend from Walker_Nav_Menu class
class Porto_Walker_Nav_Menu_Edit extends Walker_Nav_Menu {
	/**
	 * @see Walker_Nav_Menu::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
	}

	/**
	 * @see Walker_Nav_Menu::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $_wp_nav_menu_max_depth;

		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$item_id      = $item->ID;
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);
		ob_start();
		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) ) {
				$original_title = false;
			}
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title  = $original_object->post_title;
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( '%s (Invalid)', $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( '%s (Pending)', $item->title );
		}

		$title = empty( $item->label ) ? $title : $item->label;

		$porto_blocks = porto_get_post_type_items(
			'porto_builder',
			array(
				'meta_query' => array(
					array(
						'key'   => 'porto_builder_type',
						'value' => 'block',
					),
				),
			),
			'all'
		);
		foreach ( $porto_blocks as $slug => $arg ) {
			if ( $arg['id'] ) {
				$link = admin_url( 'post.php?post=' . $arg['id'] . '&action=edit' );
				if ( defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $arg['id'], '_elementor_edit_mode', true ) ) {
					$link = add_query_arg( 'action', 'elementor', $link );
				}
				$porto_blocks[$slug]['link'] = $link;
			}
		}
		$porto_blocks = array_merge(
			array(
				'' => array(
					'title' => __( 'Select', 'porto' ),
					'id'    => '',
					'link'  => ''
				)
			),
			$porto_blocks
		);
		?>
	<li id="menu-item-<?php echo esc_attr( $item_id ); ?>" class="<?php echo implode( ' ', $classes ); ?>">
	<dl class="menu-item-bar">
		<dt class="menu-item-handle">
			<input id="menu-item-checkbox-<?php echo esc_attr( $item_id ); ?>" type="checkbox" class="menu-item-checkbox" data-menu-item-id="<?php echo esc_attr( $item_id ); ?>">
			<span class="menu-item-title item-title"><?php echo esc_html( $title ); ?></span>
			<span class="item-controls">
				<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
				<span class="item-order hide-if-js">
					<a href="<?php
						echo wp_nonce_url(
							esc_url(
								add_query_arg(
									array(
										'action'    => 'move-up-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								)
							),
							'move-menu_item'
						);
					?>" class="item-move-up"><abbr title="Move up">&#8593;</abbr></a>

					<a href="<?php
						echo wp_nonce_url(
							esc_url(
								add_query_arg(
									array(
										'action'    => 'move-down-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								)
							),
							'move-menu_item'
						);
					?>" class="item-move-down"><abbr title="Move down">&#8595;</abbr></a>
				</span>
				<a class="item-edit" id="edit-<?php echo esc_attr( $item_id ); ?>" title="Edit Menu Item" href="<?php
						echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] )
						? esc_url( admin_url( 'nav-menus.php' ) )
						: esc_url(
							add_query_arg(
								'edit-menu-item',
								$item_id,
								remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) )
							)
						);
					?>"><span class="screen-reader-text"><?php esc_html_e( 'Edit Menu Item', 'porto' ); ?></span></a>
			</span>
		</dt>
	</dl>

	<div class="menu-item-settings" id="menu-item-settings-<?php echo esc_attr( $item_id ); ?>">
		<?php if ( 'custom' == $item->type ) : ?>
	<p class="description description-wide">
		<label for="edit-menu-item-url-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'URL', 'porto' ); ?><br />
			<input type="text" id="edit-menu-item-url-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-url"
				<?php if ( $item->url ) : ?>
					name="menu-item-url[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-url[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( $item->url ); ?>" />
		</label>
	</p>
		<?php endif; ?>
	<p class="description description-wide">
		<label for="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Navigation Label', 'porto' ); ?><br />
			<input type="text" id="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>" class="widefat edit-menu-item-title"
				<?php if ( $item->title ) : ?>
					name="menu-item-title[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-title[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( $item->title ); ?>" />
		</label>
	</p>
	<p class="description">
		<label for="edit-menu-item-target-<?php echo esc_attr( $item_id ); ?>">
			<input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr( $item_id ); ?>" value="_blank"
				<?php if ( '_blank' == $item->target ) : ?>
					name="menu-item-target[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-target[<?php echo esc_attr( $item_id ); ?>]"
				<?php checked( $item->target, '_blank' ); ?> />
			<?php echo 'Open link in a new window/tab'; ?>
		</label>
	</p>
		<?php
		/* New fields insertion starts here */
		?>
	<div class="menu-dependency-wrap">
		<p class="description description-wide">
			<label for="edit-menu-item-icon_type-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Prefix Icon Type', 'porto' ); ?><br />
				<select data-dependency="icon-type" id="edit-menu-item-icon_type-<?php echo esc_attr( $item_id ); ?>"
					<?php if ( $item->icon_type ) : ?>
						name="menu-item-icon_type[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
					data-name="menu-item-icon_type[<?php echo esc_attr( $item_id ); ?>]"
					>
					<option value="" 
					<?php
					if ( '' == $item->icon_type ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Icon Class', 'porto' ); ?></option>
					<option value="svg" 
					<?php
					if ( 'svg' == $item->icon_type ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Upload SVG', 'porto' ); ?></option>
					<option value="img" 
					<?php
					if ( 'img' == $item->icon_type ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Image', 'porto' ); ?></option>
				</select>
			</label>
		</p>

		<p class="description description-wide" data-dependency-icon-type="">
			<label for="edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Icon Class', 'porto' ); ?><br />
				<input type="text" id="edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-icon"
					<?php if ( $item->icon ) : ?>
						name="menu-item-icon[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-icon[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->icon ); ?>" />
				<?php /* translators: $1: opening A tag which has link to the FontAwesome icons page $2: closing A tag */ ?>
				<span><?php printf( esc_html__( 'Input icon class. You can see %1$sFont Awesome Icons in here%2$s. For example: fas fa-user', 'porto' ), '<a target="_blank" href="https://www.portotheme.com/wordpress/porto/shortcodes/icons/" rel="noopener noreferrer">', '</a>' ); ?></span>
			</label>
		</p>
		<p class="description description-wide" data-dependency-icon-type="svg">
			<label for="edit-menu-item-icon_svg-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Upload SVG', 'porto' ); ?> <a target="_blank" href="<?php echo porto_get_theme_option_url( 'allow-svg-upload' ); ?>"><?php esc_html_e( 'Allow SVG Upload', 'porto' ); ?></a><br />
				<input type="text" id="edit-menu-item-icon_svg-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-icon_svg"
					<?php if ( $item->icon_svg ) : ?>
						name="menu-item-icon_svg[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-icon_svg[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->icon_svg ); ?>" />
				<input class="btn_upload_svg button" id="edit-menu-item-icon_svg-<?php echo esc_attr( $item->ID ); ?>" type="button" value="Upload Svg" />&nbsp;
				<input class="btn_remove_svg button" id="edit-menu-item-icon_svg-<?php echo esc_attr( $item->ID ); ?>" type="button" value="Remove Svg" />
				
			</label>
		</p>
		<p class="description description-wide" data-dependency-icon-type="img">
			<label for="edit-menu-item-icon_img-<?php echo esc_attr( $item_id ); ?>">

				<span class="attach_image" id="edit-menu-item-icon_img-<?php echo esc_attr( $item_id ); ?>_thumb">
					<?php
					if ( $item->icon_img ) {
						echo wp_get_attachment_image( (int) $item->icon_img, 'thumbnail' );
					}
					?>
				</span>
				<input value="<?php echo stripslashes( $item->icon_img ); ?>" type="hidden" id="edit-menu-item-icon_img-<?php echo esc_attr( $item_id ); ?>" class="edit-menu-item-icon_img"
					<?php if ( $item->icon_img ) : ?>
						name="menu-item-icon_img[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-icon_img[<?php echo esc_attr( $item_id ); ?>]"
				/>
				<button class="button_attach_image button" data-id="edit-menu-item-icon_img-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'Attach Image', 'porto-functionality' ); ?></button>
				<button class="button_remove_image button" data-id="edit-menu-item-icon_img-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'Remove Image', 'porto-functionality' ); ?></button>
				<br/>
				<?php esc_html_e( 'Image Width(px)', 'porto' ); ?>
				<br/>
				<input type="number" placeholder="20" id="edit-menu-item-icon_img_wd-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-icon_img_wd"
					<?php if ( $item->icon_img_wd ) : ?>
						name="menu-item-icon_img_wd[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-icon_img_wd[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->icon_img_wd ); ?>" />
			</label>
		</p>
	</div>

	<p class="description">
		<label for="edit-menu-item-nolink-<?php echo esc_attr( $item_id ); ?>">
			<input type="checkbox" id="edit-menu-item-nolink-<?php echo esc_attr( $item_id ); ?>" class="code edit-menu-item-custom" value="nolink"
				<?php if ( 'nolink' == $item->nolink ) : ?>
					name="menu-item-nolink[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-nolink[<?php echo esc_attr( $item_id ); ?>]"
				<?php checked( $item->nolink, 'nolink' ); ?> />
			<?php esc_html_e( "Don't link", 'porto' ); ?>
		</label>
	</p>
	<p class="description">
		<label for="edit-menu-item-hide-<?php echo esc_attr( $item_id ); ?>">
			<input type="checkbox" id="edit-menu-item-hide-<?php echo esc_attr( $item_id ); ?>" class="code edit-menu-item-custom" value="hide"
				<?php if ( 'hide' == $item->hide ) : ?>
					name="menu-item-hide[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-hide[<?php echo esc_attr( $item_id ); ?>]"
				<?php checked( $item->hide, 'hide' ); ?> />
			<?php esc_html_e( "Don't show a link on desktop", 'porto' ); ?>
		</label>
	</p>
	<p class="description">
		<label for="edit-menu-item-mobile_hide-<?php echo esc_attr( $item_id ); ?>">
			<input type="checkbox" id="edit-menu-item-mobile_hide-<?php echo esc_attr( $item_id ); ?>" class="code edit-menu-item-custom" value="hide"
				<?php if ( 'hide' == $item->mobile_hide ) : ?>
					name="menu-item-mobile_hide[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-mobile_hide[<?php echo esc_attr( $item_id ); ?>]"
				<?php checked( $item->mobile_hide, 'hide' ); ?> />
			<?php esc_html_e( "Don't show a link ( < 992px )", 'porto' ); ?>
		</label>
	</p>
	<h4 class="description-wide porto-separator-title porto-popup-title" style="display: none;">Popup Settings</h4>
	<div class="menu-dependency-wrap edit-menu-item-level0-<?php echo esc_attr( $item_id ); ?>" style="<?php echo 0 == $depth ? 'display:block;' : 'display:none;'; ?>">
		<div style="clear:both;"></div>
		<p class="description description-thin description-thin-custom">
			<label for="edit-menu-item-type-menu-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Menu Type', 'porto' ); ?><br />
				<select data-dependency="menu-type" id="edit-menu-item-type-menu-<?php echo esc_attr( $item_id ); ?>"
					<?php if ( $item->popup_type ) : ?>
						name="menu-item-popup_type[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
					data-name="menu-item-popup_type[<?php echo esc_attr( $item_id ); ?>]"
					>
					<option value="" 
					<?php
					if ( '' == $item->popup_type ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Narrow ( Simple Menu )', 'porto' ); ?></option>
					<option value="wide" 
					<?php
					if ( 'wide' == $item->popup_type ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Wide ( Mega Menu )', 'porto' ); ?></option>
				</select>
			</label>
		</p>
		<p class="description description-thin description-thin-custom">
			<label for="edit-menu-item-popup_pos-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Popup Position', 'porto' ); ?><br />
				<select data-dependency="menu-pos" id="edit-menu-item-popup_pos-<?php echo esc_attr( $item_id ); ?>"
					<?php if ( isset( $item->popup_pos ) ) : ?>
						name="menu-item-popup_pos[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
					data-name="menu-item-popup_pos[<?php echo esc_attr( $item_id ); ?>]">
					<option value="pos-left" 
					<?php
					if ( 'pos-left' == $item->popup_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Left', 'porto' ); ?></option>
					<option value="pos-right" 
					<?php
					if ( 'pos-right' == $item->popup_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Right', 'porto' ); ?></option>
					<option value="" 
					<?php
					if ( '' == $item->popup_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Justify (for wide)', 'porto' ); ?></option>
					<option value="pos-center" 
					<?php
					if ( 'pos-center' == $item->popup_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Center (for wide)', 'porto' ); ?></option>
					<option value="pos-fullwidth" 
					<?php
					if ( 'pos-fullwidth' == $item->popup_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Full Width (for wide)', 'porto' ); ?></option>
				</select>
			</label>
		</p>
		<div style="clear:both;"></div>
		<p class="description description-wide" data-dependency-menu-type="wide">
			<label for="edit-menu-item-popup_cols-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Popup Columns', 'porto' ); ?><br />
				<select id="edit-menu-item-popup_cols-<?php echo esc_attr( $item_id ); ?>"
					<?php if ( $item->popup_cols ) : ?>
						name="menu-item-popup_cols[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-popup_cols[<?php echo esc_attr( $item_id ); ?>]"
					>
					<option value="" 
					<?php
					if ( '' == $item->popup_cols ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Select (4 Columns)', 'porto' ); ?></option>
					<option value="col-1" 
					<?php
					if ( 'col-1' == $item->popup_cols ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( '1 Columns', 'porto' ); ?></option>
					<option value="col-2" 
					<?php
					if ( 'col-2' == $item->popup_cols ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( '2 Columns', 'porto' ); ?></option>
					<option value="col-3" 
					<?php
					if ( 'col-3' == $item->popup_cols ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( '3 Columns', 'porto' ); ?></option>
					<option value="col-4" 
					<?php
					if ( 'col-4' == $item->popup_cols ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( '4 Columns', 'porto' ); ?></option>
					<option value="col-5" 
					<?php
					if ( 'col-5' == $item->popup_cols ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( '5 Columns', 'porto' ); ?></option>
					<option value="col-6" 
					<?php
					if ( 'col-6' == $item->popup_cols ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( '6 Columns', 'porto' ); ?></option>
				</select>
			</label>
		</p>
		<p class="description description-wide" data-dependency-menu-type="wide">
			<label for="edit-menu-item-popup_max_width-<?php echo esc_attr( $item_id ); ?>" data-dependency-menu-pos="<?php esc_attr_e( json_encode( array( 'pos-left', 'pos-right', 'pos-center', '' ) ) ); ?>">
				<?php esc_html_e( 'Popup Max Width', 'porto' ); ?><br />
				<input placeholder="e.g: 480px;" type="text" id="edit-menu-item-popup_max_width-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-popup_max_width"
					<?php if ( $item->popup_max_width ) : ?>
						name="menu-item-popup_max_width[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-popup_max_width[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->popup_max_width ); ?>" />
			</label>
		</p>
	</div>
	<div class="edit-menu-item-level1-<?php echo esc_attr( $item_id ); ?>" style="<?php echo 1 == $depth ? 'display:block;' : 'display:none;'; ?>">
		<div style="clear:both;"></div>
		<p class="description description-wide" data-dependency-menu-type="wide">
			<label for="edit-menu-item-cols-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Columns', 'porto' ); ?><br />
				<input type="text" id="edit-menu-item-cols-<?php echo esc_attr( $item_id ); ?>" class="widefat edit-menu-item-cols"
					<?php if ( $item->cols ) : ?>
						name="menu-item-cols[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-cols[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->cols ? $item->cols : 1 ); ?>" />
				<span class="description"><?php esc_html_e( 'will occupy x columns of parent popup columns', 'porto' ); ?></span>
			</label>
		</p>
		<p class="description description-wide">
			<label for="edit-menu-item-block-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Select Block', 'porto' ); ?><br />
				<?php $block_link = false;
				?>
				<select id="edit-menu-item-poup_block-<?php echo esc_attr( $item_id ); ?>" class="widefat edit-menu-item-block"
					<?php if ( $item->block ) : ?>
						name="menu-item-block[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-block[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->block ); // textarea_escaped ?>">
				<?php foreach ( $porto_blocks as $slug => $arg ) { ?>
					<?php if ( $slug == $item->block ) {
						$block_link = $arg['link'];
					} ?>
					<option value="<?php echo esc_attr( $slug ) ?>"<?php echo ( $slug == $item->block ? ' selected="selected"' : '' );?> data-link="<?php echo esc_attr( $arg['link'] ); ?>"><?php echo esc_attr( $arg['title'] ) ?></option>
				<?php } ?>
				</select>
					<a href="<?php echo esc_url( $block_link ) ?>" target="_blank" class="porto-block-link" style="<?php echo ( $block_link ? 'display:block;' : 'display:none;' ); ?>"><i class="fas fa-edit" style="margin-inline-end: 4px"></i><?php esc_html_e( 'Edit this block with Page Builder', 'porto' ); ?></a>
					<a href="<?php echo admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=block' ) ?>" target="_blank" class="porto-empty-link" style="<?php echo ( $block_link ? 'display:none;' : 'display:block;' ); ?>"><i class="fas fa-edit" style="margin-inline-end: 4px"></i><?php esc_html_e( 'Create new block', 'porto' ); ?></a>
			</label>
		</p>
		<p class="description">
			<label for="edit-menu-item-popup_mobile_hide-<?php echo esc_attr( $item_id ); ?>">
				<input type="checkbox" id="edit-menu-item-popup_mobile_hide-<?php echo esc_attr( $item_id ); ?>" class="code edit-menu-item-custom" value="hide"
					<?php if ( 'hide' == $item->popup_mobile_hide ) : ?>
						name="menu-item-popup_mobile_hide[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-popup_mobile_hide[<?php echo esc_attr( $item_id ); ?>]"
					<?php checked( $item->popup_mobile_hide, 'hide' ); ?> />
				<?php esc_html_e( 'Hide the block on mobile', 'porto' ); ?>
			</label>
		</p>
		<br/>
	</div>
	<div class="edit-menu-item-level01-<?php echo esc_attr( $item_id ); ?>" style="<?php echo 0 == $depth || 1 == $depth ? 'display:block;' : 'display:none;'; ?>">
		<p class="description description-wide">
			<label for="edit-menu-item-popup_style-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Custom Styles(It is not used for mobile menu.)', 'porto' ); ?><br />
				<textarea placeholder="e.g: width: 700px;" id="edit-menu-item-popup_style-<?php echo esc_attr( $item_id ); ?>" class="code widefat edit-menu-item-popup_style" rows="3" cols="20"
					<?php if ( $item->popup_style ) : ?>
						name="menu-item-popup_style[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-popup_style[<?php echo esc_attr( $item_id ); ?>]"
					><?php echo esc_html( $item->popup_style ); // textarea_escaped ?></textarea>
			</label>
		</p>
	</div>
	<div class="edit-menu-item-level1-<?php echo esc_attr( $item_id ); ?>" style="<?php echo 1 == $depth || 0 == $depth ? 'display:block;' : 'display:none;'; ?>">
		<h4 class="description-wide porto-separator-title" style="<?php echo 1 == $depth || 0 == $depth ? 'display:block;' : 'display:none;'; ?>">Background Settings</h4>
		<p class="description description-wide" style="<?php echo 1 == $depth || 0 == $depth ? 'display:block;' : 'display:none;'; ?>">
			<label for="edit-menu-item-popup_bg_image-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Background Image', 'porto' ); ?><br />
				<input type="text" id="edit-menu-item-popup_bg_image-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-popup_bg_image"
					<?php if ( $item->popup_bg_image ) : ?>
						name="menu-item-popup_bg_image[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-popup_bg_image[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->popup_bg_image ); ?>" />
				<br/>
				<input class="button_upload_image button" data-id="edit-menu-item-popup_bg_image-<?php echo esc_attr( $item_id ); ?>" type="button" value="Upload Image" />&nbsp;
				<input class="button_remove_image button" data-id="edit-menu-item-popup_bg_image-<?php echo esc_attr( $item_id ); ?>" type="button" value="Remove Image" />
			</label>
		</p>
		<p class="description description-thin" style="<?php echo 1 == $depth || 0 == $depth ? 'display:block;' : 'display:none;'; ?>">
			<label for="edit-menu-item-popup_bg_pos-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Background Position', 'porto' ); ?><br />
				<select id="edit-menu-item-popup_bg_pos-<?php echo esc_attr( $item_id ); ?>"
					<?php if ( $item->popup_bg_pos ) : ?>
						name="menu-item-popup_bg_pos[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-popup_bg_pos[<?php echo esc_attr( $item_id ); ?>]"
					>
					<option value="" 
					<?php
					if ( '' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Select', 'porto' ); ?></option>
					<option value="left top" 
					<?php
					if ( 'left top' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Left Top', 'porto' ); ?></option>
					<option value="left center" 
					<?php
					if ( 'left center' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Left Center', 'porto' ); ?></option>
					<option value="left bottom" 
					<?php
					if ( 'left bottom' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Left Bottom', 'porto' ); ?></option>
					<option value="center top" 
					<?php
					if ( 'center top' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Center Top', 'porto' ); ?></option>
					<option value="center center" 
					<?php
					if ( 'center center' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Center Center', 'porto' ); ?></option>
					<option value="center bottom" 
					<?php
					if ( 'center bottom' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Center Bottom', 'porto' ); ?></option>
					<option value="right top" 
					<?php
					if ( 'right top' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Right Top', 'porto' ); ?></option>
					<option value="right center" 
					<?php
					if ( 'right center' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Right Center', 'porto' ); ?></option>
					<option value="right bottom" 
					<?php
					if ( 'right bottom' == $item->popup_bg_pos ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Right Bottom', 'porto' ); ?></option>
					);
				</select>
			</label>
		</p>
		<p class="description description-thin" style="<?php echo 1 == $depth || 0 == $depth ? 'display:block;' : 'display:none;'; ?>">
			<label for="edit-menu-item-popup_bg_repeat-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Background Repeat', 'porto' ); ?><br />
				<select id="edit-menu-item-popup_bg_repeat-<?php echo esc_attr( $item_id ); ?>"
					<?php if ( $item->popup_bg_repeat ) : ?>
						name="menu-item-popup_bg_repeat[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-popup_bg_repeat[<?php echo esc_attr( $item_id ); ?>]"
					>
					<option value="" 
					<?php
					if ( '' == $item->popup_bg_repeat ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Select', 'porto' ); ?></option>
					<option value="no-repeat" 
					<?php
					if ( 'no-repeat' == $item->popup_bg_repeat ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'No Repeat', 'porto' ); ?></option>
					<option value="repeat" 
					<?php
					if ( 'repeat' == $item->popup_bg_repeat ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Repeat All', 'porto' ); ?></option>
					<option value="repeat-x" 
					<?php
					if ( 'repeat-x' == $item->popup_bg_repeat ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Repeat Horizontally', 'porto' ); ?></option>
					<option value="repeat-y" 
					<?php
					if ( 'repeat-y' == $item->popup_bg_repeat ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Repeat Vertically', 'porto' ); ?></option>
					<option value="inherit" 
					<?php
					if ( 'inherit' == $item->popup_bg_repeat ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Inherit', 'porto' ); ?></option>
				</select>
			</label>
		</p>
		<p class="description description-thin" style="<?php echo 1 == $depth || 0 == $depth ? 'display:block;' : 'display:none;'; ?>">
			<label for="edit-menu-item-popup_bg_size-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Background Size', 'porto' ); ?><br />
				<select id="edit-menu-item-popup_bg_size-<?php echo esc_attr( $item_id ); ?>"
					<?php if ( $item->popup_bg_size ) : ?>
						name="menu-item-popup_bg_size[<?php echo esc_attr( $item_id ); ?>]"
					<?php endif; ?>
						data-name="menu-item-popup_bg_size[<?php echo esc_attr( $item_id ); ?>]"
					>
					<option value="" 
					<?php
					if ( '' == $item->popup_bg_size ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Select', 'porto' ); ?></option>
					<option value="inherit" 
					<?php
					if ( 'inherit' == $item->popup_bg_size ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Inherit', 'porto' ); ?></option>
					<option value="cover" 
					<?php
					if ( 'cover' == $item->popup_bg_size ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Cover', 'porto' ); ?></option>
					<option value="contain" 
					<?php
					if ( 'contain' == $item->popup_bg_size ) {
						echo 'selected="selected"';}
					?>
					><?php esc_html_e( 'Contain', 'porto' ); ?></option>
				</select>
			</label>
		</p>
		<br/>
	</div>
	<h4 class="description-wide porto-separator-title">Menu Item Attribute</h4>
	<p class="description description-thin">
		<label for="edit-menu-item-attr-title-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Title Attribute', 'porto' ); ?><br />
			<input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr( $item_id ); ?>" class="widefat edit-menu-item-attr-title"
				<?php if ( $item->post_excerpt ) : ?>
					name="menu-item-attr-title[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-attr-title[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
		</label>
	</p>
	<p class="description description-thin">
		<label for="edit-menu-item-xfn-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Link Relationship (XFN)', 'porto' ); ?><br />
			<input type="text" id="edit-menu-item-xfn-<?php echo esc_attr( $item_id ); ?>" class="widefat edit-menu-item-xfn"
				<?php if ( $item->xfn ) : ?>
					name="menu-item-xfn[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-xfn[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( $item->xfn ); ?>" />
		</label>
	</p>
	<p class="description description-wide" style="display: none;">
		<label for="edit-menu-item-preview-<?php echo esc_attr( $item_id ); ?>">
			<?php echo 'Preview Image (default size: 182 x 136)'; ?><br />
			<input type="text" id="edit-menu-item-preview-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-preview" style="margin-bottom: 0;"
				<?php if ( $item->preview ) : ?>
					name="menu-item-preview[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-preview[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( $item->preview ); ?>" />
			<br/>
			<span style="display: block; margin: 5px 0;"><?php echo 'Preview images are hidden in the <b>Toggle and Sidebar menus.</b>'; ?></span>
			<input class="button_upload_image button" data-id="edit-menu-item-preview-<?php echo esc_attr( $item_id ); ?>" type="button" value="Upload Image" />&nbsp;
			<input class="button_remove_image button" data-id="edit-menu-item-preview-<?php echo esc_attr( $item_id ); ?>" type="button" value="Remove Image" />
		</label>
	</p>
	<p class="description description-wide" style="display: none;">
		<label for="edit-menu-item-preview_fixed-<?php echo esc_attr( $item_id ); ?>">
			<input type="checkbox" id="edit-menu-item-preview_fixed-<?php echo esc_attr( $item_id ); ?>" class="code edit-menu-item-custom" value="fixed"
				<?php if ( 'fixed' == $item->preview_fixed ) : ?>
					name="menu-item-preview_fixed[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-preview_fixed[<?php echo esc_attr( $item_id ); ?>]"
				<?php checked( $item->preview_fixed, 'fixed' ); ?> />
			<?php esc_html_e( 'Fixed Preview Image', 'porto' ); ?>
		</label>
	</p>
	<h4 class="description-wide porto-separator-title">Tip Settings</h4>
	<p class="description description-thin">
		<label for="edit-menu-item-tip_label-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Tip Label', 'porto' ); ?><br />
			<input placeholder="e.g: New" type="text" id="edit-menu-item-tip_label-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-tip_label"
				<?php if ( $item->tip_label ) : ?>
					name="menu-item-tip_label[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-tip_label[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( $item->tip_label ); ?>" />
		</label>
	</p>
	<p class="description description-thin">
		<label for="edit-menu-item-tip_color-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Tip Text Color', 'porto' ); ?><br />
			<input placeholder="e.g: #fff" type="text" id="edit-menu-item-tip_color-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-tip_color"
				<?php if ( $item->tip_color ) : ?>
					name="menu-item-tip_color[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-tip_color[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( $item->tip_color ); ?>" />
		</label>
	</p>
	<p class="description description-thin">
		<label for="edit-menu-item-tip_bg-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Tip BG Color', 'porto'); ?><br />
			<input placeholder="e.g: #08c" type="text" id="edit-menu-item-tip_bg-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-tip_bg"
				<?php if ( $item->tip_bg ) : ?>
					name="menu-item-tip_bg[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-tip_bg[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( $item->tip_bg ); ?>" />
		</label>
	</p>
		<?php
		/* New fields insertion ends here */
		?>
	<div style="clear:both;"></div>
	<h4 class="description-wide porto-separator-title">Other Settings</h4>
	<p class="description description-wide">
		<label for="edit-menu-item-classes-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'CSS Classes (optional)', 'porto' ); ?><br />
			<input type="text" id="edit-menu-item-classes-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-classes"
				<?php if ( implode( ' ', $item->classes ) ) : ?>
					name="menu-item-classes[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
					data-name="menu-item-classes[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( implode( ' ', $item->classes ) ); ?>" />
		</label>
	</p>
	<p class="description description-wide">
		<label for="edit-menu-item-description-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Description', 'porto' ); ?><br />
			<textarea id="edit-menu-item-description-<?php echo esc_attr( $item_id ); ?>" class="widefat edit-menu-item-description" rows="3" cols="20"
				<?php if ( $item->description ) : ?>
						name="menu-item-description[<?php echo esc_attr( $item_id ); ?>]"
				<?php endif; ?>
						data-name="menu-item-description[<?php echo esc_attr( $item_id ); ?>]"
					><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
			<span class="description"><?php echo 'The description will be displayed in the menu if the current theme supports it.'; ?></span>
		</label>
	</p>

		<?php
		// Add this directly after the description paragraph in the start_el() method
		do_action( 'wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args );
		// end added section
		?>

	<div class="menu-item-actions description-wide submitbox">
		<?php if ( 'custom' != $item->type && false !== $original_title ) : ?>
		<p class="link-to-original">
			<?php printf( esc_html__( 'Original: %s', 'porto' ), '<a href="' . esc_url( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
		</p>
		<?php endif; ?>
		<a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr( $item_id ); ?>" href="<?php
			echo wp_nonce_url(
				esc_url(
					add_query_arg(
						array(
							'action'    => 'delete-menu-item',
							'menu-item' => $item_id,
						),
						remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
					)
				),
				'delete-menu_item_' . $item_id
			);
			?>">Remove</a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo esc_attr( $item_id ); ?>" href="<?php
			echo esc_url(
				add_query_arg(
					array(
						'edit-menu-item' => $item_id,
						'cancel'         => time(),
					),
					remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
				)
			);
			?>#menu-item-settings-<?php echo esc_attr( $item_id ); ?>">Cancel</a>
	</div>

	<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item_id ); ?>" />
	<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
	<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
	<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
	<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
	<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
	<div class="clear"></div>
	</div><!-- .menu-item-settings-->
	<ul class="menu-item-transport"></ul>
	</li>
		<?php
		$output .= ob_get_clean();
	}
}

/* Top Navigation Menu */
if ( ! class_exists( 'porto_top_navwalker' ) ) {
	class porto_top_navwalker extends Walker_Nav_Menu {

		private $has_active = false;

		// add classes to ul sub menus
		function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			if ( empty( $depth ) ) {
				$depth = 0;
			}
			$id_field = $this->db_fields['id'];
			if ( is_object( $args[0] ) ) {
				$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
			}
			if ( apply_filters( 'porto_lazymenu_depth', false ) ) {
				$max_depth = 1;
			}
			return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		// add popup class to ul sub-menus
		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );
			if ( 0 == $depth ) {
				$out_div = '<div class="popup"' . ( empty( $args->popup_max_width ) ? '' : ' data-popup-mw="' . intval( $args->popup_max_width ) . '"' ) . '><div class="inner" style="' . esc_attr( $args->popup_style ) . '">';
			} else {
				$out_div = '';
			}
			$extra_cls = '';
			if ( 0 == $depth ) {
				if ( isset( $args->menu_popup_type ) && 'wide' == $args->menu_popup_type ) {
					$extra_cls .= ' porto-wide-sub-menu';
				} else {
					$extra_cls .= ' porto-narrow-sub-menu';
				}
			}
			$output .= "\n$indent$out_div<ul class=\"sub-menu" . $extra_cls . "\">\n";
		}

		function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );
			if ( 0 == $depth ) {
				$out_div = '</div></div>';
			} else {
				$out_div = '';
			}
			$output .= "$indent</ul>$out_div\n";
		}

		// add main/sub classes to li's and links
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $wp_query;

			$sub    = '';
			$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
			if ( 0 == $depth && $args->has_children ) {
				$sub = ' has-sub';
			}

			if ( 1 == $depth && $args->has_children ) {
				$sub = ' sub';
			}

			$active = '';

			// depth dependent classes
			if ( $item->current || $item->current_item_ancestor || $item->current_item_parent || in_array( 'current_page_item', (array) $item->classes ) ) {
				if ( 0 == $depth ) {
					if ( ! $this->has_active ) {
						$active           = ' active';
						$this->has_active = true;
					}
				} else {
					$active = ' active';
				}
			}

			// passed classes
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;

			/*if ( in_array( 'current-page-ancestor', $classes ) || in_array( 'current_page_item', $classes ) ) {
				//$active = 'active';
			}*/

			$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

			// menu type, type, column class, popup style
			$menu_type   = '';
			$popup_pos   = '';
			$popup_cols  = '';
			$popup_style = '';
			$cols        = 1;

			if ( 0 == $depth ) {
				unset( $args->popup_max_width, $args->menu_popup_type );
				if ( 'wide' == $item->popup_type ) {
					$menu_type = ' wide';

					if ( '' == $item->popup_cols ) {
						$item->popup_cols = 'col-4';
					}
					$popup_cols = ' ' . $item->popup_cols;

					$popup_bg_image  = $item->popup_bg_image ? 'background-image:url(' . str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $item->popup_bg_image ) . ');' : '';
					$popup_bg_pos    = $item->popup_bg_pos ? 'background-position:' . $item->popup_bg_pos . ';' : '';
					$popup_bg_repeat = $item->popup_bg_repeat ? 'background-repeat:' . $item->popup_bg_repeat . ';' : '';
					$popup_bg_size   = $item->popup_bg_size ? 'background-size:' . $item->popup_bg_size . ';' : '';
					$popup_max_width = ( $item->popup_max_width && ( ! isset( $item->popup_pos ) || isset( $item->popup_pos ) && 'pos-fullwidth' != $item->popup_pos ) ) ? 'max-width:' . (int) $item->popup_max_width . 'px;' : '';

					$popup_style = str_replace( '"', '\'', $item->popup_style . $popup_bg_image . $popup_bg_pos . $popup_bg_repeat . $popup_bg_size . $popup_max_width );
					if ( $item->popup_max_width ) {
						$args->popup_max_width = $item->popup_max_width;
					}
					$args->menu_popup_type = 'wide';
				} else {
					$menu_type = ' narrow';
				}

				if ( $item->popup_pos ) {
					$popup_pos = ' ' . $item->popup_pos;
				}
			}

			// build html
			if ( 1 == $depth ) {
				$sub_popup_style = '';
				if ( $item->popup_style || $item->popup_bg_image || $item->popup_bg_pos || $item->popup_bg_repeat || $item->popup_bg_size ) {
					$sub_popup_image  = $item->popup_bg_image ? 'background-image:url(' . str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $item->popup_bg_image ) . ');' : '';
					$sub_popup_pos    = $item->popup_bg_pos ? 'background-position:' . $item->popup_bg_pos . ';' : '';
					$sub_popup_repeat = $item->popup_bg_repeat ? 'background-repeat:' . $item->popup_bg_repeat . ';' : '';
					$sub_popup_size   = $item->popup_bg_size ? 'background-size:' . $item->popup_bg_size . ';' : '';
					$sub_popup_style  = ' style="' . esc_attr( str_replace( '"', '\'', $item->popup_style ) . $sub_popup_image . $sub_popup_pos . $sub_popup_repeat . $sub_popup_size ) . '"';
				}
				$cols = (float) $item->cols;
				if ( $cols <= 0 ) {
					$cols = 1;
				}
				if ( $item->block ) {
					$class_names .= ' menu-block-item ';
				}
				$output .= $indent . '<li id="nav-menu-item-' . $item->ID . '" class="' . $class_names . $active . $sub . $menu_type . $popup_pos . $popup_cols . '" data-cols="' . $cols . '"' . $sub_popup_style . '>';
			} else {
				$output .= $indent . '<li id="nav-menu-item-' . $item->ID . '" class="' . $class_names . $active . $sub . $menu_type . $popup_pos . $popup_cols . '">';
			}

			$current_a = '';

			// link attributes
			$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_url( $item->url ) . '"' : '';

			if ( ( $item->current && 0 == $depth ) || ( $item->current_item_ancestor && 0 == $depth ) ) {
				$current_a .= ' current';
			}

			if ( $preview = $item->preview ) {
				$current_a .= ' has-preview';
			}

			$link_class = '';
			if ( isset( $item->mobile_hide ) && '' !== $item->mobile_hide ) {
				$link_class = ' mobile-link-hide';
			}
			if ( isset( $item->hide ) && '' !== $item->hide ) {
				$link_class = ' desktop-link-hide';
			}
			if ( '' == $item->nolink ) {
				$current_a .= $link_class;
			}
			$attributes .= $current_a ? ' class="' . $current_a . '"' : '';
			$item_output = $args->before;
			if ( '' == $item->hide || '' == $item->mobile_hide ) {
				if ( empty( $item->nolink ) ) {
					$item_output .= '<a' . $attributes . '>';
				} else {
					$item_output .= '<a class="nolink' . $link_class . '" href="#">';
				}
				if ( $item->icon ) {
					$item->icon   = trim( $item->icon );
				}
				if ( isset( $item->icon_type ) && 'svg' == $item->icon_type ) {
					$item_output .= $args->link_before;

					if ( ! empty( $item->icon_svg ) ) {
						$svg_id = attachment_url_to_postid( $item->icon_svg );

						if ( $svg_id ) {
							$svg_file = get_attached_file( $svg_id );
						}

						if ( isset( $svg_file ) && file_exists( $svg_file ) ) {
							if ( defined( 'ELEMENTOR_VERSION' ) ) {
								$svg       = Elementor\Utils::file_get_contents( $svg_file );
								$valid_svg = ( new Elementor\Core\Utils\Svg\SVG_Sanitizer() )->sanitize( $svg );
							} else { // attackable
								$valid_svg = file_get_contents( $svg_file );
							}
						}
						if ( isset( $valid_svg ) ) {
							$item_output .= $valid_svg;
						}
					}

					$item_output .= apply_filters( 'the_title', $item->title, $item->ID );
				} elseif ( isset( $item->icon_type ) && 'img' == $item->icon_type ) {
					$item_output .= $args->link_before;
					if ( ! empty( $item->icon_img ) ) {
						$sz_wd        = ! empty( $item->icon_img_wd ) ? (int) $item->icon_img_wd : 20;
						$item_output .= wp_get_attachment_image( (int) $item->icon_img, array( $sz_wd, 0 ) );
					}
					$item_output .= apply_filters( 'the_title', $item->title, $item->ID );
				} else {
					$item_output .= $args->link_before . ( $item->icon ? '<i class="' . esc_attr( 0 === strpos( $item->icon, 'fa-' ) ? 'fa ' . $item->icon : $item->icon ) . '"></i>' : '' ) . apply_filters( 'the_title', $item->title, $item->ID );
				}
				$item_output .= $args->link_after;
				if ( $item->tip_label ) {
					$item_style = '';
					if ( $item->tip_color ) {
						$item_style .= 'color:' . $item->tip_color . ';';
					}
					if ( $item->tip_bg ) {
						$item_style .= 'background:' . $item->tip_bg . ';';
						$item_style .= 'border-color:' . $item->tip_bg . ';';
					}
					$item_output .= '<span class="tip" style="' . esc_attr( $item_style ) . '">' . esc_html( $item->tip_label ) . '</span>';
				}

				// preview image
				if ( $preview = $item->preview ) {
					porto_enqueue_link_style( 'porto-thumb-info', PORTO_CSS . '/part/thumb-info' . ( is_rtl() ? '_rtl': '' ) . '.css' );
					porto_enqueue_link_style( 'porto-menu-thumb-info', PORTO_CSS . '/part/menu-thumb-info' . ( is_rtl() ? '_rtl': '' ) . '.css' );
					$item_output .= '<span class="thumb-info thumb-info-preview"><span class="thumb-info-wrapper"><span class="thumb-info-image' . ( $item->preview_fixed ? ' fixed-image' : '' ) . '" style="background-image: url(' . str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $preview ) . ');"></span></span></span>';
				}

				$item_output .= '</a>';
			}
			if ( $item->block ) {
				static $did_posts = [];
				static $level = 0;
				// Avoid recursion
				if ( ! isset( $did_posts[ $item->block ] ) ) {

					$level++;
					$did_posts[ $item->block ] = true;
					// End avoid recursion

					$item_output .= '<div class="menu-block menu-block-after">' . do_shortcode( '[porto_block name="' . $item->block . '"]' ) . '</div>';

					$level--;

					if ( 0 === $level ) {
						$did_posts = [];
					}
				}
				
			}
			$item_output      .= $args->after;
			$args->popup_style = $popup_style;

			if ( 0 == $depth && $args->has_children && 'view_switcher' != $args->theme_location && 'currency_switcher' != $args->theme_location && 'top_nav' != $args->theme_location && 'account_menu' != $args->theme_location ) {
				global $porto_settings_optimize;
				if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) {
					$cols_html = '';
					if ( 'wide' == $item->popup_type ) {
						$cols_html .= str_repeat( '<li></li>', $item->popup_cols ? intval( str_replace( 'col-', '', $item->popup_cols ) ) : 4 );
					}
					$popup_cols   = ' ' . $item->popup_cols;
					$item_output .= '<div class="popup"><div class="inner" style="' . esc_attr( $args->popup_style ) . '"><ul class="sub-menu skeleton-body">' . $cols_html . '</ul></div></div>';
				}
			}

			// build html
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}

/* Sidebar Menu */
if ( ! class_exists( 'porto_sidebar_navwalker' ) ) {
	class porto_sidebar_navwalker extends Walker_Nav_Menu {

		// add classes to ul sub menus
		function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			if ( empty( $depth ) ) {
				$depth = 0;
			}
			$id_field = $this->db_fields['id'];
			if ( is_object( $args[0] ) ) {
				$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
			}
			if ( apply_filters( 'porto_lazymenu_depth', false ) ) {
				$max_depth = 1;
			}
			return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		// add popup class to ul sub-menus
		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$out_div = '';
			if ( 0 == $depth ) {
				$out_div = '<div class="popup"' . ( empty( $args->popup_max_width ) ? '' : ' data-popup-mw="' . intval( $args->popup_max_width ) . '"' ) . '><div class="inner" style="' . esc_attr( $args->popup_style ) . '">';
			} else {
				$out_div = '';
			}
			$extra_cls = '';
			if ( 0 == $depth ) {
				if ( isset( $args->menu_popup_type ) && 'wide' == $args->menu_popup_type ) {
					$extra_cls .= ' porto-wide-sub-menu';
				} else {
					$extra_cls .= ' porto-narrow-sub-menu';
				}
			}
			$output .= "\n$indent$out_div<ul class=\"sub-menu" . $extra_cls . "\">\n";
		}

		function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );
			if ( 0 == $depth ) {
				$out_div = '</div></div>';
			} else {
				$out_div = '';
			}
			$output .= "$indent</ul>$out_div\n";
		}

		// add main/sub classes to li's and links
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $wp_query;

			$sub    = '';
			$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
			if ( 0 == $depth && $args->has_children ) {
				$sub = ' has-sub';
			}

			if ( 1 == $depth && $args->has_children ) {
				$sub = ' sub';
			}

			$active = '';

			// depth dependent classes
			if ( $item->current || $item->current_item_ancestor || $item->current_item_parent || in_array( 'current_page_item', (array) $item->classes ) ) {
				$active = 'active';
			}

			// passed classes
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;

			/*if ( in_array( 'current-page-ancestor', $classes ) || in_array( 'current_page_item', $classes ) ) {
				$active = 'active';
			}*/

			$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

			// menu type, type, column class, popup style
			$menu_type   = '';
			$popup_pos   = '';
			$popup_cols  = '';
			$popup_style = '';
			$cols        = 1;

			if ( 0 == $depth ) {
				unset( $args->popup_max_width, $args->menu_popup_type );
				if ( 'wide' == $item->popup_type ) {
					$menu_type = ' wide';
					if ( '' == $item->popup_cols ) {
						$item->popup_cols = 'col-4';
					}
					$popup_cols = ' ' . $item->popup_cols;

					$popup_bg_image  = $item->popup_bg_image ? 'background-image:url(' . str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $item->popup_bg_image ) . ');' : '';
					$popup_bg_pos    = $item->popup_bg_pos ? 'background-position:' . $item->popup_bg_pos . ';' : '';
					$popup_bg_repeat = $item->popup_bg_repeat ? 'background-repeat:' . $item->popup_bg_repeat . ';' : '';
					$popup_bg_size   = $item->popup_bg_size ? 'background-size:' . $item->popup_bg_size . ';' : '';
					$popup_max_width = ( $item->popup_max_width && ( ! isset( $item->popup_pos ) || isset( $item->popup_pos ) && 'pos-fullwidth' != $item->popup_pos ) ) ? 'max-width:' . (int) $item->popup_max_width . 'px;' : '';

					$popup_style = str_replace( '"', '\'', $item->popup_style . $popup_bg_image . $popup_bg_pos . $popup_bg_repeat . $popup_bg_size . $popup_max_width );
					if ( $item->popup_max_width ) {
						$args->popup_max_width = $item->popup_max_width;
					}
					$args->menu_popup_type = 'wide';
				} else {
					$menu_type = ' narrow';
				}
				$popup_pos = ' ' . $item->popup_pos;
			}

			if ( $item->mobile_hide && ! $args->has_children && ( empty( $item->block ) || ! empty( $item->popup_mobile_hide ) ) ) {
				$class_names .= ' hidden-item';
			}

			// build html
			if ( 1 == $depth ) {
				$sub_popup_style = '';
				if ( $item->popup_style || $item->popup_bg_image || $item->popup_bg_pos || $item->popup_bg_repeat || $item->popup_bg_size ) {
					$sub_popup_image  = $item->popup_bg_image ? 'background-image:url(' . str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $item->popup_bg_image ) . ');' : '';
					$sub_popup_pos    = $item->popup_bg_pos ? 'background-position:' . $item->popup_bg_pos . ';' : '';
					$sub_popup_repeat = $item->popup_bg_repeat ? 'background-repeat:' . $item->popup_bg_repeat . ';' : '';
					$sub_popup_size   = $item->popup_bg_size ? 'background-size:' . $item->popup_bg_size . ';' : '';
					$sub_popup_style  = ' style="' . esc_attr( str_replace( '"', '\'', $item->popup_style ) . $sub_popup_image . $sub_popup_pos . $sub_popup_repeat . $sub_popup_size ) . '"';
				}
				$cols = (float) $item->cols;
				if ( $cols <= 0 ) {
					$cols = 1;
				}
				if ( $item->block ) {
					$class_names .= ' menu-block-item ';
				}
				$output .= $indent . '<li id="nav-menu-item-' . $item->ID . '" class="' . $class_names . ' ' . $active . $sub . $menu_type . $popup_pos . $popup_cols . '" data-cols="' . $cols . '"' . $sub_popup_style . '>';
			} else {
				$output .= $indent . '<li id="nav-menu-item-' . $item->ID . '" class="' . $class_names . ' ' . $active . $sub . $menu_type . $popup_pos . $popup_cols . '">';
			}

			$current_a = '';

			// link attributes
			$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_url( $item->url ) . '"' : '';

			if ( ( $item->current && 0 == $depth ) || ( $item->current_item_ancestor && 0 == $depth ) ) {
				$current_a .= ' current';
			}

			if ( $preview = $item->preview ) {
				$current_a .= ' has-preview';
			}

			$link_class = '';
			if ( isset( $item->mobile_hide ) && '' !== $item->mobile_hide ) {
				$link_class = ' mobile-link-hide';
			}
			if ( isset( $item->hide ) && '' !== $item->hide ) {
				$link_class = ' desktop-link-hide';
			}
			if ( '' == $item->nolink ) {
				$current_a .= $link_class;
			}
			$attributes .= $current_a ? ' class="' . $current_a . '"' : '';
			$item_output = $args->before;
			if ( '' == $item->hide || '' == $item->mobile_hide ) {
				if ( empty( $item->nolink ) ) {
					$item_output .= '<a' . $attributes . '>';
				} else {
					$item_output .= '<a class="nolink' . $link_class . '" href="#">';
				}
				if ( $item->icon ) {
					$item->icon   = trim( $item->icon );
				}
				if ( isset( $item->icon_type ) && 'svg' == $item->icon_type ) {
					$item_output .= $args->link_before;

					if ( ! empty( $item->icon_svg ) ) {
						$svg_id = attachment_url_to_postid( $item->icon_svg );
	
						if ( $svg_id ) {
							$svg_file = get_attached_file( $svg_id );
						}
	
						if ( isset( $svg_file ) && file_exists( $svg_file ) ) {
							if ( defined( 'ELEMENTOR_VERSION' ) ) {
								$svg       = Elementor\Utils::file_get_contents( $svg_file );
								$valid_svg = ( new Elementor\Core\Utils\Svg\SVG_Sanitizer() )->sanitize( $svg );
							} else { // attackable
								$valid_svg = file_get_contents( $svg_file );
							}
						}
						
						if ( isset( $valid_svg ) ) {
							$item_output .= $valid_svg;
						}
					}

					$item_output .= apply_filters( 'the_title', $item->title, $item->ID );
				} elseif ( isset( $item->icon_type ) && 'img' == $item->icon_type ) {
					$item_output .= $args->link_before;
					if ( ! empty( $item->icon_img ) ) {
						$sz_wd        = ! empty( $item->icon_img_wd ) ? (int) $item->icon_img_wd : 20;
						$item_output .= wp_get_attachment_image( (int) $item->icon_img, array( $sz_wd, 0 ) );
					}
					$item_output .= apply_filters( 'the_title', $item->title, $item->ID );
				} else {
					$item_output .= $args->link_before . ( $item->icon ? '<i class="' . esc_attr( 0 === strpos( $item->icon, 'fa-' ) ? 'fa ' . $item->icon : $item->icon ) . '"></i>' : '' ) . apply_filters( 'the_title', $item->title, $item->ID );
				}
				$item_output .= $args->link_after;
				if ( $item->tip_label ) {
					$item_style = '';
					if ( $item->tip_color ) {
						$item_style .= 'color:' . $item->tip_color . ';';
					}
					if ( $item->tip_bg ) {
						$item_style .= 'background:' . $item->tip_bg . ';';
						$item_style .= 'border-color:' . $item->tip_bg . ';';
					}
					$item_output .= '<span class="tip" style="' . esc_attr( $item_style ) . '">' . esc_html( $item->tip_label ) . '</span>';
				}

				// preview image
				$preview = $item->preview;
				if ( $preview ) {
					porto_enqueue_link_style( 'porto-thumb-info', PORTO_CSS . '/part/thumb-info' . ( is_rtl() ? '_rtl': '' ) . '.css' );
					porto_enqueue_link_style( 'porto-menu-thumb-info', PORTO_CSS . '/part/menu-thumb-info' . ( is_rtl() ? '_rtl': '' ) . '.css' );
					$item_output .= '<span class="thumb-info thumb-info-preview"><span class="thumb-info-wrapper"><span class="thumb-info-image' . ( $item->preview_fixed ? ' fixed-image' : '' ) . '" style="background-image: url(' . str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $preview ) . ');"></span></span></span>';
				}

				$item_output .= '</a>';
			}
			if ( $item->block ) {
				$item_output .= '<div class="menu-block menu-block-after' . ( ( ! isset( $item->popup_mobile_hide ) || 'hide' != $item->popup_mobile_hide ) ? '': ' mobile-hide' ) . '">' . do_shortcode( '[porto_block name="' . $item->block . '"]' ) . '</div>';
			}
			$item_output      .= $args->after;
			$args->popup_style = $popup_style;

			if ( 0 == $depth && $args->has_children ) {
				$item_output .= '<span class="arrow" aria-label="Open Submenu"></span>';
			}
			global $porto_settings;
			if ( 0 < $depth && $args->has_children && isset( $porto_settings['side-menu-type'] ) && 'accordion' == $porto_settings['side-menu-type'] ) {
				$item_output .= '<span class="arrow" aria-label="Open Submenu"></span>';
			}

			if ( 0 == $depth && $args->has_children ) {
				global $porto_settings_optimize;
				if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) {
					$cols_html = '';
					if ( 'wide' == $item->popup_type ) {
						$cols_html .= str_repeat( '<li></li>', $item->popup_cols ? intval( str_replace( 'col-', '', $item->popup_cols ) ) : 4 );
					}
					$popup_cols   = ' ' . $item->popup_cols;
					$item_output .= '<div class="popup"><div class="inner" style="' . esc_attr( $args->popup_style ) . '"><ul class="sub-menu skeleton-body">' . $cols_html . '</ul></div></div>';
				}
			}

			// build html
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}

/* Accordion Menu */
if ( ! class_exists( 'porto_accordion_navwalker' ) ) {
	class porto_accordion_navwalker extends Walker_Nav_Menu {

		private $has_active = false;

		// add classes to ul sub menus
		function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			if ( empty( $depth ) ) {
				$depth = 0;
			}
			$id_field = $this->db_fields['id'];
			if ( is_object( $args[0] ) ) {
				$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
			}
			if ( apply_filters( 'porto_lazymenu_depth', false ) ) {
				$max_depth = 1;
			}
			return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		// add main/sub classes to li's and links
		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "\n$indent<span aria-label=\"Open Submenu\" class=\"arrow\" role=\"button\"></span><ul class=\"sub-menu\">\n";
		}

		function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
		}

		// add main/sub classes to li's and links
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

			global $wp_query, $porto_settings;

			$sub    = '';
			$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
			if ( ( $depth >= 0 && $args->has_children ) || ( $depth >= 0 && '' != $item->recentpost ) ) {
				$sub = ' has-sub';
			}

			$active = '';

			if ( $item->current || $item->current_item_ancestor || $item->current_item_parent || in_array( 'current_page_item', (array) $item->classes ) ) {
				if ( 0 == $depth ) {
					if ( ! $this->has_active ) {
						$active           = ' active';
						$this->has_active = true;
					}
				} else {
					$active = ' active';
				}
			}

			// passed classes
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;

			/*if ( in_array( 'current-page-ancestor', $classes ) || in_array( 'current_page_item', $classes ) ) {
				$active = ' active';
			}*/

			$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

			if ( $item->mobile_hide && ( empty( $item->block ) || ! empty( $item->popup_mobile_hide ) ) ) {
				$class_names .= ' hidden-item';
			}

			// build html
			$output .= $indent . '<li id="accordion-menu-item-' . esc_attr( $item->ID ) . '" class="' . $class_names . $active . $sub . '">';

			$current_a = '';

			// link attributes
			$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_url( $item->url ) . '"' : '';

			if ( isset( $porto_settings['mobile-menu-item-nofollow'] ) && $porto_settings['mobile-menu-item-nofollow'] ) {
				$attributes .= ' rel="nofollow"';
			}

			if ( ( $item->current && 0 == $depth ) || ( $item->current_item_ancestor && 0 == $depth ) ) {
				$current_a .= ' current ';
			}
			$link_class = '';
			if ( isset( $item->mobile_hide ) && '' !== $item->mobile_hide ) {
				$link_class = ' mobile-link-hide';
			}
			if ( isset( $item->hide ) && '' !== $item->hide ) {
				$link_class = ' desktop-link-hide';
			}
			if ( '' == $item->nolink ) {
				$current_a .= $link_class;
			}
			$attributes .= $current_a ? ' class="' . $current_a . '"' : '';
			$item_output = $args->before;

			if ( '' == $item->hide || '' == $item->mobile_hide ) {
				if ( empty( $item->nolink ) ) {
					$item_output .= '<a' . $attributes . '>';
				} else {
					$item_output .= '<a class="nolink' . $link_class . '" href="#">';
				}
				if ( $item->icon ) {
					$item->icon   = trim( $item->icon );
				}
				if ( isset( $item->icon_type ) && 'svg' == $item->icon_type ) {
					$item_output .= $args->link_before;

					if ( ! empty( $item->icon_svg ) ) {
						$svg_id = attachment_url_to_postid( $item->icon_svg );

						if ( $svg_id ) {
							$svg_file = get_attached_file( $svg_id );
						}

						if ( isset( $svg_file ) && file_exists( $svg_file ) ) {
							if ( defined( 'ELEMENTOR_VERSION' ) ) {
								$svg       = Elementor\Utils::file_get_contents( $svg_file );
								$valid_svg = ( new Elementor\Core\Utils\Svg\SVG_Sanitizer() )->sanitize( $svg );
							} else { // attackable
								$valid_svg = file_get_contents( $svg_file );
							}
						}

						if ( isset( $valid_svg ) ) {
							$item_output .= $valid_svg;
						}
					}

					$item_output .= apply_filters( 'the_title', $item->title, $item->ID );
				} elseif ( isset( $item->icon_type ) && 'img' == $item->icon_type ) {
					$item_output .= $args->link_before;
					if ( ! empty( $item->icon_img ) ) {
						$sz_wd        = ! empty( $item->icon_img_wd ) ? (int) $item->icon_img_wd : 20;
						$item_output .= wp_get_attachment_image( (int) $item->icon_img, array( $sz_wd, 0 ) );
					}
					$item_output .= apply_filters( 'the_title', $item->title, $item->ID );
				} else {
					$item_output .= $args->link_before . ( $item->icon ? '<i class="' . esc_attr( 0 === strpos( $item->icon, 'fa-' ) ? 'fa ' . $item->icon : $item->icon ) . '"></i>' : '' ) . apply_filters( 'the_title', $item->title, $item->ID );
				}
				$item_output .= $args->link_after;
				if ( $item->tip_label ) {
					$item_style = '';
					if ( $item->tip_color ) {
						$item_style .= 'color:' . $item->tip_color . ';';
					}
					if ( $item->tip_bg ) {
						$item_style .= 'background:' . $item->tip_bg . ';';
						$item_style .= 'border-color:' . $item->tip_bg . ';';
					}
					$item_output .= '<span class="tip" style="' . esc_attr( $item_style ) . '">' . esc_html( $item->tip_label ) . '</span>';
				}

				$item_output .= '</a>';
			}
			if ( ! empty( $item->block ) && ( ! isset( $item->popup_mobile_hide ) || 'hide' != $item->popup_mobile_hide ) ) {
				$item_output .= '<div class="menu-block menu-block-after">' . do_shortcode( '[porto_block name="' . $item->block . '"]' ) . '</div>';
			}
			$item_output .= $args->after;
			// build html
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}

// Menu Ajax Loading
if ( ! function_exists( 'porto_action_lazyload_menu' ) ) :
	function porto_action_lazyload_menu() {
		if ( isset( $_POST['action'] ) && 'porto_lazyload_menu' == $_POST['action'] && isset( $_POST['menu_type'] ) /*&& wp_verify_nonce( $_POST['nonce'], 'porto-nonce' )*/ ) {
			// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
			global $porto_settings_optimize, $porto_layout;
			echo '<div class="menu-lazyload">';
			$porto_settings_optimize['lazyload_menu'] = '';

			$porto_layout_arr = porto_meta_layout();
			if ( $porto_layout_arr && is_array( $porto_layout_arr ) ) {
				$porto_layout = $porto_layout_arr[0];
			}

			if ( 'sidebar_menu' == $_POST['menu_type'] ) {
				if ( ! empty( $_POST['menu_id'] ) ) {
					if ( $template = porto_shortcode_template( 'porto_sidebar_menu' ) ) {
						$atts = array( 'nav_menu' => sanitize_text_field( $_POST['menu_id'] ) );
						include $template;
					}
				} else {
					echo porto_sidebar_menu(); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
				}
			} elseif ( 'secondary_menu' == $_POST['menu_type'] ) {
				// echo porto_main_menu(); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
				echo porto_secondary_menu(); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			} elseif ( 'main_menu' == $_POST['menu_type'] ) {
				echo porto_main_menu(); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			} elseif ( 'header_side_menu' == $_POST['menu_type'] ) {
				echo porto_header_side_menu();
			} elseif ( 'toggle_menu' == $_POST['menu_type'] ) {
				echo porto_main_toggle_menu();
			} elseif ( 'mobile_menu' == $_POST['menu_type'] ) {
				global $porto_settings;
				get_template_part( 'header/mobile_menu' );
				if ( isset( $porto_settings['mobile-panel-type'] ) && 'side' === $porto_settings['mobile-panel-type'] ) {
					get_template_part( 'panel' );
				}
			}
			echo '</div>';
			// phpcs: enable
			exit;
		}
	}
endif;
