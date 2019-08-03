<?php
$this->load->view('admin/templates/header.php');
?>
<div id="content">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_wrap tabby">
                    <div class="widget_top"> 
                        <span class="h_icon list"></span>
                        <h6><?php echo $heading; ?></h6>
                    </div>
                    <div class="widget_content">
                        <?php
                        $attributes = array('class' => 'form_container left_label', 'id' => 'app_settings_form','enctype' => 'multipart/form-data');
                        echo form_open('admin/adminlogin/admin_global_settings', $attributes)
                        ?>
                        <input type="hidden" name="form_mode" value="app"/>
                        <div id="tab4">
                            <ul>

                                <li>
                                    <h3><?php if ($this->lang->line('admin_settings_website_app_mode') != '') echo stripslashes($this->lang->line('admin_settings_website_app_mode')); else echo 'Website & App Mode'; ?></h3>
                                </li>
								
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="status"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<div class="prod_dev">
													<input type="checkbox" tabindex="7" name="site_mode"  id="prod_dev" class="prod_dev" <?php if (isset($admin_settings->row()->site_mode)){if ($admin_settings->row()->site_mode == 'production'){ echo 'checked="checked"'; }}  ?> />
												</div>
											</div>
										</div>
									</li>

								<li>
                                    <h3><?php if ($this->lang->line('admin_settings_customer_service_settings') != '') echo stripslashes($this->lang->line('admin_settings_customer_service_settings')); else echo 'Customer service settings'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="customer_service_number"><?php if ($this->lang->line('admin_settings_customer_service_number') != '') echo stripslashes($this->lang->line('admin_settings_customer_service_number')); else echo 'customer service number'; ?></label>
                                        <div class="form_input">
                                            <input name="customer_service_number" id="customer_service_number" type="text" value="<?php if (isset($admin_settings->row()->customer_service_number)) echo htmlentities($admin_settings->row()->customer_service_number); ?>" tabindex="7" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_customer_service_number') != '') echo stripslashes($this->lang->line('admin_setting_customer_service_number')); else echo 'customer service number'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <h3><?php if ($this->lang->line('admin_settings_payment_billing') != '') echo stripslashes($this->lang->line('admin_settings_payment_billing')); else echo 'Payment/ Billing'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="billing_cycle"><?php if ($this->lang->line('admin_settings_billing_cycle') != '') echo stripslashes($this->lang->line('admin_settings_billing_cycle')); else echo 'Billing cycle'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="billing_cycle" id="billing_cycle" type="text" value="<?php if (isset($admin_settings->row()->billing_cycle)) echo htmlentities($admin_settings->row()->billing_cycle); ?>" tabindex="7" class="large tipTop required number positiveNumber minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_no_days_billing') != '') echo stripslashes($this->lang->line('admin_setting_no_days_billing')); else echo 'No of days for Billing'; ?>"/>
                                            <?php if ($this->lang->line('admin_settings_days') != '') echo stripslashes($this->lang->line('admin_settings_days')); else echo 'days'; ?>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <h3><?php if ($this->lang->line('admin_settings_app_settings') != '') echo stripslashes($this->lang->line('admin_settings_app_settings')); else echo 'App Settings'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="respond_timeout"><?php if ($this->lang->line('admin_settings_driver_timeout') != '') echo stripslashes($this->lang->line('admin_settings_driver_timeout')); else echo 'Driver Timeout'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="respond_timeout" id="respond_timeout" type="text" value="<?php if (isset($admin_settings->row()->respond_timeout)) echo htmlentities($admin_settings->row()->respond_timeout); ?>" tabindex="7" class="large tipTop required number positiveNumber minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_maximum_driver_respond_request') != '') echo stripslashes($this->lang->line('admin_setting_maximum_driver_respond_request')); else echo 'Maximum time for driver to respond for a request'; ?>"/>
                                            (<?php if ($this->lang->line('admin_settings_seconds') != '') echo stripslashes($this->lang->line('admin_settings_seconds')); else echo 'Seconds'; ?>)
                                        </div>
                                    </div>
                                </li>
								
								<li>
                                    <div class="form_grid_12">
                                        <label for="user_timeout" class="field_title"><?php if ($this->lang->line('admin_settings_user_timeout') != '') echo stripslashes($this->lang->line('admin_settings_user_timeout')); else echo 'User Timeout'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input type="text" class="large tipTop required number positiveNumber minfloatingNumber" tabindex="7" value="<?php if (isset($admin_settings->row()->user_timeout)) echo htmlentities($admin_settings->row()->user_timeout); ?>" id="user_timeout" name="user_timeout" original-title="<?php if ($this->lang->line('admin_setting_maximum_driver_respond_payment') != '') echo stripslashes($this->lang->line('admin_setting_maximum_driver_respond_payment')); else echo 'Maximum time for user to respond for a payment'; ?>">
                                            (<?php if ($this->lang->line('admin_settings_seconds') != '') echo stripslashes($this->lang->line('admin_settings_seconds')); else echo 'Seconds'; ?>)
                                        </div>
                                    </div>
                                </li>
								
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="map_searching_radius"><?php if ($this->lang->line('admin_settings_map_searching_radius') != '') echo stripslashes($this->lang->line('admin_settings_map_searching_radius')); else echo 'Map Searching Radius'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="map_searching_radius" id="map_searching_radius" type="text" value="<?php if (isset($admin_settings->row()->map_searching_radius)) echo htmlentities($admin_settings->row()->map_searching_radius); ?>" tabindex="8" class="large tipTop required number positiveNumber minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_maximum_search_radius_map') != '') echo stripslashes($this->lang->line('admin_setting_maximum_search_radius_map')); else echo 'Maximum search radius in map.'; ?>"/>
                                            (<?php if ($this->lang->line('admin_settings_meters') != '') echo stripslashes($this->lang->line('admin_settings_meters')); else echo 'Meters'; ?>)
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="driver_last_active_time">
                                            <?php 
                                                if ($this->lang->line('admin_settings_last_active_time') != '') {
                                                    echo stripslashes($this->lang->line('admin_settings_last_active_time')); 
                                                } else {
                                                    echo 'Driver Last Active Time'; 
                                                }
                                            ?>
                                            <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="driver_last_active_time" id="driver_last_active_time" type="text" value="<?php 
                                                if (isset($admin_settings->row()->driver_last_active_time)) {
                                                    echo htmlentities($admin_settings->row()->driver_last_active_time);
                                                }
                                            ?>" tabindex="10" class="large tipTop required number positiveNumber" title="
                                            <?php 
                                                if ($this->lang->line('admin_setting_last_active_time') != '') {
                                                    echo stripslashes($this->lang->line('admin_setting_last_active_time'));
                                                } else {
                                                    echo 'Driver Last Active Time'; 
                                                }
                                            ?>"/>                                            
                                            (<?php 
                                                if ($this->lang->line('admin_settings_seconds') != '') {
                                                    echo stripslashes($this->lang->line('admin_settings_seconds'));
                                                } else {
                                                    echo 'Seconds'; 
                                                }
                                            ?>)
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="driver_min_remit_amount">
                                            <?php 
                                                if ($this->lang->line('admin_settings_driver_min_remit_amount') != '') {
                                                    echo stripslashes($this->lang->line('admin_settings_driver_min_remit_amount')); 
                                                } else {
                                                    echo 'Driver Min Remit Amount'; 
                                                }
                                            ?>
                                            <span class="req">*</span>
                                        </label>
                                        <div class="form_input">
                                            <input name="driver_min_remit_amount" id="driver_min_remit_amount" type="text" value="<?php 
                                                if (isset($admin_settings->row()->driver_min_remit_amount)) {
                                                    echo htmlentities($admin_settings->row()->driver_min_remit_amount);
                                                }
                                            ?>" tabindex="10" class="large tipTop required number positiveNumber" title="
                                            <?php 
                                                if ($this->lang->line('admin_settings_driver_min_remit_amount') != '') {
                                                    echo stripslashes($this->lang->line('admin_settings_driver_min_remit_amount'));
                                                } else {
                                                    echo 'Driver Minimum Remit Amount before being blocked'; 
                                                }
                                            ?>"/>                                            
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <h3><?php if ($this->lang->line('admin_settings_twilio_sms_api') != '') echo stripslashes($this->lang->line('admin_settings_twilio_sms_api')); else echo 'Twilio SMS API'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="twilio_account_type"><?php if ($this->lang->line('admin_settings_account_type') != '') echo stripslashes($this->lang->line('admin_settings_account_type')); else echo 'Account Type'; ?></label>


                                        <div class="form_input">

                                            <?php
                                            if (isset($admin_settings->row()->twilio_account_type)) {
                                                $twilio_account_type = $admin_settings->row()->twilio_account_type;
                                            } else {
                                                $twilio_account_type = '';
                                            }
                                            ?>

                                            <input name="twilio_account_type" id="twilio_account_live" type="radio" <?php
                                            if ($twilio_account_type == "prod") {
                                                echo "checked";
                                            }
                                            ?> value="prod" tabindex="1" class="small tipTop" title="<?php if ($this->lang->line('admin_setting_enter_twilio_account_type') != '') echo stripslashes($this->lang->line('admin_setting_enter_twilio_account_type')); else echo 'Please enter the twilio account type'; ?>"/>
                                            <label for="twilio_account_live"><?php if ($this->lang->line('admin_settings_live') != '') echo stripslashes($this->lang->line('admin_settings_live')); else echo 'LIVE'; ?></label>

                                            <input name="twilio_account_type" <?php
                                            if ($twilio_account_type == "sandbox") {
                                                echo "checked";
                                            }
                                            ?>  id="twilio_account_test" type="radio" value="sandbox" tabindex="1" class="small tipTop" title="<?php if ($this->lang->line('admin_setting_enter_twilio_account_type') != '') echo stripslashes($this->lang->line('admin_setting_enter_twilio_account_type')); else echo 'Please enter the twilio account type'; ?>"/>
                                            <label for="twilio_account_test"><?php if ($this->lang->line('admin_settings_test') != '') echo stripslashes($this->lang->line('admin_settings_test')); else echo 'TEST'; ?></label>

                                        </div>


                                    </div>
                                </li>


                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="twilio_account_sid"><?php if ($this->lang->line('admin_settings_account_sid') != '') echo stripslashes($this->lang->line('admin_settings_account_sid')); else echo 'Account SID'; ?></label>
                                        <div class="form_input">
                                            <input name="twilio_account_sid" id="twilio_account_sid" type="text" value="<?php if (isset($admin_settings->row()->twilio_account_sid)) echo $admin_settings->row()->twilio_account_sid; ?>" tabindex="10" class="large tipTop " title="<?php if ($this->lang->line('admin_setting_enter_twilio_account_id') != '') echo stripslashes($this->lang->line('admin_setting_enter_twilio_account_id')); else echo 'Please enter the twilio account sid'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="twilio_auth_token"><?php if ($this->lang->line('admin_settings_auth_token') != '') echo stripslashes($this->lang->line('admin_settings_auth_token')); else echo 'Auth Token'; ?></label>
                                        <div class="form_input">
                                            <input name="twilio_auth_token" id="twilio_auth_token" type="text" value="<?php if (isset($admin_settings->row()->twilio_auth_token)) echo $admin_settings->row()->twilio_auth_token; ?>" tabindex="10" class="large tipTop " title="<?php if ($this->lang->line('admin_setting_enter_twilio_auth_token') != '') echo stripslashes($this->lang->line('admin_setting_enter_twilio_auth_token')); else echo 'Please enter the twilio auth token'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="twilio_number"><?php if ($this->lang->line('admin_settings_number') != '') echo stripslashes($this->lang->line('admin_settings_number')); else echo 'Number'; ?></label>
                                        <div class="form_input">
                                            <input name="twilio_number" id="twilio_number" type="text" value="<?php if (isset($admin_settings->row()->twilio_number)) echo $admin_settings->row()->twilio_number; ?>" tabindex="10" class="large tipTop " title="<?php if ($this->lang->line('admin_setting_enter_twilio_number') != '') echo stripslashes($this->lang->line('admin_setting_enter_twilio_number')); else echo 'Please enter the twilio number'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <h3><?php if ($this->lang->line('admin_settings_wallet_recharge_amount_settings') != '') echo stripslashes($this->lang->line('admin_settings_wallet_recharge_amount_settings')); else echo 'Wallet Recharge Amount Settings'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="wal_recharge_min_amount"><?php if ($this->lang->line('admin_settings_minimum_amount') != '') echo stripslashes($this->lang->line('admin_settings_minimum_amount')); else echo 'Minimum Amount'; ?></label>
                                        <div class="form_input">
                                            <input name="wal_recharge_min_amount" id="wal_recharge_min_amount" type="text" value="<?php if (isset($admin_settings->row()->wal_recharge_min_amount)) echo $admin_settings->row()->wal_recharge_min_amount; ?>" tabindex="10"  lesserThan="#wal_recharge_max_amount" class="large tipTop number positiveNumber minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_enter_minimum_wallet_recharge_amount') != '') echo stripslashes($this->lang->line('admin_setting_enter_minimum_wallet_recharge_amount')); else echo 'Please enter minimum wallet recharge amount'; ?>"/><?php echo ' '.'(Local Currency)'; ?>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="wal_recharge_max_amount"><?php if ($this->lang->line('admin_settings_maximum_amount') != '') echo stripslashes($this->lang->line('admin_settings_maximum_amount')); else echo 'Maximum Amount'; ?></label>
                                        <div class="form_input">
                                           
                                            <input name="wal_recharge_max_amount" id="wal_recharge_max_amount" type="text" value="<?php if (isset($admin_settings->row()->wal_recharge_max_amount)) echo $admin_settings->row()->wal_recharge_max_amount; ?>" tabindex="10" greaterThan="#wal_recharge_min_amount" class="large tipTop number positiveNumber greaterThan minfloatingNumber" title="<?php if ($this->lang->line('admin_setting_enter_maximum_wallet_recharge_amount') != '') echo stripslashes($this->lang->line('admin_setting_enter_maximum_wallet_recharge_amount')); else echo 'Please enter maximum wallet recharge amount'; ?>"/><?php echo ' '.'(Local Currency)'; ?>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <h3><?php if ($this->lang->line('admin_settings_googlemap_places') != '') echo stripslashes($this->lang->line('admin_settings_googlemap_places')); else echo 'Google Map Places Search API Key'; ?></h3>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="wal_recharge_min_amount"><?php if ($this->lang->line('admin_settings_website') != '') echo stripslashes($this->lang->line('admin_settings_website')); else echo 'Website'; ?></label>
                                        <div class="form_input">
                                           
                                            <input name="google_maps_api_key" id="google_maps_api_key" type="text" value="<?php if (isset($admin_settings->row()->google_maps_api_key)) echo $admin_settings->row()->google_maps_api_key; ?>" tabindex="11" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_google_map_api_key') != '') echo stripslashes($this->lang->line('admin_setting_google_map_api_key')); else echo 'Please enter the Google Map Api key'; ?>"/>
                                        
                                     
                                        </div>
                                    </div>
                                </li>
								<?php /*
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="wal_recharge_min_amount"><?php if ($this->lang->line('admin_settings_android') != '') echo stripslashes($this->lang->line('admin_settings_android')); else echo 'Android'; ?></label>
                                        <div class="form_input">
                                           
                                           <input name="google_maps_api_key_android" id="google_maps_api_key_android" type="text" value="<?php if (isset($admin_settings->row()->google_maps_api_key_android)) echo $admin_settings->row()->google_maps_api_key_android; ?>" tabindex="11" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_google_map_api_key') != '') echo stripslashes($this->lang->line('admin_setting_google_map_api_key')); else echo 'Please enter the Google Map Api key'; ?>"/>
                                        
                                     
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="wal_recharge_min_amount"><?php if ($this->lang->line('admin_settings_ios') != '') echo stripslashes($this->lang->line('admin_settings_ios')); else echo 'Ios'; ?></label>
                                        <div class="form_input">
                                           
                                           <input name="google_maps_api_key_ios" id="google_maps_api_key_ios" type="text" value="<?php if (isset($admin_settings->row()->google_maps_api_key_ios)) echo $admin_settings->row()->google_maps_api_key_ios; ?>" tabindex="11" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_google_map_api_key') != '') echo stripslashes($this->lang->line('admin_setting_google_map_api_key')); else echo 'Please enter the Google Map Api key'; ?>"/>
                                        
                                     
                                        </div>
                                    </div>
                                </li>
								*/ ?>
                                <li>
                                    <h3><?php if ($this->lang->line('admin_settings_pushnotification') != '') echo stripslashes($this->lang->line('admin_settings_pushnotification')); else echo 'Push Notification'; ?></h3>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="fcm_server_key"><?php if ($this->lang->line('admin_settings_fcm_server_key') != '') echo stripslashes($this->lang->line('admin_settings_fcm_server_key')); else echo 'FCM Server Key'; ?></label>
                                        <div class="form_input">                                           
                                           <input name="fcm_server_key" id="fcm_server_key" type="text" value="<?php if (isset($admin_settings->row()->fcm_server_key)) echo $admin_settings->row()->fcm_server_key; ?>" tabindex="10" class="large tipTop" />
                                        
                                     
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <h3><?php if ($this->lang->line('admin_settings_android') != '') echo stripslashes($this->lang->line('admin_settings_android')); else echo 'Android Key'; ?></h3>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="wal_recharge_min_amount"><?php if ($this->lang->line('admin_settings_android_user') != '') echo stripslashes($this->lang->line('admin_settings_android_user')); else echo 'Android User'; ?></label>
                                        <div class="form_input">
                                           
                                           <input name="push_android_user" id="push_android_user" type="text" value="<?php if (isset($admin_settings->row()->push_android_user)) echo $admin_settings->row()->push_android_user; ?>" tabindex="11" class="large tipTop" />
                                        
                                     
                                        </div>
                                    </div>
                              </li>
                              <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="wal_recharge_min_amount"><?php if ($this->lang->line('admin_settings_android_driver') != '') echo stripslashes($this->lang->line('admin_settings_android_driver')); else echo 'Android Driver'; ?></label>
                                        <div class="form_input">
                                           
                                           <input name="push_android_driver" id="push_android_driver" type="text" value="<?php if (isset($admin_settings->row()->push_android_driver)) echo $admin_settings->row()->push_android_driver; ?>" tabindex="11" class="large tipTop" />
                                        
                                     
                                        </div>
                                    </div>
                              </li>
                              <li>
                                    <h3><?php if ($this->lang->line('admin_settings_ios_pem') != '') echo stripslashes($this->lang->line('admin_settings_ios_pem')); else echo 'Ios Pem File'; ?></h3>
                              </li>
                              <li>
                                <h3><?php if ($this->lang->line('admin_settings_ios_pem_development') != '') echo stripslashes($this->lang->line('admin_settings_ios_pem_development')); else echo 'Development'; ?></h3>
                              </li>
                              <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="facebook_image"><?php if ($this->lang->line('ios_user_pem') != '') echo stripslashes($this->lang->line('ios_user_pem')); else echo 'Ios User'; ?></label>
                                        <div class="form_input">
                                            <input name="ios_user_dev" id="ios_user_dev" type="file" tabindex="5" class="large tipTop" value="<?php echo $admin_settings->row()->ios_user_dev; ?>"/>
                                        <?php if($admin_settings->row()->ios_user_dev!='') {?>
                                         <a href="certificates/<?php echo $admin_settings->row()->ios_user_dev; ?>" target="_blank" ><?php echo $admin_settings->row()->ios_user_dev; ?></a>
                                        <?php }?>
                                        </div>
                                        
                                      
                                       
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="facebook_image"><?php if ($this->lang->line('ios_driver_pem') != '') echo stripslashes($this->lang->line('ios_driver_pem')); else echo 'Ios Driver'; ?></label>
                                        <div class="form_input">
                                            <input name="ios_driver_dev" id="ios_driver_dev" type="file" tabindex="5" class="large tipTop" value="<?php echo $admin_settings->row()->ios_driver_dev; ?>"/>
                                              <?php if($admin_settings->row()->ios_driver_dev!='') {?>
                                              <a href="certificates/<?php echo $admin_settings->row()->ios_driver_dev; ?>" target="_blank" ><?php echo $admin_settings->row()->ios_driver_dev; ?></a>
                                           <?php }?>
                                        </div>
                                      
                                    </div>
                               </li>
                               <li>
                                <h3><?php if ($this->lang->line('admin_settings_ios_pem_production') != '') echo stripslashes($this->lang->line('admin_settings_ios_pem_production')); else echo 'Production'; ?></h3>
                              </li>
                              <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="facebook_image"><?php if ($this->lang->line('ios_user_pem') != '') echo stripslashes($this->lang->line('ios_user_pem')); else echo 'Ios User'; ?></label>
                                        <div class="form_input">
                                            <input name="ios_user_prod" id="ios_user_prod" type="file" tabindex="5" class="large tipTop" value="<?php echo $admin_settings->row()->ios_user_prod; ?>"/>
                                              <?php if($admin_settings->row()->ios_user_prod!='') {?>
                                               <a href="certificates/<?php echo $admin_settings->row()->ios_user_prod; ?>" target="_blank" ><?php echo $admin_settings->row()->ios_user_prod; ?></a>
                                             <?php }?>
                                        </div>
                                       
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="facebook_image"><?php if ($this->lang->line('ios_driver_pem') != '') echo stripslashes($this->lang->line('ios_driver_pem')); else echo 'Ios Driver'; ?></label>
                                        <div class="form_input">
                                            <input name="ios_driver_prod" id="ios_driver_prod" type="file" tabindex="5" class="large tipTop"value="<?php echo $admin_settings->row()->ios_driver_prod; ?>" />
                                              <?php if($admin_settings->row()->ios_driver_prod!='') {?>
                                                <a href="certificates/<?php echo $admin_settings->row()->ios_driver_prod; ?>" target="_blank" ><?php echo $admin_settings->row()->ios_driver_prod; ?></a>
                                             <?php }?>
                                        </div>
                                      
                                    </div>
                               </li>
                                <li>
                                    <h3><?php if ($this->lang->line('admin_settings_about_us') != '') echo stripslashes($this->lang->line('admin_settings_about_us')); else echo 'About us'; ?></h3>
                                </li>
                               <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="meta_description"><?php if ($this->lang->line('admin_settings_about_us') != '') echo stripslashes($this->lang->line('admin_settings_about_us')); else echo 'About us'; ?></label>
                                        <div class="form_input">
                                            <textarea name="about_us" class="" cols="70" rows="5" tabindex="3"><?php echo $admin_settings->row()->about_us; ?></textarea>
                                        </div>
                                    </div>
                                </li>

                               
                            </ul>
                            <ul>
                                <li>
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue" tabindex="18"><span><?php if ($this->lang->line('admin_settings_submit') != '') echo stripslashes($this->lang->line('admin_settings_submit')); else echo 'Submit'; ?></span></button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span> 
</div>

<script type="text/javascript">
    $(document).ready(function () {
        if ($("#publish1").attr("checked") == "checked") {
            $("#d_mode1").attr("checked", false);
            $("#d_mode2").attr("checked", false);
            $("#d_mode3").attr("checked", true);
            $("#d_mode4").attr("checked", false);
            $("li#dev_mode").hide();
        }
        $("#publish1").click(function () {
            $("#d_mode1").attr("checked", false);
            $("#d_mode2").attr("checked", false);
            $("#d_mode3").attr("checked", false);
            $("#d_mode4").attr("checked", false);
            $("li#dev_mode").hide();
        });
    });
</script>

<?php
$this->load->view('admin/templates/footer.php');
?>