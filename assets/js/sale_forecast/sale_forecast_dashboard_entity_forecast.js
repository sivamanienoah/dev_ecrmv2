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
	var args =  forecast_entity_values;
	if(args != '') {
		plot3 = jQuery.jqplot('forecast_entity_chart', [args], {
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
				location: 'ne',
				border: true
			},
			seriesColors: ["#910000", "#027997", "#953579", "#422460", "#4b5de4", "#48596a", "#4bb2c5", "#0d233a", "#f0eded", "#492970", "#cc99cc", "#bfdde5", "#66ffcc", "#c747a3", "#ff99ff", "#ffff00", "#cc0000", "#a35b2e"],
			highlighter: {
				show: true,
			}
		});
		
		$('#forecast_entity_chart').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
			var formdata			  		 = {};
		
			/* formdata['customer']      		 = $("#customer").val();
			formdata['lead_ids']    		 = $("#lead_ids").val();
			formdata['month_year_from_date'] = $("#month_year_from_date").val();
			formdata['month_year_to_date']   = $("#month_year_to_date").val(); */

			formdata['month_year_from_date'] = forecast_entity_month_year_from_date;
			formdata['month_year_to_date']   = forecast_entity_month_year_to_date;
			
			formdata['clicked_data'] 		 = data;
			formdata[csrf_token_name] 		 = csrf_hash_token;
			
			$.ajax({
				type: "POST",
				url: site_base_url+'sales_forecast/showEntityChartDetails',
				dataType:"html",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					$('#entity_actual_charts_info_export').hide();
					$('#entity_actual_charts_info').empty();
					$('#compare_charts_info_export').hide();
					$('#compare_charts_info').empty();
					$('#entity_charts_info_export').hide();
					$('#entity_charts_info').show();
					$('#entity_charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
				},
				success: function(html){
					// alert(html);
					var str = formdata['clicked_data'][0].split('(');
					$('#item_name').val(str[0]);
					$('#item_category').val(0);
					$('#item_type').val('entity');
					$('#item_tag_name').html(str[0]);
					$('#entity_charts_info_export').show();
					$("#entity_charts_info").html(html);
					$('html, body').animate({ scrollTop: $("#entity_charts_info").offset().top }, 1000);
				}
			});
		});
		
		$( "#forecast_entity_chart_img" ).click(function() {
			var imgelem  = $('#forecast_entity_chart').jqplotToImageElem();
			var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
			var imgdata  = imageSrc;

			var url = site_base_url+"sales_forecast/savePdf/";
			var form = $('<form action="' + url + '" method="post">' +
			  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
			  '<input id="type" type="hidden" name="type" value="entity" />'+
			  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
			  '</form>');
			$('body').append(form);
			$(form).submit();
		});
		
	} else {
		$('#forecast_entity_chart').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
	}
});

//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////