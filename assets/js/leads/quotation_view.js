/*
 *@Quotation View 
*/

	var owner = $("#owner").val(); 
	var leadassignee = $("#leadassignee").val();
	var regionname = $("#regionname").val();
	var countryname = $("#countryname").val();
	var statename = $("#statename").val();
	var locname = $("#locname").val();
	var stage = $("#stage").val(); 
	var customer = $("#customer").val(); 
	var worth = $("#worth").val();
	var lead_status = $("#lead_status").val();
	var keyword = $("#keyword").val(); 
	//alert(keyword);
	if(keyword == "Lead No, Job Title, Name or Company")
	keyword = 'null';
	if(viewlead==1) {	
		document.getElementById('advance_search').style.display = 'none';	
	} 
	var sturl = "welcome/advance_filter_search/";
	
	$('#advance_search_results').load(sturl);

//For Advance Filters functionality.
$("#advanceFilters").submit(function() {
	$('#advance').hide();
	$('#load').show();
	var owner        = $("#owner").val();
	var leadassignee = $("#leadassignee").val();
	var regionname   = $("#regionname").val();
	var countryname  = $("#countryname").val();
	var statename    = $("#statename").val();
	var locname      = $("#locname").val();
	var stage        = $("#stage").val(); 
	var customer     = $("#customer").val(); 
	var worth        = $("#worth").val();	
	var lead_status  = $("#lead_status").val();
	var keyword      = $("#keyword").val();
		
	 $.ajax({
	   type: "POST",
	   url: site_base_url+"welcome/advance_filter_search",
	   data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&lead_status="+lead_status+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
	   success: function(data){
			$('#advance_search_results').html(data);
			$('#advance').show();
			$('#load').hide();	
	   }
	 });
	return false;  //stop the actual form post !important!
});

//for lead search functionality.
 $(function(){
       $("#lead_search_form").submit(function(){
		var  keyword = $("#keyword").val(); 
		if(keyword == "Lead No, Job Title, Name or Company")
		keyword = 'null';
		var owner = $("#owner").val();
		var leadassignee = $("#leadassignee").val();
		var regionname = $("#regionname").val();
		var countryname = $("#countryname").val();
		var statename = $("#statename").val();
		var locname = $("#locname").val();
		var stage = $("#stage").val(); 
		var customer = $("#customer").val(); 
		var worth = $("#worth").val();
 
         $.ajax({
           type: "POST",
           url: site_base_url+"welcome/advance_filter_search",
           data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
           success: function(data){
			   $('#advance_search_results').html(data);
           }
         });
         return false;  //stop the actual form post !important!
 
      });
   });

function advanced_filter(){
	$('#advance_search').slideToggle('slow');
	var  keyword = $("#keyword").val();
	var status = document.getElementById('advance_search').style.display;
	
	if(status == 'none') {
		var owner = $("#owner").val();
		var leadassignee = $("#leadassignee").val();
		var regionname = $("#regionname").val();
		var countryname = $("#countryname").val();
		var statename = $("#statename").val();
		var locname = $("#locname").val();
		var stage = $("#stage").val(); 
		var customer = $("#customer").val(); 
		var worth = $("#worth").val();
	} else {
		$("#owner").val("");
		$("#leadassignee").val("");
		$("#regionname").val("");
		$("#countryname").val("");
		$("#statename").val("");
		$("#locname").val("");
		$("#stage").val("");
		$("#customer").val("");
		$("#worth").val("");
	}
}

//For Countries
$('#regionname').change(function() {
	$('#statename').html('');
	$('#locname').html('');
	loadCountry();
});

function loadCountry() {
	var region_id 			= $("#regionname").val();
	var params 				= {'region_id':region_id};
	params[csrf_token_name] = csrf_hash_token;
	$.post( 
		'welcome/loadCountrys/',
		params,
		function(data) {										
			if (data.error) {
				alert(data.errormsg);
			} else {
				$("select#countryname").html(data);
			}
		}
	);
}

//For States
$('#countryname').change(function() {
	$('#locname').html('');
	loadState();
});

function loadState() {
	var coun_id 			= $("#countryname").val();
	var params 				= {'coun_id':coun_id};
	params[csrf_token_name] = csrf_hash_token;
	if(coun_id != '') {
		$.post( 
			'welcome/loadStates/',
			params,
			function(data) {										
				if (data.error) {
					alert(data.errormsg);
				} else {
					$("select#statename").html(data);
				}
			}
		);
	}
}

//For Locations
$('#statename').change(function() {
		loadLocations();
});

function loadLocations() {
	var st_id  				= $("#statename").val();
	var params 				= {'st_id':st_id};
	params[csrf_token_name] = csrf_hash_token;
	if(st_id != '') {
		$.post( 
			'welcome/loadLocns/',
			params,
			function(data) {										
				if (data.error) {
					alert(data.errormsg);
				} else {
					$("select#locname").html(data);
				}
			}
		);
	}
}