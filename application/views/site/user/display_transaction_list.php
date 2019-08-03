<?php
$this->load->view('site/templates/profile_header');

if (!empty($wallet_history[0]['transactions'])) {
    $wallet_txns = array_reverse($wallet_history[0]['transactions']);
} else {
    $wallet_txns = array();
}
?> 

<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">

                <!-------Profile side bar ---->

                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>

                <div class="col-md-9 profile_rider_right">
                    <div class="col-md-12 rider-pickup-detail">
                        <h2 style="width:6%;"> <a href="rider/my-money" style="font-size: 15px;"><img  style="width:60%" src="images/site/purse_icon.png"></a></h2>
                        <h2 style="width:94%;"> <?php if ($this->lang->line('user_your_wallet_money_transactions') != '') echo stripslashes($this->lang->line('user_your_wallet_money_transactions')); else echo 'YOUR WALLET/MONEY TRASACTIONS'; ?></h2>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs rider_profile-tab wallet-trans-tab-head" role="tablist">
                            <li role="presentation" class="<?php if ($txn_type == 'all' || $txn_type == '') echo 'active'; ?>"><a href="rider/wallet-transactions?q=all" aria-controls="All Transactions" role="tab" data-toggle=""><?php if ($this->lang->line('user_all') != '') echo stripslashes($this->lang->line('user_all')); else echo 'ALL'; ?></a></li>
                            <li role="presentation" class="<?php if ($txn_type == 'credit') echo 'active'; ?>"><a href="rider/wallet-transactions?q=credit" aria-controls="Credit" role="tab" data-toggle=""><?php if ($this->lang->line('user_credit') != '') echo stripslashes($this->lang->line('user_credit')); else echo 'CREDIT'; ?></a></li>
                            <li role="presentation" class="<?php if ($txn_type == 'debit') echo 'active'; ?>"><a href="rider/wallet-transactions?q=debit" aria-controls="Debit" role="tab" data-toggle=""> <?php if ($this->lang->line('user_debit') != '') echo stripslashes($this->lang->line('user_debit')); else echo 'DEBIT'; ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="all">
                                <ul>
                                    <?php
                                    if ($this->lang->line('home_cabily') != '')
                                        $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                                    else
                                        $sitename = $this->config->item('email_title');
                                    ?>
                                    <?php
									
										if($this->lang->line('user_wallet_debit_txn') != '') $user_wallet_debit_txn = stripslashes($this->lang->line('user_wallet_debit_txn')); else $user_wallet_debit_txn = 'Wallet Debit TxnId';
									
                                    if (!empty($wallet_txns)) {
                                        foreach ($wallet_txns as $txns) {
										   
                                            $txns_description = '';
                                            $txn_amount = 0;
                                            $avail_balance = 0;
                                            $txn_date = '';
                                            $trans_mode = '';
                                            if ((isset($txns['credit_type']) || isset($txns['debit_type'])) && isset($txns['type'])) {
                                                if ($txns['credit_type'] == 'welcome') {
                                                    if ($this->lang->line('user_welcome_bonus') != '') $var = stripslashes($this->lang->line('user_welcome_bonus')); else $var = ' Welcome Bonus';
                                                    $txns_description = $sitename . $var;
                                                } else if ($txns['credit_type'] == 'recharge') {
                                                    if ($this->lang->line('user_wallet_recharge_txn') != '') $var = stripslashes($this->lang->line('user_wallet_recharge_txn')); else $var = 'Wallet Recharge TxnId : ';
                                                    $txns_description = $var . $txns['trans_id'];
                                                }  else if ($txns['credit_type'] == 'referral') {
                                                    if ($this->lang->line('user_referral_reward') != '') $txns_description = stripslashes($this->lang->line('user_referral_reward')); else $txns_description = 'Referral reward';
													
													$refVal = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($txns['ref_id'])), array('user_name'));
													if ($refVal->num_rows() > 0) {
														if (isset($refVal->row()->user_name)) {
															$txns_description.= '  ' . $refVal->row()->user_name;
														}
													}
													
                                                } else if ($txns['debit_type'] == 'payment') {
                                                    if ($this->lang->line('user_booking_for_crn') != '') $var = stripslashes($this->lang->line('user_booking_for_crn')); else $var = 'Booking for crn:';
                                                    $txns_description = $var ." ". $txns['ref_id'];
                                                } else {
                                                    $txns_description = $txns['credit_type'];
                                                }

                                                if (isset($txns['type'])) {
                                                    if ($txns['type'] == 'CREDIT') {
														if ($this->lang->line('credit_text') != '') $txn_text = stripslashes($this->lang->line('credit_text')); else $txn_text = 'CR';
                                                        $trans_mode = '&#8593;'.$txn_text;
                                                    } else if ($txns['type'] == 'DEBIT') {
														if ($this->lang->line('debit_text') != '') $txn_text = stripslashes($this->lang->line('debit_text')); else $txn_text = 'DR';
                                                        $trans_mode = '&#8595;'.$txn_text;
                                                    }
                                                }

                                                $txn_date = date('D, d M,Y', $txns['trans_date']->sec);
                                                $txn_amount = $txns['trans_amount'];
                                                $avail_balance = $txns['avail_amount'];
                                            }
											
											if(isset($txns['debit_type'])) {
												$txn_date = date('D, d M,Y', $txns['trans_date']->sec);
												$txn_amount = $txns['trans_amount'];
												#$var="".$user_wallet_debit_txn." :";
												$var = $txns_description ." ". $txns['ref_id'];
												$avail_balance = $txns['avail_amount'];
											}
											
											if($txn_amount > 0){
                                            ?>

                                            <li>
                                                <div class="all-debit">
                                                    <div class="col-md-12 all-top">
                                                        <div class="col-md-8 all-left">
                                                            <p><?php echo $txns['currency'] . $txn_amount; ?></p>
                                                            <span><?php echo $txns_description; ?></span>
                                                        </div>
                                                        <div class="col-md-4 all-right">
                                                            <p><strong><?php echo $trans_mode; ?></strong></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 all-bottom">
                                                        <div class="col-md-6 all-left">
                                                            <p><?php echo $txn_date; ?></p>
                                                        </div>
                                                        <div class="col-md-6 all-right">
                                                            <p><?php if ($this->lang->line('user_balance') != '') echo stripslashes($this->lang->line('user_balance')); else echo 'Balance: '; ?><?php echo $txns['currency'] . $avail_balance; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php
											}
                                        }
                                    } else {
                                        ?>
                                        <li>
                                            <div class="all-debit">
                                                <div class="col-md-12 all-top">
                                                    <h3 style="text-align:center;"><?php if ($this->lang->line('user_no_transaction') != '') echo stripslashes($this->lang->line('user_no_transaction')); else echo 'No Transaction!'; ?></h3>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>                

<style>
    .wallet-trans-tab-head li{
        width:30%;
    }
</style>
<?php
$this->load->view('site/templates/footer');
?> 