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

function advanced_filter(){
    alert('hi');return false;
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