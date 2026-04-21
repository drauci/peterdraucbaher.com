// Member Ajax on Page
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var activeMemberAjaxOnPage;

	$.extend( theme, {

		MemberAjaxPage: {

			defaults: {
				elements: '.page-members'
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

					if ( !$this.find( '#memberAjaxBox' ).get( 0 ) )
						return;

					var $container = $( this ),
						memberAjaxOnPage = {

							$wrapper: $container,
							pages: [],
							currentPage: 0,
							total: 0,
							$ajaxBox: $this.find( '#memberAjaxBox' ),
							$ajaxBoxContent: $this.find( '#memberAjaxBoxContent' ),

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
									self.show( self.pages.indexOf( href ) );
									return false;
								} );

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

								activeMemberAjaxOnPage = null;

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
											$member = $response.find( '#content article.member' ),
											$vc_css = $response.filter( 'style[data-type]:not("")' ),
											vc_css = '';

										$vc_css.each( function() {
											vc_css += $( this ).text();
										} );

										if ( $( '#memberAjaxCSS' ).get( 0 ) ) {
											$( '#memberAjaxCSS' ).text( vc_css );
										} else {
											$( '<style id="memberAjaxCSS">' + vc_css + '</style>' ).appendTo( "head" )
										}

										var $append = self.$ajaxBox.find( '.ajax-content-append' ), html = '';
										if ( $append.length ) html = $append.html();
										self.$ajaxBoxContent.html( $member.html() ).prepend( '<div class="row"><div class="col-lg-12"><hr class="tall m-t-none"></div></div>' ).append( '<div class="row"><div class="col-md-12"><hr class="m-t-md"></div></div>' + html );

										self.$ajaxBox.removeClass( 'ajax-box-loading' );
										$( window ).trigger( 'resize' );
										porto_init();
										theme.refreshVCContent( self.$ajaxBoxContent );
										activeMemberAjaxOnPage = self;
									}
								} );
							}
						};

					memberAjaxOnPage.build();

					$this.data( 'memberAjaxOnPage', memberAjaxOnPage );
				} );

				return self;
			}
		}

	} );

	// Key Press
	$( document.documentElement ).on( 'keyup', function( e ) {
		try {
			if ( !activeMemberAjaxOnPage ) return;
			// Next
			if ( e.keyCode == 39 ) {
				activeMemberAjaxOnPage.next();
			}
			// Prev
			if ( e.keyCode == 37 ) {
				activeMemberAjaxOnPage.prev();
			}
		} catch ( err ) { }
	} );

} ).apply( this, [window.theme, jQuery] );


jQuery( document ).ready( function( $ ) {
	'use strict';
    // Member Ajax on Page
	if ( typeof theme.MemberAjaxPage !== 'undefined' ) {
		theme.MemberAjaxPage.initialize();
	}
});
