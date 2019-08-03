<?php
$this->load->view('admin/templates/header.php');
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
						$attributes = array('class' => 'form_container left_label', 'id' => 'addbanner_form',  'enctype' => 'multipart/form-data');
						echo form_open_multipart('admin/banner/editBanner',$attributes) 
					?>
                    
	 						<ul>
	 							
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="name"><?php if ($this->lang->line('admin_banner_banner_name') != '') echo stripslashes($this->lang->line('admin_banner_banner_name')); else echo 'Banner Name'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="name" id="name" type="text" tabindex="1" class="large tipTop" value="<?php if(isset($banner_details->row()->name)) echo $banner_details->row()->name;?>" title="<?php if ($this->lang->line('admin_banner_enter_banner_name') != '') echo stripslashes($this->lang->line('admin_banner_enter_banner_name')); else echo 'Please enter the banner name'; ?>"/>
									</div>
								</div>
								</li>
								<li>
								   <div class="form_grid_12">
									  <label class="field_title" for="name"><?php if ($this->lang->line('admin_banner_banner_title') != '') echo stripslashes($this->lang->line('admin_banner_banner_title')); else echo 'Banner Title'; ?> <span class="req">*</span></label>
										<div class="form_input">
										<input name="banner_title" id="banner_title" type="text" tabindex="1" class="large tipTop required" title="<?php if ($this->lang->line('admin_banner_enter_banner_title') != '') echo stripslashes($this->lang->line('admin_banner_enter_banner_title')); else echo 'Please enter the banner title'; ?>" value="<?php if(isset($banner_details->row()->banner_title)) echo $banner_details->row()->banner_title;?>"/>
										 </div>
										 </div>
								 </li>
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="banner_image"><?php if ($this->lang->line('admin_banner_banner_image') != '') echo stripslashes($this->lang->line('admin_banner_banner_image')); else echo 'Banner Image'; ?> </label>
									<div class="form_input">
										<input name="banner_image" id="banner_image" type="file" tabindex="3" class="large tipTop" title="<?php if ($this->lang->line('admin_banner_upload_banner_image') != '') echo stripslashes($this->lang->line('admin_banner_upload_banner_image')); else echo 'Please upload banner image'; ?>"/>
										<div class="error" id="ErrCAtImage"><?php if ($this->lang->line('admin_banner_image_upload_size') != '') echo stripslashes($this->lang->line('admin_banner_image_upload_size')); else echo 'Note: Image Upload Size 1349 x 600 pixel'; ?></div>
									</div> 
									<img src="" id="loadedImg" style="widows:25px; height:25px; display:none;" />
									<div class="form_input">
										<?php if(isset($banner_details->row()->image)) { ?>
										<img src="<?php echo base_url();?>images/banner/<?php echo $banner_details->row()->image;?>" width="200"/>
										<?php } ?>
									</div>
								</div>
								</li>

                                <ul>
	 							<li>
								<div class="form_grid_12">
									<label class="field_title" for="status"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<div class="publish_unpublish">
											<input type="checkbox" tabindex="4" name="status" <?php if ($banner_details->row()->status == 'Publish'){?>checked="checked"<?php }?> id="publish_unpublish_publish" class="publish_unpublish"/>
										</div>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" tabindex="5"><span><?php if ($this->lang->line('admin_common_update') != '') echo stripslashes($this->lang->line('admin_common_update')); else echo 'Update'; ?></span></button>
									</div>
								</div>
								</li>
							</ul>
							<input type="hidden" name="banner_id" value="<?php echo $banner_details->row()->_id;?>" />
                            
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
</div>
<script type="text/javascript">
$('#addbanner_form').validate();
</script>

<?php 
if ($this->lang->line('admin_banner_upload_banner_image_error') != ''){
	$banner_Er = stripslashes($this->lang->line('admin_banner_upload_banner_image_error'));
} else {
	$banner_Er = 'Upload Image Too Small. Please Upload Image Size should be';
}
if ($this->lang->line('admin_success') != ''){
	$banner_Success = stripslashes($this->lang->line('admin_success'));
} else {
	$banner_Success = 'Success';
}
?>

<script>
$(document).ready(function() {
$("#banner_image").change(function(e) {   
	    e.preventDefault();
        var formData = new FormData($(this).parents('form')[0]);
        $.ajax({
			beforeSend: function()
 		      {
				   $("#loadedImg").css("display", "block");
      	        document.getElementById("loadedImg").src='images/loader64.gif';
  			  },
            url: 'admin/banner/ajax_check_banner_image_size',
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            success: function (data) {
			$("#loadedImg").css("display", "none");
			  if(data=='Success'){
		      $('#ErrCAtImage').html('<?php echo $banner_Success; ?>');
			  return true;
			  } else {
			  $('#banner_image').val('');
			  $('#ErrCAtImage').html('<?php echo $banner_Er; ?> 1349 X 600 .');
			  return false;
			  }
		   },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
		
		
});
});
</script>
<style>

.error {
    color: #f00;
    font-size: 12px !important;
    font-weight: normal;
    margin: 12px 12px 12px 0;
}
</style>

<?php 
$this->load->view('admin/templates/footer.php');
?>