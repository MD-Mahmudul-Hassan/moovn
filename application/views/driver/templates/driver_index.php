<?php
$this->load->view('site/templates/common_header');
?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&amp;sensor=false&amp;key=<?php echo $this->config->item('google_maps_api_key');?>"></script>

<?php 
/* <script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=<?php echo $this->config->item('google_maps_api_key');?>"></script> */ 
?>

<script type="text/javascript">
    function initialize() {
        autocomplete = new google.maps.places.Autocomplete((document.getElementById('autocomplete')), {types: ['geocode']});


        google.maps.event.addListener(autocomplete, 'place_changed', function () {
			var place = autocomplete.getPlace();
			$('#latVal').val(place.geometry.location.lat());
			$('#lonVal').val(place.geometry.location.lng());
            fillInAddress();
        });


        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            var address = '';
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    address += val + ',';
                }
            }

            var loc_street = jQuery('#route').val();
            var loc_city = jQuery('#locality').val();
            var loc_state = jQuery('#administrative_area_level_1').val();
            var loc_country = jQuery('#country').val();
            var loc_postal_code = jQuery('#postal_code').val();
        });
    }

    var speedTest = {};
    speedTest.map = null;
    speedTest.markerClusterer = null;
    speedTest.markers = [];
    speedTest.infoWindow = null;


    var placeSearch, autocomplete;
    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    };




    function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        for (var component in componentForm) {
            document.getElementById(component).value = '';
            document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                document.getElementById(addressType).value = val;
            }
        }
        var cityName = jQuery('#locality').val();
        var dr_lat = jQuery('#latVal').val();
        var dr_lon = jQuery('#lonVal').val();
        /* var address = jQuery('#route').val(); 
         var state = jQuery('#administrative_area_level_1').val(); 
         var country = jQuery('#country').val(); 
         var postal_code = jQuery('#postal_code').val();  */

        var purl = 'driver/profile/ajax_check_location';
        jQuery.post(purl, {'dr_cityName': cityName,'dr_lat': dr_lat,'dr_lon': dr_lon}, function (json) {
            jQuery('#locate_btn').attr('href', 'driver/signup/' + json.loc);
           
            window.location.href = '<?php echo base_url().'driver/signup/';?>' + json.loc;
        }, "json")

    }

</script>
<script type="text/javascript">
    google.maps.event.addDomListener(window, 'load', speedTest.init);
    google.maps.event.addDomListener(window, 'load', initialize);
</script>


<link rel="stylesheet" href="css/site/screen.css">


</head>
<body class="">
    <div class="dark bg_img">
        <div class="sign_up_base col-lg-12">
            <div class="container-new">
                <div class="already_sign_up pull-right">
                    <p><?php
                        if ($this->lang->line('driver_already_a_driver') != '')
                            echo stripslashes($this->lang->line('driver_already_a_driver'));
                        else
                            echo 'Already a driver?';
                        ?><a href="driver"><?php
                            if ($this->lang->line('driver_login') != '')
                                echo stripslashes($this->lang->line('driver_login'));
                            else
                                echo 'Log In';
                            ?></a></p>
                </div>
                <div class="col-md-5 sign_up_center">
                    <div class="sign_logo text-center col-lg-10">
                        <a class="brand" href="">
                            <?php
                            if ($this->lang->line('home_cabily') != '')
                                $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                            else
                                $sitename = $this->config->item('email_title');
                            ?>
							 <a href="<?php echo base_url(); ?>">
                            <img src="images/logo/<?php echo $this->config->item('logo_image'); ?>" alt="<?php echo $sitename; ?>">
							</a>
                        </a>
                    </div>
                    <div class="sign_up_start text-center">
                        <h1>  <?php
                            if ($this->lang->line('driver_earn_money_with') != '')
                                echo $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_earn_money_with')));
                            else
                                echo $sitename = "EARN MONEY WITH " . $this->config->item('email_title');
                            ?></h1>
                        <p> <?php
                            if ($this->lang->line('driver_there_is_never_been') != '')
                                echo $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_there_is_never_been')));
                            else
                                echo $sitename = "There's never been a better time to drive with " . $this->config->item('email_title') . ". Signing up is easy, and you'll be earning money in no time.";
                            ?></p>
                        <div class="sign_up_btn col-lg-10">
                            <input type="button" class=" btn1 sign_up_btn1" value="<?php
                            if ($this->lang->line('driver_sign_up_to_drive') != '')
                                echo stripslashes($this->lang->line('driver_sign_up_to_drive'));
                            else
                                echo 'SIGN UP TO DRIVE';
                            ?>">
                        </div>
                        <div class="sign_up_search col-lg-10 nopadd">
                            <p><?php
                                if ($this->lang->line('driver_sign_up_to_drive_ucfirst') != '')
                                    echo stripslashes($this->lang->line('driver_sign_up_to_drive_ucfirst'));
                                else
                                    echo 'Sign up to drive';
                                ?></p>
                            <span class="sign_up_search_in col-lg-12">
                                <input type="text" class="form-control sign_up_search_text" placeholder="<?php
                                if ($this->lang->line('driver_enter_your_city_town') != '')
                                    echo stripslashes($this->lang->line('driver_enter_your_city_town'));
                                else
                                    echo 'Enter your city or town';
                                ?>" id="autocomplete">
                                <a href="javascript:void(0);" id="locate_btn"></a>
								<input id="latVal" type="hidden" value="0" />
								<input id="lonVal" type="hidden"  value="0" />
                            </span>
                        </div>

                        <table id="address" style="display:none;">
                            <tr>
                                <td class="label"><?php
                                    if ($this->lang->line('driver_street_addr') != '')
                                        echo stripslashes($this->lang->line('driver_street_addr'));
                                    else
                                        echo 'Street address';
                                    ?></td>
                                <td class="slimField">
                                    <input class="field" id="street_number" disabled="true"></input></td>
                                <td class="wideField" colspan="2">
                                    <input class="field" id="route" disabled="true"></input></td>
                            </tr>
                            <tr>
                                <td class="label"><?php
                                    if ($this->lang->line('driver_city') != '')
                                        echo stripslashes($this->lang->line('driver_city'));
                                    else
                                        echo 'City';
                                    ?></td>
                                <td class="wideField" colspan="3">
                                    <input class="field" id="locality" disabled="true"></input></td>
                            </tr>
                            <tr>
                                <td class="label"><?php
                                    if ($this->lang->line('cms_state') != '')
                                        echo stripslashes($this->lang->line('cms_state'));
                                    else
                                        echo 'State';
                                    ?></td>
                                <td class="slimField">
                                    <input class="field" id="administrative_area_level_1" disabled="true"></input></td>
                                <td class="label"><?php
                                    if ($this->lang->line('cms_zip') != '')
                                        echo stripslashes($this->lang->line('cms_zip'));
                                    else
                                        echo 'Zip code';
                                    ?></td>
                                <td class="wideField">
                                    <input class="field" id="postal_code" disabled="true"></input></td>
                            </tr>
                            <tr>
                                <td class="label"><?php
                                    if ($this->lang->line('driver_country') != '')
                                        echo stripslashes($this->lang->line('driver_country'));
                                    else
                                        echo 'Country';
                                    ?></td>
                                <td class="wideField" colspan="3">
                                    <input class="field" id="country" disabled="true"></input></td>
                            </tr>
                        </table>


                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery('.sign_up_search').hide();
            jQuery(document).ready(function () {
                jQuery('.sign_up_btn1').click(function () {
                    jQuery('.sign_up_search').show();
                    jQuery('.sign_up_btn1').hide();

                })

            });
        </script>
</body>
</html>