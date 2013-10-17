function addTaskToggle(opt)
{
	if (opt == 'off')
	{
		$('#set-job-task .task-add:visible').hide('fast');
		$('#set-job-task .task-init:hidden').show('fast');
	}
	else
	{
		$('#set-job-task .task-add:hidden').show('fast');
		$('#set-job-task .task-init:visible').hide('fast');
	}
}

function addNewTask(random,ci_csrf_token,csrf_hasf)
{
	var errors = [],
	job_task = $('#job-task-desc').val(),
	task_user = $('#set-job-task select[name="task_user"]').val(),
	user_label = $('#set-job-task select[name="task_user"] option:selected').text(),
	task_hours = $('#set-job-task input[name="task_hours"]').val(),
	task_mins = $('#set-job-task select[name="task_mins"]').val(),
	task_start_date = $('#set-job-task input[name="task_start_date"]').val(),
	task_end_date = $('#set-job-task input[name="task_end_date"]').val();
		
	if (job_task == '')
	{
		errors.push('Task is required!');
	}
	
	if ( ! /^[0-9]+$/.test(task_hours) && task_mins < 15)
	{
		errors.push('Valid hours required!');
	}
	
	if ( ! /^[0-9]+$/.test(task_user))
	{
		errors.push('Task user required!');
	}
	
	if ( ! /^[0-9]{2}-[0-9]{2}-[0-9]{4}$/.test(task_start_date))
	{
		errors.push('Valid start date is required!');
	}
	
	if ( ! /^[0-9]{2}-[0-9]{2}-[0-9]{4}$/.test(task_end_date))
	{
		errors.push('Valid end date is required!');
	}
	
	if (errors.length > 0)
	{
		alert(errors.join('\n'));
		return false;
	}
	
	$('#jv-tab-4').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	
	var random_task_url = '';
	if (random == 'random')
	{
		random_task_url = '/NO/YES';
	}
	
	$.post(
		'ajax/request/add_lead_task' + random_task_url,
		{'leadid': lead_id, 'job_task': job_task, 'task_user': task_user, 'user_label':  user_label, 'task_hours': task_hours, 'task_mins': task_mins, 'task_start_date': task_start_date, 'task_end_date': task_end_date,'ci_csrf_token':csrf_hasf},
		function (data)
		{
			if (data.error)
			{
				alert(data.errormsg);
			}
			else
			{
				$('.existing-task-list').find('.task-notice').remove().end().append(data.html);
				addTaskToggle('off');
				
				if (random_task_url != '')
				{
					window.location.href = window.location.href;
				}
				
				$('#set-job-task')[0].reset();
			}
			$('#jv-tab-4').unblock();
		},
		'json'
	);
}

var task_being_edited = 0;
var random_task_edit;

function openEditTask(taskid, random)
{
	task_being_edited = taskid;
	
	if (random == 'random')
	{
		random_task_edit = true;
	}
	else
	{
		random_task_edit = false;
	}
	
	$.blockUI({
            message:$('#edit-job-task table'),
			css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333', width: '500px', marginLeft: '-250px', left: '50%'}
        });
	
	var the_task_el = $('#task-table-' + taskid);
	var edit_table_el = $('.blockUI .task-edit');
	
	// workout the existing values and replace them
	$('.edit-job-task-desc', edit_table_el).val($.trim($('.task', the_task_el).text()));
	$('.edit-task-allocate', edit_table_el).val($('.user-name', the_task_el).attr('rel'));
	$('.edit-start-date', edit_table_el).val($.trim($('.start-date', the_task_el).text()));
	$('.edit-end-date', edit_table_el).val($.trim($('.end-date', the_task_el).text()));
	
	//if (window.console) console.log($.trim($('item.start-date', the_task_el).text()));
	
	// get the hours and mins
	var hours_mins = $('.hours-mins', the_task_el).attr('rel').split(':');
	
	$('.edit-task-hours', edit_table_el).val(hours_mins[0]);
	$('.edit-task-mins', edit_table_el).val(hours_mins[1]);
	
	return false;
}

