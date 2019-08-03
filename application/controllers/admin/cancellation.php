<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* 
* This controller contains the functions related to Cancellation types management 
* @author Casperon
*
**/

class Cancellation extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));
		$this->load->model('cancellation_model');
		if ($this->checkPrivileges('cancellation',$this->privStatus) == FALSE){
			redirect('admin');
		}
	}

	
	/**
	*
	* This function Displays the user cancellation types
	*
	**/
	public function user_cancellation_types(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$this->data['reason_for'] = 'user';
		    if ($this->lang->line('admin_menu_user_cancellation_reasons') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_user_cancellation_reasons')); 
		    else  $this->data['heading'] = 'User Cancellation Reasons';
			//$this->data['heading'] = 'User Cancellation Reasons';
			$condition = array('type'=>'user');
			$this->data['cancellationTypes'] = $this->cancellation_model->get_all_details(CANCELLATION_REASON,$condition);
			$this->data['type'] = 'user';
			$this->load->view('admin/cancellation/display_cancellation_types',$this->data);
		}
	}
	/**
	*
	* This function Displays the driver cancellation types
	*
	**/
	public function driver_cancellation_types(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$this->data['reason_for'] = 'driver';
		    if ($this->lang->line('admin_menu_driver_cancellation_reasons') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_driver_cancellation_reasons')); 
		    else  $this->data['heading'] = 'Driver Cancellation Reasons';
			//$this->data['heading'] = 'Driver Cancellation Reasons';
			$condition = array('type'=>'driver');
			$this->data['cancellationTypes'] = $this->cancellation_model->get_all_details(CANCELLATION_REASON,$condition);
			$this->data['type'] = 'driver';
			$this->load->view('admin/cancellation/display_cancellation_types',$this->data);
		}
	}

	/**
	*
	* This function loads the add/Edit cancellation type form
	*
	**/
	public function add_edit_cancellation_type(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$type = $this->uri->segment(4,0);
			$reason_type='';
			if($type=='driver'){
				$rdir='driver_cancellation_types';
				$reason_type='driver';
			}else{
				$rdir='user_cancellation_types';
				$reason_type='user';
			}
			$cancellation_id = $this->uri->segment(5,0);
			$form_mode=FALSE;
			if ($this->lang->line('admin_cancellation_add_new_reason') != '') 
		    $heading= stripslashes($this->lang->line('admin_cancellation_add_new_reason')); 
		    else  $heading='Add New Reason';
			//$heading='Add New Reason';
			if($cancellation_id!=''){
				$condition = array('_id' => new \MongoId($cancellation_id));
				$this->data['cancellationdetails'] = $this->cancellation_model->get_all_details(CANCELLATION_REASON,$condition);
				if ($this->data['cancellationdetails']->num_rows() != 1){
					redirect('admin/cancellation/'.$rdir);
				}
				$form_mode=TRUE;
				if ($this->lang->line('admin_cancellation_edit_cancellation_reason') != '') 
		        $heading= stripslashes($this->lang->line('admin_cancellation_edit_cancellation_reason')); 
		        else  $heading='Edit Cancellation Reason';
				//$heading='Edit Cancellation Reason';
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['type'] = (string)$reason_type;
			$this->data['heading'] = $heading;
			$this->load->view('admin/cancellation/add_edit_cancellation_type',$this->data);
		}
	}
	
	/**
	*
	* This function insert/update cancellation reason into databse
	*
	**/
	public function insertEditReason(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$reason_id = $this->input->post('reason_id');
			$reason = $this->input->post('reason');
			$type = $this->input->post('type');
			
			if($type=='driver'){
				$rdir='driver_cancellation_types';
			}else{
				$rdir='user_cancellation_types';
			}
			
			if ($this->input->post('status') != ''){
				$status = 'Active';
			}else {
				$status = 'Inactive';
			}			
				
			$dataArr = array('reason' => $reason,
											'type' => $type,
											'status' => $status,
											'created' => new \MongoDate(time())
										);				
			
			if($reason_id==''){
				$this->cancellation_model->simple_insert(CANCELLATION_REASON,$dataArr);
				$this->setErrorMessage('success','Reason added successfully','admin_coupon_code_reason_add');
			}else{
				$condition=array('_id'=>new \MongoId($reason_id));
				$this->cancellation_model->update_details(CANCELLATION_REASON,$dataArr,$condition);
				$this->setErrorMessage('success','Reason updated successfully','admin_cancellation_code_reason_update');
			}
			redirect('admin/cancellation/'.$rdir);
		}
	}
	
	/**
	*
	* This function delete the cancellation reason record from db
	*
	**/
	public function delete_reason(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$reason_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($reason_id));
			$this->cancellation_model->commonDelete(CANCELLATION_REASON,$condition);
			$this->setErrorMessage('success','Reason deleted successfully','admin_cancellation_code_reason_delete');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	*
	* This function change the status of the cancellation reason
	*
	**/
	public function change_reason_status(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$mode = $this->uri->segment(4,0);
			$reason_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => new \MongoId($reason_id));
			$this->cancellation_model->update_details(CANCELLATION_REASON,$newdata,$condition);
			$this->setErrorMessage('success','Reason Status Changed Successfully','admin_cancellation_status_change');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	*
	* This function change the status of the cancellation reason globally
	*
	**/
	public function change_cancellation_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			$this->cancellation_model->activeInactiveCommon(CANCELLATION_REASON,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Reason records deleted successfully','admin_cancellation_record_delete');
			}else {
				$this->setErrorMessage('success','Reason records status changed successfully','admin_cancellation_record_status_change');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	
	public function edit_language_cancellation(){
		if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $cancellation_id = $this->uri->segment(4, 0);
            $this->data['reason_for'] = $this->uri->segment(5, 0);
			if ($cancellation_id != '') {
                $condition = array('_id' => new \MongoId($cancellation_id));
                $this->data['cancellationDetails'] = $this->cancellation_model->get_all_details(CANCELLATION_REASON, $condition);
                $this->data['languagesList'] = $this->cancellation_model->get_all_details(LANGUAGES, array('status' => 'Active'));
				if ($this->data['cancellationDetails']->num_rows() != 1) {
                    redirect('admin/cancellation/'.$cancellation_type);
                }
            }
			
			if ($this->lang->line('admin_cancellation_edit_cancellation_reason') != '') 
			$heading = stripslashes($this->lang->line('admin_cancellation_edit_cancellation_reason')); 
			else  $heading = 'Edit Cancellation Reason';
			 
			 $this->data['heading'] = $heading;
            $this->load->view('admin/cancellation/edit_cancellation_language_form', $this->data);
        }
	}
	
	public function update_language_content(){
		if ($this->checkLogin('A') == '') {
            redirect('admin');
        }
		$reason_for = $this->input->post('reason_for');
		$language_content = $this->input->post('name_languages');  
		$cancellation_id = $this->input->post('cancellation_id');
		$updCond = array('_id' => new MongoId($cancellation_id ));
		$dataArr = array('name_languages' => $language_content);  #echo '<pre>'; print_r($dataArr ); die;
		$this->cancellation_model->update_details(CANCELLATION_REASON,$dataArr ,$updCond);
		$this->setErrorMessage('success', 'Language content updated successfully','language_content_updated_successfully');
		if($reason_for == 'user'){
			redirect('admin/cancellation/user_cancellation_types');
		}else if($reason_for == 'driver'){
			redirect('admin/cancellation/driver_cancellation_types');
		}
        
	}
	
	
}

/* End of file cancellation.php */
/* Location: ./application/controllers/admin/cancellation.php */