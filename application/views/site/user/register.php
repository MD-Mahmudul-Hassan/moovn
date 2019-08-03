<?php  
$this->load->view('site/templates/common_header');

if (is_file('google-login-mats/index.php')){
	require_once 'google-login-mats/index.php';
} 

?>
</head>
<?php
if ($this->lang->line('home_cabily') != '')
	$home_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
else
	$home_cabily = $this->config->item('email_title');
?>
<body class="">
    <div class="logo_sign_up text-center">
        <a class="brand" href="">
            <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $home_cabily; ?>">
        </a>
    </div>
    <div class="container-new">
        <div class="col-lg-7 sign_up2_center">
            <?php /* <div class="col-lg-4">
                <img src="images/site/mobile-tab-imag.png" alt="<?php if ($this->lang->line('rider_signup_img_cabily_app') != '') echo stripslashes($this->lang->line('rider_signup_img_cabily_app')); else echo 'dectar-cabily-app'; ?>">
            </div> */ ?>
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <h1><?php if ($this->lang->line('rider_signup_signup_to_ride') != '') echo stripslashes($this->lang->line('rider_signup_signup_to_ride')); else echo 'SIGN UP TO RIDE'; ?></h1>
                <p><?php if ($this->lang->line('rider_singup_welcome_to_dectar') != '') echo str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('rider_singup_welcome_to_dectar'))); else echo "Welcome to " . $this->config->item('email_title') . ", the easiest way to get around at the tap of a button.";
                    ?></p>
                <p><?php if ($this->lang->line('rider_signup_create_your_account') != '') echo stripslashes($this->lang->line('rider_signup_create_your_account')); else echo 'Create your account and get moving in minutes.'; ?></p>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="sign_up_acc col-lg-5">
            <form name="rider_register_form" id="rider_register_form" action="site/user/register_rider" method="post" enctype="multipart/form-data">
			<?php if (($this->config->item('facebook_app_id') != '' && $this->config->item('facebook_app_secret') != '')|| ($this->config->item('google_client_id') != '' && $this->config->item('google_redirect_url') != '' && $this->config->item('google_client_secret') != '')) { ?>
                <div class="sig_up_fb col-lg-12 nopadd">
					<h2><?php if ($this->lang->line('signup_with') != '') echo stripslashes($this->lang->line('signup_with')); else echo 'Signup with'; ?></h2>
					<div class="clearfix"></div>
					<?php if ($this->config->item('facebook_app_id') != '' && $this->config->item('facebook_app_secret') != '') { ?>
					<div class="col-lg-6 sig_up_fb1">
						<a href="<?php echo base_url().'facebook/user.php'; ?>" class="popup_facebook"><?php if ($this->lang->line('signup_with_facebook') != '') echo stripslashes($this->lang->line('signup_with_facebook')); else echo 'Signup with Facebook'; ?></a>
					</div>
					<?php } ?>
					<?php if($this->config->item('google_client_id') != '' && $this->config->item('google_redirect_url') != '' && $this->config->item('google_client_secret') != '') { ?>
					<div class="col-lg-6 sig_up_fb2">
						<a href="<?php echo $authUrl; ?>" class="popup_google"><?php if ($this->lang->line('signup_with_google') != '') echo stripslashes($this->lang->line('signup_with_google')); else echo 'Signup with Google'; ?></a>	
					</div>
					<?php } ?>
								  
				</div>
				
				
				<h2>
				<?php if ($this->lang->line('rider_profile_account') != '') echo stripslashes($this->lang->line('rider_profile_account')); else echo 'Account'; ?>
				</h2>
			<?php } ?>
				
                <div class="col-lg-12 sign_up_base">
                    <label><span>*</span><?php if ($this->lang->line('rider_email') != '') echo stripslashes($this->lang->line('rider_email')); else echo 'EMAIL'; ?></label>
                    <input type="text" class="form-control required email" name="email" id="email" placeholder="<?php if ($this->lang->line('rider_signup_email_placeholder') != '') echo stripslashes($this->lang->line('rider_signup_email_placeholder')); else echo 'name@example.com'; ?>">
                </div>
                <div class="col-lg-12 sign_up_base">
                    <label><span>*</span><?php if ($this->lang->line('rider_password') != '') echo stripslashes($this->lang->line('rider_password')); else echo 'PASSWORD'; ?></label>
                    <input type="password" class="form-control required"  minlength="6" name="password" id="password" placeholder="<?php if ($this->lang->line('rider_signup_password_placeholder') != '') echo stripslashes($this->lang->line('rider_signup_password_placeholder')); else echo 'At least 6 characters '; ?>">
                </div>
                <div class="col-lg-12 sign_up_base">
                    <label><span>*</span><?php if ($this->lang->line('rider_password') != '') echo stripslashes($this->lang->line('rider_password')); else echo 'PASSWORD'; ?></label>
                    <input type="password" class="form-control required" minlength="6" equalTo="#password" name="confirm_password" id="confirm_password"  placeholder="<?php if ($this->lang->line('rider_signup_password_placeholder') != '') echo stripslashes($this->lang->line('rider_signup_password_placeholder')); else echo 'At least 6 characters '; ?>">
                </div>
                <div class="profile_sign_up col-lg-12 nopadd">
                    <h2><?php if ($this->lang->line('rider_profile_profile') != '') echo stripslashes($this->lang->line('rider_profile_profile')); else echo 'Profile'; ?></h2>
                    <div class="col-lg-12 sign_up_base">
                        <label><span>*</span><?php if ($this->lang->line('rider_signup_name') != '') echo stripslashes($this->lang->line('rider_signup_name')); else echo 'NAME'; ?></label>
                        <div class="col-lg-12 nopadd">
                            <input type="text" name="user_name" id="user_name" class="form-control required" placeholder="<?php if ($this->lang->line('rider_signup_name_placeholder') != '') echo stripslashes($this->lang->line('rider_signup_name_placeholder')); else echo 'Full Name'; ?>">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 sign_up_base">
				<label class="text-left"><span></span><?php if ($this->lang->line('driver_mobile') != '') echo stripslashes($this->lang->line('driver_mobile')); else echo 'Mobile'; ?>
				</label>
						
						<select name="dail_code" id="country_code" tabindex="7" class="required phoneCode form-control" title="<?php if ($this->lang->line('please_enter_countrycode') != '') echo stripslashes($this->lang->line('please_enter_countrycode')); else echo 'Please enter your country code'; ?>" style="width: 36%;" onchange="update_d_country_code();">
							<option value=""><?php if ($this->lang->line('please_enter_countrycode') != '') echo stripslashes($this->lang->line('please_enter_countrycode')); else echo 'Please enter your country code'; ?></option>
							<?php 
							foreach ($countryList as $country) {  
								if($country->dial_code != ''){  
							?>
                            <option data-cCode="<?php echo $country->cca2; ?>" value="<?php echo $country->dial_code; ?>" <?php if($dcurrencyCode == $country->cca2) echo 'selected="selected"';?> ><?php echo $country->dial_code; ?> (<?php echo $country->name; ?>)</option>
                            <?php 
								} 
							}  
							?>
						</select>
						<input type="hidden" name="d_country_code" id="d_country_code" value="<?php echo $dcurrencyCode; ?>" />
						
						
						<input type="text" name="mobile_number" id="mobile_number" class="form-control required number phoneNumber" placeholder="777-777-777" title="<?php if ($this->lang->line('please_enter_mobilenumber') != '') echo stripslashes($this->lang->line('please_enter_mobilenumber')); else echo 'Please enter your mobile number'; ?>" style="width: 60%;">
						
						 <button type="button" class="btn1 category_btn mob_resend_otp" id="otp_send_btn" onclick="sendOtp();" data-otpstatus="begin"><?php  if ($this->lang->line('send_otp') != '')
                                    echo stripslashes($this->lang->line('send_otp'));
                                else
                                    echo 'Send OTP';
                                ?></button>
						<img src="images/indicator.gif" id="sms_loader">
				</div>
				<div class="col-lg-12 sign_up_base">		
					<span id="otpSuccess"></span>
					<span id="temp_otp"></span>
					<span id="otpNumErr"></span>
					<input type="hidden" id="otp_mode" value="<?php echo $this->config->item('twilio_account_type'); ?>"/>
					<input type="hidden" id="otp_phone_number" value=""/>
					<input type="hidden" id="otp_country_code" value=""/>
					<input type="hidden" id="isNumberExists" value=""/>
					
				</div>
				<div class="col-lg-12 sign_up_base otp_container" id="otp_container">
					<label class="text-left"><?php if ($this->lang->line('rider_profile_enter_otp') != '') echo stripslashes($this->lang->line('rider_profile_enter_otp')); else echo 'Enter OTP'; ?> : </label>
					 <input type="text" name="mobile_otp" id="mobile_otp" class="form-control required mob_otp" placeholder="<?php if ($this->lang->line('rider_profile_enter_otp') != '') echo stripslashes($this->lang->line('rider_profile_enter_otp')); else echo 'Enter OTP'; ?>">
					 <button type="button" class="btn1 category_btn mob_resend_otp" onclick="verifyOtp();"><?php if ($this->lang->line('rider_profile_verify_otp') != '') echo stripslashes($this->lang->line('rider_profile_verify_otp')); else echo 'Verify OTP'; ?></button>	
				</div>
			
				
				<div class="col-lg-12 sign_up_base">
					<label><?php if ($this->lang->line('rider_signup_referral_code') != '') echo stripslashes($this->lang->line('rider_signup_referral_code')); else echo 'REFERRAL CODE ( Optional )'; ?></label>
					<div class="col-lg-12 nopadd">
						<input type="text" value="<?php if($this->input->get('ref') != '') echo base64_decode($this->input->get('ref'));?>" class="form-control" name="referal_code" id="referal_code" placeholder="<?php if ($this->lang->line('rider_signup_referral_code_placeholder') != '') echo stripslashes($this->lang->line('rider_signup_referral_code_placeholder')); else echo 'Enter Referral Code If You Have'; ?>">
					</div>
				</div>
                <div class="payment_sign_up col-lg-12 nopadd">
                    <div class="col-lg-12 text-center sign_up2_btn">
                        <input type="submit" id="create_user_btn" class=" sign_up2_btn2" value="<?php if ($this->lang->line('rider_signup_referral_create_account') != '') echo stripslashes($this->lang->line('rider_signup_referral_create_account')); else echo 'Create Account'; ?>">
                        <p><?php if ($this->lang->line('rider_signup_please_fill') != '') echo stripslashes($this->lang->line('rider_signup_please_fill')); else echo 'Please fill out all required'; ?> (<span>*</span>) <?php if ($this->lang->line('rider_signup_fields') != '') echo stripslashes($this->lang->line('rider_signup_fields')); else echo 'fields'; ?> .</p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="sign_up_foot text-center col-lg-12">
                        <p><?php if ($this->lang->line('rider_signup_by_clicking') != '') echo stripslashes($this->lang->line('rider_signup_by_clicking')); else echo 'By clicking “Create Account” , you agree to'; ?> <?php if ($this->lang->line('home_cabily') != '') $welcome_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily'))); else $welcome_cabily = $this->config->item('email_title') . '\'s'; ?> <?php echo $welcome_cabily; ?>
                            <a href="pages/terms-and-conditions"> <?php if ($this->lang->line('rider_signup_terms_condition') != '') echo stripslashes($this->lang->line('rider_signup_terms_condition')); else echo 'Terms and Conditions'; ?> </a>
                            <?php if ($this->lang->line('rider_signup_and') != '') echo stripslashes($this->lang->line('rider_signup_and')); else echo 'and'; ?>
                            <a href="pages/privacy-and-policy"> <?php if ($this->lang->line('rider_signup_privacy_policy') != '') echo stripslashes($this->lang->line('rider_signup_privacy_policy')); else echo 'Privacy Policy.'; ?></a></p>
                    </div>
                </div>
            </form>
        </div>
    </div>


  <script>
	
    $(document).ready(function () {
    $("#rider_register_form").validate({
		submitHandler: function(form) {
			  otp_phone_number=$("#otp_phone_number").val();
			  otp_country_code=$("#otp_country_code").val();
			  phone_code = $('#country_code').val();
			  otp_phone = $('#mobile_number').val();
			  isNumberExists=$("#isNumberExists").val();
			if(isNumberExists=='true'){
				$('#otpNumErr').html('<p style="color:red;"><?php
												if ($this->lang->line('this_mobile_number_exist') != '')
													echo stripslashes($this->lang->line('this_mobile_number_exist'));
												else
													echo 'This mobile number already exist';
												?> !!!</p>');
			}else if(otp_phone_number !=otp_phone || otp_country_code !=phone_code){
				  sendOtp();
			}else{
			 
					 $.ajax({
						type: 'POST',
						url: 'site/sms_twilio/check_is_valid_otp_fields',
						dataType: "json",
						data: {"otp_phone":otp_phone_number,"phone_code":otp_country_code},
						success: function (response) {
						if(response=='success'){
							form.submit();
							
						}else if(response =='exist'){
							$('#otpNumErr').html('<p style="color:red;"><?php
												if ($this->lang->line('this_mobile_number_exist') != '')
													echo stripslashes($this->lang->line('this_mobile_number_exist'));
												else
													echo 'This mobile number already exist';
												?> !!!</p>');
						}
						else{
							  $('#otpNumErr').html('<p style="color:red;"><?php
												if ($this->lang->line('rider_profile_verify_otp') != '')
													echo stripslashes($this->lang->line('rider_profile_verify_otp'));
												else
													echo 'Verify OTP';
												?> !!!</p>');
						}
						
						}
						});
					}
			
		}
	});
			
			
			 $("input[name='mobile_number']").blur(function(){
			
			   otp_phone_number=$("#otp_phone_number").val();
			   otp_country_code=$("#otp_country_code").val();
			   phone_code = $('#country_code').val();
			   otp_phone = $('#mobile_number').val();
			
					if(otp_country_code !='' | otp_phone_number !=''){
							
							if(otp_phone_number!=otp_phone | otp_country_code!=phone_code)
							{
								$("#isNumberExists").val("false");
								$("#otpSuccess,#temp_otp,#otp_container").css('display','none');
								$("#otp_send_btn").html('<?php  if ($this->lang->line('send_otp') != '')
                                    echo stripslashes($this->lang->line('send_otp'));
                                else
                                    echo 'Send OTP';
                                ?>');
								$("#mobile_otp").val('');
								$('#otpNumErr').html('');
							}
					}
			});
        });
   
</script>
<script>
    function sendOtp() {
		var phone_code = $('#country_code').val();
        var otp_phone = $('#mobile_number').val();
        if (phone_code == '') {
            $('#otpNumErr').html('<p style="color:red; background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
                                            if ($this->lang->line('dash_country_code') != '')
                                                echo stripslashes($this->lang->line('dash_country_code'));
                                            else
                                                echo 'Please enter mobile country code';
                                            ?></p>');
        } else if (otp_phone == '') {
            $('#otpNumErr').html('<p style="color:red; background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
                                            if ($this->lang->line('dash_enter_mobile_phone') != '')
                                                echo stripslashes($this->lang->line('dash_enter_mobile_phone'));
                                            else
                                                echo 'Please enter the mobile number';
                                            ?></p>');
        } else {
            $('#otpNumErr').html('');
            $('#sms_loader').css('display', 'inline-block');
            $.ajax({
                type: 'POST',
                url: 'site/sms_twilio/send_otp',
                dataType: "json",
                data: {"otp_phone": otp_phone, "phone_code": phone_code},
                success: function (response) {
					
					$("#otp_phone_number").val(otp_phone);
					$("#otp_country_code").val(phone_code);
                    if (response.status != '0') {
					
						$("#isNumberExists").val("false");
						if(response.status == '2'){
							$("#isNumberExists").val("true");
							$('#otpNumErr').html('<p class="error"><?php
												if ($this->lang->line('this_mobile_number_exist') != '')
													echo stripslashes($this->lang->line('this_mobile_number_exist'));
												else
													echo 'This mobile number already exist';
												?> !!!</p>');
						} else {
							$('#otp_container').css('display','block');
							$('#otp_send_btn').html('<?php
												if ($this->lang->line('dash_resend_otp') != '')
													echo stripslashes($this->lang->line('dash_resend_otp'));
												else
													echo 'Resend OTP';
												?>');
							$('#otpSuccess').html('<p  style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0; color:green;"><?php
												if ($this->lang->line('dash_otp_sent_your_number') != '')
													echo stripslashes($this->lang->line('dash_otp_sent_your_number'));
												else
													echo 'OTP has been sent to your phone number';
												?></p>').css('display','block');;
							if (response.mode == 'sandbox') {
								$("#temp_otp").html('<p  style=" margin: 10px 0 0;"><?php
												if ($this->lang->line('dash_otp_demo_response_msg') != '')
													echo stripslashes($this->lang->line('dash_otp_demo_response_msg'));
												else
													echo 'OTP is in demo mode, so please use this otp <b>';
												?> ' + response.otp + '</b></p>').css('display','block');;
							}
						}
                    } else {
                        $('#otpNumErr').html('<p style="color:red; background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
                                            if ($this->lang->line('dash_otp_failed_response_msg') != '')
                                                echo stripslashes($this->lang->line('dash_otp_failed_response_msg'));
                                            else
                                                echo 'OTP failed to send, please try again';
                                            ?>.</p>');
                    }
                    $('#sms_loader').css('display', 'none');
                }
            });
        }

    }
    function reset_otp() {
    
        $('#country_code').val('');
        $('#mobile_number').val('');
        $('#mobile_otp').val('');
        $('#otpSuccess').html('');
        $("#otp_send_btn").html('<?php  if ($this->lang->line('send_otp') != '')
                                    echo stripslashes($this->lang->line('send_otp'));
                                else
                                    echo 'Send OTP';
                                ?>');
        $("#otp_send_btn").attr("onclick", "sendOtp()");
    
    }

    function verifyOtp() {
        var phone_code = $('#dail_code').val();
        var otp_phone = $('#mobile_number').val();
        $('#sms_loader').css('display', 'none');
			if (phone_code == '') {
				$('#otpNumErr').html('<p style="color:red; background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
												if ($this->lang->line('dash_country_code') != '')
													echo stripslashes($this->lang->line('dash_country_code'));
												else
													echo 'Please enter mobile country code';
												?></p>');
				return false;
			} else if (otp_phone == '') {
				$('#otpNumErr').html('<p style="color:red; background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
												if ($this->lang->line('dash_enter_mobile_phone') != '')
													echo stripslashes($this->lang->line('dash_enter_mobile_phone'));
												else
													echo 'Please enter the mobile number';
												?></p>');
				return false;
			} else if ($("#mobile_otp").val() == '') {
				$("#mobile_otp").css('border-color', 'red');
				return false;
			} else {
				var mobile_otp = $("#mobile_otp").val();
				$('#sms_loader').css('display', 'block');
				$.ajax({
					type: 'POST',
					url: 'site/sms_twilio/otp_verification',
					data: {"otp": mobile_otp,"otp_phone":otp_phone_number,"phone_code":otp_country_code},
					dataType:'json',
					success: function (response) {
						if (response.status == '1') { 
							$('#otpNumErr').html('');
							$('#temp_otp').html('');
							$('#otpSuccess').html('<?php
												if ($this->lang->line('dash_otp_verified_successfully') != '')
													echo stripslashes($this->lang->line('dash_otp_verified_successfully'));
												else
													echo 'OTP has been verified successfully';
												?>.').css('display','block');
							
							$('#mobile_otp').css('border-color','green');
							$('#otpSuccess').css('color','green');
							$('#otp_container').css('display','none');
                            $("#otp_send_btn").html('<?php
												if ($this->lang->line('change_number') != '')
													echo stripslashes($this->lang->line('change_number'));
												else
													echo 'Change Number';
												?>');
                            $("#otp_send_btn").attr("onclick", "reset_otp()");
                          
						} else {
							$('#otpNumErr').html('<p style="color:red;"><?php
												if ($this->lang->line('dash_entered_wrong_otp') != '')
													echo stripslashes($this->lang->line('dash_entered_wrong_otp'));
												else
													echo 'You have entered wrong OTP';
												?></p>');
						}
					}
				});
				$('#sms_loader').css('display', 'none');
			}
    }
function update_d_country_code(){
	var country_code = $('#country_code').find(':selected').attr('data-cCode');
	$('#d_country_code').val(country_code);
}
</script>
</script>

</body>
</html>