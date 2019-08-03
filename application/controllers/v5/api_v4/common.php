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
			$apply_function = array();
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
			
    }
		/*Authentication End*/
	
	/**
	*
	*	This function will update the driver location by every 100 meters
	*
	**/	
	public function driver_update_location_hun() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$driver_id = (string)$this->input->post('driver_id');
			$ride_id = (string)$this->input->post('ride_id');
			if($driver_id!='' && $ride_id!=''){
				$checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'no_of_step'));
                    if ($checkRide->num_rows() == 1) {
						if(isset($checkRide->row()->no_of_step)){
							$no_of_step = $checkRide->row()->no_of_step;
							$no_of_step++;
							$this->app_model->hun_lat($ride_id,$no_of_step);
						}else{
							$this->app_model->hun_ini($ride_id,1);
						}
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string("Updated Successfully", "updated_successfully");
					}else{
						$returnArr['response'] = $this->format_string("Invalid Ride", "invalid_ride");
					}
				}else{
					$returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
				}
			}else{
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will update the driver location by every 10 min
	*
	**/	
	public function driver_update_bulk_location() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		
		try {
			$driver_id = (string)$this->input->post('driver_id');
			$ride_id = (string)$this->input->post('ride_id');
			$travel_history = $this->input->post('travel_history'); // string lat;log;time,lat;lon;time,.etc
			
			$travel_history = rtrim($travel_history,',');
			
			if($driver_id!='' && $ride_id!='' && $travel_history!=''){
				$checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status'));
                    if ($checkRide->num_rows() == 1) {
						/** update travel history **/
						$travel_historyArr = array();
						$travelRecords = @explode(',',$travel_history);
						if(count($travelRecords)>1){
							for( $i = 0; $i < count($travelRecords); $i++){
								if($travelRecords[$i]!=''){
									$splitedHis = @explode(';',$travelRecords[$i]);
									$travel_historyArr[] = array('lat' => $splitedHis[0],
																							 'lon' => $splitedHis[1],
																							 'update_time' => new mongoDate(strtotime($splitedHis[2]))
																							);
								}
							}
						}
						$checkHistory = $this->app_model->get_selected_fields(TRAVEL_HISTORY, array('ride_id' => $ride_id), array('history'));
						if($checkHistory->num_rows()>0){
							if(!empty($travel_historyArr)){
								/* foreach($travel_historyArr as $rowVal){
									$this->app_model->simple_push(TRAVEL_HISTORY,array('ride_id' => $ride_id),array('history' => $rowVal));
								} */
								$oldHistory = $checkHistory->row()->history;
								if(count($travel_historyArr) > count($oldHistory)){
									$this->app_model->update_details(TRAVEL_HISTORY,array('history' => $travel_historyArr),array('ride_id' => $ride_id));
								}
							}							
						}else{
							if(!empty($travel_historyArr)){
								$this->app_model->simple_insert(TRAVEL_HISTORY,array('ride_id' => $ride_id,'history' => $travel_historyArr));
							}
						}
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string("Updated Successfully", "updated_successfully");
					}else{
						$returnArr['response'] = $this->format_string("Invalid Ride", "invalid_ride");
					}
				}else{
					$returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
				}
			}else{
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }	
	
	/**
	*
	*	This function will update the driver location and send the location to user by notifications
	*
	**/	
	public function driver_update_ride_location() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		
		try {
			$driver_id = (string)$this->input->post('driver_id');
			$ride_id = (string)$this->input->post('ride_id');
			$lat = (string)$this->input->post('lat');
			$lon = (string)$this->input->post('lon');
			$bearing = (string)$this->input->post('bearing');
			
			if($driver_id!='' && $ride_id!='' && $lat!='' && $lon!=''){
				$checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status','user'));
                    if ($checkRide->num_rows() == 1) {
						$ride_location[] = array('lat' => $lat,
																		'lon' => $lon,
																		'update_time' => new MongoDate(time())
																		);
						$checkRideHistory = $this->app_model->get_selected_fields(RIDE_HISTORY, array('ride_id' => $ride_id), array('values'));
						if($checkRideHistory->num_rows()>0){
							if(!empty($travel_historyArr)){
								$this->app_model->simple_push(RIDE_HISTORY,array('ride_id' => $ride_id),array('values' => $rowVal));
							}
						}else{
							if(!empty($travel_historyArr)){
								$this->app_model->simple_insert(RIDE_HISTORY,array('ride_id' => $ride_id,'values' => $travel_historyArr));
							}
						}
						/* Notification to user about driver current ride location */
						$user_id = $checkRide->row()->user['id'];
						$userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
						if (isset($userVal->row()->push_type)) {
							if ($userVal->row()->push_type != '') {
								$message = 'Driver current ride location';
								$options = array("action"=>"driver_loc",'ride_id' => (string) $ride_id, 'latitude' => (string) $lat, 'longitude' => (string) $lon,'bearing' => (string) $bearing);
								if ($userVal->row()->push_type == 'ANDROID') {
									if (isset($userVal->row()->push_notification_key['gcm_id'])) {
										if ($userVal->row()->push_notification_key['gcm_id'] != '') {
											$this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'driver_loc', 'ANDROID', $options, 'USER');
										}
									}
								}
								if ($userVal->row()->push_type == 'IOS') {
									if (isset($userVal->row()->push_notification_key['ios_token'])) {
										if ($userVal->row()->push_notification_key['ios_token'] != '') {
											$this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'driver_loc', 'IOS', $options, 'USER');
										}
									}
								}
							}
						}
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string("Updated Successfully", "updated_successfully");
					}else{
						$returnArr['response'] = $this->format_string("Invalid Ride", "invalid_ride");
					}
				}else{
					$returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
				}
			}else{
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection', 'error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }


	/**
	*
	*	This function will update the driver location and send the location to user by notifications
	*
	**/	
	public function update_primary_language() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		
		try {
			$id = (string)$this->input->post('id');
			$lang_code = (string)$this->input->post('lang_code');
			$user_type = (string)$this->input->post('user_type');  // Options : user/driver
			
			if($id !='' && $user_type != '' && $lang_code != ''){
				$chekLang = $this->app_model->get_selected_fields(LANGUAGES, array('lang_code' => (string)$lang_code), array('name'));
				if($chekLang->num_rows() == 1){
					$action = FALSE;
					if($user_type == 'user'){
						$chekUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($id)), array('_id'));
						if($chekUser ->num_rows() == 1){
							$this->app_model->update_details(USERS, array('lang_code' => $lang_code),array('_id' => new \MongoId($id)));
							$action = TRUE;
						}
					} else if($user_type == 'driver'){
						$chekDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($id)), array('_id'));
						if($chekDriver ->num_rows() == 1){
							$this->app_model->update_details(DRIVERS, array('lang_code' => $lang_code),array('_id' => new \MongoId($id)));
							$action = TRUE;
						}
					}
					if($action){
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string("Updated Successfully", "updated_successfully");
					}else{
						$returnArr['response'] = $this->format_string("Failed to update", "failed_to_update");
					}
				} else {
					$returnArr['response'] = $this->format_string("Invalid language code", "invalid_language_code");
				}
			}else{
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection', 'error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	*	This function will upload the user profile picture
	*
	**/	
	public function upload_user_profile_image() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		$returnArr['image_url'] = '';
		try{
			$user_id = $this->input->post('user_id');
			if($user_id!=""){
				$userInfo = $this->app_model->get_selected_fields(USERS, array('_id' => new MongoId($user_id)),array('image'));
				if($userInfo->num_rows() > 0 ){
					$config['overwrite'] = FALSE;
					$config['encrypt_name'] = TRUE;
					$config['allowed_types'] = 'jpg|jpeg|gif|png';
					$config['max_size'] = 2000;
					$config['upload_path'] = './images/users';
					$this->load->library('upload', $config);
					
					if (!$this->upload->do_upload('user_image')){
						$returnArr['response'] = $this->format_string("Error in updating profile picture", "profile_picture_updated_error");
						#$returnArr['response'] = (string)$this->upload->display_errors();
					}else{
						$imgDetails = $this->upload->data();
						$ImageName = $imgDetails['file_name'];
						
						$this->ImageResizeWithCrop(600, 600, $ImageName, './images/users/');
						@copy('./images/users/' . $ImageName, './images/users/thumb/' . $ImageName);
						$this->ImageResizeWithCrop(210, 210, $ImageName, './images/users/thumb/');
					
						$returnArr['image_url'] = base_url().USER_PROFILE_THUMB.$ImageName;	
						
						$condition =  array('_id' => new \MongoId($user_id));
						$this->app_model->update_details(USERS, array('image' => $ImageName), $condition);
						
						$returnArr['response'] = $this->format_string("Profile picture updated successfully", "profile_picture_updated_success");
						$returnArr['status'] = '1';
					}
				}else{
					$returnArr['response'] = $invalid_user;
				}
			} else {
				$returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
			}
		}catch(Exception $e){
			$returnArr['response'] = $this->format_string("Error in Connection", "error_in_connection");
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
	
	
}

/* End of file common.php */
/* Location: ./application/controllers/api_v4/common.php */