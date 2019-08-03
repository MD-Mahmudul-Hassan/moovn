<?php
$route['api/v3/app/favourite-driver/add'] ='api_v3/user/add_favourite_driver';
$route['api/v3/app/favourite-driver/edit'] ='api_v3/user/edit_favourite_driver';
$route['api/v3/app/favourite-driver/remove'] ='api_v3/user/remove_favourite_driver';
$route['api/v3/app/favourite-driver/list'] ='api_v3/user/display_favourite_driver';

$route['api/v3/social-login'] = 'api_v3/user/social_login';

$route['api/xmpp-status'] ='api_v3/common/update_receive_mode';


/********** Tracking Ride's Routes  **********/
$route['api/v3/track-driver'] = 'api_v3/user/track_driver_location';
$route['api/v3/track-driver/share-my-ride'] = 'api_v3/user/share_track_driver_location';


$route['api/v3/get-app-info'] = 'api_v3/common/get_app_info';


$route['api/v3/check-trip-status'] = 'api_v3/drivers/check_trip_payment_status';


/* Stripe cards */
$route['v3/app/stripe-api-keys'] = 'api_v3/stripe_process/get_stripe_api_key';
$route['v3/app/stripe-delete-card'] = 'api_v3/stripe_process/stripe_delete_card';
$route['v3/app/stripe-wallet-recharge'] = 'api_v3/stripe_process/stripe_wallet_payment_process';
$route['v3/app/stripe-fees-payment'] = 'api_v3/stripe_process/stripe_fees_payment_process';


/* Driver Registration Routes */
$route['v3/app/get-location-list'] = 'api_v3/drivers/get_location_list';
$route['v3/app/get-category-list'] = 'api_v3/drivers/get_category_list';
$route['v3/app/get-country-list'] = 'api_v3/drivers/get_country_list';
$route['v3/app/get-vehicle-list'] = 'api_v3/drivers/get_vehicle_list';
$route['v3/app/get-maker-list'] = 'api_v3/drivers/get_maker_list';
$route['v3/app/get-model-list'] = 'api_v3/drivers/get_model_list';
$route['v3/app/get-year-list'] = 'api_v3/drivers/get_year_list';
$route['v3/app/send-otp-driver'] = 'api_v3/drivers/send_otp_driver';
$route['v3/app/save-image'] = 'api_v3/drivers/upload_image';
$route['v3/app/register-driver'] = 'api_v3/drivers/register';


?>