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
                        <h2><?php echo $heading; ?></h2>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs rider_profile-tab" role="tablist">
                            <li role="presentation" class="<?php if ($findpage == 'all' || $findpage == '') echo 'active'; ?>">
                                <a href="rider/my-rides"><?php
                                    if ($this->lang->line('rides_all_rides') != '')
                                        echo stripslashes($this->lang->line('rides_all_rides'));
                                    else
                                        echo 'ALL RIDES';
                                    ?></a>
                            </li>
                            <li class="<?php if ($findpage == 'upcoming') echo 'active'; ?>">
                                <a href="rider/my-rides?list=upcoming"><?php
                                    if ($this->lang->line('rides_upcoming') != '')
                                        echo stripslashes($this->lang->line('rides_upcoming'));
                                    else
                                        echo 'UPCOMING';
                                    ?></a>
                            </li>
                            <li class="<?php if ($findpage == 'onride') echo 'active'; ?>">
                                <a href="rider/my-rides?list=onride"><?php
                                    if ($this->lang->line('rides_onride') != '')
                                        echo stripslashes($this->lang->line('rides_onride'));
                                    else
                                        echo 'ONRIDE';
                                    ?></a>
                            </li>
                            <li class="<?php if ($findpage == 'completed') echo 'active'; ?>">
                                <a href="rider/my-rides?list=completed"><?php
                                    if ($this->lang->line('rides_completed') != '')
                                        echo stripslashes($this->lang->line('rides_completed'));
                                    else
                                        echo 'COMPLETED';
                                    ?></a>
                            </li>
                            <li class="<?php if ($findpage == 'cancelled') echo 'active'; ?>">
                                <a href="rider/my-rides?list=cancelled"><?php
                                    if ($this->lang->line('ride_cancelled') != '')
                                        echo stripslashes($this->lang->line('ride_cancelled'));
                                    else
                                        echo 'RIDES CANCELLED';
                                    ?></a>
                            </li>
                        </ul>

                        <div class="">

                            <div class="ride_list" id="ride">
                                <ul class="list">
                                    <?php
                                    $all = 0;
                                    foreach ($ridesList->result() as $rides) {

                                        if (!isset($row->timezone) || !$rides->timezone) {
                                            $lat = $rides->booking_information['pickup']['latlong']['lat'];
                                            $long = $rides->booking_information['pickup']['latlong']['lon'];
                                            $url = str_replace(array('{lat}','{long}'), array($lat, $long), $this->config->item('timezone_url'));
                                            $json = json_decode(file_get_contents($url));
                                            date_default_timezone_set($json->timeZoneId);
                                        } else {
                                            date_default_timezone_set($rides->timezone);
                                        }

                                        $bookinTime = $rides->booking_information['booking_date']->sec;
                                        ?>
                                        <li>
                                            <a href="rider/view-ride/<?php echo $rides->ride_id; ?>">
                                                <div class="col-md-12 ride-all-ride">
                                                    <?php
                                                    $date_class = '';
                                                    if ($rides->ride_status == 'Booked' || $rides->ride_status == 'Confirmed') {
                                                        $date_class = 'upcoming-ride-date';
                                                    }
                                                    if ($rides->ride_status == 'Completed') {
                                                        $date_class = 'completed-ride-date';
                                                    }
                                                    if ($rides->ride_status == 'Cancelled') {
                                                        $date_class = 'cancelled-ride-date';
                                                    }
                                                    ?>
                                                    <div class="col-md-3 ride-date <?php echo $date_class; ?>">
                                                        <p><?php echo date('h:i A', $bookinTime); ?><span>
                                                                <?php echo date('jS M, Y', $bookinTime); ?></span></p>
                                                    </div>
                                                    <div class="col-md-9 ride-place">
                                                        <?php
                                                        if (isset($rides->booking_information['pickup']['location'])) {
                                                            $pickupLocation = $rides->booking_information['pickup']['location'];
                                                        } else {

                                                            if ($this->lang->line('rides_location_not_avail') != '')
                                                                $pickupLocation = stripslashes($this->lang->line('rides_location_not_avail'));
                                                            else
                                                                $pickupLocation = 'Location is not available';
                                                        }
                                                        ?>
                                                        <p class="black"># <?php echo $rides->ride_id; ?></p>
                                                        <p><?php echo $pickupLocation; ?></p>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <?php
                                        $all++;
                                    }
                                    ?>

                                </ul>
                                <?php if ($all == 0) { ?>
                                    <div class="ride-book">
                                        <h3><?php
                                            if ($this->lang->line('rides_no_rides') != '')
                                                echo stripslashes($this->lang->line('rides_no_rides'));
                                            else
                                                echo 'No rides!';
                                            ?></h3>
                                    </div>
                                <?php } ?>
                            </div>
                            <div id="infscr-loading" style="text-align: center; display: none;">
                                <span><img src="images/spinner.gif" alt="<?php
                                    if ($this->lang->line('rides_loading') != '')
                                        echo stripslashes($this->lang->line('rides_loading'));
                                    else
                                        echo 'Loading...';
                                    ?>" /></span>
                            </div>
                            <div class="scrolling_pagination" id="scrolling_page_id" style="display: none;">
                                <?php echo $paginationDisplay; ?>
                            </div>

                        </div>
                    </div> 
                </div>
            </div>
        </section>
    </div>
</div>                


<script type="text/javascript">
    var loading = false;
    var $win = $(window),
            $stream = $('div.ride_list ul.list');
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() <= $(document).height() + 500) {
            var $url = $('.scrolling-btn-more').attr('href');
            if (!$url)
                $url = '';
            if ($url != '' && loading == false) {
                loading = true; //prevent further ajax loading
                $('#infscr-loading').show(); //show loading image
                $.ajax({
                    type: 'post',
                    url: $url,
                    success: function (html) {
                        var $html = $($.trim(html)),
                                $more = $('.scrolling_pagination > a'),
                                $new_more = $html.find('.scrolling_pagination > a');
                        if ($html.find('div.ride_list ul.list').text() == '') {

                        } else {
                            $stream.append($html.find('div.ride_list ul.list').html());
                        }
                        if ($new_more.length) {
                            $('.scrolling_pagination').append($new_more);
                        }
                        $more.remove()

                        $('#infscr-loading').hide(); //hide loading image once data is received
                        loading = false;
                    },
                    fail: function (xhr, ajaxOptions, thrownError) { //any errors?
                        alert(thrownError); //alert with HTTP error
                        $('#infscr-loading').hide(); //hide loading image
                        loading = false;
                    }
                });
            }
        }
    });
</script>

<?php
$this->load->view('site/templates/footer');
?> 