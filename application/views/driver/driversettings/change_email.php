<?php 
$this->load->view('driver/templates/header.php');
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
						$attributes = array('class' => 'form_container left_label', 'id' => 'regitstraion_form');
						echo form_open('driver/profile/change_email',$attributes) 
					?>
	 						<ul>
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="email">
											<?php if($this->lang->line('dash_current_email') != '') echo stripslashes($this->lang->line('dash_current_email')); else  echo 'Current Email';?><span class="req">*</span>
										</label>
										<div class="form_input">
											<input name="email" id="email" type="text" tabindex="1" class="required email large tipTop" title="<?php 
											if($this->lang->line('dash_enter_current_email') != '') echo stripslashes($this->lang->line('dash_enter_current_email')); else  echo 'Please enter the current email';?>"/><br/>
										</div>
									</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="newemail"><?php 
						if($this->lang->line('dash_new_mail') != '') echo stripslashes($this->lang->line('dash_new_mail')); else  echo 'New Email';
						?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="new_email" id="new_email" type="text" tabindex="2" class="required large email tipTop" title="<?php 
						if($this->lang->line('dash_enter_new_email') != '') echo stripslashes($this->lang->line('dash_enter_new_email')); else  echo 'Please enter a new email';
						?>"/>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue"><span><?php 
						if($this->lang->line('dash_change') != '') echo stripslashes($this->lang->line('dash_change')); else  echo 'Change';
						?></span></button>
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
$this->load->view('driver/templates/footer.php');
?>