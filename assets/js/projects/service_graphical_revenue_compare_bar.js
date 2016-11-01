/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspage is global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

$(function() {
	plot1 = $.jqplot('revenue_compare_bar', [inv_tot_arr_val], {
		// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
		animate: !$.jqplot.use_excanvas,
		seriesDefaults: {
			shadow: false,
			renderer:$.jqplot.BarRenderer,
			// Show point labels to the right ('e'ast) of each bar.
			// edgeTolerance of -15 allows labels flow outside the grid
			// up to 15 pixels.  If they flow out more than that, they
			// will be hidden.
			pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
			// Rotate the bar shadow as if bar is lit from top right.
			shadowAngle: 135,
			// Here's where we tell the chart it is oriented horizontally.
			rendererOptions: {
				barDirection: 'horizontal',
				barWidth: 12,
				varyBarColor: true,
				animation: { speed: 1000 }
			}
		},
		grid: {
				drawGridLines: true,        // wether to draw lines across the grid or not.
				gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
				background: '#ffffff',      // CSS color spec for background color of grid.
				borderColor: '#ffffff',     // CSS color spec for border around grid.
				borderWidth: 2.0,           // pixel width of border around grid.
				//backgroundColor: 'transparent', 
				drawBorder: false,
				shadow: false
		},
		highlighter: { show: false },
		seriesColors: ["#00e143", "#00a7e5"],
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
		series:[
            {label:'Current Year'},
            {label:'Last Year'},
        ],
		/* legend: {
			renderer: jQuery.jqplot.EnhancedLegendRenderer,
			show: true,
			placement: 'insideGrid',
			// marginTop: "-42px",
			rendererOptions: { numberRows: '5' }
			// placement: 'outsideGrid',
			// labels: ticks
		}, */
		axes: {
			xaxis: {
				min:0,
				label:'Values ('+default_currency_name+')--->',
				tickOptions:{
					//fontFamily:'Arial',
					//fontSize: '10pt',
					//fontWeight:"bold",
					//angle: -30,
					// min:0,
					show: false
				}
			},
			yaxis: {
				label:'Years --->',
				tickOptions:{
					fontFamily:'Arial',
					fontSize: '8pt',
					fontWeight:"bold"
					//angle: -30
					//show: false
				},
				renderer: $.jqplot.CategoryAxisRenderer,
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			}
		}
	});

	
	
	

});


//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////