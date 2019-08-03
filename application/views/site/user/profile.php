<?php
$this->load->view('site/templates/profile_header');
error_reporting(E_ERROR);
?>
<?php
if (isset($rider_info->row()->image)) {
    if ($rider_info->row()->image != '') {
        $profilePic = base_url() . USER_PROFILE_IMAGE . $rider_info->row()->image;
    } else {
        $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
    }
} else {
    $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
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

                <div class="col-lg-7 sign_up2_center edit-rider">
                    <h2><?php if ($this->lang->line('rider_profile_customer_details') != '') echo stripslashes($this->lang->line('rider_profile_customer_details')); else echo 'CUSTOMER DETAILS'; ?></h2>
                    <?php
                    /* echo validation_errors();
                    $attributes = array('class' => 'form_container left_label', 'id' => 'profile_update_form', 'method' => 'post', 'enctype' => 'multipart/form-data');
                    echo form_open_multipart('site/rider/update_rider_profile', $attributes) */
                    ?>
					<form action="site/rider/update_rider_profile" id="profile_update_form" method="post" enctype="multipart/form-data">
                    <div class="sign_up_acc col-md-12 edit-details">
                        <h3>	<?php if ($this->lang->line('rider_profile_account') != '') echo stripslashes($this->lang->line('rider_profile_account')); else echo 'Account'; ?></h3>

                        <div class="col-lg-12 sign_up_base">
                            <label><span>*</span><?php if ($this->lang->line('rider_email') != '') echo stripslashes($this->lang->line('rider_email')); else echo 'EMAIL'; ?></label>
                            <input disabled name="email" type="text" class="form-control" placeholder="<?php if ($this->lang->line('rider_signup_email_placeholder') != '') echo stripslashes($this->lang->line('rider_signup_email_placeholder')); else echo 'name@example.com'; ?>" value="<?php echo $rider_info->row()->email; ?>">
                        </div>
						
						<div class="col-lg-12 sign_up_base">
                            <label><span>*</span><?php if ($this->lang->line('rider_signup_name') != '') echo stripslashes($this->lang->line('rider_signup_name')); else echo 'NAME'; ?></label>
                            <input name="user_name" id="user_name" type="text" class="form-control required" placeholder="<?php if ($this->lang->line('profile_user_name_placeholder') != '') echo stripslashes($this->lang->line('profile_user_name_placeholder')); else echo 'User Name'; ?>" value="<?php echo $rider_info->row()->user_name; ?>">
                        </div>
                        <div class="col-lg-12 sign_up_base">
							<label><span>*</span>	<?php if ($this->lang->line('old_password_upper') != '') echo stripslashes($this->lang->line('old_password_upper')); else echo 'OLD PASSWORD'; ?></label>
                            <input name="old_password" type="password" class="form-control" placeholder="<?php if ($this->lang->line('old_password') != '') echo stripslashes($this->lang->line('old_password')); else echo 'Old Password'; ?>">
                            <label><span>*</span>	<?php if ($this->lang->line('rider_profile_change_password') != '') echo stripslashes($this->lang->line('rider_profile_change_password')); else echo 'CHANGE PASSWORD'; ?></label>
                            <input name="password" type="password" class="form-control" placeholder="<?php if ($this->lang->line('rider_profile_new_password') != '') echo stripslashes($this->lang->line('rider_profile_new_password')); else echo 'New password'; ?>">
                            <label><span>*</span><?php if ($this->lang->line('rider_profile_confirm_password') != '') echo stripslashes($this->lang->line('rider_profile_confirm_password')); else echo 'CONFIRM PASSWORD'; ?></label>
                            <input name="confirm_password" type="password" style='margin-bottom: 10px !important' class="form-control" placeholder="<?php if ($this->lang->line('rider_profile_confirm_password_lower') != '') echo stripslashes($this->lang->line('rider_profile_confirm_password_lower')); else echo 'Confirm password'; ?>">
                            <span id='show_password_error' style="color:red; "></span>
                        </div>
                        <div class="profile_sign_up col-lg-12 nopadd">
                            <h3><?php if ($this->lang->line('rider_profile_profile') != '') echo stripslashes($this->lang->line('rider_profile_profile')); else echo 'Profile'; ?></h3>

                            <div class="col-lg-12 sign_up_base">
                                <div class="profile-image">
                                    <div  id="image-holder"></div> 
                                    <div style="color:red;margin-bottom: 10px" id="ErrNotify"></div>
                                    <?php
                                    $profile_pic_path = $rider_info->row()->image;
                                    if (!empty($profile_pic_path)) {
                                        ?><img src='<?php echo base_url() . USER_PROFILE_IMAGE . $profile_pic_path ?>' name='profile_pic' style="width:100px;height:100px;margin-bottom: 10px;border-radius:50%" id='old_prof_pic'>
                                    <?php } else {
                                        ?>
                                        <img src='<?php echo base_url() . USER_PROFILE_IMAGE_DEFAULT ?>' name='default_pic' style="width:100px;height:100px;margin-bottom: 10px;border-radius:50%" id='default_pic'>
                                        <?php
                                    }
                                    ?>
<!--                                    <input type="file" class="media_image" name="fileToUpload" size="40" class="img-upload">-->
                                    <label><?php if ($this->lang->line('rider_profile_change_profile') != '') echo stripslashes($this->lang->line('rider_profile_change_profile')); else echo 'Change Profile Picture'; ?></label> <input id="fileUpload"  name="image" class="media_image" type="file" tabindex="2" class="large tipTop" title=" <?php if ($this->lang->line('user_please_upload_notification_image') != '') echo stripslashes($this->lang->line('user_please_upload_notification_image')); else echo 'Please upload Notification Image'; ?>"/>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-12 sign_up_base">
                            <label><?php if ($this->lang->line('rider_profile_mobile_number') != '') echo stripslashes($this->lang->line('rider_profile_mobile_number')); else echo 'Mobile Number'; ?><span id="cancel_otp" style="display: none;cursor: pointer"><?php if ($this->lang->line('profile_mobile_number_edit_cancel_option') != '') echo stripslashes($this->lang->line('profile_mobile_number_edit_cancel_option')); else echo 'Cancel'; ?></span>
                                <span id="firstVerify" style="display: none;color: red"><?php if ($this->lang->line('driver_verify_your_otp') != '') echo stripslashes($this->lang->line('driver_verify_your_otp')); else echo 'Verify Your OTP, Try Again!'; ?></span></label>
                            <p id="show_mobile"><?php echo $rider_info->row()->phone_number; ?><span id="edit_mobile" style="cursor:pointer"><?php if ($this->lang->line('rider_profile_edit') != '') echo stripslashes($this->lang->line('rider_profile_edit')); else echo 'Edit'; ?></span></p>

                            <div id='change_mobile_number' style="display: none">
                                 <div class="col-lg-2 nopadd">
                                    <select class="form-control grey_bg required" name="country_code" id="country_code">
                                        <?php
                                        $c_code = $rider_info->row()->country_code;
                                        if (!empty($c_code)) {
                                            ?>
                                            <option value="<?php echo $rider_info->row()->country_code; ?>"><?php echo $rider_info->row()->country_code; ?></option>
                                            <?php
                                        } else {
                                            ?> <option value=""><?php if ($this->lang->line('user_country_code') != '') echo stripslashes($this->lang->line('user_country_code')); else echo 'Country Code...'; ?></option>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        foreach ($countryLists as $country) {
                                            if ($country->dial_code != '') {
                                                ?>
                                                <option value="<?php echo $country->dial_code; ?>"><?php echo $country->dial_code; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div> 
                                <div class="col-lg-4 nopadd" style="margin-left:5px">
                                    <input name="mNumber" type="text" class="number required form-control mNumber" placeholder="(201) 555-5555" value="<?php echo $rider_info->row()->phone_number; ?>">
                                </div>
                                <div class="col-lg-5 nopadd" style="margin-left:5px; margin-top: 13px">
                                    <a href="javascript:void(0)" id="otp_send_btn" onclick="sendOtp()"><?php if ($this->lang->line('rider_profile_send_otp') != '') echo stripslashes($this->lang->line('rider_profile_send_otp')); else echo 'Send OTP'; ?></a>
                                    <span style="display:none;" id="sms_loader"><img src="images/indicator.gif"  /></span>
                                </div>
								 <div class="col-lg-12 sign_up_base enter_otp" id='otpblock'>
                                    <label><span>*</span><?php if ($this->lang->line('rider_profile_enter_otp') != '') echo stripslashes($this->lang->line('rider_profile_enter_otp')); else echo 'Enter OTP'; ?></label>

                                    <div class="col-lg-8 nopadd" style="margin-left:5px">
                                        <input name="otpNumber" class="otpNumber" type="text" class="form-control" placeholder="">
                                        <a href="javascript:void(0)" id="otp_send_btn" onclick="verifyOtp()"><?php if ($this->lang->line('rider_profile_verify_otp') != '') echo stripslashes($this->lang->line('rider_profile_verify_otp')); else echo 'Verify OTP'; ?></a>
                                    </div>


                                </div>
								
                            </div>
                        </div>

                        <div class="payment_sign_up col-lg-12 nopadd edit-details">
                            <div class="col-lg-12 text-center sign_up2_btn">
							   <input type="hidden" id="riderId" value=" <?php echo $rider_info->row()->_id; ?>">
                               <span id="otpSuccess" style="color:green;"></span>
							   <span id="otpNumErr" style="color:red;"></span> <br/>
							   <span id="temp_otp"></span>
                                <a href="javascript:void(0)">
									<input type="button" id="profileSbmitBtn" onclick="return checkFormfields();" class="sign_up2_btn2 profile_submit" value="<?php if ($this->lang->line('rider_profile_update_account') != '') echo stripslashes($this->lang->line('rider_profile_update_account')); else echo 'Update Account'; ?>">
								</a>
                            </div>

                        </div>
                        <div class="col-lg-12 sign_up_base">
                            <input type="hidden" id="riderId" value=" <?php echo $rider_info->row()->_id; ?>">
                            <input type="hidden" name="otpVerified" id='otpVerified' value='false'>
                            <input type="hidden" id="otp_mode" value="<?php echo $this->config->item('twilio_account_type'); ?>"/>
                            <input type='hidden' id="changed_mobile_number" name='changed_number'>
                            <input type="hidden" name="countryCodeIfAlreadyExists" value="<?php echo $rider_info->row()->country_code; ?>">
                            <input type="hidden" name="mobileNumberIfAlreadyExists" value="<?php echo $rider_info->row()->phone_number; ?>">
                            <input type="hidden" name="isMobileNumberChanged" value="">
							<input type='hidden' id="isEditing" name='isEditing'>
                        </div>
                    </div>
					</form>
                </div>
            </div>
        </section>
    </div>
</div>                

<script>
	
	
    function sendOtp(otp_number) {
        var phone_code = $('#country_code').val();
        var otp_phone = $('.mNumber').val();
        var rider_id = $("#riderId").val();
        if (phone_code == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_enter_mobile_code_number') != '') echo stripslashes($this->lang->line('rider_profile_enter_mobile_code_number')); else echo 'Please enter mobile code number'; ?></p>');
        } else if (otp_phone == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_enter_phone_number') != '') echo stripslashes($this->lang->line('rider_profile_enter_phone_number')); else echo 'Please enter phone number'; ?></p>');
        } else {
            $('#otpNumErr').html('');
            $('#sms_loader').css('display', 'inline-block');
            $.ajax({
                type: 'POST',
                url: 'site/sms_twilio/send_otp',
                dataType: "json",
                data: {"otp_phone": otp_phone, "phone_code": phone_code, "riderId": rider_id},
                success: function (response) {
					
                    if (response.status != '0') {
                       if(response.status == '2'){
							$("#isNumberExists").val("true");
							$('#otpNumErr').html('<p class="error"><?php
												if ($this->lang->line('this_mobile_number_exist') != '')
													echo stripslashes($this->lang->line('this_mobile_number_exist'));
												else
													echo 'This mobile number already exist';
												?> !!!</p>');
						}else if ($('#otp_mode').val() == 'sandbox') {
							$('#otp_send_btn').text('<?php if ($this->lang->line('rider_profile_resend_otp') != '') echo stripslashes($this->lang->line('rider_profile_resend_otp')); else echo 'Resend OTP'; ?>');
							$("#temp_otp").html('<p  style=" margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_otp_is_in_demo_mode') != '') echo stripslashes($this->lang->line('rider_profile_otp_is_in_demo_mode')); else echo 'OTP is in demo mode, only the registed mobile number will receive OTP code, For other number use this'; ?>'+' ' + response.otp + '</p>');
							$(".enter_otp").css("display", 'block');
                        }else{
							  $('#otpSuccess').html('<p  style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0; color:green;"><?php if ($this->lang->line('rider_profile_otp_sent') != '') echo stripslashes($this->lang->line('rider_profile_otp_sent')); else echo 'OTP has been sent to your phone number'; ?></p>');
						}
                     
                    } else {
                        $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_otp_failed') != '') echo stripslashes($this->lang->line('rider_profile_otp_failed')); else echo 'OTP failed to send, please try again.'; ?></p>');
                    }
                    $('#sms_loader').css('display', 'none');
                }
            });
        }

    }

    function verifyOtp() {

        var phone_code = $('#country_code').val();
        var otp_phone = $('.otpNumber').val();
        var rider_id = $("#riderId").val();
        var phone_number = $('.mNumber').val();
        $('#sms_loader').css('display', 'none');
        if (phone_code == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_enter_mobile_code_number') != '') echo stripslashes($this->lang->line('rider_profile_enter_mobile_code_number')); else echo 'Please enter mobile code number'; ?></p>');
            return false;
        } else if (otp_phone == '') {
            $('#otpNumErr').html('<p  class="error" style="background: none repeat scroll 0 0 #fff; margin: 10px 0 0;"><?php if ($this->lang->line('rider_profile_enter_phone_number') != '') echo stripslashes($this->lang->line('rider_profile_enter_phone_number')); else echo 'Please enter phone number'; ?>Please enter phone number</p>');
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
                data: {"otp": otp_phone, "riderId": rider_id},
                dataType: "json",
                success: function (response) {
                
                    if (response.status == '1') {
						      $("#isEditing").val('false')
                        $('#otpSuccess').html('<?php if ($this->lang->line('rider_profile_otp_verified') != '') echo stripslashes($this->lang->line('rider_profile_otp_verified')); else echo 'OTP has been verified successfully.'; ?>');
                        // $("#mobile_form").submit();
                        $("#otpVerified").val('true');
                        $('#otpNumErr,#firstVerify,#temp_otp').html("");
                        $("#changed_mobile_number").val(phone_number);
                        $("#otpblock").css('display','none');
                    } else {
                        $('#otpSuccess').html("");
                        $('#otpNumErr').html("<?php if ($this->lang->line('dash_entered_wrong_otp') != '') echo stripslashes($this->lang->line('dash_entered_wrong_otp')); else echo 'You have entered wrong OTP'; ?>");
                        $("#otpVerified").val('false');
                    }
                }
            });
            $('#sms_loader').css('display', 'none');
        }
    }
