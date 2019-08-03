<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	/* Refunding captured amount if cancellation amount not being applied */
	
	if ( ! function_exists('save_ride_details_for_stats'))
	{
		function save_ride_details_for_stats($ride_id) {
			$ci =& get_instance();
			$checkRide = $ci->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id),array('booking_information','user'));
			if($checkRide->num_rows() > 0 ){
				$dataArr = array(
									'user_id' => $checkRide->row()->user['id'],
									'location'=>$checkRide->row()->booking_information['pickup']['latlong'],'pickup_address'=> trim(
															preg_replace( "/\r|\n/", "", $checkRide->row()->booking_information['pickup']['location'] )
															),
									'category' => $checkRide->row()->booking_information['service_id'],
									'ride_time' => $checkRide->row()->booking_information['est_pickup_date']
								);
				$ci->app_model->simple_insert(RIDE_STATISTICS,$dataArr);
			}
			
		}
	}
	


/* End of file payment_helper.php */
/* Location: ./application/helpers/payment_helper.php */