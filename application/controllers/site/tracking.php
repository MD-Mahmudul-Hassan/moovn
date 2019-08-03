<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* Tracking related functions
* @author Casperon
*
* */
class Tracking extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('cookie', 'date', 'form', 'email'));
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->model(array('user_model','app_model'));
	}
	
	/**
	*
	* This function  tracks the ride details
	*
	* */
	public function track_ride_map_details() {
		if(isset($_POST['rideId']) || isset($_GET['rideId'])){
			$ride_id = $this->input->post('rideId');
			if ($ride_id == '') {
				$ride_id = $this->input->get('rideId');
			}
		} else if(isset($_POST['q']) || isset($_GET['q'])){
			$ride_id = $this->input->post('q');
			if ($ride_id == '') {
				$ride_id = $this->input->get('q');
			}
		}

		if ($ride_id == '') {
			$this->setErrorMessage('error','Invalid ride id');
			redirect('');
		}
		$ride_info = $this->user_model->get_all_details(RIDES,array('ride_id' => $ride_id));
		if($ride_info->num_rows() == 0){
			$this->setErrorMessage('error','No records found');
			redirect('');
		}
		$driver_info = array();
		if(isset($ride_info->row()->driver['id'])){
			if($ride_info->row()->driver['id'] != ''){
				$driver_info = $this->user_model->get_selected_fields(DRIVERS,array('_id' => new MongoId($ride_info->row()->driver['id'])),array('avg_review','image'))->row();
			}
		}

		$this->data['driver_info'] = $driver_info;
		$this->data['ride_info'] = $ride_info;
		$this->data['heading'] = 'Track Ride - '.$this->config->item('email_title');
		$this->load->view('site/rides/track_ride_in_map', $this->data);
	}

}

/* End of file tracking.php */
/* Location: ./application/controllers/site/tracking.php */