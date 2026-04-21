( function( $, theme ) {
    if ( theme.isReady ) { // Finish init
        if ( $.fn.vcwaypoint ) {
            // Progress bar tooltip
            $( '.vc_progress_bar' ).each( function() {
                var $this = $( this );
                if ( !$this.find( '.progress-bar-tooltip' ).length ) {
                    return;
                }
                $this.vcwaypoint( function() {
                    var $tooltips = $this.find( '.progress-bar-tooltip' ),
                        index = 0,
                        count = $tooltips.length;
                    function loop() {
                        theme.requestTimeout( function() {
                            $tooltips.animate( {
                                opacity: 1
                            } );
                        }, 200 );
                        index++;
                        if ( index < count ) {
                            loop();
                        }
                    }
                    loop();
                }, {
                    offset: '85%'
                } );
            } );
        }
    }
    $( document.body ).on( 'porto_init', function( e, $wrap ) {
        if ( $.fn.vcwaypoint ) {
            // Progress bar tooltip
            $wrap.find( '.vc_progress_bar' ).each( function() {
                var $this = $( this );
                if ( !$this.find( '.progress-bar-tooltip' ).length ) {
                    return;
                }
                $this.vcwaypoint( function() {
                    var $tooltips = $this.find( '.progress-bar-tooltip' ),
                        index = 0,
                        count = $tooltips.length;
                    function loop() {
                        theme.requestTimeout( function() {
                            $tooltips.animate( {
                                opacity: 1
                            } );
                        }, 200 );
                        index++;
                        if ( index < count ) {
                            loop();
                        }
                    }
                    loop();
                }, {
                    offset: '85%'
                } );
            } );
        }
    } );
} )( window.jQuery, window.theme )