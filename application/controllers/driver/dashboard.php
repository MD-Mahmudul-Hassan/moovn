<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to driver dashboard
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
     * This function loads the driver Dashboard 
     *
     */
    public function index() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            redirect('driver/dashboard/driver_dashboard');
        }
    }

    /**
     * 
     * This function loads the driver Dashboard 
     * */
    public function driver_dashboard() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            /* Total Site statistics */
            $driver_id = $this->checkLogin('D');
            $condition = array('driver.id' => $driver_id);

            $driver_info = $this->dashboard_model->get_all_details(DRIVERS, array('_id' => new \MongoId($driver_id)));
			if($driver_info->num_rows() > 0 && isset($driver_info->row()->currency)){
				$this->data['dcurrencySymbol'] = $driver_info->row()->currency_symbol;
				$this->data['dcountryCode'] = $driver_info->row()->currency;
			}

            $totalRides = $this->dashboard_model->get_all_counts(RIDES, $condition);
#echo '<pre>'; print_r($totalRides); die;

            /* Ride Statistics Informations */
            $condition = array('ride_status' => 'Completed', 'driver.id' => $driver_id);
            $completedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array(
                'ride_status' => 'Confirmed', 
                'driver.id' => $driver_id,
                'booking_information.est_pickup_date' => array(
                    '$gt' => new MongoDate(time())
                )
            );
            $upcommingRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $onRides = $this->dashboard_model->get_on_rides($driver_id);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'User', 'driver.id' => $driver_id);
            $riderDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);

            $condition = array('ride_status' => 'Cancelled', 'cancelled.primary.by' => 'Driver', 'driver.id' => $driver_id);
            $driverDeniedRides = $this->dashboard_model->get_all_counts(RIDES, $condition);


            $totalEarnings = $this->dashboard_model->get_total_earnings($driver_id);

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
                            #$todayRides = $todayList->row()->ride_booked['count'];
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


            $monthDriversArr = $this->dashboard_model->get_this_month_drivers($driver_id);
            $monthRidesArr = $this->dashboard_model->get_this_month_rides($driver_id);
            $todayRidesArr = $this->dashboard_model->get_today_rides($driver_id);
            
            if (!empty($todayRidesArr['result'])) {
                $todayRides = count($todayRidesArr['result']);
            }
            
            if (!empty($monthDriversArr['result'])) {
                $monthDrivers = count($monthDriversArr['result']);
            }
            if (!empty($monthRidesArr['result'])) {
                $monthRides = count($monthRidesArr['result']);
            }
            

            $yearDriversArr = $this->dashboard_model->get_this_year_drivers($driver_id);
            $yearRidesArr = $this->dashboard_model->get_this_year_rides($driver_id); #echo '<pre>'; print_r($yearRidesArr); die;

            if (!empty($yearDriversArr['result'])) {
                $yearDrivers = count($yearDriversArr['result']);
            }
            if (!empty($yearRidesArr['result'])) {
                $yearRides = count($yearRidesArr['result']);
            }



            $monthEarnings = array();
            for ($m = 0; $m < 30; $m++) {
                if ($m == 0) {
                    $mStartDate = strtotime(date("Y-m-d 00:00:00"));
                    $mEndDate = strtotime(date("Y-m-d 23:59:59"));
                    $currMonth = date("M d");
                } else {
                    $mStartDate = strtotime(date("Y-m-d 00:00:00", strtotime("-" . $m . " day", time())));
                    $mEndDate = strtotime(date("Y-m-d 23:59:59", strtotime("-" . $m . " day", time())));
                    $currMonth = date("M d", $mStartDate);
                }
                $thismonthearnings = $this->dashboard_model->get_monthly_earnings($mStartDate, $mEndDate, $driver_id);

                $monthEarnings[] = array((string) $currMonth, (int) $thismonthearnings['totalAmount']);
            }
            $monthEarnings = array_reverse($monthEarnings);
            $monthlyEarningsGraph = $monthEarnings;



            # echo "<pre>"; print_r($monthlyEarningsGraph);  die;
            #  echo "<pre>"; print_r($monthlySiteEarningsGraph); die; 


            /* Calculating the rides informations for graph */
            $vehicleModel = '';
            $modelId = $driver_info->row()->vehicle_model;
            if ($modelId != '') {
                $vehicleModel = $this->dashboard_model->get_all_details(MODELS, array('_id' => new \MongoId($modelId)))->row();
            }
            $this->data['totalRides'] = $totalRides;


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

#echo '<pre>'; print_r( $driver_info->result()); die;
            $this->data['monthlyEarningsGraph'] = $monthlyEarningsGraph;
            $this->data['driver_info'] = $driver_info;
            $this->data['vehicleModel'] = $vehicleModel;

            $this->data['heading'] = 'Dashboard';
            $this->load->view('driver/driversettings/dashboard', $this->data);
            /* Assign dashboard values to view end */
        }
    }

}
