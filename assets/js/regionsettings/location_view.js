/*
 *@Location View
 *@Region Settings Module
*/

	// "site_base_url" is global javascript variable 
 
	$('.first').addClass('4');
	$('.prev').addClass('4');
	$('.pagedisplay').addClass('4');
	$('.next').addClass('4');
	$('.last').addClass('4');
	$('.pagesize').addClass('4');
	
	
	$(document).ready(function() {
			$('.error').hide();
			$('a.edit').click(function() {			
				var url = $(this).attr('href');
				$('.in-content').load(url);
				return false;
			});

			$('button.negative').click(function() {
				window.location.href= site_base_url+"regionsettings/region_settings/location"; // site_base_url is base url
				return false;
			});

			$('.positive').click(function() {
			$('.error').hide();
			var varRegions = $('#regionid').val();
			//alert(varRegions);
			if (varRegions == 0) {
				$('td#Varerr1.error').show();
				return false;
			}

			var varCountrys = $('#add1_country').val();
			if (varCountrys == "0") {
				$('td#err2.error').show();
				return false;
			}

			var varStates  = $('#stateid').val() ;
			if(varStates == "0"){
				$('td#erro3.error').show();
				return false;
			}

			var varLocations  = $('#location_name').val() ;
					if(varLocations == ""){
						$('td#err4.error').show();
						return false;
						}			
			});

			$('button.locsearch').click(function() {    
				var loc = $('#locationsearch').val();
				//alert(loc);
				var locurl = "regionsettings/location_search/0/"+ loc;
				//alert(locurl);
				//  $('.in-content').load(locurl);

				 $('#ui-tabs-9').load(locurl,function() {
				 $('#location_form').attr("action","./regionsettings/location");
				});
				return false;
			});

			$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']}) 
			$(".data-table").tablesorterPager({container: $("#pager4"),positionFixed: false});  
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

/////////////////