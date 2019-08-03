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
						<form class="form_container left_label" action="admin/documents/insertEditDocument" id="addEditvehicle_form" method="post" enctype="multipart/form-data">
							<div>
								<ul>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="vehicle_type"><?php if ($this->lang->line('admin_document_document_category') != '') echo stripslashes($this->lang->line('admin_document_document_category')); else echo 'Document Category'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<select class="chzn-select required" name="category" tabindex="1" style="width: 375px; display: none;" data-placeholder="Select Document">
													<option value="Driver" <?php if($form_mode){ if ($documentdetails->row()->category == 'Driver'){echo 'selected="selected"';} } ?>><?php if ($this->lang->line('admin_document_for_driver') != '') echo stripslashes($this->lang->line('admin_document_for_driver')); else echo 'For Driver'; ?></option>
													<option value="Vehicle" <?php if($form_mode){ if ($documentdetails->row()->category == 'Vehicle'){echo 'selected="selected"';} } ?>><?php if ($this->lang->line('admin_document_for_vehicle') != '') echo stripslashes($this->lang->line('admin_document_for_vehicle')); else echo 'For Vehicle'; ?></option>
												</select>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="name"><?php if ($this->lang->line('admin_document_document_name') != '') echo stripslashes($this->lang->line('admin_document_document_name')); else echo 'Document Name'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<input name="name" id="name" type="text" tabindex="2" class="large tipTop required" title="<?php if ($this->lang->line('admin_review_enter_document_name') != '') echo stripslashes($this->lang->line('admin_review_enter_document_name')); else echo 'Please enter document name'; ?>" value="<?php if($form_mode){ echo $documentdetails->row()->name; } ?>"/>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="hasExp"><?php if ($this->lang->line('admin_document_has_expiry_date') != '') echo stripslashes($this->lang->line('admin_document_has_expiry_date')); else echo 'Has Expiry Date'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<div class="yes_no">
													<input type="checkbox" tabindex="3" name="hasExp"  id="yes_no_yes" class="yes_no" <?php if($form_mode){ if ($documentdetails->row()->hasExp == 'Yes'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?> />
												</div>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="hasReq"><?php if ($this->lang->line('admin_document_has_required') != '') echo stripslashes($this->lang->line('admin_document_has_required')); else echo 'Has Required'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<div class="yes_no">
													<input type="checkbox" tabindex="4" name="hasReq"  id="yes_no_yes" class="yes_no" <?php if($form_mode){ if ($documentdetails->row()->hasReq == 'Yes'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?> />
												</div>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<label class="field_title" for="status"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> <span class="req">*</span></label>
											<div class="form_input">
												<div class="active_inactive">
													<input type="checkbox" tabindex="5" name="status"  id="active_inactive_active" class="active_inactive" <?php if($form_mode){ if ($documentdetails->row()->status == 'Active'){echo 'checked="checked"';} }else{echo 'checked="checked"';} ?> />
												</div>
											</div>
										</div>
									</li>
									
									<li>
										<div class="form_grid_12">
											<div class="form_input">
												<input type="hidden" name="document_id" id="document_id" value="<?php if($form_mode){ echo $documentdetails->row()->_id; } ?>"  />
												<button type="submit" class="btn_small btn_blue" tabindex="15"><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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