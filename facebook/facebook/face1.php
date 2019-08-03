<?php 
include_once '../main.php'; 
//echo $_REQUEST['token']; die; 
/****************** New window for Facebook Login ***************/
if(isset($_REQUEST['token'])){

$user = json_decode(file_get_contents('https://graph.facebook.com/me?access_token='.$_REQUEST['token']));

$FBlogout='https://www.facebook.com/logout.php?next='.BASE_PATH.'signout.html%3Fsecret%3D&access_token='.$_REQUEST['token'];
}
/****************** New window for Facebook Login ***************/

/*///////////////// All values assigned in $user /////////////**/
	
		if(($user)&&(!isset($_SESSION['wwd_user_id']))){ 
		
		//print_r($user);die;
		$mail = $user->email; 
		$fb_id = $user->id;

		//Login User
		$select = "SELECT * FROM ".USER." WHERE emailAddress ='$mail' and fb_id='$fb_id'";
		$selUserQry = $userDAO->ExecuteQuery($select,'selectassoc');
		unset($selUserQry[0]['password']);
		//$SelResult=mysql_fetch_array($selUserQry);
		$SelRow= $userDAO->ExecuteQuery($select,'norows');;
		
		if($SelRow>0){
		
			$update = "update ".USER." set last_login_date = now(), last_login_ip = '".$_SERVER['REMOTE_ADDR']."' where id = '".$selUserQry[0]['id']."'";
			$userDAO->ExecuteQuery($update,'update');
					
			$_SESSION['f_dummy'] ='facebook';
			$_SESSION['f_login'] = $selUserQry[0]['fb_id'];
			$_SESSION['FBlogout']=$FBlogout;
			$_SESSION['wwd_user_id'] = $selUserQry[0]['id'];
			Redirect("index.html");exit;
		}else{
		//Insert as a User
			$random = substr(number_format(time() * rand(),0,'',''),0,6); 	
			$firstName 		= $user->first_name;
			$lastName 		= $user->last_name;
			$gender 		= $user->gender;
			$email 			= $user->email;
			$verified 		= $user->verified;
			$fb_id          = $user->id;
	

		
			$insQuery="INSERT INTO ".USER." (emailAddress,fname,lname,status,dateAdded,verify,fb_id,refer_id,login_type) VALUES('".$email."','".$firstName."','".$lastName."','Active','".date('Y-m-d')."','".$verified."','".$fb_id."','".$random."','Affiliate') ";
			 $selUserQry = $userDAO->ExecuteQuery($insQuery,'insert');	
			 $id= mysql_insert_id();		
			 
			$Affiliatefriend = defaultAffiliate();
			
			if($Affiliatefriend!=''){
				$query = "select * from ".USER." where refer_id='".$Affiliatefriend."' "; 
				$refers = $this->ExecuteQuery($query,'selectassoc');
				
				 $query1 = "insert INTO ".AFFILIATEREFER." SET affiliate_id = '".$refers[0]['id']."', user_login_id ='".$insert_id."',credit='0',status='Active'"; 
				 $result = $this->ExecuteQuery($query1, 'insert');	
			
			}else{
				$defaultfriends = defaultReferfriend();
			}
			
			if($defaultfriends!=''){
				$query = "select * from ".USER." where refer_id='".$defaultfriends."' "; 
				$refers = $userDAO->ExecuteQuery($query,'selectassoc');

				 $query1 = "insert INTO ".REFERAL." SET refer_id = '".$refers[0]['id']."', user_login_id ='".$insert_id."',credit='0',status='Active'"; 
				 $result = $userDAO->ExecuteQuery($query1, 'insert');			
			
			} 
 			
				 $query = "INSERT INTO ".SUBSCRIP." SET subscrip_mail = '".trim(addslashes($email))."',
								active = '0',
								status= 'Active',
								dateAdded ='".date('Y-m-d')."'";  
				$result = $userDAO->ExecuteQuery($query, 'insert');
					
				$_SESSION['f_dummy'] ='facebook';			
				$_SESSION['FBlogout']=$FBlogout;
				$_SESSION['f_login'] = $fb_id;
				$_SESSION['wwd_user_id'] = $id;
				$update = "update ".USER." set last_login_date = now(), last_login_ip = '".$_SERVER['REMOTE_ADDR']."' where id = '".$id."'";
				$userDAO->ExecuteQuery($update,'update');
		
			
							
		$subject = 'Welcome to '.$json_result[0]['site_name'].' - Thank you for Registration using Facebook';
		$message = '<body background="'.BASE_PATH.'images/main-bg.gif" leftmargin="0" rightmargin="15" topmargin="15" bottommargin="0" >
					
<div style="width:600px;background:#FFFFFF; margin:0 auto; border-radius:10px;-webkit-border-radius:10px;-moz-border-radius:10px;-ms-border-radius:10px;-o-border-radius:10px; box-shadow:0 0 5px #ccc; -webkit-box-shadow:0 0 5px #ccc;-moz-box-shadow:0 0 5px #ccc;-ms-box-shadow:0 0 5px #ccc;-o-box-shadow:0 0 5px #ccc; border:1px solid #b9b9b9;">

<div style="background:#C9CACB; padding:10px; border-radius:10px 10px 0 0;-webkit-border-top-left-radius:10px;-webkit-border-top-right-radius:10px;-moz-border-top-left-radius:10px;-moz-border-top-right-radius:10px;-ms-border-top-left-radius:10px;-ms-border-top-right-radius:10px;-o-border-top-left-radius:10px;-o-border-top-right-radius:10px; text-align:center;">
    
   <div style="float:left; width:50%;"> <a href="'.BASE_PATH.'" target="_blank"><img src="'.LOGO_PATH.$json_result[0]['logo_image'].'" style="border:none;"  alt="'.$json_result[0]['site_name'].'" width="300" height="55" /></a></div>
	<div style="float:right; width:50%; text-align:right; font-family:Myriad Pro; font-size:20px; color:#11367E; padding-top:30px;">'.date('l').', '.date('M').'  '.date('d').', '.date('Y').'</div>
    <div style="clear:both;"></div>
    
    </div>
    
    <div style=" background:#FFFFFF; padding:10px; width:580px;">
 		<div style="font-family:Myriad Pro; font-size:24px; color:#bc240c; padding-bottom:15px;">Thank You for Registering '.$json_result[0]['site_name'].' using Facebook</div>

	<div  style="font-family:Myriad Pro; font-size:16px; color:#000;padding-bottom:15px; line-height:24px; text-align:justify;">You are now a part of an exclusive community of '.ucfirst($json_result[0]['site_name']).'</div>
	<div  style="font-family:Myriad Pro; font-size:16px; color:#000;padding-bottom:15px; line-height:24px; text-align:justify;">To login to the site, just <a href="'.BASE_PATH.'signin.html" target="_blank"> signin</a> here with this email address: '.$email.'</div>

	<div  style="font-family:Myriad Pro; font-size:18px; color:#000;padding-bottom:15px;">Regards</div>
	<div  style="font-family:Myriad Pro; font-size:18px; color:#000;padding-bottom:15px;">'.strtoupper($json_result[0]['site_name'].' Team').'</div>
  </div>
    
    
    <div style="background:#C9CACB; padding:10px; width:580px;border-radius:0 0 10px 10px;-webkit-border-bottom-left-radius:10px;-webkit-border-bottom-right-radius:10px;-moz-border-bottom-left-radius:10px;-moz-border-bottom-right-radius:10px;-ms-border-bottom-left-radius:10px;-ms-border-bottom-right-radius:10px;-o-border-bottom-left-radius:10px;-o-border-bottom-right-radius:10px;">
    
    <div style="margin:1% 0; width:100%; float:left; text-align:center;">
    	<a href="'.$json_result[0]['facebook_link'].'" style="margin-right:1%; border:none;" target="_blank"><img src="'.BASE_PATH.'images/facebook.png"  style="border:none;"/></a>
        <a href="'.$json_result[0]['twitter_link'].'" style="margin-right:1%;" target="_blank"><img src="'.BASE_PATH.'images/twitter.png"  style="border:none;"/></a>
         <a href="'.$json_result[0]['googleplus_link'].'" style="margin-right:1%;" target="_blank"><img src="'.BASE_PATH.'images/google.png"  style="border:none;"/></a>
          <a href="'.$json_result[0]['pinterest_link'].'" style="margin-right:1%;" target="_blank"><img src="'.BASE_PATH.'images/pinterest.png"  style="border:none;"/></a>
    </div>
    
   <div style="font-size:13px; font-weight:normal; font-family:Myriad Pro; color:#000;  text-align:center; padding-bottom:10px;">'.$json_result[0]['footer_content'].'  <a href="'.BASE_PATH.'pages/about-us.html" style="color: #990000; text-decoration:none;">About Us</a></div>  
   
    <div style="font-size:13px; font-weight:normal; font-family:Myriad Pro; color:#000;  text-align:center;padding-bottom:10px;  ">Visit us on the web at: <a href="'.BASE_PATH.'index.html" style="color:#990000;text-decoration:none;" target="_blank">http://webworkerdeals.com/</a></div>

 <div style="font-size:13px; font-weight:normal; font-family:Myriad Pro; color:#000;  text-align:center; padding-bottom:10px; ">Email us:  <a href="mailto:'.$json_result[0]['support_email'].'" style="color:#990000;text-decoration:none;">'.$json_result[0]['support_email'].'</a></div>

  </div>

</div>
</body>'; 
			
		$res = $this->pearEmailSending(strip_tags($json_result[0]['support_email']),$email,$subject,$message);							
				
		Redirect("index.html");exit;
	}
}else{
	Redirect("index.html");exit;
}
?>