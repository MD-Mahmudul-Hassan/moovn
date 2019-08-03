<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to sub-admin management 
 * @author Teamtweaks
 *
 */

class Subadmin extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('subadmin_model');
		//if (!$this->data['demoserverChk']){
			if ($this->checkPrivileges('subadmin',$this->privStatus) == FALSE){
				redirect('admin');
			}
		//}	
    }
    
	/**
	 * 
	 * This function loads the subadmin users list
	 */
	public function display_sub_admin(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_subadmin_subadmin_user_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_subadmin_subadmin_user_list')); 
		    else  $this->data['heading'] = 'Sub Admin Users List';
			$condition = array();
			$this->data['admin_users'] = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
			$this->load->view('admin/subadmin/display_subadmin',$this->data);
		}
	}
	
	/**
	 * 
	 * This function change the subadmin user status
	 */
	public function change_subadmin_status(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$mode = $this->uri->segment(4,0);
			$adminid = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => new \MongoId($adminid));
			$this->subadmin_model->update_details(SUBADMIN,$newdata,$condition);
			$this->setErrorMessage('success','Sub Admin Status Changed Successfully','admin_subadmin_admin_status_change');
			redirect('admin/subadmin/display_sub_admin');
		}
	}
	
	/**
	 * 
	 * This function loads the add subadmin form 
	 */
	public function add_sub_admin_form(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_subadmin_add_subadmin') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_subadmin_add_subadmin')); 
		    else  $this->data['heading'] = 'Add Sub admin';
			$condition = array();
			$this->load->view('admin/subadmin/add_subadmin',$this->data);
		}
	}
	
	/**
	 * 
	 * This function insert and edit a subadmin and his privileges
	 */
	public function insertEditSubadmin(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
	#	if (!$this->data['demoserverChk']){}
			$subadminid = $this->input->post('subadminid');
			$admin_name = $this->input->post('admin_name');
			$admin_password = md5($this->input->post('admin_password'));
			$email = $this->input->post('email');
			if ($subadminid == ''){
				$condition = array('email' => $email);
				$duplicate_admin= $this->subadmin_model->get_all_details(ADMIN,$condition);
				if ($duplicate_admin->num_rows() > 0){
					$this->setErrorMessage('error','Admin email already exists','admin_adminlogin_admin_email_already_exists');
					redirect('admin/subadmin/add_sub_admin_form');
				}else {
					$duplicate_email = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
					#$duplicate_email_user = $this->subadmin_model->get_all_details(USERS,$condition);
					if ($duplicate_email->num_rows() > 0){
						$this->setErrorMessage('error','Sub Admin email already exists','admin_subadmin_email_already_exist');
						redirect('admin/subadmin/add_sub_admin_form');
						/*
							else if($duplicate_email_user->num_rows() > 0){
								$this->setErrorMessage('error','User email already exists');
								redirect('admin/subadmin/add_sub_admin_form');
							}
						*/
					}else {
						$condition = array('admin_name' => $admin_name);
						$duplicate_adminname = $this->subadmin_model->get_all_details(ADMIN,$condition);
						if ($duplicate_adminname->num_rows() > 0){
							$this->setErrorMessage('error','Admin name already exists','admin_adminlogin_admin_name_already_exist');
							redirect('admin/subadmin/add_sub_admin_form');
						}else {
							$duplicate_name = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
							if ($duplicate_name->num_rows() > 0){
								$this->setErrorMessage('error','Sub Admin name already exists','admin_subadmin_admin_name_already_exist');
								redirect('admin/subadmin/add_sub_admin_form');
							}
							/*else{
								$condition = array('user_name' => $admin_name);
								$duplicate_username = $this->subadmin_model->get_all_details(USERS,$condition);
							if ($duplicate_username->num_rows() > 0){
								$this->setErrorMessage('error','User name already exists');
								redirect('admin/subadmin/add_sub_admin_form');
							}	
							
							}*/
						}
					}
				}
			}
			$excludeArr = array("email","subadminid","admin_name","admin_password");
			$privArr = array();
			foreach ($this->input->post() as $key => $val){
				if (!in_array($key, $excludeArr)){
					$privArr[$key] = $val;
				}
			} 
			#echo '<pre>'; print_r($privArr); die;
			$inputArr = array('privileges' => $privArr);
			$datestring = "%Y-%m-%d";
			$time = time();
			if ($subadminid == ''){
				$admindata = array(
					'admin_id' => time(),
					'admin_name'	=>	$admin_name,
					'admin_password'	=>	$admin_password,
					'email'	=>	$email,
					'created'	=>	mdate($datestring,$time),
					'modified'	=>	mdate($datestring,$time),
					'admin_type'	=>	'sub',
					'is_verified'	=>	'Yes',
					'status'	=>	'Active'
				);
			} else {
				$admindata = array('modified' =>	mdate($datestring,$time));
			}
			$dataArr = array_merge($admindata,$inputArr);
			if($subadminid != ''){
				$condition = array('_id' => new \MongoId($subadminid));
			} else {
				$condition = array();
			} 
			$this->subadmin_model->add_edit_subadmin($dataArr,$condition);
			if ($subadminid == ''){
				$this->setErrorMessage('success','Subadmin added successfully','admin_subadmin_added_successfully');
			}else {
				$this->setErrorMessage('success','Subadmin updated successfully','admin_subadmin_updated_successfully');
			}
			redirect('admin/subadmin/display_sub_admin');
		/* }else {
				$this->setErrorMessage('error','You are in demo mode. Settings cannot be changed');
				redirect('admin/subadmin/display_sub_admin');
		} */
		}
	}
	
	/**
	 * 
	 * This function loads the edit subadmin form
	 */
	public function edit_subadmin_form(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_subadmin_add_subadmin') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_subadmin_add_subadmin')); 
		    else  $this->data['heading'] = 'Edit Subadmin';
			
			$adminid = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($adminid));
			$this->data['admin_details'] = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
			if ($this->data['admin_details']->num_rows() == 1){
				$privileges = $this->data['admin_details']->row()->privileges; 
				if(is_array($privileges)){
					$this->data['privArr'] = $privileges;
				} else {
					$this->data['privArr'] = @unserialize($this->data['admin_details']->row()->privileges);
				}
				if (!is_array($this->data['privArr'])){
					$this->data['privArr'] = array();
				}
				$this->load->view('admin/subadmin/edit_subadmin',$this->data);
			}else {
				redirect('admin');
			}
		}
	}
	
	/**
	 * 
	 * This function loads the subadmin view page
	 */
	public function view_subadmin(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_subadmin_view_subadmin') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_subadmin_view_subadmin')); 
		    else  $this->data['heading'] = 'View Subadmin';
			
			$adminid = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($adminid));
			$this->data['admin_details'] = $this->subadmin_model->get_all_details(SUBADMIN,$condition);
			if ($this->data['admin_details']->num_rows() == 1){
				$privileges = $this->data['admin_details']->row()->privileges; 
				if(is_array($privileges)){
					$this->data['privArr'] = $privileges;
				} else {
					$this->data['privArr'] = @unserialize($this->data['admin_details']->row()->privileges);
				}
				if (!is_array($this->data['privArr'])){
					$this->data['privArr'] = array();
				}
				$this->load->view('admin/subadmin/view_subadmin',$this->data);
			}else {
				redirect('admin');
			}
		}
	}
	
	/**
	 * 
	 * This function delete the subadmin record from db
	 */
	public function delete_subadmin(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$subadmin_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($subadmin_id));
			$this->subadmin_model->commonDelete(SUBADMIN,$condition);
			$this->setErrorMessage('success','Subadmin deleted successfully','admin_subadmin_deleted_successfully');
			redirect('admin/subadmin/display_sub_admin');
		}
	}
	
	/**
	 * 
	 * This function change the subadmin status, delete the subadmin record
	 */
	public function change_subadmin_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			$this->subadmin_model->activeInactiveCommon(SUBADMIN,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Subadmin records deleted successfully','admin_subadmin_records_deleted_success');
			}else {
				$this->setErrorMessage('success','Subadmin records status changed successfully','admin_subadmin_records_status_changed');
			}
			redirect('admin/subadmin/display_sub_admin');
		}
	}
}

/* End of file subadmin.php */
/* Location: ./application/controllers/admin/subadmin.php */
?>