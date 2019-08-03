<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * ride later related functions
 * @author Casperon
 *
 * */
class Ride_charge extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('braintree_model');
        $this->load->model('rides_model');
        $returnArr = array();
    }

    /*
     *
     * Complete Unpaid Rides by auto charging customers
     *
     */
    public function complete_unpaid_rides() {
        $unpaidRides = $this->rides_model->getUnpaidRides();
        if ($unpaidRides->num_rows() > 0) {
            foreach ($unpaidRides->result() as $ride) {
                $this->braintree_model->make_trip_payment(array('ride_id' => $ride->ride_id));
            }
        }
    }
}