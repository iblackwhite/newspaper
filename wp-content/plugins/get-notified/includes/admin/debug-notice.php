<?php
/**
 * @package Get Notified - Viral Facebook Notifications
 */

if( !defined('GN_VERSION') )
	die; // don't load this file directly

/**
 * Get Notified Debug Notice
 * Since: 2.2
 * Updated: 2.2
 */

add_action( 'admin_notices', 'gn_debug_notice' );
function gn_debug_notice() {
	
	if( !defined('GN_DEBUG') || !GN_DEBUG )
		return;
	
    printf( '<div class="error"> <p> %s </p> </div>', esc_html__( 'Get Notified debug mode is on. Please disable debug mode.', 'get-notified' ) );
 
}

?>