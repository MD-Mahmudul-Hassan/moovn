<?php
$this->load->view('site/templates/profile_header');
$findpage = $this->uri->segment(2);

$contact_details = array();

if (isset($rider_info->row()->emergency_contact)) {
    if (isset($rider_info->row()->emergency_contact['em_email'])) {
        $contact_details = $rider_info->row()->emergency_contact;
    }
}

$mobile_verification = 'No';
$notVerified = 'Mobile and Email';
if (isset($contact_details['verification']['mobile'])) {
    if ($contact_details['verification']['mobile'] == 'Yes') {
        $mobile_verification = 'Yes';
        $notVerified = str_replace('Mobile and ', '', $notVerified);
    }
}

$email_verification = 'No';
if (isset($contact_details['verification']['email'])) {
    if ($contact_details['verification']['email'] == 'Yes') {
        $email_verification = 'Yes';
        $notVerified = str_replace(' and Email', '', $notVerified);
    }
}
?> 
<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">
                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>
                <div class="col-md-9 nopadding profile_rider_right">
                    <form action="site/rider/update_emergency_contact" name="em_contact_form" id="em_contact_form" method="POST">
                        <div class="col-md-12 rider-pickup-detail">
                            <h2><?php if ($this->lang->line('user_emergency_contact') != '') echo stripslashes($this->lang->line('user_emergency_contact')); else echo 'EMERGENCY CONTACT!'; ?></h2> 

                            <?php if ($email_verification == 'Yes' || $mobile_verification == 'Yes' && isset($rider_info->row()->emergency_contact)) { ?>
                                <div class="emergency-alarm" style="display:none;"><a id="alert_confirmation"  href="javascript:void(0);"><img src="images/site/emergency-alert.png"></a></div>
                            <?php } ?>

                            <div class="col-md-12 emergency-contact">
                                <div class="form-group">

                                    <div class="form-group">
                                        <div class="input-group col-lg-12">
                                            <input type="text" class="form-control required col-lg-12" id="em_name" name="em_name"  placeholder="<?php if ($this->lang->line('user_enter_name') != '') echo stripslashes($this->lang->line('user_enter_name')); else echo 'Enter Name'; ?>"  value="<?php if (isset($contact_details['em_name'])) echo $contact_details['em_name']; ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon element-group">
                                                <select class="form-control grey_bg required " name="em_mobile_code" id="em_mobile_code">
                                                    <?php
                                                    foreach ($countryList as $country) {
                                                        if ($country->dial_code != '') {
                                                            if (isset($contact_details['em_mobile_code'])) {
                                                                $chekDailCode = $contact_details['em_mobile_code'];
                                                            } else {
                                                                $chekDailCode = $rider_info->row()->country_code;
                                                            }
                                                            ?>
                                                            <option <?php if ($chekDailCode == $country->dial_code) echo 'selected="selected"' ?>><?php echo $country->dial_code; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </span>
                                            <input type="text" class="form-control" id="em_mobile" name="em_mobile" placeholder="<?php if ($this->lang->line('rider_profile_mobile_number') != '') echo stripslashes($this->lang->line('rider_profile_mobile_number')); else echo 'Mobile Number'; ?>" value="<?php if (isset($contact_details['em_mobile'])) echo $contact_details['em_mobile']; ?>">
                                            <div class="input-group-addon element-group">
                                                <?php
                                                if ($mobile_verification == 'Yes') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('user_mobile_still_not_verified') != '') echo stripslashes($this->lang->line('user_mobile_still_not_verified')); else echo 'Mobile Number Still Not Verified'; ?>" class="c-active">&emsp;</a>
                                                       <?php
                                                   } else {
                                                       ?>
                                                    <a title="<?php if ($this->lang->line('user_mobile_still_not_verified') != '') echo stripslashes($this->lang->line('user_mobile_still_not_verified')); else echo 'Mobile Number Still Not Verified'; ?>" class="c-inactive">&emsp;</a>
                                                       <?php
                                                   }
                                                   ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">@</span>
                                            <input type="text" class="form-control required email" id="em_email" name="em_email" placeholder="<?php if ($this->lang->line('rider_email_address') != '') echo stripslashes($this->lang->line('rider_email_address')); else echo 'Email Address'; ?>" value="<?php if (isset($contact_details['em_email'])) echo $contact_details['em_email']; ?>" />
                                            <div class="input-group-addon element-group">
                                                <?php
                                                if ($email_verification == 'Yes') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('user_mobile_still_not_verified') != '') echo stripslashes($this->lang->line('user_mobile_still_not_verified')); else echo 'Mobile Number Still Not Verified'; ?>" class="c-active">&emsp;</a>
                                                       <?php
                                                   } else {
                                                       ?>
                                                    <a title="<?php if ($this->lang->line('user_mobile_still_not_verified') != '') echo stripslashes($this->lang->line('user_mobile_still_not_verified')); else echo 'Mobile Number Still Not Verified'; ?>" class="c-inactive">&emsp;</a>
                                                       <?php
                                                   }
                                                   ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <?php if ($email_verification == 'Yes' || $mobile_verification == 'Yes' && isset($rider_info->row()->emergency_contact)) { ?>
                                <p style="display:none;"><strong style="color:red;"><?php if ($this->lang->line('user_alert') != '') echo stripslashes($this->lang->line('user_alert')); else echo 'Alert :'; ?></strong> <?php if ($this->lang->line('user_to_alert_this_person') != '') echo stripslashes($this->lang->line('user_to_alert_this_person')); else echo 'To alert this person just click the'; ?><b style="color:#28cbf9;"> <?php if ($this->lang->line('user_horn') != '') echo stripslashes($this->lang->line('user_horn')); else echo 'horn'; ?></b> <?php if ($this->lang->line('user_icon') != '') echo stripslashes($this->lang->line('user_icon')); else echo 'icon'; ?></p> <br/>
                                <?php } else { ?>
                                <p><?php if ($this->lang->line('user_enter_your_emergency') != '') echo stripslashes($this->lang->line('user_enter_your_emergency')); else echo 'Enter your emergency contact\'s name,mobile number and email here'; ?></p> <br/>
                            <?php } ?>

                            <?php if ($email_verification == 'No' || $mobile_verification == 'No' && isset($rider_info->row()->emergency_contact)) { ?>
                                <p><strong><?php if ($this->lang->line('user_note') != '') echo stripslashes($this->lang->line('user_note')); else echo 'Note:'; ?></strong> <?php if ($this->lang->line('user_your_contact_can_be_alerted') != '') echo stripslashes($this->lang->line('user_your_contact_can_be_alerted')); else echo 'Your contact can be alerted only if it is verified.';?><span style="color:grey;"> <?php if ($this->lang->line('user_now_may_not_alerted') != '') echo stripslashes($this->lang->line('user_now_may_not_alerted')); else echo 'Now may not alerted with'; ?> <?php echo $notVerified; ?> </span></p>
                            <?php } ?>
                            <br/>

                            <?php /* <div class="checkbox emergency-checkbox">
                              <label>
                              <input type="checkbox" value=""> Always share my ride with my contact
                              </label>
                              </div> */ ?>
                        </div>

                        <div class="col-lg-12 text-left sign_driver_text login_inner sign_in_inner">
                            <input type="submit" value="<?php if ($this->lang->line('rides_update_contact') != '') echo stripslashes($this->lang->line('rides_update_contact')); else echo 'UPDATE CONTACT'; ?>" class=" btn1 sign_in_driver">
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>


<script src="js/site/jquery.confirm.js"></script>

<script>
    $(document).ready(function () {
        $("#em_contact_form").validate();
    });


    $("#alert_confirmation").click(function () {
        $.confirm({
            text: "<?php if ($this->lang->line('user_are_you_sure_send_emergency') != '') echo stripslashes($this->lang->line('user_are_you_sure_send_emergency')); else echo 'Are you sure do you want to send emergency alert to this person?'; ?>",
            confirm: function () {
                //alert("You just confirmed.");
                window.location.href = "rider/emergency-alert";
            },
            cancel: function () {
                //alert("You cancelled.");
            }
        });
    });

</script>

<?php
$this->load->view('site/templates/footer');
?> 