$(function() {
	
	//export
	$("#btnExport").click(function () {
		// document.getElementById("it_cost_grid").deleteTFoot();
		$('#exp_hide').hide();
		$("#it_cost_grid").btechco_excelexport({
			containerid: "it_cost_grid"
		   , datatype: $datatype.Table
		   , filename: 'cost_report'
		});
		// $('#exp_hide').hide();
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