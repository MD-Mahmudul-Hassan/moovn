<?php $message .= '<div style=\"width: 680px; margin: 0 auto; border: 1px solid #ccc;\">
<table style=\"margin: 0; padding: 0; border-spacing: 0; border: 0; border-collapse: initial;\" width=\"100%\">
<tbody>
<tr style=\"background: #000; width: 100%;\">
<td style=\"width: 50%; padding: 10px;\"><img style=\"width: 30%;\" src=\"'.base_url().'images/logo/'.$mail_logo.'\" alt=\"\" /></td>
<td style=\"text-align: right; width: 50%; padding: 10px;\">
<p style=\"margin: 0; color: #fff; font-size: 14px;\">INVOICE NO:'.$invoice_id.'</p>
<span style=\"margin: 0; color: #fff; font-size: 12px;\">'.$bill_date.'</span></td>
</tr>
<tr style=\"width: 100%;\">
<td style=\"width: 100%; padding-left: 10px; padding-top: 12px; padding-right: 10px; padding-bottom: 12px; text-align: left;\" colspan=\"2\">
<h2 style=\"color: #000; font-size: 18px; margin: 0;\">Thanks for using '.$mail_emailTitle.'! Here\'s your invoice.</h2>
</td>
</tr>
<tr style=\"width: 100%;\">
<td style=\"width: 100%; padding-left: 10px; padding-top: 12px; padding-right: 10px; padding-bottom: 12px; text-align: left;\" colspan=\"2\"><span style=\"color: #000; font-size: 15px; margin: 0;\">Hi  '.$driver_name.',</span></td>
</tr>
<tr style=\"width: 100%;\">
<td style=\"width: 100%; padding-left: 10px; padding-top: 12px; padding-right: 10px; padding-bottom: 12px; text-align: left;\" colspan=\"2\"><span style=\"color: #000; font-size: 15px; margin: 0;\"> Your '.$mail_emailTitle.' invoice for the period from '.$binsf.' is now available to view in your account. </span></td>
</tr>
<tr style=\"width: 100%;\">
<td style=\"width: 100%; padding-left: 10px; padding-top: 12px; padding-right: 10px; padding-bottom: 12px; text-align: left; border-bottom: 5px solid #ccc;\" colspan=\"2\">&nbsp;</td>
</tr>
</tbody>
</table>
<table style=\"width: 100%; background: #fff; border-bottom: 1px solid #ccc; padding-bottom: 10px;\" cellspacing=\"10\">
<tbody>
<tr>
<td style=\"font-size: 14px; color: #333; width: 50%; font-weight: bold;\">Number of trips</td>
<td style=\"font-size: 14px; color: #333;\">: '.$total_rides.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333; width: 50%; font-weight: bold;\">Grand Fare</td>
<td style=\"font-size: 14px; color: #333;\">: <span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$dcurrencySymbol.'</span> '.$total_revenue.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333; width: 50%; font-weight: bold;\">Commission</td>
<td style=\"font-size: 14px; color: #333;\">: <span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$dcurrencySymbol.'</span> '.$site_earnings.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333; width: 50%; font-weight: bold;\">Total Amount</td>
<td style=\"font-size: 14px; color: #333;\">: <span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$dcurrencySymbol.'</span> '.$driver_earnings.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333; width: 50%; font-weight: bold;\">Total Tips</td>
<td style=\"font-size: 14px; color: #333;\">: <span style=\"font-family: DejaVu Sans, Helvetica, sans-serif;\">'.$dcurrencySymbol.'</span> '.$tips_details.'</td>
</tr>
<tr>
<td style=\"font-size: 14px; color: #333; width: 50%;\">Click <a href=\"'.base_url().'driver/billing-summary/'.$invoice_id.'\" target=\"_blank\">here</a> to view the billing summary</td>
</tr>
</tbody>
</table>
<table style=\"width: 100%; background: #000; text-align: center;\">
<tbody>
<tr>
<td style=\"width: 100%; padding: 10px;\">
<h6 style=\"color: #27caf8; font-size: 13px; margin: 0;\">'.$mail_footerContent.'</h6>
</td>
</tr>
</tbody>
</table>
</div>';  ?>