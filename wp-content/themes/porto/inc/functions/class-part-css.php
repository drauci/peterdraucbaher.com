<?php
/**
 * Load part css
 *
 * @author     Porto Themes
 * @since      7.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Porto_Part_Css' ) ) {
    class Porto_Part_Css {
        /**
         * Global Instance Objects
         *
         * @var array $instances
         * @since 7.5.0
         * @access private
         */
        private static $instance = null;

        /**
         * Style Lists.
         *
         * @var array
         */
        private $style_queue = array();

        public static function get_instance() {
            if ( ! self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }


        /**
         * Enqueue inline style by key.
         *
         * @param string $key File slug.
         */
        public function enqueue_link_style( $handle, $src = '', $ver = PORTO_VERSION, $media = 'all' ) {
                
            if ( is_array( $this->style_queue ) && in_array( $handle, $this->style_queue ) ) { // phpcs:ignore
                return false;
            }
            $this->style_queue[] = $handle;

            ?>
            <link rel="stylesheet" id="<?php echo esc_attr( $handle ); ?>-css" href="<?php echo esc_attr( $src ); ?>?ver=<?php echo esc_attr( $ver ); ?>" type="text/css" media="<?php echo esc_attr( $media ); ?>" /> <?php // phpcs:ignore ?>
            <?php
        }
    }
}

Porto_Part_Css::get_instance();

if ( ! function_exists( 'porto_enqueue_link_style' ) ) {
	/**
	 * Enqueue inline style by key.
	 *
	 * @param string $key File slug.
	 */
	function porto_enqueue_link_style( $handle, $src = '', $ver = PORTO_VERSION, $media = 'all' ) {
		if ( function_exists( 'wc' ) && ( wc()->is_rest_api_request() ) || wp_is_serving_rest_request() ) {
			return;
		}

		Porto_Part_Css::get_instance()->enqueue_link_style( $handle, $src, $ver, $media );
	}
}
