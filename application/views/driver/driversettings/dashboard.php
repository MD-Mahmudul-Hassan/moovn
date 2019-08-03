<?php
$this->load->view('driver/templates/header.php');
?> 
<?php if (!empty($monthlyEarningsGraph)) { ?>
    <script>
        /*=================
         CHART 6
         ===================*/
        $(function () {



            var s1 = jQuery.parseJSON('<?php echo json_encode($monthlyEarningsGraph); ?>');
            var s2 = jQuery.parseJSON('<?php echo json_encode($monthlyEarningsGraph); ?>');
            console.log(s1);

            plot1 = $.jqplot("chart7", [s1], {
                // Turns on animatino for all series in this plot.
                animate: true,
                // Will animate plot on calls to plot1.replot({resetAxes:true})
                animateReplot: true,
                highlighter: {
                    show: false,
                    sizeAdjust: 1,
                    tooltipOffset: 9
                },
                seriesColors: ['#0F78B9'],
                seriesDefaults: {
                    renderer: $.jqplot.BarRenderer,
                    rendererOptions: {
                        // Set varyBarColor to tru to use the custom colors on the bars.
                        varyBarColor: true
                    },
                    pointLabels: {show: true}
                },
                axesDefaults: {
                    tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                    tickOptions: {
                        fontFamily: 'Georgia',
                        fontSize: '8pt'
                    }
                },
                axes: {
                    xaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer,
                        tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                        tickOptions: {
                            angle: -80,
                            formatString: "%b %d",
                            fontSize: '10pt',
                            textColor: '#000',
                        }
                    },
                    yaxis: {
                        tickOptions: {
                            formatString: "<?php echo $dcurrencySymbol; ?>%'d"
                        },
                        rendererOptions: {
                            forceTickAt0: true
                        }
                    },
                }
            });
        });

    </script>

<?php } ?>



