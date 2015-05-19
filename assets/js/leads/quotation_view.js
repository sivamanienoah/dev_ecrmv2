/*
 *@Quotation View 
*/

	var owner = $("#owner").val(); 
	var leadassignee = $("#leadassignee").val();
	var regionname 	 = $("#regionname").val();
	var countryname  = $("#countryname").val();
	var statename	 = $("#statename").val();
	var locname		 = $("#locname").val();
	var stage		 = $("#stage").val(); 
	var customer	 = $("#customer").val(); 
	var worth		 = $("#worth").val();
	var lead_status	 = $("#lead_status").val();
	var lead_indi	 = $("#lead_indi").val();
	var keyword		 = $("#keyword").val(); 
	//alert(keyword);
	if(keyword == "Lead No, Job Title, Name or Company")
		keyword = 'null';
	if(viewlead==1) {	
		document.getElementById('advance_search').style.display = 'none';	
	}

	//for ie ajax loading issue appending random number
	var sturl = site_base_url+"welcome/advance_filter_search/?"+Math.random();
	$('#advance_search_results').load(sturl);

//For Advance Filters functionality.
$("#search_advance").click(function() {

	$('#search_advance').hide();
	$('#save_advance').hide();
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
	var lead_indi    = $("#lead_indi").val();
	var keyword      = '';

	$.ajax({
		type: "POST",
		url: site_base_url+"welcome/advance_filter_search/search",
		cache: false,
		data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&lead_status="+lead_status+"&lead_indi="+lead_indi+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
		success: function(data){
			$('#advance_search_results').html(data);
			$('#load').hide();
			$('#search_advance').show();
			$('#save_advance').show();
			$("#search_type").val("search");
		}
	});
	return false;  //stop the actual form post !important!
});


function show_search_results(search_id) {
	$.ajax({
		type: "POST",
		url: site_base_url+"welcome/advance_filter_search/search/"+search_id,
		cache: false,
		data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&lead_status="+lead_status+"&lead_indi="+lead_indi+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
		success: function(data){
			$('#advance_search_results').html(data);
			$('#search_advance').show();
			$('#save_advance').show();
			$('#load').hide();	
			$("#search_type").val("");
		}
	});
}

function delete_save_search(search_id) {
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"welcome/delete_save_search/"+search_id+'/1',
		cache: false,
		data: csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			$('.search-root').block({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
		},
		success: function(response){
			if(response.resu=='deleted') {
				$('#item_'+search_id).remove();
				if($(".search-root li").length == 1) {
					$('.search-root').append('<li id="no_record" style="text-align: center; margin: 5px;">No Save & Search Found</li>');
				}
			} else {
				alert('Not updated');
			}
			$('.search-root').unblock();
		}
	});
}

$("#save_advance").click(function() {
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"welcome/get_search_name_form",
		cache: false,
		data: csrf_token_name+'='+csrf_hash_token,
		success: function(res){
			// alert(res.html)
			// return false;
			$('#popupGetSearchName').html(res);
			$("#search_type").val("");
			$.blockUI({
				message:$('#popupGetSearchName'),
				css:{border: '2px solid #999', color:'#333',padding:'6px',top:'280px',left:($(window).width() - 265) /2+'px',width: '246px', position: 'absolute'}
				// focusInput: false 
			});
			$( "#popupGetSearchName" ).parent().addClass( "no-scroll" );
		}
	});
});

function save_cancel() {
	$.unblockUI();
}

