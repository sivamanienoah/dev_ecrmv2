/*
 *@lead confirmation view Practice
 *@Welcome Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable
$('.del_file').hide();
$( "#tabs" ).tabs();
$(function() {
	$('#succes_err_msg').empty();
	$('#ui-datepicker-div').addClass('blockMsg');
	
	/* $('#date_start').datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		onSelect: function(date) {
			if($('#date_due').val!='')
			{
				$('#date_due').val('');
			}
			var return_date = $('#date_start').val();
			$('#date_due').datepicker("option", "minDate", return_date);
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
	$('#date_due').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true }); */
	
	$('#date_start').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true, onSelect: function(date) {
		if($('#date_due').val!='')
		{
			$('#date_due').val('');
		}
		var return_date = $('#date_start').val();
		$('#date_due').datepicker("option", "minDate", return_date);
	}});
	$('#date_due').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true });
	
	if(project_category == 1) {
		$('#project_center_tr').show();
		$('#cost_center_tr').hide();
	} else if(project_category == 2) {
		$('#cost_center_tr').show();
		$('#project_center_tr').hide();
	} else {
		$('#cost_center_tr').hide();
		$('#project_center_tr').hide();
	}
	
	datefield_datepicker();
	monthyear_datepicker();

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

	updt = 'updt';

	if(reg != 0 && cty != 0)
	getCountry(reg,cty,updt);

	if(cty != 0 && st != 0)
	getState(cty,st,updt);

	if(st != 0 && loc != 0)
	getLocation(st,loc,updt);
}
function getCountry(val,id,updt) {
	var sturl = site_base_url+"regionsettings/getCountry/"+ val+"/"+id+"/"+updt;	
	//alert("SDfds");
    $('#country_row').load(sturl);	
    return false;	
}
function getState(val,id,updt) {
	var sturl = site_base_url+"regionsettings/getState/"+ val+"/"+id+"/"+updt;	
    $('#state_row').load(sturl);	
    return false;	
}
function getLocation(val,id,updt) {
	var sturl = site_base_url+"regionsettings/getLocation/"+ val+"/"+id+"/"+updt;	
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


function update_client(lead_id,current_tab){
	update_customer(lead_id,current_tab);		
}
function update_customer_project(lead_id,current_tab){
	update_customer(lead_id,'');
	update_project_detail(lead_id,current_tab);
}
function update_cust_proj_users(lead_id,current_tab){
	update_customer(lead_id,'');
	update_project_detail(lead_id,'');		
	update_project_users(current_tab)
}

//pre-populate the default region, country, state & location
/* if(usr_level >= 2 && cus_updt != 'update' ) {
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
} */
function update_customer(id,current_tab) {
	var form_data = $('#customer_detail_form').serialize();	
	$('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	
	$.ajax({
		url : site_base_url + 'customers/custom_update_customer',
		cache : false,
		type: "POST",
		dataType: 'json',
		data:form_data,
		success : function(response){
			if(response.result=='ok') {
				if(current_tab){
					var cs = $('.tabs-confirm li').eq(1).find("a").attr('href');
					var v = cs.split("#")
					if(v[1] !=current_tab)	$('.tabs-confirm li').eq(1).find("a").trigger('click');	
				}
				
			} else {
				alert("Update Failed");
			}
			$('.blockUI .layout').unblock();
		}
	});
}
$(".errmsg").empty();
$('#emailval').keyup(function(){
	if( $('#emailval').val().length >= 1 )
	{
		var username = $('#emailval').val();
		var filter = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		if(filter.test(username)){
			$("#positiveBtn").removeAttr("disabled");
			$(".errmsg").html('<span class="ajx_success_msg">Valid Email</span>');
		} else {
			$(".errmsg").html('<span class="ajx_failure_msg">Invaild Email.</span>');
			$("#positiveBtn").attr("disabled", "disabled");
		}
	}
	return false;
});

function update_project_detail(project_id,current_tab) {

	var err = [];
  
    if ($.trim($('#department_id_fk').val()) == 'not_select') {
        err.push('Department must be selected');
		$('#department_err').html('Department must be selected');
    }
	if ($.trim($('#resource_type').val()) == 'not_select') {
        err.push('Resource type must be selected');
		$('#resource_type_err').html('Resource type must be selected');
    }
	if ($.trim($('#practice').val()) == 'not_select') {
        err.push('Practice must be selected');
		$('#practice_err').html('Practice must be selected');
    }
    if ($('#project_name').val() == '') {
        err.push('Project name is required');
		$('#project_name_err').html('Project name is required');
    }
	if ($('#timesheet_project_types').val() == 'not_select') {
        err.push('Project billing type must be selected');
		$('#timesheet_project_types_err').html('Project billing type must be selected');
    }
	if ($('#project_types').val() == 'not_select') {
        err.push('Project type must be selected');
		$('#project_type_err').html('Project type must be selected');
    }
	if ($("input[name=project_category]").is(":checked") == false) {
        err.push('Project category must be selected');
		$('#project_category_err').html('Project category must be selected');
    } else if ($("input[name=project_category]").is(":checked") == true && $("input[name=project_category]:checked").val() == 1 && $('#project_center_value').val() == 'not_select') {
		err.push('Project center must be selected');
		$('#project_center_value_err').html('Project center must be selected');
	} else if ($("input[name=project_category]").is(":checked") == true && $("input[name=project_category]:checked").val() == 2 && $('#cost_center_value').val() == 'not_select') {
		err.push('Cost center must be selected');
		$('#cost_center_value_err').html('Cost center must be selected');
	}	
	if ($("input[name=sow_status]").is(":checked") == false) {
        err.push('SOW status must be selected');
		$('#sow_status_err').html('SOW status must be selected');
    }
	if ($('#actual_worth_amount').val() == '') {
        err.push('SOW Value is required');
		$('#sow_value_err').html('SOW Value is required');
    }
	if ($('#date_start').val() == '') {
        err.push('SOW Start Date is required');
		$('#date_start_err').html('SOW Start Date is required');
    }
	if ($('#date_due').val() == '') {
        err.push('SOW End Date is required');
		$('#date_due_err').html('SOW End Date is required');
    }
	if ($("input[name=customer_type]").is(":checked") == false) {
        err.push('Customer Type must be selected');
		$('#customer_type_err').html('Customer Type must be selected');
    }
    if (err.length > 0) {
		setTimeout('timerfadeout()', 6000);
		// $('.errmsg_confirm').html('<b>Few errors occured! Please correct them and submit again!</b><br />' + err.join('<br />'));
		// alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
        return false;
    }

	var form_data = $('#project-confirm-form').serialize();	
	$('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	
	$.ajax({
		url : site_base_url + 'welcome/update_project_info/'+project_id,
		cache : false,
		type: "POST",
		dataType: 'json',
		data:form_data,
		success : function(response) {
			if(response.result=='ok') {
				if(current_tab){
					var cs = $('.tabs-confirm li').eq(2).find("a").attr('href');
					var v = cs.split("#")
					if(v[1] !=current_tab)	$('.tabs-confirm li').eq(2).find("a").trigger('click');	
				}
			} else if(response.result=='') {
				alert("Update Failed");
			}
			$('.blockUI .layout').unblock();
		}
	});
}

function update_project_users(current_tab) {
	var form_data = $('#set-assign-users').serialize();	
	
	/* $('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	}); */
		
	$.ajax({
		url : site_base_url + 'welcome/custom_update_users',
		cache : false,
		type: "POST",
		dataType: 'json',
		data:form_data,
		success : function(response){
			if(response.result=='ok') {
				if(current_tab){
					var cs = $('.tabs-confirm li').eq(3).find("a").attr('href');
					var v = cs.split("#")
					if(v[1] !=current_tab)	$('.tabs-confirm li').eq(3).find("a").trigger('click');	
				}
			} else {
				alert("Update Failed");
			}
			$('.blockUI .layout').unblock();
		}
	});
}


function timerfadeout() {
	$('.ajx_failure_msg').empty();
}
function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
	return false;
	else
	return true;
}

function datefield_datepicker() {
	$('input[name^="expected_date[]"]').datepicker({
		dateFormat: 'dd-mm-yy',
		beforeShow : function(input, inst) {
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
}

function monthyear_datepicker() {
	$('input[name^="month_year[]"]').datepicker({
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
}

$('#milestone-tbl').delegate( '#addMilestoneRow', 'click', function () {
	var thisRow = $(this).closest('tr');
	$(this).hide();
	$("#milestone-tbl tbody tr").find('.del_file').show();	
	var obj = $(thisRow).clone().insertAfter(thisRow);
	obj.find(".project_milestone_name,.expected_date,.month_year,.amount").val("");
	obj.find(".project_milestone_name,.expected_date,.month_year,.amount").css('border','');
	obj.find('.createBtn').show();
	$('input[name^="expected_date[]"], input[name^="month_year[]"]').each(function(index){
	$(this).attr('id',index+$(this).attr("class"));
		if ($(this).hasClass('hasDatepicker')) {
			$(this).removeClass('hasDatepicker');
		} 
		datefield_datepicker();
		monthyear_datepicker();
	});
});

if($('#milestone-tbl tbody tr').length<=1){
	$('#milestone-tbl .del_file').hide();
	$('#milestone-tbl .createBtn').show();
}else{
	$('#milestone-tbl .del_file').show();
}

$('#milestone-tbl').delegate( '.del_file', 'click', function () {
	var thisRow = $(this).parent('td').parent('tr');
	$(thisRow).remove();
	$("#milestone-tbl tbody tr").each(function(){
		$("#milestone-tbl tbody tr:last").find('.createBtn').show();
	})
	if($('#milestone-tbl tbody tr').length<=1){
		$('#milestone-tbl .del_file').hide();
		$('#milestone-tbl .createBtn').show();
	}
});

function confirm_project(project_id) 
{
	$("#milestone-tbl").find('.textfield').css('border-color', '');
	var ms_error = false;
	var rowCount = $("#milestone-tbl tbody tr").length;
	//alert($("#milestone-tbl tbody tr").length);
	$("#milestone-tbl tbody tr").each(function(i){
		var innerIndex = 0;
		var textBoxClass = [];
		if($(this).find("td").find('.project_milestone_name').val()!="") {
			innerIndex++;
		} else {
			textBoxClass.push('project_milestone_name');
		}
		if($(this).find("td").find('.expected_date').val()!="") {
			innerIndex++;
		} else {
			textBoxClass.push('expected_date');
		}
		if($(this).find("td").find('.month_year').val()!="") {
			innerIndex++;
		} else {
			textBoxClass.push('month_year');
		}
		if($(this).find("td").find('.amount').val()!="") {
			innerIndex++;
		} else {
			textBoxClass.push('amount');
		}
		
		currentRow = $(this);
		
		if((innerIndex != 0 && innerIndex !=4 && rowCount==1) || rowCount>1) {
			ms_error = (textBoxClass.length>0) ? true : false;
			$.each(textBoxClass, function(index, value) {
				currentRow.find('.'+value).css('border-color', 'red');
			});
		}
	});
	
	if(ms_error == true) {
		return false;
	}
	
	if (confirm('Are you sure you want to move \nthis lead to Project?') == true) {
        move_project(project_id);
    }
}

function move_project(project_id)
{
	var form_data = $('#set-milestones').serialize();
	
	$('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	
	$.ajax({
		url : site_base_url + 'welcome/confirm_project/'+project_id,
		cache : false,
		type: "POST",
		dataType: 'json',
		data:form_data,
		success : function(response) {
			if(response.result=='fail') {
				alert("Milestone insertion failed");
				$('.blockUI .layout').unblock();
				return false;
			}
			$('.blockUI .layout').block({
				message:'<h3>Processing</h3>',
				css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
			});
			if(response.error == true) {
				if(response.errortype==1) {
					setTimeout('timerfadeout()', 6000);
					$('.tabs-confirm li').eq(1).find("a").trigger('click');
					$('.errmsg_confirm').html(response.errormsg);
					$('.blockUI .layout').unblock();
					return false;
				}
				alert(response.errormsg);
				window.location.href = site_base_url+"welcome/edit_quote" + "/" + project_id +"/";
				$.unblockUI();
			} else {
				reloadWithMessagePjt('Lead has been Successfully moved to Project', project_id);
			}
			// $('.blockUI .layout').unblock();
		}
	});
}

$(".file-tabs-close-confirm-tab").click(function() {
	$.unblockUI();
	return false;
});

function reloadWithMessagePjt(str, project_id) 
{
	var params  = {str: str};
	params[csrf_token_name] = csrf_hash_token;
	
	$.post("ajax/request/set_flash_data",params, 
		function(info){
			document.location.href = site_base_url+'project/view_project/' + project_id;}
		);

}

/*
*@Method change_project_category
*@Use Show and hide the project and cost center tr
*Author eNoah - Mani.S
*/
function change_project_category(val)
{
	if(val == 1) {
		$('#project_center_tr').show();
		$('#cost_center_tr').hide();
	}else if(val == 2) {
		$('#cost_center_tr').show();
		$('#project_center_tr').hide();
	}else {
		$('#cost_center_tr').hide();
		$('#project_center_tr').hide();
	}
}


//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////