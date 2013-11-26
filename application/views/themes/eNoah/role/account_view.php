<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
	<div id="left-menu">
		<a href="user/log_history/">View My Logs</a>
		<a href="#" onclick="$('.time-log-form:hidden').slideDown(); return false;">Add a Log</a>
	</div>
    <div class="inner">
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
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>My Account</h2>
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
					<td>Additional Email:</td>
					<td><input type="text" name="add_email" value="<?php echo  $this->validation->add_email ?>" class="textfield width200px required" /></td>
					
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="3">
						<p><strong>Important:</strong> This log setting is only applied when the client emails are not selected when logging<br /> and when you have a valid additional email set-up in your 'My Account' area.</p>
					</td>
				</tr>
				<tr>
					<td>Signature:</td>
					<td colspan="3">
						<textarea name="signature" class="textfield width300px" rows="6"><?php echo  $this->validation->signature ?></textarea>
					</td>
				</tr>
				<tr class="hide-passwords">
					<td>Password:</td>
					<td><input type="password" name="password" value="" class="textfield width200px" /> *</td>
                    <td>Confirm Password:</td>
                    <td><input type="password" name="pass_conf" value="" class="textfield width200px" /> *</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="checkbox" name="update_password" value="1" style="vertical-align:middle;" onclick="showPassFields($(this));" /> Update password?</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
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
	</div>
</div>
<script type="text/javascript" src="assets/js/role/account_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
