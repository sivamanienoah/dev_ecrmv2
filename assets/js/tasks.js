function addNewTask(random,ci_csrf_token,csrf_hasf)
{   //alert(random); return false;
	var errors = [],
	job_task = $('#job-task-desc').val(),
	task_user = $('#set-job-task select[name="task_user"]').val(),
	remarks = $('#task-remarks').val(),	
	user_label = $('#set-job-task select[name="task_user"] option:selected').text(),
	task_hours = $('#set-job-task input[name="task_hours"]').val(),
	task_estimated_hours = $('#set-job-task input[name="estimated_hours"]').val(),
	task_mins = $('#set-job-task select[name="task_mins"]').val(),
	task_start_date = $('#set-job-task input[name="task_start_date"]').val(),
	task_end_date = $('#set-job-task input[name="task_end_date"]').val(),
	task_end_hour = $('#set-job-task select[name="task_end_hour"]').val(),
	actualstart_date = $('#actualstart-date').val(),
	taskCategory = $("#taskCategory").val(),
	taskpriority = $("#taskpriority").val(),
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
	if ( ! /^[0-9]+$/.test(taskpriority))
	{
		errors.push('Task Priority required!');
	}
	if ( ! /^[0-9]+$/.test(taskCategory))
	{
		errors.push('Task Category required!');
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
	// $('#jv-tab-4').blockUI({
	// $(this).blockUI({
	$('#jv-tab-4').block({
		message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
		css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
	}).delay(1);
	
	var random_task_url = '';
	if (random == 'random')
	{
		random_task_url = '/NO/YES';
	}
	
	$.post(
		'ajax/request/add_job_task' + random_task_url,
		{'lead_id': curr_job_id, 'job_task': job_task, 'task_user': task_user, 'user_label':  user_label, 'task_hours': task_hours, 'task_mins': task_mins, 'task_start_date': task_start_date, 'task_end_date': task_end_date, 'task_end_hour': task_end_hour, 'require_qc': require_qc, 'priority': priority, 'remarks': remarks, 'task_category': taskCategory, 'task_priority': taskpriority, 'estimated_hours': task_estimated_hours,'ci_csrf_token': csrf_hasf},
		function (data)
		{
			if (data.error)
			{
				alert(data.errormsg);
			}
			else
			{
				//$('.existing-task-list').find('.task-notice').remove().end().append(data.html);
				//$('.toggler').slideToggle();
				if ( $( "#search-job-task" ).length  ||  $('#dashboard').val()==1 ) 
				{
					resetpage();
				}
				else
				{
					tabselector();
				}
				if (random_task_url != '')
				{
					window.location.href = window.location.href;
				}
				$('#set-job-task')[0].reset();
			}
			$("#taskToAlloc").val('').trigger("liszt:updated");
			$("#taskCategory").val('').trigger("liszt:updated");
			$("#taskpriority").val('').trigger("liszt:updated");
			$('#jv-tab-4').unblock();
			// $.unblockUI();
		},
		'json'
	);
}

var task_being_edited = 0;
var random_task_edit;

/* function openEditTask(taskid, random) 
{
if( $('#search-job-task').length  ||  $('#dashboard').val()==1)
{
    var add=1;
}
else
{
	var add=0;
}

 var tr = $("#"+taskid).closest('tr');
 var taskcategory = $("#"+taskid).closest('table').attr('rel');
 var taskdescvalue = tr[0].cells[sumint(0,add)].innerHTML;
 var Createduser = tr[0].cells[sumint(2,add)].innerHTML;
 var taskPriority = tr[0].cells[sumint(1,add)]['id'];
 var Allocateduser = tr[0].cells[sumint(3,add)]['id'];
 var taskplStartdate = tr[0].cells[sumint(4,add)].innerHTML;
 var taskplEnddate = tr[0].cells[sumint(5,add)].innerHTML;
 var taskacStartdate = tr[0].cells[sumint(6,add)].innerHTML;
 var taskacEnddate = tr[0].cells[sumint(7,add)].innerHTML;
 var taskremarksvalue = tr[0].cells[sumint(10,add)].innerHTML;
 var taskEstimatedHour = tr[0].cells[sumint(8,add)].innerHTML;

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
		css: {background:'#fff', border: '2px solid #999', padding:'8px', color:'#333', width: '500px', marginLeft: '-250px', left: '50%', position:'absolute'}
	});
	// alert($('#task-table-' + taskid).length);
	var the_task_el = $("#"+taskid).closest('table').attr('id');
	// console.log(the_task_el);
	var the_task_el1 = taskid;
	var edit_table_el = $('.task-edit');
	var createdbyid = $.trim($('.task-cid', the_task_el).text());
	
	var uid=$.trim($('.task-uid', the_task_el).text());	
	// workout the existing values and replace them

	$('.edit-task-remarks', edit_table_el).val($.trim(taskremarksvalue));

		selectoptionassign('.edit-task-category',taskcategory)
		selectoptionassign('.edit-task-allocate',Allocateduser)
		selectoptionassign('.edit-task-priority',taskPriority)
	
	if (createdbyid == uid)
	{
		selectvalueassign('.edit-task-allocate',Allocateduser,edit_table_el);
		selectvalueassign('.edit-task-category',taskcategory,edit_table_el);
		selectvalueassign('.edit-task-priority',taskPriority,edit_table_el);
		
		$('.edit-job-task-desc', edit_table_el).val($.trim(taskdescvalue));	
	    $('.edit-job-task-desc', edit_table_el).removeAttr('readonly', ($.trim(taskdescvalue)) );
		$('.edit-start-date', edit_table_el).val($.trim(taskplStartdate));
		$('.edit-start-date', edit_table_el).removeAttr('disabled', ($.trim(taskplStartdate)) );
		$('.edit-end-date', edit_table_el).val($.trim(taskplEnddate));
		$('.edit-end-date', edit_table_el).removeAttr('disabled', ($.trim(taskplEnddate)) );
		$('.edit-actualend-date', edit_table_el).val($.trim(taskacEnddate));
	    $('.edit-actualend-date', edit_table_el).removeAttr('disabled', ($.trim(taskacEnddate)) );
	}
	else
	{
		selectvalueassign('.edit-task-allocate',Allocateduser,edit_table_el);
		selectvalueassign('.edit-task-category',taskcategory,edit_table_el);
		selectvalueassign('.edit-task-priority',taskPriority,edit_table_el);
		
		$('.edit-job-task-desc', edit_table_el).val($.trim(taskdescvalue));	
	    $('.edit-job-task-desc', edit_table_el).attr('readonly', ($.trim(taskdescvalue)) );
		$('.edit-start-date', edit_table_el).val($.trim(taskplStartdate));
		$('.edit-start-date', edit_table_el).attr('disabled', ($.trim(taskplStartdate)) );
		$('.edit-end-date', edit_table_el).val($.trim(taskplEnddate));
		$('.edit-end-date', edit_table_el).attr('disabled', ($.trim(taskplEnddate)) );
		$('.edit-actualend-date', edit_table_el).val($.trim(taskacEnddate));
		$('.edit-actualend-date', edit_table_el).attr('disabled', ($.trim(taskacEnddate)) );
	}
	$('.edit-job-est-hr', edit_table_el).val($.trim(taskEstimatedHour));
	$('.edit-actualstart-date', edit_table_el).val($.trim(taskacStartdate));
	$('.edit-task-owner', edit_table_el).val($.trim(Createduser));
	$('.task-require-qc', edit_table_el).attr('checked', ($.trim($('.task-require-qc', the_task_el).text()) == '0') ? false : true);
	$('.priority', edit_table_el).attr('checked', ($.trim($('.priority', the_task_el).text()) == '0') ? false : true);
	return false;
} */
function openEditTask(taskid, random)
{
	if( $('#search-job-task').length  ||  $('#dashboard').val()==1) {
		var add=1; 
	} else {
		var add=0;
	}
	
	var tr 					= $("#"+taskid).closest('tr');
	var taskcategory 		= $("#"+taskid).closest('table').attr('rel');
	var taskdescvalue 		= tr[0].cells[sumint(0,add)].innerHTML;
	var Createduser 		= tr[0].cells[sumint(2,add)].innerHTML;
	var taskPriority 		= tr[0].cells[sumint(1,add)]['id'];
	var Allocateduser 		= tr[0].cells[sumint(3,add)]['id'];
	var taskplStartdate 	= tr[0].cells[sumint(4,add)].innerHTML;
	var taskplEnddate 		= tr[0].cells[sumint(5,add)].innerHTML;
	var taskacStartdate 	= tr[0].cells[sumint(6,add)].innerHTML;
	var taskacEnddate 		= tr[0].cells[sumint(7,add)].innerHTML;
	var taskremarksvalue 	= tr[0].cells[sumint(10,add)].innerHTML;
	var taskEstimatedHour 	= tr[0].cells[sumint(8,add)].innerHTML;
	var the_task_el 		= $("#"+taskid).closest('table').attr('id');
	var edit_table_el 		= $('.task-edit');
	var createdbyid   		= $.trim($('.task-cid', the_task_el).text());
	var uid			  		= $.trim($('.task-uid', the_task_el).text());
	
	task_being_edited = taskid;
	if (random == 'random') {
		random_task_edit = true;
	} else {
		random_task_edit = false;
	}

	var params			    = {'taskid':taskid, 'random':random,};
	params[csrf_token_name] = csrf_hash_token;

	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/get_task_edit_form',
		dataType: 'json',
		data: params,
		beforeSend: function() {

		},
		success: function(res) {
			// alert(res.task_priority); return false;
			/* $(".edit-task-category").val(res.task_category);
			$(".edit-task-category").trigger("liszt:updated");
			
			$(".edit-task-allocate").val(res.created_by);
			$(".edit-task-allocate").trigger("liszt:updated");
			
			$(".edit-task-priority").val(res.task_priority);
			$(".edit-task-priority").trigger("liszt:updated"); */
			
			$.blockUI({
				message:$('#edit-job-task table'),			
				css: {background:'#fff', border: '2px solid #999', padding:'8px', color:'#333', width: '500px', marginLeft: '-250px', left: '50%', position:'absolute'}
			});
		
			selectoptionassign('.edit-task-category',taskcategory)
			selectoptionassign('.edit-task-allocate',Allocateduser)
			selectoptionassign('.edit-task-priority',taskPriority)
			
			if (createdbyid == uid)
			{
				selectvalueassign('.edit-task-allocate',Allocateduser,edit_table_el);
				selectvalueassign('.edit-task-category',taskcategory,edit_table_el);
				selectvalueassign('.edit-task-priority', taskPriority,edit_table_el);
				
				$('.edit-job-task-desc', edit_table_el).val($.trim(taskdescvalue));	
				$('.edit-job-task-desc', edit_table_el).removeAttr('readonly', ($.trim(taskdescvalue)) );
				$('.edit-start-date', edit_table_el).val($.trim(taskplStartdate));
				$('.edit-start-date', edit_table_el).removeAttr('disabled', ($.trim(taskplStartdate)) );
				$('.edit-end-date', edit_table_el).val($.trim(taskplEnddate));
				$('.edit-end-date', edit_table_el).removeAttr('disabled', ($.trim(taskplEnddate)) );
				$('.edit-actualend-date', edit_table_el).val($.trim(taskacEnddate));
				$('.edit-actualend-date', edit_table_el).removeAttr('disabled', ($.trim(taskacEnddate)) );
			}
			else
			{
				selectvalueassign('.edit-task-allocate',Allocateduser,edit_table_el);
				selectvalueassign('.edit-task-category',taskcategory,edit_table_el);
				selectvalueassign('.edit-task-priority',taskPriority,edit_table_el);
				
				$('.edit-job-task-desc', edit_table_el).val($.trim(taskdescvalue));	
				$('.edit-job-task-desc', edit_table_el).attr('readonly', ($.trim(taskdescvalue)) );
				$('.edit-start-date', edit_table_el).val($.trim(taskplStartdate));
				$('.edit-start-date', edit_table_el).attr('disabled', ($.trim(taskplStartdate)) );
				$('.edit-end-date', edit_table_el).val($.trim(taskplEnddate));
				$('.edit-end-date', edit_table_el).attr('disabled', ($.trim(taskplEnddate)) );
				$('.edit-actualend-date', edit_table_el).val($.trim(taskacEnddate));
				$('.edit-actualend-date', edit_table_el).attr('disabled', ($.trim(taskacEnddate)) );
			}
			$('.edit-job-est-hr', edit_table_el).val($.trim(taskEstimatedHour));
			$('.edit-actualstart-date', edit_table_el).val($.trim(taskacStartdate));
			$('.edit-task-owner', edit_table_el).val($.trim(Createduser));
			$('.task-require-qc', edit_table_el).attr('checked', ($.trim($('.task-require-qc', the_task_el).text()) == '0') ? false : true);
			$('.priority', edit_table_el).attr('checked', ($.trim($('.priority', the_task_el).text()) == '0') ? false : true);
			$('.edit-task-remarks', edit_table_el).val($.trim(res.remarks));
			$('#edit_complete_status', edit_table_el).val($.trim(res.status));
			selectvalueassign('#taskstages',res.task_stage,edit_table_el);
		}
	});
}
function editTask()
{
	if (task_being_edited == 0) return;
	
	var edit_table_el = $('.blockUI .task-edit');
	var follow_up = 0;
	var errors = [],
		job_task = $('.edit-job-task-desc', edit_table_el).val(),
		task_owner = $('.edit-task-owner', edit_table_el).val(),
		remarks = $('.edit-task-remarks', edit_table_el).val(),
		task_user = $('.edit-task-allocate', edit_table_el).val(),
		user_label = $('.edit-task-allocate option:selected', edit_table_el).text(),
		taskCategory = $('.edit-task-category', edit_table_el).val(),	
		category_label = $('.edit-task-category option:selected', edit_table_el).text(),
		taskpriority = $('.edit-task-priority', edit_table_el).val(),
		priority_label = $('.edit-task-priority option:selected', edit_table_el).text(),		
		task_hours = $('.edit-task-hours', edit_table_el).val(),
		task_mins = $('.edit-task-mins', edit_table_el).val(),
		task_start_date = $('.edit-start-date', edit_table_el).val(),
		task_end_date = $('.edit-end-date', edit_table_el).val(),
		actualstart_date = $('.edit-actualstart-date', edit_table_el).val(),
		task_end_hour = $('.edit-end-hour', edit_table_el).val(),
		task_estimated_hour = $('.edit-job-est-hr', edit_table_el).val(),
		task_stages = $('#taskstages', edit_table_el).val(),
		task_complete_status = $('#edit_complete_status', edit_table_el).val(),
		require_qc = ($('.task-require-qc', edit_table_el).is(':checked')) ? 'YES' : 'NO';
		priority = ($('.priority', edit_table_el).is(':checked')) ? 'YES' : 'NO';
		follow_up = ($('#follow_up_task', edit_table_el).is(':checked')) ? 1 : 0;
		
	if (job_task == '')
	{
		errors.push('Task is required!');
	}
	
	if (task_stages == 14 && task_complete_status != 100)
	{
		errors.push('Task Completion Percentage Must be 100 %!');
	}
	
	if ( ! /^[0-9]+$/.test(task_hours) && task_mins < 15)
	{
		errors.push('Valid hours required!');
	}
	
	if ( ! /^[0-9]+$/.test(task_user))
	{
		errors.push('Task user required!');
	}
	if ( ! /^[0-9]+$/.test(taskpriority))
	{
		errors.push('Task Priority required!');
	}
	if ( ! /^[0-9]+$/.test(taskCategory))
	{
		errors.push('Task Category required!');
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

	var param =	{'lead_id': curr_job_id, 'job_task': job_task, 'task_user': task_user, 'user_label':  user_label, 'task_hours': task_hours, 'task_mins': task_mins, 'task_start_date': task_start_date, 'task_end_date': task_end_date, 'task_end_hour': task_end_hour, 'require_qc': require_qc, 'priority': priority, 'remarks': remarks, 'actualstart_date': actualstart_date, 'task_owner': task_owner,'task_category': taskCategory, 'task_priority': taskpriority, 'estimated_hours': task_estimated_hour , 'task_stage': task_stages , 'follow_up': follow_up };
	param[csrf_token_name] = csrf_hash_token;
	$.post(
		'ajax/request/add_job_task/' + task_being_edited + random_task_url,
		param,
		function (data)
		{
			// console.info(data); return;
			if (data.error)
			{
				alert(data.errormsg);
			} else if(follow_up == 1) {
				window.location.href = site_base_url+'tasks/all/?id='+data.follow_up_id+'&type=random';
				return false;
			} else {
				window.location.href = site_base_url+'tasks/all/';
				return false;
				/* $('#task-table-' + task_being_edited).replaceWith(data.html);
				if (random_task_url != '')
				{
					// window.location.href = window.location.href;
					window.location.href = site_base_url+'tasks/all/';
				} */
			}
			$('.blockUI .task-add.task-edit').unblock();
			$.unblockUI();
			$('#edit-job-task')[0].reset();
			if ( $( "#search-job-task" ).length ||  $('#dashboard').val()==1 ) 
			{
				resetpage();
			}
			else
			{
				tabselector();
			}
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
		
		if(1!=$( "#taskslistval" ).val())
		{
			//return false;
		}
		
	}
	$('#jv-tab-4').block({
		message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
		css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
	});
	$.get(
		'ajax/request/get_job_tasks/' + curr_job_id+'?task_completed='+$("#taskcompleted").val(),
		{},
		function(data)
		{
			$( ".existing-task-list" ).empty();
			$('.existing-task-list').append(data);
			
			$('#jv-tab-4').unblock();
			$.unblockUI();
		}
	);
	
	// tasks_loaded = true;
}

var _task_require_qc = false;

function setTaskStatus(taskid, el)
{	
	var params = {};

	//dynamically bring the hostname and project folder name.
	var hstname  = window.location.host;
	var pathname = window.location.pathname;
	var pth      = pathname.split("/");
	
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
		/* if ( ! window.confirm('Are you sure you want to delete this task?'))
		{
			$('#jv-tab-4').unblock();
			$.unblockUI();
			return false;
		}
		else
		{
			params = {'taskid': taskid, 'delete_task': true};
			params[csrf_token_name] = csrf_hash_token;
		} */
		params = {'taskid': taskid, 'delete_task': true};
		params[csrf_token_name] = csrf_hash_token;
	}
	else /* set the status */
	{

		var notifyPM = 'NO', task_status_val = $('#set_task_status_' + taskid).val();
		
		if (task_status_val == 100 && ! _task_require_qc && $('tr.require-qc-' + taskid).size() > 0)
		{
			alert('To mark this task as 100%,\nplease click through to the job\nas it requires quality verification!');
			$('#jv-tab-4').unblock();
			$.unblockUI();
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
					else if (data.task_delete)
					{
						if (window.location.href == site_base_url+'tasks/all/')
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
					else 
					{
						$('#set_task_status_' + taskid).parents('.tasks').removeClass('complete')
					}
					if(1==$("#taskslistval").val())
					{
						
						if ( $( "#search-job-task" ).length ||  $('#dashboard').val()==1) 
						{
							resetpage();
						}
						else
						{
							tabselector();
						}
						
					}
					else if(typeof(this_is_home) != 'undefined')
					{
						window.location.href = window.location.href;
					}
				}
				
				// reset the global task qc check status
				_task_require_qc = false;				
				
				$('#jv-tab-4').unblock();
				$.unblockUI();
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

function deleteConfirm(taskid) {
	$.blockUI({
		message:'<br /><h5>Are you sure you want to delete this task?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+taskid+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}

function processDelete(taskid) {
	$.unblockUI();
	setTaskStatus(taskid, 'delete');
}

function cancelDel() {
	$('#jv-tab-4').unblock();
	$.unblockUI();
	return false;
}

function selectvalueassign(elementname,elementvalue,edit_table_el)
{
	$(elementname, edit_table_el).val(elementvalue);
	$(elementname, edit_table_el).removeAttr('disabled', ($.trim(elementvalue)) );
	$(elementname).removeAttr('disabled', true).trigger("liszt:updated");	
}

function selectoptionassign(elementname,elementvalue)
{
	$(elementname).val( elementvalue ).prop('selected',true);
	$(elementname).next("div").find("span").html($(elementname+" option:selected").text());			
}

function sumint(a,b)
{
	var input1 = parseInt(a);
	var input2 = parseInt(b);
	var input = input1 + input2;
	return input;
}

function tabselector()
{
	loadExistingTasks();
	/* $( ".ui-tabs-nav li" ).each(function( index ) 
	{
		alert("triugger");
		if($( this ).attr('aria-controls')=='jv-tab-4')
		{		
			$('.ui-tabs-nav li').eq(0).find("a").trigger('click');
			$('.ui-tabs-nav li').eq(index).find("a").trigger('click');
		}

	}
	); */
	
}


