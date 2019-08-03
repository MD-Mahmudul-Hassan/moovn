<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
*
* This model contains all functions related to Braintree
* @author Casperon
*
*/

class Braintree_model extends My_Model {
    public $returnArr_status = '0';
    public $returnArr_response = '';
    public function __construct() {
        parent::__construct();
        $this->load->library('Braintree_lib','braintree_lib');
        
    }
    
    public function getallcustomer(){
        $retArr = array('status'=>0,'customers'=>array());
        $customers = Braintree_Customer::all();
        $customersArr = $customers->_ids;
        if(!empty($customersArr)){
            $retArr['status'] = 1;
            $retArr['customers'] = $customersArr;
        }
        return $retArr;
    }
    
    public function addCardForExistingCustomer($user, $nonce) 
    {
        try {
            $result = Braintree_PaymentMethod::create(
                [
                    'customerId' => $user->brain_profile_id,
                    'paymentMethodNonce' => $nonce
                ]
            );
            if ($result->success) {
                $retArr['status'] = true;
            } else {
                $retArr['status'] = false;
                $retArr['error_message'] = $result->_attributes['message'];
            }
        } catch (Braintree_Exception_Authentication $e) {
            $retArr['status'] = false;
            $retArr['error_message'] =  'Authentication Error. Contact Support Team';
        } catch (Braintree_Exception_Transaction $e) {
            $retArr['status'] = false;
            $retArr['error_message'] =  $e->getMessage();
        }
        return $retArr;
    }

    public function addCardForNewCustomer($user, $nonce) 
    {
        try {
            $result = Braintree_Customer::create(
                [
                    'firstName' => $user->user_name,
                    'email' => $user->email,
                    'phone' => $user->phone_number,
                    'paymentMethodNonce' => $nonce
                ]
            );
            if ($result->success) {
                $retArr['status'] = true;
                $retArr['customer_id'] = $result->customer->id;
                $retArr['token'] = $result->customer->paymentMethods[0]->token;
            } else {
                $retArr['status'] = false;
                $retArr['error_message'] = $result->_attributes['message'];
            }
        } catch (Braintree_Exception_Authentication $e) {
            $retArr['status'] = false;
            $retArr['error_message'] =  'Authentication Error. Contact Support Team';
        } catch (Braintree_Exception_Transaction $e) {
            $retArr['status'] = false;
            $retArr['error_message'] =  $e->getMessage();
        }
        return $retArr;
    }

    public function addCard($user, $nonce)
    {
        $retArr = array('error_message'=>'', 'profile_id' => 0);

        if (isset($user->brain_profile_id) 
            && $user->brain_profile_id != ''
        ) {
            $braintreeCustomer = $this->getcustomer($user->brain_profile_id);
            if ($braintreeCustomer['status'] == '1') {
                $retArr = $this->addCardForExistingCustomer($user, $nonce);
            } else {
                $retArr = $this->addCardForNewCustomer($user, $nonce);
            }
        } else {
            $retArr = $this->addCardForNewCustomer($user, $nonce);
        }
        return $retArr;
    }
    
