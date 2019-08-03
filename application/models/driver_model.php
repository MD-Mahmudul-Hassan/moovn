<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to user management
 * @author Casperon
 *
 */
class Driver_model extends My_Model{
    public function __construct(){
        parent::__construct();

    }

    /***
    * Check driver exist or not
    ***/
    public function check_driver_exist($condition = array()){ 
        $this->cimongo->select();
        $this->cimongo->where($condition);
        return $res = $this->cimongo->get(DRIVERS);  
    }
    
    /**
    * 
    * This function selects the vehicles list by category using where IN
    */
    public function get_vehicles_list_by_category($idsList=''){  
        $ids=array();
        foreach($idsList as $val){
            $ids[]=new \MongoId($val);
        }
        $idsList = array();
        $this->cimongo->where_in('_id',$ids);
        $res = $this->cimongo->get(VEHICLES); 
        return $res;
    }
    
    
    /**
    *
    * This function return the trip summary
    *   String $driver_id
    *
    **/
    public function get_trip_summary($driver_id = '',$start_date = '',$end_date = ''){
        
        if($start_date!='' && $end_date!=''){
            $matchArr=array(
                                        '$match' => array(
                                            'driver.id' =>array('$eq'=>$driver_id),
                                            'ride_status' =>array('$eq'=>'Completed'),
                                            'pay_status' =>array('$eq'=>'Paid'),
                                            'history.end_ride' =>array('$gte'=>new \MongoDate($start_date),'$lte'=>new \MongoDate($end_date))
                                        )
                                    );
        }else{
            $matchArr=array(
                                        '$match' => array(
                                            'driver.id' =>array('$eq'=>$driver_id),
                                            'ride_status' =>array('$eq'=>'Completed'),
                                            'pay_status' =>array('$eq'=>'Paid')
                                        )
                                    );      
        }
                                
        $option = array(
                                array(
                                    '$project' => array(
                                        'ride_id' =>1,
                                        'commission_percent' =>1,
                                        'driver' =>1,
                                        'total' =>1,
                                        'booking_information' =>1,
                                        'ride_status' =>1,
                                        'pay_status' =>1,
                                        'summary' =>1,
                                        'pay_summary' =>1,
                                        'history' =>1,
                                        'driver_revenue' =>1,
                                        'amount_commission' =>1,
                                        'amount_detail' =>1
                                    )
                                ),
                                $matchArr
                            );
        #echo "<pre>";print_r($option);
        $res = $this->cimongo->aggregate(RIDES,$option);
        return $res;
    }
    
    /**
    *
    * This function return the total earnings
    *
    **/
    public function get_total_earnings($driver_id=''){
        $option = array(                                
                                array(
                                    '$project' => array(
                                        'ride_status' =>1,
                                        'driver' =>1,
                                        'total' =>1
                                    )
                                ),                          
                                array(
                                    '$match' => array(
                                        'ride_status' =>array('$eq'=>'Completed'),
                                        'driver.id' =>array('$eq'=>$driver_id)
                                    )
                                ),
                                array(
                                    '$group' => array(
                                        '_id' =>'$ride_status',
                                        'ride_status'=>['$last'=>'$ride_status'],
                                        'totalAmount'=>array('$sum'=>'$total.grand_fare')
                                    )
                                )
                            );
        $res = $this->cimongo->aggregate(RIDES,$option);
        $totalAmount=0;
        if(!empty($res)){ 
            if(isset($res['result'][0]['totalAmount'])){
                $totalAmount=$res['result'][0]['totalAmount'];
            }
        }
        return $totalAmount;
    }
  public function get_available_category($condition = array()) {
        $data = array();
        $k = 0;
        foreach ($condition as $key => $value) {
            $data[$k] = new MongoId($value);
            $k++;
        }
        $this->cimongo->select();
        $this->cimongo->where_in('_id', $data);
        $res = $this->cimongo->get(CATEGORY);
        return $res;
    }
    
    public function get_driver_last_ride_status($driver_id=''){
        $this->cimongo->select(array('ride_id','ride_status'));
        $this->cimongo->where(array('driver.id' => $driver_id));
        $this->cimongo->order_by(array('ride_id' => 'DESC'));
        $this->cimongo->limit(1);
        return $res = $this->cimongo->get(RIDES);  
    }
    
    /***
    * Get Driver by ID
    ***/
    public function getDriver($id){ 
        $this->cimongo->select();
        $this->cimongo->where(array('_id' => new \MongoId($id)));
        return $res = $this->cimongo->get(DRIVERS);  
    }

