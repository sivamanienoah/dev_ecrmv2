/*
 *@Manage Cost Center
 *@Manage Cost Center Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#succes_err_msg').empty();
});

function chk_profit_center_name() {
	$('#succes_err_msg').empty();
	
	var profit_center 			= $("#profit_center").val();
	var profit_center_id     	= $("#profit_center_id").val();
	var params 				= {name: profit_center_id, id: profit_center};
	params[csrf_token_name] = csrf_hash_token;
	
	if (profit_center == "") {
		$('#succes_err_msg').show();
		$('#succes_err_msg').append("<span class='ajx_failure_msg'>Cost Center Required.</span>");
		return false;
	} else {
		$.ajax({
			url: site_base_url+"profit_center/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#succes_err_msg').show();
					$('#succes_err_msg').append("<span class='ajx_failure_msg'>Cost Center Already Exists.</span>");
					return false;
				} else {
					document.add_profit_center.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////