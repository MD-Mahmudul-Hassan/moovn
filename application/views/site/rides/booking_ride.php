<?php    
	$this->load->view('site/templates/profile_header'); 
?> 
<link rel="stylesheet" href="datepicker/css/bootstrap-datetimepicker.min.css">
<script src="datepicker/js/bootstrap-datetimepicker.min.js"></script>


<div class="container-new">
<section>
<div class="col-md-12 profile_rider">
<?php 
	$this->load->view('site/templates/profile_sidebar'); 
?>

<div class="col-md-9 nopadding profile_rider_right booking_ride_new_form">
	  <ul class="destination_page" >
	  	
		<li class="active col-lg-12 col-sm-12 col-xs-12 rider-pickup-detail">
         <h2> <?php if ($this->lang->line('rider_book_ride') != '') echo stripslashes($this->lang->line('rider_book_ride')); else echo 'Book Ride'; ?></h2>
			<div class="way_form_new">
			  
			<form class="pickup_address_form" id="booking_form" action="site/rider/temp_store" method="post">
			
            <input type="text" id="autocomplete" name="pickup_location" onFocus="geolocate()" placeholder="<?php if ($this->lang->line('book_pickup_location') != '') echo stripslashes($this->lang->line('book_pickup_location')); else echo 'Pickup Location'; ?>" class="col-lg-12 col-sm-12 bexti_pickup_address" />
				
			<span id="autocompleteErr" class="error"></span>
            
            <input type="text" id="drop_location" name="drop_location" value="" onFocus="geolocate()" placeholder="<?php if ($this->lang->line('rider_drop_address') != '') echo stripslashes($this->lang->line('rider_drop_address')); else echo 'Drop Location'; ?>" class="col-lg-12 col-sm-12 bexti_pickup_address"/>
			<br/>
			<span id="autocompleteErr1" class="error"></span>
			
            <div>
		      <label class="radio-inline">
              <input type="radio" name="type" id="type" value="0" onclick="servicetype('0')"> <?php if ($this->lang->line('rider_drop_address') != '') echo stripslashes($this->lang->line('rider_now')); else echo 'Now'; ?>
            </label>
            <label class="radio-inline">
              <input type="radio" name="type" id="type" value="1" onclick="servicetype('1')"> <?php if ($this->lang->line('rider_later') != '') echo stripslashes($this->lang->line('rider_later')); else echo 'Schedule'; ?>
            </label>
            
            </div>
            <br/>
            <div class='input-group date col-lg-12 col-sm-12' id='datetimepicker' style="display:none">
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
					<input type="text" placeholder="<?php if ($this->lang->line('rider_pickup_date_time') != '') echo stripslashes($this->lang->line('rider_pickup_date_time')); else echo 'Pick up Date & Time'; ?>" name="pickup_date_time" id="pickup_date_time" class="col-lg-12 col-sm-12 bexti_pickup_address_desti"/>
					<span id="pickup_date_timeErr" class="error"></span>
				</div>
            
				<select name="category" id="category" class="col-lg-12 col-sm-12 bexti_pickup_address_select" onchange="calculate_fee();">
					<option value=""><?php if ($this->lang->line('rider_category_type') != '') echo stripslashes($this->lang->line('rider_category_type')); else echo 'Category Type'; ?></option>
					<?php 
						foreach($vehicleTypes->result() as $vehicle){
					?>
					<option value="<?php echo (string)$vehicle->_id; ?>"><?php echo $vehicle->name; ?></option>
					<?php 
					}
					?>
			</select>
            
            <br/>
				<span id="categoryErr" class="error"></span>
				
				
				<input type="hidden" name="pickup_lon" value="0" id="pickup_lon"/>
				<input type="hidden" name="pickup_lat" value="0" id="pickup_lat"/>
				<input type="hidden" name="ride_type"  id="ride_type"/>
                <input type="hidden" name="drop_lon" value="0" id="drop_lon"/>
				<input type="hidden" name="drop_lat" value="0" id="drop_lat"/>
				
				<br/>
            <div class="col-md-12 no_padding_no">
				    <div class="col-md-6 no_padding_no">
				        <input type="text" id="coupon_code" name="code" placeholder="<?php if ($this->lang->line('rider_coupon_code') != '') echo stripslashes($this->lang->line('rider_coupon_code')); else echo 'Enter Promo code here'; ?>" class="col-lg-12 col-sm-12 otlcab_coupon_code"/>
						<span id="coupon_codeErr" class="text-center coupon_co_de"></span>
				    </div>
					<div class="col-md-6">
						<button id="coupon_code_but" type="button" class="coupon_code_but" onclick="validate_coupon_code()"><?php if ($this->lang->line('Apply_coupon_code') != '') echo stripslashes($this->lang->line('Apply_coupon_code')); else echo 'Apply code'; ?></button>
						
					</div>
				</div>
				<input type="button" value="<?php if ($this->lang->line('rider_complete_booking') != '') echo stripslashes($this->lang->line('rider_complete_booking')); else echo 'Complete Booking'; ?>" onclick="validate_booking_form();" class="destination_search_btn col-lg-4 col-sm-12 col-xs-12"/>
			 </form>
			</div>
        </li>	

		
		
	
    
    </ul>
   			
		
</div>
	
	
</div>
</section>
</div>

