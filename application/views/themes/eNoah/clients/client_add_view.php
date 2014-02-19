<?php 
require (theme_url().'/tpl/header.php'); 
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('edit')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
    	<form action="<?php echo $this->uri->uri_string() ?>" method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Client Detail</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="100">Client Name: *</td>
					<td width="240"><input type="text" name="client_name" value="<?php echo $this->validation->client_name ?>" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td>Address Line 1:</td>
					<td><input type="text" name="address_1" value="<?php echo $this->validation->address_1 ?>" class="textfield width200px" /></td>
                    <td>Address Line 2:</td>
					<td><input type="text" name="address_2" value="<?php echo $this->validation->address_2 ?>" class="textfield width200px" /></td>
				</tr>
				<tr>
					<td>Suburb:</td>
					<td><input type="text" name="suburb" value="<?php echo $this->validation->suburb ?>" class="textfield width200px" /></td>
                    <td>Post code:</td>
					<td><input type="text" name="post_code" value="<?php echo $this->validation->post_code ?>" class="textfield width200px" /></td>
				</tr>
				
				<tr>
				<td width="100">Region: *</td>
				<?php if (($userdata['level']>=2) && ($this->uri->segment(3)!='update')) { ?>
					<td width="240" id="def_reg"></td>
				<?php } else { ?>
					<td width="240">
						<select id="region_id" name="region_id" onchange="getCountry(this.value)" class="textfield width200px required">
						<option value="0">Select Region</option>
							<?php
							if(count($regions>0)) {
								foreach ($regions as $region) { ?>
									<option value="<?php echo $region['regionid'] ?>"<?php echo ($this->validation->region_id == $region['regionid']) ? ' selected="selected"' : '' ?>><?php echo $region['region_name']; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</td>
				<?php } ?>
				<td width="100">Country: *</td>
				<?php if (($userdata['level']>=3) && ($this->uri->segment(3)!='update')) { ?>
					<td width="240" id="def_cntry"></td>
				<?php } else { ?>
					<td width="240" id='country_row'>
						<select id="country_id" name="country_id" class="textfield width200px required" >
							<option value="0">Select Country</option>                           
						</select>
						<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2) { ?>
							<!--a class="addNew" id="addButton"></a--> <!--Display the Add button-->
						<?php } ?>	
					</td>
				<?php } ?>
				</tr>
				
				<tr>
					<td width="100">State: *</td>
					<?php if (($userdata['level']>=4) && ($this->uri->segment(3)!='update')) { ?>
						<td width="240" id="def_ste"></td>
					<?php } else { ?>
						<td width="240" id='state_row'>
							<select id="state_id" name="state_id" class="textfield width200px required">
							<option value="0">Select State</option>                           
							</select>
							<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3) { ?>
								<!--a id="addStButton" class="addNew"></a--> <!--Display the Add button-->
							<?php } ?>
						</td>
					<?php } ?>
					<td width="100">Location: *</td>
					<?php if (($userdata['level']>=5) && ($this->uri->segment(3)!='update')) { ?>
						<td width="240" id="def_loc"></td>
					<?php } else { ?>
						<td width="240" id='location_row'>
							<select id="location_id" name="location_id" class="textfield width200px required">
							<option value="0">Select Location</option>                           
							</select>
							<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4) { ?>
								<!--a id="addLocButton" class="addNew"></a--> <!--Display the Add button-->
							<?php } ?>	
						</td>
					<?php } ?>
				</tr>
                <tr>
					<td>Website:</td>
					<td><input type="text" name="website" value="<?php echo $this->validation->website ?>" class="textfield width200px required" /></td>
				</tr>
                <tr>
					<td>
						&nbsp;
					</td>
					<td colspan="3">
                        <div class="buttons">
							<button type="submit" name="update_client" id="update_client" class="positive">
								<?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Client
							</button>
						</div>
						<div class="buttons">
                            <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>clients'">
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
	var customer_user_id = "<?php echo $userdata['userid']; ?>";
	var usr_level 		 = "<?php echo $userdata['level']; ?>";
	var cus_updt		 = "<?php echo ($this->uri->segment(3) == 'update') ? 'update' : 'no_update' ?>";
</script>
<script type="text/javascript" src="assets/js/client/client_add_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>