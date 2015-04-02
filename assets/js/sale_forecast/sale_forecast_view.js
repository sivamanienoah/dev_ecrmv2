/*
 *@Manage Practice
 *@Manage Practice Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$('#advance_search').hide();

function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}

//For Advance Filters functionality.
$("#advanceFiltersForecast").submit(function() {
	$('#advance').hide();
	$('#load').show();
	var entity      = $("#entity").val();
	var customer    = $("#customer").val();
	var lead_ids    = $("#lead_ids").val();
	var project_ids = $("#project_ids").val();
	var month_year_from_date = $("#month_year_from_date").val();
	var month_year_to_date   = $("#month_year_to_date").val();

	$.ajax({
		type: "POST",
		url: site_base_url+"sales_forecast/index/",
		// dataType: "json",
		data: "filter=filter"+"&lead_ids="+lead_ids+"&project_ids="+project_ids+"&customer="+customer+"&entity="+entity+'&month_year_from_date='+month_year_from_date+"&month_year_to_date="+month_year_to_date+"&"+csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			$('#results').empty();
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

function checkStatus(id) {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
	return false;
}

function processDelete(id) {
	window.location.href = site_base_url+'sales_forecast/delete_sale_forecast/update/'+id;
}

function cancelDel() {
    $.unblockUI();
}

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
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////