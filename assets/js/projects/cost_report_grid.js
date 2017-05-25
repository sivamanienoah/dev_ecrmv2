$(function() {
	
	//export
	$("#btnExport").click(function () {
		// document.getElementById("it_cost_grid").deleteTFoot();
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
	
	/* $("#it_cost_grid thead tr th").click(function(){
		$("#it_cost_grid thead tr th").addClass("desc_opt");
		//$("#it_cost_grid thead tr th").removeClass("desc_asc_opt");
			if($(this).hasClass('desc_asc_opt')){
				$("#it_cost_grid thead tr th").removeClass("desc_asc_opt");				
			}else{
				$("#it_cost_grid thead tr th").removeClass("desc_asc_opt");
				$(this).addClass("desc_asc_opt");
			}
	}); */
	
	
	
	//data table
	$('#it_cost_grid').dataTable({
		"bInfo": false,
		"bFilter": true,
		"bPaginate": false,
		"bProcessing": false,
		"bServerSide": false,
		"bLengthChange": false,
		"bDestroy": true,
		'bAutoWidth': true
	});
	
	/* $("#it_cost_grid").tablesorter({widthFixed: false, widgets: ['zebra']});
	$('.data-table tr, .data-table th').hover(
		function() { $(this).addClass('over'); },
		function() { $(this).removeClass('over'); 
	}); */
	
	/* $('table#it_cost_grid').tableSearch({
		searchText:'Search ',
		searchPlaceHolder:'',
	}); */
	
});
if(filter_area_status==1) {
	$('#advance_search').show();
}

/* $('.search_input').on('keyup', function() { alert('1');
	$("#it_cost_grid").tablesorter({widthFixed: false, widgets: ['zebra']});
});
 */