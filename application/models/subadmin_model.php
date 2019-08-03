<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to sub-admin management
 * @author Teamtweaks
 *
 */
class Subadmin_model extends My_Model
{
	// To add/edit sub admin
	// Param 	1. Array $dataArr
	//				2. String $condition
	public function add_edit_subadmin($dataArr='',$condition=''){
		if ($condition['_id'] != ''){
			$this->cimongo->where($condition);
			$this->cimongo->update(SUBADMIN,$dataArr);
		}else {
			$this->cimongo->insert(SUBADMIN,$dataArr);
		}
	}
}