    public function createcustomerprofile($user_id='',$cardArr = array(),$nonce=''){
        $retArr = array('error_message'=>'','profile_id'=>0);
        if($user_id!=''){
            if($nonce==''){
                $payment['number'] = $cardArr['card_number'];
                $payment['exp_year'] = $cardArr['exp_year'];
                
                $exp_month = intval($cardArr['exp_month']);
                if($exp_month<10){
                    $exp_month = '0'.ltrim($cardArr['exp_month'],0);
                }else{
                    $exp_month = (string)$cardArr['exp_month'];
                }
                $payment['exp_month'] = (string)$exp_month;
                $payment['expirationDate'] = $exp_month.'/'.$cardArr['exp_year'];
                $payment['cvv'] = $cardArr['cvc_number'];
            }
            
            $this->cimongo->select();
            $this->cimongo->where(array('_id'=>new \MongoId($user_id)));
            $userValues = $this->cimongo->get(USERS);
            if($userValues->num_rows()>0){
                $isConnected = '0';
                if(isset($userValues->row()->brain_profile_id)){
                    if($userValues->row()->brain_profile_id!=''){
                        $respose = $this->getcustomer($userValues->row()->brain_profile_id);
                        if(!empty($respose)){
                            if($respose['status']=='1'){
                                $isConnected = '1';
                            }
                        }
                    }
                }else{
                    $isConnected = '0';
                }
                if($isConnected=='0'){
                    $customerId = time();
                    $user_code_data = array('user_code'=>(string)$customerId);              
                    $this->update_details(USERS, $user_code_data,array('_id'=>new \MongoId($user_id)));
                    
                    $profileInfo['customer_name']= $userValues->row()->user_name;
                    $profileInfo['customer_email']= $userValues->row()->email;
                    $profileInfo['customer_phone']= $userValues->row()->country_code.$userValues->row()->phone_number;
                    $profileInfo['customerId']= (string)$customerId;                    
                    
                    
                    if($nonce==''){
                        $creditCard = array('cardholderName'=> $profileInfo['customer_name'],
                                            'number' => $payment['number'],
                                            'expirationMonth' => $payment['exp_month'],
                                            'expirationYear' => $payment['exp_year'],
                                            'cvv' => $payment['cvv']
                                        );
                    }
                    try {
                        if($nonce!=''){
                            $result = Braintree_Customer::create(array(
                                                                    'id' => $profileInfo['customerId'],
                                                                    'firstName' => $profileInfo['customer_name'],
                                                                    'email' => $profileInfo['customer_email'],
                                                                    'phone' => $profileInfo['customer_phone'],
                                                                    'paymentMethodNonce' => $nonce
                                                                    )
                                                                );
                        }else{
                            $result = Braintree_Customer::create(array(
                                                                    'id' => $profileInfo['customerId'],
                                                                    'firstName' => $profileInfo['customer_name'],
                                                                    'email' => $profileInfo['customer_email'],
                                                                    'phone' => $profileInfo['customer_phone'],
                                                                    'creditCard' => $creditCard
                                                                    )
                                                                );

                        }
                        if($result->success){
                            $customer_id = $result->customer->id;
                            $retArr['profile_id'] = $customer_id;
                        }else{
                            $retArr['error_message'] = $result->_attributes['message'];
                        }
                    } catch (Braintree_Exception_Authentication $e) {
                        $retArr['error_message'] =  'Authentication Error. Contact Support Team';
                    }catch (Braintree_Exception_Transaction $e) {
                        $retArr['error_message'] =  $e->getMessage();
                    }
                }else{
                    $retArr['error_message'] = 'Already Your account connected';
                }
            }
        }
        return $retArr;
    }

    public function delete_customer_profile($customer_id=''){
        $retArr = array('error_message'=>'','status'=>0);
        if($customer_id!=''){
            try{
                $result = Braintree_Customer::delete($customer_id);
                if($result->success){
                    $retArr['status'] = 1;
                }else{
                    $retArr['error_message'] = $result->_attributes['message'];
                }
            }catch(Braintree_Exception_NotFound $e){
                $retArr['error_message'] =  $e->getMessage();
            }
        }else{
            $retArr['error_message'] =  'Expected customer id is Missing';
        }
        return $retArr;
    }
    
    public function getcustomerprofiles(){
        $retArr = array('status'=>'0','response'=>'');
        $profileList = $this->authorizecimlib->get_customer_ids();
        if(empty($profileList)){
            $retArr['response'] = $this->authorizecimlib->get_error_msg();
        }       
        $this->authorizecimlib->clear_data();
        
        if(!empty($profileList)){
            $profilesArr = array();
            $profilesArr = $profileList;
            $retArr['status'] = '1';
            $retArr['response'] = $profilesArr;
        }else{
            $retArr['response'] = 'No profiles are found';
        }
        return $retArr;
    }
    
    public function createcustomerpaymentprofile($customer_profile_id ='',$cardArr = array(),$profileArr = array()){
        $retArr = array('error_message'=>'','payment_profile_id'=>0);
        $payment['profileid'] = $customer_profile_id;
        $payment['number'] = $cardArr['card_number'];
        $payment['exp_year'] = $cardArr['exp_year'];
        
        $exp_month = intval($cardArr['exp_month']);
        if($exp_month<10){
            $exp_month = '0'.ltrim($cardArr['exp_month'],0);
        }else{
            $exp_month = (string)$cardArr['exp_month'];
        }
        $payment['expirationDate'] = $exp_month.'/'.$cardArr['exp_year'];
        $payment['cvv'] = $cardArr['cvc_number'];
        
        try{
            try{
                $result = Braintree_PaymentMethod::create([
                    'customerId' => $customer_profile_id,
                    'creditCard' => array(
                                         'number' => (string)$payment['number'],
                                         'expirationMonth' => (string)$exp_month,
                                         'expirationYear' => (string)$payment['exp_year'],
                                         'cvv' => (string)$payment['cvv']
                                    ),
                    'options'    => array(
                                          'failOnDuplicatePaymentMethod'    => true,
                                          'makeDefault'    => true,
                                          'verifyCard'    => true,
                                          'storeInVault'    => true
                                        )
                    #'paymentMethodNonce' => nonceFromTheClient
                ]);
            }catch(Braintree_Exception_Unexpected $e){
                #var_dump($e);
            }
            
            if($result->success){
                $payment_profile_id = '';
                $retArr['payment_profile_id'] = $payment_profile_id;
            }else{
                $retArr['error_message'] = $result->_attributes['message'];
            }
        }catch(Braintree_Exception_NotFound $e){
            $retArr['error_message'] =  $e->getMessage();
        }
        return $retArr;
    }
    
