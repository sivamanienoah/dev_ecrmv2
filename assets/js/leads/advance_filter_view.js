/*
 *@Advance Filter View 
*/

$('#excel').click(function() {
	//mychanges
	var sturl = site_base_url+"welcome/excelExport/";
	document.location.href = sturl;
	return false;
});

$(function() {
	dtTable();
});	
	
function dtTable() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 1, "desc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,
		"bDestroy": true
	});
}

function deleteLeads(id) {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete <br />this project?<br /><br />This will delete all the items<br />and logs attached to this Lead.</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}
function processDelete(id) {
	window.location.href = site_base_url+'welcome/delete_quote/'+id;
}

function cancelDel() {
	$.unblockUI();
}

$.ajaxSetup ({
    cache: false
});

/*
*Save & Search script
**/
$( ".set_default_search" ).on( "click", function() {
var search_id = $( this ).val();
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"welcome/set_default_search/"+search_id+'/1',
		cache: false,
		data: csrf_token_name+'='+csrf_hash_token,
		success: function(response){
			if(response.resu=='updated') {
				$('.search-dropdown').html(response.search_div);
				saveSearchScript();
				show_search_results(search_id);
			} else {
				alert('Not updated');
			}
			
		}
	});
});
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////