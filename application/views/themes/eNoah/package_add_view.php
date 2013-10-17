<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <?php include 'tpl/hosting_submenu.php'; ?>
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3)=="") || (($this->session->userdata('edit')==1) && ($this->uri->segment(2) == 'update' && is_numeric($this->uri->segment(3))))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		<input type="hidden" value="<?php echo $toDB; ?>" name="toDB">
            <h2><?php echo  ($this->uri->segment(2) == 'update' && is_numeric($this->uri->segment(3))) ? 'Update' : 'New' ?> Package Type</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="120">Package Type Name:</td>
					<td width="300">
                        <input type="text" name="package_name" value="<?php echo  (!empty($package_name)) ? $package_name : $this->validation->package_name; ?>" class="textfield width200px" /> *
                        <input type="hidden" name="customer_id" id="cust_id" value="<?php echo  (isset($customer_id)) ? $customer_id : '' ?>" />
                    </td>
				</tr>
				<tr>
					<td>Months:</td>
					<td><input type="text" name="type_months" value="<?php echo  (!empty($type_months)) ? $type_months : $this->validation->type_months; ?>" class="textfield width200px required" /> *</td>
				</tr>
				<?php
				(!empty($package_flag)) ? $p=$package_flag : $p=$this->validation->package_flag;
				?>
				<tr>
					<td>Flag:</td>
					<td><select name="package_flag" class="textfield width200px required">
					<option value="">Select Flag</option>
					<option value="active" <?php if($p=='active') echo 'selected="selected"'; ?>>Active</option>
					<option value="inactive"<?php if($p=='inactive') echo 'selected="selected"'; ?>>Inactive</option>
					</select> *</td>
                    
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_customer" class="positive">
								
								<?php echo  ($this->uri->segment(2) == 'update' && is_numeric($this->uri->segment(3))) ? 'Update' : 'Add' ?> Package Type
							</button>
						</div>
                    </td>
                    <td>
                        <?php if ($this->uri->segment(2) == 'update' && is_numeric($this->uri->segment(3)) && $userdata['level'] < 1) { ?>
                        <div class="buttons">
                            <button type="submit" name="delete_account" class="negative" onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
                                Delete Account
                            </button>
                        </div>
                        <?php } else { echo "&nbsp;"; } ?>
                    </td>
                    <td>&nbsp;</td>
				</tr>
            </table>
		</form>
		<?php } else{
			echo "You have no rights to access this page";
		}?>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>
