/*
 *@Manage Service View
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable 

function checkStatuspk(id) {
	var formdata = { 'data':id, 'wh_condn':'typeid_fk', 'tbl':'package' }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'package/ajax_check_status_package_name/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-pk-msg').empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#dialog-pk-msg').show();
				$('#dialog-pk-msg').append("One or more package currently assigned for this type. This cannot be deleted.");
				$('html, body').animate({ scrollTop: $("#dialog-pk-msg").offset().top }, 1000);
				setTimeout('timerfadeout()', 4000);
			} else {
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'package/delete/'+id;
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