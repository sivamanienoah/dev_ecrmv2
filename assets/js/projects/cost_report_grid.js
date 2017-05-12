$(function() {
	
	//export
	$("#btnExport").click(function () {
		$('#it_cost_grid_filter').hide();
		$("#it_cost_grid").btechco_excelexport({
			containerid: "it_cost_grid"
		   , datatype: $datatype.Table
		   , filename: 'cost_report'
		});
		$('#it_cost_grid_filter').show();
	});
	
	$("#reset_drilldown").click(function(){
		$('#filter_group_by').prop('selectedIndex',0);
		$('#filter_sort_by').prop('selectedIndex','desc');
		$('#filter_sort_val').prop('selectedIndex','hour');
	});
	
	//data table
	$('#it_cost_grid').dataTable({
		"bInfo": false,
		"bPaginate": false,
		"bProcessing": false,
		"bServerSide": false,
		"bLengthChange": false,
		"bSort": true,
		"bDestroy": true,
		"oTableTools": {
        "aButtons": [
        {
        'sExtends':'csv',
        "sFileName": "subscribers.csv",
        'mColumns':[0,1]
        },
        {
        'sExtends':'pdf',
        "sFileName": "subscribers.pdf",
        'mColumns':[0,1] 
        },
    ]
    },
    "sDom": '<"H"Tlfr>tip<"F">',
	});
	
});
if(filter_area_status==1) {
	$('#advance_search').show();
}