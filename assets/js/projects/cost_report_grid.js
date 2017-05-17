$(function() {
	
	//export
	$("#btnExport").click(function () {
		// document.getElementById("it_cost_grid").deleteTFoot();
		$(".it_cost_grid_div").btechco_excelexport({
			containerclass: "it_cost_grid"
		   , datatype: $datatype.Table
		   , filename: 'cost_report'
		});
	});
	
	$("#reset_drilldown").click(function(){
		$('#filter_group_by').prop('selectedIndex',0);
		$('#filter_sort_by').prop('selectedIndex','desc');
		$('#filter_sort_val').prop('selectedIndex','hour');
	});
	
	//data table
	$('#it_cost_grid').dataTable({
		"bInfo": false,
		"bFilter": false,
		"bPaginate": false,
		"bProcessing": false,
		"bServerSide": false,
		"bLengthChange": false,
		"bDestroy": true,
		'bAutoWidth': true
	});
	
});
if(filter_area_status==1) {
	$('#advance_search').show();
}