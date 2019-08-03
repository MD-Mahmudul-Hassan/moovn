<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width"/>
<base href="<?php echo base_url(); ?>">
<title><?php echo $heading.' - '.$title;?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>images/logo/<?php echo $favicon;?>">
<link href="css/reset.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/layout.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/themes.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/typography.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/styles.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/rating.css" rel="stylesheet" type="text/css" media="screen">

<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/jquery.jqplot.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/data-table.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/form.css" rel="stylesheet" type="text/css" media="screen">

<link href="css/ui-elements.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/wizard.css" rel="stylesheet" type="text/css">
<link href="css/sprite.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/gradient.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/developer.css" rel="stylesheet" type="text/css">
<link href="css/developer_colors.css" rel="stylesheet" type="text/css">
<link href="css/custom-dev-css.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" type="text/css" media="all" href="plugins/daterangepicker/css/glyphicons.css" />

<!--<link rel="stylesheet" type="text/css" href="css/ie/ie7.css" />
<link rel="stylesheet" type="text/css" href="css/ie/ie8.css" />
<link rel="stylesheet" type="text/css" href="css/ie/ie9.css" />-->
<script type="text/javascript">
var BaseURL = '<?php echo base_url();?>';
var baseURL = '<?php echo base_url();?>';

</script>
<script type="text/javascript">
	<?php  $validationArr = get_language_array_for_validation($this->data['langCode']);?>
	var required_txt = "<?php if(array_key_exists('required',$validationArr)){ echo $validationArr['required']; }else { echo "This field is required.";} ?>";
	var remote_txt = "<?php if(array_key_exists('remote',$validationArr)){ echo $validationArr['remote']; }else { echo "Please fix this field.";} ?>";
	var email_txt = "<?php if(array_key_exists('email',$validationArr)){ echo $validationArr['email']; }else { echo "Please enter a valid email address.";} ?>";
	var url_txt = "<?php if(array_key_exists('url',$validationArr)){ echo $validationArr['url']; }else { echo "Please enter a valid URL.";} ?>";
	var date_txt = "<?php if(array_key_exists('date',$validationArr)){ echo $validationArr['date']; }else { echo "Please enter a valid date.";} ?>";
	var dateISO_txt = "<?php if(array_key_exists('dateISO',$validationArr)){ echo $validationArr['dateISO']; }else { echo "Please enter a valid date (ISO).";} ?>";
	var number_txt = "<?php if(array_key_exists('number',$validationArr)){ echo $validationArr['number']; }else { echo "Please enter a valid number.";} ?>";
	var positiveNumber_txt = "<?php if(array_key_exists('positiveNumber',$validationArr)){ echo $validationArr['positiveNumber']; }else { echo "Please enter a valid positive number.";} ?>";
	var minfloatingNumber_txt = "<?php if(array_key_exists('minfloatingNumber',$validationArr)){ echo $validationArr['minfloatingNumber']; }else { echo "Please enter a less than 3 decimal point number.";} ?>";
	var phoneNumber_txt = "<?php if(array_key_exists('phoneNumber',$validationArr)){ echo $validationArr['phoneNumber']; }else { echo "Please enter a valid phone number.";} ?>";
	var digits_txt = "<?php if(array_key_exists('digits',$validationArr)){ echo $validationArr['digits']; }else { echo "Please enter only digits.";} ?>";
	var creditcard_txt = "<?php if(array_key_exists('creditcard',$validationArr)){ echo $validationArr['creditcard']; }else { echo "Please enter a valid credit card number.";} ?>";
	var equalTo_txt = "<?php if(array_key_exists('equalTo',$validationArr)){ echo $validationArr['equalTo']; }else { echo "Please enter the same value again.";} ?>";
	var lesserThan_txt = "<?php if(array_key_exists('lesserThan',$validationArr)){ echo $validationArr['lesserThan']; }else { echo "enter a value less than or equal to maximum amount";} ?>";
	var greaterThan_txt = "<?php if(array_key_exists('greaterThan',$validationArr)){ echo $validationArr['greaterThan']; }else { echo "Please enter a value greater than or equal to minimum amount";} ?>";
	var accept_txt = "<?php if(array_key_exists('accept',$validationArr)){ echo $validationArr['accept']; }else { echo "Please enter a value with a valid extension.";} ?>";
	var maxlength_txt = "<?php if(array_key_exists('maxlength',$validationArr)){ echo $validationArr['maxlength']; }else { echo "Please enter no more than {0} characters.";} ?>";
	var minlength_txt = "<?php if(array_key_exists('minlength',$validationArr)){ echo $validationArr['minlength']; }else { echo "Please enter at least {0} characters.";} ?>";
	var rangelength_txt = "<?php if(array_key_exists('rangelength',$validationArr)){ echo $validationArr['rangelength']; }else { echo "Please enter a value between {0} and {1} characters long.";} ?>";
	var range_txt = "<?php if(array_key_exists('range',$validationArr)){ echo $validationArr['range']; }else { echo "Please enter a value between {0} and {1}.";} ?>";
	var max_txt = "<?php if(array_key_exists('max',$validationArr)){ echo $validationArr['max']; }else { echo "Please enter a value less than or equal to {0}.";} ?>";
	var min_txt = "<?php if(array_key_exists('min',$validationArr)){ echo $validationArr['min']; }else { echo "Please enter a value greater than or equal to {0}.";} ?>";
	var firstname_txt = "<?php if(array_key_exists('firstname',$validationArr)){ echo $validationArr['firstname']; }else { echo "Please enter your firstname";} ?>";
	var username_txt = "<?php if(array_key_exists('username',$validationArr)){ echo $validationArr['username']; }else { echo "Please enter a username";} ?>";
	var username_length_txt = "<?php if(array_key_exists('username_length',$validationArr)){ echo $validationArr['username_length']; }else { echo "Your username must consist of at least 2 characters";} ?>";
	var password_txt = "<?php if(array_key_exists('password',$validationArr)){ echo $validationArr['password']; }else { echo "Please provide a password";} ?>";
	var new_password_txt = "<?php if(array_key_exists('new_password',$validationArr)){ echo $validationArr['new_password']; }else { echo "Please provide a new password";} ?>";
	var password_length_txt = "<?php if(array_key_exists('password_length',$validationArr)){ echo $validationArr['password_length']; }else { echo "Your password must be at least 5 characters long";} ?>";
	var retypr_password_txt = "<?php if(array_key_exists('retypr_password',$validationArr)){ echo $validationArr['retypr_password']; }else { echo "Please re-type your new password";} ?>";
	var same_password_txt = "<?php if(array_key_exists('same_password',$validationArr)){ echo $validationArr['same_password']; }else { echo "Please enter the same password as above";} ?>";
	var valid_email_address_txt = "<?php if(array_key_exists('valid_email_address',$validationArr)){ echo $validationArr['valid_email_address']; }else { echo "Please enter a valid email address.";} ?>";
	var accept_policy_txt = "<?php if(array_key_exists('accept_policy',$validationArr)){ echo $validationArr['accept_policy']; }else { echo "Please accept our policy";} ?>";
	var sub_admin_email_txt = "<?php if(array_key_exists('sub_admin_email',$validationArr)){ echo $validationArr['sub_admin_email']; }else { echo "Please enter sub-admin email address";} ?>";
	var admin_username_txt = "<?php if(array_key_exists('admin_username',$validationArr)){ echo $validationArr['admin_username']; }else { echo "Please enter admin username";} ?>";
	var new_admin_password_txt = "<?php if(array_key_exists('new_admin_password',$validationArr)){ echo $validationArr['new_admin_password']; }else { echo "Please enter new admin password";} ?>";
