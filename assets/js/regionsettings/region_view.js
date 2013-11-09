/*
 *@Region View
 *@Region Settings Module
*/

// "site_base_url" is global javascript variable 
$(document).ready(function() {
	$('#error1').hide();
	//$('a.edit').click(function() {
	$(document).delegate('a.editReg','click',function() {
		$('#error1').hide();
		var url = $(this).attr('href');		
		$('.in-content').load(url +" .in-content",function(){
			datTable();
		});
		return false;
	});
	
	// $('button.positive').click(function() {
	$(document).delegate('button.positive','click',function() {
		$('#error1').hide();
		var region  = $("#region_name").val() ;
		if(region == ""){			
			$("#error1").show();
			return false;
		}
	});
	
	// $('button.negative').click(function() {
	$(document).delegate('button.negative','click',function() {
		window.location.href= site_base_url+"regionsettings/region_settings/region";
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
	datTable();
});	
	
function datTable(){
	$('.reg-data-tbl').dataTable({
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