/*
*@projects_view_inprogress
*@
*/
/*For Timesheet Metrics Data - Start*/
$(function() {

	$("#metrics_data").click(function(){

		if($("#metrics_month").val() == '')
		return false;
		
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
	}); 
	
	$('.excel').click(function() {
		var stage = $('#pjt_stage').val();
		// var pm    = $('#pm_acc').val();
		var customer = $('#customer1').val();
		var service = $('#services').val();
		var practice = $('#practices').val();
		var datefilter  = $("#datefilter").val();
		var from_date   = $("#from_date").val();
		var to_date  	= $("#to_date").val();
		var export_type = $(this).attr("id");
		
		var monthly = '';
		if(export_type == 'monthly') {
			var monthly = '<input type="hidden" name="metrics_month" value="' +$("#metrics_month").val()+ '" />'+
						'<input type="hidden" name="metrics_year" value="' +$("#metrics_year").val()+ '" />';
		}
		
		var url = site_base_url+"project/excelExport";
		
		var form = $('<form action="' + url + '" method="post">' +
		  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		  '<input type="hidden" name="stages" value="' +stage+ '" />' +
		  '<input type="hidden" name="customers" value="' +customer+ '" />' +
		  '<input type="hidden" name="services" value="' +service+ '" />' +
		  '<input type="hidden" name="practices" value="' +practice+ '" />' +
		  '<input type="hidden" name="datefilter" value="' +datefilter+ '" />' +
		  '<input type="hidden" name="from_date" value="' +from_date+ '" />' +
		  '<input type="hidden" name="to_date" value="' +to_date+ '" />' +
		  '<input type="hidden" name="export_type" value="' +export_type+ '" />' +
		  monthly+
		  '</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
});
/*For Timesheet Metrics Data - End*/