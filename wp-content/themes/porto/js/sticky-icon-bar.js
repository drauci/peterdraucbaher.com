
// Porto Sticky icon bar on mobile
( function( theme, $ ) {
	'use strict';

	var $header_main = $( '#header .header-main' );
	var $menu_wrap = $( '#header .main-menu-wrap' );
	if ( $( '.porto-sticky-navbar' ).length > 0 ) {
		window.addEventListener( 'scroll', function() {
			if ( window.innerWidth < 576 ) {
				var headerOffset = -1;
				var scrollTop = $( window ).scrollTop();

				if ( $header_main.length ) {
					headerOffset = Math.max( $header_main.scrollTop() + $header_main.height(), headerOffset );
				}
				if ( $menu_wrap.length ) {
					headerOffset = Math.max( $menu_wrap.scrollTop() + $menu_wrap.height(), headerOffset );
				}
				if ( headerOffset <= 0 ) {
					if ( $( '#header' ).length > 0 && $( '#header' ).height() > 10 ) headerOffset = $( '#header' ).scrollTop() + $( '#header' ).height();
					else headerOffset = 100;
				}
				if ( headerOffset <= scrollTop ) {
					$( '.porto-sticky-navbar' ).addClass( 'fixed' );
				} else {
					$( '.porto-sticky-navbar' ).removeClass( 'fixed' );
				}
			}
		}, { passive: true } );
	}
} )( window.theme, jQuery );
