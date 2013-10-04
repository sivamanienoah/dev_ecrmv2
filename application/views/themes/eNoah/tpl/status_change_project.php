<?php

if ( isset($quote_data) )
{
	$js = $quote_data['complete_status'];
	if ($js == "" || $js == 'NULL') {
		$js = 0;
	}
	?>
<?php if ($chge_access == 1) { ?>
<form id="change-quote-status"<?php if (!isset($quote_data)) echo ' class="display-none"' ?>>

	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

	<h3>Adjust Project Stage <span class="small">[ current stage - <em><?php echo $quote_data['lead_stage_name']; ?></em> ]</span></h3>
	
	<select class="textfield width300px" name="job_status" id="general_convert_quote_status" style="width:298px;">
		<?php foreach ($get_lead_stage_projects as $stage) { ?>
               <option value="<?php echo  $stage['lead_stage_id'] ?>" <?php if($quote_data['job_status'] == $stage['lead_stage_id']) echo 'selected="selected"'; ?> >
					<?php echo  $stage['lead_stage_name'] ?>
			   </option>
         <?	} ?>
	</select>

	<div class="quote-invoice convert">
		<div class="buttons">
			<button type="submit" class="positive" onclick="convertProjectStatus(<?php echo $js; ?>); return false;">Set</button>
		</div>
	</div>

</form>
<?php } ?>

<?php
} // status change conditional end
?>