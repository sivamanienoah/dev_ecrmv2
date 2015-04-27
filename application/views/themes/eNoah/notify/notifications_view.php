<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
//echo baseurl();
?>
<div id="content">
	<div class="inner">	
	<h2><?php echo $page_heading; ?></h2>
	<?php //if($this->session->userdata('accesspage')==1) { ?>
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th>Action</th>
				<th>Cron Name</th>
				<th>Onscreen Notification Status</th>
				<th>Email Notification Status</th>
				<th>No. of Days</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0; ?>
			<?php //echo "<pre>"; print_r($getAllCrons);  ?>
			<?php foreach($getAllCrons as $cron) { ?>
				<?php if (($cron['cron_id'] == 1) && ($viewLeads['view'] == 0)) { continue; } ?>
				<?php if (($cron['cron_id'] == 2) && ($viewTasks['view'] == 0)) { continue; } ?>
				<tr>
					<td class="actions">
						<?php if($this->session->userdata('edit')==1) { ?>
							<a href="notifications/crons_edit/update/<?php echo $cron['cron_id'] ?>" title='Edit'><img src="assets/img/edit.png" alt='edit'> </a> 
						<?php } else { echo "-"; } ?>
					</td>
					<td><?php echo $cron['cron_name']; ?></td>
					<td><?php if ($cron['onscreen_notify_status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?></td>
					<td><?php if ($cron['email_notify_status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?></td>
					<td><?php echo $cron['no_of_days']; ?></td>
				</tr>
			<?php $i++; } ?>
		</tbody>
	</table>
	<?php //} else { 
		//echo "You have no rights to access this page"; 
	//} 
	?>
	</div><!--/Inner div -->
</div><!--/Content div -->
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/notifications/notifications.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>