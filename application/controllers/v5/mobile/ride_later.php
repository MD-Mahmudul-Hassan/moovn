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
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');
        $this->load->model('app_model');
        $returnArr = array();
		
		header('Content-type:application/json;charset=utf-8');
    }

    /*
     *
     * Select the rides which are to be un allocated
     *
     */

    public function get_later_rides() {
        $start_time = time();
        $end_time = $start_time + 1800;
        ;
        $later_rides = $this->app_model->get_ride_later_list($start_time, $end_time);
        if ($later_rides->num_rows() > 0) {
            foreach ($later_rides->result() as $rides) {
                $rid = $rides->ride_id;
                $this->booking_ride_later_request($rid);
                die;
            }
        }
    }

    /**
     *
     * This Function used for booking a ride later request
     *
     * */
    public function booking_ride_later_request($ride_id = '') {
        $limit = 3;
        if ($ride_id != '') {
            $checkRide = $this->app_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($checkRide->num_rows() == 1) {
                $checkUser = $this->app_model->get_selected_fields(USERS, array('_id' => new \MongoId($checkRide->row()->user['id'])), array('email', 'user_name', 'country_code', 'phone_number', 'push_type'));
                if ($checkUser->row()->push_type != '') {
                    $pickup_lon = $checkRide->row()->booking_information['pickup']['latlong']['lon'];
                    $pickup_lat = $checkRide->row()->booking_information['pickup']['latlong']['lat'];
                    $coordinates = array(floatval($pickup_lon), floatval($pickup_lat));
                    $location = $this->app_model->find_location(floatval($pickup_lon), floatval($pickup_lat));
                    if (!empty($location['result'])) {
                        $condition = array('status' => 'Active');
                        $category = $checkRide->row()->booking_information['service_id'];
                        $categoryResult = $this->app_model->get_selected_fields(CATEGORY, array('_id' => new \MongoId($category)), array('name'));
                        if ($categoryResult->num_rows() > 0) {
                            $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, $limit);

                            if (empty($category_drivers['result'])) {
                                $category_drivers = $this->app_model->get_nearest_driver($coordinates, (string) $category, $limit * 2);
                            }
                            $android_driver = array();
                            $apple_driver = array();
                            foreach ($category_drivers['result'] as $driver) {
                                if (isset($driver['push_notification'])) {
                                    if ($driver['push_notification']['type'] == 'ANDROID') {
                                        if (isset($driver['push_notification']['key'])) {
                                            if ($driver['push_notification']['key'] != '') {
                                                $android_driver[] = $driver['push_notification']['key'];
                                            }
                                        }
                                    }
                                    if ($driver['push_notification']['type'] == 'IOS') {
                                        if (isset($driver['push_notification']['key'])) {
                                            if ($driver['push_notification']['key'] != '') {
                                                $apple_driver[] = $driver['push_notification']['key'];
                                            }
                                        }
                                    }
                                }
                            }


                            if ($checkRide->row()->type == 'Later') {
                                $pickup = $checkRide->row()->booking_information['pickup']['location'];
                                $message = $this->format_string("Request for pickup user","request_pickup_user");
                                $response_time = $this->config->item('respond_timeout');
                                $options = array($ride_id, $response_time, $pickup);
                                if (!empty($android_driver)) {
                                    $this->sendPushNotification($android_driver, $message, 'ride_request', 'ANDROID', $options, 'DRIVER');
                                }
                                if (!empty($apple_driver)) {
                                    $this->sendPushNotification($apple_driver, $message, 'ride_request', 'IOS', $options, 'DRIVER');
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}

/* End of file ride_later.php */
/* Location: ./application/controllers/mobile/ride_later.php */