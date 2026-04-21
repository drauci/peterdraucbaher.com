// Parallax
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__parallax';

	var Parallax = function( $el, opts ) {
		return this.initialize( $el, opts );
	};

	Parallax.defaults = {
		speed: 1.5,
		horizontalPosition: '50%',
		offset: 0,
		scale: false,
		startOffset: 7,
		scaleInvert: false,
	};

	Parallax.prototype = {
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
			this.options = $.extend( true, {}, Parallax.defaults, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function() {
			var self = this,
				$window = $( window ),
				offset,
				yPos,
				bgpos,
				background;

			// Create Parallax Element
			background = $( '<div class="parallax-background"></div>' );

			// Set Style for Parallax Element
			var bg = self.options.wrapper.data( 'image-src' ) ? 'url(' + self.options.wrapper.data( 'image-src' ) + ')' : self.options.wrapper.css( 'background-image' );
			background.css( {
				'background-image': bg,
				'background-size': 'cover',
				'background-position': '50% 0%',
				'position': 'absolute',
				'top': 0,
				'left': 0,
				'width': '100%',
				'height': self.options.speed * 100 + '%'
			} );

			// Add Parallax Element on DOM
			self.options.wrapper.prepend( background );

			// Set Overlfow Hidden and Position Relative to Parallax Wrapper
			self.options.wrapper.css( {
				'position': 'relative',
				'overflow': 'hidden'
			} );

			if ( self.options.wrapper.attr( 'data-parallax-type' ) ) { // horizontal
				self.options.parallaxType = 'horizontal';
				background.css( {
					'background-position': '0% 50%',
					'width': self.options.speed * 100 + '%',
					'height': '100%',
				} );
			}

			// Scroll Scale
			if ( self.options.scale ) {
				background.wrap( '<div class="parallax-scale-wrapper"></div>' );
				background.css( {
					'transition': 'transform 500ms ease-out'
				} );
				this.scaleParallaxFunc = this.scaleParallax.bind( this );
				this.scaleParallaxFunc();
				window.addEventListener( 'scroll', this.scaleParallaxFunc );
				window.addEventListener( 'resize', this.scaleParallaxFunc );
			}
			// Parallax Effect on Scroll & Resize
			var parallaxEffectOnScrolResize = function() {
				var skrollr_size = 100 * self.options.speed,
					skrollr_start = -( skrollr_size - 100 );
				if ( !self.options.parallaxType ) {
					background.attr( "data-bottom-top", "top: " + skrollr_start + "%;" ).attr( "data-top-bottom", "top: 0%;" );
				} else {
					skrollr_start /= 9.8;
					background.attr( "data-bottom-top", "left: " + skrollr_start + "%;" ).attr( "data-top-bottom", "left: 0%;" );
				}
			}

			if ( !theme.is_device_mobile ) {
				parallaxEffectOnScrolResize();
			} else {
				if ( self.options.enableOnMobile == true ) {
					parallaxEffectOnScrolResize();
				} else {
					self.options.wrapper.addClass( 'parallax-disabled' );
				}
			}

			return this;
		},

		scaleParallax: function() {
			var self = this,
				$window = $( window ),
				scrollTop = $window.scrollTop(),
				$background = self.options.wrapper.find( '.parallax-background' ),
				elementOffset = self.options.wrapper.offset().top,
				currentElementOffset = ( elementOffset - scrollTop ),
				scrollPercent = Math.abs( +( currentElementOffset - $window.height() ) / ( self.options.startOffset ? self.options.startOffset : 7 ) );
			scrollPercent = parseInt( ( scrollPercent >= 100 ) ? 100 : scrollPercent );
			var currentScale = ( scrollPercent / 100 ) * 50;
			if ( !self.options.scaleInvert ) {
				$background.css( {
					'transform': 'scale(1.' + String( currentScale ).padStart( 2, '0' ) + ', 1.' + String( currentScale ).padStart( 2, '0' ) + ')'
				} );
			} else {
				$background.css( {
					'transform': 'scale(1.' + String( 50 - currentScale ).padStart( 2, '0' ) + ', 1.' + String( 50 - currentScale ).padStart( 2, '0' ) + ')'
				} );
			}
		},

		disable: function() {
			var self = this;
			if ( self.options.scale ) {
				window.removeEventListener( 'scroll', this.scaleParallaxFunc );
				window.removeEventListener( 'resize', this.scaleParallaxFunc );
			}
		}
	};

	// expose to scope
	$.extend( theme, {
		Parallax: Parallax
	} );

	// jquery plugin
	$.fn.themeParallax = function( opts ) {
		if ( typeof skrollr == 'undefined' ) {
			return this;
		}
		var obj = this.map( function() {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this.data( instanceName );
			} else {
				return new theme.Parallax( $this, opts );
			}

		} );
		if ( theme.portoSkrollr ) {
			theme.portoSkrollr.refresh();
		} else if ( !theme.is_device_mobile ) {
			theme.portoSkrollr = skrollr.init( { forceHeight: false, smoothScrolling: false, mobileCheck: function() { return theme.is_device_mobile } } );
		}
		return obj;
	}

} ).apply( this, [window.theme, jQuery] );


( function( $, theme ) {
    if ( theme.isReady ) { // Finish init
        // Parallax
		if ( $.fn.themeParallax ) {
			$( function() {
				$( '[data-plugin-parallax]:not(.manual)' ).each( function() {
					var $this = $( this ),
						opts;
					var pluginOptions = $this.data( 'plugin-options' ),
						parallaxScale = $this.attr( 'data-parallax-scale' );
					if ( pluginOptions )
						opts = pluginOptions;

					if ( typeof parallaxScale !== 'undefined' ) {
						opts['scale'] = true;
						if ( parallaxScale == 'invert' ) {
							opts['scaleInvert'] = true;
						}
					}
					$this.themeParallax( opts );
				} );
			} );
		}
    }
    $( document.body ).on( 'porto_init', function( e, $wrap ) {
        // Parallax
		if ( $.fn.themeParallax ) {
			$( function() {
				$wrap.find( '[data-plugin-parallax]:not(.manual)' ).each( function() {
					var $this = $( this ),
						opts;
					var pluginOptions = $this.data( 'plugin-options' ),
						parallaxScale = $this.attr( 'data-parallax-scale' );
					if ( pluginOptions )
						opts = pluginOptions;

					if ( typeof parallaxScale !== 'undefined' ) {
						opts['scale'] = true;
						if ( parallaxScale == 'invert' ) {
							opts['scaleInvert'] = true;
						}
					}
					$this.themeParallax( opts );
				} );
			} );
		}
    } );
} )( window.jQuery, window.theme )
