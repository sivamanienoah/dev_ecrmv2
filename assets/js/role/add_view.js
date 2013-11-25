/*
 *@Add View
 *@Role Controller
*/

$(document).ready(function() {
	$('.check').click(function() {
		if ($(this).is(':checked')) {
			$(this).parent().find('input:checkbox').prop('checked', 'checked');
		}else{
			$(this).parent().find('input:checkbox').prop('checked', '');
		}
	});
});

function chk_role_name() {
	$('#role_msg').empty();
	var role_name 			= $("#role_name").val();
	var role_id_hidden     	= $("#role_id_hidden").val();
	var type		        = 'roles';
	var params 				= {name: role_name, id: role_id_hidden, type: type};
	params[csrf_token_name] = csrf_hash_token;
	
	if (role_name == "") {
		$('#role_msg').show();
		$('#role_msg').append("<span class='ajx_failure_msg'>Role Name Required.</span>");
		return false;
	} else {
		$.ajax({
			url: "role/chk_role_duplicate",
			data: params,
			type: "POST",
			dataType: 'json',
			success: function(data) {
				if(data == 'fail') {
					$('#role_msg').show();
					$('#role_msg').append("<span class='ajx_failure_msg'>Role Name Already Exists.</span>");
					return false;
				} else {
					document.add_role.submit();
				}
			}		
		});
	}
	return false;
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////