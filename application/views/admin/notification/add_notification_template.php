<?php
$this->load->view('admin/templates/header.php');
?>
<script>
	$(document).ready(function(){
		$('#notification_type').click(function(){
			$('#notification_type').change(function(){
				if($('#notification_type').val() == 'notification'){
					$('.noti-mail').css('display','none');
					$('.noti-notify').css('display','block');
					$('.noti-sms').css('display','none');
				} else if ($('#notification_type').val() == 'sms') {
					$('.noti-mail').css('display','none');
					$('.noti-sms').css('display','block');
					$('.noti-notify').css('display','none');
				} else {
					$('.noti-mail').css('display','block');
					$('.noti-notify').css('display','none');
					$('.noti-sms').css('display','none');
				}
			});
		});
	
	});
</script>
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
						$attributes = array('class' => 'form_container left_label', 'id' => 'commentForm','enctype' => 'multipart/form-data');
						echo form_open('admin/notification/insertEditNotificationTemplate',$attributes) 
					?>
	 					<ul>
						
							<li>
								<div class="form_grid_12">
									<label class="field_title" for="notification_type"><?php if ($this->lang->line('admin_notification_notify_type') != '') echo stripslashes($this->lang->line('admin_notification_notify_type')); else echo 'Notify Type'; ?><span class="req">*</span></label>
									<div class="form_input">
										<select name="notification_type" id="notification_type" tabindex="1" class="required" style="height: 31px; width: 51%;">
											<option value="email"><?php if ($this->lang->line('admin_notification_e_Mail') != '') echo stripslashes($this->lang->line('admin_notification_e_Mail')); else echo 'E-Mail'; ?></option>
											<option value="notification"><?php if ($this->lang->line('admin_notification_notification') != '') echo stripslashes($this->lang->line('admin_notification_notification')); else echo 'Notification'; ?></option>
											<option value="sms"><?php if ($this->lang->line('admin_notification_sms') != '') echo stripslashes($this->lang->line('admin_notification_sms')); else echo 'SMS'; ?></option>
										</select>
									</div> 
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title" for="news_title"><?php if ($this->lang->line('admin_notification_template_title') != '') echo stripslashes($this->lang->line('admin_notification_template_title')); else echo 'Template Title'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="message[title]" style=" width:51%;" id="news_title" value="" type="text" tabindex="1" class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_name') != '') echo stripslashes($this->lang->line('admin_newsletter_template_name')); else echo 'Please enter the email templete name'; ?>"/>
									</div> 
								</div>
							</li>
							
							<li class="noti-mail">
								<div class="form_grid_12">
									<label class="field_title" for="news_subject"><?php if ($this->lang->line('admin_notification_email_subject') != '') echo stripslashes($this->lang->line('admin_notification_email_subject')); else echo 'Email Subject'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="message[subject]" style=" width:51%;" id="news_subject" type="text" tabindex="2" class="required tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_subject') != '') echo stripslashes($this->lang->line('admin_newsletter_template_subject')); else echo 'Please enter the email templete subject'; ?>"/>
									</div>
								</div>
							</li>
							
                           
							
							<input name="sender[name]" id="sender_name" type="hidden" tabindex="3" value="<?php echo $this->data['title'];?>" class="required tipTop" title="<?php if ($this->lang->line('admin_payment_gateway_sender_name') != '') echo stripslashes($this->lang->line('admin_payment_gateway_sender_name')); else echo 'Please enter the sender name'; ?>"/>
							<input name="sender[email]" id="sender_email" type="hidden" tabindex="4" value="<?php echo $this->config->item('email');?>" class="required tipTop" />
							
                           
								
                            <li class="noti-mail">
								<div class="form_grid_12">
									<label class="field_title" for="news_descrip"><?php if ($this->lang->line('admin_notification_email_description') != '') echo stripslashes($this->lang->line('admin_notification_email_description')); else echo 'Email Description'; ?>  </label>
									<div class="form_input">
										<textarea name="message[mail_description]" style=" width:51%;" class="tipTop mceEditor required" title="<?php if ($this->lang->line('admin_newsletter_template_description') != '') echo stripslashes($this->lang->line('admin_newsletter_template_description')); else echo 'Please enter the email templete description'; ?>"  tabindex=""></textarea>
									</div>
								</div>
							</li>
							
							<li class="noti-notify" style="display:none;">
								<div class="form_grid_12">
									<label class="field_title" for="news_descrip"><?php if ($this->lang->line('admin_notification_notification_description') != '') echo stripslashes($this->lang->line('admin_notification_notification_description')); else echo 'Notification Description'; ?>  </label>
									<div class="form_input">
										<textarea name="message[msg_description]" style=" width:51%; required" class="tipTop" title="<?php if ($this->lang->line('admin_newsletter_template_description') != '') echo stripslashes($this->lang->line('admin_newsletter_template_description')); else echo 'Please enter the email templete description'; ?>"  tabindex=""></textarea>
									</div>
								</div>
							</li>
							
							<li class="noti-notify" style="display:none;">
								<div class="form_grid_12">
									<label class="field_title" for="news_descrip"><?php if ($this->lang->line('admin_notification_notification_image') != '') echo stripslashes($this->lang->line('admin_notification_notification_image')); else echo 'Notification Image'; ?>  </label>
									<div class="form_input">
										<input name="image" class="tipTop" type="file" title="<?php if ($this->lang->line('admin_payment_gateway_notification_image') != '') echo stripslashes($this->lang->line('admin_payment_gateway_notification_image')); else echo 'Please choose the notification image'; ?>"/>
									</div>
								</div>
							</li>
							
							<li class="noti-sms" style="display:none;">
								<div class="form_grid_12">
									<label class="field_title" for="sms_body"><?php if ($this->lang->line('admin_notification_sms_body') != '') echo stripslashes($this->lang->line('admin_notification_sms_body')); else echo 'SMS Body'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<textarea name="message[sms_body]" style=" width:51%; required" class="tipTop required" title="<?php if ($this->lang->line('admin_newsletter_sms_body') != '') echo stripslashes($this->lang->line('admin_newsletter_sms_body')); else echo 'Please enter the SMS Body'; ?>"  tabindex="" maxlength="150"></textarea>
									</div>
								</div>
							</li>

                            <input type="hidden" name="status" id="status" />
							<input type="hidden" name="_id" value=""/>
							
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