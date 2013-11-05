<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
	<div class="inner">	
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
	<form action="manage_service/search_currency/" method="post" id="cust_search_form">
		<input type='hidden' name='<?php echo $this->security->get_csrf_token_name(); ?>' value='<?php echo $this->security->get_csrf_hash(); ?>' />
		<table border="0" cellpadding="0" cellspacing="0" class="search-table">
			<tr>
				<td>
					Search by Currency Type
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
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>manage_service/expect_worth_cur_add'">
							Add New Currency
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
	
	<table border="0" cellpadding="0" cellspacing="0" class="tbl-data dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th width="12%">Action</th>
				<th>Currency</th>
				<th>Currency Name</th>
				<th>Status</th>
				<th>Default Currency</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($getExptWorthCur as $source) { ?>
			<tr>
				<td class="actions">
					<?php if($this->session->userdata('edit')==1) { ?>
						<a href="manage_service/expect_worth_cur_edit/update/<?php echo $source['expect_worth_id'] ?>/">Edit &raquo; </a> 
					<?php } else { echo "Edit &raquo;"; } ?> 
					<?php if(($this->session->userdata('delete')==1) && ($source['expect_worth_id'] != 1)) { ?>
						&nbsp;|&nbsp;
						<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $source['expect_worth_id'] ?>);"> Delete &raquo; </a> 
					<?php } else { echo "&nbsp;|&nbsp; Delete &raquo;"; } ?>
				</td>
				<td><?php echo $source['expect_worth_name']; ?></td>
				<td><?php echo $source['cur_name']; ?></td>
				<td>
					<?php if ($source['status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>					
				</td>
				<td>
					<?php if ($source['is_default']==1) { ?><img src="assets/img/tick.png" alt="Default Currency" style="width:14px; height:14px" /> <?php } ?>
					<div class="dialog-err pull-right" id="dialog-message-<?php echo $source['expect_worth_id'] ?>" style="display:none"></div>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php } else { echo "You have no rights to access this page"; } ?>
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<script type="text/javascript" src="assets/js/manage_service/manage_expect_worth_cur.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>