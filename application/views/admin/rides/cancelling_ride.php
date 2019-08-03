<?php
$this->load->view('admin/templates/header.php');
?>
<script type="text/javascript">
$(document).ready(function(){


$("#cancelled_by").change(function(){
	var user_type = $(this).val();
	$.ajax({
      type: 'POST',
      data: { 'user_type': user_type },
      url: 'admin/rides/user_type_cancellation_reason',
      dataType: "json",
	  success: function(data){
		   $("#cancel_reason").empty();
		  $(data).each(function(key,val){
			$("#cancel_reason").append($('<option></option>').val(val.id).html(val.reason)).trigger("liszt:updated");
		  });
	  }
	});
});
	
});
</script>
<style type="text/css">
.model_type .error {
    float: right;
    margin-right: 30%;
}
.year-of-models .chzn-drop{
	width: 65px !important;
}
#year_of_model_chzn{
	width: 250px !important;
}
.default {
	width: 650px !important;
}
.track_ride, .view_details{
	padding: 7px 12px 7px 23px !important;
	color: #fff;
}
</style>
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
                    <form class="form_container left_label" action="admin/rides/cancelling_ride" id="admin_search_ride_form" method="post" enctype="multipart/form-data">
                        <div>
                        <ul>
						<li>
							<div class="form_grid_12">
							<label class="field_title" for="vehicle_type"><?php if ($this->lang->line('admin_rides_search_ride') != '') echo stripslashes($this->lang->line('admin_rides_search_ride')); else echo 'Search Ride'; ?> <span class="req">*</span></label>
							
							<div class="form_input">
								<input name="search_ride_id" id="search_ride_id" type="text" tabindex="2" class="large tipTop required" title="<?php if ($this->lang->line('admin_ride_enter_ride_id') != '') echo stripslashes($this->lang->line('admin_ride_enter_ride_id')); else echo 'Please enter Ride ID'; ?>" value="<?php  if(isset($ride_id)) { echo $ride_id;} ?>"/>
								<button type="submit" class="btn_small btn_blue" tabindex="15"><span><?php if ($this->lang->line('admin_rides_search') != '') echo stripslashes($this->lang->line('admin_rides_search')); else echo 'Search'; ?> </span></button>
							</div>
							</div>
						</li>
						
						

                            </ul>
                        </div>

                    </form>
                </div>
                <div class="widget_content">
                    <form class="form_container left_label" action="admin/rides/make_ride_cancelled" id="admin_cancelling_ride_form" method="post" enctype="multipart/form-data">
                        <div>
                        <ul>
						<?php if($rideFound == 'true'){ ?>
                        <li>
							<div class="form_grid_12">
							<label class="field_title" for="vehicle_type"><?php if ($this->lang->line('admin_rides_ride_id') != '') echo stripslashes($this->lang->line('admin_rides_ride_id')); else echo 'Ride ID'; ?></label>
							<div class="form_input">
								<input name="ride_id" id="ride_id" type="text" tabindex="2" class="large tipTop required" title="<?php if ($this->lang->line('admin_ride_enter_ride_id') != '') echo stripslashes($this->lang->line('admin_ride_enter_ride_id')); else echo 'Please enter Ride ID'; ?>" readonly value="<?php  if($ride_id != '') { echo $ride_id;} ?>"/>
								
									<a class="p_car tipTop track_ride" target="_blank" href="track-ride?rideId=<?php echo $ride_id; ?>" title="<?php if ($this->lang->line('admin_ride_track_this_ride') != '') echo stripslashes($this->lang->line('admin_ride_track_this_ride')); else echo 'Track this ride'; ?>"><?php if ($this->lang->line('admin_rides_track_ride') != '') echo stripslashes($this->lang->line('admin_rides_track_ride')); else echo 'Track Ride'; ?></a>
									<a class="p_edit tipTop view_details" target="_blank" href="admin/rides/view_ride_details/<?php echo $ride_details->row()->_id; ?>" title="<?php if ($this->lang->line('admin_referral_history_view_details') != '') echo stripslashes($this->lang->line('admin_referral_history_view_details')); else echo 'View Details'; ?>"><?php if ($this->lang->line('admin_rides_view_ride') != '') echo stripslashes($this->lang->line('admin_rides_view_ride')); else echo 'View Ride'; ?></a>
								
							</div>
							</div>
						</li>
						<li>
						<div class="form_grid_12">
						<label class="field_title" for="name"><?php if ($this->lang->line('admin_rides_current_ride_status') != '') echo stripslashes($this->lang->line('admin_rides_current_ride_status')); else echo 'Current Ride Status'; ?></label>
							<div class="form_input">
								<input name="current_ride_status" id="current_ride_status" type="text" tabindex="2" class="large tipTop required" title="<?php if ($this->lang->line('admin_ride_current_ride_status') != '') echo stripslashes($this->lang->line('admin_ride_current_ride_status')); else echo 'Current Ride Status'; ?>" disabled value="<?php echo get_language_value_for_keyword($ride_details->row()->ride_status,$this->data['langCode']); ?>"/>
							</div>
						</div>
						</li>
						<li>
						<div class="form_grid_12">
						<label class="field_title" for="name"><?php if ($this->lang->line('admin_rides_current_payment_status') != '') echo stripslashes($this->lang->line('admin_rides_current_payment_status')); else echo 'Current Payment Status'; ?></label>
							<div class="form_input">
								<input name="current_pay_status" id="current_pay_status" type="text" tabindex="2" class="large tipTop required" title="<?php if ($this->lang->line('admin_ride_current_payment_status') != '') echo stripslashes($this->lang->line('admin_ride_current_payment_status')); else echo 'Current Payment Status'; ?>" disabled value="<?php if(isset($ride_details->row()->pay_status))echo $ride_details->row()->pay_status; else echo get_language_value_for_keyword('Not Available',$this->data['langCode']); ?>"/>
							</div>
						</div>
						</li>
						<li>
						<div class="form_grid_12">
						<label class="field_title" for="vehicle_type"><?php if ($this->lang->line('admin_rides_cancelled_by') != '') echo stripslashes($this->lang->line('admin_rides_cancelled_by')); else echo 'Cancelled By'; ?> <span class="req">*</span></label>
							<div class="form_input model_type">
								<select id="cancelled_by" class="chzn-select admin_cancel_ride_chosen required" name="cancelled_by" tabindex="1" style="width: 375px; display: none;" title="<?php if ($this->lang->line('admin_ride_who_cancelled_ride') != '') echo stripslashes($this->lang->line('admin_ride_who_cancelled_ride')); else echo 'Who Cancelled the Ride?'; ?>">
									<option value=""><?php if ($this->lang->line('admin_rides_select_cancelling_ride') != '') echo stripslashes($this->lang->line('admin_rides_select_cancelling_ride')); else echo 'Select who is Cancelling Ride?'; ?></option>
									<option value="driver"><?php if ($this->lang->line('admin_rides_driver') != '') echo stripslashes($this->lang->line('admin_rides_driver')); else echo 'Driver'; ?></option>
									<option value="user"><?php if ($this->lang->line('admin_rides_user') != '') echo stripslashes($this->lang->line('admin_rides_user')); else echo 'User'; ?></option>
								</select>
							</div>
							</div>
						</li>
						<li>
						<div class="form_grid_12">
						<label class="field_title" for="vehicle_type"><?php if ($this->lang->line('admin_rides_cancellation_reason') != '') echo stripslashes($this->lang->line('admin_rides_cancellation_reason')); else echo 'Cancelling Reason'; ?> <span class="req">*</span></label>
							<div class="form_input model_type">
								<select id="cancel_reason" class="chzn-select admin_cancel_ride_chosen required" name="cancel_reason" tabindex="1" style="width: 375px; display: none;" title="<?php if ($this->lang->line('admin_ride_why_cancelled_ride') != '') echo stripslashes($this->lang->line('admin_ride_why_cancelled_ride')); else echo 'Why are you cancelling this ride?'; ?>">
								
								</select>
							</div>
							</div>
						</li>
						<li>
							<div class="form_grid_12">
								<div class="form_input">
								<button type="submit" class="btn_small btn_blue" tabindex="15"><span><?php if ($this->lang->line('admin_rides_cancel_ride') != '') echo stripslashes($this->lang->line('admin_rides_cancel_ride')); else echo 'Cancel Ride'; ?></span></button>
								</div>
							</div>
						</li>
						<?php } ?>

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