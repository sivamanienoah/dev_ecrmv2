/*
 *@Manage Lead Source
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#name_msg').empty();
	
	$('.layout').delegate( '#email', 'keyup', function () {
		$("#email_msg").empty();
		if( $(this).val().length >= 3 )
		{
			var email 	   = $(this).val();
			var company_id = $('#company_id').val();
			var custid 	   = $('#custid').val();

			var filter = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			if(filter.test(email)){

				var custid 	= custid;
			
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
							$("#email_msg").html("<span class='ajx_success_msg'>Email Available.</span>");
							$("#email_msg").show();
						} else { 
							$("#email_msg").html('<span class="ajx_failure_msg">Email Already Exists.</span>');
							$("#email_msg").show();
							$("#positiveBtn").attr("disabled", "disabled");
						}
					}
				});
			} else {
				$("#positiveBtn").attr("disabled", "disabled");
				$("#email_msg").html('<span class="ajx_failure_msg">Not valid email.</span>');
				$("#email_msg").show();
			}
		}
		return false;
	});
	
});

function chk_customers_contact() {
	$('#name_msg').empty();
	var contact_error = false;
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
		contact_error = true;
	}
	if (email == "") {
		$('#email_msg').show();
		$('#email_msg').append("<span class='ajx_failure_msg'>Email Required.</span>");
		contact_error = true;
	}
	/* if (position_title == "") {
		$('#position_msg').show();
		$('#position_msg').append("<span class='ajx_failure_msg'>Position Title Required.</span>");
	} */
	if (phone == "") {
		$('#phone_msg').show();
		$('#phone_msg').append("<span class='ajx_failure_msg'>Phone No Required.</span>");
		contact_error = true;
	} 
	if(contact_error == true){
		return false;
	}
	/* if(customer_name!='' && email!= "" && phone!="")
	{
		$.ajax({
			url: site_base_url+'customers_contact/update_contacts/',
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				alert(data.result);
				if(data.result == 'success'){
					window.location.href = site_base_url+'customers_contact/';
				}
			}		
		});
	} */
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////