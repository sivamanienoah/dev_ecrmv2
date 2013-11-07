/*
 *@manage expect worth currency
 *@Manage Service Controller
*/

//'accesspage' is global variable 

$(function() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 15,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": false,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,	
	});
});
function checkStatus(id) {
	var formdata              = { 'data':id }
	formdata[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: "POST",
		url:site_base_url+'manage_service/ajax_check_status_currency/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-message-'+id).empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				//alert("You can't Delete the Lead source!. \n This Source is used in Leads.");
				$('#dialog-message-'+id).show();
				$('#dialog-message-'+id).append('One of more leads currently mapped to this currency. This cannot be deleted.');
				setTimeout('timerfadeout()', 4000);
			} else {
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'manage_service/cur_type_delete/update/'+id;
				} else {
					return false;
				}
			}
		}                                                                                       
	});
	return false;
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////