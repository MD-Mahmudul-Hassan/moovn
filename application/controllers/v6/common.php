<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
* 
* User related functions
* @author Casperon
*
**/
 
class Common extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie','date','form','email'));
        $this->load->library(array('encrypt','form_validation'));
        $this->load->model('user_action_model'); 
        $this->load->model('app_model'); 
        $responseArr = array();
        
        /* Authentication Begin */
        $headers = $this->input->request_headers();
        header('Content-type:application/json;charset=utf-8');
        $current_function = $this->router->fetch_method();
        $public_functions = array('get_app_info');
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
        } else if (array_key_exists("Driver-Token", $headers)) {
            $this->driver_token = $headers['Driver-Token'];
            try {
                if (isset($this->driver_token)) {
                    $driver = $this->app_model->get_selected_fields(DRIVERS, array('sso_token' => $this->driver_token), array('_id'));
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
        /*Authentication End*/
    }
    
    /**
    *
    *   This function will update the users/drivers current availablity
    *
    **/
    
    public function update_receive_mode() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        
        try {
            $usertype = (string)strtolower($this->input->post('user_type'));    #   (user/driver)
            $id = (string)$this->input->post('id');
            $mode = (string)$this->input->post('mode'); #   (available/unavailable)
                        
            if($usertype != '' && $id != '' && $mode != ''){
                $collection = '';
                if($usertype == "user"){
                    $collection = USERS;
                }else if($usertype == "driver"){
                    $collection = DRIVERS;
                }
                if($collection!=''){
                    $userInfo = $this->app_model->get_selected_fields($collection, array('_id' => new \MongoId($id)), array('chat_status'));                    
                    if($userInfo->num_rows()==1){
                        $dataArr =  array('messaging_status' => strtolower($mode));
                        $condition =  array('_id' => new \MongoId($id));
                        $this->app_model->update_details($collection, $dataArr, $condition);
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $this->format_string("Status Updated Successfully",'status_update_success');
                    }else{
                        $returnArr['response'] = $this->format_string("Cannot find your identity",'cant_find_your_identity');
                    }
                }else{
                    $returnArr['response'] = $this->format_string("Cannot find your identity",'cant_find_your_identity');
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters are missing",'some_parameters_missing');
            }
        
        }catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
    *
    *   This function will update the driver location and send the location to user by notifications
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
    *   This function will return the information to app during launching
    *
    **/ 
    public function get_app_info() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        
        try {
            $usertype = (string)strtolower($this->input->post('user_type'));    #   (user/driver)
            $id = (string)$this->input->post('id');
            
            $site_mode_string = "currently we are not able to service you, please try again later";
            $site_mode_status = (string)$this->config->item('site_mode');
            if($site_mode_status==""){
                $site_mode_status = "development";  #(development/production)
            }
            
            $lang_code = "en";
            if($this->mailLang!=""){
                $lang_code = $this->mailLang;
            }
            
            $infoArr =  array('site_contact_mail' => (string)$this->config->item('site_contact_mail'),
                            'customer_service_number' => (string)$this->config->item('customer_service_number'),
                            'site_mode' => $site_mode_status,
                            'site_mode_string' => $site_mode_string,
                            'site_url' => base_url(),
                            'facebook_id' => (string)$this->config->item('facebook_app_id_android'),
                            'google_plus_app_id' => (string)$this->config->item('google_client_id'),
                            'driver_google_ios_key' => (string)$this->config->item('google_ios_key'),
                            'driver_google_android_key' => (string)$this->config->item('google_android_key'),
                            'driver_google_ios_server_key' => (string)$this->config->item('google_server_key'),
                            'driver_google_android_server_key' => (string)$this->config->item('google_ios_key'),
                            'user_google_ios_key' => (string)$this->config->item('google_ios_key'),
                            'user_google_android_key' => (string)$this->config->item('google_android_key'),
                            'user_google_ios_server_key' => (string)$this->config->item('google_server_key'),
                            'user_google_android_server_key' => (string)$this->config->item('google_ios_key'),
                            'app_identity_name' => (string)APP_NAME,
                            'about_content' => (string)$this->config->item('about_us'),
                            'user_image' => (string)"",
                            'lang_code' => (string)$lang_code
                            );          
                            
            if($usertype != '' && $id != ''){
                $collection = '';
                if($usertype == "user"){
                    $collection = USERS;
                }else if($usertype == "driver"){
                    $collection = DRIVERS;
                }
                if($collection!=''){
                    $userInfo = $this->app_model->get_selected_fields($collection, array('_id' => new \MongoId($id)), array('chat_status','lang_code'));                    
                    if($userInfo->num_rows()==1){
                        if(isset($userInfo->row()->lang_code)){
                            $infoArr['lang_code'] = $userInfo->row()->lang_code;
                        }else{
                            $infoArr['lang_code'] = "en";
                        }                       
                    }
                }
                if($collection == USERS){
                    $userVal = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($id)), array( 'image'));
                    $user_image = USER_PROFILE_IMAGE_DEFAULT;
                    if(isset($userVal->row()->image)){
                        if ($userVal->row()->image != '') {
                            $user_image = USER_PROFILE_IMAGE . $userVal->row()->image;
                        }
                    }
                    $infoArr['user_image'] = base_url() . $user_image;
                }
                        
            }
            $returnArr['status'] = '1';
            $returnArr['response'] = array('info'=>$infoArr);
        
        }catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
    *
    *   This function will update the driver location and send the location to user by notifications
    *
    **/ 
    public function driver_update_ride_location() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        
        try {
            $ride_id = (string)$this->input->post('ride_id');
            $lat = (string)$this->input->post('lat');
            $lon = (string)$this->input->post('lon');
            $bearing = (string)$this->input->post('bearing');
            
            if ($ride_id!='' && $lat!='' && $lon!='') {
                $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status','user'));
                if ($checkRide->num_rows() == 1) {
                    $ride_location[] = array('lat' => $lat,
                        'lon' => $lon,
                        'update_time' => new MongoDate(time())
                    );
                    $checkRideHistory = $this->app_model->get_selected_fields(RIDE_HISTORY, array('ride_id' => $ride_id), array('values'));
                    if ($checkRideHistory->num_rows() > 0) {
                        if (!empty($travel_historyArr)) {
                            $this->app_model->simple_push(RIDE_HISTORY, array('ride_id' => $ride_id), array('values' => $rowVal));
                        }
                    } else {
                        if (!empty($travel_historyArr)) {
                            $this->app_model->simple_insert(RIDE_HISTORY,array('ride_id' => $ride_id, 'values' => $travel_historyArr));
                        }
                    }
                    /* Notification to user about driver current ride location */
                    $user_id = $checkRide->row()->user['id'];
                    $userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('fcm_token', 'device_type'));
                    $message = 'Driver current ride location';
                    $options = array('action' => 'driver_loc', 'ride_id' => (string) $ride_id, 'latitude' => (string) $lat, 'longitude' => (string) $lon, 'bearing' => (string) $bearing);
                    $user_type = 'rider';
                    $silent = true;
                    $this->notify($userVal->row()->fcm_token, $message, 'driver_loc', $options, $userVal->row()->device_type, $user_type, $silent);
                    
                    $returnArr['status'] = '1';
                    $returnArr['response'] = $this->format_string('Updated Successfully', 'updated_successfully');
                } else {
                    $returnArr['response'] = $this->format_string('Invalid Ride', 'invalid_ride');
                }
            } else {
                $returnArr['response'] = $this->format_string('Some Parameters Missing', 'some_parameters_missing');
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection', 'error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
    *
    *   This function will upload the user profile picture
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
/* Location: ./application/controllers/api_v3/common.php */