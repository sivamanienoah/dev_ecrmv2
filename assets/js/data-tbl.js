/*
 *@DataTable Javascript
 *@For all tables
*/

$(function() {
	$('.tbl-data').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 15,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": false,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,	
	});
});