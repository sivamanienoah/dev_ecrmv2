/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

$(function() {
	// var bill_value  = [2, 6, 7];
	// var inter_value = [10, 5, 3];
	// var xticks       = ['May', 'June', 'July'];
	var curr_yr_value  = curr_fiscal_inv_val;
	var last_yr_value  = last_fiscal_inv_val;
	var xticks         = line_x_axis_inv_val;
	var cur_name 	   = default_currency_name;
	var yaxis_label    = 'Month';
	
	plot2 = $.jqplot('revenue_compare_line', [last_yr_value, curr_yr_value], {
		// title: ' ',
		animate: !$.jqplot.use_excanvas,
		seriesDefaults:{
			// renderer:$.jqplot.BarRenderer,
			shadow: false,
			// pointLabels: { show: true, ypadding:3 },
			pointLabels: { show: true },
			rendererOptions: {
				barWidth: 34,
				animation: { speed: 1000 },
				fillToZero: true
			}
		},
		legend: {
			renderer: jQuery.jqplot.EnhancedLegendRenderer,
			show: true,
			placement: 'insideGrid',
			marginTop: "-42px",
			rendererOptions: { numberRows: '1' }
			// placement: 'outsideGrid',
			// labels: ticks
		},
		axesDefaults: {
			tickRenderer: $.jqplot.CanvasAxisTickRenderer ,         
			tickOptions: {
			  //angle: 10,
			  fontSize: '10pt'            
			},
			rendererOptions: { baselineWidth: 0.5, baselineColor: '#444444', animation: { speed: 1000 }, drawBaseline: true }
		},
		axes: {
			xaxis: {
				label:yaxis_label+' --->',
				renderer: $.jqplot.CategoryAxisRenderer,
				ticks: xticks
			},
			yaxis: {
				min:0,
				label:'Vaules(USD) --->',
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
            {label:'Last Year'},
            {label:'Current Year'}
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
		highlighter: { show: false },
		seriesColors: ["#00a7e5", "#00e143"],
		highlighter: { show: true, tooltipAxes: 'y', formatString: '%s', lineWidthAdjust:5.5, tooltipOffset:8 },
	});
});
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////