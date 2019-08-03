<?php
$this->load->view('site/templates/common_header');
?>

<link rel="stylesheet" href="css/site/screen.css">


</head>
<body class="">
    <div class="sign_up_cat_bg">
        <div class="container-new">
            <div class="text-center head_category">
                <a class="brand" href="">
                    <?php
                    if ($this->lang->line('home_cabily') != '')
                        $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                    else
                        $sitename = $this->config->item('email_title');
                    ?>
                    <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $sitename; ?>">
                </a>
                <h1 class="text-center"><?php
                    if ($this->lang->line('driver_earn_money') != '')
                        echo stripslashes($this->lang->line('driver_earn_money'));
                    else
                        echo 'EARN MONEY WITH YOUR CAR!';
                    ?></h1>
            </div>
        </div>
    </div>
    <div class="container-new">
        <div class="category_cont col-lg-12">
            <h2 class="text-center"><?php
                if ($this->lang->line('driver_your_vehicle') != '')
                    echo stripslashes($this->lang->line('driver_your_vehicle'));
                else
                    echo 'YOUR VEHICLE AND DRIVER TYPE!';
                ?></h2>
            <div class="col-lg-12 category_base text-center">

                <?php
                	if(!empty($categoryList)){
                foreach ($categoryList as $vehicles) {
                    ?>

                    <div class="col-lg-4 col-sm-12 col-xs-12 text-center vehicle-inner">
                        <div class="cate_bdr col-lg-12 nopadd">
                            <div class="category_inner">
                                <h3><?php echo $vehicles['name']; ?></h3>
                                <div class="col-lg-12 nopadd text-center">
                                    <?php
                                    if (isset($vehicles['image']) && $vehicles['image'] != '') {
                                        $cate_img_src = CATEGORY_IMAGE . $vehicles['image'];
                                    } else {
                                        $cate_img_src = CATEGORY_IMAGE_DEFAULT;
                                    }

                                    $carslist = @implode(', ', $vehicles['vehicle_type_names']);
                                    ?>
                                    <img src="<?php echo $cate_img_src; ?>" alt="<?php echo $vehicles['name']; ?>" title="<?php echo $vehicles['name']; ?>" width="180px">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <h4><?php
                                if ($this->lang->line('driver_your_vehicle_is') != '')
                                    echo stripslashes($this->lang->line('driver_your_vehicle_is'));
                                else
                                    echo 'Your Vehicle Is...';
                                ?></h4>
                            <?php if (count($vehicles['vehicle_type_names']) > 0) { ?>
                                <p><?php
                                    if ($this->lang->line('driver_the') != '')
                                        echo stripslashes($this->lang->line('driver_the'));
                                    else
                                        echo 'The';
                                    ?>  <?php echo $carslist; ?> <?php
                                    if ($this->lang->line('driver_will_come') != '')
                                        echo stripslashes($this->lang->line('driver_will_come'));
                                    else
                                        echo 'will comes under this category.';
                                    ?></p>
                            <?php } else { ?>
                                <p><?php
                                    if ($this->lang->line('driver_none_of_the_vehicles') != '')
                                        echo stripslashes($this->lang->line('driver_none_of_the_vehicles'));
                                    else
                                        echo 'None of the vehicles specified in this category';
                                    ?></p>
                            <?php } ?>
                            <a href="driver/register/<?php echo $this->uri->segment(3) . '/' . urlencode($vehicles['name']); ?>"><input type="button" class=" btn1 category_btn" value="<?php
                                if ($this->lang->line('driver_sign_up_for') != '')
                                    echo stripslashes($this->lang->line('driver_sign_up_for'));
                                else
                                    echo 'SIGN UP FOR';
                                ?> <?php echo strtoupper($vehicles['name']); ?>"></a>
                        </div>
                    </div>

                    <?php
                }
                }
                else{
							?>
							<h2 class="text-center">NO CATEGORIES AVAILABLE IN THIS LOCATION</h2>
							<?php } ?>


            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container-new">
        <div class="text-center">
            <div class="have_a_qstn col-lg-10">
                <h5 class="text-center"><?php
                    if ($this->lang->line('driver_have_a_question') != '')
                        echo stripslashes($this->lang->line('driver_have_a_question'));
                    else
                        echo 'HAVE A QUESTION? PLEASE';
                    ?> <span><a href="pages/contact-us" target="_blank"><?php
                            if ($this->lang->line('driver_contact_us') != '')
                                echo stripslashes($this->lang->line('driver_contact_us'));
                            else
                                echo 'CONTACT US';
                            ?></a></span> </h5>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="foot_catgory"></div>
</body>
</html>