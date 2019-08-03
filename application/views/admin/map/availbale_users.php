<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
?>

<style>
#location {
    clear: both;
    height: 23px;
    margin: 1%;
    width: 50%;
}
#btn_find {
    background-color: #28cbf9;
    border: medium none;
    cursor: pointer;
    padding: 6px 32px;
}
</style>
<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('admin_map_view_display_users') != '') echo stripslashes($this->lang->line('admin_map_view_display_users')); else echo 'Display available users in their location'; ?></h6>
                        <div id="widget_tab">
            			</div>
					</div>
					<div class="widget_content">
							<form>
								<div class="grid_12">
									<input name="location" id="location" type="text" tabindex="1" class="form-control" value="<?php if(isset($address)){ echo $address; } ?>" autocomplete="off" placeholder="<?php if ($this->lang->line('admin_enter_location') != '') echo stripslashes($this->lang->line('admin_enter_location')); else echo 'Enter location'; ?>"/>
								<button type="submit" class="btn" id="btn_find" tabindex="4"><?php if ($this->lang->line('admin_map_find') != '') echo stripslashes($this->lang->line('admin_map_find')); else echo 'Find'; ?></button>
								</div>
								
								<div class="grid_12">
									<?php echo $map['js']; ?>
									<?php echo $map['html']; ?>
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