<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width"/>
        <base href="<?php echo base_url(); ?>">
        <title><?php echo $heading . ' - ' . $title; ?></title>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(); ?>images/logo/<?php echo $favicon; ?>">
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
        <link href="css/driver_colors.css" rel="stylesheet" type="text/css">
        <link href="css/custom-dev-css.css" rel="stylesheet" type="text/css">
        <link href="css/driver_responsive_styles.css" rel="stylesheet" type="text/css">

        <link rel="stylesheet" type="text/css" media="all" href="plugins/daterangepicker/css/glyphicons.css" />

        <!--<link rel="stylesheet" type="text/css" href="css/ie/ie7.css" />
        <link rel="stylesheet" type="text/css" href="css/ie/ie8.css" />
        <link rel="stylesheet" type="text/css" href="css/ie/ie9.css" />-->
        <script type="text/javascript">
            var BaseURL = '<?php echo base_url(); ?>';
            var baseURL = '<?php echo base_url(); ?>';
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
        <script src="js/custom-scripts.js"></script>
        <script src="js/validation.js"></script>
        <script type="text/javascript" src="js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript">
            tinyMCE.init({
                // General options
                mode: "specific_textareas",
                editor_selector: "mceEditor",
                theme: "advanced",
                plugins: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
                // Theme options
                theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
                theme_advanced_toolbar_location: "top",
                theme_advanced_toolbar_align: "left",
                theme_advanced_statusbar_location: "bottom",
                theme_advanced_resizing: true,
                file_browser_callback: "ajaxfilemanager",
                relative_urls: false,
                convert_urls: false,
                // Example content CSS (should be your site CSS)
                content_css: "css/example.css",
                // Drop lists for link/image/media/template dialogs
                //template_external_list_url : "js/template_list.js",
                external_link_list_url: "js/link_list.js",
                external_image_list_url: "js/image_list.js",
                media_external_list_url: "js/media_list.js",
                // Replace values for the template plugin
                template_replace_values: {
                    username: "Some User",
                    staffid: "991234"
                }
            });

            function ajaxfilemanager(field_name, url, type, win) {
                var ajaxfilemanagerurl = '<?php echo base_url(); ?>js/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php';
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
                    url: '<?php echo base_url(); ?>js/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php',
                    width: 782,
                    height: 440,
                    inline: "yes",
                    close_previous: "no"
                }, {
                    window: win,
                    input: field_name
                });

                return false;
                var fileBrowserWindow = new Array();
                fileBrowserWindow["file"] = ajaxfilemanagerurl;
                fileBrowserWindow["title"] = "Ajax File Manager";
                fileBrowserWindow["width"] = "782";
                fileBrowserWindow["height"] = "440";
                fileBrowserWindow["close_previous"] = "no";
                tinyMCE.openWindow(fileBrowserWindow, {
                    window: win,
                    input: field_name,
                    resizable: "yes",
                    inline: "yes",
                    editor_id: tinyMCE.getWindowArg("editor_id")
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

        $('#hierarchybreadcrumb').click(function(e){
            e.preventDefault();
            // stop a.click firing but allow click event to continue bubbling?
        });
        
		</script>
    </head>
    <body id="theme-default" class="full_block">
        <?php $this->load->view('driver/templates/sidebar.php'); ?>
        <?php
        if ($this->lang->line('driver_dashboard') != '')
            $dashboard = stripslashes($this->lang->line('driver_dashboard'));
        else
            $dashboard = 'dashboard';
        $currentUrl = $this->uri->segment(2, 0);
        $currentPage = $this->uri->segment(3, 0);
        if ($currentUrl == '') {
            $currentUrl = $dashboard;
        }
        if ($currentPage == '') {
            $currentPage = $dashboard;
        }
        ?>
        <div id="container">
            <div id="header" style="background:#ccc;">
                <div class="header_left">
                    <div id="responsive_mnu">
                        <div class="responsive_logo">
                            <?php
                            if ($this->lang->line('home_cabily') != '')
                                $sitename = str_replace('{SITENAME}', $this->config->item('email_title'), stripslashes($this->lang->line('home_cabily')));
                            else
                                $sitename = $this->config->item('email_title');
                            ?>
                            <img src="images/logo/<?php echo $logo; ?>" alt="<?php echo $sitename; ?>" width="90px" title="<?php echo $sitename; ?>">
                        </div>
                        <a href="#responsive_menu" class="fg-button" id="hierarchybreadcrumb"><span class="responsive_icon"></span><?php
                            if ($this->lang->line('driver_menu') != '')
                                echo stripslashes($this->lang->line('driver_menu'));
                            else
                                echo 'Menu';
                            ?></a>
                        <div id="responsive_menu" class="hidden">
							<ul>
								<li>
									<a href="<?php echo base_url(); ?>driver/dashboard/driver_dashboard" <?php
									if ($currentUrl == 'dashboard') {
										echo 'class="active"';
									}
									?>>
										<?php
										if ($this->lang->line('driver_dash') != '')
											echo stripslashes($this->lang->line('driver_dash'));
										else
											echo 'Dashboard';
										?> 
									</a>
								</li>
								<li>
									<a href="driver/profile/edit_profile_form" <?php
									if ($currentPage == 'edit_profile_form') {
										echo 'class="active"';
									}
									?>>
										<span class="nav_icon admin_user"></span><?php
										if ($this->lang->line('rider_profile_profile') != '')
											echo stripslashes($this->lang->line('rider_profile_profile'));
										else
											echo 'Profile';
										?>
									</a>
								</li>

								<li>
									<a href="driver/profile/banking" <?php
									if ($currentPage == 'banking') {
										echo 'class="active"';
									}
									?>>
										<?php
										if ($this->lang->line('driver_banking') != '')
											echo stripslashes($this->lang->line('driver_banking'));
										else
											echo 'Banking';
										?>
									</a>
								</li>

								<li>
									<a href="driver/profile/change_email_form" <?php
									if ($currentPage == 'change_email_form') {
										echo 'class="active"';
									}
									?>>
										<span class="nav_icon attach_2_co"></span><?php
										if ($this->lang->line('driver_change_mail') != '')
											echo stripslashes($this->lang->line('driver_change_mail'));
										else
											echo 'Change Email';
										?>
									</a>
								</li>

								<li>
									<a href="driver/profile/change_mobile_form" <?php
									if ($currentPage == 'change_mobile_form') {
										echo 'class="active"';
									}
									?>>
										<span class="nav_icon mobile_phone"></span><?php
										if ($this->lang->line('driver_change_mob') != '')
											echo stripslashes($this->lang->line('driver_change_mob'));
										else
											echo 'Change Mobile';
										?>
									</a>
								</li>

								<li>
									<a href="driver/profile/change_password_form" <?php
									if ($currentPage == 'change_password_form') {
										echo 'class="active"';
									}
									?>>
										<span class="nav_icon locked_2"></span><?php
										if ($this->lang->line('driver_change_pwd') != '')
											echo stripslashes($this->lang->line('driver_change_pwd'));
										else
											echo 'Change Password';
										?>
									</a>
								</li>

								<?php $ride_action = $this->input->get('act'); ?>
								<li>
									<a href="#" <?php
									if ($currentUrl == 'rides') {
										echo 'class="active"';
									}
									?>>
										<span class="nav_icon car"></span> Rides<span class="up_down_arrow">&nbsp;</span>
									</a>
									<ul <?php
									if ($currentUrl == 'rides') {
										echo 'style="display: block;"';
									} else {
										echo 'style="display: none;"';
									}
									?>>
                                        <li>
                                            <a href="driver/rides/display_rides?act=Upcoming" <?php
                                            if ($ride_action == 'Upcoming') {
                                                echo 'class="active"';
                                            }
                                            ?>>
                                                <?php
                                                if ($this->lang->line('driver_upcoming') != '')
                                                    echo stripslashes($this->lang->line('driver_upcoming'));
                                                else
                                                    echo 'Upcoming';
                                                ?>
                                            </a>
                                        </li>
										<li>
											<a href="driver/rides/display_rides?act=OnRide" <?php
											if ($ride_action == 'OnRide') {
												echo 'class="active"';
											}
											?>>
												<?php
												if ($this->lang->line('driver_on_rides') != '')
													echo stripslashes($this->lang->line('driver_on_rides'));
												else
													echo 'On Rides';
												?>
											</a>
										</li>
										<li>
											<a href="driver/rides/display_rides?act=Completed" <?php
											if ($ride_action == 'Completed') {
												echo 'class="active"';
											}
											?>>
												<?php
												if ($this->lang->line('driver_comp_rides') != '')
													echo stripslashes($this->lang->line('driver_comp_rides'));
												else
													echo 'Completed Rides';
												?>
											</a>
										</li>
										<li>
											<a href="driver/rides/display_rides?act=Cancelled" <?php
											if ($ride_action == 'Cancelled') {
												echo 'class="active"';
											}
											?>>
												<?php
												if ($this->lang->line('driver_cancel_rides') != '')
													echo stripslashes($this->lang->line('driver_cancel_rides'));
												else
													echo 'Cancelled Rides';
												?>
											</a>
										</li>
									</ul>
								</li>

								<li>
									<a href="driver/payments/display_payments" <?php
									if ($currentPage == 'display_payments' || $currentPage == 'payment_summary') {
										echo 'class="active"';
									}
									?>>
										<span class="nav_icon money"></span><?php
										if ($this->lang->line('driver_earn') != '')
											echo stripslashes($this->lang->line('driver_earn'));
										else
											echo 'Earnings';
										?>
									</a>
								</li>
							</ul>
                        </div>
                    </div>
                </div>


                <div class="header_right">
                    <div id="user_nav" style="width: 300px;">
                        <ul>
                            <li class="user_thumb"><span class="icon"><img src="images/profile.png" width="30" height="30" alt="<?php
                                    if ($this->lang->line('driver_user') != '')
                                        echo stripslashes($this->lang->line('driver_user'));
                                    else
                                        echo 'User';
                                    ?>"></span></li>
                            <li class="user_info">
                                <span class="user_name">
                                    <?php echo $this->session->userdata(APP_NAME.'_session_driver_name'); ?>
                                </span>
                                    <span>
                                        <a href="<?php echo base_url(); ?>" target="_blank" class="tipBot" title="<?php if ($this->lang->line('driver_view_site') != '') echo stripslashes($this->lang->line('driver_view_site')); else echo 'View Site'; ?>">
										<?php
										if ($this->lang->line('driver_visit_site') != '')
											echo stripslashes($this->lang->line('driver_visit_site'));
										else
											echo 'Visit Site';
										?>
										</a> &#124; 
										<a href="driver/profile/change_password_form" class="tipBot" title="<?php
                                        if ($this->lang->line('driver_click_to_change') != '')
                                            echo stripslashes($this->lang->line('driver_click_to_change'));
                                        else
                                            echo 'Click to change your password';
                                        ?>">
										<?php
										if ($this->lang->line('driver_change_password') != '')
											echo stripslashes($this->lang->line('driver_change_password'));
										else
											echo 'Change Password';
										?>
										</a> 
                                    </span>
                            </li>
                            <li class="logout">
								<a href="driver/profile/driver_logout" class="tipBot" title="<?php if ($this->lang->line('rider_profile_logout') != '') echo stripslashes($this->lang->line('rider_profile_logout')); else echo 'Logout'; ?>">
									<span class="icon"></span>
									<?php if ($this->lang->line('rider_profile_logout') != '') echo stripslashes($this->lang->line('rider_profile_logout')); else echo 'Logout'; ?>
								</a>
							</li>
                        </ul>
                    </div>
                </div>
            </div>


            <?php if (validation_errors() != '') { ?>
                <div id="validationErr">
                    <script>setTimeout("hideErrDiv('validationErr')", 3000);</script>
                    <p><?php echo validation_errors(); ?></p>
                </div>
            <?php } ?>
            <script src="js/jquery.growl.js" type="text/javascript"></script>
            <link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />
			<?php if($this->session->flashdata('sErrMSG') != '') { ?>
			<script type="text/javascript">
			var admin_error='<?php if ($this->lang->line('admin_error') != '') echo stripslashes($this->lang->line('admin_error')); else echo 'Error'; ?>';
			var Success='<?php if ($this->lang->line('admin_success') != '') echo stripslashes($this->lang->line('admin_success')); else echo 'Success'; ?>';
			  <?php 
			  $sErrMSGdecoded = base64_decode($this->session->flashdata('sErrMSG'));
			  if($this->session->flashdata('sErrMSGType')=='message-red'){ ?>
			  $.growl.error({ title:admin_error,message: "<?php echo  $sErrMSGdecoded;  ?>" });
			  <?php } ?>
			  <?php if($this->session->flashdata('sErrMSGType')=='message-green'){ ?>
			  $.growl.notice({ title:Success,message: "<?php echo  $sErrMSGdecoded;  ?>"});
			  <?php } ?>
			  <?php if($this->session->flashdata('sErrMSGType')=='warning'){ ?>
			  $.growl.warning({ message: "<?php echo  $sErrMSGdecoded;  ?>" });
			  <?php } ?>
			</script>
			<?php } ?>

            <input type="hidden" id="tabValidator" value="Yes"/>
			<script>
			var admin_ride_pickup_location='<?php if ($this->lang->line('admin_rides_pickup_location') != '') echo stripslashes($this->lang->line('admin_rides_pickup_location')); else echo 'Pickup Location'; ?>';
			var admin_ride_drop_location='<?php if ($this->lang->line('admin_rides_drop_location') != '') echo stripslashes($this->lang->line('admin_rides_drop_location')); else echo 'Drop Location'; ?>';
			var admin_ride_payment_by='<?php if ($this->lang->line('admin_ride_payment_by') != '') echo stripslashes($this->lang->line('admin_ride_payment_by')); else echo 'Payment by'; ?>';
			</script>