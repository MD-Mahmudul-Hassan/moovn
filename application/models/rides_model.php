<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to Rides management
 * @author Casperon
 *
 * */
class Rides_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * This functions selects rides list 
     * */
    public function get_rides_total($ride_actions = '', $driver_id = '') {
        $this->cimongo->select('*');
        if ($ride_actions == 'Upcoming' || $ride_actions == '') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Booked'),
                    array("ride_status" => 'Confirmed'),
                ),
                'booking_information.est_pickup_date' => array(
                    '$gt' => new MongoDate(time())
                )
            );
        } else if ($ride_actions == 'OnRide') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                )
            );
        } else if ($ride_actions == 'Completed') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Completed'),
                    array("ride_status" => 'Finished'),
                )
            );
        } else if ($ride_actions == 'Cancelled') {
            $where_clause = array('ride_status' => 'Cancelled');
        } else if ($ride_actions == 'riderCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
        } else if ($ride_actions == 'driverCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
        } else if ($ride_actions == 'Expired') {
            $where_clause = array('ride_status' => 'Expired');
        } else if ($ride_actions == 'total') {
            $where_clause = array();
        } else if ($ride_actions == 'reservations') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Booked'),
                    array("ride_status" => 'Confirmed'),
                )
            );
        }
        
        if ($driver_id != '') {
            $where_clause['driver.id'] = $driver_id;
        }
        $this->cimongo->where($where_clause, TRUE);
        $res = $this->cimongo->get(RIDES);
        return $res;
    }

    /**
     * 
     * This functions selects rides list 
     * */
    public function get_rides_list($ride_actions = '', $limit = FALSE, $offset = FALSE, $driver_id = '', $filter_array = array()) {
        $this->cimongo->select('*');
        if ($ride_actions == 'Upcoming' || $ride_actions == '') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Booked'),
                    array("ride_status" => 'Confirmed'),
                ),
                'booking_information.est_pickup_date' => array(
                    '$gt' => new MongoDate(time())
                )
            );
        } else if ($ride_actions == 'OnRide') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                    array("ride_status" => 'Finished'),
                )
            );
        } else if ($ride_actions == 'Completed') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Completed'),
                #array("ride_status"=>'Finished'),
                )
            );
        } else if ($ride_actions == 'Cancelled') {
            $where_clause = array('ride_status' => 'Cancelled');
        } else if ($ride_actions == 'riderCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
        } else if ($ride_actions == 'driverCancelled') {
            $where_clause = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
        } else if ($ride_actions == 'Expired') {
            $where_clause = array('ride_status' => 'Expired');
        } else if ($ride_actions == 'total') {
            $where_clause = array();
        } else if ($ride_actions == 'reservations') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Booked'),
                    array("ride_status" => 'Confirmed')
                )
            );
        }


        if ($driver_id != '') {
            $where_clause['driver.id'] = $driver_id;
        }
        /* Filter Rides*/
        if(!empty($filter_array))
            extract($filter_array);
            
        if(isset($location) && !empty($location)){
            $where_clause['location.id'] = $location;
        }	
        
        if(isset($to) && !empty($to) && isset($from) && !empty($from)){
            $from_date = base64_decode($from).' 00:00:00';
            $to_date = base64_decode($to).' 23:59:59';
            $where_clause['booking_information.est_pickup_date'] = array('$lte' => new MongoDate(strtotime($to_date)),'$gte' => new MongoDate(strtotime($from_date)));
        }else if(isset($from) && !empty($from)){
            $from_date = base64_decode($from).' 00:00:00';
            $where_clause['booking_information.est_pickup_date'] = array('$gte' => new MongoDate(strtotime($from_date)));
        }
        /* Filter Rides*/
        
        $this->cimongo->where($where_clause, TRUE);
        $this->cimongo->order_by(array('ride_id' => -1));
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $res = $this->cimongo->get(RIDES, $limit, $offset);
        } else {
            $res = $this->cimongo->get(RIDES);
        }


        return $res;
    }

    /**
     *
     * This function return the ride list
     * @param String $type (all/upcoming/completed)
     * @param String $user_id
     * @param Array $fieldArr
     *
     * */
    public function get_ride_list($user_id = '', $type = '', $fieldArr = array()) {
        if ($user_id != '' && $type != '') {
            $this->cimongo->select($fieldArr);

            switch ($type) {
                case 'all':
                    $where_clause = array("user.id" => $user_id);
                    break;
                case 'upcoming':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Booked'),
                            array("ride_status" => 'Confirmed'),
                        ),
                        "user.id" => $user_id
                    );
                    break;
                case 'completed':
                    $where_clause = array(
                        '$or' => array(
                            #array("ride_status"=>'Finished')
                            array("ride_status" => 'Completed')
                        ),
                        "user.id" => $user_id
                    );
                    #$this->cimongo->or_where(array('ride_status'=>'Completed', 'ride_status'=>'Cancelled','ride_status'=>'Confirmed', 'ride_status'=>'Arrived','ride_status'=>'Onride', 'ride_status'=>'Finished'));
                    break;
                default:
                    $where_clause = array("user.id" => $user_id);
                    break;
            }
            $this->cimongo->where($where_clause, TRUE);
            $this->cimongo->order_by(array('ride_id' => -1));
            $res = $this->cimongo->get(RIDES);
            return $res;
        }
    }

    /**
     * 
     * This functions selects driver's rides list 
     * */
    public function get_driver_rides_list($ride_actions = '', $driver_id = '') {
        $this->cimongo->select('*');
        if ($driver_id != '') {
            $this->cimongo->where(array('driver.id' => $driver_id));
        }
        if ($ride_actions == 'Booked' || $ride_actions == '') {
            $this->cimongo->where(array('ride_status' => 'Booked'));
        } else if ($ride_actions == 'OnRide') {
            $this->cimongo->or_where(array('ride_status' => 'Onride', 'ride_status' => 'Confirmed', 'ride_status' => 'Arrived'));
        } else if ($ride_actions == 'Completed') {
            $this->cimongo->or_where(array('ride_status' => 'Completed', 'ride_status' => 'Finished'));
        } else if ($ride_actions == 'Cancelled') {
            $this->cimongo->where(array('ride_status' => 'Cancelled'));
        }
        $this->cimongo->order_by(array('ride_id' => -1));
        $res = $this->cimongo->get(RIDES);
        return $res;
    }
    
    
    /**
    * Get Unfilled Rides
    **/
    public function get_unfilled_rides($coordinates = array(),$matchArr = array()){
        $option = array(
                                array(
                                    '$geoNear'=>array("near"=>array("type"=>"Point",
                                                        "coordinates"=>$coordinates
                                                        ),
                                    "spherical"=> true,
                                    "maxDistance"=>50000,
                                    "includeLocs"=>'location',
                                    "distanceField"=>"distance",
                                    "distanceMultiplier"=>0.001
                                    
                                    ),
                                ),
                                array(
                                    '$project' => array(
                                        'pickup_address' =>1,
                                        'user_id' =>1,
                                        'location' =>1,
                                        'category' =>1,
                                        'ride_time' =>1
                                    )
                                )
                    );
        
        if(!empty($matchArr)){
            $option[] = $matchArr;
        }
        $res = $this->cimongo->aggregate(RIDE_STATISTICS,$option);
        return $res;
    }

    /**
    * Sent 1 Hour Notification to Driver before Pickup Time
    **/
    public function update_one_hour_notif($ride_id) {
        $updateCond = array('_id' => new \MongoId($ride_id));
        $updateData = array('one_hour_notif' => 'Yes');        
        $this->cimongo->where($updateCond);
        $this->cimongo->update(RIDES, $updateData);
    }

    /**
     *
     * This function return the upcoming rides
     *
     * */
    public function get_upcoming_rides() {
        $this->cimongo->select(array('driver.id', 'ride_id'));
        $this->cimongo->where(
            array(
                'one_hour_notif' => array('$ne' => 'Yes'),
                'type' => 'Later', 
                'ride_status' => 'Confirmed',
                'booking_information.est_pickup_date' => array(
                    '$gte'=>new \MongoDate(time()),
                    '$lte'=>new \MongoDate(time()+3600)
                )
            )
        );
        $this->cimongo->order_by(array('_id' => 'ASC'));
        $res = $this->cimongo->get(RIDES);            
        return $res;
    }

    /**
    * update reminder sent
    **/
    public function update_last_re_dispatch($ride_id)
    {
        $updateCond = array(
            '_id' => new \MongoId($ride_id),
            'ride_status' => array('$ne' => 'Confirmed')
        );
        $updateData = array(
            'last_re_dispatch' => new \MongoDate(time())
        );
        $this->cimongo->where($updateCond)->inc('re_dispatch_times', 1);
        $this->cimongo->update(RIDES, $updateData);    
    }

    /**
    * calculate peak time charge
    **/
    public function getPeakTimeCharge($pickup_date, $location, $pickup_datetime, $category) {
        $time1 = strtotime($pickup_date . ' ' . $location['result'][0]['peak_time_frame']['from']);
        $time2 = strtotime($pickup_date . ' ' . $location['result'][0]['peak_time_frame']['to']);
        $ptc = FALSE;
        if ($time1 > $time2) {
            if (date('a', $pickup_datetime) == 'PM') {
                if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
                    $ptc = TRUE;
                }
            } else {
                if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
                    $ptc = TRUE;
                }
            }
        } else if ($time1 < $time2) {
            if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
                $ptc = TRUE;
            }
        }
        $peak_time_amount = 1;
        if ($ptc) {
            $peak_time_amount = $location['result'][0]['fare'][$category]['peak_time_charge'];
        }
        return $peak_time_amount;
    }

    /**
    * Calculate night time charge
    **/
    public function getNightCharge($pickup_date, $location, $pickup_datetime, $category) {
        $time1 = strtotime($pickup_date . ' ' . $location['result'][0]['night_time_frame']['from']);
        $time2 = strtotime($pickup_date . ' ' . $location['result'][0]['night_time_frame']['to']);
        $nc = FALSE;
        if ($time1 > $time2) {
            if (date('a', $pickup_datetime) == 'PM') {
                if (($time1 <= $pickup_datetime) && (strtotime('+1 day', $time2) >= $pickup_datetime)) {
                    $nc = TRUE;
                }
            } else {
                if ((strtotime('-1 day', $time1) <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
                    $nc = TRUE;
                }
            }
        } else if ($time1 < $time2) {
            if (($time1 <= $pickup_datetime) && ($time2 >= $pickup_datetime)) {
                $nc = TRUE;
            }
        }
        $night_charge_amount = 1;
        if ($nc) {
            $night_charge_amount = $location['result'][0]['fare'][$category]['night_charge'];
        }
        return $night_charge_amount;
    }

    /**
    * Calculate maximum trip estimate
    **/
    public function getMaxTripEstimate($location, $category, $minduration, $mindistance) {
        $min_amount = floatval($location['result'][0]['fare'][$category]['min_fare']);
        if (floatval($location['result'][0]['fare'][$category]['min_time']) < floatval($minduration)) {
            $ride_fare = 0;
            $ride_time = floatval($minduration) - floatval($location['result'][0]['fare'][$category]['min_time']);
            $ride_fare = $ride_time * floatval($location['result'][0]['fare'][$category]['per_minute']);
            $min_amount = $min_amount + $ride_fare;
        }
        if (floatval($location['result'][0]['fare'][$category]['min_km']) < floatval($mindistance)) {
            $after_fare = 0;
            $ride_time = floatval($mindistance) - floatval($location['result'][0]['fare'][$category]['min_km']);
            $after_fare = $ride_time * floatval($location['result'][0]['fare'][$category]['per_km']);
            $min_amount = $min_amount + $after_fare;
        }
        return $min_amount;
    }

    /**
    * Calculate trip estimate
    *
    * @return maximum trip estimate
    **/
    public function getTripEstimate($pickup_date, $pickup_datetime, $location, $category, $minduration, $mindistance) 
    {
        $peak_time_amount = 1;
        $night_charge_amount = 1;
        $min_amount = 0.00;
        $max_amount = 0.00;

        if ($location['result'][0]['peak_time'] == 'Yes') {
            $peak_time_amount = $this->rides_model->getPeakTimeCharge($pickup_date, $location, $pickup_datetime, $category);
        }
        if ($location['result'][0]['night_charge'] == 'Yes') {
            $night_charge_amount = $this->rides_model->getNightCharge($pickup_date, $location, $pickup_datetime, $category);
        }
        $min_amount = $this->rides_model->getMaxTripEstimate($location, $category, $minduration, $mindistance);

        $min_amount = $min_amount * $night_charge_amount;
        $min_amount = $min_amount * $peak_time_amount;
        $max_amount = $min_amount + ($min_amount*0.01*30);

        return $max_amount;
    }

    public function getUnpaidRides() {
        $this->cimongo->select(array('ride_id'));
        $this->cimongo->where(
            array(
                'pay_status' => 'Pending', 
                'ride_status' => 'Finished',
                'booking_information.drop_date' => array(
                    '$lt' => new \MongoDate(time() - 7200)
                )
            )
        );
        return $this->cimongo->get(RIDES);
    }
}
