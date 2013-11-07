/*
 *@Report Lead Assignee
*/

$(function(){

	$('.advanced_filter').click(function(){
		$('#advance_search').slideToggle('slow');
	});	

	$('#task_search_start_date').datepicker({dateFormat: 'dd-mm-yy',  onSelect: function(selected) {
		 		var date2 = $('#task_search_start_date').datepicker('getDate');
        		$("#task_search_end_date").datepicker("option","minDate", date2);
     		 }
	});

	$('#task_search_end_date').datepicker({dateFormat: 'dd-mm-yy',  onSelect: function(selected) {
			var date1 = $('#task_search_end_date').datepicker('getDate');							
			$("#task_search_start_date").datepicker("option","maxDate", date1);
		 }
	});
	
	$('#report_lead_frm').submit(function(e){
		e.preventDefault();		
		$('#advance').hide();
		$('#load').show();		
		var base_url    = site_base_url;  //"site_base_url is global variable"
		var start_date  = $('#task_search_start_date').val();
		var end_date    = $('#task_search_end_date').val();
		var stage       = $('#stage').val();		
		stage = stage + "";			
		var customer    = $('#customer').val();
		customer        = customer + "";
		var worth       = $('#worth').val();
		worth = worth+"";
		var owner = $('#owner').val();
		owner = owner+"";
		var leadassignee = $('#leadassignee').val();				
		leadassignee = leadassignee+"";
		
		var regionname = $('#regionname').val();	
		regionname = regionname+"";	
		var countryname = $('#countryname').val();
		countryname = countryname+"";
		var statename = $('#statename').val();
		statename = statename+"";
		var locname = $('#locname').val();
		locname = locname+"";
		
		var params = {start_date:start_date,end_date:end_date,stage:stage,customer:customer,worth:worth,owner:owner,leadassignee:leadassignee,regionname:regionname,countryname:countryname,statename:statename,locname:locname};
		params[csrf_token_name] = csrf_hash_token;		
		$('#report_grid').load(base_url+'report/report_lead_assignee/get_lead_report',params,function(){
			$('#advance').show();
			$('#load').hide();	
		});
		
	});

	//For Countries
	$('#regionname').change(function() {
		$('#statename').html('');
		$('#locname').html('');
		loadCountry();
	});

	
	function loadCountry() {
		var region_id = $("#regionname").val(); 
		var	params                  = {};
	    params[csrf_token_name]     = csrf_hash_token;
		$.post( 
			'welcome/loadCountrys/'+ region_id,
			params,
			function(data) {										
				if (data.error) 
				{
					alert(data.errormsg);
				} 
				else 
				{
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
		var coun_id = $("#countryname").val();
		var	params                  = {};
	    params[csrf_token_name]     = csrf_hash_token;
		$.post( 
			'welcome/loadStates/'+ coun_id,
			params,
			function(data) {										
				if (data.error) 
				{
					alert(data.errormsg);
				} 
				else 
				{
					$("select#statename").html(data);
				}
			}
		);
	}

	//For Locations
	$('#statename').change(function() {
			loadLocations();
	});

	function loadLocations() {
		var st_id = $("#statename").val();
		var	params                  = {};
	    params[csrf_token_name]     = csrf_hash_token;
		$.post( 
			'welcome/loadLocns/'+ st_id,
			params,
			function(data) {										
				if (data.error) 
				{
					alert(data.errormsg);
				} 
				else 
				{
					$("select#locname").html(data);
				}
			}
		);
	}

		
});

	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////