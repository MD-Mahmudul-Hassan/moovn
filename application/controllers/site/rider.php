<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * User personal info related functions, Only Logged in functionalities
 * @author Casperon
 *
 * */
class Rider extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email','ride_helper', 'string'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');
        $this->load->model('braintree_model');


        if ($this->checkLogin('U') != '') {
            $this->data['rider_info'] = $this->user_model->get_all_details(USERS, array('_id' => new \MongoId($this->checkLogin('U'))));
            $rider_info = $this->data['rider_info']->row();
        } else {
            $this->setErrorMessage('error', 'You must login first', 'driver_login_first');
            $nextUrl = current_url();
            redirect('rider/login?action=' . $nextUrl);
        }
    }

    /**
     * 
     * This function redirects the rider if they are loggen in
     * 
     */
    public function index() {
        if ($this->checkLogin('U') == '') {
            $this->setErrorMessage('error', 'You must login first', 'driver_login_first');
            redirect('rider/login');
        } else {
            redirect('rider/profile');
        }
    }

    /**
     * 
     * This function loads the riders personal profile page
     * 
     */
    public function profile_view() {
		if ($this->lang->line('rider_profile_profile') != '') {
			$this->data['heading']= stripslashes($this->lang->line('rider_profile_profile')); 
		}else{
			$this->data['heading'] = "Profile";
		}
		
		$this->data['countryLists'] = $this->user_model->get_all_details(COUNTRY, array('status' => 'Active'), array('dial_code' => 1))->result();
        $this->load->view('site/user/profile', $this->data);
    }

    /**
     * 
     * This function loads the riders rides List Page
     * 
     */
    function display_my_rides() {

        $this->data['sideMenu'] = 'rides';
        $limit = 10;
        $offset = 0;
        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";
        } else {
            $protocol = 'http://';
        }
        $currentURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (substr_count($currentURL, '?pg=') == 0) {
            $curUrl = @explode('&pg=', $currentURL);
        } else {
            $curUrl = @explode('?pg=', $currentURL);
        }
        $currentPage = 1;
        $npage = intval($this->input->get('pg'));
        if ($npage > 0) {
            $currentPage = $npage;
        }

        if ($npage != 0) {
            $paginationVal = $this->input->get('pg') * $limit;
            $offset = $paginationVal;
        }

        $newPage = $currentPage + 1;
        if (substr_count($curUrl[0], '?') >= 1) {
            $qry_str = $curUrl[0] . '&pg=' . $newPage;
        } else {
            $qry_str = $curUrl[0] . '?pg=' . $newPage;
        }

        $user_id = $this->checkLogin('U');
        $list = (empty($_GET['list'])) ? 'all' : $_GET['list'];
        switch ($list) {
            case 'all':
                $list = 'all';
                break;
            case 'upcoming':
                $list = 'upcoming';
                break;
            case 'cancelled':
                $list = 'cancelled';
                break;
            case 'onride':
                $list = 'onride';
                break;
            case 'completed':
                $list = 'completed';
                break;
            default:
                $list = 'all';
                break;
        }
        $this->data['findpage'] = $list;
        $getFields = array('_id', 'ride_status', 'ride_id', 'booking_information', 'ride_status', 'pay_status', 'timezone');
        $this->data['ridesList'] = $ridesList = $this->user_model->get_ride_list($user_id, $list, $getFields, $limit, $offset);

        if ($ridesList->num_rows() > 0) {
            $paginationDisplay = '<a title="' . $newPage . '" class="scrolling-btn-more" href="' . $qry_str . '" style="display: none;">See More List</a>';
        } else {
            $paginationDisplay = '<a title="' . $newPage . '" class="scrolling-btn-more" style="display: none;">No More List</a>';
        }
        $this->data['paginationDisplay'] = $paginationDisplay;
        if ($this->lang->line('rider_profile_my_rides') != '')
            $driver_my_rides = stripslashes($this->lang->line('rider_profile_my_rides'));
        else
            $driver_my_rides = 'My Rides';
        $this->data['heading'] = $driver_my_rides;
        $this->load->view('site/rides/display_rides', $this->data);
    }

    /**
     * 
     * This function loads the particular ride detils page
     * 
     */
    function view_ride_details() {
        $this->data['sideMenu'] = 'rides';
        $ride_id = $this->uri->segment(3);
		
        if ($this->lang->line('rider_profile_my_ride_detail') != ''){
			$this->data['heading'] = stripslashes($this->lang->line('rider_profile_my_ride_detail'));
		}else{
			$this->data['heading'] = "My Ride Details";
		}
		
        $condition = array('user.id' => $this->checkLogin('U'), 'ride_id' => $ride_id);
        $this->data['rides_details'] = $rides_details = $this->user_model->get_all_details(RIDES, $condition);

        if (!isset($rides_details->row()->timezone) || !$rides_details->row()->timezone) {
            $lat = $rides_details->row()->booking_information['pickup']['latlong']['lat'];
            $lon = $rides_details->row()->booking_information['pickup']['latlong']['lon'];
            $this->setTimezone($lat, $lon);
        } else {
            set_default_timezone($rides_details->row()->timezone);
        }
		$favcondition = array('user_id' => new \MongoId($this->checkLogin('U')));
		$this->data['favouriteList'] = $favouriteList = $this->user_model->get_all_details(FAVOURITE, $favcondition);

        if ($rides_details->num_rows() == 1) {
			if (isset($rides_details->row()->booking_information['pickup']['latlong'])) {
                $latlong = @implode(array_reverse($rides_details->row()->booking_information['pickup']['latlong']), ',');
            } else {
                $latlong = '';
            }
            $config['center'] = $latlong;
            $config['zoom'] = 'auto';
			$config['language'] = $this->data['langCode'];
            $this->googlemaps->initialize($config);
            $marker = array();
            $marker['position'] = $latlong;
            $this->googlemaps->add_marker($marker);
            $this->data['map'] = $this->googlemaps->create_map();

            $this->load->view('site/rides/view_rides', $this->data);
        } else {
            $this->setErrorMessage('error', 'This ride is no longer available', 'driver_no_longer_avail');
            redirect('rider/my-rides');
        }
    }

    /**
     * 
     * This function loads the emergency contact page
     * 
     */
    function emergency_contact() {

        if ($this->lang->line('driver_emergency_contact_details') != '')
            $driver_emergency_contact_details = stripslashes($this->lang->line('driver_emergency_contact_details'));
        else
            $driver_emergency_contact_details = "Emergency Contact Details";
        $this->data['sideMenu'] = 'emergency';
        $this->data['heading'] = $driver_emergency_contact_details;
        $this->load->view('site/user/emergency_contact', $this->data);
    }

    /**
     * 
     * This function updates the emergency contact page
     * 
     */
    function update_emergency_contact() {

        $user_id = $this->checkLogin('U');
        $em_name = $this->input->post('em_name');
        $em_email = $this->input->post('em_email');
        $em_mobile = $this->remove_leading_zeros($this->input->post('em_mobile'));
        $em_mobile_code = $this->input->post('em_mobile_code');

        $condition = array('_id' => new \MongoId($user_id));
        $rider_info = $this->data['rider_info'];


        $email_verify_status = 'No';
        $mobile_verify_status = 'No';
        if ($rider_info->row()->emergency_contact['em_email']) {
            if (isset($rider_info->row()->emergency_contact['verification']['email']))
                $email_verify_status = $rider_info->row()->emergency_contact['verification']['email'];
            if (isset($rider_info->row()->emergency_contact['verification']['mobile']))
                $mobile_verify_status = $rider_info->row()->emergency_contact['verification']['mobile'];
            if ($rider_info->row()->emergency_contact['em_email'] != $em_email) {
                $email_verify_status = 'No';
            }
            if ($rider_info->row()->emergency_contact['em_mobile'] != $em_mobile) {
                $mobile_verify_status = 'No';
            }
        }

        $vfyArr = array('email' => $email_verify_status, 'mobile' => $mobile_verify_status);

        if ($rider_info->num_rows() == 1) {
            if ($rider_info->row()->email != $em_email && $rider_info->row()->phone_number != $em_mobile) {
                $em_dataArr = array('emergency_contact.em_name' => $em_name, 'emergency_contact.em_email' => $em_email, 'emergency_contact.em_mobile' => $em_mobile, 'emergency_contact.em_mobile_code' => $em_mobile_code, 'emergency_contact.verification' => $vfyArr);

                $em_dataMailArr = array('em_name' => $em_name, 'em_email' => $em_email, 'em_mobile' => $em_mobile, 'em_mobile_code' => $em_mobile_code, 'verification' => $vfyArr);

                if (!isset($rider_info->row()->emergency_contact['em_name'])) {
                    $em_dataArr = array('emergency_contact' => $em_dataMailArr);
                }

                if (isset($rider_info->row()->emergency_contact['em_email'])) {
                    $olderEmail = $rider_info->row()->emergency_contact['em_email'];
                }

                $this->user_model->update_details(USERS, $em_dataArr, $condition);


                if (isset($rider_info->row()->emergency_contact)) {
			      $this->emergency_contact_verification_request($rider_info, $em_dataMailArr);
                    if ($olderEmail == $em_email) {
                        $this->setErrorMessage('success', 'Emergency contact updated successfully', 'driver_emergency_contact_updated');
                    } else {
                        $this->setErrorMessage('success', 'Emergency contact added successfully', 'driver_emergency_contact_added');
                    }
                } else {
                    $this->setErrorMessage('success', 'Emergency contact added successfully', 'driver_emergency_contact_added');
                }
            } else {
                $this->setErrorMessage('error', 'Sorry, You can not add your own contact details', 'driver_cannot_add_own');
            }
        } else {
            $this->setErrorMessage('error', 'Sorry, Your records not found', 'driver_your_record_not_found');
        }
        redirect('rider/emergency-contact');
    }

    /**
     * 
     * This function sends the confination SMS and Email to emergency contact person
     * 
     */
    public function emergency_contact_verification_request($user_info, $contactArr) {
        /* ---------------SMS--------------------- */
        $phone_code = $contactArr['em_mobile_code'];
        $phone_number = $contactArr['em_mobile'];
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }
        $otp_number = rand(10000, 99999);
        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
        $em_user_name = $contactArr['em_name'];
        $em_user_email = $contactArr['em_email'];
        $user_name = $user_info->row()->user_name;
        $user_id = $user_info->row()->_id;

        $message = 'Dear ' . $em_user_name . '!, ' . $user_name . ' added you as his/her emergency contact for ' . $this->config->item('email_title') . '. Your one time password to confirm your mobile number is ' . $otp_number;
        $response = $this->twilio->sms($from, $to, $message);

        $condition = array('_id' => new \MongoId($user_id));
        $this->user_model->update_details(USERS, array('emergency_contact.mobile_otp' => $otp_number), $condition);

        $responseArr['otp'] = $otp_number;
        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['otp_status'] = $otp_status;

        /* --------------------Email-------------------- */
        $newsid = '5';
        $confirm_link = base_url() . 'emergency-contact/confirm?c=' . md5($otp_number) . '&u=' . $user_id;
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
		$adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
		extract($adminnewstemplateArr);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $template_values['subject'] . '</title>
			<body>';
        include($template_values['templateurl']);
        $message .= '</body>
			</html>';
			
		$sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $em_user_email,
            'subject_message' => $template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        return $responseArr;
    }

    /**
     * 
     * This function sends the alert notification to contact person
     * 
     */
    public function emergency_alert_notification() {

        $user_id = $this->checkLogin('U');
        $latitude = 13.057215;
        $longitude = 80.253157;


        if ($latitude != '' && $longitude != '') {
            $condition = array('_id' => new \MongoId($user_id));
            $checkUser = $this->user_model->get_selected_fields(USERS, $condition, array('_id', 'user_name', 'emergency_contact'));
            if ($checkUser->num_rows() == 1) {
                if (isset($checkUser->row()->emergency_contact)) {
                    if (count($checkUser->row()->emergency_contact) > 0) {

                        $latlng = $latitude . ',' . $longitude;
                        $gmap = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false");
                        $mapValues = json_decode($gmap)->results;
                        $formatted_address = $mapValues[0]->formatted_address;

                        $this->send_alert_notification_to_emergency_contact($checkUser->row()->user_name, $checkUser->row()->emergency_contact, $formatted_address);
                        $this->setErrorMessage('success', 'Alert notification sent successfully', 'driver_alert_sent_success');
                    } else {
                        $this->setErrorMessage('error', 'Emergency contact is not available', 'driver_emergency_contact_na');
                    }
                } else {
                    $this->setErrorMessage('error', 'Sorry, You have not set emergency contact yet', 'driver_you_have_not_set_emergency_contact');
                }
            } else {
                $this->setErrorMessage('error', 'This user does not exist', 'driver_user_not_exist');
            }
        } else {
            $this->setErrorMessage('error', 'Not able to find your current location for send with alert notification.', 'driver_not_able_to_find_location');
        }
        redirect('rider/emergency-contact');
    }

    public function send_alert_notification_to_emergency_contact($user_name, $contactArr, $currentLocation = '') {
        /* ---------------SMS--------------------- */
        $phone_code = $contactArr['em_mobile_code'];
        $phone_number = $contactArr['em_mobile'];
        if (substr($phone_code, 0, 1) == '+') {
            $phone_code = $phone_code;
        } else {
            $phone_code = '+' . $phone_code;
        }

        $from = $this->config->item('twilio_number');
        $to = $phone_code . $phone_number;
        $em_user_name = $contactArr['em_name'];
        $em_user_email = $contactArr['em_email'];
        $user_name = $user_name;
        $message = 'Dear ' . $em_user_name . '!, ' . $user_name . ' sent alert notification to you for his/her emergency, For more details please check email. Team - ' . $this->config->item('email_title');
        $response = $this->twilio->sms($from, $to, $message);


        if ($this->config->item('twilio_account_type') == 'sandbox') {
            $otp_status = 'development';
        } else {
            $otp_status = 'production';
        }
        $responseArr['otp_status'] = $otp_status;

        /* --------------------Email-------------------- */
        $newsid = '6';
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $template_values['subject'] . '</title>
			<body>';
        include($template_values['templateurl']);
        $message .= '</body>
			</html>';
		$sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $em_user_email,
            'subject_message' =>$template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        return $responseArr;
    }

    /*     * *
     *
     * This function loads the riders rate card
     *
     */

    public function display_rate_card() {
        $this->data['sideMenu'] = 'ratecard';

        if ($this->lang->line('driver_rate_card') != '')
            $heading = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_rate_card')));
        else
            $heading = $this->config->item('email_title') . " Rate Card";

        $this->data['heading'] = $heading;

        $this->data['locationsList'] = $locationsList = $this->user_model->get_selected_fields(LOCATIONS, array('status' => 'Active'), array('city', '_id', 'avail_category','fare'),array('city' => 1));

        if ($this->input->get('loc') != '') {
            $location_id = (string) $this->input->get('loc');
        } else {
            $location_id = (string) $locationsList->row()->_id;
        }

        if ($this->input->get('cat') != '') {
            $category_id = (string) $this->input->get('cat');
        } else {
            $category_id = '';
        }

        $this->data['RatecategoryList'] = array();


        $ratecardArr = array();
		$have_date = '';

        if ($location_id != '') {
            $locationsVal = $this->user_model->get_selected_fields(LOCATIONS, array('_id' => new \MongoId($location_id)), array('currency', 'currency_symbol','fare', 'peak_time', 'night_charge', 'service_tax', 'avail_category','distance_unit'));

            if (isset($locationsVal->row()->avail_category)) {
                if (!empty($locationsVal->row()->avail_category)) {
					$avail_cat = array();
					$fare_cat = array();
					if (isset($locationsVal->row()->fare)) {
						if (!empty($locationsVal->row()->fare)) {
							$fare_cat = array_keys($locationsVal->row()->fare);
						}
					}
					$avail_cat = $locationsVal->row()->avail_category;
					$final_cat = array_intersect($fare_cat,$avail_cat);
                    $catCond = array('_id', $final_cat, array('status' => 'Active'));
                    $this->data['RatecategoryList'] = $categoryList = $this->user_model->get_selected_fields_where_in(CATEGORY, $catCond, array('_id', 'name'))->result();
                }
            }

            #echo '<pre>'; print_r($locationsVal->row()); die;

            if ($locationsVal->num_rows() > 0) {
               if ($category_id == '') {
                  $availArr = $locationsVal->row()->avail_category;
                  foreach($availArr as $availCat){
                     if(isset($locationsVal->row()->fare[$availCat])){
                        $category_id = $availCat;
                        break;
                     }
                  }
               }
                
               $ratecardArr = array('currency' => $locationsVal->row()->currency,
                  'location_id' => $location_id,
                  'category_id' => $category_id
               );
                
               if (isset($locationsVal->row()->fare[$category_id])) {
                  if ($this->lang->line('driver_first') != '')
                        $first = stripslashes($this->lang->line('driver_first'));
                    else
                        $first = 'First';

                    if ($this->lang->line('driver_after') != '')
                        $after = stripslashes($this->lang->line('driver_after'));
                    else
                        $after = 'After';

                    if ($this->lang->line('driver_ride_time_charges') != '')
                        $ride_time_charges = stripslashes($this->lang->line('driver_ride_time_charges'));
                    else
                        $ride_time_charges = 'Ride time charges';

                    if ($this->lang->line('driver_ride_time_is_free') != '')
                        $driver_ride_time_is_free = stripslashes($this->lang->line('driver_ride_time_is_free'));
                    else
                        $driver_ride_time_is_free = 'mins ride time is FREE! Wait time is chargeable.';

                    if ($this->lang->line('driver_wait_time_charges') != '')
                        $driver_wait_time_charges = stripslashes($this->lang->line('driver_wait_time_charges'));
                    else
                        $driver_wait_time_charges = 'Waiting time charges';


                    if ($this->lang->line('driver_peak_time_charge') != '')
                        $driver_peak_time_charge = stripslashes($this->lang->line('driver_peak_time_charge'));
                    else
                        $driver_peak_time_charge = 'Peak time charges';


                    if ($this->lang->line('driver_may_applicable_during_high_demand') != '')
                        $driver_may_applicable_during_high_demand = stripslashes($this->lang->line('driver_may_applicable_during_high_demand'));
                    else
                        $driver_may_applicable_during_high_demand = 'Peak time charges may be applicable during hign demand hours and will be conveyed during the booking. This enables us to make more cabs available to you.';


                    if ($this->lang->line('driver_night_time_charges') != '')
                        $driver_night_time_charges = stripslashes($this->lang->line('driver_night_time_charges'));
                    else
                        $driver_night_time_charges = 'Night time charges';


                    if ($this->lang->line('driver_night_time_charges_may_apply') != '')
                        $driver_night_time_charges_may_apply = stripslashes($this->lang->line('driver_night_time_charges_may_apply'));
                    else
                        $driver_night_time_charges_may_apply = 'Night time charges may be applicable during the late night hours and will be conveyed during the booking. This enables us to make more cabs available to you.';


                    if ($this->lang->line('driver_service_tax') != '')
                        $driver_service_tax = stripslashes($this->lang->line('driver_service_tax'));
                    else
                        $driver_service_tax = 'Service Tax';


                    if ($this->lang->line('driver_service_tax_is_payable') != '')
                        $driver_service_tax_is_payable = stripslashes($this->lang->line('driver_service_tax_is_payable'));
                    else
                        $driver_service_tax_is_payable = 'Service tax is payable in addition to ride fare.';
												
										if ($this->lang->line('ride_per') != '')
                        $ride_per = stripslashes($this->lang->line('ride_per'));
                    else
                        $ride_per = 'per';	
                   
                   if ($this->lang->line('ride_per_min') != '')
                        $ride_per_min = stripslashes($this->lang->line('ride_per_min'));
                    else
                        $ride_per_min = 'per min';
				   $distance_unit = $this->data['d_distance_unit'];
					if(isset($locationsVal->row()->distance_unit)){
						if($locationsVal->row()->distance_unit != ''){
							$distance_unit = $locationsVal->row()->distance_unit;
						} 
					}
												
                    $standard_rate = array(array('title' => $first .' '. $locationsVal->row()->fare[$category_id]['min_km'] .' '. $distance_unit,
                            'fare' => $locationsVal->row()->fare[$category_id]['min_fare'],
                            'sub_title' => ''
                        ),
                        array('title' => $after .' '. $locationsVal->row()->fare[$category_id]['min_km'] .' '. $distance_unit,
                            'fare' => $locationsVal->row()->fare[$category_id]['per_km'] .' '. $ride_per.' '.$distance_unit,
                            'sub_title' => ''
                        )
                    );
                    $extra_charges = array(array('title' => $ride_time_charges,
                            'fare' => $locationsVal->row()->fare[$category_id]['per_minute'] .' ' .$ride_per_min,
                            'sub_title' => $first .' '. $locationsVal->row()->fare[$category_id]['min_time'] . " " . $driver_ride_time_is_free
                        )
                    );


                    if (isset($locationsVal->row()->service_tax)) {
                        if ($locationsVal->row()->service_tax > 0) {
                            $extra_charges[] = array('title' => $driver_wait_time_charges,
                                'fare' => $locationsVal->row()->fare[$category_id]['wait_per_minute'].' '. $ride_per_min,
                                'sub_title' => ''
                            );
                        }
                    }

                    if (isset($locationsVal->row()->peak_time)) {
                        if ($locationsVal->row()->peak_time == 'Yes') {
                            $extra_charges[] = array('title' => $driver_peak_time_charge,
                                'fare' => $locationsVal->row()->fare[$category_id]['peak_time_charge'] . ' x ',
                                'sub_title' => $driver_may_applicable_during_high_demand
                            );
                        }
                    }
                    if (isset($locationsVal->row()->night_charge)) {
                        if ($locationsVal->row()->night_charge == 'Yes') {
                            $extra_charges[] = array('title' => $driver_night_time_charges,
                                'fare' => $locationsVal->row()->fare[$category_id]['night_charge'] . ' x ',
                                'sub_title' => $driver_night_time_charges_may_apply
                            );
                        }
                    }
                    if (isset($locationsVal->row()->service_tax)) {
                        if ($locationsVal->row()->service_tax > 0) {
                            $extra_charges[] = array('title' => $driver_service_tax,
                                'fare' => $locationsVal->row()->service_tax,
                                'sub_title' => $driver_service_tax_is_payable
                            );
                        }
                    }



                    $ratecardArr = array('currency' => $locationsVal->row()->currency,
                        'currency_symbol' => $locationsVal->row()->currency_symbol,
                        'standard_rate' => $standard_rate,
                        'extra_charges' => $extra_charges,
                        'location_id' => $location_id,
                        'category_id' => $category_id,
						'distance_unit'=>$distance_unit
                    );
					
                } else {					
					if ($this->lang->line('driver_car_type_not_avail') != '') $have_date =  stripslashes($this->lang->line('driver_car_type_not_avail')); else $have_date =  'Sorry, Car type is not available for this location, choose another car type.';
                }
            } else {
				if ($this->lang->line('driver_location_not_found_for_rate_card') != '') $have_date =  stripslashes($this->lang->line('driver_location_not_found_for_rate_card')); else $have_date =  'Sorry, No location found for rate card calculation';
            }
        } else {
			if ($this->lang->line('driver_location_not_found_for_rate_card') != '') $have_date =  stripslashes($this->lang->line('driver_location_not_found_for_rate_card')); else $have_date =  'Sorry, No location found for rate card calculation';
        }
        $this->data['have_date'] = $have_date;
        $this->data['ratecard_data'] = $ratecardArr;
        $this->load->view('site/user/rate_card', $this->data);
    }
    /* * *
     *
     * This function loads the dectar money page
     *
     * * */
    function display_money_page() {   
        $user_id = $this->checkLogin('U');
        if ($this->lang->line('driver_sitename_money') != ''){
			$disp_msg = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes(trim($this->lang->line('driver_sitename_money'))));
		} else{
			$disp_msg = $this->config->item('email_title') . ' Money';
		}
        $this->data['heading'] = $disp_msg;
        $this->data['wallet_balance'] = $this->user_model->get_selected_fields(WALLET, array('user_id' => New \MongoId($user_id)), array('total'))->row()->total;
        $this->data['sideMenu'] = 'wallet';
        $this->load->view('site/user/my_money', $this->data);
    }
    /* * *
     *
     * This function loads the dectar money transactions history page
     *
     */

    function display_transaction_list() {


        if ($this->lang->line('driver_sitename_money_transaction') != '')
            $heading = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_sitename_money_transaction')));
        else
            $heading = ucfirst($this->config->item('email_title')) . " Money Transactions";



        $this->data['sideMenu'] = 'wallet';
        $this->data['heading'] = $heading;
        $user_id = $this->checkLogin('U');
        $this->data['txn_type'] = $transType = $this->input->get('q');
       
		
		
		if ($transType == 'credit') {
            $transType_cond='CREDIT';
        } else if ($transType == 'debit') {
            $transType_cond='DEBIT'; 
        } else {
            $transType_cond='';
        }
		
        #echo '<pre>'; print_r($condition); die;
        #$this->data['wallet_history'] = $wallet_history = $this->user_model->get_all_details(WALLET, $condition);
        
		#print_r($transType_cond);die;
		$wallet_history = $this->user_model->user_transaction($user_id, $transType_cond);
        #echo '<pre>'; print_r($wallet_history['result']); die;
		$ref_id = 0;
		if(isset($wallet_history['result'][0]['transactions'])){
			$transactionsList = $wallet_history['result'][0]['transactions'];
			foreach($transactionsList as $referral){
				if(isset($wallet_history['result'][0]['transactions'][$ref_id]['credit_type'])){
					if($wallet_history['result'][0]['transactions'][$ref_id]['credit_type'] == 'referral'){
						$userName = $this->user_model->get_selected_fields(USERS,array('_id' => new MongoId($referral['ref_id'])),array('user_name'));
						if(isset($userName->row()->user_name)){
							$wallet_history['result'][0]['transactions'][$ref_id]['ref_user_name'] = $userName->row()->user_name;
						}
					}
				}
				$ref_id++;
			}
		}
		
		
        $this->data['wallet_history'] =$wallet_history['result'];
		
		#echo "<pre>"; print_r($this->data['wallet_history']); die;
		
        $this->load->view('site/user/display_transaction_list', $this->data);
    }

    function display_share_earnings() {

        if ($this->lang->line('driver_share_earnings') != '')
            $driver_share_earnings = stripslashes($this->lang->line('driver_share_earnings'));
        else
            $driver_share_earnings = 'Share and Earnings';


        if ($this->lang->line('driver_sign_up_with_my_code') != '')
            $driver_sign_up_with_my_code = stripslashes($this->lang->line('driver_sign_up_with_my_code'));
        else
            $driver_sign_up_with_my_code = 'Sign up with my code';

        if ($this->lang->line('driver_to_get') != '')
            $driver_to_get = stripslashes($this->lang->line('driver_to_get'));
        else
            $driver_to_get = 'to get';

        if ($this->lang->line('driver_bonus_amount_on') != '')
            $driver_bonus_amount_on = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('driver_bonus_amount_on')));
        else
            $driver_bonus_amount_on = "bonus amount on " . $this->config->item('email_title');

        $this->data['sideMenu'] = 'share_earnings';
        $this->data['heading'] = $driver_share_earnings;

        $location_result = $this->app_model->get_selected_fields(LOCATIONS, array('_id' => $this->data['rider_info']->row()->location_id));

        $shareDesc = $driver_sign_up_with_my_code .' '. $this->data['rider_info']->row()->unique_code . " " . $driver_to_get . " " . $location_result->row()->currency . " " . number_format($location_result->row()->welcome_amount, 2) . " " . $driver_bonus_amount_on;
        $this->data['location_data'] = $location_result->row();
        $this->data['shareDesc'] = $shareDesc;
        $this->load->view('site/user/display_share_earnings', $this->data);
    }

    function display_earnings() { 
        if ($this->lang->line('driver_emergency_contact_details') != '')
            $driver_emergency_contact_details = stripslashes($this->lang->line('driver_emergency_contact_details'));
        else
            $driver_emergency_contact_details = 'Emergency Contact Details';

        $this->data['heading'] = $driver_emergency_contact_details;
        $this->load->view('site/user/display_earnings', $this->data);
    }

    function update_rider_profile() {
        
        $this->data['rider_info'] = $this->user_model->get_all_details(USERS, array('_id' => new \MongoId(trim($this->checkLogin('U')))));
        $rider_info = $this->data['rider_info']->row();
		$image_name = '';
            if ($_FILES['image']['name'] != '') {
                $config['overwrite'] = false;
                $config['encrypt_name'] = TRUE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = 'images/users';
                $this->load->library('upload', $config);
                
                if ($this->upload->do_upload('image')) {
                    $imageData = $this->upload->data();
                    $this->ImageResizeWithCrop(600, 600, $imageData['file_name'], './images/users/');
                    @copy('./images/users/' . $imageData['file_name'], './images/users/thumb/' . $imageData['file_name']);
                    $this->ImageResizeWithCrop(210, 210, $imageData['file_name'], './images/users/thumb/');
                    $notificationImageName = $imageData['file_name'];
                    $profile_pic_path = $notificationImageName;
                } else {
                    $error = array('error' => $this->upload->display_errors());
                    $this->setErrorMessage('error', $this->upload->display_errors());
                    redirect('rider/profile');
                }
            } else {
                $profile_pic_path = $rider_info->image;
            }
            $otpVerified = $this->input->post('otpVerified');
            $mobileInput = $this->input->post('mNumber');
            $mNumber = $this->input->post('changed_number');
            $isMobChanged = $this->input->post('isMobileNumberChanged');
            //  print_r("<pre>".$otpVerified);
            $mobile = $rider_info->phone_number;
            $country_code = $rider_info->country_code;
            if ($isMobChanged == 'changed' && $otpVerified != 'true') {
                $this->setErrorMessage('error', 'Verify Your OTP, Try Again!', 'driver_verify_your_otp');
            } else if ($mNumber != '' && $mNumber != $rider_info->phone_number) {
                if ($otpVerified == 'true') {
                    $mobile = $this->remove_leading_zeros($this->input->post('changed_number'));
                    $country_code = $this->input->post('country_code');
                } else {
                    $this->setErrorMessage('error', 'Verify Your OTP, Try Again!', 'driver_verify_your_otp');
                }
            }
			$user_name = trim($this->input->post('user_name'));
			$stringOldPassword = trim($this->input->post('old_password'));
            $stringPassword = trim($this->input->post('password'));
            $stringConfirmPassword = trim($this->input->post('confirm_password'));
		    $password = $rider_info->password;
			if ($stringPassword != '' && $stringOldPassword !='') {
			 if((md5($stringOldPassword) == $rider_info->password)){
				if ($stringPassword === $stringConfirmPassword) {
				   $password = md5($stringPassword);
				} else {
                     $this->setErrorMessage('error', 'Your Password Doesn\'t Match', 'driver_pwd_doesnt_match');
				   redirect('site/rider/profile_view');
                  }
			}else {
                    $this->setErrorMessage('error', 'Your Old Password Doesn\'t Match', 'driver_old_pwd_doesnt_match');
				  redirect('site/rider/profile_view');	
             }	
                
            }
		   $mId = new MongoId($this->checkLogin('U'));
            $condition = array('_id' => $mId);
            $updating = $this->user_model->update_details(USERS, array('email' => $rider_info->email, 'password' => $password, 'country_code' => $country_code, 'phone_number' => $mobile, 'image' => $profile_pic_path,'user_name' => $user_name), $condition);
            if ($updating) {
                $this->setErrorMessage('success', 'Profile Updated Successfully!','profile_updated');
            }
            redirect('site/rider/profile_view');
        
    }

    /**
    * This functions loads the language settings form
    * */

    public function language_settings_form() {

        if ($this->lang->line('driver_set_your_lang_pref') != '')
            $driver_set_your_lang_pref = stripslashes($this->lang->line('driver_set_your_lang_pref'));
        else
            $driver_set_your_lang_pref = 'Set Your Language Preference';


        $this->data['sideMenu'] = 'language_settings';
        $this->data['heading'] = $driver_set_your_lang_pref;
        $this->data['languageList'] = $languageList = $this->user_model->get_all_details(LANGUAGES, array('status' => 'Active'));
        $this->load->view('site/user/language_settings', $this->data);
    }
	
	/**
    * This functions loads the language settings form
    * */
	function display_fav_locations() {
        if ($this->lang->line('user_favourite_locations') != '')
            $user_favourite_locations = stripslashes($this->lang->line('user_favourite_locations'));
        else
            $user_favourite_locations = 'Favourite Locations';
		$favcondition = array('user_id' => new \MongoId($this->checkLogin('U')));
		$this->data['favouriteList'] = $favouriteList = $this->user_model->get_all_details(FAVOURITE, $favcondition); 
		#echo '<pre>'; print_r($favouriteList->result()); die;
		$this->data['sideMenu'] = 'fav_locations';		
        $this->data['heading'] = $user_favourite_locations;
        $this->load->view('site/user/fav_locations', $this->data);
    }
    /**
    * This functions loads the ride booking form
    * */
    public function booking_ride_form() {
		$this->data['inputArr'] = $_GET;
        $this->data['sideMenu'] = 'bookride';
        if ($this->lang->line('book_ride_now') != '')
           $book_ride_now = stripslashes($this->lang->line('book_ride_now'));
        else
            $book_ride_now = 'Book your ride now';
        $this->data['heading'] = $book_ride_now;
        $this->data['vehicleTypes'] = $this->user_model->get_all_details(CATEGORY, array('status' => 'Active'));
        $this->load->view('site/rides/booking_ride', $this->data);
    }
	
	public function temp_store() {
     
       $this->app_model->simple_insert(TEMP_RIDES, $_POST);
	   $last_insert_id = $this->cimongo->insert_id();
       $client=$this->getcustomer($this->checkLogin('U'));
       $this->data['booking_id'] = $last_insert_id;
       $this->data['client_id'] = $client['client_token'];
       
       $this->load->view('site/rides/braintree_form', $this->data);
   }
   public function getcustomer($user_id){
		$returnArr['status'] = '0';
		$returnArr['Items'] = array();
		$returnArr['profile_id'] = '0';
		$returnArr['card_status'] = '0';
		$returnArr['response'] =array();
		try {
			
			if($user_id != ''){
				$userExists=$this->braintree_model->get_selected_fields(USERS,array('_id'=>new MongoId($user_id)),array('_id','brain_profile_id'));
				if ($userExists->num_rows() > 0) {
					if($this->data['bt_auto_charge'] == 'Yes'){
						if($this->data['braintree_settings']['status'] == 'Enable'){
							try{
								$returnArr['client_token'] = Braintree_ClientToken::generate();
							}catch(Braintree_Exception_Authentication $e){
								$returnArr['response'] = 'Authentication Problem, Try again Later';
							}catch(Braintree_Exception_Authorization $e){
								$returnArr['response'] = 'Authentication Problem, Try again Later';
							}
							if(isset($userExists->row()->brain_profile_id)){
								$respose = $this->braintree_model->getcustomer($userExists->row()->brain_profile_id);
								if(!empty($respose)){
									if($respose['status']=='1'){
										
										$returnArr['status'] = '1';									
										$returnArr['card_status'] = '1';
										$returnArr['profile_id'] =  (string)$respose['profile_id'];
										$brain_profile_id = $userExists->row()->brain_profile_id;
										$savedItems = $this->braintree_model->getcustomerpaymentmethods($brain_profile_id);
										
										$returnArr['Items'] = array();
										if($savedItems['status']=='1'){
											$returnArr['Items'] = $savedItems['methods'];
										}
										
										$returnArr['response'] = $this->format_string('Payment Methods Available','payment_available');
										
										try{
											$returnArr['client_token'] = Braintree_ClientToken::generate(["customerId" => "$brain_profile_id"]);
										}catch(Braintree_Exception_Authentication $e){
											$returnArr['response'] = 'Authentication Problem, Try again Later';
										}catch(Braintree_Exception_Authorization $e){
											$returnArr['response'] = 'Authentication Problem, Try again Later';
										}
							
							
									}else{
										$returnArr['response'] = $this->format_string('No Payment Methods Available','no_payment_methods');
									}
								}else{
									$returnArr['response'] = $this->format_string('No Payment Methods Available','no_payment_methods');
								}
							}else{
								$returnArr['response'] = $this->format_string('No Payment Methods Available','no_payment_methods');
							}
						} else {
							$returnArr['response'] = $this->format_string('Sorry, Cannot continue this payment','cannot_continue_payment');
						}	
					} else {
						$returnArr['response'] = $this->format_string('Sorry, Cannot process this payment','cannot_proceed_payment');
					}
				} else {
					$returnArr['response'] = $this->format_string('Invalid User','invalid_user');
				}
			} else {
				$returnArr['response'] = $this->format_string('Some parameters are missing','some_parameters_missing');
			}
		
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_connection');
        }
		return $returnArr;
	}
    /**
     *
     * This Function used for booking a ride
     *
     * */
    public function booking_ride() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            /*** language ***/
            if ($this->lang->line('no_cabs_available') != '')
                $no_cabs_available = stripslashes($this->lang->line('no_cabs_available'));
            else
                $no_cabs_available = 'No cabs available nearby';
            if ($this->lang->line('sorry_dont') != '')
                $sorry_dont = stripslashes($this->lang->line('sorry_dont'));
            else
                $sorry_dont = 'Sorry ! We do not provide services in your city yet.';
            if ($this->lang->line('invalid_user') != '')
                $invalid_user = stripslashes($this->lang->line('invalid_user'));
            else
                $invalid_user = 'Invalid User';
            if ($this->lang->line('some_parameters_missing') != '')
                $some_parameters_missing = stripslashes($this->lang->line('some_parameters_missing'));
            else
                $some_parameters_missing = 'Some Parameters Missing';
            if ($this->lang->line('after_one_from_now') != '')
                $after_one_from_now = stripslashes($this->lang->line('after_one_from_now'));
            else
                $after_one_from_now = 'You can book ride only after one hour from now';
            if ($this->lang->line('error_in_connection') != '')
                $error_in_connection = stripslashes($this->lang->line('error_in_connection'));
            else
                $error_in_connection = 'Error in connection';
            if ($this->lang->line('ride_booked_success') != '')
                $ride_booked_success = stripslashes($this->lang->line('ride_booked_success'));
            else
                $ride_booked_success = 'Your job has been booked successfully';                
            
            /*** language ***/
            if(isset($_GET['booking_id'])) {
                $booking_id = $_GET['booking_id'];
            } else {
                $booking_id = $this->input->post('booking_id');
            }
           
            $booking_info = $this->braintree_model->get_all_details(TEMP_RIDES, array('_id' => new MongoId($booking_id)));
            
			$payment_method_nonce = (string) trim($this->input->post('nonce'));
			
            $user_id = $this->checkLogin('U');
            $pickup = $booking_info->row()->pickup_location;
            $pickup_lat = $booking_info->row()->pickup_lat;
            $pickup_lon = $booking_info->row()->pickup_lon;
            $category = $booking_info->row()->category;
            $type = $booking_info->row()->type;
            $pickup_datetime = $booking_info->row()->pickup_date_time;
            $code = $booking_info->row()->code;
            $try = intval($this->input->post('try'));
            $ride_id = (string) $this->input->post('ride_id');

            $drop_loc = $booking_info->row()->drop_location;
            $drop_lat = $booking_info->row()->drop_lat;
            $drop_lon = $booking_info->row()->drop_lon;
            
            if ($drop_loc == '') {
                $drop_lat = 0;
                $drop_lon = 0;
            }			
			
            $riderlocArr = array('lat' => (string) $pickup_lat, 'lon' => (string) $pickup_lon);

            if ($type == 1) {
                $ride_type = 'Later';
                $pickup_datetime = $pickup_datetime . ':00';
                $this->setTimezone($pickup_lat, $pickup_lon);
            } else {
                $ride_type = 'Now';
                $pickup_datetime = date('Y-m-d H:i:s'); 
            }
           
            $pickup_timestamp = strtotime($pickup_datetime);
            $after_one_hour = strtotime('+1 hour', time());
            if( $type == 0 || ($type ==1 && ($pickup_timestamp > $after_one_hour)) ) {
                if (is_array($this->input->post())) {
                    $chkValues = count(array_filter($this->input->post()));
                } else {
                    $chkValues = 0;
                }

                $acceptance = 'No';
                if ($ride_id != '') {
                    $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('ride_id', 'ride_status', 'booking_information', 'driver', 'coupon_used', 'coupon', 'cancelled'));
                    if ($checkRide->num_rows() == 1) {
                        if ($checkRide->row()->ride_status == 'Confirmed' || $checkRide->row()->ride_status == 'Arrived') {
                            $acceptance = 'Yes';
                            $driver_id = $checkRide->row()->driver['id'];
                            $mindurationtext = '';
                            if (isset($checkRide->row()->driver['est_eta'])) {
                                $mindurationtext = $checkRide->row()->driver['est_eta'] . '';
                            }
                            $lat_lon = @explode(',', $checkRide->row()->driver['lat_lon']);
                            $driver_lat = $lat_lon[0];
                            $driver_lon = $lat_lon[1];
                        } else {
    						if($checkRide->row()->ride_status == 'Booked'){
    							/* Saving Unaccepted Ride for future reference */
    							save_ride_details_for_stats($ride_id);
    							/* Saving Unaccepted Ride for future reference */
    							$this->app_model->commonDelete(RIDES, array('ride_id' => $ride_id));
    						}
                        }
                    }
                }
                if ($acceptance == 'No') {
                    $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email', 'user_name', 'country_code', 'phone_number', 'push_type'));
                    if ($checkUser->num_rows() == 1) {                           
                        $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
                        $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
                        if (!empty($location['result'])) {
                            $condition = array('status' => 'Active');
                            $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($category)), array('name'));
                            if ($categoryResult->num_rows() > 0) {
                                if ($type != 1) {
                                    $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
                                    $limit = 100000;
                                    $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, $limit);
                                    if (count($category_drivers['result']) == 0) {
                                        $returnArr['response'] = $this->format_string('No cabs available nearby', 'cabs_not_available_nearby');
                                    } else {
                                        if ($try < 1) {
                                            $try = 1;
                                        }
                                        $closest_drivers = $this->driver_model->find_closest_drivers($category_drivers, $pickup_lat, $pickup_lon, $try);

                                        if (!$closest_drivers) {
                                            $returnArr['response'] = $this->format_string('We\'re sorry, there\'s no available drivers nearby, Please try again later', 'drivers_out_of_range');
                                        } else {
                                            $category_drivers['result'] = $closest_drivers;
                                        }
                                        $fcm_data = array();
                                        $push_driver = array();
                                        foreach ($category_drivers['result'] as $driver) {
                                            if (isset($driver['fcm_token'])) {
                                                if (isset($driver['is_voda_customer']) 
                                                    && $driver['is_voda_customer'] == true 
                                                    && isset($driver['outstanding_amount']['is_due']) 
                                                    && $driver['outstanding_amount']['is_due'] == true) {
                                                    // skip
                                                } else {
                                                    $fcm_data[$driver['fcm_token']] = $driver['device_type'];
                                                    $push_driver[] = $driver['_id'];
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($type == 1 || ($type != 1 && count($category_drivers['result'] > 0))) {
                                    $checkCode = $this->app_model->get_all_details(PROMOCODE, array('promo_code' => $code));
                                    $code_used = 'No';
                                    $coupon_type = '';
                                    $coupon_amount = '';
                                    if ($checkCode->num_rows() > 0) {
                                        $code_used = 'Yes';
                                        $coupon_type = $checkCode->row()->code_type;
                                        $coupon_amount = $checkCode->row()->promo_value;
                                    }
                                    $site_commission = 0;
                                    if (isset($location['result'][0]['site_commission'])) {
                                        if ($location['result'][0]['site_commission'] > 0) {
                                            $site_commission = $location['result'][0]['site_commission'];
                                        }
                                    }

                                    $currencyCode = $this->data['dcurrencyCode'];
									$currencySymbol = $this->data['dcurrencySymbol'];
						 
									if(isset($location['result'][0]['currency'])){
										if($location['result'][0]['currency'] != ''){
											$currencyCode  = $location['result'][0]['currency'];
											$currencySymbol  = $location['result'][0]['currency_symbol'];
										}
									}
									
									$merchant_id ='';
									if (!empty($location['result'])) {
										  if (isset($location['result'][0]['merchant_id'])) {
											if ($location['result'][0]['merchant_id']!='') {
											   $merchant_id = $location['result'][0]['merchant_id'];
											}
										 }		
									}									
								
									$distance_unit = $this->data['d_distance_unit'];
									if(isset($location['result'][0]['distance_unit'])){
										$distance_unit = $location['result'][0]['distance_unit'];
									}

                                    $ride_id = $this->app_model->get_ride_id();
                                    $bookingInfo = array('ride_id' => (string) $ride_id,
                                        'type' => $ride_type,
                                        'booking_ref' =>'website',
                                        'merchant_id' =>$merchant_id,
										'payment_method_nonce' => $payment_method_nonce,
										'currency' => $currencyCode,
										'currency_symbol' => $currencySymbol,
                                        'commission_percent' => $site_commission,
                                        'location' => array('id' => (string) $location['result'][0]['_id'],
                                            'name' => $location['result'][0]['city']
                                        ),
                                        'user' => array('id' => (string) $checkUser->row()->_id,
                                            'name' => $checkUser->row()->user_name,
                                            'email' => $checkUser->row()->email,
                                            'phone' => $checkUser->row()->country_code . $checkUser->row()->phone_number
                                        ),
                                        'driver' => array('id' => '',
                                            'name' => '',
                                            'email' => '',
                                            'phone' => ''
                                        ),
                                        'total' => array('fare' => '',
                                            'distance' => '',
                                            'ride_time' => '',
                                            'wait_time' => ''
                                        ),
                                        'fare_breakup' => array('min_km' => '',
                                            'min_time' => '',
                                            'min_fare' => '',
                                            'per_km' => '',
                                            'per_minute' => '',
                                            'wait_per_minute' => '',
                                            'peak_time_charge' => '',
                                            'night_charge' => '',
                                            'distance_unit' =>$distance_unit,
                                            'duration_unit' => 'min',
                                        ),
                                        'tax_breakup' => array('service_tax' => ''),
                                        'booking_information' => array('service_type' => $categoryResult->row()->name,
                                            'service_id' => (string) $categoryResult->row()->_id,
                                            'booking_date' => new \MongoDate(time()),
                                            'pickup_date' => '',
                                            'est_pickup_date' => new \MongoDate(strtotime($pickup_datetime)),
                                            'booking_email' => $checkUser->row()->email,
                                            'pickup' => array('location' => $pickup,
                                                'latlong' => array('lon' => floatval($pickup_lon),
                                                    'lat' => floatval($pickup_lat))
                                            ),
                                            'drop' => array('location' => (string)$drop_loc,
                                                'latlong' => array('lon' => floatval($drop_lon),
                                                    'lat' => floatval($drop_lat)
                                                )
                                            )
                                        ),
                                        'ride_status' => 'Booked',
                                        'coupon_used' => $code_used,
                                        'coupon' => array('code' => $code,
                                            'type' => $coupon_type,
                                            'amount' => floatval($coupon_amount)
                                        ),
                                        'payment_type' => 'card',
                                        'category' => $category,
                                        'timezone'  =>  $this->getTimezone($pickup_lat, $pickup_lon)
                                    );
                                    #echo '<pre>'; print_r($bookingInfo); die;
                                    $this->app_model->simple_insert(RIDES, $bookingInfo);
                                    $last_insert_id = $this->cimongo->insert_id();
                                    #echo '<pre>'; print_r($bookingInfo); die;
                                    if ($type == 0) {
                                        $message = $this->format_string("Request for pickup user","request_pickup_user");
                                        $response_time = $this->config->item('respond_timeout');
                                        $options = array($ride_id, $response_time, $pickup, $drop_loc);

                                        if (count($fcm_data) > 0) {
                                            $user_type = 'driver';
                                            foreach ($fcm_data as $fcm_token => $device_type) {
                                                $this->notify($fcm_token, $message, 'ride_request', $options, $device_type, $user_type);
                                            }
                                        } else if (!empty($android_driver)) {
                                            foreach ($push_and_driver as $keys => $value) {
                                                $driver_id = $value;
                                                $condition = array('_id' => new \MongoId($driver_id));
                                                $this->cimongo->where($condition)->inc('req_received', 1)->update(DRIVERS);
                                            }
                                            $this->sendPushNotification($android_driver, $message, 'ride_request', 'ANDROID', $options, 'DRIVER');
                                        } else if (!empty($apple_driver)) {
                                            foreach ($push_ios_driver as $keys => $value) {
                                                $driver_id = $value;
                                                $condition = array('_id' => new \MongoId($driver_id));
                                                $this->cimongo->where($condition)->inc('req_received', 1)->update(DRIVERS);
                                            }
                                            $this->sendPushNotification($apple_driver, $message, 'ride_request', 'IOS', $options, 'DRIVER');
                                        }
                                    }
                                    if (isset($response_time)) {
                                        if ($response_time <= 0) {
                                            $response_time = 10;
                                        }
                                    } else {
                                        $response_time = 10;
                                    }
                                    if (empty($riderlocArr)) {
                                        $riderlocArr = json_decode("{}");
                                    }

                                    $returnArr['status'] = '1';
                                    $returnArr['response'] = array('type' => (string) $type, 'response_time' => (string) $response_time + 10, 'ride_id' => (string) $ride_id, 'message' => $this->format_string('Booking Request Sent', 'booking_request_sent'), 'rider_location' => $riderlocArr);
                                }
                            } else {
                                $returnArr['response'] = $no_cabs_available;
                            }
                        } else {
                            $returnArr['response'] = $sorry_dont;
                        }
                    } else {
                        $returnArr['response'] = $invalid_user;
                    }
                } else {
                    $returnArr['status'] = '1';
                    $returnArr['acceptance'] = $acceptance;

                    $checkDriver = $this->app_model->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('_id', 'driver_name', 'image', 'avg_review', 'email', 'dail_code', 'mobile_number', 'vehicle_number', 'vehicle_model'));
                    /* Preparing driver information to share with user -- Start */
                    $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                    if (isset($checkDriver->row()->image)) {
                        if ($checkDriver->row()->image != '') {
                            $driver_image = USER_PROFILE_IMAGE . $checkDriver->row()->image;
                        }
                    }
                    $driver_review = 0;
                    if (isset($checkDriver->row()->avg_review)) {
                        $driver_review = $checkDriver->row()->avg_review;
                    }
                    $vehicleInfo = $this->app_model->get_selected_fields(MODELS, array('_id' => new \MongoId($checkDriver->row()->vehicle_model)), array('_id', 'name', 'brand_name'));
                    $vehicle_model = '';
                    if ($vehicleInfo->num_rows() > 0) {
                        $vehicle_model = $vehicleInfo->row()->name;
                        #$vehicle_model=$vehicleInfo->row()->brand_name.' '.$vehicleInfo->row()->name;
                    }

                    $driver_profile = array('driver_id' => (string) $checkDriver->row()->_id,
                        'driver_name' => (string) $checkDriver->row()->driver_name,
                        'driver_email' => (string) $checkDriver->row()->email,
                        'driver_image' => (string) base_url() . $driver_image,
                        'driver_review' => (string) floatval($driver_review),
                        'driver_lat' => floatval($driver_lat),
                        'driver_lon' => floatval($driver_lon),
                        'min_pickup_duration' => $mindurationtext,
                        'ride_id' => (string) $ride_id,
                        'phone_number' => (string) $checkDriver->row()->dail_code . $checkDriver->row()->mobile_number,
                        'vehicle_number' => (string) $checkDriver->row()->vehicle_number,
                        'vehicle_model' => (string) $vehicle_model
                    );
                    /* Preparing driver information to share with user -- End */
                    if (empty($driver_profile)) {
                        $driver_profile = json_decode("{}");
                    }
                    if (empty($riderlocArr)) {
                        $riderlocArr = json_decode("{}");
                    }
                    $returnArr['response'] = array('type' => (string) $type, 'ride_id' => (string) $ride_id, 'message' => $this->format_string('ride confirmed', 'ride_confirmed'), 'driver_profile' => $driver_profile, 'rider_location' => $riderlocArr);
                }
                $returnArr['acceptance'] = $acceptance;
            } else {
                $returnArr['response'] = $after_one_from_now;
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $error_in_connection;
        }
        if ($returnArr['status'] == '0') {
           $this->setErrorMessage('error', $returnArr['response']);
           redirect('rider/booking');
        } else {
           $this->setErrorMessage('success', $ride_booked_success);
           redirect('rider/view-ride/' . $ride_id);
        }
    }    
}