</script>
<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="js/jquery.ui.touch-punch.js"></script>
<script src="js/chosen.jquery.js"></script>
<script src="js/uniform.jquery.js"></script>
<script src="js/bootstrap-dropdown.js"></script>
<script src="js/bootstrap-colorpicker.js"></script>
<script src="js/sticky.full.js"></script>
<script src="js/jquery.noty.js"></script>
<script src="js/selectToUISlider.jQuery.js"></script>
<script src="js/fg.menu.js"></script>
<script src="js/jquery.tagsinput.js"></script>

<script src="js/jquery.cleditor.js"></script>
<script src="js/jquery.tipsy.js"></script>
<script src="js/jquery.peity.js"></script>
<script src="js/jquery.simplemodal.js"></script>
<script src="js/jquery.jBreadCrumb.1.1.js"></script>
<script src="js/jquery.colorbox-min.js"></script>
<script src="js/jquery.idTabs.min.js"></script>
<script src="js/jquery.multiFieldExtender.min.js"></script>
<script src="js/jquery.confirm.js"></script>
<script src="js/elfinder.min.js"></script>
<script src="js/accordion.jquery.js"></script>
<script src="js/autogrow.jquery.js"></script>
<script src="js/check-all.jquery.js"></script>
<script src="js/data-table.jquery.js"></script>
<script src="js/ZeroClipboard.js"></script>
<script src="js/TableTools.min.js"></script>
<script src="js/jeditable.jquery.js"></script>
<script src="js/ColVis.min.js"></script>
<script src="js/duallist.jquery.js"></script>
<script src="js/easing.jquery.js"></script>
<script src="js/full-calendar.jquery.js"></script>
<script src="js/input-limiter.jquery.js"></script>
<script src="js/inputmask.jquery.js"></script>
<script src="js/iphone-style-checkbox.jquery.js"></script>
<script src="js/meta-data.jquery.js"></script>
<script src="js/quicksand.jquery.js"></script>
<script src="js/raty.jquery.js"></script>
<script src="js/smart-wizard.jquery.js"></script>
<script src="js/stepy.jquery.js"></script>
<script src="js/treeview.jquery.js"></script>
<script src="js/ui-accordion.jquery.js"></script> 
<script src="js/vaidation.jquery.js"></script>
<script src="js/mosaic.1.0.1.min.js"></script>
<script src="js/jquery.collapse.js"></script>
<script src="js/jquery.cookie.js"></script>
<script src="js/jquery.autocomplete.min.js"></script>
<script src="js/localdata.js"></script>
<script src="js/excanvas.min.js"></script>
<script src="js/jquery.jqplot.min.js"></script>
<script src="js/chart-plugins/jqplot.dateAxisRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.cursor.min.js"></script>
<script src="js/chart-plugins/jqplot.logAxisRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.canvasTextRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.highlighter.min.js"></script>
<script src="js/chart-plugins/jqplot.pieRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.barRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.pointLabels.min.js"></script>
<script src="js/chart-plugins/jqplot.meterGaugeRenderer.min.js"></script>
<script src="js/jquery.MultiFile.js"></script>
<script src="js/validation.js"></script>

<script src="js/custom-scripts.js"></script>
<script src="js/jquery-input-file-text.js"></script>
<script type="text/javascript" src="js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
		tinyMCE.init({
		// General options
		mode : "specific_textareas",
		editor_selector : "mceEditor",
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		 
		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		file_browser_callback : "ajaxfilemanager",
		relative_urls : false,
		convert_urls: false,
		// Example content CSS (should be your site CSS)
		content_css : "css/example.css",
		 
		// Drop lists for link/image/media/template dialogs
		//template_external_list_url : "js/template_list.js",
		external_link_list_url : "js/link_list.js",
		external_image_list_url : "js/image_list.js",
		media_external_list_url : "js/media_list.js",
		 
		// Replace values for the template plugin
		template_replace_values : {
		username : "Some User",
		staffid : "991234"
		}
		});
		
		function ajaxfilemanager(field_name, url, type, win) {
			var ajaxfilemanagerurl = '<?php echo base_url();?>js/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php';
			switch (type) {
				case "image":
					break;
				case "media":
					break;
				case "flash": 
					break;
				case "file":
					break;
				default:
					return false;
			}
            tinyMCE.activeEditor.windowManager.open({
                url: '<?php echo base_url();?>js/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php',
                width: 782,
                height: 440,
                inline : "yes",
                close_previous : "no"
            },{
                window : win,
                input : field_name
            });
            
            return false;			
			var fileBrowserWindow = new Array();
			fileBrowserWindow["file"] = ajaxfilemanagerurl;
			fileBrowserWindow["title"] = "Ajax File Manager";
			fileBrowserWindow["width"] = "782";
			fileBrowserWindow["height"] = "440";
			fileBrowserWindow["close_previous"] = "no";
			tinyMCE.openWindow(fileBrowserWindow, {
			  window : win,
			  input : field_name,
			  resizable : "yes",
			  inline : "yes",
			  editor_id : tinyMCE.getWindowArg("editor_id")
			});
			
			return false;
		}
