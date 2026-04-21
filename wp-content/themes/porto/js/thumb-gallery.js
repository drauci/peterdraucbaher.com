( function( theme, $ ) {
    theme = theme || {};
    $( document.body ).on( 'porto_after_async_init', function( e, $wrap, wrapObj ) {
		// Thumb Gallery
		$wrap.find( '.thumb-gallery-thumbs, .thumbnail-gallery' ).each( function() {
			var $thumbs = $( this ),
				$detail = $thumbs.parent().find( '.thumb-gallery-detail' ),
				flag = false,
				duration = 300;

			if ( $thumbs.data( 'initThumbs' ) )
				return;

			$detail.on( 'changed.owl.carousel', function( e ) {
				if ( !flag ) {
					flag = true;
					var len = $detail.find( '.owl-item' ).length,
						cloned = $detail.find( '.cloned' ).length,
						currentSlide = e.item.index - cloned / 2;
						currentSlide = ( currentSlide + e.item.count ) % e.item.count;

					if ( len ) {
						$thumbs.find( '.owl-item.selected' ).removeClass( 'selected' );
						$thumbs.find( '.owl-item' ).eq( currentSlide ).addClass( 'selected' );
						$thumbs.trigger( 'to.owl.carousel', [currentSlide, duration, true] );
					}
					flag = false;
				}
			} );

			$thumbs.on( 'changed.owl.carousel', function( e ) {
				if ( !flag ) {
					flag = true;
					var len = $thumbs.find( '.owl-item' ).length,
						cloned = $thumbs.find( '.cloned' ).length;
					if ( len ) {
						$thumbs.find( '.owl-item.selected' ).removeClass( 'selected' );
						$thumbs.find( '.owl-item' ).eq( e.item.index ).addClass( 'selected' );
						$detail.trigger( 'to.owl.carousel', [( e.item.index - cloned / 2 ) % len, duration, true] );
					}
					flag = false;
				}
			} ).on( 'click', '.owl-item', function() {
				if ( !flag ) {
					flag = true;
					var len = $thumbs.find( '.owl-item' ).length,
						cloned = $thumbs.find( '.cloned' ).length;
					if ( len ) {
						$thumbs.find( '.owl-item.selected' ).removeClass( 'selected' );
						$(this).addClass( 'selected' );
						$detail.trigger( 'to.owl.carousel', [( $( this ).index() - cloned / 2 ) % len, duration, true] );
					}
					flag = false;
				}
			} ).data( 'initThumbs', true );
		} );
    } );
} ).apply( this, [window.theme, jQuery] );