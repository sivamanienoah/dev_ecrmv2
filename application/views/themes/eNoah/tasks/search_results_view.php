<?php
ob_start();
$cfg = $this->config->item('crm');
if ($this->session->userdata('logged_in') == TRUE) {
	$userdata = $this->session->userdata('logged_in_user');
}
$permission =$this->session->all_userdata();

/* $lead_access = getAccessFromLead($userdata['userid'], $result['lead_id']);
	$team_access = getAccessFromTeam($userdata['userid'], $result['lead_id']);
	$stake_access = getAccessFromStakeHolder($userdata['userid'], $result['lead_id']);
	
	$link_access = 0;
	if($lead_access == 1 || $team_access == 1 || $stake_access == 1) {
		$link_access = 1;
	} */
	
	
?>
<div id="task-page">
	<div class="task-contents">
<div  style="width:100%">
<input type="hidden" value="1" name="taskslistval" id="taskslistval"/>
	<?php 
	
		$table_head= array(
			"Project"=>"4",
			"Task Description"=>"12",
			"Priority"=>"5",
			"Task Owner"=>"8",
			"Allocated to"=>"10",
			"Planned Start Date"=>"10",
			"Planned End Date"=>"10",
			"Actual Start Date"=>"9",
			"Actual End Date"=>"9",
			"Status"=>"12",
			"Remarks"=>"4",	
			"Action"=>"7",		
			);	

		$val="0";
		$pendingtasks=0;	
	foreach($newarray as $row) 
	{
		
		if(0 < count($row['records']) )
		{
			$pendingtasks +=count($row['records']);
			$val="1";
			datatable_structure($row['records'],$permission,$row['values'],$row['categoryid'],$table_head,1);
		}
	}
	?>
	<?php if($val=='0')
	{	echo '<br/>';
		echo '<p class="task-notice">Sorry, there are no tasks set for this project!</p>';
	}
	?>
</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo  $this->config->item('base_url')."";?>assets/js/tasks/task_list.js"></script>