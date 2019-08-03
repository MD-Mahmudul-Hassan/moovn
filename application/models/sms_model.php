<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to SMS sending
 * @author Casperon
 *
 */
class Sms_model extends My_Model{
    public function __construct(){
        parent::__construct();
        $this->load->library(array('twilio'));
        $this->load->model(array('driver_model'));
    }
    
    /**
    *
    * This function sends the otp on registration account
    * @param String $phone_code
    * @param String $phone_number
    * @param String $otp_number
    *
    **/
    public function opt_for_registration($phone_code='',$phone_number='',$otp_number=''){
        if($phone_code!='' || $phone_number!='' || $otp_number!=''){
            if(substr($phone_code,0,1) == '+'){
                $phone_code = $phone_code;
            } else {
                $phone_code = '+'.$phone_code;
            }
            $from = $this->config->item('twilio_number');
            $to = $phone_code.$phone_number;
            $message = 'Your one-time verification code is '.$otp_number;
            $response = $this->twilio->sms($from, $to, $message); 
        }
    }
    
    /**
    *
    * This function sends the otp regarding the ride
    * @param String $ride_id
    *
    **/
    public function opt_for_ride($ride_id=''){
        if($ride_id!=''){
            $checkRide = $this->get_all_details(RIDES,array('ride_id'=>$ride_id));
            if($checkRide->num_rows() == 1){
                $user_id = $checkRide->row()->user['id'];
                $userVal = $this->get_selected_fields(USERS,array('_id'=>new \MongoId($user_id)),array('country_code','phone_number','email','user_name'));
                if($userVal->num_rows() == 1){
                    $phone_code = $userVal->row()->country_code;
                    $phone_number = $userVal->row()->phone_number;
                    if(substr($phone_code,0,1) == '+'){
                        $phone_code = $phone_code;
                    } else {
                        $phone_code = '+'.$phone_code;
                    }
                    $otp_number=rand(1000,9999); 
                    $from = $this->config->item('twilio_number');
                    $to = $phone_code.$phone_number;
                    $message = 'Your ride '.$ride_id.' one-time password is '.$otp_number;
                    $response = $this->twilio->sms($from, $to, $message); 
                    $condition=array('ride_id'=>$ride_id);
                    $otp_array=array('ride_otp'=>(string)$otp_number);
                    #echo '<pre>'; print_r($otp_array); print_r($condition);
                    $this->update_details(RIDES,$otp_array,$condition);  
                }
            }
        }
    }
    
    /**
    *
    * This function sends the SMS on drived reached rider's location
    * @param String $ride_id
    *
    **/
    public function sms_on_driver_arraival($ride_id=''){
        if($ride_id!=''){
            $checkRide = $this->get_all_details(RIDES,array('ride_id'=>$ride_id));
            if($checkRide->num_rows() == 1){
                $user_id = $checkRide->row()->user['id'];
                $userVal = $this->get_selected_fields(USERS,array('_id'=>new \MongoId($user_id)),array('country_code','phone_number','email','user_name'));
                if($userVal->num_rows() == 1){
                    $phone_code = $userVal->row()->country_code;
                    $phone_number = $userVal->row()->phone_number;
                    if(substr($phone_code,0,1) == '+'){
                        $phone_code = $phone_code;
                    } else {
                        $phone_code = '+'.$phone_code;
                    }
                    $from = $this->config->item('twilio_number');
                    $to = $phone_code.$phone_number;
                    $message = 'Your Moovn ride '.$ride_id.' is here!';
                    $response = $this->twilio->sms($from, $to, $message);
                }
            }
        }
    }
    
