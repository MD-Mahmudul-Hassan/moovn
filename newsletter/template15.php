<?php $message .= '<p>&nbsp;</p>
<table width=\"100%\">
<tbody>
<tr>
<td><img src=\"'.base_url().'images/logo/'.$logo_image.'\" alt=\"\" /></td>
<td>
<p>INVOICE NO:'.$ride_id.'</p>
<span>'.$pickup_date.'</span></td>
</tr>
<tr>
<td colspan=\"2\">
<h2>'.$user_name.'</h2>
<span>Thanks for using '.$email_title.'</span></td>
</tr>
<tr>
<td colspan=\"2\"><img src=\"'.base_url().'images/site/Car.png\" alt=\"\" />&nbsp;<br /><br />
<p>TOTAL FARE</p>
<h3><span>'.$rcurrencySymbol.'</span>'.$grand_fare.'</h3>
<p>( TIPS:'.$rcurrencySymbol.' '.$tips_amount.')</p>
<p>TOTAL DISTANCE: '.$ride_distance.''.$ride_distance_unit.'</p>
<p>TOTAL RIDE TIME: '.$ride_duration.' min</p>
</td>
</tr>
<tr>
<td>
<p>'.$site_name_capital.' MONEY DEDUCTED</p>
<span>'.$wallet_usage.'</span></td>
<td>
<p>CASH PAID</p>
<span>'.$paid_amount.'</span></td>
</tr>
</tbody>
</table>
<table>
<tbody>
<tr>
<td>Discount</td>
<td><span>'.$rcurrencySymbol.'</span>'.$coupon_discount.'</td>
</tr>
</tbody>
</table>
<table cellspacing=\"10\">
<tbody>
<tr>
<th colspan=\"2\" width=\"48%\">
<h4>FARE BREAKUP</h4>
</th><th colspan=\"2\" width=\"48%\">
<h4>TAX BREAKUP</h4>
</th>
</tr>
<tr>
<td>Base fare for '.$fare_breakup_km.' '.$ride_distance_unit.':</td>
<td><span>'.$rcurrencySymbol.'</span>'.$base_fare.'</td>
<td>Service Tax</td>
<td><span>'.$rcurrencySymbol.'</span>'.$service_tax.'</td>
</tr>
<tr>
<td>Rate for '.$after_min_distance.' '.$ride_distance_unit.':</td>
<td><span>'.$rcurrencySymbol.'</span>'.$distance.'</td>
<td>(Taxes added to your total fare)</td>
</tr>
<tr>
<td>Free ride time ('.$fare_breakup_time.' min)</td>
<td><span>'.$rcurrencySymbol.'</span>0</td>
</tr>
<tr>
<td>Ride time charge for '.$after_min_duration.' min:</td>
<td><span>'.$rcurrencySymbol.'</span>'.$ride_time.'</td>
</tr>
<tr>
<td>Peak Pricing charge ('.$peak_time_charge_def.'x)</td>
<td><span>'.$rcurrencySymbol.'</span>'.$peak_time_charge.'</td>
</tr>
<tr>
<td>Night Charge('.$night_charge_def.' x)</td>
<td><span>'.$rcurrencySymbol.'</span>'.$night_charge.'</td>
</tr>
<tr>
<td>Wait Time Charge('.$wait_time_def.' x)</td>
<td><span>'.$rcurrencySymbol.'</span>'.$wait_time.'</td>
</tr>
</tbody>
</table>
<table cellspacing=\"10\">
<tbody>
<tr>
<th colspan=\"2\" width=\"100%\">
<h4>BOOKING DETAILS</h4>
</th>
</tr>
<tr>
<td>Service type</td>
<td>'.$location.','.$service_type.'</td>
</tr>
<tr>
<td>Booking Date</td>
<td>'.$booking_date.'</td>
</tr>
<tr>
<td>Pickup Date</td>
<td>'.$pickup_date.'</td>
</tr>
<tr>
<td>Booking Email id</td>
<td><a href=\"mailto:'.$booking_email.'\">'.$booking_email.'</a></td>
</tr>
</tbody>
</table>
<table>
<tbody>
<tr>
<td>
<p>Minimun bill of&nbsp;<span>'.$rcurrencySymbol.'</span>'.$fare_breakup_fare.' for the first '.$fare_breakup_km.' '.$ride_distance_unit.' and&nbsp;<span>'.$rcurrencySymbol.'</span>'.$fare_breakup_per_km.'/'.$ride_distance_unit.' thereafter. Ride time at&nbsp;<span>'. $rcurrencySymbol.'</span>'.$fare_breakup_per_min.' per min after first '.$fare_breakup_time.' min. Includes waiting time during the trip.</p>
<p>Additional service tax is applicable on your fare. Toll and parking charges are extra.</p>
<p>We levy Peak Pricing charges when the demand is high, so that we can make more cabs available to you and continue to serve you efficiently.</p>
<p>For further queries, please write to&nbsp;<a href=\"mailto:'.$site_contact_mail.'\">'.$site_contact_mail.'</a></p>
<p>This is an electronically generated invoice and does not require signature. All terms and conditions are as given on&nbsp;<a href=\"'.base_url().'\">'.base_url().'</a></p>
</td>
</tr>
</tbody>
</table>';  ?>