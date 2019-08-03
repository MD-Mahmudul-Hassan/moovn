
<script src="js/site/jquery-ui-core-1.11.4.js"></script>

<footer>
   
    <div id="footerlast" class="footer-bg">
       
        <div class="container-new">
            <div class="col-sm-3 col-xs-12 footer-logo">
                <?php
				/*  =p
                if ($this->lang->line('home_cabily') != '')
                    $home_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                else
                    $home_cabily = $this->config->item('email_title');
                */
				?>
               <!--
                <img src="images/logo/<?php /*echo $this->config->item('logo_image'); */?>" alt="<?php /* echo $home_cabily */?>" width="180px">
                -->
            </div>
			<?php  /*if(!empty($footer_menu)){ ?>
            <div class="col-md-2 col-xs-12 col-sm-4 new_footer_added_class">
                <div class="footer-menus">
                    <h1><?php if ($this->lang->line('quick_links') != '') echo stripslashes($this->lang->line('quick_links')); else echo 'QUICK LINKS'; ?></h1>
                    <ul>
						<?php if($footer_home == 'yes'){ ?>
						  <li><a href="<?php echo base_url(); ?>">  <?php if ($this->lang->line('home_home') != '') echo stripslashes($this->lang->line('home_home')); else echo 'Home'; ?></a></li>
						<?php } ?>
						<?php foreach($footer_menu as $menu){ $url = $menu['url']; ?>
                        <li><a href="<?php echo base_url(); ?>pages/<?php echo $url; ?>"><?php echo $menu['name'] ?></a></li>
                       <?php } ?>
                    </ul>
                </div>
            </div>
			<?php } */?>
            <div class="col-md-3 col-xs-12 col-sm-4 new_footer_added_class">
                <div class="footer-menus">
                   <!-- =p
                    <h1><?php /*if ($this->lang->line('footer_newsletter') != '') echo stripslashes($this->lang->line('footer_newsletter')); else echo 'newsletter'; */?></h1>
                    <form action="">
                        <ul>
                            <li>
                                <input type="text" placeholder="<?php /*if ($this->lang->line('footer_name') != '') echo stripslashes($this->lang->line('footer_name')); else echo 'Name'; ?>" name="subscriber_name" id="subscriber_name" />
                            </li>
                            <li>
                                <input type="text" placeholder="<?php if ($this->lang->line('footer_email_address') != '') echo stripslashes($this->lang->line('footer_email_address')); else echo 'Email Address'; ?>" name="subscriber_email" id="subscriber_email" />
                            </li>
                            <li>
                                <span id="subscribeMsg"></span>
                            </li>
                            <li>
                                <input type="button" onclick="email_subscription();" value="<?php if ($this->lang->line('footer_submit') != '') echo stripslashes($this->lang->line('footer_submit')); else echo 'Submit'; */?>" class="home-submit" id="subscribe_btn">
                            </li>
                        </ul>
                    </form>
                    -->
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="col-md-12 footer-rider-top">
                    <div class="footer-menus app-ftr-menu">
                        <!-- =p
                        <h1><?php /* if ($this->lang->line('footer_our_apps') != '') echo stripslashes($this->lang->line('footer_our_apps')); else echo 'our apps'; ?></h1>
                        <p><?php if ($this->lang->line('footer_rider') != '') echo stripslashes($this->lang->line('footer_rider')); else echo 'RIDER'; ?></p>
                        <div class="col-md-12 android-rider">
                            <a href="#"><h5 class="android-icon"><?php if ($this->lang->line('footer_android') != '') echo stripslashes($this->lang->line('footer_android')); else echo 'ANDROID'; ?></h5></a>
                            <a href="#"><h5 class="apple-icon"><?php if ($this->lang->line('footer_ios') != '') echo stripslashes($this->lang->line('footer_ios')); else echo 'IOS'; ?></h5></a>
                        </div>
                    </div>
                    <div class="footer-menus app-ftr-menu">
                        <h1>&nbsp;</h1>
                        <p><?php if ($this->lang->line('footer_driver') != '') echo stripslashes($this->lang->line('footer_driver')); else echo 'DRIVER'; ?></p>
                        <div class="col-md-12 android-rider">
                            <a href="#"><h5 class="android-icon"><?php if ($this->lang->line('footer_android') != '') echo stripslashes($this->lang->line('footer_android')); else echo 'ANDROID'; ?></h5></a>
                            <a href="#"><h5 class="apple-icon"><?php if ($this->lang->line('footer_ios') != '') echo stripslashes($this->lang->line('footer_ios')); else echo 'IOS'; */ ?></h5></a>
                        </div>
                    </div>
                    -->
                </div>
				<div class="clearfix"></div>
                <div class="col-md-12 footer-rider-bottom">
                    <!--
                    <p><?php /* if ($this->lang->line('footer_follow_us') != '') echo stripslashes($this->lang->line('footer_follow_us')); else echo 'Follow Us'; ?></p>
                    <ul>

                        <?php if ($this->config->item('facebook_link') != '') { ?>
                            <li>
                                <a target="_blank" href="<?php echo $this->config->item('facebook_link'); ?>">
                                    <img src="images/facebook.png">
                                </a>			
                            </li>			
                        <?php } ?>

                        <?php if ($this->config->item('twitter_link') != '') { ?>
                            <li>
                                <a target="_blank" href="<?php echo $this->config->item('twitter_link'); ?>">
                                    <img src="images/tw.png">
                                </a>			
                            </li>
                        <?php } ?>


                        <?php if ($this->config->item('pinterest') != '') { ?>
                            <li>
                                <a target="_blank" href="<?php echo $this->config->item('pinterest'); ?>">
                                    <img src="images/pay.png">
                                </a>		
                            </li>
                        <?php } ?>


                        <?php if ($this->config->item('googleplus_link') != '') { ?>
                            <li>
                                <a target="_blank" href="<?php echo $this->config->item('googleplus_link'); ?>">
                                    <img src="images/google.png">
                                </a>		
                            </li>
                        <?php } */ ?>

                    </ul>
                    -->
                    
                </div>
            </div>
        </div>
        
    </div>

    <div class="footer-btm"><?php echo $this->config->item('footer_content'); ?></div> <!-- sshhhhh.... =p ->

</footer>




<?php echo $this->config->item('google_verification_code'); ?>
</body>
</html>