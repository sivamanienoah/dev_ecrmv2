/*
 *@Level View
 *@Region Settings Module
*/

// "url_segment" is global Array 
 
var level_ids = []; 

var nc_form_msg  = '<div class="new-cust-form-loader">Loading Content.<br />';
nc_form_msg     += '<img src="assets/img/indicator.gif" alt="wait" /><br /> Thank you for your patience!</div>';

$(document).ready(function() {

		 if(url_segment[3] == 'update' && is_numeric(url_segment[4])) { 
				var regionUrl = 'regionsettings/getCountryList/'+ level_ids['country_ids'];
				$('#country_row').load(regionUrl);	
		 }
		 
		 if(url_segment[3] == 'update' && is_numeric(url_segment[4])) { 
				var countryUrl = 'regionsettings/getStateList/'+ level_ids['state_ids'];
				$('#state_row').load(countryUrl);
		 } 

		 if(url_segment[3]=='update' && is_numeric(url_segment[4])) { 
			var locationUrl = 'regionsettings/getLocationList/'+ level_ids['location_ids'];
			$('#location_row').load(locationUrl);   
		 } 


		$('.checkUser').hide();
		$('.checkUser1').hide();
		$('#level_id').blur(function(){        
			if( $('#level_id').val().length >= 3 )
				{
				  var username = $('#level_id').val();
				  getResult(username); 
				}
			return false;
		});

		function getResult(name){
			var baseurl = $('.hiddenUrl').val();
				$.ajax({
				url : baseurl + 'regionsettings/getResultfromdb/' + name,
				cache : false,
				success : function(response){
					$('.checkUser').hide();
					if(response == 'userOk') {$('.checkUser').show(); $('.checkUser1').hide();}
					else { $('.checkUser').hide(); $('.checkUser1').show();}
				}
			});
		}	

		$('#region_country').change(function() { 
		   var countryValues = $('#region_country').val();
		   var regionUrl = 'regionsettings/getCountryList/'+ countryValues;
		   $('#country_row').load(regionUrl);
		});

 });

function getStateLists() {
		var stateValues = $('#country_state').val();
		var countryUrl = 'regionsettings/getStateList/'+ stateValues;
	    $('#state_row').load(countryUrl);
}

function getLocationLists() {
	    var locationValues = $('#state_location').val();
        var locationUrl = 'regionsettings/getLocationList/'+ locationValues;
	    $('#location_row').load(locationUrl);
}

function ndf_cancel() {
       $.unblockUI();
       return false;
}

$(function() {
	$('.modal-new-cust').click(function(){
		var url = $(this).attr('href');
		$.blockUI({
					message:nc_form_msg,
					css: {width: '690px', marginLeft: '50%', left: '-345px', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default'},
					overlayCSS: {backgroundColor:'#000000', opacity: '0.9', cursor: 'wait'}
				});
		$.get(
			url,
			{},
			function(data){
				$('.new-cust-form-loader').slideUp(500, function(){
					$(this).parent().css({backgroundColor: '#535353', color: '#cccccc'});
					$(this).css('text-align', 'left').html(data).slideDown(500, function(){
						$('.error-cont').css({margin:'10px 25px 10px 0', padding:'10px 10px 0 10px', backgroundColor:'#CEB1B0'});
					});
				})
			}
		);
		return false;
	});
});

// Data table 

$(function() {
	$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager"),positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});

function cancel() {
 window.history.back();
}

/////////////////