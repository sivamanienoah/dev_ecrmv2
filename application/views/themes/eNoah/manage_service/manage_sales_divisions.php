<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
//echo baseurl();
?>

<div id="content">
	<div class="inner">
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
	<form action="manage_service/search_sales/" method="post" id="cust_search_form">
	
		<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	
		<table border="0" cellpadding="0" cellspacing="0" class="search-table">
			<tr>
				<td>
					Search by Division Name
				</td>
				<td>
					<input type="text" name="cust_search" value="<?php echo $this->uri->segment(4) ?>" class="textfield width200px" />
				</td>
				<td>
					<div class="buttons">
						<button type="submit" class="positive">
							Search
						</button>
					</div>
				</td>
				<?php if($this->session->userdata('add')==1) { ?>
				<td valign="middle";>
					<div class="buttons">
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>manage_service/division_add'">
							Add New Division
						</button>
					</div>
				</td>
				<?php } ?>
				<?php if ($this->uri->segment(4)) { ?>
				<td>
					<div class="buttons">
						<button type="submit" name="cancel_submit" class="negative">
							Cancel
						</button>
					</div>
				</td>
				<?php } ?>
			</tr>
		</table>
	</form>
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th width="12%">Action</th>
				<th width="38%">Divisions</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php if (is_array($sales_divisions) && count($sales_divisions) > 0) { ?>
			<?php foreach($sales_divisions as $sales) { ?>
				<tr>
					<td class="actions">
						<?php if($this->session->userdata('edit')==1) { ?>
							<a href="manage_service/division_add/update/<?php echo $sales['div_id']; ?>/">Edit &raquo;</a> 
						<?php } else { echo "Edit"; } ?> 
						<?php if($this->session->userdata('delete')==1) { ?>
							&nbsp;|&nbsp;
							<!--<a class="delete" href="manage_service/division_delete/update/<?php echo $sales['div_id']; ?>" onclick="return confirm('Are you sure you want to delete?')"> Delete &raquo; </a>-->
							<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $sales['div_id']; ?>);"> Delete &raquo; </a> 
						<?php } ?>
					</td>
					<td><?php echo $sales['division_name']; ?></td>
					<td>
						<?php if ($sales['status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>
						<div class="dialog-err pull-right" id="dialog-message-<?php echo $sales['div_id'] ?>" style="display:none"></div>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
		</tbody>
	</table>
	<?php } else { echo "You have no rights to access this page"; } ?>
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<script type="text/javascript" src="assets/js/manage_service/manage_sales_divisions.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>