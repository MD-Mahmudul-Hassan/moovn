$(function(){

	$("#turnOn").click(function(){
		$('#blog_view').show();
		});
	
	$("#turnOff").click(function(){
		$('#blog_view').hide();
		});
		
		/*Add Post Validation*/
	$("#publish").click(function(){
       if(jQuery.trim($("#post_title").val()) == ''){
			$("#post_title_warn").html('');
			$("#post_title_warn").html('This field is required');
			$("#post_title").focus();
			return false;
	   }
	});

		/*Blog Setup Validation*/
	$("#blog_set").click(function(){
		 if($('input[name=tempType]:checked').length<=0){
			  alert("Select atleast one template");
			  return false;	  
		  }
	});

		/*Publish Back Function*/
	$("#publish_back").click(function(){
		window.location.href = baseUrl+"blog-published";
	});	

		/*Post Back Function*/
	$("#post_back").click(function(){
		window.location.href = baseUrl+"blog-all-post";
	});	

	    /*Draft Back Function*/
	$("#draft_back").click(function(){
		window.location.href = baseUrl+"blog-drafts";
	});	
	$("#drafts").click(function(){
		$("#post_status").val('draft');
		$('#postForm').submit();
	});	
});
/* This is a common confirm action  */
function changeStatusCommon(modeChange)
{
	if(modeChange=='active')
	{
		var res = window.confirm('Do you want to inactive this?');
	}
	else if(modeChange=='inactive')
	{
		var res = window.confirm('Do you want to active this?');
	}
	else if(modeChange=='delete')
	{
		var res = window.confirm('Do you want to delete this?');
	}
	return res;
}

function hideErrDiv(arg) {
       $("#"+arg).slideUp();
}
function checkBoxValidationAdmin(req,namess) {	
		var tot=0;
		var chkVal = 'on';
		var frm = document.seekerActionForm;
		for (var i = 0; i < frm.elements.length; i++){
			if(frm.elements[i].type=='checkbox') {
				if(frm.elements[i].checked) {
					tot=1;
					chkVal = frm.elements[i].value;
				}
			}
		}
		//alert(chkVal);return false;
			if(tot == 0) {
					alert("Please Select the CheckBox");
					return false;
			}else if(chkVal == 'on') {
					alert("No records found ");
					return false;  
			
			} else {
				var res = window.confirm('Whether you want to continue this action');
				
				if(res == true){
							document.getElementById("statusMode").value=req;
							document.getElementById("seekerActionForm").submit();
				} else { 
						return false; 				  
				}
				   
			} 
			
}

