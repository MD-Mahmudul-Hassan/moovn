<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mpesa_api extends MY_Controller {

    function __construct() {
        parent::__construct();

        $this->load->library('mpesa');
        $this->load->model(array('user_model', 'wallet_recharge_model', 'driver_model', 'wallet_model', 'mpesa_transaction_model', 'mail_model'));

        /* Authentication Begin */
        $headers = $this->input->request_headers();
        header('Content-type:application/json;charset=utf-8');
        $current_function = $this->router->fetch_method();
        $public_functions = array('callback');
        if (array_key_exists("User-Token", $headers)) {
            $this->user_token = $headers['User-Token'];
            try {
                if (isset($this->user_token) && $this->user_token != '') {
                    $user = $this->app_model->get_selected_fields(USERS, array('login_token' => $this->user_token), array('_id'));
                    if ($user->num_rows() <= 0) {
                        echo json_encode(array("is_dead"=>"Yes")); die;
                    }
                }
            } catch (MongoException $ex) {
                echo $ex->getMessage(); die;
            }
        } else if (array_key_exists("Driver-Token", $headers)) {
            $this->driver_token = $headers['Driver-Token'];
            try {
                if (isset($this->driver_token)) {
                    $driver = $this->app_model->get_selected_fields(DRIVERS, array('sso_token' => $this->driver_token), array('_id'));
                    if ($driver->num_rows() <= 0) {
                        echo json_encode(array("is_dead" => "Yes"));
                        die;
                    }
                }
            } catch (MongoException $ex) {
                echo $ex->getMessage();
                die;
            }
        } else if (!in_array($current_function, $public_functions)) {
            show_404();
        }
        /* Authentication End */
    }

    private function _login()
    {
        $returnArr['status'] = false;

        $mpesa_client = $this->mpesa->init_login_client();
        if ($mpesa_client['status'] === true) {
            $mpesa_login = $this->mpesa->login();
            if ($mpesa_login['status'] === true) {
                $returnArr['status'] = true;
                $returnArr['SessionId'] = $mpesa_login['SessionId'];
            } else {
                $returnArr['response'] = $mpesa_login['message'];
            }
        } else {
            $returnArr['response'] = $mpesa_client['message'];
        }
        return $returnArr;
    }

    public function _transaction($token, $request_data_items, $third_party_reference) 
    {
        $returnArr['status'] = false;

        $transactionResult = $this->mpesa->ussd_push_initiate($token, $request_data_items, $third_party_reference);
        if ($transactionResult['status'] === true) {
            $returnArr['status'] = true;
            $returnArr['response'] = $transactionResult['message'];
            $returnArr['insight_reference'] = $transactionResult['insight_reference'];
        } else {
            $returnArr['response'] = $transactionResult['message'];
        }
        return $returnArr;
    }

    public function user_recharge_wallet() 
    {
        try {
            $returnArr['status'] = '0';
            $returnArr['response'] = '';

            $user_id = trim($this->input->post('user_id'));
            $amount_param = trim($this->input->post('amount'));

            if (isset($user_id) && empty($user_id) && isset($amount_param) && empty($amount_param)) {
                $returnArr['response'] = 'Incorrect values of parameters passed';
            } else {
                $userResult = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('country_code', 'phone_number', 'currency'));
                if ($userResult->num_rows() == 1) {
                    $user = $userResult->row();
                    $request_data_items['customer_msisdn'] = $user->country_code . $user->phone_number;
                    $amount = str_replace(',', '', $amount_param);
                    $request_data_items['amount'] = $amount;
                    $request_data_items['currency'] = $user->currency;
                    $loginResult = $this->_login();
                    if ($loginResult['status'] == 1) {
                        $third_party_reference = uniqid();
                        $mpesaTransaction = $this->_transaction($loginResult['SessionId'], $request_data_items, $third_party_reference);
                        if ($mpesaTransaction['status'] === true) {
                            $walletUpdateResult = $this->wallet_recharge_model->update($user_id, $amount, $third_party_reference, $mpesaTransaction['insight_reference']);
                            if ($walletUpdateResult['status'] === true) {
                                $add_tx = $this->mpesa_transaction_model->add($user_id, 'rider', $amount, $third_party_reference, $mpesaTransaction['insight_reference']);
                                if ($add_tx['status']) {
                                    $returnArr['status'] = '1';
                                    $returnArr['third_party_reference'] = $third_party_reference;
                                    $returnArr['insight_reference'] = $mpesaTransaction['insight_reference'];
                                    $returnArr['login_token'] = $loginResult['SessionId'];
                                    $returnArr['response'] = 'Updated Wallet Recharge Table Successfully';
                                } else {
                                    $returnArr['response'] = $add_tx['response'];
                                }                                
                            } else {
                                $returnArr['response'] = 'Could not update Wallet Recharge table';
                            }
                        } else {
                            $returnArr['response'] = $mpesaTransaction['response'];
                        }
                    } else {
                        $returnArr['response'] = $loginResult['response'];
                    }
                } else {
                    $returnArr['response'] = 'User not found';
                }
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function driver_remit() 
    {
        try {
            $returnArr['status'] = '0';
            $returnArr['response'] = '';

            $driver_id = trim($this->input->post('driver_id'));

            if (isset($driver_id) && empty($driver_id)) {
                $returnArr['response'] = 'Empty Parameters';
            } else {
                $driverResult = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('dail_code', 'mobile_number', 'outstanding_amount', 'currency'));
                if ($driverResult->num_rows() == 1) {
                    $driver = $driverResult->row();
                    $request_data_items['customer_msisdn'] = $driver->dail_code . $driver->mobile_number;
                    $request_data_items['amount'] = $driver->outstanding_amount['amount'];
                    $request_data_items['currency'] = $driver->currency;
                    $loginResult = $this->_login();
                    if ($loginResult['status'] == 1) {
                        $third_party_reference = uniqid();
                        $mpesaTransaction = $this->_transaction($loginResult['SessionId'], $request_data_items, $third_party_reference);
                        if ($mpesaTransaction['status'] === true) {
                            $add_tx = $this->mpesa_transaction_model->add($driver_id, 'driver', $request_data_items['amount'], $third_party_reference, $mpesaTransaction['insight_reference']);
                            if ($add_tx['status']) {
                                $returnArr['status'] = '1';
                                $returnArr['response'] = 'Processing';
                                $returnArr['third_party_reference'] = $third_party_reference;
                                $returnArr['insight_reference'] = $mpesaTransaction['insight_reference'];
                                $returnArr['login_token'] = $loginResult['SessionId'];
                            } else {
                                $returnArr['response'] = $add_tx['response'];
                            }
                        } else {
                            $returnArr['response'] = $mpesaTransaction['response'];
                        }
                    } else {
                        $returnArr['response'] = $loginResult['response'];
                    }
                } else {
                    $returnArr['response'] = 'Driver not found';
                }
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    private function _confirm_wallet_recharge($third_party_reference) 
    {
        $returnArr['status'] = false;
        $returnArr['response'] = '';

        $walletRechargeResult = $this->wallet_recharge_model->get_all_details(WALLET_RECHARGE, array('third_party_reference' => $third_party_reference, 'pay_status' => 'Pending'));
        if ($walletRechargeResult->num_rows() == 1) {
            $walletRechargeData = $walletRechargeResult->row();
            $user_id = $walletRechargeData->user_id;
            $total_amount = $walletRechargeData->total_amount;
            $this->app_model->update_wallet((string) $user_id, 'CREDIT', floatval($total_amount));

            $currentWallet = $this->wallet_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
            $avail_amount = 0.00;
            if ($currentWallet->num_rows() > 0) {
                if (isset($currentWallet->row()->total)) {
                    $avail_amount = floatval($currentWallet->row()->total);
                }
            }
            $transaction_time = time();
            $walletData = array(
                'type' => 'CREDIT',
                'credit_type' => 'recharge',
                'ref_id' => 'Mpesa',
                'trans_amount' => floatval($total_amount),
                'avail_amount' => floatval($avail_amount),
                'trans_date' => new \MongoDate($transaction_time),
                'trans_id' => $third_party_reference
            );
            $wallet_update_result = $this->wallet_model->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $walletData));
            if ($wallet_update_result) {
                $this->wallet_recharge_model->update_status('Completed', $third_party_reference);
                $returnArr['status'] = true;
            }
        } else if ($walletRechargeResult->num_rows() == 0) {
            $returnArr['status'] = false;
            $returnArr['response'] = 'Could not update Wallet';
        } else if ($walletRechargeResult->num_rows() > 1) {
            $returnArr['status'] = false;
            $returnArr['response'] = 'Multiple Insight Reference ID\'s found';
        }
        return $returnArr;
    }

    private function _get_mpesa_data()
    {
        $data = file_get_contents('php://input');
        $parsed_xml = $this->get_data_from_xml($data);
        $elements = $parsed_xml['elements'];
        foreach($elements as $key => $item) {
            if (isset($item['value']) && $item['value'] == 'TransactionStatus') {
                $transaction_status = $elements[$key+1]['value'];
            }
            if (isset($item['value']) && $item['value'] === 'ThirdPartyReference') {
                $third_party_reference = $elements[$key+1]['value'];
            }
        }
        return array(
            'transaction_status' => $transaction_status,
            'third_party_reference' => $third_party_reference
        );
    }

    public function callback() 
    {
        $callback_data = $this->_get_mpesa_data();
        $third_party_reference = $callback_data['third_party_reference'];
        $transaction_status = $callback_data['transaction_status'];

        if ($transaction_status === 'Success' || $transaction_status === 'Completed') {
            $this->_confirm_wallet_recharge($third_party_reference);
        } else {
            $this->wallet_recharge_model->update_status('Cancelled', $third_party_reference);
        }
        $this->mpesa_transaction_model->update_status($transaction_status, $third_party_reference);
        $transaction = $this->mpesa_transaction_model->get_selected_fields(MPESA_TRANSACTIONS, array('third_party_reference' => $third_party_reference), array('user_type', 'user_id', 'status', 'amount'));
        $user_id = $transaction->row()->user_id;        
        if ($transaction->row()->user_type == 'driver') {
            if ($transaction_status === 'Success' || $transaction_status === 'Completed') {
                $this->driver_model->subtract_outstanding_amount($user_id, $transaction->row()->amount);
            }
            $this->mpesa_transaction_model->notify_driver($transaction);
        } else if ($transaction->row()->user_type == 'rider') {
            $this->mpesa_transaction_model->notify_user($transaction);
        }
    }

    public function get_transaction_status()
    {
        $returnArr['status'] = 'failure';
        $returnArr['response'] = '';
        
        try {
            $login_token = trim($this->input->post('login_token'));
            $third_party_reference = trim($this->input->post('third_party_reference'));
            $insight_reference = trim($this->input->post('insight_reference'));
            $user_id = trim($this->input->post('user_id'));
            $driver_id = trim($this->input->post('driver_id'));
            
            if (!empty($login_token) && !empty($third_party_reference) && (!empty($user_id) || !empty($driver_id))) {
                if (isset($user_id) && !empty($user_id)) {
                    $userResult = $this->user_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('country_code', 'phone_number'));
                    $user = $userResult->row();
                    $phone_number = $user->country_code . $user->phone_number;
                } else if (isset($driver_id) && !empty($driver_id)) {
                    $driverResult = $this->driver_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('dail_code', 'mobile_number'));
                    $driver = $driverResult->row();
                    $phone_number = $driver->dail_code . $driver->mobile_number;
                }
                $transactionResult = $this->mpesa->transaction_status($login_token, $insight_reference, $phone_number);
                if ($transactionResult['message'] === 'Success' || $transactionResult['message'] === 'Completed') {
                    if (isset($user_id) && !empty($user_id)) {
                        $confirm_recharge = $this->_confirm_wallet_recharge($third_party_reference);
                        if ($confirm_recharge['status']) {
                            $returnArr['status'] = 'success';
                            $returnArr['response'] = 'Wallet Updated Successfully';
                        } else {
                            $returnArr['response'] = $confirm_recharge['response'];
                        }
                        
                        $currentWalletResult = $this->wallet_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));

                        $avail_amount = 0.00;
                        if ($currentWalletResult->num_rows() > 0) {
                            if (isset($currentWalletResult->row()->total)) {
                                $avail_amount = floatval($currentWalletResult->row()->total);
                            }
                        }
                        $returnArr['wallet_balance'] = (string) $avail_amount;

                    } else if (isset($driver_id) && !empty($driver_id)) {
                        $returnArr['status'] = 'success';
                        $returnArr['response'] = 'Transaction Successful';
                    }
                } else if ($transactionResult['message'] == 'USSDCallbackCancel') {
                    $this->wallet_recharge_model->update_status('Cancelled', $third_party_reference);
                    $returnArr['response'] = 'Cancelled';
                } else if ($transactionResult['message'] == 'Forwarded' || $transactionResult['message'] == 'USSDCallbackSuccess') {
                    $returnArr['status'] = 'in-progress';
                } else {
                    $returnArr['response'] = $transactionResult['message'];
                }
            } else {
                $returnArr['response'] = 'Required Parameters Missing';
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function test_login() 
    {
        try {
            $returnArr['status'] = '0';
            $returnArr['response'] = '';

            $loginResult = $this->_login();
            if ($loginResult['status'] == 1) {
                $returnArr['status'] = '1';
                $returnArr['login_token'] = $loginResult['SessionId'];
            } else {
                $returnArr['response'] = $loginResult['response'];
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
}