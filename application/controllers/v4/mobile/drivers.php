<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
        $this->load->model(array('driver_model'));
        $this->load->model(array('app_model'));
        /* $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
          if(stripos($ua,'cabily2k15') === false) {
          show_404();
          } */

        header('Content-type:application/json;charset=utf-8');
		/* Authentication Begin */
        $headers = $this->input->request_headers();
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
					$storedToken = '';
                    if (strtolower($deadChk->row()->push_notification['type']) == "ios") {
                        $storedToken = $deadChk->row()->push_notification["key"];
                    }
                    if (strtolower($deadChk->row()->push_notification['type']) == "android") {
                        $storedToken = $deadChk->row()->push_notification["key"];
                    }
					$c_fun= $this->router->fetch_method();
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
     * This function creates a new account for driver
     *
     * */
    public function register_driver() {
        
    }

    /**
     *
     * Login Driver 
     *
     * */
    public function login_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = (string) $this->input->post('deviceToken');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($email != '' && $password != '') {
                if (valid_email($email)) {
                    $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('email' => strtolower($email), 'password' => md5($password)), array('email', 'user_name', 'phone_number','push_notification'));
                    if ($checkDriver->num_rows() == 1) {
                        $push_data = array();
                        $key = '';
                        if ($gcm_id != "") {
                            $key = $gcm_id;
                            $push_data = array('push_notification.key' => $gcm_id, 'push_notification.type' => 'ANDROID');
                        } else if ($deviceToken != "") {
                            $key = $deviceToken;
                            $push_data = array('push_notification.key' => $deviceToken, 'push_notification.type' => 'IOS');
                        }
						$is_alive_other = "No";
						if (isset($checkDriver->row()->push_notification)) {
							if ($checkDriver->row()->push_notification['type'] != '') {
								if ($checkDriver->row()->push_notification['type'] == "ANDROID") {
									$existingKey = $checkDriver->row()->push_notification["key"];
								}
								if ($checkDriver->row()->push_notification['type'] == "IOS") {
									$existingKey = $checkDriver->row()->push_notification["key"];
								}
								if ($existingKey != $key) {
									$is_alive_other = "Yes";
								}
							}
						}
						$returnArr['is_alive_other'] = (string) $is_alive_other;
						
						
                        if (!empty($push_data)) {
                            $this->driver_model->update_details(DRIVERS, array('push_notification.key' => '', 'push_notification.type' => ''), $push_data);
                            $this->driver_model->update_details(DRIVERS, $push_data, array('_id' => new \MongoId($checkDriver->row()->_id)));
                        }
						
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
                        $driverVal = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($checkDriver->row()->_id)), array('email', 'image', 'driver_name', 'push_notification', 'vehicle_number', 'vehicle_model', 'password', 'category'));
                        if (isset($driverVal->row()->image)) {
                            if ($driverVal->row()->image == '') {
                                $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                            } else {
                                $driver_image = USER_PROFILE_IMAGE . $driverVal->row()->image;
                            }
                        } else {
                            $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                        }
                        $modelVal = $this->driver_model->get_selected_fields(MODELS, array('_id' => new \MongoId($driverVal->row()->vehicle_model)), array('name', 'brand_name'));
                        $vehicle_model = '';
                        if ($modelVal->num_rows() > 0) {
                            if (isset($modelVal->row()->name)) {
                                $vehicle_model = $modelVal->row()->name;
                            }
                        }
                        $categoryInfo = $this->driver_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($driverVal->row()->category)), array('_id', 'name', 'brand_name'));
                        $driver_category = '';
                        if ($categoryInfo->num_rows() > 0) {
                            $driver_category = $categoryInfo->row()->name;
                        }

                        $returnArr['driver_image'] = (string) base_url() . $driver_image;
                        $returnArr['driver_id'] = (string) $checkDriver->row()->_id;
                        $returnArr['driver_name'] = (string) $driverVal->row()->driver_name;
                        $returnArr['sec_key'] = md5((string) $driverVal->row()->_id);
                        $returnArr['email'] = (string) $driverVal->row()->email;
                        $returnArr['vehicle_number'] = (string) $driverVal->row()->vehicle_number;
                        $returnArr['vehicle_model'] = (string) $vehicle_model;
                        $returnArr['key'] = (string) $key;
                    } else {
                        $returnArr['response'] = $this->format_string('Please check the email and password and try again', 'please_check_email_and_password');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                if ($deviceToken == "") {
                    $returnArr['response'] = $this->format_string("Cannot recognize your device", "cannot_recognise_device");
                } else {
                    $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
                }
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Logout Driver 
     *
     * */
    public function logout_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $device = $this->input->post('device');

            if ($driver_id != '' && $device != '') {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('push_notification'));
                if ($checkDriver->num_rows() == 1) {
                    if ($device == 'IOS' || $device == 'ANDROID') {
                        $condition = array('_id' => new \MongoId($driver_id));
                        $this->driver_model->update_details(DRIVERS, array('availability' => 'No', 'push_notification.key' => '', 'push_notification.type' => ''), $condition);
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string("You are logged out", "you_are_logged_out");
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid inputs', 'invalid_input');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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
     * Update driver Location
     *
     * */
    public function update_driver_location() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');

            $c_ride_id = $this->input->post('ride_id');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 3) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'mode', 'availability','verify_status'));
                if ($checkDriver->num_rows() == 1) {
                    $geo_data = array('loc' => array('lon' => floatval($longitude), 'lat' => floatval($latitude)),'last_active_time'=>new \MongoDate(time()));
                    $this->driver_model->update_details(DRIVERS, $geo_data, array('_id' => new \MongoId($driver_id)));

                    $ride_id = '';
					$verify_status = 'No';
					$errorMsg = 'You do not have a verified account, Contact us for more information';
					if(isset($checkDriver->row()->verify_status)){
						if ($checkDriver->row()->verify_status == 'Yes') {
							$verify_status = 'Yes';
							$errorMsg = '';
						}
					}
                    /* $available = $this->format_string("Available", "available");
                    $unavailable = $this->format_string("Unavailable", "unavailable"); */
                    $available = "Available";
                    $unavailable = "Unavailable";
                    if ($checkDriver->row()->mode == 'Available') {
                        $availability_string = $available;
                    } else if ($checkDriver->row()->mode == 'Booked') {
                        $checkPending = $this->app_model->get_uncompleted_trips($driver_id, array('ride_id', 'ride_status', 'pay_status'));
                        if ($checkPending->num_rows() > 0) {
                            $availability_string = $unavailable;
                            $ride_id = $checkPending->row()->ride_id;
							$errorMsg = 'You have a pending trip / transaction. Please tap to view. Without resolving this you will not get ride requests.';
                        } else {
                            $availability_string = $available;
                            $avail_data = array('availability' => 'Yes', 'last_active_time' => time());
                            $this->driver_model->update_details(DRIVERS, $avail_data, array('_id' => new \MongoId($driver_id)));
                        }
                    }

                    if ($c_ride_id != '' && $c_ride_id != NULL) {
                        $checkInfo = $this->driver_model->get_all_details(TRACKING, array('ride_id' => $c_ride_id));
					
						$latlng = $latitude . ',' . $longitude;
						$gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
						$mapValues = json_decode($gmap)->results;
						if(!empty($mapValues)){
							$formatted_address = $mapValues[0]->formatted_address;
							$cuurentLoc = array('timestamp' => new \MongoDate(time()),
								'locality' => (string) $formatted_address,
								'location' => array('lat' => floatval($latitude), 'lon' => floatval($longitude))
							);
							
							if ($checkInfo->num_rows() > 0) {
								$this->app_model->simple_push(TRACKING, array('ride_id' => (string) $c_ride_id), array('steps' => $cuurentLoc));
							} else {
								$this->app_model->simple_insert(TRACKING, array('ride_id' => (string) $c_ride_id));
								$this->app_model->simple_push(TRACKING, array('ride_id' => (string) $c_ride_id), array('steps' => $cuurentLoc));
							}
						}
                    }

                    if (empty($availability_string)) {
                        $availability_string = json_decode("{}");
                    }

                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('message' => $this->format_string('Geo Location Updated', 'geo_location_updated'),
													'availability' => $availability_string,
													'ride_id' => $ride_id,
													'verify_status' => $verify_status,
													'errorMsg' => $errorMsg
												);
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * Update driver availablity
     *
     * */
    public function update_driver_availablity() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $availability = $this->input->post('availability');
            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }
            if ($chkValues >= 2) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id'));
                if ($checkDriver->num_rows() == 1) {
                    $avail_data = array('availability' => $availability, 'last_active_time' => time());
                    $this->driver_model->update_details(DRIVERS, $avail_data, array('_id' => new \MongoId($driver_id)));
                    $returnArr['status'] = '1';
                    $returnArr['response'] = $this->format_string('Availability Updated', 'availability_updated');
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * Update driver Mode
     *
     * */
    public function update_driver_mode() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $type = $this->input->post('type');
            if ($type == '') {
                $type = 'Available';
            }

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }
            if (isset($_GET['dev'])) {
                if ($_GET['dev'] == 'jj') {
                    $avail_data = array('mode' => $type);
                    $this->driver_model->update_details(DRIVERS, $avail_data, array());
                }
            }
            if ($chkValues >= 2) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id'));
                if ($checkDriver->num_rows() == 1) {
                    $avail_data = array('mode' => $type);
                    $this->driver_model->update_details(DRIVERS, $avail_data, array('_id' => new \MongoId($driver_id)));
                    $this->driver_model->update_details(DRIVERS, $avail_data, array());
                    $returnArr['status'] = '1';
                    $returnArr['response'] = $this->format_string('Mode Updated', 'mode_updated');
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This function used for driver will accepting the users requesting for ride
     *
     * */
    public function accept_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            $driver_lat = $this->input->post('driver_lat');
            $driver_lon = $this->input->post('driver_lon');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 4) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'driver_commission'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->driver_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'location.id', 'coupon_used', 'coupon', 'est_pickup_date', 'commission_percent'));
                    if ($checkRide->num_rows() == 1) {
                        if ($checkRide->row()->ride_status == 'Booked') {
                            $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                            if ($userVal->num_rows() > 0) {
                                /* Update the ride information with fare and driver details -- Start */
                                $pickup_lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
                                $pickup_lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
                                $from = $driver_lat . ',' . $driver_lon;
                                $to = $pickup_lat . ',' . $pickup_lon;

								$urls = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$this->data['google_maps_api_key'];
                                $gmap = file_get_contents($urls);
								$map_values = json_decode($gmap);
								$routes = $map_values->routes;
								if(!empty($routes)){
									usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));								
									
									$distance_unit = $this->data['d_distance_unit'];
									$duration_unit = 'min';
									if(isset($checkRide->row()->fare_breakup)){
										if($checkRide->row()->fare_breakup['distance_unit']!=''){
											$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
											$duration_unit = $checkRide->row()->fare_breakup['duration_unit'];
										} 
									}

									$mindistance = 1;
									$minduration = 1;
									$mindurationtext = '';
									$est_pickup_time = time();
									if (!empty($routes[0])) {
										#$mindistance = ($routes[0]->legs[0]->distance->value) / 1000;
										$min_distance = $routes[0]->legs[0]->distance->text;
										if (preg_match('/km/',$min_distance)){
											$return_distance = 'km';
										}else if (preg_match('/mi/',$min_distance)){
											$return_distance = 'mi';
										}else if (preg_match('/m/',$min_distance)){
											$return_distance = 'm';
										} else {
											$return_distance = 'km';
										}
										
										$mindistance = floatval(str_replace(',','',$min_distance));
										if($distance_unit!=$return_distance){
											if($distance_unit=='km' && $return_distance=='mi'){
												$mindistance = $mindistance * 1.60934;
											} else if($distance_unit=='mi' && $return_distance=='km'){
												$mindistance = $mindistance * 0.621371;
											} else if($distance_unit=='km' && $return_distance=='m'){
												$mindistance = $mindistance / 1000;
											} else if($distance_unit=='mi' && $return_distance=='m'){
												$mindistance = $mindistance * 0.00062137;
											}
										}
										$mindistance = floatval(round($mindistance,2));
										
										
										$minduration = ($routes[0]->legs[0]->duration->value) / 60;
										$est_pickup_time = (time()) + $routes[0]->legs[0]->duration->value;
										#$est_pickup_time=($checkRide->row()->booking_information['est_pickup_date']->sec)+$routes[0]->legs[0]->duration->value;
										$mindurationtext = $routes[0]->legs[0]->duration->text;
									}

									$fareDetails = $this->driver_model->get_all_details(LOCATIONS, array('_id' => new \MongoId($checkRide->row()->location['id'])));
									if ($fareDetails->num_rows() > 0) {
										$service_id = $checkRide->row()->booking_information['service_id'];
										if (isset($fareDetails->row()->fare[$service_id])) {
											$peak_time = '';
											$night_charge = '';
											$peak_time_amount = '';
											$night_charge_amount = '';
											$min_amount = 0.00;
											$max_amount = 0.00;
											$service_tax = 0.00;
											if (isset($fareDetails->row()->service_tax)) {
												if ($fareDetails->row()->service_tax > 0) {
													$service_tax = $fareDetails->row()->service_tax;
												}
											}
											$pickup_datetime = $checkRide->row()->booking_information['est_pickup_date']->sec;
											$pickup_date = date('Y-m-d', $checkRide->row()->booking_information['est_pickup_date']->sec);

											if ($fareDetails->row()->peak_time == 'Yes') {
												$time1 = strtotime($pickup_date . ' ' . $fareDetails->row()->peak_time_frame['from']);
												$time2 = strtotime($pickup_date . ' ' . $fareDetails->row()->peak_time_frame['to']);
												$ptc = FALSE;
												if ($time1 > $time2) {
													if (date('A', $pickup_datetime) == 'PM') {
														if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
															$ptc = TRUE;
														}
													} else {
														if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
															$ptc = TRUE;
														}
													}
												} else if ($time1 < $time2) {
													if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
														$ptc = TRUE;
													}
												}
												if ($ptc) {
													$peak_time_amount = $fareDetails->row()->fare[$service_id]['peak_time_charge'];
												}
											}
											if ($fareDetails->row()->night_charge == 'Yes') {
												$time1 = strtotime($pickup_date . ' ' . $fareDetails->row()->night_time_frame['from']);
												$time2 = strtotime($pickup_date . ' ' . $fareDetails->row()->night_time_frame['to']);
												$nc = FALSE;
												if ($time1 > $time2) {
													if (date('A', $pickup_datetime) == 'PM') {
														if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
															$nc = TRUE;
														}
													} else {
														if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
															$nc = TRUE;
														}
													}
												} else if ($time1 < $time2) {
													if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
														$nc = TRUE;
													}
												}
												if ($nc) {
													$night_charge_amount = $fareDetails->row()->fare[$service_id]['night_charge'];
												}
											}
											$fare_breakup = array('min_km' => (string) $fareDetails->row()->fare[$service_id]['min_km'],
												'min_time' => (string) $fareDetails->row()->fare[$service_id]['min_time'],
												'min_fare' => (string) $fareDetails->row()->fare[$service_id]['min_fare'],
												'per_km' => (string) $fareDetails->row()->fare[$service_id]['per_km'],
												'per_minute' => (string) $fareDetails->row()->fare[$service_id]['per_minute'],
												'wait_per_minute' => (string) $fareDetails->row()->fare[$service_id]['wait_per_minute'],
												'peak_time_charge' => (string) $peak_time_amount,
												'night_charge' => (string) $night_charge_amount,
												'distance_unit' => (string) $distance_unit,
												'duration_unit' => (string) $duration_unit
											);
										}
									}

									$vehicleInfo = $this->driver_model->get_selected_fields(MODELS, array('_id' => new \MongoId($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
									$vehicle_model = '';
									if ($vehicleInfo->num_rows() > 0) {
										$vehicle_model = $vehicleInfo->row()->name;
										#$vehicle_model=$vehicleInfo->row()->brand_name.' '.$vehicleInfo->row()->name;
									}
									$driverInfo = array('id' => (string) $checkDriver->row()->_id,
										'name' => (string) $checkDriver->row()->driver_name,
										'email' => (string) $checkDriver->row()->email,
										'phone' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
										'vehicle_model' => (string) $vehicle_model,
										'vehicle_no' => (string) $checkDriver->row()->vehicle_number,
										'lat_lon' => (string) $driver_lat . ',' . $driver_lon,
										'est_eta' => (string) $mindurationtext
									);
									$history = array('booking_time' => $checkRide->row()->booking_information['booking_date'],
										'estimate_pickup_time' => new \MongoDate($est_pickup_time),
										'driver_assigned' => new \MongoDate(time())
									);

									$driver_commission = $checkRide->row()->commission_percent;
									if (isset($checkDriver->row()->driver_commission)) {
										$driver_commission = $checkDriver->row()->driver_commission;
									}

									$rideDetails = array('ride_status' => 'Confirmed',
										'commission_percent' => floatval($driver_commission),
										'driver' => $driverInfo,
										'fare_breakup' => $fare_breakup,
										'tax_breakup' => array('service_tax' => $service_tax),
										'booking_information.est_pickup_date' => new \MongoDate($est_pickup_time),
										'history' => $history
									);
									#echo '<pre>'; print_r($rideDetails); 
									$checkBooked = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'ride_status' => 'Booked'), array('ride_id', 'ride_status'));
									$checkAvailable = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('mode'));
									$availablity = false;
									if ($checkAvailable->row()->mode == 'Available') {
										$availablity = true;
									}
									if ($checkBooked->num_rows() > 0 && $availablity === true) {
										$this->driver_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
										/* Update the ride information with fare and driver details -- End */

										/* Update the coupon usage details */
										if ($checkRide->row()->coupon_used == 'Yes') {
											$usage = array("user_id" => (string) $userVal->row()->_id, "ride_id" => $ride_id);
											$promo_code = (string) $checkRide->row()->coupon['code'];
											$this->driver_model->simple_push(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
										}
										/* Update the driver status to Booked */
										$this->driver_model->update_details(DRIVERS, array('mode' => 'Booked'), array('_id' => new \MongoId($driver_id)));

										/* Update the no of rides  */
										$this->app_model->update_user_rides_count('no_of_rides', $userVal->row()->_id);
										$this->app_model->update_driver_rides_count('no_of_rides', $driver_id);

										/* Update Stats Starts */
										$current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
										$field = array('ride_booked.hour_' . date('H') => 1, 'ride_booked.count' => 1);
										$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
										/* Update Stats End */


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
										$driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
											'driver_name' => (string) $checkDriver->row()->driver_name,
											'driver_email' => (string) $checkDriver->row()->email,
											'driver_image' => (string) base_url() . $driver_image,
											'driver_review' => (string) floatval($driver_review),
											'driver_lat' => floatval($driver_lat),
											'driver_lon' => floatval($driver_lon),
											'min_pickup_duration' => $mindurationtext,
											'ride_id' => $ride_id,
											'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
											'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
											'vehicle_model' => (string) $vehicle_model,
											'pickup_location' => (string) $checkRide->row()->booking_information['pickup']['location'],
											'pickup_lat' => (string) $pickup_lat,
											'pickup_lon' => (string) $pickup_lon
										);
										/* Preparing driver information to share with user -- End */


										/* Preparing user information to share with driver -- Start */
										if ($userVal->row()->image == '') {
											$user_image = USER_PROFILE_IMAGE_DEFAULT;
										} else {
											$user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
										}
										$user_review = 0;
										if (isset($userVal->row()->avg_review)) {
											$user_review = $userVal->row()->avg_review;
										}
										$user_profile = array('user_id' => (string)$userVal->row()->_id,
											'user_name' => $userVal->row()->user_name,
											'user_email' => $userVal->row()->email,
											'phone_number' => (string) $userVal->row()->country_code . $userVal->row()->phone_number,
											'user_image' => base_url() . $user_image,
											'user_review' => floatval($user_review),
											'ride_id' => $ride_id,
											'pickup_location' => $checkRide->row()->booking_information['pickup']['location'],
											'pickup_lat' => $pickup_lat,
											'pickup_lon' => $pickup_lon,
											'pickup_time' => date("h:i A jS M, Y", $checkRide->row()->booking_information['est_pickup_date']->sec)
										);
										/* Preparing user information to share with driver -- End */

										/* Sending notification to user regarding booking confirmation -- Start */
										# Push notification
										if (isset($userVal->row()->push_type)) {
											if ($userVal->row()->push_type != '') {
												$message = $this->format_string('Your ride request confirmed', 'ride_request_confirmed');
												$options = $driver_profile;
												if ($userVal->row()->push_type == 'ANDROID') {
													if (isset($userVal->row()->push_notification_key['gcm_id'])) {
														if ($userVal->row()->push_notification_key['gcm_id'] != '') {
															$this->sendPushNotification($userVal->row()->push_notification_key['gcm_id'], $message, 'ride_confirmed', 'ANDROID', $driver_profile, 'USER');
														}
													}
												}
												if ($userVal->row()->push_type == 'IOS') {
													if (isset($userVal->row()->push_notification_key['ios_token'])) {
														if ($userVal->row()->push_notification_key['ios_token'] != '') {
															$this->sendPushNotification($userVal->row()->push_notification_key['ios_token'], $message, 'ride_confirmed', 'IOS', $driver_profile, 'USER');
														}
													}
												}
											}
										}
										# mail notification
										$this->booking_confirmation_mail_user($user_profile, $driver_profile);
										$this->booking_confirmation_mail_driver($user_profile, $driver_profile);
										/* Sending notification to user regarding booking confirmation -- End */
										
										
										$drop_location = 0;
										$drop_loc = '';$drop_lat = '';$drop_lon = '';
										if($checkRide->row()->booking_information['drop']['location']!=''){
											$drop_location = 1;
											$drop_loc = $checkRide->row()->booking_information['drop']['location'];
											$drop_lat = $checkRide->row()->booking_information['drop']['latlong']['lat'];
											$drop_lon = $checkRide->row()->booking_information['drop']['latlong']['lon'];
										}
										$user_profile['drop_location'] = (string)$drop_location;
										$user_profile['drop_loc'] = (string)$drop_loc;
										$user_profile['drop_lat'] = floatval($drop_lat);
										$user_profile['drop_lon'] = floatval($drop_lon);

										if (empty($user_profile)) {
											$user_profile = json_decode("{}");
										}
										$returnArr['status'] = '1';
										$returnArr['response'] = array('user_profile' => $user_profile, 'message' => $this->format_string("Ride Accepted", "ride_accepted"));
									} else {
										$returnArr['response'] = $this->format_string('you are too late, this ride is booked.', 'you_are_too_late_to_book_this_ride');
									}
								}else{
									$returnArr['response'] = $this->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');
								}
                            } else {
                                $returnArr['response'] = $this->format_string('You cannot accept this ride.', 'you_cannot_accept_this_ride');
                            }
                        } else {
                            $returnArr['response'] = $this->format_string('you are too late, this ride is booked.', 'you_are_too_late_to_book_this_ride');
                        }
                    } else {
                        $returnArr['response'] = $this->format_string("This ride is unavailable", "ride_unavailable");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     * 
     * This function send the mail notification to user regarding booking ride
     *
     * */
    public function booking_confirmation_mail_user($userProfile = array(), $driverProfile = array()) {
        
    }

    /**
     * 
     * This function send the mail notification to driver regarding booking ride
     *
     * */
    public function booking_confirmation_mail_driver($userProfile = array(), $driverProfile = array()) {
        
    }

    /**
     *
     * This Function return the ride cancellation reson for driver 
     *
     * */
    public function driver_cancelling_reason() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 1) {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $reasonVal = $this->app_model->get_selected_fields(CANCELLATION_REASON, array('status' => 'Active', 'type' => 'driver'), array('reason'));
                    if ($reasonVal->num_rows() > 0) {
                        $reasonArr = array();
                        foreach ($reasonVal->result() as $row) {
                            $reasonArr[] = array('id' => (string) $row->_id,
                                'reason' => (string) $row->reason
                            );
                        }
                        if (empty($reasonArr)) {
                            $reasonArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('reason' => $reasonArr);
                    } else {
                        $returnArr['response'] = $this->format_string('No reasons available to cancelling ride', 'no_reasons_available_to_cancel_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This Function used for cancelling a ride by a user
     *
     * */
    public function cancelling_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            $reason = $this->input->post('reason');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 3) {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'driver.id', 'coupon_used', 'coupon', 'cancelled'));

                    if ($checkRide->num_rows() == 1) {

                        $doAction = 0;
                        if ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived') {
                            $doAction = 1;
                            if ($checkRide->row()->ride_status == 'Cancelled') {
                                if ($checkRide->row()->cancelled['primary']['by'] == 'Driver') {
                                    $doAction = 0;
                                }
                            }
                            if (isset($checkRide->row()->cancelled['secondary']['by'])) {
                                    if ($checkRide->row()->cancelled['secondary']['by'] == 'User') {
                                        $doAction = 0;
                                    }
                           }
                        }

                        if ($doAction == 1) {
                            $reasonVal = $this->app_model->get_selected_fields(CANCELLATION_REASON, array('_id' => new \MongoId($reason)), array('reason'));
                            if ($reasonVal->num_rows() > 0) {
                                $reason_id = (string) $reasonVal->row()->_id;
                                $reason_text = (string) $reasonVal->row()->reason;

                                $isPrimary = 'No';
                                /* Update the ride information */
                                if ($checkRide->row()->ride_status != 'Cancelled') {
                                    $rideDetails = array('ride_status' => 'Cancelled',
                                        'cancelled' => array('primary' => array('by' => 'Driver',
                                                'id' => $driver_id,
                                                'reason' => $reason_id,
                                                'text' => $reason_text
                                            )
                                        ),
                                        'history.cancelled_time' => new \MongoDate(time())
                                    );
                                    $isPrimary = 'Yes';
                                } else if ($checkRide->row()->ride_status == 'Cancelled') {
                                    $rideDetails = array('cancelled.secondary' => array('by' => 'Driver',
                                            'id' => $driver_id,
                                            'reason' => $reason_id,
                                            'text' => $reason_text
                                        ),
                                        'history.secondary_cancelled_time' => new \MongoDate(time())
                                    );
                                }
                                $this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));

                                if ($isPrimary == 'Yes') {
                                    /* Update the coupon usage details */
                                    if ($checkRide->row()->coupon_used == 'Yes') {
                                        $usage = array("user_id" => (string) $checkRide->row()->user['id'], "ride_id" => $ride_id);
                                        $promo_code = (string) $checkRide->row()->coupon['code'];
                                        $this->app_model->simple_pull(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
                                    }
                                    /* Update the driver status to Available */
                                    $driver_id = $checkRide->row()->driver['id'];
                                    $this->app_model->update_details(DRIVERS, array('mode' => 'Available'), array('_id' => new \MongoId($driver_id)));

                                    /* Update the no of cancellation under this reason  */
                                    $this->app_model->update_user_rides_count('cancelled_rides', $checkRide->row()->user['id']);
                                    $this->app_model->update_driver_rides_count('cancelled_rides', $driver_id);


                                    /* Push Notification to driver regarding cancelling ride */
                                    $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                                    if (isset($userVal->row()->push_type)) {
                                        if ($userVal->row()->push_type != '') {
                                            $message = $this->format_string("your ride cancelled","your_ride_cancelled");
                                            $options = array('ride_id' => (string) $ride_id);
                                            if ($userVal->row()->push_type == 'ANDROID') {
                                                if (isset($userVal->row()->push_notification_key['gcm_id'])) {
                                                    if ($userVal->row()->push_notification_key['gcm_id'] != '') {
                                                        $this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'ride_cancelled', 'ANDROID', $options, 'USER');
                                                    }
                                                }
                                            }
                                            if ($userVal->row()->push_type == 'IOS') {
                                                if (isset($userVal->row()->push_notification_key['ios_token'])) {
                                                    if ($userVal->row()->push_notification_key['ios_token'] != '') {
                                                        $this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'ride_cancelled', 'IOS', $options, 'USER');
                                                    }
                                                }
                                            }
                                        }
                                    }


                                    /* Update Stats Starts */
                                    $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                                    $field = array('ride_cancel.hour_' . date('H') => 1, 'ride_cancel.count' => 1);
                                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                                    /* Update Stats End */
                                }

                                $returnArr['status'] = '1';
                                $returnArr['response'] = array('ride_id' => (string) $ride_id, 'message' => $this->format_string('Ride Cancelled', 'ride_cancelled'));
                            } else {
                                $returnArr['response'] = $this->format_string('You cannot do this action', 'you_cannot_do_this_action');
                            }
                        } else {
                            if ($checkRide->row()->ride_status == 'Cancelled') {
                                if (isset($checkRide->row()->cancelled['primary']['by'])) {
                                if ($checkRide->row()->cancelled['primary']['by'] == 'User') {
                                    $text =$this->format_string('Already Cancelled By You', 'already_cancelled_you');
                                }
                                }
                                if (isset($checkRide->row()->cancelled['secondary']['by'])) {
                                    if ($checkRide->row()->cancelled['secondary']['by'] == 'User') {
                                        $text =$this->format_string('Already Cancelled By Driver', 'already_cancelled_driver');;
                                    }
                                }
                                $returnArr['response'] = $text;
                            } else  {
                            $returnArr['response'] = $this->format_string("You cannot do this action", "you_cannot_do_this_action");
                            }
                        }
                    } else {
                        $returnArr['response'] = $this->format_string("This ride is unavailable", "ride_unavailable");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
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

    /**
     *
     * This Function updates status of the driver reached on pickup location
     *
     * */
    public function location_arrived() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 1) {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email','loc'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'driver.id'));
                    if ($checkRide->num_rows() == 1) {
                        if ($checkRide->row()->ride_status != 'Cancelled' || $checkRide->row()->ride_status != 'Arrived') {
                            /* Update the ride information */
                            $rideDetails = array('ride_status' => 'Arrived',
                                'history.arrived_time' => new \MongoDate(time())
                            );
                            $this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
							
							$driver_lat = 0;
							$driver_lon = 0;
							if(isset($checkDriver->row()->loc)){
								if(is_array($checkDriver->row()->loc)){
									$driver_lat = floatval($checkDriver->row()->loc['lat']);
									$driver_lon = floatval($checkDriver->row()->loc['lon']);
								}
							}
							
                            /* Notification to user about driver reached his location */
                            $user_id = $checkRide->row()->user['id'];
                            $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                            if (isset($userVal->row()->push_type)) {
                                if ($userVal->row()->push_type != '') {
                                    $message = $this->format_string("Driver arrived on your place","driver_arrived");
                                    $options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id, 'driver_lat' => (string) $driver_lat, 'driver_lon' => (string) $driver_lon);
                                    if ($userVal->row()->push_type == 'ANDROID') {
                                        if (isset($userVal->row()->push_notification_key['gcm_id'])) {
                                            if ($userVal->row()->push_notification_key['gcm_id'] != '') {
                                                $this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'cab_arrived', 'ANDROID', $options, 'USER');
                                            }
                                        }
                                    }
                                    if ($userVal->row()->push_type == 'IOS') {
                                        if (isset($userVal->row()->push_notification_key['ios_token'])) {
                                            if ($userVal->row()->push_notification_key['ios_token'] != '') {
                                                $this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'cab_arrived', 'IOS', $options, 'USER');
                                            }
                                        }
                                    }
                                }
                            }
                            $this->sms_model->sms_on_driver_arraival($ride_id);

                            $returnArr['status'] = '1';
                            $returnArr['response'] = $this->format_string('Status Updated', 'status_updated');
                        } else {
                            $returnArr['response'] = $this->format_string('Ride Cancelled', 'ride_cancelled');
                        }
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This Function updates the ride status to Started (Onride)
     *
     * */
    public function begin_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            $pickup_lat = $this->input->post('pickup_lat');
            $pickup_lon = $this->input->post('pickup_lon');
			
            $drop_lat = (string)$this->input->post('drop_lat');
            $drop_lon = (string)$this->input->post('drop_lon');

            if ($driver_id !='' && $ride_id !='' && $pickup_lat !='' && $pickup_lon !='' && $drop_lat !='' && $drop_lon !='') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'driver.id'));
                    if ($checkRide->num_rows() == 1) {
                        if (($checkRide->row()->ride_status != 'Cancelled' || $checkRide->row()->ride_status != 'Onride') && ($checkRide->row()->ride_status =='Arrived')) {
						
                            $latlng = $pickup_lat . ',' . $pickup_lon;
                            $gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
                            $map_result = json_decode($gmap);
                            $mapValues = $map_result->results;
							
                            $drop_latlng = $drop_lat . ',' . $drop_lon;
                            $urldrop = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $drop_latlng . "&sensor=false".$this->data['google_maps_api_key'];
							$gmap_drop = file_get_contents($urldrop);
                            $drop_result = json_decode($gmap_drop);
                            $mapValues_drop = $drop_result->results;
							
							if(!empty($mapValues) && !empty($mapValues_drop)){
							
								$formatted_address = $mapValues[0]->formatted_address;
								$drop_address = $mapValues_drop[0]->formatted_address;
								
								
								/* Update the ride information */
								$rideDetails = array('ride_status' => 'Onride',
									'booking_information.pickup_date' => new \MongoDate(time()),
									'booking_information.pickup.location' => (string) $formatted_address,
									'booking_information.pickup.latlong' => array('lon' => floatval($pickup_lon),
										'lat' => floatval($pickup_lat)
									),
									'booking_information.drop.location' => (string) $drop_address,
									'booking_information.drop.latlong' => array('lon' => floatval($drop_lon),
										'lat' => floatval($drop_lat)
									),
									'history.begin_ride' => new \MongoDate(time())
								);
								$this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
								
								/* Notification to user about begin trip  */
								$user_id = $checkRide->row()->user['id'];
								$userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
								if (isset($userVal->row()->push_type)) {
									if ($userVal->row()->push_type != '') {
										$message = $this->format_string("Your trip has been started", "your_trip_has_been_started");
										$options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id, 'drop_lat' => (string) $drop_lat, 'drop_lon' => (string) $drop_lon, 'pickup_lat' => (string) $pickup_lat, 'pickup_lon' => (string) $pickup_lon);
										if ($userVal->row()->push_type == 'ANDROID') {
											if (isset($userVal->row()->push_notification_key['gcm_id'])) {
												if ($userVal->row()->push_notification_key['gcm_id'] != '') {
													$this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'trip_begin', 'ANDROID', $options, 'USER');
												}
											}
										}
										if ($userVal->row()->push_type == 'IOS') {
											if (isset($userVal->row()->push_notification_key['ios_token'])) {
												if ($userVal->row()->push_notification_key['ios_token'] != '') {
													$this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'trip_begin', 'IOS', $options, 'USER');
												}
											}
										}
									}
								}
								

								$returnArr['status'] = '1';
								$returnArr['response'] = $this->format_string('Ride Started', 'ride_started');
							
							}else{
								$returnArr['response'] = $this->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');
							}
                        } else {
                            $returnArr['response'] = $this->format_string('Ride Started Already', 'ride_started_already');
                        }
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This Function updates status of the ride to End  (Finished)
     *
     * */
    public function end_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            $drop_lat = $this->input->post('drop_lat');
            $drop_lon = $this->input->post('drop_lon');

            $interrupted = (string) $this->input->post('interrupted');
            $drop_loc = $this->input->post('drop_loc');
            $drop_time = $this->input->post('drop_time');

            $distance = $this->input->post('distance'); // in kilometer(km)
            $wait_time_frame = $this->input->post('wait_time'); // in minutes

           if ($interrupted == 'YES' || $interrupted != 'YES') {

                $wait_time = 0;
                if ($wait_time_frame != '') {
                    $wt = @explode(':', $wait_time_frame);
                   
					$h = 0; $m = 0; $s = 0;
					if(isset($wt[0])) $h = intval($wt[0]);
                    if(isset($wt[1])) $m = intval($wt[1]);
                    if(isset($wt[2])) $s = intval($wt[2]);
				   
                    if ($h > 0) {
                        $wait_time = $h * 60;
                    }
                    if ($m > 0) {
                        $wait_time = $wait_time + ($m);
                    }
					if ($s > 0) {
                        $wait_time = $wait_time + ($s/60);
                    }
                }
            }

            if ($driver_id!='' && $ride_id!='' && $distance!='') {
                $chkValues = 1;
            } else {
                $chkValues = 0;
            }

            if ($chkValues >0) {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
                    if ($checkRide->num_rows() == 1) {
						if($checkRide->row()->ride_status=='Onride'){
							$currency = $checkRide->row()->currency;
							$grand_fare = 0;
							$total_fare = 0;
							$free_ride_time = 0;
							$total_base_fare = 0;
							$total_distance_charge = 0;
							$total_ride_charge = 0;
							$total_waiting_charge = 0;
							$total_peak_time_charge = 0;
							$total_night_time_charge = 0;
							$total_tax = 0;
							$coupon_discount = 0;

							if ($interrupted == 'YES') {
								$gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($drop_loc) . "&sensor=false".$this->data['google_maps_api_key']);
							} else {
								$latlng = $drop_lat . ',' . $drop_lon;
								$gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
							}
                            $map_values = json_decode($gmap);
							$mapValues = $map_values->results;
							if(!empty($mapValues)){
							
								$dropping_address = $mapValues[0]->formatted_address;

								$pickup_time = $checkRide->row()->booking_information['pickup_date']->sec;
								if ($interrupted == 'YES') {
									$drop_time = strtotime($drop_time);
								} else {
									$drop_time = time();
								}
								$ride_time = $drop_time - $pickup_time;
								$ride_time_min = ceil($ride_time / 60);
								
								$ride_begin_time = $checkRide->row()->history['begin_ride']->sec;
								$ride_end_time = $drop_time;									// Trip Timestamp
								$ride_wait_time = ceil($wait_time*60);	// in Seconds
								
								if(($ride_begin_time+$ride_wait_time)<=$ride_end_time){

									$total_base_fare = $checkRide->row()->fare_breakup['min_fare'];
									$min_time = $ride_time_min - $checkRide->row()->fare_breakup['min_time'];
									if ($min_time > 0) {
										$total_ride_charge = ($ride_time_min - $checkRide->row()->fare_breakup['min_time']) * $checkRide->row()->fare_breakup['per_minute'];
									}
									$min_distance = $distance - $checkRide->row()->fare_breakup['min_km'];
									if ($min_distance > 0) {
										$total_distance_charge = ($distance - $checkRide->row()->fare_breakup['min_km']) * $checkRide->row()->fare_breakup['per_km'];
									}
									if ($wait_time > 0) {
										$total_waiting_charge = $wait_time * $checkRide->row()->fare_breakup['wait_per_minute'];
									}
									$total_fare = $total_base_fare + $total_distance_charge + $total_ride_charge + $total_waiting_charge;
									$grand_fare = $total_fare;
									if ($checkRide->row()->fare_breakup['peak_time_charge'] != '') {
										$total_peak_time_charge = $total_fare * $checkRide->row()->fare_breakup['peak_time_charge'];
										$grand_fare =$total_peak_time_charge;
									}
									if ($checkRide->row()->fare_breakup['night_charge'] != '') {
										$total_night_time_charge = $total_fare * $checkRide->row()->fare_breakup['night_charge'];
										$grand_fare =$total_night_time_charge;
									}
									if($grand_fare != $total_fare){
										$grand_fare = $total_peak_time_charge + $total_night_time_charge;
									}else{
										$grand_fare = $total_fare;
									}
									
									
									if ($checkRide->row()->coupon_used == 'Yes') {
										if ($checkRide->row()->coupon['type'] == 'Percent') {
											$coupon_discount = ($grand_fare * 0.01) * $checkRide->row()->coupon['amount'];
										} else if ($checkRide->row()->coupon['type'] == 'Flat') {
											if ($checkRide->row()->coupon['amount'] <= $grand_fare) {
												$coupon_discount = $checkRide->row()->coupon['amount'];
											} else if ($checkRide->row()->coupon['amount'] > $grand_fare) {
												$coupon_discount = $grand_fare;
											}
										}
										$grand_fare = $grand_fare - $coupon_discount;
										if ($grand_fare < 0) {
											$grand_fare = 0;
										}
										$coupon_condition = array('promo_code' => $checkRide->row()->coupon['code']);
										$this->cimongo->where($coupon_condition)->inc('no_of_usage', 1)->update(PROMOCODE);
									}

									
									
									if($total_peak_time_charge>0){
										$total_peak_time_charge = $grand_fare - ($total_fare+$total_tax);
									}
									if($total_night_time_charge>0){
										$total_night_time_charge = $grand_fare - ($total_fare+$total_tax);
									}
									if ($checkRide->row()->tax_breakup['service_tax'] != '') {
										$total_tax = $grand_fare * 0.01 * $checkRide->row()->tax_breakup['service_tax'];
										$grand_fare = $grand_fare + $total_tax;
									}

									/*
									$original_grand_fare=0;
									$original_currency='USD';
									$currencyval=$this->app_model->get_currency_value(round($grand_fare,2),$currency,$original_currency);
									if(!empty($currencyval)){
										$original_grand_fare=$currencyval['CurrencyVal'];
									}
									*/
									
									$total_fare = array('base_fare' => round($total_base_fare, 2),
										'distance' => round($total_distance_charge, 2),
										'free_ride_time' => round($free_ride_time, 2),
										'ride_time' => round($total_ride_charge, 2),
										'wait_time' => round($total_waiting_charge, 2),
										'peak_time_charge' => round($total_peak_time_charge, 2),
										'night_time_charge' => round($total_night_time_charge, 2),
										'total_fare' => round($total_fare, 2),
										'coupon_discount' => round($coupon_discount, 2),
										'service_tax' => round($total_tax, 2),
										'grand_fare' => round($grand_fare, 2),
										'wallet_usage' => 0,
										'paid_amount' => 0
											/* 'original_grand_fare'=>round($original_grand_fare,2),
											  'original_currency'=>$original_currency, */
									);
									$summary = array('ride_distance' => round($distance, 2),
										'ride_duration' => round(ceil($ride_time_min), 2),
										'waiting_duration' => round(ceil($wait_time), 2)
									);
									
									
									$need_payment = 'YES';
									$ride_status = 'Finished';
									$pay_status = 'Pending';
									$isFree = 'NO';
									if ($grand_fare <= 0) {
										$need_payment = 'NO';
										$ride_status = 'Completed';
										$pay_status = 'Paid';
										$isFree = 'Yes';
									}	
									$mins = $this->format_string('mins', 'mins');
									
									$distance_unit = $this->data['d_distance_unit'];
									if(isset($checkRide->row()->fare_breakup['distance_unit'])){
										$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
									}
									$fare_details = array('currency' => $currency,
										'ride_fare' => floatval(round($grand_fare, 2)),
										'ride_distance' => floatval(round($distance, 2)) .' '.$distance_unit,
										'ride_duration' => floatval(round($ride_time_min, 2)) .' '. $mins,
										'waiting_duration' => floatval(round($wait_time, 2)) .' '. $mins,
										'need_payment' => $need_payment
									);


									$amount_commission = 0;
									$driver_revenue = 0;

									$total_grand_fare = $coupon_discount + $grand_fare;
									$amount_commission = ($total_grand_fare * 0.01) * $checkRide->row()->commission_percent;
									$driver_revenue = $total_grand_fare - $amount_commission;

									
									

									/* Update the ride information */
									$rideDetails = array('ride_status' => (string)$ride_status,
										'pay_status' => (string)$pay_status,
										'amount_commission' => floatval(round($amount_commission, 2)),
										'driver_revenue' => floatval(round($driver_revenue, 2)),
										'booking_information.drop_date' => new \MongoDate(time()),
										'booking_information.drop.location' => (string) $dropping_address,
										'booking_information.drop.latlong' => array('lon' => floatval($drop_lon),
											'lat' => floatval($drop_lat)
										),
										'history.end_ride' => new \MongoDate(time()),
										'total' => $total_fare,
										'summary' => $summary
									);
									#echo '<pre>'; print_r($rideDetails); die;
									$this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
									$this->app_model->simple_insert(PAYMENTS, array('ride_id' => (string) $ride_id, 'total' => round($grand_fare, 2), 'transactions' => array()));
									$this->sms_model->opt_for_ride($ride_id);

									/* First ride money credit for referrer */
									$sortArr = array('ride_id' => -1);
									$firstRide = $this->driver_model->get_selected_fields(RIDES, array('user.id' => new \MongoId($checkRide->row()->user['id']), 'ride_status' => 'Completed'), array('_id','ride_id'),$sortArr,1,0);
									
									$isFirstRide = 'No';
									if(isset($firstRide->row()->ride_id)){
										$returnRideId = (string)$firstRide->row()->ride_id;
										if($ride_id == $returnRideId){
											$isFirstRide = 'Yes';
										}
									}
									
									if ($firstRide->num_rows() == 0 || $isFirstRide == 'Yes') {
										$get_referVal = $this->driver_model->get_all_details(REFER_HISTORY, array('history.reference_id' => $checkRide->row()->user['id'], 'history.used' => 'false'));
										if ($get_referVal->num_rows() > 0) {
											$condition = array('history.reference_id' => $checkRide->row()->user['id'],
												'user_id' => new \MongoId($get_referVal->row()->user_id));
												
											

											$trans_amount = 0.00;

											if (is_array($get_referVal->row()->history)) {
												foreach ($get_referVal->row()->history as $key => $value) {
													if ($value['reference_id'] == $checkRide->row()->user['id']) {
														$trans_amount = $value['amount_earns'];
													}
												}
											}
											
											$referrDataArr = array('history.$.used' => 'true','history.$.amount_earns' => floatval($trans_amount));
											$this->driver_model->update_details(REFER_HISTORY, $referrDataArr, $condition);

											$this->driver_model->update_wallet((string) $get_referVal->row()->user_id, 'CREDIT', floatval($trans_amount));
											$walletDetail = $this->driver_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($get_referVal->row()->user_id)), array('total'));
											$avail_amount = 0;
											if (isset($walletDetail->row()->total)) {
												$avail_amount = $walletDetail->row()->total;
											}
											$trans_id = time() . rand(0, 2578);
											$walletArr = array('type' => 'CREDIT',
												'credit_type' => 'referral',
												'ref_id' => (string) $checkRide->row()->user['id'],
												'trans_amount' => floatval($this->config->item('referal_amount')),
												'avail_amount' => floatval($avail_amount),
												'trans_date' => new \MongoDate(time()),
												'trans_id' => $trans_id
											);
											$this->driver_model->simple_push(WALLET, array('user_id' => new \MongoId($get_referVal->row()->user_id)), array('transactions' => $walletArr));
										}
									}

									
									$makeInvoice = 'No';
									/*	Making the automatic payment process start	*/
									#$this->auto_payment_deduct($ride_id);
									$crideNew = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
									if($crideNew->num_rows()>0){
										if($crideNew->row()->ride_status == "Completed"){
											$need_payment = 'NO';
											$makeInvoice = 'Yes';
										}
									}
									/*	Making the automatic payment process end	*/
									

									/* Sending notification to user regarding booking confirmation -- Start */
									# Push notification
									$userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
									if (isset($userVal->row()->push_type)) {
										if ($userVal->row()->push_type != '') {
											if ($need_payment == 'NO') {
												$user_id = $checkRide->row()->user['id'];
												$message = $this->format_string('Ride Completed', 'ride_completed');
												$options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id);
												if ($userVal->row()->push_type == 'ANDROID') {
													if (isset($userVal->row()->push_notification_key['gcm_id'])) {
														if ($userVal->row()->push_notification_key['gcm_id'] != '') {
															$this->sendPushNotification($userVal->row()->push_notification_key['gcm_id'], $message, 'payment_paid', 'ANDROID', $options, 'USER');
														}
													}
												}
												if ($userVal->row()->push_type == 'IOS') {
													if (isset($userVal->row()->push_notification_key['ios_token'])) {
														if ($userVal->row()->push_notification_key['ios_token'] != '') {
															$this->sendPushNotification($userVal->row()->push_notification_key['ios_token'], $message, 'payment_paid', 'IOS', $options, 'USER');
														}
													}
												}
											}else{
												/* $message = $this->format_string('Ride Completed', 'ride_completed');
												$options = $fare_details;
												$options['ride_id'] = (string) $ride_id;
												if ($userVal->row()->push_type == 'ANDROID') {
													if (isset($userVal->row()->push_notification_key['gcm_id'])) {
														if ($userVal->row()->push_notification_key['gcm_id'] != '') {
															$this->sendPushNotification($userVal->row()->push_notification_key['gcm_id'], $message, 'ride_completed', 'ANDROID', $options, 'USER');
														}
													}
												}
												if ($userVal->row()->push_type == 'IOS') {
													if (isset($userVal->row()->push_notification_key['ios_token'])) {
														if ($userVal->row()->push_notification_key['ios_token'] != '') {
															$this->sendPushNotification($userVal->row()->push_notification_key['ios_token'], $message, 'ride_completed', 'IOS', $options, 'USER');
														}
													}
												} */
												
												$message = $this->format_string('Ride Completed', 'ride_completed');
												$options = array('ride_id' => (string) $ride_id);
												if ($userVal->row()->push_type == 'ANDROID') {
													if (isset($userVal->row()->push_notification_key['gcm_id'])) {
														if ($userVal->row()->push_notification_key['gcm_id'] != '') {
															$this->sendPushNotification($userVal->row()->push_notification_key['gcm_id'], $message, 'make_payment', 'ANDROID', $options, 'USER');
														}
													}
												}
												if ($userVal->row()->push_type == 'IOS') {
													if (isset($userVal->row()->push_notification_key['ios_token'])) {
														if ($userVal->row()->push_notification_key['ios_token'] != '') {
															$this->sendPushNotification($userVal->row()->push_notification_key['ios_token'], $message, 'make_payment', 'IOS', $options, 'USER');
														}
													}
												}
												
											}
										}
									}
									if ($need_payment == 'NO' && $isFree == 'Yes') {
										$pay_summary = array('type' => 'FREE');
										$paymentInfo = array('pay_summary' => $pay_summary);
										$this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
										/* Update Stats Starts */
										$current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
										$field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
										$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
										/* Update Stats End */
										$avail_data = array('mode' => 'Available', 'availability' => 'Yes');
										$this->app_model->update_details(DRIVERS, $avail_data, array('_id' => new \MongoId($driver_id)));
										$trans_id = time() . rand(0, 2578);
										$transactionArr = array('type' => 'Coupon',
											'amount' => floatval($grand_fare),
											'trans_id' => $trans_id,
											'trans_date' => new \MongoDate(time())
										);
										$this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
										$makeInvoice = 'Yes';
									}
									
									if (empty($fare_details)) {
										$fare_details = json_decode("{}");
									}else{
										$fare_details['need_payment'] = $need_payment;
									}
									
									if($makeInvoice == 'Yes'){
										$this->app_model->update_ride_amounts($ride_id);
										#	make and sending invoice to the rider 	#
										$fields = array(
											'ride_id' => (string) $ride_id
										);
										$url = base_url().'prepare-invoice';
										$this->load->library('curl');
										$output = $this->curl->simple_post($url, $fields);
									}
									$receive_cash = 'Disable';
									if ($this->config->item('pay_by_cash') != '' && $this->config->item('pay_by_cash') != 'Disable') {
										$receive_cash = 'Enable';
									}
									
									
									$returnArr['status'] = '1';
									$returnArr['response'] = array('need_payment' => $need_payment, 
																	'receive_cash' => $receive_cash, 
																	'fare_details' => $fare_details, 
																	'message' => $this->format_string('Ride Completed', 'ride_completed'));
								}else{
									$returnArr['response'] = $this->format_string("Entered inputs are incorrect", "invalid_trip_end_inputs");
								}
							
							}else{
								$returnArr['response'] = $this->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');
							}
						} else {
							$returnArr['response'] = $this->format_string("This trip has been already ended", "already_trip_completed");
						}
                    } else {
                        $returnArr['response'] = $this->format_string("This trip has been already ended", "already_trip_completed");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This Function return the rider informations
     *
     * */
    public function get_rider_information() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 2) {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'driver.id'));
                    if ($checkRide->num_rows() == 1) {
                        $user_id = $checkRide->row()->user['id'];
                        $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email', 'user_name', 'country_code', 'phone_number', 'image', 'avg_review'));
                        $infoArr = array();
                        if ($checkUser->num_rows() == 1) {
                            if ($checkUser->row()->image == '') {
                                $user_image = USER_PROFILE_IMAGE_DEFAULT;
                            } else {
                                $user_image = USER_PROFILE_IMAGE . $checkUser->row()->image;
                            }
                            $user_review = 0;
                            if (isset($checkUser->row()->avg_review)) {
                                $user_review = $checkUser->row()->avg_review;
                            }
                            $infoArr = array('user_name' => $checkUser->row()->user_name,
                                'user_id' => (string) $checkUser->row()->_id,
                                'user_email' => $checkUser->row()->email,
                                'user_phone' => $checkUser->row()->country_code . '' . $checkUser->row()->phone_number,
                                'user_image' => base_url() . $user_image,
                                'user_review' => floatval($user_review),
                                'ride_id' => $ride_id
                            );
                        }
                        if (empty($infoArr)) {
                            $infoArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('information' => $infoArr);
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This Function returns the driver rides list
     *
     * */
    public function driver_all_ride_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $type = (string) $this->input->post('trip_type');
            if ($type == '')
                $type = 'all';

            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_ride_list_for_driver($driver_id, $type, array('booking_information', 'ride_id', 'ride_status'));
                    $rideArr = array();
                    if ($checkRide->num_rows() > 0) {
                        foreach ($checkRide->result() as $ride) {
                            $group = 'all';
                            if ($ride->ride_status == 'Onride' || $ride->ride_status == 'Confirmed' || $ride->ride_status == 'Arrived') {
                                $group = 'onride';
                            } else if ($ride->ride_status == 'Completed' || $ride->ride_status == 'Finished') {
                                $group = 'completed';
                            }
                            $rideArr[] = array('ride_id' => $ride->ride_id,
                                'ride_time' => date("h:i A", $ride->booking_information['booking_date']->sec),
                                'ride_date' => date("jS M, Y", $ride->booking_information['booking_date']->sec),
                                'pickup' => $ride->booking_information['pickup']['location'],
                                'group' => $group,
                                'datetime' => date("d-m-Y", $ride->booking_information['booking_date']->sec),
                            );
                        }
                    }
                    $total_rides = intval($checkRide->num_rows());
                    $returnArr['status'] = '1';
                    if (empty($rideArr)) {
                        $rideArr = json_decode("{}");
                    }
                    $returnArr['response'] = array('total_rides' => (string) $total_rides, 'rides' => $rideArr);
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some of the parameters are missing","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This Function return the drivers particular ride details
     *
     * */
    public function view_driver_ride_information() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');

            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id'));
                if ($driverVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $fareArr = array();
                        $summaryArr = array();
                        if (isset($checkRide->row()->summary)) {
                            if (is_array($checkRide->row()->summary)) {
                                $summaryArr = $checkRide->row()->summary;
                            }
                        }
                        if (isset($checkRide->row()->total)) {
                            if (is_array($checkRide->row()->total)) {
                                $total_bill = 0.00;
                                $tips_amount = 0.00;
                                $coupon_discount = 0.00;
                                $grand_bill = 0.00;
                                $total_paid = 0.00;
                                $wallet_usage = 0.00;
                                if (isset($checkRide->row()->total['total_fare'])) {
                                    $total_bill = $checkRide->row()->total['total_fare'];
                                }

                                if (isset($checkRide->row()->total['tips_amount'])) {
                                    $tips_amount = $checkRide->row()->total['tips_amount'];
                                }

                                $tips_status = '0';
                                if ($tips_amount > 0) {
                                    $tips_status = '1';
                                }


                                if (isset($checkRide->row()->total['coupon_discount'])) {
                                    $coupon_discount = $checkRide->row()->total['coupon_discount'];
                                }
                                if (isset($checkRide->row()->total['grand_fare'])) {
                                    $grand_bill = $checkRide->row()->total['grand_fare'];
                                }
                                if (isset($checkRide->row()->total['paid_amount'])) {
                                    $total_paid = $checkRide->row()->total['paid_amount'];
                                }
                                if (isset($checkRide->row()->total['wallet_usage'])) {
                                    $wallet_usage = $checkRide->row()->total['wallet_usage'];
                                }
                                $fareArr = array('total_bill' => (string) floatval(round($total_bill, 2)),
                                    'coupon_discount' => (string) floatval(round($coupon_discount, 2)),
                                    'grand_bill' => (string) floatval(round($grand_bill, 2)),
                                    'total_paid' => (string) floatval(round($total_paid, 2)),
                                    'wallet_usage' => (string) floatval(round($wallet_usage, 2))
                                );

                                $tipsArr = array('tips_status' => $tips_status,
                                    'tips_amount' => (string) floatval($tips_amount)
                                );
                            }
                        }

                        $pay_status = '';
                        if (isset($checkRide->row()->pay_status)) {
                            $pay_status = $checkRide->row()->pay_status;
                        }


                        $doAction = 0;
                        if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived') {
                            $doAction = 1;
                            if ($checkRide->row()->ride_status == 'Cancelled') {
                                 $doAction = 0;
                               /* if ($checkRide->row()->cancelled['primary']['by'] == 'Driver') {
                                    $doAction = 0;
                                }*/
                            }
                        }
                        $iscontinue = 'NO';
                        if ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride') {
                            if ($checkRide->row()->ride_status == 'Confirmed') {
                                $iscontinue = 'arrived';
                            }
                            if ($checkRide->row()->ride_status == 'Arrived') {
                                $iscontinue = 'begin';
                            }
                            if ($checkRide->row()->ride_status == 'Onride') {
                                $iscontinue = 'end';
                            }
                        }
                        $user_profile = array();
                        if ($iscontinue != 'NO') {
                            $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                            if ($userVal->num_rows() > 0) {
                                if ($userVal->row()->image == '') {
                                    $user_image = USER_PROFILE_IMAGE_DEFAULT;
                                } else {
                                    $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                                }
                                $user_review = 0;
                                if (isset($userVal->row()->avg_review)) {
                                    $user_review = $userVal->row()->avg_review;
                                }
								
								$drop_location = 0;
								$drop_loc = '';$drop_lat = '';$drop_lon = '';
								if($checkRide->row()->booking_information['drop']['location']!=''){
									$drop_location = 1;
									$drop_loc = $checkRide->row()->booking_information['drop']['location'];
									$drop_lat = $checkRide->row()->booking_information['drop']['latlong']['lat'];
									$drop_lon = $checkRide->row()->booking_information['drop']['latlong']['lon'];
								}
								
								
                                $user_profile = array('user_name' => $userVal->row()->user_name,
                                    'user_email' => $userVal->row()->email,
                                    'phone_number' => (string) $userVal->row()->country_code . $userVal->row()->phone_number,
                                    'user_image' => base_url() . $user_image,
                                    'user_review' => floatval($user_review),
                                    'ride_id' => $ride_id,
                                    'pickup_location' => $checkRide->row()->booking_information['pickup']['location'],
                                    'pickup_lat' => $checkRide->row()->booking_information['pickup']['latlong']['lat'],
                                    'pickup_lon' => $checkRide->row()->booking_information['pickup']['latlong']['lon'],
                                    'pickup_time' => date("h:i A jS M, Y", $checkRide->row()->booking_information['est_pickup_date']->sec),
									'drop_location' => (string)$drop_location,
									'drop_loc' => (string)$drop_loc,
									'drop_lat' => (string)$drop_lat,
									'drop_lon' => (string)$drop_lon,
                                );
                            }
                        }

                        $dropArr = array();
                        if ($checkRide->row()->booking_information['drop']['location']!='') {
                            $dropArr = $checkRide->row()->booking_information['drop'];
                        }
						if (empty($dropArr)) {
							$dropArr = json_decode("{}");
						}
						$distance_unit = $this->data['d_distance_unit'];
						if(isset($checkRide->row()->fare_breakup['distance_unit'])){
							$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
						}
						
						$invoice_path = 'trip_invoice/'.$ride_id.'_path.jpg'; 
						if(file_exists($invoice_path)) {
							$invoice_src = base_url().$invoice_path;
						} else {
							$invoice_src = '';
						}
						
						$drop_date_time = '';
						if(isset($checkRide->row()->booking_information['drop_date']->sec)){
							$drop_date_time = date("h:i A", $checkRide->row()->booking_information['drop_date']->sec) . ' on ' . date("jS M, Y", $checkRide->row()->booking_information['drop_date']->sec);
						}
						
                        $responseArr = array('currency' => $checkRide->row()->currency,
                            'cab_type' => $checkRide->row()->booking_information['service_type'],
                            'ride_id' => $checkRide->row()->ride_id,
                            'ride_status' => $checkRide->row()->ride_status,
                            'do_cancel_action' => (string) $doAction,
                            'pay_status' => $pay_status,
                            'pickup' => $checkRide->row()->booking_information['pickup'],
                            'drop' => $dropArr,
                            'pickup_date' => date("h:i A", $checkRide->row()->booking_information['booking_date']->sec) . ' on ' . date("jS M, Y", $checkRide->row()->booking_information['booking_date']->sec),
							'drop_date' => $drop_date_time,
                            'summary' => $summaryArr,
                            'fare' => $fareArr,
                            'tips' => $tipsArr,
                            'continue_ride' => $iscontinue,
                            'distance_unit' => $distance_unit,
							'invoice_src' => $invoice_src
                        );
						
						$receive_cash = 'Disable';
						if ($this->config->item('pay_by_cash') != '' && $this->config->item('pay_by_cash') != 'Disable') {
							$receive_cash = 'Enable';
						}
						
						
                        if (empty($responseArr)) {
                            $responseArr = json_decode("{}");
                        }
                        if (empty($user_profile)) {
                            $user_profile = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('receive_cash' => $receive_cash,'details' => $responseArr, 'user_profile' => $user_profile);
                    } else {
                        $returnArr['response'] = $this->format_string("Records not available", "no_records_found");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
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

    /**
     *
     * This Function return the transaction list
     *
     * */
    public function get_payment_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');


            if ($driver_id != '') {
                $driverChek = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array());
                if ($driverChek->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $paymentArr = array();
                        $pay_by_cash = 'Disable';
                        $use_wallet_amount = 'Disable';
                        if ($this->config->item('pay_by_cash') != '') {
                            //$pay_by_cash = $this->config->item('pay_by_cash');
                            $pay_by_cash_string = $this->format_string('Pay by Cash', 'pay_by_cash');
                            $paymentArr[] = array('name' => $pay_by_cash_string, 'code' => 'cash');
                        }
                        if ($this->config->item('use_wallet_amount') != '') {
                            //$use_wallet_amount = $this->config->item('use_wallet_amount');
                            $user_my_wallet = $this->format_string('Use my wallet/money', 'user_my_wallet');
                            $paymentArr[] = array('name' => $user_my_wallet, 'code' => 'wallet');
                        }
                        $getPaymentgatway = $this->app_model->get_all_details(PAYMENT_GATEWAY, array('status' => 'Enable'));
                        if ($getPaymentgatway->num_rows() > 0) {
                            foreach ($getPaymentgatway->result() as $row) {
                                $paymentArr[] = array('name' => $row->gateway_name, 'code' => $row->gateway_number);
                            }
                        }
                        if (empty($paymentArr)) {
                            $paymentArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';

                        $returnArr['response'] = array('payment' => $paymentArr);
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This Function sends the request to riders about payment
     *
     * */
    public function requesting_payment() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');


            if ($driver_id != '') {
                $driverChek = $this->app_model->get_all_details(DRIVERS, array('_id' => new \MongoId($driver_id)), array());
                if ($driverChek->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $user_id = $checkRide->row()->user['id'];
                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                        if (isset($userVal->row()->push_type)) {
                            if ($userVal->row()->push_type != '') {

                                $tip_status = '0';
                                $tips_amount = '0.00';
                                if (isset($checkRide->row()->total['tips_amount'])) {
                                    if ($checkRide->row()->total['tips_amount'] > 0) {
                                        $tip_status = '0';
                                        $tips_amount = (string) $checkRide->row()->total['tips_amount'];
                                    }
                                }


                                /* Preparing driver information to share with user -- Start */
                                $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                                if (isset($driverChek->row()->image)) {
                                    if ($driverChek->row()->image != '') {
                                        $driver_image = USER_PROFILE_IMAGE . $driverChek->row()->image;
                                    }
                                }
                                $driver_review = 0;
                                if (isset($driverChek->row()->avg_review)) {
                                    $driver_review = $driverChek->row()->avg_review;
                                }
                                $driver_name = '';
                                if (isset($driverChek->row()->driver_name)) {
                                    $driver_name = $driverChek->row()->driver_name;
                                }
                                $driver_lat = '';
                                $driver_long = '';
                                if (isset($driverChek->row()->loc)) {
                                    $driver_lat = $driverChek->row()->loc['lat'];
                                    $driver_long = $driverChek->row()->loc['lon'];
                                }
                                $user_name = $userVal->row()->user_name;
                                $user_lat = '';
                                $user_long = '';
                                $userLocation = $this->app_model->get_all_details(USER_LOCATION, array('user_id' => new \MongoId($user_id)));
                                if ($userLocation->num_rows() > 0) {
                                    if (isset($userLocation->row()->geo)) {
                                        $latlong = $userLocation->row()->geo;
                                        $user_lat = $latlong[1];
                                        $user_long = $latlong[0];
                                    }
                                }
                                $subtotal = 0;
                                $coupon = 0;
                                $service_tax = 0;
                                $total = 0;
                                if (isset($checkRide->row()->total['total_fare'])) {
                                    if ($checkRide->row()->total['total_fare'] > 0) {
                                        $subtotal = $checkRide->row()->total['total_fare'];
                                    }
                                }
                                if (isset($checkRide->row()->total['coupon_discount'])) {
                                    if ($checkRide->row()->total['coupon_discount'] > 0) {
                                        $coupon = $checkRide->row()->total['coupon_discount'];
                                    }
                                }
                                if (isset($checkRide->row()->total['service_tax'])) {
                                    if ($checkRide->row()->total['service_tax'] > 0) {
                                        $service_tax = $checkRide->row()->total['service_tax'];
                                    }
                                }
                                if (isset($checkRide->row()->total['grand_fare'])) {
                                    if ($checkRide->row()->total['grand_fare'] > 0) {
                                        $total = $checkRide->row()->total['grand_fare'];
                                    }
                                }


                                $message = $this->format_string("your payment is pending", "your_payment_is_pending");
                                $currency = $checkRide->row()->currency;
                                $mins = $this->format_string('mins', 'mins');
								
								
								$distance_unit = $this->data['d_distance_unit'];
								if(isset($checkRide->row()->fare_breakup['distance_unit'])){
									$distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
								}
                                $options = array('currency' => (string) $currency,
                                    'ride_fare' => (string) $checkRide->row()->total['grand_fare'],
                                    'ride_distance' => (string) $checkRide->row()->summary['ride_distance'] . ' ' . $distance_unit,
                                    'ride_duration' => (string) $checkRide->row()->summary['ride_duration'] . ' ' . $mins,
                                    'waiting_duration' => (string) $checkRide->row()->summary['waiting_duration'] . ' ' . $mins,
                                    'ride_id' => (string) $ride_id,
                                    'user_id' => (string) $user_id,
                                    'tip_status' => (string) $tip_status,
                                    'tips_amount' => (string) $tips_amount,
                                    'driver_name' => (string) $driver_name,
                                    'driver_image' => (string) base_url() . $driver_image,
                                    'driver_review' => (string) $driver_review,
                                    'driver_lat' => (string) $driver_lat,
                                    'driver_long' => (string) $driver_long,
                                    'user_name' => (string) $user_name,
                                    'user_lat' => (string) $user_lat,
                                    'user_long' => (string) $user_long,
                                    'subtotal' => (string) $subtotal,
                                    'coupon' => (string) $coupon,
                                    'service_tax' => (string) $service_tax,
                                    'total' => (string) $total
                                );

                                if ($userVal->row()->push_type == 'ANDROID') {
                                    if (isset($userVal->row()->push_notification_key['gcm_id'])) {
                                        if ($userVal->row()->push_notification_key['gcm_id'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'requesting_payment', 'ANDROID', $options, 'USER');
                                        }
                                    }
                                }
                                if ($userVal->row()->push_type == 'IOS') {
                                    if (isset($userVal->row()->push_notification_key['ios_token'])) {
                                        if ($userVal->row()->push_notification_key['ios_token'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'requesting_payment', 'IOS', $options, 'USER');
                                        }
                                    }
                                }
                            }
                        }

                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('request sent', 'request_sent');
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This Function accepting the cash and update the ride payment status
     *
     * */
    public function payment_received() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');
            $amount = $this->input->post('amount');


            if ($driver_id != '' && $ride_id != '' && $amount != '') {
                $driverChek = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array());
                if ($driverChek->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $paid_amount = 0.00;
                        $tips_amount = 0.00;
						
						
						if (isset($checkRide->row()->total['tips_amount'])) {
							$tips_amount = $checkRide->row()->total['tips_amount'];
						}
						
                        if (isset($checkRide->row()->total)) {
                            if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
                                $paid_amount = ($checkRide->row()->total['grand_fare']+ $tips_amount) - $checkRide->row()->total['wallet_usage'];
								$paid_amount = round($paid_amount,2);
                            }
                        }
                        $pay_summary = 'Cash';
                        if (isset($checkRide->row()->pay_summary)) {
                            if ($checkRide->row()->pay_summary != '') {
                                if ($checkRide->row()->pay_summary != 'Cash') {
                                    $pay_summary = $checkRide->row()->pay_summary['type'] . '_Cash';
                                }
                            } else {
                                $pay_summary = 'Cash';
                            }
                        }
                        $pay_summary = array('type' => $pay_summary);
                        $paymentInfo = array('ride_status' => 'Completed',
                            'pay_status' => 'Paid',
                            'history.pay_by_cash_time' => new \MongoDate(time()),
                            'total.paid_amount' => round(floatval($paid_amount), 2),
                            'pay_summary' => $pay_summary
                        );
                        $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
                        /* Update Stats Starts */
                        $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                        $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                        $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                        /* Update Stats End */
                        $avail_data = array('mode' => 'Available', 'availability' => 'Yes');
                        $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => new \MongoId($driver_id)));
                        $trans_id = time() . rand(0, 2578);
                        $transactionArr = array('type' => 'cash',
                            'amount' => floatval($paid_amount),
                            'trans_id' => $trans_id,
                            'trans_date' => new \MongoDate(time())
                        );
                        $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));

                        $user_id = $checkRide->row()->user['id'];
                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                        if (isset($userVal->row()->push_type)) {
                            if ($userVal->row()->push_type != '') {
                                $message = $this->format_string("your billing amount paid successfully", "your_billing_amount_paid");
                                $options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id);
                                if ($userVal->row()->push_type == 'ANDROID') {
                                    if (isset($userVal->row()->push_notification_key['gcm_id'])) {
                                        if ($userVal->row()->push_notification_key['gcm_id'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'payment_paid', 'ANDROID', $options, 'USER');
                                        }
                                    }
                                }
                                if ($userVal->row()->push_type == 'IOS') {
                                    if (isset($userVal->row()->push_notification_key['ios_token'])) {
                                        if ($userVal->row()->push_notification_key['ios_token'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'payment_paid', 'IOS', $options, 'USER');
                                        }
                                    }
                                }
                            }
                        }
						$this->app_model->update_ride_amounts($ride_id);
						$fields = array(
							'ride_id' => (string) $ride_id
						);
						$url = base_url().'prepare-invoice';
						$this->load->library('curl');
						$output = $this->curl->simple_post($url, $fields);

                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('amount received', 'amount_received');
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This sends the ride otp for receiving bill amount confirmation
     *
     * */
    public function receive_payment_confirmation() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');

            if ($driver_id != '' && $ride_id != '') {
                $driverChek = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array());
                if ($driverChek->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $otp_string = '';
                        if (isset($checkRide->row()->ride_otp)) {
                            $otp_string = $checkRide->row()->ride_otp;
                        }
                        $otp_status = "development";
                        if ($this->config->item('twilio_account_type') == 'prod') {
                            $otp_status = "production";
                            $this->sms_model->opt_for_registration($country_code, $phone_number, $otp_string);
                        }
                        $paid_amount = 0.00;
                        if (isset($checkRide->row()->total)) {
                            if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
                                $paid_amount = round(($checkRide->row()->total['grand_fare'] - $checkRide->row()->total['wallet_usage']), 2);
                            }
                        }
                        $currency = $checkRide->row()->currency;
                        $returnArr['currency'] = (string) $currency;
                        $returnArr['otp_status'] = (string) $otp_status;
                        $returnArr['otp'] = (string) $otp_string;
                        $returnArr['ride_id'] = (string) $ride_id;
                        $returnArr['amount'] = (string) $paid_amount;
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('waiting for otp', 'waiting_for_otp');
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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

    /**
     *
     * This function returns the banking detail of the driver
     *
     * */
    public function get_banking_details() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');

            if ($driver_id != '') {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'banking'));
                if ($checkDriver->num_rows() == 1) {
                    $bankingArr = array("acc_holder_name" => (string) '',
                        "acc_holder_address" => (string) '',
                        "acc_number" => (string) '',
                        "bank_name" => (string) '',
                        "branch_name" => (string) '',
                        "branch_address" => (string) '',
                        "swift_code" => (string) '',
                        "routing_number" => (string) ''
                    );
                    if (isset($checkDriver->row()->banking)) {
                        if (is_array($checkDriver->row()->banking)) {
                            if (!empty($checkDriver->row()->banking)) {
                                $bankingArr = $checkDriver->row()->banking;
                            }
                        }
                    }
                    $returnArr['status'] = '1';
                    if (empty($bankingArr)) {
                        $bankingArr = json_decode("{}");
                    }
                    $returnArr['response'] = array('banking' => $bankingArr);
                } else {
                    $returnArr['response'] = $this->format_string('"Invalid Driver", "invalid_driver"');
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

    /**
     *
     * This function save and return the banking detail of the driver
     *
     * */
    public function save_banking_details() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($driver_id != '' && $chkValues >= 8) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'banking'));
                if ($checkDriver->num_rows() == 1) {

                    $banking = array("acc_holder_name" => trim($this->input->post('acc_holder_name')),
                        "acc_holder_address" => trim($this->input->post('acc_holder_address')),
                        "acc_number" => trim($this->input->post('acc_number')),
                        "bank_name" => trim($this->input->post('bank_name')),
                        "branch_name" => trim($this->input->post('branch_name')),
                        "branch_address" => trim($this->input->post('branch_address')),
                        "swift_code" => trim($this->input->post('swift_code')),
                        "routing_number" => trim($this->input->post('routing_number'))
                    );
                    $dataArr = array('banking' => $banking);
                    $this->driver_model->update_details(DRIVERS, $dataArr, array('_id' => new \MongoId($driver_id)));

                    $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'banking'));
                    $bankingArr = array();
                    if (isset($checkDriver->row()->banking)) {
                        if (is_array($checkDriver->row()->banking)) {
                            $bankingArr = $checkDriver->row()->banking;
                        }
                    }
                    $returnArr['status'] = '1';
                    if (empty($bankingArr)) {
                        $bankingArr = json_decode("{}");
                    }
                    $returnArr['response'] = array('banking' => $bankingArr);
                } else {
                    $returnArr['response'] = $this->format_string('"Invalid Driver", "invalid_driver"');
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

    /**
     *
     * This Function returns the driver payment list
     *
     * */
    public function driver_all_payment_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');

            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                    $total_payments = 5;
                    $paymentArr = array();
                    $billingDetails = $this->app_model->get_all_details(BILLINGS, array('driver_id' => $driver_id), array('bill_date' => 'DESC'));
                    if ($billingDetails->num_rows() > 0) {
                        foreach ($billingDetails->result() as $bill) {
                            $paymentArr[] = array('pay_id' => (string) $bill->invoice_id,
                                'pay_duration_from' => (string) date("d-m-Y", $bill->bill_from->sec),
                                'pay_duration_to' => (string) date("d-m-Y", $bill->bill_to->sec),
                                'amount' => (string) $bill->driver_earnings,
                                'pay_date' => (string) date("d-m-Y", $bill->bill_date->sec)
                            );
                        }
                    }


                    /* $pay_id=123546;

                      for($i=1;$i<=$total_payments;$i++){
                      $pay_duration_from=date("d-m-Y",strtotime('-'.(($i*7)+7).' day',time()));
                      $pay_duration_to=date("d-m-Y",strtotime('-'.($i*7).' day',time()));
                      $amount='1250';
                      $pay_date=date("d-m-Y");
                      $paymentArr[] = array('pay_id'=>(string)$pay_id,
                      'pay_duration_from'=>(string)$pay_duration_from,
                      'pay_duration_to'=>(string)$pay_duration_to,
                      'amount'=>(string)$amount,
                      'pay_date'=>(string)$pay_date
                      );
                      $pay_id++;
                      } */

                    if (empty($paymentArr)) {
                        $paymentArr = json_decode("{}");
                    }

                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('total_payments' => (string) $total_payments, 'payments' => $paymentArr, 'currency' => (string) $this->data['dcurrencyCode']);
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some of the parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This Function returns the driver payment summary
     *
     * */
    public function view_driver_payment_information() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $invoice_id = (string) $this->input->post('pay_id');

            if ($driver_id != '' && $invoice_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                    $total_payments = 0;
                    $paymentArr = array();
                    $listsArr = array();

                    $billingDetails = $this->app_model->get_all_details(BILLINGS, array('invoice_id' => floatval($invoice_id)));
                    if ($billingDetails->num_rows() > 0) {
                        $paymentArr[] = array('pay_id' => (string) $billingDetails->row()->invoice_id,
                            'pay_duration_from' => (string) date("d-m-Y", $billingDetails->row()->bill_from->sec),
                            'pay_duration_to' => (string) date("d-m-Y", $billingDetails->row()->bill_to->sec),
                            'amount' => (string) $billingDetails->row()->driver_earnings,
                            'pay_date' => (string) date("d-m-Y", $billingDetails->row()->bill_date->sec)
                        );

                        $ridesVal = $this->app_model->get_billing_rides($billingDetails->row()->bill_from->sec, $billingDetails->row()->bill_to->sec, $billingDetails->row()->driver_id);
                        if ($ridesVal->num_rows() > 0) {
                            $total_payments = $ridesVal->num_rows();
                            foreach ($ridesVal->result() as $rides) {
                                $listsArr[] = array('ride_id' => (string) $rides->ride_id,
                                    'amount' => (string) $rides->driver_revenue,
                                    'ride_date' => (string) date("d-m-Y", $rides->booking_information['pickup_date']->sec)
                                );
                            }
                        }
                    }

                    /* $pay_duration_from=date("d-m-Y",strtotime('-7 day',time()));
                      $pay_duration_to=date("d-m-Y",strtotime('-1 day',time()));
                      $amount='1250';
                      $pay_date=date("d-m-Y");

                      $paymentArr[] = array('pay_id'=>(string)$pay_id,
                      'pay_duration_from'=>(string)$pay_duration_from,
                      'pay_duration_to'=>(string)$pay_duration_to,
                      'amount'=>(string)$amount,
                      'pay_date'=>(string)$pay_date
                      ); */

                    /* $amount='250';
                      for($i=1;$i<=$total_payments;$i++){
                      $ride_id=time().rand(0,259);
                      $ride_date=date("d-m-Y",strtotime('-'.$i.' day',time()));;
                      $listsArr[] = array('ride_id'=>(string)$ride_id,
                      'amount'=>(string)$amount,
                      'ride_date'=>(string)$ride_date
                      );
                      } */
                    if (empty($paymentArr)) {
                        $paymentArr = json_decode("{}");
                    }
                    if (empty($listsArr)) {
                        $listsArr = json_decode("{}");
                    }

                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('total_payments' => (string) $total_payments, 'payments' => $paymentArr, 'listsArr' => $listsArr, 'currency' => (string) $this->data['dcurrencyCode']);
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some of the parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This Function returns the rider profile
     *
     * */
    public function continue_trip() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');

            if ($driver_id != '' && $ride_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('city', 'avail_category'));
                if ($driverVal->num_rows() > 0) {
                    $checkRide = $this->driver_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'location.id', 'coupon_used', 'coupon', 'est_pickup_date'));
                    if ($checkRide->num_rows() == 1) {
                        $iscontinue = 'NO';
                        if ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived') {
                            if ($checkRide->row()->ride_status == 'Confirmed') {
                                $iscontinue = 'arrived';
                            }
                            if ($checkRide->row()->ride_status == 'Arrived') {
                                $iscontinue = 'begin';
                            }
                        }

                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                        $user_profile = array();
                        if ($userVal->num_rows() > 0) {
                            if ($userVal->row()->image == '') {
                                $user_image = USER_PROFILE_IMAGE_DEFAULT;
                            } else {
                                $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                            }
                            $user_review = 0;
                            if (isset($userVal->row()->avg_review)) {
                                $user_review = $userVal->row()->avg_review;
                            }
							$drop_location = 0;
							$drop_loc = '';$drop_lat = '';$drop_lon = '';
							if($checkRide->row()->booking_information['drop']['location']!=''){
								$drop_location = 1;
								$drop_loc = $checkRide->row()->booking_information['drop']['location'];
								$drop_lat = $checkRide->row()->booking_information['drop']['latlong']['lat'];
								$drop_lon = $checkRide->row()->booking_information['drop']['latlong']['lon'];
							}
							
							
                            $user_profile = array('user_name' => $userVal->row()->user_name,
                                'user_id' => (string)$userVal->row()->_id,
                                'user_email' => $userVal->row()->email,
                                'phone_number' => (string) $userVal->row()->country_code . $userVal->row()->phone_number,
                                'user_image' => base_url() . $user_image,
                                'user_review' => floatval($user_review),
                                'ride_id' => $ride_id,
                                'pickup_location' => $checkRide->row()->booking_information['pickup']['location'],
                                'pickup_lat' => $checkRide->row()->booking_information['pickup']['latlong']['lat'],
                                'pickup_lon' => $checkRide->row()->booking_information['pickup']['latlong']['lon'],
                                'pickup_time' => date("h:i A jS M, Y", $checkRide->row()->booking_information['est_pickup_date']->sec),
                                'continue_trip' => $iscontinue,
                                'drop_location' => (string)$drop_location,
                                'drop_loc' => (string)$drop_loc,
                                'drop_lat' => floatval($drop_lat),
                                'drop_lon' => floatval($drop_lon),
                            );
                        }

                        if (empty($user_profile)) {
                            $user_profile = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('user_profile' => $user_profile, 'message' => $this->format_string("Ride Accepted", "ride_accepted", "ride_accepted"));
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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
     * This Function complete the free trip 
     *
     * */
    public function trip_completed() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $ride_id = (string) $this->input->post('ride_id');


            if ($driver_id != '' && $ride_id != '') {
                $driverChek = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array());
                if ($driverChek->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
                    if ($checkRide->num_rows() == 1) {
                        $paid_amount = 0.00;
                        $pay_summary = array('type' => 'FREE');
                        $paymentInfo = array('ride_status' => 'Completed',
                            'pay_status' => 'Paid',
                            'history.pay_by_coupon_time' => new \MongoDate(time()),
                            'total.paid_amount' => round(floatval($paid_amount), 2),
                            'pay_summary' => $pay_summary
                        );
                        $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));

                        /* Update Stats Starts */
                        $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                        $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                        $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                        /* Update Stats End */

                        $avail_data = array('mode' => 'Available', 'availability' => 'Yes');
                        $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => new \MongoId($driver_id)));
                        $trans_id = time() . rand(0, 2578);
                        $transactionArr = array('type' => 'coupon',
                            'amount' => floatval($paid_amount),
                            'trans_id' => $trans_id,
                            'trans_date' => new \MongoDate(time())
                        );
                        $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));

                        $user_id = $checkRide->row()->user['id'];
                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_type', 'push_notification_key'));
                        if (isset($userVal->row()->push_type)) {
                            if ($userVal->row()->push_type != '') {
                                $message = $this->format_string("your billing amount paid successfully", "your_billing_amount_paid");
                                $options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id);
                                if ($userVal->row()->push_type == 'ANDROID') {
                                    if (isset($userVal->row()->push_notification_key['gcm_id'])) {
                                        if ($userVal->row()->push_notification_key['gcm_id'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['gcm_id']), $message, 'payment_paid', 'ANDROID', $options, 'USER');
                                        }
                                    }
                                }
                                if ($userVal->row()->push_type == 'IOS') {
                                    if (isset($userVal->row()->push_notification_key['ios_token'])) {
                                        if ($userVal->row()->push_notification_key['ios_token'] != '') {
                                            $this->sendPushNotification(array($userVal->row()->push_notification_key['ios_token']), $message, 'payment_paid', 'IOS', $options, 'USER');
                                        }
                                    }
                                }
                            }
                        }
						$this->app_model->update_ride_amounts($ride_id);
						$fields = array(
							'ride_id' => (string) $ride_id
						);
						$url = base_url().'prepare-invoice';
						$this->load->library('curl');
						$output = $this->curl->simple_post($url, $fields);

                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('ride completed', 'ride_completed');
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
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
	
	/**
	*
	* Deduct the automatic payment for a trip while end the trip
	*
	**/
    public function auto_payment_deduct($ride_id=''){
		$rideinfoUpdated=$this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
		$bayMethod ='';
		if($rideinfoUpdated->num_rows() ==1){
			$user_id=$rideinfoUpdated->row()->user['id'];
			$wallet_amount=$this->app_model->get_all_details(WALLET,array('user_id'=>new \MongoId($user_id)));
			$total_grand_fare = $rideinfoUpdated->row()->total['grand_fare'];
			if($wallet_amount->num_rows() >0){
				if($total_grand_fare <= $wallet_amount->row()->total){
					$bayMethod = 'wallet';
				}else{
					$bayMethod = 'stripe';
				}
			} else {
				$bayMethod = 'stripe';
			}
			$is_completed= 'No';
			if($bayMethod == 'wallet'){
				$bal_walletamount=($wallet_amount->row()->total-$total_grand_fare);
				$walletamount=array('total'=>floatval($bal_walletamount));
				$this->app_model->update_details(WALLET,$walletamount,array('user_id'=>new \MongoId($user_id)));
				$txn_time = time() . rand(0, 2578);
				$initialAmt = array('type' => 'DEBIT',
								   'debit_type' => 'payment',
								   'ref_id' => $ride_id,
								   'trans_amount' => floatval($total_grand_fare),
								   'avail_amount' => floatval($bal_walletamount),
								   'trans_date' => new \MongoDate(time()),
								   'trans_id' => $txn_time
								);
				$this->app_model->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $initialAmt));
				$is_completed= 'Yes';
			}else if($bayMethod == 'stripe'){
				$stripe_settings = $this->data['stripe_settings'];
				if($stripe_settings['status'] == 'Enable'){
					$getUsrCond = array('_id' => new \MongoId($user_id));
					$get_user_info = $this->app_model->get_selected_fields(USERS, $getUsrCond, array('email', 'stripe_customer_id')); 
					$email = $get_user_info->row()->email;
					$stripe_customer_id = '';
					$auto_pay_status = 'No';
					if (isset($get_user_info->row()->stripe_customer_id)) {
						$stripe_customer_id = $get_user_info->row()->stripe_customer_id;
						if ($stripe_customer_id != '') {
							$auto_pay_status = 'Yes';
						}
					}
					
					if($auto_pay_status == 'Yes'){
						require_once('./stripe/lib/Stripe.php');

						$stripe_settings = $this->data['stripe_settings'];
						$secret_key = $stripe_settings['settings']['secret_key'];
						$publishable_key = $stripe_settings['settings']['publishable_key'];

						$stripe = array(
							"secret_key" => $secret_key,
							"publishable_key" => $publishable_key
						);
						$description = ucfirst($this->config->item('email_title')) . ' - trip payment';
						
						
						$currency = $this->data['dcurrencyCode'];
						if(isset($rideinfoUpdated->row()->currency)) $currency = $rideinfoUpdated->row()->currency;
						$amounts = $this->get_stripe_currency_smallest_unit($total_grand_fare,$currency);
						
						
						Stripe::setApiKey($secret_key);
						
						
						try {
							if ($stripe_customer_id!='') {
								// Charge the Customer instead of the card
								$charge = Stripe_Charge::create(array(
											"amount" => $amounts, # amount in cents, again
											"currency" => $currency,
											"customer" => $stripe_customer_id,
											"description" => $description)
								);

								$paymentData=array('user_id' => $user_id, 
													'ride_id' => $ride_id, 
													'payType' => 'stripe', 
													'stripeTxnId' => $charge['id']
												);
								$is_completed= 'Yes';
								$strip_txnid=$charge['id'];
							}
						} catch (Exception $e) {
							$error = $e->getMessage();
						}
					}
					
				}
			}
			
			if($is_completed == 'Yes'){
				###	Update into the ride and driver collection ###
				if ($rideinfoUpdated->row()->pay_status == 'Pending' || $rideinfoUpdated->row()->pay_status == 'Processing') {
					if (isset($rideinfoUpdated->row()->total)) {
                        if (isset($rideinfoUpdated->row()->total['grand_fare'])) {
                            $paid_amount = round($rideinfoUpdated->row()->total['grand_fare'], 2);
                        }
                    }
                    if($bayMethod=='stripe'){
						$pay_summary = 'Gateway';
						$trans_id=$strip_txnid;
						$type='Card';
                    } else if($bayMethod == 'wallet'){
						$pay_summary = 'Wallet';
						$trans_id  =$txn_time; 
						$type='wallet';
                    }
                    $pay_summary = array('type' => $pay_summary);
                    $paymentInfo = array('ride_status' => 'Completed',
                        'pay_status' => 'Paid',
                        'total.paid_amount' => round(floatval($paid_amount), 2),
                        'pay_summary' => $pay_summary
                    );
					if($bayMethod=='stripe'){
						$paymentInfo['history.pay_by_gateway_time'] = new \MongoDate(time());
                    } else if($bayMethod == 'wallet'){
						$paymentInfo['history.wallet_usage_time'] = new \MongoDate(time());
                    }
                    $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
                    /* Update Stats Starts */
                    $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                    $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                    /* Update Stats End */
                    $avail_data = array('mode' => 'Available', 'availability' => 'Yes');
                    $driver_id = $rideinfoUpdated->row()->driver['id'];
                    $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => new \MongoId($driver_id)));
                    $transactionArr = array('type' => $type,
                        'amount' => floatval($paid_amount),
                        'trans_id' => $trans_id,
                        'trans_date' => new \MongoDate(time())
                    );
                    $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
				}
     
			}
        
		}   
	}

}

/* End of file drivers.php */
/* Location: ./application/controllers/mobile/drivers.php */