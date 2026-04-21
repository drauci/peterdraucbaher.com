( function( $ ) {
	window.theme = window.theme || {};
	if ( typeof window.theme.animation_support == 'undefined' ) {
		theme.animation_support = !window.jQuery( 'html' ).hasClass( 'no-csstransitions' );
		theme.getOptions = function( opts ) {
			if ( typeof ( opts ) == 'object' ) {
				return opts;
			} else if ( typeof ( opts ) == 'string' ) {
				try {
					return JSON.parse( opts.replace( /'/g, '"' ).replace( ';', '' ) );
				} catch ( e ) {
					return {};
				}
			} else {
				return {};
			}
		}
		theme.execPluginFunction = function( functionName, context ) {
			var args = Array.prototype.slice.call( arguments, 2 );
			var namespaces = functionName.split( "." );
			var func = namespaces.pop();

			for ( var i = 0; i < namespaces.length; i++ ) {
				context = context[namespaces[i]];
			}

			return context[func].apply( context, args );
		}
		theme.mergeOptions = function( obj1, obj2 ) {
			var obj3 = {};
			for ( var attrname in obj1 ) { obj3[attrname] = obj1[attrname]; }
			for ( var attrname in obj2 ) { obj3[attrname] = obj2[attrname]; }
			return obj3;
		}
		theme.dynIntObsInit = function( selector, functionName, pluginDefaults ) {
			var $el;
			if ( typeof selector == 'string' ) {
				$el = document.querySelectorAll( selector );
			} else {
				$el = selector;
			}

			Array.prototype.forEach.call( $el, function( obj ) {
				var $this = $( obj ),
					opts;
				if ( $this.data( 'observer-init' ) ) {
					return;
				}

				var pluginOptions = theme.getOptions( $this.data( 'plugin-options' ) );
				if ( pluginOptions )
					opts = pluginOptions;

				var mergedPluginDefaults = theme.mergeOptions( pluginDefaults, opts )

				var intersectionObserverOptions = {
					rootMargin: '0px 0px 200px 0px',
					thresholds: 0
				}
				if ( mergedPluginDefaults.accY ) {
					intersectionObserverOptions.rootMargin = '0px 0px ' + Number( mergedPluginDefaults.accY ) + 'px 0px';
				}

				var observer = new IntersectionObserver( function( entries ) {
					for ( var i = 0; i < entries.length; i++ ) {
						var entry = entries[i];
						if ( entry.intersectionRatio > 0 ) {
							theme.execPluginFunction( functionName, $this, mergedPluginDefaults );

							// Unobserve
							observer.unobserve( entry.target );
						}
					}
				}, intersectionObserverOptions );

				observer.observe( obj );
				$this.data( 'observer-init', true );
			} );
		}
	}
} )( window.jQuery );

// Animate
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__animate';

	var Animate = function( $el, opts ) {
		return this.initialize( $el, opts );
	};

	Animate.defaults = {
		accX: 0,
		accY: -120,
		delay: 1,
		duration: 1000
	};

	Animate.prototype = {
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
			this.$el.data( instanceName, true );

			return this;
		},

		setOptions: function( opts ) {
			this.options = $.extend( true, {}, Animate.defaults, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function() {
			var self = this,
				$el = this.options.wrapper,
				delay = 0,
				duration = 0;

			if ( $el.data( 'appear-animation-svg' ) ) {
				$el.find( '[data-appear-animation]' ).each( function() {
					var $this = $( this ),
						opts;

					var pluginOptions = theme.getOptions( $this.data( 'plugin-options' ) );
					if ( pluginOptions )
						opts = pluginOptions;

					$this.themeAnimate( opts );
				} );

				return this;
			}

			$el.addClass( 'appear-animation' );

			var el_obj = $el.get( 0 );

			delay = Math.abs( $el.data( 'appear-animation-delay' ) ? $el.data( 'appear-animation-delay' ) : self.options.delay );
			duration = Math.abs( $el.data( 'appear-animation-duration' ) ? $el.data( 'appear-animation-duration' ) : self.options.duration );
			if ( 'undefined' !== typeof $el.data( 'appear-animation' ) && $el.data( 'appear-animation' ).includes( 'revealDir' ) ) {
				if ( delay > 1 ) {
					el_obj.style.setProperty( '--porto-reveal-animation-delay', delay + 'ms' );
				}
				if ( duration != 1000 ) {
					el_obj.style.setProperty( '--porto-reveal-animation-duration', duration + 'ms' );
				}
				if ( $el.data( 'animation-reveal-clr' ) ) {
					el_obj.style.setProperty(  '--porto-reveal-clr', $el.data( 'animation-reveal-clr' ) );
				}
			} else {
				if ( delay > 1 ) {
					el_obj.style.animationDelay = delay + 'ms';
				}
				if ( duration != 1000 ) {
					el_obj.style.animationDuration = duration + 'ms';
				}				
			}

			/*if ( $el.find( '.porto-lazyload:not(.lazy-load-loaded)' ).length ) {
				$el.find( '.porto-lazyload:not(.lazy-load-loaded)' ).trigger( 'appear' );
			}*/
			$el.addClass( $el.data( 'appear-animation' ) + ' appear-animation-visible' );

			return this;
		}
	};

	// expose to scope
	$.extend( theme, {
		Animate: Animate
	} );

	// jquery plugin
	$.fn.themeAnimate = function( opts ) {
		return this.map( function() {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this;
			} else {
				return new theme.Animate( $this, opts );
			}

		} );
	};

} ).apply( this, [window.theme, jQuery] );


( function( theme, $ ) {
    theme = theme || {};
	
	var funcAnimate = function( $wrap, wrapObj ) {
        // Animate
		if ( $.fn.themeAnimate && typeof wrapObj != 'undefined' ) {

			$( function() {
				var svgAnimates = wrapObj.querySelectorAll( 'svg [data-appear-animation]' );
				if ( svgAnimates.length ) {
					$( svgAnimates ).closest( 'svg' ).attr( 'data-appear-animation-svg', '1' );
				}
				var $animates = wrapObj.querySelectorAll( '[data-plugin-animate], [data-appear-animation], [data-appear-animation-svg]' );
				if ( $animates.length ) {
					var animateResize = function() {
						if ( window.innerWidth < 768 ) {
							window.removeEventListener( 'resize', animateResize );
							$animates.forEach( function( o ) {
								o.classList.add( 'appear-animation-visible' );
							} );
						}
					};
					if ( theme.animation_support ) {
						window.addEventListener( 'resize', animateResize );
						theme.dynIntObsInit( $animates, 'themeAnimate', theme.Animate.defaults );
					} else {
						$animates.forEach( function( o ) {
							o.classList.add( 'appear-animation-visible' );
						} );
					}
				}
			} );
		} 
	}

	funcAnimate( '', document.body );
    $( document.body ).on( 'porto_after_async_init', function( e, $wrap, wrapObj ) {
		if ( theme.isAsyncInit != -1 ) { // not initial
			funcAnimate( $wrap, wrapObj );	
		}
    } );
} ).apply( this, [window.theme, jQuery] );