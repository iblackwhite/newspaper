<?php

add_action( 'add_meta_boxes', 'gn_fb_notification_metabox' );

function gn_fb_notification_metabox( $post_type ) {
	
	$post_types = get_post_types( array( 'public' => true ) );
	
	if( in_array($post_type, $post_types) ){
		
		add_meta_box(
			'gn_fb_notification_metabox',
			__( 'Get Notified - Send notification', 'get-notified' ),
			'gn_fb_notification_metabox_callback',
			$post_type,
			'normal',
			'high'
		);
		
	}
	

}

function gn_fb_notification_metabox_callback( $post ) {
	
	wp_nonce_field( 'gn_fb_notification', 'gn_nonce' );
?>
	<p><?php _e( 'This sends a notification to your Get Notified subscribers inviting them to view this page within Facebook.', 'get-notified' ); ?></p>
	
	<div class="phpbaba_success_box sending_processing" style="display: none;"> <p>  </p> </div>
	<p class="gn_play_resume_buttons" style="text-align: right; display: none;">
		<input plan="pause" type="button" value="<?php _e( 'Pause Sending', 'get-notified' ); ?>" class="button button-primary" />
		<input plan="resume" type="button" value="<?php _e( 'Resume Sending', 'get-notified' ); ?>" class="button button-primary" />
		<input plan="cancel" type="button" value="<?php _e( 'Cancel Sending', 'get-notified' ); ?>" class="button button-red" />
	</p>
	
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<?php _e( 'Facebook Notification Message', 'get-notified' ); ?>
				</th>
				<td>
					<input type="text" maxlength="180" placeholder="<?php _e( 'Plain text (upto 180 characters)', 'get-notified' ); ?>" value="<?php echo esc_attr( get_post_meta($post->ID, 'gn_fb_notification_template', true) ); ?>" name="gn_fb_notification_template" id="gn_fb_notification_template" class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<a class="button button-primary gn_fb_notification_action" type="preview"><?php _e( 'Preview List', 'get-notified' ); ?></a>
				</th>
				<td>
					<a id="send_notification_button" class="button button-primary gn_fb_notification_action" type="send"><?php _e( 'Send Notification', 'get-notified' ); ?></a>
				</td>
			</tr>
		</tbody>
	</table>
	<style type="text/css">
		#TB_ajaxContent{
			width: auto !important;
			max-height: 600px !important;
		}
	</style>
	<?php add_thickbox(); ?>
	<div id="gn_preview" style="display: none;"></div> 
	<a id="gn_preview_opener" href="#TB_inline?width=800&height=700&inlineId=gn_preview" class="thickbox" style="display:none;"><?php _e( 'Preview', 'get-notified' ); ?></a>
	
	
	<script type="text/javascript">
		jQuery(document).ready(function(){
			
			<?php 
		
			$gn_db = new GN_DB();
			$notification_logs = $gn_db->check_pending_logs( array( 'post_id' => $post->ID ) );
			
			?>
			
			<?php if( $notification_logs ){ ?>
				
				<?php $session_report = $gn_db->get_notification_session_report( $notification_logs[0]->session ); ?>
				jQuery('.sending_processing').show().children('p').html( "<?php printf( __( '%s of %s notification(s) Sent, %s Failed, %s Completed.', 'get-notified' ), '<b>' . $session_report->sent . '</b>', '<b>' . $session_report->total . '</b>', '<b>' . $session_report->failed . '</b>', '<b>' . ( $session_report->total - $session_report->pending ) . '</b>'); ?>" );
				var Session = "<?php echo $notification_logs[0]->session; ?>";
			
			<?php }else{ ?>
				
				var Session = false;
			
			<?php } ?>
			
			var sending = false;
			var processing = false;
			var SessionProblem = 0;
			
			
			window.onbeforeunload = function () {
				
				if(sending == false)
					return;
				
				return "<?php _e( 'Leaving this page will pause your sending until you return and resume.', 'get-notified' ); ?>";
				
			}
			
			function gn_show_pause_resume_buttons(){
				
				jQuery( '.gn_play_resume_buttons' ).show();
				
			}
			
			function gn_hide_pause_resume_buttons(){
				
				jQuery( '.gn_play_resume_buttons' ).hide();
				
			}
			
			function gn_start_processing( button ){
				
				button.html("<?php _e( 'Please Wait...', 'get-notified' ); ?>");
				processing = true;
				
			}
			
			function gn_stop_processing( button, text ){
				
				processing = false;
				button.html(text);
				
			}
			
			function gn_OnPauseSending(){

				sending = false;
				
			}
			
			function gn_OnResumeSending(){

				sending = true;
				gn_send_notifications_callback();
				
			}
			
			function gn_OnCancelSending(){
				
				sending = false;
				
				jQuery.ajax({
					
					type: 'POST',
					url: ajaxurl,
					data: { 
						'action'			: 'gn_ajax_cancel_notifications',
						'session'			: Session,
						'nonce'				: jQuery("#gn_nonce").val()
						}
					
				}).done(function(data){
					
					var button = jQuery("#send_notification_button");
					
					if( data.indexOf("Error!") != -1 || data.indexOf("</table>") == -1 ){

						alert( "<?php _e( 'Please refresh the page and try again.', 'get-notified' ); ?>" );
					
					}else {
					
						gn_stop_processing( button, "<?php _e( 'Send Notification', 'get-notified' ); ?>" );
						gn_hide_pause_resume_buttons();
						gn_show_modal_data( data );
						
					}
					
				});
				
			}
			
			function gn_send_notifications_callback(){
				
				if(sending != true)
					return;
				
				gn_send_notifications();
				
			}
			
			function gn_send_notifications(){
				
				jQuery.ajax({
					
					type: 'POST',
					url: ajaxurl,
					data: { 
						'action'			: 'gn_ajax_send_notifications',
						'session'			: Session,
						'nonce'				: jQuery("#gn_nonce").val()
						}
					
				}).done(function(data){
			
					var button = jQuery("#send_notification_button");
					
					if( data.indexOf("Error!") != -1 ){
						
						sending = false;
						alert( "<?php _e( 'Please refresh the page and try again.', 'get-notified' ); ?>" );
					
					}else if( data.indexOf("</table>") != -1 || data.indexOf("</tr>") != -1 ){
							
						gn_stop_processing( button, "<?php _e( 'Send Notification', 'get-notified' ); ?>" );
						sending = false;
						gn_hide_pause_resume_buttons();
						gn_show_modal_data( data );
						
					}else if( data.indexOf("SessionProblem") != -1 ){
						
						++SessionProblem;
						if( SessionProblem >= 5 ){
							
							sending = false;
							alert( "<?php _e( 'Failed to create session with facebook. Please refresh the page.', 'get-notified' ); ?>" );
							return;
							
						}
						
						gn_send_notifications_callback();
						
					}else if( data.indexOf("Processing...") != -1 ){
						
						SessionProblem = 0;
						gn_send_notifications_callback();
						jQuery('.sending_processing').show().children('p').html( data );
					
					}else{
						
						gn_stop_processing( button, "<?php _e( 'Send Notification', 'get-notified' ); ?>" );
						
					}
					
				});
				
			}
			
			function gn_show_modal_data( data ){
				
				jQuery("#gn_preview").html(data);
				jQuery('#gn_preview_opener').trigger('click');
				
				var TB_WIDTH = 800,
					TB_HEIGHT = 700; // set the new width and height dimensions here..
				jQuery("#TB_window").animate({
					marginLeft: '-' + parseInt((TB_WIDTH / 2), 10) + 'px',
					width: TB_WIDTH + 'px',
					height: TB_HEIGHT + 'px'
				});
				
			}
			
			jQuery('.gn_fb_notification_action').click(function(e){
				e.preventDefault();
				
				if(processing == true){
					
					alert("<?php _e( 'Another function is in process. Please wait...', 'get-notified' ); ?>");
					return false;
					
				}
				
				var postdata = { 
						'action'			: 'gn_single_post_fb_notification',
						'post_id'			: <?php echo $post->ID; ?>,  
						'type' 				: jQuery(this).attr('type'),
						'template' 			: jQuery("#gn_fb_notification_template").val(),
						'nonce'				: jQuery("#gn_nonce").val()
						}
				
				if( postdata.type == 'send' && '<?php echo $post->post_status; ?>' != 'publish' ){
					
					var ask = confirm("<?php _e( 'This post is not published yet, Do you want to send notification?', 'get-notified' ); ?>");
						
					if(ask != true){ return; }
					
				}
				
				var button = jQuery(this);
				var button_old_text = button.html();
				gn_start_processing( button );

				Session = jQuery.now();
				postdata.session = Session;
				
				jQuery.ajax({
					
					type: 'POST',
					url: ajaxurl,
					data: postdata
					
				}).done(function(data){
					
					if( postdata.type == 'send' && data.indexOf("Processing...") != -1 ){
						
						jQuery('.sending_processing').show().children('p').html( data );
						gn_OnResumeSending();
						gn_show_pause_resume_buttons();
						
					}else{
						
						gn_stop_processing( button, button_old_text );
						gn_show_modal_data( data );
						
					}
					
				});
				
			});
			
			jQuery( '.gn_play_resume_buttons input' ).click(function(e){
				e.preventDefault();
				
				var plan = jQuery(this).attr('plan');
				
				if( plan == 'pause' ){
					gn_OnPauseSending();
				}else if( plan == 'resume' ){
					gn_OnResumeSending();
				}else if( plan == 'cancel' ){
					gn_OnCancelSending();
				}
				
			});

			if(Session != false){
				
				gn_start_processing( jQuery("#send_notification_button") );
				gn_show_pause_resume_buttons();
				
			}
			
		});
	</script>
	
<?php
}

add_action( 'save_post', 'gn_save_fb_notification_metabox' );

function gn_save_fb_notification_metabox( $post_id ) {
	
	if ( ! isset( $_POST['gn_nonce'] ) )
		return $post_id;

	if ( ! wp_verify_nonce( $_POST['gn_nonce'], 'gn_fb_notification' ) )
		return $post_id;
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return $post_id;

	update_post_meta( $post_id, 'gn_fb_notification_template', sanitize_text_field( $_POST['gn_fb_notification_template'] ) );

}



?>