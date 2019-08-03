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
                        <div id="widget_tab">
            			</div>
					</div>
					<div class="widget_content">
						<form class="form_container left_label" action="admin/cancellation/insertEditReason" id="addEditcancellation_form" method="post" enctype="multipart/form-data">
							<div>
								<ul>
	<input type="hidden" name="reason_id" id="reason_id" value="<?php if($form_mode){ echo $cancellationdetails->row()->_id; } ?>"  />
	<input type="hidden" name="type" id="type" value="<?php if($form_mode){ echo $cancellationdetails->row()->type; }else{ echo $type; } ?>"  />
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="reason"><?php if ($this->lang->line('admin_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_cancellation_reason')); else echo 'Reason'; ?>  <span class="req">*</span></label>
											<div class="form_input">
												<input name="reason" id="reason" type="text" tabindex="2" class="required large tipTop" title="<?php if ($this->lang->line('admin_enter_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_enter_cancellation_reason')); else echo 'Please enter the Cancellation Reason'; ?>" value="<?php if($form_mode){ echo $cancellationdetails->row()->reason; } ?>"/>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="status"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?>  <span class="req">*</span></label>
											<div class="form_input">
												<div class="active_inactive">
													<input type="checkbox" tabindex="7" name="status"  id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($cancellationdetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?> />
												</div>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<button type="submit" class="btn_small btn_blue" tabindex="15"><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?> </span></button>
											</div>
										</div>
									</li>
									
								</ul>
						   </div>
					   
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