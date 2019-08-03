<?php
if (isset($sideMenu)) {
    if ($sideMenu == '') {
        $sideMenu = '';
    }
} else {
    $sideMenu = '';
}
if (isset($rider_info->row()->image)) {
    if ($rider_info->row()->image != '') {
        $profilePic = base_url() . USER_PROFILE_IMAGE . $rider_info->row()->image;
    } else {
        $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
    }
} else {
    $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
}

$findpage = $this->uri->segment(2);
?>

<div class="col-md-3 profile_rider_left">
    <div class="profile_pic_rider text-center col-md-12">
        <a href="rider">
            <img src="<?php echo $profilePic; ?>" class="img-circle">
            <h6><?php echo $rider_info->row()->user_name;#ucfirst($this->session->userdata(APP_NAME.'_session_user_name')); ?></h6>
        </a>
    </div>

    <div class="profile-detail-menu col-md-12">
        <ul>
            <li <?php if ($sideMenu == 'bookride') { ?>class="active"<?php } ?>>
                <a href="rider/booking"><?php if ($this->lang->line('rider_book_ride') != '') echo stripslashes($this->lang->line('rider_book_ride')); else echo 'Book Ride'; ?></a>
            </li>
            <li <?php if ($sideMenu == 'rides') { ?>class="active"<?php } ?>>
                <a href="rider/my-rides"><?php if ($this->lang->line('rider_profile_my_rides') != '') echo stripslashes($this->lang->line('rider_profile_my_rides')); else echo 'My Rides'; ?></a>
            </li>
            <li <?php if ($sideMenu == 'ratecard') { ?>class="active"<?php } ?>>
                <a href="rider/rate-card"><?php if ($this->lang->line('trip_estimate') != '') echo stripslashes($this->lang->line('trip_estimate')); else echo 'Trip Estimate'; ?></a>
            </li>
            <?php
            if ($this->lang->line('rider_profile_cabily_money') != '')
                $cabily_money = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('rider_profile_cabily_money')));
            else
                $cabily_money = $this->config->item('email_title') . " Money";
            ?>
			
            <li <?php if ($sideMenu == 'wallet') { ?>class="active"<?php } ?>> <a href="rider/my-money"><?php if ($this->lang->line('mobile_money_balance') != '') echo stripslashes($this->lang->line('mobile_money_balance')); else echo 'Mobile Money Balance'; ?></a></li>
            <li <?php if ($sideMenu == 'share_earnings') { ?>class="active"<?php } ?>>
                <a href="rider/share-and-earnings"><?php if ($this->lang->line('rider_profile_share_earnings') != '') echo stripslashes($this->lang->line('rider_profile_share_earnings')); else echo 'Share & Earnings'; ?></a>
            </li>

            <li <?php if ($sideMenu == 'emergency') { ?>class="active"<?php } ?>><a href="rider/emergency-contact"><?php if ($this->lang->line('rider_profile_emergency_contact') != '') echo stripslashes($this->lang->line('rider_profile_emergency_contact')); else echo 'Emergency Contact'; ?></a></li>
			<li <?php if ($sideMenu == 'fav_locations') { ?>class="active"<?php } ?>><a href="rider/fav-location"><?php if ($this->lang->line('user_favourite_locations') != '') echo stripslashes($this->lang->line('user_favourite_locations')); else echo 'Favourite Locations'; ?></a></li>
			
            <li><a href="rider/logout"><?php if ($this->lang->line('rider_profile_logout') != '') echo stripslashes($this->lang->line('rider_profile_logout')); else echo 'Logout'; ?></a></li>

			<?php /* <li <?php if ($sideMenu == 'language_settings') { ?>class="active"<?php } ?>><a href="rider/language-settings">Language Settings</a> </li> */ ?>
        </ul>
    </div>
</div>