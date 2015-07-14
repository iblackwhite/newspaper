<?php 

add_action( 'wp_ajax_gn_single_post_fb_notification', 'gn_single_post_fb_notification' );

function gn_single_post_fb_notification(){
	
	check_ajax_referer( 'gn_fb_notification', 'nonce' );
	
	if( !isset( $_POST['post_id'] ) || !isset( $_POST['type'] ) || !isset( $_POST['template'] ) )
		die( gn_wrap_in_heading( __( 'Please refresh the page and try again.', 'get-notified' ) ) );
	
	global $wpdb;
	$gn_db = new GN_DB();
	$fb_userdata_table = $gn_db->fb_userdata_table();
	
	$post_id = (int)$_POST['post_id'];
	$type = $_POST['type'];
	$template = strip_tags( stripslashes( $_POST['template'] ) );
	$template = substr( $template, 0, 180 );
	$post_permalink = get_permalink( $post_id );
	$href = str_replace( home_url(), "", $post_permalink );
	
	$notification_session = time();
	$table = "";
	$rows = "";
	$notification_url = str_replace( 'http://', 'https://', $post_permalink );
	$notification_url = '<a href="'.$notification_url.'" target="_blank">'.$notification_url.'</a>';
	
	$query = "
		SELECT *
		FROM {$fb_userdata_table}
		WHERE fb_token_expires > NOW() AND user_status = 1 AND fb_app = %s
		";
	
	$users = $wpdb->get_results( $wpdb->prepare( $query, gn_current_app_id() ) );
	
	if( !$users )
		die( gn_wrap_in_heading( __( 'No user available to get notification', 'get-notified' ) ) );
	
		
	$table .= gn_wrap_in_heading( __( 'Notification Preview.', 'get-notified' ) );
	$table .= gn_wrap_in_heading( sprintf( __( 'Notification url: %s', 'get-notified' ), '<strong>' . $notification_url . '</strong>' ), 'p' );
	$table .= gn_wrap_in_heading( sprintf( __( 'Notification Template: %s', 'get-notified' ), '<strong>' . $template . '</strong>' ), 'p' ).'<br />';
	
	if( $type != 'send' ){
		
		foreach ( $users as $user ){
			
			$rows .= "
				<tr>
				 <td>$user->fb_name</td>
				 <td>$user->fb_email</td>
				 <td>$user->fb_id</td>
				 <td>$user->connected</td>
			   </tr>"."\n";
		
		}
		
		$table .= gn_wrap_in_heading( sprintf( __( '%d user(s) will receive notification.', 'get-notified' ), count( $users ) ) );
		$table .= "
		<table class=\"widefat\">
			<thead>
				<tr>   
					<th>".__( 'Name', 'get-notified' )."</th>
					<th>".__( 'Email', 'get-notified' )."</th>
					<th>".__( 'Facebook ID', 'get-notified' )."</th>
					<th>".__( 'Subscribed on', 'get-notified' )."</th>
				</tr>
			</thead>
			<tbody>
			   $rows
			</tbody>
			
		</table>";
		
		echo $table;
		die;
		
	}else{
		
		ini_set('max_execution_time', 0);
		
		if( empty( $template ) ){

			echo gn_wrap_in_heading( __( "Notification template must not be empty.", 'get-notified' ) );
			die;
			
		}		
		
		$GN_FB_Connect = new GN_FB_Connect();
		$GN_FB_Connect->app_level_access();
		
		if( $GN_FB_Connect->validate_session() === false ){

			echo gn_wrap_in_heading( __( "Failed to create session with facebook.", 'get-notified' ) );
			echo gn_wrap_in_heading( sprintf( __( "Please set up your %s facebook application settings correctly. %s", 'get-notified' ), '<a href="'.admin_url('admin.php?page=gn_general_options').'">', '</a>' ), 'p' );
			die;
		}
		
		echo $table;
		
		echo gn_wrap_in_heading( __( 'Results:', 'get-notified' ) );
		
		echo "
		<table class=\"widefat\">
			<thead>
				<tr>
					<th>User ID</th>     
					<th>Name</th>
					<th>Email</th>
					<th>Subscribed on</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>";
		
		
		foreach ( $users as $user ){

			if( ( $response = $GN_FB_Connect->send_notification( $user->fb_id, $href, $template ) ) === false ){
				
				$status = "User unauthorized your app";
				
				$gn_db->update_fb_user( array( 'user_status' => '2' ), array( 'fb_id' => $user->fb_id ) );
				
			}elseif( isset( $response['success'] ) ){
				
				$status = "Sent";
				
				$log = array( 
						'time' => current_time('mysql'), 
						'post_id' => $post_id,
						'user_id' => $user->user_id,
						'fb_id' => $user->fb_id,
						'notification_href' => $href,
						'notification_template' => $template,
						'status' => 'sent',
						'session' => $notification_session
						);
				
				$gn_db->insert_notification_log( $log );
				
			}else{
				
				$status = "User unauthorized your app";
				
				$gn_db->update_fb_user( array( 'user_status' => '3' ), array( 'fb_id' => $user->fb_id ) );
				
			}
			
			echo "
				<tr>
				 <td>$user->user_id</td>
				 <td>$user->fb_name</td>
				 <td>$user->fb_email</td>
				 <td>$user->connected</td>
				 <td>$status</td>
			   </tr>"."\n";
	
		}

		echo "</tbody>
		</table>";
		
		die;
	}

	
}

?>