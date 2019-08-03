<?php
$this->load->view('site/templates/profile_header');
?> 

<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">
                <!-------Profile side bar ---->  <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>

                <div class="col-lg-7 sign_up2_center edit-rider">
                    <h2><?php if ($this->lang->line('rider_profile_customer_details') != '') echo stripslashes($this->lang->line('rider_profile_customer_details')); else echo 'CUSTOMER DETAILS'; ?></h2>
                    <div class="sign_up_acc col-md-12 edit-details">
                        <h3><?php if ($this->lang->line('rider_profile_account') != '') echo stripslashes($this->lang->line('rider_profile_account')); else echo 'Account'; ?></h3>
                        <div class="col-lg-12 sign_up_base">
                            <label><span>*</span><?php if ($this->lang->line('rider_email') != '') echo stripslashes($this->lang->line('rider_email')); else echo 'EMAIL'; ?></label>
                            <input type="text" class="form-control" placeholder="<?php if ($this->lang->line('rider_signup_email_placeholder') != '') echo stripslashes($this->lang->line('rider_signup_email_placeholder')); else echo 'name@example.com'; ?>">
                        </div>
                        <div class="col-lg-12 sign_up_base">
                            <label><span>*</span><?php if ($this->lang->line('rider_password') != '') echo stripslashes($this->lang->line('rider_password')); else echo 'PASSWORD'; ?></label>
                            <input type="text" class="form-control" placeholder="<?php if ($this->lang->line('rider_signup_password_placeholder') != '') echo stripslashes($this->lang->line('rider_signup_password_placeholder')); else echo 'At least 6 characters'; ?>">
                        </div>
                        <div class="profile_sign_up col-lg-12 nopadd">
                            <h3><?php if ($this->lang->line('rider_profile_profile') != '') echo stripslashes($this->lang->line('rider_profile_profile')); else echo 'Profile'; ?></h3>
                            <div class="col-lg-12 sign_up_base">
                                <div class="profile-image">
                                    <span>
                                        <img src="images/profile_pic.png">
                                    </span>
                                    <input type="file" name="datafile" size="40" class="img-upload">
                                </div>
                                <label><span>*</span><?php  if ($this->lang->line('rider_signup_name') != '') echo stripslashes($this->lang->line('rider_signup_name'));  else  echo 'NAME';?></label>
                                <div class="col-lg-6 nopadd">
                                    <input type="text" class="form-control" placeholder="<?php
                                    if ($this->lang->line('user_first_name') != '') echo stripslashes($this->lang->line('user_first_name')); else echo 'First Name';?>">
                                </div>
                                <div class="col-lg-6 rgtpadd">
                                    <input type="text" class="form-control" placeholder="<?php
                                    if ($this->lang->line('user_last_name') != '') echo stripslashes($this->lang->line('user_last_name')); else echo 'Last Name';?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 sign_up_base">
                            <label><span>*</span><?php
                                if ($this->lang->line('rider_profile_mobile_number') != '') echo stripslashes($this->lang->line('rider_profile_mobile_number')); else echo 'Mobile Number';?></label>
                            <div class="col-lg-3 nopadd">
                                <select class="form-control grey_bg"></select>
                            </div>
                            <div class="col-lg-9 nopadd">
                                <input type="text" class="form-control" placeholder="(201) 555-5555">
                            </div>
                            <div class="col-lg-12 sign_up_base nopadd">
                                <label><span>*</span><?php
                                    if ($this->lang->line('user_language') != '') echo stripslashes($this->lang->line('user_language')); else echo 'LANGUAGE';?></label>
                                <select class="form-control">	
                                    <option><?php
                                        if ($this->lang->line('user_english') != '') echo stripslashes($this->lang->line('user_english')); else echo 'English';?></option>
                                    <option><?php
                                        if ($this->lang->line('user_tamil') != '') echo stripslashes($this->lang->line('user_tamil')); else echo 'Tamil';?></option>
                                    <option><?php
                                        if ($this->lang->line('user_spanish') != '') echo stripslashes($this->lang->line('user_spanish')); else echo 'Spanish'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="payment_sign_up col-lg-12 nopadd edit-details">
                            <h3><?php
                                if ($this->lang->line('rides_payment') != '')
                                    echo stripslashes($this->lang->line('rides_payment'));
                                else
                                    echo 'Payment';
                                ?></h3>
                            <div class="col-lg-12 sign_up_base ">
                                <div class="col-lg-8 nopadd">
                                    <label><span>*</span><?php
                                        if ($this->lang->line('user_credit_card_number') != '')
                                            echo stripslashes($this->lang->line('user_credit_card_number'));
                                        else
                                            echo 'Credit Card Number';
                                        ?></label>
                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456">
                                </div>
                                <div class="col-lg-4 rgtpadd">
                                    <label><span>*</span><?php
                                        if ($this->lang->line('user_cvv') != '')
                                            echo stripslashes($this->lang->line('user_cvv'));
                                        else
                                            echo 'CVV';
                                        ?></label>
                                    <input type="text" class="form-control" placeholder="123">
                                </div>
                            </div>
                            <div class="col-lg-12 sign_up_base ">
                                <div class="col-lg-4 nopadd">
                                    <label><span>*</span><?php
                                        if ($this->lang->line('user_expiration_date') != '')
                                            echo stripslashes($this->lang->line('user_expiration_date'));
                                        else
                                            echo 'Expiration Date';
                                        ?></label>
                                    <select class="form-control"></select>
                                </div>
                                <div class="col-lg-4 rgtpadd">
                                    <label><span></span></label>
                                    <select class="form-control"></select>
                                </div>
                                <div class="col-lg-4 rgtpadd">
                                    <label><span>*</span><?php
                                        if ($this->lang->line('user_postal_code') != '')
                                            echo stripslashes($this->lang->line('user_postal_code'));
                                        else
                                            echo 'Postal Code';
                                        ?></label>
                                    <input type="text" class="form-control" placeholder="94103">
                                </div>
                            </div>
                            <div class="col-lg-12 sign_up_base">
                                <label><?php
                                    if ($this->lang->line('rider_signup_referral_code') != '')
                                        echo stripslashes($this->lang->line('rider_signup_referral_code'));
                                    else
                                        echo 'REFERRAL CODE ( Optional )';
                                    ?></label>
                                <div class="col-lg-12 nopadd">
                                    <input type="text" class="form-control" name="referal_code" id="referal_code" placeholder="
                                    <?php
                                    if ($this->lang->line('rider_signup_referral_code_placeholder') != '')
                                        echo stripslashes($this->lang->line('rider_signup_referral_code_placeholder'));
                                    else
                                        echo 'Enter Referral Code If You Have';
                                    ?>">
                                </div>
                            </div>
                            <div class="col-lg-12 text-center sign_up2_btn">
                                <a href="#"><input type="button" class=" sign_up2_btn2" value="
                                                   <?php
                                                   if ($this->lang->line('rider_profile_update_account') != '')
                                                       echo stripslashes($this->lang->line('rider_profile_update_account'));
                                                   else
                                                       echo 'Update Account';
                                                   ?>"></a>
                            </div> </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>                


<?php
$this->load->view('site/templates/footer');
?> 