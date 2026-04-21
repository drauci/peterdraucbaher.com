// Chart Circular
( function( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__chartCircular';

	var ChartCircular = function( $el, opts ) {
		return this.initialize( $el, opts );
	};

	ChartCircular.defaults = {
		accX: 0,
		accY: -150,
		delay: 1,
		barColor: '#0088CC',
		trackColor: '#f2f2f2',
		scaleColor: false,
		scaleLength: 5,
		lineCap: 'round',
		lineWidth: 13,
		size: 175,
		rotate: 0,
		animate: ( {
			duration: 2500,
			enabled: true
		} )
	};

	ChartCircular.prototype = {
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
			this.options = $.extend( true, {}, ChartCircular.defaults, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function() {
			if ( !$.fn.easyPieChart ) {
				return this;
			}

			var self = this,
				$el = this.options.wrapper,
				value = this.options.percentValue ? parseInt( this.options.percentValue ) : parseInt( $el.attr( 'data-percent' ), 10 ),
				percentEl = $el.find( '.percent' );

			if ( !value ) value = 1;
			var labelValue = this.options.labelValue ? parseInt( this.options.labelValue, 10 ) : value;

			$.extend( true, self.options, {
				onStep: function( from, to, currentValue ) {
					percentEl.html( parseInt( labelValue * currentValue / value ) );
				}
			} );

			$el.attr( 'data-percent', 0 ).easyPieChart( self.options );

			var handler;
			if ( Number( self.options.delay ) <= 1000 / 60 ) {
				handler = theme.requestFrame;
			} else {
				handler = theme.requestTimeout;
			}

			handler( function() {
				if ( $el.data( 'easyPieChart' ) ) {
					$el.data( 'easyPieChart' ).update( value );
					$el.attr( 'data-percent', value );
				}
			}, self.options.delay );

			return this;
		}
	};

	// expose to scope
	$.extend( theme, {
		ChartCircular: ChartCircular
	} );

	// jquery plugin
	$.fn.themeChartCircular = function( opts ) {
		return this.map( function() {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this.data( instanceName );
			} else {
				return new theme.ChartCircular( $this, opts );
			}

		} );
	};

	if ( theme.isReady ) { // Finish init
		// Chart.Circular		
		var $objects = $(document.body).get(0).querySelectorAll( '[data-plugin-chart-circular]:not(.manual), .circular-bar-chart:not(.manual)' );
		if ( $objects.length ) {
			theme.dynIntObsInit( $objects, 'themeChartCircular', theme.ChartCircular.defaults );
		}
	}
	$( document.body ).on( 'porto_init', function( e, wrapObj ) {
		// Chart.Circular		
		var $objects = wrapObj.get(0).querySelectorAll( '[data-plugin-chart-circular]:not(.manual), .circular-bar-chart:not(.manual)' );
		if ( $objects.length ) {
			theme.dynIntObsInit( $objects, 'themeChartCircular', theme.ChartCircular.defaults );
		}
	});

} ).apply( this, [window.theme, jQuery] );