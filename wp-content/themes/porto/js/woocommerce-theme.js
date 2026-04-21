( function() {
	'use strict';
	// Compatibility with YITH WISHLIST PRO
	if ( typeof yith_wcwl_l10n != 'undefined' ) {
		yith_wcwl_l10n.enable_tooltip = false;
	}
	// Hide Tooltip before adding to wishlist
	jQuery( document ).on( 'yith_wcwl_add_to_wishlist_data yith_woocompare_product_added', function( e, $el ) {
		if ( $el.length ) {
			$el.tooltip( 'dispose' );
		}
		if ( $el.$initiator ) {
			$el.$initiator.tooltip( 'dispose' );
		}
	} );
	jQuery( document ).on( 'click', 'a.compare.added', function( e ) {
		jQuery( this ).tooltip( 'dispose' );
	});
	// Theme Functions
	function portoCalcSliderButtonsPosition( $parent, padding ) {
		var $buttons = $parent.find( '.show-nav-title .owl-nav' );
		if ( $buttons.length ) {
			if ( window.theme.rtl ) {
				$buttons.css( 'left', padding );
			} else {
				$buttons.css( 'right', padding );
			}
			if ( $buttons.closest( '.porto-products' ).length && $buttons.closest( '.porto-products' ).parent().children( '.products-slider-title' ).length ) {
				var $title = $buttons.closest( '.porto-products' ).parent().children( '.products-slider-title' ), newMT = $title.offset().top - $parent.offset().top - parseInt( $title.css( 'padding-top' ), 10 ) - parseInt( $title.css( 'line-height' ), 10 ) / 2 + $buttons.children().outerHeight() - parseInt( $buttons.children().css( 'margin-top' ), 10 );
				$buttons.css( 'margin-top', newMT );
			}
		}
	}

	// Woocommerce Products Slider
	if ( typeof jQuery.fn.owlCarousel == 'function' ) {
		( function( theme, $ ) {

			theme = theme || {};

			var instanceName = '__wooProductsSlider';

			var WooProductsSlider = function( $el, opts ) {
				return this.initialize( $el, opts );
			};

			WooProductsSlider.defaults = {
				rtl: theme.rtl,
				autoplay: theme.slider_autoplay == '1' ? true : false,
				autoplayTimeout: theme.slider_speed ? theme.slider_speed : 5000,
				loop: theme.slider_loop,
				nav: false,
				navText: ["", ""],
				dots: false,
				autoplayHoverPause: true,
				items: 1,
				responsive: {},
				autoHeight: true,
				lazyLoad: true
			};

			WooProductsSlider.prototype = {
				initialize: function( $el, opts ) {
					if ( $el.data( instanceName ) ) {
						return this;
					}

					this.$el = $el;

					this
						.setData()
						.setOptions( opts )
						.build();

					return this;
				},

				setData: function() {
					this.$el.data( instanceName, true );

					return this;
				},

				setOptions: function( opts ) {
					this.options = $.extend( true, {}, WooProductsSlider.defaults, opts, {
						wrapper: this.$el
					} );

					return this;
				},

				calcOwlHeight: function( $el ) {
					var h = 0;
					$el.find( '.owl-item.active' ).each( function() {
						if ( h < $( this ).height() )
							h = $( this ).height();
					} );
					$el.find( '.owl-stage-outer' ).height( h );
				},

				build: function() {
					var self = this,
						$el = this.options.wrapper,
						lg = this.options.lg,
						md = this.options.md,
						xs = this.options.xs,
						ls = this.options.ls,
						$slider_wrapper = $el.closest( '.slider-wrapper' ),
						single = this.options.single,
						dots = this.options.dots,
						nav = this.options.nav,
						responsive = {},
						items,
						// scrollwidth = 0 not using getscrollbarwidth
						scrollWidth = 0,
						count = $el.find( '> *' ).length,
						w_xs = 576 - scrollWidth,
						w_md = 768 - scrollWidth,
						w_xl = theme.screen_xl - scrollWidth,
						w_sl = theme.screen_xxl - scrollWidth;

					if ( $el.find( '.product-col' ).get( 0 ) ) {
						portoCalcSliderButtonsPosition( $slider_wrapper, $el.find( '.product-col' ).css( 'padding-left' ) );
					}

					if ( single ) {
						items = 1;
					} else {
						items = lg ? lg : 1;
						if ( this.options.xl ) {
							responsive[w_sl] = { items: this.options.xl, loop: ( this.options.loop && count > this.options.xl ) ? true : false };
						}
						responsive[w_xl] = { items: items, loop: ( this.options.loop && count > items ) ? true : false };
						if ( md ) responsive[w_md] = { items: md, loop: ( this.options.loop && count > md ) ? true : false };
						if ( xs ) responsive[w_xs] = { items: xs, loop: ( this.options.loop && count > xs ) ? true : false };
						if ( ls ) responsive[0] = { items: ls, loop: ( this.options.loop && count > ls ) ? true : false };
					}

					this.options = $.extend( true, {}, this.options, {
						loop: ( this.options.loop && count > items ) ? true : false,
						items: items,
						responsive: responsive,
						onRefresh: function() {
							if ( $el.find( '.product-col' ).get( 0 ) ) {
								portoCalcSliderButtonsPosition( $slider_wrapper, $el.find( '.product-col' ).css( 'padding-left' ) );
							}
						},
						onInitialized: function() {
							if ( $el.find( '.product-col' ).get( 0 ) ) {
								portoCalcSliderButtonsPosition( $slider_wrapper, $el.find( '.product-col' ).css( 'padding-left' ) );
							}
							//$el.find('.cloned .porto-lazyload:not(.lazy-load-loaded)').themePluginLazyLoad();

							if ( $el.find( '.owl-item.cloned' ).length ) {
								setTimeout( function() {
									if ( $.fn.themePluginLazyLoad ) {
										var ins = $el.find( '.owl-item.cloned .porto-lazyload:not(.lazy-load-loaded)' ).themePluginLazyLoad( { effect: 'fadeIn', effect_speed: 400 } );
										if ( ins && ins.loadAndDestroy ) {
											ins.loadAndDestroy();
										}
									}
								}, 100 );
							}
						},
						touchDrag: ( count == 1 ) ? false : true,
						mouseDrag: ( count == 1 ) ? false : true
					} );

					// Auto Height Fixes
					if ( this.options.autoHeight ) {
						var thisobj = this;
						$( window ).on( 'resize', function() {
							thisobj.calcOwlHeight( $el );
						} );

						if ( theme.isLoaded ) {
							setTimeout( function() {
								thisobj.calcOwlHeight( $el );
							}, 100 );
						} else {
							$( window ).on( 'load', function() {
								thisobj.calcOwlHeight( $el );
							} );
						}
					}

					if ( $el.hasClass( 'has-ccols-spacing' ) ) {
						$el.removeClass( 'has-ccols-spacing' );
					}
					$el.owlCarousel( this.options );

					return this;
				}
			};

			// expose to scope
			$.extend( theme, {
				WooProductsSlider: WooProductsSlider
			} );

			// jquery plugin
			$.fn.themeWooProductsSlider = function( opts ) {
				return this.map( function() {
					var $this = $( this );

					if ( $this.data( instanceName ) ) {
						return $this;
					} else {
						return new theme.WooProductsSlider( $this, opts );
					}

				} );
			}

		} ).apply( this, [window.theme, jQuery] );
	}

	// Woocommerce Add to Cart, View Cart Events
	( function( theme, $ ) {

		var $supports_html5_storage;
		try {
			$supports_html5_storage = ( 'sessionStorage' in window && window.sessionStorage !== null );

			window.sessionStorage.setItem( 'wc', 'test' );
			window.sessionStorage.removeItem( 'wc' );
		} catch ( err ) {
			$supports_html5_storage = false;
		}

		var setCartCreationTimestamp = function() {
			if ( $supports_html5_storage ) {
				sessionStorage.setItem( 'wc_cart_created', ( new Date() ).getTime() );
			}
		};

		var setCartHash = function( cart_hash ) {
			if ( $supports_html5_storage && wc_cart_fragments_params ) {
				localStorage.setItem( wc_cart_fragments_params.cart_hash_key, cart_hash );
				sessionStorage.setItem( wc_cart_fragments_params.cart_hash_key, cart_hash );
			}
		};

		var initAjaxRemoveCartItem = function() {
			$( document ).off( 'click', '.widget_shopping_cart .remove-product, .shop_table.cart .remove-product, .shop_table.review-order .remove-product' ).on( 'click', '.widget_shopping_cart .remove-product, .shop_table.cart .remove-product, .shop_table.review-order .remove-product', function( e ) {
				e.preventDefault();
				var $this = $( this );
				var cart_id = $this.data( "cart_id" );
				var product_id = $this.data( "product_id" );
				var is_checkout = false;
				$this.closest( 'li' ).find( '.ajax-loading' ).show();

				if ( 'undefined' == typeof cart_id ) {
					is_checkout = true;
					cart_id = $this.closest( '.cart_item' ).data( 'key' );
				}
				$.ajax( {
					type: 'POST',
					dataType: 'json',
					url: theme.ajax_url,
					data: {
						action: "porto_cart_item_remove",
						nonce: js_porto_vars.porto_nonce,
						cart_id: cart_id
					},
					success: function( response ) {
						updateCartFragment( response );
						$( document.body ).trigger( 'wc_fragments_refreshed' );
						var this_page = window.location.toString(),
							item_count = $( response.fragments['div.widget_shopping_cart_content'] ).find( '.mini_cart_item' ).length;

						this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );
						$( '.viewcart-' + product_id ).removeClass( 'added' );
						$( '.porto_cart_item_' + cart_id ).remove();

						// Block widgets and fragments
						if ( item_count == 0 && ( $( 'body' ).hasClass( 'woocommerce-cart' ) || $( 'body' ).hasClass( 'woocommerce-checkout' ) ) ) {
							$( '.page-content' ).fadeTo( 400, 0.8 ).block( {
								message: null,
								overlayCSS: {
									opacity: 0.2
								}
							} );
						} else {
							$( 'form.woocommerce-cart-form, #order_review, .updating, .cart_totals' ).fadeTo( 400, 0.8 ).block( {
								message: null,
								overlayCSS: {
									opacity: 0.2
								}
							} );
						}

						// Unblock
						$( '.widget_shopping_cart, .updating' ).stop( true ).css( 'opacity', '1' ).unblock();

						// Cart page elements
						if ( item_count == 0 && ( $( 'body' ).hasClass( 'woocommerce-cart' ) || $( 'body' ).hasClass( 'woocommerce-checkout' ) ) ) {
							$( '.page-content' ).load( this_page + ' .page-content:eq(0) > *', function() {
								$( '.page-content' ).stop( true ).css( 'opacity', '1' ).unblock();
							} );
						} else {
							$( 'form.woocommerce-cart-form' ).load( this_page + ' form.woocommerce-cart-form:eq(0) > *', function() {
								$( 'form.woocommerce-cart-form' ).stop( true ).css( 'opacity', '1' ).unblock();
							} );

							$( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
								$( '.cart_totals' ).stop( true ).css( 'opacity', '1' ).unblock();
							} );

							// Checkout page elements
							$( '#order_review' ).load( this_page + ' #order_review:eq(0) > *', function() {
								$( '#order_review' ).stop( true ).css( 'opacity', '1' ).unblock();
							} );
						}
					}
				} );

				return false;
			} );
		};

		var refreshCartFragment = function() {
			initAjaxRemoveCartItem();
			if ( $.cookie( 'woocommerce_items_in_cart' ) > 0 ) {
				$( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).show();
			} else {
				$( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).hide();
			}
		};

		var updateCartFragment = function( data ) {
			if ( data && data.fragments ) {
				var fragments = data.fragments,
					cart_hash = data.cart_hash;

				$.each( fragments, function( key, value ) {
					$( key ).replaceWith( value );
				} );
				if ( typeof wc_cart_fragments_params === 'undefined' ) {
					return;
				}
				/* Storage Handling */
				if ( $supports_html5_storage ) {
					var prev_cart_hash = sessionStorage.getItem( 'wc_cart_hash' );

					if ( prev_cart_hash === null || prev_cart_hash === undefined || prev_cart_hash === '' ) {
						setCartCreationTimestamp();
					}
					sessionStorage.setItem( wc_cart_fragments_params.fragment_name, JSON.stringify( fragments ) );
					setCartHash( cart_hash );
				}
			}
		};

		$( function() {

			refreshCartFragment();

			// add ajax cart loading
			$( document ).on( 'click', '.add_to_cart_button', function( e ) {
				var $this = $( this );
				if ( typeof theme.noAjaxCart == 'undefined' ) {
					theme.noAjaxCart = ! $( '#wc-add-to-cart-js' ).length;
				}
				if ( $this.is( '.product_type_simple' ) || $this.is( '.jck_wssv_add_to_cart' ) ) {
					if ( 'SPAN' == $this.prop( 'tagName' ) && ( theme.noAjaxCart || ! $this.attr( 'data-product_id' ) ) ) {
						window.location.href = $this.attr( 'href' );
					}

					if ( $.fn.tooltip ) {
						$this.tooltip( 'hide' );
					}

					if ( $this.attr( 'data-product_id' ) ) {
						$this.addClass( 'product-adding' );

						// add to cart notifaction style 2, 3
						if ( $this.hasClass( 'viewcart-style-2' ) || $this.hasClass( 'viewcart-style-3' ) ) {
							if ( $this.closest( '.porto-hotspot' ).length == 0 && $this.closest( '.menu-block' ).length == 0 && ! $( '#loading-mask' ).length ) {
								$( 'body' ).append( '<div id="loading-mask"><div class="background-overlay"></div></div>' );
							}
							if ( !$( this ).closest( '.product' ).find( '.loader-container' ).length ) {
								$( this ).closest( '.product' ).find( '.product-image' ).append( '<div class="loader-container"><div class="loader"><i class="porto-ajax-loader"></i></div></div>' );
							}
							$( this ).closest( '.product' ).find( '.loader-container' ).show();
						}
					}
				} else if ( 'SPAN' == $this.prop( 'tagName' ) ) {
					window.location.href = $this.attr( 'href' );
				}
			} );

			$( document.body ).on( 'added_to_cart', function() {
				$( 'ul.products li.product .added_to_cart, .porto-tb-item .added_to_cart' ).remove();
				initAjaxRemoveCartItem();
			} );

			$( document.body ).on( 'wc_cart_button_updated', function() {
				$( 'ul.products li.product .added_to_cart, .porto-tb-item .added_to_cart' ).remove();
			} );

			$( document.body ).on( 'wc_fragments_refreshed wc_fragments_loaded', function() {
				refreshCartFragment();
			} );

			$( document ).on( 'click', '.product-image .viewcart, .after-loading-success-message .viewcart', function( e ) {
				if ( wc_add_to_cart_params.cart_url ) {
					window.location.href = wc_add_to_cart_params.cart_url;
				}
				e.preventDefault();
			} );
			var porto_product_add_cart_timer = null;
			$( document ).on( 'added_to_cart', 'body', function( event ) {
				var $mc_item = $( '#mini-cart .cart-items' );
				if ( $mc_item.length ) {
					$mc_item.addClass( 'count-updating' );
					setTimeout( function() {
						$mc_item.removeClass( 'count-updating' );
					}, 1000 );
				}

				//add to cart notifaction style 2,3
				$( 'body #loading-mask' ).remove();

				$( '.add_to_cart_button.product-adding' ).each( function() {
					var $link = $( this );
					$link.removeClass( 'product-adding' );
					if ( $( '.woocommerce-wishlist.woocommerce-page' ).length ) { //wishlist page
						return;
					}
					if ( $link.hasClass( 'viewcart-style-1' ) ) {
						$link.closest( '.product' ).find( '.viewcart' ).addClass( 'added' );
						$( '.minicart-offcanvas' ).addClass( 'minicart-opened' );
					} else {
						$link.closest( '.product' ).find( '.loader-container' ).hide();
						if ( $link.closest( 'li.outofstock' ).length ) {
							return;
						}
						var $msg;
						if ( $link.hasClass( 'viewcart-style-2' ) ) {
							$msg = $( '.after-loading-success-message .success-message-container' ).eq( 0 );
							$msg.find( '.product-name' ).text( $link.closest( '.product' ).find( '.woocommerce-loop-product__title, .product-loop-title, .post-title a' ).text() );
						} else {
							$msg = $( '.after-loading-success-message .success-message-container' ).last().clone().removeClass( 'd-none' );
							$msg.find( '.product-name' ).empty().append( $link.closest( '.product' ).find( '.product-loop-title, .post-title a' ).clone() );
						}
						$msg.find( '.msg-box img' ).remove();
						if ( $link.closest( '.product' ).find( '.product-image img' ).length ) {
							var $img = $link.closest( '.product' ).find( '.product-image img' ).eq( 0 );
							$( '<img />' ).attr( 'src', $img.data( 'oi' ) ? $img.data( 'oi' ) : $img.attr( 'src' ) ).appendTo( $msg.find( '.msg-box' ) ).attr( 'alt', $msg.find( '.msg .product-name' ).text() );
						} else if ( typeof js_porto_vars.wc_placeholder_img != 'undefined' ) {
							$( '<img />' ).attr( 'src', js_porto_vars.wc_placeholder_img ).appendTo( $msg.find( '.msg-box' ) ).attr( 'alt', $msg.find( '.msg .product-name' ).text() );
						}
						$( '.after-loading-success-message' ).eq( 0 ).stop().show();

						if ( $link.hasClass( 'viewcart-style-2' ) ) {
							if ( porto_product_add_cart_timer ) {
								clearTimeout( porto_product_add_cart_timer );
							}
							porto_product_add_cart_timer = setTimeout( function() { $( '.after-loading-success-message' ).eq( 0 ).hide(); }, 4000 );
						} else {
							$msg.prependTo( '.after-loading-success-message' );
							theme.requestTimeout( function() {
								$msg.addClass( 'active' );
							}, 50 );
							setTimeout( function() { $msg.find( '.mfp-close' ).trigger( 'click' ); }, 5000 );
						}
					}
				} );
			} );

			$( '.after-loading-success-message .continue_shopping' ).on( 'click', function() {
				$( '.after-loading-success-message' ).eq( 0 ).fadeOut( 200 );
			} );
			$( '.after-loading-success-message' ).on( 'click', '.mfp-close', function() {
				var $obj = $( this ).closest( '.success-message-container' );
				$obj.removeClass( 'active' );
				theme.requestTimeout( function() {
					$obj.slideUp( 300, function() {
						$obj.remove();
					} );
				}, 350 );
			} );

			$( document.body ).on( 'click', '.variations_form .variations .filter-item-list .filter-color, .variations_form .variations .filter-item-list .filter-item', function( e ) {
				e.preventDefault();
				var $this = $( this ),
					$selector = $this.closest( 'ul' ).siblings( 'select' );
				if ( !$selector.length || $this.hasClass( 'disabled' ) ) {
					return;
				}
				var $li_obj = $this.closest( 'li' );
				if ( $li_obj.hasClass( 'active' ) ) {
					$li_obj.removeClass( 'active' );
					$selector.val( '' );
				} else {
					$li_obj.addClass( 'active' ).siblings().removeClass( 'active' );
					$selector.val( $this.data( 'value' ) );
				}
				$selector.trigger( 'change.wc-variation-form' );
			} );


			$( document.body ).on( 'click', '.porto-general-swatch .filter-color, .porto-general-swatch .filter-item', function( e ) {
				e.preventDefault();
				var $this = $( this ),
					$swatch_li = $this.parent();

				var $product = $( this ).closest( '.product, .product-col' );
				var $product_img;
				if ( $product.hasClass( 'porto-tb-item' ) ) { // in type builder
					$product_img = $product.find( '.porto-tb-featured-image img' ).eq( 0 );
				} else if ( $product.hasClass( 'product-col' ) ) { // product legacy loop
					$product_img = $product.find( 'div.product-image .inner img:first-child' );
				}

				var srcOrig    = $product_img.data( 'original-src' ),
				srcsetOrig = $product_img.data( 'original-srcset '),
				sizesOrig  = $product_img.data( 'original-sizes' );
				if ( typeof srcOrig == 'undefined' ) {
					$product_img.data( 'original-src', $product_img.attr( 'src' ) );
					if ( typeof srcsetOrig == 'undefined' && $product_img.attr( 'srcset' ) ) {
						$product_img.data( 'original-srcset', $product_img.attr( 'srcset' ) );
					}
		
					if ( typeof sizesOrig == 'undefined' && $product_img.attr( 'sizes' ) ) {
						$product_img.data( 'original-sizes', $product_img.attr( 'sizes' ) );
					}
				}

				var image_src = '',
					image_srcset = '',
					image_sizes = '';

				if ( $this.parent().hasClass( 'active' ) ) {
					$swatch_li.removeClass( 'active' );
				} else { // active
					$this.closest( 'ul' ).find( 'li' ).removeClass( 'active' );
					$swatch_li.addClass( 'active' );
					if ( $swatch_li.data( 'image-src' ) ) {
						image_src = $swatch_li.data( 'image-src' );
						image_srcset = $swatch_li.data( 'image-srcset' );
						image_sizes = $swatch_li.data( 'image-sizes' );
					}
				}
				if ( ! image_src ) {
					var $active_swatch = $this.closest( '.porto-general-swatch' ).find( 'li.active' );
					if ( $active_swatch.length && $active_swatch.data( 'image-src' ) ) {
						image_src = $active_swatch.data( 'image-src' );
						image_srcset = $active_swatch.data( 'image-srcset' );
						image_sizes = $active_swatch.data( 'image-sizes' );
					} else {
						if ( $product_img.data('original-src' ) ) {
							image_src = $product_img.data('original-src' );
						}
			
						if ( $product_img.data( 'original-srcset' ) ) {
							image_srcset = $product_img.data( 'original-srcset' );
						}
			
						if ( $product_img.data( 'original-sizes' ) ) {
							image_sizes = $product_img.data( 'original-sizes' );
						}
					}
				}
				if ( image_src ) {
					$product_img.attr( 'src', image_src ).attr( 'srcset', image_srcset ).attr( 'image_sizes', image_sizes );
					var $image_slider = $product.find( '.owl-carousel' );

					if ( $image_slider.length ) {
						$image_slider.trigger( 'to.owl.carousel', [0, 300, true] );
					}
				}
			});


			$( document ).on( 'wc_variation_form', '.variations_form', function() {
				$( this ).addClass( 'vf_init' );
				if ( $( this ).find( '.filter-item-list' ).length < 1 ) {
					return;
				}
				$( this ).find( '.variations select' ).trigger( 'focusin' );
			} );
			$( document ).on( 'updated_wc_div', function() {
				if ( $.fn.themePluginLazyLoad ) {
					$( '.woocommerce-cart-form .porto-lazyload' ).themePluginLazyLoad();
				}
			} );
			$( document ).on( 'found_variation reset_data', '.variations_form', function( e, args ) {
				// attribute description
				var $this = $( this );
				if ( $this.find( '.product-attr-description' ).length ) {
					if ( typeof args == 'undefined' ) {
						$this.find( '.product-attr-description' ).removeClass( 'active' );
					} else {
						$this.find( '.product-attr-description' ).addClass( 'active' );
						$this.find( '.product-attr-description .attr-desc' ).removeClass( 'active' );
						$this.find( '.variations select' ).each( function() {
							var $obj = $( this );
							$this.find( '.product-attr-description .attr-desc[data-attrid="' + $obj.val() + '"]' ).addClass( 'active' );
						} );
					}
				}

				if ( $this.find( ".filter-item-list" ).length < 1 ) {
					return;
				}
				$this.find( ".filter-item-list" ).each( function() {
					if ( $( this ).next( "select" ).length < 1 ) {
						return;
					}
					var selector = $( this ).next( "select" ),
						//html = '',
						$list = $( this );
					$list.find( 'li.active' ).removeClass( 'active' );
					$list.find( '.filter-color, .filter-item' ).removeClass( 'enabled' ).removeClass( 'disabled' );
					selector.children( "option" ).each( function() {
						/*var isColor = typeof $(this).data('color') != 'undefined' ? true : false,
							isImage = typeof $(this).data('image') != 'undefined' ? true : false,
						spanClass = isColor ? "filter-color" : ( isImage ? "filter-item filter-image" : "filter-item" );*/
						if ( !$( this ).val() ) {
							return;
						}
						$list.find( '[data-value="' + $( this ).val().replace(/"/g, '\\\"') + '"]' ).addClass( 'enabled' );
						if ( $( this ).val() == selector.val() ) {
							$list.find( '[data-value="' + $( this ).val().replace(/"/g, '\\\"') + '"]' ).parent().addClass( 'active' );
						}
						/*html += '<li';
						if ($(this).val() == selector.val()) {
							html += ' class="active"';
						}
						html += '><a href="#" data-value="'+ escape( $(this).val() ) +'" class="' + spanClass + '"';
						if (isColor) {
							html += ' style="background-color: #' + escape( $(this).data('color').replace('#','') ) + '"';
						}
						if (isImage) {
							html += ' style="background-image:url(' + $(this).data('image') + ')"';
						}
						html += '>';
						if (!isColor) {
							html += $(this).text();
						}
						html += '</a></li>';*/
					} );
					$list.find( '.filter-color:not(.enabled), .filter-item:not(.enabled)' ).addClass( 'disabled' );
					//$(this).html(html);
				} );
			} );

			// daily sale
			$( document ).on( 'found_variation reset_data', '.variations_form', function( e, obj ) {
				var $wrapper = $( this ).closest( '.product' ),
					$timer = $wrapper.find( '.sale-product-daily-deal.for-some-variations' );
				if ( !$timer.length ) {
					$timer = $wrapper.find( '.porto-product-sale-timer' ).eq( 0 );
					if ( !$timer.length ) {
						return;
					}
				}
				if ( obj && obj.is_purchasable && typeof obj.porto_date_on_sale_to != 'undefined' && obj.porto_date_on_sale_to ) {
					var saleTimer = $timer.find( '.porto_countdown-dateAndTime' );
					if ( saleTimer.data( 'terminal-date' ) != obj.porto_date_on_sale_to ) {
						var newDate = new Date( obj.porto_date_on_sale_to );
						saleTimer.porto_countdown( 'option', { until: newDate } );
						saleTimer.data( 'terminal-date', obj.porto_date_on_sale_to );
					}
					$timer.slideDown();
				} else {
					if ( $timer.is( ':hidden' ) ) {
						$timer.hide();
					} else {
						$timer.slideUp();
					}
				}
			} );

			$( 'body' ).on( 'click', '.product-attr-description > a', function( e ) {
				e.preventDefault();
				$( this ).next().stop().slideToggle( 400 );
			} );

			// if product was already added to cart, show check icon in add to cart button and view cart button in single product page
			if ( $( document.body ).hasClass( 'single-product' ) ) {
				$( document ).on( 'woocommerce_variation_has_changed', '.variations_form', function( e, variation ) {
					$( document.body ).removeClass( 'single-add-to-cart' );
				} );
				$( document ).on( 'found_variation', '.variations_form', function( e, variation ) {
					try {
						var cart_items = JSON.parse( sessionStorage.getItem( wc_cart_fragments_params.fragment_name ) );
						if ( cart_items['div.widget_shopping_cart_content'] ) {
							var cart_item = $( cart_items['div.widget_shopping_cart_content'] ).find( '.porto-variation-' + variation.variation_id );
							if ( cart_item.length ) {
								theme.requestFrame( function() {
									$( document.body ).addClass( 'single-add-to-cart' );
								} );
							}
						}
					} catch ( e ) {
					}
				} );
			}

			// Mini Cart Quantity on Cart Popup or Offcanvas
			var timeout;

			$( document ).on( 'change input', '.cart_list .quantity .qty, .woocommerce-checkout-review-order-table .quantity .qty', function() {
				var input = $(this);
				var itemID = '';
				var qtyVal = input.val();
				var maxValue = input.attr( 'max' );
				var is_checkout = false;
	
				clearTimeout( timeout );
	
				if ( parseInt( qtyVal ) > parseInt( maxValue ) ) {
					qtyVal = maxValue;
				}
				if ( input.closest( '.cart_list' ).length ) {
					itemID = input.parents( '.woocommerce-mini-cart-item' ).data( 'key' );
				} else {
					is_checkout = true;
					itemID = input.closest( '.cart_item' ).data( 'key' );
				}
				timeout = setTimeout( function() {
					if ( ! is_checkout ) {
						input.parents( '.mini_cart_item' ).find( '.ajax-loading' ).show();
					}
					$.ajax( {
						url     : theme.ajax_url,
						data    : {
							action : 'porto_update_cart_item',
							item_id: itemID,
							qty    : qtyVal
						},
						success : function( data ) {
							if ( data && data.fragments ) {
								updateCartFragment( data );
								$( document.body ).trigger( 'wc_fragments_refreshed' );
							}
							if ( is_checkout ) {
								input.closest( 'form.checkout' ).trigger( 'update' );
							} else {
								input.parents( '.mini_cart_item' ).find( '.ajax-loading' ).hide();
							}
						},
						dataType: 'json',
						method  : 'GET'
					} );
				}, 500 );
			} );

			if ( $( 'form.woocommerce-cart-form button.update-button-hidden' ).length > 0 ) {
				$( document ).on( 'change input', 'form.woocommerce-cart-form .product-quantity input', function () {
					var $form = $( this ).closest( 'form' ),
						$updateBtn = $form.find( '.update-button-hidden' );

					$updateBtn.removeAttr( 'disabled' );
					$updateBtn.click();
				} );
			}

			// fix dummy yith ajax requests on first load
			if ( 'undefined' !== typeof yith_wcwl_l10n && yith_wcwl_l10n.reload_on_found_variation && $('.products-container .variations_form').length ) {
				var porto_update_reload_wishlist_fn = function() {
					yith_wcwl_l10n.reload_on_found_variation = true;
					if ( $(this).closest( '.products-container' ).length ) {
						yith_wcwl_l10n.reload_on_found_variation = false;
					}
				};
				$('.products-container').one( 'woocommerce_variation_has_changed woocommerce_variation_select_change', '.variations_form', function() {
					yith_wcwl_l10n.reload_on_found_variation = true;
					$( document ).off( 'update_variation_values', '.variations_form', porto_update_reload_wishlist_fn );
				} );
				$( document ).on( 'update_variation_values', '.variations_form', porto_update_reload_wishlist_fn );
			}
		} );

	} ).apply( this, [window.theme, jQuery] );


	// Woocommerce Product Image Slider
	( function( theme, $ ) {

		theme = theme || {};

		var duration = 300,
			flag = false;

		$.extend( theme, {

			WooProductImageSlider: {

				defaults: {
					elements: '.product-image-slider'
				},

				initialize: function( $elements ) {

					this.$elements = ( $elements || $( this.defaults.elements ) );
					if ( !this.$elements.length && !$( '.product-images-block' ).length ) {
						return this;
					}
					// if ( theme.isMobile() ) {
					// 	let $zoom = this.$elements.siblings( '.zoom' );
					// 	if ( $zoom.length ) {
					// 		this.$elements.off( 'click' ).on( 'click', function() {
					// 			$zoom.trigger( 'click' );
					// 		} );
					// 	}
					// }
/*
					if ( !$.fn.owlCarousel ) {

						this.$elements.each(function(){
							var links = [],
								$this = $( this );
							if ( theme.product_image_popup ) {
								var i = 0;
								$this.find( 'img' ).each( function() {
									var slide = {};

									slide.src = $( this ).attr( 'href' );
									slide.title = $( this ).attr( 'alt' );
									slide.w = parseInt( $( this ).attr( 'data-large_image_width' ) );
									slide.h = parseInt( $( this ).attr( 'data-large_image_height' ) );
									
									links[i] = slide;
									i++;
								} );
							}

							$this.data( 'links', links );

							if ( theme.product_image_popup ) {
								var $zoom_buttons = $this.siblings( '.zoom' );
								$zoom_buttons.off( 'click' ).on( 'click', function( e ) {
									if ( ! $this.data( 'links' ).length ) {
										return;
									}
									e.preventDefault();
									if ( typeof PhotoSwipe == 'undefined' ) {
										return;
									}
									var options = $.extend( {
										index: 0,
										addCaptionHTMLFn: function( item, captionEl ) {
											if ( ! item.title ) {
												captionEl.children[0].textContent = '';
												return false;
											}
											captionEl.children[0].textContent = item.title;
											return true;
										}
									}, wc_single_product_params.photoswipe_options );

									// Initializes and opens PhotoSwipe.
									var photoswipe = new PhotoSwipe( $( '.pswp' )[0], PhotoSwipeUI_Default, $this.data( 'links' ), options );
									photoswipe.init();

								} );
							}
						});
						return false;
					}
*/

					this.build();

					return this;
				},

				build: function() {
					var self = this,
						thumbs_count = theme.product_thumbs_count;

					if ( theme.product_zoom && ( !( 'ontouchstart' in document ) || ( ( 'ontouchstart' in document ) && theme.product_zoom_mobile ) ) ) {
						var zoomConfig = {
							responsive: true,
							zoomWindowFadeIn: 200,
							zoomWindowFadeOut: 100,
							zoomType: js_porto_vars.zoom_type,
							cursor: 'grab'
						};

						if ( js_porto_vars.zoom_type == 'lens' ) {
							zoomConfig.scrollZoom = js_porto_vars.zoom_scroll;
							zoomConfig.lensSize = js_porto_vars.zoom_lens_size;
							zoomConfig.lensShape = js_porto_vars.zoom_lens_shape;
							zoomConfig.containLensZoom = js_porto_vars.zoom_contain_lens;
							zoomConfig.lensBorderSize = js_porto_vars.zoom_lens_border;
							zoomConfig.borderColour = js_porto_vars.zoom_border_color;
						}

						if ( js_porto_vars.zoom_type == 'inner' ) {
							zoomConfig.borderSize = 0;
						} else {
							zoomConfig.borderSize = js_porto_vars.zoom_border;
						}
						zoomConfig.zoomActivation = 'dbltouch';

						if ( !self.$elements.length ) {
							var $images_grid = $( '.product-images-block' );
							if ( $images_grid.length ) {
								self.initZoom( $images_grid, zoomConfig );
							}
						}
					}

					self.$elements.each( function() {
						var $this = $( this ),
							$product = $this.closest( '.product' );
						if ( !$product.length ) {
							$product = $this.closest( '.product_layout, .product-layout-image' ).eq( 0 );
						}
						var $thumbs_slider = $product.find( '.product-thumbs-slider' ),
							$thumbs = $product.find( '.product-thumbnails-inner' ),
							$thumbs_vertical_slider = $product.find( '.product-thumbs-vertical-slider' ),
							currentSlide = 0,
							count = $this.find( '> *' ).length;

						$this.find( '> *:first-child' ).imagesLoaded( function() {

							var links = [];
							if ( theme.product_image_popup ) {
								var i = 0;
								$this.find( '.img-thumbnail img' ).each( function() {
									var slide = {}, _imageItem = $( this );
									if ( _imageItem.closest( '.vd-image' ).length ) {
										return;
									}
									slide.src = _imageItem.attr( 'href' );
									slide.title = _imageItem.attr( 'alt' );
									slide.w = parseInt( _imageItem.attr( 'data-large_image_width' ) );
									slide.h = parseInt( _imageItem.attr( 'data-large_image_height' ) );
									
									links[i] = slide;
									i++;
								} );
							}

							if ( $.fn.owlCarousel ) {
								$thumbs_slider.removeClass( 'has-ccols-spacing' );
								$thumbs_slider.owlCarousel( {
									rtl: theme.rtl,
									loop: false,
									autoplay: false,
									items: thumbs_count,
									nav: false,
									navText: ["", ""],
									dots: false,
									rewind: true,
									margin: 8,
									stagePadding: 1,
									lazyLoad: true,
									onInitialized: function() {
										self.selectThumb( null, $thumbs_slider, 0 );
										if ( $thumbs_slider.find( '.owl-item' ).length >= thumbs_count )
											$thumbs_slider.append( '<div class="thumb-nav"><div class="thumb-prev"></div><div class="thumb-next"></div></div>' );
									}
								} ).on( 'click', '.owl-item', function() {
									self.selectThumb( $this, $thumbs_slider, $( this ).index() );
								} );
								if ( $thumbs_vertical_slider.length > 0 && typeof $.fn.slick == 'function' ) {
									var slickOptions = {
										dots: false,
										vertical: true,
										slidesToShow: thumbs_count,
										slidesToScroll: 1,
										infinite: false,
									}
									if ( thumbs_count >= 5 ) {
										slickOptions['responsive'] = [
											{
												breakpoint: 992,
												settings: {
													slidesToShow: 4,
												}
											},
											{
												breakpoint: 768,
												settings: {
													slidesToShow: 3,
												}
											}
										]
									}
									$thumbs_vertical_slider.slick( slickOptions ).on( 'click', '.slick-slide', function() {
										self.selectVerticalSliderThumb( $this, $thumbs_vertical_slider, $( this ).data( 'slick-index' ) );
									} );
									$( '.product-layout-transparent .product-image-slider' ).on( 'resized.owl.carousel', function() {
										$( window ).trigger( 'resize.slick' );
									} );

									self.selectVerticalSliderThumb( null, $thumbs_vertical_slider, 0 );
									if ( $thumbs_vertical_slider.find( '.porto-lazyload' ).length ) {
										theme.requestTimeout( function() {
											$thumbs_vertical_slider.find( '.slick-cloned .porto-lazyload:not(.lazy-load-loaded)' ).each( function() {
												$( this ).attr( 'src', $( this ).data( 'oi' ) ).removeAttr( 'data-oi' ).addClass( 'lazy-load-loaded' );
											} );
										}, 100 );
									}
								}

								self.selectVerticalThumb( null, $thumbs, 0 );
								$thumbs.off( 'click', '.img-thumbnail' ).on( 'click', '.img-thumbnail', function() {
									self.selectVerticalThumb( $this, $thumbs, $( this ).index() );
								} );

								$thumbs_slider.off( 'click', '.thumb-prev' ).on( 'click', '.thumb-prev', function( e ) {
									var currentThumb = $thumbs_slider.data( 'currentThumb' );
									self.selectThumb( $this, $thumbs_slider, --currentThumb );
								} );
								$thumbs_slider.off( 'click', '.thumb-next' ).on( 'click', '.thumb-next', function( e ) {
									var currentThumb = $thumbs_slider.data( 'currentThumb' );
									self.selectThumb( $this, $thumbs_slider, ++currentThumb );
								} );

								var itemsCount = typeof $this.data( 'items' ) != 'undefined' ? $this.data( 'items' ) : 1,
									itemsResponsive = typeof $this.data( 'responsive' ) != 'undefined' ? $this.data( 'responsive' ) : {},
									centerItem = typeof $this.data( 'centeritem' ) != 'undefined' ? true : false,
									margin = typeof $this.data( 'margin' ) != 'undefined' ? $this.data( 'margin' ) : 0,
									loop = ( count > 1 ) ? ( typeof $this.data( 'loop' ) != 'undefined' ? $this.data( 'loop' ) : true ) : false;
								for ( var itemCount in itemsResponsive ) {
									itemsResponsive[itemCount] = { items: itemsResponsive[itemCount] };
								}
								$this.removeClass( 'has-ccols-spacing' );
								$this.owlCarousel( {
									rtl: theme.rtl,
									loop: loop,
									autoplay: false,
									items: itemsCount,
									margin: margin,
									responsive: itemsResponsive,
									autoHeight: true,
									nav: true,
									navText: ["", ""],
									dots: false,
									rewind: true,
									lazyLoad: true,
									center: centerItem,
									onInitialized: function() {
										if ( $this.find( '.owl-item.cloned' ).length ) {
											setTimeout( function() {
												if ( $.fn.themePluginLazyLoad ) {
													var ins = $this.find( '.owl-item.cloned .porto-lazyload:not(.lazy-load-loaded)' ).themePluginLazyLoad( { effect: 'fadeIn', effect_speed: 400 } );
													if ( ins && ins.loadAndDestroy ) {
														ins.loadAndDestroy();
													}
												}
											}, 100 );
										}
										//$this.find('.cloned .porto-lazyload:not(.lazy-load-loaded)').themePluginLazyLoad();
											self.initZoom( $this, zoomConfig );
									},
									onTranslate: function( event ) {
										currentSlide = event.item.index - $this.find( '.cloned' ).length / 2;
										currentSlide = ( currentSlide + event.item.count ) % event.item.count;
										self.selectThumb( null, $thumbs_slider, currentSlide );
										self.selectVerticalThumb( null, $thumbs, currentSlide );
										self.selectVerticalSliderThumb( null, $thumbs_vertical_slider, currentSlide );

										/*var $obj = event.relatedTarget.items(currentSlide).find('img.owl-lazy:not(.owl-lazy-loaded)');
										if ($obj.length) {
											var src = $obj.attr('href'),
												elevateZoom = $obj.data('elevateZoom'),
												smallImage = $obj.data('src') ? $obj.data('src') : $obj.attr('src');
											if (typeof elevateZoom != 'undefined') {
												elevateZoom.swaptheimage(smallImage, src);
											}
										}*/
									},
									onRefreshed: function() {
										if ( theme.product_zoom && ( !( 'ontouchstart' in document ) || ( ( 'ontouchstart' in document ) && theme.product_zoom_mobile ) ) ) {
											$this.find( 'img' ).each( function() {
												var $this = $( this ),
													src = typeof $this.attr( 'href' ) != 'undefined' ? $this.attr( 'href' ) : ( $this.data( 'oi' ) ? $this.data( 'oi' ) : $this.attr( 'src' ) ),
													elevateZoom = $this.data( 'elevateZoom' ),
													smallImage = $this.data( 'src' ) ? $this.data( 'src' ) : ( $this.data( 'oi' ) ? $this.data( 'oi' ) : $this.attr( 'src' ) );
												if ( typeof elevateZoom != 'undefined' ) {
													elevateZoom.startZoom();
													elevateZoom.swaptheimage( smallImage, src );
												} else if ( $.fn.elevateZoom ) {
													zoomConfig.zoomContainer = $this.parent();
													if ( ! $this.closest( '.vd-image' ).length ) {
														$this.elevateZoom( zoomConfig );
													}
												}
											} );
										}
									}
								} );
							} else {
								self.initZoom( $this, zoomConfig );
							}

							$this.data( 'links', links );

							if ( theme.product_image_popup ) {
								var $zoom_buttons = $this.siblings( '.zoom' );
								$zoom_buttons.off( 'click' ).on( 'click', function( e ) {
									if ( ! $this.data( 'links' ).length ) {
										return;
									}
									e.preventDefault();
									if ( typeof PhotoSwipe == 'undefined' ) {
										return;
									}
									var options = $.extend( {
										index: currentSlide ? currentSlide : 0,
										addCaptionHTMLFn: function( item, captionEl ) {
											if ( ! item.title ) {
												captionEl.children[0].textContent = '';
												return false;
											}
											captionEl.children[0].textContent = item.title;
											return true;
										}
									}, wc_single_product_params.photoswipe_options );

									// Initializes and opens PhotoSwipe.
									var photoswipe = new PhotoSwipe( $( '.pswp' )[0], PhotoSwipeUI_Default, $this.data( 'links' ), options );
									photoswipe.init();

								} );
							}

						} );
					} );

					return self;
				},

				selectThumb: function( $image_slider, $thumbs_slider, index ) {
					if ( flag || !$thumbs_slider.length ) return;

					flag = true;
					var len = $thumbs_slider.find( '.owl-item' ).length,
						actives = [],
						i = 0;

					index = ( index + len ) % len;
					if ( $image_slider ) {
						$image_slider.trigger( 'to.owl.carousel', [index, duration, true] );
					}
					$thumbs_slider.find( '.owl-item' ).removeClass( 'selected' );
					$thumbs_slider.find( '.owl-item:eq(' + index + ')' ).addClass( 'selected' );
					$thumbs_slider.data( 'currentThumb', index );
					$thumbs_slider.find( '.owl-item.active' ).each( function() {
						actives[i++] = $( this ).index();
					} );
					if ( $.inArray( index, actives ) == -1 ) {
						if ( Math.abs( index - actives[0] ) > Math.abs( index - actives[actives.length - 1] ) ) {
							$thumbs_slider.trigger( 'to.owl.carousel', [( index - actives.length + 1 ) % len, duration, true] );
						} else {
							$thumbs_slider.trigger( 'to.owl.carousel', [index % len, duration, true] );
						}
					}
					flag = false;
				},

				selectVerticalSliderThumb: function( $image_slider, $thumbs_vertical_slider, index ) {
					if ( flag || !$thumbs_vertical_slider.length ) return;
					flag = true;
					if ( 'undefined' == typeof $thumbs_vertical_slider[0].slick ) {
						return;
					}
					var len = $thumbs_vertical_slider[0].slick.slideCount,
						actives = [],
						i = 0;
					index = ( index + len ) % len;
					if ( $image_slider ) {
						$image_slider.trigger( 'to.owl.carousel', [index, duration, true] );
					}
					$thumbs_vertical_slider.find( '.slick-slide' ).removeClass( 'selected' );
					$thumbs_vertical_slider.find( '.slick-slide:eq(' + index + ')' ).addClass( 'selected' );
					$thumbs_vertical_slider.data( 'currentThumb', index );
					$thumbs_vertical_slider.find( '.slick-slide.slick-active' ).each( function() {
						actives[i++] = $( this ).index();
					} );
					if ( $.inArray( index, actives ) == -1 ) {
						if ( Math.abs( index - actives[0] ) > Math.abs( index - actives[actives.length - 1] ) ) {
							$thumbs_vertical_slider.get( 0 ).slick.goTo( ( index - actives.length + 1 ) % len, false );
						} else {
							$thumbs_vertical_slider.get( 0 ).slick.goTo( index % len, false );
						}
					}
					flag = false;
				},

				selectVerticalThumb: function( $image_slider, $thumbs, index ) {
					if ( flag || !$thumbs.length ) return;
					flag = true;
					var len = $thumbs.find( '.img-thumbnail' ).length,
						i = 0;

					index = ( index + len ) % len;
					if ( $image_slider ) {
						$image_slider.trigger( 'to.owl.carousel', [index, duration, true] );
					}
					$thumbs.find( '.img-thumbnail' ).removeClass( 'selected' );
					$thumbs.find( '.img-thumbnail:eq(' + index + ')' ).addClass( 'selected' );
					$thumbs.data( 'currentThumb', index );
					flag = false;
				},

				initZoom: function( $this, zoomConfig ) {
					if ( theme.product_zoom && ( !( 'ontouchstart' in document ) || ( ( 'ontouchstart' in document ) && theme.product_zoom_mobile ) ) ) {
						$this.find( 'img' ).each( function() {
							var $this = $( this );
							zoomConfig.zoomContainer = $this.parent();
							if ( $.fn.elevateZoom ) {
								if ( ! $this.closest( '.vd-image' ).length ) {
									$this.elevateZoom( zoomConfig );
								}
							} else {
								setTimeout( function() {
									if ( $.fn.elevateZoom ) {
										if ( ! $this.closest( '.vd-image' ).length ) {
											$this.elevateZoom( zoomConfig );
										}
									}
								}, 1000 );
							}
						} );
					}
				}
			}

		} );

	} ).apply( this, [window.theme, jQuery] );


	// Woocommerce Quick View
	( function( theme, $ ) {

		theme = theme || {};

		$.extend( theme, {

			WooQuickView: {

				initialize: function() {

					this.events();

					return this;
				},

				events: function() {
					var self = this;

					$( document ).on( 'click', '.quickview', function( e ) {
						e.preventDefault();

						if ( !$.fn.elevateZoom && !$( '#porto-script-jquery-elevatezoom' ).length ) {
							var js = document.createElement( 'script' );
							js.id = 'porto-script-jquery-elevatezoom';
							$( js ).appendTo( 'body' ).attr( 'src', js_porto_vars.ajax_loader_url.replace( '/images/ajax-loader@2x.gif', '/js/libs/jquery.elevatezoom.min.js' ) );
						}

						var $this = $( this ),
							pid = $this.attr( 'data-id' );

						function init_quick_view_window() {

							var args = {
								href: theme.ajax_url,
								ajax: {
									data: {
										action: 'porto_product_quickview',
										variation_flag: typeof wc_add_to_cart_variation_params !== 'undefined',
										pid: pid,
										nonce: js_porto_vars.porto_nonce
									}
								},
								type: 'ajax',
								helpers: {
									overlay: {
										locked: true,
										fixed: true
									}
								},
								tpl: {
									error: '<p class="fancybox-error">' + theme.request_error + '</p>',
									closeBtn: '<a title="' + js_porto_vars.popup_close + '" class="fancybox-item fancybox-close" href="javascript:;"></a>',
									next: '<a title="' + js_porto_vars.popup_next + '" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
									prev: '<a title="' + js_porto_vars.popup_prev + '" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
								},
								autoSize: true,
								autoWidth: true,
								afterShow: function( flag ) {
									theme.requestTimeout( function() {
										var $quickviewEl = $( '.quickview-wrap-' + pid );
										if ( $quickviewEl.length == 0 && $( '.woocommerce-wishlist' ).length ) { // if select variation on wishlist page
											$quickviewEl = $( '.quickview-wrap' );
										}
										if ( typeof flag == 'undefined' || flag ) {
											porto_woocommerce_init();
										}
										theme.WooProductImageSlider.initialize( $quickviewEl.find( '.product-image-slider' ) );

										// compatibility issue with Yith WooCommerce Booking form
										if ( $( document.body ).hasClass( 'yith-booking' ) ) {
											$( document ).trigger( 'yith-wcbk-init-booking-form' );
										}

										// Variation Form
										var form_variation = $quickviewEl.find( 'form.variations_form' );
										if ( form_variation.length > 0 ) {
											form_variation.wc_variation_form();
										}

										// Enqueue Countdown, Countdown-loader
										if ( $( '.quickview-wrap-' + pid + ' .porto_countdown' ).length && ! theme.isFirstLoad && typeof $.fn.porto_countdown == 'undefined' ) {
											theme.isFirstLoad = true;
											var scripts = ['countdown.min.js', 'countdown-loader.min.js'];
											for ( let index = 0; index < scripts.length; index++ ) {
												if ( !document.getElementById( scripts[index] ) ) {
													var wf, script;
													wf = document.createElement( 'script' );
													script = document.scripts[0];
													wf.id = scripts[index];
													wf.src = js_porto_vars.func_url + 'shortcodes/assets/js/' + scripts[index];
													script.parentNode.insertBefore( wf, script );
												}
											}
										}
										$( document.body ).trigger( 'porto_init_countdown', [$quickviewEl] );

										// Ajax load at the first by Yith Wishlist Plugin
										if ( ( 'undefined' !== typeof yith_wcwl_l10n ) && yith_wcwl_l10n.enable_ajax_loading ) {
											if ( $( '.fancybox-opened .wishlist-fragment' ).length ) {
												var options = {},
													$product = $( '.fancybox-opened .wishlist-fragment' ),
													id = $product.attr( 'class' ).split( ' ' ).filter( ( val ) => {
														return val.length && val !== 'exists';
													} ).join( yith_wcwl_l10n.fragments_index_glue );
												options[id] = $product.data( 'fragment-options' );

												if ( !options ) {
													return;
												}

												var ajaxData = {
													action: yith_wcwl_l10n.actions.load_fragments,
													context: 'frontend',
													fragments: options
												};

												if ( typeof yith_wcwl_l10n.nonce != 'undefined' ) {
													ajaxData.nonce = yith_wcwl_l10n.nonce.load_fragments_nonce;
												}

												$.ajax( {
													ajaxData,
													method: 'post',
													success: function( data ) {
														if ( typeof data.fragments !== 'undefined' ) {
															$.each( data.fragments, function( i, v ) {
																var itemSelector = '.' + i.split( yith_wcwl_l10n.fragments_index_glue ).filter( ( val ) => { return val.length && val !== 'exists' && val !== 'with-count'; } ).join( '.' ),
																	toReplace = $( itemSelector );

																// find replace tempalte
																var replaceWith = $( v ).filter( itemSelector );

																if ( !replaceWith.length ) {
																	replaceWith = $( v ).find( itemSelector );
																}

																if ( toReplace.length && replaceWith.length ) {
																	toReplace.replaceWith( replaceWith );
																}
															} );
														}
													},
													url: yith_wcwl_l10n.ajax_url
												} );
											}
										}
										// Compatibility with Woo Variation Gallery
										$( document ).trigger( 'woo_variation_gallery_init' );
									}, 200 );
								},
								onUpdate: function() {
									theme.requestTimeout( function() {
										var $quickviewEl = $( '.quickview-wrap-' + pid );
										if ( $quickviewEl.length == 0 && $( '.woocommerce-wishlist' ).length ) { // if select variation on wishlist page
											$quickviewEl = $( '.quickview-wrap' );
										}
										if ( js_porto_vars.use_skeleton_screen.indexOf( 'quickview' ) == -1 || !js_porto_vars.quickview_skeleton ) {
											porto_woocommerce_init();
										}
										var $slider = $quickviewEl.find( '.product-image-slider' );
										if ( typeof $slider.data( 'owl.carousel' ) != 'undefined' && typeof $slider.data( 'owl.carousel' )._invalidated != 'undefined' )
											$slider.data( 'owl.carousel' )._invalidated.width = true;
										$slider.trigger( 'refresh.owl.carousel' );
										$( document.body ).trigger( 'porto_init_countdown', [$quickviewEl] );
									}, 300 );
								}
							};
							if ( js_porto_vars.use_skeleton_screen.indexOf( 'quickview' ) != -1 && js_porto_vars.quickview_skeleton ) {
								delete args['href'];
								delete args['ajax'];
								args['type'] = 'inline';
								$.fancybox.open(
									js_porto_vars.quickview_skeleton,
									args
								);
								$.ajax( {
									url: theme.ajax_url,
									type: 'post',
									dataType: 'html',
									data: {
										action: 'porto_product_quickview',
										variation_flag: typeof wc_add_to_cart_variation_params !== 'undefined',
										pid: pid,
										nonce: js_porto_vars.porto_nonce
									},
									success: function( res ) {
										var $res = $( res );
										$res.imagesLoaded( function() {
											$( '.skeleton-body.product' ).replaceWith( $res );
											theme.WooQtyField.initialize();
											$( window ).trigger( 'resize' );
											args['afterShow'].call( false );
										} );
									}
								} );
							} else {
								if ( typeof $.fancybox == 'function' ) {
								$.fancybox( args );
								} else if ( typeof $.fancybox == 'object' && $.fancybox.version && 0 === $.fancybox.version.indexOf( '3' ) ) {
									args['src'] = args['href'];
									args['ajax']['settings'] = {
										data: args['ajax']['data']
									};
									$.fancybox.open( args );
								}
							}
						}

						if ( $.fn.fancybox ) {
							init_quick_view_window();
						} else if ( !$( '#porto-script-jquery-fancybox' ).length ) {
							var js1 = document.createElement( 'script' );
							js1.id = 'porto-script-jquery-fancybox';
							$( js1 ).appendTo( 'body' ).on( 'load', function() {
								init_quick_view_window();
							} ).attr( 'src', js_porto_vars.ajax_loader_url.replace( '/images/ajax-loader@2x.gif', '/js/libs/jquery.fancybox.min.js' ) );
						}

						return false;
					} );

					// ajax add to cart on quickview
					if ( typeof wc_add_to_cart_params != 'undefined' ) {
						$( document.body ).on( 'click', '.single-product .single_add_to_cart_button:not(.disabled, .wpcbn-btn)', function( e ) {
							if ( $( this ).closest( '.single-product' ).hasClass( 'product-type-external' ) || $( this ).closest( '.single-product' ).hasClass( 'product-type-grouped' ) ) {
								return true;
							}
							if ( $( this ).hasClass( 'readmore' ) ) {
								return true;
							}
							e.preventDefault();

							var $button = $( this ),
								product_id = $button.val(),
								variation_id = $button.closest( 'form' ).find( 'input[name="variation_id"]' ).val(),
								quantity = $button.closest( 'form' ).find( 'input[name="quantity"]' ).val();
							if ( $button.hasClass( 'loading' ) ) {
								return false;
							}
							$button.removeClass( 'added' );
							$button.addClass( 'loading' );
							$button.parent().addClass( 'porto-ajax-loading' );
							if ( !$button.siblings( '.porto-loading-icon' ).length ) {
								let $last = $button.siblings( 'button:last-of-type' );
								$( '<span class="porto-loading-icon"></span>' ).insertAfter( $last.length ? $last : $button );
							}

							var data = {
								action: 'porto_add_to_cart',
								product_id: variation_id ? variation_id : product_id,
								quantity: quantity
							};
							if ( variation_id ) {
								var $variations = $button.closest( 'form' ).find( '.variations select' );
								if ( $variations.length ) {
									$variations.each( function() {
										var name = $( this ).data( 'attribute_name' ),
											val = $( this ).val();
										if ( name && val ) {
											data[name] = val;
										}
									} );
								}
							}

							// Trigger event.
							$( document.body ).trigger( 'adding_to_cart', [$button, data] );

							$.ajax( {
								type: 'POST',
								url: theme.ajax_url,
								data: data,
								dataType: 'json',
								success: function( response ) {
									$button.parent().removeClass( 'porto-ajax-loading' );
									if ( !response ) {
										return;
									}
									if ( response.error && response.product_url ) {
										window.location = response.product_url;
										return;
									}
									// Redirect to cart option
									if ( wc_add_to_cart_params.cart_redirect_after_add === 'yes' ) {
										window.location = wc_add_to_cart_params.cart_url;
										return;
									}

									// Trigger event.
									$( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash, $button] );
								}
							} );
						} );
					}

					return self;
				}
			}

		} );

	} ).apply( this, [window.theme, jQuery] );


	// Woocommerce Qty Field
	( function( theme, $ ) {

		theme = theme || {};

		$.extend( theme, {

			WooQtyField: {

				initialize: function() {

					this.build()
						.events();

					return this;
				},

				qty_handler: function() {
					var $obj = $( this );
					if ( $obj.closest( '.quantity' ).next( '.add_to_cart_button[data-quantity]' ).length ) {
						var count = $obj.val();
						if ( count ) {
							$obj.closest( '.quantity' ).next( '.add_to_cart_button[data-quantity]' ).attr( 'data-quantity', count );
						}
					}
				},

				build: function() {
					var self = this;

					// Quantity buttons
					$( 'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<button type="button" value="+" class="plus">+</button>' ).prepend( '<button type="button" value="-" class="minus">-</button>' );

					// Target quantity inputs on product pages
					$( 'input.qty:not(.product-quantity input.qty)' ).each( function() {
						var min = parseFloat( $( this ).attr( 'min' ) );

						if ( min && min > 0 && parseFloat( $( this ).val() ) < min ) {
							$( this ).val( min );
						}
					} );

					$( 'input.qty:not(.product-quantity input.qty)' ).off( 'change', self.qty_handler ).on( 'change', self.qty_handler );

					$( document ).off( 'click', '.quantity .plus, .quantity .minus' ).on( 'click', '.quantity .plus, .quantity .minus', function() {

						// Get values
						var $qty = $( this ).closest( '.quantity' ).find( '.qty' ),
							currentVal = parseFloat( $qty.val() ),
							max = parseFloat( $qty.attr( 'max' ) ),
							min = parseFloat( $qty.attr( 'min' ) ),
							step = $qty.attr( 'step' );

						// Format values
						if ( !currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
						if ( max === '' || max === 'NaN' ) max = '';
						if ( min === '' || min === 'NaN' ) min = 0;
						if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;

						// Change the value
						if ( $( this ).is( '.plus' ) ) {

							if ( max && ( max == currentVal || currentVal > max ) ) {
								$qty.val( max );
							} else {
								$qty.val( currentVal + parseFloat( step ) );
							}

						} else {

							if ( min && ( min == currentVal || currentVal < min ) ) {
								$qty.val( min );
							} else if ( currentVal > 0 ) {
								$qty.val( currentVal - parseFloat( step ) );
							}

						}

						// Trigger change event
						$qty.trigger( 'change' );
					} );

					return self;
				},

				events: function() {
					var self = this;

					$( document ).ajaxComplete( function( event, xhr, options ) {
						self.build();
					} );

					return self;
				}
			}

		} );

	} ).apply( this, [window.theme, jQuery] );


	// Woocommerce Variation Form
	( function( theme, $ ) {

		theme = theme || {};

		var duration = 300;

		$.extend( theme, {

			WooVariationForm: {

				initialize: function() {

					this.init().events();

					return this;
				},

				init: function() {
					$( '.variations_form' ).each( function() {
						var $variation_form = $( this ),
							$reset_variations = $variation_form.find( '.reset_variations' );

						if ( $reset_variations.css( 'visibility' ) == 'hidden' )
							$reset_variations.hide();
					} );
					return this;
				},

				events: function() {
					var self = this;

					$( document ).on( 'check_variations', '.variations_form', function( event, exclude, focus ) {
						var $variation_form = $( this ),
							$reset_variations = $variation_form.find( '.reset_variations' );

						if ( $reset_variations.css( 'visibility' ) == 'hidden' )
							$reset_variations.hide();
					} );

					$( document ).on( 'reset_image', '.variations_form', function( event ) {
						var $product = $( this ).closest( '.product, .product-col' ),
							$product_img = $product.find( 'div.product-images .woocommerce-main-image' );
						if ( $product.hasClass( 'porto-tb-item' ) ) { // in type builder
							$product_img = $product.find( '.porto-tb-featured-image img' ).eq( 0 );
						} else if ( $product.hasClass( 'product-col' ) ) { // shop pages
							$product_img = $product.find( 'div.product-image .inner img:first-child' );
						}
						var o_src = $product_img.attr( 'data-o_src' ),
							o_title = $product_img.attr( 'data-o_title' ),
							o_href = $product_img.attr( 'data-o_href' ),
							$thumb_img = $product.find( '.woocommerce-main-thumb' ),
							o_thumb_src = $thumb_img.attr( 'data-o_src' );

						var $image_slider = $product.find( '.product-image-slider' ),
							$thumbs_slider = $product.find( '.product-thumbs-slider' ),
							links;

						if ( $image_slider.length ) {
							$image_slider.trigger( 'to.owl.carousel', [0, duration, true] );
							links = $image_slider.data( 'links' );
						}
						if ( $thumbs_slider.length ) {
							$thumbs_slider.trigger( 'to.owl.carousel', [0, duration, true] );
							$thumbs_slider.find( '.owl-item:eq(0)' ).trigger( 'click' );
						}

						if ( o_src ) {
							$product_img
								.attr( 'src', o_src )
								.attr( 'srcset', '' )
								.attr( 'alt', o_title )
								.attr( 'href', o_href );

							$product_img.each( function() {
								var elevateZoom = $( this ).data( 'elevateZoom' );
								if ( typeof elevateZoom != 'undefined' ) {
									elevateZoom.swaptheimage( $( this ).attr( 'src' ), $( this ).attr( 'src' ) );
								}
							} );

							if ( theme.product_image_popup && typeof links != 'undefined' ) {
								links[0].src = o_href;
								links[0].title = o_title;
							}
						}
						if ( o_thumb_src ) {
							$thumb_img.attr( 'src', o_thumb_src );
						}
					} );

					$( document ).on( 'found_variation', '.variations_form', function( event, variation ) {

						if ( typeof variation == 'undefined' ) {
							return;
						}

						var $product = $( this ).closest( '.product, .product-col' ),
							$image_slider = $product.find( '.product-image-slider' ),
							$thumbs_slider = $product.find( '.product-thumbs-slider' ),
							links;

						if ( $image_slider.length ) {
							$image_slider.trigger( 'to.owl.carousel', [0, duration, true] );
							links = $image_slider.data( 'links' );
						}
						if ( $thumbs_slider.length ) {
							$thumbs_slider.trigger( 'to.owl.carousel', [0, duration, true] );
							$thumbs_slider.find( '.owl-item:eq(0)' ).trigger( 'click' );
						}

						var $shop_single_image = $product.find( 'div.product-images .woocommerce-main-image' ).length ? $product.find( 'div.product-images .woocommerce-main-image' ) : $( '.single-product div.product-images .woocommerce-main-image' ),
							productimage = $shop_single_image.attr( 'data-o_src' ),
							imagetitle = $shop_single_image.attr( 'data-o_title' ),
							imagehref = $shop_single_image.attr( 'data-o_href' ),
							$shop_thumb_image = $product.find( '.woocommerce-main-thumb' ),
							thumbimage = $shop_thumb_image.attr( 'data-o_src' ),
							variation_image = variation.image_src,
							variation_link = variation.image_link,
							variation_title = variation.image_title,
							variation_thumb = variation.image_thumb;

						if ( $product.hasClass( 'porto-tb-item' ) ) { // in type builder
							$shop_single_image = $product.find( '.porto-tb-featured-image img' ).eq( 0 );
							productimage = $shop_single_image.attr( 'data-o_src' );
							variation_image = variation.image.thumb_src;
						} else if ( $product.hasClass( 'product-col' ) ) { // shop pages
							$shop_single_image = $product.find( 'div.product-image .inner img:first-child' );
							productimage = $shop_single_image.attr( 'data-o_src' );
							variation_image = variation.image.thumb_src;
						}

						if ( !productimage ) {
							productimage = $shop_single_image.attr( 'data-oi' ) ? $shop_single_image.attr( 'data-oi' ) : ( ( !$shop_single_image.attr( 'src' ) ) ? '' : $shop_single_image.attr( 'src' ) );
							$shop_single_image.attr( 'data-o_src', productimage );
						}

						if ( !imagehref ) {
							imagehref = ( !$shop_single_image.attr( 'href' ) ) ? '' : $shop_single_image.attr( 'href' );
							$shop_single_image.attr( 'data-o_href', imagehref );
						}

						if ( !imagetitle ) {
							imagetitle = ( !$shop_single_image.attr( 'alt' ) ) ? '' : $shop_single_image.attr( 'alt' );
							$shop_single_image.attr( 'data-o_title', imagetitle );
						}

						if ( !thumbimage ) {
							thumbimage = $shop_thumb_image.attr( 'data-oi' ) ? $shop_thumb_image.attr( 'data-oi' ) : ( ( !$shop_thumb_image.attr( 'src' ) ) ? '' : $shop_thumb_image.attr( 'src' ) );
							$shop_thumb_image.attr( 'data-o_src', thumbimage );
						}

						if ( variation_image ) {
							$shop_single_image.attr( 'src', variation_image );
							$shop_single_image.attr( 'srcset', '' );
							$shop_single_image.attr( 'alt', variation_title );
							$shop_single_image.attr( 'href', variation_link );
							$shop_thumb_image.attr( 'src', variation_thumb );
							if ( theme.product_image_popup && typeof links != 'undefined' ) {
								links[0].src = variation_link;
								links[0].title = variation_title;
							}
						} else {
							$shop_single_image.attr( 'src', productimage );
							$shop_single_image.attr( 'srcset', '' );
							$shop_single_image.attr( 'alt', imagetitle );
							$shop_single_image.attr( 'href', imagehref );
							$shop_thumb_image.attr( 'src', thumbimage );
							if ( theme.product_image_popup && typeof links != 'undefined' ) {
								links[0].src = imagehref;
								links[0].title = imagetitle;
							}
						}
						$shop_single_image.each( function() {
							var elevateZoom = $( this ).data( 'elevateZoom' );
							if ( typeof elevateZoom != 'undefined' ) {
								elevateZoom.swaptheimage( $( this ).attr( 'src' ), $( this ).attr( 'src' ) );
							}
						} );
					} );

					// fix scrolling to top issue on fancybox quickview whenever updating variation
					var porto_fb_update_trigger = null;
					$( document ).on( 'found_variation reset_image', '.variations_form', function( event, variation ) {
						if ( $( this ).closest( '.fancybox-inner' ).length && $.fancybox ) {
							$( window ).off( 'resize.fb', $.fancybox.update );
							if ( porto_fb_update_trigger ) {
								theme.deleteTimeout( porto_fb_update_trigger );
							}
							porto_fb_update_trigger = theme.requestTimeout( function() {
								$( window ).on( 'resize.fb', $.fancybox.update );
								$.fancybox.reposition();
								porto_fb_update_trigger = false;
							}, 600 );
						}
					} );

					return self;
				}
			}

		} );

	} ).apply( this, [window.theme, jQuery] );


	// Woocommerce Events
	( function( theme, $ ) {

		theme = theme || {};

		$.extend( theme, {

			WooEvents: {

				initialize: function() {

					this.events();

					return this;
				},

				events: function() {
					var self = this;

					// wcml currency switcher
					$( document ).on( 'click', '.wcml-switcher li', function() {
						if ( $( this ).parent().attr( 'disabled' ) == 'disabled' )
							return;
						var currency = $( this ).attr( 'rel' );
						self.loadCurrency( currency );
					} );

					// woocommerce currency switcher
					$( document ).on( 'click', '.woocs-switcher li', function() {
						if ( $( this ).parent().attr( 'disabled' ) == 'disabled' )
							return;
						var currency = $( this ).attr( 'rel' );
						self.loadWoocsCurrency( currency );
					} );

					return self;
				},

				loadCurrency: function( currency ) {
					$( '.wcml-switcher' ).attr( 'disabled', 'disabled' );
					$( '.wcml-switcher' ).append( '<li class="loading"></li>' );
					var data = { action: 'wcml_switch_currency', currency: currency };
					$.ajax( {
						type: 'post',
						url: theme.ajax_url,
						data: {
							action: 'wcml_switch_currency',
							currency: currency
						},
						success: function( response ) {
							$( '.wcml-switcher' ).removeAttr( 'disabled' );
							$( '.wcml-switcher' ).find( '.loading' ).remove();
							window.location = window.location.href;
						}
					} );
				},

				loadWoocsCurrency: function( currency ) {
					$( '.woocs-switcher' ).attr( 'disabled', 'disabled' );
					$( '.woocs-switcher' ).append( '<li class="loading"></li>' );
					var l = window.location.href;
					l = l.split( '?' );
					l = l[0];
					var string_of_get = '?';
					woocs_array_of_get.currency = currency;

					if ( Object.keys( woocs_array_of_get ).length > 0 ) {
						jQuery.each( woocs_array_of_get, function( index, value ) {
							string_of_get = string_of_get + "&" + index + "=" + value;
						} );
					}
					window.location = l + string_of_get;
				},

				removeParameterFromUrl: function( url, parameter ) {
					return url
						.replace( new RegExp( '[?&]' + parameter + '=[^&#]*(#.*)?$' ), '$1' )
						.replace( new RegExp( '([?&])' + parameter + '=[^&]*&' ), '$1' );
				}
			}

		} );

	} ).apply( this, [window.theme, jQuery] );

	( function( theme, $ ) {

		$( document ).ready( function() {
			// Woocommerce Qty Field
			if ( typeof theme.WooQtyField !== 'undefined' ) {
				theme.WooQtyField.initialize();
			}

			// Woocommerce Quick View
			if ( typeof theme.WooQuickView !== 'undefined' ) {
				theme.WooQuickView.initialize();
			}

			// Woocommerce Events
			if ( typeof theme.WooEvents !== 'undefined' ) {
				theme.WooEvents.initialize();
			}

			// disable drop down
			if ( !( 'ontouchstart' in document ) ) {
				$( '.mini-cart' ).on( 'hide.bs.dropdown', function() {
					return false;
				} );
			} else {
				$( '#mini-cart .cart-head' ).on( 'click', function( e ) {
					$( this ).parent().toggleClass( 'open' );
				} );
				$( 'html,body' ).on( 'click', function( e ) {
					if ( $( '#mini-cart' ).hasClass( 'open' ) && !$( e.target ).closest( '#mini-cart' ).length ) {
						$( '#mini-cart' ).removeClass( 'open' );
					}
				} );
			}

			$( document ).on( 'tabactivate', '.woocommerce-tabs', function( e, ui ) {
				var label = $( ui ).attr( 'data-target' );
				var panel = $( ui ).closest( '.woocommerce-tabs' ).find( '[aria-labelledby="' + label + '"' );
				theme.refreshVCContent( panel );
			} );

			// Perfect WooCommerce Brand Plugin
			$( document ).find( '.pwb-columns a[href="' + window.location.href + '"' ).each( function(){
				$( this ).addClass( 'active' );
			} )
		} );
	} ).apply( this, [window.theme, jQuery] );


	( function( theme, $, undefined ) {

		$( document ).ready( function() {

			/*===================================================================================*/
			/*  TOOLTIP FOR YITH WISHLIST, COMPARE, ADD TO CART, QUICK VIEW
			/*===================================================================================*/
			if ( ! theme.isMobile() ) {
				$( document ).on( 'yith_wcwl_init_after_ajax', function() {
					$( '.product-col .add_to_wishlist:not([data-bs-original-title]), .product-col .yith-wcwl-wishlistaddedbrowse > a:not([data-bs-original-title]), .product-col .yith-wcwl-wishlistexistsbrowse > a:not([data-bs-original-title])' ).each( function() {
						let _this = $( this );
						if ( ! _this.attr( 'title' ) ) {
							_this.attr( 'title', _this.text().trim() );
						}
						_this.tooltip();
					} );
				} );
			}
			// Woocommerce Variation Form
			theme.WooVariationForm.initialize();

			// Woocommerce Product Image Slider
			if ( typeof theme.initAsync == 'function' ) {
				theme.WooProductImageSlider.initialize();
				porto_woocommerce_init();
			} else {
				$.when( theme.asyncDeferred ).done( function() {
					theme.WooProductImageSlider.initialize();
					porto_woocommerce_init();
				} );
			}

			// Yith plugins compatibility
			$( document ).on( 'yith_wccl_product_gallery_loaded', function() {
				theme.WooProductImageSlider.initialize();
			} );

			$( window ).on( 'vc_reload', function() {
				porto_woocommerce_init();
				$( '.type-product' ).addClass( 'product' );
			} );

			// Add wishlist popup
			/*if ( !$( '#yith-wcwl-popup-message' ).length ) {
				$( 'body' ).prepend( $( '<div>' ).attr( 'id', 'yith-wcwl-popup-message' ).html( '<div id="yith-wcwl-message"></div>' ).hide() );
			}*/

			// shop horizontal filter
			$( document ).on( 'click', '.porto-product-filters-toggle a', function( e ) {
				e.preventDefault();
				$( this ).closest( '.porto-product-filters-toggle' ).toggleClass( 'opened' );
				var $products_wrapper = $( this ).closest( '#main' ).find( '.main-content' ).find( 'ul.products' ), offset, $main = $( this ).closest( '#main' ).find( '.main-content-wrap' );
				$main.toggleClass( 'opened' );
				if ( $main.hasClass( 'opened' ) ) {
					offset = -1;
				} else {
					offset = 1;
				}
				if ( $products_wrapper.hasClass( 'grid' ) ) {
					var cols_lg_index = 0, cols_md_index = 0, width_lg_index = 0, width_md_index = 0;
					for ( var i = 1; i <= 8; i++ ) {
						if ( !cols_lg_index && $products_wrapper.hasClass( 'ccols-xl-' + i ) ) {
							cols_lg_index = i;
							if ( i + offset >= 1 ) {
								$products_wrapper.removeClass( 'ccols-xl-' + i );
								$products_wrapper.addClass( 'ccols-xl-' + ( i + offset ) );
							}
						}
						if ( !cols_md_index && $products_wrapper.hasClass( 'ccols-lg-' + i ) ) {
							cols_md_index = i;
							if ( i + offset >= 1 ) {
								$products_wrapper.removeClass( 'ccols-lg-' + i );
								if ( offset === -1 ) {
									$products_wrapper.addClass( 'ccols-md-' + i );
								}
								$products_wrapper.addClass( 'ccols-lg-' + ( i + offset ) );
							}
						}
						if ( !width_lg_index && $products_wrapper.hasClass( 'pwidth-lg-' + i ) ) {
							width_lg_index = i;
							if ( i + offset >= 1 ) {
								$products_wrapper.removeClass( 'pwidth-lg-' + i );
								$products_wrapper.addClass( 'pwidth-lg-' + ( i + offset ) );
							}
						}
						if ( !width_md_index && $products_wrapper.hasClass( 'pwidth-md-' + i ) ) {
							width_md_index = i;
							if ( i + offset >= 1 ) {
								$products_wrapper.removeClass( 'pwidth-md-' + i );
								$products_wrapper.addClass( 'pwidth-md-' + ( i + offset ) );
							}
						}
					}
				}
				theme.requestTimeout( function() {
					$( window ).trigger( 'scroll' );
					// Update Carousel
					$( document ).find( '.owl-carousel' ).each( function( e ) {
						var $this = $( this );
						if ( $this.data( 'owl.carousel' ) ) {
							$this.trigger( 'refresh.owl.carousel' );
						}
					} );
					// Update Swiper
					$( document ).find( '.swiper-container' ).each( function( e ) {
						var $this = $( this ),
							$instance = $this.data( 'swiper' );
						if ( $instance ) {
							$instance.update();
						}
					} );
				}, 300 );

				if ( $main.hasClass( 'opened' ) ) {
					$.cookie( 'porto_horizontal_filter', 'opened' );
				} else {
					$.cookie( 'porto_horizontal_filter', 'closed' );
				}
				theme.refreshStickySidebar( true );

				return false;
			} );
			if ( $.cookie && 'opened' == $.cookie( 'porto_horizontal_filter' ) && $( '#main .porto-products-filter-body' ).length && !theme.isTablet() ) {
				$( '.porto-product-filters-toggle a' ).trigger( 'click' );
				$( '#main .porto-products-filter-body [data-plugin-sticky]:not(.manual)' ).addClass( 'manual' );
				setTimeout( function() {
					var $obj = $( '#main .porto-products-filter-body [data-plugin-sticky].manual' ),
						pluginOptions = $obj.data( 'plugin-options' );
					$obj.removeClass( 'manual' ).themeSticky( pluginOptions );
					theme.requestTimeout( function() {
						$( window ).trigger( 'scroll' );
					}, 100 );
				}, 500 );
			}

			$( document ).on( 'click', '.porto-product-filters.style2 .widget-title', function( e ) {
				e.preventDefault();
				if ( $( this ).next().is( ':hidden' ) ) {
					$( '.porto-product-filters.style2 .widget-title' ).next().hide();
					$( '.porto-product-filters.style2 .widget' ).removeClass( 'opened' );
					$( this ).next().show();
					$( this ).next().find( 'input[type="text"]:first-child' ).focus();
				} else {
					$( this ).next().hide();
				}
				$( this ).parent().toggleClass( 'opened' );
				return false;
			} );
			$( 'body' ).on( 'click', function( e ) {
				if ( !$( e.target ).is( '.porto-product-filters' ) && !$( e.target ).is( '.porto-product-filters *' ) ) {
					$( '.porto-product-filters.style2 .widget-title' ).next().hide();
					$( '.porto-product-filters.style2 .widget' ).removeClass( 'opened' );
				}
			} );

			// Perform AJAX login on form submit
			$( 'body' ).on( 'click', '#login-form-popup form .woocommerce-Button', function( e ) {
				var $this = $( this ),
					$form = $this.closest( 'form' ),
					isLogin = $this.hasClass( 'login-btn' );
				if ( !isLogin && !$this.hasClass( 'register-btn' ) ) {
					isLogin = $form.hasClass( 'login' );
				}
				$form.find( '#email' ).val( $form.find( '#username' ).val() );
				$form.find( 'p.status' ).show().text( js_porto_vars.login_popup_waiting_msg ? js_porto_vars.login_popup_waiting_msg : 'Please wait...' ).addClass( 'loading' );
				$form.find( 'button[type=submit]' ).attr( 'disabled', 'disabled' );
				$.ajax( {
					type: 'POST',
					dataType: 'json',
					url: theme.ajax_url,
					data: $form.serialize() + '&action=porto_account_login_popup_' + ( isLogin ? 'login' : 'register' ),
					success: function( data ) {
						$form.find( 'p.status' ).html( data.message.replace( '/<script.*?\/script>/s', '' ) ).removeClass( 'loading' );
						$form.find( 'button[type=submit]' ).removeAttr( 'disabled' );
						if ( data.loggedin === true ) {
							window.location.reload();
						}
					}
				} );
				e.preventDefault();
			} );

			// shortcodes
			var $ajax_tab_cache = {};
			$( document ).on( 'click', '.porto-products.show-category .product-categories a', function( e ) {
				e.preventDefault();
				var $this = $( this ), $form = $this.closest( '.porto-products' ).find( '.pagination-form' ), id = $this.closest( '.porto-products' ).attr( 'id' ), group = [];
				$( this ).parent().siblings().removeClass( 'current' );
				$( this ).parent().addClass( 'current' );
				if ( typeof $this.data( 'sort_id' ) != 'undefined' ) {
					$form.find( 'input[name="orderby"]' ).val( $this.data( 'sort_id' ) );
					group = $this.data( 'sort_id' );
					$form.find( 'input[name="category"]' ).val( '' );
				}
				if ( typeof $this.data( 'cat_id' ) != 'undefined' ) {
					if ( typeof $this.data( 'sort_id' ) == 'undefined' ) {
						$form.find( 'input[name="orderby"]' ).val( $form.find( 'input[name="original_orderby"]' ).val() );
						group = $form.find( 'input[name="original_orderby"]' ).val();
					}
					if ( typeof $form.data( 'original_cat_id' ) == 'undefined' ) {
						$form.data( 'original_cat_id', $form.find( 'input[name="category"]' ).val() );
						group = $form.find( 'input[name="category"]' ).val();
					}
					if ( $this.data( 'cat_id' ) ) {
						$form.find( 'input[name="category"]' ).val( $this.data( 'cat_id' ) );
						group = $this.data( 'cat_id' );
					} else {
						if ( $form.data( 'original_cat_id' ) ) {
							$form.find( 'input[name="category"]' ).val( $form.data( 'original_cat_id' ) );
							group = $form.data( 'original_cat_id' );
						} else {
							$form.find( 'input[name="category"]' ).val( '' );
							group = '';
						}
					}
				}
				var data = $form.serialize() + '&product-page=1&action=porto_woocommerce_shortcodes_products&nonce=' + js_porto_vars.porto_nonce;
				$this.closest( '.porto-products' ).find( 'ul.products' ).trigger( 'porto_update_products', [data, '', $this, id, group] );
			} );
			$( document ).on( 'click', '.porto-products .page-numbers a', function( e ) {
				var $this = $( this ), pagination_style,
					$shop_container = $this.closest( '.porto-products' ).find( 'ul.products' ),
					cur_page = $shop_container.data( 'cur_page' ),
					max_page = $shop_container.data( 'max_page' ),
					$form = $this.closest( '.porto-products' ).find( '.pagination-form' );
				e.preventDefault();
				if ( $this.closest( '.pagination' ).hasClass( 'load-more' ) ) {
					if ( !cur_page || !max_page || ++cur_page > max_page ) {
						return;
					}
					pagination_style = 'load_more';
					$this.data( 'text', $this.text() );
					$this.text( js_porto_vars.loader_text );
				} else {
					var url = new RegExp( "product-page(=|/)([^(&|/)]*)", "i" ).exec( this.href );
					cur_page = url && unescape( url[2] ) || "";
					pagination_style = 'default';
				}
				var page_var = cur_page ? '&product-page=' + escape( cur_page ) : '', data = $form.serialize() + page_var + '&action=porto_woocommerce_shortcodes_products&nonce=' + js_porto_vars.porto_nonce;
				$shop_container.trigger( 'porto_update_products', [data, pagination_style, $this] );
				if ( 'default' == pagination_style ) {
					theme.scrolltoContainer( $shop_container );
				}
			} );
			$( document ).on( 'porto_update_products', 'ul.products', function( e, data, pagination_style, $obj, id, group ) {
				var $this = $( this );
				// ajax tab
				if ( undefined == $ajax_tab_cache[id] || -1 == Object.keys( $ajax_tab_cache[id] ).indexOf( group ) ) {
					porto_ajax_load_products( $this, data, pagination_style, $ajax_tab_cache, id, group );
				} else {
					var response = $ajax_tab_cache[id][group];
					//animation
					$this.css( 'opacity', 0 );
					$this.animate(
						{
							'opacity': 1,
						},
						400,
						function() {
							$this.css( 'opacity', '' );
						}
					);

					porto_ajax_load_products_success( $this, response, pagination_style );
				}
			} );

			// initialize woocommerce actions after skeleton loading
			var skeletonLoadingTrigger;
			$( '.skeleton-loading' ).on( 'skeleton-loaded', function() {
				var $this = $( this );
				if ( skeletonLoadingTrigger ) {
					theme.deleteTimeout( skeletonLoadingTrigger );
				}
				porto_woocommerce_variations_init( $this );

				// yith wishlist pro compatibility
				if ( $this.hasClass( 'products' ) || $this.hasClass( 'product' ) ) {
					$( document ).trigger( 'yith_infs_added_elem' );
				}

				skeletonLoadingTrigger = theme.requestTimeout( function() {
					porto_woocommerce_init();
					if ( $( 'body' ).hasClass( 'single-product' ) ) {
						theme.WooVariationForm.init();
						var $image_slider = $( '.product-image-slider' );
						if ( $image_slider.length && $image_slider.data( 'owl.carousel' ) ) {
							$image_slider.trigger( 'refresh.owl.carousel' );
						} else {
							theme.WooProductImageSlider.initialize();
						}
						$( '.wc-tabs-wrapper, .woocommerce-tabs, #rating' ).trigger( 'init' );

						// compatibility issue with Yith WooCommerce Booking form
						if ( $( document.body ).hasClass( 'yith-booking' ) ) {
							$( document ).trigger( 'yith-wcbk-init-booking-form' );
						}
					}

					// refresh cart content
					if ( $this.find( '.widget_shopping_cart_content' ).length ) {
						$( document.body ).trigger( 'wc_fragment_refresh' );
					}
				}, 100 );
			} );
		} );
		
		var stickyShop = function() {
		
			// sticky add to cart
			var $sticky_product_obj = $( '.single-product .sticky-product' ),
				is_elementor_editor = $( document.body ).hasClass( 'elementor-editor-active' ),
				$sticky_product_form;

			var init_sticky_add_to_cart_fn = function( $sticky_product_obj, is_elementor_editor ) {
				if ( is_elementor_editor && elementorFrontend && elementorFrontend.hooks ) {
					elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_cp_addcart_sticky.default', function( $obj ) {
						$sticky_product_obj = $( '.single-product .sticky-product' );
						window.dispatchEvent( new Event( 'scroll' ) );
					} );
				}

				$sticky_product_form = $( 'form.cart:visible' ).eq(0);

				window.addEventListener( 'scroll', function() {
					var scrollTop = $( window ).scrollTop(),
						offset = theme.adminBarHeight() + ( theme.StickyHeader.sticky_height > 1 ? theme.StickyHeader.sticky_height : 0 ),
                        prevScrollPos = $sticky_product_obj.data('prev-pos') ? $sticky_product_obj.data('prev-pos') : 0;
					
					if ( ! $sticky_product_obj.hasClass( 'show-mobile' ) && $( window ).width() < 768 ) {
						$( 'body' ).css( 'padding-bottom', '' );
					} else if ( $sticky_product_form.length && $sticky_product_form.offset().top + $sticky_product_form.height() / 2 <= scrollTop + offset ) {
						if ( $( '.page-wrapper' ).hasClass( 'sticky-scroll-up' ) && ! $( 'html' ).hasClass( 'porto-search-opened' ) && $sticky_product_obj.hasClass( 'pos-top' ) ) {
							if ( scrollTop >= prevScrollPos ) {
								$sticky_product_obj.addClass('scroll-down');
							} else {
								$sticky_product_obj.removeClass('scroll-down');
							}

							var scrollUpOffset = - theme.StickyHeader.sticky_height;
							if ( 'undefined' == typeof ( theme.StickyHeader.sticky_height ) ) {
								$sticky_product_obj.data( 'prev-pos', 0 );
							} else {
								// The transition of Sticky isn't working in this area
								var $transitionOffset = ( offset > 100 ) ? offset : 100;
								if ( $( 'form.cart' ).offset().top + $sticky_product_obj.outerHeight() + $transitionOffset < scrollTop + offset + scrollUpOffset ) {
									$sticky_product_obj.addClass( 'sticky-ready' );
								} else {
									$sticky_product_obj.removeClass( 'sticky-ready' );
								}
								$sticky_product_obj.data( 'prev-pos', scrollTop );
							}
						}

						var porto_progress_obj = $( '.porto-scroll-progress.fixed-top.fixed-under-header' );
						if ( porto_progress_obj.length > 0 ) {
							offset += porto_progress_obj.height();
						}

						$sticky_product_obj.removeClass( 'hide' );
						if ( !$sticky_product_obj.hasClass( 'pos-bottom' ) ) {
							$sticky_product_obj.css( 'top', offset );
						} else if ( $sticky_product_obj.hasClass( 'show-mobile' ) || ( ! $sticky_product_obj.hasClass( 'show-mobile' ) && $( window ).width() >= 768 ) ) {
							$( 'body' ).css( 'padding-bottom', $sticky_product_obj.outerHeight() );
						}
					} else {
						$sticky_product_obj.addClass( 'hide' );
						if ( $sticky_product_obj.hasClass( 'pos-bottom' ) && ( $sticky_product_obj.hasClass( 'show-mobile' ) || ( ! $sticky_product_obj.hasClass( 'show-mobile' ) && $( window ).width() >= 768 ) ) ) {
							$( 'body' ).css( 'padding-bottom', '' );
						}
					}
				}, { passive: true } );
				$sticky_product_obj.find( '.add-to-cart .button' ).on( 'click', function( e ) {
					e.preventDefault();
					if ( $sticky_product_obj.find( '.add-to-cart .qty' ).length ) {
						$( '.single-product form .quantity .qty' ).filter(function() {
							if ( $( this ).closest( '.product-col' ).length ) {
								return false;
							}
							return true;
						}).val( $sticky_product_obj.find( '.add-to-cart .qty' ).val() );
					}
					$( '.single-product form .single_add_to_cart_button' ).filter(function() {
						if ( $( this ).closest( '.product-col' ).length ) {
							return false;
						}
						return true;
					}).eq(0).trigger( 'click' );
				} );
				$( '.single-product .entry-summary .quantity' ).clone().prependTo( '.single-product .sticky-product .add-to-cart' );

				var origin_img = $sticky_product_obj.find( '.sticky-image img' ).data( 'oi' ) ? $sticky_product_obj.find( '.sticky-image img' ).data( 'oi' ) : $sticky_product_obj.find( '.sticky-image img' ).attr( 'src' ),
					origin_price = $sticky_product_obj.find( '.price' ).html(),
					origin_stock = $sticky_product_obj.find( '.availability' ).html(),
					is_variation = false;
				$( document ).on( 'found_variation reset_data', '.variations_form', function( e, obj ) {
					if ( $( e.currentTarget ).closest( '.product-col' ).length == 0 ) {
						if ( obj ) {
							is_variation = true;
							$sticky_product_obj.find( '.sticky-image img' ).attr( 'src', obj.image_thumb ? obj.image_thumb : origin_img );
							$sticky_product_obj.find( '.price' ).replaceWith( obj.price_html );
							$sticky_product_obj.find( '.availability' ).html( obj.availability_html ? obj.availability_html : origin_stock );
						} else if ( is_variation ) {
							is_variation = false;
							$sticky_product_obj.find( '.sticky-image img' ).attr( 'src', origin_img );
							$sticky_product_obj.find( '.price' ).html( origin_price );
							$sticky_product_obj.find( '.availability' ).html( origin_stock );
						}	
					}
				} );
			};

			if ( $sticky_product_obj.length || is_elementor_editor ) {
				init_sticky_add_to_cart_fn( $sticky_product_obj, is_elementor_editor );
			} else if ( $( 'div.product.skeleton-loading' ).length ) {
				$( 'div.product.skeleton-loading' ).on( 'skeleton-loaded', function() {
					$sticky_product_obj = $( '.single-product .sticky-product' );
					init_sticky_add_to_cart_fn( $sticky_product_obj, is_elementor_editor );
				} );
			} else {
				$( document.body ).on( 'porto_elementor_editor_init', function() {
					var $sticky_product_obj = $( '.single-product .sticky-product' ),
						is_elementor_editor = $( document.body ).hasClass( 'elementor-editor-active' );
					if ( $sticky_product_obj.length || is_elementor_editor ) {
						init_sticky_add_to_cart_fn( $sticky_product_obj, is_elementor_editor );
					}
				} );
			}

			// sticky filter on mobile
			if ( 1 === $( '.shop-loop-before' ).length ) {
				var porto_progress_obj = $( '.porto-scroll-progress.fixed-top.fixed-under-header' ),
					porto_progress_height = 0;
				if ( porto_progress_obj.length > 0 ) {
					var flag = false;
					if ( porto_progress_obj.is( ':hidden' ) ) {
						porto_progress_obj.show();
						flag = true;
					}
					porto_progress_height = porto_progress_obj.height();
					if ( flag ) {
						porto_progress_obj.hide();
					}
				} else {
					porto_progress_height = 0;
				}

				var init_filter_sticky = function() {
					var $obj = $( '.shop-loop-before' ),
						prevScrollPos = $obj.data('prev-pos') ? $obj.data('prev-pos') : 0,
						scrollUpOffset = 0,
						$pageWrapper = $( '.page-wrapper' );
					if ( 'none' == $obj.css( 'display' ) ) {
						// Found Nothing
						return;
					}
					if ( !$obj.prev( '.filter-placeholder' ).length ) {
						$( '<div class="filter-placeholder m-0"></div>' ).insertBefore( $obj );
					}
					var $ph = $obj.prev( '.filter-placeholder' ),
						scrollTop = $( window ).scrollTop(),
						offset = theme.adminBarHeight() + theme.StickyHeader.sticky_height + porto_progress_height - 1,
						objHeight = $obj.outerHeight() + parseInt( $obj.css( 'margin-bottom' ) );
					if ( $( '.page-wrapper' ).hasClass( 'sticky-scroll-up' ) ) {
						if ( scrollTop >= prevScrollPos ) {
							$obj.addClass('scroll-down');
						} else {
							$obj.removeClass('scroll-down');
						}
						// Header is scroll-up Sticky Type
						scrollUpOffset = - theme.StickyHeader.sticky_height;
						if ( 'undefined' == typeof ( theme.StickyHeader.sticky_height ) ) {
							$obj.data( 'prev-pos', 0 );
						} else {
							// The transition of Sticky isn't working in this area
							var $transitionOffset = ( offset > 100 ) ? offset : 100;
							if ( $ph.offset().top + objHeight + $transitionOffset < scrollTop + offset + scrollUpOffset ) {
								$obj.addClass( 'sticky-ready' );
							} else {
								$obj.removeClass( 'sticky-ready' );
							}
							$obj.data( 'prev-pos', scrollTop );
						}
					}
					// if ( $( 'html.filter-sidebar-opened' ).length ) {
					// 	$ph.css( 'height', '' );
					// 	return;
					// }
					if ( ( $ph.offset().top + objHeight < scrollTop + offset + scrollUpOffset ) ) {
						if ( ! $pageWrapper.hasClass( 'sticky-scroll-up' ) || ( $pageWrapper.hasClass( 'sticky-scroll-up' ) && 0 !== prevScrollPos ) ) {
							$ph.css( 'height', objHeight );
							$obj.css( 'top', offset );
							$obj.addClass( 'sticky' );
						}
					} else {
						$ph.css( 'height', '' );
						$obj.removeClass( 'sticky' ).css( 'top', '' );
					}
				};
				if ( window.innerWidth < 992 ) {
					window.removeEventListener( 'scroll', init_filter_sticky );
					window.addEventListener( 'scroll', init_filter_sticky, { passive: true } );
					init_filter_sticky();
				}
				var request_timer = null,
					old_win_width = window.innerWidth;
				$( window ).on( 'resize', function() {
					if ( old_win_width != window.innerWidth ) {
						if ( request_timer ) {
							theme.deleteTimeout( request_timer );
							request_timer = false;
						}
						if ( window.innerWidth < 992 ) {
							request_timer = theme.requestTimeout( function() {
								window.removeEventListener( 'scroll', init_filter_sticky );
								window.addEventListener( 'scroll', init_filter_sticky, { passive: true } );
								$( window ).trigger( 'scroll' );
							}, 100 );
						} else {
							window.removeEventListener( 'scroll', init_filter_sticky );
							$( '.shop-loop-before' ).removeClass( 'sticky' ).css( 'top', '' ).prev( '.filter-placeholder' ).css( 'height', '' );
						}

						if ( $sticky_product_obj.length ) {
							$sticky_product_form = $( 'form.cart:visible' ).eq(0);
						}
						old_win_width = window.innerWidth;
					}
				} );
			}
		}
		if ( theme.isReady ) { // Finish init
			stickyShop();
		}
		$( document ).on( 'porto_theme_init', stickyShop );

		// cart page accordion
		$( '.cart-v2 .cart_totals .accordion-toggle.out' ).removeClass( 'out' );
		$( document ).ajaxComplete( function( event, xhr, options ) {
			$( '.cart-v2 .cart_totals .accordion-toggle.out' ).each( function() {
				if ( $( $( this ).attr( 'href' ) ).length && $( $( this ).attr( 'href' ) ).is( ':hidden' ) ) {
					$( this ).removeClass( 'collapsed' );
					$( $( this ).attr( 'href' ) ).addClass( 'show' );
				}
			} );
		} );

		// porto products filter element
		$( '.porto_products_filter_form .btn-submit' ).on( 'click', function( e ) {
			e.preventDefault();
			var data = $( this ).closest( 'form' ).serializeArray(),
				submit_data = '';
			for ( var i in data ) {
				var param = data[i];
				if ( param.value ) {
					if ( submit_data ) {
						submit_data += '&';
					}
					submit_data += param.name + '=' + param.value;
					if ( 'min_price' == param.name ) {
						var max_price = $( this ).closest( 'form' ).find( '.porto_dropdown_price_range option:selected' ).data( 'maxprice' );
						if ( max_price ) {
							submit_data += '&max_price=' + max_price;
						}
					}
				}
			}
			var action_url = $( this ).closest( 'form' ).attr( 'action' );
			location.href = action_url + ( -1 === action_url.indexOf( '?' ) ? '?' : '&' ) + submit_data;
		} );

		// yith wishlist
		if ( $( '.wishlist_table.responsive' ).length ) {
			$( window ).on( 'resize', function() {
				var media = window.matchMedia( '(max-width: 768px)' ),
					$wishlist_table = $( '.wishlist_table.responsive' );
				if ( $wishlist_table.hasClass( 'traditional' ) ) {
					if ( media.matches ) {
						$wishlist_table.addClass( 'mobile' );
					} else {
						$wishlist_table.removeClass( 'mobile' );
					}
				}
			} );
		}

		// pre-order
		if ( js_porto_vars.pre_order ) {
			var porto_pre_order = {
				init: function() {
					this.$add_to_cart_btn = $( '.product-summary-wrap .single_add_to_cart_button:not(.wpcbn-btn)' );
					this.add_to_cart_label = this.$add_to_cart_btn.html();
					$( '.product-summary-wrap form.variations_form' ).on( 'show_variation', function( e, v, p ) {
						if ( v.porto_pre_order ) {
							porto_pre_order.$add_to_cart_btn.html( v.porto_pre_order_label );
							if ( v.porto_pre_order_date ) {
								$( this ).find( '.woocommerce-variation-description' ).append( v.porto_pre_order_date );
							}
						} else {
							porto_pre_order.$add_to_cart_btn.html( porto_pre_order.add_to_cart_label );
						}
					} ).on( 'hide_variation', function() {
						porto_pre_order.$add_to_cart_btn.html( porto_pre_order.add_to_cart_label );
					} );
				}
			};
			if ( $( 'div.product.skeleton-loading' ).length ) {
				$( 'div.product.skeleton-loading' ).on( 'skeleton-loaded', function() {
					porto_pre_order.init();
				} );
			} else {
				porto_pre_order.init();
			}
		}

		// refresh yith wishlist
		if ( $( '#header .my-wishlist .wishlist-count' ).length ) {
			$( document.body ).on( 'added_to_wishlist removed_from_wishlist added_to_cart', function( e ) {
				var $obj = $( '#header .my-wishlist .wishlist-count' );
				if ( $obj.text() ) {
					$.ajax( {
						type: 'POST',
						dataType: 'json',
						url: theme.ajax_url,
						data: {
							action: 'porto_refresh_wishlist_count',
							nonce: js_porto_vars.porto_nonce,
						},
						success: function( response ) {
							if ( response || 0 === response ) {
								$obj.addClass( 'count-updating' ).text( Number( response ) );
								setTimeout( function() {
									$obj.removeClass( 'count-updating' );
								}, 1000 );
							}
						}
					} );
				}
			} );
		}

		// fix contact form 7 role="alert" issue in cart page
		if ( $( document.body ).hasClass( 'woocommerce-cart' ) && $( '.wpcf7 .screen-reader-response' ).length ) {
			$( '.wpcf7 .screen-reader-response' ).attr( 'role', '' );
		}

		// fix dokan search vendor
		$( '#dokan-store-listing-filter-form-wrap .store-search-input' ).on( 'keydown', function( e ) {
			if ( e.which && event.which == 13 ) {
				$( this ).closest( 'form' ).find( '#apply-filter-btn' ).trigger( 'click' );
				e.preventDefault();
			}
		} );

		// add spinner to block
		if ( $.fn.block ) {
			var funcBlock = $.fn.block;
			$.fn.block = function( opts ) {
				if ( this.hasClass( 'yith-wcwl-add-to-wishlist' ) ) {
					this.children().addClass( 'pe-none opacity-6' );
					return this;
				}
				if ( this.is( '.woocommerce-checkout' ) ) {
					this.append( '<div class="loader-container d-block"><div class="loader"><i class="porto-ajax-loader"></i></div></div>' );
				}
				return funcBlock.call( this, opts );
			}

			var funcUnblock = $.fn.unblock;
			$.fn.unblock = function( opts ) {
				if ( this.hasClass( 'yith-wcwl-add-to-wishlist' ) ) {
					this.children().removeClass( 'pe-none opacity-6' );
					return this;
				}
				funcUnblock.call( this, opts );
				this.is( '.processing' ) || ( this.is( '.woocommerce-checkout' ) && this.children( '.loader-container' ).remove() );
				return this;
			}
		}
		$( 'body' ).on( 'click', '.single_add_to_cart_button.scroll-to-sticky', function (e) {
			$( 'html, body' ).animate(
				{ scrollTop: $( 'form.cart' ).offset().top - 200 }
			);
		} )
	} )( window.theme, jQuery );

	// Compare
	( function( theme, $ ) {
		// remove margin-right
		$( 'body' ).on( 'click', '.yith_woocompare_colorbox #cboxClose, #cboxOverlay', function() {
			$( 'html' ).css( { 'overflow': '', 'margin-right': '' } );
		} );
	} )( window.theme, window.jQuery );

} )();

