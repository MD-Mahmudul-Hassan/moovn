<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Query extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model(array('user_model'));
		$this->load->model(array('app_model'));
    }
    
	
   
	
}

/* End of file query.php */
/* Location: ./application/controllers/query.php */