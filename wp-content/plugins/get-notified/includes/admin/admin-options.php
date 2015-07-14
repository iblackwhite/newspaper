<?php
/**
 * @package Get Notified - Viral Facebook Notifications
 */
/*
Admin Options Page
*/

function gn_admin_pages(){
	
	$pages = array(
			'gn_general_options'			=>	array( 
												'slug' => 'gn_general_options', 
												'name' => __( 'Setup Application', 'get-notified' )
											),
			'gn_display_options'			=>	array( 
												'slug' => 'gn_display_options', 
												'name' => __( 'Button Insertion', 'get-notified' )
											),
			'gn_authorization_settings'		=>	array( 
												'slug' => 'gn_authorization_settings', 
												'name' => __( 'User Instructions', 'get-notified' )
											),
			'gn_quick_notifications_page'	=>	array( 
												'slug' => 'gn_quick_notifications_page', 
												'name' => __( 'Quick Notifications', 'get-notified' )
											),
			'gn_fb_userdata_list'			=>	array( 
												'slug' => 'gn_fb_userdata_list', 
												'name' => __( 'Subscribers List', 'get-notified' )
											)
	);
	
	return $pages;
	
}


add_action( 'admin_menu', 'gn_admin_menu_pages' );
function gn_admin_menu_pages() {
	
	$gn_admin_pages = gn_admin_pages();
	
	add_menu_page(
		__( 'Get Notified', 'get-notified' ),		// The value used to populate the browser's title bar when the menu page is active
		__( 'Get Notified', 'get-notified' ),		// The text of the menu in the administrator's sidebar
		'manage_options',							// What roles are able to access the menu
		$gn_admin_pages['gn_general_options']['slug'],		// The ID used to bind submenu items to this menu 
		'gn_admin_interface',						// The callback function used to render this menu
		GN_URL . 'images/icon.png'
	);
	
	add_submenu_page(
		$gn_admin_pages['gn_general_options']['slug'],			// The ID of the top-level menu page to which this submenu item belongs
		$gn_admin_pages['gn_general_options']['name'],			// The value used to populate the browser's title bar when the menu page is active
		$gn_admin_pages['gn_general_options']['name'],			// The label of this submenu item displayed in the menu
		'manage_options',										// What roles are able to access this submenu item
		$gn_admin_pages['gn_general_options']['slug'],			// The ID used to represent this submenu item
		'gn_admin_interface'									// The callback function used to render the options for this submenu item
	);
	
	add_submenu_page(
		$gn_admin_pages['gn_general_options']['slug'],			// The ID of the top-level menu page to which this submenu item belongs
		$gn_admin_pages['gn_display_options']['name'],			// The value used to populate the browser's title bar when the menu page is active
		$gn_admin_pages['gn_display_options']['name'],			// The label of this submenu item displayed in the menu
		'manage_options',										// What roles are able to access this submenu item
		$gn_admin_pages['gn_display_options']['slug'],			// The ID used to represent this submenu item
		create_function( null, 'gn_admin_interface( "gn_display_options" );' )		// The callback function used to render the options for this submenu item
	);
	
	add_submenu_page(
		$gn_admin_pages['gn_general_options']['slug'],			// The ID of the top-level menu page to which this submenu item belongs
		$gn_admin_pages['gn_authorization_settings']['name'],			// The value used to populate the browser's title bar when the menu page is active
		$gn_admin_pages['gn_authorization_settings']['name'],			// The label of this submenu item displayed in the menu
		'manage_options',										// What roles are able to access this submenu item
		$gn_admin_pages['gn_authorization_settings']['slug'],			// The ID used to represent this submenu item
		create_function( null, 'gn_admin_interface( "gn_authorization_settings" );' )		// The callback function used to render the options for this submenu item
	);

	add_submenu_page(
		$gn_admin_pages['gn_general_options']['slug'],			// The ID of the top-level menu page to which this submenu item belongs
		$gn_admin_pages['gn_quick_notifications_page']['name'],			// The value used to populate the browser's title bar when the menu page is active
		$gn_admin_pages['gn_quick_notifications_page']['name'],			// The label of this submenu item displayed in the menu
		'manage_options',										// What roles are able to access this submenu item
		$gn_admin_pages['gn_quick_notifications_page']['slug'],			// The ID used to represent this submenu item
		$gn_admin_pages['gn_quick_notifications_page']['slug']		// The callback function used to render the options for this submenu item
	);
	
	add_submenu_page(
		$gn_admin_pages['gn_general_options']['slug'],			// The ID of the top-level menu page to which this submenu item belongs
		$gn_admin_pages['gn_fb_userdata_list']['name'],			// The value used to populate the browser's title bar when the menu page is active
		$gn_admin_pages['gn_fb_userdata_list']['name'],			// The label of this submenu item displayed in the menu
		'manage_options',										// What roles are able to access this submenu item
		$gn_admin_pages['gn_fb_userdata_list']['slug'],			// The ID used to represent this submenu item
		$gn_admin_pages['gn_fb_userdata_list']['slug']		// The callback function used to render the options for this submenu item
	);
	
}


include_once GN_ADMIN_DIR . 'includes/admin-interface.php';

include_once GN_ADMIN_DIR . 'includes/general-options.php';

include_once GN_ADMIN_DIR . 'includes/display-options.php';

include_once GN_ADMIN_DIR . 'includes/authorization-settings.php';

include_once GN_ADMIN_DIR . 'includes/subscribers-list.php';

include_once GN_ADMIN_DIR . 'includes/quick-notifications.php';

include_once GN_ADMIN_DIR . 'includes/scripts-styles.php';

include_once GN_ADMIN_DIR . 'includes/sanitize.php';

?>