<style>
.hide { display: none; }
</style>
<?php
ob_start();
$cfg = $this->config->item('crm');
if ($this->session->userdata('logged_in') == TRUE) {
	$userdata = $this->session->userdata('logged_in_user');
}
$permission =$this->session->all_userdata();
?>
<div id="task-page">
	<div class="task-contents">
		<div  style="width:100%">
		<input type="hidden" value="1" name="taskslistval" id="taskslistval"/>
			<?php 
				$val="0";
				$pendingtasks=0;	
				$table_head= array(
							"Project"=>"4",
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
<script type="text/javascript">
$(function(){
	var ts = $('.great-task-table').size();
	ts = Math.floor(ts / 2);
	$('.great-task-table:lt(' + ts + ')').addClass('right');
});
</script>