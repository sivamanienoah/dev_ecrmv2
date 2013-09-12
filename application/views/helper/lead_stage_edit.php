<p class="error-cont" style="display:none;">&nbsp;</p> 
<?php if($this->session->userdata('edit')==1) { ?>
<form name="lead_stage_edit_form" id="lead_stage_edit_form" method="post" onsubmit="return false;">
 
 <input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

    <table width='100%'>
		<tr>
			<td> Lead Stage: </td>
			<td> <input type="text" name="lead_stage_name" value="<?php echo $lead_stage_name; ?>" class="textfield width200px" /> </td>
		</tr>
		<tr>
			<td> Status: </td>
			<td>
				<input type="checkbox" name="status" value="1" <?php if ($status == 1) echo 'checked="checked"' ?>
				<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>>
				<?php if ($cb_status != 0) echo "One or more leads currently assigned for this Lead Stage. This cannot be made Inactive."; ?>
				<?php if (($status == 1) && ($cb_status == 0)) echo "Uncheck if the Lead Stage need to be Inactive."; ?>
				<?php if ($status != 1) echo "Check if the Lead Stage need to be Active."; ?>
			</td>
		</tr>
		<tr>
			<td>Is sale:</td>
			<td><input type="checkbox" name="is_sale" value="1" <?php if ($is_sale == 1) echo 'checked="checked"' ?> /></td>
			<input type="hidden" name="lead_stage_id" value="<?php echo $lead_stage_id ?>" />
		</tr>
		<tr>
			<td>
				<div class="buttons">
					<button type="submit" class="positive" onclick="processLeadStgEdit(); return false;">Save</button>
				</div>
			</td>
			<td>
				<div class="buttons">
					<button type="submit" class="negative" onclick="cancelDelEdit();">Cancel</button>
				</div>	
			</td>
		</tr>
	</table>
</form>
<?php } else { echo "You have no rights to access this page"; } ?>