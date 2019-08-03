<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
 * 
 * User related functions
 * @author Casperon
 *
 **/
 
class User extends MY_Controller {
	function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form','email'));
		$this->load->library(array('encrypt','form_validation'));
		$this->load->model('user_model'); 
		$this->load->model('app_model'); 
		$responseArr=array();
		
		/* $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (stripos($ua, 'cabily2k15android') === false) {
            show_404();
        } */
        header('Content-type:application/json;charset=utf-8');
		/* Authentication Begin */
        $headers = $this->input->request_headers();
        if (array_key_exists("Apptype", $headers)) $this->Apptype = $headers['Apptype'];
        if (array_key_exists("Userid", $headers)) $this->Userid = $headers['Userid'];
        if (array_key_exists("Apptoken", $headers)) $this->Token = $headers['Apptoken'];
        try {
            if ($this->Userid != "" && $this->Token != "" && $this->Apptype != "") {
                $deadChk = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($this->Userid)), array('push_type', 'push_notification_key'));
                if ($deadChk->num_rows() > 0) {
					$storedToken = '';
                    if (strtolower($deadChk->row()->push_type) == "ios") {
                        $storedToken = $deadChk->row()->push_notification_key["ios_token"];
                    }
                    if (strtolower($deadChk->row()->push_type) == "android") {
                        $storedToken = $deadChk->row()->push_notification_key["gcm_id"];
                    }
					$c_fun= $this->router->fetch_method();
					$apply_function = array('login_user','social_Login');
					if(!in_array($c_fun,$apply_function)){
						if($storedToken!=''){
							if ($storedToken != $this->Token) {
								echo json_encode(array("is_dead" => "Yes"));
								die;
							}
						}
					}
                }
            }
        } catch (MongoException $ex) {
            
        }
		/* Authentication End */
    }
	
	 /**
    *
    * This Function applying the tips amount for driver
    *
    * */
    public function apply_tips_amount() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            $tips_amount = $this->input->post('tips_amount');
            if ($ride_id != '' && $tips_amount != '') {
                $cond = array('ride_id' => $ride_id);
                $rideInfo = $this->app_model->get_selected_fields(RIDES, $cond, array('total','pay_status'));
                if ($rideInfo->num_rows() > 0) {
					if($rideInfo->row()->pay_status == 'Pending'){
					  $dataArr = array('total.tips_amount' => floatval($tips_amount));
						$this->app_model->update_details(RIDES, $dataArr, $cond);
						$responseArr['response']['tips_amount'] = (string) number_format($tips_amount, 2);
						$responseArr['response']['total'] = (string) number_format(($rideInfo->row()->total['grand_fare']+$tips_amount), 2);
						$responseArr['response']['tip_status'] = '1';
						$responseArr['response']['msg'] = $this->format_string('tips added successfully','tips_added');
						$responseArr['status'] = '1';
				    } else {
						$responseArr['response'] = $this->format_string('You Can\'t apply tips amount right now.','cant_apply_tips');
					}
                  } else {
                    $responseArr['response'] = $this->format_string('Records not available.','no_records_found');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
		$json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    /**
     *
     * This Function applying the tips amount for driver
     *
     * */    
    public function remove_tips_amount() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            if ($ride_id != '') {
                $cond = array('ride_id' => $ride_id);
                $rideInfo = $this->app_model->get_selected_fields(RIDES, $cond, array('total'));
                if ($rideInfo->num_rows() > 0) {
                    $dataArr = array('total.tips_amount' => floatval(0));
                    $this->app_model->update_details(RIDES, $dataArr, $cond);

                    $responseArr['response']['tips_amount'] = '0.00';
					$responseArr['response']['total'] = (string) number_format($rideInfo->row()->total['grand_fare'], 2);
                    $responseArr['response']['tip_status'] = '0';

                    $responseArr['response']['msg'] = $this->format_string('tips removed successfully','tips_removed');
                    $responseArr['status'] = '1';
                } else {
                    $responseArr['response'] = $this->format_string('Records not available.','no_records_found');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
		$json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
    /**
     *
     * This Function return the fare breakup details of a particular ride
     *
     * */
	 
    public function get_fare_breakup() {
        $responseArr['status'] = '0';
        $responseArr['response'] = '';
        try {
            $ride_id = $this->input->post('ride_id');
            $user_id = $this->input->post('user_id');
            if ($user_id != '' && $ride_id != '') {
                $cond = array('user.id' => $user_id,'ride_id' => $ride_id);
                $rideInfo = $this->app_model->get_all_details(RIDES, $cond);
			if ($rideInfo->num_rows() > 0) {
				if ($rideInfo->row()->ride_status =='Finished') {	#	Finished
					$locationArr = array();
					$driverinfoArr = array();
					$fareArr = array();
					
					$tips_amount = 0.00;
					if(isset($rideInfo->row()->total['tips_amount'])){
						$tips_amount = $rideInfo->row()->total['tips_amount'];
					}
					
					$driverInfo = $this->app_model->get_selected_fields(DRIVERS, array('_id'=>new \MongoId($rideInfo->row()->driver['id'])),array('image'));
					$driver_image = USER_PROFILE_IMAGE_DEFAULT;
					if (isset($driverInfo->row()->image)) {
						if ($driverInfo->row()->image != '') {
							$driver_image = USER_PROFILE_IMAGE . $driverInfo->row()->image;
						}
					}
					
					$locationArr = array ( 'pickup_lat'=>(string)$rideInfo->row()->booking_information['pickup']['latlong']['lat'],
					'pickup_lon'=>(string)$rideInfo->row()->booking_information['pickup']['latlong']['lon'],
					'drop_long'=>(string)$rideInfo->row()->booking_information['drop']['latlong']['lat'],
					'drop_lon'=>(string)$rideInfo->row()->booking_information['drop']['latlong']['lon']
				);
					$driverinfoArr = array ( 'name'=>(string)$rideInfo->row()->driver['name'],
					'image'=>(string) base_url().$driver_image,
					'contact_number'=>(string)$rideInfo->row()->driver['phone'],
					'cab_no'=>(string)$rideInfo->row()->driver['vehicle_no'],
					'cab_model'=>(string)$rideInfo->row()->driver['vehicle_model']
															);
					
					$userVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('stripe_customer_id'));
					$having_card = 'No';
					if ($userVal->num_rows() > 0) {
                        if (isset($userVal->row()->stripe_customer_id)) {
                            $stripe_customer_id = $userVal->row()->stripe_customer_id;
                            if ($stripe_customer_id != '') {
                                $having_card = 'Yes';
                            }
                        }
					}					
					$stripe_connected = 'No';
					if($this->data['auto_charge'] == 'Yes'){
						if($having_card == 'Yes'){
							#$stripe_connected = 'Yes';
						}
					}
					$user_timeout = $this->data['user_timeout'];

					
					$distance_unit = $this->data['d_distance_unit'];
					if(isset($rideInfo->row()->fare_breakup['distance_unit'])){
						$distance_unit = $rideInfo->row()->fare_breakup['distance_unit'];
					}
					
					$invoice_src = '';
					if ($rideInfo->row()->ride_status == 'Completed') {
						$invoice_path = 'trip_invoice/'.$ride_id.'_path.jpg'; 
						if(file_exists($invoice_path)) {
							$invoice_src = base_url().$invoice_path;
						}
					}
					
					$fareArr = array ( 'cab_type'=>(string)$rideInfo->row()->booking_information['service_type'],
						'trip_date'=>(string) date("d-m-Y",$rideInfo->row()->booking_information['pickup_date']->sec),
						'base_fare'=>(string)number_format($rideInfo->row()->total['base_fare'],2),
						'ride_duration'=>(string)$rideInfo->row()->summary['ride_duration'],
						'time_fare'=>(string)number_format($rideInfo->row()->total['ride_time'],2),
						'ride_distance'=>(string)$rideInfo->row()->summary['ride_distance'],
						'distance_fare'=>(string)number_format($rideInfo->row()->total['distance'],2),
						'tax_amount'=>(string)number_format($rideInfo->row()->total['service_tax'],2),
						'tip_amount'=>(string)number_format($tips_amount,2),
						'coupon_amount'=>(string)number_format($rideInfo->row()->total['coupon_discount'],2),
						'sub_total'=>(string)number_format($rideInfo->row()->total['total_fare'],2),
						'total'=>(string)number_format($rideInfo->row()->total['grand_fare'],2),
						'wallet_usage'=>(string) number_format($rideInfo->row()->total['wallet_usage'],2),
						'stripe_connected'=>(string)$stripe_connected,
						'payment_timeout'=>(string)$user_timeout,
						'distance_unit'=>(string)$distance_unit,
						'invoice_src' => $invoice_src
					);
					
					
					if(empty($locationArr)){
						$locationArr = json_decode("{}");
					}
					if(empty($driverinfoArr)){
						$driverinfoArr = json_decode("{}");
					}
					if(empty($fareArr)){
						$fareArr = json_decode("{}");
					}
					$responseArr['status'] = '1';
					$responseArr['response'] = array('currency'=>$this->data['dcurrencyCode'],'location'=>$locationArr,'driverinfo'=>$driverinfoArr,'fare'=>$fareArr);
				}else{
					$responseArr['response'] = $this->format_string('You cannot make the payment for this trip now.','cannot_make_payment_now');
				}
                } else {
                    $responseArr['response'] = $this->format_string('Records not available.','no_records_found');
                }
            } else {
                $responseArr['response'] = $this->format_string("Some Parameters are missing","some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $responseArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
		$json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

}

/* End of file user.php */
/* Location: ./application/controllers/api_v2/user.php */