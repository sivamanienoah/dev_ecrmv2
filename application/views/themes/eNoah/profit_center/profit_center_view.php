<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
	<div class="inner">
	<?php if($this->session->userdata('accesspage')==1) { ?>
	
	<div class="page-title-head">
		<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<?php if($this->session->userdata('add')==1) { ?>
				<div class="section-right">
					<div class="buttons add-new-button">
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>profit_center/profit_center_add'">
							Add New Profit Center
						</button>
					</div>
				</div>
			<?php } ?>
		<div class="clearfix"></div>
	</div>
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th width="20%">Profit Center</th>
				<th width="20%">Profit Center Code</th>
				<th width="12%">Status</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		<?php if (is_array($profit_center) && count($profit_center) > 0) { ?>
			<?php foreach($profit_center as $profit_center_name) { ?>
				<tr>
					<td><?php echo $profit_center_name['profit_center']; ?></td>
					<td><?php echo substr(strtoupper($profit_center_name['profit_center']), 0, 3); ?></td>
					<td>
						<?php if ($profit_center_name['status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>
					</td>
					<td class="actions">
						<?php if($this->session->userdata('edit')==1) { ?>
							<a href="profit_center/profit_center_add/update/<?php echo $profit_center_name['id']; ?>" title='Edit' ><img src="assets/img/edit.png" alt='edit'> </a>
						<?php } ?> 
						<?php if($this->session->userdata('delete')==1) { ?>
							<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $profit_center_name['id']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a>
						<?php } ?>
						<div class="dialog-err pull-right" id="dialog-message-<?php echo $profit_center_name['id']; ?>" style="display:none"></div>
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
<script type="text/javascript" src="assets/js/profit_center/profit_center_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>