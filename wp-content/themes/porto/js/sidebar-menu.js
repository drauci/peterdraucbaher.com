// Sidebar Menu
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {

		SidebarMenu: {

			defaults: {
				menu: $( '.sidebar-menu:not(.side-menu-accordion)' ),
				toggle: $( '.widget_sidebar_menu .widget-title .toggle' ),
				menu_toggle: $( '#main-toggle-menu .menu-title' )
			},

			rtl: theme.rtl,

			initialize: function( $menu, $toggle, $menu_toggle ) {
				if ( this.$menu && this.$menu.length && $menu && $menu.length ) {
					this.$menu = $.uniqueSort( $.merge( this.$menu, $menu ) );
					this.build();
					return this;
				}
				this.$menu = ( $menu || this.defaults.menu );
				if ( !this.$menu.length ) {
					return this;
				}
				this.$toggle = ( $toggle || this.defaults.toggle );
				this.$menu_toggle = ( $menu_toggle || this.defaults.menu_toggle );

				this.events();

				return this;
			},

			isRightSidebar: function( $menu ) {
				var flag = false;
				if ( this.rtl ) {
					flag = !( $( '#main' ).hasClass( 'column2-right-sidebar' ) || $( '#main' ).hasClass( 'column2-wide-right-sidebar' ) );
				} else {
					flag = $( '#main' ).hasClass( 'column2-right-sidebar' ) || $( '#main' ).hasClass( 'column2-wide-right-sidebar' );
				}

				if ( $menu.closest( '#main-toggle-menu' ).length ) {
					if ( this.rtl ) {
						flag = true;
					} else {
						flag = false;
					}
				}

				var $header_wrapper = $menu.closest( '.header-wrapper' );
				if ( $header_wrapper.length && $header_wrapper.hasClass( 'header-side-nav' ) ) {
					if ( this.rtl ) {
						flag = true;
					} else {
						flag = false;
					}
					if ( $( '.page-wrapper' ).hasClass( 'side-nav-right' ) ) {
						if ( this.rtl ) {
							flag = false;
						} else {
							flag = true;
						}
					}
				}

				return flag;
			},

			popupWidth: function() {
				var winWidth = window.innerWidth,
					popupWidth = theme.bodyWidth - theme.grid_gutter_width * 2;
				if ( !$( 'body' ).hasClass( 'wide' ) ) {
					if ( winWidth >= 1140 + theme.grid_gutter_width && winWidth <= theme.container_width + 2 * theme.grid_gutter_width - 1 && theme.container_width >= 1360 )
						popupWidth = 1140 - theme.grid_gutter_width;
					else if ( winWidth >= theme.container_width + theme.grid_gutter_width - 1 )
						popupWidth = theme.container_width - theme.grid_gutter_width;
					else if ( winWidth >= 992 )
						popupWidth = 960 - theme.grid_gutter_width;
					else if ( winWidth >= 768 )
						popupWidth = 720 - theme.grid_gutter_width;
				}
				return popupWidth;
			},

			build: function( $menus ) {
				var self = this;
				if ( !$menus ) {
					$menus = self.$menu;
				}
				if ( !$menus.length ) {
					return;
				}


				$menus.find( '.menu-item-has-children' ).each( function () {
					var $this = $(this);
					if ( $this.find( '>.popup' ).length > 0 && $this.find( '>.popup>.inner>.sub-menu > li:not(.hidden-item)' ).length == 0 ) {
						$this.addClass( 'hidden-item' );
					}
				} );

				var $parent_toggle_wrap = $menus.parent( '.toggle-menu-wrap' ),
					parent_toogle_wrap = null;
				if ( $parent_toggle_wrap.length && $parent_toggle_wrap.is( ':hidden' ) ) {
					parent_toogle_wrap = $parent_toggle_wrap.get( 0 );
					parent_toogle_wrap.style.display = 'block';
					parent_toogle_wrap.style.visibility = 'hidden';
				}

				$menus.each( function() {
					var menuobj = this, $menu = $( this ), container_width;
					if ( menuobj.classList.contains( 'side-menu-slide' ) ) {
						return;
					}
					if ( window.innerWidth < 992 )
						container_width = self.popupWidth();
					else {
						var menu_width = this.offsetWidth ? this.offsetWidth : $menu.width();
						container_width = self.popupWidth() - menu_width - 45;
					}

					var is_right_sidebar = self.isRightSidebar( $menu ),
						$menu_items = $menu.children( 'li' );

					$menu_items.each( function() {
						var $menu_item = $( this ),
							$popup = $menu_item.children( '.popup' );

						if ( $popup.length ) {
							var popup_obj = $popup.get( 0 ),
								is_opened = false;
							if ( $popup.is( ':visible' ) ) {
								is_opened = true;
							} else {
								popup_obj.style.display = 'block';
							}
							if ( $menu_item.hasClass( 'wide' ) ) {
								if ( !$menu.hasClass( 'side-menu-columns' ) ) {
									popup_obj.style.left = 0;
								}
								var row_number = 4;

								if ( $menu_item.hasClass( 'col-1' ) ) row_number = 1;
								if ( $menu_item.hasClass( 'col-2' ) ) row_number = 2;
								if ( $menu_item.hasClass( 'col-3' ) ) row_number = 3;
								if ( $menu_item.hasClass( 'col-4' ) ) row_number = 4;
								if ( $menu_item.hasClass( 'col-5' ) ) row_number = 5;
								if ( $menu_item.hasClass( 'col-6' ) ) row_number = 6;

								if ( window.innerWidth < 992 )
									row_number = 1;

								var col_length = 0;
								$popup.find( '> .inner > ul > li' ).each( function() {
									var cols = parseFloat( $( this ).data( 'cols' ) );
									if ( !cols || cols <= 0 )
										cols = 1;

									if ( cols > row_number )
										cols = row_number;

									col_length += cols;
								} );

								if ( col_length > row_number ) col_length = row_number;

								var popup_max_width = $popup.data( 'popup-mw' ) ? $popup.data( 'popup-mw' ) : $popup.find( '.inner' ).css( 'max-width' ),
									col_width = container_width / row_number;
								if ( 'none' !== popup_max_width && parseInt( popup_max_width ) < container_width ) {
									col_width = parseInt( popup_max_width ) / row_number;
								}

								$popup.find( '> .inner > ul > li' ).each( function() {
									var cols = parseFloat( $( this ).data( 'cols' ) );
									if ( cols <= 0 )
										cols = 1;

									if ( cols > row_number )
										cols = row_number;

									if ( $menu_item.hasClass( 'pos-center' ) || $menu_item.hasClass( 'pos-left' ) || $menu_item.hasClass( 'pos-right' ) )
										this.style.width = ( 100 / col_length * cols ) + '%';
									else
										this.style.width = ( 100 / row_number * cols ) + '%';
								} );

								popup_obj.children[0].children[0].style.width = col_width * col_length + 1 + 'px';

								if ( !$menu.hasClass( 'side-menu-columns' ) ) {
									if ( is_right_sidebar ) {
										popup_obj.style.left = 'auto';
										popup_obj.style.right = ( this.offsetWidth ? this.offsetWidth : $( this ).width() ) + 'px';
									} else {
										popup_obj.style.left = ( this.offsetWidth ? this.offsetWidth : $( this ).width() ) + 'px';
										popup_obj.style.right = 'auto';
									}
								}
							}

							if ( !is_opened ) {
								popup_obj.style.display = 'none';
							}
							if ( menuobj.classList.contains( 'side-menu-accordion' ) ) {

							} else if ( menuobj.classList.contains( 'side-menu-slide' ) ) {

							} else if ( !$menu_item.hasClass( 'sub-ready' ) ) {
								if ( !( 'ontouchstart' in document ) && window.innerWidth > 991 ) {
									$menu_item.on( 'mouseenter', function() {
										$menu_items.find( '.popup' ).hide();
										$popup.show();
										$popup.parent().addClass( 'open' );
										//$( document.body ).trigger( 'appear_refresh' );

										if ( !$menu.hasClass( 'side-menu-columns' ) && 'static' !== $popup.parent().css('position') ) {
											let _thisTop = this.getBoundingClientRect().top;
											
											if ( this.offsetParent && ( _thisTop + popup_obj.offsetHeight > theme.innerHeight ) ) {
												// let _top = ( popup_obj.offsetHeight - this.offsetHeight ) / 2;
												let _top = _thisTop + popup_obj.offsetHeight - theme.innerHeight;
												let _top1 = _thisTop - this.parentNode.getBoundingClientRect().top;
												if ( _top1 < _top ) {
													_top = _top1;
												}
												popup_obj.style.top = -1 * ( _top ) + 'px';
												popup_obj.style.setProperty( '--porto-sd-menu-popup-top', -1 * ( _top ) + 'px' );
											} else {
												popup_obj.style.top = '';
												popup_obj.style.setProperty( '--porto-sd-menu-popup-top', '' );
											}
										}
										if ( $popup.find( '.owl-carousel' ).length ) {
											$popup.find( '.owl-carousel' ).each( function() {
												var $this = $( this ),
													opts;
												if ( ! $this.hasClass( 'owl-loaded' ) ) {
													var pluginOptions = $this.data( 'plugin-options' );
													if ( pluginOptions )
														opts = pluginOptions;
								
													$this.themeCarousel( opts );
												}
											} );
										}

									} ).on( 'mouseleave', function() {
										$popup.hide();
										$popup.parent().removeClass( 'open' );
									} );
								} else {
									$menu_item.on( 'click', '.arrow', function() {
										if ( ! $menu.hasClass( 'side-menu-columns' ) && ! $popup.parent().hasClass( 'open' ) && window.innerWidth > 991 ) {
											$menus.children( 'li.has-sub' ).removeClass( 'open' ).children( '.popup' ).hide();
										}
										$popup.slideToggle();
										$popup.parent().toggleClass( 'open' );
										//$( document.body ).trigger( 'appear_refresh' );
									} );
								}
								$menu_item.addClass( 'sub-ready' );
							}
						}
					} );
				} );

				// slide menu
				if ( $menus.hasClass( 'side-menu-slide' ) ) {
					var slideNavigation = {
						$mainNav: $menus,
						$mainNavItem: $menus.find( 'li' ),

						build: function() {
							var self = this;

							self.menuNav();
						},
						initSub: function( $obj ) {
							var currentMenu = $obj.closest( 'ul' ),
								nextMenu = $obj.parent().find( 'ul' ).first();

							if ( nextMenu.children( '.menu-item' ).children( '.go-back' ).length < 1 ) {
								nextMenu.prepend( '<li class="menu-item"><a class="go-back" href="#">' + js_porto_vars.submenu_back + '</a></li>' );
							}


							currentMenu.addClass( 'next-menu' );

							nextMenu.addClass( 'visible' );
							currentMenu.css( {
								overflow: 'visible',
								'overflow-y': 'visible'
							} );

							//for (i = 0; i < nextMenu.find('> li').length; i++) {
							if ( nextMenu.outerHeight() < ( nextMenu.closest( '.header-main' ).outerHeight() - 100 ) ) {
								nextMenu.css( {
									height: nextMenu.outerHeight() + nextMenu.find( '> li' ).outerHeight()
								} );
							}
							// }

							var nextMenuHeightDiff = nextMenu.find( '> li' ).length * nextMenu.find( '> li' ).outerHeight() - nextMenu.outerHeight();

							if ( nextMenuHeightDiff > 0 ) {
								nextMenu.css( {
									overflow: 'hidden',
									'overflow-y': 'scroll'
								} );
							}

				

							nextMenu.css( {
								'padding-top': nextMenuHeightDiff + 'px'
							} );
						},
						menuNav: function() {
							var self = this;

							self.$mainNav.find( '.menu-item-has-children > a.nolink' ).removeClass( 'nolink' ).attr( 'href', '' );

							self.$mainNav.find( '.menu-item-has-children > a:not(.go-back)' ).off( 'click' ).on( 'click', function( e ) {
								e.stopImmediatePropagation();
								e.preventDefault();
								var $this = $( this );
								if ( js_porto_vars.lazyload_menu && !self.$mainNav.hasClass( 'sub-ready' ) ) {
									self.initSub( $this );
									self.$mainNav.on( 'sub-loaded', function() {
										self.initSub( $this );
									} );
								} else {
									self.initSub( $this );
								}
							} );
						}
					};

					slideNavigation.build();
				}

				if ( parent_toogle_wrap ) {
					parent_toogle_wrap.style.display = '';
					parent_toogle_wrap.style.visibility = '';
				}

				return self;
			},

			events: function() {
				var self = this;

				self.$toggle.on( 'click', function() {
					var $widget = $( this ).parent().parent();
					var $this = $( this );
					if ( $this.hasClass( 'closed' ) ) {
						$this.removeClass( 'closed' );
						$widget.removeClass( 'closed' );
						$widget.find( '.sidebar-menu-wrap' ).stop().slideDown( 400, function() {
							$( this ).attr( 'style', '' ).show();
							self.build();
						} );
					} else {
						$this.addClass( 'closed' );
						$widget.addClass( 'closed' );
						$widget.find( '.sidebar-menu-wrap' ).stop().slideUp( 400, function() {
							$( this ).attr( 'style', '' ).hide();
						} );
					}
				} );

				this.$menu_toggle.on( 'click', function() {
					var $toggle_menu = $( this ).parent();
					if ( $toggle_menu.hasClass( 'show-always' ) || $toggle_menu.hasClass( 'show-hover' ) ) {
						return;
					}
					var $this = $( this );
					if ( $this.hasClass( 'closed' ) ) {
						$this.removeClass( 'closed' );
						$toggle_menu.removeClass( 'closed' );
						$toggle_menu.find( '.toggle-menu-wrap' ).stop().slideDown( 400, function() {
							$( this ).attr( 'style', '' ).show();
						} );

						self.build();

					} else {
						$this.addClass( 'closed' );
						$toggle_menu.addClass( 'closed' );
						$toggle_menu.find( '.toggle-menu-wrap' ).stop().slideUp( 400, function() {
							$( this ).attr( 'style', '' ).hide();
						} );
					}
				} );

				if ( self.$menu.hasClass( 'side-menu-slide' ) ) {
					self.$menu.on( 'click', '.go-back', function( e ) {
						e.preventDefault();
						var prevMenu = $( this ).closest( '.next-menu' ),
							prevMenuHeightDiff = 0;
						if ( prevMenu.length && prevMenu.find( '> li' ).length ) {
							prevMenuHeightDiff = prevMenu.find( '> li' ).length * prevMenu.find( '> li' ).outerHeight() - prevMenu.outerHeight();
						}




						prevMenu.removeClass( 'next-menu' );
						$( this ).closest( 'ul' ).removeClass( 'visible' );

						if ( prevMenuHeightDiff > 0 ) {
							prevMenu.css( {
								overflow: 'hidden',
								'overflow-y': 'scroll'
							} );
						}
					} );
				}

				if ( $( '.sidebar-menu:not(.side-menu-accordion)' ).closest( '[data-plugin-sticky]' ).length ) {
					var sidebarRefreshTimer;
					$( window ).smartresize( function() {
						if ( sidebarRefreshTimer ) {
							clearTimeout( sidebarRefreshTimer );
						}
						sidebarRefreshTimer = setTimeout( function() {
							self.build();
						}, 800 );
					} );
				} else {
					$( window ).smartresize( function( e ) {
						if ( e.originalEvent ) {
							self.build();
						}
					} );
				}

				setTimeout( function() {
					self.build();
				}, 400 );

				if ( 'ontouchstart' in document ) {
					$( document.body ).on( 'click', function(e) {
						if ( window.innerWidth > 991 ) {
							if ( ! $( e.target ).closest( 'li.has-sub.open' ).length ) {
								self.$menu.each( function() {
									var $this = $(this);
									if ( $this.hasClass( 'side-menu-accordion' ) || $this.hasClass( 'side-menu-slide' ) || $this.hasClass( 'side-menu-columns' ) ) {
										return;
									}

									$this.children( 'li.has-sub' ).removeClass( 'open' ).children( '.popup' ).hide();
								} );
							}
						}
					} );
				}

				return self;
			}
		}

	} );

} ).apply( this, [window.theme, jQuery] );



jQuery( document ).ready( function( $ ) {
    // Sidebar Menu
    if ( typeof theme.SidebarMenu !== 'undefined' ) {
        if ( ! theme.bodyWidth ) {
            theme.bodyWidth = theme.bodyWidth || document.body.offsetWidth;
        }
        theme.SidebarMenu.initialize();

        if ( $.fn.themeAccordionMenu ) {
            $( '.sidebar-menu.side-menu-accordion' ).themeAccordionMenu( { 'open_one': true } );
        }
    }
} );