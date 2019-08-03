<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* 
* This controller contains the functions related to reviews management 
* @author Casperon
*
**/

class Reviews extends MY_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('review_model');
		if ($this->checkPrivileges('reviews',$this->privStatus) == FALSE){
			redirect('admin');
		}
    }

	
	/**
	* 
	* This function loads the reviews list page
	*
	**/
	public function display_reviews_options_list(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_review_display_reviews_options') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_review_display_reviews_options')); 
		    else  $this->data['heading'] = 'Display Reviews Options';
			$condition = array();
			$this->data['reviewsList'] = $this->review_model->get_all_details(REVIEW_OPTIONS,$condition);
			$this->load->view('admin/reviews/display_review_options',$this->data);
		}
	}
	
	/**
	* 
	* This function loads the reviews list page
	*
	**/
	public function add_review_option_form(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_review_display_reviews_options') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_review_display_reviews_options')); 
		    else  $this->data['heading'] = 'Add New Reviews Option';
			$condition = array();
			$this->load->view('admin/reviews/add_review_options',$this->data);
		}
	}
	
	
	
	
	/**
	* 
	* This function loads the add new reviews form
	*
	**/
	public function edit_review_option_form(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$reviews_id = $this->uri->segment(4);
			if($reviews_id!=''){
				$condition = array('_id' => new \MongoId($reviews_id));
				$this->data['reviewsdetails'] = $this->review_model->get_all_details(REVIEW_OPTIONS,$condition);
				if ($this->data['reviewsdetails']->num_rows() != 1){
					redirect('admin/reviews/display_reviews_options_list');
				}
				$heading='Edit Review Option';
			} else {
				redirect('admin');
			}
			$this->data['heading'] = $heading;
			$this->load->view('admin/reviews/edit_review_options',$this->data);
		}
	}
	
	/**
	* 
	* This function and/edit a reviews informations
	*
	**/
	public function insertEditReviews_options(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$option_id = $this->input->post('option_id');
			$option_name = $this->input->post('option_name');
			$option_holder =trim($this->input->post('option_holder'));
			
			$option_number = array(); 
			if($option_id == ''){
				
				$getMaxCount = $this->review_model->get_selected_fields(REVIEW_OPTIONS,array(),array('option_id'),array('option_id'=>'DESC'));
				$option_id_temp = $getMaxCount->row()->option_id->value+1;
				$option_number = array('option_id'=> new \MongoInt64 ($option_id_temp));
			}
			$chkcondition=array('option_name'=>$option_name,'option_holder'=> $option_holder);
			$reviewOptionCheck = $this->review_model->get_all_details(REVIEW_OPTIONS,$chkcondition);
		
			if(($reviewOptionCheck->num_rows() > 0 && $option_id =='') || ($option_id != '' && $reviewOptionCheck->num_rows() > 1)) {
				$this->setErrorMessage('error','Reviews option is already exist, Please try with another title','admin_review_option_exist');
				redirect($_SERVER['HTTP_REFERER']);
			}
			
			$excludeArr = array("option_id","status");
			
			if ($this->input->post('status') == 'on'){
				$reviews_status = 'Active';
			}else{
				$reviews_status = 'Inactive';
			}
			$reviews_dataArr = array('status' => $reviews_status);
			$reviews_data = array_merge($reviews_dataArr,$option_number);
			$condition = array();
			if ($option_id == ''){
				$this->review_model->commonInsertUpdate(REVIEW_OPTIONS,'insert',$excludeArr,$reviews_data,$condition);
				$this->setErrorMessage('success','Reviews added successfully');
			}else {
				$condition = array('_id' => new \MongoId($option_id));
				$this->review_model->commonInsertUpdate(REVIEW_OPTIONS,'update',$excludeArr,$reviews_data,$condition);
				$this->setErrorMessage('success','Reviews updated successfully');
			}
			redirect('admin/reviews/display_reviews_options_list');
		}
	}
	
	
	/**
	* 
	* This function change the reviews status
	*
	**/
	public function change_reviews_status(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$mode = $this->uri->segment(4,0);
			$reviews_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'Inactive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => new \MongoId($reviews_id));
			$this->review_model->update_details(REVIEW_OPTIONS,$newdata,$condition);
			$this->setErrorMessage('success','Reviews Option Status Changed Successfully','admin_review_option_status_change');
			redirect('admin/reviews/display_reviews_options_list');
		}
	}
	
	/**
	*
	* This function delete the reviews record from db
	*
	**/
	public function delete_reviews(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {	
			$reviews_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($reviews_id));
			$this->review_model->commonDelete(REVIEW_OPTIONS,$condition);
			$this->setErrorMessage('success','Reviews deleted successfully','admin_review_delete_success');
			redirect('admin/reviews/display_reviews_options_list'); 
		}
	}
	
		
	/**
	* 
	* This function change the reviews status, delete the reviews record
	*
	**/
	public function change_reviews_status_global(){
		if(count($_POST['checkbox_id']) > 0 &&  $_POST['statusMode'] != ''){
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('error','This service is not available');
				redirect('admin/reviews/display_reviews_options_list');		
			}
			$this->user_model->activeInactiveCommon(REVIEW_OPTIONS,'_id');
			if (strtolower($_POST['statusMode']) == 'delete'){
				$this->setErrorMessage('success','Reviews records deleted successfully','admin_review_records_delete');
			}else {
				$this->setErrorMessage('success','Reviews records status changed successfully','admin_review_records_status_change');
			}
			redirect('admin/reviews/display_reviews_options_list');
		}
	}
	
	
	/**
	* 
	* This function loads the reviews list for driver and rider
	*
	**/
	public function display_reviews_list(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {	
			$reviewType = $this->input->get('q');
			if($reviewType == 'driver' || $reviewType == 'rider'){
				#$reviews_id = $this->uri->segment(4,0);
				#$condition = array('_id' => new \MongoId($reviews_id));
				
				$this->setErrorMessage('success','Reviews deleted successfully','admin_review_delete_successfully');
				redirect('admin/reviews/display_reviews_options_list'); 
			} else {
				$this->setErrorMessage('error','Invalid link','admin_review_invalid_link');
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}

	/**
	* 
	* This function loads the users reviews summary
	*
	**/
	public function view_user_reviews(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {	
			$user_id = $this->uri->segment(4);
			if($user_id != ''){
				$get_review_options = $this->review_model->get_all_details(REVIEW_OPTIONS,array('option_holder' => 'rider')); 
				$reviewsList = array();
				$getCond = array('user.id' => $user_id,'rider_review_status' => 'Yes');
				$get_ratings = $this->review_model->get_selected_fields(RIDES,$getCond,array('ratings.rider','rider_review_status'));   #echo '<pre>'; print_r($get_ratings->result()); die;
				if($get_ratings->num_rows() > 0){
					$usersTotalRates = 0; $commonNumTotal = 0; 
					foreach($get_review_options->result() as $options){
						$tot_no_of_Rates  = 0; $totalRates = 0; $reviewStatus = 'No'; 
						foreach($get_ratings->result() as $ratings){ 
							if(isset($ratings->rider_review_status)){
								if($ratings->rider_review_status == 'Yes'){
									$reviewStatus = $ratings->rider_review_status;
									foreach($ratings->ratings['rider']['ratings'] as $rateOptions){  
										if($options->option_id == $rateOptions['option_id']){ 
											$commonNumTotal++; $tot_no_of_Rates++;
											$totalRates = $totalRates + $rateOptions['rating'];
											$usersTotalRates = $usersTotalRates + $rateOptions['rating'];
										}
									}
								}
							}
						}
                        if($totalRates > 0) {
                            $avgRates = ($totalRates/$tot_no_of_Rates);
                        } else {
                           $avgRates=0.00;
                        }
						$rateArr = array('review_post_status' => $reviewStatus,
													 'no_of_rates' => $tot_no_of_Rates,
													 'IndtotalRates' => $totalRates,
													 'avg_rates' => $avgRates,
													 'option_holder' => $options->option_holder,
													 'option_name' => $options->option_name, 
													 'status' => $options->status,
													 'option_id' => $options->option_id
													 );
						$reviewsList[] = $rateArr;
					}
					#echo $usersTotalRates.'---'.$commonNumTotal;
					
					$commonAvgRates = $usersTotalRates/$commonNumTotal;
					$summaryRateArr = array('totalRates' => $usersTotalRates,'commonNumTotal' => $get_ratings->num_rows(),'commonAvgRates' => $commonAvgRates);
					$this->data['reviewsSummary'] = $summaryRateArr; 
					$this->data['reviewsList'] = $reviewsList;
					#echo '<pre>'; print_r($summaryRateArr); echo '<pre>'; print_r($reviewsList); die;
					if ($this->lang->line('admin_user_rating_summary') != ''){
						$heading = stripslashes($this->lang->line('admin_user_rating_summary')); 
					}else{
						$heading = 'Users Ratings Summary';
					}
					$this->data['heading'] = $heading;
					$this->load->view('admin/reviews/view_review_summary',$this->data);
				} else {
					$this->setErrorMessage('error','No ratings found this user','admin_review_no_rating_found_user');
					redirect($_SERVER['HTTP_REFERER']);
				}
				
			} else {
				$this->setErrorMessage('error','Invalid link','admin_review_invalid_link');
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	/**
	* 
	* This function loads the drivers reviews summary
	*
	**/
	public function view_driver_reviews(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {	
			$driver_id = $this->uri->segment(4);
			if($driver_id != ''){
				
				$get_review_options = $this->review_model->get_all_details(REVIEW_OPTIONS,array('option_holder' => 'driver')); 
				$reviewsList = array();
				$getCond = array('driver.id' => $driver_id,'driver_review_status' => 'Yes');
				$get_ratings = $this->review_model->get_selected_fields(RIDES,$getCond,array('ratings.driver','driver_review_status'));   #echo '<pre>'; print_r($get_ratings->result()); die;
				if($get_ratings->num_rows() > 0){
					$usersTotalRates = 0; $commonNumTotal = 0; 
					foreach($get_review_options->result() as $options){
						$tot_no_of_Rates  = 0; $totalRates = 0; $reviewStatus = 'No'; 
						foreach($get_ratings->result() as $ratings){ 
							if(isset($ratings->driver_review_status)){
								if($ratings->driver_review_status == 'Yes'){
									$reviewStatus = $ratings->driver_review_status;
									foreach($ratings->ratings['driver']['ratings'] as $rateOptions){  
										if($options->option_id == $rateOptions['option_id']){ 
											$commonNumTotal++; $tot_no_of_Rates++;
											$totalRates = $totalRates + $rateOptions['rating'];
											$usersTotalRates = $usersTotalRates + $rateOptions['rating'];
										}
									}
								}
							}
						}
						$avgRates = $totalRates/$tot_no_of_Rates;
						$rateArr = array('review_post_status' => $reviewStatus,
													 'no_of_rates' => $tot_no_of_Rates,
													 'IndtotalRates' => $totalRates,
													 'avg_rates' => $avgRates,
													 'option_holder' => $options->option_holder,
													 'option_name' => $options->option_name, 
													 'status' => $options->status,
													 'option_id' => $options->option_id
													 );
						$reviewsList[] = $rateArr;
					}
					#echo $usersTotalRates.'---'.$commonNumTotal;
					
					$commonAvgRates = $usersTotalRates/$commonNumTotal;
					$summaryRateArr = array('totalRates' => $usersTotalRates,'commonNumTotal' => $get_ratings->num_rows(),'commonAvgRates' => $commonAvgRates);
					$this->data['reviewsSummary'] = $summaryRateArr; 
					$this->data['reviewsList'] = $reviewsList;
					#echo '<pre>'; print_r($summaryRateArr); echo '<pre>'; print_r($reviewsList); die;
					if ($this->lang->line('admin_driver_rating_summary') != ''){
						$heading = stripslashes($this->lang->line('admin_driver_rating_summary')); 
					}else{
						$heading = 'Driver Ratings Summary';
					}
					$this->data['heading'] = $heading;
					$this->load->view('admin/reviews/view_review_summary',$this->data);
				} else {
					$this->setErrorMessage('error','No ratings found for this driver','admin_review_no_rating_found_driver');
					redirect($_SERVER['HTTP_REFERER']);
				}
				
			} else {
				$this->setErrorMessage('error','Invalid link','admin_review_invalid_link');
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}
    public function edit_language_review(){
		if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $review_id = $this->uri->segment(4, 0);
            if ($review_id != '') {
                $condition = array('_id' => new \MongoId($review_id));
                $this->data['reviewdetails'] = $reviewdetails = $this->review_model->get_all_details(REVIEW_OPTIONS, $condition);
                $this->data['languagesList'] = $this->review_model->get_all_details(LANGUAGES, array('status' => 'Active'));
				#echo '<pre>'; print_r( $this->data['reviewdetails']->result()); die;
                if ($this->data['reviewdetails']->num_rows() != 1) {
                    redirect('admin/reviews/display_reviews_options_list');
                }
            }
			
			if ($this->lang->line('edit_review_lanaguage') != '') 
			$heading = stripslashes($this->lang->line('edit_review_lanaguage')); 
			else  $heading = 'Edit Review Setting language';
			 
			 $this->data['heading'] = $heading;
            $this->load->view('admin/reviews/edit_category_language_form', $this->data);
        }
	}
    public function update_language_content(){
		if ($this->checkLogin('A') == '') {
            redirect('admin');
        }  
		$language_content = $this->input->post('option_name_languages');  
		$category_id = $this->input->post('category_id');
		$updCond = array('_id' => new MongoId($category_id ));
		$dataArr = array('option_name_languages' => $language_content);  
		$this->review_model->update_details(REVIEW_OPTIONS,$dataArr ,$updCond);
		$this->setErrorMessage('success', 'Language content updated successfully','language_content_updated_successfully');
        redirect('admin/reviews/display_reviews_options_list');
	}
	
	
	
	
	
}

/* End of file reviews.php */
/* Location: ./application/controllers/admin/reviews.php */