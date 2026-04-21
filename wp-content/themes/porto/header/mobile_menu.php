<?php
global $porto_settings, $porto_settings_optimize;

if ( ! empty( $porto_settings['mobile-panel-type'] ) ) {
	return;
}

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

$is_preset = porto_header_type_is_preset();

$is_load_menu = ( ( isset( $_POST['action'] ) && 'porto_lazyload_menu' == $_POST['action'] ) || empty( $porto_settings_optimize['lazyload_menu'] ) );

$extra_cls = '';
if ( ! $is_load_menu ) {
	$extra_cls .= ' skeleton-body';
}
?>

<nav id="nav-panel">
	<div class="container">
		<div class="mobile-nav-wrap<?php echo esc_attr( $extra_cls ); ?>">
		<?php
		if ( $is_load_menu ) {

			// show top navigation and mobile menu
			$menu = porto_mobile_menu( '19' == $header_type || empty( $header_type ) );
			$empty_menu = true;
			if ( $menu ) {
				echo '<div class="menu-wrap">' . $menu . '</div>';
				$empty_menu = false;
			}

			if ( ( ! $is_preset || 1 == $header_type || 3 == $header_type || 4 == $header_type || 9 == $header_type || 13 == $header_type || 14 == $header_type ) && ! empty( $porto_settings['menu-block'] ) ) {
				echo '<div class="menu-custom-block">' . wp_kses_post( $porto_settings['menu-block'] ) . '</div>';
			}

			$menu = porto_mobile_top_navigation( true );

			if ( $menu ) {
				echo '<div class="menu-wrap">' . $menu . '</div>';
				$empty_menu = false;
			}

			if ( '7' == $header_type || '8' == $header_type || ! empty( $porto_settings['mobile-panel-add-switcher'] ) ) {
				// show currency and view switcher
				$switcher  = '';
				$switcher .= porto_mobile_currency_switcher( true );
				$switcher .= porto_mobile_view_switcher( true );

				if ( $switcher ) {
					echo '<div class="menu-wrap">' . $switcher . '</div>';
					$empty_menu = false;
				}
			}
			if ( $empty_menu ) {
				echo '<div class="menu-empty">';
				echo sprintf( esc_html__( 'Check the Main Menu location in %1$sApppearance->Menus->Display Location%2$s.', 'porto' ),  '<a href="' .  esc_url( admin_url( 'nav-menus.php' ) ) . '" target="_blank">', '</a>' );
				echo '</div>';
			}
		} else {
			echo '<i class="porto-loading-icon"></i>';
		}
		?>
		</div>
	</div>
</nav>
