// Blog / Portfolio Like
( function( theme, $ ) {
	'use strict';

	$( function() {
		$( document ).on( 'click', '.blog-like, .portfolio-like', function( e ) {
			e.preventDefault();
			var $this = $( this ),
				parentObj = this.parentNode,
				item_id = $this.attr( 'data-id' ),
				is_blog = $this.hasClass( 'blog-like' ),
				sendData = { nonce: js_porto_vars.porto_nonce };
			if ( is_blog ) {
				if ( $this.hasClass( 'updating' ) ) {
					return false;
				}
				$this.addClass( 'updating' ).text( '...' );
				sendData.blog_id = item_id;
				sendData.action = 'porto_blog-like';
			} else {
				sendData.portfolio_id = item_id;
				sendData.action = 'porto_portfolio-like';
			}
			$.post(
				theme.ajax_url,
				sendData,
				function( data ) {
					if ( data ) {
						$this.remove();
						parentObj.innerHTML = data;
						if ( typeof bootstrap != 'undefined' ) {
							var tooltipTriggerList = [].slice.call( parentObj.querySelectorAll( '[data-bs-tooltip]' ) );
							tooltipTriggerList.map( function( tooltipTriggerEl ) {
								return new bootstrap.Tooltip( tooltipTriggerEl )
							} );
						}
					}
				}
			);
			return false;
		} );
	} );

} ).apply( this, [window.theme, jQuery] );