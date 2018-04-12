<?php
ob_start();
require (theme_url().'/tpl/header.php');
?>
<link rel="stylesheet" href="assets/css/chosen.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript">var this_is_home = true;</script>
<script type="text/javascript">var curr_job_id = 0;</script>
<script type="text/javascript" src="assets/js/tasks.js?q=9"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script src="assets/js/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> 
$(function(){
	var config = {
	  '.chzn-select'           : {},
	  '.chzn-select-deselect'  : {allow_single_deselect:true},
	  '.chzn-select-no-single' : {disable_search_threshold:10},
	  '.chzn-select-no-results': {no_results_text:'Oops, nothing found!'},
	  '.chzn-select-width'     : {width:"95%"}
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]);
	}
});
</script>
<div id="content">
    <div class="inner" id="jv-tab-4">
		<?php include theme_url() . '/tpl/user_accounts_options.php'; ?>
		<h2>Tasks</h2>

		<div style="margin-top:20px;" class="tasks-mgmt">
			<form id="set-job-task" onsubmit="return false;">
			
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<table border="0" cellpadding="0" cellspacing="0" class="task-add toggler">
					<tr>
						<td colspan="4"><strong>All fields are required!</strong></td>
					</tr>
					<tr>
						<td valign="top"><br /><br />Task Desc</td>
						<td colspan="3">
							<strong><span id="task-desc-countdown">1000</span></strong> characters left.<br />
							<textarea name="job_task" id="job-task-desc" class="width420px"></textarea>
						</td>
					</tr>
					<tr>
						<td style="padding-bottom:10px;" ><br/>Category</td>
						<td>
							<select name="task_category" data-placeholder="Choose category." class="chzn-select" id="taskCategory" style="width:140px;">
								<option value=""></option>
								<?php
									foreach($category_listing_ls as $ua)
									{
										echo '<option value="'.$ua['id'].'">'.$ua['task_category'].'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td style="padding-bottom:10px;">Priority</td>
						<td>
							<select name="task_priority" data-placeholder="Choose Priority." class="chzn-select" id="taskpriority" style="width:140px;">
								<option value=""></option>
								<option value="1">Critical</option>
								<option value="2">High</option>
								<option value="3">Medium</option>
								<option value="4">Low</option>
								
							</select>
						</td>
					</tr>
					<tr>
						<td >Allocate to</td>
						<td style="padding: 5px 0;">
							<select name="task_user" style="width:160px;" class="chzn-select textfield" data-placeholder="Choose a User...">
							<?php
								echo $remind_options, $remind_options_all;
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Estimated Hours</td>
						<td><input type="text" name="estimated_hours" class="textfield width100px" onkeypress="return isPaymentVal(event)" style="margin-top:5px;" maxlength="5"/></td>
					</tr>
					<tr>
						<td>Start Date</td>
						<td><input type="text" name="task_start_date" class="textfield pick-date width100px" style="margin-top:5px;"/></td>
						<td>End Date</td>
						<td><input type="text" name="task_end_date" class="textfield pick-date width100px" />
					</tr>
					<tr>
						<td>Remarks</td>
						<td><textarea name="task-remarks" id="task-remarks"></textarea></td>
					</tr>
					<tr>
						<td colspan="4">
							<div class="buttons">
								<button type="submit" class="positive" onclick="addNewTask('random','<?php echo $this->security->get_csrf_token_name()?>','<?php echo $this->security->get_csrf_hash(); ?>');">Add</button>
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
		<p style="margin:15px 0 15px;">
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
		
		<?php 
		if($this->session->userdata('add')==1) {
		?>
			<div class="buttons task-init toggler clearfix">
				<button type="button" class="positive" onclick="$('.toggler').slideToggle();">Add New Task</button>
			</div>
		<?php
		}
		?>
		
		<p>&nbsp;</p>
			
		<div style="margin-top:15px;" class="tasks-search">
			<form id="search-job-task" onsubmit="return false;" style="overflow:visible;">
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<table border="0" cellpadding="0" cellspacing="4" class="task-add" style="width:46%;">
					<tr>
						<td colspan="4">
							<h4>Search</h4>
						</td>
					</tr>
					
					<tr >
					<td style="padding-bottom:10px;" ><br/></td>
					<td>

					</td>
				</tr>
				<tr>
						<td>
							Project
						</td>
						<td>
							<select name="task_project" data-placeholder="Choose Projects" class="chzn-select clr-czn" id="taskCategory" style="width:140px;">
							<option value=""></option>
							<?php
							foreach($project_listing_ls as $ua)
							{
								echo '<option value="'.$ua['lead_id'].'">'.$ua['lead_title'].'</option>';
							}
							?>
							</select>
						</td>
						<td>
							Category
						</td>
						<td>
							<select name="task_category" data-placeholder="Choose category" class="chzn-select clr-czn" id="taskCategory" style="width:140px;">
							<option value=""></option>
							<?php
							foreach($category_listing_ls as $ua)
							{
							echo '<option value="'.$ua['id'].'">'.$ua['task_category'].'</option>';
							}
							?>
							</select>
						</td>
					</tr>
				
				<tr>
						
						<td>
							Task Owner
						</td>
						<td>
							<select name="task_owner_user" class="chzn-select clr-czn textfield width100px" >
							
<?php						
							echo $remind_options, $remind_options_all;
							?>						
							</select>
						</td>
						<td>
							Allocated Member
						</td>
						<td>
						<select name="task_allocated_user" class="chzn-select clr-czn textfield width100px" >
							<option value=""></option>
							<?php						
							echo $remind_options, $remind_options_all;
							?>						
						</select>
						</td>
					</tr>
					<tr>
						<!--td>
							Tasks Status
						</td>
						<td>
							<?php #$arrayTask = $cfg['tasks_search']; ?>
							<select id="task_search" name="task_search"   data-placeholder="Choose category."  class=" chzn-select textfield width118px">
								<?php
									/* foreach($arrayTask as $key => $value):
										echo '<option value="'.$key.'">'.$value.'</option>';
									endforeach; */
								?>
							</select>
						</td-->
						<td>
							Tasks Status
						</td>
						<td>
							<select id="task_search" name="task_search" data-placeholder="Choose Status" class="chzn-select clr-czn textfield width118px">
								<option value=""></option>
								<?php
									foreach($task_stages as $tstag){
										echo '<option value="'.$tstag['task_stage_id'].'">'.$tstag['task_stage_name'].'</option>';
									}
								?>
							</select>
						</td>
						<td>
							Task Priority
						</td>
						<td>
							<select name="task_priority" data-placeholder="Choose Priority" class="chzn-select clr-czn" id="taskpriority" style="width:140px;">
							<option value=""></option>
							<option value="1">Critical</option>
							<option value="2">High</option>
							<option value="3">Medium</option>
							<option value="4">Low</option>
							
						</select>
						</td>
					</tr>

					<tr>
						<td>
							From Date
						</td>
						<td>
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
							
							<br/>
							<br/>
								<button type="submit" class="positive" onclick="searchTasks();">Search</button>
								<button type="reset" class="negative" onclick="resetpage();">Reset</button>
							</div>
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
				$cid='';
				$taskid="<div class='edit-job-task-id'></div>";
			?>
				<tr>
					<td colspan="4">
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
				<tr >
					<td style="padding-bottom:10px;" ><br/>Category</td>
					<td>
						<select name="task_category" data-placeholder="Choose category." class="chzn-select edit-task-category" id="taskCategory" style="width:140px;">
							<option value=""></option>
							<?php
								foreach($category_listing_ls as $ua)
								{
									echo '<option value="'.$ua['id'].'">'.$ua['task_category'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">Priority</td>
					<td>
						<select name="task_priority" data-placeholder="Choose Priority." class="chzn-select edit-task-priority" id="taskpriority" style="width:140px;">
							<option value=""></option>
							<option value="1">Critical</option>
							<option value="2">High</option>
							<option value="3">Medium</option>
							<option value="4">Low</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Allocate to
					</td>
					<td style="padding: 5px 0;">
						<select name="task_user" class="chzn-select edit-task-allocate textfield width100px" >
							<?php						
							echo $remind_options, $remind_options_all;
							?>						
						</select>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">Status</td>
					<td>
						<select name="task_priority" data-placeholder="Choose Status." class="chzn-select edit-task-stages" id="taskstages" style="width:140px;">
							<option value=""></option>
							<?php
								foreach($task_stages as $tstag)
								{
									echo '<option value="'.$tstag['task_stage_id'].'">'.$tstag['task_stage_name'].'</option>';
								}
							?>
						</select>
						<input type="hidden" name="task_complete_status" id="edit_complete_status" class="edit-complete-status textfield width100px" />	
					</td>
				</tr>
				<tr>
					<td>Estimated Hours</td>
					<td><input type="text" name="estimated_hours" class="edit-job-est-hr textfield width100px" onkeypress="return isPaymentVal(event)" style="margin-top:5px;" maxlength="5"/></td>
				</tr>
				<tr>
					<td>
						Planned Start Date
					</td>
					<td>
						<input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" style="margin-top:5px;"/>
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
						<?php
						if($created_by['jobid_fk'] == $remind_options) {
						?>
							<input type="text"  name="task_actualstart_date" class="edit-actualstart textfield pick-date width100px" readonly />
						<?php 
						} else {
						?>
							<input type="text" name="task_actualstart_date" class="edit-actualstart-date textfield pick-date width100px"  />
						<?php 
						} 
						?>
					</td>
					<td>Actual End Date</td>
					<td class="actualend-date"><input type="text" class="edit-actualend-date textfield" readonly></td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td colspan="3">
						<?php 
						if($created_by['jobid_fk'] == $remind_options) {
						?>
							<textarea name="remarks" class="edit-task-remarks" width="420px" readonly ></textarea>
						<?php
						} else {
						?>
							<textarea name="remarks" class="edit-task-remarks" width="420px"  ></textarea>
						<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="buttons">
							<button type="submit" class="positive" onclick="editTask();">Update</button>
						</div>
						<div class="buttons">
							<button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button>
						</div>
					</td>
					<td colspan="2">
						<div class="buttons">
							<label><input type="checkbox" name="follow_up" id="follow_up_task" value="1" /><span class="tas-flw">Create Follow Up</span></label>
						</div>
					</td>
				</tr>
			</table>
		</form>
		<div style="margin-top:20px;" class="all-tasks" id="tasks_content_ajax">
		</div>
		<form style="display:none;" class="random-task-tables" onsubmit="return false;">
		</form>
	</div>
	<form id="add-follow-job-task" style="display:none;" onsubmit="return false;">
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		<!-- edit task -->
			
			<table border="0" cellpadding="0" cellspacing="0" class="task-add-follow">
				<tr>
					<td colspan="4">
						<strong>All fields are required!</strong>						
					</td>
				</tr>
				<tr>
					<td valign="top" width="80">
						<br /><br />Task Desc
					</td>
					<td colspan="3">
						<strong><span id="edit-task-desc-countdown">1000</span></strong> characters left.<br />
						<textarea name="job_task" id="follow-job-task-desc" class="edit-job-task-desc width420px" ></textarea>
						<input type="hidden" name="jobid_fk" id="jobid_fk_follow_up" value="" />
					</td>
				</tr>
				<tr>
					<td>Task Owner</td>
					<td><input type="text" id="tast_user_lbl" class="edit-task-owner textfield" value="<?php echo $this->userdata['first_name'].' '.$this->userdata['last_name']; ?>" readonly="readonly"></td>					
				</tr>
				<tr >
					<td style="padding-bottom:10px;" ><br/>Category</td>
					<td>
						<select name="task_category" data-placeholder="Choose category." class="chzn-select edit-task-category" id="follow_taskCategory" style="width:140px;">
							<option value=""></option>
							<?php
								foreach($category_listing_ls as $ua)
								{
									echo '<option value="'.$ua['id'].'">'.$ua['task_category'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">Priority</td>
					<td>
						<select name="task_priority" data-placeholder="Choose Priority." class="chzn-select edit-task-priority" id="follow_taskpriority" style="width:140px;">
							<option value=""></option>
							<option value="1">Critical</option>
							<option value="2">High</option>
							<option value="3">Medium</option>
							<option value="4">Low</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Allocate to
					</td>
					<td style="padding: 5px 0;">
						<select name="task_user" id="follow_task_user" class="chzn-select edit-task-allocate textfield width100px" >
							<?php						
							echo $remind_options, $remind_options_all;
							?>						
						</select>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">Status</td>
					<td>
						<select name="task_priority" data-placeholder="Choose Status." class="chzn-select edit-task-stages" id="follow_taskstages" style="width:140px;">
							<option value=""></option>
							<?php
								foreach($task_stages as $tstag)
								{
									echo '<option value="'.$tstag['task_stage_id'].'">'.$tstag['task_stage_name'].'</option>';
								}
							?>
						</select>
						<input type="hidden" name="task_complete_status" value='0' />	
					</td>
				</tr>
				<tr>
					<td>Estimated Hours</td>
					<td><input type="text" name="estimated_hours" class="edit-job-est-hr textfield width100px" onkeypress="return isPaymentVal(event)" style="margin-top:5px;" maxlength="5"/></td>
				</tr>
				<tr>
					<td>
						Planned Start Date
					</td>
					<td>
						<input type="text" name="task_start_date" id="follow-edit-start-date" class="follow-edit-start-date textfield pick-date width100px" style="margin-top:5px;"/>
					</td>		
					<td>
						Planned End Date
					</td>
					<td>
						<input type="text" name="task_end_date" id="follow-edit-end-date" class="follow-edit-end-date textfield pick-date width100px" />
					</td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td colspan="3">
						<?php 
						if($created_by['jobid_fk'] == $remind_options) {
						?>
							<textarea name="remarks" id="follow-task-remarks" class="edit-task-remarks" width="420px" readonly ></textarea>
						<?php
						} else {
						?>
							<textarea name="remarks" id="follow-task-remarks" class="edit-task-remarks" width="420px"  ></textarea>
						<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="buttons">
							<button type="submit" class="positive" onclick="addNewFollowTask('random','<?php echo $this->security->get_csrf_token_name()?>','<?php echo $this->security->get_csrf_hash(); ?>');">Add</button>
						</div>
						<div class="buttons">
							<button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button>
						</div>
					</td>
				</tr>
			</table>
			<script>
			$('select').chosen( { width: '100%' } );
			</script>
		</form>
	
</div>
<script type="text/javascript">
	var task_userid = '<?php echo $userdata['userid'] ?>';
	var get_type 	= '<?php echo isset($_GET['type']) ? $_GET['type'] : '' ?>';
	var get_id      = '<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>';
	var job_id      = '<?php echo isset($_GET['job_id']) ? $_GET['job_id'] : '' ?>';
</script>
<script type="text/javascript" src="assets/js/tasks/main_view.js"></script>
<?php
require theme_url() . '/tpl/footer.php';
ob_end_flush();
?>