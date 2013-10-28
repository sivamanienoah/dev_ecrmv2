<?php 
require (theme_url().'/tpl/header.php'); ?>
<div id="content">
	 
    <div class="inner">
	<?php if($this->session->userdata('accesspage')==1){?>
		<form action="myaccount/add_log" method="post" onsubmit="return false;" style="display:none;" class="time-log-form">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2>Add a log for today</h2>
			<table class="layout">
				<tr>
					<td>
						Details
					</td>
					<td colspan="2">
						<textarea name="standalone_log" id="standalone_log" class="textfield" style="width:400px;" rows="6"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						Time Spent in minutes
					</td>
					<td>
						<input type="text" name="time_spent" id="time_spent" value="" class="textfield" style="margin-bottom:0;" />
					</td>
					<td align="right">
						<div class="buttons">
							<button type="submit" name="add_log" id="add_log" class="positive" style="float:right; margin:0;">
								
								Add Details
							</button>
						</div>
					</td>
				</tr>
			</table>
		</form>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkForm();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>My Profile</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>If you would like to update your details, please use the form below.</p>
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
					<td><input type="password" name="oldpassword" value="" class="textfield width200px" /> </td>
				</tr>
				<tr>
					<td>Password:</td>
					<td><input type="password" name="password" value="" class="textfield width200px" /> </td>
                    <td>Confirm Password:</td>
                    <td><input type="password" name="pass_conf" value="" class="textfield width200px" /> </td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_user" class="positive">Update</button>
						</div>
                    </td>
                    <td colspan="2">
                        &nbsp;
                    </td>
				</tr>
            </table>
		</form>
		
		<?php } else{
	echo "You have no rights to access this page";
}?>
	</div>
</div>
<script type="text/javascript">
function showPassFields(obj) {
	if (obj.attr('checked')) {
		$('.hide-passwords td:hidden').css('visibility', 'visible');
	} else {
		$('.hide-passwords td:visible').css('visibility', 'hidden');
	}
}
$(function(){
	$('.hide-passwords td').css('visibility', 'hidden');
	
	$('#add_log').click(function(){
		var log_details = $.trim($('#standalone_log').val());
		var log_time = $.trim($('#time_spent').val());
		
		if (log_details == '')
		{
			alert('Please fill content for your log');
			return false;
		}
		
		if (log_time == '' || log_time == 0)
		{
			alert('Please add time');
			return false;
		}
		
		$.post(
			'myaccount/add_log',
			{'log_content': log_details, 'time_spent': log_time},
			function (data)
			{
				if (data.error)
				{
					alert(data.error);
				}
				else
				{
					$('#time_spent').val('');
					$('#standalone_log').val('');
					$('.time-log-form:visible').slideUp('normal');
				}
				return false;
			},
			'json'
		);
	});
});
</script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
