<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th>Month & Year</th>
			<th>Customer Name</th>
			<th>Project Title</th>
			<th>Project Code</th>
 
		</tr>
	</thead>
	<tbody>
	<?php if (is_array($invoices) && count($invoices) > 0) { ?>
		<?php foreach($invoices as $inv) { ?>
			<tr>
 
				<td><?php echo $inv['project_milestone_name']; ?></td>
				<td><?php echo $inv['actual_amt']; ?></td>
				<td><?php echo sprintf('%0.2f', $inv['coverted_amt']); ?></td>
				<td><a href="<?php echo base_url().'invoice/send_invoice/'.$inv['expectid'];?>" >Generate</td>
			</tr>
		<?php } ?>
	<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='4' align='right'><strong>Total Value</strong></td><td><?php echo sprintf('%0.2f', $total_amt); ?></td>
		</tr>
	</tfoot>
</table>
<script type="text/javascript" src="assets/js/invoice/payment_milestones-tbl.js"></script>