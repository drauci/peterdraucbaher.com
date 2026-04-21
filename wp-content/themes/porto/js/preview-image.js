// Preview Image
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__previewImage';

	var PreviewImage = function( $el, opts ) {
		return this.initialize( $el, opts );
	};

	PreviewImage.defaults = {

	};

	PreviewImage.prototype = {
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
			this.options = $.extend( true, {}, PreviewImage.defaults, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function() {
			var $el = this.options.wrapper,
				image = $el.data( 'image' );

			if ( image ) {
				$el.css( 'background-image', 'url(' + image + ')' );
			}

			return this;
		}
	};

	// expose to scope
	$.extend( theme, {
		PreviewImage: PreviewImage
	} );

	// jquery plugin
	$.fn.themePreviewImage = function( opts ) {
		return this.map( function() {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this.data( instanceName );
			} else {
				return new theme.PreviewImage( $this, opts );
			}

		} );
	}

	if ( theme.isReady ) { // Finish init
		// Preview Image
		$( function() {
			$(document.body).find( '.thumb-info-preview .thumb-info-image:not(.manual)' ).each( function() {
				var $this = $( this ),
					opts;

				var pluginOptions = $this.data( 'plugin-options' );
				if ( pluginOptions )
					opts = pluginOptions;

				$this.themePreviewImage( opts );
			} );
		} );
	}
    $( document.body ).on( 'porto_init', function( e, $wrap ) {
		// Preview Image
        $( function() {
            $wrap.find( '.thumb-info-preview .thumb-info-image:not(.manual)' ).each( function() {
                var $this = $( this ),
                    opts;

                var pluginOptions = $this.data( 'plugin-options' );
                if ( pluginOptions )
                    opts = pluginOptions;

                $this.themePreviewImage( opts );
            } );
        } );
    } );

} ).apply( this, [window.theme, jQuery] );
