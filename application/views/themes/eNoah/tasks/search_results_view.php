<?php
ob_start();
$cfg = $this->config->item('crm');
if ($this->session->userdata('logged_in') == TRUE) {
	$userdata = $this->session->userdata('logged_in_user');
	#define the users who can see the prices
	$sensitive_information_allowed = ( in_array($userdata['level'], array(0, 1, 2, 4, 5)) ) ? TRUE : FALSE;
}

echo '<div class="search-results task-contents">';
//echo "<pre>"; print_r($results); exit;
if (empty($results))
		{
			echo "No Records found.";
		}
		else 
# weekend offset
$wend_offset = 0;
if (date('l', $start_date_stamp) == 'Monday')
{
	$wend_offset = 2;
}
else if (date('l', $start_date_stamp) == 'Tuesday')
{
	$wend_offset = 1;
}

$user_tasks = $unallocated_tasks = array();
foreach ($results as $result)
{
	/*if ($result['approved'] == 0)
	{
		$unallocated_tasks[$result['userid_fk']]['user_name'] = $result['user_label'];
		$unallocated_tasks[$result['userid_fk']]['tasks'][$result['taskid']] = array(
																			'task' => $result['task'],
																			'company' => ($result['company'] == NULL) ? 'General Task' : $result['company'],
																			'hours' => str_pad($result['hours'], 2, '0', STR_PAD_LEFT),
																			'mins' => str_pad($result['mins'], 2, '0', STR_PAD_RIGHT),
																			'status' => $result['status'],
																			'start_date' => $result['start_date'],
																			'end_date' => $result['end_date'],
																			'delayed' => (int) $result['delayed'] * -1,
																			'due_today' => $result['due_today'],
																			'jobid' => $result['jobid'],
																			'lead' => $result['lead']
																		);
	}
	else
	{
		$user_tasks[$result['userid_fk']]['user_name'] = $result['user_label'];
		$user_tasks[$result['userid_fk']]['tasks'][$result['taskid']] = array(
																			'task' => $result['task'],
																			'company' => ($result['company'] == NULL) ? 'General Task' : $result['company'],
																			'hours' => str_pad($result['hours'], 2, '0', STR_PAD_LEFT),
																			'mins' => str_pad($result['mins'], 2, '0', STR_PAD_RIGHT),
																			'status' => $result['status'],
																			'start_date' => $result['start_date'],
																			'end_date' => $result['end_date'],
																			'delayed' => (int) $result['delayed'] * -1,
																			'due_today' => $result['due_today'],
																			'jobid' => $result['jobid'],
																			'lead' => $result['lead']
																		);
	}
	*/
	
	if ($result['approved'] == 0)
	{
		$unallocated_tasks[$result['userid_fk']]['user_name'] = $result['user_label'];
		$unallocated_tasks[$result['userid_fk']]['tasks'][$result['taskid']] = array(
																			'task' => $result['task'],
																			'company' => ($result['company'] == NULL) ? 'General Task' : $result['company'],
																			'hours' => str_pad($result['hours'], 2, '0', STR_PAD_LEFT),
																			'mins' => str_pad($result['mins'], 2, '0', STR_PAD_RIGHT),
																			'status' => $result['status'],
																			'is_complete' => $result['is_complete'],
																			'start_date' => date('d-m-Y', strtotime($result['start_date'])),
																			'end_date' => date('d-m-Y', strtotime($result['end_date'])),
																			'delayed' => (int) $result['delayed'] * -1,
																			'due_today' => $result['due_today'],
																			'jobid' => $result['jobid'],
																			'leadid' => $result['leadid'],
																			'require_qc' => $result['require_qc'],
																			'priority' => $result['priority'],
																			'taskowner' => $result['created'],
																			'actualstart_date' => $result['actualstart_date'],
																			'actualend_date' => $result['actualend_date'],
																			'created_byid' => $result['created_byid'],
																			'userid_fk' => $result['userid_fk'],
																			'remark' => $result['remark']
																		);
	}
	else
	{
		$user_tasks[$result['userid_fk']]['user_name'] = $result['user_label'];
		$user_tasks[$result['userid_fk']]['tasks'][$result['taskid']] = array(
																			'task' => $result['task'],
																			'company' => ($result['company'] == NULL) ? 'General Task' : $result['company'],
																			'hours' => str_pad($result['hours'], 2, '0', STR_PAD_LEFT),
																			'mins' => str_pad($result['mins'], 2, '0', STR_PAD_RIGHT),
																			'status' => $result['status'],
																			'is_complete' => $result['is_complete'],
																			'start_date' => date('d-m-Y', strtotime($result['start_date'])),
																			'end_date' => date('d-m-Y', strtotime($result['end_date'])),
																			'delayed' => (int) $result['delayed'] * -1,
																			'due_today' => $result['due_today'],
																			'jobid' => $result['jobid'],
																			'leadid' => $result['leadid'],
																			'require_qc' => $result['require_qc'],
																			'priority' => $result['priority'],
																			'taskowner' => $result['created'],
																			'actualstart_date' => $result['actualstart_date'],
																			'actualend_date' => $result['actualend_date'],
																			'created_byid' => $result['created_byid'],
																			'userid_fk' => $result['userid_fk'],
																			'remark' => $result['remark']
																		);
	}
}
$uio = $userdata['userid'];
foreach($created_by as $value) {
	$b[] = $value[created_by];						
}
	
	$i = 0;
	foreach ($user_tasks as $uk => $ut)
	{
		if($userdata['role_id'] == 1 || $userdata['userid'] != $uk ) {
			$use = $ut['user_name'].' - ';			
		}
		else {
			$use =  '' ; 
		}
		
			$utuser = $ut['user_name'];
			$td = '<td class="user" align="center" width="200px">Assigned To</td>';
			$tdcon = '<td valign=top align=center>'.$utuser.'</td>';
		
		/*mycahanges*/
			
			$tdowner = '<td class="user" align="center" width="200px">Assigned By</td>';
			
			/*ends*/
		
			if($uk == $userdata['userid']) {
				$title = "<h3 style='border-bottom:1px solid #ccc;'>Tasks Assigned To Me</h3><br />";
			} else if($uk == in_array($uio,$b)) {
				$title = "<h3 style='border-bottom:1px solid #ccc;'>Tasks Assigned To -"." ". $utuser." </h3><br />";
			}
		echo <<< EOD
		{$title}
	<table class="great-task-table" border="0" cellpadding="0" cellspacing="0" id="user-{$uk}">
		<tr class="row-header">
			<td class="user">Task Description</td>
			<td class="user" align="center">Task Remarks</td>
			{$tdowner}
			{$td}
			
			<td class="user" align="center" width="200px">Planned Start Date</td>
			<td class="user" align="center" width="200px">Planned End Date</td>
			<td class="user" align="center" width="220px">Actual Start Date</td>
			<td class="user" align="center" width="220px">Actual End Date</td>
			<td class="user" align="center" width="150px">Status</td>
			<td class="user" align="center" width="100px">Action</td>
		</tr>
EOD;
		$total_time = $today_total_time = 0;
		foreach ($ut['tasks'] as $tk => $task)
		{		
		//print_r($task);exit;
			$format_task = nl2br($task['task']);
			$prior='';$complete = '';
			if ($task['priority'] == '1' && $task['status'] != '100')
			{
				$prior = ' prior';
			}
			else if ($task['status'] == '100')
			{
				$complete = ' complete';
			}
			else
			{
				$total_time += (int) $task['hours'] * 60;
				$total_time += (int) $task['mins'];
				
				if ($task['due_today'] == '1' || $task['delayed'] > 0)
				{
					$today_total_time += (int) $task['hours'] * 60;
					$today_total_time += (int) $task['mins'];
				}
			}
			/*mycahanges*/
			
			 $utowner = $task['taskowner'];
			$tdowner = '<td align="center">Assigned By</td>';
			$tdownercon = '<td valign=top align=center>'.$utowner.'</td>';			
			
			/*ends*/
			if ($task['delayed'] > 1)
			{
				$task['delayed'] -= $wend_offset;
			}
			
			$delayed = '';
			if ($task['delayed'] == 1)
			{
				$delayed = ' late';
			}
			else if ($task['delayed'] == 2)
			{
				$delayed = ' late-2';
			}
			else if ($task['delayed'] > 2)
			{
				$delayed = ' late-more';
			}
			
			$due_today = ($task['due_today'] == '1' && $task['status'] != '100') ? ' due-today' : '';
			
			if ($task['company'] != 'General Task')
			{
				if ($task['leadid'] == 'YES')
				{
					$company_link = "<a href=\"leads/index/{$task['jobid']}\">{$task['company']}</a>";
				}
				else
				{
					$company_link = "<a href=\"welcome/view_quote/{$task['jobid']}\">{$task['company']}</a>";
				}
				
				$random_task_class = '';
			}
			else
			{
				$company_link = $task['company'];
				//$random_task_class = ' random-task';
			}
			
			if($userdata['role_id'] == 1 || in_array($userdata['userid'],$b)) {
				$random_task_class = ' random-task';
			} else if($task['userid_fk'] == $userdata['userid']){
				$random_task_class = ' newrandom-task';
			}
			
			
			$require_qc = ($task['require_qc'] == '1') ? " require-qc-{$tk}" : '';
			
			$options = array(0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100);
			$opts = '';
			foreach ($options as $o)
			{
				$sel = ($task['status'] == $o) ? ' selected="selected"' : '';
				$opts .= "<option value=\"{$o}\"{$sel}>{$o}%</option>";
			}
			
			
			/*mychanges*/
			$actualstart_date=$task['actualstart_date'];
			$actualend_date=$task['actualend_date'];
				if($actualstart_date != '0000-00-00') {					
					$actualstart_date = date('d-m-Y', strtotime($actualstart_date));					
				}
				else {
					$actualstart_date = ' - ';					
				}
				
				if($actualend_date != '0000-00-00') {
					$actualend_date = date('d-m-Y', strtotime($actualend_date));
				} 
				else {
					$actualend_date = ' - ';
				}
			/*ends*/
			
			
			if($userdata['userid'] == $uk && $task['is_complete'] != 1) {
			//$own_task_form = ($uk != $userdata['userid'] || $task['leadid'] == 'YES') ? '' : <<< EOD
			$own_task_form =  <<< EOD
		<td valign="top" align="center"><form onsubmit="return false" style="margin-bottom:0;">
			<select name="set_task_status_{$tk}" id="set_task_status_{$tk}" class="set-task-status" style="margin-bottom:0;">
				{$opts}
			</select>
			<div class="buttons">
				<button type="submit" onclick="setTaskStatus('{$tk}'); return false;">Set</button>
			</div>
		</form></td>
EOD;
	} else {
			$stat =$task['status'];
			//echo $completed = $task['is_complete']; exit;
			$own_task_form = '<td class=\"status-'.$stat.'\" valign=top align=right>'.$stat.'%</td>';
			}
			
			if($userdata['userid'] == $uk && $userdata['userid'] != $task['created_byid'] && $this->session->userdata('edittask') == 1) {
			$action = <<< EOD
			<button type="submit" onclick="openEditTask('{$tk}','random'); return false;">Edit</button> 
EOD;
	} else if($userdata['userid'] == $task['created_byid'] && $this->session->userdata('deletetask') == 1 && $this->session->userdata('edittask') == 1 && $task['is_complete'] != 1) {
			$action = <<< EOD
			<button type="submit" onclick="openEditTask('{$tk}','random'); return false;">Edit</button> 
			<button type="submit" onclick="setTaskStatus('{$tk}','complete'); return false;">Approve</button> 
			<button type="submit" onclick="setTaskStatus('{$tk}', 'delete'); return false;">Delete</button>
EOD;
	} else if($userdata['userid'] == $task['created_byid'] && $this->session->userdata('edittask') == 1 && $task['is_complete'] != 1) {
			$action = <<< EOD
			<button type="submit" onclick="openEditTask('{$tk}','random'); return false;">Edit</button> 
			<button type="submit" onclick="setTaskStatus('{$tk}','complete'); return false;">Approve</button> 
			
EOD;
	} else if($userdata['userid'] == $uk && $userdata['userid'] == $task['created_byid'] && $this->session->userdata('deletetask') == 1 && $this->session->userdata('edittask') == 1 && $task['is_complete'] != 1) {
		$action = <<< EOD
			<button type="submit" onclick="openEditTask('{$tk}','random'); return false;">Edit</button> 
			<button type="submit" onclick="setTaskStatus('{$tk}','complete'); return false;">Approve</button> 
			<button type="submit" onclick="setTaskStatus('{$tk}', 'delete'); return false;">Delete</button>
EOD;
    }
	else {
			$action = 'No Access';
		}
				
			
			
		echo "<tr class=\"tasks{$delayed}{$due_today}{$require_qc}{$prior}{$complete}\">
			<td class=\"first\" rel='{$tk}' valign=top >{$format_task}</td>
			<td class=\"rema\" valign=top >{$task['remark']}</td>
			{$tdownercon}
			{$tdcon}
					
			";

			echo "<td class=start-date valign=top align=center>{$task['start_date']}</td>
			<td class=end-date valign=top align=center>{$task['end_date']}</td>
			<td valign=top align=center>{$actualstart_date}</td>
			<td valign=top align=center>{$actualend_date}</td>
			{$own_task_form}
			<td valign=top class=\"task{$random_task_class}\" rel='{$tk}'> <div class=\"buttons\">{$action} 
								 
							</div></td>
		</tr>";
		}
		$mins = $total_time % 60;
		$hours = floor($total_time / 60);
		$total_hours = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_RIGHT);
		$mins = $today_total_time % 60;
		$hours = floor($today_total_time / 60);
		$today_total_hours = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_RIGHT);
		echo <<< EOD
		
	</table>	
EOD;
?>
<p><?php echo '&nbsp;'; ?></p>	
<?php
		$i++;
		unset($user_tasks[$uk]);
	}

	
echo '</div>';

ob_end_flush();
