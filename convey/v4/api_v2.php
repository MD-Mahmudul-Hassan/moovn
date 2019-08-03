<?php

/*
|-----------------------------------------------------------------------------
|  Mobile Application Routes Starts Here For Mobile Application
|-----------------------------------------------------------------------------
 */

/* For User */
$route['v4/app/apply-tips'] = 'v4/api_v2/user/apply_tips_amount';
$route['v4/app/remove-tips'] = 'v4/api_v2/user/remove_tips_amount';
$route['v4/api/v1/app/apply-tips'] = 'v4/api_v2/user/apply_tips_amount';
$route['v4/api/v1/app/remove-tips'] = 'v4/api_v2/user/remove_tips_amount';

$route['v4/app/get-fare-breakup'] = 'v4/api_v2/user/get_fare_breakup';


/*  For Driver */
$route['v4/provider/dashboard'] = 'v4/api_v2/drivers/driver_dashboard';
$route['v4/provider/change-password'] = 'v4/api_v2/drivers/change_password';
$route['v4/provider/forgot-password'] = 'v4/api_v2/drivers/forgot_password';
$route['v4/api/v1/provider/dashboard'] = 'v4/api_v2/drivers/driver_dashboard';
$route['v4/api/v1/provider/change-password'] = 'v4/api_v2/drivers/change_password';
$route['v4/api/v1/provider/forgot-password'] = 'v4/api_v2/drivers/forgot_password';



