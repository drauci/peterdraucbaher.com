( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {
		mfpConfig: {
			tClose: js_porto_vars.popup_close,
			tLoading: '<div class="porto-ajax-loading"><i class="porto-loading-icon"></i></div>',
			gallery: {
				tPrev: js_porto_vars.popup_prev,
				tNext: js_porto_vars.popup_next,
				tCounter: js_porto_vars.mfp_counter
			},
			image: {
				tError: js_porto_vars.mfp_img_error
			},
			ajax: {
				tError: js_porto_vars.mfp_ajax_error
			},
			callbacks: {
				open: function() {
					$( 'body' ).addClass( 'lightbox-opened' );
					var fixed = this.st.fixedContentPos;
					if ( fixed ) {
						$( '#header.sticky-header .header-main.sticky, #header.sticky-header .main-menu-wrap, .fixed-header #header.sticky-header .header-main, .fixed-header #header.sticky-header .main-menu-wrap' ).css( theme.rtl_browser ? 'left' : 'right', theme.getScrollbarWidth() );
					}
					/* D3-Ahsan - Start */
					var that = $( this._lastFocusedEl );
					if ( ( that.closest( '.portfolios-lightbox' ).hasClass( 'with-thumbs' ) ) && $( document ).width() >= 1024 ) {

						var portfolio_lightbox_thumbnails_base = that.closest( '.portfolios-lightbox.with-thumbs' ).find( '.porto-portfolios-lighbox-thumbnails' ).clone(),
							magnificPopup = $.magnificPopup.instance;

						$( 'body' ).prepend( portfolio_lightbox_thumbnails_base );

						var $portfolios_lightbox_thumbnails = $( 'body > .porto-portfolios-lighbox-thumbnails' ),
							$portfolios_lightbox_thumbnails_carousel = $portfolios_lightbox_thumbnails.children( '.owl-carousel' );
						$portfolios_lightbox_thumbnails_carousel.themeCarousel( $portfolios_lightbox_thumbnails_carousel.data( 'plugin-options' ) );
						$portfolios_lightbox_thumbnails_carousel.trigger( 'refresh.owl.carousel' );

						var $carousel_items_wrapper = $portfolios_lightbox_thumbnails_carousel.find( '.owl-stage' );

						$carousel_items_wrapper.find( '.owl-item' ).removeClass( 'current' );
						$carousel_items_wrapper.find( '.owl-item' ).eq( magnificPopup.currItem.index ).addClass( 'current' );

						$.magnificPopup.instance.next = function() {
							var magnificPopup = $.magnificPopup.instance;
							$.magnificPopup.proto.next.call( this );
							$carousel_items_wrapper.find( '.owl-item' ).removeClass( 'current' );
							$carousel_items_wrapper.find( '.owl-item' ).eq( magnificPopup.currItem.index ).addClass( 'current' );
						};

						$.magnificPopup.instance.prev = function() {
							var magnificPopup = $.magnificPopup.instance;
							$.magnificPopup.proto.prev.call( this );
							$carousel_items_wrapper.find( '.owl-item' ).removeClass( 'current' );
							$carousel_items_wrapper.find( '.owl-item' ).eq( magnificPopup.currItem.index ).addClass( 'current' );
						};

						$carousel_items_wrapper.find( '.owl-item' ).on( 'click', function() {
							$carousel_items_wrapper.find( '.owl-item' ).removeClass( 'current' );
							$.magnificPopup.instance.goTo( $( this ).index() );
							$( this ).addClass( 'current' );
						} );

					}
					/* End - D3-Ahsan */
				},
				close: function() {
					$( 'body' ).removeClass( 'lightbox-opened' );
					var fixed = this.st.fixedContentPos;
					if ( fixed ) {
						$( '#header.sticky-header .header-main.sticky, #header.sticky-header .main-menu-wrap, .fixed-header #header.sticky-header .header-main, .fixed-header #header.sticky-header .main-menu-wrap' ).css( theme.rtl_browser ? 'left' : 'right', '' );
					}
					$( '.owl-carousel .owl-stage' ).each( function() {
						var $this = $( this ),
							w = $this.width() + parseInt( $this.css( 'padding-left' ) ) + parseInt( $this.css( 'padding-right' ) );

						$this.css( { 'width': w + 200 } );
						setTimeout( function() {
							$this.css( { 'width': w } );
						}, 0 );
					} );
					/* D3-Ahsan - Start */
					var that = $( this._lastFocusedEl );
					if ( ( that.parents( '.portfolios-lightbox' ).hasClass( 'with-thumbs' ) ) && $( document ).width() >= 1024 ) {
						$( ' body > .porto-portfolios-lighbox-thumbnails' ).remove();
					}
					/* End - D3-Ahsan */
				}
			}
		},
	} );

} ).apply( this, [window.theme, jQuery] );

// Search
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {

		Search: {

			defaults: {
				popup: $( '.searchform-popup' ),
				form: $( '.searchform' )
			},

			initialize: function( $popup, $form ) {
				this.$popup = ( $popup || this.defaults.popup );
				this.$form = ( $form || this.defaults.form );
				this.form_layout = this.$form.hasClass( 'search-layout-overlay' ) ? 'overlay' : this.$form.hasClass( 'search-layout-reveal' ) ? 'reveal' : false;

				this.build()
					.events();

				return this;
			},

			build: function() {
				var self = this;

				/* Change search form values */
				var $search_form_texts = self.$form.find( '.text input' ),
					$search_form_cats = self.$form.find( '.cat' );

				if ( $( '.searchform .cat' ).get( 0 ) && $.fn.selectric ) {
					$( '.searchform .cat' ).selectric( {
						arrowButtonMarkup: '',
						expandToItemText: true,
						maxHeight: 240
					} );
				}

				$search_form_texts.on( 'change', function() {
					var $this = $( this ),
						val = $this.val();

					$search_form_texts.each( function() {
						if ( $this != $( this ) ) $( this ).val( val );
					} );
				} );

				$search_form_cats.on( 'change', function() {
					var $this = $( this ),
						val = $this.val();

					$search_form_cats.each( function() {
						if ( $this != $( this ) ) $( this ).val( val );
					} );
				} );

				$( document ).on( 'focus', '.searchform-popup .text input', function (e) {
					let $this = $( this );
					$this.trigger( 'porto_sh_before_open' );
					if ( $this.closest( '.searchform-popup' ).find( '.search-list:not(.search-history-list)' ).length ) {
						$this.closest( '.searchform-popup' ).find( '.search-lists' ).addClass( 'show' );
					}
				});

				return this;
			},

			events: function() {
				var self = this;

				$( 'body' ).on( 'click', '.searchform-popup', function( e ) {
					e.stopPropagation();
				} );
				$( 'body' ).off( 'click', '.searchform-popup .search-toggle' ).on( 'click', '.searchform-popup .search-toggle', function( e ) {
					var $this = $( this ),
						$form = $this.next();
					$this.toggleClass( 'opened' );
					if ( 'overlay' == self.form_layout ) {
						$this.siblings( '.search-layout-overlay' ).addClass( 'show' );
						$( 'html' ).addClass( 'porto-search-opened porto-search-overlay-wrap' );
						$this.closest( '.vc_row.vc_row-flex>.vc_column_container>.vc_column-inner' ).css( 'z-index', '999' );
					} else if ( 'reveal' == self.form_layout  ) {
						self.parents = [];
						var $element = self.$popup;
						while ( ! ( ( $element.hasClass( 'elementor-container' ) && ! $element.parent().hasClass( 'elementor-inner-container' ) ) || ( $element.hasClass( 'e-con-inner' ) && ! $element.parent().hasClass( 'e-child' ) ) || ( $element.parent().hasClass( 'vc_row' ) && ! $element.parent().hasClass( 'vc_inner' ) ) || 'header' == $element.parent().attr( 'id' ) || $element.parent().hasClass( 'header-main' ) || $element.parent().hasClass( 'header-top' ) || $element.parent().hasClass( 'header-bottom' ) ) ) {
							$element = $element.parent();
							$element.addClass( 'position-static' );
							self.parents.push( $element );
						}
						if ( 'static' == $element.parent().css( 'position' ) ) {
							self.topParent = $element.parent();
							self.topParent.addClass( 'position-relative' );
						}
						$form.toggle();
						window.setTimeout( function () {
							$( 'body' ).addClass( 'porto-search-opened' );
							$form.find( '.text>input[name="s"]' ).focus();
						}, 100 );
					} else {
						$form.toggle();
					}
					if ( $this.hasClass( 'opened' ) ) {
						$( '#mini-cart.open' ).removeClass( 'open' );
						$this.next().find( 'input[type="text"]' ).focus();
						if ( self.$popup.find( '.btn-close-search-form' ).length ) {
							self.$popup.parent().addClass( 'position-static' );
						}
					} else if ( 'reveal' == self.form_layout ) {
						self.removeFormStyle();
					}
					e.preventDefault();
					e.stopPropagation();
				} );

				$( 'html,body' ).on( 'click', function() {
					self.removeFormStyle();
					$( '.search-lists' ).removeClass( 'show' );
				} );

				if ( !( 'ontouchstart' in document ) ) {

					$( window ).on( 'resize', function() {
						self.removeFormStyle();
					} );
				}

				$( '.btn-close-search-form' ).on( 'click', function( e ) {
					e.preventDefault();
					self.removeFormStyle();
				} );

				return self;
			},

			removeFormStyle: function() {
				this.$form.removeAttr( 'style' );
				var $searchToggle = this.$popup.find( '.search-toggle' );
				$searchToggle.removeClass( 'opened' );
				if ( 'overlay' == this.form_layout ) {
					$( 'html' ).removeClass( 'porto-search-opened porto-search-overlay-wrap' );
					$searchToggle.siblings( '.search-layout-overlay' ).removeClass( 'show' );
					$searchToggle.closest( '.vc_row.vc_row-flex>.vc_column_container>.vc_column-inner' ).css( 'z-index', '' );
				} else if ( 'reveal' == this.form_layout && this.parents && this.parents.length >= 1 ) {
					$( 'body' ).removeClass( 'porto-search-opened' );
					this.parents.forEach( $element => {
						$element.removeClass( 'position-static' );
					});
					if ( this.topParent ) {
						this.topParent.removeClass( 'position-relative' );
					}
				}
				if ( this.$popup.find( '.btn-close-search-form' ).length ) {
					this.$popup.parent().removeClass( 'position-static' );
				}
			}
		}

	} );

} ).apply( this, [window.theme, jQuery] );


