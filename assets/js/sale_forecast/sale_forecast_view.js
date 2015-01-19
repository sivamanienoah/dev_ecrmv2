/*
 *@Manage Practice
 *@Manage Practice Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable
	
$('.data-tbl').dataTable({
	"aaSorting": [[ 6, "desc" ]],
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

function checkStatus(id) {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
	return false;
}

function processDelete(id) {
	window.location.href = site_base_url+'sales_forecast/delete_sale_forecast/update/'+id;
}

function cancelDel() {
    $.unblockUI();
}
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////