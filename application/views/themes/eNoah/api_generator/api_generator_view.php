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
								   <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>api_generator'">Cancel</button>
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
	   $.post('api_generator/updateapi',form_data,function (res) {
	     $("#api_key").val(res);
	   });
	 return false;
	});
	$("#create_strong_password").click(function()
	{
	var form_data = "<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>"
	   $.post('api_generator/generatesecretkey',form_data,function (res) {
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