<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* 
* This model contains all db functions related to brand management
* @author Casperon
*
**/

class Brand_model extends My_Model{

	public function __construct(){
		parent::__construct();
	}
	
	/**
	* 
	* This function get the vehicle types (and return the id)
	* @param String $collection
	* @param Array $condition
	* 
	**/
	public function get_all_vehicles($collection='',$condition=array()){
		$this->cimongo->select(array('_id','vehicle_type'));
		$this->cimongo->where($condition);
		$res = $this->cimongo->get($collection);
		return $res;
	}
	
}

?>