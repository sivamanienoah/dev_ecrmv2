<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<div id="content">
    <div class="inner">
		<?php 
		if($this->session->userdata('accesspage')==1) { 
		?>
		
		<h2>Change API Settings</h2>
		<form id="updateapi-form" action="<?php echo $this->uri->uri_string() ?>" method="post" >
		<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		   <table class="layout">
					<tr>
						<td width="100">API KEY:</td>
						<td width="240"><input type="text" readonly="true" name="api_key" id="api_key" value="" class="textfield width200px required" /> *</td>
						<td width="100"><input type="button" tabindex="-1" value="Key Generator" class="input-button" id="create_strong_password"></td>
						<td width="240">&nbsp;</td>
					</tr>
					<tr>
						<td width="100">API USERNAME:</td>
						<td width="240"><input type="text" name="api_username" id="api_username" class="textfield width200px required" /> *</td>
						<td width="100">&nbsp;</td>
						<td width="240">&nbsp;</td>
					</tr>
					<tr>
						<td width="100">API PASSWORD:</td>
						<td width="240"><input type="password" name="api_password" id="api_password" value="" class="textfield width200px required" /> *</td>
						<td width="100">&nbsp;</td>
						<td width="240">&nbsp;</td>
					</tr>
					<tr>
						<td>
							&nbsp;
						</td>
						<td colspan="4">
							<?php 
							if($this->session->userdata('edit')==1) { 
							?>
								<div class="buttons">
									<button type="submit" id="update_api" name="update_api" class="positive">Update</button>
								</div>
								<div class="buttons">
								   <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>myaccount'">Cancel</button>
								</div>
							<?php 
							}
							?>
						</td>
					</tr>
			</table>
		</form>
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
							<input type="text" name="email" value="<?php echo  $this->validation->email ?>" class="textfield width200px" /> &nbsp; (This is your login email)
						</td>
					</tr>
					<tr>
						<td>Signature:</td>
						<td colspan="3">
							<textarea name="signature" class="textfield width300px" rows="6"><?php echo  $this->validation->signature ?></textarea>
						</td>
					</tr>
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
					<tr>
						<td>
							&nbsp;
						</td>
						<td colspan="4">
							<?php 
							if($this->session->userdata('edit')==1) { 
							?>
								<div class="buttons">
									<button type="submit" name="update_user" class="positive">Update</button>
								</div>
								<div class="buttons">
								   <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>myaccount'">Cancel</button>
								</div>
							<?php 
							}
							?>
						</td>
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
<script type="text/javascript" src="assets/js/user/account_view.js"></script>
<script type="text/javascript">
	$(function(){
	$("#update_api").click(function()
	{
	if($("#api_key").val()=="")
	{
	   $.blockUI({
			message:'<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">Secret key should not be empty</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
			css:{width:'440px'}
		});
        return false;
	 }
	 if($("#api_username").val()=="")
	{
	   $.blockUI({
			message:'<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">Username key should not be empty</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
			css:{width:'440px'}
		});
        return false;
	 }
	 if($("#api_password").val()=="")
	{
	   $.blockUI({
			message:'<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">Password should not be empty</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
			css:{width:'440px'}
		});
        return false;
	 }
	 var form_data = $('#updateapi-form').serialize();
	   $.post('myaccount/updateapi',form_data,function (res) {
	     $("#api_key").val(res);
	   });
	 return false;
	});
	$("#create_strong_password").click(function()
	{
	var form_data = "<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>"
	   $.post('myaccount/generatesecretkey',form_data,function (res) {
	     $("#api_key").val(res);
	   });
		return false;
	});
});
function cancelDel() {
    $.unblockUI();
}
</script>
<?php require (theme_url(). '/tpl/footer.php'); ?>