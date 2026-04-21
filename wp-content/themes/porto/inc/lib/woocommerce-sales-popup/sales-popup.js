// Sales Popup
( function( theme, $ ) {
    'use strict';
    theme = theme || {};
    if ( !js_porto_vars.sales_popup ) return;
    $.extend( theme, {
        salesPopup: {
            isCart: true,
            popupContainer: null,
            current: 0,
            products: [],
            worker: null,
            initialize: function() {
                if ( $( '.after-loading-success-message.style-3' ).length == 0 ) {
                    $( 'body' ).append( '<div class="after-loading-success-message style-3 d-block"></div>' );
                    this.isCart = false;
                    this.popupContainer = $( '.after-loading-success-message.style-3' );
                }
                else {
                    this.popupContainer = $( '.after-loading-success-message.style-3' );
                    this.popupContainer.eq( 0 ).stop().show();
                }

                this.popupContainer.on( 'click', '.sales-close', function( e ) {
                    e.stopPropagation();
                    var $obj = $( this ).closest( '.success-message-container' );
                    $obj.removeClass( 'active' );
                    setTimeout( function() {
                        $obj.slideUp( 300, function() {
                            $obj.remove();
                        } );
                    }, 350 );
                } );
                this.build();
                return this;
            },
            appendContent: function( product ) {
                var message = js_porto_vars.sales_popup.title,
                    link = product.link,
                    date = product.date,
                    image = product.image,
                    price = product.price,
                    title = product.title;

                var $content = $( '<div class="sales-msg success-message-container"><p class="sales-popup-title">' + message +
                    '</p><div class="msg-box mb-0"><div class="msg"><div class="product-name"><a href="' + link +
                    '"><h3 class="product-title font-weight-bold line-height-sm mb-1">' + title + '</h3></a></div>' + '<span class="price text-primary">'
                    + price + '</span><p class="mt-1 mb-0' + ( date == 'not sale' ? ' d-none' : '' ) + '">' + date + '</p></div><img src="' + image + '" alt="' + title + '"/></div><button aria-label="Close the sales popup" class="sales-close mfp-close"></button></div>' )

                this.popupContainer.prepend( $content );
                setTimeout( function() {
                    $content.addClass( 'active' );
                }, 150 );
                setTimeout( function() { $content.find( '.sales-close' ).trigger( 'click' ); }, 4000 );
            },
            build: function() {
                var self = this;

                self.worker = new Worker( js_porto_vars.sales_popup.themeuri + '/inc/lib/woocommerce-sales-popup/worker.js' );
                self.worker.onmessage = function( e ) {
                    if ( e.data && e.data.title ) {
                        self.appendContent( e.data );
                    }
                };
                self.worker.postMessage( { init: true, vars: js_porto_vars.sales_popup, ajaxurl: theme.ajax_url, nonce: js_porto_vars.porto_nonce } );
                return self;
            }
        }
    } );
} ).apply( this, [window.theme, jQuery] );


jQuery( document ).ready( function( $ ) {
    // Sales Popup
    if ( typeof theme.salesPopup !== 'undefined' && !document.getElementById( 'yith-woocompare' ) && window.Worker ) {
        theme.salesPopup.initialize();
    }
});