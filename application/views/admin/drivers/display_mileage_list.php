<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
?>
<?php
 $dialcode=array();

 foreach ($countryList as $country) {
    
     if ($country->dial_code != '') {
        
      $dialcode[]=str_replace(' ', '', $country->dial_code);  
       
       
       
     }
 }
 
   asort($dialcode);
   $dialcode=array_unique($dialcode);

                                    
?>
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
	
	$(function (){
		
		$("#export_mileage").click(function(event){
		
			event.preventDefault();
			get_field_values();
			window.location.href = "admin/drivers/mileage_list_report?type="+$type+'&vehicle_category='+$vehicle_category+'&date_range='+$date_range+'&dateto='+$dateto+'&filtervalue='+$value;
		});
		
	});
	function submit_ride_filter(){
		get_field_values();
		window.location.href = "admin/rides/display_rides?act=<?php if(isset($_GET['act']) && $_GET['act'] != ''){ echo $_GET['act'];} ?>&from="+$filter_from+'&to='+$filter_to+'&location='+$rideLocation;
	}
	function get_field_values(){
			
		$type = $("#filtertype").val();
		$vehicle_category = $(".vehicle_category").val();
		$date_range = $("#rideFromdate").val();
		$dateto = $("#rideTodate").val();
		$value = $("#filtervalue").val();
		
	}
</script>
<script>
$(document).ready(function(){
   $vehicle_category='';
   $country='';
   <?php  if(isset($_GET['vehicle_category'])) {?>
	$vehicle_category = "<?php echo $_GET['vehicle_category']; ?>";
    <?php }?>
    <?php  if(isset($_GET['country'])) {?>
	$country = "<?php echo $_GET['country']; ?>";
    <?php }?>
	if($vehicle_category != ''){
		$('.vehicle_category').css("display","inline");
		$('#filtervalue').css("display","none");
        $("#country").attr("disabled", true);
	}
    if($country != ''){
		$('#country').css("display","inline");
        $('.vehicle_category').attr("disabled", true);
		
	}
	$("#filtertype").change(function(){
		$filter_val = $(this).val();
        $('#filtervalue').val('');
		$('.vehicle_category').css("display","none");
		$('#filtervalue').css("display","inline");
        $('#country').css("display","none");
        $("#country").attr("disabled", true);
        $(".vehicle_category").attr("disabled", true);
		if($filter_val == 'vehicle_type'){
			$('.vehicle_category').css("display","inline");
			$('#filtervalue').css("display","none");
            $('#country').css("display","none");
            $('.vehicle_category').prop("disabled", false);
            $("#country").attr("disabled", true);
		}
        if($filter_val == 'mobile_number'){
			$('#country').css("display","inline");
			$('#country').prop("disabled", false);
            $(".vehicle_category").attr("disabled", true);
            $('.vehicle_category').css("display","none");
		}
		
	});
	
});
</script>

