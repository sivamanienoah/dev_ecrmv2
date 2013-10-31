/*
 *@Dashboard Jquery
*/

 if(viewlead==1) { 

 $(document).ready(function(){
 alert(dashboard_s1); 
	 if (dashboard_s1!='') { 
	 
			alert(dashboard_s1);
	 
			var testtest = ['Initial(6)',6],['Prospect(1)',1],['Demo Scheduled(1)',1],['Proposal Sent to client(3)',3],['Proposal Accepted(2)',2],['SOW under Review(1)',1],['SOW Sent to Client(1)',1],['SOW Approved(1)',1],['Project Charter Approved(1)',1];
	 
			 plot1 = $.jqplot('funnel1', [[dashboard_s1]], {
			//plot1 = $.jqplot('funnel1', [[testtest]], {
			//title: 'Leads - Current Pipeline',
			legend: {
			   show: true,
			   rendererOptions: {
				   border: false,
				   fontSize: '10pt',
				   location: 'e'
			   }
			},
			seriesDefaults: {
				shadow: false,
				renderer: $.jqplot.FunnelRenderer
			},
			grid: {
					drawGridLines: true,        // wether to draw lines across the grid or not.
					gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
					background: '#ffffff',      // CSS color spec for background color of grid.
					drawBorder: false,
					shadow: false
			},
			seriesColors: ["#027997", "#910000", "#bfdde5", "#8bbc21", "#1aadce", "#492970", "#2f7ed8", "#0d233a", "#48596a", "#640cb1", "#eaa228", "#422460"]
			});
			$('#funnel1').bind('jqplotDataClick',function (ev, seriesIndex, pointIndex, data) {
				var formdata              = { 'data':data, 'type':'funnel'}
				formdata[csrf_token_name] = csrf_hash_token; 
				$.ajax({
					type: "POST",
					url: site_base_url+'dashboard/showLeadsDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info').empty();
						$('#charts_info').show();
						$('#charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
					},
					success: function(html){
						//alert(html.html);
						$('#charts_info').empty();
						$("#charts_info").show();
						if (html.html == 'NULL') {
							$('#charts_info').html('');
						} else {
							$('#charts_info').show();
							$('#charts_info').html(html.html);
							
							$('#example_funnel').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6]; 
										//var str = TotalMarks.split(" "); //for USD 1200.00
										//cost += parseFloat(str[1]);//for USD 1200.00
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									//nCells[1].innerHTML = "USD " + cost.toFixed(2); //for USD 1200.00
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			});
			
			$( "#funnelimg" ).click(function() {
				var imgelem = $('#funnel1').jqplotToImageElem();
				var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
				var imgdata = imageSrc;
				var base_url = site_base_url;		
			
				var url = base_url+"dashboard/savePdf/";
				var form = $('<form action="' + url + '" method="post">' +
				  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
				  '</form>');
				$('body').append(form);
				$(form).submit();
			});
		
		} else { 
			$('#funnel1').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
	    } 
	});



function getLeadDashboardTable(userid, user_name) {
	var baseurl = site_base_url;
	$.ajax({
	url : baseurl + 'dashboard/getLeadDependency/'+ userid + "/" + user_name,
		success : function(response){
			if(response != '') {
				$("#lead-dependency-list").show();
				$("#lead-dependency-list").html(response);
				$('#lead-dependency-table').dataTable( {
					"aaSorting": [[ 0, "desc" ]],
					"iDisplayLength": 5,
					"sPaginationType": "full_numbers",
					"bInfo": true,
					"bPaginate": true,
					"bProcessing": true,
					"bServerSide": false,
					"bLengthChange": false,
					"bSort": true,
					"bAutoWidth": false,
					"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
						//var TotalMarks = 0;
						var cost = 0
						for ( var i=0 ; i<aaData.length ; i++ )
						{
							var TotalMarks = aaData[i][6];
							cost += parseFloat(TotalMarks);
						}
						//$('#lead-dependency-table').append('<p>'+cost+'</p>');
						var nCells = nRow.getElementsByTagName('td');
						nCells[1].innerHTML = cost.toFixed(2);
						
					}
				});
				$('html, body').animate({ scrollTop: $("#lead-dependency-list").offset().top }, 1000);
			} 			
		}
	});
}


function getLeadAssigneeTable(userid,user_name) {
	var baseurl = site_base_url;
	$.ajax({
	url : baseurl + 'dashboard/getLeadAssigneeDependency/'+ userid+'/'+user_name,
		beforeSend:function(){
			$('#lead-dependency-list').empty();
			$("#lead-dependency-list").show();
			$('#lead-dependency-list').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success : function(response){
			if(response != '') {
				$('#lead-dependency-list').empty();
				$("#lead-dependency-list").show();
				$("#lead-dependency-list").html(response);
				$('#lead-assignee-table').dataTable( {
					"aaSorting": [[ 0, "desc" ]],
					"iDisplayLength": 5,
					"sPaginationType": "full_numbers",
					"bInfo": true,
					"bPaginate": true,
					"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
						//var TotalMarks = 0;
						var cost = 0
						for ( var i=0 ; i<aaData.length ; i++ )
						{
							var TotalMarks = aaData[i][6]; 
							cost += parseFloat(TotalMarks);
							
						}
						var nCells = nRow.getElementsByTagName('td');
						nCells[1].innerHTML = cost.toFixed(2);
						
					},
					"bProcessing": true,
					"bServerSide": false,
					"bLengthChange": false,
					"bSort": true,
					"bAutoWidth": false
				});
				$('html, body').animate({ scrollTop: $("#lead-dependency-list").offset().top }, 1000);
			} 			
		}
	});
}
function getCurrentLeadActivity(jobid,lead_name)  {
	var baseurl = site_base_url;
	$.ajax({
	url : baseurl + 'dashboard/getLeadsCurrentActivity/'+ jobid+'/'+lead_name,
		success : function(response){
			if(response != '') {
				$("#leads-current-activity-list").show();
				$("#leads-current-activity-list").html(response);
				$('#leads-current-activity-table').dataTable( {
					"bInfo": false,
					"bPaginate": false,
					"bSort": false,
					"bFilter": false,
					"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
						//var TotalMarks = 0;
						var cost = 0
						for ( var i=0 ; i<aaData.length ; i++ )
						{
							var TotalMarks = aaData[i][6]; 
							//var str = TotalMarks.split(" ");
							//cost += parseFloat(str[1]);
							cost += parseFloat(TotalMarks);
							
						}
						var nCells = nRow.getElementsByTagName('td');
						//nCells[1].innerHTML = "USD " + cost.toFixed(2);
						nCells[1].innerHTML = cost.toFixed(2);
					}
				});
				$('html, body').animate({ scrollTop: $("#leads-current-activity-list").offset().top }, 1000);
			} 			
		}
	});
}


$(document).ready(function(){
	$('.table_grid').dataTable({
		"iDisplayLength": 5,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": false,
		"bSort": true,
		"bAutoWidth": false
	});	
});
$('#leads-current-activity-list').delegate('.grid-close','click',function(){
	var $lead = $("#leads-current-activity-list");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});
$('#lead-dependency-list').delegate('.grid-close','click',function(){
	var $lead = $("#lead-dependency-list");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});
$('#charts_info').delegate('.grid-close','click',function(){
	var $lead = $("#charts_info");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});
$('#charts_info2').delegate('.grid-close','click',function(){
	var $lead = $("#charts_info2");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});
$('#charts_info3').delegate('.grid-close','click',function(){
	var $lead = $("#charts_info3");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});

$('#current-lead-report').change(function() {
	var statusVar = 'statusVar='+$(this).val()+','+'&'+csrf_token_name+'='+csrf_hash_token;
	var baseurl = site_base_url;
	$.ajax({
	type: 'GET',
	url : baseurl + 'dashboard/get_leads_current_weekly_monthly_report/',
	data: statusVar,
		success : function(response){
			if(response != '') {
				$("#weekly-monthly").html(response);
				$('#weekly-monthly-table').dataTable({
					"iDisplayLength": 5,
					"sPaginationType": "full_numbers",
					"bInfo": true,
					"bPaginate": true,
					"bProcessing": true,
					"bServerSide": false,
					"bLengthChange": false,
					"bSort": true,
					"bAutoWidth": false
				});
			} 			
		}
	});
	
});
/* dashboard excel report starts here */
/* Lead Owner report */
$('#lead-dependency-list').delegate('#lead-ownner-export','click',function(){
        var user_id = $('#lead-dependency-table').attr('name'); 
		var user_name = $('#lead-owner-username').val(); 
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+user_id+'/'+user_name+'/leadowner';
		document.location.href = sturl;
		return false;
});
/*lead assignee report */
$('#lead-dependency-list').delegate('#lead-assignee-export','click',function(){
        var user_id = $('#lead-assignee-table').attr('name'); 
		var user_name = $('#lead-assignee-username').val();  
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+user_id+'/'+user_name+'/assignee';
		document.location.href = sturl;
		return false;
});

/*current pipeline report */
$('#charts_info').delegate('#current-pipeline-export','click',function(){
	    var lead_stage_name = $("#current-pipeline-export").attr('name'); //alert(lead_stage_name);
		var type = $("#lead-type-name").val();  
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+"pipeline/"+lead_stage_name+"/"+type;
		document.location.href = sturl;
		return false;
});
/*lead by region report*/
$('#charts_info').delegate('#leads-by-region-export','click',function(){
	    var lead_region_name = $("#leads-by-region-export").attr('name'); 
		var type = $("#lead-by-region").val(); // alert(lead_stage_name + " " + type);
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+"leadsregion/"+lead_region_name+"/"+type;
		document.location.href = sturl;
		return false;
});
/*lead current activity report */
$('#leads-current-activity-list').delegate('#lead-current-activity-export','click',function(){
		var lead_no = $("#lead-no").val(); 
		var lead_name = $("#lead-no").attr('name');
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+lead_name+"/"+lead_no+"/currentactivity";
		document.location.href = sturl;
		return false;
});

/*lead aging report */
$('#charts_info2').delegate('#lead-aging-report','click',function(){
	    var lead_aging = $("#lead-aging-report").attr('name');
		var type = $("#lead-aging-type").val();   
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+lead_aging+"/"+type+"/leadsaging";
		document.location.href = sturl;
		return false;
});
$('#charts_info2').delegate('#closed-oppor-report','click',function(){
	    var gra_id = $("#closed-oppor-report").attr('name');
		var type = $("#cls-oppr-type").val();   
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+gra_id+"/"+type+"/closedopp";
		document.location.href = sturl;
		return false;
});
$('#leads-current-activity-list').delegate('#least-active-report','click',function() {
	    var lead_indi = $("#least-active-report").attr('name');
		var type = $("#least-active-type").val(); 
		//alert(type); return false;
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+lead_indi+"/"+type+"/leastactive";
		document.location.href = sturl;
		return false;
});
//for pie2 & pie3 charts export
$('#charts_info3').delegate('#leads-by-leadsource-export','click',function(){
	    var arg1 = $("#leads-by-leadsource-export").attr('name'); 
		var arg2 = $("#lead-by-leadsource").val();   
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+"pipeline/"+arg1+"/"+arg2;
		document.location.href = sturl;
		return false;
});
$('#charts_info3').delegate('#leads-by-service-req-export','click',function(){
	    var arg1 = $("#leads-by-service-req-export").attr('name');
		var arg2 = $("#lead-by-service-req").val();   
		var baseurl = site_base_url;
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+"pipeline/"+arg1+"/"+arg2;
		document.location.href = sturl;
		return false;
});
/* dashboard excel report ends here */



 } 

