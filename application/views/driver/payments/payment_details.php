<?php
$this->load->view('driver/templates/header.php');
?>
<style>
    .ui-datepicker .ui-datepicker-buttonpane { background-image: none; margin: .7em 0 0 0; padding:0 .2em; border-left: 0; border-right: 0; border-bottom: 0; margin: 32px 0 0; }
	
</style>
<link href="css/admin_custom.css" rel="stylesheet" type="text/css" media="screen">
<div id="content">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon blocks_images"></span>
                    <h6><?php 
						if($this->lang->line('dash_revenue_summary') != '') echo stripslashes($this->lang->line('dash_revenue_summary')); else  echo 'Revenue Summary';
						?> : <?php echo date("j M Y",$bill_details['bill_from']->sec) . ' - ' . date("j M Y",$bill_details['bill_to']->sec); ?></h6>
                    <div style="float: right;line-height:40px;padding:0px 10px;height:39px;">
                    </div>
                </div>
                <div class="widget_content">
                    <?php if (!empty($rideList)) { ?>
                        <table class="custom-table tbl-open-close">
                            <thead>
                                <tr>
                                    <th><?php 
						if($this->lang->line('dash_S_No') != '') echo stripslashes($this->lang->line('dash_S_No')); else  echo 'S.No';
						?></th>
                                    <th><?php 
						if($this->lang->line('dash_ride_id') != '') echo stripslashes($this->lang->line('dash_ride_id')); else  echo 'Ride Id';
						?></th>
                                    <th><?php 
						if($this->lang->line('dash_date') != '') echo stripslashes($this->lang->line('dash_date')); else  echo 'Date';
						?></th>
                                    <th><?php 
						if($this->lang->line('dash_total_fare') != '') echo stripslashes($this->lang->line('dash_total_fare')); else  echo 'Total Fare';
						?></th>
						<th><?php 
						if($this->lang->line('dash_tips') != '') echo stripslashes($this->lang->line('dash_tips')); else  echo 'Tips';
						?></th>
                        <th><?php 
						if($this->lang->line('dash_coupon_amount') != '') echo stripslashes($this->lang->line('dash_coupon_amount')); else  echo 'Coupon Amount';
						?></th>
                                    <th><?php 
						if($this->lang->line('dash_amount_site') != '') echo stripslashes($this->lang->line('dash_amount_site')); else  echo 'Amount in Site';
						?></th>
                                    <th><?php 
						if($this->lang->line('dash_amount_driver') != '') echo stripslashes($this->lang->line('dash_amount_driver')); else  echo 'Amount in Driver';
						?></th>
                                    <th><?php 
						if($this->lang->line('dash_site_earnings') != '') echo stripslashes($this->lang->line('dash_site_earnings')); else  echo 'Site Earnings';
						?></th>
                                    <th><?php 
						if($this->lang->line('dash_driver_earnings') != '') echo stripslashes($this->lang->line('dash_driver_earnings')); else  echo 'Driver Earnings';
						?></th>
                                </tr>
                            </thead>
                            <?php $i = 0; ?>
                            <?php
                            $total_grand_fare = 0;
                            $total_coupon_discount = 0;
                            $total_amount_in_site = 0;
                            $total_amount_in_driver = 0;
                            $total_site_earnings = 0;
                            $total_driver_earnings = 0;
							$total_driver_tips = 0;
                            ?>							
                            <tbody>
                                <?php
                                foreach ($rideList as $ride) {
                                    $i++;
                                    ?>
                                    <?php
                                    $amount_in_site = 0;
                                    $amount_in_driver = 0;
                                    $site_earnings = 0;
                                    $driver_earnings = 0;
                                    $pay_type = '';
									$tips_amount = 0;

                                    $amount_in_site = $ride['total']['wallet_usage'];
									
									if(isset($ride['total']['tips_amount'])){
										$tips_amount = $ride['total']['tips_amount'];
									}
									if(isset($ride['amount_detail']['amount_in_site'])){
										$amount_in_site = $ride['amount_detail']['amount_in_site'];
									}
									if(isset($ride['amount_detail']['amount_in_driver'])){
										$amount_in_driver = $ride['amount_detail']['amount_in_driver'];
									}
									
									
                                    if (isset($ride['pay_summary']['type'])) {
                                        $pay_type = $ride['pay_summary']['type'];
                                    }
                                    if ($pay_type == '') {
                                        $pay_type = 'FREE';
                                    }
									
									$driver_earnings = $ride['driver_revenue'] + $tips_amount;
									
                                    ?>
                                    <tr id="<?php echo $ride['ride_id']; ?>">
                                        <td>
                                            <?php echo $i; ?>
                                            <em data-pickup="<?php echo $ride['booking_information']['pickup']['location']; ?>" data-drop="<?php echo $ride['booking_information']['drop']['location']; ?>" data-paytype="<?php echo $pay_type; ?>"></em>
                                        </td>
                                        <td><?php echo $ride['ride_id']; ?></td>
                                        <td><?php echo date("d-m-Y h:i A", $ride['booking_information']['pickup_date']->sec); ?></td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($ride['total']['grand_fare'], 2); ?></span>
                                            <span class="admin-currency">&nbsp;<?php #echo $dcurrencySymbol;               ?>&nbsp;</span>
                                        </td>
										<td>
                                            <span class="amt-right"><?php  echo number_format($tips_amount, 2); ?></span>
                                            <span class="admin-currency">&nbsp;<?php #echo $dcurrencySymbol;               ?>&nbsp;</span>
                                        </td>
										
                                        <td>
                                            <span class="amt-right"><?php echo number_format($ride['total']['coupon_discount'], 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($amount_in_site, 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($amount_in_driver, 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($ride['amount_commission'], 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="amt-right"><?php echo number_format($driver_earnings, 2); ?></span>
                                        </td>
                                    </tr>
                                    <?php
									$total_grand_fare+=$ride['total']['grand_fare'];
                                    $total_coupon_discount+=$ride['total']['coupon_discount'];
                                    $total_amount_in_site+=$amount_in_site;
                                    $total_amount_in_driver+=$amount_in_driver;
                                    $total_site_earnings+=$ride['amount_commission'];
                                    $total_driver_earnings+=$driver_earnings;
									if(isset($ride['total']['tips_amount'])){
										$total_driver_tips+=$ride['total']['tips_amount'];
									}
                                    ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan='4'> <?php 
						if($this->lang->line('dash_trip_summary') != '') echo stripslashes($this->lang->line('dash_trip_summary')); else  echo 'Trip Summary';
						?>: </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_grand_fare, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
									<th>
                                        <span class="amt-right"><?php echo number_format($total_driver_tips, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_coupon_discount, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_amount_in_site, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_amount_in_driver, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_site_earnings, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                    <th>
                                        <span class="amt-right"><?php echo number_format($total_driver_earnings, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
						<?php if(!empty($bill_details)){ ?>
                        <table class="custom-table grid_5 right">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?php 
						if($this->lang->line('dash_site_owner') != '') echo stripslashes($this->lang->line('dash_site_owner')); else  echo 'Site Owner';
						?></th>
                                    <th><?php 
						if($this->lang->line('dash_driver') != '') echo stripslashes($this->lang->line('dash_driver')); else  echo 'Driver';
						?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php 
						if($this->lang->line('dash_payment_received') != '') echo stripslashes($this->lang->line('dash_payment_received')); else  echo 'Payment Received';
						?></td>
                                    <td>
                                        <?php $payment_in_site = $total_coupon_discount + $total_amount_in_site; ?>
                                        <span class="amt-right"><?php echo number_format($payment_in_site, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                    <td>
                                        <span class="amt-right"><?php echo number_format($total_amount_in_driver, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php 
						if($this->lang->line('dash_earnings') != '') echo stripslashes($this->lang->line('dash_earnings')); else  echo 'Earnings';
						?></td>
                                    <td>
                                        <span class="amt-right"><?php echo number_format($total_site_earnings, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                    <td>
                                        <span class="amt-right"><?php echo number_format($total_driver_earnings, 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php 
						if($this->lang->line('dash_payment_due') != '') echo stripslashes($this->lang->line('dash_payment_due')); else  echo 'Payment Due';
						?></td>
                                    <td>
                                        <span class="amt-right"><?php echo number_format($bill_details['site_pay_amount'], 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                    <td>
                                        <span class="amt-right"><?php echo number_format($bill_details['driver_pay_amount'], 2); ?></span>
                                        <span class="admin-currency">&nbsp;<?php echo $dcurrencySymbol; ?>&nbsp;</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
						<?php } ?>

                    <?php } else { ?>
                        <h3> <?php 
						if($this->lang->line('dash_no_trips_between_dates') != '') echo stripslashes($this->lang->line('dash_no_trips_between_dates')); else  echo 'No trips between this dates';
						?></h3>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span>
</div>

<link rel="stylesheet" type="text/css" media="all" href="plugins/timepicker/jquery-ui-timepicker-addon.css" />
<link rel="stylesheet" type="text/css" media="all" href="plugins/timepicker/jquery-ui-timepicker-addon.min.css" />
<script type="text/javascript" src="plugins/timepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="plugins/timepicker/jquery-ui-sliderAccess.js"></script>
<?php
$this->load->view('admin/templates/footer.php');
?>