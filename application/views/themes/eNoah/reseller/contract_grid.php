<table class="data-table" cellspacing="0" cellpadding="0" border="0">
	<thead>
		<tr align="left">
			<th class="header">Contract Title</th>
			<th class="header">Contract Manager</th>
			<th class="header">Contract Start Date</th>
			<th class="header">Contract End Date</th>
			<th class="header">Renewal Reminder Date</th>
			<th class="header">Contract Signed Date</th>
			<th class="header">Contract Status</th>
			<th class="header">Currency</th>
			<th class="header">Tax</th>
			<th class="header">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php if(!empty($contract_data) && count($contract_data)>0) { ?>
			<?php foreach($contract_data as $row) { ?>
				<tr id="contr_<?php echo $row['id']; ?>">
					<td align="left"><?php echo $row['contract_title']; ?></td>
					<td align="left">
						<?php
							$cm_name = $row['first_name'];
							if(isset($row['last_name']) && !empty($row['last_name'])){
								$cm_name .= " ".$row['last_name'];
							}
							echo $cm_name;
						?>
					</td>
					<td align="left"><?php echo ($row['contract_start_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['contract_start_date'])) : '';?></td>
					<td align="left"><?php echo ($row['contract_end_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['contract_end_date'])) : '';?></td>
					<td align="left"><?php echo ($row['renewal_reminder_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['renewal_reminder_date'])) : '';?></td>
					<td align="left"><?php echo ($row['contract_signed_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['contract_signed_date'])) : '';?></td>
					<td align="left"><?php echo $this->contract_status[$row['contract_status']]; ?></td>
					<td align="right"><?php echo $currency_arr[$row['currency']];?></td>
					<td align="right"><?php echo $row['tax']; ?></td>
					<td align="left">
						<a title="Edit" onclick="editContractData(<?php echo $row['id']; ?>, <?php echo $row['contracter_id']; ?>); return false;"><img src="assets/img/edit.png" alt="edit"></a>
						<a title="Delete" onclick="deleteContractData(<?php echo $row['id']; ?>, <?php echo $row['contracter_id']; ?>); return false;"><img src="assets/img/trash.png" alt="delete"></a>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
			<tr><td colspan='10'> No Records Available. </td></tr>
		<?php } ?>
	</tbody>
</table>