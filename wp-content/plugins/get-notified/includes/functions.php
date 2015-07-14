<?php 

if( !defined('GN_VERSION') )
	die; // don't load this file directly

if( !function_exists('pre') ){
	
	function pre($string){
		
		if( is_array( $string ) || is_object( $string ) )
			$string = print_r( $string, true );
		
		return '<pre>' . $string .'</pre>';
		
	}
	
}

if( !function_exists('pb_get_image') ){

	function pb_get_image( $url, $alt = '' ){
		
		return '<img src="'.$url.'" alt="'.$alt.'" />';
		
	}

}

if( !function_exists('pb_get_current_url') ){
	
	function pb_get_current_url($full = false){

		if (!$full)
			return $_SERVER['REQUEST_URI'];
		
		return 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'].(($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443) ? '' : ':'.$_SERVER['SERVER_PORT']).$_SERVER['REQUEST_URI'];

	}

}

function gn_is_compatible_server(){
	
	return version_compare( PHP_VERSION, '5.4', '>=');
	
}

/**
 * Get Notified HTTPS Replacement
 * Since: 1.0
 * Updated: 2.2
 */
function gn_https_replacement( $url ){
	
	$from = is_ssl() ? 'http://' : 'https://';
	$to = is_ssl() ? 'https://' : 'http://';
	
	return str_replace( $from, $to, $url );
	
}

function gn_wrap_in_heading($message, $tag = 'h1', $classes = ''){
	
	return "<$tag class=\"$classes\">$message</$tag>";
	
}

function gn_hide_on_subscription(){
	
	$options = get_option('gn_display_options');
	
	return $options['hide_on_subscription'];
	
}

function gn_get_button(){
	
	$options = get_option('gn_display_options');
	
	return gn_https_replacement( $options['notify_button'] );
	
}

function gn_current_app_id(){
	
	$options = get_option('gn_general_options');
	
	return $options['fb_app_id'];
	
}

function gn_log_user_in( $user_id ){
	
	wp_set_current_user( $user_id, true, false );
	wp_set_auth_cookie( $user_id, true, false );
	
}

function gn_save_authorization_in_cookie(){
	
	setcookie( 'get_notified', true, time() + ( 60 * 86400 ), '/' );
	
}

function gn_is_authorized_get_notified(){
	
	return isset( $_COOKIE['get_notified'] );
	
}

function gn_set_token_updation(){
	
	setcookie( 'gn_token_updated', time(), time() + ( 1 * 86400 ), '/' );
	
}

function gn_is_token_updated(){
	
	return isset( $_COOKIE['gn_token_updated'] );
	
}

function gn_notification_logs_table( $rows = '' ){

	
	$table = '
		<table id="notification_logs_table" class="widefat">
			<thead>
				<tr>   
					<th>'.__( 'Name', 'get-notified' ).'</th>
					<th>'.__( 'Email', 'get-notified' ).'</th>
					<th>'.__( 'Facebook ID', 'get-notified' ).'</th>
					<th>'.__( 'Subscribed on', 'get-notified' ).'</th>
					<th>'.__( 'Status', 'get-notified' ).'</th>
				</tr>
			</thead>
			<tbody id="notification_logs_rows">
			'.$rows.'
			</tbody>
		</table>';
	
	return $table;
	
}

function gn_notification_prepare_logs_rows( $logs = array() ){

	$rows = '';
	foreach( $logs as $log ){
		
		$rows .= "
				<tr>
				 <td>$log->fb_name</td>
				 <td>$log->fb_email</td>
				 <td>$log->fb_id</td>
				 <td>$log->connected</td>
				 <td>".gn_get_log_status( $log->status )."</td>
			   </tr>"."\n";
		
	}
	
	return $rows;
	
}

function gn_get_log_status( $status ){
	
	switch( $status ){
		case 'pending':
			return __( 'Pending', 'get-notified' );
			break;
		case 'sent':
			return __( 'Sent', 'get-notified' );
			break;
		case 'cancelled':
			return __( 'Cancelled', 'get-notified' );
			break;
		case 'debug':
			return __( 'Debugging', 'get-notified' );
			break;
		default:
			return __( 'User unauthorized your app', 'get-notified' );
	} 
	
}

function gn_get_user_status( $status ){
	
	switch( $status ){
		case 1:
			return __( 'Active', 'get-notified' );
			break;
		default:
			return __( 'User unauthorized your app', 'get-notified' );
	} 
	
}


?>