</script>
<script type="text/javascript">
function hideErrDiv(arg) {
    document.getElementById(arg).style.display = 'none';
}
</script>
<?php $checkbox_lan=get_language_array_for_keyword($this->data['langCode']);?>
<script>
var datatable_entries_per_page = "<?php if(array_key_exists('datatable_entries_per_page',$checkbox_lan)){ echo $checkbox_lan['datatable_entries_per_page']; }else { echo "Entries per page";} ?>";
var datatable_no_data_available = "<?php if(array_key_exists('datatable_no_data_available',$checkbox_lan)){ echo $checkbox_lan['datatable_no_data_available']; }else { echo "No data available in table";} ?>";
var datatable_no_record_found = "<?php if(array_key_exists('datatable_no_record_found',$checkbox_lan)){ echo $checkbox_lan['datatable_no_record_found']; }else { echo "No matching records found";} ?>";
var datatable_search = "<?php if(array_key_exists('datatable_search',$checkbox_lan)){ echo $checkbox_lan['datatable_search']; }else { echo "Search";} ?>";
var pagination_first = "<?php if(array_key_exists('pagination_first',$checkbox_lan)){ echo $checkbox_lan['pagination_first']; }else { echo "First";} ?>";
var pagination_last = "<?php if(array_key_exists('pagination_last',$checkbox_lan)){ echo $checkbox_lan['pagination_last']; }else { echo "Last";} ?>";
var pagination_previous = "<?php if(array_key_exists('pagination_previous',$checkbox_lan)){ echo $checkbox_lan['pagination_previous']; }else { echo "Previous";} ?>";
var pagination_next = "<?php if(array_key_exists('pagination_next',$checkbox_lan)){ echo $checkbox_lan['pagination_next']; }else { echo "Next";} ?>";

