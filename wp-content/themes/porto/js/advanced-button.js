( function( theme, $ ) {
	/* Advanced Buttons */
	$( '.porto-btn[data-hover]' ).on( 'mouseenter', function() {
		var hoverColor = $( this ).data( 'hover' ),
			hover_border_color = $( this ).data( 'border-hover' );
		if ( hoverColor ) {
			$( this ).data( 'originalColor', $( this ).css( 'color' ) );
			$( this ).css( 'color', hoverColor );
		}
		if ( hover_border_color ) {
			$( this ).data( 'originalBorderColor', $( this ).css( 'border-color' ) );
			$( this ).css( 'border-color', hover_border_color );
		}
	} ).on( 'mouseleave', function() {
		var originalColor = $( this ).data( 'originalColor' ),
			original_border_color = $( this ).data( 'originalBorderColor' );
		if ( originalColor ) {
			$( this ).css( 'color', originalColor );
		}
		if ( original_border_color ) {
			$( this ).css( 'border-color', original_border_color );
		}
	} );
})( window.theme, jQuery);