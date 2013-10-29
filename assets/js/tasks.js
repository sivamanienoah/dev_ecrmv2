function addNewTask(random,ci_csrf_token,csrf_hasf)
{   //alert(random); return false;
	var errors = [],
	job_task = $('#job-task-desc').val(),
	task_user = $('#set-job-task select[name="task_user"]').val(),
	remarks = $('#task-remarks').val(),	
	user_label = $('#set-job-task select[name="task_user"] option:selected').text(),
	task_hours = $('#set-job-task input[name="task_hours"]').val(),
	task_mins = $('#set-job-task select[name="task_mins"]').val(),
	task_start_date = $('#set-job-task input[name="task_start_date"]').val(),
	task_end_date = $('#set-job-task input[name="task_end_date"]').val(),
	task_end_hour = $('#set-job-task select[name="task_end_hour"]').val(),
	actualstart_date = $('#actualstart-date').val(),
	require_qc = ($('#set-job-task input[name="require_qc"]').is(':checked')) ? 'YES' : 'NO';
	priority = ($('#set-job-task input[name="priority"]').is(':checked')) ? 'YES' : 'NO';
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
		'ajax/request/add_job_task' + random_task_url,
		{'jobid': curr_job_id, 'job_task': job_task, 'task_user': task_user, 'user_label':  user_label, 'task_hours': task_hours, 'task_mins': task_mins, 'task_start_date': task_start_date, 'task_end_date': task_end_date, 'task_end_hour': task_end_hour, 'require_qc': require_qc, 'priority': priority, 'remarks': remarks,'ci_csrf_token': csrf_hasf},
		function (data)
		{
			if (data.error)
			{
				alert(data.errormsg);
			}
			else
			{
				$('.existing-task-list').find('.task-notice').remove().end().append(data.html);
				$('.toggler').slideToggle();
				
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
	//alert(taskid);

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
			css: {background:'#fff', border: '2px solid #999', padding:'8px', color:'#333', width: '500px', marginLeft: '-250px', left: '50%'}
        });
	
	var the_task_el = $('#task-table-' + taskid);
	var the_task_el1 = taskid;	
	var edit_table_el = $('.blockUI .task-edit');
	var createdbyid = $.trim($('.task-cid', the_task_el).text());	
	var uid=$.trim($('.task-uid', the_task_el).text());	
	// workout the existing values and replace them
	
	$('.edit-task-remarks', edit_table_el).val($.trim($('.taskremarks', the_task_el).text()));
	
	//$('.edit-task-allocate', edit_table_el).val($('.user-name', the_task_el).attr('rel'));
	if (createdbyid == uid)
	{
		//alert(createdbyid + "==" + uid);
		$('.edit-task-allocate', edit_table_el).val($('.user-name', the_task_el).attr('rel'));
		$('.edit-task-allocate', edit_table_el).removeAttr('disabled', ($.trim($('.user-name', the_task_el).attr('rel'))) );
		$('.edit-job-task-desc', edit_table_el).val($.trim($('.task', the_task_el).text()));	
	    $('.edit-job-task-desc', edit_table_el).removeAttr('readonly', ($.trim($('.task', the_task_el).text())) );
		$('.edit-start-date', edit_table_el).val($.trim($('.start-date', the_task_el).text()));
		$('.edit-start-date', edit_table_el).removeAttr('disabled', ($.trim($('.start-date', the_task_el).text())) );
		$('.edit-end-date', edit_table_el).val($.trim($('.end-date span.date_part', the_task_el).text()));
		$('.edit-end-date', edit_table_el).removeAttr('disabled', ($.trim($('.end-date', the_task_el).text())) );
		$('.edit-actualend-date', edit_table_el).val($.trim($('.actualend-date', the_task_el).text()));
	    $('.edit-actualend-date', edit_table_el).removeAttr('disabled', ($.trim($('.actualend-date', the_task_el).text())) )
	}
	else
	{
		$('.edit-task-allocate', edit_table_el).val($('.user-name', the_task_el).attr('rel'));
		$('.edit-task-allocate', edit_table_el).attr('disabled', ($.trim($('.user-name', the_task_el).attr('rel'))) );
		$('.edit-job-task-desc', edit_table_el).val($.trim($('.task', the_task_el).text()));	
	    $('.edit-job-task-desc', edit_table_el).attr('readonly', ($.trim($('.task', the_task_el).text())) );
		$('.edit-start-date', edit_table_el).val($.trim($('.start-date', the_task_el).text()));
		$('.edit-start-date', edit_table_el).attr('disabled', ($.trim($('.start-date', the_task_el).text())) );
		$('.edit-end-date', edit_table_el).val($.trim($('.end-date span.date_part', the_task_el).text()));
		$('.edit-end-date', edit_table_el).attr('disabled', ($.trim($('.end-date', the_task_el).text())) );
		$('.edit-actualend-date', edit_table_el).val($.trim($('.actualend-date', the_task_el).text()));
		$('.edit-actualend-date', edit_table_el).attr('disabled', ($.trim($('.actualend-date', the_task_el).text())) )
	}	
	
	$('.edit-actualstart-date', edit_table_el).val($.trim($('.actualstart-date', the_task_el).text()));	;
	$('.edit-task-owner', edit_table_el).val($.trim($('.task-owner', the_task_el).text()));
	$('.task-require-qc', edit_table_el).attr('checked', ($.trim($('.task-require-qc', the_task_el).text()) == '0') ? false : true);
	$('.priority', edit_table_el).attr('checked', ($.trim($('.priority', the_task_el).text()) == '0') ? false : true);	
	//$('#edit-job-task-id', edit_table_el).val($.trim($('.task-id', the_task_el).text()));
	
	//if (window.console) console.log($.trim($('item.start-date', the_task_el).text()));
	// get the hours and mins
	return false;
}

