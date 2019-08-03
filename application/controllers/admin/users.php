<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to SMS and Email Templates management 
 * @author Casperon
 *
 * */
class Users extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));

		$this->load->library(array('encrypt', 'form_validation', 'excel'));
        $this->load->model(array('user_model', 'location_model'));

        if ($this->checkPrivileges('user', $this->privStatus) == FALSE) {
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
            redirect('admin/users/display_user_list');
        }
    }

    /**
     * 
     * This function loads the users list page
     *
     * */
    public function display_user_list() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
			if ($this->lang->line('admin_menu_users_list') != '') {
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_users_list')); 
            }
		    else {
                $this->data['heading'] = 'Users List';
            }			
			$condition = array('status' => array('$ne' => 'Deleted'));
			$user_type = $this->input->get('user_type');
			if($user_type == 'deleted'){
				$condition = array('status' => 'Deleted');
			}
			$sortArr  = array('created' => 'DESC');
			$sortby = '';
			if (isset($_GET['sortby'])) {
				$this->data['filter'] = 'filter';
				$sortby = $_GET['sortby'];
				if($sortby=="doj_asc"){
					$sortArr  = array('created' => 'ASC');
				}
				if($sortby=="doj_desc"){
					$sortArr  = array('created' => 'DESC');
				}
				if($sortby=="rides_asc"){
					$sortArr  = array('no_of_rides' => 'ASC');
				}
				if($sortby=="rides_desc"){
					$sortArr  = array('no_of_rides' => 'DESC');
				}
			}
			$this->data['sortby'] = $sortby;
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
               if($_GET['type'] == 'phone_number') {
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
            $usersCount = $this->user_model->get_all_counts(USERS, $condition, $filterArr);
            if ($usersCount > 1000) {
                $searchPerPage = 500;
                $paginationNo = $this->uri->segment(4);
                if ($paginationNo == '') {
                    $paginationNo = 0;
                } else {
                    $paginationNo = $paginationNo;
                }
                $this->data['usersList'] = $this->user_model->get_all_details(USERS, $condition,$sortArr, $searchPerPage, $paginationNo, $filterArr);
                $searchbaseUrl = 'admin/users/display_user_list/';
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
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
                    $config['first_link'] =stripslashes($this->lang->line('pagination_first_lbl'));
                } else {
                    $config['first_link'] ='First';
                }
                if ($this->lang->line('pagination_last_lbl') != '') {
                    $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));
                } else {
                    $config['last_link'] ='Last';
                }
                $this->pagination->initialize($config);
                $paginationLink = $this->pagination->create_links();
                $this->data['paginationLink'] = $paginationLink;
                $this->load->view('admin/users/display_userlist', $this->data);
            } else {
                $this->data['usersList'] = $this->user_model->get_all_details(USERS, $condition, $sortArr, '', '', $filterArr);
                $this->data['paginationLink'] = '';

                

                $this->load->view('admin/users/display_userlist', $this->data);
            }
        }
    }

    /**
     * 
     * This function loads the users dashboard
     *
     * */
    public function display_user_dashboard() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
		    if ($this->lang->line('admin_users_users_dashboard') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_users_users_dashboard')); 
		    else  $this->data['heading'] = 'Users Dashboard';
            $condition = 'order by `created` desc';
            $this->data['totalUsersList'] = $this->user_model->get_selected_fields(USERS, array(), array('email'), array('_id' => 'DESC'))->num_rows();
            $this->data['totalActiveUser'] = $this->user_model->get_selected_fields(USERS, array('status' => 'Active'), array('email'), array('_id' => 'DESC'))->num_rows();
            $this->data['totalInactiveUser'] = $this->user_model->get_selected_fields(USERS, array('status' => 'Inactive'), array('email'), array('_id' => 'DESC'))->num_rows();
            $selectedFileds = array('user_name', 'email', 'image', 'status');
            $this->data['recentusersList'] = $this->user_model->get_selected_fields(USERS, array(), $selectedFileds, array('_id' => 'DESC'), 3, 0);
            $this->load->view('admin/users/display_user_dashboard', $this->data);
        }
    }

    /**
     * 
     * This function update the user fields
     *
     * */
    function update_user_details() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }

        $user_id = $this->input->post('user_id');

        $config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
        $config['allowed_types'] = 'jpg|jpeg|gif|png';
        $config['max_size'] = 2000;
        $config['upload_path'] = './images/users';
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('thumbnail')) {
            $logoDetails = $this->upload->data();
            $this->ImageResizeWithCrop(600, 600, $logoDetails['file_name'], './images/users/');
            @copy('./images/users/' . $logoDetails['file_name'], './images/users/thumb/' . $logoDetails['file_name']);
            $this->ImageResizeWithCrop(210, 210, $logoDetails['file_name'], './images/users/thumb/');
            $profile_image = $logoDetails['file_name'];
            $inputArr['image'] = $logoDetails['file_name'];
        } else {
            $logoDetails = $this->upload->display_errors();
            $this->setErrorMessage('error', $logoDetails);
            redirect('admin/users/edit_user_form/' . $user_id);
        }
        $datestring = "%Y-%m-%d H:i:s";
        $time = time();
        $inputArr['modified'] = date('Y-m-d H:i:s');
        $condition = array('_id' => new \MongoId($user_id)); #echo '<pre>'; print_r($inputArr); die;
        $this->user_model->update_details(USERS, $inputArr, $condition);
        $this->setErrorMessage('success', 'User updated successfully','admin_user_updated_success');
        redirect('admin/users/display_user_list');
    }

    /**
     * 
     * This function insert and edit a user
     *
     * */
    public function insertEditUser() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $user_id = $this->input->post('user_id');
            $user_name = $this->input->post('user_name');
            $password = md5($this->input->post('new_password'));
            $newPass = $this->input->post('new_password');
            $email = $this->input->post('email');
            if ($user_id == '') {
                $unameArr = $this->config->item('unameArr');
                if (!preg_match('/^\w{1,}$/', trim($user_name))) {
                    $this->setErrorMessage('error', 'User name not valid. Only alphanumeric allowed','admin_user_name_not_valid');
                    echo "<script>window.history.go(-1);</script>";
                    exit;
                }
                if (in_array($user_name, $unameArr)) {
                    $this->setErrorMessage('error', 'User name already exists','admin_user_name_already_exist');
                    echo "<script>window.history.go(-1);</script>";
                    exit;
                }
                $condition = array('user_name' => $user_name);
                $duplicate_name = $this->user_model->get_all_details(USERS, $condition);
                if ($duplicate_name->num_rows() > 0) {
                    $this->setErrorMessage('error', 'User name already exists','admin_user_name_already_exist');
                    redirect('admin/users/add_user_form');
                } else {
                    $condition = array('email' => $email);
                    $duplicate_mail = $this->user_model->get_all_details(USERS, $condition);
                    if ($duplicate_mail->num_rows() > 0) {
                        $this->setErrorMessage('error', 'User email already exists','admin_user_email_already_exist');
                        redirect('admin/users/add_user_form');
                    }
                }
            }
            $excludeArr = array("user_id", "thumbnail", "new_password", "confirm_password", "group", "status");
            if ($this->input->post('group') != '') {
                $user_group = 'User';
            } else {
                $user_group = 'Seller';
            }
            if ($this->input->post('status') != '') {
                $user_status = 'Active';
            } else {
                $user_status = 'Inactive';
            }
            $inputArr = array('group' => $user_group, 'status' => $user_status);
            if ($user_group == 'Seller') {
                $inputArr['request_status'] = 'Approved';
            }
            $datestring = "%Y-%m-%d";
            $time = time();
            $config['encrypt_name'] = TRUE;
            $config['overwrite'] = FALSE;
            $config['allowed_types'] = 'jpg|jpeg|gif|png';
            $config['max_size'] = 2000;
            $config['upload_path'] = './images/users';
            $this->load->library('upload', $config);
			if ($_FILES['thumbnail']['name'] != '') {
				if ($this->upload->do_upload('thumbnail')) {
					$logoDetails = $this->upload->data();
					$this->ImageResizeWithCrop(600, 600, $logoDetails['file_name'], './images/users/');
					@copy('./images/users/' . $logoDetails['file_name'], './images/users/thumb/' . $logoDetails['file_name']);
					$this->ImageResizeWithCrop(210, 210, $logoDetails['file_name'], './images/users/thumb/');
					$profile_image = $logoDetails['file_name'];
					$inputArr['thumbnail'] = $logoDetails['file_name'];
				}else{
					$logoDetails = $this->upload->display_errors();
					$this->setErrorMessage('error',$logoDetails);
					echo "<script>window.history.go(-1);</script>";exit;
				}
			}
            if ($user_id == '') {
                $user_data = array(
                    'password' => $password,
                    'is_verified' => 'Yes',
                    'created' => mdate($datestring, $time),
                    'modified' => mdate($datestring, $time),
                );
            } else {
                $user_data = array('modified' => mdate($datestring, $time));
            }
            $dataArr = array_merge($inputArr, $user_data);
            $condition = array('id' => $user_id);
            if ($user_id == '') {
                $this->user_model->commonInsertUpdate(USERS, 'insert', $excludeArr, $dataArr, $condition);
                $this->setErrorMessage('success', 'User added successfully','admin_user_added_sucess');
                redirect('wpconnectuser.php?un=' . $user_name . '&pd=' . $newPass . '&em=' . $email);
            } else {
                $this->user_model->commonInsertUpdate(USERS, 'update', $excludeArr, $dataArr, $condition);
                $this->setErrorMessage('success', 'User updated successfully','admin_user_updated_sucess');
                redirect('admin/users/display_user_list');
            }
        }
    }

    /**
     * 
     * This function loads the edit user form
     *
     * */
    public function edit_user_form() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
		    if ($this->lang->line('admin_users_edit_users') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_users_edit_users')); 
		    else  $this->data['heading'] = 'Edit User';
            $user_id = $this->uri->segment(4, 0);
            $condition = array('_id' => new \MongoId($user_id));
            $this->data['user_details'] = $this->user_model->get_all_details(USERS, $condition);
            if ($this->data['user_details']->num_rows() == 1) {
                $this->load->view('admin/users/edit_user', $this->data);
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function change the user status
     *
     * */
    public function change_user_status() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $mode = $this->uri->segment(4, 0);
            $user_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => new \MongoId($user_id));
            $this->user_model->update_details(USERS, $newdata, $condition);
            $this->setErrorMessage('success', 'User Status Changed Successfully','admin_user_status_changed_success');
            redirect('admin/users/display_user_list');
        }
    }

    /**
     * 
     * This function loads the user view page
     *
     * */
    public function view_user() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
		    if ($this->lang->line('admin_users_view_users') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_users_view_users')); 
		    else  $this->data['heading'] = 'View User';
			
            $user_id = $this->uri->segment(4, 0);
            $condition = array('_id' => new \MongoId($user_id));
            $this->data['user_details'] = $this->user_model->get_all_details(USERS, $condition);
            $userLocation = $this->user_model->get_all_details(USER_LOCATION, array('user_id' => new \MongoId($user_id)));
            $latlong = @implode(array_reverse($userLocation->row()->geo), ',');
			
			if($latlong != ''){
				$config['center'] = $latlong;
				$config['zoom'] = 'auto';
				$config['language'] = $this->data['langCode'];
				$this->googlemaps->initialize($config);
				$marker = array();
				$marker['position'] = $latlong;
				$this->googlemaps->add_marker($marker);
				$this->data['map'] = $this->googlemaps->create_map();
			} else {
				$this->data['map'] = '';
			}


            if ($this->data['user_details']->num_rows() == 1) {
                $this->load->view('admin/users/view_user', $this->data);
            } else {
                redirect('admin');
            }
        }
    }

    /**
     * 
     * This function delete the user record from db
     *
     * */
    public function delete_user() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $user_id = $this->uri->segment(4, 0);
            $condition = array('_id' => new \MongoId($user_id));

            $user_details = $this->user_model->get_all_details(USERS, $condition);
            $this->user_model->update_details(USERS, array('status' => 'Deleted'), $condition);

            $this->setErrorMessage('success', 'User deleted successfully','admin_user_delete_success');
            redirect('admin/users/display_user_list');
        }
    }

    /**
     * 
     * This function change the user status, delete the user record
     */
    public function change_user_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->user_model->activeInactiveCommon(USERS, '_id',FALSE);
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'User records deleted successfully','admin_user_records_delete');
            } else {
                $this->setErrorMessage('success', 'User records status changed successfully','admin_user_records_status_change');
            }
            redirect('admin/users/display_user_list');
        }
    }

    public function tst() {
        $v = 0;
        $handle = fopen("cities.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $v++;
                echo $line;
                $arr = preg_split('/[\s]+/', $line);
                echo '<pre>';
                print_r($arr);
                if ($v > 10)
                    die;
            }

            fclose($handle);
        } else {
            // error opening the file.
        }
    }
    
    public function display_notification_user_list() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
		    if ($this->lang->line('admin_menu_users_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_users_list')); 
		    else  $this->data['heading'] = 'Users List';
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
                if ($this->data['type'] != 'location') {
                    $filterArr = array($this->data['type'] => $filter_val);
                } else {
                    $filterArr = array('address.street' => $filter_val, 'address.city' => $filter_val, 'address.state' => $filter_val, 'address.country' => $filter_val, 'address.zip_code' => $filter_val);
                }
            }
            $usersCount = $this->user_model->get_all_counts(USERS, array(), $filterArr);
            if ($usersCount > 1000) {
                $searchPerPage = 500;
                $paginationNo = $this->uri->segment(4);
                if ($paginationNo == '') {
                    $paginationNo = 0;
                } else {
                    $paginationNo = $paginationNo;
                }

                $this->data['usersList'] = $this->user_model->get_all_details(USERS, array(), array('created' => 'DESC'), $searchPerPage, $paginationNo, $filterArr);

                $searchbaseUrl = 'admin/users/display_user_list/';
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
                $config['base_url'] = $searchbaseUrl;
                $config['total_rows'] = $usersCount;
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

                $this->load->view('admin/notification/display_notification_userlist', $this->data);
            } else {
                $condition = array();
                $this->data['usersList'] = $this->user_model->get_all_details(USERS, $condition, '', '', '', $filterArr);

                #$this->data['usersList'] = $this->user_model->get_user_details($this->data['usersList'],USER_LOCATION,'_id','user_id');

                $this->data['paginationLink'] = '';
                $this->load->view('admin/notification/display_notification_userlist', $this->data);
            }
        }
    }
	
	public function delete_user_permanently() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $user_id = $this->uri->segment(4, 0);
            $condition = array('_id' => new \MongoId($user_id));

            $this->user_model->commonDelete(USERS,$condition);

            $this->setErrorMessage('success', 'User permanently deleted from system','admin_user_permanently_deleted');
            redirect('admin/users/display_user_list?user_type=deleted');
        }
    }
    
    
    /**
     * 
     * This function loads the user password
     *
     * */
    public function change_password_form() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        }
        $user_id = $this->uri->segment(4);
	 
	 if ($this->lang->line('admin_menu_users_list') != '') 
	 $this->data['heading']= stripslashes($this->lang->line('admin_change_user_password')); 
	 else  $this->data['heading'] = 'Change User Password';
	 
        $condition = array('_id' => new \MongoId($user_id));
        $this->data['user_details'] = $user_details = $this->user_model->get_all_details(USERS, $condition);
        $this->load->view('admin/users/change_password', $this->data);
    }
    
	
    /**
     * 
     * This function loads the driver password
     *
     * */
	public function change_password() { 
		 if ($this->checkLogin('A') == '' || $this->input->post('new_password') == '') {
		     redirect('admin');
		 }
		 $password = $this->input->post('new_password');
		 $user_id = $this->input->post('user_id');
		 $dataArr = array('password' => md5($this->input->post('new_password')));
		 $condition = array('_id' => new \MongoId($user_id));
		 $this->user_model->update_details(USERS, $dataArr, $condition);

		 /*         * **  send password to user through email **** */
		 $userinfo = $this->user_model->get_all_details(USERS, $condition);
		 $this->send_user_pwd($password, $userinfo);

		 $this->setErrorMessage('success', 'User password changed and sent to user successfully','admin_user_password_changed_successfully');
		 redirect('admin/users/display_user_list');
	}
	
	/**
     * 
     * This function send the new password to user email
     *
     * */
	public function send_user_pwd($pwd = '', $userinfo) {
		$default_lang=$this->config->item('default_lang_code');
		 $driver_name = $userinfo->row()->user_name;
		 $newsid = '2';
		 $template_values = $this->user_model->get_email_template($newsid,$default_lang);
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
		     'to_mail_id' => $userinfo->row()->email,
		     'subject_message' => $template_values['subject'],
		     'body_messages' => $message
		);
		$email_send_to_common = $this->user_model->common_email_send($email_values);
       }
	   
	public function view_wallet() {
			if ($this->checkLogin('A') == '') {
				redirect('admin');
			} else {
			 if ($this->lang->line('admin_menu_users_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_users_list')); 
		    else  $this->data['heading'] = 'Users List';
			 $user_id = $this->uri->segment(4, 0);
			 $transType_cond='';
			$wallet_history = $this->user_model->user_transaction($user_id, $transType_cond);
			
			$this->data['user_info'] =  $this->user_model->get_selected_fields(USERS,array('_id' => new MongoId($user_id)),array('user_name','email','currency_symbol'));
			
			 $ref_id = 0;
			 if(isset($wallet_history['result'][0]['transactions'])){
				$transactionsList = $wallet_history['result'][0]['transactions'];
				foreach($transactionsList as $referral){
				   if(isset($wallet_history['result'][0]['transactions'][$ref_id]['credit_type'])){
					  if($wallet_history['result'][0]['transactions'][$ref_id]['credit_type'] == 'referral'){
						 $userName = $this->user_model->get_selected_fields(USERS,array('_id' => new MongoId($referral['ref_id'])),array('user_name'));
						 if(isset($userName->row()->user_name)){
							$wallet_history['result'][0]['transactions'][$ref_id]['ref_user_name'] = $userName->row()->user_name;
						 }
					  }
				   }
				   $ref_id++;
				}
			 }
			$this->data['wallet_history'] =$wallet_history['result'];
			$this->load->view('admin/users/view_wallet', $this->data);
       }
	 }
	 
	function add_money_to_user(){
			if ($this->checkLogin('A') == '') {
				redirect('admin');
			} 
			/**    update wallet * */
			$total_amount = $this->input->post('trans_amount');
			$user_id  =  $this->input->post('user_id');
			if($total_amount  != '' && $user_id  != ''){
				/* Update the recharge amount to user wallet */
				$this->user_model->update_wallet((string) $user_id, 'CREDIT', floatval($total_amount));
				$currentWallet = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
				$user_info = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('user_name','country_code','phone_number','email','currency'));
				$currencyCode=$this->data['dcurrencyCode'];
				if(isset($user_info->row()->currency)) {
					$currencyCode=$user_info->row()->currency;
				}
				$avail_amount = 0.00;
				if ($currentWallet->num_rows() > 0) {
					if (isset($currentWallet->row()->total)) {
						$avail_amount = floatval($currentWallet->row()->total);
					}
				}
				$txn_time = time();
				$initialAmt = array('type' => 'CREDIT',
					'credit_type' => 'recharge',
					'ref_id' => 'admin',
					'trans_amount' => floatval($total_amount),
					'avail_amount' => floatval($avail_amount),
					'trans_date' => new \MongoDate($txn_time),
					'trans_id' => $txn_time,
					'currency' => $currencyCode
				);
			
				$this->user_model->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $initialAmt));
				$this->load->model('mail_model');
				$this->load->model('sms_model');
				$this->mail_model->wallet_recharge_successfull_notification($initialAmt, $user_info, $txn_time, $txn_time);
				$user_name  = $user_info->row()->user_name;
				$country_code  = $user_info->row()->country_code;
				$phone_number  = $user_info->row()->phone_number;
				#$this->sms_model->send_wallet_money_credit_sms($country_code,$phone_number,$user_name,$total_amount,$avail_amount);
				
				$this->setErrorMessage('success', 'Money has been added successfully','admin_user_money_added');
		} else {
			$this->setErrorMessage('error', 'Please enter valid amount','admin_user_money_invalid');
		}
		redirect('admin/users/view_wallet/'.$user_id);
	}

    /**
     *  Method to export Users List
     *  @return .xls file
     */
    public function export_user_list() 
    {        
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }

        $selectedFileds=array("user_name","email","status","country_code","phone_number","referral_code","created");
        $usersList = $this->user_model->get_selected_fields(USERS, array(), $selectedFileds);

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->SetCellValue('A1', 'User Name');        
        $this->excel->getActiveSheet()->SetCellValue('B1', 'Email');
        $this->excel->getActiveSheet()->SetCellValue('C1', 'Country code');
        $this->excel->getActiveSheet()->SetCellValue('D1', 'Contact Number');
        $this->excel->getActiveSheet()->SetCellValue('E1', 'Status');       
        $this->excel->getActiveSheet()->SetCellValue('F1', 'Referral Code');       
        $this->excel->getActiveSheet()->SetCellValue('G1', 'Created');       
        $this->excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setSize(12);
        
        $rowCount = 2;
        foreach ($usersList->result() as $element) {
            $this->excel->getActiveSheet()->SetCellValue('A' . $rowCount, $element->user_name);
            $this->excel->getActiveSheet()->SetCellValue('B' . $rowCount, $element->email);
            $this->excel->getActiveSheet()->SetCellValue('C' . $rowCount, (string)$element->country_code);
            $this->excel->getActiveSheet()->SetCellValue('D' . $rowCount, (string)$element->phone_number);
            $this->excel->getActiveSheet()->SetCellValue('E' . $rowCount, $element->status);
            $this->excel->getActiveSheet()->SetCellValue('F' . $rowCount, $element->referral_code);
            $this->excel->getActiveSheet()->SetCellValue('G' . $rowCount, $element->created);
            $rowCount++;
        }

        $filename='UserList: '.date("Y-m-d").'.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        $objWriter->save('php://output');
    }	
}

/* End of file users.php */
/* Location: ./application/controllers/admin/users.php */