<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to admin dashboard
 * @author Casperon
 *
 * */
class Dashboard extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('dashboard_model');
    }

    /**
     * 
     * This function loads the admin Dashboard 
     *
     */
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            redirect('admin/dashboard/admin_dashboard');
        }
    }

    /**
     * 
     * This function loads the admin Dashboard 
     * */
    public function admin_dashboard() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            /* Total Site statistics */

            $condition = array();
            $totalUsers = $this->dashboard_model->get_all_counts(USERS, $condition);

            $totalRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $totalDrivers = $this->dashboard_model->get_all_counts(DRIVERS, $condition);

            $totalcouponCode = $this->dashboard_model->get_all_counts(PROMOCODE, $condition);

            $totalLocations = $this->dashboard_model->get_all_counts(LOCATIONS, $condition);

            $condition = array('mode' => 'Available', 'availability' => 'Yes');
            $activeDrivers = $this->dashboard_model->get_all_counts(DRIVERS, $condition);

            /* Ride Statistics Informations */
            $condition = array('ride_status' => 'Completed');
            $completedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Booked');
            $upcommingRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $onRides = $this->dashboard_model->get_on_rides();

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User');
            $riderDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver');
            $driverDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);


            $totalEarnings = $this->dashboard_model->get_total_earnings();

            /*  Rides Lists */
            $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
            $todayList = $this->dashboard_model->get_all_details(STATISTICS, array('day_hour' => $current_date));

            $todayRides = 0;
            $todayDrivers = 0;
            $monthRides = 0;
            $monthDrivers = 0;
            $yearRides = 0;
            $yearDrivers = 0;
            if ($todayList->num_rows() > 0) {
                $todayListArr = (array) $todayList->result_array();
                if (array_key_exists('ride_booked', $todayListArr[0])) {
                    if (is_array($todayList->row()->ride_booked)) {
                        if ($todayList->row()->ride_booked['count'] > 0) {
                            $todayRides = $todayList->row()->ride_booked['count'];
                        }
                    }
                }
                if (array_key_exists('driver', $todayListArr[0])) {
                    if (is_array($todayList->row()->driver)) {
                        if ($todayList->row()->driver['count'] > 0) {
                            $todayDrivers = $todayList->row()->driver['count'];
                        }
                    }
                }
            }

            $monthDriversArr = $this->dashboard_model->get_this_month_drivers();
            $monthRidesArr = $this->dashboard_model->get_this_month_rides();

            if (!empty($monthDriversArr['result'])) {
                $monthDrivers = count($monthDriversArr['result']);
            }
            if (!empty($monthRidesArr['result'])) {
                $monthRides = count($monthRidesArr['result']);
            }

            $yearDriversArr = $this->dashboard_model->get_this_year_drivers();
            $yearRidesArr = $this->dashboard_model->get_this_year_rides();

            if (!empty($yearDriversArr['result'])) {
                $yearDrivers = count($yearDriversArr['result']);
            }
            if (!empty($yearRidesArr['result'])) {
                $yearRides = count($yearRidesArr['result']);
            }

            $monthEarnings = array();
            $sitemonthEarnings = array();
            for ($m = 0; $m < 12; $m++) {
                if ($m == 0) {
                    $mStartDate = strtotime(date("Y-m-01 00:00:00"));
                    $mEndDate = strtotime(date("Y-m-31 23:59:59"));
                    $currMonth = date("Y-m-d");
                } else {
                    $mStartDate = strtotime(date("Y-m-01 00:00:00", strtotime("-" . $m . " month")));
                    $mEndDate = strtotime(date("Y-m-d 23:59:59", strtotime('last day of this month', $mStartDate)));
                    $currMonth = date("Y-m-31", $mStartDate);
                }
                $thismonthearnings = $this->dashboard_model->get_monthly_earnings($mStartDate, $mEndDate);

                if ($thismonthearnings['totalAmount'] > 0) {
                    $monthEarnings[] = array($currMonth, $thismonthearnings['totalAmount']);
                }
                if ($thismonthearnings['site_Earnings'] > 0) {
                    $sitemonthEarnings[] = array($currMonth, $thismonthearnings['site_Earnings']);
                }
            }

            $monthEarnings = array_reverse($monthEarnings);
            $monthlyEarningsGraph = $monthEarnings;

            $sitemonthEarnings = array_reverse($sitemonthEarnings);
            $monthlySiteEarningsGraph = $sitemonthEarnings;

            /* echo "<pre>"; print_r($monthlyEarningsGraph); 
              echo "<pre>"; print_r($monthlySiteEarningsGraph); die; */


            /* Calculating the rides informations for graph */


            $this->data['totalUsers'] = $totalUsers;
            $this->data['totalRides'] = $totalRides;
            $this->data['totalDrivers'] = $totalDrivers;
            $this->data['activeDrivers'] = $activeDrivers;
            $this->data['totalcouponCode'] = $totalcouponCode;
            $this->data['totalLocations'] = $totalLocations;

            $this->data['completedRides'] = $completedRides;
            $this->data['upcommingRides'] = $upcommingRides;
            $this->data['onRides'] = $onRides;
            $this->data['riderDeniedRides'] = $riderDeniedRides;
            $this->data['driverDeniedRides'] = $driverDeniedRides;

            $this->data['totalEarnings'] = $totalEarnings;

            $this->data['todayRides'] = $todayRides;
            $this->data['todayDrivers'] = $todayDrivers;
            $this->data['monthRides'] = $monthRides;
            $this->data['monthDrivers'] = $monthDrivers;
            $this->data['yearRides'] = $yearRides;
            $this->data['yearDrivers'] = $yearDrivers;


            $this->data['monthlyEarningsGraph'] = $monthlyEarningsGraph;
            $this->data['monthlySiteEarningsGraph'] = $monthlySiteEarningsGraph;

			if ($this->lang->line('admin_menu_dashboard') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_dashboard')); 
		    else  $this->data['heading'] = 'Dashboard'; 
            //$this->data['heading'] = 'Dashboard';
            $this->load->view('admin/adminsettings/dashboard', $this->data);
            /* Assign dashboard values to view end */
        }
    }

}
