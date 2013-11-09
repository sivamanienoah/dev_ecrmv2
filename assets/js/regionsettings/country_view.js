/*
 *@Country View
 *@Region Settings Module
*/

$(document).ready(function() {
 $('.error').hide();
	// $('a.edit').click(function() {
	$(document).delegate('a.editConty','click',function() {
		var url = $(this).attr('href');
		$('.in-content').load(url +" .in-content",function(){
			dtTable();
		});
		return false;
	});

	$('button.negative').click(function() {
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

/////////////////