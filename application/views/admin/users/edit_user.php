<?php
$this->load->view('admin/templates/header.php');
?>
<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('admin_users_edit_users') != '') echo stripslashes($this->lang->line('admin_users_edit_users')); else echo 'Edit User'; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'edituser_form', 'enctype' => 'multipart/form-data');
						echo form_open_multipart('admin/users/update_user_details',$attributes) 
					?>
	 						<ul>
	 							<li>
								<div class="form_grid_12">
									<label class="field_title" for="user_name"><?php if ($this->lang->line('admin_users_users_list_user_name') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_name')); else echo 'User Name'; ?> </label>
									<div class="form_input">
										<?php echo $user_details->row()->user_name;?>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="email"><?php if ($this->lang->line('admin_subadmin_email_address') != '') echo stripslashes($this->lang->line('admin_subadmin_email_address')); else echo 'Email Address'; ?> </label>
									<div class="form_input">
										<?php if($isDemo){ ?>
										<?php echo $dEmail; ?>
										<?php }  else{ ?>
										<?php echo $user_details->row()->email;?>
										<?php } ?>										
									</div>
								</div>
								</li>
	 							
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="thumbnail"><?php if ($this->lang->line('admin_users_users_list_user_image') != '') echo stripslashes($this->lang->line('admin_users_users_list_user_image')); else echo 'User Image'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="thumbnail" id="thumbnail" type="file" tabindex="7" class="large tipTop" title="<?php if ($this->lang->line('user_select_user_image') != '') echo stripslashes($this->lang->line('user_select_user_image')); else echo 'Please select user image'; ?>"/>
									</div>
									<div class="form_input">
									<?php if($user_details->row()->image != ''){ ?>
										<img src="<?php echo base_url().USER_PROFILE_IMAGE.$user_details->row()->image;?>" width="100px"/>
									<?php } else {  ?>
										<img src="<?php echo base_url().USER_PROFILE_IMAGE_DEFAULT;?>" width="100px"/>
									<?php } ?>
									</div>
								</div>
								</li>
								
								<input type="hidden" value="<?php echo $user_details->row()->_id;?>" name="user_id" />
								
								<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" tabindex="4"><span><?php if ($this->lang->line('admin_subadmin_update') != '') echo stripslashes($this->lang->line('admin_subadmin_update')); else echo 'Update'; ?></span></button>
									</div>
								</div>
								</li>
							</ul>
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
</div>
<?php 
$this->load->view('admin/templates/footer.php'); //echo $this->lang->line('admin_common_select_file');echo "dffgdusgfjjjjjj";
?>
<script>
/*
$( "input:file" ).inputFileText({
   text: '<?php if ($this->lang->line('admin_common_select_file') != '') echo stripslashes($this->lang->line('admin_common_select_file')); else echo 'Choose File'; ?>'
 
});*/
/*
$('#thumbnail').inputFileText({
      text: '<?php if ($this->lang->line('admin_common_select_file') != '') echo stripslashes($this->lang->line('admin_common_select_file')); else echo 'Choose File'; ?>'
    });*/

</script>