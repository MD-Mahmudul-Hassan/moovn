<?php
$this->load->view('site/templates/common_header');
?>
</head>
<body class="">
    <div class="logo_sign_up text-center">
        <a class="brand" href="">
            <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $this->config->item('email_title'); ?>">
        </a>
    </div>
    <div class="container-new">
        <div class="col-lg-12 sign_up2_center bexti_sign_up_new">
            <h1>JUST ONE MORE STEP !</h1>
        </div>
        <div class="clearfix"></div>
        <div class="sign_up_acc col-lg-5">
            <form name="rider_register_form" id="rider_register_form" action="site/user/register_rider" method="post" enctype="multipart/form-data">
				<?php 
					if($this->session->userdata('social_email_name') == ''){
				?>
                <div class="col-lg-12 sign_up_base">
                    <label><span>*</span>EMAIL</label>
                    <input type="text" class="form-control required email" name="email" id="email" placeholder="name@example.com">
                </div>
				<?php 
				} else {
				?>
				<input type="hidden" name="email" id="email" value="<?php echo $this->session->userdata('social_email_name'); ?>" />
				<?php 
				}
				?>
				
                <div class="col-lg-12 sign_up_base">
                    <label><span>*</span>PASSWORD</label>
                    <input type="password" class="form-control required"  minlength="6" name="password" id="password" placeholder="At least 6 characters">
                </div>
                <div class="col-lg-12 sign_up_base">
                    <label><span>*</span>PASSWORD</label>
                    <input type="password" class="form-control required" minlength="6" equalTo="#password" name="confirm_password" id="confirm_password"  placeholder="At least 6 characters">
                </div>
                <div class="profile_sign_up col-lg-12 nopadd">
					<?php 
					if($this->session->userdata('social_login_name') == ''){
					?>
                    <div class="col-lg-12 sign_up_base">
                        <label><span>*</span>NAME</label>
                        <div class="col-lg-12 nopadd">
                            <input type="text" name="user_name" id="user_name" class="form-control required" placeholder="Full Name">
                        </div>
                    </div>
					<?php 
					} else {
					?>
						<input type="hidden" name="user_name" id="user_name" value="<?php echo $this->session->userdata('social_login_name');?>" />
					<?php 
					}
					?>
                </div>
                
					<?php 
					if($this->session->userdata('loginUserType') != ''){ 
					?>
					<input type="hidden" name="user_type" id="user_type" value="<?php echo $this->session->userdata('loginUserType');?>" />
					<?php 
					}
					?>
					<?php 
					if($this->session->userdata('social_image_name') != ''){ 
					?>
					<input type="hidden" name="user_image" value="<?php echo $this->session->userdata('social_image_name');?>" />
					<?php 
					}
					?>
					<?php 
					if($this->session->userdata('social_user_id') != ''){ 
					?>
					<input type="hidden" name="fb_user_id" value="<?php echo $this->session->userdata('social_user_id');?>" />
					<?php 
					}
					?>
			
			
				<div class="col-lg-12 sign_up_base">
					<label><span>*</span>Mobile Number</label>
					<input type="text" name="dail_code" placeholder="+91" id="country_code" class="form-control required phoneCode" placeholder="+91"  title="Please enter your county code" style="width:15%"/>
					<input type="text" name="mobile_number" id="mobile_number" class="form-control required number phoneNumber" placeholder="777-777-777" title="Please enter your mobile number" style="width:55%">
					<button type="button" class="btn1 category_btn mob_resend_otp" id="otp_send_btn" onclick="sendOtp();">Send OTP</button>
					<img src="images/indicator.gif" id="sms_loader">
				</div>
				<div class="col-lg-12 sign_up_base otp_container" id="otp_container">
					<label class="text-left">Enter OTP : </label>
					<input type="text" name="mobile_otp" id="mobile_otp" class="form-control required mob_otp" placeholder="Please enter otp">
					 <button type="button" class="btn1 category_btn mob_resend_otp" onclick="verifyOtp();">Verify OTP</button>	
				</div>
				<div class="col-lg-12 sign_up_base">		
					<span id="otpSuccess"></span>
					<span id="temp_otp"></span>
					<span id="otpNumErr" class="error"></span>
					<input type="hidden" id="otp_mode" value="<?php echo $this->config->item('twilio_account_type'); ?>"/>
					<input type="hidden" id="otp_phone_number" value=""/>
					<input type="hidden" id="otp_country_code" value=""/>
				</div>
				
                <div class="payment_sign_up col-lg-12 nopadd">
                    <div class="col-lg-12 text-center sign_up2_btn">
						<input type="submit" class=" sign_up2_btn2" value="Complete Registration">
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
						  
						  if(otp_phone_number !=otp_phone || otp_country_code !=phone_code){
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
								}else{
									  $('#otpNumErr').html('<p class="error"><?php
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
			$("#otpSuccess,#temp_otp,#otp_container").css('display','none');
			$("#otp_send_btn").html('Send OTP');
			$("#mobile_otp").val('');
			$('#otpNumErr').html('');
			}
			}
			});
        });
   

    function sendOtp() {
        var phone_code = $('#country_code').val();
        var otp_phone = $('#mobile_number').val();
        if (phone_code == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
                                            if ($this->lang->line('dash_country_code') != '')
                                                echo stripslashes($this->lang->line('dash_country_code'));
                                            else
                                                echo 'Please enter mobile country code';
                                            ?></p>');
        } else if (otp_phone == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
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
					console.log(response);
					$("#otp_phone_number").val(otp_phone);
					$("#otp_country_code").val(phone_code);
                    if (response != 'error') {
					
						if(response == 'exist'){
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
							if ($('#otp_mode').val() == 'sandbox') {
								$("#temp_otp").html('<p  style=" margin: 10px 0 0;"><?php
												if ($this->lang->line('dash_otp_demo_response_msg') != '')
													echo stripslashes($this->lang->line('dash_otp_demo_response_msg'));
												else
													echo 'OTP is in demo mode, so please use this otp <b>';
												?> ' + response + '</b></p>').css('display','block');;
							}
						}
                    } else {
                        $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
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

    function verifyOtp() {
        var phone_code = $('#dail_code').val();
        var otp_phone = $('#mobile_number').val();
        $('#sms_loader').css('display', 'none');
        if (phone_code == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
                                            if ($this->lang->line('dash_country_code') != '')
                                                echo stripslashes($this->lang->line('dash_country_code'));
                                            else
                                                echo 'Please enter mobile country code';
                                            ?></p>');
            return false;
        } else if (otp_phone == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php
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
                data: {"otp": mobile_otp},
                success: function (response) {
                    if (response == 'success') { 
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
                    } else {
                        $('#otpNumErr').html('<p class="error"><?php
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
</script>
	
<style>
.sign_up_acc {
    padding-top: 0;
}
.sign_up2_center h1 {
    margin-bottom: 0;
	padding-top: 0;
	text-align: center;
}
.sign_up2_center {
    padding-top: 0;
}
.payment_sign_up {
    padding-top: 0 !important;
}
</style>
</body>
</html>