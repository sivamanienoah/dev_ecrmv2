/*
 *@Region View
 *@Region Settings Module
*/

// "site_base_url" is global javascript variable 
 
$('.first').addClass('3');
$('.prev').addClass('3');
$('.pagedisplay').addClass('3');
$('.next').addClass('3');
$('.last').addClass('3');
$('.pagesize').addClass('3');
$(document).ready(function() {
 $('button.stsearch').click(function() {    
    var st = $('#statesearch').val();
	var stencode=encodeURIComponent(st);
    var sturl = "regionsettings/state_search/0/"+ stencode;	
    //$('.in-content').load(sturl);
	$('#ui-tabs-7').load(sturl,function() {
     $('#state_form').attr("action","./regionsettings/state");
});
    return false;
  });
 $('.error').hide();
   $('a.edit').click(function() {
    var url = $(this).attr('href');
    $('.in-content').load(url);
    return false;
  });
  $('button.negative').click(function() {
	window.location.href= site_base_url+"regionsettings/region_settings/state"
	return false;
	});
  $('.positive').click(function() {
    $('.error').hide();
	var region  = $('#country_id').val() ;
			if(region == ""){
				$('.error').show();
				return false;
				}
	var country  = $('#state_id').val() ;
			if(country == ""){
				$('td#error2.error').show();
				return false;
				}			
    });
});



var id='';
function getCountryst(val,id) {
	var sturl = "regionsettings/getCountryst/"+ val+"/"+id;	
    $('#country_row').load(sturl);	
    return false;	
}

$(function() {
	$('.ste-data-tbl').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 15,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": false,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,	
	});
});
/////////////////