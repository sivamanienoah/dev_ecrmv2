/*
 *@Manage Lead Source
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

function checkStatus(leadSrc_id) {
	var formdata = { 'data':leadSrc_id }
	formdata[csrf_token_name] = csrf_hash_token	
	$.ajax({
		type: "POST",
		url: site_base_url+'manage_service/ajax_check_status/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-message-'+leadSrc_id).empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#dialog-message-'+leadSrc_id).show();
				$('#dialog-message-'+leadSrc_id).append('One of more leads currently mapped to this lead source. This cannot be deleted.');
				setTimeout('timerfadeout()', 4000);
			} else {
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'manage_service/ls_delete/update/'+leadSrc_id;
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