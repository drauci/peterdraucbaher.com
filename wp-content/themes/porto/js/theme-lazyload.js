
// Lazy Load
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__lazyload';

	var PluginLazyLoad = function( $el, opts ) {
		return this.initialize( $el, opts );
	};

	PluginLazyLoad.defaults = {
		effect: 'show',
		appearEffect: '',
		appear: function( elements_left, settings ) {

		},
		load: function( elements_left, settings ) {
			$( this ).addClass( 'lazy-load-loaded' );
		}
	};

	PluginLazyLoad.prototype = {
		initialize: function( $el, opts ) {
			if ( !$el.length ) {
				return this;
			}

			if ( !$.fn.lazyload ) {
				return this;
			}

			var options = $.extend( true, {}, PluginLazyLoad.defaults, opts, {} );
			return lazyload( $el, options );
		}
	};

	// expose to scope
	$.extend( theme, {
		PluginLazyLoad: PluginLazyLoad
	} );

	// jquery plugin
	$.fn.themePluginLazyLoad = function( opts ) {
		var $this = $( this );
		if ( $this.data( instanceName ) ) {
			return this;
		} else {
			var ins = new PluginLazyLoad( $.makeArray( this ), opts );
			$this.data( instanceName, ins );
		}
		return this;
	}

    var funcLazy = function( $wrap ) {
        // Lazy Load
        if ( $.fn.themePluginLazyLoad ) {

            $( function() {
                $wrap.find( '[data-plugin-lazyload]:not(.manual)' ).each( function() {
                    var $this = $( this ),
                        opts;

                    var pluginOptions = $this.data( 'plugin-options' );
                    if ( pluginOptions )
                        opts = pluginOptions;
                    $this.themePluginLazyLoad( opts );
                } );

                if ( $wrap.find( '.porto-lazyload' ).length ) {
                    $wrap.find( '.porto-lazyload' ).filter( function() {
                        if ( $( this ).data( '__lazyload' ) || ( $( this ).closest( '.owl-carousel' ).length && $( this ).closest( '.owl-carousel' ).find( '.owl-item.cloned' ).length ) ) {
                            return false;
                        }
                        return true;
                    } ).themePluginLazyLoad( { effect: 'fadeIn', effect_speed: 400 } );
                    if ( $wrap.find( '.porto-lazyload' ).closest( '.nivoSlider' ).length ) {
                        setTimeout( function() {
                            $wrap.find( '.nivoSlider' ).each( function() {
                                if ( $( this ).find( '.porto-lazyload' ).length ) {
                                    $( this ).closest( '.nivoSlider' ).find( '.nivo-main-image' ).attr( 'src', $( this ).closest( '.nivoSlider' ).find( '.porto-lazyload' ).eq( 0 ).attr( 'src' ) );
                                }
                            } );
                        }, 100 );
                    }
                    if ( $wrap.find( '.porto-lazyload' ).closest( '.porto-carousel-wrapper' ).length ) {
                        setTimeout( function() {
                            $wrap.find( '.porto-carousel-wrapper' ).each( function() {
                                if ( $( this ).find( '.porto-lazyload:not(.lazy-load-loaded)' ).length ) {
                                    $( this ).find( '.slick-list' ).css( 'height', 'auto' );
                                    //$( this ).find( '.porto-lazyload:not(.lazy-load-loaded)' ).trigger( 'appear' );
                                }
                            } );
                        }, 100 );
                    }
                }
            }  );

        }
    }
    // if ( theme.isReady ) {
        funcLazy( $( document.body ) );
    // }
    $( document.body ).on( 'porto_init', function( e, $wrap, initial ) { // if initial is true, not execute
        if ( ! initial ) {
            funcLazy( $wrap );
        }
    } );

} ).apply( this, [window.theme, jQuery] );

