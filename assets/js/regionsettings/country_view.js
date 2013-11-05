/*
 *@Country View
 *@Region Settings Module
*/

$('.first').addClass('2');
$('.prev').addClass('2');
$('.pagedisplay').addClass('2');
$('.next').addClass('2');
$('.last').addClass('2');
$('.pagesize').addClass('2');

$(document).ready(function() {
 $('.error').hide();
   $('a.edit').click(function() {
    var url = $(this).attr('href');
    $('.in-content').load(url);
    return false;
  });
  $('button.negative').click(function() {
	window.location.href="regionsettings/region_settings/country"
	return false;
	});
  $('button.positive').click(function() {
    $('.error').hide();
	var region  = $('#region_id').val() ;
			if(region == ""){
				$('.error').show();
				return false;
				}
	var country  = $('#country_name').val() ;
			if(country == ""){
				$('td#error2.error').show();
				return false;
				}			
    });
	
	$('button.negative').click(function() {
	window.location.href=site_base_url+"regionsettings/region_settings/country"
	return false;
	});
	
	$('button.search').click(function() {
		var search = $('#search-vals').val();
		//alert(search);
		var stencode=encodeURIComponent(search);
		var linkUrl = "regionsettings/country_search/0/"+stencode;
		//alert(linkUrl);
		//$('.in-content').load(linkUrl);
		$('#ui-tabs-5').load(linkUrl,function() {
     $('#country_form').attr("action","./regionsettings/country");
});
		return false;
	});
    
});

$(function() {
	$('#cntry-data-tbl').dataTable({
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