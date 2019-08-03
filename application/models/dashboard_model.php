<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to admin management
 * @author Casperon
 *
 * */
class Dashboard_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * This function return the total number of on rides
     *
     * */
    public function get_on_rides($driver_id = '') {
        if ($driver_id != '') {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Finished'),
                ),
                'driver.id' => $driver_id
            );
        } else {
            $where_clause = array(
                '$or' => array(
                    array("ride_status" => 'Confirmed'),
                    array("ride_status" => 'Arrived'),
                    array("ride_status" => 'Onride'),
                    array("ride_status" => 'Finished'),
                )
            );
        }
        $this->cimongo->where($where_clause, TRUE);
        $res = $this->cimongo->count_all_results(RIDES);
        return $res;
    }

    /**
     *
     * This function return the total earnings
     *
     * */
    public function get_total_earnings($driver_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_status' => 1,
                        'total' => 1,
                        'driver.id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'ride_status' => array('$eq' => 'Completed'),
                        'driver.id' => $driver_id
                    )
                ),
                array(
                    '$group' => array(
                        '_id' => '$ride_status',
                        'ride_status' => ['$last' => '$ride_status'],
                        'totalAmount' => array('$sum' => '$total.grand_fare')
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_status' => 1,
                        'total' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'ride_status' => array('$eq' => 'Completed')
                    )
                ),
                array(
                    '$group' => array(
                        '_id' => '$ride_status',
                        'ride_status' => ['$last' => '$ride_status'],
                        'totalAmount' => array('$sum' => '$total.grand_fare')
                    )
                )
            );
        }
        $res = $this->cimongo->aggregate(RIDES, $option);
        $totalAmount = 0;
        if (!empty($res)) {
            if (isset($res['result'][0]['totalAmount'])) {
                $totalAmount = $res['result'][0]['totalAmount'];
            }
        }
        return $totalAmount;
    }
    
    

    /**
     *
     * This function return the current month details
     *
     * */
    public function get_this_month_drivers($driver_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-01 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
                        '_id' => new \MongoId($driver_id)
                    )
                )
            );
            $this->cimongo->where();
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-m-01 00:00:00'), '$lte' => date('Y-m-d H:i:s'))
                    )
                )
            );
        }
        $res = $this->cimongo->aggregate(DRIVERS, $option);
        return $res;
    }

    /**
     *
     * This function return the current month details
     *
     * */
    public function get_this_month_rides($driver_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'booking_information.booking_date' => 1,
                        'driver.id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'booking_information.booking_date' => array('$gte' => new \Mongodate(strtotime(date('Y-m-01 00:00:00'))), '$lte' => new \Mongodate(strtotime(date('Y-m-d H:i:s')))),
                        'driver.id' => $driver_id
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'booking_information.booking_date' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'booking_information.booking_date' => array('$gte' => new \Mongodate(strtotime(date('Y-m-01 00:00:00'))), '$lte' => new \Mongodate(strtotime(date('Y-m-d H:i:s'))))
                    )
                )
            );
        }
        $res = $this->cimongo->aggregate(RIDES, $option);
        return $res;
    }
    
    public function get_today_rides($driver_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'booking_information.booking_date' => 1,
                        'driver.id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'booking_information.booking_date' => array('$gte' => new \Mongodate(strtotime(date('Y-m-d 00:00:00'))), '$lte' => new \Mongodate(strtotime(date('Y-m-d H:i:s')))),
                        'driver.id' => $driver_id
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'booking_information.booking_date' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'booking_information.booking_date' => array('$gte' => new \Mongodate(strtotime(date('Y-m-d 00:00:00'))), '$lte' => new \Mongodate(strtotime(date('Y-m-d H:i:s'))))
                    )
                )
            );
        }
        $res = $this->cimongo->aggregate(RIDES, $option);
        return $res;
    }

    /**
     *
     * This function return the current year details
     *
     * */
    public function get_this_year_drivers($driver_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-01-01 00:00:00'), '$lte' => date('Y-m-d H:i:s')),
                        '_id' => new \MongoId($driver_id)
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'driver_name' => 1,
                        'created' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'created' => array('$gte' => date('Y-01-01 00:00:00'), '$lte' => date('Y-m-d H:i:s'))
                    )
                )
            );
        }
        $res = $this->cimongo->aggregate(DRIVERS, $option);
        return $res;
    }

    /**
     *
     * This function return the current year details
     *
     * */
    public function get_this_year_rides($driver_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'booking_information.booking_date' => 1,
                        'driver.id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'booking_information.booking_date' => array('$gte' => new \Mongodate(strtotime(date('Y-01-01 00:00:00'))), '$lte' => new \Mongodate(strtotime(date('Y-m-d H:i:s')))),
                        'driver.id' => $driver_id
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_id' => 1,
                        'booking_information.booking_date' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'booking_information.booking_date' => array('$gte' => new \Mongodate(strtotime(date('Y-01-01 00:00:00'))), '$lte' => new \Mongodate(strtotime(date('Y-m-d H:i:s'))))
                    )
                )
            );
        }
        $res = $this->cimongo->aggregate(RIDES, $option);
        return $res;
    }

    /**
     *
     * This function return the monthly earnings
     *
     * */
    public function get_monthly_earnings($fromdate = '', $todate = '', $driver_id = '') {
        if ($driver_id != '') {
            $option = array(
                array(
                    '$project' => array(
                        'ride_status' => 1,
                        'booking_information.booking_date' => 1,
                        'total' => 1,
                        'amount_commission' => 1,
						      'driver_revenue' => 1,
                        'driver.id' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'ride_status' => array('$eq' => 'Completed'),
                        'booking_information.booking_date' => array('$gte' => new \Mongodate($fromdate), '$lte' => new \Mongodate($todate)),
                        'driver.id' => $driver_id
                    )
                ),
                array(
                    '$group' => array(
                        '_id' => '$ride_status',
                        'ride_status' => ['$last' => '$ride_status'],
                        'totalAmount' => array('$sum' => '$driver_revenue'),
                        'site_Earnings' => array('$sum' => '$amount_commission')
                    )
                )
            );
        } else {
            $option = array(
                array(
                    '$project' => array(
                        'ride_status' => 1,
                        'booking_information.booking_date' => 1,
                        'total' => 1,
                        'amount_commission' => 1
                    )
                ),
                array(
                    '$match' => array(
                        'ride_status' => array('$eq' => 'Completed'),
                        'booking_information.booking_date' => array('$gte' => new \Mongodate($fromdate), '$lte' => new \Mongodate($todate))
                    )
                ),
                array(
                    '$group' => array(
                        '_id' => '$ride_status',
                        'ride_status' => ['$last' => '$ride_status'],
                        'totalAmount' => array('$sum' => '$total.grand_fare'),
                        'site_Earnings' => array('$sum' => '$amount_commission')
                    )
                )
            );
        }
        $res = $this->cimongo->aggregate(RIDES, $option);
        $resultArr = array('totalAmount' => 0, 'site_Earnings' => 0);
        if (!empty($res)) {
            if (isset($res['result'][0]['totalAmount'])) {
                $resultArr = array('totalAmount' => $res['result'][0]['totalAmount'], 'site_Earnings' => $res['result'][0]['site_Earnings']);
            }
        }
        return $resultArr;
    }

}
