<?php
$this->load->view('site/templates/profile_header');
?> 

<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">
                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>
                <div class="col-md-9 profile_rider_right">
                    <div class="col-md-11 pay-instructions">
                        <h2> <?php if ($this->lang->line('wallet_your_total_charge') != '') echo stripslashes($this->lang->line('wallet_your_total_charge')); else echo 'Your total charge is'; ?> <?php echo $this->config->item('currency_symbol') . ' ' . number_format($trans_details->row()->total_amount, 2); ?></h2>
                        <span><b><?php if ($this->lang->line('user_note') != '') echo stripslashes($this->lang->line('user_note')); else echo 'NOTE :'; ?></b> <?php if ($this->lang->line('wallet_your_card_information_saved') != '') echo stripslashes($this->lang->line('wallet_your_card_information_saved')); else echo 'Your card information will be saved in stripe secure gateway for your later and faster transaction.';
                            ?></span>
                        <form action="site/wallet_recharge/stripe_payment_process" name="wallet_recharge_form" id="wallet_recharge_form" method="POST" onsubmit="return showLoader();">
						<?php
						$tatalAmount = ($trans_details->row()->total_amount * 100);
						
		
						if ($this->lang->line('wallet_pay_with_your_card') != '') $pay_with = stripslashes($this->lang->line('wallet_pay_with_your_card')); else $pay_with = 'Pay With Your Card';
						$product_description = $siteTitle .' Money - Wallet Recharge';
						$newImgpathtoStripe = 'images/logo/' . $this->config->item('logo_image');
						$payment_btn_label = $pay_with;
						?>

						<input type="hidden" value="<?php echo $trans_details->row()->transaction_id; ?>" name="transaction_id" />
						<input type="hidden" value="<?php echo $trans_details->row()->user_id; ?>" name="user_id" />
						<input type="hidden" value="<?php echo $trans_details->row()->total_amount; ?>" name="total_amount" />
						<input type="hidden" value="<?php echo $rider_info->row()->email; ?>" name="email" />

						<input type="hidden" name="Stripeproduct_description" value="<?php echo $product_description; ?>"/>
						<script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
								data-key="<?php echo $stripe_settings['settings']['publishable_key']; ?>"
								data-amount="<?php echo $tatalAmount; ?>" 
								data-billing-address ="false"
								data-image="<?php echo $newImgpathtoStripe; ?>"
								data-name="<?php echo $this->config->item('email_title'); ?>"
								data-label ="<?php echo $payment_btn_label; ?>"
								data-description="<?php echo $product_description; ?>">
						</script> 
						<br/>
						<img src="images/loader.gif" style="display:none;" id="payLoader">
					</div>
				</div>
			</div>
		</section>
	</div>
</div> 
<script>
function showLoader() {
	$('#payLoader').css('display', 'block');
}
</script>
<?php
$this->load->view('site/templates/footer');
?> 