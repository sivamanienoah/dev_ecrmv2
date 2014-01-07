/*
 *@Advance Filter View 
*/

$('#excel').click(function() {
	//mychanges
	var sturl = site_base_url+"welcome/excelExport/";
	document.location.href = sturl;
	return false;
});

$(function() {
	dtTable();
});	
	
function dtTable() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 1, "desc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,
		"bDestroy": true
	});
}

function deleteLeads(id, title) { 
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete <br />'+title+'?<br /><br />This will delete all the items<br />and logs attached to this Lead.</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}
function processDelete(id,t) {
	window.location.href = site_base_url+'welcome/delete_quote/'+id;
}

function cancelDel() {
	$.unblockUI();
}

$.ajaxSetup ({
    cache: false
});
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////