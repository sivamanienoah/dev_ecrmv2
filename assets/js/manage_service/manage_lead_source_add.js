/*
 *@Manage Lead Source
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#lead_src_msg').empty();
});

function chk_src_dup() {
	$('#lead_src_msg').empty();
	
	var lead_source_name 	= $("#lead_source_name").val();
	var lead_src_hidden     = $("#lead_src_hidden").val();
	var type		        = 'lead_source';
	var params 				= {name: lead_source_name, id: lead_src_hidden, type: type};
	params[csrf_token_name] = csrf_hash_token;
	
	if (lead_source_name == "") {
		$('#lead_src_msg').show();
		$('#lead_src_msg').append("<span class='ajx_failure_msg'>Lead Source Required.</span>");
		return false;
	} else {
		$.ajax({
			url: "manage_service/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#lead_src_msg').show();
					$('#lead_src_msg').append("<span class='ajx_failure_msg'>Lead Source Already Exists.</span>");
					return false;
				} else {
					document.lead_source.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////