function porto_woocommerce_init( $wrap ) {
	'use strict';

	if ( !$wrap ) {
		$wrap = jQuery( document.body );
	}
	// Woo Widget Toggle
	( function( $ ) {

		if ( $.fn.themeWooWidgetToggle ) {

			$( function() {
				$wrap.find( '.widget_filter_by_brand, .widget_product_categories, .widget_price_filter, .widget_layered_nav, .widget_layered_nav_filters, .widget_rating_filter, .widget-woof, .porto_widget_price_filter, #wcfmmp-store .widget.sidebar-box, #wcfmmp-store-lists-sidebar .sidebar-box' ).find( '.widget-title' ).each( function() {
					var $this = $( this ),
						opts;

					var pluginOptions = $this.data( 'plugin-options' );
					if ( pluginOptions )
						opts = pluginOptions;

					$this.themeWooWidgetToggle( opts );
				} );
			} );

		}

		// Woo Widget Accordion
		if ( $.fn.themeWooWidgetAccordion ) {

			$( function() {
				$wrap.find( '.widget_filter_by_brand, .widget_product_categories, .widget_price_filter, .widget_layered_nav, .widget_layered_nav_filters, .widget_rating_filter, .widget-woof, #wcfmmp-store .widget.sidebar-box, #wcfmmp-store-lists-sidebar .sidebar-box' ).each( function() {
					var $this = $( this ),
						opts;

					var pluginOptions = $this.data( 'plugin-options' );
					if ( pluginOptions )
						opts = pluginOptions;

					$this.themeWooWidgetAccordion( opts );
				} );
			} );

		}

		// Woo Products Slider
		if ( $.fn.themeWooProductsSlider ) {

			$( function() {
				var $direct_carousels = $wrap.find( '.products-slider:not(.manual)' ).filter( function() {
					if ( $( this ).closest( '.porto-carousel:not(.owl-loaded)' ).length ) {
						return false;
					}
					return true;
				} );
				var $parent_carousel = $wrap.find( '.porto-carousel:not(.owl-loaded)' ).filter( function() {
					if ( $( this ).find( '.products-slider:not(.manual)' ).length ) {
						return true;
					}
					return false;
				} );
				if ( $parent_carousel.length ) {
					$parent_carousel.one( 'initialized.owl.carousel', function() {
						$( this ).find( '.products-slider:not(.manual)' ).each( function() {
							var $this = $( this );
							$this.themeWooProductsSlider( $this.data( 'plugin-options' ) );
						} );
					} );
				}

				$direct_carousels.each( function() {
					var $this = $( this ),
						opts;

					var pluginOptions = $this.data( 'plugin-options' );
					if ( pluginOptions )
						opts = pluginOptions;

					setTimeout( function() {
						$this.themeWooProductsSlider( opts );
					}, 0 );
				} );
			} );
		}

		/*===================================================================================*/
		/*  TOOLTIP FOR YITH WISHLIST, COMPARE, ADD TO CART, QUICK VIEW
		/*===================================================================================*/
		if ( ! theme.isMobile() ) {
			$wrap.find( '.product-col .quickview, .product-col .add_to_cart_read_more, .product-col .add_to_cart_button, .product-col a.compare, .product-col .add_to_wishlist, .product-col .yith-wcwl-wishlistaddedbrowse > a, .product-col .yith-wcwl-wishlistexistsbrowse > a' ).each( function() {
				let _this = $( this );
				// Exclude
				let $productCol = _this.closest( '.product-col' );
				if ( _this.closest( '.porto-tb-woo-link' ).hasClass( 'no-tooltip' ) ) {
					return;
				}
				if ( _this.hasClass( 'add_to_cart_read_more' ) || _this.hasClass( 'add_to_cart_button' ) ) {
					if ( $productCol.hasClass( 'product-wq_onimage' ) || $productCol.hasClass( 'product-onimage' ) || $productCol.hasClass( 'product-outimage' ) || $productCol.hasClass( 'product-default' ) ) {
						return;
					}
					if ( _this.closest( 'ul.products' ).hasClass( 'list' ) ) {
						return;
					}
				}
				if ( _this.hasClass( 'quickview' ) ) {
					if ( $productCol.hasClass( 'product-wq_onimage' ) || $productCol.hasClass( 'product-onimage3' ) || $productCol.hasClass( 'product-onimage2' ) || $productCol.hasClass( 'product-onimage' ) || $productCol.hasClass( 'product-outimage_aq_onimage' ) ) {
						return;
					}
				}
				if ( ! _this.attr( 'title' ) ) {
					_this.attr( 'title', _this.text().trim() );
				}
				_this.tooltip();
			} );
		}

	} )( jQuery );
}

