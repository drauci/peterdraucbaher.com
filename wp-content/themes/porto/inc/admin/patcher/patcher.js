jQuery( document ).ready( function( $ ) {
    'use strict';
    $( '#patch-apply:not(.inactive)' ).on( 'click', function( e ) {
        e.preventDefault();
        $( '#patcher-tbody' ).addClass( 'loading' ).append( '<i class="porto-ajax-loader"></i>' );
        $( '.porto-patch-layout .btn' ).attr( 'disabled', true );
        $.ajax( {
            url: js_porto_admin_vars.ajax_url,
            type: 'POST',
            data: {
                'action': 'porto_apply_patches',
                'nonce': js_porto_admin_vars.nonce,
            },
            success: function( response ) {

                $( '.apply-alert' ).remove();
                if ( response.success ) {
                    if ( response.data ) {
                        var update_patches = response.data.update,
                            delete_patches = response.data.delete;
                        if ( 'object' == typeof update_patches ) {
                            update_patches = Object.keys( update_patches );
                            update_patches.forEach( patch => {
                                $( '[data-path="update-' + patch ).remove();
                            } );
                        }

                        if ( 'object' == typeof delete_patches ) {
                            delete_patches = Object.keys( delete_patches );
                            delete_patches.forEach( patch => {
                                $( '[data-path="delete-' + patch ).remove();
                            } );
                        }
                    }

                    if ( response.data.error ) {
                        $( '#main' ).prepend( '<div class="apply-alert error"><p>' + wp.i18n.__( 'The below patches could not be applied. Because your files have write permission or aren\'t existed.', 'porto' ) + '</p></div>' );
                    } else {
                        $( '#main' ).prepend( '<div class="apply-alert updated"><p>' + wp.i18n.__( 'All files patched successfully.', 'porto' ) + '</p></div>' );
                    }
                } else {
                    $( '#main' ).prepend( '<div class="apply-alert error"><p>' + wp.i18n.__( 'The Porto patches server could not be reached.', 'porto' ) + '</p></div>' );
                }
                $( '#patcher-tbody' ).removeClass( 'loading' ).find( '.porto-ajax-loader' ).remove();
                $( '.porto-patch-layout .btn' ).removeAttr( 'disabled' );
            },
        } );
    } )
} );