$(document).ready(function(){
	/* Validate chosen select */
	$.validator.setDefaults({ ignore: ":hidden:not(.admin_cancel_ride_chosen)" });
	
	$('.checkboxCon input:checked').parent().css('background-position','-114px -260px');
	$("#selectallseeker").click(function () {
          $('.caseSeeker').attr('checked', this.checked);
          if(this.checked){
        	  $(this).parent().addClass('checked');
        	  $('.checkboxCon').css('background-position','-114px -260px');
          }else{
        	  $(this).parent().removeClass('checked');
        	  $('.checkboxCon').css('background-position','-38px -260px');
          }
    });
 
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $(".caseSeeker").click(function(){
        if($(".caseSeeker").length == $(".caseSeeker:checked").length) {
            $("#selectallseeker").attr("checked", "checked");
            $("#selectallseeker").parent().addClass("checked");
        } else {
            $("#selectallseeker").removeAttr("checked");
            $("#selectallseeker").parent().removeClass("checked");
        }
    });
    
    $('.checkboxCon input').click(function(){
    	if(this.checked){
      	  $(this).parent().css('background-position','-114px -260px');
        }else{
      	  $(this).parent().css('background-position','-38px -260px');
        }
    });
	
});

	
function checkBoxValidationUser(req,AdmEmail) {	
	var tot=0;
	var chkVal = 'on';
	var frm = $('#seekerActionForm input');
	for (var i = 0; i < frm.length; i++){
		if(frm[i].type=='checkbox') {
			if(frm[i].checked) {
				tot=1;
				if(frm[i].value != 'on'){
					chkVal = frm[i].value;
				}
			}
		}
	}
	if(tot == 0) {
			alert(admin_checkBoxvalidationadmin);
			return false;
	}else if(chkVal == 'on') {
			alert(admin_no_records_found);
			return false;  
	
	} else {
		var didConfirm = confirm(admin_checkboxvalidationuser);
		  if (didConfirm == true) {
			$('#statusMode').val(req);
			$('#seekerActionForm').submit();
		  }else{
				return false;  
		  }		
	} 
}

function checkBoxValidationAdmin(req,AdmEmail) {
//alert(admin_checkBoxvalidationadmin);
	var tot=0;
	var chkVal = 'on';
	var frm = $('#display_form input');
	for (var i = 0; i < frm.length; i++){
		if(frm[i].type=='checkbox') {
			if(frm[i].checked) {
				tot=1;
				if(frm[i].value != 'on'){
					chkVal = frm[i].value;
				}
			}
		}
	}
	if(tot == 0) {
			alert(admin_checkBoxvalidationadmin);
			return false;
	}else if(chkVal == 'on') {
			alert(admin_no_records_found);
			return false;  
	} else {
		confirm_global_status(req,AdmEmail);
	} 
		
}
function checkBoxWithSelectValidationAdmin(req,AdmEmail) {	
	var templat = $('#mail_contents').val();
	if(templat==''){
		alert(admin_select_mail_tempolate);
		return false;
	}
	var tot=0;
	var chkVal = 'on';
	var frm = $('#display_form input');
	for (var i = 0; i < frm.length; i++){
		if(frm[i].type=='checkbox') {
			if(frm[i].checked) {
				tot=1;
				if(frm[i].value != 'on'){
					chkVal = frm[i].value;
				}
			}
		}
	}
	if(tot == 0) {
			alert(admin_checkBoxvalidationadmin);
			return false;
	}else if(chkVal == 'on') {
			alert(admin_no_records_found);
			return false;  
	
	} else {
		confirm_global_status(req,AdmEmail);
	} 
}

function SelectValidationAdmin(req,AdmEmail) {	
	var templat = $('#mail_contents').val();
	if(templat==''){
		alert(admin_select_mail_tempolate);
		return false;
	}
	confirm_global_status(req,AdmEmail);
}

