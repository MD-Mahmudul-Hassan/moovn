<?php
$this->load->view('admin/templates/header.php');
?>
<div id="content">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_wrap tabby">
                    <div class="widget_top"> 
                        <span class="h_icon list"></span>
                        <h6><?php echo $heading; ?></h6>
                    </div>
                    <div class="widget_content">
                        <?php
                        $attributes = array('class' => 'form_container left_label', 'id' => 'dispatch_settings_form');
                        echo form_open('admin/adminlogin/admin_dispatch_settings', $attributes)
                        ?>
                        <div id="tab4">
                            <ul>
<!--                                 <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="offer_interval">Offer Interval</label>
                                        <div class="form_input">
                                            <?php
                                            $offer_interval = '';
                                            if (isset($dispatch_settings->offer_interval)) {
                                                $offer_interval = $dispatch_settings->offer_interval;
                                            }
                                            ?>
                                            <input name="dispatch_settings[offer_interval]" id="offer_interval" type="text" value="<?php echo $offer_interval; ?>" tabindex="1" class="large tipTop" title="Offer Interval" /> seconds
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['offer_interval']) ? $errors['offer_interval']: ''); ?></div>
                                        </div>
                                    </div>
                                </li> -->

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="auto_re_dispatch">Auto Re-Dispatch</label>
                                        <div class="form_input">
                                            <?php
                                            $auto_re_dispatch = '';
                                            if (isset($dispatch_settings->auto_re_dispatch)) {
                                                $auto_re_dispatch = $dispatch_settings->auto_re_dispatch;
                                            }
                                            ?>
                                            <input name="dispatch_settings[auto_re_dispatch]" id="auto_re_dispatch" type="text" value="<?php echo $auto_re_dispatch; ?>" tabindex="2" class="large tipTop" title="Auto Re-Dispatch" /> seconds
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['auto_re_dispatch']) ? $errors['auto_re_dispatch']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="max_auto_dispatch">Maximum Auto-Dispatch</label>
                                        <div class="form_input">
                                            <?php
                                            $max_auto_dispatch = '';
                                            if (isset($dispatch_settings->max_auto_dispatch)) {
                                                $max_auto_dispatch = $dispatch_settings->max_auto_dispatch;
                                            }
                                            ?>
                                            <input name="dispatch_settings[max_auto_dispatch]" id="max_auto_dispatch" type="text" value="<?php echo $max_auto_dispatch; ?>" tabindex="3" class="large tipTop" title="Max Auto-Dispatch" /> times
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['max_auto_dispatch']) ? $errors['max_auto_dispatch']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="time_out">Time Out</label>
                                        <div class="form_input">
                                            <?php
                                            $time_out = '';
                                            if (isset($dispatch_settings->time_out)) {
                                                $time_out = $dispatch_settings->time_out;
                                            }
                                            ?>
                                            <input name="dispatch_settings[time_out]" id="time_out" type="text" value="<?php echo $time_out; ?>" tabindex="4" class="large tipTop" title="Time Out" /> mins
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['time_out']) ? $errors['time_out']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="driver_reminder">Driver Reminder</label>
                                        <div class="form_input">
                                            <?php
                                            $driver_reminder = '';
                                            if (isset($dispatch_settings->driver_reminder)) {
                                                $driver_reminder = $dispatch_settings->driver_reminder;
                                            }
                                            ?>
                                            <input name="dispatch_settings[driver_reminder]" id="driver_reminder" type="text" value="<?php echo $driver_reminder; ?>" tabindex="5" class="large tipTop" title="Reminder" /> hours
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['driver_reminder']) ? $errors['driver_reminder']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="rider_reminder">Rider Reminder</label>
                                        <div class="form_input">
                                            <?php
                                            $rider_reminder = '';
                                            if (isset($dispatch_settings->rider_reminder)) {
                                                $rider_reminder = $dispatch_settings->rider_reminder;
                                            }
                                            ?>
                                            <input name="dispatch_settings[rider_reminder]" id="rider_reminder" type="text" value="<?php echo $rider_reminder; ?>" tabindex="5" class="large tipTop" title="Reminder" /> hours
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['rider_reminder']) ? $errors['rider_reminder']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="min_book_ahead_time">Minimum Book Ahead Time</label>
                                        <div class="form_input">
                                            <?php
                                            $min_book_ahead_time = '';
                                            if (isset($dispatch_settings->min_book_ahead_time)) {
                                                $min_book_ahead_time = $dispatch_settings->min_book_ahead_time;
                                            }
                                            ?>
                                            <input name="dispatch_settings[min_book_ahead_time]" id="min_book_ahead_time" type="text" value="<?php echo $min_book_ahead_time; ?>" tabindex="6" class="large tipTop" title="Minimum Book Ahead Time" /> hrs
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['min_book_ahead_time']) ? $errors['min_book_ahead_time']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="max_book_ahead_time">Maximum Book Ahead Time</label>
                                        <div class="form_input">
                                            <?php
                                            $max_book_ahead_time = '';
                                            if (isset($dispatch_settings->max_book_ahead_time)) {
                                                $max_book_ahead_time = $dispatch_settings->max_book_ahead_time;
                                            }
                                            ?>
                                            <input name="dispatch_settings[max_book_ahead_time]" id="max_book_ahead_time" type="text" value="<?php echo $max_book_ahead_time; ?>" tabindex="7" class="large tipTop" title="Maximum Book Ahead Time" /> days
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['max_book_ahead_time']) ? $errors['max_book_ahead_time']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="driver_cancellation_duration">Driver Cancellation Duration</label>
                                        <div class="form_input">
                                            <?php
                                            $driver_cancellation_duration = '';
                                            if (isset($dispatch_settings->driver_cancellation_duration)) {
                                                $driver_cancellation_duration = $dispatch_settings->driver_cancellation_duration;
                                            }
                                            ?>
                                            <input name="dispatch_settings[driver_cancellation_duration]" id="driver_cancellation_duration" type="text" value="<?php echo $driver_cancellation_duration; ?>" tabindex="8" class="large tipTop" title="Driver Cancellation Duration" /> hrs
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['driver_cancellation_duration']) ? $errors['driver_cancellation_duration']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="driver_dispatch_1">Closest Driver Radius 1</label>
                                        <div class="form_input">
                                            <?php
                                            $driver_dispatch_1 = '';
                                            if (isset($dispatch_settings->driver_dispatch_1)) {
                                                $driver_dispatch_1 = $dispatch_settings->driver_dispatch_1;
                                            }
                                            ?>
                                            <input name="dispatch_settings[driver_dispatch_1]" id="driver_dispatch_1" type="text" value="<?php echo $driver_dispatch_1; ?>" tabindex="8" class="large tipTop" title="Closest Driver Radius 1" /> mins
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['driver_dispatch_1']) ? $errors['driver_dispatch_1']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="driver_dispatch_2">Closest Driver Radius 2</label>
                                        <div class="form_input">
                                            <?php
                                            $driver_dispatch_2 = '';
                                            if (isset($dispatch_settings->driver_dispatch_2)) {
                                                $driver_dispatch_2 = $dispatch_settings->driver_dispatch_2;
                                            }
                                            ?>
                                            <input name="dispatch_settings[driver_dispatch_2]" id="driver_dispatch_2" type="text" value="<?php echo $driver_dispatch_2; ?>" tabindex="8" class="large tipTop" title="Closest Driver Radius 2" /> mins
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['driver_dispatch_2']) ? $errors['driver_dispatch_2']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="driver_dispatch_3">Closest Driver Radius 3</label>
                                        <div class="form_input">
                                            <?php
                                            $driver_dispatch_3 = '';
                                            if (isset($dispatch_settings->driver_dispatch_3)) {
                                                $driver_dispatch_3 = $dispatch_settings->driver_dispatch_3;
                                            }
                                            ?>
                                            <input name="dispatch_settings[driver_dispatch_3]" id="driver_dispatch_3" type="text" value="<?php echo $driver_dispatch_3; ?>" tabindex="8" class="large tipTop" title="Closest Driver Radius 3" /> mins
                                            <div class="dispatch-settings-form-error"><?php echo (isset($errors['driver_dispatch_3']) ? $errors['driver_dispatch_3']: ''); ?></div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <button type="submit" class="btn_small btn_blue" tabindex="9"><span>
                                                <?php
                                                if ($this->lang->line('admin_settings_submit') != '') :
                                                    echo stripslashes($this->lang->line('admin_settings_submit'));
                                                else :
                                                    echo 'Submit';
                                                endif;
                                                ?></span></button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span> 
</div>
<?php
$this->load->view('admin/templates/footer.php');
