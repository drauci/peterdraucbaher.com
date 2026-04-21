theme = theme || {};
jQuery( document ).ready( function( $ ) {
    if ( js_porto_vars.user_edit_pages ) {
        var porto_init_builder_tooltip = function( obj ) {
            var tooltipTriggerList = [].slice.call( obj.querySelectorAll( '.pb-edit-link' ) );
            tooltipTriggerList.map( function( o ) {
                var title = o.getAttribute( 'data-title' );
                if ( o.nextSibling && o.nextSibling.classList.contains( 'porto-block' ) ) {
                    var tooltipobj = o.nextSibling;
                    tooltipobj.classList.add( 'has-pb-edit' );
                    var $config = {
                        html: true,
                        template: '<div class="tooltip porto-tooltip-wrap" role="tooltip"><div class="tooltip-inner"></div></div>',
                        trigger: 'manual',
                        title: '<a href="' + o.getAttribute( 'data-link' ) + '"><i class="porto-icon-edit me-1"></i><span>' + title + '</span></a>' + ( o.getAttribute( 'data-tracking-url' ) ? ( '<a href="' + o.getAttribute( 'data-tracking-url' ) + '"><i class="porto-icon-edit me-1"></i>' + o.getAttribute( 'data-tracking-title' ) + '</a>' ) : '' ),
                        delay: 300
                    }
                    if ( o.getAttribute('data-builder-id') ) {
                        $config['container'] = '[data-id="' + o.getAttribute('data-builder-id') + '"]';
                    }
                    var tooltip_ins = new bootstrap.Tooltip( tooltipobj, $config );
                    if ( tooltip_ins && tooltip_ins._element ) {
                        if ( o.getAttribute('data-builder-type') && ( 'block' != o.getAttribute('data-builder-type') || 0 != $( tooltipobj ).height() ) ) {
                            setTimeout( function(){
                                tooltip_ins.show();
                            }, 1000);
                        } else {
                            tooltip_ins._element.addEventListener( 'mouseenter', function( e ) {
                                tooltip_ins._enter( e, tooltip_ins );
                            } );
                            tooltip_ins._element.addEventListener( 'mouseleave', function( e ) {
                                tooltip_ins._leave( e, tooltip_ins );
                            } );
                        }
                    }
                }

                o.parentNode.removeChild( o );
            } );
        };
        porto_init_builder_tooltip( document.body );
        $( '.skeleton-loading' ).on( 'skeleton-loaded', function() {
            porto_init_builder_tooltip( this );
        } );
        $( document.body ).on( 'mouseenter mouseleave', '.porto-tooltip-wrap[role="tooltip"]', function( e ) {
            var $element = $( '.porto-block[aria-describedby="' + $( this ).attr( 'id' ) + '"]' );
            if ( $element.length && $( this ).parent( 'body' ).length ) {
                var ins = bootstrap.Tooltip.getInstance( $element.get( 0 ) );
                if ( ins ) {
                    var fn_name = 'mouseenter' == e.type ? '_enter' : '_leave';
                    ins[fn_name]( e, ins );
                }
            }
        } ).on( 'porto_init_start', function( e, wrapObj ) {
            // init edit tooltip
            if ( wrapObj.classList.contains( 'porto-posts-grid' ) ) {
                porto_init_builder_tooltip( wrapObj );
            }
        } );
        $( window ).on( 'porto_posts_updated', function() {
            porto_init_builder_tooltip( document.body );
        } );
        theme.porto_init_builder_tooltip = porto_init_builder_tooltip;
    }
});