// Carousel

( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__carousel';

	var Carousel = function( $el, opts ) {
		return this.initialize( $el, opts );
	};

	Carousel.defaults = $.extend( {}, {
		loop: true,
		navText: [],
		themeConfig: false,
		lazyLoad: true,
		lg: 0,
		md: 0,
		sm: 0,
		xs: 0,
		single: false,
		rtl: theme.rtl
	} );

	Carousel.prototype = {
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
			if ( ( opts && opts.themeConfig ) || !opts ) {
				this.options = $.extend( true, {}, Carousel.defaults, theme.owlConfig, opts, {
					wrapper: this.$el,
					themeConfig: true
				} );
			} else {
				this.options = $.extend( true, {}, Carousel.defaults, opts, {
					wrapper: this.$el
				} );
			}

			return this;
		},

		calcOwlHeight: function( $el ) {
			var h = 0;
			$el.find( '.owl-item.active' ).each( function() {
				if ( h < $( this ).height() )
					h = $( this ).height();
			} );
			$el.children( '.owl-stage-outer' ).height( h );
		},

		build: function() {
			if ( !$.fn.owlCarousel ) {
				return this;
			}

			var $el = this.options.wrapper,
				loop = this.options.loop,
				lg = this.options.lg,
				md = this.options.md,
				sm = this.options.sm,
				xs = this.options.xs,
				single = this.options.single,
				zoom = $el.find( '.zoom' ).filter( function() {
					if ( $( this ).closest( '.tb-image-type-slider' ).length ) {
						return false;
					}
					return true;
				} ).get( 0 ),
				responsive = {},
				items,
				count = $el.find( '.owl-item' ).length > 0 ? $el.find( '.owl-item:not(.cloned)' ).length : $el.find( '> *' ).length,
				fullscreen = typeof this.options.fullscreen == 'undefined' ? false : this.options.fullscreen;


			/*if (fullscreen) {
				$el.children().width(window.innerWidth - theme.getScrollbarWidth());
				$el.children().height($el.closest('.fullscreen-carousel').length ? $el.closest('.fullscreen-carousel').height() : window.innerHeight);
				$el.children().css('max-height', '100%');
				$(window).on('resize', function() {
					$el.find('.owl-item').children().width(window.innerWidth - theme.getScrollbarWidth());
					$el.find('.owl-item').children().height($el.closest('.fullscreen-carousel').length ? $el.closest('.fullscreen-carousel').height() : window.innerHeight);
					$el.find('.owl-item').children().css('max-height', '100%');
				});
			}*/

			if ( single ) {
				items = 1;
			} else if ( typeof this.options.responsive != 'undefined' ) {
				for ( var w in this.options.responsive ) {
					var number_items = Number( this.options.responsive[w] );
					responsive[Number( w )] = { items: number_items, loop: ( loop && count >= number_items ) ? true : false };
				}
			} else {
				items = this.options.items ? this.options.items : ( lg ? lg : 1 );
				var isResponsive = ( this.options.xxl || this.options.xl || lg || md || sm || xs );
				if ( isResponsive ) {
					if ( this.options.xxl ) {
						responsive[theme.screen_xxl] = { items: this.options.xxl, loop: ( loop && count > this.options.xxl ) ? true : false, mergeFit: this.options.mergeFit };
					} else if ( lg && items > lg + 1 ) {
						responsive[theme.screen_xxl] = { items: items, loop: ( loop && count > items ) ? true : false, mergeFit: this.options.mergeFit };
						responsive[theme.screen_xl] = { items: lg + 1, loop: ( loop && count > lg + 1 ) ? true : false, mergeFit: this.options.mergeFit };
					}
					if ( this.options.xl ) {
						responsive[theme.screen_xl] = { items: this.options.xl, loop: ( loop && count > this.options.xl ) ? true : false, mergeFit: this.options.mergeFit };
					} else if ( typeof responsive[theme.screen_xl] == 'undefined' && ( ! lg || items != lg ) ) {
						responsive[theme.screen_xl] = { items: items, loop: ( loop && count >= items ) ? true : false, mergeFit: this.options.mergeFit };
					}
					if ( lg ) responsive[992] = { items: lg, loop: ( loop && count >= lg ) ? true : false, mergeFit: this.options.mergeFit_lg };
					if ( md ) responsive[768] = { items: md, loop: ( loop && count > md ) ? true : false, mergeFit: this.options.mergeFit_md };
					if ( sm ) {
						responsive[576] = { items: sm, loop: ( loop && count > sm ) ? true : false, mergeFit: this.options.mergeFit_sm };
					} else {
						if ( xs && xs > 1 )  {
							responsive[576] = { items: xs, loop: ( loop && count > xs ) ? true : false, mergeFit: this.options.mergeFit_sm };
						} else {
							responsive[576] = { items: 1, mergeFit: false };
						}
					}
					if ( xs ) {
						responsive[0] = { items: xs, loop: ( loop && count > xs ) ? true : false, mergeFit: this.options.mergeFit_xs };
					} else {
						responsive[0] = { items: 1 };
					}
				}
			}

			if ( !$el.hasClass( 'show-nav-title' ) && this.options.themeConfig && theme.slider_nav && theme.slider_nav_hover ) {
				$el.addClass( 'show-nav-hover' );
			}

			this.options = $.extend( true, {}, this.options, {
				items: items,
				loop: ( loop && count > items ) ? true : false,
				responsive: responsive,
				onInitialized: function() {
					if ( $el.hasClass( 'stage-margin' ) ) {
						$el.find( '.owl-stage-outer' ).css( {
							'margin-left': this.options.stagePadding,
							'margin-right': this.options.stagePadding
						} );
					}
					var heading_cls = '.porto-u-heading, .vc_custom_heading, .slider-title, .elementor-widget-heading, .porto-heading';
					if ( $el.hasClass( 'show-dots-title' ) && ( $el.prev( heading_cls ).length || $el.closest( '.slider-wrapper' ).prev( heading_cls ).length || $el.closest( '.porto-recent-posts' ).prev( heading_cls ).length || $el.closest( '.elementor-widget-porto_recent_posts, .elementor-section' ).prev( heading_cls ).length ) ) {
						var $obj = $el.prev( heading_cls );
						if ( !$obj.length ) {
							$obj = $el.closest( '.slider-wrapper' ).prev( heading_cls );
						}
						if ( !$obj.length ) {
							$obj = $el.closest( '.porto-recent-posts' ).prev( heading_cls );
						}
						if ( !$obj.length ) {
							$obj = $el.closest( '.elementor-widget-porto_recent_posts, .elementor-section' ).prev( heading_cls );
						}
						try {
							var innerWidth = $obj.addClass( 'w-auto' ).css( 'display', 'inline-block' ).width();
							$obj.removeClass( 'w-auto' ).css( 'display', '' );
							if ( innerWidth + 15 + $el.find( '.owl-dots' ).width() <= $obj.width() ) {
								$el.find( '.owl-dots' ).css( ( $( 'body' ).hasClass( 'rtl' ) ? 'right' : 'left' ), innerWidth + 15 + ( $el.width() - $obj.width() ) / 2 );
								$el.find( '.owl-dots' ).css( 'top', -1 * $obj.height() / 2 - parseInt( $obj.css( 'margin-bottom' ) ) - $el.find( '.owl-dots' ).height() / 2 + 2 );
							} else {
								$el.find( '.owl-dots' ).css( 'position', 'static' );
							}
						} catch ( e ) { }
					}
				}
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

			var links = false;
			if ( zoom ) {
				links = [];
				var i = 0;

				$el.find( '.zoom' ).each( function() {
					var slide = {},
						$zoom = $( this );

					slide.src = $zoom.data( 'src' ) ? $zoom.data( 'src' ) : $zoom.data( 'mfp-src' );
					slide.title = $zoom.data( 'title' );
					links[i] = slide;
					$zoom.data( 'index', i );
					i++;
				} );
			}

			if ( $el.hasClass( 'show-nav-title' ) ) {
				this.options.stagePadding = 0;
			} else {
				if ( this.options.themeConfig && theme.slider_nav && theme.slider_nav_hover )
					$el.addClass( 'show-nav-hover' );
				if ( this.options.themeConfig && !theme.slider_nav_hover && theme.slider_margin )
					$el.addClass( 'stage-margin' );
			}
			if ( $el.hasClass( 'has-ccols-spacing' ) ) {
				$el.removeClass( 'has-ccols-spacing' );
			}
			$el.owlCarousel( this.options );

			if ( zoom && links ) {
				$el.on( 'click', '.zoom', function( e ) {
					e.preventDefault();
					if ( $.fn.magnificPopup ) {
						var image_index = $( this ).data( 'index' );
						if ( typeof image_index == 'undefined' ) {
							image_index = ( $( this ).closest( '.owl-item' ).index() - $el.find( '.cloned' ).length / 2 ) % $el.data( 'owl.carousel' ).items().length;
						}
						$.magnificPopup.close();
						$.magnificPopup.open( $.extend( true, {}, theme.mfpConfig, {
							items: links,
							gallery: {
								enabled: true
							},
							type: 'image'
						} ), image_index );
					}
					return false;
				} );
			}

			return this;
		}
	}
	// expose to scope
	$.extend( theme, {
		Carousel: Carousel
	} );

	// jquery plugin
	$.fn.themeCarousel = function( opts, zoom ) {
		if ( typeof $.fn.owlCarousel != 'function' ) {
			return this;
		}
		return this.map( function() {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this;
			} else {
				return new theme.Carousel( $this, opts, zoom );
			}

		} );
	}

} ).apply( this, [window.theme, jQuery] );

