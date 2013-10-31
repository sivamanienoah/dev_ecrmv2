/*
 *@Choice View Jquery
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
	var owner = $("#owner").val();
	var leadassignee = $("#leadassignee").val();
	var regionname = $("#regionname").val();
	var countryname = $("#countryname").val();
	var statename = $("#statename").val();
	var locname = $("#locname").val();
	var stage = $("#stage").val(); 
	var customer = $("#customer").val(); 
	var worth = $("#worth").val();	
	var  keyword = $("#keyword").val();
		
	 $.ajax({
	   type: "POST",
	   url: site_base_url+"welcome/advance_filter_search", // site_base_url is global variable 
	   data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&keyword="+keyword+'&'+csrf_token_name+'='+csrf_hash_token,
	   success: function(data){
		   $('#advance_search_results').html(data);
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
		
	}
	else {
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
	var region_id = $("#regionname").val(); 
	var params 				= {};			
	params[csrf_token_name] = csrf_hash_token; 
	$.post( 
		'choice/loadCountrys/'+ region_id,
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
	var params    		     = {};			
	params[csrf_token_name]  = csrf_hash_token; 
	$.post( 
		'choice/loadStates/'+ coun_id,
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
	var params    		     = {};			
	params[csrf_token_name]  = csrf_hash_token; 
	$.post( 
		'choice/loadLocns/'+ st_id,
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


//For Projects
	var pjtstage = $("#pjt_stage").val(); 
	var pm_acc = $("#pm_acc").val(); 
	var cust = $("#customer1").val(); 
	var keyword = $("#keywordpjt").val(); 
	//alert(keyword);
	if(keyword == "Project No, Project Title, Name or Company")
	keyword = 'null';
	<?php if($this->session->userdata('viewPjt')==1) { 	?>
	document.getElementById('advance_search_pjt').style.display = 'none';	
	<?php } ?>
	var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
	//alert(sturl);	
	$('#advance_search_results_pjts').load(sturl);
	
function advanced_filter_pjt(){
	$('#advance_search_pjt').slideToggle('slow');
	var  keyword = $("#keywordpjt").val();
	var status = document.getElementById('advance_search_pjt').style.display;
	
	if(status == 'none') {
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val(); 
	}
	else   {
			
		$("#pjt_stage").val("");
		$("#pm_acc").val("");
		$("#customer1").val("");

	}
}


$('#advanceFilters_pjt').submit(function() {	
	var pjtstage = $("#pjt_stage").val(); 
	var pm_acc = $("#pm_acc").val(); 
	var cust = $("#customer1").val(); 
	var  keyword = $("#keywordpjt").val(); 
	if(keyword == "Project No, Project Title, Name or Company")
	keyword = 'null';
	document.getElementById('advance_search_results_pjts').style.display = 'block';	
	var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
	//alert(sturl);
	$('#advance_search_results_pjts').load(sturl);	
	return false;
});

$('#pjt_search_form').submit(function() {	
		var  keyword = $("#keywordpjt").val(); 
		if(keyword == "Project No, Project Title, Name or Company")
		keyword = 'null';
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val();  
		//document.getElementById('ad_filter').style.display = 'block';
		var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
		$('#advance_search_results_pjts').load(sturl);
		return false;
});

/*mychanges*/
$(function(){
	$('.all-tasks').load('tasks/index/extend #task-page .task-contents', {}, loadEditTables);
	$('#set-job-task .pick-date, #search-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M'});
	
	$('#task_search_user').val('<?php echo $userdata['userid']; ?>');
	/* job tasks character limit */
	$('#job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		var remain_len = 1000 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		$('#task-desc-countdown').text(remain_len);
	});
	$('#edit-job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		
		var remain_len = 1000 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		
		$('#edit-task-desc-countdown').text(remain_len);
	});
});
function searchTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	var data = $('#search-job-task').serialize();
	$.post('tasks/search',data+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function searchtodayTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	$.post('tasks/search','task_search_user='+$('#task_search_user').val()+'&task_search_start_date='+$('#hided').val()+'&task_search_end_date='+$('#hided').val()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function loadEditTables(){
	$('#jv-tab-4').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	var taskids = [];
	$('td.task.random-task').each(function(){
		taskids.push($(this).attr('rel'));
		
			$(this).append('<div class="buttons" style="display:none"> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit |</button> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'complete\'); return false;">Approve |</button> \
								<button type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'delete\'); return false;">Delete</button> \
							</div>');	
		
	});
	$('td.task.newrandom-task').each(function(){
		taskids.push($(this).attr('rel'));
			$(this).append('<div class="buttons" style="display:none"> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit</button> \
							</div>');
	});
	if (taskids.length < 1)	{
		$('#jv-tab-4').unblock();
		return;
	}
	$.post('ajax/request/get_random_tasks',{'id_set': taskids.join(','),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},function(data){
		if (data != '')	{
			$('form.random-task-tables').html(data);
		} 
		$('#jv-tab-4').unblock();
	});
}
/*ends*/

/////////////////