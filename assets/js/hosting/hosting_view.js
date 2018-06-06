/*
 *@Hosting View Jquery
 */

function delHosting(id) {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}
	
function processDelete(id) {
	window.location.href = 'hosting/delete_account/'+id;
}

function cancelDel() {
	$.unblockUI();
}

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
	
	$('#sub_frm').submit(function(e){
		//alert('hi');return false;
		e.preventDefault();		
		$('#advance').hide();
		$('#load').show();		
		var base_url   = site_base_url; // site_base_url is global variable 
		var start_date = $('#from_date').val();
		var end_date   = $('#to_date').val();
		var stage = $('#stage').val();		
		stage = stage + "";		
		var sub_name = $('#sub_name').val();		
		sub_name = sub_name + "";			
		var customer = $('#customer').val();
		customer = customer + "";
		var worth = $('#worth').val();
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
		
		var params = {start_date:start_date,end_date:end_date,sub_name:sub_name,customer:customer,worth:worth,owner:owner,leadassignee:leadassignee,regionname:regionname,countryname:countryname,statename:statename,locname:locname};
		params[csrf_token_name] = csrf_hash_token; 
		$('#hostme').load(base_url+'hosting/get_subscription_report',params,function(){
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
		var region_id 			= $("#regionname").val();
		var params 				= {'region_id':region_id};
		params[csrf_token_name] = csrf_hash_token; 
		$.post( 
			'welcome/loadCountrys/',
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
		var coun_id 			= $("#countryname").val();
		var params 				= {'coun_id':coun_id};
		params[csrf_token_name] = csrf_hash_token; 
		$.post( 
			'welcome/loadStates/',
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
		var st_id 				= $("#statename").val();
	    var params 				= {'st_id':st_id};	
	    params[csrf_token_name] = csrf_hash_token; 
		$.post( 
			'welcome/loadLocns/',
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


//For Advance Filters functionality.
//$("#search_advance").click(function() {
////alert('hi');return false;
//	$('#search_advance').hide();
//	$('#save_advance').hide();
//	$('#load').show();
//	
//	var from_date = $("#from_date").val();
//	var to_date   = $("#to_date").val();
//	var sub_name        = $("#sub_name").val();
//	var leadassignee = $("#leadassignee").val();
//	var regionname   = $("#regionname").val();
//	var countryname  = $("#countryname").val();
//	var statename    = $("#statename").val();
//	var locname      = $("#locname").val();
//	var stage        = $("#stage").val(); 
//	var customer     = $("#customer").val();
//	var service      = $("#service").val();
//	var lead_src     = $("#lead_src").val();
//	var industry     = $("#industry").val();
//	var worth        = $("#worth").val();	
//	var lead_status  = $("#lead_status").val();
//	var lead_indi    = $("#lead_indi").val();
//	var keyword      = '';
//
//	$.ajax({
//		type: "POST",
//		url: site_base_url+"hosting/advance_filter_search/search",
//		cache: false,
//		data: "&sub_name="+sub_name+"&from_date="+from_date+"&to_date="+to_date+"&customer="+customer+"&service="+service+"&lead_src="+lead_src+"&industry="+industry+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&lead_status="+lead_status+"&lead_indi="+lead_indi+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
//		success: function(data){
//			$('#advance_search_results').html(data);
//			$('#load').hide();
//			$('#search_advance').show();
//			$('#save_advance').show();
//			$("#search_type").val("search");
//		}
//	});
//	return false;  //stop the actual form post !important!
//});
//
//function advanced_filter(){
//	$('#advance_search').slideToggle('slow');
//	var  keyword = $("#keyword").val("");
//	var status = document.getElementById('advance_search').style.display;
//	
//	if(status == 'none') {
//		var owner 			= $("#owner").val();
//		var leadassignee 	= $("#leadassignee").val();
//		var regionname 		= $("#regionname").val();
//		var countryname 	= $("#countryname").val();
//		var statename 		= $("#statename").val();
//		var locname 		= $("#locname").val();
//		var stage 			= $("#stage").val(); 
//		var customer 		= $("#customer").val(); 
//		var worth 			= $("#worth").val();
//		var industry 		= $("#industry").val();
//	} else {
//		$("#owner").val("");
//		$("#leadassignee").val("");
//		$("#regionname").val("");
//		$("#countryname").val("");
//		$("#statename").val("");
//		$("#locname").val("");
//		$("#stage").val("");
//		$("#customer").val("");
//		$("#worth").val("");
//		$("#industry").val("");
//	}//
//}

//-----------------------------------X---------------------------------------X---------------