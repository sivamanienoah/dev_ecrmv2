
	//for ie ajax loading issue appending random number
	var sturl = site_base_url+"enquiries/advance_filter_search/?"+Math.random();
	$('#advance_search_results').load(sturl);


function deleteEnquiry(id, title) { 
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete this <br />'+title+'?<br /><br /></h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="enquiryDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}

function enquiryDelete(id) {
	window.location.href = site_base_url+'enquiries/delete_enquiry/'+id;
}