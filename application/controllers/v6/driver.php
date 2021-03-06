<?php

if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

/**
*
* This controller contains the functions related to Drivers at the app end
* @author Casperon
*
* */
class Driver extends MY_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('cookie', 'date', 'form', 'email', 'string'));
        $this->load->library(array('encrypt', 'form_validation'));

        $this->load->model('driver_model');
        $this->load->model('app_model');
        $this->load->model('mileage_model');

        /* Authentication Begin */
        $headers = $this->input->request_headers();
        header('Content-type:application/json;charset=utf-8');
        $current_function = $this->router->fetch_method();
        $public_functions = array('login', 'forgot_password', 'view');
        if (array_key_exists("Driver-Token", $headers)) {
            $this->driver_token = $headers['Driver-Token'];
            try {
                if (isset($this->driver_token)) {
                    $driver = $this->app_model->get_selected_fields(DRIVERS, array('sso_token' => $this->driver_token));
                    if ($driver->num_rows() <= 0) {
                        echo json_encode(array("is_dead" => "Yes"));
                        die;
                    } else {
                        $this->driver = $driver->row();
                    }
                }
            } catch (MongoException $ex) {
                echo $ex->getMessage();
                die;
            }
        } else if (!in_array($current_function, $public_functions)) {
            show_404();
        }
        /* Authentication End */
    }

    /**
     *
     * Login Driver 
     *
     * */
    public function login()
    {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $fcm_token = $this->input->post('fcm_token');
            $device_type = $this->input->post('device_type');

            if ($email == '' || $password == '' || $fcm_token == '') {
                $returnArr['response'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            } else {
                if (!valid_email($email)) {
                    $returnArr['response'] = $this->format_string("Invalid Email address", "invalid_email_address");
                } else {
                    $driver = $this->driver_model->get_selected_fields(DRIVERS, 
                        array(
                            'email' => strtolower($email), 
                            'password' => md5($password)
                        ), 
                        array('email', 'image', 'user_name', 'driver_name', 'mobile_number', 'status', 'dail_code', 'outstanding_amount', 'vehicle_number', 'vehicle_model', 'category'));
                    
                    if ($driver->num_rows() != 1) {
                        $returnArr['response'] = $this->format_string('Please check the email and password and try again', 'please_check_email_and_password');
                    } else {
                        if (strtolower($driver->row()->status) != 'active') {
                            $returnArr['response'] = $this->format_string('Your account have not been activated yet', 'driver_account_not_activated');
                        } else {
                            $is_voda_customer = ($this->is_voda_customer($driver->row()->dail_code, $driver->row()->mobile_number) ? true : false);
                            $sso_token = $this->generate_random_string();
                            $driverUpdateData = array(
                                'sso_token' => $sso_token,
                                'is_voda_customer' => $is_voda_customer,
                                'fcm_token' => $fcm_token,
                                'device_type' => $device_type
                            );
                            $this->driver_model->update_details(DRIVERS, $driverUpdateData, array('_id' => new \MongoId($driver->row()->_id)));
                            
                            $returnArr['status'] = '1';
                            $returnArr['response'] = $this->format_string('You are Logged In successfully', 'you_logged_in');

                            if (isset($driver->row()->image)) {
                                if ($driver->row()->image == '') {
                                    $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                                } else {
                                    $driver_image = USER_PROFILE_IMAGE . $driver->row()->image;
                                }
                            } else {
                                $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                            }

                            $modelVal = $this->driver_model->get_selected_fields(MODELS, array('_id' => new \MongoId($driver->row()->vehicle_model)), array('name', 'brand_name'));
                            $vehicle_model = '';
                            $brand_name = '';
                            if ($modelVal->num_rows() > 0) {
                                if (isset($modelVal->row()->name)) {
                                    $vehicle_model = $modelVal->row()->name;
                                    $brand_name = $modelVal->row()->brand_name;
                                }
                            }

                            $categoryInfo = $this->driver_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($driver->row()->category)), array('icon_car_image'));
                            $category_icon = base_url().ICON_MAP_CAR_IMAGE;
                            if ($categoryInfo->num_rows() > 0) {
                                if(isset($categoryInfo->row()->icon_car_image)){
                                    $category_icon = base_url() . ICON_IMAGE . $categoryInfo->row()->icon_car_image;
                                }                                
                            }

                            $outstanding_amount = ($driver->row()->outstanding_amount['amount'] > 0.00 ? $driver->row()->outstanding_amount['amount']:0.00);
                            $is_outstanding_due = (isset($driver->row()->outstanding_amount['is_due']) ? $driver->row()->outstanding_amount['is_due']:false);

                            $returnArr['driver_image']              = (string) base_url() . $driver_image;
                            $returnArr['driver_id']                 = (string) $driver->row()->_id;
                            $returnArr['driver_name']               = (string) $driver->row()->driver_name;
                            $returnArr['sec_key']                   = md5((string) $driver->row()->_id);
                            $returnArr['email']                     = (string) $driver->row()->email;
                            $returnArr['vehicle_number']            = (string) $driver->row()->vehicle_number;
                            $returnArr['vehicle_model']             = (string) $vehicle_model;
                            $returnArr['brand_name']                = (string) $brand_name;
                            $returnArr['calendar_driver_reminder']  = $this->config->item('driver_reminder');
                            $returnArr['sso_token']                 = $sso_token;
                            $returnArr['country_code']              = $driver->row()->dail_code;
                            $returnArr['national_phone_number']     = $driver->row()->mobile_number;
                            $returnArr['is_voda_customer']          = $is_voda_customer;
                            $returnArr['outstanding_amount']        = (string) $outstanding_amount;
                            $returnArr['category_icon']             = (string) $category_icon;
                            $returnArr['is_outstanding_due']        = $is_outstanding_due;
                        }
                    }
                }
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
            $returnArr['debug'] = $this->format_string($ex->getMessage());
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Logout Driver 
     *
     * */
    public function logout()
    {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $condition = array('_id' => new \MongoId($this->driver->_id));
            $updateCondition = array(
                'availability' => 'No', 
                'sso_token' => ''
            );
            $this->driver_model->update_details(DRIVERS, $updateCondition, $condition);
            $returnArr['status'] = '1';
            $returnArr['response'] = $this->format_string("You are logged out", "you_are_logged_out");
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
    *
    * This function send the new password to driver email
    *
    * */
    private function _send_password($pwd = '', $query) {
        $newsid = '10';
        $reset_url = base_url() . 'driver/reset-password-form/' . $pwd;
        $user_name = $query->row()->driver_name;
        $template_values = $this->app_model->get_email_template($newsid, $this->data['langCode']);
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['subject'];
        $drivernewstemplateArr = array(
            'email_title' => $this->config->item('email_title'), 
            'mail_emailTitle' => $this->config->item('email_title'), 
            'mail_logo' => $this->config->item('logo_image'), 
            'mail_footerContent' => $this->config->item('footer_content'), 
            'mail_metaTitle' => $this->config->item('meta_title'), 
            'mail_contactMail' => $this->config->item('site_contact_mail')
        );
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
        return $this->app_model->common_email_send($email_values);
    }

    /**
    *
    * This function forgot driver password request
    *
    * */
    public function forgot_password()
    {
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
                    if (!$this->_send_password($new_pwd, $checkDriver)) {
                        $responseArr['response'] = $this->format_string('Error sending email');
                    } else {
                        $responseArr['status'] = '1';
                        $responseArr['response'] = $this->format_string('Password reset link has been sent to your email address.', 'password_reset_link_sent');
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Authentication Failed.', 'authentication_failed');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing.", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection', 'error_in_connection');
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
                            $responseArr['response'] = $this->format_string('Your current password is not matching our records.','password_not_matching');
                        }
                    } else {
                        $responseArr['response'] = $this->format_string('New Password should be atleast 6 characters.','password_should_be_6_characters');
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Authentication Failed','authentication_failed');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing.","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
            $responseArr['debug'] = $this->format_string($ex->getMessage());
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Update driver Location
     *
     * */
    public function update_location() {
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
                $driver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'mode', 'availability', 'verify_status'));
                if ($driver->num_rows() == 1) {
                    $geo_data = array(
                        'loc' => array(
                            'lon' => floatval($longitude), 
                            'lat' => floatval($latitude)
                        ),
                        'last_active_time'=>new \MongoDate(time())
                    );
                    $this->driver_model->update_details(DRIVERS, $geo_data, array('_id' => new \MongoId($driver_id)));

                    $ride_id = '';
                    $verify_status = 'No';
                    $errorMsg = $this->format_string("You do not have a verified account, Contact support for more information or help", "account_not_verified", TRUE);
                    if (isset($driver->row()->verify_status) && $driver->row()->verify_status == 'Yes') {
                        $verify_status = 'Yes';
                        $errorMsg = '';
                    }

                    $available = "Available";
                    $unavailable = "Unavailable";
                    $checkPending = $this->app_model->get_uncompleted_trips($driver_id, array('ride_id', 'ride_status', 'pay_status'));
                    if ($checkPending->num_rows() > 0) {
                        $availability_string = $unavailable;
                        $ride_id = $checkPending->row()->ride_id;
                        $errorMsg = $this->format_string("You have a pending trip / transaction. Please tap this message to view. You will not get ride requests until you resolve this issue", "pending_trip_cant_ride_request", TRUE);
                    } else {
                        $availability_string = $available;
                    }

                    if ($c_ride_id != '' && $c_ride_id != NULL) {
                        $checkInfo = $this->driver_model->get_all_details(TRACKING, array('ride_id' => $c_ride_id));
                        $current_location = array('timestamp' => new \MongoDate(time()),
                            'location' => array('lat' => floatval($latitude), 'lon' => floatval($longitude))
                        );
                        if ($checkInfo->num_rows() > 0) {
                            $this->app_model->simple_push(TRACKING, array('ride_id' => (string) $c_ride_id), array('steps' => $current_location));
                        } else {
                            $this->app_model->simple_insert(TRACKING, array('ride_id' => (string) $c_ride_id));
                            $this->app_model->simple_push(TRACKING, array('ride_id' => (string) $c_ride_id), array('steps' => $current_location));
                        }
                    }

                    if (empty($availability_string)) {
                        $availability_string = json_decode("{}");
                    }

                    $returnArr['status'] = '1';
                    $returnArr['response'] = array(
                        'message' => $this->format_string('Geo Location Updated', 'geo_location_updated'),
                        'availability' => $availability_string,
                        'ride_id' => $ride_id,
                        'verify_status' => $verify_status,
                        'errorMsg' => $errorMsg
                    );
                } else {
                    $returnArr['response'] = $this->format_string("Invalid Driver", "invalid_driver");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Are Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Connection Error", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Update driver availablity
     *
     * */
    public function update_availablity() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->input->post('driver_id');
            $availability = $this->input->post('availability');
            $distance = $this->input->post('distance');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }
            if ($chkValues >= 2) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id','last_online_time'));
                if ($checkDriver->num_rows() == 1) {
                    if($availability == 'Yes') {
                        $dataArr = array('last_online_time' => new \MongoDate(time()));
                        $this->driver_model->update_details(DRIVERS, $dataArr, array('_id' => new \MongoId($driver_id)));
                    } else {
                        $this->mileage_model->update_mileage_system($driver_id,$checkDriver->row()->last_online_time->sec,'free-roaming',$distance,$this->data['d_distance_unit']);
                    }
                    $avail_data = array('availability' => $availability, 'last_active_time' => new \MongoDate(time()));
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
    public function update_mode() {
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
                $returnArr['response'] = $this->format_string("Some Parameters Are Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Accept Reserved Rides
     *
     * */
    public function accept_reserved_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        $returnArr['ride_view'] = 'stay';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }
            if ($chkValues >= 2) {
                $driver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'driver_commission','last_online_time'));
                if ($driver->num_rows() == 1) {
                    $ride = $this->driver_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'location.id', 'coupon_used', 'coupon', 'est_pickup_date', 'commission_percent','fare_breakup', 'timezone'));
                    $dataArr = array('last_accept_time' => new \MongoDate(time()));
                    $this->driver_model->update_details(DRIVERS, $dataArr, array('_id' => new \MongoId($driver_id)));
                    if ($ride->num_rows() == 1) {
                        if ($ride->row()->ride_status == 'Booked') {
                            $rider = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($ride->row()->user['id'])), array('_id', 'fcm_token', 'email', 'country_code', 'phone_number', 'device_type'));
                            if ($rider->num_rows() > 0) {
                                $vehicleInfo = $this->driver_model->get_selected_fields(MODELS, array('_id' => new \MongoId($driver->row()->vehicle_model)), array('name'));
                                $vehicle_model = '';
                                if ($vehicleInfo->num_rows() > 0) {
                                    $vehicle_model = $vehicleInfo->row()->name;
                                }
                                $driverInfo = array(
                                    'id' => (string) $driver->row()->_id,
                                    'name' => (string) $driver->row()->driver_name,
                                    'email' => (string) $driver->row()->email,
                                    'phone' => (string) $driver->row()->dail_code . $driver->row()->mobile_number,
                                    'vehicle_model' => (string) $vehicle_model,
                                    'vehicle_no' => (string) $driver->row()->vehicle_number
                                );
                                $history = array(
                                    'booking_time' => $ride->row()->booking_information['booking_date'],
                                    'driver_assigned' => new \MongoDate(time())
                                );
                                $rideDetails = array(
                                    'ride_status' => 'Confirmed',
                                    'driver' => $driverInfo,
                                    'history' => $history
                                );
                                $checkBooked = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'ride_status' => 'Booked'), array('ride_id', 'ride_status'));
                                $checkAvailable = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('mode'));
                                $availablity = false;
                                if ($checkAvailable->row()->mode == 'Available') {
                                    $availablity = true;
                                }
                                if ($checkBooked->num_rows() > 0 && $availablity === true) {
                                    $this->driver_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));

                                    /* Update the coupon usage details */
                                    if ($ride->row()->coupon_used == 'Yes') {
                                        $usage = array("user_id" => (string) $rider->row()->_id, "ride_id" => $ride_id);
                                        $promo_code = (string) $ride->row()->coupon['code'];
                                        $this->driver_model->simple_push(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
                                    }
                                    /* Sending notification to user regarding booking confirmation -- Start */
                                    $message = $this->format_string('Your ride request is confirmed', 'ride_request_confirmed');
                                    $options = array(
                                        'ride_id' => $ride_id
                                    );
                                    $user_type = 'rider';
                                    $this->notify($rider->row()->fcm_token, $message, 'reserved_ride_confirmed', $options, $rider->row()->device_type, $user_type);

                                    if (!isset($ride->row()->timezone) || !$ride->row()->timezone) {
                                        $pickup_lat = $ride->row()->booking_information['pickup']['latlong']['lat'];
                                        $pickup_lon = $ride->row()->booking_information['pickup']['latlong']['lon'];
                                        $this->setTimezone($pickup_lat, $pickup_lon);
                                    } else {
                                        set_default_timezone($ride->row()->timezone);
                                    }
                                    
                                    $this->mail_model->user_ride_accepted($ride->row(), $rider->row(), $driverInfo);
                                    $this->sms_model->sms_on_ride_accept($ride->row(), $rider->row(), $driverInfo);

                                    $returnArr['status'] = '1';
                                    $returnArr['response'] = array('message' => $this->format_string("Ride Accepted", "ride_accepted"));
                                } else {
                                    $returnArr['response'] = $this->format_string('you are too late, this ride is already booked.', 'you_are_too_late_to_book_this_ride');
                                }
                            } else {
                                $returnArr['response'] = $this->format_string('You cannot accept this ride.', 'you_cannot_accept_this_ride');
                            }
                        } else {
                            $returnArr['response'] = $this->format_string('you are too late, this ride is already booked.', 'you_are_too_late_to_book_this_ride');
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
     * Start Upcoming Ride
     *
     * */
    public function start_upcoming_ride() 
    {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        $returnArr['ride_view'] = 'stay';

        try {
            $ride_id = $this->input->post('ride_id');
            $driver_lat = $this->input->post('driver_lat');
            $driver_lon = $this->input->post('driver_lon');
            $distance = $this->input->post('distance');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 3) {

                $checkRide = $this->driver_model->get_selected_fields(RIDES, array('ride_id' => $ride_id));

                $driver_id = $checkRide->row()->driver['id'];

                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'vehicle_type', 'driver_commission','last_online_time'));

                $this->mileage_model->update_mileage_system($driver_id, $checkDriver->row()->last_online_time->sec,'free-roaming',$distance,$this->data['d_distance_unit'], $ride_id);
                
                $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'fcm_token', 'device_type'));

                /* Update the ride information with fare and driver details -- Start */
                $pickup_lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
                $pickup_lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
                $from = $driver_lat . ',' . $driver_lon;
                $to = $pickup_lat . ',' . $pickup_lon;

                if (!isset($checkRide->row()->timezone) || !$checkRide->row()->timezone) {
                    $this->setTimezone($pickup_lat, $pickup_lon);
                } else {
                    set_default_timezone($checkRide->row()->timezone);
                }

                $urls = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$this->data['google_maps_api_key'];
                $gmap = file_get_contents($urls);
                $map_values = json_decode($gmap);
                $routes = $map_values->routes;
                if (!empty($routes))
                {
                    usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));                     
                    $distance_unit = $this->data['d_distance_unit'];
                    $duration_unit = 'min';
                    if(isset($checkRide->row()->fare_breakup))
                    {
                        if($checkRide->row()->fare_breakup['distance_unit']!='')
                        {
                            $distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
                            $duration_unit = $checkRide->row()->fare_breakup['duration_unit'];
                        } 
                    }
                    $mindurationtext = '';
                    $est_pickup_time = time();
                    if (!empty($routes[0])) {
                        $est_pickup_time = (time()) + $routes[0]->legs[0]->duration->value;
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
                    $driverInfo = array(
                        'lat_lon' => (string) $driver_lat . ',' . $driver_lon,
                        'est_eta' => (string) $mindurationtext
                    );
                    $history = array(
                        'estimate_pickup_time' => new \MongoDate($est_pickup_time)
                    );
                    $driver_commission = $checkRide->row()->commission_percent;
                    if (isset($checkDriver->row()->driver_commission)) {
                        $driver_commission = $checkDriver->row()->driver_commission;
                    }
                    $rideDetails = array(
                        'started'   =>  'Yes',
                        'commission_percent' => floatval($driver_commission),
                        'driver.lat_lon' => (string) $driver_lat . ',' . $driver_lon,
                        'driver.est_eta' => (string) $mindurationtext,
                        'fare_breakup' => $fare_breakup,
                        'tax_breakup' => array('service_tax' => $service_tax),
                        'booking_information.est_pickup_date' => new \MongoDate($est_pickup_time),
                        'history' => $history
                    );

                    $this->driver_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
                    
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

                    $vehicleInfo = $this->driver_model->get_selected_fields(MODELS, array('_id' => new \MongoId($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
                    $vehicle_model = '';
                    if ($vehicleInfo->num_rows() > 0) {
                        $vehicle_model = $vehicleInfo->row()->name;
                    }

                    $vehicle_type = $this->getVehicleType($checkDriver->row()->vehicle_type);

                    $this->load->model('category_model');

                    if (isset($checkRide->row()->category) && !empty($checkRide->row()->category)) {
                        $category = $this->category_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($checkRide->row()->category)), array('icon_active'));
                        $vehicle_image = '';
                        if ($category->num_rows() > 0) {
                            $vehicle_image = base_url() . ICON_IMAGE . $category->row()->icon_active;
                        } else {
                            $vehicle_image = null;
                        }
                    } else if ($vehicle_type) {
                        $vehicle_image = $this->getVehicleImage($vehicle_type);
                    } else {
                        $vehicle_image = null;
                    }

                    $driver_profile = array(
                        'driver_id' => (string) $checkDriver->row()->_id,
                        'driver_name' => (string) $this->get_driver_first_name($checkDriver->row()->driver_name),
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
                        'vehicle_type' => $vehicle_type,
                        'vehicle_image' => $vehicle_image,
                        'pickup_location' => (string) $checkRide->row()->booking_information['pickup']['location'],
                        'pickup_lat' => (string) $pickup_lat,
                        'pickup_lon' => (string) $pickup_lon
                    );
                    /* Preparing driver information to share with user -- End */

                    /* Preparing user information to share with driver -- Start */
                    if ($userVal->row()->image == '') {
                        $user_image = USER_PROFILE_IMAGE_DEFAULT;
                    }
                    else {
                        $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                    }
                    $user_review = 0;
                    if (isset($userVal->row()->avg_review)) {
                        $user_review = $userVal->row()->avg_review;
                    }
                    $user_profile = array('user_id' => (string)$userVal->row()->_id,
                        'user_name' => get_first_name($userVal->row()->user_name),
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
                    $message = $this->format_string('Your driver is on the way', 'user_trip_started');
                    $options = $driver_profile;
                    $user_type = 'rider';
                    $this->notify($userVal->row()->fcm_token, $message, 'ride_later_started', $driver_profile, $userVal->row()->device_type, $user_type); /*update ride later notification*/

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
                    
                    if ($ride_id != '') {
                        $checkInfo = $this->driver_model->get_all_details(TRACKING, array('ride_id' => $ride_id));
                    
                        $latlng = $driver_lat . ',' . $driver_lon;
                        $gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
                        $mapValues = json_decode($gmap)->results;
                        if(!empty($mapValues)){
                            $formatted_address = $mapValues[0]->formatted_address;
                            $cuurentLoc = array('timestamp' => new \MongoDate(time()),
                                'locality' => (string) $formatted_address,
                                'location' => array('lat' => floatval($driver_lat), 'lon' => floatval($driver_lon))
                            );
                            
                            if ($checkInfo->num_rows() > 0) {
                                $this->app_model->simple_push(TRACKING, array('ride_id' => (string) $ride_id), array('steps' => $cuurentLoc));
                            } else {
                                $this->app_model->simple_insert(TRACKING, array('ride_id' => (string) $ride_id));
                                $this->app_model->simple_push(TRACKING, array('ride_id' => (string) $ride_id), array('steps' => $cuurentLoc));
                            }
                        }
                    }
                    if (empty($user_profile)) {
                        $user_profile = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('user_profile' => $user_profile);
                }
                else{
                    $returnArr['response'] = $this->format_string('Sorry ! We can not fetch your information', 'cannot_fetch_location_information_in_map');
                }
            }
            else {
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
        $returnArr['ride_view'] = 'stay';
        try {
            $driver_id = $this->input->post('driver_id');
            $ride_id = $this->input->post('ride_id');
            $driver_lat = $this->input->post('driver_lat');
            $driver_lon = $this->input->post('driver_lon');
            $distance = $this->input->post('distance');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 4) {
                $checkDriver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'vehicle_type', 'driver_commission','last_online_time'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->driver_model->get_selected_fields(RIDES, array('ride_id' => $ride_id));
                    $dataArr = array('last_accept_time' => new \MongoDate(time()));
                    $this->driver_model->update_details(DRIVERS, $dataArr, array('_id' => new \MongoId($driver_id)));
                    $this->mileage_model->update_mileage_system($driver_id,$checkDriver->row()->last_online_time->sec,'free-roaming',$distance,$this->data['d_distance_unit'],$ride_id);
                    if ($checkRide->num_rows() == 1) {

                        $lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
                        $lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];

                        if (!isset($checkRide->row()->timezone) || !$checkRide->row()->timezone) {
                            $this->setTimezone($lat, $lon);
                        } else {
                            set_default_timezone($checkRide->row()->timezone);
                        }

                        if ($checkRide->row()->ride_status == 'Booked') {
                            $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'fcm_token', 'device_type'));
                            if ($userVal->num_rows() > 0) {
                                /* Update the ride information with fare and driver details -- Start */
                                $pickup_lon = $lon;
                                $pickup_lat = $lat;
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

                                        $vehicle_type = $this->getVehicleType($checkDriver->row()->vehicle_type);

                                        $this->load->model('category_model');
                                        
                                        if (isset($checkRide->row()->category) && !empty($checkRide->row()->category)) {
                                            $category = $this->category_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($checkRide->row()->category)), array('icon_active'));
                                            $vehicle_image = '';
                                            if ($category->num_rows() > 0) {
                                                $vehicle_image = base_url() . ICON_IMAGE . $category->row()->icon_active;
                                            } else {
                                                $vehicle_image = null;
                                            }
                                        }
                                        else if ($vehicle_type) {
                                            $vehicle_image = $this->getVehicleImage($vehicle_type);
                                        } else {
                                            $vehicle_image = null;
                                        }
                                        
                                        $driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
                                            'driver_name' => (string) $this->get_driver_first_name($checkDriver->row()->driver_name),
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
                                            'vehicle_type' => $vehicle_type,
                                            'vehicle_image' => $vehicle_image,
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
                                            'user_name' => get_first_name($userVal->row()->user_name),
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
                                        $message = $this->format_string('Your ride request is confirmed', 'ride_request_confirmed');
                                        $options = $driver_profile;
                                        $user_type = 'rider';
                                        $this->notify($userVal->row()->fcm_token, $message, 'ride_confirmed', $driver_profile, $userVal->row()->device_type, $user_type);
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
                                        
                                        if ($ride_id != '') {
                                            $checkInfo = $this->driver_model->get_all_details(TRACKING, array('ride_id' => $ride_id));
                                        
                                            $latlng = $driver_lat . ',' . $driver_lon;
                                            $gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
                                            $mapValues = json_decode($gmap)->results;
                                            if(!empty($mapValues)){
                                                $formatted_address = $mapValues[0]->formatted_address;
                                                $cuurentLoc = array('timestamp' => new \MongoDate(time()),
                                                    'locality' => (string) $formatted_address,
                                                    'location' => array('lat' => floatval($driver_lat), 'lon' => floatval($driver_lon))
                                                );
                                                
                                                if ($checkInfo->num_rows() > 0) {
                                                    $this->app_model->simple_push(TRACKING, array('ride_id' => (string) $ride_id), array('steps' => $cuurentLoc));
                                                } else {
                                                    $this->app_model->simple_insert(TRACKING, array('ride_id' => (string) $ride_id));
                                                    $this->app_model->simple_push(TRACKING, array('ride_id' => (string) $ride_id), array('steps' => $cuurentLoc));
                                                }
                                            }
                                        }
                                        

                                        if (empty($user_profile)) {
                                            $user_profile = json_decode("{}");
                                        }
                                        $returnArr['status'] = '1';
                                        $returnArr['response'] = array('user_profile' => $user_profile, 'message' => $this->format_string("Ride Accepted", "ride_accepted"));
                                    } else {
                                        $returnArr['response'] = $this->format_string('You are too late, this ride is already booked.', 'you_are_too_late_to_book_this_ride');
                                    }
                                }else{
                                    $returnArr['response'] = $this->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');
                                }
                            } else {
                                $returnArr['response'] = $this->format_string('You cannot accept this ride.', 'you_cannot_accept_this_ride');
                            }
                        } else {
                            $returnArr['response'] = $this->format_string('You are too late, this ride is already booked.', 'you_are_too_late_to_book_this_ride');
                        }
                    } else {
                        $returnArr['ride_view'] = 'home';
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
     * This Function return the ride cancellation reson for driver 
     *
     * */
    public function cancelling_reason() {
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
                $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email'));
                if ($checkDriver->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status','cancelled'));
                    if ($checkRide->num_rows() == 1) {
                        if ($checkRide->row()->ride_status != 'Cancelled') {
                            $reasonVal = $this->app_model->get_selected_fields(CANCELLATION_REASON, array('status' => 'Active', 'type' => 'driver'), array('reason'));
                            if ($reasonVal->num_rows() > 0) {
                                $reasonArr = array();
                                foreach ($reasonVal->result() as $row) {
                                    $reasonArr[] = array('id' => (string) $row->_id,
                                        'reason' => (string) $row->reason
                                    );
                                }
                                $returnArr['status'] = '1';
                                $returnArr['response'] = array('reason' => $reasonArr);
                            } else {
                                $returnArr['response'] = $this->format_string('No reasons available for cancelling ride', 'no_reasons_available_to_cancel_ride');
                            }
                        }else{
                            $returnArr['response'] = $this->format_string('This ride has already been cancelled', 'already_ride_cancelled');
                        }
                    }else{
                        $returnArr['response'] = $this->format_string("This ride is unavailable", "ride_unavailable");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Driver not found", "driver_not_found");
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
    public function cancel_ride() {
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
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'driver.id', 'coupon_used', 'coupon', 'cancelled', 'type'));

                    if ($checkRide->num_rows() == 1) {

                        $doAction = 0;
                        if ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived') {
                            $doAction = 1;
                            if ($checkRide->row()->ride_status == 'Cancelled') {
                                $doAction = 0;
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
                                    if ($checkRide->row()->type == 'Now') {
                                        $ride_status = 'Cancelled';
                                    }
                                    else {
                                        $ride_status = 'Booked';
                                    }
                                    $rideDetails = array('ride_status' => $ride_status,
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
                                    if ($checkRide->row()->ride_status == 'Cancelled') {
                                        $message = $this->format_string("your ride cancelled","your_ride_cancelled");
                                    }
                                    else if ($checkRide->row()->ride_status == 'Booked') {
                                        $message = $this->format_string("Sorry your driver cancelled the ride. We will look out for another driver.","your_reserved_ride_cancelled");
                                    }
                                    $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('fcm_token', 'device_type'));
                                    $options = array('ride_id' => (string) $ride_id);
                                    $user_type = 'rider';
                                    $this->notify($userVal->row()->fcm_token, $message, 'ride_cancelled', $options, $userVal->row()->device_type, $user_type);

                                    $this->app_model->remove_driver_from_ride($ride_id);
                                    
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
                            $returnArr['response'] = $this->format_string('This ride has already been cancelled', 'already_ride_cancelled');
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
    public function arrived() {
        $returnArr['status'] = '0';
        $returnArr['ride_view'] = 'stay';
        $returnArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            $driver_id = $this->driver->_id->{'$id'};

            $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'driver.id'));
            if ($checkRide->num_rows() == 1) {
                if ($checkRide->row()->ride_status == 'Confirmed') {
                    /* Update the ride information */
                    $rideDetails = array(
                        'ride_status' => 'Arrived',
                        'history.arrived_time' => new \MongoDate(time()),
                        'started' => null
                    );
                    $this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
                    
                    $driver_lat = 0;
                    $driver_lon = 0;
                    if(isset($this->driver->loc)){
                        if(is_array($this->driver->loc)){
                            $driver_lat = floatval($this->driver->loc['lat']);
                            $driver_lon = floatval($this->driver->loc['lon']);
                        }
                    }
                    
                    /* Notification to user about driver reached his location */
                    $user_id = $checkRide->row()->user['id'];
                    $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'fcm_token', 'device_type'));
                    $message = $this->format_string("Your Driver has arrived","driver_arrived");
                    $options = array(
                        'ride_id'       => (string) $ride_id, 
                        'user_id'       => (string) $user_id, 
                        'driver_lat'    => (string) $driver_lat, 
                        'driver_lon'    => (string) $driver_lon,
                        'driver_image'  => (string) $this->driver_model->get_driver_image($this->driver->image)
                    );
                    $user_type = 'rider';
                    $this->notify($userVal->row()->fcm_token, $message, 'cab_arrived', $options, $userVal->row()->device_type, $user_type);

                    $this->sms_model->driver_arrived($userVal->row(), $this->driver);
                    
                    $returnArr['status'] = '1';
                    $returnArr['response'] = $this->format_string('Status Updated', 'status_updated');
                } else {
                    if($checkRide->row()->ride_status == 'Arrived'){
                        $returnArr['ride_view'] = 'next';
                        $returnArr['response'] = $this->format_string('Already Arrived At Location', 'ride_location_already_arrived');
                    }else{
                        $returnArr['ride_view'] = 'detail';
                        $returnArr['response'] = $this->format_string('Ride Cancelled', 'ride_cancelled');
                    }
                }
            } else {
                $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
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
        $returnArr['ride_view'] = 'stay';
        $returnArr['response'] = '';
        try {
            $driver_id  = $this->driver->_id->{'$id'};
            $ride_id    = $this->input->post('ride_id');
            $pickup_lat = $this->input->post('pickup_lat');
            $pickup_lon = $this->input->post('pickup_lon');
            $distance   = $this->input->post('distance');
            $drop_lat   = (string)$this->input->post('drop_lat');
            $drop_lon   = (string)$this->input->post('drop_lon');

            if ($driver_id !='' && $ride_id !='' && $pickup_lat !='' && $pickup_lon !='' && $drop_lat !='' && $drop_lon !='') {
                $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'user.id', 'driver.id'));
                if ($checkRide->num_rows() == 1) {
                    if ($checkRide->row()->ride_status == 'Arrived') {

                        $dataArr = array('last_begin_time' => new \MongoDate(time()));
                        $this->driver_model->update_details(DRIVERS, $dataArr, array('_id' => new \MongoId($driver_id)));

                        $this->mileage_model->update_mileage_system($driver_id, $this->driver->last_accept_time->sec, 'customer-pickup', $distance, $this->data['d_distance_unit'], $ride_id);

                        $latlng = $pickup_lat . ',' . $pickup_lon;
                        $gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false" . $this->data['google_maps_api_key']);
                        $map_result = json_decode($gmap);
                        $mapValues = $map_result->results;
                        
                        $drop_latlng = $drop_lat . ',' . $drop_lon;
                        $urldrop = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $drop_latlng . "&sensor=false" . $this->data['google_maps_api_key'];
                        $gmap_drop = file_get_contents($urldrop);
                        $drop_result = json_decode($gmap_drop);
                        $mapValues_drop = $drop_result->results;
                        
                        if (!empty($mapValues) && !empty($mapValues_drop)) {
                        
                            $formatted_address  = $mapValues[0]->formatted_address;
                            $drop_address       = $mapValues_drop[0]->formatted_address;
                            
                            /* Update the ride information */
                            $rideDetails = array(
                                'ride_status' => 'Onride',
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
                            $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'fcm_token', 'device_type'));
                            $message = $this->format_string("Your trip has started", "your_trip_has_been_started");
                            $options = array(
                                'ride_id'       => (string) $ride_id, 
                                'user_id'       => (string) $user_id, 
                                'drop_lat'      => (string) $drop_lat, 
                                'drop_lon'      => (string) $drop_lon, 
                                'pickup_lat'    => (string) $pickup_lat, 
                                'pickup_lon'    => (string) $pickup_lon,
                                'driver_image'  => (string) $this->driver_model->get_driver_image($this->driver->image)
                            );
                            $user_type = 'rider';
                            $this->notify($userVal->row()->fcm_token, $message, 'trip_begin', $options, $userVal->row()->device_type, $user_type);

                            $returnArr['status'] = '1';
                            $returnArr['ride_view'] = 'next';
                            $returnArr['response'] = $this->format_string('Ride Started', 'ride_started');
                        } else{
                            $returnArr['response'] = $this->format_string('Sorry ! We can not fetch your information', 'cannot_fetch_location_information_in_map');
                            $returnArr['google_maps_error'] = $map_result->error_message;
                        }
                    } else {
                        if ($checkRide->row()->ride_status == 'Onride') {
                            $returnArr['ride_view'] = 'next';
                            $returnArr['response'] = $this->format_string('Ride Already Started', 'already_ride_started');
                        } else {
                            $returnArr['ride_view'] = 'detail';
                            $returnArr['response'] = $this->format_string('Ride Cancelled', 'ride_cancelled');
                        }
                    }
                } else {
                    $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
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
        $returnArr['ride_view'] = 'stay';
        $returnArr['response'] = '';
        try {
            $driver_id = $this->driver->_id->{'$id'};
            $ride_id = $this->input->post('ride_id');
            $drop_lat = $this->input->post('drop_lat');
            $drop_lon = $this->input->post('drop_lon');

            $interrupted = (string) $this->input->post('interrupted');
            $drop_loc = $this->input->post('drop_loc');
            $drop_time = $this->input->post('drop_time');

            $distance = $this->input->post('distance'); // in kilometer(km)
            $device_distance = $this->input->post('distance'); // in kilometer(km)
            $wait_time_frame = $this->input->post('wait_time'); // in minutes
            
            $travel_history = $this->input->post('travel_history'); // string lat;log;time,lat;lon;time,.etc
            $travel_history = trim($travel_history, ',');
            /** update travel history **/
            $travel_historyArr = array();
            $travelRecords = @explode(',', $travel_history);
            if (count($travelRecords) > 1) {
                for ( $i = 0; $i < count($travelRecords); $i++) {
                    $splitedHis = @explode(';', $travelRecords[$i]);
                    $travel_historyArr[] = array(
                        'lat' => $splitedHis[0],
                        'lon' => $splitedHis[1],
                        'update_time' => new \MongoDate(strtotime($splitedHis[2]))
                    );
                }
            }
            if (!empty($travel_historyArr)) {
                $getRideHIstoryVal = $this->driver_model->get_all_details(TRAVEL_HISTORY, array('ride_id' => (string) $ride_id));
                if ($getRideHIstoryVal->num_rows() > 0) {
                    $this->driver_model->update_details(TRAVEL_HISTORY, array('history_end' => $travel_historyArr),array('ride_id' => $ride_id));
                } else{
                    $this->driver_model->simple_insert(TRAVEL_HISTORY, array('ride_id' => $ride_id, 'history_end' => $travel_historyArr));
                }
            }
            /**/
            $dis_val_arr = array();
            $val1 = array();
            $val2 = array();
            $getRideHIstory = $this->driver_model->get_all_details(TRAVEL_HISTORY, array('ride_id' => $ride_id));
            if ($getRideHIstory->num_rows() > 0) {
                foreach ($getRideHIstory->result() as $key => $data) {
                    $hisMid = array();
                    $hisEnd = array();
                    if (isset($data->history)) {
                        $hisMid = $data->history;
                    }
                    if (isset($data->history_end)) {
                        $hisEnd = $data->history_end;
                    }
                    $hisFinal = $hisEnd;
                    if (count($hisEnd) > count($hisMid)){
                        $hisFinal = $hisEnd;
                    } else {
                        $hisFinal = $hisMid;
                    }
                    foreach ($hisFinal as $value) {
                        if (count($val1) == 0) {
                            $val1[0] = $value['lat'];
                            $val1[1] = $value['lon']; 
                            $val2[0] = $value['lat'];
                            $val2[1] = $value['lon'];
                            continue;
                        } else {
                            $val1[0] = $val2[0];
                            $val1[1] = $val2[1]; 
                        }
                        $val2[0] = $value['lat'];
                        $val2[1] = $value['lon'];
                        $dis_val_arr[] = round($this->cal_distance_from_positions($val1[0], $val1[1], $val2[0], $val2[1]), 3);
                    }
                }
            }
            $math_ext_distance = array_sum($dis_val_arr);

            if ($interrupted == 'YES' || $interrupted != 'YES') {
                $wait_time = 0;
                if ($wait_time_frame != '') {
                    $wt = @explode(':', $wait_time_frame);
                   
                    $h = 0; $m = 0; $s = 0;
                    if (isset($wt[0])) $h = intval($wt[0]);
                    if (isset($wt[1])) $m = intval($wt[1]);
                    if (isset($wt[2])) $s = intval($wt[2]);
                   
                    if ($h > 0) {
                        $wait_time = $h * 60;
                    }
                    if ($m > 0) {
                        $wait_time = $wait_time + ($m);
                    }
                    if ($s > 30) {
                        $wait_time = $wait_time + 1;
                    }
                }
            }

            if ($driver_id != '' && $ride_id != '' && $distance != '') {
                $chkValues = 1;
            } else {
                $chkValues = 0;
            }

            if ($chkValues > 0) {
                $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
                if ($checkRide->num_rows() == 1) {
                    if ($math_ext_distance > 0) {
                        $distance = $math_ext_distance;
                    }
                    
                    $distance_unit = $this->data['d_distance_unit'];
                    if (isset($checkRide->row()->fare_breakup['distance_unit'])) {
                        $distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
                    }
                    
                    $dataArr = array('last_online_time' => new \MongoDate(time()));
                    $this->driver_model->update_details(DRIVERS, $dataArr, array('_id' => new \MongoId($driver_id)));
                    $this->mileage_model->update_mileage_system($driver_id, $this->driver->last_begin_time->sec,'customer-drop', $distance, $this->data['d_distance_unit'], $ride_id);
                    
                    if ($distance_unit == 'mi') {
                        $distance = round(($distance / 1.609344), 2);
                    }
                    
                    if ($checkRide->row()->ride_status=='Onride') {
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
                            $latlng = $drop_lat . ',' . $drop_lon;
                            $gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
                        } else {
                            $latlng = $drop_lat . ',' . $drop_lon;
                            $gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
                        }
                        $map_values = json_decode($gmap);
                        $mapValues = $map_values->results;
                        if (!empty($mapValues)) {
                        
                            $dropping_address = $mapValues[0]->formatted_address;

                            $pickup_time = $checkRide->row()->booking_information['pickup_date']->sec;
                            $drop_time = time();
                            $ride_time = $drop_time - $pickup_time;
                            $ride_time_min = round($ride_time / 60);
                            
                            $ride_begin_time = $checkRide->row()->history['begin_ride']->sec;
                            $ride_end_time = $drop_time;                                    // Trip Timestamp
                            $ride_wait_time = round($wait_time * 60); // in Seconds
                            
                            $ride_time_min = $ride_time_min - $wait_time;
                            
                            if (($ride_begin_time + $ride_wait_time) <= $ride_end_time + 100) {

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
                                    $grand_fare = $total_peak_time_charge;
                                }
                                if ($checkRide->row()->fare_breakup['night_charge'] != '') {
                                    $total_night_time_charge = $total_fare * $checkRide->row()->fare_breakup['night_charge'];
                                    if ($total_peak_time_charge == 0){
                                        $grand_fare = $total_night_time_charge;
                                    } else {
                                        $grand_fare = $grand_fare + $total_night_time_charge;
                                    }
                                }
                                
                                if ($grand_fare != $total_fare) {
                                    $grand_fare = $total_peak_time_charge + $total_night_time_charge;
                                } else {
                                    $grand_fare = $total_fare;
                                }
                                
                                if ($total_peak_time_charge > 0 && $total_night_time_charge > 0) {
                                    $total_surge = $total_peak_time_charge + $total_night_time_charge;
                                    $surge_val = $checkRide->row()->fare_breakup['peak_time_charge'] + $checkRide->row()->fare_breakup['night_charge'];
                                    $unit_surge = ($total_surge-$total_fare) / $surge_val;
                                    $total_peak_time_charge = $unit_surge * $checkRide->row()->fare_breakup['peak_time_charge'];
                                    $total_night_time_charge = $unit_surge * $checkRide->row()->fare_breakup['night_charge'];
                                } else {
                                    if ($total_peak_time_charge > 0) {
                                        $total_peak_time_charge = $grand_fare - $total_fare;
                                    }
                                    if ($total_night_time_charge > 0) {
                                        $total_night_time_charge = $grand_fare - $total_fare;
                                    }
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
                                
                                if ($checkRide->row()->tax_breakup['service_tax'] != '') {
                                    $total_tax = $grand_fare * 0.01 * $checkRide->row()->tax_breakup['service_tax'];
                                    $grand_fare = $grand_fare + $total_tax;
                                }
                                
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
                                );
                                $summary = array('ride_distance' => round($distance, 2),
                                    'device_distance' => round($device_distance, 2),
                                    'math_distance' => round($math_ext_distance, 2),
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
                                
                                $min_short = $this->format_string('min', 'min_short');
                                $mins_short = $this->format_string('mins', 'mins_short');
                                if ($ride_time_min > 1) {
                                    $ride_time_unit = $mins_short;
                                } else {
                                    $ride_time_unit = $min_short;
                                }
                                if ($wait_time > 1) {
                                    $wait_time_unit = $mins_short;
                                } else {
                                    $wait_time_unit = $min_short;
                                }
                                
                                $distance_unit = $this->data['d_distance_unit'];
                                if (isset($checkRide->row()->fare_breakup['distance_unit'])) {
                                    $distance_unit = $checkRide->row()->fare_breakup['distance_unit'];
                                }
                                if ($distance_unit == 'km'){
                                    $disp_distance_unit = $this->format_string('km', 'km');
                                } else if ($distance_unit == 'mi'){
                                    $disp_distance_unit = $this->format_string('mi', 'mi');
                                }
                                $fare_details = array('currency' => $currency,
                                    'ride_fare' => floatval(round($grand_fare, 2)),
                                    'ride_distance' => floatval(round($distance, 2)) .' '.$disp_distance_unit,
                                    'ride_duration' => floatval(round($ride_time_min, 2)) .' '. $ride_time_unit,
                                    'waiting_duration' => floatval(round($wait_time, 2)) .' '. $wait_time_unit,
                                    'need_payment' => $need_payment
                                );

                                $amount_commission = 0;
                                $driver_revenue = 0;

                                $total_grand_fare = $coupon_discount + $grand_fare;
                                $total_grand_fare_without_tax = $total_grand_fare - $total_tax;
                                $admin_commission_percent = $checkRide->row()->commission_percent;
                                $amount_commission = (($total_grand_fare_without_tax * 0.01) * $admin_commission_percent)+$total_tax;
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
                                $this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
                                $this->app_model->simple_insert(PAYMENTS, array('ride_id' => (string) $ride_id, 'total' => round($grand_fare, 2), 'transactions' => array()));

                                /* First ride money credit for referrer */
                                $this->user_model->first_ride_credit($checkRide->row()->user['id']);
                                
                                $makeInvoice = 'No';
                                /*  Making the automatic payment process start  */
                                #$this->auto_payment_deduct($ride_id);
                                $crideNew = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
                                if ($crideNew->num_rows() > 0) {
                                    if ($crideNew->row()->ride_status == "Completed") {
                                        $need_payment = 'NO';
                                        $makeInvoice = 'Yes';
                                    }
                                }
                                /*  Making the automatic payment process end    */
                                
                                /* Sending notification to user regarding booking confirmation -- Start */
                                # Push notification
                                $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('fcm_token', 'device_type'));
                                if ($need_payment == 'NO') {
                                    $user_id = $checkRide->row()->user['id'];
                                    $message = $this->format_string('Ride Completed', 'ride_completed');
                                    $options = array(
                                        'ride_id' => (string) $ride_id, 
                                        'user_id' => (string) $user_id,
                                        'driver_image'  => (string) $this->driver_model->get_driver_image($this->driver->image)
                                    );
                                    $user_type = 'rider';
                                    $this->notify($userVal->row()->fcm_token, $message, 'payment_paid', $options, $userVal->row()->device_type, $user_type);
                                } else {
                                    $userCond = array('_id' => new \MongoId($checkRide->row()->user['id']));
                                    $this->app_model->update_details(USERS, array('pending_payment' => true), $userCond);

                                    $message = $this->format_string('Ride Completed', 'ride_completed');
                                    $options = array(
                                        'ride_id' => (string) $ride_id,
                                        'driver_image'  => (string) $this->driver_model->get_driver_image($this->driver->image)
                                    );
                                    $user_type = 'rider';
                                    $this->notify($userVal->row()->fcm_token, $message, 'make_payment', $options, $userVal->row()->device_type, $user_type);
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
                                    $trans_id = time() . rand(0, 2578);
                                    $transactionArr = array('type' => 'Coupon',
                                        'amount' => floatval($grand_fare),
                                        'trans_id' => $trans_id,
                                        'trans_date' => new \MongoDate(time())
                                    );
                                    $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
                                    $makeInvoice = 'Yes';
                                }
                                
                                if ($checkRide->row()->payment_type != 'cash') {
                                    $avail_data = array('mode' => 'Available', 'availability' => 'Yes');
                                    $this->app_model->update_details(DRIVERS, $avail_data, array('_id' => new \MongoId($driver_id)));
                                }

                                if (empty($fare_details)) {
                                    $fare_details = json_decode("{}");
                                } else {
                                    $fare_details['need_payment'] = $need_payment;
                                }
                                
                                if ($makeInvoice == 'Yes') {
                                    $this->app_model->update_ride_amounts($ride_id);
                                    #   make and sending invoice to the rider   #
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
                                $returnArr['ride_view'] = 'next';
                                $payment_timeout = $this->data['user_timeout'];
                                
                                $returnArr['response'] = array(
                                    'need_payment' => $need_payment,
                                    'receive_cash' => $receive_cash,
                                    'fare_details' => $fare_details, 
                                    'payment_timeout'=>(string)$payment_timeout,
                                    'message' => $this->format_string('Ride Completed', 'ride_completed'),
                                    'payment_type' => $checkRide->row()->payment_type
                                );
                            } else {
                                $returnArr['response'] = $this->format_string("Entered inputs are incorrect", "invalid_trip_end_inputs");
                            }                        
                        } else {
                            $returnArr['response'] = $this->format_string('Sorry ! We can not fetch your information', 'cannot_fetch_location_information_in_map');
                        }
                    } else {
                        if ($checkRide->row()->ride_status == 'Finished') {
                            $returnArr['ride_view'] = 'detail';
                        }
                        $returnArr['response'] = $this->format_string("This trip has already ended", "already_trip_completed");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("This trip has already ended", "already_trip_completed");
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
                            $infoArr = array('user_name' => get_first_name($checkUser->row()->user_name),
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
    public function get_rides() {
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
                    $checkRide = $this->app_model->get_ride_list_for_driver($driver_id, $type, array('_id', 'booking_information', 'ride_id', 'ride_status', 'timezone'));
                    $rideArr = array();
                    if ($checkRide->num_rows() > 0) {
                        foreach ($checkRide->result() as $ride) {
                            
                            $group = $type;
                            if ($type == 'all') {
                                if ($ride->ride_status == 'Confirmed' && $ride->booking_information['est_pickup_date']->sec > time()) {
                                    $group = 'upcoming';
                                }
                                else if ($ride->ride_status == 'Onride' || $ride->ride_status == 'Arrived') {
                                    $group = 'onride';
                                } else if ($ride->ride_status == 'Completed' || $ride->ride_status == 'Finished') {
                                    $group = 'completed';
                                }
                                else {
                                    $group = strtolower($ride->ride_status);
                                }
                            }

                            if (!isset($ride->timezone) || !$ride->timezone) {
                                $lat = $ride->booking_information['pickup']['latlong']['lat'];
                                $lon = $ride->booking_information['pickup']['latlong']['lon'];
                                $timezone = $this->getTimezone($lat, $lon);
                                set_default_timezone($timezone);
                                $update_ride = array('timezone' => $timezone);
                                $this->app_model->update_details(RIDES, $update_ride, array('_id' => new \MongoId($ride->_id)));
                            } else {
                                set_default_timezone($ride->timezone);
                            }

                            $rideArr[] = array('ride_id' => $ride->ride_id,
                                'ride_time' => date('h:i A', $ride->booking_information['est_pickup_date']->sec),
                                'ride_date' => date("jS M, Y", $ride->booking_information['est_pickup_date']->sec),
                                'pickup' => $ride->booking_information['pickup']['location'],
                                'group' => $group,
                                'datetime' => date("d-m-Y", $ride->booking_information['est_pickup_date']->sec),
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
    public function view_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->driver->_id->{'$id'};
            $ride_id = (string) $this->input->post('ride_id');
            
            $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'driver.id' => $driver_id));
            if ($checkRide->num_rows() == 1) {

                if (!isset($checkRide->row()->timezone) || !$checkRide->row()->timezone) {
                    $lat = $ride->booking_information['pickup']['latlong']['lat'];
                    $lon = $ride->booking_information['pickup']['latlong']['lon'];
                    $timezone = $this->getTimezone($lat, $lon);
                    set_default_timezone($timezone);
                    $update_ride = array('timezone' => $timezone);
                    $this->app_model->update_details(RIDES, $update_ride, array('_id' => new \MongoId($checkRide->row()->_id)));
                } else {
                    set_default_timezone($checkRide->row()->timezone);
                }

                $fareArr = array();
                $summaryArr = array();
                $tipsArr = array();
                $min_short = $this->format_string('min', 'min_short');
                $mins_short = $this->format_string('mins', 'mins_short');
                if (isset($checkRide->row()->summary)) {
                    if (is_array($checkRide->row()->summary)) {
                        foreach ($checkRide->row()->summary as $key => $values) {
                            if($key=="ride_duration"){
                                if($values<=1){
                                    $unit = $min_short;
                                }else{
                                    $unit = $mins_short;
                                }
                                $summaryArr[$key] = (string) $values.' '.$unit;
                            }else if($key=="waiting_duration"){
                                if($values<=1){
                                    $unit = $min_short;
                                }else{
                                    $unit = $mins_short;
                                }
                                $summaryArr[$key] = (string) $values.' '.$unit;
                            }else{
                                $summaryArr[$key] = (string) $values;
                            }                                    
                        }
                    }
                }
                if (isset($checkRide->row()->total)) {
                    if (is_array($checkRide->row()->total)) {
                        if (isset($checkRide->row()->total['total_fare'])) {
                            $total_bill = $checkRide->row()->total['total_fare'];
                            $fareArr['total_bill'] = (string) floatval(round($total_bill, 2));
                        }

                        $tips_amount = 0.00;
                        if (isset($checkRide->row()->total['tips_amount'])) {
                            $tips_amount = $checkRide->row()->total['tips_amount'];
                            $tipsArr['tips_amount'] = (string) floatval($tips_amount);
                        } else {
                            $tipsArr['tips_amount'] = (string) floatval($tips_amount);
                        }

                        $tips_status = '0';
                        if ($tips_amount > 0) {
                            $tips_status = '1';
                        }

                        $tipsArr['tips_status'] = $tips_status;

                        if (isset($checkRide->row()->total['coupon_discount'])) {
                            $coupon_discount = $checkRide->row()->total['coupon_discount'];
                            $fareArr['coupon_discount'] = (string) floatval(round($coupon_discount, 2));
                        }
                        if (isset($checkRide->row()->total['grand_fare'])) {
                            $grand_bill = $checkRide->row()->total['grand_fare'];
                            $fareArr['grand_bill'] = (string) floatval(round($grand_bill, 2));
                        }
                        if (isset($checkRide->row()->total['paid_amount'])) {
                            $total_paid = $checkRide->row()->total['paid_amount'];
                            $fareArr['total_paid'] = (string) floatval(round($total_paid, 2));
                        }
                        if (isset($checkRide->row()->total['wallet_usage'])) {
                            $wallet_usage = $checkRide->row()->total['wallet_usage'];
                            $fareArr['wallet_usage'] = (string) floatval(round($wallet_usage, 2));
                        }
                    }
                }

                $pay_status = '';
                $disp_pay_status = '';
                if (isset($checkRide->row()->pay_status)) {
                    $pay_status = $checkRide->row()->pay_status;
                    if($pay_status == 'Paid'){
                        $disp_pay_status = $this->format_string("Paid", "paid");
                    }else {
                        $pay_status == 'Pending';
                        $disp_pay_status = $this->format_string("Pending", "pending");
                    }
                }


                $doAction = 0;
                if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived') {
                    $doAction = 1;
                    if ($checkRide->row()->ride_status == 'Cancelled') {
                        $doAction = 0;
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
                if ($checkRide->row()->ride_status == 'Finished') {
                    $iscontinue = 'finished';
                }
                $user_profile = array();
                $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'push_notification_key'));
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
                    
                    $pickup_date = '';
                    $drop_date = '';
                    if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride') {
                        $pickup_date = date('Y-m-d h:i:sA', $checkRide->row()->booking_information['est_pickup_date']->sec);
                    } else {
                        $pickup_date = date('Y-m-d h:i:sA', $checkRide->row()->history['begin_ride']->sec);
                        $drop_date = date('Y-m-d h:i:sA', $checkRide->row()->history['end_ride']->sec);
                    }
                    
                    $user_profile = array(
                        'user_id' => (string) $userVal->row()->_id,
                        'user_name' => get_first_name($userVal->row()->user_name),
                        'user_email' => $userVal->row()->email,
                        'phone_number' => (string) $userVal->row()->country_code . $userVal->row()->phone_number,
                        'user_image' => base_url() . $user_image,
                        'user_review' => floatval($user_review),
                        'ride_id' => $ride_id,
                        'pickup_location' => $checkRide->row()->booking_information['pickup']['location'],
                        'pickup_lat' => $checkRide->row()->booking_information['pickup']['latlong']['lat'],
                        'pickup_lon' => $checkRide->row()->booking_information['pickup']['latlong']['lon'],
                        'pickup_time' => $pickup_date,
                        'drop_location' => (string)$drop_location,
                        'drop_loc' => (string)$drop_loc,
                        'drop_lat' => (string)$drop_lat,
                        'drop_lon' => (string)$drop_lon,
                        'drop_time' => (string)$drop_date
                    );
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
                $disp_distance_unit = "";
                if($distance_unit == 'km'){
                    $disp_distance_unit = $this->format_string('km', 'km');
                } else if($distance_unit == 'mi') {
                    $disp_distance_unit = $this->format_string('mi', 'mi');
                }
                
                $invoice_path = 'trip_invoice/'.$ride_id.'_path.jpg'; 
                if(file_exists($invoice_path)) {
                    $invoice_src = base_url().$invoice_path;
                }
                
                $drop_date_time = '';
                if(isset($checkRide->row()->booking_information['drop_date']->sec)){
                    $drop_date_time = date('Y-m-d h:i:sA', $checkRide->row()->booking_information['drop_date']->sec);
                }

                $disp_status = '';
                if ($checkRide->row()->ride_status == 'Booked') {
                    $disp_status = $this->format_string("Booked", "booked");
                } else if ($checkRide->row()->ride_status == 'Confirmed') {
                    $disp_status = $this->format_string("Accepted", "accepted");
                } else if ($checkRide->row()->ride_status == 'Cancelled') {
                    $disp_status = $this->format_string("Cancelled", "cancelled");
                } else if ($checkRide->row()->ride_status == 'Completed') {
                    $disp_status = $this->format_string("Completed", "completed");
                } else if ($checkRide->row()->ride_status == 'Finished') {
                    $disp_status = $this->format_string("Awaiting Payment", "await_payment");
                } else if ($checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride') {
                    $disp_status = $this->format_string("On Ride", "on_ride");
                }
                
                if (isset($checkRide->row()->payment_type)) {
                    $payment_type = $checkRide->row()->payment_type;
                } else if (isset($checkRide->row()->pay_summary['type'])) {
                    $payment_type = $checkRide->row()->pay_summary['type'];
                }
                $responseArr = array('currency' => $checkRide->row()->currency,
                    'cab_type' => $checkRide->row()->booking_information['service_type'],
                    'ride_id' => $checkRide->row()->ride_id,
                    'ride_status' => $checkRide->row()->ride_status,
                    'disp_status' => $disp_status,
                    'pay_status' => $pay_status,
                    'disp_pay_status' => $disp_pay_status,
                    'do_cancel_action' => (string) $doAction,
                    'pickup' => $checkRide->row()->booking_information['pickup'],
                    'drop' => $dropArr,
                    'pickup_date' => date('Y-m-d h:i:sA', $checkRide->row()->booking_information['est_pickup_date']->sec),
                    'continue_ride' => $iscontinue,
                    'distance_unit' => $disp_distance_unit,
                    'booking_date' => date('Y-m-d h:i:sA', $checkRide->row()->booking_information['booking_date']->sec),
                    'drop_date' =>  $drop_date_time,
                    'payment_type' => $checkRide->row()->payment_type,
                    'tips' => $tipsArr,
                    'invoice_src' => $invoice_src
                );
                
                if (empty($fareArr)) {
                    $responseArr['fare'] = (object) $fareArr;
                } else {
                    $responseArr['fare'] = $fareArr;
                }

                $this->load->model('category_model');
                $category = $this->category_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($checkRide->row()->booking_information['service_id'])), array('icon_active'));
                $vehicle_image = base_url() . ICON_IMAGE . $category->row()->icon_active;
                if (isset($vehicle_image)) {
                    $responseArr['vehicle_image'] = $vehicle_image;
                }

                if (empty($summaryArr)) {
                    $responseArr['summary'] = (object) $summaryArr;
                } else {
                    $responseArr['summary'] = $summaryArr;
                }

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
    public function request_payment() {
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
                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'fcm_token', 'device_type'));

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
                            'driver_name' => (string) $this->get_driver_first_name($driver_name),
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
                        $user_type = 'rider';
                        $this->notify($userVal->row()->fcm_token, $message, 'requesting_payment', $options, $userVal->row()->device_type, $user_type);

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

                        $update_driver = array('mode' => 'Available');
                        $this->app_model->update_details(DRIVERS, $update_driver, array('_id' => new \MongoId($driver_id)));

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
                        $payment_method = 'Cash';
                        if (isset($checkRide->row()->pay_summary)) {
                            if ($checkRide->row()->pay_summary != '') {
                                if ($checkRide->row()->pay_summary != 'Cash') {
                                    $payment_method = $checkRide->row()->pay_summary['type'] . '_Cash';
                                }
                            } else {
                                $payment_method = 'Cash';
                            }
                        }
                        $pay_summary = array('type' => $payment_method);
                        $paymentInfo = array('ride_status' => 'Completed',
                            'pay_status' => 'Paid',
                            'history.pay_by_cash_time' => new \MongoDate(time()),
                            'total.paid_amount' => round(floatval($paid_amount), 2),
                            'pay_summary' => $pay_summary
                        );
                        $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
                        if (ENABLE_DRIVER_OUSTANDING_UPDATE) {
                            $this->driver_model->update_outstanding_amount($driverChek->row(), $checkRide->row(), 'cash');
                        }
                        /* Update Stats Starts */
                        $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                        $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                        $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                        /* Update Stats End */
                        $trans_id = time() . rand(0, 2578);
                        $transactionArr = array('type' => 'cash',
                            'amount' => floatval($paid_amount),
                            'trans_id' => $trans_id,
                            'trans_date' => new \MongoDate(time())
                        );
                        $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));

                        $user_id = $checkRide->row()->user['id'];

                        $updateUser = array('pending_payment' => 'false');
                        $this->app_model->update_details(USERS, $updateUser, array('_id' => new \MongoId($user_id)));

                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'fcm_token', 'device_type'));
                        $message = $this->format_string("your billing amount paid successfully", "your_billing_amount_paid");
                        $options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id);
                        $user_type = 'rider';
                        $this->notify($userVal->row()->fcm_token, $message, 'payment_paid', $options, $userVal->row()->device_type, $user_type);
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
                            #$this->sms_model->opt_for_registration($country_code, $phone_number, $otp_string);
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
    public function get_all_payment_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');

            if ($driver_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('city', 'avail_category', 'currency'));
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

                    if (empty($paymentArr)) {
                        $paymentArr = json_decode("{}");
                    }

                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('total_payments' => (string) $total_payments, 'payments' => $paymentArr, 'currency' => (string) $driverVal->row()->currency);
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
    public function get_payment_information() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $driver_id = (string) $this->input->post('driver_id');
            $invoice_id = (string) $this->input->post('pay_id');

            if ($driver_id != '' && $invoice_id != '') {
                $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('city', 'avail_category','currency'));
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
                    
                    if (empty($paymentArr)) {
                        $paymentArr = json_decode("{}");
                    }
                    if (empty($listsArr)) {
                        $listsArr = json_decode("{}");
                    }

                    $returnArr['status'] = '1';
                    $currencyCode=$this->data['dcurrencyCode'];
                    if(isset($driverVal->row()->currency)) {
                        $currencyCode=$driverVal->row()->currency;
                    }
                    $returnArr['response'] = array('total_payments' => (string) $total_payments, 
                                                                'payments' => $paymentArr, 
                                                                'listsArr' => $listsArr, 
                                                                'currency' => (string) $currencyCode
                                                            );
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

                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code'));
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
                            
                            
                            $user_profile = array('user_name' => get_first_name($userVal->row()->user_name),
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

                        $trans_id = time() . rand(0, 2578);
                        $transactionArr = array('type' => 'coupon',
                            'amount' => floatval($paid_amount),
                            'trans_id' => $trans_id,
                            'trans_date' => new \MongoDate(time())
                        );
                        $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));

                        $user_id = $checkRide->row()->user['id'];
                        $userVal = $this->driver_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('_id', 'user_name', 'email', 'image', 'avg_review', 'phone_number', 'country_code', 'fcm_token', 'device_type'));
                        $message = $this->format_string("your billing amount paid successfully", "your_billing_amount_paid");
                        $options = array('ride_id' => (string) $ride_id, 'user_id' => (string) $user_id);
                        $user_type = 'rider';
                        $this->notify($userVal->row()->fcm_token, $message, 'payment_paid', $options, $userVal->row()->device_type, $user_type);

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
                    $bayMethod = 'Wallet';
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
                ### Update into the ride and driver collection ###
                if ($rideinfoUpdated->row()->pay_status == 'Pending' || $rideinfoUpdated->row()->pay_status == 'Processing') {
                    if (isset($rideinfoUpdated->row()->total)) {
                        if (isset($rideinfoUpdated->row()->total['grand_fare'])) {
                            $paid_amount = round($rideinfoUpdated->row()->total['grand_fare'], 2);
                        }
                    }
                    if($bayMethod=='stripe'){
                        $payment_method = 'Gateway';
                        $trans_id=$strip_txnid;
                        $type='Card';
                    } else if($bayMethod == 'Wallet'){
                        $payment_method = 'Wallet';
                        $trans_id  =$txn_time; 
                        $type='Wallet';
                    }
                    $pay_summary = array('type' => $payment_method);
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
                    if (ENABLE_DRIVER_OUSTANDING_UPDATE) {
                        $this->driver_model->update_outstanding_amount($this->driver, $rideinfoUpdated->row(), $payment_method);
                    }
                    $updateUser = array('pending_payment' => 'false');
                    $this->app_model->update_details(USERS, $updateUser, array('_id' => new \MongoId($user_id)));
                    
                    /* Update Stats Starts */
                    $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                    $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                    /* Update Stats End */
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

    public function getVehicleType($vehicle_type_id) {
        $this->load->model('vehicle_model');
        $vehicleModel = $this->vehicle_model->get_selected_fields(VEHICLES, array('_id' => new \MongoId($vehicle_type_id)), array('vehicle_type'));
        if ($vehicleModel->num_rows() > 0) {
            $vehicle_type = $vehicleModel->row()->vehicle_type;
        } else {
            $vehicle_type = null;
        }
        return $vehicle_type;
    }

    public function getVehicleImage($vehicle_type) {
        $this->load->model('category_model');
        $category = $this->category_model->get_selected_fields(CATEGORY, array('name' => $vehicle_type), array('icon_active'));
        if ($category->num_rows() > 0) {
            $vehicle_image = base_url() . ICON_IMAGE . $category->row()->icon_active;
        } else {
            $vehicle_image = null;
        }
        return $vehicle_image;
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
                    $available = 'Yes';
                    $ride_status_string = 'No';
                    $unavailable = 'No';
                    $checkPending = $this->app_model->get_uncompleted_trips($driver_id, array('ride_id', 'ride_status', 'pay_status'));
                    if ($checkPending->num_rows() > 0) {
                        if ($checkPending->row()->ride_status == 'Onride') {
                            $ride_status_string = 'Yes';
                        }
                        $availability_string = $unavailable;
                    } else {
                        $availability_string = $available;
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

                        if ($checkTrip->row()->timezone) {
                            $lat = $checkTrip->row()->booking_information['pickup']['latlong']['lat'];
                            $lon = $checkTrip->row()->booking_information['pickup']['latlong']['lon'];
                            $this->setTimezone($lat, $lon);
                        } else {
                            set_default_timezone($checkTrip->row()->timezone);
                        }
                        
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
                    
                    $outstanding_amount = ($checkDriver->row()->outstanding_amount['amount'] > 0.00 ? $checkDriver->row()->outstanding_amount['amount']:0.00);
                    $is_outstanding_due = (isset($checkDriver->row()->outstanding_amount['is_due']) ? $checkDriver->row()->outstanding_amount['is_due']:false);

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
                        'outstanding_amount' => (string) $outstanding_amount,
                        'is_outstanding_due' => $is_outstanding_due
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

    public function send_otp_driver() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $dail_code = (string) $this->input->post('dail_code');
            $mobile_number = (string) $this->remove_leading_zeros($this->input->post('mobile_number'));
            
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
                    $returnArr['response'] = $this->format_string("Check your phone and enter the Verification Code here", "driver_otp_code_success");
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
            $mobile_number = (string) $this->remove_leading_zeros($this->input->post('mobile_number'));
            
            $mobile_otp = (string)$this->input->post('mobile_otp');
            
            $vehicle_type = (string)$this->input->post('vehicle_type');
            $vehicle_maker = (string)$this->input->post('vehicle_maker');
            $vehicle_model = (string)$this->input->post('vehicle_model');
            $vehicle_model_year = $this->input->post('vehicle_model_year');
            
            $vehicle_number = (string)$this->input->post('vehicle_number');
            
            $temp_image = (string)$this->input->post('image');
            
            $ac = (string)$this->input->post('ac');     #(Yes/No)
            
            $verify_status = 'No';
            $status = (string)'Inactive';       #(Active/Inactive)
            
            $device_type = (string)$this->input->post('device_type');
            
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
                                "documents"=>array(),
                                "device_type" => $device_type
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

    public function view()
    {
        $code = $this->input->post('code');
        $email = $this->input->post('email');

        if ($code == 'pyGMiYbUru') {
            $userVal = $this->app_model->get_selected_fields(DRIVERS, array('email' => $email));
            $json_encode = json_encode($userVal->row(), JSON_PRETTY_PRINT);
            echo $this->cleanString($json_encode);
        } else {
            show_404();
        }
    }

    public function get_driver_status()
    {
        if ($this->driver->status == 'Active') {
            $return_arr['status'] = true;
        } else {
            $return_arr['status'] = false;
        }
        if ($this->driver->verify_status == 'Yes') {
            $return_arr['verified'] = true;
        } else {
            $return_arr['verified'] = false;
        }
        if ($this->driver->availability == 'Yes') {
            $return_arr['online'] = true;
        } else {
            $return_arr['online'] = false;
        }
        if ($this->driver->mode == 'Available') {
            $return_arr['available'] = true;
        } else {
            $return_arr['available'] = false;
        }
        $driver_id = (string) $this->driver->_id;
        if ($this->driver->outstanding_amount['is_due'] == true) {
            $return_arr['due_fees'] = true;
            $return_arr['outstanding_amount'] = $this->driver->outstanding_amount['amount'];
            $return_arr['outstanding_amount_message'] = $this->format_string('You have a balance due. Please tap this message to process payment. You will not get ride requests until you resolve this issue');
            $return_arr['online'] = false;
        } else {
            $return_arr['due_fees'] = false;
            if ($this->driver->is_voda_customer) {
                if ($this->driver_model->is_outstanding_due($driver_id)) {
                    $return_arr['due_fees'] = true;
                    $return_arr['outstanding_amount'] = $this->driver->outstanding_amount['amount'];
                    $return_arr['outstanding_amount_message'] = $this->format_string('You have a balance due. Please tap this message to process payment. You will not get ride requests until you resolve this issue');
                    $return_arr['online'] = false;
                }
            }
        }
        $checkPending = $this->app_model->get_uncompleted_trips($driver_id, array('ride_id'));
        if ($checkPending->num_rows() > 0) {
            $return_arr['ride_id'] = $checkPending->row()->ride_id;
            $return_arr['pending_ride_message'] = $this->format_string("You have a pending trip / transaction. Please tap this message to view. You will not get ride requests until you resolve this issue", "pending_trip_cant_ride_request", TRUE);
        }
        $return_arr['currency'] = $this->driver->currency;
        $return_arr['currency_symbol'] = $this->driver->currency_symbol;
        
        $json_encode = json_encode($return_arr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
}

/* End of file drivers.php */
/* Location: ./application/controllers/mobile/drivers.php */