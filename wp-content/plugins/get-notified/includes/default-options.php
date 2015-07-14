<?php

if( !defined('GN_VERSION') )
	die; // don't load this file directly

class GN_Default_Options {
	
	public function notify_buttons() {
		
		$defaults = array(
					'static' => array(
						'static_1' => GN_URL . 'images/buttons/button1.jpg',
						'static_2' => GN_URL . 'images/buttons/button2.jpg',
						'static_3' => GN_URL . 'images/buttons/button3.jpg',
						'static_4' => GN_URL . 'images/buttons/button4.jpg',
						'static_5' => GN_URL . 'images/buttons/button5.jpg',
						'static_6' => GN_URL . 'images/buttons/button6.jpg'
					),
					'animated' => array(
						'animated_1' => GN_URL . 'images/buttons/animated/button1.gif',
						'animated_2' => GN_URL . 'images/buttons/animated/button2.gif',
						'animated_3' => GN_URL . 'images/buttons/animated/button3.gif'
					)
					
		);
		
		return $defaults;
		
	}

	public function general_options() {
		
		$defaults = array(
			'fb_app_id'			=>	'',
			'fb_app_secret'		=>	'',
			'fb_sdk_lang'		=>	'en_US'
		);
		
		return $defaults;
		
	}
	
	public function display_options() {
		
		$defaults = array(
			'display_dynamically'			=>	0,
			'display_in_post_types'			=>	array(),
			'button_position'				=>	'after-content',
			'hide_on_subscription'			=>	0,
			'notify_button'					=>	GN_URL . 'images/buttons/button4.jpg',
			'custom_notify_button'			=>	''
		);
		
		return $defaults;
		
	}
	
	public function pre_authorization_message() {
		
		return '<h2 class="gn_heading">Get Notified</h2>
		<p>Don\'t clutter your email. Get notified in facebook when we post something new to share with you. You\'ll stay upto date.</p>';
		
	}
	
	public function post_authorization_message() {
		
		return '<p>Thanks for your subscription, You\'ll be notified as soon as worthy information or updates are availabe.</p>';
		
	}
	
}

?>