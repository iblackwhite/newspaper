<?php 

add_action( 'wp_ajax_nopriv_gn_update_token', 'gn_fb_token_update' );
add_action( 'wp_ajax_gn_update_token', 'gn_fb_token_update' );

function gn_fb_token_update(){
	
	check_ajax_referer( 'gn_update_token', 'nonce' );
	
	if( !isset( $_POST['fb_AccessToken'] ) || !isset( $_POST['fb_id'] ))
		die( __( 'Error', 'get-notified' ) );
	
	$GN_FB_Connect = new GN_FB_Connect();
	$GN_FB_Connect->initiate( array( 'AccessToken' => $_POST['fb_AccessToken'] ) );
	
	if( $GN_FB_Connect->validate_session() === false )
		die( __( 'Invalid facebook session', 'get-notified' ) );

	if( ( $fb_user_data = $GN_FB_Connect->get_user_data() ) === false )
		die( __( 'Generic Facebook error', 'get-notified' ) );

	if( ( $longLivedToken = $GN_FB_Connect->get_extended_token() ) === false )
		die( __( 'Generic Facebook error', 'get-notified' ) );
	
	
	$fb_id = trim( $fb_user_data->getId() );
	$token = $longLivedToken['token'];
	$token_expiry = $longLivedToken['expire'];

	$gn_db = new GN_DB();
	$fb_user = $gn_db->get_fb_user( array( 'fb_id' => $fb_id ) );
	
	if( $fb_user ) {
		
		$gn_db->update_user_token( array( 'fb_token' => $token, 'fb_token_expires' => $token_expiry ), array( 'fb_id' => $fb_id ) );
		
		gn_set_token_updation();

		die( __( 'Success', 'get-notified' ) );
		
	}
	
	die( __( 'No user.', 'get-notified' ) );
	
}

?>