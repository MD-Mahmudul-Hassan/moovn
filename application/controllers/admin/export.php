<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This controller contains the functions related to Export management 
* @author Casperon
*
**/

class Export extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','csv','download'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('export_model'));	
		$this->load->dbutil();
		if ($this->checkPrivileges('export',$this->privStatus) == FALSE){
			redirect('admin');
		}
    }
    
    /**
    *
    * This function loads the export page
	*
    **/
   	public function index(){	
		redirect('admin');
	}
	
	/**
	* 
	* This function Exports users List
	*
	**/
	public function userlist(){
		$filterArr=array();
		if((isset($_POST['type']) && isset($_POST['value'])) && ($_POST['type']!='' && $_POST['value']!='')){
			if(isset($_POST['type']) && $_POST['type']!=''){
				$this->data['type']=$_POST['type'];
			}
			if(isset($_POST['value']) && $_POST['value']!=''){
				$this->data['value']=$_POST['value'];
				$filter_val= $this->data['value'];
			}
			if($this->data['type']!='location'){
				$filterArr=array($this->data['type']=>$filter_val);
			}else{
				$filterArr=array('address.street'=>$filter_val,'address.city'=>$filter_val,'address.state'=>$filter_val,'address.country'=>$filter_val,'address.zip_code'=>$filter_val);
			}
		}
		$selectedFileds=array("user_name","email","image","country_code","phone_number","referral_code","created");
		$usersList = $this->export_model->get_selected_fields(USERS,array(),$selectedFileds,'','','',$filterArr);
		
		if($usersList->num_rows()==0){
			$this->setErrorMessage('error','No Data found to export');
			echo "<script>window.history.go(-1)</script>";exit();
		 }else{
			$dataArr=array();
			$fields=array("user_name","email","image","country_code","phone_number","referral_code","created","address1","city","state","country","zip_code");
			$dataArr[]=$fields;
			
			if($usersList->num_rows()>0){
				foreach($usersList->result() as $row){
					if($row->image!=''){
						$image=base_url().USER_PROFILE_IMAGE.$row->image;
					}else{
						$image=base_url().USER_PROFILE_IMAGE_DEFAULT;
					}
					$dataArr[] = array('user_name'=>$row->user_name,
													'email'=>$row->email,
													'image'=>$image,
													'country_code'=>$row->country_code,
													'phone'=>$row->phone_number,
													'referral_code'=>$row->referral_code,
													'created'=>$row->created,
												);
				}
			}
			$fileName='user_list_'.time().'.csv';
			array_to_csv($dataArr,$fileName); 
			die;
		 }
	}
  
}
/* End of file export.php */
/* Location: ./application/controllers/admin/export.php */