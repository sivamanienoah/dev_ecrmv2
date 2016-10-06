<?php
ob_start();
$userdata = $this->session->userdata('logged_in_user');
$permission =$this->session->all_userdata();
?>
<style>


 
</style>
<div id="dashboardcount" style="margin-left: 30%;position: relative;bottom: 26px;">
<label id="pendingval" class="label-warm"><a href='javascript:void(0);' onclick= 'refreshalltask(0);'> Pending Tasks : <?php echo $pendingtasks;?></a></label>&nbsp; &nbsp;
<label id="completedval" class="label-success"><a href='javascript:void(0);' onclick= 'refreshalltask(1);'> All Tasks: <?php echo $completedtasks;?> </a></label> </div>
<div  style="width:100%">
<input type="hidden" value="1" name="taskslistval" id="taskslistval"/>
	<?php 
		$val="0";
		$pendingtasks=0;
	$table_head= array(
						"Task Owner"=>"8",
						"Task Description"=>"12",
						"Priority"=>"6",
						"Allocated to"=>"10",
						"Planned Start Date"=>"11",
						"Planned End Date"=>"11",
						"Actual Start Date"=>"11",
						"Actual End Date"=>"11",
						"Status"=>"5",
						"Remarks"=>"8",	
						"Action"=>"7",		
						);		
	foreach($newarray as $row) 
	{
		
		if(0 < count($row['records']) )
		{
			$pendingtasks +=count($row['records']);
			$val="1";
			datatable_structure($row['records'],$permission,$row['values'],$row['categoryid'],$table_head);
		}
	}
	?>
	<?php if($val=='0')
	{	echo '<br/>';
		echo '<p class="task-notice">Sorry, there are no tasks set for this project!</p>';
	}
	?>
</div>

<script type="text/javascript" src="assets/js/tasks/task_list.js"></script>
