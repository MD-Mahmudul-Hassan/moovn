$(function(){
    //original field values
    var field_values = {
            //id        :  value
            'email'  : 'email address',
            'username'  : 'username',
            'password'  : 'password',
            'cpassword' : 'password',
            'sitename'  : 'Website name',
            'siteurl'  : 'Website url'
    };


    //inputfocus
    $('input#email').inputfocus({ value: field_values[''] }); 
    $('input#username').inputfocus({ value: field_values[''] });
    $('input#password').inputfocus({ value: field_values[''] });
    $('input#cpassword').inputfocus({ value: field_values[''] }); 
    $('input#sitename').inputfocus({ value: field_values[''] });
    $('input#siteurl').inputfocus({ value: field_values[''] });




    //reset progress bar
    $('#progress').css('width','0');
    $('#progress_text').html('0% Complete');

    //first_step
    $('#installation').submit(function(){ event.preventDefault(); });
    $('#submit_first').click(function(){
        //remove classes
        $('#first_step input').removeClass('error').removeClass('valid');

        //ckeck if inputs aren't empty
		var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
        var fields = $('#first_step input[type=text], #first_step input[type=password]');
        var error = 0;
        fields.each(function(){
            var value = $(this).val();
            if( value.length<4 || value==field_values[$(this).attr('id')] || ( $(this).attr('id')=='email' && !emailPattern.test(value) ) ) {
                $(this).addClass('error');
                $(this).effect("shake", { times:3 }, 50);
                error++;
            } else {
                $(this).addClass('valid');
            }
        });        
        
        if(!error) {
            if( $('#password').val() != $('#cpassword').val() ) {
                    $('#first_step input[type=password]').each(function(){
                        $(this).removeClass('valid').addClass('error');
                        $(this).effect("shake", { times:3 }, 50);
                    });
                    
                    return false;
            } else {   
                //update progress bar
                $('#progress_text').html('33% Complete');
                $('#progress').css('width','113px');
                
                //slide steps
                $('#first_step').slideUp();
                $('#second_step').slideDown();  
                $('#submit_first').remove();  
            }               
        } else return false;
    });

	//second_step
    $('#submit_second').click(function(){
        //remove classes
        $('#second_step input').removeClass('error').removeClass('valid');

        var fields = $('#second_step input[type=text]');
        var error = 0;
        fields.each(function(){
            var value = $(this).val();
            if( value.length<1 || value==field_values[$(this).attr('id')]) {
                $(this).addClass('error');
                $(this).effect("shake", { times:3 }, 50);
                
                error++;
            } else {
                $(this).addClass('valid');
            }
        });

        if(!error) {
                //update progress bar
                $('#progress_text').html('66% Complete');
                $('#progress').css('width','226px');
                
                //slide steps
                $('#second_step').slideUp();
                $('#third_step').slideDown();   
                $('#submit_second').remove();    
        } else return false;

    });

    $('#submit_third').click(function(){
        //remove classes
        $('#third_step input').removeClass('error').removeClass('valid');

		var fields = $('#third_step input[type=text], #third_step input[type=password]');
        var error = 0;
        fields.each(function(){
            var value = $(this).val();
            if( value.length<1 || value==field_values[$(this).attr('id')]) {
                $(this).addClass('error');
                $(this).effect("shake", { times:3 }, 50);
                
                error++;
            } else {
                $(this).addClass('valid');
            }
        });

        if(!error) {
                //update progress bar
                $('#progress_text').html('99% Complete');
                $('#progress').css('width','339px');
				
				//prepare the fourth step
				var fields = new Array(
					$('#email').val(),
					$('#username').val(),
					$('#password').val(),
					$('#sitename').val(),
					$('#siteurl').val(),
					$('#mongo_host').val(),
					$('#mongo_port').val(),
					$('#mongo_user').val(),
					$('#mongo_pass').val(),
					$('#mongo_db').val()
				);
				var tr = $('#fourth_step tr');
				tr.each(function(){
					//alert( fields[$(this).index()] )
					$(this).children('td:nth-child(2)').html(fields[$(this).index()]);
				});
                
                //slide steps
                $('#third_step').slideUp();
                $('#fourth_step').slideDown();     
                $('#submit_third').remove();     
        } else return false;

    });


    $('#submit_fourth').click(function(){
        //send information to server
        //$('#installation').submit(function(){ return true; });
        $('#installation').submit();
    });

});
function hideErrDiv(){
	$('.errorCon').hide();
}