/*
 *@Manage Lead Source
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$(document).ready(function() {
   // alert('hi');return false;
	$('#loc_div_msg').empty();
});

function chk_loc_dup() {
    //alert('hi');return false;
	$('#loc_div_msg').empty();
	
	var asset_location 		= $("#asset_location").val();
	var loc_div_hidden     = $("#loc_div_hidden").val();
	//var type		        = 'sales_divisions';
	var params 				= {name: asset_location, id: loc_div_hidden};
	params[csrf_token_name] = csrf_hash_token;
	
	if (asset_location == "") {
		$('#loc_div_msg').show();
		$('#loc_div_msg').append("<span class='ajx_failure_msg'>Location Required.</span>");
		return false;
	} else {
		$.ajax({
			url: "asset_register/chk_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#loc_div_msg').show();
					$('#loc_div_msg').append("<span class='ajx_failure_msg'>Location Already Exists.</span>");
					return false;
				} else {
					document.loc_div.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////