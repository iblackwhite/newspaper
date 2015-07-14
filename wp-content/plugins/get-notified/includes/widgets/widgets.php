<?php
/**
 * @package Get Notified - Viral Facebook Notifications
 */
/*
Widgets
*/

if( !defined('GN_VERSION') )
	die; // don't load this file directly


include_once GN_WIDGET_DIR . 'get-notified-widget.php';

add_action( 'widgets_init', function(){
	
    register_widget( 'GN_Get_Notified_Widget' );
	
});	

?>