<?php
/**
 * Single Product tabs
 *
 * @version     9.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

global $porto_settings, $porto_tmp_tab_rand;

$review_index = 0;

if ( isset( $porto_tmp_tab_rand ) ) {
	$rand = $porto_tmp_tab_rand;
} else {
	$rand = porto_generate_rand( 8 );
}

wp_enqueue_script( 'easy-responsive-tabs' );

if ( isset( $hide_tabs ) && is_array( $hide_tabs ) ) {
	foreach ( $hide_tabs as $tab ) {
		// Unset default Product Tabs
		unset( $product_tabs[$tab] );
	}
}

if ( ! empty( $product_tabs ) || ! empty( $custom_tabs_title ) || ! empty( $global_tab_title ) ) :
	$skeleton_lazyload = ! empty( $porto_settings['skeleton_lazyload_product_desc'] );
	?>

	<div class="woocommerce-tabs woocommerce-tabs-<?php echo esc_attr( $rand ), ! $skeleton_lazyload ? '' : ' skeleton-loading'; ?> resp-htabs" id="product-tab">
	<?php
	if ( $skeleton_lazyload ) {
		ob_start();
	}
	?>
		<ul class="resp-tabs-list" role="tablist">
			<?php
			$i = 0;
			foreach ( $product_tabs as $key => $product_tab ) :
				?>
				<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" data-target="tab-<?php echo esc_attr( $key ); ?>">
					<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
				</li>
				<?php
				if ( 'reviews' == $key ) {
					$review_index = $i;
				}
				$i++;
			endforeach;
			?>

		</ul>
		<div class="resp-tabs-container">
			<?php 
			$i = 0;
			foreach ( $product_tabs as $key => $product_tab ) : ?>

				<div class="tab-content <?php echo 0 != $i ? 'resp-tab-content' : '' ?>" id="tab-<?php echo esc_attr( $key ); ?>">
					<?php
					if ( isset( $product_tab['callback'] ) ) {
						call_user_func( $product_tab['callback'], $key, $product_tab );
					}
					?>
				</div>

			<?php 
			$i++;
			endforeach; ?>
		</div>

		<?php do_action( 'woocommerce_product_after_tabs' ); ?>

		<script>
			( function() {
				var porto_init_desc_tab = function() {
					( function( $ ) {
						var $tabs = $('.woocommerce-tabs-<?php echo esc_js( $rand ); ?>');

						function init_tabs($tabs) {
							$tabs.easyResponsiveTabs({
								type: 'default', //Types: default, vertical, accordion
								width: 'auto', //auto or any width like 600px
								fit: true,   // 100% fit in a container
								activate: function(event) { // Callback function if tab is switched
								},
								closed: <?php echo isset( $closed ) ? ( empty( $closed ) ? 'false' : 'true' ) : ( empty( $porto_settings['product-tab-close-mobile'] ) ? 'false' : 'true' ); ?>
							});
						}
						if (!$.fn.easyResponsiveTabs) {
							var js_src = "<?php echo PORTO_JS . '/libs/easy-responsive-tabs.min.js'; ?>";
							if (!$('script[src="' + js_src + '"]').length) {
								var js = document.createElement('script');
								$(js).appendTo('body').on('load', function() {
									init_tabs($tabs);
								}).attr('src', js_src);
							}
						} else {
							init_tabs($tabs);
						}

						function goAccordionTab(target) {
							setTimeout(function() {
								var label = target.attr('data-target');
								var $tab_content = $tabs.find('.resp-tab-content[aria-labelledby="' + label + '"]');
								if ($tab_content.length && $tab_content.css('display') != 'none') {
									var offset = target.offset().top - theme.StickyHeader.sticky_height - theme.adminBarHeight() - 14;
									if (offset < $(window).scrollTop())
									$('html, body').stop().animate({
										scrollTop: offset
									}, 600, 'easeOutQuad');
								}
							}, 500);
						}

						$tabs.find('h2.resp-accordion').on('click', function(e) {
							goAccordionTab($(this));
						});
					} )( window.jQuery );
				};

				if ( window.theme && theme.isLoaded ) {
					porto_init_desc_tab();
				} else {
					window.addEventListener( 'load', porto_init_desc_tab );
				}
			} )();
		</script>
		<?php
		if ( $skeleton_lazyload ) :
			$post_content = ob_get_clean();
			?>
			<script type="text/template"><?php echo json_encode( $post_content ); ?></script>
		<?php endif; ?>
	</div>

	<?php if ( $skeleton_lazyload ) : ?>
		<div class="tab-content skeleton-body"></div>
	<?php endif; ?>
<?php endif; ?>
