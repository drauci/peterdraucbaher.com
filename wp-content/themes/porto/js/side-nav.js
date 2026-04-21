// Side Nav
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {

		SideNav: {

			defaults: {
				side_nav: $( '.header-side-nav #header' )
			},

			bc_pos_top: 0,

			initialize: function( $side_nav ) {
				this.$side_nav = ( $side_nav || this.defaults.side_nav );

				if ( !this.$side_nav.length )
					return this;

				var self = this;

				self.$side_nav.addClass( 'initialize' );

				self.reset()
					.build()
					.events();

				return self;
			},

			build: function() {
				var self = this;

				var $page_top = $( '.page-top' ),
					$main = $( '#main' );

				if ( theme.isTablet() ) {
					//self.$side_nav.removeClass("fixed-bottom");
					$page_top.removeClass( "fixed-pos" );
					$page_top.attr( 'style', '' );
					$main.attr( 'style', '' );
				} else {
					//var side_height = self.$side_nav.innerHeight();
					//var window_height = window.innerHeight;
					var scroll_top = $( window ).scrollTop();

					/*if (side_height - window_height + theme.adminBarHeight() < scroll_top) {
						if (!self.$side_nav.hasClass("fixed-bottom"))
							self.$side_nav.addClass("fixed-bottom");
					} else {
						if (self.$side_nav.hasClass("fixed-bottom"))
							self.$side_nav.removeClass("fixed-bottom");
					}*/

					if ( $page_top.length && $page_top.outerHeight() < 100 && !$( '.side-header-narrow-bar-top' ).length ) {
						if ( self.page_top_offset == theme.adminBarHeight() || self.page_top_offset <= scroll_top ) {
							if ( !$page_top.hasClass( "fixed-pos" ) ) {
								$page_top.addClass( "fixed-pos" );
								$page_top.css( 'top', theme.adminBarHeight() );
								$main.css( 'padding-top', $page_top.outerHeight() );
							}
						} else {
							if ( $page_top.hasClass( "fixed-pos" ) ) {
								$page_top.removeClass( "fixed-pos" );
								$page_top.attr( 'style', '' );
								$main.attr( 'style', '' );
							}
						}
					}
					$main.css( 'min-height', window.innerHeight - theme.adminBarHeight() - $( '.page-top:not(.fixed-pos)' ).height() - $( '.footer-wrapper' ).height() );
				}

				return self;
			},

			reset: function() {
				var self = this;

				if ( theme.isTablet() && $( '.side-header-narrow-bar' ).length == 0 ) {

					self.$side_nav.attr( 'style', '' );

				} else {

					var w_h = window.innerHeight,
						$side_bottom = self.$side_nav.find( '.side-bottom' );

					self.$side_nav.css( {
						'min-height': w_h - theme.adminBarHeight(),
						'padding-bottom': $side_bottom.length ? $side_bottom.height() + parseInt( $side_bottom.css( 'margin-top' ) ) + parseInt( $side_bottom.css( 'margin-bottom' ) ) : ''
					} );

					var appVersion = navigator.appVersion;
					var webkitVersion_positionStart = appVersion.indexOf( "AppleWebKit/" ) + 12;
					var webkitVersion_positionEnd = webkitVersion_positionStart + 3;
					var webkitVersion = appVersion.slice( webkitVersion_positionStart, webkitVersion_positionEnd );
					if ( webkitVersion < 537 ) {
						self.$side_nav.css( '-webkit-backface-visibility', 'hidden' );
						self.$side_nav.css( '-webkit-perspective', '1000' );
					}
				}

				var $page_top = $( '.page-top' ),
					$main = $( '#main' );

				if ( $page_top.length ) {
					$page_top.removeClass( "fixed-pos" );
					$page_top.attr( 'style', '' );
					$main.attr( 'style', '' );
					self.page_top_offset = $page_top.offset().top;
				}

				return self;
			},

			events: function() {
				var self = this;

				$( window ).on( 'resize', function() {
					self.reset()
						.build();
				} );

				window.addEventListener( 'scroll', function() {
					self.build();
				}, { passive: true } );

				if ( $( '.side-header-narrow-bar-top' ).length ) {
					if ( $( window ).scrollTop() > theme.adminBarHeight() + $( '.side-header-narrow-bar-top' ).height() ) {
						$( '.side-header-narrow-bar-top' ).addClass( 'side-header-narrow-bar-sticky' );
					}
					window.addEventListener( 'scroll', function() {
						var scroll_top = $( this ).scrollTop();
						if ( scroll_top > theme.adminBarHeight() + $( '.side-header-narrow-bar-top' ).height() ) {
							$( '.side-header-narrow-bar-top' ).addClass( 'side-header-narrow-bar-sticky' );
						} else {
							$( '.side-header-narrow-bar-top' ).removeClass( 'side-header-narrow-bar-sticky' );
						}
					}, { passive: true } );
				}

				// Side Narrow Bar
				$( '.side-header-narrow-bar .hamburguer-btn' ).on( 'click', function() {
					$( this ).toggleClass( 'active' );
					$( '#header' ).toggleClass( 'side-header-visible' );
					if ( $( this ).closest( '.side-header-narrow-bar-top' ).length && !$( '#header > .hamburguer-btn' ).length ) {
						$( this ).closest( '.side-header-narrow-bar-top' ).toggle();
					}
				} );
				$( '.hamburguer-close' ).on( 'click', function() {
					$( '.side-header-narrow-bar .hamburguer-btn' ).trigger( 'click' );
				} );

				return self;
			}
		}

	} );

} ).apply( this, [window.theme, jQuery] );


window.jQuery( document ).ready( function() {
    // Side Navigation
    if ( typeof theme.SideNav !== 'undefined' ) {
        theme.SideNav.initialize();
    }
} );