/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

$(function() {
	$.jqplot.config.enablePlugins = true;

	// var forecast_values = [2, 6, 7, 10];
	// var actual_values   = [10, 5, 3, 2];
	// var ticks           = ['May', 'June', 'July', 'August'];
	
	var forecast_value = forecast_values;
	var actual_value   = actual_values;
	var ticks          = x_axis_values;
	var cur_name       = currency_name;
	
	plot2 = $.jqplot('forecast_compare_chart', [forecast_value, actual_value], {
		// title: ' ',
		animate: !$.jqplot.use_excanvas,
		seriesDefaults:{
			renderer:$.jqplot.BarRenderer,
			shadow: false,
			// pointLabels: { show: true, ypadding:3 },
			// pointLabels: { show: true },
			rendererOptions: {
				barWidth: 34,
				animation: {
					speed: 1000
				},
				fillToZero: true
			}
		},
		legend: {
			renderer: jQuery.jqplot.EnhancedLegendRenderer,
			show: true,
			placement: 'insideGrid',
			rendererOptions: {
				numberRows: '1',
			}
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
				label:'Values('+currency_name+') --->',
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer
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
            {label:'Forecast'},
            {label:'Actual'}
        ],
		grid: {
			drawGridLines: true,        // wether to draw lines across the grid or not.
			gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
			background: '#ffffff',      // CSS color spec for background color of grid.
			borderColor: '#ffffff',     // CSS color spec for border around grid.
			//borderWidth: 2.0,           // pixel width of border around grid.
			//backgroundColor: 'transparent', 
			drawBorder: false,
			shadow: false
		},
		highlighter: {
			show: false
		},
		seriesColors: ["#00a7e5", "#00e143"]
	});
	$('#forecast_compare_chart').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
		var formdata			  		 = {};
	
		formdata['entity']      		 = compare_entity;
		formdata['services']      		 = compare_service;
		formdata['practices']      		 = compare_practice;
		formdata['industries']      	 = compare_industry;
		formdata['customer']      		 = compare_customer;
		formdata['lead_ids']    		 = compare_lead_ids;
		formdata['month_year_from_date'] = compare_month_year_from_date;
		formdata['month_year_to_date']   = compare_month_year_to_date;
		
		formdata['clicked_month'] 		 = month_no_arr[pointIndex];
		formdata['clicked_type'] 		 = seriesIndex;
		formdata[csrf_token_name] 		 = csrf_hash_token;
		
		$.ajax({
			type: "POST",
			url: site_base_url+'sales_forecast/showCompareChartDetails',
			dataType:"html",                                                                
			data: formdata,
			cache: false,
			beforeSend:function(){
				$('#compare_charts_info_export').hide();
				$('#entity_actual_charts_info_export').hide();
				$('#entity_actual_charts_info').empty();
				$('#entity_charts_info_export').hide();
				$('#entity_charts_info').empty();
				$('#compare_charts_info').show();
				$('#compare_charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success: function(html){
				// alert(html);
				$('#compare_item_name').val(formdata['clicked_month']);
				$('#compare_item_category').val(formdata['clicked_type']);
				$('#compare_item_type').val('compare');
				$('#compare_item_tag_name').html(month_name_arr[pointIndex]);
				$('#compare_charts_info_export').show();
				$("#compare_charts_info").html(html);
				$('html, body').animate({ scrollTop: $("#compare_charts_info").offset().top }, 1000);
			}
		});
	});
	$('.jqplot-xaxis-tick').css({ cursor: "pointer", zIndex: "1" }).click(function () {
		// console.info(month_no_arr[($(this).index()-1)]);
		
		var formdata			  		 = {};

		formdata['entity']      		 = compare_entity;
		formdata['services']      		 = compare_service;
		formdata['practices']      		 = compare_practice;
		formdata['industries']      	 = compare_industry;
		formdata['customer']      		 = compare_customer;
		formdata['lead_ids']    		 = compare_lead_ids;
		formdata['month_year_from_date'] = compare_month_year_from_date;
		formdata['month_year_to_date']   = compare_month_year_to_date;
		
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
				$('#compare_charts_info_export').hide();
				$('#compare_charts_info').show();
				$('#compare_charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success: function(html){
				// alert(html);
				$('#compare_item_name').val(formdata['clicked_month']);
				$('#compare_item_category').val(2);
				$('#compare_item_type').val('compare');
				$('#compare_item_tag_name').html(formdata['display_month']);
				$('#compare_charts_info_export').show();
				$("#compare_charts_info").html(html);
				$('html, body').animate({ scrollTop: $("#compare_charts_info").offset().top }, 1000);
			}
		});
		
	});
});

$('.grid-close').click(function() {
	$('#compare_charts_info_export, #compare_charts_info').slideUp('fast', function(){ 
		$('#compare_charts_info_export, #compare_charts_info').css('display','none');
	});
})



//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////