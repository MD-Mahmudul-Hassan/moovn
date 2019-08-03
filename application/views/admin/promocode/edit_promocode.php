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
						$attributes = array('class' => 'form_container left_label', 'id' => 'editpromo_form');
						echo form_open('admin/promocode/insertEditPromoCode',$attributes) 
					?>
						<ul>
                            <li>
								<div class="form_grid_12">
									<label class="field_title" for="user_name"><?php if ($this->lang->line('admin_promocode_coupon_code') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_code')); else echo 'Coupon code'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="promo_code" id="promo_code" type="text" tabindex="0" readonly class="required large tipTop" title="<?php if ($this->lang->line('admin_promocode_enter_coupon_code') != '') echo stripslashes($this->lang->line('admin_promocode_enter_coupon_code')); else echo 'Please Enter the Coupon Code'; ?>" value="<?php echo $promocode_details->row()->promo_code;?>" readonly />
									</div>
								</div>
							</li>
                                
							<li>
								<div class="form_grid_12">
									<label class="field_title" for="usage_allowed"><?php if ($this->lang->line('admin_promocode_usage_limit_per_coupon') != '') echo stripslashes($this->lang->line('admin_promocode_usage_limit_per_coupon')); else echo 'Usage limit per coupon'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="usage_allowed" id="usage_allowed" type="text" tabindex="1" class="required number positiveNumber minfloatingNumber large tipTop" title="<?php if ($this->lang->line('admin_promocode_enter_coupon_usage_limit') != '') echo stripslashes($this->lang->line('admin_promocode_enter_coupon_usage_limit')); else echo 'Please enter the coupon usage limit'; ?>" value="<?php echo $promocode_details->row()->usage_allowed;?>"/>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title" for="user_usage"><?php if ($this->lang->line('admin_promocode_usage_limit_per_user') != '') echo stripslashes($this->lang->line('admin_promocode_usage_limit_per_user')); else echo 'Usage limit per user'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="user_usage" id="user_usage" type="text" tabindex="5" class="required number positiveNumber minfloatingNumber large tipTop" title="<?php if ($this->lang->line('admin_promocode_enter_coupon_usage_limit_per_user') != '') echo stripslashes($this->lang->line('admin_promocode_enter_coupon_usage_limit_per_user')); else echo 'Please enter the coupon usage limit per user'; ?>" value="<?php if(isset($promocode_details->row()->user_usage))echo $promocode_details->row()->user_usage;?>" />
									</div>
								</div>
							</li>
                                
                            <li>
								<div class="form_grid_12">
									<label class="field_title" for="datefrom"><?php if ($this->lang->line('admin_promocode_coupon_valid_from') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_valid_from')); else echo 'Coupon Valid From'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="validity[valid_from]" id="datefrom" type="text" tabindex="2" class="required large tipTop datepicker" title="<?php if ($this->lang->line('admin_promocode_select_the_code') != '') echo stripslashes($this->lang->line('admin_promocode_select_the_code')); else echo 'Please select the date'; ?>" value="<?php echo $promocode_details->row()->validity['valid_from'];?>" data-avail="<?php echo date("m-d-Y",strtotime($promocode_details->row()->validity['valid_from'])); ?>"/>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title" for="dateto"><?php if ($this->lang->line('admin_promocode_coupon_valid_till') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_valid_till')); else echo 'Coupon Valid Till'; ?><span class="req">*</span></label>
									<div class="form_input">
										<input name="validity[valid_to]" id="dateto" type="text" tabindex="3" class="required large tipTop datepicker" title="<?php if ($this->lang->line('admin_promocode_select_the_code') != '') echo stripslashes($this->lang->line('admin_promocode_select_the_code')); else echo 'Please select the date'; ?>" value="<?php echo $promocode_details->row()->validity['valid_to'];?>"/>
									</div>
								</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title" for="location_id"><?php if ($this->lang->line('admin_promocode_coupon_select_country') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_select_country')); else echo 'Select Location'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<select class="required chzn-select" name="location_id" id="location_id" tabindex="-1" style="width: 375px; display: none;" data-placeholder="<?php if ($this->lang->line('admin_promocode_coupon_select_country') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_select_country')); else echo 'Select Location'; ?>">
											<option value=""></option>
											<?php foreach ($location_details->result() as $row) { ?>
												<option value="<?php echo $row->_id; ?>" <?php if (isset($promocode_details->row()->location_id)) if ($promocode_details->row()->location_id == $row->_id) echo 'selected="selected"' ?>><?php echo $row->city; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
                            </li>
							
							<li>
									<div class="form_grid_12">
										<label class="field_title" for="price_type"><?php if ($this->lang->line('admin_promocode_coupon_type') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_type')); else echo 'Coupon Type'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<div class="flat_percentage">
												<input type="checkbox" name="price_type" <?php if ($promocode_details->row()->code_type == 'Flat'){echo 'checked="checked"';}?> id="flat_percentage_flat" class="flat_percentage"/>
											</div>
										</div>
									</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<label class="field_title" for="promo_value"><?php if ($this->lang->line('admin_promocode_coupon_amount') != '') echo stripslashes($this->lang->line('admin_promocode_coupon_amount')); else echo 'Coupon Amount'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="promo_value" id="promo_value" type="text" tabindex="5" class="required number positiveNumber minfloatingNumber large tipTop" title="<?php if ($this->lang->line('admin_promocode_select_price_value') != '') echo stripslashes($this->lang->line('admin_promocode_select_price_value')); else echo 'Please enter the price value'; ?>" value="<?php echo $promocode_details->row()->promo_value;?>"/>
									</div>
								</div>
							</li>
	 						
							<li>
									<div class="form_grid_12">
										<label class="field_title" for="status"><?php if ($this->lang->line('admin_common_status') != '') echo stripslashes($this->lang->line('admin_common_status')); else echo 'Status'; ?> <span class="req">*</span></label>
										<div class="form_input">
											<div class="active_inactive">
												<input type="checkbox" name="status" <?php if ($promocode_details->row()->status == 'Active'){echo 'checked="checked"';}?> id="active_inactive_active" class="active_inactive"/>
											</div>
										</div>
									</div>
							</li>
							
							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" tabindex="7"><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
									</div>
								</div>
							</li>  
							
						</ul>
						<input type="hidden" name="promo_id" value="<?php echo $promocode_details->row()->_id?>"/>
					</form>
				</div>
			</div>
		</div>
	</div>
	<span class="clear"></span>
</div>


<script type="text/javascript"> 
$(document).ready(function(){
    $('#addpromo_forms').validate({ ignore: ":hidden:not(select)" });
});
</script>
<style>
label.error{
	float: right;
}
</style>

<?php 
$this->load->view('admin/templates/footer.php');
?>