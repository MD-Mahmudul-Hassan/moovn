<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to wallet management
 * @author Casperon
 *
 */
class Mpesa_transaction_model extends My_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('mail_model', 'wallet_model'));
    }

    public function add($user_id, $user_type, $total_amount, $third_party_reference, $insight_reference) 
    {
        $returnArr['status'] = false;
        $returnArr['response'] = '';
        $pay_date = date("Y-m-d H:i:s");
        $updateData = array(
            'user_id' => $user_id, 
            'amount' => $total_amount, 
            'third_party_reference' => $third_party_reference,
            'insight_reference' => $insight_reference, 
            'date' => $pay_date, 
            'status' => 'Pending',
            'user_type' => $user_type
        );

        try {
            $insertResult = $this->simple_insert(MPESA_TRANSACTIONS, $updateData);
            if ($insertResult) {
                $returnArr['status'] = true;
            } else {
                $returnArr['status'] = false;
            }
        } catch (Exception $e) {
            $returnArr['response'] = $e->getMessage();;           
        }
        return $returnArr;
    }

    public function notify_driver($transaction)
    {
        $driverVal = $this->get_selected_fields(DRIVERS, array('_id' => new \MongoId($transaction->row()->user_id)), array('fcm_token', 'device_type'));
        if ($driverVal->num_rows() > 0) {
            if ($transaction->row()->status == 'USSDCallbackCancel') {
                $options = array(
                    'status' => 'failure',
                    'response' => 'Cancelled'
                );
                $message = 'Sorry! Your transaction for amount: ' . $transaction->row()->amount . ' could not be processed.';
            } if ($transaction->row()->status == 'Success' || $transaction->row()->status == 'Completed') {
                $options = array(
                    'status' => 'success',
                    'response' => 'Transaction Successful'
                );
                $message = 'Your transaction for amount: ' . $transaction->row()->amount . ' was successful.';
            } else {
                $options = array(
                    'status' => 'failure',
                    'response' => 'Cancelled'
                );
                $message = 'Sorry! Your transaction for amount: ' . $transaction->row()->amount . ' could not be processed.';
            }
            $my_ci =& get_instance();
            $my_ci->notify($driverVal->row()->fcm_token, $message, 'mpesa_payment_status', $options, $driverVal->row()->device_type);
        }
    }

    public function notify_user($transaction)
    {
        $currentWalletResult = $this->wallet_model->get_selected_fields(WALLET, array('user_id' => new \MongoId($transaction->row()->user_id)), array('total'));
        $avail_amount = 0.00;
        if ($currentWalletResult->num_rows() > 0) {
            if (isset($currentWalletResult->row()->total)) {
                $avail_amount = floatval($currentWalletResult->row()->total);
            }
        }
        if ($transaction->row()->status == 'USSDCallbackCancel') {
            $options = array(
                'status' => 'failure',
                'response' => 'Cancelled',
                'wallet_balance' => (string) $avail_amount
            );
            $message = 'Sorry! Your transaction for amount: ' . $transaction->row()->amount . ' could not be processed.';
        } if ($transaction->row()->status == 'Success' || $transaction->row()->status == 'Completed') {
            $options = array(
                'status' => 'success',
                'response' => 'Transaction Successful',
                'wallet_balance' => (string) $avail_amount
            );
            $message = 'Your transaction for amount: ' . $transaction->row()->amount . ' was successful.';
        } else {
            $options = array(
                'status' => 'failure',
                'response' => 'Cancelled',
                'wallet_balance' => (string) $avail_amount
            );
            $message = 'Sorry! Your transaction for amount: ' . $transaction->row()->amount . ' could not be processed.';
        }
        $userVal = $this->get_selected_fields(USERS, array('_id' => new \MongoId($transaction->row()->user_id)), array('fcm_token', 'device_type'));
        $my_ci =& get_instance();
        $my_ci->notify($userVal->row()->fcm_token, $message, 'mpesa_payment_status', $options, $userVal->row()->device_type);
    }

    public function update_status($status, $third_party_reference) 
    {
        $updateData = array('status' => $status);
        try {
            $this->update_details(MPESA_TRANSACTIONS, $updateData, array('third_party_reference' => $third_party_reference));
        } catch (Exception $e) {
             $returnArr['response'] = $e->getMessage();        
        }
    }
}