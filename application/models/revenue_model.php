<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to Revenue and commission management
 * @author Casperon
 *
 */
class Revenue_model extends My_Model{
	public function __construct(){
        parent::__construct();
    }
	
	/**
	*
	* This function return the rides details
	*	String $driver_id
	*
	**/
	public function get_ride_details($driver_id = '', $start_date = '', $end_date = '', $having = '', $location_id = '')
	{		
		if ($start_date != '' && $end_date != '') {
			$matchArr = array(
				'$match' => array(
					'driver.id' => array('$eq' => $driver_id),
					'ride_status' => array('$eq' => 'Completed'),
					'pay_status' => array('$eq' => 'Paid'),
					'history.end_ride' => array('$gte' => new \MongoDate($start_date), '$lte' => new \MongoDate($end_date))
				)
			);
		} else {
			$matchArr = array(
				'$match' => array(
					'driver.id' => array('$eq' => $driver_id),
					'ride_status' => array('$eq' => 'Completed'),
					'pay_status' => array('$eq' => 'Paid')
				)
			);		
		}
		
		if ($location_id != '') {
			$matchArr['$match']['location.id'] = array('$eq' => $location_id);
		}
		
		if ($having == 'site') {
			$matchArr['$match']['pay_summary.type'] = array('$in' => array('Gateway', 'Wallet', 'Wallet_Gateway', 'FREE'));
			$groupArr = array(
				'$group' => array(
					'_id' =>'$driver.id',
					'tipsAmount' => array('$sum' => '$total.tips_amount'),
					'site_having' => array('$sum' => '$total.paid_amount')
				)
			);
		} else if ($having == 'driver') {
			$matchArr['$match']['pay_summary.type'] = array('$in' => array('Cash', 'Wallet_Cash'));
			$groupArr=array(
				'$group' => array(
					'_id' => '$driver.id',
					'tipsAmount' => array('$sum' => '$total.tips_amount'),
					'driver_having' => array('$sum' => '$total.paid_amount'),
				)
			);
		} else {
			$groupArr=array(
				'$group' => array(
					'_id' => '$driver.id',
					'totalTrips' => array('$sum' => 1),
					'totalAmount' => array('$sum' => '$total.grand_fare'),
					'couponAmount' => array('$sum' =>'$total.coupon_discount'),
					'tipsAmount' => array('$sum' => '$total.tips_amount'),
					'by_wallet' => array('$sum' => '$total.wallet_usage'),
					'site_earnings' => array('$sum' => '$amount_commission'),
					'driver_earnings' => array('$sum' => '$driver_revenue'),
					'amount_in_site' => array('$sum' => '$amount_detail.amount_in_site'),
					'amount_in_driver' => array('$sum' => '$amount_detail.amount_in_driver')
				)
			);
		}		
		$option = array(
			array(
				'$project' => array(
					'ride_id' => 1,
					'commission_percent' => 1,
					'driver' => 1,
					'total' => 1,
					'booking_information' => 1,
					'ride_status' => 1,
					'pay_status' => 1,
					'summary' => 1,
					'pay_summary' => 1,
					'history' => 1,
					'driver_revenue' => 1,
					'amount_commission' => 1,
					'amount_detail' => 1,
					'location' => 1
				)
			),
			$matchArr,
			$groupArr
		);
		$res = $this->cimongo->aggregate(RIDES,$option);
		return $res;
	}
	
	/**
	*
	* This function return the rides details
	*	String $driver_id
	*
	**/
	public function get_ride_summary($start_date = '',$end_date = '',$having = '',$location_id=''){
		
		if($start_date!='' && $end_date!=''){
			$matchArr=array(
										'$match' => array(
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid'),
											'history.end_ride' =>array('$gte'=>new \MongoDate($start_date),'$lte'=>new \MongoDate($end_date))
										)
									);
		}else{
			$matchArr=array(
										'$match' => array(
											'ride_status' =>array('$eq'=>'Completed'),
											'pay_status' =>array('$eq'=>'Paid')
										)
									);		
		}
		
		if($location_id != ''){
			$matchArr['$match']['location.id'] = array('$eq'=>$location_id);
		}
		#echo '<pre>'; print_r($matchArr); die;
		$groupArr=array(
									'$group' => array(
										'_id' =>'$ride_status',
										'totalTrips'=>array('$sum'=>1),
										'totalAmount'=>array('$sum'=>'$total.grand_fare'),
										'couponAmount'=>array('$sum'=>'$total.coupon_discount'),
										'by_wallet'=>array('$sum'=>'$total.wallet_usage'),
										'site_earnings'=>array('$sum'=>'$amount_commission'),
										'driver_earnings'=>array('$sum'=>'$driver_revenue')
									)
								);
		
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
										'location' =>1,
									)
								),
								$matchArr,
								$groupArr
							);
		#echo "<pre>";print_r($option);
		$res = $this->cimongo->aggregate(RIDES,$option);
		return $res;
	}
	
	/**
	*
	* This function return the trip summary
	*	String $driver_id
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
}