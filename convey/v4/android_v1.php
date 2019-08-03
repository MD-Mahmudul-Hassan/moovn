<?php

/*
  |-------------------------------------------------------------------------------
  |  Mobile Application Routes Starts Here For Android Application
  |-------------------------------------------------------------------------------
 */
/* For User */
$route['v4/api/v1/app/check-user'] ='v4/android/user/check_account';
$route['v4/api/v1/app/social-check'] ='v4/android/user/check_social_login';
$route['v4/api/v1/app/register'] ='v4/android/user/register_user';
$route['v4/api/v1/app/login'] ='v4/android/user/login_user';
$route['v4/api/v1/app/logout'] ='v4/android/user/logout_user';
$route['v4/api/v1/app/forgot-password'] ='v4/android/user/findAccount';
$route['v4/api/v1/app/social-login'] ='v4/android/user/social_Login';
$route['v4/api/v1/app/set-user-geo'] ='v4/android/user/update_user_location';
$route['v4/api/v1/app/get-map-view'] ='v4/android/user/get_drivers_in_map';
$route['v4/api/v1/app/get-eta'] ='v4/android/user/get_eta';
$route['v4/api/v1/app/apply-coupon'] ='v4/android/user/apply_coupon_code';
$route['v4/api/v1/app/book-ride'] ='v4/android/user/booking_ride';
$route['v4/api/v1/app/delete-ride'] ='v4/android/user/delete_ride';
$route['v4/api/v1/app/cancellation-reason'] ='v4/android/user/user_cancelling_reason';
$route['v4/api/v1/app/cancel-ride'] ='v4/android/user/cancelling_ride';
$route['v4/api/v1/app/get-location'] ='v4/android/user/get_location_list';
$route['v4/api/v1/app/get-category'] ='v4/android/user/get_category_list';
$route['v4/api/v1/app/get-ratecard'] ='v4/android/user/get_rate_card';


$route['v4/api/v1/app/my-rides'] ='v4/android/user/all_ride_list';
$route['v4/api/v1/app/view-ride'] ='v4/android/user/view_ride_information';
$route['v4/api/v1/app/get-invites'] ='v4/android/user/get_invites';
$route['v4/api/v1/app/get-earnings'] ='v4/android/user/get_earnings_list';
$route['v4/api/v1/app/get-money-page'] ='v4/android/user/get_money_page';
$route['v4/api/v1/app/get-trans-list'] ='v4/android/user/get_transaction_list';
$route['v4/api/v1/app/payment-list'] ='v4/android/user/get_payment_list';

$route['v4/api/v1/app/payment/by-cash'] ='v4/android/user/payment_by_cash';
$route['v4/api/v1/app/payment/by-wallet'] ='v4/android/user/payment_by_wallet';
$route['v4/api/v1/app/payment/by-gateway'] ='v4/android/user/payment_by_gateway';
$route['v4/api/v1/app/payment/by-auto-detect'] ='v4/android/user/payment_by_auto_charge';

$route['v4/api/v1/app/favourite/add'] ='v4/android/user_profile/add_favourite_location';
$route['v4/api/v1/app/favourite/edit'] ='v4/android/user_profile/edit_favourite_location';
$route['v4/api/v1/app/favourite/remove'] ='v4/android/user_profile/remove_favourite_location';
$route['v4/api/v1/app/favourite/display'] ='v4/android/user_profile/display_favourite_location';
$route['v4/api/v1/app/user/change-name'] ='v4/android/user_profile/change_user_name';
$route['v4/api/v1/app/user/change-mobile'] ='v4/android/user_profile/change_user_mobile_number';
$route['v4/api/v1/app/user/change-password'] ='v4/android/user_profile/change_user_password';
$route['v4/api/v1/app/user/reset-password'] ='v4/android/user_profile/user_reset_password';
$route['v4/api/v1/app/user/update-reset-password'] ='v4/android/user_profile/update_reset_password';

$route['v4/api/v1/app/user/set-emergency-contact'] ='v4/android/user_profile/emergency_contact_add_edit';
$route['v4/api/v1/app/user/view-emergency-contact'] ='v4/android/user_profile/emergency_contact_view';
$route['v4/api/v1/app/user/delete-emergency-contact'] ='v4/android/user_profile/emergency_contact_delete';
$route['v4/api/v1/app/user/alert-emergency-contact'] ='v4/android/user_profile/emergency_contact_alert';


$route['v4/api/v1/app/review/options-list'] ='v4/android/reviews/get_review_options';
$route['v4/api/v1/app/review/submit'] ='v4/android/reviews/submit_reviews';

$route['v4/api/v1/app/mail-invoice'] ='v4/android/user/mail_invoice';

$route['v4/api/v1/app/favourite-driver/add'] ='v4/android/user_action/add_favourite_driver';
$route['v4/api/v1/app/favourite-driver/edit'] ='v4/android/user_action/edit_favourite_driver';
$route['v4/api/v1/app/favourite-driver/remove'] ='v4/android/user_action/remove_favourite_driver';
$route['v4/api/v1/app/favourite-driver/list'] ='v4/android/user_action/display_favourite_driver';

