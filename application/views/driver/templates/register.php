<?php
$this->load->view('site/templates/common_header');
?>
<?php
if ($this->lang->line('driver_register_active') != '')
    $active=stripslashes($this->lang->line('driver_register_active'));
else
    $active='Active';
if ($this->lang->line('driver_register_inactive') != '')
    $inactive=stripslashes($this->lang->line('driver_register_inactive'));
else
    $inactive='Inactive';
?>


<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<link href="css/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="css/site/screen.css">
<style>
.sign_up_base .onoffswitch-inner:before {
  content: "<?php echo $active; ?>";
  
}

.sign_up_base .onoffswitch-inner:after {
  content: "<?php echo $inactive; ?>";
 
}
</style>
</head>
<body class="">
    <div class="driver_detail_base">
        <div class="container-new">
            <div class="driver_details_logo text-center">
                <?php
                if ($this->lang->line('home_cabily') != '')
                    $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                else
                    $sitename = $this->config->item('email_title');
                ?>
				 <a href="<?php echo base_url(); ?>">
					<img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $sitename; ?>">
				</a>
            </div>
            <div class="col-lg-5 driver_detail_center text-center">
                <div class="driver_form_start">
                    <h1><?php
                        if ($this->lang->line('driver_sign_up_today') != '')
                            echo stripslashes($this->lang->line('driver_sign_up_today'));
                        else
                            echo 'Sign Up Today';
                        ?></h1>
                    <h2><?php
                        if ($this->lang->line('driver_tell_us') != '')
                            echo stripslashes($this->lang->line('driver_tell_us'));
                        else
                            echo 'Tell us a bit about yourself';
                        ?></h2>
                </div>

                <form name="driver_register_form" id="driver_register_form" action="driver/profile/register" method="post" enctype="multipart/form-data">

                    <div class="col-lg-12 nopadd driver_sign_up_form text-center">


                        <input type="hidden" name="driver_location" id="driver_location" value="<?php if (isset($locationDetail->_id)) echo $locationDetail->_id; ?>" class="required"/>
                        <input type="hidden" name="category" id="category" value="<?php if (isset($categoryDetail->_id)) echo $categoryDetail->_id; ?>" class="required"/>

                        <div class="headline-hr"></div>
                        <span class="bdr_bg"><h3><?php
                                if ($this->lang->line('driver_login_details') != '')
                                    echo stripslashes($this->lang->line('driver_login_details'));
                                else
                                    echo 'Login Details';
                                ?></h3></span>
                        <div class="sign_up_block col-lg-12 nopadd">
                            <div class="col-lg-12 sign_up_base">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('rider_signup_name') != '')
                                        echo stripslashes($this->lang->line('rider_signup_name'));
                                    else
                                        echo 'Name';
                                    ?></label>
                                <input name="driver_name" id="driver_name" type="text" tabindex="2" class="required form-control" placeholder="<?php
                                if ($this->lang->line('driver_your_name') != '')
                                    echo stripslashes($this->lang->line('driver_your_name'));
                                else
                                    echo 'Your Name';
                                ?>"/>
                            </div>

                            <div class="col-lg-12 sign_up_base">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('cms_email') != '')
                                        echo stripslashes($this->lang->line('cms_email'));
                                    else
                                        echo 'Email';
                                    ?></label>
                                <input name="email" id="email" type="text" tabindex="3" class="required form-control email" placeholder="<?php
                                if ($this->lang->line('driver_name_placeholder') != '')
                                    echo stripslashes($this->lang->line('driver_name_placeholder'));
                                else
                                    echo 'name@email.com';
                                ?>"/>
                            </div>

                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_password_ucfirst') != '')
                                        echo stripslashes($this->lang->line('driver_password_ucfirst'));
                                    else
                                        echo 'Password';
                                    ?></label>
                                <input name="password" minlength="6" id="password" type="password" tabindex="4" class="required form-control"  placeholder="<?php
                                if ($this->lang->line('rider_signup_password_placeholder') != '')
                                    echo stripslashes($this->lang->line('rider_signup_password_placeholder'));
                                else
                                    echo 'At least 6 characters';
                                ?>" />
                            </div>

                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_retype_pwd') != '')
                                        echo stripslashes($this->lang->line('driver_retype_pwd'));
                                    else
                                        echo 'Re-type Password';
                                    ?> </label>
                                <input name="confirm_password" minlength="6" equalTo="#password" id="confirm_password" type="password" tabindex="6" class="required form-control" placeholder="<?php
                                if ($this->lang->line('driver_retype_pwd_again') != '')
                                    echo stripslashes($this->lang->line('driver_retype_pwd_again'));
                                else
                                    echo 'Retype your pass again';
                                ?>"/>
                            </div>

                        </div>

                        <!---------------------    ADDRESS      ------------------------->

                        <div class="sign_up_block col-lg-12 nopadd">
                            <div class="headline-hr"></div>
                            <span class="bdr_bg"><h3><?php
                                    if ($this->lang->line('driver_address_details') != '')
                                        echo stripslashes($this->lang->line('driver_address_details'));
                                    else
                                        echo 'Address Details';
                                    ?></h3></span>
                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('cms_address') != '')
                                        echo stripslashes($this->lang->line('cms_address'));
                                    else
                                        echo 'Address';
                                    ?></label>
                                <textarea name="address" id="address" tabindex="6" class="required form-control" placeholder="<?php
                                if ($this->lang->line('driver_your_address') != '')
                                    echo stripslashes($this->lang->line('driver_your_address'));
                                else
                                    echo 'Your Address';
                                ?>" ></textarea>
                            </div>
                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_country') != '')
                                        echo stripslashes($this->lang->line('driver_country'));
                                    else
                                        echo 'Country';
                                    ?></label>
                                <select name="county" id="county" tabindex="7" class="required form-control" title="Please choose ypur country">
                                    <?php foreach ($countryList as $country) { ?>
                                        <option value="<?php echo $country->name; ?>" data-dialCode="<?php echo $country->dial_code; ?>"><?php echo $country->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_state_province_region') != '')
                                        echo stripslashes($this->lang->line('driver_state_province_region'));
                                    else
                                        echo 'State / Province / Region';
                                    ?></label>
                                <input name="state" id="state" type="text" tabindex="8" class="required form-control" placeholder="<?php
                                if ($this->lang->line('driver_state_province_region_your') != '')
                                    echo stripslashes($this->lang->line('driver_state_province_region_your'));
                                else
                                    echo 'Your State / Province / Region';
                                ?>" />
                            </div>
                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('cms_city') != '')
                                        echo stripslashes($this->lang->line('cms_city'));
                                    else
                                        echo 'City';
                                    ?></label>
                                <input name="city" id="city" type="text" tabindex="9" class="required form-control" placeholder="<?php
                                if ($this->lang->line('driver_your_city') != '')
                                    echo stripslashes($this->lang->line('driver_your_city'));
                                else
                                    echo 'Your City';
                                ?>" value="<?php if (isset($locationDetail->city)) echo $locationDetail->city; ?>" />
                            </div>
                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('user_postal_code') != '')
                                        echo stripslashes($this->lang->line('user_postal_code'));
                                    else
                                        echo 'Postal Code';
                                    ?></label>
                                <input name="postal_code"  id="postal_code" type="text" tabindex="10" maxlength="10" class="required form-control" placeholder="000 111" />
                            </div>
                            <div class="col-lg-12 sign_up_base">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_mobile') != '')
                                        echo stripslashes($this->lang->line('driver_mobile'));
                                    else
                                        echo 'Mobile';
                                    ?></label>
                                <input type="text" name="dail_code" placeholder="<?php echo $d_country_code; ?>" id="country_code" class="form-control required phoneCode" />
                                <input type="text" name="mobile_number" id="mobile_number" class="form-control required number phoneNumber" placeholder="777-777-777" >
								 <button type="button" class="btn1 category_btn mob_resend_otp" id="otp_send_btn" onclick="sendOtp();"><?php
                                if ($this->lang->line('send_otp') != '')
                                    echo stripslashes($this->lang->line('send_otp'));
                                else
                                    echo 'Send OTP';
                                ?></button>
								<img src="images/indicator.gif" id="sms_loader">
                            </div>
							<div class="col-lg-12 sign_up_base">		
								<span id="otpSuccess"></span>
								<span id="temp_otp"></span>
								<span id="otpNumErr" class="error"></span>
								<input type="hidden" id="otp_mode" value="<?php echo $this->config->item('twilio_account_type'); ?>"/>
								<input type="hidden" id="otp_phone_number" value=""/>
					            <input type="hidden" id="otp_country_code" value=""/>
								<input type="hidden" id="isNumberExists" value=""/>
							</div>
							<div class="col-lg-12 sign_up_base otp_container" id="otp_container">
								<label class="text-left">Enter OTP : </label>
								 <input type="text" name="mobile_otp" id="mobile_otp" class="form-control required mob_otp"  placeholder="Please enter otp">
								 <button type="button" class="btn1 category_btn mob_resend_otp" onclick="verifyOtp();">Verify OTP</button>	
							</div>
                        </div>


                        <div class="sign_up_block col-lg-12 nopadd">
                            <div class="headline-hr"></div>
                            <span class="bdr_bg"><h3><?php
                                    if ($this->lang->line('driver_identity') != '')
                                        echo stripslashes($this->lang->line('driver_identity'));
                                    else
                                        echo 'Identity';
                                    ?></h3></span>
                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_your_profile_image') != '')
                                        echo stripslashes($this->lang->line('driver_your_profile_image'));
                                    else
                                        echo 'Your Profile Image';
                                    ?></label>
                                <input name="thumbnail" id="thumbnail" type="file" tabindex="12" class=""  title="Please enter your profile image"/>
                            </div>
                        </div>
                        <div class="sign_up_block col-lg-12 nopadd">
                            <div class="headline-hr"></div>
                            <span class="bdr_bg"><h3><?php
                                    if ($this->lang->line('driver_vehicle_info') != '')
                                        echo stripslashes($this->lang->line('driver_vehicle_info'));
                                    else
                                        echo 'Vehicle Information';
                                    ?></h3></span>
							<div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_vehicle_type') != '')
                                        echo stripslashes($this->lang->line('driver_vehicle_type'));
                                    else
                                        echo 'Vehicle Type';
                                    ?></label>
                                <select class="required form-control"  name="vehicle_type" id="vehicle_type" >
                                    <option value=""><?php
                                        if ($this->lang->line('driver_choose_vehicle_type') != '')
                                            echo stripslashes($this->lang->line('driver_choose_vehicle_type'));
                                        else
                                            echo 'Please choose vehicle type... ';
                                        ?></option>
                                    <?php if ($vehicle_types->num_rows() > 0) { ?>
                                        <?php foreach ($vehicle_types->result() as $vehicles) { ?>
                                            <option value="<?php echo $vehicles->_id; ?>"><?php echo $vehicles->vehicle_type; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>		
							<div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_vehicle_maker') != '')
                                        echo stripslashes($this->lang->line('driver_vehicle_maker'));
                                    else
                                        echo 'Vehicle Maker';
                                    ?></label>
                                <select class="required form-control"  name="vehicle_maker" id="vehicle_maker" >
                                    <option value=""><?php
                                        if ($this->lang->line('driver_choose_vehicle_maker') != '')
                                            echo stripslashes($this->lang->line('driver_choose_vehicle_maker'));
                                        else
                                            echo 'Please choose vehicle maker...';
                                        ?></option>
                                    <?php if ($brandList->num_rows() > 0) { ?>
                                        <?php foreach ($brandList->result() as $brand) { ?>
                                            <option value="<?php echo $brand->_id; ?>"><?php echo $brand->brand_name; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
							
							<div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('dash_vehicle_model') != '')
                                        echo stripslashes($this->lang->line('dash_vehicle_model'));
                                    else
                                        echo 'Vehicle Model';
                                    ?></label>
                                <select class="required form-control"  name="vehicle_model" id="vehicle_model" >
                                    <option value=""><?php
                                        if ($this->lang->line('driver_choose_vehicle_model') != '')
                                            echo stripslashes($this->lang->line('driver_choose_vehicle_model'));
                                        else
                                            echo 'Please choose vehicle model...';
                                        ?></option>
                                     <?php 
									$sldmodelYrs=array(); 
									if ($modelList->num_rows() > 0) { $syr = 0; ?>
                                        <?php foreach ($modelList->result() as $model) {
											$modelYears = '';
											if(isset($model->year_of_model))$modelYears = @implode(',',$model->year_of_model);
											if($syr == 0)$sldmodelYrs = $model->year_of_model;
											$syr++;
										?>
                                            <option value="<?php echo $model->_id; ?>" data-years="<?php echo $modelYears; ?>" data-vmodel="<?php echo $model->brand . '_' . $model->type; ?>"><?php echo $model->name; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
							<div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php 
						if($this->lang->line('dash_year_of_model') != '') echo stripslashes($this->lang->line('dash_year_of_model')); else  echo 'Year Of Model';
						?></label>
                                <select class="required form-control"  name="vehicle_model_year" id="vehicle_model_year">
                                        <option value=""><?php 
						if($this->lang->line('dash_please_choose_year_of_model') != '') echo stripslashes($this->lang->line('dash_please_choose_year_of_model')); else  echo 'Please choose year of model';
						?>...</option>
                                        <?php 
											if (count($sldmodelYrs) > 0) { ?>
                                            <?php foreach ($sldmodelYrs as $modelyr) { 			
											?>
                                                <option value="<?php echo $modelyr; ?>"><?php echo $modelyr; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                            </div>
                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_vehicle_number') != '')
                                        echo stripslashes($this->lang->line('driver_vehicle_number'));
                                    else
                                        echo 'Vehicle Number';
                                    ?></label>
                                <input name="vehicle_number" id="vehicle_number"  type="text" tabindex="16" class="required form-control Vehicle_Number_Chk" />
                                <label class="error_chk" id="vehicle_number_exist"></label>
                            </div>								

                            <input name="driver_id" type="hidden" value="" />
                            <div class="col-lg-12 sign_up_base ">
                                <label class="text-left"><span></span><?php
                                    if ($this->lang->line('driver_air_conditioned') != '')
                                        echo stripslashes($this->lang->line('driver_air_conditioned'));
                                    else
                                        echo 'Air Conditioned';
                                    ?> </label>
                                <div class="onoffswitch">
                                    <input type="checkbox" name="aircond" class="onoffswitch-checkbox" id="myonoffswitch" checked="checked" />
                                    <label class="onoffswitch-label" for="myonoffswitch">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>

                            </div>
                        </div>
<div class="clearfix"></div>
                        <!-------------------------       DOCUMENTS          ------------------------------>
                        <?php if ($docx_list->num_rows() > 0) { ?>
                            <div class="sign_up_block col-lg-12 nopadd">
                                <div class="headline-hr"></div>
                                <span class="bdr_bg"><h3><?php
                                        if ($this->lang->line('driver_your_docs') != '')
                                            echo stripslashes($this->lang->line('driver_your_docs'));
                                        else
                                            echo 'Your Documents';
                                        ?></h3></span>
                                <?php
                                foreach ($docx_list->result() as $docx) {
                                    if ($docx->category == 'Driver') {
                                        $docx_uniq = 'docx-' . $docx->_id;
                                        ?>

                                        <div class="col-lg-12 sign_up_base ">
                                            <label class="text-left" for="<?php echo $docx_uniq; ?>"><span></span>
                                                <?php
                                                echo $docx->name;
                                                if ($docx->hasreq == 'Yes') {
                                                    echo '<span class="req">*</span>';
                                                }
                                                ?>
                                            </label>

                                            <input name="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>" data-docx="<?php echo $docx->name; ?>" data-docx_id="<?php echo $docx->_id; ?>" type="file" tabindex="17" class="form-control <?php
                                            if ($docx->hasreq == 'Yes') {
                                                echo 'required';
                                            }
                                            ?> docx" title="Please select <?php echo strtolower($docx->name); ?>"/>
                                            <input type="hidden" name="driver_docx[]" value="<?php echo $docx->name; ?>|:||:|<?php echo (string)$docx->_id; ?>" id="<?php echo $docx_uniq; ?>-Hid" />
                                            <input type="hidden" name="driver_docx_expiry[]" value="<?php echo $docx->hasexp; ?>" />
                                            <span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
                                            <span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
                                            <a href="" target="_blank" id="<?php echo $docx_uniq; ?>-View"></a>

                                            <?php if ($docx->hasexp == 'Yes') { ?>
                                                <label class="text-left"><span></span><?php echo ucfirst($docx->name); ?> <?php
                                                    if ($this->lang->line('driver_exp_date') != '')
                                                        echo stripslashes($this->lang->line('driver_exp_date'));
                                                    else
                                                        echo 'Expiry Date :';
                                                    ?> </label>
                                                <input type="text"  id="expiry-<?php echo $docx_uniq; ?>" class="required form-control" name="driver-<?php echo url_title($docx->name); ?>" /> 
                                                <script>
                                                    $(function () {
                                                        var mdate = new Date('<?php echo date("F d,Y H:i:s"); ?>');
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker();
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeMonth", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeYear", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "minDate", mdate);
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "showAnim", "clip");
                                                        // drop,fold,slide,bounce,slideDown,blind
                                                    });
                                                </script>

                                            <?php } ?>

                                        </div>
                                        <?php
                                    }
                                }
                                ?>

                            </div>

                            <?php
                        }
                        ?>




                        <?php if ($docx_list->num_rows() > 0) { ?>

                            <div class="sign_up_block col-lg-12 nopadd">
                                <div class="headline-hr"></div>
                                <span class="bdr_bg"><h3><?php
                                        if ($this->lang->line('driver_veh_docs') != '')
                                            echo stripslashes($this->lang->line('driver_veh_docs'));
                                        else
                                            echo 'Vehicle Documents ';
                                        ?></h3></span>

                                <?php
                                foreach ($docx_list->result() as $docx) {
                                    if ($docx->category == 'Vehicle') {
                                        $docx_uniq = 'docx-' . $docx->_id;
                                        ?>

                                        <div class="col-lg-12 sign_up_base ">
                                            <label class="text-left" for="<?php echo $docx_uniq; ?>"><span></span>
                                                <?php
                                                echo $docx->name;
                                                if ($docx->hasreq == 'Yes') {
                                                    echo '<span class="req">*</span>';
                                                }
                                                ?>
                                            </label>
                                            <input name="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>" data-docx="<?php echo $docx->name; ?>" data-docx_id="<?php echo $docx->_id; ?>" type="file" tabindex="18" class="form-control <?php
                                            if ($docx->hasreq == 'Yes') {
                                                echo 'required';
                                            }
                                            ?> docx" title="Please select <?php echo strtolower($docx->name); ?>"/>
											
                                            <input type="hidden" name="vehicle_docx[]" value="<?php echo $docx->name; ?>|:||:|<?php echo (string)$docx->_id; ?>" id="<?php echo $docx_uniq; ?>-Hid" />
											
                                            <input type="hidden" name="vehicle_docx_expiry[]" value="<?php echo $docx->hasexp; ?>" />
                                            <span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
                                            <span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
                                            <a href="" target="_blank" id="<?php echo $docx_uniq; ?>-View"></a>

                                            <?php if ($docx->hasexp == 'Yes') { ?>
                                                <label class="text-left"><span></span><?php echo ucfirst($docx->name); ?> <?php
                                                    if ($this->lang->line('driver_exp_date') != '')
                                                        echo stripslashes($this->lang->line('driver_exp_date'));
                                                    else
                                                        echo 'Expiry Date :';
                                                    ?> </label>

                                                <input type="text"  id="expiry-<?php echo $docx_uniq; ?>" class="required form-control" name="vehicle-<?php echo url_title($docx->name); ?>" /> 

                                                <script>
                                                    $(function () {
                                                        var mdate = new Date('<?php echo date("F d,Y H:i:s"); ?>');
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker();
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeMonth", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeYear", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "minDate", mdate);
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "showAnim", "clip");
                                                        // drop,fold,slide,bounce,slideDown,blind
                                                    });
                                                </script>

                                            <?php } ?>

                                        </div>
                                        <?php
                                    }
                                }
                                ?>

                            </div>

                            <?php
                        }
                        ?>

                        <input type="checkbox" tabindex="19" name="status" checked="checked" id="active_inactive_active" class="active_inactive" style="display:none;"/>
                        <input type="hidden" name="verify_status" value="No" />

                        <div class="sign_up_block col-lg-12 nopadd">
                            <div class="col-lg-12 sign_up_base text-left">
                                <input checked data-toggle="toggle" type="checkbox" name="termsCondition" id="termsCondition" class="required" title="Please agree our terms & condition" />
                                <?php
                                if ($this->lang->line('driver_agree') != '')
                                    $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_agree')));
                                else
                                    $sitename = "By proceeding, I agree that" . $this->config->item('email_title') . "or its representatives may contact me by email, phone, or SMS (including by automatic telephone dialing system) at the email address or number I provide, including for marketing purposes. I have read and understand the relevant";
                                ?>
                                <p> <a href="pages/driver-privacy-statement" target="_blank"> <?php echo $sitename.' ';
                                        if ($this->lang->line('driver_privacy_statement') != '')
                                            echo stripslashes($this->lang->line('driver_privacy_statement'));
                                        else
                                            echo 'Driver Privacy Statement.';
                                        ?></a></p>
                            </div>
                            <input type="submit" class=" btn1 category_btn" value="<?php
                            if ($this->lang->line('user_submit_upper') != '')
                                echo stripslashes($this->lang->line('user_submit_upper'));
                            else
                                echo 'SUBMIT';
                            ?>" />
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="foot_catgory"></div>


    <script src="js/jquery.validate.js"></script> 

	<script>
	
        $(document).ready(function () {
            $("#driver_register_form").validate({
			submitHandler: function(form) {
			  otp_phone_number=$("#otp_phone_number").val();
			  otp_country_code=$("#otp_country_code").val();
			  phone_code = $('#country_code').val();
			  otp_phone = $('#mobile_number').val();
			  isNumberExists=$("#isNumberExists").val();
			  if(isNumberExists=='true'){
				$('#otpNumErr').html('<p class="error"><?php
												if ($this->lang->line('this_mobile_number_exist') != '')
													echo stripslashes($this->lang->line('this_mobile_number_exist'));
												else
													echo 'This mobile number already exist';
												?> !!!</p>');
			  }else
			  if(otp_phone_number !=otp_phone || otp_country_code !=phone_code){
				  sendOtp();
				  
			  }else{
			 
					 $.ajax({
						type: 'POST',
						url: 'driver/sms_twilio/check_is_valid_otp_fields',
						dataType: "json",
						data: {"otp_phone":otp_phone_number,"phone_code":otp_country_code},
						success: function (response) {
							
						if(response=='success'){
							form.submit();
							
						}else if(response =='exist'){
							$('#otpNumErr').html('<p class="error"><?php
												if ($this->lang->line('this_mobile_number_exist') != '')
													echo stripslashes($this->lang->line('this_mobile_number_exist'));
												else
													echo 'This mobile number already exist';
												?> !!!</p>');
						}
						else{
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
                url: 'driver/sms_twilio/send_otp',
                dataType: "json",
                data: {"otp_phone": otp_phone, "phone_code": phone_code},
                success: function (response) {
					
					$("#otp_phone_number").val(otp_phone);
					$("#otp_country_code").val(phone_code);
                    if (response != 'error') {
					
						$("#isNumberExists").val("false");
						if(response == 'exist'){
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
					url: 'driver/sms_twilio/otp_verification',
					data: {"otp": mobile_otp,"otp_phone":otp_phone_number,"phone_code":otp_country_code},
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
							$('#otp_container').css('display','none');
                            $("#otp_send_btn").html('<?php
												if ($this->lang->line('change_number') != '')
													echo stripslashes($this->lang->line('change_number'));
												else
													echo 'Change Number';
												?>');
                            $("#otp_send_btn").attr("onclick", "reset_otp()");
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
    <script>
		$(document).ready(function () {

			var catoptions = $("#category").html();
			$("#driver_location").change(function (e) {
				var category_list = $("#driver_location :selected").attr('data-category');
				$("#category").html(catoptions);
				if (category_list == "") {
					return;
				} else {
					var vArr = category_list.split(",");
					$("#category option").each(function (e) {
						var optval = $(this).val();
						if (optval != '') {
							if ($.inArray(optval, vArr) == -1) {
								$('#category option[value="' + optval + '"]').remove();
							}
						}
					});
				}
			});

			var options = $("#vehicle_type").html();
			$("#category").change(function (e) {
				var vehicle_types = $("#category :selected").attr('data-vehicle');
				$("#vehicle_type").html(options);
				if (vehicle_types == "") {
					return;
				} else {
					var vArr = vehicle_types.split(",");
					$("#vehicle_type option").each(function (e) {
						var optval = $(this).val();
						if (optval != '') {
							if ($.inArray(optval, vArr) == -1) {
								$('#vehicle_type option[value="' + optval + '"]').remove();
							}
						}
					});
				}
			});

			// vehicle model
			var vehicleoptions = $("#vehicle_model").html();
			$('#vehicle_model option:not(:first)').remove().end();
			$("#vehicle_maker").change(function (e) {
				var maker = $("#vehicle_maker :selected").val();
				$("#vehicle_model").html(vehicleoptions);
				if (maker == "") {
					return;
				} else {
					var type = $("#vehicle_type :selected").val();
					$("#vehicle_model").html(vehicleoptions);
					if (type == "") {
						return;
					} else {
						var models = maker + '_' + type;
						updatemodelList(models);
					}
				}
			});
			$("#vehicle_type").change(function (e) {
				var type = $("#vehicle_type :selected").val();
				$("#vehicle_model").html(vehicleoptions);
				if (type == "") {
					return;
				} else {
					var maker = $("#vehicle_maker :selected").val();
					$("#vehicle_model").html(vehicleoptions);
					if (maker == "") {
						return;
					} else {
						var models = maker + '_' + type;
						updatemodelList(models);
					}
				}
			});
			
				$("#vehicle_model").change(function (e) {
				var modelYrs = $("#vehicle_model :selected").attr('data-years'); 
				var option = '<option value=""><?php 
						if($this->lang->line('dash_please_choose_year_of_model') != '') echo stripslashes($this->lang->line('dash_please_choose_year_of_model')); else  echo 'Please choose year of model';
						?>...</option>';
				if(modelYrs != ''){
					var modelYrsArr = modelYrs.split(',');
					for(var yr=0; yr < modelYrsArr.length; yr++){
						option = option+'<option>'+modelYrsArr[yr]+'</option>';
					}
				}
				$("#vehicle_model_year").html(option);
			});
			
			function updatemodelList(model) {
			$("#vehicle_model").val('');
			$("#vehicle_model option").each(function (e) {
				var vmodel = $(this).attr("data-vmodel");
				if (vmodel != '') {
					if (model != vmodel) {
						$('#vehicle_model option[data-vmodel="' + vmodel + '"]').remove();
					}
				}
			});
			$("#vehicle_model_year").val('');
		}

		});
		function updatemodelList(model) {
			$("#vehicle_model option").each(function (e) {
				var vmodel = $(this).attr("data-vmodel");
				if (vmodel != '') {
					if (model != vmodel) {
						$('#vehicle_model option[data-vmodel="' + vmodel + '"]').remove();
					}
				}
			});
		}
		$(document).ready(function () {
			$("#county").change(function (e) {
				var dail_code = $(this).find(':selected').attr('data-dialCode'); //.data('dialCode'); 
				$('#country_code').val(dail_code);
			});

			$(".docx").change(function (e) {
				e.preventDefault();
				var docxId = $(this).attr('id');
				var docxType = $(this).attr('data-docx');
				var docxTypeId = $(this).attr('data-docx_id');
				$("#" + docxId + "-Err").html('<img src="images/indicator.gif" />');
				var formData = new FormData($(this).parents('form')[0]);
				$.ajax({
					url: 'driver/profile/ajax_document_upload?docx_name=' + docxId,
					type: 'POST',
					xhr: function () {
						var myXhr = $.ajaxSettings.xhr();
						return myXhr;
					},
					success: function (data) {
						if (data.err_msg == 'Success') {
							$("#" + docxId + "-Hid").val(docxType + '|:|' + data.docx_name + '|:|' + docxTypeId);
							$("#" + docxId + "-Err").html('');
							$("#" + docxId + "-View").attr('href', 'drivers_documents_temp/' + data.docx_name);
							$("#" + docxId + "-View").html('<?php 
						if($this->lang->line('dash_view_uploaded_document') != '') echo stripslashes($this->lang->line('dash_view_uploaded_document')); else  echo 'View Uploaded Document';
						?>');
							//$("#"+docxId+"-Succ").html('Success');
						} else {
							$("#" + docxId).val('');
							$("#" + docxId + "-Hid").val('');
							$("#" + docxId + "-Succ").html('');
							$("#" + docxId + "-Err").html(data.err_msg);
						}
					},
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					dataType: "json"
				});
				return false;
			});
		});
    </script>		 


	
</body>
</html>