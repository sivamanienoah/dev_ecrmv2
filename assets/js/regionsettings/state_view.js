/*
 *@Region View
 *@Region Settings Module
*/

// "site_base_url" is global javascript variable 
 
$(document).ready(function() {
	$('.error').hide();
	// $('a.edit').click(function() {
	$(document).delegate('a.editSte','click',function() {
		var url = $(this).attr('href');
		$('.in-content').load(url +" .in-content", function(){
			datStTable();
		});
		return false;
	});
	
	// $('button.negative').click(function() {
	$(document).delegate('button#state_cancl','click',function() {
		window.location.href= site_base_url+"regionsettings/region_settings/state"
		return false;
	});
	
	// $('.positive').click(function() {
	$(document).delegate('.positive','click',function() {
		$('.error').hide();
		var region  = $('#st_regionid').val() ;
		if(region == 0){
			$('#errorreg').show();
			return false;
		}
		var country  = $('#country_id').val() ;
		if(country == 0){
			$('.error').show();
			return false;
		}
		var state  = $('#state_name').val() ;
		if(state == 0){
			$('td#error2.error').show();
			return false;
		}
		$('#state_form').submit();
    });
});

var id='';
function getCountryst(val,id) {
	var sturl = "regionsettings/getCountryst/"+ val+"/"+id;	
    $('#country_row').load(sturl);	
    return false;	
}

$(function() {
	datStTable();
});

function datStTable() {
	$('.ste-data-tbl').dataTable({
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
function checkStatus_Ste(id) {
	var formdata = { 'data':id, 'type':'ste' }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'regionsettings/ajax_check_status_rcsl/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-err-ste').empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#dialog-err-ste').show();
				$('#dialog-err-ste').append('One or more User / Customer currently mapped to this State. This cannot be deleted.');
				$('html, body').animate({ scrollTop: $('#dialog-err-ste').offset().top }, 500);
				setTimeout('timerfadeout()', 4000);
			} else {
				$.blockUI({
					message:'<br /><h5>Are You Sure Want to Delete this State?<br />(It will delete all the Locations)</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDeleteState('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
					css:{width:'440px'}
				});
			}
		}          
	});
return false;
}

function processDeleteState(id) {
	window.location.href = 'regionsettings/state_delete/delete/'+id;
}

//Check status for Location
function checkStatus_Loc(id) {
	var formdata = { 'data':id, 'type':'loc' }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'regionsettings/ajax_check_status_rcsl/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-err-loc').empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#dialog-err-loc').show();
				$('#dialog-err-loc').append('One or more User / Customer currently mapped to this Location. This cannot be deleted.');
				$('html, body').animate({ scrollTop: $('#dialog-err-loc').offset().top }, 500);
				setTimeout('timerfadeout()', 4000);
			} else {
				$.blockUI({
					message:'<br /><h5>Are You Sure Want to Delete this Location?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDeleteLocation('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
					css:{width:'440px'}
				});
			}
		}          
	});
return false;
}

function processDeleteLocation(id) {
	window.location.href = 'regionsettings/location_delete/delete/'+id;
}

function cancelDel() {
    $.unblockUI();
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}

$('#errors, #confirm').fadeOut(4000);
/////////////////