<?php 

add_action( 'wp_ajax_gn_single_post_fb_notification', 'gn_single_post_fb_notification' );

function gn_single_post_fb_notification(){
	
	check_ajax_referer( 'gn_fb_notification', 'nonce' );
	
	if( !isset( $_POST['post_id'] ) || !isset( $_POST['type'] ) || !isset( $_POST['template'] ) || !isset( $_POST['session'] ) )
		die( gn_wrap_in_heading( __( 'Please refresh the page and try again.', 'get-notified' ) ) );
	
	$post_id = (int)$_POST['post_id'];
	$type = $_POST['type'];
	$template = strip_tags( stripslashes( $_POST['template'] ) );
	
	if( empty( $template ) ){

		echo gn_wrap_in_heading( __( "Notification message must not be empty.", 'get-notified' ) );
		die;
		
	}
	
	$GN_FB_Connect = new GN_FB_Connect();
	$template = $GN_FB_Connect->prepare_notification_template( "FB_NAME", $template );
	
	if( $post_id == 0 ){
		
		if( !isset( $_POST['href'] ) || trim( $_POST['href'] ) == '' )
			die( gn_wrap_in_heading( __( 'HREF Append field must not be empty', 'get-notified' ) ) );
		
		$href = stripslashes( $_POST['href'] );
		$full_url = home_url( $href );
		
	}else{
		
		$full_url = get_permalink( $post_id );
		$href = str_replace( home_url(), "", $full_url );
		
	}

	$gn_db = new GN_DB();
	$users = $gn_db->get_fb_users( 'users', 1, 4444, 1 );//4444 for all users
	
	if( !$users )
		die( gn_wrap_in_heading( __( 'No subscribers available to get notification', 'get-notified' ) ) );
	
	if( $type != 'send' ){
		
		$notification_url = str_replace( 'http://', 'https://', $full_url );
		$notification_url = '<a href="'.$notification_url.'" target="_blank">'.$notification_url.'</a>';
		
		$table = gn_wrap_in_heading( __( 'Notification Preview.', 'get-notified' ) );
		$table .= gn_wrap_in_heading( sprintf( __( 'Notification url: %s', 'get-notified' ), '<strong>' . $notification_url . '</strong>' ), 'p' );
		$table .= gn_wrap_in_heading( sprintf( __( 'Notification Template: %s', 'get-notified' ), '<strong>' . $template . '</strong>' ), 'p' ).'<br />';
		
		$rows = "";
		foreach ( $users as $user ){
			
			$rows .= "
				<tr>
				 <td>$user->fb_name</td>
				 <td>$user->fb_email</td>
				 <td>$user->fb_id</td>
				 <td>$user->connected</td>
			   </tr>"."\n";
		
		}
		
		$table .= gn_wrap_in_heading( sprintf( __( '%d subscriber(s) will receive notification.', 'get-notified' ), count( $users ) ) );
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
		
		if( !defined('GN_DEBUG') || !GN_DEBUG ){
		
			$GN_FB_Connect->app_level_access();
			
			if( $GN_FB_Connect->validate_session() === false ){

				echo gn_wrap_in_heading( __( "Failed to create facebook session.", 'get-notified' ) );
				echo gn_wrap_in_heading( sprintf( __( "Please set up your %s facebook application settings correctly. %s", 'get-notified' ), '<a href="'.admin_url('admin.php?page=gn_general_options').'">', '</a>' ), 'p' );
				die;
			}	
		}
		
		$notification_session = $_POST['session'];
		foreach ( $users as $user ){
				
			$log = array( 
					'time' => current_time('mysql'), 
					'post_id' => $post_id,
					'user_id' => $user->user_id,
					'fb_id' => $user->fb_id,
					'notification_href' => $href,
					'notification_template' => $template,
					'status' => 'pending',
					'session' => $notification_session
					);
			
			$gn_db->insert_notification_log( $log );
	
		}

		die( "Processing..." );
		
	}

}

