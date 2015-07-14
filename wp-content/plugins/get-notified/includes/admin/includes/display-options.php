<?php
/**
Admin General Options
*/


/* ------------------------------------------------------------------------ *
 * Settings Registration
 * ------------------------------------------------------------------------ */ 

function gn_initialize_display_options() {

	
	/*----------------------------General Settings Section---------------------------------------*/

	add_settings_section(
		'gn_dynamically_display_options_section',		// ID used to identify this section and with which to register options
		'',									// Title to be displayed on the administration page
		'gn_dynamically_display_options_section_callback',	// Callback used to render the description of the section
		'gn_display_options'				// Page on which to add this section of options
	);
	
		add_settings_field(	
			'gn_display_button_in_posts',						// ID used to identify the field throughout the theme
			__( 'Insert in posts/pages', 'get-notified' ), 			// The label to the left of the option interface element
			'gn_display_button_in_posts_callback',				// The name of the function responsible for rendering the option interface
			'gn_display_options',								// The page on which this option will be displayed
			'gn_dynamically_display_options_section'			// The name of the section to which this field belongs
		);
		
		add_settings_field(	
			'gn_display_button_in_post_types',
			__( 'Display in posts types', 'get-notified' ),
			'gn_display_button_in_post_types_callback',
			'gn_display_options',
			'gn_dynamically_display_options_section'
		);
		
		add_settings_field(	
			'gn_display_button_position',
			__( 'Display position', 'get-notified' ),
			'gn_display_button_position_callback',
			'gn_display_options',
			'gn_dynamically_display_options_section'
		);
		
		add_settings_field(	
			'gn_hide_on_subscription',
			__( 'Hide after subscription', 'get-notified' ),
			'gn_hide_on_subscription_callback',
			'gn_display_options',
			'gn_dynamically_display_options_section'
		);
	
	add_settings_section(
		'gn_manual_display_section',		// ID used to identify this section and with which to register options
		'',									// Title to be displayed on the administration page
		'gn_manual_display_section_callback',	// Callback used to render the description of the section
		'gn_display_options'				// Page on which to add this section of options
	);
		
	add_settings_section(
		'gn_notify_button_section',		// ID used to identify this section and with which to register options
		'',									// Title to be displayed on the administration page
		'gn_notify_button_section_callback',	// Callback used to render the description of the section
		'gn_display_options'				// Page on which to add this section of options
	);
	
	// Finally, we register the fields with WordPress
	register_setting( 'gn_display_options', 'gn_display_options' ); // 1)Settings Group, 2)Setting Name, 3) Sanitize Callback

	
}
add_action( 'admin_init', 'gn_initialize_display_options' );



/* ------------------------------------------------------------------------ *
 * Callbacks
 * ------------------------------------------------------------------------ */ 

function gn_dynamically_display_options_section_callback() {
	echo '<h2>' . __( 'Automatic insertion:', 'get-notified' ) . '</h2>';
}


function gn_display_button_in_posts_callback( $args ) {

	$options = get_option('gn_display_options');
	
	//$html = '<input type="radio" id="display_dynamically_enable" name="gn_display_options[display_dynamically]" value="1"' . checked( 1, $options['display_dynamically'], false ) . '/>';
	//$html .= '<label for="display_dynamically_enable">Enable &nbsp;&nbsp;&nbsp;</label>';
	//$html .= '&nbsp;';
	//$html .= '<input type="radio" id="display_dynamically_disable" name="gn_display_options[display_dynamically]" value="0"' . checked( 0, $options['display_dynamically'], false ) . '/>';
	//$html .= '<label for="display_dynamically_disable">Disable &nbsp;&nbsp;&nbsp;</label>';
	
	$html = '<select name="gn_display_options[display_dynamically]">';
		$html .= '<option value="1"' . selected( $options['display_dynamically'], 1, false) . '>' . __( 'Enable', 'get-notified' ) . '</option>';
		$html .= '<option value="0"' . selected( $options['display_dynamically'], 0, false) . '>' . __( 'Disable', 'get-notified' ) . '</option>';
	$html .= '</select>';
	
	echo $html;

}

function gn_display_button_in_post_types_callback( $args ) {

	$options = get_option('gn_display_options');
	
	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	
	$html = '';
	$html .= '<input type="hidden" name="gn_display_options[display_in_post_types][]" value="pb_dummy" checked="checked"/>';
	foreach ( $post_types  as $post_type ) {
		
		$checked = is_array( $options['display_in_post_types'] ) && in_array( $post_type->name, $options['display_in_post_types'] ) ? 'checked="checked"' : '';
		
		$html .= '<input type="checkbox" id="post_type_'.$post_type->name.'" name="gn_display_options[display_in_post_types][]" value="'.$post_type->name.'" '.$checked.'/>';
		$html .= '<label for="post_type_'.$post_type->name.'">'.$post_type->label.' &nbsp;&nbsp;&nbsp;</label>';
	
	}
	
	echo $html;

}