function editTask()
{
	if (task_being_edited == 0) return;
	
	var edit_table_el = $('.blockUI .task-edit');
	
	var errors = [],
		job_task = $('.edit-job-task-desc', edit_table_el).val(),
		task_user = $('.edit-task-allocate', edit_table_el).val(),
		user_label = $('.edit-task-allocate option:selected', edit_table_el).text(),
		task_hours = $('.edit-task-hours', edit_table_el).val(),
		task_mins = $('.edit-task-mins', edit_table_el).val(),
		task_start_date = $('.edit-start-date', edit_table_el).val(),
		task_end_date = $('.edit-end-date', edit_table_el).val();
		
	if (job_task == '')
	{
		errors.push('Task is required!');
	}
	
	if ( ! /^[0-9]+$/.test(task_hours) && task_mins < 15)
	{
		errors.push('Valid hours required!');
	}
	
	if ( ! /^[0-9]+$/.test(task_user))
	{
		errors.push('Task user required!');
	}
	
	if ( ! /^[0-9]{2}-[0-9]{2}-[0-9]{4}$/.test(task_start_date))
	{
		errors.push('Valid start date is required!');
	}
	
	if ( ! /^[0-9]{2}-[0-9]{2}-[0-9]{4}$/.test(task_end_date))
	{
		errors.push('Valid end date is required!');
	}
	
	if (errors.length > 0)
	{
		alert(errors.join('\n'));
		return false;
	}
	
	$('.blockUI .task-add.task-edit').block({
            message:'<h3>Processing</h3>',
			css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
        });
	
	var random_task_url = '';
	if (random_task_edit)
	{
		random_task_url = '/YES';
	}
	
	$.post(
		'ajax/request/add_lead_task/' + task_being_edited + random_task_url,
		{'leadid': lead_id, 'job_task': job_task, 'task_user': task_user, 'user_label':  user_label, 'task_hours': task_hours, 'task_mins': task_mins, 'task_start_date': task_start_date, 'task_end_date': task_end_date},
		function (data)
		{
			if (data.error)
			{
				alert(data.errormsg);
			}
			else
			{
				$('#task-table-' + task_being_edited).replaceWith(data.html);
				if (random_task_url != '')
				{
					window.location.href = window.location.href;
				}
			}
			$('.blockUI .task-add.task-edit').unblock();
			$.unblockUI();
			$('#edit-job-task')[0].reset();
		},
		'json'
	);
	
	return false;
}

var tasks_loaded = false;

function loadExistingTasks()
{
	if (tasks_loaded)
	{
		return false;
	}
	
	$('#jv-tab-4').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	
	$.get(
		'ajax/request/get_lead_tasks/' + lead_id,
		{},
		function(data)
		{
			$('.existing-task-list').append(data);
			$('#jv-tab-4').unblock();
		}
	);
	
	tasks_loaded = true;
}

function setTaskStatus(taskid, el)
{	
	//dynamically bring the hostname and project folder name.
	var hstname = window.location.host;
	var pathname = window.location.pathname;
	var pth=pathname.split("/");
	//alert(pth[1]);
	$('#jv-tab-4').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	
	/* just set as complete */
	if (typeof el == 'string' && el == 'complete')
	{
		var params = {'taskid': taskid, 'set_as_complete': true};
	}
	else if (typeof el == 'string' && el == 'delete') /* delete task */
	{
		if ( ! window.confirm('Are you sure you want to delete this task?'))
		{
			$('#jv-tab-4').unblock();
			return false;
		}
		else
		{
			var params = {'taskid': taskid, 'delete_task': true};
		}
	}
	else /* set the status */
	{
		var notifyPM = 'NO', task_status_val = $('#set_task_status_' + taskid).val();
		
		if (task_status_val == 100)
		{
			notifyPM = 'YES';
		}
		var params = {'taskid': taskid, 'task_status': task_status_val, 'notify_pm': notifyPM};
	}
	
	$.post(
			'ajax/request/set_task_status/lead',
			params,
			function (data)
			{
				if (data.error)
				{
					alert(data.errormsg);
				}
				else
				{
					if (data.set_complete)
					{
						$('#task-table-' + taskid).addClass('completed');
					}
					else if (data.marked_100pct)
					{
						$('#task-table-' + taskid).addClass('marked_100pct');
					}
					else if (data.task_delete)
					{
						//if (window.location.href == 'http://192.168.1.31/vcslocaldev/tasks/all/')
						//if (window.location.href == 'http://localhost/ecrmv2/tasks/all/')
						if (window.location.href == 'http://' + hstname + '/' + pth[1] + '/tasks/all/')
						{
							window.location.href = window.location.href;
						}
						else
						{
							$('#task-table-' + taskid).hide('fast', function(){
								$(this).remove();
							});
						}
					}
					else if (typeof(this_is_home) != 'undefined')
					{
						window.location.href = window.location.href;
					}
				}
				
				$('#jv-tab-4').unblock();
			},
			'json'
		);
}
