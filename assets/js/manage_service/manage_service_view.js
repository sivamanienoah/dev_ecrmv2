/*
 *@Manage Service View
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable 

function checkStatus(id) {
	var formdata = { 'data':id }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'manage_service/ajax_check_status_job_category/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-message-'+id).empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#dialog-message-'+id).show();
				$('#dialog-message-'+id).append("One or more leads currently assigned for this service. This cannot be deleted.");
				setTimeout('timerfadeout()', 4000);
			} else {
				$.blockUI({
					message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
					css:{width:'440px'}
				});
				$( ".blockUI.blockMsg.blockPage" ).addClass( "no-scroll" );
			}
		}                                                                                       
	});
return false;
}

function processDelete(id) {
	window.location.href = 'manage_service/ser_delete/update/'+id;
}

function cancelDel() {
    $.unblockUI();
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////