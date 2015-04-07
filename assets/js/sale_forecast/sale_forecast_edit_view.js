/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

$(function() {

	$('#ui-datepicker-div').addClass('blockMsg');

	$( ".file-tabs-close-confirm-tab" ).on( "click", function() {
		$.unblockUI();
		return false;
	});
	
	$('#for_month_year').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'MM yy',
		showButtonPanel: true,
		minDate: new Date(<?php echo date('Y') ?>, <?php echo date('m') ?>, 1),
		onClose: function(input, inst) {
			var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
		},
		beforeShow: function(input, inst) {
			if ((selDate = $(this).val()).length > 0) 
			{
				iYear = selDate.substring(selDate.length - 4, selDate.length);
				iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
				$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
			}
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
});

function update_sf_data(id) {
	var form_data = $('#sales_forecast_edit_form').serialize();	
	$('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	
	$.ajax({
		url: site_base_url + 'sales_forecast/save_sale_forecast_milestone/'+id,
		cache: false,
		type: "POST",
		dataType: 'json',
		data: form_data,
		success: function(response){
			if(response.result=='ok') {
				setTimeout(function(){
					$.blockUI({
						message:'<h4>Updating...</h4><img src="'+site_base_url+'assets/img/ajax-loader.gif" />',
						css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
					});
					window.location.href = site_base_url + "sales_forecast/add_sale_forecast/update/"+url_segment[4];
					// window.location.reload(true);
				},500);
			} else {
				alert("Update Failed");
				$.unblockUI();
			}
		}
	});
}






//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////