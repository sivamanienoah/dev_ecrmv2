<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<div id="content">
    <div class="inner">
		<form action="<?php echo $this->uri->uri_string() ?>" method="post" >
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2>My Profile</h2>
			<?php if ($this->validation->error_string != '') { ?>
			<div class="form_error">
				<?php echo  $this->validation->error_string ?>
			</div>
			<?php } ?>
			<p>***If you would like to update your details, please use the form below.</p>
			<table class="layout">
				<tr>
					<td width="100">First name:</td>
					<td width="240"><input type="text" name="first_name" value="<?php echo  $this->validation->first_name ?>" class="textfield width200px required" /> *</td>
					<td width="100">Last Name:</td>
					<td width="240"><input type="text" name="last_name" value="<?php echo  $this->validation->last_name ?>" class="textfield width200px required" /> *</td>
				</tr>
				<tr>
					<td>Telephone:</td>
					<td><input type="text" name="phone" value="<?php echo  $this->validation->phone ?>" class="textfield width200px required" /></td>
					<td>Mobile:</td>
					<td><input type="text" name="mobile" value="<?php echo  $this->validation->mobile ?>" class="textfield width200px required" /></td>
				</tr>
				<tr>
					<td>Email:</td>
					<td colspan="3">
						<input type="text" name="email" value="<?php echo $this->validation->email ?>" readonly class="textfield width200px" /> &nbsp; (This is your login email)
					</td>
				</tr>
				<tr>
					<td>Signature:</td>
					<td colspan="3">
						<textarea name="signature" class="textfield width300px" rows="6"><?php echo $this->validation->signature ?></textarea>
					</td>
				</tr>
				<?php if($this->userdata['auth_type']==0) { ?>
				<tr>
					<td>Old Password:</td>
					<td><input type="password" name="oldpassword" value="" class="textfield width200px" autocomplete="off"/></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td><input type="password" name="password" value="" class="textfield width200px" /> </td>
					<td>Confirm Password:</td>
					<td><input type="password" name="pass_conf" value="" class="textfield width200px" /> </td>
				</tr>
				<?php } ?>
				<tr>
					<td>
						&nbsp;
					</td>
					<td colspan="4">
						<div class="buttons">
							<button type="submit" name="update_user" class="positive">Update</button>
						</div>
						<div class="buttons">
						   <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>myaccount'">Cancel</button>
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript" src="assets/js/user/account_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>