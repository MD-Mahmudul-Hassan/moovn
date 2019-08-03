<?php 

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
* 
* User related functions
* @author Casperon
*
* */
class User extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email','ride_helper', 'string'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');
        $this->load->model('app_model');
        $this->load->model('dynamic_driver');
        $this->load->model('rides_model');
        $returnArr = array();

        /* Authentication Begin */
        $headers = $this->input->request_headers();
        header('Content-type:application/json;charset=utf-8');
        $current_function = $this->router->fetch_method();
        $public_functions = array('create_account', 'check_social_login', 'register', 'social_Login', 'login', 'view');
        if (array_key_exists("User-Token", $headers)) {
            $this->user_token = $headers['User-Token'];
            try {
                if (isset($this->user_token) && $this->user_token != '') {
                    $user = $this->app_model->get_selected_fields(USERS, array('login_token' => $this->user_token));
                    if ($user->num_rows() <= 0) {
                        echo json_encode(array("is_dead"=>"Yes")); die;
                    } else {
                        $this->user = $user->row();
                    }
                }
            } catch (MongoException $ex) {
                echo $ex->getMessage(); die;
            }
        } else if (!in_array($current_function, $public_functions)) {
            show_404();
        }
        /* Authentication End */
    }

    public function index() {
        echo '<h2 style="text-align:center; margin-top:20%;">Welcome To Dectarfortaxi</h2>';
    }

    /**
     *
     * This function creates a new account for user
     *
     * */
    public function create_account() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $user_name = $this->input->post('user_name');
            $country_code = $this->input->post('country_code');
            $phone_number = $this->remove_leading_zeros($this->input->post('phone_number'));
            $referal_code = $this->input->post('referal_code');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 5) {
                if (valid_email($email)) {
                    $user_exist = $this->user_model->check_user_exist(array('email' => $email));
                    if ($user_exist->num_rows() >= 1) {
                        if ($user_exist->row()->status != "Active") {
                            $returnArr['message'] = $this->format_string("Your account is currenty unavailable", "account_currently_unavailbale");
                        } else {
                            $returnArr['message'] = $this->format_string('Email address already exists', 'email_already_exist');
                        }
                    } else {
                        $condition = array('country_code' => $country_code, 'phone_number' => $phone_number);
                        $user = $this->user_model->get_selected_fields(USERS, $condition, array('_id'));
                        if ($user->num_rows() == 0) {
                            $cStatus = false;
                            if ($referal_code != '') {
                                $chekCode = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('_id'));
                                if ($chekCode->num_rows() > 0) {
                                    $cStatus = true;
                                }
                            } else {
                                $cStatus = true;
                            }
                            if ($cStatus) {
                                $otp_string = $this->user_model->get_random_string(6);
                                $otp_status = "development";
                                if ($this->config->item('twilio_account_type') == 'prod') {
                                    $otp_status = "production";
                                    $this->sms_model->opt_for_registration($country_code, $phone_number, $otp_string);
                                }
                                $returnArr['message'] = $this->format_string('Success', 'success');
                                $returnArr['user_name'] = $user_name;
                                $returnArr['email'] = $email;
                                $returnArr['country_code'] = $country_code;
                                $returnArr['phone_number'] = $phone_number;
                                $returnArr['referal_code'] = $referal_code;
                                $returnArr['otp_status'] = (string) $otp_status;
                                $returnArr['otp'] = (string) $otp_string;
                                $returnArr['status'] = '1';
                            } else {
                                $returnArr['message'] = $this->format_string('Invalid referral code', 'invalid_referral_code');
                            }
                        } else {
                            $returnArr['message'] = $this->format_string('This mobile number already registered', 'mobile_number_already_registered');
                        }
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function creates a new account for user
     *
     * */
    public function check_social_login() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $media_id = $this->input->post('media_id');
            $fcm_token = $this->input->post('fcm_token');
            $device_type = $this->input->post('device_type');
            $email = $this->input->post('email');
 
            if ($media_id != "") {
                $condition = array('media_id' => $media_id);
                $checkUser = $this->user_model->get_all_details(USERS, $condition);
                
                if($checkUser->num_rows() == 0 && $email != ''){
                    $condition = array('email' => $email);
                    $checkUser = $this->user_model->get_all_details(USERS, $condition);
                }
        
                if ($checkUser->num_rows() == 1) {
                    if ($checkUser->row()->status == "Active") {
                        $push_data = array();
                        $login_token = $this->generate_random_string();
                        $push_data = array(
                            'fcm_token' => $fcm_token,
                            'login_token' => $login_token,
                            'device_type'   =>  $device_type
                        );
                        if (!empty($push_data)) {
                            $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($checkUser->row()->_id)));
                        }

                        $returnArr['status'] = '1';
                        $returnArr['message'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
                        $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkUser->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'unique_code','currency'));
                        if ($userVal->row()->image == '') {
                            $user_image = USER_PROFILE_IMAGE_DEFAULT;
                        } else {
                            $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                        }
                        $returnArr['user_image'] = base_url() . $user_image;
                        $returnArr['user_id'] = (string) $checkUser->row()->_id;
                        $returnArr['user_name'] = $userVal->row()->user_name;
                        $returnArr['email'] = $userVal->row()->email;
                        $returnArr['country_code'] = $userVal->row()->country_code;
                        $returnArr['phone_number'] = $userVal->row()->phone_number;
                        $returnArr['sec_key'] = md5((string) $checkUser->row()->_id);
                        $returnArr['referal_code'] = $userVal->row()->unique_code;
                        $returnArr['login_token'] = $login_token;

                        $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($checkUser->row()->_id)), array('total'));
                        $avail_amount = 0;
                        if (isset($walletDetail->row()->total)) {
                            $avail_amount = $walletDetail->row()->total;
                        }
                        $returnArr['wallet_amount'] = (string) round($avail_amount,2);
                        $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                        
                        if(isset($userVal->row()->currency)) {
                            $returnArr['currency'] = (string)$userVal->row()->currency;
                        }
                        
                        $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('name'));
                        $category = '';
                        if ($categoryResult->num_rows() > 0) {
                            $category = $categoryResult->row()->_id;
                        }
                        $returnArr['category'] = (string) $category;
                    } else {
                        if ($checkUser->row()->status == "Deleted") {
                            $returnArr['message'] = $this->format_string("Your account is currently unavailable", "account_currently_unavailbale");
                        } else {
                            $returnArr['message'] = $this->format_string("Your account has been inactivated", "your_account_inactivated");
                        }
                    }
                } else {
                    $returnArr['status'] = '2';
                    $returnArr['message'] = $this->format_string("Continue Signup Process", "continue_signup_process");
                }
            } else {
                $returnArr['message'] = $this->format_string("Authentication Failed", "authentication_failed");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function creates a new account for user
     *
     * */
    public function register() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $user_name = $this->input->post('user_name');
            $country_code = $this->input->post('country_code');
            $phone_number = $this->remove_leading_zeros($this->input->post('phone_number'));
            $referal_code = $this->input->post('referal_code');
            $fcm_token = $this->input->post('fcm_token');            
            $dcountry_code = $this->input->post('dcountry_code');
            $device_type = $this->input->post('device_type');
            $longitude = $this->input->post('longitude');
            $latitude = $this->input->post('latitude');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 5) {
                if (valid_email($email)) {
                    $checkEmail = $this->user_model->check_user_exist(array('email' => $email));
                    if ($checkEmail->num_rows() >= 1) {
                        $returnArr['message'] = $this->format_string('Email address already exists', 'email_already_exist');
                    } else {
                        $cStatus = FALSE;
                        if ($referal_code != '') {
                            $chekCode = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('_id','country_code'));
                            if ($chekCode->num_rows() > 0) {
                                if($chekCode->row()->country_code==$country_code){
                                    $cStatus = TRUE;
                                }
                            }
                        } else {
                            $cStatus = TRUE;
                        }
                        if ($cStatus) {
                            
                            $currencyCode = $this->data['dcurrencyCode'];
                            $currencySymbol = $this->data['dcurrencySymbol'];
                            if($dcountry_code != ''){
                                $getCurrency = $this->app_model->get_all_details(COUNTRY, array('cca2' => $dcountry_code));
                                if($getCurrency->num_rows() > 0 && isset($getCurrency->row()->currency_code)){
                                    if($getCurrency->row()->currency_code != ''){
                                        $currencyCode  = $getCurrency->row()->currency_code;
                                        $currencySymbol  = $getCurrency->row()->currency_symbol;
                                        if($currencySymbol == '') $currencySymbol = $currencyCode;
                                    }
                                }
                            }
                            
                            $verification_code = $this->get_rand_str('10');
                            $unique_code = $this->app_model->get_unique_id($user_name);

                            $isVodaCustomer = $this->is_voda_customer($country_code, $phone_number);

                            $user_data = array('user_name' => $user_name,
                                'user_type' => 'Normal',
                                'unique_code' => $unique_code,
                                'email' => $email,
                                'password' => md5($password),
                                'image' => '',
                                'status' => 'Active',
                                'country_code' => $country_code,
                                'phone_number' => $phone_number,
                                'referral_code' => $referal_code,
                                'verification_code' => array("email" => $verification_code),
                                'created' => date("Y-m-d H:i:s"),
                                'currency' => $currencyCode,
                                'currency_symbol' => $currencySymbol,
                                'is_voda_customer' => $isVodaCustomer
                            );
                            $this->user_model->insert_user($user_data);
                            $last_insert_id = $this->cimongo->insert_id();
                            if ($last_insert_id != '') {
                                $push_data = array();
                                $login_token = $this->generate_random_string();
                                $push_data = array(
                                    'fcm_token' => $fcm_token,
                                    'login_token' => $login_token,
                                    'device_type' => $device_type
                                );
                                if (!empty($push_data)) {
                                    $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($last_insert_id)));
                                }

                                $returnArr['message'] = $this->format_string('Successfully registered', 'successfully_registered');
                                $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($last_insert_id)), array('image', 'password','currency'));
                                if ($userVal->row()->image == '') {
                                    $user_image = USER_PROFILE_IMAGE_DEFAULT;
                                } else {
                                    $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                                }
                                $returnArr['user_image'] = base_url() . $user_image;
                                $returnArr['user_id'] = (string) $last_insert_id;
                                $returnArr['user_name'] = $user_name;
                                $returnArr['email'] = $email;
                                $returnArr['country_code'] = $country_code;
                                $returnArr['phone_number'] = $phone_number;
                                $returnArr['referal_code'] = $unique_code;
                                $returnArr['sec_key'] = md5((string) $last_insert_id);
                                $returnArr['login_token'] = $login_token;
                                $returnArr['status'] = '1';
                                $returnArr['is_voda_customer'] = ($isVodaCustomer ? true:false);

                                $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('name'));
                                $category = '';
                                if ($categoryResult->num_rows() > 0) {
                                    $category = $categoryResult->row()->_id;
                                }
                                $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                                if(isset($userVal->row()->currency)) {
                                    $returnArr['currency'] = (string)$userVal->row()->currency;
                                }
                                $returnArr['category'] = (string) $category;

                                /* Insert Referal and wallet collection */
                                $this->user_model->simple_insert(REFER_HISTORY, array('user_id' => new \MongoId($last_insert_id)));
                                $this->user_model->simple_insert(WALLET, array('user_id' => new \MongoId($last_insert_id), 'total' => floatval(0)));

                                /* Update the welcome amount to the registered user wallet */
                                $this->user_model->add_welcome_amount($country_code, $currencyCode, $last_insert_id);

                                /* Update the referer history */
                                if ($referal_code != '') {
                                    $this->user_model->add_referral_credit($referal_code, $last_insert_id, $email, $currencyCode);
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
                                $returnArr['wallet_amount'] = (string) round($avail_amount,2);

                                /* Sending Mail notification about registration */
                                $this->mail_model->send_user_registration_mail($last_insert_id);
                            } else {
                                $returnArr['message'] = $this->format_string('Registration Failure', 'registration_failed');
                            }
                        } else {
                            $returnArr['response'] = $this->format_string('Invalid referral code', 'invalid_referral_code');
                        }
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
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
    public function social_Login() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = strtolower($this->input->post('email'));
            $user_name = $this->input->post('user_name');
            $country_code = $this->input->post('country_code');
            $phone_number = $this->remove_leading_zeros($this->input->post('phone_number'));
            $referal_code = $this->input->post('referal_code');
            $fcm_token = $this->input->post('fcm_token');
            $media = $this->input->post('media');
            $media_id = $this->input->post('media_id');
            $password = $this->input->post('password');
            
            $dcountry_code = $this->input->post('dcountry_code');
            $device_type = $this->input->post('device_type');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 6) {
                if (valid_email($email)) {
                    $currencyCode = $this->data['dcurrencyCode'];
                    $currencySymbol = $this->data['dcurrencySymbol'];
                    if($dcountry_code != ''){
                        $getCurrency = $this->app_model->get_all_details(COUNTRY, array('cca2' => $dcountry_code));
                        if($getCurrency->num_rows() > 0 && isset($getCurrency->row()->currency_code)){
                            if($getCurrency->row()->currency_code != ''){
                                $currencyCode  = $getCurrency->row()->currency_code;
                                $currencySymbol  = $getCurrency->row()->currency_symbol;
                                if($currencySymbol == '') $currencySymbol = $currencyCode;
                            }
                        }
                    }
                    $checkEmail = $this->user_model->check_user_exist(array('email' => $email));
                    if ($checkEmail->num_rows() >= 1) {
                        $push_data = array();
                        $login_token = $this->generate_random_string();
                        $push_data = array(
                            'fcm_token'         =>  $fcm_token,
                            'login_token'       =>  $login_token,
                            'device_type'       =>  $device_type
                        );
                        if (!empty($push_data)) {
                            $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($checkEmail->row()->_id)));
                        }

                        $returnArr['status'] = '1';
                        $returnArr['message'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
                        $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkEmail->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'unique_code', 'currency', 'is_voda_customer'));
                        if ($userVal->row()->image == '') {
                            $user_image = USER_PROFILE_IMAGE_DEFAULT;
                        } else {
                            $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                        }
                        $unique_code = $userVal->row()->unique_code;
                        $returnArr['user_image'] = base_url() . $user_image;
                        $returnArr['user_id'] = (string) $checkEmail->row()->_id;
                        $returnArr['user_name'] = $userVal->row()->user_name;
                        $returnArr['email'] = $userVal->row()->email;
                        $returnArr['country_code'] = $userVal->row()->country_code;
                        $returnArr['phone_number'] = $userVal->row()->phone_number;
                        $returnArr['referal_code'] = $unique_code;
                        $returnArr['sec_key'] = md5((string) $checkEmail->row()->_id);
                        $returnArr['login_token'] = $login_token;
                        $returnArr['is_voda_customer'] = $userVal->row()->is_voda_customer ? true:false;

                        $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('name'));
                        $category = '';
                        if ($categoryResult->num_rows() > 0) {
                            $category = $categoryResult->row()->_id;
                        }
                        $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                        if(isset($userVal->row()->currency)) {
                            $returnArr['currency'] = (string)$userVal->row()->currency;
                        }
                        $returnArr['category'] = (string) $category;

                        $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($checkEmail->row()->_id)), array('total'));
                        $avail_amount = 0;
                        if (isset($walletDetail->row()->total)) {
                            $avail_amount = $walletDetail->row()->total;
                        }
                        $returnArr['wallet_amount'] = (string) round($avail_amount,2);
                    } else {
                        $cStatus = FALSE;
                        if ($referal_code != '') {
                            $chekCode = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('_id','currency'));
                            if($chekCode->row()->currency==$currencyCode){
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
                            $isVodaCustomer = $this->is_voda_customer($country_code, $phone_number);
                            $user_data = array('user_name' => $user_name,
                                'user_type' => $media,
                                'media_id' => (string) $media_id,
                                'unique_code' => $unique_code,
                                'email' => $email,
                                'password' => md5($password),
                                'image' => $user_image,
                                'status' => 'Active',
                                'country_code' => $country_code,
                                'phone_number' => $phone_number,
                                'referral_code' => $referal_code,
                                'verification_code' => array("email" => $verification_code),
                                'created' => date("Y-m-d H:i:s"),
                                'currency' => $currencyCode,
                                'currency_symbol' => $currencySymbol,
                                'is_voda_customer'  =>  $isVodaCustomer
                            );
                            $this->user_model->insert_user($user_data);
                            $last_insert_id = $this->cimongo->insert_id();
                            if ($last_insert_id != '') {
                                $push_data = array();
                                $login_token = $this->generate_random_string();
                                $push_data = array(
                                    'fcm_token' => $fcm_token,
                                    'login_token' => $login_token
                                );
                                if (!empty($push_data)) {
                                    $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($last_insert_id)));
                                }

                                /* Insert Referal and wallet collection */
                                $this->user_model->simple_insert(REFER_HISTORY, array('user_id' => new \MongoId($last_insert_id)));
                                $this->user_model->simple_insert(WALLET, array('user_id' => new \MongoId($last_insert_id), 'total' => floatval(0)));

                                /* Update the welcome amount to the registered user wallet */
                                $this->user_model->add_welcome_amount($country_code, $currencyCode, $last_insert_id);

                                /* Update the referer history */
                                if ($referal_code != '') {
                                    $this->user_model->add_referral_credit($referal_code, $last_insert_id, $email, $currencyCode);
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
                                $returnArr['wallet_amount'] = (string) round($avail_amount,2);

                                /* Sending Mail notification about registration */
                                $this->mail_model->send_user_registration_mail($last_insert_id);

                                $returnArr['message'] = $this->format_string('Successfully registered', 'successfully_registered');
                                $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($last_insert_id)), array('image','currency'));
                                if ($userVal->row()->image == '') {
                                    $user_image = USER_PROFILE_IMAGE_DEFAULT;
                                } else {
                                    $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                                }
                                $returnArr['user_image'] = base_url() . $user_image;
                                $returnArr['user_id'] = (string) $last_insert_id;
                                $returnArr['user_name'] = $user_name;
                                $returnArr['email'] = $email;
                                $returnArr['country_code'] = $country_code;
                                $returnArr['phone_number'] = $phone_number;
                                $returnArr['referal_code'] = $unique_code;
                                $returnArr['login_token'] = $login_token;
                                $returnArr['status'] = '1';
                                $returnArr['is_voda_customer'] = $isVodaCustomer ? true:false;

                                $fields = array(
                                    'username' => $last_insert_id,
                                    'password' => md5($last_insert_id)
                                );
                                $url = $this->data['soc_url'] . 'create-user.php';
                                $this->load->library('curl');
                                $output = $this->curl->simple_post($url, $fields);

                                $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('name'));
                                $category = '';
                                if ($categoryResult->num_rows() > 0) {
                                    $category = $categoryResult->row()->_id;
                                }
                                $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                                if(isset($userVal->row()->currency)) {
                                    $returnArr['currency'] = (string)$userVal->row()->currency;
                                }
                                $returnArr['category'] = (string) $category;
                            } else {
                                $returnArr['message'] = $this->format_string('Registration Failure', 'registration_failed');
                            }
                        } else {
                            $returnArr['message'] = $this->format_string('Invalid referral code', 'invalid_referral_code');
                        }
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Login User 
     *
     * */
    public function login() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $fcm_token = $this->input->post('fcm_token');
            $device_type = $this->input->post('device_type');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 3) {
                if (valid_email($email)) {
                    $checkUser = $this->user_model->get_selected_fields(USERS, array('email' => $email, 'password' => md5($password)), array('email', 'user_name', 'phone_number', 'status', 'fcm_token', 'is_voda_customer', 'default_payment_method', 'lang_code'));
                    if ($checkUser->num_rows() == 1) {
                        if ($checkUser->row()->status == "Active") {
                            
                            $login_token = $this->generate_random_string();
                            $push_data = array(
                                'fcm_token' => $fcm_token,
                                'login_token' => $login_token,
                                'device_type' => $device_type
                            );
                            
                            if (!empty($push_data)) {
                                $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($checkUser->row()->_id)));
                            } else {
                                $returnArr['message'] = 'could not save fcm token and login token';
                                $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
                                echo $this->cleanString($json_encode);
                                die();
                            }

                            $returnArr['status'] = '1';
                            $returnArr['message'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
                            $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkUser->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'referral_code','currency', 'pending_payment'));
                            if ($userVal->row()->image == '') {
                                $user_image = USER_PROFILE_IMAGE_DEFAULT;
                            } else {
                                $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                            }
                            $returnArr['user_image'] = base_url() . $user_image;
                            $returnArr['user_id'] = (string) $checkUser->row()->_id;
                            $returnArr['user_name'] = $userVal->row()->user_name;
                            $returnArr['email'] = $userVal->row()->email;
                            $returnArr['country_code'] = $userVal->row()->country_code;
                            $returnArr['phone_number'] = $userVal->row()->phone_number;
                            $returnArr['referal_code'] = $userVal->row()->referral_code;
                            $returnArr['sec_key'] = md5((string) $checkUser->row()->_id);
                            $returnArr['pending_payment'] = ($userVal->row()->pending_payment ? 'true':'false');
                            $returnArr['login_token'] = $login_token;

                            $returnArr['min_book_ahead_time'] = $this->config->item('min_book_ahead_time');
                            $returnArr['max_book_ahead_time'] = $this->config->item('max_book_ahead_time');
                            $returnArr['calendar_rider_reminder'] = $this->config->item('rider_reminder');

                            if ($checkUser->row()->is_voda_customer !== true) {
                                $isVodaCustomer = $this->is_voda_customer($userVal->row()->country_code, $userVal->row()->phone_number);
                                // for old users who were not checked whether they were vodacom users while registering
                                if ($isVodaCustomer) {
                                    $userCond = array('_id' => new \MongoId($checkUser->row()->_id));
                                    $this->user_model->update_details(USERS, array('is_voda_customer' => $isVodaCustomer), $userCond);
                                }
                            }
                            else {
                                $isVodaCustomer = true;
                            }
                            $returnArr['is_voda_customer'] = $isVodaCustomer;

                            $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($checkUser->row()->_id)), array('total'));
                            $avail_amount = 0;
                            if (isset($walletDetail->row()->total)) {
                                $avail_amount = $walletDetail->row()->total;
                            }

                            if(isset($checkUser->row()->lang_code)){
                                $returnArr['lang_code'] = $checkUser->row()->lang_code;
                            }else{
                                $returnArr['lang_code'] = "en";
                            }

                            $returnArr['wallet_amount'] = (string) round($avail_amount,2);
                            $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                            if(isset($userVal->row()->currency)) {
                                $returnArr['currency'] = (string)$userVal->row()->currency;
                            }

                            $returnArr['default_payment_method'] = $checkUser->row()->default_payment_method;

                            if ($walletDetail->row()->total < $this->config->item('low_wallet_balance')) {
                                $returnArr['low_wallet_balance'] = "true";
                            } else {
                                $returnArr['low_wallet_balance'] = "false";
                            }

                            $returnArr['required_wallet_balance'] = (string) $this->config->item('low_wallet_balance');

                            $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('status' => 'Active'), array('name'));
                            $category = '';
                            if ($categoryResult->num_rows() > 0) {
                                $category = $categoryResult->row()->_id;
                            }
                            $returnArr['category'] = (string) $category;
                        } else {
                            if ($checkUser->row()->status == "Deleted") {
                                $returnArr['message'] = $this->format_string("Your account is currently unavailable", "account_currently_unavailbale");
                            } else {
                                $returnArr['message'] = $this->format_string("Your account has been inactivated", "your_account_inactivated");
                            }
                        }
                    } else {
                        $returnArr['message'] = $this->format_string('Please check the email and password and try again', 'please_check_email_and_password');
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
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
    public function logout() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $condition = array('_id' => new \MongoId($this->user->_id));
            $checkUser = $this->app_model->get_selected_fields(USERS, $condition);
            if ($checkUser->num_rows() == 1) {
                $push_update_data = array(
                    'login_token' => ''
                );
                $this->app_model->update_details(USERS, $push_update_data, $condition);
                $returnArr['status'] = '1';
                $returnArr['response'] = $this->format_string("You are logged out", "you_are_logged_out");
            } else {
                $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Forgot Password
     *
     * */
    public function findAccount() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = $this->input->post('email');
            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 1) {
                if (valid_email($email)) {
                    $checkUser = $this->user_model->get_selected_fields(USERS, array('email' => $email), array('email', 'user_name', 'phone_number'));
                    if ($checkUser->num_rows() == 1) {
                        $verification_code = $this->get_rand_str('10');
                        $user_data = array('verification_code.forgot' => $verification_code);
                        $this->user_model->update_details(USERS, $user_data, array('email' => $email));
                        $returnArr['status'] = '1';
                        $returnArr['message'] = $this->format_string('Kindly check your email', 'check_your_email');
                    } else {
                        $returnArr['message'] = $this->format_string('Please enter the correct email and try again', 'enter_correct_email');
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid Email address", "invalid_email_address");
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters are missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * Update user Location
     *
     * */
    public function update_user_location() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 3) {
                $geo_data = array('geo' => array(floatval($longitude), floatval($latitude)));
                $checkGeo = $this->user_model->get_selected_fields(USER_LOCATION, array('user_id' => new \MongoId($user_id)), array('user_id'));

                $location = $this->app_model->find_location(floatval($longitude), floatval($latitude));
                $user_location = array();
                if (isset($location['result'][0])) {
                    $user_location = array(
                        'location_id' => new \MongoId($location['result'][0]['_id'])
                    );
                }
                $geo_data_user = array(
                    'loc' => array(
                        'lon' => floatval($longitude), 
                        'lat' => floatval($latitude)
                    ),
                    'last_active_time'=>new \MongoDate(time())
                );
                $update_data = array_merge($geo_data_user, $user_location);
                $this->user_model->update_details(USERS, $update_data, array('_id' => new \MongoId($user_id)));
                if ($checkGeo->num_rows() > 0) {
                    $this->user_model->update_details(USER_LOCATION, $geo_data, array('user_id' => new \MongoId($user_id)));
                } else {
                    $newGeo = array('user_id' => new \MongoId($user_id), 'geo' => array(floatval($longitude), floatval($latitude)));
                    $this->user_model->simple_insert(USER_LOCATION, $newGeo);
                }
                $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
                $avail_amount = 0;
                if (isset($walletDetail->row()->total)) {
                    $avail_amount = $walletDetail->row()->total;
                }

                $category_id = '';
                if (!empty($location['result'])) {
                    if (array_key_exists('avail_category', $location['result'][0]) && array_key_exists('fare', $location['result'][0])) {
                        if (!empty($location['result'][0]['avail_category']) && !empty($location['result'][0]['fare'])) {
                            $cat_avail = $location['result'][0]['avail_category'];
                            $cat_fare = array_keys($location['result'][0]['fare']);
                            $final_cat_list = array_intersect($cat_avail,$cat_fare);
                            $final_cat_list = array_values($final_cat_list);
                            $category_id = $final_cat_list[0];
                            #$category_id = $location['result'][0]['avail_category'][0];
                        }
                    }
                }                   
                $returnArr['ongoing_trips'] = 'No';
                $ongoing_trips = $this->app_model->get_ongoing_rides($user_id);
                if($ongoing_trips>0){
                    $returnArr['ongoing_trips'] = 'Yes';
                }
                
                
                $returnArr['category_id'] = (string) $category_id;

                $returnArr['status'] = '1';
                $returnArr['message'] = $this->format_string('Geo Location Updated', 'geo_location_updated');
                $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                if(isset($this->user->currency)) {
                    $returnArr['currency'] = (string)$this->user->currency;
                }
                $returnArr['wallet_amount'] = (string) round($avail_amount,2);

                if (isset($this->user->pending_payment) && $this->user->pending_payment == 'true') {
                    $returnArr['havePendingPayment'] = 'true';
                    $rideCondition = array(
                        'user.id' => $user_id,
                        'pay_status' => 'Pending'
                    );
                    $pendingRide = $this->rides_model->get_selected_fields(RIDES, $rideCondition, array('ride_id'));
                    $returnArr['unpaidRideId'] = $pendingRide->row()->ride_id;
                } else {
                    $returnArr['havePendingPayment'] = 'false';
                }
            } else {
                $returnArr['message'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This Function return the location list
     *
     * */
    public function get_location_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('status' => 'Active'), array('city'), array('city' => 'ASC'));
            if ($locationsVal->num_rows() > 0) {
                $locationsArr = array();
                foreach ($locationsVal->result() as $row) {
                    $locationsArr[] = array('id' => (string) $row->_id,
                        'city' => (string) $row->city
                    );
                }
                if (empty($locationsArr)) {
                    $locationsArr = json_decode("{}");
                }
                $returnArr['status'] = '1';
                $returnArr['response'] = array('locations' => $locationsArr);
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
     * This Function return the category list
     *
     * */
    public function get_category_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $location_id = (string) $this->input->post('location_id');

            if ($location_id != '') {
                $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('_id' => new \MongoId($location_id)), array('city', 'avail_category','fare'));
                if ($locationsVal->num_rows() > 0) {
                    $final_cat_list = $locationsVal->row()->avail_category;
                    if (isset($locationsVal->row()->avail_category) && isset($locationsVal->row()->fare)) {
                        if (!empty($locationsVal->row()->avail_category) && !empty($locationsVal->row()->fare)) {
                            $cat_avail = $locationsVal->row()->avail_category;
                            $cat_fare = array_keys($locationsVal->row()->fare);
                            $final_cat_list = array_intersect($cat_avail,$cat_fare);
                        }
                    }
                    $categoryResult = $this->app_model->get_available_category(CATEGORY, $final_cat_list);
                    $categoryArr = array();
                    if ($categoryResult->num_rows() > 0) {
                        foreach ($categoryResult->result() as $row) {
                            $categoryArr[] = array('id' => (string) $row->_id,
                                'category' => (string) $row->name
                            );
                        }
                    }
                    if (empty($categoryArr)) {
                        $categoryArr = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
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

    /**
     *
     * This Function return the rate card
     *
     * */
    public function get_rate_card() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $location_id = (string) $this->input->post('location_id');
            $category_id = (string) $this->input->post('category_id');
            
            
            $mins = $this->format_string('mins', 'mins');
            $per_min = $this->format_string('per min', 'per_min');
            $per = $this->format_string('per', 'per');
            
            $first = $this->format_string('First', 'first');
            $after = $this->format_string('After', 'after');
            $service_tax = $this->format_string('Service Tax', 'service_tax');
            $night_time_charges = $this->format_string('Night time charges', 'night_time_charges');
            $service_tax_payable = $this->format_string('Service tax is payable in addition to ride fare.', 'service_tax_payable');
            $night_time_charges_may_applicable = $this->format_string('Night time charges may be applicable during the late night hours and will be conveyed during the booking.', 'night_time_charges_may_applicable');
            $peak_time_charges_may_applicable = $this->format_string('Peak time charges may be applicable during high demand hours and will be conveyed during the booking.', 'peak_time_charges_may_applicable');
            $sur_charges_may_applicable_append = $this->format_string('This enables us to make more cabs available to you.', 'sur_charges_may_applicable_append');
            $peak_time_charges = $this->format_string('Peak time charges', 'peak_time_charges');
            $mins = $this->format_string('min', 'min_short');
            $mins_short = $this->format_string('mins', 'mins_short');
            $mins_ride_times_free = $this->format_string(' ride time is FREE! Wait time is chargeable.', 'mins_ride_times_free_text');
            $ride_time_charges = $this->format_string('Ride time charges', 'ride_time_charges');

            if ($location_id != '') {
                $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('_id' => new \MongoId($location_id)), array('currency', 'fare', 'peak_time', 'night_charge', 'service_tax','distance_unit'),array('city' => 1));
                
                $distance_unit = $this->data['d_distance_unit'];
                if(isset($locationsVal->row()->distance_unit)){
                    if($locationsVal->row()->distance_unit != ''){
                        $distance_unit = $locationsVal->row()->distance_unit;
                    } 
                }
                if($distance_unit == 'km'){
                    $disp_distance_unit = $this->format_string('km', 'km');
                }else if($distance_unit == 'mi'){
                    $disp_distance_unit = $this->format_string('mi', 'mi');
                }
                
                
                if ($locationsVal->num_rows() > 0) {
                    $ratecardArr = array();
                    if (isset($locationsVal->row()->fare[$category_id])) {
                        $standard_rate = array(array('title' => $first . ' ' . $locationsVal->row()->fare[$category_id]['min_km'] . ' ' . $disp_distance_unit,
                                'fare' => $locationsVal->row()->fare[$category_id]['min_fare'],
                                'sub_title' => ''
                            ),
                            array('title' => $after . ' ' . $locationsVal->row()->fare[$category_id]['min_km'] . ' ' . $disp_distance_unit,
                                #'fare' => $locationsVal->row()->fare[$category_id]['per_km'].' '.$per_km,
                                'fare' => $locationsVal->row()->fare[$category_id]['per_km'].' '.$per.' '.$disp_distance_unit,
                                'sub_title' => ''
                            )
                        );
                        if($locationsVal->row()->fare[$category_id]['min_time'] >1){
                                $wait_unit = $mins_short;
                        }else{
                                $wait_unit = $mins;
                         }
                        $extra_charges = array(array('title' => $ride_time_charges,
                                'fare' => $locationsVal->row()->fare[$category_id]['per_minute'] . ' ' . $per_min,
                                'sub_title' => $first . ' ' . $locationsVal->row()->fare[$category_id]['min_time'] . ' ' . $wait_unit.''.$mins_ride_times_free
                            )
                        );
                        if (isset($locationsVal->row()->peak_time)) {
                            if ($locationsVal->row()->peak_time == 'Yes') {
                                $extra_charges[] = array('title' => $peak_time_charges,
                                    'fare' => '',
                                    'sub_title' => $peak_time_charges_may_applicable.' '.$sur_charges_may_applicable_append
                                );
                            }
                        }
                        if (isset($locationsVal->row()->night_charge)) {
                            if ($locationsVal->row()->night_charge == 'Yes') {
                                $extra_charges[] = array('title' => $night_time_charges,
                                    'fare' => '',
                                    'sub_title' => $night_time_charges_may_applicable.' '.$sur_charges_may_applicable_append
                                );
                            }
                        }
                        if (isset($locationsVal->row()->service_tax)) {
                            if ($locationsVal->row()->service_tax > 0) {
                                $extra_charges[] = array('title' => $service_tax,
                                    'fare' => '',
                                    'sub_title' => $service_tax_payable
                                );
                            }
                        }
                        $currencyCode=$this->data['dcurrencyCode'];
                        if(isset($locationsVal->row()->currency)){
                            $currencyCode=$locationsVal->row()->currency;
                        }
                        $ratecardArr = array('currency' => $currencyCode,
                            'standard_rate' => $standard_rate,
                            'extra_charges' => $extra_charges,
                        );
                    }
                    if (empty($ratecardArr)) {
                        $ratecardArr = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('ratecard' => $ratecardArr);
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

    /**
     *
     * This Function add a new location
     *
     * */
    public function add_location($lat, $lon) {

        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=" . $lat . "," . $lon . "&sensor=false".$this->data['google_maps_api_key']);
        $jsonArr = json_decode($json);
        $newAddress = $jsonArr->{'results'}[0]->{'address_components'};
        #echo "<pre>"; print_r($newAddress); #die;
        foreach ($newAddress as $nA) {
            if ($nA->{'types'}[0] == 'route')
                $addressArr['street'] = $nA->{'long_name'};
            if ($nA->{'types'}[0] == 'sublocality_level_2')
                $addressArr['street1'] = $nA->{'long_name'};
            if ($nA->{'types'}[0] == 'sublocality_level_1')
                $addressArr['area'] = $nA->{'long_name'};
            if ($nA->{'types'}[0] == 'locality')
                $addressArr['location'] = $nA->{'long_name'};
            if ($nA->{'types'}[0] == 'administrative_area_level_2')
                $addressArr['city'] = $nA->{'long_name'};
            if ($nA->{'types'}[0] == 'administrative_area_level_1')
                $addressArr['state'] = $nA->{'long_name'};
            if ($nA->{'types'}[0] == 'country')
                $addressArr['country'] = $nA->{'long_name'};
            if ($nA->{'types'}[0] == 'country')
                $addressArr['country_code'] = $nA->{'short_name'};
            if ($nA->{'types'}[0] == 'postal_code')
                $addressArr['zip'] = $nA->{'long_name'};
        }
        if (!array_key_exists('city', $addressArr)) {
            if ($addressArr['state'] != "") {
                $addressArr['city'] = $addressArr['state'];
            } else if ($addressArr['country'] != "") {
                $addressArr['city'] = $addressArr['country'];
            }
            $address = $addressArr['city'];
        } else {
            $address = $addressArr['city'];
            if ($addressArr['state'] != "") {
                $address .= ', ' . $addressArr['state'];
            }
            if ($addressArr['country'] != "") {
                $address .= ', ' . $addressArr['country'];
            }
        }

        $url = "https://maps.google.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false".$this->data['google_maps_api_key'];
        $jsonnew = file_get_contents($url);
        $jsonArr1 = json_decode($jsonnew);
        $newAddress1 = $jsonArr1->{'results'}[0]->{'address_components'};
        #echo "<pre>"; print_r($newAddress1); die;
        foreach ($newAddress1 as $nA1) {
            if ($nA1->{'types'}[0] == 'route')
                $addressArr['street'] = $nA1->{'long_name'};
            if ($nA1->{'types'}[0] == 'sublocality_level_2')
                $addressArr['street1'] = $nA1->{'long_name'};
            if ($nA1->{'types'}[0] == 'sublocality_level_1')
                $addressArr['area'] = $nA1->{'long_name'};
            if ($nA1->{'types'}[0] == 'locality')
                $addressArr['location'] = $nA1->{'long_name'};
            if ($nA1->{'types'}[0] == 'administrative_area_level_2')
                $addressArr['city'] = $nA1->{'long_name'};
            if ($nA1->{'types'}[0] == 'administrative_area_level_1')
                $addressArr['state'] = $nA1->{'long_name'};
            if ($nA1->{'types'}[0] == 'country')
                $addressArr['country'] = $nA1->{'long_name'};
            if ($nA1->{'types'}[0] == 'country')
                $addressArr['country_code'] = $nA1->{'short_name'};
            if ($nA1->{'types'}[0] == 'postal_code')
                $addressArr['zip'] = $nA1->{'long_name'};
        }


        $condition = array('cca2' => (string) $addressArr['country_code']);
        $countryList = $this->user_model->get_all_details(COUNTRY, $condition);
        if ($countryList->num_rows() > 0) {
            $country_name = $addressArr['country'];
            $country_code = $addressArr['country_code'];
            #$country_currency=$countryList->row()->currency_code;
            $country_id = (string) $countryList->row()->_id;
        }
        $country_currency = $this->data['dcurrencyCode'];

        $lat = $jsonArr1->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        $lang = $jsonArr1->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        $northeast_lat = $jsonArr1->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lat'};
        $northeast_lng = $jsonArr1->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lng'};
        $southwest_lat = $jsonArr1->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lat'};
        $southwest_lng = $jsonArr1->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lng'};

        /* Get latitude and longitude for an address */
        $location = array('lng' => floatval($lang), 'lat' => floatval($lat));
        $bounds = array('southwest' => array('lng' => floatval($southwest_lng), 'lat' => floatval($southwest_lat)), 'northeast' => array('lng' => floatval($northeast_lng), 'lat' => floatval($northeast_lat)));

        $avail_category = array();
        $fare = array();
        $categoryResult = $this->app_model->get_all_details(CATEGORY, array("status" => 'Active'));
        if ($categoryResult->num_rows() > 0) {
            foreach ($categoryResult->result() as $row) {
                $avail_category[] = (string) $row->_id;
                $fare [(string) $row->_id] = array("min_km" => 1,
                    "min_time" => "2",
                    "min_fare" => "0.8",
                    "per_km" => "0.5",
                    "per_minute" => "1",
                    "wait_per_minute" => "0.2",
                    "peak_time_charge" => "",
                    "night_charge" => "",
                );
            }
        }
        $exist = 0;
        $condition = array('location' => $location);
        $duplicate_name = $this->app_model->get_all_details(LOCATIONS, $condition);
        if ($duplicate_name->num_rows() > 0) {
            $exist = 1;
        }
        $city = $addressArr['city'];
        $condition = array('city' => $city);
        $duplicate_name = $this->app_model->get_all_details(LOCATIONS, $condition);
        if ($duplicate_name->num_rows() > 0) {
            $exist = 1;
        }
        $is_location_exist = $this->app_model->location_exist($lang, $lat);
        if (!empty($is_location_exist['result'])) {
            $exist = 1;
        }
        $country = array('id' => new \MongoId($country_id), 'name' => $country_name, 'code' => $country_code);
        $locationArr = array(
            "peak_time_frame" => array(
                "from" => "",
                "to" => "",
            ),
            "night_time_frame" => array(
                "from" => "",
                "to" => "",
            ),
            "service_tax" => floatval(1.2),
            "site_commission" => floatval(2.8),
            "country" => $country,
            "city" => $city,
            "location" => array(
                "lng" => floatval($lon),
                "lat" => floatval($lat),
            ),
            "bounds" => $bounds,
            "currency" => (string) $country_currency,
            "avail_category" => $avail_category,
            "peak_time" => "No",
            "night_charge" => "No",
            "status" => "Active",
            "fare" => $fare,
        );
        if ($exist == 0) {
            $this->app_model->simple_insert(LOCATIONS, $locationArr);
        }
    }

    /**
     *
     * This Function return the drivers information for map view
     *
     * */
    public function get_drivers_in_map() {
        $limit = 100000;
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = trim($this->input->post('user_id'));
            $latitude = $this->input->post('lat');
            $longitude = $this->input->post('lon');
            $category = $this->input->post('category');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($category!='') {
                if ($chkValues >= 3) {
                    $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)));
                    if ($checkUser->num_rows() == 1) {
                        $coordinates = array(floatval($longitude), floatval($latitude));
                        $location = $this->app_model->find_location(floatval($longitude), floatval($latitude),"Yes");
                        
                        if (!empty($location['result'])) {
                            $condition = array('status' => 'Active');

                            $geo_data_user = array(
                                'loc' => array(
                                    'lon' => floatval($longitude), 
                                    'lat' => floatval($latitude)
                                ), 
                                'last_active_time'=>new \MongoDate(time()),
                                'location_id' => new \MongoId($location['result'][0]['_id'])
                            );
                            $this->user_model->update_details(USERS, $geo_data_user, array('_id' => new \MongoId($user_id)));
                            
                            /*
                                Make the final category list
                            */
                            $final_cat_list = $location['result'][0]['avail_category'];
                            if (array_key_exists('avail_category', $location['result'][0]) && array_key_exists('fare', $location['result'][0])) {
                                if (!empty($location['result'][0]['avail_category']) && !empty($location['result'][0]['fare'])) {
                                    $cat_avail = $location['result'][0]['avail_category'];
                                    $cat_fare = array_keys($location['result'][0]['fare']);
                                    $final_cat_list = array_intersect($cat_avail,$cat_fare);
                                }
                            }
                            $categoryResult = $this->app_model->get_available_category(CATEGORY, $final_cat_list);
                            $availCategory = array();
                            $categoryArr = array();
                            $rateCard = array();
                            $vehicle_type = '';
                            if ($categoryResult->num_rows() > 0) {
                                foreach ($categoryResult->result() as $cat) {
                                    $availCategory[(string) $cat->_id] = $cat->name;
                                    $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $cat->_id, $limit);
                                    
                                    #$mins = $this->format_string('mins', 'mins');
                                    $mins = $this->format_string('min', 'min_short');
                                    $mins_short = $this->format_string('mins', 'mins_short');
                                    $no_cabs = $this->format_string('no cabs', 'no_cabs');
                                    if (!empty($category_drivers['result'])) {
                                        $distance = $category_drivers['result'][0]['distance'];
                                        $eta_time = $this->app_model->calculateETA($distance);
                                        if ($eta_time > 1) {
                                            $eta_unit = $mins_short;
                                        } else {
                                            $eta_unit = $mins;
                                        }
                                        $eta = $this->app_model->calculateETA($distance) . ' ' . $mins;
                                    } else {
                                        $eta_time = "";
                                        $eta_unit = "";
                                        $eta = $no_cabs;
                                    }
                                    $avail_vehicles = array();
                                    if ((string) $cat->_id == $category) {
                                        $avail_vehicles = $cat->vehicle_type;
                                    }
                                    $icon_normal = base_url() . ICON_IMAGE_DEFAULT;
                                    $icon_active = base_url() . ICON_IMAGE_ACTIVE;
                                    $icon_car_image = base_url().ICON_MAP_CAR_IMAGE;

                                    if (isset($cat->icon_normal)) {
                                        if ($cat->icon_normal != '') {
                                            $icon_normal = base_url() . ICON_IMAGE . $cat->icon_normal;
                                        }
                                    }
                                    if (isset($cat->icon_active)) {
                                        if ($cat->icon_active != '') {
                                            $icon_active = base_url() . ICON_IMAGE . $cat->icon_active;
                                        }
                                    }
                                    if (isset($cat->icon_car_image)) {
                                        if ($cat->icon_car_image != '') {
                                            $icon_car_image = base_url() . ICON_IMAGE . $cat->icon_car_image;
                                        }
                                    }                                    

                                    $categoryArr[] = array('id' => (string) $cat->_id,
                                        'name' => $cat->name,
                                        'eta' => (string) $eta,
                                        'eta_time' => (string) $eta_time,
                                        'eta_unit' => (string) $eta_unit,
                                        'icon_normal' => (string) $icon_normal,
                                        'icon_active' => (string) $icon_active,
                                        'icon_car_image' => (string) $icon_car_image    
                                    );
                                }
                                $vehicleResult = $this->app_model->get_available_vehicles($avail_vehicles);
                                if ($vehicleResult->num_rows() > 0) {
                                    $vehicleArr = (array) $vehicleResult->result_array();
                                    $vehicle_type = implode(',', array_map(function($n) {
                                                return $n['vehicle_type'];
                                            }, $vehicleArr));
                                }
                                $note_heading = $this->format_string('Note', 'note_heading');
                                $note_peak_time = $this->format_string('Peak time charges may apply. Service tax extra.', 'note_peak_time');
                                if (isset($availCategory[(string) $category])) {
                                    $rateCard['category'] = $availCategory[(string) $category];
                                    $rateCard['vehicletypes'] = $vehicle_type;
                                    $rateCard['note'] = $note_heading.' : '.$note_peak_time;
                                    $fare = array();
                                    
                                    $distance_unit = $this->data['d_distance_unit'];
                                    if(isset($location['result'][0]['distance_unit'])){
                                        if($location['result'][0]['distance_unit'] != ''){
                                            $distance_unit = $location['result'][0]['distance_unit'];
                                        } 
                                    }
                                    
                                    $min = $this->format_string('min', 'min');
                                    $first = $this->format_string('First', 'first');
                                    $after = $this->format_string('After', 'after');
                                    $ride_time_rate_post = $this->format_string('Ride time rate post ', 'ride_time_rate_post');
                                    if (isset($location['result'][0]['fare'])) {
                                        if (array_key_exists($category, $location['result'][0]['fare'])) {
                                            if($location['result'][0]['fare'][$category]['min_time']>1){
                                                $min_time_unit = $mins_short;
                                            }else{
                                                $min_time_unit = $mins;
                                            }
                                            $fare['min_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['min_fare'],
                                                'text' => $first . ' ' . $location['result'][0]['fare'][$category]['min_km'] . ' ' . $distance_unit);
                                            $fare['after_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['per_km'] . '/' . $distance_unit,
                                                'text' => $after . ' ' . $location['result'][0]['fare'][$category]['min_km'] . ' ' . $distance_unit);
                                            $fare['other_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['per_minute'] . '/' . $mins,
                                                'text' => $ride_time_rate_post . ' ' . $location['result'][0]['fare'][$category]['min_time'] . ' ' . $min_time_unit);
                                        }
                                    }
                                    $rateCard['farebreakup'] = $fare;
                                }
                            }

                            $driverList = $this->app_model->get_nearest_driver($coordinates, $category, $limit);
                            $driversArr = array();
                            if (!empty($driverList['result'])) {
                                foreach ($driverList['result'] as $driver) {
                                    $lat = $driver['loc']['lat'];
                                    $lon = $driver['loc']['lon'];
                                    $driversArr[] = array('lat' => $lat,
                                        'lon' => $lon
                                    );
                                }
                            }
                            if (empty($categoryArr)) {
                                $categoryArr = json_decode("{}");
                            } if (empty($driversArr)) {
                                $driversArr = json_decode("{}");
                            } if (empty($rateCard)) {
                                $rateCard = json_decode("{}");
                            }
                            $currencyCode=$this->data['dcurrencyCode'];
                            if(isset($location['result'][0]['currency'])) {
                                $currencyCode=$location['result'][0]['currency'];
                            }
                            $returnArr['status'] = '1';
                            $returnArr['response'] = array('currency' => (string) $currencyCode, 'category' => $categoryArr, 'drivers' => $driversArr, 'ratecard' => $rateCard, 'selected_category' => (string) $category);
                        } else {
                            $returnArr['response'] = $this->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
                        }
                    } else {
                        $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                    }
                } else {
                    $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
                }
            }else{
                    $returnArr['response'] = $this->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This Function return the eta information for a ride
     *
     * */
    public function get_eta() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $pickup = $this->input->post('pickup');
            $drop = $this->input->post('drop');
            $pickup_lat = $this->input->post('pickup_lat');
            $pickup_lon = $this->input->post('pickup_lon');
            $drop_lat = $this->input->post('drop_lat');
            $drop_lon = $this->input->post('drop_lon');
            $category = $this->input->post('category');
            $type = $this->input->post('type');
            $pickup_date = $this->input->post('pickup_date');
            $pickup_time = $this->input->post('pickup_time');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 8) {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email'));
                if ($checkUser->num_rows() == 1) {
                    $this->setTimezone($pickup_lat, $pickup_lon);
                    $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
                    $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
                    if (!empty($location['result'])) {
                        if (!empty($location['result'][0]['fare'][$category])){
                            $condition = array('status' => 'Active');
                            $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($category)), array('name'));
                            $availCategory = array();
                            $etaArr = array();
                            $rateCard = array();
                                
                            $distance_unit = $this->data['d_distance_unit'];
                            if(isset($location['result'][0]['distance_unit'])){
                                if($location['result'][0]['distance_unit'] != ''){
                                    $distance_unit = $location['result'][0]['distance_unit'];
                                } 
                            }
                            if ($categoryResult->num_rows() > 0) {
                                $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, 1);

                                $from = $pickup_lat . ',' . $pickup_lon;
                                $to = $drop_lat . ',' . $drop_lon;

                                $gmap = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$this->data['google_maps_api_key']);
                                $map_values = json_decode($gmap);
                                $routes = $map_values->routes;
                                if (!empty($routes)) {
                                        usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));

                                        $pickup = (string) $routes[0]->legs[0]->start_address;
                                        $drop = (string) $routes[0]->legs[0]->end_address;

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
                                    
                                        $minduration = round(($routes[0]->legs[0]->duration->value) / 60);
                                        $mindurationtext = $routes[0]->legs[0]->duration->text;
                                        $mins = $this->format_string('mins', 'mins');
                                        $mindurationtext = $minduration.' '.$mins;

                                        $peak_time = '';
                                        $night_charge = '';
                                        $peak_time_amount = 1;
                                        $night_charge_amount = 1;
                                        $min_amount = 0.00;
                                        $max_amount = 0.00;

                                        if ($type = 1) {
                                            $pickup_datetime = strtotime($pickup_date . ' ' . $pickup_time);
                                        } else {
                                            $pickup_datetime = time();
                                            $pickup_date = date('Y-m-d');
                                        }
                                        if ($location['result'][0]['peak_time'] == 'Yes') {
                                            $time1 = strtotime($pickup_date . ' ' . $location['result'][0]['peak_time_frame']['from']);
                                            $time2 = strtotime($pickup_date . ' ' . $location['result'][0]['peak_time_frame']['to']);
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
                                                $peak_time_amount = $location['result'][0]['fare'][$category]['peak_time_charge'];
                                                $peak_time = $this->format_string('Peak time surcharge', 'peak_time_surcharge').' '. $location['result'][0]['fare'][$category]['peak_time_charge'] . 'X';
                                            }
                                        }
                                        if ($location['result'][0]['night_charge'] == 'Yes') {
                                            $time1 = strtotime($pickup_date . ' ' . $location['result'][0]['night_time_frame']['from']);
                                            $time2 = strtotime($pickup_date . ' ' . $location['result'][0]['night_time_frame']['to']);
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
                                                $night_charge_amount = $location['result'][0]['fare'][$category]['night_charge'];
                                                $night_charge = $this->format_string('Night time charge', 'night_time_charge').' '. $location['result'][0]['fare'][$category]['night_charge'] . 'X';
                                            }
                                        }
                                        $min_amount = floatval($location['result'][0]['fare'][$category]['min_fare']);
                                        if (floatval($location['result'][0]['fare'][$category]['min_time']) < floatval($minduration)) {
                                            $ride_fare = 0;
                                            $ride_time = floatval($minduration) - floatval($location['result'][0]['fare'][$category]['min_time']);
                                            $ride_fare = $ride_time * floatval($location['result'][0]['fare'][$category]['per_minute']);
                                            $min_amount = $min_amount + $ride_fare;
                                        }
                                        if (floatval($location['result'][0]['fare'][$category]['min_km']) < floatval($mindistance)) {
                                            $after_fare = 0;
                                            $ride_time = floatval($mindistance) - floatval($location['result'][0]['fare'][$category]['min_km']);
                                            $after_fare = $ride_time * floatval($location['result'][0]['fare'][$category]['per_km']);
                                            $min_amount = $min_amount + $after_fare;
                                        }


                                        if ($night_charge_amount >= 1) {
                                            $min_amount = $min_amount * $night_charge_amount;
                                        }
                                        if ($peak_time_amount >= 1) {
                                            $min_amount = $min_amount * $peak_time_amount;
                                        }

                                        $max_amount = $min_amount + ($min_amount*0.01*30);
                                        $note_heading = $this->format_string('Note', 'note_heading');
                                        $note_approximate_estimate = $this->format_string('This is an approximate estimate. Actual cost and travel time may be different.', 'note_approximate_estimate');
                                        $note_peak_time = $this->format_string('Peak time charges may apply. Service tax extra.', 'note_peak_time');
                                        $etaArr = array('catrgory_id' => (string) $categoryResult->row()->_id,
                                            'catrgory_name' => $categoryResult->row()->name,
                                            'pickup' => (string) $pickup,
                                            'drop' => (string) $drop,
                                            'min_amount' => number_format($min_amount, 2),
                                            'max_amount' => number_format($max_amount, 2),
                                            'att' => (string) $mindurationtext,
                                            'peak_time' => (string) $peak_time,
                                            'night_charge' => (string) $night_charge,
                                            'note' => $note_heading.' : '.$note_approximate_estimate
                                        );
                                        $rateCard['note'] = $note_heading.' : '.$note_peak_time;
                                        
                                        $distance_unit = $this->data['d_distance_unit'];
                                        if(isset($location['result'][0]['distance_unit'])){
                                            if($location['result'][0]['distance_unit'] != ''){
                                                $distance_unit = $location['result'][0]['distance_unit'];
                                            } 
                                        }
                                        if($distance_unit == 'km'){
                                            $disp_distance_unit = $this->format_string('km', 'km');
                                        }else if($distance_unit == 'mi'){
                                            $disp_distance_unit = $this->format_string('mi', 'mi');
                                        }
                                        $min_short = $this->format_string('min', 'min_short');      
                                        $mins_short = $this->format_string('mins', 'mins_short');                       
                                        if($location['result'][0]['fare'][$category]['min_time']>1){
                                            $min_time_unit = $mins_short;
                                        }else{
                                            $min_time_unit = $min_short;
                                        }
                                        $first = $this->format_string('First', 'first');
                                        $after = $this->format_string('After', 'after');
                                        $ride_time_rate_post = $this->format_string('Ride time rate post', 'ride_time_rate_post');
                                        
                                        $fare = array();
                                        $fare['min_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['min_fare'],
                                            'text' => $first . ' ' . $location['result'][0]['fare'][$category]['min_km'] . ' ' . $disp_distance_unit);
                                        $fare['after_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['per_km'] . '/' . $disp_distance_unit,
                                            'text' => $after . ' ' . $location['result'][0]['fare'][$category]['min_km'] . ' ' . $disp_distance_unit);
                                        $fare['other_fare'] = array('amount' => (string) $location['result'][0]['fare'][$category]['per_minute'] . '/' . $min_short,
                                            'text' => $ride_time_rate_post . ' ' . $location['result'][0]['fare'][$category]['min_time'] . ' ' . $min_time_unit);
                                        $rateCard['farebreakup'] = $fare;
                                                
                                        if (empty($etaArr)) {
                                            $etaArr = json_decode("{}");
                                        }
                                        if (empty($rateCard)) {
                                            $rateCard = json_decode("{}");
                                        } 
                                        $returnArr['status'] = '1';
                                        $currencyCode=$this->data['dcurrencyCode'];
                                        if(isset($location['result'][0]['currency'])) {
                                            $currencyCode=$location['result'][0]['currency'];
                                        }
                                        $returnArr['response'] = array('currency' => (string) $currencyCode, 
                                                                        'eta' => $etaArr, 
                                                                        'ratecard' => $rateCard
                                                                    );
                                } else{
                                    $returnArr['response'] = $this->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');
                                }
                            } else{
                                $returnArr['response'] = $this->format_string('Service category not found','service_category_not_found');
                            }
                            
                        } else {
                            $returnArr['response'] = $this->format_string('This type of cab service is not available in your location.', 'this_type_of_cab_not_available_location');
                        }
                        
                    } else {
                        $returnArr['response'] = $this->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
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
     * This Function check whether 
     *
     * */
    public function apply_coupon_code() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $code = $this->input->post('code');
            $pickup_date = $this->input->post('pickup_date');
            $pickup_lat = floatval($this->input->post('pickup_lat'));
            $pickup_lon = floatval($this->input->post('pickup_lon'));

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }
            
            if ($chkValues >= 5) {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email'));
                if ($checkUser->num_rows() == 1) {
                    if($pickup_lat !='' && $pickup_lon !=''){
                        $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));                 
                        $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
                        if (!empty($location['result'])) {
                            $location_id = $location['result'][0]['_id'];
                            $checkCode = $this->app_model->get_all_details(PROMOCODE, array('promo_code' => $code));
                            if ($checkCode->num_rows() > 0) {
                                if ($checkCode->row()->status == 'Active') {
                                    if($checkCode->row()->location_id == $location_id){
                                        $valid_from = strtotime($checkCode->row()->validity['valid_from'] . ' 00:00:00');
                                        $valid_to = strtotime($checkCode->row()->validity['valid_to'] . ' 23:59:59');
                                        $date_time = strtotime($pickup_date);
                                        if (($valid_from <= $date_time) && ($valid_to >= $date_time)) {
                                            if ($checkCode->row()->usage_allowed > $checkCode->row()->no_of_usage) {
                                                $coupon_usage = array();
                                                if (isset($checkCode->row()->usage)) {
                                                    $coupon_usage = $checkCode->row()->usage;
                                                }
                                                $usage = $this->app_model->check_user_usage($coupon_usage, $user_id);
                                                if ($usage < $checkCode->row()->user_usage) {
                                                    $discount_amount = $checkCode->row()->promo_value;
                                                    $discount_type = $checkCode->row()->code_type;
                                                    $currencyCode=$this->data['dcurrencyCode'];
                                                    if(isset($checkCode->row()->currency_symbol)){
                                                        $currencyCode = $checkCode->row()->currency_symbol;
                                                    }
                                                    $returnArr['status'] = '1';
                                                    $returnArr['response'] = array('code' => (string) $code, 
                                                                                    'discount_amount' => (string)$discount_amount,
                                                                                    'discount_type' => (string)$discount_type,
                                                                                    'currency_code' => $currencyCode,
                                                                                    'message' => $this->format_string('Coupon code applied.', 'coupon_applied'));
                                                } else {
                                                    $returnArr['response'] = $this->format_string('Maximum no used in your account', 'maximum_not_used_in_your_account');
                                                }
                                            } else {
                                                $returnArr['response'] = $this->format_string('Coupon Expired', 'coupon_expired');
                                            }
                                        } else {
                                            $returnArr['response'] = $this->format_string('Coupon Expired', 'coupon_expired');
                                        }
                                    } else {
                                        $returnArr['response'] = $this->format_string('Sorry,Your unable to use this coupon code for this location', 'invalid_location');
                                    }
                                } else {
                                    $returnArr['response'] = $this->format_string('Unavailable Coupon', 'coupon_unavailable');
                                }
                            } else {
                                $returnArr['response'] = $this->format_string('Invalid Coupon', 'nvalid_coupon');
                            }
                        } else {
                                $returnArr['response'] = $this->format_string('Invalid location', 'invalid_location');
                            }
                    } else {
                        $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
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
     * This Function used for booking a ride
     *
     * */
    public function booking_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $pickup = $this->input->post('pickup');
            $pickup_lat = $this->input->post('pickup_lat');
            $pickup_lon = $this->input->post('pickup_lon');
            $category = $this->input->post('category');
            $type = $this->input->post('type');
            $pickup_date = $this->input->post('pickup_date');
            $pickup_time = $this->input->post('pickup_time');
            $code = $this->input->post('code');
            $try = intval($this->input->post('try'));
            $ride_id = (string) $this->input->post('ride_id');

            $drop_loc = trim((string)$this->input->post('drop_loc'));
            $drop_lat = $this->input->post('drop_lat');
            $drop_lon = $this->input->post('drop_lon');
            $payment_method_nonce = (string)trim($this->input->post('payment_method_nonce'));
            $payment_type = strtolower($this->input->post('payment_type'));

            if($drop_loc=='') {
                $drop_lat = 0;
                $drop_lon = 0;
            }           
            
            $riderlocArr = array('lat' => (string) $pickup_lat, 'lon' => (string) $pickup_lon);

            if ($type == 1) {
                $ride_type = 'Later';
                $pickup_datetime = $pickup_date . ' ' . $pickup_time;
                $this->setTimezone($pickup_lat, $pickup_lon);
                $pickup_timestamp = strtotime($pickup_datetime);
            } else {
                $ride_type = 'Now';
                $pickup_timestamp = time();
            }

            $after_one_hour = strtotime('+' . $this->config->item('min_book_ahead_time') .' hour', time());
            if( $type == 0 || ($type ==1 && ($pickup_timestamp > $after_one_hour)) ) {
                if (is_array($this->input->post())) {
                    $chkValues = count(array_filter($this->input->post()));
                } else {
                    $chkValues = 0;
                }

                $acceptance = 'No';
                if ($ride_id != '') {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id));
                    if ($checkRide->num_rows() == 1) {
                        if ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived') {
                            $acceptance = 'Yes';
                            $driver_id = $checkRide->row()->driver['id'];
                            $mindurationtext = '';
                            if (isset($checkRide->row()->driver['est_eta'])) {
                                $mindurationtext = $checkRide->row()->driver['est_eta'] . '';
                            }
                            $lat_lon = @explode(',', $checkRide->row()->driver['lat_lon']);
                            $driver_lat = $lat_lon[0];
                            $driver_lon = $lat_lon[1];
                        } else {
                            if($checkRide->row()->ride_status == 'Booked'){
                                /* Saving Unaccepted Ride for future reference */
                                save_ride_details_for_stats($ride_id);
                                /* Saving Unaccepted Ride for future reference */
                                $this->app_model->commonDelete(RIDES, array('ride_id' => $ride_id));
                            }
                        }
                    }
                }

                if ($acceptance == 'No') {
                    if($payment_method_nonce != '') {
                        if ($chkValues >= 6) {
                            $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email', 'user_name', 'country_code', 'phone_number', 'push_type', 'pending_payment'));
                            if ($checkUser->num_rows() == 1) {
                                if ($checkUser->row()->pending_payment !== true) {
                                    $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
                                    $currencyCode = $this->data['dcurrencyCode'];
                                    $currencySymbol = $this->data['dcurrencySymbol'];                            
                                    if(isset($location['result'][0]['currency'])) {
                                        if($location['result'][0]['currency'] != '') {
                                            $currencyCode  = $location['result'][0]['currency'];
                                            $currencySymbol  = $location['result'][0]['currency_symbol'];
                                        }
                                    }

                                    $distance_unit = $this->data['d_distance_unit'];
                                    if (isset($location['result'][0]['distance_unit'])) {
                                        if ($location['result'][0]['distance_unit'] != '') {
                                            $distance_unit = $location['result'][0]['distance_unit'];
                                        } 
                                    }

                                    $canBook['status'] = true;
                                    if ($payment_type == 'wallet' || $payment_type == 'card') {
                                        // start: pre-authorize, does user have enough balance ?
                                        $this->load->model('rides_model');

                                        if ($type == 1) {
                                            $pickup_datetime = strtotime($pickup_date . ' ' . $pickup_time);
                                        } else {
                                            $pickup_datetime = time();
                                            $pickup_date = date('Y-m-d');
                                        }
                                        $from = $pickup_lat . ',' . $pickup_lon;
                                        $to = $drop_lat . ',' . $drop_lon;    
                                        $gmap = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin=' . $from . '&destination=' . $to . '&alternatives=true&sensor=false&mode=driving'.$this->data['google_maps_api_key']);
                                        $map_values = json_decode($gmap);
                                        $routes = $map_values->routes;
                                        $minduration = round(($routes[0]->legs[0]->duration->value) / 60);

                                        $min_distance = $routes[0]->legs[0]->distance->text;
                                        if (preg_match('/km/', $min_distance)){
                                            $return_distance = 'km';
                                        } else if (preg_match('/mi/', $min_distance)){
                                            $return_distance = 'mi';
                                        } else if (preg_match('/m/', $min_distance)){
                                            $return_distance = 'm';
                                        } else {
                                            $return_distance = 'km';
                                        }
                                        
                                        $mindistance = floatval(str_replace(',', '', $min_distance));
                                        if ($distance_unit!=$return_distance) {
                                            if ($distance_unit == 'km' && $return_distance == 'mi') {
                                                $mindistance = $mindistance * 1.60934;
                                            } else if ($distance_unit == 'mi' && $return_distance == 'km') {
                                                $mindistance = $mindistance * 0.621371;
                                            } else if ($distance_unit == 'km' && $return_distance == 'm') {
                                                $mindistance = $mindistance / 1000;
                                            } else if ($distance_unit == 'mi' && $return_distance == 'm') {
                                                $mindistance = $mindistance * 0.00062137;
                                            }
                                        }
                                        $mindistance = floatval(round($mindistance, 2));
                                        
                                        $trip_estimate = $this->rides_model->getTripEstimate($pickup_date, $pickup_datetime, $location, $category, $minduration, $mindistance);
                                        $trip_estimate = ceil($trip_estimate);

                                        $original_currency = $this->config->item('currency_code');
                                        $supportedCurrency = $this->get_braintree_merchant_currency($currencyCode);
                                        
                                        $converted_trip_estimate = $trip_estimate;

                                        $this->load->model('braintree_model');

                                        if (!$supportedCurrency) {
                                            $currencyval = $this->braintree_model->get_currency_value(round($trip_estimate, 2), $currencyCode, $original_currency);
                                            if (!empty($currencyval)) {
                                                $converted_trip_estimate = $currencyval['CurrencyVal'];
                                            }
                                        }

                                        if ($payment_type == 'wallet') {
                                            $this->load->model('wallet_model');
                                            $wallet_balance = $this->wallet_model->get_wallet_balance($user_id);
                                            if ($wallet_balance < $trip_estimate) {
                                                $canBook['status'] = false;
                                                $canBook['error_message'] = 'Insufficient Wallet Amount';
                                            }
                                        } else if ($payment_type == 'card') {
                                            $preAuthResult = $this->braintree_model->preAuthTransaction($converted_trip_estimate, $payment_method_nonce);
                                            if ($preAuthResult['status'] == 1) {
                                                $this->braintree_model->voidTransaction($preAuthResult['txnId']);
                                            }
                                            else {
                                                $canBook['status'] = false;
                                                $canBook['error_message'] = $preAuthResult['error_message'];
                                            }
                                        }
                                    }
                                    if ($canBook['status'] === true) {                                        
                                        if (!empty($location['result'])) {
                                            $condition = array('status' => 'Active');
                                            $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($category)), array('name'));
                                            if ($categoryResult->num_rows() > 0) {
                                                $can_ride_now = false;
                                                if ($type != 1) {
                                                    $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
                                                    $limit = 100000;
                                                    $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, $limit);
                                                    if (count($category_drivers['result']) == 0) {
                                                        $returnArr['response'] = $this->format_string('No cabs available nearby', 'cabs_not_available_nearby');
                                                    } else {
                                                        if ($try < 1) {
                                                            $try = 1;
                                                        }
                                                        $closest_drivers = $this->driver_model->find_closest_drivers($category_drivers, $pickup_lat, $pickup_lon, $try);

                                                        if (!$closest_drivers) {
                                                            $returnArr['response'] = $this->format_string('We\'re sorry, there\'s no available drivers nearby, Please try again later', 'drivers_out_of_range');
                                                        }
                                                        $fcm_data = array();
                                                        $push_driver = array();
                                                        foreach ($closest_drivers as $driver) {
                                                            if (isset($driver['fcm_token'])) {
                                                                if (isset($driver['is_voda_customer']) 
                                                                    && $driver['is_voda_customer'] == true 
                                                                    && isset($driver['outstanding_amount']['is_due']) 
                                                                    && $driver['outstanding_amount']['is_due'] == true) {
                                                                    // skip
                                                                } else {
                                                                    $fcm_data[$driver['fcm_token']] = $driver['device_type'];
                                                                    $push_driver[] = $driver['_id'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($type != 1 && $closest_drivers) {
                                                        $can_ride_now = true;
                                                    }
                                                }
                                                if ($type == 1 || $can_ride_now) {
                                                    $checkCode = $this->app_model->get_all_details(PROMOCODE, array('promo_code' => $code));
                                                    $code_used = 'No';
                                                    $coupon_type = '';
                                                    $coupon_amount = '';
                                                    if ($checkCode->num_rows() > 0) {
                                                        $code_used = 'Yes';
                                                        $coupon_type = $checkCode->row()->code_type;
                                                        $coupon_amount = $checkCode->row()->promo_value;
                                                    }
                                                    $site_commission = 0;
                                                    if (isset($location['result'][0]['site_commission'])) {
                                                        if ($location['result'][0]['site_commission'] > 0) {
                                                            $site_commission = $location['result'][0]['site_commission'];
                                                        }
                                                    }
                                                    
                                                    $merchant_id ='';
                                                    if (!empty($location['result'])) {
                                                        if (isset($location['result'][0]['merchant_id'])) {
                                                            if ($location['result'][0]['merchant_id']!='') {
                                                            $merchant_id = $location['result'][0]['merchant_id'];
                                                            }
                                                        }       
                                                    }                               
                                                
                                                    $ride_id = $this->app_model->get_ride_id();
                                                    $bookingInfo = array(
                                                        'ride_id' => (string) $ride_id,
                                                        'type' => $ride_type,
                                                        'booking_ref' =>'android',
                                                        'merchant_id' =>$merchant_id,
                                                        'payment_method_nonce' => $payment_method_nonce,
                                                        'currency' => $currencyCode,
                                                        'currency_symbol' => $currencySymbol,
                                                        'commission_percent' => $site_commission,
                                                        'location' => array('id' => (string) $location['result'][0]['_id'],
                                                            'name' => $location['result'][0]['city']
                                                        ),
                                                        'user' => array('id' => (string) $checkUser->row()->_id,
                                                            'name' => $checkUser->row()->user_name,
                                                            'email' => $checkUser->row()->email,
                                                            'phone' => $checkUser->row()->country_code . $checkUser->row()->phone_number
                                                        ),
                                                        'driver' => array('id' => '',
                                                            'name' => '',
                                                            'email' => '',
                                                            'phone' => ''
                                                        ),
                                                        'total' => array('fare' => '',
                                                            'distance' => '',
                                                            'ride_time' => '',
                                                            'wait_time' => ''
                                                        ),
                                                        'fare_breakup' => array('min_km' => '',
                                                            'min_time' => '',
                                                            'min_fare' => '',
                                                            'per_km' => '',
                                                            'per_minute' => '',
                                                            'wait_per_minute' => '',
                                                            'peak_time_charge' => '',
                                                            'night_charge' => '',
                                                            'distance_unit' =>$distance_unit,
                                                            'duration_unit' => 'min',
                                                        ),
                                                        'tax_breakup' => array('service_tax' => ''),
                                                        'booking_information' => array('service_type' => $categoryResult->row()->name,
                                                            'service_id' => (string) $categoryResult->row()->_id,
                                                            'booking_date' => new \MongoDate(time()),
                                                            'pickup_date' => '',
                                                            'actual_pickup_date' => new \MongoDate($pickup_timestamp),
                                                            'est_pickup_date' => new \MongoDate($pickup_timestamp),
                                                            'booking_email' => $checkUser->row()->email,
                                                            'pickup' => array('location' => $pickup,
                                                                'latlong' => array('lon' => floatval($pickup_lon),
                                                                    'lat' => floatval($pickup_lat))
                                                            ),
                                                            'drop' => array('location' => (string)$drop_loc,
                                                                'latlong' => array('lon' => floatval($drop_lon),
                                                                    'lat' => floatval($drop_lat)
                                                                )
                                                            )
                                                        ),
                                                        'ride_status' => 'Booked',
                                                        'coupon_used' => $code_used,
                                                        'coupon' => array('code' => $code,
                                                            'type' => $coupon_type,
                                                            'amount' => floatval($coupon_amount)
                                                        ),
                                                        'payment_type' => $payment_type,
                                                        'category' => $category,
                                                        'timezone'  =>  $this->getTimezone($pickup_lat, $pickup_lon)
                                                    );
                                                    $this->app_model->simple_insert(RIDES, $bookingInfo);
                                                    $last_insert_id = $this->cimongo->insert_id();

                                                    $this->mail_model->user_ride_booked($checkUser->row()->_id, $ride_id);

                                                    if ($type == 0) {
                                                        $message = $this->format_string("Request for pickup user","request_pickup_user");
                                                        $response_time = $this->config->item('respond_timeout');
                                                        $options = array(
                                                            'ride_id' => $ride_id, 
                                                            'response_time' => $response_time, 
                                                            'pickup' => $pickup, 
                                                            'drop_loc' => $drop_loc
                                                        );
                                                        $user_type = 'driver';
                                                        foreach ($fcm_data as $fcm_token => $device_type) {
                                                            $this->notify($fcm_token, $message, 'ride_request', $options, $device_type, $user_type);
                                                        }
                                                        
                                                        if (!empty($push_driver)) {
                                                            foreach ($push_driver as $keys => $value) {
                                                                $condition = array('_id' => new \MongoId($value));
                                                                $this->cimongo->where($condition)->inc('req_received', 1)->update(DRIVERS);
                                                            }
                                                        }
                                                    }
                                                    if (isset($response_time)) {
                                                        if ($response_time <= 0) {
                                                            $response_time = 10;
                                                        }
                                                    } else {
                                                        $response_time = 10;
                                                    }
                                                    if (empty($riderlocArr)) {
                                                        $riderlocArr = json_decode("{}");
                                                    }

                                                    $returnArr['status'] = '1';
                                                    $returnArr['response'] = array('type' => (string) $type, 'response_time' => (string) $response_time + 10, 'ride_id' => (string) $ride_id, 'message' => $this->format_string('Booking Request Sent', 'booking_request_sent'), 'rider_location' => $riderlocArr);
                                                }
                                            } else {
                                                $returnArr['response'] = $this->format_string('No cabs available nearby', 'cabs_not_available_nearby');
                                            }
                                        } else {
                                            $returnArr['response'] = $this->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
                                        }
                                    }
                                    else {
                                        $returnArr['trip_estimate'] = (string) $trip_estimate;
                                        $returnArr['response'] = $canBook['error_message'];
                                    }
                                } else {
                                    $returnArr['response'] = $this->format_string('You have a payment pending for a previous trip');
                                }
                            } else {
                                $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                            }
                        } else {
                            $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
                        }
                    } else{
                        $returnArr['response'] = $this->format_string("You cannot book at this time", "cannot_book_at_this_time");
                    }
                } else {
                    $returnArr['status'] = '1';
                    $returnArr['acceptance'] = $acceptance;

                    $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'vehicle_type'));
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
                    } else {
                        if ($vehicle_type) {
                            $vehicle_image = $this->getVehicleImage($vehicle_type);
                        }
                    }

                    $driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
                        'driver_name' => (string) $this->get_driver_first_name($checkDriver->row()->driver_name),
                        'driver_email' => (string) $checkDriver->row()->email,
                        'driver_image' => (string) base_url() . $driver_image,
                        'driver_review' => (string) floatval($driver_review),
                        'driver_lat' => floatval($driver_lat),
                        'driver_lon' => floatval($driver_lon),
                        'min_pickup_duration' => $mindurationtext,
                        'ride_id' => (string) $ride_id,
                        'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
                        'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
                        'vehicle_model' => (string) $vehicle_model,
                        'vehicle_type' => $vehicle_type,
                        'vehicle_image' => $vehicle_image
                    );
                    /* Preparing driver information to share with user -- End */
                    if (empty($driver_profile)) {
                        $driver_profile = json_decode("{}");
                    }
                    if (empty($riderlocArr)) {
                        $riderlocArr = json_decode("{}");
                    }
                    $returnArr['response'] = array('type' => (string) $type, 'ride_id' => (string) $ride_id, 'message' => $this->format_string('ride confirmed', 'ride_confirmed'), 'driver_profile' => $driver_profile, 'rider_location' => $riderlocArr);
                }
                $returnArr['acceptance'] = $acceptance;
           } 
           else{
                if ($this->config->item('min_book_ahead_time') == 1) {
                    $hrsTense = 'hour';
                }
                else {
                    $hrsTense = 'hours';
                }
                $returnArr['response'] = $this->format_string("You can book ride only after " . $this->config->item('min_book_ahead_time') . " " . $hrsTense . " from now", "after_one_from_now");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This Function return the ride cancellation reson for users 
     *
     * */
    public function user_cancelling_reason() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $ride_id = $this->input->post('ride_id');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 1) {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email', 'user_name', 'country_code', 'phone_number'));
                if ($checkUser->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status','cancelled'));
                    if ($checkRide->num_rows() == 1) {
                        if ($checkRide->row()->ride_status != 'Cancelled') {
                            $reasonVal = $this->app_model->get_selected_fields(CANCELLATION_REASON, array('status' => 'Active', 'type' => 'user'), array('reason'));
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
                        }else{
                            $returnArr['response'] = $this->format_string('Already this ride has been cancelled', 'already_ride_cancelled');
                        }
                    }else{
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
     * This Function used for cancelling a ride by a user
     *
     * */
    public function cancelling_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $ride_id = $this->input->post('ride_id');
            $reason = $this->input->post('reason');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 3) {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email', 'user_name', 'country_code', 'phone_number'));
                if ($checkUser->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'driver.id', 'coupon_used', 'coupon', 'cancelled'));
                    if ($checkRide->num_rows() == 1) {

                        $doAction = 0;
                        if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived') {
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
                                    $rideDetails = array('ride_status' => 'Cancelled',
                                        'cancelled' => array('primary' => array('by' => 'User',
                                                'id' => $user_id,
                                                'reason' => $reason_id,
                                                'text' => $reason_text
                                            )
                                        ),
                                        'history.cancelled_time' => new \MongoDate(time())
                                    );
                                    $isPrimary = 'Yes';
                                } else if ($checkRide->row()->ride_status == 'Cancelled') {
                                    $rideDetails = array('cancelled.secondary' => array('by' => 'User',
                                            'id' => $user_id,
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
                                        $usage = array("user_id" => (string) $checkUser->row()->_id, "ride_id" => $ride_id);
                                        $promo_code = (string) $checkRide->row()->coupon['code'];
                                        $this->app_model->simple_pull(PROMOCODE, array('promo_code' => $promo_code), array('usage' => $usage));
                                    }
                                    if ($checkRide->row()->driver['id'] != '') {
                                        /* Update the driver status to Available */
                                        $driver_id = $checkRide->row()->driver['id'];
                                        $this->app_model->update_details(DRIVERS, array('mode' => 'Available'), array('_id' => new \MongoId($driver_id)));
                                    }

                                    /* Update the no of cancellation under this reason  */
                                    $this->app_model->update_user_rides_count('cancelled_rides', $user_id);
                                    if ($checkRide->row()->driver['id'] != '') {
                                        $this->app_model->update_driver_rides_count('cancelled_rides', $driver_id);
                                    }

                                    /* Update Stats Starts */
                                    $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                                    $field = array('ride_cancel.hour_' . date('H') => 1, 'ride_cancel.count' => 1);
                                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                                    /* Update Stats End */

                                    if ($checkRide->row()->driver['id'] != '') {
                                        $driver_id = $driver_id;
                                        $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'fcm_token', 'device_type'));

                                        if (isset($driverVal->row()->fcm_token)) {
                                            $message = $this->format_string("rider cancelled this ride","rider_cancelled_ride");
                                            $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
                                            $user_type = 'driver';
                                            $this->notify($driverVal->row()->fcm_token, $message, 'ride_cancelled', $options, $driverVal->row()->device_type, $user_type);
                                        }
                                    }
                                }

                                $returnArr['status'] = '1';
                                $returnArr['response'] = array('ride_id' => (string) $ride_id, 'message' => $this->format_string('Ride Cancelled', 'ride_cancelled'));
                            } else {
                                $returnArr['response'] = $this->format_string('You cannot do this action', 'you_cannot_do_this_action');
                            }
                        } else {
                            $returnArr['response'] = $this->format_string('Already this ride has been cancelled', 'already_ride_cancelled');
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
     * This Function used for delete a ride
     *
     * */
    public function delete_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $ride_id = $this->input->post('ride_id');

            if ($user_id != '' && $ride_id != '') {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email'));
                if ($checkUser->num_rows() == 1) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status'));
                    if ($checkRide->num_rows() == 1) {
                        if ($checkRide->row()->ride_status == 'Booked') {
                            /* Saving Unaccepted Ride for future reference */
                            save_ride_details_for_stats($ride_id);
                            /* Saving Unaccepted Ride for future reference */
                            $this->app_model->commonDelete(RIDES, array('ride_id' => $ride_id));
                            $returnArr['status'] = '1';
                            $returnArr['response'] = $this->format_string('Ride request cancelled', 'ride_request_cancelled');
                        } else {
                            $returnArr['response'] = $this->format_string('You cannot do this action', 'you_cannot_do_this_action');
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
     * This Function return the ride details
     *
     * */
    public function view_ride_information() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');

            $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
            if ($checkRide->num_rows() == 1) {

                if (!isset($checkRide->row()->timezone) || !$checkRide->row()->timezone) {
                    $lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
                    $lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
                    $this->setTimezone($lat, $lon);
                } else {
                    set_default_timezone($checkRide->row()->timezone);
                }
                
                $fareArr = array();
                $summaryArr = array();
                $min_short = $this->format_string('min', 'min_short');
                $mins_short = $this->format_string('mins', 'mins_short');
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
                            } else if($key=="ride_distance"){
                                $summaryArr[$key] = (string) $values;
                            }else{
                                $summaryArr[$key] = (string) $values;
                            }
                            
                        }
                    }
                }

                if (isset($checkRide->row()->total)) {
                    if (is_array($checkRide->row()->total)) {
                        if (isset($checkRide->row()->total['tips_amount'])) {
                            if ($checkRide->row()->total['tips_amount'] > 0) {
                                $tips_amount = $checkRide->row()->total['tips_amount'];
                                $fareArr['tips_amount'] = (string) floatval($tips_amount);
                            }
                        }

                        if (isset($checkRide->row()->total['total_fare'])) {
                            $total_bill = $checkRide->row()->total['total_fare'];
                            $fareArr['total_bill'] = (string) floatval($total_bill);
                        }
                        if (isset($checkRide->row()->total['coupon_discount'])) {
                            $coupon_discount = $checkRide->row()->total['coupon_discount'];
                            $fareArr['coupon_discount'] = (string) floatval($coupon_discount);
                        }
                        if (isset($checkRide->row()->total['grand_fare'])) {
                            $grand_bill = $checkRide->row()->total['grand_fare'];
                            $fareArr['grand_bill'] = (string) floatval($grand_bill);
                        }
                        if (isset($checkRide->row()->total['paid_amount'])) {
                            $total_paid = $checkRide->row()->total['paid_amount'];
                            $fareArr['total_paid'] = (string) floatval($total_paid);
                        }
                    }
                }
                
                $invoice_src = '';
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
                
                
                $disp_status = '';
                if ($checkRide->row()->ride_status == 'Booked') {
                   $disp_status = $this->format_string("Booked", "booked");
                        } else if ($checkRide->row()->ride_status == 'Confirmed') {
                    $disp_status = $this->format_string("Accepted", "accepted");
                } else if ($checkRide->row()->ride_status == 'Cancelled') {
                    $disp_status = $this->format_string("Cancelled", "cancelled");
                } else if ($checkRide->row()->ride_status == 'Completed') {
                     $disp_status = $this->format_string("Completed", "completed");
                    
                    $invoice_path = 'trip_invoice/'.$ride_id.'_path.jpg'; 
                    if(file_exists($invoice_path)) {
                        $invoice_src = base_url().$invoice_path;
                    } else {
                        $invoice_src = '';
                    }
                    
                } else if ($checkRide->row()->ride_status == 'Finished') {
                    $disp_status = $this->format_string("Awaiting Payment", "await_payment");
                } else if ($checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride') {
                    $disp_status = $this->format_string("On Ride", "on_ride");
                }

                $isFav = 0;
                $longitude = $checkRide->row()->booking_information['drop']['latlong']['lon'];
                $latitude = $checkRide->row()->booking_information['drop']['latlong']['lat'];
                $loc_key = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);
                $fav_condition = array('user_id' => new \MongoId($user_id));
                $checkUserInFav = $this->app_model->get_all_details(FAVOURITE, $fav_condition);
                if ($checkUserInFav->num_rows() > 0) {
                    if (isset($checkUserInFav->row()->fav_location)) {
                        if (array_key_exists($loc_key, $checkUserInFav->row()->fav_location)) {
                            $isFav = 1;
                        }
                    }
                }


                $doTrack = 0;
                if ((isset($checkRide->row()->driver['id']) && $checkRide->row()->driver['id'] != '') && ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Finished' || $checkRide->row()->ride_status == 'Onride')) {
                    $doTrack = 1;
                }
                $doAction = 0;
                        if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived') {
                    $doAction = 1;
                    if ($checkRide->row()->ride_status == 'Cancelled') {
                        $doAction = 0;
                    }
                }

                $pickup_date = '';
                $drop_date = '';
                        if ($checkRide->row()->ride_status == 'Booked' || $checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Cancelled' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Onride') {
                    $pickup_date = date('Y-m-d h:i:sA', $checkRide->row()->booking_information['est_pickup_date']->sec);
                } else {
                    $pickup_date = date('Y-m-d h:i:sA', $checkRide->row()->history['begin_ride']->sec);
                    $drop_date = date('Y-m-d h:i:sA', $checkRide->row()->history['end_ride']->sec);
                }
                
                $drop_arr = array();
                if($checkRide->row()->booking_information['drop']['location']!=''){
                    $drop_arr = $checkRide->row()->booking_information['drop'];
                }
                if (empty($drop_arr)) {
                    $drop_arr = json_decode("{}");
                }
                
                $fare_summary = array();
                
                
                if (isset($checkRide->row()->total['base_fare'])) {
                    if ($checkRide->row()->total['base_fare'] >= 0) {
                        $fare_summary[] = array("title"=>(string)$this->format_string("Base fare", "fare_summary_base_fare"),
                                                            "value"=>(string)round($checkRide->row()->total['base_fare'],2)
                                                            );
                    }
                }
                if (isset($checkRide->row()->total['peak_time_charge'])) {
                    if ($checkRide->row()->total['peak_time_charge'] > 0) {
                        $fare_summary[] = array("title"=>(string)$this->format_string("Peak time fare", "fare_summary_peak_time_fare").' ('.floatval($checkRide->row()->fare_breakup['peak_time_charge']).'X)',
                                                            "value"=>(string)round($checkRide->row()->total['peak_time_charge'],2)
                                                            );
                    }
                }
                if (isset($checkRide->row()->total['night_time_charge'])) {
                    if ($checkRide->row()->total['night_time_charge'] > 0) {
                        $fare_summary[] = array("title"=>(string)$this->format_string("Night time fare", "fare_summary_night_time_fare").' ('.floatval($checkRide->row()->fare_breakup['night_charge']).'X)',
                                                            "value"=>(string)round($checkRide->row()->total['night_time_charge'],2)
                                                            );
                    }
                }
                if (isset($checkRide->row()->total['total_fare'])) {
                    if ($checkRide->row()->total['total_fare'] >= 0) {
                        $fare_summary[] = array("title"=>(string)$this->format_string("Subtotal", "fare_summary_total"),
                                                            "value"=>(string)round($checkRide->row()->total['total_fare'],2)
                                                            );
                    }
                }
                
                if (isset($checkRide->row()->total['coupon_discount'])) {
                    if ($checkRide->row()->total['coupon_discount'] > 0) {
                        $fare_summary[] = array("title"=>(string)$this->format_string("Discount amount", "fare_summary_coupon_discount"),
                                                            "value"=>(string)round($checkRide->row()->total['coupon_discount'],2)
                                                            );
                    }
                }
                
                if (isset($checkRide->row()->total['grand_fare'])) {
                    if ($checkRide->row()->total['grand_fare'] >= 0) {
                        $fare_summary[] = array("title"=>(string)$this->format_string("Grand Total", "fare_summary_grand_fare"),
                                                            "value"=>(string)round($checkRide->row()->total['grand_fare'],2)
                                                            );
                    }
                }
                
                if (isset($checkRide->row()->total['tips_amount'])) {
                    if ($checkRide->row()->total['tips_amount'] > 0) {
                        $fare_summary[] = array("title"=>(string)$this->format_string("Tips amount", "fare_summary_tips"),
                                                            "value"=>(string)round($checkRide->row()->total['tips_amount'],2)
                                                            );
                    }
                }
                if (isset($checkRide->row()->total['wallet_usage'])) {
                    if ($checkRide->row()->total['wallet_usage'] > 0) {
                        $fare_summary[] = array("title"=>(string)$this->format_string("Wallet used amount", "fare_summary_wallet_used"),
                                                            "value"=>(string)round($checkRide->row()->total['wallet_usage'],2)
                                                            );
                    }
                }
                
                if (isset($checkRide->row()->total['paid_amount'])) {
                    if ($checkRide->row()->total['paid_amount'] >= 0) {
                        $fare_summary[] = array("title"=>(string)$this->format_string("Paid Amount", "fare_summary_paid_amount"),
                                                            "value"=>(string)round($checkRide->row()->total['paid_amount'],2)
                                                            );
                    }
                }
                
                $this->load->model('category_model');
                $category = $this->category_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($checkRide->row()->booking_information['service_id'])), array('icon_active'));
                $vehicle_image = base_url() . ICON_IMAGE . $category->row()->icon_active;

                if (isset($checkRide->row()->driver['id']) && $checkRide->row()->driver['id'] != '') {
                    $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($checkRide->row()->driver['id'])), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'vehicle_type'));
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

                    $vehicle_type = $this->getVehicleType($checkDriver->row()->vehicle_type);

                    $lat_lon = @explode(',', $checkRide->row()->driver['lat_lon']);
                    $driver_lat = $lat_lon[0];
                    $driver_lon = $lat_lon[1];

                    $driver_profile = array(
                        'driver_id' => (string) $checkDriver->row()->_id,
                        'driver_name' => (string) $this->get_driver_first_name($checkDriver->row()->driver_name),
                        'driver_email' => (string) $checkDriver->row()->email,
                        'driver_image' => (string) base_url() . $driver_image,
                        'driver_review' => (string) floatval($driver_review),
                        'driver_lat' => floatval($driver_lat),
                        'driver_lon' => floatval($driver_lon),
                        'min_pickup_duration' => $mindurationtext,
                        'ride_id' => (string) $ride_id,
                        'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
                        'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
                        'vehicle_model' => (string) $vehicle_model,
                        'vehicle_type' => $vehicle_type,
                        'vehicle_image' => $vehicle_image
                    );
                }

                if (isset($checkRide->row()->payment_type)) {
                    switch ($checkRide->row()->payment_type) {
                        case 'card':
                            $payment_type = 'Credit Card';
                            break;
                        case 'wallet':
                            $payment_type = 'Moovn Wallet';
                            break;
                        case 'cash':
                            $payment_type = 'Cash';
                            break;
                    }
                } else {
                    $payment_type = 'Cash';
                }
                $responseArr = array('currency' => $checkRide->row()->currency,
                    'cab_type' => $checkRide->row()->booking_information['service_type'],
                    'ride_id' => $checkRide->row()->ride_id,
                    'ride_status' => $checkRide->row()->ride_status,
                    'ride_type' => to_lowercase($checkRide->row()->type),
                    'disp_status' => (string) $disp_status,
                    'do_cancel_action' => (string) $doAction,
                    'do_track_action' => (string) $doTrack,
                    'is_fav_location' => (string) $isFav,
                    'pay_status' => $pay_status,
                    'disp_pay_status' => $disp_pay_status,
                    'pickup' => $checkRide->row()->booking_information['pickup'],
                    'drop' => $drop_arr,
                    'pickup_date' => (string) $pickup_date,
                    'drop_date' => (string) $drop_date,
                    'distance_unit' => $disp_distance_unit,
                    'invoice_src' => $invoice_src,
                    'vehicle_image' => $vehicle_image,
                    'payment_type'  =>  $payment_type,
                    'booking_date' => date('Y-m-d h:i:sA', $checkRide->row()->booking_information['booking_date']->sec)
                );

                if (!empty($summaryArr)) {
                    $responseArr = array_merge($responseArr, array('summary' => $summaryArr));
                }

                if (!empty($driver_profile)) {
                    $responseArr = array_merge($responseArr, array('driver_profile' => $driver_profile));
                }

                if (!empty($fare_summary)) {
                    $responseArr = array_merge($responseArr, array('fare_summary' => $fare_summary));
                }

                if (!empty($fareArr)) {
                    $responseArr = array_merge($responseArr, array('fare' => $fareArr));
                }

                if (empty($responseArr)) {
                    $responseArr = json_decode("{}");
                }
                $returnArr['status'] = '1';
                $returnArr['response'] = array('details' => $responseArr);
            } else {
                $returnArr['response'] = $this->format_string("Records not available", "no_records_found ");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This Function return the ride details
     *
     * */
    public function all_ride_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $type = (string) $this->input->post('type');
            if ($type == '')
                $type = 'all';

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('city', 'avail_category'));
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_ride_list($user_id, $type, array('booking_information', 'ride_id', 'ride_status', 'timezone'));
                    $rideArr = array();
                    if ($checkRide->num_rows() > 0) {
                        foreach ($checkRide->result() as $ride) {

                            if (!isset($ride->timezone) || !$ride->timezone) {
                                $lat = $ride->booking_information['pickup']['latlong']['lat'];
                                $lon = $ride->booking_information['pickup']['latlong']['lon'];
                                $this->setTimezone($lat, $lon);
                            } else {
                                set_default_timezone($ride->timezone);
                            }

                            $group = 'all';
                            if ($ride->ride_status == 'Booked' || $ride->ride_status == 'Confirmed' || $ride->ride_status == 'Arrived') {
                                if ($ride->ride_status == 'Booked' && ($ride->booking_information['est_pickup_date']->sec < time())) {
                                    $group = 'all';
                                } else {
                                    $group = 'upcoming';
                                }
                            } else if ($ride->ride_status == 'Completed' || $ride->ride_status == 'Finished') {
                                $group = 'completed';
                            }
                            $disp_status = '';
                            if ($ride->ride_status == 'Booked') {
                                $disp_status = $this->format_string("Booked", "booked");
                            } else if ($ride->ride_status == 'Confirmed') {
                                $disp_status = $this->format_string("Accepted", "accepted");
                            } else if ($ride->ride_status == 'Cancelled') {
                                $disp_status = $this->format_string("Cancelled", "cancelled");
                            } else if ($ride->ride_status == 'Completed') {
                                $disp_status = $this->format_string("Completed", "completed");
                            } else if ($ride->ride_status == 'Finished') {
                                $disp_status = $this->format_string("Awaiting Payment", "await_payment");
                            } else if ($ride->ride_status == 'Arrived' || $ride->ride_status == 'Onride') {
                                $disp_status = $this->format_string("On Ride", "on_ride");
                            }
                            
                            $ride_status = $ride->ride_status;
                            
                            if ($ride->ride_status != 'Expired') {
                                $rideArr[] = array(
                                    'ride_id' => $ride->ride_id,
                                    'ride_time' => date('h:i A', $ride->booking_information['est_pickup_date']->sec),
                                    'ride_date' => date("jS M, Y", $ride->booking_information['est_pickup_date']->sec),
                                    'pickup' => $ride->booking_information['pickup']['location'],
                                    'ride_status' => (string) $ride_status,
                                    'display_status' => (string) $disp_status,
                                    'group' => $group,
                                    'datetime' => date("d-m-Y", $ride->booking_information['est_pickup_date']->sec)
                                );
                            }
                        }
                    }
                    if (empty($rideArr)) {
                        $rideArr = json_decode("{}");
                    }
                    $total_rides = intval($checkRide->num_rows());
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('total_rides' => (string) $total_rides, 'rides' => $rideArr);
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
     * This Function return the invites page info
     *
     * */
    public function get_invites() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $location_result = $this->app_model->get_selected_fields(LOCATIONS, array('_id' => $this->user->location_id));
            $referral_credit_type = $location_result->row()->referral_credit_type;
            if ($referral_credit_type == 'instant') {
                $your_earn = 'Friend Joins';
                $your_earn_condition = 'instant';
            } else if ($referral_credit_type == 'on_first_ride') {
                $your_earn = 'Friend Rides';
                $your_earn_condition = 'on_first_ride';
            }
            $currencyCode = $this->data['dcurrencyCode'];
            if(isset($this->user->currency)) {
                $currencyCode = (string) $this->user->currency;
            }
            $subject = $this->config->item('email_title') . ' app invitation';
            $message = "Get MOOVN with the best ride in town! Use my code " . $this->user->unique_code . " for iOS: https://goo.gl/tffPSz or GooglePlay: https://goo.gl/vC1Rla";

            $welcome_amount = $location_result->row()->welcome_amount;
            $referal_amount = $location_result->row()->referral_credit;

            $detailsArr = array('friends_earn_amount' => floatval($welcome_amount),
                'your_earn' => $your_earn,
                'your_earn_condition' => $your_earn_condition,
                'your_earn_amount' => floatval($referal_amount),
                'referral_code' => $this->user->unique_code,
                'currency' => $currencyCode,
                'subject' => (string) $subject,
                'message' => (string) $message,
                'url' => base_url() . 'rider/signup?ref=' . base64_encode($this->user->unique_code)
            );
            if (empty($detailsArr)) {
                $detailsArr = json_decode("{}");
            }
            $returnArr['status'] = '1';
            $returnArr['response'] = array('details' => $detailsArr);
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This Function return the invites page info
     *
     * */
    public function get_earnings_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('city', 'unique_code','currency'));
                if ($userVal->num_rows() > 0) {
                    $earningsArr = array();
                    $wallet_amount = 0;
                    $walletAmt = $this->app_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
                    if ($walletAmt->num_rows() > 0) {
                        if (isset($walletAmt->row()->total)) {
                            $wallet_amount = $walletAmt->row()->total;
                        }
                    }
                    $referralArr = $this->app_model->get_all_details(REFER_HISTORY, array('user_id' => new \MongoId($user_id)));
                    if ($referralArr->num_rows() > 0) {
                        if (isset($referralArr->row()->history)) {
                            foreach ($referralArr->row()->history as $earn) {
                                if ($earn['used'] == 'true') {
                                    $amount = $earn['amount_earns'];
                                } else if ($earn['used'] == 'false') {
                                    $amount = 'joined';
                                }
                                $earningsArr = array('emil' => $earn['reference_mail'],
                                    'amount' => $amount
                                );
                            }
                        }
                    }
                    if (empty($earningsArr)) {
                        $earningsArr = json_decode("{}");
                    }
                    if (empty($wallet_amount)) {
                        $wallet_amount = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
                    $currencyCode=$this->data['dcurrencyCode'];
                    if(isset($userVal->row()->currency)) {
                        $currencyCode = (string)$userVal->row()->currency;
                    }
                    $returnArr['response'] = array('currency' => $currencyCode, 'wallet_amount' => round($wallet_amount,2), 'earnings' => $earningsArr);
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
     * This Function return the money/wallet page details
     *
     * */
    public function get_money_page() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('city', 'unique_code', 'stripe_customer_id','currency','currency_symbol'));
                if ($userVal->num_rows() > 0) {
                    $current_balance = 0;
                    $walletAmt = $this->app_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
                    if ($walletAmt->num_rows() > 0) {
                        if (isset($walletAmt->row()->total)) {
                            $current_balance = $walletAmt->row()->total;
                        }
                    }
                    $wallet_min_amount = floatval($this->config->item('wal_recharge_min_amount'));
                    $wallet_max_amount = floatval($this->config->item('wal_recharge_max_amount'));
                    $wallet_middle_amount = floatval(($this->config->item('wal_recharge_max_amount') + $this->config->item('wal_recharge_min_amount')) / 2);


                    if ($wallet_max_amount != '' && $wallet_max_amount != '') {
                        $wallet_money = array('min_amount' => $wallet_min_amount, 'middle_amount' => round($wallet_middle_amount), 'max_amount' => $wallet_max_amount,);
                    } else {
                        $wallet_money = array();
                    }

                    $stripe_customer_id = '';
                    if (isset($userVal->row()->stripe_customer_id)) {
                        $stripe_customer_id = $userVal->row()->stripe_customer_id;
                    }

                    $auto_charge_status = '0';
                    if ($this->data['auto_charge'] == 'Yes' && $stripe_customer_id != '') {
                        $auto_charge_status = '1';
                    }

                    $returnArr['auto_charge_status'] = $auto_charge_status;
                    
                    $currency = $this->data['dcurrencyCode'];
                    if(isset($userVal->row()->currency)){
                        $currency = $userVal->row()->currency;
                    }

                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('currency' => $currency,
                        'current_balance' => number_format($current_balance, 2),
                        'recharge_boundary' => $wallet_money
                    );
                } else {
                    $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
            #echo '<pre>'; print_r($returnArr); die;
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
    public function get_transaction_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $type = (string) $this->input->post('type');


            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('currency','currency_symbol'));
                if ($userVal->num_rows() > 0) {
                    $checkList = $this->app_model->get_transaction_lists($user_id, $type, array('user_id', 'total', 'transactions'));
                    $transArr = array();
                    $total_amount = 0;
                    $total_transaction = 0;

                    if ($checkList->num_rows() > 0) {
                        $total_amount = $checkList->row()->total;
                        if (isset($checkList->row()->transactions)) {
                            $transactions = array_reverse($checkList->row()->transactions);
                            foreach ($transactions as $trans) {
                                $title = '';
                                if ($trans['type'] == 'CREDIT') {
                                    if ($trans['credit_type'] == 'welcome') {
                                        $title = $this->format_string("Welcome bonus", "welcome_bonus");
                                    } else if ($trans['credit_type'] == 'referral') {
                                        $refVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($trans['ref_id'])), array('user_name'));
                                        $title = $this->format_string("Referral reward", "referral_reward");
                                        if ($refVal->num_rows() > 0) {
                                            if (isset($refVal->row()->user_name)) {
                                                $title.=' : ' . $refVal->row()->user_name;
                                            }
                                        }
                                    } else if ($trans['credit_type'] == 'Promo') {
                                        $title = $this->format_string('Promo');
                                    } else {
                                        $title = $this->format_string("Recharge", "recharge");
                                    }
                                } else if ($trans['type'] == 'DEBIT') {
                                    if ($trans['debit_type'] == 'payment') {
                                        $title = 'Trip ID: #' . $trans['ref_id'];
                                    }
                                }
                                $transArr[] = array('type' => (string) $trans['type'],
                                    'trans_amount' => (string) $trans['trans_amount'],
                                    'title' => (string) $title,
                                    'trans_date' => (string) date("jS M, Y", $trans['trans_date']->sec),
                                    'balance_amount' => (string) $trans['avail_amount']
                                );
                            }
                            $total_transaction = count($checkList->row()->transactions);
                        }
                    }
                    
                    $currency = $this->data['dcurrencyCode'];
                    if(isset($userVal->row()->currency)){
                        $currency = $userVal->row()->currency;
                    }
                    
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('currency' => $currency,
                        'total_amount' => $total_amount,
                        'total_transaction' => $total_transaction,
                        'trans' => $transArr
                    );
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
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');


            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('stripe_customer_id'));
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
                    if ($checkRide->num_rows() == 1) {
                        $having_card = 'No';
                        if (isset($userVal->row()->stripe_customer_id)) {
                            $stripe_customer_id = $userVal->row()->stripe_customer_id;
                            if ($stripe_customer_id != '') {
                                $have_con_cards = $this->get_stripe_card_details($stripe_customer_id);
                                if($have_con_cards['error_status']=='1' && count($have_con_cards['result']) > 0){
                                    $having_card = 'Yes';
                                }
                            }
                        }
                        
                        $pay_by_cash_req = 'No';
                        if(isset($checkRide->row()->pay_by_cash)){
                            $pay_by_cash_req = $checkRide->row()->pay_by_cash;
                        }

                        $pay_amount = $checkRide->row()->total['grand_fare'];
                        $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
                        $avail_amount = 0;
                        if (isset($walletDetail->row()->total)) {
                            $avail_amount = $walletDetail->row()->total;
                        }



                        $paymentArr = array();
                        $pay_by_cash = 'Disable';
                        $use_wallet_amount = 'Disable';
                        if ($this->config->item('pay_by_cash') != '' && $this->config->item('pay_by_cash') != 'Disable') {
                            if($pay_by_cash_req=='No'){
                                $pay_by_cash = $this->format_string('Pay by Cash', 'pay_by_cash');
                                $paymentArr[] = array('name' => $pay_by_cash, 'code' => 'cash');
                            }
                        }
                        if (0 < $avail_amount) {
                            if ($this->config->item('use_wallet_amount') != '' && $this->config->item('use_wallet_amount') != 'Disable') {
                                $user_my_wallet = $this->format_string('Use my wallet/money', 'user_my_wallet');
                                $paymentArr[] = array('name' => $user_my_wallet . ' (' . $this->data['dcurrencySymbol'] . $avail_amount . ')', 'code' => 'wallet');
                            }
                        }
                        $getPaymentgatway = $this->app_model->get_all_details(PAYMENT_GATEWAY, array('status' => 'Enable'));
                        
                        if ($this->data['auto_charge'] == "Yes") {
                            if($having_card == 'Yes') $gateway_number = 'auto_detect'; else $gateway_number = 3;
                            $pay_by_card = $this->format_string('Pay by Card', 'pay_by_card');
                            $paymentArr[] = array('name' => $pay_by_card, 'code' => (string)$gateway_number);
                        } else {
                            if ($getPaymentgatway->num_rows() > 0) {
                                foreach ($getPaymentgatway->result() as $row) {
                                    $paymentArr[] = array('name' => $row->gateway_name, 'code' => (string)$row->gateway_number);
                                }
                            }
                        }
                        
                        $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('stripe_customer_id'));
                        $having_card = 'No';
                        if ($userVal->num_rows() > 0) {
                            if (isset($userVal->row()->stripe_customer_id)) {
                                $stripe_customer_id = $userVal->row()->stripe_customer_id;
                                if ($stripe_customer_id != '') {
                                    $having_card = 'Yes';
                                }
                            }
                        }
                        $stripe_connected = 'No';
                        if($this->data['auto_charge'] == 'Yes'){
                            if($having_card == 'Yes'){
                                $stripe_connected = 'Yes';
                            }
                        }
                        $user_timeout = $this->data['user_timeout'];

                        
                        if (empty($paymentArr)) {
                            $paymentArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('payment' => $paymentArr,
                                                        'stripe_connected'=>(string)$stripe_connected,
                                                        'payment_timeout'=>(string)$user_timeout
                                                    );
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
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
     * This Function process the wallet usage for payment
     *
     * */
    public function payment_by_wallet() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array());
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
                    if ($checkRide->num_rows() == 1) {
                        $walletVal = $this->app_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
                        if ($walletVal->num_rows() == 1) {
                            $wallet_amount = 0.00;
                            $ride_charge = 0.00;
                            if (isset($walletVal->row()->total)) {
                                $wallet_amount = round($walletVal->row()->total,2);
                            }
                            if (isset($checkRide->row()->total['grand_fare'])) {
                                $ride_charge = floatval($checkRide->row()->total['grand_fare']);
                            }
                            $tips_amt = 0.00;
                            if (isset($checkRide->row()->total['tips_amount'])) {
                                if ($checkRide->row()->total['tips_amount'] > 0) {
                                    $tips_amt = $checkRide->row()->total['tips_amount'];
                                }
                            }
                            $ride_charge = $ride_charge + $tips_amt;

                            if ($wallet_amount > 0 && $ride_charge > 0) {
                                if ($ride_charge <= $wallet_amount) {
                                    $pay_summary = array('type' => 'Wallet');
                                    $paymentInfo = array('ride_status' => 'Completed',
                                        'pay_status' => 'Paid',
                                        'history.wallet_usage_time' => new \MongoDate(time()),
                                        'total.wallet_usage' => $ride_charge,
                                        'pay_summary' => $pay_summary
                                    );
                                    /* Update the user wallet */
                                    $currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
                                    $avail_amount = 0.00;
                                    if ($currentWallet->num_rows() > 0) {
                                        if (isset($currentWallet->row()->total)) {
                                            $avail_amount = floatval($currentWallet->row()->total);
                                        }
                                    }
                                    if ($avail_amount > 0) {
                                        $this->app_model->update_wallet((string) $user_id, 'DEBIT', floatval($avail_amount - $ride_charge));
                                    }
                                    $currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
                                    $avail_amount = 0.00;
                                    if ($currentWallet->num_rows() > 0) {
                                        if (isset($currentWallet->row()->total)) {
                                            $avail_amount = floatval($currentWallet->row()->total);
                                        }
                                    }
                                    $walletArr = array('type' => 'DEBIT',
                                        'debit_type' => 'payment',
                                        'ref_id' => $ride_id,
                                        'trans_amount' => floatval($ride_charge),
                                        'avail_amount' => floatval($avail_amount),
                                        'trans_date' => new \MongoDate(time())
                                    );
                                    $this->app_model->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $walletArr));
                                    $transactionArr = array('type' => 'wallet',
                                        'amount' => floatval($ride_charge),
                                        'trans_date' => new \MongoDate(time())
                                    );
                                    $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
                                    $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
                                    if (ENABLE_DRIVER_OUSTANDING_UPDATE) {
                                        $driver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($checkRide->row()->driver['id'])));
                                        $this->load->model('driver_model');
                                        $this->driver_model->update_outstanding_amount($driver->row(), $checkRide->row(), 'wallet');
                                    }
                                    $updateUser = array('pending_payment' => 'false');
                                    $this->user_model->update_details(USERS, $updateUser, array('_id' => new \MongoId($user_id)));
                                    
                                    $driver_id = $checkRide->row()->driver['id'];
                                    $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'fcm_token', 'device_type'));

                                    /* Update Stats Starts */
                                    $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                                    $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                                    /* Update Stats End */

                                    if ($driverVal->num_rows() > 0) {
                                        if (isset($driverVal->row()->fcm_token)) {
                                            $message = $this->format_string("payment successfully completed", "payment_completed");
                                            $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
                                            $user_type = 'driver';
                                            $this->notify($driverVal->row()->fcm_token, $message, 'payment_paid', $options, $driverVal->row()->device_type, $user_type);
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
                                    $returnArr['response'] = $this->format_string('payment successfully completed', 'payment_completed');
                                } else if ($ride_charge > $wallet_amount) {
                                    $returnArr['status'] = '0';
                                    $returnArr['response'] = $this->format_string('Insufficient Mobile Money Balance', 'wallet_empty');
                                }
                                if (isset($userVal->row()->currency)) {
                                    $returnArr['currency'] = (string)$userVal->row()->currency;
                                } else {
                                    $returnArr['currency'] = (string) $this->data['dcurrencyCode'];
                                }
                            } else {
                                $returnArr['response'] = $this->format_string("Insufficient Mobile Money Balance", "wallet_empty");
                            }
                            $returnArr['ride_total'] = (string) $ride_charge;
                        } else {
                            $returnArr['response'] = $this->format_string("Insufficient Mobile Money Balance", "wallet_empty");
                        }
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
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
     * This Function process the wallet usage for payment
     *
     * */
    public function payment_by_cash() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');


            if ($user_id != '' && $ride_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array());
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
                    if ($checkRide->num_rows() == 1) {

                        $driver_id = $checkRide->row()->driver['id'];
                        $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'fcm_token', 'device_type'));

                        if (isset($driverVal->row()->fcm_token)) {
                            $message = $this->format_string("rider wants to pay by cash", "rider_want_pay_by_cash");
                            $amount_to_receive = 0.00;
                            $tips_amt = 0.00;
                            if (isset($checkRide->row()->total['tips_amount'])) {
                                if ($checkRide->row()->total['tips_amount'] > 0) {
                                    $tips_amt = $checkRide->row()->total['tips_amount'];
                                }
                            }
                            #$amount_to_receive = $amount_to_receive + $tips_amt;
                            if (isset($checkRide->row()->total)) {
                                if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
                                    $amount_to_receive = ($checkRide->row()->total['grand_fare'] + $tips_amt) - $checkRide->row()->total['wallet_usage'];
                                    
                                    $amount_to_receive = round($amount_to_receive,2);
                                }
                            }
                            

                            $currency = (string) $checkRide->row()->currency;
                            $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id, 'amount' => (string) $amount_to_receive, 'currency' => $currency);
                            $user_type = 'driver';
                            $this->notify($driverVal->row()->fcm_token, $message, 'receive_cash', $options, $driverVal->row()->device_type, $user_type);
                                
                            $payArr = array('pay_by_cash'=>'Yes');
                            $this->app_model->update_details(RIDES, $payArr, array('ride_id' => $ride_id));
                        }

                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('Pay your bill by cash', 'pay_bill_by_cash');
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
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
     * This Function process the strip auto payment deduct
     *
     * */
    public function payment_by_auto_charge() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');

            if ($user_id != '' && $ride_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array());
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id), array('total'));
                    #echo '<pre>'; print_r($checkRide->row()); die;
                    if ($checkRide->num_rows() == 1) {
                        $grand_fare = $checkRide->row()->total['grand_fare'];
                        $paid_amount = $checkRide->row()->total['paid_amount'];
                        $wallet_amount = $checkRide->row()->total['wallet_usage'];

                        $tips_amt = 0.00;
                        if (isset($checkRide->row()->total['tips_amount'])) {
                            if ($checkRide->row()->total['tips_amount'] > 0) {
                                $tips_amt = $checkRide->row()->total['tips_amount'];
                            }
                        }
                        $grand_fare = $grand_fare + $tips_amt;
                        
                        $pay_amount = $grand_fare - ($paid_amount + $wallet_amount);

                        if ($pay_amount > 0) {
                            // Stripe Payment Process Starts here (Auto charge)
                            $paymentData = array('user_id' => $user_id, 'ride_id' => $ride_id, 'total_amount' => $pay_amount);
                            $pay_response = $this->common_auto_stripe_payment_process($paymentData);
                        } else {
                            $pay_response['status'] = '1';
                            $pay_response['msg'] = $this->format_string('This ride has been paid already', 'ride_has_been_paid_already');
                        }
                        $returnArr['status'] = $pay_response['status'];
                        $returnArr['response'] = $pay_response['msg'];
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
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
     * Setting values for payment
     *
     * */
    public function payment_by_gateway() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = (string) $this->input->post('user_id');
            $ride_id = (string) $this->input->post('ride_id');
            $payment = (string) $this->input->post('gateway');

            if ($payment != '' && $ride_id != '' && $user_id != '') {
                $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
                if ($checkRide->num_rows() == 1) {
                    $driver_id = $checkRide->row()->driver['id'];
                    $paymentVal = $this->app_model->get_all_details(PAYMENT_GATEWAY, array('status' => 'Enable', 'gateway_number' => $payment));
                    if ($paymentVal->num_rows() > 0) {
                        $payment_name = $paymentVal->row()->gateway_name;
                        $pay_amount = 0.00;
                        if (isset($checkRide->row()->total)) {
                            if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
                                $pay_amount = round(($checkRide->row()->total['grand_fare'] - $checkRide->row()->total['wallet_usage']), 2);
                            }
                        }
                        
                        $tips_amt = 0.00;
                        if (isset($checkRide->row()->total['tips_amount'])) {
                            if ($checkRide->row()->total['tips_amount'] > 0) {
                                $tips_amt = $checkRide->row()->total['tips_amount'];
                            }
                        }
                        
                        $payArr = array('user_id' => $user_id,
                            'driver_id' => $driver_id,
                            'ride_id' => $ride_id,
                            'payment_id' => $payment,
                            'payment' => $payment_name,
                            'amount' => $pay_amount,
                            'tips_amount' => $tips_amt,
                            'dateAdded' => new \MongoDate(time())
                        );
                        $this->app_model->simple_insert(MOBILE_PAYMENT, $payArr);
                        $mobile_id = $this->cimongo->insert_id();
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string('Payment Initiated', 'payment_initiated');
                        $returnArr['mobile_id'] = (string) $mobile_id;
                    } else {
                        $returnArr['response'] = $this->format_string('Payment method currently unavailable', 'payment_method_unavailable');
                    }
                } else {
                    $returnArr['response'] = $this->format_string('Authentication Failed', 'authentication_failed');
                }
            }else{
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
     * Mail Invoice
     *
     * */
    public function mail_invoice() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            $email = $this->input->post('email');
            if ($ride_id != '' && $email != '') {
                $this->mail_model->send_invoice($ride_id, $email);
                $returnArr['status'] = '1';
                $returnArr['response'] = $this->format_string('Mail sent', 'mail_sent');
            } else {
                $returnArr['response'] = $this->format_string('Mail not sent', 'mail_not_sent');
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function track_driver_location() {
        $ride_id = $this->input->post('ride_id');
        if ($ride_id == '') {
            $ride_id = $this->input->get('ride_id');
        }
        $returnArr['status'] = '0';
        if ($ride_id != '') {
            $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'driver', 'coupon_used', 'coupon', 'cancelled', 'category'));
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
                    if (!empty($routes)) {
                        usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
                        $mindurationtext = $routes[0]->legs[0]->duration->text;
                    }

                    $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model', 'vehicle_type'));
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
                    $vehicleModel = $this->app_model->get_selected_fields(MODELS, array('_id' => new \MongoId($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
                    $vehicle_model = '';
                    if ($vehicleModel->num_rows() > 0) {
                        $vehicle_model = $vehicleModel->row()->name;
                    }
                    
                    $pickup_arr = array();
                    if ($checkRide->row()->booking_information['pickup']['location']!='') {
                        $pickup_arr = $checkRide->row()->booking_information['pickup'];
                    }
                    if (empty($pickup_arr)) {
                        $pickup_arr = json_decode("{}");
                    }
                    
                    $drop_arr = array();
                    if ($checkRide->row()->booking_information['drop']['location']!='') {
                        $drop_arr = $checkRide->row()->booking_information['drop'];
                    }
                    if (empty($drop_arr)) {
                        $drop_arr = json_decode("{}");
                    }

                    $this->load->model('vehicle_model');
                    $vehicle = $this->vehicle_model->get_selected_fields(VEHICLES, array('_id' => new \MongoId($checkDriver->row()->vehicle_type)), array('vehicle_type', 'icon'));
                    $vehicle_type = '';
                    if ($vehicle->num_rows() > 0) {
                        $vehicle_type = $vehicle->row()->vehicle_type;
                    } else {
                        $vehicle_type = null;
                    }

                    $this->load->model('category_model');
                    
                    if (isset($checkRide->row()->category) && !empty($checkRide->row()->category)) {
                        $category = $this->category_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($checkRide->row()->category)), array('icon_active'));
                        $vehicle_image = '';
                        if ($category->num_rows() > 0) {
                            $vehicle_image = base_url() . ICON_IMAGE . $category->row()->icon_active;
                        } else {
                            $vehicle_image = null;
                        }
                    } else {
                        $category = $this->category_model->get_selected_fields(CATEGORY, array('name' => $vehicle_type), array('icon_active'));
                        $vehicle_image = '';
                        if ($category->num_rows() > 0) {
                            $vehicle_image = base_url() . ICON_IMAGE . $category->row()->icon_active;
                        } else {
                            $vehicle_image = null;
                        }
                    }

                    $driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
                        'driver_name' => (string) $this->get_driver_first_name($checkDriver->row()->driver_name),
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
                        'vehicle_type' => $vehicle_type,
                        'vehicle_image' => $vehicle_image,
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
                    $msg = $this->format_string('Your ride has been successfully shared with', 'ride_successfully_shared_with');
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

    public function apply_tips_amount() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            $tips_amount = $this->input->post('tips_amount');
            if ($ride_id != '' && $tips_amount != '') {
                $cond = array('ride_id' => $ride_id);
                $rideInfo = $this->app_model->get_selected_fields(RIDES, $cond, array('total','pay_status'));
                if ($rideInfo->num_rows() > 0) {
                    if($rideInfo->row()->pay_status == 'Pending'){
                      $dataArr = array('total.tips_amount' => floatval($tips_amount));
                        $this->app_model->update_details(RIDES, $dataArr, $cond);
                        $responseArr['response']['tips_amount'] = (string) number_format($tips_amount, 2, '.', '');
                        $responseArr['response']['total'] = (string) number_format(($rideInfo->row()->total['grand_fare']+$tips_amount), 2, '.', '');
                        $responseArr['response']['tip_status'] = '1';
                        $responseArr['response']['msg'] = $this->format_string('tips added successfully','tips_added');
                        $responseArr['status'] = '1';
                    } else {
                        $responseArr['response'] = $this->format_string('You Can\'t apply tips amount right now.','cant_apply_tips');
                    }
                  } else {
                    $responseArr['response'] = $this->format_string('Records not available.','no_records_found');
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
     * This Function applying the tips amount for driver
     *
     * */    
    public function remove_tips_amount() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            if ($ride_id != '') {
                $cond = array('ride_id' => $ride_id);
                $rideInfo = $this->app_model->get_selected_fields(RIDES, $cond, array('total'));
                if ($rideInfo->num_rows() > 0) {
                    $dataArr = array('total.tips_amount' => floatval(0));
                    $this->app_model->update_details(RIDES, $dataArr, $cond);

                    $responseArr['response']['tips_amount'] = '0.00';
                    $responseArr['response']['total'] = (string) number_format($rideInfo->row()->total['grand_fare'], 2, '.', '');
                    $responseArr['response']['tip_status'] = '0';

                    $responseArr['response']['msg'] = $this->format_string('tips removed successfully','tips_removed');
                    $responseArr['status'] = '1';
                } else {
                    $responseArr['response'] = $this->format_string('Records not available.','no_records_found');
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
     * This Function return the fare breakup details of a particular ride
     *
     * */
     
    public function get_fare_breakup() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            $user_id = $this->input->post('user_id');
            if ($user_id != '' && $ride_id != '') {
                $cond = array('user.id' => $user_id,'ride_id' => $ride_id);
                $rideInfo = $this->app_model->get_all_details(RIDES, $cond);
                if ($rideInfo->num_rows() > 0) {
                    if ($rideInfo->row()->ride_status =='Finished') {   #   Finished
                        $locationArr = array();
                        $driverinfoArr = array();
                        $fareArr = array();
                        
                        $tips_amount = 0.00;
                        if(isset($rideInfo->row()->total['tips_amount'])){
                            $tips_amount = $rideInfo->row()->total['tips_amount'];
                        }
                        
                        $driverInfo = $this->app_model->get_selected_fields(DRIVERS, array('_id'=>new \MongoId($rideInfo->row()->driver['id'])),array('image'));
                        $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                        if (isset($driverInfo->row()->image)) {
                            if ($driverInfo->row()->image != '') {
                                $driver_image = USER_PROFILE_IMAGE . $driverInfo->row()->image;
                            }
                        }
                        $driver_ratting = 0;
                        if (isset($driverInfo->row()->avg_review)) {
                            if ($driverInfo->row()->avg_review != '') {
                                $driver_ratting = $driverInfo->row()->avg_review;
                            }
                        }
                        
                        $locationArr = array ( 'pickup_lat'=>(string)$rideInfo->row()->booking_information['pickup']['latlong']['lat'],
                            'pickup_lon'=>(string)$rideInfo->row()->booking_information['pickup']['latlong']['lon'],
                            'drop_long'=>(string)$rideInfo->row()->booking_information['drop']['latlong']['lat'],
                            'drop_lon'=>(string)$rideInfo->row()->booking_information['drop']['latlong']['lon']
                        );
                        $driverinfoArr = array ( 'name'=>(string)$rideInfo->row()->driver['name'],
                                                            'image'=>(string) base_url().$driver_image,
                                                            'ratting'=>(string) $driver_ratting,
                                                            'contact_number'=>(string)$rideInfo->row()->driver['phone'],
                                                            'cab_no'=>(string)$rideInfo->row()->driver['vehicle_no'],
                                                            'cab_model'=>(string)$rideInfo->row()->driver['vehicle_model']
                                                        );
                        
                        $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('stripe_customer_id'));
                        $having_card = 'No';
                        if ($userVal->num_rows() > 0) {
                            if (isset($userVal->row()->stripe_customer_id)) {
                                $stripe_customer_id = $userVal->row()->stripe_customer_id;
                                if ($stripe_customer_id != '') {
                                    ### Check the customer id is in merchant account    ###
                                    $have_con_cards = $this->get_stripe_card_details($stripe_customer_id);
                                    if($have_con_cards['error_status']=='1' && count($have_con_cards['result']) > 0){
                                        $having_card = 'Yes';
                                    }
                                }
                            }
                        }                   
                        $stripe_connected = 'No';
                        if($this->data['auto_charge'] == 'Yes'){
                            if($having_card == 'Yes'){
                                $stripe_connected = 'Yes';
                            }
                        }
                        $user_timeout = $this->data['user_timeout'];

                        
                        $distance_unit = $this->data['d_distance_unit'];
                        if(isset($rideInfo->row()->fare_breakup['distance_unit'])){
                            $distance_unit = $rideInfo->row()->fare_breakup['distance_unit'];
                        }
                        if($distance_unit == 'km'){
                            $distance_km = $this->format_string('km', 'km');
                        } else if($distance_unit == 'mi') {
                            $distance_km = $this->format_string('mi', 'mi');
                        }
                        $invoice_src = '';
                        if ($rideInfo->row()->ride_status == 'Completed') {
                            $invoice_path = 'trip_invoice/'.$ride_id.'_path.jpg'; 
                            if(file_exists($invoice_path)) {
                                $invoice_src = base_url().$invoice_path;
                            }
                        }
                        
                        $min_short = $this->format_string('min', 'min_short');
                        $mins_short = $this->format_string('mins', 'mins_short');
                        $ride_duration_unit = $min_short;
                        if($rideInfo->row()->summary['ride_duration']>1){
                            $ride_duration_unit = $mins_short;
                        }
                        
                        $trip_total = $rideInfo->row()->total['grand_fare'] + $tips_amount;
                        
                        $fareArr = array ('cab_type'=>(string)$rideInfo->row()->booking_information['service_type'],
                            'trip_date'=>(string) date("d-m-Y",$rideInfo->row()->booking_information['pickup_date']->sec),
                            'base_fare'=>(string)number_format($rideInfo->row()->total['base_fare'], 2, '.', ''),
                            'ride_duration'=>(string)$rideInfo->row()->summary['ride_duration'],
                            'ride_duration_unit'=>(string)$ride_duration_unit,
                            'time_fare'=>(string)number_format($rideInfo->row()->total['ride_time'], 2, '.', ''),
                            'ride_distance'=>(string)$rideInfo->row()->summary['ride_distance'],
                            'distance_fare'=>(string)number_format($rideInfo->row()->total['distance'], 2, '.', ''),
                            'tax_amount'=>(string)number_format($rideInfo->row()->total['service_tax'], 2, '.', ''),
                            'tip_amount'=>(string)number_format($tips_amount, 2, '.', ''),
                            'coupon_amount'=>(string)number_format($rideInfo->row()->total['coupon_discount'], 2, '.', ''),
                            'sub_total'=>(string)number_format($rideInfo->row()->total['total_fare'], 2, '.', ''),
                            'total'=>(string)number_format($trip_total, 2, '.', ''),
                            'wallet_usage'=>(string) number_format($rideInfo->row()->total['wallet_usage'], 2, '.', ''),
                            'stripe_connected'=>(string)$stripe_connected,
                            'braintree_connected'=>(string)$this->data['bt_auto_charge'],
                            'payment_timeout'=>(string)$user_timeout,
                            'distance_unit'=>(string)$distance_km,
                            'invoice_src' => $invoice_src                           
                        );
                        
                        $currency = $this->data['dcurrencyCode'];
                        if(isset($rideInfo->row()->currency)){
                            $currency = $rideInfo->row()->currency;
                        }
                        
                        if(empty($locationArr)){
                            $locationArr = json_decode("{}");
                        }
                        if(empty($driverinfoArr)){
                            $driverinfoArr = json_decode("{}");
                        }
                        if(empty($fareArr)){
                            $fareArr = json_decode("{}");
                        }
                        $responseArr['status'] = '1';
                        $responseArr['response'] = array(
                            'currency' => $currency,
                            'payment_type' => $rideInfo->row()->payment_type,
                            'location' => $locationArr,
                            'driverinfo' => $driverinfoArr,
                            'fare' => $fareArr
                        );
                    } else {
                        $responseArr['response'] = $this->format_string('You cannot make the payment for this trip now.','cannot_make_payment_now');
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Records not available.','no_records_found');
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

    public function view()
    {
        $code = $this->input->post('code');
        $email = $this->input->post('email');

        if ($code == 'pyGMiYbUru') {
            $userVal = $this->app_model->get_selected_fields(USERS, array('email' => $email));
            $json_encode = json_encode($userVal->row(), JSON_PRETTY_PRINT);
            echo $this->cleanString($json_encode);
        } else {
            show_404();
        }
    }

    public function change_email()
    {
        $status = false;

        $input_params = $this->get_raw_request();
        
        if (!isset($input_params->email)) {
            $message = 'Missing Email Parameter in request.';
        } else {
            $email = $input_params->email;
            if (!valid_email($email)) {
                $message = 'Incorrect Email Format.';
            } else if ($email === $this->user->email) {
                $message = 'Email is same as the current one.';
            } else {
                $user_exist = $this->user_model->check_user_exist(array('email' => $email));
                if ($user_exist->num_rows() === 0) {
                    // update user
                    $update_data = array('email' => $email);
                    $condition = array('_id' => new \MongoId($this->user->_id));                    
                    if ($this->user_model->update_details(USERS, $update_data, $condition)) {
                        $status = true;
                        $message = 'Updated Successfully';
                    }
                } else {
                    $message = 'Email Exists. Use another email.';
                }
            }
        }
        $response = array('message' => $message);
        $json_encode = json_encode($response, JSON_PRETTY_PRINT);

        if ($status == false) {
            http_response_code(400);
        }
        echo $this->cleanString($json_encode);
    }

    public function update_drop_address()
    {
        $status = false;
        $input_params = $this->get_raw_request();

        if (!isset($input_params->drop_loc) || empty(trim($input_params->drop_loc)) 
            || !isset($input_params->drop_lat) || empty(trim($input_params->drop_lat))  
            || !isset($input_params->drop_lon) || empty(trim($input_params->drop_lon))  
            || !isset($input_params->ride_id) || empty(trim($input_params->ride_id))) {
            $message = 'Missing Parameters in request.';
        } else {
            $user_id = $this->user->_id->{'$id'};
            $find_conditions = array(
                'ride_id' => $input_params->ride_id,
                'user.id' => $user_id
            );
            $ride_info = $this->app_model->get_all_details(RIDES, $find_conditions);
            if ($ride_info->num_rows() == 0) {
                $message = 'Could not find ride';
            } else if ($ride_info->row()->type !== 'Later') {
                $message = 'You can only update reserved rides';
            } else if ($ride_info->num_rows() > 1) {
                $message = 'Multiple Rides with same ID found';
            } else if ($ride_info->num_rows() == 1) {
                $valid_statuses = array('Confirmed', 'Booked', 'Arrived', 'Onride');
                if (in_array($ride_info->row()->ride_status, $valid_statuses)) {
                    $ride_data = array(
                        'booking_information.drop' => array(
                            'location'  => (string) $input_params->drop_loc,
                            'latlong'   => array(
                                'lon' => floatval($input_params->drop_lon),
                                'lat' => floatval($input_params->drop_lat)
                            )
                        )
                    );
                    $update_conditions = array('ride_id' => $input_params->ride_id);
                    $update_ride_result = $this->app_model->update_details(RIDES, $ride_data, $update_conditions);
                    if ($update_ride_result) {
                        $status = true;
                        $message = 'Updated Successfully';
                    } else {
                        $message = 'Could not update';
                    }
                } else {
                    $message = 'You cannot update a ' . $ride_info->row()->ride_status . ' ride.';
                }
            } else {
                $message = 'There was some error';
            }
        }
        $response = array('message' => $message);
        $json_encode = json_encode($response, JSON_PRETTY_PRINT);

        if ($status == false) {
            http_response_code(400);
        }
        echo $this->cleanString($json_encode);
    }
}

/* End of file user.php */
/* Location: ./application/controllers/mobile/user.php */