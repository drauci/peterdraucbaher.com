( function( theme, $ ) {
    // shortcode: porto_one_page_category_products
	$( '.porto-onepage-category.show-products .category-section .sub-category' ).children( '.cat-item' ).addClass( 'product-col' );
    $( document ).on( 'click', '.porto-onepage-category .sub-category a', function( e ) {
        var $this = $( this ), category, data;
        category = new RegExp( "cat-item-([^( |\")]*)", "i" ).exec( $this.parent().attr( 'class' ) );
        category = category && unescape( category[1] ) || "";
        if ( category ) {
            data = $this.closest( '.category-details' ).find( '.ajax-form' ).serialize() + '&action=porto_woocommerce_shortcodes_products&category_description=true&category=' + category + '&nonce=' + js_porto_vars.porto_nonce;
            e.preventDefault();
            $this.closest( '.category-section' ).find( '.woocommerce > ul.products' ).trigger( 'porto_update_products', [data, ''] );
        }
    } );

    var scrollspyData = null;
    var onePageCategoryFunc = function() {
        if ( $( '.porto-onepage-category.show-products' ).length && typeof bootstrap != 'undefined' ) {
            document.body.style.position = 'relative';
            scrollspyData = new bootstrap.ScrollSpy( 'body', { target: '.porto-onepage-category.show-products .category-list', offset: theme.StickyHeader.sticky_height + theme.adminBarHeight() + theme.sticky_nav_height + 20 } );
            var previousScrollTop = 0, $loadObj;
            window.addEventListener( 'scroll', function() {
                if ( !$( '.porto-onepage-category.show-products.ajax-load .category-section:not(.ajax-loaded)' ).length ) {
                    return;
                }
                var currentScrollTop = $( window ).scrollTop();
                if ( previousScrollTop > currentScrollTop ) { // up
                    $loadObj = $( '.porto-onepage-category.show-products.ajax-load .category-section:not(.ajax-loaded)' ).last();
                } else { //down
                    $loadObj = $( '.porto-onepage-category.show-products.ajax-load .category-section:not(.ajax-loaded)' ).eq( 0 );
                }
                previousScrollTop = $( window ).scrollTop();
                if ( !$loadObj.closest( '.porto-onepage-category' ).hasClass( 'loading' ) && ( $loadObj.offset().top <= $( window ).scrollTop() + $( window ).innerHeight() * 0.7 ) ) {
                    $loadObj.trigger( 'porto_load_category_products' );
                }
            }, { passive: true } );
        }
    }
    if ( theme.isReady ) { // Finish init
        onePageCategoryFunc();
    }
    $( document ).on( 'porto_theme_init', onePageCategoryFunc );

    $( document ).on( 'click', '.porto-onepage-category.show-products .category-list .nav-link', function( e ) {
        var $target = $( $( this ).attr( 'href' ) );
        if ( !$target.length ) {
            return;
        }
        e.preventDefault();
        if ( $( this ).closest( '.porto-onepage-category' ).hasClass( 'ajax-load' ) && !$target.hasClass( 'ajax-loaded' ) ) {
            $target.trigger( 'porto_load_category_products' );
        }
        $target.closest( '.porto-onepage-category' ).addClass( 'moving' );
        $( 'html, body' ).stop().animate( {
            scrollTop: $target.offset().top - theme.StickyHeader.sticky_height - theme.adminBarHeight() - theme.sticky_nav_height - 10
        }, 600, 'easeOutQuad', function() {
            $target.closest( '.porto-onepage-category' ).removeClass( 'moving' );
        } );
    } );

    $( document ).on( 'porto_load_category_products', '.category-section', function() {
        var $target = $( this ), cat_id = $target.attr( 'id' ).replace( 'category-', '' );
        if ( $target.closest( '.porto-onepage-category' ).hasClass( 'loading' ) || $target.closest( '.porto-onepage-category' ).hasClass( 'moving' ) || $target.hasClass( 'ajax-loaded' ) ) {
            return false;
        }
        $target.css( 'min-height', 200 );
        $target.addClass( 'yith-wcan-loading' );
        if ( !$target.children( '.porto-loading-icon' ).length ) {
            $target.append( '<i class="porto-loading-icon"></i>' );
        }
        $target.closest( '.porto-onepage-category' ).addClass( 'loading' );
        var data = $target.closest( '.porto-onepage-category' ).find( '.ajax-form' ).serialize() + '&action=porto_woocommerce_shortcodes_products&category_description=true&category=' + cat_id + '&nonce=' + js_porto_vars.porto_nonce;
        $.ajax( {
            url: theme.ajax_url,
            data: data,
            type: 'post',
            success: function( response ) {
                $target.addClass( 'ajax-loaded' );
                $target.append( $( response ).html() );
                $target.removeClass( 'yith-wcan-loading' );
                $( document ).trigger( 'yith-wcan-ajax-filtered' );
                porto_woocommerce_init(); // Init woo slider
                $( window ).trigger( 'resize' );
                if ( scrollspyData ) {
                    setTimeout( function() {
                        scrollspyData.refresh();
                    }, 300 );
                }
                $target.closest( '.porto-onepage-category' ).removeClass( 'loading' );
            }
        } );
    } );

} )( window.theme, jQuery );