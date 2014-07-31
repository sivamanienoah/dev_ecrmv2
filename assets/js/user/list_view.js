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
				$('#dialog-err-msg').append('One or more Leads/Projects currently mapped to this user. This cannot be deleted.');
				$('html, body').animate({ scrollTop: $('#dialog-err-msg').offset().top }, 500);
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
	window.location.href = 'user/delete_user/'+id;
}

function cancelDel() {
    $.unblockUI();
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}

/////////////////