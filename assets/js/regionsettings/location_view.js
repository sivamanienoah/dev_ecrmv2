/*
 *@Location View
 *@Region Settings Module
*/

// "site_base_url" is global javascript variable 
	
	
$(document).ready(function() {
	$('.error').hide();
	// $('a.edit').click(function() {
	$(document).delegate('a.editLoc','click',function() {	
		var url = $(this).attr('href');
		$('.in-content').load(url +" .in-content", function(){
			datTable();
		});
		return false;
	});

	// $('button.negative').click(function() {
	$(document).delegate('button#location_cancl','click',function() {
		window.location.href= site_base_url+"regionsettings/region_settings/location"; // site_base_url is base url
		return false;
	});

	// $('.positive').click(function() {
	$(document).delegate('#btnAddLoc','click',function() {
		$('.error').hide();
		var varRegions = $('#loc_regionid').val();
		if (varRegions == 0) {
			$('td#Varerr1.error').show();
			return false;
		}

		var varCountrys = $('#add1_country').val();
		if (varCountrys == 0) {
			$('td#err2.error').show();
			return false;
		}

		var varStates  = $('#stateid').val() ;
		if(varStates == 0){
			$('td#erro3.error').show();
			return false;
		}

		var varLocations  = $('#loc_location_name').val() ;
		if(varLocations == ""){
			$('td#err4.error').show();
			return false;
		}
		$('#location_form').submit();		
	});
});
	
var id='';
function getCountrylo(val,id) {
	var sturl = "regionsettings/getCountrylo/"+ val+"/"+id;

	$('#country_row1').load(sturl);	
	//alert(sturl);
	return false;	
}
function getStateloc(val,id) {
	var sturl = "regionsettings/getStateloc_all/"+ val+"/"+id;		
	$('#state_row').load(sturl);	
	return false;	
}

$(function() {
	datTable();
});

function datTable() {
	$('.loc-data-tbl').dataTable({
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
					message:'<br /><h5>Are You Sure Want to Delete this Location?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
					css:{width:'440px'}
				});
			}
		}          
	});
return false;
}

function processDelete(id) {
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