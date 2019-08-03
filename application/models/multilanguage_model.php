<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to user management
 * @author Casperon
 *
 */
class Multilanguage_model extends My_Model
{
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
    * 
    * Getting Users details
    * @param String $condition
	*
    **/
   public function get_language_list(){
		$this->cimongo->select();
		$this->cimongo->order_by(array('name' => 'asc'));
		$res = $this->cimongo->get(LANGUAGES);
		return $res;
   }
   /**
    * 
    * Change language status
    * @param String $Mode
    * @param String $condition
	*
    **/
    public function change_language_status($statusMode='',$checkbox_id=array()){
		#$condArr = array('lang_code'=> array('$ne' => 'en'));
		$condArr = array();
   		if($statusMode == 'Active' || $statusMode == 'Inactive'){			
			$data = array('status' => $statusMode);
			$this->cimongo->where($condArr);
			$this->cimongo->where_in('_id', $checkbox_id);
			$this->cimongo->update_batch(LANGUAGES, $data); 
			}else if($statusMode == 'Delete') {
				$this->cimongo->where_in('_id', $checkbox_id);
				$this->cimongo->where($condArr);
				$this->cimongo->delete_batch(LANGUAGES); 
			}
			return 1;
		
   }
   /**
    * 
    * To delete LANGUAGES
    * @param Integer $ID
    */
   public function delete_language($languageId = ''){   
		$updateCond = array('_id' => new \MongoId($languageId),'lang_code'=> array('$ne' => 'en'));
		$this->cimongo->where($updateCond);
		$this->cimongo->delete(LANGUAGES);
		return 1;
   }   
   /**
    * 
    * To change Language Details
    * @param String $Current status
    * @param Integer $ID
    */
    public function change_language_details($current_status = '',$languageId=''){
    	if($current_status ==  'Active'){
			$new_status = 'Inactive';
		}else if($current_status == 'Inactive'){	
			$new_status = 'Active';
		}else{		 
			$new_status = 'Active';
		}
		$updateCond = array('_id' => new \MongoId($languageId));
		$updateData = array('status' => $new_status);		 
		$this->cimongo->where($updateCond);
		$this->cimongo->update(LANGUAGES, $updateData); 
		return 1;   	
   }
   
  
}