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


function unSelectCreate(id)
{
	// tab_chk_view-101
	var viewid = '#tab_chk_view-'+id;
	if($('#tab_chk_add-'+id).is(':checked') == false) {
		$('#tab_chk_all-'+id).attr('checked', false);
	} else {
		var viewid = '#tab_chk_view-'+id;
		// $(viewid).attr ( "checked" ,true );
		$(viewid).prop( 'checked',true );
	}
}

function unSelectView(id)
{
	var addid 	 = '#tab_chk_add-'+id;
	var editid   = '#tab_chk_edit-'+id;
	var deleteid = '#tab_chk_del-'+id;
	var all	     = '#tab_chk_all-'+id;
	var viewid   = '#tab_chk_view-'+id;
	if($('#tab_chk_view-'+id).is(':checked') == false) {
		// $(viewid).attr('checked', false);
		$(viewid).removeAttr('checked');
		$(addid).attr('checked', false);
		$(editid).attr('checked', false);
		$(deleteid).attr('checked', false);
		$(all).attr('checked', false);
	} else {
		$(viewid).prop( 'checked',true );
	}
}

function unSelectEdit(id)
{
	if($('#tab_chk_edit-'+id).is(':checked') == false) {
		$('#tab_chk_all-'+id).attr('checked', false);
	} else {
		$('#tab_chk_view-'+id).prop( 'checked',true );
	}
}

function unSelectDelete(id)
{	
	var viewid   = '#tab_chk_view-'+id;
	if($('#tab_chk_del-'+id).is(':checked') == false) {
		$('#tab_chk_all-'+id).attr('checked', false);
	} else {
		var viewid = '#tab_chk_view-'+id;
		$(viewid).prop( 'checked',true );
	}

}

// tab_chk_1 = create/edit = tab_chk_add = tab_chk_edit
// tab_chk_4 = view = tab_chk_view
// tab_chk_2 = delete = tab_chk_del


//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////