// Lightbox
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__lightbox';

	var Lightbox = function( $el, opts ) {
		return this.initialize( $el, opts );
	};

	Lightbox.defaults = {
		callbacks: {
			open: function() {
				$( 'body' ).addClass( 'lightbox-opened' );
			},
			close: function() {
				$( 'body' ).removeClass( 'lightbox-opened' );
			}
		}
	};

	Lightbox.prototype = {
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
			this.$el.data( instanceName, this );

			return this;
		},

		setOptions: function( opts ) {
			this.options = $.extend( true, {}, Lightbox.defaults, theme.mfpConfig, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function() {
			if ( !$.fn.magnificPopup ) {
				return this;
			}

			this.options.wrapper.magnificPopup( this.options );

			return this;
		}
	};

	// expose to scope
	$.extend( theme, {
		Lightbox: Lightbox
	} );

	// jquery plugin
	$.fn.themeLightbox = function( opts ) {
		return this.map( function() {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this.data( instanceName );
			} else {
				return new theme.Lightbox( $this, opts );
			}

		} );
	}

} ).apply( this, [window.theme, jQuery] );

// Post Filter
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {

		PostFilter: {

			cache: {
			},

			defaults: {
				elements: '.portfolio-filter'
			},

			initialize: function( $elements, post_type ) {
				this.$elements = ( $elements || $( this.defaults.elements ) );
				this.build( post_type );

				return this;
			},

			filterFn: function( e ) {
				if ( typeof e == 'undefined' || typeof e.data == 'undefined' || typeof e.data.elements == 'undefined' || !e.data.elements || !e.data.elements.length ) {
					return;
				}
				var self = e.data.selfobj;

				if ( self.isLoading ) {
					return false;
				}
				var $this = e.data.thisobj,
					$elements = e.data.elements,
					position = e.data.position,
					post_type = e.data.post_type,
					$parent = e.data.parent,
					$posts_wrap = e.data.posts_wrap,
					use_ajax = e.data.use_ajax,
					page_path = e.data.page_path,
					infinite_load = e.data.infinite_load,
					load_more = e.data.load_more;
				e.preventDefault();
				if ( $( this ).hasClass( 'active' ) ) {
					return;
				}

				self.isLoading = true;
				var selector = $( this ).attr( 'data-filter' );
				if ( 'sidebar' == position ) {
					$( '.sidebar-overlay' ).trigger( 'click' );
				}

				$this.find( '.active' ).removeClass( 'active' );

				if ( use_ajax ) {
					var current_cat = '*' == selector ? '' : selector;
					if ( !page_path ) {
						page_path = $posts_wrap.data( 'page_path' );
					}
					if ( page_path ) {
						$posts_wrap.data( 'page_path', page_path.replace( /&category=[^&]*&/, '&category=' + current_cat + '&' ) );
					}

					$( this ).addClass( 'active' );
					self.load_posts( current_cat, infinite_load || load_more ? true : false, $parent, post_type, $posts_wrap, undefined, $( this ).children( 'a' ).attr( 'href' ) );
				} else if ( 'faq' == post_type ) {
					$parent.find( '.faq' ).each( function() {
						var $that = $( this ), easing = "easeInOutQuart", timeout = 300;
						if ( selector == '*' ) {
							if ( $that.css( 'display' ) == 'none' ) $that.stop( true ).slideDown( timeout, easing, function() {
								$( this ).attr( 'style', '' ).show();
							} );
							selected++;
						} else {
							if ( $that.hasClass( selector ) ) {
								if ( $that.css( 'display' ) == 'none' ) $that.stop( true ).slideDown( timeout, easing, function() {
									$( this ).attr( 'style', '' ).show();
								} );
								selected++;
							} else {
								if ( $that.css( 'display' ) != 'none' ) $that.stop( true ).slideUp( timeout, easing, function() {
									$( this ).attr( 'style', '' ).hide();
								} );
							}
						}
					} );

					if ( !selected && $parent.find( '.faqs-infinite' ).length && typeof ( $.fn.infinitescroll ) != 'undefined' ) {
						$parent.find( '.faqs-infinite' ).infinitescroll( 'retrieve' );
					}
				} else if ( $parent.hasClass( 'portfolios-timeline' ) ) {
					var selected = 0;
					$parent.find( '.portfolio' ).each( function() {
						var $that = $( this ), easing = "easeInOutQuart", timeout = 300;
						if ( selector == '*' ) {
							if ( $that.css( 'display' ) == 'none' ) $that.stop( true ).slideDown( timeout, easing, function() {
								$( this ).attr( 'style', '' ).show();
							} );
							selected++;
						} else {
							if ( $that.hasClass( selector ) ) {
								if ( $that.css( 'display' ) == 'none' ) $that.stop( true ).slideDown( timeout, easing, function() {
									$( this ).attr( 'style', '' ).show();
								} );
								selected++;
							} else {
								if ( $that.css( 'display' ) != 'none' ) $that.stop( true ).slideUp( timeout, easing, function() {
									$( this ).attr( 'style', '' ).hide();
								} );
							}
						}
					} );
					if ( !selected && $parent.find( '.portfolios-infinite' ).length && typeof ( $.fn.infinitescroll ) != 'undefined' ) {
						$parent.find( '.portfolios-infinite' ).infinitescroll( 'retrieve' );
					}
					setTimeout( function() {
						theme.FilterZoom.initialize( $parent );
					}, 400 );
				} else {
					$parent.find( '.' + post_type + '-row' ).isotope( {
						filter: selector == '*' ? selector : '.' + selector
					} );
				}

				if ( !use_ajax ) {
					$( this ).addClass( 'active' );
					self.isLoading = false;
				}

				if ( position == 'sidebar' ) {
					self.$elements.each( function() {
						var $that = $( this );

						if ( $that == $this && $that.data( 'position' ) != 'sidebar' ) return;
						$that.find( 'li' ).removeClass( 'active' );
						$that.find( 'li[data-filter="' + selector + '"]' ).addClass( 'active' );
					} );
				}

				if ( !use_ajax ) {
					window.location.hash = '#' + selector;
				}
				theme.refreshVCContent();
				return false;
			},

			build: function( post_type_param ) {
				var self = this;

				self.$elements.each( function() {
					var $this = $( this ),
						position = $this.data( 'position' ),
						$parent,
						post_type;
					if ( typeof post_type_param == 'undefined' ) {
						if ( $this.hasClass( 'member-filter' ) ) {
							post_type = 'member';
						} else if ( $this.hasClass( 'faq-filter' ) ) {
							post_type = 'faq';
						} else if ( $this.hasClass( 'product-filter' ) ) {
							post_type = 'product';
						} else if ( $this.hasClass( 'post-filter' ) ) {
							post_type = 'post';
						} else if ( $this.hasClass( 'portfolio-filter' ) ) {
							post_type = 'portfolio';
						} else {
							post_type = $this.attr( 'data-filter-type' );
						}
					} else {
						post_type = post_type_param;
					}

					if ( 'sidebar' == position ) {
						$parent = $( '.main-content .page-' + post_type + 's' );
						//theme.scrolltoContainer($parent);
					} else if ( 'global' == position ) {
						$parent = $( '.main-content .page-' + post_type + 's' );
					} else {
						$parent = $this.closest( '.page-' + post_type + 's' );
					}
					if ( !$parent.length ) {
						$parent = $this.closest( '.porto-posts-grid' );
					}
					if ( !$parent || !$parent.length ) {
						return;
					}
					var use_ajax = $this.hasClass( 'porto-ajax-filter' ),
						infinite_load = $parent.hasClass( 'load-infinite' ),
						load_more = $parent.hasClass( 'load-more' );

					var $posts_wrap = $parent.find( '.' + post_type + 's-container' ),
						page_path;
					if ( use_ajax && ( ( !infinite_load && !load_more ) || !$parent.data( 'ajax_load_options' ) ) ) {
						var current_url = window.location.href;
						if ( -1 !== current_url.indexOf( '#' ) ) {
							current_url = current_url.split( '#' )[0];
						}
						page_path = theme.ajax_url + ( -1 === theme.ajax_url.indexOf( '?' ) ? '?' : '&' )  + 'action=porto_ajax_posts&nonce=' + js_porto_vars.porto_nonce + '&post_type=' + post_type + '&current_link=' + current_url + '&category=&page=%cur_page%';
						if ( $parent.data( 'post_layout' ) ) {
							page_path += '&post_layout=' + $parent.data( 'post_layout' );
						}
						$posts_wrap.data( 'page_path', page_path );
					}

					$this.find( 'li' ).on( 'click', { thisobj: $this, selfobj: self, elements: self.$elements, position: position, parent: $parent, post_type: post_type, posts_wrap: $posts_wrap, use_ajax: use_ajax, page_path: page_path, infinite_load: infinite_load, load_more: load_more }, self.filterFn );
				} );

				$( window ).on( 'hashchange', { elements: self.$elements }, self.hashchange );
				self.hashchange( { data: { elements: self.$elements } } );

				return self;
			},

			hashchange: function( e ) {
				if ( typeof e == 'undefined' || typeof e.data == 'undefined' || typeof e.data.elements == 'undefined' || !e.data.elements || !e.data.elements.length ) {
					return;
				}
				var $elements = e.data.elements,
					$filter = $( $elements.get( 0 ) ),
					hash = window.location.hash;

				if ( hash ) {
					var $o = $filter.find( 'li[data-filter="' + hash.replace( '#', '' ) + '"]' );
					if ( !$o.hasClass( 'active' ) ) {
						$o.trigger( 'click' );
					}
				}
			},

			set_elements: function( $elements ) {
				var self = this;
				if ( typeof $elements == 'undefined' || !$elements || !$elements.length ) {
					self.destroy( self.$elements );
					return;
				}
				self.$elements = $elements;
				$( window ).off( 'hashchange', self.hashchange ).on( 'hashchange', { elements: $elements }, self.hashchange );
			},

			destroy: function( $elements ) {
				if ( typeof $elements == 'undefined' || !$elements || !$elements.length ) {
					return;
				}
				var self = this;
				$elements.find( 'li' ).off( 'click', self.filterFn );
				$( window ).off( 'hashchange', self.hashchange );
			},

			load_posts: function( cat, is_infinite, $parent, post_type, $posts_wrap, default_args, page_url ) {
				var _gridcookie = '';
				if ( $parent.hasClass( 'archive-products' ) ) {
					_gridcookie = new URLSearchParams( location.search.substring( 1 ) ).get( 'gridcookie' );
					if ( ! ( _gridcookie == null || _gridcookie == '' || _gridcookie == 'grid' ) ) {
						page_url = theme.addUrlParam( page_url, 'gridcookie', _gridcookie );
					}
				}
				var pid = $parent.attr( 'id' ),
					self = this,
					is_archive = $parent.hasClass( 'archive-posts' ),
					successfn = function( res, directcall ) {
						if ( !res ) {
							return;
						}
						if ( ( typeof directcall == 'undefined' || true !== directcall ) && typeof default_args == 'undefined' && pid ) {
							if ( !self.cache[pid] ) {
								self.cache[pid] = {};
							}
							self.cache[pid][cat + _gridcookie] = res;
						}
						var $res = $( res ),
							is_shop = $parent.hasClass( 'archive-products' ),
							$posts = $res.find( is_archive ? '.archive-posts .posts-wrap' : '.posts-wrap' ).children();

						if ( !$posts.length ) {
							return;
						}

						if ( typeof $posts_wrap == 'undefined' || is_archive ) {
							$posts_wrap = $parent.find( '.' + post_type + 's-container' );
						}
						if ( !$posts_wrap.length ) {
							return;
						}

						if ( $posts_wrap.data( 'isotope' ) ) {
							$posts_wrap.isotope( 'remove', $posts_wrap.children() );
						} else {
							$posts_wrap.children().remove();
						}

						if ( $posts_wrap.hasClass( 'owl-loaded' ) ) {
							$posts_wrap.removeClass( 'owl-loaded' );
						}
						$posts.children().addClass( 'fadeInUp animated' );
						$posts_wrap.append( $posts );
						theme.refreshVCContent( $posts );

						// filter
						var $old_filter = $parent.find( '.' + post_type + '-filter' );
						if ( $old_filter.length && !$old_filter.hasClass( 'porto-ajax-filter' ) && !$parent.hasClass( 'load-infinite' ) && !$parent.hasClass( 'load-more' ) ) {
							var $new_filter = $res.find( ( is_archive ? '.archive-posts ' : '' ) + '.' + post_type + '-filter' );
							if ( $new_filter.length ) {
								$old_filter.find( 'li:first-child' ).trigger( 'click' );
								theme.PostFilter.destroy( $old_filter );
								$old_filter.replaceWith( $new_filter );
								//$new_filter = $parent.find( '.' + post_type + '-filter' );
								theme.PostFilter.initialize( $new_filter, post_type );
								theme.PostFilter.set_elements( $( 'ul[data-filter-type], ul.portfolio-filter, ul.member-filter, ul.faq-filter, .porto-ajax-filter.product-filter, .porto-ajax-filter.post-filter' ) );
							}
						}

						porto_init( $parent );

						var behavior_action = '';
						if ( post_type != 'product' && post_type != 'member' && post_type != 'faq' && post_type != 'portfolio' && post_type != 'post' ) {
							behavior_action = 'ptu';
						} else {
							behavior_action = post_type;
						}
						theme.PostsInfinite[behavior_action + 'Behavior']( $posts, $posts_wrap );

						// init CountDown
						$( document.body ).trigger( 'porto_init_countdown', [$posts_wrap] );

						// pagination
						var $old_pagination = $parent.find( '.pagination-wrap' ),
							$new_pagination = $res.find( ( is_archive ? '.archive-posts ' : '' ) + '.pagination-wrap' ).eq( 0 ),
							has_pagination = false,
							nst_pagination = false;
						if ( $old_pagination.length ) {
							if ( $new_pagination.length ) {
								$old_pagination.replaceWith( $new_pagination );
								has_pagination = true;
								nst_pagination = true;
							} else {
								$old_pagination.children().remove();
							}
						} else if ( $new_pagination.length ) {
							$parent.append( $new_pagination );
							has_pagination = true;
							nst_pagination = true;
						}

						if ( is_shop ) { // shop builder with counter per page widget
							let _paginationWrap = $( '.woocommerce-pagination' );
							var $old_pagination = _paginationWrap.find( 'ul.page-numbers' ),
							$new_pagination = $res.find( '.woocommerce-pagination ul.page-numbers' ).eq( 0 ),
							has_pagination = false;
							if ( $old_pagination.length ) {
								if ( $new_pagination.length ) {
									$old_pagination.replaceWith( $new_pagination );
									has_pagination = true;
								} else {
									$old_pagination.children().remove();
								}
							} else if ( $new_pagination.length ) {
								_paginationWrap.append( $new_pagination );
								has_pagination = true;
							}
						}
					

						if ( is_infinite ) {
							var infinitescroll_ins = $posts_wrap.data( 'infinitescroll' );
							if ( has_pagination || ( is_shop && nst_pagination ) ) {
								var $new_posts_wrap = $res.find( is_archive ? '.archive-posts .posts-wrap' : '.posts-wrap' );
								if ( $new_posts_wrap.data( 'cur_page' ) ) {
									$posts_wrap.data( 'cur_page', $new_posts_wrap.data( 'cur_page' ) );
									$posts_wrap.data( 'max_page', $new_posts_wrap.data( 'max_page' ) );
								}

								var should_init_again = true;
								if ( infinitescroll_ins ) {
									if ( infinitescroll_ins.options.state.isDestroyed ) {
										$posts_wrap.removeData( 'infinitescroll' );
									} else {
										should_init_again = false;
										if ( $new_posts_wrap.data( 'cur_page' ) ) {
											infinitescroll_ins.update( {
												maxPage: $new_posts_wrap.data( 'max_page' ),
												state: {
													currPage: $new_posts_wrap.data( 'cur_page' )
												}
											} );
										}
										if ( infinitescroll_ins.options.state.isPaused ) {
											infinitescroll_ins.resume();
										}
									}
								}

								if ( should_init_again ) {
									var ins = $posts_wrap.data( '__postsinfinite' );
									if ( ins ) {
										ins.destroy();
									}
									var selector_product = '.' + post_type + ', .timeline-date';
									if ( is_shop ) {
										selector_product = '.archive-products .product';
										if ( $( '.elementor-widget-wc-archive-products' ).length ) {
											selector_product = '.elementor-widget-wc-archive-products .product';
										}
									}
									new theme.PostsInfinite( $posts_wrap, selector_product, $posts_wrap.data( 'infiniteoptions' ), post_type );
								}

								if ( is_archive ) {
									var page_path = $posts_wrap.siblings( '.pagination-wrap' ).find( '.next' ).attr( 'href' );
									if ( page_path ) {
										page_path += ( -1 !== page_path.indexOf( '?' ) ? '&' : '?' ) + 'portoajax=1&load_posts_only=2';
										page_path = page_path.replace( /(paged=)(\d+)|(page\/)(\d+)/, '$1$3%cur_page%' );
										$posts_wrap.data( 'page_path', page_path );
									}
								}
								var selector_product = '.' + post_type + ', .timeline-date';
								if ( is_shop ) {
									selector_product = '.archive-products .product';
									if ( $( '.elementor-widget-wc-archive-products' ).length ) {
										selector_product = '.elementor-widget-wc-archive-products .product';
									}
								}
								new theme.PostsInfinite( $posts_wrap, selector_product, $posts_wrap.data( 'infiniteoptions' ), post_type );
							}
						}

						// in archive page
						if ( is_archive ) {
							// update widgets
							$( '.sidebar-content' ).each( function( index ) {
								var $this = $( this ),
									$that = $( $res.find( '.sidebar-content' ).get( index ) );

								$this.html( $that.html() );

								// in shop
								if ( is_shop ) {
									if ( typeof updateSelect2 != 'undefined' && updateSelect2 ) {
										// Use Select2 enhancement if possible
										if ( jQuery().selectWoo ) {
											var porto_wc_layered_nav_select = function() {
												$this.find( 'select.woocommerce-widget-layered-nav-dropdown' ).each( function() {
													$( this ).selectWoo( {
														placeholder: $( this ).find( 'option' ).eq( 0 ).text(),
														minimumResultsForSearch: 5,
														width: '100%',
														allowClear: typeof $( this ).attr( 'multiple' ) != 'undefined' && $( this ).attr( 'multiple' ) == 'multiple' ? 'false' : 'true'
													} );
												} );
											};
											porto_wc_layered_nav_select();
										}
										$( 'body' ).children( 'span.select2-container' ).remove();
									}
								}
							} );

							// in shop
							if ( is_shop ) {
								var $script = $res.filter( 'script:contains("var woocommerce_price_slider_params")' ).first();
								if ( $script && $script.length && $script.text().indexOf( '{' ) !== -1 && $script.text().indexOf( '}' ) !== -1 ) {
									var arrStr = $script.text().substring( $script.text().indexOf( '{' ), $script.text().indexOf( '}' ) + 1 );
									window.woocommerce_price_slider_params = JSON.parse( arrStr );
								}

								// update entry title
								var $title = $( '.entry-title' );
								if ( $title.length ) {
									var $newTitle = $res.find( '.entry-title' ).eq( 0 );
									if ( $newTitle.length ) {
										$title.html( $newTitle.html() );
									}
								}

								// update entry description
								var $desc = $( '.entry-description' );
								if ( $desc.length ) {
									var $newDesc = $res.find( '.entry-description' ).eq( 0 );
									if ( $newDesc.length ) {
										$desc.html( $newDesc.html() );
									}
								}

								// top toolbar
								var shop_before = '.shop-loop-before',
									$shop_before = $( shop_before );
								if ( $shop_before.length ) {
									if ( $res.find( shop_before ).length ) {
										$shop_before.each( function( index ) {
											var $res_shop_before = $res.find( shop_before ).eq( index );
											if ( $res_shop_before.length ) {
												$( this ).html( $res_shop_before.html() ).show();
											}
										} );
									} else {
										$shop_before.empty();
									}
								}

								// update result count
								var $count = $( '.woocommerce-result-count' );
								if ( $count.length ) {
									var $newCount = $res.find( '.woocommerce-result-count' ).eq( 0 );
									if ( $newCount.length ) {
										$count[0].outerHTML = $newCount.length ? $newCount[0].outerHTML : '';
									}
								}

								// trigger ready event
								$( document ).trigger( 'yith_wcan_init_shortcodes' );
								$( document ).trigger( 'yith-wcan-ajax-filtered' );
							}

							// update browser history (IE doesn't support it)
							if ( page_url && !navigator.userAgent.match( /msie/i ) ) {
								window.history.pushState( { 'pageTitle': ( res && res.pageTitle ) || '' }, '', page_url );
							}
						}

						$( document.body ).trigger( 'porto_load_posts_end', [$parent.parent()] );
					};

				if ( typeof default_args == 'undefined' && typeof self.cache[pid] != 'undefined' && typeof self.cache[pid][cat+ _gridcookie] != 'undefined' && self.cache[pid][cat+ _gridcookie] ) {
					successfn( self.cache[pid][cat+ _gridcookie], true );
					self.isLoading = false;
					$parent.removeClass( 'porto-ajax-loading' ).removeClass( 'loading' ).find( '.porto-loading-icon' ).remove();
					return;
				}

				var ajax_load_options = $parent.data( 'ajax_load_options' );
				if ( ( $parent.hasClass( 'archive-products' ) && -1 != js_porto_vars.use_skeleton_screen.indexOf( 'shop' ) ) ||
					( is_archive && -1 != js_porto_vars.use_skeleton_screen.indexOf( 'blog' ) ) ) { // skeleton screen in archive builder
					$posts_wrap = $parent.find( '.' + post_type + 's-container' );
					if ( ajax_load_options ) {
						var tag_name = 'div';
						if ( 'product' == post_type && 'ul' == $posts_wrap.get( 0 ).tagName.toLowerCase() ) {
							tag_name = 'li';
						}
						$posts_wrap.addClass( 'skeleton-body' ).empty();
						for ( var i = 0; i < Number( ajax_load_options.count || ( ajax_load_options.columns && ajax_load_options.columns * 3 ) || 12 ); i++ ) {
							$posts_wrap.append( '<' + tag_name + ' class="porto-tb-item post ' + post_type + ( 'product' == post_type ? ' product-col' : '' ) + '"></' + tag_name + '>' );
						}
					} else {
						$posts_wrap.addClass( 'skeleton-body' ).children().empty();
					}
				} else {
					if ( !$parent.children( '.porto-loading-icon' ).length ) {
						$parent.append( '<i class="porto-loading-icon"></i>' );
					}
					$parent.addClass( 'porto-ajax-loading' );
				}

				var current_url = window.location.href;
				if ( -1 !== current_url.indexOf( '#' ) ) {
					current_url = current_url.split( '#' )[0];
				}

				var args, load_url = theme.ajax_url;
				if ( $parent.hasClass( 'archive-posts' ) ) { // archive builder
					args = {
						portoajax: true,
						load_posts_only: true
					};
					if ( $parent.closest( '.porto-block' ).length ) {
						args['builder_id'] = $parent.closest( '.porto-block' ).data( 'id' );
					}
					load_url = typeof page_url != 'undefined' ? page_url : current_url;
				} else {
					args = {
						action: 'porto_ajax_posts',
						nonce: js_porto_vars.porto_nonce,
						post_type: post_type,
						current_link: current_url
					};
					if ( $parent.data( 'post_layout' ) ) {
						args['post_layout'] = $parent.data( 'post_layout' );
					}
					if ( ajax_load_options ) {
						args['extra'] = ajax_load_options;
					}
					if ( typeof default_args != 'undefined' ) {
						args = $.extend( args, default_args );
					}
				}
				if ( cat ) {
					args['category'] = cat;
				}

				$.ajax( {
					url: load_url,
					type: 'post',
					data: args,
					success: successfn,
					complete: function() {
						self.isLoading = false;
						$posts_wrap.removeClass( 'skeleton-body' );
						$parent.removeClass( 'porto-ajax-loading' ).removeClass( 'loading' ).find( '.porto-loading-icon' ).remove();
					}
				} );

			}
		}

	} );

} ).apply( this, [window.theme, jQuery] );

