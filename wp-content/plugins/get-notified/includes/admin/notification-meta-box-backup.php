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
	
	<?php 
			
		$gn_db = new GN_DB();
		$notification_logs = $gn_db->check_pending_logs( $post->ID );
	
	?>
	
	<?php if( $notification_logs ){ ?>
			
		<div id="notification_sending_table" style="display: none;">
			<?php echo gn_wrap_in_heading( 'Processing...', 'h1', 'sending_processing' ); ?>
			<?php echo gn_notification_logs_table( '', true ); ?>
		</div>
	
	<?php } ?>
	
	
	
	<script type="text/javascript">
		jQuery(document).ready(function(){
		
			var processing = false;
			var SessionProblem = 0;
			
			function gn_start_processing( button ){
				
				button.html("<?php _e( 'Please Wait...', 'get-notified' ); ?>");
				processing = true;
				
			}
			
			function gn_stop_processing( button, text ){
				
				processing = false;
				button.html(text);
				
			}
			
			function gn_send_notifications(){
				
				jQuery.ajax({
					
					type: 'POST',
					url: ajaxurl,
					data: { 
						'action'			: 'gn_ajax_send_notifications',
						'post_id'			: <?php echo $post->ID; ?>,
						'nonce'				: jQuery("#gn_nonce").val()
						}
					
				}).done(function(data){
			
					if( data.indexOf("</tr>") != -1 ){
						
						jQuery('#notification_logs_table').show();
						jQuery('#notification_logs_rows').append( data );
						
						gn_send_notifications_callback();
						
						SessionProblem = 0;
						
					}else{
						
						var button = jQuery("#send_notification_button");
						
						if( data.indexOf("FinishedProcessing") != -1 ){
							
							jQuery('.sending_processing').html( "<?php _e( 'Notifications sent successfully.', 'get-notified' ); ?>" );
							
							gn_stop_processing( button, "<?php _e( 'Send Notification', 'get-notified' ); ?>" );
							
						}else if( data.indexOf("SessionProblem") != -1 ){
							
							++SessionProblem;
							if( SessionProblem >= 4 ){
								
								alert( "<?php _e( 'Failed to create session with facebook. Please refresh the page.', 'get-notified' ); ?>" );
								return;
								
							}
							
							gn_send_notifications_callback();
							
						}else{
							
							gn_stop_processing( button, "<?php _e( 'Send Notification', 'get-notified' ); ?>" );
							
						}
						
						
					}
					
				});
				
				
			}
			
			function gn_send_notifications_callback(){

				gn_send_notifications();
				
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

				jQuery.ajax({
					
					type: 'POST',
					url: ajaxurl,
					data: postdata
					
				}).done(function(data){
						
					gn_show_modal_data( data );
					
					if( postdata.type == 'send' && data.indexOf("Processing...") != -1 ){
						
						gn_send_notifications_callback();
						
					}else{
						
						gn_stop_processing( button, button_old_text );
						
					}
					
					
				});
				
			});
			
			<?php if( $notification_logs ){ ?>
			
				gn_start_processing( jQuery("#send_notification_button") );
				
				jQuery(window).load(function(){
					
					var table = jQuery("#notification_sending_table").html();
					jQuery("#notification_sending_table").remove();
					gn_show_modal_data( table );
					gn_send_notifications_callback();
				});
			
			<?php } ?>
			
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