/*
 *@Hosting View Jquery
 */

function delHosting(id) {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}
	
function processDelete(id) {
	window.location.href = 'hosting/delete_account/'+id;
}

function cancelDel() {
	$.unblockUI();
}

//For Advance Filters functionality.
$("#search_advance").click(function() {
alert('hi');return false;
	$('#search_advance').hide();
	$('#save_advance').hide();
	$('#load').show();
	
	var from_date = $("#from_date").val();
	var to_date   = $("#to_date").val();
	var sub_name        = $("#sub_name").val();
	var leadassignee = $("#leadassignee").val();
	var regionname   = $("#regionname").val();
	var countryname  = $("#countryname").val();
	var statename    = $("#statename").val();
	var locname      = $("#locname").val();
	var stage        = $("#stage").val(); 
	var customer     = $("#customer").val();
	var service      = $("#service").val();
	var lead_src     = $("#lead_src").val();
	var industry     = $("#industry").val();
	var worth        = $("#worth").val();	
	var lead_status  = $("#lead_status").val();
	var lead_indi    = $("#lead_indi").val();
	var keyword      = '';

	$.ajax({
		type: "POST",
		url: site_base_url+"hosting/advance_filter_search/search",
		cache: false,
		data: "&sub_name="+sub_name+"&from_date="+from_date+"&to_date="+to_date+"&customer="+customer+"&service="+service+"&lead_src="+lead_src+"&industry="+industry+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&lead_status="+lead_status+"&lead_indi="+lead_indi+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
		success: function(data){
			$('#advance_search_results').html(data);
			$('#load').hide();
			$('#search_advance').show();
			$('#save_advance').show();
			$("#search_type").val("search");
		}
	});
	return false;  //stop the actual form post !important!
});

function advanced_filter(){
	$('#advance_search').slideToggle('slow');
	var  keyword = $("#keyword").val("");
	var status = document.getElementById('advance_search').style.display;
	
	if(status == 'none') {
		var owner 			= $("#owner").val();
		var leadassignee 	= $("#leadassignee").val();
		var regionname 		= $("#regionname").val();
		var countryname 	= $("#countryname").val();
		var statename 		= $("#statename").val();
		var locname 		= $("#locname").val();
		var stage 			= $("#stage").val(); 
		var customer 		= $("#customer").val(); 
		var worth 			= $("#worth").val();
		var industry 		= $("#industry").val();
	} else {
		$("#owner").val("");
		$("#leadassignee").val("");
		$("#regionname").val("");
		$("#countryname").val("");
		$("#statename").val("");
		$("#locname").val("");
		$("#stage").val("");
		$("#customer").val("");
		$("#worth").val("");
		$("#industry").val("");
	}
}

//-----------------------------------X---------------------------------------X---------------