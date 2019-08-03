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

<div class="col-md-9 nopadding profile_rider_right booking_ride_new_form new_booking_sub_btn">

<form action="site/rider/booking_ride" method="post" id="myForm" name="myForm">
	  <div id="dropin-container"></div>
     <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
     <input type="hidden" name="nonce" id="nonce">
	  <input type="submit" value="Submit" />
</form>
  
				
		
</div>
		
	
</div>
</section>
</div>

<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
<script src="https://js.braintreegateway.com/js/braintree-2.22.2.min.js"></script>
<script type="text/javascript">
   	braintree.setup("<?php echo $client_id; ?>", "dropin", {
	   container: "dropin-container",
	   onPaymentMethodReceived: function (nonce) {
	    console.log(JSON.stringify(nonce));
       $('#nonce').val(JSON.stringify(nonce));
       var obj = $.parseJSON(JSON.stringify(nonce));
       $('#nonce').val(obj['nonce']);
       myForm.submit();
	  }
	});
    </script>
<?php  
	$this->load->view('site/templates/footer'); 
?> 


