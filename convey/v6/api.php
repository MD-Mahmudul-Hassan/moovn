<?php
/* For Rider */
$route['v6/user/check-user']             = 'v6/user/create_account';
$route['v6/user/social-check']           = 'v6/user/check_social_login';
$route['v6/user/register']               = 'v6/user/register';
$route['v6/user/login']                  = 'v6/user/login';
$route['v6/user/logout']                 = 'v6/user/logout';
$route['v6/user/social-login']           = 'v6/user/social_Login';
$route['v6/user/set-user-geo']           = 'v6/user/update_user_location';
$route['v6/user/get-map-view']           = 'v6/user/get_drivers_in_map';
$route['v6/user/get-eta']                = 'v6/user/get_eta';
$route['v6/user/apply-coupon']           = 'v6/user/apply_coupon_code';
$route['v6/user/view']                   = 'v6/user/view';

$route['v6/user/book-ride']              = 'v6/user/booking_ride';
$route['v6/user/delete-ride']            = 'v6/user/delete_ride';
$route['v6/user/cancellation-reason']    = 'v6/user/user_cancelling_reason';
$route['v6/user/cancel-ride']            = 'v6/user/cancelling_ride';
$route['v6/user/get-location']           = 'v6/user/get_location_list';
$route['v6/user/get-category']           = 'v6/user/get_category_list';
$route['v6/user/get-ratecard']           = 'v6/user/get_rate_card';

$route['v6/user/my-rides']               = 'v6/user/all_ride_list';
$route['v6/user/view-ride']              = 'v6/user/view_ride_information';
$route['v6/user/get-money-page']         = 'v6/user/get_money_page';
$route['v6/user/get-trans-list']         = 'v6/user/get_transaction_list';
$route['v6/user/payment-list']           = 'v6/user/get_payment_list';

$route['v6/user/change-email']           = 'v6/user/change_email';

$route['v6/user/payment/by-cash']        = 'v6/user/payment_by_cash';
$route['v6/user/payment/by-wallet']      = 'v6/user/payment_by_wallet';
$route['v6/user/payment/by-gateway']     = 'v6/user/payment_by_gateway';
$route['v6/user/payment/by-auto-detect'] = 'v6/user/payment_by_auto_charge';

$route['v6/user/favourite/add']              = 'v6/user_profile/add_favourite_location';
$route['v6/user/favourite/edit']             = 'v6/user_profile/edit_favourite_location';
$route['v6/user/favourite/remove']           = 'v6/user_profile/remove_favourite_location';
$route['v6/user/favourite/display']          = 'v6/user_profile/display_favourite_location';
$route['v6/user/change-name']           = 'v6/user_profile/change_user_name';
$route['v6/user/change-mobile']         = 'v6/user_profile/change_user_mobile_number';
$route['v6/user/change-password']       = 'v6/user_profile/change_user_password';
$route['v6/user/reset-password']        = 'v6/user_profile/user_reset_password';
$route['v6/user/update-reset-password'] = 'v6/user_profile/update_reset_password';

$route['v6/user/set-emergency-contact']     = 'v6/user_profile/emergency_contact_add_edit';
$route['v6/user/view-emergency-contact']    = 'v6/user_profile/emergency_contact_view';
$route['v6/user/delete-emergency-contact']  = 'v6/user_profile/emergency_contact_delete';
$route['v6/user/alert-emergency-contact']   = 'v6/user_profile/emergency_contact_alert';

$route['v6/user/mail-invoice']              = 'v6/user/mail_invoice';

$route['v6/user/get-invites']               = 'v6/user/get_invites';
$route['v6/wallet-recharge/stripe-process'] = "v6/mobile_wallet_recharge/stripe_payment_process";

$route['v6/user/track-driver']                   =  'v6/user/track_driver_location';
$route['v6/user/share-my-ride']                  =  'v6/user/share_track_driver_location';

$route['v6/user/apply-tips']                     =  'v6/user/apply_tips_amount';
$route['v6/user/remove-tips']                    =  'v6/user/remove_tips_amount';
$route['v6/user/get-fare-breakup']               =  'v6/user/get_fare_breakup';
$route['v6/user/update-drop-address']            =  'v6/user/update_drop_address';
 
$route['v6/user-upload-image']                   =  'v6/common/upload_user_profile_image';

$route['v6/payment/check-account']                  =   'v6/braintree_api/getcustomer';
$route['v6/payment/get-settings']                   =   'v6/braintree_api/get_braintree_settings';
$route['v6/payment/check-account']                  =   'v6/braintree_api/getcustomer';
$route['v6/payment/connect-account']                =   'v6/braintree_api/drop_in_process_add';
$route['v6/payment/trip']                           =   'v6/braintree_api/make_trip_payment_rest';
$route['v6/payment/wallet']                         =   'v6/braintree_api/make_wallet_payment_rest';
$route['v6/payment/get-cc-info']                    =   'v6/braintree_api/getcustomerpaymentmethods';
$route['v6/braintree/wallet-payment-form']          =   'v6/braintree_api/drop_in_form';
$route['v6/braintree/wallet-payment']               =   'v6/braintree_api/drop_in_process_wallet';
$route['v6/braintree/account-form']                 =   'v6/braintree_api/drop_in_view';
$route['v6/payment/delete-payment-method']          =   'v6/braintree_api/deletePaymentMethod';
$route['v6/payment/update-payment-method']          =   'v6/braintree_api/update_payment_method';
$route['v6/payment/set-default-payment-method']     =   'v6/braintree_api/setDefaultPaymentMethod';
$route['v6/payment/get-card-details']               =   'v6/braintree_api/getCardDetails';

