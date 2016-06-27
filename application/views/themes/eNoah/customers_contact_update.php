<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <div class="inner">
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" name="customers_contact_div" onsubmit="return chk_customers_contact();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
			<input id="custid" type="hidden" name="custid" value="<?php echo $contacts['custid']; ?>" />
			
			<h2>Update Customer Contact</h2>
			
			<?php if($this->session->userdata('edit')==1) { ?>
				<a class="pull-right" target="_blank" href="customers/add_customer/update/<?php echo $contacts['company_id']; ?>" title='Edit'>Edit Company Details</a>
			<?php } ?>
			
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Customer Name: *</td>
					<td>
						<input type="text" name="customer_name" id="customer_name" value="<?php echo $contacts['customer_name']; ?>" class="textfield width200px" />
					</td>
					<td><div id="name_msg"></div></td>
				</tr>
				<tr>
					<td>Email ID: *</td>
					<td>
						<input type="text" name="email" id="email" value="<?php echo $contacts['email_1']; ?>" class="textfield width200px" />
					</td>
					<td><div id="email_msg"></div></td>
				</tr>
				<tr>
					<td>Position: *</td>
					<td>
						<input type="text" name="position_title" id="position_title" value="<?php echo $contacts['position_title']; ?>" class="textfield width200px" />
					</td>
					<td><div id="position_msg"></div></td>
				</tr>
				<tr>
					<td>Contact No: *</td>
					<td>
						<input type="text" name="phone" id="phone" value="<?php echo $contacts['phone_1']; ?>" class="textfield width200px" />
					</td>
					<td><div id="phone_msg"></div></td>
				</tr>
				<tr>
					<td>Skype</td>
					<td colspan="2">
						<input type="text" name="skype_name" id="skype_name" value="<?php echo $contacts['skype_name']; ?>" class="textfield width200px" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons" colspan="2">
                        <div class="buttons">
							<button type="submit" name="update_contacts" class="positive">
								Update Contact
							</button>
						</div>
						<div class="buttons">
                            <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>customers_contact'">Cancel</button>
                        </div>
                    </td>
				</tr>
            </table>
		</form>
	</div><!--Inner div close-->
</div><!--Content div close-->
<script type="text/javascript" src="assets/js/customers_contact.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
