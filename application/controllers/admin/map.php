<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This controller contains the functions related to Map management 
* @author Casperon
*
**/

class Map extends MY_Controller {
    function __construct(){
        parent::__construct();
        $this->load->helper(array('cookie','date','form'));
        $this->load->library(array('encrypt','form_validation'));       
        $this->load->model(array('map_model'));
    
        if ($this->checkPrivileges('map',$this->privStatus) == FALSE){
            redirect('admin');
        }
    }
    
    /**
    *
    * This function loads the available drivers in a map
    *
    **/
    public function index(){
        if ($this->checkLogin('A') == ''){
            redirect('admin');
        }else {
            redirect('admin/map/map_avail_drivers');
        }
    }
    
    /**
    *
    * This function loads the available drivers in a map
    *
    **/
    public function map_avail_drivers() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $center = $this->config->item('latitude') . ',' . $this->config->item('longitude');
            $coordinates = array(floatval($this->config->item('longitude')), floatval($this->config->item('latitude')));
            $condition = array('status' => 'Active');
            $category = $this->map_model->get_selected_fields(CATEGORY, $condition, array('name','image'));
            $availCategory = array();
            if ($category->num_rows() > 0) {
                foreach ($category->result() as $cat) {
                    $availCategory[(string)$cat->_id] = $cat->name;
                }
            }
            $address = $this->input->get('location');
            /*Get latitude and longitude for an address*/
            if ($address!='') {
                $address = str_replace(" ", "+", $address);
                $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false".$this->data['google_maps_api_key']);
                $jsonArr = json_decode($json);
                $lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                $lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
                /*Get latitude and longitude for an address*/
                $location = array($lat,$lang);
                $coordinates = array_reverse($location);
                $center = @implode($location, ',');
            }
            if (!empty($coordinates) && $coordinates[0] != '') {
                $driverList = $this->map_model->get_nearest_driver($coordinates); 
            } else {
               $this->setErrorMessage('error', 'No location Found','admin_no_location');
               redirect('admin/map/map_avail_drivers');
            }
            
            $this->load->library('googlemaps');

            $config['center'] =$center;
            #$config['zoom'] = 'auto';
            $config['places'] = TRUE;
            $config['cluster'] = TRUE;
            $config['language'] = $this->data['langCode'];
            $config['placesAutocompleteInputID'] = 'location';
            $config['placesAutocompleteBoundsMap'] = TRUE;
            $this->googlemaps->initialize($config);
            $avail = 0;
            $unavail = 0;
            $onride = 0;

            if ($this->config->item('driver_last_active_time')) {
                $driver_last_active_time = $this->config->item('driver_last_active_time');
            } else {
                $driver_last_active_time = 1800;
            }

            if (!empty($driverList['result'])) {
                foreach ($driverList['result'] as $driver) {
                    $loc = array_reverse($driver['loc']);
                    $latlong = @implode($loc,',');
                    $marker = array();
                    $marker['position'] = $latlong;
                    $current = time() - $driver_last_active_time;               
               
                   if ($driver['availability'] == 'Yes' && $driver['mode'] == 'Available' && isset($driver['last_active_time']->sec) && $driver['last_active_time']->sec > $current) {
                            $avail++;
                            $marker['icon'] = base_url().'images/pin-available.png';
                    } else if($driver['availability'] == 'Yes' && $driver['mode'] == 'Booked') {
                        $onride++;
                        $marker['icon'] = base_url().'images/pin-yellow.png';
                    } else {
                        $unavail++;
                        $marker['icon'] = base_url().'images/pin-unavailable.png';
                    }
                    $marker['icon_scaledSize'] = '25,25';
                    $catDis = "";
                    if (array_key_exists((string)$driver['category'],$availCategory)) {
                        $catDis = $availCategory[(string)$driver['category']];
                    }
                    $marker['infowindow_content'] = "<div style='width:150px !important;height:50Px!important;'>" . $driver['driver_name'] . '<br/>' . $catDis . "</div>";
                    $this->googlemaps->add_marker($marker);
                }
            }
            $this->data['map'] = $this->googlemaps->create_map();
            $this->data['online_drivers'] = $avail;
            $this->data['offline_drivers'] = $unavail;
            $this->data['onride_drivers'] = $onride;
            $this->data['address'] = urldecode($address);
            
