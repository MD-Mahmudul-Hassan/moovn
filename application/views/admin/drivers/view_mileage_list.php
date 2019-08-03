<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
?>
<script>
		
	$(function (){
		
		$("#export_mileage").click(function(event){
		
			event.preventDefault();
			get_field_values();
			window.location.href = "admin/drivers/view_mileage_list_report/<?php echo $driver_id; ?>?date_from="+$date_from+'&date_to='+$date_to+'&ride_id='+$ride_id;
		});
		
	});
	
	function get_field_values(){
	
		$date_from = $("#date_from").val();
		$date_to = $("#date_to").val();
		$ride_id = $("#filtervalue").val();
		
	}
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
								
									<h6>Ride Filter</h6>
									<div class="btn_30_light">	
									<form method="get" id="filter_form" action="admin/drivers/view_mileage/<?php echo $driver_id; ?>" accept-charset="UTF-8">
									<input name="ride_id" id="filtervalue" type="text" tabindex="2" class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($ride_id)) echo $ride_id; ?>" placeholder="Ride id" />
									
									<input name="date_from" id="date_from" type="hidden" value="<?php if(isset($start_date)) echo $start_date; ?>" />
									
									<input name="date_to" id="date_to" type="hidden" value="<?php if(isset($end_date)) echo $end_date; ?>"  />
				
										<button type="submit" class="tipTop filterbtn" tabindex="3" original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
											<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_filter') != '') echo stripslashes($this->lang->line('admin_drivers_filter')); else echo 'Filter'; ?></span>
										</button>
										<?php if(isset($ride_id) && $ride_id!=""){ 
										 
												$urlVal='';
												if($end_date!='' && $start_date!=''){
													$enc_fromdate=$start_date;
													$enc_todate=$end_date;
													$urlVal='?&date_from='.$enc_fromdate.'&date_to='.$enc_todate;
												}
										
										?>
										
										<a href="admin/drivers/view_mileage/<?php echo $driver_id; ?><?php echo $urlVal; ?>" class="tipTop filterbtn" original-title="<?php if ($this->lang->line('driver_enter_view_all_users') != '') echo stripslashes($this->lang->line('driver_enter_view_all_users')); else echo 'View All Users'; ?>">
											<span class="icon delete_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_remove_filter') != '') echo stripslashes($this->lang->line('admin_drivers_remove_filter')); else echo 'Remove Filter'; ?></span>
										</a>
										<?php } ?>
										</form>
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
                                    <span class="lct"><?php echo round($total_distance,1);?> <?php echo $d_distance_unit; ?></span>
									<br>
									 Total Duration
                                    <span class="lct"><?php echo convertToHoursMins($total_duration);?></span>
                                    
                                </div>
                            </a>								
                           
							<a class="activities_s orangebox" href="javascript:void(0)">
                                <div class="block_label">
                                     Total Free Roaming Distance
                                    <span class="lct"><?php echo round($tot_free_distance,1);?> <?php echo $d_distance_unit; ?></span>
									<br>
									 Total Free Roaming Duration
                                    <span class="lct"><?php echo convertToHoursMins($tot_free_duration);?></span>
                                </div>
                            </a>
						  <a class="activities_s greenbox" href="javascript:void(0)">
                                <div class="block_label">
                                     Total Pickup Distance
                                    <span class="lct"><?php echo round($tot_pick_distance,1);?> <?php echo $d_distance_unit; ?></span>
									<br>
									 Total Pickup Duration
                                   <span class="lct"><?php echo convertToHoursMins($tot_pick_duration);?></span>
                                </div>
                          </a>
						  <a class="activities_s redbox" href="javascript:void(0)">
                                <div class="block_label">
                                     Total Drop Distance
                                    <span class="lct"><?php echo round($tot_drop_distance,1);?> <?php echo $d_distance_unit; ?></span>
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
			</div>
		</div>
	
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open('admin/drivers/change_driver_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
					<a style="color:#fff" class="p_edit tipTop export_report" id="export_mileage">Export</a>

                </div>
                <div class="widget_content">
                    

                    <table class="display" id="mileage_view">
                        <thead>
                            <tr>
                               <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    SNO
                                </th> 
                               <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    From Time
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    To Time
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                   Type
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Duration 
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sno=1;
                                foreach ($mileage_data['result'] as $key=>$row) {
								    
									
                                    ?>
                                    <tr style="border-bottom: 1px solid #dddddd !important;">
                                       
                                        <td class="center">
                                            <?php echo $sno; ?>
											  
                                        </td>
										<td class="center">
                                            <?php echo date('d-m-Y h:i:s A',$row['mileage_data']['start_time']->sec); ?>
											  
                                        </td>
										<td class="center">
                                            
											<?php echo date('d-m-Y h:i:s A',$row['mileage_data']['end_time']->sec); ?>
											  
                                        </td>
										<td class="center">
                                            <?php echo $row['mileage_data']['type']; 
											
											if(isset($row['mileage_data']['ride_id']) && $row['mileage_data']['ride_id']!='') {
											 echo "(".$row['mileage_data']['ride_id'].")";
											
											}
											
											?>
											
											  
                                        </td>
										<td class="center">
                                            <?php echo convertToHoursMins($row['mileage_data']['duration_min']); ?>
											  
                                        </td>
										<td class="center">
                                           <?php echo round($row['mileage_data']['distance'],1); ?>
                                        </td>
																				

                                        
                                       
                                    </tr>
                                    <?php
									$sno++;
                                }
                            
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
								<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    SNO
                                </th> 
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    From Time
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    To Time
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                   Type
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Duration 
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    Distance (<?php echo $d_distance_unit_name; ?>)
                                </th>
                                
                            </tr>
                        </tfoot>
                    </table>

                  

                </div>
            </div>
        </div>
        <input type="hidden" name="statusMode" id="statusMode"/>
        <input type="hidden" name="SubAdminEmail" id="SubAdminEmail"/>
        </form>	
    </div>
    <span class="clear"></span>
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
.lct{
	text-transform: lowercase !important;
}
</style>
<?php
$this->load->view('admin/templates/footer.php');
?>