<?php
$this->load->view('site/templates/profile_header');
$findpage = $this->uri->segment(2);
$rides_details = $rides_details->row();


$longitude = $rides_details->booking_information['pickup']['latlong']['lon'];
$latitude = $rides_details->booking_information['pickup']['latlong']['lat'];
$loc_key = 'lat_' . str_replace('.', '-', $longitude) . 'lon_' . str_replace('.', '-', $latitude);

$fav = 'No'; #echo $loc_key; echo '<pre>';  print_r($favouriteList->row()->fav_location); die;
if(isset($favouriteList->row()->fav_location[$loc_key])){
	$fav = 'Yes';
} 
if(isset($rides_details->currency_symbol)){
	$dcurrencySymbol = $rides_details->currency_symbol;
}
if(isset($rides_details->fare_breakup['distance_unit'])){
	$d_distance_unit = $rides_details->fare_breakup['distance_unit'];
}


if($d_distance_unit=="km"){
	 $d_distance_unit = get_language_value_for_keyword('km',$this->data['langCode']);
}else if($d_distance_unit=="mi"){
	 $d_distance_unit = get_language_value_for_keyword('mi',$this->data['langCode']);
}

?> 


<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">
                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>
                <div class="col-md-9 nopadding profile_rider_right">
                    <div class="col-md-12 nopadding rider-pickup-detail">
                        <h2><?php if ($this->lang->line('rides_ride_details') != '') echo stripslashes($this->lang->line('rides_ride_details')); else echo 'RIDE DETAILS'; ?></h2>
                        <div class="col-md-12 nopadding pickup-detail-top">
                            <div class="col-md-8">
                                <h3><?php echo strtoupper($this->config->item('email_title')); ?> <?php if ($this->lang->line('rides_taxi') != '') echo stripslashes($this->lang->line('rides_taxi')); else echo 'TAXI'; ?> <?php echo $rides_details->booking_information['service_type']; ?> <?php if ($this->lang->line('rides_ride') != '') echo stripslashes($this->lang->line('rides_ride')); else echo 'RIDE'; ?></h3>
                                <span><?php if ($this->lang->line('rides_ride_id') != '') echo stripslashes($this->lang->line('rides_ride_id')); else echo 'RIDE ID :'; ?><?php echo $rides_details->ride_id; ?> </span>
                            </div>
                            <div class="col-md-4">
                                <a style="text-transform:uppercase;"><?php echo get_language_value_for_keyword($rides_details->ride_status,$this->data['langCode']); ?></a>
                            </div>
                        </div>
                        <div class="col-md-12 nopadding pickup-detail-middle" id="map_container">
                            <?php echo $map['js']; ?>
                            <?php echo $map['html']; ?>

                            <?php 
							if($fav == 'Yes'){
								?>
								<div class="hover-fav"><a href="#" data-toggle="modal" data-target="#make-unfav"><img src="images/Heart_red.png"></a></div>
							<?php } else { ?>
								<div class="hover-fav"><a href="#" data-toggle="modal" data-target="#make-fav"><img src="images/Heart_gray.png"></a></div>
							<?php } ?>
                        </div>
                        <div class="col-md-12 nopadding pickup-detail-bottom">
									<div class="ride-location ride-description">
									<h2><?php if ($this->lang->line('rides_map_pickup_details') != '') echo stripslashes($this->lang->line('rides_map_pickup_details')); else echo 'Pickup Details :'; ?></h2>
									</div>
                            <div class="ride-location">

                                <?php

								
								$est_pickup_date = '';
                                if(isset($rides_details->booking_information['est_pickup_date']->sec)){
									$est_pickup_date = $rides_details->booking_information['est_pickup_date']->sec;
								}
								$location = '';
								if(isset($rides_details->booking_information['pickup']['location'])){
									$location = $rides_details->booking_information['pickup']['location'];
								}
								$location_lat = '';
								if(isset($rides_details->booking_information['pickup']['latlong']['lat'])){
									$location_lat = $rides_details->booking_information['pickup']['latlong']['lat'];
								}
								$location_lon = '';
								if(isset($rides_details->booking_information['pickup']['latlong']['lon'])){
									$location_lon = $rides_details->booking_information['pickup']['latlong']['lon'];
								}
                               

							   $locationArr = @explode(',', $location);
                                $initial_Loc = $locationArr[0];
                                unset($locationArr[0]);
                                $remainLOcation = implode(', ', $locationArr);
                                ?>

                                <p><?php echo $initial_Loc; ?>
                                    <span><?php echo $remainLOcation; ?></span></p>
                            </div>
									<div class="ride-location ride-time">
                                <p><?php echo date('h:i A', $est_pickup_date); ?> on <?php echo date('d M, Y', $est_pickup_date); ?></p>
                            </div>
							<?php		

								$dropinTime = '';
                                if(isset($rides_details->booking_information['drop_date']->sec)){
									$dropinTime = $rides_details->booking_information['drop_date']->sec;
								}								
								$drop_location = '';
								if(isset($rides_details->booking_information['drop']['location'])){
									$drop_location = $rides_details->booking_information['drop']['location'];
								}
								$drop_location_lat = '';
								if(isset($rides_details->booking_information['drop']['latlong']['lat'])){
									$drop_location_lat = $rides_details->booking_information['drop']['latlong']['lat'];
								}
								$drop_location_lon = '';
								if(isset($rides_details->booking_information['drop']['latlong']['lon'])){
									$drop_location_lon = $rides_details->booking_information['drop']['latlong']['lon'];
								}
                               

							   $drop_locationArr = @explode(',', $drop_location);
                                $drop_initial_Loc = $drop_locationArr[0];
                                unset($drop_locationArr[0]);
                                $drop_remainLOcation = implode(', ', $drop_locationArr);
                                ?>
							<?php if($dropinTime!='' && $drop_initial_Loc!='') {?>
							 <div class="ride-location ride-description">
									<h2><?php if ($this->lang->line('rides_map_drop_details') != '') echo stripslashes($this->lang->line('rides_map_drop_details')); else echo 'Drop Details :'; ?></h2>
									</div>
                                 <div class="ride-location">

                                

                                <p><?php echo $drop_initial_Loc; ?>
                                    <span><?php echo $drop_remainLOcation; ?></span></p>
                            </div>
                            
							
									<div class="ride-location ride-time">
                                <p><?php echo date('h:i A', $dropinTime); ?> <?php if ($this->lang->line('rides_map_on') != '') echo stripslashes($this->lang->line('rides_map_on')); else echo 'on'; ?> <?php echo date('d M, Y', $dropinTime); ?></p>
                            </div>
                          <?php }?>  
							
						 <?php
							$ride_distance = 0;
							$time_taken = 0;
							$wait_time = 0;
							if(isset( $rides_details->summary['ride_distance'])) $ride_distance = $rides_details->summary['ride_distance']; 
							if(isset( $rides_details->summary['ride_duration'])) $time_taken = $rides_details->summary['ride_duration'];
							if(isset( $rides_details->summary['waiting_duration'])) $wait_time = $rides_details->summary['waiting_duration'];
								
							if(isset($rides_details->ride_status)){
							if($rides_details->ride_status == 'Completed' || $rides_details->ride_status == 'Completed'){
						?>
						
									<div class="ride-location ride-description">
									<h2><?php if ($this->lang->line('dash_ride_details') != '') echo stripslashes($this->lang->line('dash_ride_details')); else echo 'Ride Details'; ?></h2>
									</div>
							
							<div class="ride-location ride-distance">
                                <ul>
                                    <li>
                                        <p>
											<?php
                                            echo $ride_distance.' ';
                                            
                                                 echo $d_distance_unit;
                                            
                                            ?> 
										</p>
                                        <span>
										<?php if ($this->lang->line('rides_ride_distance') != '') echo stripslashes($this->lang->line('rides_ride_distance')); else echo 'ride distance'; ?>
										</span>
                                    </li>
                                    <li>
                                        <p>
											<?php
                                            echo $time_taken.' ';
                                            if ($time_taken > 1) {
                                                if ($this->lang->line('rides_mins_lower') != '') echo stripslashes($this->lang->line('rides_mins_lower')); else echo 'mins';
                                            } else {
                                                if ($this->lang->line('rides_min_lower') != '') echo stripslashes($this->lang->line('rides_min_lower')); else echo 'min';
                                            }
                                            ?> 
										</p>
                                        <span><?php if ($this->lang->line('rides_time_taken') != '') echo stripslashes($this->lang->line('rides_time_taken')); else echo 'time taken'; ?></span>
                                    </li>
                                    <li>
                                        <p><?php
                                            echo $wait_time.' ';
                                            if ($wait_time > 1) {
                                                if ($this->lang->line('rides_mins_lower') != '') echo stripslashes($this->lang->line('rides_mins_lower')); else echo 'mins'; 
											} else {
                                                if ($this->lang->line('rides_min_lower') != '') echo stripslashes($this->lang->line('rides_min_lower')); else echo 'min';
                                            }
                                            ?>
										</p>
                                        <span>
											<?php if ($this->lang->line('rides_wait_time') != '') echo stripslashes($this->lang->line('rides_wait_time')); else echo 'wait time'; ?>
										</span>
                                    </li>
                                </ul>
                            </div>
				<?php   	}
						}
						?>
						
						
						
							<?php 
								if($rides_details->ride_status == 'Completed' || $rides_details->ride_status == 'Finished'){
							?>
							<div class="ride-location ride-description">
								<h2><?php if ($this->lang->line('dash_fare_details') != '') echo stripslashes($this->lang->line('dash_fare_details')); else echo 'Fare Details'; ?></h2>
								<ul  class="fare-ul">
									<li>
										<label><?php if ($this->lang->line('dash_total_fare') != '') echo stripslashes($this->lang->line('dash_total_fare')); else echo 'Total Fare'; ?></label>
										<label>
											<?php
											if(isset($rides_details->total['total_fare'])) {
												if($rides_details->total['total_fare'] != '') {
													echo $dcurrencySymbol.' '.number_format($rides_details->total['total_fare'],2); 
												}else{
													echo 'Not Available'; 
												}
											} else {
												 echo 'Not Available'; 
											}		
											?>
										</label>
									</li>
									<li>
										<label><?php if ($this->lang->line('driver_service_tax') != '') echo stripslashes($this->lang->line('driver_service_tax')); else echo 'Service Tax'; ?></label>
										<label>
											<?php
											if(isset($rides_details->total['service_tax'])) {
												if($rides_details->total['service_tax'] != '') {
													echo $dcurrencySymbol.' '.number_format($rides_details->total['service_tax'],2); 
												}else{
													echo 'Not Available'; 
												}
											} else {
												 echo 'Not Available'; 
											}		
											?>
										</label>
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
										<label><?php if ($this->lang->line('admin_rides_coupon_discount') != '') echo stripslashes($this->lang->line('admin_rides_coupon_discount')); else echo 'Coupon discount'; ?></label>
										<label>
											<?php echo $dcurrencySymbol.' '.number_format($coupon_discount,2); ?>
										</label>
									</li>
									<?php } ?>
									<li>
										<label><?php if ($this->lang->line('rides_grand_fare') != '') echo stripslashes($this->lang->line('rides_grand_fare')); else echo 'Grand Fare'; ?></label>
										<label>
											<?php
											if(isset($rides_details->total['grand_fare'])) {
												if($rides_details->total['grand_fare'] != '') {
													echo $dcurrencySymbol.' '.number_format($rides_details->total['grand_fare'],2); 
												}else{
													echo 'Not Available'; 
												}
											} else {
												 echo 'Not Available'; 
											}		
											?>
										</label>
									</li>
									
									
								</ul>
							</div>
							<?php } ?>
						
						
						
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="make-unfav" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel1">  <?php if ($this->lang->line('user_are_you_confirm') != '') echo stripslashes($this->lang->line('user_are_you_confirm')); else echo 'Are you confirm'; ?>!</h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 text-left sign_driver_text">
					<span> <?php if ($this->lang->line('user_remove_fav_loc_confirm') != '') echo stripslashes($this->lang->line('user_remove_fav_loc_confirm')); else echo 'Do you want to remove this location from your favourite list'; ?>? </span>
					<span id="FavErr1" class="favErr"></span>
				</div>
				
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="return makeLocUnFav();" id="cont-btn1"><?php if ($this->lang->line('user_yes') != '') echo stripslashes($this->lang->line('user_yes')); else echo 'Yes'; ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="make-fav" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php if ($this->lang->line('user_add_fav_location') != '') echo stripslashes($this->lang->line('user_add_fav_location')); else echo 'Add this location into your favourite list'; ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 text-left sign_driver_text">
					<input type="text" class="form-control sign_in_text required email" id="favourite_title" placeholder="<?php if ($this->lang->line('user_favourite_title') != '') echo stripslashes($this->lang->line('user_favourite_title')); else echo 'Favourite location title'; ?>">
					<span id="FavErr" class="favErr"></span>
				</div>
				
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="return makeLocFav();" id="cont-btn"><?php if ($this->lang->line('user_continue') != '') echo stripslashes($this->lang->line('user_continue')); else echo 'Continue'; ?></button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="exist-fav" value="<?php echo $fav; ?>" />
<input type="hidden" id="address" value="<?php echo $location; ?>" />
<input type="hidden" id="user_id" value="<?php echo $rides_details->user['id']; ?>" />
<input type="hidden" id="longitude" value="<?php echo $location_lon; ?>" />
<input type="hidden" id="latitude" value="<?php echo $location_lat; ?>" />
<input type="hidden" id="favKey" value="<?php echo $loc_key; ?>" />

