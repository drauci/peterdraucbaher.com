// reviews
( function( $ ) {
	var $review_content, $review_title1, $review_title2;
	$( window ).on( 'load', function() {
		var goReviewTab = function( targetSelector ) {
			var target = $( targetSelector );
			if ( ! target.length ) {
				return;
			}
			var recalc_pos = false;
			if ( $review_content.length && $review_content.is(':hidden') ) {
				recalc_pos = true;
				if ( $review_title1.length && $review_title1.is( ':visible' ) ) {
					$review_title1.click();
				} else if ( $review_title2.length && $review_title2.is( ':visible' ) ) {
					$review_title2.click();
				}
			}
			if ( '#review_form' == targetSelector && target.is( ':hidden' ) ) {
				if ( $( '.cr-ajax-reviews-add-review' ).length ) {
					$( '.cr-ajax-reviews-add-review' ).trigger( 'click' );
				} else {
					target = $( '#reviews' );
				}
			}

			var delay = recalc_pos ? 400 : 0;
			setTimeout(function() {
				if ( target.is(':visible') ) {
					$('html, body').stop().animate({
						scrollTop: target.offset().top - theme.StickyHeader.sticky_height - theme.adminBarHeight() - 14
					}, 600, 'easeOutQuad');
				}
			}, delay);
		};

		$review_content = $('#tab-reviews');
		$review_title1 = $review_content.prev('.resp-accordion');
		$review_title2 = $('#tab-title-reviews');

		$( document.body ).on( 'click', '.woocommerce-review-link, .woocommerce-write-review-link', function(e) {
			if ( $(this).closest( '.quickview-wrap' ).length ) {
				return true;
			}
			goReviewTab( this.hash );
			e.preventDefault();
		} );
		// Open review form if accessed via anchor
		if ( window.location.hash == '#review_form' || window.location.hash == '#reviews' || window.location.hash.indexOf('#comment-') != -1 ) {
			goReviewTab( window.location.hash );
		}
	} );

	$( '.skeleton-loading' ).on( 'skeleton-loaded', function() {
		$review_content = $('#tab-reviews');
		$review_title1 = $review_content.prev('.resp-accordion');
		$review_title2 = $('#tab-title-reviews');
	} );
} )( window.jQuery );
