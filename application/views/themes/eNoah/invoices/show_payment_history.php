<table cellspacing="0" cellpadding="0" border="0" class="data-table">
<thead>
	<tr>
		<th>Date</th>
		<th>Link</th>
		<th>Amount</th>
		<th>Card Number</th>
		<th>Paid Status</th>
		<th>Transaction ID</th>
		<th>Transaction Date</th>
		<th>Transaction Method</th>
		<th>Transaction Message</th>
		</tr>
	</thead>
	<tbody>
</thead>
	<tbody>
	<?php 
	//echo '<pre>';print_r($invoices);exit;
	$paid_status = array(0=>"Failed",1 => "Success");
	$payment_method = array(1=>"Paypal",2 => "Authorize.net");
	if (is_array($payments) && count($payments) > 0) { ?>
		<?php 
			foreach($payments as $his)   { ?>
				<tr>
				<td><?php echo date("d-m-Y",strtotime($his->created_date));?></td>
				<td><?php echo $his->unique_link;?></td>
				<td><?php echo $his->paid_amount;?></td>
				<td><?php echo $his->card_number;?></td>
				<td><?php echo $paid_status[$his->paid_status];?></td>
				<td><?php echo $his->transaction_id;?></td>
				<td><?php echo date("d-m-Y",strtotime($his->transaction_date));?></td>
				<td><?php echo $payment_method[$his->transaction_method];?></td>
				<td><?php echo $his->transaction_message;?></td>
				</tr>
		<?php }   ?>
	<?php } ?>
<tr>
	<td colspan="6" align="left"><button type="button" class="js_close positive">Close</button></td></tr>
	</tr>	
	</tbody>
</table>