<style>
.modal-footer {
    clear: both;
}
#FavErr ,#FavErr1 {
	padding-left:12px;
}
.ride-location.ride-description {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0 !important;
}
.fare-ul label:last-child {
    font-weight: normal;
}
.fare-ul label:first-child {
    width: 74%;
}
.fare-ul {
    margin: 0;
    width: 100%;
}
.fare-ul > li {
    line-height: 25px;
    width: 50%;
}
.ride-location.ride-description > h2 {
    text-align: left;
}
.ride-location.ride-description > h2 {
    margin-left: -30px !important;
    text-align: left;
}
</style>
<script>
<?php if ($this->lang->line('user_fav_location_added') != ''){ ?>
var favAdded = "<?php echo stripslashes($this->lang->line('user_fav_location_added')); ?>";
<?php }else{ ?>
var favAdded = "Location added into your favourite list";
<?php } ?>
<?php if ($this->lang->line('user_fav_location_removed') != ''){ ?>
var favRemoved = "<?php echo stripslashes($this->lang->line('user_fav_location_removed')); ?>";
<?php }else{ ?>
var favRemoved = "Location removed from your favourite list";
<?php } ?>
	function makeLocFav(){
		var fav_title = $('#favourite_title').val();
		var address = $('#address').val();
		var user_id = $('#user_id').val();
		var longitude = $('#longitude').val();
		var latitude = $('#latitude').val();
		$('#favourite_title').css('border-color','none');
		$('#FavErr').css('display','none');
		if(fav_title != ''){
			$('#cont-btn').html('<img src="images/indicator.gif">');
			$.ajax({
			    type: "POST",
			    url: 'mobile/user_profile/add_favourite_location',
			    data: {'title':fav_title,'address':address,'user_id':user_id,'longitude':longitude,'latitude':latitude},
			    dataType: 'json',
				success:function(res){
					$('#FavErr').css('display','block');
					$('#cont-btn').html('Continue');
					if(res.status == '1'){ 
						$('#FavErr').css('color','green');
						$('#FavErr').html(favAdded);
						location.reload();
					} else {
						$('#FavErr').css('color','red');
						$('#FavErr').html(res.message);

					}
				} 
			});
		} else {
			$('#favourite_title').css('border-color','red');	
		}
	}
	
	function makeLocUnFav(){
		var user_id = $('#user_id').val();
		var favLocKey = $('#favKey').val();
		if(favLocKey != ''){
			$('#cont-btn1').html('<img src="images/indicator.gif">');
			$('#FavErr1').css('display','none');
			$.ajax({
			    type: "POST",
			    url: 'mobile/user_profile/remove_favourite_location',
			    data: {'user_id':user_id,'location_key':favLocKey},
			    dataType: 'json',
				success:function(res){
					$('#FavErr1').css('display','block');
					$('#cont-btn1').html('Yes');
					if(res.status == '1'){ 
						$('#FavErr1').css('color','green');
						$('#FavErr').html(favRemoved);
						location.reload();
					} else {
						$('#FavErr1').css('color','red');
						$('#FavErr1').html(res.message);
					}
				} 
			});
		} else {
			alert('Please refresh this page and try again');
		}
	}

</script>


<?php
$this->load->view('site/templates/footer');
?> 