/*
 *@Region,Country,State & Location View
 *@Region Settings Module
 *"site_base_url" is global javascript variable
*/

$(document).ready(function() {
	$('#error1').hide();
	// $('a.edit').click(function() {
	$(document).delegate('a.editRegion','click',function() {
		$('#error1').hide();
		var url = $(this).attr('href');
		block_msg(); //block the page until response will load
		$('.in-content').load(url +" .in-content",function(){
			datTable();
			$.unblockUI(); //unblock the page once response loaded
		});
		return false;
	});
	
	$(document).delegate('#btnAddRegion','click',function() {
		$('#error1').hide();
		var region  = $("#region_name").val() ;
		if(region == ""){			
			$("#error1").show();
			return false;
		}
	});
	
	$(document).delegate('button#reg_cancel','click',function() {
		window.location.href= site_base_url+"regionsettings/region_settings/region";
		return false;
	});
});

$(document).ready(function() {
	$('.error').hide();
	$(document).delegate('a.editCountry','click',function() {
		var url = $(this).attr('href');
		block_msg();
		$('.in-content').load(url +" .in-content",function(){
			datTable();
			$.unblockUI();
		});
		return false;
	});

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
	
	$(document).delegate('#country_cancel','click',function() {
		window.location.href=site_base_url+"regionsettings/region_settings/country"
		return false;
	});
});

$(document).ready(function() {
	$('.error').hide();
	$(document).delegate('a.editState','click',function() {
		var url = $(this).attr('href');
		block_msg();
		$('.in-content').load(url +" .in-content", function(){
			datTable();
			$.unblockUI();
		});
		return false;
	});
	
	$(document).delegate('button#state_cancel','click',function() {
		window.location.href= site_base_url+"regionsettings/region_settings/state"
		return false;
	});
	
	$(document).delegate('#btnAddState','click',function() {
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

//for location
$(document).ready(function() {
	$('.error').hide();
	$(document).delegate('a.editLocation','click',function() {
		var url = $(this).attr('href');
		block_msg();
		$('.in-content').load(url +" .in-content", function() {
			datTable();
			$.unblockUI();
		});
		return false;
	});

	$(document).delegate('button#location_cancel','click',function() {
		window.location.href= site_base_url+"regionsettings/region_settings/location";
		return false;
	});

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
function getCountryst(val,id) {
	var sturl = "regionsettings/getCountryst/"+ val+"/"+id;	
    $('#country_row').load(sturl);	
    return false;	
}

var id='';
function getCountrylo(val,id) {
	var sturl = "regionsettings/getCountrylo/"+ val+"/"+id;
	$('#country_row1').load(sturl);
	return false;	
}
function getStateloc(val,id) {
	var sturl = "regionsettings/getStateloc_all/"+ val+"/"+id;		
	$('#state_row').load(sturl);	
	return false;	
}

function datTable(){
	$('.dataTable').dataTable({
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

//Check status before delete the Region
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
					message:'<br /><h5>Are You Sure Want to Delete this Region?<br />(It will Delete all the Countries, States & Locations)</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDeleteRegion('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
					css:{width:'440px'}
				});
			}
		}          
	});
return false;
}

function processDeleteRegion(id) {
	window.location.href = site_base_url+'regionsettings/region_delete/delete/'+id;
}

//Check status before delete the Country
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
	window.location.href = site_base_url+'regionsettings/country_delete/delete/'+id;
}

//Check status before delete the State
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
	window.location.href = site_base_url+'regionsettings/state_delete/delete/'+id;
}

//Check status before delete the location
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
	window.location.href = site_base_url+'regionsettings/location_delete/delete/'+id;
}

function cancelDel() {
    $.unblockUI();
}

function block_msg() {
	$.blockUI({ css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .5, 
            color: '#fff' 
        } }); 
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}

$('#errors, #confirm').fadeOut(4000);

/////////////////