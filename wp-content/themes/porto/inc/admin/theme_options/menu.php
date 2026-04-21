<?php

if ( $this->legacy_mode ) {
	// Menu Settings
	$this->sections[] = $this->add_customizer_field(
		array(
			'icon'       => 'Simple-Line-Icons-menu',
			'icon_class' => '',
			'title'      => __( 'Menu', 'porto' ),
		),
		$options_style
	);
	$this->sections[] = array(
		'transport'  => 'postMessage',
		'subsection' => true,
		'title'      => __( 'Menu', 'porto' ),
		'fields'     => array(
			array(
				'id'    => 'desc_info_menu_notice',
				'type'  => 'info',
				'desc'  => wp_kses(
					/* translators: %s: Builder url */
					sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Menu widget.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $header_url ),
					array(
						'strong' => array(),
						'b'      => array(),
						'a'      => array(
							'href'   => array(),
							'target' => array(),
							'class'  => array(),
						),
						'i'     => array(
							'class'  => array(),
						),
						'span'  => array(),
						'br'    => array(),
					)
				),
				'class' => 'porto-important-note',
			),
			array(
				'id'      => 'menu-type',
				'type'    => 'image_select',
				'title'   => __( 'Main Menu Type', 'porto' ),
				'options' => array(
					''                           => array( 
						'title' => __( 'Normal', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-normal.svg',
					),
					'menu-flat'                  => array(
						'title' => __( 'Flat', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-flat.svg',
					),
					'menu-flat menu-flat-border' => array(
						'title' => __( 'Flat & Border', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-flat-border.svg',
					),
					'menu-hover-line'            => array(
						'title' => __( 'Top Border on hover', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-hover-line.gif',
					),
					'menu-hover-line menu-hover-underline' => array(
						'title' => __( 'Thick Underline on hover', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-hover-underline.gif',
					),
					'overlay'                    => array(
						'title' => __( 'Popup', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-overlay.svg',
					),
				),
				'class'   => 'pt-always-visible',
				'default' => '',
			),
			array(
				'id'      => 'desc_info_menu_warn',
				'type'    => 'info',
				'desc'    => wp_kses(
					sprintf( 
						__( 'You can change the skin of menu item in <a href="%1$s">Menu > Menu Styling > Link Color</a> or Menu Widget of <a href="%2$s">Header Builder</a>.', 'porto' ), 
						porto_get_theme_option_url( 'mainmenu-toplevel-link-color' ), 
						$header_url 
					),
					array(
						'a' => array(
							'href'  => array(),
							'title' => array(),
							'class' => array(),
						),
					)
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),				
			array(
				'id'       => 'menu-arrow',
				'type'     => 'switch',
				'title'    => __( 'Show Menu Arrow', 'porto' ),
				'subtitle'     => __( 'If menu item has children, show arrow in first-level menu item.', 'porto' ),
				'default'  => false,
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
				'required' => array( 'menu-type', 'equals', array( '', 'menu-flat', 'menu-flat menu-flat-border', 'menu-hover-line', 'menu-hover-line menu-hover-underline' ) ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-arrow.gif"/>' ),
				),
			),
			array(
				'id'       => 'submenu-arrow',
				'type'     => 'switch',
				'title'    => __( 'Show triangle in dropdown', 'porto' ),
				'subtitle'     => __( 'Show triangle arrow to the menu popup.', 'porto' ),
				'default'  => false,
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
				'required' => array( 'menu-type', 'equals', array( '', 'menu-flat', 'menu-flat menu-flat-border', 'menu-hover-line', 'menu-hover-line menu-hover-underline' ) ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'submenu-arrow.gif"/>' ),
				),
			),
			array(
				'id'      => 'menu-align',
				'type'    => 'button_set',
				'title'   => __( 'Main Menu Align', 'porto' ),
				'options' => array(
					''         => __( 'Left', 'porto' ),
					'centered' => __( 'Center', 'porto' ),
				),
				'default'  => '',
				'required' => array(
					array( 'header-type-select', 'equals', '' ),
					array( 'header-type', 'equals', array( '1', '4', '13', '14', '17' ) ),
				),
			),				
			array(
				'id'     => 'desc_info_side_header',
				'type'   => 'info',
				'title'  => wp_kses(
					__( 'When using <span>Side Header</span> or showing Main Menu in <span>Sidebar</span>', 'porto' ),
					array(
						'span' => array(),
					)
				),
				'required'  => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),
			array(
				'id'        => 'menu-sidebar',
				'type'      => 'switch',
				'title'     => __( 'Show Main Menu in Sidebar', 'porto' ),
				'subtitle'  => __( 'If the layout of a page is left sidebar or right sidebar, the main menu shows in the sidebar.', 'porto' ),
				'default'   => false,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'refresh',
			),	
			array(
				'id'       => 'menu-sidebar-title',
				'type'     => 'text',
				'title'    => __( 'Sidebar Menu Title', 'porto' ),
				'subtitle' => __( 'Input the title of sidebar menu.', 'porto' ),
				'default'  => __( 'All Department', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-sidebar-title.jpg"/>' ),
				),
			),
			array(
				'id'        => 'side-menu-type',
				'type'      => 'image_select',
				'title'     => __( 'Menu Type of Side Header or Sidebar', 'porto' ),
				'subtitle'      => __( 'Controls how to show its submenus.', 'porto' ),
				'options'   => array(
					''          => array(
						'title' => __( 'Normal', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sidemenu-normal.svg',
					),
					'accordion' => array(
						'title' => __( 'Accordion Menu', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sidemenu-accordion.svg',
					),
					'slide'     => array(
						'title' => __( 'Horizontal Slide Menu', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sidemenu-slide.svg',
					),
					'columns'   => array(
						'title' => __( 'Horizontal Columns', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sidemenu-columns.svg',
					),
				),
				'default'   => '',
				'transport' => 'refresh',
			),
			array(
				'id'       => 'menu-sidebar-toggle',
				'type'     => 'switch',
				'title'    => __( 'Toggle Sidebar Menu', 'porto' ),
				'subtitle' => __( 'Add a toggle button of the sidebar menu.', 'porto' ),
				'required' => array( 'menu-sidebar', 'equals', true ),
				'default'  => false,
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-sidebar-toggle.gif"/>' ),
				),
			),
			array(
				'id'        => 'menu-sidebar-home',
				'type'      => 'switch',
				'title'     => __( 'Show Main Menu in only the homepage sidebar', 'porto' ),
				'subtitle'  => __( 'You can see sidebar menu only on homepage.', 'porto' ),
				'required'  => array( 'menu-sidebar', 'equals', true ),
				'default'   => true,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'refresh',
				'hint'      => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-sidebar-home.jpg"/>' ),
				),
			),
			array(
				'id'     => 'desc_info_header_preset2',
				'type'   => 'info',
				'title'  => wp_kses( 
					__( '<a href="https://www.portotheme.com/wordpress/porto/shop35-soft/" target="blank">Toggle Menu like Shop 35</a>: If header type is 9 or header builder', 'porto' ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						)
					)
				),
				'notice' => false,
			),
			array(
				'id'       => 'menu-title',
				'type'     => 'text',
				'title'    => wp_kses( 
					__( 'Title of Toggle Menu like <a href="https://www.portotheme.com/wordpress/porto/shop35-soft/" target="blank">Shop 35</a>', 'porto' ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						)
					)
				),
				'subtitle' => __( 'Please change the title of Toggle menu.', 'porto' ),
				'default'  => __( 'All Department', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-title.gif"/>' ),
				),
			),
			array(
				'id'        => 'menu-toggle-onhome',
				'type'      => 'switch',
				'title'     => __( 'Collapse the Toggle Menu on home page', 'porto' ),
				'subtitle'  => __( 'In homepage, a toggle menu is collapsed at first. Then it works as a toggle....', 'porto' ),
				'default'   => false,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'refresh',
				'hint'      => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-toggle-onhome.gif"/>' ),
				),
				'required'  => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'     => 'desc_info_header_preset3',
				'type'   => 'info',
				'title'  => __( 'If header type is 1, 3, 4, 9, 13, 14, 19 or header builder', 'porto' ),
				'notice' => false,
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'menu-block',
				'type'     => 'textarea',
				'title'    => __( 'Menu Custom Content', 'porto' ),
				'subtitle' => __( 'example: &lt;span&gt;Custom Message&lt;/span&gt;&lt;a href="#"&gt;Special Offer!&lt;/a&gt;&lt;a href="#"&gt;Buy this Theme!&lt;em class="tip hot"&gt;HOT&lt;/em&gt;&lt;/a&gt;', 'porto' ),
				'default'  => '',
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-block.gif"/>' ),
				),
			),
		),
	);	
} else {
	$this->sections[] = $this->add_customizer_field(
		array(
			'icon'       => 'Simple-Line-Icons-menu',
			'icon_class' => '',
			'title'      => __( 'Menu', 'porto' ),
		),
		$options_style
	);
	$this->sections[] = array(
		'transport'  => 'postMessage',
		'subsection' => true,
		'title'      => __( 'Menu', 'porto' ),
		'fields'     => array(
			array(
				'id'    => 'desc_info_menu_notice',
				'type'  => 'info',
				'desc'  => wp_kses(
					/* translators: %s: Builder url */
					sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Menu widget.', 'porto' ), $header_url ),
					array(
						'strong' => array(),
						'b'      => array(),
						'a'      => array(
							'href'   => array(),
							'target' => array(),
							'class'  => array(),
						),
					)
				),
				'class' => 'porto-important-note',
			),
			array(
				'id'      => 'menu-type',
				'type'    => 'image_select',
				'title'   => __( 'Main Menu Type', 'porto' ),
				'options' => array(
					''                           => array( 
						'title' => __( 'Normal', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-normal.svg',
					),
					'menu-flat'                  => array(
						'title' => __( 'Flat', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-flat.svg',
					),
					'menu-flat menu-flat-border' => array(
						'title' => __( 'Flat & Border', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-flat-border.svg',
					),
					'menu-hover-line'            => array(
						'title' => __( 'Top Border on hover', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-hover-line.gif',
					),
					'menu-hover-line menu-hover-underline' => array(
						'title' => __( 'Thick Underline on hover', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-hover-underline.gif',
					),
					'overlay'                    => array(
						'title' => __( 'Popup', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/menu-overlay.svg',
					),
				),
				'default' => '',
			),
			array(
				'id'      => 'desc_info_menu_warn',
				'type'    => 'info',
				'desc'    => wp_kses(
					sprintf( 
						__( 'You can change the skin of menu item in <a href="%1$s">Menu > Menu Styling > Link Color</a> or Menu Widget of <a href="%2$s">Header Builder</a>.', 'porto' ), 
						porto_get_theme_option_url( 'mainmenu-toplevel-link-color' ), 
						$header_url 
					),
					array(
						'a' => array(
							'href'  => array(),
							'title' => array(),
							'class' => array(),
						),
					)
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),					
			array(
				'id'       => 'menu-arrow',
				'type'     => 'switch',
				'title'    => __( 'Show Menu Arrow', 'porto' ),
				'subtitle'     => __( 'If menu item has children, show arrow in first-level menu item.', 'porto' ),
				'default'  => false,
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
				'required' => array( 'menu-type', 'equals', array( '', 'menu-flat', 'menu-flat menu-flat-border', 'menu-hover-line', 'menu-hover-line menu-hover-underline' ) ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-arrow.gif"/>' ),
				),
			),
			array(
				'id'       => 'submenu-arrow',
				'type'     => 'switch',
				'title'    => __( 'Show triangle in dropdown', 'porto' ),
				'subtitle'     => __( 'Show triangle arrow to the menu popup.', 'porto' ),
				'default'  => false,
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
				'required' => array( 'menu-type', 'equals', array( '', 'menu-flat', 'menu-flat menu-flat-border', 'menu-hover-line', 'menu-hover-line menu-hover-underline' ) ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'submenu-arrow.gif"/>' ),
				),
			),
			array(
				'id'     => 'desc_info_side_header',
				'type'   => 'info',
				'title'  => wp_kses(
					__( 'When using <span>Side Header</span> or showing Main Menu in <span>Sidebar</span>', 'porto' ),
					array(
						'span' => array(),
					)
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),
			array(
				'id'        => 'menu-sidebar',
				'type'      => 'switch',
				'title'     => __( 'Show Main Menu in Sidebar', 'porto' ),
				'subtitle'      => __( 'If the layout of a page is left sidebar or right sidebar, the main menu shows in the sidebar.', 'porto' ),
				'default'   => false,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'refresh',
			),
			array(
				'id'       => 'menu-sidebar-title',
				'type'     => 'text',
				'title'    => __( 'Sidebar Menu Title', 'porto' ),
				'subtitle' => __( 'Input the title of sidebar menu.', 'porto' ),
				'default'  => __( 'All Department', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-sidebar-title.jpg"/>' ),
				),
			),		
			array(
				'id'        => 'side-menu-type',
				'type'      => 'image_select',
				'title'     => __( 'Menu Type of Side Header or Sidebar', 'porto' ),
				'subtitle'      => __( 'Controls how to show its submenus.', 'porto' ),
				'options'   => array(
					''          => array(
						'title' => __( 'Normal', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sidemenu-normal.svg',
					),
					'accordion' => array(
						'title' => __( 'Accordion Menu', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sidemenu-accordion.svg',
					),
					'slide'     => array(
						'title' => __( 'Horizontal Slide Menu', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sidemenu-slide.svg',
					),
					'columns'   => array(
						'title' => __( 'Horizontal Columns', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sidemenu-columns.svg',
					),
				),
				'default'   => '',
				'transport' => 'refresh',
			),
			array(
				'id'       => 'menu-sidebar-toggle',
				'type'     => 'switch',
				'title'    => __( 'Toggle Sidebar Menu', 'porto' ),
				'subtitle' => __( 'Add a toggle button of the sidebar menu.', 'porto' ),
				'required' => array( 'menu-sidebar', 'equals', true ),
				'default'  => false,
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-sidebar-toggle.gif"/>' ),
				),
			),
			array(
				'id'        => 'menu-sidebar-home',
				'type'      => 'switch',
				'title'     => __( 'Show Main Menu in only the homepage sidebar', 'porto' ),
				'subtitle'  => __( 'You can see sidebar menu only on homepage.', 'porto' ),
				'required'  => array( 'menu-sidebar', 'equals', true ),
				'default'   => true,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'refresh',
				'hint'      => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-sidebar-home.jpg"/>' ),
					'required'  => array(
						array( 'header-type-select', 'not', 'header_builder_p' ),
					),
				),
			),
			array(
				'id'       => 'menu-title',
				'type'     => 'text',
				'title'    => __( 'Title of Toggle Menu like Shop 35', 'porto' ),
				'subtitle' => __( 'Please change the title of Toggle menu.', 'porto' ),
				'default'  => __( 'All Department', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-title.gif"/>' ),
				),
			),
			array(
				'id'        => 'menu-toggle-onhome',
				'type'      => 'switch',
				'title'     => __( 'Collapse the Toggle Menu on home page', 'porto' ),
				'subtitle'  => __( 'In homepage, a toggle menu is collapsed at first. Then it works as a toggle....', 'porto' ),
				'default'   => false,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'refresh',
				'hint'      => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-toggle-onhome.gif"/>' ),
				),
				'required'  => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
		),		
	);
}

if ( $this->legacy_mode ) {
	$this->sections[] = array(
		'id'         => 'skin-main-menu',
		'icon_class' => 'icon',
		'subsection' => true,
		'title'      => __( 'Menu Styling', 'porto' ),
		'transport'  => 'postMessage',
		'fields'     => array(
			array(
				'id'    => 'desc_info_menu_skin_notice',
				'type'  => 'info',
				'desc'  => wp_kses(
					/* translators: %s: Builder url */
					sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Menu widget.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $header_url ),
					array(
						'strong' => array(),
						'b'      => array(),
						'a'      => array(
							'href'   => array(),
							'target' => array(),
							'class'  => array(),
						),
						'i'     => array(
							'class'  => array(),
						),
						'span'  => array(),
						'br'    => array(),
					)
				),
				'class' => 'porto-important-note',
			),
			array(
				'id'       => 'mainmenu-wrap-bg-color',
				'type'     => 'color',
				'title'    => __( 'Main Menu Wrapper Background Color', 'porto' ),
				'subtitle' => __( 'if header type is 1, 4, 9, 13, 14, 17 or header builder which contains main menu in header bottom section.', 'porto' ),
				'default'  => 'transparent',
				'validate' => 'color',
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-wrap-bg-color.jpg"/>' ),
				),
			),
			array(
				'id'       => 'mainmenu-wrap-bg-color-sticky',
				'type'     => 'color',
				'title'    => __( 'Main Menu Wrapper Background Color in Sticky Header', 'porto' ),
				'subtitle' => __( 'if header type is 1, 4, 9, 13, 14, 17 or header builder which contains main menu in header bottom section.', 'porto' ),
				'validate' => 'color',
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-wrap-bg-cs.jpg"/>' ),
				),
			),
			array(
				'id'       => 'mainmenu-wrap-padding',
				'type'     => 'spacing',
				'mode'     => 'padding',
				'title'    => __( 'Main Menu Wrapper Padding', 'porto' ),
				'subtitle' => __( 'if header type is 1, 4, 9, 13, 14, 17 or header builder which contains main menu in header bottom section.', 'porto' ),
				'default'  => array(
					'padding-top'    => 0,
					'padding-bottom' => 0,
					'padding-left'   => 0,
					'padding-right'  => 0,
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-wrap-padding.gif"/>' ),
				),
			),
			array(
				'id'       => 'mainmenu-bg-color',
				'type'     => 'color',
				'title'    => __( 'Main Menu Background Color', 'porto' ),
				'subtitle' => __( 'Controls the background color for main menu.', 'porto' ),
				'default'  => 'transparent',
				'validate' => 'color',
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-bg-color.jpg"/>' ),
				),
			),
			array(
				'id'     => 'desc_info_top_level',
				'type'   => 'info',
				'title'  => __( 'Top Level Menu Item', 'porto' ),
				'notice' => false,
				'class'  => 'pt-always-visible',
			),
			array(
				'id'             => 'menu-font',
				'type'           => 'typography',
				'title'          => __( 'Menu Font', 'porto' ),
				'subtitle'       => __( 'Controls the typography for main menu\'s first level items.', 'porto' ),
				'google'         => true,
				'subsets'        => false,
				'font-style'     => false,
				'text-align'     => false,
				'color'          => false,
				'letter-spacing' => true,
				'default'        => array(
					'google'         => true,
					'font-weight'    => '700',
					'font-size'      => '12px',
					'line-height'    => '20px',
					'letter-spacing' => '0',
				),
				'hint'           => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-font.jpg"/>' ),
				),
				'transport'      => 'refresh',
				'class'          => 'pt-always-visible',
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'             => 'menu-side-font',
				'type'           => 'typography',
				'title'          => __( 'Side Menu Font', 'porto' ),
				'subtitle'       => __( 'Controls the typography for main sidebar menu\'s first level items.', 'porto' ),
				'google'         => true,
				'subsets'        => false,
				'font-style'     => false,
				'text-align'     => false,
				'color'          => false,
				'letter-spacing' => true,
				'default'        => array(
					'google'         => true,
					'font-weight'    => '400',
					'font-size'      => '14px',
					'line-height'    => '18px',
					'letter-spacing' => '0',
				),
				'selector'       => array(
					'node' => '.main-sidebar-menu',
				),
				'hint'           => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-side-font.jpg"/>' ),
				),
				'class'          => 'pt-always-visible',
			),
			array(
				'id'       => 'menu-text-transform',
				'type'     => 'button_set',
				'title'    => __( 'Text Transform', 'porto' ),
				'subtitle' => __( 'Controls the text transform for the first level items of main menu and main sidebar menu.', 'porto' ),
				'options'  => array(
					'none'       => __( 'None', 'porto' ),
					'capitalize' => __( 'Capitalize', 'porto' ),
					'uppercase'  => __( 'Uppercase', 'porto' ),
					'lowercase'  => __( 'Lowercase', 'porto' ),
					'initial'    => __( 'Initial', 'porto' ),
				),
				'default'  => 'uppercase',
				'selector' => array(
					'node' => ':root',
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'class'   => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-toplevel-link-color',
				'type'     => 'link_color',
				'active'   => false,
				'title'    => __( 'Link Color', 'porto' ),
				'subtitle' => __( 'Controls the menu item color for the first level items of main menu.', 'porto' ),
				'default'  => array(
					'regular' => 'var(--porto-primary-color)',
					'hover'   => '#ffffff',
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-link-color.gif"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-toplevel-link-color-sticky',
				'type'     => 'link_color',
				'active'   => true,
				'title'    => __( 'Link Color in Sticky Header', 'porto' ),
				'subtitle' => __( 'Controls the menu item color for the first level items of main menu in sticky header.', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-link-cs.gif"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-toplevel-hbg-color',
				'type'     => 'color',
				'title'    => __( 'Hover Background Color', 'porto' ),
				'subtitle' => __( 'Controls the background color for the first level items on hover and active.', 'porto' ),
				'default'  => 'var(--porto-primary-color)',
				'validate' => 'color',
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-hbg-color.gif"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'class'   => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-toplevel-config-active',
				'type'     => 'switch',
				'title'    => __( 'Configure Active Color', 'porto' ),
				'subtitle' => __( 'Controls the background and color for the first level active menu items.', 'porto' ),
				'default'  => false,
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-config.gif"/>' ),
				),
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-toplevel-alink-color',
				'type'     => 'color',
				'title'    => __( 'Active Link Color', 'porto' ),
				'required' => array( 'mainmenu-toplevel-config-active', 'equals', true ),
				'default'  => '#ffffff',
				'validate' => 'color',
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-toplevel-abg-color',
				'type'     => 'color',
				'title'    => __( 'Active Background Color', 'porto' ),
				'required' => array( 'mainmenu-toplevel-config-active', 'equals', true ),
				'default'  => 'var(--porto-primary-color)',
				'validate' => 'color',
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-toplevel-padding1',
				'type'     => 'spacing',
				'mode'     => 'padding',
				'title'    => __( 'Padding on Desktop', 'porto' ),
				'subtitle' => __( 'Controls the padding for the first level menu items on desktop.', 'porto' ),
				'desc'     => __( 'This is not working for sidebar menus.', 'porto' ),
				'default'  => array(
					'padding-top'    => 10,
					'padding-bottom' => 10,
					'padding-left'   => 16,
					'padding-right'  => 16,
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-padding1.gif"/>' ),
				),
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-toplevel-padding2',
				'type'     => 'spacing',
				'mode'     => 'padding',
				'title'    => __( 'Padding on Desktop (width > 991px)', 'porto' ),
				'subtitle' => __( 'Controls the padding for the first level menu items on small desktop.', 'porto' ),
				'desc'     => __( 'This is not working for sidebar menus.', 'porto' ),
				'default'  => array(
					'padding-top'    => 9,
					'padding-bottom' => 9,
					'padding-left'   => 14,
					'padding-right'  => 14,
				),
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-toplevel-padding3',
				'type'     => 'spacing',
				'mode'     => 'padding',
				'title'    => __( 'Padding on Sticky Header (width > 991px)', 'porto' ),
				'subtitle' => __( 'Controls the padding for the first level menu items in sticky header on large displays.', 'porto' ),
				'desc'     => __( 'This is not working for sidebar menus. Please leave blank if you use same values with the ones in default header.', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-padding3.gif"/>' ),
				),
				'class'    => 'pt-always-visible',
			),
			array(
				'id'     => 'desc_info_menu_popup',
				'type'   => 'info',
				'title'  => __( 'Menu Popup', 'porto' ),
				'notice' => false,
				'class'  => 'pt-always-visible',
			),
			array(
				'id'             => 'menu-popup-font',
				'type'           => 'typography',
				'title'          => __( 'Menu Popup Font', 'porto' ),
				'google'         => true,
				'subsets'        => false,
				'font-style'     => false,
				'text-align'     => false,
				'color'          => false,
				'letter-spacing' => true,
				'default'        => array(
					'google'         => true,
					'font-weight'    => '400',
					'font-size'      => '14px',
					'line-height'    => '24px',
					'letter-spacing' => '0',
				),
				'transport'      => 'refresh',
				'hint'           => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-popup-font.jpg"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'class'   => 'pt-always-visible',
			),
			array(
				'id'      => 'menu-popup-text-transform',
				'type'    => 'button_set',
				'title'   => __( 'Text Transform', 'porto' ),
				'options' => array(
					'none'       => __( 'None', 'porto' ),
					'capitalize' => __( 'Capitalize', 'porto' ),
					'uppercase'  => __( 'Uppercase', 'porto' ),
					'lowercase'  => __( 'Lowercase', 'porto' ),
					'initial'    => __( 'Initial', 'porto' ),
				),
				'default' => 'none',
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'class'   => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-popup-top-border',
				'type'     => 'border',
				'all'      => false,
				'style'    => false,
				'left'     => false,
				'right'    => false,
				'bottom'   => false,
				'title'    => __( 'Top Border', 'porto' ),
				'subtitle' => __( 'Control the menu popup border color and border width.', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-tb.gif"/>' ),
				),
				'class'   => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-popup-bg-color',
				'type'     => 'color',
				'title'    => __( 'Background Color', 'porto' ),
				'subtitle' => wp_kses(
					__( 'Controls the background color of the mega menu. For the narrow mega menu, this option is overridden by the <strong>Hover Background Color</strong>.', 'porto' ),
					array(
						'strong' => array(),
					)
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-bc.jpg"/>' ),
				),
				'default'  => '#ffffff',
				'validate' => 'color',
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-popup-heading-color',
				'type'     => 'color',
				'title'    => __( 'Heading Color', 'porto' ),
				'subtitle' => __( 'Controls the color of sub titles in the mega menu (wide menu).', 'porto' ),
				'default'  => '#333333',
				'validate' => 'color',
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-hc.jpg"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-popup-text-color',
				'type'     => 'link_color',
				'active'   => false,
				'title'    => __( 'Link Color', 'porto' ),
				'default'  => array(
					'regular' => '#777777',
					'hover'   => '#777777',
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-tc.jpg"/>' ),
				),
				'selector' => array(
					'node' => 'li.menu-item, .sub-menu',
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-popup-text-hbg-color',
				'type'     => 'color',
				'title'    => __( 'Link Hover Background Color', 'porto' ),
				'subtitle' => __( 'Only if \'Horizontal Slide Menu\', Controls the border color instead background color.', 'porto' ),
				'default'  => '#f4f4f4',
				'validate' => 'color',
				'selector' => array(
					'node' => 'li.menu-item',
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-thbg-color.gif"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-popup-narrow-type',
				'type'     => 'button_set',
				'title'    => __( 'Narrow Menu Style', 'porto' ),
				'subtitle' => __( 'Controls the background color style for the narrow sub menus (menu popup).', 'porto' ),
				'desc'     => __( 'If you select "With Top Menu Hover Bg Color", please insert hover background color for the first level items in the "Top Level Menu Item / Hover Background Color".', 'porto' ),
				'options'  => array(
					''  => array(
						'label' => __( 'With Popup BG Color', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-nt-bc.gif"/>' ),
						),
					),
					'1' => array(
						'label' => __( 'With Top Menu Hover Bg Color', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-nt-hbc.gif"/>' ),
						),
					),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'default'  => '',
			),
			array(
				'id'     => 'desc_info_tip',
				'type'   => 'info',
				'title'  => __( 'Tip', 'porto' ),
				'notice' => false,
				'class'  => 'pt-always-visible',
			),
			array(
				'id'       => 'mainmenu-tip-bg-color',
				'type'     => 'color',
				'title'    => __( 'Tip Background Color', 'porto' ),
				'subtitle' => __( 'Controls the background color for the tip labels in the main menu item.', 'porto' ),
				'default'  => '#0cc485',
				'validate' => 'color',
				'class'    => 'pt-always-visible',
			),
			array(
				'id'     => 'desc_info_menu_custom',
				'type'   => 'info',
				'title'  => __( 'Menu Custom Content (if header type is 1, 3, 4, 9, 13, 14, 19 or header builder)', 'porto' ),
				'notice' => false,
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'menu-custom-text-color',
				'type'     => 'color',
				'title'    => __( 'Text Color', 'porto' ),
				'subtitle' => __( 'Controls the text color for the menu custom content which is inserted in Header / Menu Custom Content', 'porto' ),
				'default'  => '#777777',
				'validate' => 'color',
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-custom-tc.jpg"/>' ),
				),
			),
			array(
				'id'       => 'menu-custom-link',
				'type'     => 'link_color',
				'title'    => __( 'Link Color', 'porto' ),
				'subtitle' => __( 'Controls the color of A tag for the menu custom content which is inserted in Header / Menu Custom Content', 'porto' ),
				'active'   => false,
				'default'  => array(
					'regular' => 'var(--porto-primary-color)',
					'hover'   => 'var(--porto-primary-light-5)',
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-custom-link.gif"/>' ),
				),
			),
		),
	);
} else {
	$this->sections[] = array(
		'id'         => 'skin-main-menu',
		'icon_class' => 'icon',
		'subsection' => true,
		'title'      => __( 'Menu Styling', 'porto' ),
		'transport'  => 'postMessage',
		'fields'     => array(
			array(
				'id'     => 'desc_info_top_level',
				'type'   => 'info',
				'title'  => __( 'Top Level Menu Item', 'porto' ),
				'notice' => false,
			),
			array(
				'id'             => 'menu-font',
				'type'           => 'typography',
				'title'          => __( 'Menu Font', 'porto' ),
				'subtitle'       => __( 'Controls the typography for main menu\'s first level items.', 'porto' ),
				'google'         => true,
				'subsets'        => false,
				'font-style'     => false,
				'text-align'     => false,
				'color'          => false,
				'letter-spacing' => true,
				'default'        => array(
					'google'         => true,
					'font-weight'    => '700',
					'font-size'      => '12px',
					'line-height'    => '20px',
					'letter-spacing' => '0',
				),
				'transport'      => 'refresh',
				'hint'           => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-font.jpg"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'             => 'menu-side-font',
				'type'           => 'typography',
				'title'          => __( 'Side Menu Font', 'porto' ),
				'subtitle'       => __( 'Controls the typography for main sidebar menu\'s first level items.', 'porto' ),
				'google'         => true,
				'subsets'        => false,
				'font-style'     => false,
				'text-align'     => false,
				'color'          => false,
				'letter-spacing' => true,
				'default'        => array(
					'google'         => true,
					'font-weight'    => '400',
					'font-size'      => '14px',
					'line-height'    => '18px',
					'letter-spacing' => '0',
				),
				'transport'      => 'refresh',
				'selector'       => array(
					'node' => '.main-sidebar-menu',
				),
				'hint'           => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-side-font.jpg"/>' ),
				),
			),
			array(
				'id'       => 'menu-text-transform',
				'type'     => 'button_set',
				'title'    => __( 'Text Transform', 'porto' ),
				'subtitle' => __( 'Controls the text transform for the first level items of main menu and main sidebar menu.', 'porto' ),
				'options'  => array(
					'none'       => __( 'None', 'porto' ),
					'capitalize' => __( 'Capitalize', 'porto' ),
					'uppercase'  => __( 'Uppercase', 'porto' ),
					'lowercase'  => __( 'Lowercase', 'porto' ),
					'initial'    => __( 'Initial', 'porto' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'default'  => 'uppercase',
				'selector' => array(
					'node' => ':root',
				),
			),
			array(
				'id'       => 'mainmenu-toplevel-link-color',
				'type'     => 'link_color',
				'active'   => false,
				'title'    => __( 'Link Color', 'porto' ),
				'subtitle' => __( 'Controls the menu item color for the first level items of main menu.', 'porto' ),
				'default'  => array(
					'regular' => 'var(--porto-primary-color)',
					'hover'   => '#ffffff',
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-link-color.gif"/>' ),
				),
			),
			array(
				'id'       => 'mainmenu-toplevel-link-color-sticky',
				'type'     => 'link_color',
				'active'   => true,
				'title'    => __( 'Link Color in Sticky Header', 'porto' ),
				'subtitle' => __( 'Controls the menu item color for the first level items of main menu in sticky header.', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-link-cs.gif"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-toplevel-hbg-color',
				'type'     => 'color',
				'title'    => __( 'Hover Background Color', 'porto' ),
				'subtitle' => __( 'Controls the background color for the first level items on hover and active.', 'porto' ),
				'default'  => 'var(--porto-primary-color)',
				'validate' => 'color',
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-hbg-color.gif"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-toplevel-config-active',
				'type'     => 'switch',
				'title'    => __( 'Configure Active Color', 'porto' ),
				'subtitle' => __( 'Controls the background and color for the first level active menu items.', 'porto' ),
				'default'  => false,
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-config.gif"/>' ),
				),
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'mainmenu-toplevel-alink-color',
				'type'     => 'color',
				'title'    => __( 'Active Link Color', 'porto' ),
				'required' => array( 'mainmenu-toplevel-config-active', 'equals', true ),
				'default'  => '#ffffff',
				'validate' => 'color',
			),
			array(
				'id'       => 'mainmenu-toplevel-abg-color',
				'type'     => 'color',
				'title'    => __( 'Active Background Color', 'porto' ),
				'required' => array( 'mainmenu-toplevel-config-active', 'equals', true ),
				'default'  => 'var(--porto-primary-color)',
				'validate' => 'color',
			),
			array(
				'id'       => 'mainmenu-toplevel-padding1',
				'type'     => 'spacing',
				'mode'     => 'padding',
				'title'    => __( 'Padding on Desktop', 'porto' ),
				'subtitle' => __( 'Controls the padding for the first level menu items on desktop.', 'porto' ),
				'desc'     => __( 'This is not working for sidebar menus.', 'porto' ),
				'default'  => array(
					'padding-top'    => 10,
					'padding-bottom' => 10,
					'padding-left'   => 16,
					'padding-right'  => 16,
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-padding1.gif"/>' ),
				),
			),
			array(
				'id'       => 'mainmenu-toplevel-padding2',
				'type'     => 'spacing',
				'mode'     => 'padding',
				'title'    => __( 'Padding on Desktop (width > 991px)', 'porto' ),
				'subtitle' => __( 'Controls the padding for the first level menu items on small desktop.', 'porto' ),
				'desc'     => __( 'This is not working for sidebar menus.', 'porto' ),
				'default'  => array(
					'padding-top'    => 9,
					'padding-bottom' => 9,
					'padding-left'   => 14,
					'padding-right'  => 14,
				),
			),
			array(
				'id'       => 'mainmenu-toplevel-padding3',
				'type'     => 'spacing',
				'mode'     => 'padding',
				'title'    => __( 'Padding on Sticky Header (width > 991px)', 'porto' ),
				'subtitle' => __( 'Controls the padding for the first level menu items in sticky header on large displays.', 'porto' ),
				'desc'     => __( 'This is not working for sidebar menus. Please leave blank if you use same values with the ones in default header.', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-toplevel-padding3.gif"/>' ),
				),
			),
			array(
				'id'     => 'desc_info_menu_popup',
				'type'   => 'info',
				'title'  => __( 'Menu Popup', 'porto' ),
				'notice' => false,
			),
			array(
				'id'             => 'menu-popup-font',
				'type'           => 'typography',
				'title'          => __( 'Menu Popup Font', 'porto' ),
				'google'         => true,
				'subsets'        => false,
				'font-style'     => false,
				'text-align'     => false,
				'color'          => false,
				'letter-spacing' => true,
				'default'        => array(
					'google'         => true,
					'font-weight'    => '400',
					'font-size'      => '14px',
					'line-height'    => '24px',
					'letter-spacing' => '0',
				),
				'transport'      => 'refresh',
				'hint'           => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-popup-font.jpg"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'      => 'menu-popup-text-transform',
				'type'    => 'button_set',
				'title'   => __( 'Text Transform', 'porto' ),
				'options' => array(
					'none'       => __( 'None', 'porto' ),
					'capitalize' => __( 'Capitalize', 'porto' ),
					'uppercase'  => __( 'Uppercase', 'porto' ),
					'lowercase'  => __( 'Lowercase', 'porto' ),
					'initial'    => __( 'Initial', 'porto' ),
				),
				'default' => 'none',
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-popup-top-border',
				'type'     => 'border',
				'all'      => false,
				'style'    => false,
				'left'     => false,
				'right'    => false,
				'bottom'   => false,
				'title'    => __( 'Top Border', 'porto' ),
				'subtitle' => __( 'Control the menu popup border color and border width.', 'porto' ),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-tb.gif"/>' ),
				),
			),
			array(
				'id'       => 'mainmenu-popup-bg-color',
				'type'     => 'color',
				'title'    => __( 'Background Color', 'porto' ),
				'subtitle' => wp_kses(
					__( 'Controls the background color of the mega menu. For the narrow mega menu, this option is overridden by the <strong>Hover Background Color</strong>.', 'porto' ),
					array(
						'strong' => array(),
					)
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-bc.jpg"/>' ),
				),
				'default'  => '#ffffff',
				'validate' => 'color',
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-popup-heading-color',
				'type'     => 'color',
				'title'    => __( 'Heading Color', 'porto' ),
				'subtitle' => __( 'Controls the color of sub titles in the mega menu (wide menu).', 'porto' ),
				'default'  => '#333333',
				'validate' => 'color',
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-hc.jpg"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-popup-text-color',
				'type'     => 'link_color',
				'active'   => false,
				'title'    => __( 'Link Color', 'porto' ),
				'default'  => array(
					'regular' => '#777777',
					'hover'   => '#777777',
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-tc.jpg"/>' ),
				),
				'selector' => array(
					'node' => 'li.menu-item, .sub-menu',
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-popup-text-hbg-color',
				'type'     => 'color',
				'title'    => __( 'Link Hover Background Color', 'porto' ),
				'subtitle' => __( 'Only if \'Horizontal Slide Menu\', Controls the border color instead background color.', 'porto' ),
				'default'  => '#f4f4f4',
				'validate' => 'color',
				'selector' => array(
					'node' => 'li.menu-item',
				),
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-thbg-color.gif"/>' ),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
			),
			array(
				'id'       => 'mainmenu-popup-narrow-type',
				'type'     => 'button_set',
				'title'    => __( 'Narrow Menu Style', 'porto' ),
				'subtitle' => __( 'Controls the background color style for the narrow sub menus (menu popup).', 'porto' ),
				'desc'     => __( 'If you select "With Top Menu Hover Bg Color", please insert hover background color for the first level items in the "Top Level Menu Item / Hover Background Color".', 'porto' ),
				'options'  => array(
					''  => array(
						'label' => __( 'With Popup BG Color', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-nt-bc.gif"/>' ),
						),
					),
					'1' => array(
						'label' => __( 'With Top Menu Hover Bg Color', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mainmenu-popup-nt-hbc.gif"/>' ),
						),
					),
				),
				'required' => array(
					array( 'header-type-select', 'not', 'header_builder_p' ),
				),
				'default'  => '',
			),
			array(
				'id'     => 'desc_info_tip',
				'type'   => 'info',
				'title'  => __( 'Tip', 'porto' ),
				'notice' => false,
			),
			array(
				'id'       => 'mainmenu-tip-bg-color',
				'type'     => 'color',
				'title'    => __( 'Tip Background Color', 'porto' ),
				'subtitle' => __( 'Controls the background color for the tip labels in the main menu item.', 'porto' ),
				'default'  => '#0cc485',
				'validate' => 'color',
			),
		),
	);
}

$this->sections[] = array(
	'id'         => 'mobile-panel-settings',
	'icon_class' => 'icon',
	'subsection' => true,
	'title'      => __( 'Mobile Menu', 'porto' ),
	'fields'     => array(
		array(
			'id'      => 'mobile-panel-type',
			'type'    => 'image_select',
			'title'   => __( 'Mobile Panel Type', 'porto' ),
			'subtitle'    => __( 'Controls the panel type of mobile toggle menu.', 'porto' ),
			'options' => array(
				''     => array(
					'title' => __( 'Default', 'porto' ),
					'img'   => PORTO_OPTIONS_URI . '/svg/mobile-default.svg',
				),
				'side' => array(
					'title' => __( 'Side Navigation', 'porto' ),
					'img'   => PORTO_OPTIONS_URI . '/svg/mobile-side.svg',
				),
			),
			'default' => '',
		),
		array(
			'id'        => 'mobile-panel-pos',
			'type'      => 'button_set',
			'title'     => __( 'Position', 'porto' ),
			'subtitle'      => __( 'Controls the position of mobile offcanvas menu.', 'porto' ),
			'options'   => array(
				''            => __( 'Default', 'porto' ),
				'panel-left'  => array(
                    'label' => __( 'Left (Right on RTL)', 'porto' ),
                    'hint'  => array(
                        'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mobile-panel-pos-left.jpg"/>' ),
                    ),
                ),
				'panel-right'  => array(
                    'label' => __( 'Right (Left on RTL)', 'porto' ),
                    'hint'  => array(
                        'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mobile-panel-pos-right.jpg"/>' ),
                    ),
                ),
			),
			'default'   => '',
			'required'  => array( 'mobile-panel-type', 'equals', array( 'side' ) ),
			'transport' => 'postMessage',
		),
		array(
			'id'        => 'mobile-panel-add-switcher',
			'type'      => 'switch',
			'title'     => __( 'Add Language, Currency Switcher', 'porto' ),
			'subtitle'  => sprintf( __( 'Determines whether to put the switchers in the mobile menu. You should enable %1$sHeader/Language,Currency Switcher%2$s', 'porto' ), '<b>', '</b>' ),
			'required'  => array( 'mobile-panel-type', 'equals', array( '', 'side' ) ),
			'default'   => false,
			'on'        => __( 'Yes', 'porto' ),
			'off'       => __( 'No', 'porto' ),
			'transport' => 'postMessage',
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mobile-panel-add-switcher.jpg"/>' ),
			),
		),
		array(
			'id'        => 'mobile-panel-add-search',
			'type'      => 'switch',
			'title'     => __( 'Add Search Box', 'porto' ),
			'subtitle'  => __( 'Determines whether to put a search box in the mobile menu.', 'porto' ),
			'required'  => array( 'mobile-panel-type', 'equals', array( 'side' ) ),
			'default'   => true,
			'on'        => __( 'Yes', 'porto' ),
			'off'       => __( 'No', 'porto' ),
			'transport' => 'postMessage',
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mobile-panel-add-search.jpg"/>' ),
			),
		),
		array(
			'id'       => 'desc_info_mobile_menu_location',
			'type'     => 'info',
			'title'    => __( 'Display Mobile Menus', 'porto' ),
			'notice'   => false,
			'required' => array( 'mobile-panel-type', 'contains', 'side' ),
		),
		array(
			'id'          => 'show-mobile-menus',
			'type'        => 'select',
			'title'       => __( 'Select menus to display on mobile', 'porto' ),
			'subtitle'    => __( 'This will show menus on the top of the mobile menu as tab title.', 'porto' ),
			'sortable'    => true,
			'multi'       => true,
			'class'       => 'porto-select-input',
			'options'     => array(
				'main'       => __( 'Main Menu', 'porto' ),
				'secondary'  => __( 'Secondary Menu', 'porto' ),
				'side'       => __( 'Sidebar Menu', 'porto' ),
				'navigation' => __( 'Top Navigation', 'porto' ),
			),
			'default'     => array( 'main' ),
			'required'    => array( 'mobile-panel-type', 'contains', 'side' ),
			'hint'        => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-mobile-menus.jpg"/>' ),
			),
		),
		array(
			'id'       => 'menu-main',
			'type'     => 'text',
			'title'    => __( 'Tab Title for Main Menu', 'porto' ),
			'default'  => __( 'Main Menu', 'porto' ),
			'required' => array( 'show-mobile-menus', 'contains', 'main' ),
		),
		array(
			'id'       => 'menu-secondary',
			'type'     => 'text',
			'title'    => __( 'Tab Title for Secondary Menu', 'porto' ),
			'default'  => __( 'Secondary Menu', 'porto' ),
			'required' => array( 'show-mobile-menus', 'contains', 'secondary' ),
		),
		array(
			'id'       => 'menu-side',
			'type'     => 'text',
			'title'    => __( 'Tab Title for Sidebar Menu', 'porto' ),
			'default'  => __( 'Sidebar Menu', 'porto' ),
			'required' => array( 'show-mobile-menus', 'contains', 'side' ),
		),
		array(
			'id'       => 'menu-navigation',
			'type'     => 'text',
			'title'    => __( 'Tab Title for Top Navigation', 'porto' ),
			'default'  => __( 'Top Navigation', 'porto' ),
			'required' => array( 'show-mobile-menus', 'contains', 'navigation' ),
		),
		array(
			'id'       => 'panel-tab-color',
			'type'     => 'link_color',
			'hover'    => false,
			'title'    => __( 'Tab Title Color', 'porto' ),
			'desc'     => __( 'Controls the color in mobile menu tab.', 'porto' ),
			'default' => array(
				'regular' => '',
				'hover'   => '',
			),
			'required' => array( 'mobile-panel-type', 'contains', 'side' ),
		),
		array(
			'id'     => 'desc_info_mobile_menu_toggle',
			'type'   => 'info',
			'title'  => __( 'Mobile Menu Toggle', 'porto' ),
			'notice' => false,
			'required' => array(
				array( 'header-type-select', 'not', 'header_builder_p' ),
			),
		),
		array(
			'id'       => 'mobile-menu-toggle-text-color',
			'type'     => 'color',
			'title'    => __( 'Toggle Icon Color', 'porto' ),
			'subtitle'     => __( 'Controls the color of mobile toggle icon.', 'porto' ),
			'default'  => '#fff',
			'validate' => 'color',
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mobile-menu-toggle-tc.gif"/>' ),
			),
			'required' => array(
				array( 'header-type-select', 'not', 'header_builder_p' ),
			),
		),
		array(
			'id'       => 'mobile-menu-toggle-bg-color',
			'type'     => 'color',
			'title'    => __( 'Background Color', 'porto' ),
			'subtitle' => __( 'Controls the background color of mobile toggle icon.', 'porto' ),
			'validate' => 'color',
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'mobile-menu-toggle-bc.gif"/>' ),
			),
			'required' => array(
				array( 'header-type-select', 'not', 'header_builder_p' ),
			),
		),
		array(
			'id'     => 'desc_info_mobile_menu_panel',
			'type'   => 'info',
			'title'  => __( 'Mobile Menu Panel', 'porto' ),
			'notice' => false,
		),
		array(
			'id'       => 'panel-bg-color',
			'type'     => 'color',
			'title'    => __( 'Background Color', 'porto' ),
			'subtitle'     => __( 'Controls the background color of mobile offcanvas or dropdown.', 'porto' ),
			'validate' => 'color',
			'default'  => '#ffffff',
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'panel-bg-color.jpg"/>' ),
			),

		),
		array(
			'id'       => 'panel-border-color',
			'type'     => 'color',
			'title'    => __( 'Border Color', 'porto' ),
			'subtitle'     => __( 'Controls the divider color of mobile offcanvas or dropdown.', 'porto' ),
			'default'  => '#e7e7e7',
			'validate' => 'color',
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'panel-border-color.jpg"/>' ),
			),

		),
		array(
			'id'       => 'panel-link-hbgcolor',
			'type'     => 'color',
			'title'    => __( 'Hover Background Color', 'porto' ),
			'subtitle'     => __( 'Controls the hover / active background color of mobile menu item.', 'porto' ),
			'default'  => '',
			'validate' => 'color',
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'panel-link-hbgcolor.jpg"/>' ),
			),
		),
		array(
			'id'       => 'panel-text-color',
			'type'     => 'color',
			'title'    => __( 'Text Color', 'porto' ),
			'subtitle'     => __( 'Controls the text color in mobile panel.', 'porto' ),
			'default'  => '',
			'validate' => 'color',
		),
		array(
			'id'       => 'panel-link-color',
			'type'     => 'link_color',
			'active'   => false,
			'title'    => __( 'Link Color', 'porto' ),
			'subtitle' => __( 'Controls the link color in mobile panel.', 'porto' ),
			'default'  => array(
				'regular' => '',
				'hover'   => '',
			),
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'panel-link-color.jpg"/>' ),
			),
		),
	),
);

