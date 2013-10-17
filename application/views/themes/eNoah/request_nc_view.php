<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
    <?php if ($this->login_model->check_login_status(array(6))) { ?>
    <div id="left-menu">
		<a href="welcome/ex_customer/">Existing Customer</a>
	</div>
    <?php } ?>
    <div class="inner">
    	<form action="" method="post" onsubmit="return checkForm();">
		
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>Request a quotation</h2>
            <?php echo $this->validation->error_string; ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="100">First name:</td>
					<td width="240"><input type="text" name="first_name" value="<?php echo  $this->validation->first_name ?>" class="textfield width200px required" /> *</td>
					<td width="100">Last Name:</td>
					<td width="240"><input type="text" name="last_name" value="<?php echo  $this->validation->last_name ?>" class="textfield width200px required" /> *</td>
				</tr>
				<tr>
					<td>Position:</td>
					<td><input type="text" name="position_title" value="<?php echo  $this->validation->position_title ?>" class="textfield width200px required" /></td>
                    <td>Company:</td>
					<td><input type="text" name="company" value="<?php echo  $this->validation->company ?>" class="textfield width200px required" /> *</td>
				</tr>
				<tr>
					<td>Address Line 1:</td>
					<td><input type="text" name="add1_line1" value="<?php echo  $this->validation->add1_line1 ?>" class="textfield width200px" /></td>
                    <td>Address Line 2:</td>
					<td><input type="text" name="add1_line2" value="<?php echo  $this->validation->add1_line2 ?>" class="textfield width200px" /></td>
				</tr>
				<tr>
					<td>Suburb:</td>
					<td><input type="text" name="add1_suburb" value="<?php echo  $this->validation->add1_suburb ?>" class="textfield width200px" /></td>
                    
					<td>State:</td>
					<td>
                        <select name="add1_state" class="textfield width200px" id="userState">
                            <option <?php echo  $this->validation->set_select('add1_state', 'NSW') ?>>NSW</option>
                            <option <?php echo  $this->validation->set_select('add1_state', 'QLD') ?>>QLD</option>
                            <option <?php echo  $this->validation->set_select('add1_state', 'SA') ?>>SA</option>
                            <option <?php echo  $this->validation->set_select('add1_state', 'VIC') ?>>VIC</option>
                            <option <?php echo  $this->validation->set_select('add1_state', 'NT') ?>>NT</option>
                            <option <?php echo  $this->validation->set_select('add1_state', 'ACT') ?>>ACT</option>
                            <option <?php echo  $this->validation->set_select('add1_state', 'TAS') ?>>TAS</option>
                            <option <?php echo  $this->validation->set_select('add1_state', 'WA') ?>>WA</option>
                        </select>
					</td>
				</tr>
				<tr>
					<td>Post code:</td>
					<td><input type="text" name="add1_postcode" value="<?php echo  $this->validation->add1_postcode ?>" class="textfield width200px" /></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
				</tr>
				<tr>
					<td>Direct Phone:</td>
					<td><input type="text" name="phone_1" value="<?php echo  $this->validation->phone_1 ?>" class="textfield width200px required" />
						*</td>
                    
					<td>Work Phone:</td>
					<td><input type="text" name="phone_2" value="<?php echo  $this->validation->phone_2 ?>" class="textfield width200px" /></td>
				</tr>
                    <tr>
					<td>Mobile Phone:</td>
					<td><input type="text" name="phone_3" value="<?php echo  $this->validation->phone_3 ?>" class="textfield width200px required" />
						</td>
                    
					<td>Fax Line:</td>
					<td><input type="text" name="phone_4" value="<?php echo  $this->validation->phone_4 ?>" class="textfield width200px" /></td>
				</tr>
                <tr>
					<td>Email:</td>
					<td><input type="text" name="email_1" id="emailval" value="<?php echo  $this->validation->email_1 ?>" class="textfield width200px required" /> *
					</td>
                    <td>Secondary Email:</td>
					<td><input type="text" name="email_2" value="<?php echo  $this->validation->email_2 ?>" class="textfield width200px required" /> 
					</td>
				</tr>
                    <tr>
					<td>Web:</td>
					<td><input type="text" name="www_1" value="<?php echo  $this->validation->www_1 ?>" class="textfield width200px required" />
					</td>
                    <td>Secondary Web:</td>
					<td><input type="text" name="www_2" value="<?php echo  $this->validation->www_2 ?>" class="textfield width200px required" />
					</td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" class="positive">
								
								Request
							</button>
						</div>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
				</tr>
            </table>
		</form>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>