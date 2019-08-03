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

                        <form name="driver_forget_pass_form" id="driver_forget_pass_form" action="site/user/update_reset_password" method="post" enctype="multipart/form-data">
                            <?php
                            if ($this->lang->line('home_cabily') != '')
                                $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                            else
                                $sitename = $this->config->item('email_title');
                            ?>
                            <div class="text-center">
								<a href="<?php echo base_url(); ?>">
									<img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $sitename; ?>">
								</a>
                                <p><span><?php if ($this->lang->line('user_reset_password') != '') echo stripslashes($this->lang->line('user_reset_password')); else echo 'RESET PASSWORD'; ?></span></p>
                                <div class="col-lg-12 text-left sign_driver_text">
                                    <label><?php if ($this->lang->line('user_new_password_upper') != '') echo stripslashes($this->lang->line('user_new_password_upper')); else echo 'New Password '; ?></label>
                                    <input name="new_password" id="new_password" type="password" tabindex="2" class="form-control sign_in_text required" title="<?php if ($this->lang->line('user_please_enter_new_password') != '') echo stripslashes($this->lang->line('user_please_enter_new_password')); else echo 'Please enter a new password'; ?>" minlength="6" />
                                </div>

                                <div class="col-lg-12 text-left sign_driver_text">
                                    <label><?php if ($this->lang->line('user_retype_password') != '') echo stripslashes($this->lang->line('user_retype_password')); else echo 'Retype Password'; ?></label>
                                    <input name="confirm_password" id="confirm_password" type="password" tabindex="2" class="form-control sign_in_text required" title="<?php if ($this->lang->line('user_enter_password_again') != '') echo stripslashes($this->lang->line('user_enter_password_again')); else echo 'Please re-enter your new password again'; ?>" equalto="#new_password" minlength="6" />
                                </div>
                                <input type="hidden" name="reset_id" value="<?php echo $reset_id; ?>" />
                                <div class="col-lg-12 text-left sign_driver_text">
                                    <input type="submit" class=" btn1 sign_in_driver" value="<?php if ($this->lang->line('user_submit_upper') != '') echo stripslashes($this->lang->line('user_submit_upper')); else echo 'SUBMIT'; ?>">
                                </div>
                                <a href="rider/login" class="tipLeft forgot_password" title="<?php if ($this->lang->line('user_click_to_go_back') != '') echo stripslashes($this->lang->line('user_click_to_go_back')); else echo 'Click to go back login page'; ?>"><?php if ($this->lang->line('user_back_to_login') != '') echo stripslashes($this->lang->line('user_back_to_login')); else echo 'Back to login'; ?></a>   			
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#driver_forget_pass_form").validate();
        });
    </script>

</body>
</html>
