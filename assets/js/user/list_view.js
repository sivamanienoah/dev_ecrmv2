/*
 *@User List Jquery
 *@User Module
*/

function checkStatus(id) {
	var formdata = { 'data':id }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'user/ajax_check_status_user/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			//$("#loadingImage").show();
			$('#dialog-err-msg').empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				//alert("You can't Delete the Lead source!. \n This Source is used in Leads.");
				$('#dialog-err-msg').show();
				$('#dialog-err-msg').append('One or more Leads / Projects currently mapped to this user. This cannot be deleted.');
				setTimeout('timerfadeout()', 4000);
			} else {
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'user/delete_user/'+id;
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