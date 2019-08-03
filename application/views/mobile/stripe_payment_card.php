<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $this->config->item('email_title'); ?> - Payment Credit Card</title>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/mobile/app-style.css" type="text/css" media="all" />
	</head>
	<body>
		<section>
			<div class="shipping_address">
				<div class="main">		
					<div class="app-content-box">
						<h1>Enter Credit Card Informations<a class="close_btn" href="<?php echo base_url(); ?>mobile/payment/Cancel?mobileId=<?php echo $mobileId; ?>"></a></h1>
						<form name="wallet_recharge_form" id="wallet_recharge_form" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>mobile/stripe-manual-payment-process" autocomplete="off" onsubmit="return showLoader();">
						<?php 
						$tatalAmount  = ($total_amount * 100);
						$product_description = ucfirst($this->config->item('email_title')).' Booking Ride';
						$newImgpathtoStripe = base_url().'images/logo/'.$this->config->item('logo_image');
						$payment_btn_label = 'Pay By Card' ;
						?>
						
						<input type="hidden" value="<?php echo $mobileId;?>" name="mobileId" />
						<input type="hidden" value="<?php echo $ride_id;?>" name="transaction_id" />
						<input type="hidden" value="<?php echo $user_id;?>" name="user_id" />
						<input type="hidden" value="<?php echo $total_amount;?>" name="total_amount" />
						
						<ul>
							<li><input type="text" class="input-scroll-3" placeholder="Card number" id="cardNumber" name="card_number" maxlength="16" size="16"></input></li>
							<li><label>Expiration</label> 
								<?php $Sel ='selected="selected"';  ?>
								<select id="CCExpDay" name="exp_month" class="input-scroll-2">
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
								<select id="CCExpMnth" name="exp_year" class="input-scroll-2"> 
									<?php for($i=date('Y');$i< (date('Y') + 30);$i++){ ?>
									<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</li>
							<li><input type="password" class="input-scroll" placeholder="Security Code" id="creditCardIdentifier" name="cvc_number"></input></li>
							<li class="last"><input type="submit" id="submit_btn" class="input-submit-btn" value="Pay With Your Card" onClick="return validatecard();"></input></li>
							<li class="last"><p id="loading" class="input-loading" style="display:none;">This page redirect automatically, please wait...</p></li>
							
							<li class="last"><img src="images/loader.gif" style="display:none;" id="payLoader"></li>
						
						</ul>
						</form>
					</div>
				</div>	
			</div>
		</section>
		<script type="text/javascript">
		function validatecard(){
			var cardNumber=document.getElementById("cardNumber").value.trim();
			var CCExpDay=document.getElementById("CCExpDay").value.trim();
			var CCExpMnth=document.getElementById("CCExpMnth").value.trim();
			var creditCardIdentifier=document.getElementById("creditCardIdentifier").value.trim();
			//var cardType=document.getElementById("cardType").value.trim();
			
			document.getElementById("cardNumber").classList.remove("txt-error");
			document.getElementById("CCExpDay").classList.remove("txt-error");
			document.getElementById("CCExpMnth").classList.remove("txt-error");
			document.getElementById("creditCardIdentifier").classList.remove("txt-error");
			//document.getElementById("cardType").classList.remove("txt-error");
			
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
			/* if(cardType==""){
				document.getElementById("cardType").classList.add("txt-error");
				status++;
			} */
			if(status!=0){
				return false;
			}else{
				document.getElementById("submit_btn").style.display = 'none';
				document.getElementById("loading").style.display = 'block';
			}
		}
		</script>
		
		<script>
			function showLoader(){
				$('#payLoader').css('display','block'); 
			}
		</script>
		
	</body>
</html>
