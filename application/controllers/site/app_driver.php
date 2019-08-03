<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This controller contains the functions related to Web View Driver Signup process
* @author Casperon
*
* */
class App_driver extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('driversettings_model');
        $this->load->model('driver_model');
        $this->load->model('mail_model');
        $this->load->model('app_model');
    }

    /**
     * 
     * This function loads the drivers login  form
     * 
     */
    public function register_form() {
		$this->data['heading'] = 'Driver Registration';
		$location = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
		$this->data['locationList'] = $location;
        $this->load->view('driver/forapp/driver_index.php', $this->data);
    }
    
	
	/**
     * 
     * This function checks drivers signup available location
     * 
     */
    public function singupInitiate_category_form() {
			
        if ($this->lang->line('dash_we_are_not_city_yet') != '')
            $dash_we_are_not_city_yet = stripslashes($this->lang->line('dash_we_are_not_city_yet'));
        else
            $dash_we_are_not_city_yet = 'We are not in your city yet';
            
        if ($this->lang->line('dash_no_available_category') != '')
            $dash_no_available_category = stripslashes($this->lang->line('dash_no_available_category'));
        else
            $dash_no_available_category = 'No Available Category In Your Location';
            
        if ($this->lang->line('dash_find_vehicle_category') != '')
            $dash_find_vehicle_category = stripslashes($this->lang->line('dash_find_vehicle_category'));
        else
            $dash_find_vehicle_category = 'Find Vehicle Category';


        $location = $this->uri->segment(4);
        if ($location == 'global') {
            $this->data['heading'] = $dash_we_are_not_city_yet;
            $this->load->view('driver/forapp/no_city_found', $this->data);
        } 
        else if($location == 'Nocategory'){
            $this->data['heading'] = $dash_no_available_category;
            $this->load->view('driver/forapp/no_city_found', $this->data);   
        }
        else {
        $this->data['heading'] = $dash_find_vehicle_category;
		$location = $this->driver_model->get_all_details(LOCATIONS, array('city' => urldecode($location)));
		$lArr =  array();
	    if($location->num_rows()>0){
		if(isset($location->row()->avail_category)){
			if(is_array($location->row()->avail_category)){
				$lArr =(array)array_unique(array_filter($location->row()->avail_category));
				}
			}
		}
			$vehiclesCatList = array();
			if(!empty($lArr)){
				$categoryList = $this->driver_model->get_available_category($lArr);
				
				if($categoryList->num_rows()>0){
					
					$addedCat=array();
					
					foreach($lArr as $id){
					foreach ($categoryList->result_array() as $catkey => $category) {
						
							if($id == $category['_id'] && !in_array($category['_id'],$addedCat)){
								
								$vehiclesCatList[$catkey] = $category;
								$addedCat[]=$id;
							}
						}
					}
					
					foreach ($vehiclesCatList as $catkey => $category) {
						
						$vehCatTypes = array();
						if(isset($category['vehicle_type'])){
						  $vehCatTypes = $category['vehicle_type'];
						}
					   
						$vehicle_type = array();

						$get_vehicle_type = $this->driver_model->get_vehicles_list_by_category($vehCatTypes)->result();
						foreach ($get_vehicle_type as $vehCat) {
							$vehicle_type[] = $vehCat->vehicle_type;
						}
						$vehiclesCatList[$catkey]['vehicle_type_names'] = $vehicle_type;
					}
				}
			}
            $this->data['categoryList'] = $vehiclesCatList;
            $this->load->view('driver/forapp/driver_category.php', $this->data);
        }
    }
	
	/**
     * 
     * This function opens the driver registration form
     * 
     */
    public function singupInitiate_form() { 
        if ($this->lang->line('dash_driver_registration_form') != '')
            $dash_driver_registration_form = stripslashes($this->lang->line('dash_driver_registration_form'));
        else
            $dash_driver_registration_form = 'Driver Registration Form';


        $this->data['heading'] = $dash_driver_registration_form;
        #$LocationName = urldecode($this->uri->segment(4));
        #$vehCatName = urldecode($this->uri->segment(5));
		$LocationName = $this->input->post('locationName');
        $vehCatName = $this->input->post('categoryName');

        $get_locationId = array();
        if ($LocationName != '') {
            $get_locationId = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active', 'city' => $LocationName), array('city' => 'ASC'))->row();
        }
        if (count($get_locationId) == 0) {
           # $this->setErrorMessage('error', 'Sorry, Could not find your location, please try again', 'dash_could_not_location');
            redirect('app/driver/signup');
        }

        $get_vehicle_catId = array();
        if ($vehCatName != '') {
            $get_vehicle_catId = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active', 'name' => $vehCatName), array('city' => 'ASC'))->row();
        }
               
        if (count($get_vehicle_catId) > 0) {
			        if(isset($get_locationId->avail_category)){
				      $avail_category = (array)$get_locationId->avail_category;
				         if(!in_array((string)$get_vehicle_catId->_id,$avail_category)){
					      #$this->setErrorMessage('error', 'Sorry, Could not find your vehicle category in this location');
					      redirect('app/driver/signup');
			 	    } else {
						if(!isset($get_vehicle_catId->vehicle_type)){
							#$this->setErrorMessage('error', 'Sorry, Could not find your vehicle type in this category');
							redirect('app/driver/signup');
						}
					}
			      }else{
			     	     #$this->setErrorMessage('error', 'Sorry, Could not find your vehicle category in this location');
				          redirect('app/driver/signup');
			      }
        }else{
		 # $this->setErrorMessage('error', 'Sorry, Could not find your vehicle category please try again');
          redirect('app/driver/signup');
		}
        $this->data['vehicle_types'] = $this->driver_model->get_vehicles_list_by_category($get_vehicle_catId->vehicle_type);
        $this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
        $this->data['categoryDetail'] = $get_vehicle_catId;
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['locationDetail'] = $get_locationId;
        $this->load->view('driver/forapp/register.php', $this->data);
    }
	
	
    /**
     * 
     * This function inserts the new driver to database
     * 
     */
    public function register() {
		
        /**
         * clear the temp folders
         */
        #echo '<pre>'; print_r($_POST); die;

        $driver_id = $this->input->post('driver_id');

        $dir = getcwd() . "/drivers_documents_temp"; //dir absolute path
        $interval = strtotime('-24 hours'); //files older than 24hours
        foreach (glob($dir . "*.*") as $file) {
            if (filemtime($file) <= $interval) {
                unlink($file);
            }
        }


        if ($this->input->post('email') == '') {
			$this->setErrorMessage('error', 'Some of the fields are missing', 'dash_some_fields_missing');
            redirect('app/driver/signup');
        }

        $email = strtolower($this->input->post('email'));

        $checkEmail = $this->driver_model->check_driver_exist(array('email' => $email));
        if ($checkEmail->num_rows() >= 1) {
           $this->setErrorMessage('error', 'This email already exist, please register with different email address.', 'dash_email_already_exist');
            redirect('app/driver/signup');
        }


        if ($this->input->post('status') == 'on') {
            $status = 'Active';
        } else {
            $status = 'Inactive';
        }
        if ($this->input->post('aircond') == 'on') {
            $ac = 'Yes';
        } else {
            $ac = 'No';
        }

        $excludeArr = array("confirm_password", "new_password", "address", "county", "state", "city", "postal_code", "driver_docx", "driver_docx_expiry", "vehicle_docx", "vehicle_docx_expiry", "vehicle_type", "mobile_number", "dail_code", "aircond", "termsCondition", "email");

        $addressArr['address'] = array('address' => $this->input->post('address'),
            'county' => $this->input->post('county'),
            'state' => $this->input->post('state'),
            'city' => $this->input->post('city'),
            'postal_code' => $this->input->post('postal_code')
        );

        $image_data = array();

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
            $image_data['image'] = $logoDetails['file_name'];
        }

        /*         * *
         *
         * document section 
         */
        $documents = array();
        $dr_documentArr = $this->input->post('driver_docx');  #echo '<pre>'; print_r($dr_documentArr); die;
        $dr_expiryArr = $this->input->post('driver_docx_expiry');
        for ($i = 0; $i < count($dr_documentArr); $i++) {
            $fileArr = @explode('|:|', $dr_documentArr[$i]);
            $fileArr = array_filter($fileArr);
            if (count($fileArr) > 0) {
                $docxName = $fileArr[0];
                $fileName = $fileArr[1];
                $fileTypeId = new \MongoId($fileArr[2]);
                if ($docxName != '' && $fileName != '' && count($fileArr) == 3) {
                    @copy('./drivers_documents_temp/' . $fileName, './drivers_documents/' . $fileName);
                }
                if ($dr_expiryArr[$i] == 'Yes') {
                    $expiryDate = $this->input->post('driver-' . url_title($docxName));
                    $excludeArr[] = url_title('driver-' . $docxName);
                } else {
                    $expiryDate = '';
                }
                if (count($fileArr) > 0) {
                    $documents['driver'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate, 'verify_status' => 'No');
                }
            }
        }


        $veh_documentArr = $this->input->post('vehicle_docx');  #echo '<pre>'; print_r($veh_documentArr); die;
        $veh_expiryArr = $this->input->post('vehicle_docx_expiry');
        for ($i = 0; $i < count($veh_documentArr); $i++) {
            $fileArr = @explode('|:|', $veh_documentArr[$i]);
            $docxName = $fileArr[0];
            $fileName = $fileArr[1];
            $fileTypeId = new \MongoId($fileArr[2]);
            if ($docxName != '' && $fileName != '' && count($fileArr) == 3) {
                @copy('./drivers_documents_temp/' . $fileName, './drivers_documents/' . $fileName);
            }
            if ($veh_expiryArr[$i] == 'Yes') {
                $expiryDate = $this->input->post('vehicle-' . url_title($docxName));
                $excludeArr[] = 'vehicle-' . url_title($docxName);
            } else {
                $expiryDate = '';
            }

            $documents['vehicle'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate, 'verify_status' => 'No');
        }

        $driver_data = array('created' => date('Y-m-d H:i:s'),
            'email' => $email,
            'password' => md5($this->input->post('password')),
            'vehicle_type' => new \MongoId($this->input->post('vehicle_type')),
            'status' => $status,
            'ac' => $ac,
            'no_of_rides' => 0,
            'availability' => 'No',
            'mode' => 'Available',
            'dail_code' => (string) $this->session->userdata(APP_NAME.'otp_country_code'),
            'mobile_number' => (string) $this->session->userdata(APP_NAME.'otp_phone_number'),
            'category' => new \MongoId($this->input->post('category'))
        );

         if($this->input->post('driver_commission') == ''){
			$cond=array('_id'=> new \MongoId($this->input->post('driver_location')));
			$get_loc_commison = $this->driver_model->get_selected_fields(LOCATIONS,$cond,array('site_commission','currency','currency_symbol'));
			if(isset($get_loc_commison->row()->site_commission)){ 
				$driver_data['driver_commission'] = floatval($get_loc_commison->row()->site_commission);
			}
			$currencyCode = $this->data['dcurrencyCode'];
			$currencySymbol = $this->data['dcurrencySymbol'];
			if(isset($get_loc_commison->row()->currency)){
				$currencyCode  = $get_loc_commison->row()->currency;
				$currencySymbol  = $get_loc_commison->row()->currency_symbol;
				if($currencySymbol  == '') $currencySymbol = $currencyCode;
			}
			$driver_data['currency']  = $currencyCode;
			$driver_data['currency_symbol']  = $currencySymbol;
		}


        $dataArr = array_merge($driver_data, $image_data, $addressArr, array('documents' => $documents)); 

        $condition = array();
        $this->driver_model->commonInsertUpdate(DRIVERS, 'insert', $excludeArr, $dataArr, $condition);
        $last_insert_id = $this->cimongo->insert_id();
        $fields = array(
            'username' => (string) $last_insert_id,
            'password' => md5((string) $last_insert_id)
        );
        $url = $this->data['soc_url'] . 'create-user.php';
        $this->load->library('curl');
        $output = $this->curl->simple_post($url, $fields);

        /* Update Stats Starts */
        $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
        $field = array('driver.hour_' . date('H') => 1, 'driver.count' => 1);
        $this->driver_model->update_stats(array('day_hour' => $current_date), $field, 1);
        /* Update Stats End */

        $this->mail_model->send_driver_register_confirmation_mail((string)$last_insert_id);


       #$this->setErrorMessage('success', 'You have registered successfully', 'dash_you_have_registered_successfully');
        redirect('v4/app/driver/signup/success');
    }
	
	/*
	***** Function loads when registration is success ******
	*/
	public function success(){
			$this->data['heading']='Login Success';
			$this->load->view('driver/forapp/success.php',$this->data);
	}
	
	/*
	***** This function return available categories in specified location ******
	*/
	public function available_categories(){
		
		$returnArr['status']=0;
		$dataArr='';
		try{
			$location_id=$this->input->post('locId');
			$location_details=$this->driver_model->get_all_details(LOCATIONS,array('_id'=>new MongoId($location_id)));
			if($location_details->num_rows() >0){
			
			if ($this->lang->line('driver_choose_catagory') != '')
				$driver_choose_catagory = stripslashes($this->lang->line('driver_choose_catagory'));
				else
				$driver_choose_catagory = 'Please choose your category...';	
			$avail_category=$location_details->row()->avail_category;
				if(isset($avail_category)&& !empty($avail_category)){
					$returnArr['status']=1;	
					$dataArr.='<option value="">'.$driver_choose_catagory.'</option>';
					foreach($avail_category as $cat){
						
						$cat_details=$this->driver_model->get_all_details(CATEGORY,array('_id'=>new MongoId($cat)));
							$dataArr.='<option value="'.$cat_details->row()->name.'">'.$cat_details->row()->name.'</option>';
					}
					$returnArr['message']=$dataArr;
				}else{
					if ($this->lang->line('dash_no_available_category') != '')
					$dash_no_available_category = stripslashes($this->lang->line('dash_no_available_category'));
					else
					$dash_no_available_category = 'No Category Available In Your Location';
					$returnArr['message']= $dash_no_available_category;
				}
			
			}else{
				if ($this->lang->line('rides_location_not_avail') != '')
				$rides_location_not_avail = stripslashes($this->lang->line('rides_location_not_avail'));
				else
				$rides_location_not_avail = 'Location is not available';
				$returnArr['message']=$rides_location_not_avail;
			}
			
		}catch(MongoException $me){
			if ($this->lang->line('error_in_connnection') != '')
            $error_in_connnection = stripslashes($this->lang->line('error_in_connnection'));
			else
            $error_in_connnection = 'Error in connection';
			$returnArr['message']=$error_in_connnection;
		}
		header("Content-type:text/plain");
		echo json_encode($returnArr);
		
		
	}
	

}

/* End of file app_driver.php */
/* Location: ./application/controllers/site/app_driver.php */