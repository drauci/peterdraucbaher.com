<?php

// Register sidebars and widgetized areas

add_action( 'widgets_init', 'porto_register_sidebars' );

function porto_register_sidebars() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'porto' ),
			'description'   => esc_html__( 'Widget Area for blog and single post pages.', 'porto' ),
			'id'            => 'blog-sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Homepage Sidebar', 'porto' ),
			'description'   => esc_html__( 'Widget Area for homepage.', 'porto' ),
			'id'            => 'home-sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Secondary Sidebar', 'porto' ),
			'description'   => esc_html__( 'Widget Area for secondary(right) sidebar of pages.', 'porto' ),
			'id'            => 'secondary-sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	if ( class_exists( 'Woocommerce' ) ) {
		global $porto_settings;

		register_sidebar(
			array(
				'name'          => __( 'Shop Sidebar(Woo Category Sidebar)', 'porto' ),
				'description'   => esc_html__( 'Widget Area for shop page or product category pages.', 'porto' ),
				'id'            => 'woo-category-sidebar',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Shop Horizontal(For horizontal filters layout)', 'porto' ),
				'description'   => esc_html__( 'Widget Area for shop page which has horizontal filters layout on Theme Option > WooCommerce > Filter Layout.', 'porto' ),
				'id'            => 'woo-category-filter-sidebar',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Single Product Sidebar(Woo Product Sidebar)', 'porto' ),
				'description'   => esc_html__( 'Widget Area for single product pages.', 'porto' ),
				'id'            => 'woo-product-sidebar',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

	}

	if ( apply_filters( 'porto_legacy_mode', true ) ) {
		register_sidebar(
			array(
				'name'          => __( 'Content Bottom Widget 1', 'porto' ),
				'id'            => 'content-bottom-1',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Content Bottom Widget 2', 'porto' ),
				'id'            => 'content-bottom-2',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Content Bottom Widget 3', 'porto' ),
				'id'            => 'content-bottom-3',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Content Bottom Widget 4', 'porto' ),
				'id'            => 'content-bottom-4',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Footer Top Widget', 'porto' ),
				'description'   => esc_html__( 'Widget Area for footer top.', 'porto' ),
				'id'            => 'footer-top',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Footer Widget 1', 'porto' ),
				'description'   => esc_html__( 'Widget Area for 1st column of footer middle.', 'porto' ),
				'id'            => 'footer-column-1',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Footer Widget 2', 'porto' ),
				'description'   => esc_html__( 'Widget Area for 2nd column of footer middle.', 'porto' ),
				'id'            => 'footer-column-2',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Footer Widget 3', 'porto' ),
				'description'   => esc_html__( 'Widget Area for 3rd column of footer middle.', 'porto' ),
				'id'            => 'footer-column-3',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Footer Widget 4', 'porto' ),
				'description'   => esc_html__( 'Widget Area for 4th column of footer middle.', 'porto' ),
				'id'            => 'footer-column-4',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Footer Bottom Widget', 'porto' ),
				'description'   => esc_html__( 'Widget Area for footer bottom like copyright text.', 'porto' ),
				'id'            => 'footer-bottom',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}
