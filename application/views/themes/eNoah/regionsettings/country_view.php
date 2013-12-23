<div id="content">
	<div class="inner">
		<div class="in-content"> 
			<script type="text/javascript" src="assets/js/regionsettings/country_view.js"></script>
			<?php $userdata = $this->session->userdata('logged_in_user'); ?>
			<?php
			if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update' && $userdata['level'] <= 2) || ($this->session->userdata('edit')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) {
			?>

				<form action="<?php echo $this->uri->uri_string() ?>" id="country_form" method="post">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Country Details</h2>
						<?php if ($this->validation->error_string != '') { ?>
							<div class="form_error">
							<?php echo  $this->validation->error_string ?>
							</div>
						<?php } ?>
					<p>All mandatory fields marked * must be filled in correctly.</p>
					<table class="layout">
						<tr>
							<td width="100">Region: *</td><?php $regid = $this->validation->regionid ?>
							<td width="240">
								<select id="country_region_id" name="regionid" class="textfield width200px" ><option value="">Select Region</option><?php if (is_array($regions) && count($regions) > 0) { ?>
								<?php foreach ($regions as $region) { ?><option value="<?php echo $region['regionid']; ?>"<?php if($regid==$region['regionid']) { echo "selected"; } ?>><?php echo  $region['region_name'] ; ?></option><?php } } ?></select>
							</td>
							<td class="error" style="color:red; display:none;" id="error1">Select Region</td>
						</tr>				
						<tr>	
							<td width="100">Country: *</td>
							<td width="240">
								<input id="country_country_name" type="text" name="country_name" value="<?php echo  $this->validation->country_name ?>" class="textfield width200px required" />
							</td>
							<td class="error" style="color:red; display:none;" id="error2">Country Field required.</td>
						</tr>
						<tr>
							<td>Status:</td>
							<td colspan="3">
								<input type="checkbox" name="inactive" value="1" <?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?>
								<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>> 
								<?php if ($cb_status != 0) echo "One or more User / Customer currently assigned for this Country. This cannot be made Inactive."; ?>
								<?php if (($this->validation->inactive == 0) && ($cb_status == 0)) echo "Check if the User need to be Inactive."; ?>
								<?php if ($this->validation->inactive != 0) echo "Uncheck if the User need to be Active."; ?>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td style="float:left;">
								<div class="buttons">
									<button type="submit" name="update_country" id="btnAddCountry" class="positive">
										<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> country
									</button>
								</div>
							</td>
							<td style="float:left;">
								<?php if ($this->uri->segment(4)) { ?>
									<div class="buttons">
										<button type="submit" name="cancel_submit" id="country_cancl" class="negative">
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
				<h2>Country List</h2>
				
				<div class="dialog-err" id="dialog-err-cntry" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
				
				<table class="cntry-data-tbl dashboard-heads dataTable" style="width:100%" border="0" cellpadding="0" cellspacing="0" >            
				<thead>
					<tr>
						<th>Country Name</th>
						<th>Region Name</th>
						<th>Created Date</th>
						<th>Created By</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>            
				<tbody>
					<?php 
						if (is_array($customers) && count($customers) > 0) {
							foreach ($customers as $customer) { 
					?>
							<tr>
								<td>
									<?php if ($this->session->userdata('edit')==1) { ?><a class="editConty clrmarron" href="regionsettings/country/update/<?php echo  $customer['countryid'] ?>"><?php echo $customer['country_name'] ; ?></a><?php } else { echo $customer['country_name']; } ?>
								</td>
								<td><?php echo $customer['region_name']; ?></td>
								<td><?php echo  date('d-m-Y', strtotime($customer['created'])); ?></td>
								<td><?php echo  $customer['cfnam'].$customer['clnam']; ?></td>   
								<td>
									<?php 
									if($customer['inactive']==0)
										echo "<span class=label-success>Active</span>"; 
									else 
										echo "<span class=label-warning>Inactive</span>";
									?>
								</td>  
								<td class="actions">
									<?php if ($this->session->userdata('edit')==1) { ?><a class="editConty clrmarron" href="regionsettings/country/update/<?php echo $customer['countryid']; ?>"><?php echo  "Edit"; ?></a> <?php } else echo "Edit"; ?>
									<?php if($this->session->userdata('delete')==1) { ?> | <a class="delete clrmarron" href="javascript:void(0)" onclick="return checkStatus_Cntry(<?php echo $customer['countryid'] ?>);" ><?php echo "Delete"; ?></a><?php } ?>
								</td>                      
							</tr>																								
						<?php 
							} 
						?>
					<?php 
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