/*
 *@notifications Jquery
 *@My Profile Module
*/

// 'accesspage' is global variable 
	
$(document).ready(function() {
	$( "#reseller_tabs" ).tabs({
		beforeActivate: function( event, ui ) {
			if (ui.newPanel[0].id=='rt-tab-1')
				reset_add_form();
				reset_commission_form();
			if (ui.newPanel[0].id=='rt-tab-2') {
				reset_add_form();
				reset_commission_form();
			}
			if (ui.newPanel[0].id=='rt-tab-3') {
			}
			if (ui.newPanel[0].id=='rt-tab-4') {
				getAllJobs(reseller_id, '1');				
			}
			if (ui.newPanel[0].id=='rt-tab-5') {
				getAllJobs(reseller_id, '2');
			}
			if (ui.newPanel[0].id=='rt-tab-6') {
				getCustomerContact(reseller_id)
			}
			if (ui.newPanel[0].id=='rt-tab-7') {
				getAuditHistory(reseller_id);
			}
		}
	});
});

function getAddContractForm(reseller_id)
{
	// alert('getAddContractForm');
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'reseller/getContractForm/'+reseller_id,
		cache:false,
		dataType:'html',
		beforeSend: function() {
			//show loading symbol
			$('#add_contract_form').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success:function(data) {
			// console.info(data);
			$('#create_contract_btn').hide();
			$('#add_contract_form').html(data);
		}
	});
}


/*
*Edit the contract Details
*@params contract_id, contracter_user_id
*/
function editContractData(contract_id, contracter_user_id)
{
	var params 						= {};
	params[csrf_token_name] 		= csrf_hash_token;
	params['contract_id'] 			= contract_id;
	params['contracter_user_id'] 	= contracter_user_id;
	
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'reseller/getEditContractData/',
		cache:false,
		dataType:'json',
		beforeSend: function() {
			//show loading symbol
			$('#add_contract_form').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success:function(data) {
			// alert(data);
			if(data.msg == 'success'){
				$('#create_contract_btn').hide();
				$('#add_contract_form').html(data.res);
			}
		}
	});
}

/*
*Deleting the Contract data
*@params contract_id, contracter_user_id
**/
function deleteContractData(contract_id, contracter_user_id)
{
	var agree							= confirm("Are you sure you want to delete?");
	if (agree) {
		var params 						= {};
		params[csrf_token_name] 		= csrf_hash_token;
		params['contract_id'] 			= contract_id;
		params['contracter_user_id'] 	= contracter_user_id;
		$.ajax({
			type:'POST',
			data:params,
			url:site_base_url+'reseller/deleteContractData/',
			cache:false,
			dataType:'json',
			beforeSend: function() {
				//show loading symbol
			},
			success:function(data) {
				if(data.res == 'success'){
					$('#succes_add_contract_data').html("<span class='ajx_success_msg'>Deleted Successfully.</span>");
					$('#contr_'+contract_id).remove();
					setTimeout('timerfadeout()', 4000);
					// alert($('#list_contract_det tbody tr').length)
					if($('#list_contract_det tbody tr').length==0){
						reset_add_form();
					}
				} else if(data.res == 'failure'){
					$('#succes_add_contract_data').html("<span class='ajx_failure_msg'>Error in deleting contract.</span>");
				}
			}
		});
	} else {
		return false;
	}
}

function reset_add_form()
{
	$('#add_contract_form').html('');
	load_contract_grid(reseller_id);
	$('#create_contract_btn').show();
}

function load_contract_grid(contracter_user_id)
{
	// alert('tst');
	var params 						= {};
	params[csrf_token_name] 		= csrf_hash_token;
	params['contracter_user_id'] 	= contracter_user_id;
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'reseller/loadContractGrid/',
		cache:false,
		dataType:'json',
		beforeSend: function() {
			//show loading symbol
		},
		success:function(data) {
			$('#list_contract_det').html(data.res);
		}
	});
}

//get all the leads by reseller
function getAllJobs(userid, type)
{
	var type_id = '';
	if(type == 1) {
		type_id = 'reseller_lead_data';
	} else if(type == 2) {
		type_id = 'reseller_project_data';
	}
		$.ajax({
		type: "POST",
		url: site_base_url+'reseller/getResellerJobs/',
		data: '&userid='+userid+'&type='+type+'&'+csrf_token_name+'='+csrf_hash_token,
		cache: false,
		dataType:'html',
		beforeSend:function() {
			$('#'+type_id).html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			$('#'+type_id).show();
		},
		success: function(data) {
			$('#'+type_id).html(data);
			$('#'+type_id).show();
		}                                                                                   
	});
}

//get all the history
function getAuditHistory(userid)
{
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	params['userid'] = userid;
	$.post( 
		site_base_url+'reseller/getAuditHistory/',params,
		function(data) {
			if (data.error) {
				alert(data.errormsg);
			} else {
				$('#audit_history_data').html(data);
				logsDataTable();
			}
		}
	);
}

