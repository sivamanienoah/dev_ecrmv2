<?php require (theme_url().'/tpl/header.php'); ?>
<?php
	if($this->validation->add1_region != 0) 
	echo '<input type="hidden" name="region_update" id="region_update" value="'.$this->validation->add1_region.'" />';
	if($this->validation->add1_country != 0)
	echo '<input type="hidden" name="country_update" id="country_update" value="'.$this->validation->add1_country.'" />';
	if($this->validation->add1_state != 0)
	echo '<input type="hidden" name="state_update" id="state_update" value="'.$this->validation->add1_state.'" />';
	if($this->validation->add1_location != 0)
	echo '<input type="hidden" name="location_update" id="location_update" value="'.$this->validation->add1_location.'" />';
	//When user edit the customer details the add button will not appear for the country, state & Location -starts here
	if($this->uri->segment(3)=='update')
	echo '<input type="hidden" name="varEdit" id="varEdit" value="update" />';
	//When user edit the customer details the add button will not appear for the country, state & Location -Ends here
	$usernme = $this->session->userdata('logged_in_user');
?>

<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('edit')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
    	<form id="formone" action="<?php echo  $this->uri->uri_string() ?>" method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Customer Details</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked <span class='mandatory_asterick'>*</span> must be filled in correctly.</p>
			<table class="layout">
			
			<?php if ($this->uri->segment(3) == 'update') { ?>
			<!--<tr>
					<td width="100">Client Code</td>
					<td width="240"><input type="text" name="client_code" value="<?php #echo $this->validation->client_code; ?>" class="textfield width200px" readonly /> </td>
			</tr>-->
			<?php } ?>
			
				<!--<tr>
					<td width="100">First name: <span class='mandatory_asterick'>*</span> </td>
					<td width="240"><input type="text" name="first_name" value="<?php #echo $this->validation->first_name ?>" class="textfield width200px required" /> </td>
					<td width="100">Last Name: </td>
					<td width="240"><input type="text" name="last_name" value="<?php #echo $this->validation->last_name ?>" class="textfield width200px required" /> </td>
				</tr>-->
				<tr>
					<!--<td>Position:</td>
					<td><input type="text" name="position_title" value="<?php #echo $this->validation->position_title ?>" class="textfield width200px required" /></td>-->
                    <td>Company: <span class='mandatory_asterick'>*</span> </td>
					<td>
						<input type="text" name="company" id="company" value="<?php echo $this->validation->company; ?>" class="textfield width200px required" />
						<span class="company_err_msg"></span>
					</td>
					<?php if ($this->uri->segment(3) == 'update') { ?>
					<td width="100">Client Code</td>
					<td width="240"><input type="text" name="client_code" value="<?php echo $this->validation->client_code; ?>" class="textfield width200px" readonly /> </td>
					<?php } ?>
				</tr>
				<tr>
					<td>Address Line 1:</td>
					<td><input type="text" name="add1_line1" value="<?php echo $this->validation->add1_line1 ?>" class="textfield width200px" /></td>
                    <td>Address Line 2:</td>
					<td><input type="text" name="add1_line2" value="<?php echo $this->validation->add1_line2 ?>" class="textfield width200px" /></td>
				</tr>
				<?php //print_r($regions); ?>
				<tr>
					<td>Suburb:</td>
					<td><input type="text" name="add1_suburb" value="<?php echo $this->validation->add1_suburb ?>" class="textfield width200px" /></td>
                    <td>Post code:</td>
					<td><input type="text" name="add1_postcode" value="<?php echo $this->validation->add1_postcode ?>" class="textfield width200px" /></td>
				</tr>
				
				<tr>
				<td width="100">Region: <span class='mandatory_asterick'>*</span> </td>
				<?php if (($usernme['level']>=2) && ($this->uri->segment(3)!='update')) { ?>
					<td width="240" id="def_reg"></td>
				<?php } else { ?>
					<td width="240">
						<select id="add1_region" name="add1_region" onchange="getCountry(this.value)" class="textfield width200px required">
							<option value="0">Select Region</option>
							<?php
							if(count($regions>0)) {
								foreach ($regions as $region) { ?>
									<option value="<?php echo $region['regionid'] ?>"<?php echo ($this->validation->add1_region == $region['regionid']) ? ' selected="selected"' : '' ?>><?php echo $region['region_name']; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
						<span class="region_err_msg"></span>
					</td>
				<?php } ?>
				<td width="100">Country: <span class='mandatory_asterick'>*</span> </td>
				<?php if (($usernme['level']>=3) && ($this->uri->segment(3)!='update')) { ?>
					<td width="240" id="def_cntry"></td>
				<?php } else { ?>
					<td width="240" id='country_row'>
						<select id="add1_country" name="add1_country" class="textfield width200px required" >
							<option value="0">Select Country</option>                           
						</select>
						<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2) { ?>
							<a class="addNew" id="addButton"></a> <!--Display the Add button-->
						<?php } ?>
					</td>
				<?php } ?>
				</tr>
				
				<tr>
					<td width="100">State: <span class='mandatory_asterick'>*</span> </td>
					<?php if (($usernme['level']>=4) && ($this->uri->segment(3)!='update')) { ?>
						<td width="240" id="def_ste"></td>
					<?php } else { ?>
						<td width="240" id='state_row'>
							<select id="add1_state" name="add1_state" class="textfield width200px required">
							<option value="0">Select State</option>                           
							</select>
							<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3) { ?>
								<a id="addStButton" class="addNew"></a> <!--Display the Add button-->
							<?php } ?>
						</td>
					<?php } ?>
					<td width="100">Location: <span class='mandatory_asterick'>*</span> </td>
					<?php if (($usernme['level']>=5) && ($this->uri->segment(3)!='update')) { ?>
						<td width="240" id="def_loc"></td>
					<?php } else { ?>
						<td width="240" id='location_row'>
							<select id="add1_location" name="add1_location" class="textfield width200px required" onchange="getSalescontactDetails(this.value)">
							<option value="0">Select Location</option>                           
							</select>
							<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4) { ?>
								<a id="addLocButton" class="addNew"></a> <!--Display the Add button-->
							<?php } ?>	
						</td>
					<?php } ?>
					</tr>
				<tr>
					<td>Phone:</td>
					<td><input type="text" name="phone" value="<?php echo  $this->validation->phone ?>" class="textfield width200px" /></td>
                    <td>Fax Line:</td>
					<td><input type="text" name="fax" value="<?php echo  $this->validation->fax ?>" class="textfield width200px required" />
					</td>
				</tr>
				<tr>
					<td>Email: </td>
					<td><input type="text" name="email_2" id="emailval" autocomplete="off" value="<?php echo  $this->validation->email_2 ?>" class="textfield width200px required" /> 
					
					<div><span class="checkUser" style="color:green">Valid Email.</span></div>
					<div><span class="checkUser1" id="email-existsval" style="color:red">Email Already Exists.</span></div>
					<div><span class="checkUser2" id="email-existsval" style="color:red">Invalid Email.</span></div>
					
					<input type="hidden" class="hiddenUrl"/>
					<?php if ($this->uri->segment(3) == 'update') { ?>
						<input type="hidden" value="<?php echo $this->uri->segment(4); ?>" name="emailupdate" id="emailupdate" />
					<?php } else { ?>
						<input type="hidden" value="0" name="emailupdate" id="emailupdate" />
					<?php } ?>
					<td>Web:</td>
					<td><input type="text" name="www" value="<?php echo $this->validation->www ?>" class="textfield width200px required" />
					</td>
				</td>
				</tr>
				<tr>
                   <!-- <td>Email 4:</td>
					<td><input type="text" name="email_4" value="<?php #echo  $this->validation->email_4 ?>" class="textfield width200px required" /> 
					</td>-->
					
					<!--td>Skype Name:</td>
					<td><input type="text" name="skype_name" value="<?php #echo $this->validation->skype_name ?>" class="textfield width200px required" /></td-->
				
				<?php /*?>  <td>Is a Client: </td>
					<td>
						<lable for="is_client_yes"><input type="radio" name="is_client"  id="is_client_yes" value="1" <?php if ((isset($this->validation->is_client) && $this->validation->is_client == 1) || $client_projects !=0 ) echo ' checked="checked"' ?> <?php if($client_projects != 0) { ?> disabled <?php }?>> Yes </lable>
						<lable for="is_client_no"><input type="radio" name="is_client" id="is_client_no" value="0" <?php if (!isset($this->validation->is_client) ||  $this->validation->is_client == 0 && $client_projects == 0)  echo ' checked="checked"' ?> <?php if($client_projects != 0) { ?> disabled <?php }?> > No </lable>
					</td><?php */?>
					
					
					<?php if ($this->uri->segment(3) == 'update') { ?>
					<td>Is a Client:</td>
					<td>
						<input type="checkbox" name="is_client" value="1" <?php if ($this->validation->is_client == 1) echo ' checked="checked"' ?> disabled >
					</td>
					<?php } else { ?>
                    <td colspan="2">&nbsp;</td>
					<?php } ?>
				</tr>
				<!--tr>
					<td>Sales Contact Name:</td>
					<td>
						<?php #if ($this->uri->segment(3) != 'update') { ?>
							<input type="text" name="sales_contact_name" value="<?php #echo $login_sales_contact_name; ?>" class="textfield width200px" readonly />
						<?php #} else { ?>
							<?php #if(!empty($sales_person_detail)) {
									#$login_sales_contact_name  = $sales_person_detail['first_name'].' '.$sales_person_detail['last_name'];
									#$login_sales_contact_email = $sales_person_detail['email'];
								#}
							?>
							<input type="text" name="sales_contact_name" value="<?php #echo $login_sales_contact_name; ?>" class="textfield width200px" readonly />
						<?php #} ?>
						<?php #if ($this->uri->segment(3) != 'update') { ?>
						<input type="hidden" name="sales_contact_userid_fk" value="<?php #echo $usernme['userid']; ?>" class="textfield width200px" readonly />
						<?php #} else { ?>
						<input type="hidden" name="sales_contact_userid_fk" value="<?php #echo ($this->validation->sales_contact_userid_fk == 0) ? $usernme['userid'] : $this->validation->sales_contact_userid_fk; ?>" class="textfield width200px" readonly />
						<?php #} ?>
					</td>
                    <td>Sales Contact Email:</td>
					<td><input type="text" name="sales_contact_email" value="<?php #echo $login_sales_contact_email; ?>" class="textfield width200px" readonly />
					</td>
				</tr-->
                <tr>
					<td valign="top">Comments:</td>

					<?php
						$comments = "";
						if(isset($this->validation->comments) && !empty($this->validation->comments)) {
							$comments = str_replace(array('\r\n', '\r', '\n'), "\n", $this->validation->comments);
							// $comments = str_replace('<br />', PHP_EOL, $comments);
						}
					?>
					<td colspan="3"><textarea name="comments" class="textfield width200px" style="width:544px;" rows="2" cols="25"><?php echo stripslashes($comments); ?></textarea></td>
				</tr>
				<tr><td colspan='4'>&nbsp;</td></tr>
				<tr><td colspan='4'>&nbsp;</td></tr>
				<tr>
					<td colspan='4'>
						  <table class="table websiteBrd data-tbl dashboard-heads dataTable" id="document_tbl" >
							<thead>
								<tr class="bg-blue">
									<td>Name <span class='mandatory_asterick'>*</span></td>
									<td>Position</td>
									<td>Email ID <span class='mandatory_asterick'>*</span></td>
									<td>Contact No</td>
									<td>Skype</td>
									<td>Action</td>
								</tr>
							</thead>
							<?php
							    $i=1;
								if(!empty($customer_contacts) && count($customer_contacts)>0){
									foreach($customer_contacts as $row) {
										$disp_style = '';
										if($i!=count($customer_contacts)) {
											$disp_style = "style='display:none;'";
										}
										?>
										<tr>
											<td>
												<input type="hidden" name="contact_id[]" value="<?php echo $row['custid']; ?>" class="textfield contact_id width180px required" />
												<input type="text" name="customer_name[]" value="<?php echo $row['customer_name']; ?>" class="first_name textfield width180px required" />
												<span class="first_name_err_msg text-danger"></span>
											</td>
											<td>
											   <input type="text" name="position[]" value="<?php echo $row['position_title']; ?>" class="position_title textfield width180px required" />
												<span class="position_title_err_msg text-danger"></span>
											</td>
											<td>
											   <input type="text" name="email[]" value="<?php echo $row['email_1']; ?>" class="textfield email width180px required check_email" />
												<span class="email_err_msg text-danger"></span>
											</td>
											<td>
											   <input type="text" name="phone_no[]" value="<?php echo $row['phone_1']; ?>" class="textfield phone width180px required" />
												<span class="phone_err_msg text-danger"></span>
											</td>
											<td>
											   <input type="text" name="skype[]" value="<?php echo $row['skype_name']; ?>" class="textfield skype width180px required" />
												<span class="skype_err_msg text-danger"></span>
											</td>
										
											<td width="100">
												<a id="addRow" class="createBtn" <?=$disp_style?> ></a>
												<a id="deleteRow" hyperid="<?=$row['custid']?>" class="del_file"></a>
											</td>
										</tr>
									<?php
									$i++;
									}
								} else {
							?>
								<tr>
									<td>
										<input type="text" name="customer_name[]" value="" class="first_name textfield width180px required" />
										<span class="first_name_err_msg text-danger"></span>
									</td>
									<td>
									   <input type="text" name="position[]" value="" class="position_title textfield width180px required" />
										<span class="position_title_err_msg text-danger"></span>
									</td>
									<td>
									   <input type="text" name="email[]" value="" class="textfield email width180px required check_email" />
										<span class="email_err_msg err_msg text-danger"></span>
									</td>
									
									<td>
									   <input type="text" name="phone_no[]" value="" class="textfield phone width180px required" />
										<span class="phone_err_msg text-danger"></span>
									</td>
								
									<td>
									   <input type="text" name="skype[]" value="" class="textfield skype width180px required" />
										<span class="skype_err_msg err_msg text-danger"></span>
									</td>
									<td width="100" class='width180px'>
										<a id="addRow" class="createBtn"></a>
										<a id="deleteRow" hyperid="0" class="del_file"></a>
									</td>
								</tr>
							<?php 
						    }
							?>
						</table>
					   
					</td>
				</tr>
				<tr><td colspan='4'>&nbsp;</td></tr>
                <tr>
					<td>
						&nbsp;
					</td>
					<td colspan="3">
                        <div class="buttons">
							<button type="button" onclick='validate_customer()' name="update_customer" id="positiveBtn" class="positive">
								<?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Customer
							</button>
						</div>
						<div class="buttons">
                            <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>customers'">
                                Cancel
                            </button>
                        </div>
                    </td>
				</tr>
            </table>
		</form>
		<?php } else {
			echo "You have no rights to access this page";
		} ?>
	</div>
</div>
<style>
.text-danger{
	color:red;
}
</style>
<script>
	var customer_user_id = "<?php echo $usernme['userid']; ?>";
	var usr_level 		 = "<?php echo $usernme['level']; ?>";
	var cus_updt		 = "<?php echo ($this->uri->segment(3) == 'update') ? 'update' : 'no_update' ?>";
</script>
<script type="text/javascript" src="assets/js/customer/customer_add_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>