<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places<?php echo $google_maps_api_key; ?>"></script>
<script>
	
	function initialize() {
        var input = document.getElementById('autocomplete');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            //document.getElementById('city2').value = place.name;
			$('#pickup_lat').val(place.geometry.location.lat());
			$('#pickup_lon').val(place.geometry.location.lng());
            ajax_catgeory();
            //alert(place.geometry.location.lat());
           // alert(place.address_components[0].long_name);

        });

		var input = document.getElementById('drop_location');
        var drop_location = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(drop_location, 'place_changed', function () {
            var place = drop_location.getPlace();
            //document.getElementById('city2').value = place.name;
			$('#drop_lat').val(place.geometry.location.lat());
			$('#drop_lon').val(place.geometry.location.lng());
            //alert(place.geometry.location.lat());
           // alert(place.address_components[0].long_name);

        });
    }
    google.maps.event.addDomListener(window, 'load', initialize); 
	
</script>



<script type="text/javascript">
	$(function () {
		var nowDate = new Date();
		var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
		$('#datetimepicker').datetimepicker({autoclose: true,startDate: today});
	});
</script>



<script>
function servicetype(type) {
   
   if(type == '1') {
   $('#ride_type').val(type);
    $('#datetimepicker').css('display','block');
   } else {
   $('#ride_type').val(type);
    $('#datetimepicker').css('display','none');
   }

}
function validate_booking_form(){
	var loc = $('#autocomplete').val();
	var drop_location = $('#drop_location').val();
	var category = $('#category').val();
	var pickup_date_time = $('#pickup_date_time').val();
	var type = $('#type').val();
	var country_code = $('#country_code').val();
	var mobile_no = $('#mobile_no').val();
	
	var vl = 0;
	if(loc == ''){
		vl++;
		
		$('#autocompleteErr').html('<?php if ($this->lang->line('rider_please_enter_pickup_location') != '') echo stripslashes($this->lang->line('rider_please_enter_pickup_location')); else echo 'Please enter your pickup location'; ?>');
	} else {
		$('#autocompleteErr').html('');
	}
	if(drop_location == ''){
		vl++;
		
		$('#autocompleteErr1').html('<?php if ($this->lang->line('rider_please_enter_drop_location') != '') echo stripslashes($this->lang->line('rider_please_enter_drop_location')); else echo 'Please enter your drop location'; ?>');
	} else {
		$('#autocompleteErr1').html('');
	}
   
   
	if(category == ''){ 
		vl++;
		
		$('#categoryErr').html('<?php if ($this->lang->line('ride_Cab_type') != '') echo stripslashes($this->lang->line('ride_Cab_type')); else echo 'Please choose cab type'; ?>');
        
	} else {
		$('#categoryErr').html('');
	}
	
	if(type == 1){
		if(pickup_date_time == ''){
			vl++;
			$('#pickup_date_timeErr').html('<?php if ($this->lang->line('rider_please_enter_pickup_datetime') != '') echo stripslashes($this->lang->line('rider_please_enter_pickup_datetime')); else echo 'Please choose your pickup date & time'; ?>');
		} else {
			$('#pickup_date_timeErr').html('');
		}
	}
	if(vl == 0){
		$('#booking_form').submit();
	}
}
function validate_coupon_code(){
	var code = $('#coupon_code').val();
	var pickup_date = $('#pickup_date_time').val();
	var type = $('#type').val();
	var pickup_lon = $('#pickup_lon').val();
	var pickup_lat = $('#pickup_lat').val();	
        if(code == ''){
		
		$('#coupon_codeErr').html('<?php if ($this->lang->line('rider_please_enter_coupen') != '') echo stripslashes($this->lang->line('rider_please_enter_coupen')); else echo 'Please enter your coupon code'; ?>');
		return false;
	    }	
		$.ajax({
		    url: 'site/user/ajax_coupon_validation',
		    data: {"code":code,"pickup_date":pickup_date,"pickup_lon":pickup_lon,"pickup_lat":pickup_lat},
            type: 'POST',
            dataType: 'json',
		    success: function(data) {         
                if(data.status == '1'){
					                    
					$('#coupon_codeErr').html('<span class="sucess_coupon_code">'+"<?php if ($this->lang->line('rider_coupen_success') != '') echo stripslashes($this->lang->line('rider_coupen_success')); else echo 'coupon code success,You will get '; ?>"+data.promo_value+" "+data.code_type+"<?php if ($this->lang->line('rider_coupen_offer') != '') echo stripslashes($this->lang->line('rider_coupen_offer')); else echo ' offer from this code'; ?>"+'</>');
                    
				} else if(data.status == '0'){
					$('#coupon_codeErr').html('<span class="failure_coupon_code">Invalid Coupon code</>');
					$('#coupon_codeErr').html('<span class="failure_coupon_code"><?php if ($this->lang->line('invalid_coupen') != '') echo stripslashes($this->lang->line('invalid_coupen')); else echo 'Invalid Coupon code'; ?></>');
					$('#coupon_code').val('');
				 }         
            }
		});
}
function ajax_catgeory(){
	var pickup = $('#autocomplete').val();
	var pickup_lon = $('#pickup_lon').val();
	var pickup_lat = $('#pickup_lat').val();
	if(pickup != '' && pickup_lon!='' && pickup_lat!=''){
                $.ajax({
                    url: 'site/user/ajax_catgeory',
                    data: {"pickup":pickup,"pickup_lon":pickup_lon,"pickup_lat":pickup_lat},
                    type: 'POST',
                    success: function(data) {         
                        $('#category').html(data);    
                    }
            });
		
	}	
		
}
  
</script>
     
<?php  
	$this->load->view('site/templates/footer'); 
?> 


