/*
 *@Manage Cost Center
 *@Manage Cost Center Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#succes_err_msg').empty();
});

function chk_cost_center_name() {
	$('#succes_err_msg').empty();
	
	var cost_center 			= $("#cost_center").val();
	var cost_center_id     	= $("#cost_center_id").val();
	var params 				= {name: cost_center_id, id: cost_center};
	params[csrf_token_name] = csrf_hash_token;
	
	if (cost_center == "") {
		$('#succes_err_msg').show();
		$('#succes_err_msg').append("<span class='ajx_failure_msg'>Cost Center Required.</span>");
		return false;
	} else {
		$.ajax({
			url: site_base_url+"cost_center/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#succes_err_msg').show();
					$('#succes_err_msg').append("<span class='ajx_failure_msg'>Cost Center Already Exists.</span>");
					return false;
				} else {
					document.add_cost_center.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////