/*
 *@Manage Practice
 *@Manage Practice Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#succes_err_msg').empty();
});

function chk_task_category_name() {
	$('#succes_err_msg').empty();
	
	var task_category 			= $("#task_category").val();
	var task_category_id     	= $("#id").val();
	var params 					= {'name': task_category, 'id': task_category_id};
	params[csrf_token_name] 	= csrf_hash_token;
	
	if (task_category == "") {
		$('#succes_err_msg').show();
		$('#succes_err_msg').append("<span class='ajx_failure_msg'>Task Category Required.</span>");
		return false;
	} else {
		$.ajax({
			url: site_base_url+"manage_task_category/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#succes_err_msg').show();
					$('#succes_err_msg').append("<span class='ajx_failure_msg'>Task Category Already Exists.</span>");
					return false;
				} else {
					document.add_task_category.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////