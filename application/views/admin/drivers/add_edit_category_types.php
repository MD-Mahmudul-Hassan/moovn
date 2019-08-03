<?php
$this->load->view('admin/templates/header.php');
?>
<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('admin_drivers_map_vehicle_types') != '') echo stripslashes($this->lang->line('admin_drivers_map_vehicle_types')); else echo 'Vehicle Types'; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditcategorytypes_form', 'enctype' => 'multipart/form-data');
						echo form_open_multipart('admin/drivers/insertEditTypes',$attributes) 
					?>
						<ul>
							<li>
								<div class="form_grid_12">
									<label class="field_title" for="attribute_name"><?php if ($this->lang->line('admin_drivers_category_name') != '') echo stripslashes($this->lang->line('admin_drivers_category_name')); else echo 'Category Name'; ?> </label>
									<div class="form_input">
										<?php echo $categorydetails->row()->name;?>
									</div>
								</div>
							</li>
                            <?php 
							if(isset($categorydetails->row()->vehicle_type)){
								$vehicleArr=$categorydetails->row()->vehicle_type;
							}else{
								$vehicleArr='';
							}
							if(!is_array($vehicleArr))$vehicleArr=array();
							?>
                            <li>
								<div class="form_grid_12">
									<label class="field_title" for="attribute_name"><?php if ($this->lang->line('admin_drivers_map_vehicle_types') != '') echo stripslashes($this->lang->line('admin_drivers_map_vehicle_types')); else echo 'Vehicle Types'; ?></label>
									<?php if($vehicle_types->num_rows()>0){ ?>
									<select class="chzn-select required Validname" multiple="multiple" id="vehicle_type" name="vehicle_type[]" tabindex="1" style="width: 400px; display: none;" data-placeholder="<?php if ($this->lang->line('admin_select_vehicle') != '') echo stripslashes($this->lang->line('admin_select_vehicle')); else echo 'Select Vehicle Types'; ?>">
										<?php foreach($vehicle_types->result() as $row){ ?>
										<option value="<?php echo $row->_id; ?>" <?php if (in_array($row->_id,$vehicleArr)){echo 'selected="selected"';}  ?>>
											<?php echo $row->vehicle_type; ?>
										</option>
										<?php } ?>
									</select>
									<?php }else{ ?>
										<div><p class="error"><?php if ($this->lang->line('admin_drivers_map_vehicle_types_list') != '') echo stripslashes($this->lang->line('admin_drivers_map_vehicle_types_list')); else echo 'Kindly check vehicle types list. There is no types to be add into a category.'; ?></p></div>
									<?php } ?>
								</div>
							</li>

							<li>
								<div class="form_grid_12">
									<div class="form_input">
										<input type="hidden" name="category_id" value="<?php echo $categorydetails->row()->_id;?>"/>
										<button type="submit" class="btn_small btn_blue" tabindex="2"><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
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
<script>
$("#vehicle_type").chosen().change(function() {});
</script>
<?php 
$this->load->view('admin/templates/footer.php');
?>