<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * This controller contains the functions related to Notification and Email Templates management 
 * @author Casperon
 *
 * */
class Notification extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation','curl'));
        $this->load->model(array('notification_model', 'location_model'));

        if ($this->checkPrivileges('notification', $this->privStatus) == FALSE) {
            redirect('admin');
        }
    }

    /**
     *
     * This function loads the users list page
     *
     * */
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            redirect('admin/notification/display_notification_user_list');
        }
    }

    /*
     *
     * Display user lists to send a notification
     *
     * 
     */
    public function display_notification_user_list()
    {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $condition = array('status' => 'Active');
            if ($this->lang->line('admin_notification_send_notification') != '') {
                $this->data['heading']= stripslashes($this->lang->line('admin_notification_send_notification'));
            } else {
                $this->data['heading'] = 'Send Notification';
            }
            $filterArr = array();
            if ((isset($_GET['type']) && isset($_GET['value'])) && ($_GET['type'] != '' && $_GET['value'] != '')) {
                if (isset($_GET['type']) && $_GET['type'] != '') {
                    $this->data['type'] = $_GET['type'];
                }
                if (isset($_GET['value']) && $_GET['value'] != '') {
                    $this->data['value'] = $_GET['value'];
                    $filter_val = $this->data['value'];
                }
                $this->data['filter'] = 'filter';
                if ($_GET['type'] == 'phone_number') {
                    $condition = array_merge($condition, array('country_code' => $_GET['country']));
                    $filterArr = array($this->data['type'] => $filter_val);
                } else if ($_GET['type'] == 'location') {
                    $condition = array_merge($condition, array('location_id' => new MongoId($filter_val)));
                } else {
                    $filterArr = array($this->data['type'] => $filter_val);
                }
            } else if (isset($_GET['type']) && $_GET['type'] == 'phone_number') {
                if (isset($_GET['type']) && $_GET['type'] != '') {
                    $this->data['type'] = $_GET['type'];
                }
                $condition = array_merge($condition, array('country_code' => $_GET['country']));
            }
            $this->data['locationList'] = $this->location_model->get_all_details(LOCATIONS, array());
            $this->data['template_details']=$this->app_model->get_all_details(NOTIFICATION_TEMPLATES, array());
            $usersCount = $this->app_model->get_all_counts(USERS, $condition, $filterArr);
            if ($usersCount > 1000) {
                $searchPerPage = 500;
                $paginationNo = $this->uri->segment(4);
                if ($paginationNo == '') {
                    $paginationNo = 0;
                } else {
                    $paginationNo = $paginationNo;
                }
                $this->data['usersList'] = $this->app_model->get_all_details(USERS, $condition, array('created' => 'DESC'), $searchPerPage, $paginationNo, $filterArr);
                $searchbaseUrl = 'admin/notification/display_notification_user_list';
                $config['num_links'] = 3;
                $config['display_pages'] = true;
                $config['base_url'] = $searchbaseUrl;
                if (count($_GET) > 0) {
                    $config['suffix'] = '?' . http_build_query($_GET, '', "&");
                    $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
                }
                $config['total_rows'] = $usersCount;
                $config["per_page"] = $searchPerPage;
                $config["uri_segment"] = 4;
                $config['first_link'] = '';
                $config['last_link'] = '';
                $config['full_tag_open'] = '<ul class="tsc_pagination tsc_paginationA tsc_paginationA01">';
                $config['full_tag_close'] = '</ul>';
                if ($this->lang->line('pagination_prev_lbl') != '') {
                    $config['prev_link'] = stripslashes($this->lang->line('pagination_prev_lbl'));
                } else {
                    $config['prev_link'] ='Prev';
                }
                $config['prev_tag_open'] = '<li>';
                $config['prev_tag_close'] = '</li>';
                if ($this->lang->line('pagination_next_lbl') != '') {
                    $config['next_link'] = stripslashes($this->lang->line('pagination_next_lbl'));
                } else {
                    $config['next_link'] ='Next';
                }
                $config['next_tag_open'] = '<li>';
                $config['next_tag_close'] = '</li>';
                $config['cur_tag_open'] = '<li class="current"><a href="javascript:void(0);" style="cursor:default;">';
                $config['cur_tag_close'] = '</a></li>';
                $config['num_tag_open'] = '<li>';
                $config['num_tag_close'] = '</li>';
                $config['first_tag_open'] = '<li>';
                $config['first_tag_close'] = '</li>';
                $config['last_tag_open'] = '<li>';
                $config['last_tag_close'] = '</li>';
                if ($this->lang->line('pagination_first_lbl') != '') {
                    $config['first_link'] = stripslashes($this->lang->line('pagination_first_lbl'));
                }
                else {
                    $config['first_link'] ='First';
                }
                if ($this->lang->line('pagination_last_lbl') != '') {
                    $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));
                }
                else {
                    $config['last_link'] ='Last';
                }
                $this->pagination->initialize($config);
                $paginationLink = $this->pagination->create_links();
                $this->data['paginationLink'] = $paginationLink;
                $this->load->view('admin/notification/display_notification_userlist', $this->data);
            } else {
                $this->data['usersList'] = $this->app_model->get_all_details(USERS, $condition, '', '', '', $filterArr);
                $this->data['paginationLink'] = '';
                $this->load->view('admin/notification/display_notification_userlist', $this->data);
            }
        }
    }
    
    
    /*
     *
     * Display drivers lists to send a notification
     *
     * 
     */

     public function display_notification_driver_list() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            /* Getting Notification Details --- Ends Here ------ */
            
            if ($this->lang->line('admin_notification_send_notification') != '') {
                $this->data['heading']= stripslashes($this->lang->line('admin_notification_send_notification')); 
            }else{
                $this->data['heading'] = 'Send Notification';
            }
            
            $condition = array('status' => 'Active');
            $filterArr = array();
            if (isset($_GET['type']) && (isset($_GET['value']) || isset($_GET['dropdown_value']) || isset($_GET['vehicle_category']))){
                if($_GET['type'] != '' && ($_GET['value'] != '' || $_GET['dropdown_value'] != '' || $_GET['vehicle_category'] != '')) {
                    if (isset($_GET['type']) && $_GET['type'] != '') {
                        $this->data['type'] = $_GET['type'];
                    }
                    
                     if (isset($_GET['dropdown_value']) && $_GET['dropdown_value'] != '') {
                        $this->data['dropdown_value'] = $_GET['dropdown_value'];
                        $filter_val = $this->data['dropdown_value'];
                    }else if (isset($_GET['value']) && $_GET['value'] != '') {
                        $this->data['value'] = $_GET['value'];
                        $filter_val = $this->data['value'];
                    }
                    
                    $this->data['filter'] = 'filter';
                    $filterCondition = array();
                    if($_GET['type'] == 'vehicle_type'){
                        $vehicle_category = trim($_GET['vehicle_category']);
                        $categoryVal=$this->user_model->get_all_details(CATEGORY,'','','','',array('name'=>$vehicle_category));
                        $condition = array('category' => $categoryVal->row()->_id,'status' => 'Active');
                    }else if($_GET['type'] == 'driver_location') {
                        $location=$this->user_model->get_all_details(LOCATIONS,'','','','',array('city'=>$filter_val));
                        $filterArr = array($this->data['type'] => $location->row()->_id);
                    } else if($_GET['type'] == 'mobile_number') {
                        $condition = array_merge($condition, array('dail_code' => $_GET['country']));
                        $filterArr = array($this->data['type'] => $filter_val);
                    }else{
                        $filterArr = array($this->data['type'] => $filter_val);
                    }
                } else if (isset($_GET['type']) && $_GET['type'] == 'mobile_number') {
                    if (isset($_GET['type']) && $_GET['type'] != '') {
                        $this->data['type'] = $_GET['type'];
                    }
                    $condition = array_merge($condition, array('dail_code' => $_GET['country']));
                }
            } 
            $cabCats = $this->app_model->get_selected_fields(CATEGORY, array(), array('_id', 'name'))->result();
            $cabsTypeArr = array();
            foreach ($cabCats as $cab) {
                $cabId = (string) $cab->_id;
                $cabsTypeArr[$cabId] = $cab;
            }
            $this->data['cabCats'] = $cabsTypeArr;
            #$condition = array("push_notification.key" => array('$nin' => array(' ', '', null)), "push_notification.type" => array('$nin' => array(' ', '', null)));

            if ((isset($_GET['type']) && isset($_GET['value'])) && ($_GET['type'] != '' && $_GET['value'] != '')) {
                $this->data['type'] = $_GET['type'];
                $this->data['value'] = $_GET['value'];
                $filter_val = $this->data['value'];
                $this->data['filter'] = 'filter';
            } #var_dump($filterArr); die;
            $this->data['template_details']=$this->app_model->get_all_details(NOTIFICATION_TEMPLATES,array());
            $driversCount = $this->app_model->get_all_counts(DRIVERS, $condition, $filterArr);
            if ($driversCount > 1000) {

                $searchPerPage = 500;
                $paginationNo = $this->uri->segment(4);
                if ($paginationNo == '') {
                    $paginationNo = 0;
                } else {
                    $paginationNo = $paginationNo;
                }

                $this->data['driversList'] = $this->app_model->get_all_details(DRIVERS, $condition, array('created' => 'DESC'), $searchPerPage, $paginationNo, $filterArr);

                $searchbaseUrl = 'admin/notification/display_notification_driver_list';
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
                $config['base_url'] = $searchbaseUrl;
                $config['total_rows'] = $driversCount;
                $config["per_page"] = $searchPerPage;
                $config["uri_segment"] = 4;
                $config['first_link'] = '';
                $config['last_link'] = '';
                $config['full_tag_open'] = '<ul class="tsc_pagination tsc_paginationA tsc_paginationA01">';
                $config['full_tag_close'] = '</ul>';
                if ($this->lang->line('pagination_prev_lbl') != '') $config['prev_link'] =stripslashes($this->lang->line('pagination_prev_lbl'));  else  $config['prev_link'] ='Prev';
                $config['prev_tag_open'] = '<li>';
                $config['prev_tag_close'] = '</li>';
                if ($this->lang->line('pagination_next_lbl') != '') $config['next_link'] =stripslashes($this->lang->line('pagination_next_lbl'));  else  $config['next_link'] ='Next';
                $config['next_tag_open'] = '<li>';
                $config['next_tag_close'] = '</li>';
                $config['cur_tag_open'] = '<li class="current"><a href="javascript:void(0);" style="cursor:default;">';
                $config['cur_tag_close'] = '</a></li>';
                $config['num_tag_open'] = '<li>';
                $config['num_tag_close'] = '</li>';
                $config['first_tag_open'] = '<li>';
                $config['first_tag_close'] = '</li>';
                $config['last_tag_open'] = '<li>';
                $config['last_tag_close'] = '</li>';
                if ($this->lang->line('pagination_first_lbl') != '') $config['first_link'] =stripslashes($this->lang->line('pagination_first_lbl'));  else  $config['first_link'] ='First';
                if ($this->lang->line('pagination_last_lbl') != '') $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));  else  $config['last_link'] ='Last';
                $this->pagination->initialize($config);
                $paginationLink = $this->pagination->create_links();
                $this->data['paginationLink'] = $paginationLink;


                $this->load->view('admin/notification/display_notification_driverlist', $this->data);
            } else {
                $this->data['driversList'] = $this->app_model->get_all_details(DRIVERS, $condition, '', '', '', $filterArr);
                // print_r( $this->data['driversList']->result());die;
                $this->data['paginationLink'] = '';
                $this->load->view('admin/notification/display_notification_driverlist', $this->data);
            }
        }
    }
    
    /*
     *
     * This sends notification to all users that have been checked
     *
     * 
     */
     
    public function send_notification_to_device() {
        if($this->checkLogin('A') == ''){
            redirect('admin');
        }
        $userIds = $this->input->post('userIds');
        $notification_id = $this->input->post('notification_id');
        $user_type = $this->input->post('user_type');
        $redirect_url='admin/notification/display_notification_'.$user_type.'_list';
         
        if(!empty($notification_id)){
        if(!empty($userIds)){
        if($notification_id == ''){
            $this->setErrorMessage('error','Please select notification template','admin_notification_select_notification_template');
            redirect('admin/notification/display_notification_user_list');
        }
        
        $getTemplateDetails = $this->app_model->get_all_details(NOTIFICATION_TEMPLATES,array('news_id' => (int)$notification_id));
        
        if($getTemplateDetails->num_rows() <= 0){
            $this->setErrorMessage('error','Notification records not available','admin_notification_records_not_available');
            redirect('admin/notification/display_notification_user_list');
        }
        
        $title = $getTemplateDetails->row()->message['title'];
        $description = $getTemplateDetails->row()->message['msg_description'];
        $image_name = '';
        if(isset($getTemplateDetails->row()->message['image'])){
            $image_name = base_url() . 'images/notification/' . $getTemplateDetails->row()->message['image'];
        }

        $u_id = @explode(',', $userIds);
        $u_id = array_filter(array_unique($u_id));

        $usersList = $this->app_model->get_selected_fields_where_in(USERS, array('_id', $u_id, ''), array('fcm_token', 'device_type', 'push_notification_key', 'push_type'));
        /*  ------- Getting an uploaded image details and do upload it in an appropriate file ------------
         *  -------- Image uploading path is **** images/notification **** ---------------------
         */
        
        $options = array('title' => $title,
            'message' => $description,
            'image' => $image_name
        );

        $android_user = array();
        $apple_user = array();
        $user_type = 'rider';
        foreach ($usersList->result_array() as $user) {
            if (isset($user['fcm_token'])) {
                if (isset($user['device_type']) && !empty($user['device_type'])) {
                    $this->notify($user['fcm_token'], $title, 'ads', $options, $user['device_type'], $user_type);
                }
            } else {
                if (array_key_exists('push_type', $user)) {
                    if ($user['push_type'] == 'ANDROID') {
                        if (isset($user['push_notification_key']['gcm_id'])) {
                            if ($user['push_notification_key']['gcm_id'] != '') {
                                $this->sendPushNotification($user['push_notification_key']['gcm_id'], $title, 'ads', 'ANDROID', $options, 'USER');
                            }
                        }
                    }
                    if ($user['push_type'] == 'IOS') {
                        if (isset($user['push_notification_key']['ios_token'])) {
                            if ($user['push_notification_key']['ios_token'] != '') {
                                $this->sendPushNotification($user['push_notification_key']['ios_token'], $title, 'ads', 'IOS', $options, 'USER');
                            }
                        }
                    }
                }
                
            }
        }

        $this->setErrorMessage('success', 'Notifications Sent.','admin_notification_sent');
        redirect('admin/notification/display_notification_user_list');
        
        }else{
            if($user_type=="driver"){
                $this->setErrorMessage('error', 'Please select one or more driver\'s','admin_notification_select_drivers');
            }else{
                $this->setErrorMessage('error', 'Please select one or more user\'s','admin_notification_select_user');
            }
            redirect($redirect_url);
         }
        }else{
            $this->setErrorMessage('error', 'Please select Notification template','admin_notification_select_notification_template');
            redirect($redirect_url);
        }
    }
    
    /*
     *
     * This sends notification to all drivers that have been checked
     *
     * 
     */
    
    public function send_notification_to_device_driver() {
        if($this->checkLogin('A') == ''){
            redirect('admin');
        }
        $userIds = $this->input->post('userIds');
        $notification_id = $this->input->post('notification_id');
        $user_type = $this->input->post('user_type');
        $redirect_url='admin/notification/display_notification_'.$user_type.'_list';
        if(!empty($notification_id)){
            if(!empty($userIds)){
        
        
        if($notification_id == ''){
            $this->setErrorMessage('error','Please select notification template','admin_notification_select_notification_template');
            redirect('admin/notification/display_notification_user_list');
        }
        
        $getTemplateDetails = $this->app_model->get_all_details(NOTIFICATION_TEMPLATES,array('news_id' => (int)$notification_id));
        
        if($getTemplateDetails->num_rows() <= 0){
            $this->setErrorMessage('error','Notification records not available','admin_notification_records_not_available');
            redirect('admin/notification/display_notification_user_list');
        }
        
        
        
        $title = $getTemplateDetails->row()->message['title'];
        $description = $getTemplateDetails->row()->message['msg_description'];
        $image_name = '';
        if(isset($getTemplateDetails->row()->message['image'])){
            $image_name = base_url() . 'images/notification/' . $getTemplateDetails->row()->message['image'];
        }
        
        $u_id = @explode(',', $userIds);
        $u_id = array_filter(array_unique($u_id));
        $usersList = $this->app_model->get_selected_fields_where_in(DRIVERS, array('_id', $u_id, ''), array('fcm_token', 'device_type', 'push_notification'));
        /*  ------- Getting an uploaded image details and do upload it in an appropriate file ------------
         *  -------- Image uploading path is **** images/notification **** ---------------------
         */

        $options = array('title' => $title,
            'message' => $description,
            'image' => $image_name
        );
        $user_type = 'driver';
        foreach ($usersList->result_array() as $user) {
            if (isset($user['fcm_token'])) {
                if (isset($user['device_type']) && !empty($user['device_type'])) {
                    $this->notify($user['fcm_token'], $title, 'ads', $options, $user['device_type'], $user_type);
                }
            } else {
                if (array_key_exists('push_notification', $user)) {
                    if ($user['push_notification']['type'] == 'ANDROID') {
                        if (isset($user['push_notification']['key'])) {
                            if ($user['push_notification']['key'] != '') {
                                $this->sendPushNotification($user['push_notification']['key'], $message, 'ads', 'ANDROID', $options, 'DRIVER');
                            }
                        }
                    }
                    if ($user['push_notification']['type'] == 'IOS') {
                        if (isset($user['push_notification']['key'])) {
                            if ($user['push_notification']['key'] != '') {
                                $this->sendPushNotification($user['push_notification']['key'], $message, 'ads', 'IOS', $options, 'DRIVER');
                            }
                        }
                    }
                }
            }
        }

        $this->setErrorMessage('success', 'Notifications Sent.','admin_notification_sent');
        redirect('admin/notification/display_notification_driver_list');
    
        }else{
            if($user_type=="driver"){
                $this->setErrorMessage('error', 'Please select one or more driver\'s','admin_notification_select_drivers');
            }else{
                $this->setErrorMessage('error', 'Please select one or more user\'s','admin_notification_select_user');
            }
            redirect($redirect_url);
         }
        }else{
            $this->setErrorMessage('error', 'Please select Notification template','admin_notification_select_notification_template');
            redirect($redirect_url);
        }
    }
    
    /*
    /
    /***** Sending bulk email to Drivers or Users  ********
    /
   */
    public function send_email_to_users(){
         if($this->checkLogin('A') == ''){
            redirect('admin');
        }
        
        $userIds = $this->input->post('userIds');
        $email_id = $this->input->post('email_id');
        $user_type = $this->input->post('user_type');
        $redirect_url='admin/notification/display_notification_'.$user_type.'_list';
        if(!empty($email_id)){
        if(!empty($userIds)){
                $fields=$email_list=array();
                $fields['news_id']=$email_id;
                if($user_type=='user'){
                    $collection = USERS;
                }else{
                    $collection = DRIVERS;
                }
            if($collection!=''){
                        
                $u_id = @explode(',', $userIds);
                $u_id = array_filter(array_unique($u_id));
                $usersList = $this->app_model->get_selected_fields_where_in($collection, array('_id', $u_id, ''), array('email'));
                
                
                foreach($usersList->result_array() as $user){
                    $email_list[]=$user['email'];
                }
                
                $total_email = count($email_list); //Total Emails
                $num_of_email_to_send=50;
                $ne = ceil($total_email/$num_of_email_to_send);//Number of Execution
                $max=0;
                $url = base_url() . 'send-bulk-emails';
                    for($i=0;$i<$ne;$i++){
                        $to="";
                        $sliced_array=array_slice($email_list,$max,$num_of_email_to_send);
                        $bcc=implode(",",$sliced_array);
                        $fields['to_mail_id']=$to;
                        $fields['bcc_mail_id']=$bcc;
                        $max=($i+1)*$num_of_email_to_send;
                        $output = $this->curl->simple_post($url, $fields);
                    }
                $this->setErrorMessage('success', 'Successfully Emails Sent!','admin_notification_successfully_email_sent');
                redirect($redirect_url);    
            }
                
        }else{
            if($user_type=="driver"){
                $this->setErrorMessage('error', 'Please select one or more driver\'s','admin_notification_select_drivers');
            }else{
                $this->setErrorMessage('error', 'Please select one or more user\'s','admin_notification_select_user');
            }
            redirect($redirect_url);
         }
        }else{
            $this->setErrorMessage('error', 'Please select email template','admin_notification_select_email_template');
            redirect($redirect_url);
        }
        
    }
    
    public function send_sms_to_users() {
        if ($this->checkLogin('A') == '') {
           redirect('admin');
        }      
        $userIds = $this->input->post('userIds');
        $user_type = $this->input->post('user_type');
        $news_id = $this->input->post('sms_id');
        $redirect_url = 'admin/notification/display_notification_'.$user_type.'_list';
        if (!empty($news_id)) {
            if (!empty($userIds)) {
                $getTemplateDetails = $this->app_model->get_all_details(NOTIFICATION_TEMPLATES, array('news_id' => (int) $news_id));
                $smsContent = $getTemplateDetails->row()->message['sms_body'];

                $user_ids = @explode(',', $userIds);
                $user_ids = array_filter(array_unique($user_ids));

                if ($user_type == 'user'){
                    $usersList = $this->app_model->get_selected_fields_where_in(USERS, array('_id', $user_ids, ''), array('country_code', 'phone_number'));
                } else{
                    $usersList = $this->app_model->get_selected_fields_where_in(DRIVERS, array('_id', $user_ids, ''), array('dail_code', 'mobile_number'));
                }
                $from = $this->config->item('twilio_number');
                foreach($usersList->result_array() as $user) {
                    if ($user_type == 'user'){
                        $to = $user['country_code'] . $user['phone_number'];
                    } else{
                        $to = $user['dail_code'] . $user['mobile_number'];
                    }
                    $sendSmsResponse[] = $this->sms_model->send($from, $to, $smsContent);
                }
                if ($sendSmsResponse[0]->IsError === true) {
                    $this->setErrorMessage('error', $sendSmsResponse[0]->ErrorMessage);
                } else {
                    $this->setErrorMessage('success', 'Successfully SMS\'s Sent!','admin_notification_successfully_sms_sent');
                }
                redirect($redirect_url);    
            } else{
                if($user_type == "driver"){
                    $this->setErrorMessage('error', 'Please select one or more driver\'s', 'admin_notification_select_drivers');
                }else{
                    $this->setErrorMessage('error', 'Please select one or more user\'s', 'admin_notification_select_user');
                }
                redirect($redirect_url);
            }
        } else {
           $this->setErrorMessage('error', 'Please select sms template', 'admin_notification_select_sms_template');
           redirect($redirect_url);
       }

    }

    /*
     *
     * Display all available notification and email templates
     *
     * 
     */
    public function display_notification_templates(){
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->lang->line('admin_notification_display_notifications_newsletters') != '') 
            $this->data['heading']= stripslashes($this->lang->line('admin_notification_display_notifications_newsletters')); 
            else  $this->data['heading'] = 'Display Notifications Newsletters';
            $this->data['display_newsletters'] = $this->app_model->get_all_details(NOTIFICATION_TEMPLATES,array());
            $this->load->view('admin/notification/display_notification_templates', $this->data);
        }
    }
    
    /**
    * 
    * This function loads the add templates page
    *
    **/
    public function add_notification_template(){
        if ($this->checkLogin('A') == ''){
            redirect('admin');
        }else {
           if ($this->lang->line('admin_notification_add_new_notification_template') != '') 
           $this->data['heading']= stripslashes($this->lang->line('admin_notification_add_new_notification_template')); 
           else  $this->data['heading'] = 'Add New Notification Template';
           $this->load->view('admin/notification/add_notification_template',$this->data);
        }
    }
    
    /**
    * 
    * This function loads edit templates page
    *
    **/
    public function edit_notification_template(){
        if ($this->checkLogin('A') == ''){
            redirect('admin');
        }else {
            if ($this->lang->line('admin_notification_edit_notification_template') != '') 
            $this->data['heading']= stripslashes($this->lang->line('admin_notification_edit_notification_template')); 
            else  $this->data['heading'] = 'Edit Notification Template';
            $template_id = $this->uri->segment(4,0);
            if($template_id != ''){
                $condition = array('_id' =>  new \MongoId($template_id));
                $this->data['templateDetails'] = $this->app_model->get_all_details(NOTIFICATION_TEMPLATES,$condition);
                if($this->data['templateDetails']->num_rows() == 0){
                    $this->setErrorMessage('error', 'No records found','admin_notification_no_records_found');
                    redirect("admin/notification/display_notification_templates");  
                }
            } else {
                $this->setErrorMessage('error', 'No records found','admin_notification_no_records_found');
                redirect("admin/notification/display_notification_templates");  
            }
            $this->load->view('admin/notification/edit_notification_template',$this->data);
        }
    }
    
    /**
    * 
    * This function loads the view notification and email templates page
    *
    **/
    public function view_notification_template(){
        if ($this->checkLogin('A') == ''){
            redirect('admin');
        }else {
            if ($this->lang->line('admin_notification_edit_notification_template') != '') 
            $this->data['heading']= stripslashes($this->lang->line('admin_notification_edit_notification_template')); 
            else  $this->data['heading'] = 'Edit Notification Template';
            //$this->data['heading'] = 'Edit Notification Template';
            $template_id = $this->uri->segment(4,0);
            if($template_id != ''){
                $condition = array('_id' =>  new \MongoId($template_id));
                $this->data['template_details'] = $this->app_model->get_all_details(NOTIFICATION_TEMPLATES,$condition);
                if($this->data['template_details']->num_rows() == 0){
                    $this->setErrorMessage('error', 'No records found','admin_notification_no_records_found');
                    redirect("admin/notification/display_notification_templates");  
                }                   
            } else {
                $this->setErrorMessage('error', 'No records found','admin_notification_no_records_found');
                redirect("admin/notification/display_notification_templates");  
            }
            $this->load->view('admin/notification/view_email_template',$this->data);
        }
    }
    
    /**
    * 
    * This function insert and edit a notification template
    *
    */
    public function insertEditNotificationTemplate(){
        if ($this->checkLogin('A') == ''){
            redirect('admin');
        }else { 
            $template_id = $this->input->post('_id');
            $excludeArr = array("_id","status","image","old_image");
            $etemplate_status = 'Active';
            $dataArr = array();

            $getTemplates=$this->app_model->get_selected_fields(NOTIFICATION_TEMPLATES,array(),array('news_id'),array('news_id'=>'DESC'));
            if ($template_id == ''){
                $nid = $getTemplates->row()->news_id->value;
                $news_id = $nid+1;
                $dataArr = array(
                    'news_id'=> new \MongoInt64 ($news_id),
                    'status' => $etemplate_status,
                    'created_date'=>date('Y-m-d H:i:s')
                    );
            }
            $notification_type = $this->input->post('notification_type');
            $temp_description = str_replace("'.base_url().'", base_url(), $this->input->post('message[mail_description]'));
            
            if ($template_id == ''){
                $condition = array();
                $this->app_model->commonInsertUpdate(NOTIFICATION_TEMPLATES,'insert',$excludeArr,$dataArr,$condition);
                $temp_id=$this->app_model->get_last_insert_id(); 
                $template_id = $temp_id;                
                if($notification_type == 'email'){
                    $condition = array('_id' =>  new \MongoId($temp_id));
                    $template_content=$this->app_model->get_selected_fields(NOTIFICATION_TEMPLATES,$condition,array('message.mail_description','news_id'));
                    $etemp_id=$template_content->row()->news_id;
                    $template_content_new = str_replace("{","'.",addslashes($template_content->row()->message['mail_description']));
                    $temp_description = str_replace("}",".'",$template_content_new);
                    
                    $config = "<?php \$message .= '";
                    $config .= "$temp_description";
                    $config .= "';  ?>";
                    $file = 'newsletter/notify_template'.$etemp_id.'.php';
                    file_put_contents($file, $config);
                }
                if($notification_type=="email"){
                    $this->setErrorMessage('success', 'Email template added successfully','admin_notification_email_temp_add_success');
                } else if($notification_type=="sms"){
                    $this->setErrorMessage('success', 'SMS template added successfully','admin_notification_sms_temp_add_success');
                } else{
                    $this->setErrorMessage('success', 'Notification template added successfully','admin_notification_notification_temp_add_success');
                }
            }else {
                $temp_description = str_replace("adminnewstemplateArr['","",$temp_description);
                $temp_description = str_replace("']","",$temp_description);
                    
                $condition = array('_id' =>  new \MongoId($template_id));
                $template_contentold=$this->app_model->get_selected_fields(NOTIFICATION_TEMPLATES,$condition,array('message.mail_description','news_id'));
                $dataArr = array('news_id'=> new \MongoInt64 ($template_contentold->row()->news_id));
                $this->app_model->commonInsertUpdate(NOTIFICATION_TEMPLATES,'update',$excludeArr,$dataArr,$condition);
                $template_content=$this->app_model->get_selected_fields(NOTIFICATION_TEMPLATES,$condition,array('message.mail_description','news_id'));
                
                if($notification_type == 'email'){
                    $etemp_id=$template_content->row()->news_id;
                    $template_content_new = str_replace("{","'.",addslashes($template_content->row()->message['mail_description']));
                    $temp_description = str_replace("}",".'",$template_content_new);
                    
                    $config = "<?php \$message .= '";
                    $config .= "$temp_description";
                    $config .= "';  ?>";
                    $file = 'newsletter/notify_template'.$etemp_id.'.php';
                    file_put_contents($file, $config);
                }
                if($notification_type=="email"){
                    $this->setErrorMessage('success', 'Email template updated successfully','admin_notification_email_temp_update_success');
                } else if($notification_type=="sms"){
                    $this->setErrorMessage('success', 'SMS template updated successfully','admin_notification_sms_temp_update_success');
                } else{
                    $this->setErrorMessage('success', 'Notification template updated successfully','admin_notification_notification_temp_update_success');
                }
            }
            $notificationImageName = ''; 
            $notificationImageName = $this->input->post('old_image');  
            if ($_FILES['image']['name'] != '' && $template_id != '') {
                $config['overwrite'] = false;
                $config['encrypt_name'] = TRUE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = 'images/notification';
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('image')) {
                    $imageData = $this->upload->data();
                    $notificationImageName = $imageData['file_name'];
                } else { 
                    $error = array('error' => $this->upload->display_errors()); 
                    $this->setErrorMessage('error', $error['error']);
                    redirect('admin/notification/display_notification_templates');
                }
            } 
            if($notificationImageName != ''){
                $condition = array('_id' =>  new \MongoId($template_id));
                $this->app_model->update_details(NOTIFICATION_TEMPLATES,array('message.image' => $notificationImageName),$condition);
            } 
            redirect('admin/notification/display_notification_templates');
        }
    }
    
    /**
    * 
    * This function delete the template you have chosen
    *
    **/
    public function delete_email_template(){
        if ($this->checkLogin('A') == ''){
            redirect('admin');
        }else {
            $template_id = $this->uri->segment(4,0);
            if($template_id != ''){
                $condition = array('_id' =>  new \MongoId($template_id));
                $template_details = $this->app_model->get_selected_fields(NOTIFICATION_TEMPLATES,$condition,array('_id'));
                if($template_details->num_rows() == 0){
                    $this->setErrorMessage('error', 'No records found','admin_notification_no_records_found');
                    redirect("admin/notification/display_notification_templates");
                }else{
                    
                    $this->app_model->commonDelete(NOTIFICATION_TEMPLATES,array('_id'=>$template_details->row()->_id));
                    $this->setErrorMessage('success', 'Template have been deleted successfully!','admin_notification_template_delete_success');
                    redirect("admin/notification/display_notification_templates");
                }                   
            } else {
                $this->setErrorMessage('error', 'Choose a template to delete','admin_notification_template_delete');
                redirect("admin/notification/display_notification_templates");  
            }
            $this->load->view('admin/notification/view_email_template',$this->data);
        }
    }

}

/* End of file notification.php */
/* Location: ./application/controllers/admin/notification.php */
?>