    public function getcustomer($profileid=''){
        $retArr = array('error_message'=>'','token'=>'0','profile_id'=>'0','status'=>'0','cards'=>array());
        if($profileid!=''){
            try {
                $result = Braintree_Customer::find($profileid); 
                if(!empty($result)){
                    $retArr['status'] = '1';
                    $customer_id = $result->id;
                    $token = $result->creditCards;
                    $retArr['token'] = $token;
                    $retArr['cards'] = $result->creditCards;
                    $retArr['profile_id'] = $customer_id;
                }else{
                    $retArr['error_message'] = 'Your account not connected';
                }
            } catch (Braintree_Exception_NotFound $e) {
                $retArr['error_message'] =  $e->getMessage();
            }
        }
        return $retArr;
    }
    
    
    public function makewalletpayment($paymentData=array(),$nonce='',$supportedCurrency=''){
        $retArr = array('status'=>0,'error_message'=>'','txnId'=>'0');
        if(!empty($paymentData)){
            $amount = $paymentData['amount'];
            $brain_profile_id = $paymentData['brain_profile_id'];
            $transaction_id = $paymentData['transaction_id']; 
            
            $orderId = $transaction_id;
            try{
                
                if($nonce!='' && $supportedCurrency!='') {
                
                    $result = Braintree_Transaction::sale(
                                                          [
                                                            'customerId' => $brain_profile_id,
                                                            'amount' => $amount,
                                                            'orderId' => (string)$orderId,
                                                            'merchantAccountId' => $supportedCurrency,
                                                            'paymentMethodNonce' => $nonce,
                                                            'options' => [
                                                                'submitForSettlement' => True
                                                              ]
                                                          ]
                                                        );
                
                }else if($nonce!='' && $supportedCurrency==''){
                
                    $result = Braintree_Transaction::sale(
                                                          [
                                                            'customerId' => $brain_profile_id,
                                                            'amount' => $amount,
                                                            'orderId' => (string)$orderId,
                                                            'paymentMethodNonce' => $nonce,
                                                            'options' => [
                                                                'submitForSettlement' => True
                                                              ]
                                                          ]
                                                        );
                }else if($supportedCurrency!=''){
                    $result = Braintree_Transaction::sale(
                                                      [
                                                        'customerId' => $brain_profile_id,
                                                        'amount' => $amount,
                                                        'orderId' => (string)$orderId,
                                                        'merchantAccountId' => $supportedCurrency,
                                                        'options' => [
                                                            'submitForSettlement' => True
                                                          ]
                                                      ]
                                                    );
                } else {
                        $result = Braintree_Transaction::sale(
                                                      [
                                                        'customerId' => $brain_profile_id,
                                                        'amount' => $amount,
                                                        'orderId' => (string)$orderId,
                                                        'options' => [
                                                            'submitForSettlement' => True
                                                          ]
                                                      ]
                                                    );
                }
                if($result->success){
                    $retArr['status'] = 1;
                    $retArr['txnId'] = $orderId;
                }else{
                    $retArr['error_message'] = $result->message;
                }
            }catch (Braintree_Exception_Authentication $e) {
                $retArr['error_message'] =  'Authentication Error. Contact Support Team';
            }catch (Braintree_Exception_Transaction $e) {
                $retArr['error_message'] =  $e->getMessage();
            }
        }
        return $retArr;
    }
    
