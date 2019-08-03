<?php
$currentUrl = $this->uri->segment(2, 0);
$currentPage = $this->uri->segment(3, 0);
if ($currentUrl == '') {
    $currentUrl = 'dashboard';
}
if ($currentPage == '') {
    $currentPage = 'dashboard';
}
?>
<div id="left_bar" >
    <div id="sidebar">
        <div id="secondary_nav">
            <ul id="sidenav" class="accordion_mnu collapsible">
                <div class="side_logo">
                    <?php
                    if ($this->lang->line('home_cabily') != '')
                        $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                    else
                        $sitename = $this->config->item('email_title');
                    ?>
                    <img src="images/logo/<?php echo $logo; ?>" alt="<?php echo $siteTitle; ?>" width="90px" title="<?php echo $sitename; ?>">
                </div>
                <li>
                    <a href="<?php echo base_url(); ?>driver/dashboard/driver_dashboard" <?php
                    if ($currentUrl == 'dashboard') {
                        echo 'class="active"';
                    }
                    ?>>
                        <span class="nav_icon computer_imac"></span> 
                        <?php
                        if ($this->lang->line('driver_dash') != '')
                            echo stripslashes($this->lang->line('driver_dash'));
                        else
                            echo 'Dashboard';
                        ?> 
                    </a>
                </li>
                <li>
                    <a href="driver/profile/edit_profile_form" <?php
                    if ($currentPage == 'edit_profile_form') {
                        echo 'class="active"';
                    }
                    ?>>
                        <span class="nav_icon admin_user"></span><?php
                        if ($this->lang->line('rider_profile_profile') != '')
                            echo stripslashes($this->lang->line('rider_profile_profile'));
                        else
                            echo 'Profile';
                        ?>
                    </a>
                </li>

                <li>
                    <a href="driver/profile/banking" <?php
                    if ($currentPage == 'banking') {
                        echo 'class="active"';
                    }
                    ?>>
                        <span class="nav_icon money"></span><?php
                        if ($this->lang->line('driver_banking') != '')
                            echo stripslashes($this->lang->line('driver_banking'));
                        else
                            echo 'Banking';
                        ?>
                    </a>
                </li>

                <li>
                    <a href="driver/profile/change_email_form" <?php
                    if ($currentPage == 'change_email_form') {
                        echo 'class="active"';
                    }
                    ?>>
                        <span class="nav_icon attach_2_co"></span><?php
                        if ($this->lang->line('driver_change_mail') != '')
                            echo stripslashes($this->lang->line('driver_change_mail'));
                        else
                            echo 'Change Email';
                        ?>
                    </a>
                </li>

                <li>
                    <a href="driver/profile/change_mobile_form" <?php
                    if ($currentPage == 'change_mobile_form') {
                        echo 'class="active"';
                    }
                    ?>>
                        <span class="nav_icon mobile_phone"></span><?php
                        if ($this->lang->line('driver_change_mob') != '')
                            echo stripslashes($this->lang->line('driver_change_mob'));
                        else
                            echo 'Change Mobile';
                        ?>
                    </a>
                </li>

                <li>
                    <a href="driver/profile/change_password_form" <?php
                    if ($currentPage == 'change_password_form') {
                        echo 'class="active"';
                    }
                    ?>>
                        <span class="nav_icon locked_2"></span><?php
                        if ($this->lang->line('driver_change_pwd') != '')
                            echo stripslashes($this->lang->line('driver_change_pwd'));
                        else
                            echo 'Change Password';
                        ?>
                    </a>
                </li>

                <?php $ride_action = $this->input->get('act'); ?>
                <li>
                    <a href="#" <?php
                    if ($currentUrl == 'rides') {
                        echo 'class="active"';
                    }
                    ?>>
                        <span class="nav_icon car"></span> <?php
                        if ($this->lang->line('admin_menu_rides') != '')
                            echo stripslashes($this->lang->line('admin_menu_rides'));
                        else
                            echo 'Rides';
                        ?><span class="up_down_arrow">&nbsp;</span>
                    </a>
                    <ul class="acitem" <?php
                    if ($currentUrl == 'rides') {
                        echo 'style="display: block;"';
                    } else {
                        echo 'style="display: none;"';
                    }
                    ?>>
                        <li>
                            <a href="driver/rides/display_rides?act=Upcoming" <?php
                            if ($ride_action == 'Upcoming') {
                                echo 'class="active"';
                            }
                            ?>>
                                <span class="list-icon">&nbsp;</span><?php
                                if ($this->lang->line('driver_upcoming') != '')
                                    echo stripslashes($this->lang->line('driver_upcoming'));
                                else
                                    echo 'Upcoming';
                                ?>
                            </a>
                        </li>
                        <li>
                            <a href="driver/rides/display_rides?act=OnRide" <?php
                            if ($ride_action == 'OnRide') {
                                echo 'class="active"';
                            }
                            ?>>
                                <span class="list-icon">&nbsp;</span><?php
                                if ($this->lang->line('driver_on_rides') != '')
                                    echo stripslashes($this->lang->line('driver_on_rides'));
                                else
                                    echo 'On Rides';
                                ?>
                            </a>
                        </li>
                        <li>
                            <a href="driver/rides/display_rides?act=Completed" <?php
                            if ($ride_action == 'Completed') {
                                echo 'class="active"';
                            }
                            ?>>
                                <span class="list-icon">&nbsp;</span><?php
                                if ($this->lang->line('driver_comp_rides') != '')
                                    echo stripslashes($this->lang->line('driver_comp_rides'));
                                else
                                    echo 'Completed Rides';
                                ?>
                            </a>
                        </li>
                        <li>
                            <a href="driver/rides/display_rides?act=Cancelled" <?php
                            if ($ride_action == 'Cancelled') {
                                echo 'class="active"';
                            }
                            ?>>
                                <span class="list-icon">&nbsp;</span><?php
                                if ($this->lang->line('driver_cancel_rides') != '')
                                    echo stripslashes($this->lang->line('driver_cancel_rides'));
                                else
                                    echo 'Cancelled Rides';
                                ?>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="driver/payments/display_payments" <?php
                    if ($currentPage == 'display_payments' || $currentPage == 'payment_summary') {
                        echo 'class="active"';
                    }
                    ?>>
                        <span class="nav_icon money"></span><?php
                        if ($this->lang->line('driver_earn') != '')
                            echo stripslashes($this->lang->line('driver_earn'));
                        else
                            echo 'Earnings';
                        ?>
                    </a>
                </li>


            </ul>
        </div>
    </div>
</div>


