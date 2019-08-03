<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This model contains all db functions related to location management
* @author Casperon
*
**/
 
class Location_model extends My_Model{
	public function __construct(){
		parent::__construct();
	}
	/**
	* 
	* This function get the availbale service in a particular location
	* @param String $collection
	* @param String $field
	* @param Array $data
	* 
	**/
	public function get_available_services($collection='', $field='',$data=array()){
		for($i=0;$i<count($data);$i++){
			if($data[$i] == 'on'){
				unset($data[$i]);
			}
		}
		if($field=='_id'){
			$datanew=$data;
			$data=array();
			$k=0;
			foreach($datanew as $key=>$value){
				$data[$k]=new MongoId($value);
				$k++;
			}
		}
		$this->cimongo->select(array('name'));
		$this->cimongo->where_in($field,$data);
		$res = $this->cimongo->get($collection);
		return $res;
	}
	/**
	* 
	* This function check the country
	* @param String $collection
	* @param Array $condition
	* @param Array $primary_condition
	* 
	**/
	public function chk_country_exist($collection='',$condition=array(),$primary_condition=array()){
		$this->cimongo->select(array('_id'));
		$this->cimongo->where($primary_condition);
		$this->cimongo->or_where($condition);
		$res = $this->cimongo->get($collection);
		return $res;
	}
}