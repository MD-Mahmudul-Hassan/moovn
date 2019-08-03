<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mongo_lib{
	
	function __construct(){
		
    }
	public function db_backup($path=''){
		date_default_timezone_set('Asia/Kolkata');
		$this->_ci =& get_instance();
		require_once(APPPATH.'/third_party/Mongo/mongodumper.php');
		
		$proj_path = str_replace("system/", "", BASEPATH);
		#echo $proj_path.$path; die;
		$dumper = new MongoDumper('D:/dump/db');
		
		$dumper->run("dectarfortaxi", true);
	}
}