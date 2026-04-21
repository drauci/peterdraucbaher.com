<?php

// Woocommerce
$all_roles = array();
if ( is_admin() ) {
	$roles = wp_roles()->roles;
	$roles = apply_filters( 'editable_roles', $roles );
	foreach ( $roles as $role_name => $role_info ) {
		$initial_assigned_roles = array( $role_name => $role_info['name'] );
		$all_roles              = array_merge( $all_roles, $initial_assigned_roles );
	}
}
$this->sections[] = $this->add_customizer_field(
	array(
		'icon'       => 'icon-plugins',
		'icon_class' => 'porto-icon',
		'title'      => __( 'WooCommerce', 'porto' ),
	),
	$options_style
);

$this->sections[] = array(
	'icon_class' => 'icon',
	'subsection' => true,
	'title'      => __( 'General', 'porto' ),
	'transport'  => 'postMessage',
	'id'         => 'wc-general',
	'fields'     => array(
		array(
			'id'    => 'desc_info_builder_product',
			'type'  => 'info',
			'desc'  => wp_kses( 
				__( '
				<span><span style="min-width: 150px;">
					<b>Product Type</b>
					<span class="description">You can change the product type, product layout, shop layout.</span>
				</span>
				<span>
				<span class="flex-row">
					<img src="' . PORTO_OPTIONS_URI . '/builder/loop.svg' . '" style="margin-right: 10px;" />
					<span>
						<a href="' . $type_url . '" target="_blank">Add or Change Product Type</a>
						A Loop is a layout you can customize to display recurring dynamic content - like listings, posts, portfolios, products, , etc.
					</span>
				</span>
				<span class="flex-row">
					<img src="' . PORTO_OPTIONS_URI . '/builder/product.svg' . '" style="margin-right: 10px;" />
					<span>
						<a href="' . $product_url . '" target="_blank">Add or Change Single Product layout</a>
						A single product template allows you to easily design the layout and style of WooCommerce single product pages, and apply that template to various conditions that you assign.
					</span>
				</span>
				<span class="flex-row">
					<img src="' . PORTO_OPTIONS_URI . '/builder/shop.svg' . '" style="margin-right: 10px;" />
					<span>
						<a href="' . $shop_url . '" target="_blank">Add or Change Product Archive Layout</a>
						A products archive template allows you to easily design the layout and style of your WooCommerce shop page or other product archive pages - those pages that show a list of products, which may be filtered by terms such as categories, tags, etc.
					</span>
				</span>													
				</span></span>', 'porto' ), 
				array( 
					'b'    => array(),
					'span' => array(
						'class' => array(),
						'style' => array(),
					),
					'img'  => array(
						'src'   => array(),
						'style' => array(),
					),
					'a'    => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			),
			'class' => 'porto-opt-ux-builder',
		),
		array(
			'title'   => __( 'Product Swatch Mode', 'porto' ),
			'id'      => 'product_variation_display_mode',
			'type'    => 'button_set',
			'default' => 'button',
			'required' => array( 'undefined', 'equals', true ), // unknown condition
			'options' => array(
				'button' => array(
					'label' => __( 'Label, Image / Color swatch', 'porto' ),
					'hint'  => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product_variation_display_btn.jpg"/>' ),
					),
				),
				'select' => array(
					'label' => __( 'Select Box', 'porto' ),
					'hint'  => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product_variation_display_sel.jpg"/>' ),
					),
				),
			),
		),
		array(
			'id'       => 'woo-show-product-border',
			'type'     => 'switch',
			'title'    => __( 'Show Border on product images', 'porto' ),
			'subtitle' => __( 'To show border( width: 1px, color: #F4F4F4 ) on product image in all products.', 'porto' ),
			'default'  => true,
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'woo-show-product-border.gif"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'shipping-progress-bar',
			'type'     => 'switch',
			'title'    => __( 'Free Shipping Progress Bar', 'porto' ),
			'subtitle' => __( 'To display a free shipping progress bar on the website.', 'porto' ),
			'default'  => true, 
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'shipping-progress-bar.jpg"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'     => 'desc_info_product_login_link',
			'type'   => 'info',
			'title'  => __( 'Login link on Menu', 'porto' ),
			'notice' => false,
		),
		array(
			'id'       => 'menu-login-pos',
			'type'     => 'button_set',
			'title'    => __( 'Display Login / Register Link', 'porto' ),
			'subtitle' => __( 'Show the log in link, log out link(logout) in Top Navigation or Main Menu.', 'porto' ),
			'options'  => array(
				''          => __( 'None', 'porto' ),
				'top_nav'   => array(
					'label' => __( 'In Top Navigation', 'porto' ),
					'hint'  => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-login-pos-top_nav.gif"/>' ),
					),
				),
				'main_menu' => array(
					'label' => __( 'In Main Menu', 'porto' ),
					'hint'  => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-login-pos-main_menu.gif"/>' ),
					),
				),
			),
			'default'  => '',
		),
		array(
			'id'       => 'menu-enable-register',
			'type'     => 'switch',
			'title'    => __( 'Show Register Link', 'porto' ),
			'subtitle' => sprintf( __( 'You should allow to register on your site using %1$sWordPress%2$s or %3$sWooCommerce%4$s settings.', 'porto' ), '<a target="_blank" href="' . ( is_multisite() ? esc_url( network_admin_url( 'settings.php' ) ) : esc_url( admin_url( 'options-general.php' ) ) ) . '">', '</a>', '<a target="_blank" href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=account' ) ) . '">', '</a>' ),
			'required' => array( 'menu-login-pos', 'equals', array( 'top_nav', 'main_menu' ) ),
			'default'  => true,
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-enable-register.gif"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'menu-show-login-icon',
			'type'     => 'switch',
			'title'    => __( 'Show Login, Logout Icon', 'porto' ),
			'subtitle' => __( 'Show the icon for login link, logout link.' ),
			'required' => array( 'menu-login-pos', 'equals', array( 'top_nav', 'main_menu' ) ),
			'default'  => false,
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'menu-show-login-icon.jpg"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'woo-account-login-style',
			'type'     => 'button_set',
			'title'    => __( 'Login Style', 'porto' ),
			'subtitle' => __( 'Please select lightbox if you want to use login popup instead of displaying login link.', 'porto' ),
			// 'required' => array( 'menu-login-pos', 'equals', array( 'top_nav', 'main_menu' ) ),
			'default'  => '',
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'woo-account-login-style.jpg"/>' ),
			),
			'options'  => array(
				''     => __( 'Lightbox', 'porto' ),
				'link' => __( 'Link', 'porto' ),
			),
		),
		array(
			'id'     => 'desc_info_product_label',
			'type'   => 'info',
			'title'  => __( 'Product Labels', 'porto' ),
			'notice' => false,
		),
		array(
			'id'        => 'product-stock',
			'type'      => 'switch',
			'title'     => __( 'Show "Out of stock" Status', 'porto' ),
			'subtitle'  => __( 'To show "Out of stock" text for the out-of-stock products.', 'porto' ),
			'default'   => true,
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-stock.jpg"/>' ),
			),
			'on'        => __( 'Yes', 'porto' ),
			'off'       => __( 'No', 'porto' ),
			'transport' => 'postMessage',
		),
		array(
			'id'       => 'product-labels',
			'type'     => 'button_set',
			'title'    => __( 'Select labels to display', 'porto' ),
			'subtitle' => __( 'Offers "Featured", "Sale" and "New" lables for Product', 'porto' ),
			'multi'    => true,
			'default'  => array( 'hot', 'sale' ),
			'options'  => array(
				'hot' => array(
					'label' => __( 'Hot', 'porto' ),
					'hint'  => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-labels-hot.gif"/>' ),
					),
				),
				'sale' => array(
					'label' => __( 'Sale', 'porto' ),
					'hint'  => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-labels-sale.gif"/>' ),
					),
				),
				'new' => array(
					'label' => __( 'New', 'porto' ),
					'hint'  => array(
						'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-labels-new.gif"/>' ),
					),
				),
			),
		),
		array(
			'id'       => 'product-hot-label',
			'type'     => 'text',
			'required' => array( 'product-labels', 'contains', 'hot' ),
			'title'    => __( '"Hot" Text', 'porto' ),
			'subtitle' => __( 'This will be displayed in the featured product.', 'porto' ),
			'default'  => '',
		),
		array(
			'id'       => 'product-sale-label',
			'type'     => 'text',
			'required' => array( 'product-labels', 'contains', 'sale' ),
			'title'    => __( '"Sale" Text', 'porto' ),
			'subtitle' => __( 'This will be displayed in the product on sale.', 'porto' ),
			'default'  => '',
		),
		array(
			'id'       => 'product-sale-percent',
			'type'     => 'switch',
			'required' => array( 'product-labels', 'contains', 'sale' ),
			'title'    => __( 'Show Saved Sale Price Percentage', 'porto' ),
			'subtitle' => __( 'Select "No" to display "Sale" text instead of sale percentage.', 'porto' ),
			'default'  => true, 
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-sale-percent.jpg"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'product-new-label',
			'type'     => 'text',
			'required' => array( 'product-labels', 'contains', 'new' ),
			'title'    => __( 'New Product Label', 'porto' ),
			'subtitle' => __( 'This will be displayed in the new product.', 'porto' ),
			'default'  => '',
		),
		array(
			'id'       => 'product-new-days',
			'type'     => 'slider',
			'title'    => __( 'New Product Period (days)', 'porto' ),
			'required' => array( 'product-labels', 'contains', 'new' ),
			'subtitle' => __( 'The Products which were created over this option will be displayed', 'porto' ),
			'default'  => 7,
			'min'      => 1,
			'max'      => 100,
		),
		array(
			'id'     => 'desc_info_sale_popup',
			'type'   => 'info',
			'title'  => __( 'Sales Popup : Show products popup in all page.', 'porto' ),
			'notice' => false,
		),
		array(
			'id'        => 'woo-sales-popup',
			'type'      => 'select',
			'title'     => __( 'Sales Popup Content', 'porto' ),
			'subtitle'  => __( 'Select which products you want to show in sales popup.', 'porto' ),
			'default'   => '',
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'woo-sales-popup.jpg"/>' ),
			),
			'options'   => array(
				''         => __( 'Do not show', 'porto' ),
				'real'     => __( 'Recent sale products', 'porto' ),
				'popular'  => __( 'Popular products', 'porto' ),
				'rating'   => __( 'Top rated products', 'porto' ),
				'sale'     => __( 'Sale products', 'porto' ),
				'featured' => __( 'Featured products', 'porto' ),
				'recent'   => __( 'Recent products', 'porto' ),
			),
			'transport' => 'refresh',
		),
		array(
			'id'        => 'woo-sales-popup-title',
			'type'      => 'text',
			'title'     => __( 'Popup Title', 'porto' ),
			'default'   => __( 'Someone just purchased', 'porto' ),
			'subtitle'  => __( 'This will show at top of popup dialog.', 'porto' ),
			'required'  => array( 'woo-sales-popup', '!=', '' ),
			'transport' => 'refresh',
		),
		array(
			'id'        => 'woo-sales-popup-count',
			'type'      => 'slider',
			'title'     => __( 'Products Count', 'porto' ),
			'required'  => array( 'woo-sales-popup', '!=', '' ),
			'default'   => 10,
			'min'       => 1,
			'max'       => 30,
			'transport' => 'refresh',
		),
		array(
			'id'        => 'woo-sales-popup-start-delay',
			'type'      => 'slider',
			'title'     => __( 'Start Delay(seconds)', 'porto' ),
			'subtitle'  => __( 'Change delay time to show the first popup after page loading.', 'porto' ),
			'required'  => array( 'woo-sales-popup', '!=', '' ),
			'default'   => 10,
			'min'       => 1,
			'max'       => 30,
			'transport' => 'refresh',
		),
		array(
			'id'        => 'woo-sales-popup-interval',
			'type'      => 'slider',
			'title'     => __( 'Interval(seconds)', 'porto' ),
			'subtitle'  => __( 'Change duration between popups. Each sales popup will be disappeared after 4 seconds.', 'porto' ),
			'required'  => array( 'woo-sales-popup', '!=', '' ),
			'default'   => 60,
			'min'       => 1,
			'max'       => 600,
			'transport' => 'refresh',
		),
		array(
			'id'        => 'woo-sales-popup-mobile',
			'type'      => 'switch',
			'title'     => __( 'Enable on Mobile', 'porto' ),
			'subtitle'  => __( 'Do you want to enable sales popup on mobile?', 'porto' ),
			'required'  => array( 'woo-sales-popup', '!=', '' ),
			'default'   => true,
			'on'        => __( 'Yes', 'porto' ),
			'off'       => __( 'No', 'porto' ),
			'transport' => 'refresh',
		),
		array(
			'id'     => 'desc_info_pre_order',
			'type'   => 'info',
			'title'  => __( 'Pre-Order', 'porto' ),
			'notice' => false,
		),
		array(
			'id'        => 'woo-pre-order',
			'type'      => 'switch',
			'title'     => __( 'Enable Pre-Order', 'porto' ),
			'subtitle'  => __( 'Pre-Order functionality offers customers the chance to purchase the unavailable products and provide them only after they are officially on sale.', 'porto' ),
			'desc'      => __( 'Before selecting "ON", You should check "pre-order" meta option of WooCoommerce Product.', 'porto' ),
			'transport' => 'refresh',
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'woo-pre-order.gif"/>' ),
			),
		),
		array(
			'id'        => 'woo-pre-order-label',
			'type'      => 'text',
			'title'     => __( 'Pre-Order Label', 'porto' ),
			'subtitle'  => __( 'This text will be used on \'Add to Cart\' button.', 'porto' ),
			'required'  => array( 'woo-pre-order', 'equals', true ),
			'transport' => 'refresh',
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'woo-pre-order-label.gif"/>' ),
			),
		),
		array(
			'id'        => 'woo-pre-order-msg-date',
			'type'      => 'text',
			'title'     => __( 'Pre-Order Availability Date Text', 'porto' ),
			/* translators: available date */
			'subtitle'  => __( 'ex: Available date: %1$s (%1$s will be replaced with available date.)', 'porto' ),
			'required'  => array( 'woo-pre-order', 'equals', true ),
			'transport' => 'refresh',
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'woo-pre-order-msg-date.gif"/>' ),
			),
		),
		array(
			'id'          => 'woo-pre-order-msg-nodate',
			'type'        => 'text',
			'title'       => __( 'Pre-Order No Date Message', 'porto' ),
			'subtitle'    => __( 'This text will be used for the product without Available Date.', 'porto' ),
			'placeholder' => __( 'Available soon', 'porto' ),
			'required'    => array( 'woo-pre-order', 'equals', true ),
			'transport'   => 'refresh',
		),
	),
);

