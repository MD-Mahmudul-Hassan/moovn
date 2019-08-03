<?php
$this->load->view('admin/templates/header.php');
?>
<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addsubadmin_form');
						echo form_open('admin/subadmin/insertEditSubadmin',$attributes) 
					?>
	 						<ul>
	 							<li>
								<div class="form_grid_12">
									<label class="field_title" for="email"><?php if ($this->lang->line('admin_subadmin_email_address') != '') echo stripslashes($this->lang->line('admin_subadmin_email_address')); else echo 'Email Address'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="email" id="email" type="text" tabindex="1" class="required large tipTop" title="<?php if ($this->lang->line('admin_subadmin_please_enter_admin_email_address') != '') echo stripslashes($this->lang->line('admin_subadmin_please_enter_admin_email_address')); else echo 'Please enter the admin email address'; ?>"/>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="admin_name"><?php if ($this->lang->line('admin_subadmin_sub_admin_name') != '') echo stripslashes($this->lang->line('admin_subadmin_sub_admin_name')); else echo 'Sub Admin Name'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="admin_name" id="admin_name" type="text" tabindex="2" class="required large tipTop" title="<?php if ($this->lang->line('admin_subadmin_please_enter_admin_username') != '') echo stripslashes($this->lang->line('admin_subadmin_please_enter_admin_username')); else echo 'Please enter the admin username'; ?>"/>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="site_contact_mail"><?php if ($this->lang->line('admin_subadmin_password') != '') echo stripslashes($this->lang->line('admin_subadmin_password')); else echo 'Password'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="admin_password" id="admin_password" type="password" tabindex="3" class="required large tipTop" title="<?php if ($this->lang->line('admin_subadmin_please_enter_admin_password') != '') echo stripslashes($this->lang->line('admin_subadmin_please_enter_admin_password')); else echo 'Please enter the new password'; ?>"/>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="site_contact_mail"></label>
									<div id="uniform-undefined" class="form_input checker focus">
										<span class="" style="float:left;"><input type="checkbox" class="checkbox" id="selectallseeker" /></span><label style="float:left;margin:5px;"><?php if ($this->lang->line('admin_subadmin_select_all') != '') echo stripslashes($this->lang->line('admin_subadmin_select_all')); else echo 'Select all'; ?></label>
									</div>
								</div>
								<div style="margin-top: 20px;"></div>
								<div class="form_grid_12">
									<label class="field_title"><?php if ($this->lang->line('admin_subadmin_mangement_name') != '') echo stripslashes($this->lang->line('admin_subadmin_mangement_name')); else echo 'Management Name'; ?></label>
									<table border="0" cellspacing="0" cellpadding="0" width="400">
								     	<tr>
								            <td align="center" width="15%"><?php if ($this->lang->line('admin_subadmin_view') != '') echo stripslashes($this->lang->line('admin_subadmin_view')); else echo 'View'; ?></td>
								             <td align="center" width="15%"><?php if ($this->lang->line('admin_subadmin_add') != '') echo stripslashes($this->lang->line('admin_subadmin_add')); else echo 'Add'; ?></td>
								              <td align="center" width="15%"><?php if ($this->lang->line('admin_subadmin_edit') != '') echo stripslashes($this->lang->line('admin_subadmin_edit')); else echo 'Edit'; ?></td>
								               <td align="center" width="15%"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></td>
								        </tr>
								    </table>
								</div>
								<?php  
								for($i=0;$i<sizeof($adminPrevArr); $i++) {
									$subAdmin = $adminPrevArr[$i]; 
									$disp_subAdmin = get_language_value_for_keyword($subAdmin,$this->data['langCode']);
								?>
								<div class="form_grid_12">
									<label class="field_title"><?php echo $disp_subAdmin; ?></label>
									<table border="0" cellspacing="0" cellpadding="0" width="400">
								     	<tr>
								        	<?php for($j=0;$j<4; $j++) { ?>
								        	<td align="center" width="15%">
								        		<span class="checkboxCon">
									        		<input class="caseSeeker" type="checkbox" name="<?php echo $subAdmin.'[]';?>" id="<?php echo $subAdmin.'[]';?>"  value="<?php echo $j;?>" />
								        		</span>
								        	</td>
											<?php } ?>
								        </tr>
								    </table>
								</div>
								<?php } ?>
								</li>
								<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" tabindex="15"><span><?php if ($this->lang->line('admin_subadmin_submit') != '') echo stripslashes($this->lang->line('admin_subadmin_submit')); else echo 'Submit'; ?></span></button>
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
$this->load->view('admin/templates/footer.php');
?>