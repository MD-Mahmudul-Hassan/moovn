<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
$minDate=strtotime('2015-01-01');
?>

<link rel="stylesheet" type="text/css" media="all" href="plugins/daterangepicker/css/daterangepicker.css" />
<script type="text/javascript" src="plugins/daterangepicker/js/moment.js"></script>
<script type="text/javascript" src="plugins/daterangepicker/js/daterangepicker.js"></script>
<script type="text/javascript">
	$(function (){      
        $("#export_site_earning").click(function(event){
            event.preventDefault();
            var daysRange = $("#export-days-range").val();
            if (daysRange) {
            	window.location.href = "admin/revenue/export_site_earning/?days="+daysRange;	
            	
            } else {
            	alert("Please select a filter to export the data");
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
									<h6><?php if ($this->lang->line('admin_site_earnings_bill_period') != '') echo stripslashes($this->lang->line('admin_site_earnings_bill_period')); else echo 'Bill Period'; ?></h6>
										
									<div class="btn_30_light" style="width: 85%;">
										<select name="date_range" id="date_range">
											<option value="" <?php if($cB==''){ ?>selected="selected"<?php } ?>><?php echo $last_bill; ?> - <?php if ($this->lang->line('admin_revenue_till_now') != '') echo stripslashes($this->lang->line('admin_revenue_till_now')); else echo 'till now'; ?></option>
											<?php if($billingsList->num_rows()>0){ ?>
											<?php foreach($billingsList->result() as $bill){ ?>
											<?php
												$bill_val='';
												$bill_dis='';
												if($bill->bill_period_from->sec==$bill->bill_period_to->sec){
													$bill_val=date("m/d/Y",$bill->bill_period_from->sec);
													$bill_dis=date("m/d/Y",$bill->bill_period_from->sec);
												}else{
													$bill_val=date("m/d/Y",$bill->bill_period_from->sec).' - '.date("m/d/Y",$bill->bill_period_to->sec);
													$bill_dis=date("m/d/Y",$bill->bill_period_from->sec).' - '.date("m/d/Y",$bill->bill_period_to->sec);
												}
											?>
											<option value="<?php echo $bill_val; ?>" <?php if($cB==$bill_val){ ?>selected="selected"<?php } ?>><?php echo $bill_dis; ?></option>
											<?php } ?>
											<?php } ?>
										</select>
										<?php /*
										<input name="date_range" id="date_range" type="text" tabindex="1" class="tipTop monthYearPicker" title="Please select the Starting Date" readonly="readonly" value="<?php echo $fromdate; ?>" placeholder="Starting Date"/>
										
										 <input name="datefrom" id="datefrom" type="text" tabindex="1" class="tipTop monthYearPicker" title="Please select the Starting Date" readonly="readonly" value="<?php echo $fromdate; ?>" placeholder="Starting Date"/>
										<input name="dateto" id="dateto" type="text" tabindex="2" class="tipTop monthYearPicker" title="Please select the Ending Date" readonly="readonly" value="<?php echo $todate; ?>"  placeholder="Ending Date"/> */ ?>
										
										<a href="javascript:void(0)" onclick="return viewTrips();" class="tipTop" original-title="<?php if ($this->lang->line('site_earning_view_review_details') != '') echo stripslashes($this->lang->line('site_earning_view_review_details')); else echo 'Select month range and click here to view records'; ?>" style="margin: 0 5px -8px 7px;height: 26px;">
											<span class="icon search" style="margin-top: 8px;"></span>
											<span class="btn_link" style="line-height: 26px;height: 27px;"><?php if ($this->lang->line('admin_subadmin_submit') != '') echo stripslashes($this->lang->line('admin_subadmin_submit')); else echo 'SUBMIT'; ?></span>
										</a>
										<?php /*if($filter!=""){ ?>
										<a href="javascript:void(0)" onclick="return viewoverallTrips();" class="tipTop" original-title="View All BIllings">
											<span class="icon delete_co"></span><span class="btn_link">Clear</span>
										</a>
										<?php }*/ ?>
										
										<h6> <?php if ($this->lang->line('admin_ride_manual_filter') != '') echo stripslashes($this->lang->line('admin_ride_manual_filter')); else echo 'Manual Filter'; ?></h6>
										
										<input name="date_range" id="billFromdate" type="text" tabindex="1" class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_starting_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_starting_ride')); else echo 'Please select the Starting Date'; ?>" readonly="readonly" value="<?php echo $fromdate; ?>" placeholder="Starting Date"/>
										
										<input name="dateto" id="billTodate" type="text" tabindex="2" class="tipTop monthYearPicker" title="<?php if ($this->lang->line('admin_ride_pls_ending_ride') != '') echo stripslashes($this->lang->line('admin_ride_pls_ending_ride')); else echo 'Please select the Ending Date'; ?>" readonly="readonly" value="<?php echo $todate; ?>"  placeholder="Ending Date"/>
										
										<a href="javascript:void(0)" onclick="return viewTripsManal();" class="tipTop" original-title="<?php if ($this->lang->line('site_earning_view_review_details') != '') echo stripslashes($this->lang->line('site_earning_view_review_details')); else echo 'Select month range and click here to view records'; ?>" style="margin: 0 5px -8px 7px;height: 26px;">
											<span class="icon search" style="margin-top: 8px;"></span>
											<span class="btn_link" style="line-height: 26px;height: 27px;"><?php if ($this->lang->line('admin_subadmin_submit') != '') echo stripslashes($this->lang->line('admin_subadmin_submit')); else echo 'SUBMIT'; ?></span>
										</a>
										
									</div>
								</div>
								<div class="widget_top filter_widget">
									<div class="btn_30_light" style="width: 85%;padding-left: 20px;">

										<select name="export_days_range" id="export-days-range">
											<option value="">Select filter</option>
											<option value="last 7 days">Last 7 days</option>
											<option value="last 14 days">Last 14 days</option>
											<option value="last 21 days">Last 21 days</option>
											<option value="last month">Last month</option>
											<option value="last 2 months">Last 2 months</option>
											<option value="last 3 months">Last 3 months</option>
										</select>									

										<a href="javascript:void(0)" id="export_site_earning" class="tipTop" style="margin: 0 5px -8px 7px;height: 26px;">
											<span class="glyphicon glyphicon-upload" style="margin-top: 8px;"></span>
											<span class="btn_link" style="line-height: 26px;height: 27px;">Export</span>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>		
		
		
		
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open('admin/revenue/display_site_revenue',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php if ($this->lang->line('admin_site_earnings_revenue_details') != '') echo stripslashes($this->lang->line('admin_site_earnings_revenue_details')); else echo 'Revenue Details'; ?></h6>
						
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
									<h6 style="margin: 0;">
                                
                                <?php if ($this->lang->line('admin_filter_location') != '') echo stripslashes($this->lang->line('admin_filter_location')); else echo 'Filter By Location'; ?>
                                </h6>
								<?php 
									$location_id = $this->input->get('location_id'); 
								?>
								<div class="btn_30_light" style="margin: -15px 5px 5px;">
									<select name="locationFilter" id="locationFilter" onchange="revenue_filterby_locations();" class="chzn-select1"> 
										<?php 
										foreach($locationList->result() as $loc){
										?>
										<option value="<?php echo (string)$loc->_id; ?>" <?php if($location_id == (string)$loc->_id)echo 'selected="selected"'; ?>><?php echo $loc->city; ?></option>
										<?php 
										}
										?>
									</select>
								</div>
	
						</div>
						
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
						</div>
					</div>
					
					<div class="widget_content">
						<div class="stat_block">
							<div class="social_activities">								
								<a class="activities_s redbox w-23" >
									<div class="block_label">
										<span class="store_icon"></span><div class="clear"></div>
										<?php if ($this->lang->line('admin_site_earnings_total_rides') != '') echo stripslashes($this->lang->line('admin_site_earnings_total_rides')); else echo 'Total Rides'; ?>
										<span><?php if(isset($totalRides))echo $totalRides; ?></span>
									</div>
								</a>								
								<a class="activities_s bluebox w-23" >
									<div class="block_label">
										<span class="store_icon"></span><div class="clear"></div>
										<?php if ($this->lang->line('admin_site_earnings_total_revenue') != '') echo stripslashes($this->lang->line('admin_site_earnings_total_revenue')); else echo 'Total Revenue'; ?>
										<span><?php echo $dcurrencySymbol; ?><?php if(isset($totalRevenue))echo number_format($totalRevenue,2); ?></span>
									</div>
								</a>								
								<a class="activities_s  greenbox w-23" >
									<div class="block_label">
										<span class="store_icon"></span><div class="clear"></div>
										<?php if ($this->lang->line('admin_site_earnings_site_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_site_earnings')); else echo 'Site Earnings'; ?>
										<span><?php echo $dcurrencySymbol; ?><?php if(isset($siteRevenue))echo number_format($siteRevenue,2); ?></span>
									</div>
								</a>								
								<a class="activities_s purplebox w-23" >
									<div class="block_label">
										<span class="store_icon"></span><div class="clear"></div>
										<?php if ($this->lang->line('admin_site_earnings_driver_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_driver_earnings')); else echo 'Driver Earnings'; ?>
										<span><?php echo $dcurrencySymbol; ?><?php if(isset($driverRevenue))echo number_format($driverRevenue,2); ?></span>
									</div>
								</a>								
							</div>
						</div>
						
						
						<?php
						$tble='revenueListTbl';
						if(isset($paginationLink)){
							if($paginationLink != '') { 
								echo $paginationLink; $tble = 'revenueListTblCustom'; 
							}
						}
						?>
					
						<table class="display" id="<?php echo $tble; ?>">
							<thead>
								<tr>
									<th class="center">
										<?php if ($this->lang->line('admin_site_earnings_driver_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_driver_earnings')); else echo 'Driver Info'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_Phone') != '') echo stripslashes($this->lang->line('admin_site_earnings_Phone')); else echo 'Phone'; ?>
									</th>
									<?php /* <th class="tip_top" title="Click to sort">
										Image
									</th> */ ?>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_Trips') != '') echo stripslashes($this->lang->line('admin_site_earnings_Trips')); else echo 'Trips'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_amount_in_site') != '') echo stripslashes($this->lang->line('admin_site_earnings_amount_in_site')); else echo 'Amount in site'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_amount_in_driver') != '') echo stripslashes($this->lang->line('admin_site_earnings_amount_in_driver')); else echo 'Amount in driver'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_total') != '') echo stripslashes($this->lang->line('admin_site_earnings_total')); else echo 'Total'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_tips') != '') echo stripslashes($this->lang->line('admin_site_earnings_tips')); else echo 'Tips'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_coupon') != '') echo stripslashes($this->lang->line('admin_site_earnings_coupon')); else echo 'Coupon'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_site_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_site_earnings')); else echo 'Site Earnings'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_driver_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_driver_earnings')); else echo 'Driver Earnings'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_outstanding_amount') != '') echo stripslashes($this->lang->line('admin_site_earnings_outstanding_amount')); else echo 'Oustanding Amount'; ?>
									</th>									
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_actions') != '') echo stripslashes($this->lang->line('admin_site_earnings_actions')); else echo 'Actions'; ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								if(isset($driversList)){ 
									if (!empty($driversList)){
										foreach($driversList as $driver){
								?>
								<tr>
									<td>
										<center>
											<b><?php echo $driver['driver_name']; ?></b><br/>
											(<?php echo $driver['id']; ?>)
										</center>
									</td>
									<td><?php echo $driver['driver_phone']; ?></td>
									<?php /*
									<td>									
										<a href="<?php echo $driver['driver_image']; ?>" target=" _blank" onclick="window.open('<?php echo $driver['driver_image']; ?>', 'popup', 'height=500px, width=400px'); return false;">
											View Photo
										</a>
									</td>
									*/ ?>
									<td><?php echo $driver['total_rides']; ?></td>
									<td><?php echo $driver['in_site']; ?></td>
									<td><?php echo $driver['in_driver']; ?></td>
									<td><?php echo $driver['total_revenue']; ?></td>
									<td><?php echo $driver['driver_tips']; ?></td>
									<td><?php echo $driver['couponAmount']; ?></td>
									<td><?php echo $driver['site_earnings']; ?></td>
									<td><?php echo $driver['driver_earnings']; ?></td>
									<td><?php echo $driver['outstanding_amount']; ?></td>
									
									<td>
										<?php
										$urlVal='';
										if($fromdate!='' && $todate!=''){
											$enc_fromdate=base64_encode($fromdate);
											$enc_todate=base64_encode($todate);
											$urlVal='?trip_from='.$enc_fromdate.'&trip_to='.$enc_todate;
										}
										?>
											<span>
												<a class="action-icons c-suspend" href="admin/revenue/driver_trip_summary/<?php echo $driver['id'].$urlVal; ?>" title="<?php if ($this->lang->line('site_earning_view_summary') != '') echo stripslashes($this->lang->line('site_earning_view_summary')); else echo 'View Summary'; ?>">
													<?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?>View
												</a>
											</span>
									</td>
								</tr>
								<?php 
										}
									}
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th class="center">
										<?php if ($this->lang->line('admin_site_earnings_driver_info') != '') echo stripslashes($this->lang->line('admin_site_earnings_driver_info')); else echo 'Driver info'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_Phone') != '') echo stripslashes($this->lang->line('admin_site_earnings_Phone')); else echo 'Phone'; ?>
									</th>
									<?php /* <th>
										Image
									</th> */ ?>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_Trips') != '') echo stripslashes($this->lang->line('admin_site_earnings_Trips')); else echo 'Trips'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_amount_in_site') != '') echo stripslashes($this->lang->line('admin_site_earnings_amount_in_site')); else echo 'Amount in site'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_amount_in_driver') != '') echo stripslashes($this->lang->line('admin_site_earnings_amount_in_driver')); else echo 'Amount in driver'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_total') != '') echo stripslashes($this->lang->line('admin_site_earnings_total')); else echo 'Total'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_tips') != '') echo stripslashes($this->lang->line('admin_site_earnings_tips')); else echo 'Tips'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_coupon') != '') echo stripslashes($this->lang->line('admin_site_earnings_coupon')); else echo 'Coupon'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_site_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_site_earnings')); else echo 'Site Earnings'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_driver_earnings') != '') echo stripslashes($this->lang->line('admin_site_earnings_driver_earnings')); else echo 'Driver Earnings'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_site_earnings_outstanding_amount') != '') echo stripslashes($this->lang->line('admin_site_earnings_outstanding_amount')); else echo 'Oustanding Amount'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_site_earnings_actions') != '') echo stripslashes($this->lang->line('admin_site_earnings_actions')); else echo 'Actions'; ?>
									</th>
								</tr>
							</tfoot>
						</table>
						
						<?php 
						if(isset($paginationLink)){
							if($paginationLink != '') { 
								echo $paginationLink; $tble = 'revenueListTblCustom'; 
							}
						}
						?>
						
					</div>
				</div>
			</div>
			<input type="hidden" name="statusMode" id="statusMode"/>
			<input type="hidden" name="SubAdminEmail" id="SubAdminEmail"/>
		</form>	
	</div>
	<span class="clear"></span>
