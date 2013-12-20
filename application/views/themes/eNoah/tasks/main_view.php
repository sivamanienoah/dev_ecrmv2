<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
?>

<style type="text/css">
@import url(assets/css/tasks.css?q=1);
</style>

<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript">var this_is_home = true;</script>
<script type="text/javascript">var curr_job_id = 0;</script>
<script type="text/javascript" src="assets/js/tasks.js?q=9"></script>

<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/easypaginate.js"></script>

<div id="content">
    <div class="inner" id="jv-tab-4">
	
		<?php include theme_url() . '/tpl/user_accounts_options.php'; ?>
		<h2>Tasks</h2>

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
						</tr>
						<tr>
							<td>
								Remarks
							</td>
							<td>
								
								<textarea name="task-remarks" id="task-remarks"></textarea>
							</td>
							
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
				</tr>
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
		</form>
		<div style="margin-top:20px;" class="all-tasks">
		</div>
		<form style="display:none;" class="random-task-tables" onsubmit="return false;">
		</form>
	</div>
	
</div>
<script type="text/javascript">
	var task_userid = '<?php echo $userdata['userid'] ?>';
	var get_type 	= '<?php echo $_GET['type'] ?>';
	var get_id      = '<?php echo $_GET['id'] ?>';
</script>
<script type="text/javascript" src="assets/js/tasks/main_view.js"></script>
<?php
require theme_url() . '/tpl/footer.php';
ob_end_flush();
?>