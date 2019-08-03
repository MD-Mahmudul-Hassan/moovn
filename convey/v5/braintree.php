<?php
$route['v5/api/payment/get-settings'] = 'v5/payment/braintree_api/get_braintree_settings';
$route['v5/api/payment/check-account'] = 'v5/payment/braintree_api/getcustomer';
$route['v5/api/payment/connect-account'] = 'v5/payment/braintree_api/drop_in_process_add';

$route['v5/api/payment/trip'] = 'v5/payment/braintree_api/make_trip_payment_rest';
$route['v5/api/payment/wallet'] ='v5/payment/braintree_api/make_wallet_payment_rest';

$route['v5/api/payment/get-cc-info'] ='v5/payment/braintree_api/getcustomerpaymentmethods';
$route['v5/braintree/wallet-payment-form'] ='v5/payment/braintree_api/drop_in_form';
$route['v5/braintree/wallet-payment'] ='v5/payment/braintree_api/drop_in_process_wallet';
$route['v5/braintree/account-form'] ='v5/payment/braintree_api/drop_in_view';

$route['v5/api/payment/delete-payment-method'] = 'v5/payment/braintree_api/deletePaymentMethod';
$route['v5/api/payment/update-payment-method'] = 'v5/payment/braintree_api/update_payment_method';
$route['v5/api/payment/set-default-payment-method'] = 'v5/payment/braintree_api/setDefaultPaymentMethod';
$route['v5/api/payment/get-card-details'] = 'v5/payment/braintree_api/getCardDetails';