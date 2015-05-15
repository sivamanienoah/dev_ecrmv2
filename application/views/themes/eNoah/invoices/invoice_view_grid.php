<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th>Invoice Date</th>
			<th>For the Month & Year</th>
			<th>Customer Name</th>
			<th>Project Title</th>
			<th>Project Code</th>
			<th>Milestone Name</th>
			<th>Actual Value</th>
			<th>Value(<?php echo $default_currency; ?>)</th>
		</tr>
	</thead>
	<tbody>
	<?php if (is_array($invoices) && count($invoices) > 0) { ?>
		<?php foreach($invoices as $inv) { ?>
			<tr>
				<td><?php echo date('d-m-Y', strtotime($inv['invoice_generate_notify_date'])); ?></td>
				<td><?php echo ($inv['month_year']!='0000-00-00 00:00:00') ? date('F Y', strtotime($inv['month_year'])) : ''; ?></td>
				<td><?php echo $inv['customer']; ?></td>
				<td><a title='View' href="project/view_project/<?php echo $inv['lead_id'] ?>"><?php echo character_limiter($inv['lead_title'], 30); ?></a></td>
				<td><?php echo isset($inv['pjt_id']) ? $inv['pjt_id'] : '-'; ?></td>
				<td><?php echo $inv['project_milestone_name']; ?></td>
				<td><?php echo $inv['actual_amt']; ?></td>
				<td><?php echo sprintf('%0.2f', $inv['coverted_amt']); ?></td>
			</tr>
		<?php } ?>
	<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='7' align='right'><strong>Total Value</strong></td><td><?php echo sprintf('%0.2f', $total_amt); ?></td>
		</tr>
	</tfoot>
</table>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>