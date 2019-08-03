<?php

/*
  |----------------------------------------------------------------------------
  |  Mobile Application Routes Starts Here For Apple Application
  |----------------------------------------------------------------------------
 */
/* For User */
$route['app/check-user'] = 'mobile/user/check_account';
$route['app/social-check'] = 'mobile/user/check_social_login';
$route['app/register'] = 'mobile/user/register_user';
$route['app/login'] = 'mobile/user/login_user';
$route['app/logout'] = 'mobile/user/logout_user';
$route['app/forgot-password'] = 'mobile/user/findAccount';
$route['app/social-login'] = 'mobile/user/social_Login';
$route['app/set-user-geo'] = 'mobile/user/update_user_location';
$route['app/get-map-view'] = 'mobile/user/get_drivers_in_map';
$route['app/get-eta'] = 'mobile/user/get_eta';
$route['app/apply-coupon'] = 'mobile/user/apply_coupon_code';
$route['app/book-ride'] = 'mobile/user/booking_ride';
$route['app/delete-ride'] = 'mobile/user/delete_ride';
$route['app/cancellation-reason'] = 'mobile/user/user_cancelling_reason';
$route['app/cancel-ride'] = 'mobile/user/cancelling_ride';
$route['app/get-location'] = 'mobile/user/get_location_list';
$route['app/get-category'] = 'mobile/user/get_category_list';
$route['app/get-ratecard'] = 'mobile/user/get_rate_card';


$route['app/my-rides'] = 'mobile/user/all_ride_list';
$route['app/view-ride'] = 'mobile/user/view_ride_information';
$route['app/get-invites'] = 'mobile/user/get_invites';
$route['app/get-earnings'] = 'mobile/user/get_earnings_list';
$route['app/get-money-page'] = 'mobile/user/get_money_page';
$route['app/get-trans-list'] = 'mobile/user/get_transaction_list';
$route['app/payment-list'] = 'mobile/user/get_payment_list';

$route['app/payment/by-cash'] = 'mobile/user/payment_by_cash';
$route['app/payment/by-wallet'] = 'mobile/user/payment_by_wallet';
$route['app/payment/by-gateway'] = 'mobile/user/payment_by_gateway';
$route['app/payment/by-auto-detect'] = 'mobile/user/payment_by_auto_charge';

$route['app/favourite/add'] = 'mobile/user_profile/add_favourite_location';
$route['app/favourite/edit'] = 'mobile/user_profile/edit_favourite_location';
$route['app/favourite/remove'] = 'mobile/user_profile/remove_favourite_location';
$route['app/favourite/display'] = 'mobile/user_profile/display_favourite_location';
$route['app/user/change-name'] = 'mobile/user_profile/change_user_name';
$route['app/user/change-mobile'] = 'mobile/user_profile/change_user_mobile_number';
$route['app/user/change-password'] = 'mobile/user_profile/change_user_password';
$route['app/user/reset-password'] = 'mobile/user_profile/user_reset_password';
$route['app/user/update-reset-password'] = 'mobile/user_profile/update_reset_password';

$route['app/user/set-emergency-contact'] = 'mobile/user_profile/emergency_contact_add_edit';
$route['app/user/view-emergency-contact'] = 'mobile/user_profile/emergency_contact_view';
$route['app/user/delete-emergency-contact'] = 'mobile/user_profile/emergency_contact_delete';
$route['app/user/alert-emergency-contact'] = 'mobile/user_profile/emergency_contact_alert';


$route['app/review/options-list'] = 'mobile/reviews/get_review_options';
$route['app/review/submit'] = 'mobile/reviews/submit_reviews';

$route['app/mail-invoice'] = 'mobile/user/mail_invoice';


