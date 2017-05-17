/*
*@Project View 
*@
*/

// $('#ad_filter').hide();
$('#excel').hide();
$('#ajax_loader').show();
var sturl = site_base_url+"project/advance_filter_search_pjt/?"+Math.random();
$('#ad_filter').load(sturl,function(){
	$('#ajax_loader').hide();
	$('#ad_filter').show();
	$('#excel').show();
});

$(function() {
	saveSearchDropDownScript();
	
	dtPjtTable();
	
	// $('#from_date, #to_date').datepicker({dateFormat: 'dd-mm-yy'});
	$('#from_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true, onSelect: function(date) {
		if($('#to_date').val!='')
		{
			$('#to_date').val('');
		}
		var return_date = $('#from_date').val();
		$('#to_date').datepicker("option", "minDate", return_date);
	}});
	$('#to_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true });
	
});
	
function dtPjtTable() {
	$('.data-tbl').dataTable({
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,
		"bDestroy": true
	});
}

//For Projects
var pjtstage 	= $("#pjt_stage").val(); 
// var pm_acc	= $("#pm_acc").val(); 
var customer	= $("#customer1").val(); 
var service	 	= $("#services").val();
var practice 	= $("#practices").val();
var keyword	 	= $("#keywordpjt").val(); 
var divisions	= $("#divisions").val();
 	
if(keyword == "Project Title, Name or Company")
keyword = 'null';

if (document.getElementById('advance_search_pjt'))
document.getElementById('advance_search_pjt').style.display = 'none';

function advanced_filter_pjt(){
	$('#advance_search_pjt').slideToggle('slow');
	var keyword = $("#keywordpjt").val();
	var status  = document.getElementById('advance_search_pjt').style.display;
	
	if(status == 'none') {
		var pjtstage 	= $("#pjt_stage").val(); 
		// var pm_acc	= $("#pm_acc").val();
		var customer	= $("#customer1").val(); 
		var service	 	= $("#services").val();
		var practice 	= $("#practices").val();	
		var divisions 	= $("#divisions").val();					
	} else {
		$("#pjt_stage").val("");
		// $("#pm_acc").val("");
		$("#customer1").val("");
		$("#services").val("");
		$("#practices").val("");
		$("#divisions").val("");
	}
}

$('#pjt_search_form').submit(function() {
	var pjtstage 		= $("#pjt_stage").val(); 
	// var pm_acc 	 	= $("#pm_acc").val(); 
	var customer 		= $("#customer1").val(); 
	var service  		= $("#services").val();
	var practice 		= $('#practices').val();
	var keyword  		= $("#keywordpjt").val();
	var datefilter  	= $("#datefilter").val();
	var from_date   	= $("#from_date").val();
	var to_date  		= $("#to_date").val();
	var divisions  		= $("#divisions").val();
	var customer_type 	= $("#customer_type").val();
	if(keyword == "Project Title, Name or Company") {
		keyword = '';
	}
	
	var params = {'pjtstage':pjtstage,'customer':customer,'service':service,'practice':practice,'divisions':divisions,'customer_type':customer_type,'keyword':encodeURIComponent(keyword),'datefilter':datefilter,'from_date':from_date,'to_date':to_date};
	params[csrf_token_name] = csrf_hash_token; 
	if($(this).attr("id") == 'advanceFilters_pjt'){
		$('#advance').hide();
		$('#load').show();
		$("#ad_filter" ).hide();
		$('#ajax_loader').show();
		$('#excel').hide();
	}
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'project/advance_filter_search_pjt/search',
		data: params,
		success: function(data) {
			$("#ad_filter" ).html(data);
			$('#load').hide();
			$('#ajax_loader').hide();
			$("#ad_filter" ).show();
			$('#advance').show();
			$('#excel').show();
		}
	});
	return false;
});

$("#search_advance").click(function() {
	
	var pjtstage 		= $("#pjt_stage").val(); 
	// var pm_acc		= $("#pm_acc").val(); 
	var customer 		= $("#customer1").val(); 
	var service  		= $("#services").val();
	var practice 		= $('#practices').val();
	var keyword  		= $("#keywordpjt").val();
	var datefilter  	= $("#datefilter").val();
	var from_date   	= $("#from_date").val();
	var to_date  		= $("#to_date").val();
	var divisions  		= $("#divisions").val();
	var customer_type 	= $("#customer_type").val();
	if(keyword == "Project Title, Name or Company") {
		keyword = '';
	}
	
	var params = {'pjtstage':pjtstage,'customer':customer,'service':service,'practice':practice,'divisions':divisions,'customer_type':customer_type,'keyword':encodeURIComponent(keyword),'datefilter':datefilter,'from_date':from_date,'to_date':to_date};
	params[csrf_token_name] = csrf_hash_token; 
	if($(this).attr("id") == 'advanceFilters_pjt'){
		$('#search_advance').hide();
		$('#save_advance').hide();
		$('#load').show();
		$("#ad_filter" ).hide();
		$('#ajax_loader').show();
		$('#excel').hide();
	}
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'project/advance_filter_search_pjt/search',
		data: params,
		beforeSend:function(){
			$("#ad_filter" ).hide();
			$('#search_advance').hide();
			$('#save_advance').hide();
			$('#load').show();
			$('#ajax_loader').show();
			$('#excel').hide();
		},
		success: function(data) {
			$('#load').hide();
			$('#search_advance').show();
			$('#save_advance').show();
			$("#ad_filter" ).html(data);			
			$('#ajax_loader').hide();
			$("#ad_filter" ).show();
			$('#excel').show();
		}
	});
	return false;
});