    public function update_outstanding_amount($driver_details, $ride_details, $payment_type)
    {
        $current_outstanding_amount = 0;
        if (isset($driver_details->outstanding_amount['amount'])) {
            $current_outstanding_amount = $driver_details->outstanding_amount['amount'];
        }
        $tips_amount = (isset($ride_details->total['tips_amount']) ? $ride_details->total['tips_amount']:0.00);
        $new_outstanding_amount = $current_outstanding_amount;
        switch (strtolower($payment_type)) {
            case 'cash':
                $new_outstanding_amount += $ride_details->amount_commission;
                break;
            case 'gateway':
            case 'wallet':
                $new_outstanding_amount -= $ride_details->driver_revenue;
                $new_outstanding_amount -= $tips_amount;
                break;
        }
        if ($current_outstanding_amount > 0) {
            $update_data = array('outstanding_amount.amount' => ceil($new_outstanding_amount));
        } else {
            $update_data = array(
                'outstanding_amount.amount' => ceil($new_outstanding_amount),
                'outstanding_amount.updated_on' => new MongoDate(time())
            );
        }
        $this->cimongo->where(array('_id' => new \MongoId($driver_details->_id)));
        $this->cimongo->update(DRIVERS, $update_data);

        $this->is_outstanding_due($driver_details->_id);
    }

    public function is_outstanding_due($driver_id)
    {
        date_default_timezone_set('UTC');
        $driver_min_remit_amount = $this->config->item('driver_min_remit_amount');
        if (!isset($driver_min_remit_amount) || empty($driver_min_remit_amount)) {
            $driver_min_remit_amount = 0;
        }
        $this->cimongo->select('outstanding_amount.amount');
        $this->cimongo->where(array('_id' => new \MongoId($driver_id)));
        $this->cimongo->where(array(
            'outstanding_amount.updated_on' => array(
                '$lt' => new \MongoDate(strtotime('-1 day'))
            ),
            'outstanding_amount.amount' => array(
                '$gte' => floatval($driver_min_remit_amount)
            )
        ));
        $driver_outstanding = $this->cimongo->get(DRIVERS);

        $this->cimongo->where(array('_id' => new \MongoId($driver_id)));
        if ($driver_outstanding->num_rows() > 0) {
            $update_data = array(
                'outstanding_amount.is_due' => true,
                'availability' => 'No'
            );
            $return_status = true;
        } else {
            $update_data = array('outstanding_amount.is_due' => false);
            $return_status = false;
        }
        $this->cimongo->update(DRIVERS, $update_data);
        return $return_status;

    }

    public function subtract_outstanding_amount($driver_id, $amount)
    {
        date_default_timezone_set('UTC');
        $update_data = array('outstanding_amount.updated_on' => new MongoDate(time()));
        $condition = array('_id' => new \MongoId($driver_id));
        $field = 'outstanding_amount.amount';
        $this->cimongo->where($condition)->inc($field, (-1) * $amount)->update(DRIVERS, $update_data);

        $this->is_outstanding_due($driver_id);
    }

    public function find_closest_drivers($category_drivers, $pickup_lat, $pickup_lon, $attempt)
    {
        $driver_dispatch_1 = 5;
        if (!empty($this->config->item('driver_dispatch_1'))) {
            $driver_dispatch_1 = intval($this->config->item('driver_dispatch_1'));
        }
        $driver_dispatch_2 = 10;
        if (!empty($this->config->item('driver_dispatch_2'))) {
            $driver_dispatch_3 = intval($this->config->item('driver_dispatch_2'));
        }
        $driver_dispatch_3 = 15;
        if (!empty($this->config->item('driver_dispatch_3'))) {
            $driver_dispatch_3 = intval($this->config->item('driver_dispatch_3'));
        }
        $from = $pickup_lat . ',' . $pickup_lon;

        $first_range_drivers = array();
        $second_range_drivers = array();
        $third_range_drivers = array();

        foreach ($category_drivers['result'] as $driver) {
            $distance = $driver['distance'];
            $eta = $this->app_model->calculateETA($distance);
            if ($eta <= $driver_dispatch_1) {
                $first_range_drivers[] = $driver;
            }
            if ($eta <= $driver_dispatch_2) {
                $second_range_drivers[] = $driver;
            }
            if ($eta <= $driver_dispatch_3) {
                $third_range_drivers[] = $driver;
            }
        } 
        if ($attempt == 1 && count($first_range_drivers) > 0) {
            return $first_range_drivers;
        } else if ($attempt == 1 && count($second_range_drivers) > 0) {
            return $second_range_drivers;
        } else if (count($third_range_drivers) > 0) {
            return $third_range_drivers;
        } else {
            return false;
        }
    }

    public function get_driver_image($image)
    {
        return (isset($image) && !empty(trim($image)) ? USER_PROFILE_IMAGE . $image : USER_PROFILE_IMAGE_DEFAULT);
    }
}