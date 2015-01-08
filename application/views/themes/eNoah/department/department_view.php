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
			<?php /*?><?php if($this->session->userdata('add')==1) { ?>
				<div class="buttons pull-right">
					<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>project_types/project_type_add'">
						Add New Project Types
					</button>
				</div>
			<?php } ?><?php */?>
		<div class="clearfix"></div>
		</div>
	</div>
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th width="30%">Department</th>				
				<th width="15%">Added On</th>
				<th width="15%">Modified On</th>
				<th width="12%">Status</th>
			</tr>
		</thead>
		<tbody>
		<?php if (is_array($departments) && count($departments) > 0) { ?>
			<?php foreach($departments as $list_departments) { ?>
				<tr>
					<td><?php echo $list_departments['department_name']; ?></td>
					<td>
							<?php echo $list_departments['created_on']; ?>
					</td>
					<td>
							<?php echo $list_departments['modified_on']; ?>
					</td>
					<td class="actions">
					
						<?php if ($list_departments['active'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>
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
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>