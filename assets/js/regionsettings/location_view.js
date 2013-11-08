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
	$(document).delegate('button.negative','click',function() {
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
	var sturl = "regionsettings/getStateloc/"+ val+"/"+id;		
	$('#state_row').load(sturl);	
	return false;	
}

$(function() {
	datTable();
});

function datTable() {
	$('.loc-data-tbl').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 15,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": false,
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false,
		"bDestroy": true
	});
}
/////////////////