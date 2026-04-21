/*
* Footer Reveal
*/

( function( $ ) {
	var footerReveal = {

		$wrapper: $( '.footer-reveal' ),
		footerHeight: -1,
		init: function() {
			var self = this;

			self.build();
			self.events();
		},

		build: function() {
			var self = this,
				footer_height = self.$wrapper.outerHeight( true );
			if ( self.footerHeight == footer_height ) {
				return;
			}
			self.footerHeight = footer_height;
			
			var	window_height = window.innerHeight - theme.adminBarHeight();
			if ( $( '#header .header-main' ).length ) {
				window_height -= $( '#header .header-main' ).height();
			}

			if ( footer_height > window_height ) {
				$( '.footer-wrapper' ).removeClass( 'footer-reveal' );
				$( '.page-wrapper' ).css( 'margin-bottom', 0 );
			} else {
				$( '.footer-wrapper' ).addClass( 'footer-reveal' );
				$( '.page-wrapper' ).css( 'margin-bottom', footer_height );
				if ( document.body.offsetHeight < window.innerHeight ) {
					document.body.style.paddingBottom = '0.1px';
				}
			}

		},

		events: function() {
			var self = this,
				$window = $( window );

			$window.smartresize( function() {
				self.build();
			} );
			$window.on( 'scroll', function() {
				self.build();
			} );			
		}
	}

	if ( $( '.footer-reveal' ).get( 0 ) ) {
		footerReveal.init();
	}

} )( jQuery );