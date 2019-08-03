<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * ride later related functions
 * @author Casperon
 *
 * */
class Ride_later extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email','ride_helper', 'string'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');
        $this->load->model('app_model');
        $this->load->model('rides_model');
        $this->load->model('driver_model');
        $this->load->model('sms_model');
        $returnArr = array();
    }

    /*
     *
     * Select the rides which are to be un allocated
     *
     */

    public function get_later_rides() { 
        $later_rides = $this->app_model->get_ride_later_list();
        $expired_booked_rides = $this->app_model->get_expired_booked_ride_list();
        // $expired_confirmed_rides = $this->app_model->get_expired_confirmed_ride_list();
        
        if ($expired_booked_rides->num_rows() > 0) {
            foreach ($expired_booked_rides->result() as $rides) {
                $rid = $rides->ride_id;
                /* Saving Unaccepted Ride for future reference */
                save_ride_details_for_stats($rid);
                /* Saving Unaccepted Ride for future reference */
                $email = $rides->user['email'];
                $user_id =$rides->user['id'];
                $this->mail_to_user($rid, $email, $user_id);
            }
        }

        if ($later_rides->num_rows() > 0)
        {
            foreach ($later_rides->result() as $ride)
            {
                $this->booking_ride_later_request($ride->ride_id);
                $this->rides_model->update_last_re_dispatch($ride->_id);
            }
        }
    }
    
    public function time_checking() {
          $start_time = time();
        $end_time = $start_time + 3600;
         echo date('Y-m-d h:i',$start_time);
        echo "<br>";
        echo  date('Y-m-d h:i',$end_time);
        exit;
    
    
    }
    
    /**
     *
     * This Function used for mail to user regrading Ride Expired
     *
     * */
    public function mail_to_user($ride_id, $email, $user_id) {
        /* Update the ride information */
        $rideDetails = array('ride_status' => 'Expired',
           'booking_information.expired_date' => new \MongoDate(time())        
        );
        $this->app_model->update_details(RIDES, $rideDetails, array('ride_id' => $ride_id));
        $newsid = '13';
        $rider = $this->app_model->get_all_details(USERS, array('_id' => new \MongoId($user_id)));
        $user_name = $rider->row()->user_name;
   
        $template_values = $this->user_model->get_email_template($newsid,$this->data['langCode']);
        $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['subject'];
        $ridernewstemplateArr = array('email_title' => $this->config->item('email_title'), 'mail_emailTitle' => $this->config->item('email_title'), 'mail_logo' => $this->config->item('logo_image'), 'mail_footerContent' => $this->config->item('footer_content'), 'mail_metaTitle' => $this->config->item('meta_title'), 'mail_contactMail' => $this->config->item('site_contact_mail'));
        extract($ridernewstemplateArr);
        
        $message = '<!DOCTYPE HTML>
           <html>
           <head>
           <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
           <meta name="viewport" content="width=device-width"/>
           <title>' . $subject . '</title>
           <body>';
        include($template_values['templateurl']);
        $message .= '</body>
           </html>';
        $sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
           'from_mail_id' => $sender_email,
           'mail_name' => $sender_name,
           'to_mail_id' => $rider->row()->email,
           'subject_message' =>'Your '.$this->config->item('email_title').' ride has been expired',
           'body_messages' => $message
        );
      
        $email_send_to_common = $this->user_model->common_email_send($email_values);
        
        $smsFrom = $this->config->item('twilio_number');
        $phone_code = $rider->row()->country_code;
        $phone_number = $rider->row()->phone_number;
        $smsTo = $phone_code . $phone_number;
        $smsMessage = 'Your ride ('.$ride_id.') has expired. We are sorry we couldn\'t get you '.$this->config->item('email_title').' sooner due to high demand for our service. We look forward to getting you Moovn soon!';
        $this->sms_model->send($smsFrom, $smsTo, $smsMessage);

        $message = $this->format_string('Your ride has expired', 'ride_expired');
        $options = array(
            'ride_id' => $ride_id
        );
        if (isset($rider->row()->fcm_token)) {
            $user_type = 'rider';
            $this->notify($rider->row()->fcm_token, $message, 'ride_expired', $options, $rider->row()->device_type, $user_type);
        } else {
            if ($rider->row()->push_type == 'ANDROID') {
                if (isset($rider->row()->push_notification_key['gcm_id'])) {
                    if ($rider->row()->push_notification_key['gcm_id'] != '') {
                        $this->sendNewPushNotification($rider->row()->push_notification_key['gcm_id'], $message, 'ride_expired', 'ANDROID', $options, 'USER');
                    }
                }
            }
            if ($rider->row()->push_type == 'IOS') {
                if (isset($rider->row()->push_notification_key['ios_token'])) {
                    if ($rider->row()->push_notification_key['ios_token'] != '') {
                        $this->sendNewPushNotification($rider->row()->push_notification_key['ios_token'], $message, 'ride_expired', 'IOS', $options, 'USER');
                    }
                }
            }
        }
    }

    /**
     *
     * This Function used for booking a ride later request
     *
     * */
    public function booking_ride_later_request($ride_id = '') {
        $limit = 10;
        if ($ride_id != '') {
            $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($checkRide->num_rows() == 1) {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('email', 'user_name', 'country_code', 'phone_number', 'push_type'));
                if ($checkUser->row()->push_type != '') {

                    if (!isset($checkRide->row()->timezone) || !$checkRide->row()->timezone) {
                        $pickup_lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
                        $pickup_lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
                        $this->setTimezone($pickup_lat, $pickup_lon);
                    } else {
                        set_default_timezone($checkRide->row()->timezone);
                    }

                    $filter_lon = floatval($pickup_lon);
                    if ($pickup_lon < 0) {
                        $filter_lon = -1 * abs($filter_lon);
                    }

                    $filter_lat = floatval($pickup_lat);
                    if ($pickup_lat < 0) {
                        $filter_lat = -1 * abs($filter_lat);
                    }

                    $coordinates = array($filter_lon, $filter_lat);
                    $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
                    if (!empty($location['result'])) {
                        $condition = array('status' => 'Active');
                        $category = $checkRide->row()->booking_information['service_id'];
                        $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($category)), array('name'));
                        if ($categoryResult->num_rows() > 0) {
                            $limit = 100000;
                            $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, $limit);
                            $try = 1;
                            if (count($category_drivers['result']) > 0) {
                                $closest_drivers = $this->driver_model->find_closest_drivers($category_drivers, $pickup_lat, $pickup_lon, $try);
                                if ($closest_drivers) {
                                    $category_drivers['result'] = $closest_drivers;
                                    $android_driver = array();
                                    $apple_driver = array();
                                    $push_and_driver = array();
                                    $push_ios_driver = array();
                                    foreach ($category_drivers['result'] as $driver) {
                                        if (isset($driver['fcm_token'])) {
                                            $fcm_data[$driver['fcm_token']] = $driver['device_type'];
                                            $driver_ids[] = $driver['_id'];
                                        } 
                                        else if (isset($driver['push_notification'])) {
                                            if ($driver['push_notification']['type'] == 'ANDROID') {
                                                if (isset($driver['push_notification']['key'])) {
                                                    if ($driver['push_notification']['key'] != '') {
                                                        $android_driver[] = $driver['push_notification']['key'];
                                                        $k = $driver['push_notification']['key'];
                                                        $push_and_driver[$k] = $driver['_id'];
                                                    }
                                                }
                                            }
                                            if ($driver['push_notification']['type'] == 'IOS') {
                                                if (isset($driver['push_notification']['key'])) {
                                                    if ($driver['push_notification']['key'] != '') {
                                                        $apple_driver[] = $driver['push_notification']['key'];
                                                        $k = $driver['push_notification']['key'];
                                                        $push_ios_driver[$k] = $driver['_id'];
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if ($checkRide->row()->type == 'Later') {
                                        $message = 'Request for pickup user';
                                        $options = array(
                                            'ride_id' => $ride_id, 
                                            'response_time' => $this->config->item('respond_timeout'), 
                                            'pickup_location' => $checkRide->row()->booking_information['pickup']['location'],
                                            'pickup_time' => date('Y-m-d h:i:sA', $checkRide->row()->booking_information['est_pickup_date']->sec),
                                            'drop_location' => $checkRide->row()->booking_information['drop']['location']
                                        );                              
                                        if (!empty($fcm_data)) {
                                            foreach ($driver_ids as $driver_id) {
                                                $condition = array('_id' => new \MongoId($driver_id));
                                                $this->cimongo->where($condition)->inc('req_received', 1)->update(DRIVERS);
                                            }
                                            $user_type = 'driver';
                                            foreach ($fcm_data as $fcm_token => $device_type) {
                                                $this->notify($fcm_token, $message, 'ride_later_request', $options, $device_type, $user_type);
                                            }
                                        } 
                                        else if (!empty($android_driver)) {
                                            foreach ($push_and_driver as $keys => $value) {
                                                $driver_id = $value;
                                                $condition = array('_id' => new \MongoId($driver_id));
                                                $this->cimongo->where($condition)->inc('req_received', 1)->update(DRIVERS);
                                            }
                                            $this->sendNewPushNotification($android_driver, $message, 'ride_later_request', 'ANDROID', $options, 'DRIVER');
                                        }
                                        else if (!empty($apple_driver)) {
                                            foreach ($push_ios_driver as $keys => $value) {
                                                $driver_id = $value;
                                                $condition = array('_id' => new \MongoId($driver_id));
                                                $this->cimongo->where($condition)->inc('req_received', 1)->update(DRIVERS);
                                            }
                                            $this->sendNewPushNotification($apple_driver, $message, 'ride_later_request', 'IOS', $options, 'DRIVER');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     *
     * This Function used to notify driver of upcoming ride
     *
     * */
    public function notify_driver($driver = '', $ride_id = '') {
        $message = 'Reminder for Upcoming Ride';
        $options = array('ride_id' => $ride_id);
        if (isset($driver->fcm_token)) {
            $user_type = 'driver';
            $this->notify($driver->fcm_token, $message, 'ride_upcoming_reminder', $options, $driver->device_type, $user_type);
        } else if (isset($driver->push_notification)) {
            if ($driver->push_notification['type'] == 'ANDROID') {
                if (isset($driver->push_notification['key']) && $driver->push_notification['key'] != '') {
                    $android_driver = $driver->push_notification['key'];
                    $this->sendNewPushNotification($android_driver, $message, 'ride_upcoming_reminder', 'ANDROID', $options, 'DRIVER');
                }
            }
            if ($driver->push_notification['type'] == 'IOS') {
                if (isset($driver->push_notification['key']) && $driver->push_notification['key'] != '') {
                    $apple_driver = $driver->push_notification['key'];
                    $this->sendNewPushNotification($apple_driver, $message, 'ride_upcoming_reminder', 'IOS', $options, 'DRIVER');
                }
            }
        }
    }

    /*
     *
     * Driver receives notification 1 hour before estimated pickup time
     *
     */

    public function upcoming_ride_notification() {
        $upcoming_rides = $this->rides_model->get_upcoming_rides();
        if ($upcoming_rides->num_rows() > 0) {
            foreach ($upcoming_rides->result() as $ride) {
                $driver = $this->driver_model->getDriver($ride->driver['id']);
                if ($driver->result()[0]->availability == 'Yes') {
                    $this->notify_driver($driver->result()[0], $ride->ride_id);
                    $this->rides_model->update_one_hour_notif($ride->_id);
                }
            }
        }
    }
}

/* End of file ride_later.php */
/* Location: ./application/controllers/mobile/ride_later.php */