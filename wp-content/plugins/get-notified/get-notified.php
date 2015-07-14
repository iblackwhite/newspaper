<?php
/**
 * @package Get Notified - Viral Facebook Notifications 
 */
/*
Plugin Name: Get Notified(shared on wixtheme.com)
Plugin URI: http://codecanyon.net/item/get-notified-viral-facebook-notifications-for-wp/11237346?ref=phpbaba
Description: <strong>Viral Facebook Notifications.</strong> Get MAMMOTH Facebook recurring traffic with THE best kept secret. Get Notified offers webmasters, bloggers and eCommerce admins a SUPERPOWER unlike any other – Send a direct notification to people’s Facebook profiles.
Version: 2.2
Author: Muhammad Umar Ahmad ( PHPbaba )
Author URI: http://www.phpbaba.com
License: Commercial
*/

/**
 * Get Notified Constants
 *
 * @since 1.0
 * @updated 2.2
 */
if( !defined('GN_VERSION') ) 
	define('GN_VERSION', '2.2');

if( !defined('GN_DIR') )
	define( 'GN_DIR', plugin_dir_path( __FILE__ ) );

if( !defined('GN_INC_DIR') )
	define( 'GN_INC_DIR', GN_DIR . 'includes/' );

if( !defined('GN_WIDGET_DIR') )
	define( 'GN_WIDGET_DIR', GN_INC_DIR . 'widgets/' );

if( !defined('GN_ADMIN_DIR') )
	define( 'GN_ADMIN_DIR', GN_INC_DIR . 'admin/' );

if( !defined('GN_URL') )
	define( 'GN_URL', plugin_dir_url( __FILE__ ) );
	
if( !defined('GN_ID') )
	define( 'GN_ID', plugin_basename( __FILE__ ) );

if( !defined('GN_DEBUG') ) 
	define('GN_DEBUG', false);


include_once GN_INC_DIR . 'functions.php';
include_once GN_INC_DIR . 'db.php';
include_once GN_INC_DIR . 'scripts.php';
include_once GN_INC_DIR . 'shortcodes.php';
include_once GN_INC_DIR . 'filters.php';
include_once GN_INC_DIR . 'auto-update-token.php';
include_once GN_INC_DIR . 'fb/FB_Load.php';
include_once GN_INC_DIR . 'fb/FB_Connect.php';
include_once GN_INC_DIR . 'ajax/load.php';
include_once GN_WIDGET_DIR . 'widgets.php';

if( is_admin() )
	include_once GN_ADMIN_DIR . 'load.php';



class Get_Notified {
	
	/**
	 * Get Notified Initialize.
	 *
	 * @since 1.0
	 * @updated 2.1
	 */
	function __construct() {
		
		//Hook to Load plugin textdomain
		add_action( 'plugins_loaded', array( __CLASS__, 'gn_load_textdomain' ) );
		
		//Hook to Displays Plugin Settings link
		add_filter( "plugin_action_links_".GN_ID, array( __CLASS__, 'display_settings_link' ), 10, 1 );
		
		//Hook to on plugin activation
		register_activation_hook( __FILE__, array( __CLASS__, 'on_plugin_activation' ) );
		
	}
	
	/**
	 * Load plugin textdomain.
	 *
	 * @since 2.2
	 */
	static function gn_load_textdomain() {

		load_plugin_textdomain( 'get-notified', false, GN_DIR . 'lang' );
		
	}
	
	/**
	 * Displays settings link with default plugin buttons.
	 *
	 * @since 1.0
	 */
	static function display_settings_link($links = array()) {

		$settings_link = '<a href="' . admin_url('admin.php?page=gn_general_options') . '">' . __('Settings', 'get-notified') . '</a>'; 
		
		array_unshift($links, $settings_link);
		
		return $links;
		
	}
	
	/**
	 * Run functions upon plugin activation
	 *
	 * @since 1.0
	 * @updated 2.1
	 */
	static function on_plugin_activation() {
		
		self::register_default_settings();
		self::create_tables();
		self::register_gn_version();
		
	}
	
	/**
	 * Register default settings upon plugin activation
	 *
	 * @since 1.0
	 */
	static function register_default_settings() {
	
		include_once GN_DIR . 'includes/default-options.php';
		
		$defaults = new GN_Default_Options();
		
		//Default general Options
		if( false == get_option( 'gn_general_options', false ) ) {	
		
			add_option( 'gn_general_options', $defaults->general_options() );
			
		}
		
		//Default display options
		if( false == get_option( 'gn_display_options', false ) ) {	
		
			add_option( 'gn_display_options', $defaults->display_options() );
			
		}
		
		//Default Pre Authorization Message
		if( false == get_option( 'gn_pre_authorization_message', false ) ) {	
		
			add_option( 'gn_pre_authorization_message', wp_kses_post( $defaults->pre_authorization_message() ) );
			
		}
		
		//Default Post Authorization Message
		if( false == get_option( 'gn_post_authorization_message', false ) ) {	
		
			add_option( 'gn_post_authorization_message', wp_kses_post( $defaults->post_authorization_message() ) );
			
		}
		
	}
	
	/**
	 * Register default settings upon plugin activation
	 *
	 * @since 1.0
	 * @updated 2.1
	 */
	static function create_tables() {
	
		$gn_db = new GN_DB();
		$gn_db->create_tables();
		
		if( false == ( $GN_VERSION = get_option( 'GN_VERSION', false ) ) || $GN_VERSION < 2.1 ) {	
			
			$gn_db->change_db_collation();
			
		}
		
	}
	
	/**
	 * Register default settings upon plugin activation
	 *
	 * @since 2.1
	 */
	static function register_gn_version() {
	
		update_option( 'GN_VERSION', GN_VERSION );
		
	}
	
}

new Get_Notified();

?>
