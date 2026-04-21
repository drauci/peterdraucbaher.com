// Fit Video
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__fitVideo';

	var FitVideo = function( $el, opts ) {
		return this.initialize( $el, opts );
	};

	FitVideo.defaults = {

	};

	FitVideo.prototype = {
		initialize: function( $el, opts ) {
			if ( $el.data( instanceName ) ) {
				return this;
			}

			this.$el = $el;

			this
				.setData()
				.setOptions( opts )
				.build();

			return this;
		},

		setData: function() {
			this.$el.data( instanceName, this );

			return this;
		},

		setOptions: function( opts ) {
			this.options = $.extend( true, {}, FitVideo.defaults, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function() {
			if ( !$.fn.fitVids ) {
				return this;
			}

			var $el = this.options.wrapper;

			$el.fitVids();
			$( window ).on( 'resize', function() {
				$el.fitVids();
			} );

			return this;
		}
	};

	// expose to scope
	$.extend( theme, {
		FitVideo: FitVideo
	} );

	// jquery plugin
	$.fn.themeFitVideo = function( opts ) {
		return this.map( function() {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this.data( instanceName );
			} else {
				return new theme.FitVideo( $this, opts );
			}

		} );
	};

} ).apply( this, [window.theme, jQuery] );


( function( $, theme ) {
    if ( theme.isReady ) { // Finish init
        // Fit Video
        if ( $.fn.themeFitVideo ) {

            $( function() {
                $( '.fit-video:not(.manual)' ).each( function() {
                    var $this = $( this ),
                        opts;

                    var pluginOptions = $this.data( 'plugin-options' );
                    if ( pluginOptions )
                        opts = pluginOptions;

                    $this.themeFitVideo( opts );
                } );
            } );

        }
    }
    $( document.body ).on( 'porto_init', function( e, $wrap ) {
        // Fit Video
        if ( $.fn.themeFitVideo ) {

            $( function() {
                $wrap.find( '.fit-video:not(.manual)' ).each( function() {
                    var $this = $( this ),
                        opts;

                    var pluginOptions = $this.data( 'plugin-options' );
                    if ( pluginOptions )
                        opts = pluginOptions;

                    $this.themeFitVideo( opts );
                } );
            } );

        }
    } );
} )( window.jQuery, window.theme )
