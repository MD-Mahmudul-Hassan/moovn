<?php
$this->load->view('site/templates/profile_header');
?> 

<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">

                <!-------Profile side bar ---->

                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>

                <div class="col-md-9 profile_rider_right">
                    <div class="col-md-12 rider-pickup-detail">
                        <h2><?php
                            if ($this->lang->line('user_your_fav_locations') != '')
                                echo stripslashes($this->lang->line('user_your_fav_locations'));
                            else
                                echo 'YOUR FAVOURITE LOCATIONS';
                            ?></h2>
                        <!-- Nav tabs -->
                       

                        <div class="">

                            <div class="ride_list" id="ride">
                                <ul class="list">
                                    <?php
									$favLocations = array();
                                    if(isset($favouriteList->row()->fav_location)){
										$favLocations = $favouriteList->row()->fav_location;
									}
									if(count($favLocations)){ ?>
										<li class="fav-location-title">
											<div class="col-md-12 ride-all-ride">
												<div class="col-md-3">
													<h3><?php
                            if ($this->lang->line('admin_cms_title') != '')
                                echo stripslashes($this->lang->line('admin_cms_title'));
                            else
                                echo 'TITLE';
                            ?></h3>
												</div>
												<div class="col-md-6">  
													<h3><?php
                            if ($this->lang->line('admin_location_and_fare_location_details') != '')
                                echo stripslashes($this->lang->line('admin_location_and_fare_location_details'));
                            else
                                echo 'LOCATION DETAILS';
                            ?></h3>
												</div>
												<div class="col-md-2"> 
												<h3><?php
                            if ($this->lang->line('dash_action') != '')
                                echo stripslashes($this->lang->line('dash_action'));
                            else
                                echo 'ACTION';
                            ?></h3>
												</div>
											</div>
										</li>
										<?php 
										foreach ($favLocations as $key => $locations) { 
											?>
											<li>
												<div class="col-md-12 fav_locations">
													<div class="col-md-3">
														<p><?php echo $locations['title'];?></p>
													</div>
													<div class="col-md-6">  
														<p><?php echo $locations['address'];?></p>
													</div>
													<div class="col-md-2"> 
														<p>
														<a href="#" data-toggle="modal" onclick="updateAddrKey('<?php echo $key; ?>');" data-target="#make-fav"><img src="images/site/edit_icon.png" title="Edit"></span</a>
														<a href="#" data-toggle="modal" onclick="updateAddrKey('<?php echo $key; ?>');" data-target="#make-unfav"><img class="make-unfav" src="images/site/remove-from-fav.png" title="Unfavourite this location"></a>
														</p>
													</div>
												</div>
												<input type="hidden" id="addrTitle<?php echo $key; ?>" value="<?php echo $locations['title'];?>" />
												<input type="hidden" id="address<?php echo $key; ?>" value="<?php echo $locations['address'];?>" />
												<input type="hidden" id="longitude<?php echo $key; ?>" value="<?php echo $locations['geo']['longitude'];?>" />
												<input type="hidden" id="latitude<?php echo $key; ?>" value="<?php echo $locations['geo']['latitude'];?>" />
											
											</li>
											<?php
										} 
									} else {
                                    ?> 
										<li>
											<h3>
												<?php if ($this->lang->line('old_Favourite_locations_not_found') != '') echo stripslashes($this->lang->line('old_Favourite_locations_not_found')); else echo 'Favourite locations not found!'; ?>
											</h3>
										</li>
									<?php } ?>

                                </ul>
                               
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </section>
    </div>
</div>                
<input type="hidden" id="addrKey" value="" />
<input type="hidden" id="user_id" value="<?php if(isset($favouriteList->row()->user_id)) echo (string)$favouriteList->row()->user_id;?>" />

