<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
?>
<div id="content">
	<div class="grid_container">
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open('admin/location/change_location_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
						<div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
							<?php 
							if ($allPrev == '1' || in_array('2', $location)){
							?>
								<div class="btn_30_light" style="height: 29px;">
									<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Active','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_active_records') != '') echo stripslashes($this->lang->line('common_select_active_records')); else echo 'Select any checkbox and click here to active records'; ?>">
										<span class="icon accept_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_subadmin_active') != '') echo stripslashes($this->lang->line('admin_subadmin_active')); else echo 'Active'; ?></span>
									</a>
								</div>
								<div class="btn_30_light" style="height: 29px;">
									<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Inactive','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_inactive_records') != '') echo stripslashes($this->lang->line('common_select_inactive_records')); else echo 'Select any checkbox and click here to inactive records'; ?>">
										<span class="icon delete_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_subadmin_inactive') != '') echo stripslashes($this->lang->line('admin_subadmin_inactive')); else echo 'Inactive'; ?></span>
									</a>
								</div>
							<?php 
							}
							if ($allPrev == '1' || in_array('3', $location)){
							?>
								<div class="btn_30_light" style="height: 29px;">
									<a href="javascript:void(0)" onclick="return checkBoxValidationAdmin('Delete','<?php echo $subAdminMail; ?>');" class="tipTop" title="<?php if ($this->lang->line('common_select_delete_records') != '') echo stripslashes($this->lang->line('common_select_delete_records')); else echo 'Select any checkbox and click here to delete records'; ?>">
										<span class="icon cross_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></span>
									</a>
								</div>
							<?php 
							}
							?>
						</div>
					</div>
					<div class="widget_content">
						<table class="display display_tbl" id="location_tbl">
							<thead>
								<tr>
									<th class="center">
										<input name="checkbox_id[]" type="checkbox" value="on" class="checkall">
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
									
										<?php if ($this->lang->line('admin_location_and_fare_city') != '') echo stripslashes($this->lang->line('admin_location_and_fare_city')); else echo 'City'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
									
										<?php if ($this->lang->line('admin_location_and_fare_country') != '') echo stripslashes($this->lang->line('admin_location_and_fare_country')); else echo 'Country'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
									
										<?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
									
										<?php if ($this->lang->line('admin_location_and_fare_fare_details') != '') echo stripslashes($this->lang->line('admin_location_and_fare_fare_details')); else echo 'Fare Details'; ?>
									</th>
									<th>
										 <?php if ($this->lang->line('admin_subadmin_action') != '') echo stripslashes($this->lang->line('admin_subadmin_action')); else echo 'Action'; ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								if ($locationList->num_rows() > 0){
									foreach ($locationList->result() as $row){
								?>
								<tr>
									<td class="center tr_select ">
										<input name="checkbox_id[]" type="checkbox" value="<?php echo $row->_id;?>">
									</td>
									<td class="center">
										<?php echo $row->city;?>
									</td>
									<td class="center">
										<?php echo $row->country['name'];?>
									</td>
									<td class="center">
										<?php 
										$disp_status = get_language_value_for_keyword($row->status,$this->data['langCode']);
										if ($allPrev == '1' || in_array('2', $location)){
											if($row->status=='Active' || $row->status=='Inactive'){
												$mode = ($row->status == 'Active')?'0':'1';
											}else{
												$mode='2';
											}
											if ($mode == '0'){
										?>
											<a title="<?php if ($this->lang->line('common_click_inactive') != '') echo stripslashes($this->lang->line('common_click_inactive')); else echo 'Click to inactive'; ?>" class="tip_top" href="javascript:confirm_status('admin/location/change_location_status/<?php echo $mode;?>/<?php echo $row->_id;?>');">
												<span class="badge_style b_done"><?php echo $disp_status;?></span>
											</a>
										<?php
											}else if ($mode == '1'){	
										?>
											<a title="<?php if ($this->lang->line('common_click_active') != '') echo stripslashes($this->lang->line('common_click_active')); else echo 'Click to active'; ?>" class="tip_top" href="javascript:confirm_status('admin/location/change_location_status/<?php echo $mode;?>/<?php echo $row->_id;?>')">
												<span class="badge_style"><?php echo $disp_status;?></span>
											</a>
										<?php 
											}else{
										?>
											<span class="badge_style b_pending"><?php echo $disp_status;?></span>
										<?php 
											}
										}else {
										?>
										<span class="badge_style b_done"><?php echo $disp_status;?></span>
										<?php }?>
									</td>
									<td class="center">
										<div class="btn_30_light" style="height: 29px;">
											<a class="" href="admin/location/location_fare/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_location_and_fare_update_fare') != '') echo stripslashes($this->lang->line('admin_location_and_fare_update_fare')); else echo 'Update Fare'; ?>">
												<span class="action-icons c-money-white"></span><span class="btn_link"><?php if ($this->lang->line('admin_location_and_fare_update_fare') != '') echo stripslashes($this->lang->line('admin_location_and_fare_update_fare')); else echo 'Update Fare'; ?></span>
											</a>
										</div>
									</td>
									<td class="center">
										<span>
											<a class="action-icons c-suspend" href="admin/location/view_location/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?>">
												<?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?>
											</a>
										</span>
										<?php if ($allPrev == '1' || in_array('2', $location)){?>
											<?php /* <span>
												<a class="action-icons c-money" href="admin/location/location_fare/<?php echo $row->_id;?>" title="Update Fare">
													Fare
												</a>
											</span> */ ?>
											<span>
												<a class="action-icons c-edit" href="admin/location/add_edit_location/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_subadmin_edit') != '') echo stripslashes($this->lang->line('admin_subadmin_edit')); else echo 'Edit'; ?>">
													<?php if ($this->lang->line('admin_subadmin_edit') != '') echo stripslashes($this->lang->line('admin_subadmin_edit')); else echo 'Edit'; ?>
												</a>
											</span>
											<span>
												<a class="action-icons c-copy" href="admin/location/copy_location/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_location_and_fare_copy_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_copy_location')); else echo 'Copy Location'; ?>">
													<?php if ($this->lang->line('admin_location_and_fare_copy_location') != '') echo stripslashes($this->lang->line('admin_location_and_fare_copy_location')); else echo 'Copy Location'; ?>
												</a>
											</span>
											<span>
												<a class="action-icons c-map-marker" href="admin/location/update_location_geo_points/<?php echo $row->_id;?>" title="<?php if ($this->lang->line('admin_location_and_fare_update_boundary') != '') echo stripslashes($this->lang->line('admin_location_and_fare_update_boundary')); else echo 'Update boundary'; ?>">
													<?php if ($this->lang->line('admin_location_and_fare_update_boundary') != '') echo stripslashes($this->lang->line('admin_location_and_fare_update_boundary')); else echo 'Update boundary'; ?>
												</a>
											</span>
										<?php }?>
										<?php if ($allPrev == '1' || in_array('3', $location)){?>	
											<span>
												<a class="action-icons c-delete" href="javascript:confirm_delete('admin/location/delete_location/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?>">
													<?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?>
												</a>
											</span>
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
										<?php if ($this->lang->line('admin_location_and_fare_city') != '') echo stripslashes($this->lang->line('admin_location_and_fare_city')); else echo 'City'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_location_and_fare_country') != '') echo stripslashes($this->lang->line('admin_location_and_fare_country')); else echo 'Country'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_location_and_fare_fare_details') != '') echo stripslashes($this->lang->line('admin_location_and_fare_fare_details')); else echo 'Fare Details'; ?>
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
		</form>	
	</div>
	<span class="clear"></span>
</div>
<style>
.c-map-marker {
    background: rgba(0, 0, 0, 0) url("images/map_loc_icon.png") no-repeat scroll 0 0;
}

</style>
<?php 
$this->load->view('admin/templates/footer.php');
?>