<div id="content">
    <div class="grid_container">		


        <?php if (!empty($monthlyEarningsGraph)) { ?>
            <span class="clear"></span>	
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <span class="h_icon graph"></span>
                        <h6>
                            <?php
                            if ($this->lang->line('dash_last30_days') != '')
                                echo stripslashes($this->lang->line('dash_last30_days'));
                            else
                                echo 'Last 30 Days Earnings';
                            ?>
                        </h6>
                    </div>
                    <div class="widget_content">
                        <div class="stats_bar">
                            <div id="chart7" class="chart_block jqplot-target" style="position: relative;"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>



        <div class="item_widget" style="margin-bottom: 12px;">
            <div class="item_block">
                <div class="icon_block green_block">
                    <span class="item_icon">
                        <span class="stats_icon archives_sl"><a href="driver/rides/display_rides?act=total">
                                <?php
                                if ($this->lang->line('dash_total_rides') != '')
                                    echo stripslashes($this->lang->line('dash_total_rides'));
                                else
                                    echo 'Total Rides';
                                ?>
                            </a></span>
                    </span>
                </div>
                <h3><?php if (isset($totalRides)) echo $totalRides; ?></h3>
                <p>
                    <?php
                    if ($this->lang->line('dash_total_rides') != '')
                        echo stripslashes($this->lang->line('dash_total_rides'));
                    else
                        echo 'Total Rides';
                    ?>

                </p>
            </div>
            <div class="item_block">
                <div class="icon_block blue_block">
                    <span class="item_icon">
                        <span class="stats_icon calendar_sl	"><a href="driver/rides/display_rides?act=Upcoming"> 
                                <?php
                                if ($this->lang->line('dash_upcomming') != '')
                                    echo stripslashes($this->lang->line('dash_upcomming'));
                                else
                                    echo 'Upcoming';
                                ?>
                            </a></span>
                    </span>
                </div>
                <h3><?php if (isset($upcommingRides)) echo $upcommingRides; ?></h3>
                <p>
                    <?php
                    if ($this->lang->line('dash_upcomming') != '')
                        echo stripslashes($this->lang->line('dash_upcomming'));
                    else
                        echo 'Upcoming';
                    ?>

                </p>
            </div>
            <div class="item_block ">
                <div class="icon_block orange_block">
                    <span class="item_icon">
                        <span class="stats_icon communication_sl"><a href="driver/rides/display_rides?act=OnRide">
                                <?php
                                if ($this->lang->line('dash_on_rides') != '')
                                    echo stripslashes($this->lang->line('dash_on_rides'));
                                else
                                    echo 'On Rides';
                                ?>
                            </a></span>
                    </span>
                </div>
                <h3><?php if (isset($onRides)) echo $onRides; ?></h3>
                <p>
                    <?php
                    if ($this->lang->line('dash_on_rides') != '')
                        echo stripslashes($this->lang->line('dash_on_rides'));
                    else
                        echo 'On Rides';
                    ?>

                </p>
            </div>
            <div class="item_block">
                <div class="icon_block gray_block">
                    <span class="item_icon">
                        <span class="stats_icon badge_icon customers_sl"><a href="driver/rides/display_rides?act=riderCancelled"><?php
                                if ($this->lang->line('dash_rider_denied') != '')
                                    echo stripslashes($this->lang->line('dash_rider_denied'));
                                else
                                    echo 'Rider Denied';
                                ?></a></span>
                    </span>
                </div>
                <h3><?php if (isset($riderDeniedRides)) echo $riderDeniedRides; ?></h3>
                <p>
                    <?php
                    if ($this->lang->line('dash_rider_denied') != '')
                        echo stripslashes($this->lang->line('dash_rider_denied'));
                    else
                        echo 'Rider Denied';
                    ?>
                </p>
            </div>
            <div class="item_block">
                <div class="icon_block brown_block">
                    <span class="item_icon">
                        <span class="stats_icon icon arrow_branch_co"><a href="driver/rides/display_rides?act=driverCancelled"><?php
                                if ($this->lang->line('dash_you_denied') != '')
                                    echo stripslashes($this->lang->line('dash_you_denied'));
                                else
                                    echo 'You Denied';
                                ?></a></span>
                    </span>
                </div>
                <h3><?php if (isset($driverDeniedRides)) echo $driverDeniedRides; ?></h3>
                <p><?php
                    if ($this->lang->line('dash_you_denied') != '')
                        echo stripslashes($this->lang->line('dash_you_denied'));
                    else
                        echo 'You Denied';
                    ?>
                </p>
            </div>
            <div class="item_block">
                <div class="icon_block tur_block">
                    <span class="item_icon">
                        <span class="stats_icon finished_work_sl"><a href="driver/rides/display_rides?act=Completed"><?php
                                if ($this->lang->line('dash_compltedted_rides') != '')
                                    echo stripslashes($this->lang->line('dash_compltedted_rides'));
                                else
                                    echo 'Completed Rides';
                                ?></a></span>
                    </span>
                </div>
                <h3><?php if (isset($completedRides)) echo $completedRides; ?></h3>
                <p>
                    <?php
                    if ($this->lang->line('dash_compltedted_rides') != '')
                        echo stripslashes($this->lang->line('dash_compltedted_rides'));
                    else
                        echo 'Completed Rides';
                    ?>
                </p>
            </div>
            <div class="item_block">
                <div class="icon_block violet_block">
                    <span class="item_icon">
                        <span class="stats_icon badge_icon bank_sl"><a href="driver/payments/display_payments"><?php
                                if ($this->lang->line('dash_total_earnings') != '')
                                    echo stripslashes($this->lang->line('dash_total_earnings'));
                                else
                                    echo 'Total Earnings';
                                ?></a></span>
                    </span>
                </div>
                <h3><?php echo $dcurrencySymbol; ?> <?php if (isset($totalEarnings)) echo $totalEarnings; ?></h3>
                <p>
                    <?php
                    if ($this->lang->line('dash_total_earnings') != '')
                        echo stripslashes($this->lang->line('dash_total_earnings'));
                    else
                        echo 'Total Earnings';
                    ?>
                </p>
            </div>
        </div>

        <?php
        $vehicLe = '';
        if (isset($vehicleModel->type_name))
            $vehicLe = $vehicleModel->type_name . ' - ';
        if (isset($vehicleModel->name))
            $vehicLe = $vehicLe . $vehicleModel->name . ' ';
        if (isset($vehicleModel->brand_name))
            $vehicLe = $vehicLe . '(' . $vehicleModel->brand_name . ')';
        ?>

        <div class="grid_6" style="margin-bottom: 12px;">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon user"></span>
                    <h6><?php
                        if ($this->lang->line('dash_your_profile') != '')
                            echo stripslashes($this->lang->line('dash_your_profile'));
                        else
                            echo 'Your Profile';
                        ?></h6>
                </div>
                <div class="widget_content">
                    <div class="stat_block">
                        <div class="stat_chart">							

                            <div class="pie_chart profile_chart">
                                <span class="">
                                    <?php
                                    if (isset($driver_info->row()->image)) {
                                        $driver_img = $driver_info->row()->image;
                                        if ($driver_img != '') {
                                            ?>
                                            <img width="100px" src="<?php echo base_url() . USER_PROFILE_THUMB . $driver_img; ?>" />
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <img width="100px" src="<?php echo base_url() . USER_PROFILE_IMAGE_DEFAULT; ?>" />
                                    <?php } ?>
                                </span>

                            </div>
                            <div class="chart_label profile_chart_lbl">
                                <ul>
                                    <li><?php if (isset($driver_info->row()->driver_name)) echo ucfirst($driver_info->row()->driver_name); ?></li>

                                    <?php
                                    $cityName = '';
                                    if (isset($driver_info->row()->address['city']))
                                        $cityName = ' <span style="font-weight: lighter;">From </span>' . ucfirst($driver_info->row()->address['city']);
                                    ?>

                                    <li> <span style="font-weight: lighter;"><?php
                                            if ($this->lang->line('dash_driver_since') != '')
                                                echo stripslashes($this->lang->line('dash_driver_since'));
                                            else
                                                echo 'Driver Since';
                                            ?> </span><?php echo date('M Y', strtotime($driver_info->row()->created)) . $cityName ?></li>
                                    <?php
                                    $rating = 0.00;
                                    if (isset($driver_info->row()->avg_review))
                                        $rating = $driver_info->row()->avg_review;
                                    ?>
                                    <li> 
                                        <?php echo $vehicLe; ?>
                                    </li>
                                    <li> 
                                        <div class="star" id="star-pos" data-star="<?php echo $rating; ?>"></div><span>(<?php echo $rating; ?>)</span>
                                    </li>
                                </ul>
                            </div>

                            <table class="profile_tbl">
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php
                                            if ($this->lang->line('driver_status') != '')
                                                echo stripslashes($this->lang->line('driver_status'));
                                            else
                                                echo 'Status';
                                            ?>
                                        </td>
                                        <td>
																				  <?php
                                            if (isset($driver_info->row()->status)) 
																						echo get_language_value_for_keyword($driver_info->row()->status,$this->data['langCode']); 
																						?>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php
                                            if ($this->lang->line('dash_active_email') != '')
                                                echo stripslashes($this->lang->line('dash_active_email'));
                                            else
                                                echo 'Active Email';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (isset($driver_info->row()->email)) echo $driver_info->row()->email; ?>
                                        </td>
                                        <td>
                                            <a class="p_edits" href="driver/profile/change_email_form"><?php
                                                if ($this->lang->line('driver_edit') != '')
                                                    echo stripslashes($this->lang->line('driver_edit'));
                                                else
                                                    echo 'Edit';
                                                ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php
                                            if ($this->lang->line('dash_active_mobile') != '')
                                                echo stripslashes($this->lang->line('dash_active_mobile'));
                                            else
                                                echo 'Active Mobile';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (isset($driver_info->row()->mobile_number)) echo $driver_info->row()->dail_code . $driver_info->row()->mobile_number; ?>
                                        </td>
                                        <td>
                                            <a class="p_edits" href="driver/profile/change_mobile_form"><?php
                                                if ($this->lang->line('driver_edit') != '')
                                                    echo stripslashes($this->lang->line('driver_edit'));
                                                else
                                                    echo 'Edit';
                                                ?></a>
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td>
                                            <?php
                                            if ($this->lang->line('dash_last_login_date') != '')
                                                echo stripslashes($this->lang->line('dash_last_login_date'));
                                            else
                                                echo 'Last Login Date';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (isset($driver_info->row()->last_login_date)) echo date('M d Y, h:i A', strtotime($driver_info->row()->last_login_date)); ?>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid_6" style="margin-bottom: 12px;">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon graph"></span>
                    <h6><?php
                        if ($this->lang->line('dash_rides_summary') != '')
                            echo stripslashes($this->lang->line('dash_rides_summary'));
                        else
                            echo 'Rides Summary';
                        ?></h6>
                </div>
                <div class="widget_content">
                    <div class="stat_block">
                        <div class="stat_chart">							
                            <h4><?php
                                if ($this->lang->line('dash_rides_count') != '')
                                    echo stripslashes($this->lang->line('dash_rides_count'));
                                else
                                    echo 'Rides Count';
                                ?> : <?php if (isset($totalRides)) echo $totalRides; ?></h4>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php
                                            if ($this->lang->line('dash_today') != '')
                                                echo stripslashes($this->lang->line('dash_today'));
                                            else
                                                echo 'Today';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (isset($todayRides)) echo $todayRides; ?>
                                        </td>                                     
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php
                                            if ($this->lang->line('dash_this_month') != '')
                                                echo stripslashes($this->lang->line('dash_this_month'));
                                            else
                                                echo 'This Month';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (isset($monthRides)) echo $monthRides; ?>
                                        </td>                                      
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php
                                            if ($this->lang->line('dash_this_year') != '')
                                                echo stripslashes($this->lang->line('dash_this_year'));
                                            else
                                                echo 'This Year';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (isset($yearRides)) echo $yearRides; ?>
                                        </td>
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
                                    <li><span class="new_visits"></span><?php
                                        if ($this->lang->line('dash_compltedted_rides') != '')
                                            echo stripslashes($this->lang->line('dash_compltedted_rides'));
                                        else
                                            echo 'Completed Rides';
                                        ?>: <?php if (isset($completedRides)) echo $completedRides; ?></li>
                                    <li><span class="unique_visits"></span><?php
                                        if ($this->lang->line('dash_total_rides') != '')
                                            echo stripslashes($this->lang->line('dash_total_rides'));
                                        else
                                            echo 'Total Rides';
                                        ?>: <?php if (isset($totalRides)) echo $totalRides; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<style>
    .profile_chart {
        float: left;
        height: 125px !important;
        position: relative;
        width: 25% !important;
    }

    .profile_chart_lbl {
        float: left;
        padding-top: 0 !important;
        width: 75% !important;
    }
    .profile_tbl a {
        display: inline-block;
        height: 20px;
        line-height: 20px;
        padding: 0 10px;
        color: #333;
        font-size: 11px;
        color: #666;
        background: #fff url(images/icons-a.png) no-repeat;
        border: #ccc 1px solid;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        margin-right: 3px;
    }
    .post_block a, .ticket_block a {
        color: #445f7a;
    }
    .p_edits {
        background-position: 0 -2020px !important;
        padding-left: 22px !important;
    }
    a {
        text-decoration: none;
        color: #06C;
    }

</style>

<?php
$this->load->view('driver/templates/footer.php');
?>