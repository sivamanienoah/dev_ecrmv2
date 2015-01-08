<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
	<div class="inner">
	<?php if($this->session->userdata('accesspage')==1) { ?>
	
	<div style="padding-bottom: 10px;">
		<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<?php if($this->session->userdata('add')==1) { ?>
				<div class="buttons pull-right">
					<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>project_types/project_type_add'">
						Add New Project Types
					</button>
				</div>
			<?php } ?>
		<div class="clearfix"></div>
		</div>
	</div>
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th width="20%">Project Type</th>
				<th width="12%">Status</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		<?php if (is_array($project_types) && count($project_types) > 0) { ?>
			<?php foreach($project_types as $project_types_name) { ?>
				<tr>
					<td><?php echo $project_types_name['project_types']; ?></td>
					<td>
						<?php if ($project_types_name['status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>
					</td>
					<td class="actions">
						<?php if($this->session->userdata('edit')==1) { ?>
							<a href="project_types/project_type_add/update/<?php echo $project_types_name['id']; ?>" title='Edit' ><img src="assets/img/edit.png" alt='edit'> </a>
						<?php } ?> 
						<?php if($this->session->userdata('delete')==1) { ?>
							<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $project_types_name['id']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a>
						<?php } ?>
						<div class="dialog-err pull-right" id="dialog-message-<?php echo $project_types_name['id']; ?>" style="display:none"></div>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
		</tbody>
	</table>
	<?php 
	} else { 
		echo "You have no rights to access this page"; 
	} 
	?>
	</div><!--Inner div-close here -->
</div><!--Content div-close here -->
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/project_types/project_types_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>