function porto_woocommerce_variations_init( $parent_obj ) {
	'use strict';

	theme.requestTimeout( function() {
		var form_variation = $parent_obj.find( 'form.variations_form:not(.vf_init)' );
		if ( form_variation.length && jQuery.fn.wc_variation_form ) {
			form_variation.each( function() {
				var data_a = jQuery._data( this, 'events' );
				if ( !data_a || !data_a['show_variation'] ) {
					jQuery( this ).wc_variation_form();
				}
			} );
		}
	}, 100 );
}
function porto_ajax_load_products( $obj, data, pagination_style, $ajax_tab_cache, id, group ) {
	'use strict';
	( function( $ ) {
		if ( $obj.hasClass( 'loading' ) ) {
			return;
		}
		$obj.addClass( 'loading' );
		if ( 'load_more' != pagination_style ) {
			$obj.addClass( 'yith-wcan-loading' );
			if ( !$obj.children( '.porto-loading-icon' ).length ) {
				$obj.append( '<i class="porto-loading-icon"></i>' );
			}
		}
		if ( $ajax_tab_cache[id] == undefined ) {
			$ajax_tab_cache[id] = {};
		}
		$.ajax( {
			url: theme.ajax_url,
			data: data,
			type: 'post',
			success: function( response ) {
				//cache
				if ( $( response ).length ) {
					$ajax_tab_cache[id][group] = $( response ).html();
				} else {
					$ajax_tab_cache[id][group] = '';
				}
				porto_ajax_load_products_success( $obj, response, pagination_style );
			},
			complete: function() {
				$obj.removeClass( 'loading' );
			}
		} );
	} )( jQuery );
}

