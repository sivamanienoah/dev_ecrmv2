/*
 *@Manage Sales Divisions
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

if(accesspage==1) { 
	$(function() {
		$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']}) 
		.tablesorterPager({container: $("#pager"),positionFixed: false});
		$('.data-table tr, .data-table th').hover(
			function() { $(this).addClass('over'); },
			function() { $(this).removeClass('over'); }
		);
	});
} 

function checkStatus(id) {
	var formdata = { 'data':id }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'manage_service/ajax_check_status_division/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-message-'+id).empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				// alert("You can't Delete the Sales Division!. \n This Division is used in Leads.");
				$('#dialog-message-'+id).show();
				$('#dialog-message-'+id).append("One or more leads currently assigned for this sales division. This cannot be deleted.");
				setTimeout('timerfadeout()', 4000);
			} else {
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'manage_service/division_delete/update/'+id;
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