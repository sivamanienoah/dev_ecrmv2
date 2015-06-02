/*
 *@DataTable Javascript
 *@For all tables
*/

$(function() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 0, "desc" ]],
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
		"aoColumns": [
            { sWidth: '5%' },
            { sWidth: '10%' },
            { sWidth: '5%' },
            { sWidth: '5%' },
            { sWidth: '5%' },
            { sWidth: '7%' },
            { sWidth: '7%' },
            { sWidth: '7%' },
            { sWidth: '7%' },
            { sWidth: '7%' },
            { sWidth: '10%' }]
	});
});