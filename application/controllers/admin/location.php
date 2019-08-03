<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* 
* This controller contains the functions related to vechicle management 
* @author Casperon
*
**/

class Location extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('location_model');
		if ($this->checkPrivileges('location',$this->privStatus) == FALSE){
			redirect('admin');
		}
		
		$c_fun = $this->uri->segment(3);
		$restricted_function = array('delete_location','change_location_status_global');
		if(in_array($c_fun,$restricted_function) && $this->data['isDemo']===TRUE){
			$this->setErrorMessage('error','You are in demo version. you can\'t do this action.','admin_template_demo_version');
			redirect($_SERVER['HTTP_REFERER']); die;
		}
    }
    
    /**
    * 
    * This function loads the location list page
	*
    **/
   	public function index(){	
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			redirect('admin/location/display_location_list');
		}
	}
	
	/**
	* 
	* This function loads the location list page
	*
	**/
	public function display_location_list(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_menu_location_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_location_list')); 
		    else  $this->data['heading'] = 'Location List';
			$condition = array();
			$sortArr = array('city'=>'ASC');
			$this->data['locationList'] = $this->location_model->get_all_details(LOCATIONS,$condition);
			$this->load->view('admin/location/display_location',$this->data);
		}
	}
	
	/**
	* 
	* This function loads the add new location form
	*
	**/
	public function add_edit_location() {
		if ($this->checkLogin('A') == '') {
			redirect('admin');
		} else {
			$location_id = $this->uri->segment(4, 0);
			$form_mode = FALSE;
			if ($this->lang->line('admin_location_and_fare_add_new_location') != '') {
				$heading = stripslashes($this->lang->line('admin_location_and_fare_add_new_location'));
			} else {
				$heading = 'Add New Location';
			}
			if ($location_id !='') {
				$condition = array('_id' => new \MongoId($location_id));
				$this->data['locationdetails'] = $this->location_model->get_all_details(LOCATIONS,$condition);
				if ($this->data['locationdetails']->num_rows() != 1) {
					redirect('admin/location/display_location_list');
				}
				$form_mode = TRUE;
				
				if ($this->lang->line('admin_location_edit_location') != '') {
					$heading = stripslashes($this->lang->line('admin_location_edit_location')); 
				} else {
					$heading = 'Edit Location';
				}
			}
			$this->data['categoryList'] = $this->location_model->get_all_details(CATEGORY, array('status' => 'Active'),array('name'=>'ASC'));
			$this->data['form_mode'] = $form_mode;
			$this->data['currencyList'] = $this->location_model->get_all_details(CURRENCY, array('status' => 'Active'));
			$this->data['heading'] = $heading;
			$this->load->view('admin/location/add_edit_location', $this->data);
		}
	}
	
	/**
	* 
	* This function loads the copy new location form
	*
	**/
	public function copy_location(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$location_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			if ($this->lang->line('admin_location_and_fare_add_new_location') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_location_and_fare_add_new_location')); 
		    else  $this->data['heading'] = 'Add New Location';
			if($location_id!=''){
				$condition = array('_id' => new \MongoId($location_id));
				$this->data['locationdetails'] = $this->location_model->get_all_details(LOCATIONS,$condition);
				if ($this->data['locationdetails']->num_rows() != 1){
					redirect('admin/location/display_location_list');
				}
				$form_mode=TRUE;
				if ($this->lang->line('admin_location_edit_location') != '') {
					$heading= stripslashes($this->lang->line('admin_location_edit_location')); 
				}else{
					$heading = 'Edit Location';
				}
			}
			$this->data['categoryList'] = $this->location_model->get_all_details(CATEGORY,array('status' => 'Active'),array('name'=>'ASC'));
			$this->data['form_mode'] = $form_mode;
			
			$this->data['heading'] = $heading;
			$this->load->view('admin/location/copy_location',$this->data);
		}
	}
	
	/**
	* 
	* This function insert and edit a location
	*
	**/
	public function insertEditLocation(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {            
			$location_id = $this->input->post('location_id');
			$city = $this->input->post('city');
			$category = @explode(",",$this->input->post('available_category'));
			/*Get latitude and longitude for an address*/
			$address = str_replace(" ", "+", $city);
			$json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$this->data['google_maps_api_key']);
			$jsonArr = json_decode($json);
			$newAddress = $jsonArr->{'results'}[0]->{'address_components'};
			foreach($newAddress as $nA){
				if($nA->{'types'}[0] == 'route')$addressArr['street'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'sublocality_level_2')$addressArr['street1'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'sublocality_level_1')$addressArr['area'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'locality')$addressArr['location'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'locality')$addressArr['city'] = $nA->{'long_name'};
				if($addressArr['city'] == ''){
					if($nA->{'types'}[0] == 'administrative_area_level_2')$addressArr['city'] = $nA->{'long_name'};
				}
				
				if($addressArr['city'] == '' && in_array('sublocality_level_1',$nA->{'types'})){
					$addressArr['city'] = $nA->{'long_name'};
				}
				
				if($addressArr['city'] == ''){
					if($nA->{'types'}[0] == 'colloquial_area')$addressArr['city'] = $nA->{'long_name'};
				}
				if($nA->{'types'}[0] == 'administrative_area_level_1')$addressArr['state'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'country')$addressArr['country'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'country')$addressArr['country_code'] = $nA->{'short_name'};
				if($nA->{'types'}[0] == 'postal_code')$addressArr['zip'] = $nA->{'long_name'};
			}
			
			if(!array_key_exists('city',$addressArr)){
				if($addressArr['state']!=""){
					$addressArr['city'] = $addressArr['state'];
				}else if($addressArr['country']!=""){
					$addressArr['city'] = $addressArr['country'];
				}
			}
            
            if ($location_id != ''){
                $condition = array('_id' => new \MongoId($location_id));
                $city_check = $this->location_model->get_all_details(LOCATIONS,$condition);
                $location_lng=$city_check->row()->location['lng'];
                $location_lat=$city_check->row()->location['lat'];
                $location_city=$city_check->row()->city;
                if($location_city!=$addressArr['city']) {
                  $field = array('loc');
                  $this->cimongo->where($condition)->unset_field($field)->update(LOCATIONS);
                }
                
            }
			$condition = array('cca2' => (string)$addressArr['country_code']);
			$countryList = $this->user_model->get_all_details(COUNTRY,$condition);
			if($countryList->num_rows()>0){				
				$country_name=$addressArr['country'];
				$country_code=$addressArr['country_code'];
				$country_currency=$countryList->row()->currency_code;
				$country_id=(string)$countryList->row()->_id;
			}else{
				$this->setErrorMessage('error','Unknown country, Please try again','admin_location_unknown_country');
				redirect('admin/location/add_edit_location/'.$location_id);
			}
			
			/** Multi currency changes **/
			$curCond = array('code' => $country_currency,'status' => 'Active');
			$currencyList = $this->location_model->get_all_details(CURRENCY,$curCond);
			if($currencyList->num_rows()>0){	
				$country_currency=$currencyList->row()->code;
				$country_currency_symbol=$currencyList->row()->symbol;
				if($country_currency_symbol == '') $country_currency_symbol = $country_currency;
			} else {
				$this->setErrorMessage('error','Currency records not found..');
				redirect('admin/location/add_edit_location/'.$location_id);
			}
			
			/** Multi currency changes **/
			$lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			$lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
			$northeast_lat = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lat'};
			$northeast_lng = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lng'};
			$southwest_lat = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lat'};
			$southwest_lng = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lng'};
			/*Get latitude and longitude for an address*/
			$location=array('lng'=>floatval($lang),'lat'=>floatval($lat));
			$bounds=array('southwest'=>array('lng'=>floatval($southwest_lng),'lat'=>floatval($southwest_lat)),'northeast'=>array('lng'=>floatval($northeast_lng),'lat'=>floatval($northeast_lat)));
					
			if ($location_id == ''){
				$condition = array('location' => $location);
			} else {
				$condition = array('_id' => array('$ne' => new MongoId($location_id)),'location' => $location);
			} 
			$duplicate_name = $this->location_model->get_all_details(LOCATIONS,$condition);
			if ($duplicate_name->num_rows() > 0){
				$this->setErrorMessage('error','Location already exists','admin_location_already_exist');
				redirect('admin/location/add_edit_location/'.$location_id);
			}
			$excludeArr = array("location_id","status","country","city","peak_time","night_charge","category","available_category");
			
			if ($this->input->post('status') == 'on'){
				$location_status = 'Active';
			}else{
				$location_status = 'Inactive';
			}
			if ($this->input->post('peak_time') == 'on'){
				$peak_time_status = 'Yes';
			}else{
				$peak_time_status = 'No';
			}
			if ($this->input->post('night_charge') == 'on'){
				$night_charge_status = 'Yes';
			}else{
				$night_charge_status = 'No';
			}
			$avail_category=$category;
			$distance_unit_short = $this->data['d_distance_unit']; 
			$distance_unit_name = $this->data['d_distance_unit_name'];
			if($this->input->post('distance_unit') != ''){
				$distance_unit = @explode('-',$this->input->post('distance_unit'));
				$distance_unit_short = $distance_unit[0];
				$distance_unit_name = $distance_unit[1];
			}
			$country=array('id'=>new \MongoId($country_id),'name'=>$country_name,'code'=>$country_code);
			$location_data = array('country' => $country,
													'city' => $addressArr['city'],
													'location' => $location,
													'distance_unit' => $distance_unit_short,
													'distance_unit_name' => $distance_unit_name,
													'bounds' => $bounds,
													'currency' => $country_currency,
													'currency_symbol' => $country_currency_symbol,
													'avail_category' => $avail_category,
													'peak_time' => $peak_time_status,
													'night_charge' => $night_charge_status,
													'status' => $location_status
												);
												
			#echo "<pre>"; print_r($location_data); die;
			$condition = array();
			if ($location_id == ''){
				$this->location_model->commonInsertUpdate(LOCATIONS,'insert',$excludeArr,$location_data,$condition);
				$location_id = $this->cimongo->insert_id();
				$this->setErrorMessage('success','Location added successfully','admin_location_added_success');
			}else {
				$condition = array('_id' => new \MongoId($location_id));
				$this->location_model->commonInsertUpdate(LOCATIONS,'update',$excludeArr,$location_data,$condition);
				$this->setErrorMessage('success','Location updated successfully','admin_location_updated_success');
			}
			
			#redirect('admin/location/display_location_list');
			redirect('admin/location/update_location_geo_points/'.$location_id);
		}
	}
	
	/**
	* 
	* This function insert copying location
	*
	**/
	public function insertCopyLocation(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$location_id = $this->input->post('location_id');			
			$copy_location_id = $this->input->post('copy_location_id');			
			$city = $this->input->post('city');
			$category = $this->input->post('category');
			
			/*Get latitude and longitude for an address*/
			$address = str_replace(" ", "+", $city);
			$json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$this->data['google_maps_api_key']);
			$jsonArr = json_decode($json);
			$newAddress = $jsonArr->{'results'}[0]->{'address_components'};
			foreach($newAddress as $nA){
				if($nA->{'types'}[0] == 'route')$addressArr['street'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'sublocality_level_2')$addressArr['street1'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'sublocality_level_1')$addressArr['area'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'locality')$addressArr['location'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'locality')$addressArr['city'] = $nA->{'long_name'};
				if($addressArr['city'] == ''){
					if($nA->{'types'}[0] == 'administrative_area_level_2')$addressArr['city'] = $nA->{'long_name'};
				}
				if($addressArr['city'] == '' && in_array('sublocality_level_1',$nA->{'types'})){
					$addressArr['city'] = $nA->{'long_name'};
				}
				if($addressArr['city'] == ''){
					if($nA->{'types'}[0] == 'colloquial_area')$addressArr['city'] = $nA->{'long_name'};
				}
				if($nA->{'types'}[0] == 'administrative_area_level_1')$addressArr['state'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'country')$addressArr['country'] = $nA->{'long_name'};
				if($nA->{'types'}[0] == 'country')$addressArr['country_code'] = $nA->{'short_name'};
				if($nA->{'types'}[0] == 'postal_code')$addressArr['zip'] = $nA->{'long_name'};
			}
			
			if(!array_key_exists('city',$addressArr)){
				if($addressArr['state']!=""){
					$addressArr['city'] = $addressArr['state'];
				}else if($addressArr['country']!=""){
					$addressArr['city'] = $addressArr['country'];
				}
			}
			$condition = array('cca2' => (string)$addressArr['country_code']);
			$countryList = $this->user_model->get_all_details(COUNTRY,$condition);
			if($countryList->num_rows()>0){				
				$country_name=$addressArr['country'];
				$country_code=$addressArr['country_code'];
				$country_currency=$countryList->row()->currency_code;
				$country_id=(string)$countryList->row()->_id;
			}else{
				$this->setErrorMessage('error','Unknown country, Please try again','admin_location_unknown_country');
				redirect('admin/location/add_edit_location/'.$location_id);
			}
			
			/** Multi currency changes **/
			$curCond = array('code' => $country_currency,'status' => 'Active');
			$currencyList = $this->location_model->get_all_details(CURRENCY,$curCond);
			if($currencyList->num_rows()>0){	
				$country_currency=$currencyList->row()->code;
				$country_currency_symbol=$currencyList->row()->symbol;
				if($country_currency_symbol == '') $country_currency_symbol = $country_currency;
			} else {
				$this->setErrorMessage('error','Currency records not found..');
				redirect('admin/location/add_edit_location/'.$location_id);
			}
			
			$lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			$lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
			$northeast_lat = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lat'};
			$northeast_lng = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'northeast'}->{'lng'};
			$southwest_lat = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lat'};
			$southwest_lng = $jsonArr->{'results'}[0]->{'geometry'}->{'bounds'}->{'southwest'}->{'lng'};
			/*Get latitude and longitude for an address*/
			$location=array('lng'=>floatval($lang),'lat'=>floatval($lat));
			$bounds=array('southwest'=>array('lng'=>floatval($southwest_lng),'lat'=>floatval($southwest_lat)),'northeast'=>array('lng'=>floatval($northeast_lng),'lat'=>floatval($northeast_lat)));
					
			if ($location_id == ''){
				$condition = array('location' => $location);
				$duplicate_name = $this->location_model->get_all_details(LOCATIONS,$condition);
				if ($duplicate_name->num_rows() > 0){
					$this->setErrorMessage('error','Location already exists','admin_location_already_exist');
					redirect('admin/location/add_edit_location/'.$location_id);
				}
			}
			$excludeArr = array("location_id","status","country","city","peak_time","night_charge","category");
			
			if ($this->input->post('status') == 'on'){
				$location_status = 'Active';
			}else{
				$location_status = 'Inactive';
			}
			if ($this->input->post('peak_time') == 'on'){
				$peak_time_status = 'Yes';
			}else{
				$peak_time_status = 'No';
			}
			if ($this->input->post('night_charge') == 'on'){
				$night_charge_status = 'Yes';
			}else{
				$night_charge_status = 'No';
			}
			$avail_category=$category;
			$fare = array();
			$source_location = $this->location_model->get_all_details(LOCATIONS,array('_id'=>new \MongoId($copy_location_id)));
			if($source_location->num_rows()>0){
				$source_fare = $source_location->row()->fare;
				foreach($avail_category as $key){
					if(array_key_exists($key,$source_fare)){
						$fare[$key] = $source_fare[$key];
					}else{
						$fare[$key] = array();
					}
				}
			}
			$distance_unit_short = $this->data['d_distance_unit']; 
			$distance_unit_name = $this->data['d_distance_unit_name'];
			if($this->input->post('distance_unit') != ''){
				$distance_unit = @explode('-',$this->input->post('distance_unit'));
				$distance_unit_short = $distance_unit[0];
				$distance_unit_name = $distance_unit[1];
			}
			$country=array('id'=>new \MongoId($country_id),'name'=>$country_name,'code'=>$country_code);
			$location_data = array('country' => $country,
													'city' => $addressArr['city'],
													'distance_unit' => $distance_unit_short,
													'distance_unit_name' => $distance_unit_name,
													'location' => $location,
													'bounds' => $bounds,
													'currency' => $country_currency,
													'avail_category' => $avail_category,
													'peak_time' => $peak_time_status,
													'night_charge' => $night_charge_status,
													'status' => $location_status,
													'fare'=>$fare
												);
			#echo "<pre>"; print_r($location_data); die;
			$condition = array();
			if ($location_id == ''){
				$this->location_model->commonInsertUpdate(LOCATIONS,'insert',$excludeArr,$location_data,$condition);
				$location_id = $this->cimongo->insert_id();
				$this->setErrorMessage('success','Location added successfully','admin_location_added_success');
			}else {
				$condition = array('_id' => new \MongoId($location_id));
				$this->location_model->commonInsertUpdate(LOCATIONS,'update',$excludeArr,$location_data,$condition);
				$this->setErrorMessage('success','Location updated successfully','admin_location_updated_success');
			}
			#redirect('admin/location/display_location_list');
			redirect('admin/location/update_location_geo_points/'.$location_id);
		}
	}
	
	/**
	* 
	* This function loads the location view page
	*
	**/
	public function view_location(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_location_and_fare_view_location') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_location_and_fare_view_location')); 
		    else  $this->data['heading'] = 'View Location';
			$location_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($location_id));
			$this->data['location_details'] = $location_details=$this->location_model->get_all_details(LOCATIONS,$condition);
			if ($this->data['location_details']->num_rows() == 1){
				$avail_category=$location_details->row()->avail_category;
				if(!is_array($avail_category)){
					$avail_category=array();
				}
				$availableCategory =  $this->location_model->get_available_services(CATEGORY,'_id',$avail_category);
				$categoryArr=array();
				if($availableCategory->num_rows()>0){
					foreach($availableCategory->result() as $category){
						$categoryArr[(string)$category->_id]=$category->name;
					}
				}
				$this->data['availableCategory']=$categoryArr;
				#echo '<pre>'; print_r($this->data['availableCategory']); die;
				$this->load->view('admin/location/view_location',$this->data);
			}else {
				redirect('admin');
			}
		}
	}
	
	/**
	* 
	* This function change the location status
	*
	**/
	public function change_location_status(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$mode = $this->uri->segment(4,0);
			$location_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => new \MongoId($location_id));
			$this->location_model->update_details(LOCATIONS,$newdata,$condition);
			$this->setErrorMessage('success','Location Status Changed Successfully','admin_location_status_change');
			redirect('admin/location/display_location_list');
		}
	}
	
	/**
	*
	* This function delete the location record from db
	*
	**/
	public function delete_location(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$location_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($location_id));
			$this->location_model->commonDelete(LOCATIONS,$condition);
			$this->setErrorMessage('success','Location deleted successfully','admin_location_deleted_success');
			redirect('admin/location/display_location_list');
		}
	}
	
		
	/**
	* 
	* This function change the location status, delete the location record
	*
	**/
	public function change_location_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			$this->user_model->activeInactiveCommon(LOCATIONS,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Locations deleted successfully','admin_location_deleted_success');
			}else {
				$this->setErrorMessage('success','Locations status changed successfully','admin_location_status_change');
			}
			redirect('admin/location/display_location_list');
		}
	}
	
	/**
	* 
	* This function loads the add new location form
	*
	**/
	public function location_fare(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$location_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			$categoryArr=array();
			if($location_id!=''){
				$condition = array('_id' => new \MongoId($location_id));
				$this->data['locationdetails'] = $this->location_model->get_all_details(LOCATIONS,$condition);
				$this->data['categorydetails'] = $this->location_model->get_all_details(CATEGORY,array());
				if ($this->data['locationdetails']->num_rows() != 1){
					redirect('admin/location/display_location_list');
				}
				$form_mode=TRUE;				
				if(isset($this->data['locationdetails']->row()->avail_category)){
					$categoryArr=$this->data['locationdetails']->row()->avail_category;
				}else{
					$categoryArr='';
				}
				if(!is_array($categoryArr))$categoryArr=array();
			}			
			$this->data['availableCategory'] = $categoryArr;
			$this->data['form_mode'] = $form_mode;
			$this->data['locationId'] = $location_id;
            if ($this->lang->line('admin_rides_fare_details') != '') 
		    $title= stripslashes($this->lang->line('admin_rides_fare_details')); 
		    else  $title = 'Fare Details';
			$this->data['heading'] = $this->data['locationdetails']->row()->city.'-'.$title;
			$this->load->view('admin/location/add_edit_fare',$this->data);
		}
	}
	
	/**
	* 
	* This function insert and edit a fare
	*
	**/
	public function insertEditFare(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$fare=$this->input->post();
			unset($fare['apply']);
			unset($fare['location_id']);
			$fareArr=array('fare'=>$fare);
			$location_id=$this->input->post('location_id');
			$condition=array('_id'=>new \MongoId($location_id));
			if($location_id!=''){
				$this->location_model->update_details(LOCATIONS,$fareArr,$condition);
				$this->setErrorMessage('success','Fare System updated successfully','admin_location_fare_system_update');
			}else{
				$this->setErrorMessage('error','Fare System updation failed. Please try again.','admin_location_fare_system_update_failed');
			}
			redirect('admin/location/location_fare/'.$location_id);
		}
	}
	
	/**
	* 
	* This function loads the country list page
	*
	**/
	public function display_country_list(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_location_and_fare_country_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_location_and_fare_country_list')); 
		    else  $this->data['heading'] = 'Country List';
			$condition = array();
			$sortArr = array('name'=>'ASC');
			$this->data['countryList'] = $this->location_model->get_all_details(COUNTRY,$condition);
			$this->load->view('admin/location/display_country',$this->data);
		}
	}
	
	/**
	* 
	* This function loads the Add/Edit new Country form
	*
	**/
	public function add_edit_country(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$country_id = $this->uri->segment(4,0);
			$form_mode=FALSE;
			if ($this->lang->line('admin_location_and_fare_add_new_country') != '') 
		    $heading= stripslashes($this->lang->line('admin_location_and_fare_add_new_country')); 
		    else  $heading = 'Add New Country';
			
			//$heading='Add New Country';
			if($country_id!=''){
				$condition = array('_id' => new \MongoId($country_id));
				$this->data['countrydetails'] = $this->location_model->get_all_details(COUNTRY,$condition);
				if ($this->data['countrydetails']->num_rows() != 1){
					redirect('admin/location/display_country');
				}
				$form_mode=TRUE;
				if ($this->lang->line('admin_location_and_fare_edit_country') != '') 
		        $heading= stripslashes($this->lang->line('admin_location_and_fare_edit_country')); 
		        else  $heading = 'Edit Country';
				//$heading='Edit Country';
			}
			$condition = array('status'=>'Active');
			$sortArr = array('name'=>'ASC');
			$this->data['currencyList'] = $this->location_model->get_all_details(CURRENCY,$condition);
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = $heading;
			$this->load->view('admin/location/add_edit_country',$this->data);
		}
	}
	
	/**
	* 
	* This function and/edit a Country informations
	*
	**/
	public function insertEditCountry(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$country_id = $this->input->post('country_id');
			$cca2 = $this->input->post('cca2');
			$cca3 = $this->input->post('cca3');
			$dial_code = $this->input->post('dial_code');
			$name = $this->input->post('name');
			if($country_id==''){
				$condition=array('name'=>$name,'cca2'=>$cca2,'cca3'=>$cca3,'dial_code'=>$dial_code);
				$primary_condition=array();
			}else{
				$condition=array('name'=>$name,'cca2'=>$cca2,'cca3'=>$cca3,'dial_code'=>$dial_code);
				$primary_condition=array();
			}
			$countryList = $this->location_model->chk_country_exist(COUNTRY,$condition,$primary_condition);
			$duplicateCountry=array();
			if($countryList->num_rows()>0){
				foreach($countryList->result() as $cnty){
					$duplicateCountry[]=(string)$cnty->_id;
				}
			}
			$isDuplicate=FALSE;
			if(!empty($duplicateCountry)){
				if(($key = array_search($country_id, $duplicateCountry)) !== false) {
					unset($duplicateCountry[$key]);
				}
				if(!empty($duplicateCountry)){
					$isDuplicate=TRUE;
				}
			}
			if($isDuplicate){
				$this->setErrorMessage('error','Country informations are already exist, Please try again','admin_location_country_inforamtion_exit');
				redirect('admin/location/add_edit_country/'.$country_id);
			}
			$excludeArr = array("country_id","status","currency","dial_code");
			
			if ($this->input->post('status') == 'on'){
				$currency_status = 'Active';
			}else{
				$currency_status = 'Inactive';
			}
			$currency_data = array('status' => $currency_status,'dial_code' => (string)$dial_code);
			
			$currency_code=$this->input->post('currency');
			$currencyList = $this->location_model->get_all_details(CURRENCY,array('code'=>$currency_code));
			
			if($currencyList->num_rows()>0){
				$currency_data['currency_code']=$currencyList->row()->code;
				$currency_data['currency_symbol']=$currencyList->row()->symbol;
				$currency_data['currency_name']=$currencyList->row()->name;
			}
			
			$condition = array();
			if ($country_id == ''){
				$this->location_model->commonInsertUpdate(COUNTRY,'insert',$excludeArr,$currency_data,$condition);
				$this->setErrorMessage('success','Country added successfully','admin_location_country_added_success');
			}else {
				$condition = array('_id' => new \MongoId($country_id));
				$this->location_model->commonInsertUpdate(COUNTRY,'update',$excludeArr,$currency_data,$condition);
				$this->setErrorMessage('success','Country updated successfully','admin_location_country_updated_success');
			}
			redirect('admin/location/display_country_list');
		}
	}
	
	/**
	* 
	* This function loads the country view page
	*
	**/
	public function view_country(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			if ($this->lang->line('admin_location_view_country') != '') 
			$heading= stripslashes($this->lang->line('admin_location_view_country')); 
			else  $heading = 'View Country';
			$this->data['heading'] = $heading;
			$country_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($country_id));
			$this->data['countrydetails'] = $countrydetails=$this->location_model->get_all_details(COUNTRY,$condition);
			if ($this->data['countrydetails']->num_rows() == 1){
				$this->load->view('admin/location/view_country',$this->data);
			}else {
				redirect('admin');
			}
		}
	}		
	
	/**
	*
	* This function delete the country record from db
	*
	**/
	public function delete_country(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$this->setErrorMessage('error','This service is not available','admin_location_service_not_available');
			redirect('admin/location/display_country_list');
			/* $country_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($country_id));
			$this->location_model->commonDelete(COUNTRY,$condition);
			$this->setErrorMessage('success','Country deleted successfully');
			redirect('admin/location/display_country_list'); */
		}
	}
	
	/**
	* 
	* This function change the country status
	*
	**/
	public function change_country_status(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$mode = $this->uri->segment(4,0);
			$country_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => new \MongoId($country_id));
			$this->location_model->update_details(COUNTRY,$newdata,$condition);
			$this->setErrorMessage('success','Country Status Changed Successfully','admin_location_country_status_change');
			redirect('admin/location/display_country_list');
		}
	}
	
	/**
	* 
	* This function change the country status, delete the country record
	*
	**/
	public function change_country_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('error','This service is not available','admin_location_service_not_available');
				redirect('admin/location/display_country_list');		
			}
			$this->location_model->activeInactiveCommon(COUNTRY,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Country records deleted successfully','admin_location_country_records_deleted');
			}else {
				$this->setErrorMessage('success','Country records status changed successfully','admin_location_country_records_status_change');
			}
			redirect('admin/location/display_country_list');
		}
	}
	
	
	/**
	* 
	* This function loads the Geo places of the location
	*
	**/
	public function update_location_geo_points(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$location_id = $this->uri->segment(4,0);
			$form_mode=TRUE;
			
		    if ($this->lang->line('admin_location_update_bounday_points') != '') 
		    $heading= stripslashes($this->lang->line('admin_location_update_bounday_points')); 
		    else  $heading = 'Update Map Boundary Points';
			
			if($location_id!=''){
				$condition = array('_id' => new \MongoId($location_id));
				$this->data['locationdetails'] = $this->location_model->get_all_details(LOCATIONS,$condition);
				if ($this->data['locationdetails']->num_rows() != 1){
					redirect('admin/location/display_location_list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = $heading;
			
			$this->load->view('admin/location/update_location_geo_points',$this->data);
		}
	}
	
	/**
	* 
	* This function update the location boundary
	*
	**/
	public function updateLocationBoundary(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$location_id = $this->input->post('location_id');			
			$boundayVal = $this->input->post('boundayVal');
			
			$boundayVal = trim($boundayVal,'(');
			$boundayVal = trim($boundayVal,')');
			$bArr = @explode('),(',$boundayVal);
			$bcArr= array();
			foreach($bArr as $points){
				$bcArrTemp = @explode(', ',$points);
				$bcArr[] = array(floatval($bcArrTemp[1]),floatval($bcArrTemp[0]));
			}
			if(!empty($bcArr)){
				$bcArr[] = $bcArr[0];
			}
			
			$boundarydata = array('loc'=>array("type"=>"Polygon",'coordinates'=>array($bcArr)));
			$condition = array('_id' => new \MongoId($location_id));
			$this->location_model->update_details(LOCATIONS,$boundarydata,$condition);
			$this->setErrorMessage('success','Boundary updated Successfully','admin_location_boundary_updated');
			
			#redirect('admin/location/display_location_list');
			redirect('admin/location/location_fare/'.$location_id);
		}
	}


	/**
	 * Display form for adding no show fee
	 */
	public function add_noshow_fee() {
		if ($this->checkLogin('A') == '') {
			redirect('admin');
		} else {
			$location_id = $this->uri->segment(4, 0);
			$form_mode = FALSE;
			if ($this->lang->line('admin_location_and_fare_add_fee') != '') {
				$heading = stripslashes($this->lang->line('admin_location_and_fare_add_fee'));
			} else {
				$heading = 'Add Fee:';
			}
			if ($location_id !='') {
				$condition = array('_id' => new \MongoId($location_id));
				$this->data['locationdetails'] = $this->location_model->get_all_details(LOCATIONS,$condition);
				if ($this->data['locationdetails']->num_rows() != 1) {
					redirect('admin/location/display_location_list');
				}
				$form_mode = TRUE;
				
				if ($this->lang->line('admin_location_edit_location') != '') {
					$heading = stripslashes($this->lang->line('admin_location_edit_location')); 
				} else {
					$heading = 'Edit Location';
				}
			}
			$this->data['categoryList'] = $this->location_model->get_all_details(CATEGORY, array('status' => 'Active'),array('name'=>'ASC'));
			$this->data['form_mode'] = $form_mode;
			$this->data['currencyList'] = $this->location_model->get_all_details(CURRENCY, array('status' => 'Active'));
			$this->data['heading'] = $heading;
			$this->load->view('admin/location/add_noshow_fee', $this->data);
		}
	}

	/**
	 * Insert noshow fee into database
	 */
	public function insert_noshow_fee()
	{
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		} else {
			$condition = array();
			if ($location_id == ''){
				$this->location_model->commonInsertUpdate(LOCATIONS,'insert',$excludeArr,$location_data,$condition);
				$location_id = $this->cimongo->insert_id();
				$this->setErrorMessage('success','Location added successfully','admin_location_added_success');
			} else {
				$condition = array('_id' => new \MongoId($location_id));
				$this->location_model->commonInsertUpdate(LOCATIONS,'update',$excludeArr,$location_data,$condition);
				$this->setErrorMessage('success','Location updated successfully','admin_location_updated_success');
			}	

		}

		
	}
}

/* End of file location.php */
/* Location: ./application/controllers/admin/location.php */