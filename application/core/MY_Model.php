<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This model contains all common db related functions
* @author Casperon
*
* */
class My_Model extends CI_Model {

    /**
    * 
    * This function connect the database and load the functions from CI_Model
    *
    * */
    public function __construct() {
        parent::__construct();
    }

    /**
    *
    * This functions returns all the collection details using @param 
    * @param String $collection
    * @param Array $sortArr
    * @param Array $condition
    * @param Numeric $limit
    * @param Numeric $offset
    * @param Array $likearr
    *
    * */
    public function get_all_details($collection, $condition = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->cimongo->select();
        if (!empty($condition)) {
            $this->cimongo->where($condition);
        }
        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->cimongo->or_like($key, $val);
                }
            } else {
                $this->cimongo->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->cimongo->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $res = $this->cimongo->get($collection, $limit, $offset);
        } else {
            $res = $this->cimongo->get($collection);
        }

        return $res;
    }

    /**
     *
     * This functions returns all the collection details using @param 
     * @param String $collection
     * @param Array $sortArr
     * @param Array $fields
     * @param Array $condition
     * @param Numeric $limit
     * @param Numeric $offset
     * @param Array $likearr
     *
     * */
    public function get_selected_fields($collection, $condition = array(), $fields = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->cimongo->select($fields);
        if (!empty($condition)) {
            $this->cimongo->where($condition);
        }
        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->cimongo->or_like($key, $val);
                }
            } else {
                $this->cimongo->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->cimongo->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $res = $this->cimongo->get($collection, $limit, $offset);
        } else {
            $res = $this->cimongo->get($collection);
        }
        return $res;
    }

    /**
     * 
     * This function do all insert and edit operations
     * @param String $collection	   -->	Collection name
     * @param String $mode		   -->	Insert, Update
     * @param Array $excludeArr	   -->   To avoid post inputs
     * @param Array $dataArr         -->   Add additional inputs with posted inputs
     * @param Array $condition      -->  Applicable only for updates
     *
     * */
    public function commonInsertUpdate($collection = '', $mode = '', $excludeArr = '', $dataArr = '', $condition = '') {
        $inputArr = array();
        foreach ($this->input->post() as $key => $val) {
            if (!in_array($key, $excludeArr)) {
                if (is_numeric($val)) {
                    $inputArr[$key] = floatval($val);
                } else {
                    $inputArr[$key] = $val;
                }
            }
        }
        $finalArr = array_merge($inputArr, $dataArr);

        if ($mode == 'insert') {
            return $this->cimongo->insert($collection, $finalArr);
        } else if ($mode == 'update') {
            $this->cimongo->where($condition);
            return $this->cimongo->update($collection, $finalArr);
        }
    }

    /**
     * 
     * Simple function for inserting data into a collection
     * @param String $collection
     * @param Array $data
     *
     * */
    public function simple_insert($collection = '', $data = '') {
        return $this->cimongo->insert($collection, $data);
    }

    /**
     *
     * This functions updates the collection details using @param 
     * @param String $collection
     * @param Array $data
     * @param Array $condition
     *
     * */
    public function update_details($collection = '', $data = '', $condition = '') {
        if (!empty($collection)) {
            $this->cimongo->where($condition);
            return $this->cimongo->update_batch($collection, $data);
        }
    }

    /**
     * 
     * This function deletes the document based upon the condition
     * @param String $collection
     * @param Array $condition
     * */
    public function commonDelete($collection = '', $condition = '') {
        return $this->cimongo->where($condition)->delete_batch($collection);
    }

    /**
     *
     * Common function for executing mongoDB query
     * @param String $Query	->	mongoDB Query
     *
     * */
    public function ExecuteQuery($Query) {
        $res = $this->cimongo->command($Query);
        return $res;
    }

    /**
     *
     * Common function for get last inserted _id
     *
     * */
    public function get_last_insert_id() {
        $last_insert_id = $this->cimongo->insert_id();
        return $last_insert_id;
    }

    /**
     *
     * Get newsletter templates details
     * @param Interger $news_id
     *
     * */
    public function get_newsletter_template_details($news_id = '') {
        $this->cimongo->select();
        if ($news_id != '') {
            $this->cimongo->where(array('news_id' => (int) $news_id));
        }
        $res = $this->cimongo->get(NEWSLETTER);
        return $res->row();
    }

    /**
     *
     * Get newsletter templates details
     * @param Interger $news_id
     *
     * */
    public function get_mail_template($messageTitle = '') {
        $this->cimongo->select();
        $this->cimongo->where(array('message.title' => $messageTitle));
        $res = $this->cimongo->get(NEWSLETTER);
        return $res->row();
    }

    /**
     * 
     * This function change the status of records and delete the records
     * @param String $collection
     * @param String $field
     * 
     * */
    public function activeInactiveCommon($collection = '', $field = '', $delete = TRUE) {
        $data = $_POST['checkbox_id'];
        $mode = $this->input->post('statusMode');
        for ($i = 0; $i <= count($data); $i++) {
            if ($data[$i] == 'on') {
                unset($data[$i]);
            }
        }
        if ($field == '_id') {
            $datanew = $data;
            $data = array();
            $k = 0;
            foreach ($datanew as $key => $value) {
                $data[$k] = new MongoId($value);
                $k++;
            }
        }
        $newdata = array_values($data);
        $this->cimongo->where_in($field, $newdata);
        if (strtolower($mode) == 'delete') {
            if ($delete === TRUE) {
                $this->cimongo->delete_batch($collection);
            } else if ($delete === FALSE) {
                $statusArr = array('status' => 'Deleted');
                $this->cimongo->update_batch($collection, $statusArr);
            }
        } else {
            $statusArr = array('status' => $mode);
            $this->cimongo->update_batch($collection, $statusArr);
        }
    }

    /**
     * 
     * Common select base on the where in conditions
     *
     * @param $condition = array('field','where_in Array');
     */
    public function get_selected_fields_where_in($collection, $conditionArr = array(), $fields = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->cimongo->select($fields);

        if (!empty($conditionArr)) {
            $field = $conditionArr[0];
            $data = $conditionArr[1];
            $condition = $conditionArr[2];

            if (!empty($condition)) {
                $this->cimongo->where($condition);
            }
            if ($field != '' && !empty($data)) {
                if ($field == '_id') {
                    $datanew = $data;
                    $data = array();
                    $k = 0;
                    foreach ($datanew as $key => $value) {
                        $data[$k] = new MongoId($value);
                        $k++;
                    }
                }
                $newdata = array_values($data);
                $this->cimongo->where_in($field, $newdata);
            }
        }

        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->cimongo->or_like($key, $val);
                }
            } else {
                $this->cimongo->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->cimongo->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $res = $this->cimongo->get($collection, $limit, $offset);
        } else {
            $res = $this->cimongo->get($collection);
        }
        return $res;
    }

    /**
     * 
     * Common Email send funciton 
     * @param Array $eamil_vaues
     * @return 1
     *
     */
    public function common_email_send($eamil_vaues = array()) {
        $server_ip = $this->input->ip_address();
        $mail_id = 'set';
#echo '<pre>'; print_r($eamil_vaues); die;
        if ($mail_id != '') {
            if (is_file('./commonsettings/dectar_smtp_settings.php')) {
                include('commonsettings/dectar_smtp_settings.php');
            }
            // Set SMTP Configuration
            if ($config['smtp_user'] != '' && $config['smtp_pass'] != '') {
                $emailConfig = array(
                    'protocol' => 'smtp',
                    'smtp_host' => $config['smtp_host'],
                    'smtp_port' => $config['smtp_port'],
                    'smtp_user' => $config['smtp_user'],
                    'smtp_pass' => $config['smtp_pass'],
                    'auth' => true,
                );
            }

            // Set your email information
            $from = array('email' => $eamil_vaues['from_mail_id'], 'name' => $eamil_vaues['mail_name']);
            $to = $eamil_vaues['to_mail_id'];
            $subject = $eamil_vaues['subject_message'];
            $message = stripslashes($eamil_vaues['body_messages']);
#echo "<pre>"; echo $message; die;
            // Load CodeIgniter Email library
            if ($config['smtp_user'] != '' && $config['smtp_pass'] != '') {
                $this->load->library('email', $emailConfig);
            } else {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                // Additional headers
                $headers .= 'From: ' . $eamil_vaues['mail_name'] . ' <' . $eamil_vaues['from_mail_id'] . '>' . "\r\n";
                if (array_key_exists('cc_mail_id', $eamil_vaues)) {
                    if ($eamil_vaues['cc_mail_id'] != '') {
                        $headers .= 'Cc: ' . $eamil_vaues['cc_mail_id'] . "\r\n";
                    }
                }
                if (array_key_exists('bcc_mail_id', $eamil_vaues)) {
                    if ($eamil_vaues['bcc_mail_id'] != '') {
                        $headers .= 'Bcc: ' . $eamil_vaues['bcc_mail_id'] . "\r\n";
                    }
                }

                // Mail it
                mail($eamil_vaues['to_mail_id'], trim(stripslashes($eamil_vaues['subject_message'])), trim(stripslashes($eamil_vaues['body_messages'])), $headers);
                return 1;
            }

            // Sometimes you have to set the new line character for better result
            $this->email->set_newline("\r\n");
            // Set email preferences
            $this->email->set_mailtype($eamil_vaues['mail_type']);
            $this->email->from($from['email'], $from['name']);
            $this->email->to($to);
            if (array_key_exists('cc_mail_id', $eamil_vaues)) {
                if ($eamil_vaues['cc_mail_id'] != '') {
                    $this->email->cc($eamil_vaues['cc_mail_id']);
                }
            }
            if (array_key_exists('bcc_mail_id', $eamil_vaues)) {
                if ($eamil_vaues['bcc_mail_id'] != '') {
                    $this->email->bcc($eamil_vaues['bcc_mail_id']);
                }
            }
            $this->email->subject($subject);
            $this->email->message($message);
            if (!empty($eamil_vaues['attachments'])) {
                foreach ($eamil_vaues['attachments'] as $attach) {
                    if ($attach != '') {
                        $this->email->attach($attach);
                    }
                }
            }
            // Ready to send email and check whether the email was successfully sent;

            if (!$this->email->send()) {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                // Additional headers
                $headers .= 'From: ' . $eamil_vaues['mail_name'] . ' <' . $eamil_vaues['from_mail_id'] . '>' . "\r\n";
                if (array_key_exists('cc_mail_id', $eamil_vaues)) {
                    if ($eamil_vaues['cc_mail_id'] != '') {
                        $headers .= 'Cc: ' . $eamil_vaues['cc_mail_id'] . "\r\n";
                    }
                }
                if (array_key_exists('bcc_mail_id', $eamil_vaues)) {
                    if ($eamil_vaues['bcc_mail_id'] != '') {
                        $headers .= 'Bcc: ' . $eamil_vaues['bcc_mail_id'] . "\r\n";
                    }
                }

                // Mail it
                return mail($eamil_vaues['to_mail_id'], trim(stripslashes($eamil_vaues['subject_message'])), trim(stripslashes($eamil_vaues['body_messages'])), $headers);
            } else {
                // Show success notification or other things here
                //echo 'Success to send email';
                return 1;
            }
        } else {
            return 1;
        }
    }

    /**
     * 
     * This function return the admin settings details
     *
     * */
    public function getAdminSettings() {
        $this->cimongo->select();
        $this->cimongo->where(array('admin_id' => '1'));
        $result = $this->cimongo->get(ADMIN);
        unset($result->row()->admin_password);
        return $result;
    }

    /**
     * 
     * This function return the count of particular records
     * @param String $collection
     * @param Array $condition
     * @param Array $filterarr
     *
     * */
    public function get_all_counts($collection = '', $condition = array(), $filterarr = array(), $limit = FALSE, $offset = FALSE) {
        if (!empty($condition)) {
            $this->cimongo->where($condition);
        }
        if (!empty($filterarr)) {
            if (count($filterarr) > 0) {
                foreach ($filterarr as $key => $val) {
                    $this->cimongo->or_like($key, $val);
                }
            } else {
                $this->cimongo->like($key, $val);
            }
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            return $this->cimongo->count_all_results($collection, $limit, $offset);
        }
        return $this->cimongo->count_all_results($collection);
    }

    /**
     * 
     * This function push the data in to a field
     * @param String $collection
     * @param Array $condition
     * @param Array/String $pushdata
     *
     * */
    public function simple_push($collection = '', $condition = array(), $pushdata = array()) {
        if (!empty($condition)) {
            $this->cimongo->where($condition);
        }
        $this->cimongo->push($pushdata);
        return $this->cimongo->update($collection);
    }

    /**
     * 
     * This function removes the data in a field
     * @param String $collection
     * @param Array $condition
     * @param Array/String $pushdata
     *
     * */
    public function simple_pull($collection = '', $condition = array(), $pulldata, $value = array()) {
        if (!empty($condition)) {
            $this->cimongo->where($condition);
        }
        if (is_array($pulldata)) {
            foreach ($pulldata as $field => $value) {
                $this->cimongo->pull($field, $value);
            }
        } elseif (is_string($pulldata)) {
            $this->cimongo->pull($pulldata, $value);
        }
        return $this->cimongo->update($collection);
    }

    /**
     * 
     * This function add to set data in a field
     * @param String $collection
     * @param Array $condition
     * @param Array $setdata
     *
     * */
    public function set_to_field($collection = '', $condition = array(), $setdata = array()) {
        if (!empty($condition)) {
            $this->cimongo->where($condition);
        }
        if (is_array($setdata)) {
            $this->cimongo->set($setdata);
        }
        return $this->cimongo->update($collection);
    }

    /**
     * 
     * This function calculate the distance between two lat lon
     * @param String $lat1
     * @param String $lon1
     * @param String $lat2
     * @param String $lon2
     * @param String $unit (M=>Miles,K=>Kilometers,N=>Nautical Miles)
     *
     * */
    public function geoDistance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /**
     * 
     * This function calculate the ETA (return in minutes)
     * @param String $distance km
     * @param String $speed in kmh
     *
     * */
    public function calculateETA($distance, $speed = 20) {
        $time = ($distance / $speed) * 60;
        if ($time > 0) {
            $eta = ceil($time);
            $eta = intval($eta);
        } else {
            $eta = 0;
        }
        return $eta;
    }

    /**
     * 
     * This function update the statistics information
     * @param Array $condition
     * @param String/Array $field
     * @param Numeric $value
     *
     * */
    public function update_stats($condition = '', $field, $value = 1) {
        $this->cimongo->select(array('day_hour'));
        $this->cimongo->where($condition);
        $res = $this->cimongo->get(STATISTICS);
        if ($res->num_rows() > 0) {
            if (!empty($condition)) {
                $this->cimongo->where($condition)->inc($field, $value)->update(STATISTICS);
            }
        } else {
            $this->cimongo->insert(STATISTICS, $condition);
        }
    }

    /**
     * 
     * This function generate the ride id
     *
     * */
    public function get_ride_id() {
        $ride_id = time();
        $condition = array('ride_id' => $ride_id);

        $this->cimongo->select(array('ride_id'));
        $this->cimongo->where($condition);
        $res = $this->cimongo->get(RIDES);
        if ($res->num_rows() > 0) {
            $check = 0;
            $ride_id = time() + rand(0000, 999999);
            while ($check == 0) {
                $condition = array('ride_id' => $ride_id);
                $duplicate_id = $this->get_all_details(RIDES, $condition);
                if ($duplicate_id->num_rows() > 0) {
                    $ride_id = time() + rand(0000, 999999);
                } else {
                    $check = 1;
                }
            }
        }
        return $ride_id;
    }

    /**
     * 
     * This function generate the random string
     *
     * */
    public function get_random_string($length = 6) {
        #$random_string = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        $six_digit_random_number = mt_rand(100000, 999999);
        return $six_digit_random_number;
    }
    
    public function get_random_number($length = 6) {
        $six_digit_random_number = mt_rand(100000, 999999);
        return $six_digit_random_number;
    }

    /**
     * 
     * This function generate the unique id
     *
     * */
    public function get_unique_id($user_name = '', $length = 7) {
        if ($user_name == '') {
            $unique_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        } else {
            $unique_code = preg_replace('/[^A-Za-z0-9\-\']/', '', $user_name);
            $unique_code.= time();
            $unique_code = substr($unique_code, 0, $length);
        }
        $condition = array('unique_code' => strtoupper($unique_code));

        $this->cimongo->select(array('unique_code'));
        $this->cimongo->where($condition);
        $res = $this->cimongo->get(USERS);
        if ($res->num_rows() > 0) {
            $check = 0;
            if ($user_name == '') {
                $unique_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            } else {
                $unique_code = preg_replace('/[^A-Za-z0-9\-\']/', '', $user_name);
                $unique_code.= time();
                $unique_code = substr($unique_code, 0, $length);
            }
            while ($check == 0) {
                $condition = array('unique_code' => strtoupper($unique_code));
                $duplicate_id = $this->get_all_details(USERS, $condition);
                if ($duplicate_id->num_rows() > 0) {
                    $unique_code = time() + rand(0000, 999999);
                } else {
                    $check = 1;
                }
            }
        }
        $unique_code = substr($unique_code, 0, $length);
        return strtoupper($unique_code);
    }

    /**
     * 
     * This function update the total wallet amount
     * @param String $user_id
     * @param Numeric $amount
     *
     * */
    public function update_wallet($user_id = '', $type = '', $amount = 0) {
		if($amount < 0){
			$amount = 0;
		}
        if ($user_id != '' && $amount >= 0) {
            if ($type == 'CREDIT') {
                $this->cimongo->where(array('user_id' => new \MongoId($user_id)))->inc('total', $amount)->update(WALLET);
            } else if ($type == 'DEBIT') {
                #$this->cimongo->where(array('user_id'=>new \MongoId($user_id)))->dec('total',$amount)->update(WALLET);
                $this->cimongo->where(array('user_id' => new \MongoId($user_id)))->set(array('total' => $amount))->update(WALLET);
            }
        }
    }

    /**
     * 
     * This function gets the current currency conversion value
     * @param String $from
     * @param String $to
     * @param Numeric $value
     *
     * */
    public function get_currency_value($value = 1, $from, $to = 'USD') {
        $gCurrencyVal = floatval($this->currencyget->currency_conversion($value, $from, $to));
        $gCurrencyRev = floatval($this->currencyget->currency_conversion($value, $to, $from));
        if ($gCurrencyVal == 0) {
            $CurrencyVal = 1;
            $CurrencyRev = 1;
        } else {
            $CurrencyVal = $gCurrencyVal;
            $CurrencyRev = $gCurrencyRev;
        }
        $currencyValArr = array('CurrencyVal' => round($CurrencyVal, 2), 'CurrencyRev' => round($gCurrencyRev, 2));
        return $currencyValArr;
    }

    /**
    *
    * get ids from device 
    *
    */
    public function get_user_ids_from_device($collection, $data = array(), $field = '') {
        $this->cimongo->select(array('_id','messaging_status',$field));
        $this->cimongo->where_in($field, $data);
        $res = $this->cimongo->get($collection);
        return $res;
    }
    /**
    *
    * get email template
    *
    */
    public function get_email_template($newsid,$langcode='') {
	
		if($langcode==''){
			$langcode = $this->mailLang;
		}
    
        $email_data=$this->get_newsletter_template_details($newsid);
       
        $data=array();
        if($langcode!='en')
        {
         $templateurl=FCPATH.DIRECTORY_SEPARATOR.'newsletter'.DIRECTORY_SEPARATOR.'template' . $newsid . '_'.$langcode.'.php';
        
         
          if(!file_exists($templateurl))
          {
            $subject=$email_data->message['subject'];
            $sender_name = $email_data->sender['name'];
            $sender_email = $email_data->sender['email'];
            $templateurl=FCPATH.DIRECTORY_SEPARATOR.'newsletter'.DIRECTORY_SEPARATOR.'template' . $newsid .'.php';
            
            $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
           
          }
          else{
             
             $lang_details = $email_data->$langcode;
             $subject= $lang_details['email_subject'];
             $sender_name =  $lang_details['sender_name'];
             $sender_email =  $lang_details['sender_email'];
             $templateurl=FCPATH.DIRECTORY_SEPARATOR.'newsletter'.DIRECTORY_SEPARATOR.'template' . $newsid . '_'.$langcode.'.php';
             $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
          }
        }
        else
        {
            $subject=$email_data->message['subject'];
            $sender_name = $email_data->sender['name'];
            $sender_email = $email_data->sender['email'];
            $templateurl=FCPATH.DIRECTORY_SEPARATOR.'newsletter'.DIRECTORY_SEPARATOR.'template' . $newsid .'.php';
            
            $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
        }
       
         return $data;
          
    }
		
		/**
    *
    * get invoice template
    *
    */
    public function get_invoice_template($langcode='') {
	
			if($langcode==''){
				$langcode = $this->mailLang;
			}
   
			$email_data=$this->get_invoice_template_details();
			$data=array();
			if($langcode!='en')
			{
        $templateurl=FCPATH.'invoice'.DIRECTORY_SEPARATOR.'invoice_template_'.$langcode.'.php';
        
				$sender_name =  $this->config->item('email_title');
				$sender_email =  $lang_details['site_contact_mail'];
				if(!file_exists($templateurl))
				{
					$subject=$email_data->message['subject'];
					$templateurl=FCPATH.'invoice'.DIRECTORY_SEPARATOR.'invoice_template.php';
            
					$data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
           
				}
				else{
             
				  $lang_details = $email_data->$langcode;
					$subject=  $lang_details['site_contact_mail'];
          
				  $templateurl=FCPATH.'invoice'.DIRECTORY_SEPARATOR.'invoice_template_'.$langcode.'.php';
				  $data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
				}
			}
			else
			{
				$subject=$email_data->message['subject'];
				$sender_name = $this->config->item('email_title');
				$sender_email = $this->config->item('email');
				$templateurl=FCPATH.'invoice'.DIRECTORY_SEPARATOR.'invoice_template.php';
            
				$data=array("subject"=>$subject,"sender_name"=>$sender_name,"sender_email"=>$sender_email,"templateurl"=>$templateurl);
			}
       
		  return $data;
          
    }
		
    /**
    * 
    * function used to update the ride amount in driver end and site end
    *
    */
    public function update_ride_amounts($ride_id = '') {
		if($ride_id != ''){
			$ride_info_detail = $this->get_selected_fields(RIDES,array('ride_id'=>$ride_id),array('total','ride_status','pay_status','pay_summary','ride_id'));
			if($ride_info_detail->num_rows()==1){
				$amount_in_site = 0;
				$amount_in_driver = 0;
				$pay_type = '';
				$tips_amount = 0;

				$amount_in_site = $ride_info_detail->row()->total['wallet_usage'];
				if (isset($ride_info_detail->row()->pay_summary['type'])) {
					$pay_type = $ride_info_detail->row()->pay_summary['type'];
				}
				
				if(isset($ride_info_detail->row()->total['tips_amount'])){
					$tips_amount = $ride_info_detail->row()->total['tips_amount'];
				}
				
				$total_amount = $ride_info_detail->row()->total['grand_fare'] + $tips_amount;
				
				if ($pay_type == '') {
					$pay_type = 'FREE';
				}
				$siteArray = array('Gateway', 'Wallet_Gateway','FREE','Wallet');
				$driverArray = array('Cash', 'Wallet_Cash');
				
				if (in_array($pay_type, $siteArray)) {
					$amount_in_site = $amount_in_site + $ride_info_detail->row()->total['paid_amount'];
					if($ride_info_detail->row()->total['grand_fare'] >= $ride_info_detail->row()->total['wallet_usage']){
						$amount_in_site = $amount_in_site + $tips_amount;
					}
				}
				
				if(in_array($pay_type, $driverArray)) {
					$tot_fare = $ride_info_detail->row()->total['grand_fare']+ $tips_amount;
					$amount_in_driver = $ride_info_detail->row()->total['paid_amount'];
					if($tot_fare == $ride_info_detail->row()->total['paid_amount']){
						if($ride_info_detail->row()->total['grand_fare'] < $ride_info_detail->row()->total['paid_amount']){
							$amount_in_driver = $amount_in_driver + $tips_amount;
						}
					}
				}
				
				#if($total_amount == ($amount_in_site+$amount_in_driver)){}
				$update_arr = array('amount_detail'=>array('total_amount'=>$total_amount,
														'amount_in_site'=>$amount_in_site,
														'amount_in_driver'=>$amount_in_driver
														)
									);
				$this->update_details(RIDES,$update_arr,array('ride_id'=>$ride_id));
				
			}
		}
	}
	
	/**
	 *
	 * Get invoice templates details
	 * @param Interger $news_id
	 *
	 * */
	public function get_invoice_template_details() {
			$this->cimongo->select();

			$res = $this->cimongo->get(INVOICE);
			return $res->row();
	}
	/* wallet amount auto deduction */
	public function auto_trip_payment_deduction_from_wallet($checkRide=array()){
		$is_completed = 'No';
		$pay_status = 'Pending';
		if(isset($checkRide->row()->ride_id)){
			$user_id=$checkRide->row()->user['id'];
			$get_currency = $this->get_selected_fields(USERS,array('_id'=>new \MongoId($user_id)),array('_id','currency'));
			if($checkRide->row()->currency == $get_currency->row()->currency) {
			$get_wallet = $this->get_all_details(WALLET,array('user_id'=>new \MongoId($user_id)));
			$total_grand_fare = $checkRide->row()->total['grand_fare'];
			if($get_wallet->num_rows() >0){
				if($get_wallet->row()->total > 0){
					$currency = $checkRide->row()->currency;
					
					$grand_fare = $checkRide->row()->total['grand_fare'];
					$paid_amount = $checkRide->row()->total['paid_amount'];
					
					$tips_amt = 0.00;
					if (isset($checkRide->row()->total['tips_amount'])) {
						if ($checkRide->row()->total['tips_amount'] > 0) {
							$tips_amt = round(($checkRide->row()->total['tips_amount']),2);
							
						}
					}
					$grand_fare = $grand_fare + $tips_amt;
					$total_grand_fare = $grand_fare - $paid_amount;
					
					if($total_grand_fare <= $get_wallet->row()->total){
						$bal_walletamount=($get_wallet->row()->total - $total_grand_fare);
						$is_completed= 'Yes';
						$pay_status = 'Paid';
						$wallet_usage_amount = $total_grand_fare;
						
					} else {
						$bal_walletamount= 0;
						$pay_status = 'Processing';
						$wallet_usage_amount = $get_wallet->row()->total;
						
						
					}
					$walletamount=array('total'=>floatval($bal_walletamount));
					$this->update_details(WALLET,$walletamount,array('user_id'=>new \MongoId($user_id)));
					$txn_time = time() . rand(0, 2578);
					$ride_id = $checkRide->row()->ride_id;
					$initialAmt = array('type' => 'DEBIT',
									   'debit_type' => 'payment',
									   'ref_id' => $ride_id,
									   'trans_amount' => floatval($wallet_usage_amount),
									   'avail_amount' => floatval($bal_walletamount),
									   'trans_date' => new \MongoDate(time()),
									   'trans_id' => $txn_time,
									   'currency' => $currency
									);
					$this->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $initialAmt));
					
					$transactionArr = array('type' => 'wallet',
											'amount' => floatval($wallet_usage_amount),
											'trans_id' => $txn_time,
											'trans_date' => new \MongoDate(time())
										);
					$this->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
					
					$pay_summary = array('type' => 'Wallet');
					$paymentInfo = array('pay_status' => $pay_status,
						'history.wallet_usage_time' => new \MongoDate(time()),
						'total.wallet_usage' => $wallet_usage_amount,
						 'pay_summary' => $pay_summary
					);
					$this->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
					
					if($pay_status == 'Paid'){
						
						$paymentInfo['ride_status'] = 'Completed';
						
						
						
					}
					
					
					
					
				}
			} 
		 }
		}
		$returnArr['is_completed'] = $is_completed;
		$returnArr['pay_status'] = $pay_status;
		return $returnArr;
	}
	public function unset_details($collection = '', $fileds_data = '', $condition = '') {
		if($collection != ''){
			$this->cimongo->where($condition)->unset_field($fileds_data)->update_batch($collection);
		}
	}

}
?>