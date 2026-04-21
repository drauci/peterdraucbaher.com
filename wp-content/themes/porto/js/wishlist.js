( function( theme, $ ) {
    // init wishlist off-canvas
    if ( $( '.wishlist-popup' ).length ) {
        var worker = null;

        $( '.wishlist-offcanvas .my-wishlist' ).on( 'click', function( e ) {
            e.preventDefault();
            var $this = $(this);
            $this.parent().toggleClass( 'minicart-opened' );
            if ( $this.parent().hasClass( 'minicart-opened' ) ) {
                $( 'html' ).css( theme.rtl_browser ? 'margin-left' : 'margin-right', theme.getScrollbarWidth() );
                $( 'html' ).css( 'overflow', 'hidden' );
            } else {
                $( 'html' ).css( 'overflow', '' );
                $( 'html' ).css( theme.rtl_browser ? 'margin-left' : 'margin-right', '' );
            }
        } );

        $( '.wishlist-offcanvas .minicart-overlay' ).on( 'click', function() {
            $( this ).closest( '.wishlist-offcanvas' ).removeClass( 'minicart-opened' );
            $( 'html' ).css( 'overflow', '' );
            $( 'html' ).css( theme.rtl_browser ? 'margin-left' : 'margin-right', '' );
        } );

        var init_wishlist = function() {
            worker = new Worker( js_porto_vars.ajax_loader_url.replace( '/images/ajax-loader@2x.gif', '/js/woocommerce-worker.js' ) );
            worker.onmessage = function( e ) {
                $( '.wishlist-popup' ).html( e.data );
                $( '.wishlist-count' ).text( $( '.wishlist-popup' ).find( '.wishlist-item' ).length );
            };
            worker.postMessage( { initWishlist: true, ajaxurl: theme.ajax_url, nonce: js_porto_vars.porto_nonce } );
        };

        if ( theme && theme.isLoaded ) {
            setTimeout( function() {
                init_wishlist();
            }, 100 );
        } else {
            $( window ).on( 'load', function() {
                init_wishlist();
            } );
        }

        // remove from wishlist
        $( '.wishlist-popup' ).on( 'click', '.remove_from_wishlist', function( e ) {
            e.preventDefault();

            var $this = $( this ),
                id = $this.attr( 'data-product_id' ),
                $table = $( '.wishlist_table #yith-wcwl-row-' + id + ' .remove_from_wishlist' );

            $this.closest( '.wishlist-item' ).find( '.ajax-loading' ).show();

            if ( $table.length ) {
                $table.trigger( 'click' );
            } else {
                if ( typeof yith_wcwl_l10n !== 'undefined' ) {
                    $.ajax( {
                        url: yith_wcwl_l10n.ajax_url,
                        data: {
                            action: yith_wcwl_l10n.actions.remove_from_wishlist_action,
                            remove_from_wishlist: id,
                            nonce: typeof yith_wcwl_l10n.nonce !== 'undefined' ? yith_wcwl_l10n.nonce.remove_from_wishlist_nonce : '',
                            from: 'theme'
                        },
                        method: 'post',
                        success: function( data ) {
                            var $wcwlWrap = $( '.yith-wcwl-add-to-wishlist.add-to-wishlist-' + id );
                            if ( $wcwlWrap.length ) {
                                var fragmentOptions = $wcwlWrap.data( 'fragment-options' ),
                                    $link = $wcwlWrap.find( 'a' );
                                if ( $link.length ) {
                                    if ( fragmentOptions.in_default_wishlist ) {
                                        delete fragmentOptions.in_default_wishlist;
                                        $wcwlWrap.attr( JSON.stringify( fragmentOptions ) );
                                    }
                                    $wcwlWrap.removeClass( 'exists' );
                                    $wcwlWrap.find( '.yith-wcwl-wishlistexistsbrowse' ).addClass( 'yith-wcwl-add-button' ).removeClass( 'yith-wcwl-wishlistexistsbrowse' );
                                    $wcwlWrap.find( '.yith-wcwl-wishlistaddedbrowse' ).addClass( 'yith-wcwl-add-button' ).removeClass( 'yith-wcwl-wishlistaddedbrowse' );
                                    $link.attr( 'href', location.href + ( -1 === location.href.indexOf( '?' ) ? '?' : '&' ) + 'post_type=product&amp;add_to_wishlist=' + id ).attr( 'data-product-id', id ).attr( 'data-product-type', fragmentOptions.product_type );
                                    var text = $( '.single_add_to_wishlist' ).data( 'title' );
                                    if ( !text ) {
                                        text = 'Add to wishlist';
                                    }
                                    $link.attr( 'title', text ).attr( 'data-title', text ).addClass( 'add_to_wishlist single_add_to_wishlist' ).html( '<span>' + text + '</span>' );
                                }
                            }
                            $( document.body ).trigger( 'removed_from_wishlist' );
                            //$this.closest('.wishlist-item').remove();
                        }
                    } );
                }
            }
        } );

        $( document.body ).on( 'added_to_wishlist removed_from_wishlist', function( e ) {
            if ( worker ) {
                worker.postMessage( { loadWishlist: true, ajaxurl: theme.ajax_url, nonce: js_porto_vars.porto_nonce } );
            }
        } );
    }
} ).apply( this, [window.theme, jQuery] );
