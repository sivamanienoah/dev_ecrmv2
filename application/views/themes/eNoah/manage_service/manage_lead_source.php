<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
//echo baseurl();
?>
<div id="content">
	<div class="inner">	
	<?php if($this->session->userdata('accesspage')==1) { ?>
	
	<div class="page-title-head">
		<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
		<?php if($this->session->userdata('add')==1) { ?>
			<div class="section-right">
				<div class="buttons add-new-button">
					<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>manage_service/ls_add'">
						Add New Lead Source
					</button>
				</div>
			</div>
		<?php } ?>
		<div class="clearfix"></div>
	</div>
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th width="30%">Sources</th>
				<th width="12%">Status</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		<?php if (is_array($get_lead_source) && count($get_lead_source) > 0) { ?>
			<?php foreach($get_lead_source as $source) { ?>
				<tr>
					<td><?php echo $source['lead_source_name']; ?></td>
					<td>
						<?php if ($source['status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>
					</td>
					<td class="actions">
						<?php if($this->session->userdata('edit')==1) { ?>
							<a href="manage_service/ls_add/update/<?php echo $source['lead_source_id'] ?>" title='Edit'><img src="assets/img/edit.png" alt='edit'> </a> 
						<?php } ?> 
						<?php if($this->session->userdata('delete')==1) { ?>
							<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $source['lead_source_id'] ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a> 
						<?php } ?>
						<?php if(($this->session->userdata('delete')!=1) && ($this->session->userdata('edit')!=1)) echo '-'; ?>
						<div class="dialog-err pull-right" id="dialog-message-<?php echo $source['lead_source_id'] ?>" style="display:none"></div>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
		</tbody>
	</table>
	<?php } else { echo "You have no rights to access this page"; } ?>
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/manage_service/manage_lead_source.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>