    public function makepayment($paymentData=array()){
        $retArr = array('status'=>0,'error_message'=>'','txnId'=>'0');
        if(!empty($paymentData)){
            $amount = $paymentData['amount'];
            $brain_profile_id = $paymentData['brain_profile_id'];
            $ride_id = $paymentData['ride_id']; 
            
            $orderId = $ride_id.time();
            try{
                $result = Braintree_Transaction::sale(
                                                      [
                                                        'customerId' => $brain_profile_id,
                                                        'amount' => $amount,
                                                        'orderId' => (string)$orderId,
                                                        'options' => [
                                                            'submitForSettlement' => True
                                                          ]
                                                      ]
                                                    );
                                        
                if($result->success){
                    $retArr['status'] = 1;
                    $retArr['txnId'] = $orderId;
                }else{
                    // Find out if it was approved or not.
                    $retArr['error_message'] = $result->_attributes['message'];
                }
            }catch (Braintree_Exception_Authentication $e) {
                $retArr['error_message'] =  'Authentication Error. Contact Support Team';
            }catch (Braintree_Exception_Transaction $e) {
                $retArr['error_message'] =  $e->getMessage();
            }
        }
        return $retArr;
    }
    
    
    public function makeinitialwalletpayment($paymentData = array(),$nonce='',$supportedCurrency=''){
        $retsArr = array('status'=>0,'error_message'=>'','txnId'=>'0');
        if(!empty($paymentData)){   
            $paymentData['amount'] = $paymentData['total_amount'];
            $cardArr = array();
            if(isset($paymentData['card_number'])){
                $cardArr = array('card_number'=>$paymentData['card_number'],
                                'exp_month'=>$paymentData['exp_month'],
                                'exp_year'=>$paymentData['exp_year'],
                                'cvc_number'=>$paymentData['cvv']
                                );
            }
            $profile_info = $this->createcustomerprofile($paymentData['user_id'],$cardArr,$nonce);
        
            if($profile_info['profile_id']!=0){
                $brain_profile_id = $profile_info['profile_id'];        
                $paymentData['brain_profile_id'] = $brain_profile_id;
                $pay_responseArr = $this->makewalletpayment($paymentData,'',$supportedCurrency);
                if($pay_responseArr['status']==1){
                    $user_data = array('brain_profile_id'=>(string)$brain_profile_id);                      
                    $this->update_details(USERS, $user_data,array('_id'=>new \MongoId($paymentData['user_id'])));
                    
                    $retsArr['status'] = 1;
                    $retsArr['txnId'] = $pay_responseArr['txnId'];
                }else{
                    $this->delete_customer_profile($brain_profile_id);
                    $retsArr['error_message'] = $payment_profile_info['error_message'];
                }               
            }else{
                $retsArr['error_message'] = $profile_info['error_message'];
            }
        }
        return $retsArr;
    }
    
    public function makewalletpayment_dropui($paymentData = array(),$nonce=''){
        $retsArr = array('status'=>0,'error_message'=>'','txnId'=>'0');
        if(!empty($paymentData)){   
            $paymentData['amount'] = $paymentData['total_amount'];
            $paymentData['transaction_id'] = $paymentData['total_amount'];
            if($nonce==''){
            $cardArr = array('card_number'=>$paymentData['card_number'],
                            'exp_month'=>$paymentData['exp_month'],
                            'exp_year'=>$paymentData['exp_year'],
                            'cvc_number'=>$paymentData['cvv']
                            );
            }else{
                $cardArr = array();
            }
            $profile_info = $this->createcustomerprofile($paymentData['user_id'],$cardArr,$nonce);
            
            if($profile_info['profile_id']!=0){
                $brain_profile_id = $profile_info['profile_id'];        
                $paymentData['brain_profile_id'] = $brain_profile_id;
                $pay_responseArr = $this->makewalletpayment($paymentData);
                
                if($pay_responseArr['status']==1){
                    $user_data = array('brain_profile_id'=>(string)$brain_profile_id);                      
                    $this->update_details(USERS, $user_data,array('_id'=>new \MongoId($paymentData['user_id'])));
                    
                    $retsArr['status'] = 1;
                    $retsArr['txnId'] = $pay_responseArr['txnId'];
                }else{
                    $this->delete_customer_profile($brain_profile_id);
                    $retsArr['error_message'] = $pay_responseArr['error_message'];
                }               
            }else{
                $retsArr['error_message'] = $profile_info['error_message'];
            }
        }
        return $retsArr;
    }
    
