$(function() {
	
	//export
	$("#btnExport").click(function () {
		$("#it_cost_grid").btechco_excelexport({
			containerid: "it_cost_grid"
		   , datatype: $datatype.Table
		   , filename: 'cost_report'
		});
	});
	
	$("#reset_drilldown").click(function(){
		$('#filter_group_by').prop('selectedIndex',0);
		$('#filter_sort_by').prop('selectedIndex','desc');
		$('#filter_sort_val').prop('selectedIndex','hour');
	});
	
});