if ( class_exists( 'WooCommerce' ) ) {
	$this->sections[] = array(
		'id'         => 'mobile-sticky-bar',
		'icon_class' => 'icon',
		'subsection' => true,
		'title'      => __( 'Mobile Bottom Sticky Bar', 'porto' ),
		'fields'     => array(
			array(
				'id'      => 'show-icon-menus-mobile',
				'type'    => 'button_set',
				'title'   => __( 'Show Sticky Icon Menu bar on mobile', 'porto' ),
				'subtitle'    => __( 'This will show sticky icon menu bar at the bottom of the page on mobile.', 'porto' ),
				'multi'   => true,
				'options' => array(
					'home'     => __( 'Home', 'porto' ),
					'blog'     => __( 'Blog', 'porto' ),
					'shop'     => __( 'Shop', 'porto' ),
					'wishlist' => __( 'Wishlist', 'porto' ),
					'account'  => __( 'Account', 'porto' ),
					'cart'     => __( 'Cart', 'porto' ),
				),
				'default' => array(),
				'hint'    => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show-icon-menus-mobile.jpg"/>' ),
				),
			),
			array(
				'id'       => 'sticky-icon-home',
				'type'     => 'text',
				'title'    => __( 'Home Icon', 'porto' ),
				'default'  => __( 'porto-icon-category-home', 'porto' ),
				'required' => array( 'show-icon-menus-mobile', 'contains', 'home' ),
			),
			array(
				'id'       => 'sticky-icon-blog',
				'type'     => 'text',
				'title'    => __( 'Blog Icon', 'porto' ),
				'default'  => __( 'far fa-calendar-alt', 'porto' ),
				'required' => array( 'show-icon-menus-mobile', 'contains', 'blog' ),
			),
			array(
				'id'       => 'sticky-icon-shop',
				'type'     => 'text',
				'title'    => __( 'Shop Icon', 'porto' ),
				'default'  => __( 'porto-icon-bars', 'porto' ),
				'required' => array( 'show-icon-menus-mobile', 'contains', 'shop' ),
			),
			array(
				'id'       => 'sticky-icon-wishlist',
				'type'     => 'text',
				'title'    => __( 'Wishlist Icon', 'porto' ),
				'default'  => __( 'porto-icon-wishlist-2', 'porto' ),
				'required' => array( 'show-icon-menus-mobile', 'contains', 'wishlist' ),
			),
			array(
				'id'       => 'sticky-icon-account',
				'type'     => 'text',
				'title'    => __( 'Account Icon', 'porto' ),
				'default'  => __( 'porto-icon-user-2', 'porto' ),
				'required' => array( 'show-icon-menus-mobile', 'contains', 'account' ),
			),
			array(
				'id'       => 'sticky-icon-cart',
				'type'     => 'text',
				'title'    => __( 'Cart Icon', 'porto' ),
				'default'  => __( 'porto-icon-shopping-cart', 'porto' ),
				'required' => array( 'show-icon-menus-mobile', 'contains', 'cart' ),
			),
		),
	);
}
