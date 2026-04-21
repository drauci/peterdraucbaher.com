<?php

/**
 * Porto Patcher
 * 
 * @since 6.7.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PORTO_PATCHER', PORTO_ADMIN . '/patcher' );

if ( defined( 'PORTO_FUNC_VERSION' ) ) {
	if ( ! class_exists( 'Porto_Patcher' ) ) {
		class Porto_Patcher {

			/**
			 * The transient name.
			 *
			 * @since 6.7.0
			 */
			private static $transient_name = 'porto_patch_transient';

			/**
			 * Global Instance Objects
			 *
			 * @var array $instances
			 * @since 6.7.0
			 * @access private
			 */
			private static $instance = null;

			/**
			 * Patches for the database
			 * 
			 * @since 6.7.0
			 */
			public $patches = array();

			/**
			 * Patches for applied
			 * 
			 * @since 6.7.0
			 */
			public $patched_data = array();

			public static function get_instance() {
				if ( ! self::$instance ) {
					self::$instance = new self();
				}
				return self::$instance;
			}

			public function __construct() {
				add_action( 'admin_menu', array( $this, 'admin_menu' ), 11 );
				// apply patches
				add_action('wp_ajax_porto_apply_patches', array( $this, 'apply_patches' ) );
			}

			/**
			 * Add Patcher submenu
			 *
			 * @since 6.7.0
			 */
			public function admin_menu() {
				if ( Porto()->is_registered() ) {
					$title =  __( 'Patcher', 'porto' );
					if ( $this->check_patches() ) {
						$title = sprintf( __( 'Patcher %1$sNew%2$s', 'porto' ), '<span class="update-plugins">', '</span>' );
					}
					add_submenu_page( 'porto', __( 'Patcher', 'porto' ), $title, 'administrator', 'porto-patcher', array( $this, 'page_patcher' ) );
				}
			}

			/**
			 * Show the patch table
			 * 
			 * @since 6.7.0
			 */
			public function page_patcher() {
				if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || 'porto-patcher' != $_GET['page'] ) {
					return;
				}
				// enqueue assest
				wp_enqueue_style( 'porto-setup-wizard', PORTO_URI . '/inc/admin/setup_wizard/assets/css/style.css', array( 'porto_admin' ), PORTO_VERSION );
				wp_enqueue_style( 'porto-patcher', PORTO_URI . '/inc/admin/patcher/patcher.css', array( 'porto_admin' ), PORTO_VERSION );
				wp_enqueue_script( 'porto-patcher', PORTO_URI . '/inc/admin/patcher/patcher.js', array( 'porto-admin' ), PORTO_VERSION, true );
				
				$reach_success = true;
				if ( ( isset( $_GET['action'] ) && 'refresh' == $_GET['action'] ) || ! $this->check_transients() ) {
					require_once PORTO_PLUGINS . '/importer/importer-api.php';
					$importer_api = new Porto_Importer_API();
					$reach_success = $importer_api->get_patch_files();
					$this->patches = $reach_success;
					$this->set_transient();
				} else {
					$this->get_transient();
				}
				if ( ! $reach_success ) {
					$atts = false;
				} else {
					$this->get_filter_patches();
					$atts = $this->patched_data;
				}
				include PORTO_PATCHER . '/patches-template.php';
				Porto()->view_footer();
			}

			public function check_patches() {
				$data_patches = get_site_option( self::$transient_name );
				$server_patches = $this->check_transients();
				if ( !$server_patches ) {
					require_once PORTO_PLUGINS . '/importer/importer-api.php';
					$importer_api = new Porto_Importer_API();
					$server_patches = $importer_api->get_patch_files();
					$this->patches = $server_patches;
					$this->set_transient();
				}
				// database and patches are empty
				if ( empty( $server_patches['update'] ) && empty( $server_patches['delete'] ) ) {
					return false;
				}
				// patched files are the same as server patches
				if ( ! empty( $data_patches ) && ! empty( $server_patches ) && ( $server_patches == $data_patches ) ) {
					return false;
				}
				return true;
			}

			/**
			 * Set & Update the transient
			 *
			 * @since 6.7.0
			 */
			public function set_transient() {
				if ( ! empty ( $this->patches ) ) {
					// How often to check for updates
					set_site_transient( self::$transient_name, $this->patches, DAY_IN_SECONDS );
				} else {
					// Patching is failed
					set_site_transient( self::$transient_name, 'failed', DAY_IN_SECONDS );
				}
			}

			/**
			 * Get the transient
			 *
			 * @since 6.7.0
			 */
			public function get_transient() {
				$this->patches = get_site_transient( self::$transient_name );
			}

			/**
			 * Get the applied patches from database
			 *
			 * @since 6.7.0
			 */
			public function get_applied_patches() {
				// Get patche files from database
				$data = get_site_option( self::$transient_name );
				if ( ! empty( $data ) ) {
					if ( ( PORTO_VERSION != $data['theme_version'] ) || ( PORTO_FUNC_VERSION != $data['func_version'] ) ) {
						delete_site_option( self::$transient_name );
						$data = array();
					}
				} else {
					$data = array();
				}
				return $data;
			}

			/**
			 * Get the new patches
			 *
			 * @since 6.7.0
			 */
			public function get_filter_patches() {
				
				$legacy_patches = $this->get_applied_patches();
				if ( ! empty( $this->patches ) ) {
					$this->patched_data = $this->patches;
					if ( is_array( $this->patches ) ) {
						foreach ( $this->patches as $key => $patches ) {
							if ( 'update' == $key ) {
								$legacy_updated_patches = ! empty( $legacy_patches['update'] ) ? $legacy_patches['update'] : array();
								foreach ( $patches as $file_path => $value ) {
									if ( ! empty( $legacy_updated_patches[ $file_path ] ) ) {
										$patch =  $legacy_updated_patches[ $file_path ];
										if ( $patch['patch_version'] == $value['patch_version'] ) {
											unset( $this->patched_data['update'][ $file_path ] );
										}
									}
								}
							} else if ( ( 'delete' == $key ) && ! empty( $patches ) ) {
								$delete_files = ! empty( $legacy_patches['delete'] ) ? $legacy_patches['delete'] : array();
								foreach ( $delete_files as $path => $target ) {
									if ( array_key_exists( $path, $this->patches['delete'] ) ) {
										unset( $this->patched_data['delete'][ $path ] );
									}
								}
							}
						}
					}
				}
			}

			/**
			 * Transient is existed, return transient
			 * 
			 * @since 6.7.0
			 */
			public function check_transients() {
				$legacy_transient = get_site_transient( self::$transient_name );
				if ( ! empty( $legacy_transient ) ) {
					if ( is_string( $legacy_transient ) ) {
						return true;
					} elseif ( ( PORTO_VERSION == $legacy_transient['theme_version'] ) && ( PORTO_FUNC_VERSION == $legacy_transient['func_version'] ) ) {
						return $legacy_transient;
					} elseif ( PORTO_VERSION != $legacy_transient['theme_version'] ) {
						$this->reset_saved_patches( 'theme' );
						return false;
					} elseif ( PORTO_FUNC_VERSION != $legacy_transient['func_version'] ) {
						$this->reset_saved_patches( 'functionality' );
						return false;
					} else {
						// Version updated
						$this->reset_saved_patches();
						return false;
					}
				}
				return false;
			}

			/**
			 * Clear the transient
			 *
			 * @since 6.7.0
			 */
			public function reset_saved_patches( $remove_location = '' ) {
				
				// delete transient
				delete_site_transient( self::$transient_name );
				
				if ( '' == $remove_location ) {
					// delete patched log in database
					if ( get_site_option( self::$transient_name ) ) {
						delete_site_option( self::$transient_name );
					}
				} elseif( 'theme' == $remove_location || 'functionality' == $remove_location ) {
					
					// Remove database only for changed version
					$patched_files = get_site_option( self::$transient_name );
					if ( $patched_files ) {
						foreach ( $patched_files as $key => $patches ) {
							if ( 'update' == $key ) {
								foreach ( $patches as $file_path => $value ) {
									if ( $remove_location == $value['target'] ) {
										unset($patched_files['update'][$file_path]);
									}
								}
							} else if ( ( 'delete' == $key ) && !empty( $patches ) ) {
								foreach ( $patches as $path => $target ) {
									if ( $remove_location == $target ) {
										unset( $patched_files['delete'][$path] );
									}
								}
							}
						}
						if ( 'theme' == $remove_location ) {
							$patched_files['theme_version'] = PORTO_VERSION;
						} else {
							$patched_files['func_version'] = PORTO_FUNC_VERSION;
						}
						// Remove Changelog
						$patched_files['changelog'] = array();
						update_site_option( self::$transient_name, $patched_files );
					}
				}
			}

			/**
			 * Apply patches and update database
			 * 
			 * @since 6.7.0
			 */
			public function apply_patches() {
				// filesystem
				global $wp_filesystem;
				// initialize the WordPress filesystem, no more using file_put_contents function
				if ( empty( $wp_filesystem ) ) {
					require_once ABSPATH . '/wp-admin/includes/file.php';
					WP_Filesystem();
				}

				// path for parent directory of porto-theme root directory - .../wp-content/themes/
				$theme_dir = wp_normalize_path( dirname( PORTO_DIR ) ) . '/';
				// path for parent directory of porto-funtionality root directory - .../wp-content/plugins/
				if ( defined( 'PORTO_FUNC_FILE' ) ) {
					$func_dir = substr( wp_normalize_path( dirname( PORTO_FUNC_FILE ) ), 0, -19 );
				}
				// get transient
				$this->get_transient();
				// get unapplied patches
				$this->get_filter_patches();
				$this->patched_data['error'] = false;
				$patches_data = array();
				foreach ( $this->patched_data as $action => $patches ) {
					if ( 'update' == $action && ! empty( $patches ) ) {
						foreach ( $patches as $file_path => $value ) {
							// get patch files content from server
							$patches_data[ $file_path ] = $value['patch_path'];
						}
					} elseif( 'delete' == $action && ! empty( $patches ) ) {
						foreach ( $patches as $path => $target ) {
							$status = true;
							if ( 'theme' == $target ) {
								$status = $this->delete_file( $theme_dir . $path );
							} elseif ( 'functionality' == $target ) {
								$status = $this->delete_file( $func_dir . $path );
							}
							if ( ! $status ) {
								unset( $this->patches['delete'][$path] );
								unset( $this->patched_data['delete'][$path] );
								$this->patched_data['error'] = true;
							}
						}
					}
				}
				
				if ( ! empty( $patches_data ) ) {
					// apply patches except delete action
					require_once PORTO_PLUGINS . '/importer/importer-api.php';
					$importer_api = new Porto_Importer_API();
					$response = $importer_api->get_patch_content( $patches_data );
					
					foreach( $this->patched_data['update'] as $file_path => $value ) {
						if ( isset( $response[$file_path] ) ) {
							$status = true;
							if ( 'theme' == $value['target'] ) {
								$status = $this->write_file( $theme_dir . $file_path, $response[$file_path]  );
							} elseif ( 'functionality' == $value['target'] ) {
								$status = $this->write_file( $func_dir . $file_path, $response[$file_path]  );
							}
						} else {
							$status = false;
						}
						if ( ! $status ) {
							unset($this->patched_data['update'][$file_path]);
							unset($this->patches['update'][$file_path]);
							$this->patched_data['error'] = true;
						}
					}
				}
				// Save Patches
				update_site_option( self::$transient_name, $this->patches );
				wp_send_json_success( $this->patched_data );
				die;
			}

			/**
			 * Create File - If the file isn't existed, create folder and file
			 * 
			 * @since 6.7.0
			 */
			public function write_file( $file_path, $response ) {
				global $wp_filesystem;
				if ( ! $wp_filesystem->exists( $file_path ) ) {
					$pos = strripos( $file_path, '/' );
					if ( ! wp_mkdir_p( substr( $file_path, 0, $pos) ) ){
						return false;
					}
				}
				return $wp_filesystem->put_contents( $file_path, $response, FS_CHMOD_FILE );
			}

			/**
			 * Delete File
			 * 
			 * @since 6.7.0
			 */
			public function delete_file( $file_path ) {
				global $wp_filesystem;
				if ( $wp_filesystem->is_dir( $file_path ) ) {
					return $wp_filesystem->rmdir( $file_path, true );
				} else if ( $wp_filesystem->is_file( $file_path )  ) {
					return $wp_filesystem->delete( $file_path );
				} else {
					// File is not existed
					return true;
				}
			}
		}
	}
	Porto_Patcher::get_instance();
}
