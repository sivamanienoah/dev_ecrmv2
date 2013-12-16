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

//-----------------------------------X---------------------------------------X---------------