$("#save_advance").click(function() {
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"project/get_search_name_form",
		cache: false,
		data: csrf_token_name+'='+csrf_hash_token,
		success: function(res){
			// alert(res.html)
			// return false;
			$('#popupGetSearchName').html(res);
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
	
	var search_name = $('#search_name').val();
	var is_default  = is_defalut_val;
	
	var pjtstage 		= $("#pjt_stage").val(); 
	var customer 		= $("#customer1").val(); 
	var service  		= $("#services").val();
	var practice 		= $('#practices').val();
	var keyword  		= $("#keywordpjt").val();
	var datefilter  	= $("#datefilter").val();
	var from_date   	= $("#from_date").val();
	var to_date  		= $("#to_date").val();
	var divisions  		= $("#divisions").val();
	var customer_type 	= $("#customer_type").val();
	if(keyword == "Project Title, Name or Company") {
		keyword = '';
	}
	
	var params = {'pjtstage':pjtstage,'customer':customer,'service':service,'practice':practice,'divisions':divisions,'customer_type':customer_type,'datefilter':datefilter,'from_date':from_date,'to_date':to_date};
	params[csrf_token_name] = csrf_hash_token; 
	
	//Save the search criteria
	
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"project/save_search/2",
		cache: false,
		data: "search_name="+search_name+"&is_default="+is_default+"&pjtstage="+pjtstage+"&customer="+customer+"&service="+service+"&divisions="+divisions+"&customer_type="+customer_type+"&datefilter="+datefilter+"&practice="+practice+"&from_date="+from_date+"&to_date="+to_date+'&'+csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			$('#popupGetSearchName').html('<div style="margin:10px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(response){
			if(response.res == true) {
				$('#no_record').remove();
				$('.search-root').append(response.search_div);
			}
			
			$.ajax({
				type: 'POST',
				url: site_base_url+'project/advance_filter_search_pjt/search',
				data: params,
				success: function(data) {
					$("#ad_filter" ).html(data);
					$('#load').hide();
					$('#ajax_loader').hide();
					$("#ad_filter" ).show();
					$('#advance').show();
					$('#excel').show();
					
					$.unblockUI();
					$('#search_advance').show();
					$('#save_advance').show();
					
				}
			});
			
		}
	});
	return false;  //stop the actual form post !important!
}

function show_search_results(search_id) {

	var pjtstage 		= $("#pjt_stage").val(); 
	var customer 		= $("#customer1").val(); 
	var service  		= $("#services").val();
	var practice 		= $('#practices').val();
	var keyword  		= $("#keywordpjt").val();
	var datefilter  	= $("#datefilter").val();
	var from_date   	= $("#from_date").val();
	var to_date  		= $("#to_date").val();
	var divisions  		= $("#divisions").val();
	var customer_type 	= $("#customer_type").val();
	if(keyword == "Project Title, Name or Company") {
		keyword = '';
	}
	
	var params = {'pjtstage':pjtstage,'customer':customer,'service':service,'practice':practice,'divisions':divisions,'customer_type':customer_type,'datefilter':datefilter,'from_date':from_date,'to_date':to_date};
	params[csrf_token_name] = csrf_hash_token; 

	$.ajax({
		type: "POST",
		url: site_base_url+"project/advance_filter_search_pjt/search/"+search_id,
		cache: false,
		data: params,
		beforeSend:function(){
			$("#ad_filter" ).hide();
			$('#ajax_loader').show();
		},
		success: function(data){
			$('#ajax_loader').hide();
			$("#ad_filter" ).html(data);
			$("#ad_filter" ).show();
			$('#load').hide();
			$("#val_export").val(search_id);
			$(".saved-search-criteria").slideUp();
		}
	});
}

$('.search-root').on('click', '.set_default_search', function() {

	var search_id = $( this ).val();
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"project/set_default_search/"+search_id+'/2',
		cache: false,
		data: "filter=filter&"+csrf_token_name+'='+csrf_hash_token,
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

function delete_save_search(search_id) {
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: site_base_url+"project/delete_save_search/"+search_id+'/2',
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

function deleteProject(id) {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete <br />this project?<br /><br />This will delete all the items<br />and logs attached to this Project.</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}
		
function processDelete(id) {
	window.location.href = site_base_url+'project/delete_quote/'+id;
}

function cancelDel() {
	$.unblockUI();
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