$(function () {
$('.on_off :checkbox').iphoneStyle();
$('.yes_no :checkbox').iphoneStyle({checkedLabel:'<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['verify_status_no_ucfirst']; ?>'});
$('.flat_percentage :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['coupon_code_flat']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['coupon_code_percent']; ?>'});
$('.active_inactive :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['status_active_ucfirst']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['status_inactive_ucfirst']; ?>'});
$('.publish_unpublish :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['status_publish_ucfirst']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['status_unpublish_ucfirst']; ?>'});
$('.live_sandbox :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['checkbox_live']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['checkbox_sandbox']; ?>'});
$('.disabled :checkbox').iphoneStyle();
$('.ac_nonac :checkbox').iphoneStyle({checkedLabel:'<?php echo $checkbox_lan['checkbox_ac']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['checkbox_non_ac']; ?>'});
$('.cod_on_off :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['status_enable_ucfirst']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['status_disable_ucfirst']; ?>'});
$('.prod_dev :checkbox').iphoneStyle({ checkedLabel: '<?php echo $checkbox_lan['checkbox_production']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['checkbox_development']; ?>' });
});
</script>
</head>
<body id="theme-default" class="full_block">
<?php  $this->load->view('admin/templates/sidebar.php'); ?>
<?php
$currentUrl = $this->uri->segment(2,0); 
$currentPage = $this->uri->segment(3,0);
if($currentUrl==''){
	$currentUrl = 'dashboard';
} 
if($currentPage==''){
	$currentPage = 'dashboard';
}
$current_url = $_SERVER['REQUEST_URI'];
?>
<div id="container">
	<div id="header">
		<div class="header_left">
			<div class="logo">
				<img src="images/logo/<?php echo $logo;?>" alt="<?php echo $siteTitle;?>" width="90px" title="<?php echo $siteTitle;?>">
			</div>
			<div id="responsive_mnu">
				<a href="#responsive_menu" class="fg-button" id="hierarchybreadcrumb"><span class="responsive_icon"></span><?php if ($this->lang->line('admin_menu_menu') != '') echo stripslashes($this->lang->line('admin_menu_menu')); else echo 'Menu'; ?></a>
				<div id="responsive_menu" class="hidden">
					<ul>
						<li>
							<a href="<?php echo base_url();?>admin/dashboard/admin_dashboard" <?php if($currentUrl=='dashboard'){ echo 'class="active"';} ?>>
								<span class="nav_icon computer_imac"></span> <?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?>
								<span class="up_down_arrow">&nbsp;</span>
							</a>
						</li>
						<li>
							<h6 style="margin: 10px 0;padding-left:10px; font-size:13px; font-weight:bold;color:#333; text-transform:uppercase; "><?php if ($this->lang->line('admin_menu_managements') != '') echo stripslashes($this->lang->line('admin_menu_managements')); else echo 'Managements'; ?></h6>
						</li>
						
						<?php extract($privileges); if ($allPrev == '1'){ ?>
						<li>
							<a href="#" <?php if($currentUrl=='adminlogin'){ echo 'class="active"';} ?>>
								<span class="nav_icon admin_user"></span> <?php if ($this->lang->line('admin_menu_admin') != '') echo stripslashes($this->lang->line('admin_menu_admin')); else echo 'Admin'; ?>
								<span class="up_down_arrow">&nbsp;</span>
							</a>
							<ul <?php if($currentUrl=='adminlogin'){ echo 'style="display: block;"';}else{ echo 'style="display: none;"';} ?>>
								<li>
									<a href="admin/adminlogin/display_admin_list" <?php if($currentPage=='display_admin_list'){ echo 'class="active"';} ?>>
										<?php if ($this->lang->line('admin_header_admin_user') != '') echo stripslashes($this->lang->line('admin_header_admin_user')); else echo 'Admin Users'; ?>
									</a>
								</li>
								<li>
									<a href="admin/adminlogin/change_admin_password_form" <?php if($currentPage=='change_admin_password_form'){ echo 'class="active"';} ?>>
										<?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?>
									</a>
								</li>
								<li>
									<a href="admin/adminlogin/admin_global_settings_form" <?php if($currentPage=='admin_global_settings_form'){ echo 'class="active"';} ?>>
										<?php if ($this->lang->line('admin_menu_settings') != '') echo stripslashes($this->lang->line('admin_menu_settings')); else echo 'Settings'; ?>
									</a>
								</li>
								<li>
									<a href="admin/adminlogin/admin_smtp_settings" <?php if($currentPage=='admin_smtp_settings'){ echo 'class="active"';} ?>>
										<?php if ($this->lang->line('admin_menu_smtp_settings') != '') echo stripslashes($this->lang->line('admin_menu_smtp_settings')); else echo 'SMTP Settings'; ?>
									</a>
								</li>
								<li>
                                <a href="admin/adminlogin/admin_site_settings" <?php
                                if ($currentPage == 'admin_site_settings') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_site_settings') != '') echo stripslashes($this->lang->line('admin_menu_site_settings')); else echo 'Site Settings'; ?>
                                </a>
								</li>								
								<?php if ($this->config->item('currency_name') == '' || $this->config->item('currency_code') == '' || $this->config->item('currency_symbol') == '') { ?>
									<li>
										<a href="admin/adminlogin/admin_currency_settings" <?php if ($currentPage == 'admin_currency_settings') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('currency_setting') != '') echo stripslashes($this->lang->line('admin_menu_admin')); else echo 'Currency Settings'; ?>
										</a>
									</li>
								<?php } ?>
								<?php if ($this->config->item('countryId') == '' || $this->config->item('countryName') == '') { ?>
									<li>
										<a href="admin/adminlogin/admin_country_settings" <?php if ($currentPage == 'admin_country_settings') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('currency_setting') != '') echo stripslashes($this->lang->line('currency_setting')); else echo 'Country Settings'; ?>
										</a>
									</li>
								<?php } ?>
							</ul>
						</li>
						
						
						<li>
							<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'subadmin') { echo 'class="active"'; } ?>>
								<span class="nav_icon user"></span> <?php if ($this->lang->line('admin_menu_subadmin') != '') echo stripslashes($this->lang->line('admin_menu_subadmin')); else echo 'Subadmin'; ?>
								<span class="up_down_arrow">&nbsp;</span>
							</a>
							<ul <?php if ($currentUrl == 'subadmin') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
								<li>
									<a href="admin/subadmin/display_sub_admin" <?php if ($currentPage == 'display_sub_admin') { echo 'class="active"'; } ?>>
									<?php if ($this->lang->line('admin_menu_subadmin_list') != '') echo stripslashes($this->lang->line('admin_menu_subadmin_list')); else echo 'Subadmin List'; ?>
									</a>
								</li>

								<li>
									<a href="admin/subadmin/add_sub_admin_form" <?php if ($currentPage == 'add_sub_admin_form') { echo 'class="active"'; } ?>>
										<?php if ($this->lang->line('admin_menu_add_new_subadmin') != '') echo stripslashes($this->lang->line('admin_menu_add_new_subadmin')); else echo 'Add New Subadmin'; ?>
									</a>
								</li>
							</ul>
						</li>
						
						<?php } ?>
						
						 <?php if ((isset($map) && is_array($map)) && in_array('0', $map) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'map') { echo 'class="active"'; } ?>>
									<span class="nav_icon marker"></span> <?php if ($this->lang->line('admin_menu_map_view') != '') echo stripslashes($this->lang->line('admin_menu_map_view')); else echo 'Map View'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'map') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/map/map_avail_drivers" <?php if ($currentPage == 'map_avail_drivers') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_view_available_drivers') != '') echo stripslashes($this->lang->line('admin_menu_view_available_drivers')); else echo 'View available drivers'; ?>
										</a>
									</li>
								</ul>
							</li>
						<?php } ?>
						
						<?php
						if ((isset($location) && is_array($location)) && in_array('0', $location) || $allPrev == '1') {
							if ($this->config->item('countryId') != '' || $this->config->item('countryName') != '') {
						?>
						<li>
							<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'location') { echo 'class="active"'; } ?>>
								<span class="nav_icon globe"></span> <?php if ($this->lang->line('admin_menu_location_fare') != '') echo stripslashes($this->lang->line('admin_menu_location_fare')); else echo 'Location & Fare'; ?><span class="up_down_arrow">&nbsp;</span>
							</a>
							<ul <?php if ($currentUrl == 'location') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
								<li>
									<a href="admin/location/display_location_list" <?php if ($currentPage == 'display_location_list' || $currentPage == 'location_fare') { echo 'class="active"'; } ?>>
										<?php if ($this->lang->line('admin_menu_location_list') != '') echo stripslashes($this->lang->line('admin_menu_location_list')); else echo 'Location List'; ?>
									</a>
								</li>
								<?php if ($allPrev == '1' || in_array('1', $location)) { ?>
								<li>
									<a href="admin/location/add_edit_location" <?php if ($currentPage == 'add_edit_location') { echo 'class="active"'; } ?>>
										<?php if ($this->lang->line('admin_menu_add_location') != '') echo stripslashes($this->lang->line('admin_menu_add_location')); else echo 'Add Location'; ?>
									</a>
								</li>
								<?php } ?>
							</ul>
						</li>  
						<?php
							}
						}
						?>
						
						<?php if ((isset($driver) && is_array($driver)) && in_array('0', $driver) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category')) { echo 'class="active"'; } ?>>
									<span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category')) { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; }?>>
									<li>
										<a href="admin/drivers/display_driver_dashboard" <?php if ($currentPage == 'display_driver_dashboard') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_drivers_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_drivers_dashboard')); else echo 'Drivers Dashboard'; ?>
										</a>
									</li>
									<li>
										<a href="admin/drivers/display_drivers_list" <?php if ($currentPage == 'display_drivers_list' || $currentPage == 'edit_driver_form' || $currentPage == 'change_password_form' || $currentPage == 'view_driver' || $currentPage == 'banking' || $currentPage == 'view_driver_reviews') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_drivers_list') != '') echo stripslashes($this->lang->line('admin_menu_drivers_list')); else echo 'Drivers List'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $driver)) { ?>
										<li>
											<a href="admin/drivers/add_driver_form" <?php if ($currentPage == 'add_driver_form') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_driver') != '') echo stripslashes($this->lang->line('admin_menu_add_driver')); else echo 'Add Driver'; ?>
											</a>
										</li>
									<?php } ?>
									<?php if ($allPrev == '1' || in_array('1', $driver)) { ?>
									<li>
										<a href="admin/drivers/mileage_list" <?php
										if ($currentPage == 'mileage_list') {
											echo 'class="active"';
										}
										?>>
											<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_mileage_list') != '') echo stripslashes($this->lang->line('admin_menu_mileage_list')); else echo 'Mileage List'; ?>
										</a>
									</li>
                            <?php } ?>
								</ul>
							</li>
						<?php } ?>
						
						<?php if ((isset($category) && is_array($category)) && in_array('0', $category) || $allPrev == '1') { ?>
							<li>
								<a href="admin/drivers/display_drivers_category" <?php if (($currentPage == 'display_drivers_category' || $currentPage == 'add_edit_category_types' || $currentPage == 'add_edit_category')) { echo 'class="active"'; } ?>>
									<span class="nav_icon record"></span> <?php if ($this->lang->line('admin_menu_car_types') != '') echo stripslashes($this->lang->line('admin_menu_car_types')); else echo 'Car Types'; ?>
								</a>
							</li>
						<?php } ?>
						
						<?php if ((isset($brand) && is_array($brand)) && in_array('0', $brand) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'brand') { echo 'class="active"'; } ?>>
									<span class="nav_icon companies"></span> <?php if ($this->lang->line('admin_menu_make_and_model') != '') echo stripslashes($this->lang->line('admin_menu_make_and_model')); else echo 'Make and Model'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'brand' || $currentPage == 'display_brand_list' || $currentPage == 'add_brand_form' || $currentPage == 'edit_brand_form') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/brand/display_brand_list" <?php if ($currentPage == 'display_brand_list') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_makers_list') != '') echo stripslashes($this->lang->line('admin_menu_makers_list')); else echo 'Makers List'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $brand)) { ?>
										<li>
											<a href="admin/brand/add_brand_form" <?php if ($currentPage == 'add_brand_form') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_new_maker') != '') echo stripslashes($this->lang->line('admin_menu_add_new_maker')); else echo 'Add New Maker'; ?>
											</a>
										</li>
									<?php } ?>							
									<li>
										<a href="admin/brand/display_model_list" <?php if ($currentPage == 'display_model_list') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_model_list') != '') echo stripslashes($this->lang->line('admin_menu_model_list')); else echo 'Model List'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $brand)) { ?>
										<li>
											<a href="admin/brand/add_edit_model" <?php if ($currentPage == 'add_edit_model') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_new_model') != '') echo stripslashes($this->lang->line('admin_menu_add_new_model')); else echo 'Add New Model'; ?>
											</a>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>
						
						<?php if ((isset($user) && is_array($user)) && in_array('0', $user) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'users' || $currentPage == 'view_user_reviews') { echo 'class="active"'; } ?>>
									<span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_users') != '') echo stripslashes($this->lang->line('admin_menu_users')); else echo 'Users'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'users' || $currentPage == 'view_user_reviews') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/users/display_user_dashboard" <?php if ($currentPage == 'display_user_dashboard') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?>
										</a>
									</li>
									<li>
										<a href="admin/users/display_user_list" <?php if ($currentPage == 'display_user_list' || $currentPage == 'view_user_reviews') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_users_list') != '') echo stripslashes($this->lang->line('admin_menu_users_list')); else echo 'Users List'; ?>
										</a>
									</li>
									<li>
										<a href="admin/users/display_user_list?user_type=deleted" <?php
										if (($currentPage == 'display_user_list' || $currentPage == 'view_user_reviews') && $this->input->get('user_type') == 'deleted') {
											echo 'class="active"';
										}
										?>>
											<?php if ($this->lang->line('admin_menu_deleted_users_list') != '') echo stripslashes($this->lang->line('admin_menu_deleted_users_list')); else echo 'Deleted Users List'; ?>
										</a>
									</li>
								</ul>
							</li>
						<?php } ?>
						
						 <?php if ((isset($revenue) && is_array($revenue)) && in_array('0', $revenue) || $allPrev == '1') { ?>
							<li>
								<a href="admin/revenue/display_site_revenue" <?php if ($currentUrl == 'revenue') { echo 'class="active"'; } ?>>
									<span class="nav_icon money"></span> <?php if ($this->lang->line('admin_menu_site_earnings') != '') echo stripslashes($this->lang->line('admin_menu_site_earnings')); else echo 'Site Earnings'; ?> 
								</a>
							</li>
						<?php } ?>
						
						<?php
						if ((isset($rides) && is_array($rides)) && in_array('0', $rides) || $allPrev == '1') {
							$ride_action = $this->input->get('act');
						?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'rides') { echo 'class="active"'; } ?>>
									<span class="nav_icon car"></span> <?php if ($this->lang->line('admin_menu_rides') != '') echo stripslashes($this->lang->line('admin_menu_rides')); else echo 'Rides'; ?>
									<span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'rides') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/rides/ride_dashboard" <?php
										if ($currentPage == 'ride_dashboard') {
											echo 'class="active"';
										}
										?>>
											<span class="list-icon">&nbsp;</span>Rides Dashboard
										</a>
									</li>
									<li>
										<a href="admin/rides/display_rides?act=Booked" <?php if ($ride_action == 'Booked') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_just_booked') != '') echo stripslashes($this->lang->line('admin_menu_just_booked')); else echo 'Just Booked'; ?>
										</a>
									</li>
									<li>
										<a href="admin/rides/display_rides?act=OnRide" <?php if ($ride_action == 'OnRide') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_on_rides') != '') echo stripslashes($this->lang->line('admin_menu_on_rides')); else echo 'On Rides'; ?>
										</a>
									</li>
									<li>
										<a href="admin/rides/display_rides?act=Completed" <?php if ($ride_action == 'Completed') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_completed_rides') != '') echo stripslashes($this->lang->line('admin_menu_completed_rides')); else echo 'Completed Rides'; ?>
									</li>
									<li>
										<a href="admin/rides/display_rides?act=Cancelled" <?php if ($ride_action == 'Cancelled' || $ride_action == 'riderCancelled' || $ride_action == 'driverCancelled') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_cancelled_rides') != '') echo stripslashes($this->lang->line('admin_menu_cancelled_rides')); else echo 'Cancelled Rides'; ?>
										</a>
									</li>
									
									<li>
										<a href="admin/rides/display_rides?act=Expired" <?php
										if ($ride_action == 'Expired') {
											echo 'class="active"';
										}
										?>>
											<?php if ($this->lang->line('admin_menu_expired_rides') != '') echo stripslashes($this->lang->line('admin_menu_expired_rides')); else echo 'Expired Rides'; ?>
										</a>
									</li>
									
									<li>
										<a href="admin/rides/cancel_ride" <?php
										if ($currentPage == 'cancel_ride' || $currentPage == 'cancelling_ride') {
											echo 'class="active"';
										}
										?>>
											<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_cancel_ride') != '') echo stripslashes($this->lang->line('admin_menu_cancel_ride')); else echo 'Cancel Ride'; ?>
										</a>
									</li>
								</ul>
							</li>
						<?php } ?>
						
						<?php if ((isset($vehicle) && is_array($vehicle)) && in_array('0', $vehicle) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'vehicle') { echo 'class="active"'; } ?>>
									<span class="nav_icon application_put_co"></span> <?php if ($this->lang->line('admin_menu_vehicles') != '') echo stripslashes($this->lang->line('admin_menu_vehicles')); else echo 'Vehicles'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'vehicle') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/vehicle/display_vehicle_list" <?php if ($currentPage == 'display_vehicle_list') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_vehicle_type_list') != '') echo stripslashes($this->lang->line('admin_menu_vehicle_type_list')); else echo 'Vehicle Type List'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $vehicle)) { ?>
										<li>
											<a href="admin/vehicle/add_edit_vehicle_type_form" <?php if ($currentPage == 'add_edit_vehicle_type_form') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_new_vehicle_type') != '') echo stripslashes($this->lang->line('admin_menu_add_new_vehicle_type')); else echo 'Add New Vehicle Type'; ?>
											</a>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?> 

						<?php if ((isset($reviews) && is_array($reviews)) && in_array('0', $reviews) || $allPrev == '1') { ?>
							<li>
								<a href="admin/reviews/display_reviews_options_list" <?php if ($currentPage == 'display_reviews_options_list') { echo 'class="active"'; } ?>>
									<span class="nav_icon feed_sl">&nbsp;</span><?php if ($this->lang->line('admin_menu_review_settings') != '') echo stripslashes($this->lang->line('admin_menu_review_settings')); else echo 'Review Settings'; ?>
								</a>
							</li>
						<?php } ?>
						
						<?php if ((isset($documents) && is_array($documents)) && in_array('0', $documents) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'documents') { echo 'class="active"'; } ?>>
									<span class="nav_icon documents"></span> <?php if ($this->lang->line('admin_menu_documents') != '') echo stripslashes($this->lang->line('admin_menu_documents')); else echo 'Documents'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'documents') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/documents/display_documents_list" <?php if ($currentPage == 'display_documents_list') { echo 'class="active"'; }?>>
											<?php if ($this->lang->line('admin_menu_documents_list') != '') echo stripslashes($this->lang->line('admin_menu_documents_list')); else echo 'Documents List'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $documents)) { ?>
										<li>
											<a href="admin/documents/add_edit_document_form" <?php if ($currentPage == 'add_edit_document_form') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_new_documents') != '') echo stripslashes($this->lang->line('admin_menu_add_new_documents')); else echo 'Add New Documents'; ?>
											</a>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>				

						<?php if ((isset($promocode) && is_array($promocode)) && in_array('0', $promocode) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'promocode') { echo 'class="active"'; } ?>>
									<span class="nav_icon bestseller_sl"></span> <?php if ($this->lang->line('admin_menu_coupon_codes') != '') echo stripslashes($this->lang->line('admin_menu_coupon_codes')); else echo 'Coupon Codes'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'promocode') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/promocode/promocode" <?php if ($currentPage == 'display_promocodes') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_coupon_code_list') != '') echo stripslashes($this->lang->line('admin_menu_coupon_code_list')); else echo 'Coupon code List'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $promocode)) { ?>
										<li>
											<a href="admin/promocode/add_promocode_form" <?php if ($currentPage == 'add_promocode_form') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_coupon_code') != '') echo stripslashes($this->lang->line('admin_menu_add_coupon_code')); else echo 'Add Coupon code'; ?>
											</a>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>
						
						<?php if ((isset($cancellation) && is_array($cancellation)) && in_array('0', $cancellation) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'cancellation') { echo 'class="active"'; } ?>>
									<span class="nav_icon pencil"></span> <?php if ($this->lang->line('admin_menu_cancellation') != '') echo stripslashes($this->lang->line('admin_menu_cancellation')); else echo 'Cancellation'; ?>
									<span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'cancellation') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/cancellation/user_cancellation_types" <?php if ($currentPage == 'user_cancellation_types') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_user_cancellation_reasons') != '') echo stripslashes($this->lang->line('admin_menu_user_cancellation_reasons')); else echo 'User Cancellation reasons'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $promocode)) { ?>
										<li>
											<a href="admin/cancellation/driver_cancellation_types" <?php if ($currentPage == 'driver_cancellation_types') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_driver_cancellation_reasons') != '') echo stripslashes($this->lang->line('admin_menu_driver_cancellation_reasons')); else echo 'Driver Cancellation reasons'; ?>
											</a>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>


						<?php if ((isset($banner) && is_array($banner)) && in_array('0', $banner) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'banner') { echo 'class="active"'; } ?>>
									<span class="nav_icon ipad"></span><?php if ($this->lang->line('admin_menu_banners') != '') echo stripslashes($this->lang->line('admin_menu_banners')); else echo 'Banners'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'banner') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/banner/display_banner" <?php if ($currentPage == 'display_banner') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_banners_list') != '') echo stripslashes($this->lang->line('admin_menu_banners_list')); else echo 'Banners List'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $banner)) { ?>
										<li>
											<a href="admin/banner/add_banner_form" <?php if ($currentPage == 'add_banner_form') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_banner') != '') echo stripslashes($this->lang->line('admin_menu_add_banner')); else echo 'Add Banner'; ?>
											</a>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>
						
						<?php if ((isset($cms) && is_array($cms)) && in_array('0', $cms) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'cms') { echo 'class="active"'; } ?>>
									<span class="nav_icon documents"></span> <?php if ($this->lang->line('admin_menu_pages') != '') echo stripslashes($this->lang->line('admin_menu_pages')); else echo 'Pages'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'cms') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/cms/display_cms" <?php if ($currentPage == 'display_cms') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_list_of_pages') != '') echo stripslashes($this->lang->line('admin_menu_list_of_pages')); else echo 'List of pages'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $cms)) { ?>
										<li>
											<a href="admin/cms/add_cms_form" <?php if ($currentPage == 'add_cms_form') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_main_page') != '') echo stripslashes($this->lang->line('admin_menu_add_main_page')); else echo 'Add Main Page'; ?>
											</a>
										</li>
										<li>
											<a href="admin/cms/add_subpage_form" <?php if ($currentPage == 'add_subpage_form') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_sub_page') != '') echo stripslashes($this->lang->line('admin_menu_add_sub_page')); else echo 'Add Sub Page'; ?>
											</a>
										</li>
										<li>
											<a href="admin/cms/add_landing_page_form" <?php
											if ($currentPage == 'add_landing_page_form') {
												echo 'class="active"';
											}
											?>><span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_landing_page') != '') echo stripslashes($this->lang->line('admin_menu_landing_page')); else echo 'Landing Page'; ?></a>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?> 

						<?php if ((isset($templates) && is_array($templates)) && in_array('0', $templates) || $allPrev == '1') { ?>                
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'templates') { echo 'class="active"'; } ?>>
									<span class="nav_icon mail"></span><?php if ($this->lang->line('admin_menu_templates') != '') echo stripslashes($this->lang->line('admin_menu_templates')); else echo 'Templates'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'templates') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/templates/display_email_template" <?php if ($currentPage == 'display_email_template') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_email_template_list') != '') echo stripslashes($this->lang->line('admin_menu_email_template_list')); else echo 'Email Template List'; ?>
										</a>
									</li>
									<?php if ($allPrev == '1' || in_array('1', $templates)) { ?>
										<li>
											<a href="admin/templates/add_email_template" <?php if ($currentPage == 'add_email_template') { echo 'class="active"'; } ?>>
												<?php if ($this->lang->line('admin_menu_add_email_template') != '') echo stripslashes($this->lang->line('admin_menu_add_email_template')); else echo 'Add Email Template'; ?>
											</a>
										</li>
									<?php } ?>
									
									<?php if ($allPrev == '1') { ?>
										<li>
											<a href="admin/templates/invoice_template" <?php if ($currentPage == 'invoice_template') { echo 'class="active"'; } ?>>
											<?php if($this->lang->line('invoice_template_lang') != '') echo stripslashes($this->lang->line('invoice_template_lang')); else echo 'Invoice Template';?>
											</a>
										</li>
									<?php } ?>
									
									<li>
										<a href="admin/templates/display_subscribers_list" <?php if ($currentPage == 'display_subscribers_list') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_news_subscribers_list') != '') echo stripslashes($this->lang->line('admin_menu_news_subscribers_list')); else echo 'News Subscribers List'; ?>
										</a>
									</li>

								</ul>
							</li>
						<?php } ?>
						
						<?php if ((isset($payment_gateway) && is_array($payment_gateway)) && in_array('0', $payment_gateway) || $allPrev == '1') { ?>
							<li>
								<a href="admin/payment_gateway/display_payment_gateway_list" <?php if ($currentPage == 'display_payment_gateway_list') { echo 'class="active"'; } ?>>
									<span class="nav_icon shopping_cart_2">&nbsp;</span><?php if ($this->lang->line('admin_menu_payment_gateway') != '') echo stripslashes($this->lang->line('admin_menu_payment_gateway')); else echo 'Payment Gateway'; ?>
								</a>
							</li>
						<?php } ?>

						<?php if ((isset($user) && is_array($user)) && in_array('0', $user) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'notification' || $currentPage == 'display_notification_user_list') { echo 'class="active"'; } ?>>
									<span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_notification') != '') echo stripslashes($this->lang->line('admin_menu_notification')); else echo 'Notification'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php if ($currentUrl == 'notification' || $currentPage == 'display_notification_user_list' || $currentPage =='display_notification_driver_list') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/notification/display_notification_user_list" <?php if ($currentPage == 'display_notification_user_list') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_users') != '') echo stripslashes($this->lang->line('admin_menu_users')); else echo 'Users'; ?>
										</a>
									</li>
									<li>
										<a href="admin/notification/display_notification_driver_list" <?php if ($currentPage == 'display_notification_driver_list') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?>
										</a>
									</li>

								</ul>
							</li>
						<?php } ?>


						<?php if ((isset($multilang) && is_array($multilang)) && in_array('0', $multilang) || $allPrev == '1') { ?>
							<li>
								<a href="<?php echo $current_url; ?>" <?php if ($currentUrl == 'multilanguage' || $currentPage == 'display_language_list') { echo 'class="active"'; } ?>>
									<span class="nav_icon cog_3"></span> <?php if ($this->lang->line('admin_menu_language_management') != '') echo stripslashes($this->lang->line('admin_menu_language_management')); else echo 'Language Management'; ?><span class="up_down_arrow">&nbsp;</span>
								</a>
								<ul <?php
								if ($currentUrl == 'multilanguage' || $currentPage == 'display_language_list' || $currentPage == 'mobile_display_language_list') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
									<li>
										<a href="admin/multilanguage/display_language_list" <?php if ($currentPage == 'display_language_list') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_website') != '') echo stripslashes($this->lang->line('admin_menu_website')); else echo 'Website'; ?>
										</a>
									</li>
									<li>
										<a href="admin/multilanguage/mobile_edit_language" <?php if ($currentPage == 'mobile_display_language_list') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_mobile') != '') echo stripslashes($this->lang->line('admin_menu_mobile')); else echo 'Mobile'; ?>
										</a>
									</li>
									
									<li>
										<a href="admin/multilanguage/keyword_edit_language" <?php if ($currentPage == 'keyword_edit_language') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_lang_keywords') != '') echo stripslashes($this->lang->line('admin_menu_lang_keywords')); else echo 'Keywords'; ?>
										</a>
									</li>
									<li>
										<a href="admin/multilanguage/validation_edit_language" <?php if ($currentPage == 'validation_edit_language') { echo 'class="active"'; } ?>>
											<?php if ($this->lang->line('admin_menu_lang_validation') != '') echo stripslashes($this->lang->line('admin_menu_lang_validation')); else echo 'Validation'; ?>
										</a>
									</li>

								</ul>
							</li>
						<?php } ?>
						
						
						<?php
						if ((isset($referral) && is_array($referral)) && in_array('0', $referral) || $allPrev == '1') {
							$ride_action = $this->input->get('act');
							?>
							<li>
								<a href="admin/referral/display_user_referrals" <?php
								if ($currentUrl == 'referral') {
									echo 'class="active"';
								}
								?>>
									<?php if ($this->lang->line('admin_menu_referral_history') != '') echo stripslashes($this->lang->line('admin_menu_referral_history')); else echo 'Referral History'; ?>
								</a>
							</li>
						<?php } ?>
				
				
				
				
					</ul>
				</div>
			</div>
		</div>
		
		
		<div class="header_right">
			<div id="user_nav" style="width: 300px;">
				<ul>
					<li class="user_thumb"><span class="icon"><img src="images/profile.png" width="30" height="30" alt="User"></span></li>
					<li class="user_info">
						<span class="user_name">
							<?php echo $this->session->userdata(APP_NAME.'_session_admin_name'); ?>
						</span>
						<?php if ($allPrev == '1'){?>
						<span>
							<a href="<?php echo base_url();?>" target="_blank" class="tipBot" title="<?php if ($this->lang->line('driver_view_site') != '') echo stripslashes($this->lang->line('driver_view_site')); else echo 'View Site'; ?>"><?php if ($this->lang->line('admin_header_visit_site') != '') echo stripslashes($this->lang->line('admin_header_visit_site')); else echo 'Visit Site'; ?></a> &#124;               
							<a href="admin/adminlogin/admin_global_settings_form" class="tipBot" title="<?php if ($this->lang->line('driver_edit_account_details') != '') echo stripslashes($this->lang->line('driver_edit_account_details')); else echo 'Edit account details'; ?>"><?php if ($this->lang->line('admin_menu_settings') != '') echo stripslashes($this->lang->line('admin_menu_settings')); else echo 'Settings'; ?></a>
						</span>
						<?php }else {?>
						<span>
							<a href="<?php echo base_url();?>" target="_blank" class="tipBot" title="<?php if ($this->lang->line('driver_view_site') != '') echo stripslashes($this->lang->line('driver_view_site')); else echo 'View Site'; ?>"><?php if ($this->lang->line('admin_header_visit_site') != '') echo stripslashes($this->lang->line('admin_header_visit_site')); else echo 'Visit Site'; ?></a> &#124; 
							<a href="admin/adminlogin/change_admin_password_form" class="tipBot" title="<?php if ($this->lang->line('driver_click_to_change') != '') echo stripslashes($this->lang->line('driver_click_to_change')); else echo 'Click to change your password'; ?>"><?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?></a> 
						</span>
						<?php }?>
					</li>
					
					<li class="logout"><a href="admin/adminlogin/admin_logout" class="tipBot" title="<?php if ($this->lang->line('rider_profile_logout') != '') echo stripslashes($this->lang->line('rider_profile_logout')); else echo 'Logout'; ?>"><span class="icon"></span><?php if ($this->lang->line('rider_profile_logout') != '') echo stripslashes($this->lang->line('rider_profile_logout')); else echo 'Logout'; ?></a></li>
				</ul>
			</div>
		</div>
	</div>

	
	
