// Sort Filter
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {

		SortFilter: {

			defaults: {
				filters: '.porto-sort-filters ul',
				elements: '.porto-sort-container .row'
			},

			initialize: function( $elements, $filters ) {
				this.$elements = ( $elements || $( this.defaults.elements ) );
				this.$filters = ( $filters || $( this.defaults.filters ) );

				this.build();

				return this;
			},

			build: function() {
				var self = this;

				self.$elements.each( function() {
					var $this = $( this );
					$this.isotope( {
						itemSelector: '.porto-sort-item',
						layoutMode: 'masonry',
						getSortData: {
							popular: '[data-popular] parseInt'
						},
						sortBy: 'popular',
						isOriginLeft: !theme.rtl
					} );
					/*$this.isotope( 'on', 'layoutComplete', function () {
						$this.find( '.porto-lazyload:not(.lazy-load-loaded)' ).trigger( 'appear' );
					} );*/
					imagesLoaded( this, function() {
						if ( $this.data( 'isotope' ) ) {
							$this.isotope( 'layout' );
						}
					} );
				} );

				self.$filters.each( function() {
					var $this = $( this );
					var id = $this.attr( 'data-sort-id' );
					var $container = $( '#' + id );
					if ( $container.length ) {
						$this.on( 'click', 'li', function( e ) {
							e.preventDefault();

							var $that = $( this );
							$this.find( 'li' ).removeClass( 'active' );
							$that.addClass( "active" );

							var sortByValue = $that.attr( 'data-sort-by' );
							$container.isotope( { sortBy: sortByValue } );

							var filterByValue = $that.attr( 'data-filter-by' );
							if ( filterByValue ) {
								$container.isotope( { filter: filterByValue } );
							} else {
								$container.isotope( { filter: '.porto-sort-item' } );
							}
							theme.refreshVCContent();
						} );

						$this.find( 'li[data-active]' ).trigger( 'click' );
					}
				} );

				return self;
			}
		}

	} );

    $( window ).on( 'load', function() {
        // Sort Filter
		if ( typeof theme.SortFilter !== 'undefined' ) {
			theme.SortFilter.initialize();
		}
    } );
} ).apply( this, [window.theme, jQuery] );