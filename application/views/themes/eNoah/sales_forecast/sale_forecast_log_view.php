<div style="width:100%;">
	<div class="file-tabs-close-confirm-tab"></div>
	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

	<table border="0" cellpadding="0" cellspacing="0" id="ms_log_table" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th>Milestone Name</th>
				<th>Milestone Value</th>
				<th>For Month & Year</th>
				<th>Modified By</th>
				<th>Modified On</th>
			</tr>
		</thead>
		<tbody>
			<?php if (is_array($log_data) && count($log_data) > 0) { ?>
				<?php foreach($log_data as $lg) { ?>
					<tr>
						<td><?php echo $lg['milestone_name']; ?></td>
						<td><?php echo $lg['milestone_value']; ?></td>
						<td>
							<?php if($lg['for_month_year']!='0000-00-00 00:00:00') 
								  echo date('F Y', strtotime($lg['for_month_year']));
								  else
								  echo "-";
							?>
						</td>
						<td><?php echo $lg['first_name'].' '.$lg['last_name']; ?></td>
						<td><?php echo date('d-m-Y h:m:s', strtotime($lg['modified_on'])); ?></td>
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
	
	$('#ms_log_table').dataTable({
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