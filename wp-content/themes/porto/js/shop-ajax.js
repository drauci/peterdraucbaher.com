// Woocommerce Category Filter
( function( theme, $ ) {

    /**
     Copyright (c) 2010, All Right Reserved, Wong Shek Hei @ shekhei@gmail.com
        License: GNU Lesser General Public License (http://www.gnu.org/licenses/lgpl.html)
        **/
    var expr = /[.#\w].([\S]*)/g, classexpr = /(?!(\[))(\.)[^.#[]*/g, idexpr = /(#)[^.#[]*/, tagexpr = /^[\w]+/, varexpr = /(\w+?)=(['"])([^\2$]*?)\2/, simpleselector = /^[\w]+$/, parseSelector = function( d ) {
        for ( var c = { sel: [], val: [] }, a = [], j = !1, h = "", e = [], f = 0, m = d.length; f < m; f++ ) {
            var g = d.charAt( f );
            if ( j ) if ( "\\" === g && f + 1 < d.length ) e.push( d.charAt( ++f ) ); else if ( h === g ) h = "", e.push( g ); else if ( ( "'" === g || '"' === g ) && "" === h ) h = g, e.push( g ); else if ( "]" === g && "" === h ) c.val.push( e.join( "" ) ), e = [], j = !1; else {
                if ( "]" !== g || "" !== h ) "" === h && "," === g ? ( c.val.push( e.join( "" ) ),
                    e = [] ) : e.push( g )
            } else "\\" === g && f + 1 < d.length ? j && e.push( d.charAt( ++f ) ) : "[" === g && "" === h ? j = !0 : " " === g || "+" === g ? ( c.sel = c.sel.join( "" ), a.push( c ), "+" === g && a.push( { sel: "+", val: "" } ), c = { sel: [], val: [] } ) : " " !== g && "]" !== g && c.sel.push( g )
        }
        if ( 0 != c.sel.length || 0 != c.val.length ) c.sel = c.sel.join( "" ), a.push( c );
        for ( f = 0; f < a.length; f++ ) {
            c = a[f].sel;
            if ( "+" === c ) b.tag = c; else {
                var b = [];
                b.tag = tagexpr.exec( c );
                b.id = idexpr.exec( c );
                b.id && Array.isArray( b.id ) && ( b.id = b.id[0].substr( 1 ) );
                b.tag || ( b.tag = "div" );
                b.vars = [];
                for ( d = 0; d < a[f].val.length; d++ )h =
                    a[f].val[d].indexOf( "=" ), j = a[f].val[d].substr( 0, h ), h = a[f].val[d].substr( h + 1 ), h = h.replace( /^[\s]*[\"\']*|[\"\']*[\s]*$/g, "" ), "text" === j ? b.text = h : b.vars.push( [j, h] );
                c = c.match( classexpr );
                j = [];
                if ( c ) {
                    for ( d = 0; d < c.length; d++ )j.push( c[d].substr( 1 ) );
                    b.className = j.join( " " )
                }
            }
            a[f] = b
        }
        return a
    }, rmFromParent = function( d ) {
        var c = d.parentNode, a = d.nextSibling;
        c.removeChild( d );
        return a ? function() {
            c.insertBefore( d, a )
        } : function() {
            c.appendChild( d )
        }
    }, nonArrVer = function( d, c ) {
        var a = [], a = simpleselector.test( d ) ? [
            { tag: d }
        ] : parseSelector( d ),
            j = [];
        "undefined" === typeof c && ( c = 1 );
        for ( var h = [], e = [], f = [], m = document.createElement( "div" ), g = 0, b = 0; b < a.length; b++ ) {
            if ( "+" == a[b].tag ) e = f.slice(), --g; else {
                for ( var l = 0; l < c; l++ ) {
                    var k;
                    if ( "input" == a[b].tag ) {
                        k = [];
                        k.push( "<" + a[b].tag );
                        a[b].id && k.push( "id='" + a[b].id + "'" );
                        a[b].className && ( k.push( "class='" + a[b].className ), b + 1 === a.length && k.push( lastClass ), k.push( "'" ) );
                        if ( a[b].vars ) for ( var n = 0; n < a[b].vars.length; n++ )k.push( a[b].vars[n][0] + "='" + a[b].vars[n][1] + "'" );
                        a[b].text && k.push( "value='" + a[b].text + "'" );
                        k.push( "/>" );
                        f[l] = e[l];
                        e[l] ? ( e[l].innerHTML += k.join( " " ), e[l] = e[l].lastChild ) : ( m.innerHTML = k.join( " " ), e[l] = m.removeChild( m.firstChild ) )
                    } else {
                        k = document.createElement( a[b].tag );
                        if ( a[b].vars ) for ( var n = 0; n < a[b].vars.length; n++ )k.setAttribute( a[b].vars[n][0], a[b].vars[n][1] );
                        a[b].id && ( k.id = a[b].id );
                        a[b].className && ( k.className = a[b].className );
                        a[b].text && k.appendChild( document.createTextNode( a[b].text ) );
                        f[l] = e[l];
                        e[l] = e[l] ? e[l].appendChild( k ) : k
                    }
                }
                g++ || Array.prototype.push.apply( h, e );
            }
            j = $.merge( j, e );
        }
        return $( h )
    }, arrVer = function( d, c, a ) {
        for ( var j = d.match( /%[^%]*%/g ) || [], h = [], e = 0; e < c.length; e++ ) {
            for ( var f = d, m = 0; m < j.length; m++ )var g = j[m].substr( 1, j[m].length - 2 ), f = f.replace( j[m], c[e][g] );
            h = $.merge( h, nonArrVer( f, a ) )
        }
        return $( h )
    };

    $.porto_jseldom = function( d ) {
        if ( 2 == arguments.length && $.isPlainObject( arguments[1] ) ) return arrVer.apply( this, [arguments[0], [arguments[1]]] );
        if ( 1 == arguments.length || 2 == arguments.length && !Array.isArray( arguments[1] ) ) return nonArrVer.apply( this, arguments );
        if ( 2 == arguments.length ) return arrVer.apply( this, arguments )
    };

    var refreshPriceSlider = function() {

        var $price_slider = $( '.price_slider' );

        if ( $price_slider.length ) {
            // woocommerce_price_slider_params is required to continue, ensure the object exists
            if ( typeof woocommerce_price_slider_params === 'undefined' ) {
                return false;
            }

            // Get markup ready for slider
            $( 'input#min_price, input#max_price' ).hide();
            $( '.price_slider, .price_label' ).show();

            // Price slider uses jquery ui
            var min_price = $( '.price_slider_amount #min_price' ).data( 'min' ),
                max_price = $( '.price_slider_amount #max_price' ).data( 'max' ),
                current_min_price = parseInt( $( '.price_slider_amount #min_price' ).val() ? $( '.price_slider_amount #min_price' ).val() : min_price, 10 ),
                current_max_price = parseInt( $( '.price_slider_amount #max_price' ).val() ? $( '.price_slider_amount #max_price' ).val() : max_price, 10 );

            $( '.price_slider' ).filter( function() {
                if ( $( this ).closest( 'form' ).find( 'input#min_price, input#max_price' ).length ) {
                    return true;
                }
                return false;
            } ).slider( {
                range: true,
                animate: true,
                min: min_price,
                max: max_price,
                values: [current_min_price, current_max_price],
                create: function() {

                    $( '.price_slider_amount #min_price' ).val( current_min_price );
                    $( '.price_slider_amount #max_price' ).val( current_max_price );

                    $( document.body ).trigger( 'price_slider_create', [current_min_price, current_max_price] );
                },
                slide: function( event, ui ) {

                    $( 'input#min_price' ).val( ui.values[0] );
                    $( 'input#max_price' ).val( ui.values[1] );

                    $( document.body ).trigger( 'price_slider_slide', [ui.values[0], ui.values[1]] );
                },
                change: function( event, ui ) {

                    $( document.body ).trigger( 'price_slider_change', [ui.values[0], ui.values[1]] );
                }
            } );
        }

        // remove filter loading
        $( '.yith-woo-ajax-navigation, .yith-wcan-list-price-filter' ).removeClass( 'loading' );
    };

    var categoryAjaxProcess = function( href, updateSelect2, updateStyle, isCategoryFilter = false ) {
        if ( 'undefined' != typeof window.parent && 'undefined' != typeof window.parent.vc ) {
            // prevent for WPBakery frontend preview
            return;
        }
        var _prefix = '.archive-products ';
        if ( $( '.elementor-widget-wc-archive-products' ).length ) {
            _prefix = '.elementor-widget-wc-archive-products ';
        }
        if ( $( '.wcfmmp-store-page-wrap' ).length ) {
            _prefix = '.wcfmmp-store-page-wrap ';
        }
        var shop_before = '.shop-loop-before',
            $shop_before = $( shop_before ),
            shop_after = '.shop-loop-after:not(.is-shortcode)',
            shop_container = _prefix + '.products:not(.is-shortcode)',
            shop_info = _prefix + '.woocommerce-info',
            //$wrapper = $('#content.site-main'),
            $shop_parent = $shop_before.parent(),
            $shop_container = $( shop_container ),
            $sticky_sidebar = $( '.sidebar [data-plugin-sticky]' ),
            show_toolbar = $shop_before.data( 'show' ),
            horizontal_filter = '.porto-product-filters:not(.style2)';

        if ( show_toolbar )
            $( shop_before + ',' + shop_after ).stop( true ).fadeTo( 400, 1 ).block( { message: null, overlayCSS: { opacity: 0.2 } } );
        if ( js_porto_vars.use_skeleton_screen.indexOf( 'shop' ) == -1 ) {
            if ( $shop_container.length ) {
                $shop_container.addClass( 'yith-wcan-loading' );
                if ( !$shop_container.children( '.porto-loading-icon' ).length ) {
                    $shop_container.append( '<i class="porto-loading-icon"></i>' );
                }
            } else {
                $( shop_info ).html( '' ).addClass( 'yith-wcan-loading products' );
                if ( !$( shop_info ).children( '.porto-loading-icon' ).length ) {
                    $( shop_info ).append( '<i class="porto-loading-icon"></i>' );
                }
            }
        } else {
            if ( $shop_container.length ) {
                $shop_container.addClass( 'skeleton-body' );
                var lg_cols,
                    other_col_cls = $shop_container.data( ( $shop_container.hasClass( 'list' ) ? 'list' : 'grid' ) + '_col_cls' );
                if ( $shop_container.hasClass( 'list' ) && !other_col_cls ) {
                    lg_cols = 4;
                } else {
                    for ( var i = 1; i <= 8; i++ ) {
                        if ( $shop_container.hasClass( 'ccols-xl-' + i ) ) {
                            lg_cols = i;
                            break;
                        }
                    }
                    if ( !lg_cols ) {
                        if ( $shop_container.hasClass( 'has-ccols' ) ) {
                            var clses;
                            if ( other_col_cls ) {
                                clses = $shop_container.attr( 'class' ).split( ' ' );
                                var new_cls = [];
                                for ( var i = 0; i < clses.length; i++ ) {
                                    if ( 0 !== clses[i].indexOf( 'ccols-' ) && 'has-ccols' != clses[i] ) {
                                        new_cls.push( clses[i] );
                                    }
                                }
                                $shop_container.attr( 'class', new_cls.join( ' ' ) + ' ' + other_col_cls );
                                clses = other_col_cls.split( ' ' );
                            } else {
                                clses = $shop_container.attr( 'class' ).split( ' ' );
                            }
                            for ( var i = 0; i < clses.length; i++ ) {
                                if ( 0 === clses[i].indexOf( 'ccols-' ) ) {
                                    lg_cols = clses[i].replace( /ccols-[sm|md|lg|xl|xxl]*[-]*([\d])/, '$1' );
                                    break;
                                }
                            }
                        }
                    }
                }
                if ( lg_cols ) {
                    var product_class = 'product product-col';
                    $shop_container.empty();
                    if ( $shop_container.data( 'product_layout' ) ) {
                        product_class += ' ' + escape( $shop_container.data( 'product_layout' ) );
                    }
                    for ( var i = 0; i < lg_cols * 3; i++ ) {
                        $shop_container.append( '<li class="' + product_class + '"></li>' );
                    }
                } else {
                    $shop_container.find( '.product-col' ).empty();
                }

                if ( $shop_container.hasClass( 'owl-loaded' ) ) {
                    $shop_container.removeClass( 'owl-loaded' );
                }
            }
        }

        if ( $( horizontal_filter ).length ) {
            $( horizontal_filter ).block( { message: null, overlayCSS: { opacity: 0.2 } } );
        }

        // if ( $sticky_sidebar.get( 0 ) ) {
        //     //$shop_parent.css('min-height', $sticky_sidebar.height());
        //     theme.refreshStickySidebar( false );
        // }

        theme.scrolltoContainer( show_toolbar ? ( $shop_before.hasClass( 'sticky' ) && $shop_before.prev( '.filter-placeholder' ).length ? $shop_before.prev( '.filter-placeholder' ) : $shop_before ) : $shop_container );

        $( '.yith-woo-ajax-navigation, .yith-wcan-list-price-filter' ).addClass( 'loading' );

        var cart_content, widget_cart;

        if ( widget_cart = $( '.sidebar-content .widget_shopping_cart' ).get( 0 ) ) {
            cart_content = $( widget_cart ).html();
        }


        if ( ! theme.shopAjaxCache ) {
            theme.shopAjaxCache = {};
        }
        if ( ! theme.urlAnchor ) {
            theme.urlAnchor = document.createElement( 'a' );
        }
        theme.urlAnchor.href = href;
        let href1 = theme.urlAnchor.href;
        // if ( -1 == isCategoryFilter || ( $.cookie && 'list' == $.cookie( 'gridcookie' ) ) ) {
        //     href1 += '&toggle=list';
        // }
        var uniqueId = encodeURIComponent( href1 + 'is_category=' + isCategoryFilter );

        var successfn = function( response, filterID, isCategoryFilter, popstate = false ) {


            if ( filterID && response && ! theme.shopAjaxCache[filterID] ) {
                theme.shopAjaxCache[filterID] = response;
            }

            var $response = $( response );

            if ( isCategoryFilter == true ) {
                $( '.header-wrapper ~ *:not(.footer-wrapper)' ).remove();
                $response.find( '.header-wrapper ~ *:not(.footer-wrapper)' ).clone().insertAfter( '.header-wrapper' );

                $shop_before = $( shop_before );
                $shop_parent = $shop_before.parent();
                $shop_container = $( shop_container );
                $sticky_sidebar = $( '.sidebar [data-plugin-sticky]' );
                if ( $( 'html' ).hasClass( 'sidebar-opened' ) && $( 'body' ).hasClass( 'woocommerce' ) ) {
                    $( '.main-content-wrap>.sidebar-overlay' ).addClass( 'active' );
                }
            }

            var $parent = $shop_container.parent();
            if ( $parent.hasClass( 'yit-wcan-container' ) ) {
                $parent = $parent.parent();
            }

            if ( $sticky_sidebar.get( 0 ) )
                $shop_parent.css( 'min-height', 0 );

            var $response_container = $response.find( shop_container );
            // products container
            if ( $response_container.length ) {
                if ( $shop_container.length && $shop_container.data( 'infinitescroll' ) ) {
                    try {
                        $shop_container.data( 'infinitescroll' ).destroy();
                        var ins = $shop_container.data( '__postsinfinite' );
                        if ( ins ) {
                            ins.destroy();
                        }
                    } catch ( e ) {
                    }
                }
                // update style
                if ( typeof updateStyle != 'undefined' && updateStyle && $parent.hasClass( 'porto-posts-grid' ) ) {
                    var old_style = $shop_container.siblings( 'style' ),
                        new_style = $response_container.siblings( 'style' );
                    if ( old_style.length && new_style.length ) {
                        old_style.replaceWith( new_style );
                    }
                }

                //$parent.html( $response_container );
                $( $response_container ).addClass( 'animated fadeInUp' );
                $shop_container.replaceWith( $response_container[0].outerHTML );
                $shop_container = $( shop_container );
            } else if ( $parent.hasClass( 'porto-posts-grid' ) ) {
                $shop_container.empty();
            } else {
                $parent.html( $response.find( '.woocommerce-info' ) );
                $parent.find( '.woocommerce-info' ).addClass( 'products' );
            }

            if ( $( shop_before + ',' + shop_after ).get( 0 ) )
                $( shop_before + ',' + shop_after ).stop( true ).css( 'opacity', '1' ).unblock();

            // top toolbar
            if ( $response.find( shop_before ).length ) {
                if ( $( shop_before ).length == 0 ) {
                    $.porto_jseldom( shop_before ).insertBefore( $( shop_container ) );
                }

                $( shop_before ).each( function( index ) {
                    var $res_shop_before = $response.find( shop_before ).eq( index );
                    if ( $res_shop_before.length ) {
                        $( this ).html( $res_shop_before.html() ).show();
                    }
                } );
            } else {
                $( shop_before ).empty();
            }

            // reset variations form
            porto_woocommerce_variations_init( $parent );

            // horizontal filter
            if ( $response.find( horizontal_filter ).length ) {
                $( horizontal_filter ).html( $response.find( horizontal_filter ).html() );
            }
            $( horizontal_filter ).unblock();

            // bottom toolbar
            if ( $response.find( shop_after ).length ) {
                if ( $( shop_after ).length == 0 ) {
                    $.porto_jseldom( shop_after ).insertAfter( $( shop_container ) );
                }
                if ( $( shop_after ).length > 1 && $( shop_after ).length == $response.find( shop_after ).length ) {
                    for ( var i = 0; i < $( shop_after ).length; i++) {
                        $( shop_after ).eq(i).html( $response.find( shop_after ).eq(i).html() ).show();
                    }
                } else {
                    $( shop_after ).html( $response.find( shop_after ).html() ).show();
                }
            } else {
                if ( $( shop_after ).length == 0 && $response.find( '.woocommerce-pagination' ).length ) {
                    var $responsePg = $response.find( '.woocommerce-pagination' );
                    $( '.content-area#primary .woocommerce-pagination' ).each( function( index ) {
                        var $responseContent = $responsePg.eq( index );
                        if ( $responseContent.length ) {
                            $( this ).html( $responseContent.html() );
                        }
                    } );
                } else {
                    $( shop_after ).empty();
                }
            }

            // Update "Sort by" widget unwrapped with shop-loop-before or shop-loop-after
            $response.find( '.woocommerce-ordering' ).each( function ( index ) {
                var $this = $(this);
                if ( $this.closest( '.shop-loop-before' ).length == 0 && $this.closest( '.shop-loop-after' ).length == 0 ) {
                    $( 'body' ).find( '.woocommerce-ordering' ).eq(index).html( $this.html() );
                }
            } );

            // update pagination in shop builder
            var $pagination = $parent.children( '.pagination-wrap' ),
                $newPagination = $response_container.siblings( '.pagination-wrap' );
            if ( $parent.hasClass( 'yit-wcan-container' ) ) {
                $pagination = $parent.siblings( '.pagination-wrap' );
            }
            if ( $pagination.length ) {
                $pagination[0].outerHTML = $newPagination.length ? $newPagination[0].outerHTML : '';
            } else {
                $newPagination.length && $parent.append( $newPagination );
            }

            // update result count
            var $count = $( '.woocommerce-result-count' );
            if ( $count.length ) {
                var $newCount = $response.find( '.woocommerce-result-count' ).eq( 0 );
                if ( $newCount.length ) {
                    $count.each( function () {
                        this.outerHTML = $newCount[0].outerHTML;
                    } );
                }
            }

            // update category filter in shop builder
            /*var $filter = $parent.children( '.product-filter' ),
                $newFilter = $response_container.siblings( '.product-filter' );
            if ( $filter.length ) {
                if ( $newFilter.length ) {
                    $filter.replaceWith( $newFilter );
                } else {
                    $filter.remove();
                }
            } else if ( $newFilter.length ) {
                $newFilter = $newFilter.insertBefore( $shop_container );
            }
            if ( $newFilter.length && $newFilter.hasClass( 'porto-ajax-filter' ) ) {

            }*/

            // infinite scroll
            if ( $parent.hasClass( 'porto-posts-grid' ) ) {
                if ( $parent.is( '.porto-ajax-load.load-infinite, .porto-ajax-load.load-more' ) ) {
                    $parent.portoInfiniteScroll();
                }
            } else if ( typeof theme.PostsInfinite !== 'undefined' && typeof porto_infinite_scroll !== 'undefined' ) {
                new theme.PostsInfinite( $( shop_container ) );
            }

            $( '.sidebar-content' ).each( function( index ) {
                var $this = $( this ),
                    $that = $( $response.find( '.sidebar-content' ).get( index ) );

                $this.html( $that.html() );

                if ( typeof updateSelect2 != 'undefined' && updateSelect2 ) {
                    // Use Select2 enhancement if possible
                    if ( jQuery().selectWoo ) {
                        var porto_wc_layered_nav_select = function() {
                            $this.find( 'select.woocommerce-widget-layered-nav-dropdown' ).each( function() {
                                $( this ).selectWoo( {
                                    placeholder: $( this ).find( 'option' ).eq( 0 ).text(),
                                    minimumResultsForSearch: 5,
                                    width: '100%',
                                    allowClear: typeof $( this ).attr( 'multiple' ) != 'undefined' && $( this ).attr( 'multiple' ) == 'multiple' ? 'false' : 'true'
                                } );
                            } );
                        };
                        porto_wc_layered_nav_select();
                    }
                    $( 'body' ).children( 'span.select2-container' ).remove();
                }
            } );

            var $sidebar_menu = $( '.sidebar-content .sidebar-menu:not(.side-menu-accordion)' );
            if ( $sidebar_menu.length ) {
                theme.SidebarMenu.build( $sidebar_menu );
            }

            var $script = $response.filter( 'script:contains("var woocommerce_price_slider_params")' ).first();
            if ( $script && $script.length && $script.text().indexOf( '{' ) !== -1 && $script.text().indexOf( '}' ) !== -1 ) {
                var arrStr = $script.text().substring( $script.text().indexOf( '{' ), $script.text().indexOf( '}' ) + 1 );
                window.woocommerce_price_slider_params = JSON.parse( arrStr );
            }/* else {
                window.woocommerce_price_slider_params = undefined;
            }*/

            $(  '.sidebar-content form.woocommerce-widget-layered-nav-dropdown select' ).each( function () {
                var $this = $(this);
                $this.selectWoo({
                    placeholder: $this.find( 'option' ).eq( 0 ).text(),
                    minimumResultsForSearch: 5,
                    width: '100%',
                    allowClear: typeof $this.attr( 'multiple' ) != 'undefined' && $this.attr( 'multiple' ) == 'multiple' ? 'false' : 'true'
                });
                $this.siblings('.select2').css( 'width', '100%' );
            });
            
            //update browser history (IE doesn't support it)
            if ( !navigator.userAgent.match( /msie/i ) && ! popstate) {
                window.history.pushState( { pageTitle: response.pageTitle ? response.pageTitle : '', isShopAjax: 1, filterID: filterID, isCategoryFilter: isCategoryFilter }, "", href );
            }

            // Perfect brands
            var getFilterArg = function(e) {
                var o, i, r = decodeURIComponent(window.location.search.substring(1)).split("&");
                for (i = 0; i < r.length; i++)
                    if ((o = r[i].split("="))[0] === e)
                        return void 0 === o[1] || o[1]
            }
            var r = getFilterArg( 'pwb-brand-filter' );
            if ( null != r ) {
                var t = r.split( "," );
                $( '.pwb-filter-products input[type="checkbox"]' ).prop( "checked", !1 );
                for ( var a = 0, n = t.length; a < n; a++ )
                    $( '.pwb-filter-products input[type="checkbox"]' ).each( function(o) {
                        $( this ).val() && t[a] == $( this ).val() && $( this ).prop( "checked", !0 )
                    });
            } else
                $('.pwb-filter-products input[type="checkbox"]').prop("checked", !1)



            if ( $parent.hasClass( 'yit-wcan-container' ) ) {
                $parent.parent().removeClass( 'porto-ajax-loading' );
            } else if ( $parent.closest( '.elementor-widget-wc-archive-products' ).length ) {
                $parent.closest( '.elementor-widget-wc-archive-products' ).removeClass( 'porto-ajax-loading' );
            } else if ( $parent.closest( '.wcfmmp-store-page-wrap' ).length ) {
                $parent.closest( '.wcfmmp-store-page-wrap .products' ).removeClass( 'porto-ajax-loading' );
            } else {
                $parent.removeClass( 'porto-ajax-loading' );
            }

            //trigger ready event
            $( document ).trigger( 'yith_wcan_init_shortcodes' );
            $( document ).trigger( 'yith-wcan-ajax-filtered' );

            if ( widget_cart = $( '.sidebar-content .widget_shopping_cart' ).get( 0 ) ) {
                $( '.sidebar-content .widget_shopping_cart' ).html( cart_content );
                if ( $.cookie( 'woocommerce_items_in_cart' ) > 0 ) {
                    $( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).show();
                } else {
                    $( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).hide();
                }
            }

            if ( ( 'undefined' !== typeof yith_wcwl_l10n ) && yith_wcwl_l10n.enable_ajax_loading ) {
                $parent.trigger( 'yith_wcwl_reload_fragments' );
            }
            // init CountDown
            $( document.body ).trigger( 'porto_init_countdown', [$parent] );
            if ( typeof lazyloadRunObserver == 'function' ) {
                lazyloadRunObserver();
            }
            $( window ).trigger( 'porto_posts_updated' );
        }
        theme.successfn = successfn;
        if ( theme.shopAjaxCache[uniqueId] ) {
            successfn( theme.shopAjaxCache[uniqueId], uniqueId, isCategoryFilter );
        } else {
            $.ajax( {
                url: href,
                data: { portoajax: true, load_posts_only: true, is_category_filter: isCategoryFilter },
                type: "POST",
                success: function( res ) { successfn( res, uniqueId, isCategoryFilter ); }
            } );
        }

    };

    function porto_update_url_param( uri, key, value ) {
        var re = new RegExp( "([?&])" + key + "=.*?(&|$)", "i" );
        var separator = uri.indexOf( '?' ) !== -1 ? "&" : "?";
        if ( uri.match( re ) ) {
            return uri.replace( re, '$1' + key + "=" + value + '$2' );
        } else {
            return uri + separator + key + "=" + value;
        }
    }

    var categoryAjax = function() {
        // add class in price filter widget
        $( '.widget_price_filter' ).addClass( 'yith-wcan-list-price-filter' );

        if ( theme.category_ajax ) {

            // order by ajax
            $( '.woocommerce-ordering' ).off( 'change', 'select.orderby' ).on( 'change', 'select.orderby', function( e ) {
                e.preventDefault();

                var $this = $( this ),
                    $form = $this.closest( 'form' ),
                    href = '?' + $form.serialize();

                categoryAjaxProcess( href );
            } );

            // view ajax
            $( '.woocommerce-viewing' ).off( 'change', 'select.count' ).on( 'change', 'select.count', function( e ) {
                e.preventDefault();

                var $this = $( this ),
                    $form = $this.closest( 'form' ),
                    href = '?' + $form.serialize();

                categoryAjaxProcess( href );
            } );

            $( '.porto-posts-grid .product-category, .with-shop-ajax, .product-categories .cat-item, .wc-block-product-categories-list-item' ).off( 'click', 'a' ).on( 'click', 'a', function( e ) { 
                e.preventDefault();
                e.stopPropagation();
                var href = this.href;
                categoryAjaxProcess( href, undefined, undefined, true );
            } );
            // pagination ajax
            $( '.woocommerce-pagination:not(.load-more)' ).each( function() {
                if ( $( this ).closest( '.porto-products' ).length || $( this ).closest( '#comments' ).length ) {
                    return;
                }
                $( this ).off( 'click', 'a.page-numbers' ).on( 'click', 'a.page-numbers', function( e ) {
                    e.preventDefault();
                    var href = this.href;
                    categoryAjaxProcess( href );
                } );
            } );

            // yith filter
            $( document ).off( 'click', '.yith-wcan a' ).on( 'click', '.yith-wcan a', function( e ) {
                $( this ).yith_wcan_ajax_filters( e, this );
            } );

            // price filter ajax
            $( '.widget_price_filter .price_slider_wrapper' ).off( 'click', '.button' ).on( 'click', '.button', function( e ) {
                e.preventDefault();

                var $this = $( this ),
                    $form = $this.closest( 'form' ),
                    action = $form.attr( 'action' ),
                    href = action + ( -1 === action.indexOf( '?' ) ? '?' : '&' ) + $form.serialize(),
                    $count = $( '.woocommerce-viewing select.count' );

                if ( $count.length ) {
                    var count = $( '.woocommerce-viewing select.count' ).val();
                    if ( count != $count.find( 'option:not([disabled]):first' ).val() ) {
                        href += '&count=' + count;
                    }
                }

                $( '.widget_price_filter' ).removeClass( 'yith-wcan-list-price-filter' );

                categoryAjaxProcess( href );
            } );
            $( '.porto_widget_price_filter' ).off( 'click', '.button' ).on( 'click', '.button', function( e ) {
                e.preventDefault();

                var $this = $( this ),
                    $form = $this.closest( 'form' ),
                    action = $form.attr( 'action' ),
                    $count = $( '.woocommerce-viewing select.count' ),
                    hrefArr = $form.serializeArray(),
                    href = action;
                $.each( hrefArr, function( i, field ) {
                    if ( $.trim( field.value ) ) {
                        if ( action.indexOf( '?' ) == -1 && href == action ) {
                            href += '?';
                        } else {
                            href += '&';
                        }
                        href += ( field.name + "=" + $.trim( field.value ) );
                    }
                } );
                if ( $count.length ) {
                    var count = $( '.woocommerce-viewing select.count' ).val();
                    if ( count != $count.find( 'option:not([disabled]):first' ).val() ) {
                        if ( href.indexOf( '?' ) == -1 ) {
                            href += '?count=' + count;
                        } else {
                            href += '&count=' + count;
                        }
                    }
                }

                categoryAjaxProcess( href );
            } );

            // layerd nav filter
            $( '.widget_layered_nav, .widget_rating_filter, .widget_layered_nav_filters' ).off( 'click', 'a' ).on( 'click', 'a', function( e ) {
                if ( $( this ).hasClass( 'yit-wcan-select-open' ) )
                    return;

                e.preventDefault();

                var $this = $( this ),
                    href = $this.attr( 'href' ),
                    $count = $( '.woocommerce-viewing select.count' );
                var _prefix = '.archive-products ';
                if ( $( '.elementor-widget-wc-archive-products' ).length ) {
                    _prefix = '.elementor-widget-wc-archive-products ';
                }
                if ( $( '.wcfmmp-store-page-wrap' ).length ) {
                    _prefix = '.wcfmmp-store-page-wrap ';
                }
                if ( $this.hasClass( 'yith-wcan-reset-navigation' ) && !$( _prefix + '.products:not(.is-shortcode)' ).length ) {
                    window.location.href = href;
                    return false;
                }

                if ( $count.length ) {
                    var count = $( '.woocommerce-viewing select.count' ).val();
                    if ( count != $count.find( 'option:not([disabled]):first' ).val() ) {
                        //href += '&count=' + count;
                        href = porto_update_url_param( href, 'count', count );
                    }
                }

                var yith_select = $this.closest( '.yith-wcan-select' );
                if ( yith_select.get( 0 ) ) {
                    yith_select.parent().css( { "opacity": 0, "z-index": -1 } );
                }

                categoryAjaxProcess( href );

                return false;
            } );

            $( '.widget_layered_nav select:not([multiple])' ).off( 'change' ).on( 'change', function( e ) {
                var $this = $( this ),
                    name = $this.closest( 'form' ).find( 'input[type=hidden]' ).length ? $this.closest( 'form' ).find( 'input[type=hidden]' ).attr( 'name' ).replace( 'filter_', '' ) : $this.attr( 'class' ).replace( 'dropdown_layered_nav_', '' ),
                    slug = $this.val(),
                    href,
                    $count = $( '.woocommerce-viewing select.count' );

                if ( name == 'wc-brand-dropdown-layered-nav-product_brand' ) { // WC Brand
                    name = 'product_brand';
                }
                href = window.location.href;
                href = href.replace( /\/page\/\d+/, "" ).replace( "&amp;", '&' ).replace( "%2C", ',' );

                href = porto_update_url_param( href, 'filtering', '1' );
                href = porto_update_url_param( href, 'filter_' + name, slug );
                if ( $count.length ) {
                    var count = $( '.woocommerce-viewing select.count' ).val();
                    if ( count != $count.find( 'option:not([disabled]):first' ).val() ) {
                        href = porto_update_url_param( href, 'count', count );
                    }
                }

                categoryAjaxProcess( href, name );
                return false;
            } );

            var _brands = function() {
                var o = [location.protocol, "//", location.host, location.pathname].join("")
                , i = window.location.href
                , r = [];
              $( '.pwb-filter-products input[type="checkbox"]' ).each( ( function(o) {
                  $(this).prop("checked") && r.push( $(this).val() )
              } )),
              i = (r = r.join()) ? -1 === (i = (i = i.replace(/&?pwb-brand-filter=([^&]$|[^&]*)/i, "")).replace(/\/page\/\d*\//i, "")).indexOf("?") ? i + "?pwb-brand-filter=" + r : i + "&pwb-brand-filter=" + r : o;
            //   location.href = i
              categoryAjaxProcess( i );
            }
            // Perfect brands for woocommerce
            $( '.pwb-filter-products.pwb-hide-submit-btn input' ).off( 'change' ).on( 'change', function() {
                _brands();
            } );
            $( '.pwb-apply-filter' ).off( 'click' ).on( 'click', function() {
                _brands();
            });
            $( '.pwb-remove-filter' ).off( 'click' ).on( 'click', function() {                
                [location.protocol, "//", location.host, location.pathname].join("");
                var e = window.location.href;
                e = (e = e.replace(/&?pwb-brand-filter=([^&]$|[^&]*)/i, "")).replace(/\/page\/\d*\//i, ""),
                // location.href = e
                categoryAjaxProcess( e ); 
            });

        } else {
            $( document ).on( 'change', '.woocommerce-viewing select.count', function() {
                $( this ).closest( 'form' ).trigger( 'submit' );
            } );

        }
    };

    var ajaxFiltered = function( initLoad ) {
        var _prefix = '.archive-products ';
        if ( $( '.elementor-widget-wc-archive-products' ).length ) {
            _prefix = '.elementor-widget-wc-archive-products ';
        }

        if ( $( '.wcfmmp-store-page-wrap' ).length ) {
            _prefix = '.wcfmmp-store-page-wrap ';
        }

        var shop_before = '.shop-loop-before',
            shop_after = '.shop-loop-after',
            shop_container = _prefix + '.products',
            shop_wrap = '.archive-products',
            $shop_before = $( shop_before ),
            $shop_parent = $shop_before.parent(),
            $shop_toolbox = $( shop_before + ',' + shop_after ),
            $sticky_sidebar = $( '.sidebar [data-plugin-sticky]' );

        if ( $sticky_sidebar.get( 0 ) ) {
            $shop_parent.css( 'min-height', 0 );
        }

        if ( $shop_toolbox.length ) {
            $shop_toolbox.stop( true ).fadeTo( 400, 1 ).unblock();
        }

        if ( $( shop_container ).find( '.nothing-found-message' ).length || $( shop_wrap ).find( '.woocommerce-no-products-found' ).length ) {
            if ( typeof js_porto_vars != 'undefined' && js_porto_vars.shop_filter_layout == '' && ( ! theme.isTablet() || $( '.porto-product-filters-toggle' ).length == 0 ) ) {
                $shop_toolbox.hide().data( 'show', false );
            }
            if ( typeof js_porto_vars != 'undefined' && js_porto_vars.shop_filter_layout == 'horizontal' && theme.isTablet() && $( '.porto-product-filters-toggle.d-lg-none' ).length == 0 ) {
                $shop_toolbox.hide().data( 'show', false );
            }
            if ( typeof js_porto_vars != 'undefined' && js_porto_vars.shop_filter_layout == 'horizontal2' && $( '.porto-product-filters' ).length == 0 ) {
                $shop_toolbox.hide().data( 'show', false );
            }
        } else if ( $( shop_container ).find( '.product' ).length || $( shop_after ).closest( '.porto-products' ).length || $shop_before.hasClass( 'shop-builder' ) || $( shop_after ).closest( '.elementor-widget-wc-archive-products' ) || $( shop_after ).closest( '.wcfmmp-store-page-wrap' ) ) {
            $shop_toolbox.show().data( 'show', true );
        } else {
            $shop_toolbox.hide().data( 'show', false );
            if ( $shop_before.find( '.porto-product-filters.style2' ).length ) {
                $shop_before.show().data( 'show', true );
            }
        }

        refreshPriceSlider();
        var element = document.getElementById( 'porto_price_filter_chart' );
        if ( element && ! $( element ).hasClass( 'show-chart' ) ) {
            if ( typeof theme.price_chart == 'function' ) {
                theme.price_chart( element );
            } else {
                if ( js_porto_vars && js_porto_vars.func_url ) {
                    var wf = document.createElement('script'), s = document.scripts[document.scripts.length - 1];
                    wf.src = js_porto_vars.func_url + 'widgets/apexcharts/apexcharts.js';
                    wf.id = 'porto-price-filter-chart-js';
                    s.parentNode.insertBefore(wf, s);
                    // $( '#porto-price-filter-chart' ).on( 'load', function() {
                        // theme.price_chart( element );
                    // });
                }
            }
            
        }
        
        if ( typeof initLoad == 'undefined' || !initLoad ) {
            porto_init();
            porto_woocommerce_init();

            if ( $( '.sidebar [data-plugin-sticky]' ).length ) {
                //$shop_parent.css('min-height', $sticky_sidebar.height());
                theme.requestTimeout( function() {
                    theme.refreshStickySidebar( false );
                }, 200 );
            }
            $( '.page-wrapper' ).find( '.elementor-invisible' ).each( function() {
                var $animation_item = $( this ),
                    settings = $animation_item.data( 'settings' );
                if ( settings && ( settings._animation || settings.animation ) ) {
                    var animation = settings._animation || settings.animation,
                        delay = settings._animation_delay || settings.animation_delay || 0;
                    theme.requestTimeout( function() {
                        $animation_item.removeClass( 'elementor-invisible' ).addClass( 'animated ' + animation );
                    }, delay );
                }
            } );
        }

        $( '.woocommerce-ordering' ).off( 'change', 'select.orderby' ).on( 'change', 'select.orderby', function() {
            $( this ).closest( 'form' ).trigger( 'submit' );
        } );

        // category ajax

        categoryAjax();


    };

    // initialize woocommerce actions after skeleton loading
    var skeletonLoadingShopTrigger;
    $( '.skeleton-loading' ).on( 'skeleton-loaded', function() {
        if ( skeletonLoadingShopTrigger ) {
            theme.deleteTimeout( skeletonLoadingShopTrigger );
        }
        skeletonLoadingShopTrigger = theme.requestTimeout( function() {
            refreshPriceSlider();
        }, 100 );
    } );

    $( function() {
        var _prefix = '.archive-products ';
        if ( $( '.elementor-widget-wc-archive-products' ).length ) {
            _prefix = '.elementor-widget-wc-archive-products ';
        }
        if ( $( '.wcfmmp-store-page-wrap' ).length ) {
            _prefix = '.wcfmmp-store-page-wrap ';
        }
        // yith woo ajax filter events
        if ( typeof yith_wcan != 'undefined' ) {
            yith_wcan.container = _prefix + '.products';
            yith_wcan.pagination = '.shop-loop-before';
            yith_wcan.result_count = '.shop-loop-after';
        }

        if ( theme.category_ajax ) {
            $( document ).on( 'click', '.yith-wcan a', function( e ) {
                // add price filter loading
                var shop_before = '.shop-loop-before',
                    $shop_before = $( shop_before ),
                    shop_after = '.shop-loop-after',
                    shop_container = _prefix + '.products',
                    shop_info = _prefix + '.woocommerce-info',
                    //$shop_parent = $shop_before.parent(),
                    $sticky_sidebar = $( '.sidebar [data-plugin-sticky]' ),
                    show_toolbar = $shop_before.data( 'show' );
    
                if ( show_toolbar )
                    $( shop_before + ',' + shop_after ).stop( true ).show().fadeTo( 400, 0.8 ).block( { message: null, overlayCSS: { opacity: 0.2 } } );
                if ( $( shop_container ).length ) {
                    $( shop_container ).html( '' ).addClass( 'yith-wcan-loading' );
                    if ( !$( shop_container ).children( '.porto-loading-icon' ).length ) {
                        $( shop_container ).append( '<i class="porto-loading-icon"></i>' );
                    }
                } else {
                    $( shop_info ).html( '' ).addClass( 'yith-wcan-loading products' );
                    if ( !$( shop_info ).children( '.porto-loading-icon' ).length ) {
                        $( shop_info ).append( '<i class="porto-loading-icon"></i>' );
                    }
                }
    
                if ( $sticky_sidebar.get( 0 ) ) {
                    //$shop_parent.css('min-height', $sticky_sidebar.height());
                    theme.refreshStickySidebar( false );
                }
                $( '.yith-woo-ajax-navigation, .yith-wcan-list-price-filter' ).addClass( 'loading' );
                theme.scrolltoContainer( show_toolbar ? ( $shop_before.hasClass( 'sticky' ) && $shop_before.prev( '.filter-placeholder' ).length ? $shop_before.prev( '.filter-placeholder' ) : $shop_before ) : $( shop_container ) );
            } );
        } else {
            $( document ).off( 'click', '.yith-wcan a' );
        }

        $( document ).ready( function() {
            ajaxFiltered( true );
        } );

        $( document ).on( 'yith-wcan-ajax-filtered', function( e, res ) {
            ajaxFiltered();

            // infinite scroll after yith plugin filtered
            if ( res && $( 'html' ).hasClass( 'sidebar-opened' ) ) {
                $( '.sidebar-overlay' ).addClass( 'active' );
            }
            if ( res && typeof porto_infinite_scroll !== 'undefined' ) {
                var _prefix = '.archive-products ';
                if ( $( '.elementor-widget-wc-archive-products' ).length ) {
                    _prefix = '.elementor-widget-wc-archive-products ';
                }
                if ( $( '.wcfmmp-store-page-wrap' ).length ) {
                    _prefix = '.wcfmmp-store-page-wrap ';
                }
                var shop_container = _prefix + '.products:not(.is-shortcode)',
                    $shop_container = $( shop_container );
                if ( $shop_container.length ) {
                    var $parent = $shop_container.parent();
                    if ( $parent.hasClass( 'porto-posts-grid' ) ) {
                        if ( $parent.is( '.porto-ajax-load.load-infinite, .porto-ajax-load.load-more' ) ) {
                            $parent.portoInfiniteScroll();
                        }
                    } else if ( typeof theme.PostsInfinite !== 'undefined' ) {
                        new theme.PostsInfinite( $shop_container );
                    }
                }
            }
        } );

        //categoryAjax();

        // product filter ajax
        if ( theme.prdctfltr_ajax ) {
            // select count
            $( document ).on( 'change', '.woocommerce-viewing select.count', function() {
                $( this ).closest( 'form' ).trigger( 'submit' );
            } );
            // page number
            $( document ).on( 'click', '.woocommerce-pagination:not(.load-more) a.page-numbers', function( e ) {
                var $shop_before = $( '.shop-loop-before' );
                theme.scrolltoContainer( $shop_before.hasClass( 'sticky' ) && $shop_before.prev( '.filter-placeholder' ).length ? $shop_before.prev( '.filter-placeholder' ) : $shop_before );
            } );
        }

        // woocommerce grid / list
        $( document ).on( 'click', '.gridlist-toggle #grid, .gridlist-toggle #list', function( e ) {
            
            var $this = $( this );
            if ( $this.hasClass( 'active' ) ) {
                return false;
            }
            $( '.gridlist-toggle #grid, .gridlist-toggle #list' ).removeClass( 'active' );
            $this.addClass( 'active' );
            var viewMode = $this.attr( 'id' );
            if ( theme.category_ajax ) {
                e.preventDefault();
                var _prefix = '.archive-products';
                if ( $( '.elementor-widget-wc-archive-products' ).length ) {
                    _prefix = '.elementor-widget-wc-archive-products';
                }
                if ( $( '.wcfmmp-store-page-wrap' ).length ) {
                    _prefix = '.wcfmmp-store-page-wrap .products';
                }
                if ( js_porto_vars.use_skeleton_screen.indexOf( 'shop' ) != -1 ) {
                    $( _prefix + ' ul.products, ' + _prefix + ' .products-container' ).removeClass( 'grid' ).removeClass( 'list' ).addClass( viewMode );
                } else {
                    $( _prefix ).addClass( 'porto-ajax-loading' );
                }
                categoryAjaxProcess( $this.attr( 'href' ) ); 
            }
        } );

        if ( theme.category_ajax ) {
            window.addEventListener( 'popstate', function(e) {
                if ( e.state && e.state.isShopAjax ) {
                    let uniqueId = e.state.filterID;
                    let isCategoryFilter = e.state.isCategoryFilter;
                    if ( theme.shopAjaxCache && theme.shopAjaxCache[uniqueId] && theme.successfn) {
                        theme.successfn( theme.shopAjaxCache[uniqueId], uniqueId, isCategoryFilter, true );
                    } else {
                        location.reload();
                    }
                }
            } );
            window.history.replaceState( { pageTitle: '', isShopAjax: 1, filterID: 'first' }, "", location.href );
        }
    } );

} ).apply( this, [window.theme, jQuery] );