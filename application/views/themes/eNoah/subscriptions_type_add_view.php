<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <?php include 'tpl/hosting_submenu.php'; ?>
    <div class="inner">
	
	
	<?php 
	
	
	if(($this->session->userdata('add')==1 && $this->uri->segment(3)=="") || (($this->session->userdata('edit')==1) && ($this->uri->segment(2) == 'subscription_type_update' && is_numeric($this->uri->segment(3))))) { 	
	?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		<input type="hidden" value="<?php echo $toDB; ?>" name="toDB">
            <h2><?php echo  ($this->uri->segment(2) == 'update' && is_numeric($this->uri->segment(3))) ? 'Update' : 'New' ?> Subscription Type</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="170">Subscription Type Name: *</td>
					<td width="300">
                        <input type="text" name="subscriptions_type_name" value="<?php echo  (!empty($subscriptions_type_name)) ? $subscriptions_type_name : $this->validation->subscriptions_type_name; ?>" class="textfield width200px" />
                        <input type="hidden" name="customer_id" id="cust_id" value="<?php echo  (isset($customer_id)) ? $customer_id : '' ?>" />
                    </td>
				</tr>
				
				<?php
				(!empty($subscriptions_type_flag)) ? $p=$subscriptions_type_flag : $p=$this->validation->subscriptions_type_flag;
				?>
				<tr>
					<td>Flag: *</td>
					<td>
						<select name="subscriptions_type_flag" class="textfield width200px required">
						<option value="">Select Flag</option>
						<option value="active" <?php if($p=='active') echo 'selected="selected"'; ?>>Active</option>
						<option value="inactive"<?php if($p=='inactive') echo 'selected="selected"'; ?>>Inactive</option>
						</select>
					</td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_customer" class="positive">
								<?php echo  ($this->uri->segment(2) == 'update' && is_numeric($this->uri->segment(3))) ? 'Update' : 'Add' ?> Subscription Type
							</button>
						</div>
						<div class="buttons">
							<button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>package/subscription_type'">
								Cancel
							</button>
						</div>
                    </td>
                    <td>&nbsp;</td>
				</tr>
            </table>
		</form>
		<?php 
		} else {
			echo "You have no rights to access this page";
		} 
		?>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>
