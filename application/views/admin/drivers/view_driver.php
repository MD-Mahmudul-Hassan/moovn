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
						<h6><?php if ($this->lang->line('admin_drivers_global_site_configuration') != '') echo stripslashes($this->lang->line('admin_drivers_global_site_configuration')); else echo 'Global Site Configuration'; ?></h6>
						<div id="widget_tab">
							<ul>
								<li><a href="#tab1" class="active_tab"><?php if ($this->lang->line('admin_drivers_driver_details') != '') echo stripslashes($this->lang->line('admin_drivers_driver_details')); else echo 'Driver Details'; ?></a></li>
								<li><a href="#tab2"><?php if ($this->lang->line('admin_drivers_vehicle_details') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_details')); else echo 'Vehicle Details'; ?></a></li>
								<li><a href="#tab3"><?php if ($this->lang->line('admin_drivers_documents') != '') echo stripslashes($this->lang->line('admin_drivers_documents')); else echo 'Documents'; ?></a></li>
								<li><a href="#tab4"><?php if ($this->lang->line('admin_drivers_banking') != '') echo stripslashes($this->lang->line('admin_drivers_banking')); else echo 'Banking'; ?></a></li>
							</ul>
						</div>
					</div>
					<div class="widget_content">
						<?php 
							$attributes = array('class' => 'form_container left_label ajaxsubmit', 'id' => 'settings_form', 'enctype' => 'multipart/form-data');
							echo form_open_multipart('admin/adminlogin/admin_global_settings',$attributes);
						
							$driver_details = $driver_details->row();
						
						?>
							<div id="tab1">
								<ul>
								
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="admin_name"><?php if ($this->lang->line('admin_drivers_driver_category') != '') echo stripslashes($this->lang->line('admin_drivers_driver_category')); else echo 'Driver Category'; ?></label>
											<div class="form_input">
												<p><?php echo $driver_category->row()->name;?></p>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="admin_name"><?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?></label>
											<div class="form_input">
												<p><?php echo $driver_details->driver_name;?></p>
											</div>
										</div>
									</li>
									
									<li>
									<div class="form_grid_12">
										<label class="field_title" for="email"><?php if ($this->lang->line('admin_drivers_email_address') != '') echo stripslashes($this->lang->line('admin_drivers_email_address')); else echo 'Email Address'; ?> </label>
										<div class="form_input">
											<p>											
											<?php if($isDemo){ ?>
											<?php echo $dEmail; ?>
											<?php }  else{ ?>
											<?php echo $driver_details->email;?>
											<?php } ?>
											</p>											
										</div>
									</div>
									</li>
	
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="logo_image"><?php if ($this->lang->line('admin_drivers_driver_profile_image') != '') echo stripslashes($this->lang->line('admin_drivers_driver_profile_image')); else echo 'Driver Profile Image'; ?></label>
											<div class="form_input">
												<?php if (isset($driver_details->image) != ''){?>
												 <img width="15%" src="<?php echo base_url().USER_PROFILE_THUMB.$driver_details->image;?>" />
												<?php }else {?>
												 <img width="15%" src="<?php echo base_url().USER_PROFILE_THUMB_DEFAULT;?>" />
												<?php }?>
											</div>
										</div>
									</li>
		
									
								<li>
									<h3><?php if ($this->lang->line('admin_drivers_login_details') != '') echo stripslashes($this->lang->line('admin_drivers_login_details')); else echo 'Location Details'; ?></h3>
								</li>
									
									<li>
									<div class="form_grid_12">
										<label class="field_title" for="address"><?php if ($this->lang->line('admin_drivers_address') != '') echo stripslashes($this->lang->line('admin_drivers_address')); else echo 'Address'; ?></label>
										<div class="form_input">
											<p><?php if(isset($driver_details->address['address'])) echo $driver_details->address['address']; ?></p>
										</div>
									</div>
									</li>
									
									<li>
									<div class="form_grid_12">
										<label class="field_title" for="Country"><?php if ($this->lang->line('admin_drivers_country') != '') echo stripslashes($this->lang->line('admin_drivers_country')); else echo 'Country'; ?></label>
										<div class="form_input">
												<p><?php echo $driver_details->address['county']; ?></p>
											</select>
										</div>
									</div>
									</li>
									
									<li>
									<div class="form_grid_12">
										<label class="field_title" for="state"><?php if ($this->lang->line('admin_drivers_state_province_region') != '') echo stripslashes($this->lang->line('admin_drivers_state_province_region')); else echo 'State / Province / Region'; ?></label>
										<div class="form_input">
											<p><?php if(isset($driver_details->address['state'])) echo $driver_details->address['state']; ?></p>
										</div>
									</div>
									</li>
									
									<li>
									<div class="form_grid_12">
										<label class="field_title" for="city"><?php if ($this->lang->line('admin_drivers_city') != '') echo stripslashes($this->lang->line('admin_drivers_city')); else echo 'City'; ?></label>
										<div class="form_input">
											<p><?php if(isset($driver_details->address['city'])) echo $driver_details->address['city']; ?></p>
										</div>
									</div>
									</li>

									<li>
									<div class="form_grid_12">
										<label class="field_title" for="postal_code"><?php if ($this->lang->line('admin_drivers_postal_code') != '') echo stripslashes($this->lang->line('admin_drivers_postal_code')); else echo 'Postal Code'; ?></label>
										<div class="form_input">
											<p><?php if(isset($driver_details->address['postal_code'])) echo $driver_details->address['postal_code']; ?></p>
										</div>
									</div>
									</li>

									<li>
									<div class="form_grid_12">
										<label class="field_title" for="mobile_number"><?php if ($this->lang->line('admin_drivers_mobile_number') != '') echo stripslashes($this->lang->line('admin_drivers_mobile_number')); else echo 'Mobile Number'; ?></label>
										<div class="form_input">
											<p><?php if(isset($driver_details->dail_code)) echo $driver_details->dail_code; ?>
											<?php if(isset($driver_details->mobile_number)) echo $driver_details->mobile_number; ?></p>
										</div>
									</div>
									</li>
									
									<li>
									<div class="form_grid_12">
										<label class="field_title" for="mobile_number"><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?></label>
										<div class="form_input">
											<p><?php if(isset($driver_details->status)) echo get_language_value_for_keyword($driver_details->status,$this->data['langCode']); ?></p>
										</div>
									</div>
									</li>
								
								</ul>
								
								<ul>
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<a  href="admin/drivers/display_drivers_list" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_drivers_back_to_driver_list') != '') echo stripslashes($this->lang->line('admin_drivers_back_to_driver_list')); else echo 'Back To Drivers List'; ?></span></a>
											</div>
										</div>
									</li>
								</ul>
							</div>
						
						
							<div id="tab2">
								<ul>
									<li>
										<h3><?php if ($this->lang->line('admin_drivers_vehicle_details') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_details')); else echo 'Vehicle Details'; ?></h3>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_type') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_type')); else echo 'Vehicle Type'; ?></label>
											<div class="form_input">
												<p><?php echo $vehicle_types->row()->vehicle_type;?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_maker') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_maker')); else echo 'Vehicle Maker'; ?></label>
											<div class="form_input">
												<p><?php if(isset($vehicle_maker->row()->brand_name)) echo $vehicle_maker->row()->brand_name; else echo 'Not Available'; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_model') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_model')); else echo 'Vehicle Model'; ?></label>
											<div class="form_input">
												<p><?php if(isset($vehicle_model->row()->name)) echo $vehicle_model->row()->name; else echo 'Not Available'; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_air_conditioned') != '') echo stripslashes($this->lang->line('admin_drivers_air_conditioned')); else echo 'Air Conditioned'; ?></label>
											<div class="form_input">
												<p><?php if(isset($driver_details->ac)){ if ($driver_details->ac == 'Yes') echo 'A/C'; else echo 'Non A/C';  }else{echo 'Non A/C';} ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title"><?php if ($this->lang->line('admin_drivers_vehicle_number') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_number')); else echo 'License Plate'; ?></label>
											<div class="form_input">
												<p><?php if(isset($driver_details->vehicle_number)) echo $driver_details->vehicle_number; ?></p>
											</div>
										</div>
									</li>
								</ul>
								<ul>
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<a  href="admin/drivers/display_drivers_list" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_drivers_back_to_driver_list') != '') echo stripslashes($this->lang->line('admin_drivers_back_to_driver_list')); else echo 'Back To Drivers List'; ?></span></a>
											</div>
										</div>
									</li>
								</ul>
							</div>
				
							<div id="tab3">
								<ul>
									<li>
											<h3><?php if ($this->lang->line('admin_drivers_driver_documents') != '') echo stripslashes($this->lang->line('admin_drivers_driver_documents')); else echo 'Driver Documents'; ?></h3>
									</li>
									
								<?php 
								if(isset($driver_details->documents['driver'])){
									$driver_docx = $driver_details->documents['driver'];
								} else {
									$driver_docx = array();
								}
										
								if(count($driver_docx) > 0){
									foreach($driver_docx as $docx_key => $drivers_doc){  #echo '<pre>'; print_r($drivers_doc); die;
								?>
									
									<li>
								<div class="form_grid_12">
									<label class="field_title"><?php if(isset($drivers_doc['typeName'])) echo $drivers_doc['typeName']; ?> </label>
									<div class="form_input expiry_box">
										<?php 
											if(isset($drivers_doc['fileName'])){
												if($drivers_doc['fileName'] != ''){
										?>
											<a href="drivers_documents/<?php echo $drivers_doc['fileName']; ?>" target="_blank" > <?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?> <?php echo $drivers_doc['typeName']; ?> </a>
										<?php } else { ?>
											<a><?php echo $drivers_doc['typeName'] ?> <?php if ($this->lang->line('admin_drivers_not_available') != '') echo stripslashes($this->lang->line('admin_drivers_not_available')); else echo 'Not Available'; ?></a>
										<?php } 
											}
										?>
										<?php 
										if(isset($drivers_doc['expiryDate'])){ 
											if($drivers_doc['expiryDate'] != ''){
										?>
											<p><b><?php if ($this->lang->line('admin_drivers_expiry_date') != '') echo stripslashes($this->lang->line('admin_drivers_expiry_date')); else echo 'Expiry Date'; ?> : </b><?php echo date('M,d Y',strtotime($drivers_doc['expiryDate'])); ?></p>
										<?php 
											}
										} ?>	
									</div>
									<?php 
										if(isset($drivers_doc['verify_status'])){
											if($drivers_doc['verify_status'] == 'Yes'){
												$docx_status = 'Verified';
												$state_color = 'background:green';
											} else {
												$docx_status = 'Verify';
												$state_color = 'background:red';
											}
										} else {
											$docx_status = 'Verify';
											$state_color = '';
										}										
									
									?>
									<a class="expiry_box_status" href="javascript:void(0);" id="docx_vrf_<?php echo $docx_key; ?>" onclick="verify_docx('<?php echo $docx_key; ?>','<?php echo $driver_details->_id;?>','driver','<?php echo $docx_status; ?>');" style="<?php echo $state_color; ?>"><?php echo $docx_status; ?></a>
								</div>
								</li>
						<?php 
										}
									} else {
							?>
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="vehicle_number"></label>
										<div class="form_input">
											<p><?php if ($this->lang->line('admin_drivers_no_record_found') != '') echo stripslashes($this->lang->line('admin_drivers_no_record_found')); else echo 'No records found'; ?></p>
										</div>
									</div>
								</li>
							<?php 
								}
							?>
							
							<li>
								<h3><?php if ($this->lang->line('admin_drivers_vehicle_documents') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_documents')); else echo 'Vehicle Documents'; ?></h3>
							</li>
									
								<?php 
										if(isset($driver_details->documents['vehicle'])){
											$vehicle_docx = $driver_details->documents['vehicle'];
										} else {
											$vehicle_docx = array();
										}
										
										if(count($vehicle_docx) > 0){
											foreach($vehicle_docx as $veh_docx_key => $vehicles_doc){  #echo '<pre>'; print_r($drivers_doc); die;
								?>
									
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if(isset($vehicles_doc['typeName'])) echo $vehicles_doc['typeName']; ?> </label>
										<div class="form_input expiry_box">
											<?php 
												if(isset($vehicles_doc['fileName'])){
													if($vehicles_doc['fileName'] != ''){
											?>
												<a href="drivers_documents/<?php echo $vehicles_doc['fileName']; ?>" target="_blank" > <?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?> <?php echo $vehicles_doc['typeName']; ?> </a>
											<?php } else { ?>
												<a><?php echo $vehicles_doc['typeName'] ?> <?php if ($this->lang->line('admin_drivers_not_available') != '') echo stripslashes($this->lang->line('admin_drivers_not_available')); else echo 'Not Available'; ?></a>
											<?php } 
												}
											?>
										
										
										<?php 
										if(isset($vehicles_doc['expiryDate'])){ 
											if($vehicles_doc['expiryDate'] != ''){
										?>
											<p><b><?php if ($this->lang->line('admin_drivers_expiry_date') != '') echo stripslashes($this->lang->line('admin_drivers_expiry_date')); else echo 'Expiry Date'; ?> : </b><?php echo date('M,d Y',strtotime($vehicles_doc['expiryDate'])); ?></p>
										<?php 
											}
										} ?>
										</div>
										<div>
										<?php 
											if(isset($vehicles_doc['verify_status'])){
												if($vehicles_doc['verify_status'] == 'Yes'){
													$docx_status = 'Verified';
													$state_color = 'background:green';
												} else {
													$docx_status = 'Verify';
													$state_color = 'background:red';
												}
											} else {
												$docx_status = 'Verify';
												$state_color = '';
											}										
										
										?>
											<a class="expiry_box_status" href="javascript:void(0);" id="docx_vrf_<?php echo $veh_docx_key; ?>" onclick="verify_docx('<?php echo $veh_docx_key; ?>','<?php echo $driver_details->_id;?>','vehicle','<?php echo $docx_status; ?>');" style="<?php echo $state_color; ?>"><?php echo $docx_status; ?></a>
										</div>
									</div>
								</li>
									
									
						<?php 
										}
									} else {
							?>

							<li>
									<div class="form_grid_12">
											<label class="field_title" for="vehicle_number"></label>
											<div class="form_input">
												<p><?php if ($this->lang->line('admin_drivers_no_record_found') != '') echo stripslashes($this->lang->line('admin_drivers_no_record_found')); else echo 'No records found'; ?></p>
											</div>
										</div>
									</li>
							<?php 
							}
							?>
								</ul>
								<ul>
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<a  href="admin/drivers/display_drivers_list" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_drivers_back_to_driver_list') != '') echo stripslashes($this->lang->line('admin_drivers_back_to_driver_list')); else echo 'Back To Drivers List'; ?></span></a>
											</div>
										</div>
									</li>
								</ul>
							</div>
							
							<div id="tab4">
								<ul>
									<li>
											<h3><?php if ($this->lang->line('admin_drivers_bank_information') != '') echo stripslashes($this->lang->line('admin_drivers_bank_information')); else echo 'Banking Informations'; ?></h3>
									</li>
									<?php 
										if(isset($driver_details->banking)){
											$banking = $driver_details->banking;
										} else {
											$banking = array();
										}
										
										if(!empty($banking)){
									?>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="acc_holder_name"><?php if ($this->lang->line('admin_drivers_account_holder_name') != '') echo stripslashes($this->lang->line('admin_drivers_account_holder_name')); else echo 'Account holder name'; ?> </label>
											<div class="form_input">
												<p><?php  if(isset($banking['acc_holder_name'])) echo $banking['acc_holder_name'];  ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="acc_holder_address"><?php if ($this->lang->line('admin_drivers_account_holder_address') != '') echo stripslashes($this->lang->line('admin_drivers_account_holder_address')); else echo 'Account holder address'; ?> </label>
											<div class="form_input">
												<p><?php if(isset($banking['acc_holder_address'])) echo $banking['acc_holder_address']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="acc_number"><?php if ($this->lang->line('admin_drivers_account_number') != '') echo stripslashes($this->lang->line('admin_drivers_account_number')); else echo 'Account number'; ?> </label>
											<div class="form_input">
												<p><?php if(isset($banking['acc_number'])) echo $banking['acc_number']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="bank_name"><?php if ($this->lang->line('admin_drivers_bank_name') != '') echo stripslashes($this->lang->line('admin_drivers_bank_name')); else echo 'Bank Name'; ?> </label>
											<div class="form_input">
												<p><?php if(isset($banking['bank_name'])) echo $banking['bank_name']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="branch_name"><?php if ($this->lang->line('admin_drivers_branch_name') != '') echo stripslashes($this->lang->line('admin_drivers_branch_name')); else echo 'Branch Name'; ?> </label>
											<div class="form_input">
												<p><?php  if(isset($banking['branch_name'])) echo $banking['branch_name']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="branch_address"><?php if ($this->lang->line('admin_drivers_branch_address') != '') echo stripslashes($this->lang->line('admin_drivers_branch_address')); else echo 'Branch address'; ?>  </label>
											<div class="form_input">
												<p><?php  if(isset($banking['branch_address'])) echo $banking['branch_address']; ?></p>
											</div>
										</div>
									</li>		
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="swift_code"><?php if ($this->lang->line('admin_drivers_swift_code') != '') echo stripslashes($this->lang->line('admin_drivers_swift_code')); else echo 'Swift Code'; ?> </label>
											<div class="form_input">
												<p>
												
												<?php 
												if(isset($banking['swift_code'])) { 
													if($banking['swift_code'] != ''){ 
														echo $banking['swift_code']; 
													} else {
														echo 'Not Available';
													}
												} ?>
												
												</p>
											</div>
										</div>
									</li>		
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="routing_number"><?php if ($this->lang->line('admin_drivers_routing_number') != '') echo stripslashes($this->lang->line('admin_drivers_routing_number')); else echo 'Routing Number'; ?></label>
											<div class="form_input">
												<p>
												<?php 
												if(isset($banking['routing_number'])) { 
													if($banking['routing_number'] != ''){ 
														echo $banking['routing_number']; 
													} else {
														echo 'Not Available';
													}
												} ?>
											</div>
										</div>
									</li>								
									
									<?php 
									} else {
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="vehicle_number"></label>
											<div class="form_input">
												<p><?php if ($this->lang->line('admin_drivers_no_record_found') != '') echo stripslashes($this->lang->line('admin_drivers_no_record_found')); else echo 'No records found'; ?></p>
												<a href="admin/drivers/banking/<?php echo $driver_details->_id; ?>" class="expiry_box"><?php if ($this->lang->line('admin_drivers_connect_banking') != '') echo stripslashes($this->lang->line('admin_drivers_connect_banking')); else echo 'Connect Banking'; ?></a>
											</div>
										</div>
									</li>
									<?php 
									}
									?>
									
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

<script>

function verify_docx(docxNo,driverNo,docxType,docx_state_org){
	var docx_state = $('#docx_vrf_'+docxNo).html();
	$('#docx_vrf_'+docxNo).html('<img src="images/indicator.gif">');
	$.get('admin/drivers/document_verify_status_ajax?docxId='+docxNo+'&driverId='+driverNo+'&docx_state='+docx_state+'&docxType='+docxType, function(res){
		if(res == 'Success'){
			if(docx_state == 'Verify'){
				$('#docx_vrf_'+docxNo).html('Verified');
				$('#docx_vrf_'+docxNo).css('background','green');
			} else {
				$('#docx_vrf_'+docxNo).html('Verify');
				$('#docx_vrf_'+docxNo).css('background','red');
			}
		} else{
			$('#docx_vrf_'+docxNo).html('Retry');
		}
	});
}

</script>


<style>

.expiry_box {
    background: none repeat scroll 0 0 gainsboro;
    border: 1px solid grey;
    border-radius: 5px;
    margin-top: 2%;
    padding: 1%;
    text-align: center;
    width: 50% !important;
}

.expiry_box_status {
    background: gainsboro none repeat scroll 0 0;
    border: 1px solid grey;
    border-radius: 5px;
    color: #fff;
    float: right;
    font-weight: bold;
     margin: -32px 0 0;
    padding: 5px 13px;
    text-align: center;
    width: 39px !important;
}
.expiry_box a{
	color: green;
}
.expiry_box p{
	margin-bottom: 0;
    margin-top: 12px;
}
</style>

<?php 
$this->load->view('admin/templates/footer.php');
?>
