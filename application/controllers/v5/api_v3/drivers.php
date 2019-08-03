<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to Drivers at the app end
 * @author Casperon
 *
 * */
class Drivers extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('driver_model','app_model'));

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
        if (array_key_exists("Apptype", $headers))
            $this->Apptype = $headers['Apptype'];
        if (array_key_exists("Driverid", $headers))
            $this->Driverid = $headers['Driverid'];
        if (array_key_exists("Apptoken", $headers))
            $this->Token = $headers['Apptoken'];
        try {
            if ($this->Driverid != "" && $this->Token != "" && $this->Apptype != "") {
                $deadChk = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($this->Driverid)), array('push_notification'));
                if ($deadChk->num_rows() > 0) {
					$storedToken ='';
                    if (strtolower($deadChk->row()->push_notification['type']) == "ios") {
                        $storedToken = $deadChk->row()->push_notification["key"];
                    }
                    if (strtolower($deadChk->row()->push_notification['type']) == "android") {
                        $storedToken = $deadChk->row()->push_notification["key"];
                    }
					
					$c_fun= (string)$this->router->fetch_method();
					$apply_function = array('login_driver','logout_driver','update_driver_location');
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
     * This Function returns the trip payment process
     *
     * */
    public function check_trip_payment_status() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            if ($driver_id != '' && $ride_id != '') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array());
                if ($checkDriver->num_rows() == 1) {
					$checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id), array('ride_id', 'ride_status','pay_status', 'booking_information','driver_review_status'));
                    if ($checkRide->num_rows() == 1) {
						$trip_waiting = 'Yes';
						$ratting_submited = 'No';
						if($checkRide->row()->ride_status=='Completed'){
							$trip_waiting = 'No';
						}
						if($checkRide->row()->ride_status=='Finished'){
							$trip_waiting = 'Yes';
						}
						
						if($trip_waiting == 'Yes'){
							if(isset($checkRide->row()->driver_review_status)){
								if($checkRide->row()->driver_review_status=='Yes'){
									$ratting_submited = 'Yes';
								}
							}
						}
						if($ratting_submited == 'Yes'){
							$ratting_pending = 'No';
						}else{
							$ratting_pending = 'Yes';
						}
						
						$responseArr['status'] = '1';
						$responseArr['response'] = array('trip_waiting'=>(string)$trip_waiting,
														'ratting_pending'=>(string)$ratting_pending,
														);
					}else{
						$responseArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
					}
                } else {
                    $responseArr['response'] = $this->format_string('Authentication Failed','authentication_failed');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	/**
	*
	* The following functions are used to returns the informations while registerings as a driver
	*
	* */
    public function get_location_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('status' => 'Active'), array('city','fare'), array('city' => 'ASC'));
            if ($locationsVal->num_rows() > 0) {
                $locationsArr = array();
                foreach ($locationsVal->result() as $row) {
					if(isset($row->fare)){
						if(is_array($row->fare)){
							if(!empty($row->fare)){
								$locationsArr[] = array('id' => (string) $row->_id,
									'city' => (string) $row->city
								);
							}
						}
					}
                }
                $returnArr['status'] = '1';
                if (empty($locationsArr)) {
                    $locationsArr = json_decode("{}");
                }
                $returnArr['response'] = array('locations' => $locationsArr);
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
    public function get_category_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $location_id = (string) $this->input->post('location_id');

            if ($location_id != '') {
                $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('_id' => new \MongoId($location_id)), array('city', 'avail_category', 'fare'));
                if ($locationsVal->num_rows() > 0) {
					$a_cat = $locationsVal->row()->fare;
                    $categoryResult = $this->app_model->get_available_category(CATEGORY, $locationsVal->row()->avail_category);
                    $categoryArr = array();
                    if ($categoryResult->num_rows() > 0) {
                        foreach ($categoryResult->result() as $row) {
							$cId = (string)$row->_id;
							if(array_key_exists($cId,$a_cat)){
								$categoryArr[] = array('id' => (string) $row->_id,
									'category' => (string) $row->name
								);
							}
                        }
                    }
                    $returnArr['status'] = '1';
                    if (empty($categoryArr)) {
                        $categoryArr = json_decode("{}");
                    }
                    $returnArr['response'] = array('category' => $categoryArr);
                } else {
                    $returnArr['response'] = $this->format_string("Records not available", "no_records_found");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	public function get_country_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $countriesVal = $this->app_model->get_selected_fields(COUNTRY, array('status' => 'Active'), array('name', 'dial_code'), array('name' => 'ASC'));
            if ($countriesVal->num_rows() > 0) {
                $countriesArr = array();
                foreach ($countriesVal->result() as $row) {
                    $countriesArr[] = array('id' => (string) $row->_id,
                        'name' => (string) $row->name,
                        'dial_code' => (string) $row->dial_code
                    );
                }
                if (empty($countriesArr)) {
                    $countriesArr = json_decode("{}");
                }
                $returnArr['status'] = '1';
                $returnArr['response'] = array('countries' => $countriesArr);
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
    public function get_vehicle_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $category_id = (string) $this->input->post('category_id');

            if ($category_id != '') {
                $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($category_id)), array('name', 'vehicle_type'));
                if ($categoryResult->num_rows() > 0) {
					$vehicle_type= array();
					if(isset($categoryResult->row()->vehicle_type)){
						$vehicle_type = $categoryResult->row()->vehicle_type;
					}
					
					$vehicleResult = $this->driver_model->get_vehicles_list_by_category($vehicle_type); 
					
                    $vehicleArr = array();
                    if ($vehicleResult->num_rows() > 0) {
                        foreach ($vehicleResult->result() as $row) {
							$vehicleArr[] = array('id' => (string) $row->_id,
								'vehicle_type' => (string) $row->vehicle_type
							);
                        }
                    }
                    $returnArr['status'] = '1';
                    if (empty($vehicleArr)) {
                        $vehicleArr = json_decode("{}");
                    }
                    $returnArr['response'] = array('vehicle' => $vehicleArr);
                } else {
                    $returnArr['response'] = $this->format_string("Records not available", "no_records_found");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	public function get_maker_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $makerVal = $this->app_model->get_selected_fields(BRAND, array('status' => 'Active'), array('brand_name'), array('name' => 'ASC'));
            if ($makerVal->num_rows() > 0) {
                $makerArr = array();
                foreach ($makerVal->result() as $row) {
                    $makerArr[] = array('id' => (string) $row->_id,
                        'brand_name' => (string) $row->brand_name
                    );
                }
                if (empty($makerArr)) {
                    $makerArr = json_decode("{}");
                }
                $returnArr['status'] = '1';
                $returnArr['response'] = array('maker' => $makerArr);
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
    public function get_model_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $maker_id = (string) $this->input->post('maker_id');
            $vehicle_id = (string) $this->input->post('vehicle_id');

            if ($maker_id != '' && $vehicle_id != '') {
                $makerResult = $this->app_model->get_selected_fields(BRAND, array('_id' => new \MongoId($maker_id)), array());
                if ($makerResult->num_rows() > 0) {
					$brand = $maker_id;
					$modelResult = $this->app_model->get_selected_fields(MODELS, array('brand' => $brand,'type' => $vehicle_id), array('name','year_of_model'));
					
                    $modelArr = array();
                    if ($modelResult->num_rows() > 0) {
                        foreach ($modelResult->result() as $row) {
							$modelArr[] = array('id' => (string) $row->_id,
								'name' => (string) $row->name
							);
                        }
                    }
                    $returnArr['status'] = '1';
                    if (empty($modelArr)) {
                        $modelArr = json_decode("{}");
                    }
                    $returnArr['response'] = array('model' => $modelArr);
                } else {
                    $returnArr['response'] = $this->format_string("Records not available", "no_records_found");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
    public function get_year_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $model_id = (string) $this->input->post('model_id');

            if ($model_id != '') {
				$modelResult = $this->app_model->get_selected_fields(MODELS, array('_id' => new \MongoId($model_id)), array('name','year_of_model'));
				$yearArr = array();
				if ($modelResult->num_rows() > 0) {
					$yearArr = $modelResult->row()->year_of_model;
				}
                $returnArr['status'] = '1';
                if (empty($yearArr)) {
					$yearArr = json_decode("{}");
                }
                $returnArr['response'] = array('model' => $yearArr);
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
    public function check_email_exist() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $model_id = (string) $this->input->post('model_id');

            if ($model_id != '') {
				$modelResult = $this->app_model->get_selected_fields(MODELS, array('_id' => new \MongoId($model_id)), array('name','year_of_model'));
				$yearArr = array();
				if ($modelResult->num_rows() > 0) {
					$yearArr = $modelResult->row()->year_of_model;
				}
                $returnArr['status'] = '1';
                if (empty($yearArr)) {
					$yearArr = json_decode("{}");
                }
                $returnArr['response'] = array('model' => $yearArr);
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
    public function upload_image() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $img = (string) $this->input->post('image');
            if ($img != '') {
				$image_name = '';
				
				$imgPath = 'drivers_documents_temp/';
                $imgName = md5(time() . rand(10, 99999999) . time()) . ".jpg";
                $imageFormat = array('data:image/jpeg;base64','data:image/png;base64','data:image/jpg;base64','data:image/gif;base64');
                $img = str_replace($imageFormat,'', $img);
                $data = base64_decode($img);
                $image = @imagecreatefromstring($data);
                if ($image !== false) {
                    $uploadPath = $imgPath . $imgName;
                    imagejpeg($image, $uploadPath, 100);
                    imagedestroy($image);
					$image_name = $imgName;
					
					$returnArr['status'] = '1';
					$returnArr['response'] = array('image_name' => $image_name);
                } else {
                    $returnArr['response'] = $this->format_string('An error occurred.','error_occurred');
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
    public function send_otp_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $dail_code = (string) $this->input->post('dail_code');
            $mobile_number = (string) $this->input->post('mobile_number');
			
            if ($dail_code != '' && $mobile_number != '') {
				$chkMobile = $this->app_model->get_selected_fields(DRIVERS, array('dail_code'=>$dail_code,'mobile_number'=>$mobile_number), array());
				if ($chkMobile->num_rows() == 0) {
					$otp_string = $this->user_model->get_random_string(6);
					$otp_status = "development";
					if ($this->config->item('twilio_account_type') == 'prod') {
						$otp_status = "production";
						$this->sms_model->opt_for_driver_registration($dail_code, $mobile_number, $otp_string);
					}
					$returnArr['otp_status'] = (string) $otp_status;
					$returnArr['otp'] = (string) $otp_string;
					$returnArr['status'] = '1';
					$returnArr['response'] = $this->format_string("Check you mobile and enter the Code here", "driver_otp_code_success");
				}else{
					$returnArr['response'] = $this->format_string("Mobile Number Already Exist", "mobile_number_already_exit");
				}
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
    public function register() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
			$driver_location = $this->input->post('driver_location');
			$category = (string)$this->input->post('category');
			$driver_name = (string)$this->input->post('driver_name');
			$email = (string)strtolower($this->input->post('email'));
			$password = (string)$this->input->post('password');
			
			$address = (string)$this->input->post('address');
			$county = (string)$this->input->post('county');
			$state = (string)$this->input->post('state');
			$city = (string)$this->input->post('city');
			$postal_code = (string)$this->input->post('postal_code');
			
			$dail_code = (string)$this->input->post('dail_code');
			$mobile_number = (string)$this->input->post('mobile_number');
			
			$mobile_otp = (string)$this->input->post('mobile_otp');
			
			$vehicle_type = (string)$this->input->post('vehicle_type');
			$vehicle_maker = (string)$this->input->post('vehicle_maker');
			$vehicle_model = (string)$this->input->post('vehicle_model');
			$vehicle_model_year = $this->input->post('vehicle_model_year');
			
			$vehicle_number = (string)$this->input->post('vehicle_number');
			
			$temp_image = (string)$this->input->post('image');
			
			$ac = (string)$this->input->post('ac');		#(Yes/No)
			
			$verify_status = 'No';
			$status = (string)'Inactive';		#(Active/Inactive)
			
			
			$checkEmail = $this->driver_model->check_driver_exist(array('email' => $email));
			if ($checkEmail->num_rows() >= 1) {
				$returnArr['response'] = $this->format_string("This email already exist, please register with different email address.", "driver_email_address_already_exist");
			}else{
				$addressArr = array('address' => $address,'county' => $county,'state' => $state,'city' => $city,'postal_code' => $postal_code);
				
				$image = '';
				if ($temp_image!='') {
					@copy('./drivers_documents_temp/' . $temp_image, './images/users/' . $temp_image);
					@copy('./drivers_documents_temp/' . $temp_image, './images/users/thumb/' . $temp_image);
					$this->ImageResizeWithCrop(210, 210, $temp_image, './images/users/thumb/');
					$image = $temp_image;
				}
				
				$driver_commission = 0;
				$cond = new \MongoId($driver_location);
				$get_loc_commison = $this->driver_model->get_selected_fields(LOCATIONS, $cond, array('site_commission'));
				if (isset($get_loc_commison->row()->site_commission)) {
					$driver_commission = $get_loc_commison->row()->site_commission;
				}
				
				$dataArr = array("driver_location"=>(string)$driver_location,
								"category"=>new MongoId($category),
								'driver_commission' => floatval($driver_commission),
								"email"=>$email,
								"driver_name"=>(string)$driver_name,
								"password"=>md5($password),
								"vehicle_maker"=>(string)$vehicle_maker,
								"vehicle_model"=>(string)$vehicle_model,
								"vehicle_model_year"=>(string)$vehicle_model_year,
								"vehicle_number"=>(string)$vehicle_number,
								"status"=>(string)$status,
								"verify_status"=>"No",
								"created"=>date("Y-m-d H:i:s"),
								'image' => (string)$image,
								"vehicle_type"=>new MongoId($vehicle_type),
								"ac"=>(string)$ac,
								"no_of_rides"=>floatval(0),
								"availability"=>"No",
								"mode"=>"Available",
								"dail_code"=>(string)$dail_code,
								"mobile_number"=>(string)$mobile_number,
								"address"=>$addressArr,
								"documents"=>array()
								) ;
				#echo '<pre>'; print_r($dataArr); die;

				$condition = array();
				$this->driver_model->simple_insert(DRIVERS,$dataArr);
				$last_insert_id = $this->cimongo->insert_id();
				$fields = array(
					'username' => (string) $last_insert_id,
					'password' => md5((string) $last_insert_id)
				);
				$url = $this->data['soc_url'] . 'create-user.php';
				$this->load->library('curl');
				$output = $this->curl->simple_post($url, $fields);

				/* Update Stats Starts */
				$current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
				$field = array('driver.hour_' . date('H') => 1, 'driver.count' => 1);
				$this->driver_model->update_stats(array('day_hour' => $current_date), $field, 1);
				/* Update Stats End */

				$this->mail_model->send_driver_register_confirmation_mail((string)$last_insert_id);
				
				$returnArr['status'] = '1';
				$returnArr['response'] = $this->format_string("You have registered successfully", "driver_registered_successfully");
			}
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
}

/* End of file drivers.php */
/* Location: ./application/controllers/api_v3/drivers.php */