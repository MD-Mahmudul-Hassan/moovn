<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to promocode management
 * @author Casperon
 *
 */
class Promocode_model extends My_Model{
	public function __construct(){
        parent::__construct();
    }
	
	public function check_code_exist($condition,$promo_id){ 
		$this->cimongo->select(array('_id')); 
		$this->cimongo->where($condition);
		$this->cimongo->where_ne('_id',new \MongoId($promo_id));
		$res = $this->cimongo->get(PROMOCODE);		
		return $res;
	}
}