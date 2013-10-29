<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
	<div class="inner">
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
	
	<form action="manage_service/search/" method="post" id="cust_search_form">
	
		<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	
		<table border="0" cellpadding="0" cellspacing="0" class="search-table">
			<tr>
				<td>
					Search by Product Name
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
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>manage_service/ser_add'">
							Add New Product
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
	<table border="0" cellpadding="0" cellspacing="0" class="data-table">
		<thead>
			<tr>
				<th width="12%">Action</th>
				<th width="40%">Product</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($job_categories as $jobs) { ?>
			<tr>
				<td class="actions">
					<?php if($this->session->userdata('edit')==1) { ?>
						<a href="manage_service/ser_add/update/<?php echo $jobs['cid']; ?>/">Edit &raquo; </a> 
					<?php } else { echo "Edit"; } ?> 
					<?php if($this->session->userdata('delete')==1) { ?>
						&nbsp;|&nbsp;
						<!--<a class="delete" href="manage_service/ser_delete/update/<?php echo $jobs['cid']; ?>" onclick="return confirm('Are you sure you want to delete?')"> Delete &raquo; </a>-->
						<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $jobs['cid']; ?>);"> Delete &raquo; </a>
					<?php } ?>
				</td>
				<td><?php echo $jobs['category']; ?></td>
				<td>
					<?php if ($jobs['status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>
					<div class="dialog-err pull-right" id="dialog-message-<?php echo $jobs['cid']; ?>" style="display:none"></div>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<p><?php echo '&nbsp;'; ?></p>
	<div id="pager">
	<a class="first"> First </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
	<a class="prev"> &laquo; Prev </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
	<input type="text" size="2" class="pagedisplay"/><?php echo '&nbsp;&nbsp;&nbsp;'; ?> <!-- this can be any element, including an input --> 
	<a class="next"> Next &raquo; </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
	<a class="last"> Last </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
	<span>No. of Records per page:<?php echo '&nbsp;'; ?> </span>
	<select class="pagesize"> 
		<option selected="selected" value="10">10</option> 
		<option value="20">20</option> 
		<option value="30">30</option> 
		<option value="40">40</option> 
	</select> 
	</div>
	<?php } else { echo "You have no rights to access this page"; } ?>
	</div><!--Inner div - close here -->
</div><!--Content div - close here -->
<script type="text/javascript" src="assets/js/manage_service/manage_service_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>