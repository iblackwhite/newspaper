<?php 

if( !defined('GN_VERSION') )
	die; // don't load this file directly

add_action('init','gn_register_scripts');

function gn_register_scripts() {

	wp_register_script( 'get_notified', 			GN_URL . 'scripts/get-notified.js', array(), false, false );
	wp_register_script( 'gn_modal', 				GN_URL . 'scripts/gn-modal-2.2.js', array( 'jquery' ), false, true );
	
	wp_register_style( 'get_notified', 				GN_URL . 'css/get-notified-2.2.css' );
	wp_register_style( 'phpbaba_admin_interface', 	GN_URL . 'css/phpbaba-admin-settings-interface-1.2.css' );
	
}

add_action( 'wp_enqueue_scripts', 'gn_enqueue_scripts' );

function gn_enqueue_scripts(){
	
	wp_enqueue_script( 'get_notified' );
	wp_enqueue_script( 'gn_modal' );
	
	wp_enqueue_style( 'get_notified' );
	
	wp_localize_script( 'get_notified', 'GetNotified', array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'authorize' => __('Authorize','get-notified'),
			'processing' => __('Processing...','get-notified'),
			'fb_authorize' => __('Oops! Couldn\'t connect. You must authorize.','get-notified')
	));
	
}


?>