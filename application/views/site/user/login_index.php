<?php
$this->load->view('site/templates/common_header');
?>
</head>
<?php
if ($this->lang->line('home_cabily') != '')
	$home_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
else
	$home_cabily = $this->config->item('email_title');
?>
<body class="">
    <div class="dark focused">
        <div class="login_base">
            <div class="container-new">
                <div class="login_section col-md-5">
                    <div class="login_inner">
                        <div class="text-center">
                            <a class="brand" href="">
                                <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $home_cabily; ?>">
                            </a>
                            <p><span><?php if ($this->lang->line('home_login') != '') echo stripslashes($this->lang->line('home_login')); else echo 'LOG IN'; ?></span></p>
                            <a href="rider/login"><input type="button" class=" btn1 login_ride" value="<?php if ($this->lang->line('login_login_as_rider') != '') echo stripslashes($this->lang->line('login_login_as_rider')); else echo 'LOG IN AS A RIDER'; ?>"></a>
                            <a href="driver/login"><input type="button" class=" btn1 login_driver" value="<?php if ($this->lang->line('login_login_as_driver') != '') echo stripslashes($this->lang->line('login_login_as_driver')); else echo 'LOG IN AS A DRIVER'; ?>"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>