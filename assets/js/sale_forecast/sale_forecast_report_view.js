/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

$('#advance_search').hide();

function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}

$(function() {
	
	$( "#category_for_lead" ).on( "click", function() {
		get_customers(1);
		$('#lead-data').hide();
		$('#project-data').hide();
		$('#leaddetail').hide();
		$('#project-ms-detail').hide();
	});
	
	$( "#category_for_project" ).on( "click", function() {
		get_customers(2);
		$('#lead-data').hide();
		$('#project-data').hide();
		$('#leaddetail').hide();
		$('#project-ms-detail').hide();
	});
	
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


function get_records(custid, job_id) {
	
	$('#leaddetail').hide();
	$('#project-data').hide();
	$('#project-ms-detail').hide();
	
	$('#show-lead-detail').empty();
	$('#show-project-ms-detail').empty();
	
	if(custid == '')
	return false;
	
	category = $('input[name=category]:checked', '.addForm').val();
	
	params['category'] = category;
	params['custid']   = custid;	
		
	$.ajax({
		url: site_base_url+"sales_forecast/getRecords/"+job_id,
		data: params,
		type: "POST",
		dataType: 'json',
		async: false,
		success: function(data) {
		
			// alert(data)
			if(category == 1) {
				$('#lead_job_id').html(data.records);
				$('#lead-data').show();
				$('#project-data').hide();
			} else if(category == 2) {
				$('#project_job_id').html(data.records);
				$('#lead-data').hide();
				$('#project-data').show();
			}
		}
	});
	
}

//Deleting the Sales Forecast Milestone
function deleteSalesForecast(id) {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}



function cancelDel() {
	$.unblockUI();
	return false;
}


//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////