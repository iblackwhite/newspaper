<?php 

add_action( 'wp_ajax_nopriv_gn_fb_connect', 'gn_fb_ajax_connect' );
add_action( 'wp_ajax_gn_fb_connect', 'gn_fb_ajax_connect' );

function gn_fb_ajax_connect(){
	
	check_ajax_referer( 'gn_fb_connect', 'nonce' );
	
	$status = array();
	$status['ajax'] = true;
	$status['success'] = 0;
	$status['msg'] = '';
	
	if( !isset( $_POST['fb_AccessToken'] ) || !isset( $_POST['fb_id'] ) || !isset( $_POST['wp_log'] ) ){
		
		$status['msg'] = __( 'Please refresh the page and try again', 'get-notified' );
		die( json_encode($status) );
	
	}
	
	$GN_FB_Connect = new GN_FB_Connect();
	$GN_FB_Connect->initiate( array( 'AccessToken' => $_POST['fb_AccessToken'] ) );
	
	if( $GN_FB_Connect->validate_session() === false ){
		
		$status['msg'] = __( 'Invalid facebook session', 'get-notified' );
		die( json_encode($status) );
		
	}
	
	if( ( $fb_user_data = $GN_FB_Connect->get_user_data() ) === false ){
		
		$status['msg'] = __( 'Generic Facebook error', 'get-notified' );
		die( json_encode($status) );
		
	}
	
	if( ( $longLivedToken = $GN_FB_Connect->get_extended_token() ) === false ){
		
		$status['msg'] = __( 'Generic Facebook error', 'get-notified' );
		die( json_encode($status) );
		
	}
	
	$fb_id = trim( $fb_user_data->getId() );
	$email = esc_sql( trim( $fb_user_data->getProperty('email') ) );
	$first_name = sanitize_text_field( $fb_user_data->getFirstName() );
	$last_name = sanitize_text_field( $fb_user_data->getLastName() );
	$first_last_name = $first_name .' '. $last_name;
	
	$token = $longLivedToken['token'];
	$token_expiry = $longLivedToken['expire'];

	$gn_db = new GN_DB();
	$fb_user = $gn_db->get_fb_user( array( 'fb_id' => $fb_id ) );
	
	gn_save_authorization_in_cookie();
	
	if( $fb_user ) {
	
		$gn_db->update_user_token( array( 'fb_token' => $token, 'fb_token_expires' => $token_expiry, 'user_status' => 1 ), array( 'fb_id' => $fb_id ) );
		
	}else{
		
		$gn_db->insert_new_fb_user( get_current_user_id(), $fb_id, $first_last_name, $email, $token, $token_expiry );
		
	}

	$status['success'] = 1;
	$status['msg'] = get_option('gn_post_authorization_message');
	die( json_encode( $status ) );
	
}

?>