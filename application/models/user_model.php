<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to user management
 * @author Casperon
 *
 */

class User_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    public function insert_user($user_data = array()) {
        if (!empty($user_data)) {
            $this->cimongo->insert(USERS, $user_data);
        }
    }

    public function check_user_exist($condition = array()) {
        $this->cimongo->select();
        $this->cimongo->where($condition);
        return $res = $this->cimongo->get(USERS);
    }

    public function get_user_details($origin, $refcollection, $primary, $reference) {
        if ($origin->num_rows() > 0) {
            $neworigin = $origin->result_array();
            foreach ($origin->result_array() as $key => $value) {
                $data = array($value[$primary]);
                $this->cimongo->where_in($reference, $data);
                $res = $this->cimongo->get($refcollection);
                if ($res->num_rows() > 0) {
                    $neworigin[$key]['geo'] = $res->row()->geo;
                } else {
                    $neworigin[$key]['geo'] = '';
                }
            }
        }
        return (object) $neworigin;
    }

    public function remove_favorite_location($condition = array(), $field = '') {
        $this->cimongo->where($condition);
        $this->cimongo->unset_field($field);
        $this->cimongo->update(FAVOURITE);
    }

    public function get_current_location() {
        
    }

    /**
     *
     * This function return the ride list
     * @param String $type (all/upcoming/completed)
     * @param String $user_id
     * @param Array $fieldArr
     *
     * */
    public function get_ride_list($user_id = '', $type = '', $fieldArr = array(), $limit, $offset) {
        if ($user_id != '' && $type != '') {
            $this->cimongo->select($fieldArr);

            switch ($type) {
                case 'all':
                    $where_clause = array("user.id" => $user_id);
                    break;
                case 'onride':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Arrived'),
                            array("ride_status" => 'Onride'),
                            array("ride_status" => 'Finished'),
                        ),
                        "user.id" => $user_id
                    );
                    break;
                case 'upcoming':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Booked'),
                            array("ride_status" => 'Confirmed'),
                        ),
                        'booking_information.est_pickup_date' => array(
                            '$gt' => new MongoDate(time())
                        ),
                        "user.id" => $user_id
                    );
                    break;
                case 'cancelled':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Cancelled'),
                        ),
                        "user.id" => $user_id
                    );
                    break;
                case 'completed':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Completed')
                        ),
                        "user.id" => $user_id
                    );
                    break;
                default:
                    $where_clause = array("user.id" => $user_id);
                    break;
            }
            $this->cimongo->where($where_clause, TRUE);
            $this->cimongo->order_by(array('_id' => 'DESC'));
            $res = $this->cimongo->get(RIDES, $limit, $offset);
            return $res;
        }
    }
    
    public function check_vehicle_number($vehicle_number="", $driver_id=""){
        $exist = 0;
        if($vehicle_number!=""){
            $this->cimongo->select(array('_id')); 
            $this->cimongo->where(array("vehicle_number"=>$vehicle_number));
            if($driver_id!=""){
                $this->cimongo->where_ne('_id',new \MongoId($driver_id));
            }
            $res = $this->cimongo->get(DRIVERS);        
            if($res->num_rows()>0){
                $exist = 1;
            }
        }
        return $exist;
    }
    
    public function user_transaction($user_id, $trans_type) {
   
      if($trans_type!='')
      {
        $option = array(
                array('$match' => array('user_id'=>New \MongoId($user_id))),
                array('$unwind'=>'$transactions'),
                array('$match' => array('transactions.type'=>$trans_type)),
                array('$group'=>array('_id'=>'$_id','transactions'=>array('$push'=>'$transactions'))));
              
       }
       else
       {
          $option = array(
      
                array('$match' => array('user_id'=>New \MongoId($user_id))),
                );
       }
        $res = $this->cimongo->aggregate(WALLET, $option);
        
        return $res;
    }

    public function isVodaCustomer($rider_country_code, $rider_phone_number) {
        $voda_country_code = $this->config->config['voda_country_code'];
        $voda_initial_digits = $this->config->config['voda_initial_digits']; 
        if (in_array($rider_country_code, $voda_country_code)) {
            $returnValue = false;
            foreach ($voda_initial_digits as $phone_number) {
                $posMatch[0] = strpos($rider_phone_number, $phone_number);
                $posMatch[1] = strpos($rider_phone_number, '0' . $phone_number);
                if ($posMatch[0] === 0 || $posMatch[1] === 0) {
                    $returnValue = true;
                    break;
                }
            }
            return $returnValue;
        }
        else {
            return false;
        }
    }

    public function add_welcome_amount($country_code, $currencyCode, $user_id)
    {
        $city = $this->app_model->get_city_by_country_code($country_code);
        if ($city != null) {
            $location_result = $this->app_model->get_selected_fields(LOCATIONS, array('city' => $city));
            if ($location_result->num_rows() > 0 && isset($location_result->row()->welcome_amount) && $location_result->row()->welcome_amount > 0) {
                $welcome_amount = $location_result->row()->welcome_amount;
                $trans_id = time() . rand(0, 2578);
                $initialAmt = array(
                    'type' => 'CREDIT',
                    'credit_type' => 'welcome',
                    'ref_id' => '',
                    'trans_amount' => floatval($welcome_amount),
                    'avail_amount' => floatval($welcome_amount),
                    'trans_date' => new \MongoDate(time()),
                    'trans_id' => $trans_id,
                    'currency'=> $currencyCode
                );
                $this->user_model->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $initialAmt));
                $this->user_model->update_wallet((string) $user_id, 'CREDIT', floatval($welcome_amount));
            }
        }
    }

    public function add_referral_credit($referal_code, $new_user_id, $email, $currencyCode)
    {
        $referral_user = $this->user_model->get_selected_fields(USERS, array('unique_code' => $referal_code), array('referral_count', 'location_id'));
        if ($referral_user->num_rows() > 0) {
            $referral_count = 1;
            if (isset($referral_user->row()->referral_count)) {
                $referral_count = $referral_user->row()->referral_count + 1;
            }
            $user_cond = array('_id' => new \MongoId($referral_user->row()->_id));
            $this->user_model->update_details(USERS, array('referral_count' => floatval($referral_count)), $user_cond);
        
            $ref_status = 'true';
            $location_result = $this->app_model->get_selected_fields(LOCATIONS, array('_id' => $referral_user->row()->location_id));
            if ($location_result->num_rows() > 0 && isset($location_result->row()->referral_credit)) {
                $referral_credit = $location_result->row()->referral_credit;
                $referral_credit_type = $location_result->row()->referral_credit_type;
                if ($referral_credit_type == 'on_first_ride') {
                    $ref_status = 'false';
                }
                $refer_history_update_data = array(
                    'reference_id' => (string) $new_user_id,
                    'reference_mail' => (string) $email,
                    'amount_earns' => floatval($referral_credit),
                    'reference_date' => new \MongoDate(time()),
                    'used' => $ref_status
                );
                $refer_history_exist_result = $this->app_model->get_selected_fields(REFER_HISTORY, array('user_id' => new \MongoId($referral_user->row()->_id)));
                if ($refer_history_exist_result->num_rows() == 0) {
                    $this->user_model->simple_insert(REFER_HISTORY, array('user_id' => new \MongoId($referral_user->row()->_id)));
                }
                $this->user_model->simple_push(REFER_HISTORY, array('user_id' => new \MongoId($referral_user->row()->_id)), array('history' => $refer_history_update_data));
                if ($referral_credit_type == 'instant') {
                    $this->user_model->update_wallet((string) $referral_user->row()->_id, 'CREDIT', floatval($referral_credit));
                    $walletDetail = $this->user_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($referral_user->row()->_id)), array('total'));
                    $avail_amount = 0;
                    if (isset($walletDetail->row()->total)) {
                        $avail_amount = $walletDetail->row()->total;
                    }
                    $trans_id = time() . rand(0, 2578);
                    $walletArr = array(
                        'type' => 'CREDIT',
                        'credit_type' => 'referral',
                        'ref_id' => (string) $new_user_id,
                        'trans_amount' => floatval($referral_credit),
                        'avail_amount' => floatval($avail_amount),
                        'trans_date' => new \MongoDate(time()),
                        'trans_id' => $trans_id,
                        'currency' => $currencyCode
                    );
                    $this->user_model->simple_push(WALLET, array('user_id' => new \MongoId($referral_user->row()->_id)), array('transactions' => $walletArr));
                }
            }
        }
    }

    public function first_ride_credit($rider_id)
    {
        $sortArr = array('ride_id' => -1);
        $firstRide = $this->driver_model->get_selected_fields(RIDES, array('user.id' => $rider_id, 'ride_status' => array('$in' => array('Finished', 'Completed'))), array('_id', 'ride_id'), $sortArr, 1, 0);        
        if ($firstRide->num_rows() == 1) {
            $get_referVal = $this->driver_model->get_all_details(REFER_HISTORY, array('history.reference_id' => $rider_id, 'history.used' => 'false'));
            if ($get_referVal->num_rows() > 0) {
                $referer_user_id = (string) $get_referVal->row()->user_id;
                $condition = array(
                    'history.reference_id' => $rider_id,
                    'user_id' => new \MongoId($get_referVal->row()->user_id));                    
                if (is_array($get_referVal->row()->history)) {
                    foreach ($get_referVal->row()->history as $key => $value) {
                        if ($value['reference_id'] == $rider_id) {
                            $trans_amount = $value['amount_earns'];
                        }
                    }
                }
                $referrDataArr = array('history.$.used' => 'true','history.$.amount_earns' => floatval($trans_amount));
                $this->driver_model->update_details(REFER_HISTORY, $referrDataArr, $condition);
                $this->driver_model->update_wallet($referer_user_id, 'CREDIT', floatval($trans_amount));
                $walletDetail = $this->driver_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($referer_user_id)), array('total'));
                $avail_amount = 0;
                if (isset($walletDetail->row()->total)) {
                    $avail_amount = $walletDetail->row()->total;
                }
                $trans_id = time() . rand(0, 2578);
                $walletArr = array(
                    'type' => 'CREDIT',
                    'credit_type' => 'referral',
                    'ref_id' => (string) $rider_id,
                    'trans_amount' => floatval($trans_amount),
                    'avail_amount' => floatval($avail_amount),
                    'trans_date' => new \MongoDate(time()),
                    'trans_id' => $trans_id
                );
                $this->driver_model->simple_push(WALLET, array('user_id' => new \MongoId($referer_user_id)), array('transactions' => $walletArr));
            }
        }
    }
}
