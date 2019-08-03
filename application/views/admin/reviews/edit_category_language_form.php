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
			<form class="form_container left_label" action="admin/reviews/update_language_content" id="addeditcategory_form" method="post" enctype="multipart/form-data">
				<div>
					<ul>
								<li>
                                    <h3> <?php echo $reviewdetails->row()->option_name; ?> </h3>
                                </li>
					
							<?php 
								$langContents = array();
								if(isset($reviewdetails->row()->option_name_languages)){
									$langContents = $reviewdetails->row()->option_name_languages;
								}
								foreach($languagesList->result() as $lang){
							?>
								<li>
									<div class="form_grid_12">
										<label class="field_title" for="name"><?php echo $lang->name; ?> </label>
										<div class="form_input">
											<input name="option_name_languages[<?php echo $lang->lang_code; ?>]" id="name" value="<?php if(isset($langContents[$lang->lang_code])) echo $langContents[$lang->lang_code]; ?>" type="text" tabindex="2" class="large tipTop " />
										</div>
									</div>
								</li>
							<?php } ?>
				
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<input type="hidden" name="category_id" id="category_id" value="<?php  echo $reviewdetails->row()->_id;  ?>"  />
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