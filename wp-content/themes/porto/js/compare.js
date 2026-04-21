( function( theme, $ ) {
    'use strict';
    theme = theme || {};

    $( document ).ready( function() {
        if ( typeof yith_woocompare == 'undefined' ) {
            return;
        }
        $( 'body' ).on( 'click', '.wishlist_table a.compare.added', function( e ) {
            e.preventDefault();
            $( 'body' ).trigger( 'yith_woocompare_open_popup', { response: theme.add_query_arg( 'action', yith_woocompare.actions ? yith_woocompare.actions.view : yith_woocompare.actionview ) + '&iframe=true' } );
        } );
        // Redefine "add to compare" function because they do not use async ajax.
        $( document )
            .off( 'click', 'a.compare:not(.added)' )
            .off( 'click', '.product a.compare:not(.added)' )
            .on( 'click', '.product a.compare:not(.added), .wishlist_table a.compare:not(.added)', function( e ) {
                e.preventDefault();
                if ( typeof theme.comparePopup != 'undefined' ) {
                    theme.comparePopup.initialize( $( this ) );
                }

                var button = $( this ),
                    data = {
                        action: yith_woocompare.actions ? yith_woocompare.actions.add : yith_woocompare.actionadd,
                        id: button.data( 'product_id' ),
                        context: 'frontend',
                        security: yith_woocompare.nonces ? yith_woocompare.nonces.add : yith_woocompare.add_nonce,
                    },
                    widget_list = $( '.yith-woocompare-widget ul.products-list' );

                button.addClass( 'added' );

                // do ajax
                $.ajax( {
                    type: 'post',
                    url: yith_woocompare.ajaxurl.toString().replace( '%%endpoint%%', yith_woocompare.actions ? yith_woocompare.actions.add : yith_woocompare.actionadd ),
                    data: data,
                    dataType: 'json',
                    success: function( response ) {

                        // increase compare count
                        $( '.yith-woocompare-open .compare-count' ).each( function() {
                            this.innerHTML = parseInt( this.innerHTML ) + 1;
                        } );
                        var added_icon_html = '';
                        if ( button.data( 'added_icon' ) ) {
                            added_icon_html += '<i class="' + button.data( 'added_icon' ) + '"></i>';
                        }
                        var added_label = yith_woocompare.added_label;
                        button.attr('data-bs-original-title', added_label);
                        if ( 'hide' == button.data( 'hide_title' ) ) {
                            added_label = '';
                        }
                        button.attr( 'href', response.table_url )
                            .html( !button.data( 'icon_pos' ) ? added_icon_html + added_label : added_label + added_icon_html );
                        // add the product in the widget
                        widget_list.html( response.widget_table );

                        if ( ( typeof yith_woocompare != 'undefined' ) && ( 'yes' == yith_woocompare.auto_open || '1' == yith_woocompare.auto_open ) ) {
                            button.trigger( 'click' );
                            /*response.added && $( document ).trigger('yith_woocompare_open_popup', {
                              response: response.table_url,
                              button: button
                            });*/
                        }

                        response.added && $( document ).trigger('yith_woocompare_product_added', {
                          productId: button.data( 'product_id' ),
                          $initiator: button
                        });
                    }
                } ).fail( function() {
                    button.removeClass( 'added' );
                } );
            } );
    } );

    if ( !js_porto_vars.compare_popup ) return;
    $.extend( theme, {
        comparePopup: {
            isCart: true,
            popupContainer: null,
            $el: null,
            initialize: function( $el ) {
                this.$el = $el;
                if ( $( '.after-loading-success-message.style-3' ).length == 0 ) {
                    $( 'body' ).append( '<div class="after-loading-success-message style-3 d-block"></div>' );
                    this.isCart = false;
                    this.popupContainer = $( '.after-loading-success-message.style-3' );
                }
                else {
                    this.popupContainer = $( '.after-loading-success-message.style-3' );
                    this.popupContainer.eq( 0 ).stop().show();
                }

                if ( ! this.popupContainer.data( 'inited' ) ) {
                this.popupContainer.on( 'click', '.compare-close', function() {
                    var $obj = $( this ).closest( '.success-message-container' );
                    $obj.removeClass( 'active' );
                    setTimeout( function() {
                        $obj.slideUp( 300, function() {
                            $obj.remove();
                        } );
                    }, 350 );
                } );
                    this.popupContainer.data( 'inited', true );
                }
                this.build();
                return this;
            },
            build: function() {
                var self = this;
                if ( js_porto_vars.compare_popup && ( ! yith_woocompare || ( yith_woocompare && ( 'no' == yith_woocompare.auto_open || '' == yith_woocompare.auto_open ) ) ) ) {
                    var self = this,
                        isWishlistTable = $( '.wishlist_table' ).length > 0 ? true : false,
                        $product = isWishlistTable ? self.$el.closest( 'tr' ) : self.$el.closest( '.product' ),
                        message = typeof js_porto_vars.compare_popup_title == 'string' ? js_porto_vars.compare_popup_title : '',
                        link = isWishlistTable ? $product.find( '.product-thumbnail a:first-child' ).attr( 'href' ) : ( $product.find( '.product-image>a:first-child' ).length > 0 ? $product.find( '.product-image>a:first-child' ).attr( 'href' ) : '#' ),
                        image = isWishlistTable ? $product.find( '.product-thumbnail img' ).attr( 'src' ) : ( $product.find( '.product-inner .product-image img' ).length > 0 ? $product.find( '.product-inner .product-image img' ).attr( 'src' ) : $product.find( '.product-images .owl-item.active .img-thumbnail img' ).attr( 'src' ) ),
                        price = isWishlistTable ? $product.find( '.product-price' ).html() : $product.find( '.price' ).html(),
                        title = isWishlistTable ? $product.find( '.product-name a' ).text() : ( $product.find( '.woocommerce-loop-product__title' ).length == 1 ? $product.find( '.woocommerce-loop-product__title' ).text() : $product.find( '.product_title' ).text() );

                    if ( !image && $product.find( '.product-image img' ).length ) {
                        image = $product.find( '.product-image img' ).attr( 'src' );
                        var _image = $product.find( '.product-images img' );
                        if ( $product.find( '.product-image' ).closest( '.product-nav' ) && _image.length ) {
                            image = _image.attr( 'src' );
                            if ( _image.attr( 'data-oi' ) ) {
                                image = _image.attr( 'data-oi' );
                            }
                        }
                    }
                    if ( !title && $product.find( '.product-image' ).length ) {
                        title = $product.find( '.product-image' ).data( 'title' );
                    }
                    var $content = $( '<div class="compare-msg success-message-container"><p class="compare-popup-title">' + message +
                        '</p><div class="msg-box mb-0"><div class="msg"><div class="product-name"><a href="' + link +
                        '"><h3 class="product-title font-weight-bold line-height-sm mb-1">' + title + '</h3></a></div>' + '<span class="price text-primary">'
                        + ( price ? price : '' ) + '</span></div><img src="' + image + '" alt="' + title + '"/></div><button class="compare-close mfp-close"></button></div>' );

                    self.popupContainer.prepend( $content );
                    setTimeout( function() {
                        $content.addClass( 'active' );
                    }, 150 );
                    setTimeout( function() { $content.find( '.compare-close' ).trigger( 'click' ); }, 4000 );
                }
                return self;
            }
        }
    } );
} ).apply( this, [window.theme, jQuery] );


