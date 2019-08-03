<?php
$this->load->view('site/templates/profile_header');
$dcurrencySymbol =  $rider_info->row()->currency_symbol;
?> 
<?php
if ($this->lang->line('user_sitename_upper') != ''){
	$home_cabily_upper = str_replace('{SITENAME}', $this->config->item('site_name_capital'), stripslashes($this->lang->line('user_sitename_upper')));}
	
else{$home_cabily_upper = $this->config->item('site_name_capital');}
	
?>
<?php
if ($this->lang->line('home_cabily') != '')
	$home_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
else
	$home_cabily = $this->config->item('email_title');
?>
<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">
                <!-------Profile side bar ---->
                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>
                <div class="col-md-9 nopadding profile_rider_right">
                    <div class="col-md-12 rider-pickup-detail">

                        <h2><?php echo $home_cabily_upper; ?> <?php if ($this->lang->line('user_money') != '') echo stripslashes($this->lang->line('user_money')); else echo 'MONEY'; ?></h2>
                        <div class="col-md-12 money-head">
                            <img src="images/site/purse_icon.png">
                            <h3><?php if ($this->lang->line('user_cashless_hassle_free') != '') echo stripslashes($this->lang->line('user_cashless_hassle_free')); else echo 'Cashless, hassle-free rides with'; ?> <?php echo $home_cabily; ?> <?php if ($this->lang->line('user_money_ucfirst') != '') echo stripslashes($this->lang->line('user_money_ucfirst')); else echo 'Money'; ?></h3>
                        </div>
                        <div class="current-balance">
                            <a href="rider/wallet-transactions" class="cur-bal"><h4><?php if ($this->lang->line('user_current_balance') != '') echo stripslashes($this->lang->line('user_current_balance')); else echo 'Current Balance'; ?><span> <?php echo $dcurrencySymbol . $wallet_balance; ?> </span></h4></a>
                        </div>
                        <div class="col-md-12 recharge-money" style="margin-bottom:12px;">
                            <h4><?php if ($this->lang->line('user_recharge') != '') echo stripslashes($this->lang->line('user_recharge')); else echo 'Recharge'; ?> <?php echo $home_cabily; ?> <?php if ($this->lang->line('user_money_ucfirst') != '') echo stripslashes($this->lang->line('user_money_ucfirst')); else  echo 'Money '; ?></h4>
                            <ul class="recharge-amt">

                                <?php
                                $wal_recharge_max_amount = $this->config->item('wal_recharge_max_amount');
                                $wal_recharge_min_amount = $this->config->item('wal_recharge_min_amount');

                                $divider = 10;

                                $addMoney1 = $wal_recharge_min_amount;
                                
                                $addMoney2 = ($wal_recharge_max_amount + $wal_recharge_min_amount) / 2 ;
                                $addMoney2 = (ceil($addMoney2));

                                $addMoney3 = $wal_recharge_max_amount;
                         
                                ?>

                                <li><a href="javascript:void(0);" id="money_bucket1" class="money_bucket" data-bucket="<?php echo $addMoney1; ?>"><?php echo $dcurrencySymbol . $addMoney1; ?></a></li>
                                <li><a href="javascript:void(0);" id="money_bucke2" class="money_bucket" data-bucket="<?php echo $addMoney2; ?>"><?php echo $dcurrencySymbol . $addMoney2; ?></a></li>
                                <li><a href="javascript:void(0);" id="money_bucke3" class="money_bucket" data-bucket="<?php echo $addMoney3; ?>"><?php echo $dcurrencySymbol . $addMoney3; ?></a></li>
                            </ul>

                            <input type="hidden" id="auto_charge_status" value="<?php echo $auto_charge; ?>" />
                            <input type="hidden" id="wal_recharge_max_amount" value="<?php echo $wal_recharge_max_amount; ?>" />
                            <input type="hidden" id="wal_recharge_min_amount" value="<?php echo $wal_recharge_min_amount; ?>" />
                            <?php
                            $stripe_customer_id = '';
                            if (isset($rider_info->row()->stripe_customer_id)) {
                                $stripe_customer_id = $rider_info->row()->stripe_customer_id;
                            }
                            if ($auto_charge == 'Yes' && $stripe_customer_id != '') {
                                ?>

                                <form action="site/wallet_recharge/stripe_payment_process" name="wallet_recharge_form" id="wallet_recharge_form" method="POST">
                                    <input type="text" class="form-control" name="total_amount" id="total_amount" placeholder="<?php if ($this->lang->line('user_enter_amount_between') != '') echo stripslashes($this->lang->line('user_enter_amount_between')); else echo 'Enter amount between'; ?> <?php echo $dcurrencySymbol . $wal_recharge_min_amount; ?> - <?php echo $dcurrencySymbol . $wal_recharge_max_amount; ?>">
                                    <input type="hidden" value="" name="transaction_id" />
                                    <input type="hidden" value="<?php echo (string) $rider_info->row()->_id; ?>" name="user_id" />
                                    <input type="hidden" value="<?php echo $rider_info->row()->email; ?>" name="email" />


                                    <button type="button" class="btn btn-default money-btn" id="payBtn" onclick="wallet_payment_amt_validate('auto');"><?php if ($this->lang->line('user_add') != '') echo stripslashes($this->lang->line('user_add')); else echo 'ADD'; ?> <?php if ($this->lang->line('user_money_from_your_card') != '') echo stripslashes($this->lang->line('user_money_from_your_card')); else echo 'MONEY FROM YOUR CARD'; ?></button></br></br>
                                </form>

                                <?php
                            } else {
                                ?>

                                <form action="rider/wallet-recharge/pay-option" name="wallet_recharge_form" id="wallet_recharge_form" method="POST">

                                    <input type="text" class="form-control" name="total_amount" id="total_amount" placeholder="<?php if ($this->lang->line('user_enter_amount_between') != '') echo stripslashes($this->lang->line('user_enter_amount_between')); else echo 'Enter amount between'; ?> <?php echo $dcurrencySymbol . $wal_recharge_min_amount; ?> - <?php echo $dcurrencySymbol . $wal_recharge_max_amount; ?>">
                                    <button type="button" class="btn btn-default money-btn" id="payBtn" onclick="wallet_payment_amt_validate('manual');"><?php if ($this->lang->line('user_add') != '') echo stripslashes($this->lang->line('user_add')); else echo 'ADD'; ?> <?php echo $home_cabily_upper; ?> <?php if ($this->lang->line('user_money') != '') echo stripslashes($this->lang->line('user_money')); else echo 'MONEY'; ?></button></br></br>
                                </form>

                            <?php }
                            ?>
                            <span class="error" id="Wallet_money_err"></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div> 

<style>
    .stripe-button-el {
        margin-bottom: 3%;
        width: 100%;
    }
    .coupon {
        width: 100% !important;
    }

    .stripe-button-el {
        display:none !important;
    }
</style>
<?php
$this->load->view('site/templates/footer');
?> 