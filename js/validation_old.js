$(document).ready(function(){
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
 
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
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
	
	$(".popup-signup-ajax").click(function()
   {
	   //alert(baseURL);return false;
	   $.ajax(
		{
			type: 'POST',
			url: baseURL+'googlelogin/index.php',
			data:{},
			success: function(data) 
			{
				//location.reload();
				//alert('sss');
				//$("#popupCheckId").val('1');
				$("#popup_container").css("display","block");
			}
			
		});
   });
	
	/**
	 * Menu notifications hover
	 * 
	 */
	$('.gnb-notification').mouseenter(function(){
		if($(this).hasClass('cntLoading'))return;
		$(this).addClass('cntLoading');
		$('.feed-notification').show();
		$('.feed-notification').find('ul').remove();
		$(this).find('.loading').show();
		$.ajax({
			type:'post',
			url	: baseURL+'site/notify/getlatest',
			dataType: 'json',
			success: function(json){
				if(json.status_code == 1){
					$('.feed-notification').find('.loading').after(json.content);
					$('.moreFeed').show();
				}else if(json.status_code == 2){
					$('.feed-notification').find('.loading').after(json.content);
					$('.moreFeed').hide();
				}
			},
			complete:function(){
				$('.gnb-notification').find('.loading').hide();
				$('.gnb-notification').removeClass('cntLoading');
			}
		});
	}).mouseleave(function(){
		$('.feed-notification').hide();
	});
	

/*************Common validaion function*******************/
$("#fullname,#username,#email,#user_password,#pass,#confirmpass,#lastname").keypress(function(){
	$("#"+this.id+"Err").html('');
	$("#"+this.id+"Err").hide();
});
				
$("#fullname,#username,#email,#user_password,#pass,#confirmpass,#lastname").blur(function(){
	$("#"+this.id+"Err").html('');
	$("#"+this.id+"Err").hide();
});

//confirm order status change
	$(".changeShipstatus").change(function(){
		var dealCodeNumber=$(this).attr('data-val-id');
		var shipping_status=$(this).val();
		$.ajax({
			type:'post',
			url	: baseURL+'admin/order/order_update',
			dataType: 'html',			
			data:{'dealCodeNumber':dealCodeNumber,'shipping_status':shipping_status},
			success: function(response){
				window.location.reload();
				/*if(response== 'Success'){
					window.location.reload();
				}*/
			}
		});
	});	


//confirm order status change
	$(".changeShipstatusShop").change(function(){
		var dealCodeNumber=$(this).attr('data-val-id');
		var shipping_status=$(this).val();
		$.ajax({
			type:'post',
			url	: baseURL+'site/shop/shoporder_update',
			dataType: 'html',			
			data:{'dealCodeNumber':dealCodeNumber,'shipping_status':shipping_status},
			success: function(response){
				window.location.reload();
			}
		});
	});	
	
	
	
	$(".changePaymentStatusOrder").change(function(){
		var dealCodeNumber=$(this).attr('data-val-id');
		var shipping_status=$(this).val();
		$.ajax({
			type:'post',
			url	: baseURL+'site/shop/payment_status',
			dataType: 'html',			
			data:{'dealCodeNumber':dealCodeNumber,'payment_status':shipping_status},
			success: function(response){
				window.location.reload();
			}
		});
	});	
	
	$(".changePaymentStatusOrder1").change(function(){
		var dealCodeNumber=$(this).attr('data-val-id');
		var shipping_status=$(this).val();
		$.ajax({
			type:'post',
			url	: baseURL+'admin/order/order_payupdate',
			dataType: 'html',			
			data:{'dealCodeNumber':dealCodeNumber,'payment_status':shipping_status},
			success: function(response){
				window.location.reload();
			}
		});
	});	
	
	
	
	$("#postcmt").keypress(function(){
		$("#"+this.id).removeClass('errors');
	});
	$("#postcomment").click(function(){
		var orderid=$(this).attr('data-val-id');
		var post_message=$('#postcmt').val();
		var buyerid=$('#buyerid').val();
		var sellerid=$('#sellerid').val();
		$('#postcmt').removeClass('errors');
		if(post_message==''){
			$('#postcmt').addClass('errors');
			return false;
		}else{
			$('#postcmt').removeClass('errors');	
			$('#postLoading').show();
			$.ajax({
				type:'post',
				url	: baseURL+'site/order/postcomment',
				dataType: 'html',			
				data:{'orderid':orderid,'post_message':post_message,'buyerid':buyerid,'sellerid':sellerid},
				success: function(response){
					if(response!=''){
						//$("#comments tr:first").before(response);
						$('#postcmt').val('');
						var arr = response.split('|<^>|');
						$("#comments").html(arr[0]);
						$("#totalCmt").html('('+arr[1]+')');
						$('#postLoading').hide();
					}
					//alert(response);
				}
			});
		}
	});	
	
	$("#postclaim").click(function(){
		var orderid=$(this).attr('data-val-id');
		var post_message=$('#postcmt').val();
		var buyerid=$('#buyerid').val();
		var sellerid=$('#sellerid').val();
		var grand_total=$('#grand_total').val();
		//alert(grand_total); 
		$('#postcmt').removeClass('errors');
		if(post_message==''){
			$('#postcmt').addClass('errors');
			return false;
		}else{
			$('#postcmt').removeClass('errors');	
			$('#postLoading').show();
			$.ajax({
				type:'post',
				url	: baseURL+'site/order/postclaim',
				dataType: 'html',			
				data:{'orderid':orderid,'post_message':post_message,'buyerid':buyerid,'sellerid':sellerid,'grand_total':grand_total},
				success: function(response){
					if(response!=''){
						//$("#comments tr:first").before(response);
						$('#postcmt').val('');
						var arr = response.split('|<^>|');
						$("#comments").html(arr[0]);
						$("#totalCmt").html('('+arr[1]+')');
						$('#postLoading').hide();
						window.location.reload();						
					}
					//alert(response); return false;
				}
			});
		}
	});	
	
/* Dispute Files Upload Starts here*/
	$(document).ready(function(e) {	
		$("#file_upload_attach").change(function(e) {		
			var filecount=parseInt($('#filecount').html());
			if(filecount>0){
			$("#loadedFile").css("display", "block");
			var formData = new FormData($(this).parents('form').serialize());
			//var formData = new FormData($('#attach_image'));
			alert(formData); return false;
			$.ajax({
				beforeSend: function(){
					document.getElementById("loadedFile").src='images/loading.gif';
				},
				url: 'site/order/ajax_load_Files',
				type: 'POST',
				xhr: function() {
					var myXhr = $.ajaxSettings.xhr();
					return myXhr;
				},
				success: function (datas) {
					//alert(datas); return false;
					//load_ajax_DigiFiles_list
					//
					$.get('site/order/load_ajax_DigiFiles_list/'+datas, function(data) {
						$("#DigiFiles tr:last").after(data); 
					});	
					$("#loadedFile").css("display", "none");
					$('#filecount').html(filecount-1);				
				},
				data: formData,
				cache: false,
				contentType: false,
				processData: false			
				});			
			}
			else
			{alert('Maximum five files are Allowed to upload');}
		});
		return false;	
	});
/* Dispute Files Upload Ends here*/
	
	$("#closedclaim").click(function(){
		var orderid=$(this).attr('data-val-id');
		var post_message=$('#postcmt').val();
		var buyerid=$('#buyerid').val();
		var sellerid=$('#sellerid').val();
		var grand_total=$('#grand_total').val();						
		
		$('#postcmt').removeClass('errors');
		if(post_message==''){
			$('#postcmt').addClass('errors');
			return false;
		} else {
			$('#postcmt').removeClass('errors');	
			$('#postLoading').show();
			/* $('input[name="file_upload_attach[]"]').change(function(){
				var fileNames = $('input[name="file_upload_attach[]"]').map(function(){return $(this).val();}).get();
				console.log(fileNames);
				//alert(fileNames); return false;
			}) */			
			$.ajax({		
				type:'post',
				url	: baseURL+'site/order/closedclaim',
				dataType: 'html',			
				//data:{'orderid':orderid,'post_message':post_message,'buyerid':buyerid,'sellerid':sellerid,'grand_total':grand_total},
				data:{'orderid':orderid,'post_message':post_message,'buyerid':buyerid,'sellerid':sellerid,'grand_total':grand_total},				
				success: function(response){
					if(response!='') {
						//$("#comments tr:first").before(response);
						$('#postcmt').val('');
						var arr = response.split('|<^>|');
						$("#comments").html(arr[0]);
						$("#totalCmt").html('('+arr[1]+')');
						$('#postLoading').hide();	
						window.location.reload();						
					}
					//alert(response); return false;
				}								
			});
		}
	});	
	
});

function message_validation() {
	if(document.getElementById('postcmt').value=="") {
		alert("Please enter your comments");
		document.getElementById('postcmt').focus();
		return false;
	}
}
	
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
			alert("Please Select the CheckBox");
			return false;
	}else if(chkVal == 'on') {
			alert("No records found ");
			return false;  
	
	} else {
		var didConfirm = confirm("Whether you want to continue this action?");
		  if (didConfirm == true) {
			$('#statusMode').val(req);
			$('#seekerActionForm').submit();
		  }else{
				return false;  
		  }		
	} 
		
}

