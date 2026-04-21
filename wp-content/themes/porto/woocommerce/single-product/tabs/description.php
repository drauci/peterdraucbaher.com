<?php
/**
 * Description tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

global $post;

$heading = apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) );

?>

<?php if ( $heading ) : ?>
	<h2><?php echo esc_html( $heading ); ?></h2>
<?php endif; ?>

<?php 
	if (  ! empty( $post->ID ) && defined( 'ELEMENTOR_VERSION' ) &&  Elementor\Plugin::$instance->documents->get( $post->ID )->is_built_with_elementor() && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) {

		$editor       =  Elementor\Plugin::$instance->editor;
		$is_edit_mode = $editor->is_edit_mode();
		$editor->set_edit_mode( false );
		$ret          = apply_filters( 'the_content',  Elementor\Plugin::$instance->frontend->get_builder_content( $post->ID, true ) );
		$editor->set_edit_mode( $is_edit_mode );
		echo balanceTags( $ret, true );
		
	} else {
		the_content(); 
	}
?>
