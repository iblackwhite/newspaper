<?php 

	
add_action('wp_footer','gn_authorization_modal', 3);

function gn_authorization_modal(){
	
	$content = get_option('gn_pre_authorization_message');
	
?>

<div id="gn_overlay"></div>

<div id="gn_authorization_modal" class="gn_authorization_modal">
	<a class="gn_close_modal">&#215;</a>
	<div class="gn_authorization_modal_content">
		<?php echo $content; ?>
		<a id="gn_subscription_button" class="gn_button" href="javascript:void(0);" onclick="GN_FB_Connect('99');"><?php _e( 'Authorize', 'get-notified' ); ?></a>
	</div>
</div>

<?php
	
}

?>