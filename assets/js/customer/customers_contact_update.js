/*
 *@Manage Lead Source
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#name_msg').empty();
	
	$('#document_tbl').delegate( '.check_email', 'keyup', function () {
		var thisRow = $(this).parent('td');
		if( $(this).val().length >= 3 )
		{
			var email 	   = $(this).val();
			var company_id = $('#emailupdate').val();
			var custids    = $(this).parent().parent().children().find('.contact_id').val();

			var filter = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			if(filter.test(email)){
				if (custids == "")
				var custid 	= 0;
				else
				var custid 	= custids;
			
				var params		= {};
				params[csrf_token_name] = csrf_hash_token;
				params['email'] 		= email;
				params['custid'] 		= custid;
				params['company_id'] 	= company_id;
				$.ajax({
					type: "POST",
					url : site_base_url + 'customers/Check_email/',
					cache : false,
					data : params,
					success : function(response){
						if(response == 'userOk') {
							$("#positiveBtn").removeAttr("disabled");
							$(thisRow).children(".email_err_msg").html("<span class='ajx_success_msg'>Email Available.</span>");
						} else { 
							$(thisRow).children(".email_err_msg").html('Email Already Exists.');
							$("#positiveBtn").attr("disabled", "disabled");
						}
					}
				});
			} else {
				$("#positiveBtn").attr("disabled", "disabled");
				$(thisRow).children(".email_err_msg").html('Not valid email.');
			}
		}
		return false;
	});
	
});

function chk_customers_contact() {
	$('#name_msg').empty();
	
	var customer_name=$("#customer_name").val();
	var email=$("#email").val();
	var position_title=$("#position_title").val();
	var phone=$("#phone").val();
	var skype_name=$("#skype_name").val();
	var custid=$("#custid").val();
	var params={customer_name: customer_name,email: email,position_title:position_title,phone:phone,skype_name:skype_name,custid:custid};
	params[csrf_token_name] = csrf_hash_token;
	
	if (customer_name == "") {
		$('#name_msg').show();
		$('#name_msg').append("<span class='ajx_failure_msg'>Name Required.</span>");
	}
	if (email == "") {
		$('#email_msg').show();
		$('#email_msg').append("<span class='ajx_failure_msg'>Email Required.</span>");
	}if (position_title == "") {
		$('#position_msg').show();
		$('#position_msg').append("<span class='ajx_failure_msg'>Position Title Required.</span>");
	}if (phone == "") {
		$('#phone_msg').show();
		$('#phone_msg').append("<span class='ajx_failure_msg'>Phone No Required.</span>");
	} 
	if(customer_name!='' && email!= "" && position_title!= "" && phone!="")
	{
		$.ajax({
			url: "customers_contact/update_contacts",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				window.location.href = 'customers_contact';
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////