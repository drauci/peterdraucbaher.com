<?php
/**
 * GeneratePress child theme functions and definitions.
 */
add_filter( 'generate_copyright', 'do_shortcode', 20 );
add_action( 'generate_after_header', function() {
    // VAROVALO: Slider se prikaže samo na prvi strani (Home)
    if ( ! is_front_page() ) {
        return;
    }
    ?>
        <div class="site-content">
            <?php
            if ( function_exists('pll_current_language') ) {
                $lang = pll_current_language();
                if ( $lang == 'sl' ) {
                    echo do_shortcode( '[metaslider id="1451"]' );
                } elseif ( $lang == 'en' ) {
                    echo do_shortcode( '[metaslider id="1459"]' );
                }
            } else {
                echo do_shortcode( '[metaslider id="1451"]' );
            }
            ?>
        </div>
    <?php
} );

function amnesty_social_links_shortcode() {
    $napis = 'SLEDITE MI:';
    if ( function_exists('pll_current_language') ) {
        $lang = pll_current_language();
        if ( $lang == 'en' ) { $napis = 'FOLLOW ME ON:'; }
    }

    $html = '
    <div class="amnesty-footer-social">
        <span class="amnesty-social-label">' . $napis . '</span>
        <ul class="amnesty-social-list">
            <!-- Facebook Oglat -->
            <li class="amnesty-social-item">
                <a href="https://www.facebook.com/profile.php?id=61576215297196" target="_blank" rel="nofollow">
                    <svg viewBox="0 0 24 24" width="24" height="24"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7v-7h-2v-3h2V8.5A3.5 3.5 0 0 1 15.5 5H18v3h-2.5a1 1 0 0 0-1 1V11h3.5l-.5 3h-3v7h5a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"></path></svg>
                </a>
            </li>
            <!-- Instagram (iz slike cilj.png) -->
            <li class="amnesty-social-item">
                <a href="https://www.instagram.com/peter.draucbaher/" target="_blank" rel="nofollow">
                    <svg viewBox="0 0 24 24" width="24" height="24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path></svg>
                </a>
            </li>
            <!-- YouTube (iz slike cilj.png) -->
            <li class="amnesty-social-item">
                <a href="https://www.youtube.com/@PeterDraucbaher" target="_blank" rel="nofollow">
                    <svg viewBox="0 0 24 24" width="24" height="24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"></path></svg>
                </a>
            </li>
        </ul>
    </div>';

    return $html;
}
add_shortcode('amnesty_socials', 'amnesty_social_links_shortcode');


add_action( 'wp_footer', function() {
    ?>
    <a href="#" id="custom-back-to-top" title="Skok na vrh">
        <svg viewBox="0 0 330 512" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
            <path d="M305.863 314.916c0 2.266-1.133 4.815-2.832 6.514l-14.157 14.163c-1.699 1.7-3.964 2.832-6.513 2.832-2.265 0-4.813-1.133-6.512-2.832L164.572 224.276 53.295 335.593c-1.699 1.7-4.247 2.832-6.512 2.832-2.265 0-4.814-1.133-6.513-2.832L26.113 321.43c-1.699-1.7-2.831-4.248-2.831-6.514s1.132-4.816 2.831-6.515L158.06 176.408c1.699-1.7 4.247-2.833 6.512-2.833 2.265 0 4.814 1.133 6.513 2.833L303.03 308.4c1.7 1.7 2.832 4.249 2.832 6.515z"></path>
        </svg>
    </a>
    <?php
});

add_action( 'wp_footer', function() {
    ?>
    <script>
        document.getElementById("custom-back-to-top").addEventListener("click", function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
    <?php
}, 20);


/**
 * Skrije naslov na vseh WordPress straneh, razen v menijih.
 */
function skrij_naslove_strani( $title, $id = null ) {
    // Preverimo, če smo v "zanki" (the loop), če gre za stran in če nismo v administrativnem delu
    if ( is_page( $id ) && in_the_loop() && !is_admin() ) {
        return '';
    }

    return $title;
}
add_filter( 'the_title', 'skrij_naslove_strani', 10, 2 );

add_filter( 'generate_copyright', 'moj_lastni_footer_copyright' );

function moj_lastni_footer_copyright( $copyright ) {
    $copyright = sprintf(
        '<span class="copyright">&copy; %1$s %2$s</span>  %4$s <a href="%3$s"%6$s>%5$s</a>',
        date( 'Y' ),
        get_bloginfo( 'name' ),
        '', // Tukaj spremeni povezavo
        '',               // Tvoj poljuben tekst
        '',                 // Tvoje ime ali podjetje
        ''
    );

    return $copyright;
}
add_action( 'wp_before_admin_bar_render', 'force_remove_dwuos_notice', 999 );
function force_remove_dwuos_notice() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node( 'wp-admin-bar-dwuos-notice' );
}

add_action('admin_head', 'hide_dwuos_with_css'); // Za admin vmesnik
add_action('wp_head', 'hide_dwuos_with_css');    // Za javni del strani (front-end)
function hide_dwuos_with_css() {
    echo '<style>
        #wp-admin-bar-dwuos-notice { display: none !important; }
    </style>';
}

add_action('wp_head', function() {
    ob_start(function($html) {
        return preg_replace('/<div class="site-branding">.*?<\/div>/s', '', $html);
    });
});
add_action('wp_footer', function() {
    if (ob_get_level() > 0) ob_end_flush();
});

add_action('after_setup_theme', function() {
    load_child_theme_textdomain('generatepress_peterdraucbahercom', get_stylesheet_directory() . '/languages');
});

add_filter( 'generate_footer_entry_meta_items', function( $items ) {
    // Če smo na seznamu objav (blog) ali v arhivu (kategorije, oznake)
    if ( is_home() || is_archive() ) {
        // Odstrani 'comments-link' iz seznama meta podatkov v nogi objave
        return array_diff( $items, array( 'comments-link' ) );
    }

    return $items;
} );

add_filter( 'generate_header_entry_meta_items', function( $items ) {
    // Enako preverjanje še za glavo objave (če bi tvoja tema tam kazala komentarje)
    if ( is_home() || is_archive() ) {
        return array_diff( $items, array( 'comments-link' ) );
    }

    return $items;
} );