</script>
<script>
    $(document).ready(function () {

        $(".profile_submit").click(function () {

            $isMChanged = $("input[name='isMobileNumberChanged']").val();
            $otp_verified = $("#otpVerified").val();
            isEditing=$("#isEditing").val();
		   if(isEditing=='false'){
			   $("#profile_update_form").submit();
		   }else
		   if ($isMChanged == 'changed' && $otp_verified != 'true') {
                $("#firstVerify").css('display', 'block');
                return false;
            } else {
				var pasChk = $('#show_password_error').html().trim(); 
				if(pasChk != ''){
					alert('<?php if ($this->lang->line('rider_please_check_password_fields') != '') echo stripslashes($this->lang->line('rider_please_check_password_fields')); else echo 'Please check password fields'; ?>');
					return false;
				} else { 
					if($("#user_name").val() != ''){
						$('#profile_update_form').submit();
					} else {
						alert('<?php if ($this->lang->line('rider_name_empty') != '') echo stripslashes($this->lang->line('rider_name_empty')); else echo 'Your name is empty!'; ?>');
					}
				}
            }
        });
        $("#country_code,input[name='mNumber']").blur(function () {

            var changedCountryCode = $("#country_code").val();
            var changedMobileNumber = $("input[name='mNumber']").val();
            var ccAlias = $("input[name='countryCodeIfAlreadyExists']").val();
            var mnAlias = $("input[name='mobileNumberIfAlreadyExists']").val();
            $isChanged = (ccAlias == '' && mnAlias == '') ? (changedCountryCode != '' | changedMobileNumber != '') : (changedCountryCode != ccAlias | changedMobileNumber != mnAlias);
            $isChanged ? $("input[name='isMobileNumberChanged']").val('changed') : $("input[name='isMobileNumberChanged']").val('');
        });
	    $(".media_image").change(function (e) {
            e.preventDefault();
            if (typeof (FileReader) != "undefined") {
                var image_holder = $("#image-holder");
                image_holder.empty();
                var reader = new FileReader();
                reader.onload = function (e) {

                    var res = e.target.result;
                    var ext = res.substring(11, 14);
                    extensions = ['jpg', 'jpe', 'gif', 'png', 'bmp'];
                    if ($.inArray(ext, extensions) !== -1) {
                        var image = new Image();
                        image.src = e.target.result;

                        image.onload = function () {
                            if (this.width >= 75 && this.height >= 42) {
                                $("#loadedImg").css("display", "none");
                                $("<img />", {
                                    "src": e.target.result,
                                    "id": "thumb-image",
                                    "style": "width:100px;height:100px;margin-top:20px;border-radius: 5px;border-radius: 50%;",
                                }).appendTo(image_holder);
                                $('#ErrNotify').html('');
                                $("#old_prof_pic").remove();
                                $("#default_pic").remove();

                            } else {
                                $('#ErrNotify').html("<?php if ($this->lang->line('user_upload_image_too_small') != '') echo stripslashes($this->lang->line('user_upload_image_too_small')); else echo 'Upload Image Too Small. Please Upload Image Size More than or Equalto 75 X 42 .'; ?>");
                            }
                        };
                    }
                    else {
                        $('#ErrNotify').html("<?php if ($this->lang->line('user_please_select_an_image') != '') echo stripslashes($this->lang->line('user_please_select_an_image')); else echo 'Please Select an Image file'; ?>");
                    }
                }
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);
            }
        });

        $("input[name='confirm_password']").keyup(function () {
            $pwd = $("input[name='password']").val();
            $confirm_pwd = $("input[name='confirm_password']").val();
            if ($pwd != $confirm_pwd) {
                $("#show_password_error").text("<?php if ($this->lang->line('user_password_doesnt_matches') != '') echo stripslashes($this->lang->line('user_password_doesnt_matches')); else echo 'Password Doesn\'t matches'; ?>")
				return false;
            } else {
                $("#show_password_error").text('');
            }

        });

        $("#edit_mobile").click(function () {
            $("#change_mobile_number").css("display", "block");
            $("#show_mobile").css("display", 'none');
            $("#cancel_otp").css("display", "inline-block");
			$("#isEditing").val('true');
        });
        $("#cancel_otp").click(function () {
            $("#change_mobile_number").css("display", "none");
            $("#show_mobile").css("display", 'block');
            $("#otpNumErr").css("display", 'none');
            $("#cancel_otp").css("display", "none");
            $("#otpVerified").val('false');
			$("#isEditing").val('false');
			$("#otpSuccess,#temp_otp").html('');
			$("#firstVerify").css("display","none");
        });


    });


</script>


<?php
$this->load->view('site/templates/footer');
?> 