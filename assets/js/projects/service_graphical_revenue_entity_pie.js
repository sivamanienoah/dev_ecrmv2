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
	var args =  revenue_entity_val;
	if(args != '') {
		plot3 = jQuery.jqplot('revenue_entity_pie', [args], {
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
				fontSize: '8pt',
				rendererOptions: {
					numberRows:2
				},
				location: 's',
				border: false
			},
			seriesColors: ["#1a5a2e", "#027997", "#cc0000", "#c747a3", "#910000", "#66ffcc", "#bfdde5", "#cc99cc", "#492970", "#f0eded", "#0d233a", "#4bb2c5", "#a35b2e", "#4b5de4", "#422460", "#953579"],
			highlighter: {
				show: true,
			}
		});
		
		$('#actual_entity_chart').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
		
			var formdata			  		 = {};
		
			formdata['month_year_from_date'] = actual_entity_month_year_from_date;
			formdata['month_year_to_date']   = actual_entity_month_year_to_date;
			
			formdata['clicked_data'] 		 = data;
			formdata['clicked_type'] 		 = 'A';
			formdata[csrf_token_name] 		 = csrf_hash_token;
			
			$.ajax({
				type: "POST",
				url: site_base_url+'sales_forecast/showClickChartDetails',
				dataType:"html",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					$('#entity_actual_charts_info_export').hide();
					$('#compare_charts_info_export').hide();
					$('#entity_charts_info_export').hide();
					$('#entity_charts_info').empty();
					$('#compare_charts_info').empty();
					$('#entity_actual_charts_info').show();
					$('#entity_actual_charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
				},
				success: function(html){
					// alert(html);
					var str = formdata['clicked_data'][0].split('(');
					$('#actual_item_name').val(str[0]);
					$('#actual_item_category').val(1);
					$('#actual_item_type').val('entity');
					$('#actual_item_tag_name').html(str[0]);
					$('#entity_actual_charts_info_export').show();
					$("#entity_actual_charts_info").html(html);
					$('html, body').animate({ scrollTop: $("#entity_actual_charts_info").offset().top }, 1000);
				}
			});
		});
		
		$( "#actual_entity_chart-img" ).click(function() {
			var imgelem  = $('#actual_entity_chart').jqplotToImageElem();
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
		$('#actual_entity_chart').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
	}
});


$('.grid-close').click(function() {
	$('#charts_info_export, #charts_info').slideUp('fast', function(){ 
		$('#charts_info_export, #charts_info').css('display','none');
	});
})



//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////