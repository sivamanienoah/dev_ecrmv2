<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th>Invoice Date</th>
			<th>Month & Year</th>
			<th>Customer Name</th>
			<th>Project Title</th>
			<th>Project Code</th>
			<th>Milestone Name</th>
			<th>Actual Value</th>
			<th>Status</th>
			<th>Value(<?php echo $default_currency; ?>)</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$st_array = array(0=>"Pending",1=>"Payment Completed",2=>"Payment Partially Completed");
		if (is_array($invoices) && count($invoices) > 0) { ?>
		<?php foreach($invoices as $inv) { ?>
			<tr>
				<td><a href="<?php echo base_url().'invoice/edit_invoice/'.$inv['expectid'];?>"><?php echo date('d-m-Y', strtotime($inv['invoice_generate_notify_date'])); ?></a></td>
				<td><?php echo ($inv['month_year']!='0000-00-00 00:00:00') ? date('M Y', strtotime($inv['month_year'])) : ''; ?></td>
				<td><?php echo $inv['customer']; ?></td>
				<td><a title='View' href="project/view_project/<?php echo $inv['lead_id'] ?>"><?php echo character_limiter($inv['lead_title'], 30); ?></a></td>
				<td><?php echo isset($inv['pjt_id']) ? $inv['pjt_id'] : '-'; ?></td>
				<td><?php echo $inv['project_milestone_name']; ?></td>
				<td><?php echo $inv['actual_amt']; ?></td>
				<td><?php echo $st_array[$inv['received']]; ?></td>
				<td><?php echo sprintf('%0.2f', $inv['coverted_amt']); ?></td>
				<td><a class="js_view_payment" rel="<?php echo $inv['expectid'];?>" href="javascript:void(0);">View</a></td>
			</tr>
		<?php } ?>
	<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='8' align='right'><strong>Total Value</strong></td><td><?php echo sprintf('%0.2f', $total_amt); ?></td>
		</tr>
	</tfoot>
</table>
<script type="text/javascript" src="assets/js/invoice/invoice_data-tbl.js"></script>