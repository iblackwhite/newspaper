<?php
/**
Admin General Options
*/


/* ------------------------------------------------------------------------ *
 * Settings Registration
 * ------------------------------------------------------------------------ */ 

function gn_initialize_authorization_settings() {

	
	/*----------------------------General Settings Section---------------------------------------*/

	add_settings_section(
		'gn_pre_authorization_message',		// ID used to identify this section and with which to register options
		'',									// Title to be displayed on the administration page
		'gn_pre_authorization_message_callback',	// Callback used to render the description of the section
		'gn_authorization_settings'				// Page on which to add this section of options
	);
	
	add_settings_section(
		'gn_post_authorization_message',		// ID used to identify this section and with which to register options
		'',									// Title to be displayed on the administration page
		'gn_post_authorization_message_callback',	// Callback used to render the description of the section
		'gn_authorization_settings'				// Page on which to add this section of options
	);

	// Finally, we register the fields with WordPress
	register_setting( 'gn_authorization_settings', 'gn_pre_authorization_message', 'gn_sanitize_html' ); // 1)Settings Group, 2)Setting Name, 3) Sanitize Callback
	register_setting( 'gn_authorization_settings', 'gn_post_authorization_message', 'gn_sanitize_html' ); // 1)Settings Group, 2)Setting Name, 3) Sanitize Callback

	
}
add_action( 'admin_init', 'gn_initialize_authorization_settings' );



/* ------------------------------------------------------------------------ *
 * Callbacks
 * ------------------------------------------------------------------------ */ 

function gn_pre_authorization_message_callback( $args ) {
	
	echo '<h2>' . __( 'User Instructions:', 'get-notified' ) . '</h2>';
	
	echo '<p>' . __( 'A little hand holding goes a long way... We\'ll show subscribers an explanation of what Get Notified does, and thank them for subscribing afterwards. You can edit these Pre/Post instructions below.', 'get-notified' ) . '</p>';
	
	echo '<h3>' . __( 'Pre-instructions message', 'get-notified' ) . '</h3>';

	$content = get_option('gn_pre_authorization_message');
	
	wp_editor( $content, 'gn_pre_authorization_message', array( 'textarea_rows' => 10, 'wpautop' => false ) );

}

function gn_post_authorization_message_callback( $args ) {
	
	echo '<br />';
	echo '<h3>' . __( 'Post-instructions message', 'get-notified' ) . '</h3>';

	$content = get_option('gn_post_authorization_message');
	
	wp_editor( $content, 'gn_post_authorization_message', array( 'textarea_rows' => 10, 'wpautop' => false ) );

}

?>