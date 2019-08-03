<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
 * 
 * User related functions
 * @author Casperon
 *
 **/
 
class User extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','email'));
		$this->load->library(array('encrypt','form_validation'));
		$this->load->model('user_action_model'); 
		$this->load->model('app_model'); 
		$responseArr=array();
		
		//* $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        /*if (stripos($ua, 'cabily2k15android') === false) {
            show_404();
        } */
        header('Content-type:application/json;charset=utf-8');
		/* Authentication Begin */
        $headers = $this->input->request_headers();
        if (array_key_exists("Apptype", $headers)) $this->Apptype = $headers['Apptype'];
        if (array_key_exists("Userid", $headers)) $this->Userid = $headers['Userid'];
        if (array_key_exists("Apptoken", $headers)) $this->Token = $headers['Apptoken'];
        try {
            if ($this->Userid != "" && $this->Token != "" && $this->Apptype != "") {
                $deadChk = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($this->Userid)), array('push_type', 'push_notification_key'));
                if ($deadChk->num_rows() > 0) {
					$storedToken = '';
                    if (strtolower($deadChk->row()->push_type) == "ios") {
                        $storedToken = $deadChk->row()->push_notification_key["ios_token"];
                    }
                    if (strtolower($deadChk->row()->push_type) == "android") {
                        $storedToken = $deadChk->row()->push_notification_key["gcm_id"];
                    }
					$c_fun= $this->router->fetch_method();
					$apply_function = array('login_user','social_Login');
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
        } catch (MongoException $ex) {
            
        }
		/* Authentication End */
		
    }
	
	
	
	/**
     * 
     * Add Favourite Driver
     */
	 
	  public function add_favourite_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $title = trim($this->input->post('title'));
            $desc = trim($this->input->post('description'));
            $user_id = $this->input->post('user_id');
			$driver_id = $this->input->post('driver_id');
			if(isset($title)&&isset($user_id)&&isset($driver_id)){
				if(!empty($title)&&!empty($user_id)&&!empty($driver_id)){
					$user_details=$this->user_action_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id'));
					if($user_details->num_rows()>0){
						$driver_details=$this->user_action_model->get_selected_fields(DRIVERS,array('_id'=>new MongoId($driver_id)),array('_id'));	
						if($driver_details->num_rows()>0){
							$fav_condition = array('user_id' => new \MongoId($user_id));
							$checkDriverFav = $this->user_action_model->get_all_details(FAVOURITE, $fav_condition);
						if (isset($checkDriverFav->row()->fav_driver[$driver_id])) {
							 $returnArr['response'] = $this->format_string('Driver already exist in your favourite list', 'driver_already_exist_in_favourite');
						}else{
							 if ($checkDriverFav->num_rows() == 0) {
								$dataArr = array('user_id' => new \MongoId($user_id),
								'fav_driver' => array($driver_id => array('title' => $title,
										'description' => $desc
								 )));
								$this->user_action_model->simple_insert(FAVOURITE, $dataArr);
								$returnArr['status'] = '1';
								$returnArr['response'] = $this->format_string('Driver added to favourite successfully!', 'driver_added_to_favourite');
							}else {
								$dataArr = array('fav_driver.' . $driver_id => array('title' => $title,
                                'description' => $desc));
								$this->user_action_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);
								$returnArr['status'] = '1';
								$returnArr['response'] = $this->format_string('Driver added to favourite successfully!', 'driver_added_to_favourite');
						   }
						}
						}else{
							 $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
						}
					}else{
						 $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
						}
				}  else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }	
			 } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	 /**
     *
     * This function edit favourite driver added by user
     *
     * */
    public function edit_favourite_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $title = trim($this->input->post('title'));
            $desc = trim($this->input->post('description'));
            $user_id = $this->input->post('user_id');
			$driver_id = $this->input->post('driver_id');

           	if(isset($title)&&isset($user_id)&&isset($driver_id)){
				if(!empty($title)&&!empty($user_id)&&!empty($driver_id)){
					$user_details=$this->user_action_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id'));
					if($user_details->num_rows()>0){
						$driver_details=$this->user_action_model->get_selected_fields(DRIVERS,array('_id'=>new MongoId($driver_id)),array('_id'));	
						if($driver_details->num_rows()>0){
							$fav_condition = array('user_id' => new \MongoId($user_id));
							$checkDriverFav = $this->user_action_model->get_all_details(FAVOURITE, $fav_condition);
						if (isset($checkDriverFav->row()->fav_driver[$driver_id])) {
							$dataArr = array('fav_driver.' . $driver_id => array('title' => $title,
                                'description' => $desc));
								$this->user_action_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);
								$returnArr['status'] = '1';
								$returnArr['response'] = $this->format_string('Favorite driver edited successfully!', 'favorite_driver_updated');
						}else{
							
							$returnArr['response'] = $this->format_string('Driver not found in your favorite drivers list', 'driver_not_found_in_favorite');
						}
						}else{
							 $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
						}
					}else{
						 $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
						}
				}  else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }	
			 } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	 /**
     *
     * This function remove the favorite driver from user's favorite driver list
     *
     * */
    public function remove_favourite_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $user_id = $this->input->post('user_id');
			if(isset($user_id)&&isset($driver_id)){
				if(!empty($user_id)&&!empty($driver_id)){
					$user_details=$this->user_action_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id'));
					if($user_details->num_rows()>0){
						$driver_details=$this->user_action_model->get_selected_fields(DRIVERS,array('_id'=>new MongoId($driver_id)),array('_id'));	
						if($driver_details->num_rows()>0){
						$fav_condition = array('user_id' => new \MongoId($user_id));
						$checkDriverFav = $this->user_action_model->get_all_details(FAVOURITE, $fav_condition);
						if (isset($checkDriverFav->row()->fav_driver[$driver_id])) {
						$this->user_action_model->remove_favorite_driver($fav_condition, 'fav_driver.' . $driver_id);
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string('Driver unfavored successfully!', 'driver_unfavored_successfully');
							
						}else{
							
							$returnArr['response'] = $this->format_string('Driver not found in your favorite drivers list', 'driver_not_found_in_favorite');
						}
						}else{
							 $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
						}
					}else{
						 $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
						}
				}  else {
				$returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
			}	
					 } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	/**
     *
     * This function displays the all favourite drivers added by user
     *
     * */
    public function display_favourite_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
			$user_id = $this->input->post('user_id');
			$page = $this->input->post('page');
			$perPage = $this->input->post('perPage');
			if ($perPage <= 0) {
                $perPage = 20;
            }

			if(isset($user_id)&&!empty($user_id)){
					$user_details=$this->user_action_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id'));
					if($user_details->num_rows()>0){
						$fav_condition = array('user_id' => new \MongoId($user_id));
						$checkDriverFav = $this->user_action_model->get_all_details(FAVOURITE, $fav_condition);
						if ($checkDriverFav->num_rows() == 0) {
						$returnArr['response'] = $this->format_string('No records found for in your favourite list', 'no_records_found_in_your_favourite_list');
						} else {
							
							if ($page <= 0) {
								$offset = 0;
								$current_page = 1;
							} else {
								$current_page = $page;
								$offset = ($page * $perPage) - $perPage;
							}
							
							if (isset($checkDriverFav->row()->fav_driver)) {
								$favDrivers = $checkDriverFav->row()->fav_driver;
							} else {
								$favDrivers = array();
							}
							$favDriverArr =$favDriverArray= array();
							foreach ($favDrivers as $key=>$val) {
								$favDriverArr[] = array('driver_id' => $key,
									'title' => $val['title'],
									'description' => $val['description']
								);
							}
							$favDriverArr=array_slice($favDriverArr,$offset,$perPage);
							if (empty($favDriverArr)) {
								$favDriverArr = json_decode("{}");
							}
							$totalFavDriver = count($favDrivers);
							if ($totalFavDriver > 0) {
								$returnArr['status'] = '1';
								$returnArr['response'] = array('drivers' => $favDriverArr,'current_page' => (string) $current_page, 'perPage' => (string) $perPage, 'total_count' => $totalFavDriver);
							} else {
								$returnArr['response'] = $this->format_string('No records found for in your favourite list', 'no_records_found_in_your_favourite_list');
							}
						}
						
					}else{
						 $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
						}
		
					 } else {
						$returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	/**
     *
     * Social Media Login and Register
     *
     * */
    public function social_login() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';

        $email = strtolower($this->input->post('email'));
        $user_name = $this->input->post('user_name');
        $country_code = $this->input->post('country_code');
        $phone_number = $this->input->post('phone_number');
        $referal_code = $this->input->post('referal_code');
        $gcm_id = $this->input->post('gcm_id');
        $deviceToken = $this->input->post('deviceToken');
        $media = $this->input->post('media');
        $media_id = $this->input->post('media_id');
        $password = $this->input->post('password');

        #$password = $this->user_model->get_random_string(6);

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 6) {
            if (valid_email($email)) {
                $checkEmail = $this->user_model->check_user_exist(array('email' => $email));
                if ($checkEmail->num_rows() >= 1) {
                    $push_data = array();
                    $key = '';
                    if ($gcm_id != "") {
                        $key = $gcm_id;
                        $push_data = array('push_notification_key.gcm_id' => $gcm_id, 'push_type' => 'ANDROID');
                        $push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
                    } else if ($deviceToken != "") {
                        $key = $deviceToken;
                        $push_data = array('push_notification_key.ios_token' => $deviceToken, 'push_type' => 'IOS');
                        $push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
                    }
                    $is_alive_other = "No";
                    if (isset($checkUser->row()->push_type)) {
                        if ($checkUser->row()->push_type != '') {
                            if ($checkUser->row()->push_type == "ANDROID") {
                                $existingKey = $checkUser->row()->push_notification_key["gcm_id"];
                            }
                            if ($checkUser->row()->push_type == "IOS") {
                                $existingKey = $checkUser->row()->push_notification_key["ios_token"];
                            }
                            if ($existingKey != $key) {
                                $is_alive_other = "Yes";
                            }
                        }
                    }
                    $returnArr['is_alive_other'] = (string) $is_alive_other;

                    if (!empty($push_data)) {
                        $this->user_model->update_details(USERS, $push_update_data, $push_data);
                        $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($checkEmail->row()->_id)));
                    }

                    $returnArr['status'] = '1';
                    $returnArr['message'] = $this->format_string('You are Logged In successfully','you_logged_successfully');
                    $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkEmail->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'referral_code', 'push_notification_key.gcm_id', 'user_group', 'location_id'));
                    if ($userVal->row()->image == '') {
                        $user_image = USER_PROFILE_IMAGE_DEFAULT;
                    } else {
                        $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                    }
                    $unique_code = '';
                    $location_id = '';
                    if (isset($userVal->row()->location_id)) {
                        if ($userVal->row()->location_id != '') {
                            $location_id = $userVal->row()->location_id;
                        }
                    }
                    $returnArr['user_image'] = base_url() . $user_image;
                    $returnArr['user_id'] = (string) $checkEmail->row()->_id;
                    $returnArr['soc_key'] = md5((string) $checkEmail->row()->_id);
                    $returnArr['user_name'] = $userVal->row()->user_name;
                    $returnArr['user_group'] = (string) $userVal->row()->user_group;
                    $returnArr['email'] = $userVal->row()->email;
                    $returnArr['country_code'] = $userVal->row()->country_code;
                    $returnArr['phone_number'] = $userVal->row()->phone_number;
                    $returnArr['referal_code'] = $unique_code;
                    $returnArr['key'] = $key;
                    $returnArr['location_id'] = (string) $location_id;
                    $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('name'));
                    $category = '';
                    if ($categoryResult->num_rows() > 0) {
                        $category = $categoryResult->row()->_id;
                    }
                    $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                    $returnArr['category'] = (string) $category;

                    $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($checkEmail->row()->_id)), array('total'));
                    $avail_amount = 0;
                    if (isset($walletDetail->row()->total)) {
                        $avail_amount = $walletDetail->row()->total;
                    }
                    $returnArr['wallet_amount'] = (string) $avail_amount;
                } else {
                    $cStatus = FALSE;
                    if ($referal_code != '') {
                        $chekCode = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('_id'));
                        if ($chekCode->num_rows() > 0) {
                            $cStatus = TRUE;
                        }
                    } else {
                        $cStatus = TRUE;
                    }
                    if ($cStatus) {
                        $user_image = '';

                        if (isset($_FILES['photo'])) {
                            if ($_FILES['photo']['size'] > 0) {
                                $data = file_get_contents($_FILES['photo']['tmp_name']);
                                $image = imagecreatefromstring($data);
                                $imgname = md5(time() . rand(10, 99999999) . time()) . ".jpg";
                                $savePath = USER_PROFILE_IMAGE . $imgname;
                                imagejpeg($image, $savePath, 99);

                                $option = $this->getImageShape(250, 250, $savePath);
                                $resizeObj = new Resizeimage($savePath);
                                $resizeObj->resizeImage(75, 75, $option);
                                $resizeObj->saveImage(USER_PROFILE_THUMB . $imgname, 100);

                                $this->ImageCompress(USER_PROFILE_IMAGE . $imgname);
                                $this->ImageCompress(USER_PROFILE_THUMB . $imgname);
                                $user_image = $imgname;
                            }
                        }

                        $verification_code = $this->get_rand_str('10');
                        $unique_code = $this->app_model->get_unique_id($user_name);
                        $user_data = array('user_name' => $user_name,
                            'user_type' => $media,
                            'media_id' => (string) $media_id,
                            'unique_code' => $unique_code,
                            'user_group' => 'User',
                            'email' => $email,
                            'password' => md5($password),
                            'image' => $user_image,
                            'status' => 'Active',
                            'country_code' => $country_code,
                            'phone_number' => $phone_number,
                            'referral_code' => $unique_code,
                            'verification_code' => array("email" => $verification_code),
                            'created' => date("Y-m-d H:i:s")
                        );
                        $this->user_model->insert_user($user_data);
                        $last_insert_id = $this->cimongo->insert_id();
                        if ($last_insert_id != '') {
                            $push_data = array();
                            $key = '';
                            if ($gcm_id != "") {
                                $key = $gcm_id;
                                $push_data = array('push_notification_key.gcm_id' => $gcm_id, 'push_type' => 'ANDROID');
                                $push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
                            } else if ($deviceToken != "") {
                                $key = $deviceToken;
                                $push_data = array('push_notification_key.ios_token' => $deviceToken, 'push_type' => 'IOS');
                                $push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
                            }
                            if (!empty($push_data)) {
                                $this->user_model->update_details(USERS, $push_update_data, $push_data);
                                $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($last_insert_id)));
                            }

                            /* Insert Referal and wallet collection */
                            $this->user_model->simple_insert(REFER_HISTORY, array('user_id' => new \MongoId($last_insert_id)));
                            $this->user_model->simple_insert(WALLET, array('user_id' => new \MongoId($last_insert_id), 'total' => floatval(0)));
                            /* Update the welcome amount to the registered user wallet */
                            $trans_id = time() . rand(0, 2578);
                            $initialAmt = array('type' => 'CREDIT',
                                'credit_type' => 'welcome',
                                'ref_id' => '',
                                'trans_amount' => floatval($this->config->item('welcome_amount')),
                                'avail_amount' => floatval($this->config->item('welcome_amount')),
                                'trans_date' => new \MongoDate(time()),
                                'trans_id' => $trans_id
                            );
                            $this->user_model->simple_push(WALLET, array('user_id' => new \MongoId($last_insert_id)), array('transactions' => $initialAmt));
                            $this->user_model->update_wallet((string) $last_insert_id, 'CREDIT', floatval($this->config->item('welcome_amount')));
                            /* Update the referer history */
                            if ($referal_code != '') {
                                $refererVal = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('email'));
                                if ($refererVal->num_rows() > 0) {
                                    $ref_status = 'true';
                                    $amount_earns = floatval($this->config->item('referal_amount'));
                                    if ($this->config->item('referal_credit') == 'on_first_job') {
                                        $ref_status = 'false';
                                        $amount_earns = floatval(0);
                                    }
                                    $refArr = array('reference_id' => (string) $last_insert_id,
                                        'reference_mail' => (string) $email,
                                        'amount_earns' => $amount_earns,
                                        'reference_date' => new \MongoDate(time()),
                                        'used' => $ref_status
                                    );
                                    $this->user_model->simple_push(REFER_HISTORY, array('user_id' => new \MongoId($refererVal->row()->_id)), array('history' => $refArr));
                                    if ($this->config->item('referal_credit') == 'instant') {
                                        $this->user_model->update_wallet((string) $refererVal->row()->_id, 'CREDIT', floatval($this->config->item('referal_amount')));
                                        $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($refererVal->row()->_id)), array('total'));
                                        $avail_amount = 0;
                                        if (isset($walletDetail->row()->total)) {
                                            $avail_amount = $walletDetail->row()->total;
                                        }
                                        $walletArr = array('type' => 'CREDIT',
                                            'credit_type' => 'referral',
                                            'ref_id' => (string) $last_insert_id,
                                            'trans_amount' => floatval($this->config->item('referal_amount')),
                                            'avail_amount' => floatval($avail_amount),
                                            'trans_date' => new \MongoDate(time())
                                        );
                                        $this->user_model->simple_push(WALLET, array('user_id' => new \MongoId($refererVal->row()->_id)), array('transactions' => $walletArr));
                                    }
                                }
                            }

                            /* Update Stats Starts */
                            $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                            $field = array('user.hour_' . date('H') => 1, 'user.count' => 1);
                            $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                            /* Update Stats End */


                            $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($last_insert_id)), array('total'));
                            $avail_amount = 0;
                            if (isset($walletDetail->row()->total)) {
                                $avail_amount = $walletDetail->row()->total;
                            }
                            $returnArr['wallet_amount'] = (string) $avail_amount;

                            /* Sending Mail notification about registration */
                            $this->mail_model->send_user_registration_mail($last_insert_id);

                            $returnArr['message'] = $this->format_string('Successfully registered','successfully_registered');
                            $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($last_insert_id)), array('image'));
                            if ($userVal->row()->image == '') {
                                $user_image = USER_PROFILE_IMAGE_DEFAULT;
                            } else {
                                $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                            }
                            $returnArr['user_image'] = base_url() . $user_image;
                            $returnArr['user_id'] = (string) $last_insert_id;
                            $returnArr['soc_key'] = md5((string) $last_insert_id);
                            $returnArr['user_name'] = $user_name;
                            $returnArr['email'] = $email;
                            $returnArr['country_code'] = $country_code;
                            $returnArr['phone_number'] = $phone_number;
                            $returnArr['referal_code'] = $referal_code;
                            $returnArr['key'] = $key;
                            $returnArr['status'] = '1';
                            $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('name'));
                            $category = '';
                            if ($categoryResult->num_rows() > 0) {
                                $category = $categoryResult->row()->_id;
                            }
                            $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                            $returnArr['category'] = (string) $category;
                        } else {
                            $returnArr['message'] = $this->format_string('Registration Failure','registration_failure');
                        }
                    } else {
                        $returnArr['message'] = $this->format_string('Invalid referral code','invaild_referral');
                    }
                }
            } else {
                $returnArr['message'] = $this->format_string("Invalid email address","invalid_email_address");
            }
        } else {
            $returnArr['message'] = $this->format_string("Some parameters are missing","some_parameters_missing");
        }

        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	/**
     *
     * User track the river location after booking confirmed
     *
     * */
    public function track_driver_location() {
        $ride_id = $this->input->post('ride_id');
        if ($ride_id == '') {
            $ride_id = $this->input->get('ride_id');
        }
        $returnArr['status'] = '0';
        if ($ride_id != '') {
            $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'driver', 'coupon_used', 'coupon', 'cancelled'));
            if ($checkRide->num_rows() == 1) {
                $driver_id = $checkRide->row()->driver['id'];
                if ($driver_id != '') {
                    $lat_lon = @explode(',', $checkRide->row()->driver['lat_lon']);
                    $driver_lat = $lat_lon[0];
                    $driver_lon = $lat_lon[1];

                    /*                     * *********   find estimated duration   ********** */
                    $pickupLocArr = $checkRide->row()->booking_information['pickup']['latlong'];

                    $from = $driver_lat . ',' . $driver_lon;
                    $to = $pickupLocArr['lat'] . ',' . $pickupLocArr['lon'];
					
					$mindurationtext = 'N/A';

                    $gmap = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$this->data['google_maps_api_key']);
                    $map_values = json_decode($gmap);
                    $routes = $map_values->routes;
					if(!empty($routes)){
						usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
						$mindurationtext = $routes[0]->legs[0]->duration->text;
					}


                    $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model'));
                    /* Preparing driver information to share with user -- Start */
                    $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                    if (isset($checkDriver->row()->image)) {
                        if ($checkDriver->row()->image != '') {
                            $driver_image = USER_PROFILE_IMAGE . $checkDriver->row()->image;
                        }
                    }
                    $driver_review = 0;
                    if (isset($checkDriver->row()->avg_review)) {
                        $driver_review = $checkDriver->row()->avg_review;
                    }
                    $vehicleInfo = $this->app_model->get_selected_fields(MODELS, array('_id' => new \MongoId($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
                    $vehicle_model = '';
                    if ($vehicleInfo->num_rows() > 0) {
                        $vehicle_model = $vehicleInfo->row()->name;
                    }
					
					
					$pickup_arr = array();
					if($checkRide->row()->booking_information['pickup']['location']!=''){
						$pickup_arr = $checkRide->row()->booking_information['pickup'];
					}
					if (empty($pickup_arr)) {
						$pickup_arr = json_decode("{}");
					}
					
					$drop_arr = array();
					if($checkRide->row()->booking_information['drop']['location']!=''){
						$drop_arr = $checkRide->row()->booking_information['drop'];
					}
					if (empty($drop_arr)) {
						$drop_arr = json_decode("{}");
					}


                    $driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
                        'driver_name' => (string) $checkDriver->row()->driver_name,
                        'driver_email' => (string) $checkDriver->row()->email,
                        'driver_image' => (string) base_url() . $driver_image,
                        'driver_review' => (string) floatval($driver_review),
                        'driver_lat' => (string) floatval($driver_lat),
                        'driver_lon' => (string) floatval($driver_lon),
                        'rider_lat' => (string) floatval($checkRide->row()->booking_information['pickup']['latlong']['lat']),
                        'rider_lon' => (string) floatval($checkRide->row()->booking_information['pickup']['latlong']['lon']),
                        'min_pickup_duration' => $mindurationtext,
                        'ride_id' => (string) $ride_id,
                        'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
                        'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
                        'vehicle_model' => (string) $vehicle_model,
                        'ride_status' => (string) $checkRide->row()->ride_status,
                        'pickup' => $pickup_arr,
                        'drop' => $drop_arr
                    );
                    /* Preparing driver information to share with user -- End */
                } else {
                    $driver_profile = array();
                }

                /* get driver current location and path */
                $tracking_records = $this->app_model->get_all_details(TRACKING, array('ride_id' => $ride_id));

                $tracking = array();
                if ($tracking_records->num_rows() != 0) {
                    $allStages = $tracking_records->row()->steps;
                    for ($i = 0; $i < count($allStages); $i++) {
                        $lastTime = date('M d, Y h:i A', $allStages[$i]['timestamp']->sec);
                        $tracking[] = array('on_time' => $lastTime,
                            'location' => $allStages[$i]['location']
                        );
                    }
                }
                if (empty($driver_profile)) {
                    $driver_profile = json_decode("{}");
                }
                if (empty($tracking)) {
                    $tracking = json_decode("{}");
                }
                $returnArr['status'] = '1';
                $returnArr['response'] = array('ride_id' => (string) $ride_id, 'driver_profile' => $driver_profile, 'tracking_details' => $tracking);
            } else {
                $returnArr['response'] = $this->format_string('Records not available', 'no_records_found');
            }
        } else {
            $returnArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Share user track the river location after booking confirmed
     *
     * */
    public function share_track_driver_location() {
        $ride_id = $this->input->post('ride_id');
        $mobile_no = $this->input->post('mobile_no');
        if ($ride_id == '') {
            $ride_id = $this->input->get('ride_id');
        }
        if ($mobile_no == '') {
            $mobile_no = $this->input->get('mobile_no');
        }
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        if ($ride_id != '' && $mobile_no != '') {
            $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'driver', 'coupon_used', 'coupon', 'cancelled', 'user'));
            if ($checkRide->num_rows() == 1) {

                $tracking_records = $this->app_model->get_all_details(TRACKING, array('ride_id' => $ride_id));
                $tracking = array();
                if ($tracking_records->num_rows() > 0) {
                    $allStages = $tracking_records->row()->steps;
                    $user_id = $checkRide->row()->user['id'];
                    $user_name = 'unknown';
                    if ($user_id != '') {
                        $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('user_name'));
                        $user_name = $checkUser->row()->user_name;
                    }
                    $location = $allStages[count($allStages) - 1]['locality'];

                    /*                     * *****     send sms to particular user  ******* */
                    $this->sms_model->send_sms_share_driver_tracking_location($mobile_no, $location, $user_name, $ride_id);
                    $returnArr['status'] = '1';
                    $msg = $this->format_string('Your ride has been successfully shared with ', 'ride_successfully_shared_with ');
                    $returnArr['response'] = $msg . ' ' . $mobile_no;
                } else {
                    $returnArr['response'] = $this->format_string('Tracking records not available for this ride', 'trackings_records_not_found');
                }
            } else {
                $returnArr['response'] = $this->format_string('Records not available', 'no_records_found');
            }
        } else {
            $returnArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

	
}

/* End of file user.php */
/* Location: ./application/controllers/api_v2/user.php */