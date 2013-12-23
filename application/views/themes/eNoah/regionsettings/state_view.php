<?php
if($this->validation->regionid != 0) 
echo '<input type="hidden" name="region_update" id="region_update" value="'.$this->validation->regionid.'" />';
?>
<div id="content">	
    <div class="inner">
		<div class="in-content">
		<script type="text/javascript" src="assets/js/regionsettings/state_view.js"></script>

		<?php $userdata = $this->session->userdata('logged_in_user'); ?>
		<?php
		if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update' && $userdata['level'] <= 3) || ($this->session->userdata('edit')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) {
		?>
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
						<td width="100">Region: *</td>
						<td width="240">
						<select name="regionid" id="st_regionid" class="textfield width200px" onchange="getCountryst(this.value)" class="textfield width200px required">
							<option value="0">Select Region</option>
								<?php 
								foreach ($regions as $region) { ?>
									<option value="<?php echo  $region['regionid'] ?>"<?php echo  ($this->validation->regionid == $region['regionid']) ? ' selected="selected"' : '' ?>><?php echo  $region['region_name']; ?></option>
								<?php } ?>
						</select>
						</td>
						<td class="error" id="errorreg" style="color:red; display:none;">Select Region</td>
					</tr>
					<div id="test"></div>
					<tr>
						<td width="100">Country: *</td>
						<?php $cid = $this->validation->countryid ?>
						<td id='country_row' width="240">
							<select id="country_id" name="countryid" style="width:210px;">
								<option value="0">Select Country</option>
								<?php if (is_array($countrys) && count($countrys) > 0) { ?>
								<?php foreach ($countrys as $country) { ?>
								<option value="<?php echo $country['countryid'];?>" <?php if($cid==$country['countryid']) { echo "selected"; } ?>><?php echo $country['country_name']; ?> </option>
								<?php } } ?>
							</select>
						</td>
						<td class="error" id="error1" style="color:red; display:none;">Select Country</td>
					</tr>
					<tr>
						<td width="100">State: *</td>
						<td width="240">
							<input id="state_name" type="text" name="state_name" value="<?php echo $this->validation->state_name ?>" class="textfield width200px required" />
						</td>
						<td class="error" id="error2" style="color:red; display:none;">State Field Required.</td>
					</tr>
					<tr>
						<td>Status:</td>
						<td colspan="3">
							<input type="checkbox" name="inactive" value="1" <?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?>
							<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>> 
							<?php if ($cb_status != 0) echo "One or more User / Customer currently assigned for this State. This cannot be made Inactive."; ?>
							<?php if (($this->validation->inactive == 0) && ($cb_status == 0)) echo "Check if the User need to be Inactive."; ?>
							<?php if ($this->validation->inactive != 0) echo "Uncheck if the User need to be Active."; ?>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td style="float:left;">
							<div class="buttons">
								<button type="submit" name="update_state" class="positive">
									<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> state
								</button>
							</div>
						</td>
						<td style="float:left;">
							<?php if ($this->uri->segment(4)) { ?>
								<div class="buttons">
									<button type="submit" name="cancel_submit" class="negative" id="state_cancl">
									Cancel
									</button>
								</div>
							<?php } ?>
						</td>
					</tr>
				</table>
			</form>
		<?php
		}
		?>
		
		<?php				
			if($this->session->userdata('accesspage')==1) {
		?>
			<h2>State List</h2>
			
			<div class="dialog-err" id="dialog-err-ste" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
			
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
					<?php 
					if (is_array($customers) && count($customers) > 0) {  
						foreach ($customers as $customer) { 
					?>
							<tr>
								<td>
									<?php if ($this->session->userdata('edit')==1) {?><a class="editSte clrmarron" href="regionsettings/state/update/<?php echo  $customer['stateid'] ?>"><?php echo $customer['state_name'] ; ?></a><?php } else { echo $customer['state_name']; } ?>
								</td>
								<td><?php echo $customer['country_name']; ?></td>
								<td><?php echo $customer['region_name']; ?></td>
								<td><?php echo date('d-m-Y', strtotime($customer['created'])); ?></td>
								<td><?php echo $customer['cfnam'].$customer['clnam']; ?></td>
								<td>
									<?php 
									if($customer['inactive']==0)
										echo "<span class=label-success>Active</span>";
									else 
										echo "<span class = label-warning>Inactive</span>"; ?>
								</td>                         
								<td class="actions">
									<?php if ($this->session->userdata('edit')==1) { ?>
										<a class="editSte clrmarron" href="regionsettings/state/update/<?php echo $customer['stateid']; ?>">Edit &raquo;</a> 
									<?php } else echo "Edit &raquo;"; ?>                    
									<?php if($this->session->userdata('delete')==1) { ?> | 
										<a class="delete clrmarron" href="javascript:void(0)" onclick="return checkStatus_Ste(<?php echo $customer['stateid'] ?>);" >Delete &raquo;</a>
									<?php } ?>
								</td>
							</tr>
					<?php 
						}
					}
					?>
				</tbody>            
			</table>
		<?php 
		} else {
			echo "You have no rights to view this content";
		}
		?>
		</div>
	</div>
</div>