/* For Driver */
$route['v6/driver/login']                   = 'v6/driver/login';
$route['v6/driver/logout']                  = 'v6/driver/logout';
$route['v6/driver/forgot-password']         = 'v6/driver/forgot_password';
$route['v6/driver/change-password']         = 'v6/driver/change_password';
$route['v6/driver/dashboard']               = 'v6/driver/driver_dashboard';
$route['v6/driver/update-availability']     = 'v6/driver/update_availablity';
$route['v6/driver/update-driver-geo']       = 'v6/driver/update_location';
$route['v6/driver/update-driver-mode']      = 'v6/driver/update_mode';
$route['v6/driver/accept-ride']             = 'v6/driver/accept_ride';
$route['v6/driver/accept-reserved-ride']    = 'v6/driver/accept_reserved_ride';
$route['v6/driver/start-upcoming-ride']     = 'v6/driver/start_upcoming_ride';
$route['v6/driver/cancellation-reason']     = 'v6/driver/cancelling_reason';
$route['v6/driver/cancel-ride']             = 'v6/driver/cancel_ride';
$route['v6/driver/arrived']                 = 'v6/driver/arrived';
$route['v6/driver/begin-ride']              = 'v6/driver/begin_ride';
$route['v6/driver/end-ride']                = 'v6/driver/end_ride';
$route['v6/driver/get-rider-info']          = 'v6/driver/get_rider_information';
$route['v6/driver/get-banking-info']        = 'v6/driver/get_banking_details';
$route['v6/driver/save-banking-info']       = 'v6/driver/save_banking_details';
$route['v6/driver/my-trips/list']           = 'v6/driver/get_rides';
$route['v6/driver/my-trips/view']           = 'v6/driver/view_ride';
$route['v6/driver/continue-trip']           = 'v6/driver/continue_trip';
$route['v6/driver/payment-list']            = 'v6/driver/get_all_payment_list';
$route['v6/driver/payment-summary']         = 'v6/driver/get_payment_information';
$route['v6/driver/get-payment-list']        = 'v6/driver/get_payment_list';
$route['v6/driver/request-payment']         = 'v6/driver/request_payment';
$route['v6/driver/receive-payment']         = 'v6/driver/receive_payment_confirmation';
$route['v6/driver/payment-received']        = 'v6/driver/payment_received';
$route['v6/driver/payment-completed']       = 'v6/driver/trip_completed';
$route['v6/driver/check-trip-status']       = 'v6/driver/check_trip_payment_status';
$route['v6/driver/get-location-list']       = 'v6/driver/get_location_list';
$route['v6/driver/get-category-list']       = 'v6/driver/get_category_list';
$route['v6/driver/get-country-list']        = 'v6/driver/get_country_list';
$route['v6/driver/get-vehicle-list']        = 'v6/driver/get_vehicle_list';
$route['v6/driver/get-maker-list']          = 'v6/driver/get_maker_list';
$route['v6/driver/get-model-list']          = 'v6/driver/get_model_list';
$route['v6/driver/get-year-list']           = 'v6/driver/get_year_list';

// not used in app
$route['v6/driver/send-otp-driver']         = 'v6/driver/send_otp_driver';

$route['v6/driver/save-image']              = 'v6/driver/upload_image';

// not used in app
$route['v6/driver/register-driver']         = 'v6/driver/register';

$route['v6/driver/view']                    = 'v6/driver/view';
$route['v6/driver/get-driver-status']       = 'v6/driver/get_driver_status';

/* Driver Registration on Mobile Application */
$route['v6/driver/signup']                              = "site/app_driver/register_form";
$route['v6/driver/signup/success']                      = "site/app_driver/success";

$route['v6/driver/register/get-location-list']          = 'v6/drivers_signup/get_location_list';
$route['v6/driver/register/get-category-list']          = 'v6/drivers_signup/get_category_list';
$route['v6/driver/register/get-country-list']           = 'v6/drivers_signup/get_country_list';
$route['v6/driver/register/get-location-with-category'] = 'v6/drivers_signup/get_location_with_category_list';

$route['v6/review/options-list']            = 'v6/reviews/get_review_options';
$route['v6/review/submit']                  = 'v6/reviews/submit_reviews';

$route['v6/xmpp-status']                    =   'v6/common/update_receive_mode';
$route['v6/update-primary-language']        =   "v6/common/update_primary_language";
$route['v6/update-ride-location']           =   "v6/common/driver_update_ride_location";
$route['v6/get-app-info']                   =   'v6/common/get_app_info';

//  mpesa
$route['v6/mpesa/pay']                      = "v6/Mpesa_api/pay";
$route['v6/mpesa/user-recharge-wallet']     = "v6/Mpesa_api/user_recharge_wallet";
$route['v6/mpesa/driver-remit']             = "v6/Mpesa_api/driver_remit";
$route['v6/mpesa/callback']                 = "v6/Mpesa_api/callback";
$route['v6/mpesa/get-transaction-status']   = "v6/Mpesa_api/get_transaction_status";
