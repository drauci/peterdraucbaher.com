// Woocommerce Widget Toggle
( function( theme, $ ) {

    theme = theme || {};

    var instanceName = '__wooWidgetToggle';

    var WooWidgetToggle = function( $el, opts ) {
        return this.initialize( $el, opts );
    };

    WooWidgetToggle.defaults = {

    };

    WooWidgetToggle.prototype = {
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
            this.options = $.extend( true, {}, WooWidgetToggle.defaults, opts, {
                wrapper: this.$el
            } );

            return this;
        },

        build: function() {
            var $el = this.options.wrapper;

            $el.parent().removeClass( 'closed' );
            if ( !$el.find( '.toggle' ).length ) {
                $el.append( '<span class="toggle"></span>' );
            }
            $el.find( '.toggle' ).on( 'click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if ( $el.next().is( ":visible" ) ) {
                    $el.parent().addClass( 'closed' );
                } else {
                    $el.parent().removeClass( 'closed' );
                }
                $el.next().stop().slideToggle( 200 );
                theme.refreshVCContent();
                return false;
            } );

            return this;
        }
    };

    // expose to scope
    $.extend( theme, {
        WooWidgetToggle: WooWidgetToggle
    } );

    // jquery plugin
    $.fn.themeWooWidgetToggle = function( opts ) {
        return this.map( function() {
            var $this = $( this );

            if ( $this.data( instanceName ) ) {
                return $this.data( instanceName );
            } else {
                return new theme.WooWidgetToggle( $this, opts );
            }

        } );
    }

} ).apply( this, [window.theme, jQuery] );


// Woocommerce Widget Accordion
( function( theme, $ ) {

    theme = theme || {};

    var instanceName = '__wooWidgetAccordion';

    var WooWidgetAccordion = function( $el, opts ) {
        return this.initialize( $el, opts );
    };

    WooWidgetAccordion.defaults = {

    };

    WooWidgetAccordion.prototype = {
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
            this.options = $.extend( true, {}, WooWidgetAccordion.defaults, opts, {
                wrapper: this.$el
            } );

            return this;
        },

        build: function() {
            var self = this,
                $el = this.options.wrapper;

            $el.find( 'ul.children' ).each( function() {
                var $this = $( this );
                if ( !$this.prev().hasClass( 'toggle' ) ) {
                    $this.before(
                        $( '<span class="toggle"></span>' ).on( 'click', function() {
                            var $that = $( this );
                            if ( $that.next().is( ":visible" ) ) {
                                $that.parent().removeClass( 'open' ).addClass( 'closed' );
                            } else {
                                $that.parent().addClass( 'open' ).removeClass( 'closed' );
                            }
                            $that.next().stop().slideToggle( 200 );
                            theme.refreshVCContent();
                        } )
                    );
                }
            } );
            $el.find( 'li[class*="current-"]' ).addClass( 'current' );

            return this;
        }
    };

    // expose to scope
    $.extend( theme, {
        WooWidgetAccordion: WooWidgetAccordion
    } );

    // jquery plugin
    $.fn.themeWooWidgetAccordion = function( opts ) {
        return this.map( function() {
            var $this = $( this );

            if ( $this.data( instanceName ) ) {
                return $this.data( instanceName );
            } else {
                return new theme.WooWidgetAccordion( $this, opts );
            }

        } );
    }

} ).apply( this, [window.theme, jQuery] );