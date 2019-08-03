<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to wallet management
 * @author Casperon
 *
 */
class Wallet_recharge_model extends My_Model{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function update($user_id, $total_amount, $third_party_reference, $insight_reference) 
    {
        $returnArr['status'] = false;
        $returnArr['response'] = '';
        $pay_date = date("Y-m-d H:i:s");
        $walletRechargeData = array(
            'user_id' => $user_id, 
            'total_amount' => $total_amount, 
            'third_party_reference' => $third_party_reference,
            'insight_reference' => $insight_reference,
            'pay_date' => $pay_date, 
            'pay_status' => 'Pending',
            'payment_host' => 'web',
            'type' => 'Mpesa'
        );

        try {
            $walletRechargeInsert = $this->simple_insert(WALLET_RECHARGE, $walletRechargeData);
            if ($walletRechargeInsert) {
                $returnArr['status'] = true;
            } else {
                $returnArr['status'] = false;
            }
        } catch (Exception $e) {
            $returnArr['response'] = $e->getMessage();;           
        }
        return $returnArr;
    }

    public function update_status($status, $third_party_reference) 
    {
        $updateData = array('pay_status' => $status);

        try {
            $this->update_details(WALLET_RECHARGE, $updateData, array('third_party_reference' => $third_party_reference));
        } catch (Exception $e) {
             $returnArr['response'] = $e->getMessage();;           
        }
    }
}