// Portfolio Ajax on Page
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var activePortfolioAjaxOnPage;

	$.extend( theme, {

		PortfolioAjaxPage: {

			defaults: {
				elements: '.page-portfolios'
			},

			initialize: function( $elements ) {
				this.$elements = ( $elements || $( this.defaults.elements ) );

				this.build();

				return this;
			},

			build: function() {
				var self = this;

				self.$elements.each( function() {

					var $this = $( this );

					if ( !$this.find( '#portfolioAjaxBox' ).get( 0 ) ) {
						return;
					}
					if ( $this.data( 'portfolioAjaxOnPage' ) ) {
						return;
					}

					var $container = $( this ),
						portfolioAjaxOnPage = {

							$wrapper: $container,
							pages: [],
							currentPage: 0,
							total: 0,
							$ajaxBox: $this.find( '#portfolioAjaxBox' ),
							$ajaxBoxContent: $this.find( '#portfolioAjaxBoxContent' ),

							build: function() {
								var self = this;

								self.pages = [];
								self.total = 0;

								$this.find( 'a[data-ajax-on-page]' ).each( function() {
									self.add( $( this ) );
								} );

								$this.off( 'mousedown', 'a[data-ajax-on-page]' ).on( 'mousedown', 'a[data-ajax-on-page]', function( ev ) {
									if ( ev.which == 2 ) {
										ev.preventDefault();
										return false;
									}
								} );
							},

							add: function( $el ) {

								var self = this,
									href = $el.attr( 'href' );

								self.pages.push( href );
								self.total++;

								$el.off( 'click' ).on( 'click', function( e ) {
									e.preventDefault();
									/* D3-Start */
									var _class = e.target.className
									if ( _class == 'owl-next' ) {
										return false;
									} else if ( _class == 'owl-prev' ) {
										return false;
									} else {
										self.show( self.pages.indexOf( href ) );
									}
									/* End-D3 */
									return false;
								} );

							},

							events: function() {
								var self = this;

								// Close
								$this.off( 'click', 'a[data-ajax-portfolio-close]' ).on( 'click', 'a[data-ajax-portfolio-close]', function( e ) {
									e.preventDefault();
									self.close();
									return false;
								} );

								if ( self.total <= 1 ) {
									$( 'a[data-ajax-portfolio-prev], a[data-ajax-portfolio-next]' ).remove();
								} else {
									// Prev
									$this.off( 'click', 'a[data-ajax-portfolio-prev]' ).on( 'click', 'a[data-ajax-portfolio-prev]', function( e ) {
										e.preventDefault();
										self.prev();
										return false;
									} );
									// Next
									$this.off( 'click', 'a[data-ajax-portfolio-next]' ).on( 'click', 'a[data-ajax-portfolio-next]', function( e ) {
										e.preventDefault();
										self.next();
										return false;
									} );
								}
							},

							close: function() {
								var self = this;

								if ( self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).get( 0 ) ) {
									try { self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).revkill(); } catch ( err ) { }
								}
								self.$ajaxBoxContent.empty();
								self.$ajaxBox.removeClass( 'ajax-box-init' ).removeClass( 'ajax-box-loading' );
							},

							next: function() {
								var self = this;
								if ( self.currentPage + 1 < self.total ) {
									self.show( self.currentPage + 1 );
								} else {
									self.show( 0 );
								}
							},

							prev: function() {
								var self = this;

								if ( ( self.currentPage - 1 ) >= 0 ) {
									self.show( self.currentPage - 1 );
								} else {
									self.show( self.total - 1 );
								}
							},

							show: function( i ) {
								var self = this;

								activePortfolioAjaxOnPage = null;

								if ( self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).get( 0 ) ) {
									try { self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).revkill(); } catch ( err ) { }
								}
								self.$ajaxBoxContent.empty();
								self.$ajaxBox.removeClass( 'ajax-box-init' ).addClass( 'ajax-box-loading' );

								theme.scrolltoContainer( self.$ajaxBox );

								self.currentPage = i;

								if ( i < 0 || i > ( self.total - 1 ) ) {
									self.close();
									return false;
								}

								// Ajax
								$.ajax( {
									url: self.pages[i],
									complete: function( data ) {
										var $response = $( data.responseText ),
											$portfolio = $response.find( '#content article.portfolio' ),
											$vc_css = $response.filter( 'style[data-type]:not("")' ),
											vc_css = '';

										if ( $( '#portfolioAjaxCSS' ).get( 0 ) ) {
											$( '#portfolioAjaxCSS' ).text( vc_css );
										} else {
											$( '<style id="portfolioAjaxCSS">' + vc_css + '</style>' ).appendTo( "head" )
										}

										$portfolio.find( '.portfolio-nav-all' ).html( '<a href="#" data-ajax-portfolio-close data-bs-tooltip data-original-title="' + js_porto_vars.popup_close + '"><i class="fas fa-th"></i></a>' );
										$portfolio.find( '.portfolio-nav' ).html( '<a href="#" data-ajax-portfolio-prev class="portfolio-nav-prev" data-bs-tooltip data-original-title="' + js_porto_vars.popup_prev + '"><i class="fa"></i></a><a href="#" data-toggle="tooltip" data-ajax-portfolio-next class="portfolio-nav-next" data-bs-tooltip data-original-title="' + js_porto_vars.popup_next + '"><i class="fa"></i></a>' );
										self.$ajaxBoxContent.html( $portfolio.html() ).append( '<div class="row"><div class="col-lg-12"><hr class="tall"></div></div>' );
										self.$ajaxBox.removeClass( 'ajax-box-loading' );
										$( window ).trigger( 'resize' );
										porto_init();
										theme.refreshVCContent( self.$ajaxBoxContent );
										self.events();
										activePortfolioAjaxOnPage = self;

										self.$ajaxBoxContent.find( '.lightbox:not(.manual)' ).each( function() {
											var $this = $( this ),
												opts;

											var pluginOptions = $this.data( 'plugin-options' );
											if ( pluginOptions )
												opts = pluginOptions;

											$this.themeLightbox( opts );
										} );
									}
								} );
							}
						};

					portfolioAjaxOnPage.build();

					$this.data( 'portfolioAjaxOnPage', portfolioAjaxOnPage );
				} );

				return self;
			}
		}

	} );

	// Key Press
	$( document.documentElement ).on( 'keyup', function( e ) {
		try {
			if ( !activePortfolioAjaxOnPage ) return;
			// Next
			if ( e.keyCode == 39 ) {
				activePortfolioAjaxOnPage.next();
			}
			// Prev
			if ( e.keyCode == 37 ) {
				activePortfolioAjaxOnPage.prev();
			}
		} catch ( err ) { }
	} );

} ).apply( this, [window.theme, jQuery] );


jQuery( document ).ready( function( $ ) {
	'use strict';
    // Portfolio Ajax on Page
	if ( typeof theme.PortfolioAjaxPage !== 'undefined' ) {
		theme.PortfolioAjaxPage.initialize();
	}
});