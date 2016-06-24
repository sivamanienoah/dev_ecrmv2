/*
 *@Manage Practice
 *@Manage Practice Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#succes_err_msg').empty();
});

function chk_practice_name() {
	$('#succes_err_msg,#hours_succes_err_msg').empty();
	
	var practices 			= $("#practices").val();
	var practice_id     	= $("#practice_id").val();
	var max_hours			= $("#max_hours").val();
	var params 				= {name: practices, id: practice_id};
	params[csrf_token_name] = csrf_hash_token;
	
	var error = true;
	if (practices == "") {
		$('#succes_err_msg').show();
		$('#succes_err_msg').append("<span class='ajx_failure_msg'>Practice Required.</span>");
		error = false;
	}
	if(max_hours == ""){
		
		$('#hours_succes_err_msg').show();
		$('#hours_succes_err_msg').append("<span class='ajx_failure_msg'>Max Hours Required.</span>");
		error =  false;
	}else if(max_hours==0){
		$('#hours_succes_err_msg').show();
		$('#hours_succes_err_msg').append("<span class='ajx_failure_msg'>Max hours should be morethan 0 hours.</span>");
		error =  false;
	}
	
	console.log("error"+error);
	if(error) {
		$.ajax({
			url: site_base_url+"manage_practice/chk_duplicate",
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