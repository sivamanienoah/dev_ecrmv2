<script type="text/javascript" src="assets/js/regionsettings/location_view.js"></script>
<?php
if($this->validation->regionid != 0) 
echo '<input type="hidden" name="region_update" id="region_update" value="'.$this->validation->regionid.'" />';
if($this->validation->countryid != 0) 
echo '<input type="hidden" name="countryid" id="countryid" value="'.$this->validation->countryid.'" />';
if($this->validation->stateid != 0)
echo '<input type="hidden" name="stateid" id="stateid" value="'.$this->validation->stateid.'" />';

?>
<div id="content">	
	<div class="inner">
	<?php if(($this->session->userdata('viewAdmin')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('addAdmin')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('editAdmin')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
		<div class="in-content">				
		<form action="<?php echo  $this->uri->uri_string() ?>" id="location_form" method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		    <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Location Details</h2>
		    <?php if ($this->validation->error_string != '') { ?>
		    <div class="form_error">
			<?php echo  $this->validation->error_string ?>
		    </div>
		    <?php } ?>
			<p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">				
				<tr>
					<td width="100">Region:</td>
					<td width="240">
					<select name="regionid" id="regionid" class="textfield width200px" onchange="getCountrylo(this.value)" class="textfield width200px required">
						<option value="0">Select Region</option>
                            <?php 
							foreach ($regions as $region) { ?>
								<option value="<?php echo  $region['regionid'] ?>"<?php echo  ($this->validation->regionid == $region['regionid']) ? ' selected="selected"' : '' ?>><?php echo  $region['region_name']; ?></option>
							<?php } ?>
					</select> *
					</td>
					<td class="error" id="Varerr1" style="color:red;">Region is Mandatory.</td>
				</tr>
				<tr>
					<td width="100">Country:</td>
					<td id='country_row1'>
                        <select name="add1_country" id="add1_country" class="textfield width200px required" >
							<option value="0">Select Country</option>
								<?php 
							foreach ($countrys as $country) { ?>
								<option value="<?php echo $country['countryid'] ?>"<?php echo ($this->validation->countryid == $country['countryid']) ? ' selected="selected"' : '' ?>><?php echo  $country['country_name']; ?></option>
							<?php } ?>	
                        </select>*
					</td>
					<td class="error" id="err2" style="color:red;">Country is Mandatory.</td>
				</tr>
				<tr>
					<td width="100">State:</td>
					<?php $stid=$this->validation->stateid ?>
					<td id='state_row'>
                        <select id="stateid" name="stateid" class="textfield width200px required">
							<option value="0">Select State</option>   
							<?php 
							foreach ($states as $state) { ?>
								<option value="<?php echo $state['stateid'] ?>"<?php echo ($this->validation->stateid == $state['stateid']) ? ' selected="selected"' : '' ?>><?php echo  $state['state_name']; ?></option>
							<?php } ?>
                        </select>*
						
					</td>
					<!--<td width="240"><select id="state_name" name="stateid"><option value="">Select</option><?php if (is_array($states) && count($states) > 0) { ?>
					<?php foreach ($states as $state) { ?><option value="<?php echo $state['stateid']; ?>" <?php if($stid==$state['stateid']) { echo "selected"; } ?>><?php echo $state['state_name']; ?> </option><?php } } ?></select> *</td>-->
					<td class="error" id="erro3" style="color:red;">State is Mandatory.</td>
				</tr>				
				<tr>
					<td width="100">Location:</td>
					<td width="240"><input id="location_name" type="text" name="location_name" value="<?php echo  $this->validation->location_name ?>" class="textfield width200px required" /> *</td>
					<td class="error" id="err4" style="color:red;">Location Field is Required.</td>
				</tr>
				<tr>
					<td>Status:</td>
					<td colspan="3"><input type="checkbox" name="inactive" value="1"<?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the location is inactive .</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<?php if (isset($this_user) && $userdata['userid'] == $this_user) { ?>
					<td colspan="3">
						Active location cannot be modified! Please use my account to update your details.
					</td>
					<?php } else if (isset($this_user_level) && $userdata['level'] >= $this_user_level && $userdata['level'] != 0) { ?>
					<td colspan="3">
						Your location level cannot updated similar levels or levels above you.
					</td>
					<?php } else { ?>
					<td style="float:left;">
						<div class="buttons">
							<button type="submit" name="update_location" class="positive">								
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> location
							</button>
						</div>
					</td>
					    <?php if ($this->uri->segment(4)) { ?>
                    <td style="float:left;">
                        <div class="buttons">
                            <button type="submit" name="cancel_submit" class="negative">Cancel</button>
                        </div>
                    </td>
                    <?php } ?>
					<?php } ?>
				</tr>
			</table>
		</form>	
		
		<h2>Location List</h2>
          
        <table class="loc-data-tbl dashboard-heads dataTable" style="width:100%" border="0" cellpadding="0" cellspacing="0" >            
            <thead>
                <tr>
                    <th>Location Name</th>
                    <th>State Name</th>
                    <th>Country Name</th>
                    <th>Created Date</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>            
            <tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td><?php if ($this->session->userdata('editAdmin')==1) {?><a class="edit clrmarron" href="regionsettings/location/update/<?php echo  $customer['locationid'] ?>"><?php echo  $customer['location_name'] ; ?></a><?php } else { echo $customer['location_name']; } ?></td>
						<td><?php echo $customer['state_name']; ?></td>
						<td><?php echo $customer['country_name']; ?></td>
						<!--td><?php echo  $customer['mfnam']. $customer['mlnam']; ?></td-->
                        <td><?php echo  date('d-m-Y', strtotime($customer['created'])); ?></td>
						<td><?php echo  $customer['cfnam'].$customer['clnam']; ?></td>   
                        <!--td><?php echo  $customer['modified'];?></td-->
                        <td>
							<?php 
							if($customer['inactive']==0) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>";	
							?>
						</td>      
						<td class="actions">
							<?php if ($this->session->userdata('editAdmin')==1) {?><a class="edit clrmarron" href="regionsettings/location/update/<?php echo $customer['locationid']; ?>"><?php echo  "Edit"; ?><?php } else echo "Edit"; ?></a>
							<?php if ($this->session->userdata('deleteAdmin')==1) {?> | <a class="delete clrmarron" href="regionsettings/location_delete/delete/<?php echo $customer['locationid']; ?>" onclick="return confirm('Are you sure you want to delete?')"><?php echo "Delete"; ?></a><?php } ?>
						</td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>            
        </table>
		
	<?php } else {
			echo "You have no rights to access this page";
		}
	?>	
	 </div>
    </div>
</div>