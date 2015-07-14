<?php 

function gn_fb_userdata_list(){ 

?>

<div class="wrap" >
	
	<h2>Get Notified - Subscribers list</h2>
	
	<?php	
										
	$gn_db = new GN_DB();
	$users = $gn_db->get_fb_users();
	
	if( !$users ){
		
		echo gn_wrap_in_heading( __( '0 Subscribers', 'get-notified' ) );

	}else{
		
		$page_count = $gn_db->get_fb_users( 'page_count' );
		
		echo gn_wrap_in_heading( sprintf( __( '%d Subscribers', 'get-notified' ), $gn_db->total_users ) );
		
	?>
		<p style="text-align: right;">
			<input id="export_users" type="button" value="<?php _e( 'Export Subscribers List', 'get-notified' ); ?>" class="button button-primary" />
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Subscribed on', 'get-notified' ); ?></th>
					<th><?php _e( 'Name', 'get-notified' ); ?></th>
					<th><?php _e( 'Email', 'get-notified' ); ?></th>
					<th><?php _e( 'Facebook ID', 'get-notified' ); ?></th>
					<th colspan="2"><?php _e( 'Status', 'get-notified' ); ?></th>
				</tr>
			</thead>
			<tbody id="gn_users_list">
	
				<?php gn_print_users( $users ) ?>
			
			</tbody>
		</table>
		
		<script type="text/javascript">
			
			var gn_nonce = '<?php echo wp_create_nonce( 'gn_nonce' ); ?>';
			var count = 2;
			var total = <?php echo $page_count; ?>;
			
			var processing = false;
			
			function gn_LoadMoreUsers(page_no){   
				
				jQuery('.wrap').append('<h3 id="loading_more"><?php _e( 'Loading more subscribers...', 'get-notified' ); ?></h3>');
				
				jQuery.ajax({
					url: ajaxurl,
					type:'GET',
					data: { 
						action : 'gn_LoadMoreUsers',
						page_no : page_no,
						nonce : gn_nonce
					},
					success: function(html){
						jQuery('#loading_more').remove();
						jQuery('#gn_users_list').append(html);
						jQuery(window).bind( 'scroll', gn_bindScroll );
					}
				});
				
			}

			function gn_bindScroll(){
			 
				if(jQuery(window).scrollTop() + jQuery(window).height() > jQuery(document).height() - 200 ) {
				   
					jQuery(window).unbind('scroll', gn_bindScroll );
				   
					if (count > total){
						
						return false;
						
					}
					
					gn_LoadMoreUsers(count);
					count++;

				}
			   
			}
			
			jQuery(window).bind( 'scroll', gn_bindScroll );
			
			jQuery('#gn_users_list').on('click','.gn_delete_user',function(){

				var ask = confirm("<?php _e( 'Do you want to delete this subscriber?', 'get-notified' ); ?>");
						
				if(ask != true){ return; }
				
				var gn_id = jQuery(this).attr("gn_id");
				
				jQuery.ajax({
					url: ajaxurl,
					type:'POST',
					data: { 
						action : 'gn_delete_user',
						gn_id : gn_id,
						nonce : gn_nonce
					},
					success: function(html){
						
						jQuery("#gn_subscriber_" + gn_id).css('background', '#FFC0CB').fadeOut(1500);
					
					}
				});
				
				
			});
			
			function GN_JSON2CSV(objArray) {
				var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;

				var str = "\uFEFF";
				var line = "\uFEFF";

				var head = array[0];

				for (var index in array[0]) {
					line += index + ',';
				}

				
				line = line.slice(0, -1);
				str += line + '\r\n';

				for (var i = 0; i < array.length; i++) {
					var line = '';

					for (var index in array[i]) {
						line += array[i][index] + ',';
					}

					line = line.slice(0, -1);
					str += line + '\r\n';
				}
				return str;
				
			}
			
			jQuery("#export_users").click(function(e){
				e.preventDefault();
				
				if(processing == true){
					alert("<?php _e( 'Another function is already in process. Please wait...', 'get-notified' ); ?>");
					return false;
				}
				processing = true;
				
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					dataType: 'json',
					data: { 
						action : 'gn_export_users',
						nonce : gn_nonce
					}
					
				}).done(function(data){
						
					if( typeof data.ajax != 'undefined'){
						
						var csv = GN_JSON2CSV(data.data);
						var csvContent = "data:application/csv;charset=utf-8," + encodeURI(csv);
						
						//window.open(csvContent);
					
						var link         = document.createElement('a');
						link.href        = csvContent;
						link.target      = '_blank';
						link.download    = 'Get_Notified_Subscribers.csv';

						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
						
					}else{
						
						alert("<?php _e( 'Refresh page and try again.', 'get-notified' ); ?>");
						
					}

					processing = false;
				});
				
			});
			
		</script>
		
	<?php } ?>
	
