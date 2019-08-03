<?php
$this->load->view('admin/templates/header.php');
?>
<div id="content">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php if ($this->lang->line('admin_templates_sms_templates') != '') echo stripslashes($this->lang->line('admin_templates_sms_templates')); else echo 'SMS Templates'; ?></h6>
				</div>
				<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'smsForm');
						echo form_open('admin/templates/insertEditSMStemplate',$attributes) 
					?>
	 					<ul>							
                            <li>
								<div class="form_grid_12">
									<label class="field_title" for="req_created"><?php if ($this->lang->line('admin_templates_sms_request_created') != '') echo stripslashes($this->lang->line('admin_templates_sms_request_created')); else echo 'Sms Request Created'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<textarea name="req_created" id="req_created" class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_notify_admin_sms_created') != '') echo stripslashes($this->lang->line('admin_newsletter_notify_admin_sms_created')); else echo 'This Template will be used to notify admin by SMS when a new request is created'; ?>"  ></textarea>
									</div>
								</div>
							</li>
                            <li>
								<div class="form_grid_12">
									<label class="field_title" for="driver_accept"><?php if ($this->lang->line('admin_templates_sms_when_driver_accepts') != '') echo stripslashes($this->lang->line('admin_templates_sms_when_driver_accepts')); else echo 'Sms When Driver Accepts'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<textarea name="driver_accept" id="driver_accept" class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_notify_admin_sms_driver') != '') echo stripslashes($this->lang->line('admin_newsletter_notify_admin_sms_driver')); else echo 'This Template will be used to notify user by SMS when a driver accepts request'; ?>"  ></textarea>
									</div>
								</div>
							</li>
                            <li>
								<div class="form_grid_12">
									<label class="field_title" for="driver_arrives"><?php if ($this->lang->line('admin_templates_sms_when_driver_arrives') != '') echo stripslashes($this->lang->line('admin_templates_sms_when_driver_arrives')); else echo 'Sms When Driver Arrives'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<textarea name="driver_arrives" id="driver_arrives" class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_notify_admin_sms_driver_arrives') != '') echo stripslashes($this->lang->line('admin_newsletter_notify_admin_sms_driver_arrives')); else echo 'This Template will be used to notify user by SMS when a driver the arrives'; ?>"  ></textarea>
									</div>
								</div>
							</li>
                            <li>
								<div class="form_grid_12">
									<label class="field_title" for="news_descrip"><?php if ($this->lang->line('admin_templates_sms_when_driver_completes_job') != '') echo stripslashes($this->lang->line('admin_templates_sms_when_driver_completes_job')); else echo 'Sms When Driver Completes Job'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<textarea name="message" class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_notify_admin_sms_driver_completes_service') != '') echo stripslashes($this->lang->line('admin_newsletter_notify_admin_sms_driver_completes_service')); else echo 'This Template will be used to notify user by SMS when a driver completes the service'; ?>"  ></textarea>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" tabindex="6"><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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