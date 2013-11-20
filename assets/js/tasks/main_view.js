/*
 *@Main View
 *@Tasks Controller
 *@task variable : task_userid,uio,created_by
*/


$(function(){
	$('.all-tasks').load('tasks/index/extend #task-page .task-contents', {}, loadEditTables);
	$('#set-job-task .pick-date, #search-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '0', maxDate: '+6M'});
	
	$('#task_search_user').val(task_userid);
	/* job tasks character limit */
	$('#job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		if (desc_len.length > 240) {
			$(this).focus().val(desc_len.substring(0, 240));
		}
		var remain_len = 240 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		$('#task-desc-countdown').text(remain_len);
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
	$('td.random-task').each(function(){	
			taskids.push($(this).attr('rel'));
			 if(deletetask == 1) { 
									 $(this).append('<div class="buttons"> \
										<button type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit</button> \
										<button type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'complete\'); return false;">Approve</button> \
										<button type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'delete\'); return false;">Delete</button> \
									</div>');	
									
			 } else if(in_array(uio, created_by)) {
					
									  $(this).append('<div class="buttons"> \
										<button type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit</button> \
										<button type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'complete\'); return false;">Approve</button> \
										<button type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'delete\'); return false;">Delete</button> \
									</div>');
			 } else {
									  $(this).append('<div class="buttons"> \
										<button type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit</button> \
										<button type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'complete\'); return false;">Approve</button> \
									</div>');
			 } 
	});
	
	$('td.newrandom-task').each(function(){
		taskids.push($(this).attr('rel'));
			$(this).append('<div class="buttons"> \
								<button type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit</button> \
							</div>');
	});
	
	if (taskids.length < 1)	{
		$('#jv-tab-4').unblock();
		return;
	}
	
	var params = {'id_set': taskids.join(',')};
	params[csrf_token_name] = csrf_hash_token;
	
	$.post('ajax/request/get_random_tasks',params,function(data){
		if (data != '')	{
			$('form.random-task-tables').html(data);
		}
		$('#jv-tab-4').unblock();
	});
}

	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////