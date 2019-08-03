<?php
$this->load->view('driver/templates/header.php');
?>
<div id="content">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php 
						if($this->lang->line('dash_edit_driver') != '') echo stripslashes($this->lang->line('dash_edit_driver')); else  echo 'Edit Driver';
						?></h6>
                </div>
                <div class="widget_content">
                    <?php
                    $attributes = array('class' => 'form_container left_label', 'id' => 'driver_form', 'enctype' => 'multipart/form-data');
                    echo form_open_multipart('driver/profile/insertEdit_driver', $attributes);

                    $driver_details = $driver_details->row();
                    ?>
                    <ul>



                        <li>
                            <div class="form_grid_12">
                                <h3><?php 
						if($this->lang->line('dash_your_location') != '') echo stripslashes($this->lang->line('dash_your_location')); else  echo 'Your Location';
						?></h3>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="driver_location"><?php 
						if($this->lang->line('dash_location') != '') echo stripslashes($this->lang->line('dash_location')); else  echo 'Location';
						?> 
						<span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="driver_location" id="driver_location" tabindex="1" class="required" style="height: 31px; width: 51%;" disabled>
                                        <?php
                                        if ($locationList->num_rows() > 0) {
                                            foreach ($locationList->result() as $loclist) {
                                                if (isset($loclist->avail_category)) {
                                                    $category_list = @implode($loclist->avail_category, ',');
                                                } else {
                                                    $category_list = '';
                                                }
                                                ?>
                                                <option value="<?php echo $loclist->_id; ?>" data-category="<?php echo $category_list; ?>" <?php if (isset($driver_details->driver_location)) if ($driver_details->driver_location == $loclist->_id) echo 'selected="selected"' ?>>
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
                                <h3><?php 
						if($this->lang->line('dash_driver_category') != '') echo stripslashes($this->lang->line('dash_driver_category')); else  echo 'Driver Category';
						?></h3>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="driver_name"><?php 
						if($this->lang->line('dash_driver_category') != '') echo stripslashes($this->lang->line('dash_driver_category')); else  echo 'Driver Category';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="category" id="category" tabindex="1" class="required" style="height: 31px; width: 51%;">
                                        <option value="" data-vehicle=''><?php 
						if($this->lang->line('dash_choose_driver_cat') != '') echo stripslashes($this->lang->line('dash_choose_driver_cat')); else  echo 'Choose driver category';
						?></option>
                                        <?php
                                        if ($categoryList->num_rows() > 0) {
                                            foreach ($categoryList->result() as $category) {
                                                if (isset($category->vehicle_type)) {
                                                    $vehicle_type = @implode($category->vehicle_type, ',');
                                                } else {
                                                    $vehicle_type = '';
                                                }
                                                ?>
                                                <option value="<?php echo $category->_id; ?>" data-vehicle="<?php echo $vehicle_type; ?>" <?php if (isset($driver_details->category)) if ($driver_details->category == $category->_id) echo 'selected="selected"' ?>>
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
                                <h3><?php 
						if($this->lang->line('dash_login_details') != '') echo stripslashes($this->lang->line('dash_login_details')); else  echo 'Login Details';
						?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="driver_name"><?php 
						if($this->lang->line('dash_driver_name') != '') echo stripslashes($this->lang->line('dash_driver_name')); else  echo 'Driver Name';
						?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="driver_name" id="driver_name" type="text" tabindex="1" class="required large tipTop" title="<?php 
						if($this->lang->line('dash_enter_diver_full_name') != '') echo stripslashes($this->lang->line('dash_enter_diver_full_name')); else  echo 'Please enter the diver full name';
						?>" value="<?php if (isset($driver_details->driver_name)) echo $driver_details->driver_name; ?>" />
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="email"><?php 
						if($this->lang->line('dash_email_address') != '') echo stripslashes($this->lang->line('dash_email_address')); else  echo 'Email Address';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="email" id="email" type="text" tabindex="4" class="required large tipTop email" title="<?php 
						if($this->lang->line('dash_enter_diver_email') != '') echo stripslashes($this->lang->line('dash_enter_diver_email')); else  echo 'Please enter the diver email address';
						?>" value="<?php if (isset($driver_details->email)) echo $driver_details->email; ?>" readonly onkeypress="javascript: alert('<?php 
						if($this->lang->line('dash_can_not_change_email') != '') echo stripslashes($this->lang->line('dash_can_not_change_email')); else  echo 'You can not change email';
						?>');" disabled="disabled" />
						
							<a href="driver/profile/change_email_form" target="_blank" style="background: #dfdfdf; padding: 5px 12px;"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else  echo 'Edit'; ?></a>
						
                                </div>
                            </div>
                        </li>


                        <li>
                            <div class="form_grid_12">
                                <h3><?php 
						if($this->lang->line('dash_address_details') != '') echo stripslashes($this->lang->line('dash_address_details')); else  echo 'Address Details';
						?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="address"><?php 
						if($this->lang->line('dash_address') != '') echo stripslashes($this->lang->line('dash_address')); else  echo 'Address';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <textarea name="address" id="address" tabindex="1" class="required large tipTop" style="width: 372px;"><?php if (isset($driver_details->address['address'])) echo $driver_details->address['address']; ?></textarea>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="county"><?php 
						if($this->lang->line('dash_country') != '') echo stripslashes($this->lang->line('dash_country')); else  echo 'Country';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select name="county" id="county" tabindex="1" class="required chzn-select" style="height: 31px; width: 51%;">
                                        <?php foreach ($countryList as $country) { ?>
                                            <option value="<?php echo $country->name; ?>" data-dialCode="<?php echo $country->dial_code; ?>" <?php if ($driver_details->address['county'] == $country->name) echo 'selected="selected"' ?>><?php echo $country->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="state"><?php 
						if($this->lang->line('dash_state_province_region') != '') echo stripslashes($this->lang->line('dash_state_province_region')); else  echo 'State / Province / Region';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="state" id="state" type="text" tabindex="1" class="required large tipTop" title="<?php 
						if($this->lang->line('dash_enter_the_state') != '') echo stripslashes($this->lang->line('dash_enter_the_state')); else  echo 'Please enter the state';
						?>" value="<?php if (isset($driver_details->address['state'])) echo $driver_details->address['state']; ?>"/>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="city"><?php 
						if($this->lang->line('dash_city') != '') echo stripslashes($this->lang->line('dash_city')); else  echo 'City';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="city" id="city" type="text" tabindex="1" class="required large tipTop" title="<?php 
						if($this->lang->line('dash__enter_the_city') != '') echo stripslashes($this->lang->line('dash__enter_the_city')); else  echo 'Please enter the city';
						?>" value="<?php if (isset($driver_details->address['city'])) echo $driver_details->address['city']; ?>"/>
                                </div>
                            </div>
                        </li>




                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="postal_code"><?php 
						if($this->lang->line('dash_postal_code') != '') echo stripslashes($this->lang->line('dash_postal_code')); else  echo 'Postal Code';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="postal_code" id="postal_code" type="text" tabindex="1"  maxlength="10" class="required large tipTop" title="<?php 
						if($this->lang->line('driver_enter_postal_code') != '') echo stripslashes($this->lang->line('driver_enter_postal_code')); else  echo 'Please enter the postal code';
						?>" value="<?php if (isset($driver_details->address['postal_code'])) echo $driver_details->address['postal_code']; ?>"/>
                                </div>
                            </div>
                        </li>


                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="mobile_number"><?php 
						if($this->lang->line('dash_mobile_number') != '') echo stripslashes($this->lang->line('dash_mobile_number')); else  echo 'Mobile Number';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="dail_code" placeholder="<?php echo $d_country_code; ?>" id="country_code" type="text" style="width: 10% !important;" tabindex="6" class="required large tipTop" value="<?php if (isset($driver_details->dail_code)) echo $driver_details->dail_code; ?>" disabled readonly />
                                    <input name="mobile_number"  id="mobile_number" type="text" tabindex="6" class="required large tipTop phoneNumber" maxlength="20" style="width: 38.5% !important;" value="<?php if (isset($driver_details->mobile_number)) echo $driver_details->mobile_number; ?>" disabled readonly />
									<a href="driver/profile/change_mobile_form" target="_blank" style="background: #dfdfdf; padding: 5px 12px;"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else  echo 'Edit'; ?></a>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <h3><?php 
						if($this->lang->line('dash_identity') != '') echo stripslashes($this->lang->line('dash_identity')); else  echo 'Identity';
						?></h3>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="thumbnail"><?php 
						if($this->lang->line('dash_driver_image') != '') echo stripslashes($this->lang->line('dash_driver_image')); else  echo 'Driver Image';
						?></label>
                                <div class="form_input">
                                    <input name="thumbnail" id="thumbnail" type="file" tabindex="7" class="large tipTop" title="<?php 
						if($this->lang->line('dash_choose_driver_image') != '') echo stripslashes($this->lang->line('dash_choose_driver_image')); else  echo 'Please choose driver image';
						?>" value="<?php if (isset($driver_details->driver_name)) echo $driver_details->driver_name; ?>"/>

                                    <br/>

                                    <?php if (isset($driver_details->image) != '') { ?>
                                        <img width="15%" src="<?php echo base_url() . USER_PROFILE_THUMB . $driver_details->image; ?>" />
                                    <?php } else { ?>
                                        <img width="15%" src="<?php echo base_url() . USER_PROFILE_IMAGE_DEFAULT; ?>" />
                                    <?php } ?>


                                </div>
                            </div>
                        </li>



                        <li>
                            <div class="form_grid_12">
                                <h3><?php 
						if($this->lang->line('dash_vehicle_information') != '') echo stripslashes($this->lang->line('dash_vehicle_information')); else  echo 'Vehicle Information';
						?></h3>
                            </div>
                        </li>


                        <?php if ($vehicle_types->num_rows() > 0) { ?>
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title" for="vehicle_type"><?php 
						if($this->lang->line('dash_vehicle_type') != '') echo stripslashes($this->lang->line('dash_vehicle_type')); else  echo 'Vehicle Type';
						?><span class="req">*</span></label>
                                    <div class="form_input">
                                        <select class="required"  name="vehicle_type" id="vehicle_type" style="height: 31px; width: 51%;">
                                            <option value=""><?php 
						if($this->lang->line('dash_please_choose_vehicle_type') != '') echo stripslashes($this->lang->line('dash_please_choose_vehicle_type')); else  echo 'Please choose vehicle type';
						?>... </option>
                                            <?php foreach ($vehicle_types->result() as $vehicles) { ?>
                                                <option value="<?php echo $vehicles->_id; ?>" <?php if ($driver_details->vehicle_type == $vehicles->_id) echo 'selected="selected"'; ?>><?php echo $vehicles->vehicle_type; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="vehicle_maker"><?php 
						if($this->lang->line('dash_vehicle_maker') != '') echo stripslashes($this->lang->line('dash_vehicle_maker')); else  echo 'Vehicle Maker';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_maker" id="vehicle_maker" style="height: 31px;  width: 51%;">
                                        <?php if ($brandList->num_rows() > 0) { ?>
                                            <?php foreach ($brandList->result() as $brand) { ?>
                                                <option value="<?php echo $brand->_id; ?>" <?php
                                                if (isset($driver_details->vehicle_maker)) {
                                                    if ($driver_details->vehicle_maker == $brand->_id)
                                                        echo 'selected="selected"';
                                                }
                                                ?>><?php echo $brand->brand_name; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="vehicle_model"><?php 
						if($this->lang->line('dash_vehicle_model') != '') echo stripslashes($this->lang->line('dash_vehicle_model')); else  echo 'Vehicle Model';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_model" id="vehicle_model" style="height: 31px;  width: 51%;">
                                        <option value=""><?php 
						if($this->lang->line('dash_please_choose_vehicle_model') != '') echo stripslashes($this->lang->line('dash_please_choose_vehicle_model')); else  echo 'Please choose vehicle model';
						?>...</option>
                                        <?php 
										$sldmodelYrs=array(); 
										if ($modelList->num_rows() > 0) { ?>
                                            <?php foreach ($modelList->result() as $model) {

												$modelYears = '';
												if(isset($model->year_of_model))$modelYears = @implode(',',$model->year_of_model);
												if ($driver_details->vehicle_model == $model->_id){
													if(isset($model->year_of_model)) $sldmodelYrs = $model->year_of_model;
												}

											?>
                                                <option value="<?php echo $model->_id; ?>" data-years="<?php echo $modelYears; ?>" data-vmodel="<?php echo $model->brand . '_' . $model->type; ?>" <?php
                                                if (isset($driver_details->vehicle_model)) {
                                                    if ($driver_details->vehicle_model == $model->_id)
                                                        echo 'selected="selected"';
                                                }
                                                ?>><?php echo $model->name; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>
						<li>
                            <div class="form_grid_12">
                                <label class="field_title" for="vehicle_model_year"><?php 
						if($this->lang->line('dash_year_of_model') != '') echo stripslashes($this->lang->line('dash_year_of_model')); else  echo 'Year Of Model';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <select class="required"  name="vehicle_model_year" id="vehicle_model_year" style="height: 31px;  width: 51%;">
                                        <option value=""><?php 
						if($this->lang->line('dash_please_choose_year_of_model') != '') echo stripslashes($this->lang->line('dash_please_choose_year_of_model')); else  echo 'Please choose year of model';
						?>...</option>
                                        <?php 
											if (count($sldmodelYrs) > 0) { ?>
                                            <?php foreach ($sldmodelYrs as $modelyr) { 			
											?>
                                                <option value="<?php echo $modelyr; ?>" <?php 
												if(isset($driver_details->vehicle_model_year)){ 
												if($driver_details->vehicle_model_year == $modelyr) echo 'selected="selected"';}
												?>><?php echo $modelyr; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="vehicle_number"><?php 
						if($this->lang->line('dash_vehicle_number') != '') echo stripslashes($this->lang->line('dash_vehicle_number')); else  echo 'License Plate';
						?><span class="req">*</span></label>
                                <div class="form_input">
                                    <input name="vehicle_number" id="vehicle_number" type="text" tabindex="6" class="required large tipTop Vehicle_Number_Chk" title="<?php 
						if($this->lang->line('dash_enter_vehicle_number') != '') echo stripslashes($this->lang->line('dash_enter_vehicle_number')); else  echo 'Please enter  License Plate';
						?>" value="<?php if (isset($driver_details->vehicle_number)) echo $driver_details->vehicle_number; ?>"/>
                                    <label class="error_chk" id="vehicle_number_exist"></label>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="ac"><?php 
						if($this->lang->line('dash_air_conditioned') != '') echo stripslashes($this->lang->line('dash_air_conditioned')); else  echo 'Air Conditioned';
						?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <div class="ac_nonac">
                                        <input type="checkbox" tabindex="8" name="ac" id="ac_nonac_ac" class="ac_nonac" <?php
                                        if (isset($driver_details->ac)) {
                                            if ($driver_details->ac == 'Yes') {
                                                echo 'checked="checked"';
                                            }
                                        } else {
                                            echo 'checked="checked"';
                                        }
                                        ?>/>
                                    </div>
                                </div>
                            </div>
                        </li>




                        <?php if ($docx_list->num_rows() > 0) { ?>

                            <li>
                                <div class="form_grid_12">
                                    <h3><?php 
						if($this->lang->line('dash_driver_documents') != '') echo stripslashes($this->lang->line('dash_driver_documents')); else  echo 'Driver Documents';
						?></h3>
                                </div>
                            </li>

                            <?php
                            $doc = 0;
                            foreach ($docx_list->result() as $docx) {
                                if ($docx->category == 'Driver') {
                                    $docx_uniq = 'docx-' . $docx->_id;

                                    $docxValues = '';
                                    $expiryValue = '';
                                    $fileName = '';
                                    if (isset($driver_details->documents) && isset($driver_details->documents['driver'][(string) $docx->_id])) {
                                        $did = (string) $docx->_id;
                                        if (!isset($driver_details->documents['driver'][$did]['typeName'])) {
                                            continue;
                                        }
										
										if(isset($driver_details->documents['driver'][(string) $docx->_id]['fileName'])){
											$fileName = $driver_details->documents['driver'][(string) $docx->_id]['fileName'];
										}
										
										if(isset($driver_details->documents['driver'][(string) $docx->_id]['expiryDate'])){
											$expiryValue = $driver_details->documents['driver'][(string) $docx->_id]['expiryDate'];
										}
										
										if(isset($driver_details->documents['driver'][(string) $docx->_id]['typeName'])){
											$typeName = $driver_details->documents['driver'][(string) $docx->_id]['typeName'];
											$docxValues = $typeName . '|:|' . $fileName . '|:|' . (string) $docx->_id . '|:|Old-docx';
											
										} else {
											$docxValues = $docx->name . '|:||:|' . (string) $docx->_id . '|:|Old-docx';
										}
									

										
                                    }
									
									
									
									
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
                                                <input name="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>" data-docx="<?php echo $docx->name; ?>" data-docx_id="<?php echo $docx->_id; ?>" type="file" tabindex="7" value="" class="large tipTop <?php
                                                if ($docx->hasreq == 'Yes' && $fileName == '') {
                                                    echo 'required';
                                                }
                                                ?> docx" title="Please select <?php echo strtolower($docx->name); ?>"/>
                                                <input type="hidden" name="driver_docx[]" value="<?php echo $docxValues; ?>" id="<?php echo $docx_uniq; ?>-Hid" />
                                                <input type="hidden" name="driver_docx_expiry[]" value="<?php echo $docx->hasexp; ?>" />
                                                <span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
                                                <span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
                                                <?php
                                                if ($fileName != '') {
                                                    ?>
                                                    <a href="drivers_documents/<?php echo $fileName; ?>" target="_blank" id="<?php echo $docx_uniq; ?>-View"> <?php 
						if($this->lang->line('dash_view_document') != '') echo stripslashes($this->lang->line('dash_view_document')); else  echo 'View Document';
						?></a>
                                                <?php } else { ?>
                                                    <a href="drivers_documents/<?php echo $fileName; ?>" target="_blank" id="<?php echo $docx_uniq; ?>-View"></a>
                                                <?php } ?>
                                            </div>

                                            <?php if ($docx->hasexp == 'Yes') { ?>
                                                <label class="field_title"></label>
                                                <div class="form_input">
                                                    <div class="expiry_box">
                                                        <b><?php 
						if($this->lang->line('dash_expiry_date') != '') echo stripslashes($this->lang->line('dash_expiry_date')); else  echo 'Expiry Date';
						?> : </b>
                                                        <input type="text"  id="expiry-<?php echo $docx_uniq; ?>" class="required" name="driver-<?php echo url_title($docx->name); ?>"  value="<?php echo $expiryValue; ?>"/> 
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
                                    $doc++;
                                }
                            }
                        }
                        ?>

                        <?php
                        if ($docx_list->num_rows() > 0) {
                            ?>
                            <li>
                                <div class="form_grid_12">
                                    <h3><?php 
						if($this->lang->line('dash_vehicle_documents') != '') echo stripslashes($this->lang->line('dash_vehicle_documents')); else  echo 'Vehicle Documents';
						?></h3>
                                </div>
                            </li>
                            <?php
                            $doc = 0;
                            foreach ($docx_list->result() as $docx) {
                                if ($docx->category == 'Vehicle') {
                                    $docx_uniq = 'docx-' . $docx->_id;

                                    $docxValues = '';
                                    $expiryValue = '';
                                    $fileName = '';
                                    if (isset($driver_details->documents)) {
                                        $did = (string) $docx->_id;
                                        if (!isset($driver_details->documents['vehicle'][$did]['typeName'])) {
                                            #continue;
                                        }
                                        
										
										
										if(isset($driver_details->documents['vehicle'][$did]['fileName'])){
											$fileName = $driver_details->documents['vehicle'][$did]['fileName'];
										}
										
										if(isset($driver_details->documents['vehicle'][$did]['expiryDate'])){
											$expiryValue = $driver_details->documents['vehicle'][$did]['expiryDate'];
										}
                                        
										if(isset($driver_details->documents['vehicle'][$did]['typeName'])) {
											$typeName = $driver_details->documents['vehicle'][$did]['typeName'];
											$docxValues = $typeName . '|:|' . $fileName . '|:|' . $did . '|:|Old-docx';
											
                                        } else {
											$docxValues = $docx->name . '|:||:|' . (string) $docx->_id . '|:|Old-docx';
										}
										
                                    }
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
                                                <input name="<?php echo $docx_uniq; ?>" id="<?php echo $docx_uniq; ?>" data-docx="<?php echo $docx->name; ?>" data-docx_id="<?php echo $docx->_id; ?>" type="file" tabindex="7" value="" class="large tipTop <?php
                                                if ($docx->hasreq == 'Yes' && $fileName == '') {
                                                    echo 'required';
                                                }
                                                ?> docx" title="Please select <?php echo strtolower($docx->name); ?>"/>
                                                <input type="hidden" name="vehicle_docx[]" value="<?php echo $docxValues; ?>" id="<?php echo $docx_uniq; ?>-Hid" />
                                                <input type="hidden" name="vehicle_docx_expiry[]" value="<?php echo $docx->hasexp; ?>" />
                                                <span id="<?php echo $docx_uniq; ?>-Err" style="color:red;"></span>
                                                <span id="<?php echo $docx_uniq; ?>-Succ" style="color:green;"></span>
                                                <?php
                                                if ($fileName != '') {
                                                    ?>
                                                    <a href="drivers_documents/<?php echo $fileName; ?>" target="_blank" id="<?php echo $docx_uniq; ?>-View"> <?php 
						if($this->lang->line('dash_view_document') != '') echo stripslashes($this->lang->line('dash_view_document')); else  echo 'View Document';
						?> </a>
                                                <?php } else { ?>
                                                    <a href="drivers_documents/<?php echo $fileName; ?>" target="_blank" id="<?php echo $docx_uniq; ?>-View"></a>
                                                <?php } ?>
                                            </div>

                                            <?php if ($docx->hasexp == 'Yes') { ?>
                                                <label class="field_title"></label>
                                                <div class="form_input">
                                                    <div class="expiry_box">
                                                        <b><?php 
						if($this->lang->line('dash_expiry_date') != '') echo stripslashes($this->lang->line('dash_expiry_date')); else  echo 'Expiry Date';
						?> : </b>
                                                        <input type="text"  id="expiry-<?php echo $docx_uniq; ?>" class="required" name="vehicle-<?php echo url_title($docx->name); ?>"  value="<?php echo $expiryValue; ?>"/> 
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
                                    $doc++;
                                }
                            }
                        }
                        ?>	

                        <input name="driver_id" type="hidden" value="<?php echo $driver_details->_id; ?>" />

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title" for="status"><?php 
						if($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else  echo 'Status';
						?> <span class="req">*</span></label>
                                <div class="form_input">
                                    <div class="active_inactive">
                                        <input type="checkbox" tabindex="8" name="status" id="active_inactive_active" class="active_inactive" <?php
                                        if (isset($driver_details->status)) {
                                            if ($driver_details->status == 'Active') {
                                                echo 'checked="checked"';
                                            }
                                        } else {
                                            echo 'checked="checked"';
                                        }
                                        ?>/>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <div class="form_input">
                                    <button type="submit" class="btn_small btn_blue" tabindex="9"><span><?php 
						if($this->lang->line('dash_submit') != '') echo stripslashes($this->lang->line('dash_submit')); else  echo 'Submit';
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

<?php
$this->load->view('driver/templates/footer.php');
?>