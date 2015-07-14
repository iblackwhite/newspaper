<?php 

	
add_action('wp_footer','gn_fb_connect_script', 2);

function gn_fb_connect_script(){
?>
<script type="text/javascript">
	
	function gn_show_processing(){
		
		jQuery("#gn_subscription_button").html(GetNotified.processing);
		
	}
	
	function gn_hide_processing(){
		
		jQuery("#gn_subscription_button").html(GetNotified.authorize);
		
	}
	
	function GN_FB_Connect( wp_log ){
				
		gn_show_processing();
		
		FB.login(function(response) {
			
			if (response.authResponse) {
				
				var postdata = { 
				'action': 'gn_fb_connect', 
				'fb_AccessToken': response.authResponse.accessToken,
				'fb_id': response.authResponse.userID, 
				'nonce': '<?php echo wp_create_nonce('gn_fb_connect'); ?>' };
				
				postdata.wp_log = wp_log;
				
				GN_Site_Connect( postdata );
				
			} else {
				
				gn_hide_processing();
				alert( GetNotified.fb_authorize );
				
			}
			
		}, {scope: 'email', return_scopes: true});
		
	}

	function GN_Site_Connect( postdata ){
		
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: GetNotified.ajaxurl,
			data: postdata
		}).done(function(data){
			
			if( typeof data.ajax != 'undefined'){
				
				if (data.success == 1){

					jQuery(".gn_authorization_modal_content").html( data.msg );
					
					jQuery(".gn_show_authorization_modal").each(function( i, el ){
						
						if( ( jQuery(this).attr('hide_on_subscription') ) != 0){
							
							jQuery(this).hide();
							
						}
						
					});
					
				}else{
					
					alert(data.msg);
				
				}
			
			}else{
			
				alert(data);
				
			}
		
		}).always(function(){
			
			gn_hide_processing();
			
		});
		
	}
</script>
<?php
	
}

?>