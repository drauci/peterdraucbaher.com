// Post Ajax on Modal
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var $rev_sliders;

	$.extend( theme, {

		PostAjaxModal: {

			defaults: {
				elements: '.page-portfolios'
			},

			initialize: function( $elements, post_type ) {
				this.$elements = ( $elements || $( this.defaults.elements ) );
				if ( typeof post_type == 'undefined' ) {
					post_type = 'portfolio';
				}

				this.build( post_type );

				return this;
			},

			build: function( post_type ) {
				var parentobj = this,
					postAjaxOnModal = {

						$wrapper: null,
						modals: [],
						currentModal: 0,
						total: 0,
						p_type: 'portfolio',

						build: function( $this, p_type ) {
							var self = this;
							self.$wrapper = $this;
							if ( !self.$wrapper ) {
								return;
							}
							self.modals = [];
							self.total = 0;
							self.p_type = p_type;

							$this.find( 'a[data-ajax-on-modal]' ).each( function() {
								self.add( $( this ) );
							} );

							$this.off( 'mousedown', 'a[data-ajax-on-modal]' ).on( 'mousedown', 'a[data-ajax-on-modal]', function( ev ) {
								if ( ev.which == 2 ) {
									ev.preventDefault();
									return false;
								}
							} );
						},

						add: function( $el ) {

							var self = this,
								href = $el.attr( 'href' ),
								index = self.total;

							self.modals.push( { src: href } );
							self.total++;

							$el.off( 'click' ).on( 'click', function( e ) {
								e.preventDefault();
								self.show( index );
								return false;
							} );

						},

						next: function() {
							var self = this;
							if ( self.currentModal + 1 < self.total ) {
								self.show( self.currentModal + 1 );
							} else {
								self.show( 0 );
							}
						},

						prev: function() {
							var self = this;

							if ( ( self.currentModal - 1 ) >= 0 ) {
								self.show( self.currentModal - 1 );
							} else {
								self.show( self.total - 1 );
							}
						},

						show: function( i ) {
							var self = this;

							self.currentModal = i;

							if ( i < 0 || i > ( self.total - 1 ) ) {
								return false;
							}

							$.magnificPopup.close();
							$.magnificPopup.open( $.extend( true, {}, theme.mfpConfig, {
								type: 'ajax',
								items: self.modals,
								gallery: {
									enabled: true
								},
								ajax: {
									settings: {
										type: 'post',
										data: {
											ajax_action: self.p_type + '_ajax_modal'
										}
									}
								},
								mainClass: self.p_type + '-ajax-modal',
								fixedContentPos: true,
								callbacks: {
									parseAjax: function( mfpResponse ) {
										var $response = $( mfpResponse.data ),
											$post = $response.find( '#content article.' + self.p_type ),
											$vc_css = $response.filter( 'style[data-type]:not("")' ),
											vc_css = '';

										$vc_css.each( function() {
											vc_css += $( this ).text();
										} );

										if ( $( '#' + self.p_type + 'AjaxCSS' ).get( 0 ) ) {
											$( '#' + self.p_type + 'AjaxCSS' ).text( vc_css );
										} else {
											$( '<style id="' + self.p_type + 'AjaxCSS">' + vc_css + '</style>' ).appendTo( "head" )
										}

										$post.find( '.' + self.p_type + '-nav-all' ).html( '<a href="#" data-ajax-' + self.p_type + '-close data-bs-tooltip data-original-title="' + js_porto_vars.popup_close + '" data-bs-placement="bottom"><i class="fas fa-th"></i></a>' );
										$post.find( '.' + self.p_type + '-nav' ).html( '<a href="#" data-ajax-' + self.p_type + '-prev class="' + self.p_type + '-nav-prev" data-bs-tooltip data-original-title="' + js_porto_vars.popup_prev + '" data-bs-placement="bottom"><i class="fa"></i></a><a href="#" data-toggle="tooltip" data-ajax-' + self.p_type + '-next class="' + self.p_type + '-nav-next" data-bs-tooltip data-original-title="' + js_porto_vars.popup_next + '" data-bs-placement="bottom"><i class="fa"></i></a>' );
										$post.find( '.elementor-invisible' ).removeClass( 'elementor-invisible' );
										if ( $post.length == 0 ) {
											$post = $response.find( '.main-content>.porto-block' );
										}
										mfpResponse.data = '<div class="ajax-container">' + $post.html() + '</div>';
									},
									ajaxContentAdded: function() {
										// Wrapper
										var $wrapper = $( '.' + self.p_type + '-ajax-modal' );

										// Close
										$wrapper.find( 'a[data-ajax-' + self.p_type + '-close]' ).on( 'click', function( e ) {
											e.preventDefault();
											$.magnificPopup.close();
											return false;
										} );

										$rev_sliders = $wrapper.find( '.rev_slider, rs-module' );

										// Remove Next and Close
										if ( self.modals.length <= 1 ) {
											$wrapper.find( 'a[data-ajax-' + self.p_type + '-prev], a[data-ajax-' + self.p_type + '-next]' ).remove();
										} else {
											// Prev
											$wrapper.find( 'a[data-ajax-' + self.p_type + '-prev]' ).on( 'click', function( e ) {
												e.preventDefault();
												if ( $rev_sliders && $rev_sliders.get( 0 ) ) {
													try { $rev_sliders.revkill(); } catch ( err ) { }
												}
												$wrapper.find( '.mfp-arrow-left' ).trigger( 'click' );
												return false;
											} );
											// Next
											$wrapper.find( 'a[data-ajax-' + self.p_type + '-next]' ).on( 'click', function( e ) {
												e.preventDefault();
												if ( $rev_sliders && $rev_sliders.get( 0 ) ) {
													try { $rev_sliders.revkill(); } catch ( err ) { }
												}
												$wrapper.find( '.mfp-arrow-right' ).trigger( 'click' );
												return false;
											} );
										}
										if ( 'portfolio' == self.p_type ) {
											$( window ).trigger( 'resize' );
										}
										porto_init();
										theme.refreshVCContent( $wrapper );
										setTimeout( function() {
											var videos = $wrapper.find( 'video' );
											if ( videos.get( 0 ) ) {
												videos.each( function() {
													$( this )[0].play();
													$( this ).parent().parent().parent().find( '.video-controls' ).attr( 'data-action', 'play' );
													$( this ).parent().parent().parent().find( '.video-controls' ).html( '<i class="ult-vid-cntrlpause"></i>' );
												} );
											}
										}, 600 );
										$wrapper.off( 'scroll' ).on( 'scroll', function() {
											if ( $.fn.appear ) {
												$.fn.appear.run();
											}
										} );
									},
									change: function() {
										$( '.mfp-wrap .ajax-container' ).trigger( 'click' );
									},
									beforeClose: function() {
										if ( $rev_sliders && $rev_sliders.get( 0 ) ) {
											try { $rev_sliders.revkill(); } catch ( err ) { }
										}
										// Wrapper
										var $wrapper = $( '.' + self.p_type + '-ajax-modal' );
										$wrapper.off( 'scroll' );
									}
								}
							} ), i );
						}
					};

				parentobj.$elements.each( function() {

					var $this = $( this );

					if ( !$this.find( 'a[data-ajax-on-modal]' ).get( 0 ) ) {
						return;
					}
					if ( $this.data( post_type + 'AjaxOnModal' ) ) {
						return;
					}

					postAjaxOnModal.build( $this, post_type );

					$this.data( post_type + 'AjaxOnModal', postAjaxOnModal );
				} );

				return parentobj;
			}
		}

	} );

	// Key Press
	$( document.documentElement ).on( 'keydown', function( e ) {
		try {
			if ( e.keyCode == 37 || e.keyCode == 39 ) {
				if ( $rev_sliders && $rev_sliders.get( 0 ) ) {
					$rev_sliders.revkill();
				}
			}
		} catch ( err ) { }
	} );

} ).apply( this, [window.theme, jQuery] );

jQuery( document ).ready( function( $ ) {
	'use strict';
    	// Post Ajax Modal
	if ( typeof theme.PostAjaxModal !== 'undefined' ) {
		// Portfolio
		if ( $( '.page-portfolios' ).length ) {
			$( '.page-portfolios' ).each( function() {
				theme.PostAjaxModal.initialize( $( this ) );
			} );
		}
		// Member
		if ( $( '.page-members' ).length ) {
			$( '.page-members' ).each( function() {
				theme.PostAjaxModal.initialize( $( this ), 'member' );
			} );
		}
	}
});