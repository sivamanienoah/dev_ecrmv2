<div style="display: none;">
	<form class="add-recurring-item">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	
	    <input type="hidden" name="cust_id" value="<?php echo $cust_id; ?>" id="recurring_hidden_cust_id" />
	    <input type="hidden" name="recurringitemid" value="<?php echo $recurringitemid; ?>" id="recurring_hidden_recurringitemid" />
	    <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>" id="recurring_hidden_parent_id" />
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                <p><label>Item description, <strong><span id="desc-countdown-recurring">600</span></strong> characters left.</label></p>
                <p><textarea name="recurring_item_desc" id="recurring_item_desc" class="textfield width99pct" style="height: 50px"><?php echo $desc; ?></textarea></p>
            </td>
        </tr>
		<tr>
			<td>
                <p><label>Category</label></p>
                <p>
					<select name="recurring_category" id="recurring_category" style="width: 277px;">
						<option value="4-1005"<?php echo ($category == '4-1005' ? ' selected="selected"' : ''); ?>>One Page Real Estate Website</option>
						<option value="4-1010"<?php echo ($category == '4-1010' ? ' selected="selected"' : ''); ?>>AgentPRO (Desktop/Mobile)</option>
						<option value="4-1015"<?php echo ($category == '4-1015' ? ' selected="selected"' : ''); ?>>Pr/Un RE Website (inc. Mobile)</option>
						<option value="4-1020"<?php echo ($category == '4-1020' ? ' selected="selected"' : ''); ?>>V1 Pr/Un Website</option>
						<option value="4-1025"<?php echo ($category == '4-1025' ? ' selected="selected"' : ''); ?>>V2 Pr/Un Website</option>
						<option value="4-1030"<?php echo ($category == '4-1030' ? ' selected="selected"' : ''); ?>>V3 Pr/Un Website Shared SSL</option>
						<option value="4-1035"<?php echo ($category == '4-1035' ? ' selected="selected"' : ''); ?>>Dedicated SSL</option>
						<option value="4-1040"<?php echo ($category == '4-1040' ? ' selected="selected"' : ''); ?>>Webflow Intranet</option>
						<option value="4-1045"<?php echo ($category == '4-1045' ? ' selected="selected"' : ''); ?>>NewsletterPRO</option>
						<option value="4-1050"<?php echo ($category == '4-1050' ? ' selected="selected"' : ''); ?>>Loyalty Manager Shared SSL</option>
						<option value="4-1055"<?php echo ($category == '4-1055' ? ' selected="selected"' : ''); ?>>iPad Dtl Listing Kit PerAgent</option>
						<option value="4-1060"<?php echo ($category == '4-1060' ? ' selected="selected"' : ''); ?>>SEO</option>
						<option value="4-1065"<?php echo ($category == '4-1065' ? ' selected="selected"' : ''); ?>>Domain Names</option>
						<option value="4-1070"<?php echo ($category == '4-1070' ? ' selected="selected"' : ''); ?>>General Hosting</option>
					</select>
				</p>
            </td>
		</tr>
        <tr>
            <td>
                <p><label>Item price</label></p>
                <p>
					<input type="text" name="recurring_item_price" id="recurring_item_price" class="textfield width80px" value="<?php echo $price; ?>" /> per 
					<select name="recurring_item_period" id="recurring_item_period">
						<option value="month"<?php echo ($period == 'month' ? ' selected="selected"' : ''); ?>>Month</option>
						<option value="3 months"<?php echo ($period == '3 months' ? ' selected="selected"' : ''); ?>>3 Months</option>
						<option value="year"<?php echo ($period == 'year' ? ' selected="selected"' : ''); ?>>Year</option>
						<option value="2 years"<?php echo ($period == '2 years' ? ' selected="selected"' : ''); ?>>2 Years</option>
					</select>
				</p>
            </td>
        </tr>
        <tr>
            <td>
                <p><label>Repeat For</label></p>
                <p>
									<input type="text" name="recurring_item_cycles_remaining" id="recurring_item_price" class="textfield width80px" value="<?php echo $cycles_remaining; ?>" /> cycles (leave blank for permanent items)
								</p>
            </td>
        </tr>
    </table>
    <div class="buttons" style="overflow: hidden;">
        <button type="submit" class="positive" id="submit-recurring-item">Done</button>
        <button type="submit" class="negative" id="cancel-recurring-item">Cancel</button>
    </div>
    <div class="buttons" style="margin-top: 4px; overflow: hidden;">
      <button type="submit" class="" id="add-to-recurring-library">Save to Library</button>
      <button type="submit" class="" id="open-prefill-window">Prefill</button>
    </div>
	</form>
</div>