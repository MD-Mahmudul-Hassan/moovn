<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $this->config->item('email_title'); ?> - Choose payment option</title>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/mobile/app-style.css" type="text/css" media="all" />
		<script type="text/javascript" src="<?php echo base_url();?>js/site/jquery-1.10.2.js"></script>
	</head>
	<body>
		<section>
			<div class="shipping_address">
				<div class="main">		
					<div class="app-content-box ">
						<?php if($paypal_settings['status'] == 'Enable' || $authorize_net_settings['status'] == 'Enable'){ ?>
						<h1>Choose Your Payment Method<a class="close_btn" href="<?php echo base_url(); ?>wallet-recharge/pay-cancel?mobileId=<?php echo $trans_details->row()->transaction_id;?>"></a></h1>
						
						<ul>
							<li>
								<?php if($paypal_settings['status'] == 'Enable'){ ?>
								<span class="paypalBtn">
								<label for="pay_paypal">PayPal</label>
								<input type="radio" class="input-scroll-3 payment_radio" onclick="payment_action('pay_paypal');"  id="pay_paypal" checked="checked" name="payment_value"/>
								</span>
								<?php } ?>
								<?php if($authorize_net_settings['status'] == 'Enable'){ ?>
								<label for="pay_credit_card"> Credit Card </label>
								<input type="radio" class="input-scroll" id="pay_credit_card" onclick="payment_action('pay_credit_card');" name="payment_value" />
								<?php } ?>
							</li>
						</ul>
						<?php }else{ ?> 
						<h1> Currently payment options are not available.</h1>
						<?php }?>
					</div>
					
					<?php if($paypal_settings['status'] == 'Enable'){ ?>
					<div class="app-content-box payment_container" style="display:block;" id="paypal_container">
						<form name="PaymentForm" id="PaymentPalForm" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>mobile/mobile_wallet_recharge/paypal_wal_payment_process">
							
							<input type="hidden" value="<?php echo $trans_details->row()->transaction_id;?>" name="transaction_id" />
							<input type="hidden" value="<?php echo $trans_details->row()->user_id;?>" name="user_id" />
							<input type="hidden" value="<?php echo $trans_details->row()->total_amount;?>" name="total_amount" />
							
							<ul>
								<li class="last"><input type="submit" class="input-submit-btn" value="Proceed to Pay <?php echo $this->config->item('currency_symbol').' '.number_format($trans_details->row()->total_amount,2); ?>"></input></li>
							</ul>
							
							</form>
					</div>
					<?php } ?>
					
	<!------------------------------------------------------------------------------------------------------------------------->
					<?php if($authorize_net_settings['status'] == 'Enable'){ ?>
					<div class="app-content-box payment_container" style="display:none;" id="credit_card_container">
						<form name="PaymentCard" id="PaymentCard" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>mobile/mobile_wallet_recharge/user_wal_PaymentCard" >
							<ul>
								<li><input type="text" class="input-scroll-3" placeholder="Card number" id="cardNumber" name="cardNumber" maxlength="16" size="16"></input></li>
								<li><label>Expiration</label> 
									<?php $Sel ='selected="selected"';  ?>
									<select id="CCExpDay" name="CCExpDay" class="input-scroll-2">
									<option value="01" <?php if(date('m')=='01'){ echo $Sel;} ?>>01</option>
									<option value="02" <?php if(date('m')=='02'){ echo $Sel;} ?>>02</option>
									<option value="03" <?php if(date('m')=='03'){ echo $Sel;} ?>>03</option>
									<option value="04" <?php if(date('m')=='04'){ echo $Sel;} ?>>04</option>
									<option value="05" <?php if(date('m')=='05'){ echo $Sel;} ?>>05</option>
									<option value="06" <?php if(date('m')=='06'){ echo $Sel;} ?>>06</option>
									<option value="07" <?php if(date('m')=='07'){ echo $Sel;} ?>>07</option>
									<option value="08" <?php if(date('m')=='08'){ echo $Sel;} ?>>08</option>
									<option value="09" <?php if(date('m')=='09'){ echo $Sel;} ?>>09</option>
									<option value="10" <?php if(date('m')=='10'){ echo $Sel;} ?>>10</option>
									<option value="11" <?php if(date('m')=='11'){ echo $Sel;} ?>>11</option>
									<option value="12" <?php if(date('m')=='12'){ echo $Sel;} ?>>12</option>
									</select>
									<select id="CCExpMnth" name="CCExpMnth" class="input-scroll-2"> 
										<?php for($i=date('Y');$i< (date('Y') + 30);$i++){ ?>
										<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php } ?>
									</select>
								</li>
								<li><input type="password" class="input-scroll" placeholder="Security Code" id="creditCardIdentifier" name="creditCardIdentifier"></input></li>
								<li>
									<select id="cardType" name="cardType" class="input-scroll-4">
										<option value="">Select Card Type</option>
										<option value="american_express">American Express</option>
										<option value="visa">Visa</option>
										<option value="master_card">Master Card</option>
										<option value="discover">Discover</option>
									</select>
								</li>
								
								
							<input type="hidden" value="<?php echo $trans_details->row()->transaction_id;?>" name="transaction_id" />
							<input type="hidden" value="<?php echo $trans_details->row()->user_id;?>" name="user_id" />
							<input type="hidden" value="<?php echo $trans_details->row()->total_amount;?>" name="total_amount" />
								
								<li class="last">
									<input type="submit" class="input-submit-btn" value="Proceed to Pay <?php echo $this->config->item('currency_symbol').' '.number_format($trans_details->row()->total_amount,2); ?>" onClick="return validatecard();"></input>
								</li>
							</ul>
						</form>
					</div>
					<?php }?>
					
				</div>	
			</div>
		</section>
		
		<style>
		.payment_radio{
			width: 3%;
		}
		
		label {
			cursor: pointer;
		}
		</style>
			
		<script>
			function payment_action(opt){
				$('.payment_container').css('display','none');
				if(opt == 'pay_paypal'){
					$('#paypal_container').css('display','block');
				} else if(opt == 'pay_credit_card'){
					$('#credit_card_container').css('display','block');
				}
			}
		</script>
		
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
		
	</body>
</html>
