/*
 *@Country View
 *@Region Settings Module
*/

$(document).ready(function() {
 $('.error').hide();
	// $('a.edit').click(function() {
	$(document).delegate('a.editConty','click',function() {
		alert('country');
		var url = $(this).attr('href');
		$('.in-content').load(url +" .in-content",function(){
			dtTable();
		});
		return false;
	});

	$('button#country_cancl').click(function() {
		window.location.href="regionsettings/region_settings/country"
		return false;
	});
	
	// $('button.positive').click(function() {
	$(document).delegate('#btnAddCountry','click',function() {
		
		$('.error').hide();
		var region  = $('#country_region_id').val() ;
			if(region == ""){
				$('.error').show();
				return false;
			}
		var country  = $('#country_country_name').val();
			if(country == ""){
				$('td#error2.error').show();
				return false;
		}
		$('#country_form').submit();
    });
	
	$(document).delegate('button.negative','click',function() {
		window.location.href=site_base_url+"regionsettings/region_settings/country"
		return false;
	});
});

$(function() {
	dtTable();
});

function dtTable() {
	$('.cntry-data-tbl').dataTable({
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
function checkStatus_Cntry(id) {
	var formdata = { 'data':id, 'type':'cntry' }
	formdata[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: "POST",
		url: site_base_url+'regionsettings/ajax_check_status_rcsl/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			$('#dialog-err-cntry').empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#dialog-err-cntry').show();
				$('#dialog-err-cntry').append('One or more User / Customer currently mapped to this Country. This cannot be deleted.');
				$('html, body').animate({ scrollTop: $('#dialog-err-cntry').offset().top }, 500);
				setTimeout('timerfadeout()', 4000);
			} else {
				$.blockUI({
					message:'<br /><h5>Are You Sure Want to Delete this Country?<br />(It will delete all the States & Locations)</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDeleteCountry('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
					css:{width:'440px'}
				});
			}
		}          
	});
return false;
}

function processDeleteCountry(id) {
	window.location.href = 'regionsettings/country_delete/delete/'+id;
}

function cancelDel() {
    $.unblockUI();
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}

$('#errors, #confirm').fadeOut(4000);
/////////////////