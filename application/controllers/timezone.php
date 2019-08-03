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

	public function timezone_list() {
		$timezones = null;
		if ($timezones === null) {
			$timezones = [];
			$offsets = [];
			$now = new DateTime('now', new DateTimeZone('UTC'));
			foreach (DateTimeZone::listIdentifiers() as $timezone) {
				$now->setTimezone(new DateTimeZone($timezone));
				$offsets[] = $offset = $now->getOffset();
				$timezones[$timezone] = '(' . $this->format_GMT_offset($offset) . ') ' . $this->format_timezone_name($timezone);
				$timezonesV[$timezone.'_'] = $this->format_GMT_offset_value($offset);
			}
			array_multisort($offsets, $timezones);
		}
		
		$tzarray = array();
		foreach($timezones as $key=>$val){
			if($timezonesV[$key."_"]==""){
				$diff_from_GMT = "+0";
			}else{
				$diff_from_GMT = $timezonesV[$key."_"];
			}
			$arr = array("time_zone"=>$key,
										"time_zone_val"=>$val,
										"diff_from_GMT"=>(string)$diff_from_GMT,
										"sort_GMT"=>(string)str_replace(':','.',$diff_from_GMT)
									);
			$tzarray[] = $arr;						
			#$this->user_model->simple_insert(TIMEZONE_LIST, $arr);
		}
		
		echo "<pre>"; print_r($tzarray);
	}

	public function format_GMT_offset($offset) {
		$hours = intval($offset / 3600);
		$minutes = abs(intval($offset % 3600 / 60));
		return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
	}
	public function format_GMT_offset_value($offset) {
		$hours = intval($offset / 3600);
		$minutes = abs(intval($offset % 3600 / 60));
		return ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
	}

	public function format_timezone_name($name) {
		$name = str_replace('/', ', ', $name);
		$name = str_replace('_', ' ', $name);
		$name = str_replace('St ', 'St. ', $name);
		return $name;
	}
	
	
	

	
}

/* End of file demo.php */
/* Location: ./application/controllers/demo.php */