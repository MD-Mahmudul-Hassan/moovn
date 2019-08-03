<?php $pageFinder = $this->uri->segment(2); ?>
</head>
<body>
	<header class="banner-menu new-added-banner-menu">
		<div class="container-new">
			<?php 
			$c_fun= $this->router->fetch_method();
			$apply_function = array('track_ride_location_details');
			if(!in_array($c_fun,$apply_function)){
			?>
			<div class="login-signup">
				<ul>
					<li>
						<a class="sign-up"  href="login">
							<?php if ($this->lang->line('home_login') != '') echo stripslashes($this->lang->line('home_login')); else echo 'LOG IN'; ?>
						</a>
					</li>
					<li>
						<a class="sign-up" href="signup">
							<?php if ($this->lang->line('home_signup') != '') echo stripslashes($this->lang->line('home_signup')); else echo 'SIGN UP'; ?>
						</a>
					</li>
				</ul>
			</div>
			<?php } ?>
			
			<nav class="navbar navbar-default my-nav">
				<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
					    <a class="brand" href="<?php echo base_url(); ?>">
							<?php
							if ($this->lang->line('home_cabily') != '')
								$home_cabily = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
							else
								$home_cabily = $this->config->item('email_title');
							?>
							<img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $home_cabily; ?>">
						</a>
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>

					<?php if(!empty($header_menu)){ ?>
				<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<?php if($header_home == 'yes'){ ?>
								<li <?php if($pageFinder == '') echo 'class="active"';?>>
									<a href="<?php echo base_url(); ?>" <?php if($pageFinder == '') echo 'class="activemenu"';?>>
										Home
									</a>
								</li>
							<?php } ?>
							<?php foreach($header_menu as $menu){ $url = $menu['url']; ?>
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