function save_search() {

	if($('#search_name').val()=='') {
		$("#search_name").css("border-color", "red");
		return false;
	}
	
	$("#search_name").keyup(function(){
		$("#search_name").css("border-color", "");
	});
	
	$('#search_advance').hide();
	$('#save_advance').hide();
	$('#load').show();
	
	var is_defalut_val = 0;
	
	if($( "#is_default:checked" ).val() == 1) {
		is_defalut_val = 1;
	}
	
	var search_name  = $('#search_name').val();
	var is_default   = is_defalut_val;
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
	var lead_indi    = $("#lead_indi").val();
	var keyword      = '';
	
	//Save the search criteria
	
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"welcome/save_search/1",
		cache: false,
		data: "search_name="+search_name+"&is_default="+is_default+"&stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&lead_status="+lead_status+"&lead_indi="+lead_indi+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			$('#popupGetSearchName').html('<div style="margin:10px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(response){
			
			if(response.res == true) {
				$('#no_record').remove();
				$('.search-root').append(response.search_div);
			}
			
			$.ajax({
				type: "POST",
				url: site_base_url+"welcome/advance_filter_search/search",
				cache: false,
				data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&lead_status="+lead_status+"&lead_indi="+lead_indi+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
				success: function(data){
					$.unblockUI();
					$('#advance_search_results').html(data);
					$('#search_advance').show();
					$('#save_advance').show();
					$('#load').hide();	
				}
			});
		}
	});
	return false;  //stop the actual form post !important!
}

//for lead search functionality.
$(function(){
       $("#lead_search_form").submit(function(){
			var  keyword 		= $("#keyword").val(); 
			if(keyword == "Lead No, Job Title, Name or Company")
			keyword 			= 'null';
			var stage 			= $("#stage").val(); 
			var customer 		= $("#customer").val();
			var owner 			= $("#owner").val();
			var leadassignee 	= $("#leadassignee").val();
			var regionname 		= $("#regionname").val();
			var countryname 	= $("#countryname").val();
			var statename 		= $("#statename").val();
			var locname 		= $("#locname").val();
			var lead_indi 		= $("#lead_indi").val();
			var worth 			= $("#worth").val();
	 
			 $.ajax({
			   type: "POST",
			   url: site_base_url+"welcome/advance_filter_search",
			   data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&lead_indi="+lead_indi+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
			   success: function(data){
				   $('#advance_search_results').html(data);
			   }
			 });
			 return false;  //stop the actual form post !important!
		});
	  
		saveSearchDropDownScript();
		
		/*
		*Save & Search script
		**/
		// $( ".set_default_search" ).on( "click", function() {
		$('.search-root').on('click', '.set_default_search', function() {
			var search_id = $( this ).val();
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: site_base_url+"welcome/set_default_search/"+search_id+'/1',
				cache: false,
				data: csrf_token_name+'='+csrf_hash_token,
				beforeSend:function(){
					$('.search-root').block({
						message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif />',
						css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
					});
				},
				success: function(response){
					if(response.resu=='updated') {
						show_search_results(search_id);
					} else {
						alert('Not updated');
					}
					$('.search-root').unblock();
				}
			});
		});

   });
   
function advanced_filter(){
	$('#advance_search').slideToggle('slow');
	var  keyword = $("#keyword").val();
	var status = document.getElementById('advance_search').style.display;
	
	if(status == 'none') {
		var owner 			= $("#owner").val();
		var leadassignee 	= $("#leadassignee").val();
		var regionname 		= $("#regionname").val();
		var countryname 	= $("#countryname").val();
		var statename 		= $("#statename").val();
		var locname 		= $("#locname").val();
		var stage 			= $("#stage").val(); 
		var customer 		= $("#customer").val(); 
		var worth 			= $("#worth").val();
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

function saveSearchDropDownScript(){
	/*for saved search - start*/
	  	$(".saved-search-head").click(function(){
			var X=$(this).attr('id');

			if(X==1) {
				$(".saved-search-criteria").hide();
				$(this).attr('id', '0');
			} else {
				$(".saved-search-criteria").show();
				$(this).attr('id', '1');
			}
		});

		//Mouseup textarea false
		$(".saved-search-criteria").mouseup(function() {
			return false
		});
		$(".saved-search-head").mouseup(function() {
			return false
		});

		//Textarea without editing.
		$(document).mouseup(function() {
			$(".saved-search-criteria").hide();
			$(".saved-search-head").attr('id', '');
		});
	  
	/*for saved search - end*/
}