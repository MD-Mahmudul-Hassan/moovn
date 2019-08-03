<?php
$this->load->view('site/templates/profile_header');
$findpage = $this->uri->segment(2);
?> 
<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">
                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>

                <div class="col-md-9 nopadding profile_rider_right">
                    <div class="col-md-12 rider-pickup-detail">
                        <h2><?php if ($this->lang->line('user_rate_card_upper') != '') echo stripslashes($this->lang->line('user_rate_card_upper')); else echo 'RATE CARD'; ?></h2>
                        <?php if ($locationsList->num_rows() > 0) { ?>
                            <div class="car-lcoation">
                                <select class="form-control" onchange="city_rate_charge('loc');" id="city_rate_card">
									<option value=""><?php if ($this->lang->line('ride_select_your_city') != '') echo stripslashes($this->lang->line('ride_select_your_city')); else echo 'Select your city'; ?>...</option>
                                    <?php
                                    foreach ($locationsList->result() as $locations) {
                                        ?>
                                        <option <?php if (isset($ratecard_data['location_id'])) if ($ratecard_data['location_id'] == (string) $locations->_id) echo 'selected="selected"'; ?> value="<?php echo (string) $locations->_id; ?>"><?php echo $locations->city; ?></option>
                                    <?php } ?>
                                </select>
                                <span><?php if ($this->lang->line('cms_city') != '') echo stripslashes($this->lang->line('cms_city')); else echo 'City'; ?></span>
                            </div>
                            <div class="car-lcoation car-type">
                                <select class="form-control" onchange="city_rate_charge('cat');" id="cat_rate_card">
									<option value=""><?php if ($this->lang->line('ride_select_car_type') != '') echo stripslashes($this->lang->line('ride_select_car_type')); else echo 'Select car type'; ?>...</option>
                                    <?php
                                    foreach ($RatecategoryList as $categories) {
                                        ?>
                                        <option <?php if (isset($ratecard_data['category_id'])) if ($ratecard_data['category_id'] == (string) $categories->_id) echo 'selected="selected"'; ?> value="<?php echo (string) $categories->_id; ?>"><?php echo $categories->name; ?></option>
                                    <?php } ?>
                                </select>
                                <span><?php if ($this->lang->line('user_car_type') != '') echo stripslashes($this->lang->line('user_car_type')); else echo 'CAR TYPE'; ?></span>
                            </div>
							<?php if($have_date==''){ ?>
                            <div class="car-std">
                                <h3><?php if ($this->lang->line('user_standard_rate') != '') echo stripslashes($this->lang->line('user_standard_rate')); else echo 'STANDARD RATE'; ?></h3>
                                <ul>
                                    <?php
									$dcurrencySymbol=$ratecard_data['currency_symbol'];									
                                    if (isset($ratecard_data['standard_rate']) && count($ratecard_data['standard_rate']) > 0) {
										
                                        $standard_rate = $ratecard_data['standard_rate'];
									
										 $d_distance_unit=$ratecard_data['distance_unit'];
									
                                        for ($s = 0; $s < count($standard_rate); $s++) {
										
											
																				  #echo $d_distance_unit;die;
																				# echo  print_r($standard_rate);die;
																						 if($d_distance_unit == "km"){
																						  if ($this->lang->line('ride_km') != '') $d_distance_unit1=stripslashes($this->lang->line('ride_km'));
																							else
																							$d_distance_unit1= 'km'; 
																							#echo '24324'.$d_distance_unit1;die;
																						}
																						
																						?>
                                            <li><p><?php echo str_replace("km",$d_distance_unit1,$standard_rate[$s]['title']); ?> <?php echo $standard_rate[$s]['sub_title']; ?><span> <?php echo $dcurrencySymbol . str_replace("km",$d_distance_unit1,$standard_rate[$s]['fare']); ?></span></p></li>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <li><p style="text-align: center;"><?php if ($this->lang->line('user_rate_information_na') != '') echo stripslashes($this->lang->line('user_rate_information_na')); else echo 'Rate Information not available'; ?></p></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="car-std extra-charge">
                                <h3><?php if ($this->lang->line('user_extra_charges') != '') echo stripslashes($this->lang->line('user_extra_charges')); else echo 'EXTRA CHARGES'; ?></h3>
                                <ul>

                                    <?php
                                    if (isset($ratecard_data['extra_charges']) && count($ratecard_data['extra_charges']) > 0) {
                                        $extra_charges = $ratecard_data['extra_charges'];
                                        for ($exr = 0; $exr < count($extra_charges); $exr++) {
                                            ?>
                                            <li>
                                                <h5><?php echo $extra_charges[$exr]['title']; ?> 
                                                <span>
                                                <?php 
                                                if($extra_charges[$exr]['title'] =='Service Tax')
                                                  echo  $extra_charges[$exr]['fare']." %"; 
                                                else
                                                  echo $dcurrencySymbol . $extra_charges[$exr]['fare'];
                                                
                                                ?>
                                                </span>
                                                </h5>
                                                <p><?php echo $extra_charges[$exr]['sub_title']; ?></p>
                                            </li>

                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <li><p style="text-align: center;"><?php if ($this->lang->line('user_rate_information_na') != '') echo stripslashes($this->lang->line('user_rate_information_na')); else echo 'Rate Information not available'; ?></p></li>
                                        <?php
                                    }
                                    ?>


                                </ul>
                            </div>
							<?php }else{ ?>
							<div class="car-std">
								<h3><?php echo $have_date; ?></h3>
							</div>
							<?php } ?>

                        <?php } else { ?>
                            <div class="car-std">
                                <h3><?php if ($this->lang->line('user_no_locations_found') != '') echo stripslashes($this->lang->line('user_no_locations_found')); else echo 'No locations found for rate card'; ?></h3>
                            </div>
                        <?php } ?>
                    </div>
                </div>				
            </div>
        </section>
    </div>
</div>

<script>
    function city_rate_charge(finder) {
        var city_rate_card = $('#city_rate_card').val();
        var cat_rate_card = $('#cat_rate_card').val();
        if (city_rate_card != '' & finder == 'loc') {
            window.location.href = "rider/rate-card?loc=" + city_rate_card;
        }
        if (city_rate_card != '' && cat_rate_card != '' & finder == 'cat') {
            window.location.href = "rider/rate-card?loc=" + city_rate_card + '&cat=' + cat_rate_card;
        }
    }
</script>

<?php
$this->load->view('site/templates/footer');
?> 