    public function getcustomerpaymentmethods($profileid=''){
        $retArr = array('error_message'=>'','profile_id'=>'0','status'=>'0','methods'=>array());
        if($profileid!=''){
            try {
                $result = Braintree_Customer::find($profileid); 
                
                if(!empty($result)){
                    $retArr['status'] = '1';
                    $customer_id = $result->id;
                    $methods = array();
                    
                    $payPal = array();
                    if(isset($result->paypalAccounts)){
                        $payPal = $result->paypalAccounts;
                        foreach($payPal as $pp){
                            $email = $pp->email;                            
                            $imageUrl = $pp->imageUrl;
                            
                            $imageUrl = base_url().'images/payments/paypal.png';
                            
                            $methods[] = array('image'=>$imageUrl,'type'=>'PayPal','identity'=>(string)$email);
                        }
                    }
                    
                    $creditCards = array();
                    if(isset($result->creditCards)){
                        $creditCards = $result->creditCards;
                        foreach($creditCards as $cc){
                            $cardType = $cc->cardType;
                            $last4 = $cc->last4;                            
                            $imageUrl = $cc->imageUrl;
                            
                            switch($cardType){
                                case 'Visa':
                                    $imageUrl = base_url().'images/payments/visa.png';
                                break;
                                case 'MasterCard':
                                    $imageUrl = base_url().'images/payments/mastercard.png';
                                break;
                                case 'American Express':
                                    $imageUrl = base_url().'images/payments/american_express.png';
                                break;
                                case 'Discover':
                                    $imageUrl = base_url().'images/payments/discover.png';
                                break;
                                case 'JCB':
                                    $imageUrl = base_url().'images/payments/jcb.png';
                                break;
                            }
                            $methods[] = array(
                                'image'=>$imageUrl,
                                'type'=>$cardType,
                                'identity'=>'ending in '.$last4,
                                'token' => $cc->token,
                                'default' => (bool) $cc->default
                            );
                        }
                    }
                    
                    $retArr['methods'] = $methods;
                    $retArr['profile_id'] = $customer_id;
                } else {
                    $retArr['error_message'] = 'Your account not connected';
                }
            } catch (Braintree_Exception_NotFound $e) {
                $retArr['error_message'] =  $e->getMessage();
            }
        }
        return $retArr;
    }
    
    
    
    public function makecancellationpayment($paymentData=array()){
        $retArr = array('status'=>0,'error_message'=>'','txnId'=>'0');
        if(!empty($paymentData)){
            $amount = $paymentData['amount'];
            $brain_profile_id = $paymentData['brain_profile_id'];
            $transaction_id = $paymentData['transaction_id']; 
            
            $orderId = $transaction_id;
            try{
                    $result = Braintree_Transaction::sale(
                                                      [
                                                        'customerId' => $brain_profile_id,
                                                        'amount' => $amount,
                                                        'orderId' => (string)$orderId,
                                                        'options' => [
                                                            'submitForSettlement' => True
                                                          ]
                                                      ]
                                                    );
                if($result->success){
                    $retArr['status'] = 1;
                    $retArr['txnId'] = $orderId;
                }else{
                    $retArr['error_message'] = $result->message;
                }
            }catch (Braintree_Exception_Authentication $e) {
                $retArr['error_message'] =  'Authentication Error. Contact Support Team';
            }catch (Braintree_Exception_Transaction $e) {
                $retArr['error_message'] =  $e->getMessage();
            }
        }
        return $retArr;
    }
    