function confirm_global_status(req,AdmEmail){

  

 	$.confirm({
 		'title'		: Confirmation,
 		'message'	: admin_checkboxvalidationuser,
 		'buttons'	: {
 			'Yes': {
 				'class'	: 'yes',
 				'action': function(){
					bulk_logs_action(req,AdmEmail);
 				},
                'title' : Yes
 			},
 			'No'	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				},
                'title' : No            // Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }
 
//Bulk Active, Inactive, Delete Logs 
function bulk_logs_action(req,AdmEmail){ 
	var perms=prompt(security_purpose);
	if(perms==''){ 
			alert(admin_common_enter_email_id);
			return false;
	}else if(perms==null){	
			return false;
	}else{ 
		if(perms==AdmEmail){
				$('#statusMode').val(req);
				$('#SubAdminEmail').val(AdmEmail);				
		 		$('#display_form').submit();
		}else{
				alert(admin_common_correct_email_id);
				return false;	
		}
	}
}

 
//confirm status change
function confirm_status(path){
 	$.confirm({
 		'title'		: Confirmation,
 		'message'	: admin_common_change_status_record,
 		'buttons'	: {
 			Yes	: {
 				'class'	: 'yes',
 				'action': function(){
 					window.location = BaseURL+path;
 				},
                'title' : Yes
 			},
 			No	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				},
               'title' : No
                // Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }			
 
//confirm mode change
function confirm_mode(path){
	$.confirm({
		'title'		: Confirmation,
		'message'	: admin_change_mode_record,
		'buttons'	: {
			Yes	: {
				'class'	: 'yes',
				'action': function(){
					window.location = BaseURL+path;
				},
                'title' : Yes
			},
			No	: {
				'class'	: 'no',
				'action': function(){
					return false;
				},
               'title' : No
                // Nothing to do in this case. You can as well omit the action property.
			}
		}
	});
}			
function confirm_delete(path){
 	$.confirm({
 		'title'		: security_delete,
 		'message'	: admin_delete_record_restore_later,
 		'buttons'	: {
 			Yes	: {
 				'class'	: 'yes',
 				'action': function(){
 					window.location = BaseURL+path;
 				},
                'title' : Yes
 			},
 			No	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				},
                'title' : No
                // Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }	 
 
//Category Add Function
function checkBoxCategory() {
	var tot=0;
	var chkVal = 'on';
	var frm = $('#display_form input');
	for (var i = 0; i < frm.length; i++){
		if(frm[i].type=='checkbox') {
			if(frm[i].checked) {
				tot=1;
				chkVal = frm[i].value;
			}
		}
	}
		if(tot == 0) {
				alert(admin_checkBoxvalidationadmin);
				return false;
		}else if(tot > 1){
				alert(admin_select_only_one_checkbox);
				return false;
		}else if(chkVal == 'on') {
				alert(admin_no_records_found);
				return false;  
		
		} else {
			confirm_category_checkbox(chkVal);
		} 
		
}

//Category Checkbox Confirmation
function confirm_category_checkbox(chkVal){
 	$.confirm({
 		'title'		: 'Confirmation',
 		'message'	: admin_checkboxvalidationuser,
 		'buttons'	: {
 			Yes	: {
 				'class'	: 'yes',
 				'action': function(){
					$('#checkboxID').val(chkVal);
 					$('#display_form').submit();
 				},
                'title' : Yes
 			},
 			No	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				},
                'title' : No
                // Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }
 
function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
   return regex.test(email);
}

function validateAlphabet(value) {
         var regexp = /^[a-zA-Z ]*$/;
         return regexp.test(value);
}

function hideErrDiv(arg) {
	 $("#"+arg).hide("slow");
}

//confirm status change
function confirm_status_dashboard(path){
 	$.confirm({
 		'title'		: Confirmation,
 		'message'	: admin_common_change_status_record,
 		'buttons'	: {
 			Yes	: {
 				'class'	: 'yes',
 				'action': function(){
 					window.location = BaseURL+'admin/dashboard/admin_dashboard';
 				},
                'title' : Yes
 			},
 			No	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				},
                'title' : No
                // Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }			
 
function changeCatPos(evt,catID){
	var pos = $(evt).prev().val();
	if((pos-pos) != 0){
		alert('Invalid position');
		return;
	}else{
		$(evt).hide();
		$(evt).next().show();
		$.ajax({
			type:'post',
			url:baseURL+'admin/category/changePosition',
			data:{'catID':catID,'pos':pos},
			complete:function(){
				$(evt).next().hide();
				$(evt).show().text('Done').delay(800).text('Update');
			}
		});
	}
}

// for controlling checkboxs
var checked = false;
function checkBoxController(frm,names,search_mode) {
	if (checked == false){
			checked = true;
		}else {
	         checked = false;
		} 
	for (var i = 0; i < frm.elements.length; i++){
		if(frm.elements[i].name  == names){
		frm.elements[i].checked=checked;
		}
	}
}

function viewTrips(){
	/* var mfrom=$('#datefrom').val();
	var mto=$('#dateto').val();
	if(mfrom==null || mfrom=="" || mto==null || mto==""){
		alert('Select the month range.');
	}else{
		window.location.href=baseURL+"admin/revenue/display_site_revenue?from="+encodeURIComponent(mfrom)+'&to='+encodeURIComponent(mto);
	} */
	var date_range=$('#date_range').val();
	/* if(date_range==null || date_range==""){
		alert('Select the month range.');
	}else{
		date_range = btoa(date_range);
		window.location.href=baseURL+"admin/revenue/display_site_revenue?range="+encodeURIComponent(date_range);
	} */
	date_range = btoa(date_range);
	window.location.href=baseURL+"admin/revenue/display_site_revenue?range="+encodeURIComponent(date_range);
}
function viewoverallTrips(){
	window.location.href=baseURL+"admin/revenue/display_site_revenue";
}


function  email_subscription(){
	var subscriber_name = $('#subscriber_name').val();
	var subscriber_email = $('#subscriber_email').val();
	var chk = 0;
	
	if(subscriber_name == ''){
		$( "#subscriber_name" ).effect( "shake" );
		chk++;
	}
	
	if(subscriber_email == ''){
		$( "#subscriber_email" ).effect( "shake" );
		chk++;
	} else if(!IsEmail(subscriber_email)){
		$( "#subscriber_email" ).effect( "shake" );
		chk++;
	}
	
	if(chk == 0){
		$('#subscribe_btn').val('Please Wait...');
		$.ajax({
			type:'post',
			url:'site/user/email_subscription',
			data:{'subscriber_name':subscriber_name,'subscriber_email':subscriber_email},
			dataType: 'json',
			success:function(res){
				if(res.msg == 'Success'){ 
					$('#subscribeMsg').html('<p style="color:green;">Thanks for subscribing with us</p>');
				} else if(res.msg == 'Exist'){
					$('#subscribeMsg').html('<p style="color:red;">You have already subscribed</p>');
				} else {
					$('#subscribeMsg').html('<p style="color:red;">Sorry, Please try again</p>');
				}
				$('#subscribe_btn').val('Submit');
			}
		});
	}
}

function otpValidation(){ 
	var em_mobile_otp = $('#em_mobile_otp').val();
	if(em_mobile_otp != ''){
		$('#confirm_emergency_contact_form').submit();
	} else {
		$('#otpErrMsg').html('Please enter the otp you have received.');
	}
}

$(document).ready(function(){
    $(".money_bucket").click(function(){
		var bucket_id = $(this).attr('id'); 
		var bucket_money = $('#'+bucket_id).attr('data-bucket'); 
		$('#total_amount').val(bucket_money);
    });
});

function wallet_payment_amt_validate(paymode){
	var wamt = $('#total_amount').val();
	var wal_max_amount = $('#wal_recharge_max_amount').val(); 
	var wal_min_amount = $('#wal_recharge_min_amount').val();
	var auto_charge_status = $('#auto_charge_status').val();
	var errChk = 0;
	if(wamt == '' || wamt === null){ 
		$('#Wallet_money_err').html(ride_recharge_amount);
		errChk++;
		return false;
	} else {
		$('#Wallet_money_err').html('');
	}
	
	if(isNaN(wamt)){ 
		$('#Wallet_money_err').html(ride_recharge_amount_number);
		errChk++;
		return false;
	} else {
		$('#Wallet_money_err').html('');
	}
	
	if(Number(wamt) > Number(wal_max_amount)){
		$('#Wallet_money_err').html(ride_amount_between+wal_min_amount+' - '+wal_max_amount);
		errChk++;
	} else if(Number(wamt) < Number(wal_min_amount)){
		$('#Wallet_money_err').html(ride_amount_between+wal_min_amount+' - '+wal_max_amount);
		errChk++;
	} else {
		$('#Wallet_money_err').html('');
	}
	
	if(errChk == 0){
		$('#wallet_recharge_form').submit();
		if(paymode == 'auto'){
		
			$('#payBtn').html(ride_transaction+'<br/><img src="images/loader.gif" style="margin-top:8px;">');
		}
	}
	
}

$(document).ready(function(){
	$(".Vehicle_Number_Chk").blur(function(){ 
		var vehicle_number = $(this).val();
		var driver_id = $("#driver_id").val();
		if(vehicle_number !=null && vehicle_number != ''){
			$('#vehicle_number_exist').html('Checking number...');
			$.ajax({
				type:'post',
				url:'site/cms/check_number',
				data:{'vehicle_number':vehicle_number,'driver_id':driver_id},
				dataType: 'json',
				success:function(res){
					$('#vehicle_number_exist').show();
					if(res.status == '1'){
						$('#vehicle_number_exist').html(res.message);
						$(".Vehicle_Number_Chk").val('');
						return false;
					} else {
						$('#vehicle_number_exist').html('');
						$('#vehicle_number_exist').hide();
					}
				}
			});
		}
	});
});

$("form").submit(function() {
	$("input[type=text],textarea").each(function(){
		$input = $(this).val();
		$new_input = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $input);
		$(this).val($new_input);
	});
	return false;
});

function viewTripsManal(){
	var mfrom=$('#billFromdate').val();
	var mto=$('#billTodate').val();
	var locationFilter=$('#locationFilter').val();
	if(mfrom==null || mfrom=="" || mto==null || mto==""){
		alert('Select the month range.');
	}else{ 
		window.location.href=baseURL+"admin/revenue/display_site_revenue?from="+encodeURIComponent(mfrom)+'&to='+encodeURIComponent(mto)+'&location_id='+locationFilter;
	} 	
}

$( document ).ready(function() {
	$('#locationFilter').click(function(){
		$('#locationFilter').change(function(){
			var mfrom=$('#billFromdate').val();
			var mto=$('#billTodate').val();
			var locationFilter=$('#locationFilter').val();
			window.location.href=baseURL+"admin/revenue/display_site_revenue?from="+encodeURIComponent(mfrom)+'&to='+encodeURIComponent(mto)+'&location_id='+locationFilter;
		});
	});
});