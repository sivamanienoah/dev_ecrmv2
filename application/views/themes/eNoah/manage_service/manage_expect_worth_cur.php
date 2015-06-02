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
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>manage_service/expect_worth_cur_add'">
							Add New Currency
						</button>
					</div>
				
					<!--div class="buttons update-currency-value">
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>manage_service/updt_cur_from_live'">
							Update Currency Values
						</button>
					</div-->
					
					<div class="buttons update-currency-value">
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>manage_service/updt_bk_currency'">
							Update Book Keeping Currency
						</button>
					</div>
			</div>
			<?php } ?>
			<div class="clearfix"></div>
	</div>
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th width="10%">Currency</th>
				<th width="18%">Currency Name</th>
				<th width="8%">Status</th>
				<th width="12%">Default Currency</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		<?php if (is_array($getExptWorthCur) && count($getExptWorthCur) > 0) { ?>
			<?php foreach($getExptWorthCur as $source) { ?>
				<tr>
					<td><?php echo $source['expect_worth_name']; ?></td>
					<td><?php echo $source['cur_name']; ?></td>
					<td>
						<?php if ($source['status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>					
					</td>
					<td>
						<?php if ($source['is_default']==1) { ?><img src="assets/img/tick.png" alt="Default Currency" style="width:14px; height:14px" /> <?php } ?>
					</td>
					<td class="actions">
						<?php if($this->session->userdata('edit')==1) { ?>
							<a href="manage_service/expect_worth_cur_edit/update/<?php echo $source['expect_worth_id'] ?>" title='edit'><img src="assets/img/edit.png" alt='edit'></a> 
						<?php } ?> 
						<?php if(($this->session->userdata('delete')==1) && ($source['is_default'] != 1) && ($source['expect_worth_id']!=1)) { ?>
							<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $source['expect_worth_id'] ?>);" title='delete'> <img src="assets/img/trash.png" alt='delete'> </a> 
						<?php } ?>
						<?php if(($this->session->userdata('delete')!=1) && ($this->session->userdata('edit')!=1)) echo '-'; ?>
						<div class="dialog-err pull-right" id="dialog-message-<?php echo $source['expect_worth_id'] ?>" style="display:none"></div>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
		</tbody>
	</table>
	<?php } else { echo "You have no rights to access this page"; } ?>
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/manage_service/manage_expect_worth_cur.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>