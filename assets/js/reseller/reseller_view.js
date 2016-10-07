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
			if (ui.newPanel[0].id=='rt-tab-2') {
				// populateJobOverview();
			}
			if (ui.newPanel[0].id=='rt-tab-3') {
			}
			if (ui.newPanel[0].id=='rt-tab-4') {
			}
			if (ui.newPanel[0].id=='rt-tab-5') {
			}
			if (ui.newPanel[0].id=='rt-tab-6') {
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

function timerfadeout()
{
	$('.succ_err_msg').empty();
}
/////////////////