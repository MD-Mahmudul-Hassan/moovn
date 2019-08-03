<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* 
* This controller contains the functions related to currency management 
* @author Casperon
*
**/

class Currency extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('currency_model');
		if ($this->checkPrivileges('currency',$this->privStatus) == FALSE){
			redirect('admin');
		}
    }
    
    /**
    * 
    * This function loads the currency list page
	*
    **/
   	public function index(){	
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			redirect('admin/currency/display_currency_list');
		}
	}
	
	/**
	* 
	* This function loads the currency list page
	*
	**/
	public function display_currency_list(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$this->data['heading'] = 'Currency List';
			$condition = array();
			$sortArr = array('name'=>'ASC');
			$this->data['currencyList'] = $this->currency_model->get_all_details(CURRENCY,$condition);
			$this->load->view('admin/currency/display_currency',$this->data);
		}
	}
	
	/**
	* 
	* This function loads the add new currency form
	*
	**/
	public function add_edit_currency(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$currency_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			$heading='Add New Location';
			if($currency_id!=''){
				$condition = array('_id' => new \MongoId($currency_id));
				$this->data['currencydetails'] = $this->currency_model->get_all_details(CURRENCY,$condition);
				if ($this->data['currencydetails']->num_rows() != 1){
					redirect('admin/currency/display_currency_list');
				}
				$form_mode=TRUE;
				$heading='Edit Location';
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = $heading;
			$this->load->view('admin/currency/add_edit_currency',$this->data);
		}
	}
	
	/**
	* 
	* This function and/edit a currency informations
	*
	**/
	public function insertEditCurrency(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$currency_id = $this->input->post('currency_id');
			$currency_code = $this->input->post('code');
			$name =trim($this->input->post('name'));
			if($currency_id==''){
				$condition=array('name'=>$name,'code'=>$currency_code);
			}else{
				$condition=array('name'=>$name,'code'=>$currency_code);
			}
			$primary_condition=array();
			$currencyList = $this->currency_model->chk_currency_exist(CURRENCY,$condition,$primary_condition);
			$duplicateCurrency=array();
			if($currencyList->num_rows()>0){
				foreach($currencyList->result() as $cnty){
					$duplicateCurrency[]=(string)$cnty->_id;
				}
			}
			$isDuplicate=FALSE;
			if(!empty($duplicateCurrency)){
				if(($key = array_search($currency_id, $duplicateCurrency)) !== false) {
					unset($duplicateCurrency[$key]);
				}
				if(!empty($duplicateCurrency)){
					$isDuplicate=TRUE;
				}
			}
			if($isDuplicate){
				$this->setErrorMessage('error','Currency code or name is already exist, Please try again');
				redirect('admin/currency/add_edit_currency/'.$currency_id);
			}
			$excludeArr = array("currency_id","status");
			
			if ($this->input->post('status') == 'on'){
				$currency_status = 'Active';
			}else{
				$currency_status = 'Inactive';
			}
			$currency_data = array('status' => $currency_status);
			$condition = array();
			if ($currency_id == ''){
				$this->currency_model->commonInsertUpdate(CURRENCY,'insert',$excludeArr,$currency_data,$condition);
				$this->setErrorMessage('success','Currency added successfully');
			}else {
				$condition = array('_id' => new \MongoId($currency_id));
				$this->currency_model->commonInsertUpdate(CURRENCY,'update',$excludeArr,$currency_data,$condition);
				$this->setErrorMessage('success','Currency updated successfully');
			}
			redirect('admin/currency/display_currency_list');
		}
	}
	
	/**
	* 
	* This function loads the currency view page
	*
	**/
	public function view_currency(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$this->data['heading'] = 'View Currency';
			$currency_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($currency_id));
			$this->data['currency_details'] = $currency_details=$this->currency_model->get_all_details(CURRENCY,$condition);
			if ($this->data['currency_details']->num_rows() == 1){
				$this->load->view('admin/currency/view_currency',$this->data);
			}else {
				redirect('admin/currency/display_currency_list');
			}
		}
	}
	
	/**
	* 
	* This function change the currency status
	*
	**/
	public function change_currency_status(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$mode = $this->uri->segment(4,0);
			$currency_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => new \MongoId($currency_id));
			$this->currency_model->update_details(CURRENCY,$newdata,$condition);
			$this->setErrorMessage('success','Currency Status Changed Successfully');
			redirect('admin/currency/display_currency_list');
		}
	}
	
	/**
	*
	* This function delete the currency record from db
	*
	**/
	public function delete_currency(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$this->setErrorMessage('error','This service is not available');
			redirect('admin/currency/display_currency_list');			
			/* $currency_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($currency_id));
			$this->currency_model->commonDelete(CURRENCY,$condition);
			$this->setErrorMessage('success','Currency deleted successfully');
			redirect('admin/currency/display_currency_list'); */
		}
	}
	
		
	/**
	* 
	* This function change the currency status, delete the currency record
	*
	**/
	public function change_currency_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('error','This service is not available');
				redirect('admin/currency/display_currency_list');		
			}
			$this->user_model->activeInactiveCommon(CURRENCY,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Currency records deleted successfully');
			}else {
				$this->setErrorMessage('success','Currency records status changed successfully');
			}
			redirect('admin/currency/display_currency_list');
		}
	}
}

/* End of file currency.php */
/* Location: ./application/controllers/admin/currency.php */