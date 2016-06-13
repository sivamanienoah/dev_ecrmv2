/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

$(function() {
	$.jqplot.config.enablePlugins = true;
	
	// var bill_value  = [2, 6, 7, 10];
	// var inter_value = [10, 5, 3];
	// var ticks       = ['May', 'June', 'July'];
	
	var bill_value    = billable_value;
	var inter_value   = internal_value;
	var nonbill_value = non_billable_value;
	var ticks         = x_axis_values;
	var cur_name      = currency_name;
	var yaxis_label   = '';
	var yaxis_lbl     = '';
	var lbl_symbol    = '';
	if(value_based == 'value') {
		yaxis_lbl = 'Value';
	} else if(value_based == 'percent') {
		lbl_symbol = ' %';
		yaxis_lbl = 'Percentage';
	}

	if(graph_based == 'hour')
	yaxis_label = 'hour';
	else if(graph_based == 'cost')
	yaxis_label = 'cost';
	else if(graph_based == 'directcost')
	yaxis_label = 'direct cost';
	
	plot2 = $.jqplot('trend_analysis_chart', [bill_value, inter_value, nonbill_value], {
		// title: ' ',
		animate: !$.jqplot.use_excanvas,
		seriesDefaults:{
			// renderer:$.jqplot.BarRenderer,
			shadow: false,
			// pointLabels: { show: true, ypadding:3 },
			pointLabels: { show: true, formatString: '%s'+lbl_symbol },
			rendererOptions: {
				barWidth: 34,
				animation: { speed: 1000 },
				fillToZero: true
			}
		},
		legend: {
			renderer: jQuery.jqplot.EnhancedLegendRenderer,
			show: true,
			location: 'se',
			placement: 'insideGrid',
			marginBottom: "288px",
			rendererOptions: { numberRows: '1', }
			// placement: 'outsideGrid',
			// labels: ticks
		},
		axesDefaults: {
			tickRenderer: $.jqplot.CanvasAxisTickRenderer ,         
			tickOptions: {
			  //angle: 10,
			  fontSize: '10pt'            
			},
			rendererOptions: {
				baselineWidth: 0.5,
				baselineColor: '#444444',
				drawBaseline: true
			}
		},
		axes: {
			xaxis: {
				label:'Month --->',
				renderer: $.jqplot.CategoryAxisRenderer,
				ticks: ticks
			},
			yaxis: {
				min:0,
				label:yaxis_lbl+'('+yaxis_label+') --->',
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				// tickOptions:{ formatString:'$%.2f' }
			}
		},
		/* series: [{
			markerOptions: {
				show: true
			},
			rendererOptions: {
				smooth: false
			}
		}], */
		series:[
            {label:'Billable'},
            {label:'Non-Billable'},
			{label:'Internal'}
        ],
		grid: {
			drawGridLines: true,        // wether to draw lines across the grid or not.
			gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
			background: '#ffffff',      // CSS color spec for background color of grid.
			borderColor: '#ffffff',     // CSS color spec for border around grid.
			//borderWidth: 2.0,         // pixel width of border around grid.
			//backgroundColor: 'transparent', 
			drawBorder: false,
			shadow: false
		},
		highlighter: { show: true, tooltipAxes: 'y', formatString: '%s', lineWidthAdjust:5.5, tooltipOffset:8 },
		seriesColors: ["#00e143", "#ff0000", "#00a7e5"]
	});
	
	$('#trend_analysis_chart').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
		var resource_data = '';
		var dept_type     = '';
		var formdata			  		 = {};
	
		// formdata['entity']      		 = compare_entity;
		// formdata['customer']      	 = compare_customer;
		// formdata['lead_ids']    		 = compare_lead_ids;
		// formdata['start_date'] = start_date;
		// formdata['end_date']   = end_date;
		
		formdata['clicked_month'] 		 = month_no_arr[pointIndex];
		formdata['clicked_type'] 		 = seriesIndex;
		formdata[csrf_token_name] 		 = csrf_hash_token;
		if(seriesIndex==0){
			resource_data = 'Billable';
		} else if(seriesIndex==1) {
			resource_data = 'Internal';
		} else if(seriesIndex==2) {
			resource_data = 'Non-Billable';
		}
		// alert($('#hdept_ids').val())
		if($('#hdept_ids').val()==''){
			dept_type = 1;
		} else {
			var hdept_type = $('#hdept_ids').val();
			if(hdept_type == '10,11') {
				dept_type = 1;
			} else if(hdept_type == '10'){
				dept_type = 2;
			} else if(hdept_type == '11'){
				dept_type = 3;
			} else {
				dept_type = 1;
			}
		}
		getTrendDrillData(resource_data, dept_type, month_name_arr[pointIndex]);
		/* $.ajax({
			type: "POST",
			url: site_base_url+'projects/dashboard/get_data',
			dataType:"html",                                                                
			data: formdata,
			cache: false,
			beforeSend:function(){
				$('#trend_analysis_info_export').hide();
				$('#entity_actual_charts_info_export').hide();
				$('#entity_actual_charts_info').empty();
				$('#entity_charts_info_export').hide();
				$('#entity_charts_info').empty();
				$('#trend_analysis_info').show();
				$('#trend_analysis_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success: function(html){
				// alert(html);
				$('#compare_item_name').val(formdata['clicked_month']);
				$('#compare_item_category').val(formdata['clicked_type']);
				$('#compare_item_type').val('compare');
				$('#compare_item_tag_name').html(month_name_arr[pointIndex]);
				$('#trend_analysis_info_export').show();
				$("#trend_analysis_info").html(html);
				$('html, body').animate({ scrollTop: $("#trend_analysis_info").offset().top }, 1000);
			}
		}); */
	});
	/* 
	$('.jqplot-xaxis-tick').css({ cursor: "pointer", zIndex: "1" }).click(function () {
		// console.info(month_no_arr[($(this).index()-1)]);
		
		var formdata			  		 = {};

		// formdata['entity']      		 = compare_entity;
		// formdata['customer']      	 = compare_customer;
		// formdata['lead_ids']    		 = compare_lead_ids;
		formdata['start_date'] = start_date;
		formdata['end_date']   = end_date;
		
		formdata['clicked_month'] 		 = month_no_arr[($(this).index()-1)];
		formdata['display_month'] 		 = month_name_arr[($(this).index()-1)];
		formdata['clicked_type'] 		 = 2;
		formdata[csrf_token_name] 		 = csrf_hash_token;
		
		$.ajax({
			type: "POST",
			url: site_base_url+'sales_forecast/showCompareChartDetails',
			dataType:"html",                                                                
			data: formdata,
			cache: false,
			beforeSend:function(){
				$('#trend_analysis_info_export').hide();
				$('#trend_analysis_info').show();
				$('#trend_analysis_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success: function(html){
				// alert(html);
				$('#compare_item_name').val(formdata['clicked_month']);
				$('#compare_item_category').val(2);
				$('#compare_item_type').val('compare');
				$('#compare_item_tag_name').html(formdata['display_month']);
				$('#trend_analysis_info_export').show();
				$("#trend_analysis_info").html(html);
				$('html, body').animate({ scrollTop: $("#trend_analysis_info").offset().top }, 1000);
			}
		});
		
	}); */
});
function getTrendDrillData(resource_type, dept_type, drill_month)
{
	$('#filter_group_by').prop('selectedIndex',0);
	if($('#department_ids').val() == null) {
		$('#hdept_ids').val('');
	} else {
		$('#hdept_ids').val($('#department_ids').val());
	}
	if($('#practice_ids').val() == null) {
		$('#hprac_ids').val('');
	} else {
		$('#hprac_ids').val($('#practice_ids').val());
	}
	// $('#hmonth_year').val($('#month_year_from_date').val());
	$('#hmonth_year').val(drill_month);
	$('#hskill_ids').val($('#skill_ids').val())
	$('#hmember_ids').val($('#member_ids').val())
	if($('#exclude_leave').attr('checked'))
	$('#hexclude_leave').val(1);
	if($('#exclude_holiday').attr('checked'))
	$('#hexclude_holiday').val(1)
	
	var formdata = $('#fliter_data_trend').serialize();
	
	$.ajax({
		type: "POST",
		url: site_base_url+'projects/dashboard/get_trend_drill_data/',                                                        
		data: formdata+'&resource_type='+resource_type+'&dept_type='+dept_type+'&filter_group_by=0',
		cache: false,
		beforeSend:function() {
			$('#filter_group_by').prop('selectedIndex',0);
			$('#drilldown_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		},
		success: function(data) {
			$('#drilldown_data').html(data);
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		}                                                                                   
	});
}

/* $('.grid-close').click(function() {
	$('#trend_analysis_info_export, #trend_analysis_info').slideUp('fast', function(){ 
		$('#trend_analysis_info_export, #trend_analysis_info').css('display','none');
	});
}) */

$('#rd_grph_hr').click(function() {
	$('#hgraph_based').val('hour');
	$('#hidgraph_based').val('hour');
	$( "#project_dashboard" ).submit();
});
$('#rd_grph_cost').click(function() {
	$('#hgraph_based').val('cost');
	$('#hidgraph_based').val('cost');
	$( "#project_dashboard" ).submit();
})
$('#rd_grph_directcost').click(function() {
	$('#hgraph_based').val('directcost');
	$('#hidgraph_based').val('directcost');
	$( "#project_dashboard" ).submit();
})

$('#rd_value').click(function() {
	$('#hvalue_based').val('value');
	$('#hidvalue_based').val('value');
	$( "#project_dashboard" ).submit();
});
$('#rd_percent').click(function() {
	$('#hvalue_based').val('percent');
	$('#hidvalue_based').val('percent');
	$( "#project_dashboard" ).submit();
});
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////