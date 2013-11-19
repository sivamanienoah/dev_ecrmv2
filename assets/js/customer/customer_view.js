/*
 *@Customer List Jquery
 *@Customer Module
*/

//ajax - check status
function checkStatus(id) {
	var formdata = { 'data':id }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'customers/ajax_chk_status_customer/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-err-msg').empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#dialog-err-msg').show();
				$('#dialog-err-msg').append('One or more Leads currently mapped to this customer. This cannot be deleted.');
				$('html, body').animate({ scrollTop: $('#dialog-err-msg').offset().top }, 500);
				setTimeout('timerfadeout()', 4000);
			} else {
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'customers/delete_customer/'+id;
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


/////////////////