// Filter Zoom
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {

		FilterZoom: {

			defaults: {
				elements: null
			},

			initialize: function( $elements ) {
				this.$elements = ( $elements || this.defaults.elements );

				this.build();

				return this;
			},

			build: function() {
				var self = this;

				self.$elements.each( function() {
					var $this = $( this ),
						zoom = $this.find( '.zoom, .thumb-info-zoom' ).get( 0 );

					if ( !zoom ) return;

					$this.find( '.zoom, .thumb-info-zoom' ).off( 'click' );
					var links = [];
					var i = 0;
					$this.find( 'article' ).each( function() {
						var $that = $( this );
						if ( $that.css( 'display' ) != 'none' ) {
							var $zoom = $that.find( '.zoom, .thumb-info-zoom' ),
								slide,
								src = $zoom.data( 'src' ),
								title = $zoom.data( 'title' );

							$zoom.data( 'index', i );
							if ( Array.isArray( src ) ) {
								$.each( src, function( index, value ) {
									slide = {};
									slide.src = value;
									slide.title = title[index];
									links[i] = slide;
									i++;
								} );
							} else {
								slide = {};
								slide.src = src;
								slide.title = title;
								links[i] = slide;
								i++;
							}
						}
					} );
					$this.find( 'article' ).each( function() {
						var $that = $( this );
						if ( $that.css( 'display' ) != 'none' ) {
							$that.off( 'click', '.zoom, .thumb-info-zoom' ).on( 'click', '.zoom, .thumb-info-zoom', function( e ) {
								var $zoom = $( this ), $parent = $zoom.parents( '.thumb-info' ), offset = 0;
								if ( $parent.get( 0 ) ) {
									var $slider = $parent.find( '.porto-carousel' );
									if ( $slider.get( 0 ) ) {
										offset = $slider.data( 'owl.carousel' ).current() - $slider.find( '.cloned' ).length / 2;
									}
								}
								e.preventDefault();
								if ( $.fn.magnificPopup ) {
									$.magnificPopup.close();
									$.magnificPopup.open( $.extend( true, {}, theme.mfpConfig, {
										items: links,
										gallery: {
											enabled: true
										},
										type: 'image'
									} ), $zoom.data( 'index' ) + offset );
								}
								return false;
							} );
						}
					} );
				} );

				return self;
			}
		}

	} );

} ).apply( this, [window.theme, jQuery] );

