/*
 *@Dashboard Jquery
*/

if(viewlead==1) {
	$(document).ready(function() {
		if (dashboard_s1!='') {
			plot1 = $.jqplot('funnel1', [dashboard_s1], {
			//title: 'Leads - Current Pipeline',
			legend: {
			   show: true,
			   rendererOptions: {
				   border: false,
				   fontSize: '10pt',
				   location: 'e'
			   }
			},
			seriesDefaults: {
				shadow: false,
				renderer: $.jqplot.FunnelRenderer
			},
			grid: {
					drawGridLines: true,        // wether to draw lines across the grid or not.
					gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
					background: '#ffffff',      // CSS color spec for background color of grid.
					drawBorder: false,
					shadow: false
			},
			seriesColors: ["#027997", "#910000", "#bfdde5", "#8bbc21", "#1aadce", "#492970", "#2f7ed8", "#0d233a", "#48596a", "#640cb1", "#eaa228", "#422460"]
			});
			
			$('#funnel1').bind('jqplotDataClick',function (ev, seriesIndex, pointIndex, data) {
				// alert($('#val_export').val());
				
				var def_search_id = $("#val_export").val();
	
				if(!isNaN(def_search_id)) {
					var formdata = {};
					formdata['search_id'] = def_search_id;
				} else if( def_search_id == 'search' ) {
					var formdata = advanceFilterParameterForDataClick();
				} else if( def_search_id == 'no_search' ) {
					var formdata = {};
				} else {
					var formdata = {};
				}
				formdata['data'] 	  = data;
				formdata['type'] 	  = 'funnel';
				formdata[csrf_token_name] = csrf_hash_token;
				
				/* if (filter_toggle_stat == 'toggle') {
					var formdata = advanceFilterParameterForDataClick();
				} else {
					var formdata = {};
				} */
				

				$.ajax({
					type: "POST",
					url: site_base_url+'dashboard/showLeadsDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info').empty();
						$('#charts_info').show();
						$('#charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
					},
					success: function(html){
						//alert(html.html);
						$('#charts_info').empty();
						$("#charts_info").show();
						if (html.html == 'NULL') {
							$('#charts_info').html('');
						} else {
							$('#charts_info').show();
							$('#charts_info').html(html.html);
							
							$('#example_funnel').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6]; 
										//var str = TotalMarks.split(" "); //for USD 1200.00
										//cost += parseFloat(str[1]);//for USD 1200.00
										cost += parseFloat(TotalMarks);
									}
									var nCells = nRow.getElementsByTagName('td');
									//nCells[1].innerHTML = "USD " + cost.toFixed(2); //for USD 1200.00
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			});
			
			$( "#funnelimg" ).click(function() {
				var imgelem = $('#funnel1').jqplotToImageElem();
				var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
				var imgdata = imageSrc;
				var base_url = site_base_url;		
			
				var url = base_url+"dashboard/savePdf/";
				var form = $('<form action="' + url + '" method="post">' +
				  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
				  '</form>');
				$('body').append(form);
				$(form).submit();
			});
		
		} else {
			$('#funnel1').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		}
	});

	$(document).ready(function() {
	   if (dashboard_s2!='') { 
			$.jqplot.config.enablePlugins = true;
			var plot2 = $.jqplot('pie1', [dashboard_s2], {
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
					tooltipLocation:'sw', 
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
				fontSize: '9pt',
				location: 'e',
				border: false
			},
			seriesColors: ["#422460", "#da7b00", "#9c1a4b", "#48596a", "#0d233a", "#2f7ed8", "#492970", "#1aadce", "#8bbc21", "#bfdde5", "#910000", "#027997"]
			});

			$('#pie1').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
				/* if (filter_toggle_stat == 'toggle') {
					var formdata = advanceFilterParameterForDataClick();
				} else {
					var formdata = {};
				} */
				var def_search_id = $("#val_export").val();
	
				if(!isNaN(def_search_id)) {
					var formdata = {};
					formdata['search_id'] = def_search_id;
				} else if( def_search_id == 'search' ) {
					var formdata = advanceFilterParameterForDataClick();
				} else if( def_search_id == 'no_search' ) {
					var formdata = {};
				} else {
					var formdata = {};
				}
				formdata['data'] 		  = data;
				formdata['type'] 		  = 'pie1';
				formdata[csrf_token_name] = csrf_hash_token;
				
				$.ajax({
					type: "POST",
					url: site_base_url+'dashboard/showLeadsDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info').empty();
						$('#charts_info').show();
						$('#charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
					},
					success: function(html){
						//alert(html.html);
						$('#charts_info').empty();
						$("#charts_info").show();
						if (html.html == 'NULL') {
							$('#charts_info').html('');
						} else {
							$('#charts_info').show();
							$('#charts_info').html(html.html);
							
							$('#example_pie1').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
									//alert(nRow);
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6];
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			});

			$( "#pieimg" ).click(function() {
				var imgelem = $('#pie1').jqplotToImageElem();
				var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
				var imgdata = imageSrc;
				var base_url = site_base_url;		

				var url = base_url+"dashboard/savePdf/";
				var form = $('<form action="' + url + '" method="post">' +
				  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
				  '</form>');
				$('body').append(form);
				$(form).submit();
				
			});
		} else { 
			$('#pie1').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		} 
	});


	$(document).ready(function() {
		$.jqplot.config.enablePlugins = true;
		var ticks = dashboard_s3_name;
		if (dashboard_s3!='') { 
			var plot3 = $.jqplot('bar1', [dashboard_s3,[],[]], {
				title: {
					//text: 'Lead Indicator',   // title for the plot,
					//show: true,
				},
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					shadow: false,
					//pointLabels: { show: true, ypadding:3 },
					pointLabels: { show: true },
					rendererOptions: {
						barWidth: 34,
						varyBarColor: true,
						animation: {
							speed: 4000
						}
					}
				},
				legend: {
					show: true,
					placement: 'insideGrid',
					labels: ticks
				},
				//seriesColors: [<?php echo $resColors; ?>],
				seriesColors: ["#910000", "#f47123", "#2c84c5"],
				axesDefaults: {
					tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
					tickOptions: {
					  //fontFamily:"Arial",
					  //textColor:'black',
					}
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
						label:'Lead Indicator--->',
						renderer: $.jqplot.CategoryAxisRenderer,
						tickOptions:{
							
							show: false
						}
					},
					yaxis: {
						label:'No. of Leads--->',
						labelRenderer: $.jqplot.CanvasAxisLabelRenderer
					}
				},
				series: [{
					markerOptions: {
						show: true
					},
					rendererOptions: {
						smooth: false
					}
				}],
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

			$('#bar1').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
				/* if (filter_toggle_stat == 'toggle') {
					var formdata = advanceFilterParameterForDataClick();
				} else {
					var formdata = {};
				} */
				var def_search_id = $("#val_export").val();
	
				if(!isNaN(def_search_id)) {
					var formdata = {};
					formdata['search_id'] = def_search_id;
				} else if( def_search_id == 'search' ) {
					var formdata = advanceFilterParameterForDataClick();
				} else if( def_search_id == 'no_search' ) {
					var formdata = {};
				} else {
					var formdata = {};
				}
				formdata['gid'] 	  = pointIndex;
				formdata['type'] 	  = 'bar1';
				formdata[csrf_token_name] = csrf_hash_token;
				
				$.ajax({
					type: "POST",
					url: site_base_url+'dashboard/showLeadDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#leads-current-activity-list').empty();
						$('#leads-current-activity-list').show();
						$('#leads-current-activity-list').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
					},
					success: function(html){
						//alert(html.html);
						$('#leads-current-activity-list').empty();
						$("#leads-current-activity-list").show();
						if (html.html == 'NULL') {
							$('#leads-current-activity-list').html('');
						} else {
							$('#leads-current-activity-list').show();
							$('#leads-current-activity-list').html(html.html);
							
							$('#example_bar1').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
									//var TotalMarks = 0;
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6]; 
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#leads-current-activity-list").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			});
				
			$( "#barimg" ).click(function() {
				//var imgelem = evt.data.chart.jqplotToImageElem();
				var imgelem = $('#bar1').jqplotToImageElem();
				var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
				//alert(imageSrc);// return false;
				//open(imageSrc); // this will open the image in another tab
				var imgdata = imageSrc;
				var base_url = site_base_url;		

				var url = base_url+"dashboard/savePdf/";
				var form = $('<form action="' + url + '" method="post">' +
				  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
				  '</form>');
				$('body').append(form);
				$(form).submit();
			});
		} else { 
			$('#bar1').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		} 
	});

	$(document).ready(function() {
		// For horizontal bar charts, x and y values must will be "flipped"
		// from their vertical bar counterpart.
		$.jqplot.config.enablePlugins = true;
		if (dashboard_s4!='') { 
			var plot5 = $.jqplot('line1', [dashboard_s4], {
			//title:'Lead Aging',
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
					barWidth: 25,
					varyBarColor: true,
					animation: {
						speed: 4000
					}
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
			highlighter: { 
					show: false
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
					label:'No. of Leads--->',
					tickOptions:{
						//fontFamily:'Arial',
						//fontSize: '10pt',
						//fontWeight:"bold",
						//angle: -30,
						show: false
					}
				},
				yaxis: {
					label:'No. of Days(from lead creation)--->',
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

			$('#line1').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
				//alert(pointIndex);
				/* if (filter_toggle_stat == 'toggle') {
					var formdata = advanceFilterParameterForDataClick();
				} else {
					var formdata = {};
				} */
				var def_search_id = $("#val_export").val();
	
				if(!isNaN(def_search_id)) {
					var formdata = {};
					formdata['search_id'] = def_search_id;
				} else if( def_search_id == 'search' ) {
					var formdata = advanceFilterParameterForDataClick();
				} else if( def_search_id == 'no_search' ) {
					var formdata = {};
				} else {
					var formdata = {};
				}
				formdata['gid'] 	  = pointIndex;
				formdata['type'] 	  = 'line1';
				formdata[csrf_token_name] = csrf_hash_token;

				$.ajax({
					type: "POST",
					url: site_base_url+'dashboard/showLeadDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info2').empty();
						$('#charts_info2').show();
						$('#charts_info2').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
					},
					success: function(html){
						//alert(html.html);
						$('#charts_info2').empty();
						$("#charts_info2").show();
						if (html.html == 'NULL') {
							$('#charts_info2').html('');
						} else {
							$('#charts_info2').show();
							$('#charts_info2').html(html.html);
							
							$('#example_line1').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
									//var TotalMarks = 0;
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6]; 
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info2").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			});
			
			$( "#lineimg" ).click(function() {
				//var imgelem = evt.data.chart.jqplotToImageElem();
				var imgelem = $('#line1').jqplotToImageElem();
				var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
				//alert(imageSrc);// return false;
				//open(imageSrc); // this will open the image in another tab
				var imgdata = imageSrc;
				var base_url = site_base_url;		
				var url = base_url+"dashboard/savePdf/";
				var form = $('<form action="' + url + '" method="post">' +
				  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
				  '</form>');
				$('body').append(form);
				$(form).submit();
			});
		} else { 
			$('#line1').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		} 
	});

	$(document).ready(function() {
		//var inpu = [['Cup', 5000], ['Gen', 98876], ['HDTV', 15000], ['dul', 12545], ['Mod', 3987], ['Tck', 66545], ['Hai', 1809]];
		if (dashboard_cls_oppr!='') { 
			var plot6 = $.jqplot('line2', [dashboard_cls_oppr], {
			seriesDefaults: {
				rendererOptions: {
				smooth: true
				}
			},
			grid: {
				drawGridLines: false,        // wether to draw lines across the grid or not.
				gridLineColor: '#C7C7C7',   // CSS color spec of the grid lines.
				background: '#ffffff',      // CSS color spec for background color of grid.
				//borderColor: '#999999',     // CSS color spec for border around grid.
				borderWidth: 1.0,		// pixel width of border around grid.
				//backgroundColor: 'transparent', 
				drawBorder: true,
				shadow: false
			},

			axesDefaults: {
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
				rendererOptions: {
					baselineWidth: 0.6,
					baselineColor: '#444444',
					drawBaseline: true
				},
				tickOptions:{
					fontWeight:"bold"
				}
			},
			axes: {
				xaxis: {
				  renderer: $.jqplot.CategoryAxisRenderer,
				  label: 'Current Financial Year-->',
				  labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
				  tickRenderer: $.jqplot.CanvasAxisTickRenderer,
				  tickOptions: {
					  //angle: -10,
					  fontFamily: 'Courier New',
					  fontSize: '10pt',
					  showGridline: true
				  }
				},
				yaxis: {
					label: 'Values(USD)-->',
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
					tickOptions: {
					  //angle: -10,
					  fontFamily: 'Courier New',
					  fontSize: '10pt',
					  showGridline: true
				  }
				}
			},

			highlighter: { 
				show: false,
				//tooltipAxes: 'y',
				//tooltipLocation: 'nw',
				useAxesFormatters:false
			}
			});

			$('#line2').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
				/* if (filter_toggle_stat == 'toggle') {
					var formdata = advanceFilterParameterForDataClick();
				} else {
					var formdata = {};
				} */
				var def_search_id = $("#val_export").val();
	
				if(!isNaN(def_search_id)) {
					var formdata = {};
					formdata['search_id'] = def_search_id;
				} else if( def_search_id == 'search' ) {
					var formdata = advanceFilterParameterForDataClick();
				} else if( def_search_id == 'no_search' ) {
					var formdata = {};
				} else {
					var formdata = {};
				}
				formdata['gid'] 		  = pointIndex;
				formdata['type'] 	 	  = 'line2';
				formdata[csrf_token_name] = csrf_hash_token;

				$.ajax({
					type: "POST",
					url: site_base_url+'dashboard/showLeadDetails_cls/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info2').empty();
						$('#charts_info2').show();
						$('#charts_info2').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
					},
					success: function(html){
						//alert(html.html);
						$('#charts_info2').empty();
						$("#charts_info2").show();
						if (html.html == 'NULL') {
							$('#charts_info2').html('');
						} else {
							$('#charts_info2').show();
							$('#charts_info2').html(html.html);
							
							$('#example_line2').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
									//var TotalMarks = 0;
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6]; 
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info2").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			});
			
			$( "#line2img" ).click(function() {
			var imgelem = $('#line2').jqplotToImageElem();
			var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
			var imgdata = imageSrc;
			var base_url = site_base_url;		
			var url = base_url+"dashboard/savePdf/";
			var form = $('<form action="' + url + '" method="post">' +
			  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
			  '</form>');
			$('body').append(form);
			$(form).submit();
			});
			
		} else { 
			$('#line2').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		} 
	});

	$(document).ready(function() {
		if (dashboard_s7!='') {
			$.jqplot.config.enablePlugins = true;
			var plot7 = $.jqplot('pie2', [dashboard_s7], {
				gridPadding: {top:25, bottom:24, left:0, right:0},
				//title:'<?php echo $chart_title; ?>',
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
						tooltipLocation:'ne', 
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
					fontSize: '9pt',
					location: 'e',
					border: false
				},
				seriesColors: ["#eaa228", "#ff5800", "#c5b47f", "#8bbc21", "#579575", "#1aadce", "#839557", "#910000", "#027997", "#953579", "#422460", "#4b5de4", "#48596a", "#4bb2c5", "#0d233a", "#f0eded", "#492970", "#cc99cc", "#bfdde5", "#66ffcc", "#c747a3", "#ff99ff", "#ffff00", "#cc0000", "#a35b2e"]
			});
			
			$('#pie2').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
				/* if (filter_toggle_stat == 'toggle') {
					var formdata = advanceFilterParameterForDataClick();
				} else {
					var formdata = {};
				} */
				var def_search_id = $("#val_export").val();
	
				if(!isNaN(def_search_id)) {
					var formdata = {};
					formdata['search_id'] = def_search_id;
				} else if( def_search_id == 'search' ) {
					var formdata = advanceFilterParameterForDataClick();
				} else if( def_search_id == 'no_search' ) {
					var formdata = {};
				} else {
					var formdata = {};
				}
				formdata['data'] 	      = data;
				formdata['type'] 	      = 'pie2';
				formdata[csrf_token_name] = csrf_hash_token;

				$.ajax({
					type: "POST",
					url: site_base_url+'dashboard/showLeadsDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info3').empty();
						$('#charts_info3').show();
						$('#charts_info3').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
						//$('#res').hide();
					},
					success: function(html){
						//alert(html.html);
						//$("#loadingImage").hide();
						$('#charts_info3').empty();
						$("#charts_info3").show();
						if (html.html == 'NULL') {
							$('#charts_info3').html('');
						} else {
							$('#charts_info3').show();
							$('#charts_info3').html(html.html);
							
							$('#example_pie2').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
									//alert(nRow);
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6];
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info3").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			});
			
		} else { 
			$('#pie2').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		} 
	});

	$(document).ready(function(){
		if (dashboard_s8!='') { 
		$.jqplot.config.enablePlugins = true;
		var plot8 = $.jqplot('pie3', [dashboard_s8], {
			gridPadding: {top:25, bottom:24, left:0, right:0},
			//title:'<?php echo $chart_title; ?>',
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
					tooltipLocation:'sw', 
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
				fontSize: '9pt',
				location: 'e',
				border: false
			},
			seriesColors: ["#eaa228", "#ff5800", "#c5b47f", "#8bbc21", "#579575", "#1aadce", "#839557", "#910000", "#027997", "#953579", "#422460", "#4b5de4", "#48596a", "#4bb2c5", "#0d233a", "#f0eded", "#492970", "#cc99cc", "#bfdde5", "#66ffcc", "#c747a3", "#ff99ff", "#ffff00", "#cc0000", "#a35b2e"]
		});
		
		$('#pie3').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
			/* if (filter_toggle_stat == 'toggle') {
				var formdata = advanceFilterParameterForDataClick();
			} else {
				var formdata = {};
			} */
			var def_search_id = $("#val_export").val();
	
			if(!isNaN(def_search_id)) {
				var formdata = {};
				formdata['search_id'] = def_search_id;
			} else if( def_search_id == 'search' ) {
				var formdata = advanceFilterParameterForDataClick();
			} else if( def_search_id == 'no_search' ) {
				var formdata = {};
			} else {
				var formdata = {};
			}
			formdata['data'] 	      = data;
			formdata['type'] 	      = 'pie3';
			formdata[csrf_token_name] = csrf_hash_token;

			$.ajax({
				type: "POST",
				url: site_base_url+'dashboard/showLeadsDetails/',
				dataType:"json",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					$('#charts_info3').empty();
					$('#charts_info3').show();
					$('#charts_info3').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
				},
				success: function(html){
					//alert(html.html);
					$('#charts_info3').empty();
					$("#charts_info3").show();
					if (html.html == 'NULL') {
						$('#charts_info3').html('');
					} else {
						$('#charts_info3').show();
						$('#charts_info3').html(html.html);
						
						$('#example_pie3').dataTable( {
							"aaSorting": [[ 0, "desc" ]],
							"iDisplayLength": 5,
							"sPaginationType": "full_numbers",
							"bInfo": true,
							"bPaginate": true,
							"bProcessing": true,
							"bServerSide": false,
							"bLengthChange": false,
							"bSort": true,
							"bAutoWidth": false,
							"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
								//alert(nRow);
								var cost = 0
								for ( var i=0 ; i<aaData.length ; i++ )
								{
									var TotalMarks = aaData[i][6];
									cost += parseFloat(TotalMarks);
									
								}
								var nCells = nRow.getElementsByTagName('td');
								nCells[1].innerHTML = cost.toFixed(2);
							}
						});
						$('html, body').animate({ scrollTop: $("#charts_info3").offset().top }, 1000);
					}
				}                                                                                       
			});
			return false;
		});
		} else { 
			$('#pie3').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		} 
	});
	
	$(document).ready(function() {
		if (dashboard_s9!='') {
			$.jqplot.config.enablePlugins = true;
			var plot7 = $.jqplot('pie4', [dashboard_s9], {
				gridPadding: {top:25, bottom:24, left:0, right:0},
				//title:'<?php echo $chart_title; ?>',
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
						tooltipLocation:'ne', 
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
					fontSize: '9pt',
					location: 'e',
					border: false
				},
				seriesColors: ["#eaa228", "#ff5800", "#c5b47f", "#8bbc21", "#579575", "#1aadce", "#839557", "#910000", "#027997", "#953579", "#422460", "#4b5de4", "#48596a", "#4bb2c5", "#0d233a", "#f0eded", "#492970", "#cc99cc", "#bfdde5", "#66ffcc", "#c747a3", "#ff99ff", "#ffff00", "#cc0000", "#a35b2e"]
			});
			
			$('#pie4').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
				/* if (filter_toggle_stat == 'toggle') {
					var formdata = advanceFilterParameterForDataClick();
				} else {
					var formdata = {};
				} */
				var def_search_id = $("#val_export").val();
	
				if(!isNaN(def_search_id)) {
					var formdata = {};
					formdata['search_id'] = def_search_id;
				} else if( def_search_id == 'search' ) {
					var formdata = advanceFilterParameterForDataClick();
				} else if( def_search_id == 'no_search' ) {
					var formdata = {};
				} else {
					var formdata = {};
				}
				formdata['data'] 	      = data;
				formdata['type'] 	      = 'pie4';
				formdata[csrf_token_name] = csrf_hash_token;

				$.ajax({
					type: "POST",
					url: site_base_url+'dashboard/showLeadsDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info4').empty();
						$('#charts_info4').show();
						$('#charts_info4').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
						//$('#res').hide();
					},
					success: function(html){
						//alert(html.html);
						//$("#loadingImage").hide();
						$('#charts_info4').empty();
						$("#charts_info4").show();
						if (html.html == 'NULL') {
							$('#charts_info4').html('');
						} else {
							$('#charts_info4').show();
							$('#charts_info4').html(html.html);
							
							$('#example_pie4').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
									//alert(nRow);
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6];
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info4").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			});
			
		} else { 
			$('#pie4').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		} 
	});

	
	function getLeadDashboardTable(userid, user_name) {
		/* if (filter_toggle_stat == 'toggle') {
			var formdata = advanceFilterParameterForDataClick();
		} else {
			var formdata = {};
		} */
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var formdata = {};
			formdata['search_id'] = def_search_id;
		} else if( def_search_id == 'search' ) {
			var formdata = advanceFilterParameterForDataClick();
		} else if( def_search_id == 'no_search' ) {
			var formdata = {};
		} else {
			var formdata = {};
		}
		formdata['userid']   	  = userid;
		formdata['user_name']     = user_name;
		formdata[csrf_token_name] = csrf_hash_token;
		
		$.ajax({
			type: "POST",
			url : site_base_url + 'dashboard/getLeadDependency/',
			data: formdata,
			dataType:"json",
			success : function(response){
				if(response != '') {
					$("#lead-dependency-list").show();
					$("#lead-dependency-list").html(response);
					$('#lead-dependency-table').dataTable( {
						"aaSorting": [[ 0, "desc" ]],
						"iDisplayLength": 5,
						"sPaginationType": "full_numbers",
						"bInfo": true,
						"bPaginate": true,
						"bProcessing": true,
						"bServerSide": false,
						"bLengthChange": false,
						"bSort": true,
						"bAutoWidth": false,
						"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
							//var TotalMarks = 0;
							var cost = 0
							for ( var i=0 ; i<aaData.length ; i++ )
							{
								var TotalMarks = aaData[i][6];
								cost += parseFloat(TotalMarks);
							}
							//$('#lead-dependency-table').append('<p>'+cost+'</p>');
							var nCells = nRow.getElementsByTagName('td');
							nCells[1].innerHTML = cost.toFixed(2);
						}
					});
					$('html, body').animate({ scrollTop: $("#lead-dependency-list").offset().top }, 1000);
				} 			
			}
		});
	}


	function getLeadAssigneeTable(userid,user_name) {
		/* if (filter_toggle_stat == 'toggle') {
			var formdata = advanceFilterParameterForDataClick();
		} else {
			var formdata = {};
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var formdata = {};
			formdata['search_id'] = def_search_id;
		} else if( def_search_id == 'search' ) {
			var formdata = advanceFilterParameterForDataClick();
		} else if( def_search_id == 'no_search' ) {
			var formdata = {};
		} else {
			var formdata = {};
		}
		
		formdata['userid'] 	  	  = userid;
		formdata['user_name'] 	  = user_name;
		formdata[csrf_token_name] = csrf_hash_token;

		$.ajax({
			type: "POST",
			url : site_base_url + 'dashboard/getLeadAssigneeDependency/',
			data: formdata,
			dataType:"json",
			beforeSend:function(){
				$('#lead-dependency-list').empty();
				$("#lead-dependency-list").show();
				$('#lead-dependency-list').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success : function(response){
				if(response != '') {
					$('#lead-dependency-list').empty();
					$("#lead-dependency-list").show();
					$("#lead-dependency-list").html(response);
					$('#lead-assignee-table').dataTable( {
						"aaSorting": [[ 0, "desc" ]],
						"iDisplayLength": 5,
						"sPaginationType": "full_numbers",
						"bInfo": true,
						"bPaginate": true,
						"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
							//var TotalMarks = 0;
							var cost = 0
							for ( var i=0 ; i<aaData.length ; i++ )
							{
								var TotalMarks = aaData[i][6]; 
								cost += parseFloat(TotalMarks);
							}
							var nCells = nRow.getElementsByTagName('td');
							nCells[1].innerHTML = cost.toFixed(2);
							
						},
						"bProcessing": true,
						"bServerSide": false,
						"bLengthChange": false,
						"bSort": true,
						"bAutoWidth": false
					});
					$('html, body').animate({ scrollTop: $("#lead-dependency-list").offset().top }, 1000);
				} 			
			}
		});
	}
	
	function getCurrentLeadActivity(lead_id,lead_name)  {
		var baseurl = site_base_url;
		$.ajax({
		url : baseurl + 'dashboard/getLeadsCurrentActivity/'+ lead_id+'/'+lead_name,
			success : function(response){
				if(response != '') {
					$("#leads-current-activity-list").show();
					$("#leads-current-activity-list").html(response);
					$('#leads-current-activity-table').dataTable( {
						"bInfo": false,
						"bPaginate": false,
						"bSort": false,
						"bFilter": false,
						"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
							//var TotalMarks = 0;
							var cost = 0
							for ( var i=0 ; i<aaData.length ; i++ )
							{
								var TotalMarks = aaData[i][6]; 
								//var str = TotalMarks.split(" ");
								//cost += parseFloat(str[1]);
								cost += parseFloat(TotalMarks);
								
							}
							var nCells = nRow.getElementsByTagName('td');
							//nCells[1].innerHTML = "USD " + cost.toFixed(2);
							nCells[1].innerHTML = cost.toFixed(2);
						}
					});
					$('html, body').animate({ scrollTop: $("#leads-current-activity-list").offset().top }, 1000);
				} 			
			}
		});
	}

	$(document).ready(function() {
		$('.table_grid').dataTable({
			"iDisplayLength": 5,
			"sPaginationType": "full_numbers",
			"bInfo": true,
			"bPaginate": true,
			"bProcessing": true,
			"bServerSide": false,
			"bLengthChange": false,
			"bSort": true,
			"bAutoWidth": false
		});	
	});
	
	$('#leads-current-activity-list').delegate('.grid-close','click',function() {
		var $lead = $("#leads-current-activity-list");
		$lead.slideUp('fast', function () { $lead.css('display','none'); });
	});
	$('#lead-dependency-list').delegate('.grid-close','click',function(){
		var $lead = $("#lead-dependency-list");
		$lead.slideUp('fast', function () { $lead.css('display','none'); });
	});
	$('#charts_info').delegate('.grid-close','click',function(){
		var $lead = $("#charts_info");
		$lead.slideUp('fast', function () { $lead.css('display','none'); });
	});
	$('#charts_info2').delegate('.grid-close','click',function(){
		var $lead = $("#charts_info2");
		$lead.slideUp('fast', function () { $lead.css('display','none'); });
	});
	$('#charts_info3').delegate('.grid-close','click',function(){
		var $lead = $("#charts_info3");
		$lead.slideUp('fast', function () { $lead.css('display','none'); });
	});
	$('#charts_info4').delegate('.grid-close','click',function(){
		var $lead = $("#charts_info4");
		$lead.slideUp('fast', function () { $lead.css('display','none'); });
	});

	//currently active leads
	$('#current-lead-report').change(function() {
		/* if (filter_toggle_stat == 'toggle') {
			var formdata = advanceFilterParameterForDataClick();
		} else {
			var formdata = {};
		} */
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var formdata = {};
			formdata['search_id'] = def_search_id;
		} else if( def_search_id == 'search' ) {
			var formdata = advanceFilterParameterForDataClick();
		} else if( def_search_id == 'no_search' ) {
			var formdata = {};
		} else {
			var formdata = {};
		}
		formdata['statusVar'] 	  = $(this).val();
		formdata[csrf_token_name] = csrf_hash_token;
		var baseurl				  = site_base_url;
		$.ajax({
			type: 'POST',
			url : baseurl + 'dashboard/get_leads_current_weekly_monthly_report/',
			data: formdata,
			beforeSend:function(){
				$('#weekly-monthly').empty();
				$("#weekly-monthly").show();
				$('#weekly-monthly').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success : function(response){
				if(response != '') {
					$('#weekly-monthly').empty();
					$("#weekly-monthly").show();
					$("#weekly-monthly").html(response);
					$('#weekly-monthly-table').dataTable({
						"iDisplayLength": 5,
						"sPaginationType": "full_numbers",
						"bInfo": true,
						"bPaginate": true,
						"bProcessing": true,
						"bServerSide": false,
						"bLengthChange": false,
						"bSort": true,
						"bAutoWidth": false
					});
				} 			
			}
		});
	});
	
	
	/* dashboard excel report starts here */
	
	/*Current Pipeline Report*/
	$('#charts_info').delegate('#current-pipeline-export','click',function() {
		var lead_stage_name = $("#current-pipeline-export").attr('name'); //alert(lead_stage_name);
		var type 			= $("#lead-type-name").val(); 
		var baseurl			= site_base_url;
		var url				= baseurl+"dashboard/excel_export_lead_owner";
		
		/* if (filter_toggle_stat == 'toggle') {
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}
		
		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />' +
		'<input type="hidden" name="lead_stage_name" value="' +lead_stage_name+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
	
	/*Lead by Region Report*/
	$('#charts_info').delegate('#leads-by-region-export','click',function() {
		var lead_region_name = $("#leads-by-region-export").attr('name'); 
		var type 		 	 = $("#lead-by-region").val();
		var baseurl			 = site_base_url;
		var url				 = baseurl+"dashboard/excel_export_lead_owner";

		/* if (filter_toggle_stat == 'toggle') {	
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}
		
		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="lead_region_name" value="' +lead_region_name+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
	
	/*Lead Indicator Report*/
	$('#leads-current-activity-list').delegate('#least-active-report','click',function() {
		var lead_indi = $("#least-active-report").attr('name');
		var type 	  = $("#least-active-type").val();
		var baseurl	  = site_base_url;
		var url	  	  = baseurl+"dashboard/excel_export_lead_owner";
		
		/* if (filter_toggle_stat == 'toggle') {	
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}
		
		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="lead_indi" value="' +lead_indi+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
	
	/*Currently Active Report*/
	$('#leads-current-activity-list').delegate('#lead-current-activity-export','click',function() {
		var lead_no = $("#lead-no").val();
		var type 	= 'currentactivity';
		var baseurl = site_base_url;
		var url = baseurl+"dashboard/excel_export_lead_owner";

		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="lead_no" value="' +lead_no+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});

	/*Lead Aging Report*/
	$('#charts_info2').delegate('#lead-aging-report','click',function() {
		var lead_aging = $("#lead-aging-report").attr('name');
		var type 	   = $("#lead-aging-type").val();   
		var baseurl	   = site_base_url;
		var url		   = baseurl+"dashboard/excel_export_lead_owner";
		/* if (filter_toggle_stat == 'toggle') {	
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}

		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="lead_aging" value="' +lead_aging+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
	
	/*Closed Opportunities Report*/
	$('#charts_info2').delegate('#closed-oppor-report','click',function() {
		var month_id = $("#closed-oppor-report").attr('name');
		var type	 = $("#cls-oppr-type").val();   
		var baseurl  = site_base_url;
		var url		 = baseurl+"dashboard/excel_export_lead_owner";
		/* if (filter_toggle_stat == 'toggle') {	
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}
		
		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="month_id" value="' +month_id+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});

	/*Lead Source Report*/
	$('#charts_info3').delegate('#leads-by-leadsource-export','click',function() {
		var lead_source	= $("#leads-by-leadsource-export").attr('name'); 
		var type   		= $("#lead-by-leadsource").val();   
		var baseurl		= site_base_url;
		
		var url		 = baseurl+"dashboard/excel_export_lead_owner";
		/* if (filter_toggle_stat == 'toggle') {	
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}
		
		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="lead_source" value="' +lead_source+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
	
	/*Lead Industry Report*/
	$('#charts_info4').delegate('#leads-by-industry-export','click',function() {
		var lead_source	= $("#leads-by-industry-export").attr('name'); 
		var type   		= $("#lead-by-industry").val();   
		var baseurl		= site_base_url;
		
		var url		 = baseurl+"dashboard/excel_export_lead_owner";
		/* if (filter_toggle_stat == 'toggle') {	
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}
		
		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="lead_source" value="' +lead_source+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
	
	/*Service Requirement Report*/
	$('#charts_info3').delegate('#leads-by-service-req-export','click',function() {
		var servic_require = $("#leads-by-service-req-export").attr('name');
		var type 		   = $("#lead-by-service-req").val();   
		var baseurl		   = site_base_url;
		
		var url		 = baseurl+"dashboard/excel_export_lead_owner";
		/* if (filter_toggle_stat == 'toggle') {	
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}
		
		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="servic_require" value="' +servic_require+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
	
	/*Lead Owner Report*/
	$('#lead-dependency-list').delegate('#lead-ownner-export','click',function() {
		var user_id   = $('#lead-dependency-table').attr('name'); 
		var user_name = $('#lead-owner-username').val();
		var type	  = 'leadowner';
		var baseurl   = site_base_url;
		var url	      = baseurl+"dashboard/excel_export_lead_owner";
		
		/* if (filter_toggle_stat == 'toggle') {	
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}
		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="user_id" value="' +user_id+ '" />' +
		'<input type="hidden" name="user_name" value="' +user_name+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
	
	/*Lead Assignee Report*/
	$('#lead-dependency-list').delegate('#lead-assignee-export','click',function() {
		var user_id   = $('#lead-assignee-table').attr('name');
		var user_name = $('#lead-assignee-username').val(); 
		var type	  = 'assignee';
		var baseurl   = site_base_url;
		var url 	  = baseurl+"dashboard/excel_export_lead_owner";
		
		/* if (filter_toggle_stat == 'toggle') {	
			var advancedFilters = advanceFilterParameterForExcel();
		} */
		
		var def_search_id = $("#val_export").val();
	
		if(!isNaN(def_search_id)) {
			var advancedFilters = '<input type="hidden" name="search_id" value="' +def_search_id+ '" />';
		} else if( def_search_id == 'search' ) {
			var advancedFilters = advanceFilterParameterForExcel();
		} else if( def_search_id == 'no_search' ) {
			var advancedFilters;
		} else {
			var advancedFilters;
		}
		
		var form = $('<form action="' + url + '" method="post">' +
		'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		'<input type="hidden" name="user_id" value="' +user_id+ '" />' +
		'<input type="hidden" name="user_name" value="' +user_name+ '" />' +
		'<input type="hidden" name="type" value="' +type+ '" />' +
		'"'+advancedFilters+'"' +
		'</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});

	//global function for graph click parameters
	function advanceFilterParameterForDataClick() {
		var stge 	   = filter_stgs;
		var cust_id    = filter_custs_id;
		var ownr_id    = filter_owr_id;
		var assg_id    = filter_assg_id;
		var reg_id	   = filter_reg_nme;
		var cntry_id   = filter_country;
		var stet_id    = filter_state;
		var locn_id    = filter_location;
		var servic_req = filter_servic_req;
		var lead_sour  = filter_lead_sour;
		var industry   = filter_industry;
		var lead_indic = filter_lead_indic;
	
		var filterParameterDataClick = {'stge':stge, 'cust_id':cust_id, 'ownr_id':ownr_id, 'assg_id':assg_id, 'reg_id':reg_id, 'cntry_id':cntry_id, 'stet_id':stet_id, 'locn_id':locn_id, 'servic_req':servic_req, 'lead_sour':lead_sour, 'industry':industry, 'lead_indic':lead_indic};
		
		return filterParameterDataClick;
	}

	//global function for export to excel parameters
	function advanceFilterParameterForExcel() {
		var stge 	   = filter_stgs;
		var cust_id    = filter_custs_id;
		var ownr_id    = filter_owr_id;
		var assg_id    = filter_assg_id;
		var reg_id	   = filter_reg_nme;
		var cntry_id   = filter_country;
		var stet_id    = filter_state;
		var locn_id    = filter_location;
		var servic_req = filter_servic_req;
		var lead_sour  = filter_lead_sour;
		var industry   = filter_industry;
		var lead_indic = filter_lead_indic;
		
		var filterParameter = '<input type="hidden" name="stge" value="' +stge+ '" />' +
		'<input type="hidden" name="cust_id" value="' +cust_id+ '" />' +
		'<input type="hidden" name="ownr_id" value="' +ownr_id+ '" />' +
		'<input type="hidden" name="assg_id" value="' +assg_id+ '" />' +
		'<input type="hidden" name="reg_id" value="' +reg_id+ '" />' +
		'<input type="hidden" name="cntry_id" value="' +cntry_id+ '" />' +
		'<input type="hidden" name="stet_id" value="' +stet_id+ '" />' +
		'<input type="hidden" name="locn_id" value="' +locn_id+ '" />' +
		'<input type="hidden" name="servic_req" value="' +servic_req+ '" />' +
		'<input type="hidden" name="lead_sour" value="' +lead_sour+ '" />' +
		'<input type="hidden" name="industry" value="' +industry+ '" />' +
		'<input type="hidden" name="lead_indic" value="' +lead_indic+ '" />';
		
		return filterParameter;
	}

	/* dashboard excel report ends here */
	
	$(".saved-search-head").click(function(){
		var X=$(this).attr('id');

		if(X==1) {
			$(".saved-search-criteria").hide();
			$(this).attr('id', '0');
		} else {
			$(".saved-search-criteria").show();
			$(this).attr('id', '1');
		}
	});

	//Mouseup textarea false
	$(".saved-search-criteria").mouseup(function() {
		return false
	});
	$(".saved-search-head").mouseup(function() {
		return false
	});

	//Textarea without editing.
	$(document).mouseup(function() {
		$(".saved-search-criteria").hide();
		$(".saved-search-head").attr('id', '');
	});
}

if(viewPjt==1) {

	$('#ajax_loader').show();

	//For Projects
	var pjtstage = $("#pjt_stage").val();
	var cust = $("#customer1").val();
	var keyword = $("#keywordpjt").val();
	//alert(keyword);
	if(keyword == "Project Title, Name or Company")
		keyword = 'null';

	if (document.getElementById('advance_search_pjt'))
		document.getElementById('advance_search_pjt').style.display = 'none';	

	// var sturl = "project/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
	var sturl = site_base_url+"project/advance_filter_search_pjt/";
	//alert(sturl);	
	// $('#advance_search_results_pjts').load(sturl);
	$('#advance_search_results_pjts').load(sturl,function(){
		$('#ajax_loader').hide();
		$('#advance_search_results_pjts').show();
	});
		
	function advanced_filter_pjt() {
		$('#advance_search_pjt').slideToggle('slow');
		var  keyword = $("#keywordpjt").val();
		var status = document.getElementById('advance_search_pjt').style.display;
		
		if(status == 'none') {
			var pjtstage = $("#pjt_stage").val();
			var cust = $("#customer1").val();
		}
		else   {
				
			$("#pjt_stage").val("");
			$("#customer1").val("");

		}
	}

	/*
	$('#advanceFilters_pjt').submit(function() {
		$('#advance').hide();
		$('#load').show();
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val(); 
		var  keyword = $("#keywordpjt").val(); 
		if(keyword == "Project Title, Name or Company")
		keyword = 'null';
		document.getElementById('advance_search_results_pjts').style.display = 'block';	
		var sturl = "project/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
		//alert(sturl);
		// $('#advance_search_results_pjts').load(sturl);	
		$('#advance_search_results_pjts').load(sturl,function(){
			$('#advance').show();
			$('#load').hide();
		});
		return false;
	});
	
	$('#pjt_search_form').submit(function() {	
			var  keyword = $("#keywordpjt").val(); 
			if(keyword == "Project Title, Name or Company")
			keyword = 'null';
			var pjtstage = $("#pjt_stage").val(); 
			var pm_acc = $("#pm_acc").val(); 
			var cust = $("#customer1").val();  
			var sturl = "project/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
			$('#advance_search_results_pjts').load(sturl);
			return false;
	});
	*/
	
	$('#advanceFilters_pjt,#pjt_search_form').submit(function() {
		var pjtstage    = $("#pjt_stage").val();
		var cust 	    = $("#customer1").val(); 
		var service     = $("#services").val(); 
		var keyword     = $("#keywordpjt").val();
		var practice    = $('#practices').val();
		var divisions  	= $("#divisions").val();
		var datefilter  = $("#datefilter").val();
		var from_date   = $("#from_date").val();
		var to_date  	= $("#to_date").val();
		if(keyword == "Project Title, Name or Company")
		keyword = '';
		
		var params = {'pjtstage':pjtstage,'cust':cust,'service':service,'practice':practice,'divisions':divisions,'keyword':encodeURIComponent(keyword),'datefilter':datefilter,'from_date':from_date,'to_date':to_date};
		params[csrf_token_name] = csrf_hash_token; 
		if($(this).attr("id") == 'advanceFilters_pjt'){
			$('#advance').hide();
			$('#load').show();
			$('#ajax_loader').show();
			$("#advance_search_results_pjts" ).empty();
		}
		
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/advance_filter_search_pjt',
			data: params,
			success: function(data) {
				$("#advance_search_results_pjts" ).html(data);
				$('#advance').show();
				$('#load').hide();
				$('#ajax_loader').hide();
			}
		});
		return false;
	});
	
	$(function() {
		// $('#from_date, #to_date').datepicker({dateFormat: 'dd-mm-yy'});
		$('#from_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true, onSelect: function(date) {
			if($('#to_date').val!='')
			{
				$('#to_date').val('');
			}
			var return_date = $('#from_date').val();
			$('#to_date').datepicker("option", "minDate", return_date);
		}});
		$('#to_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true });
	});
}


//For Tasks
/*mychanges*/
$(function(){
	
	var params    		     = {};	
	params[csrf_token_name]  = csrf_hash_token; 

	$('.all-tasks').load('tasks/index/extend #task-page .task-contents',params, loadEditTables);
	$('#set-job-task .pick-date, #search-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M'});
	
	$('#task_search_user').val(dashboard_userid);
	/* job tasks character limit */
	$('#job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		var remain_len = 1000 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		$('#task-desc-countdown').text(remain_len);
	});
	$('#edit-job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		
		var remain_len = 1000 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		
		$('#edit-task-desc-countdown').text(remain_len);
	});
});
function searchTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	$.post('tasks/search',$('#search-job-task').serialize()+'&'+csrf_token_name+'='+csrf_hash_token,function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function searchtodayTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	$.post('tasks/search','task_search_user='+$('#task_search_user').val()+'&task_search_start_date='+$('#hided').val()+'&task_search_end_date='+$('#hided').val()+'&'+csrf_token_name+'='+csrf_hash_token,function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function loadEditTables(){
	/* $('#jv-tab-4').block({
		message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
		css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
	}); */
	
	$.blockUI({
		message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
		css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
	});
	
	var taskids = [];
	$('td.task.random-task').each(function(){
		taskids.push($(this).attr('rel'));
		
			$(this).append('<div class="buttons" style="display:none"> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit |</button> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'complete\'); return false;">Approve |</button> \
								<button type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'delete\'); return false;">Delete</button> \
							</div>');	
		
	});
	$('td.task.newrandom-task').each(function(){
		taskids.push($(this).attr('rel'));
			$(this).append('<div class="buttons" style="display:none"> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit</button> \
							</div>');
	});
	if (taskids.length < 1)	{
		// $('#jv-tab-4').unblock();
		$.unblockUI();
		return;
	}
	
	var params 				= {'id_set': taskids.join(',')};
	params[csrf_token_name] = csrf_hash_token; 
	
	$.post('ajax/request/get_random_tasks',params,function(data){

		if (data != '')	{
			$('form.random-task-tables').html(data);
		} 
		// $('#jv-tab-4').unblock();
		$.unblockUI();
	});
}
if(viewlead==1 && filter_toggle_stat!='toggle') {
	// document.getElementById('advance_search').style.display = 'none';
	$('#advance_search').hide();
}

if(viewlead==1 && filter_toggle_stat=='toggle') {
	// document.getElementById('advance_search').style.display;	
	$('#advance_search').show();
	$('#val_export').val('search');
	$(function() {
		var regionname = $("#regionname").val();
		if (regionname != null) {
			$('#statename').html('');
			$('#locname').html('');
			 loadCountry(filter_country);
			/*if (loadCountry() == 1) {
				if (filter_country!="") {
				set_country(filter_country);
				}
			}*/
			// loadState(filter_state);
		}
		
	});

	function set_country(filter_country) {
		var cnty = new Array();
		if (filter_country)
		cnty = filter_country.split(",");
		// alert(cnty[0]);
		for (var i=0;i<cnty.length;i++)
		{
			$('#countryname option[value='+cnty[i]+']').attr('selected','selected');
		}
		loadState(filter_state);
	}

	function set_state(filter_state) {
		var stat = new Array();
		if (filter_state)
		stat = filter_state.split(",");
		for (var i=0;i<stat.length;i++)
		{
			$('#statename option[value='+stat[i]+']').attr('selected','selected');
		}
		loadLocations(filter_location);
	}
	
	function set_location(filter_location) {
		var loc = new Array();
		if (filter_location)
		loc = filter_location.split(",");
		for (var i=0;i<loc.length;i++)
		{
			$('#locname option[value='+loc[i]+']').attr('selected','selected');
		}
	}
	
	$('#filter_reset').click(function() {
		$('.advfilter option').removeAttr('selected');
	});
}


function advanced_filter() {
	$('#advance_search').slideToggle('slow');
	var status = document.getElementById('advance_search').style.display;
	
	if(status == 'none') {
		var owner 		 = $("#owner").val();
		var leadassignee = $("#leadassignee").val();
		var regionname 	 = $("#regionname").val();
		var countryname  = $("#countryname").val();
		var statename    = $("#statename").val();
		var locname		 = $("#locname").val();
		var stage		 = $("#stage").val(); 
		var customer	 = $("#customer").val(); 
	} else {
		$("#owner").val("");
		$("#leadassignee").val("");
		$("#regionname").val("");
		$("#countryname").val("");
		$("#statename").val("");
		$("#locname").val("");
		$("#stage").val("");
		$("#customer").val("");
	}
}
//For Countries
$('#regionname').change(function() {
	$('#statename').html('');
	$('#locname').html('');
	loadCountry();
});

function loadCountry(cids) {
	var region_id 			= $("#regionname").val();
	var params 				= {'region_id':region_id};
	params[csrf_token_name] = csrf_hash_token;
	$.post( 
		'welcome/loadCountrys/',
		params,
		function(data) {										
			if (data.error) {
				alert(data.errormsg);
			} else {
				$("select#countryname").html(data);
			}
			if (filter_toggle_stat=='toggle') {
				if(cids!= '' || cids!=undefined){
					set_country(cids)
				}
			}
		}
	);
}


//For States
$('#countryname').change(function() {
	$('#locname').html('');
	loadState();
});

function loadState(sids) {
	var coun_id 			= $("#countryname").val();
	var params 				= {'coun_id':coun_id};
	params[csrf_token_name] = csrf_hash_token;
	if(coun_id != '') {
		$.post( 
			'welcome/loadStates/',
			params,
			function(data) {										
				if (data.error) {
					alert(data.errormsg);
				} else {
					$("select#statename").html(data);
				}
				if (filter_toggle_stat=='toggle') {
					if(sids!= '' || sids!=undefined){
						set_state(sids)
					}
				}
			}
		);
	}
}

//For Locations
$('#statename').change(function() {
	loadLocations();
});



function loadLocations(lids) {
	var st_id  				= $("#statename").val();
	var params 				= {'st_id':st_id};
	params[csrf_token_name] = csrf_hash_token;
	if(st_id != '') {
		$.post( 
			'welcome/loadLocns/',
			params,
			function(data) {										
				if (data.error) {
					alert(data.errormsg);
				} else {
					$("select#locname").html(data);
				}
				if (filter_toggle_stat=='toggle') {
					if(lids!= '' || lids!=undefined){
						set_location(lids)
					}
				}
			}
		);
	}
}

$("#save_advance").click(function() {
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"welcome/get_search_name_form",
		cache: false,
		data: csrf_token_name+'='+csrf_hash_token,
		success: function(res){
			// alert(res.html)
			// return false;
			$('#popupGetSearchName').html(res);
			$.blockUI({
				message:$('#popupGetSearchName'),
				css:{border: '2px solid #999', color:'#333',padding:'6px',top:'280px',left:($(window).width() - 265) /2+'px',width: '246px', position: 'absolute'}
				// focusInput: false 
			});
			$( "#popupGetSearchName" ).parent().addClass( "no-scroll" );
		}
	});
});

function save_cancel() {
	$.unblockUI();
}

function save_search() {

	if($('#search_name').val()=='') {
		$("#search_name").css("border-color", "red");
		return false;
	}
	
	$("#search_name").keyup(function(){
		$("#search_name").css("border-color", "");
	});
	
	$('#search_advance').hide();
	$('#save_advance').hide();
	$('#load').show();
	
	var is_defalut_val = 0;
	
	if($( "#is_default:checked" ).val() == 1) {
		is_defalut_val = 1;
	}
	
	var search_name = $('#search_name').val();
	var is_default  = is_defalut_val;
	
	var stage		 = $("#stage").val();
	var customer	 = $("#customer").val();
	var owner 		 = $("#owner").val();
	var leadassignee = $("#leadassignee").val();
	var ser_requ   	 = $("#ser_requ").val();
	var lead_src     = $("#lead_src").val();
	var industry     = $("#industry").val();
	var regionname 	 = $("#regionname").val();
	var countryname  = $("#countryname").val();
	var statename    = $("#statename").val();
	var locname		 = $("#locname").val();
	var lead_indi    = $("#lead_indi").val();
	//Save the search criteria
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"dashboard/save_search/4",
		cache: false,
		data: "search_name="+search_name+"&is_default="+is_default+"&stage="+stage+"&customer="+customer+"&owner="+owner+"&leadassignee="+leadassignee+"&ser_requ="+ser_requ+"&lead_src="+lead_src+"&industry="+industry+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&lead_indi="+lead_indi+'&'+csrf_token_name+'='+csrf_hash_token,
		// data: "search_name="+search_name+"&"+$("#advancefilterhome").serialize()+'&'+csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			$('#popupGetSearchName').html('<div style="margin:10px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(response){
			if(response.res == true) {
				$('#no_record').remove();
				$('.search-root').append(response.search_div);
			}
			$( "#advance" ).trigger("click");
		}
	});
	return false;  //stop the actual form post !important!
}

function delete_save_search(search_id) {
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"welcome/delete_save_search/"+search_id+'/4',
		cache: false,
		data: csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			$('.search-root').block({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
		},
		success: function(response){
			if(response.resu=='deleted') {
				$('#item_'+search_id).remove();
				if($(".search-root li").length == 1) {
					$('.search-root').append('<li id="no_record" style="text-align: center; margin: 5px;">No Save & Search Found</li>');
				}
			} else {
				alert('Not updated');
			}
			$('.search-root').unblock();
		}
	});
}

$('.search-root').on('click', '.set_default_search', function() {
	
	var search_id = $( this ).val();
	//return;
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"welcome/set_default_search/"+search_id+'/4',
		cache: false,
		data: "filter=filter&"+csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			$('.search-root').block({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
		},
		success: function(response){
			$('.search-root').unblock();
			if(response.resu=='updated') {
				// show_search_results(search_id);
				// $('#val_export').val(search_id);
				 location.reload();
			} else {
				alert('Not updated');
			}
		}
	});
});

function show_search_results(search_id) {
	var url = site_base_url+"dashboard";
	var form = $('<form action="' + url + '" method="post">' +
	  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
	  '<input id="customer" type="hidden" name="search_type" value="'+search_id+'" /></form>');
	$('body').append(form);
	$(form).submit();
}

function loadajaxwithurl(url)
{
		var params    		     = {};	
	params[csrf_token_name]  = csrf_hash_token;
	
	//$('.all-tasks').load('tasks/index/extend #task-page .task-contents', params, check());
	
 	    $(".all-tasks").load(url,params, function(responseTxt, statusTxt, xhr){
        if(statusTxt == "success")
          // alert("External content loaded successfully!");
        if(statusTxt == "error")
            alert("Error: " + xhr.status + ": " + xhr.statusText);
    }); 
}

function resetpage()
{
	loadajaxwithurl('tasks/index/extend');
}
/////////////////