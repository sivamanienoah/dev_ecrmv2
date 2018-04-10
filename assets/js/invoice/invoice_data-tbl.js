/*
 *@DataTable Javascript
 *@For all tables
*/

$(function() {
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
		"bFilter": true,
		"bAutoWidth": false,
		"aoColumns": [
            { sWidth: '6%' },
            { sWidth: '6%' },
			{ sWidth: '7%' },
            { sWidth: '7%' },
            { sWidth: '10%' },
            { sWidth: '10%' },
            { sWidth: '10%' },
            { sWidth: '9%' },
            { sWidth: '8%' },
            { sWidth: '6%' },
            { sWidth: '5%' }
		]
	});
});