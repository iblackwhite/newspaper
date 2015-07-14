<?php 

// Insert WPTC Scripts and Styles
add_action('admin_enqueue_scripts', 'gn_enqueue_admin_scripts_and_styles');

function gn_enqueue_admin_scripts_and_styles( $hook_suffix ) {
	
	global $wp_version, $pagenow;
	
	if( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) )
		wp_enqueue_style( 'phpbaba_admin_interface' );
		
	$gn_admin_pages = gn_admin_pages();
	
	if ( !isset( $_GET['page'] ) || !array_key_exists( $_GET['page'], $gn_admin_pages ) )
		return;

	wp_enqueue_style( 'phpbaba_admin_interface' );

	if ( $wp_version >= 3.5 ){
		
		wp_enqueue_media();
		
	}else{
		
		wp_enqueue_script( 'media-upload', array('jquery') );
		
		//for media uploader
		wp_enqueue_style('thickbox');
		wp_enqueue_script( 'thickbox', array('jquery') );
	
	}
	
}

add_action('admin_footer','gn_load_scripts', 9999999999);

function gn_load_scripts() {
	
	global $wp_version;
	
	$gn_admin_pages = gn_admin_pages();
	
	if ( !isset( $_GET['page'] ) || !array_key_exists( $_GET['page'], $gn_admin_pages ) )
		return;
	
?>
	<script type="text/javascript">
	
		jQuery(document).ready(function(){

			<?php if ( $wp_version >= 3.5 ){ ?>

				var gn_uploader;
			 
				jQuery('#custom_notify_button_uploader').click(function(e) {
			 
					e.preventDefault();
					
					//If the uploader object has already been created, reopen the dialog
					if (gn_uploader) {
						gn_uploader.open();
						return;
					}
			 
					//Extend the wp.media object
					gn_uploader = wp.media.frames.file_frame = wp.media({
						multiple: false
					});
			 
					//When a file is selected, grab the URL and set it as the text field's value
					gn_uploader.on('select', function() {
						attachment = gn_uploader.state().get('selection').first().toJSON();
						
						jQuery('#custom_notified_image').attr('src', attachment.url);
						jQuery('#custom_notify_button_url').val( attachment.url );
						jQuery('#custom_notify_button').val( attachment.url ).prop('checked', true);
						jQuery('#custom_notify_button_outer').css( 'display', 'inline' );
						
					});
			 
					//Open the uploader dialog
					gn_uploader.open();
			 
				});
	
			<?php }else{ ?>

				jQuery('#custom_notify_button_uploader').click(function() {

					tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');

					return false;
				 
				});
				 
				window.send_to_editor = function(html) {
				
					imgurl = jQuery('img',html).attr('src');
					
					jQuery('#custom_notified_image').attr('src', imgurl);
					jQuery('#custom_notify_button_url').val( imgurl );
					jQuery('#custom_notify_button').val( imgurl ).prop('checked', true);
					jQuery('#custom_notify_button_outer').css( 'display', 'inline' );
					
					tb_remove();
					
				}
		
			<?php } ?>
	
		});	
		
	</script>
	
<?php
	
}//end wptc_load_scripts()

?>