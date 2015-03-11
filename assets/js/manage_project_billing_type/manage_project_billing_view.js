/*
 *@Manage Practice View
 *@Manage Practice Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable 

function checkStatus(id) {
	var formdata = { 'data':id }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'manage_project_billing_type/ajax_check_status/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-message-'+id).empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#dialog-message-'+id).show();
				$('#dialog-message-'+id).append("One or more Projects currently assigned for this Type. This cannot be deleted.");
				setTimeout('timerfadeout()', 4000);
			} else {
				$.blockUI({
					message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
					css:{width:'440px'}
				});
			}
		}                                                                                       
	});
return false;
}

function processDelete(id) {
	window.location.href = site_base_url+'manage_project_billing_type/delete_project_billing_type/update/'+id;
}

function cancelDel() {
    $.unblockUI();
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////