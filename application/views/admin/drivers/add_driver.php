<?php
$this->load->view('admin/templates/header.php');
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
                    $attributes = array('class' => 'form_container left_label', 'id' => 'driver_form', 'enctype' => 'multipart/form-data');
                    echo form_open_multipart('admin/drivers/insertEdit_driver', $attributes)
                    ?>
                    <ul>

                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_drivers_location') != '') echo stripslashes($this->lang->line('admin_drivers_drivers_location')); else echo 'Driver Location'; ?></h3>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="driver_location"><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="driver_location" id="driver_location" tabindex="1" class="required" style="height: 31px; width: 51%;">
                                        <?php
                                        if ($locationList->num_rows() > 0) {
                                            foreach ($locationList->result() as $loclist) {
                                                if (isset($loclist->avail_category)) {
                                                    $category_list = @implode($loclist->avail_category, ',');
                                                } else {
                                                    $category_list = '';
                                                }
                                                ?>
                                                <option value="<?php echo $loclist->_id; ?>" data-category="<?php echo $category_list; ?>">
                                                    <?php echo $loclist->city; ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('dash_driver_category') != '') echo stripslashes($this->lang->line('dash_driver_category')); else echo 'Driver Category'; ?></h3>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="driver_name"><?php if ($this->lang->line('admin_drivers_driver_category') != '') echo stripslashes($this->lang->line('admin_drivers_driver_category')); else echo 'Driver Category'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="category" id="category" tabindex="1" class="required" style="height: 31px; width: 51%;">
                                        <option value="" data-vehicle=''><?php if ($this->lang->line('admin_drivers_choose_driver_category') != '') echo stripslashes($this->lang->line('admin_drivers_choose_driver_category')); else echo 'Choose driver category'; ?></option>
                                        <?php
                                        if ($categoryList->num_rows() > 0) {
                                            foreach ($categoryList->result() as $category) {
                                                if (isset($category->vehicle_type)) {
                                                    $vehicle_type = @implode($category->vehicle_type, ',');
                                                } else {
                                                    $vehicle_type = '';
                                                }
                                                ?>
                                                <option value="<?php echo $category->_id; ?>" data-vehicle="<?php echo $vehicle_type; ?>">
                                                    <?php echo $category->name; ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_login_details') != '') echo stripslashes($this->lang->line('admin_drivers_login_details')); else echo 'Login Details'; ?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="driver_name"><?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="driver_name" id="driver_name" type="text" tabindex="2" class="required large tipTop" title="<?php if ($this->lang->line('driver_upload_enter_driver_fullname') != '') echo stripslashes($this->lang->line('driver_upload_enter_driver_fullname')); else echo 'Please enter the driver fullname'; ?>"/>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="email"><?php if ($this->lang->line('admin_drivers_email_address') != '') echo stripslashes($this->lang->line('admin_drivers_email_address')); else echo 'Email Address'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="email" id="email" type="text" tabindex="3" class="required large tipTop email" title="<?php if ($this->lang->line('driver_enter_driver_email_address') != '') echo stripslashes($this->lang->line('driver_enter_driver_email_address')); else echo 'Please enter the driver email address'; ?>"/>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="password"><?php if ($this->lang->line('admin_drivers_password') != '') echo stripslashes($this->lang->line('admin_drivers_password')); else echo 'Password'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="password" minlength="6" id="password" type="password" tabindex="4" class="required large tipTop" title="<?php if ($this->lang->line('driver_enter_new_password') != '') echo stripslashes($this->lang->line('driver_enter_new_password')); else echo 'Please enter the new password'; ?>"/>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="confirm_password"><?php if ($this->lang->line('admin_drivers_retype_password') != '') echo stripslashes($this->lang->line('admin_drivers_retype_password')); else echo 'Re-type Password'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="confirm_password" minlength="5" equalTo="#password" id="confirm_password" type="password" tabindex="6" class="required large tipTop" title="<?php if ($this->lang->line('driver_re_type_above_password') != '') echo stripslashes($this->lang->line('driver_re_type_above_password')); else echo 'Please re-type the above password'; ?>"/>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_address_details') != '') echo stripslashes($this->lang->line('admin_drivers_address_details')); else echo 'Address Details'; ?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="address"><?php if ($this->lang->line('admin_drivers_address') != '') echo stripslashes($this->lang->line('admin_drivers_address')); else echo 'Address'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <textarea name="address" id="address" tabindex="6" class="required large tipTop" title="<?php if ($this->lang->line('driver_enter_driver_address') != '') echo stripslashes($this->lang->line('driver_enter_driver_address')); else echo 'Please enter the divers address'; ?>" style="width: 372px;"></textarea>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="county"><?php if ($this->lang->line('admin_drivers_country') != '') echo stripslashes($this->lang->line('admin_drivers_country')); else echo 'Country'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="county" id="county" tabindex="7" class="required chzn-select" style="height: 31px; width: 51%;">
                                        <?php foreach ($countryList as $country) { ?>
                                            <option value="<?php echo $country->name; ?>" data-dialCode="<?php echo $country->dial_code; ?>"><?php echo $country->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="state"><?php if ($this->lang->line('admin_drivers_state_province_region') != '') echo stripslashes($this->lang->line('admin_drivers_state_province_region')); else echo 'State / Province / Region'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="state" id="state" type="text" tabindex="8" class="required large tipTop" title="<?php if ($this->lang->line('driver_enter_driver_state') != '') echo stripslashes($this->lang->line('driver_enter_driver_state')); else echo 'Please enter the state'; ?>"/>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="city"><?php if ($this->lang->line('admin_drivers_city') != '') echo stripslashes($this->lang->line('admin_drivers_city')); else echo 'City'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="city" id="city" type="text" tabindex="9" class="required large tipTop" title="<?php if ($this->lang->line('location_enter_the_city') != '') echo stripslashes($this->lang->line('location_enter_the_city')); else echo 'Please enter the city'; ?>"/>
                                </div>
                            </div>
                        </li>




                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="postal_code"><?php if ($this->lang->line('admin_drivers_postal_code') != '') echo stripslashes($this->lang->line('admin_drivers_postal_code')); else echo 'Postal Code'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="postal_code" id="postal_code" type="text" tabindex="10" maxlength="10" class="required large tipTop" title="<?php if ($this->lang->line('location_enter_the_postalcode') != '') echo stripslashes($this->lang->line('location_enter_the_postalcode')); else echo 'Please enter the postal code'; ?>"/>
                                </div>
                            </div>
                        </li>


                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="mobile_number"><?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="dail_code" placeholder="<?php echo $d_country_code; ?>" style="width: 10% !important;" id="country_code" type="text"  tabindex="11" class="required small tipTop" title="<?php if ($this->lang->line('driver_enter_mobile_country_code') != '') echo stripslashes($this->lang->line('driver_enter_mobile_country_code')); else echo 'Please enter mobile country code'; ?>"/>
                                    <input name="mobile_number" placeholder="Mobile Number.." id="mobile_number" type="text" tabindex="6" class="required medium tipTop phoneNumber" maxlength="20" title="<?php if ($this->lang->line('driver_enter_mobile_number') != '') echo stripslashes($this->lang->line('driver_enter_mobile_number')); else echo 'Please enter the mobile number'; ?>" style="width: 38.5% !important;"/>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_identity') != '') echo stripslashes($this->lang->line('admin_drivers_identity')); else echo 'Identity'; ?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="thumbnail"><?php if ($this->lang->line('admin_drivers_driver_image') != '') echo stripslashes($this->lang->line('admin_drivers_driver_image')); else echo 'Driver Image'; ?></label>
                                <div class="form_input">
                                    <input name="thumbnail" id="thumbnail" type="file" tabindex="12" class="large tipTop" title="<?php if ($this->lang->line('driver_select_driver_image') != '') echo stripslashes($this->lang->line('driver_select_driver_image')); else echo 'Please select driver image'; ?>"/>
                                </div>
                            </div>
                        </li>

                        <input type="hidden" name="verify_status" value="No" />


                        <li>
                            <div class="form_grid_12">
                                <h3><?php if ($this->lang->line('admin_drivers_vehicle_information') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_information')); else echo 'Vehicle Information'; ?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="vehicle_type"><?php if ($this->lang->line('admin_drivers_vehicle_type') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_type')); else echo 'Vehicle Type'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_type" id="vehicle_type" style="height: 31px;  width: 51%;">
                                        <option value=""><?php if ($this->lang->line('driver_choose_vehicle_type') != '') echo stripslashes($this->lang->line('driver_choose_vehicle_type')); else echo 'Please choose vehicle type'; ?></option>
                                        <?php if ($vehicle_types->num_rows() > 0) { ?>
                                            <?php foreach ($vehicle_types->result() as $vehicles) { ?>
                                                <option value="<?php echo $vehicles->_id; ?>"><?php echo $vehicles->vehicle_type; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>


                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="vehicle_maker"><?php if ($this->lang->line('admin_drivers_vehicle_maker') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_maker')); else echo 'Vehicle Maker'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_maker" id="vehicle_maker" style="height: 31px;  width: 51%;">
                                        <?php if ($brandList->num_rows() > 0) { ?>
                                            <?php foreach ($brandList->result() as $brand) { ?>
                                                <option value="<?php echo $brand->_id; ?>"><?php echo $brand->brand_name; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="vehicle_model"><?php if ($this->lang->line('admin_drivers_vehicle_model') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_model')); else echo 'Vehicle Model'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_model" id="vehicle_model" style="height: 31px;  width: 51%;">
                                        <option value=""><?php if ($this->lang->line('dash_please_choose_vehicle_model') != '') echo stripslashes($this->lang->line('dash_please_choose_vehicle_model')); else echo 'Please choose vehicle model'; ?>...</option>
                                        <?php $sldmodelYrs=array(); 
											if ($modelList->num_rows() > 0) { $syr = 0;?>
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
                            </div>
                        </li>
						
						<li>
                            <div class="form_grid_12">
                                <label class="field_title" for="vehicle_model_year"><?php if ($this->lang->line('admin_drivers_year_of_model') != '') echo stripslashes($this->lang->line('admin_drivers_year_of_model')); else echo 'Year Of Model'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_model_year" id="vehicle_model_year" style="height: 31px;  width: 51%;">
                                        <option value=""><?php if ($this->lang->line('dash_please_choose_year_of_model') != '') echo stripslashes($this->lang->line('dash_please_choose_year_of_model')); else echo 'Please choose year of model'; ?>...</option>
                                        <?php 
											if (count($sldmodelYrs) > 0) { ?>
                                            <?php foreach ($sldmodelYrs as $modelyr) { 			
											?>
                                                <option value="<?php echo $modelyr; ?>"><?php echo $modelyr; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="vehicle_number"><?php if ($this->lang->line('admin_drivers_vehicle_number') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_number')); else echo 'License Plate'; ?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="vehicle_number" id="vehicle_number" type="text" tabindex="16" class="required large tipTop Vehicle_Number_Chk" title="<?php if ($this->lang->line('driver_enter_vechile_number') != '') echo stripslashes($this->lang->line('driver_enter_vechile_number')); else echo 'Please enter License Plate'; ?>"/>
                                    <label class="error_chk" id="vehicle_number_exist"></label>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="ac"><?php if ($this->lang->line('admin_drivers_air_conditioned') != '') echo stripslashes($this->lang->line('admin_drivers_air_conditioned')); else echo 'Air Conditioned'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <div class="ac_nonac">
                                        <input type="checkbox" tabindex="19" name="ac" checked="checked" id="ac_nonac_ac" class="ac_nonac"/>
                                    </div>
                                </div>
                            </div>
                        </li>




                        <?php if ($docx_list->num_rows() > 0) { ?>

                            <li>
                                <div class="form_grid_12">
                                    <h3><?php if ($this->lang->line('admin_drivers_driver_documents') != '') echo stripslashes($this->lang->line('admin_drivers_driver_documents')); else echo 'Driver Documents'; ?></h3>
                                </div>
                            </li>

                            <?php
                            foreach ($docx_list->result() as $docx) {
                                if ($docx->category == 'Driver') {
                                    $docx_uniq = 'docx-' . $docx->_id;
                                    ?>

                                    <li>
                                        <div class="form_grid_12">
                                            <label class="field_title" for="<?php echo $docx_uniq; ?>"><?php
                                                echo $docx->name;
                                                if ($docx->hasreq == 'Yes') {
                                                    echo '<span class="req">*</span>';
                                                }
                                                ?> </label>
                                            <div class="form_input">
                                                <input name="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>" data-docx="<?php echo $docx->name; ?>" data-docx_id="<?php echo $docx->_id; ?>" type="file" tabindex="17" class="large tipTop <?php
                                                if ($docx->hasreq == 'Yes') {
                                                    echo 'required';
                                                }
                                                ?> docx" title="<?php if ($this->lang->line('admin_please_select') != '') echo stripslashes($this->lang->line('admin_please_select')); else echo 'Please select'; ?> <?php echo strtolower($docx->name); ?>"/>
												
                                                <input type="hidden" name="driver_docx[]" value="<?php echo $docx->name; ?>|:||:|<?php echo (string)$docx->_id; ?>" id="<?php echo $docx_uniq; ?>-Hid" />
												
                                                <input type="hidden" name="driver_docx_expiry[]" value="<?php echo $docx->hasexp; ?>" />
                                                <span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
                                                <span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
                                                <a href="" target="_blank" id="<?php echo $docx_uniq; ?>-View"></a>
                                            </div>

                                            <?php if ($docx->hasexp == 'Yes') { ?>
                                                <label class="field_title"></label>
                                                <div class="form_input">
                                                    <div class="expiry_box">
                                                        <b><?php if ($this->lang->line('dash_expiry_date') != '') echo stripslashes($this->lang->line('dash_expiry_date')); else echo 'Expiry Date'; ?> : </b><input type="text"  id="expiry-<?php echo $docx_uniq; ?>" class="required" name="driver-<?php echo url_title($docx->name); ?>" /> 
                                                    </div>
                                                </div>

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
                                    </li>
                                    <?php
                                }
                            }
                        }
                        ?>


                        <?php if ($docx_list->num_rows() > 0) { ?>

                            <li>
                                <div class="form_grid_12">
                                    <h3><?php if ($this->lang->line('admin_drivers_vehicle_documents') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_documents')); else echo 'Vehicle Documents'; ?></h3>
                                </div>
                            </li>

                            <?php
                            foreach ($docx_list->result() as $docx) {
                                if ($docx->category == 'Vehicle') {
                                    $docx_uniq = 'docx-' . $docx->_id;
                                    ?>

                                    <li>
                                        <div class="form_grid_12">
                                            <label class="field_title" for="<?php echo $docx_uniq; ?>"><?php
                                                echo $docx->name;
                                                if ($docx->hasreq == 'Yes') {
                                                    echo '<span class="req">*</span>';
                                                }
                                                ?> </label>
                                            <div class="form_input">
                                                <input name="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>" data-docx="<?php echo $docx->name; ?>" data-docx_id="<?php echo $docx->_id; ?>" type="file" tabindex="18" class="large tipTop <?php
                                                if ($docx->hasreq == 'Yes') {
                                                    echo 'required';
                                                }
                                                ?> docx" title="<?php if ($this->lang->line('admin_please_select') != '') echo stripslashes($this->lang->line('admin_please_select')); else echo 'Please select'; ?> <?php echo strtolower($docx->name); ?>"/>
                                                <input type="hidden" name="vehicle_docx[]" value="<?php echo $docx->name; ?>|:||:|<?php echo (string)$docx->_id; ?>" id="<?php echo $docx_uniq; ?>-Hid" />
                                                <input type="hidden" name="vehicle_docx_expiry[]" value="<?php echo $docx->hasexp; ?>" />
                                                <span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
                                                <span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
                                                <a href="" target="_blank" id="<?php echo $docx_uniq; ?>-View"></a>
                                            </div>

                                            <?php if ($docx->hasexp == 'Yes') { ?>
                                                <label class="field_title"></label>
                                                <div class="form_input">
                                                    <div class="expiry_box">
                                                        <b><?php if ($this->lang->line('driver_exp_date') != '') echo stripslashes($this->lang->line('driver_exp_date')); else echo 'Expiry Date :'; ?> </b><input type="text"  id="expiry-<?php echo $docx_uniq; ?>" class="required" name="vehicle-<?php echo url_title($docx->name); ?>" /> 
                                                    </div>
                                                </div>

                                                <script>
                                                    $(function () {
                                                          var mdate = new Date('<?php echo date("F d,Y H:i:s"); ?>');
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker();
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeMonth", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "changeYear", "true");
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "minDate", mdate);
                                                        $("#expiry-<?php echo $docx_uniq; ?>").datepicker("option", "showAnim", "clip");  // drop,fold,slide,bounce,slideDown,blind
                                                    });
                                                </script>
                                                      
                                            <?php } ?>

                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                        }
                        ?>	

                        <input name="driver_id" type="hidden" value="" />

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="status"><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <div class="active_inactive">
                                        <input type="checkbox" tabindex="19" name="status" checked="checked" id="active_inactive_active" class="active_inactive"/>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <div class="form_input">
                                    <button type="submit" class="btn_small btn_blue" tabindex="20"><span><?php if ($this->lang->line('admin_subadmin_submit') != '') echo stripslashes($this->lang->line('admin_subadmin_submit')); else echo 'Submit'; ?></span></button>
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

<style>

    .expiry_box {
        background: none repeat scroll 0 0 gainsboro;
        border: 1px solid grey;
        border-radius: 5px;
        margin-top: 2%;
        padding: 1%;
        width: 23%;
    }


    .expiry_box input {
        width:50% !important;
        border-radius: 5px;
        border: 1px solid grey !important;
    }

</style>


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
			var option = '<option value="">Please choose year of model...</option>';
			if(modelYrs != ''){
				var modelYrsArr = modelYrs.split(',');
				for(var yr=0; yr < modelYrsArr.length; yr++){
					option = option+'<option>'+modelYrsArr[yr]+'</option>';
				}
			}
			$("#vehicle_model_year").html(option);
		});

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
                url: 'admin/drivers/ajax_document_upload?docx_name=' + docxId,
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
                        $("#" + docxId + "-View").html('View Uploaded Document');
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

<?php
$this->load->view('admin/templates/footer.php');
?>