/* initialize */
( function( theme, $ ) {
	theme.initAsync = function( $wrap, wrapObj ) {
		// Carousel
		if ( $.fn.themeCarousel ) {

			$( function() {
				
				var portoImgNavMiddle = function ( $el ) {
					var $images = $el.find( '.owl-item img' ),
						height = 0;
						for ( var i = 0; i < $images.length; i++) {
							var imgHeight = $images.eq(i).height();
							if ( height < imgHeight ) {
								height = imgHeight;
							}
						}
					if ( $el.hasClass( 'products-slider' ) ) {
						$el.children( '.owl-nav' ).css( 'top', ( 5 + height / 2 ) + 'px' );
					} else {
						$el.children( '.owl-nav' ).css( 'top', height / 2 + 'px' );
					}
				};
				// Carousel Lazyload images
				var portoCarouselInit = function( e ) {
					var $this = $( e.currentTarget );

					$this.find( '[data-appear-animation]:not(.appear-animation)' ).addClass( 'appear-animation' );
					if ( $this.find( '.owl-item.cloned' ).length ) {
						// compatibility with W3 Total Cache
						var $not_loaded = $this.find( 'img.lazy:not(.loaded)' );
						if ( $not_loaded.length ) {
							if (typeof window.w3tc_lazyload == 'object') {
								window.w3tc_lazyload.update();
							} else if (theme.w3tcLazyLoadInstance) {
								theme.w3tcLazyLoadInstance.update();
							}
						}

						if ( $.fn.themePluginLazyLoad ) {
							$this.find( '.porto-lazyload:not(.lazy-load-loaded)' ).themePluginLazyLoad( { effect: 'fadeIn', effect_speed: 400 } );
						}
						var $animates = e.currentTarget.querySelectorAll( '.appear-animation' );
						if ( $animates.length ) {
							if ( theme.animation_support ) {
								// theme.dynIntObsInit( $animates, 'themeAnimate', theme.Animate.defaults );
							} else {
								$animates.forEach( function( o ) {
									o.classList.add( 'appear-animation-visible' );
								} );
							}
						}
						if ( $.fn.themePluginAnimatedLetters && ( $( this ).find( '.owl-item.cloned [data-plugin-animated-letters]:not(.manual)' ).length ) ) {
							theme.dynIntObsInit( $( this ).find( '.owl-item.cloned [data-plugin-animated-letters]:not(.manual)' ), 'themePluginAnimatedLetters' );
						}
					}

					setTimeout( function() {
						var $hiddenItems = $this.find( '.owl-item:not(.active)' );
						if ( theme.animation_support ) {
							$hiddenItems.find( '.appear-animation' ).removeClass( 'appear-animation-visible' );
							$hiddenItems.find( '.appear-animation' ).each( function() {
								var $el = $( this ),
									delay = Math.abs( $el.data( 'appear-animation-delay' ) ? $el.data( 'appear-animation-delay' ) : 0 );
								if ( delay > 1 ) {
									this.style.animationDelay = delay + 'ms';
								}

								var duration = Math.abs( $el.data( 'appear-animation-duration' ) ? $el.data( 'appear-animation-duration' ) : 1000 );
								if ( 1000 != duration ) {
									this.style.animationDuration = duration + 'ms';
								}
							} );
						}
						if ( window.innerWidth >= 1200 ) {
							$hiddenItems.find( '[data-vce-animate]' ).removeAttr( 'data-vcv-o-animated' );
						}
						// Position the navigation in the middle of the image
						if ( $this.hasClass( 'nav-center-images-only' ) ) {
							portoImgNavMiddle( $this );
						}
					}, 300 );
				};
				var portoCarouselTranslated = function( e ) {
					var $this = $( e.currentTarget );
					/*if ( window.innerWidth > 767 ) {
						if ( $this.find( '.owl-item.cloned' ).length && $this.find( '.appear-animation:not(.appear-animation-visible)' ).length ) {
							$( document.body ).trigger( 'appear_refresh' );
						}
					}*/

					var $active = $this.find( '.owl-item.active' );
					if ( $active.hasClass( 'translating' ) ) {
						$active.removeClass( 'translating' );
						return;
					}
					$this.find( '.owl-item.translating' ).removeClass( 'translating' );
					// Animated Letters
					$this.find( '[data-plugin-animated-letters]' ).removeClass( 'invisible' );
					$this.find( '.owl-item.active [data-plugin-animated-letters]' ).trigger( 'animated.letters.initialize' );

					if ( window.innerWidth > 767 ) {
						// WPBakery
						$this.find( '.appear-animation' ).removeClass( 'appear-animation-visible' );
						$active.find( '.appear-animation' ).each( function() {
							var $animation_item = $( this ),
								anim_name = $animation_item.data( 'appear-animation' );
							$animation_item.addClass( anim_name + ' appear-animation-visible' );
						} );
					}

					// sticky sidebar
					if ( window.innerWidth > 991 ) {
						if ( $this.closest( '[data-plugin-sticky]' ).length ) {
							theme.refreshStickySidebar( false, $this.closest( '[data-plugin-sticky]' ) );
						}
					}

					// Elementor
					$active.find( '.slide-animate' ).each( function() {
						var $animation_item = $( this ),
							settings = $animation_item.data( 'settings' );
						if ( settings && ( settings._animation || settings.animation ) ) {
							var animation = settings._animation || settings.animation,
								delay = settings._animation_delay || settings.animation_delay || 0;
							theme.requestTimeout( function() {
								$animation_item.removeClass( 'elementor-invisible' ).addClass( 'animated ' + animation );
							}, delay );
						}
					} );

					// Visual Composer
					if ( window.innerWidth >= 1200 ) {
						$this.find( '[data-vce-animate]' ).removeAttr( 'data-vcv-o-animated' ).removeAttr( 'data-vcv-o-animated-fully' );
						$active.find( '[data-vce-animate]' ).each( function() {
							var $animation_item = $( this );
							if ( $animation_item.data( 'porto-origin-anim' ) ) {
								var anim_name = $animation_item.data( 'porto-origin-anim' );
								$animation_item.attr( 'data-vce-animate', anim_name ).attr( 'data-vcv-o-animated', true );
								var duration = parseFloat( window.getComputedStyle( this )['animationDuration'] ) * 1000,
									delay = parseFloat( window.getComputedStyle( this )['animationDelay'] ) * 1000;
								window.setTimeout( function() {
									$animation_item.attr( 'data-vcv-o-animated-fully', true );
								}, delay + duration + 5 );
							}
						} );
					}
				};
				var portoCarouselTranslateVC = function( e ) {
					var $this = $( e.currentTarget );
					$this.find( '.owl-item.active' ).addClass( 'translating' );

					if ( window.innerWidth >= 1200 ) {
						$this.find( '[data-vce-animate]' ).each( function() {
							var $animation_item = $( this );
							$animation_item.data( 'porto-origin-anim', $animation_item.data( 'vce-animate' ) ).attr( 'data-vce-animate', '' );
						} );
					}
				};
				var portoCarouselTranslateElementor = function( e ) {
					var $this = $( e.currentTarget );
					$this.find( '.owl-item.active' ).addClass( 'translating' );
					$this.find( '.owl-item:not(.active) .slide-animate' ).addClass( 'elementor-invisible' );
					$this.find( '.slide-animate' ).each( function() {
						var $animation_item = $( this ),
							settings = $animation_item.data( 'settings' );
						if ( settings._animation || settings.animation ) {
							$animation_item.removeClass( settings._animation || settings.animation );
						}
					} );
				};
				var portoCarouselTranslateWPB = function( e ) {
					if ( window.innerWidth > 767 ) {
						var $this = $( e.currentTarget );
						$this.find( '.owl-item.active' ).addClass( 'translating' );
						$this.find( '.appear-animation' ).each( function() {
							var $animation_item = $( this );
							$animation_item.removeClass( $animation_item.data( 'appear-animation' ) );
						} );
					}
				};

				var carouselItems = $wrap.find( '.owl-carousel:not(.manual)' );
				carouselItems.on( 'initialized.owl.carousel refreshed.owl.carousel', portoCarouselInit ).on( 'translated.owl.carousel', portoCarouselTranslated );
				carouselItems.on( 'translate.owl.carousel', function() {
					// Hide elements inside carousel
					$( this ).find( '[data-plugin-animated-letters]' ).addClass( 'invisible' );
					// Animated Letters
					$( this ).find( '[data-plugin-animated-letters]' ).trigger( 'animated.letters.destroy' );
				} );
				carouselItems.on( 'resized.owl.carousel', function () {
					var $this = $( this );
					// Position the navigation in the middle of the image
					if ( $this.hasClass( 'nav-center-images-only' ) ) {
						portoImgNavMiddle( $this );
					}
				} )
				carouselItems.filter( function() {
					if ( $( this ).find( '[data-vce-animate]' ).length ) {
						return true;
					}
					return false;
				} ).on( 'translate.owl.carousel', portoCarouselTranslateVC );
				carouselItems.filter( function() {
					var $anim_obj = $( this ).find( '.elementor-invisible' );
					if ( $anim_obj.length ) {
						$anim_obj.addClass( 'slide-animate' );
						return true;
					}
					return false;
				} ).on( 'translate.owl.carousel', portoCarouselTranslateElementor );
				carouselItems.filter( function() {
					if ( $( this ).find( '.appear-animation, [data-appear-animation]' ).length ) {
						return true;
					}
					return false;
				} ).on( 'translate.owl.carousel', portoCarouselTranslateWPB );

				$wrap.find( '[data-plugin-carousel]:not(.manual), .porto-carousel:not(.manual)' ).each( function() {
					var $this = $( this ),
						opts;
					if ( $this.closest( '.tab-pane' ).length && ! $this.closest( '.tab-pane' ).hasClass( 'active' ) ) {
						return;
					}
					if ( $this.closest( '.e-n-tabs-content > .e-con' ).length && ! $this.closest( '.e-n-tabs-content > .e-con' ).hasClass( 'e-active' ) ) {
						return;
					}
					if ( $this.closest( '.sidebar-menu:not(.side-menu-accordion) .menu-block' ).length ) {
						return;
					}
					if ( $this.closest( '.mega-menu .menu-block' ).length ) {
						return;
					}
					var pluginOptions = $this.data( 'plugin-options' );
					if ( pluginOptions )
						opts = pluginOptions;

					setTimeout( function() {
						$this.themeCarousel( opts );
					}, 0 );
				} );
			} );

		}
		// Fixed video
		$wrap.find( '.video-fixed' ).each( function() {
			var $this = $( this ),
				$video = $this.find( 'video, iframe' );

			if ( $video.length ) {
				window.addEventListener( 'scroll', function() {
					var offset = $( window ).scrollTop() - $this.position().top + theme.adminBarHeight();
					$video.css( "cssText", "top: " + offset + "px !important;" );
				}, { passive: true } );
			}
		} );
		setTimeout( function() {
			// Search
			if ( typeof theme.Search !== 'undefined' ) {
				theme.Search.initialize();
			}
		}, 0 );
		if ( typeof theme.isAsyncInit == 'undefined' ) {
			theme.isAsyncInit = -1; //initial
		}
		$( document.body ).trigger( 'porto_after_async_init', [ $wrap, wrapObj ] );
		theme.isAsyncInit = 1;
	};

	$( document.body ).trigger( 'porto_async_init' );
} ).apply( this, [window.theme, jQuery] );

