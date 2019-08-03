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
        $this->load->model(array('driver_model'));
        $this->load->model(array('app_model'));

		/* Authentication Begin */
        $headers = $this->input->request_headers();
		header('Content-type:application/json;charset=utf-8');
		if (array_key_exists("Authkey", $headers)) $auth_key = $headers['Authkey']; else $auth_key = "";
		if(stripos($auth_key,APP_NAME) === false) {
			$cf_fun= $this->router->fetch_method();
			$apply_function = array('driver_dashboard','forgot_password');
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
                $deadChk = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($this->Driverid)), array('push_notification','status'));
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
						if(strtolower($deadChk->row()->status)!="active"){
							$is_out_message = $this->format_string('Your account has been modified, please login to again.', 'is_out_message');
							echo json_encode(array("is_out" => "Yes","message" => $is_out_message));die;
						}
						if($storedToken!=''){
							if ($storedToken != $this->Token) {
								echo json_encode(array("is_dead" => "Yes"));
								die;
							}
						}
					}
                }else{
					$is_out_message = $this->format_string('Your account has been modified, please login to again.', 'is_out_message');
					echo json_encode(array("is_out" => "Yes","message" => $is_out_message));die;
				}
            }
        } catch (MongoException $ex) {
            
        }
        /* Authentication End */
    }

    /**
     *
     * This Function returns the driver dashboard
     *
     * */
    public function driver_dashboard() { 
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            if ($driver_id != '') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'driver_commission', 'loc', 'category','availability','mode','currency', 'outstanding_amount'));
                if ($checkDriver->num_rows() == 1) {
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
                    $availability = 'No';
                    if (isset($checkDriver->row()->availability)) {
                        $availability = $checkDriver->row()->availability;
                    }														
					$availability_string = 'Yes';
					$ride_status_string = 'No';
					if ($checkDriver->row()->mode == 'Available') {
                        $availability_string = 'Yes';
                    } else if ($checkDriver->row()->mode == 'Booked') {
                        $checkPending = $this->app_model->get_uncompleted_trips($driver_id, array('ride_id', 'ride_status', 'pay_status'));
                        if ($checkPending->num_rows() > 0) {
							if ($checkPending->row()->ride_status == 'Onride') {
								$ride_status_string = 'Yes';
							}
                            $availability_string = 'No';
                        } else {
                            $availability_string = 'Yes';
                        }
                    }
                    $driver_lat = $checkDriver->row()->loc['lat'];
                    $driver_lon = $checkDriver->row()->loc['lon'];
                    $vehicleInfo = $this->driver_model->get_selected_fields(MODELS, array('_id' => new \MongoId($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
                    $vehicle_model = '';
                    $brand_name = '';
                    if ($vehicleInfo->num_rows() > 0) {
                        $vehicle_model = $vehicleInfo->row()->name;
                        $brand_name = $vehicleInfo->row()->brand_name;
                    }
                    $categoryInfo = $this->driver_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($checkDriver->row()->category)), array('_id', 'name', 'brand_name', 'icon_car_image'));
                    $driver_category = '';
					$category_icon = base_url().ICON_MAP_CAR_IMAGE;
                    if ($categoryInfo->num_rows() > 0) {
                        $driver_category = $categoryInfo->row()->name;
						if(isset($categoryInfo->row()->icon_car_image)){
							$category_icon = base_url() . ICON_IMAGE . $categoryInfo->row()->icon_car_image;
						}
                        
                    }

                    $last_trip = array();
                    $checkTrip = $this->app_model->get_all_details(RIDES, array('driver.id' => $driver_id, 'ride_status' => "Completed", "pay_status" => "Paid"), array("ride_id" => "DESC"));
					$currencyCode=$this->data['dcurrencyCode'];
					if(isset($checkDriver->row()->currency)) {
						$currencyCode=$checkDriver->row()->currency;
					}
                    if ($checkTrip->num_rows() > 0) {

                        $lat = $checkTrip->row()->booking_information['pickup']['latlong']['lat'];
                        $lon = $checkTrip->row()->booking_information['pickup']['latlong']['lon'];
                        $this->setTimezone($lat, $lon);
                        
                        $last_trip = array("ride_time" => date("h:i A", $checkTrip->row()->booking_information['drop_date']->sec),
                            "ride_date" => date("jS M, Y", $checkTrip->row()->booking_information['drop_date']->sec),
                            "earnings" => (string) number_format($checkTrip->row()->driver_revenue, 2),
                            "currency" => (string) $currencyCode
                        );
                    }

                    $today_earnings = array();
                    $checkRide = $this->app_model->get_today_rides($driver_id);
                    if (!empty($checkRide['result'])) {
                        $online_hours = $checkRide['result'][0]['freeTime'] + $checkRide['result'][0]['tripTime'] + $checkRide['result'][0]['waitTime'];
                        $online_hours_txt = '0 hours';
                        if ($online_hours > 0) {
                            if ($online_hours >= 60) {
								$online_hours_in_hrs = ($online_hours / 60);
                                $online_hours_txt = round($online_hours_in_hrs,2) . ' hours';
                            } else {
                                $online_hours_txt = $online_hours . ' minutes';
                            }
                        }
                        $mins = $this->format_string('min', 'min_short');
			            $mins_short = $this->format_string('mins', 'mins_short');
                        if($checkRide['result'][0]['ridetime'] >1){
                                $min_unit = $mins_short;
                        }else{
                                $min_unit = $mins;
                        }
                        $trip = $this->format_string('trip', 'trip_singular');
			            $trips = $this->format_string('trips', 'trip_plural');
                        if($checkRide['result'][0]['totalTrips'] >1) {
                           $trip_unit = $trips;
                        } else {
                           $trip_unit = $trip;
                        }
                       
                        $today_earnings = array("online_hours" => (string) $checkRide['result'][0]['ridetime'].' '.$min_unit,
                            "trips" => (string) $checkRide['result'][0]['totalTrips'],
                            "earnings" => (string) number_format($checkRide['result'][0]['driverAmount'], 2),
                            "currency" => (string) $currencyCode,
                            "trip_unit" => (string) $trip_unit
                        );
                    }
                    $today_tips = array();
                    $todayTips = $this->app_model->get_today_tips($driver_id);
                    if (!empty($todayTips['result'])) {
                        $today_tips = array("trips" => (string) $todayTips['result'][0]['totalTrips'],
                            "tips" => (string) number_format($todayTips['result'][0]['tipsAmount'], 2),
                            "currency" => (string) $currencyCode
                        );
                    }
					
					if(empty($last_trip)){
						$last_trip = json_decode("{}");
					}
					if(empty($today_earnings)){
						$today_earnings = json_decode("{}");
					}
					if(empty($today_tips)){
						$today_tips = json_decode("{}");
					}
					
					
                    $driver_dashboard = array("currency" => (string) $currencyCode,
						'driver_id' => (string) $checkDriver->row()->_id,
						'driver_status' => (string) $checkDriver->row()->availability,
                        'driver_name' => (string) $checkDriver->row()->driver_name,
                        'driver_email' => (string) $checkDriver->row()->email,
                        'driver_image' => (string) base_url() . $driver_image,
                        'driver_review' => (string) floatval($driver_review),
                        'driver_lat' => (string) floatval($driver_lat),
                        'driver_lon' => (string) floatval($driver_lon),
                        'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
                        'driver_category' => (string) $driver_category,
                        'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
                        'vehicle_model' => (string) $vehicle_model,
                        'brand_name' => (string) $brand_name,
                        'last_trip' => $last_trip,
                        'today_earnings' => $today_earnings,
                        'today_tips' => $today_tips,
                        'availability' => (string)$availability,
						'availability_string' => (string)$availability_string,
						'ride_status_string' => (string)$ride_status_string,
                        'category_icon' => (string)$category_icon,
                        'outstanding_amount' => (string) ($checkDriver->row()->outstanding_amount ? $checkDriver->row()->outstanding_amount : 0.00)
                    );

                    $responseArr['status'] = '1';
                    $responseArr['response'] = $driver_dashboard;
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
    * This function changes the driver password
    *
    * */
    public function change_password() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $password = $this->input->post('password');
            $new_password = (string) trim($this->input->post('new_password'));

            if ($driver_id != '' && $password != '' && $new_password != '') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('password'));
                if ($checkDriver->num_rows() == 1) {
                    if (strlen($new_password) >= 6) {
                        if ($checkDriver->row()->password == md5($password)) {
                            $condition = array('_id' => new \MongoId($driver_id));
                            $dataArr = array('password' => md5($new_password));
                            $this->app_model->update_details(DRIVERS, $dataArr, $condition);
                            $responseArr['status'] = '1';
                            $responseArr['response'] = $this->format_string('Password changed successfully.','password_changed');
                        } else {
                            $responseArr['response'] = $this->format_string('Your current password is not matching.','password_not_matching');
                        }
                    } else {
                        $responseArr['response'] = $this->format_string('Password should be atleast 6 characters.','password_should_be_6_characters');
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Authentication Failed','authentication_failed');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing.","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
    *
    * This function forgot driver password request
    *
    * */
    public function forgot_password() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $email = $this->input->post('email');
            if ($email != '') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('email' => $email), array('password', 'driver_name', 'email'));
                if ($checkDriver->num_rows() == 1) {
                    $new_pwd = $this->get_rand_str('6') . time();
                    $newdata = array('reset_id' => $new_pwd);
                    $condition = array('email' => $email);
                    $this->app_model->update_details(DRIVERS, $newdata, $condition);
                    $this->send_driver_pwd($new_pwd, $checkDriver);
                    $responseArr['status'] = '1';
                    $responseArr['response'] = $this->format_string('Password reset link has been sent to your email address.','password_reset_link_sent');
                } else {
                    $responseArr['response'] = $this->format_string('Authentication Failed.','authentication_failed');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing.","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
    *
    * This function send the new password to driver email
    *
    * */
    public function send_driver_pwd($pwd = '', $query) {
        $newsid = '10';
        $reset_url = base_url() . 'driver/reset-password-form/' . $pwd;
        $user_name = $query->row()->driver_name;
		$template_values = $this->app_model->get_email_template($newsid,$this->data['langCode']);
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['subject'];
        $drivernewstemplateArr = array('email_title' => $this->config->item('email_title'), 'mail_emailTitle' => $this->config->item('email_title'), 'mail_logo' => $this->config->item('logo_image'), 'mail_footerContent' => $this->config->item('footer_content'), 'mail_metaTitle' => $this->config->item('meta_title'), 'mail_contactMail' => $this->config->item('site_contact_mail'));
        extract($drivernewstemplateArr);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $subject . '</title>
			<body>';
        include($template_values['templateurl']);
        $message .= '</body>
			</html>';
		$sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $query->row()->email,
            'subject_message' => 'Password Reset',
            'body_messages' => $message
        );
        $email_send_to_common = $this->app_model->common_email_send($email_values);
    }

}

/* End of file drivers.php */
/* Location: ./application/controllers/api_v2/drivers.php */