function editTask()
{
	if (task_being_edited == 0) return;
	
	var edit_table_el = $('.blockUI .task-edit');
	
	var errors = [],
		job_task = $('.edit-job-task-desc', edit_table_el).val(),
		task_owner = $('.edit-task-owner', edit_table_el).val(),
		remarks = $('.edit-task-remarks', edit_table_el).val(),
		task_user = $('.edit-task-allocate', edit_table_el).val(),
		user_label = $('.edit-task-allocate option:selected', edit_table_el).text(),
		task_hours = $('.edit-task-hours', edit_table_el).val(),
		task_mins = $('.edit-task-mins', edit_table_el).val(),
		task_start_date = $('.edit-start-date', edit_table_el).val(),
		task_end_date = $('.edit-end-date', edit_table_el).val(),
		actualstart_date = $('.edit-actualstart-date', edit_table_el).val(),
		task_end_hour = $('.edit-end-hour', edit_table_el).val(),
		require_qc = ($('.task-require-qc', edit_table_el).is(':checked')) ? 'YES' : 'NO';;
		priority = ($('.priority', edit_table_el).is(':checked')) ? 'YES' : 'NO';;
		
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

	var param =	{'jobid': curr_job_id, 'job_task': job_task, 'task_user': task_user, 'user_label':  user_label, 'task_hours': task_hours, 'task_mins': task_mins, 'task_start_date': task_start_date, 'task_end_date': task_end_date, 'task_end_hour': task_end_hour, 'require_qc': require_qc, 'priority': priority, 'remarks': remarks, 'actualstart_date': actualstart_date, 'task_owner': task_owner };
	param[csrf_token_name] = csrf_hash_token;
	$.post(
		'ajax/request/add_job_task/' + task_being_edited + random_task_url,
		param,
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
		'ajax/request/get_job_tasks/' + curr_job_id,
		{},
		function(data)
		{
			$('.existing-task-list').append(data);
			$('#jv-tab-4').unblock();
		}
	);
	
	tasks_loaded = true;
}

var _task_require_qc = false;

function setTaskStatus(taskid, el)
{	

	var params = {};

	//dynamically bring the hostname and project folder name.
	var hstname = window.location.host;
	var pathname = window.location.pathname;
	var pth=pathname.split("/");
	
	$('#jv-tab-4').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	
	/* just set as complete */
	if (typeof el == 'string' && el == 'complete')
	{
		params = {'taskid': taskid, 'set_as_complete': true};
		params[csrf_token_name] = csrf_hash_token;
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
			params = {'taskid': taskid, 'delete_task': true};
			params[csrf_token_name] = csrf_hash_token;
		}
	}
	else /* set the status */
	{
		var notifyPM = 'NO', task_status_val = $('#set_task_status_' + taskid).val();
		
		if (task_status_val == 100 && ! _task_require_qc && $('tr.require-qc-' + taskid).size() > 0)
		{
			alert('To mark this task as 100%,\nplease click through to the job\nas it requires quality verification!');
			$('#jv-tab-4').unblock();
			return false;
		}
		else if (task_status_val == 100 && ! _task_require_qc && $.trim($('.task-require-qc', $('#task-table-' + taskid)).text()) == '1')
		{
			$('#task-require-qc-cover input[name="hidden_taskid"]').val(taskid);
			
			$.blockUI({
				message:$('#task-require-qc-cover table'),
				css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333', width: '550px', marginLeft: '-275px', left: '50%'}
			});
			
			return false;
		}
		if (task_status_val == 100)
		{
			notifyPM = 'YES';
		}
		//alert(task_status_val);
		params = {'taskid': taskid, 'task_status': task_status_val, 'notify_pm': notifyPM};
		params[csrf_token_name] = csrf_hash_token;
	}
	$.post(
			'ajax/request/set_task_status',
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
					else if (data.marked_100pct && $('#job_log').size() > 0 && $.trim($('.task-require-qc', $('#task-table-' + taskid)).text()) == '1')
					{
						// all good! Place a log
						var msg = "The following task has been marked 100% and have been verified to comply with the Visiontech standard.\n\n.";
						
						msg += $('#task-table-' + taskid).find('tr:first td:eq(1)').text().replace(/^(\s|\t)+/, '') + '\n\n';
						
						$('table.the-task-require-qc td:has("span")').each(function(){
							msg += '** ' + $('span', $(this)).text() + ' - YES\n'
						});
						
						// get adrian@ (6)
						$('#job_log').val(msg);
						$('.user .production-manager-user').attr('checked', true);
						addLog();
						
						$('#task-table-' + taskid).addClass('marked_100pct');
					}
					else if (data.task_delete)
					{
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
					else {
					
						$('#set_task_status_' + taskid).parents('.tasks').removeClass('complete')
					
					
					}
					if (typeof(this_is_home) != 'undefined')
					{
						window.location.href = window.location.href;
					}
				}
				
				// reset the global task qc check status
				_task_require_qc = false;				
				
				$('#jv-tab-4').unblock();
			},
			'json'
		);
}
function submitFullCompleteStatus()
{
	var all_good = true;
	$('table.the-task-require-qc td input:checkbox').each(function(){
		if ( ! $(this).is(':checked'))
		{
			all_good = false;
		}
	});
	
	if ( ! all_good)
	{
		alert('You need to verify that the item is fully fuctional\nbefore marking it 100% !');
		return false;
	}
	else
	{
		_task_require_qc = true; // update the global task qc check status
		
		// unblock
		$.unblockUI(); $('#jv-tab-4').unblock();
		
		var submit_taskid = $('table.the-task-require-qc input[name="hidden_taskid"]').val();
		setTaskStatus(submit_taskid);
	}
	
	return false;
}
