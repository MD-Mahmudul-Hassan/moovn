<?php

/*
  |-------------------------------------------------------------------------------
  |  Mobile Application Routes Starts Here For Android Application
  |-------------------------------------------------------------------------------
 */
/* For User */
$route['v5/api/v1/app/check-user'] ='v5/android/user/check_account';
$route['v5/api/v1/app/social-check'] ='v5/android/user/check_social_login';
$route['v5/api/v1/app/register'] ='v5/android/user/register_user';
$route['v5/api/v1/app/login'] ='v5/android/user/login_user';
$route['v5/api/v1/app/logout'] ='v5/android/user/logout_user';
$route['v5/api/v1/app/forgot-password'] ='v5/android/user/findAccount';
$route['v5/api/v1/app/social-login'] ='v5/android/user/social_Login';
$route['v5/api/v1/app/set-user-geo'] ='v5/android/user/update_user_location';
$route['v5/api/v1/app/get-map-view'] ='v5/android/user/get_drivers_in_map';
$route['v5/api/v1/app/get-eta'] ='v5/android/user/get_eta';
$route['v5/api/v1/app/apply-coupon'] ='v5/android/user/apply_coupon_code';
$route['v5/api/v1/app/book-ride'] ='v5/android/user/booking_ride';
$route['v5/api/v1/app/delete-ride'] ='v5/android/user/delete_ride';
$route['v5/api/v1/app/cancellation-reason'] ='v5/android/user/user_cancelling_reason';
$route['v5/api/v1/app/cancel-ride'] ='v5/android/user/cancelling_ride';
$route['v5/api/v1/app/get-location'] ='v5/android/user/get_location_list';
$route['v5/api/v1/app/get-category'] ='v5/android/user/get_category_list';
$route['v5/api/v1/app/get-ratecard'] ='v5/android/user/get_rate_card';


$route['v5/api/v1/app/my-rides'] ='v5/android/user/all_ride_list';
$route['v5/api/v1/app/view-ride'] ='v5/android/user/view_ride_information';
$route['v5/api/v1/app/get-invites'] ='v5/android/user/get_invites';
$route['v5/api/v1/app/get-earnings'] ='v5/android/user/get_earnings_list';
$route['v5/api/v1/app/get-money-page'] ='v5/android/user/get_money_page';
$route['v5/api/v1/app/get-trans-list'] ='v5/android/user/get_transaction_list';
$route['v5/api/v1/app/payment-list'] ='v5/android/user/get_payment_list';

$route['v5/api/v1/app/payment/by-cash'] ='v5/android/user/payment_by_cash';
$route['v5/api/v1/app/payment/by-wallet'] ='v5/android/user/payment_by_wallet';
$route['v5/api/v1/app/payment/by-gateway'] ='v5/android/user/payment_by_gateway';
$route['v5/api/v1/app/payment/by-auto-detect'] ='v5/android/user/payment_by_auto_charge';

$route['v5/api/v1/app/favourite/add'] ='v5/android/user_profile/add_favourite_location';
$route['v5/api/v1/app/favourite/edit'] ='v5/android/user_profile/edit_favourite_location';
$route['v5/api/v1/app/favourite/remove'] ='v5/android/user_profile/remove_favourite_location';
$route['v5/api/v1/app/favourite/display'] ='v5/android/user_profile/display_favourite_location';
$route['v5/api/v1/app/user/change-name'] ='v5/android/user_profile/change_user_name';
$route['v5/api/v1/app/user/change-mobile'] ='v5/android/user_profile/change_user_mobile_number';
$route['v5/api/v1/app/user/change-password'] ='v5/android/user_profile/change_user_password';
$route['v5/api/v1/app/user/reset-password'] ='v5/android/user_profile/user_reset_password';
$route['v5/api/v1/app/user/update-reset-password'] ='v5/android/user_profile/update_reset_password';

