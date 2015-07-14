<?php 

	
add_action('gn_fb_sdk_modules','gn_token_updation_script', 2);

function gn_token_updation_script(){
?>

	FB.getLoginStatus(function(response) {
		
		gn_setCookie( 'gn_token_updated', false, 1 );
		
		if (response.status === 'connected') {
		
			var postdata = { 
				'action': 'gn_update_token', 
				'fb_AccessToken': response.authResponse.accessToken,
				'fb_id': response.authResponse.userID, 
				'nonce': '<?php echo wp_create_nonce('gn_update_token'); ?>' };
			
			jQuery.ajax({
				type: 'POST',
				url: GetNotified.ajaxurl,
				data: postdata
			}).done(function(data){
				
				console.log(data);
			
			});
			
		}else if (response.status === 'not_authorized') {
			console.log(response);
		} else {
			console.log('Not connected');
		}
		
	 });

<?php
	
}

?>