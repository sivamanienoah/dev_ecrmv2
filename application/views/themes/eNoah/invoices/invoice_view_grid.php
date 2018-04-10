<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th>Invoice Date</th>
			<th>Month & Year</th>
			<th>Entity</th>
			<th>Practice</th>
			<th>Customer Name</th>
			<th>Project Title</th>
			<th>Project Code</th>
			<th>Milestone Name</th>
			<th>Actual Value</th>
			<th>Entity Book Value</th>
			<th>Value(<?php echo $default_currency; ?>)</th>
			<!--th>Status</th>
			<th>Action</th-->
		</tr>
	</thead>
	<tbody>
	<?php 
		$st_array = array(0=>"Pending",1=>"Payment Completed",2=>"Payment Partially Completed");
		if (is_array($invoices) && count($invoices) > 0) { ?>
		<?php foreach($invoices as $inv) { ?>
			<tr>
				<td><?php echo date('d-m-Y', strtotime($inv['invoice_generate_notify_date'])); ?></td>
				<td><?php echo ($inv['month_year']!='0000-00-00 00:00:00') ? date('M Y', strtotime($inv['month_year'])) : ''; ?></td>
				<td><?php echo $inv['division_name']; ?></td>
				<td><?php echo $inv['practices']; ?></td>
				<td><?php echo $inv['customer']; ?></td>
				<td><a title='View' href="project/view_project/<?php echo $inv['lead_id'] ?>"><?php echo character_limiter($inv['lead_title'], 30); ?></a></td>
				<td><?php echo isset($inv['pjt_id']) ? $inv['pjt_id'] : '-'; ?></td>
				<td><?php echo $inv['project_milestone_name']; ?></td>
				<td align="right"><?php echo $inv['actual_amt']; ?></td>
				<td align="right"><?php echo $currency_names[$inv['entity_conversion_name']] .' '. sprintf('%0.2f', $inv['entity_conversion_value']); ?></td>
				<td align="right"><?php echo sprintf('%0.2f', $inv['coverted_amt']); ?></td>
				<!--td><?php #echo $st_array[$inv['received']]; ?></td>
				<td><a class="js_view_payment" rel="<?php #echo $inv['expectid'];?>" href="javascript:void(0);">View</a></td-->
			</tr>
		<?php } ?>
	<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='10' align='right'><strong>Total Value</strong></td><td align='right'><?php echo sprintf('%0.2f', $total_amt); ?></td>
		</tr>
	</tfoot>
</table>
<script type="text/javascript" src="assets/js/invoice/invoice_data-tbl.js"></script>