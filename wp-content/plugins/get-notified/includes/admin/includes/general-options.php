<?php
/**
Admin General Options
*/


/* ------------------------------------------------------------------------ *
 * Settings Registration
 * ------------------------------------------------------------------------ */ 

function gn_initialize_general_options() {

	
	/*----------------------------General Settings Section---------------------------------------*/

	add_settings_section(
		'gn_general_options_section',		// ID used to identify this section and with which to register options
		'',									// Title to be displayed on the administration page
		'gn_general_options_section_callback',	// Callback used to render the description of the section
		'gn_general_options'				// Page on which to add this section of options
	);
	
		add_settings_field(	
			'gn_fb_app_id',						// ID used to identify the field throughout the theme
			__( 'Facebook application id', 'get-notified' ), // The label to the left of the option interface element
			'gn_fb_app_id_callback',			// The name of the function responsible for rendering the option interface
			'gn_general_options',				// The page on which this option will be displayed
			'gn_general_options_section'		// The name of the section to which this field belongs
		);
		
		add_settings_field(	
			'gn_fb_app_secret',						
			__( 'Facebook application secret', 'get-notified' ),				
			'gn_fb_app_secret_callback',	
			'gn_general_options',					
			'gn_general_options_section'
		);
		
		add_settings_field(	
			'gn_fb_sdk_lang',						
			__( 'Facebook SDK Locale', 'get-notified' ),				
			'gn_fb_sdk_lang_callback',	
			'gn_general_options',					
			'gn_general_options_section'
		);
	
	// Finally, we register the fields with WordPress
	register_setting( 'gn_general_options', 'gn_general_options' ); // 1)Settings Group, 2)Setting Name, 3) Sanitize Callback

	
}
add_action( 'admin_init', 'gn_initialize_general_options' );



/* ------------------------------------------------------------------------ *
 * Sections Callbacks
 * ------------------------------------------------------------------------ */ 

function gn_general_options_section_callback() {
	echo '<h2>' . __( 'Facebook App Settings', 'get-notified' ) . '</h2>';
	echo '<p>' . sprintf( __( 'You can create a new app from the %s facebook developer page. %s', 'get-notified' ), '<a href="https://developers.facebook.com/apps" target="_blank">', '</a>' ) . '</p>';
}


 /* ------------------------------------------------------------------------ *
 * Fields Callbacks
 * ------------------------------------------------------------------------ */ 

function gn_fb_app_id_callback( $args ) {

	$options = get_option('gn_general_options');
	
	$html = '<input style="width: 95%;" type="text" name="gn_general_options[fb_app_id]" value="' . $options['fb_app_id'] . '" />';
	$html .= '<span class="phpbaba_form_hint">' . sprintf( __( '* Please note; the subscribers you acquire with an app ID will not be notified if you change the app ID. Already using Facebook connect or other FB app? You can use the same APP ID, just be sure to add "CANVAS" to it. %s Click here for instructions. %s', 'get-notified' ), '<a href="http://phpbaba.com/gn-docs/#!/fb_canvas" target="_blank">', '</a>' ) . '</span>';
	
	echo $html;

}

function gn_fb_app_secret_callback( $args ) {

	$options = get_option('gn_general_options');
	
	$html = '<input style="width: 95%;" type="text" name="gn_general_options[fb_app_secret]" value="' . $options['fb_app_secret'] . '" />';
	
	echo $html;

}

function gn_fb_sdk_lang_callback( $args ) {

	$options = get_option('gn_general_options');
	
	$lang = $options['fb_sdk_lang'] ? $options['fb_sdk_lang'] : 'en_US';
	
	$html = '<input type="text" name="gn_general_options[fb_sdk_lang]" value="' . $lang . '" />';
	$html .= '<br/><span>'. sprintf( __( 'Read more about %s SDK Localization.%s', 'get-notified' ), '<a href="https://developers.facebook.com/docs/internationalization" target="_blank">', '</a>' ) .'</span>';
	
	echo $html;

}


?>