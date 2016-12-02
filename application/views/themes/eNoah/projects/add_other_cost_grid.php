<table class="data-table" cellspacing="0" cellpadding="0" border="0">
	<thead>
		<tr align="left">
			<th class="header">Description</th>
			<th class="header">Cost Incurred Date</th>
			<th class="header">Attachments</th>
			<th class="header">Value</th>
			<th class="header">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php if(!empty($other_cost_data) && count($other_cost_data)>0) { ?>
			<?php foreach($other_cost_data as $row) { ?>
				<tr id="cost_<?php echo $row['id']; ?>">
					<?php $oc_attchments = getOtherCostFiles($row['id']); ?>
					<td align="left"><?php echo $row['description']; ?></td>
					<td align="left"><?php echo ($row['cost_incurred_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['cost_incurred_date'])) : '';?></td>
					<td align="center">
						<?php
							$att = '';
							if($oc_attchments==true){
								$att = "<img src='assets/img/attachment_icon.png' alt='Attachments' >";
							}
							echo $att;
						?>
					</td>
					<td align="right"><?php echo $currency_arr[$row['currency_type']].' '.number_format($row['value'], 2, '.', ',');?></td>
					<td align="left">
						<a title="Edit" onclick="editOtherCostData(<?php echo $row['id']; ?>, <?php echo $project_id?>); return false;"><img src="assets/img/edit.png" alt="edit"> </a>
						<a title="Delete" onclick="deleteOtherCostData(<?php echo $row['id']; ?>, <?php echo $project_id?>); return false;"><img src="assets/img/trash.png" alt="delete"></a>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
	</tbody>
</table>