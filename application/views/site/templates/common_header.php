<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php if ($sideMenu == 'share_code') { ?>
            <?php /* <meta property="og:site_name" content="<?php echo $this->config->item('email_title'); ?>"/> */ ?>
            <meta property="og:type" content="website"/>
            <meta property="og:url" content="<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"/>
            <meta property="og:title" content="Signup with my code."/>
            <?php if($this->config->item('facebook_image')!='') {?>
            <meta property="og:image" content="<?php echo base_url() . 'images/logo/'.$this->config->item('facebook_image'); ?>"/>
            <?php } else {?>
            <meta property="og:image" content="<?php echo base_url() . 'images/logo/'.$this->config->item('logo_image'); ?>"/>
            <?php }?>
            <meta property="og:image:width" content="100" />
            <meta property="og:image:height" content="100" />
            <meta property="og:description" content="<?php echo $shareDesc; ?>"/>
        <?php } ?>
        <meta name="Title" content="<?php if ($meta_title == '') { echo $this->config->item('meta_title'); } else { echo $meta_title; } ?>" />  
        <base href="<?php echo base_url(); ?>" />
        <?php
        if ($this->config->item('google_verification')) {
            echo stripslashes($this->config->item('google_verification'));
        }
        
        if ($heading == '') { ?>    
            <title><?php echo $title; ?></title>
        <?php } else { ?>
            <title><?php echo $heading; ?></title>
        <?php } ?>

        <meta name="Title" content="<?php if ($meta_title == '') { echo $this->config->item('meta_title'); } else { echo $meta_title; } ?>" />
        <meta name="keywords" content="<?php if ($meta_keyword == '') { echo $this->config->item('meta_keyword'); } else { echo $meta_keyword; } ?>" />
        <meta name="description" content="<?php if ($meta_description == '') { echo $this->config->item('meta_description'); } else { echo $meta_description; } ?>" />
		<?php
		if (isset($meta_abstraction)){
		  if ($meta_abstraction == '') {
			  echo "<!-- " . $this->config->item('meta_abstraction') . " --><cmt>";
		  } else {
			  echo "<!-- " . $meta_abstraction . " --><cmt>";
		  }
		}
		?>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url() . 'images/logo/' . $this->config->item('favicon_image'); ?>">    

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
        var ride_transaction='<?php if ($this->lang->line('ride_transaction_proceed') != '') echo stripslashes($this->lang->line('ride_transaction_proceed')); else echo 'Please Wait... Your Transaction Being Processed'; ?>';
        var ride_recharge_amount='<?php if ($this->lang->line('ride_enter_recharge_amount') != '') echo stripslashes($this->lang->line('ride_enter_recharge_amount')); else echo 'Please enter recharge amount'; ?>';
        var ride_recharge_amount_number='<?php if ($this->lang->line('ride_enter_recharge_amount_number') != '') echo stripslashes($this->lang->line('ride_enter_recharge_amount_number')); else echo 'Recharge amount should be a number'; ?>';
        var ride_amount_between='<?php if ($this->lang->line('ride_amount_between') != '') echo stripslashes($this->lang->line('ride_amount_between')); else echo 'Recharge amount should be between'; ?>';
		</script>
	
            <?php
            $this->load->view('site/templates/css_files', $this->data);
            $this->load->view('site/templates/script_files', $this->data);
            ?>

	<script src="js/jquery.growl.js" type="text/javascript"></script>
	<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />
	<?php if ($this->session->flashdata('sErrMSG') != '') { ?>
		<div></div>
		<script type="text/javascript">
	   
		var ErrorFlsh='<?php if ($this->lang->line('admin_error') != '') echo stripslashes($this->lang->line('admin_error')); else echo 'Error'; ?>';
		var SuccessFlsh='<?php if ($this->lang->line('admin_success') != '') echo stripslashes($this->lang->line('admin_success')); else echo 'Success'; ?>';
		<?php
		$sErrMSGdecoded = base64_decode($this->session->flashdata('sErrMSG'));
		if ($this->session->flashdata('sErrMSGType') == 'message-red') {
		?>
			$.growl.error({title: ErrorFlsh, message: "<?php echo $sErrMSGdecoded; ?>"});
		<?php } ?>
		<?php if ($this->session->flashdata('sErrMSGType') == 'message-green') { ?>
			$.growl.notice({title: SuccessFlsh, message: "<?php echo $sErrMSGdecoded; ?>"});
		<?php } ?>
		<?php if ($this->session->flashdata('sErrMSGType') == 'warning') { ?>
			$.growl.warning({message: "<?php echo $sErrMSGdecoded; ?>"});
		<?php } ?>
		</script>
	<?php } ?>
	
	
	<?php 
	if(isset($billing_job)){
		if($billing_job=='Yes'){ 
	?>
		<script>
			$.ajax({
				type:'post',
				url:'<?php echo base_url(); ?>generate-billing',
				data:{},
				complete:function(){
					//console.log('success');
				}
			});
		</script>
	<?php 
		}
	} 
	?>
	
