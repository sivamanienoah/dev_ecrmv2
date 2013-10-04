<?php require ('tpl/header.php'); ?>

<div id="content">
    <div id="left-menu">
        <a href="customers/category">Categories</a>
	</div>
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || (($this->session->userdata('edit')==1) && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkForm();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Category</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="100">Category name:</td>
					<td width="240"><input type="text" name="category_name" value="<?php echo  $this->validation->category_name ?>" class="textfield width200px required" /> *</td>
					<td width="100">Description:</td>
					<td width="240"><input type="text" name="cat_comments" value="<?php echo  $this->validation->cat_comments ?>" class="textfield width200px required" /></td>
				</tr>
				
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_category" class="positive">
								
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Category
							</button>
						</div>
                    </td>
                    <!--<td colspan="2">
                        <?php if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && $userdata['level'] < 2) { ?>
						<?php if($this->session->userdata('delete')==1) { ?>
                        <div class="buttons">
                            <button type="submit" name="delete_category" class="negative" onclick="if (!confirm('Are you sure you want to delete this category?\n\nCustomers associated with this category are not affected,\nthey will be removed from this category.\n\nThis action cannot be undone!')) { this.blur(); return false; }">
                                Delete Category
                            </button>
                        </div>
						<?php } ?>
                        <?php } else { echo "&nbsp;"; } ?>
                    </td>-->
				</tr>
            </table>
		</form>
		        <?php } else{
	echo "You have no rights to access this page";
}?>
	</div>
</div>
<?php require ('tpl/footer.php'); ?>