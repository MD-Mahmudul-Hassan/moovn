<?php
$this->load->view('site/templates/common_header');
?>
</head>
<body class="">
    <div class="dark focused">
        <div class="login_base">
            <div class="container-new">
                <div class="login_section col-md-5">
                    <div class="login_inner sign_in_inner">

                        <form name="driver_login_form" id="driver_login_form" action="driver/profile/driver_login" method="post" enctype="multipart/form-data">

                            <div class="text-center">
                                <?php
                                if ($this->lang->line('home_cabily') != '')
                                    $home_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                                else
                                    $home_cabily = $this->config->item('email_title');
                                ?>
								 <a href="<?php echo base_url(); ?>">
                                <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $home_cabily; ?>">
								</a>
                                <p><span><?php
                                        if ($this->lang->line('login_signin') != '')
                                            echo stripslashes($this->lang->line('login_signin'));
                                        else
                                            echo 'SIGN IN';
                                        ?></span></p>
                                <div class="col-lg-12 text-left sign_driver_text">
                                    <label><?php
                                        if ($this->lang->line('rider_email') != '')
                                            echo stripslashes($this->lang->line('rider_email'));
                                        else
                                            echo 'EMAIL';
                                        ?></label>
                                    <input type="text" class="form-control sign_in_text required email" name="driver_name" placeholder="<?php
                                    if ($this->lang->line('rider_email_address') != '')
                                        echo stripslashes($this->lang->line('rider_email_address'));
                                    else
                                        echo 'Email Address';
                                    ?>">
                                </div>
                                <div class="col-lg-12 text-left sign_driver_text">
                                    <label><?php
                                        if ($this->lang->line('rider_password') != '')
                                            echo stripslashes($this->lang->line('rider_password'));
                                        else
                                            echo 'PASSWORD';
                                        ?></label>
                                    <input type="password" name="driver_password" class="form-control sign_in_text required"  placeholder="<?php
                                    if ($this->lang->line('rider_password_lower') != '')
                                        echo stripslashes($this->lang->line('rider_password_lower'));
                                    else
                                        echo 'password';
                                    ?>">
                                </div>
                                <?php /*
                                  <div class="col-lg-12 text-left sign_driver_text">
                                  <input type="checkbox" name="checkboxG1" id="checkboxG1" class="css-checkbox" />
                                  <label for="checkboxG1" class="css-label">Remember Me</label>
                                  </div>
                                 */ ?>
                                <div class="col-lg-12 text-left sign_driver_text">
                                    <input type="submit" class=" btn1 sign_in_driver" value="<?php
                                    if ($this->lang->line('login_signin') != '')
                                        echo stripslashes($this->lang->line('login_signin'));
                                    else
                                        echo 'SIGN IN';
                                    ?>">
                                </div>
                                <a href="driver/reset-password" class="tipLeft forgot_password" title="	<?php
                                if ($this->lang->line('driver_reset_pwd') != '')
                                    echo stripslashes($this->lang->line('driver_reset_pwd'));
                                else
                                    echo 'Click to reset a new password';
                                ?>"><?php
                                       if ($this->lang->line('rider_forget_password') != '')
                                           echo stripslashes($this->lang->line('rider_forget_password'));
                                       else
                                           echo 'Forgot Password';
                                       ?></a>   
                                <div class="dont_have_acc">
                                    <big><?php
                                        if ($this->lang->line('rider_dont_have_account') != '')
                                            echo stripslashes($this->lang->line('rider_dont_have_account'));
                                        else
                                            echo 'Don\'t have an account?';
                                        ?><a href="driver/signup">
                                            <?php
                                            if ($this->lang->line('home_signup') != '')
                                                echo stripslashes($this->lang->line('home_signup'));
                                            else
                                                echo 'Sign Up';
                                            ?></a></big>  
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#driver_login_form").validate();
        });
    </script>

</body>
</html>


