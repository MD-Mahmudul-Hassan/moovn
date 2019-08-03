<?php
ob_start();
error_reporting(-1);
if(session_id() == '') {
	session_start();
}


$file_cnt = array( 
	'admin_email' => addslashes(trim($_REQUEST['email'])),
	'admin_name' => addslashes(trim($_REQUEST['username'])),
	'admin_password' => addslashes(trim($_REQUEST['password'])),
	'site_name' => addslashes(trim($_REQUEST['sitename'])),
	'SiteUrl' => addslashes(trim($_REQUEST['siteurl'])),
	'hostName' => addslashes(trim($_REQUEST['mongo_host'])),
	'portNumber' => addslashes(trim($_REQUEST['mongo_port'])),
	'dbUserName' => addslashes(trim($_REQUEST['mongo_user'])),
	'dbPassword' => addslashes(trim($_REQUEST['mongo_pass'])),
	'databaseName' => addslashes(trim($_REQUEST['mongo_db'])),
);

$session_encrypt_key = md5(time());
function generate_token ($len = 32){
	// Array of potential characters, shuffled.
	$chars = array(
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 
		'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 
		'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
		'0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
	);
	shuffle($chars);
	$num_chars = count($chars) - 1;
	$token = '';
	// Create random token at the specified length.
	for ($i = 0; $i < $len; $i++)
	{
		$token .= $chars[mt_rand(0, $num_chars)];
	}
	return $token;
}
$session_encrypt_key = generate_token(32);
$app_name = time();


try{

	$connection = new MongoClient("mongodb://".trim($file_cnt['hostName']).":".trim($file_cnt['portNumber']), array("username" =>trim($file_cnt['dbUserName']), "password" => trim($file_cnt['dbPassword']),"db" => trim($file_cnt['databaseName'])));
		
	if($connection){
		try{
		
			// Write databse settings to a file
$file_content = "<?php 
\$dbhost = '".addslashes($file_cnt['hostName'])."';
\$dbport = '".addslashes($file_cnt['portNumber'])."';
\$databaseName = '".addslashes($file_cnt['databaseName'])."';
\$dbUserName = '".addslashes($file_cnt['dbUserName'])."';
\$dbPassword = '".addslashes($file_cnt['dbPassword'])."';
?>";
		   
			$file_name = 'config-dectar/databaseValues.php'; 
			@file_put_contents($file_name, $file_content);
			$baseURL	= dirname(__FILE__);
			@chmod($baseURL . '/config-dectar/databaseValues.php', 0644);
			
			// Update the site settings in the database
			
	
		$file_name = 'config-dectar/dectar_app_settings.php';
$config = "<?php 
define('APP_NAME','".trim($app_name)."');
\$config['encryption_key']='".trim($session_encrypt_key)."';
\$config['sess_cookie_name']=APP_NAME;
?>";
		
		@file_put_contents($file_name, $config);
		
	
		$file_name = 'commonsettings/dectar_admin_settings.php';
$config = "<?php 
\$config['admin_id']='1';
\$config['admin_name']='".addslashes($file_cnt['admin_name'])."';		
\$config['site_contact_mail']='".addslashes($file_cnt['admin_email'])."';
\$config['email_title']='".addslashes($file_cnt['site_name'])."';		
\$config['logo_image']='logo.png';
\$config['favicon_image']= 'favicon.png';		
\$config['meta_title']='".addslashes($file_cnt['site_name'])."';
\$config['meta_keyword']='".addslashes($file_cnt['site_name'])."';
\$config['meta_description']='".addslashes($file_cnt['site_name'])."';		
\$config['email']='".addslashes($file_cnt['admin_email'])."';
\$config['admin_type']='super';
\$config['is_verified']='Yes';
\$config['status']='Active'; 
\$config['base_url']='".$file_cnt['SiteUrl']."';
?>";
		
		@file_put_contents($file_name, $config);
		
		$newdata = array('admin_name'=>addslashes($file_cnt['admin_name']),
											'admin_password'=>md5(addslashes($file_cnt['admin_password'])),
											'email'=>addslashes($file_cnt['admin_email']),
											'site_contact_mail'=>addslashes($file_cnt['admin_email']),
											'email_title'=>addslashes($file_cnt['site_name'])
											);  
											
        $db = $connection->selectDB(addslashes($file_cnt['databaseName']));
		$collection = 'dectar_admin';
		$res = $db->$collection->update(array("admin_id" => "1"), array('$set'=>$newdata));
		
		@unlink('installation.php');
		@unlink('check_db_connect.php');
		@unlink('req_check.php');
		@unlink('req_failed.php');
		session_regenerate_id();
		header('location:'.$file_cnt['SiteUrl']);
		
		}catch ( MongoException $e ){
			$_SESSION['errorMSG'] = $e->getMessage();
			echo "<script>window.history.go(-1);</script>";
		}
	}
}catch ( MongoConnectionException $e ){
    $_SESSION['errorMSG'] = $e->getMessage();
	echo "<script>window.history.go(-1);</script>";
}catch ( MongoException $e ){
    $_SESSION['errorMSG'] = $e->getMessage();
	echo "<script>window.history.go(-1);</script>";
}

die;