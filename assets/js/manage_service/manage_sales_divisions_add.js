/*
 *@Manage Lead Source
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
	$('#sales_div_msg').empty();
});

function chk_sale_dup() {
	$('#sales_div_msg').empty();
	
	var division_name 		= $("#division_name").val();
	var sale_div_hidden     = $("#sale_div_hidden").val();
	var type		        = 'sales_divisions';
	var params 				= {name: division_name, id: sale_div_hidden, type: type};
	params[csrf_token_name] = csrf_hash_token;
	
	if (division_name == "") {
		$('#sales_div_msg').show();
		$('#sales_div_msg').append("<span class='ajx_failure_msg'>Sale Division Required.</span>");
		return false;
	} else {
		$.ajax({
			url: "manage_service/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#sales_div_msg').show();
					$('#sales_div_msg').append("<span class='ajx_failure_msg'>Sale Division Already Exists.</span>");
					return false;
				} else {
					document.sale_div.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////