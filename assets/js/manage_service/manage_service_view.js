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
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'manage_service/ser_delete/update/'+id;
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