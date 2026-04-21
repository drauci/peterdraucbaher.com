// Lazyload Menu
( function( theme, $ ) {

	'use strict';

	theme = theme || {};

	// expose to scope
	$.extend( theme, {
		lazyload_menu: function( $el, menu_type, menu_id ) {
			if ( !js_porto_vars.lazyload_menu && 'mobile_menu' != menu_type ) {
				return;
			}
			if ( menu_type ) {
				var reload_menu = false,
					menu_args = {
						action: 'porto_lazyload_menu',
						menu_type: menu_type,
						nonce: js_porto_vars.porto_nonce
					};
				if ( menu_id ) {
					menu_args['menu_id'] = menu_id;
				}
				var menu_loaded_fn = function( data ) {
					if ( !data ) {
						return;
					}
					var $data = $( data );
					if ( 'mobile_menu' != menu_type ) {
						$el.each( function( i ) {
							var $menu = $( this ),
								$main_menu = $data.children( '.mega-menu, .sidebar-menu' ).eq( i );
							if ( !$main_menu.length ) {
								$main_menu = $data.find( '.mega-menu, .sidebar-menu' ).eq( i );
							}
							$menu.children( 'li.menu-item-has-children' ).each( function( index ) {
								var popup = $main_menu.children( 'li.menu-item-has-children' ).eq( index ).children( '.popup, .sub-menu' );
								if ( popup.hasClass( 'popup' ) ) {
									popup = popup.children( '.inner' );
								}
								if ( popup.length ) {
									if ( $( this ).children( '.popup' ).length ) {
										$( this ).children( '.popup' ).children( '.inner' ).replaceWith( popup );
									} else if ( $menu.hasClass( 'overlay' ) ) {
										$( this ).children( '.sub-menu' ).remove();
										$( this ).append( popup );
									} else {
										$( this ).children( '.sub-menu' ).replaceWith( popup );
									}
								}
							} );
							if ( $menu.hasClass( 'mega-menu' ) ) {
								theme.MegaMenu.build( $menu );
							} else {
								if ( $menu.hasClass( 'side-menu-accordion' ) ) {
									$menu.themeAccordionMenu( { 'open_one': true } );
								} else {
									theme.SidebarMenu.build( $menu );
								}
							}
							$menu.addClass( 'sub-ready' ).trigger( 'sub-loaded' );
						} );
					}
					if ( $data.find( '#nav-panel, #side-nav-panel' ).length || 'mobile_menu' == menu_type ) {
						var lazyload_again = false;
						if ( $( '#nav-panel' ).length ) {
							var $menu_content = $data.find( '.mobile-nav-wrap > *' );
							if ( $menu_content.length ) {
								$( '#nav-panel .mobile-nav-wrap > *' ).replaceWith( $menu_content );
								$( '#nav-panel .mobile-nav-wrap' ).removeClass( 'skeleton-body porto-ajax-loading' );
								$( '#nav-panel .accordion-menu' ).themeAccordionMenu();
							} else {
								lazyload_again = true;
							}
						} else if ( $( '#side-nav-panel' ).length ) {
							var $menu_content = $data.find( '#side-nav-panel' );
							if ( $menu_content.length ) {
								$( '#side-nav-panel' ).replaceWith( $menu_content );
								$( '#side-nav-panel .accordion-menu' ).themeAccordionMenu();
							} else {
								lazyload_again = true;
							}
						}
						if ( lazyload_again && !reload_menu ) {
							reload_menu = true;
							lazyload_again = false;
							var menu_again_args = menu_args;
							menu_again_args['porto_lazyload_menu_2'] = 1;
							$.post( window.location.href, menu_again_args, menu_loaded_fn );
						}
					}
					if ( typeof $el == 'object' && $el.length ) {
						if ( $.fn.themePluginLazyLoad ) {
							$el.find( '.porto-lazyload:not(.lazy-load-loaded)' ).themePluginLazyLoad( {} );
						}
						$el.find( '.porto-carousel' ).each( function() {
							$( this ).themeCarousel( $( this ).data( 'plugin-options' ) );
						} );
						$el.find( '[data-appear-animation]' ).each( function() {
							$( this ).themeAnimate( $( this ).data( 'plugin-options' ) );
						} );
					}
				};
				var page_url = window.location.href;
				if ( theme.WooEvents && theme.WooEvents.removeParameterFromUrl ) {
					page_url = theme.WooEvents.removeParameterFromUrl( page_url, 'add-to-cart' );
				}
				$.post( page_url, menu_args, menu_loaded_fn );
			}
		}
	} );

} ).apply( this, [window.theme, jQuery] );


jQuery( document ).ready( function($) {
    // Lazy load Menu
    if ( js_porto_vars.lazyload_menu ) {
        var menu_type, $menu_obj;
        if ( $( '.secondary-menu.mega-menu' ).length ) {
            menu_type = 'secondary_menu';
            $menu_obj = $( '.secondary-menu.mega-menu' );
            menu_lazyload( $menu_obj, menu_type );
        }
        if ( $( '.mega-menu.main-menu:not(.scroll-wrapper):not(.secondary-menu)' ).length ) {
            menu_type = 'main_menu';
            $menu_obj = $( '.mega-menu.main-menu:not(.scroll-wrapper):not(.secondary-menu)' );
            menu_lazyload( $menu_obj, menu_type );
        }
        if ( $( '.toggle-menu-wrap .sidebar-menu' ).length ) {
            menu_type = 'toggle_menu';
            $menu_obj = $( '.toggle-menu-wrap .sidebar-menu' );
            menu_lazyload( $menu_obj, menu_type );
        }
        if ( $( '.main-sidebar-menu .sidebar-menu' ).length ) {
            menu_type = 'sidebar_menu';
            $menu_obj = $( '.main-sidebar-menu .sidebar-menu' );
            $menu_obj.each( function() {
                let $menu_item = $( this );
                menu_lazyload( $menu_item, menu_type, $menu_item.closest( '.main-sidebar-menu' ).data( 'menu' ) );
            } );

        }
        if ( $( '.header-side-nav .sidebar-menu' ).length ) {
            menu_type = 'header_side_menu';
            $menu_obj = $( '.header-side-nav .sidebar-menu' );
            menu_lazyload( $menu_obj, menu_type );
        }

        if ( $( '#nav-panel .skeleton-body, #side-nav-panel .skeleton-body' ).length && 'pageload' == js_porto_vars.lazyload_menu ) {
            theme.lazyload_menu( 1, 'mobile_menu' );
        }

        function menu_lazyload( $menu_item, menu_type, menu_id ) {
            var porto_menu_loaded = false;
            if ( 'pageload' == js_porto_vars.lazyload_menu ) {
                theme.lazyload_menu( $menu_item, menu_type, menu_id );
            } else if ( 'firsthover' == js_porto_vars.lazyload_menu ) {
                $menu_item.one( 'mouseenter touchstart', 'li.menu-item-has-children', function() {
                    if ( porto_menu_loaded ) {
                        return true;
                    }
                    theme.lazyload_menu( $menu_item, menu_type, menu_id );
                    porto_menu_loaded = true;
                } );
            }
        }
    }
});