/*
 *@Sale Forecast
 *@Sale Forecast Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspage are global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

function setDmsMembers(type) 
{
	$('#resmsg3').hide();
	var members = $('#'+type).val();

	if(!confirm("Are you sure to update?")) {
		$('#resmsg3').show();
		// $('#resmsg3').html("<span class='ajx_failure_msg'>Select Any members!.</span>");
		return false;
	}
	
	show_processing();
	 
	$.ajax({
		type: 'POST',
		url: site_base_url+'manage_dms/set_dms_users/',
		dataType: 'json',
		data: 'members='+members+'&type='+type+'&'+csrf_token_name+'='+csrf_hash_token,
		success: function(data) {
			if (data.error == false) {
				$('#'+type+'_msg').show();
				$('#'+type+'_msg').html("<span class='ajx_success_msg'>Updated.</span>");
				// show_updating();
				$.unblockUI();
			} else {
				$('#'+type+'_msg').show();
				$('#'+type+'_msg').html("<span class='ajx_failure_msg'>"+data.error+".</span>");
				$.unblockUI();
			}
		}
	});
	setTimeout('timerfadeout()', 3000);
	return false;
}

function show_processing(){
	$.blockUI({
		message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
		css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
	});		
}

function show_updating(){
	setTimeout(function(){
		$.blockUI({
			message:'<h4>Status Updating...</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		window.location.reload(true);
	},500);
	$.unblockUI();
}

function timerfadeout()
{
	$('.error-msg').empty();
}

//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////