        /**
    *
    * This function sends the otp on registration account
    * @param String $phone_code
    * @param String $phone_number
    *
    **/
    public function send_sms_share_driver_tracking_location($mobile_no='',$location='',$user_name='',$ride_id=''){
        if($mobile_no != ''){
            $trackLink = base_url().'track-ride?q='.$ride_id;
            $from = $this->config->item('twilio_number');
            $to = $mobile_no;
            $user_name = ucfirst($user_name);
            $message = $user_name.' shared a ride. Track now at: '.$trackLink.' - Team '.$this->config->item('email_title');  
            $response = $this->twilio->sms($from, $to, $message);
        }
    }
    
    
    /**
    *
    * This function sends the otp on driver registration account
    * @param String $phone_code
    * @param String $phone_number
    * @param String $otp_number
    *
    **/
    public function opt_for_driver_registration($phone_code='',$phone_number='',$otp_number=''){
        if($phone_code!='' || $phone_number!='' || $otp_number!=''){
            if(substr($phone_code,0,1) == '+'){
                $phone_code = $phone_code;
            } else {
                $phone_code = '+'.$phone_code;
            }
            $from = $this->config->item('twilio_number');
            $to = $phone_code.$phone_number;
            $message = 'Your one-time verification code is '.$otp_number;
            $response = $this->twilio->sms($from, $to, $message); 
        }
    }
    
            /**
    *
    * This sends sms to the user when admin added amount
    * @param String $phone_code
    * @param String $phone_number
    *
    **/
    public function send_wallet_money_credit_sms($phone_code='',$phone_number='',$user_name='',$amt=0,$tot_amt=0){
        if($phone_code!='' || $phone_number!='') {
            if(substr($phone_code,0,1) == '+'){
                $phone_code = $phone_code;
            } else {
                $phone_code = '+'.$phone_code;
            }
            $from = $this->config->item('twilio_number');
            $to = $phone_code.$phone_number;
            $message = 'Dear '.$user_name.' Your mobile wallet is credited with '.$amt.'. Your new balance is '.$tot_amt;
            $response = $this->twilio->sms($from, $to, $message); 
        }
    }

    /**
    *
    * This function sends the SMS when ride is accepted
    * @param String $ride_id
    *
    **/
    public function sms_on_ride_accept($ride, $rider, $driver) {
        $phone_code = $rider->country_code;
        $phone_number = $rider->phone_number;
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }
        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;

        $booking_time = date("h:i A", $ride->booking_information['est_pickup_date']->sec).' on '. date("jS M, Y", $ride->booking_information['est_pickup_date']->sec);

        $message = 'Your booking ' . $ride->ride_id . ' for ' . $booking_time . ' is confirmed. Your driver is ' . $driver['name'] . ', ' . $driver['vehicle_model'] . ', license ' . $driver['vehicle_no'];

        $response = $this->twilio->sms($from, $to, $message);
    } 

    /**
    *
    * This function sends the SMS to user
    * @param String $from
    * @param String $to
    * @param String $message
    *
    **/
    public function send($from, $to, $message) {
        return $this->twilio->sms($from, $to, $message);
    }

    public function driver_arrived($user, $driver)
    {
        $vehicleInfo = $this->driver_model->get_selected_fields(MODELS, array('_id' => new \MongoId($driver->vehicle_model)), array('_id', 'name', 'brand_name'));
        if ($vehicleInfo->num_rows() > 0) {
            $vehicle_details = trim($vehicleInfo->row()->brand_name) . ' ' . trim($vehicleInfo->row()->name);
        } else {
            $vehicle_details = '';
        }
        if (substr($user->country_code, 0, 1) == '+') {
            $country_code = $user->country_code;
        } else {
            $country_code = '+' . $user->country_code;
        }
        $from = $this->config->item('twilio_number');
        $to = $country_code . $user->phone_number;

        $driver_name = explode(' ', $driver->driver_name);
        $driver_first_name = $driver_name[0];

        if (!empty($vehicle_details)) {
            $vehicle_details_sms = ', ' . $vehicle_details;
        } else {
            $vehicle_details_sms = '';
        }
        $message = 'Your Moovn ride is here! Your driver is ' . $driver_first_name . $vehicle_details_sms . ', License ' . $driver->vehicle_number;
        $this->twilio->sms($from, $to, $message);
    }
}