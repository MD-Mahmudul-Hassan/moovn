<?php error_reporting(0); ?>
<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title><?php echo $this->config->item('email_title'); ?></title>
    </head>

    <body style="font-family: sans-serif; margin:0;padding:0;">
        <div style="width:680px; margin:0 auto;border:1px solid #ccc;">
            <table width="100%" style="margin:0;padding:0;border-spacing: 0;border:0;border-collapse: initial;">
                <tr style="background:#000;width:100%;">
                    <td style="width:50%;padding-left: 10px;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;">
                        <img src="<?php echo base_url() . 'images/logo/' . $this->config->item('logo_image'); ?>" style="width:150px" >
                    </td>
                    <td style="text-align:right;width:50%;padding-left: 10px;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;">
                        <p style="margin:0;color:#fff;font-size:14px;">INVOICE NO:<?php echo $ride_info->ride_id; ?></p>
                        <span style="margin:0;color:#fff;font-size:12px;"><?php echo date("d M, Y", $ride_info->booking_information['pickup_date']->sec); ?></span>
                    </td>
                </tr>
                <tr style="background:#27CAF8;width:100%;">
                    <td colspan="2" style="width:100%;padding-left: 10px;padding-top: 12px;padding-right: 10px;padding-bottom: 12px;text-align:center;border-bottom:5px solid #ccc;">
                        <h2 style="color:#fff;font-size:18px;margin:0;"><?php echo $ride_info->user['name']; ?></h2>
                        <span style="color:#fff;font-size:15px;margin:0;">Thanks for using <?php echo $this->config->item('email_title'); ?></span>
                    </td>
                </tr>
                <tr style="background:#fff;width:100%;">
                    <td colspan="2" style="width:100%;padding-left: 10px;padding-top: 20px;padding-right: 10px;padding-bottom: 20px;text-align:center;">
                        <img src="<?php echo base_url() . 'images/site/Car.png'; ?>">
                        <br>
                        <br>
                        <p style="color:#000;font-size:16px;margin:0;">TOTAL FARE</p>
                        <h3 style="color:#27CAF8;font-size:50px;margin:0;"><font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo number_format($ride_info->total['grand_fare'], 2); ?></h3> 

                        <?php
                        if (isset($ride_info->total['tips_amount'])) {
                            if ($ride_info->total['tips_amount'] > 0) {
                                ?>
                                <p style="color:#27CAF8;font-size:14px;margin:0;"> ( TIPS:<?php
                                    echo $rcurrencySymbol;
                                    echo number_format($ride_info->total['tips_amount'], 2);
                                    ?> )</p>
                                <?php
                            }
                        }
                        ?>

                        <p style="color:#000;font-size:12px;margin:0;margin-bottom:3px;">TOTAL DISTANCE: <?php echo $ride_info->summary['ride_distance']; ?> <?php echo $ride_distance_unit;?></p>
                        <p style="color:#000;font-size:12px;margin:0;">TOTAL RIDE TIME: <?php echo $ride_info->summary['ride_duration']; ?> min</p>
                    </td>
                </tr>
                <tr style="background:#fff;width:100%;">
                    <td style="width:50%;padding-left: 10px;padding-top: 10px;padding-right: 10px;padding-bottom: 20px;text-align:center;">
                        <p style="color:#000;font-size:12px;margin:0;"><?php echo $this->config->item('site_name_capital'); ?> MONEY DEDUCTED</p>
                        <span style="color:#000;font-size:14px;margin:0;"><?php echo number_format($ride_info->total['wallet_usage'], 2); ?></span>
                    </td>
                    <td style="width:50%;padding-left: 10px;padding-top: 10px;padding-right: 10px;padding-bottom: 20px;text-align:center;">
                        <p style="color:#000;font-size:12px;margin:0;">CASH PAID</p>
                        <span style="color:#000;font-size:14px;margin:0;"><?php echo number_format($ride_info->total['paid_amount'], 2); ?></span>
                    </td>
                </tr>
            </table>
            <table cellspacing="10" style="width:100%;background:#fff;border-top: 1px solid #ddd;">
                <tr>
                    <th colspan="2" width="48%" style="text-align:center; background:#fff;vertical-align:middle;">
                <h4 style="background:#27CAF8;color:#fff;padding-top: 5px;padding-bottom: 5px;margin:0;">FARE BREAKUP</h4>
                </th>
                <th colspan="2" width="48%" style="text-align:center; background:#fff;vertical-align:middle;">
                <h4 style="background:#27CAF8;color:#fff;padding-top: 5px;padding-bottom: 5px;margin:0;">TAX BREAKUP</h4>
                </th>
                </tr>
                <tr>
                    <td style="font-size:14px;color:#333;">
                        Base fare for <?php echo $ride_info->fare_breakup['min_km']; ?> <?php echo $ride_distance_unit;?>:
                    </td>
                    <td style="font-size:14px;color:#333;"><font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo number_format($ride_info->total['base_fare'], 2); ?>
                    </td>
                    <td style="font-size:14px;color:#333;">
                        Service Tax
                    </td>
                    <td style="font-size:14px;color:#333;"><font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo number_format($ride_info->total['service_tax'], 2); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:14px;color:#333;">
                        <?php
                        if ($ride_info->summary['ride_distance'] > $ride_info->fare_breakup['min_km']) {
                            $after_min_distance = $ride_info->summary['ride_distance'] - $ride_info->fare_breakup['min_km'];
                        } else {
                            $after_min_distance = 0;
                        }
                        ?>
                        Rate for <?php echo $after_min_distance; ?> <?php echo $ride_distance_unit;?>:
                    </td>
                    <td style="font-size:14px;color:#333;"><font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo number_format($ride_info->total['distance'], 2); ?>
                    </td>
                    <td style="font-size:14px;color:#333;">
                        (Taxes added to your total fare)
                    </td>
                </tr>
                <tr>
                    <td style="font-size:14px;color:#333;">
                        Free ride time (<?php echo $ride_info->fare_breakup['min_time']; ?> min)
                    </td>
                    <td style="font-size:14px;color:#333;"><font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font>0.0
                    </td>
                </tr>
                <tr>
                    <td style="font-size:14px;color:#333;">
                        <?php
                        if ($ride_info->summary['ride_duration'] > $ride_info->fare_breakup['min_time']) {
                            $after_min_duration = $ride_info->summary['ride_duration'] - $ride_info->fare_breakup['min_time'];
                        } else {
                            $after_min_duration = 0;
                        }
                        ?>
                        Ride time charge for <?php echo $after_min_duration; ?> min:
                    </td>
                    <td style="font-size:14px;color:#333;"><font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo number_format($ride_info->total['ride_time'], 2); ?>
                    </td>
                </tr>
                <?php if ($ride_info->fare_breakup['peak_time_charge'] != '') { ?>
                    <tr>
                        <td style="font-size:14px;color:#333;">
                            Peak Pricing charge (<?php echo $ride_info->fare_breakup['peak_time_charge']; ?>x)
                        </td>
                        <td style="font-size:14px;color:#333;"><font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo number_format($ride_info->total['peak_time_charge'], 2); ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($ride_info->fare_breakup['night_charge'] != '') { ?>
                    <tr>
                        <td style="font-size:14px;color:#333;">Night Charge(<?php echo $ride_info->fare_breakup['night_charge']; ?>x)
                        </td>
                        <td style="font-size:14px;color:#333;"><font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo number_format($ride_info->total['night_time_charge'], 2); ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($ride_info->total['coupon_discount'] > 0) { ?>
                    <tr>
                        <td style="font-size:14px;color:#333;">
                            Discount
                        </td>
                        <td style="font-size:14px;color:#333;"><font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo number_format($ride_info->total['coupon_discount'], 2); ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <table cellspacing="10" style="width:100%;background:#fff;border-bottom:1px solid #ccc;padding-bottom:10px;">
                <tr>
                    <th colspan="2" width="100%" style="text-align:center; background:#fff;vertical-align:middle;">
                <h4 style="background:#27CAF8;color:#fff;padding-top: 5px;padding-bottom: 5px;margin:0;">BOOKING DETAILS</h4>
                </th>
                </tr>
                <tr>
                    <td style="font-size:14px;color:#333;">
                        Service type
                    </td>
                    <td style="font-size:14px;color:#333;"><?php echo $ride_info->location['name'] . ', ' . $ride_info->booking_information['service_type']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:14px;color:#333;">
                        Booking Date
                    </td>
                    <td style="font-size:14px;color:#333;"><?php echo date("d M, Y, h:i A", $ride_info->booking_information['booking_date']->sec); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:14px;color:#333;">
                        Pickup Date
                    </td>
                    <td style="font-size:14px;color:#333;"><?php echo date("d M, Y, h:i A", $ride_info->booking_information['pickup_date']->sec); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:14px;color:#333;">
                        Booking Email id
                    </td>
                    <td style="font-size:14px;color:#333;"><a href="mailto:<?php echo $ride_info->booking_information['booking_email']; ?>" style="color: #15c;"><?php echo $ride_info->booking_information['booking_email']; ?></a>
                    </td>
                </tr>
            </table>
            <table style="width:100%;background:#fff;">
                <tr>
                    <td style="width:100%;padding-left: 10px;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;">
                        <p style="font-size:12px;line-height:18px;color:#333;margin:0;margin-bottom:5px;">Minimun bill of <font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo number_format($ride_info->fare_breakup['min_fare'], 2); ?> for the first <?php echo $ride_info->fare_breakup['min_km']; ?> <?php echo $ride_distance_unit;?> and <font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo $ride_info->fare_breakup['per_km']; ?>/<?php echo $ride_distance_unit;?> thereafter. Ride time at <font style="font-family : DejaVu Sans, Helvetica, sans-serif;"><?php echo $rcurrencySymbol; ?></font><?php echo $ride_info->fare_breakup['per_minute']; ?> per min after first <?php echo $ride_info->fare_breakup['min_time']; ?> min. Includes waiting time during the trip.</p>
                        <p style="font-size:12px;line-height:18px;color:#333;margin:0;margin-bottom:5px;">Additional service tax is applicable on your fare. Toll and parking charges are extra.</p>
                        <p style="font-size:12px;line-height:18px;color:#333;margin:0;margin-bottom:5px;">We levy Peak Pricing charges when the demand is high, so that we can make more cabs available to you and continue to serve you efficiently.</p>
                        <p style="font-size:12px;line-height:18px;color:#333;margin:0;margin-bottom:5px;">For further queries, please write to
                            <a href="mailto:<?php echo $this->config->item('site_contact_mail'); ?>" style="color: #15c;"><?php echo $this->config->item('site_contact_mail'); ?></a>
                        </p>
                        <p style="font-size:12px;line-height:18px;color:#333;margin:0;">This is an electronically generated invoice and does not require signature. All terms and conditions are as given on <a href="<?php echo base_url(); ?>" style="color: #15c;"><?php echo base_url(); ?></a>
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:100%;background:#000;text-align:center;">
                <tr>
                    <td style="width:100%;padding-left: 10px;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;">
                        <?php /* <p style="font-size:13px;line-height:18px;color:#fff;margin:0;margin-bottom:5px;">A wing Suntech Center,37-40 Sbhash Road, Chennai, India</p> 
                          <p style="font-size:13px;line-height:18px;color:#fff;margin:0;margin-bottom:5px;">Tel:+91 22 3322 5566, Fax: 022 32154569,
                          <a href="#" style="color: #27CAF8;"><?php echo base_url(); ?></a>
                          </p> */ ?>
                        <h6 style="color:#27CAF8;font-size:13px;margin:0;"><?php echo $this->config->item('footer_content'); ?></h6>
                    </td>
                </tr>
            </table>
        </div>
    </body>

</html>