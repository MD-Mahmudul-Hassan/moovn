<?php

$this->load->view('admin/templates/header.php');
extract($privileges);
?>
<div id="content">
	<div class="grid_container">
     <div class="grid_12">
			<div class="">
					<div class="widget_content">
						<span class="clear"></span>						
						<div class="">
							<div class=" filter_wrap">
								<div class="widget_top filter_widget">
								
									<h6><?php if ($this->lang->line('admin_referral_history_members_filter') != '') echo stripslashes($this->lang->line('admin_referral_history_members_filter')); else echo 'Members Filter'; ?></h6>
									<div class="btn_30_light">	
									<form method="GET" id="filter_form" action="admin/referral/display_driver_referrals" accept-charset="UTF-8">	
										<select class="form-control" id="filtertype" name="type" tabindex="1">
											<option value="" data-val=""><?php if ($this->lang->line('admin_notification_select_filter_type') != '') echo stripslashes($this->lang->line('admin_notification_select_filter_type')); else echo 'Select Filter Type'; ?></option>
											<option value="driver_name" data-val="driver_name" <?php if(isset($type)){if($type=='driver_name'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_notification_driver_name') != '') echo stripslashes($this->lang->line('admin_notification_driver_name')); else echo 'Driver Name'; ?></option>
											<option value="email" data-val="email" <?php if(isset($type)){if($type=='email'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_notification_driver_email') != '') echo stripslashes($this->lang->line('admin_notification_driver_email')); else echo 'Driver Email'; ?></option>
											<option value="mobile_number" data-val="mobile_number" <?php if(isset($type)){if($type=='mobile_number'){ echo 'selected="selected"'; } }?>><?php if ($this->lang->line('admin_notification_driver_phoneNumber') != '') echo stripslashes($this->lang->line('admin_notification_driver_phoneNumber')); else echo 'Driver PhoneNumber'; ?></option>
										</select>
										
										<input name="value" id="filtervalue" type="text" tabindex="2" class="tipTop" title="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" value="<?php if(isset($value)) echo $value; ?>" placeholder="<?php if ($this->lang->line('driver_enter_keyword') != '') echo stripslashes($this->lang->line('driver_enter_keyword')); else echo 'Please enter keyword'; ?>" />
										
										<button type="submit" class="tipTop filterbtn" tabindex="3" original-title="<?php if ($this->lang->line('driver_enter_keyword_filter') != '') echo stripslashes($this->lang->line('driver_enter_keyword_filter')); else echo 'Select filter type and enter keyword to filter'; ?>">
											<span class="icon search"></span><span class="btn_link"><?php if ($this->lang->line('admin_notification_filter') != '') echo stripslashes($this->lang->line('admin_notification_filter')); else echo 'Filter'; ?></span>
										</button>
										<?php if(isset($filter) && $filter!=""){ ?>
										<a href="admin/referral/display_driver_referrals"class="tipTop filterbtn" original-title="<?php if ($this->lang->line('driver_enter_view_all_users') != '') echo stripslashes($this->lang->line('driver_enter_view_all_users')); else echo 'View All Users'; ?>">
											<span class="icon delete_co"></span><span class="btn_link"><?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?></span>
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
			echo form_open('admin/referral/change_driver_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
					</div>
					<div class="widget_content">
						<?php if($paginationLink != '') { echo $paginationLink; $tble = 'alldriverListTbl'; } else { $tble='userListTbl';}?>
					
						<table class="display" id="<?php echo $tble; ?>">
							<thead>
								<tr>
									<th class="center">
										<?php if ($this->lang->line('admin_s_no') != '') echo stripslashes($this->lang->line('admin_s_no')); else echo 'S.No'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_notification_user_name') != '') echo stripslashes($this->lang->line('admin_notification_user_name')); else echo 'User Name'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_notification_email') != '') echo stripslashes($this->lang->line('admin_notification_email')); else echo 'Email'; ?>
									</th>
									<th style="width:90px !important">
										<?php if ($this->lang->line('admin_referral_history_thumbnail') != '') echo stripslashes($this->lang->line('admin_referral_history_thumbnail')); else echo 'Thumbnail'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_referral_history_referral') != '') echo stripslashes($this->lang->line('admin_referral_history_referral')); else echo 'Referral Count'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_referral_history_referral_code') != '') echo stripslashes($this->lang->line('admin_referral_history_referral_code')); else echo 'Referral Code'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 1;
								if ($driversList->num_rows() > 0){
									foreach ($driversList->result() as $row){
								?>
								<tr>
									<td class="center tr_select ">
										<?php echo $i ; $i++; ?>
									</td>
									
									<td class="center">
										<?php echo $row->driver_name;?>
									</td>
									
									<td class="center">
										<?php echo $row->email;?>
									</td>		
									<td class="center" style="width:90px;">
										<div class="widget_thumb">
											<?php if ($row->image != ''){?>
											 <img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB.$row->image;?>" />
											<?php }else {?>
											 <img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB_DEFAULT;?>" />
											<?php }?>
										</div>
									</td>								
									<td class="center">
										<?php if(isset($row->referral_count)) echo $row->referral_count; else echo '0'; ?>
									</td>
									
									<td class="center">
										<?php echo $row->unique_code;  ?>
									</td>
									
									<td class="center">
										<ul class="action_list">
											<li style="width:100%;">
												<a href="admin/referral/view_referral_details/<?php echo (string)$row->_id;?>?q=driver" class="p_edit tipTop" original-title="<?php if ($this->lang->line('admin_referral_history_view_details') != '') echo stripslashes($this->lang->line('admin_referral_history_view_details')); else echo 'View Details'; ?>">
												<?php if ($this->lang->line('admin_referral_history_view_details') != '') echo stripslashes($this->lang->line('admin_referral_history_view_details')); else echo 'View Details'; ?>
												</a>
											</li>
										</ul>
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
										<?php if ($this->lang->line('admin_s_no') != '') echo stripslashes($this->lang->line('admin_s_no')); else echo 'S.No'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										 <?php if ($this->lang->line('admin_notification_user_name') != '') echo stripslashes($this->lang->line('admin_notification_user_name')); else echo 'User Name'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_notification_email') != '') echo stripslashes($this->lang->line('admin_notification_email')); else echo 'Email'; ?> 
									</th>
									<th style="width:90px !important">
										<?php if ($this->lang->line('admin_referral_history_thumbnail') != '') echo stripslashes($this->lang->line('admin_referral_history_thumbnail')); else echo 'Thumbnail'; ?>
									</th>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
										<?php if ($this->lang->line('admin_referral_history_referral_count') != '') echo stripslashes($this->lang->line('admin_referral_history_referral_count')); else echo 'Referral Count'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_referral_history_referral_code') != '') echo stripslashes($this->lang->line('admin_referral_history_referral_code')); else echo 'Referral Code'; ?>
									</th>
									<th>
										<?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?>
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
</div>

<style>
.filterbtn {
    background-color: #a7a9ac !important;
    border: 1px solid #e0761a;
    color: #000;
    cursor: pointer;
    height: 29px;
    margin-bottom: 3px;
    vertical-align: middle;
}
</style>

<?php 
$this->load->view('admin/templates/footer.php');
?>