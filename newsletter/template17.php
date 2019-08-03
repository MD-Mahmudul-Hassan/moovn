<?php $message .= '<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" align=\"center\" bgcolor=\"#4D4C50\">
<tbody>
<tr>
<td style=\"padding-top: 10px; padding-bottom: 10px;\" align=\"center\">
<table cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#4D4C50\">
<tbody>
<tr>
<td width=\"130\">&nbsp;</td>
<td width=\"105\"><a href=\"'.base_url().'\"><img style=\"display: block; line-height: 0px; font-size: 0px; border: 0px; color: #fa923c; width: 150px;\" src=\"'.base_url().'images/logo/'.$mail_logo.'\" alt=\"'.$meta_title.'\" width=\"105\" /></a></td>
<td width=\"130\">&nbsp;</td>
</tr>
</tbody>
</table>
<table style=\"background: linear-gradient(to right bottom, #2bcbf9, #a7a9ac) repeat scroll 0 0; padding: 0px 20px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" bgcolor=\"#9BC3AF\">
<tbody>
<tr>
<td style=\"background-size: cover;\" align=\"center\" valign=\"top\">
<table class=\"table600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"280\" align=\"center\">
<tbody>
<tr>
<td height=\"30\">&nbsp;</td>
</tr>
<tr>
<td style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 25px; color: black;\" align=\"center\">
<p>Your booking <span>'.$ride_id.' for '.$booking_date.' has been accepted.</span></p>
<p><span>Your driver is '.$driver_name.', '.$car_type.', license '.$license_plate.'</span></p>
<p><span><br /></span></p>
</td>
</tr>
<tr>
<td style=\"text-align: center; padding: 0px 0px 15px 0; color: #5a5a5a; font-size: 15px;\">
<p><span><span>Thank you,</span></span></p>
<p><span>'.$site_title.'</span> SUPPORT</p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<table style=\"margin: 0 auto; text-align: center; color: black;\" cellspacing=\"0\" cellpadding=\"0\">
<tbody>
<tr>
<td style=\"text-align: center; font-weight: inherit; padding-top: 50px;\">Need help? Email us at</td>
</tr>
<tr>
<td style=\"text-align: center; font-weight: inherit;\"><a style=\"color: black; font-weight: bold; text-decoration: none;\" onclick=\"return false\" rel=\"noreferrer\" href=\"./#NOP\">'.$site_contact_mail.'</a></td>
</tr>
<tr>
<td style=\"text-align: center; font-weight: inherit;\">'.$footer_content.'</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>';  ?>