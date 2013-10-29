/*
 *@manage expect worth currency
 *@Manage Service Controller
*/

//'accesspage' is global variable 

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