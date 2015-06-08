<table cellspacing="0" cellpadding="0" border="0" class="data-table">
<thead>
	<tr>
		<th>Project Name</th>
		<th>Payment Milestone</th>
		<th>Milestone Date</th>
		<th>For the Month & Year</th>
		<th>Amount</th>
		<th>Attachment(s)</th>
		</tr>
	</thead>
	<tbody>
</thead>
	<tbody>
	<?php 
	//echo '<pre>';print_r($invoices);exit;
	if (is_array($invoices) && count($invoices) > 0) { ?>
		<?php 
			foreach($invoices as $inv)   { ?>
				<tr>
				<td><?php echo $inv->lead_title;?></td>
				<td><?php echo $inv->project_milestone_name;?></td>
				<td><?php echo date("d-m-Y",strtotime($inv->expected_date));?></td>
				<td><?php echo date("F Y",strtotime($inv->month_year));?></td>
				<td><?php echo $inv->expect_worth_name.' '.$inv->total_amount;?></td>
				<td>
					<?php
					$attachments = get_attachments_show($inv->expectid);
					echo $attachments;
					?>
				</td>
				</tr>
		<?php }   ?>
	<?php } ?>
<tr>
	<td colspan="6" align="left"><button type="button" class="js_close positive">Close</button></td></tr>
	</tr>	
	</tbody>
</table>

