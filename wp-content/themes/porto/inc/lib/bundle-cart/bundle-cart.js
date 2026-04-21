// Add to cart multi products
( function( theme, $ ) {
    'use strict';

    theme = theme || {};

    $( document ).on( 'click', '.porto_bundle_cart', function( e ) {
        e.preventDefault();
        var $obj = $(this),
            $ids = $obj.data( 'bundle-cart' );
            $obj.addClass( 'loading' );

        $.ajax( {
            url: theme.ajax_url,
            data: {
                action : 'porto_bundle_cart',
                ids    : $ids
            },
            type: 'post',
            dataType: 'json',
            success : function( data ) {
                $obj.removeClass( 'loading' );
                if ( true == data['success'] ) {
                    window.location.href = wc_add_to_cart_params.cart_url;
                    return;
                }
            }
        } );
    } );

} ).apply( this, [window.theme, jQuery] );