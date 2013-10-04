<div class="recurring-item discount parent-id-<?php echo $parent_id; ?>" rel="<?php echo $recurringitemid; ?>">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr valign="top">
			<td width="680"><?php echo $desc; ?></td>
			<td align="right">
				<span class="price">$<?php echo $price; ?>/<?php echo $period; ?></span>
				<?php echo (!empty($cycles_remaining) && $cycles_remaining > 0 ? '<br />' . $cycles_remaining . ' cycles left' : ''); ?>
				<br /><em>Ledger Code: <?php echo $category; ?></em>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align="right">
				<a href="javascript:void(0)" class="btn edit" rel="<?php echo $recurringitemid; ?>">Edit</a>
				<a href="javascript:void(0)" class="btn delete" rel="<?php echo $recurringitemid; ?>">Delete</a>
			</td>
		</tr>
	</table>
</div>