<p class="error-cont" style="display:none;">&nbsp;</p>
<form name="quote_item_edit_form" id="quote_item_edit_form" method="post" onsubmit="return false;">

	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
    <p>
		Item description, <span id="desc-edit-countdown"></span> characters left.<br />
		<textarea name="item_desc" class="textfield"><?php echo $item_desc ?></textarea>
    </p>
    <p>
		Item price:<br />
		<input type="text" name="item_price" value="<?php echo $item_price ?>" class="textfield required" />
		<input type="hidden" name="itemid" value="<?php echo $itemid ?>" />
    </p>
    <div class="buttons">
        <button type="submit" class="positive" onclick="processItemEdit(); return false;">Save</button>
    </div>
    <div class="buttons">
        <button type="submit" class="negative" onclick="cancelDelEdit();">Cancel</button>
    </div>
</form>
    