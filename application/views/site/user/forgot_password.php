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

                        <form name="driver_forget_pass_form" id="driver_forget_pass_form" action="site/user/user_forgot_password" method="post" enctype="multipart/form-data">

                            <div class="text-center">
                                <?php 
								if ($this->lang->line('home_cabily') != '')
                                    $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                                else
                                    $sitename = $this->config->item('email_title');
                                ?>
								 <a href="<?php echo base_url(); ?>">
									<img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $sitename; ?>">
								</a>
                                <p><span><?php if ($this->lang->line('rider_forget_password') != '') echo stripslashes($this->lang->line('rider_forget_password')); else echo 'FORGOT PASSWORD'; ?></span></p>
                                <div class="col-lg-12 text-left sign_driver_text">
                                    <label><?php if ($this->lang->line('cms_email') != '') echo stripslashes($this->lang->line('cms_email')); else echo 'EMAIL'; ?></label>
                                    <input type="text" class="form-control sign_in_text required email" name="email" placeholder=" <?php if ($this->lang->line('rider_email_address') != '') echo stripslashes($this->lang->line('rider_email_address')); else echo 'Email Address'; ?>">
                                </div>

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