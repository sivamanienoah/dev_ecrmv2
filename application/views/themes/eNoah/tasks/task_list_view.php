<?php
ob_start();
$userdata = $this->session->userdata('logged_in_user');
$permission =$this->session->all_userdata();
?>
<div id="dashboardcount" style="position: absolute;top: 10px;right:-50px;">
<label id="pendingval" class=""><a href='javascript:void(0);' onclick= 'refreshalltask(0);'> Pending Tasks : <?php echo $pendingtasks;?></a></label>&nbsp; &nbsp;
<label id="completedval" class=""><a href='javascript:void(0);' onclick= 'refreshalltask(1);'> All Tasks: <?php echo $completedtasks;?> </a></label> </div>
<div  style="width:100%">
<input type="hidden" value="1" name="taskslistval" id="taskslistval"/>
	<?php 
		$val="0";
		$pendingtasks=0;
		$table_head= array(
						"Task Description"=>"12",
						"Priority"=>"5",
						"Task Owner"=>"8",
						"Allocated to"=>"10",
						"Start Date(Plan)"=>"10",
						"End Date(Plan)"=>"10",
						"Start Date(Act)"=>"9",
						"End Date(Act)"=>"9",
						"Est.Hr"=>"4",
						"Complete %"=>"13",
						"Status"=>"5",	
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