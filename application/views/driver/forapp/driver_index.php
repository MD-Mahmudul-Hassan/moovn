<?php
$this->load->view('site/templates/common_header');
?>

<script>
	$(document).ready(function(){
        $('#locationName').val('');
          $('#categoryName').val('');
          $('form').submit(function(){
         
           $(".loader_img_submit").css("display","block");
           
         });
		$('#locationName').change(function(){
          
			$(".loader_img").css("display","block");
			var locId = $('#locationName').find(':selected').attr('data-catId'); 
			if(locId != '' && locId != 'undefined'){
				$.ajax({
				    type: "POST",
				    url: 'site/app_driver/available_categories',
				    data: {'locId':locId},
				    dataType: 'json',
				    success: function(res) {
							console.log(res);
					$(".loader_img").css("display","none");
						if(res.status == '1'){ 
							$('#categoryName').html(res.message);
						} else {
							alert(res.message);
						}
					}
				});
			}
		});
	});
</script>

<link rel="stylesheet" href="css/web_view.css">
<link rel="stylesheet" href="css/site/screen.css">
</head>
<body class="">
	<div class="loadGif"></div>
    <div class="dark bg_img">
        <div class="sign_up_base col-lg-12">
            <form name="driver_register_form" id="driver_register_initite_form" action="site/app_driver/singupInitiate_form" method="post" enctype="multipart/form-data">
				<div class="col-lg-12 nopadd driver_sign_up_form text-center">
				
					<div class="col-lg-12 sign_up_base ">   
						<select class="form-control required" id="locationName" name="locationName">
							<option value="" data-catId=""><?php if ($this->lang->line('driver_choose_location') != '')
                                    echo stripslashes($this->lang->line('driver_choose_location'));
                                else
                                    echo 'Please choose your location'; ?>...</option>
							<?php foreach($locationList->result() as $locations){ ?>
								<option data-catId="<?php echo (string)$locations->_id; ?>"><?php echo $locations->city; ?></option>
							<?php } ?>
						</select>
						<img src="images/indicator.gif" class="loader_img"/>
					</div>
					
					<div class="col-lg-12 sign_up_base ">   
						<select class="form-control required" id="categoryName" name="categoryName">
							<option value=""><?php if ($this->lang->line('driver_choose_catagory') != '')
                                    echo stripslashes($this->lang->line('driver_choose_catagory'));
                                else
                                    echo 'Please choose your category'; ?>...</option>
						</select>
					</div>
					 <input type="submit" id="proceed_next" class=" btn1 category_btn" value="<?php if ($this->lang->line('Next') != '')
                                    echo stripslashes($this->lang->line('Next'));
                                else
                                    echo 'Next'; ?>" />
				</div>
			</form>
        </div>
	</div>
</body>

 
<script src="js/jquery.validate.js"></script> 
<script>
	
			$(document).ready(function () {
				$(".loadGif").css("display","none");
			});
			$("#driver_register_initite_form").validate({ submitHandler: function(form) {
				$(".loadGif").css("display","block");
				$(form).ajaxSubmit();
			}});
</script>
<style>
.sign_up_center {
    float: none;
    margin: 0 auto;
    padding-top: 0px !important;
}
.category_btn {
    width: 100% !important;
}
.col-lg-12.sign_up_base {
    margin-top: 3%;
}
.driver_sign_up_form {
    background: #fff !important;
    border: medium none;
    margin-bottom: 50px;
    padding: 50px 30px !important;
}
.dark.bg_img {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
}
.loader_img{
	float:right;
	display:none;
}
.loader_img_submit{
	float:right;
	display:none;
}
</style>
</html>