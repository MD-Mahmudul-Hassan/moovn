<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* Braintree related functions
* @author Casperon
*
* */
class Braintree_api extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
		  $this->load->library('Braintree_lib','braintree_lib');
        $this->load->model(array('braintree_model','braintree_model'));
        $returnArr = array();
		
		/* 
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        if(stripos($ua,'cabily2k15') === false) {
			show_404();
        } 
		*/
		header('Content-type:application/json;charset=utf-8');
		/*Authentication Begin*/
		$headers = $this->input->request_headers();
		if(array_key_exists("Apptype",$headers)) $this->Apptype =$headers['Apptype'];
		if(array_key_exists("Userid",$headers)) $this->Userid =$headers['Userid'];
		if(array_key_exists("Driverid",$headers)) $this->Driverid =$headers['Driverid'];
		if(array_key_exists("Apptoken",$headers)) $this->Token =$headers['Apptoken'];
		try{
			if(($this->Userid!="" || $this->Driverid!="") && $this->Token!="" && $this->Apptype!=""){
				if($this->Driverid!=''){
					$deadChk = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($this->Driverid)), array('push_notification'));
					if($deadChk->num_rows()>0){
						$storedToken ='';
						if(strtolower($deadChk->row()->push_notification['type']) == "ios"){
							$storedToken = $deadChk->row()->push_notification["key"];
						}
						if(strtolower($deadChk->row()->push_notification['type']) == "android"){
							$storedToken = $deadChk->row()->push_notification["key"];
						}
						$c_fun= $this->router->fetch_method();
						$apply_function = array('update_receive_mode','get_app_info');
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
				if($this->Userid!=''){
					$deadChk = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($this->Userid)), array('push_type', 'push_notification_key'));
					if($deadChk->num_rows()>0){
						$storedToken ='';
						if(strtolower($deadChk->row()->push_type) == "ios"){
							$storedToken = $deadChk->row()->push_notification_key["ios_token"];
						}
						if(strtolower($deadChk->row()->push_type) == "android"){
							$storedToken = $deadChk->row()->push_notification_key["gcm_id"];
						}
						if($storedToken!=''){
							if($storedToken != $this->Token){
								echo json_encode(array("is_dead"=>"Yes")); die;
							}
						}
					}
				}
			 }
		} catch (MongoException $ex) {}
        /* Authentication End */

    }
	
	public function get_braintree_settings(){
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		try {
			$returnArr['status'] = '1';
			$braintree_settings = $this->data['braintree_settings'];
			$braintree_environment = 'sandbox'; #(sandbox/production/qa)
			if($braintree_settings['settings']['mode']=='live'){
				$braintree_environment = 'production';
			}
			if($braintree_settings['settings']['mode']=='sandbox'){
				$braintree_environment = 'sandbox';
			}
			$returnArr['response'] = $this->format_string('Settings Found','settings_found');
			$returnArr['environment'] = $braintree_environment;			
			$returnArr['merchant_id'] = $braintree_settings['settings']['merchant_id'];			
			$returnArr['public_key'] = $braintree_settings['settings']['public_key'];			
			$returnArr['private_key'] = $braintree_settings['settings']['private_key'];
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
	
	public function getallcustomer(){
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		try {
			$resArr = $this->braintree_model->getallcustomer();
			#echo "<pre>"; print_r($resArr); die;
			if($resArr['status']==1){
				$returnArr['status'] = '1';
				$returnArr['response'] =$resArr['customers'];
			}else{
				$returnArr['response'] = $this->format_string('No Customers are connected','no_customer_profile');
			}
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
	
	public function deletecustomer(){
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		try {
			$resArr = $this->braintree_model->delete_customer_profile($customer_id);
			if($resArr['status']==1){
				$returnArr['status'] = '1';
				$returnArr['response'] = $this->format_string('Customer Profile deleted','customer_profile_deleted');;
			}else{
				$returnArr['response'] = $resArr['error_message'];
			}
		}catch (Braintree_Exception_Authentication $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }catch(Braintree_Exception_NotFound $e){
			$returnArr['response'] = $this->format_string('Cannot connect with server','server_eroor');
		}
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
	
	public function getcustomer(){
		$returnArr['status'] = '0';
		$returnArr['Items'] = array();
		$returnArr['profile_id'] = '0';
		$returnArr['card_status'] = '0';
		$returnArr['payment_method_count'] = '0';
		$returnArr['response'] =array();
		try {
			$user_id = $this->input->post('user_id');
			if($user_id == ''){
				$user_id = $this->input->get('user_id');
			}
			if($user_id != ''){
				$userExists=$this->braintree_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','brain_profile_id', 'default_payment_method'));
				if ($userExists->num_rows() > 0) {
					if ($userExists->row()->default_payment_method) {
						$returnArr['default_payment_method'] = $userExists->row()->default_payment_method;
					} else {
						$returnArr['default_payment_method'] = 'cash';
					}
					if($this->data['bt_auto_charge'] == 'Yes'){
						if($this->data['braintree_settings']['status'] == 'Enable'){
							try{
								$returnArr['client_token'] = Braintree_ClientToken::generate();
							}catch(Braintree_Exception_Authentication $e){
								$returnArr['response'] = 'Authentication Problem, Try again Later';
							}catch(Braintree_Exception_Authorization $e){
								$returnArr['response'] = 'Authentication Problem, Try again Later';
							}
							if(isset($userExists->row()->brain_profile_id)){
								$respose = $this->braintree_model->getcustomer($userExists->row()->brain_profile_id);
								if(!empty($respose)){
									if($respose['status']=='1'){
										
										$returnArr['status'] = '1';									
										$returnArr['card_status'] = '1';
										$returnArr['profile_id'] =  (string)$respose['profile_id'];
										$brain_profile_id = $userExists->row()->brain_profile_id;
										$savedItems = $this->braintree_model->getcustomerpaymentmethods($brain_profile_id);
										
										$returnArr['Items'] = array();
										if($savedItems['status']=='1'){
											$returnArr['Items'] = $savedItems['methods'];
											$returnArr['payment_method_count'] = (string) count($returnArr['Items']);
										}
										$returnArr['response'] = $this->format_string('Payment Methods Available','payment_available');
										
										try{
											$returnArr['client_token'] = Braintree_ClientToken::generate(["customerId" => "$brain_profile_id"]);
										}catch(Braintree_Exception_Authentication $e){
											$returnArr['response'] = 'Authentication Problem, Try again Later';
										}catch(Braintree_Exception_Authorization $e){
											$returnArr['response'] = 'Authentication Problem, Try again Later';
										}
							
							
									}else{
										$returnArr['response'] = $this->format_string('No Payment Methods Available','no_payment_methods');
									}
								}else{
									$returnArr['response'] = $this->format_string('No Payment Methods Available','no_payment_methods');
								}
							}else{
								$returnArr['response'] = $this->format_string('No Payment Methods Available','no_payment_methods');
							}
						} else {
							$returnArr['response'] = $this->format_string('Sorry, Cannot continue this payment','cannot_continue_payment');
						}	
					} else {
						$returnArr['response'] = $this->format_string('Sorry, Cannot process this payment','cannot_proceed_payment');
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
	
	public function createcustomerprofile(){
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		try {
			$user_id = $this->input->post('user_id');
			
			$cardArr = array();
			if($this->input->post('card_number') != '') $cardArr['card_number'] = $this->input->post('card_number');			
			if($this->input->post('exp_month') != '') $cardArr['exp_month'] = $this->input->post('exp_month');			
			if($this->input->post('exp_year') != '') $cardArr['exp_year'] = $this->input->post('exp_year');
			if($this->input->post('cvc_number') != '') $cardArr['cvc_number'] = $this->input->post('cvc_number'); 
			
			if ($user_id == '' || count($cardArr) != 4) {
				$returnArr['response'] = $this->format_string('Some parameters are missing','some_parameters_missing');
			} else {
				$getUsrCond = array('_id' => new \MongoId($user_id));
				$get_user_info = $this->braintree_model->get_selected_fields(USERS, $getUsrCond, array('user_name','email', 'brain_profile_id','phone_number'));
				
				if ($get_user_info->num_rows() == 0) {
					$returnArr['response'] = $this->format_string('User records not available','user_records_not_avail');
				} else {
					$resArr = $this->braintree_model->createcustomerprofile($user_id,$cardArr);
					
					if(intval($resArr['profile_id'])!=0){
						$brain_profile_id = (string)$resArr['profile_id'];
						if($brain_profile_id!=''){
							$user_data = array('brain_profile_id'=>(string)$brain_profile_id);						
							$this->braintree_model->update_details(USERS, $user_data,array('_id'=>new \MongoId($user_id)));
							
							$returnArr['status'] = '1';
							$returnArr['response'] = $this->format_string('Operation Successful','operation_successful');
						}else{
							$returnArr['response'] = $resArr['error_message'];;
						}
					}else{
						$returnArr['response'] = $resArr['error_message'];
					}
				}
			}
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
	
	public function wallet_recharge_success($successVal = array()){		
		$rArr = array('avail_amount'=>0,'status'=>'0');
					
		$user_id = $successVal['user_id'];
		$transaction_id = $successVal['transaction_id'];
		$payment_type = $successVal['payType'];
		if(isset($successVal['trans_id'])){
			$trans_id = $successVal['trans_id'];
		} else {
			$trans_id = $transaction_id;
		}
		
		$checkRecharge = $this->braintree_model->get_all_details(WALLET_RECHARGE, array('transaction_id' => floatval($transaction_id)));
		if ($checkRecharge->num_rows() == 1) {
			if ($checkRecharge->row()->pay_status == 'Pending') {
				/**    update wallet * */
				$total_amount = $checkRecharge->row()->total_amount;

				/* Update the recharge amount to user wallet */
				$this->braintree_model->update_wallet((string) $user_id, 'CREDIT', floatval($total_amount));
				$currentWallet = $this->braintree_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
				$avail_amount = 0.00;
				if ($currentWallet->num_rows() > 0) {
					if (isset($currentWallet->row()->total)) {
						$avail_amount = floatval($currentWallet->row()->total);
					}
				}
				$userInfo = $this->braintree_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','currency','currency_symbol'));
				if ($userInfo->num_rows() > 0) {
					$currency = $userInfo->row()->currency;
					$currency_symbol = $userInfo->row()->currency_symbol;
				}
				
				$txn_time = time();
				$initialAmt = array('type' => 'CREDIT',
					'credit_type' => 'recharge',
					'ref_id' => $payment_type,
					'trans_amount' => floatval($total_amount),
					'avail_amount' => floatval($avail_amount),
					'trans_date' => new \MongoDate($txn_time),
					'trans_id' => $trans_id,
					'currency'=>$currency
				);
				$rArr['avail_amount'] = (string)$avail_amount;
				$this->braintree_model->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $initialAmt));
				$this->braintree_model->commonDelete(WALLET_RECHARGE, array('transaction_id' => floatval($transaction_id)));

				$rider_info = $this->braintree_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('user_name', 'email', 'phone_number','currency','currency_symbol'));

				$this->load->model('mail_model');
				$this->mail_model->wallet_recharge_successfull_notification($initialAmt, $rider_info, $txn_time, $transaction_id);
			}
		}
		return $rArr;
	}
	
	
public function make_site_wallet_payment(){
		
		try {
			$user_id = $this->input->post('user_id');
			$total_amount = $this->input->post('total_amount');
			$transaction_id = $this->input->post('transaction_id');
			$nonce = $this->input->post('nonce');
			
			$brain_profile_connected = intval($this->input->post('brain_profile_connected'));
			
			if($nonce != '') $payment_data['nonce'] = $nonce;
						
			$userInfo = $this->braintree_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','brain_profile_id','user_name','phone_number','email','currency','currency_symbol'));
			if ($userInfo->num_rows() > 0) {
				$currency = $userInfo->row()->currency;
				$currency_symbol = $userInfo->row()->currency_symbol;
			}
			
			$payment_data = array();
			if($this->input->post('card_number') != '') $payment_data['card_number'] = $this->input->post('card_number');
			if($this->input->post('exp_month') != '') $payment_data['exp_month'] = $this->input->post('exp_month');
			if($this->input->post('exp_year') != '') $payment_data['exp_year'] = $this->input->post('exp_year');
			if($this->input->post('cvv') != '') $payment_data['cvv'] = $this->input->post('cvv');
			if($this->input->post('card_type') != '') $payment_data['card_type'] = $this->input->post('card_type');
			
			$card_chk = FALSE;
			if($nonce != ''){
				$mode = 'initial';
				$card_chk = TRUE;
			} else {
				if(count($payment_data)==5 && $brain_profile_connected ==0){
					$mode = 'initial';
					$card_chk = TRUE;
				}else if(count($payment_data)==0 && $brain_profile_connected==1){
					$mode = 'auto';
					$card_chk = TRUE;
				}
			}
			
			$txn_chk = FALSE;
			if($mode == 'initial'){
				$condition = array('transaction_id' => floatval($transaction_id), 'user_id' => $user_id);
				$trans_details = $this->braintree_model->get_all_details(WALLET_RECHARGE, $condition);
				if($trans_details->num_rows()>0){
					$txn_chk = TRUE;
					$total_amount = $trans_details->row()->total_amount;
					$user_id = $trans_details->row()->user_id;
					$transaction_id = $trans_details->row()->transaction_id;
					
					$payment_data['transaction_id'] = $transaction_id;
					$payment_data['user_id'] = $user_id;
				}
			}else{
				$txn_chk = TRUE;
				$transaction_id = time();
				$pay_date = date("Y-m-d H:i:s");
				$paydataArr = array('user_id' => $user_id,'total_amount' => (string)$total_amount , 'transaction_id' => $transaction_id,'pay_date' => $pay_date,'pay_status' => 'Pending','currency'=>$currency); 
				$this->braintree_model->simple_insert(WALLET_RECHARGE,$paydataArr);
				
				$payment_data['transaction_id'] = $transaction_id;
				$payment_data['user_id'] = $user_id;
			}
			#echo "<pre>"; print_r($payment_data); die;
			#echo "<pre>"; print_r($trans_details->result()); die;
			
			if($card_chk == TRUE && $txn_chk == TRUE){
				$userExists=$this->braintree_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','brain_profile_id','user_name','phone_number','email','currency','currency_symbol'));
				if ($userExists->num_rows() > 0) {
					if($this->data['bt_auto_charge'] == 'Yes'){
						if($this->data['braintree_settings']['status'] == 'Enable'){
						
							$payment_data['name'] = $userExists->row()->user_name;
							$payment_data['phone_number'] = $userExists->row()->phone_number;
							$payment_data['email'] = $userExists->row()->email;
							
							
							if (!ctype_alpha($payment_data['name'])) {
								$payment_data['name'] = $this->data['siteTitle'].' User';
							}
							
							$original_currency = $this->config->item('currency_code');
							$supportedCurrency = $this->get_braintree_merchant_currency($currency);
							
							if(!$supportedCurrency){
								$currencyval = $this->braintree_model->get_currency_value(round($total_amount, 2), $currency, $original_currency);
								if (!empty($currencyval)) {
									$total_amount = $currencyval['CurrencyVal'];
								}
							}
							$payment_data['total_amount'] = $total_amount;
							
															
							#echo "<pre>"; print_r($payment_data); echo $userExists->row()->brain_profile_id; die;
							
							$ini_chk = FALSE;
							if(isset($userExists->row()->brain_profile_id)){
								if($userExists->row()->brain_profile_id!=''){
									$brain_profile_id = $userExists->row()->brain_profile_id;
									$respose = $this->braintree_model->getcustomer($brain_profile_id);
									if(!empty($respose)){
										if($respose['status']=='1'){
											$paymentData = array('user_id' => $user_id,
																'transaction_id' => $transaction_id,
																'amount' => floatval($payment_data['total_amount']),
																'brain_profile_id' => $brain_profile_id
																);
											
											$pay_responseArr = $this->braintree_model->makewalletpayment($paymentData,$nonce,$supportedCurrency);
										}else{
											$ini_chk = TRUE;
										}
									}else{
										$ini_chk = TRUE;
									}
								}else{
									$ini_chk = TRUE;
								}
							}else{
								$ini_chk = TRUE;
							}
							if($ini_chk == TRUE){
								#echo "<pre>Initial"; print_r($payment_data); die;
								$pay_responseArr = $this->braintree_model->makeinitialwalletpayment($payment_data,$nonce,$supportedCurrency);
							}
							
							#echo "<pre>"; print_r($pay_responseArr); die;
							
							$pay_response['status'] = (string)$pay_responseArr['status'];
							$pay_response['msg'] = $pay_responseArr['error_message'];
							
							$this->data['payOption'] = 'wallet recharge';
							
							if($pay_response['status']==1){
								$paymentData = array('user_id' => $user_id, 
													'transaction_id' => $transaction_id, 
													'payType' => 'BrainTree', 
													'trans_id' => $pay_responseArr['txnId']);
								$wallet_values = $this->wallet_recharge_success($paymentData);
								$pay_response['msg'] = 'Wallet recharged Successfully';
								
								$this->data['trans_id'] = $pay_responseArr['txnId'];
								
								redirect('rider/wallet-recharge/success/' . $user_id . '/' . $transaction_id . '/credit-card/' . $this->data['trans_id']);
								
							}else{
								redirect('rider/wallet-recharge/failed/Error ' . $pay_response['msg'] . '?mobileId=' . $transaction_id . '&user_id=' . $user_id);
							}
						} else {
							redirect('rider/wallet-recharge/failed/Error Sorry, Cannot continue this payment?mobileId=' . $transaction_id . '&user_id=' . $user_id);
						}	
					} else {
						redirect('rider/wallet-recharge/failed/Error Sorry, Cannot continue this payment?mobileId=' . $transaction_id . '&user_id=' . $user_id);
					}
				} else {
					redirect('rider/wallet-recharge/failed/Error Sorry, Cannot continue this payment?mobileId=' . $transaction_id . '&user_id=' . $user_id);
				}
			} else {
				redirect('rider/wallet-recharge/failed/Error Sorry, Cannot continue this payment?mobileId=' . $transaction_id . '&user_id=' . $user_id);
			}
		} catch (MongoException $ex) {
            redirect('rider/wallet-recharge/failed/Error In Connecting with server?mobileId=' . $transaction_id . '&user_id=' . $user_id);
        }
	}
	
	public function drop_in_form(){
		$user_id = $this->input->get('user_id');
		if($user_id==''){
			$user_id = $this->input->post('user_id');
		}
		$total_amount = $this->input->get('total_amount');
		if($total_amount==''){
			$total_amount = $this->input->post('total_amount');
		}
		$brain_profile_id = '';
		$have_account = 0;
		$this->data['payOption']= 'braintree-wallet-recharge';
		$this->data['total_amount']= floatval($total_amount);
		try{
			if($user_id!=''){
				$userExists=$this->braintree_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','brain_profile_id','user_code'));
				if ($userExists->num_rows() > 0) {
					try{
						if($this->data['bt_auto_charge'] == 'Yes'){
							if($this->data['braintree_settings']['status'] == 'Enable'){
								if(isset($userExists->row()->brain_profile_id)){
									$brain_profile_id = $userExists->row()->brain_profile_id;
									if($brain_profile_id!=''){
										$respose = $this->braintree_model->getcustomer($brain_profile_id);
										if(!empty($respose)){
											if($respose['status']!='0'){
												$have_account = 1;
											}
										}
									}
									
								}
							}
						}
						if($have_account==0){
							#$user_code = $userExists->row()->user_code;
							try{
								$clientToken = Braintree_ClientToken::generate();
							}catch(Braintree_Exception_Authentication $e){
								$this->data['errors'] = 'Authentication Problem, Try again Later';
								$this->load->view('mobile/failed.php', $this->data); die;
							}catch(Braintree_Exception_Authorization $e){
								$this->data['errors'] = 'Authentication Problem, Try again Later';
								$this->load->view('mobile/failed.php', $this->data); die;
							}
						}else{
							try{
								$clientToken = Braintree_ClientToken::generate(["customerId" => "$brain_profile_id"]);
							}catch(Braintree_Exception_Authentication $e){
								$this->data['errors'] = 'Authentication Problem, Try again Later';
								$this->load->view('mobile/failed.php', $this->data); die;
							}catch(Braintree_Exception_Authorization $e){
								$this->data['errors'] = 'Authentication Problem, Try again Later';
								$this->load->view('mobile/failed.php', $this->data); die;
							}
						}
					}catch(Braintree_Exception_Authentication $e){
						$this->data['errors'] = 'Authentication Problem, Try again Later';
						$this->load->view('mobile/failed.php', $this->data); die;
					}catch(Braintree_Exception_Authorization $e){
						$this->data['errors'] = 'Authentication Problem, Try again Later';
						$this->load->view('mobile/failed.php', $this->data); die;
					}
						
					$this->data['user_id'] = (string)$user_id;
					$this->data['brain_profile_id'] = (string)$brain_profile_id;
					$this->data['clientToken'] = (string)$clientToken;
					$this->load->view('mobile/braintree.php', $this->data); die;
				}
				$this->data['errors'] = 'Invalid User Account';
				$this->load->view('mobile/failed.php', $this->data);
			}else{ 
				$this->data['errors'] = 'Cannot Identify Your Account';
				$this->load->view('mobile/failed.php', $this->data);
			}
		} catch (MongoException $ex) {
			$this->data['errors'] = 'Error in connection';
			$this->load->view('mobile/failed.php', $this->data);
        }
	}
	
	public function drop_in_process_wallet(){
		try {
			$user_id = $this->input->post('user_id');
			$total_amount = $this->input->post('amount');
			
			$brain_profile_id = $this->input->post('brain_profile_id');
			
			$nonce = (string)$_POST["payment_method_nonce"];
						
			$currency = $this->config->item('currency_code');
			
			$this->data['payOption']= 'braintree-wallet-recharge';
						
			$payment_data = array();
			
			$transaction_id = time();
			$pay_date = date("Y-m-d H:i:s");
			$paydataArr = array('user_id' => $user_id,'total_amount' => (string)$total_amount , 'transaction_id' => $transaction_id,'pay_date' => $pay_date,'pay_status' => 'Pending'); 
			$this->braintree_model->simple_insert(WALLET_RECHARGE,$paydataArr);
			
			$card_chk = FALSE;
			if($user_id!='' && $total_amount!='' && $nonce!=''){
				$card_chk = TRUE;
			}
			
			if($card_chk == TRUE && $nonce !=''){
				$userExists=$this->braintree_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','brain_profile_id','user_name','phone_number','email'));
				if ($userExists->num_rows() > 0) {
					if($this->data['bt_auto_charge'] == 'Yes'){
						if($this->data['braintree_settings']['status'] == 'Enable'){
						
							$payment_data['user_id'] = (string)$userExists->row()->_id;
							$payment_data['name'] = $userExists->row()->user_name;
							$payment_data['phone_number'] = $userExists->row()->phone_number;
							$payment_data['email'] = $userExists->row()->email;
							
							if (!ctype_alpha($payment_data['name'])) {
								$payment_data['name'] = $this->data['siteTitle'].' User';
							}
							
							$original_currency = 'USD';
							if($original_currency != $currency){
								$currencyval = $this->braintree_model->get_currency_value(round($total_amount, 2), $currency, $original_currency);
								if (!empty($currencyval)) {
									$total_amount = $currencyval['CurrencyVal'];
								}
							}
							$payment_data['total_amount'] = $total_amount;
							$payment_data['transaction_id'] = $transaction_id;
							
															
							#echo "<pre>"; print_r($payment_data); die;
							$ini_chk = FALSE;
							if(isset($userExists->row()->brain_profile_id)){
								if($userExists->row()->brain_profile_id!=''){
									$brain_profile_id = $userExists->row()->brain_profile_id;
									$respose = $this->braintree_model->getcustomer($userExists->row()->brain_profile_id);
									if(!empty($respose)){
										if($respose['status']=='1'){
											$paymentData = array('user_id' => $user_id,
																'transaction_id' => $transaction_id,
																'amount' => floatval($payment_data['total_amount']),
																'brain_profile_id' => $brain_profile_id
																);
											#echo '<pre>Normal'; print_r($paymentData); die;
											$pay_responseArr = $this->braintree_model->makewalletpayment($paymentData,$nonce);
										}else{
											$ini_chk = TRUE;
										}
									}else{
										$ini_chk = TRUE;
									}
								}else{
									$ini_chk = TRUE;
								}
							}else{
								$ini_chk = TRUE;
							}
							if($ini_chk == TRUE){
								#echo "<pre>Initial"; print_r($payment_data); die;
								$pay_responseArr = $this->braintree_model->makewalletpayment_dropui($payment_data,$nonce);
							}
							
							#echo "<pre>"; print_r($pay_responseArr); die;
							
							$pay_response['status'] = (string)$pay_responseArr['status'];
							$pay_response['msg'] = $pay_responseArr['error_message'];
														
							if($pay_response['status']==1){
								$paymentData = array('user_id' => $user_id, 
													'transaction_id' => $transaction_id, 
													'payType' => 'BrainTree', 
													'trans_id' => $pay_responseArr['txnId']);
								$wallet_values = $this->wallet_recharge_success($paymentData);
								$pay_response['msg'] = 'Wallet recharged Successfully';
								
								$this->data['trans_id'] = $pay_responseArr['txnId'];
								if($this->data['trans_id'] == ''){
									$this->data['trans_id'] = $transaction_id;
								}
								$this->load->view('mobile/success.php', $this->data);
								
							}else{
								$this->data['errors'] = $pay_response['msg'];
								$this->load->view('mobile/failed.php', $this->data);
							}
						} else {
							$this->data['errors'] = 'Sorry, Cannot continue this payment';
							$this->load->view('mobile/failed.php', $this->data);
						}	
					} else {
						$this->data['errors'] = 'Sorry, Cannot process this payment';
						$this->load->view('mobile/failed.php', $this->data);
					}
				} else {
					$this->data['errors'] = 'Sorry, Cannot process this payment';
					$this->load->view('mobile/failed.php', $this->data);
				}
			} else {
				$this->data['errors'] = 'Sorry, Cannot process this payment';
				$this->load->view('mobile/failed.php', $this->data);
			}
		
		} catch (MongoException $ex) {
			$this->data['errors'] = 'Error in connection';
			$this->load->view('mobile/failed.php', $this->data);
        }
	}
	
	
	
	public function drop_in_view(){
		$user_id = $this->input->get('user_id');
		$msg = '';
		if($this->input->get('msg')!=''){
			$msg = $this->input->get('msg');
		}
		if($user_id==''){
			$user_id = $this->input->post('user_id');
		}
		$brain_profile_id = '';
		$have_account = 0;
		$this->data['payOption']= 'braintree-wallet-recharge';
		$this->data['msg']= $msg;
		try{
			if($user_id!=''){
				$userExists=$this->braintree_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','brain_profile_id','user_code'));
				if ($userExists->num_rows() > 0) {
					try{
						if($this->data['bt_auto_charge'] == 'Yes'){
							if($this->data['braintree_settings']['status'] == 'Enable'){
								if(isset($userExists->row()->brain_profile_id)){
									$brain_profile_id = $userExists->row()->brain_profile_id;
									if($brain_profile_id!=''){
										$respose = $this->braintree_model->getcustomer($brain_profile_id);
										if(!empty($respose)){
											if($respose['status']!='0'){
												$have_account = 1;
											}
										}
									}
									
								}
							}
						}
						if($have_account==0){
							#$user_code = $userExists->row()->user_code;
							try{
								$clientToken = Braintree_ClientToken::generate();
							}catch(Braintree_Exception_Authentication $e){
								$this->data['errors'] = 'Authentication Problem, Try again Later';
								$this->load->view('mobile/failed.php', $this->data); die;
							}catch(Braintree_Exception_Authorization $e){
								$this->data['errors'] = 'Authentication Problem, Try again Later';
								$this->load->view('mobile/failed.php', $this->data); die;
							}
						}else{
							try{
								$clientToken = Braintree_ClientToken::generate(["customerId" => "$brain_profile_id"]);
							}catch(Braintree_Exception_Authentication $e){
								$this->data['errors'] = 'Authentication Problem, Try again Later';
								$this->load->view('mobile/failed.php', $this->data); die;
							}catch(Braintree_Exception_Authorization $e){
								$this->data['errors'] = 'Authentication Problem, Try again Later';
								$this->load->view('mobile/failed.php', $this->data); die;
							}
						}
					}catch(Braintree_Exception_Authentication $e){
						$this->data['errors'] = 'Authentication Problem, Try again Later';
						$this->load->view('mobile/failed.php', $this->data); die;
					}catch(Braintree_Exception_Authorization $e){
						$this->data['errors'] = 'Authentication Problem, Try again Later';
						$this->load->view('mobile/failed.php', $this->data); die;
					}
						
					$this->data['have_account'] = (string)$have_account;
					$this->data['user_id'] = (string)$user_id;
					$this->data['brain_profile_id'] = (string)$brain_profile_id;
					$this->data['clientToken'] = (string)$clientToken;
					$this->load->view('mobile/braintree_accounts.php', $this->data); die;
				}
				$this->data['errors'] = 'Invalid User Account';
				$this->load->view('mobile/failed.php', $this->data);
			}else{ 
				$this->data['errors'] = 'Cannot Identify Your Account';
				$this->load->view('mobile/failed.php', $this->data);
			}
		} catch (MongoException $ex) {
			$this->data['errors'] = 'Error in connection';
			$this->load->view('mobile/failed.php', $this->data);
        }
	}
	
	public function drop_in_process_add() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		$returnArr['client_token'] = '';
		try {
			$user_id = $this->input->post('user_id');
			$nonce = $this->input->post('payment_method_nonce');
			
			$cardArr = array();
			
			if ($user_id == '' || $nonce =='') {
				$returnArr['response'] = $this->format_string('Some parameters are missing','some_parameters_missing');
			} else {
				$getUsrCond = array('_id' => new \MongoId($user_id));
				$userResult = $this->braintree_model->get_selected_fields(USERS, $getUsrCond, array('_id', 'user_name', 'email', 'brain_profile_id', 'phone_number'));
				if ($userResult->num_rows() == 0) {
					$returnArr['response'] = $this->format_string('Operation Failed','operation_failed');
				} else {
					$resArr = $this->braintree_model->addCard($userResult->row(), $nonce);					
					if (intval($resArr['customer_id']) != 0) {
						$brain_profile_id = (string) $resArr['customer_id'];
						try{							
							$returnArr['client_token'] = Braintree_ClientToken::generate(["customerId" => "$brain_profile_id"]);								
							
							$user_data = array('brain_profile_id' => (string) $brain_profile_id);
							$this->braintree_model->update_details(USERS, $user_data, array('_id'=>new \MongoId($user_id)));
							$returnArr['status'] = '1';								
						}catch(Braintree_Exception_Authentication $e){
							$returnArr['response'] = 'Authentication Problem, Try again Later';
						}catch(Braintree_Exception_Authorization $e){
							$returnArr['response'] = 'Authentication Problem, Try again Later';
						}
					} else {
						if ($resArr['status']) {
							$returnArr['status'] = '1';
							$returnArr['response'] = 'Added Card Successfully';
						} else {
							$returnArr['response'] = $resArr['error_message'];
						}
					}
				}
			}
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}	
	
	public function make_trip_payment_rest() {
		$responseArr['status'] = '0';
		$responseArr['response'] = '';

		$ride_id = trim($this->input->post('ride_id'));
		$payment_method_nonce = trim($this->input->post('payment_method_nonce'));

		if (empty($payment_method_nonce)) {
			$params = array('ride_id' => $ride_id);
		}
		else {
			$params = array(
				'ride_id' => $ride_id,
				'payment_method_nonce' => $payment_method_nonce
			);
		}
		$responseArr = $this->braintree_model->make_trip_payment($params);
		
		$json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
	
	public function make_wallet_payment_rest(){
		$responseArr['status'] = '0';
		$responseArr['response'] ='';
		try {
			$user_id = $this->input->post('user_id');
			$total_amount = $this->input->post('amount');
			
			$nonce = (string)$this->input->post('payment_method_nonce');
						
			
						
			$payment_data = array();
			
			$transaction_id = time();
			$pay_date = date("Y-m-d H:i:s");
			$paydataArr = array('user_id' => $user_id,
								'total_amount' => (string)$total_amount , 
								'transaction_id' => $transaction_id,
								'pay_date' => $pay_date,
								'pay_status' => 'Pending'
							); 
			$this->braintree_model->simple_insert(WALLET_RECHARGE,$paydataArr);
			
			$con_chk = FALSE;
			if($user_id!='' && $total_amount!='' && $nonce!=''){
				$con_chk = TRUE;
			}
			
			if($con_chk == TRUE && $nonce !=''){
				$userExists=$this->braintree_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','brain_profile_id','user_name','phone_number','email','currency'));
				$currency = $userExists->row()->currency;
				if ($userExists->num_rows() > 0) {
					if($this->data['bt_auto_charge'] == 'Yes'){
						if($this->data['braintree_settings']['status'] == 'Enable'){
						
							$payment_data['user_id'] = (string)$userExists->row()->_id;
							$payment_data['name'] = $userExists->row()->user_name;
							$payment_data['phone_number'] = $userExists->row()->phone_number;
							$payment_data['email'] = $userExists->row()->email;
							
							if (!ctype_alpha($payment_data['name'])) {
								$payment_data['name'] = $this->data['siteTitle'].' User';
							}
							
							
							$original_currency = $this->config->item('currency_code');
							$supportedCurrency = $this->get_braintree_merchant_currency($currency);
							
							if(!$supportedCurrency){
								$currencyval = $this->braintree_model->get_currency_value(round($total_amount, 2), $currency, $original_currency);
								if (!empty($currencyval)) {
									$total_amount = $currencyval['CurrencyVal'];
								}
							}
							$payment_data['total_amount'] = $total_amount;
							$payment_data['transaction_id'] = $transaction_id;
							
															
							#echo "<pre>"; print_r($payment_data); die;
							
							
							$pay_chk = FALSE;
							if(isset($userExists->row()->brain_profile_id)){
								if($userExists->row()->brain_profile_id!=''){
									$brain_profile_id = $userExists->row()->brain_profile_id;
									$respose = $this->braintree_model->getcustomer($userExists->row()->brain_profile_id);
									if(!empty($respose)){
										if($respose['status']=='1'){
											$paymentData = array('user_id' => $user_id,
																'transaction_id' => $payment_data['transaction_id'],
																'amount' => floatval($payment_data['total_amount']),
																'brain_profile_id' => $brain_profile_id
																);
											
											$pay_responseArr = $this->braintree_model->make_wallet_payment_for_rest($paymentData,$nonce,$supportedCurrency);
											$pay_chk = TRUE;
										}
									}
								}
							}else{
								$pay_chk = TRUE;
								$pay_responseArr = $this->braintree_model->makeinitialwalletpayment($payment_data,$nonce,$supportedCurrency);
							}
							
							if($pay_chk == TRUE){
								
								
								$pay_response['status'] = (string)$pay_responseArr['status'];
															
								if($pay_response['status']==1){
								
									$paymentData = array('user_id' => $user_id, 
														'transaction_id' => $transaction_id, 
														'payType' => 'BrainTree', 
														'trans_id' => $pay_responseArr['txnId']);
									$wallet_values = $this->wallet_recharge_success($paymentData);
									
									$avail_amount = $wallet_values['avail_amount'];
									
									$responseArr['current_amount'] = (string)round($avail_amount,2);
									$responseArr['status'] = '1';
									$responseArr['response'] = 'You wallet has been recharged successfully';
									
								}else{
									$responseArr['response'] = $pay_responseArr['error_message'];
								}
							}else{
								$responseArr['response'] = 'Sorry, Cannot process you request';
							}
						} else {
							$responseArr['response'] = 'Sorry, Cannot process you request';
						}	
					} else {
						$responseArr['response'] = 'Sorry, Cannot process you request';
					}
				} else {
					$responseArr['response'] = 'Sorry, Cannot process you request';
				}
			} else {
				$responseArr['response'] = 'Sorry, Cannot process you request';
			}
		} catch (MongoException $ex) {
			$responseArr['response'] = 'Error in connection';
        }
		
		$json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
	
	public function setDefaultCreditCard($token) {
		$result = $this->braintree_model->setDefaultPaymentMethod($token);
		if ($result['status']) {
			$returnArr['status'] = true;
			$returnArr['response'] = 'Card has been set as default';
		}
		else {
			$returnArr['status'] = false;
			$returnArr['response'] = $result['message'];
		}
		return $returnArr;
	}

	public function deletePaymentMethod() {
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		try {
			$token = $this->input->post('token');

			if ($token == '') {
				$returnArr['response'] = $this->format_string('Token Missing','some_parameters_missing');
			} else {
				$result = $this->braintree_model->deletePaymentMethod($token);
				if ($result['status']) {
					$returnArr['response'] = 'Card has been deleted';
					$returnArr['status'] = '1';
				}
				else {
					$returnArr['response'] = $result['message'];
				}
			}
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}

	public function update_payment_method() {
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		try {
			$token = $this->input->post('token');
			$cardData = array(
				'expirationMonth' => $this->input->post('expiration_month'),
				'expirationYear' => $this->input->post('expiration_year'),
				'cvv' => $this->input->post('cvv'),
			);

			if ($token == '') {
				$returnArr['response'] = $this->format_string('Token Missing','some_parameters_missing');
			} else {
				$updateResult = $this->braintree_model->update_payment_method($token, $cardData);
				if ($updateResult['status']) {
					$returnArr['response'] = 'Card has been updated';
					$returnArr['status'] = '1';
				}
				else {
					$returnArr['response'] = $updateResult['message'];
				}
			}
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}

	public function setDefaultPaymentMethod() {
		$returnArr['status'] = '0';
		$returnArr['response'] ='';
		try {
			$user_id = $this->input->post('user_id');
			$payment_method = $this->input->post('payment_method');
			$card_token = $this->input->post('card_token');

			if ($user_id == '' || $payment_method == '') {
				$returnArr['response'] = $this->format_string('Some parameters are missing','some_parameters_missing');
			} else {
				$shouldUpdateDb = true;
				if ($payment_method == 'card') {
				 	if (!isset($card_token) || $card_token == '') {
						$shouldUpdateDb = false;
						$returnArr['response'] = $this->format_string('Card Token is missing');
					}
					else {
						$cardDefaultResult = $this->setDefaultCreditCard($card_token);
						$shouldUpdateDb = $cardDefaultResult['status'];
						if ($cardDefaultResult['status'] === false) {
							$returnArr['response'] = $this->format_string($cardDefaultResult['response']);
						}
					}
				}
				if ($shouldUpdateDb) {
					$user_data = array('default_payment_method' => (string) $payment_method);
					$updateResult = $this->braintree_model->update_details(USERS, $user_data, array('_id'=>new \MongoId($user_id)));
					if ($updateResult) {
						$returnArr['status'] = '1';
						$returnArr['response'] = $this->format_string( $payment_method . ' has been set as the default method of payment');
					}
				}
			}
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}

	public function getCardDetails() 
	{
		$returnArr['status'] = '0';
		$returnArr['response'] ='';

		$card_token = $this->input->post('card_token');

		if (!empty($card_token)) {
			$cardDetails = $this->braintree_model->getCardDetails($card_token);
			if ($cardDetails['status'] == '1') {
				$returnArr = $cardDetails;
			}
			else {
				$returnArr['response'] = $cardDetails['message'];
			}
		}
		else {
			$returnArr['response'] = $this->format_string('Card Token is missing');
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
	}
}

/* End of file braintree_api.php */
/* Location: ./application/controllers/api_v3/braintree_api.php */
