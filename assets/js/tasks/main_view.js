/*
 *@Main View
 *@Tasks Controller
 *@task variable : task_userid,uio,created_by
*/

$(function(){

	loadajaxwithurl('tasks/index/extend');
	$('#set-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '0', maxDate: '+6M'});
	$('#search-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy'});
	
	$('#task_search_user').val(task_userid);
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
	var params    		     = $('#search-job-task').serialize();	
	params[csrf_token_name]  = csrf_hash_token;
    $(".all-tasks").load("tasks/search",params, function(responseTxt, statusTxt, xhr){
    if(statusTxt == "success")
	{
		
	}
   else if(statusTxt == "error")
   {
	 alert("Error: " + xhr.status + ": " + xhr.statusText);
   }    

    }); 
}
function searchtodayTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	$.post('tasks/search','task_search_user='+$('#task_search_user').val()+'&task_search_start_date='+$('#hided').val()+'&task_search_end_date='+$('#hided').val(),function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function check()
{
 $.getScript( "assets/js/tasks/task_list.js", function( data, textStatus, jqxhr ) {
  console.log( data ); // Data returned
  console.log( textStatus ); // Success
  console.log( jqxhr.status ); // 200
  console.log( "Load was performed." );
});
$.getScript( "assets/js/tasks/task_list.js" )
  .done(function( script, textStatus ) {
    console.log( textStatus );
  })
  .fail(function( jqxhr, settings, exception ) {
    $( "div.log" ).text( "Triggered ajaxError handler." );
});
}
function loadEditTables(){
	$('#jv-tab-4').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
		
	var taskids = [];
	$('td.random-task').each(function(){	
		taskids.push($(this).attr('rel'));
			$(this).append('<div class="buttons" style="display:none"> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit | </button> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'complete\'); return false;">Approve | </button> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'delete\'); return false;">Delete</button> \
							</div>');	
		
	});
	$('td.newrandom-task').each(function(){
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
	params[csrf_token_name] = csrf_hash_token;
	
	$.post('ajax/request/get_random_tasks', params, function(data){
		//alert("tes");
		if (data != '')	{
			$('form.random-task-tables').html(data);
		}
		$('#jv-tab-4').unblock();
		//on click by from floating div
		if(!isNaN(get_id) && (get_type=='random')) {
			openEditTask(get_id, get_type);
		}
	});
}
	
function loadajaxwithurl(url)
{
		var params    		     = {};	
	params[csrf_token_name]  = csrf_hash_token;
	
	//$('.all-tasks').load('tasks/index/extend #task-page .task-contents', params, check());
	
 	    $(".all-tasks").load(url,params, function(responseTxt, statusTxt, xhr){
        if(statusTxt == "success")
          // alert("External content loaded successfully!");
        if(statusTxt == "error")
            alert("Error: " + xhr.status + ": " + xhr.statusText);
    }); 
}

function resetpage()
{
	$('#search-job-task')[0].reset();
	$('.clr-czn').val('').trigger('liszt:updated');
	loadajaxwithurl('tasks/index/extend');
}
function isPaymentVal(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 45 && charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
	return false;
	else
	return true;
}	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////