<div class="modal fade" id="make-fav" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php if ($this->lang->line('user_add_fav_location') != '') echo stripslashes($this->lang->line('user_add_fav_location')); else echo 'Add this location into your favourite list'; ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 text-left sign_driver_text">
					<input type="text" class="form-control sign_in_text required email" id="favourite_title" placeholder="<?php if ($this->lang->line('user_favourite_title') != '') echo stripslashes($this->lang->line('user_favourite_title')); else echo 'Favourite location title'; ?>">
					<span id="FavErr" class="favErr"></span>
				</div>
				
				
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="return makeLocFav();" id="cont-btn"><?php if ($this->lang->line('user_continue') != '') echo stripslashes($this->lang->line('user_continue')); else echo 'Continue'; ?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="make-unfav" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title confirm-title" id="myModalLabel1">  <?php if ($this->lang->line('user_are_you_confirm') != '') echo stripslashes($this->lang->line('user_are_you_confirm')); else echo 'Are you sure'; ?>!</h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 text-left sign_driver_text">
					<span> <?php if ($this->lang->line('user_remove_fav_loc_confirm') != '') echo stripslashes($this->lang->line('user_remove_fav_loc_confirm')); else echo 'Do you want to remove this location from your favourite list'; ?>? </span>
					<span id="FavErr1" class="favErr"></span>
				</div>
				
				
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="return makeLocUnFav();" id="cont-btn1"><?php if ($this->lang->line('user_yes') != '') echo stripslashes($this->lang->line('user_yes')); else echo 'Yes'; ?></button>
            </div>
        </div>
    </div>
</div>


<script>


<?php if ($this->lang->line('user_fav_location_added') != ''){ ?>
var favAdded = "<?php echo stripslashes($this->lang->line('user_fav_location_added')); ?>";
<?php }else{ ?>
var favAdded = "Location added into your favourite list";
<?php } ?>
<?php if ($this->lang->line('user_fav_location_removed') != ''){ ?>
var favRemoved = "<?php echo stripslashes($this->lang->line('user_fav_location_removed')); ?>";
<?php }else{ ?>
var favRemoved = "Location removed from your favourite list";
<?php } ?>

	function updateAddrKey(row){
		$('#addrKey').val(row); 
		var cur_title = $('#addrTitle'+row).val();
		$('#favourite_title').val(cur_title);
	}
	function makeLocFav(){
		var lkey = $('#addrKey').val();
		var fav_title = $('#favourite_title').val();
		var address = $('#address'+lkey).val();
		var user_id = $('#user_id').val();
		var longitude = $('#longitude'+lkey).val();
		var latitude = $('#latitude'+lkey).val();
		$('#favourite_title').css('border-color','none');
		$('#FavErr').css('display','none');
		if(fav_title != ''){
			$('#cont-btn').html('<img src="images/indicator.gif">');
			$.ajax({
			    type: "POST",
			    url: 'mobile/user_profile/edit_favourite_location',
			    data: {'title':fav_title,'address':address,'user_id':user_id,'longitude':longitude,'latitude':latitude,'location_key':lkey},
			    dataType: 'json',
				success:function(res){
					$('#FavErr').css('display','block');
					$('#cont-btn').html('Continue');
					if(res.status == '1'){ 
						$('#FavErr').css('color','green');
						$('#FavErr').html(favAdded);
						location.reload();
					} else {
						$('#FavErr').css('color','red');
						$('#FavErr').html(res.message);
					}
				} 
			});
		} else {
			$('#favourite_title').css('border-color','red');	
		}
	}
	
	function makeLocUnFav(){
		var user_id = $('#user_id').val();
		var favLocKey = $('#addrKey').val();
		$('#FavErr1').css('display','none');
		if(favLocKey != ''){
			$('#cont-btn1').html('<img src="images/indicator.gif">');
			$.ajax({
			    type: "POST",
			    url: 'mobile/user_profile/remove_favourite_location',
			    data: {'user_id':user_id,'location_key':favLocKey},
			    dataType: 'json',
				success:function(res){
					$('#FavErr1').css('display','block');
					$('#cont-btn').html('Yes');
					if(res.status == '1'){ 
						$('#FavErr1').css('color','green');
						$('#FavErr').html(favRemoved);
						location.reload();
					} else { 
						$('#FavErr1').css('color','red'); 
						$('#FavErr1').html(res.message);
					}
				} 
			});
		} else {
			alert('Please refresh this page and try again');
		}
	}
	

</script>
<style>
.modal-footer {
    clear: both;
}
#FavErr ,#FavErr1 {
	padding-left:12px;
}
</style>
<?php
$this->load->view('site/templates/footer');
?> 