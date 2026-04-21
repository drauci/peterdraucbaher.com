( function( $ ) {
    // Overlay Menu
    if ( $( '.porto-popup-menu' ).length ) {
        $( '.porto-popup-menu .hamburguer-btn' ).on( 'click', function( e ) {
            e.preventDefault();
            var $this = $( this ), $html = $( 'html' );
            if ( $( '.porto-popup-menu-spacer' ).length ) {
                $( '.porto-popup-menu-spacer' ).remove();
            } else {
                $( '<div class="porto-popup-menu-spacer"></div>' ).insertBefore( $this.parent() );
                $( '.porto-popup-menu-spacer' ).width( $this.parent().width() );
            }
            if ( $this.parent().hasClass( 'opened' ) ) {
                $html.css( 'overflow', '' );
                $html.css( 'margin-right', '' );
            } else {
                $html.css( 'margin-right', theme.getScrollbarWidth() );
                $html.css( 'overflow', 'hidden' );
            }
            $this.parent().toggleClass( 'opened' );
            theme.requestFrame( function() {
                $this.toggleClass( 'active' );
            } );
        } );
        $( '.porto-popup-menu .main-menu' ).scrollbar();
        $( '.porto-popup-menu' ).on( 'click', 'li.menu-item-has-children > a', function( e ) {
            e.preventDefault();
            $( this ).parent().siblings( 'li.menu-item-has-children.opened' ).removeClass( 'opened' );
            $( this ).parent().toggleClass( 'opened' );
        } );
    }
    

} )( window.jQuery );