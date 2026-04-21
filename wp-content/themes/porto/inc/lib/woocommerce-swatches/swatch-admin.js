/**
 * Swatch Admin
 * 
 * For product attribute and edit product page
 * 
 * @since 7.2.0
 */

( function( $ ) {
    $( document.body ).on( 'click', '.attribute_type_image .img-btn-item', function (e) {
        var $this = $( e.target ).parent(),
            type = $this.data( 'value' ),
            $select = $( 'select#attribute_type' );
        $this.addClass( 'active' ).siblings().removeClass( 'active' );

        if ( $select.length ) {
            $select.find( 'option[value="' + type + '"]' ).prop( 'selected', true ).siblings().prop( 'selected', false );
        }
    } );


    // images / colors swatch
	$( '#porto_swatches' ).on( 'change', 'select.swatch_option_type', function() {
		var $parent = $( this ).closest( '.porto_swatches_section' );
		$parent.find( '[class*="swatch_field_"]' ).hide();
		$parent.find( '.swatch_field_' + $( this ).val() ).show();
	} );
	$( '#porto_swatches' ).on( 'click', '.remove_swatch_image_button', function( e ) {
		e.preventDefault();
		$( this ).parent().find( '.upload_image_id' ).val( '' );
		$( this ).closest( 'td' ).find( 'img' ).attr( 'src', porto_swatches_params.placeholder_src );
	} );
	var frame = null;
	$( '#porto_swatches' ).on( 'click', '.upload_swatch_image_button', function( e ) {
		e.preventDefault();
		var $button = $( this );
		if ( frame ) {
			frame.porto_swatches_btn = $button;
			frame.open();
			return;
		}
		frame = wp.media( {
			title: 'Select or Upload an Image',
			button: {
				text: 'Use this media'
			},
			multiple: false
		} );
		frame.porto_swatches_btn = $button;
		frame.on( 'select', function() {
			if ( frame.porto_swatches_btn ) {
				var attachment = frame.state().get( 'selection' ).first().toJSON(),
					$input = frame.porto_swatches_btn.parent().find( '.upload_image_id' );
				$input.val( attachment.id );
				frame.porto_swatches_btn.closest( 'td' ).find( 'img' ).attr( 'src', attachment.url );
			}
		} );
		frame.open();
		return false;
	} );
	$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', function() {
		var wrapper = $( '#porto_swatches' );
		if ( !wrapper.length ) {
			return;
		}
		wrapper.block( {
			message: null,
			overlayCSS: {
				opacity: 0.1
			}
		} );
		$.ajax( {
			url: porto_swatches_params.ajax_url,
			data: {
				action: 'porto_load_swatches',
				wpnonce: porto_swatches_params.wpnonce,
				product_id: porto_swatches_params.post_id
			},
			type: 'POST',
			success: function( response ) {
				wrapper.empty().append( response );
				wrapper.find( '.porto-meta-color' ).each( function() {
					$( this ).trigger( 'plugin_init' );
				} );
				$( '.woocommerce-help-tip', wrapper ).tipTip( {
					'attribute': 'data-tip',
					'fadeIn': 50,
					'fadeOut': 50,
					'delay': 200
				} );
			}
		} );
	} );
} )( window.jQuery );