<?php if (validation_errors() != ''){?>
<div id="validationErr">
	<script>setTimeout("hideErrDiv('validationErr')", 3000);</script>
	<p><?php echo validation_errors();?></p>
</div>
<?php }?>


<script src="js/jquery.growl.js" type="text/javascript"></script>
<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />

<?php if($this->session->flashdata('sErrMSG') != '') { ?>
<script type="text/javascript">
  <?php 
	$sErrMSGdecoded = base64_decode($this->session->flashdata('sErrMSG'));
	$sErrMSGKeydecoded = base64_decode($this->session->flashdata('sErrMSGKey'));
  if($this->session->flashdata('sErrMSGType')=='message-red'){
  ?>
  $.growl.error({ title:"<?php echo $sErrMSGKeydecoded; ?>",message: "<?php echo  $sErrMSGdecoded;  ?>" });
  <?php } ?>
  <?php
  if($this->session->flashdata('sErrMSGType')=='message-green'){ 
  ?>
  $.growl.notice({ title:"<?php echo $sErrMSGKeydecoded; ?>",message: "<?php echo  $sErrMSGdecoded;  ?>"});
  <?php } ?>
  <?php 
  if($this->session->flashdata('sErrMSGType')=='warning'){ 
  ?>
  $.growl.warning({ message: "<?php echo  $sErrMSGdecoded;  ?>" });
  <?php } ?>
</script>
<?php } ?>

