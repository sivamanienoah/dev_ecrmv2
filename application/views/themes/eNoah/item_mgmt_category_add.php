<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || (($this->session->userdata('edit')==1) && ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" name="formone" onsubmit="return valid();">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Category Details</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Category Name: * </td>
					<td>
						<input type="text" id="cat_name" name="cat_name" value="<?php echo  $this->validation->cat_name ?>" class="textfield width200px" />
					</td>
					<?php if ($this->uri->segment(3) == 'update') { ?>
					<input type="hidden" id="cat_updt" name="cat_updt" value="<?php echo $this->uri->segment(4); ?>" />
					<?php } ?>
					<td colspan="2" align="right"><span class="errmsg" style="color:red;font-size:12px;">Category Name Already Exists.</span> </td>
				</tr>
				<tr> </tr>
                <tr>
					<td>&nbsp;</td>
					<td class="action-buttons">
                        <div class="buttons">
							<button type="submit" name="update_item" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Item
							</button>
						</div>
                    </td>
                    <td colspan="2" class="action-buttons">
						<!--
                        <?php if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && $userdata['level'] < 2) { ?>
                        <div class="buttons">
                            <button type="submit" name="delete_item" class="negative" onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
                                Delete Item
                            </button>
                        </div>
                        <?php } else { echo "&nbsp;"; } ?>
						-->
                    </td>
				</tr>
            </table>
		</form>
		<?php } else {
	echo "You have no rights to access this page";
}?>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>
<script type="text/javascript" src="assets/js/item_mgmt/item_mgmt_category_add.js"></script>