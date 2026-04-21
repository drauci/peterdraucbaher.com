<?php
global $porto_settings, $porto_settings_optimize;
$header_type = porto_get_header_type();

if ( 'overlay' == $porto_settings['menu-type'] ) {
	if ( empty( $header_type ) ) {
		global $porto_menu_wrap;
		if ( empty( $porto_menu_wrap ) ) {
			return;
		}
	} elseif ( ! in_array( (int) $header_type, array( 1, 4, 9, 13, 14, 17 ) ) ) {
		return;
	}
}
?>
<div class="panel-overlay" <?php echo apply_filters( 'porto_panel_overlay_data_attrs', '' ); ?>></div>
<a href="#" role="button" aria-label="<?php esc_attr_e( 'Close the Mobile Menu', 'porto' ); ?>" class="side-nav-panel-close"><svg width="1.5rem" height="1.5rem" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g id="cross"><line stroke="#fff" stroke-width="2px" x1="7" x2="25" y1="7" y2="25"/><line stroke="#fff" stroke-width="2px" x1="7" x2="25" y1="25" y2="7"/></g></svg></a>
<div id="side-nav-panel" class="<?php echo ( isset( $porto_settings['mobile-panel-pos'] ) && $porto_settings['mobile-panel-pos'] ) ? $porto_settings['mobile-panel-pos'] : ''; ?>">

<?php if ( ( isset( $_POST['action'] ) && 'porto_lazyload_menu' == $_POST['action'] ) || empty( $porto_settings_optimize['lazyload_menu'] ) ) : ?>
	<?php
	if ( isset( $porto_settings['mobile-panel-add-search'] ) && $porto_settings['mobile-panel-add-search'] ) {
		echo porto_search_form_content( true );
	}
	$empty_menu = true;
	$is_option = isset( $porto_settings['show-mobile-menus'] ) ? ( is_array( $porto_settings['show-mobile-menus'] ) ? true : 'empty' ) : false;
	$menu_is_tab = ( ! empty( $porto_settings['show-mobile-menus'] ) && sizeof( $porto_settings['show-mobile-menus'] ) >= 2 ) ? sizeof( $porto_settings['show-mobile-menus'] ) : false;

	// show top navigation and mobile menu
	$menu = porto_mobile_menu( '19' == $header_type || empty( $header_type ), 'panel' );

	if ( $menu_is_tab ) {
		echo '<div class="mobile-tabs"><ul role="tablist" class="mobile-tab-items nav nav-fill nav-tabs">';
		for ( $i = 0; $i < $menu_is_tab; $i++ ) {
			$id = 'menu-' . $porto_settings['show-mobile-menus'][$i];
			$title = ! empty( $porto_settings[ $id ] ) ? $porto_settings[ $id ] : __( 'Mobile Menu', 'porto' );
			$id = 'menu-side' == $id ? 'menu-sidebar' : $id;
			echo '<li role="presentation" class="mobile-tab-item nav-item' . ( 0 == $i ? ' active' : '' ) . '" pane-id="' . $id . '"><a role="tab" href="#" rel="nofollow noopener">' . $title . '</a></li>' ;
		}
		echo '</ul>';
		echo '<div class="mobile-tab-content">';
	}

	if ( $menu ) {
		if ( $menu_is_tab ) {
			echo porto_filter_output( $menu );
		} else {
			echo '<div class="menu-wrap">' . $menu . '</div>';
		}
		$empty_menu = false;
	}

	if ( $empty_menu ) {
		echo '<div class="menu-empty text-body">';
		echo sprintf( esc_html__( 'Check the Main Menu location in %1$sApppearance->Menus->Display Location%2$s.', 'porto' ),  '<a href="' .  esc_url( admin_url( 'nav-menus.php' ) ) . '" target="_blank">', '</a>' );
		echo '</div>';
	}

	if ( $menu_is_tab ) {
		echo '</div></div>';
	}

	if ( isset( $porto_settings['mobile-panel-add-switcher'] ) && $porto_settings['mobile-panel-add-switcher'] ) {
		// show currency and view switcher
		$switcher  = '';
		$switcher .= porto_mobile_currency_switcher();
		$switcher .= porto_mobile_view_switcher();

		if ( $switcher ) {
			echo '<div class="switcher-wrap">' . $switcher . '</div>';
		}
	}
	
	if ( ( ! porto_header_type_is_preset() || 1 == $header_type || 3 == $header_type || 4 == $header_type || 9 == $header_type || 13 == $header_type || 14 == $header_type ) && ! empty( $porto_settings['menu-block'] ) ) {
		echo '<div class="menu-custom-block">' . wp_kses_post( $porto_settings['menu-block'] ) . '</div>';
	}


	// show social links
	echo porto_header_socials();
	?>
<?php else : ?>
	<div class="skeleton-body porto-ajax-loading"><i class="porto-loading-icon"></i></div>
<?php endif; ?>
</div>
