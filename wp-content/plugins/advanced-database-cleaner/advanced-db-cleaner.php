<?php
/**
 * Plugin Name:       Advanced Database Cleaner
 * Plugin URI:        https://sigmaplugin.com/downloads/wordpress-advanced-database-cleaner
 * Description:       The most advanced Database Cleaner for WordPress. Clean database by deleting orphaned items such as old "revisions", "old drafts", optimize database, and more.
 * Version:           4.1.0
 * Author:            SigmaPlugin
 * Author URI:        https://sigmaplugin.com
 * Contributors:      symptote
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       advanced-database-cleaner
 * Domain Path:       /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

// Return if not in main site
if ( ! is_main_site() )
	return;

/*************************************************************************************
 * Check for conflicts with other versions and stop loading if a conflict is detected. 
 ************************************************************************************/
if ( function_exists( 'is_plugin_active' ) === false )
	include_once ABSPATH . 'wp-admin/includes/plugin.php';

// Free should not run if Pro or Premium is active.
if ( basename( dirname( __FILE__ ) ) === 'advanced-database-cleaner' ) {
	if ( is_plugin_active( 'advanced-database-cleaner-pro/advanced-db-cleaner.php' ) ||
		is_plugin_active( 'advanced-database-cleaner-premium/advanced-db-cleaner.php' ) )
		return;
}

// Pro should not run if Premium is active.
if ( basename( dirname( __FILE__ ) ) === 'advanced-database-cleaner-pro' ) {
	if ( is_plugin_active( 'advanced-database-cleaner-premium/advanced-db-cleaner.php' ) )
		return;
}
/*************************************************************************************
 * End of conflicts check
 ************************************************************************************/

if ( ! defined( "ADBC_MAIN_PLUGIN_FILE_PATH" ) )
	define( "ADBC_MAIN_PLUGIN_FILE_PATH", __FILE__ );

if ( ! defined( 'ADBC_PLUGIN_VERSION' ) )
	define( 'ADBC_PLUGIN_VERSION', '4.1.0' );

class ADBC_Advanced_DB_Cleaner {

