<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to Revenue and commission management
 * @author Casperon
 *
 */
class Mileage_model extends My_Model{
	public function __construct(){
        parent::__construct();
    }
	
	/**
	*
	* This function return the rides details
	*	String $driver_id
	*
	**/
	public function update_mileage_system($driver_id='',$start_time='',$type='',$distance='',$distance_unit='',$ride_id=''){
		
			$checkMileage = $this->get_selected_fields(DRIVERS_MILEAGE, array('driver_id' => new \MongoId($driver_id)),array('_id'));
			$checkDriver = $this->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)),array('_id','total_mileage','total_duration','roaming_mileage','roaming_duration','pickup_mileage','pickup_duration','drop_mileage','drop_duration'));
			$end_time=time();
			if($distance_unit == 'mi'){
				$distance = round(($distance / 1.609344),2);
			}
			
		
			$total_mileage=$distance;
			if(isset($checkDriver->row()->total_mileage)) {
				
				$total_mileage=$distance+$checkDriver->row()->total_mileage;
			}
			$total_duration_min=floor(($end_time-$start_time)/60);
			
			$total_duration=floor(($end_time-$start_time)/60);
			if(isset($checkDriver->row()->total_duration)) {
				$total_duration=$total_duration_min+$checkDriver->row()->total_duration;
			}
			$update_totalArr=array('total_mileage'=>floatval($total_mileage),'total_duration'=>floatval($total_duration));
			$this->update_details(DRIVERS, $update_totalArr,array('_id' => new \MongoId($driver_id)));
			/*** update total mileage ***/
			switch($type) {
			
			    case 'free-roaming':
						$roaming_mileage=$distance;
						if(isset($checkDriver->row()->roaming_mileage)) {
							
							$roaming_mileage=$distance+$checkDriver->row()->roaming_mileage;
						}
						
						$roaming_duration=floor(($end_time-$start_time)/60);
						
						if(isset($checkDriver->row()->roaming_duration)) {
							$roaming_duration=$roaming_duration+$checkDriver->row()->roaming_duration;
						}
						$update_roaming_Arr=array('roaming_mileage'=>floatval($roaming_mileage),'roaming_duration'=>floatval($roaming_duration));
						$this->update_details(DRIVERS,$update_roaming_Arr,array('_id' => new \MongoId($driver_id)));
					break;
				case 'customer-pickup':
						$pickup_mileage=$distance;
						if(isset($checkDriver->row()->pickup_mileage)) {
							
							$pickup_mileage=$distance+$checkDriver->row()->pickup_mileage;
						}
						
						$pickup_duration=floor(($end_time-$start_time)/60);
						
						if(isset($checkDriver->row()->pickup_duration)) {
							$pickup_duration=$pickup_duration+$checkDriver->row()->pickup_duration;
						}
						$update_pickup_Arr=array('pickup_mileage'=>floatval($pickup_mileage),'pickup_duration'=>floatval($pickup_duration));
						$this->update_details(DRIVERS,$update_pickup_Arr,array('_id' => new \MongoId($driver_id)));
					break;
				case 'customer-drop':
						$drop_mileage=$distance;
						if(isset($checkDriver->row()->drop_mileage)) {
							
							$drop_mileage=$distance+$checkDriver->row()->drop_mileage;
						}
						
						$drop_duration=floor(($end_time-$start_time)/60);
						
						if(isset($checkDriver->row()->drop_duration)) {
							$drop_duration=$drop_duration+$checkDriver->row()->drop_duration;
						}
						$update_drop_Arr=array('drop_mileage'=>floatval($drop_mileage),'drop_duration'=>floatval($drop_duration));
						$this->update_details(DRIVERS,$update_drop_Arr,array('_id' => new \MongoId($driver_id)));
				 break;
					
			
			}
			if($checkMileage->num_rows() == 0) {
			
			 $dataArr = array('driver_id' => new \MongoId($driver_id),
							  'created'=>new \MongoDate(time()),
							  'mileage_data' => array(array(
							  'start_time' =>new \MongoDate($start_time),
							  'end_time' =>new \MongoDate($end_time),
							  'duration_min' =>floatval($total_duration_min),
							  'distance' =>$distance,
							  'type' =>$type,
							  'ride_id'=>$ride_id
							 )
							 ));
				$this->simple_insert(DRIVERS_MILEAGE, $dataArr);
			} else {
				
				$dataArr=array('mileage_data' =>array(
							  'start_time' =>new \MongoDate($start_time),
							  'end_time' =>new \MongoDate($end_time),
							  'duration_min' =>floatval($total_duration_min),
							  'distance' =>$distance,
							  'type' =>$type,
							  'ride_id'=>$ride_id
							  )
							 );
				$this->simple_push(DRIVERS_MILEAGE,array('driver_id' => new \MongoId($driver_id)),$dataArr);
				
			}
		
	}
	public function get_mileage_list($driver_array=array(),$start_date='',$end_date=''){
	  
	   $matchArr=array();
	   $unwindArr=array();
	   $groupArr=array();
	   if(!empty($driver_array) && $start_date!='' && $end_date!='') {
		   $matchArr=array('$match' => array('driver_id' =>array('$in'=>$driver_array),
											'mileage_data.start_time'=>array('$gte'=> new \MongoDate($start_date)),
											'mileage_data.end_time'=>array('$lte'=>new \MongoDate($end_date))
														
										   )
						
						);	
		 $unwindArr=array('$unwind' =>'$mileage_data');
		 $groupArr=array(
								  '$group' => array(
										'_id' =>array('type'=>'$mileage_data.type',
													'driver_id'=>'$driver_id',
												),
									'total_distance'=>array('$sum'=>'$mileage_data.distance'),
									'total_duration'=>array('$sum'=>'$mileage_data.duration_min'),
										
										
									)
								);
					$option = array(array('$project' => array(
										'driver_id' =>1,
										'mileage_data'=>1,
										'created' =>1
										
									)
								),
								$unwindArr,
								$matchArr,
								$groupArr
								
								
							);
		
	
		 $res = $this->cimongo->aggregate(DRIVERS_MILEAGE,$option);
		 
		} else if(!empty($driver_array)) {
			$matchArr=array('$match' => array('driver_id' =>array('$in'=>$driver_array)
											
														
										   )
						
						);	
		  $unwindArr=array('$unwind' =>'$mileage_data');
		  $groupArr=array(
								  '$group' => array(
										'_id' =>array('type'=>'$mileage_data.type',
													'driver_id'=>'$driver_id',
												),
									'total_distance'=>array('$sum'=>'$mileage_data.distance'),
									'total_duration'=>array('$sum'=>'$mileage_data.duration_min'),
										
										
									)
								);
			$option = array(array('$project' => array(
										'driver_id' =>1,
										'mileage_data'=>1,
										'created' =>1
										
									)
								),
								$unwindArr,
								$matchArr,
								$groupArr
								
								
							);
	
		 $res = $this->cimongo->aggregate(DRIVERS_MILEAGE,$option);
		}
		
		
		
	   
		
		$mileage_Record=array();
		if(!empty($res['result'])) {
			foreach($res['result'] as $row) {
			 
                if($row['_id']['type']=='customer-drop') {
				  
				  $mileage_Record[(string)$row['_id']['driver_id']]['drop_distance']=$row['total_distance'];
				  $mileage_Record[(string)$row['_id']['driver_id']]['drop_duration']=$row['total_duration'];
				  
				} else if($row['_id']['type']=='customer-pickup') {
					
					$mileage_Record[(string)$row['_id']['driver_id']]['pickup_distance']=$row['total_distance'];
				    $mileage_Record[(string)$row['_id']['driver_id']]['pickup_duration']=$row['total_duration'];
				} else  {
					$mileage_Record[(string)$row['_id']['driver_id']]['free_distance']=$row['total_distance'];
				    $mileage_Record[(string)$row['_id']['driver_id']]['free_duration']=$row['total_duration'];
				}
				
			}
		}
		
		return $mileage_Record;
	 
	}
	public function view_mileage_list($driver_id,$start_date,$end_date,$ride_id){
	   $matchArr=array();
	   if($driver_id!='' && $start_date!='' && $end_date!='' && $ride_id!='') {
		   $matchArr=array('$match' => array('driver_id' =>array('$eq'=>new \MongoId($driver_id)),
		   
											'mileage_data.ride_id'=>array('$eq'=>$ride_id),
											'mileage_data.start_time'=>array('$gte'=> new \MongoDate($start_date)),
											'mileage_data.end_time'=>array('$lte'=>new \MongoDate($end_date))
														
										   )
						
						);	
		
		} else if($driver_id!='' &&  $start_date!='' && $end_date!='') {
		
			$matchArr=array('$match' => array('driver_id' =>array('$eq'=>new \MongoId($driver_id)),
		   									'mileage_data.start_time'=>array('$gte'=> new \MongoDate($start_date)),
											'mileage_data.end_time'=>array('$lte'=>new \MongoDate($end_date))
														
										   )
						
						);
		


		} else if($driver_id!='' && $ride_id!='') {
		   #echo $ride_id;
		
			$matchArr=array('$match' => array('driver_id' =>array('$eq'=>new \MongoId($driver_id)),
											'mileage_data.ride_id'=>array('$eq'=>$ride_id)
	
										   )
						
						);	
		
		} else if($driver_id!='') {
			$matchArr=array('$match' => array('driver_id' =>array('$eq'=>new \MongoId($driver_id))
												
										   )
						
						);	
		
		
		}
		
		$unwindArr=array('$unwind' =>'$mileage_data');
	
		
	    $option = array(array('$project' => array(
										'driver_id' =>1,
										'mileage_data'=>1,
										'created' =>1
										
									)
								),
							  
								$unwindArr,
								$matchArr,
								
								
								
							);
		
		$res = $this->cimongo->aggregate(DRIVERS_MILEAGE,$option);
		
		
		return $res;
	 
	}
	
	
}?>