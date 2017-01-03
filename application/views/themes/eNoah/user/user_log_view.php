<div style="width:100%;">
	<div class="file-tabs-close-confirm-tab"></div>
	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

	<table border="0" cellpadding="0" cellspacing="0" id="user_log_table" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th>Username</th>
				<th>Role Name</th>
				<th>Skill</th>
				<th>Department</th>
				<th>Status</th>
				<th>Created On</th>
			</tr>
		</thead>
		<tbody>
			<?php if (is_array($log_data) && count($log_data) > 0) { ?>
				<?php foreach($log_data as $lg) { ?>
					<tr>
						<td><?php echo $lg['username']; ?></td>
						<td><?php echo $lg['name']; ?></td>
						<td><?php echo $lg['skill_name']; ?></td>
						<td><?php echo $lg['department_name']; ?></td>
						<td><?php echo ($lg['active'] == 0) ? 'Active' : 'Inactive'; ?></td>
						<td><?php echo date('d-m-Y h:m:s', strtotime($lg['created_on'])); ?></td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>

<script>
$(function() {
	$( ".file-tabs-close-confirm-tab" ).on( "click", function() {
		$.unblockUI();
		return false;
	});
	
	$('#user_log_table').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,
		"bDestroy": true,
		"bLengthChange": false
	});
});
</script>