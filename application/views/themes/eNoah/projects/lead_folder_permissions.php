<form name="form_lead_folder_permissions" id="form_lead_folder_permissions">
<input type="hidden" id="lead_id" name="lead_id" value="<?php echo $lead_id; ?>" />
<table>
	<thead>
		<tr>
			<th></th>
			<?php foreach($team_members as $member)
			{
				$initial = '';
				if($member['last_name'])
				{
					$initial = substr($member['last_name'], 0, 1);
				}
			?>
			<th><?php echo $member['first_name'].' '.$initial; ?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($lead_folders as $folder_id => $folder_name) 
		{
			if($folder_name!=$lead_id)
			{
			?>
			<tr>
				<td>
					<?php echo $folder_name; ?>
				</td>
				<?php foreach($team_members as $member) { ?>
				<td>
					<input type="radio" id="permission_for_<?php echo $folder_id.'_'.$member['userid_fk']; ?>" name="permission_for_<?php echo $folder_id.'_'.$member['userid_fk']; ?>" value="1" />R&nbsp;&nbsp;
					<input type="radio" id="permission_for_<?php echo $folder_id.'_'.$member['userid_fk']; ?>" name="permission_for_<?php echo $folder_id.'_'.$member['userid_fk']; ?>" value="2" />W&nbsp;&nbsp;
					<input type="radio" id="permission_for_<?php echo $folder_id.'_'.$member['userid_fk']; ?>" name="permission_for_<?php echo $folder_id.'_'.$member['userid_fk']; ?>" value="0" />N&nbsp;&nbsp;
				</td>
				<?php } ?>
			</tr>
			<?php
			}
		} ?>
	</tbody>
</table>

<div class="buttons"><button type="submit" class="positive" id="save_folder_permissions">Save</button></div>
<div class="buttons"><button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button></div>
</form>