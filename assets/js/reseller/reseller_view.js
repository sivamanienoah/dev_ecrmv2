/*
 *@notifications Jquery
 *@My Profile Module
*/

// 'accesspage' is global variable 
	
$(document).ready(function() {
	$( "#reseller_tabs" ).tabs({
		beforeActivate: function( event, ui ) {
			if (ui.newPanel[0].id=='rt-tab-1')
				// loadExistingTasks();
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
	alert('getAddContractForm');
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

function reset_add_form()
{
	// alert('tst');
	$('#add_contract_form').html('');
	$('#create_contract_btn').show();
}

function timerfadeout()
{
	$('.succ_err_msg').empty();
}
/////////////////