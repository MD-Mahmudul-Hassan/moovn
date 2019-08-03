<?php

/*
|-----------------------------------------------------------------------------
|  Mobile Application Routes Starts Here For Mobile Application
|-----------------------------------------------------------------------------
 */

/* For User */
$route['v5/app/apply-tips'] = 'v5/api_v2/user/apply_tips_amount';
$route['v5/app/remove-tips'] = 'v5/api_v2/user/remove_tips_amount';
$route['v5/api/v1/app/apply-tips'] = 'v5/api_v2/user/apply_tips_amount';
$route['v5/api/v1/app/remove-tips'] = 'v5/api_v2/user/remove_tips_amount';

$route['v5/app/get-fare-breakup'] = 'v5/api_v2/user/get_fare_breakup';


/*  For Driver */
$route['v5/provider/dashboard'] = 'v5/api_v2/drivers/driver_dashboard';
$route['v5/provider/change-password'] = 'v5/api_v2/drivers/change_password';
$route['v5/provider/forgot-password'] = 'v5/api_v2/drivers/forgot_password';
$route['v5/api/v1/provider/dashboard'] = 'v5/api_v2/drivers/driver_dashboard';
$route['v5/api/v1/provider/change-password'] = 'v5/api_v2/drivers/change_password';
$route['v5/api/v1/provider/forgot-password'] = 'v5/api_v2/drivers/forgot_password';



