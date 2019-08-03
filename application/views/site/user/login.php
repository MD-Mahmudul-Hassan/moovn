<?php
$this->load->view('site/templates/common_header');

if (is_file('google-login-mats/index.php')){
	require_once 'google-login-mats/index.php';
}

?>
</head>

<body class="">
    <div class="dark focused">
        <div class="login_base">
            <div class="container-new">
                <div class="login_section col-md-5">
                    <div class="login_inner sign_in_inner">

                        <form name="rider_login_form" id="rider_login_form" action="site/user/rider_login" method="post" enctype="multipart/form-data">

                            <div class="text-center">
                                <a class="brand" href="">
                                    <?php if ($this->lang->line('home_cabily') != '') $home_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily'))); else $home_cabily = $this->config->item('email_title'); ?>
                                    <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $home_cabily; ?>">
                                </a>

                                <p><span><?php if ($this->lang->line('login_signin') != '') echo stripslashes($this->lang->line('login_signin')); else echo 'SIGN IN'; ?></span></p>
                                <div class="col-lg-12 text-left sign_driver_text">
                                    <label><?php if ($this->lang->line('rider_email') != '') echo stripslashes($this->lang->line('rider_email')); else echo 'EMAIL'; ?></label>
                                    <input type="text" class="form-control sign_in_text required email" name="emailAddr" placeholder="<?php if ($this->lang->line('rider_email_address') != '') echo stripslashes($this->lang->line('rider_email_address')); else echo 'Email Address'; ?>">
                                </div>
                                <div class="col-lg-12 text-left sign_driver_text">
                                    <label><?php if ($this->lang->line('rider_password') != '') echo stripslashes($this->lang->line('rider_password')); else echo 'PASSWORD'; ?></label>
                                    <input type="password" name="password" class="form-control sign_in_text required"  placeholder="<?php if ($this->lang->line('rider_password_lower') != '') echo stripslashes($this->lang->line('rider_password_lower')); else echo 'password'; ?>">
                                </div>

                                <div class="col-lg-12 text-left sign_driver_text">
                                    <input type="checkbox" name="stay_signed_in" id="stay_signed_in" name="stay_signed_in" value="yes" class="css-checkbox" />
                                    <label for="stay_signed_in" class="css-label"><?php if ($this->lang->line('rider_rember_me') != '') echo stripslashes($this->lang->line('rider_rember_me')); else echo 'Remember Me'; ?></label>
                                </div> 

                                <div class="col-lg-12 text-left sign_driver_text">
                                    <input type="submit" class=" btn1 sign_in_driver" value="<?php if ($this->lang->line('login_signin') != '') echo stripslashes($this->lang->line('login_signin')); else echo 'SIGN IN'; ?>">
                                </div>
                                <a href="rider/reset-password" class="tipLeft forgot_password" title="<?php if ($this->lang->line('user_click_to_reset') != '') echo stripslashes($this->lang->line('user_click_to_reset')); else echo 'Click to reset a new password'; ?>"><?php if ($this->lang->line('rider_forget_password') != '') echo stripslashes($this->lang->line('rider_forget_password')); else echo 'Forgot Password'; ?></a>  

								<div class="padd_fb_base">
                                <ul class="login_form_home_page home_page_login define_y">
                                    	<?php if ($this->config->item('facebook_app_id') != '' && $this->config->item('facebook_app_secret') != '') { ?> 
										<li class="new_homepage_login edit">
											<a href="<?php echo base_url().'facebook/user.php'; ?>" class="popup_facebook"><?php if ($this->lang->line('login_with_facebook') != '') echo stripslashes($this->lang->line('login_with_facebook')); else echo 'Login with Facebook'; ?></a>
										</li>
										<?php } ?>
										<?php if($this->config->item('google_client_id') != '' && $this->config->item('google_redirect_url') != '' && $this->config->item('google_client_secret') != '') { ?>
                                     	<li class="new_homepage_login edit">
											<a href="<?php echo $authUrl; ?>" class="popup_google"><?php if ($this->lang->line('login_with_google') != '') echo stripslashes($this->lang->line('login_with_google')); else echo 'Login with Google'; ?></a>
										</li>
										<?php } ?>
                                  </ul>
                                </div>
								
                                <div class="dont_have_acc">
                                    <big><?php if ($this->lang->line('rider_dont_have_account') != '') echo stripslashes($this->lang->line('rider_dont_have_account')); else echo 'Don\'t have an account?'; ?><a href="rider/signup"><?php if ($this->lang->line('home_signup') != '') echo stripslashes($this->lang->line('home_signup')); else echo 'Sign Up'; ?></a></big>  
                                </div>
								
                            </div>
                            <input type='hidden' value="<?php echo $this->input->get('action'); ?>" name="next_url"/>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#rider_login_form").validate();
        });
    </script>

</body>
</html>