            if ($this->lang->line('admin_menu_map_view') != '') {
                $title = stripslashes($this->lang->line('admin_menu_map_view')); 
            } else {
                $title = 'Map View';
            }
            $this->data['heading'] = $title;
            $this->load->view('admin/map/availbale_drivers', $this->data);
        }
    }
    
    /**
    *
    * This function loads the available users in a map
    *
    **/
    public function map_avail_users(){
        if ($this->checkLogin('A') == ''){
            redirect('admin');
        }else{
            $center=$this->config->item('latitude').','.$this->config->item('longitude');
            #$coordinates=array(80.233692,13.040503);  // T Nagar
            #$coordinates=array(72.85085,19.040208); // Dharavi
            $coordinates=array(floatval($this->config->item('longitude')),floatval($this->config->item('latitude')));
            
            
            
            $address=$this->input->get('location');
            /*Get latitude and longitude for an address*/
            if($address!=''){
                $address = str_replace(" ", "+", $address);
                $google_map_api='AIzaSyC5YIg8-Yk_zqjzWpFyZrgYuzzjTCBJV7k';
                $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false".$this->data['google_maps_api_key']);
                $jsonArr = json_decode($json);
                $lat = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                $lang = $jsonArr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
                /*Get latitude and longitude for an address*/
                $location=array($lat,$lang);
                $coordinates=array_reverse($location);
                $center=@implode($location,',');
            }
            if(!empty($coordinates) & $coordinates[0]!='') {
            $userList = $this->map_model->get_nearest_user($coordinates); 
            } else {
              $this->setErrorMessage('error', 'No location Found','admin_no_location');
              redirect('admin/map/map_avail_users');
            }
            
            $this->load->library('googlemaps');

            $config['center'] =$center;
            #$config['zoom'] = 'auto';
            $config['places'] = TRUE;
            $config['cluster'] = TRUE;
            $config['language'] = $this->data['langCode'];
            $config['placesAutocompleteInputID'] = 'location';
            $config['placesAutocompleteBoundsMap'] = TRUE;
            $this->googlemaps->initialize($config);
            
            if(!empty($userList['result'])){
                foreach($userList['result'] as $user){
                    $loc=array_reverse($user['loc']);
                    $latlong=@implode($loc,',');
                    $marker = array();
                    $marker['position'] = $latlong;
                    $marker['icon'] = base_url().'images/user.png';
                    $marker['icon_scaledSize'] = '25,25';
                    $marker['infowindow_content'] ="<div style='width:200px !important;height:50Px!important;'>".$user['user_name'].'<br/>'.$user['email']."</div>";
                    $this->googlemaps->add_marker($marker);
                }
            }
            $this->data['map'] = $this->googlemaps->create_map();
        
            $this->data['address'] = urldecode($address);
            if ($this->lang->line('admin_menu_map_view') != '') 
            $title= stripslashes($this->lang->line('admin_menu_map_view')); 
            else  $title = 'Map View';
            $this->data['heading'] = $title;
            $this->load->view('admin/map/availbale_users',$this->data);
        }
    }
    /**
    *
    * This function loads the available drivers in a map
    *
    **/
    public function estimate_fare(){
        if ($this->checkLogin('A') == ''){
            redirect('admin');
        }else{
            echo $geoDistance = $this->map_model->geoDistance(13.061037,80.254521,13.022360,80.219498); echo '<br/>';
            $originlatlon='13.061037,80.254521';
            $destinationlatlon='13.022360,80.219498';
            $from = str_replace(' ','%20',$originlatlon);
            $to = str_replace(' ','%20',$destinationlatlon);

            $gmap=file_get_contents('http://maps.googleapis.com/maps/api/directions/json?origin='.$from.'&destination='.$to.'&alternatives=true&sensor=false&mode=driving');
            $routes=json_decode($gmap)->routes;
            usort($routes,create_function('$a,$b','return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
            echo $routes[0]->legs[0]->distance->text;echo '<br/>';
            echo $routes[0]->legs[0]->duration->text;echo '<br/>';
            $distance = preg_replace('/[^0-9.]+/i', '', $routes[0]->legs[0]->distance->text);
            echo $distance = (double) $distance;
            die;
            if ($this->lang->line('admin_menu_map_view') != '') 
            $this->data['heading']= stripslashes($this->lang->line('admin_menu_map_view')); 
            else  $this->data['heading'] = 'Map View';
            $this->load->view('admin/map/availbale_drivers',$this->data);
        }
    }
    
}

/* End of file map.php */
/* Location: ./application/controllers/admin/map.php */