</div>
<script>
/* var mdate = new Date('<?php echo date("F d,Y H:i:s",$minDate); ?>');
$.fn.monthYearPicker = function(options) {
    options = $.extend({
        dateFormat: "yy-m-d",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        showAnim: "",
		//minDate:mdate,
		maxDate:new Date()
    }, options);
   /*  function hideDaysFromCalendar() {
        var thisCalendar = $(this);
        $('.ui-datepicker-calendar').detach();
        // Also fix the click event on the Done button.
        $('.ui-datepicker-close').unbind("click").click(function() {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            thisCalendar.datepicker('setDate', new Date(year, month, 1));
        });
    }
    $("#datefrom").datepicker(options).focus(hideDaysFromCalendar);
    $("#dateto").datepicker(options).focus(hideDaysFromCalendar); */
  /*  $("#datefrom").datepicker(options);
    $("#dateto").datepicker(options);
}
$('input.monthYearPicker').monthYearPicker(); */
/* 
$('#date_ranges').daterangepicker({
    "showDropdowns": true,
    "showWeekNumbers": true,
	//"timePicker": true,
    //"timePicker24Hour": true,
    "startDate": "<?php echo $fromdate; ?>",
    "endDate": "<?php echo $todate; ?>",
    "opens": "right",
    "buttonClasses": "btns btns-sm",
    "applyClass": "btns-selected",
    "cancelClass": "btns-cancel"
}, function(start, end, label) {
  //console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
});
 */
</script>

 <script>
	$(function () {
		$("#billFromdate").datepicker({  maxDate: '<?php echo date('m/d/Y'); ?>' });
		$("#billFromdate").datepicker("option", "showAnim", "clip");  // drop,fold,slide,bounce,slideDown,blind
		
		$("#billTodate").datepicker({  maxDate: '<?php echo date('m/d/Y'); ?>' });
		$("#billTodate").datepicker("option", "showAnim", "clip");  // drop,fold,slide,bounce,slideDown,blind
	});
</script>

<style>
			.filter_widget input, .filter_widget select {
			    border: 1px solid #d8d8d8;
			    font-family: "OpenSansRegular";
			    font-size: 12px;
			    padding: 5px 2px;
			    width: 160px;
			}
			
						
			#date_range {	
				width:150px !important;
			}

			#locationFilter {
				font-size: 14px;
				height: 33px;
				width: 200px;
			}
			#locationFilter_chzn {
				margin-top: 20px;
			    width: 200px;
			 }
			
</style>

<?php 
$this->load->view('admin/templates/footer.php');
?>