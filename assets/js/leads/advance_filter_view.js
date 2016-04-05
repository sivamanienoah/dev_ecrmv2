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

function deleteLeads(id) {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete <br />this project?<br /><br />This will delete all the items<br />and logs attached to this Lead.</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}
/* function processDelete(id) {
	window.location.href = site_base_url+'welcome/delete_quote/'+id;
} */
function processDelete(id) {
		var formdata = {};
		formdata['id'] = id;
		formdata[csrf_token_name] = csrf_hash_token;
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url : site_base_url + 'welcome/delete_quote/',
			data: formdata,
			beforeSend:function(){
				$.blockUI();
			},
			success : function(response){
				// $.unblockUI();
				if(response['error'] == false) {
					$.blockUI({
						message:'<br /><h5>'+response['msg']+'</h5><br />',
						css:{width:'440px'}
					});
					$('#'+id).remove();
					/* var leng = $("select[name=DataTables_Table_0_length]").val();
					$('.data-tbl').dataTable({
						"aaSorting": [[ 1, "desc" ]],
						"iDisplayLength": leng,
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
					}); */
					setTimeout('timerfadeout()', 3000);
				} else {
					alert(response['msg']);
					return false;
				}				
			}
		});
}

function cancelDel() {
	$.unblockUI();
}

$.ajaxSetup ({
    cache: false
});

function timerfadeout() {
	$.unblockUI();
}

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

//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////