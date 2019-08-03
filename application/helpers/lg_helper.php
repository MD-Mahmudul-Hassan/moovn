<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
*
* Returns the language array for the keywords
*
**/
if ( ! function_exists('get_language_files_for_keywords'))
{
	function get_language_files_for_keywords() {
		$languagPath = 'lg_files/keywords.json';
		$json_content = @file_get_contents($languagPath);
		$decoded_values = json_decode($json_content, TRUE);
		return $decoded_values;
	}
}
/**
*
* Returns the language array for the validation
*
**/
if ( ! function_exists('get_language_files_for_validation'))
{
	function get_language_files_for_validation() {
		$languagPath = 'lg_files/validation.json';
		$json_content = @file_get_contents($languagPath);
		$decoded_values = json_decode($json_content, TRUE);
		return $decoded_values;
	}
}

/**
*
* Returns the language value for the keyword
*
**/
if ( ! function_exists('get_language_value_for_keyword'))
{
	function get_language_value_for_keyword($value="",$lang_code="",$converted_values="") {
		$ci =& get_instance();
		
		$languagPath = 'lg_files/keywords.json';
		$json_content = @file_get_contents($languagPath);
		$decoded_values = json_decode($json_content, TRUE);
		
		$lang_key = FALSE;
		if(!empty($decoded_values)){
			$lang_key = array_search($value,$decoded_values);
		}
		
		if($lang_key){
			$language_list_db = $ci->app_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $lang_code,'type'=>"keyword"));
			
			if($language_list_db->num_rows()>0){
				if(isset($language_list_db->row()->key_values)){
					if(!empty($language_list_db->row()->key_values)){
						if(array_key_exists($lang_key,$language_list_db->row()->key_values)){
							$converted_values = $language_list_db->row()->key_values[$lang_key];
						}
					}
				}
			}
		}
		
		
		if($converted_values==""){
			$converted_values = $value;
		}
		return $converted_values;
	}
}

/**
*
* Returns the language array for the keyword
*
**/
if ( ! function_exists('get_language_array_for_keyword'))
{
	function get_language_array_for_keyword($lang_code="",$converted_values="") {
		$ci =& get_instance();
		
		$languagPath = 'lg_files/keywords.json';
		$json_content = @file_get_contents($languagPath);
		$lang_arrayS = json_decode($json_content, TRUE);
		
		$converted_array = array();
		if($lang_code!=""){
			$language_list_db = $ci->app_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $lang_code,'type'=>"keyword"));
			
			if($language_list_db->num_rows()>0){
				if(isset($language_list_db->row()->key_values)){
					if(!empty($language_list_db->row()->key_values)){
						$converted_array = $language_list_db->row()->key_values;
					}
				}
			}
		}
		
		
		if(!empty($converted_array)){
			$lang_arrayS = $converted_array;
		}
		return $lang_arrayS;
	}
}


/**
*
* convert minutes to hrs
*
**/
if ( ! function_exists('convertToHoursMins'))
{
	function convertToHoursMins($time,$format = '%2d hr:%02d mins') {
		
		$hours = floor($time / 60);
		$minutes = ($time % 60);
		return sprintf($format, $hours, $minutes);
	}
	
}

/**
*
* Returns the language array for the keyword
*
**/
if ( ! function_exists('get_language_array_for_validation'))
{
	function get_language_array_for_validation($lang_code="",$converted_values="") {
		$ci =& get_instance();
		
		$languagPath = 'lg_files/validation.json';
		$json_content = @file_get_contents($languagPath);
		$lang_arrayS = json_decode($json_content, TRUE);
		
		$converted_array = array();
		if($lang_code!=""){
			$language_list_db = $ci->app_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $lang_code,'type'=>"validation"));
			
			if($language_list_db->num_rows()>0){
				if(isset($language_list_db->row()->key_values)){
					if(!empty($language_list_db->row()->key_values)){
						$converted_array = $language_list_db->row()->key_values;
					}
				}
			}
		}
		
		
		if(!empty($converted_array)){
			$lang_arrayS = $converted_array;
		}
		return $lang_arrayS;
	}
}


/* End of file lg_helper.php */
/* Location: ./application/helpers/lg_helper.php */