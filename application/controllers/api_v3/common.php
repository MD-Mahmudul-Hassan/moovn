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
		
		/* $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (stripos($ua, 'cabily2k15android') === false) {
            show_404();
        } */
		header('Content-type:application/json;charset=utf-8');
		/*Authentication Begin*/
		$headers = $this->input->request_headers();
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
						$returnArr['response'] = $this->format_string("Status Updated Successfully");
					}else{
						$returnArr['response'] = $this->format_string("Cannot find your identity");
					}
				}else{
					$returnArr['response'] = $this->format_string("Cannot find your identity");
				}
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing");
            }
		
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will return the information to app during launch app
	*
	**/
		
	public function get_app_info() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$usertype = (string)strtolower($this->input->post('user_type'));	# 	(user/driver)
			$id = (string)$this->input->post('id');
			
			$infoArr =  array('site_contact_mail' => (string)$this->config->item('site_contact_mail'),
							'customer_service_number' => (string)$this->config->item('customer_service_number')
							);			
							
			if($usertype != '' && $id != ''){
				$collection = '';
				if($usertype == "user"){
					$collection = USERS;
				}else if($usertype == "driver"){
					$collection = DRIVERS;
				}
				if($collection!=''){
					$userInfo = $this->app_model->get_selected_fields($collection, array('_id' => new \MongoId($id)), array('chat_status'));					
					if($userInfo->num_rows()==1){
						#$infoArr[] ='';
					}
				}
			}
			$returnArr['status'] = '1';
			$returnArr['response'] = array('info'=>$infoArr);
		
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
}

/* End of file common.php */
/* Location: ./application/controllers/api_v3/common.php */