/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

$(function() {
	var series_lbl = {};
	for(var i = 0; i < tre_pra_month_label.length; i++) {
		series_lbl[i] = { label: tre_pra_month_label[i] };
	}
	// console.info(series_lbl)
	var curr_yr_value  = [2, 6, 7];
	var last_yr_value  = [10, 5, 3];
	// var xticks         = ['May', 'June', 'July'];
	// var curr_yr_value  = curr_fiscal_inv_val;
	// var last_yr_value  = last_fiscal_inv_val;
	var xticks         = tre_pra_month_x_val;
	var cur_name 	   = default_currency_name;
	var yaxis_label    = 'Month';
	
	plot2 = $.jqplot('revenue_trend', tre_pra_month_value, {
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
		series:series_lbl,
		legend:{
			renderer: jQuery.jqplot.EnhancedLegendRenderer,
			show:true,
			// placement: 'outside',
			fontSize: '8pt',
			rendererOptions: { numberRows:1 },
			marginTop: "-43px",
			right: "-8px",
			// location: 's',
			border: false
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
		highlighter: { show: false },
		seriesColors: ["#1a5a2e", "#027997", "#cc0000", "#c747a3", "#910000", "#66ffcc", "#bfdde5", "#cc99cc", "#492970", "#f0eded", "#0d233a", "#4bb2c5", "#a35b2e", "#4b5de4", "#422460", "#953579"],
		highlighter: { show: true, tooltipAxes: 'y', formatString: '%s', lineWidthAdjust:5.5, tooltipOffset:8 },
	});
});
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////