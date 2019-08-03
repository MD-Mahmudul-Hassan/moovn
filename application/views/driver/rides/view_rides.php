<?php
$this->load->view('driver/templates/header.php');

if(isset($rides_details->row()->fare_breakup['distance_unit'])){
	$d_distance_unit = $rides_details->row()->fare_breakup['distance_unit'];
}
if($d_distance_unit=="km"){
	$d_distance_unit = get_language_value_for_keyword('km',$this->data['langCode']);
}else if($d_distance_unit=="mi"){
	$d_distance_unit = get_language_value_for_keyword('mi',$this->data['langCode']);
}

if(isset($rides_details->row()->currency_symbol)){
	$dcurrencySymbol = $rides_details->row()->currency_symbol;
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
			                <li><a href="#tab1" class="active_tab"><?php 
						if($this->lang->line('dash_details') != '') echo stripslashes($this->lang->line('dash_details')); else  echo 'Details';
						?></a></li> 
							<?php if(isset($rides_details->row()->ratings)){ ?>
							<li><a href="#tab2" class=""><?php 
						if($this->lang->line('dash_ratings') != '') echo stripslashes($this->lang->line('dash_ratings')); else  echo 'Ratings';
						?></a></li>
							<?php } ?>
			              </ul>
			            </div>
						
					</div>
					<div class="widget_content">
					<?php  $rides_details = $rides_details->row();
						$attributes = array('class' => 'form_container left_label');
						echo form_open('driver/rides/display_rides_list',$attributes) 
					?> 		
	 				<?php if(isset($rides_details->currency)) $currency = $rides_details->currency; else  $currency='';?>
						<div id="tab1">
							<ul>		
								 <li>
									<h2><?php 
						if($this->lang->line('dash_ride_details') != '') echo stripslashes($this->lang->line('dash_ride_details')); else  echo 'Ride Details';
						?></h2>
								 </li>
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="name"><?php 
						if($this->lang->line('dash_ride_id') != '') echo stripslashes($this->lang->line('dash_ride_id')); else  echo 'Ride Id';
						?></label>
										<div class="form_input">
										<?php if(isset($rides_details->ride_id)){ echo $rides_details->ride_id; } ?>
										</div>
									</div>
								</li> 
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="name"><?php 
						if($this->lang->line('dash_ride_type') != '') echo stripslashes($this->lang->line('dash_ride_type')); else  echo 'Ride Type';
						?></label>
						
						
						<?php 
						if($this->lang->line('dash_instant_ride') != '') $dash_instant_ride = stripslashes($this->lang->line('dash_instant_ride')); else  $dash_instant_ride = 'Instant Ride';
						?>
						
						<?php 
						if($this->lang->line('dash_later_ride') != '') $dash_later_ride = stripslashes($this->lang->line('dash_later_ride')); else  $dash_later_ride = 'Later Ride';
						?>
						
										<div class="form_input">									
										<?php  
										if($rides_details->type == 'Now'){ echo $dash_instant_ride; } else { echo $dash_later_ride;}
										?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="name"><?php 
						if($this->lang->line('dash_ride_status') != '') echo stripslashes($this->lang->line('dash_ride_status')); else  echo 'Ride Status';
						?></label>
						<?php 
						if($this->lang->line('dash_not_available') != '') 
						$dash_not_available = stripslashes($this->lang->line('dash_not_available')); 
						else  $dash_not_available = 'Not Available';
						?>
										<div class="form_input">									
										<?php 
										 if(isset($rides_details->ride_status))
										 echo $rideStatus = get_language_value_for_keyword($rides_details->ride_status,$this->data['langCode']); 
										 else 
										 echo $rideStatus = get_language_value_for_keyword($dash_not_available,$this->data['langCode']); 
										?>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="name"><?php 
						if($this->lang->line('dash_city') != '') echo stripslashes($this->lang->line('dash_city')); else  echo 'City';
						?></label>
										<div class="form_input">									
										<?php  
										if(isset($rides_details->location['name'])) echo $rides_details->location['name']; else echo $dash_not_available; 
										?>
										</div>
									</div>
								</li>
								
								 <?php 
									if(isset($rides_details->driver['id'])){
										if($rides_details->driver['id'] != ''){
								 ?>
								 <li>
									<h2><?php 
						if($this->lang->line('dash_driver_details') != '') echo stripslashes($this->lang->line('dash_driver_details')); else  echo 'Driver Details';
						?></h2>
								 </li>
													
                                <li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_name') != '') echo stripslashes($this->lang->line('dash_name')); else  echo 'Name';
						?></label>
										<div class="form_input">
										<?php if(isset($rides_details->driver['name']))echo $rides_details->driver['name']; else echo $dash_not_available;  ?>
										</div>
									</div>
								</li> 
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_email') != '') echo stripslashes($this->lang->line('dash_email')); else  echo 'Email';
						?></label>
										<div class="form_input">
										<?php /*if($isDemo){ ?>
										<?php echo $dEmail; ?>
										<?php }  else{ ?>
										<?php }*/ ?>
										<?php if(isset($rides_details->driver['email']))echo $rides_details->driver['email']; else echo $dash_not_available;  ?>
										</div>
									</div>
								</li> 
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_mobile_number') != '') echo stripslashes($this->lang->line('dash_mobile_number')); else  echo 'Mobile Number';
						?></label>
										<div class="form_input">
										<?php if(isset($rides_details->driver['phone']))echo $rides_details->driver['phone']; else echo $dash_not_available;  ?>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_vehicle_model') != '') echo stripslashes($this->lang->line('dash_vehicle_model')); else  echo 'Vehicle Model';
						?></label>
										<div class="form_input">
										<?php if(isset($rides_details->driver['vehicle_model']))echo $rides_details->driver['vehicle_model']; else echo $dash_not_available;  ?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_vehicle_no') != '') echo stripslashes($this->lang->line('dash_vehicle_no')); else  echo 'Vehicle No';
						?></label>
										<div class="form_input">
										<?php if(isset($rides_details->driver['vehicle_no']))echo $rides_details->driver['vehicle_no']; else echo $dash_not_available;  ?>
										</div>
									</div>
								</li>
								
								<?php 
									}
								}
								?>

								 <li>
									<h2><?php 
						if($this->lang->line('dash_booking_details') != '') echo stripslashes($this->lang->line('dash_booking_details')); else  echo 'Booking Details';
						?></h2>
								 </li>
								 
								 <li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_service_type') != '') echo stripslashes($this->lang->line('dash_service_type')); else  echo 'Service Type';
						?></label>
										<div class="form_input">
										<?php if(isset($rides_details->booking_information['service_type']))echo $rides_details->booking_information['service_type']; else echo $dash_not_available;  ?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_booking_date') != '') echo stripslashes($this->lang->line('dash_booking_date')); else  echo 'Booking Date';
						?></label>
										<div class="form_input">
										<?php
										if(isset($rides_details->booking_information['booking_date']->sec))
										$bookDateSec = $rides_details->booking_information['booking_date']->sec; else $bookDateSec='';
										
										if($bookDateSec != '') echo date('d,M-Y h:i A',$bookDateSec); else echo $dash_not_available;
										?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_pickup_date') != '') echo stripslashes($this->lang->line('dash_pickup_date')); else  echo 'Pickup Date';
						?></label>
										<div class="form_input">
										<?php
										if(isset($rides_details->booking_information['pickup_date']->sec))
										$pickDateSec = $rides_details->booking_information['pickup_date']->sec; else $pickDateSec='';
										
										if($pickDateSec != '') echo date('d,M-Y h:i A',$pickDateSec); else echo $dash_not_available;
										?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_estimated_pickup_date') != '') echo stripslashes($this->lang->line('dash_estimated_pickup_date')); else  echo 'Estimated Pickup Date';
						?></label>
										<div class="form_input">
										<?php
										if(isset($rides_details->booking_information['est_pickup_date']->sec))
										$estpickDateSec = $rides_details->booking_information['est_pickup_date']->sec; else $estpickDateSec='';
										
										if($estpickDateSec != '') echo date('d,M-Y h:i A',$estpickDateSec); else echo $dash_not_available;
										?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_pickup_location') != '') echo stripslashes($this->lang->line('dash_pickup_location')); else  echo 'Pickup Location';
						?></label>
										<div class="form_input">
										<?php 
												if(isset($rides_details->booking_information['pickup']['location'])){ 
													if($rides_details->booking_information['pickup']['location'] != '') 
													echo $rides_details->booking_information['pickup']['location']; else echo $dash_not_available; 
												} else {
												echo $dash_not_available; 
												}
												?>
										</div>
									</div>
								</li>
								
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_drop_location') != '') echo stripslashes($this->lang->line('dash_drop_location')); else  echo 'Drop Location';
						?></label>
										<div class="form_input">
										<?php
												if(isset($rides_details->booking_information['drop']['location'])){ 
													if($rides_details->booking_information['drop']['location'] != '') 
													echo $rides_details->booking_information['drop']['location']; else echo $dash_not_available; 
												} else {
												echo $dash_not_available; 
												}
												?>
										</div>
									</div>
								</li>
								
								<?php 
									if($rideStatus == 'Completed' || $rideStatus == 'Finished'){
								?>
									<li>
										<h2><?php 
						if($this->lang->line('dash_fare_summary') != '') echo stripslashes($this->lang->line('dash_fare_summary')); else  echo 'Fare Summary';
						?></h2>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_total_fare') != '') echo stripslashes($this->lang->line('dash_total_fare')); else  echo 'Total Fare';
						?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['grand_fare'])) {
												if($rides_details->total['grand_fare'] != '') echo $dcurrencySymbol.' '.number_format($rides_details->total['grand_fare'],2); else echo $dash_not_available; 
											} else {
												 echo $dash_not_available; 
											}
													
											?>
											</div>
										</div>
									</li>
									
									<?php  if(isset($rides_details->total['tips_amount'])) { ?>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_driver_tips_amount') != '') echo stripslashes($this->lang->line('dash_driver_tips_amount')); else  echo 'Driver Tips Amount';
						?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['tips_amount'])) {
												if($rides_details->total['tips_amount'] != '') echo $dcurrencySymbol.' '.number_format($rides_details->total['tips_amount'],2); else echo $dash_not_available; 
											} else {
												 echo $dash_not_available; 
											}
													
											?>
											</div>
										</div>
									</li>
									
									<?php } ?>
									
									<?php  if(isset($rides_details->amount_commission)) { 
										if($rides_details->amount_commission > 0){
									?>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_commission_amount') != '') echo stripslashes($this->lang->line('dash_commission_amount')); else  echo 'Commission Amount';
						?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->amount_commission)) {
												if($rides_details->amount_commission != '') echo $dcurrencySymbol.' '.number_format($rides_details->amount_commission,2); else echo $dash_not_available; 
											} else {
												 echo $dash_not_available; 
											}
											
											if(isset($rides_details->commission_percent)) {
												if($rides_details->commission_percent != '')  echo ' ('.$rides_details->commission_percent.'%)'; 
											} 
													
											?>
											</div>
										</div>
									</li>
									
									<?php } } ?>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_total_distance') != '') echo stripslashes($this->lang->line('dash_total_distance')); else  echo 'Total Distance';
						?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->summary['ride_distance'])) { 
												$totRidedistance = $rides_details->summary['ride_distance'];
												echo number_format($rides_details->summary['ride_distance'],1). ' '.$d_distance_unit; 
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>
									
										
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_total_riding_time') != '') echo stripslashes($this->lang->line('dash_total_riding_time')); else  echo 'Total Riding Time';
						?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->summary['ride_duration'])) {
												$totRidetime = $rides_details->summary['ride_duration'];
												echo $rides_details->summary['ride_duration'].' min'; 
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_total_waiting_time') != '') echo stripslashes($this->lang->line('dash_total_waiting_time')); else  echo 'Total Waiting Time';
						?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->summary['waiting_duration'])) {
												echo $rides_details->summary['waiting_duration'].' min'; 
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>

									<li>
										<h2><?php 
						if($this->lang->line('dash_fare_details') != '') echo stripslashes($this->lang->line('dash_fare_details')); else  echo 'Fare Details';
						?></h2>
									</li>
									
									<li>
									<?php
											if(isset($rides_details->fare_breakup['min_km'])) $min_km = $rides_details->fare_breakup['min_km']; else $min_km = 0; ?>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_base_fare_for') != '') echo stripslashes($this->lang->line('dash_base_fare_for')); else  echo 'Base fare for';
						?> <?php echo $min_km;?>  <?php 
						  echo $d_distance_unit;
						?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['base_fare'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['base_fare'],2);
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>
									
									<?php 
									if(isset($rides_details->total['coupon_discount'])) {
										$coupon_discount = $rides_details->total['coupon_discount']; 
									}else{ 
										$coupon_discount = 0; 
									}
									if($coupon_discount > 0){
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_coupon_discount') != '') echo stripslashes($this->lang->line('dash_coupon_discount')); else  echo 'Coupon discount';
						?></label>
											<div class="form_input">
											<?php echo $dcurrencySymbol.' '.number_format($coupon_discount,2); ?>
											</div>
										</div>
									</li>
									<?php } ?>
									
									<li>
										<div class="form_grid_12">
											<?php $baseDistance = 0;
												if(isset($rides_details->fare_breakup['min_km'])){
													$baseDistance = $rides_details->fare_breakup['min_km'];
												}
												$remainDistancetocharge = $totRidedistance - $baseDistance;
												if($remainDistancetocharge < 0){
													$remainDistancetocharge = 0;
												}
											?>
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_rate_for') != '') echo stripslashes($this->lang->line('dash_rate_for')); else  echo 'Rate for';
						?> <?php echo number_format($remainDistancetocharge,1);?> <?php  echo $d_distance_unit; ?>
						</label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['distance'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['distance'],2); 
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
										<?php $freerideTime = 0;
										if(isset($rides_details->fare_breakup['min_time'])){
											$freerideTime = $rides_details->fare_breakup['min_time'];
										}
										?>
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_free_ride_time') != '') echo stripslashes($this->lang->line('dash_free_ride_time')); else  echo 'Free ride time';
						?> (<?php echo $freerideTime; ?> <?php 
						if($this->lang->line('dash_min') != '') echo stripslashes($this->lang->line('dash_min')); else  echo 'min';
						?>)</label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['free_ride_time'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['free_ride_time'],2); 
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<?php $baseTime = 0;
												if(isset($rides_details->fare_breakup['min_time'])){
													$baseTime = $rides_details->fare_breakup['min_time'];
												}
												$remainTimetocharge = $totRidetime - $baseTime;
												if($remainTimetocharge < 0){
													$remainTimetocharge = 0;
												}
											?>
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_rate_for') != '') echo stripslashes($this->lang->line('dash_rate_for')); else  echo 'Rate for';
						?> <?php echo $remainTimetocharge;?> <?php 
						if($this->lang->line('dash_min') != '') echo stripslashes($this->lang->line('dash_min')); else  echo 'min';
						?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['ride_time'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['ride_time'],2); 
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>
									
									<?php 
									if(isset($rides_details->fare_breakup['peak_time_charge'])) $peak_time_charge = $rides_details->fare_breakup['peak_time_charge'];
									if($peak_time_charge != '' && $peak_time_charge > 0){
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code">Peak Pricing charge ( <?php echo $peak_time_charge;?> <span style="font-size:8px;">x</span> )</label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['peak_time_charge'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['peak_time_charge'],2); 
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>
								<?php } ?>
								
								<?php 
									if(isset($rides_details->fare_breakup['night_charge'])) $night_charge = $rides_details->fare_breakup['night_charge'];
									if($night_charge != '' && $night_charge > 0){
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_night_time_charge') != '') echo stripslashes($this->lang->line('dash_night_time_charge')); else  echo 'Night time charge';
						?></label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['night_time_charge'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['night_time_charge'],2); 
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>
								<?php } ?>
								
								<?php 
									if(isset($rides_details->fare_breakup['wait_per_minute'])) $wait_per_minute = $rides_details->fare_breakup['wait_per_minute'];
									if($wait_per_minute != '' && $wait_per_minute > 0){
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_waiting_time_charge') != '') echo stripslashes($this->lang->line('dash_waiting_time_charge')); else  echo 'Waiting time charge';
						?> ( <?php echo $dcurrencySymbol.' '.number_format($wait_per_minute,2);?> <?php 
						if($this->lang->line('dash_per_min') != '') echo stripslashes($this->lang->line('dash_per_min')); else  echo 'per min';
						?> )</label>
											<div class="form_input">
											<?php
											if(isset($rides_details->total['wait_time'])) {
												echo $dcurrencySymbol.' '.number_format($rides_details->total['wait_time'],2); 
											} else {
												 echo $dash_not_available; 
											}
											?>
											</div>
										</div>
									</li>
								<?php } ?>
								
								<?php 
									if(isset($rides_details->total['service_tax'])) $service_tax = $rides_details->total['service_tax'];
									if($service_tax != '' && $service_tax > 0){
									?>
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="code"><?php 
						if($this->lang->line('dash_service_tax') != '') echo stripslashes($this->lang->line('dash_service_tax')); else  echo 'Service Tax';
						?></label>
											<div class="form_input">
											<?php
												echo $dcurrencySymbol.' '.number_format($service_tax,2); 
											?>
											</div>
										</div>
									</li>
								<?php } ?>
									

								<?php 
									}
								?>
								
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<a class="tipLeft" href="<?php  if(isset($_SERVER['HTTP_REFERER'])) echo $_SERVER['HTTP_REFERER']; else echo 'driver/rides/display_rides';?>" original-title="<?php 
						if($this->lang->line('dash_go__rides_list') != '') echo stripslashes($this->lang->line('dash_go__rides_list')); else  echo 'Go to rides list';
						?>">
												<span class="badge_style b_done"><?php 
						if($this->lang->line('dash_back') != '') echo stripslashes($this->lang->line('dash_back')); else  echo 'Back';
						?></span>
											</a>
										</div>
									</div>
								</li>
							</ul>
						
							</div>
							
							<div id="tab2">
								<ul>
								
								
							<?php 
						if(isset($rides_details->ratings)){
							if(count($rides_details->ratings) > 0){
								foreach($rides_details->ratings as $holder => $reviewsHolder) { 
									if($holder == 'driver'){
										$holder = 'you';
									}
								?>	
									<li>	
										<h2><?php 
						if($this->lang->line('dash_ratings_for') != '') echo stripslashes($this->lang->line('dash_ratings_for')); else  echo 'Ratings for';
						?> <?php echo $holder; ?> 
													<?php 
													if(isset($reviewsHolder['avg_rating']))  $avg_rating = number_format($reviewsHolder['avg_rating'],2); else $avg_rating = 0; 
													?>
													<?php /*<div class="ratingstar-<?php echo trim(round(stripslashes($reviewsHolder['avg_rating'])));?>" id="rating-pos<?php echo $holder;?>" style="float:none;">  </div>*/ ?>
													
													<div class="star str" id="star-pos<?php echo $holder;?>"  data-star="<?php echo $avg_rating;?>" style="width: 200px;float:none;"></div>
													<span>( <?php echo $avg_rating;?> )</span>
										</h2>
									</li>
									<?php 
									foreach($reviewsHolder['ratings'] as $reviews) { 
												?>	
										
											<li>
												<div class="form_grid_12">
													<label class="field_title"><?php echo $reviews['option_title']; ?></label>
													<div class="form_input">
														<?php if(isset($reviews['rating']))  $rating = number_format($reviews['rating'],2); else $rating = 0; ?>
														<?php /*<div class="ratingstar-<?php echo trim(round(stripslashes($reviews['rating'])));?>" id="rating-pos<?php echo $reviews['option_id'];?>">  </div>*/ ?>
														<div class="star" id="star-pos<?php echo $reviews['option_id'];?>" data-star="<?php echo $rating;?>"></div>
														<span>&nbsp;</span>
														<span class="starRatings-count">( <?php echo $rating;?> )</span>
													</div>
												</div>
											</li>
												<?php 
									} 
								}
							} else {
							?>
							
								<li>	
									<h2><?php 
						if($this->lang->line('dash_no_records_found_this') != '') echo stripslashes($this->lang->line('dash_no_records_found_this')); else  echo 'No records found for this';
						?> <?php $reviews[0]['option_holder']; ?></h2>
								</li>
							
							<?php } 
						} else {
						?>
						
							<li>	
								<h2><?php 
						if($this->lang->line('dash_not_submitted_ratings_rides') != '') echo stripslashes($this->lang->line('dash_not_submitted_ratings_rides')); else  echo 'Not submitted any ratings for this rides';
						?></h2>
							</li>
						
						<?php } ?>
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<a href="javascript:void(0);" onclick="javascript: window.history.go(-1);" class="tipLeft" title="<?php 
						if($this->lang->line('dash_go_users_list') != '') echo stripslashes($this->lang->line('dash_go_users_list')); else  echo 'Go to users list';
						?>"><span class="badge_style b_done"><?php 
						if($this->lang->line('dash_back') != '') echo stripslashes($this->lang->line('dash_back')); else  echo 'Back';
						?></span></a>
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


<style>
.starRatings-count {
	float: right;
    margin-right: 77%;
    margin-top: -21px;
}

#tab2 h2 {
    border: 1px solid grey;
    border-radius: 8px;
	background-color: #eee;
}
.str{
	width:200px !important;
}

</style>

<?php 
$this->load->view('driver/templates/footer.php');
?>