<div class="recurring-item" rel="<?php echo $recurringitemid; ?>">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr valign="top">
			<td width="700"><?php echo $desc; ?></td>
			<td align="right">
				<span class="price">$<?php echo $price; ?>/<?php echo $period; ?></span>
				<?php echo (!empty($cycles_remaining) && $cycles_remaining > 0 ? '<br />' . $cycles_remaining . ' cycles left' : ''); ?>
				<br /><em>Ledger Code: <?php echo $category; ?></em>
			</td>
		</tr>
		<tr>
			<td>
				<a href="javascript:void(0)" class="btn edit" rel="<?php echo $recurringitemid; ?>">Edit</a>
			<a href="javascript:void(0)" class="btn delete" rel="<?php echo $recurringitemid; ?>">Delete</a>
			</td>
			<td align="right"><a href="javascript:void(0)" class="btn discount" rel="<?php echo $recurringitemid; ?>">Add Adjustment</a></td>
		</tr>
	</table>
</div>