<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
*  
* User related functions
* @author Casperon
*
* */
class User extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email','ride_helper'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');
        $this->load->model('app_model');
        $this->load->model('dynamic_driver');
        $this->load->model('rides_model');
        $returnArr = array();

        /* Authentication Begin */
        $headers = $this->input->request_headers();
        header('Content-type:application/json;charset=utf-8');
        if (array_key_exists("Authkey", $headers)) $auth_key = $headers['Authkey']; else $auth_key = "";
        if(stripos($auth_key,APP_NAME) === false) {
            $cf_fun= $this->router->fetch_method();
            $apply_function = array('check_account','check_social_login','register_user','social_Login','login_user','proceed_payment');
            if(!in_array($cf_fun,$apply_function)){
                show_404();
            }
        }
        
        if (array_key_exists("Apptype", $headers)) $this->Apptype = $headers['Apptype'];
        if (array_key_exists("Userid", $headers)) $this->Userid = $headers['Userid'];
        if (array_key_exists("Apptoken", $headers)) $this->Token = $headers['Apptoken'];
        try {
            if ($this->Userid != "" && $this->Token != "" && $this->Apptype != "") {
                $deadChk = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($this->Userid)), array('push_type', 'push_notification_key','status'));
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
                }
            }
        } catch (MongoException $ex) {
            
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
    public function check_account() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $user_name = $this->input->post('user_name');
            $country_code = $this->input->post('country_code');
            $phone_number = $this->input->post('phone_number');
            $referal_code = $this->input->post('referal_code');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 5) {
                if (valid_email($email)) {
                    $checkEmail = $this->user_model->check_user_exist(array('email' => $email));
                    if ($checkEmail->num_rows() >= 1) {
                        if ($checkEmail->row()->status != "Active") {
                            $returnArr['message'] = $this->format_string("Your account is currenty unavailable", "account_currently_unavailbale");
                        } else {
                            $returnArr['message'] = $this->format_string('Email address already exists', 'email_already_exist');
                        }
                    } else {
                        $condition = array('country_code' => $country_code, 'phone_number' => $phone_number);
                        $chekMobile = $this->user_model->get_selected_fields(USERS, $condition, array('_id'));
                        if ($chekMobile->num_rows() == 0) {
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
                                $key = '';
                                if ($gcm_id != "") {
                                    $key = $gcm_id;
                                } else if ($deviceToken != "") {
                                    $key = $deviceToken;
                                }
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
                                $returnArr['key'] = $key;
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
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');
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
                            $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($checkUser->row()->_id)));
                        }

                        $returnArr['status'] = '1';
                        $returnArr['message'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
                        $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkUser->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'referral_code', 'push_notification_key.gcm_id','unique_code','currency'));
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
                        $returnArr['key'] = $key;

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
                        
                        
                        $is_alive_other = "No";
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
                        $returnArr['is_alive_other'] = (string) $is_alive_other;
                        
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
    public function register_user() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $user_name = $this->input->post('user_name');
            $country_code = $this->input->post('country_code');
            $phone_number = $this->input->post('phone_number');
            $referal_code = $this->input->post('referal_code');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');
            
            $dcountry_code = $this->input->post('dcountry_code');
            


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

                            $isVodaCustomer = $this->user_model->isVodaCustomer($country_code, $phone_number);

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
                                $returnArr['key'] = $key;
                                $returnArr['status'] = '1';
                                $returnArr['is_voda_customer'] = ($isVodaCustomer ? 'true':'false');

                                $fields = array(
                                    'username' => (string) $last_insert_id,
                                    'password' => md5((string) $last_insert_id)
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
                                    'trans_id' => $trans_id,
                                    'currency'=>$currencyCode
                                );
                                $this->user_model->simple_push(WALLET, array('user_id' => new \MongoId($last_insert_id)), array('transactions' => $initialAmt));
                                $this->user_model->update_wallet((string) $last_insert_id, 'CREDIT', floatval($this->config->item('welcome_amount')));
                                /* Update the referer history */
                                if ($referal_code != '') {
                                    $refererVal = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('email','referral_count'));
                                    if ($refererVal->num_rows() > 0) {
                                    
                                        $referral_count = 1;
                                        if(isset($refererVal->row()->referral_count)) $referral_count = $refererVal->row()->referral_count+1;
                                        $userCond = array('_id' => new \MongoId($refererVal->row()->_id));
                                        $this->user_model->update_details(USERS, array('referral_count' => floatval($referral_count)), $userCond);
                                    
                                        $ref_status = 'true';
                                        $amount_earns = floatval($this->config->item('referal_amount'));
                                        if ($this->config->item('referal_credit') == 'on_first_ride') {
                                            $ref_status = 'false';
                                            #$amount_earns = floatval(0);
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
                                            $trans_id = time() . rand(0, 2578);
                                            $walletArr = array('type' => 'CREDIT',
                                                'credit_type' => 'referral',
                                                'ref_id' => (string) $last_insert_id,
                                                'trans_amount' => floatval($this->config->item('referal_amount')),
                                                'avail_amount' => floatval($avail_amount),
                                                'trans_date' => new \MongoDate(time()),
                                                'trans_id' => $trans_id,
                                                'currency'=>$currencyCode
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
            $phone_number = $this->input->post('phone_number');
            $referal_code = $this->input->post('referal_code');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');
            $media = $this->input->post('media');
            $media_id = $this->input->post('media_id');
            $password = $this->input->post('password');
            
            $dcountry_code = $this->input->post('dcountry_code');

            #$password = $this->user_model->get_random_string(6);

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
                        $checkUser = $this->user_model->get_selected_fields(USERS, array('email' => $email), array('push_type','push_notification_key'));
                        if ($checkUser->num_rows() == 1) {
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
                        }
                        $returnArr['is_alive_other'] = (string) $is_alive_other;
                        
                        
                        if (!empty($push_data)) {
                            $this->user_model->update_details(USERS, $push_update_data, $push_data);
                            $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($checkEmail->row()->_id)));
                        }

                        $returnArr['status'] = '1';
                        $returnArr['message'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
                        $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkEmail->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'referral_code', 'push_notification_key.gcm_id','unique_code','currency'));
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
                        $returnArr['key'] = $key;
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
                                'currency_symbol' => $currencySymbol
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
                                    $refererVal = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('email','referral_count','currency'));
                                    if ($refererVal->num_rows() > 0) {
                                        $rStatus = FALSE;
                                        if($refererVal->row()->currency==$currencyCode){
                                                $rStatus = TRUE;
                                        }
                                       if($rStatus)  {
                                        if(isset($refererVal->row()->referral_count)) $referral_count = $refererVal->row()->referral_count+1;
                                        $userCond = array('_id' => new \MongoId($refererVal->row()->_id));
                                        $this->user_model->update_details(USERS, array('referral_count' => floatval($referral_count)), $userCond);
                                    
                                        $ref_status = 'true';
                                        $amount_earns = floatval($this->config->item('referal_amount'));
                                        if ($this->config->item('referal_credit') == 'on_first_ride') {
                                            $ref_status = 'false';
                                            #$amount_earns = floatval(0);
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
                                $returnArr['key'] = $key;
                                $returnArr['status'] = '1';

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
    public function login_user() {
        $returnArr['status'] = '0';
        $returnArr['message'] = '';
        try {
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $gcm_id = $this->input->post('gcm_id');
            $deviceToken = $this->input->post('deviceToken');

            if (is_array($this->input->post())) {
                $chkValues = count(array_filter($this->input->post()));
            } else {
                $chkValues = 0;
            }

            if ($chkValues >= 2) {
                if (valid_email($email)) {
                    $checkUser = $this->user_model->get_selected_fields(USERS, array('email' => $email, 'password' => md5($password)), array('email', 'user_name', 'phone_number', 'status','push_type','push_notification_key', 'is_voda_customer', 'default_payment_method'));
                    if ($checkUser->num_rows() == 1) {
                        if ($checkUser->row()->status == "Active") {
                            $push_data = array();
                            $key = '';
                            if ($gcm_id != "") {
                                $key = $gcm_id;
                                $push_data = array('push_notification_key.gcm_id' => $gcm_id, 'push_type' => 'ANDROID');
                                $push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
                            }
                            if ($deviceToken != "") {
                                $key = $deviceToken;
                                $push_data = array('push_notification_key.ios_token' => $deviceToken, 'push_type' => 'IOS');
                                $push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
                            }
                            /* if($key==""){
                              $this->user_model->update_details(USERS,array('push_type'=>''),array('_id'=>new \MongoId($checkUser->row()->_id)));
                              } */
                              
                            
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
                                $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($checkUser->row()->_id)));
                            }


                            $returnArr['status'] = '1';
                            $returnArr['message'] = $this->format_string('You are Logged In successfully', 'you_logged_in');
                            $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkUser->row()->_id)), array('email', 'image', 'user_name', 'country_code', 'phone_number', 'referral_code', 'push_notification_key.gcm_id', 'password','currency', 'pending_payment'));
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
                            $returnArr['key'] = $key;

                            $returnArr['min_book_ahead_time'] = $this->config->item('min_book_ahead_time');
                            $returnArr['max_book_ahead_time'] = $this->config->item('max_book_ahead_time');
                            $returnArr['calendar_rider_reminder'] = $this->config->item('rider_reminder');

                            if ($checkUser->row()->is_voda_customer !== true) {
                                $isVodaCustomer = $this->user_model->isVodaCustomer($userVal->row()->country_code, $userVal->row()->phone_number);
                                // for old users who were not checked whether they were vodacom users while registering
                                if ($isVodaCustomer) {
                                    $userCond = array('_id' => new \MongoId($checkUser->row()->_id));
                                    $this->user_model->update_details(USERS, array('is_voda_customer' => $isVodaCustomer), $userCond);
                                }
                            }
                            else {
                                $isVodaCustomer = true;
                            }
                            $returnArr['is_voda_customer'] = ($isVodaCustomer ? 'true':'false');

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
    public function logout_user() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $user_id = $this->input->post('user_id');
            $device = $this->input->post('device');

            if ($user_id != '' && $device != '') {
                $condition = array('_id' => new \MongoId($user_id));
                $checkUser = $this->app_model->get_selected_fields(USERS, $condition, array('push_notification_key', 'push_type'));
                if ($checkUser->num_rows() == 1) {
                    if ($device == 'IOS' || $device == 'ANDROID') {
                        if ($device == 'ANDROID') {
                            $push_update_data = array('push_notification_key.gcm_id' => '', 'push_type' => '');
                        } else if ($device == 'IOS') {
                            $push_update_data = array('push_notification_key.ios_token' => '', 'push_type' => '');
                        } else {
                            $push_update_data = array('push_notification_key.gcm_id' => '', 'push_notification_key.ios_token' => '', 'push_type' => '');
                        }
                        $this->app_model->update_details(USERS, $push_update_data, $condition);
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string("You are logged out", "you_are_logged_out");
                    } else {
                        $returnArr['response'] = $this->format_string('Invalid inputs', 'invalid_input');
                    }
                } else {
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
                $checkUser = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email','currency', 'pending_payment'));
                if ($checkUser->num_rows() == 1) {
                    $geo_data = array('geo' => array(floatval($longitude), floatval($latitude)));
                    $checkGeo = $this->user_model->get_selected_fields(USER_LOCATION, array('user_id' => new \MongoId($user_id)), array('user_id'));
                    $geo_data_user = array('loc' => array('lon' => floatval($longitude), 'lat' => floatval($latitude)),'last_active_time'=>new \MongoDate(time()));
                    $this->user_model->update_details(USERS, $geo_data_user, array('_id' => new \MongoId($user_id)));
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
                    $location = $this->app_model->find_location(floatval($longitude), floatval($latitude));
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
                    if(isset($checkUser->row()->currency)) {
                        $returnArr['currency'] = (string)$checkUser->row()->currency;
                    }
                    $returnArr['wallet_amount'] = (string) round($avail_amount,2);

                    $returnArr['havePendingPayment'] = ($checkUser->row()->pending_payment == 'true' ? 'true':'false');
                    if ($checkUser->row()->pending_payment == 'true') {
                        $rideCondition = array(
                            'user.id' => $user_id,
                            'pay_status' => 'Pending'
                        );
                        $pendingRide = $this->rides_model->get_selected_fields(RIDES, $rideCondition, array('ride_id'));
                        $returnArr['unpaidRideId'] = $pendingRide->row()->ride_id;
                    }
                } else {
                    $returnArr['message'] = $this->format_string("Invalid User", "invalid_user");
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
        #print_r(json_encode($_POST)); die;
        $limit = 1000;
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

            if($category!=''){
                if ($chkValues >= 3) {
                    $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email','xmpp_connected'));
                    $geo_data_user = array('loc' => array('lon' => floatval($longitude), 'lat' => floatval($latitude)),'last_active_time'=>new \MongoDate(time()));
                    $this->user_model->update_details(USERS, $geo_data_user, array('_id' => new \MongoId($user_id)));
                    if ($checkUser->num_rows() == 1) {
                        $xmpp_connected = 'No';
                         if(isset($checkUser->row()->xmpp_connected)){
                            $xmpp_connected = $checkUser->row()->xmpp_connected;
                         }
                         if($xmpp_connected != 'Yes'){
                            /************  connect users with xmpp ************/
                            $fields = array(
                                        'username' => (string) $user_id,
                                        'password' => md5((string) $user_id)
                                     );
                            $url = $this->data['soc_url'] . 'create-user.php';
                            $this->load->library('curl');
                            $output = $this->curl->simple_post($url, $fields);
                            
                            $this->user_model->update_details(USERS,array('xmpp_connected' => 'Yes'),array('_id' => new \MongoId($user_id)));
                         }
                        $coordinates = array(floatval($longitude), floatval($latitude));
                        $location = $this->app_model->find_location(floatval($longitude), floatval($latitude),"Yes");
                        
                        if (!empty($location['result'])) {
                            $condition = array('status' => 'Active');
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
                                        if($eta_time>1){
                                            $eta_unit = $mins_short;
                                        }else{
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
                                if(!empty($routes)){
                                    usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));


                                    $pickup = (string) $routes[0]->legs[0]->start_address;
                                    $drop = (string) $routes[0]->legs[0]->end_address;

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
                                }else{
                                    $returnArr['response'] = $this->format_string('Sorry ! We can not fetch information', 'cannot_fetch_location_information_in_map');
                                }
                            }else{
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

            date_default_timezone_set('UTC');

            $category = $this->input->post('category');
            $type = $this->input->post('type');
            $pickup_date = $this->input->post('pickup_date');
            $pickup_time = $this->input->post('pickup_time');
            $code = $this->input->post('code');
            $try = intval($this->input->post('try'));
            $ride_id = (string) $this->input->post('ride_id');
            
            $drop_loc = (string)trim($this->input->post('drop_loc'));
            $drop_lat = $this->input->post('drop_lat');
            $drop_lon = $this->input->post('drop_lon');
            $payment_method_nonce = (string)trim($this->input->post('payment_method_nonce'));
            if($drop_loc==''){
                $drop_lat = 0;
                $drop_lon = 0;
            }
            
            $riderlocArr = array('lat' => (string) $pickup_lat, 'lon' => (string) $pickup_lon);

            if ($try > 1) {
                $limit = 10 * $try;
            } else {
                $limit = 10;
            }
            if ($type == 1) {
                $ride_type = 'Later';
                $pickup_datetime = $pickup_date . ' ' . $pickup_time;
                $pickup_timestamp = strtotime($pickup_datetime);
            } else {
                $ride_type = 'Now';
                $pickup_timestamp = time();
            }
            
            $after_one_hour = strtotime('+1 hour', time());
            $acceptance = 'No';
            
                if( $type == 0 || ($type ==1 && ($pickup_timestamp > $after_one_hour)) ){
                    if (is_array($this->input->post())) {
                        $chkValues = count(array_filter($this->input->post()));
                    } else {
                        $chkValues = 0;
                    }

                
                if ($ride_id != '') {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'driver', 'coupon_used', 'coupon', 'cancelled'));
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
                        $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email', 'user_name', 'country_code', 'phone_number', 'push_type'));
                        if ($checkUser->num_rows() == 1) {
                            if ($checkUser->row()->push_type != '') {
                                $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
                                $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
                                if (!empty($location['result'])) {
                                    $condition = array('status' => 'Active');
                                    $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($category)), array('name'));
                                    if ($categoryResult->num_rows() > 0) {
                                        $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, $limit);
                                        if (empty($category_drivers['result'])) {
                                            $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, $limit * 2);
                                        }
                                        $android_driver = array();
                                        $apple_driver = array();
                                        $push_and_driver = array();
                                        $push_ios_driver = array();
                                        foreach ($category_drivers['result'] as $driver) {
                                            if (isset($driver['push_notification'])) {
                                                if ($driver['push_notification']['type'] == 'ANDROID') {
                                                    if (isset($driver['push_notification']['key'])) {
                                                        if ($driver['push_notification']['key'] != '') {
                                                            $android_driver[] = $driver['push_notification']['key'];
                                                            $k = $driver['push_notification']['key'];
                                                            $push_and_driver[$k] = $driver['_id'];
                                                        }
                                                    }
                                                }
                                                if ($driver['push_notification']['type'] == 'IOS') {
                                                    if (isset($driver['push_notification']['key'])) {
                                                        if ($driver['push_notification']['key'] != '') {
                                                            $apple_driver[] = $driver['push_notification']['key'];
                                                            $k = $driver['push_notification']['key'];
                                                            $push_ios_driver[$k] = $driver['_id'];
                                                        }
                                                    }
                                                }
                                            }
                                        }

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

                                        $currencyCode = $this->data['dcurrencyCode'];
                                        $currencySymbol = $this->data['dcurrencySymbol'];
                             
                                        if(isset($location['result'][0]['currency'])){
                                            if($location['result'][0]['currency'] != ''){
                                                $currencyCode  = $location['result'][0]['currency'];
                                                $currencySymbol  = $location['result'][0]['currency_symbol'];
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
                                        
                                        $distance_unit = $this->data['d_distance_unit'];
                                        if(isset($location['result'][0]['distance_unit'])){
                                            $distance_unit = $location['result'][0]['distance_unit'];
                                        }

                                        $ride_id = $this->app_model->get_ride_id();
                                        $bookingInfo = array('ride_id' => (string) $ride_id,
                                            'type' => $ride_type,
                                            'booking_ref' =>'ios',
                                            'merchant_id' =>$merchant_id,
                                            'currency' => $currencyCode,
                                            'currency_symbol' => $currencySymbol,
                                            'payment_method_nonce' => $payment_method_nonce,
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
                                                'distance_unit' => $distance_unit,
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
                                            )
                                        );



                                        #echo '<pre>'; print_r($bookingInfo); die;
                                        $this->app_model->simple_insert(RIDES, $bookingInfo);
                                        $last_insert_id = $this->cimongo->insert_id();
                                        #echo '<pre>'; print_r($bookingInfo); die;
                                        if ($type == 0) {
                                            $message = $this->format_string("Request for pickup user","request_pickup_user");
                                            $response_time = $this->config->item('respond_timeout');
                                            $options = array($ride_id, $response_time, $pickup,$drop_loc);
                                            if (!empty($android_driver)) {
                                                foreach ($push_and_driver as $keys => $value) {
                                                    $driver_id = $value;
                                                    $condition = array('_id' => new \MongoId($driver_id));
                                                    $this->cimongo->where($condition)->inc('req_received', 1)->update(DRIVERS);
                                                }
                                                $this->sendPushNotification($android_driver, $message, 'ride_request', 'ANDROID', $options, 'DRIVER');
                                            }
                                            if (!empty($apple_driver)) {
                                                foreach ($push_ios_driver as $keys => $value) {
                                                    $driver_id = $value;
                                                    $condition = array('_id' => new \MongoId($driver_id));
                                                    $this->cimongo->where($condition)->inc('req_received', 1)->update(DRIVERS);
                                                }
                                                $this->sendPushNotification($apple_driver, $message, 'ride_request', 'IOS', $options, 'DRIVER');
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
                                    
                                } else {
                                    $returnArr['response'] = $this->format_string('No cabs available nearby', 'cabs_not_available_nearby');
                                }
                            } else {
                                $returnArr['response'] = $this->format_string('Sorry ! We do not provide services in your city yet.', 'service_unavailable_in_your_city');
                            }
                        } else {
                            $returnArr['response'] = $this->format_string('Cannot recognize your device', 'cannot_recognise_device');
                        }
                    } else {
                        $returnArr['response'] = $this->format_string("Invalid User", "invalid_user");
                    }
                  
                  } else {
                        $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
                  }
                }else {
                        $returnArr['response'] = $this->format_string("You cannot book at this time", "cannot_book_at_this_time");
                }
            } else {
                    $returnArr['status'] = '1';
                    $returnArr['acceptance'] = $acceptance;

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
                        #$vehicle_model=$vehicleInfo->row()->brand_name.' '.$vehicleInfo->row()->name;
                    }

                    $driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
                        'driver_name' => (string) $checkDriver->row()->driver_name,
                        'driver_email' => (string) $checkDriver->row()->email,
                        'driver_image' => (string) base_url() . $driver_image,
                        'driver_review' => (string) floatval($driver_review),
                        'driver_lat' => floatval($driver_lat),
                        'driver_lon' => floatval($driver_lon),
                        'min_pickup_duration' => $mindurationtext,
                        'ride_id' => (string) $ride_id,
                        'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
                        'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
                        'vehicle_model' => (string) $vehicle_model
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
        
            }else{
                $returnArr['response'] = $this->format_string("You can book ride only after one hour from now", "after_one_from_now");
            }
          
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $returnArr['acceptance'] = $acceptance;

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
                                        $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'push_notification'));

                                        if (isset($driverVal->row()->push_notification)) {
                                            if ($driverVal->row()->push_notification != '') {
                                                $message = $this->format_string("rider cancelled this ride","rider_cancelled_ride");
                                                $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
                                                if (isset($driverVal->row()->push_notification['type'])) {
                                                    if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
                                                        if (isset($driverVal->row()->push_notification['key'])) {
                                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'ride_cancelled', 'ANDROID', $options, 'DRIVER');
                                                            }
                                                        }
                                                    }
                                                    if ($driverVal->row()->push_notification['type'] == 'IOS') {
                                                        if (isset($driverVal->row()->push_notification['key'])) {
                                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'ride_cancelled', 'IOS', $options, 'DRIVER');
                                                            }
                                                        }
                                                    }
                                                }
                                            }
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

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('city', 'avail_category'));
                if ($userVal->num_rows() > 0) {
                    $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
                    if ($checkRide->num_rows() == 1) {

                        $lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
                        $lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
                        $this->setTimezone($lat, $lon);
                        
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
                                $total_bill = 0.00;
                                $coupon_discount = 0.00;
                                $grand_bill = 0.00;
                                $total_paid = 0.00;
                                $wallet_usage = 0.00;
                                $tips_amount = 0.00;

                                if (isset($checkRide->row()->total['tips_amount'])) {
                                    if ($checkRide->row()->total['tips_amount'] > 0) {
                                        $tips_amount = $checkRide->row()->total['tips_amount'];
                                    }
                                }

                                if (isset($checkRide->row()->total['total_fare'])) {
                                    $total_bill = $checkRide->row()->total['total_fare'];
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
                               
                                $fareArr = array('total_bill' => (string) floatval($total_bill),
                                    'coupon_discount' => (string) floatval($coupon_discount),
                                    'grand_bill' => (string) floatval($grand_bill),
                                    'total_paid' => (string) floatval($total_paid),
                                    'wallet_usage' => (string) floatval($wallet_usage),
                                    'tips_amount' => (string) floatval($tips_amount)
                                );
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
                        $longitude = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
                        $latitude = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
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
                        if (($checkRide->row()->driver['id'] != '') && ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived' || $checkRide->row()->ride_status == 'Finished' || $checkRide->row()->ride_status == 'Onride')) {
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
                        
                        if (empty($fare_summary)) {
                            $fare_summary = json_decode("{}");
                        }


                        $responseArr = array('currency' => $checkRide->row()->currency,
                            'cab_type' => $checkRide->row()->booking_information['service_type'],
                            'ride_id' => $checkRide->row()->ride_id,
                            'ride_status' => $checkRide->row()->ride_status,
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
                            'summary' => $summaryArr,
                            'fare' => $fareArr,
                            'fare_summary' => $fare_summary,
                            'distance_unit' => $disp_distance_unit,
                            'invoice_src' => $invoice_src
                        );
                        if (empty($responseArr)) {
                            $responseArr = json_decode("{}");
                        }
                        $returnArr['status'] = '1';
                        $returnArr['response'] = array('details' => $responseArr);
                    } else {
                        $returnArr['response'] = $this->format_string("Records not available", "no_records_found ");
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
                    $checkRide = $this->app_model->get_ride_list($user_id, $type, array('booking_information', 'ride_id', 'ride_status'));
                    $rideArr = array();
                    if ($checkRide->num_rows() > 0) {
                        foreach ($checkRide->result() as $ride) {

                            $lat = $ride->booking_information['pickup']['latlong']['lat'];
                            $lon = $ride->booking_information['pickup']['latlong']['lon'];
                            $this->setTimezone($lat, $lon);

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
                            if ($ride->ride_status != 'Expired') {
                                $rideArr[] = array(
                                    'ride_id' => $ride->ride_id,
                                    'ride_time' => date('h:i A', $ride->booking_information['est_pickup_date']->sec),
                                    'ride_date' => date("jS M, Y", $ride->booking_information['est_pickup_date']->sec),
                                    'pickup' => $ride->booking_information['pickup']['location'],
                                    'ride_status' => (string) $ride->ride_status,
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
            $user_id = (string) $this->input->post('user_id');

            if ($user_id != '') {
                $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('city', 'unique_code','currency'));
                if ($userVal->num_rows() > 0) {
                    if ($this->config->item('referal_credit') == 'instant') {
                        $your_earn = 'Friend Joins';
                        $your_earn_condition = "instant";
                    } else if ($this->config->item('referal_credit') == 'on_first_ride') {
                        $your_earn = 'Friend Rides';
                        $your_earn_condition = "on_first_ride";
                    }
                    $currencyCode=$this->data['dcurrencyCode'];
                    if(isset($userVal->row()->currency)) {
                        $currencyCode = (string)$userVal->row()->currency;
                    }
                    $subject = $this->config->item('email_title')." app invitation";
                    /*$message = "I have an ".$this->config->item('email_title')." coupon worth ".$currencyCode.$this->config->item('welcome_amount')." for you."." Sign up with my referral code ".$userVal->row()->unique_code.". for more info visit : ".base_url();*/
                    $message = "Get MOOVN with the best ride in town! Sign up with my code " .$userVal->row()->unique_code. " in iTunes: https://goo.gl/tffPSz or GooglePlay: https://goo.gl/vC1Rla";
                    $detailsArr = array('friends_earn_amount' => floatval($this->config->item('welcome_amount')),
                        'your_earn' => $your_earn,
                        'your_earn_condition' => $your_earn_condition,
                        'your_earn_amount' => floatval($this->config->item('referal_amount')),
                        'referral_code' => $userVal->row()->unique_code,
                        'currency' => $currencyCode,
                        'subject' => (string)$subject,
                        'message' => (string)$message,
                        'url'=>base_url().'rider/signup?ref=' . base64_encode($userVal->row()->unique_code)
                    );
                    if (empty($detailsArr)) {
                        $detailsArr = json_decode("{}");
                    }
                    $returnArr['status'] = '1';
                    $returnArr['response'] = array('details' => $detailsArr);
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
                                    }else{
                                        $title = $this->format_string("Recharge", "recharge");
                                    }
                                } else if ($trans['type'] == 'DEBIT') {
                                    if ($trans['debit_type'] == 'payment') {
                                        $title = $this->format_string("Booking for", "booking_for").' #' . $trans['ref_id'];
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

                                    $updateUser = array('pending_payment' => 'false');
                                    $this->user_model->update_details(USERS, $updateUser, array('_id' => new \MongoId($user_id)));
                                    
                                    $driver_id = $checkRide->row()->driver['id'];
                                    $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'push_notification'));

                                    /* Update Stats Starts */
                                    $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                                    $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                                    $this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
                                    /* Update Stats End */

                                    if ($driverVal->num_rows() > 0) {
                                        if (isset($driverVal->row()->push_notification)) {
                                            if ($driverVal->row()->push_notification != '') {
                                                $message = $this->format_string("payment successfully completed", "payment_completed");
                                                $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
                                                if (isset($driverVal->row()->push_notification['type'])) {
                                                    if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
                                                        if (isset($driverVal->row()->push_notification['key'])) {
                                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'ANDROID', $options, 'DRIVER');
                                                            }
                                                        }
                                                    }
                                                    if ($driverVal->row()->push_notification['type'] == 'IOS') {
                                                        if (isset($driverVal->row()->push_notification['key'])) {
                                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'IOS', $options, 'DRIVER');
                                                            }
                                                        }
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
                                    $returnArr['response'] = $this->format_string('payment successfully completed', 'payment_completed');
                                } else if ($ride_charge > $wallet_amount) {

                                    // $pay_summary = array('type' => 'Wallet');
                                    // $paymentInfo = array('pay_status' => 'Processing',
                                    //     'history.wallet_usage_time' => new \MongoDate(time()),
                                    //     'total.wallet_usage' => $wallet_amount,
                                    //     'pay_summary' => $pay_summary
                                    // );
                                    // /* Update the user wallet */
                                    // $currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
                                    // $avail_amount = 0.00;
                                    // if ($currentWallet->num_rows() > 0) {
                                    //     if (isset($currentWallet->row()->total)) {
                                    //         $avail_amount = floatval($currentWallet->row()->total);
                                    //     }
                                    // }
                                    // if ($avail_amount > 0) {
                                    //     $this->app_model->update_wallet((string) $user_id, 'DEBIT', floatval($avail_amount - $wallet_amount));
                                    // }
                                    // $currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
                                    // $avail_amount = 0.00;
                                    // if ($currentWallet->num_rows() > 0) {
                                    //     if (isset($currentWallet->row()->total)) {
                                    //         $avail_amount = floatval($currentWallet->row()->total);
                                    //     }
                                    // }
                                    // $trans_id = time() . rand(0, 2578);
                                    // $walletArr = array('type' => 'DEBIT',
                                    //     'debit_type' => 'payment',
                                    //     'ref_id' => $ride_id,
                                    //     'trans_amount' => floatval($wallet_amount),
                                    //     'avail_amount' => floatval($avail_amount),
                                    //     'trans_date' => new \MongoDate(time()),
                                    //     'trans_id' => $trans_id
                                    // );
                                    // $this->app_model->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $walletArr));
                                    // $transactionArr = array('type' => 'wallet',
                                    //     'amount' => floatval($wallet_amount),
                                    //     'trans_id' => $trans_id,
                                    //     'trans_date' => new \MongoDate(time())
                                    // );
                                    // $this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
                                    // $this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));

                                    // $unbill_amount = $ride_charge - $wallet_amount;
                                    // $returnArr['status'] = '2';
                                    // $returnArr['response'] = $this->format_string('Wallet amount used successfully', 'wallet_used_successfully');
                                    // $returnArr['used_amount'] = (string) $wallet_amount;
                                    // $returnArr['unbill_amount'] = (string) $unbill_amount;

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
                        $driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'push_notification'));

                        if (isset($driverVal->row()->push_notification)) {
                            if ($driverVal->row()->push_notification != '') {
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
                                
                                
                                if (isset($driverVal->row()->push_notification['type'])) {
                                    if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'receive_cash', 'ANDROID', $options, 'DRIVER');
                                            }
                                        }
                                    }
                                    if ($driverVal->row()->push_notification['type'] == 'IOS') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                $this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'receive_cash', 'IOS', $options, 'DRIVER');
                                            }
                                        }
                                    }
                                    $payArr = array('pay_by_cash'=>'Yes');
                                    $this->app_model->update_details(RIDES, $payArr, array('ride_id' => $ride_id));
                                }
                            }
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
     * Routing the payment process
     *
     * */
    public function proceed_payment() {
        $mobile_id = (string) $this->input->get('mobileId');
        if ($mobile_id != '') {
            $checkPayment = $this->app_model->get_all_details(MOBILE_PAYMENT, array('_id' => new \MongoId($mobile_id)));
            if ($checkPayment->num_rows() == 1) {
                $payment_id = $checkPayment->row()->payment_id;
                switch ($payment_id) {
                    case '1':
                        redirect(base_url() . 'v5/mobile/payment-form?mobileId=' . $mobile_id);
                        break;
                    case '2':
                        redirect(base_url() . 'v5/mobile/payment-paypal?mobileId=' . $mobile_id);
                        break;
                    case '3':
                        redirect(base_url() . 'v5/mobile/stripe-manual-payment-form?mobileId=' . $mobile_id);
                        break;
                }
            }
        }
        #redirect(base_url);
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
}

/* End of file user.php */
/* Location: ./application/controllers/mobile/user.php */