<?php require (theme_url().'/tpl/header.php'); ?>
<?php 
#error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); 
$this->userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
    <div class="inner">
	<?php 
	if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || (($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) && ($this->session->userdata('edit')==1))) {
	?>
		<?php if($this->uri->segment(3) != 'update') { ?>
			<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkAddUser();" id="frm">
		<?php } else { ?>
			<form action="<?php echo  $this->uri->uri_string() ?>" method="post" id="frm">
		<?php } ?>
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> User Details</h2>
            <?php 
			if ($this->validation->error_string != '') {
			?>
				<div class="form_error">
					<?php echo $this->validation->error_string; ?>
				</div>
            <?php 
			}
			?>

			<table class="layout">
				<tr>
					<td width="115">First Name: *</td>
					<td width="240"><input type="text" id="first_name" name="first_name" value="<?php echo $this->validation->first_name ?>" class="textfield width200px required" />
						<div class="error" style="color:red;" id="error12">Required</div>			
					</td>					
					<td width="115">Last Name: *</td>
					<td width="240"><input type="text" id="last_name" name="last_name" value="<?php echo $this->validation->last_name ?>" class="textfield width200px required" /> 
						<div class="error" style="color:red;" id="error2">Required</div>
					</td>
				</tr>
				<tr>
					<td>Username: *</td>
					<td>
						<input type="text" name="username" id="username" value="<?php echo $this->validation->username ?>" class="textfield width200px required" autocomplete="off" />
						<div class="error" style="color:red;" id="errorun">Required</div>
						<div id='username_errmsg'></div>
					</td>
					<td>Role: *</td>
					<td>
                        <select id="role_id" name="role_id" class="textfield width200px">
                            <option value="">Please Select</option>
							<?php foreach ($roles as $role) { ?>
								<option value="<?php echo $role['id'];?>" <?php echo ($this->validation->role_id == $role['id']) ? ' selected="selected"' : '' ?>><?php echo $role['name'] ;?></option>
							<?php } ?>
                        </select> 
						<div class="error" style="color:red;" id="error3">Required</div>
						<input type="hidden" value="0" id="role_change_mail" name="role_change_mail"/>
						<script>
							$('#role_id').change(function() {
								var assign_mail = $('#role_id').val();
								//alert(assign_mail);
								$('#role_change_mail').val(assign_mail);
							});
						</script>
					</td>
				</tr>
				<tr id="reseller_row" style="display:none;">
					<td>Contract Manager</td>
					<td>
						<select name="contract_manager" id="contract_manager" class="textfield width200px">
							<option value="">Please Select</option>
							<?php 
								if(is_array($users) && !empty($users) && count($users)) {
									foreach($users as $user_row) {
										$user_name = $user_row['first_name'];
										if(isset($user_row['last_name']) && !empty($user_row['last_name'])){
											$user_name .= " ".$user_row['last_name'];
										}
										if(isset($user_row['emp_id']) && !empty($user_row['emp_id'])) {
											$user_name .= " - ".$user_row['emp_id'];
										}
							?>
										<option value="<?php echo $user_row['userid']; ?>" <?php echo ($this->validation->contract_manager == $user_row['userid']) ? ' selected="selected"' : '' ?>><?php echo $user_name; ?></option>
							<?php
									}
								}
							?>
						</select>
						<div class="error" style="color:red;" id="error_contractmanager">Required</div>
					</td>
				</tr>
				<tr>
					<td>Telephone:</td>
					<td><input type="text" name="phone" value="<?php echo $this->validation->phone ?>" class="textfield width200px required" /></td>
                    <td>Mobile:</td>
					<td><input type="text" name="mobile" value="<?php echo $this->validation->mobile ?>" class="textfield width200px required" /></td>
				</tr>
				<tr>
					<td>Email: *</td>
					<td><input type="text" id="email" name="email" value="<?php echo $this->validation->email ?>" class="textfield width200px" autocomplete="off"/><br/> 
					<span class="error" style="color:red;" id="error4">Required</span>
					<span class="error" style="color:red;" id="notvalid">Not a valid e-mail address</span>
					<span class="checkUser" style="color:green">Email Available.</span>
					<span class="checkUser1" id="email-existsval" style="color:red">Email Already Exists.</span>
					<input type="hidden" class="hiddenUrl"/>
					<?php if ($this->uri->segment(3) == 'update') { ?>
						<input type="hidden" value="<?php echo $this->uri->segment(4); ?>" name="email_1" id="email_1" />
					<?php } ?>	
					</td>
				</tr>
				<tr>
					<td>Password: *</td>
					<td><input type="password" id="password" name="password" value="" class="textfield width200px" autocomplete="off"/>
						<?php if ($this->uri->segment(3) != 'update') { ?>
							<div class="error" style="color:red;" id="error5">Required</div>
						<?php } ?>
					</td>
                    <td>
						<?php 
						if (($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) && ($this->session->userdata('edit')==1)) {
						?>
							Update Password
					</td>
					<td>
							<input type="checkbox" name="update_password" value="1" /> 
						<?php 
						} else { 
						?>
							<input type="hidden" name="new_user" value="1" />
						<?php 
						} 
						?>
					</td>					
				</tr>
				<tr>
					<td>Login Authentication: *</td>
					<td>
						<label for="auth-ldb">LDB</label>
						<input <?php echo ($this->validation->auth_type == 0) ? ' checked="checked"' : '' ?> id="auth-dp" type="radio" name="auth_type" value="0"/>&nbsp;&nbsp;&nbsp;
						<label for="auth-ldap">LDAP</label>
						<input <?php echo ($this->validation->auth_type == 1) ? ' checked="checked"' : '' ?> id="auth-ldap" type="radio" name="auth_type" value="1"/>
					</td>
				</tr>
				<tr>
				<!-- Levels and region settings starts here -->
					<td>Level: *</td>
					<td>
						<?php 
						if($this->uri->segment(3) != 'update') { 
						?>
							<select id="level_id" name="level" class="textfield width200px">
								<option value="">Please Select</option>
								<?php foreach ($levels as $val) { ?>
									<option value="<?php echo $val['level_id']; ?>"<?php echo  ($this->validation->level == $val['level_id']) ? ' selected="selected"' : '' ?>><?php echo  $val['level_name']; ?></option>
								<?php } ?>
							</select> 
							<br/>
							<div class="error" style="color:red;" id="error6">Required</div><br/> <br/> 
							<div class="level-message"></div>
						<?php 
						} else { 
						?>
							<select id="level_id" name="level" class="textfield width200px">
								<option value="">Please Select</option>
								<?php foreach ($levels as $val) { ?>
									<option value="<?php echo $val['level_id']; ?>"<?php echo  ($this->validation->level == $val['level_id']) ? ' selected="selected"' : '' ?>><?php echo  $val['level_name']; ?></option>
								<?php } ?>
							</select><br/> <div class="error" style="color:red;" id="error6">Required</div><br/><br/> <div class="level-message"></div>
						<?php 
						} 
						?>						
						<input type="hidden" value="0" id="level_change_mail" name="level_change_mail"/>
					</td>
					<!-- Levels and region settings ends here -->	
					<?php 
					if (($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) && ($this->session->userdata('edit')==1)) { 
					?>
					<td>Inactive User:</td>
					<td colspan="3">
						<input type="checkbox" name="inactive" value="1" <?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?>
						<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>> 
						<?php if ($cb_status != 0) echo "One or more Leads currently assigned for this User. This cannot be made Inactive."; ?>
						<?php if (($this->validation->inactive == 0) && ($cb_status == 0)) echo "Check if the User need to be Inactive."; ?>
						<?php if ($this->validation->inactive != 0) echo "Uncheck if the User need to be Active."; ?>						
					</td>
					<?php 
					} 
					?>
				</tr>
				<tr>
				<td colspan="5">
					<div class="container-region" style="float:left;display:none;">
						<table border="0" cellpadding="0" cellspacing="0" class="data-tabl-dupl">
							<thead>
									<tr>
										<th><div class="select-region" style="display:none">Select Region</div></th>
										<th><div class="select-country" style="display:none">Select Country</div></th>
										<th><div class="select-state" style="display:none">Select State</div></th>
										<th><div class="select-location" style="display:none">Select Location</div></th>
									</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<div class="region-box" style="display:none">
											<select id="region_load" class="required" name="region[]" multiple="multiple">
										</div>
									</td>
									<td id="country_row">
										<div class="country-box" style="display:none">
										<select name="country[]" class="required" id="country_load" multiple="multiple"><option value="">Select</option></select>
										</div>
									</td>
									<td id="state_row">
										<div class="state-box" style="display:none">
										<select name="state[]" class="required" id="state_load" multiple="multiple"><option value="">Select</option></select>
										</div>
									</td>
									<td id="location_row">
										<div class="location-box" style="display:none">
										<select name="location[]" class="required" id="location_load" multiple="multiple"><option value="">Select</option></select>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</td>
				</tr>
				<tr><td><td>&nbsp;</td></td></tr>				
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
						<?php if($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) { ?>
							<button type="submit" onclick="return last();" name="update_user" class="positive" id="checkemail">				
						<?php } else { ?>
							<button type="submit" name="update_user" class="positive" id="checkemail">	
						<?php } ?>							
								<?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> User
							</button>
						</div>
						<div class="buttons">
                           <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>user'">
								Cancel
							</button>
                        </div>
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
<script>
	var curUserId = '<?php echo $this->userdata['userid'] ?>';
</script>
<script type="text/javascript" src="assets/js/user/add_user.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>