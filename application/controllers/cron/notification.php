<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set('display_errors', 'on');

/**
 * 
 * ride later related functions
 * @author Casperon
 *
 * */
class Notification extends MY_Controller {

    function __construct() {
        parent::__construct();
        
        $this->load->model(array('app_model'));

        $returnArr = array();
    }

    function send() {
        $user_id = $this->input->post('user_id');
        $rider = $this->app_model->get_all_details(USERS, array('_id' => new \MongoId($user_id)));
        $message = $this->format_string('This is a test notification');
        $options = array();
        if ($rider->row()->push_type == 'ANDROID') {
            if (isset($rider->row()->push_notification_key['gcm_id'])) {
                if ($rider->row()->push_notification_key['gcm_id'] != '') {
                    $this->sendPushNotification($rider->row()->push_notification_key['gcm_id'], $message, 'ride_expired', 'ANDROID', $options, 'USER');
                }
            }
        }
        if ($rider->row()->push_type == 'IOS') {
            if (isset($rider->row()->push_notification_key['ios_token'])) {
                if ($rider->row()->push_notification_key['ios_token'] != '') {
                    $this->sendPushNotification($rider->row()->push_notification_key['ios_token'], $message, 'ride_expired', 'IOS', $options, 'USER');
                }
            }
        }
    }

    function senddriver() {
        $driver_id = $this->input->post('driver_id');
        $driver = $this->app_model->get_all_details(DRIVERS, array('_id' => new \MongoId($driver_id)));
        $message = $this->format_string('This is a test notification');
        $options = array();
        if ($driver->row()->push_type == 'ANDROID') {
            if (isset($driver->row()->push_notification_key['gcm_id'])) {
                if ($driver->row()->push_notification_key['gcm_id'] != '') {
                    $this->sendXmppNotifications($driver->row()->push_notification_key['gcm_id'], $message, 'ride_expired', 'ANDROID', $options, 'USER');
                }
            }
        }
        if ($driver->row()->push_type == 'IOS') {
            if (isset($driver->row()->push_notification_key['ios_token'])) {
                if ($driver->row()->push_notification_key['ios_token'] != '') {
                    $this->sendXmppNotifications($driver->row()->push_notification_key['ios_token'], $message, 'ride_expired', 'IOS', $options, 'USER');
                }
            }
        }
    }

    function sendfcm() {
        $user_id = $this->input->post('user_id');
        $rider = $this->app_model->get_all_details(USERS, array('_id' => new \MongoId($user_id)));
        $message = $this->format_string('This is from user id');
        $options = array('ride_id' => '123456', 'driver_id' => '456789');
        $this->sendFcmPushNotif($rider->row()->fcm_token, $message, 'ride_expired', $options, 'ios', 'rider');
    }

    function sendfcmdriver() {
        $driver_id = $this->input->post('driver_id');
        $driver = $this->app_model->get_all_details(DRIVERS, array('_id' => new \MongoId($driver_id)));
        $message = $this->format_string('This is from user id');
        $options = array('ride_id' => '123456', 'driver_id' => '456789');
        $user_type = 'driver';
        $this->notify($driver->row()->fcm_token, $message, 'test_tidy', $options, $driver->row()->device_type, $user_type);
    }
}