if ( $this->legacy_mode ) {
	$this->sections[] = array(
		'icon_class' => 'icon',
		'subsection' => true,
		'title'      => __( 'Product Archives', 'porto' ),
		'fields'     => array(
			array(
				'id'    => 'desc_info_shop',
				'type'  => 'info',
				'desc'  => wp_kses(
					/* translators: %s: Builder url */
					sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Product Archive</a> & <a href="%2$s" target="_blank">Product Type</a> Builders help you to develop shop page easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $shop_url, $type_url ),
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
				'id'      => 'desc_info_go_shop_sidebar',
				'type'    => 'info',
				'desc'    => wp_kses(
					sprintf(
						/* translators: %s: widgets url */
						__( 'You can control the Woo Category sidebar and <a  href="%1$s" target="_blank">secondary</a> sidebar in <a href="%2$s" target="_blank">here</a>.', 'porto' ),
						esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) ),
						esc_url( admin_url( 'widgets.php' ) )
					),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				),
				'class'   => 'pt-always-visible',
			),
			array(
				'id'       => 'product-archive-layout',
				'type'     => 'image_select',
				'title'    => __( 'Page Layout', 'porto' ),
				'subtitle' => __( 'Shop Page Layout', 'porto' ),
				'options'  => $page_layouts,
				'default'  => 'left-sidebar',
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'product-archive-sidebar2',
				'type'     => 'select',
				'title'    => __( 'Select Sidebar 2', 'porto' ),
				'required' => array( 'product-archive-layout', 'equals', $both_sidebars ),
				'data'     => 'sidebars',
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'category-ajax',
				'type'     => 'switch',
				'title'    => __( 'Enable Ajax Filter', 'porto' ),
				'subtitle' => __( 'Filter all products including default pagination by Ajax in shop pages. "Load More" and "Infinite Scroll" pagination types don\'t depend on this option.', 'porto' ),
				'default'  => false,
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'category-ajax.gif"/>' ),
				),
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
				'class'    => 'pt-always-visible',
			),
			array(
				'id'       => 'product-archive-filter-layout',
				'type'     => 'image_select',
				'class'    => 'pt-always-visible',
				'title'    => __( 'Filter Layout', 'porto' ),
				'subtitle' => __( 'Products filtering layout in shop pages.', 'porto' ),
				'desc'     => wp_kses(
					__( '<span style="color: red">Horizontal filter</span> is shown with <a target="_blank" href="' . esc_url( admin_url( 'widgets.php' ) ) . '">Shop Horizontal Widget</a> in Appearance > Widgets.<br/>If you use Shop Builder, filter sidebar should be shown with <span style="color: red">Filter Toggle</span> widget.', 'porto' ),
					array(
						'span' => array(
							'style' => array(),
						),
						'br'   => array(),
						'a'    => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				),
				'default'  => '',
				'options'  => array(
					''            => array(
						'title' => __( 'Default', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/shop-default.svg',
					),
					'horizontal'  => array(
						'title' => __( 'Sidebar with Toggle', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/shop-horizontal1.svg',
					),
					'horizontal2' => array(
						'title' => __( 'Horizontal filters', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/shop-horizontal2.svg',
					),
					'offcanvas'   => array(
						'title' => __( 'Off Canvas', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/shop-offcanvas.svg',
					),
				),
			),
			array(
				'id'       => 'product-infinite',
				'type'     => 'button_set',
				'title'    => __( 'Pagination style', 'porto' ),
				'default'  => '',
				'subtitle' => __( 'Choose a type for the pagination.', 'porto' ),
				'options'  => array(
					'' => array(
						'label' => __( 'Default', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-infinite.gif"/>' ),
						),
					),
					'load_more' => array(
						'label' => __( 'Load More', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-infinite-load_more.gif"/>' ),
						),
					),
					'infinite_scroll' => array(
						'label' => __( 'Infinite Scroll', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-infinite-infinite_scroll.gif"/>' ),
						),
					),
				),
			),
			array(
				'id'        => 'category-item',
				'type'      => 'text',
				'title'     => __( 'Products per page (shop products count)', 'porto' ),
				'subtitle'  => __( 'Comma separated list of product counts. If use shop builder, default value is \'Count(per page)\' option on Type Builder Archives Widget.', 'porto' ),
				'default'   => '12,24,36',
				'hint'      => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'category-item.gif"/>' ),
				),
				'transport' => 'postMessage',
				'class'     => 'pt-always-visible',
			),
			array(
				'id'        => 'category-view-mode',
				'type'      => 'button_set',
				'title'     => __( 'View Mode', 'porto' ),
				'subtitle'      => __( 'Products display mode in non-builder shop pages', 'porto' ),
				'options'   => porto_ct_category_view_mode( true ),
				'default'   => '',
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'shop-product-cols',
				'type'      => 'slider',
				'title'     => __( 'Shop Page Product Columns', 'porto' ),
				'subtitle'      => __( 'Controls the number of columns to display in non-builder shop page.', 'porto' ),
				'default'   => 3,
				'min'       => 2,
				'max'       => 8,
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'shop-product-cols-mobile',
				'type'      => 'slider',
				'title'     => __( 'Shop Page Product Columns on Mobile ( < 576px )', 'porto' ),
				'subtitle'      => __( 'Controls the number of columns to display for mobile in non-builder shop page.', 'porto' ),
				'default'   => 2,
				'min'       => 1,
				'max'       => 3,
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-cols',
				'type'      => 'slider',
				'title'     => __( 'Category Product Columns', 'porto' ),
				'subtitle'  => __( 'Controls the number of columns to display in non-builder category page.', 'porto' ),
				'default'   => 3,
				'min'       => 2,
				'max'       => 8,
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-cols-mobile',
				'type'      => 'slider',
				'title'     => __( 'Category Product Columns on Mobile ( < 576px )', 'porto' ),
				'subtitle'  => __( 'Controls the number of columns to display for mobile in non-builder category page.', 'porto' ),
				'default'   => 2,
				'min'       => 1,
				'max'       => 3,
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'cat-view-type',
				'type'      => 'button_set',
				'title'     => __( 'Category Content Position', 'porto' ),
				'subtitle'  => __( 'The position of content section which contains title, description and product count in a product category', 'porto' ),
				'default'   => '',
				'options'   => array(
					''  => array(
						'label' => __( 'Inner Bottom Left', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'cat-view-type.jpg"/>' ),
						),
					),
					'2' => array(
						'label' => __( 'Outside Center', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'cat-view-type-2.jpg"/>' ),
						),
					),
				),
				'transport' => 'postMessage',
			),
			array(
				'id'     => 'desc_info_product_layout',
				'type'   => 'info',
				'title'  => __( 'Product Layout Options', 'porto' ),
				'notice' => false,
			),
			array(
				'id'        => 'category-addlinks-convert',
				'type'      => 'switch',
				'title'     => esc_html__( 'Change <a> Tag to <span>', 'porto' ),
				'subtitle'      => esc_html__( 'To use <span> for the add to cart, quickview and add to wishlist buttons in shop pages.', 'porto' ),
				'default'   => false,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'category-addlinks-pos',
				'type'      => 'image_select',
				'title'     => __( 'Product Layout', 'porto' ),
				'subtitle'      => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto' ),
				'options'   => array(
					'default'              => array(
						'title' => __( 'Default', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_default.jpg',
					),
					'onhover'              => array(
						'title' => __( 'Default - Show Links on Hover', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_default.jpg',
					),
					'outimage_aq_onimage'  => array(
						'title' => __( 'Add to Cart, Quick View On Image', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_outimage_aq_onimage.jpg',
					),
					'outimage_aq_onimage2' => array(
						'title' => __( 'Add to Cart, Quick View On Image with Padding', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_outimage_aq_onimage2.jpg',
					),
					'awq_onimage'          => array(
						'title' => __( 'Link On Image', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_awq_onimage.jpg',
					),
					'outimage'             => array(
						'title' => __( 'Out of Image', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_outimage.jpg',
					),
					'onimage'              => array(
						'title' => __( 'On Image', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_onimage.jpg',
					),
					'onimage2'             => array(
						'title' => __( 'On Image with Overlay 1', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_onimage2.jpg',
					),
					'onimage3'             => array(
						'title' => __( 'On Image with Overlay 2', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_onimage3.jpg',
					),
					'quantity'             => array(
						'title' => __( 'Show Quantity Input', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_quantity_input.jpg',
					),
				),
				'default'   => 'default',
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'add-to-cart-notification',
				'type'      => 'image_select',
				'class'     => 'pt-always-visible',
				'title'     => __( 'Add to Cart Notification Type', 'porto' ),
				'subtitle'      => __( 'Select the notification type whenever product is added to cart.', 'porto' ),
				'options'   => array(
					''  => array(
						'title' => __( 'Style 1', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/addcart-1.jpg',
					),
					'2' => array(
						'title' => __( 'Style 2', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/addcart-2.jpg',
					),
					'3' => array(
						'title' => __( 'Style 3', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/addcart-3.jpg',
					),
				),
				'default'   => '3',
				'transport' => 'postMessage',
			),
			array(
				'id'     => 'desc_info_shop_loop',
				'type'   => 'info',
				'desc'   => wp_kses(
					__( '<b>Show / Hide Elements:</b> If you use <span>type builder</span>, below options <span>aren\'t</span> necessary. Please use the options of builder widgets.', 'porto' ),
					array(
						'span' => array(),
						'b'    => array(),
					)
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),
			array(
				'id'        => 'category-image-hover',
				'type'      => 'switch',
				'title'     => __( 'Show image on hover', 'porto' ),
				'subtitle'      => __( 'If enabled, the first image of product gallery will be displayed on product hover.', 'porto' ),
				'default'   => true,
				'hint'      => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'category-image-hover.gif"/>' ),
				),
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'show_swatch',
				'type'      => 'switch',
				'title'     => __( 'Show product swatch', 'porto' ),
				'subtitle'  => __( 'To show swatch on product loop.', 'porto' ),
				'default'   => false,
				'hint'      => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'show_swatch.jpg"/>' ),
				),
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-categories',
				'type'      => 'switch',
				'title'     => __( 'Show Categories', 'porto' ),
				'subtitle'      => __( 'To show categories on product loop.', 'porto' ),
				'default'   => true,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-review',
				'type'      => 'switch',
				'title'     => __( 'Show Reviews', 'porto' ),
				'subtitle'      => __( 'To show reviews on product loop.', 'porto' ),
				'default'   => true,
				'hint'      => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-preview.gif"/>' ),
                ),
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-price',
				'type'      => 'switch',
				'title'     => __( 'Show Price', 'porto' ),
				'subtitle'      => __( 'To show price on product loop.', 'porto' ),
				'default'   => true,
				'hint'      => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-price.gif"/>' ),
                ),
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-desc',
				'type'      => 'switch',
				'title'     => __( 'Show Description', 'porto' ),
				'desc'      => __( 'To show description on product loop.', 'porto' ),
				'subtitle'  => __( 'This option works except product list type.', 'porto' ),
				'default'   => false,
				'hint'      => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-desc.gif"/>' ),
                ),
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-wishlist',
				'type'      => 'switch',
				'title'     => __( 'Show Wishlist', 'porto' ),
				'subtitle'      => __( 'To show wishlist on product loop.', 'porto' ),
				'default'   => true,
				'hint'      => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-wishlist.gif"/>' ),
                ),
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-quickview',
				'type'      => 'switch',
				'title'     => __( 'Show Quick View', 'porto' ),
				'subtitle'      => __( 'To show quickview on product loop.', 'porto' ),
				'default'   => true,
				'hint'      => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-quickview.jpg"/>' ),
                ),
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-quickview-label',
				'type'      => 'text',
				'required'  => array( 'product-quickview', 'equals', true ),
				'title'     => __( '"Quick View" Text', 'porto' ),
				'subtitle'      => __( 'Shows this text instead of "Quick View".', 'porto' ),
				'default'   => '',
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-compare',
				'type'      => 'switch',
				'title'     => __( 'Show Compare', 'porto' ),
				'subtitle'      => __( 'To show compare on product loop.', 'porto' ),
				'default'   => true,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-compare-title',
				'type'      => 'text',
				'title'     => __( 'Compare Popup Title', 'porto' ),
				'subtitle'      => __( 'Shows this text at the compare popup.', 'porto' ),
				'default'   => __( 'You just added to compare list.', 'porto' ),
				'required'  => array( 'product-compare', '!=', false ),
				'transport' => 'refresh',
			),
		),
	);
	$this->sections[] = array(
		'icon_class' => 'icon',
		'subsection' => true,
		'title'      => __( 'Single Product', 'porto' ),
		'transport'  => 'postMessage',
		'fields'     => array(
			array(
				'id'    => 'desc_info_single_product',
				'type'  => 'info',
				'desc'  => wp_kses(
					/* translators: %s: Builder url */
					sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Single Product</a> & <a href="%2$s" target="_blank">Product Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $product_url, $type_url ),
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
				'id'      => 'desc_info_go_product_sidebar',
				'type'    => 'info',
				'class'   => 'pt-always-visible',
				'desc'    => wp_kses(
					sprintf(
						/* translators: %s: widgets url */
						__( 'You can control the Woo Product sidebar and <a  href="%1$s" target="_blank">secondary</a> sidebar in <a href="%2$s" target="_blank">here</a>.', 'porto' ),
						esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) ),
						esc_url( admin_url( 'widgets.php' ) )
					),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				),
			),
			array(
				'id'        => 'product-single-layout',
				'type'      => 'image_select',
				'title'     => __( 'Page Layout', 'porto' ),
				'subtitle'  => __( 'Product Page Layout', 'porto' ),
				'options'   => $page_layouts,
				'default'   => 'right-sidebar',
				'transport' => 'refresh',
				'class'     => 'pt-always-visible',
			),
			array(
				'id'       => 'product-single-sidebar2',
				'type'     => 'select',
				'title'    => __( 'Select Sidebar 2', 'porto' ),
				'required' => array( 'product-single-layout', 'equals', $both_sidebars ),
				'data'     => 'sidebars',
				'class'    => 'pt-always-visible',
			),
			array(
				'id'        => 'product-single-content-layout',
				'type'      => 'image_select',
				'title'     => __( 'Product Layout', 'porto' ),
				'subtitle'  => __( 'Individual product has the meta option for <b>product layout</b>', 'porto' ),
				'options'   => array(
					'default'                => array(
						'title' => __( 'Default', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/default.jpg',
					),
					'extended'               => array(
						'title' => __( 'Extended', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/extended.jpg',
					),
					'full_width'             => array(
						'title' => __( 'Full Width', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/full_width.jpg',
					),
					'grid'                   => array(
						'title' => __( 'Grid Images', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/grid.jpg',
					),
					'sticky_info'            => array(
						'title' => __( 'Sticky Info', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/sticky_info.jpg',
					),
					'sticky_both_info'       => array(
						'title' => __( 'Sticky Left & Right Info', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/sticky_info_both.jpg',
					),
					'transparent'            => array(
						'title' => __( 'Transparent Images', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/transparent.jpg',
					),
					'centered_vertical_zoom' => array(
						'title' => __( 'Centered Vertical Zoom', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/centered_vertical_zoom.jpg',
					),
					'left_sidebar'           => array(
						'title' => __( 'Left Sidebar', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/left_sidebar.jpg',
					),
					'builder'                => array(
						'title' => __( 'Custom', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/custom.jpg',
					),
				),
				'default'   => 'default',
				'transport' => 'refresh',
			),
			array(
				'id'       => 'product-single-content-builder',
				'type'     => 'select',
				'title'    => __( 'Custom Product Layout', 'porto' ),
				'subtitle' => __( 'We recommend to use <strong>Display Condition</strong> when creating single product builder instead of this option. This option is overrided by <strong>Display Condition</strong>.', 'porto' ),
				'desc'     => __( 'Please select a product layout. You can create a product layout in <strong>Porto / Templates Builder / Single Product / Add New</strong>.', 'porto' ),
				'options'  => $product_layouts,
				'default'  => '',
				'required' => array( 'product-single-content-layout', 'equals', 'builder' ),
			),
			array(
				'id'        => 'product-content_bottom',
				'type'      => 'text',
				'title'     => __( 'Content Bottom Block', 'porto' ),
				'subtitle'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
				'transport' => 'refresh',
			),
			/*array(
			'id'=>'product-ajax-addcart-button',
			'type' => 'switch',
			'title' => __( 'Enable AJAX add to cart button', 'porto' ),
			'default' => true,
			'on' => __('Yes', 'porto'),
			'off' => __('No', 'porto'),
			),*/
			array(
				'id'        => 'product-sticky-addcart',
				'type'      => 'image_select',
				'title'     => __( 'Sticky add to cart section', 'porto' ),
				'desc'      => __( 'Select the position to display sticky add to cart section in single product page.', 'porto' ),
				'subtitle'  => __( 'This option can be overrided by <strong>Sticky Add to Cart Widget</strong>.', 'porto' ),
				'options'   => array(
					''       => array(
						'title' => __( 'None', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sticky-cart-none.svg',
					),
					'top'    => array(
						'title' => __( 'At the Top', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sticky-cart-top.svg',
					),
					'bottom' => array(
						'title' => __( 'At the Bottom', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/sticky-cart-bottom.svg',
					),
				),
				'default'   => '',
				'transport' => 'refresh',
			),
			array(
				'id'     => 'desc_info_sp_tab',
				'type'   => 'info',
				'desc'   => wp_kses(
					__( '<b>Product Tab</b>', 'porto' ),
					array(
						'span' => array(),
						'b'    => array(),
					)
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),
			array(
				'id'       => 'product-tab-close-mobile',
				'type'     => 'switch',
				'title'    => __( 'Collapse the accordion on mobile at first?', 'porto' ),
				'subtitle' => __( 'Enable this option to collapse the accordion on mobile.', 'porto' ),
				'default'  => false,
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'      => 'product-tabs-pos',
				'type'    => 'button_set',
				'title'   => __( 'Tabs Position', 'porto' ),
				'subtitle'    => __( 'Select the position of tab where to put.', 'porto' ),
				'options' => array(
					''      => array(
						'label' => __( 'Default', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-tabs-pos.jpg"/>' ),
						),
					),
					'below' => array(
						'label' => __( 'Below Price & Short Description', 'porto' ),
						'hint'  => array(
							'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-tabs-pos-below.jpg"/>' ),
						),
					),
				),
				'default' => '',
			),
			array(
				'id'       => 'product-custom-tabs-count',
				'type'     => 'text',
				'title'    => __( 'Additional Tabs Count (Product Custom Tab)', 'porto' ),
				'subtitle' => __( 'You can input the tab content in meta fields of "Edit Product".', 'porto' ),
				'default'  => '2',
			),
			array(
				'id'       => 'product-tab-title',
				'type'     => 'text',
				'title'    => __( 'Global Product Custom Tab Title', 'porto' ),
				'subtitle' => __( 'Input the title of Product Custom Tab.', 'porto' ),
				'default'  => '',
			),
			array(
				'id'       => 'product-tab-block',
				'type'     => 'text',
				'title'    => __( 'Global Product Custom Tab Block', 'porto' ),
				'subtitle' => __( 'This block will be shown in the Custom Tab Content.', 'porto' ),
				'desc'     => __( 'Input block slug name', 'porto' ),
				'default'  => '',
			),
			array(
				'id'       => 'product-tab-priority',
				'type'     => 'text',
				'title'    => __( 'Global Product Custom Tab Priority', 'porto' ),
				'subtitle' => __( 'Input the custom tab priority. (Description: 10, Additional Information: 20, Reviews: 30)', 'porto' ),
				'default'  => '60',
			),
			array(
				'id'     => 'desc_info_sp_show',
				'type'   => 'info',
				'desc'   => wp_kses(
					__( '<b>Show / Hide Elements:</b> If you use <span>single product builder</span>, below options <span>aren\'t</span> necessary. Please use the options of builder widgets.', 'porto' ),
					array(
						'span' => array(),
						'b'    => array(),
					)
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),
			array(
				'id'      => 'product-nav',
				'type'    => 'switch',
				'title'   => __( 'Show Prev/Next Product', 'porto' ),
				'subtitle'    => __( 'To show Prev/Next navigation.', 'porto' ),
				'default' => true,
				'hint'    => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-nav.gif"/>' ),
                ),
				'on'      => __( 'Yes', 'porto' ),
				'off'     => __( 'No', 'porto' ),
			),
			array(
				'id'      => 'product-short-desc',
				'type'    => 'switch',
				'title'   => __( 'Show Short Description', 'porto' ),
				'subtitle'    => __( 'This is available for Default Product Layouts.', 'porto' ),
				'default' => true,
				'hint'    => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-short-desc.jpg"/>' ),
                ),
				'on'      => __( 'Yes', 'porto' ),
				'off'     => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'product-metas',
				'type'     => 'button_set',
				'title'    => __( 'Show Product Meta', 'porto' ),
				'subtitle' => __( 'Select product metas to show like product sku, categories, tags, product brand, gtin and so on.', 'porto' ),
				'multi'    => true,
				'options'  => array(
					'sku'              => __( 'SKU', 'porto' ),
					'cats'             => __( 'Categories', 'porto' ),
					'tags'             => __( 'Tags', 'porto' ),
					'global_unique_id' => __( 'GTIN, UPC, EAN or ISBN.', 'woocommerce' ),
					'brand'            => __( 'Brand', 'woocommerce' ),
					'-'                => 'None',
				),
				'hint'      => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-metas.gif"/>' ),
                ),
				'default'  => array( 'sku', 'cats', 'tags', 'brand', '-' ),
			),
			array(
				'id'       => 'product-attr-desc',
				'type'     => 'switch',
				'title'    => __( 'Show Description of Selected Attribute', 'porto' ),
				'subtitle' => __( 'To show description if it exists when selecting product attribute in the variations.', 'porto' ),
				'default'  => false,
				'hint'     => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-attr-desc.gif"/>' ),
                ),
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'      => 'product-share',
				'type'    => 'switch',
				'title'   => __( 'Show Social Share Links', 'porto' ),
				'subtitle'    => __( 'To show Social Links.', 'porto' ),
				'default' => true,
				'on'      => __( 'Yes', 'porto' ),
				'off'     => __( 'No', 'porto' ),
			),
			array(
				'id'     => 'desc_info_single_product_related',
				'type'   => 'info',
				'desc'   => wp_kses(
					__( '<b>Related Products in Single Product</b>', 'porto' ),
					array(
						'span' => array(),
						'b'    => array(),
					)
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),
			array(
				'id'      => 'product-related',
				'type'    => 'switch',
				'title'   => __( 'Show Related Products', 'porto' ),
				'subtitle'    => __( 'To show related products in the single product page.', 'porto' ),
				'default' => true,
				'on'      => __( 'Yes', 'porto' ),
				'off'     => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'product-related-count',
				'type'     => 'text',
				'required' => array( 'product-related', 'equals', true ),
				'title'    => __( 'Related Products Count', 'porto' ),
				'default'  => '10',
			),
			array(
				'id'       => 'product-related-cols',
				'type'     => 'button_set',
				'required' => array( 'product-related', 'equals', true ),
				'title'    => __( 'Related Product Columns', 'porto' ),
				'options'  => porto_ct_related_product_columns(),
				'default'  => '4',
			),
			array(
				'id'     => 'desc_info_single_product_upsell',
				'type'   => 'info',
				'desc'   => wp_kses(
					__( '<b>Upsell Products in Single Product</b>', 'porto' ),
					array(
						'span' => array(),
						'b'    => array(),
					)
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),
			array(
				'id'      => 'product-upsells',
				'type'    => 'switch',
				'title'   => __( 'Show Up Sells', 'porto' ),
				'subtitle'    => __( 'To show Upsell products in the cart page.', 'porto' ),
				'default' => true,
				'on'      => __( 'Yes', 'porto' ),
				'off'     => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'product-upsells-count',
				'type'     => 'text',
				'required' => array( 'product-upsells', 'equals', true ),
				'title'    => __( 'Up Sells Count', 'porto' ),
				'default'  => '10',
			),
			array(
				'id'       => 'product-upsells-cols',
				'type'     => 'button_set',
				'required' => array( 'product-upsells', 'equals', true ),
				'title'    => __( 'Up Sells Product Columns', 'porto' ),
				'options'  => porto_ct_related_product_columns(),
				'default'  => '4',
			),
		),
	);
} else {
	$this->sections[] = array(
		'icon_class' => 'icon',
		'subsection' => true,
		'title'      => __( 'Product Archives', 'porto' ),
		'fields'     => array(
			array(
				'id'    => 'desc_info_shop',
				'type'  => 'info',
				'desc'  => wp_kses(
					/* translators: %s: Builder url */
					sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Product Archive</a> & <a href="%2$s" target="_blank">Product Type</a> Builders help you to develop shop page easily.', 'porto' ), $shop_url, $type_url ),
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
				'id'      => 'desc_info_go_shop_sidebar',
				'type'    => 'info',
				'desc'    => wp_kses(
					sprintf(
						/* translators: %s: widgets url */
						__( 'You can control the Woo Category sidebar and <a  href="%1$s" target="_blank">secondary</a> sidebar in <a href="%2$s" target="_blank">here</a>.', 'porto' ),
						esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) ),
						esc_url( admin_url( 'widgets.php' ) )
					),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				),
			),
			array(
				'id'       => 'product-archive-layout',
				'type'     => 'image_select',
				'title'    => __( 'Page Layout', 'porto' ),
				'subtitle' => __( 'Shop Page Layout', 'porto' ),
				'options'  => $page_layouts,
				'default'  => 'left-sidebar',
			),
			array(
				'id'       => 'product-archive-sidebar2',
				'type'     => 'select',
				'title'    => __( 'Select Sidebar 2', 'porto' ),
				'required' => array( 'product-archive-layout', 'equals', $both_sidebars ),
				'data'     => 'sidebars',
			),
			array(
				'id'       => 'category-ajax',
				'type'     => 'switch',
				'title'    => __( 'Enable Ajax Filter', 'porto' ),
				'subtitle' => __( 'Filter all products including default pagination by Ajax in shop pages. "Load More" and "Infinite Scroll" pagination types don\'t depend on this option.', 'porto' ),
				'default'  => false,
				'hint'     => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'category-ajax.gif"/>' ),
				),
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'product-archive-filter-layout',
				'type'     => 'image_select',
				'title'    => __( 'Filter Layout', 'porto' ),
				'subtitle' => __( 'Products filtering layout in shop pages.', 'porto' ),
				'desc'     => wp_kses(
					__( '<span style="color: red">Horizontal filter</span> is shown with <a target="_blank" href="' . esc_url( admin_url( 'widgets.php' ) ) . '">Shop Horizontal Widget</a> in Appearance > Widgets.<br/>If you use Shop Builder, filter sidebar should be shown with <span style="color: red">Filter Toggle</span> widget.', 'porto' ),
					array(
						'span' => array(
							'style' => array(),
						),
						'br'   => array(),
						'a'    => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				),
				'default'  => '',
				'options'  => array(
					''            => array(
						'title' => __( 'Default', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/shop-default.svg',
					),
					'horizontal'  => array(
						'title' => __( 'Sidebar with Toggle', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/shop-horizontal1.svg',
					),
					'horizontal2' => array(
						'title' => __( 'Horizontal filters', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/shop-horizontal2.svg',
					),
					'offcanvas'   => array(
						'title' => __( 'Off Canvas', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/svg/shop-offcanvas.svg',
					),
				),
			),
			array(
				'id'        => 'category-item',
				'type'      => 'text',
				'title'     => __( 'Products per page (shop products count)', 'porto' ),
				'subtitle'  => __( 'Comma separated list of product counts. If use shop builder, default value is \'Count(per page)\' option on Type Builder Archives Widget.', 'porto' ),
				'default'   => '12,24,36',
				'hint'      => array(
					'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'category-item.gif"/>' ),
				),
				'transport' => 'postMessage',
			),
			array(
				'id'     => 'desc_info_product_layout',
				'type'   => 'info',
				'title'  => __( 'Product Layout Options', 'porto' ),
				'notice' => false,
			),
			array(
				'id'        => 'category-addlinks-convert',
				'type'      => 'switch',
				'title'     => esc_html__( 'Change <a> Tag to <span>', 'porto' ),
				'subtitle'  => esc_html__( 'To use <span> for the add to cart, quickview and add to wishlist buttons in shop pages.', 'porto' ),
				'default'   => false,
				'on'        => __( 'Yes', 'porto' ),
				'off'       => __( 'No', 'porto' ),
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'add-to-cart-notification',
				'type'      => 'image_select',
				'title'     => __( 'Add to Cart Notification Type', 'porto' ),
				'subtitle'  => __( 'Select the notification type whenever product is added to cart.', 'porto' ),
				'options'   => array(
					''  => array(
						'title' => __( 'Style 1', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/addcart-1.jpg',
					),
					'2' => array(
						'title' => __( 'Style 2', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/addcart-2.jpg',
					),
					'3' => array(
						'title' => __( 'Style 3', 'porto' ),
						'img'   => PORTO_OPTIONS_URI . '/products/addcart-3.jpg',
					),
				),
				'default'   => '3',
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-quickview-label',
				'type'      => 'text',
				'title'     => __( '"Quick View" Text', 'porto' ),
				'subtitle'  => __( 'Shows this text instead of "Quick View".', 'porto' ),
				'default'   => '',
				'transport' => 'postMessage',
			),
			array(
				'id'        => 'product-compare-title',
				'type'      => 'text',
				'title'     => __( 'Compare Popup Title', 'porto' ),
				'subtitle'  => __( 'Shows this text at the compare popup.', 'porto' ),
				'default'   => __( 'You just added to compare list.', 'porto' ),
				'transport' => 'refresh',
			),
		),
	);
	$this->sections[] = array(
		'icon_class' => 'icon',
		'subsection' => true,
		'title'      => __( 'Single Product', 'porto' ),
		'transport'  => 'postMessage',
		'fields'     => array(
			array(
				'id'    => 'desc_info_single_product',
				'type'  => 'info',
				'desc'  => wp_kses(
					/* translators: %s: Builder url */
					sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single Product</a> & <a href="%2$s" target="_blank">Product Type</a> Builders help you to develop your site easily. <br/>You can enable the <b>sticky cart</b> with "Sticky Add to Cart" Widget on Single Product builder.', 'porto' ), $product_url, $type_url ),
					array(
						'strong' => array(),
						'b'      => array(),
						'a'      => array(
							'href'   => array(),
							'target' => array(),
							'class'  => array(),
						),
						'br'      => array(),
					)
				),
				'class' => 'porto-important-note',
			),
			array(
				'id'      => 'desc_info_go_product_sidebar',
				'type'    => 'info',
				'desc'    => wp_kses(
					sprintf(
						/* translators: %s: widgets url */
						__( 'You can control the Woo Product sidebar and <a  href="%1$s" target="_blank">secondary</a> sidebar in <a href="%2$s" target="_blank">here</a>.', 'porto' ),
						esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) ),
						esc_url( admin_url( 'widgets.php' ) )
					),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				),
			),
			array(
				'id'        => 'product-single-layout',
				'type'      => 'image_select',
				'title'     => __( 'Page Layout', 'porto' ),
				'subtitle'  => __( 'Product Page Layout', 'porto' ),
				'options'   => $page_layouts,
				'default'   => 'right-sidebar',
				'transport' => 'refresh',
			),
			array(
				'id'       => 'product-single-sidebar2',
				'type'     => 'select',
				'title'    => __( 'Select Sidebar 2', 'porto' ),
				'required' => array( 'product-single-layout', 'equals', $both_sidebars ),
				'data'     => 'sidebars',
			),
			array(
				'id'       => 'product-metas',
				'type'     => 'button_set',
				'title'    => __( 'Show Product Meta', 'porto' ),
				'subtitle' => __( 'Select product metas to show like product sku, categories, tags, product brand, gtin and so on.', 'porto' ),
				'multi'    => true,
				'options'  => array(
					'sku'              => __( 'SKU', 'porto' ),
					'cats'             => __( 'Categories', 'porto' ),
					'tags'             => __( 'Tags', 'porto' ),
					'global_unique_id' => __( 'GTIN, UPC, EAN or ISBN.', 'woocommerce' ),
					'brand'            => __( 'Brand', 'woocommerce' ),
					'-'                => 'None',
				),
				'hint'     => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-metas.gif"/>' ),
                ),
				'default'  => array( 'sku', 'cats', 'tags', 'brand', '-' ),
			),
			array(
				'id'       => 'product-attr-desc',
				'type'     => 'switch',
				'title'    => __( 'Show Description of Selected Attribute', 'porto' ),
				'subtitle' => __( 'To show description if it exists when selecting product attribute in the variations.', 'porto' ),
				'default'  => false,
				'hint'     => array(
                    'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-attr-desc.gif"/>' ),
                ),
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'     => 'desc_info_sp_tab',
				'type'   => 'info',
				'desc'   => wp_kses(
					__( '<b>Product Tab</b>', 'porto' ),
					array(
						'span' => array(),
						'b'    => array(),
					)
				),
				'notice' => false,
				'class'  => 'porto-redux-section',
			),
			array(
				'id'       => 'product-tab-close-mobile',
				'type'     => 'switch',
				'title'    => __( 'Collapse the accordion on mobile at first?', 'porto' ),
				'subtitle' => __( 'Enable this option to collapse the accordion on mobile.', 'porto' ),
				'default'  => false,
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
				'required' => array( 'undefined', 'equals', true ), // unknown condition
			),
			array(
				'id'       => 'product-custom-tabs-count',
				'type'     => 'text',
				'title'    => __( 'Additional Tabs Count (Product Custom Tab)', 'porto' ),
				'subtitle' => __( 'You can input the tab content in meta fields of "Edit Product".', 'porto' ),
				'default'  => '2',
			),
			array(
				'id'       => 'product-tab-title',
				'type'     => 'text',
				'title'    => __( 'Global Product Custom Tab Title', 'porto' ),
				'subtitle' => __( 'Input the title of Product Custom Tab.', 'porto' ),
				'default'  => '',
			),
			array(
				'id'       => 'product-tab-block',
				'type'     => 'text',
				'title'    => __( 'Global Product Custom Tab Block', 'porto' ),
				'subtitle' => __( 'This block will be shown in the Custom Tab Content.', 'porto' ),
				'desc'     => __( 'Input block slug name', 'porto' ),
				'default'  => '',
			),
			array(
				'id'       => 'product-tab-priority',
				'type'     => 'text',
				'title'    => __( 'Global Product Custom Tab Priority', 'porto' ),
				'subtitle' => __( 'Input the custom tab priority. (Description: 10, Additional Information: 20, Reviews: 30)', 'porto' ),
				'default'  => '60',
			),
		),
	);
}

$this->sections[] = array(
	'icon_class' => 'icon',
	'subsection' => true,
	'title'      => __( 'Product Image & Zoom', 'porto' ),
	'transport'  => 'postMessage',
	'fields'     => array(
		array(
			'id'       => 'product-thumbs',
			'type'     => 'switch',
			'title'    => __( 'Show Thumbnails', 'porto' ),
			'subtitle' => __( 'To show product thumbnails gallery below the main products slider in single product page.', 'porto' ),
			'default'  => true,
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-thumbs.jpg"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'product-thumbs-count',
			'type'     => 'text',
			'required' => array( 'product-thumbs', 'equals', true ),
			'title'    => __( 'Thumbnails Count', 'porto' ),
			'subtitle' => __( 'This option is available for default layout of single product image.', 'porto' ),
			'default'  => '4',
		),
		array(
			'id'       => 'product-thumbs-w',
			'type'     => 'slider',
			'required' => array( 'product-thumbs', 'equals', true ),
			'title'    => __( 'Thumbnails Image Width', 'porto' ),
			'subtitle' => __( 'Thumbnails image width in pixel', 'porto' ),
			'description' => sprintf( __( 'NOTE: You need to regenerate all thumbnails to apply the changes. Please use this %1$splugin%2$s to do it.', 'porto' ), '<a href="https://wordpress.org/plugins/regenerate-thumbnails/" target="_blank">', '</a>' ),
			'default'  => 150,
			'min'      => 50,
			'max'      => 500,
			'step'     => 10,
		),
		/*array(
		'id'=>'product-image-border',
		'type' => 'switch',
		'title' => __('Show Product Image Border', 'porto'),
		'desc' => __( 'If you select yes, this will display border on product image.', 'porto' ),
		'default' => true,
		'on' => __('Yes', 'porto'),
		'off' => __('No', 'porto'),
		),*/
		array(
			'id'       => 'product-zoom',
			'type'     => 'switch',
			'title'    => __( 'Enable Image Zoom', 'porto' ),
			'subtitle' => __( 'To show zoom lens on product image hover in single product page.', 'porto' ),
			'default'  => true,
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-zoom.gif"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'product-zoom-mobile',
			'type'     => 'switch',
			'title'    => __( 'Enable Image Zoom on Mobile', 'porto' ),
			'required' => array( 'product-zoom', 'equals', true ),
			'default'  => true,
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'product-image-popup',
			'type'     => 'switch',
			'title'    => __( 'Enable Image Popup', 'porto' ),
			'subtitle' => __( 'To show the image gallery popup on click in single product page.', 'porto' ),
			'default'  => true,
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-image-popup.gif"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'     => 'desc_info_zoom_type',
			'type'   => 'info',
			'title'  => __( 'Zoom Type For Single Product Page.', 'porto' ),
			'notice' => false,
		),
		array(
			'id'       => 'zoom-type',
			'type'     => 'button_set',
			'title'    => __( 'Zoom Type', 'porto' ),
			'subtitle' => __( 'Select the type to zoom in/out image in single product page.', 'porto' ),
			'options'  => array(
				'inner' => __( 'Inner', 'porto' ),
				'lens'  => __( 'Lens', 'porto' ),
			),
			'default' => 'inner',
		),
		array(
			'id'       => 'zoom-scroll',
			'type'     => 'switch',
			'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
			'title'    => __( 'Scroll Zoom', 'porto' ),
			'subtitle' => __( 'To zoom in or out the product image by mouse scroll.', 'porto' ),
			'default'  => true,
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'zoom-lens-size',
			'type'     => 'text',
			'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
			'title'    => __( 'Lens Size', 'porto' ),
			'subtitle' => __( 'Input the zoom size of magnifier.', 'porto' ),
			'default'  => '200',
		),
		array(
			'id'       => 'zoom-lens-shape',
			'type'     => 'button_set',
			'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
			'title'    => __( 'Lens Shape', 'porto' ),
			'subtitle' => __( 'Input the type of magnifier.', 'porto' ),
			'options'  => array(
				'round' => array(
                    'label' => __( 'Round', 'porto' ),
                    'hint'  => array(
                        'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'zoom-lens-shape-circle.gif"/>' ),
                    ),
                ),
				'square' => array(
                    'label' => __( 'Square', 'porto' ),
                    'hint'  => array(
                        'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'zoom-lens-shape-square.gif"/>' ),
                    ),
                ),
			),
			'default'  => 'square',
		),
		array(
			'id'       => 'zoom-contain-lens',
			'type'     => 'switch',
			'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
			'title'    => __( 'Contain Lens Zoom', 'porto' ),
			'default'  => true,
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'zoom-lens-border',
			'type'     => 'text',
			'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
			'title'    => __( 'Lens Border', 'porto' ),
			'default'  => '1',
		),
		array(
			'id'       => 'zoom-border',
			'type'     => 'text',
			'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
			'title'    => __( 'Border Size', 'porto' ),
			'subtitle' => __( 'Controls the border size of Lens.', 'porto' ),
			'default'  => '4',
		),
		array(
			'id'       => 'zoom-border-color',
			'type'     => 'color',
			'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
			'title'    => __( 'Border Color', 'porto' ),
			'subtitle' => __( 'Controls the border color of Lens.', 'porto' ),
			'default'  => '#888888',
			'required' => array( 'zoom-border', '!=', '' ),
		),
	),
);
$this->sections[] = array(
	'icon_class' => 'icon',
	'subsection' => true,
	'title'      => __( 'Cart & Checkout Page', 'porto' ),
	'transport'  => 'postMessage',
	'fields'     => array(
		array(
			'id'        => 'woo-show-default-page-header',
			'type'      => 'switch',
			'title'     => __( 'Page header in Cart and Checkout page', 'porto' ),
			'default'   => true,
			// 'on'        => __( 'Yes', 'porto' ),
			// 'off'       => __( 'No', 'porto' ),
			'on'        => '<img data-original="' . PORTO_OPTIONS_URI . '/svg/ph-progressive.svg" src="' . PORTO_URI . '/images/lazy.png" title="Progressive Page Header" />',
			'off'       => '<img data-original="' . PORTO_OPTIONS_URI . '/svg/ph-default.svg" src="' . PORTO_URI . '/images/lazy.png" title="Default Page Header" />',
			'transport' => 'refresh',
		),
		array(
			'id'       => 'product-crosssell',
			'type'     => 'switch',
			'title'    => __( 'Show Cross Sells', 'porto' ),
			'subtitle' => __( 'To show cross-sell products.', 'porto' ),
			'default'  => true,
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'product-crosssell.gif"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'product-crosssell-count',
			'type'     => 'text',
			'required' => array( 'product-crosssell', 'equals', true ),
			'title'    => __( 'Cross Sells Count', 'porto' ),
			'subtitle' => __( 'Controls the count of product to show.', 'porto' ),
			'default'  => '8',
		),
		array(
			'id'       => 'auto-update-cart',
			'type'     => 'switch',
			'title'    => __( 'Auto Update Cart on Quantity Change', 'porto' ),
			'subtitle' => __( 'The cart will be refreshed automatically when you increase/decrease the product quantity.', 'porto' ),
			'default'  => false,
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'auto-update-cart.gif"/>' ),
			),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'disable-quantity-checkout',
			'type'     => 'switch',
			'title'    => __( 'Disable Quantity of Cart Item on Checkout Page', 'porto' ),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'        => 'cart-version',
			'type'      => 'button_set',
			'title'     => __( 'Cart Page Type', 'porto' ),
			'subtitle'  => __( 'Select the type of cart page layout.', 'porto' ),
			'options'   => array(
				'v1' => __( 'Type 1', 'porto' ),
				'v2' => __( 'Type 2', 'porto' ),
			),
			'default'   => 'v2',
			'transport' => 'refresh',
		),
		array(
			'id'       => 'checkout-version',
			'type'     => 'button_set',
			'title'    => __( 'Checkout Page Type', 'porto' ),
			'subtitle' => __( 'Select the type of checkout page layout.', 'porto' ),
			'options'  => array(
				'v1' => __( 'Type 1', 'porto' ),
				'v2' => __( 'Type 2', 'porto' ),
			),
			'default' => 'v1',
		),
	),
);
$this->sections[] = array(
	'icon_class' => 'icon',
	'subsection' => true,
	'title'      => __( 'Catalog Mode', 'porto' ),
	'fields'     => array(
		array(
			'id'        => 'product-show-price-role',
			'type'      => 'button_set',
			'multi'     => true,
			'title'     => __( 'Select roles to see product price', 'porto' ),
			'subtitle'  => __( 'Show the product price by roles.', 'porto' ),
			'default'   => array(),
			'options'   => $all_roles,
			'transport' => 'refresh',
		),
		array(
			'id'       => 'catalog-enable',
			'type'     => 'switch',
			'title'    => __( 'Enable Catalog Mode', 'porto' ),
			'subtitle' => __( 'Catalog mode is generally used to hide some product fields such as price and add to cart button on shop and product detail page.', 'porto' ),
			'default'  => false,
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'catalog-admin',
			'type'     => 'switch',
			'title'    => __( 'Enable also for administrators', 'porto' ),
			'subtitle' => __( '"YES" option enables catalog mode to administrator also.', 'porto' ),
			'default'  => true,
			'required' => array( 'catalog-enable', 'equals', true ),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'catalog-price',
			'type'     => 'switch',
			'title'    => __( 'Show Price', 'porto' ),
			'subtitle' => __( 'To show price on catalog mode.', 'porto' ),
			'default'  => false,
			'required' => array( 'catalog-enable', 'equals', true ),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'catalog-review',
			'type'     => 'switch',
			'title'    => __( 'Show Reviews', 'porto' ),
			'subtitle' => __( 'To show reviews.', 'porto' ),
			'default'  => false,
			'required' => array( 'catalog-enable', 'equals', true ),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'desc_info_add_cart',
			'type'     => 'info',
			'title'    => __( 'For Add To Cart Button', 'porto' ),
			'required' => array( 'catalog-enable', 'equals', true ),
			'notice'   => false,
		),
		array(
			'id'       => 'catalog-cart',
			'type'     => 'switch',
			'title'    => __( 'Show Add Cart Button', 'porto' ),
			'subtitle' => __( 'To show Add Cart Button on catalog mode.', 'porto' ),
			'default'  => false,
			'required' => array( 'catalog-enable', 'equals', true ),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'catalog-readmore',
			'type'     => 'switch',
			'title'    => __( 'Show Read More Button', 'porto' ),
			'subtitle' => __( 'To show Read More Button on catalog mode.', 'porto' ),
			'default'  => false,
			'required' => array( 'catalog-cart', 'equals', false ),
			'on'       => __( 'Yes', 'porto' ),
			'off'      => __( 'No', 'porto' ),
		),
		array(
			'id'       => 'catalog-readmore-label',
			'type'     => 'text',
			'required' => array( 'catalog-readmore', 'equals', true ),
			'title'    => __( 'Read More Button Label', 'porto' ),
			'subtitle' => __( 'Input the Label instead of "Read More".', 'porto' ),
			'default'  => 'Read More',
		),
		array(
			'id'       => 'catalog-readmore-target',
			'type'     => 'button_set',
			'title'    => __( 'Read More Button Target Attribute', 'porto' ),
			'subtitle' => __( 'Determines how to display the target of the linked URL.', 'porto' ),
			'required' => array( 'catalog-readmore', 'equals', true ),
			'options'  => array(
				''       => __( 'Self', 'porto' ),
				'_blank' => __( 'Blank', 'porto' ),
			),
			'default'  => '',
		),
		array(
			'id'       => 'catalog-readmore-archive',
			'type'     => 'button_set',
			'title'    => __( 'Determines where to use target attribute.', 'porto' ),
			'required' => array( 'catalog-readmore', 'equals', true ),
			'options'  => array(
				'all'     => __( 'Product and Product Archives', 'porto' ),
				'product' => __( 'Product', 'porto' ),
			),
			'default'  => 'all',
		),
	),
);
// Register form
$this->sections[] = array(
	'icon_class' => 'icon',
	'subsection' => true,
	'title'      => __( 'Registration form', 'porto' ),
	'fields'     => array(
		array(
			'id'       => 'reg-form-info',
			'type'     => 'button_set',
			'title'    => __( 'Registration Fields', 'porto' ),
			'subtitle' => __( 'If select "Full Info", extra fields such as first name, last name and password confirmation are added in registration form.', 'porto' ),
			'multi'    => false,
			'options'  => array(
				'full'  => array(
                    'label' => __( 'Full Info', 'porto' ),
                    'hint'  => array(
                        'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'reg-form-info-full.jpg"/>' ),
                    ),
                ),
				'short' => array(
                    'label' => __( 'Short Info', 'porto' ),
                    'hint'  => array(
                        'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'reg-form-info-short.jpg"/>' ),
                    ),
                ),
			),
			'default' => 'short',
		),

	),
);

// WC Vendor
if ( class_exists( 'WC_Vendors' ) ) {
	$this->sections[] = array(
		'title'      => __( 'Wc Vendor', 'porto' ),
		'icon'       => 'el el-usd',
		'customizer' => false,
		'fields'     => array(
			array(
				'id'    => 'desc_info_wc_vendor',
				'type'  => 'info',
				'title' => __( 'General Wc Vendor Shop Settings', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_phone',
				'type'     => 'switch',
				'title'    => __( 'Select Vendor Phone Number', 'porto' ),
				'compiler' => true,
				'default'  => '1',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_email',
				'type'     => 'switch',
				'title'    => __( 'Show Vendor Email', 'porto' ),
				'compiler' => true,
				'default'  => '1',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_url',
				'type'     => 'switch',
				'title'    => __( 'Show Vendor URL', 'porto' ),
				'compiler' => true,
				'default'  => '1',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'    => 'desc_info_vendor_shop',
				'type'  => 'info',
				'title' => __( 'WC Vendors - Shop Page', 'porto' ),
			),

			array(
				'id'       => 'porto_wcvendors_shop_description',
				'type'     => 'switch',
				'title'    => __( 'Vendor Description on Top of Shop Page', 'porto' ),
				'compiler' => true,
				'default'  => '1',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_shop_avatar',
				'type'     => 'switch',
				'title'    => __( 'Show Vendor Avatar in Vendor Description', 'porto' ),
				'compiler' => true,
				'default'  => '1',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_shop_profile',
				'type'     => 'switch',
				'title'    => __( 'Show Social and Contact Info in Vendor Description', 'porto' ),
				'compiler' => true,
				'default'  => '1',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_shop_soldby',
				'type'     => 'switch',
				'title'    => __( 'Sold by" at Product List', 'porto' ),
				'compiler' => true,
				'default'  => '1',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'    => 'desc_info_vendor_sp',
				'type'  => 'info',
				'title' => __( 'WC Vendors - Single Product Page', 'porto' ),
			),
			/*array(
			'id' => 'porto_single_wcvendors_hide_header',
			'type' => 'switch',
			'title' => __ ( 'Vendor Single Product Page Show Header', 'porto' ),
			'compiler' => true,
			'default' => '1',
			'on' => __('Yes','porto'),
			'off' =>  __('No','porto'),
			),*/
			array(
				'id'       => 'porto_single_wcvendors_product_description',
				'type'     => 'switch',
				'title'    => __( 'Vendor Description on Top of Single Product Page', 'porto' ),
				'compiler' => true,
				'default'  => '0',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_product_avatar',
				'type'     => 'switch',
				'title'    => __( 'Show Vendor Avatar in Vendor Description', 'porto' ),
				'compiler' => true,
				'default'  => '0',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_product_profile',
				'type'     => 'switch',
				'title'    => __( 'Show Social and Contact Info in Vendor Description', 'porto' ),
				'compiler' => true,
				'default'  => '0',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_product_tab',
				'type'     => 'switch',
				'title'    => __( '"Seller Info" at Product Tab', 'porto' ),
				'compiler' => true,
				'default'  => '0',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_product_moreproducts',
				'type'     => 'switch',
				'title'    => __( '"More From This Seller" Products', 'porto' ),
				'compiler' => true,
				'default'  => '0',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_product_soldby',
				'type'     => 'switch',
				'title'    => __( 'Sold by" at Product Meta', 'porto' ),
				'compiler' => true,
				'default'  => '0',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
			array(
				'id'    => 'desc_info_vendor_cart',
				'type'  => 'info',
				'title' => __( 'WC Vendors - Cart Page', 'porto' ),
			),
			array(
				'id'       => 'porto_wcvendors_cartpage_soldby',
				'type'     => 'switch',
				'title'    => __( '"Sold by" at Cart page', 'porto' ),
				'compiler' => true,
				'default'  => '1',
				'on'       => __( 'Yes', 'porto' ),
				'off'      => __( 'No', 'porto' ),
			),
		),
	);
}
$this->sections[] = array(
	'icon_class' => 'icon',
	'subsection' => true,
	'title'      => __( 'Styling', 'porto' ),
	'transport'  => 'postMessage',
	'fields'     => array(
		array(
			'id'    => 'desc_info_styling',
			'type'  => 'info',
			'desc'  => wp_kses(
				/* translators: %s: Builder url */
				sprintf( __( '<a class="pt-showm-options" href="#"><span>Show More Options</span><i class="fas fa-angle-down"></i></a><strong>Important Note:</strong> <a href="%1$s" target="_blank">Single Product</a> & <a href="%2$s" target="_blank">Product Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.<br/><b>We recommend to use Template Builder to customize easily.</b>', 'porto' ), $product_url, $type_url ),
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
			'id'       => 'shop-add-links-color',
			'type'     => 'color',
			'title'    => 'Add Links Color',
			'subtitle' => __( 'Add to cart, Wishlist and Quick View Color on archive page', 'porto' ),
			'default'  => '#333333',
			'validate' => 'color',
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'shop-add-links-color.gif"/>' ),
			),
			'selector' => array(
				'node' => 'ul.products, .porto-posts-grid',
			),
		),
		array(
			'id'       => 'shop-add-links-bg-color',
			'type'     => 'color',
			'title'    => 'Add Links Background Color',
			'subtitle' => __( 'Add to cart, Wishlist and Quick View Background Color on archive page', 'porto' ),
			'default'  => '#ffffff',
			'validate' => 'color',
			'selector' => array(
				'node' => 'ul.products, .porto-posts-grid',
			),
		),
		array(
			'id'       => 'shop-add-links-border-color',
			'type'     => 'color',
			'title'    => 'Add Links Border Color',
			'subtitle' => __( 'Add to cart, Wishlist and Quick View Border Color on archive page', 'porto' ),
			'default'  => '#dddddd',
			'validate' => 'color',
			'selector' => array(
				'node' => 'ul.products, .porto-posts-grid',
			),
		),
		array(
			'id'       => 'hot-color',
			'type'     => 'color',
			'title'    => __( 'Hot Bg Color', 'porto' ),
			'subtitle' => __( 'Control the background of Hot label for featured product.', 'porto' ),
			'desc'     => __( 'To show Hot label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
			'default'  => '#62b959',
			'validate' => 'color',
			'selector' => array(
				'node' => '.post-date, .onhot',
			),
		),
		array(
			'id'       => 'hot-color-inverse',
			'type'     => 'color',
			'title'    => __( 'Hot Text Color', 'porto' ),
			'subtitle' => __( 'Control the text color of Hot label for featured product.', 'porto' ),
			'desc'     => __( 'To show Hot label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
			'default'  => '#ffffff',
			'validate' => 'color',
			'selector' => array(
				'node' => '.post-date, .onhot',
			),
		),
		array(
			'id'       => 'sale-color',
			'type'     => 'color',
			'title'    => __( 'Sale Bg Color', 'porto' ),
			'subtitle' => __( 'Control the background of Sale label.', 'porto' ),
			'desc'     => __( 'To show Sale label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
			'default'  => '#e27c7c',
			'validate' => 'color',
			'selector' => array(
				'node' => '.onsale',
			),
		),
		array(
			'id'       => 'sale-color-inverse',
			'type'     => 'color',
			'title'    => __( 'Sale Text Color', 'porto' ),
			'subtitle' => __( 'Control the text color of Sale label.', 'porto' ),
			'desc'     => __( 'To show Sale label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
			'default'  => '#ffffff',
			'validate' => 'color',
			'selector' => array(
				'node' => '.onsale',
			),
		),
		array(
			'id'       => 'new-bgc',
			'type'     => 'color',
			'title'    => __( 'New Label Bg Color', 'porto' ),
			'subtitle' => __( 'Control the background of New label for products.', 'porto' ),
			'desc'     => __( 'To show New label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
			'default'  => '',
			'validate' => 'color',
			'selector' => array(
				'node' => '.onnew',
			),
		),
		array(
			'id'          => 'add-to-cart-font',
			'type'        => 'typography',
			'title'       => __( 'Add to Cart Font', 'porto' ),
			'subtitle'    => __( 'Used in add to cart button, quickview, wishlist, price, etc', 'porto' ),
			'google'      => true,
			'subsets'     => false,
			'font-style'  => false,
			'text-align'  => false,
			'color'       => false,
			'font-weight' => false,
			'font-size'   => false,
			'line-height' => false,
			'default'     => array(
				'google'      => true,
			),
			'selector'    => array(
				'node' => ':root',
			),
		),
		array(
			'id'       => 'wishlist-color',
			'type'     => 'color',
			'title'    => __( 'Product Action Color', 'porto' ),
			'subtitle' => __( 'Controls the color of wishlist and compare on single product page.', 'porto' ),
			'default'  => '#302e2a',
			'validate' => 'color',
			'hint'     => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'wishlist-color.gif"/>' ),
			),
			'selector' => array(
				'node' => '.product-summary-wrap .yith-wcwl-add-to-wishlist, .product-summary-wrap .yith-compare',
			),
		),
		array(
			'id'       => 'wishlist-color-inverse',
			'type'     => 'color',
			'title'    => __( 'Product Action Hover Color', 'porto' ),
			'subtitle' => __( 'Controls the hover color of wishlist and compare on single product page.', 'porto' ),
			'default'  => '',
			'validate' => 'color',
			'hint'      => array(
				'content' => esc_html( '<img src="' . PORTO_HINT_URL . 'wishlist-color-inverse.gif"/>' ),
			),
			'selector' => array(
				'node' => '.product-summary-wrap .yith-wcwl-add-to-wishlist, .product-summary-wrap .yith-compare',
			),
		),
	),
);
