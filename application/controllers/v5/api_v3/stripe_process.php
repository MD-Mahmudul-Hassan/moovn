<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* Stripe cards related functions
* @author Casperon
*
* */
class Stripe_process extends MY_Controller {

    Public $Apptype = '';
    Public $Userid = '';
    Public $Token = '';

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('app_model');
        $returnArr = array();
		
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
                    if (strtolower($deadChk->row()->push_type) == "ios") {
                        $storedToken = $deadChk->row()->push_notification_key["ios_token"];
                    }
                    if (strtolower($deadChk->row()->push_type) == "android") {
                        $storedToken = $deadChk->row()->push_notification_key["gcm_id"];
                    }
                    if ($storedToken != $this->Token) {
                        echo json_encode(array("is_dead" => "Yes"));
                        die;
                    }
                }
            }
        } catch (MongoException $ex) {
            
        }
        /* Authentication End */
		

    }
    /**
    *
    * Returnning stripe api key
    *
    * */
    public function get_stripe_api_key() {
		$returnArr['status'] = '0';
		$returnArr['response'] =array();
		try {
			$user_id = $this->input->post('user_id');
			if($user_id == ''){
				$user_id = $this->input->get('user_id');
			}
			if($user_id != ''){ 
				$userExists=$this->app_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','stripe_customer_id'));
				if ($userExists->num_rows() > 0) {
					if($this->data['auto_charge'] == 'Yes'){
						if($this->data['stripe_settings']['status'] == 'Enable'){
							$stripeSettings = $this->data['stripe_settings']['settings'];
							$cardsList = array();  
							$cards = $this->format_string('No saved cards!','no_cards');
							$card_status = '0';
							if(isset($userExists->row()->stripe_customer_id)){
								$cardsResponse = $this->get_stripe_card_details((string)$userExists->row()->stripe_customer_id);
								$card_status = $cardsResponse['error_status'];
								$cards=$cardsResponse['result'];
								if($card_status == '1'){
									if(count($cardsResponse['result']) == 0){
										$card_status = '0';
									}
								}
							} 
							$cardsList= array('card_status' =>$card_status,'result' => $cards);
							$returnArr['status'] = '1';
							$returnArr['response']['stripe_keys'] = $stripeSettings;
							$returnArr['response']['cards'] = $cardsList;
						} else {
							$returnArr['response'] = $this->format_string('Sorry, Stripe payment is disabled','stripe_disabled');
						}	
					} else {
						$returnArr['response'] = $this->format_string('Sorry, Stripe payment is not available','stripe_not_available');
					}
				} else {
					$returnArr['response'] = $this->format_string('Invalid User','invalid_user');
				}
			} else {
				$returnArr['response'] = $this->format_string('Some parameters are missing','some_parameters_missing');
			}
		
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
    
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}

	
	
	
	/**
    *
    * save card details in stripe account 
    *
    * */
    public function stripe_delete_card() {
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		$customer_id = $this->input->post('customer_id');
		$user_id = $this->input->post('user_id');
		$card_id = $this->input->post('card_id');
		if($user_id != '' && $customer_id != '' && $card_id != ''){
			$userExists=$this->app_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','stripe_customer_id'));
			if($userExists->num_rows() == 1){
				$stripe_customer_id = '';
				if(isset($userExists->row()->stripe_customer_id)) $stripe_customer_id = $userExists->row()->stripe_customer_id;
				if($stripe_customer_id == $customer_id){
					
					try {
						require_once('./stripe/lib/Stripe.php');		
						$stripe_settings = $this->data['stripe_settings'];
						$secret_key = $stripe_settings['settings']['secret_key'];
						Stripe::setApiKey($secret_key);
						$customer = Stripe_Customer::retrieve($customer_id);
						$car_res = $customer->sources->retrieve($card_id)->delete();  
						if(isset($car_res->deleted)){
							$returnArr['status'] = '1';
							$returnArr['response'] = $this->format_string('Card deleted successfully','card_delete_success');
						} else {
							$returnArr['response'] = $this->format_string('Operation failed','card_delete_error');
						}
					} catch (Exception $e) {    
						$error = $e->getMessage();
						if ($error == '') {
							$error = $this->format_string('Network error, Please try again','network_error_try_again');
						}
						$returnArr['response'] = $this->format_string($error,'card_delete_error');
					}
				} else {
					$returnArr['response'] = $this->format_string('Card is not relevant to this user','card_not_relevent_user');
				}
			} else {
				$returnArr['response'] = $this->format_string('Invalid User','invalid_user');
			}
		} else {
			$returnArr['response'] = $this->format_string('Some parameters are missing','some_parameters_missing');
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
	
	
	/**
    *
    * save card details in stripe account 
    *
    * */
    public function stripe_wallet_payment_process() {
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		try {
			$user_id = $this->input->post('user_id');
			$email = $this->input->post('stripe_email');
			$token = $this->input->post('stripe_token');
			$card_id = $this->input->post('card_id');
			$total_amount = $this->input->post('total_amount');
			
			
			if($user_id != '' && $total_amount != '' && ($token != '' || $card_id != '')){
				$userExists=$this->app_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id'));
				if ($userExists->num_rows() > 0) {

					$getUsrCond = array('_id' => new \MongoId($user_id));
					$get_user_info = $this->app_model->get_selected_fields(USERS,$getUsrCond,array('email','stripe_customer_id'));
					if($email == ''){
						$email = $get_user_info->row()->email;
					}
					
					$stripe_customer_id = '';
					if(isset($get_user_info->row()->stripe_customer_id)){
						$stripe_customer_id = $get_user_info->row()->stripe_customer_id;
					}
					

					$transaction_id = time();
					$pay_date = date("Y-m-d H:i:s");
					$paydataArr = array('user_id' => $user_id,'total_amount' => $total_amount , 'transaction_id' => $transaction_id,'pay_date' => $pay_date,'pay_status' => 'Pending','payment_host' => 'web'); 
					$this->app_model->simple_insert(WALLET_RECHARGE,$paydataArr);


					require_once('./stripe/lib/Stripe.php');
					
					$stripe_settings = $this->data['stripe_settings'];
					$secret_key = $stripe_settings['settings']['secret_key'];
					$publishable_key = $stripe_settings['settings']['publishable_key'];
					
					$product_description = ucfirst($this->config->item('email_title')).' money - Wallet Recharge ';
					
					#echo '<pre>'; print_r($_POST);die;
					Stripe::setApiKey($secret_key);
					
					$currency = $this->data['dcurrencyCode'];
					$amounts = $this->get_stripe_currency_smallest_unit($total_amount,$currency);
					
					
					try {
						// Create a Customer if not exist
						
						$customer_id = $stripe_customer_id;
						if($customer_id == '' || $token != ''){
							if($customer_id != '' && $token != ''){
								$customer = Stripe_Customer::retrieve($customer_id);
								$car_res = $customer->sources->create(array("source" => $token)); 
								if(isset($car_res->id)){
									$card_id = $car_res->id;
									$token = '';
								}
							} else {
								$customer = Stripe_Customer::create(array(
									"card" => $token,
									"description" => $product_description,
									"email" => $email)
								);
								$customer_id = $customer->id;
							}
						}
						
						if(!isset($get_user_info->row()->stripe_customer_id)){
							$this->app_model->update_details(USERS,array('stripe_customer_id' => $customer_id),$getUsrCond);
						} else {
							if($get_user_info->row()->stripe_customer_id == ''){
								$this->app_model->update_details(USERS,array('stripe_customer_id' => $customer_id),$getUsrCond);
							} else if($get_user_info->row()->stripe_customer_id != $customer_id){
								$this->app_model->update_details(USERS,array('stripe_customer_id' => $customer_id),$getUsrCond);
							}
						}
						
						
						// Charge the Customer instead of the card
						if($token == '' && $card_id != ''){
							$charge = Stripe_Charge::create(array(
								"amount" => $amounts, 
								"currency" => $currency,
								"customer" => $customer_id,
								"description" => $product_description,
								'card' => $card_id)
							); 
						} else {
							$charge = Stripe_Charge::create(array(
								"amount" => $amounts, 
								"currency" => $currency,
								"customer" => $customer_id,
								"description" => $product_description)
							);
						}
						
						
						$paymentData = array('user_id' => $user_id, 
											 'transaction_id' => $transaction_id, 
											 'payType' => 'stripe', 
											 'stripeTxnId' => $charge['id']);
						$this->wallet_pay_success($paymentData); 
					} catch (Exception $e) {    
						$error = $e->getMessage();
						if ($error == '') {
							$error = $this->format_string('Payment Cancelled','payment_cancelled');
						}
						$returnArr['status'] = '0';
						$returnArr['response'] = $this->format_string('Transaction Failed','transaction_failed').' : ' . $error;
						$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
						echo $this->cleanString($json_encode);
						die;
					}
				} else {
					$returnArr['response'] = $this->format_string('Invalid User','invalid_user');
				}
			} else {
				$returnArr['response'] = $this->format_string('Some parameters are missing','some_parameters_missing');
			}
		
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
    
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	
	}
	
	/**
     * 
     * Loading success payment
     *
     * */
    public function wallet_pay_success($paymentData = array()) {
		$user_id = $paymentData['user_id'];
		$transaction_id = $paymentData['transaction_id'];
		$payment_type = $paymentData['payType'];
		$trans_id = $paymentData['stripeTxnId'];

		$returnArr['response'] = $this->format_string('Wallet Recharge Successful','wallet_recharge_successful');
		$returnArr['status'] = '0';
		$checkRecharge = $this->app_model->get_all_details(WALLET_RECHARGE, array('transaction_id' => floatval($transaction_id)));
		if ($checkRecharge->num_rows() == 1) {
			if ($checkRecharge->row()->pay_status == 'Pending') {

				/**    update wallet * */
				$total_amount = $checkRecharge->row()->total_amount;

				/* Update the recharge amount to user wallet */
				$this->app_model->update_wallet((string) $user_id, 'CREDIT', floatval($total_amount));
				$currentWallet = $this->app_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
				$avail_amount = 0.00;
				if ($currentWallet->num_rows() > 0) {
					if (isset($currentWallet->row()->total)) {
						$avail_amount = floatval($currentWallet->row()->total);
					}
				}
				$txn_time = time();
				$initialAmt = array('type' => 'CREDIT',
					'credit_type' => 'recharge',
					'ref_id' => $payment_type,
					'trans_amount' => floatval($total_amount),
					'avail_amount' => floatval($avail_amount),
					'trans_date' => new \MongoDate($txn_time),
					'trans_id' => $trans_id
				);
				$this->app_model->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $initialAmt));
				$this->app_model->commonDelete(WALLET_RECHARGE, array('transaction_id' => floatval($transaction_id)));

				$user_info = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('user_name', 'email', 'phone_number'));

				$this->load->model('mail_model');
				$this->mail_model->wallet_recharge_successfull_notification($initialAmt, $user_info, $txn_time, $transaction_id);
				$returnArr['status'] = '1';
			} else {
				$returnArr['response'] = $this->format_string('Wallet Recharge Already Successful','wallet_recharge_already_successful');
				$returnArr['status'] = '0';
			}
		} else {
			$returnArr['response'] = $this->format_string('Transaction records not found','transaction_records_not_found');
			$returnArr['status'] = '0';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode); die;
    }
	
	/**
     * 
     * Complete the payment process through stripe pay and manual payment
     *
     * */
    public function stripe_fees_payment_process() {
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		
		try {
			$user_id = $this->input->post('user_id');
			$ride_id = $this->input->post('ride_id');
			$token = trim($this->input->post('stripe_token'));
			$card_id = trim($this->input->post('card_id'));
			$email = $this->input->post('stripe_email');

			if ($user_id == '' || $ride_id == '' || ($token == '' && $card_id == '')) {
				$returnArr['response'] = $this->format_string('Some parameters are missing','some_parameters_missing');
			} else {
				$getUsrCond = array('_id' => new \MongoId($user_id));
				$get_user_info = $this->app_model->get_selected_fields(USERS, $getUsrCond, array('email', 'stripe_customer_id'));
				if ($get_user_info->num_rows() == 0) {
					$returnArr['response'] = $this->format_string('Payment records not available','payment_records_not_avail');
				} else {
					$checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id,'user.id' => $user_id));
					if ($checkRide->num_rows() == 1) {
						$pay_status = '';
						if(isset($checkRide->row()->pay_status)){
							$pay_status = $checkRide->row()->pay_status;
						}
						if ($pay_status == 'Paid') {
							$returnArr['response'] = $this->format_string('Already you made payment for this ride','payment_already_done');
						} else {
							$this->app_model->update_details(RIDES, array('pay_status' => 'Processing'), array('ride_id' => $ride_id));
							
							$grand_fare = $checkRide->row()->total['grand_fare'];
							$paid_amount = $checkRide->row()->total['paid_amount'];
							$wallet_amount = $checkRide->row()->total['wallet_usage'];
							
							$tips_amt = 0.00;
							if (isset($checkRide->row()->total['tips_amount'])) {
								if ($checkRide->row()->total['tips_amount'] > 0) {
									$tips_amt = $checkRide->row()->total['tips_amount'];
								}
							}
							$grand_fare = $grand_fare + $tips_amt;
							$total_amount = $grand_fare - ($paid_amount + $wallet_amount); 
							if($total_amount > 0){
								
								$currency = $this->data['dcurrencyCode'];
								if(isset($checkRide->row()->currency)) $currency = $checkRide->row()->currency;
								$amounts = $this->get_stripe_currency_smallest_unit($total_amount,$currency);
								
								
								if ($email == '') {
									$email = $get_user_info->row()->email;
								}
								
								$stripe_customer_id = '';
								if (isset($get_user_info->row()->stripe_customer_id)) {
									$stripe_customer_id = $get_user_info->row()->stripe_customer_id;
								}

								require_once('./stripe/lib/Stripe.php');

								$stripe_settings = $this->data['stripe_settings'];
								$secret_key = $stripe_settings['settings']['secret_key']; 
								$product_description = ucfirst($this->config->item('email_title')) . ' - Ride payment ';

								Stripe::setApiKey($secret_key);
								try {
									// Create a Customer
									$customer_id = $stripe_customer_id; 
									if($customer_id == '' || $token != ''){
										if($customer_id != '' && $token != ''){
											$customer = Stripe_Customer::retrieve($customer_id);
											$car_res = $customer->sources->create(array("source" => $token)); 
											if(isset($car_res->id)){
												$card_id = trim($car_res->id);
												$token = '';
											}
										} else {
											$customer = Stripe_Customer::create(array(
												"card" => $token,
												"description" => $product_description,
												"email" => $email)
											);
											$customer_id = $customer->id;
										}
									}


									if(!isset($get_user_info->row()->stripe_customer_id)){
										$this->app_model->update_details(USERS,array('stripe_customer_id' => $customer_id),$getUsrCond);
									} else {
										if($get_user_info->row()->stripe_customer_id == ''){
											$this->app_model->update_details(USERS,array('stripe_customer_id' => $customer_id),$getUsrCond);
										} else if($get_user_info->row()->stripe_customer_id != $customer_id){
											$this->app_model->update_details(USERS,array('stripe_customer_id' => $customer_id),$getUsrCond);
										}
									}


									// Charge the Customer instead of the card
									
									if($token == '' && $card_id != ''){ 
										$charge = Stripe_Charge::create(array(
											"amount" => $amounts, 
											"currency" => $currency,
											"customer" => $customer_id,
											"description" => $product_description,
											'card' => $card_id)
										); 
									} else { 
										$charge = Stripe_Charge::create(array(
											"amount" => $amounts, 
											"currency" => $currency,
											"customer" => $customer_id,
											"description" => $product_description)
										);
									}
									
									$paymentData = array('user_id' => $user_id, 'ride_id' => $ride_id, 'payType' => 'stripe', 'stripeTxnId' => $charge['id']);
									$sendSucc = $this->stripe_fees_pay_success($paymentData);
									$returnArr['status'] = '1';
									$returnArr['response'] = $this->format_string('Transaction Successful','transaction_successful');
								} catch (Exception $e) {
									$error = $e->getMessage();
									if ($error == '') {
										$error = $this->format_string('Payment Cancelled','payment_cancelled');
									}
									$returnArr['response'] = $this->format_string('Transaction Failed','transaction_failed').' : ' . $error;
								}
							} else {
								$returnArr['response'] = $this->format_string('Already you made payment for this ride','payment_already_done');
							}
						}
					} else {
						$returnArr['response'] = $this->format_string('Ride records not available','ride_records_not_avail');
					}
				}
			}
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
	
	
	/**
     * 
     * Loading success payment
     *
     * */
    public function stripe_fees_pay_success($paymentData) {
        $user_id = $paymentData['user_id'];
        $ride_id = $paymentData['ride_id'];
        $payment_type = $paymentData['payType'];
        $trans_id = $paymentData['stripeTxnId'];
		
		try {
			$checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id,'user.id' => $user_id));
			if ($checkRide->num_rows() == 1) {
				if ($checkRide->row()->pay_status == 'Pending' || $checkRide->row()->pay_status == 'Processing') {
					$paid_amount = 0.00;
					if (isset($checkRide->row()->total)) {
						if (isset($checkRide->row()->total['grand_fare']) && isset($checkRide->row()->total['wallet_usage'])) {
							$paid_amount = round(($checkRide->row()->total['grand_fare'] - $checkRide->row()->total['wallet_usage']), 2);
						}
					}
					$pay_summary = 'Gateway';
					if (isset($checkRide->row()->pay_summary['type'])) {
						if ($checkRide->row()->pay_summary['type'] != '') {
							if ($checkRide->row()->pay_summary['type'] != 'Gateway') {
								$pay_summary = $checkRide->row()->pay_summary['type'] . '_Gateway';
							}
						} else {
							$pay_summary = 'Gateway';
						}
					}
					$pay_summary = array('type' => $pay_summary);
					$paymentInfo = array('ride_status' => 'Completed',
						'pay_status' => 'Paid',
						'history.pay_by_gateway_time' => new \MongoDate(time()),
						'total.paid_amount' => round(floatval($paid_amount), 2),
						'pay_summary' => $pay_summary
					);
					$this->app_model->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
					/* Update Stats Starts */
					$current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
					$field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
					$this->app_model->update_stats(array('day_hour' => $current_date), $field, 1);
					/* Update Stats End */

					$avail_data = array('mode' => 'Available', 'availability' => 'Yes');
					$driver_id = $checkRide->row()->driver['id'];
					$this->app_model->update_details(DRIVERS, $avail_data, array('_id' => new \MongoId($driver_id)));
					$transactionArr = array('type' => 'Card',
						'amount' => floatval($paid_amount),
						'trans_id' => $trans_id,
						'trans_date' => new \MongoDate(time())
					);
					$this->app_model->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
					$driverVal = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'push_notification'));
					if ($driverVal->num_rows() > 0) {
						if (isset($driverVal->row()->push_notification)) {
							if ($driverVal->row()->push_notification != '') {
								$message = $this->format_string("payment successfully completed", "payment_completed");
								$options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);
								if (isset($driverVal->row()->push_notification['type'])) {
									if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
										if (isset($driverVal->row()->push_notification['key'])) {
											if ($driverVal->row()->push_notification['key'] != '') {
												$this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'ANDROID', $options, 'DRIVER');
											}
										}
									}
									if ($driverVal->row()->push_notification['type'] == 'IOS') {
										if (isset($driverVal->row()->push_notification['key'])) {
											if ($driverVal->row()->push_notification['key'] != '') {
												$this->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'IOS', $options, 'DRIVER');
											}
										}
									}
								}
							}
						}
					}
					$this->app_model->update_ride_amounts($ride_id);
					$fields = array(
						'ride_id' => (string) $ride_id
					);
					$url = base_url().'prepare-invoice';
					$this->load->library('curl');
					$output = $this->curl->simple_post($url, $fields);
				}
			}
			return "Success";
		} catch (MongoException $ex) {
			return "Error";
		}
    }

	
	
	

}

/* End of file user.php */
/* Location: ./application/controllers/api/user.php */