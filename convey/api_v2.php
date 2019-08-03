<?php

/*
|-----------------------------------------------------------------------------
|  Mobile Application Routes Starts Here For Mobile Application
|-----------------------------------------------------------------------------
 */

/* For User */
$route['app/apply-tips'] = 'api_v2/user/apply_tips_amount';
$route['app/remove-tips'] = 'api_v2/user/remove_tips_amount';
$route['api/v1/app/apply-tips'] = 'api_v2/user/apply_tips_amount';
$route['api/v1/app/remove-tips'] = 'api_v2/user/remove_tips_amount';

$route['app/get-fare-breakup'] = 'api_v2/user/get_fare_breakup';


/*  For Driver */
$route['provider/dashboard'] = 'api_v2/drivers/driver_dashboard';
$route['provider/change-password'] = 'api_v2/drivers/change_password';
$route['provider/forgot-password'] = 'api_v2/drivers/forgot_password';
$route['api/v1/provider/dashboard'] = 'api_v2/drivers/driver_dashboard';
$route['api/v1/provider/change-password'] = 'api_v2/drivers/change_password';
$route['api/v1/provider/forgot-password'] = 'api_v2/drivers/forgot_password';



