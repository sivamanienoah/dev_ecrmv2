/*
 *@Item Management Category Jquery
*/

$(document).ready(function() {
	$('#lead_stg_msg').empty();
});

function chk_lead_stg() {
	$('#lead_stg_msg').empty();
	
	var lead_stage_name 	= $("#lead_stage_name").val();
	var lead_stg_hidden     = $("#lead_stg_hidden").val();
	var params 				= {name: lead_stage_name, id: lead_stg_hidden};
	params[csrf_token_name] = csrf_hash_token;
	
	if (lead_stage_name == "") {
		$('#lead_stg_msg').show();
		$('#lead_stg_msg').append("<span class='ajx_failure_msg'>Lead Stage Required.</span>");
		return false;
	} else {
		$.ajax({
			url: "manage_lead_stage/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#lead_stg_msg').show();
					$('#lead_stg_msg').append("<span class='ajx_failure_msg'>Lead Stage Already Exists.</span>");
					return false;
				} else {
					document.form.submit();
				}
			}		
		});
	}
	return false;
}

////////////////////////////////