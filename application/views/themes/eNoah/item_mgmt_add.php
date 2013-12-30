<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || (($this->session->userdata('edit')==1) && ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
			
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
					<td colspan="2" class="action-buttons">
                        <div class="buttons">
							<button type="submit" name="update_item" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Item
							</button>
						</div>
						<div class="buttons">
                           <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>item_mgmt'">
								Cancel
							</button>
                        </div>
                    </td>
				</tr>
            </table>
		</form>
		<?php } else{
		echo "You have no rights to access this page";
		}?>
	</div>
</div>
<script type="text/javascript" src="assets/js/item_mgmt/item_mgmt_add.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>
