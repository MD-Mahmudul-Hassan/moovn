<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Demo extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('user_model'));
		$this->load->model(array('mail_model'));
		$this->load->model(array('app_model'));
    }
	
    public function v_ride(){
		$rideId = $this->uri->segment(3);
		$getRide = $this->user_model->get_all_details(RIDES,array('ride_id' => $rideId));
		echo '<pre>'; print_r($getRide->result()); die;
	}
    public function t_ride(){
		$rideId = $this->uri->segment(3);
		$getRide = $this->user_model->get_all_details(TRACKING,array('ride_id' => $rideId));
		echo '<pre>'; print_r($getRide->result()); die;
	}
    public function v_driver(){
		$driver_id = $this->uri->segment(3);
		$getInfo = $this->user_model->get_all_details(DRIVERS,array('_id' => new MongoId($driver_id)));
		echo '<pre>'; print_r($getInfo->result()); die;
	}
	
}

/* End of file demo.php */
/* Location: ./application/controllers/demo.php */