function gn_display_button_position_callback( $args ) {
	
	$options = get_option('gn_display_options');
	
	$html = '<select name="gn_display_options[button_position]">';
		$html .= '<option value="after-content"' . selected( $options['button_position'], 'after-content', false) . '>' . __( 'After post content', 'get-notified' ) . '</option>';
		$html .= '<option value="before-content"' . selected( $options['button_position'], 'before-content', false) . '>' . __( 'Before post content', 'get-notified' ) . '</option>';
	$html .= '</select>';
	
	echo $html;

}


function gn_hide_on_subscription_callback( $args ) {
	
	$options = get_option('gn_display_options');
	
	$html = '<input type="radio" id="hide_on_subscription_yes" name="gn_display_options[hide_on_subscription]" value="1"' . checked( 1, $options['hide_on_subscription'], false ) . '/>';
	$html .= '<label for="hide_on_subscription_yes">Yes &nbsp;&nbsp;&nbsp;</label>';
	$html .= '&nbsp;';
	$html .= '<input type="radio" id="hide_on_subscription_no" name="gn_display_options[hide_on_subscription]" value="0"' . checked( 0, $options['hide_on_subscription'], false ) . '/>';
	$html .= '<label for="hide_on_subscription_no">No &nbsp;&nbsp;&nbsp;</label>';
	$html .= '<span class="phpbaba_form_hint">' . __( 'Hide "Get Notified" button if user has already subscribed', 'get-notified' ) . '</span>';
	
	echo $html;

}


function gn_manual_display_section_callback() {
	
	$html = '<h2>' . __( 'Manual insertion', 'get-notified' ) . '</h2>';
	
	$html .= '<p>' . sprintf( __( 'To insert a notification button anywhere, simply paste the following shortcode in your content editor: %s', 'get-notified' ), '<pre><b style="font-size: 1.1em;">[get-notified-button]</b></pre>' ). '</p>';
	
	$html .= '<h3>' . __( 'Advanced insertion', 'get-notified' ) . '</h3>';
	
	$html .= '<p>' . sprintf( __( 'If you need to call the notification button with more control, such as by editing code of WP or custom integration, use the following snippet of code: %s', 'get-notified' ), '<pre><b style="font-size: 1.1em;">&lt;?php echo do_shortcode( \'[get-notified-button]\' ); ?&gt;</b></pre>' ). '</p>';
	$html .= '<br/>';
	
	echo $html;
}

function gn_notify_button_section_callback() {

	$options = get_option('gn_display_options');
	
	include_once GN_DIR . 'includes/default-options.php';
	
	$defaults = new GN_Default_Options();
	$default_buttons = $defaults->notify_buttons();
	
	$html = '<h2>' . __( 'Select a button', 'get-notified' ) . '</h2>';
	
	$html .= '<p>' . __( 'Select the button you would like displayed or upload your own. If selecting an animated button, we suggest the "Hide after subscription" option.', 'get-notified' ). '</p>';
	
	$html .= '<h3>' . __( 'Static', 'get-notified' ) . '</h3>';
	
	foreach( $default_buttons['static'] as $k => $button ){
		
		$html .= '<input type="radio" id="'.$k.'" name="gn_display_options[notify_button]" value="'.$button.'"' . checked( $button, $options['notify_button'], false ) . '/>';
		$html .= '<label for="'.$k.'"><img style="max-width: 150px; height: auto;" src="'.$button.'" /> &nbsp;&nbsp;&nbsp;</label>';
		
	}
	
	$html .= '<h3>' . __( 'Animated', 'get-notified' ) . '</h3>';
	
	foreach( $default_buttons['animated'] as $k => $button ){
		
		$html .= '<input type="radio" id="'.$k.'" name="gn_display_options[notify_button]" value="'.$button.'"' . checked( $button, $options['notify_button'], false ) . '/>';
		$html .= '<label for="'.$k.'"><img style="max-width: 150px; height: auto;" src="'.$button.'" /> &nbsp;&nbsp;&nbsp;</label>';
		
	}
	
	$html .= '<h3>' . __( 'OR (upload custom image)', 'get-notified' ) . '</h3>';
	
	$display = empty( $options['custom_notify_button'] ) ? ' style="display: none;"' : ' style="display: inline;"';
	
	$html .= '<div id="custom_notify_button_outer"'.$display.'>';
		$html .= '<input type="radio" id="custom_notify_button" name="gn_display_options[notify_button]" value="'.$options['custom_notify_button'].'"' . checked( $options['custom_notify_button'], $options['notify_button'], false ) . '/>';
		$html .= '<label for="custom_notify_button"><img id="custom_notified_image" src="'.$options['custom_notify_button'].'" /> &nbsp;&nbsp;&nbsp;</label>';
	$html .= '</div>';
	$html .= '<input id="custom_notify_button_url" type="hidden" name="gn_display_options[custom_notify_button]" value="' . $options['custom_notify_button'] . '" />';
	$html .= '<input id="custom_notify_button_uploader" type="button" class="button button-primary" value="' . __( 'Upload Image', 'get-notified' ) . '" />';
	
	
	echo $html;
}


?>