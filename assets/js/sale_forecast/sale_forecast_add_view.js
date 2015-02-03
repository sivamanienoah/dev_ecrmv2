/*
 *@Manage Practice
 *@Manage Practice Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable
$('#advance_search').hide();
$(function() {
	$('#succes_err_msg').empty();
	
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
	
	var params  = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$( "#customer_name" ).autocomplete({
		minLength: 2,
		source: function(request, response) {
			params['cust_name'] = $("#customer_name").val();
			$.ajax({
				url: site_base_url+"sales_forecast/ajax_customer_search",
				data: params,
				type: "POST",
				dataType: 'json',
				async: false,
				success: function(data) {
					response( data );
				}
			});
		}
	});
	
});

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

var updt = '';

if(document.getElementById('region_update')) {
	var reg = document.getElementById('region_update').value;

	if (document.getElementById('country_update'))
	var cty = document.getElementById('country_update').value;

	if (document.getElementById('state_update'))
	var st = document.getElementById('state_update').value;

	if (document.getElementById('location_update'))
	var loc = document.getElementById('location_update').value;

	if (document.getElementById('varEdit'))
	var updt = document.getElementById('varEdit').value;

	if(reg != 0 && cty != 0)
	getCountry(reg,cty,updt);

	if(cty != 0 && st != 0)
	getState(cty,st,updt);

	if(st != 0 && loc != 0)
	getLocation(st,loc,updt);
}
function getCountry(val,id,updt) {
	var sturl = "regionsettings/getCountry/"+ val+"/"+id+"/"+updt;	
	//alert("SDfds");
    $('#country_row').load(sturl);	
    return false;	
}
function getState(val,id,updt) {
	var sturl = "regionsettings/getState/"+ val+"/"+id+"/"+updt;	
    $('#state_row').load(sturl);	
    return false;	
}
function getLocation(val,id,updt) {
	var sturl = "regionsettings/getLocation/"+ val+"/"+id+"/"+updt;	
    $('#location_row').load(sturl);	
    return false;	
}

function ajxCty(){
	$("#addcountry").slideToggle("slow");
}
function ajxSt() {
	$("#addstate").slideToggle("slow");
}
function ajxLoc() {
	$("#addLocation").slideToggle("slow");
}

function ajxSaveCty(){
	if ($('#newcountry').val() == "") {
		alert("Country Required.");
	} else {
		var regionId = $("#add1_region").val();
		var newCty = $('#newcountry').val();
		getCty(newCty, regionId);
	}	

    function getCty(newCty){
		var params = {regionid: $("#region_id").val(),country_name:$("#newcountry").val(),created_by:(customer_user_id)};
		params[csrf_token_name]      = csrf_hash_token; 

		$.ajax({
            url : site_base_url + 'customers/getCtyRes/' + newCty + "/" + regionId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
				{ 
					$.post("regionsettings/country_add_ajax",params, 
					function(info){$("#country_row").html(info);});
					$("#addcountry").hide();

					//var regId = $("#add1_region").val();
					$("#state_row").load("regionsettings/getState");
				}
                else
				{ 
					alert('Country Exists.'); 
				}
            }
        });
	}
}
function ajxSaveSt() {
	if ($('#newstate').val() == "") {
		alert("State Required.");
	} else {
		var cntyId = $("#add1_country").val()
		var newSte = $('#newstate').val();
		getSte(newSte,cntyId);
	}
		
	function getSte(newSte,cntyId) {
		var params = {countryid: $("#add1_country").val(),state_name:$("#newstate").val(),created_by:(customer_user_id)};
		params[csrf_token_name]      = csrf_hash_token; 
			
		$.ajax({
            url : site_base_url + 'customers/getSteRes/' + newSte + "/" + cntyId,
            cache : false,
            success : function(response) {
                if(response == 'userOk') 
				{
					$.post("regionsettings/state_add_ajax",params, 
					function(info){ $("#state_row").html(info); });
					$("#addstate").hide();

					$("#location_row").load("regionsettings/getLocation");
				}
                else
				{ 
					alert('State Exists.');
				}
            }
        });
	}
}

function ajxSaveLoc() {
	if ($('#newlocation').val() == "") {
		alert("Location Required.");
	} else {
		var stId   = $("#add1_state").val();
		var newLoc = $('#newlocation').val();
		getLoc(newLoc,stId);
	}
		
	function getLoc(newLoc,stId) {
		var baseurl = $('.hiddenUrl').val();
		var params = {stateid: $("#add1_state").val(),location_name:$("#newlocation").val(),created_by:(customer_user_id)};
		params[csrf_token_name]  = csrf_hash_token; 
		$.ajax({
			url : site_base_url + 'customers/getLocRes/' + newLoc + '/' +stId,
			cache : false,
			success : function(response){
				if(response == 'userOk') 
				{
					$.post("regionsettings/location_add_ajax",params, 
					function(info){ $("#location_row").html(info); });
					$("#addstate").hide();
				}
				else
				{
					alert('Location Exists.');
				}
			}
		});
	}
}

//pre-populate the default region, country, state & location
if(usr_level >= 2 && cus_updt != 'update' ) {
	getDefaultRegion(usr_level, cus_updt);
}

function getDefaultRegion(lvl, upd) {
	var sturl = "regionsettings/getRegDefault/"+lvl+"/"+upd;
    $('#def_reg').load(sturl);
    return false;
}
function getDefaultCountry(id, upd) {
	var sturl = "regionsettings/getCntryDefault/"+id+"/"+upd;
    $('#def_cntry').load(sturl);
    return false;	
}
function getDefaultState(id, upd) {
	var sturl = "regionsettings/getSteDefault/"+id+"/"+upd;
    $('#def_ste').load(sturl);
    return false;	
}
function getDefaultLocation(id, upd) {
	var sturl = "regionsettings/getLocDefault/"+id+"/"+upd;
    $('#def_loc').load(sturl);
    return false;	
}
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////