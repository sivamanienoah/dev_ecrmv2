<?php
// this section needs to identical to what you see on the 'welcome_view.php' view file
// only change the status if the invoice is not settled	
?>
	<?php //echo '<pre>'; print_r($quote_data); echo '</pre>'; exit;?>
<form id="change-quote-status"<?php if (!isset($quote_data)) echo ' class="display-none"' ?>>

	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

	<!--<h3>Adjust Lead Stage <span class="small">[ current stage - <em><?php #echo $cfg['job_status'][$quote_data['job_status']] ?></em> ]</span></h3>-->
	<h3>Adjust Lead Stage <span class="small">[ current stage - <em><?php echo $quote_data['lead_stage_name']; ?></em> ]</span></h3>
	<select class="textfield width300px" name="job_status" id="general_convert_quote_status" style="width:298px;">
		<?php foreach ($lead_stage as $stage) { ?>
               <option value="<?php echo  $stage['lead_stage_id'] ?>" <?php if($quote_data['job_status'] == $stage['lead_stage_id']) echo 'selected="selected"'; ?> ><?php echo  $stage['lead_stage_name'] ?></option>
			   <?php if ($stage['is_sale'] == 1 ) break;?>
         <?	} ?>
	</select>					

	<div class="quote-invoice convert">
		<div class="buttons">
			<button type="submit" class="positive" onclick="convertQuoteStatus(); return false;">Set</button>
		</div>
	</div>
</form>
<?php
?>