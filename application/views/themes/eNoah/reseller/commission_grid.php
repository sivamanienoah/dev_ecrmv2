<table class="data-table" cellspacing="0" cellpadding="0" border="0">
	<thead>
		<tr align="left">
			<th class="header">Title</th>
			<th class="header">Project Name</th>
			<th class="header">Payment Advice Date</th>
			<th class="header">Milestone Name</th>
			<th class="header">For The Month Year</th>
			<th class="header">Currency</th>
			<th class="header">Value</th>
			<th class="header">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php if(!empty($commission_data) && count($commission_data)>0) { ?>
			<?php foreach($commission_data as $cmsn_row) { ?>
				<tr id="csmn_<?php echo $cmsn_row['id']; ?>">
					<td align="left"><?php echo $cmsn_row['commission_title']; ?></td>
					<td align="left"><?php echo $cmsn_row['lead_title']; ?></td>
					<td align="left"><?php echo ($cmsn_row['payment_advice_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($cmsn_row['payment_advice_date'])) : '';?></td>
					<td align="left"><?php echo $cmsn_row['commission_milestone_name'];?></td>
					<td align="left"><?php echo ($cmsn_row['for_the_month_year']!='0000-00-00 00:00:00') ? date('M Y', strtotime($cmsn_row['for_the_month_year'])) : '';?></td>
					<td align="right"><?php echo $currency_arr[$cmsn_row['commission_currency']];?></td>
					<td align="left"><?php echo $cmsn_row['commission_value']; ?></td>
					<td align="left">
						<a title="Edit" onclick="editCommissionData(<?php echo $cmsn_row['id']; ?>, <?php echo $cmsn_row['contracter_id']; ?>); return false;"><img src="assets/img/edit.png" alt="edit"></a>
						<a title="Delete" onclick="deleteCommissionData(<?php echo $cmsn_row['id']; ?>, <?php echo $cmsn_row['contracter_id']; ?>); return false;"><img src="assets/img/trash.png" alt="delete"></a>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
			<tr><td colspan='8'> No Records Available. </td></tr>
		<?php } ?>
	</tbody>
</table>