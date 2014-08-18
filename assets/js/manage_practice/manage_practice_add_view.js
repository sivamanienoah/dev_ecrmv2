/*
 *@Manage Lead Source
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#succes_err_msg').empty();
});

function chk_practice_name() {
	$('#succes_err_msg').empty();
	
	var practices 			= $("#practices").val();
	var practice_id     	= $("#practice_id").val();
	var params 				= {name: practices, id: practice_id};
	params[csrf_token_name] = csrf_hash_token;
	
	if (practices == "") {
		$('#succes_err_msg').show();
		$('#succes_err_msg').append("<span class='ajx_failure_msg'>Practice Required.</span>");
		return false;
	} else {
		$.ajax({
			url: "manage_practice/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#succes_err_msg').show();
					$('#succes_err_msg').append("<span class='ajx_failure_msg'>Practice Already Exists.</span>");
					return false;
				} else {
					document.add_practice.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////