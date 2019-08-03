<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
*
* This model contains all db functions related to mobile user application 
* @author Casperon
*
*/

class App_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * This function return the location if the the given coordinates will be inside the location boundary
     *  Array $coordinates
     *
     * */
  public function find_location($longitude, $latitude,$app = 'No') {
        /*$option = array(
            array(
                '$match' => array(
                    'status' => array('$eq' => 'Active'),
                    'bounds.southwest.lng' => array('$lt' => $longitude),
                    'bounds.northeast.lng' => array('$gt' => $longitude),
                    'bounds.southwest.lat' => array('$lt' => $latitude),
                    'bounds.northeast.lat' => array('$gt' => $latitude)
                )
            )
        );
        $res = $this->cimongo->aggregate(LOCATIONS, $option);
        return $res;*/
    if($app=='No'){
      $option = array("loc" => array('$geoIntersects' => array('$geometry' => array("type"=>"Point",
                                                                    "coordinates"=>array($longitude,$latitude)
                                                                    )
                                                  )
                              )
                  );
    }else{
      $option = array("status"=>"Active",
                  "loc" => array('$geoIntersects' => array('$geometry' => array("type"=>"Point",
                                                                    "coordinates"=>array($longitude,$latitude)
                                                                    )
                                                  )
                              )
                  );      
    }
        $res = $this->cimongo->getlocation(LOCATIONS, $option);
    
    
    $return['result'] = array();
    if($res->num_rows()>0){
      $return['result'] = $res->result_array();
    }
        return $return;
    }

    /**
     *
     * This function return the location if the the given coordinates will be inside the location boundary
     *  Array $coordinates
     *
     * */
    public function location_exist($longitude, $latitude) {
       /* $option = array(
            array(
                '$match' => array(
                    'bounds.southwest.lng' => array('$lt' => $longitude),
                    'bounds.northeast.lng' => array('$gt' => $longitude),
                    'bounds.southwest.lat' => array('$lt' => $latitude),
                    'bounds.northeast.lat' => array('$gt' => $latitude)
                )
            )
        );
        $res = $this->cimongo->aggregate(LOCATIONS, $option);
        return $res;*/
    $option = array("loc" => array('$geoIntersects' => array('$geometry' => array("type"=>"Point","coordinates"=>array($longitude,$latitude)))));
        $res = $this->cimongo->getlocation(LOCATIONS, $option);
    
    $return['result'] = array();
    if($res->num_rows()>0){
      $return['result'] = $res->result_array();
    }
        return $return;
    }

    /**
     *
     * This function return the location  whether if availabel or else return false 
     *  Array $southwest
     *  Array $northeast
     *
     * */
    public function find_location_within_boundary($southwest = array(), $northeast = array()) {
        $option = array("location" => array('$geoWithin' => array('$box' => array($southwest, $northeast))));
        $this->cimongo->select(array('city', 'location', 'bounds'));
        $res = $this->cimongo->getlocation(LOCATIONS, $option);
        return $res;
    }

    /**
     *
     * This function return the drivers list in a selected category
     *  Array $coordinates
     *  String $category
     *  Number $limit
     *
     * */
    public function get_nearest_driver($coordinates = array(), $category, $limit=10,$distance_unit='') {
        $map_searching_radius = $this->config->item('map_searching_radius');
        if($map_searching_radius<1000){
          $map_searching_radius = 1000;
        }
        #echo $map_searching_radius; die;
        
        $distanceMultiplier = 0.001;
        if($distance_unit == 'km'){
          $distanceMultiplier = 0.001;
        } else if($distance_unit == 'mi'){
          $distanceMultiplier = 0.000621371;
        } else if($distance_unit == 'm'){
          $distanceMultiplier = 1;
        }
        

        $option = array(
            array(
                '$geoNear' => array("near" => array("type" => "Point",
                        "coordinates" => $coordinates
                    ),
                    "spherical" => true,
                    "maxDistance" => intval($map_searching_radius),
                    "includeLocs" => 'loc',
                    "distanceField" => "distance",
                    "distanceMultiplier" => $distanceMultiplier,
                    'num' => $limit
                ),
            ),
            array(
                '$project' => array(
                    'category' => 1,
                    'driver_name' => 1,
                    'loc' => 1,
                    'availability' => 1,
                    'status' => 1,
                    'mode' => 1,
                    'push_notification' => 1,
                    'fcm_token' => 1,
                    'no_of_rides' => 1,
                    'avg_review' => 1,
                    'total_review' => 1,
                    'distance' => 1,
                    'verify_status' => 1,
                    'last_active_time' => 1,
                    'device_type' => 1,
                    'outstanding_amount' => 1,
                    'is_voda_customer' => 1
                )
            ),
            array(
                '$match' => array(
                    'availability' => array('$eq' => 'Yes'),
                    'status' => array('$eq' => 'Active'),
                    'mode' => array('$eq' => 'Available'),
                    'verify_status' => array('$eq' => 'Yes'),
                    'category' => array('$eq' => new \MongoId($category)),
                    'last_active_time' => array('$gte' => new \MongoDate(time()-1800))
                )
            ),
            array(
                '$sort' => array(
                  'distance' => 1,
                  'last_active_time' => -1
                )
            )
        );
        $res = $this->cimongo->aggregate(DRIVERS, $option);
        return $res;
    }

    /**
     *
     * This function get the category in a location
     * @param String $collection
     * @param Array $condition
     *
     * */
    public function get_available_category($collection = '', $condition = array()) {
    
    if(!is_array($condition)){
      $condition = (array)$condition;
      $condition = array_unique(array_filter($condition));
    }
    
        $data = array();
        $k = 0;
        foreach ($condition as $key => $value) {
            $data[$k] = new MongoId($value);
            $k++;
        }
        $this->cimongo->select(array('name', 'image', 'vehicle_type', 'icon_normal', 'icon_active','icon_car_image'));
        $this->cimongo->where_in('_id', $data);
        $res = $this->cimongo->get($collection);
        return $res;
    }

    /**
     *
     * This function get the vehicles type in a category
     * @param Array $condition
     *
     * */
    public function get_available_vehicles($condition = array()) {
        $data = array();
        $k = 0;
        foreach ($condition as $key => $value) {
            $data[$k] = new MongoId($value);
            $k++;
        }
        $this->cimongo->select(array('vehicle_type'));
        $this->cimongo->where(array('status' => 'Active'));
        $this->cimongo->where_in('_id', $data);
        $res = $this->cimongo->get(VEHICLES);
        return $res;
    }

    /**
     *
     * This function check the coupon code usage for the given user
     * @param Array $mainarray
     * @param String $user_id
     *
     * */
    public function check_user_usage($mainarray = array(), $user_id) {
        foreach ($mainarray as $mainarraykey => $mainarray) {
            if (isset($countArr[$mainarray['user_id']])) {
                $countArr[$mainarray['user_id']] = $countArr[$mainarray['user_id']] + 1;
            } else {
                $countArr[$mainarray['user_id']] = 1;
            }
        }
        if (isset($countArr[$user_id])) {
            return $countArr[$user_id];
        } else {
            return 0;
        }
    }

    /**
     *
     * This function updates the number of rides in user collection
     * @param String $field (no_of_rides/cancelled_rides/completed_rides)
     * @param String $user_id
     *
     * */
    public function update_user_rides_count($field = '', $user_id = '') {
        if ($user_id != '' && $field != '') {
            $condition = array('_id' => new \MongoId($user_id));
            $this->cimongo->where($condition)->inc($field, 1)->update(USERS);
        }
    }

    /**
     *
     * This function updates the number of rides in driver collection
     * @param String $field (no_of_rides/cancelled_rides/completed_rides)
     * @param String $driver_id
     *
     * */
    public function update_driver_rides_count($field = '', $driver_id = '') {
        if ($driver_id != '' && $field != '') {
            $condition = array('_id' => new \MongoId($driver_id));
            $this->cimongo->where($condition)->inc($field, 1)->update(DRIVERS);
            /* $qur='db.dectar_drivers.update({"driver_name":"Suresh Kumar"},{"$inc":{"cancelled_rides":1}})'; */
        }
    }

    /**
     *
     * This function return the ride list
     * @param String $type (all/upcoming/completed)
     * @param String $user_id
     * @param Array $fieldArr
     *
     * */
    public function get_ride_list($user_id = '', $type = '', $fieldArr = array(), $limit = 50) {
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
                        'booking_information.est_pickup_date' => array(
                            '$gt' => new MongoDate(time())
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
            $this->cimongo->order_by(array('_id' => 'DESC'));
            $this->cimongo->limit($limit);
            $res = $this->cimongo->get(RIDES);
            return $res;
        }
    }

    /**
     *
     * This function return the ride list for drivers
     * @param String $type (all/onride/completed)
     * @param String $driver_id
     * @param Array $fieldArr
     *
     * */
    public function get_ride_list_for_driver($driver_id = '', $type = '', $fieldArr = array(), $limit = 50) {
        if ($driver_id != '' && $type != '') {
            $this->cimongo->select($fieldArr);

            switch ($type) {
                case 'all':
                    $where_clause = array("driver.id" => $driver_id);
                    break;
                case 'onride':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Onride'),
                            array("ride_status" => 'Confirmed'),
                            array("ride_status" => 'Arrived'),
                        ),
                        "driver.id" => $driver_id
                    );
                    break;
                case 'completed':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Completed'),
                            array("ride_status" => 'Finished')
                        ),
                        "driver.id" => $driver_id
                    );
                    break;
                case 'upcoming':
                    $where_clause = array(
                        '$or' => array(
                            array("ride_status" => 'Booked'),
                            array("ride_status" => 'Confirmed'),
                        ),
                        'booking_information.est_pickup_date' => array(
                            '$gt' => new MongoDate(time())
                        ),
                        "driver.id" => $driver_id
                    );
                    break;
                default:
                    $where_clause = array("driver.id" => $driver_id);
                    break;
            }
            $this->cimongo->where($where_clause, TRUE);
            $this->cimongo->order_by(array('_id' => 'DESC'));
            $this->cimongo->limit($limit);
            $res = $this->cimongo->get(RIDES);
            return $res;
        }
    }

    /**
     *
     * This function return the transaction list
     * @param String $type (all/credit/debit)
     * @param String $user_id
     * @param Array $fieldArr
     *
     * */
    public function get_transaction_lists($user_id = '', $type = '', $fieldArr = array()) {
        if ($user_id != '' && $type != '') {
            $this->cimongo->select($fieldArr);
            switch ($type) {
                case 'all':
                    $where_clause = array("user_id" => new \MongoId($user_id));
                    break;
                case 'credit':
                    $where_clause = array("transactions.$.type" => 'CREDIT', "user_id" => new \MongoId($user_id));
                    break;
                case 'debit':
                    $where_clause = array("transactions.$.type" => 'DEBIT', "user_id" => new \MongoId($user_id));
                    break;
                default:
                    $where_clause = array("user_id" => new \MongoId($user_id));
                    break;
            }
            $this->cimongo->where($where_clause, TRUE);
            $res = $this->cimongo->get(WALLET);
            return $res;
        }
    }

    /**
     *
     * This function return the referer document which is matched 
     *
     * */
    public function check_ref_val($ref_id = '') {
        /* $option = array(               
          array(
          '$project' => array(
          'history' =>1
          )
          ),
          array(
          '$match' => array(
          'history.reference_id' =>array('$eq'=>$ref_id),
          'history.used' =>array('$eq'=>'false')
          )
          ),
          #array(
          #'$unwind'=>"$history"
          #),
          array(
          '$group' => array("_id" => array("history.reference_id" => $ref_id))

          ),
          );
          $res = $this->cimongo->aggregate(REFER_HISTORY,$option); */
        $option = array(
            array(
                '$project' => array(
                    'transactions' => 1
                )
            ),
            array(
                '$match' => array(
                    'transactions.ref_id' => array('$eq' => $ref_id)
                )
            ),
            /* array(
              '$unwind'=>"$transactions"
              ), */
            array(
                '$group' => array("_id" => array("transactions.ref_id" => $ref_id),
                    "values" => array('$addToSet' => '$transactions')
                )
            ),
                /* array(
                  '$group' => array(
                  '_id' => array('state' => '$state', 'city' => '$city' ),
                  'pop' => array('$sum' => '$pop' )
                  )
                  ),
                  array(
                  '$group' => array(
                  '_id' => '$_id.state',
                  'avgCityPop' => array('$avg' => '$pop')
                  )
                  ) */
        );
        $res = $this->cimongo->aggregate(WALLET, $option);
        return $res;
    }

    /**
     *
     * This function return the pending(uncompleted) ride list for drivers
     *
     * */
    public function get_uncompleted_trips($driver_id = '', $fieldArr = array()) {
        if ($driver_id != '') {
            $where_clause = array(
                '$or' => array(
                    array(
                        'type'  =>  'Now',
                        '$or' => array(
                            array(
                                'payment_type'  =>  'cash',
                                '$or' => array(
                                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                    array("ride_status" => 'Onride'),
                                    array("ride_status" => 'Finished')
                                )
                ),
                            array(
                                'payment_type'  =>  array('$ne' => 'cash'),
                                '$or' => array(
                                    array("ride_status" => 'Confirmed'),
                                    array("ride_status" => 'Arrived'),
                                    array("ride_status" => 'Onride')
                                )
                            )
                        )
                    ),
                    array(
                        'type'  =>  'Later',
                        '$or' => array(
                            array(
                                'payment_type'  =>  'cash',
                                '$or' => array(
                                    array("started" => 'Yes'),
                                    array("ride_status" => 'Arrived'),
                                    array("ride_status" => 'Onride'),
                                    array("ride_status" => 'Finished')
                                )
                            ),
                            array(
                                'payment_type'  =>  array('$ne' => 'cash'),
                                '$or' => array(
                                    array("started" => 'Yes'),
                                    array("ride_status" => 'Arrived'),
                                    array("ride_status" => 'Onride')
                                )
                            )
                        )
                    )
                ),
                "driver.id" => $driver_id
            );
            $this->cimongo->select($fieldArr);
            $this->cimongo->where($where_clause, TRUE);
            $this->cimongo->order_by(array('_id' => 'DESC'));
            $res = $this->cimongo->get(RIDES);
            return $res;
        }
    }

    /**
     *
     * This function return the ride list
     *
     * */
    public function get_ride_later_list() {
        $this->cimongo->select(array('ride_id', 'type', 'ride_status', 'user'));
        $this->cimongo->where(
            array(
                'type' => 'Later', 
                'ride_status' => 'Booked',
                'booking_information.est_pickup_date' => array(
                  '$gt'=>new \MongoDate(time() + $this->config->item('time_out'))
                ),
                '$and' => array(
                    array(
                        '$or' => array(
                            array(
                                'last_re_dispatch' => array(
                                    '$exists' => false
                                )
                            ),
                            array(
                                'last_re_dispatch' => array(
                                    '$lt' => new \MongoDate(time() - $this->config->item('auto_re_dispatch'))
                                )
                            )
                        )
                    ),
                    array(
                        '$or' => array(
                            array(
                                're_dispatch_times' => array(
                                    '$exists' => false
                                )
                            ),
                            array(
                                're_dispatch_times' => array(
                                    '$lt' => (int) $this->config->item('max_auto_dispatch')
                                )
                            )
                        )
                    )
                )
            )
        );
        $this->cimongo->order_by(array('_id' => 'ASC'));
        $res = $this->cimongo->get(RIDES);
        return $res;
    }

    /**
     *
     * This function return the billing details for drivers
     * @param String $bill_from
     * @param String $bill_to
     *
     * */
    public function get_billing_rides($bill_from, $bill_to, $driver_id) {
        if ($bill_from != '' && $bill_to != '' && $driver_id != '') {
            $this->cimongo->select(array('ride_id', 'driver_revenue', 'booking_information'));
            $this->cimongo->where(array("driver.id" => $driver_id));
            $this->cimongo->where_between("history.end_ride", new \MongoDate($bill_from), new \MongoDate($bill_to));
            $this->cimongo->order_by(array('_id' => 'DESC'));
            $res = $this->cimongo->get(RIDES);
            return $res;
        }
    }
  
  /*  application version update  */
  
  /**
    *
    * This function return the today trips and earnings
    *
    * */
    public function get_today_rides($driver_id = '') {
    $option = array(
      array(
        '$project' => array(
          'ride_id' => 1,
          'booking_information' => 1,
          'driver' => 1,
          'driver_revenue' => 1,
          'pay_status' => 1,
          'ride_status' => 1,
          'total' => 1,
          'summary' =>1
        )
      ),
      array(
        '$match' => array(
          'booking_information.booking_date' => array('$gte' => new \Mongodate(strtotime(date('Y-m-d 00:00:00'))), '$lte' => new \Mongodate(strtotime(date('Y-m-d H:i:s')))),
          'driver.id' => $driver_id,
          'ride_status' => 'Completed',
          'pay_status' => 'Paid'
        )
      ),
      array(
        '$group' => array(
          '_id' =>'$driver.id',
          'totalTrips'=>array('$sum'=>1),
          'driverAmount'=>array('$sum'=>'$driver_revenue'),
          'freeTime'=>array('$sum'=>'$total.free_ride_time'),
          'tripTime'=>array('$sum'=>'$total.ride_time'),
          'waitTime'=>array('$sum'=>'$total.wait_time'),
          'ridetime'=>array('$sum'=>'$summary.ride_duration')
        )
      )
    );
        $res = $this->cimongo->aggregate(RIDES, $option);
        return $res;
    }
  /**
    *
    * This function return the today tips amount
    *
    * */
    public function get_today_tips($driver_id = '') {
      $option = array(
      array(
       '$project' => array(
        'ride_id' => 1,
        'booking_information' => 1,
        'driver' => 1,
        'pay_status' => 1,
        'ride_status' => 1,
        'total' => 1
       )
      ),
    array(
     '$match' => array(
      'booking_information.booking_date' => array('$gte' => new \Mongodate(strtotime(date('Y-m-d 00:00:00'))), '$lte' => new \Mongodate(strtotime(date('Y-m-d H:i:s')))),
      'driver.id' => $driver_id,
      'ride_status' => 'Completed',
      'pay_status' => 'Paid',
      'total.tips_amount' =>array('$gt'=> floatval(0))
     )
    ),
    array(
     '$group' => array(
      '_id' =>'$driver.id',
      'totalTrips'=>array('$sum'=>1),
      'tipsAmount'=>array('$sum'=>'$total.tips_amount')
     )
    )
   );
         $res = $this->cimongo->aggregate(RIDES, $option);
         return $res;
     }

   /**
     *
     * This function return the expired rides with booked status
     *
     * */
    public function get_expired_booked_ride_list() {
        $this->cimongo->select(array('ride_id', 'type', 'ride_status', 'user'));
        $this->cimongo->where(
            array(
              'ride_status' => 'Booked',
                'type' => 'Later',
                'booking_information.est_pickup_date' => array(
                    '$lt' => new \MongoDate(time() + ($this->config->item('time_out')*60))
                )
            )
        );
        $this->cimongo->order_by(array('_id' => 'ASC'));
        $res = $this->cimongo->get(RIDES);
        return $res;
    }

    /**
     *
     * This function return the expired rides with confirmed status
     *
     * */
    public function get_expired_confirmed_ride_list() {
        $this->cimongo->select(array('ride_id', 'type', 'ride_status', 'user'));
        $this->cimongo->where(
            array(
              'ride_status' => 'Confirmed',
                'type' => 'Later',
                'booking_information.est_pickup_date' => array(
                    '$lt' => new \MongoDate(time())
                )
            )
        );
        $this->cimongo->order_by(array('_id' => 'ASC'));
        $res = $this->cimongo->get(RIDES);
        return $res;
    }

  /**
  *
  * This function return the pending(uncompleted) ride list for user
  *
  * */
    public function get_ongoing_rides($user_id = '', $fieldArr = array()) {
    $trip_cout = 0;
        if ($user_id != '') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Finished'),
                ),
                "user.id" => $user_id
            );
            $this->cimongo->select($fieldArr);
            $this->cimongo->where($where_clause, TRUE);
            $this->cimongo->order_by(array('_id' => 'DESC'));
            $res = $this->cimongo->get(RIDES);
      $trip_cout = $res->num_rows();
        }
    return $trip_cout;
    }

    /**
    *
    * This function removes the driver from the ride
    *
    * */
    public function remove_driver_from_ride($ride_id) {
        $this->unset_details(RIDES, array('driver'), array('ride_id' => $ride_id));
    }

    public function get_city_by_country_code($country_code)
    {
        switch ($country_code) {
            case '+1':
                $city = 'Seattle';
                break;
            case '+255':
                $city = 'Dar es Salaam';
                break;
            case '+91':
                $city = 'Goa';
                break;
            case '+880':
                $city = 'Sylhet';
                break;
            case '+380':
                $city = 'Ternopil';
                break;
            default:
                $city = null;
        }
        return $city;
    }
}
