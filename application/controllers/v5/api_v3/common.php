<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
* 
* User related functions
* @author Casperon
*
**/
 
class Common extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','email'));
		$this->load->library(array('encrypt','form_validation'));
		$this->load->model('user_action_model'); 
		$this->load->model('app_model'); 
		$responseArr=array();
		
		/* Authentication Begin */
        $headers = $this->input->request_headers();
		header('Content-type:application/json;charset=utf-8');
		if (array_key_exists("Authkey", $headers)) $auth_key = $headers['Authkey']; else $auth_key = "";
		if(stripos($auth_key,APP_NAME) === false) {
			$cf_fun= $this->router->fetch_method();
			$apply_function = array('update_receive_mode','get_app_info');
			if(!in_array($cf_fun,$apply_function)){
				show_404();
			}
		}
		
		if(array_key_exists("Apptype",$headers)) $this->Apptype =$headers['Apptype'];
		if(array_key_exists("Userid",$headers)) $this->Userid =$headers['Userid'];
		if(array_key_exists("Driverid",$headers)) $this->Driverid =$headers['Driverid'];
		if(array_key_exists("Apptoken",$headers)) $this->Token =$headers['Apptoken'];
		try{
			if(($this->Userid!="" || $this->Driverid!="") && $this->Token!="" && $this->Apptype!=""){
				if($this->Driverid!=''){
					$deadChk = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($this->Driverid)), array('push_notification'));
					if($deadChk->num_rows()>0){
						$storedToken ='';
						if(strtolower($deadChk->row()->push_notification['type']) == "ios"){
							$storedToken = $deadChk->row()->push_notification["key"];
						}
						if(strtolower($deadChk->row()->push_notification['type']) == "android"){
							$storedToken = $deadChk->row()->push_notification["key"];
						}
						$c_fun= $this->router->fetch_method();
						$apply_function = array('update_receive_mode','get_app_info');
						if(!in_array($c_fun,$apply_function)){
							if($storedToken!=''){
								if ($storedToken != $this->Token) {
									echo json_encode(array("is_dead" => "Yes"));
									die;
								}
							}
						}
					}
				}
				if($this->Userid!=''){
					$deadChk = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($this->Userid)), array('push_type', 'push_notification_key'));
					if($deadChk->num_rows()>0){
						$storedToken ='';
						if(strtolower($deadChk->row()->push_type) == "ios"){
							$storedToken = $deadChk->row()->push_notification_key["ios_token"];
						}
						if(strtolower($deadChk->row()->push_type) == "android"){
							$storedToken = $deadChk->row()->push_notification_key["gcm_id"];
						}
						if($storedToken!=''){
							if($storedToken != $this->Token){
								echo json_encode(array("is_dead"=>"Yes")); die;
							}
						}
					}
				}
			 }
		} catch (MongoException $ex) {}
		/*Authentication End*/
    }
	
	/**
	*
	*	This function will update the users/drivers current availablity
	*
	**/
	
	public function update_receive_mode() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$usertype = (string)strtolower($this->input->post('user_type'));	# 	(user/driver)
			$id = (string)$this->input->post('id');
			$mode = (string)$this->input->post('mode'); #	(available/unavailable)
						
			if($usertype != '' && $id != '' && $mode != ''){
				$collection = '';
				if($usertype == "user"){
					$collection = USERS;
				}else if($usertype == "driver"){
					$collection = DRIVERS;
				}
				if($collection!=''){
					$userInfo = $this->app_model->get_selected_fields($collection, array('_id' => new \MongoId($id)), array('chat_status'));					
					if($userInfo->num_rows()==1){
						$dataArr =  array('messaging_status' => strtolower($mode));
						$condition =  array('_id' => new \MongoId($id));
						$this->app_model->update_details($collection, $dataArr, $condition);
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string("Status Updated Successfully",'status_update_success');
					}else{
						$returnArr['response'] = $this->format_string("Cannot find your identity",'cant_find_your_identity');
					}
				}else{
					$returnArr['response'] = $this->format_string("Cannot find your identity",'cant_find_your_identity');
				}
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing",'some_parameters_missing');
            }
		
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will return the information to app during launching
	*
	**/	
	public function get_app_info() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$usertype = (string)strtolower($this->input->post('user_type'));	# 	(user/driver)
			$id = (string)$this->input->post('id');
			
			$server_mode = '0';
			if($_SERVER['HTTP_HOST']=="192.168.1.251:8081"){
				$xmpp_host_url = '192.168.1.150';
				$xmpp_host_name = 'casp83';
			}else{
				$server_mode = '1';
				if (strpos($this->input->server('DOCUMENT_ROOT'), 'dectar/')) {
					$xmpp_host_url = '67.219.149.186';
					$xmpp_host_name = 'messaging.dectar.com';
				}else{
					if (is_file('xmpp-master/config.php')) {
						require_once('./xmpp-master/config.php');
						$xmpp_host_url = vhost_name;
						$xmpp_host_name = vhost_name;
					}else{
						$xmpp_host_url = '67.219.149.186';
						$xmpp_host_name = 'messaging.dectar.com';
					}
				}
			}
			
			$site_mode_string = "currently we are not able to service you, please try again later";
			$site_mode_status = (string)$this->config->item('site_mode');
			if($site_mode_status==""){
				$site_mode_status = "development";	#(development/production)
			}
			
			$lang_code = "en";
			if($this->mailLang!=""){
				$lang_code = $this->mailLang;
			}
			
			$infoArr =  array('site_contact_mail' => (string)$this->config->item('site_contact_mail'),
							'customer_service_number' => (string)$this->config->item('customer_service_number'),
							'server_mode' => $server_mode,
							'site_mode' => $site_mode_status,
							'site_mode_string' => $site_mode_string,
							'site_url' => base_url(),
							'xmpp_host_url' => (string)$xmpp_host_url,
							'xmpp_host_name' => (string)$xmpp_host_name,
							#'facebook_id' => (string)"468945646630814",
							'facebook_id' => (string)$this->config->item('facebook_app_id_android'),
							'google_plus_app_id' => (string)$this->config->item('google_client_id'),
							'driver_google_ios_key' => (string)$this->config->item('google_ios_key'),
							'driver_google_android_key' => (string)$this->config->item('google_android_key'),
							'driver_google_ios_server_key' => (string)$this->config->item('google_server_key'),
							'driver_google_android_server_key' => (string)$this->config->item('google_ios_key'),
							'user_google_ios_key' => (string)$this->config->item('google_ios_key'),
							'user_google_android_key' => (string)$this->config->item('google_android_key'),
							'user_google_ios_server_key' => (string)$this->config->item('google_server_key'),
							'user_google_android_server_key' => (string)$this->config->item('google_ios_key'),
							'app_identity_name' => (string)APP_NAME,
							'about_content' => (string)$this->config->item('about_us'),
							'user_image' => (string)"",
							'lang_code' => (string)$lang_code
							);			
							
			if($usertype != '' && $id != ''){
				$collection = '';
				if($usertype == "user"){
					$collection = USERS;
				}else if($usertype == "driver"){
					$collection = DRIVERS;
				}
				if($collection!=''){
					$userInfo = $this->app_model->get_selected_fields($collection, array('_id' => new \MongoId($id)), array('chat_status','lang_code'));					
					if($userInfo->num_rows()==1){
						if(isset($userInfo->row()->lang_code)){
							$infoArr['lang_code'] = $userInfo->row()->lang_code;
						}else{
							$infoArr['lang_code'] = "en";
						}						
					}
				}
				if($collection == USERS){
					$userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($id)), array( 'image'));
					$user_image = USER_PROFILE_IMAGE_DEFAULT;
					if(isset($userVal->row()->image)){
						if ($userVal->row()->image != '') {
							$user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
						}
					}
					$infoArr['user_image'] = base_url() . $user_image;
				}
						
			}
			$returnArr['status'] = '1';
			$returnArr['response'] = array('info'=>$infoArr);
		
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }	
	
}

/* End of file common.php */
/* Location: ./application/controllers/api_v3/common.php */