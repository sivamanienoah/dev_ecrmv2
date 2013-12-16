/*
 *@Advance Filter View 
*/

$('#excel').click(function() {
		//mychanges
		var sturl = "welcome/excelExport/";
		document.location.href = sturl;
		return false;
});

/* $(function(){
    $(".lead-table").tablesorter({widthFixed: false, widgets: ['zebra']});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
}); */
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
	window.location.href = 'welcome/delete_quote/'+id;
}

function cancelDel() {
	$.unblockUI();
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////