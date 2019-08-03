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
$(function (){      
    $("#export_driver_list").click(function(event){
        event.preventDefault();
        window.location.href = "admin/drivers/export_drivers_list";
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
								
									<h6><?php if ($this->lang->line('admin_drivers_driver_filter') != '') echo stripslashes($this->lang->line('admin_drivers_driver_filter')); else echo 'Drivers Filter'; ?></h6>
									<div class="btn_30_light">	
									<form method="get" id="filter_form" action="admin/drivers/display_drivers_list" accept-charset="UTF-8">
										<select class="form-control" id="sortby" name="sortby" tabindex="1" style="width:150px;">
											<option value="" data-val=""><?php if ($this->lang->line('admin_driver_select_sort_type') != '') echo stripslashes($this->lang->line('admin_driver_select_sort_type')); else echo 'Select Sort Type'; ?></option></option>
											<option value="doj_asc" <?php if(isset($sortby)){if($sortby=='doj_asc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_join_date') != '') echo stripslashes($this->lang->line('admin_user_by_join_date')); else echo 'By Joining Date'; ?></option>
											<option value="doj_desc" <?php if(isset($sortby)){if($sortby=='doj_desc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_recently_joined') != '') echo stripslashes($this->lang->line('admin_user_by_recently_joined')); else echo 'By Recently Joined'; ?></option>
											<option value="rides_asc" <?php if(isset($sortby)){if($sortby=='rides_asc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_least_rides') != '') echo stripslashes($this->lang->line('admin_user_by_least_rides')); else echo 'By Least Rides'; ?></option>
											<option value="rides_desc" <?php if(isset($sortby)){if($sortby=='rides_desc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_maximum_rides') != '') echo stripslashes($this->lang->line('admin_user_by_maximum_rides')); else echo 'By Maximum Rides'; ?></option>
										</select>
										<select class="form-control" id="filtertype" name="type" tabindex="1">
											<option value="" data-val=""><?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?>
											<?php if ($this->lang->line('admin_drivers_select_filter_type') != '') echo stripslashes($this->lang->line('admin_drivers_select_filter_type')); else echo 'Select Filter Type'; ?></option>
											<option value="driver_name" data-val="driver_name" <?php if(isset($type)){if($type=='driver_name'){ echo 'selected="selected"'; } }?>>
											<?php if ($this->lang->line('admin_drivers_driver_name') != '') echo stripslashes($this->lang->line('admin_drivers_driver_name')); else echo 'Driver Name'; ?></option>
											<option value="email" data-val="email" <?php if(isset($type)){if($type=='email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_change_password_driver_email') != '') echo stripslashes($this->lang->line('admin_drivers_change_password_driver_email')); else echo 'Driver Email'; ?></option>
											<option value="mobile_number" data-val="mobile_number" <?php if(isset($type)){if($type=='mobile_number'){ echo 'selected="selected"'; } }?>>
											<?php if ($this->lang->line('admin_drivers_phone_number') != '') echo stripslashes($this->lang->line('admin_drivers_phone_number')); else echo 'Driver PhoneNumber'; ?></option>
											<option value="driver_location" data-val="location" <?php if(isset($type)){if($type=='driver_location'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_location_and_fare_location_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_location')); else echo 'Location'; ?></option>
											<option value="vehicle_type" data-val="vehicle_type" <?php if(isset($type)){if($type=='vehicle_type'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_drivers_vehicle_type') != '') echo stripslashes($this->lang->line('admin_drivers_vehicle_type')); else echo 'Vehicle Type'; ?></option>
										</select>
                                           <select name="country" id="country"  class=" form-control" title="Please choose your country" style="display:none;">
                                        <?php 
                                        $country = '';
											if(isset($_GET['country']) && $_GET['country']!=''){
												$country = $_GET['country'];
											}
                                        
                                        foreach ($dialcode as $row) {
                                         
                                    
                                            if($country != '' && $country == $row){
													echo "<option selected value=".$row.">".$row."</option>";
												}else{
													echo "<option value=".$row.">".$row."</option>";
												}
                                        } ?>
                                       </select>
										<input name="value" id="filtervalue" type="text" tabindex="2" class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($value)) echo $value; ?>" placeholder="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" />
										<select name="vehicle_category" class='vehicle_category' style="display:none">
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
                                     
								
										<button type="submit" class="tipTop filterbtn" tabindex="3" original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
											<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_filter') != '') echo stripslashes($this->lang->line('admin_drivers_filter')); else echo 'Filter'; ?></span>
										</button>
										<?php if(isset($filter) && $filter!=""){ ?>
										<a href="admin/drivers/display_drivers_list"class="tipTop filterbtn" original-title="<?php if ($this->lang->line('driver_enter_view_all_users') != '') echo stripslashes($this->lang->line('driver_enter_view_all_users')); else echo 'View All Users'; ?>">
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
	
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open('admin/drivers/change_driver_status_global', $attributes)
        ?>
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                        <?php if ($allPrev == '1' || in_array('2', $driver)) { ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_active_records') != '') echo stripslashes($this->lang->line('driver_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>">
                                    <span class="icon accept_co"></span>
                                    <span class="btn_link"><?php if ($this->lang->line('admin_subadmin_active') != '') echo stripslashes($this->lang->line('admin_subadmin_active')); else echo 'Active'; ?></span>
                                </a>
                            </div>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_inactive_records') != '') echo stripslashes($this->lang->line('driver_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>">
                                    <span class="icon delete_co"></span>
                                    <span class="btn_link"><?php if ($this->lang->line('admin_subadmin_inactive') != '') echo stripslashes($this->lang->line('admin_subadmin_inactive')); else echo 'Inactive'; ?></span>
                                </a>
                            </div>
                            <?php
                        }
                        if ($allPrev == '1' || in_array('3', $user)) {
                            ?>
                            <div class="btn_30_light" style="height: 29px;">
                                <a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete', '<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('driver_select_delete_records') != '') echo stripslashes($this->lang->line('driver_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>">
                                    <span class="icon cross_co"></span>
                                    <span class="btn_link"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></span>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="btn_30_light">                              
                            <a href="javascript:void(0)" class="tipTop" id="export_driver_list">
                                <span class="glyphicon glyphicon-upload"></span>
                                <span class="btn_link">Export</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="widget_content">
                    <?php
                    if ($paginationLink != '') {
                        echo $paginationLink;
                        $tble = 'alldriverListTbl';
                    } else {
                        $tble = 'driverListTbl';
                    }
                    ?>

                    <table class="display" id="<?php echo $tble; ?>">
                        <thead>
                            <tr>
                                <th class="center">
                                    <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     <?php if ($this->lang->line('admin_driver_list_req_rcvd') != '') echo stripslashes($this->lang->line('admin_driver_list_req_rcvd')); else echo 'Req Rcvd'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     <?php if ($this->lang->line('admin_driver_list_cmpl') != '') echo stripslashes($this->lang->line('admin_driver_list_cmpl')); else echo 'CMPL'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_driver_list_cxld') != '') echo stripslashes($this->lang->line('admin_driver_list_cxld')); else echo 'CXLD'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     <?php if ($this->lang->line('admin_driver_list_avg_count') != '') echo stripslashes($this->lang->line('admin_driver_list_avg_count')); else echo 'Avg(Count)'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                     <?php if ($this->lang->line('admin_driver_list_doj') != '') echo stripslashes($this->lang->line('admin_driver_list_doj')); else echo 'DOJ'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_driver_list_avail') != '') echo stripslashes($this->lang->line('admin_driver_list_avail')); else echo 'AVAIL'; ?>
                                </th>
                                <th>BLOCKED</th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_drivers_mode') != '') echo stripslashes($this->lang->line('admin_drivers_mode')); else echo 'Mode'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_drivers_verified') != '') echo stripslashes($this->lang->line('admin_drivers_verified')); else echo 'Verified'; ?>
                                </th>
                                <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                    <?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($driversList->num_rows() > 0) {
                                foreach ($driversList->result() as $row) {
                                    ?>
                                    <tr style="border-bottom: 1px solid #dddddd !important;">
                                        <td class="center tr_select ">
                                            <input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id; ?>">
                                        </td>
                                        <td class="center">
                                            <?php echo $row->driver_name; ?>
											  <?php 
												if(isset($row->category)){
													$catsId = (string)$row->category; 
													echo '<br/><br/><span style="color: gray;">'.$cabCats[$catsId]->name.'</span>'; 
												}
											?>
                                        </td>

                                        <td class="center" style="width:70px;">
                                            <?php
                                            if (isset($row->req_received)) {
                                                echo $row->req_received;
                                            } else {
                                                echo '0';
                                            }
                                            ?>
                                        </td>		
                                        <td class="center" style="width:50px;">
                                            <?php
                                            if (isset($row->no_of_rides)) {
                                                echo $row->no_of_rides;
                                            }
                                            ?>
                                        </td>								
                                        <td class="center" style="width:50px;">
                                            <?php
                                            if (isset($row->cancelled_rides)) {
                                                echo $row->cancelled_rides;
                                            } else {
                                                echo 0;
                                            }
                                            ?>
                                        </td>

                                        <td class="center" style="width:80px;">
                                            <?php if (isset($row->avg_review)) { ?>
                                                <?php if ($row->avg_review != '') { ?>
                                                    <a href="admin/reviews/view_driver_reviews/<?php echo $row->_id; ?>" style="color:blue;" ><?php echo number_format($row->avg_review, 2) . ' (' . $row->total_review . ')'; ?></a>
                                                <?php } else { ?>
                                                    0 (0)
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                0 (0)
                                            <?php } ?>
                                        </td>

                                        <td class="center" style="width:70px;">
                                            <?php
                                            if (isset($row->created)) {
                                                echo date('M d Y', strtotime($row->created));
                                            }
                                            ?>
                                        </td>


                                        <td class="center" style="width:30px;">
                                            <?php
                                          
                                            $current=time()-300;
                                            
                                            if (isset($row->availability)) {
                                                if ($row->availability == 'Yes' && isset($row->last_active_time->sec) && $row->last_active_time->sec > $current) {
                                                    ?>
                                                    <img src="images/status-online.png" />
                                                    <?php
                                                } else {
                                                    ?>
                                                    <img src="images/status-offline.png" />
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <img src="images/status-offline.png" />
                                                <?php
                                            }
                                            ?>
                                        </td>

                                        <td class="center" style="width: 30px;">
                                            <?php
                                                if (isset($row->outstanding_amount['is_due']) && $row->outstanding_amount['is_due'] == true) {
                                                    $blocked = 'YES';
                                                    $class = 'b_done b_warn';
                                                    $title = 'Click to Unblock';
                                                    $unblock_link = "javascript:confirm_unblock('admin/drivers/unblock/" . $row->_id . "')";
                                                } else {
                                                    $blocked = 'NO';
                                                    $class = '';
                                                    $title = '';
                                                    $unblock_link = "javascript:void(0);";
                                                }
                                            ?>
                                            <a title="<?php echo $title; ?>" class="tip_top" href="<?php echo $unblock_link; ?>">
                                                <span class="badge_style <?php echo $class; ?>"><?php echo $blocked; ?></span>
                                            </a>
                                        </td>

                                        <td class="center" style="width:40px;">
												<?php
												$disp_mode = get_language_value_for_keyword($row->mode,$this->data['langCode']);
											if (isset($row->mode)) {
													if($row->mode == 'Booked'){
													$mode = '0';
												?>
												   <a title="<?php if ($this->lang->line('common_click_to_make_available') != '') echo stripslashes($this->lang->line('common_click_to_make_available')); else echo 'Click to make available'; ?>" class="tip_top" href="javascript:confirm_status('admin/drivers/change_driver_mode_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>');">
                                                        <span class="badge_style b_done b_warn"><?php echo $disp_mode; ?></span>
                                                    </a>
												<?php
												} else  {
													echo $disp_mode;
												}
											} 
                                            ?>
                                        </td>
									
                                        <td class="center" style="width:60px;">
                                            <?php
											
											if(isset($row->verify_status)){
												if($row->verify_status==''){
													$verify_status = get_language_value_for_keyword('No',$this->data['langCode']);;
												}else{
													$verify_status = get_language_value_for_keyword($row->verify_status,$this->data['langCode']);;
												}
											}else{
												$verify_status = get_language_value_for_keyword('No',$this->data['langCode']);;
											}
                                            if ($allPrev == '1' || in_array('2', $driver)) {
                                                if ($row->verify_status == 'Yes') {
                                                    $mode = 0;
													$verify_status = get_language_value_for_keyword($row->verify_status,$this->data['langCode']);;
                                                } elseif ($row->verify_status == 'No') {
                                                    $mode = 1;
													
                                                } else {
                                                    $mode = 2;
                                                }
                                                if ($mode == '0') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_inactive') != '') echo stripslashes($this->lang->line('common_click_to_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('admin/drivers/change_driver_vrification_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>');">
                                                        <span class="badge_style b_done"><?php echo $verify_status; ?></span>
                                                    </a>
                                                    <?php
                                                } else if ($mode == '1') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_active') != '') echo stripslashes($this->lang->line('common_click_to_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('admin/drivers/change_driver_vrification_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>')">
                                                        <span class="badge_style"><?php echo $verify_status; ?></span>
                                                    </a>
                                                <?php } else {
                                                    ?>
                                                    <span class="badge_style"><?php echo $verify_status; ?></span>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <span class="badge_style b_done"><?php echo $verify_status; ?></span>
                                            <?php } ?>
                                        </td>

                                        <td class="center" style="width:40px;">
                                            <?php
											$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
                                            if ($allPrev == '1' || in_array('2', $driver)) {
                                                if ($row->status == 'Active') {
                                                    $mode = 0;
                                                } elseif ($row->status == 'Inactive') {
                                                    $mode = 1;
                                                } else {
                                                    $mode = 2;
                                                }
                                                if ($mode == '0') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_inactive') != '') echo stripslashes($this->lang->line('common_click_to_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('admin/drivers/change_driver_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>');">
                                                        <span class="badge_style b_done"><?php echo $disp_status; ?></span>
                                                    </a>
                                                    <?php
                                                } else if ($mode == '1') {
                                                    ?>
                                                    <a title="<?php if ($this->lang->line('common_click_to_active') != '') echo stripslashes($this->lang->line('common_click_to_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('admin/drivers/change_driver_status/<?php echo $mode; ?>/<?php echo $row->_id; ?>')">
                                                        <span class="badge_style"><?php echo $disp_status; ?></span>
                                                    </a>
                                                <?php } else {
                                                    ?>
                                                    <span class="badge_style"><?php echo $disp_status; ?></span>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <span class="badge_style b_done"><?php echo $disp_status; ?></span>
                                            <?php } ?>
                                        </td>
                                        <td class="center" style="width:140px;">
                                            <?php if ($allPrev == '1' || in_array('2', $driver)) { ?>
                                                <span><a class="action-icons c-bank" href="admin/drivers/banking/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('driver_connect_banking') != '') echo stripslashes($this->lang->line('driver_connect_banking')); else echo 'Connect Banking'; ?>">Bank</a></span>
                                                <span><a class="action-icons c-key" href="admin/drivers/change_password_form/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('driver_change_password') != '') echo stripslashes($this->lang->line('driver_change_password')); else echo 'Change Password'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
                                                <span><a class="action-icons c-edit" href="admin/drivers/edit_driver_form/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></span>
                                            <?php } ?>
                                            <span><a class="action-icons c-suspend" href="admin/drivers/view_driver/<?php echo $row->_id; ?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>"></a></span>
                                            <?php if ($allPrev == '1' || in_array('3', $driver)) { ?>	
                                                <?php if ($row->status != 'Deleted') { ?>
                                                    <span><a class="action-icons c-delete" href="javascript:confirm_delete('admin/drivers/delete_driver/<?php echo $row->_id; ?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"></a></span>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="center">
                                    <input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_car_types_name') != '') echo stripslashes($this->lang->line('admin_car_types_name')); else echo 'Name'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_driver_list_req_rcvd') != '') echo stripslashes($this->lang->line('admin_driver_list_req_rcvd')); else echo 'Req Rcvd'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_driver_list_cmpl') != '') echo stripslashes($this->lang->line('admin_driver_list_cmpl')); else echo 'CMPL'; ?>
                                </th>
                                <th>
                                     <?php if ($this->lang->line('admin_driver_list_cxld') != '') echo stripslashes($this->lang->line('admin_driver_list_cxld')); else echo 'CXLD'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_driver_list_avg_count') != '') echo stripslashes($this->lang->line('admin_driver_list_avg_count')); else echo 'Avg(Count)'; ?>
                                </th>
                                <th>
                                     <?php if ($this->lang->line('admin_driver_list_doj') != '') echo stripslashes($this->lang->line('admin_driver_list_doj')); else echo 'DOJ'; ?>
                                </th>
                                <th>
                                     <?php if ($this->lang->line('admin_driver_list_avail') != '') echo stripslashes($this->lang->line('admin_driver_list_avail')); else echo 'AVAIL'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_mode') != '') echo stripslashes($this->lang->line('admin_drivers_mode')); else echo 'Mode'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_drivers_verified') != '') echo stripslashes($this->lang->line('admin_drivers_verified')); else echo 'Verified'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
                                </th>
                                <th>
                                    <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>

                    <?php
                    if ($paginationLink != '') {
                        echo $paginationLink;
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
	<style>										
			.b_warn {
				background: orangered none repeat scroll 0 0;
				border: medium none red;
			}
			
			.filter_widget .btn_30_light {
				margin: -11px;
				width: 83%;
			}
	</style>
<?php
$this->load->view('admin/templates/footer.php');
?>