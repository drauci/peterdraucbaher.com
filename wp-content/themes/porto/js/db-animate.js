window.theme = window.theme || {};

( function( theme, $ ) {
	'use strict';
	theme = theme || {};

	var disableMobileAnimations = function () {
		if ( $( document.body ).hasClass( 'porto-dm-animate' ) && window.innerWidth < 768 ) {
			theme.animation_support = false;
			$( '[data-appear-animation]' ).removeAttr( 'data-appear-animation' );
			$( '.elementor-invisible' ).removeAttr( 'data-settings' ).removeData( 'settings' ).removeClass( 'elementor-invisible' )
				.add( $( '.appear-animation' ).removeClass( 'appear-animation' ) );
		}
	}
	// expose to scope
	$.extend( theme, {
		disableMobileAnimations: disableMobileAnimations
	} );
	theme.disableMobileAnimations();
} ).apply( this, [window.theme, jQuery] );