/* For Driver */
$route['v4/api/v1/provider/login'] ='v4/android/drivers/login_driver';
$route['v4/api/v1/provider/logout'] ='v4/android/drivers/logout_driver';
$route['v4/api/v1/provider/update-availability'] ='v4/android/drivers/update_driver_availablity';
$route['v4/api/v1/provider/update-driver-geo'] ='v4/android/drivers/update_driver_location';
$route['v4/api/v1/provider/update-driver-mode'] ='v4/android/drivers/update_driver_mode';
$route['v4/api/v1/provider/accept-ride'] ='v4/android/drivers/accept_ride';
$route['v4/api/v1/provider/cancellation-reason'] ='v4/android/drivers/driver_cancelling_reason';
$route['v4/api/v1/provider/cancel-ride'] ='v4/android/drivers/cancelling_ride';
$route['v4/api/v1/provider/arrived'] ='v4/android/drivers/location_arrived';
$route['v4/api/v1/provider/begin-ride'] ='v4/android/drivers/begin_ride';
$route['v4/api/v1/provider/end-ride'] ='v4/android/drivers/end_ride';

$route['v4/api/v1/provider/get-rider-info'] ='v4/android/drivers/get_rider_information';

$route['v4/api/v1/provider/get-banking-info'] ='v4/android/drivers/get_banking_details';
$route['v4/api/v1/provider/save-banking-info'] ='v4/android/drivers/save_banking_details';

$route['v4/api/v1/provider/my-trips/list'] ='v4/android/drivers/driver_all_ride_list';
$route['v4/api/v1/provider/my-trips/view'] ='v4/android/drivers/view_driver_ride_information';


$route['v4/api/v1/provider/continue-trip'] ='v4/android/drivers/continue_trip';

$route['v4/api/v1/provider/payment-list'] ='v4/android/drivers/driver_all_payment_list';
$route['v4/api/v1/provider/payment-summary'] ='v4/android/drivers/view_driver_payment_information';

$route['v4/api/v1/provider/get-payment-list'] ='v4/android/drivers/get_payment_list';
$route['v4/api/v1/provider/request-payment'] ='v4/android/drivers/requesting_payment';
$route['v4/api/v1/provider/receive-payment'] ='v4/android/drivers/receive_payment_confirmation';

$route['v4/api/v1/provider/payment-received'] ='v4/android/drivers/payment_received';
$route['v4/api/v1/provider/payment-completed'] ='v4/android/drivers/trip_completed';

/* Driver Registration on Mobile Application */
$route['v4/api/v1/provider/register/get-location-list'] ='v4/android/drivers_signup/get_location_list';
$route['v4/api/v1/provider/register/get-category-list'] ='v4/android/drivers_signup/get_category_list';
$route['v4/api/v1/provider/register/get-country-list'] ='v4/android/drivers_signup/get_country_list';
$route['v4/api/v1/provider/register/get-location-with-category'] ='v4/android/drivers_signup/get_location_with_category_list';


/* User Application payment Gateway integration */
$route['v4/api/v1/mobile/proceed-payment'] ='v4/mobile/user/proceed_payment';
$route['v4/api/v1/mobile/userPaymentCard'] = "v4/mobile/mobile_payment/userPaymentCard";
$route['v4/api/v1/mobile/payment-form'] = "v4/mobile/mobile_payment/credit_card_form";
$route['v4/api/v1/mobile/payment-paypal'] = "v4/mobile/mobile_payment/paypal_payment_process";
$route['v4/api/v1/mobile/stripe-manual-payment-form'] = "v4/mobile/mobile_payment/stripe_payment_form";
$route['v4/api/v1/mobile/stripe-manual-payment-process'] = "v4/mobile/mobile_payment/stripe_payment_process";


$route['v4/api/v1/mobile/success/(:any)'] = "v4/mobile/mobile_payment/pay_success";
$route['v4/api/v1/mobile/failed/(:any)'] = "v4/mobile/mobile_payment/pay_failed";
$route['v4/api/v1/mobile/payment/(:any)'] = "v4/mobile/mobile_payment/payment_return";

$route['v4/api/v1/mobile/wallet-recharge/settings'] = "v4/mobile/mobile_wallet_recharge/wallet_money_settings";
$route['v4/api/v1/mobile/wallet-recharge/payform'] = "v4/mobile/mobile_wallet_recharge/add_pay_wallet_payment_form";

$route['v4/api/v1/mobile/wallet-recharge/stripe-process'] = "v4/mobile/mobile_wallet_recharge/stripe_payment_process";

$route['v4/api/v1/wallet-recharge/success/(:any)'] = "v4/mobile/mobile_wallet_recharge/pay_success";
$route['v4/api/v1/wallet-recharge/failed/(:any)'] = "v4/mobile/mobile_wallet_recharge/pay_failed";
$route['v4/api/v1/wallet-recharge/pay-cancel'] = "v4/mobile/mobile_wallet_recharge/payment_return";
$route['v4/api/v1/wallet-recharge/pay-completed'] = "v4/mobile/mobile_wallet_recharge/payment_return";