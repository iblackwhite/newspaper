<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\Entities\AccessToken;

class GN_FB_Connect {
	
	protected $app_id;
	protected $app_secret;
	protected $session;
	
	private function Connection_Required(){
		
		require_once dirname( __FILE__ ) . '/facebook/autoload.php';
		
		$options = get_option('gn_general_options');
		
		$this->app_id = $options['fb_app_id'];
		$this->app_secret = $options['fb_app_secret'];
		
		FacebookSession::setDefaultApplication( $this->app_id, $this->app_secret );
		
	}
	
	public function initiate( $creds = array() ){
		
		$this->Connection_Required();
		
		$session = new FacebookSession( $creds['AccessToken'] );
		
		$this->session = $session;
		
	}	
	
	public function app_level_access(){
		
		$this->Connection_Required();
		
		$session = FacebookSession::newAppSession();
		
		$this->session = $session;
		
	}
	
	public function validate_session(){
		
		try {
		
			$this->session->validate();
			return true;
			
		} 
		catch (FacebookRequestException $ex) { return false; } 
		catch (\Exception $ex) { return false; }
		
	}
	
	public function get_extended_token(){
		
		try {
			
			$accessToken = $this->session->getAccessToken();
			$longLivedAccessToken = $accessToken->extend();
			
			$token_args = array();
			$token_args['token'] = $longLivedAccessToken->__toString();
			$token_args['expire'] = $this->get_token_expiry( $longLivedAccessToken->getExpiresAt() );
			
			return $token_args;
			
		} 
		catch (FacebookRequestException $ex) { return false; } 
		catch (\Exception $ex) { return false; }

	}
	
	public function get_user_data(){

		try {
		
			$request = new FacebookRequest($this->session, 'GET', '/me');
			// this means: retrieve a GraphObject and cast it as a GraphUser (as /me returns a GraphUser object)
			$me = $request->execute()->getGraphObject(GraphUser::className());
			
			return $me;
			
		}
		catch (FacebookRequestException $ex) { return false; } 
		catch (\Exception $ex) { return false; }
		
	}
	
	public function send_notification( $fb_id, $href, $template ){

		try {
		
			$request = new FacebookRequest( $this->session, 'POST', '/'.$fb_id.'/notifications',
			  array (
				'href' => $href,
				'template' => $this->prepare_notification_template( $fb_id, $template ),
			  )
			);
			$response = $request->execute();
			$graphObject = $response->getGraphObject()->asArray();
			
			return $graphObject;
			
		}
		catch (FacebookRequestException $ex) { return false; } 
		catch (\Exception $ex) { return false; }
		
	}
	
	public function prepare_notification_template( $fb_id, $template ){

		$words = array( "@[FB_NAME]");
		$replaces = array( "@[{$fb_id}]" );
		
		$template = str_replace( $words, $replaces, $template );
		
		if( strlen( $template ) > 175 )
			$template = substr( $template, 0, 175 ) . '...';
		
		return $template;
		
	}
		
	protected function get_token_expiry( $ExpiresAt ){
		
		$data = get_object_vars( $ExpiresAt );
		$string_date = $data['date'];
		
		$timestamp = strtotime( $string_date );
		
		return date( 'Y-m-d H:i:s', $timestamp );
		
	}
	
	public function load_sdk_for_token_updation(){
		
		$this->load_script_token_updation();
		
	}
	
	//Display FB Connect button
	public function display_fb_connect( $data = array() ) {
		
		if( !gn_is_compatible_server() )
			return '<div class="gn_error_box"><p>'.__( 'Get Notified requires PHP 5.4 or greater.', 'get-notified' ).'</p></div>';

		
		$options = get_option('gn_general_options');
		
		if( trim( $options['fb_app_id'] ) == '' || trim( $options['fb_app_secret'] ) == '' )
			return '<div class="gn_error_box"><p>'.__( "Please set up your facebook application settings correctly.", 'get-notified' ).'</p></div>';

		extract( $data );
		
		$connect_button = $button ?  pb_get_image( $button ) : pb_get_image( gn_get_button() ) ;
		
		$hide_on_subscription = gn_hide_on_subscription();
		
		$content = '';
		if( $hide_on_subscription == 0 ){
			
			$content .= $this->fb_connect_button( $connect_button, $hide_on_subscription );
			
		}elseif( is_user_logged_in() ){
			
			$user = wp_get_current_user();
			$gn_db = new GN_DB();
			$fb_user = $gn_db->get_fb_user( array( 'user_id' => $user->ID ) );
			
			//Already connected
			if( !$fb_user && !gn_is_authorized_get_notified() ) {

				$content .= $this->fb_connect_button( $connect_button, $hide_on_subscription );
				
			}
			
		}elseif( !gn_is_authorized_get_notified() ) {

			$content .= $this->fb_connect_button( $connect_button, $hide_on_subscription );
			
		}
		
		return $content;
		
	}
	
	private function fb_connect_button( $connect_button, $hide_on_subscription ){

		$this->load_script_fb_connect();
		$this->load_authorization_modal();
		
		$button = '<a class="gn_show_authorization_modal" hide_on_subscription="'.$hide_on_subscription.'" href="javascript:void(0);" modal_id="#gn_authorization_modal">'.$connect_button.'</a>';
		$button .= '<script type="text/javascript"> try{ if(window.jQuery) { jQuery(document).ready(function(){ jQuery(".gn_show_authorization_modal").gnModal(); }); } }catch(ex){}</script>';
		
		return $button;
		
	}
	
	private function load_authorization_modal() {
		
		include_once dirname( __FILE__ ) . '/authorization_modal.php';
		
	}
	
	private function load_script_fb_connect() {
		
		include_once dirname( __FILE__ ) . '/script_fb_connect.php';
		
	}
	
	private function load_script_token_updation() {
		
		include_once dirname( __FILE__ ) . '/script_token_updation.php';
		
	}
	
}

?>