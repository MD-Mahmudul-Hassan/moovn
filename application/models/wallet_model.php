<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to wallet management
 * @author Casperon
 *
 */
class Wallet_model extends My_Model{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_wallet_balance($user_id) 
    {
        $walletDetail = $this->get_selected_fields(WALLET, array('user_id' => new \MongoId($user_id)), array('total'));
        $wallet_balance = 0.00;
        if (isset($walletDetail->row()->total)) {
            $wallet_balance = $walletDetail->row()->total;
        }
        return $wallet_balance;
    }
}