$route['v5/api/v1/app/user/set-emergency-contact'] ='v5/android/user_profile/emergency_contact_add_edit';
$route['v5/api/v1/app/user/view-emergency-contact'] ='v5/android/user_profile/emergency_contact_view';
$route['v5/api/v1/app/user/delete-emergency-contact'] ='v5/android/user_profile/emergency_contact_delete';
$route['v5/api/v1/app/user/alert-emergency-contact'] ='v5/android/user_profile/emergency_contact_alert';


$route['v5/api/v1/app/review/options-list'] ='v5/android/reviews/get_review_options';
$route['v5/api/v1/app/review/submit'] ='v5/android/reviews/submit_reviews';

$route['v5/api/v1/app/mail-invoice'] ='v5/android/user/mail_invoice';

$route['v5/api/v1/app/favourite-driver/add'] ='v5/android/user_action/add_favourite_driver';
$route['v5/api/v1/app/favourite-driver/edit'] ='v5/android/user_action/edit_favourite_driver';
$route['v5/api/v1/app/favourite-driver/remove'] ='v5/android/user_action/remove_favourite_driver';
$route['v5/api/v1/app/favourite-driver/list'] ='v5/android/user_action/display_favourite_driver';

/* For Driver */
$route['v5/api/v1/provider/login'] ='v5/android/drivers/login_driver';
$route['v5/api/v1/provider/logout'] ='v5/android/drivers/logout_driver';
$route['v5/api/v1/provider/update-availability'] ='v5/android/drivers/update_driver_availablity';
$route['v5/api/v1/provider/update-driver-geo'] ='v5/android/drivers/update_driver_location';
$route['v5/api/v1/provider/update-driver-mode'] ='v5/android/drivers/update_driver_mode';
$route['v5/api/v1/provider/accept-ride'] ='v5/android/drivers/accept_ride';
$route['v5/api/v1/provider/cancellation-reason'] ='v5/android/drivers/driver_cancelling_reason';
$route['v5/api/v1/provider/cancel-ride'] ='v5/android/drivers/cancelling_ride';
$route['v5/api/v1/provider/arrived'] ='v5/android/drivers/location_arrived';
$route['v5/api/v1/provider/begin-ride'] ='v5/android/drivers/begin_ride';
$route['v5/api/v1/provider/end-ride'] ='v5/android/drivers/end_ride';

$route['v5/api/v1/provider/get-rider-info'] ='v5/android/drivers/get_rider_information';

$route['v5/api/v1/provider/get-banking-info'] ='v5/android/drivers/get_banking_details';
$route['v5/api/v1/provider/save-banking-info'] ='v5/android/drivers/save_banking_details';

$route['v5/api/v1/provider/my-trips/list'] ='v5/android/drivers/driver_all_ride_list';
$route['v5/api/v1/provider/my-trips/view'] ='v5/android/drivers/view_driver_ride_information';


$route['v5/api/v1/provider/continue-trip'] ='v5/android/drivers/continue_trip';

$route['v5/api/v1/provider/payment-list'] ='v5/android/drivers/driver_all_payment_list';
$route['v5/api/v1/provider/payment-summary'] ='v5/android/drivers/view_driver_payment_information';

$route['v5/api/v1/provider/get-payment-list'] ='v5/android/drivers/get_payment_list';
$route['v5/api/v1/provider/request-payment'] ='v5/android/drivers/requesting_payment';
$route['v5/api/v1/provider/receive-payment'] ='v5/android/drivers/receive_payment_confirmation';

$route['v5/api/v1/provider/payment-received'] ='v5/android/drivers/payment_received';
$route['v5/api/v1/provider/payment-completed'] ='v5/android/drivers/trip_completed';

/* Driver Registration on Mobile Application */
$route['v5/api/v1/provider/register/get-location-list'] ='v5/android/drivers_signup/get_location_list';
$route['v5/api/v1/provider/register/get-category-list'] ='v5/android/drivers_signup/get_category_list';
$route['v5/api/v1/provider/register/get-country-list'] ='v5/android/drivers_signup/get_country_list';
$route['v5/api/v1/provider/register/get-location-with-category'] ='v5/android/drivers_signup/get_location_with_category_list';


