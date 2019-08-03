<?php 
error_reporting(0);

ob_start();
if(session_id() == '') {
	session_start();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>INSTALLATION</title>
<style>
@font-face {
    font-family: 'gibson-regular';
    src: url('fonts/gibson-regular.eot');
    src: url('fonts/gibson-regular.eot') format('embedded-opentype'),
         url('fonts/gibson-regular.woff') format('woff'),
         url('fonts/gibson-regular.ttf') format('truetype'),
         url('fonts/gibson-regular.svg#gibson-regular') format('svg');
}

body{
	margin:0;
	padding:0;
	background:#f2f2f2;
}
.main
{
	width:940px;
	margin:0 auto
}
.install_form
{
	float:left;
	width:100%;
}
.form_box{
	-webkit-border-radius:5px;
	-moz-border-radius:5px;
	border-radius:5px;
	padding:40px 25px;
	background:#FFFFFF;
	border:1px solid #c9c9c9;
	box-shadow:2px 1px 8px 2px #e9e9e6;
	-moz-box-shadow:2px 1px 8px 2px #e9e9e6;
	-webkit-box-shadow:2px 1px 8px 2px #e9e9e6;
	margin-bottom: 10px;
}
.form_field
{
	display: inline-block;
    margin: 0 0 20px;
    text-align: center;
    width: 100%;
}
.form_field label
{
	color:#373D48;
	font-size:18px;
	font-family: 'gibson-regular';
	width:18%;
	display:inline-block;
	float: left;
	text-align: left;
	margin-left: 25%;
	line-height:33px;
}
.instal_text
{
	border: 1px solid #DFDFDF;
   border-radius: 3px 3px 3px 3px;
   font-family: sans-serif;
   font-size: 15px;
   line-height: 20px;
   padding:5px 2px;
 	width:30%;
	display:inline-block;
	float: left;
	margin-bottom: 20px;	
}
.instal_text:hover{
	box-shadow:2px 1px 8px 2px #e9e9e6 inset;
	-moz-box-shadow:2px 1px 8px 2px #e9e9e6 inset;
	-webkit-box-shadow:2px 1px 8px 2px #e9e9e6 inset;
}
.instal_btn
{
	display:inline-block;
	width:150px;
	border-radius:4px;
	background:#373D48;
	color:#FFF;
	font-family: 'gibson-regular';
	border:none;
	padding:10px 0;
	cursor:pointer;
	font-size:14px;
	font-weight:bold;
	box-shadow:0 0 5px #000000;
	-moz-box-shadow:0 0 5px #000000;
	-webkit-box-shadow:0 0 5px #000000;
	letter-spacing:1px;
	margin-left:27px;
}
.instal_btn:hover
{
	color:#FFF;
	background:#000;
}
.form_field span
{
	display:inline-block;
	font-size:12px;
	font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;
	float: left;
	font-style: italic;
	font-size: 11px;
	margin-left: 5px;
	color: green;
}
.form_field span.error_msg{
	color: red;
	font-weight: bold;
	display:none;
}
.clear{
	clear:both;
}
.errorCon{
	float: left;
	width: 100%;
	text-align: center;
	color: red;
	font-weight: bold;
	font-size: 17px;
}
</style>
</head>
<body style="margin:0; padding:0">
	<div class="main">
    	<div  style="text-align:center; width:940px; margin:50px 0 20px 0;background-color: black;margin-bottom: 0px;">
        	<a href="#" style="margin:10px 0 0;"><img src="dectar-logo.png" /></a>
        </div>
		<div class="main">
			<div class="install_form">
				<div class="form_box">
						<h4>System Requirement</h4>
						<table>
							<tr>
								<td>PHP Version > 5.0</td>
								<td><img src="<?php if($phpversion){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($phpversion){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
							</tr>
							<tr>
								<td>MongoDB Extension</td>
								<td><img src="<?php if($mongo){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($mongo){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
							</tr>
							<tr>
								<td>Open SSL PHP extension</td>
								<td><img src="<?php if($openssl_enabled){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($openssl_enabled){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
							</tr>
							<tr>
								<td>GD PHP Extensions</td>
								<td><img src="<?php if($gd_enabled){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($gd_enabled){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
							</tr>
							<tr>
								<td>Curl</td>
								<td><img src="<?php if($curl){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($curl){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
							</tr>
							<tr>
								<td>HTTP Upload</td>
								<td><img src="<?php if($file_uploads){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($file_uploads){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
							</tr>
							<tr>
								<td>Rewrite Module Engine</td>
								<td><img src="<?php if($mod_rewrite){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($mod_rewrite){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
							</tr>
							<tr>
								<td style="vertical-align: top;">Directories with full permission</td>
								<td>
									<?php #if($directory_permission){ echo 'Yes'; }else{ echo 'No'; } ?>
									<table>
										<tr>
											<td><img src="<?php if($dp_commonsettings=='0777'){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($dp_commonsettings=='0777'){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>" /></td>
											<td>/commonsettings</td>
										</tr>
										<tr>
											<td><img src="<?php if($dp_images=='0777'){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($dp_images=='0777'){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
											<td>/images</td>
										</tr>
										<tr>
											<td><img src="<?php if($dp_temp_driver_docx=='0777'){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($dp_temp_driver_docx=='0777'){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
											<td>/drivers_documents_temp</td>
										</tr>
										<tr>
											<td><img src="<?php if($drivers_docx=='0777'){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($drivers_docx=='0777'){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
											<td>/drivers_documents</td>
										</tr>

										<tr>
											<td><img src="<?php if($dp_uploaded=='0777'){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($dp_uploaded=='0777'){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
											<td>/uploaded</td>
										</tr>
										<tr>
											<td><img src="<?php if($dp_newsletter=='0777'){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($dp_newsletter=='0777'){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
											<td>/newsletter</td>
										</tr>
										<tr>
											<td><img src="<?php if($trip_invoice=='0777'){ echo 'enabled.png'; }else{ echo 'disabled.png'; } ?>" alt="<?php if($trip_invoice=='0777'){ echo 'Enabled'; }else{ echo 'Disabled'; } ?>"/></td>
											<td>/trip_invoice</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<div>
							<?php if($reqChk>=1){ ?>
							<h3 style="color:#f00;">You can install the dectar script by fixing the above requirement.</h3>
							<?php } ?>
						</div>
			   </div>
			</div>
		</div>
    </div>
</body>
</html>