add_action( 'wp_ajax_gn_ajax_send_notifications', 'gn_ajax_send_notifications' );

function gn_ajax_send_notifications(){
	
	check_ajax_referer( 'gn_fb_notification', 'nonce' );
	
	if( !isset( $_POST['session'] ) )
		die( "Error!" );
	
	$session = $_POST['session'];

	$gn_db = new GN_DB();
	$logs = $gn_db->check_pending_logs( array( 'session' => $session ) );
	
	if( !$logs ){
		
		$session_report = $gn_db->get_notification_session_report( $session );
		echo '<div style="margin: 20px 0;" class="updated sending_processing"> <p>'. sprintf( __( '%s of %s notification(s) sent. %s failed.', 'get-notified' ), '<b>' . $session_report->sent . '</b>', '<b>' . $session_report->total . '</b>', '<b>' . $session_report->failed . '</b>').' </p> </div>';
		
		$session_logs = $gn_db->get_session_logs( $session );
		$rows = gn_notification_prepare_logs_rows( $session_logs );
		$table = gn_notification_logs_table( $rows );
		echo $table;
		die();
		
	}
	
	
	ini_set('max_execution_time', 0);	
	
	if( !defined('GN_DEBUG') || !GN_DEBUG ){
		
		$GN_FB_Connect = new GN_FB_Connect();
		$GN_FB_Connect->app_level_access();
		
		if( $GN_FB_Connect->validate_session() === false )
			die( "SessionProblem" );

	}
	
	foreach ( $logs as $log ){
		
		if( defined('GN_DEBUG') && GN_DEBUG ){
			
			sleep(2);

			$log_status = 'debug';
			
		}else{
			
			if( ( $response = $GN_FB_Connect->send_notification( $log->fb_id, $log->notification_href, $log->notification_template ) ) === false ){
					
				$gn_db->update_fb_user( array( 'user_status' => '2' ), array( 'fb_id' => $log->fb_id ) );
				$log_status = 'un_auth_2';
				
			}elseif( isset( $response['success'] ) ){

				$log_status = 'sent';
				
			}else{
				
				$gn_db->update_fb_user( array( 'user_status' => '3' ), array( 'fb_id' => $log->fb_id ) );
				$log_status = 'un_auth_3';
			}
			
		}
		
		$gn_db->update_notification_log( array( 'status' => $log_status ), array( 'id' => $log->id ) );
		
	}
	
	$session_report = $gn_db->get_notification_session_report( $session );
	printf( __( '%s of %s notification(s) Sent, %s Failed, %s Completed.', 'get-notified' ), '<b>' . $session_report->sent . '</b>', '<b>' . $session_report->total . '</b>', '<b>' . $session_report->failed . '</b>', '<b>' . ( $session_report->total - $session_report->pending ) . '</b>');
	die( '<div style="display: none;">Processing...</div>' );

	
}
add_action( 'wp_ajax_gn_ajax_cancel_notifications', 'gn_ajax_cancel_notifications' );

function gn_ajax_cancel_notifications(){
	
	check_ajax_referer( 'gn_fb_notification', 'nonce' );
	
	if( !isset( $_POST['session'] ) )
		die( "Error!" );
	
	$session = $_POST['session'];

	$gn_db = new GN_DB();
	$cancel = $gn_db->cancel_pending_notifications( $session );
	
	if( $cancel === false )
		die( "Error!" );
	
	$session_report = $gn_db->get_notification_session_report( $session );
	echo '<div style="margin: 20px 0;" class="updated sending_processing"> <p>'. sprintf( __( '%s of %s notification(s) sent. %s failed.', 'get-notified' ), '<b>' . $session_report->sent . '</b>', '<b>' . $session_report->total . '</b>', '<b>' . $session_report->failed . '</b>').' </p> </div>';
	
	$session_logs = $gn_db->get_session_logs( $session );
	$rows = gn_notification_prepare_logs_rows( $session_logs );
	$table = gn_notification_logs_table( $rows );
	echo $table;
	die();
	
}

?>