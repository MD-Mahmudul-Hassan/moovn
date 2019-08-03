<?php

error_reporting(-1);

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * ride later related functions
 * @author Casperon
 *
 * */
class Users extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function update_location()
    {
        $users = $this->user_model->get_selected_fields(USERS, array(), array('_id', 'loc'));
        foreach($users->result() as $user) {
            if (isset($user->loc)) {
                $location = $this->app_model->find_location(floatval($user->loc['lon']), floatval($user->loc['lat']));
                if (isset($location['result'][0])) {
                    $update_data = array('location_id' => new \MongoId($location['result'][0]['_id']));
                    $this->user_model->update_details(USERS, $update_data, array('_id' => new \MongoId($user->_id)));
                }
            }
        }
    }

    public function add_address()
    {
        $users = $this->user_model->get_selected_fields(USERS, array(), array('_id', 'loc'));
        foreach($users->result() as $user) {
            if (isset($user->loc)) {
                $latlng = $user->loc['lat'] . ',' . $user->loc['lon'];
                $geocoding_url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latlng . '&key=AIzaSyBRYs2egwvaHvlKYWEBGdoE6WLLHoQswJ8';
                $result = json_decode(file_get_contents($geocoding_url));
                if ($result->status === 'OK') {
                    $address_components = $result->results[0]->address_components;
                    $formatted_address = $result->results[0]->formatted_address;
                    $address = array();
                    $match_array = array(
                        'street_number' => 'street_number', 
                        'route' => 'route',
                        'intersection' => 'intersection',
                        'political' => 'political', 
                        'country' => 'country', 
                        'state' => 'administrative_area_level_1', 
                        'county' => 'administrative_area_level_2', 
                        'administrative_area_level_3' => 'administrative_area_level_3', 
                        'administrative_area_level_4' => 'administrative_area_level_4', 
                        'administrative_area_level_5' => 'administrative_area_level_5',
                        'alternate_name' => 'colloquial_area',
                        'city' => 'locality',
                        'ward' => 'ward',
                        'sublocality' => 'sublocality',
                        'neighborhood' => 'neighborhood',
                        'premise' => 'premise',
                        'subpremise' => 'subpremise',
                        'postal_code' => 'postal_code',
                        'natural_feature' => 'natural_feature',
                        'airport' => 'airport',
                        'park' => 'park',
                        'point_of_interest' => 'point_of_interest'
                    );
                    foreach($address_components as $component) {
                        if ($search_result = array_search($component->types[0], $match_array)) {
                            $address = array_merge($address, array($search_result => $component->long_name));
                        }
                    }
                    $address = array_merge($address, array('formatted_address' => $formatted_address));
                    $push_data = array('address' => $address);
                    $this->user_model->update_details(USERS, $push_data, array('_id' => new \MongoId($user->_id)));
                } else {
                    echo 'couldnt find address';
                }
            }
        }
    }

    public function moovn_topup() {
        
        error_reporting(E_ALL);
        ini_set('display_errors', 'on');

        $topup_amount = 45;
        $location_id = '11111111111111111';
        $credit_type = 'Promo';

        $userResult = $this->user_model->get_selected_fields(USERS, array(
            'location_id' => new \MongoId($location_id)
        ), array('_id'));
        foreach ($userResult->result() as $user) {
            $user_id = $user->_id;

            $wallet_amount = $this->app_model->get_all_details(WALLET, array('user_id' => new \MongoId($user_id)));
            
            if ($wallet_amount->num_rows() == 0) {
                $this->app_model->simple_insert(WALLET, array('user_id' => new \MongoId($user_id), 'total' => floatval(0)));
            }

            $condition = array('user_id' => new \MongoId($user_id));
            
            $field = 'total';
            $current_balance = $wallet_amount->row()->total;

            $new_balance = $current_balance + $topup_amount;
            $this->cimongo->where($condition)->set(array($field => $new_balance))->update(WALLET);

            $txn_time = time() . rand(0, 2578);
            $initialAmt = array(
                'type' => 'CREDIT',
                'credit_type' => $credit_type,
                'trans_amount' => floatval($topup_amount),
                'avail_amount' => floatval($current_balance),
                'trans_date' => new \MongoDate(time()),
                'trans_id' => $txn_time
            );
            $this->app_model->simple_push(WALLET, array('user_id' => new \MongoId($user_id)), array('transactions' => $initialAmt));
        }
    }
}