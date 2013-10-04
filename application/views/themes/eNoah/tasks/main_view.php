<?php
ob_start();
require VIEWPATH . 'tpl/header.php'; 
?>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript">var this_is_home = true;</script>
<script type="text/javascript">var curr_job_id = 0;</script>
<script type="text/javascript" src="assets/js/tasks.js?q=9"></script>

<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/vps.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/easypaginate.js"></script>
<style type="text/css">
@import url(assets/css/tasks.css?q=1);
/* over-ride tasks */
td.task {
	width:557px;
}
.great-task-table, .great-task-table td {
	border-color:#888;
}
.row-header td.user {
	font-size:12px;
}
.prior{background:purple}
/* end over-ride */
.existing-query-list {
    clear: left;
}
.cancel {
    background-color: #D8D8D8;
    border: 1px solid #CCCCCC;
    color: #565656;
    cursor: pointer;
}
ol#pagination{overflow:hidden; padding-top:50px; padding-left:15px;}
	ol#pagination li{
		float:left;
		list-style:none;
		cursor:pointer;
		margin:0 0 0 .5em;
		}
	ol#pagination li.current{color:#f00;font-weight:bold;}
</style>
<div id="content">
    <div class="inner" id="jv-tab-4">
	
		<?php
		include VIEWPATH . 'tpl/user_accounts_options.php';
		//echo '<pre>'; print_r($project); echo '</pre>';
		?>
		<h2>Tasks</h2>
	<!--	<p><a href="tasks" onclick="window.open(this.href); return false;">View Full Screen Tasks</a> | <a href="tasks/weekly" onclick="window.open(this.href); return false;">View Weekly Tasks</a></p>-->
		<div style="margin-top:20px;" class="tasks-mgmt">
				<form id="set-job-task" onsubmit="return false;">
				
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table border="0" cellpadding="0" cellspacing="0" class="task-add toggler">
						<tr>
							<td colspan="4">
								<strong>All fields are required!</strong>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<br /><br />Task Desc
							</td>
							<td colspan="3">
								<strong><span id="task-desc-countdown">1000</span></strong> characters left.<br />
								<textarea name="job_task" id="job-task-desc" class="width420px"></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Allocate to
							</td>
							<td>
								<select name="task_user" class="textfield width100px">
								<?php
								//echo $remind_options, $remind_options_all, $contractor_options;
								echo $remind_options, $remind_options_all;
								?>
								</select>
							</td>
							<!--<td>
								Hours
							</td>
							<td>
								<input name="task_hours" type="text" class="textfield width100px" /> Hours and
								<select name="task_mins" class="textfield">
									<option value="0">0</option>
									<option value="15">15</option>
									<option value="30">30</option>
									<option value="45">45</option>
								</select>
								Mins
							</td>-->
						</tr>
						
						<tr>
							<td>
								Start Date
							</td>
							<td>
								<input type="text" name="task_start_date" class="textfield pick-date width100px" />
							</td>
							<td>
								End Date
							</td>
							<td>
								<input type="text" name="task_end_date" class="textfield pick-date width100px" />
								&nbsp;
								<!--<select name="task_end_hour" class="textfield">
								<?php
								/*$time_range = array(
												'10:00:00'	=> '10:00AM',
												'11:00:00'	=> '11:00AM',
												'12:00:00'	=> '12:00PM',
												'13:00:00'	=> '1:00PM',
												'14:00:00'	=> '2:00PM',
												'15:00:00'	=> '3:00PM',
												'16:00:00'	=> '4:00PM',
												'17:00:00'	=> '5:00PM',
												'18:00:00'	=> '6:00PM',
												'19:00:00'	=> '7:00PM'
											 );
								foreach ($time_range as $k => $v)
								{
									$selected = ($k == '17:00:00') ? ' selected="selected"' : '';
									echo "
									<option value=\"{$k}\"{$selected}>{$v}</option>";
								}*/
								?>
								</select>
							</td>-->
						</tr>
						<tr>
							<td>
								Remarks
							</td>
							<td>
								
								<textarea name="task-remarks" id="task-remarks"></textarea>
							</td>
							
						</tr>
						<!--<tr>
							<td>&nbsp;</td>
							<td colspan=3>
								Priority Support : <input type="checkbox" name="priority" />
							</td>
						</tr>-->
						<tr>
							<td colspan="4">
								<div class="buttons">
									<button type="submit" class="positive" onclick="addNewTask('random');">Add</button>
								</div>
								<div class="buttons">
									<button type="submit" class="negative" onclick="$('.toggler').slideToggle();">Cancel</button>
								</div>
							</td>
						</tr>
					</table>
					
				</form>
		</div>
		<!--<p>To add a job task, please go to the relevant job page and add a task from the tasks tab</p>-->
		<p>
			<img src="assets/img/due_today.jpg" width="10" /> Due Today
			&nbsp;&nbsp;
			<img src="assets/img/task_delayed.jpg" width="10" /> Task Delayed
			&nbsp;&nbsp;
			<img src="assets/img/task_delayed_2days.jpg" width="10" /> Task Delayed more than 2 days
			&nbsp;&nbsp;
			<img src="assets/img/task_passed_deadline.jpg" width="10" /> Deadline Passed
			&nbsp;&nbsp;
			<img src="assets/img/task_completed.jpg" width="10" /> Task Completed
		</p>
		<div class="buttons task-init toggler clearfix">
						<button type="button" class="positive" onclick="$('.toggler').slideToggle();">Add New Task</button>
					</div>
			<p>&nbsp;</p>
			
		<div style="margin-top:15px;" class="tasks-search">
				<form id="search-job-task" onsubmit="return false;">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table border="0" cellpadding="0" cellspacing="4" class="task-add">
					<tr>
							<td colspan="4">
								<h4>Search</h4>
							</td>
							
						</tr>
						<tr>
							<td>
								Tasks Status
							</td>
							<td>
								<?php $arrayTask = $cfg['tasks_search']; ?>
								<select id="task_search" name="task_search" class="textfield width118px">
									<?php
										foreach($arrayTask as $key => $value):
											echo '<option value="'.$key.'">'.$value.'</option>';
										endforeach;
									?>
								</select>
							</td>
							<td>&nbsp;
								
							</td>
							<td>&nbsp;
								
							</td>
						</tr>
						
						<tr>
							<td>
								From Date
							</td>
							<td>
								<!--<input type="text" name="task_search_start_date" class="textfield pick-date width100px"/>-->
								<input type="text" name="task_search_start_date" class="textfield pick-date width100px"/>
							</td>
							<td>
								To Date
							</td>
							<td>
								<input type="text" id="task_search_end_date" name="task_search_end_date" class="textfield pick-date width100px" />
							</td>
						</tr>
						
						<tr>
							<td colspan="4">
								<div class="buttons">
									<button type="submit" class="positive" onclick="searchTasks();">Search</button>
									<button type="reset" class="negative">Reset</button>
								</div>
								<div class = "buttons">
									<!--<input type="hidden" value="<?php #echo date('d-m-Y'); ?>" id="hided">
									<input type="hidden" value="<?php #echo date('Y-m-d',(time()+(86400*7))); ?>" id="hided">-->
									<!--<button type="button" class="positive" onclick="searchtodayTasks();">Search Today</button>-->
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="4">
								<!--<p> &nbsp; Search tasks ending on this date range.</p>-->
							</td>
						</tr>
					</table>
				</form>
		</div>
			
		<form id="edit-job-task" onsubmit="return false;">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		<!-- edit task -->
			<table border="0" cellpadding="0" cellspacing="0" class="task-add task-edit">
			<?php
			


			 
							$uio = $userdata['userid'];
							$taskidd=$_POST['newPassword'];
							$cid='';
							$taskid="<div class='edit-job-task-id'></div>";	
							foreach($created_by as $value) {
								$b[] = $value[createdby];						
							}
							?>
							
							
								

				<tr>
					<td colspan="4">
						<!--<div class='edit-job-task-id'></div>-->
						<strong>All fields are required!</strong>						
						
						
					</td>
				</tr>
				<tr>
					<td valign="top" width="80">
						<br /><br />Task Desc
					</td>
					<td colspan="3">
						<strong><span id="edit-task-desc-countdown">1000</span></strong> characters left.<br />
						<textarea name="job_task" class="edit-job-task-desc width420px" ></textarea>
					</td>
				</tr>
				<tr>
					<td>Task Owner</td>
					<td><input type="text" class="edit-task-owner textfield" readonly="readonly"></td>					
				</tr>
				<?php
				 //if(in_array($uio,$b)) {
				?>
				
				<tr>
					<td>
						Allocate to
					</td>
					<td>
						<select name="task_user" class="edit-task-allocate textfield width100px" >
						<?php						
						echo $remind_options, $remind_options_all;
						?>						
						</select>
							
					</td>
					<!--<td>
						Hours
					</td>
					<td>
						<input name="task_hours" type="text" class="edit-task-hours textfield width100px" /> Hours and
						<select name="task_mins" class="edit-task-mins textfield">
							<option value="0">0</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
						</select>
						Mins
					</td>-->
				</tr>
				<?php //} else { ?>
				<!--<tr>
							<td>
								Allocate to
							</td>
							<td>
								<input type="text" name="task_user" class="edit-task-allocate1 textfield width100px" readonly />
							</td>
						</tr>-->
						<?php //} ?>
				<tr>
					<td>
						Planned Start Date
					</td>
					<td>
						<input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" />
					
					</td>		
					<td>
						Planned End Date
					</td>
					<td>								
					<input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px" />	
					</td>
				</tr>
				<tr>
					<td>Actual Start Date</td>
					<td>
					<?php if($created_by['jobid_fk'] == $remind_options) {  ?>
					
						<input type="text"  name="task_actualstart_date" class="edit-actualstart textfield pick-date width100px" readonly />
					
					<?php } else { ?>
						<input type="text" name="task_actualstart_date" class="edit-actualstart-date textfield pick-date width100px"/>
					<?php } ?>
					</td>
					<td>Actual End Date</td>
					<td class="actualend-date"><input type="text" class="edit-actualend-date pick-date textfield" ></td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td colspan="3">
					<?php if($created_by['jobid_fk'] == $remind_options) {  ?>
					<textarea name="remarks" class="edit-task-remarks" width="420px" readonly ></textarea>
					<?php } else { ?>
					<textarea name="remarks" class="edit-task-remarks" width="420px"  ></textarea>
					<?php } ?>
					</td>
				</tr>
				<!--<tr><td colspan=3>Priority Support : <input type="checkbox" name="priority" class="priority"/></td>-->
				<tr>
					<td colspan="4">
						<div class="buttons">
							<button type="submit" class="positive" onclick="editTask();">Update</button>
						</div>
						<div class="buttons">
							<button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button>
						</div>
					</td>
				</tr>
			</table>
		<!-- edit task end -->
		</form>
		<div style="margin-top:20px;" class="all-tasks">
		</div>
		<form style="display:none;" class="random-task-tables" onsubmit="return false;">
		</form>
	</div>
	
</div>
<script type="text/javascript">
$(function(){
 alert("test"); return false;
	$('.all-tasks').load('tasks/index/extend #task-page .task-contents', {}, loadEditTables);
	$('#set-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M'});
	$('#search-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy'});
	
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
	$.post('tasks/search',$('#search-job-task').serialize(),function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
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
	$.post('ajax/request/get_random_tasks',{'id_set': taskids.join(',')},function(data){
		if (data != '')	{
			$('form.random-task-tables').html(data);
		}
		$('#jv-tab-4').unblock();
	});
}
</script>
<?php
require VIEWPATH . 'tpl/footer.php';
ob_end_flush();
?>