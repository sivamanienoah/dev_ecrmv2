/*
 *@Manage Practice
 *@Manage Practice Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#succes_err_msg').empty();
});

function chk_industry_name() {
	$('#succes_err_msg').empty();
	
	var industry 			= $("#industry").val();
	var industry_id     	= $("#industry_id").val();
	var params 				= {name: industry, id: industry_id};
	params[csrf_token_name] = csrf_hash_token;
	
	if (industry == "") {
		$('#succes_err_msg').show();
		$('#succes_err_msg').append("<span class='ajx_failure_msg'>Practice Required.</span>");
		return false;
	} else {
		$.ajax({
			url: site_base_url+"manage_industry/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#succes_err_msg').show();
					$('#succes_err_msg').append("<span class='ajx_failure_msg'>Industry Already Exists.</span>");
					return false;
				} else {
					document.add_industry.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////