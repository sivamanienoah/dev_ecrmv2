/*
 *@Manage Lead Source
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#ser_req_msg').empty();
});

function chk_cat_name() {
	$('#cat_name_msg').empty();
	
	var category_name 		= $("#category_name").val();
	var category_hidden     = $("#category_hidden").val();
	var type		        = 'lead_services';
	var params 				= {name: category_name, id: category_hidden, type: type};
	params[csrf_token_name] = csrf_hash_token;
	
	if (category_name == "") {
		$('#cat_name_msg').show();
		$('#cat_name_msg').append("<span class='ajx_failure_msg'>Service Name Required.</span>");
		return false;
	} else {
		$.ajax({
			url: "manage_service/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#cat_name_msg').show();
					$('#cat_name_msg').append("<span class='ajx_failure_msg'>Service Name Already Exists.</span>");
					return false;
				} else {
					document.cat_name.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////