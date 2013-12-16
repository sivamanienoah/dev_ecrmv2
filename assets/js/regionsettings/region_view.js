/*
 *@Region View
 *@Region Settings Module
*/

// "site_base_url" is global javascript variable 
$(document).ready(function() {
	$('#error1').hide();
	//$('a.edit').click(function() {
	$(document).delegate('a.editReg','click',function() {
		$('#error1').hide();
		var url = $(this).attr('href');		
		$('.in-content').load(url +" .in-content",function(){
			datTable();
		});
		return false;
	});
	
	// $('button.positive').click(function() {
	$(document).delegate('button.positive','click',function() {
		$('#error1').hide();
		var region  = $("#region_name").val() ;
		if(region == ""){			
			$("#error1").show();
			return false;
		}
	});
	
	// $('button.negative').click(function() {
	$(document).delegate('button.negative','click',function() {
		window.location.href= site_base_url+"regionsettings/region_settings/region";
		return false;
	});
});


$(function() {
	datTable();
});	
	
function datTable(){
	$('.reg-data-tbl').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false,
		"bDestroy": true
	});
}

//Check status
function checkStatus(id) {
	var formdata = { 'data':id, 'type':'reg' }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'regionsettings/ajax_check_status_rcsl/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-err-msg').empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#dialog-err-msg').show();
				$('#dialog-err-msg').append('One or more User / Customer currently mapped to this Region. This cannot be deleted.');
				$('html, body').animate({ scrollTop: $('#dialog-err-msg').offset().top }, 500);
				setTimeout('timerfadeout()', 4000);
			} else {
				// var r=confirm("Are You Sure Want to Delete this Region?\n(It will Delete all the Countries, States & Locations)");
				$.blockUI({
					message:'<br /><h5>Are You Sure Want to Delete this Region?<br />(It will Delete all the Countries, States & Locations)</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
					css:{width:'440px'}
				});
			}
		}          
	});
return false;
}

function processDelete(id) {
	window.location.href = 'regionsettings/region_delete/delete/'+id;
}

function cancelDel() {
    $.unblockUI();
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}

$('#errors, #confirm').fadeOut(4000);

/////////////////