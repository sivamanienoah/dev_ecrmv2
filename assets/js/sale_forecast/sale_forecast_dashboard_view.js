/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

// $('#advance_search').hide();

function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}

$(function() {
	
	/*Date Picker*/
	$( "#month_year_from_date, #month_year_to_date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'MM yy',
		showButtonPanel: true,	
		onClose: function(dateText, inst) {
			var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();         
			$(this).datepicker('setDate', new Date(year, month, 1));
		},
		beforeShow : function(input, inst) {
			if ((datestr = $(this).val()).length > 0) {
				year = datestr.substring(datestr.length-4, datestr.length);
				month = jQuery.inArray(datestr.substring(0, datestr.length-5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
				$(this).datepicker('setDate', new Date(year, month, 1));    
			}
				var other  = this.id  == "month_year_from_date" ? "#month_year_to_date" : "#month_year_from_date";
				var option = this.id == "month_year_from_date" ? "maxDate" : "minDate";        
			if ((selectedDate = $(other).val()).length > 0) {
				year = selectedDate.substring(selectedDate.length-4, selectedDate.length);
				month = jQuery.inArray(selectedDate.substring(0, selectedDate.length-5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker( "option", option, new Date(year, month, 1));
			}
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
});

//For Advance Filters functionality.
$("#advanceFiltersDashboard").submit(function() {
	$('#advance').hide();
	$('#load').show();
	var entity      = $("#entity").val();
	var customer    = $("#customer").val();
	var lead_ids    = $("#lead_ids").val();
	var month_year_from_date = $("#month_year_from_date").val();
	var month_year_to_date   = $("#month_year_to_date").val();

	$.ajax({
		type: "POST",
		url: site_base_url+"sales_forecast/forecast_dashboard/",
		async: false,
		// dataType: "json",
		data: "filter=filter"+"&lead_ids="+lead_ids+"&customer="+customer+"&entity="+entity+'&month_year_from_date='+month_year_from_date+"&month_year_to_date="+month_year_to_date+"&"+csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			$('#results').html('');
			$('#results').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(res) {
			$('#advance').show();
			$('#results').html(res);
			$('#load').hide();
		}
	});
	return false;  //stop the actual form post !important!
});

//export to excel
$('#item-export').click(function() {
	var entity               = $("#entity").val();
	var customer    		 = $("#customer").val();
	var lead_ids   			 = $("#lead_ids").val();
	var month_year_from_date = $("#month_year_from_date").val();
	var month_year_to_date   = $("#month_year_to_date").val();
	
	var item_name            = $("#item_name").val();
	var item_category		 = $("#item_category").val();
	var item_type		 	 = $("#item_type").val();

	var url = site_base_url+'sales_forecast/export_data/';
	
	var form = $('<form action="' + url + '" method="post">' +
	  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
	  '<input type="hidden" name="entity" value="' +entity+ '" />' +
	  '<input type="hidden" name="customer" value="' +customer+ '" />' +
	  '<input type="hidden" name="lead_ids" value="' +lead_ids+ '" />' +
	  '<input type="hidden" name="month_year_from_date" value="' +month_year_from_date+ '" />' +
	  '<input type="hidden" name="month_year_to_date" value="' +month_year_to_date+ '" />' +
	  '<input type="hidden" name="item_name" value="' +item_name+ '" />' +
	  '<input type="hidden" name="item_type" value="' +item_type+ '" />' +
	  '<input type="hidden" name="item_category" value="' +item_category+ '" /></form>');
	$('body').append(form);
	$(form).submit();
	return false;
});

//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////