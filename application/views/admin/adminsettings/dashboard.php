<?php
$this->load->view('admin/templates/header.php');
extract($privileges);
#print_r($monthlyEarningsGraph);
?> 
<?php if (!empty($monthlyEarningsGraph)) { ?>
    <script>
        var totalEarningsa = jQuery.parseJSON('<?php echo json_encode($monthlyEarningsGraph); ?>');
        var siteearningsa = jQuery.parseJSON('<?php echo json_encode($monthlySiteEarningsGraph); ?>');
         console.log(totalEarningsa);
         console.log(siteearningsa); 

        $(function () {
            $.jqplot._noToImageButton = true;
            var totalEarnings = totalEarningsa;
            var siteearnings = siteearningsa;

            var plot1 = $.jqplot("chart1", [totalEarnings, siteearnings], {
                seriesColors: ["rgba(78, 135, 194, 0.7)", "rgb(211, 235, 59)"],
                highlighter: {
                    show: true,
                    sizeAdjust: 1,
                    tooltipOffset: 9
                },
                grid: {
                    background: 'rgba(57,57,57,0.0)',
                    drawBorder: false,
                    shadow: false,
                    gridLineColor: '#666666',
                    gridLineWidth: 2
                },
                legend: {
                    show: true,
                    placement: 'outside'
                },
                seriesDefaults: {
                    rendererOptions: {
                        smooth: true,
                        animation: {
                            show: true
                        }
                    },
                    showMarker: false
                },
                series: [
                    {
                        fill: true,
                        label: '<?php echo get_language_value_for_keyword("Total",$this->data['langCode']); ?>'
                    },
                    {
                        label: '<?php echo get_language_value_for_keyword("Site",$this->data['langCode']); ?>'
                    }
                ],
                axesDefaults: {
                    rendererOptions: {
                        baselineWidth: 1.5,
                        baselineColor: '#444444',
                        drawBaseline: false
                    }
                },
                axes: {
                    xaxis: {
                        renderer: $.jqplot.DateAxisRenderer,
                        tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                        tickOptions: {
                            formatString: "%b %Y ",
                            angle: -30,
                            textColor: '#dddddd'
                        },
                        min: "<?php echo date("Y-m-d", strtotime("-1 year")); ?>",
                        max: "<?php echo date("Y-m-d"); ?>",
                        tickInterval: "30 days",
                        drawMajorGridlines: false
                    },
                    yaxis: {
                        renderer: $.jqplot.LogAxisRenderer,
                        pad: 0,
                        rendererOptions: {
                            minorTicks: 1
                        },
                        tickOptions: {
                            formatString: "<?php #echo $dcurrencySymbol;         ?>%'d",
                            showMark: false
                        }
                    }
                }
            });

        });


    </script>
<?php } ?>
<div id="content" style="clear:both;">
    <div class="grid_container">		
        <div class="grid_6">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"><span style="display: none;"></span><canvas width="16" height="16"></canvas></span>
                    <h6><?php if ($this->lang->line('admin_dashboard_site_statistics') != '') echo stripslashes($this->lang->line('admin_dashboard_site_statistics')); else echo 'Site Statistics'; ?></h6>
                </div>
                <div class="widget_content">
                    <div class="stat_block">
                        <div class="social_activities">								
                            <a class="activities_s redbox" href="admin/users/display_user_list">
                                <div class="block_label">
                                    <span class="user_icon"></span><div class="clear"></div>
									<?php if ($this->lang->line('admin_dashboard_site_user') != '') echo stripslashes($this->lang->line('admin_dashboard_site_user')); else echo 'Users'; ?>
                                    <span><?php if (isset($totalUsers)) echo $totalUsers; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s greenbox" href="admin/drivers/display_drivers_list">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?>
                                    <span><?php if (isset($totalDrivers)) echo $totalDrivers; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s bluebox" href="admin/promocode/display_promocode">
                                <div class="block_label">
                                    <span class="seller_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_coupon_codes') != '') echo stripslashes($this->lang->line('admin_dashboard_coupon_codes')); else echo 'Coupon Codes'; ?>
                                    <span><?php if (isset($totalcouponCode)) echo $totalcouponCode; ?></span>
                                </div>	
                            </a>								
                            <a class="activities_s purplebox" href="admin/location/display_location_list">
                                <div class="block_label">
                                    <span class="seller_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_locations') != '') echo stripslashes($this->lang->line('admin_dashboard_locations')); else echo 'Locations'; ?>
                                    <span><?php if (isset($totalLocations)) echo $totalLocations; ?></span>
                                </div>	
                            </a>								
                            <!--<a class="activities_s orangebox big" href="admin/revenue/display_site_revenue">
                                <div class="block_label">
                                    <span class="seller_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_total_earnings') != '') echo stripslashes($this->lang->line('admin_dashboard_total_earnings')); else echo 'Total Earnings'; ?> 
                                    <span> <?php echo $dcurrencySymbol; ?> <?php if (isset($totalEarnings)) echo $totalEarnings; ?></span>
                                </div>	
                            </a>-->								
                        </div>
                    </div>
                </div>
            </div>
        </div>			
        <div class="grid_6" >
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"><span style="display: none;"></span><canvas width="16" height="16"></canvas></span>
                    <h6><?php if ($this->lang->line('admin_dashboard_ride_statistics') != '') echo stripslashes($this->lang->line('admin_dashboard_ride_statistics')); else echo 'Ride Statistics'; ?></h6>
                </div>
                <div class="widget_content">
                    <div class="stat_block">
                        <div class="social_activities">								
                            <a class="activities_s redbox" href="admin/rides/display_rides?act=total">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_total_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_total_rides')); else echo 'Total Rides'; ?>
                                    <span><?php if (isset($totalRides)) echo $totalRides; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s greenbox" href="admin/rides/display_rides?act=Booked">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_upcomming_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_upcomming_rides')); else echo 'Upcoming Rides'; ?>
                                    <span><?php if (isset($upcommingRides)) echo $upcommingRides; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s bluebox" href="admin/rides/display_rides?act=OnRide">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_on_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_on_rides')); else echo 'On Rides'; ?>
                                    <span><?php if (isset($onRides)) echo $onRides; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s purplebox" href="admin/rides/display_rides?act=riderCancelled">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_rider_denied') != '') echo stripslashes($this->lang->line('admin_dashboard_rider_denied')); else echo 'Rider Denied'; ?>
                                    <span><?php if (isset($riderDeniedRides)) echo $riderDeniedRides; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s orangebox" href="admin/rides/display_rides?act=driverCancelled">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_driver_denied') != '') echo stripslashes($this->lang->line('admin_dashboard_driver_denied')); else echo 'Driver Denied'; ?>
                                    <span><?php if (isset($driverDeniedRides)) echo $driverDeniedRides; ?></span>
                                </div>
                            </a>								
                            <a class="activities_s pealbox" href="admin/rides/display_rides?act=Completed">
                                <div class="block_label">
                                    <span class="store_icon"></span><div class="clear"></div>
                                    <?php if ($this->lang->line('admin_dashboard_completed_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_completed_rides')); else echo 'Completed Rides'; ?>
                                    <span><?php if (isset($completedRides)) echo $completedRides; ?></span>
                                </div>
                            </a>								
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid_6">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"></span>
                    <h6><?php if ($this->lang->line('admin_dashboard_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_rides')); else echo 'Rides'; ?></h6>
                </div>
                <div class="widget_content">
                    <div class="stat_block">
                        <div class="stat_chart">							
                            <h4><?php if ($this->lang->line('admin_dashboard_rides_count') != '') echo stripslashes($this->lang->line('admin_dashboard_rides_count')); else echo 'Rides Count'; ?> : <?php if (isset($totalRides)) echo $totalRides; ?></h4>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_today') != '') echo stripslashes($this->lang->line('admin_dashboard_today')); else echo 'Today'; ?>
                                            (<?php echo date('jS \of M'); ?>)
                                        </td>
                                        <td>
                                            <?php if (isset($todayRides)) echo $todayRides; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="bar">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_this_month') != '') echo stripslashes($this->lang->line('admin_dashboard_this_month')); else echo 'This Month'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($monthRides)) echo $monthRides; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="line">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_this_year') != '') echo stripslashes($this->lang->line('admin_dashboard_this_year')); else echo 'This Year'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($yearRides)) echo $yearRides; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="line">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="pie_chart">
                                <?php
                                $completedRidesPercent = 0.00;
                                if (isset($totalRides) && isset($completedRides)) {
                                    if ($totalRides > 0) {
                                        $completedRidesPercent = ($completedRides * 100) / $totalRides;
                                    }
                                }
								
                                ?>
                                <span class="inner_circle"><?php echo round($completedRidesPercent, 1) . '%'; ?></span>
                                <span class="pie"><?php if (isset($completedRides)) echo $completedRides; ?>/<?php if (isset($totalRides)) echo $totalRides; ?></span>
                            </div>
                            <div class="chart_label">
                                <ul>
                                    <li><span class="new_visits"></span><?php if ($this->lang->line('admin_dashboard_completed_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_completed_rides')); else echo 'Completed Rides'; ?>: <?php if (isset($completedRides)) echo $completedRides; ?></li>
                                    <li><span class="unique_visits"></span><?php if ($this->lang->line('admin_dashboard_total_rides') != '') echo stripslashes($this->lang->line('admin_dashboard_total_rides')); else echo 'Total Rides'; ?>: <?php if (isset($totalRides)) echo $totalRides; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid_6">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"></span>
                    <h6><?php if ($this->lang->line('admin_dashboard_drivers') != '') echo stripslashes($this->lang->line('admin_dashboard_drivers')); else echo 'Drivers'; ?></h6>
                </div>
                <div class="widget_content">
                    <div class="stat_block">	
                        <div class="stat_chart">
                            <h4><?php if ($this->lang->line('admin_dashboard_drivers_count') != '') echo stripslashes($this->lang->line('admin_dashboard_drivers_count')); else echo 'Drivers Count'; ?> : <?php echo $totalDrivers; ?></h4>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_today') != '') echo stripslashes($this->lang->line('admin_dashboard_today')); else echo 'Today'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($todayDrivers)) echo $todayDrivers; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="bar">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_this_month') != '') echo stripslashes($this->lang->line('admin_dashboard_this_month')); else echo 'This Month'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($monthDrivers)) echo $monthDrivers; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="line">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php if ($this->lang->line('admin_dashboard_this_year') != '') echo stripslashes($this->lang->line('admin_dashboard_this_year')); else echo 'This Year'; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($yearDrivers)) echo $yearDrivers; ?>
                                        </td>
                                        <?php /* <td class="min_chart">
                                          <span class="line">20,30,50,200,250,280,350</span>
                                          </td> */ ?>
                                    </tr>
                                </tbody>
                            </table>
							<div class="pie_chart">
                                <?php
                                $activeDriversPercent = 0.00;
                                if (isset($activeDrivers)) {
                                    if ($totalDrivers > 0) {
                                        $activeDriversPercent = ($activeDrivers * 100) / $totalDrivers;
                                    }
                                }
                                ?>
                                <span class="inner_circle"><?php echo round($activeDriversPercent, 1) . '%'; ?></span>
                                <span class="pie"><?php if (isset($activeDrivers)) echo $activeDrivers; ?>/<?php if (isset($totalDrivers)) echo $totalDrivers; ?></span>
                            </div>
                            <div class="chart_label">
                                <ul>
                                    <li><span class="new_visits"></span><?php if ($this->lang->line('admin_dashboard_active_drivers') != '') echo stripslashes($this->lang->line('admin_dashboard_active_drivers')); else echo 'Active Drivers'; ?>: <?php if (isset($activeDrivers)) echo $activeDrivers; ?></li>
                                    <li><span class="unique_visits"></span><?php if ($this->lang->line('admin_dashboard_total_drivers') != '') echo stripslashes($this->lang->line('admin_dashboard_total_drivers')); else echo 'Total Drivers'; ?>: <?php if (isset($totalDrivers)) echo $totalDrivers; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <span class="clear"></span>
        <?php if (!empty($monthlyEarningsGraph)) { ?>
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <span class="h_icon graph"></span>
                        <h6><?php if ($this->lang->line('admin_dashboard_earnings') != '') echo stripslashes($this->lang->line('admin_dashboard_earnings')); else echo 'Earnings'; ?></h6>
                    </div>
                    <div class="widget_content">
                        <div class="data_widget black_g chart_wrap">
                            <div id="chart1">
                            </div>
                            <div id="chart6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span class="clear"></span>
        <?php } ?>
    </div>
    <span class="clear"></span>
</div>

</div>
<?php
$this->load->view('admin/templates/footer.php');
?>