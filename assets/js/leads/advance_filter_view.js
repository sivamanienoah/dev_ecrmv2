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
		"aaSorting": [[ 0, "asc" ]],
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

//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////