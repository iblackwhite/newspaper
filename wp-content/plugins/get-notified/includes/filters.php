<?php
/**
 * @package Get Notified - Viral Facebook Notifications
 */
/*
Filters
*/

if( !defined('GN_VERSION') )
	die; // don't load this file directly


add_filter( 'the_content', 'gn_display_button_in_post');

function gn_display_button_in_post( $content ){
	
   //Display only on singular pages
	if(is_singular()) {
		
		$options = get_option( 'gn_display_options', array() );
		
		if( isset( $options[ 'display_dynamically' ] ) && $options[ 'display_dynamically' ] == 1 ){
			
			if( in_array( $GLOBALS['post']->post_type, $options['display_in_post_types'] ) ){
				
				$gn_button = do_shortcode( '[get-notified-button]' );
				
				$content = $options[ 'button_position' ] == 'before-content' ? $gn_button . $content : $content . $gn_button ;
				
			}
			
		}
	
	}
   
   return $content;
   
}

/**
 * WooCommerce Cart Page URL FIX for FB Canvas
 * Since: 2.2
 * Updated: 2.2
 */

add_filter( 'woocommerce_get_cart_url', 'gn_wc_secure_cart_page', 10, 1);

function gn_wc_secure_cart_page( $url ){
	
	return gn_https_replacement( $url );
	
}

?>