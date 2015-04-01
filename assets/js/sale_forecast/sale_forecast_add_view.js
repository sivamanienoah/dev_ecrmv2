/*
 *@Sales Forecast
 *@Sales Forecast Controller
*/
// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable

$('#advance_search').hide();

$('#lead-data').hide();
$('#project-data').hide();
$('#leaddetail').hide();
$('#project-ms-detail').hide();

var params  = {};
params[csrf_token_name] = csrf_hash_token;

$(function() {
	if(url_segment[3] == 'update' && $.isNumeric(url_segment[4])) {
		// alert(url_segment[4] + ' ' +job_id+ ' ' +customer_id); return false;
		$(':radio[value="' + sf_categ + '"]').attr('checked', 'checked');
		get_customers(sf_categ, customer_id);
		get_records(customer_id, job_id);
		get_lead_detail(job_id, url_segment[4]);
	}
	
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
	
	monthyear_datepicker();
	
	$('#for_month_year').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'MM yy',
		showButtonPanel: true,
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
	
	$( "#month_year_from_date, #month_year_to_date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'MM yy',            
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
	
	/* var params  = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$( "#company_name" ).autocomplete({
		minLength: 2,
		source: function(request, response) {
			params['cust_name'] = $("#company_name").val();
			$.ajax({
				url: site_base_url+"sales_forecast/ajax_customer_search",
				data: params,
				type: "POST",
				dataType: 'json',
				async: false,
				success: function(data) {
					response( data );
					$('.customer_regions').fadeIn();
				}
			});
		},
		select: function(event, ui) {
			// $('#customer_id').val(ui.item.id);
			$('#region_id option[value='+ui.item.reg+']').attr('selected','selected');
			getCountry(ui.item.reg,ui.item.cty,updt);
			getState(ui.item.cty,ui.item.ste,updt);
			getLocation(ui.item.ste,ui.item.loc,updt);
			$('.customer_regions').fadeOut();
		}
	}); */
	
});

function get_customers(data_type, cust_id) {
		
	$.ajax({
		url: site_base_url+"sales_forecast/getCustomerRecords/"+data_type+"/"+cust_id,
		data: params,
		type: "POST",
		dataType: 'json',
		async: false,
		success: function(data) {
			// console.info(data.customers);
			$('#customer_id').html(data.customers);
		}
	});
}

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

function get_lead_detail(id, sf_id) {

	$('#leaddetail').hide();
	$('#show-lead-detail').empty();
	$('#project-ms-detail').hide();
	$('#show-project-ms-detail').empty();
	
	if(id=='')
	return false;
	
	params['id'] = id;
	category = $('input[name=category]:checked', '.addForm').val();
	params['category'] = category;
	params['sf_id'] = sf_id;
	
	$.ajax({
		url: site_base_url+"sales_forecast/getLeadDetail/",
		data: params,
		type: "POST",
		dataType: 'json',
		async: false,
		success: function(data) {
			$('#show-lead-detail').html(data.det);
			$('#leaddetail').show();
			if(category == 2) {
				$('#show-project-ms-detail').html(data.ms_det);
				$('#project-ms-detail').show();
			}
		}
	});
	
}

function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}

//For Advance Filters functionality.
$("#advanceFiltersForecast").submit(function() {
	$('#advance').hide();
	$('#load').show();
	var entity     = $("#entity").val();
	var customer   = $("#customer").val();
	var lead_names = $("#lead_names").val();
	var month_year_from_date = $("#month_year_from_date").val();
	var month_year_to_date   = $("#month_year_to_date").val();

	$.ajax({
		type: "POST",
		url: site_base_url+"sales_forecast/index/",
		// dataType: "json",
		data: "filter=filter"+"&lead_names="+lead_names+"&customer="+customer+"&entity="+entity+'&month_year_from_date='+month_year_from_date+"&month_year_to_date="+month_year_to_date+"&"+csrf_token_name+'='+csrf_hash_token,
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

//Adding multiple milestone rows - Start here


function monthyear_datepicker() {
	$('input[name^="for_month_year"]').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'MM yy',
		showButtonPanel: true,
		minDate: new Date(cur_year, cur_month, 1),
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
}

function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
	return false;
	else
	return true;
}

function add_sales_forecast(sf_id) {
	var form_data = $('#add_sales_forecast_form').serialize();
	$('.layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	$.ajax({
		url : site_base_url + 'sales_forecast/save_sale_forecast/'+sf_id,
		cache : false,
		type: "POST",
		dataType: 'json',
		data:form_data,
		success : function(response) {
			// console.info(response);
			// return false;
			if(response.error==false) {
				addform_reset();
			}
			$('.layout').unblock();
			document.location.href = site_base_url + 'sales_forecast/add_sale_forecast/update/'+response.forecast_id;
		}
	});
	
	return false;
}

/*
* Editing Sales Forecast
*/
function editSalesForecast(sf_id) {

	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		url : site_base_url + 'sales_forecast/edit_sale_forecast/'+sf_id,
		cache: false,
		type: "POST",
		data:params,
		success : function(response) {
			// console.info(response);
			// return false;
			$('.layout').unblock();
			$('#edit_sales_forecast_container').html(response);
			$.blockUI({
				message:$('#edit_sales_forecast_container'),
				css:{ 
					border: '2px solid #999',
					color:'#333',
					padding:'8px',
					top:  '250px',
					left: ($(window).width() - 300) /2 + 'px',
					width: '350px',
					position: 'absolute',
					'overflow-y':'auto',
					'overflow-x':'hidden'
				}
			});
		}
	});
}

//For editing the milestones from Sales forecast listing page
if(!isNaN(ms_id) && (ms_id!='')) {
	editSalesForecast(ms_id);
}

//Deleting the Sales Forecast Milestone
function deleteSalesForecast(id) {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}

function processDelete(id) {
	window.location.href = 'sales_forecast/delete_sale_forecast/update/'+id+'/'+url_segment[4];
}

function cancelDel() {
	$.unblockUI();
	return false;
}


function addform_reset() {
	$('input[name=milestone_name]', '.addForm').val("");
	$('input[name=milestone_value]', '.addForm').val("");
	$('input[name=for_month_year]', '.addForm').val("");
}

//Adding multiple milestone rows - End here

//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////