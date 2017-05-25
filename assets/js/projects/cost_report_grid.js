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
	
	$("#it_cost_grid thead tr th").click(function(){
		$("#it_cost_grid thead tr th").addClass("desc_opt");
		//$("#it_cost_grid thead tr th").removeClass("desc_asc_opt");
			if($(this).hasClass('desc_asc_opt')){
				$("#it_cost_grid thead tr th").removeClass("desc_asc_opt");				
			}else{
				$("#it_cost_grid thead tr th").removeClass("desc_asc_opt");
				$(this).addClass("desc_asc_opt");
			}
	});
	
	
	
	//data table
/* 	$('#it_cost_grid').dataTable({
		"bInfo": false,
		"bFilter": false,
		"bPaginate": false,
		"bProcessing": false,
		"bServerSide": false,
		"bLengthChange": false,
		"bDestroy": true,
		'bAutoWidth': true
	}); */
	
	$("#it_cost_grid").tablesorter({widthFixed: false, widgets: ['zebra']});
	$('.data-table tr, .data-table th').hover(
		function() { $(this).addClass('over'); },
		function() { $(this).removeClass('over'); 
	});
	
	$('table#it_cost_grid').tableSearch({
		searchText:'Search ',
		searchPlaceHolder:''
	},
	$("#it_cost_grid").tablesorter({widthFixed: false, widgets: ['zebra']});
	);
	
});
if(filter_area_status==1) {
	$('#advance_search').show();
}

/* $('#cost_rpt_search').on('keyup', function() {
	var value = $(this).val();
	if (value.length >= 3) {
		var patt = new RegExp(value, "i");

		$('#it_cost_grid').find('tr').each(function() {		
			if (!($(this).find('td').text().search(patt) >= 0)) {
				$(this).not('#cost_rpt_head').hide();			
			}
			if (($(this).find('td').text().search(patt) >= 0)) {
				$(this).show();
			}
			var getLength=$('#it_cost_grid tbody tr:visible').length;
			if(getLength == 0) {		
			   $('.emptyerror').show();
			} else {
			   $('.emptyerror').hide();
			}
			if(getLength > 1) {
				$('.emptyerror').hide();
			}
		});
		$("#it_cost_grid").tablesorter({widthFixed: false, widgets: ['zebra']});
	}	
}); */
