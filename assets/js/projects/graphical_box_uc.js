$(document).ready(function(){
	// console.info(uc_all_graph_data);
	// alert(uc_all_graph_data.total.practice_name);
	/*for total utiliztion cost graph*/
	var s2 = [uc_all_graph_data.total.ytd_billable];
	var plot1 = 'plot_total';
	plot1 = $.jqplot('total', [s2],{
		seriesDefaults: {
			renderer: $.jqplot.MeterGaugeRenderer,
			rendererOptions: {
				label: uc_all_graph_data.total.practice_name,
				labelPosition: 'bottom',
				labelHeightAdjust: -5,
				intervalInnerRadius: 148,
				intervalOuterRadius: 120,
				background: "transparent",
				ringColor: "#bbc6d0",
				ringWidth: 1,
				hubRadius: 6,
				needleThickness: 5,
				ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
				min: 0,
				max: 100,
				intervals:[10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
				intervalColors:['#fa0002','#fb3915', '#f95d10', '#f59502', '#f7ac13', '#ffd107','#f3e219', '#d8ed22', '#b0d112', '#9eba1a' ],
				smooth: true,
				animation: { show: true }
			}
		}
	});
	$.each(uc_graph_data, function (index, value) {
		// alert( index + ' ' + value.practice_name );
		var s1 = [value.ytd_billable];
		var plot = 'plot_'+index;
		plot = $.jqplot(index, [s1],{
			seriesDefaults: {
				renderer: $.jqplot.MeterGaugeRenderer,
				rendererOptions: {
					label: value.practice_name+' - '+value.ytd_billable+' %',
					labelPosition: 'bottom',
					labelHeightAdjust: 5,
					background: "transparent",
					ringColor: "#bbc6d0",
					ringWidth: 1,
					hubRadius: 5,
					needleThickness: 3,
					intervalInnerRadius: 33,
					intervalOuterRadius: 45,
					ticks: [0, 20, 40, 60, 80, 100],
					min: 0,
					max: 100,
					intervals:[10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
					intervalColors:['#fa0002','#fb3915', '#f95d10', '#f59502', '#f7ac13', '#ffd107','#f3e219', '#d8ed22', '#b0d112', '#9eba1a' ],
					smooth: true,
					animation: { show: true }
				}
			}
		});
	});
});