<input type="hidden" id="tabValidator" value="Yes"/>
<script>
var admin_checkBoxvalidationadmin='<?php if ($this->lang->line('common_please_select_box') != '') echo stripslashes($this->lang->line('common_please_select_box')); else echo 'Please Select the CheckBox'; ?>';
var admin_checkboxvalidationuser='<?php if ($this->lang->line('common_whether_continue_action') != '') echo stripslashes($this->lang->line('common_whether_continue_action')); else echo 'Whether you want to continue this action?'; ?>';
var admin_select_mail_tempolate='<?php if ($this->lang->line('common_select_mail_template') != '') echo stripslashes($this->lang->line('common_select_mail_template')); else echo 'Please select the mail template'; ?>';
var admin_no_records_found='<?php if ($this->lang->line('admin_common_no_record_found') != '') echo stripslashes($this->lang->line('admin_common_no_record_found')); else echo 'No records found'; ?>';
var admin_common_enter_email_id='<?php if ($this->lang->line('admin_common_enter_email_id') != '') echo stripslashes($this->lang->line('admin_common_enter_email_id')); else echo 'Please Enter The Email ID'; ?>';
var admin_common_correct_email_id='<?php if ($this->lang->line('admin_common_enter_correct_email_id') != '') echo stripslashes($this->lang->line('admin_common_enter_correct_email_id')); else echo 'Please Enter The Correct Email ID'; ?>';
var admin_common_change_status_record='<?php if ($this->lang->line('admin_common_change_status_record') != '') echo stripslashes($this->lang->line('admin_common_change_status_record')); else echo 'You are about to change the status of this record ! Continue?'; ?>';
var admin_select_only_one_checkbox='<?php if ($this->lang->line('admin_select_only_one_checkbox') != '') echo stripslashes($this->lang->line('admin_select_only_one_checkbox')); else echo 'Please Select only one CheckBox at a time'; ?>';
var admin_delete_record_restore_later='<?php if ($this->lang->line('admin_delete_record_restore_later') != '') echo stripslashes($this->lang->line('admin_delete_record_restore_later')); else echo 'You are about to delete this record. <br />It cannot be restored at a later time! Continue?'; ?>';
var admin_change_mode_record='<?php if ($this->lang->line('admin_change_mode_record') != '') echo stripslashes($this->lang->line('admin_change_mode_record')); else echo 'You are about to change the display mode of this record ! Continue?'; ?>';
var admin_ride_pickup_location='<?php if ($this->lang->line('admin_rides_pickup_location') != '') echo stripslashes($this->lang->line('admin_rides_pickup_location')); else echo 'Pickup Location'; ?>';
var admin_ride_drop_location='<?php if ($this->lang->line('admin_rides_drop_location') != '') echo stripslashes($this->lang->line('admin_rides_drop_location')); else echo 'Drop Location'; ?>';
var admin_ride_payment_by='<?php if ($this->lang->line('admin_ride_payment_by') != '') echo stripslashes($this->lang->line('admin_ride_payment_by')); else echo 'Payment by'; ?>';
var success='<?php if ($this->lang->line('admin_success') != '') echo stripslashes($this->lang->line('admin_success')); else echo 'Success'; ?>';
var error='<?php if ($this->lang->line('admin_error') != '') echo stripslashes($this->lang->line('admin_error')); else echo 'Error'; ?>';
var Confirmation='<?php if ($this->lang->line('admin_confirm') != '') echo stripslashes($this->lang->line('admin_confirm')); else echo 'Confirmation'; ?>';
var Yes='<?php if ($this->lang->line('admin_yes') != '') echo stripslashes($this->lang->line('admin_yes')); else echo 'Yes'; ?>';
var No='<?php if ($this->lang->line('admin_no') != '') echo stripslashes($this->lang->line('admin_no')); else echo 'No'; ?>';
var security_purpose='<?php if ($this->lang->line('security_purpose') != '') echo stripslashes($this->lang->line('security_purpose')); else echo 'For Security Purpose, Please Enter Email Id'; ?>';
var security_delete='<?php if ($this->lang->line('security_delete') != '') echo stripslashes($this->lang->line('security_delete')); else echo 'Delete Confirmation'; ?>';

</script>

