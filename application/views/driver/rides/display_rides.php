<?php
$this->load->view('driver/templates/header.php');
?>
<div id="content">
    <div class="grid_container">
        <?php
        $attributes = array('id' => 'display_form');
        echo form_open('driver/rides/change_rides_status_global', $attributes)
        ?>

        <?php
        if ($this->lang->line('dash_click_sort') != '')
            $dash_click_sort = stripslashes($this->lang->line('dash_click_sort'));
        else
            $dash_click_sort = 'Click to sort';
        ?>

        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php echo $heading ?></h6>
                </div>
                <div class="widget_content">
                    <?php
                    if ($paginationLink != '') {
                        echo $paginationLink;
                        $tble = 'allrides_tbl';
                    } else {
                        $tble = 'rides_tbl';
                    }
                    ?>

                    <table class="display display_tbl" id="<?php echo $tble; ?>">
                        <thead>
                            <tr>
                                <th class="center" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('dash_S_No') != '')
                                        echo stripslashes($this->lang->line('dash_S_No'));
                                    else
                                        echo 'S.No';
                                    ?>
                                </th>
                                <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('dash_ride_id') != '')
                                        echo stripslashes($this->lang->line('dash_ride_id'));
                                    else
                                        echo 'Ride Id';
                                    ?>
                                </th>
                                <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('dash_booked_date') != '')
                                        echo stripslashes($this->lang->line('dash_booked_date'));
                                    else
                                        echo 'Booked Date';
                                    ?>
                                </th>
                                <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('driver_status') != '')
                                        echo stripslashes($this->lang->line('driver_status'));
                                    else
                                        echo 'Status';
                                    ?>
                                </th>
                                <?php
                                $actionRide = $this->input->get('act');
                                if ($actionRide == 'Completed') {
                                    ?>
                                    <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                        <?php
                                        if ($this->lang->line('dash_rider_ratings') != '')
                                            echo stripslashes($this->lang->line('dash_rider_ratings'));
                                        else
                                            echo 'Rider Ratings';
                                        ?>
                                    </th>
                                    <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                        <?php
                                        if ($this->lang->line('dash_driver_ratings') != '')
                                            echo stripslashes($this->lang->line('dash_driver_ratings'));
                                        else
                                            echo 'Driver Ratings';
                                        ?>
                                    </th>
                                    <?php
                                }
                                ?>

                                <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('dash_track_ride') != '')
                                        echo stripslashes($this->lang->line('dash_track_ride'));
                                    else
                                        echo 'Track Ride';
                                    ?>
                                </th>

                                <th>
                                    <?php
                                    if ($this->lang->line('dash_action') != '')
                                        echo stripslashes($this->lang->line('dash_action'));
                                    else
                                        echo 'Action';
                                    ?>
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($ridesList->num_rows() > 0) {
                                $i = $offsetVal + 1;
                                foreach ($ridesList->result() as $row) {

                                    if (!isset($row->timezone) || !$row->timezone) {
                                        $lat = $row->booking_information['pickup']['latlong']['lat'];
                                        $long = $row->booking_information['pickup']['latlong']['lon'];
                                        $url = str_replace(array('{lat}','{long}'), array($lat, $long), $this->config->item('timezone_url'));
                                        $json = json_decode(file_get_contents($url));
                                        date_default_timezone_set($json->timeZoneId);
                                    } else {
                                        date_default_timezone_set($row->timezone);
                                    }

                                    ?>
                                    <tr>
                                        <td class="center tr_select ">
                                            <?php echo $i; ?>
                                        </td>
                                        <td class="center">
                                            <?php if (isset($row->ride_id)) echo $row->ride_id; ?>
                                        </td>
                                        <td class="center">
                                            <?php
                                            $bookDateSec = $row->booking_information['booking_date']->sec;

                                            if (isset($row->booking_information['booking_date']))
                                                echo date('Y-m-d h:i A', $bookDateSec);
                                            ?>
                                        </td>
                                        <td class="center">
                                            <?php if (isset($row->ride_status)) 
																						echo get_language_value_for_keyword($row->ride_status,$this->data['langCode']); ?>
                                        </td>

                                        <?php
                                        if ($this->lang->line('dash_not_rated_yet') != '')
                                            $notrated = stripslashes($this->lang->line('dash_not_rated_yet'));
                                        else
                                            $notrated = 'Not rated yet';
                                        ?>

                                        <?php if ($actionRide == 'Completed') { ?>
                                            <td class="center">
                                                <?php
                                                if (isset($row->rider_review_status)) {
                                                    if ($row->rider_review_status == 'Yes') {
                                                        ?>
                                                        <?php echo $row->ratings['rider']['avg_rating']; ?>
                                                        <?php
                                                    } else {
                                                        echo $notrated;
                                                    }
                                                } else {
                                                    echo $notrated;
                                                }
                                                ?>
                                            </td>

                                            <td class="center">
                                                <?php
                                                if (isset($row->driver_review_status)) {
                                                    if ($row->driver_review_status == 'Yes') {
                                                        ?>
                                                        <?php echo $row->ratings['driver']['avg_rating']; ?>
                                                        <?php
                                                    } else {
                                                        echo $notrated;
                                                    }
                                                } else {
                                                    echo $notrated;
                                                }
                                                ?>
                                            </td>


                                        <?php } ?>


                                        <td class="center">
                                            <ul class="action_list">
                                                <li style="width:100%;">
                                                    <a class="p_car tipTop" href="track-ride?rideId=<?php echo $row->ride_id; ?>" target="_blank">
                                                        <?php
                                                        if ($this->lang->line('dash_track_ride') != '')
                                                            echo stripslashes($this->lang->line('dash_track_ride'));
                                                        else
                                                            echo 'Track Ride';
                                                        ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>


                                        <td class="center">
                                            <ul class="action_list"><li style="width:100%;"><a class="p_edit tipTop" href="driver/rides/view_ride_details/<?php echo $row->_id; ?>?act=<?php echo $this->input->get('act'); ?>"><?php
                                                        if ($this->lang->line('dash_view_details') != '')
                                                            echo stripslashes($this->lang->line('dash_view_details'));
                                                        else
                                                            echo 'View Details';
                                                        ?></a></li></ul>
                                        </td>
                                    </tr>
                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="center" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('dash_S_No') != '')
                                        echo stripslashes($this->lang->line('dash_S_No'));
                                    else
                                        echo 'S.No';
                                    ?>
                                </th>
                                <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('dash_ride_id') != '')
                                        echo stripslashes($this->lang->line('dash_ride_id'));
                                    else
                                        echo 'Ride Id';
                                    ?>
                                </th>
                                <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('dash_booked_date') != '')
                                        echo stripslashes($this->lang->line('dash_booked_date'));
                                    else
                                        echo 'Booked Date';
                                    ?>
                                </th>
                                <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('driver_status') != '')
                                        echo stripslashes($this->lang->line('driver_status'));
                                    else
                                        echo 'Status';
                                    ?>
                                </th>
                                <?php
                                $actionRide = $this->input->get('act');
                                if ($actionRide == 'Completed') {
                                    ?>
                                    <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                        <?php
                                        if ($this->lang->line('dash_rider_ratings') != '')
                                            echo stripslashes($this->lang->line('dash_rider_ratings'));
                                        else
                                            echo 'Rider Ratings';
                                        ?>
                                    </th>
                                    <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                        <?php
                                        if ($this->lang->line('dash_driver_ratings') != '')
                                            echo stripslashes($this->lang->line('dash_driver_ratings'));
                                        else
                                            echo 'Driver Ratings';
                                        ?>
                                    </th>
                                    <?php
                                }
                                ?>

                                <th class="tip_top" title="<?php echo $dash_click_sort; ?>">
                                    <?php
                                    if ($this->lang->line('dash_tack_ride') != '')
                                        echo stripslashes($this->lang->line('dash_tack_ride'));
                                    else
                                        echo 'Track Ride';
                                    ?>
                                </th>

                                <th>
                                    <?php
                                    if ($this->lang->line('dash_action') != '')
                                        echo stripslashes($this->lang->line('dash_action'));
                                    else
                                        echo 'Action';
                                    ?>
                                </th>

                            </tr>
                        </tfoot>
                    </table>
                    <?php
                    if ($paginationLink != '') {
                        echo $paginationLink;
                    }
                    ?>
                </div>
            </div>
        </div>
        <input type="hidden" name="statusMode" id="statusMode"/>
        <input type="hidden" name="SubAdminEmail" id="SubAdminEmail"/>
        </form>	

    </div>
    <span class="clear"></span>
</div>
</div>


<?php
$this->load->view('driver/templates/footer.php');
?>