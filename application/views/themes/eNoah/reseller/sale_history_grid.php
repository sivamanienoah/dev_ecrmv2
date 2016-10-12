<div class="page-title-head">
	<h2 class="pull-left borderBtm">Sales History</h2>
</div>
<table class="data-table" cellspacing="0" cellpadding="0" border="0">
	<thead>
		<tr align="left">
			<th class="header">Project Name</th>
			<th class="header">Customer & Contact Name</th>
			<th class="header">Project Value(<?php echo $this->default_cur_name; ?>)</th>
			<th class="header">Sale Date</th>
			<th class="header">Project Converted By</th>
			<th class="header">Project Status</th>
		</tr>
	</thead>
	<tbody>
		<?php if(!empty($sales) && count($sales)>0) { ?>
			<?php foreach($sales as $row) { ?>
				<tr id="contr_<?php echo $row['lead_id']; ?>">
					<td align="left"><?php echo $row['project_name']; ?></td>
					<td align="left"><?php echo $row['company_name']." - ".$row['customer_contact_name']; ?></td>
					<td align="right"><?php echo (isset($row['converted_amount']) && !empty($row['converted_amount'])) ? sprintf('%0.2f', $row['converted_amount']) : '0'; ?></td>
					<td align="left"><?php echo ($row['sale_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['sale_date'])) : '';?></td>
					<td align="left"><?php echo ucfirst($row['sale_by']); ?></td>
					<td align="left">
						<?php 
							switch($row['pjt_status']) {
								case 1:
									echo "<span class=''>".$this->pjt_status['1']."</span>";
								break;
								case 2:
									echo "<span class=''>".$this->pjt_status['2']."</span>";
								break;
								case 3:
									echo "<span class=''>".$this->pjt_status['3']."</span>";
								break;
								default:
									echo "<span class=''>".$this->lead_status['4']."</span>";
							}
						?>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
			<tr><td colspan='10'> No Records Available. </td></tr>
		<?php } ?>
	</tbody>
</table>