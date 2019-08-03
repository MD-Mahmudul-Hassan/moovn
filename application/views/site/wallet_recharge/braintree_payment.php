<?php  
	$this->load->view('site/templates/profile_header'); 
	$currencySymbol =  $rider_info->row()->currency_symbol;
?> 


<style>
	.pay-instructions h2 {
		font-size: 24px;
		margin-top: 12px;
	}
</style>

<div class="rider-signup">
	<div class="container-new">
		<section>
			<div class="col-md-12 profile_rider">
				
				<!-------Profile side bar ---->
				
				<?php  
					$this->load->view('site/templates/profile_sidebar'); 
				?>
				
				 <div class="col-md-9 profile_rider_right">
					<div class="col-md-11 pay-instructions">
						<h2> <?php if ($this->lang->line('rider_your_total_charge_is') != '') echo stripslashes($this->lang->line('rider_your_total_charge_is')); else echo 'Your total charge is'; ?> <?php echo $currencySymbol.' '.number_format($trans_details->row()->total_amount,2); ?></h2>
						<span><b> <?php if ($this->lang->line('rider_note_high') != '') echo stripslashes($this->lang->line('rider_note_high')); else echo 'NOTE'; ?> : </b> <?php if ($this->lang->line('rider_your_card_information_will_braintree') != '') echo stripslashes($this->lang->line('rider_your_card_information_will_braintree')); else echo 'Your card information will be saved in braintree secure gateway for your later and faster transaction'; ?>.</span>
						<form name="PaymentCard" id="PaymentCard" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>v5/payment/braintree_api/make_site_wallet_payment">
							<ul>
							
							
									<li id="payment_methodContain" style="display:<?php if($client_id == '') echo 'none';?>">
									<div class="form_grid_12">
										<div class="form_input">		
											<div id="dropin-container"></div>
											<input type="hidden" name="nonce" id="nonce">
										</div>
									</div>
								</li>
							
								
								<?php if($client_id != ''){ ?>
										<script src="https://js.braintreegateway.com/js/braintree-2.22.2.min.js"></script>
										<script type="text/javascript">
											braintree.setup("<?php echo $client_id; ?>", "dropin", {
											   container: "dropin-container",
											   onPaymentMethodReceived: function (nonce) {
												console.log(JSON.stringify(nonce));
											   $('#nonce').val(JSON.stringify(nonce));
											   var obj = $.parseJSON(JSON.stringify(nonce));
											   $('#nonce').val(obj['nonce']);
											   PaymentCard.submit();
											  }
											});
										</script>
								<?php } ?>
								
								
								<input type="hidden" value="<?php echo $trans_details->row()->transaction_id;?>" name="transaction_id" />
								<input type="hidden" value="<?php echo $trans_details->row()->user_id;?>" name="user_id" />
								<input type="hidden" value="<?php echo $trans_details->row()->total_amount;?>" name="total_amount" />
								<input type="hidden" value="<?php echo $rider_info->row()->email;?>" name="email" />
								
								<li class="last">
									<input type="submit" class="sign_up2_btn2" style="font-weight:bold;" value="Proceed To Pay" ></input>
								</li>
							</ul>
						</form>
					</div>
				
				</div>
			</div>
		</section>
	</div>
</div> 

<style>
	.pay-instructions label {
		width: 15%;
	}
	.cardInput {
		width: 50%;
	}
	.expm {
		width: 14%;
	}
	.expy {
		width: 33%;
	}
	.cardDrop {
		height: 35px;
		margin: 10px;
	}
	.sign_up2_btn2 {
		margin-left: 16%;
		width: 50% !important;
	}
	.pay-instructions h2 {
		border-bottom: 1px solid #dfdfdf;
		padding-bottom: 9px;
	}
	.col-md-11.pay-instructions {
		padding-top: 0;
		margin-top: 3%;
	}



</style>
<script type="text/javascript">
		function validatecard(){
			var cardNumber=document.getElementById("cardNumber").value.trim();
			var CCExpDay=document.getElementById("CCExpDay").value.trim();
			var CCExpMnth=document.getElementById("CCExpMnth").value.trim();
			var creditCardIdentifier=document.getElementById("creditCardIdentifier").value.trim();
			var cardType=document.getElementById("cardType").value.trim();
			
			document.getElementById("cardNumber").classList.remove("txt-error");
			document.getElementById("CCExpDay").classList.remove("txt-error");
			document.getElementById("CCExpMnth").classList.remove("txt-error");
			document.getElementById("creditCardIdentifier").classList.remove("txt-error");
			document.getElementById("cardType").classList.remove("txt-error");
			
			var status=0;
			if(cardNumber=="" || isNaN(cardNumber)){
				document.getElementById("cardNumber").classList.add("txt-error");
				status++;
			}
			if(CCExpDay==""){
				document.getElementById("CCExpDay").classList.add("txt-error");
				status++;
			}
			if(CCExpMnth==""){
				document.getElementById("CCExpMnth").classList.add("txt-error");
				status++;
			}
			if(creditCardIdentifier==""){
				document.getElementById("creditCardIdentifier").classList.add("txt-error");
				status++;
			}
			if(cardType==""){
				document.getElementById("cardType").classList.add("txt-error");
				status++;
			}
			if(status!=0){
				return false;
			}
		}
		</script>
<?php  
	$this->load->view('site/templates/footer'); 
?> 