<script type="text/javascript" src="assets/js/regionsettings/state_view.js"></script>
<?php
if($this->validation->regionid != 0) 
echo '<input type="hidden" name="region_update" id="region_update" value="'.$this->validation->regionid.'" />';
?>
<div id="content">	
    <div class="inner">
	<div class="in-content">
	<?php if(($this->session->userdata('viewAdmin')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('addAdmin')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('editAdmin')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
		<form action="<?php echo  $this->uri->uri_string() ?>" id="state_form" method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> State Details</h2>
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
					<select name="regionid" class="textfield width200px" onchange="getCountryst(this.value)" class="textfield width200px required">
						<option value="0">Select Region</option>
                            <?php 
							foreach ($regions as $region) { ?>
								<option value="<?php echo  $region['regionid'] ?>"<?php echo  ($this->validation->regionid == $region['regionid']) ? ' selected="selected"' : '' ?>><?php echo  $region['region_name']; ?></option>
							<?php } ?>
					</select> *
					</td>
					<td class="error" id="error1" style="color:red;">Select Region</td>
				</tr>
				<div id="test"></div>
				<tr>
					<td width="100">Country:</td>
					<?php $cid = $this->validation->countryid ?>
					<td id='country_row' width="240">
						<select id="country_id" name="countryid" style="width:210px;">
							<option value="">Select Country</option>
							<?php if (is_array($countrys) && count($countrys) > 0) { ?>
							<?php foreach ($countrys as $country) { ?>
							<option value="<?php echo $country['countryid'];?>" <?php if($cid==$country['countryid']) { echo "selected"; } ?>><?php echo $country['country_name']; ?> </option>
							<?php } } ?>
						</select> *
					</td>
					<td class="error" id="error1" style="color:red;">Select Country</td>
				</tr>
				<tr>
					<td width="100">State:</td>
					<td width="240"><input id="state_id" type="text" name="state_name" value="<?php echo $this->validation->state_name ?>" class="textfield width200px required" /> *</td>
					<td class="error" id="error2" style="color:red;">State Field Required.</td>
				</tr>
				<tr>
					<td>Status:</td>
					<td colspan="3"><input type="checkbox" name="inactive" value="1"<?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the state is inactive .</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<?php if (isset($this_user) && $userdata['userid'] == $this_user) { ?>
					<td colspan="3">
						Active state cannot be modified! Please use my account to update your details.
					</td>
					<?php } else if (isset($this_user_level) && $userdata['level'] >= $this_user_level && $userdata['level'] != 0) { ?>
					<td colspan="3">
						Your state level cannot updated similar levels or levels above you.
					</td>
					<?php } else { ?>
					<td style="float:left;">
						<div class="buttons">
							<button type="submit" name="update_state" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> state
							</button>
						</div>
					</td>
					<?php if ($this->uri->segment(4)) { ?>
			    <td style="float:left;">
				<div class="buttons">
				    <button type="submit" name="cancel_submit" class="negative">
					Cancel
				    </button>
				</div>
			    </td>
			    <?php } ?>
					<?php } ?>
				</tr>
			</table>
		</form>
    <h2>State List</h2>
        
        <form action="regionsettings/region_settings/" method="post" id="cust_search_form">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		<table border="0" cellpadding="0" cellspacing="0" class="search-table">
			<tr>
			    <td>
				Search by State
			    </td>
			    <td>
				<input type="text" id="statesearch" name="cust_search" class="textfield width200px"/> <!--value="<?php echo  $this->uri->segment(4) ?>"-->  
			    </td>
			    <td>
				<div class="buttons">
				    <button type="submit" class="stsearch">
					Search
				    </button>
				</div>
			    </td>
			    <?php if ($this->uri->segment(4)) { ?>
			    <td>
				<div class="buttons">
				    <button type="submit" name="cancel_submit" class="negative">
					Cancel
				    </button>
				</div>
			    </td>
			    <?php } ?>
			</tr>
		</table>
	</form>        
        <table class="ste-data-tbl dashboard-heads dataTable" style="width:100%" border="0" cellpadding="0" cellspacing="0" >            
            <thead>
                <tr>
					<th>State Name</th>
					<th>Country Name</th>
					<th>Region Name</th>
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
                        <td><?php if ($this->session->userdata('editAdmin')==1) {?><a class="edit" href="regionsettings/state/update/<?php echo  $customer['stateid'] ?>"><?php echo  $customer['state_name'] ; ?></a><?php } else { echo $customer['state_name']; } ?></td>
						<td><?php echo $customer['country_name']; ?></td>
						<td><?php echo $customer['region_name']; ?></td>
						<td><?php echo date('d-m-Y', strtotime($customer['created'])); ?></td>
						<td><?php echo  $customer['cfnam'].$customer['clnam']; ?></td>   
						<!--td><?php echo  $customer['mfnam']. $customer['mlnam']; ?></td-->
                        <!--td><?php echo  $customer['modified'] ;?></td-->
                        <td>
				<?php 
				if($customer['inactive']==0)echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>";			
				?>
			</td>                         
					<td class="actions">
						<?php if ($this->session->userdata('editAdmin')==1) {?><a class="edit" href="regionsettings/state/update/<?php echo $customer['stateid']; ?>"><?php echo  "Edit"; ?></a> <?php } else echo "Edit"; ?>                    
						<?php if ($this->session->userdata('deleteAdmin')==1) {?> | <a class="delete" href="regionsettings/state_delete/delete/<?php echo $customer['stateid']; ?>" onclick="return confirm('Are you sure you want to delete?')"><?php echo "Delete"; ?></a><?php } ?>
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