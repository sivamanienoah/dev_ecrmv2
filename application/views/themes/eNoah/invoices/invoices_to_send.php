<table cellspacing="0" cellpadding="0" border="0" class="data-table">
<thead>
	<tr>
		<th>Select Invoice(s)</th>
		<th>Project Name</th>
		<th>Payment Milestone</th>
		<th>Milestone Date</th>
		<th>For the Month & Year</th>
		<th>Amount</th>
		<th>Invoice Attachment(s)</th>
		</tr>
	</thead>
	<tbody>
</thead>
	<tbody>
	<?php 
	
	if (is_array($invoices) && count($invoices) > 0) { ?>
		<?php 
			foreach($invoices as $inv)   { 
				$email_address = $inv->email_1;
				if($inv->email_2) $email_address .= ','.$inv->email_2;
				if($inv->email_3) $email_address .= ','.$inv->email_3;
				if($inv->email_4) $email_address .= ','.$inv->email_4;?>
				<tr>
				<td><input class="js_invoice_checkbox" type="checkbox" name="invoice_id[]" value="<?php echo $inv->expectid;?>" /></td> 
				<td><?php echo $inv->lead_title;?></td>
				<td><?php echo $inv->project_milestone_name;?></td>
				<td><?php echo $inv->milestone_date;?></td>
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
	</tbody>
</table>
<script type="text/javascript">
$(".email_address").val('<?php echo $email_address;?>');
</script>