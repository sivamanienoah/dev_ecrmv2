/*
 *@Manage Project Billing Type
 *@Manage Project Billing Type Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#succes_err_msg').empty();
});

function chk_billing_type() {
	$('#succes_err_msg').empty();
	
	var project_billing_type = $("#project_billing_type").val();
	var bill_type_id     	 = $("#id").val();
	var params 				 = {name: project_billing_type, id: bill_type_id};
	params[csrf_token_name]  = csrf_hash_token;
	
	if (project_billing_type == "") {
		$('#succes_err_msg').show();
		$('#succes_err_msg').append("<span class='ajx_failure_msg'>Project Billing Type is required.</span>");
		return false;
	} else {
		$.ajax({
			url: site_base_url+"manage_project_billing_type/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#succes_err_msg').show();
					$('#succes_err_msg').append("<span class='ajx_failure_msg'>Billing Type Already Exists.</span>");
					return false;
				} else {
					document.add_billing_type.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////