    public function make_trip_payment($paymentData=array()) 
    {
        $retArr = array('status'=>'0','response'=>'','txnId'=>'0');
        $is_completed = 'No';
        $gateway_pay = 'No';
        if (!empty($paymentData)) {
        
            $ride_id = '';
            if (array_key_exists('ride_id',$paymentData)) {
                $ride_id = $paymentData['ride_id']; 
            } else {
                $ride_id = $this->input->post('ride_id');
            }
            
            $checkRide = $this->get_all_details(RIDES, array('ride_id' => $ride_id));
            
            if ($checkRide->num_rows() == 1) {
                $my_ci =& get_instance(); 
                $pay_amount = 0; $rc_pay_amount = 0;
                if ($is_completed == 'No') {
                    $gateway_pay = 'Yes';
                    $nonce = '';
                    if (array_key_exists('payment_method_nonce', $paymentData)) {
                        $nonce = $paymentData['payment_method_nonce']; 
                    } else {
                        $nonce = $checkRide->row()->payment_method_nonce;
                    }
                    
                    $brain_profile_id = '';
                    if (array_key_exists('brain_profile_id', $paymentData)) {
                        $brain_profile_id = $paymentData['brain_profile_id']; 
                    } else {
                        $user_id = $checkRide->row()->user['id'];
                        $userVal = $this->get_all_details(USERS, array('_id' => new \MongoId($user_id)));
                        if ($userVal->num_rows() > 0) {
                            if (isset($userVal->row()->brain_profile_id)) {
                                if ($userVal->row()->brain_profile_id!='') {
                                    $brain_profile_id = $userVal->row()->brain_profile_id;
                                }
                            }
                        }
                    }
                    if ($brain_profile_id == '') {
                        $profile_info = $this->createcustomerprofile($user_id, array(), $nonce);
                        if ($profile_info['profile_id']!= 0) {
                            $brain_profile_id = $profile_info['profile_id'];
                            $user_data = array('brain_profile_id'=>(string)$brain_profile_id);
                            $this->update_details(USERS, $user_data,array('_id'=>new \MongoId($user_id)));
                        }
                    }

                    if ($brain_profile_id != '') {
                        if ($checkRide->row()->ride_status=='Finished') { #Booked - Finished 
                            if ($nonce != '') {
                                $grand_fare = $checkRide->row()->total['grand_fare'];
                                $paid_amount = $checkRide->row()->total['paid_amount'];
                                $wallet_amount = $checkRide->row()->total['wallet_usage'];
                                $tips_amt = 0.00; 
                                if (isset($checkRide->row()->total['tips_amount'])) {
                                    if ($checkRide->row()->total['tips_amount'] > 0) {
                                        $tips_amt = round(($checkRide->row()->total['tips_amount']),2);
                                        
                                    }
                                }
                                $grand_fare = $grand_fare + $tips_amt;
                                $pay_amount = $grand_fare - ($paid_amount + $wallet_amount);
                                $org_pay_amount = $pay_amount;
                                $original_currency = $this->config->item('currency_code');
                                $currency=$checkRide->row()->currency;
                                $supportedCurrency = $my_ci->get_braintree_merchant_currency($currency);
                                
                                if (!$supportedCurrency) {
                                    $currencyval = $this->braintree_model->get_currency_value(round($pay_amount, 2), $currency, $original_currency);
                                    if (!empty($currencyval)) {
                                        $pay_amount = $currencyval['CurrencyVal'];
                                    }
                                }
                                
                                try{
                                    $result = Braintree_Transaction::sale(
                                        [
                                            'customerId' => $brain_profile_id,
                                            'amount' => $pay_amount,
                                            'orderId' => (string)$ride_id,
                                            'options' => [
                                                'submitForSettlement' => True
                                            ]
                                        ]
                                    );
                                    if ($result->success) {
                                        $retArr['status'] = "1";
                                        $retArr['txnId'] = $ride_id;
                                        $is_completed = 'Yes';
                                        $retArr['response'] = 'Payment Completed Successfully';
                                    } else {
                                        $retArr['response'] = $result->message;
                                    }
                                }catch (Braintree_Exception_Authentication $e) {
                                    $retArr['response'] =  'Authentication Error. Contact Support Team';
                                }catch (Braintree_Exception_Transaction $e) {
                                    $retArr['response'] =  $e->getMessage();
                                }
                            } else {
                                $retArr['response'] =  'Cannot found the nonce key.';
                            }
                        } else {
                            $retArr['response'] =  'Cannot make the payment right now.';
                        }
                    } else {
                        $retArr['response'] =  'Cannot authenticate your payment account';
                    }                
                }
            } else {
                $retArr['response'] =  'Cannot find the trip information.';
            }
        }
        
        if ($is_completed == 'Yes') {
            $rideinfoUpdated=$this->get_all_details(RIDES, array('ride_id' => $ride_id));
            ### Update into the ride and driver collection ###
            
            $wallet_usage_status = 'No';
            if (isset($rideinfoUpdated->row()->total['wallet_usage'])) {
                if ($rideinfoUpdated->row()->total['wallet_usage'] > 0) {
                    $wallet_usage_status = 'Yes';
                }
            }            
            
            if ($rideinfoUpdated->row()->pay_status == 'Pending' || $rideinfoUpdated->row()->pay_status == 'Processing' || ($rideinfoUpdated->row()->pay_status == 'Paid') && $wallet_usage_status == 'Yes') {                
                $payment_method = 'Gateway';
                if (isset($rideinfoUpdated->row()->total['wallet_usage'])) {
                    if ($rideinfoUpdated->row()->total['wallet_usage'] > 0) {
                        if ($gateway_pay == 'Yes') {
                            $payment_method = 'Wallet_Gateway';
                        } else {
                            $payment_method = 'Wallet';
                        }
                    }
                }
                
                $trans_id=$ride_id;
                $type='Card';
                
                $pay_summary = array('type' => $payment_method);
                $paymentInfo = array('ride_status' => 'Completed',
                    'pay_status' => 'Paid',
                    'total.paid_amount' => round(floatval($org_pay_amount), 2),
                    'pay_summary' => $pay_summary
                );
                
                if ($gateway_pay == 'Yes') {
                    $paymentInfo['history.pay_by_gateway_time'] = new \MongoDate(time());
                }
                
                $this->update_details(RIDES, $paymentInfo, array('ride_id' => $ride_id));
                if (ENABLE_DRIVER_OUSTANDING_UPDATE) {
                    $this->load->model('driver_model');
                    $driver = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($rideinfoUpdated->row()->driver['id'])));
                    $this->driver_model->update_outstanding_amount($driver->row(), $rideinfoUpdated->row(), $payment_method);
                }
                $updateUser = array('pending_payment' => 'false');
                $this->update_details(USERS, $updateUser, array('_id' => new \MongoId($checkRide->row()->user['id'])));

                /* Update Stats Starts */
                $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
                $field = array('ride_completed.hour_' . date('H') => 1, 'ride_completed.count' => 1);
                $this->update_stats(array('day_hour' => $current_date), $field, 1);
                /* Update Stats End */
                $transactionArr = array('type' => $type,
                    'amount' => floatval($org_pay_amount),
                    'trans_id' => $trans_id,
                    'trans_date' => new \MongoDate(time())
                );
                $this->simple_push(PAYMENTS, array('ride_id' => $ride_id), array('transactions' => $transactionArr));
                $driver_id = $checkRide->row()->driver['id'];                        
                        
                $driverVal = $this->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'fcm_token', 'device_type', 'push_notification'));
                
                $message = 'payment completed';
                $options = array('ride_id' => (string) $ride_id, 'driver_id' => $driver_id);

                if (isset($driverVal->row()->fcm_token)) {
                    $my_ci->notify($driverVal->row()->fcm_token, $message, 'payment_paid', $options, $driverVal->row()->device_type);
                } else {
                     if ($driverVal->num_rows() > 0) {
                        if (isset($driverVal->row()->push_notification)) {
                            if ($driverVal->row()->push_notification != '') {
                                if (isset($driverVal->row()->push_notification['type'])) {
                                    if ($driverVal->row()->push_notification['type'] == 'ANDROID') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {                                            
                                                $my_ci->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'ANDROID', $options, 'DRIVER');
                                            }
                                        }
                                    }
                                    if ($driverVal->row()->push_notification['type'] == 'IOS') {
                                        if (isset($driverVal->row()->push_notification['key'])) {
                                            if ($driverVal->row()->push_notification['key'] != '') {
                                                $my_ci->sendPushNotification($driverVal->row()->push_notification['key'], $message, 'payment_paid', 'IOS', $options, 'DRIVER');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                $retArr['status'] = "1";
                $retArr['txnId'] = $ride_id;
                $retArr['response'] = 'Payment Completed Successfully';
                $this->update_ride_amounts($ride_id);
                    $fields = array(
                    'ride_id' => (string) $ride_id
                );
                $url = base_url().'prepare-invoice';
                $this->load->library('curl');
                $output = $this->curl->simple_post($url, $fields);
            }
        } 
        return $retArr;
    }
    
    public function make_wallet_payment_for_rest($paymentData=array(),$nonce='',$supportedCurrency=''){
        $retArr = array('status'=>'0','error_message'=>'','txnId'=>'0');
        if(!empty($paymentData)){
            $amount = $paymentData['amount'];
            $brain_profile_id = $paymentData['brain_profile_id'];
            $transaction_id = $paymentData['transaction_id']; 
            
            try{
                    
                $result = Braintree_Transaction::sale(
                    [
                        'customerId' => $brain_profile_id,
                        'amount' => $amount,
                        'orderId' => (string)$transaction_id,
                        'paymentMethodNonce' => $nonce,
                        'options' => [
                            'submitForSettlement' => True
                        ]
                    ]
                );

                if($result->success){
                    $retArr['status'] = 1;
                    $retArr['txnId'] = $transaction_id;
                }else{
                    
                    $retArr['error_message'] = $result->message;
                }
            }catch (Braintree_Exception_Authentication $e) {
                $retArr['error_message'] =  'Authentication Error. Contact Support Team';
            }catch (Braintree_Exception_Transaction $e) {
                $retArr['error_message'] =  $e->getMessage();
            } catch (Braintree_Exception $e) {
                $retArr['error_message'] =  $e->getMessage();
            } 
        }
        return $retArr;
    }

    public function setDefaultPaymentMethod($token) {
        try{
            $updateResult = Braintree_PaymentMethod::update(
                $token,
                [
                    'options' => [
                        'makeDefault' => true
                    ]
                ]
            );
            $response['status'] = true;
        } catch(Braintree_Exception_Unexpected $e) {
            $response['message'] =  $e->getMessage();
        } catch(Braintree_Exception_NotFound $e) {
            $response['message'] =  'Invalid Token';
        } 
        return $response;
    }

    public function deletePaymentMethod($token) {
        try{
            $deleteResult = Braintree_PaymentMethod::delete($token);
            if ($deleteResult->success) {
                $response['status'] = true;
            }
            else {
                $response['message'] =  $e->getMessage();
            }
        } catch(Braintree_Exception_Unexpected $e) {
            $response['message'] =  $e->getMessage();
        }
        return $response;
    }

    public function update_payment_method($token, $cardData) {
        try{
            $updateResult = Braintree_PaymentMethod::update(
                $token,
                [
                    'cvv' => $cardData['cvv'],
                    'expirationMonth' => $cardData['expirationMonth'],
                    'expirationYear' => $cardData['expirationYear'],
                ]
            );
            $response['status'] = true;
        } catch(Braintree_Exception_NotFound $e) {
            if (empty($e->getMessage())) {
                $response['message'] = 'Braintree_Exception_NotFound';
            }
            else {
                $response['message'] =  $e->getMessage();
            }
        } catch(Braintree_Exception_Unexpected $e) {
            $response['message'] =  $e->getMessage();
        } 
        return $response;
    }

    public function getCardDetails($token) {
        try{
            $cardDetails = Braintree_PaymentMethod::find($token);
            if ($cardDetails->_attributes) {
                $cardAttributes = $cardDetails->_attributes;
                $response['status'] = '1';
                $response['maskedNumber'] = $cardAttributes['maskedNumber'];
                $response['token'] = $cardAttributes['token'];
                $response['imageUrl'] = $cardAttributes['imageUrl'];
                $response['cardType'] = $cardAttributes['cardType'];
                $response['postalCode'] = $cardAttributes['billingAddress']->_attributes['postalCode'];
                $response['expirationMonth'] = $cardAttributes['expirationMonth'];
                $response['expirationYear'] = $cardAttributes['expirationYear'];
                $response['last4'] = $cardAttributes['last4'];
            }
            else {
                $response['message'] = 'Invalid Token';
            }
        } catch(Braintree_Exception_NotFound $e) {
            if (empty($e->getMessage())) {
                $response['message'] = 'Braintree_Exception_NotFound';
            }
            else {
                $response['message'] =  $e->getMessage();
            }
        } catch(Braintree_Exception_Unexpected $e) {
            $response['message'] =  $e->getMessage();
        } 
        return $response;
    }

    public function preAuthTransaction($trip_estimate, $payment_method_nonce) {
        $retArr = array('status' => 0, 'error_message' => '', 'txnId' => '0');
        try{
            $result = Braintree_Transaction::sale([
                'amount' => $trip_estimate,
                'paymentMethodNonce' => $payment_method_nonce,
                'options' => [
                    'submitForSettlement' => False
                ]
            ]);
            if($result->success){
                $retArr['status'] = 1;
                $retArr['txnId'] = $result->transaction->_attributes['id'];
            }else{
                $retArr['error_message'] = $result->message;
            }
        }catch (Braintree_Exception_Authentication $e) {
            $retArr['error_message'] =  'Authentication Error. Contact Support Team';
        }catch (Braintree_Exception_Transaction $e) {
            $retArr['error_message'] =  $e->getMessage();
        }
        return $retArr;
    }

    public function voidTransaction($transaction_id) {
        $retArr = array('status' => 0, 'error_message' => '', 'txnId' => '0');
        try{
            $result = Braintree_Transaction::void($transaction_id);
            if($result->success){
                $retArr['status'] = 1;
                $retArr['txnId'] = $result->transaction->_attributes['id'];
            }else{
                $retArr['error_message'] = $result->message;
            }
        }catch (Braintree_Exception_Authentication $e) {
            $retArr['error_message'] =  'Authentication Error. Contact Support Team';
        }catch (Braintree_Exception_Transaction $e) {
            $retArr['error_message'] =  $e->getMessage();
        }
        return $retArr;
    }
}
