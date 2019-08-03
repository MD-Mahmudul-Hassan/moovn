<?php

//echo $_REQUEST['code']; die;
include('commonsettings/dectar_admin_settings.php');
########## Google Settings.. Client ID, Client Secret #############
$google_client_id = '';
if(isset($config['google_client_id'])){
	$google_client_id 		= $config['google_client_id'];
} 

$google_client_secret = '';
if(isset($config['google_client_secret'])){
	$google_client_secret 	= $config['google_client_secret'];
}

$google_redirect_url 	= $config['base_url'].'google-redirect';

$google_developer_key = '';
if(isset($config['google_developer_key'])){
	$google_developer_key 	= $config['google_developer_key'];
}




//include google api files
require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_Oauth2Service.php';

//start session
@session_start();
//unset($_SESSION['token']);
//echo "<pre>";print_r($_SESSION);die;

$gClient = new Google_Client();
$gClient->setApplicationName('Google');
$gClient->setClientId($google_client_id);
$gClient->setClientSecret($google_client_secret);
$gClient->setRedirectUri($google_redirect_url);
$gClient->setDeveloperKey($google_developer_key);

$google_oauthV2 = new Google_Oauth2Service($gClient);

//If user wish to log out, we just unset Session variable
if (isset($_REQUEST['reset'])) 
{
  unset($_SESSION['token']);
  $gClient->revokeToken(); 
}

//Redirect user to google authentication page for code, if code is empty.
//Code is required to aquire Access Token from google
//Once we have access token, assign token to session variable
//and we can redirect user back to page and login.
if (isset($_GET['code'])) 
{ 
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
	return;
}


if (isset($_SESSION['token'])) 
{ 
		$gClient->setAccessToken($_SESSION['token']);
}


if ($gClient->getAccessToken()) 
{
	  //Get user details if user is logged in
	  $user 				= $google_oauthV2->userinfo->get();
	  $user_id 				= $user['id'];
	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	 
	   
	  $_SESSION['token'] 	= $gClient->getAccessToken();
}
else 
{
	//get google login url
	$authUrl = $gClient->createAuthUrl();
}

?>