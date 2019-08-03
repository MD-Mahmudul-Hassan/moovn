<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This model contains all db functions related to Currency management
* @author Casperon
*
**/
 
class Currency_model extends My_Model{
	public function __construct(){
		parent::__construct();
	}
	
	/**
	* 
	* This function check the currency
	* @param String $collection
	* @param Array $condition
	* @param Array $primary_condition
	* 
	**/
	public function chk_currency_exist($collection='',$condition=array(),$primary_condition=array()){
		$this->cimongo->select(array('_id'));
		$this->cimongo->where($primary_condition);
		$this->cimongo->or_where($condition);
		$res = $this->cimongo->get($collection);
		return $res;
	}
}	