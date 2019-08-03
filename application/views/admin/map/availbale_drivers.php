<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
?>

<style>
.yellowbox {
    background: #f7941d none repeat scroll 0 0 !important;
    border: 1px solid #f7941d;
}
.center_driver_mode {
    margin-top: 20px;
    background: none !important;
     width: 98.0%;
}
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
						<h6><?php if ($this->lang->line('admin_map_view_display_drivers') != '') echo stripslashes($this->lang->line('admin_map_view_display_drivers')); else echo 'Display available drivers in their location'; ?></h6>
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
		 <div class="grid_12 center_driver_mode">
				<div class="widget_wrap">
				   <div class="widget_content">
						<div class="stat_block">
							<div class="social_activities">	
									<?php if($address != '') {?>
									<h2 style="border-bottom: medium solid; margin-bottom: 12px;width: 90%;"><b><?php if ($this->lang->line('admin_map_drivers_near') != '') echo stripslashes($this->lang->line('admin_map_drivers_near')); else echo 'Drivers Near'; ?> : </b><?php echo $address; ?></h2>
									<?php } ?>
									<a class="activities_s bluebox big" href="javascript:void(0)">
										<div class="block_label">
											<span class="user_icon"></span><div class="clear"></div>
											<?php if ($this->lang->line('admin_map_online_drivers') != '') echo stripslashes($this->lang->line('admin_map_online_drivers')); else echo 'Online Drivers'; ?>
											<span><?php if (isset($online_drivers)) echo $online_drivers; ?></span>
										</div>
									</a>								
									<a class="activities_s redbox big" href="javascript:void(0)">
										<div class="block_label">
											<span class="user_icon"></span><div class="clear"></div>
										 <?php if ($this->lang->line('admin_map_offline_drivers') != '') echo stripslashes($this->lang->line('admin_map_offline_drivers')); else echo 'Offline Drivers'; ?>	
											<span><?php if (isset($offline_drivers)) echo $offline_drivers; ?></span>
										</div>
									</a>							
									<a class="activities_s yellowbox big" href="javascript:void(0)">
										<div class="block_label">
											<span class="user_icon"></span><div class="clear"></div>
											<?php if ($this->lang->line('admin_map_on_ride_drivers') != '') echo stripslashes($this->lang->line('admin_map_on_ride_drivers')); else echo 'On Ride Drivers'; ?>	
											<span><?php if (isset($onride_drivers)) echo $onride_drivers; ?></span>
										</div>
									</a>								
								
							</div>
						</div>
					</div>
				</div>
			</div>
		<span class="clear"></span>
</div>
<?php 
$this->load->view('admin/templates/footer.php');
?>