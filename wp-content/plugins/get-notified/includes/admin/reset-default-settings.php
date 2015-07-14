<?php

add_action('wp_ajax_gn_restore_default_settings','gn_restore_default_settings');

function gn_restore_default_settings(){
	
	$success = __('Default settings restored.', 'get-notifieed');
	$cheating = __('Cheating.', 'get-notifieed');
	
	if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'gn_settings_nonce' ) )
		die( __( 'Please refresh the page and try again.', 'get-notifieed' ) );
	
	if ( !current_user_can( 'manage_options' ) )
		die( $cheating );
	
	delete_option( 'gn_display_options' );
	delete_option( 'gn_pre_authorization_message' );
	delete_option( 'gn_post_authorization_message' );
	
	Get_Notified::register_default_settings();
	
	die( $success );
	
}

add_action( 'gn_settings_page_right_side', 'gn_display_restore_default_settings_button', 2 );

function gn_display_restore_default_settings_button( $active_tab ){
	
?>

	<div class="meta-box-sortables ui-sortable" id="side-sortables">

		<div class="postbox " id="submitdiv">

			<h3 class="hndle"><span><?php _e( 'Restore Default Settings', 'get-notifieed' ); ?></span></h3>
			
			<div class="inside">

				<div id="submitpost" class="submitbox">

					<div id="major-publishing-actions">

						<div id="publishing-action">

							<input type="button" id="gn_restore_default_settings" value="<?php _e( 'Restore Default Settings', 'get-notifieed' ); ?>" class="button button-primary button-large">
							<span class="phpbaba_form_hint"><?php _e( '(resets all tabs except Application settings)', 'get-notifieed' ); ?></span>
								
						</div>
							
						<div class="clear"></div>
						
					</div>
					
				</div>

			</div>
			
		</div>

	</div>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			var processing = false;
			
			jQuery("#gn_restore_default_settings").click(function(e){
				
				e.preventDefault();
				
				if(processing == true){ return; }
					
				var error_message = "<?php _e( 'Please refresh the page and try again.', 'get-notifieed' ); ?>";
				
				var confirm_reset = confirm("<?php _e( 'Are you sure, you want to restore default settings? (This action cannot be undone).', 'get-notifieed' ); ?>");
				if(confirm_reset != true){ return; }
				
				processing = true;
				
				jQuery.ajax({
					type: 'POST',
					url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
					data: { 
					'action': 'gn_restore_default_settings',
					'nonce': jQuery('#gn_nonce').html()
					}
				}).done(function(data){
					
					if(data == 0){ alert(error_message); return; }
					alert(data);
					if( data.indexOf("restored") >= 0 ){ location.reload(); }
					
				}).fail(function(data){
					
					alert(error_message);
					processing = false;
					
				});
				
			});
			
		});
	</script>
	
<?php
}
?>