function checkBoxValidationAdmin(req,AdmEmail) {	
	
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
			alert("Please Select the CheckBox");
			return false;
	}else if(chkVal == 'on') {
			alert("No records found ");
			return false;  
	
	} else {
		confirm_global_status(req,AdmEmail);
	} 
		
}
function checkBoxWithSelectValidationAdmin(req,AdmEmail) {	
	var templat = $('#mail_contents').val();
	if(templat==''){
		alert("Please select the mail template");
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
			alert("Please Select the CheckBox");
			return false;
	}else if(chkVal == 'on') {
			alert("No records found ");
			return false;  
	
	} else {
		confirm_global_status(req,AdmEmail);
	} 
		
}
function SelectValidationAdmin(req,AdmEmail) {	
	var templat = $('#mail_contents').val();
	if(templat==''){
		alert("Please select the mail template");
		return false;
	}
	
	confirm_global_status(req,AdmEmail);
	 
		
}
function confirm_global_status(req,AdmEmail){
 	$.confirm({
 		'title'		: 'Confirmation',
 		'message'	: 'Whether you want to continue this action?',
 		'buttons'	: {
 			'Yes'	: {
 				'class'	: 'yes',
 				'action': function(){
					bulk_logs_action(req,AdmEmail);
 					//$('#statusMode').val(req);
 					//$('#display_form').submit();
 				}
 			},
 			'No'	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				}	// Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }
 
//Bulk Active, Inactive, Delete Logs created by siva
function bulk_logs_action(req,AdmEmail){
	
	
	var perms=prompt("For Security Purpose, Please Enter Email Id");
	if(perms==''){
			alert("Please Enter The Email ID");
			return false;
	}else if(perms==null){	
			return false;
	}else{ 
		if(perms==AdmEmail){
				$('#statusMode').val(req);
				$('#SubAdminEmail').val(AdmEmail);				
		 		$('#display_form').submit();
		}else{
				alert("Please Enter The Correct Email ID");
				return false;	
		}
	}

	
	
}

 
//confirm status change
function confirm_status(path){
 	$.confirm({
 		'title'		: 'Confirmation',
 		'message'	: 'You are about to change the status of this record ! Continue?',
 		'buttons'	: {
 			'Yes'	: {
 				'class'	: 'yes',
 				'action': function(){
 					window.location = BaseURL+path;
 				}
 			},
 			'No'	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				}	// Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }			
//confirm mode change
function confirm_mode(path){
	$.confirm({
		'title'		: 'Confirmation',
		'message'	: 'You are about to change the display mode of this record ! Continue?',
		'buttons'	: {
			'Yes'	: {
				'class'	: 'yes',
				'action': function(){
					window.location = BaseURL+path;
				}
			},
			'No'	: {
				'class'	: 'no',
				'action': function(){
					return false;
				}	// Nothing to do in this case. You can as well omit the action property.
			}
		}
	});
}			
function confirm_delete(path){
 	$.confirm({
 		'title'		: 'Delete Confirmation',
 		'message'	: 'You are about to delete this record. <br />It cannot be restored at a later time! Continue?',
 		'buttons'	: {
 			'Yes'	: {
 				'class'	: 'yes',
 				'action': function(){
 					window.location = BaseURL+path;
 				}
 			},
 			'No'	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				}	// Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }	
 
 
//Category Add Function By Siva 
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
				alert("Please Select the CheckBox");
				return false;
		}else if(tot > 1){
				alert("Please Select only one CheckBox at a time");
				return false;
		}else if(chkVal == 'on') {
				alert("No records found ");
				return false;  
		
		} else {
			confirm_category_checkbox(chkVal);
		} 
		
}

//Category Checkbox Confirmation
function confirm_category_checkbox(chkVal){
 	$.confirm({
 		'title'		: 'Confirmation',
 		'message'	: 'Whether you want to continue this action?',
 		'buttons'	: {
 			'Yes'	: {
 				'class'	: 'yes',
 				'action': function(){
					$('#checkboxID').val(chkVal);
 					$('#display_form').submit();
 				}
 			},
 			'No'	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				}	// Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }

/**
 * 
 * Change the seller request status
 * @param val	-> status
 * @param sid	-> seller request id
 */
function changeSellerStatus(sid,uid){
	val = $('#seller_status_'+sid).val();
	if(val != '' && sid != ''){
		$.ajax(
	    {
	        type: 'POST',
	        url: 'admin/seller/change_seller_request',
	        data: {"id": sid,'status': val,'user_id': uid},
	        dataType: 'json',
	        success: function(json)
	        {
	            alert(json);
	        }
	    });
	}
}

function disableGiftCards(path,mail){
	$.confirm({
 		'title'		: 'Confirmation',
 		'message'	: 'You are about to change the mode of giftcards ! Continue?',
 		'buttons'	: {
 			'Yes'	: {
 				'class'	: 'yes',
 				'action': function(){
 					var perms=prompt("For Security Purpose, Please Enter Email Id");
 					if(perms==''){
 							alert("Please Enter The Email ID");
 							return false;
 					}else if(perms==null){	
 							return false;
 					}else{ 
 						if(perms==mail){
 							window.location = BaseURL+path;
 						}else{
 								alert("Please Enter The Correct Email ID");
 								return false;	
 						}
 					}
 				}
 			},
 			'No'	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				}	// Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
}

function editPictureProducts(val,imgId){

	var id = 'img_'+val;
	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);
	$.ajax(
		    {
		        type: 'POST',
		        url: BaseURL+'admin/product/editPictureProducts',
		        data: {"id": id,'cpage': sPage,'position': val,'imgId':imgId},
		        dataType: 'json',
		        success: function(response)
		        {
		        	if(response == 'No') {
						alert("You can't delete all the images");
						return false;
					  } else {
							  $('#img_'+val).remove();
					  }
		        }
		    });
}

function editPictureProductsUser(val,imgId){
	
	var id = 'img_'+val;
	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);
	$.ajax(
			{
				type: 'POST',
				url: BaseURL+'site/product/editPictureProducts',
				data: {"id": id,'cpage': sPage,'position': val,'imgId':imgId},
				dataType: 'json',
				success: function(response)
				{
					if(response == 'No') {
						alert("You can't delete all the images");
						return false;
					} else {
						$('#img_'+val).remove();
					}
				}
			});
}

function quickSignup(){
	var dlg_signin = $.dialog('signin-overlay'),
    	dlg_register = $.dialog('register');
	var email = $('#signin-email').val();
	$.ajax({
        type: 'POST',
        url: baseURL+'site/user/quickSignup',
        data: {"email": email},
        dataType: 'json',
        success: function(response)
        {
        	if(response.success == '0') {
				alert(response.msg);
				return false;
			 } else {
			 	$('.quickSignup2 .username').val(response.user_name);
			 	$('.quickSignup2 .url b').text(response.user_name);
			 	$('.quickSignup2 .email').val(response.email);
			 	$('.quickSignup2 .fullname').val(response.full_name);
                dlg_register.open();
			 }
        }
    });
}
function quickSignup2(){
	var username = $('.quickSignup2 .username').val();
	var email = $('.quickSignup2 .email').val();
	var password = $('.quickSignup2 .user_password').val();
	var fullname = $('.quickSignup2 .fullname').val();
	$.ajax({
        type: 'POST',
        url: baseURL+'site/user/quickSignupUpdate',
        data: {"username":username,"email": email,"password":password,"fullname":fullname},
        dataType: 'json',
        success: function(response)
        {
        	if(response.success == '0') {
				alert(response.msg);
				return false;
			 } else {
				 location.href = baseURL+'send-confirm-mail';
			 }
        }
    });
}


function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
   return regex.test(email);
}

function ContactSeller(){

		$('#div_question').html('');
		$('#div_name').html('');
		$('#div_emailaddress').html('');	
		$('#div_phoneNumber').html('');

	var question = $('.contact_frm #question').val();
	var name = $('.contact_frm #name').val();
	var email = $('.contact_frm #emailaddress').val();
	var phone = $('.contact_frm #phoneNumber').val();
	var selleremail = $('.contact_frm #selleremail').val();
	var sellerid = $('.contact_frm #sellerid').val();	
	var product_id = $('.contact_frm #productId').val();

	if(question ==''){
		$('#div_question').html('This field is required');
		return false;
	}else if(name ==''){
		$('#div_name').html('This field is required');
		return false;		
	}else if(email ==''){
		$('#div_emailaddress').html('This field is required');
		return false;		
	}else if( !IsEmail(email)) { 
		$('#div_emailaddress').html('Please Enter Valid Email Address');		
		return false;
	/*}else if(phone ==''){
		$('#div_phoneNumber').html('This field is required');
		return false;*/		
	}else{
		$('#div_question').html('');
		$('#div_name').html('');
		$('#div_emailaddress').html('');	
		$('#div_phoneNumber').html('');

		$('#loadingImgContact').show();
		
		
		$.ajax({
		type: 'POST',   
		 url: baseURL+'site/product/contactform',
		data:{"question":question,"name": name,"email":email,"phone":phone,"selleremail":selleremail,"sellerid":sellerid,"product_id":product_id},
			success:function(response){
				//alert(response);
				if(response == 'Success'){
					
					location.reload();	
					$('#loadingImgContact').hide();
				}
			}
		});
		
	}
}

function UserContactSeller(){

		$('#div_question').html('');
		$('#div_name').html('');
		$('#div_emailaddress').html('');	
		$('#div_phoneNumber').html('');

	var question = $('.user_contact_frm #question').val();
	var name = $('.user_contact_frm #name').val();
	var email = $('.user_contact_frm #emailaddress').val();
	var phone = $('.user_contact_frm #phoneNumber').val();
	var selleremail = $('.user_contact_frm #selleremail').val();
	var sellerid = $('.user_contact_frm #sellerid').val();	
	var product_id = $('.user_contact_frm #productId').val();

	if(question ==''){
		$('#div_question').html('This field is required');
		return false;
	}else if(name ==''){
		$('#div_name').html('This field is required');
		return false;		
	}else if(email ==''){
		$('#div_emailaddress').html('This field is required');
		return false;		
	}else if( !IsEmail(email)) { 
		$('#div_emailaddress').html('Please Enter Valid Email Address');		
		return false;
	/*}else if(phone ==''){
		$('#div_phoneNumber').html('This field is required');
		return false;*/		
	}else{
		$('#div_question').html('');
		$('#div_name').html('');
		$('#div_emailaddress').html('');	
		$('#div_phoneNumber').html('');

		$('#loadingImgContact').show();
		
		
		$.ajax({
		type: 'POST',   
		 url: baseURL+'site/product/usercontactform',
		data:{"question":question,"name": name,"email":email,"phone":phone,"selleremail":selleremail,"sellerid":sellerid,"product_id":product_id},
			success:function(response){
				//alert(response);
				if(response == 'Success'){
					
					location.reload();	
					$('#loadingImgContact').hide();
				}
			}
		});
		
	}
}

function editPicturePetimage(val,imgId){  
	var id = 'img_'+val;
	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);
	$.ajax(
		    {
		        type: 'POST',
		        url: BaseURL+'site/user/deleteImages',
		        data: {"id": id,'cpage': sPage,'position': val,'imgId':imgId},
		        dataType: 'json',
		        success: function(response)
		        {
		        	if(response == 'No') {
						alert("You can't delete all the images");
						return false;
					  } else {
							  $('#img_'+val).remove();
					  }
		        }
		    });
}

function sivarating(){

	document.getElementById('PetVoteRate').innerHTML = '<font color="red">Please Login for feedback</font>' ;	
}

function AddProduct(){
	var shipMethod = $('input:radio[name=shipMethod]:checked').val();
	var category1 = $('#category1').val();
	var category2 = $('#category2').val();
	var category3 = $('#category3').val();
	var store1 = $('#store1').val();
	var store2 = $('#store2').val();
	var store3 = $('#store3').val();
	
	if(category1==''){	var category1 = 0; }else{ var category1 = 1;}
	if(category2==''){	var category2 = 0;}else{ var category2 = 1;}
	if(category3==''){	var category3 = 0;}else{ var category3 = 1;}	
	var CategoryCountVal = category1 + category2 + category3;
	
	if(store1==''){	var store1 = 0; }else{ var store1 = 1;}
	if(store2==''){	var store2 = 0;}else{ var store2 = 1;}
	if(store3==''){	var store3 = 0;}else{ var store3 = 1;}
	var StoreCountVal = store1 + store2 + store3;
	
	var description = $('#description').val();
	var materials = $('#materials').val();
	var pre_shipping = $('#pre_shipping').val();
	var pre_order = $('#pre_order').val();
	var tag =$('#tags_Amt').val();

	var product_name = $('#product_name').val();
	var price = $('#price').val();
	var quantity = $('#quantity').val();
	
	$('#SpecialErr').html('');
	
	
	
	if(CategoryCountVal==0) { 
		$('#category1_Err').html('Choose atlest 1 Category');	
	}else if(StoreCountVal==0) { 
		$('#store1_Err').html('Choose atlest 1 store');	
	}else if(product_name==''){
		$('#product_nameErr').html('Product name required');
	}else if(!$.isNumeric(price)){
		$('#product_priceErr').html('Price required');		
	}else if(!$.isNumeric(quantity)){
		$('#quantity_noErr').html('Quantity required');
	}else if(shipMethod==undefined) { 
		$('#shipMethod_Err').html('Choose shipping method ');	
	}else {
		var brand = 'no';
		if($('.brandSt').is(':checked')){
			brand = 'yes';
		}
		if(response.success == '0') {
					$('#SpecialErr').html(response.msg);
					return false;
				 } else {
					 location.href = baseURL+'';
				 }
	}
	return false;

}

function Addshipping(){
	
	
	var full_name = $('#full_name').val();
	var e = document.getElementById('country');
	var country = e.options[e.selectedIndex].value;
	var nick_name = $('#nick_name').val();
	var address1 = $('#address1').val();
	var city = $('#city').val();
	var state = $('#state').val();
	var postal_code = $('#postal_code').val();
	var phone = $('#phone').val();

	$('#SpecialErr').html('');
	
	
	
	if(country==0) { 
		$('#country_Err').html('This field is required');	
	}else if(full_name==0){
			$('#full_name_Err').html('This field is required');	
	
	}else if(nick_name==0){
			$('#nick_name_Err').html('This field is required');	
	
	}else if(address1==0){
			$('#address1_Err').html('This field is required');	
	
	}else if(city==0){
			$('#city_Err').html('This field is required');	
	
	}else if(state==0){
			$('#state_Err').html('This field is required');	
	
	}else if(!$.isNumeric(postal_code)){
			$('#postal_code_Err').html('This field is required');	
	
	}else if(!$.isNumeric(phone)){
			$('#phone_Err').html('This field is required');	
	
	}else {
		var brand = 'no';
		if($('.brandSt').is(':checked')){
			brand = 'yes';
		}
		if(response.success == '0') {
					$('#SpecialErr').html(response.msg);
					return false;
				 } else {
					 location.href = baseURL+'';
				 }
	}
	return false;

}

function AddFeedback(){
	var title = $('#title').val();

	$('#SpecialErr').html('');
	if(title=='') { 
	
		$('#title_Err').html('This field is required');	
	}else {
		var brand = 'no';
		if($('.brandSt').is(':checked')){
			brand = 'yes';
		}
		if(response.success == '0') {
					$('#SpecialErr').html(response.msg);
					return false;
				 } else {
					 location.href = baseURL+'';
				 }
	}
	return false;

}
function validateSeller_Signup(){
		var seller_businessname = $('#seller_businessname').val();
		var seller_crafting = $('#seller_crafting').val();
		var seller_product = $('#seller_product').val();
		var seller_make = $('#seller_make').val();
		var seller_site = $('#seller_site').val();
		var seller_nda = $('input[name=seller_nda]:checked').val();
		var seller_agreement = $('input[name=seller_agreement]:checked').val();
      //  var pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
      
		$('#SpecialErr').html('');
		
		if(seller_businessname==0){ 
		$('#seller_businessname_Err').html('Business name required');	
		}else if(seller_crafting==0){ 
		$('#seller_crafting_Err').html('This field required');	
		}else if(seller_product==0){ 
		$('#seller_product_Err').html('This field required');	
		}else if(seller_make==0){ 
		$('#seller_make_Err').html('This field required');	
		}/*else if(!pattern.test(seller_site)){ 
		$('#seller_site_Err').html('This field required');	
		}*/else if(seller_nda==undefined){ 
		$('#seller_nda_Err').html('This field required');	
		}else if(seller_agreement==undefined){ 
		$('#seller_agreement_Err').html('This field required');	
	}else {
		var brand = 'no';
		if($('.brandSt').is(':checked')){
			brand = 'yes';
		}
		if(response.success == '0') {
					$('#SpecialErr').html(response.msg);
					return false;
				 } else {
					 location.href = baseURL+'';
				 }
	}
	return false;

}

function subscribe_user()
{
	//alert("hello");
	var email = $('#emailtext').val();
	$('#suscribeemailErr').html('');
	$('#suscribeemailErr').hide()
	if(email==''){
		$('#suscribeemailErr').show()
		$('#suscribeemailErr').html('This field required');
		return false;
	}else if( !IsEmail(email)) { 
		$('#suscribeemailErr').show()
		$('#suscribeemailErr').html('Invalid e-mail address');	
		return false;
	}
	
	/*else {
		$.ajax({
	        type: 'POST',
	        url: baseURL+'site/user/subscribeUser',
	        data: {"email": email},
	        dataType: 'json',
	        success: function(response)
	        {
				//alert(response.success);
	        	if(response.success == 0) {
					$('#SpecialErr').show()
					$('#SpecialErr').html(response.msg);
					//location.href = baseURL+'home';					
					return false;
				 } else {
					 //location.href = baseURL+'send-confirm-mail';
				 }
	        }
	    });
	}*/
	
	return true;
}

function register_user(){
	$('#loadErr').html('<span class="loading"><img src="images/indicator.gif" alt="Loading..."></span>');
	//window.location.href='www.google.com';
	var fullname = $('#fullname').val();
	var lastname = $('#lastname').val();
	var email = $('#email').val();
	var pwd = $('#pwd').val();
	var Confirmpwd = $('#Confirmpwd').val();

	var username = $('#username').val();
	var gender=$('input[type="radio"]:checked').val();
	//var priTerm=$('input[type="checkbox"]:checked').val();
	var priTerm=$('#privacychecking').is(':checked');
	
	if($('#subscription').is(':checked')){
		subscription = 'on';
	}else{
		subscription = 'off';
	}
	
	var status=0;
	
	$('#fullnameErr').html('');
	$('#lastnameErr').html('');
	$('#emailErr').html('');
	$('#user_passwordErr').html('');
	$('#user_ConfirmpasswordErr').html('');
	$('#usernameErr').html('');
	$('#PrivacyErr').html('');
	
	$('#fullnameErr').hide();
	$('#lastnameErr').hide();
	$('#emailErr').hide();
	$('#user_passwordErr').hide();
	$('#user_ConfirmpasswordErr').hide();
	$('#usernameErr').hide();
	$('#PrivacyErr').hide();
	
	$('#SpecialErr').html('');
	if(fullname=='' || !isNaN(fullname)){
		$('#fullnameErr').show();
		$('#fullnameErr').html('This field required (alphabetical only)');
		$('#loadErr').html('');
	}else if(lastname=='' || !isNaN(lastname)){
		$('#lastnameErr').show();
		$('#lastnameErr').html('This field required (alphabetical only)');
		$('#loadErr').html('');
	}
	else if( !IsEmail(email)) { 
		$('#emailErr').show();
		$('#emailErr').html('Invalid e-mail address');	
		$('#loadErr').html('');
	}else if(email==''){
		$('#emailErr').show();
		$('#emailErr').html('This field required');
		$('#loadErr').html('');
	}else if( !IsEmail(email)) { 
		$('#emailErr').show();
		$('#emailErr').html('Invalid e-mail address');	
		$('#loadErr').html('');
	}else if(pwd==''){
		$('#user_passwordErr').show();
		$('#user_passwordErr').html('This field required');
		$('#loadErr').html('');
	}else if(Confirmpwd==''){
		$('#user_ConfirmpasswordErr').show();
		$('#user_ConfirmpasswordErr').html('This field required');
		$('#loadErr').html('');
	}else if(pwd != Confirmpwd)	{
		$('#user_ConfirmpasswordErr').show();
		$('#user_ConfirmpasswordErr').html('password not match');
		$('#loadErr').html('');
	}else if(pwd.length < 6){
		$('#user_passwordErr').show();
		$('#user_passwordErr').html('Password must be minimum of 6 characters');
		$('#loadErr').html('');
	}else if(username==''){
		$('#usernameErr').show();
		$('#usernameErr').html('This field required');	
		$('#loadErr').html('');
	}else if(!priTerm){
		$('#PrivacyErr').show();
		$('#PrivacyErr').html('Please accept our Terms of Use and Privacy Policy');
		$('#loadErr').html('');	
	}else {
		/*Check the email address is already used or no*/		
		$.ajax({
	        type: 'POST',
	        url: baseURL+'site/user/emailExists/',
	        data: {"email":email},
	        success: function(response)
	        {	
	        	if(response=='exist') {
					$('#emailErr').show();
					$('#emailErr').html('This Email id already registerd.');	
					$('#loadErr').html('');
				}else if(response=='new'){
					$.ajax({
						type: 'POST',
						url: baseURL+'site/user/registerUser',
						data: {"fullname":fullname,"username":username,"lastname":lastname,"email": email,"pwd":pwd,"gender":gender,"subscription":subscription},
						dataType: 'json',
						success: function(response)
						{	
							if(response.success == 0) {
								//$('#SpecialErr').html(response.msg);
								if(response.msg=='User name already exists'){
									$('#usernameErr').show();
									$('#usernameErr').html('Username already exists! Choose another');
									$('#loadErr').html('');
									return false;
								}
								if(response.msg=='Email id already exists'){
									$('#emailErr').show();
									$('#emailErr').html('This Email id already registered.');
									$('#loadErr').html('');
									return false;
								}
								window.location.href = baseURL+'register';				
								return false;
							 } else {
								 window.location.href = baseURL+'wpconnect.php?un='+username+'&pd='+pwd+'&em='+email;
							 }
						}
					});
				}
	        },
	    });
	}
	return false;
}

function hideErrDiv(arg) {
	 $("#"+arg).hide("slow");
}
function resendConfirmation(mail){
	if(mail != ''){
		$('.confirm-email').html('<span>Sending...</span>');
		$.ajax({
	        type: 'POST',
	        url: baseURL+'site/user/resend_confirm_mail',
	        data: {"mail": mail},
	        //dataType: 'json',
	        success: function(response){
	        	if(response.success == '0') {
					alert(response.msg);
					return false;
				 } else {
					 //$('#tempp').html(response);
					 $('.confirm-email').html('<font color="green">Confirmation Mail Sent Successfully</font>');
					 $('.confirm-email').removeAttr('onClick'); $('.confirm-email').removeAttr('style');
					 
				 }
	        }
	    });
	}
}
function resendConfirmationPopUp(mail){
	if(mail != ''){
		$.ajax({
	        type: 'POST',
	        url: baseURL+'site/user/resend_confirm_mail',
	        data: {"mail": mail},
	        //dataType: 'json',
	        success: function(response){
	        	if(response.success == '0') {
					alert(response.msg);
					return false;
				 } else {
					  location.href = baseURL+'verify';					 
				 }
	        }
	    });
	}
}
function profileUpdate(){

	
	
	$('#loadingImgProfile').show();
	$('#userErr').html('');
	//$('#save_account').disable();
	var full_name=$('.setting_fullname').val();
	var last_name=$('.setting_lastname').val();

	var paypal_id=$('.setting_paypal_email').val();
	var location=$('.setting_location').val();
	var twitter=$('.setting_twitter').val();
	var facebook=$('.setting_facebook').val();
	var pinterest=$('.setting_pinterest').val();
	var google=$('.setting_google').val();
	var b_year=$('.birthday_year').val();
	var b_month=$('.birthday_month').val();
	var b_day=$('.birthday_day').val();
	var setting_bio=$('.setting_bio').val();
	var email=$('.setting_email').val();
	var age=$('.setting_age').val();
	var gender=$('.setting_gender:checked').val();
			
	$.ajax({
		type: 'POST',
		url: baseURL+'site/user_settings/update_profile',

		data: {"full_name":full_name,"last_name":last_name,"paypal_email":paypal_id,"location":location,"twitter":twitter,"facebook":facebook,"pinterest":pinterest,"google":google,"b_year":b_year,"b_month":b_month,"b_day":b_day,"about":setting_bio,"email":email,"age":age,"gender":gender},
		dataType: 'json',
		success: function(response){
			if(response.success == 0){
				$('#userErr').html(response.msg);
				$('#save_account').removeAttr('disabled');
				$('#loadingImgProfile').hide();
				return false;
			}else{
				$('#loadingImgProfile').hide();
				window.location.href = baseURL+'settings';
			}
		}
	});
	
	return false;
}
function updateUserPhoto(){
	//$('#save_profile_image').disable();
	if($('.uploadavatar').val()==''){
		alert('Choose a image to upload');
		$('#save_profile_image').removeAttr('disabled');
		return false;
	}else{
		$('#profile_settings_form').removeAttr('onSubmit');
		$('#profile_settings_form').submit();
	}
}
function deleteUserPhoto(){
	//$('#delete_profile_image').disable();
	var res = window.confirm('Are you sure?');
	if(res){
		$.ajax({
			type:'POST',
			url:baseURL+'site/user_settings/delete_user_photo',
			dataType:'json',
			success:function(response){
				if(response.success == '0'){
					alert(response.msg);
					$('#delete_profile_image').removeAttr('disabled');
					return false;
				}else{
					window.location.href = baseURL+'settings';
				}
			}
		});
	}else{
		$('#delete_profile_image').removeAttr('disabled');
		return false;
	}
}
function deactivateUser(){
	//$('#close_account').disable();
	var res = window.confirm('Are you sure?');
	if(res){
		$.ajax({
			url:baseURL+'site/user_settings/delete_user_account',
			success:function(response){
				window.location.href = baseURL;
			}
		});
	}else{
		$('#close_account').removeAttr('disabled');
	}
}

function delete_gift(val,gid) {
	
	$.ajax({
		type: 'POST',   
		url:baseURL+'site/cart/ajaxDelete',
		data:{'curval':val,'cart':'gift'},
		success:function(response){
			window.location.reload();
			/*var arr = response.split('|');
			$('#gift_cards_amount').val(arr[0]);
			$('#item_total').html(arr[0]);
			$('#total_item').html(arr[0]);
			$('#Shop_id_count').html(arr[1]);	
			$('#Shop_MiniId_count').html(arr[1]+' items');				
			$('#giftId_'+gid).hide();
			$('#GiftMindivId_'+gid).hide();
			if(arr[0] == 0){
				$('#GiftCartTable').hide();
				if(arr[1]==0){
					$('#EmptyCart').show();
				}
			}*/
		}
	});
}	


function delete_subscribe(val,sid) {
	
	$.ajax({
		type: 'POST',   
		url:baseURL+'site/cart/ajaxDelete',
		data:{'curval':val,'cart':'subscribe'},
		success:function(response){
				//alert(response);
			var arr = response.split('|');
			$('#subcrib_amount').val(arr[0]);
			$('#subcrib_ship_amount').val(arr[1]);
			$('#subcribt_tax_amount').val(arr[2]);
			$('#subcrib_total_amount').val(arr[3]);			
			$('#SubCartAmt').html(arr[0]);
			$('#SubCartSAmt').html(arr[1]);
			$('#SubCartTAmt').html(arr[2]);
			$('#SubCartGAmt').html(arr[3]);			
			$('#Shop_id_count').html(arr[4]);
			$('#Shop_MiniId_count').html(arr[4]+' items');			
			$('#SubscribId_'+sid).hide();
			$('#SubcribtMinidivId_'+sid).hide();
			
			
			if(arr[0] == 0){
				$('#SubscribeCartTable').hide();
				if(arr[4]==0){
					$('#EmptyCart').show();
				}
			}
		}
	});
}	


function ajaxEditproductAttribute(attname,attval,attId){
		
	//alert(attname+''+attval+''+attId);

	$('#loadingImg_'+attId).html('<span class="loading"><img src="images/indicator.gif" alt="Loading..."></span>');
	
	$.ajax({
		type: 'POST',   
		url:baseURL+'admin/product/ajaxProductAttributeUpdate',
		data:{'attname':attname,'attval':attval,'attId':attId},
		success:function(response){
			//alert(response);
			$('#loadingImg_'+attId).html('');
		}
	});
	
}

function ajaxCartAttributeChange(attId,prdId){
		
	$('#loadingImg_'+prdId).html('<span class="loading"><img src="images/indicator.gif" alt="Loading..."></span>');
	$('#AttrErr').html('');
	$.ajax({
		type: 'POST',   
		url:baseURL+'site/product/ajaxProductDetailAttributeUpdate',
		data:{'prdId':prdId,'attId':attId},
		success:function(response){
			//alert(response);
			var arr = response.split('|');
			
			$('#attribute_values').val(arr[0]);
			$('#price').val(arr[1]);
			$('#SalePrice').html(arr[1]);
			$('#loadingImg_'+prdId).html('');
		}
	});
	
}


function ajaxCartAttributeChangePopup(attId,prdId){


	$('#loadingImg1_'+prdId).html('<span class="loading"><img src="images/indicator.gif" alt="Loading..."></span>');
	$.ajax({
		type: 'POST',   
		url:baseURL+'site/product/ajaxProductDetailAttributeUpdate',
		data:{'prdId':prdId,'attId':attId},
		success:function(response){
			//alert(response);
			var arr = response.split('|');
			$('#attribute_values').val(arr[0]);
			$("#attr_name_id").val(attId);
			$('#price').val(arr[1]);
			$('#SalePrice').html(arr[1]);
			$('#loadingImg1_'+prdId).html('');
		}
	});
	
}


function delete_cart(val,cid) {
		$.ajax({
			type: 'POST',   
			url:baseURL+'site/cart/ajaxDelete',
			data:{'curval':val,'cart':'cart'},
			success:function(response){
				
			//alert(response);
			var arr = response.split('|');
			$('#cart_amount').val(arr[0]);
			$('#cart_ship_amount').val(arr[1]);
			$('#cart_tax_amount').val(arr[2]);
			$('#cart_total_amount').val(arr[3]);			
			$('#CartAmt').html(arr[0]);
			$('#CartAmtDup').html(arr[0]);
			$('#CartSAmt').html(arr[1]);
			$('#CartTAmt').html(arr[2]);
			$('#CartGAmt').html(arr[3]);			
			$('#Shop_id_count').html(arr[4]);
			$('#Shop_MiniId_count').html(arr[4]+' items');			
			$('#cartdivId_'+cid).hide();
			$('#cartMindivId_'+cid).hide();
			
			if(arr[0] == 0){
				$('#CartTable').hide();
				if(arr[4]==0){
					$('#EmptyCart').show();
				}
			}
			}
		});
}	


function delete_cart_user(val,cid,selid) {
		$.ajax({
			type: 'POST',   
			url:baseURL+'site/cart/ajaxDelete',
			data:{'curval':val,'cart':'usercart','sellId':selid},
			success:function(response){
			
			window.location.reload();	
			//alert(response);
			/*var arr = response.split('|');
			$('#user_cart_amount_'+selid).val(arr[0]);
			$('#user_cart_ship_amount_'+selid).val(arr[1]);
			$('#user_cart_tax_amount_'+selid).val(arr[2]);
			$('#user_cart_total_amount_'+selid).val(arr[3]);			
			$('#UserCartAmt_'+selid).html(arr[0]);
			$('#UserCartAmtDup_'+selid).html(arr[0]);
			$('#UserCartSAmt_'+selid).html(arr[1]);
			$('#UserCartTAmt_'+selid).html(arr[2]);
			$('#UserCartGAmt_'+selid).html(arr[3]);
			$('#Shop_id_count').html(arr[4]);
			$('#Shop_MiniId_count').html(arr[4]+' items');			
			$('#UsercartdivId_'+cid).hide();
			$('#UsercartMindivId_'+cid).hide();
			
			if(arr[0] == 0){
				$('#UserCartTable_'+selid).hide();
				if(arr[4]==0){
					$('#EmptyCart').show();
				}
			}*/
			}
		});
}


function update_cart(val,cid) {
	
	var qty  = $('#quantity'+cid).val();
	var mqty = $('#quantity'+cid).data('mqty');
	if(qty-qty != 0 || qty == '' || qty == '0'){
		alert('Invalid quantity');
		return false;
	}
	if(qty>mqty){
		$('#quantity'+cid).val(mqty);
		qty = mqty;
		alert('Maximum stock available for this product is '+mqty);
	}
		$.ajax({
			type: 'POST',   
			url:baseURL+'site/cart/ajaxUpdate',
			data:{'updval':val,'qty':qty},
			success:function(response){
				//alert(response); 
				var arr = response.split('|');
				$('#cart_amount').val(arr[1]);
				$('#cart_ship_amount').val(arr[2]);
				$('#cart_tax_amount').val(arr[3]);
				$('#cart_total_amount').val(arr[4]);			
				$('#IndTotalVal'+cid).html(arr[0]);				
				$('#CartAmt').html(arr[1]);
				$('#CartAmtDup').html(arr[1]);
				$('#CartSAmt').html(arr[2]);
				$('#CartTAmt').html(arr[3]);
				$('#CartGAmt').html(arr[4]);			
				$('#Shop_id_count').html(arr[5]);
				$('#Shop_MiniId_count').html(arr[5]+' items');	
			
			}
		});
}


function update_cart_user(val,cid,selid) {
	var qty  = $('#userquantity'+cid).val();
	var mqty = $('#userquantity'+cid).data('mqty');
	if(qty-qty != 0 || qty == '' || qty == '0'){
		alert('Invalid quantity');
		return false;
	}
	if(qty>mqty){
		$('#quantity'+cid).val(mqty);
		qty = mqty;
		alert('Maximum stock available for this product is '+mqty);
	}
		$.ajax({
			type: 'POST',   
			url:baseURL+'site/cart/ajaxUserUpdate',
			data:{'updval':val,'qty':qty,'selid':selid},
			success:function(response){
				//alert(response); 
				window.location.reload();
/*				var arr = response.split('|');
				$('#user_cart_amount_'+selid).val(arr[1]);
				$('#user_cart_ship_amount_'+selid).val(arr[2]);
				$('#user_cart_tax_amount_'+selid).val(arr[3]);
				$('#user_cart_total_amount_'+selid).val(arr[4]);
				$('#UserIndTotalVal'+cid+'_'+selid).html(arr[0]);	
				$('#UserCartAmt_'+selid).html(arr[1]);
				$('#UserCartAmtDup_'+selid).html(arr[1]);
				$('#UserCartSAmt_'+selid).html(arr[2]);
				$('#UserCartTAmt_'+selid).html(arr[3]);
				$('#UserCartGAmt_'+selid).html(arr[4]);
				$('#Shop_id_count').html(arr[5]);
				$('#Shop_MiniId_count').html(arr[5]+' items');	
*/			
			}
		});
}


function CartChangeAddress(IDval){
	
	var amt = $('#cart_amount').val();	
	var disamt = $('#discount_Amt').val();	
	
	
	$.ajax({
		type: 'POST',   
		url:baseURL+'site/cart/ajaxChangeAddress',
		data:{'add_id':IDval,'amt':amt,'disamt':disamt},
		success:function(response){
			
			if(response !='0'){
				
				var arr = response.split('|');
				$('#cart_ship_amount').val(arr[0]);
				$('#cart_tax_amount').val(arr[1]);
				$('#cart_tax_Value').val(arr[2]);
				$('#cart_total_amount').val(arr[3]);
				$('#CartSAmt').html(arr[0]);
				$('#CartTAmt').html(arr[1]);
				$('#carTamt').html(arr[2]);
				$('#CartGAmt').html(arr[3]);
				
				$('#Ship_address_val').val(IDval);
				$('#Chg_Add_Val').html(arr[4]);
			}else{
			
				return false;	
			}
		}
	});
}


function UserCartChangeAddress(IDval,selid){
	
	var amt = $('#user_cart_amount_'+selid).val();	
	
	$.ajax({
		type: 'POST',   
		url:baseURL+'site/cart/ajaxUserChangeAddress',
		data:{'add_id':IDval,'amt':amt,'seller_id':selid},
		success:function(response){
			
			//alert(response); return false;
			
			if(response !='0'){
				
				window.location.reload();
				
				/*var arr = response.split('|');
				$('#user_cart_ship_amount_'+selid).val(arr[0]);
				$('#user_cart_tax_amount_'+selid).val(arr[1]);
				$('#user_cart_tax_Value_'+selid).val(arr[2]);
				$('#user_cart_total_amount_'+selid).val(arr[3]);
				$('#UserCartSAmt_'+selid).html(arr[0]);
				$('#UserCartTAmt_'+selid).html(arr[1]);
				$('#UsercarTamt_'+selid).html(arr[2]);
				$('#UserCartGAmt_'+selid).html(arr[3]);
				
				$('#User_Ship_address_val_'+selid).val(IDval);
				$('#Chg_Add_Val_'+selid).html(arr[4]);*/
				
			}else{
				return false;	
			}
		}
	});
}


function SubscribeChangeAddress(IDval){
	
	var amt = $('#subcrib_amount').val();	
	
	$.ajax({
		type: 'POST',   
		url:baseURL+'site/cart/ajaxSubscribeAddress',
		data:{'add_id':IDval,'amt':amt},
		success:function(response){
			if(response !='0'){
				//alert(response);
				var arr = response.split('|');
				$('#subcrib_ship_amount').val(arr[0]);
				$('#subcrib_tax_amount').val(arr[1]);
				$('#subcrib_total_amount').val(arr[3]);
				$('#SubCartSAmt').html(arr[0]);
				$('#SubCartTAmt').html(arr[1]);
				$('#SubTamt').html(arr[2]);
				$('#SubCartGAmt').html(arr[3]);
				$('#SubShip_address_val').val(IDval);
				$('#SubChg_Add_Val').html(arr[4]);
			}else{
				return false;	
			}
		}
	});
}

function shipping_Subcribe_address_delete(){
	var DelId = $('#SubShip_address_val').val();
	$.ajax({
		type: 'POST',   
		url:baseURL+'site/cart/ajaxDeleteAddress',
		data:{'del_ID':DelId},
		success:function(response){
			if(response ==0){
				location.reload();
			}else{
				$('#Ship_Sub_err').html('Default address don`t be deleted.');
				setTimeout("hideErrDiv('Ship_Sub_err')", 3000);
				return false;	
			}
		}
	});
}

function shipping_cart_address_delete(){
var DelId = $('#Ship_address_val').val();

	$.ajax({
		type: 'POST',   
		url:baseURL+'site/cart/ajaxDeleteAddress',
		data:{'del_ID':DelId},
		success:function(response){
			if(response ==0){
				location.reload();
			}else{
				$('#Ship_err').html('Default address don`t be deleted.');
				setTimeout("hideErrDiv('Ship_err')", 3000);
				return false;	
			}
		}
	});
}

function shipping_user_cart_address_delete(selId){
	var DelId = $('#User_Ship_address_val_'+selId).val();
	//alert(DelId);
	$.ajax({
		type: 'POST',   
		url:baseURL+'site/cart/ajaxDeleteAddress',
		data:{'del_ID':DelId},
		success:function(response){
			if(response ==0){
				location.reload();
			}else{         
				$('#User_Ship_err_'+selId).html('Default address don`t be deleted.');
				setTimeout("hideErrDiv('User_Ship_err_'+selId)", 3000);
				return false;	
			}
		}
	});
}

function apply_coupon_code(selid){
		$('#Coupon_apply_'+selid).show();
		$('#shopcoupon'+selid).attr('onclick','display_coupon_code('+selid+')');
}

function display_coupon_code(selid){
	$('#Coupon_apply_'+selid).hide();
	$('#shopcoupon'+selid).attr('onclick','apply_coupon_code('+selid+')');
}


function ajax_add_cart(){
	
	var variation_count=$('#variation_count').html();
	if(variation_count==2){
		if($('#variation_one').val()==""){
			$('#Err_variation_one').html('Choose '+$('#var_one').html());
			return false;
		}else{
			$('#Err_variation_one').html('');	
		}
		
		if($('#variation_two').val()==""){
			$('#Err_variation_two').html('Choose '+$('#var_two').html());
			return false;
		}else{
			$('#Err_variation_two').html('');
		}
			
	}else if(variation_count==1){
		if($('#variation_one').val()==""){
			$('#Err_variation_one').html('Choose '+$('#var_one').html());
			return false;
		}else{
			$('#Err_variation_one').html('');	
		}
	}
	
		$('#QtyErr').html('');
		$('#ADDCartErr').html('');
		$('#ADDCartErr').show();
		
		var AttrCountVal = parseInt($('#variation_count').html());
		var quantity=$('#qty').val();
		var mqty = $('#quantity_list').data('mqty');
		
		if(quantity == '0' || quantity == ''){
			$('#QtyErr').html('<font color="red">Invalid quantity</font>');
			return false;
		}
		if(quantity>mqty){
			$('#QtyErr').html('<font color="red">Maximum Purchase Quantity at a time is '+mqty+'</font>');
			$('#quantity').val(mqty);
			return false;
		}
		
		if(AttrCountVal == 1){
			attrVal = $('#variation_one_name').val()+':'+$('#variation_one_val').val();
		}else if(AttrCountVal == 2){
			attrVal = $('#variation_one_name').val()+':'+$('#variation_one_val').val()+','+$('#variation_two_name').val()+':'+$('#variation_two_val').val();	
		}else{
			attrVal ='';
		}
		
		var digital_files=$('#digital_files').val();
		var product_id=$('#product_id').val();
		var sell_id=$('#sell_id').val();
		var price=$('#price').val();
		
		
		//alert(baseURL);
		//alert(product_id+''+sell_id+''+price+''+''+attrVal);
		
		$.ajax({
			type: 'POST',
			url: baseURL+'site/cart/usercartadd',
			data: {'mqty':mqty,'quantity':quantity, 'product_id':product_id, 'sell_id':sell_id, 'price':price, 'attribute_values':attrVal,'digital_files':digital_files},
			success: function(response){
				//alert(response);
				
				var arr = response.split('|');
				if(arr[0] =='login'){
					window.location.href= baseURL+"login";	
				}else if(arr[0] == 'Error'){
					//alert('siva');
					$('#ADDCartErr').html('<font color="red">Maximum Purchase Quantity: '+mqty+'. Already in your cart: '+arr[1]+'.</font>');
					$('#ADDCartErr').show().delay('2000').fadeOut();
				}else{
					$('#CartCount').html(arr[1]);
					$('#ADDCartErr').html('<font color="green">Product Added in your cart</font>');
					$('#ADDCartErr').show().delay('2000').fadeOut();
					//$('#MiniCartViewDisp').html(arr[1]);
					//$('#cart_popup').show().delay('2000').fadeOut();
				}
		
			}
		});
		return false;
	

}

function ajax_add_cart_subcribe(){
	var login = $('#subscribe').attr('require_login');
	if(login){ require_login(); return;}
	
	var user_id=$('#user_id').val();
	var quantity=1;
	var fancybox_id=$('#fancybox_id').val();
	var price=$('#price').val();
	var fancy_shipping_cost=$('#shipping_cost').val();
	var fancy_tax_cost=$('#tax').val();
	var category_id=$('#category_id').val();		
	var name=$('#name').val();		
	var seourl=$('#seourl').val();		
	var image=$('#image').val();			

	$.ajax({
		type: 'POST',
		url: baseURL+'site/fancybox/cartsubscribe',
		data: {'name':name, 'quantity':quantity, 'user_id':user_id, 'fancybox_id':fancybox_id, 'price':price, 'fancy_ship_cost':fancy_shipping_cost, 'category_id':category_id, 'fancy_tax_cost':fancy_tax_cost, 'seourl':seourl, 'image':image},
		success: function(response){
			//alert(response);
			if(response =='login'){
				window.location.href= baseURL+"login";	
			}else{
				$('#MiniCartViewDisp').html(response);
				$('#cart_popup').show().delay('2000').fadeOut();
			}

		}
	});
	return false;
}



function ajax_add_gift_card(){

	var login = $('.create-gift-card').attr('require_login');
	if(login){ require_login(); return;}
	
	$('#GiftErr').html();
					   
	var price = $('#price_value').val();
	var rec_name = $('#recipient_name').val();
	var rec_mail = $('#recipient_mail').val();
	var descp = $('#description').val();
	var sen_name = $('#sender_name').val();
	var sen_mail = $('#sender_mail').val();
	if(price ==''){
		$('#GiftErr').html('Please Select the Price Value');
		return false;		
	}
	if(rec_name ==''){
		$('#GiftErr').html('Please Enter the Receiver Name');
		return false;		
	}
	if(rec_mail ==''){
		$('#GiftErr').html('Please Enter the Receiver Email');		
		return false;		
	}else{
		if( !validateEmail(rec_mail)) { 
				$('#GiftErr').html('Please Enter Valid Email Address');		
				return false;
		}
	}
	if(descp =='' ){
		$('#GiftErr').html('Please  Enter the Description');		
		return false;
	}

		$.ajax({
			type: 'POST',
			url: baseURL+'site/giftcard/insertEditGiftcard',	
			data: {'price_value':price, 'recipient_name':rec_name, 'recipient_mail':rec_mail, 'description':descp, 'sender_name':sen_name, 'sender_mail':sen_mail },
			success: function(response){
				if(response =='login'){
					window.location.href= baseURL+"login";	
				}else{
					$('#MiniCartViewDisp').html(response);
					$('#cart_popup').show().delay('2000').fadeOut();
				}
			}
		});
		
	return false;
	
}



function ajax_user_add_cart(){
	$('#QtyUserErr').html('');
	var login = $('.add_to_cart').attr('require_login');
	if(login){ require_login(); return;}
	var quantity=$('#quantity').val();
	var mqty = $('#quantity').data('mqty');
	if(quantity == '0' || quantity == ''){
		$('#QtyUserErr').html('Invalid quantity');
		return false;
	}
	if(quantity>mqty){
		$('#QtyUserErr').html('Maximum Purchase Quantity at a time is '+mqty);
		$('#quantity').val(mqty);
		return false;
	}
	
	var product_id=$('#product_id').val();
	var sell_id=$('#sell_id').val();
	var price=$('#price').val();
	var cate_id=$('#cateory_id').val();		
	var attribute_values=$('#attribute_values').val();

	//alert(product_id+''+sell_id+''+price+''+cate_id+''+quantity+''+attribute_values);
	$.ajax({
		type: 'POST',
		url: baseURL+'site/cart/usercartadd',
		data: {'quantity':quantity, 'product_id':product_id, 'sell_id':sell_id, 'cate_id':cate_id, 'price':price, 'attribute_values':attribute_values,'mqty':mqty},
		success: function(response){
			//alert(response);
			var arr = response.split('|');
			if(arr[0] =='login'){
				window.location.href= baseURL+"login";	
			}else if(arr[0] == 'Error'){
				//alert('siva');
				$('#QtyUserErr').html('Maximum Purchase Quantity: '+mqty+'. Already in your cart: '+arr[1]+'.');
			}else{
				$('#MiniCartViewDisp').html(arr[1]);
				$('#cart_popup').show().delay('2000').fadeOut();
			}

		}
	});
	return false;
	
	
}




function change_user_password(){
	$('#save_password').disable();
	var pwd = $('#pass').val();
	var cfmpwd = $('#confirmpass').val();
	if(pwd == ''){
		$('#passErr').html('Enter new password');
		$('#save_password').removeAttr('disabled');
		$('#pass').focus();
		return false;
	}else if(pwd.length < 6){
		$('#passErr').html('Password must be minimum of 6 characters');
		$('#save_password').removeAttr('disabled');
		$('#pass').focus();
		return false;
	}else if(cfmpwd == ''){
		$('#confirmpassErr').html('Confirm password required');
		$('#save_password').removeAttr('disabled');
		$('#confirmpass').focus();
		return false;
	}else if(pwd != cfmpwd){
		$('#confirmpassErr').html('Passwords doesnot match');
		$('#save_password').removeAttr('disabled');
		$('#confirmpass').focus();
		return false;
	}else{
		return true;
	}
}

function shipping_address_cart(){
	var dlg_address = $.dialog('newadds-frm'), dlg_address1 = $.dialog('editadds-frm'), $tpl = $('#address_tmpl').remove();
//	dlg_address.$obj.trigger('reset').find('.ltit').text(gettext('Add Shipping Address')).end().find('.ltxt dt').html('<b>'+gettext('New Shipping Address')+'</b><small>'+gettext('We ships worldwide with global delivery services.')+'</small>');
			dlg_address.open();

			setTimeout(function(){dlg_address.$obj.find(':text:first').focus()},10);
}

function shipping_address_login(){
	$('#Ship_err').html('Please Login to add Shipping Address.');
	setTimeout("hideErrDiv('Ship_err')", 3000);
	return false;	
}

function product_details_contact_form(){
	var dlg_signin = $.dialog('contact_frm');
	dlg_signin.open();
}


function product_details_user_contact_form(){
	var dlg_signin = $.dialog('user_contact_frm');
	dlg_signin.open();
}


//Coupon code Used

function checkCode(selId) {
	
	$('#CouponErr_'+selId).html('');
	$('#CouponErr_'+selId).show();
	
	var cartValue = $('#user_cart_amount_'+selId).val();
	if(cartValue > 0){
	
	var code = $('#is_coupon_'+selId).val();
	var amount = $('#user_cart_amount_'+selId).val();
	var shipamount = $('#user_cart_ship_amount_'+selId).val();
	var taxamount = $('#user_cart_tax_amount_'+selId).val();
	
		if(code != '') {

			$.ajax({
			type: 'POST',
			url: baseURL+'site/cart/checkCode',
			data: {'code':code, 'amount':amount, 'shipamount':shipamount, 'seller_id': selId},
			success: function(response){
				//alert(response);
				var resarr = response.split('|');
				if(response == 1) {
					$('#CouponErr_'+selId).html('Entered code is invalid');
					return false;
				} else if(response == 2) {
					$('#CouponErr_'+selId).html('Code is already used');
					return false;
				}else if(response == 3) {
					$('#CouponErr_'+selId).html('Please add more items in the cart and enter the coupon code');
					return false;
				} else if(response == 4) {
					$('#CouponErr_'+selId).html('Entered Coupon code is not valid for this product');
					return false;
				} else if(response == 5) {
					$('#CouponErr_'+selId).html('Entered Coupon code is expired');
					return false;
				} else if(response == 6) {
					$('#CouponErr_'+selId).html('Entered code is Not Valid');
					return false;
				} else if(response == 7) {
					$('#CouponErr_'+selId).html('Please add more items quantity in the particular category or product, for using this coupon code');
					return false;
				} else if(response == 8) {
					$('#CouponErr_'+selId).html('Entered Gift code is expired');
					return false;	
				} else if(resarr[0] == 'Success') {
						
						window.location.reload();
					/*$.ajax({
					type: 'POST',
					url: baseURL+'site/cart/checkCodeSuccess',
					data: {'code':code, 'amount':amount, 'shipamount':shipamount},
					success: function(response){
//						alert(response); 	
						var arr = response.split('|');
						
						$('#cart_amount').val(arr[0]);
						$('#cart_ship_amount').val(arr[1]);
						$('#cart_tax_amount').val(arr[2]);
						$('#cart_total_amount').val(arr[3]);
						$('#discount_Amt').val(arr[4]);						
						$('#CartAmt').html(arr[0]);
						$('#CartSAmt').html(arr[1]);
						$('#CartTAmt').html(arr[2]);
						$('#CartGAmt').html(arr[3]);	
						$('#disAmtVal').html(arr[4]);
						$('#disAmtValDiv').show();
						$('#CouponCode').val(code);
						$('#Coupon_id').val(resarr[1]);
						$('#couponType').val(resarr[2]);
						var j=6;
						for (var i=0;i<arr[5];i++)	{ 
						//alert(arr[j]);
							$('#IndTotalVal'+i).html(arr[j]);
							 j++;
						}
						$("#CheckCodeButton").val('Remove');
						$("#is_coupon").attr('readonly','readonly');
						//$("#CheckCodeButton").removeAttr("onclick");
						document.getElementById("CheckCodeButton").setAttribute("onclick", "javascript:checkRemove();");
					}
					});*/
				} 
			}
		});
		} else {
			$('#CouponErr').html('Enter Valid Code');
		}
	} else {
		$('#CouponErr').html('Please add items in cart and enter the coupon code');
		
	}
	setTimeout("hideErrDiv('CouponErr')", 3000);
}

function reedemGiftcard() {
	
	$('#reedemLoad').show();
	$('#ReedemErr').html('');
	$('#ReedemErr').show();
	
	var cartValue = $('#total_price').val();
		
	var code = $('#reedemcode').val();
	var amount = $('#cart_price').val();
	var shipamount = $('#ship_price').val();
	var taxamount = $('#tax_price').val();
	var discountamount = $('#discount_price').val();
	var giftdiscountamount = $('#gift_discount_price').val();
	var cartlessamount = $('#cart_less_price').val();

		if(code != '') {

			$.ajax({
			type: 'POST',
			url: baseURL+'site/checkout/ReedemCheckCode',
			data: {'code':code, 'amount':amount, 'shipamount':shipamount, 'taxamount': taxamount, 'discountamount': discountamount, 'giftdiscountamount': giftdiscountamount, 'cartlessamount': cartlessamount},
			success: function(response){
				//alert(response);
				var resarr = response.split('|');
				if(response == 1) {
					$('#ReedemErr').html('Entered code is invalid');
					$('#reedemLoad').hide();					
					return false;
				} else if(response == 2) {
					$('#ReedemErr').html('Code is already used');
					$('#reedemLoad').hide();					
					return false;
				}else if(response == 3) {
					$('#ReedemErr').html('Please add more items in the cart and enter the coupon code');
					$('#reedemLoad').hide();					
					return false;
				} else if(response == 4) {
					$('#ReedemErr').html('Entered Gift code is not valid for this product');
					$('#reedemLoad').hide();					
					return false;
				} else if(response == 5) {
					$('#ReedemErr').html('Entered Gift code is expired');
					$('#reedemLoad').hide();					
					return false;
				} else if(response == 6) {
					$('#ReedemErr').html('Entered code is Not Valid');
					$('#reedemLoad').hide();					
					return false;
				} else if(response == 7) {
					$('#ReedemErr').html('Please add more items quantity in the particular category or product, for using this Gift code');
					$('#reedemLoad').hide();	
					return false;
				} else if(response == 8) {
					$('#ReedemErr').html('Entered Gift code is expired');
					$('#reedemLoad').hide();	
					return false;	
				} else if(resarr[0] == 'Success') {
						
						window.location.reload();
					/*$.ajax({
					type: 'POST',
					url: baseURL+'site/cart/checkCodeSuccess',
					data: {'code':code, 'amount':amount, 'shipamount':shipamount},
					success: function(response){
//						alert(response); 	
						var arr = response.split('|');
						
						$('#cart_amount').val(arr[0]);
						$('#cart_ship_amount').val(arr[1]);
						$('#cart_tax_amount').val(arr[2]);
						$('#cart_total_amount').val(arr[3]);
						$('#discount_Amt').val(arr[4]);						
						$('#CartAmt').html(arr[0]);
						$('#CartSAmt').html(arr[1]);
						$('#CartTAmt').html(arr[2]);
						$('#CartGAmt').html(arr[3]);	
						$('#disAmtVal').html(arr[4]);
						$('#disAmtValDiv').show();
						$('#CouponCode').val(code);
						$('#Coupon_id').val(resarr[1]);
						$('#couponType').val(resarr[2]);
						var j=6;
						for (var i=0;i<arr[5];i++)	{ 
						//alert(arr[j]);
							$('#IndTotalVal'+i).html(arr[j]);
							 j++;
						}
						$("#CheckCodeButton").val('Remove');
						$("#is_coupon").attr('readonly','readonly');
						//$("#CheckCodeButton").removeAttr("onclick");
						document.getElementById("CheckCodeButton").setAttribute("onclick", "javascript:checkRemove();");
					}
					});*/
				} 
			}
		});
		} else {
			$('#ReedemErr').html('Enter Gift Code');
			$('#reedemLoad').hide();			
			return false;
		}

	
	setTimeout("hideErrDiv('ReedemErr')", 3000);
}

function reedemGiftcardRemove(){
	$('#reedemLoad').show();
	$('#ReedemErr').html('');
	$('#ReedemErr').show();
	
	var code = $('#reedemcode').val();
	//alert(code);
	$.ajax({
			type: 'POST',
			url: baseURL+'site/checkout/ReedemcheckCodeRemove',
			data: {'code':code},
			success: function(response){
				//alert(response);
				window.location.reload();
			
			}
		});
}

function sellerCartdelete(selId){
	//alert(selId);
	$.ajax({
			type: 'POST',
			url: baseURL+'site/cart/SellerCartRemove',
			data: {'seller_id':selId},
			success: function(response){
				//alert(response);
				window.location.reload();
			}
		});
}

function giftcardCartRemove(user_id){
	//alert(selId);
	$.ajax({
			type: 'POST',
			url: baseURL+'site/cart/giftcardCartRemove',
			data: {'user_id':user_id},
			success: function(response){
				//alert(response);
				window.location.reload();
			}
		});
}

function contactshopowner(selId,prodId){
	
	$.ajax({
			type: 'POST',
			url: baseURL+'site/cart/contactshopowner',
			data: {'seller_id':selId,'product_id':prodId},
			success: function(response){
				//alert(response);
				$('#inline_reg2').html(response);
				$(".reg-popup2").colorbox({width:"800px", height:"auto", open:true, inline:true, href:"#inline_reg2"});
				//window.location.reload();
			}
		});
}

function alertOwnprodBuy(){ 
	$(".alert-popupcart").colorbox({width:"360px", height:"auto",overflow:"auto", open:true, inline:true, href:"#alert_cartAdd"});			
}



function contacttheshop(usrId,orderId){
	$.ajax({
			type: 'POST',
			url: baseURL+'site/user/contactshop',
			data: {'usrId':usrId,'orderId':orderId},
			success: function(response){
				//alert(response);
				$('#inline_nxt').html(response);
				$(".reg-popupnxt").colorbox({width:"748px", height:"auto", open:true,inline:true, href:"#inline_nxt"});
				//window.location.reload();
			}
		});
}

function makeReview(usrId,prodId,dealCode){
	$.ajax({
			type: 'POST',
			url:baseURL+'site/user/makeReviewBox',
			data: {'userId':usrId,'product_id':prodId,'dealCode':dealCode},
			success: function(response){
				//alert(response);
			
				$('#inline_reg1').html(response);
				$(".rev-popup").colorbox({width:"700px", height:"auto", open:true, inline:true, href:"#inline_reg1"});
				//window.location.reload();
			}
		});
}

function contactUser(id){
	$('#contact_reg').html('');
	$.ajax({
			type: 'POST',
			url: baseURL+'site/shop/contactuserpopup',
			data: {'id':id},
			success: function(response){
				$('#contact_reg').html(response);
				$(".contact-popup").colorbox({width:"765px", height:"auto", open:true,inline:true, href:"#contact_reg"});
			}
		});
}

function checkRemove(selId){
	
	$('#CouponErr_'+selId).html('');
	$('#CouponErr_'+selId).show();
	
	var code = $('#is_coupon_'+selId).val();
	//alert(code);
	$.ajax({
			type: 'POST',
			url: baseURL+'site/cart/checkCodeRemove',
			data: {'code':code,'seller_id':selId},
			success: function(response){
				//alert(response);
				window.location.reload();
						/*var arr = response.split('|');
						
						$('#cart_amount').val(arr[0]);
						$('#cart_ship_amount').val(arr[1]);
						$('#cart_tax_amount').val(arr[2]);
						$('#cart_total_amount').val(arr[3]);
						$('#discount_Amt').val(arr[4]);						
						$('#CartAmt').html(arr[0]);
						$('#CartSAmt').html(arr[1]);
						$('#CartTAmt').html(arr[2]);
						$('#CartGAmt').html(arr[3]);	
						$('#disAmtVal').html(arr[4]);
						$('#disAmtValDiv').show();
						$('#CouponCode').val(code);
						$('#Coupon_id').val(0);
						$('#couponType').val('');
						var j=6;
						for (var i=0;i<arr[5];i++)	{ 
						//alert(arr[j]);
							$('#IndTotalVal'+i).html(arr[j]);
							 j++;
						}
						
						$('#is_coupon').val('');
						$('#disAmtValDiv').hide();

						$("#is_coupon").removeAttr('readonly');
						$("#CheckCodeButton").val('Apply');
						document.getElementById("CheckCodeButton").setAttribute("onclick", "javascript:checkCode();");*/
						
					
			
			}
		});
}


function paypal(){
	$('#PaypalPay').show();
	$('#CreditCardPay').hide();	
	$('#otherPay').hide();
	$("#dep1").attr("class","depth1 current");
	$("#dep2").attr("class","depth2");	
	$("#dep1 a").attr("class","current");
	$("#dep2 a").attr("class","");	
}

function creditcard(){
	
	$('#CreditCardPay').show();	
	$('#PaypalPay').hide();
	$('#otherPay').hide();
	
	$("#dep1").attr("class","depth1");
	$("#dep2").attr("class","depth2 current");	
	$("#dep1 a").attr("class","");
	$("#dep2 a").attr("class","current");	
	
}

function othermethods(){
	
	$('#otherPay').show();	
	$('#PaypalPay').hide();
	$('#CreditCardPay').hide();	
	
	$("#dep1").attr("class","depth1");
	$("#dep2").attr("class","depth2");
	$("#dep3").attr("class","depth3 current");	
	$("#dep1 a").attr("class","");
	$("#dep2 a").attr("class","");
	$("#dep3 a").attr("class","current");	
	
}

function loadListValues(e){
	var lid =  $(e).val();
	var listValue = $(e).parent().next().find('select');
	if(lid == ''){
		listValue.html('<option value="">--Select--</option>');
	}else{
		listValue.hide();
		$(e).parent().next().append('<span class="loading">Loading...</span>');
		$.ajax({
			type:'POST',
			url:BaseURL+'admin/product/loadListValues',
			data:{lid:lid},
			dataType:'json',
			success:function(json){
				listValue.next().remove();
				listValue.html(json.listCnt).show();
			}
		});
	}
}

function loadListValuesUser(e){
	var lid =  $(e).val();
	var listValue = $(e).parent().next().find('select');
	if(lid == ''){
		listValue.html('<option value="">--Select--</option>');
	}else{
		listValue.hide();
		$(e).parent().next().append('<span class="loading">Loading...</span>');
		$.ajax({
			type:'POST',
			url:BaseURL+'site/product/loadListValues',
			data:{lid:lid},
			dataType:'json',
			success:function(json){
				listValue.next().remove();
				listValue.html(json.listCnt).show();
			}
		});
	}
}



function changeListValues(e,lvID){
	var lid =  $(e).val();
	var listValue = $(e).parent().next().find('select');
	if(lid == ''){
		listValue.html('<option value="">--Select--</option>');
	}else{
		listValue.hide();
		$(e).parent().next().append('<span class="loading">Loading...</span>');
		$.ajax({
			type:'POST',
			url:BaseURL+'admin/product/loadListValues',
			data:{lid:lid,lvID:lvID},
			dataType:'json',
			success:function(json){
				listValue.next().remove();
				listValue.html(json.listCnt).show();
			},
			complete:function(){
				listValue.next().remove();
				listValue.show();
			}
		});
	}
}

function changeListValuesUser(e,lvID){
	var lid =  $(e).val();
	var listValue = $(e).parent().next().find('select');
	if(lid == ''){
		listValue.html('<option value="">--Select--</option>');
	}else{
		listValue.hide();
		$(e).parent().next().append('<span class="loading">Loading...</span>');
		$.ajax({
			type:'POST',
			url:BaseURL+'site/product/loadListValues',
			data:{lid:lid,lvID:lvID},
			dataType:'json',
			success:function(json){
				listValue.next().remove();
				listValue.html(json.listCnt).show();
			},
			complete:function(){
				listValue.next().remove();
				listValue.show();
			}
		});
	}
}


//confirm status change
function confirm_status_dashboard(path){
 	$.confirm({
 		'title'		: 'Confirmation',
 		'message'	: 'You are about to change the status of this record ! Continue?',
 		'buttons'	: {
 			'Yes'	: {
 				'class'	: 'yes',
 				'action': function(){
 					window.location = BaseURL+'admin/dashboard/admin_dashboard';
 				}
 			},
 			'No'	: {
 				'class'	: 'no',
 				'action': function(){
 					return false;
 				}	// Nothing to do in this case. You can as well omit the action property.
 			}
 		}
 	});
 }			
 
 
function validateEmail($email) {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  if( !emailReg.test( $email ) ) {
    return false;
  } else {
    return true;
  }
}

function changeShipStatus(value,dealCode,seller){
	$('.status_loading_'+dealCode).prev().hide();
	$('.status_loading_'+dealCode).show();
	$.ajax({
		type:'post',
		url:baseURL+'site/user/change_order_status',
		data:{'value':value,'dealCode':dealCode,'seller':seller},
		dataType:'json',
		success:function(json){
			if(json.status_code == 1){
//				alert('Shipping status changed successfully');
			}
		},
		fail:function(data){
			alert(data);
		},
		complete:function(){
			$('.status_loading_'+dealCode).hide();
			$('.status_loading_'+dealCode).prev().show();
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

function approveCmt(evt){
	if($(evt).hasClass('approving'))return;
	$(evt).addClass('approving');
	$(evt).text('Approving...');
	var cfm = window.confirm('Are you sure to approve this comment ?\n This action cannot be undone.');
	if(cfm){
		var cid = $(evt).data('cid');
		var tid = $(evt).data('tid');
		var uid = $(evt).data('uid');
		$.ajax({
			type:'post',
			url:baseURL+'site/product/approve_comment',
			data:{'cid':cid,'tid':tid,'uid':uid},
			dataType:'json',
			success:function(json){
				if(json.status_code == '1'){
					$(evt).parent().remove();
				}else{
					$(evt).removeClass('approving');
					$(evt).text('Approve');
				}
			}
		});
	}else{
		$(evt).removeClass('approving');
		$(evt).text('Approve');
	}
}

function deleteCmt(evt){
	if($(evt).hasClass('deleting'))return;
	$(evt).addClass('deleting');
	$(evt).text('Deleting...');
	var cfm = window.confirm('Are you sure to delete this comment ?\n This action cannot be undone.');
	if(cfm){
		var cid = $(evt).data('cid');
		$.ajax({
			type:'post',
			url:baseURL+'site/product/delete_comment',
			data:{'cid':cid},
			dataType:'json',
			success:function(json){
				if(json.status_code == '1'){
					$(evt).parent().parent().remove();
				}else{
					$(evt).removeClass('deleting');
					$(evt).text('Delete');
				}
			}
		});
	}else{
		$(evt).removeClass('deleting');
		$(evt).text('Delete');
	}
}

function post_order_comment(pid,utype,uid,dealcode){
	if($('.order_comment_'+pid).hasClass('posting'))return;
	$('.order_comment_'+pid).addClass('posting');
	var $form = $('.order_comment_'+pid).parent();
	if(uid==''){
		alert('Login required');
		$('.order_comment_'+pid).removeClass('posting');
	}else{
		if($('.order_comment_'+pid).val() == ''){
			alert('Your comment is empty');
			$('.order_comment_'+pid).removeClass('posting');
		}else{
			$form.find('img').show();
			$form.find('input').hide();
			$.ajax({
				type:'post',
				url:baseURL+'site/user/post_order_comment',
				data:{'product_id':pid,'comment_from':utype,'commentor_id':uid,'deal_code':dealcode,'comment':$('.order_comment_'+pid).val()},
				complete:function(){
					$form.find('img').hide();
					$form.find('input').show();
					window.location.reload();
				}
			});
		}
	}
}

function post_order_comment_admin(pid,dealcode){
	if($('.order_comment_'+pid).hasClass('posting'))return;
	$('.order_comment_'+pid).addClass('posting');
	var $form = $('.order_comment_'+pid).parent();
	if($('.order_comment_'+pid).val() == ''){
		alert('Your comment is empty');
		$('.order_comment_'+pid).removeClass('posting');
	}else{
		$form.find('img').show();
		$form.find('input').hide();
		$.ajax({
			type:'post',
			url:baseURL+'admin/order/post_order_comment',
			data:{'product_id':pid,'comment_from':'admin','commentor_id':'1','deal_code':dealcode,'comment':$('.order_comment_'+pid).val()},
			complete:function(){
				$form.find('img').hide();
				$form.find('input').show();
				window.location.reload();
			}
		});
	}
}

function changeReceivedStatus(evt,rid){
	$(evt).hide();
	$(evt).next().show();
	$.ajax({
		type:'post',
		url:baseURL+'site/user/change_received_status',
		data:{'rid':rid,'status':$(evt).val()},
		complete:function(){
			$(evt).show();
			$(evt).next().hide();
		}
	});
}

function update_refund(evt,uid){
	if($(evt).hasClass('updating'))return;
	$(evt).addClass('updating').text('Updating..');
	var amt = $(evt).prev().val();
	if(amt == '' || (amt-amt != 0)){
		alert('Enter valid amount');
		$(evt).removeClass('updating').text('Update');
		return false;
	}else{
		$.ajax({
			type:'post',
			url:baseURL+'admin/seller/update_refund',
			data:{'amt':amt,'uid':uid},
			complete:function(){
				window.location.reload();
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
/* Formating function for row details 
function fnFormatDetails ( oTable, nTr )
{
    var aData = oTable.fnGetData( nTr );
	
	alert(baseURL);
	$.ajax({
		type: 'POST',
		url: baseURL+'admin/order/subviewDetails',
		data: {'dealId':aData[4]},
		success: function(response){
			alert(response);
			

		}
	});
	
    var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    sOut += '<tr><td>Transaction ID:</td><td>'+aData[1]+' '+aData[4]+'</td></tr>';
    sOut += '<tr><td>Link to source:</td><td>Could provide a link here</td></tr>';
    sOut += '<tr><td>Extra info:</td><td>And any further details here (images etc)</td></tr>';
    sOut += '</table>';
     
    return sOut;
}*/


function social_media(val){
	
	$('#'+val+'Err').html('');
	var SocVal = $('#'+val).val();
	if(SocVal == ''){
		$('#'+val+'Err').html('Please enter the '+val+' Link');
		return false;
	}else if(SocVal == 'http://'){
		$('#'+val+'Err').html('Please enter the '+val+' Link');
		return false;
	}else{
		//alert('siva'); return false;
		$.ajax({
			type: 'POST',
			url: baseURL+'site/shop/socialmediaupdate',
			data: {'id':val,'idval':SocVal},
			success: function(response){
				//alert(response);
				$('#'+val+'check').prop('checked',true);
				$('#cboxClose').trigger('click');
			
			}
		});
	}
}


function location_shop(val){
	
	$('#'+val+'Err').html('');
	var SocVal = $('#'+val).val();
	if(SocVal == ''){
		$('#'+val+'Err').html('Please enter the '+val+' Link');
		return false;
	}else if(SocVal == 'Eg: Newyork, NY'){
		$('#'+val+'Err').html('Please enter the '+val+' Link');
		return false;
	}else{
		//alert('siva'); return false;
		$.ajax({
			type: 'POST',
			url: baseURL+'site/shop/socialmediaupdate',
			data: {'id':val,'idval':SocVal},
			success: function(response){
				$('#locationVal').html(SocVal);
				$('#cboxClose').trigger('click');
			}
		});
	}
}


function storesetup(){

    var chkArray = [];
    $(".check:checked").each(function() {
        chkArray.push($(this).val());
    });
     
    var SocialSelected = chkArray.join(',') + ",";
	
	var descrip = $('#seller_description').val();
	var fontfam = $('#fontfamily').val();

	var bgcolor = $('input:radio[name=bgcolor]:checked').val();
	var fontscolor = $('input:radio[name=fontscolor]:checked').val();
	var iconcolor = $('input:radio[name=iconcolor]:checked').val();
	var sellsetup = SocialSelected+'|'+fontfam+'|'+bgcolor+'|'+fontscolor+'|'+iconcolor;
	//alert(sellsetup);
	
	
	$.ajax({
			type: 'POST',
			url: baseURL+'site/shop/storesetupfirstpage',
			data: {'seller_social_icons':SocialSelected,'seller_description':descrip,'seller_font':fontfam,'seller_bg_color':bgcolor,'seller_font_color':fontscolor,'seller_icon_color':iconcolor,'seller_setup':sellsetup},
			success: function(response){
				//alert(response);
				location.href = baseURL+'shop-product-layout';
				
			}
		});
	
}

function closesettins(){
	$('#closeSetings').hide();	
}


function editPictureProductsSite(val,imgId){

	var id = 'img_'+val;
	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);
	$.ajax(
		    {
		        type: 'POST',
		        url: BaseURL+'site/product/editPictureProducts',
		        data: {"id": id,'cpage': sPage,'position': val,'imgId':imgId},
		        dataType: 'json',
		        success: function(response)
		        {
		        	if(response == 'No') {
						alert("You can't delete all the images");
						return false;
					  } else {
							  $('#img_'+val).remove();
					  }
		        }
		    });
}

function add_delete_follow(option,user_id)
{
	/*var show="";
	var hide="";
	if(option=="add_follow")
	{
	show="unfollow_button";
	hide="follow_button";	
	}	
	else
	{
	show="follow_button";
	hide="unfollow_button";
	}*/
	//alert(user_id);
	//var user_id=document.getElementById("user_id").value;
	
 	$.ajax({
                type : 'post',
                url  : BaseURL+'site/user/'+option,
				data : {user_id:user_id},
                dataType : 'json',
                success : function(json){
					//alert(json);
                    if (json.status_code == 1) {
                       //document.getElementById(hide).style.display="none";
					   //document.getElementById(show).style.display="block";
					
					   if($('#followAct').val() == 'yes' && $('#userName').val()!=''){
						window.location.href="people/"+$('#userName').val()+"/following";  
					   }else{
						window.location.href = window.location.pathname;
					   }
                    }
					else
					{
					alert("Login require");	
					}
                },
                error:function (){//alert("error");
                    //$this.attr('class', $this.data('old'));
                },
                complete : function(){
                    //$this.removeClass('loading');
                }
            });

}


/*Ajax Favorite Functions*/

function changeShopToFavourite(shopid,type){
var ar=location.href;
var d=ar.split("browse");
var redirect="";
if(d.length==1)
{
  d=ar.split("products");  	
  if(d.length>1)
  {
  redirect="products"+d[1];
  }
  else
  {
	  redirect="";
  }
}
else
{
	redirect="browse"+d[1];
}
	$(this).attr('onclick','').unbind('click');
	$.ajax({
		type:'post',
		url:baseURL+'site/user/insert_favorite_status',
		data:{'shopid':shopid,'type':type},
		dataType : 'json',
		success: function(json){
			if(json.status_code == 1) {
			   window.location.href = window.location.pathname;
            }
			else
			{
				location.href = baseURL+'login?action='+json.next_url+'&redirect='+redirect;
			}
        }
	});
}
function changeProductToFavourite(pid,type){		
var ar=location.href;
var d=ar.split("browse");
var redirect="";
if(d.length==1)
{
 
 var d=ar.split("market");
 if(d.length==1)
{
  d=ar.split("products");  	
 
  if(d.length>1)
  {
  redirect="products"+d[1];
  }
  else
  {
	  redirect="";
  }
}
else
{
	redirect="market"+d[1];
	}
}
else
{
	redirect="browse"+d[1];
}

	$.ajax({
		type:'post',
		url:baseURL+'site/user/product_favorite_status',
		data:{'pid':pid,'type':type},
        dataType : 'json',
		success: function(json){
			if (json.status_code == 1) {
			   window.location.href = window.location.href;
            }
			else
			{
				window.location.href = baseURL+'login?action='+json.next_url+'&redirect='+redirect;
			}
		}
	});
}

/*-----------------------Script for product detail page------------------------------------*/

$(document).ready(function(e) {
    
	$('#quantity_list').change(function(e) {
        $('#qty').val($('#quantity_list').val());
    });
	$('#variation_one').change(function(e) {
		variation_price=$('#variation_one').val().indexOf("$");
        if(variation_price!=-1)
		{
			end=$('#variation_one').val().indexOf("]");
			$('#price').val($('#variation_one').val().substring(variation_price+1,end));
			$('#variation_one_val').val($('#variation_one').val().substring(0,variation_price-1));
		}
		else
		{
			$('#price').val($('#price_val').val());
			$('#variation_one_val').val($('#variation_one').val());
		}
    });
	$('#variation_two').change(function(e) {
		$('#variation_two_val').val($('#variation_two').val());
    });
	return true;
});
/*Create List in Favorite page*/
$(document).ready(function(e) {
    $('#list_create').click(function(e) {
        $(this).hide();
		$('#create_list').show();
    });
	 $('#list_close').click(function(e) {
        $('#create_list').hide();
		$('#list_create').show();
    });
});

function addproducttoList(listId,prodId)
{
	$.ajax({
		type:'post',
		url:baseURL+'site/user/user_addproducttolist',
		data:{'listId':listId,'prodId':prodId},
			success: function(response){
				//alert(response);
				window.location.href = window.location.href;
            	}
	});
}
function manageRegisrtyProduct(userId,prodId)
{
	$.ajax({
		type:'post',
		url:baseURL+'site/user/user_manageRegistryProduct',
		data:{'userId':userId,'prodId':prodId},
			success: function(response){
				//alert(response);
				window.location.href = window.location.href;
            	}
	});
}
function validate_create_list(val)
{
	if($('#creat_list_'+val).val()!='')
	{
		var ddl=$('#ddl').val();
		var list=$('#list').val();
		var productId=$('#productId').val();
		$.ajax({
		type:'post',
		url:baseURL+'site/user/add_list',
		data:{'ddl':ddl,'list':list,'productId':productId},
			success: function(response){
				//alert(response);
				window.location.href = window.location.pathname;
            	}
		});
	}
	else
	{
		alert('Enter List Name!');
		return false;
	}
}
/*Validation for Add Shipping Address*/
function shipping_validation()
{
	document.getElementById("err_country").innerHTML="";
	document.getElementById("err_name").innerHTML="";
	document.getElementById("err_street").innerHTML="";
	document.getElementById("err_city").innerHTML="";
	document.getElementById("err_state").innerHTML="";
	document.getElementById("err_postal").innerHTML="";
	document.getElementById("err_phone").innerHTML="";
	var err="";
	if(document.getElementById("country").value=="Select")
	{
	document.getElementById("err_country").innerHTML="Select country";
		err=1;
	}
	if(document.getElementById("name").value=="")
	{
	document.getElementById("err_name").innerHTML="Can't be blank";
		err=1;
	}
	if(document.getElementById("street").value=="")
	{
	document.getElementById("err_street").innerHTML="Can't be blank";
		err=1;
	}
	if(document.getElementById("city").value=="")
	{
	document.getElementById("err_city").innerHTML="Can't be blank";
		err=1;
	}
	if(document.getElementById("state").value=="")
	{
	document.getElementById("err_state").innerHTML="Can't be blank";
		err=1;
	}
	if(document.getElementById("postal").value=="")
	{
	document.getElementById("err_postal").innerHTML="Can't be blank";
		err=1;
	}
	if(document.getElementById("phone").value=="")
	{
	document.getElementById("err_phone").innerHTML="Can't be blank";
		err=1;
	}
		
	if(err==1)
	{
	return false;	
	}
	
}
function editgitcardPictures(val,imgId){alert('123');

	var id = 'img_'+val;
	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);
	$.ajax(
		    {
		        type: 'POST',
		        url: BaseURL+'admin/giftcards/editgitcardPictures',
		        data: {"id": id,'cpage': sPage,'position': val,'imgId':imgId},
		        dataType: 'json',
		        success: function(response)
		        {
		        	if(response == 'No') {
						alert("You can't delete all the images");
						return false;
					  } else {
							  $('#img_'+val).remove();
					  }
		        }
		    });
}
/*delete user list*/
function delete_user_list(){

	if (!window.confirm('Are you sure?')) return false;
	var $this = $(this), params = {}, url;

	url = baseURL+'site/user/delete_user_list';
	
	params.lid = $('#list_Id').val();
	params.uid = $('#uid').val();
	
	$.ajax({
	    type : 'post',
	    url  : url,
	    data : params,
	    dataType : 'json',
	    success  : function(json){
			if(json.status_code != 1) {
			    alert(json.message);
			    return;
			}else{
				window.location = json.url;
			}
	    }
	});

	return false;
}
/* Change USer NAme */
function change_name(){
	var first_name=document.getElementById("new-first-name").value;
	var last_name=document.getElementById("new-last-name").value;
	$.ajax({
		url : baseURL+'site/user/change_name/pops',
		data : {'new-first-name' : first_name,'new-last-name' : last_name},
		type : "post",			
		success:function(e){
			window.location.href = window.location.pathname;
		},
		error: function(er){
			alert("error");
		}
	});
}

/*validation for popup send*/
function validat_popup_send(){
	if($('#subject').val()=='' ||  $('#message_text').val()==''){
		return false;
	}
}

/*Auto suggest*/
$(document).ready(function(e) {
    $('#search_items').keyup(function(e) {
		if($('#search_items').val()==''){$('#sugglist').html(''); return false;}
        search_key=$(this).val();
		$.ajax({
			url : baseURL+'site/search/autosuggest_list/'+search_key,
			data : {'search_key' : search_key},
			type : "post",			
			success:function(data){
				$('#sugglist').html(data);
			}
		});
    });
});
/*Rating Script*/
/*$(document).ready(function(e) {
    $('.ratting-list').each(function(e) {
		$(this).click(function(e) {
        	$('#dummy').val('gfgh');
		});
    });
});*/
function rattingValidation(){
	var des=$('#description').val();
	if(des.length<10){
		$('#descriptionErr').html('Type minimum 15 characters');
		$('#descriptionErr').show().delay('3000').fadeOut();
		return false;
	}else{
		return true;
	}
}
function ratting_star(rate){
	var i;
	for(i=1;i<=5;i++){
		$('#r'+i).removeClass();
		if(i<=rate){
			$('#r'+i).addClass('star-active');
		}else{
			$('#r'+i).addClass('star-inactive');
		}
	}
}
function makeReportReview(reviewer_id,review_id,reporter_id){
	$.ajax({
			type: 'POST',
			url:baseURL+'site/shop/contactReviewer',
			data: {'reviewer_id':reviewer_id,'review_id':review_id,'reporter_id':reporter_id},
			success: function(response){
				$('#reportReview').html(response);
				$(".report-popup").colorbox({width:"600px", height:"300px", open:true,inline:true, href:"#reportReview"});
			}
		});
}
function contactsCheck(){
	var subject=$('#subject').val();
	var message_text=$('#message_text').val();
	if(subject.length<3){
		$('#ErrPUP').html('Type Subject minimum 3 characters');
		$('#ErrPUP').show().delay('3000').fadeOut();
		return false;
	}if(message_text.length<5){
		$('#ErrPUP').html('Type message minimum 5 characters');
		$('#ErrPUP').show().delay('3000').fadeOut();
		return false;
	}else{
		return true;
	}
}

/*gift card scriptings*/
function add_gift_card(){

	var login = $('.cart_button').attr('require_login');
	if(login){ require_login(); return;}
	
	$('#GiftErr').html();			
	err=0;			   
	var price = $('#price_value').val();
	var rec_name = $('#recipient_name').val();	
	var descp = $('#description').val();
	var sen_name = $('#sender_name').val();
	var sen_mail = $('#sender_mail').val();	
	
	var rec_mail = $('#recipient_mail').val();
	var re_recipient_mail=$('#re_recipient_mail').val();
	
	if(price ==''){
		$('#priceErr').html('Please select one');
		$('.amount').addClass('error');
		err=1;		
	}else{
		$('#priceErr').html('');
		$('.amount').removeClass('error');
	}
	if(rec_name ==''){
		$('.recipient_nameErr').html('Please enter the recipient\'s name');
		$('.to').addClass('error');
		err=1;		
	}else{
		$('.recipient_nameErr').html('');
		$('.to').removeClass('error');
	}
	if(sen_name ==''){
		$('#sender_nameErr').html('Please enter your name');
		$('.from').addClass('error');
		err=1;		
	}else{
		$('#sender_nameErr').html('');
		$('.from').removeClass('error');
	}
	
	if(rec_mail ==''){
		$('#recipient_email').html('Please Enter the Receiver Email');	
		$('.to_mail').addClass('error');	
		err=1;		
	}else if( !validateEmail(rec_mail)) { 
		$('#recipient_email').html('Please Enter Valid Email Address');		
		$('.to_mail').addClass('error');	
		err=1;
	}else if(re_recipient_mail==''){		
		$('.to_mail').removeClass('error');
		$('#recipient_email').html('Please Re-Enter the Receiver Email');	
		$('.to_rmail').addClass('error');
		err=1;
	}else if(rec_mail!=re_recipient_mail){
		$('#recipient_email').html('Receiver Email doesn\'t matched');	
		$('.to_rmail').addClass('error');
		err=1;
	}else{
		$('#recipient_email').html('');		
		$('.to_rmail').removeClass('error');
	}
	
	
	if(err>0){
		return false;
	}
	return true;
	
}
function add_gift_card1(){

	var login = $('.cart_button').attr('require_login');
	if(login){ require_login(); return;}
	
	$('#GiftErr').html();			
	err=0;			   
	var price = $('#price_value1').val();
	var rec_name = $('#recipient_name1').val();	
	var descp = $('#description1').val();
	var sen_name = $('#sender_name1').val();
	var sen_mail = $('#sender_mail1').val();	
	
		
	if(price ==''){
		$('#priceErr1').html('Please select one');
		$('.amount1').addClass('error');
		err=1;		
	}else{
		$('#priceErr1').html('');
		$('.amount1').removeClass('error');
	}
	if(rec_name ==''){
		$('.recipient_name1Err').html('Please enter the recipient\'s name');
		$('.to').addClass('error');
		err=1;		
	}else{
		$('.recipient_name1Err').html('');
		$('.to').removeClass('error');
	}
	if(sen_name ==''){
		$('#sender_name1Err').html('Please enter your name');
		$('.from').addClass('error');
		err=1;		
	}else{
		$('#sender_name1Err').html('');
		$('.from').removeClass('error');
	}
	
	
	if(err>0){
		return false;
	}
	return true;
	
}
$(document).ready(function(){
var maxChars = $("#description");
var max_length = maxChars.attr('maxlength');
if (max_length > 0) {
    maxChars.bind('keyup', function(e){
        length = new Number(maxChars.val().length);
        counter = max_length-length;
        $("#maxtext_notify").text(counter);
    });
}
$('.priceList').click(function(e) {
    $('#price_value').val($(this).attr('id'));
});

var maxChars1 = $("#description1");
var max_length1 = maxChars.attr('maxlength');
if (max_length1 > 0) {
    maxChars1.bind('keyup', function(e){
        length1 = new Number(maxChars1.val().length);
        counter1 = max_length1-length1;
        $("#maxtext_notify1").text(counter1);
    });
}


$('.pah_priceList').click(function(e) {
    $('#price_value1').val($(this).attr('id'));
});


$('.es-carousel ul li').click(function(e) {
	$('#image').val($(this).attr('data-img'));
});

});


/****This for image size validation for banners  start ***************/



$(document).ready(function() {
$("#shop_banner_img").change(function(e) {   
	    e.preventDefault();   
        var formData = new FormData($(this).parents('form')[0]);
        $.ajax({
			beforeSend: function()
 		      {
				$("#loadedImgshop").css("display", "block");
      	       // $("#shop_banner_img").html('<img id="loadedImg" src="images/loader64.gif" style="widows:25px; height:25px;" />');
  			  },
            url: 'site/shop/ajax_check_shop_mainBanner_size',
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            success: function (data) { 
			$("#loadedImgshop").css("display", "none");
			  if(data=='Success'){
				  $('#ErrImage').css('color','#090');
				  $('#ErrImage').html('Success');
				  return true;
			  } else {
				  $('#ErrImage').css('color','#F00');
				  $('#ErrImage').html('Upload Image Too Small. Please Upload Image Size More than or Equalto 760 X 100 .');
				  return false;
			  }
		   },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
		
		
});



$("#banner_img").change(function(e) {   
	    e.preventDefault();   
        var formData = new FormData($(this).parents('form')[0]);
        $.ajax({
			beforeSend: function()
 		      {
				 $("#loadedImgPromote").css("display", "block");
      	        //$("#banner_img").html('<img id="loadedImg" src="images/loader64.gif" style="widows:25px; height:25px;" />');
  			  },
            url: 'site/shop/ajax_check_shop_mainBanner_size',
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            success: function (data) { 
			$("#loadedImgPromote").css("display", "none");
			  if(data=='Success'){
				  $('#ErrImage').css('color','#090');
				  $('#ErrImage').html('Success');
				  return true;
			  } else {
				  $('#ErrImage').css('color','#F00');
				  $('#ErrImage').html('Upload Image Too Small. Please Upload Image Size More than or Equalto 1400 X 400 .');
				  return false;
			  }
		   },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
		
		
});



});


/*gift card validtion for checkout*/
function giftcardprocess(){
	
	$('#GiftCartErr').html('');			
	var giftvalue = $("input[name=gift_payment_value]").is(":checked");
	if(giftvalue == false){
		$('#GiftCartErr').html('<span class="error">Please Choose the Payment Gateway</span>');
		return false;
	}else{
		$("#giftSubmit").submit();
	}	
}
/*Spam report validation*/
function validate_spamreport(){
	var countVAl=0;
    $('.spamchk').each(function() {
        if ($(this).is(':checked')) {
		   countVAl=countVAl+1;
		}			
    });
	if(countVAl == 0){
		$('#spamErr').html('You should select any one reason.');
		$('#spamErr').show().delay('3000').fadeOut();
		return false;
	}else if(countVAl>0){
		if($('#spam_text').val()==''){
			$('#spamErr').html('Type your Explanation.');
			$('#spamErr').show().delay('3000').fadeOut();
			return false;
		}
		else{
			return true;
		}
	}
}
/*check all in conversation*/
$(document).ready(function(e) {
    $(".select-all-msg").click(function () {
        if ($(this).is(':checked')) {
			$('input:checkbox[name=find_all]').prop("checked", true);
            $(".chkMsg").prop("checked", true);
        } else {
			$('input:checkbox[name=find_all]').prop("checked", false);
            $(".chkMsg").prop("checked", false);
        }
    });
	$(".chkMsg").click(function () {
		$(".chkMsg").each(function () {
			if ($(this).is(':checked')) {
			} else {
				$(".select-all-msg").prop("checked", false);
			}
		});
    });
});
function confirmTrashMsg(UsrId,folder,actionTake){
	var countVAl=0;
    $('.chkMsg').each(function() {
        if ($(this).is(':checked')) {
		   countVAl=countVAl+1;
		}			
    });
	if(countVAl == 0){
		alert('You should select atleast one message to delete.');
		return false;
	}else if(countVAl>0){
		if(confirm('Are you sure want to Continue?')){
			var MsgId='';
			$('.chkMsg').each(function() {
				if ($(this).is(':checked')) {
				  MsgId=MsgId+$(this).val()+'|';
				}			
			});
			$.ajax({
				beforeSend: function(){
					$("#MessageStatus").css("display", "block");
				},
				url: 'site/user/ajax_conversation_action',
				type: 'POST',
				data: {'MsgId':MsgId,'UsrId':UsrId,'folder':folder,'actionTake':actionTake},
				success: function (data) { 
					var RemoveIdsArr = data.split('|');
					var i=0;
					for(i=0;i<RemoveIdsArr.length;i++){
						$("#Msg_"+RemoveIdsArr[i]).remove();
					}
					var count=0;
					$('.chkMsg').each(function() {
						count++;
					});
					if (count<1) {
						//$('#checkbox-id').prop("checked", false);
						$('.conversation_container_right').append('This Folder is Empty');
						$('.message-box').hide();
					}
					$("#MessageStatus").css("display", "none");	
			   }
			});
		}else{
			return false;
		}
	}
}

function markmessage(modes){
	$('.chkMsg').each(function() {
		$(this).prop("checked", false);
		if ($(this).attr('data-mode')==modes) {
 			$(this).prop("checked", true);
		}			
	});
}

function taxCaluAdd(){
	var cname = $('#country_name').val();
	var sname = $('#state_name').val();
	var tax = $('#tax').val();
	
	$('#countryErr').html('');
	$('#stateErr').html('');
	$('#taxErr').html('');
	
	if(cname ==''){
		$('#countryErr').html('This field is required');
		return false;
	}else if(sname ==''){
		$('#stateErr').html('This field is required');
		return false;		
	}else if(tax ==''){
		$('#taxErr').html('This field is required');
		return false;		
	}else{
		$('#taxeditForm').submit();
		return true;
	}
	
	
}
