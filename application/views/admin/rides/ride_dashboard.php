<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
?> 

<style>
.yellowbox {
    background: #f7941d none repeat scroll 0 0 !important;
    border: 1px solid #f7941d;
}
.center_driver_mode {
    margin-top: 20px;
    background: none !important;
     width: 98.0%;
}
#location {
    clear: both;
    height: 23px;
    margin: 1%;
    width: 47%;
}
#btn_find {
    background-color: #28cbf9;
    border: medium none;
    cursor: pointer;
    padding: 6px 32px;
}

</style>
<link rel="stylesheet" type="text/css" media="all" href="plugins/daterangepicker/css/daterangepicker.css" />
<script type="text/javascript" src="plugins/daterangepicker/js/moment.js"></script>
<script type="text/javascript" src="plugins/daterangepicker/js/daterangepicker.js"></script>
 <script>
	$(function () {
		$("#rideFromdate").datepicker({  maxDate: '<?php echo date('m/d/Y'); ?>' });
		$("#rideFromdate").datepicker("option", "showAnim", "clip");
		$("#rideTodate").datepicker({  minDate: $("#rideFromdate").val(),maxDate: '<?php echo date('m/d/Y'); ?>' });
		$("#rideFromdate").change(function(){
			$( "#rideTodate" ).datepicker( "option", "minDate", $("#rideFromdate").val() );
			$( "#rideTodate" ).datepicker( "option", "maxDate", <?php echo date('m/d/Y'); ?> );
			$("#rideTodate").datepicker("option", "showAnim", "clip");  // drop,fold,slide,bounce,slideDown,blind
		});
		
	});
</script>
<div id="content">
    <div class="grid_container">		
        <div class="grid_12" >
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"><span style="display: none;"></span><canvas width="16" height="16"></canvas></span>
                    <h6><?php if ($this->lang->line('admin_dashboard_ride_dashboard') != '') echo stripslashes($this->lang->line('admin_dashboard_ride_dashboard')); else echo 'Ride Dashboard'; ?></h6>
                </div>
                <div class="widget_content">
                    <div class="stat_block">
                        <div class="social_activities">								
                            <a class="activities_s redbox" href="admin/rides/display_rides?act=total">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_total_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_total_rides')); else echo 'Total Rides'; ?>
                                    <span><?php if (isset($totalRides)) echo $totalRides; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s greenbox" href="admin/rides/display_rides?act=Booked">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_upcomming_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_upcomming_rides')); else echo 'Upcoming Rides'; ?>
                                    <span><?php if (isset($upcommingRides)) echo $upcommingRides; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s bluebox" href="admin/rides/display_rides?act=OnRide">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_on_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_on_rides')); else echo 'On Rides'; ?>
                                    <span><?php if (isset($onRides)) echo $onRides; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s purplebox" href="admin/rides/display_rides?act=riderCancelled">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_rider_denied') != '') echo stripslashes($this->lang->line('admin_dashboard_rider_denied')); else echo 'Rider Denied'; ?>
                                    <span><?php if (isset($riderDeniedRides)) echo $riderDeniedRides; ?></span>
                                </div>
                            </a>							
                            <a class="activities_s orangebox" href="admin/rides/display_rides?act=driverCancelled">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_driver_denied') != '') echo stripslashes($this->lang->line('admin_dashboard_driver_denied')); else echo 'Driver Denied'; ?>
                                    <span><?php if (isset($driverDeniedRides)) echo $driverDeniedRides; ?></span>
                                </div>
                            </a>						
                            <a class="activities_s pealbox" href="admin/rides/display_rides?act=Completed">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_completed_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_completed_rides')); else echo 'Completed Rides'; ?>
                                    <span><?php if (isset($completedRides)) echo $completedRides; ?></span>
                                </div>
                            </a>								
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
	<div class="grid_container">
		<h4 class="filter_unfilled_h4" style="margin: 12px;width: 60%;"><b><?php if ($this->lang->line('rides_map_unfilled_rides') != '') echo stripslashes($this->lang->line('rides_map_unfilled_rides')); else echo 'Unfilled Rides'; ?> : </b></h4>
		<div class="grid_12">
			<div class="widget_wrap filter_widget_wrap">
				<div class="widget_content">
					<form method="post">
						<div class="grid_12 filter_unfilled_div">
							<input name="location" id="location" type="text" tabindex="1" class="form-control" value="<?php if(isset($address)){ echo $address; } ?>" autocomplete="off" placeholder="<?php if ($this->lang->line('admin_enter_location') != '') echo stripslashes($this->lang->line('admin_enter_location')); else echo 'Enter location'; ?>" />
							<input name="date_from" id="rideFromdate" style="padding:5px;" type="text" tabindex="1" class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_starting_ride')); else echo 'Please select the Starting Date'; ?>" readonly="readonly" value="<?php if(isset($date_from))echo $date_from; ?>" placeholder="<?php if ($this->lang->line('admin_ride_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_starting_ride')); else echo 'Starting Date'; ?>"/>
									
							<input name="date_to" id="rideTodate" style="padding:5px;" type="text" tabindex="2" class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_ending_ride')); else echo 'Please select the Ending Date'; ?>" readonly="readonly" value="<?php if(isset($date_to))echo $date_to; ?>"  placeholder="<?php if ($this->lang->line('admin_ride_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_ending_ride')); else echo 'Ending Date'; ?>"/>
							<button type="submit" class="btn" id="btn_find" tabindex="4"><?php if ($this->lang->line('admin_map_find') != '') echo stripslashes($this->lang->line('admin_map_find')); else echo 'Find'; ?></button>
						</div>
						<div class="grid_12">
							<div class="widget_wrap">
							   <div class="widget_content">
									<div class="stat_block">
										<div class="social_activities">	
												<?php if($address != '') {?>
												<h2 class="filter_location_name" style="border-bottom: medium solid; margin-bottom: 12px;width: 90%;"><b><?php if ($this->lang->line('rides_map_unfilled_rides_near') != '') echo stripslashes($this->lang->line('rides_map_unfilled_rides_near')); else echo 'Unfilled Rides Near'; ?> : </b><?php echo $address; ?></h2>
												<?php } ?>							
												<a class="activities_s redbox big" href="javascript:void(0)">
													<div class="block_label">
														<span class="user_icon multi_car_icon"></span><div class="clear"></div>
													 <?php if ($this->lang->line('rides_map_unfilled_rides') != '') echo stripslashes($this->lang->line('rides_map_unfilled_rides')); else echo 'Unfilled Rides'; ?>	
														<span><?php if (isset($unfilled_rides)) echo $unfilled_rides; ?></span>
													</div>
												</a>
												<?php 
												#print_r($categories);die;
												if(!empty($categories)){
													foreach($categories as $cat){
													
													?>
													<a class="activities_s bluebox big" href="javascript:void(0)">
													<div class="block_label">
														<span class="user_icon car_icon"></span><div class="clear"></div>
													 <?php if (isset($cat['name'])) echo $cat['name']; ?>	
														<span><?php if (isset($cat['count'])) echo $cat['count']; ?></span>
													</div>
												</a>
													<?php 
													}
												} 
												?>
											
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="grid_12">
							<?php echo $map['js']; ?>
							<?php echo $map['html']; ?>
						</div>
						
					</form>
				</div>
			</div>
		</div>
	</div>
	
</div>

</div>
<?php
$this->load->view('admin/templates/footer.php');
?>