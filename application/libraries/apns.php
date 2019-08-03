<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apns
{
	public function send_push_message($deviceId,$messages,$app){
		date_default_timezone_set('Asia/Kolkata');
		$this->_ci =& get_instance();
		error_reporting(0);
		require_once(APPPATH.'/third_party/ApnsPHP/Autoload.php');
		
		$mode='LIVE';  //  (LIVE / SANDBOX)
		
		if($mode=='LIVE'){
			if($app=='DRIVER'){
				$push = new ApnsPHP_Push(ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,'certificates/'.$this->_ci->config->item('ios_driver_prod'));
			}else if($app=='USER'){
				$push = new ApnsPHP_Push(ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,'certificates/'.$this->_ci->config->item('ios_user_prod'));
			}
		}else if($mode=='SANDBOX'){
			if($app=='DRIVER'){
				$push = new ApnsPHP_Push(ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,'certificates/'.$this->_ci->config->item('ios_driver_dev'));
			}else if($app=='USER'){
				$push = new ApnsPHP_Push(ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,'certificates/'.$this->_ci->config->item('ios_user_dev'));
			}
		}
		
				
		$push->connect();
		
		#$message = new ApnsPHP_Message($deviceId);
		
		$message = new ApnsPHP_Message();
		//Put all tokens as recipients in one message
		foreach ($deviceId as $token) {
			$message->addRecipient($token);
		}

		$message->setCustomIdentifier("Message-Badge-1");

		$message->setBadge(0);

		$message->setText($messages['message']);

		$message->setSound();

		$message->setCustomProperty('acme2', array('bang', 'whiz'));

		$message->setCustomProperty('acme3', array('bing', 'bong'));
		
		$message->setCustomProperty('message', $messages);

		if($messages['action']=='ride_request'){
			$message->setExpiry(intval($this->_ci->config->item('respond_timeout')));
		}else{
			$message->setExpiry(30);
		}

		$push->add($message);

		$push->send();

		$push->disconnect();
		return $push->getErrors();
	}
}