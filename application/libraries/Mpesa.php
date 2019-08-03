<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mpesa 
{
    private $url;
    private $username;
    private $password;
    private $business_name;
    private $business_number;
    private $receiver_msisdn;
    private $currency;
    private $command_id;
    private $callback_destination;

    private $login_client;
    private $ussd_push_client;

    public function __construct()
    {
        $this->_ci =& get_instance();
        $config = $this->_ci->config->config['mpesa'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->url = $config['url'];
        $this->business_name = $config['business_name'];
        $this->business_number = $config['business_number'];
        $this->receiver_msisdn = $config['receiver_msisdn'];
        $this->command_id = $config['command_id'];
        $this->callback_destination = $config['callback_destination'];
        $this->curlopt_sslkey = $config['curlopt_sslkey'];
        $this->curlopt_cainfo = $config['curlopt_cainfo'];
        $this->curlopt_sslcert = $config['curlopt_sslcert'];
    }

    public function init_login_client() 
    {
        $returnArray['status'] = false;
        $returnArray['message'] = '';

        $context = stream_context_create([
                        'ssl' => [
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                            'local_cert'        => $this->curlopt_sslcert,
                            'local_pk'          => $this->curlopt_sslkey,
                            'cafile'            => $this->curlopt_cainfo
                        ]
                    ]);

        try {
            $this->login_client = new SoapClient($this->url, 
                array(
                    'trace' => 1, 
                    'exception' => 0,
                    'stream_context' => $context
                )
            );
            $returnArray['status'] = true;
        } catch (Exception $e) {
            $returnArray['message'] = $e->getMessage();
        }
        return $returnArray;
    }

    private function _setLoginHeaders()
    {
        $header = new SoapHeader('http://www.4cgroup.co.za/soapauth', 'EventID', 2500);
        $this->login_client->__setSoapHeaders($header);
    }

    public function login()
    {
        $returnArray['status'] = false;
        $returnArray['message'] = '';

        $this->_setLoginHeaders();
        $Request = array(
            'dataItem' => array(
                array(
                    'name' => 'Username',
                    'value' => $this->username,
                    'type' => 'String'
                ),
                array(
                    'name' => 'Password',
                    'value' => $this->password,
                    'type' => 'String'
                )
            )
        );
        try {
            $result = $this->login_client->getGenericResult($Request);
            if ($result->eventInfo->code == 4) {
                $returnArray['message'] = $result->eventInfo->detail;
            } 
            else {
                if ($result->response->dataItem->name == 'SessionId') {
                    if ($result->response->dataItem->value == 'Invalid Credentials') {
                        $returnArray['message'] = $result->response->dataItem->value;
                    } 
                    else {
                        $returnArray['status'] = true;
                        $returnArray['SessionId'] = $result->response->dataItem->value;
                    }
                } else {
                    $returnArray['message'] = 'There was some error: ' . json_encode($response);
                }
            }
        } catch (Exception $e) {
            $returnArray['message'] = $e->getMessage();
        }
        return $returnArray;
    }

    private function _init_ussd_push_client() 
    {
        $returnArray['status'] = false;
        $returnArray['message'] = '';

        $context = stream_context_create([
                        'ssl' => [
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                            'local_cert'        => $this->curlopt_sslcert,
                            'local_pk'          => $this->curlopt_sslkey,
                            'cafile'            => $this->curlopt_cainfo
                        ]
                    ]);

        try {
            $this->ussd_push_client = new SoapClient($this->url,
                array(
                    'trace' => 1, 
                    'exception' => 0,
                    'stream_context' => $context
                )
            );
            $returnArray['status'] = true;
        } catch (Exception $e) {
            $returnArray['message'] = $e->getMessage();
        }
        return $returnArray;
    }

    private function _set_ussd_push_headers($token)
    {
        $eventHeader = new SoapHeader('http://www.4cgroup.co.za/soapauth', 'EventID', 40009, false);
        $tokenHeader = new SoapHeader('http://www.4cgroup.co.za/soapauth', 'Token', $token, false);
        $this->ussd_push_client->__setSoapHeaders(array($eventHeader, $tokenHeader));
    }

    public function ussd_push_initiate($token, $request_data_items, $third_party_reference)
    {
        $returnArray['status'] = false;
        $returnArray['message'] = '';

        $this->_init_ussd_push_client();
        $this->_set_ussd_push_headers($token);

        $amount = str_replace(',', '', $request_data_items['amount']);

        $Request = array(
            'dataItem' => array(
                array(
                    'name' => 'CustomerMSISDN',
                    'value' => $request_data_items['customer_msisdn'],
                    'type' => 'String'
                ),
                array(
                    'name' => 'BusinessName',
                    'value' => $this->business_name,
                    'type' => 'String'
                ),
                array(
                    'name' => 'BusinessNumber',
                    'value' => $this->business_number,
                    'type' => 'String'
                ),
                array(
                    'name' => 'ReceiverMSISDN',
                    'value' => $this->receiver_msisdn,
                    'type' => 'String'
                ),
                array(
                    'name' => 'Currency',
                    'value' => $request_data_items['currency'],
                    'type' => 'String'
                ),
                array(
                    'name' => 'Amount',
                    'value' => $amount,
                    'type' => 'String'
                ),
                array(
                    'name' => 'ThirdPartyReference',
                    'value' => $third_party_reference,
                    'type' => 'String'
                ),
                array(
                    'name' => 'Command',
                    'value' => $this->command_id,
                    'type' => 'String'
                ),
                array(
                    'name' => 'CallBackChannel',
                    'value' => '1',
                    'type' => 'String'
                ),
                array(
                    'name' => 'CallBackDestination',
                    'value' => $this->callback_destination,
                    'type' => 'String'
                ),
                array(
                    'name' => 'Username',
                    'value' => $this->username,
                    'type' => 'String'
                ),
            )
        );
        try {
            $result = $this->ussd_push_client->getGenericResult($Request);
            if ($result->eventInfo->code == 4) {
                $returnArray['message'] = $result->eventInfo->detail;
            } 
            else {
                foreach ($result->response->dataItem as $key => $obj) {
                    if ($obj->name == 'ResponseCode') {
                        if ($obj->value == 'Duplicate') {
                            $returnArray['message'] = 'Transaction has been sent within 10minutes, with matching values for parameters: CustomerMSISDN, BusinessNumber, Amount and Command';
                        } else if ($obj->value == '0') {
                            $returnArray['status'] = true;
                            $returnArray['message'] = 'Received successfully and will process the request.';
                        } else if ($obj->value == '-1') {
                            $returnArray['message'] = 'Transaction won’t be processed. System maintanance / busy. Please try again later.';
                        }
                    } else if ($obj->name == 'ThirdPartyReference' && isset($obj->value)) {
                        $returnArray['third_party_reference'] = $obj->value;
                    } else if ($obj->name == 'InsightReference' && isset($obj->value)) {
                        $returnArray['insight_reference'] = $obj->value;
                    }
                }
            }
        } catch (Exception $e) {
            $returnArray['message'] = $e->getMessage();
        }
        return $returnArray;
    }

    private function _set_transaction_status_headers($token)
    {
        $eventHeader = new SoapHeader('http://www.4cgroup.co.za/soapauth', 'EventID', 50011, false);
        $tokenHeader = new SoapHeader('http://www.4cgroup.co.za/soapauth', 'Token', $token, false);
        $this->transaction_status_client->__setSoapHeaders(array($eventHeader, $tokenHeader));
    }

    private function _init_transaction_status_client() 
    {
        $returnArray['status'] = false;
        $returnArray['message'] = '';

        $context = stream_context_create([
                        'ssl' => [
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                            'local_cert'        => $this->curlopt_sslcert,
                            'local_pk'          => $this->curlopt_sslkey,
                            'cafile'            => $this->curlopt_cainfo
                        ]
                    ]);
        try {
            $this->transaction_status_client = new SoapClient($this->url,
                array(
                    'trace' => 1, 
                    'exception' => 0,
                    'stream_context' => $context
                )
            );
            $returnArray['status'] = true;
        } catch (Exception $e) {
            $returnArray['message'] = $e->getMessage();
        }
        return $returnArray;
    }

    public function transaction_status($token, $insight_reference, $phone_number, $third_party_reference)
    {
        $returnArray['status'] = '0';
        $returnArray['message'] = '';

        $newClient = $this->_init_transaction_status_client();
        $this->_set_transaction_status_headers($token);

        $Request = array(
            'dataItem' => array(
                array(
                    'name' => 'ThirdPartyReference',
                    'value' => $third_party_reference,
                    'type' => 'String'
                ),
                array(
                    'name' => 'InsightReference',
                    'value' => $insight_reference,
                    'type' => 'String'
                ),
                array(
                    'name' => 'CustomerMSISDN',
                    'value' => $phone_number,
                    'type' => 'String'
                ),
                array(
                    'name' => 'Command',
                    'value' => 'TransactionStatusQuery',
                    'type' => 'String'
                ),
                array(
                    'name' => 'ServiceProviderCode',
                    'value' => $this->business_number,
                    'type' => 'String'
                )
            )
        );
        try {
            $result = $this->transaction_status_client->getGenericResult($Request);
            if ($result->eventInfo->code == 4) {
                $returnArray['message'] = $result->eventInfo->detail;
            } 
            else {
                $response_data = $result->response->dataItem;
                if ($response_data->name == 'TransactionStatus') {
                    if (isset($response_data->value)) {
                        $returnArray['message'] = $response_data->value;
                    } else {
                        $returnArray['message'] = 'Error';
                    }
                } else {
                    $returnArray['message'] = 'Error';
                }
            }
        } catch (Exception $e) {
            $returnArray['message'] = $e->getMessage();
        }
        return $returnArray;
    }
}