<?php

/*
  |----------------------------------------------------------------------------
  |  Mobile Application Routes Starts Here For Apple Application
  |----------------------------------------------------------------------------
 */
/* For User */
$route['v5/app/check-user'] = 'v5/mobile/user/check_account';
$route['v5/app/social-check'] = 'v5/mobile/user/check_social_login';
$route['v5/app/register'] = 'v5/mobile/user/register_user';
$route['v5/app/login'] = 'v5/mobile/user/login_user';
$route['v5/app/logout'] = 'v5/mobile/user/logout_user';
$route['v5/app/make-mpesa-payment'] = 'v5/mobile/user/make_mpesa_payment';
$route['v5/app/forgot-password'] = 'v5/mobile/user/findAccount';
$route['v5/app/social-login'] = 'v5/mobile/user/social_Login';
$route['v5/app/set-user-geo'] = 'v5/mobile/user/update_user_location';
$route['v5/app/get-map-view'] = 'v5/mobile/user/get_drivers_in_map';
$route['v5/app/get-eta'] = 'v5/mobile/user/get_eta';
$route['v5/app/apply-coupon'] = 'v5/mobile/user/apply_coupon_code';
$route['v5/app/book-ride'] = 'v5/mobile/user/booking_ride';
$route['v5/app/delete-ride'] = 'v5/mobile/user/delete_ride';
$route['v5/app/cancellation-reason'] = 'v5/mobile/user/user_cancelling_reason';
$route['v5/app/cancel-ride'] = 'v5/mobile/user/cancelling_ride';
$route['v5/app/get-location'] = 'v5/mobile/user/get_location_list';
$route['v5/app/get-category'] = 'v5/mobile/user/get_category_list';
$route['v5/app/get-ratecard'] = 'v5/mobile/user/get_rate_card';


$route['v5/app/my-rides'] = 'v5/mobile/user/all_ride_list';
$route['v5/app/view-ride'] = 'v5/mobile/user/view_ride_information';
$route['v5/app/get-invites'] = 'v5/mobile/user/get_invites';
$route['v5/app/get-earnings'] = 'v5/mobile/user/get_earnings_list';
$route['v5/app/get-money-page'] = 'v5/mobile/user/get_money_page';
$route['v5/app/get-trans-list'] = 'v5/mobile/user/get_transaction_list';
$route['v5/app/payment-list'] = 'v5/mobile/user/get_payment_list';

$route['v5/app/payment/by-cash'] = 'v5/mobile/user/payment_by_cash';
$route['v5/app/payment/by-wallet'] = 'v5/mobile/user/payment_by_wallet';
$route['v5/app/payment/by-gateway'] = 'v5/mobile/user/payment_by_gateway';
$route['v5/app/payment/by-auto-detect'] = 'v5/mobile/user/payment_by_auto_charge';

$route['v5/app/favourite/add'] = 'v5/mobile/user_profile/add_favourite_location';
$route['v5/app/favourite/edit'] = 'v5/mobile/user_profile/edit_favourite_location';
$route['v5/app/favourite/remove'] = 'v5/mobile/user_profile/remove_favourite_location';
$route['v5/app/favourite/display'] = 'v5/mobile/user_profile/display_favourite_location';
$route['v5/app/user/change-name'] = 'v5/mobile/user_profile/change_user_name';
$route['v5/app/user/change-mobile'] = 'v5/mobile/user_profile/change_user_mobile_number';
$route['v5/app/user/change-password'] = 'v5/mobile/user_profile/change_user_password';
$route['v5/app/user/reset-password'] = 'v5/mobile/user_profile/user_reset_password';
$route['v5/app/user/update-reset-password'] = 'v5/mobile/user_profile/update_reset_password';

$route['v5/app/user/set-emergency-contact'] = 'v5/mobile/user_profile/emergency_contact_add_edit';
$route['v5/app/user/view-emergency-contact'] = 'v5/mobile/user_profile/emergency_contact_view';
$route['v5/app/user/delete-emergency-contact'] = 'v5/mobile/user_profile/emergency_contact_delete';
$route['v5/app/user/alert-emergency-contact'] = 'v5/mobile/user_profile/emergency_contact_alert';


$route['v5/app/review/options-list'] = 'v5/mobile/reviews/get_review_options';
$route['v5/app/review/submit'] = 'v5/mobile/reviews/submit_reviews';

$route['v5/app/mail-invoice'] = 'v5/mobile/user/mail_invoice';


/* For Driver */
$route['v5/provider/login'] = 'v5/mobile/drivers/login_driver';
$route['v5/provider/logout'] = 'v5/mobile/drivers/logout_driver';
$route['v5/provider/update-availability'] = 'v5/mobile/drivers/update_driver_availablity';
$route['v5/provider/update-driver-geo'] = 'v5/mobile/drivers/update_driver_location';
$route['v5/provider/update-driver-mode'] = 'v5/mobile/drivers/update_driver_mode';
$route['v5/provider/accept-ride'] = 'v5/mobile/drivers/accept_ride';
$route['v5/provider/accept-reserved-ride'] = 'v5/mobile/drivers/accept_reserved_ride';
$route['v5/provider/start-upcoming-ride'] = 'v5/mobile/drivers/start_upcoming_ride';
$route['v5/provider/cancellation-reason'] = 'v5/mobile/drivers/driver_cancelling_reason';
$route['v5/provider/cancel-ride'] = 'v5/mobile/drivers/cancelling_ride';
$route['v5/provider/arrived'] = 'v5/mobile/drivers/location_arrived';
$route['v5/provider/begin-ride'] = 'v5/mobile/drivers/begin_ride';
$route['v5/provider/end-ride'] = 'v5/mobile/drivers/end_ride';

$route['v5/provider/get-rider-info'] = 'v5/mobile/drivers/get_rider_information';

$route['v5/provider/get-banking-info'] = 'v5/mobile/drivers/get_banking_details';
$route['v5/provider/save-banking-info'] = 'v5/mobile/drivers/save_banking_details';

$route['v5/provider/my-trips/list'] = 'v5/mobile/drivers/driver_all_ride_list';
$route['v5/provider/my-trips/view'] = 'v5/mobile/drivers/view_driver_ride_information';


$route['v5/provider/continue-trip'] = 'v5/mobile/drivers/continue_trip';

$route['v5/provider/payment-list'] = 'v5/mobile/drivers/driver_all_payment_list';
$route['v5/provider/payment-summary'] = 'v5/mobile/drivers/view_driver_payment_information';

$route['v5/provider/get-payment-list'] = 'v5/mobile/drivers/get_payment_list';
$route['v5/provider/request-payment'] = 'v5/mobile/drivers/requesting_payment';
$route['v5/provider/receive-payment'] = 'v5/mobile/drivers/receive_payment_confirmation';

$route['v5/provider/payment-received'] = 'v5/mobile/drivers/payment_received';
$route['v5/provider/payment-completed'] = 'v5/mobile/drivers/trip_completed';

/* Driver Registration on Mobile Application */
$route['v5/provider/register/get-location-list'] = 'v5/mobile/drivers_signup/get_location_list';
$route['v5/provider/register/get-category-list'] = 'v5/mobile/drivers_signup/get_category_list';
$route['v5/provider/register/get-country-list'] = 'v5/mobile/drivers_signup/get_country_list';
$route['v5/provider/register/get-location-with-category'] = 'v5/mobile/drivers_signup/get_location_with_category_list';


