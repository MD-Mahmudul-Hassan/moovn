<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php if ($sideMenu == 'share_code') { ?>
            <?php /* <meta property="og:site_name" content="<?php echo $this->config->item('email_title'); ?>"/> */ ?>
            <meta property="og:type" content="website"/>
            <meta property="og:url" content="<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"/>
            <meta property="og:title" content="Signup with my code."/>
            <meta property="og:image" content="<?php echo base_url() . 'images/site/track/share.png'; ?>"/>
            <meta property="og:image:width" content="100" />
            <meta property="og:image:height" content="100" />
            <meta property="og:description" content="<?php echo $shareDesc; ?>"/>
        <?php } ?>
        <meta name="Title" content="<?php if ($meta_title == '') { echo $this->config->item('meta_title'); } else { echo $meta_title; } ?>" />  
        <base href="<?php echo base_url(); ?>" />
        <?php
        if ($this->config->item('google_verification')) {
            echo stripslashes($this->config->item('google_verification'));
        }
        if ($heading == '') { ?>    
            <title><?php echo $title; ?></title>
        <?php } else { ?>
            <title><?php echo $heading; ?></title>
        <?php } ?>

        <meta name="Title" content="<?php if ($meta_title == '') { echo $this->config->item('meta_title'); } else { echo $meta_title; } ?>" />
        <meta name="keywords" content="<?php if ($meta_keyword == '') { echo $this->config->item('meta_keyword'); } else { echo $meta_keyword; } ?>" />
        <meta name="description" content="<?php if ($meta_description == '') { echo $this->config->item('meta_description'); } else { echo $meta_description; } ?>" />
		<?php
		if (isset($meta_abstraction)){
		  if ($meta_abstraction == '') {
			  echo "<!-- " . $this->config->item('meta_abstraction') . " --><cmt>";
		  } else {
			  echo "<!-- " . $meta_abstraction . " --><cmt>";
		  }
		}
		?>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url() . 'images/site/track/logo/' . $this->config->item('favicon_image'); ?>">    

		 <link rel="stylesheet" href="css/site/bootstrap.css"type="text/css" />
		<link rel="stylesheet" href="css/site/track/style.css">
		<script type="text/javascript" src="js/site/jquery-1.10.2.js"></script>
		<script type="text/javascript" src="js/site/bootstrap.js"></script>
		
		<?php $this->load->view('site/rides/chat.php'); ?>
		
	</head>
	<body>
		<div class="full-width mobile-section">
		<div class="full-width cab-bottom">
		<h1><a href="" target="_blank"><img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $this->config->item('email_title'); ?>"></a></h1>
		<a href="signup" target="_blank" class="sign-btn">Sign Up for <?php echo $this->config->item('email_title'); ?></a>
		</div>
		</div>
		<section>
		<div class="full-width map-main">
			<div id="dvMap" style="width:100%;height:100%;"></div>
			<div class="cab-arrive">
				<div class="full-width cab-top">
					<div class="cab-top-left">
						<h2><?php if(isset($ride_info->row()->driver['name'])) echo $ride_info->row()->user['name']?></h2>
						<span>has arrived</span>
					</div>
					<div class="cab-top-right">
						<h3>5</h3>
						min
					</div>
				</div>
				<div class="full-width cab-middle">
				<?php
					$driverStatus = FALSE;
					if(isset($ride_info->row()->driver)){ 
						if($ride_info->row()->driver['id'] != ''){
							$driverStatus = TRUE;
						}
					}
					if($driverStatus){
				?>
				
					<div class="customer-profile">
						<?php 
							$driver_image = USER_PROFILE_IMAGE_DEFAULT;
							if (isset($driver_info->image)) {
								if ($driver_info->image != '') {
									$driver_image = USER_PROFILE_IMAGE . $driver_info->image;
								}
							}
						?>
						<img src="<?php echo $driver_image; ?>">
						<div class="customer-rating">
							<span><?php if(isset($driver_info->avg_review)) echo number_format($driver_info->avg_review,1); else echo '0.0';?></span>
							<div class="star-raing"><img src="images/site/track/star.png"></div>
						</div>
					</div>
					<div class="car-type">
						<h4><?php echo $ride_info->row()->driver['name']?></h4>
						<h5>
						<span class="car-type-detail"><?php if(isset($ride_info->row()->driver['vehicle_model'])) echo $ride_info->row()->driver['vehicle_model']; ?></span>
						<span class="car-number-detail"><?php if(isset($ride_info->row()->driver['vehicle_no'])) echo $ride_info->row()->driver['vehicle_no']?></span>
						</h5>
					</div>
				<?php } else { ?>
				<div class="customer-profile cab-top-left"> <span>Driver not yet assigned </span></div>
				<?php } ?>
				</div>
				<div class="full-width cab-bottom">
				<h1><a href="" target="_blank"><img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $this->config->item('email_title'); ?>"></a></h1>
				<a href="signup" target="_blank" class="sign-btn">Sign Up for <?php echo $this->config->item('email_title'); ?></a>
				</div>
			</div>
		</div>
		

		
		</section>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript">
        var markers = [
            {
                "title": 'Pickup Location',
                "lat": '<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lat'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lat']; else '0';?>',
                "lng": '<?php if(isset($ride_info->row()->booking_information['pickup']['latlong']['lon'])) echo $ride_info->row()->booking_information['pickup']['latlong']['lon']; else '0';?>',
                "description": '<?php if(isset($ride_info->row()->booking_information['pickup']['location'])) echo $ride_info->row()->booking_information['pickup']['location']; else 'Pickup Location';?>',
				"marker_icon": '<?php echo base_url().'images/pickup_marker.png';?>'
            }<?php if(isset($ride_info->row()->booking_information['drop']['latlong'])){ 
				if($ride_info->row()->booking_information['drop']['location'] != ''){
			?>			
        ,
            {
                "title": 'Drop Location',
                "lat": '<?php if(isset($ride_info->row()->booking_information['drop']['latlong']['lat'])) echo $ride_info->row()->booking_information['drop']['latlong']['lat']; else '0'; ?>',
                "lng": '<?php if(isset($ride_info->row()->booking_information['drop']['latlong']['lon'])) echo $ride_info->row()->booking_information['drop']['latlong']['lon']; else '0'; ?>',
                "description": '<?php if(isset($ride_info->row()->booking_information['drop']['location'])) echo $ride_info->row()->booking_information['drop']['location']; else 'Drop Location'; ?>',
				"marker_icon": '<?php echo base_url().'images/drop_marker.png';?>'
            }<?php } } ?>
		];
        window.onload = function () {
            var mapOptions = {
                center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControl: false,
				streetViewControl: false,
				zoomControl: false
            };
            var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
            var infoWindow = new google.maps.InfoWindow();
            var lat_lng = new Array();
            var latlngbounds = new google.maps.LatLngBounds();
            for (i = 0; i < markers.length; i++) {
                var data = markers[i]
                var myLatlng = new google.maps.LatLng(data.lat, data.lng);
                lat_lng.push(myLatlng);
                var marker = new google.maps.Marker({
                    position: myLatlng,
                    map: map,
                    title: data.title,
					icon: data.marker_icon
                });
                latlngbounds.extend(marker.position);
                (function (marker, data) {
                   /*  google.maps.event.addListener(marker, "click", function (e) {
                        infoWindow.setContent(data.description);
                        infoWindow.open(map, marker);
                    }); */
                })(marker, data);
            }
            map.setCenter(latlngbounds.getCenter());
            map.fitBounds(latlngbounds);

            //***********ROUTING****************//

            //Intialize the Path Array
            var path = new google.maps.MVCArray();

            //Intialize the Direction Service
            var service = new google.maps.DirectionsService();

            //Set the Path Stroke Color
            var poly = new google.maps.Polyline({ map: map, strokeColor: '#4986E7' });

            //Loop and Draw Path Route between the Points on MAP
            for (var i = 0; i < lat_lng.length; i++) {
                if ((i + 1) < lat_lng.length) {
                    var src = lat_lng[i];
                    var des = lat_lng[i + 1];
                    path.push(src);
                    poly.setPath(path);
                    service.route({
                        origin: src,
                        destination: des,
                        travelMode: google.maps.DirectionsTravelMode.DRIVING
                    }, function (result, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            for (var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
                                path.push(result.routes[0].overview_path[i]);
                            }
                        }
                    });
                }
            }
        }
		
		
    </script>
	</body>
</html>
