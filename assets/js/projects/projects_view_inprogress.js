/*
*@projects_view_inprogress
*@
*/
/*For Timesheet Metrics Data - Start*/
$(function() {

	/* 	$("#metrics_data").click(function(){

		if($("#metrics_month").val() == '')
		return false;
		
		// var form_data = $("#filter_metrics").serialize()+'&'+$('#advanceFilters_pjt').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;
		var form_data = $("#filter_metrics").serialize()+'&'+$('#advanceFilters_pjt').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;
		
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/advanceFilterMetrics',
			data: form_data,
			beforeSend: function() {
				$('#metrics_data').hide();
				$('#loading').show();
			},
			success: function(data) {
				$("#monthly_based").html(data);
				$('#metrics_data').show();
				$('#loading').hide();
			}
		});
		return false;
	}); */
	
	
	$("#metrics_data").click(function(){
	
		if($("#metrics_month").val() == '')
		return false;
	
		var export_proj_type = $(".val_export").val();
		
		if(!isNaN(export_proj_type)) {
			export_proj_type = 'number';
		}
		
		var stage = $('#pjt_stage').val();
		// var pm    = $('#pm_acc').val();
		var customers = $('#customer1').val();
		var service 	= $('#services').val();
		var practice = $('#practices').val();
		var datefilter  = $("#datefilter").val();
		var from_date   = $("#from_date").val();
		var to_date  	= $("#to_date").val();
		var divisions  	= $("#divisions").val();
		var keyword  	= $("#keywordpjt").val();
		if(keyword == "Project Title, Name or Company")
		keyword = '';
		
		switch(export_proj_type) {
			case 'search':
			case 'no_search':
			
				var form_data = $("#filter_metrics").serialize()+'&stages='+stage+'&customers='+customers+'&service='+service+'&practice='+practice+'&datefilter='+datefilter+'&from_date='+from_date+'&to_date='+to_date+'&divisions='+divisions+'&keyword='+encodeURIComponent(keyword)+'&'+csrf_token_name+'='+csrf_hash_token;
				
				$.ajax({
					type: 'POST',
					url: site_base_url+'project/advanceFilterMetrics',
					data: form_data,
					beforeSend: function() {
						$('#metrics_data').hide();
						$('#loading').show();
					},
					success: function(data) {
						$("#monthly_based").html(data);
						$('#metrics_data').show();
						$('#loading').hide();
					}
				});
				return false;
			break;
			case 'number':
				var form_data_1 = $("#filter_metrics").serialize()+'&keyword='+encodeURIComponent(keyword)+'&'+csrf_token_name+'='+csrf_hash_token;
				$.ajax({
					type: 'POST',
					url: site_base_url+'project/advanceFilterMetrics/'+$(".val_export").val(),
					data: form_data_1,
					beforeSend: function() {
						$('#metrics_data').hide();
						$('#loading').show();
					},
					success: function(data) {
						$("#monthly_based").html(data);
						$('#metrics_data').show();
						$('#loading').hide();
					}
				});
				return false;
				
			break;
		}
	});
	
	$('.excel').click(function() {
	
		var stage 		= $('#pjt_stage').val();
		var customer	= $('#customer1').val();
		var service     = $('#services').val();
		var practice    = $('#practices').val();
		var datefilter  = $("#datefilter").val();
		var from_date   = $("#from_date").val();
		var to_date  	= $("#to_date").val();
		var divisions  	= $("#divisions").val();
		var keyword  	= $("#keywordpjt").val();
		
		if(keyword == "Project Title, Name or Company")
		keyword = '';
		
		var export_type = $(this).attr("id");

		var export_proj_type = $(".val_export").val();
		
		if(!isNaN(export_proj_type)) {
			export_proj_type = 'number';
		}
		
		var monthly = '';
		if(export_type == 'monthly') {
			var monthly = '<input type="hidden" name="metrics_month" value="' +$("#metrics_month").val()+ '" />'+
						'<input type="hidden" name="metrics_year" value="' +$("#metrics_year").val()+ '" />';
		}

		switch(export_proj_type) {
			case 'search':
			case 'no_search':
				var url = site_base_url+"project/excelExport";
				
				var form = $('<form action="' + url + '" method="post">' +
							  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
							  '<input type="hidden" name="stages" value="' +stage+ '" />' +
							  '<input type="hidden" name="customers" value="' +customer+ '" />' +
							  '<input type="hidden" name="services" value="' +service+ '" />' +
							  '<input type="hidden" name="practices" value="' +practice+ '" />' +
							  '<input type="hidden" name="divisions" value="' +divisions+ '" />' +
							  '<input type="hidden" name="datefilter" value="' +datefilter+ '" />' +
							  '<input type="hidden" name="from_date" value="' +from_date+ '" />' +
							  '<input type="hidden" name="to_date" value="' +to_date+ '" />' +
							  '<input type="hidden" name="keyword" value="' +encodeURIComponent(keyword)+ '" />' +
							  '<input type="hidden" name="export_type" value="' +export_type+ '" />' +
							  monthly+
							  '</form>');
				$('body').append(form);
				$(form).submit(); 
				return false;
			break;
			case 'number':
				var url = site_base_url+"project/excelExport/"+$(".val_export").val();
				var form = $('<form action="' + url + '" method="post">' +
				  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
				  '<input type="hidden" name="export_type" value="' +export_type+ '" />' +
				  '<input type="hidden" name="keyword" value="' +encodeURIComponent(keyword)+ '" />' +
				  monthly+
				  '</form>');
				$('body').append(form);
				$(form).submit();
				return false;
				
			break;
		}
	});
});
/*For Timesheet Metrics Data - End*/