jQuery( document ).ready( function( $ ) {
	'use strict';

	function porto_modal_open( $this ) {
		var trigger = $this.data( 'trigger-id' ),
			overlayClass = $this.data( 'overlay-class' ),
			disableOverlay = $this.data( 'disable-overlay' ),
			extraClass = $this.data( 'extra-class' ) ? $this.data( 'extra-class' ) : '',
			type = $this.data( 'type' ),
			buttonPopup = false,
			isExist = 0;
		if ( typeof trigger != 'undefined'/* && $('#' + escape(trigger)).length > 0*/ ) {
			if ( typeof type == 'undefined' ) {
				type = 'inline';
			}
			if ( type == 'inline' ) {
				trigger = '#' + escape( trigger );
			}
			if ( typeof $.magnificPopup.instance != 'undefined' && $.magnificPopup.instance.isOpen ) {
				isExist = 350;
				$.magnificPopup.close(); // Destroy before init
			}

			try {
				if ( document.querySelector( trigger ) ) {
					if ( 'script' == document.querySelector( trigger ).nodeName.toLowerCase() ) { // Dynamic Popup Click
						trigger = $( trigger ).html();
						disableOverlay = $( trigger ).data( 'disable-overlay' );
						overlayClass = $( trigger ).data( 'overlay-class' );
						extraClass = 'button-popup ';
						buttonPopup = true;
					}
				}
			} catch( e ) {

			}

			var args = {
				items: {
					src: trigger
				},
				type: type,
				mainClass: extraClass + ( disableOverlay ? ' popup-builder-disable-overlay ' : '' ),
				prependTo: $( '.page-wrapper' )
			};
			if ( trigger == '#popup-builder' ) {
				args['fixedContentPos'] = true;
			}
			var $popupModal = $this;
			if ( buttonPopup ) {
				args['fixedContentPos'] = true;
				if ( disableOverlay ) {
					args['closeOnBgClick'] = false;
					args['fixedContentPos'] = false;
					args['focus'] = '.bb-none'; // random selector
				}
				args['callbacks'] = {
					'open': function () {
						if ( disableOverlay ) {
							$( '.button-popup.mfp-bg' ).remove();
							
						}
						// Update bootstrap tooltip for Popup
						var $popup_builder = $( '.button-popup.mfp-wrap' );
						if ( typeof theme.porto_init_builder_tooltip == 'function' ) {
							theme.porto_init_builder_tooltip( $popup_builder.get(0) );
						}
						porto_init( $popup_builder );

						if ( $popup_builder.find( '.wpcf7' ).length ) {
							var $wpcf7 = $popup_builder.find( '.wpcf7 .wpcf7-form' );
							if ( typeof wpcf7 == 'object' ) {
								if ( typeof wpcf7.initForm == 'function' ) {
									wpcf7.initForm( $wpcf7 );
								} else if ( typeof wpcf7.init == 'function' ) {
									wpcf7.init( $wpcf7.get( 0 ) );
									$wpcf7.get( 0 ).classList.replace( "no-js", "js" );
								}
							}
						}
						if ( $popup_builder.find( '.wpforms-container' ).length ) {
							if ( typeof wpforms == 'object' && typeof wpforms.init == 'function' ) {
								wpforms.init();
							}
						}
					}
				};
			}
			if ( $this.hasClass( 'porto-onload' ) || $this.hasClass( 'porto-exit-intent' ) ) {
				if ( disableOverlay ) {
					args['closeOnBgClick'] = false;
					args['fixedContentPos'] = false;
					args['focus'] = '.bb-none'; // random selector
				}
				args['callbacks'] = {
					'beforeClose': function() {
						if ( $( '.mfp-wrap .porto-disable-modal-onload' ).length && ( $( '.mfp-wrap .porto-disable-modal-onload' ).is( ':checked' ) || $( '.mfp-wrap .porto-disable-modal-onload input[type="checkbox"]' ).is( ':checked' ) ) ) {
							$.cookie( 'porto_modal_disable_onload', 'true', { expires: 7 } );
						} else if ( 'undefined' !== typeof $popupModal.data( 'expired' ) && 'undefined' !== typeof $popupModal.data( 'popup-id' ) ) {
							$.cookie( 'porto_modal_disable_period_onload_' + $popupModal.data( 'popup-id' ), $popupModal.data('expired'), { expires: $popupModal.data('expired') } );
						}
					},
					'afterClose': function() {
						// If minicart(cart, wishlist) opened, keep disabling mouse scrolling
						if ( $( '#header .minicart-opened' ).length ) {
							$( 'html' ).css( theme.rtl_browser ? 'margin-left' : 'margin-right', theme.getScrollbarWidth() );
							$( 'html' ).css( 'overflow', 'hidden' );
						}
					},
					'open': function () {
						if ( disableOverlay ) {
							$( '.popup-builder.mfp-bg' ).remove();
						}
						// Update bootstrap tooltip for Popup
						var $popup_builder = $( '.mfp-wrap .porto-block[data-bs-original-title]' );
						if ( $popup_builder.length ) {
							bootstrap.Tooltip.getInstance( $popup_builder[0] ).update();
						}
						if ( $popup_builder.find( '.marquee' ).length && $.isFunction( $.fn.marquee ) ) {
							$popup_builder.find( '.marquee' ).marquee( {
								duration: 5000,
								gap: 0,
								delayBeforeStart: 0,
								direction: 'left',
								duplicated: true
							} );
						}
						if ( $( '.mfp-wrap .porto-block .owl-carousel' ) ) {
							$( '.mfp-wrap .porto-block .owl-carousel' ).trigger( 'refresh.owl.carousel' );
						}
					}
				};
			}
			if ( typeof overlayClass != "undefined" && overlayClass ) {
				args.mainClass += escape( overlayClass );
			}
			setTimeout( () => {
				$.magnificPopup.open( $.extend( true, {}, theme.mfpConfig, args ), 0 );
			}, isExist );

		}
	}
	theme.porto_modal_open = porto_modal_open;
	function porto_init_magnific_popup_functions( $wrap ) {
		if ( typeof $wrap == 'undefined' || !$wrap.length ) {
			$wrap = $( document.body );
		}
		$wrap.find( '.lightbox:not(.manual)' ).each( function() {
			var $this = $( this ),
				opts;
			if ( $this.find( '>.lb-dataContainer' ).length ) {
				return;
			}
			var pluginOptions = $this.data( 'lightbox-options' );
			if ( pluginOptions ) {
				opts = pluginOptions;
			} else {
				pluginOptions = $this.data( 'plugin-options' );
				if ( typeof pluginOptions != 'object' ) {
					pluginOptions = JSON.parse( pluginOptions );
				}
				if ( pluginOptions ) {
					opts = pluginOptions;
				}
			}

			$this.themeLightbox( opts );
		} );

		// Popup with video or map
		$wrap.find( '.porto-popup-iframe' ).magnificPopup( $.extend( true, {}, theme.mfpConfig, {
			disableOn: 700,
			type: 'iframe',
			mainClass: 'mfp-fade',
			removalDelay: 160,
			preloader: false,
			fixedContentPos: false
		} ) );

		// Popup with ajax
		$wrap.find( '.porto-popup-ajax' ).magnificPopup( $.extend( true, {}, theme.mfpConfig, {
			type: 'ajax'
		} ) );

		// Popup with content
		$wrap.find( '.porto-popup-content' ).each( function() {
			var animation = $( this ).attr( 'data-animation' );
			$( this ).magnificPopup( $.extend( true, {}, theme.mfpConfig, {
				type: 'inline',
				fixedContentPos: false,
				fixedBgPos: true,
				overflowY: 'auto',
				closeBtnInside: true,
				preloader: false,
				midClick: true,
				removalDelay: 300,
				mainClass: animation
			} ) );
		} );

		// Porto Modal
		$wrap.find( '.popup-youtube, .popup-vimeo, .popup-gmaps' ).each( function( index ) {
			var overlayClass = $( this ).find( '.porto-modal-trigger' ).data( 'overlay-class' ),
				args = {
					type: 'iframe',
					removalDelay: 160,
					preloader: false,

					fixedContentPos: false
				};
			if ( typeof overlayClass != "undefined" && overlayClass ) {
				args.mainClass = escape( overlayClass );
			}
			$( this ).magnificPopup( args );
		} );

		$wrap.find( '[href*=porto-action_popup-id-]' ).each( function() {
			var $this = $( this ), popupId = $this.attr( 'href' );
			if ( popupId = popupId.match(/#porto-action_popup-id-(\d+)/)[1] ) {
				$this.attr( 'data-trigger-id', 'popup-id-' + popupId );
				$this.addClass( 'porto-modal-trigger' );
			}
		} );

		if ( $wrap.find( '.porto-modal-trigger.porto-onload' ).length ) {
			var $obj = $wrap.find( '.porto-modal-trigger.porto-onload' ).eq( 0 ),
				timeout = 0;
			if ( $obj.data( 'timeout' ) ) {
				timeout = parseInt( $obj.data( 'timeout' ), 10 );
			}
			setTimeout( function() {
				porto_modal_open( $obj );
			}, timeout );
		}
		$wrap.on( 'click', '.porto-modal-trigger', function( e ) {
			e.preventDefault();
			porto_modal_open( $( this ) );
		} );

		/* Woocommerce */
		// login popup
		if ( $wrap.hasClass( 'login-popup' ) ) {
			$wrap.find( '.porto-link-login, .porto-link-register' ).magnificPopup( {
				items: {
					src: theme.ajax_url + ( -1 === theme.ajax_url.indexOf( '?' ) ? '?' : '&' )  + 'action=porto_account_login_popup&nonce=' + js_porto_vars.porto_nonce,
					type: 'ajax'
				},
				tLoading: '<i class="porto-loading-icon"></i>',
				callbacks: {
					ajaxContentAdded: function() {
						$( window ).trigger( 'porto_login_popup_opened' );
					}
				}
			} );
		}

		if ( typeof PhotoSwipe != 'undefined' ) {
			let _images = $wrap.find( '.product-images' ), links = [], i = 0;
			_images.find( 'img' ).each( function() {
				var slide = {};

				slide.src = $( this ).attr( 'href' );
				slide.title = $( this ).attr( 'alt' );
				slide.w = parseInt( $( this ).attr( 'data-large_image_width' ) );
				slide.h = parseInt( $( this ).attr( 'data-large_image_height' ) );
				
				links[i] = slide;
				i++;
			} );
			_images.data( 'links', links );
			_images.on( 'click', '.img-thumbnail a.zoom', function( e ) {
				e.preventDefault();
				var options = $.extend( {
					index: $( this ).closest( '.img-thumbnail' ).index(),
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
				var photoswipe = new PhotoSwipe( $( '.pswp' )[0], PhotoSwipeUI_Default, _images.data( 'links' ), options );
				photoswipe.init();
			} );


		} else {
			$wrap.find( '.product-images' ).magnificPopup(
				$.extend( true, {}, theme.mfpConfig, {
					delegate: '.img-thumbnail a.zoom',
					type: 'image',
					gallery: { enabled: true }
				} )
			);
		}
		$wrap.find( '.porto-posts-grid' ).each( function() {
			$( this ).magnificPopup(
				$.extend( true, {}, theme.mfpConfig, {
					delegate: '.porto-tb-featured-image span.zoom, .porto-tb-featured-image a.zoom, .post-image span.zoom',
					type: 'image',
					gallery: { enabled: true }
				} )
			);
		} );
		$wrap.find( '.porto-posts-grid .tb-image-type-slider div.zoom' ).each( function() {
			var $this = $( this ),
				links = [];
			$this.find( 'a' ).each( function() {
				var slide = {};
				slide.src = $( this ).attr( 'href' );
				slide.title = $( this ).attr( 'title' );
				links.push( slide );
			} );
			if ( links.length ) {
				$this.on( 'click', function() {
					var $slider = $this.siblings( '.porto-carousel' );
					if ( $slider.length ) {
						var offset = $slider.data( 'owl.carousel' ).current() - $slider.find( '.cloned' ).length / 2;
						$.magnificPopup.open( $.extend( true, {}, theme.mfpConfig, {
							items: links,
							gallery: {
								enabled: true
							},
							type: 'image'
						} ), offset );
					}
				} );
			}
		} );
	}

	if ( $.fn.magnificPopup ) {
		porto_init_magnific_popup_functions();
	} else {
		setTimeout( function() {
			if ( $.fn.magnificPopup ) {
				porto_init_magnific_popup_functions();
			}
		}, 500 );
	}
	$( document.body ).on( 'porto_load_posts_end', function( e, $posts_wrap ) {
		if ( $.fn.magnificPopup ) {
			porto_init_magnific_popup_functions( $posts_wrap );
		}
	} );

	// Post Filter
	if ( typeof theme.PostFilter !== 'undefined' ) {
		var $postFilterElements = $( 'ul[data-filter-type], .portfolio-filter, .member-filter, .faq-filter, .porto-ajax-filter.product-filter, .porto-ajax-filter.post-filter' );
		if ( $postFilterElements.length ) {
			theme.PostFilter.initialize( $postFilterElements );
		}
	}

	// Post ajax pagination
	$( 'body' ).on( 'click', '.porto-ajax-load .pagination:not(.load-more) .page-numbers', function( e ) {
		var $this = $( this );
		if ( $this.hasClass( 'current' ) || $this.hasClass( 'dots' ) ) {
			return;
		}
		e.preventDefault();
		var $wrap = $this.closest( '.porto-ajax-load' ),
			post_type = $wrap.data( 'post_type' ),
			$obj = $wrap.find( '.' + post_type + 's-container' );

		if ( !$obj.length || $wrap.hasClass( 'loading' ) ) {
			return;
		}
		$wrap.addClass( 'loading' );
		var $filter = $wrap.find( '.porto-ajax-filter' ),
			cat = $filter.length && $filter.children( '.active' ).length && $filter.children( '.active' ).data( 'filter' );
		if ( '*' == cat ) {
			cat = '';
		}
		var default_args = {},
			page = $this.attr( 'href' ).match( /paged=(\d+)|page\/(\d+)/ );

		if ( page && Array.isArray( page ) && ( page[1] || page[2] ) ) {
			default_args['page'] = parseInt( page[1] || page[2] );
		} else {
			if ( $this.hasClass( 'prev' ) ) {
				default_args['page'] = parseInt( $this.next().text() );
			} else if ( $this.hasClass( 'next' ) ) {
				default_args['page'] = parseInt( $this.prev().text() );
			} else {
				default_args['page'] = parseInt( $this.text() );
			}
		}
		if ( cat == '' && $wrap.find( 'input[type=hidden].category' ).length ) {
			cat = $wrap.find( 'input[type=hidden].category' ).val();
			default_args['taxonomy'] = $wrap.find( 'input[type=hidden].taxonomy' ).val();
		}
		theme.PostFilter.load_posts( cat, $wrap.hasClass( 'load-infinite' ), $wrap, post_type, $obj, default_args, $this.attr( 'href' ) );

	} );

	// Filter Zooms
	if ( typeof theme.FilterZoom !== 'undefined' ) {
		// Portfolio Filter Zoom
		theme.FilterZoom.initialize( $( '.page-portfolios' ) );
		// Member Filter Zoom
		theme.FilterZoom.initialize( $( '.page-members' ) );
		// Posts Related Style Filter Zoom
		theme.FilterZoom.initialize( $( '.blog-posts-related' ) );
	}

	// close popup using esc key
	var $minicart_offcanvas = $( '.minicart-offcanvas' ),
		$wl_offcanvas = $( '.wishlist-offcanvas' ),
		$mobile_sidebar = $( '.mobile-sidebar' ),
		$mobile_panel = $( '#side-nav-panel' ),
		$overlay_search = $( '#header .btn-close-search-form' ),
		$html = $( 'html' );
	if ( $minicart_offcanvas.length || $wl_offcanvas.length || $mobile_sidebar.length || $mobile_panel.length || $( '.skeleton-loading' ).length || $overlay_search.length ) {
		$( document.documentElement ).on( 'keyup', function( e ) {
			try {
				if ( e.keyCode == 27 ) {
					$minicart_offcanvas.removeClass( 'minicart-opened' );
					$wl_offcanvas.removeClass( 'minicart-opened' );
					if ( $mobile_sidebar.length ) {
						$html.removeClass( 'filter-sidebar-opened' );
						$html.removeClass( 'sidebar-opened' );
						$( '.sidebar-overlay' ).removeClass( 'active' );
						$( 'html' ).css( 'overflow', '' );
						$( 'html' ).css( theme.rtl_browser ? 'margin-left' : 'margin-right', '' );
					}
					if ( $mobile_panel.length && $html.hasClass( 'panel-opened' ) ) {
						$html.removeClass( 'panel-opened' );
						$( '.panel-overlay' ).removeClass( 'active' );
					}
					if ( $overlay_search.length ) {
						$overlay_search.trigger( 'click' );
					}
				}
			} catch ( err ) { }
		} );
		$( '.skeleton-loading' ).on( 'skeleton-loaded', function() {
			$mobile_sidebar = $( '.mobile-sidebar' );
		} );
	}
} );
