<?php //echo "<pre>";print_r($header_menu);die;
$pageFinder = $this->uri->segment(2);
$this->load->view('site/templates/common_header');
?>
	<!--animation effects-->
	<link rel="stylesheet" type="text/css" href="css/site/animate.css" />
	<link rel="stylesheet" type="text/css" href="css/site/demo.css" />

	<!--search-effects-->
	<script>
		window.console = window.console || function (t) {
		};
		window.open = function () {
			console.log("window.open is disabled.");
		};
		window.print = function () {
			console.log("window.print is disabled.");
		};
	</script>
	<script src="js/site/prefixfree.js"></script>
	<!--slider-->
	<link rel="stylesheet" href="css/site/screen.css">
	<script type="text/javascript">
		$(function () {
			$('a[href*=#]:not([href=#])').click(function () {
				if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
					var target = $(this.hash);
					target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
					if (target.length) {
						$('html,body').animate({
							scrollTop: target.offset().top
						}, 1000);
						return false;
					}
				}
			});
		});
	</script>	
</head>
<body id="landing-pg">
    <div id="wrapper"> 
        <header class="banner-menu">
            <div class="container-new">
                <div class="login-signup">
				
                    <ul>
						   
										 
                        <li>
                        <!--
							<a href="login">
								<?php /* if ($this->lang->line('home_login') != '') echo stripslashes($this->lang->line('home_login')); else echo 'LOG IN'; ?>
                            </a>
						</li>
                        <li>
							<a class="sign-up" href="signup">
                                <?php if ($this->lang->line('home_signup') != '') echo stripslashes($this->lang->line('home_signup')); else echo 'SIGN UP'; */ ?>
                            </a>
                          -->
						</li>
                    </ul>

                    <?php
                    if ($languageList->num_rows() > 1) {
                        ?>
                        <!-- lang drop-down-toggle-->
                        <div class="lang-select">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" id="menu1" type="button" data-toggle="dropdown"><?php echo $langName; ?>
                                    <span class="caret"></span></button>
                                <ul class="dropdown-menu header-drop" role="menu" aria-labelledby="menu1">
                                    <?php
                                    foreach ($languageList->result() as $lang) {
                                        ?>
                                        <li role="presentation">
                                            <a role="menuitem" tabindex="-1" href="language-settings?q=<?php 	echo $lang->lang_code; ?>">
                                                <?php echo $lang->name; ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <!-- drop-down-toggle-end-->
                        <?php
                    }
                    ?>
                </div>
                
                
				<nav class="navbar navbar-default my-nav">
					<div class="container-fluid">
						<!-- Brand and toggle get grouped for better mobile display -->
						<div class="navbar-header">
						  
						   <a class="brand" href="<?php echo base_url();?>">
								<?php
								if ($this->lang->line('home_cabily') != '')
									$home_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
								else
									$home_cabily = $this->config->item('email_title');
								?>
								<img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $home_cabily; ?>">
							</a>
							<!--  =p
							
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							--> <!-- =p -->
						</div>

					<?php if(!empty($header_menu)){ ?>
				<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<?php if($header_home == 'yes'){ ?>
								<li <?php if($pageFinder == '') echo 'class="active"';?>>
									<a href="<?php echo base_url(); ?>" <?php if($pageFinder == '') echo 'class="activemenu"';?>>
									
                                   <?php if ($this->lang->line('home_home') != '') echo stripslashes($this->lang->line('home_home')); else echo 'Home'; ?>
									</a>
								</li>
							<?php }?>
							<?php foreach($header_menu as $menu){
                
							$url = $menu['url']; ?>
							<li <?php if($pageFinder == $url) echo 'class="active"';?>>
								<a href="pages/<?php echo $url; ?>" <?php if($pageFinder == $url) echo 'class="activemenu"';?>>
									<?php echo $menu['name'] ?>
								</a>
							</li>
							<?php } ?>
							
						</ul>
					</div><!-- /.navbar-collapse -->
					<?php } ?>
					</div><!-- /.container-fluid -->
				</nav>
            </div>
        </header>

        <div class="banner">
                <!--<img src="images/site/banner.png">-->
            <!-- SVG Arrows -->
            <div class="svg-wrap">
                <svg width="64" height="64" viewBox="0 0 64 64">
                <path id="arrow-left" d="M46.077 55.738c0.858 0.867 0.858 2.266 0 3.133s-2.243 0.867-3.101 0l-25.056-25.302c-0.858-0.867-0.858-2.269 0-3.133l25.056-25.306c0.858-0.867 2.243-0.867 3.101 0s0.858 2.266 0 3.133l-22.848 23.738 22.848 23.738z" />
                </svg>
                <svg width="64" height="64" viewBox="0 0 64 64">
                <path id="arrow-right" d="M17.919 55.738c-0.858 0.867-0.858 2.266 0 3.133s2.243 0.867 3.101 0l25.056-25.302c0.858-0.867 0.858-2.269 0-3.133l-25.056-25.306c-0.858-0.867-2.243-0.867-3.101 0s-0.858 2.266 0 3.133l22.848 23.738-22.848 23.738z" />
                </svg>
            </div>

            <div class="sleekslider">
                <!-- Slider Pages -->
                <?php      
				$thumbnails = array();
				$labels = array();
                if($banner->num_rows() >0){
					foreach($banner->result() as $row){
					$thumbnails[] = "'banner/thumbnail-$row->image'";
					$labels[] = "'$row->name'";
                ?>
                <div class="slide active new_banner_slide" style="background-image: url('images/banner/<?php echo $row->image; ?>'); background-size:cover;background-position:none;">
                    <div class="slide-container">
                        <div class="slide-content">     
                            <h1 class="bounceInLeft" style="text-align:left;"><?php /*echo $row->banner_title; */ ?> </h1> <!-- =p -->
                          	
 <!-- new code -->           
                         	              	
                          	<div class="hero-flex">
								<div class="box sign-up"><a href="<?php echo $this->config->config['base_url']; ?>signup">SIGN UP</a></div>
							  <div class="box login"><a href="<?php echo $this->config->config['base_url']; ?>login">LOGIN</a></div></div>
							</div>
							
							
<!-- =p -->
                        </div>
                    </div>
                </div>
               <?php
				} 
			   }else{ ?>
                <div class="slide active" style="background-image:url('images/banner/default.jpg');background-size:cover;
background-position:none;">
                    <div class="slide-container">
                        <div class="slide-content"> 
							<?php /* ?>
                            <h1 class="bounceInLeft" style="text-align:left;"><?php echo $this->config->item('email_title'); ?></h1><!-- =p -->
							<?php */  ?>
                        </div>
                    </div>
                </div>
			   <?php } ?>
                <!-- Navigation Arrows with Thumbnails -->
                <nav class="nav-split">
                    <a class="prev" href="">
                        <span class="icon-wrap">
							<svg class="icon" width="22" height="22" viewBox="0 0 64 64">
								<use xlink:href="#arrow-left" />
							</svg>
                        </span>
                        <div>
                            <h3>Prev</h3>
                            <img alt="Previous thumb thumbnails"/>
                        </div>
                    </a>
                    <a class="next" href="">
                        <span class="icon-wrap">
							<svg class="icon" width="22" height="22" viewBox="0 0 64 64">
								<use xlink:href="#arrow-right" />
							</svg>
						</span>
                        <div>
                            <h3>Next</h3>
                            <img alt="Next thumb"/>
                        </div>
                    </a>
                </nav>
                <!-- Pagination -->
                <nav class="pagination">
                    <span class="current"></span>
                    <span></span>
                </nav>
            </div>
        </div>
    </div>

	<?php 
	$default_lang=$dLangCode;
	$selected_lang=$langCode;
	$landing_content='';
	if($landing_details->num_rows()>0){
		if(($default_lang == $selected_lang) && $default_lang=='en'){
			if(isset($landing_details->row()->landing_page_content)){
				$landing_content=$landing_details->row()->landing_page_content;
			}
		}else if($selected_lang=='en'){
			if(isset($landing_details->row()->landing_page_content)){
				$landing_content=$landing_details->row()->landing_page_content;
			}
		}else{
			if(isset($landing_details->row()->$selected_lang)){
				$cont = $landing_details->row()->$selected_lang;
				$landing_content=$cont['landing_page_content'];
			}
		}
	}
	echo stripslashes($landing_content); 
	?>
 
 
   <?php /*  <script src="js/site/jquery.min.js"></script>  */ ?>
    <script src="js/site/viewportchecker.js"></script>



    <!--left-right-->
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('.bounceInLeft').addClass("hidden1").viewportChecker({
                classToAdd: 'visible1 animated bounceInLeft', // Class to add to the elements when they are visible
                offset: 100
            });
        });
    </script>




    <script src="js/site/stopExecutionOnTimeout-6c99970ade81e43be51fa877be0f7600.js"></script>
    <script>
        if (document.location.search.match(/type=embed/gi)) {
            window.parent.postMessage("resize", "*");
        }
    </script>

    <!-- drop down-->
    <script>
        $(document).ready(function () {
            $(".dropdown-toggle").dropdown();
        });		
    </script>
	
	<?php if(!empty($thumbnails)){ ?>
    <script>	
		$(document).ready(function(){
			$('.sleekslider').sleekslider({
				thumbs: [<?php echo @implode($thumbnails,',') ?>],
				labels:[<?php echo @implode($labels,',') ?>],
				speed: 6000
			});
		})
    </script>
	<?php }else{ ?>
    <script>	
		$(document).ready(function(){
			$('.sleekslider').sleekslider({
				thumbs: ['banner/thumbnail-default.jpg'],
				labels:['Slide'],
				speed: 6000
			});
		})
    </script>
	<?php } ?>
    <!---dropdown-end--->
    <script type="text/javascript" src="js/site/sleekslider.min.js"></script>
    <script type="text/javascript" src="js/site/app.js"></script>

<?php
$this->load->view('site/templates/footer');
?>