/*
 *@manage expect worth currency
 *@Manage Service Controller
*/

//'accesspage' is global variable 

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

function timerfadeout() {
	$('.dialog-err').fadeOut();
}

function processDelete(id) {
	window.location.href = 'manage_service/cur_type_delete/update/'+id;
}

function cancelDel() {
    $.unblockUI();
}

$(function() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 1, "asc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false,	
	});
});

//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////