/* For Driver */
$route['provider/login'] = 'mobile/drivers/login_driver';
$route['provider/logout'] = 'mobile/drivers/logout_driver';
$route['provider/update-availability'] = 'mobile/drivers/update_driver_availablity';
$route['provider/update-driver-geo'] = 'mobile/drivers/update_driver_location';
$route['provider/update-driver-mode'] = 'mobile/drivers/update_driver_mode';
$route['provider/accept-ride'] = 'mobile/drivers/accept_ride';
$route['provider/cancellation-reason'] = 'mobile/drivers/driver_cancelling_reason';
$route['provider/cancel-ride'] = 'mobile/drivers/cancelling_ride';
$route['provider/arrived'] = 'mobile/drivers/location_arrived';
$route['provider/begin-ride'] = 'mobile/drivers/begin_ride';
$route['provider/end-ride'] = 'mobile/drivers/end_ride';

$route['provider/get-rider-info'] = 'mobile/drivers/get_rider_information';

$route['provider/get-banking-info'] = 'mobile/drivers/get_banking_details';
$route['provider/save-banking-info'] = 'mobile/drivers/save_banking_details';

$route['provider/my-trips/list'] = 'mobile/drivers/driver_all_ride_list';
$route['provider/my-trips/view'] = 'mobile/drivers/view_driver_ride_information';


$route['provider/continue-trip'] = 'mobile/drivers/continue_trip';

$route['provider/payment-list'] = 'mobile/drivers/driver_all_payment_list';
$route['provider/payment-summary'] = 'mobile/drivers/view_driver_payment_information';

$route['provider/get-payment-list'] = 'mobile/drivers/get_payment_list';
$route['provider/request-payment'] = 'mobile/drivers/requesting_payment';
$route['provider/receive-payment'] = 'mobile/drivers/receive_payment_confirmation';

$route['provider/payment-received'] = 'mobile/drivers/payment_received';
$route['provider/payment-completed'] = 'mobile/drivers/trip_completed';

/* Driver Registration on Mobile Application */
$route['provider/register/get-location-list'] = 'mobile/drivers_signup/get_location_list';
$route['provider/register/get-category-list'] = 'mobile/drivers_signup/get_category_list';
$route['provider/register/get-country-list'] = 'mobile/drivers_signup/get_country_list';
$route['provider/register/get-location-with-category'] = 'mobile/drivers_signup/get_location_with_category_list';


/* User Application payment Gateway integration */
$route['mobile/proceed-payment'] = 'mobile/user/proceed_payment';
$route['mobile/userPaymentCard'] = "mobile/mobile_payment/userPaymentCard";
$route['mobile/payment-form'] = "mobile/mobile_payment/credit_card_form";
$route['mobile/payment-paypal'] = "mobile/mobile_payment/paypal_payment_process";
$route['mobile/stripe-manual-payment-form'] = "mobile/mobile_payment/stripe_payment_form";
$route['mobile/stripe-manual-payment-process'] = "mobile/mobile_payment/stripe_payment_process";
$route['paypal-payment-ipn'] = "admin/paypal_payment/ipnpayment";
$route['mobile/success/(:any)'] = "mobile/mobile_payment/pay_success";
$route['mobile/failed/(:any)'] = "mobile/mobile_payment/pay_failed";
$route['mobile/payment/(:any)'] = "mobile/mobile_payment/payment_return";

$route['mobile/wallet-recharge/settings'] = "mobile/mobile_wallet_recharge/wallet_money_settings";
$route['mobile/wallet-recharge/payform'] = "mobile/mobile_wallet_recharge/add_pay_wallet_payment_form";

$route['mobile/wallet-recharge/stripe-process'] = "mobile/mobile_wallet_recharge/stripe_payment_process";

$route['wallet-recharge/success/(:any)'] = "mobile/mobile_wallet_recharge/pay_success";
$route['wallet-recharge/failed/(:any)'] = "mobile/mobile_wallet_recharge/pay_failed";
$route['wallet-recharge/pay-cancel'] = "mobile/mobile_wallet_recharge/payment_return";
$route['wallet-recharge/pay-completed'] = "mobile/mobile_wallet_recharge/payment_return";