<div id="content">

    <div class="grid_container">
	    
	
		<div class="grid_12">
			<div class="">
					<div class="widget_content">
						<span class="clear"></span>						
						<div class="">
							<div class=" filter_wrap">
								<div class="widget_top filter_widget">
								
									<h6><?php if ($this->lang->line('admin_drivers_mileage_filter') != '') echo stripslashes($this->lang->line('admin_drivers_mileage_filter')); else echo 'Mileage Filter'; ?></h6>
									<div class="btn_30_light">	
									<form method="get" id="filter_form" action="admin/drivers/mileage_list" accept-charset="UTF-8">
										
										<select class="form-control" id="filtertype" name="type" tabindex="1">
											<option value="" data-val=""><?php if ($this->lang->line('admin_drivers_select_filter_type') != '') echo stripslashes($this->lang->line('admin_drivers_select_filter_type')); else echo 'Select Filter Type'; ?></option>
											<option value="driver_name" data-val="driver_name" <?php if(isset($type)){if($type=='driver_name'){ echo 'selected="selected"'; } }?>>
											<?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?></option>
											
											<option value="driver_location" data-val="location" <?php if(isset($type)){if($type=='driver_location'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?></option>
											<option value="vehicle_type" data-val="vehicle_type" <?php if(isset($type)){if($type=='vehicle_type'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_vehicle_type') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_type')); else echo 'Vehicle Type'; ?></option>
										</select>
                           
										<input name="value" id="filtervalue" type="text" tabindex="2" class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($value)) echo $value; ?>" placeholder="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" />
										
										<select name="vehicle_category" class='vehicle_category' style="display:none">
											<option value="">--please select vehicle type--</option>
										<?php 
											$veh_cat = '';
											if(isset($_GET['vehicle_category']) && $_GET['vehicle_category']!=''){
												$veh_cat = $_GET['vehicle_category'];
											}
											foreach($cabCats as $cat){
												if($veh_cat != '' && $veh_cat == $cat->name){
													echo "<option selected value=".$cat->name.">".$cat->name."</option>";
												}else{
													echo "<option value=".$cat->name.">".$cat->name."</option>";
												}
												
											}
										?>
										</select>
                                        <input name="date_range" id="rideFromdate" type="text" tabindex="1" class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_starting_ride')); else echo 'Please select the Starting Date'; ?>" readonly="readonly" value="<?php if(isset($_GET['date_range']))echo $_GET['date_range']; ?>" placeholder="<?php if ($this->lang->line('admin_ride_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_starting_ride')); else echo 'Starting Date'; ?>"/>
														
										<input name="dateto" id="rideTodate" type="text" tabindex="2" class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_ending_ride')); else echo 'Please select the Ending Date'; ?>" readonly="readonly" value="<?php if(isset($_GET['dateto']))echo $_GET['dateto']; ?>"  placeholder="<?php if ($this->lang->line('admin_ride_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_ending_ride')); else echo 'Ending Date'; ?>"/>
								
										<button type="submit" class="tipTop filterbtn" tabindex="3" original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
											<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_filter') != '') echo stripslashes($this->lang->line('admin_drivers_filter')); else echo 'Filter'; ?></span>
										</button>
										<?php if(isset($filter) && $filter!=""){ ?>
										<a href="admin/drivers/mileage_list" class="tipTop filterbtn" original-title="<?php if ($this->lang->line('driver_enter_view_all_users') != '') echo stripslashes($this->lang->line('driver_enter_view_all_users')); else echo 'View All Users'; ?>">
											<span class="icon delete_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_remove_filter') != '') echo stripslashes($this->lang->line('admin_drivers_remove_filter')); else echo 'Remove Filter'; ?></span>
										</a>
										<?php } ?>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>
		        <div class="grid_12">
            <div class="widget_wrap">
                
                <div class="widget_content">
                    <div class="stat_block">
                        <div class="social_activities">								
                            <a class="activities_s bluebox" href="javascript:void(0)">
                                <div class="block_label">
									 Total distance
                                    <span class="lct"><?php echo number_format($total_distance,2);?> <?php echo $d_distance_unit; ?></span>
									<br>
									 Total Duration
                                    <span class="lct"><?php echo convertToHoursMins($total_duration);?></span>
                                    
                                </div>
                            </a>								
                           
							<a class="activities_s orangebox" href="javascript:void(0)">
                                <div class="block_label">
                                     Total Free Roaming Distance
                                    <span class="lct"><?php echo number_format($tot_free_distance,2);?> <?php echo $d_distance_unit; ?></span>
									<br>
									 Total Free Roaming Duration
                                    <span class="lct"><?php echo convertToHoursMins($tot_free_duration);?></span>
                                </div>
                            </a>
						  <a class="activities_s greenbox" href="javascript:void(0)">
                                <div class="block_label">
                                     Total Pickup Distance
                                    <span class="lct"><?php echo number_format($tot_pick_distance,2);?> <?php echo $d_distance_unit; ?></span>
									<br>
									 Total Pickup Duration
                                   <span class="lct"><?php echo convertToHoursMins($tot_pick_duration);?></span>
                                </div>
                          </a>
						  <a class="activities_s redbox" href="javascript:void(0)">
                                <div class="block_label">
                                     Total Drop Distance
                                    <span class="lct"><?php echo number_format($tot_drop_distance,2);?> <?php echo $d_distance_unit; ?></span>
									<br>
									 Total Drop Duration
                                    <span class="lct"><?php echo convertToHoursMins($tot_drop_duration);?></span>
                                </div>
                          </a>
							
							
							
							
                           	
                        </div>
                    </div>
                </div>
            </div>
        </div>	
	
	<style>										
			.b_warn {
				background: orangered none repeat scroll 0 0;
				border: medium none red;
			}
			
			.filter_widget .btn_30_light {
				margin: -11px;
				width: 83%;
			}
			.activities_s{
			width: 23%;
			}
	</style>
       
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
					<a style="color:#fff" class="p_edit tipTop export_report" id="export_mileage">Export</a>
                </div>
                <div class="widget_content">
     
                    <table class="display" id="mileage_data">
                        <thead>
                            <tr>
                                
                               <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Category
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Total Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     Total Duration 
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     Free Roaming Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Free Roaming Duration 
                                </th>
                                <th>
                                   Pickup Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Pickup Duration 
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                   Drop Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                   Drop Duration 
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                           
                                foreach ($mileage_data as $key=>$row) {
									
                                    ?>
                                    <tr style="border-bottom: 1px solid #dddddd !important;">
                                       
                                        <td class="center">
                                            <?php echo $row['name']; ?>
											  
                                        </td>
										<td class="center">
                                            <?php echo $row['catgeory_name']; ?>
											  
                                        </td>
										<td class="center">
                                            <?php echo number_format(($row['free_distance']+$row['pickup_distance']+$row['drop_distance']),2); ?>
											  
                                        </td>
										<td class="center">
                                         <?php 
										 
										  $total_duration=$row['free_duration']+$row['pickup_duration']+$row['drop_duration']; 
										  
										  echo $total_duration=convertToHoursMins($total_duration);
										 ?>
											  
                                        </td>
										<td class="center">
                                            <?php echo number_format($row['free_distance'],2); ?>
											  
                                        </td>
										<td class="center">
										<?php 
										
										 echo $free_duration=convertToHoursMins($row['free_duration']);
										?>
											  
                                        </td>
										<td class="center">
                                            <?php echo number_format($row['pickup_distance'],2); ?>
											  
                                        </td>
										<td class="center">
                                            <?php 
											
											echo $pickup_duration=convertToHoursMins($row['pickup_duration']);
											?>
											  
                                        </td>
										<td class="center">
                                            <?php echo number_format($row['drop_distance'],2); ?>
											  
                                        </td>
										<td class="center">
                                            <?php 											
											echo $drop_duration=convertToHoursMins($row['drop_duration']);
											?>
											  
                                        </td>
										

                                        
                                        <td class="center" style="width:140px;">
											<?php
												$urlVal='';
												if($end_date!='' && $start_date!=''){
													$enc_fromdate=$start_date;
													$enc_todate=$end_date;
													$urlVal='?&date_from='.$enc_fromdate.'&date_to='.$enc_todate;
												}
											?>
                                            <span><a class="action-icons c-suspend" href="admin/drivers/view_mileage/<?php echo $key; ?><?php echo $urlVal; ?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>"></a></span>
                                            
                                        </td>
                                    </tr>
                                    <?php
                                }
                            
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Category
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Total Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     Total Duration 
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     Free Roaming Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Free Roaming Duration 
                                </th>
                                <th>
                                   Pickup Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Pickup Duration 
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                   Drop Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                   Drop Duration 
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>

                  

                </div>
            </div>
        </div>
        <input type="hidden" name="statusMode" id="statusMode"/>
        <input type="hidden" name="SubAdminEmail" id="SubAdminEmail"/>
		<style>
		.lct{
			text-transform: lowercase;
		}
		</style>
        
    </div>
    <span class="clear"></span>
</div>
<?php
$this->load->view('admin/templates/footer.php');
?>