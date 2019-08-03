<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to favourite locations 
 * @author Casperon
 *
 * */
class User_profile extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation', 'twilio'));
        $this->load->model(array('user_model'));
        
        
        /* Authentication Begin */
        $headers = $this->input->request_headers();
        header('Content-type:application/json;charset=utf-8');
        $current_function = $this->router->fetch_method();
        $public_functions = array('user_reset_password', 'update_reset_password');
        if (array_key_exists("User-Token", $headers)) {
            $this->user_token = $headers['User-Token'];
            try {
                if (isset($this->user_token) && $this->user_token != '') {
                    $user = $this->app_model->get_selected_fields(USERS, array('login_token' => $this->user_token), array('_id'));
                    if ($user->num_rows() <= 0) {
                        echo json_encode(array("is_dead"=>"Yes")); die;
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

    /**
     *
     * This function add the location to favourite list
     *
     * */
    public function add_favourite_location() { 
        $responseArr['status'] = '0';
        $responseArr['message'] = '';

        $title = trim($this->input->post('title'));
        $address = trim($this->input->post('address'));
        $user_id = $this->input->post('user_id');
        $longitude = $this->input->post('longitude');
        $latitude = $this->input->post('latitude');
        $loc_key = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);


        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 5) {
            $fav_condition = array('user_id' => new \MongoId($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);
            if (isset($checkUserInFav->row()->fav_location[$loc_key])) {
                $responseArr['status'] = '0';
                $responseArr['message'] = $this->format_string('Location already exist in your favourite list', 'location_already_exist_in_favourite');
            } else {
                if ($checkUserInFav->num_rows() == 0) {
                    $dataArr = array('user_id' => new \MongoId($user_id),
                        'fav_location' => array($loc_key => array('title' => $title,
                                'address' => $address,
                                'geo' => array('longitude' => floatval($longitude),
                                    'latitude' => floatval($latitude)
                                )
                            )
                        )
                    );
                    $this->user_model->simple_insert(FAVOURITE, $dataArr);
                    $responseArr['status'] = '1';
                    $responseArr['message'] = $this->format_string('Location added to favourite', 'location_added_to_favourite');
                } else {
                    $dataArr = array('fav_location.' . $loc_key => array('title' => $title,
                            'address' => $address,
                            'geo' => array('longitude' => floatval($longitude),
                                'latitude' => floatval($latitude)
                            )
                        )
                    );
                    $this->user_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);
                    $responseArr['status'] = '1';
                    $responseArr['message'] = $this->format_string('Location added to favourite', 'location_added_to_favourite');
                }
            }
        } else {
            $responseArr['message'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function edit the location from favourite list
     *
     * */
    public function edit_favourite_location() {
        $responseArr['status'] = '0';
        $responseArr['message'] = '';

        $title = trim($this->input->post('title'));
        $address = trim($this->input->post('address'));
        $user_id = $this->input->post('user_id');
        $longitude = $this->input->post('longitude');
        $latitude = $this->input->post('latitude');
        $loc_key = $this->input->post('location_key');

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 5) {
            $fav_condition = array('user_id' => new \MongoId($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);


            if (!isset($checkUserInFav->row()->fav_location[$loc_key])) {
                $responseArr['status'] = '0';
                $responseArr['message'] = $this->format_string('No records found for this location', 'no_records_found_for_location');
            } else {
                $dataArr = array('fav_location.' . $loc_key => array('title' => $title,
                        'address' => $address,
                        'geo' => array('longitude' => floatval($longitude),
                            'latitude' => floatval($latitude)
                        )
                    )
                );
                $this->user_model->remove_favorite_location($fav_condition, 'fav_location.' . $loc_key);
                $this->user_model->set_to_field(FAVOURITE, $fav_condition, $dataArr);

                $responseArr['status'] = '1';
                $responseArr['message'] = $this->format_string('Updated successfully', 'updated_successfully');
            }
        } else {
            $responseArr['message'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function remove the location from favourite list
     *
     * */
    public function remove_favourite_location() {
        $responseArr['status'] = '0';
        $responseArr['message'] = '';
        $loc_key = $this->input->post('location_key');
        $user_id = $this->input->post('user_id');


        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 2) {
            $fav_condition = array('user_id' => new \MongoId($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);

            if (!isset($checkUserInFav->row()->fav_location[$loc_key])) {
                $responseArr['status'] = '0';
                $responseArr['message'] = $this->format_string('No records found for this location', 'no_records_found_for_location');
            } else {
                $this->user_model->remove_favorite_location($fav_condition, 'fav_location.' . $loc_key);
                $responseArr['status'] = '1';
                $responseArr['message'] = $this->format_string('Location removed successfully', 'location_removed_successfully');
            }
        } else {
            $responseArr['message'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function displays the all favourite locations
     *
     * */
    public function display_favourite_location() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');

        if ($user_id != '') {
            $fav_condition = array('user_id' => new \MongoId($user_id));
            $checkUserInFav = $this->user_model->get_all_details(FAVOURITE, $fav_condition);
            if ($checkUserInFav->num_rows() == 0) {
                $responseArr['response'] = $this->format_string('No records found for in your favourite list', 'no_records_found_in_your_favourite_list');
            } else {
                if (isset($checkUserInFav->row()->fav_location)) {
                    $favLocations = $checkUserInFav->row()->fav_location;
                } else {
                    $favLocations = array();
                }
                $favLocatArr = array();
                foreach ($favLocations as $key => $val) {
                    $favLocatArr[] = array('location_key' => $key,
                        'title' => $val['title'],
                        'address' => $val['address'],
                        'longitude' => $val['geo']['longitude'],
                        'latitude' => $val['geo']['latitude'],
                    );
                }
                $totalFavLoc = count($favLocations);
                if ($totalFavLoc > 0) {
                    if (empty($favLocatArr)) {
                        $favLocatArr = json_decode("{}");
                    }
                    $responseArr['status'] = '1';
                    $responseArr['response'] = array('locations' => $favLocatArr, 'total_count' => $totalFavLoc);
                } else {
                    $responseArr['response'] = $this->format_string('No records found for in your favourite list', 'no_records_found_in_your_favourite_list');
                }
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function changes the users name 
     * */
    public function change_user_name() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $user_name = $this->input->post('user_name');
        if ($user_id != '' && $user_name != '') {
            $condition = array('_id' => new \MongoId($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id'));
            if ($checkUser->num_rows() == 1) {
                $dataArr = array('user_name' => $user_name);
                $this->user_model->update_details(USERS, $dataArr, $condition);
                $responseArr['status'] = '1';
                $responseArr['response'] = $this->format_string('User name changed successfully', 'username_changed_successfully');
                $responseArr['user_name'] = $user_name;
            } else {
                $responseArr['response'] = $this->format_string('Invalid request', 'invalid_request');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function changes the users mobile 
     * */
    public function change_user_mobile_number() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $country_code = $this->input->post('country_code');
        $phone_number = $this->remove_leading_zeros($this->input->post('phone_number'));
        $otp = (string) $this->input->post('otp');

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }
        if ($chkValues >= 3) {
            $phcondition = array('phone_number' => $phone_number, 'country_code' => $country_code);
            $checkphUser = $this->user_model->get_selected_fields(USERS, $phcondition, array('_id'));
            if ($checkphUser->num_rows() == 0) {
                $condition = array('_id' => new \MongoId($user_id));
                $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'email', 'country_code', 'phone_number'));
                if ($checkUser->num_rows() == 1) {

                    if ($otp != '') {
                        $dataArr = array('country_code' => $country_code, 'phone_number' => $phone_number);
                        $this->user_model->update_details(USERS, $dataArr, $condition);
                    }

                    if ($otp == '') {
                        /*                         * ****  mobile otp section  start**** */
                        $phone_code = $country_code;
                        if (substr($phone_code, 0, 1) == '+') {
                            $phone_code = $phone_code;
                        } else {
                            $phone_code = '+' . $phone_code;
                        }
                        $otp_number = rand(10000, 99999);
                        $from = $this->config->item('twilio_number');
                        $to = $phone_code . $phone_number;
                        $user_name = $checkUser->row()->user_name;
                        $user_email = $checkUser->row()->email;
                        $dear = $this->format_string('Dear', 'dear');
                        $your = $this->format_string('your', 'your');
                        $one_time_password_is = $this->format_string('one time password is', 'one_time_password_is');
                        $message = $dear . ' ' . $user_name . '! ' . $your . ' ' . $this->config->item('email_title') . ' ' . $one_time_password_is . ' ' . $otp_number;
                        $response = $this->twilio->sms($from, $to, $message);
                        $this->user_model->update_details(USERS, array('mobile_otp' => $otp_number), $condition);

                        $responseArr['otp'] = $otp_number;
                        if ($this->config->item('twilio_account_type') == 'sandbox') {
                            $otp_status = 'development';
                        } else {
                            $otp_status = 'production';
                        }
                        $responseArr['otp_status'] = $otp_status;
                    }
                    if ($otp == '') {
                        $responseArr['country_code'] = (string) $country_code;
                        $responseArr['phone_number'] = (string) $phone_number;
                        $responseArr['response'] = $this->format_string('otp sent successfully', 'otp_sent');
                    } else {
                        $responseArr['response'] = $this->format_string('User mobile number changed successfully', 'user_mobile_number_changed');
                        $responseArr['country_code'] = (string) $checkUser->row()->country_code;
                        $responseArr['phone_number'] = (string) $checkUser->row()->phone_number;
                    }
                    #$this->send_otp_email($otp_number,$user_name,$user_email); 
                    /*                     * ****  mobile otp section  end**** */
                    $responseArr['status'] = '1';
                } else {
                    $responseArr['response'] = $this->format_string('Invalid request', 'invalid_request');
                }
            } else {
                $responseArr['response'] = $this->format_string('This mobile number already registered', 'mobile_number_already_registered');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This function changes the users password
     * */
    public function send_otp_email($otpCode = '', $user_name = '', $user_email = '') {
        $newsid = '4';
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
        extract($adminnewstemplateArr);
        $subject = $template_values['subject'];
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
        extract($adminnewstemplateArr);
        $message = '<!DOCTYPE HTML>
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width"/>
            <title>' . $template_values['subject'] . '</title>
            <body>';
        include($template_values['templateurl']);
        $message .= '</body>
            </html>';
        $sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $user_email,
            'subject_message' => $subject,
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
    }

    /**
     *
     * This function changes the users password
     * */
    public function change_user_password() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $password = $this->input->post('password');
        $new_password = (string) $this->input->post('new_password');

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }
        if ($chkValues >= 3) {
            if (strlen($new_password) >= 6) {
                $condition = array('_id' => new \MongoId($user_id), 'password' => md5($password));
                $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id'));
                if ($checkUser->num_rows() == 1) {
                    $condition = array('_id' => new \MongoId($user_id));
                    $dataArr = array('password' => md5($new_password));
                    $this->user_model->update_details(USERS, $dataArr, $condition);
                    $responseArr['status'] = '1';
                    $responseArr['response'] = $this->format_string('User password changed successfully', 'password_changed');
                } else {
                    $responseArr['response'] = $this->format_string('Your current password is not matching', 'password_not_matching');
                }
            } else {
                $responseArr['response'] = $this->format_string('Password should be atleast 6 characters', 'password_should_be_6_characters');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }

        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function emergency_contact_add_edit() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $em_name = $this->input->post('em_name');
        $em_email = $this->input->post('em_email');
        $em_mobile = $this->remove_leading_zeros($this->input->post('em_mobile'));
        $em_mobile_code = $this->input->post('em_mobile_code');

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }
        if ($chkValues >= 5) {
            $condition = array('_id' => new \MongoId($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'email', 'phone_number', 'emergency_contact'));

            $email_verify_status = 'No';
            $mobile_verify_status = 'No';
            if(isset($checkUser->row()->emergency_contact)){
                if (array_key_exists('em_email',$checkUser->row()->emergency_contact)) {
                    if (isset($checkUser->row()->emergency_contact['verification']['email']))
                        $email_verify_status = $checkUser->row()->emergency_contact['verification']['email'];
                    if (isset($checkUser->row()->emergency_contact['verification']['mobile']))
                        $mobile_verify_status = $checkUser->row()->emergency_contact['verification']['mobile'];
                    if ($checkUser->row()->emergency_contact['em_email'] != $em_email) {
                        $email_verify_status = 'No';
                    }
                    if ($checkUser->row()->emergency_contact['em_mobile'] != $em_mobile) {
                        $mobile_verify_status = 'No';
                    }
                }
            }

            $vfyArr = array('email' => $email_verify_status, 'mobile' => $mobile_verify_status);

            if ($checkUser->num_rows() == 1) {
                if ($checkUser->row()->email != $em_email && $checkUser->row()->phone_number != $em_mobile) {

                    $em_dataArr = array('emergency_contact' => array('em_name' => $em_name, 'em_email' => $em_email, 'em_mobile' => $em_mobile, 'em_mobile_code' => $em_mobile_code), 'verification' => $vfyArr);
                    if (isset($checkUser->row()->emergency_contact)) {
                        if (is_array($checkUser->row()->emergency_contact)) {
                            if (!empty($checkUser->row()->emergency_contact)) {
                                $em_dataArr = array('emergency_contact.em_name' => $em_name, 'emergency_contact.em_email' => $em_email, 'emergency_contact.em_mobile' => $em_mobile, 'emergency_contact.em_mobile_code' => $em_mobile_code, 'emergency_contact.verification' => $vfyArr);
                            }
                        }
                    }

                    $em_dataMailArr = array('em_name' => $em_name, 'em_email' => $em_email, 'em_mobile' => $em_mobile, 'em_mobile_code' => $em_mobile_code);


                    if (isset($checkUser->row()->emergency_contact['em_email'])) {
                        $olderEmail = $checkUser->row()->emergency_contact['em_email'];
                    } else {
                        $olderEmail = '';
                    }

                    $this->user_model->update_details(USERS, $em_dataArr, $condition);

                    if (isset($checkUser->row()->emergency_contact)) {
                        if ($olderEmail == $em_email) {
                            $responseArr['response'] = $this->format_string('Emergency contact updated successfully', 'emergency_contact_updated');
                        } else {
                            $this->emergency_contact_verification_request($checkUser, $em_dataMailArr);
                            $responseArr['response'] = $this->format_string('Emergency contact added successfully', 'emergency_contact_added');
                        }
                    } else {
                        $this->emergency_contact_verification_request($checkUser, $em_dataMailArr);
                        $responseArr['response'] = $this->format_string('Emergency contact added successfully', 'emergency_contact_added');
                    }


                    $responseArr['status'] = '1';
                    $responseArr['response'] = $this->format_string('Emergency contact added successfully', 'emergency_contact_added');
                } else {
                    $responseArr['response'] = $this->format_string('Sorry, You can not add your details', 'you_cannot_add_your_details');
                }
            } else {
                $responseArr['response'] = $this->format_string('This user does not exist', 'user_not_exists');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function emergency_contact_verification_request($user_info, $contactArr) {
        /* ---------------SMS--------------------- */
        $phone_code = $contactArr['em_mobile_code'];
        $phone_number = $contactArr['em_mobile'];
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }
        $otp_number = rand(10000, 99999);
        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
        $em_user_name = $contactArr['em_name'];
        $em_user_email = $contactArr['em_email'];
        $user_name = $user_info->row()->user_name;
        $user_id = $user_info->row()->_id;
        $message = 'Hi ' . $em_user_name . ', ' . $user_name . ' added you as a emergency person in ' . $this->config->item('email_title') . '. Confirmation OTP : ' . $otp_number;
        $response = $this->twilio->sms($from, $to, $message);

        $condition = array('_id' => new \MongoId($user_id));
        $this->user_model->update_details(USERS, array('emergency_contact.mobile_otp' => $otp_number), $condition);

        $responseArr['otp'] = $otp_number;
        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['otp_status'] = $otp_status;

        /* --------------------Email-------------------- */
        $newsid = '5';
        $confirm_link = base_url() . 'emergency-contact/confirm?c=' . md5($otp_number) . '&u=' . $user_id;
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
        extract($adminnewstemplateArr);
        $message = '<!DOCTYPE HTML>
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width"/>
            <title>' . $template_values['subject'] . '</title>
            <body>';
        include($template_values['templateurl']);
        $message .= '</body>
            </html>';
        $sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $em_user_email,
            'subject_message' => $template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        return $responseArr;
    }

    /* public function confirm_emergency_contact_form() {
      $user_id = $this->input->get('u');
      $otp_encr = $this->input->get('c');
      if ($user_id != '') {
      $condition = array('_id' => new \MongoId($user_id));
      $getUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
      if ($getUser->num_rows() == 0) {
      $this->setErrorMessage('error', 'This user details does not exist');
      redirect('');
      } else {
      $em_mobile_otp = $getUser->row()->emergency_contact['mobile_otp'];
      if (md5($em_mobile_otp) == $otp_encr) {
      $this->data['user_details'] = $getUser;
      $this->load->view('site/user/emergency_contact_confirmation', $this->data);
      } else {
      $this->setErrorMessage('error', 'User authentication failed, Link is not a valid link');
      redirect('');
      }
      }
      } else {
      $this->setErrorMessage('error', 'Invalid link');
      redirect('');
      }
      }

      public function confirm_emergency_contact() {
      $user_id = $this->input->post('user_id');
      $emGiven_otp = $this->input->post('em_mobile_otp');
      if ($user_id != '') {
      $condition = array('_id' => new \MongoId($user_id));
      $getUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
      if ($getUser->num_rows() == 0) {
      $this->setErrorMessage('error', 'This user details does not exist');
      redirect('');
      } else {
      $em_mobile_otp = $getUser->row()->emergency_contact['mobile_otp'];
      if ($getUser->row()->emergency_contact . verification . mobile == 'Yes') {
      $this->setErrorMessage('success', 'Already you have been verified');
      redirect('');
      }
      if ($em_mobile_otp == $emGiven_otp) {
      $emDataArr = array('emergency_contact.verification.mobile' => 'Yes', 'emergency_contact.verification.email' => 'Yes');
      $this->user_model->update_details(USERS, $emDataArr, $condition);
      $this->setErrorMessage('success', 'Thanks, Your verification has been completed successfully');
      redirect('');
      } else {
      $this->setErrorMessage('error', 'Sorry, OTP does not match');
      redirect('');
      }
      }
      } else {
      $this->setErrorMessage('error', 'Invalid link');
      redirect('');
      }
      } */

    public function emergency_contact_view() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');


        if ($user_id != '') {
            $condition = array('_id' => new \MongoId($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
            if ($checkUser->num_rows() == 1) {
                if (isset($checkUser->row()->emergency_contact)) {
                    if (count($checkUser->row()->emergency_contact) > 0) {
                        $emgaArr = $checkUser->row()->emergency_contact;

                        if (isset($emgaArr['verification'])) {
                            $vefifyStatus = array('mobile' => $emgaArr['verification']['mobile'],
                                'email' => $emgaArr['verification']['email']
                            );
                        } else {
                            $vefifyStatus = array('mobile' => 'No',
                                'email' => 'No'
                            );
                        }

                        $emergency_contact = array('name' => $emgaArr['em_name'],
                            'email' => $emgaArr['em_email'],
                            'code' => $emgaArr['em_mobile_code'],
                            'mobile' => $emgaArr['em_mobile'],
                            'verification_status' => $vefifyStatus);

                        if (empty($emergency_contact)) {
                            $emergency_contact = json_decode("{}");
                        }
                        $responseArr['emergency_contact'] = $emergency_contact;

                        $responseArr['status'] = '1';
                    } else {
                        $responseArr['response'] = $this->format_string('Emergency contact is not available', 'emergency_contact_unavailable');
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Sorry, You have not set emergency contact yet', 'you_have_not_added_emergency_contacty_yet');
                }
            } else {
                $responseArr['response'] = $this->format_string('This user does not exist', 'user_not_exists');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function emergency_contact_delete() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');

        if ($user_id != '') {
            $condition = array('_id' => new \MongoId($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
            if ($checkUser->num_rows() == 1) {
                if (isset($checkUser->row()->emergency_contact)) {
                    $em_dataArr = array();
                    $this->user_model->update_details(USERS, array('emergency_contact' => $em_dataArr), $condition);
                    $responseArr['response'] = $this->format_string('Contact deleted successfully', 'contact_deleted');
                    $responseArr['status'] = '1';
                } else {
                    $responseArr['response'] = $this->format_string('Sorry, You have not set emergency contact yet', 'you_have_not_added_emergency_contacty_yet');
                }
            } else {
                $responseArr['response'] = $this->format_string('This user does not exist', 'user_not_exists');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function emergency_contact_alert() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        $user_id = $this->input->post('user_id');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');

        if (is_array($this->input->post())) {
            $chkValues = count(array_filter($this->input->post()));
        } else {
            $chkValues = 0;
        }

        if ($chkValues >= 3) {
            $condition = array('_id' => new \MongoId($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
            if ($checkUser->num_rows() == 1) {
                if (isset($checkUser->row()->emergency_contact)) {
                    if (count($checkUser->row()->emergency_contact) > 0) {
                           
                        if($checkUser->row()->emergency_contact['verification']['email'] == 'Yes' && $checkUser->row()->emergency_contact['verification']['mobile'] == 'Yes'){
                        
                            $latlng = $latitude . ',' . $longitude;
                            $gmap = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false".$this->data['google_maps_api_key']);
                            $mapValues = json_decode($gmap)->results;
                            $formatted_address = $mapValues[0]->formatted_address;

                            $this->send_alert_notification_to_emergency_contact($checkUser->row()->user_name, $checkUser->row()->emergency_contact, $formatted_address);
                            $responseArr['response'] = $this->format_string('Alert notification sent successfully', 'alert_notification_sent');
                            $responseArr['status'] = '1';
                        } else {
                            $responseArr['response'] = $this->format_string('Your emergency contacts are not verified yet', 'your_emergency_contacts_not_verified');
                        }
                    } else {
                        $responseArr['response'] = $this->format_string('Emergency contact is not available', 'emergency_contact_unavailable');
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Sorry, You have not set emergency contact yet', 'you_have_not_added_emergency_contacty_yet');
                }
            } else {
                $responseArr['response'] = $this->format_string('This user does not exist', 'user_not_exists');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function send_alert_notification_to_emergency_contact($user_name, $contactArr, $currentLocation = '') {
        /* ---------------SMS--------------------- */
        $phone_code = $contactArr['em_mobile_code'];
        $phone_number = $contactArr['em_mobile'];
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }

        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
        $em_user_name = $contactArr['em_name'];
        $em_user_email = $contactArr['em_email'];
        $user_name = $user_name;
        $message = 'Emergency!. Hi ' . $em_user_name . '!, ' . $user_name . ' sent alert notification to you, please check your email '.$em_user_email.' for more details. Team - ' . $this->config->item('email_title');
        $response = $this->twilio->sms($from, $to, $message);


        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['otp_status'] = $otp_status;

        /* --------------------Email-------------------- */
        $newsid = '6';
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
        extract($adminnewstemplateArr);
        
        $message = '<!DOCTYPE HTML>
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width"/>
            <title>' . $template_values['subject'] . '</title>
            <body>';
        include($template_values['templateurl']);
        $message .= '</body>
            </html>';
        $sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $em_user_email,
            'subject_message' => $template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        return $responseArr;
    }

    /**
     * 
     * This function validate the forgot password form
     * If email is correct then generate new password and send it to the email given
     */
    public function user_reset_password() {
        $email = $this->input->post('email');
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        if ($email != '') {
            $condition = array('email' => $email);
            $riderVal = $this->user_model->get_all_details(USERS, $condition);
            if ($riderVal->num_rows() == 1) {
                $new_pwd = $this->get_rand_str('4');
                $newdata = array('reset_id' => $new_pwd);
                $condition = array('email' => $email);
                $this->user_model->update_details(USERS, $newdata, $condition);
                $resturn_res = $this->send_rider_reset_pwd_verification_code($new_pwd, $riderVal);
                $responseArr['status'] = '1';
                $this->setErrorMessage('success', '');
                $responseArr = array_merge($responseArr, $resturn_res);
                $responseArr['response'] = $this->format_string('Verification code has been sent to you', 'verification_code_sent');
            } else {
                $responseArr['response'] = $this->format_string('Email id not matched in our records', 'email_not_matched');
            }
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     * 
     * This function send the new password to driver email
     */
    public function send_rider_reset_pwd_verification_code($pwd = '', $query) {

        $user_name = $query->row()->user_name;
        /* ---------------SMS--------------------- */
        $phone_code = $query->row()->country_code;
        $phone_number = $query->row()->phone_number;
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }

        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
        $message = 'Dear ' . $user_name . ' Here is your verification code for reset password ' . $pwd . '. Team - ' . $this->config->item('email_title');
        $response = $this->twilio->sms($from, $to, $message);

        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['sms_status'] = $otp_status;
        $responseArr['verification_code'] = $pwd;
        $responseArr['email_address'] = $query->row()->email;


        /* ---------------EMAIL--------------------- */

        $newsid = '11';
        $verificationCode = $pwd;
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['subject'];
        $ridernewstemplateArr = array('email_title' => $this->config->item('email_title'), 'mail_emailTitle' => $this->config->item('email_title'), 'mail_logo' => $this->config->item('logo_image'), 'mail_footerContent' => $this->config->item('footer_content'), 'mail_metaTitle' => $this->config->item('meta_title'), 'mail_contactMail' => $this->config->item('site_contact_mail'));
        extract($ridernewstemplateArr);
        $message = '<!DOCTYPE HTML>
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width"/>
            <title>' . $template_values['subject'] . '</title>
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
        #var_dump($email_values);
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        return $responseArr;
    }

    /**
     * 
     * This function updates the reset password
     */
    function update_reset_password() {
        $email = $this->input->post('email');
        $pwd = $this->input->post('password');
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        if ($pwd != '' && $email != '') {
            $condition = array('email' => $email);
            $driverVal = $this->user_model->update_details(USERS, array('password' => md5($pwd), 'reset_id' => ''), $condition);
            $responseArr['status'] = '1';
            $responseArr['response'] = $this->format_string('Password changed successfully', 'password_changed');
        } else {
            $responseArr['response'] = $this->format_string('Some Parameters are missing', 'some_parameters_missing');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

}

/* End of file user_profile.php */
/* Location: ./application/controllers/mobile/user_profile.php */