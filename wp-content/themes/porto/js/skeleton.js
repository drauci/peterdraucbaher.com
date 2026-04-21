jQuery( document ).ready( function( $ ) {
    // skeleton screens
    if ( js_porto_vars.use_skeleton_screen.length > 0 && $( '.skeleton-loading' ).length ) {
        var dclFired = false,
            dclPromise = ( function() {
                var deferred = $.Deferred();
                $( function() {
                    deferred.resolve();
                    dclFired = true;
                } );
                return deferred.promise();
            } )(),
            observer = false,
            NativeMutationObserver = window.MutationObserver || window.WebkitMutationObserver || window.MozMutationObserver;
        if ( typeof NativeMutationObserver != 'undefined' ) {
            observer = new NativeMutationObserver( function( mutationsList, observer ) {
                for ( var i in mutationsList ) {
                    var mutation = mutationsList[i];
                    if ( mutation.type == 'childList' ) {
                        $( mutation.target ).trigger( 'skeleton:initialised' );
                    }
                }
            } );
        }
        var killObserverTrigger = setTimeout( function() {
            if ( observer ) {
                observer.disconnect();
                observer = undefined;
            }
        }, 4000 );
        var skeletonTimer;
        $( '.skeleton-loading' ).each( function( e ) {
            var $this = $( this ),
                skeletonInitialisedPromise = ( function() {
                    var deferred = $.Deferred();
                    $this.on( 'skeleton:initialised', function( evt ) {
                        if ( evt.target.classList.contains( 'skeleton-loading' ) ) {
                            deferred.resolve( evt );
                        }
                    } );
                    return deferred.promise();
                } )(),
                resourcesLoadedPromise = ( function() {
                    return $.Deferred().resolve().promise();
                } )();
            $.when( skeletonInitialisedPromise, resourcesLoadedPromise, dclPromise ).done( function( e ) {
                var $real = $( e.target ),
                    $placeholder = $real.siblings( '.skeleton-body' );
                if ( !$placeholder.length ) {
                    $placeholder = $real.parent().parent().parent().find( '[class="' + $real.attr( 'class' ).replace( 'skeleton-loading', 'skeleton-body' ) + '"]' );
                }
                porto_init( $real );
                if ( $real.find( '.sidebar-menu:not(.side-menu-accordion)' ).length && typeof theme.SidebarMenu != 'undefined' ) {
                    theme.SidebarMenu.initialize( $real.find( '.sidebar-menu:not(.side-menu-accordion)' ) );
                }
                if ( skeletonTimer ) {
                    theme.deleteTimeout( skeletonTimer );
                }
                $real.trigger( 'skeleton-loaded' );
                theme.requestTimeout( function() {
                    if ( $placeholder.length ) {
                        // fix YITH Products Filters compatibility issue
                        if ( $placeholder.parent().hasClass( 'yit-wcan-container' ) ) {
                            $placeholder.parent().remove();
                        } else {
                            $placeholder.remove();
                        }
                    }
                    $real.removeClass( 'skeleton-loading' );
                    if ( $real.closest( '.skeleton-loading-wrap' ) ) {
                        $real.closest( '.skeleton-loading-wrap' ).removeClass( 'skeleton-loading-wrap' );
                    }
                    if ( $( document.body ).hasClass( 'elementor-default' ) || $( document.body ).hasClass( 'elementor-page' ) ) {
                        $( window ).trigger( 'resize' );
                    }
                    theme.refreshStickySidebar( false );
                }, 100 );
                if ( !$( '.skeleton-loading' ).length ) {
                    clearTimeout( killObserverTrigger );
                    observer.disconnect();
                    observer = undefined;
                }
            } );
            if ( $this.children( 'script[type="text/template"]' ).length ) {
                var content = $( JSON.parse( $this.children( 'script[type="text/template"]' ).eq( 0 ).html() ) );
                $this.children( 'script[type="text/template"]' ).eq( 0 ).remove();
                if ( observer ) {
                    observer.observe( this, { childList: true, subtree: false } );
                }
                $this.append( content );
                if ( !observer ) {
                    $this.trigger( 'skeleton:initialised' );
                }
            }
        } );
    }
});