( function( $ ) {
    var productID = '';
    if ( $( '#yith-woocompare.woocommerce' ).length ) { // iframe
        $( document ).on( 'click', '.remove a', function() {
            productID = $( this ).data( 'product_id' );
        } );

        $( window ).on( 'yith_woocompare_product_removed', function() {
            var $compare_btn = $( '.porto-tb-compare[data-product_id="' + productID + '"]', window.parent.document );
            $compare_btn.html( '<i class="' + $compare_btn.data( 'add_icon' ) + '"></i>' + ( $compare_btn.data( 'hide_title' ) == 'hide' ? '' : yith_woocompare.button_text ) );

            $( '.compare[data-product_id="' + productID + '"]', window.parent.document ).attr( 'data-bs-original-title', yith_woocompare.button_text );

            $( '.yith-woocompare-open .compare-count', window.parent.document ).each( function() {
                this.innerHTML = Math.max( 0, parseInt( this.innerHTML ) - 1 );
            } );
        } );
    }

    // Higher version > 3.0
    $( document ).on( 'click', '#yith-woocompare-table_wrapper .remove a', function() {
        productID = $( this ).data( 'product_id' );
    });

    $( window ).on( 'yith_woocompare_product_removed', function() {
        if ( productID ) {
            var $compare_btn = $( '.porto-tb-compare[data-product_id="' + productID + '"]' );
            $compare_btn.each( function() {
                $each_btn = $( this );
                $each_btn.html( '<i class="' + $each_btn.data( 'add_icon' ) + '"></i>' + ( $each_btn.data( 'hide_title' ) == 'hide' ? '' : yith_woocompare.button_text ) );
            });

            $( '.compare[data-product_id="' + productID + '"]' ).attr( 'data-bs-original-title', yith_woocompare.button_text );

            $( '.yith-woocompare-open .compare-count' ).each( function() {
                this.innerHTML = Math.max( 0, parseInt( this.innerHTML ) - 1 );
            } );
            // Single Product
            var $sp_btn = $( '.single-product .widget-compare [data-product_id="' + productID + '"], .single-product .product-summary-wrap [data-product_id="' + productID + '"]' );
            $sp_btn.each( function() {
                $each_btn = $( this );
                $each_btn.html( ( $each_btn.data( 'hide_title' ) == 'hide' ? '' : yith_woocompare.button_text ) );
            });
        }
    } );


} )( window.jQuery );