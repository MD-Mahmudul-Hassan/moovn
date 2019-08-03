<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * Sms related functions
 * @author Casperon
 *
 */

class Sms_twilio extends MY_Controller { 
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','email'));
		$this->load->library(array('encrypt','form_validation','twilio'));		
		$this->data['loginCheck'] = $this->checkLogin('U');  
		$this->load->model(array('user_model'));
    }
    
  
	/** 
	 * 
	 * Send Otp Ajax function 
	 */
	function send_otp(){ 
		
		$otp_phone = $this->remove_leading_zeros($this->input->post('otp_phone'));
		$phone_code = $this->input->post('phone_code');

		$checkMob = $this->user_model->get_selected_fields(DRIVERS,array('mobile_number' => $otp_phone),array(),array('_id'));
		if($checkMob->num_rows() > 0){
			echo json_encode('exist');
		} else {
			if(substr($phone_code,0,1) == '+'){
				$phone_code = $phone_code;
			} else {
				$phone_code = '+'.$phone_code;
			}
			
			if($otp_phone != ''){
				$otp_number=rand(10000,99999); 
					
			    if(isset($otp_phone)&&isset($phone_code)){
				$session_data=array(APP_NAME.'otp_phone_number'=>$otp_phone,APP_NAME.'otp_country_code'=>$phone_code,'isVerified'=>'false');
				$this->session->set_userdata($session_data);
				}
				$this->session->set_userdata(APP_NAME.'sms_otp',$otp_number);
				$from = $this->config->item('twilio_number');
				$to = $phone_code.$otp_phone;
				$message = 'Dear driver! your '.$this->config->item('email_title').' one time password is '.$otp_number;
				$response = $this->twilio->sms($from, $to, $message); 
				
				if($this->checkLogin('D') != ''){
					try{
						$condition = array('_id' => new \MongoId($this->checkLogin('D')));
						$this->user_model->update_details(DRIVERS,array('mobile_otp' => $otp_number),$condition); 
					}catch (MongoException $me){
						echo json_encode('Internal Error');
					}
					 
				}
				$mode = $this->config->item('twilio_account_type');
				if($mode == 'sandbox'){
					echo json_encode($otp_number);
				}else{
					echo json_encode('success');
				}
			} else {
				echo json_encode('error');
			}
		}
	}
	
	/**
	* verify the otp for mobile sms verification
	*
	**/
	function confirm_mobile_verification(){ 
		if ($this->checkLogin('U')==''){
			$this->setErrorMessage('success','Login Required');
			redirect('login');
		}
		$otp=$this->input->post('otp_code');
		$phone_number = $this->remove_leading_zeros($this->input->post('phone_number'));
		$phone_code=$this->input->post('phone_code'); 
		if($otp == $this->session->userdata(APP_NAME.'sms_otp')){ 
			$phone_no = $phone_code.$phone_number; 
			$this->user_model->update_details(USERS,array('phone_no' => $phone_no,'mobile_verification' => 'Yes'),array('id' => $this->checkLogin('U')));
			$this->setErrorMessage('success','Your mobile number verification success.');
		} else { 
			$this->setErrorMessage('error','Code number can not be match.');
		}
		redirect('shop/sell');
	}
	
	function otp_verification(){
		
		$otp=$this->input->post('otp');
		if($otp == $this->session->userdata(APP_NAME.'sms_otp')){
			$this->session->set_userdata('isVerified','true');
			echo 'success';
		} else {
			echo 'error';
		}
	}
	
	function check_is_valid_otp_fields(){
       	   
			$otp_phone = $this->input->post('otp_phone');
			$phone_code = $this->input->post('phone_code');
			$stored_mobile_number=$this->session->userdata(APP_NAME.'otp_phone_number');
			$stored_country_code=$this->session->userdata(APP_NAME.'otp_country_code');
			$isVerified=$this->session->userdata('isVerified');
			
			if($stored_mobile_number==$otp_phone && $stored_country_code==$phone_code && $isVerified=='true'){
			echo json_encode('success');
			}else{
			echo json_encode('error');
			}
	}
	 
}

?>