</div>


<?php

}

function gn_print_users( $users ){
	
	if( !$users )
		die( gn_wrap_in_heading( __( 'No subscribers', 'get-notified' ) ) );
?>
	
	<?php foreach ($users as $user){ ?>
		
		<tr id="gn_subscriber_<?php echo $user->id; ?>">

			<td><?php echo $user->connected; ?></td>
			<td><?php echo $user->fb_name; ?></td>
			<td><?php echo $user->fb_email; ?></td>
			<td><?php echo $user->fb_id; ?></td>
			<td><?php echo gn_get_user_status( $user->user_status ); ?></td>
			<td><a title="<?php _e( 'Delete', 'get-notified' ); ?>" class="gn_delete_user" style="font-size: 30px; cursor: pointer;" gn_id="<?php echo $user->id; ?>">&#215;</a></td>
			
	   </tr>
	   
	<?php } ?>

<?php

}

add_action( 'wp_ajax_gn_LoadMoreUsers', 'gn_LoadMoreUsers' );

function gn_LoadMoreUsers(){
	
	check_ajax_referer( 'gn_nonce', 'nonce' );
	
	if ( !current_user_can('manage_options') )
		return false;

	$gn_db = new GN_DB();
	$users = $gn_db->get_fb_users( 'users', $_GET['page_no'] );
	
	gn_print_users($users);
	
	die;
	
}

add_action( 'wp_ajax_gn_delete_user', 'gn_delete_user_function' );

function gn_delete_user_function(){
	
	check_ajax_referer( 'gn_nonce', 'nonce' );
	
	if ( !current_user_can('manage_options') )
		return false;

	$gn_db = new GN_DB();
	$gn_db->delete_fb_user( array( 'id' => $_POST['gn_id'] ) );
	
	die( __( 'Deleted', 'get-notified' ) );
}

add_action( 'wp_ajax_gn_export_users', 'gn_export_users_function' );

function gn_export_users_function( ){
	
	check_ajax_referer( 'gn_nonce', 'nonce' );
	
	if ( !current_user_can('manage_options') )
		return false;
	
	$data = array();
	$data['ajax'] = true;

	$gn_db = new GN_DB();
	$users = $gn_db->get_fb_users( 'users', 1, 4444 );
	
	if( !$users )
		die( "No Subscribers found." );
	
	ini_set('max_execution_time', 0);
	
	$new_subscribers = array();
	
	foreach( $users as $key => $user ){
		
		$user_data = array();
		
		foreach( $user as $k => $val ){
			
			if( in_array( $k, array( 'user_id', 'fb_token', 'fb_token_expires', 'last_seen', 'fb_app' ) ) )
				continue;
			
			if( $k == 'user_status' )
				$val = gn_get_user_status( $val );

			$user_data[$k] = $val;
			
		}
		
		$new_subscribers[$key] = $user_data;
		
	}

	$data['data'] = $new_subscribers;
	echo json_encode($data);
	die;

}


?>