	public function __construct() {

		// This plugin operates in admin, WP-Cron, and REST API contexts only.
		// Skip bootstrap on frontend requests to avoid unnecessary DB queries and file includes.
		if ( ! is_admin() && ! wp_doing_cron() && ! self::is_rest_request() )
			return;

		// Load the classes files on demand
		spl_autoload_register( [ $this, 'loader' ] );

		// Load constants early so activation hooks have access to them
		include_once 'constants.php';

		// Menus, scripts and custom styles
		add_action( 'admin_menu', [ 'ADBC_Admin_Init', '_add_admin_menu' ] );
		if ( is_multisite() )
			add_action( 'network_admin_menu', [ 'ADBC_Admin_Init', '_add_network_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ 'ADBC_Admin_Init', '_enqueue_scripts' ] );

		// Prevent conflicts with other versions
		if ( ADBC_Admin_Init::has_conflict() )
			return;

		// Maybe schedule a conflict notice.
		add_action( 'admin_init', [ 'ADBC_Admin_Init', 'maybe_schedule_conflict_notice' ] );

		// Register activation and deactivation hooks
		register_activation_hook( __FILE__, [ 'ADBC_Admin_Init', 'activate' ] );
		register_deactivation_hook( __FILE__, [ 'ADBC_Admin_Init', 'deactivate' ] );

		// Register all routes
		add_action( 'rest_api_init', [ 'ADBC_Routes', 'register_all_routes' ] );

		// Init
		add_action( 'init', [ 'ADBC_Admin_Init', 'load_text_domain' ], 10 );
		add_action( 'init', [ 'ADBC_Admin_Init', 'create_adbc_uploads_folder_with_its_content' ], 11 );
		add_action( 'init', [ 'ADBC_Admin_Init', 'register_cron_schedules_filter' ], 12 );
		add_action( 'init', [ 'ADBC_Admin_Init', 'load_general_cleanup_handlers' ], 13 );
		if ( wp_doing_cron() )
			add_action( 'init', [ 'ADBC_Admin_Init', 'ensure_automation_integrity' ], 14 );

		if ( ADBC_VERSION_TYPE === "FREE" )
			add_action( 'init', [ 'ADBC_Migration', 'run_free_migration' ], 15 );

		if ( ADBC_IS_PRO_VERSION === true )
			add_action( 'init', [ 'ADBC_Migration', 'run_pro_migration' ], 16 );

		// Show global notifications
		add_action( 'all_admin_notices', [ 'ADBC_Admin_Init', 'maybe_show_global_notifications' ] );

		// Add crons hooks
		add_action( 'adbc_cron_automation', [ 'ADBC_Automation', '_run_task_by_id' ], 10, 1 );

		if ( ADBC_VERSION_TYPE === "PREMIUM" ) {

			// Initialize the registered post types dict tracker
			ADBC_Registered_Post_Types_Dict_Tracker::instance()->init();

			// Register premium routes
			add_action( 'rest_api_init', [ 'ADBC_Premium_Routes', 'register_routes' ] );

			// Analytics cron hook scheduler
			add_action( 'init', [ 'ADBC_Analytics', 'check_and_schedule_cron' ], 16 );

			// Analytics cron hook
			add_action( 'adbc_cron_analytics', [ 'ADBC_Analytics', '_run_analytics_cron' ] );

			// Hook into plugin and theme events.
			add_action( 'activated_plugin', [ 'ADBC_Addons_Activity', 'on_plugin_activated' ] );
			add_action( 'deactivated_plugin', [ 'ADBC_Addons_Activity', 'on_plugin_deactivated' ] );
			add_action( 'switch_theme', [ 'ADBC_Addons_Activity', 'on_theme_switched' ], 10, 3 );
			add_action( 'delete_plugin', [ 'ADBC_Addons_Activity', 'on_plugin_uninstalled' ] );
			add_action( 'delete_theme', [ 'ADBC_Addons_Activity', 'on_theme_uninstalled' ] );

			// Initialize the plugin license handler and updater.
			$sdk_handler = __DIR__ . '/vendor/easy-digital-downloads/edd-sl-sdk/edd-sl-sdk.php';
			if ( file_exists( $sdk_handler ) ) {
				require_once $sdk_handler;
			}
			// Initialize the EDD SL SDK license manager
			add_action( 'edd_sl_sdk_registry', [ 'ADBC_License_Manager', 'register_sdk' ] );
			// Remove SDK license manage link from plugin actions
			add_filter(
				'plugin_action_links_' . plugin_basename( ADBC_MAIN_PLUGIN_FILE_PATH ),
				[ 'ADBC_License_Manager', 'filter_remove_sdk_manage_link' ],
				200,
				3
			);

		}

		// filters
		add_filter( 'load_script_translation_file', [ 'ADBC_Admin_Init', 'change_script_translation_file_name' ], 10, 3 );
		if ( method_exists( 'ADBC_Admin_Init', '_capture_original_plugin_meta_links' ) && method_exists( 'ADBC_Admin_Init', '_restore_plugin_meta_links' ) ) {
			add_filter( 'plugin_row_meta', [ 'ADBC_Admin_Init', '_capture_original_plugin_meta_links' ], 1, 2 );
			add_filter( 'plugin_row_meta', [ 'ADBC_Admin_Init', '_restore_plugin_meta_links' ], 999, 2 );
		}
		/* TO-CHECK: Always prioritize plugin's shipped translations over global ones for the pro version
		 * For the free version we keep it until new version (>=4.0.0) translations are mature in the global repo */
		add_filter( 'load_textdomain_mofile', [ 'ADBC_Admin_Init', 'prioritize_plugin_translations' ], 10, 2 );
		// Declare compliance with consent level API
		$plugin = plugin_basename( __FILE__ );
		add_filter( "wp_consent_api_registered_{$plugin}", '__return_true' );

	}

	/**
	 * Load the class file using a deterministic class map.
	 *
	 * @param string $class_name The class/interface/trait name.
	 * 
	 * @return void
	 */
	public function loader( $class_name ) {

		if ( strpos( $class_name, 'ADBC_' ) !== 0 ) {
			return;
		}

		if ( empty( self::$class_map[ $class_name ] ) ) {
			return;
		}

		$file_path = __DIR__ . '/' . self::$class_map[ $class_name ];

		include_once $file_path;

	}

	/**
	 * Detect a REST API request before the REST_REQUEST constant is defined.
	 *
	 * Called at plugin load time (before parse_request), so REST_REQUEST is not
	 * yet available. The URI and query-string checks act as early heuristics.
	 * False positives are acceptable here (just an extra bootstrap); false negatives
	 * could prevent the plugin's REST endpoints from loading correctly.
	 *
	 * @return bool
	 */
	private static function is_rest_request() {

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			return true;

		if ( isset( $_GET['rest_route'] ) && is_string( $_GET['rest_route'] ) )
			return true;

		if ( ! isset( $_SERVER['REQUEST_URI'] ) || ! is_string( $_SERVER['REQUEST_URI'] ) )
			return false;

		$prefix = function_exists( 'rest_get_url_prefix' ) ? rest_get_url_prefix() : 'wp-json';
		$path = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

		return is_string( $path ) && strpos( $path, '/' . $prefix . '/' ) !== false;

	}

	/**
	 * Deterministic class map for ADBC.
	 *
	 * @return array The class map.
	 */
	private static $class_map = [ 
		'ADBC_Abstract_Cleanup_Handler' => 'includes/classes/general-cleanup/class-adbc-abstract-cleanup-handler.php',
		'ADBC_Addons' => 'includes/classes/addons/class-adbc-addons.php',
		'ADBC_Addons_Activity' => 'includes/premium/classes/addons/class-adbc-addons-activity.php',
		'ADBC_Addons_Endpoints' => 'includes/premium/endpoints/class-adbc-addons-endpoints.php',
		'ADBC_Admin_Init' => 'includes/classes/class-adbc-admin-init.php',
		'ADBC_Analytics' => 'includes/premium/classes/class-adbc-analytics.php',
		'ADBC_Analytics_Endpoints' => 'includes/premium/endpoints/class-adbc-analytics-endpoints.php',
		'ADBC_Automation' => 'includes/classes/class-adbc-automation.php',
		'ADBC_Automation_Endpoints' => 'includes/endpoints/class-adbc-automation-endpoints.php',
		'ADBC_Automation_Events_Log' => 'includes/premium/classes/class-adbc-automation-events-log.php',
		'ADBC_Automation_Validator' => 'includes/utils/validator/class-adbc-automation-validator.php',
		'ADBC_Cleanup_Actionscheduler_Actions_Handler_Base' => 'includes/premium/classes/general-cleanup/type-handlers/class-adbc-actionscheduler-actions-handler.php',
		'ADBC_Cleanup_Actionscheduler_Canceled_Actions_Handler' => 'includes/premium/classes/general-cleanup/type-handlers/class-adbc-actionscheduler-actions-handler.php',
		'ADBC_Cleanup_Actionscheduler_Canceled_Logs_Handler' => 'includes/premium/classes/general-cleanup/type-handlers/class-adbc-actionscheduler-logs-handler.php',
		'ADBC_Cleanup_Actionscheduler_Completed_Actions_Handler' => 'includes/premium/classes/general-cleanup/type-handlers/class-adbc-actionscheduler-actions-handler.php',
		'ADBC_Cleanup_Actionscheduler_Completed_Logs_Handler' => 'includes/premium/classes/general-cleanup/type-handlers/class-adbc-actionscheduler-logs-handler.php',
		'ADBC_Cleanup_Actionscheduler_Failed_Actions_Handler' => 'includes/premium/classes/general-cleanup/type-handlers/class-adbc-actionscheduler-actions-handler.php',
		'ADBC_Cleanup_Actionscheduler_Failed_Logs_Handler' => 'includes/premium/classes/general-cleanup/type-handlers/class-adbc-actionscheduler-logs-handler.php',
		'ADBC_Cleanup_Actionscheduler_Logs_Handler_Base' => 'includes/premium/classes/general-cleanup/type-handlers/class-adbc-actionscheduler-logs-handler.php',
		'ADBC_Cleanup_Actionscheduler_Orphan_Logs_Handler' => 'includes/premium/classes/general-cleanup/type-handlers/class-adbc-actionscheduler-logs-handler.php',
		'ADBC_Cleanup_Auto_Drafts_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-posts-handler.php',
		'ADBC_Cleanup_Comments_Handler_Base' => 'includes/classes/general-cleanup/type-handlers/class-adbc-comments-handler.php',
		'ADBC_Cleanup_Duplicated_Commentmeta_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-duplicated-meta-handler.php',
		'ADBC_Cleanup_Duplicated_Meta_Handler_Base' => 'includes/classes/general-cleanup/type-handlers/class-adbc-duplicated-meta-handler.php',
		'ADBC_Cleanup_Duplicated_Postmeta_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-duplicated-meta-handler.php',
		'ADBC_Cleanup_Duplicated_Termmeta_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-duplicated-meta-handler.php',
		'ADBC_Cleanup_Duplicated_Usermeta_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-duplicated-meta-handler.php',
		'ADBC_Cleanup_Expired_Transients_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-expired-transients-handler.php',
		'ADBC_Cleanup_Oembed_Cache_Meta_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-unused-meta-handler.php',
		'ADBC_Cleanup_Optimize_Tables_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-tables-handler.php',
		'ADBC_Cleanup_Pingbacks_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-comments-handler.php',
		'ADBC_Cleanup_Posts_Handler_Base' => 'includes/classes/general-cleanup/type-handlers/class-adbc-posts-handler.php',
		'ADBC_Cleanup_Repair_Tables_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-tables-handler.php',
		'ADBC_Cleanup_Revisions_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-posts-handler.php',
		'ADBC_Cleanup_Spam_Comments_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-comments-handler.php',
		'ADBC_Cleanup_Tables_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-tables-handler.php',
		'ADBC_Cleanup_Trackbacks_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-comments-handler.php',
		'ADBC_Cleanup_Trash_Comments_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-comments-handler.php',
		'ADBC_Cleanup_Trashed_Posts_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-posts-handler.php',
		'ADBC_Cleanup_Type_Handler' => 'includes/classes/general-cleanup/class-adbc-cleanup-type-handler.php',
		'ADBC_Cleanup_Type_Registry' => 'includes/classes/general-cleanup/class-adbc-cleanup-type-registry.php',
		'ADBC_Cleanup_Unapproved_Comments_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-comments-handler.php',
		'ADBC_Cleanup_Unused_Commentmeta_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-unused-meta-handler.php',
		'ADBC_Cleanup_Unused_Meta_Handler_Base' => 'includes/classes/general-cleanup/type-handlers/class-adbc-unused-meta-handler.php',
		'ADBC_Cleanup_Unused_Postmeta_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-unused-meta-handler.php',
		'ADBC_Cleanup_Unused_Relationships_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-unused-relationships-handler.php',
		'ADBC_Cleanup_Unused_Termmeta_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-unused-meta-handler.php',
		'ADBC_Cleanup_Unused_Usermeta_Handler' => 'includes/classes/general-cleanup/type-handlers/class-adbc-unused-meta-handler.php',
		'ADBC_Collect_Files' => 'includes/premium/classes/scan/process/steps/class-adbc-collect-files.php',
		'ADBC_Common_Endpoints' => 'includes/endpoints/class-adbc-common-endpoints.php',
		'ADBC_Common_Model' => 'includes/models/class-adbc-common-model.php',
		'ADBC_Common_Utils' => 'includes/utils/class-adbc-common-utils.php',
		'ADBC_Common_Validator' => 'includes/utils/validator/class-adbc-common-validator.php',
		'ADBC_Cron_Jobs' => 'includes/models/class-adbc-cron-jobs.php',
		'ADBC_Cron_Jobs_Endpoints' => 'includes/endpoints/class-adbc-cron-jobs-endpoints.php',
		'ADBC_Database' => 'includes/models/class-adbc-database.php',
		'ADBC_Dictionary' => 'includes/classes/addons/class-adbc-dictionary.php',
		'ADBC_Exact_Match' => 'includes/premium/classes/scan/process/steps/class-adbc-exact-match.php',
		'ADBC_Files' => 'includes/utils/class-adbc-files.php',
		'ADBC_General_Cleanup' => 'includes/classes/general-cleanup/class-adbc-general-cleanup.php',
		'ADBC_General_Cleanup_Endpoints' => 'includes/endpoints/class-adbc-general-cleanup-endpoints.php',
		'ADBC_Hardcoded_Items' => 'includes/classes/class-adbc-hardcoded-items.php',
		'ADBC_Info_Endpoints' => 'includes/endpoints/class-adbc-info-endpoints.php',
		'ADBC_License_Endpoints' => 'includes/premium/endpoints/class-adbc-license-endpoints.php',
		'ADBC_License_Manager' => 'includes/premium/classes/class-adbc-license-manager.php',
		'ADBC_Local_Scan' => 'includes/premium/classes/scan/process/class-adbc-local-scan.php',
		'ADBC_Logging' => 'includes/utils/class-adbc-logging.php',
		'ADBC_Logs_Endpoints' => 'includes/endpoints/class-adbc-logs-endpoints.php',
		'ADBC_Migration' => 'includes/classes/class-adbc-migration.php',
		'ADBC_Migration_Endpoints' => 'includes/premium/endpoints/class-adbc-migration-endpoints.php',
		'ADBC_Notifications' => 'includes/utils/class-adbc-notifications.php',
		'ADBC_Options' => 'includes/models/class-adbc-options.php',
		'ADBC_Options_Endpoints' => 'includes/endpoints/class-adbc-options-endpoints.php',
		'ADBC_Partial_Match' => 'includes/premium/classes/scan/process/steps/class-adbc-partial-match.php',
		'ADBC_Plugins' => 'includes/classes/addons/class-adbc-plugins.php',
		'ADBC_Posts_Meta' => 'includes/models/class-adbc-posts-meta.php',
		'ADBC_Posts_Meta_Endpoints' => 'includes/endpoints/class-adbc-posts-meta-endpoints.php',
		'ADBC_Premium_Common_Validator' => 'includes/premium/utils/validator/class-adbc-premium-common-validator.php',
		'ADBC_Premium_Routes' => 'includes/premium/classes/class-adbc-premium-routes.php',
		'ADBC_Prepare_Items' => 'includes/premium/classes/scan/process/steps/class-adbc-prepare-items.php',
		'ADBC_Prepare_Local_Scan_Results' => 'includes/premium/classes/scan/process/steps/class-adbc-prepare-local-scan-results.php',
		'ADBC_Remote_Request' => 'includes/premium/utils/class-adbc-remote-request.php',
		'ADBC_Remote_Scan' => 'includes/premium/classes/scan/process/class-adbc-remote-scan.php',
		'ADBC_Rest' => 'includes/utils/class-adbc-rest.php',
		'ADBC_Routes' => 'includes/classes/class-adbc-routes.php',
		'ADBC_Scan' => 'includes/premium/classes/scan/process/class-adbc-scan.php',
		'ADBC_Scan_Counter' => 'includes/classes/class-adbc-scan-counter.php',
		'ADBC_Scan_Endpoints' => 'includes/premium/endpoints/class-adbc-scan-endpoints.php',
		'ADBC_Scan_Info' => 'includes/premium/classes/scan/process/class-adbc-scan-info.php',
		'ADBC_Scan_Paths' => 'includes/premium/classes/scan/class-adbc-scan-paths.php',
		'ADBC_Scan_Results' => 'includes/premium/classes/scan/class-adbc-scan-results.php',
		'ADBC_Scan_Utils' => 'includes/premium/classes/scan/class-adbc-scan-utils.php',
		'ADBC_Scan_Validator' => 'includes/premium/utils/validator/class-adbc-scan-validator.php',
		'ADBC_Selected_Items_Validator' => 'includes/utils/validator/class-adbc-selected-items-validator.php',
		'ADBC_Settings' => 'includes/classes/class-adbc-settings.php',
		'ADBC_Settings_Endpoints' => 'includes/endpoints/class-adbc-settings-endpoints.php',
		'ADBC_Settings_Validator' => 'includes/utils/validator/class-adbc-settings-validator.php',
		'ADBC_Singleton' => 'includes/classes/class-adbc-singleton.php',
		'ADBC_Sites' => 'includes/models/class-adbc-sites.php',
		'ADBC_Tables' => 'includes/models/class-adbc-tables.php',
		'ADBC_Tables_Endpoints' => 'includes/endpoints/class-adbc-tables-endpoints.php',
		'ADBC_Tables_Validator' => 'includes/utils/validator/class-adbc-tables-validator.php',
		'ADBC_Themes' => 'includes/classes/addons/class-adbc-themes.php',
		'ADBC_Transients' => 'includes/models/class-adbc-transients.php',
		'ADBC_Transients_Endpoints' => 'includes/endpoints/class-adbc-transients-endpoints.php',
		'ADBC_Users_Meta' => 'includes/models/class-adbc-users-meta.php',
		'ADBC_Users_Meta_Endpoints' => 'includes/endpoints/class-adbc-users-meta-endpoints.php',
		'ADBC_Post_Types' => 'includes/models/class-adbc-post-types.php',
		'ADBC_Post_Types_Endpoints' => 'includes/endpoints/class-adbc-post-types-endpoints.php',
		'ADBC_Registered_Post_Types_Dict_Tracker' => 'includes/premium/classes/scan/class-adbc-registered-post-types-dict-tracker.php',
	];

}

// Get instance
new ADBC_Advanced_DB_Cleaner();