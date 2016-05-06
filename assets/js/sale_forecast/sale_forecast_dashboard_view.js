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

function showFilter(forecast_type) {
	var fsl_height = parseInt($(window).height()) - 80;
	fsl_height = fsl_height + 'px';
	
	switch(forecast_type) {
		case 'F':
		case 'A':
			var top_pixel   = '370px';
			var width_pixel = '280px';
			var left_pixel  = ($(window).width() - 320) /2 + 'px';
        break;
		case 'FA':
			var top_pixel   = '570px';
			var width_pixel = '1054px';
			var left_pixel  = ($(window).width() - 1100) /2 + 'px';
        break;
	}
	
	$.ajax({
		type: "POST",
		url: site_base_url+"sales_forecast/show_popup_filter/"+forecast_type,
		async: false,
		// dataType: "html",
		data: csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			
		},
		success: function(res) {
			$('#popup-filter-section').html(res);
			$.blockUI({
				message:$('#popup-filter-section'),
				css:{border: '2px solid #999', color:'#333', padding:'8px', top:top_pixel, left:left_pixel, width: width_pixel, position: 'absolute'},
				focusInput: false
			});
			$( "#popup-filter-section" ).parent().addClass( "no-scroll" );
		}
	});
}


//export to excel
$('#item_export').click(function() {

	var month_year_from_date = forecast_entity_month_year_from_date;
	var month_year_to_date   = forecast_entity_month_year_to_date;
	
	var item_name            = $("#item_name").val();
	var item_category		 = $("#item_category").val();
	var item_type		 	 = $("#item_type").val();

	var url = site_base_url+'sales_forecast/export_data/';
	
	var form = $('<form action="' + url + '" method="post">' +
	  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
	  '<input type="hidden" name="month_year_from_date" value="' +month_year_from_date+ '" />' +
	  '<input type="hidden" name="month_year_to_date" value="' +month_year_to_date+ '" />' +
	  '<input type="hidden" name="item_name" value="' +item_name+ '" />' +
	  '<input type="hidden" name="item_type" value="' +item_type+ '" />' +
	  '<input type="hidden" name="item_category" value="' +item_category+ '" /></form>');
	$('body').append(form);
	$(form).submit();
	return false;
});

//export to excel
$('#actual_item_export').click(function() {
	
	var month_year_from_date = actual_entity_month_year_from_date;
	var month_year_to_date   = actual_entity_month_year_to_date;
	
	var item_name            = $("#actual_item_name").val();
	var item_category		 = $("#actual_item_category").val();
	var item_type		 	 = $("#actual_item_type").val();

	var url = site_base_url+'sales_forecast/export_actual_data/';
	
	var form = $('<form action="' + url + '" method="post">' +
	  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
	  '<input type="hidden" name="month_year_from_date" value="' +month_year_from_date+ '" />' +
	  '<input type="hidden" name="month_year_to_date" value="' +month_year_to_date+ '" />' +
	  '<input type="hidden" name="item_name" value="' +item_name+ '" />' +
	  '<input type="hidden" name="item_type" value="' +item_type+ '" />' +
	  '<input type="hidden" name="item_category" value="' +item_category+ '" /></form>');
	$('body').append(form);
	$(form).submit();
	return false;
});

//export to excel
$('#export_compare_data').click(function() {

	var entity 				 = compare_entity;
	var services 	 		 = compare_service;		
	var practices 	 		 = compare_practice;
	var industries 	 		 = compare_industry;
	var customer        	 = compare_customer;
	var lead_ids	 		 = compare_lead_ids;
	var month_year_from_date = actual_entity_month_year_from_date;
	var month_year_to_date   = actual_entity_month_year_to_date;
	
	var item_name            = $("#compare_item_name").val();
	var item_category		 = $("#compare_item_category").val();
	var item_type		 	 = $("#compare_item_type").val();

	var url = site_base_url+'sales_forecast/export_compare_data/';
	
	var form = $('<form action="' + url + '" method="post">' +
	  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
	  '<input type="hidden" name="entity" value="' +entity+ '" />' +
	  '<input type="hidden" name="services" value="' +services+ '" />' +
	  '<input type="hidden" name="practices" value="' +practices+ '" />' +
	  '<input type="hidden" name="industries" value="' +industries+ '" />' +
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

$('#grid_close_entity').click(function() {
	$('#entity_charts_info_export').slideUp('fast', function(){ 
		$('#entity_charts_info').css('display','none');
	});
})
$('#grid_close_actual_entity').click(function() {
	$('#entity_actual_charts_info_export').slideUp('fast', function(){ 
		$('#entity_actual_charts_info').css('display','none');
	});
})
$('#grid_close_compare_entity').click(function() {
	$('#compare_charts_info_export').slideUp('fast', function(){ 
		$('#compare_charts_info').css('display','none');
	});
})

//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////