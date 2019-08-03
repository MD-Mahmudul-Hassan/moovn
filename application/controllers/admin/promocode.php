<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This controller contains the functions related to promocodes management 
* @author Casperon
*
**/

class Promocode extends MY_Controller { 

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('promocode_model');
		if ($this->checkPrivileges('promocode',$this->privStatus) == FALSE){
			redirect('admin');
		}
    }
    
    /**
    * 
    * This function loads the promocode list page
	*
    **/
   	public function index(){	
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			redirect('admin/promocode/display_promocode');
		}
	}
	
	/**
	* 
	* This function loads the promocode list page
	*
	**/
	public function display_promocode(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_menu_coupon_code_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_coupon_code_list')); 
		    else  $this->data['heading'] = 'Coupon Codes List';
			//$this->data['heading'] = 'Coupon Codes List';
			$condition = array();
			$this->data['promocodeList'] = $this->promocode_model->get_all_details(PROMOCODE,$condition);
			$this->load->view('admin/promocode/display_promocode',$this->data);
		}
	}
	
	/**
	* 
	* This function loads the add new promocode form
	*
	**/
	public function add_promocode_form(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_promocode_add_new_coupon_code') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_promocode_add_new_coupon_code')); 
		    else  $this->data['heading'] = 'Add New Coupon Code';
			//$this->data['heading'] = 'Add New Coupon Code';
			$condition = array('status' => 'Active');
            $this->data['country_details'] = $this->promocode_model->get_all_details(COUNTRY, $condition);
            $this->data['location_details'] = $this->promocode_model->get_all_details(LOCATIONS, $condition);
			$this->data['code'] = $this->get_rand_str(10);
			$pChk = $this->promocode_model->get_selected_fields(PROMOCODE,array('promo_code'=>$this->data['code']),array('promo_code'));
			while($pChk->num_rows()>0){
				$this->data['code'] = $this->get_rand_str(10);
				$pChk = $this->promocode_model->get_selected_fields(PROMOCODE,array('promo_code'=>$this->data['code']),array('promo_code'));
			}
			$this->load->view('admin/promocode/add_promocode',$this->data);
		}
	}
	
	/**
	* 
	* This function insert and edit a promocode
	*
	**/
	public function insertEditPromoCode(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			
			$promo_code = $this->input->post('promo_code');
			$promo_id = $this->input->post('promo_id');
			$location_id = $this->input->post('location_id');
			if($location_id !=''){
				$condition = array('_id' => new \MongoId($location_id));
				$this->data['location_details']= $location_details = $this->promocode_model->get_all_details(LOCATIONS,$condition);
			}
			$location_data = array();
			if ($location_details->num_rows() > 0){
				$location_data['location_name']	=	$location_details->row()->city;
				$location_data['currency_symbol']	= $location_details->row()->currency;
			}
			//echo "<pre>";print_r($_POST);;die;
			if ($promo_id == ''){
				$condition = array('promo_code' => $promo_code);
				$duplicate_code = $this->promocode_model->get_selected_fields(PROMOCODE,$condition,array('promo_code'));  
			}else {
				$condition = array('promo_code' => $promo_code);
				$duplicate_code = $this->promocode_model->check_code_exist($condition,$promo_id); 
			} 
			
			if ($duplicate_code->num_rows() > 0){
				$this->setErrorMessage('error','This coupon already exists','admin_coupon_already_exist');
				redirect('admin/promocode/display_promocode');
			}
			
			$promocode_data = array();
			
			if($this->input->post('price_type')=='on'){
				$price_type='Flat';
			}else{
				$price_type='Percent';
			}
			
			$promocode_data['code_type']	=	$price_type;
			if($this->input->post('status')=='on'){
				$status='Active';
			}else{
				$status='Inactive';
			}
			$promocode_data['status']	=	$status;
			
			
			$excludeArr = array("promo_id","price_type","status");

			$inputArr=array();
			if ($promo_id == ''){
				$inputArr = array('created'	=>date("Y-m-d H:i:s"),'no_of_usage'=>intval(0),'promo_users'=>'');
			}
				$excludeArr[] = 'promo_code';
			$dataArr = array_merge($inputArr,$promocode_data,$location_data);
			if ($promo_id == ''){
				$condition = array();
				$dataArr = array('promo_code' => $promo_code) + $dataArr;
				$this->promocode_model->commonInsertUpdate(PROMOCODE,'insert',$excludeArr,$dataArr,$condition);
				$this->setErrorMessage('success','Coupon Code added successfully','admin_coupon_code_add');
			}else{
			
				$condition = array('_id' => new \MongoId($promo_id));
				$this->promocode_model->commonInsertUpdate(PROMOCODE,'update',$excludeArr,$dataArr,$condition);
				$this->setErrorMessage('success','Coupon Code updated successfully','admin_coupon_code_update');
			}
			redirect('admin/promocode/display_promocode');
		}
	}
	
	/**
	* 
	* This function loads the edit promocode form
	*
	**/
	public function edit_promocode_form(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_promocode_edit_coupon_code') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_promocode_edit_coupon_code')); 
		    else  $this->data['heading'] = 'Edit Coupon Code';
			//$this->data['heading'] = 'Edit Coupon Code';
			$promo_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($promo_id));
			$this->data['promocode_details'] = $this->promocode_model->get_all_details(PROMOCODE,$condition);
			$condition = array('status' => 'Active');
			$this->data['location_details'] = $this->promocode_model->get_all_details(LOCATIONS, $condition);
			if ($this->data['promocode_details']->num_rows() == 1){
				$this->load->view('admin/promocode/edit_promocode',$this->data);
			}else {
				redirect('admin');
			}
		}
	}
	
	/**
	*
	* This function change the promocode status
	*
	**/
	public function change_promocode_status(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$mode = $this->uri->segment(4,0);
			$promo_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => new \MongoId($promo_id));
			$this->promocode_model->update_details(PROMOCODE,$newdata,$condition);
			$this->setErrorMessage('success','Coupon Code Status Changed Successfully','admin_coupon_code_status_change');
			redirect('admin/promocode/display_promocode');
		}
	}
	
	/**
	* 
	* This function delete the promocode from db
	*
	**/
	public function delete_promocode(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$promo_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($promo_id));
			$this->promocode_model->commonDelete(PROMOCODE,$condition);
			$this->setErrorMessage('success','Coupon Code deleted successfully','admin_coupon_code_delete_success');
			redirect('admin/promocode/display_promocode');
		}
	}
	
	/**
	* 
	* This function change the promocode status
	*
	**/
	public function change_promocode_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			$this->promocode_model->activeInactiveCommon(PROMOCODE,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Coupon Code deleted successfully','admin_coupon_code_delete_success');
			}else {
				$this->setErrorMessage('success','Coupon Code status changed successfully','admin_coupon_code_status_change');
			}
			redirect('admin/promocode/display_promocode');
		}
	}
	
	public function ajax_get_country_details(){
		$returnArr['status']=0;
		$country_id = $this->input->post('country_id');
		$condition = array('_id' => new \MongoId($country_id));
        $country = $this->promocode_model->get_all_details(COUNTRY, $condition);
			if($country->num_rows() > 0){
				if ($country->row()->status == 'Active') {
					$returnArr['status'] = 1;
					$returnArr['country_code'] = $country->row()->cca2;
					$returnArr['currency_symbol'] = $country->row()->currency_symbol;
				}					
			}	
        $json_encode_new = json_encode($returnArr);
        echo $json_encode_new; 
	}
}

/* End of file promocode.php */
/* Location: ./application/controllers/admin/promocode.php */