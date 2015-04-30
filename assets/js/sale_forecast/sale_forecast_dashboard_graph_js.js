/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

/*Graph*/
$(function() {
	// var args = [ ['Asia Pacific', 9],['Europe', 4],['North America', 7],['APAC SAP', 3],['Middle East', 4] ];
	var args =  entity_values;
	if(args != '') {
		plot3 = jQuery.jqplot('entity-chart', [args], {
			title: ' ',
			gridPadding: {top:25, bottom:24, left:0, right:0},
			animate: !$.jqplot.use_excanvas,
			animateReplot: true,
			seriesDefaults:{
				shadow: false,
				renderer:$.jqplot.PieRenderer, 
				trendline:{ show:false }, 
				rendererOptions: { 
					padding: 8,
					sliceMargin: 2,
					showDataLabels: true
				},
				highlighter: {
					show: true,
					formatString:'%s',
					tooltipLocation:'n',
					tooltipAxes: 'yref',
					tooltipAxisY: 90,
					useAxesFormatters:false
				}				 
			},
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
			legend:{
				show:true, 
				fontSize: '10pt',
				// placement : "outside",
				location: 'e',
				border: true
			},
			highlighter: {
				show: true,
			}
		});
		
		$('#entity-chart').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
		
			var formdata			  		 = {};
		
			formdata['entity']      		 = $("#entity").val();
			formdata['customer']      		 = $("#customer").val();
			formdata['lead_ids']    		 = $("#lead_ids").val();
			formdata['month_year_from_date'] = $("#month_year_from_date").val();
			formdata['month_year_to_date']   = $("#month_year_to_date").val();
			
			formdata['clicked_data'] 		 = data;
			formdata[csrf_token_name] 		 = csrf_hash_token;
			
			$.ajax({
				type: "POST",
				url: site_base_url+'sales_forecast/showEntityChartDetails',
				dataType:"html",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					$('#charts_info_export').hide();
					$('#charts_info').show();
					$('#charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
				},
				success: function(html){
					// alert(html);
					var str = formdata['clicked_data'][0].split('(');
					$('#item_name').val(str[0]);
					$('#item_category').val(0);
					$('#item_type').val('entity');
					$('#item-tag-name').html(str[0]);
					$('#charts_info_export').show();
					$("#charts_info").html(html);
					$('html, body').animate({ scrollTop: $("#charts_info").offset().top }, 1000);
				}
			});
		});
	} else {
		$('#entity-chart').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
	}
});

$(function() {
	$.jqplot.config.enablePlugins = true;

	// var forecast_values = [2, 6, 7, 10];
	// var actual_values   = [10, 5, 3, 2];
	// var ticks           = ['May', 'June', 'July', 'August'];
	
	var forecast_value = forecast_values;
	var actual_value   = actual_values;
	var ticks          = x_axis_values;
	var cur_name       = currency_name;
	
	plot2 = $.jqplot('forecast-compare-chart', [forecast_value, actual_value], {
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
			show: true,
			placement: 'insideGrid',
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
				label:'Month--->',
				renderer: $.jqplot.CategoryAxisRenderer,
				ticks: ticks
			},
			yaxis: {
				label:'Values('+currency_name+')--->',
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
		}
	});
	$('#forecast-compare-chart').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
		var formdata			  		 = {};
	
		formdata['entity']      		 = $("#entity").val();
		formdata['customer']      		 = $("#customer").val();
		formdata['lead_ids']    		 = $("#lead_ids").val();
		formdata['month_year_from_date'] = $("#month_year_from_date").val();
		formdata['month_year_to_date']   = $("#month_year_to_date").val();
		
		formdata['clicked_month'] 		 = month_no_arr[pointIndex];
		formdata['clicked_type'] 		 = seriesIndex;
		formdata[csrf_token_name] 		 = csrf_hash_token;
		
		$.ajax({
			type: "POST",
			url: site_base_url+'sales_forecast/showForecastCompareChartDetails',
			dataType:"html",                                                                
			data: formdata,
			cache: false,
			beforeSend:function(){
				$('#charts_info_export').hide();
				$('#charts_info').show();
				$('#charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success: function(html){
				// alert(html);
				$('#item_name').val(formdata['clicked_month']);
				$('#item_category').val(formdata['clicked_type']);
				$('#item_type').val('compare');
				$('#item-tag-name').html(formdata['clicked_month']);
				$('#charts_info_export').show();
				$("#charts_info").html(html);
				$('html, body').animate({ scrollTop: $("#charts_info").offset().top }, 1000);
			}
		});
	});
	$('.jqplot-xaxis-tick').css({ cursor: "pointer", zIndex: "1" }).click(function () {
		// console.info(month_no_arr[($(this).index()-1)]);
		
		var formdata			  		 = {};
	
		formdata['entity']      		 = $("#entity").val();
		formdata['customer']      		 = $("#customer").val();
		formdata['lead_ids']    		 = $("#lead_ids").val();
		formdata['month_year_from_date'] = $("#month_year_from_date").val();
		formdata['month_year_to_date']   = $("#month_year_to_date").val();
		
		formdata['clicked_month'] 		 = month_no_arr[($(this).index()-1)];
		formdata['clicked_type'] 		 = 2;
		formdata[csrf_token_name] 		 = csrf_hash_token;
		
		$.ajax({
			type: "POST",
			url: site_base_url+'sales_forecast/showForecastCompareChartDetails',
			dataType:"html",                                                                
			data: formdata,
			cache: false,
			beforeSend:function(){
				$('#charts_info_export').hide();
				$('#charts_info').show();
				$('#charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success: function(html){
				// alert(html);
				$('#item_name').val(formdata['clicked_month']);
				$('#item_category').val(2);
				$('#item_type').val('compare');
				$('#item-tag-name').html(formdata['clicked_month']);
				$('#charts_info_export').show();
				$("#charts_info").html(html);
				$('html, body').animate({ scrollTop: $("#charts_info").offset().top }, 1000);
			}
		});
		
	});
});

$('.grid-close').click(function() {
	$('#charts_info_export, #charts_info').slideUp('fast', function(){ 
		$('#charts_info_export, #charts_info').css('display','none');
	});
})



//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////