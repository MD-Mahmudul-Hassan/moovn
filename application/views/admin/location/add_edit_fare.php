<?php
$this->load->view('admin/templates/header.php');
if(isset($locationdetails->row()->currency)){
	if($locationdetails->row()->currency != ''){
		$dcurrencyCode = $locationdetails->row()->currency;
		$dcurrencySymbol = $locationdetails->row()->currency_symbol;
	}
}
$d_distance_unit = $d_distance_unit;
$d_distance_unit_name = $d_distance_unit_name;
if(isset($locationdetails->row()->distance_unit)){
	if($locationdetails->row()->distance_unit != ''){
		$d_distance_unit = $locationdetails->row()->distance_unit;
		$d_distance_unit_name = $locationdetails->row()->distance_unit_name;
	}
}
?>

<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
						<div id="widget_tab">
              				<ul>
								<?php if($categorydetails->num_rows()>0){ ?>
								<?php $t=0; foreach($categorydetails->result() as $row){ $t++; ?>
								<?php if(in_array($row->_id,$availableCategory)){ ?>
               					 <li class="tnlnk">
									<a id="lnk_<?php echo $row->_id; ?>" href="#<?php echo $row->_id; ?>" <?php if($t==1){ ?>class="active_tab"<?php } ?> onclick="chkValid('<?php echo $row->_id; ?>')" <?php if($t==count($availableCategory)){ ?>data-tabview="climax"<?php }else{ ?>data-tabview="running"<?php } ?> data-tabid="<?php echo $row->_id; ?>">
										<?php echo $row->name; ?>
									</a>
								 </li>
								 <?php } ?>
								 <?php } ?>
								 <?php } ?>
             				 </ul>
            			</div>
					</div>
					<div class="widget_content">
					<?php if(!empty($availableCategory)){ ?>
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditfare_form');
						echo form_open('admin/location/insertEditFare',$attributes) 
					?>
						<?php if($categorydetails->num_rows()>0){ ?>
						<?php $t=0; foreach($categorydetails->result() as $row){  ?>
						<?php if(in_array($row->_id,$availableCategory)){ $t++; ?>
						<?php $category_id=(string)$row->_id; ?>
						<div id="<?php echo $row->_id; ?>">
						<button type="submit" style="display:none;" id="btn_<?php echo $row->_id; ?>"></button>
	 						<ul>
								<li>
									<h4><?php if ($this->lang->line('admin_location_and_fare_standard_rate') != '') echo stripslashes($this->lang->line('admin_location_and_fare_standard_rate')); else echo 'Standard Rate'; ?> - <?php echo $row->name; ?></h4>
								</li>
								
								<li>
									<h4><?php if ($this->lang->line('admin_location_and_fare_minimum_bill') != '') echo stripslashes($this->lang->line('admin_location_and_fare_minimum_bill')); else echo 'Minimum Bill'; ?></h4>
								</li>
								
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="min_km"><?php if ($this->lang->line('admin_location_and_fare_min') != '') echo stripslashes($this->lang->line('admin_location_and_fare_min')); else echo 'Min'; ?> <?php echo get_language_value_for_keyword($d_distance_unit_name,$this->data['langCode']); ?> <span class="req">*</span></label>
										<div class="form_input">
											<input name="<?php echo $row->_id; ?>[min_km]" id="min_km<?php echo $row->_id; ?>" type="text" tabindex="1" class="large required number positiveNumber minfloatingNumber tipTop fareip" title="<?php if ($this->lang->line('admin_location_enter_minimum') != '') echo stripslashes($this->lang->line('admin_location_enter_minimum')); else echo 'Please enter the Minimum'; ?> 
                                            <?php echo get_language_value_for_keyword($d_distance_unit_name,$this->data['langCode']); ?>" data-iptype="min_km" value="<?php if($form_mode) if(isset($locationdetails->row()->fare[$category_id])){ echo $locationdetails->row()->fare[$category_id]['min_km']; } ?>" autocomplete="off" />
										</div>
									</div>
								</li>
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="min_time"><?php if ($this->lang->line('admin_location_and_fare_min_time') != '') echo stripslashes($this->lang->line('admin_location_and_fare_min_time')); else echo 'Min Time (in Minute)'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<input name="<?php echo $row->_id; ?>[min_time]" id="min_time<?php echo $row->_id; ?>" type="text" tabindex="1" class="large required number positiveNumber tipTop fareip minfloatingNumber" title="<?php if ($this->lang->line('admin_location_enter_minimum') != '') echo stripslashes($this->lang->line('admin_location_enter_minimum')); else echo 'Please enter the Minimum Time'; ?>" data-iptype="min_time" value="<?php if($form_mode) if(isset($locationdetails->row()->fare[$category_id])){ echo $locationdetails->row()->fare[$category_id]['min_time']; } ?>" autocomplete="off" /> <?php if ($this->lang->line('admin_location_and_fare_min') != '') echo stripslashes($this->lang->line('admin_location_and_fare_min')); else echo 'Min'; ?>
										</div>
									</div>
								</li>
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="min_fare"><?php if ($this->lang->line('admin_location_and_fare_min_fare') != '') echo stripslashes($this->lang->line('admin_location_and_fare_min_fare')); else echo 'Min Fare'; ?><span class="req">*</span></label>
										<div class="form_input">
											<?php echo $dcurrencySymbol.' '; ?>
											<input name="<?php echo $row->_id; ?>[min_fare]" id="min_fare<?php echo $row->_id; ?>" type="text" tabindex="1" class="large required number positiveNumber tipTop fareip minfloatingNumber" title="<?php if ($this->lang->line('admin_location_enter_minimum_fare') != '') echo stripslashes($this->lang->line('admin_location_enter_minimum_fare')); else echo 'Please enter the Minimum Fare'; ?>" data-iptype="min_fare" value="<?php if($form_mode) if(isset($locationdetails->row()->fare[$category_id])){ echo $locationdetails->row()->fare[$category_id]['min_fare']; } ?>" autocomplete="off" />
											<?php echo $dcurrencyCode.' '; ?>
										</div>
									</div>
								</li>
								
								
								<li>
									<h4><?php if ($this->lang->line('admin_location_and_fare_after_minimum_bill') != '') echo stripslashes($this->lang->line('admin_location_and_fare_after_minimum_bill')); else echo 'After Minimum Bill'; ?></h4>
								</li>
								
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="per_km"><?php if ($this->lang->line('admin_location_and_fare_fare_per') != '') echo stripslashes($this->lang->line('admin_location_and_fare_fare_per')); else echo 'Fare Per'; ?>  <?php echo get_language_value_for_keyword($d_distance_unit_name,$this->data['langCode']); ?> <span class="req">*</span></label>
										<div class="form_input">
											<?php echo $dcurrencySymbol.' '; ?>
											<input name="<?php echo $row->_id; ?>[per_km]" id="per_km<?php echo $row->_id; ?>" type="text" tabindex="1" class="large required number positiveNumber tipTop fareip minfloatingNumber" title="<?php if ($this->lang->line('admin_location_enter_fare_fare') != '') echo stripslashes($this->lang->line('admin_location_enter_fare_fare')); else echo 'Please enter the Fare Per'; ?>  <?php echo get_language_value_for_keyword($d_distance_unit_name,$this->data['langCode']); ?> " data-iptype="per_km" value="<?php if($form_mode) if(isset($locationdetails->row()->fare[$category_id])){ echo $locationdetails->row()->fare[$category_id]['per_km']; } ?>" autocomplete="off" />
											<?php echo $dcurrencyCode.' '; ?>
										</div>
									</div>
								</li>								
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="per_minute"><?php if ($this->lang->line('admin_location_and_fare_fare_per_minitue') != '') echo stripslashes($this->lang->line('admin_location_and_fare_fare_per_minitue')); else echo 'Fare Per Minute (Ride time charges)'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<?php echo $dcurrencySymbol.' '; ?>
											<input name="<?php echo $row->_id; ?>[per_minute]" id="per_minute<?php echo $row->_id; ?>" type="text" tabindex="1" class="large required number positiveNumber tipTop fareip minfloatingNumber" title="<?php if ($this->lang->line('admin_location_enter_fare_fare_minute') != '') echo stripslashes($this->lang->line('admin_location_enter_fare_fare_minute')); else echo 'Please enter the Fare Per minutes'; ?>" data-iptype="per_minute" value="<?php if($form_mode) if(isset($locationdetails->row()->fare[$category_id])){ echo $locationdetails->row()->fare[$category_id]['per_minute']; } ?>" autocomplete="off" />
											<?php echo $dcurrencyCode.' '; ?>
										</div>
									</div>
								</li>
								
								<li>
									<h4><?php if ($this->lang->line('admin_location_and_fare_waiting_charges') != '') echo stripslashes($this->lang->line('admin_location_and_fare_waiting_charges')); else echo 'Waiting Charges'; ?> </h4>
								</li>
								
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="wait_per_minute"><?php if ($this->lang->line('admin_location_and_fare_waiting_fare_per_minute') != '') echo stripslashes($this->lang->line('admin_location_and_fare_waiting_fare_per_minute')); else echo 'Waiting Fare Per Minute'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<?php echo $dcurrencySymbol.' '; ?>
											<input name="<?php echo $row->_id; ?>[wait_per_minute]" id="wait_per_minute<?php echo $row->_id; ?>" type="text" tabindex="1" class="large required number positiveNumber minfloatingNumber tipTop fareip" title="<?php if ($this->lang->line('admin_location_enter_fare_fare_minute') != '') echo stripslashes($this->lang->line('admin_location_enter_fare_fare_minute')); else echo 'Please enter the Fare Per Minute'; ?>" data-iptype="wait_per_minute" value="<?php if($form_mode) if(isset($locationdetails->row()->fare[$category_id])){ echo $locationdetails->row()->fare[$category_id]['wait_per_minute']; } ?>" autocomplete="off" />
											<?php echo $dcurrencyCode.' '; ?>
										</div>
									</div>
								</li>
								
								<li>
									<h4><?php if ($this->lang->line('admin_location_and_fare_extra_charges') != '') echo stripslashes($this->lang->line('admin_location_and_fare_extra_charges')); else echo 'Extra charges'; ?></h4>
								</li>
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="peak_time_charge"><?php if ($this->lang->line('admin_location_and_fare_peak_time_surcharge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_peak_time_surcharge')); else echo 'Peak Time Surcharge'; ?> <?php if($form_mode){if(isset($locationdetails->row()->peak_time)){ if($locationdetails->row()->peak_time=='Yes'){ ?><span class="req">*</span><?php }}} ?></label>
										<div class="form_input">
											<input name="<?php echo $row->_id; ?>[peak_time_charge]" id="peak_time_charge<?php echo $row->_id; ?>" type="text" tabindex="1" class="large <?php if($form_mode){if(isset($locationdetails->row()->peak_time)){ if($locationdetails->row()->peak_time=='Yes'){ ?>required<?php }}} ?> number positiveNumber tipTop fareip minfloatingNumber" title="<?php if ($this->lang->line('admin_location_enter_peak_time_surcharge') != '') echo stripslashes($this->lang->line('admin_location_enter_peak_time_surcharge')); else echo 'Please enter the Peak Time Surcharge'; ?>" data-iptype="peak_time_charge" value="<?php if($form_mode) if(isset($locationdetails->row()->fare[$category_id])){ echo $locationdetails->row()->fare[$category_id]['peak_time_charge']; } ?>" autocomplete="off" /> (X)
											
										</div>
									</div>
								</li>
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="night_charge"><?php if ($this->lang->line('admin_location_and_fare_night_charge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_night_charge')); else echo 'Night charges'; ?> <?php if($form_mode){if(isset($locationdetails->row()->night_charge)){ if($locationdetails->row()->night_charge=='Yes'){ ?><span class="req">*</span><?php }}} ?></label>
										<div class="form_input">
											<input name="<?php echo $row->_id; ?>[night_charge]" id="night_charge<?php echo $row->_id; ?>" type="text" tabindex="1" class="large <?php if($form_mode){if(isset($locationdetails->row()->night_charge)){ if($locationdetails->row()->night_charge=='Yes'){ ?>required<?php }}} ?> number positiveNumber minfloatingNumber tipTop fareip" title="<?php if ($this->lang->line('admin_location_enter_night_surcharge') != '') echo stripslashes($this->lang->line('admin_location_enter_night_surcharge')); else echo 'Please enter the Night Surcharge'; ?>"  data-iptype="night_charge" value="<?php if($form_mode) if(isset($locationdetails->row()->fare[$category_id])){ echo $locationdetails->row()->fare[$category_id]['night_charge']; } ?>" autocomplete="off" /> (X)
										</div>
									</div>
								</li>
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="apply_to_all"><?php if ($this->lang->line('admin_location_and_fare_apply_to_all_category') != '') echo stripslashes($this->lang->line('admin_location_and_fare_apply_to_all_category')); else echo 'Apply to all category'; ?></label>
										<div class="form_input">
											<input type="checkbox" class="apply_to_all" name="apply[]" value="<?php echo $row->_id; ?>" />
										</div>
									</div>
								</li>
								<?php if($t==count($availableCategory)){ ?>
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<button type="submit" class="btn_small btn_blue" tabindex="4" onclick="chkValidBtn();">
												<span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span>
											</button>
										</div>
									</div>
								</li>
								<?php } ?>
							</ul>
                        </div>
						<?php } ?>
						<?php } ?>
						<?php } ?>
	 					<input type="hidden" name="location_id" value="<?php if($form_mode){ echo $locationdetails->row()->_id; } ?>"/>	
						</form>
						<?php }else{ ?>
							<ul>
								<li>
									<center>
										<h4 style="color: #a7a9ac;"><?php if ($this->lang->line('admin_location_and_fare_apply_no_category_availbale') != '') echo stripslashes($this->lang->line('admin_location_and_fare_apply_no_category_availbale')); else echo 'no category availbale in this location'; ?></h4><br/>
										<a href="<?php echo base_url().'admin/location/add_edit_location/'.$locationId; ?>" class="btn custom" tabindex="4">	<?php if ($this->lang->line('admin_location_and_fare_back_to_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_back_to_location')); else echo 'back to location'; ?>
										</a>
									</center><br/>
								</li>
							</ul>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
</div>
<script type="application/javascript">
$('input[type="checkbox"]').on('change', function() {
    $('input[name="' + this.name + '"]').not(this).prop('checked', false);
});
$('.apply_to_all').on('change', function() {
    if($(this).prop('checked')){
		var parentId=$(this).val();
		$("#"+parentId+" .fareip").each( function () {
			var itype=$(this).attr('data-iptype');
			$("input[data-iptype="+itype+"]").val($(this).val()); 
		});
	}
});
var ch=1;
function chkValidBtn(){
	$('#addeditfare_form').attr('onsubmit', 'return true');
}
function chkValid(idvalnew){
	$('#tabValidator').val('Yes'); 
	if(ch>1){
		idval=$(".active_tab").attr("data-tabid");
		var tabview=$("#lnk_"+idval).attr('data-tabview')
		if(idvalnew!=idval){
			$('#addeditfare_form').attr('onsubmit', 'return false');
			$("#btn_"+idval).trigger('click');
			var el=0;
			$("#"+idval+" .error").each( function () {
				if($(this).css('display') != 'none')el++;
			});				
			$(".tnlnk a").each( function () {
				$(this).removeClass('active_tab');
			});
			if(el==0){
				$("#lnk_"+idvalnew).trigger('click');
				if(tabview=='running'){
					$('#addeditfare_form').attr('onsubmit', 'return false');		
				}else{
					$('#addeditfare_form').attr('onsubmit', 'return true');
				}
			}else{
				$("#lnk_"+idval).trigger('click');	
				$('#tabValidator').val('No'); 
			}
		}
	}
	ch++;
}
</script>
<?php 
$this->load->view('admin/templates/footer.php');
?>