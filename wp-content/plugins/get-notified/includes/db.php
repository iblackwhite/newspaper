<?php 

if( !defined('GN_VERSION') )
	die; // don't load this file directly

class GN_DB {
	
	public $total_users;
	
	public function create_tables() {
		
		
		$this->create_fb_userdata_table();
		$this->create_notification_log_table();
		
	}
	
	public function fb_userdata_table() {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_fb_userdata';
		
		return $table_name;
		
	}
	
	public function notification_logs_table() {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_notification_log';
		
		return $table_name;
		
	}
	
	public function change_db_collation() {
		
		global $wpdb;
		
		$fb_userdata_table = $wpdb->prefix . 'gn_fb_userdata';
		$wpdb->query( "ALTER TABLE $fb_userdata_table CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;" );
		
		$notification_logs_table = $wpdb->prefix . 'gn_notification_log';
		$wpdb->query( "ALTER TABLE $notification_logs_table CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;" );
		
	}
	
	private function create_fb_userdata_table() {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_fb_userdata';
		
		$sql = "CREATE TABLE {$table_name} (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  connected datetime NOT NULL,
		  user_id bigint(20) NOT NULL,
		  fb_id bigint NOT NULL,
		  fb_name text DEFAULT NULL,
		  fb_email VARCHAR(255) NOT NULL,
		  fb_token text NOT NULL,
		  fb_token_expires datetime NOT NULL,
		  last_seen datetime NOT NULL,
		  fb_app VARCHAR(255) NOT NULL,
		  user_status tinyint DEFAULT 1 NOT NULL,
		  PRIMARY KEY  (id)
		) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";

	   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	   dbDelta( $sql );
		
	}
	
	private function create_notification_log_table() {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_notification_log';
		
		$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  post_id bigint(20) NOT NULL,
		  user_id bigint(20) NOT NULL,
		  fb_id bigint NOT NULL,
		  notification_href varchar(255) NOT NULL,
		  notification_template longtext NOT NULL,
		  status varchar(20) DEFAULT 'pending' NOT NULL,
		  session varchar(20) NOT NULL,
		  PRIMARY KEY  (id)
		) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";

	   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	   dbDelta( $sql );
		
	}	
	
	function insert_fb_user( $data = array() ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_fb_userdata';
		
		if( !isset( $data['user_id'] ) || !isset( $data['fb_id'] ) || !isset( $data['fb_email'] )  )
			return false;
	
		return $wpdb->insert( $table_name, $data );
		
	}	
	
	function insert_new_fb_user( $user_id, $fb_id, $fb_name, $email, $token, $expiry ) {
		
		$current_time = current_time('mysql');
		
		$data['connected'] = $current_time;
		$data['user_id'] = $user_id;
		$data['fb_id'] = $fb_id;
		$data['fb_name'] = $fb_name;
		$data['fb_email'] = $email;
		$data['fb_token'] = $token;
		$data['fb_token_expires'] = $expiry;
		$data['last_seen'] = $current_time;
		$data['fb_app'] = gn_current_app_id();
		$data['user_status'] = 1;
		
		return $this->insert_fb_user( $data );
		
	}
	
	function update_fb_user( $data = array(), $where = array() ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_fb_userdata';
		
		if( !isset( $where['user_id'] ) && !isset( $where['fb_id'] ) )
			return false;

		return $wpdb->update($table_name, $data, $where);
		
	}
	
	function update_user_token( $data = array(), $where = array() ) {
		
		$data['last_seen'] = current_time( 'mysql' );
		return $this->update_fb_user( $data, $where );
		
	}
	
	function delete_fb_user( $where = array() ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_fb_userdata';
		
		if( !isset( $where['id'] ) )
			return false;
		
		return $wpdb->delete( $table_name, $where );
		
	}
	
	function get_fb_user( $where = array() ) {
		
		if( !isset( $where['user_id'] ) && !isset( $where['fb_id'] ) )
			return false;
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_fb_userdata';
		
		$query = "SELECT * FROM $table_name WHERE 1";
		
		if( isset( $where['user_id'] ) ){
			
			$id = $where['user_id'];
			
			$query .= " AND user_id = %d";
			
		}else{
			
			$id = $where['fb_id'];
			
			$query .= " AND fb_id = %s";
			
		}
		
		return $wpdb->get_row( $wpdb->prepare( $query, $id ) );
		
	}
	
	function get_fb_users( $type = 'users', $page_no = 1, $per_page = 100, $user_status = false ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_fb_userdata';
		
		$page_no = intval( $page_no );
		$per_page = intval( $per_page );
		$start = ($page_no - 1) * $per_page;
	
		$fb_app = gn_current_app_id();
		
		if( $type == 'page_count' ){
			$query = "SELECT Count( fu.id )";
		}else{
			$query = "SELECT fu.*";
		}
		
		$query .= "	
			FROM $table_name fu 
			WHERE fb_app = %s";
		
		if( $user_status !== false )
			$query .= " AND user_status = $user_status";
		
		if( $type == 'page_count' ){
			
			$q = $wpdb->prepare( $query, $fb_app );
			
			$count = $wpdb->get_var( $q );
			
			$this->total_users = $count;
			
			return ceil( $count / $per_page );
			
		}
		
		$query .= " 
			ORDER BY fu.id DESC";
		
		if( $per_page != 4444 )
			$query .= " 
			Limit $start, $per_page";
		
		$q = $wpdb->prepare( $query, $fb_app );
		
		return $wpdb->get_results( $q );
		
	}
	
	public function insert_notification_log( $data ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_notification_log';
		
		$format = array( '%s','%d','%d','%s','%s','%s','%s','%s' );
		
		return $wpdb->insert( $table_name, $data, $format );
		
	}
	
	public function update_notification_log( $data = array(), $where = array(), $format = null, $where_format = null ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_notification_log';
		
		if( !isset( $where['id'] ) && !isset( $where['session'] ) )
			return false;

		return $wpdb->update( $table_name, $data, $where, $format, $where_format );
		
	}
	
	public function check_pending_logs( $criteria = array() ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_notification_log';
		$fb_userdata_table = $this->fb_userdata_table();
		
		if( !isset( $criteria['post_id'] ) && !isset( $criteria['session'] ) )
			return false;

		$limit = ( defined('GN_DEBUG') && GN_DEBUG ) ? 1 : 50;

		$condition = ( isset( $criteria['post_id'] ) ) ? $criteria['post_id'] : $criteria['session'];

		$query = "
			SELECT l.*
			FROM $table_name l
			WHERE l.status = 'pending'";
				
		if( isset( $criteria['post_id'] ) )		
			$query .=	" AND l.post_id = %d";
		else
			$query .=	" AND l.session = %s";
			
		$query .="
			ORDER BY l.id ASC 
			LIMIT 0, %d";

		return $wpdb->get_results( $wpdb->prepare( $query, $condition, $limit ) );
		
	}
	
	public function get_session_logs( $session ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_notification_log';
		$fb_userdata_table = $this->fb_userdata_table();
		
		if( !$session )
			return false;

		$query = "
			SELECT l.*, u.connected, u.fb_name, u.fb_email
			FROM $table_name l
			LEFT JOIN $fb_userdata_table u ON l.fb_id = u.fb_id
			WHERE l.session = %s
			GROUP BY l.id
			ORDER BY l.id ASC";

		return $wpdb->get_results( $wpdb->prepare( $query, $session ) );
		
	}
	
	public function get_notification_session_report( $session = false ) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'gn_notification_log';
		
		if( !$session )
			return false;
		
		$query = "
			SELECT
			count(l.id) as total,
			sum(case when l.status = 'pending' then 1 else 0 end) as pending,
			sum(case when l.status = 'sent' then 1 else 0 end) as sent,
			sum(case when l.status <> 'sent' AND l.status <> 'pending' then 1 else 0 end) as failed
			FROM $table_name l 
			WHERE l.session = %s";

		return $wpdb->get_row( $wpdb->prepare( $query, $session ) );
		
	}
	
	public function cancel_pending_notifications( $session = false ) {

		if( !$session )
			return false;
		
		$data = array( 'status' => 'cancelled' );
		$where = array( 'session' => $session, 'status' => 'pending' );
		
		return $this->update_notification_log( $data, $where, '%s', '%s' );
		
	}
	
}

?>