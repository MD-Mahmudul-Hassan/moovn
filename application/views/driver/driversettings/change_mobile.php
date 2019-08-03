<?php
$this->load->view('driver/templates/header.php');
?>
<div id="content">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php echo $heading; ?></h6>
                </div>
                <div class="widget_content">
                    <?php
                    $attributes = array('class' => 'form_container left_label', 'id' => 'mobile_form');
                    echo form_open('driver/profile/change_mobile', $attributes)
                    ?>
                    <ul>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="mobile"><?php
                                    if ($this->lang->line('driver_active_mobile_number') != '')
                                        echo stripslashes($this->lang->line('driver_active_mobile_number'));
                                    else
                                        echo 'Active Mobile Number';
                                    ?></label>
                                <div class="form_input">
                                    <p style="background: #dfdfdf; padding: 12px; width: 47%;"><?php if(isset($driverData->row()->dail_code)) echo $driverData->row()->dail_code; if(isset($driverData->row()->mobile_number)) echo $driverData->row()->mobile_number; ?> </p>
									
                                </div>
                            </div>
                        </li>
						
						<li>
                            <div class="form_grid_12">
                                <label class="field_title" for="mobile"><?php
                                    if ($this->lang->line('dash_mobile_number') != '')
                                        echo stripslashes($this->lang->line('dash_mobile_number'));
                                    else
                                        echo 'Mobile Number';
                                    ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="dail_code" placeholder="+91" id="dail_code" type="text" style="width: 10% !important;" tabindex="6" class="required large tipTop" title="<?php
                                    if ($this->lang->line('dash_country_code') != '')
                                        echo stripslashes($this->lang->line('dash_country_code'));
                                    else
                                        echo 'Please enter mobile country code';
                                    ?>" value=""/>
                                    <input name="mobile_number" placeholder="<?php
                                    if ($this->lang->line('admin_rides_mobile_number') != '')
                                        echo stripslashes($this->lang->line('admin_rides_mobile_number'));
                                    else
                                        echo 'Mobile Number';
                                    ?>" id="mobile_number" type="text" tabindex="6" class="required large tipTop" title="<?php
                                    if ($this->lang->line('dash_enter_mobile_phone') != '')
                                        echo stripslashes($this->lang->line('dash_enter_mobile_phone'));
                                    else
                                        echo 'Please enter the mobile number';
                                    ?>" style="width: 38.5% !important;" value=""/>
                                    <button type="button" class="btn_small btn_blue" onclick="sendOtp();" id="otp_send_btn"><?php
                                        if ($this->lang->line('dash_send_otp') != '')
                                            echo stripslashes($this->lang->line('dash_send_otp'));
                                        else
                                            echo 'Send OTP';
                                        ?></button>
                                    <span style="display:none;" id="sms_loader"><img src="images/indicator.gif"  /></span>	
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="mobile_otp"><?php
                                    if ($this->lang->line('dash_enter_otp') != '')
                                        echo stripslashes($this->lang->line('dash_enter_otp'));
                                    else
                                        echo 'Enter OTP';
                                    ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="mobile_otp" id="mobile_otp" type="text" tabindex="2" class="required large tipTop" title="<?php
                                    if ($this->lang->line('dash_enter_new_mobile') != '')
                                        echo stripslashes($this->lang->line('dash_enter_new_mobile'));
                                    else
                                        echo 'Please enter a new mobile';
                                    ?>"/>
                                </div>
                            </div>
                        </li>

                        <li style="text-align: center;">
                            <span id="otpNumErr" style="color:red;"></span> <br/>
                            <span id="temp_otp"></span>
                            <span id="otpSuccess" style="color:green;"></span>
                            <input type="hidden" id="otp_mode" value="<?php echo $this->config->item('twilio_account_type'); ?>"/>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <div class="form_input">
                                    <button type="button" onclick="verifyOtp();" class="btn_small btn_blue"><span><?php
                                            if ($this->lang->line('dash_change') != '')
                                                echo stripslashes($this->lang->line('dash_change'));
                                            else
                                                echo 'Change';
                                            ?></span></button>
                                </div>
                            </div>
                        </li>
                    </ul>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span>
</div>
</div>

<script>


    function sendOtp(otp_number) {
        var phone_code = $('#dail_code').val();
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
                url: baseURL + 'driver/sms_twilio/send_otp',
                dataType: "json",
                data: {"otp_phone": otp_phone, "phone_code": phone_code},
                success: function (response) {
                    if (response != 'error') {
						if(response == 'exist'){
							$('#otpNumErr').html('<p class="error"><?php
												if ($this->lang->line('this_mobile_number_exist') != '')
													echo stripslashes($this->lang->line('this_mobile_number_exist'));
												else
													echo 'This mobile number already exist';
												?> !!!</p>');
						} else {
					
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
												?></p>');
							if ($('#otp_mode').val() == 'sandbox') {
								$("#temp_otp").html('<p  style=" margin: 10px 0 0;"><?php
												if ($this->lang->line('dash_otp_demo_response_msg') != '')
													echo stripslashes($this->lang->line('dash_otp_demo_response_msg'));
												else
													echo 'OTP is in demo mode, so please use this otp';
												?> ' + response + '</p>');
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
                url: baseURL + 'driver/sms_twilio/otp_verification',
                data: {"otp": mobile_otp},
                success: function (response) {
                    if (response == 'success') { 
                        $('#otpSuccess').html('<?php
                                            if ($this->lang->line('dash_otp_verified_successfully') != '')
                                                echo stripslashes($this->lang->line('dash_otp_verified_successfully'));
                                            else
                                                echo 'OTP has been verified successfully';
                                            ?>.');
                        $("#mobile_form").submit();
                    } else {
                        $('#otpNumErr').html('<?php
                                            if ($this->lang->line('dash_entered_wrong_otp') != '')
                                                echo stripslashes($this->lang->line('dash_entered_wrong_otp'));
                                            else
                                                echo 'You have entered wrong OTP';
                                            ?>');
                    }
                }
            });
            $('#sms_loader').css('display', 'none');
        }
    }
</script>

<?php
$this->load->view('driver/templates/footer.php');
?>