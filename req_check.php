<?php 
error_reporting(0);

ob_start();
$reqChk=0;

$phpversion=TRUE;
$curl=TRUE;
$mongo=TRUE;
$mod_rewrite=TRUE;
$directory_permission=TRUE;
$gd_enabled=TRUE;
$openssl_enabled=TRUE;
$file_uploads=TRUE;

$php_version=floatval(phpversion());
$curlChk=extension_loaded('curl');
$mongoChk=extension_loaded('mongo');
$openssl=extension_loaded('openssl');
$gd=extension_loaded('gd');
$gd_info=function_exists('gd_info');
$gd_check1=function_exists('getimagesizefromstring');
$gd_check2=function_exists('file_get_contents');
$gd_check3=function_exists('getimagesize');
$gd_check4=function_exists('imagecreatefromjpeg');
$gd_check5=function_exists('imagecreatetruecolor');
$gd_check6=function_exists('imagecopyresampled');


$dp_commonsettings = substr(sprintf('%o', fileperms('./commonsettings')),-4);
$dp_images = substr(sprintf('%o', fileperms('./images')),-4);
$dp_temp_driver_docx = substr(sprintf('%o', fileperms('./drivers_documents_temp')),-4);
$drivers_docx = substr(sprintf('%o', fileperms('./drivers_documents')),-4);
$dp_uploaded = substr(sprintf('%o', fileperms('./uploaded')),-4);
$dp_newsletter = substr(sprintf('%o', fileperms('./newsletter')),-4);
$trip_invoice = substr(sprintf('%o', fileperms('./trip_invoice')),-4);

#echo '<pre>'; 
#print_r(ini_get_all());
#print_r($apache_modules);

if($dp_commonsettings != '0777' || $dp_images != '0777' || $dp_temp_driver_docx != '0777' || $drivers_docx != '0777'|| $dp_uploaded != '0777' || $dp_newsletter != '0777' || $trip_invoice != '0777'){
	$reqChk++;
	$directory_permission=FALSE;
}
if($php_version<5.0){
	$reqChk++;
	$phpversion=FALSE;
}
if($curlChk==0){
	$reqChk++;
	$curl=FALSE;
}
if($mongoChk==0){
	$reqChk++;
	$mongo=FALSE;
}

if($gd==0 || $gd_info==0 || $gd_check1==0 || $gd_check2==0 || $gd_check3==0 || $gd_check4==0 || $gd_check5==0 || $gd_check6==0){
	$reqChk++;
	$gd_enabled=FALSE;
}
if($openssl==0){
	$reqChk++;
	$openssl_enabled=FALSE;
}
if (function_exists('apache_get_modules')) {
    if ( !in_array( 'mod_rewrite', apache_get_modules() ) ) {
        $reqChk++;
		$mod_rewrite=FALSE;
    }
}

if (function_exists('ini_get')) {
	if(ini_get('file_uploads') != 1){
		$reqChk++;
		$file_uploads=FALSE;
	}
} 
?>