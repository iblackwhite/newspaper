<?php
/**
 * @package Get Notified - Viral Facebook Notifications
 */
/*
Short Codes
*/

if( !defined('GN_VERSION') )
	die; // don't load this file directly


add_action( 'init', 'gn_register_shortcodes');

function gn_register_shortcodes(){
	
   add_shortcode('get-notified-button', 'gn_button_shortcode');
   
}

function gn_button_shortcode( $atts ) {
	
		extract(
			shortcode_atts(
					array(
						'button' => ''
					), $atts
			)
		);

		$GN_FB_Connect = new GN_FB_Connect();
		$content = $GN_FB_Connect->display_fb_connect( array( 'button' => $button ) );

		$content = '<p>'.$content.'</p>';
		return $content;	
		
}

?>