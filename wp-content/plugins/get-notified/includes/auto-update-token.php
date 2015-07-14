<?php

if( !defined('GN_VERSION') )
	die; // don't load this file directly


add_action( 'init', 'gn_auto_update_token');

function gn_auto_update_token(){
	
	if( gn_is_token_updated() )
		return;
	
	$GN_FB_Connect = new GN_FB_Connect();
	$GN_FB_Connect->load_sdk_for_token_updation();
	
}

?>