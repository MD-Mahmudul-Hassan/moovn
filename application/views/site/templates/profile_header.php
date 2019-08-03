<?php
$this->load->view('site/templates/common_header');
?> 	
</head>
<body class="">
    <div class="logo_sign_up text-center">
        <div class="main-logo">
            <a href="<?php echo base_url(); ?>rider">
                <?php
                if ($this->lang->line('home_cabily') != '')
                    $home_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                else
                    $home_cabily = $this->config->item('email_title');
                ?>
                <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $home_cabily; ?>">
            </a>
        </div>
        <div class="profile-sign-up">
            <a href="javascript:void(0);" class="profile-click">


                <?php
                if (isset($rider_info->row()->image)) {
                    if ($rider_info->row()->image != '') {
                        $profilePic = base_url() . USER_PROFILE_IMAGE . $rider_info->row()->image;
                    } else {
                        $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
                    }
                } else {
                    $profilePic = base_url() . USER_PROFILE_IMAGE_DEFAULT;
                }
                ?>
                <img src="<?php echo $profilePic; ?>">
                <span class="arrow-up"></span>
            </a>
        </div>
    </div>

    <div class="profile-slide-menu">
        <ul>

            <li><a href="rider/my-rides"<?php if ($sideMenu == 'rides') { ?>class="active"<?php } ?>> <?php if ($this->lang->line('rider_profile_my_rides') != '') echo stripslashes($this->lang->line('rider_profile_my_rides')); else echo 'My Rides'; ?></a></li>
            <li><a href="rider/rate-card" <?php if ($sideMenu == 'ratecard') { ?>class="active"<?php } ?> ><?php if ($this->lang->line('rider_profile_rate_card') != '') echo stripslashes($this->lang->line('rider_profile_rate_card')); else echo 'Rate Card'; ?></a></li>
            <?php
            if ($this->lang->line('rider_profile_cabily_money') != '')
                $cabily_money = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('rider_profile_cabily_money')));
            else
                $cabily_money = $this->config->item('email_title') . " Money";
            ?>
            <li><a href="rider/my-money" <?php if ($sideMenu == 'wallet') { ?>class="active"<?php } ?>><?php echo $cabily_money; ?> </a></li>
            <li><a href="rider/share-and-earnings" <?php if ($sideMenu == 'share_earnings') { ?>class="active"<?php } ?>><?php if ($this->lang->line('rider_profile_share_earnings') != '') echo stripslashes($this->lang->line('rider_profile_share_earnings')); else echo 'Share & Earnings'; ?></a></li>
            <li><a href="rider/emergency-contact" <?php if ($sideMenu == 'emergency') { ?>class="active"<?php } ?>><?php if ($this->lang->line('rider_profile_emergency_contact') != '') echo stripslashes($this->lang->line('rider_profile_emergency_contact')); else echo 'Emergency Contact'; ?></a></li>
            
			<li><a href="rider/fav-location" <?php if ($sideMenu == 'fav_locations') { ?>class="active"<?php } ?> ><?php if ($this->lang->line('user_favourite_locations') != '') echo stripslashes($this->lang->line('user_favourite_locations')); else echo 'Favourite Locations'; ?></a></li>
			
			<li><a href="rider/logout"><?php if ($this->lang->line('rider_profile_logout') != '') echo stripslashes($this->lang->line('rider_profile_logout')); else echo 'Logout'; ?></a></li>
			
        </ul>
    </div>
    <script>
        $(document).ready(function () {
            $(".profile-click").click(function () {
                $(".profile-slide-menu").slideToggle();
            });
        });
    </script>  