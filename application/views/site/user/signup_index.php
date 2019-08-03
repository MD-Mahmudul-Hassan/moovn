<?php
$this->load->view('site/templates/common_header');
?>
</head>
<?php
if ($this->lang->line('home_cabily') != '')
	$sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
else
	$sitename = $this->config->item('email_title');
?>
<body class="">
    <div class="dark focused">
        <div class="login_base">
            <div class="container-new">
                <div class="login_section col-md-5">
                    <div class="login_inner">
                        <div class="text-center">
                            <a class="brand" href="">
                                <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php $sitename; ?>">
                            </a>
                            <p><span><?php if ($this->lang->line('home_signup') != '') echo stripslashes($this->lang->line('home_signup')); else echo 'SIGN UP'; ?></span></p>
                            <a href="rider/signup"><input type="button" class=" btn1 login_ride" value="<?php if ($this->lang->line('user_signup_as_a_rider') != '') echo stripslashes($this->lang->line('user_signup_as_a_rider')); else echo 'SIGN UP AS A RIDER'; ?>"></a>
                            <a href="driver/signup"><input type="button" class=" btn1 login_driver" value="<?php if ($this->lang->line('user_signup_as_a_driver') != '') echo stripslashes($this->lang->line('user_signup_as_a_driver')); else echo 'SIGN UP AS A DRIVER'; ?>"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


