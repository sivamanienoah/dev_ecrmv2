/*
 *@Manage Lead Source
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#name_msg').empty();
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