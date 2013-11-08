/*
 *@Region View
 *@Region Settings Module
*/

// "site_base_url" is global javascript variable 
 
 $('.first').addClass('1');
$('.prev').addClass('1');
$('.pagedisplay').addClass('1');
$('.next').addClass('1');
$('.last').addClass('1');
$('.pagesize').addClass('1');
$(document).ready(function() {
	$('#error1').hide();
	$('a.edit').click(function() {
	// $('a.edit').delegate('click', function() {
		$('#error1').hide();
		var url = $(this).attr('href');
		$('.in-content').load(url);
		return false;
	});
	$('button.positive').click(function() { 
		$('#error1').hide();
		var region  = $("#region_name").val() ;
		if(region == ""){			
			$("#error1").show();
			return false;
		}
	});
	
	$('button.negative').click(function() {
		window.location.href= site_base_url+"regionsettings/region_settings/region";
		return false;
	});

	$('button.search').click(function() {
		var search = $('#search-val').val();
		$('#error1').hide();
		var stencode=encodeURIComponent(search);
		var linkUrl = "regionsettings/region_search/0/"+stencode;
		//alert(linkUrl);
		//$('.in-content').load(linkUrl);
		// $('#ui-tabs-3').load(linkUrl,function() {
		$('.in-content').load(linkUrl,function() {
			$('#region_form').attr("action","./regionsettings/region");
		});
		return false;
	});
});

$('.checkUser').hide();
    $('.checkUser1').hide();
    $('#region_name').blur(function(){
        
        if( $('#region_name').val().length >= 3 )
            {
              var username = $('#region_name').val();
              getResult(username); 
            }
        return false;
    });
    function getResult(name){
        var baseurl = $('.hiddenUrl').val();
            $.ajax({
            url : baseurl + 'regionsettings/getResultfromRegion/' + name,
            cache : false,
            success : function(response){
                $('.checkUser').hide();
                if(response == 'userOk') {$('.checkUser').show(); $('.checkUser1').hide();}
                else { $('.checkUser').hide(); $('.checkUser1').show();}
            }
        });
	}

$(function() {
	$('.reg-data-tbl').dataTable({
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
});	
	
/////////////////