<?php

/*
  |-------------------------------------------------------------------------------
  |  Mobile Application Routes Starts Here For Android Application
  |-------------------------------------------------------------------------------
 */
/* For User */
$route['api/v1/app/check-user'] ='android/user/check_account';
$route['api/v1/app/social-check'] ='android/user/check_social_login';
$route['api/v1/app/register'] ='android/user/register_user';
$route['api/v1/app/login'] ='android/user/login_user';
$route['api/v1/app/logout'] ='android/user/logout_user';
$route['api/v1/app/forgot-password'] ='android/user/findAccount';
$route['api/v1/app/social-login'] ='android/user/social_Login';
$route['api/v1/app/set-user-geo'] ='android/user/update_user_location';
$route['api/v1/app/get-map-view'] ='android/user/get_drivers_in_map';
$route['api/v1/app/get-eta'] ='android/user/get_eta';
$route['api/v1/app/apply-coupon'] ='android/user/apply_coupon_code';
$route['api/v1/app/book-ride'] ='android/user/booking_ride';
$route['api/v1/app/delete-ride'] ='android/user/delete_ride';
$route['api/v1/app/cancellation-reason'] ='android/user/user_cancelling_reason';
$route['api/v1/app/cancel-ride'] ='android/user/cancelling_ride';
$route['api/v1/app/get-location'] ='android/user/get_location_list';
$route['api/v1/app/get-category'] ='android/user/get_category_list';
$route['api/v1/app/get-ratecard'] ='android/user/get_rate_card';


$route['api/v1/app/my-rides'] ='android/user/all_ride_list';
$route['api/v1/app/view-ride'] ='android/user/view_ride_information';
$route['api/v1/app/get-invites'] ='android/user/get_invites';
$route['api/v1/app/get-earnings'] ='android/user/get_earnings_list';
$route['api/v1/app/get-money-page'] ='android/user/get_money_page';
$route['api/v1/app/get-trans-list'] ='android/user/get_transaction_list';
$route['api/v1/app/payment-list'] ='android/user/get_payment_list';

$route['api/v1/app/payment/by-cash'] ='android/user/payment_by_cash';
$route['api/v1/app/payment/by-wallet'] ='android/user/payment_by_wallet';
$route['api/v1/app/payment/by-gateway'] ='android/user/payment_by_gateway';
$route['api/v1/app/payment/by-auto-detect'] ='android/user/payment_by_auto_charge';

$route['api/v1/app/favourite/add'] ='android/user_profile/add_favourite_location';
$route['api/v1/app/favourite/edit'] ='android/user_profile/edit_favourite_location';
$route['api/v1/app/favourite/remove'] ='android/user_profile/remove_favourite_location';
$route['api/v1/app/favourite/display'] ='android/user_profile/display_favourite_location';
$route['api/v1/app/user/change-name'] ='android/user_profile/change_user_name';
$route['api/v1/app/user/change-mobile'] ='android/user_profile/change_user_mobile_number';
$route['api/v1/app/user/change-password'] ='android/user_profile/change_user_password';
$route['api/v1/app/user/reset-password'] ='android/user_profile/user_reset_password';
$route['api/v1/app/user/update-reset-password'] ='android/user_profile/update_reset_password';

$route['api/v1/app/user/set-emergency-contact'] ='android/user_profile/emergency_contact_add_edit';
$route['api/v1/app/user/view-emergency-contact'] ='android/user_profile/emergency_contact_view';
$route['api/v1/app/user/delete-emergency-contact'] ='android/user_profile/emergency_contact_delete';
$route['api/v1/app/user/alert-emergency-contact'] ='android/user_profile/emergency_contact_alert';


$route['api/v1/app/review/options-list'] ='android/reviews/get_review_options';
$route['api/v1/app/review/submit'] ='android/reviews/submit_reviews';

$route['api/v1/app/mail-invoice'] ='android/user/mail_invoice';

$route['api/v1/app/favourite-driver/add'] ='android/user_action/add_favourite_driver';
$route['api/v1/app/favourite-driver/edit'] ='android/user_action/edit_favourite_driver';
$route['api/v1/app/favourite-driver/remove'] ='android/user_action/remove_favourite_driver';
$route['api/v1/app/favourite-driver/list'] ='android/user_action/display_favourite_driver';