if(viewlead==1) { 

		//For Projects
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val(); 
		var keyword = $("#keywordpjt").val(); 
		//alert(keyword);
		if(keyword == "Project No, Project Title, Name or Company")
			keyword = 'null';

		if (document.getElementById('advance_search_pjt'))
			document.getElementById('advance_search_pjt').style.display = 'none';	

		var sturl = "project/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
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
			var sturl = "project/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
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
				var sturl = "project/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
				$('#advance_search_results_pjts').load(sturl);
				return false;
		});
 } 


//For Tasks
/*mychanges*/
$(function(){
	
	var params    		     = {};	
	params[csrf_token_name]      = csrf_hash_token; 

	$('.all-tasks').load('tasks/index/extend #task-page .task-contents',params, loadEditTables);
	$('#set-job-task .pick-date, #search-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M'});
	
	$('#task_search_user').val(dashboard_userid);
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
	$.post('tasks/search',$('#search-job-task').serialize()+'&'+csrf_token_name+'='+csrf_hash_token,function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function searchtodayTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	$.post('tasks/search','task_search_user='+$('#task_search_user').val()+'&task_search_start_date='+$('#hided').val()+'&task_search_end_date='+$('#hided').val()+'&'+csrf_token_name+'='+csrf_hash_token,function(data) {
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
	
	var params = {'id_set': taskids.join(',')};
	params[csrf_token_name]      = csrf_hash_token; 
	
	$.post('ajax/request/get_random_tasks',params,function(data){

		if (data != '')	{
			$('form.random-task-tables').html(data);
		} 

		$('#jv-tab-4').unblock();
	});
}

/////////////////