//get Customer Contact
function getCustomerContact(userid)
{
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	params['userid'] = userid;
	$.post( 
		site_base_url+'reseller/getCustomerContact/',params,
		function(data) {
			if (data.error) {
				alert(data.errormsg);
			} else {
				$('#reseller_contact_data').html(data);
				logsDataTable();
			}
		}
	);
}

function logsDataTable(){
	$('.logstbl').dataTable( {
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": false,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": false,
		"bSort": false,
		"bFilter": false,
		"bAutoWidth": false,
		"bDestroy": true,
		"oLanguage": {
		  "sEmptyTable": "No Comments Found..."
		}
	});
}
function timerfadeout()
{
	$('.succ_err_msg').empty();
}

//**Commission - Start**//
function reset_commission_form()
{
	$('#commission_form').html('');
	load_commission_grid(reseller_id);
	$('#create_commission_btn').show();
}

/**/
function load_commission_grid(contracter_user_id)
{
	// alert('tst');
	var params 						= {};
	params[csrf_token_name] 		= csrf_hash_token;
	params['contracter_user_id'] 	= contracter_user_id;
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'reseller/loadCommissionGrid/',
		cache:false,
		dataType:'json',
		beforeSend: function() {
			$('#list_commission_det').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success:function(data) {
			$('#list_commission_det').html(data.res);
		}
	});
}

function getAddCommissionForm(reseller_id)
{
	// alert('getAddContractForm');
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'reseller/getCommissionForm/'+reseller_id,
		cache:false,
		dataType:'html',
		beforeSend: function() {
			//show loading symbol
			$('#commission_form').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success:function(data) {
			// console.info(data);
			$('#create_commission_btn').hide();
			$('#commission_form').html(data);
		}
	});
}

/*
*Edit the Commission Details
*@params commission_id, contracter_user_id
*/
function editCommissionData(commission_id, contracter_user_id)
{
	var params 						= {};
	params[csrf_token_name] 		= csrf_hash_token;
	params['commission_id'] 		= commission_id;
	params['contracter_user_id'] 	= contracter_user_id;
	
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'reseller/getEditCommissionData/',
		cache:false,
		dataType:'json',
		beforeSend: function() {
			//show loading symbol
			$('#commission_form').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success:function(data) {
			// alert(data);
			if(data.msg == 'success'){
				$('#create_commission_btn').hide();
				$('#commission_form').html(data.res);
			}
		}
	});
}

/*
*Deleting the Contract data
*@params contract_id, contracter_user_id
**/
function deleteCommissionData(commission_id, contracter_user_id)
{
	var agree							= confirm("Are you sure you want to delete?");
	if (agree) {
		var params 						= {};
		params[csrf_token_name] 		= csrf_hash_token;
		params['commission_id'] 		= commission_id;
		params['contracter_user_id'] 	= contracter_user_id;
		$.ajax({
			type:'POST',
			data:params,
			url:site_base_url+'reseller/deleteCommissionData/',
			cache:false,
			dataType:'json',
			beforeSend: function() {
				//show loading symbol
			},
			success:function(data) {
				if(data.res == 'success'){
					$('#succes_add_commission_data').html("<span class='ajx_success_msg'>Deleted Successfully.</span>");
					$('#csmn_'+commission_id).remove();
					setTimeout('timerfadeout()', 4000);
					// alert($('#list_commission_det tbody tr').length)
					if($('#list_commission_det tbody tr').length==0){
						reset_commission_form();
					}
				} else if(data.res == 'failure'){
					$('#succes_add_commission_data').html("<span class='ajx_failure_msg'>Error in deleting commission.</span>");
				}
			}
		});
	} else {
		return false;
	}
}
//**Commission - End**//

//**sale history**//

$( "#sale_history" ).submit(function( event ) {
	// alert($('#hidden_curFiscalYear').val());
	var errors = [];
	var validate_form = true;
	if($('#financial_year').val() == $('#hidden_curFiscalYear').val()) {
		$('#financial_year_err').html('<span class="red">Please Select Other Financial Year.</span>');
		setTimeout('timerfadeout()', 6000);
		return false;
	}
	if(($.trim($('#financial_year').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Select Financial Year.</p>');
		$('#financial_year_err').html('<span class="red">Select Financial Year.</span>');
	} else {
		$('#financial_year_err').html('');
	}
	
	if (errors.length > 0 && validate_form == false) 
	{
		setTimeout('timerfadeout()', 6000);
		return false;
	}
	
	$.ajax({
		type: "POST",
		dataType: "html",
		url: site_base_url+'reseller/getSaleHistory/',
		data: '&financial_year='+$('#financial_year').val()+'&reseller_id='+$('#reseller_id').val()+'&'+csrf_token_name+'='+csrf_hash_token,
		cache: false,
		beforeSend:function() {
			$('#sale_history_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(data) {
			// console.info(data);
			$('#sale_history_data').html(data);
			$('#hidden_curFiscalYear').val($('#financial_year').val());
		}                                                                                   
	});
	
  event.preventDefault();
});
//**sale history**//
/////////////////