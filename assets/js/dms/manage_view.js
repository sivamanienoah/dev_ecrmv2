/*
 *@Sale Forecast
 *@Sale Forecast Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspage are global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

$(function(){
	var config = {
		'.chzn-select'           : {},
		'.chzn-select-deselect'  : {allow_single_deselect:true},
		'.chzn-select-no-single' : {disable_search_threshold:10},
		'.chzn-select-no-results': {no_results_text:'Oops, nothing found!'},
		'.chzn-select-width'     : {width:"95%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
});

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
				$('#'+type).show();
				$('#'+type).html("<span class='ajx_success_msg'>Collater Admin Users Updated.</span>");
				show_updating();
				$.unblockUI();
			} else {
				$('#'+type).show();
				$('#'+type).html("<span class='ajx_failure_msg'>"+data.error+".</span>");
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