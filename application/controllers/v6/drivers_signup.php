<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to Drivers Signup
 * @author Casperon
 *
 * */
class Drivers_signup extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('driver_model', 'app_model'));

        header('Content-type:application/json;charset=utf-8');
    }

    /**
     *
     * The following functions are used to returns the informations while registerings as a driver
     *
     * */
    public function get_location_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = $this->format_string('No locations are availbale', 'no_location_available');
        try {
            $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('status' => 'Active'), array('city'), array('city' => 'ASC'));
            if ($locationsVal->num_rows() > 0) {
                $locationsArr = array();
                foreach ($locationsVal->result() as $row) {
                    $locationsArr[] = array('id' => (string) $row->_id,
                        'city' => (string) $row->city
                    );
                }
                $returnArr['status'] = '1';
                if (empty($locationsArr)) {
                    $locationsArr = json_decode("{}");
                }
                $returnArr['response'] = array('locations' => $locationsArr);
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function get_category_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            $location_id = (string) $this->input->post('location_id');

            if ($location_id != '') {
                $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('_id' => new \MongoId($location_id)), array('city', 'avail_category'));
                if ($locationsVal->num_rows() > 0) {
                    $categoryResult = $this->app_model->get_available_category(CATEGORY, $locationsVal->row()->avail_category);
                    $categoryArr = array();
                    if ($categoryResult->num_rows() > 0) {
                        foreach ($categoryResult->result() as $row) {
                            $categoryArr[] = array('id' => (string) $row->_id,
                                'category' => (string) $row->name
                            );
                        }
                    }
                    $returnArr['status'] = '1';
                    if (empty($categoryArr)) {
                        $categoryArr = json_decode("{}");
                    }
                    $returnArr['response'] = array('category' => $categoryArr);
                } else {
                    $returnArr['response'] = $this->format_string("Records not available", "no_records_found");
                }
            } else {
                $returnArr['response'] = $this->format_string("Some Parameters Missing", "some_parameters_missing");
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function get_country_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = $this->format_string('No locations are availbale', 'no_location_available');
        try {
            $countriesVal = $this->app_model->get_selected_fields(country, array('status' => 'Active'), array('name', 'dial_code'), array('name' => 'ASC'));
            if ($countriesVal->num_rows() > 0) {
                $countriesArr = array();
                foreach ($countriesVal->result() as $row) {
                    $countriesArr[] = array('id' => (string) $row->_id,
                        'name' => (string) $row->name,
                        'dial_code' => (string) $row->dial_code
                    );
                }
                if (empty($countriesArr)) {
                    $countriesArr = json_decode("{}");
                }
                $returnArr['status'] = '1';
                $returnArr['response'] = array('countries' => $countriesArr);
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function get_location_with_category_list() {
        $returnArr['status'] = '0';
        $returnArr['response'] = $this->format_string('No locations are availbale', 'no_location_available');
        try {
            $locationsVal = $this->app_model->get_selected_fields(LOCATIONS, array('status' => 'Active'), array('city', 'avail_category'), array('city' => 'ASC'));
            if ($locationsVal->num_rows() > 0) {
                $locationsArr = array();
                foreach ($locationsVal->result() as $location) {
                    $location_id = (string) $location->_id;
                    $avail_category = $location->avail_category;
                    $categoryResult = $this->app_model->get_available_category(CATEGORY, $avail_category);
                    $categoryArr = array();
                    if ($categoryResult->num_rows() > 0) {
                        foreach ($categoryResult->result() as $category) {
                            $categoryArr[] = array('id' => (string) $category->_id,
                                'category' => (string) $category->name
                            );
                        }
                    }
                    $locationsArr[] = array('id' => (string) $location->_id,
                        'city' => (string) $location->city,
                        'category' => $categoryArr
                    );
                }
                if (empty($last_trip)) {
                    $last_trip = json_decode("{}");
                }
                if (empty($locationsArr)) {
                    $locationsArr = json_decode("{}");
                }
                $returnArr['status'] = '1';
                $returnArr['response'] = array('values' => $locationsArr);
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string("Error in connection", "error_in_connection");
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

}

/* End of file drivers_signup.php */
/* Location: ./application/controllers/mobile/drivers_signup.php */