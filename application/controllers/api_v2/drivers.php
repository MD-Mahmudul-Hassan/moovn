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
     * This Function returns the driver dashboard
     *
     * */
    public function driver_dashboard() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            if ($driver_id != '') {
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'driver_commission', 'loc', 'category','availability'));
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
                    $driver_lat = $checkDriver->row()->loc['lat'];
                    $driver_lon = $checkDriver->row()->loc['lon'];
                    $vehicleInfo = $this->driver_model->get_selected_fields(MODELS, array('_id' => new \MongoId($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
                    $vehicle_model = '';
                    if ($vehicleInfo->num_rows() > 0) {
                        $vehicle_model = $vehicleInfo->row()->name;
                    }
                    $categoryInfo = $this->driver_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($checkDriver->row()->category)), array('_id', 'name', 'brand_name'));
                    $driver_category = '';
                    if ($categoryInfo->num_rows() > 0) {
                        $driver_category = $categoryInfo->row()->name;
                    }

                    $last_trip = array();
                    $checkTrip = $this->app_model->get_all_details(RIDES, array('driver.id' => $driver_id, 'ride_status' => "Completed", "pay_status" => "Paid"), array("ride_id" => "DESC"));
                    if ($checkTrip->num_rows() > 0) {
                        $last_trip = array("ride_time" => date("h:i A", $checkTrip->row()->booking_information['drop_date']->sec),
                            "ride_date" => date("jS M, Y", $checkTrip->row()->booking_information['drop_date']->sec),
                            "earnings" => (string) number_format($checkTrip->row()->driver_revenue, 2),
                            "currency" => (string) $this->data['dcurrencyCode']
                        );
                    }

                    $today_earnings = array();
                    $checkRide = $this->app_model->get_today_rides($driver_id);
                    if (!empty($checkRide['result'])) {
                        $online_hours = $checkRide['result'][0]['freeTime'] + $checkRide['result'][0]['tripTime'] + $checkRide['result'][0]['waitTime'];
                        $online_hours_txt = '0 hours';
                        if ($online_hours > 0) {
                            if ($online_hours >= 60) {
                                $online_hours_txt = ($online_hours / 60) . ' hours';
                            } else {
                                $online_hours_txt = $online_hours . ' minutes';
                            }
                        }
                        $today_earnings = array("online_hours" => (string) $online_hours_txt,
                            "trips" => (string) $checkRide['result'][0]['totalTrips'],
                            "earnings" => (string) number_format($checkRide['result'][0]['driverAmount'], 2),
                            "currency" => (string) $this->data['dcurrencyCode']
                        );
                    }
                    $today_tips = array();
                    $todayTips = $this->app_model->get_today_tips($driver_id);
                    if (!empty($todayTips['result'])) {
                        $today_tips = array("trips" => (string) $todayTips['result'][0]['totalTrips'],
                            "tips" => (string) number_format($todayTips['result'][0]['tipsAmount'], 2),
                            "currency" => (string) $this->data['dcurrencyCode']
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


                    $driver_dashboard = array("currency" => (string) $this->data['dcurrencyCode'],
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
                        'last_trip' => $last_trip,
                        'today_earnings' => $today_earnings,
                        'today_tips' => $today_tips
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
		$sender_name = $template_values['sender_name'];
        $sender_email = $template_values['sender_email'];
        if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {
            $sender_email = $this->config->item('site_contact_mail');
            $sender_name = $this->config->item('email_title');
        }
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