function porto_ajax_load_products_success( $obj, success, pagination_style ) {
	'use strict';

	( function( $ ) {
		let _successProducts = $( success ).find( 'ul.products' );
		if ( $obj.data( 'cur_page' ) && _successProducts.data( 'cur_page' ) ) {
			$obj.data( 'cur_page', _successProducts.data( 'cur_page' ) );
		}
		if ( ! ( $obj.hasClass( 'grid-creative' ) && typeof $obj.attr( 'data-plugin-masonry' ) != 'undefined' ) ) {
			_successProducts.children( ':not(.grid-col-sizer)' ).addClass( 'fadeInUp animated' );
		}
		if ( 'load_more' == pagination_style ) {
			$obj.append( _successProducts.html() );
		} else {
			if ( $obj.hasClass( 'owl-carousel' ) ) {
				$obj.parent().css( 'min-height', $obj.parent().height() );
			}
			if ( $obj.hasClass( 'grid-creative' ) && typeof $obj.attr( 'data-plugin-masonry' ) != 'undefined' ) {
				$obj.isotope( 'remove', $obj.children() );
				$obj.find( '.grid-col-sizer' ).remove();
				var newItems = _successProducts.children();
				$obj.append( newItems );
				$obj.isotope( 'appended', newItems );
				$obj.imagesLoaded( function() {
					$obj.isotope( 'layout' );
				} );
			} else {
				if ( $( success ).length ) {
					$obj.html( _successProducts.html() );
				} else {
					$obj.html( '' );
				}
			}
		}

		if ( $obj.hasClass( 'owl-carousel' ) && $.fn.themeWooProductsSlider ) {
			$obj.trigger( 'destroy.owl.carousel' );
			theme.requestTimeout( function() {
				var pluginOptions = $obj.data( 'plugin-options' ), opts;
				if ( pluginOptions )
					opts = pluginOptions;
				$obj.data( '__wooProductsSlider', '' ).themeWooProductsSlider( opts );
				$obj.parent().css( 'min-height', '' );
			}, 100 );
		}
		if ( $obj.closest( '.porto-products' ).find( '.shop-loop-after' ).length ) {
			if ( $( success ).find( '.shop-loop-after' ).length ) {
				$obj.closest( '.porto-products' ).find( '.shop-loop-after' ).replaceWith( $( success ).find( '.shop-loop-after' ) );
			} else {
				$obj.closest( '.porto-products' ).find( '.shop-loop-after' ).remove();
			}
		}
		if ( typeof $obj.data( 'infinitescroll' ) != 'undefined' ) {
			var infinitescrollData = $obj.data( 'infinitescroll' );
			infinitescrollData.options.state.currPage = 1;
			$obj.data( 'infinitescroll', infinitescrollData );
		}
		$obj.removeClass( 'yith-wcan-loading' );
		if ( 'load_more' == pagination_style && typeof $obj != 'undefined' && typeof $obj.data( 'text' ) != 'undefined' ) {
			$obj.text( $obj.data( 'text' ) );
		}
		$( document ).trigger( "yith-wcan-ajax-filtered" );
	} )( jQuery );
}
