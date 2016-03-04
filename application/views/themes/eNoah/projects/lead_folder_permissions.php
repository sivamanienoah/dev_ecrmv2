<form name="form_lead_folder_permissions" id="form_lead_folder_permissions">
<input type="hidden" id="lead_id" name="lead_id" value="<?php echo $lead_id; ?>" />
<?php //echo "<pre>"; print_r($lead_folders); exit; 
	$access_rt = array();
	if(!empty($folders_access) && (count($folders_access)>0)){
		foreach($folders_access as $rec)
		$access_rt[$rec['folder_id']][$rec['user_id']] = $rec['access_type'];
	}
?>
<table class="folder-permission-content">
	<thead>
		<tr>
			<th style=""></th>
			<?php
			foreach($team_members as $member)
			{
				$initial = '';
				if($member['last_name'])
				{
					$initial = substr($member['last_name'], 0, 1);
				}
			?>
			<th>
				<?php echo $member['first_name'].' '.$initial; ?>
				<table>
					<tr><td>
						<label><input type="checkbox" id="rd-read" class='all-chk' name="all_read" value="<?=$member['userid_fk']?>" />R&nbsp;&nbsp;</label>
						<label><input type="checkbox" id="rd-write" class='all-chk' name="all_write" value="<?=$member['userid_fk']?>" />W&nbsp;&nbsp;</label>
						<label><input type="checkbox" id="rd-none" class='all-chk' name="all_none" value="<?=$member['userid_fk']?>" />N&nbsp;&nbsp;</label>
					</td></tr>
				</table>
			</th>
			<?php 
			}
			?>
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
					<?php
						$rd_name      = 'permission_for_'.$folder_id.'_'.$member['userid_fk'];
						$read_checked = $write_checked = $none_checked = "";
						if(isset($folders_access) && !empty($folders_access)) {
							$stat = isset($access_rt[$folder_id][$member['userid_fk']]) ? $access_rt[$folder_id][$member['userid_fk']] : 0;
							switch($stat) {
								case 1:
									$read_checked  = 'checked="checked"';
								break;
								case 2:
									$write_checked = 'checked="checked"';
								break;
								case 0:
									$none_checked  = 'checked="checked"';
								break;
								default:
									$none_checked  = 'checked="checked"';
							}							
						} else {
							$none_checked  = 'checked="checked"';
						}
					?>
				<td>
					<input type="radio" id="<?=$rd_name?>" name="<?=$rd_name?>" class="<?php echo 'rd-read-'.$member['userid_fk']?>" value="1" <?=$read_checked?> />R&nbsp;&nbsp;
					<input type="radio" id="<?=$rd_name?>" name="<?=$rd_name?>" class="<?php echo 'rd-write-'.$member['userid_fk']?>" value="2" <?=$write_checked?> />W&nbsp;&nbsp;
					<input type="radio" id="<?=$rd_name?>" name="<?=$rd_name?>" class="<?php echo 'rd-none-'.$member['userid_fk']?>" value="0" <?=$none_checked?> />N&nbsp;&nbsp;
				</td>
				<?php } ?>
			</tr>
			<?php
			}
		} ?>
	</tbody>
</table>

<div class="buttons"><button type="submit" class="positive" id="save_folder_permissions">Save</button></div>
<div class="buttons"><button class="negative" onclick="$.unblockUI();">Cancel</button></div>
</form>
<script>
/* $( ".folder-permission-content" ).scroll(function() {  
		$(t).find('thead').addClass('.pos-fixed');
	if($(".folder-permission-content").scroll > 20) {
		console.log('true');
			$(this).find('thead').css({"position":"fixed", "padding-left":"150px" });
	}
	else {
		console.log('false');
	}
}); */
</script>