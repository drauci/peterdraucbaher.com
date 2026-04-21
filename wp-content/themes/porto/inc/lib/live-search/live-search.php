<?php
/**
 * Porto Live Search
 *
 * @author     Porto Themes
 * @category   Library
 * @since      4.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Live_Search' ) ) :

	class Porto_Live_Search {

		public function __construct() {
			global $porto_settings;

			if ( ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) || ! isset( $porto_settings['search-live'] ) || ! $porto_settings['search-live'] ) {
				return;
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'add_script' ) );
			add_action( 'wp_ajax_porto_ajax_search_posts', array( $this, 'ajax_search' ) );
			add_action( 'wp_ajax_nopriv_porto_ajax_search_posts', array( $this, 'ajax_search' ) );

			$is_searchpage = ! empty( $_REQUEST['s'] ) && ( ! empty( $_REQUEST['post_type'] ) && 'product' == $_REQUEST['post_type'] ) && ! is_admin() && ! wp_doing_ajax();
			if ( ! defined( 'YITH_WCAS_PREMIUM' ) ) {
				if ( ! empty( $porto_settings['search-live'] ) && ! empty( $porto_settings['search-by'] ) && in_array( 'sku', $porto_settings['search-by'] ) && $is_searchpage && is_main_query() ) {
					if ( ! isset( $_REQUEST['yith_pos_request'] ) || 'search-products' != $_REQUEST['yith_pos_request'] ) {
						add_filter( 'posts_search', array( $this, 'search_query_by_sku' ), 9 );
					}
				}
			} else {
				add_filter( 'yith_ajax_search_use_and_for_sku', '__return_false' );
			}

			if ( ! empty( $porto_settings['search-live'] ) && ! empty( $porto_settings['search-by'] ) && in_array( 'product_tag', $porto_settings['search-by'] ) && $is_searchpage && is_main_query() ) {
				add_filter( 'posts_search', array( $this, 'search_query_by_product_tag' ), 10 );
			}
		}

		public function add_script() {
			if ( porto_is_amp_endpoint() ) {
				return;
			}
			wp_enqueue_script( 'porto-live-search', PORTO_LIB_URI . '/live-search/live-search.min.js', array( 'jquery-core' ), PORTO_VERSION, true );
			wp_localize_script(
				'porto-live-search',
				'porto_live_search',
				array(
					'nonce'        => wp_create_nonce( 'porto-live-search-nonce' ),
					'sku'          => __( 'SKU: ', 'porto' ),
					'product_tags' => __( 'Tags: ', 'porto' ),
				)
			);
		}

		/**
		 * Filter the Unique Objects in a PHP object array
		 * How to use: returnUniqueProperty($names, 'name');
		 * 
		 * @since 7.0.0
		 */
		public function return_unique_property( $array, $property = '' ) {
			if ( ! $property ) {
				$more_unique_array = array_unique( $array );
			} else {
				$temp_array = array_unique( array_column( $array, $property ) );
				$more_unique_array = array_values( array_intersect_key( $array, $temp_array ) );
			}
			return $more_unique_array;
		}

		public function ajax_search() {
			check_ajax_referer( 'porto-live-search-nonce', 'nonce' );
			global $porto_settings;

			$query  = apply_filters( 'porto_ajax_search_query', sanitize_text_field( $_REQUEST['query'] ) );
			$posts  = array();
			$result = array();
			$args   = array(
				's'                   => $query,
				'orderby'             => '',
				'post_status'         => 'publish',
				'posts_per_page'      => apply_filters( 'porto_ajax_search_query_limit', 50 ),
				'ignore_sticky_posts' => 1,
				'post_password'       => '',
				'suppress_filters'    => false,
				'fields'              => 'ids',
			);

			if ( ! isset( $_REQUEST['post_type'] ) || empty( $_REQUEST['post_type'] ) || 'product' == $_REQUEST['post_type'] ) {
				if ( class_exists( 'Woocommerce' ) ) {
					$search_by = ! empty( $porto_settings['search-by'] ) ? $porto_settings['search-by'] : array();
					if ( in_array( 'sku', $search_by ) ) {
						add_filter( 'posts_search', array( $this, 'search_query_by_sku' ) );
					}
					$posts = $this->search_products( 'product', $args );
					if ( in_array( 'sku', $search_by ) ) {
						remove_filter( 'posts_search', array( $this, 'search_query_by_sku' ) );
					}

					if ( in_array( 'product_tag', $search_by ) ) {
						$posts = array_merge( $posts, $this->search_products( 'tag', $args ) );
						if ( ! empty( $posts ) ) {
							if ( is_array( $posts ) && isset( $posts[0]->ID ) ) {
								$posts = $this->return_unique_property( $posts, 'ID' );
							} else {
								$posts = $this->return_unique_property( $posts );
							}
						}
					}

				}
				if ( ! isset( $_REQUEST['post_type'] ) || empty( $_REQUEST['post_type'] ) ) {
					$posts = array_merge( $posts, $this->search_posts( $args, $query ) );
				}
			} else {
				$posts = $this->search_posts( $args, $query, array( sanitize_text_field( $_REQUEST['post_type'] ) ) );
			}

			foreach ( $posts as $post ) {
				$post = get_post( $post );
				if ( class_exists( 'Woocommerce' ) && ( 'product' === $post->post_type || 'product_variation' === $post->post_type ) ) {
					$product       = wc_get_product( $post );
					$product_image = wp_get_attachment_image_src( $product->get_image_id() );
					if ( $product->is_visible() ) {
						if ( ! empty( $porto_settings['search-by'] ) && in_array( 'product_tag', $porto_settings['search-by'] ) ) {
							$tag_name = get_the_terms( $product->get_id(), 'product_tag' );
							$tag_arr  = [];
							if ( ! empty( $tag_name ) && is_array( $tag_name ) ) {
								foreach ( $tag_name as $tag ) {
									$tag_arr[] = $tag->name;
								}
							}
							$tag_str = implode( ', ', $tag_arr );
						}
						$result[] = array(
							'type'  => 'Product',
							'id'    => $product->get_id(),
							'value' => $product->get_title(),
							'url'   => esc_url( $product->get_permalink() ),
							'img'   => $product_image && isset( $product_image[0] ) ? esc_url( $product_image[0] ) : '',
							'price' => $product->get_price_html(),
							'sku'   => ! empty( $product->get_sku() ) ? $product->get_sku() : '',
							'tag'   => ! empty( $tag_str ) ? $tag_str : '',
						);
					}
				} else {
					$result[] = array(
						'type'  => esc_html( $post->post_type ),
						'id'    => (int) $post->ID,
						'value' => $post->post_title,
						'url'   => esc_url( get_the_permalink( $post->ID ) ),
						'img'   => esc_url( get_the_post_thumbnail_url( $post->ID, 'thumbnail' ) ),
						'price' => '',
					);
				}
			}
			wp_send_json( array( 'suggestions' => $result ) );
		}

		private function search_posts( $args, $query, $post_type = array( 'post', 'page', 'portfolio', 'event' ) ) {
			$args['s']         = $query;
			$args['post_type'] = apply_filters( 'porto_ajax_search_post_type', $post_type );
			$args              = apply_filters( 'porto_live_search_query_args', $this->search_add_category_args( $args ), 'posts' );

			$search_query   = http_build_query( $args );
			$search_funtion = apply_filters( 'porto_ajax_search_function', 'get_posts', $search_query, $args );

			$cached_data = porto_cache_get( $args );
			if ( false === $cached_data ) {
				$cached_data = ( 'get_posts' === $search_funtion || ! function_exists( $search_funtion ) ? get_posts( $args ) : $search_funtion( $search_query, $args ) );
				porto_cache_set( $args, $cached_data );
			}

			return $cached_data;
		}

		private function search_products( $search_type, $args ) {
			$args['post_type']  = 'product';
			//$args['meta_query'] = WC()->query->get_meta_query(); // WPCS: slow query ok.
			$args               = $this->search_add_category_args( $args );

			if ( ! isset( $args['tax_query'] ) ) {
				$args['tax_query'] = array();
			}
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			if ( ! empty( $product_visibility_term_ids ) ) {
				$args['tax_query']['relation'] = 'AND';
				$args['tax_query'][]           = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['exclude-from-search'],
					'operator' => 'NOT IN',
				);
			}

			/*if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				if ( ! isset( $args['meta_query'] ) ) {
					$args['meta_query'] = array();
				}
				$args['meta_query'][] = array( 'key' => '_stock_status', 'value' => 'outofstock', 'compare' => 'NOT IN' );
			}*/

			switch ( $search_type ) {
				case 'product':
					$args['s'] = apply_filters( 'porto_ajax_search_products_query', sanitize_text_field( $_REQUEST['query'] ) );
					break;
				case 'tag':
					$args['s']           = '';
					$args['product_tag'] = apply_filters( 'porto_ajax_search_products_by_tag_query', sanitize_text_field( $_REQUEST['query'] ) );
					break;
			}

			$args           = apply_filters( 'porto_live_search_query_args', $args, $search_type );
			$search_query   = http_build_query( $args );
			$search_funtion = apply_filters( 'porto_ajax_search_function', 'get_posts', $search_query, $args );
			$transient_name = porto_cache_generate_key( $args );
			$cached_data    = porto_cache_get( $args, $transient_name );
			if ( false === $cached_data ) {
				$transient_version = Porto_Cache::get_transient_version( 'query_product' );
				$chart_transient   = get_transient( $transient_name );

				if ( isset( $chart_transient['value'], $chart_transient['version'] ) && $chart_transient['version'] === $transient_version ) {
					$cached_data = $chart_transient['value'];
				} else {
					$cached_data     = 'get_posts' === $search_funtion || ! function_exists( $search_funtion ) ? get_posts( $args ) : $search_funtion( $search_query, $args );
					$transient_value = array(
						'version' => $transient_version,
						'value'   => $cached_data,
					);
					set_transient( $transient_name, $transient_value, 4 * DAY_IN_SECONDS );
				}
				porto_cache_set( $args, $cached_data, $transient_name );
			}

			return $cached_data;
		}

		private function search_add_category_args( $args ) {
			global $porto_settings;
			if ( isset( $_REQUEST['cat'] ) && $_REQUEST['cat'] && '0' != $_REQUEST['cat'] ) {
				if ( 'product' == $porto_settings['search-type'] ) {
					$args['tax_query']   = array();
					$args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => sanitize_text_field( $_REQUEST['cat'] ),
					);
				} elseif ( 'post' == $porto_settings['search-type'] ) {
					$args['category_name'] = sanitize_text_field( $_REQUEST['cat'] );
				} elseif ( 'portfolio' == $porto_settings['search-type'] ) {
					$args['tax_query']   = array();
					$args['tax_query'][] = array(
						'taxonomy' => 'portfolio_cat',
						'field'    => 'slug',
						'terms'    => sanitize_text_field( $_REQUEST['cat'] ),
					);
				}
			}
			return $args;
		}

		/**
		 * Search query by sku
		 * 
		 * @since 7.2.9
		 */
		public function search_query_by_sku( $where ) {
			$s = '';
			if ( wp_doing_ajax() ) {
				if ( ! empty( $_REQUEST['query'] ) ) {
					$s = sanitize_text_field( $_REQUEST['query'] );
				}
			} else {
				global $wp;
				if ( isset( $wp->query_vars['s'] ) ) {
					$s = $wp->query_vars['s'];
				}
			}
			if ( ! $s ) {
				return $where;
			}

			global $wpdb;

			$product_ids = array();
			$skus        = explode( ',', $s );

			foreach ( $skus as $sku ) {
				$sku           = wc_clean( trim( $sku ) );
				$parent_ids    = $wpdb->get_col( $wpdb->prepare( "SELECT p.post_parent FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->wc_product_meta_lookup} lookup ON p.ID = lookup.product_id AND ( lookup.sku LIKE '%%%s%%' OR lookup.global_unique_id LIKE '%%%s%%' ) where p.post_parent != 0 GROUP BY p.post_parent", $sku, $sku ) );
				$child_ids_arr = $wpdb->get_results( $wpdb->prepare( "SELECT product_id FROM {$wpdb->wc_product_meta_lookup} WHERE sku LIKE '%%%s%%' OR global_unique_id LIKE '%%%s%%'", $sku, $sku ), ARRAY_N );

				$child_ids = array();
				if ( is_array( $child_ids_arr ) ) {
					foreach ( $child_ids_arr as $id_arr ) {
						$child_ids[] = $id_arr[0];
					}
				}

				$product_ids = array_merge( $product_ids, $child_ids, $parent_ids );
			}

			if ( ! empty( $product_ids ) ) {
				$product_ids = array_map( 'absint', $product_ids );
				$where       = str_replace( ')))', ")) OR ( {$wpdb->posts}.ID IN (" . implode( ',', $product_ids ) . ")))", $where );
			}

			return $where;
		}

		/**
		 * Search query by product tag
		 * 
		 * @since 7.7.0
		 */
		public function search_query_by_product_tag( $where ) {
			$s = '';
			if ( ! wp_doing_ajax() ) { // only search page
				global $wp;
				if ( isset( $wp->query_vars['s'] ) ) {
					$s = $wp->query_vars['s'];
				}
			}
			if ( ! $s ) {
				return $where;
			}

			global $wpdb;
			$product_ids = array();
			$pd_arr      = $wpdb->get_results( $wpdb->prepare( "SELECT p.id 
				FROM {$wpdb->posts} AS p 
				JOIN {$wpdb->prefix}term_relationships AS tr ON p.id = tr.object_id
				JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE p.post_type = 'product' AND tt.taxonomy = 'product_tag' AND tt.term_id IN (
					SELECT term_id
					FROM {$wpdb->prefix}terms
					WHERE name = '{$s}'
				)
				LIMIT 10" ), ARRAY_N );

			if ( ! empty( $pd_arr ) && is_array( $pd_arr ) ) {
				foreach ( $pd_arr as $id_arr ) {
					$product_ids[] = $id_arr[0];
				}
			}

			if ( ! empty( $product_ids ) ) {
				$product_ids = array_map( 'absint', $product_ids );
				$where       = str_replace( ')))', ")) OR ( {$wpdb->posts}.ID IN (" . implode( ',', $product_ids ) . ")))", $where );
			}
			return $where;
		}
	}
	new Porto_Live_Search;
endif;
