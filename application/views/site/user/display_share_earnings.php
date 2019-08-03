<?php
$this->load->view('site/templates/profile_header');

if(isset($rider_info->row()->currency_symbol)){
	$dcurrencySymbol=$rider_info->row()->currency_symbol;
}
?> 
<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">

                <!-------Profile side bar ---->

                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>
                <div class="col-md-9 nopadding profile_rider_right">
                    <div class="col-md-12 rider-pickup-detail">
                        <h2><?php if ($this->lang->line('home_share_earn') != '') echo stripslashes($this->lang->line('home_share_earn')); else echo 'SHARE AND EARN'; ?></h2>
                        <div class="col-md-12 friend-earn">
                            <p><?php if ($this->lang->line('user_friend_joins_earns') != '') echo stripslashes($this->lang->line('user_friend_joins_earns')); else echo 'Friend joins, friend earns'; ?><strong> <?php echo $dcurrencySymbol; ?><?php echo number_format($location_data->welcome_amount, 2); ?></strong>
                            </p>
                            <p>                            
                            <?php if ($location_data->referral_credit_type == 'instant') { ?>
							<?php if ($this->lang->line('user_share_friend_join_earn') != '') echo stripslashes($this->lang->line('user_share_friend_join_earn')); else echo 'Friend joins, you earns'; ?>
                            <?php } else {?>
							<?php if ($this->lang->line('user_share_friend_ride_you_earn') != '') echo stripslashes($this->lang->line('user_share_friend_ride_you_earn')); else echo 'Friend rides, you earns'; ?>
                            <?php } ?>
                                <strong> <?php echo $dcurrencySymbol; ?><?php echo number_format($location_data->referral_credit, 2); ?></strong>
                            </p>
                            <img src="images/site/Car-1.png">
                            <div class="referral-code">
                                <p><?php if ($this->lang->line('user_share_referral_code') != '') echo stripslashes($this->lang->line('user_share_referral_code')); else echo 'Share your referral code'; ?></p>
                                <h3><?php echo $rider_info->row()->unique_code; ?></h3>
                            </div>
                        </div>
                        <div class="col-md-12 share-all">
                            <h5><?php if ($this->lang->line('user_let_the_world_know') != '') echo stripslashes($this->lang->line('user_let_the_world_know')); else echo 'Let the world know'; ?></h5>
                            <ul class="share_wrap">
                                <li>
                                    <a href="http://www.facebook.com/sharer.php?u=<?php echo base_url() . 'rider/signup?ref=' . base64_encode($rider_info->row()->unique_code); ?>" onclick="window.open('http://www.facebook.com/sharer.php?u=<?php echo base_url() . 'rider/signup?ref=' . base64_encode($rider_info->row()->unique_code); ?>', 'popup', 'height=500px, width=400px');
                                            return false;">
                                           <?php if ($this->lang->line('user_facebook') != '') echo stripslashes($this->lang->line('user_facebook')); else echo 'Facebook'; ?> 
                                    </a>
                                </li>
                                <li>
                                    <a href="http://twitter.com/share?text=<?php echo $shareDesc; ?>&url=<?php echo base_url() . 'rider/signup?ref=' . base64_encode($rider_info->row()->unique_code); ?>" target=" _blank" onclick="window.open('http://twitter.com/share?text=<?php echo $shareDesc; ?>&url=<?php echo base_url() . 'rider/signup?ref=' . base64_encode($rider_info->row()->unique_code); ?>', 'popup', 'height=500px, width=400px');
                                            return false;">
                                           <?php if ($this->lang->line('user_twitter') != '') echo stripslashes($this->lang->line('user_twitter')); else echo 'Twitter'; ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>                
<?php
$this->load->view('site/templates/footer');
?> 