/* For Driver */
$route['api/v1/provider/login'] ='android/drivers/login_driver';
$route['api/v1/provider/logout'] ='android/drivers/logout_driver';
$route['api/v1/provider/update-availability'] ='android/drivers/update_driver_availablity';
$route['api/v1/provider/update-driver-geo'] ='android/drivers/update_driver_location';
$route['api/v1/provider/update-driver-mode'] ='android/drivers/update_driver_mode';
$route['api/v1/provider/accept-ride'] ='android/drivers/accept_ride';
$route['api/v1/provider/cancellation-reason'] ='android/drivers/driver_cancelling_reason';
$route['api/v1/provider/cancel-ride'] ='android/drivers/cancelling_ride';
$route['api/v1/provider/arrived'] ='android/drivers/location_arrived';
$route['api/v1/provider/begin-ride'] ='android/drivers/begin_ride';
$route['api/v1/provider/end-ride'] ='android/drivers/end_ride';

$route['api/v1/provider/get-rider-info'] ='android/drivers/get_rider_information';

$route['api/v1/provider/get-banking-info'] ='android/drivers/get_banking_details';
$route['api/v1/provider/save-banking-info'] ='android/drivers/save_banking_details';

$route['api/v1/provider/my-trips/list'] ='android/drivers/driver_all_ride_list';
$route['api/v1/provider/my-trips/view'] ='android/drivers/view_driver_ride_information';


$route['api/v1/provider/continue-trip'] ='android/drivers/continue_trip';

$route['api/v1/provider/payment-list'] ='android/drivers/driver_all_payment_list';
$route['api/v1/provider/payment-summary'] ='android/drivers/view_driver_payment_information';

$route['api/v1/provider/get-payment-list'] ='android/drivers/get_payment_list';
$route['api/v1/provider/request-payment'] ='android/drivers/requesting_payment';
$route['api/v1/provider/receive-payment'] ='android/drivers/receive_payment_confirmation';

$route['api/v1/provider/payment-received'] ='android/drivers/payment_received';
$route['api/v1/provider/payment-completed'] ='android/drivers/trip_completed';

/* Driver Registration on Mobile Application */
$route['api/v1/provider/register/get-location-list'] ='android/drivers_signup/get_location_list';
$route['api/v1/provider/register/get-category-list'] ='android/drivers_signup/get_category_list';
$route['api/v1/provider/register/get-country-list'] ='android/drivers_signup/get_country_list';
$route['api/v1/provider/register/get-location-with-category'] ='android/drivers_signup/get_location_with_category_list';


/* User Application payment Gateway integration */
$route['api/v1/mobile/proceed-payment'] ='mobile/user/proceed_payment';
$route['api/v1/mobile/userPaymentCard'] = "mobile/mobile_payment/userPaymentCard";
$route['api/v1/mobile/payment-form'] = "mobile/mobile_payment/credit_card_form";
$route['api/v1/mobile/payment-paypal'] = "mobile/mobile_payment/paypal_payment_process";
$route['api/v1/mobile/stripe-manual-payment-form'] = "mobile/mobile_payment/stripe_payment_form";
$route['api/v1/mobile/stripe-manual-payment-process'] = "mobile/mobile_payment/stripe_payment_process";


$route['api/v1/mobile/success/(:any)'] = "mobile/mobile_payment/pay_success";
$route['api/v1/mobile/failed/(:any)'] = "mobile/mobile_payment/pay_failed";
$route['api/v1/mobile/payment/(:any)'] = "mobile/mobile_payment/payment_return";

$route['api/v1/mobile/wallet-recharge/settings'] = "mobile/mobile_wallet_recharge/wallet_money_settings";
$route['api/v1/mobile/wallet-recharge/payform'] = "mobile/mobile_wallet_recharge/add_pay_wallet_payment_form";

$route['api/v1/mobile/wallet-recharge/stripe-process'] = "mobile/mobile_wallet_recharge/stripe_payment_process";

$route['api/v1/wallet-recharge/success/(:any)'] = "mobile/mobile_wallet_recharge/pay_success";
$route['api/v1/wallet-recharge/failed/(:any)'] = "mobile/mobile_wallet_recharge/pay_failed";
$route['api/v1/wallet-recharge/pay-cancel'] = "mobile/mobile_wallet_recharge/payment_return";
$route['api/v1/wallet-recharge/pay-completed'] = "mobile/mobile_wallet_recharge/payment_return";