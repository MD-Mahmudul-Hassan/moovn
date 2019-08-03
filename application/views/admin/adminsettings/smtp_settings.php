<?php
$this->load->view('admin/templates/header.php');
 if (is_file('./commonsettings/dectar_smtp_settings.php')){
	include('commonsettings/dectar_smtp_settings.php');
}
?>
<div id="content">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php echo $heading;?></h6>
				</div>
				<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'regitstraion_form');
						echo form_open('admin/adminlogin/save_smtp_settings',$attributes) 
					?>
	 					<ul>
							<li>
								<div class="form_grid_12">
									<label class="field_title" for="smtp_host"><?php if ($this->lang->line('admin_settings_smtp_host') != '') echo stripslashes($this->lang->line('admin_settings_smtp_host')); else echo 'SMTP Host'; ?></label>
									<div class="form_input">
										<input name="smtp[smtp_host]" value="<?php echo $this->config->item('smtp_host');?>" id="smtp_host" type="text" tabindex="1" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_smtp_host') != '') echo stripslashes($this->lang->line('admin_setting_enter_smtp_host')); else echo 'Please enter the smtp host'; ?>"/>
									</div>
                                </div>
							</li>
                                    
                            <li>
								<div class="form_grid_12">
									<label class="field_title" for="smtp_port"><?php if ($this->lang->line('admin_settings_smtp_port') != '') echo stripslashes($this->lang->line('admin_settings_smtp_port')); else echo 'SMTP Port'; ?></label>
									<div class="form_input">
										<input name="smtp[smtp_port]" value="<?php echo $this->config->item('smtp_port');?>" id="smtp_port" type="text" tabindex="2" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_smtp_port') != '') echo stripslashes($this->lang->line('admin_setting_enter_smtp_port')); else echo 'Please enter the smtp port'; ?>"/>
									</div>
								</div>
                            </li>
                                    
                            <li>
								 <div class="form_grid_12">
									 <label class="field_title" for="smtp_user"><?php if ($this->lang->line('admin_settings_smtp_email') != '') echo stripslashes($this->lang->line('admin_settings_smtp_email')); else echo 'SMTP Email'; ?></label>
									 <div class="form_input">
										<input name="smtp[smtp_user]" value="<?php echo $this->config->item('smtp_user');?>" id="smtp_user" type="text" tabindex="3" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_smtp_email_id') != '') echo stripslashes($this->lang->line('admin_setting_enter_smtp_email_id')); else echo 'Please enter the smtp email id'; ?>"/>
									 </div>
								 </div>
                            </li>
                                    
                            <li>
								<div class="form_grid_12">
									<label class="field_title" for="smtp_pass"><?php if ($this->lang->line('admin_settings_smtp_password') != '') echo stripslashes($this->lang->line('admin_settings_smtp_password')); else echo 'SMTP Password'; ?></label>
                                    <div class="form_input">
										<input name="smtp[smtp_pass]" value="<?php echo $this->config->item('smtp_pass');?>" id="smtp_pass" type="password" tabindex="4" class="large tipTop" title="<?php if ($this->lang->line('admin_setting_enter_smtp_password') != '') echo stripslashes($this->lang->line('admin_setting_enter_smtp_password')); else echo 'Please enter the smtp password'; ?>"/>
                                    </div>
								</div>
                            </li>
								
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" tabindex="5"><span><?php if ($this->lang->line('admin_settings_save') != '') echo stripslashes($this->lang->line('admin_settings_save')); else echo 'Save'; ?></span></button>
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
<?php 
$this->load->view('admin/templates/footer.php');
?>