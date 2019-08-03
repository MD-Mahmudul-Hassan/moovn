<?php
$this->load->view('admin/templates/header.php');
?>
<!-- Script for timepicker -->	
<script type="text/javascript" src="js/timepicker/jquery.timepicker.js"></script>
<script type="text/javascript" src="js/timepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/timepicker/site.js"></script>
<script type="text/javascript" src="js/timepicker/jquery.timepicker.min.js"></script>
<!-- Script for timepicker -->	

<!-- css for timepicker -->	
<link rel="stylesheet" type="text/css" href="css/timepicker/bootstrap-datepicker.css" />
<link rel="stylesheet" type="text/css" href="css/timepicker/site.css" />
<link rel="stylesheet" type="text/css" href="css/timepicker/jquery.timepicker.css" />

<script>
// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

var placeSearch, autocomplete;
var componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initAutocomplete() {
  // Create the autocomplete object, restricting the search to geographical
  // location types.
  autocomplete = new google.maps.places.Autocomplete(
      /** @type {!HTMLInputElement} */(document.getElementById('city')),
      {types: ['geocode']});

  // When the user selects an address from the dropdown, populate the address
  // fields in the form.
  autocomplete.addListener('place_changed');
  //autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
  // Get the place details from the autocomplete object.
  var place = autocomplete.getPlace();

  for (var component in componentForm) {
    document.getElementById(component).value = '';
    document.getElementById(component).disabled = false;
  }

  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      document.getElementById(addressType).value = val;
    }
  }
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      autocomplete.setBounds(circle.getBounds());
    });
  }
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->config->item('google_maps_api_key');?>&signed_in=true&libraries=places&callback=initAutocomplete"
async defer></script>


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
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditlocation_form');
						echo form_open('admin/location/insertCopyLocation',$attributes) 
					?> 		
	 						<ul>
                                <?php /* <li>
									<div class="form_grid_12">
										<label class="field_title" for="location_name">Country Name <span class="req">*</span></label>
										<div class="form_input">	
											<input name="countryDisp" id="countryDisp" disabled="disabled" type="text" tabindex="2" class="large required tipTop" value="<?php echo $this->config->item('countryName'); ?>"/>
											<input name="country" id="country" type="hidden" value="<?php echo $this->config->item('countryId'); ?>"/>
										</div>
									</div>
								</li> */ ?>
								
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="city"><?php if ($this->lang->line('admin_location_and_fare_city') != '') echo stripslashes($this->lang->line('admin_location_and_fare_city')); else echo 'City'; ?> <span class="req">*</span></label>
										<div class="form_input">
										<input name="city" id="city" type="text" tabindex="2" class="large required tipTop" title="<?php if ($this->lang->line('admin_location_enter_city') != '') echo stripslashes($this->lang->line('admin_location_enter_city')); else echo 'Please enter the city'; ?>" value="" onFocus="geolocate()" placeholder="<?php if ($this->lang->line('admin_enter_location') != '') echo stripslashes($this->lang->line('admin_enter_location')); else echo 'Enter location'; ?>"/>
										</div>
									</div>
								</li>
								
                                <?php 
								$categoryArr='';
								if($form_mode){
									if(isset($locationdetails->row()->avail_category)){
										$categoryArr=$locationdetails->row()->avail_category;
									}else{
										$categoryArr='';
									}
								}
								if(!is_array($categoryArr))$categoryArr=array();
								?>
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="attribute_name"><?php if ($this->lang->line('admin_location_and_fare_location_available_category') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_available_category')); else echo 'Available Category'; ?></label>
										<div class="form_input">
										<?php if($categoryList->num_rows()>0){ ?>
										<select class="chzn-select required Validname" multiple="multiple" id="category" name="category[]" tabindex="1" data-placeholder="<?php if ($this->lang->line('admin_availabe_category') != '') echo stripslashes($this->lang->line('admin_availabe_category')); else echo 'Choose available category'; ?>">
											<?php foreach($categoryList->result() as $row){ ?>
											<option value="<?php echo $row->_id; ?>" <?php if (in_array($row->_id,$categoryArr)){echo 'selected="selected"';}  ?>><?php echo $row->name; ?></option>
											<?php } ?>
										</select>
										<?php }else{ ?>
											<p class="error"><?php if ($this->lang->line('admin_location_and_fare_check_category_list') != '') echo stripslashes($this->lang->line('admin_location_and_fare_check_category_list')); else echo 'Kindly check category list. There is no category.'; ?></p>
										<?php } ?>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="Distanceunit">Distance Unit<span class="req">*</span></label>
										<div class="form_input">
											<select class="required" style="height: 31px; width: 51%;" id="distance_unit" name="distance_unit" tabindex="1" data-placeholder="Choose distance unit">
											<option value="">Choose distance unit...</option>
												<option value="km-Kilometer" <?php if($form_mode) if(isset($locationdetails->row()->distance_unit)) if($locationdetails->row()->distance_unit == 'km')echo 'selected="selected"';?>>Kilometer</option>
												<option value="mi-Miles" <?php
												if($form_mode) if(isset($locationdetails->row()->distance_unit)) if($locationdetails->row()->distance_unit == 'mi') echo 'selected="selected"';?>>Miles</option>
											</select>
										</div>
									</div>
								</li>
								
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="peak_time"><?php if ($this->lang->line('admin_location_and_fare_peak_time_surcharge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_peak_time_surcharge')); else echo 'Peak Time Surcharge'; ?><span class="req">*</span></label>
										<div class="form_input">
											<div class="peakYes_peakNo">
												<input type="checkbox" tabindex="3" name="peak_time" id="peak_time" class="peakYes_peakNo" <?php if($form_mode){ if(isset($locationdetails->row()->peak_time)){ if ($locationdetails->row()->peak_time == 'Yes'){echo 'checked="checked"'; }}else{echo 'checked="checked"';}}else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
                                <li id="peak_time_frame" style="<?php if($form_mode){ if(isset($locationdetails->row()->peak_time)){ if ($locationdetails->row()->peak_time == 'No'){echo 'display:none'; }}}?>">
									<div class="form_grid_12">
										<label class="field_title" for="peak_time_frame"><?php if ($this->lang->line('admin_location_and_fare_peak_time_surcharge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_peak_time_surcharge')); else echo 'Peak Time Surcharge'; ?><span class="req">*</span></label>
										<div class="form_input">
											<div class="peak_time_frame">
												<?php if ($this->lang->line('admin_location_and_fare_check_category_from') != '') echo stripslashes($this->lang->line('admin_location_and_fare_check_category_from')); else echo 'From'; ?> 
												<input id="peak_time_frame_from" name="peak_time_frame[from]" title="<?php if ($this->lang->line('admin_location_enter_peak_time_from') != '') echo stripslashes($this->lang->line('admin_location_enter_peak_time_from')); else echo 'Enter the Peak time from'; ?>" type="text" class="small required peak_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->peak_time_frame)){ echo $locationdetails->row()->peak_time_frame['from']; } ?>" /> 
												<?php if ($this->lang->line('admin_location_and_fare_check_category_to') != '') echo stripslashes($this->lang->line('admin_location_and_fare_check_category_to')); else echo 'To'; ?>
												<input id="peak_time_frame_to" name="peak_time_frame[to]" title="<?php if ($this->lang->line('admin_location_enter_peak_time_to') != '') echo stripslashes($this->lang->line('admin_location_enter_peak_time_to')); else echo 'Enter the Peak time to'; ?>" type="text" class="small required peak_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->peak_time_frame)){ echo $locationdetails->row()->peak_time_frame['to']; } ?>" />
											</div>
										</div>
									</div>
								</li>
								
								
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="night_charge"><?php if ($this->lang->line('admin_location_and_fare_night_charge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_night_charge')); else echo 'Night charges'; ?><span class="req">*</span></label>
										<div class="form_input">
											<div class="nightYes_nightNo">
												<input type="checkbox" tabindex="4" name="night_charge" id="night_charge" class="nightYes_nightNo" <?php if($form_mode){ if(isset($locationdetails->row()->night_charge)){ if ($locationdetails->row()->night_charge == 'Yes'){echo 'checked="checked"'; }}else{echo 'checked="checked"';}}else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
                                <li id="night_time_frame" style="<?php if($form_mode){ if(isset($locationdetails->row()->night_charge)){ if ($locationdetails->row()->night_charge == 'No'){echo 'display:none'; }}}?>">
									<div class="form_grid_12">
										<label class="field_title" for="night_time_frame"><?php if ($this->lang->line('admin_location_and_fare_night_charges_timing') != '') echo stripslashes($this->lang->line('admin_location_and_fare_night_charges_timing')); else echo 'Night charges timing'; ?><span class="req">*</span></label>
										<div class="form_input">
											<div class="night_time_frame">
												<?php if ($this->lang->line('admin_location_and_fare_check_category_from') != '') echo stripslashes($this->lang->line('admin_location_and_fare_check_category_from')); else echo 'From'; ?>  
												<input id="night_time_frame_from" name="night_time_frame[from]" title="<?php if ($this->lang->line('admin_location_enter_night_charge_timing') != '') echo stripslashes($this->lang->line('admin_location_enter_night_charge_timing')); else echo 'Enter the night charge timing from'; ?>" type="text" class="small required night_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->night_time_frame)){ echo $locationdetails->row()->night_time_frame['from']; } ?>" /> 
												<?php if ($this->lang->line('admin_location_and_fare_check_category_to') != '') echo stripslashes($this->lang->line('admin_location_and_fare_check_category_to')); else echo 'To'; ?>
												<input id="night_time_frame_to" name="night_time_frame[to]" title="<?php if ($this->lang->line('admin_location_enter_night_charge_to') != '') echo stripslashes($this->lang->line('admin_location_enter_night_charge_to')); else echo 'Enter the night charge timing to'; ?>" type="text" class="small required night_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->night_time_frame)){ echo $locationdetails->row()->night_time_frame['to']; } ?>" />
											</div>
										</div>
									</div>
								</li>
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="service_tax"><?php if ($this->lang->line('admin_location_and_fare_service_tax') != '') echo stripslashes($this->lang->line('admin_location_and_fare_service_tax')); else echo 'Service Tax'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<input name="service_tax" id="service_tax" type="text" tabindex="2" class="large required number tipTop" title="<?php if ($this->lang->line('please_enter_service_tax') != '') echo stripslashes($this->lang->line('please_enter_service_tax')); else echo 'Please enter Service tax'; ?>" value="<?php if($form_mode)if(isset($locationdetails->row()->service_tax)){ echo $locationdetails->row()->service_tax; } ?>"/> (%)
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="site_commission"><?php if ($this->lang->line('admin_location_and_fare_commission_to_site') != '') echo stripslashes($this->lang->line('admin_location_and_fare_commission_to_site')); else echo 'Commission to site'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<input name="site_commission" id="site_commission" type="text" tabindex="2" class="large required number tipTop" title="<?php if ($this->lang->line('admin_location_enter_commsion_percent_site') != '') echo stripslashes($this->lang->line('admin_location_enter_commsion_percent_site')); else echo 'Please enter Commission Percent to site'; ?>" value="<?php if($form_mode)if(isset($locationdetails->row()->site_commission)){ echo $locationdetails->row()->site_commission; } ?>"/> (%<?php if ($this->lang->line('admin_location_and_edit_ride_percent') != '') echo stripslashes($this->lang->line('admin_location_and_edit_ride_percent')); else echo 'of ride'; ?>)
										</div>
									</div>
								</li>
								
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="status"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<div class="active_inactive">
												<input type="checkbox" tabindex="5" name="status" id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($locationdetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?>/>
											</div>
										</div>
									</div>
								</li>
								<input type="hidden" name="copy_location_id" value="<?php if($form_mode){ echo $locationdetails->row()->_id; } ?>"/>
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<button type="submit" class="btn_small btn_blue" tabindex="4"><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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
<?php $checkbox_lan=get_language_array_for_keyword($this->data['langCode']);?>
<script>
$(document).ready(function() {
	
	/* Javascript function closure for Getting Selected order of Available Category  at signup*/
	var categoryArr = <?php echo json_encode($categoryArr); ?>;
	$("input[name='available_category']").val(categoryArr);
	var selectedVal=[];
	var thisValue=[];
	var finalArray=[];	
	
	 $("#category").change(function(){  
			thisValue=$(this).val();
			if(thisValue !=null){
				var i;
				for(i=0; i < thisValue.length; i++){
					if($.inArray(thisValue[i],selectedVal) == -1){
						selectedVal.push(thisValue[i]);
						$("input[name='available_category']").val(selectedVal);
					}else if(thisValue.length <= selectedVal.length){
						finalArray=[];	
						for(i=0; i < selectedVal.length; i++){
							if($.inArray(selectedVal[i],thisValue) != -1){
								finalArray.push(selectedVal[i]);
							}
							$("input[name='available_category']").val(finalArray);
						}
						selectedVal=finalArray;
					}
				}
				
			}else{
				$("input[name='available_category']").val("");
				finalArray,thisValue,selectedVal=[];
			}
		
			
	});
	/* Ending of Available Category Closure*/
	
	$('.peakYes_peakNo :checkbox').iphoneStyle({ 
		checkedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', 
		uncheckedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>' ,
		onChange: function(elem, value) {
			if($(elem)[0].checked==false){
				$("#peak_time_frame").hide();
				$('.peak_time_input').removeClass('required');
			}else{
				$("#peak_time_frame").show();
				$('.peak_time_input').addClass('required');
			}
		}
	});
	$('.nightYes_nightNo :checkbox').iphoneStyle({ 
		checkedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', 
		uncheckedLabel: '<?php echo $checkbox_lan['verify_status_no_ucfirst']; ?>',
		onChange: function(elem, value) {
			if($(elem)[0].checked==false){
				$("#night_time_frame").hide();
				$('.night_time_input').removeClass('required');
			}else{
				$("#night_time_frame").show();
				$('.night_time_input').addClass('required');
			}
		}
	});
	$('#peak_time_frame_from').timepicker({ 'timeFormat': 'h:i A' });
	$('#peak_time_frame_to').timepicker({ 'timeFormat': 'h:i A' });
				
	$('#night_time_frame_from').timepicker({ 'timeFormat': 'h:i A' });
	$('#night_time_frame_to').timepicker({ 'timeFormat': 'h:i A' });
	
	$('input.peak_time_input').bind('copy paste cut keypress', function (e) {
       e.preventDefault();
    });
	$('input.night_time_input').bind('copy paste cut keypress', function (e) {
       e.preventDefault();
    });
	
});
$.validator.setDefaults({ ignore: ":hidden:not(select)" });
</script>
<?php 
$this->load->view('admin/templates/footer.php');
?>