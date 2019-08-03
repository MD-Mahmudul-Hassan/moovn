<?php
$this->load->view('admin/templates/header.php');
extract($privileges);

$user_type = '';
if($this->input->get('user_type') != ''){
	$user_type = $this->input->get('user_type');
}
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
    $country='';
	<?php if(isset($_GET['country'])) {?>
	$country = "<?php echo $_GET['country']; ?>";
    <?php }?>
	$("#country").attr("disabled", true);
    if($country != ''){
		$('#country').css("display","inline");
       
		$('#country').prop("disabled", false);
	}
	$("#filtertype").change(function(){
        $('#filtervalue').show().prop("disabled", false).val('');
        $('#location_id').hide().prop("disabled", true);
		$filter_val = $(this).val();
        $('#country').css("display","none");
		$("#country").attr("disabled", true);
        if($filter_val == 'phone_number'){
			$('#country').css("display","inline");
            $('#country').prop("disabled", false);			
		}
		else if ($filter_val == 'location') {
            $('#filtervalue').hide().prop("disabled", true);
            $('#location_id').show().prop("disabled", false);
		}		
	});
	
});

$(function (){		
	$("#export_user_list").click(function(event){
		event.preventDefault();
		window.location.href = "admin/users/export_user_list";
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
								
									<h6><?php if ($this->lang->line('admin_users_users_filter') != '') echo stripslashes($this->lang->line('admin_users_users_filter')); else echo 'Users Filter'; ?></h6>
									<div class="btn_30_light">	
									<form method="GET" id="filter_form" action="admin/users/display_user_list" accept-charset="UTF-8">
										<select class="form-control" id="sortby" name="sortby" tabindex="1" style="width:150px;">
											<option value="" data-val=""><?php if ($this->lang->line('admin_driver_select_sort_type') != '') echo stripslashes($this->lang->line('admin_driver_select_sort_type')); else echo 'Select Sort Type'; ?></option>
											<option value="doj_asc" <?php if(isset($sortby)){if($sortby=='doj_asc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_join_date') != '') echo stripslashes($this->lang->line('admin_user_by_join_date')); else echo 'By Joining Date'; ?></option>
											<option value="doj_desc" <?php if(isset($sortby)){if($sortby=='doj_desc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_recently_joined') != '') echo stripslashes($this->lang->line('admin_user_by_recently_joined')); else echo 'By Recently Joined'; ?></option>
											<option value="rides_asc" <?php if(isset($sortby)){if($sortby=='rides_asc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_least_rides') != '') echo stripslashes($this->lang->line('admin_user_by_least_rides')); else echo 'By Least Rides'; ?></option>
											<option value="rides_desc" <?php if(isset($sortby)){if($sortby=='rides_desc'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_user_by_maximum_rides') != '') echo stripslashes($this->lang->line('admin_user_by_maximum_rides')); else echo 'By Maximum Rides'; ?></option>
										</select>
										<select class="form-control" id="filtertype" name="type" tabindex="1">
											<option value="" data-val=""><?php if ($this->lang->line('admin_drivers_select_filter_type') != '') echo stripslashes($this->lang->line('admin_drivers_select_filter_type')); else echo 'Select Filter Type'; ?></option>
											<option value="user_name" data-val="user_name" <?php if(isset($type)){if($type=='user_name'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_users_users_list_user_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_name')); else echo 'User Name'; ?></option>
											<option value="email" data-val="email" <?php if(isset($type)){if($type=='email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_users_users_list_user_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_email')); else echo 'User Email'; ?></option>
											<option value="phone_number" data-val="phone_number" <?php if(isset($type)){if($type=='phone_number'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_users_users_list_user_phonenumber') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_phonenumber')); else echo 'User PhoneNumber'; ?></option>
											<option value="location" data-val="location" <?php if(isset($type)){if($type=='location'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_users_users_list_location') != '') echo stripslashes($this->lang->line('admin_users_users_list_location')); else echo 'Location'; ?></option>
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
                                       	<?php
                                       		if (isset($_GET['type']) && $_GET['type'] == 'location'):
                                       			$input_disabled = 'style="display: none" disabled';
                                       			$select_disabled = '';
                                       		else:
                                       			$input_disabled = '';
                                       			$select_disabled = 'style="display: none" disabled disabled';
                                       		endif;
                                       	?>
										<input name="value" id="filtervalue" type="text" tabindex="2" class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($value)) echo $value; ?>" placeholder="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" <?php echo $input_disabled; ?> />
										
										<select name="value" id="location_id" class="form-control" title="Please choose location" <?php echo $select_disabled; ?>>
										<?php
											foreach ($locationList->result() as $row):
												if ($row->_id == $_GET['value']):
													$location_selected = 'selected';
												else:
													$location_selected = '';
												endif;
												echo '<option value="'. $row->_id .'" '.  $location_selected . '>' . $row->city . '</option>';
											endforeach;
										?>
										</select>

										<input name="user_type" id="user_type" type="hidden"  value="<?php echo $user_type; ?>"/>
                                        
										
										<button type="submit" class="tipTop filterbtn" tabindex="3" original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
											<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_filter') != '') echo stripslashes($this->lang->line('admin_drivers_filter')); else echo 'Filter'; ?></span>
										</button>
										<?php if(isset($filter) && $filter!=""){ ?>
										<a href="admin/users/display_user_list"class="tipTop filterbtn" original-title="<?php if ($this->lang->line('driver_enter_view_all_users') != '') echo stripslashes($this->lang->line('driver_enter_view_all_users')); else echo 'View All Users'; ?>">
											<span class="icon delete_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_drivers_remove_filter') != '') echo stripslashes($this->lang->line('admin_drivers_remove_filter')); else echo 'Remove Filter'; ?></span>
										</a>
										<?php } ?>
										</form>
										<!--<form method="POST" id="export_form" action="admin/export/userlist" accept-charset="UTF-8">
											<button type="submit" class="tipTop filterbtn rgt" tabindex="3" original-title="Export Current User List" >
												<span class="icon export"></span><span class="btn_link">Export</span>
											</button>
											<input type="hidden" name="type" id="filtertype"  value="<?php if(isset($type)) echo $type; ?>"  />
											<input type="hidden" name="value" id="filtervalue" value="<?php if(isset($value)) echo $value; ?>"  />
										</form>-->
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>
		
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open('admin/users/change_user_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
							<?php if ($allPrev == '1' || in_array('2', $user)){?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_active_records') != '') echo stripslashes($this->lang->line('common_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>">
									<span class="icon accept_co"></span>
									<span class="btn_link"><?php if ($this->lang->line('admin_subadmin_active') != '') echo stripslashes($this->lang->line('admin_subadmin_active')); else echo 'Active'; ?></span>
								</a>
							</div>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_inactive_records') != '') echo stripslashes($this->lang->line('common_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>">
									<span class="icon delete_co"></span>
									<span class="btn_link"><?php if ($this->lang->line('admin_subadmin_inactive') != '') echo stripslashes($this->lang->line('admin_subadmin_inactive')); else echo 'Inactive'; ?></span>
								</a>
							</div>
							<?php 
							}
							if ($allPrev == '1' || in_array('3', $user)){
							?>
							<div class="btn_30_light" style="height: 29px;">
								<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>">
									<span class="icon cross_co"></span>
									<span class="btn_link"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></span>
								</a>
							</div>
							<?php }?>

							<div class="btn_30_light">								
								<a href="javascript:void(0)" class="tipTop" id="export_user_list">									
									<span class="btn_link">Export</span>
								</a>
							</div>

						</div>
					</div>
					
					<div class="widget_content">
						<?php if($paginationLink != '') { echo $paginationLink; $tble = 'userListTblCustom'; } else { $tble='userListTbl';}?>
					
						<table class="display" id="<?php echo $tble; ?>">
							<thead>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_users_users_list_user_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_name')); else echo 'User Name'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_users_users_list_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_email')); else echo 'Email'; ?> 
									</th>
									<th style="width:90px !important">
										<?php if ($this->lang->line('admin_users_users_list_thumbnail') != '') echo stripslashes($this->lang->line('admin_users_users_list_thumbnail')); else echo 'Thumbnail'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_users_users_list_ratings') != '') echo stripslashes($this->lang->line('admin_users_users_list_ratings')); else echo 'Ratings'; ?>
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
								if ($usersList->num_rows() > 0){
									foreach ($usersList->result() as $row){
								?>
								<tr>
									<td class="center tr_select ">
										<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
									</td>
									<td class="center">
										<?php echo $row->user_name;?>
									</td>
									<td class="center">
										<?php if($isDemo){ ?>
										<?php echo $dEmail; ?>
										<?php }  else{ ?>
										<?php echo $row->email;?>
										<?php } ?>
									</td>
									<td class="center">
										<div class="widget_thumb">
											<?php if ($row->image != ''){?>
											 <img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB.$row->image;?>" />
											<?php }else {?>
											 <img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB_DEFAULT;?>" />
											<?php }?>
										</div>
									</td>
									
									<td class="center">
									
									<?php if(isset($row->avg_review)) { ?>
										<a href="admin/reviews/view_user_reviews/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('user_view_review_details') != '') echo stripslashes($this->lang->line('user_view_review_details')); else echo 'View review details'; ?>" class="tip_top"style="color:blue;"><?php echo $row->avg_review; ?> (<?php echo $row->total_review; ?>) </a>
									<?php }  else { ?>
										<a> 0 (0) </a>
									<?php } ?>
									
									</td>
									
									<td class="center">
										<?php 
										$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
										if ($allPrev == '1' || in_array('2', $user)){
											if($row->status == 'Active'){
												$mode = 0;
											}elseif($row->status == 'Inactive'){
												$mode = 1;
											}else{
												$mode = 2;
											}
											if ($mode == '0'){
										?>
											<a title="<?php if ($this->lang->line('common_click_inactive') != '') echo stripslashes($this->lang->line('common_click_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('admin/users/change_user_status/<?php echo $mode;?>/<?php echo $row->_id;?>');">
												<span class="badge_style b_done"><?php echo $disp_status;?></span>
											</a>
										<?php
											}else if ($mode == '1'){ 	
										?>
											<a title="<?php if ($this->lang->line('common_click_active') != '') echo stripslashes($this->lang->line('common_click_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('admin/users/change_user_status/<?php echo $mode;?>/<?php echo $row->_id;?>')">
												<span class="badge_style"><?php echo $disp_status;?></span>
											</a>
										<?php 
											}else{ ?>
												<span class="badge_style"><?php echo $disp_status;?></span>
											<?php }
										}else {
										?>
										<span class="badge_style b_done"><?php echo $disp_status;?></span>
										<?php }?>
									</td>
									<td class="center">
										<?php if (($allPrev == '1' || in_array('2', $user)) && $user_type != 'deleted'){  ?>
											
										
										<span><a class="action-icons c-money" href="admin/users/view_wallet/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_money') != '') echo stripslashes($this->lang->line('admin_common_money')); else echo 'Money'; ?>" onclick="manage_wallet('<?php echo (string)$row->_id;?>','<?php echo $row->email;?>');" data-content="manage_wallet"><?php if ($this->lang->line('admin_common_wallet_money') != '') echo stripslashes($this->lang->line('admin_common_wallet_money')); else echo 'Wallet Money'; ?></a></span>
									
											
											<span><a class="action-icons c-key" href="admin/users/change_password_form/<?php echo $row->_id;?>" original-title="<?php if ($this->lang->line('admin_user_change_password') != '') echo stripslashes($this->lang->line('admin_user_change_password')); else echo 'Change Password'; ?>"><?php if ($this->lang->line('admin_user_change_password') != '') echo stripslashes($this->lang->line('admin_user_change_password')); else echo 'Change Password'; ?></a></span>
										
											<span><a class="action-icons c-edit" href="admin/users/edit_user_form/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?>"><?php if ($this->lang->line('admin_subadmin_edit') != '') echo stripslashes($this->lang->line('admin_subadmin_edit')); else echo 'Edit'; ?></a></span>
										<?php }?>
                                            <?php if($user_type == 'deleted') { ?>
                                            <span><a class="action-icons c-hourly" href="javascript:confirm_status('admin/users/change_user_status/1/<?php echo $row->_id;?>');" title="<?php if ($this->lang->line('admin_user_userlist_restore') != '') echo stripslashes($this->lang->line('admin_user_userlist_restore')); else echo 'Restore'; ?>"><?php if ($this->lang->line('admin_user_userlist_restore') != '') echo stripslashes($this->lang->line('admin_user_userlist_restore')); else echo 'Restore'; ?></a></span>
                                            <?php }?>
											<span><a class="action-icons c-suspend" href="admin/users/view_user/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_common_view') != '') echo stripslashes($this->lang->line('admin_common_view')); else echo 'View'; ?>"><?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?></a></span>
										<?php if ($allPrev == '1' || in_array('3', $user)){?>	
										<?php if($row->status != 'Deleted'){  ?>
											<span><a class="action-icons c-delete" href="javascript:confirm_delete('admin/users/delete_user/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></a></span>
										<?php } else if($user_type == 'deleted'){ ?>
										
										<span><a class="action-icons c-delete" href="javascript:confirm_delete('admin/users/delete_user_permanently/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></a></span>
										
										
										<?php } } ?>
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
										 <?php if ($this->lang->line('admin_users_users_list_user_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_name')); else echo 'User Name'; ?>
									</th>
									<th>
										 <?php if ($this->lang->line('admin_users_users_list_email') != '') echo stripslashes($this->lang->line('admin_users_users_list_email')); else echo 'Email'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_users_users_list_thumbnail') != '') echo stripslashes($this->lang->line('admin_users_users_list_thumbnail')); else echo 'Thumbnail'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_users_users_list_ratings') != '') echo stripslashes($this->lang->line('admin_users_users_list_ratings')); else echo 'Ratings'; ?>
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
						
							<?php if($paginationLink != '') { echo $paginationLink; } ?>
						
					</div>
				</div>
			</div>
			<input type="hidden" name="statusMode" id="statusMode"/>
			<input type="hidden" name="SubAdminEmail" id="SubAdminEmail"/>
		</form>	
	</div>
	<span class="clear"></span>
	<style>
	.filter_widget .btn_30_light {
		margin: -11px;
		width: 85%;
	}
	</style>
</div>
<?php 
$this->load->view('admin/templates/footer.php');
?>