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
                    echo form_open('admin/location/insertEditLocation',$attributes) 
                ?>      
                        <ul>
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title" for="noshow-fee-title"><?php if ($this->lang->line('admin_location_and_fare_add_noshow_fee_title') != '') echo stripslashes($this->lang->line('admin_location_and_fare_add_noshow_fee_title')); else echo 'Fee title:'; ?> <span class="req">*</span></label>
                                    <div class="form_input">
                                      <input name="noshow_fee_title" id="noshow-fee-title" type="text" tabindex="2" class="large required tipTop" title="Please enter the no show fee title" value="<?php if($form_mode){ echo $locationdetails->row()->city; } ?>" onFocus="geolocate()" placeholder="Enter title"/>
                                    </div>
                                </div>
                            </li>
                            
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title" for="fee-type"><?php if ($this->lang->line('admin_location_and_fare_add_fee_type') != '') echo stripslashes($this->lang->line('admin_location_and_fare_add_fee_type')); else echo 'Fee type'; ?><span class="req">*</span></label>
                                    <div class="form_input">                                    
                                      <select class="chzn-select required Validname" id="fee-type" name="fee_type" tabindex="1" data-placeholder="<?php if ($this->lang->line('admin_location_and_fare_add_fee_type') != '') echo stripslashes($this->lang->line('admin_location_and_fare_add_fee_type')); else echo 'Type'; ?>">
                                          <option value="no-show">No show fee</option>
                                          <option value="other-fee">Other fee</option>                                        
                                      </select>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title" for="charge-type"><?php if ($this->lang->line('admin_location_and_fare_peak_time_surcharge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_peak_time_surcharge')); else echo 'Peak Time Surcharge'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                        <div class="fee-charge-type">
                                            <input type="checkbox" tabindex="3" name="charge_type" id="charge-type" class="fee-charge-type" <?php if($form_mode){ if(isset($locationdetails->row()->peak_time)){ if ($locationdetails->row()->peak_time == 'Yes'){echo 'checked="checked"'; }}else{echo 'checked="checked"';}}else{echo 'checked="checked"';} ?>/>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li id="charge-type-frame" style="<?php if($form_mode){ if(isset($locationdetails->row()->peak_time)){ if ($locationdetails->row()->peak_time == 'No'){echo 'display:none'; }}}?>">
                                <div class="form_grid_12">
                                    <label class="field_title" for="charge-type-frame"><?php if ($this->lang->line('admin_location_and_fare_peak_time_surcharge') != '') echo stripslashes($this->lang->line('admin_location_and_fare_peak_time_surcharge')); else echo 'Peak Time Surcharge'; ?><span class="req">*</span></label>
                                    <div class="form_input">
                                        <div class="charge-type-frame">
                                            <?php if ($this->lang->line('admin_location_and_edit_from') != '') echo stripslashes($this->lang->line('admin_location_and_edit_from')); else echo 'From'; ?>
                                            <input id="charge-type-frame_from" name="charge-type-frame[from]" title="<?php if ($this->lang->line('location_enter_peak_time_from') != '') echo stripslashes($this->lang->line('location_enter_peak_time_from')); else echo 'Enter the Peak time from'; ?>" type="text" class="small required peak_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->charge-type-frame)){ echo $locationdetails->row()->charge-type-frame['from']; } ?>" />                                               
                                            <?php if ($this->lang->line('admin_location_and_edit_to') != '') echo stripslashes($this->lang->line('admin_location_and_edit_to')); else echo 'TO'; ?>
                                            <input id="charge-type-frame_to" name="charge-type-frame[to]" title="<?php if ($this->lang->line('location_enter_peak_time_to') != '') echo  stripslashes($this->lang->line('location_enter_peak_time_to')); else echo 'Enter the Peak time to'; ?>" type="text" class="small required peak_time_input" value="<?php if($form_mode) if(isset($locationdetails->row()->charge-type-frame)){ echo $locationdetails->row()->charge-type-frame['to']; } ?>" />
                                        </div>
                                    </div>
                                </div>
                            </li>

                            
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title" for="status"><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?> <span class="req">*</span></label>
                                    <div class="form_input">
                                        <div class="active_inactive">
                                            <input type="checkbox" tabindex="5" name="status" id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($locationdetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?>/>
                                        </div>
                                    </div>
                                </div>
                            </li>                            

                            <input type="hidden" name="location_id" value="<?php if($form_mode){ echo $locationdetails->row()->_id; } ?>"/>
                            <li>
                                <div class="form_grid_12">
                                    <div class="form_input">
                                        <input type="hidden" val="" name="available_category">
                                        <button type="submit" class="btn_small btn_blue" tabindex="4"><span><?php if ($this->lang->line('admin_subadmin_submit') != '') echo stripslashes($this->lang->line('admin_subadmin_submit')); else echo 'Submit'; ?></span></button>
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
    
    $('.fee-charge-type :checkbox').iphoneStyle({ 
        checkedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', 
        uncheckedLabel: '<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>' ,
        onChange: function(elem, value) {
            if($(elem)[0].checked==false){
                $("#charge-type-frame").hide();
                $('.peak_time_input').removeClass('required');
            }else{
                $("#charge-type-frame").show();
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

<style>
.chzn-container {
    display: block;
    width: 50% !important;
}
.chzn-container-multi .chzn-choices .search-field {
    width: 100%;
}
.chzn-container-multi .chzn-choices .search-field .default {
    float: left;
    width: 100% !important;
}
</style>
<?php 
$this->load->view('admin/templates/footer.php');
?>