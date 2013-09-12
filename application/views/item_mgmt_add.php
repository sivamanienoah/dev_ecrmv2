<?php require ('tpl/header.php'); ?>

<div id="content">
    <?php //include 'tpl/item_mgmt_submenu.php' ?>
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || (($this->session->userdata('edit')==1) && ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkForm();">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Item Details</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Item Price: *</td>
					<td><input type="text" name="item_price" value="<?php echo  $this->validation->item_price ?>" onkeypress="return isNumberKey(event)" class="textfield width200px" /><span style="color:red;">(Numeric only)</span></td>
					<td style="padding-left:10px;">Item Category:</td>
					<td>
						<?php
						if (isset($categories))
						{
							?>
							<select name="item_type" class="textfield width200px" id="item_type">
								<?php
								foreach ($categories as $category)
								{
									?>
									<option value="<?php echo $category['cat_id'] ?>" <?php echo  ($this->validation->item_type == $category['cat_id']) ? 'selected="selected"' : '' ?>><?php echo $category['cat_name'] ?></option>
									<?php
								}
								?>
							</select>
							<?php
						}
						?>
					</td>
				</tr>
                <tr>
					<td>Item Details: *</td>
					<td colspan="3"><span id="desc-countdown">600</span></strong> characters left.</label><br />
					<textarea name="item_desc" id="add-item-desc" class="textfield width545px" rows="6">
<?php
echo $this->validation->item_desc
?></textarea>
					</td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td class="action-buttons">
                        <div class="buttons">
							<button type="submit" name="update_item" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Item
							</button>
						</div>
                    </td>
                    <!--<td colspan="2" class="action-buttons">
                        <?php # if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && $userdata['level'] < 2) { ?>
                        <div class="buttons">
                            <button type="submit" name="delete_item" class="negative" onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
                                Delete Item
                            </button>
                        </div>
                        <?php # } else { echo "&nbsp;"; } ?>
                    </td>-->
				</tr>
            </table>
		</form>
		<?php } else{
		echo "You have no rights to access this page";
		}?>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$('#add-item-desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 600) {
			$(this).val(desc_len.substring(0, 600));
		}
		
		var remain_len = 600 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		$('#desc-countdown').text(remain_len);
	});
	$('#add-item-desc').keyup();
	if (parseInt($('#desc-countdown').text()) == 0) {
		//$('td.action-buttons .buttons').remove();
		//$('td.action-buttons:first').html('<p>Update buttons removed due to extended text being trimmed. Contact the developer if you need to edit this item.</p>');
	}
});
 function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : event.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
</script>
<?php require ('tpl/footer.php'); ?>
