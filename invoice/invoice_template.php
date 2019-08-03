<?php $message .= '<!--?php error_reporting(0); ?-->
<p>&nbsp;</p>
<div style=\"width: 680px; margin: 0 auto; border: 1px solid #ccc;\">
<table style=\"margin: 0; padding: 0; border-spacing: 0; border: 0; border-collapse: initial;\" width=\"100%\">
<tbody>
<tr style=\"background: #000; width: 100%;\">
<td style=\"width: 50%; padding: 10px;\"><img style=\"width: 150px;\" src=\"'.base_url().'images/logo/'.$logo_image.'\" alt=\"\" /></td>
<td style=\"text-align: right; width: 50%; padding: 10px;\">
<p style=\"margin: 0; color: #fff; font-size: 14px;\">RECEIPT FOR RIDE NO:'.$ride_id.'</p>
<span style=\"margin: 0; color: #fff; font-size: 12px;\">'.$pickup_date.'</span></td>
</tr>
<tr style=\"background: #27CAF8; width: 100%;\">
<td style=\"width: 100%; padding-left: 10px; padding-top: 12px; padding-right: 10px; padding-bottom: 12px; text-align: center; border-bottom: 5px solid #ccc;\" colspan=\"2\">
<h2 style=\"color: #fff; font-size: 18px; margin: 0;\">'.$user_name.'</h2>
<span style=\"color: #fff; font-size: 15px; margin: 0;\">Thanks for using '.$email_title.'</span></td>
</tr>
<tr style=\"background: #fff; width: 100%;\">
<td style=\"width: 100%; padding-left: 10px; padding-top: 20px; padding-right: 10px; padding-bottom: 20px; text-align: center;\" colspan=\"2\"><img src=\"'.base_url().'images/site/Car.png\" alt=\"\" /> <br /> <br />
<p style=\"color: #000; font-size: 16px; margin: 0;\">TOTAL FARE</p>
<h3 style=\"color: #27caf8; font-size: 50px; margin: 0;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span></h3>
<h3 style=\"color: #27caf8; font-size: 50px; margin: 0;\">'.$grand_fare.'</h3>
<p style=\"color: #27caf8; font-size: 14px; margin: 0;\">( TIPS:'.$rcurrencySymbol.' '.$tips_amount.')</p>
<p style=\"color: #000; font-size: 12px; margin: 0; margin-bottom: 3px;\">TOTAL DISTANCE: '.$ride_distance.''.$ride_distance_unit.'</p>
<p style=\"color: #000; font-size: 12px; margin: 0;\">TOTAL RIDE TIME: '.$ride_duration.' min</p>
</td>
</tr>
<tr style=\"background: #fff; width: 100%;\">
<td style=\"width: 50%; padding-left: 10px; padding-top: 10px; padding-right: 10px; padding-bottom: 20px; text-align: center;\">
<p style=\"color: #000; font-size: 12px; margin: 0;\">'.$site_name_capital.' MOBILE MONEY DEDUCTED</p>
<span style=\"color: #000; font-size: 14px; margin: 0;\">'.$wallet_usage.'</span></td>
<td style=\"width: 50%; padding-left: 10px; padding-top: 10px; padding-right: 10px; padding-bottom: 20px; text-align: center;\">
<p style=\"color: #000; font-size: 12px; margin: 0;\">CASH PAID</p>
<span style=\"color: #000; font-size: 14px; margin: 0;\">'.$paid_amount.'</span></td>
</tr>
</tbody>
</table>
<table>
<tbody>
<tr>
<td style=\"font-size: 14px; color: #333;\">Discount</td>
<td style=\"font-size: 14px; color: #333;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>'.$coupon_discount.'</td>
</tr>
</tbody>
</table>
<table style=\"width: 100%; background: #fff; border-top: 1px solid #ddd;\" cellspacing=\"10\">
<tbody>
<tr>
<th style=\"text-align: center; background: #fff; vertical-align: middle;\" colspan=\"2\" width=\"48%\">
<h4 style=\"background: #27CAF8; color: #fff; padding-top: 5px; padding-bottom: 5px; margin: 0;\">FARE BREAKUP</h4>
</th> <th style=\"text-align: center; background: #fff; vertical-align: middle;\" colspan=\"2\" width=\"48%\">
<h4 style=\"background: #27CAF8; color: #fff; padding-top: 5px; padding-bottom: 5px; margin: 0;\">TAX BREAKUP</h4>
</th>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Base fare for '.$fare_breakup_km.' '.$ride_distance_unit.':</td>
<td style=\"font-size: 14px; color: #333;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\"> '.$rcurrencySymbol.'</span>'.$base_fare.'</td>
<td style=\"font-size: 14px; color: #333;\">Service Tax</td>
<td style=\"font-size: 14px; color: #333;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>'.$service_tax.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Rate for '.$after_min_distance.' '.$ride_distance_unit.':</td>
<td style=\"font-size: 14px; color: #333;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>'.$distance.'</td>
<td style=\"font-size: 14px; color: #333;\">(Taxes added to your total fare)</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Free ride time ('.$fare_breakup_time.' min)</td>
<td style=\"font-size: 14px; color: #333;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>0</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Ride time charge for '.$after_min_duration.' min:</td>
<td style=\"font-size: 14px; color: #333;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>'.$ride_time.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Peak Pricing charge ('.$peak_time_charge_def.'x)</td>
<td style=\"font-size: 14px; color: #333;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>'.$peak_time_charge.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Night Charge('.$night_charge_def.' x)</td>
<td style=\"font-size: 14px; color: #333;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>'.$night_charge.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Wait Time Charge('.$wait_time_def.' x)</td>
<td style=\"font-size: 14px; color: #333;\"><span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>'.$wait_time.'</td>
</tr>
</tbody>
</table>
<table style=\"width: 100%; background: #fff; border-bottom: 1px solid #ccc; padding-bottom: 10px;\" cellspacing=\"10\">
<tbody>
<tr>
<th style=\"text-align: center; background: #fff; vertical-align: middle;\" colspan=\"2\" width=\"100%\">
<h4 style=\"background: #27CAF8; color: #fff; padding-top: 5px; padding-bottom: 5px; margin: 0;\">BOOKING DETAILS</h4>
</th>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">
<p>Service Location</p>
<p>Service Type</p>
</td>
<td style=\"font-size: 14px; color: #333;\">
<p>'.$location.'</p>
<p>'.$service_type.'</p>
</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Booking Date</td>
<td style=\"font-size: 14px; color: #333;\">'.$booking_date.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Pickup Date</td>
<td style=\"font-size: 14px; color: #333;\">'.$pickup_date.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333;\">Booking Email ID</td>
<td style=\"font-size: 14px; color: #333;\"><a style=\"color: #15c;\" href=\"mailto:'.$booking_email.'\">'.$booking_email.'</a></td>
</tr>
</tbody>
</table>
<table style=\"width: 100%; background: #fff;\">
<tbody>
<tr>
<td style=\"width: 100%; padding: 10px;\">
<p style=\"font-size: 12px; line-height: 18px; color: #333; margin: 0; margin-bottom: 5px;\">Minimun charge of <span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>'.$fare_breakup_fare.' was assessed for the first '.$fare_breakup_km.' '.$ride_distance_unit.' and <span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$rcurrencySymbol.'</span>'.$fare_breakup_per_km.'/'.$ride_distance_unit.' thereafter. Ride time was computed at <span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'. $rcurrencySymbol.'</span>'.$fare_breakup_per_min.' per min after first '.$fare_breakup_time.' min.</p>
<p style=\"font-size: 12px; line-height: 18px; color: #333; margin: 0; margin-bottom: 5px;\">Additional service tax is applicable on your fare. Toll and parking charges if applicable, may be charged separately.</p>
<p style=\"font-size: 12px; line-height: 18px; color: #333; margin: 0; margin-bottom: 5px;\">Please forward any questions to&nbsp;<a style=\"color: #15c;\" href=\"mailto:'.$site_contact_mail.'\">'.$site_contact_mail.'</a>&nbsp;</p>
<p style=\"font-size: 12px; line-height: 18px; color: #333; margin: 0;\">This is an electronically generated receipt and does not require a signature. All terms and conditions are as reflected on our <a title=\"About\" href=\"https://about.moovn.com\">ABOUT</a> page.</p>
</td>
</tr>
</tbody>
</table>
<table style=\"width: 100%; background: #000; text-align: center;\">
<tbody>
<tr>
<td style=\"width: 100%; padding: 10px;\"><span style=\"color: #ffffff;\">Moovn Technologies | 107 Spring Street - Fourth Floor | Seattle, WA 98104</span><br />
<p style=\"font-size: 13px; line-height: 18px; color: #fff; margin: 0; margin-bottom: 5px;\"><a style=\"color: #27caf8;\" href=\"#\"><!--?php echo base_url(); ?--></a></p>
<h6 style=\"color: #27caf8; font-size: 13px; margin: 0;\">'.$footer_content.'</h6>
</td>
</tr>
</tbody>
</table>
</div>';  ?>