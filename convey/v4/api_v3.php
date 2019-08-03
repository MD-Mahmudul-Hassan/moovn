<?php
$route['v4/api/v3/app/favourite-driver/add'] ='v4/api_v3/user/add_favourite_driver';
$route['v4/api/v3/app/favourite-driver/edit'] ='v4/api_v3/user/edit_favourite_driver';
$route['v4/api/v3/app/favourite-driver/remove'] ='v4/api_v3/user/remove_favourite_driver';
$route['v4/api/v3/app/favourite-driver/list'] ='v4/api_v3/user/display_favourite_driver';

$route['v4/api/v3/social-login'] = 'v4/api_v3/user/social_login';

$route['v4/api/xmpp-status'] ='v4/api_v3/common/update_receive_mode';


/********** Tracking Ride's Routes  **********/
$route['v4/api/v3/track-driver'] = 'v4/api_v3/user/track_driver_location';
$route['v4/api/v3/track-driver/share-my-ride'] = 'v4/api_v3/user/share_track_driver_location';


$route['v4/api/v3/get-app-info'] = 'v4/api_v3/common/get_app_info';


$route['v4/api/v3/check-trip-status'] = 'v4/api_v3/drivers/check_trip_payment_status';


/* Stripe cards */
$route['v4/v3/app/stripe-api-keys'] = 'v4/api_v3/stripe_process/get_stripe_api_key';
$route['v4/v3/app/stripe-delete-card'] = 'v4/api_v3/stripe_process/stripe_delete_card';
$route['v4/v3/app/stripe-wallet-recharge'] = 'v4/api_v3/stripe_process/stripe_wallet_payment_process';
$route['v4/v3/app/stripe-fees-payment'] = 'v4/api_v3/stripe_process/stripe_fees_payment_process';


/* Driver Registration Routes */
$route['v4/v3/app/get-location-list'] = 'v4/api_v3/drivers/get_location_list';
$route['v4/v3/app/get-category-list'] = 'v4/api_v3/drivers/get_category_list';
$route['v4/v3/app/get-country-list'] = 'v4/api_v3/drivers/get_country_list';
$route['v4/v3/app/get-vehicle-list'] = 'v4/api_v3/drivers/get_vehicle_list';
$route['v4/v3/app/get-maker-list'] = 'v4/api_v3/drivers/get_maker_list';
$route['v4/v3/app/get-model-list'] = 'v4/api_v3/drivers/get_model_list';
$route['v4/v3/app/get-year-list'] = 'v4/api_v3/drivers/get_year_list';
$route['v4/v3/app/send-otp-driver'] = 'v4/api_v3/drivers/send_otp_driver';
$route['v4/v3/app/save-image'] = 'v4/api_v3/drivers/upload_image';
$route['v4/v3/app/register-driver'] = 'v4/api_v3/drivers/register';


?>