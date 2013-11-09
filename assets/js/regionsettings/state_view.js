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
	$(document).delegate('button.negative','click',function() {
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
/////////////////