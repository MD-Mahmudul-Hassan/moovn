<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Currencyget {

        protected static $_iso_currency = '^(AED|AFN|ALL|AMD|ANG|AOA|ARS|AUD|AWG|AZN|BAM|BBD|BDT|BGN|BHD|BIF|BMD|BND|BOB|BOV|BRL|BSD|BTN|BWP|BYR|BZD|CAD|CDF|CHE|CHF|CHW|CLF|CLP|CNY|COP|COU|CRC|CUC|CUP|CVE|CZK|DJF|DKK|DOP|DZD|EGP|ERN|ETB|EUR|FJD|FKP|GBP|GEL|GHS|GIP|GMD|GNF|GTQ|GYD|HKD|HNL|HRK|HTG|HUF|IDR|ILS|INR|IQD|IRR|ISK|JMD|JOD|JPY|KES|KGS|KHR|KMF|KPW|KRW|KWD|KYD|KZT|LAK|LBP|LKR|LRD|LSL|LTL|LVL|LYD|MAD|MDL|MGA|MKD|MMK|MNT|MOP|MRO|MUR|MVR|MWK|MXN|MXV|MYR|MZN|NAD|NGN|NIO|NOK|NPR|NZD|OMR|PAB|PEN|PGK|PHP|PKR|PLN|PYG|QAR|RON|RSD|RUB|RWF|SAR|SBD|SCR|SDG|SEK|SGD|SHP|SLL|SOS|SRD|SSP|STD|SVC|SYP|SZL|THB|TJS|TMT|TND|TOP|TRY|TTD|TWD|TZS|UAH|UGX|USD|USN|USS|UYI|UYU|UZS|VEF|VND|VUV|WST|XAF|XAG|XAU|XBA|XBB|XBC|XBD|XCD|XDR|XFU|XOF|XPD|XPF|XPT|XSU|XTS|XUA|XXX|YER|ZAR|ZMW|ZWL)$';
        
        private static function fixer_api($from, $to){
           $conversion = @file_get_contents('https://api.fixer.io/latest?base=' . $from . '&symbols=' . $to);
           $conversion=json_decode($conversion);
           if(isset($conversion->rates)){
                return ($conversion->rates === false) ? false : $conversion->rates->{$to};
           }else{
                return false;
           }
        }

        private function _free_currency_converter($from, $to)
        {
            $response = @file_get_contents('http://free.currencyconverterapi.com/api/v5/convert?q=' . $from . '_' . $to . '&compact=y');
            $conversion = (array) json_decode($response);
            return ($conversion[$from . '_' . $to]->val === false) ? false : $conversion[$from . '_' . $to]->val;
        }
        
        public function currency_conversion($value=1, $from, $to){

            //Validates ISO currency codes
            if (!preg_match('/'.self::$_iso_currency.'/i', strtoupper(trim($from)))) return 'Currency "FROM", is not a valid ISO Code.';
            if (!preg_match('/'.self::$_iso_currency.'/i', strtoupper(trim($to)))) return 'Currency "TO", is not a valid ISO Code.';

            //Runs the Yahoo exchange by default
            $exchange = self::_free_currency_converter($from, $to);

            if ( !$exchange ){
                return 'There has been a mistake, the servers do not respond.';
            }else{
                //Return the conversion multiplied by the value
                return ($exchange*$value);
            }
        }
    }


/* End of file currency.php */
/* Location: ./application/libraries/currency.php */