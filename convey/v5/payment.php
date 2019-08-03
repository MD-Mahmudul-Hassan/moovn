<?php

/*
|----------------------------------------------------------------------------
|  Mobile Application Routes Starts Here For Apple Application -- Later updates consider as a version 2
|----------------------------------------------------------------------------
*/
/* User Application payment Gateway integration */
/* IOS  */ 
$route['v5/mobile/proceed-payment'] = 'v5/mobile/user/proceed_payment';
$route['v5/mobile/userPaymentCard'] = "v5/mobile/mobile_payment/userPaymentCard";
$route['v5/mobile/payment-form'] = "v5/mobile/mobile_payment/credit_card_form";
$route['v5/mobile/payment-paypal'] = "v5/mobile/mobile_payment/paypal_payment_process";
$route['v5/mobile/stripe-manual-payment-form'] = "v5/mobile/mobile_payment/stripe_payment_form";
$route['v5/mobile/stripe-manual-payment-process'] = "v5/mobile/mobile_payment/stripe_payment_process";
$route['v5/paypal-payment-ipn'] = "admin/paypal_payment/ipnpayment";
$route['v5/mobile/success/(:any)'] = "v5/mobile/mobile_payment/pay_success";
$route['v5/mobile/failed/(:any)'] = "v5/mobile/mobile_payment/pay_failed";
$route['v5/mobile/payment/(:any)'] = "v5/mobile/mobile_payment/payment_return";

$route['v5/mobile/wallet-recharge/settings'] = "v5/mobile/mobile_wallet_recharge/wallet_money_settings";
$route['v5/mobile/wallet-recharge/payform'] = "v5/mobile/mobile_wallet_recharge/add_pay_wallet_payment_form";

$route['v5/mobile/wallet-recharge/stripe-process'] = "v5/mobile/mobile_wallet_recharge/stripe_payment_process";

$route['v5/wallet-recharge/success/(:any)'] = "v5/mobile/mobile_wallet_recharge/pay_success";
$route['v5/wallet-recharge/failed/(:any)'] = "v5/mobile/mobile_wallet_recharge/pay_failed";
$route['v5/wallet-recharge/pay-cancel'] = "v5/mobile/mobile_wallet_recharge/payment_return";
$route['v5/wallet-recharge/pay-completed'] = "v5/mobile/mobile_wallet_recharge/payment_return";



/* ANDROID  */ 
$route['v5/api/v1/mobile/proceed-payment'] ='v5/mobile/user/proceed_payment';
$route['v5/api/v1/mobile/userPaymentCard'] = "v5/mobile/mobile_payment/userPaymentCard";
$route['v5/api/v1/mobile/payment-form'] = "v5/mobile/mobile_payment/credit_card_form";
$route['v5/api/v1/mobile/payment-paypal'] = "v5/mobile/mobile_payment/paypal_payment_process";
$route['v5/api/v1/mobile/stripe-manual-payment-form'] = "v5/mobile/mobile_payment/stripe_payment_form";
$route['v5/api/v1/mobile/stripe-manual-payment-process'] = "v5/mobile/mobile_payment/stripe_payment_process";


$route['v5/api/v1/mobile/success/(:any)'] = "v5/mobile/mobile_payment/pay_success";
$route['v5/api/v1/mobile/failed/(:any)'] = "v5/mobile/mobile_payment/pay_failed";
$route['v5/api/v1/mobile/payment/(:any)'] = "v5/mobile/mobile_payment/payment_return";

$route['v5/api/v1/mobile/wallet-recharge/settings'] = "v5/mobile/mobile_wallet_recharge/wallet_money_settings";
$route['v5/api/v1/mobile/wallet-recharge/payform'] = "v5/mobile/mobile_wallet_recharge/add_pay_wallet_payment_form";

$route['v5/api/v1/mobile/wallet-recharge/stripe-process'] = "v5/mobile/mobile_wallet_recharge/stripe_payment_process";

$route['v5/api/v1/wallet-recharge/success/(:any)'] = "v5/mobile/mobile_wallet_recharge/pay_success";
$route['v5/api/v1/wallet-recharge/failed/(:any)'] = "v5/mobile/mobile_wallet_recharge/pay_failed";
$route['v5/api/v1/wallet-recharge/pay-cancel'] = "v5/mobile/mobile_wallet_recharge/payment_return";
$route['v5/api/v1/wallet-recharge/pay-completed'] = "v5/mobile/mobile_wallet_recharge/payment_return";

//  mpesa
$route['v5/mpesa/pay'] = "v5/mobile/Mpesa_api/pay";
$route['v5/mpesa/user-recharge-wallet'] = "v5/mobile/Mpesa_api/user_recharge_wallet";
$route['v5/mpesa/driver-remit'] = "v5/mobile/Mpesa_api/driver_remit";
$route['v5/mpesa/callback'] = "v5/mobile/Mpesa_api/callback";
$route['v5/mpesa/get-transaction-status'] = "v5/mobile/Mpesa_api/get_transaction_status";
