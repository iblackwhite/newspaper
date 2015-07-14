<?php 

	
add_action('wp_head','GN_FB_Load_Head', 1000);

function GN_FB_Load_Head(){
	
	$options = get_option('gn_general_options');
	$appId = $options['fb_app_id'];
	$sdk_lang = !empty( $options['fb_sdk_lang'] ) ? $options['fb_sdk_lang'] : 'en_US';
	
?>
<script type="text/javascript">
	
	window.fbAsyncInit = function() {

		FB.init({appId: '<?php echo $appId; ?>', status: true, cookie: true, xfbml: true});
		
		<?php do_action( 'gn_fb_sdk_modules' ); ?>

	};
	
	
	(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/<?php echo $sdk_lang; ?>/all.js";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	
</script>

<?php
	
}

add_action('wp_footer','GN_FB_Load_Footer', 1);

function GN_FB_Load_Footer(){
	
	echo '<div id="fb-root"></div>';
	
}

?>