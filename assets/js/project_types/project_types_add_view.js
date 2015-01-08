/*
 *@Manage Project Types
 *@Manage Project Types Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#succes_err_msg').empty();
});

function chk_project_types_name() {
	$('#succes_err_msg').empty();
	
	var project_types 			= $("#project_types").val();
	var project_types_id     	= $("#project_types_id").val();
	var params 				= {name: project_types_id, id: project_types};
	params[csrf_token_name] = csrf_hash_token;
	
	if (project_types == "") {
		$('#succes_err_msg').show();
		$('#succes_err_msg').append("<span class='ajx_failure_msg'>Project Types Required.</span>");
		return false;
	} else {
		$.ajax({
			url: site_base_url+"project_types/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#succes_err_msg').show();
					$('#succes_err_msg').append("<span class='ajx_failure_msg'>Project Types Already Exists.</span>");
					return false;
				} else {
					document.add_project_types.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////