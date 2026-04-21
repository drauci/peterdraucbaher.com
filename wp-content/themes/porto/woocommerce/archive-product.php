<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * @version     8.6.0
 */

defined( 'ABSPATH' ) || exit;

$load_posts_only    = porto_is_ajax() && isset( $_REQUEST['load_posts_only'] );
$is_category_filter = porto_is_ajax() && ! empty( $_REQUEST['is_category_filter'] ) && 'true' == $_REQUEST['is_category_filter'];
if ( ! $load_posts_only || $is_category_filter ) {
	get_header( 'shop' );
}

?>

<?php wc_get_template_part( 'archive-product-content' ); ?>

<?php
if ( ! $load_posts_only || $is_category_filter ) {
	get_footer( 'shop' );
}
