<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * User related functions
 * @author Casperon
 *
 * */
class Mobile_payment extends MY_Controller {

    public $mobdata = array();

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('app_model');
        $this->load->model('rides_model');

        /* $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
          if(stripos($ua,'dectartaxiapp') === false) {
          show_404();
          } */

        if ($_GET['mobileId'] == '' && $_POST['mobileId'] == '') {
            $this->load->view('mobile/error.php', $this->mobdata);
            die;
        } else {
            if (isset($_GET['mobileId'])) {
                $mobileId = $_GET['mobileId'];
            } else {
                $mobileId = $_POST['mobileId'];
            }
            $mobileData = $this->app_model->get_all_details(MOBILE_PAYMENT, array('_id' => new \MongoId($mobileId)));
            if ($mobileData->num_rows() == 0) {
                $this->load->view('mobile/error.php', $this->mobdata);
                die;
            } else {
                $this->mobdata['mobileId'] = (string) $mobileData->row()->_id;
                $this->mobdata['user_id'] = $mobileData->row()->user_id;
                $this->mobdata['driver_id'] = $mobileData->row()->driver_id;
                $this->mobdata['ride_id'] = $mobileData->row()->ride_id;
                $this->mobdata['payment'] = $mobileData->row()->payment;
                $this->mobdata['payment_id'] = $mobileData->row()->payment_id;
                $this->mobdata['total_amount'] = $mobileData->row()->amount;
            }
        }
    }

    /**
     * 
     * Loading Credit Card Payment Form
     *
     * */
    public function credit_card_form() {
        $this->load->view('mobile/credit_card_payment.php', $this->mobdata);
    }

    /**
     * 
     * Payment Process using credit card
     *
     * */
    public function userPaymentCard() {
        $user_id = $this->mobdata['user_id'];
        $ride_id = $this->mobdata['ride_id'];

        $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
        if ($checkRide->num_rows() == 1) {
            $this->app_model->update_details(RIDES, array('pay_status' => 'Processing'), array('ride_id' => $ride_id));

            $tips_amt = 0.00;
            if (isset($checkRide->row()->total['tips_amount'])) {
                if ($checkRide->row()->total['tips_amount'] > 0) {
                    $tips_amt = $checkRide->row()->total['tips_amount'];
                }
            }

            $amount = $this->mobdata['total_amount'] + $tips_amt;
            /* define("AUTHORIZENET_API",$this->config->item('payment_0'));
              $Auth_Details=unserialize(AUTHORIZENET_API); */
            //Authorize.net Intergration
            $Auth_Details = $this->data['authorize_net_settings'];
            $Auth_Setting_Details = $Auth_Details['settings'];

            define("AUTHORIZENET_API_LOGIN_ID", $Auth_Setting_Details['Login_ID']);    // Add your API LOGIN ID
            define("AUTHORIZENET_TRANSACTION_KEY", $Auth_Setting_Details['Transaction_Key']); // Add your API transaction key
            define("API_MODE", $Auth_Setting_Details['mode']);


            if (API_MODE == 'sandbox') {
                define("AUTHORIZENET_SANDBOX", true); // Set to false to test against production
            } else {
                define("AUTHORIZENET_SANDBOX", false);
            }
            define("TEST_REQUEST", "FALSE");
            require_once './authorize/AuthorizeNet.php';

            $transaction = new AuthorizeNetAIM;
            $transaction->setSandbox(AUTHORIZENET_SANDBOX);
            $transaction->setFields(array('amount' => $amount,
                'card_num' => $this->input->post('cardNumber'),
                'exp_date' => $this->input->post('CCExpDay') . '/' . $this->input->post('CCExpMnth'),
                'first_name' => $checkRide->row()->user['name'],
                'last_name' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'country' => '',
                'phone' => $checkRide->row()->user['phone'],
                'email' => $checkRide->row()->user['email'],
                'card_code' => $this->input->post('creditCardIdentifier')
                    )
            );

            #echo '<pre>'; print_r($transaction);die;			
            $response = $transaction->authorizeAndCapture();

            if ($response->approved) {
                redirect('mobile/success/' . $user_id . '/' . $ride_id . '/credit-card/' . $response->transaction_id . '?mobileId=' . $this->mobdata['mobileId']);
            } else {
                redirect('mobile/failed/' . $response->response_reason_text . '?mobileId=' . $this->mobdata['mobileId']);
            }
        } else {
            redirect('mobile/payment/Cancel?mobileId=' . $this->mobdata['mobileId']);
        }
    }

    /**
     * 
     * Payment Process using Paypal
     *
     * */
    public function paypal_payment_process() {
        $user_id = $this->mobdata['user_id'];
        $ride_id = $this->mobdata['ride_id'];

        $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
        if ($checkRide->num_rows() == 1) {
            $this->app_model->update_details(RIDES, array('pay_status' => 'Processing'), array('ride_id' => $ride_id));

            $tips_amt = 0.00;
            if (isset($checkRide->row()->total['tips_amount'])) {
                if ($checkRide->row()->total['tips_amount'] > 0) {
                    $tips_amt = $checkRide->row()->total['tips_amount'];
                }
            }

            $amount = $this->mobdata['total_amount'] + $tips_amt;

            /* Paypal integration start */
            $this->load->library('paypal_class');

            define("PAYPAL_API", $this->config->item('payment_1'));
            $Paypal_Details = unserialize(PAYPAL_API);
            $Paypal_Setting_Details = $Paypal_Details['settings'];

            $paypalmode = $Paypal_Setting_Details['mode'];
            $paypalEmail = $Paypal_Setting_Details['merchant_email'];

            $item_name = $this->config->item('email_title') . ' payment ride : ' . $ride_id;

            $currency = $checkRide->row()->currency;
            $original_grand_fare = 0;
            $original_currency = 'USD';
            $currencyval = $this->app_model->get_currency_value(round($amount, 2), $currency, $original_currency);
            if (!empty($currencyval)) {
                $original_grand_fare = $currencyval['CurrencyVal'];
            }
            $totalAmount = $original_grand_fare;

            $quantity = 1;

            if ($paypalmode == 'sandbox') {
                $this->paypal_class->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
            } else {
                $this->paypal_class->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url
            }
            $this->paypal_class->add_field('currency_code', $original_currency);

            $this->paypal_class->add_field('business', $paypalEmail); // Business Email

            $this->paypal_class->add_field('return', base_url() . 'mobile/success/' . $user_id . '/' . $ride_id . '/paypal/?mobileId=' . $this->mobdata['mobileId']); // Return URL

            $this->paypal_class->add_field('cancel_return', base_url() . 'mobile/failed/?mobileId=' . $this->mobdata['mobileId']); // Cancel URL

            $this->paypal_class->add_field('notify_url', base_url() . 'paypal-payment-ipn'); // Notify url

            $this->paypal_class->add_field('custom', 'RidePayment|' . $user_id . '|' . $ride_id); // Custom Values			

            $this->paypal_class->add_field('item_name', $item_name); // Product Name

            $this->paypal_class->add_field('user_id', $user_id);

            $this->paypal_class->add_field('quantity', $quantity); // Quantity

            $this->paypal_class->add_field('amount', $totalAmount); // Price

            $this->paypal_class->submit_paypal_post();
        } else {
            redirect('mobile/payment/Cancel?mobileId=' . $this->mobdata['mobileId']);
        }
    }

    /**
     * 
     * Loads the stripe pay form for manual payment
     *
     * */
    public function stripe_payment_form() {
        $this->mobdata['auto_charge'] = $this->data['auto_charge'];
        $this->mobdata['stripe_settings'] = $this->data['stripe_settings'];
        $this->load->view('mobile/stripe_payment.php', $this->mobdata);
    }

    /**
     * 
     * Complete the payment process through stripe pay and manual payment
     *
     * */
    public function stripe_payment_process() {

        $user_id = $this->input->post('user_id');
        $total_amount = $this->input->post('total_amount');
        $ride_id = $this->input->post('transaction_id');
        $email = $this->input->post('stripeEmail');

        $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id, 'user.id' => $user_id));
        $this->app_model->update_details(RIDES, array('pay_status' => 'Processing'), array('ride_id' => $ride_id));


        if ($checkRide->num_rows() == 1) {

            $tips_amt = 0.00;
            if (isset($checkRide->row()->total['tips_amount'])) {
                if ($checkRide->row()->total['tips_amount'] > 0) {
                    $tips_amt = $checkRide->row()->total['tips_amount'];
                }
            }

            $total_amount = $total_amount + $tips_amt;

            $getUsrCond = array('_id' => new \MongoId($user_id));
            $get_user_info = $this->app_model->get_selected_fields(USERS, $getUsrCond, array('email', 'stripe_customer_id'));
            if ($email == '') {
                $email = $get_user_info->row()->email;
            }

            $stripe_customer_id = '';
            $auto_pay_status = 'No';

            require_once('./stripe/lib/Stripe.php');

            $stripe_settings = $this->data['stripe_settings'];
            $secret_key = $stripe_settings['settings']['secret_key'];
            $publishable_key = $stripe_settings['settings']['publishable_key'];

            $stripe = array(
                "secret_key" => $secret_key,
                "publishable_key" => $publishable_key
            );

            $product_description = ucfirst($this->config->item('email_title')) . ' money - Wallet Recharge ';

            /* Convert ride currency value to default currency  */
			$paymentCurr = $this->data['dcurrencyCode'];
			if(isset($checkRide->row()->currency)){
				$currency = $checkRide->row()->currency;
			}
			if($currency != $paymentCurr){
				$get_dcurrency=$this->app_model->get_currency_value($total_amount,$currency,$paymentCurr);
				$total_amount = $get_dcurrency['CurrencyVal'];
			}
			
			/************************************************/
			
			#echo '<pre>'; print_r($_POST);die;
            Stripe::setApiKey($secret_key);
            $token = $this->input->post('stripeToken');
            $amounts = $total_amount * 100;
            try {
                // Create a Customer

                $customer_id = $stripe_customer_id;
                if ($customer_id == '') {
                    $customer = Stripe_Customer::create(array(
                                "card" => $token,
                                "description" => $product_description,
                                "email" => $email)
                    );
                    $customer_id = $customer->id;
                }


                $this->app_model->update_details(USERS, array('stripe_customer_id' => $customer_id), $getUsrCond);


                // Charge the Customer instead of the card
                $charge = Stripe_Charge::create(array(
                            "amount" => $amounts, # amount in cents, again
                            "currency" => $paymentCurr,
                            "customer" => $customer_id,
                            "description" => $product_description)
                );
                redirect('mobile/success/' . $user_id . '/' . $ride_id . '/stripe/' . $charge['id'] . '?mobileId=' . $this->mobdata['mobileId']);
            } catch (Exception $e) {
                $error = $e->getMessage();
                if ($error == '') {
                    $error = 'Payment Failed';
                }
                redirect('mobile/failed/' . $error . '?mobileId=' . $this->mobdata['mobileId']);
            }
        } else {
            redirect('mobile/payment/Cancel?mobileId=' . $this->mobdata['mobileId']);
        }
    }

    /**
     * 
     * Loading success payment
     *
     * */
    public function pay_success() {
        $user_id = $this->uri->segment(3);
        $ride_id = $this->uri->segment(4);
        $payment_type = $this->uri->segment(5);
        $trans_id = $this->uri->segment(6);
        $payment_status = 'Completed';

        if ($payment_type == 'paypal') {
            $trans_id = $_REQUEST['txn_id'];
            $payment_status = $_REQUEST['payment_status'];
        }

        if ($payment_status == 'Completed') {
            $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
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
                                $message = 'payment completed';
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
            $this->mobdata['payOption'] = 'ride payment';
            $this->load->view('mobile/success.php', $this->mobdata);
        } else {
            redirect('mobile/failed/paymentfailed?mobileId=' . $this->mobdata['mobileId']);
        }
    }

    /**
     * 
     * Loading failed payment
     */
    public function pay_failed() {
        $this->mobdata['errors'] = $this->uri->segment(3);
        $this->load->view('mobile/failed.php', $this->mobdata);
    }

    /**
     * 
     * Connecting back to mobile application
     */
    public function payment_return() {
        $this->mobdata['msg'] = $this->uri->segment(3);

        /* if($this->mobdata['msg']=='paymentfailed'){
          $driver_id = $this->mobdata['driver_id'];
          $driverVal = $this->app_model->get_selected_fields(DRIVERS,array('_id'=>new \MongoId($driver_id)),array('_id','push_notification'));
          if($driverVal->num_rows()>0){
          if(isset($driverVal->row()->push_notification)){
          if($driverVal->row()->push_notification!=''){
          $message='payment completed';
          $options=array('ride_id'=>(string)$ride_id,'driver_id'=>$driver_id);
          if(isset($driverVal->row()->push_notification['type'])){
          if($driverVal->row()->push_notification['type']=='ANDROID'){
          if(isset($driverVal->row()->push_notification['key'])){
          if($driverVal->row()->push_notification['key']!=''){
          $this->sendPushNotification($driverVal->row()->push_notification['key'],$message,'payment_paid','ANDROID',$options,'DRIVER');
          }
          }
          }
          if($driverVal->row()->push_notification['type']=='IOS'){
          if(isset($driverVal->row()->push_notification['key'])){
          if($driverVal->row()->push_notification['key']!=''){
          $this->sendPushNotification($driverVal->row()->push_notification['key'],$message,'payment_paid','IOS',$options,'DRIVER');
          }
          }
          }
          }
          }
          }
          }
          } */

        $this->clearPayData();
        $this->load->view('mobile/payment_return.php', $this->mobdata);
    }

    /**
     * 
     * Delete Payment Records
     */
    public function clearPayData() {
        $this->app_model->commonDelete(MOBILE_PAYMENT, array('_id' => new \MongoId($this->mobdata['mobileId'])));
    }

}

/* End of file mobile_payment.php */
/* Location: ./application/controllers/mobile/mobile_payment.php */