<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
?>
<div id="content" style="clear:both;">
	<div class="grid_container">
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon graph"></span>
					<h6><?php echo $heading;?></h6>
				</div>
				<div class="widget_content">
					<div class="stat_block">
						<h4><?php echo $totalUsersList;?> <?php if ($this->lang->line('admin_users_users_list_users_registered_in_site') != '') echo stripslashes($this->lang->line('admin_users_users_list_users_registered_in_site')); else echo 'users registered in this site'; ?></h4>
						<table>
							<tbody>
								<tr>
									<td>
										<?php if ($this->lang->line('admin_users_users_list_active_users') != '') echo stripslashes($this->lang->line('admin_users_users_list_active_users')); else echo 'Active Users'; ?>
									</td>
									<td>
										<?php echo $totalActiveUser;?>
									</td>
								</tr>
								<tr>
									<td>
										<?php if ($this->lang->line('admin_users_users_list_inactive_users') != '') echo stripslashes($this->lang->line('admin_users_users_list_inactive_users')); else echo 'Inactive Users'; ?>
									</td>
									<td>
										<?php echo $totalInactiveUser;?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="grid_6">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon image_1"></span>
					<h6> <?php if ($this->lang->line('admin_users_recent_users') != '') echo stripslashes($this->lang->line('admin_users_recent_users')); else echo 'Recent Users'; ?></h6>
				</div>
				<div class="widget_content">
					<table class="wtbl_list">
						<thead>
							<tr>
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
									<?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> 
								</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if ($recentusersList->num_rows() > 0){
								foreach($recentusersList->result() as $user){
							?>
							<tr class="tr_even">
								<td>
									 <?php echo $user->user_name;?>
								</td>
								<td>
									 <?php echo $user->email;?>
								</td>
								<td>
									<div class="widget_thumb">
										<?php if ($user->image != ''){?>
											<img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB.$user->image;?>" />
										<?php }else {?>
											<img width="40px" height="40px" src="<?php echo base_url().USER_PROFILE_THUMB_DEFAULT;?>" />
										<?php }?>
									</div>
								</td>
								<td>
									<?php
									$disp_status = get_language_value_for_keyword($user->status,$this->data['langCode']);
									?>
									<?php if (strtolower($user->status) == 'active'){?>
										 <span class="badge_style b_done"><?php echo $disp_status;?></span>
									<?php }else {?>
										 <span class="badge_style b_active"><?php echo $disp_status;?></span>
									<?php }?>
								</td>
							</tr>
							<?php 
									}
							}else {
							?>
							<tr>
								<td colspan="5" align="center"><?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?><?php if ($this->lang->line('admin_users_no_available') != '') echo stripslashes($this->lang->line('admin_users_no_available')); else echo 'No Users Available'; ?></td>
							</tr>
							<?php }?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<span class="clear"></span>
</div>
<?php 
$this->load->view('admin/templates/footer.php');
?>