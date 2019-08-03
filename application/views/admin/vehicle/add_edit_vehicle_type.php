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
                        <div id="widget_tab">
            			</div>
					</div>
					<div class="widget_content">
						<form class="form_container left_label" action="admin/vehicle/insertEditVehicle" id="addEditvehicle_form" method="post" enctype="multipart/form-data">
							<div>
								<ul>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="vehicle_type"><?php if ($this->lang->line('admin_vehicle_vehicle_type') != '') echo stripslashes($this->lang->line('admin_vehicle_vehicle_type')); else echo 'Vehicle Type'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<input name="vehicle_type" id="vehicle_type" type="text" tabindex="2" class="required large tipTop" title="<?php if ($this->lang->line('admin_vehicle_enter_vehicle_type') != '') echo stripslashes($this->lang->line('admin_vehicle_enter_vehicle_type')); else echo 'Please enter the Vehicle Type'; ?>" value="<?php if($form_mode){ echo $vehicledetails->row()->vehicle_type; } ?>"/>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="max_seating"><?php if ($this->lang->line('admin_vehicle_maximum_seating_capacity') != '') echo stripslashes($this->lang->line('admin_vehicle_maximum_seating_capacity')); else echo 'Maximum Seating Capacity'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<input name="max_seating" id="max_seating" type="text" tabindex="2" class="required large tipTop number" title="<?php if ($this->lang->line('admin_vehicle_enter_vehicle_type') != '') echo stripslashes($this->lang->line('admin_vehicle_enter_vehicle_type')); else echo 'Please enter the Vehicle Type'; ?>" value="<?php if($form_mode)if(isset($vehicledetails->row()->max_seating)) { echo $vehicledetails->row()->max_seating; } ?>"/>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="icon"><?php if ($this->lang->line('admin_vehicle_vehicle_type_icon') != '') echo stripslashes($this->lang->line('admin_vehicle_vehicle_type_icon')); else echo 'Vehicle Type Icon'; ?> </label>
										<div class="form_input">
											<input name="icon" id="icon" type="file" tabindex="2" class="large tipTop" title="<?php if ($this->lang->line('admin_vehicle_upload_vechicle_icon') != '') echo stripslashes($this->lang->line('admin_vehicle_upload_vechicle_icon')); else echo 'Please upload Vehicle Icon'; ?>"/>
											<img src="images/ajax-loader/ajax-loader.gif" id="loadedImg" style="width:10px;display:none;" />
											<div class="error" id="ErrNotify"><?php if ($this->lang->line('admin_vehicle_minimum_icon_size') != '') echo stripslashes($this->lang->line('admin_vehicle_minimum_icon_size')); else echo 'Note: Minimum Icon size 70 X 40 Pixels'; ?></div>
										</div>
									</div>
								</li>
									
									
								<?php 
								if($form_mode){
									if(isset($vehicledetails->row()->icon)){									
										if(isset($vehicledetails->row()->icon)){
											if($vehicledetails->row()->icon!=""){
												$icon=VEHICLE_TYPE.$vehicledetails->row()->icon;
											}else{
												$icon=VEHICLE_TYPE_DEFAULT;
											}
										}else{
											$icon=VEHICLE_TYPE_DEFAULT;
										}
								?>
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="icon_img">&nbsp; </label>
										<div class="form_input">
											<img src="<?php echo base_url().$icon; ?>" alt="<?php echo $vehicledetails->row()->vehicle_type; ?>" width="100" />

										</div>
									</div>
								</li>
                                
                                <?php 
									} 
								}
								?>									
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="status"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<div class="active_inactive">
													<input type="checkbox" tabindex="7" name="status"  id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($vehicledetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?> />
												</div>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<input type="hidden" name="vehicle_id" id="vehicle_id" value="<?php if($form_mode){ echo $vehicledetails->row()->_id; } ?>"  />
												<button type="submit" class="btn_small btn_blue" tabindex="15"><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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
		<span class="clear"></span>
	</div>

<script>
$(document).ready(function() {
	$("#icon").change(function(e) {
	    e.preventDefault();
        var formData = new FormData($(this).parents('form')[0]);
        $.ajax({
			beforeSend: function(){
				   $("#loadedImg").css("display", "block");
  			},
            url: 'admin/vehicle/ajax_check_icon',
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            success: function (data) {
				$("#loadedImg").css("display", "none");
				if(data=='Success'){
					$('#ErrNotify').html('Success').css("color","green");
					return true;
				} else {
					//document.getElementById("icon").value = '';
					$('#ErrNotify').html('Image size should be more than 70 X 40 .').css("color","red");
					return false;
				}
		   },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
	});
});
</script>
<?php 
$this->load->view('admin/templates/footer.php');
?>