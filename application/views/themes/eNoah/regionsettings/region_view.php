
<div id="content">
    <div class="inner">
		<div class="in-content"> 
		<script type="text/javascript" src="assets/js/regionsettings/region_view.js"></script>
		<?php if(($this->session->userdata('viewAdmin')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('addAdmin')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('editAdmin')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
			<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Region Details</h2>
			<?php if ($this->validation->error_string != '') { ?>
				<div class="form_error">
					<?php echo  $this->validation->error_string ?>
				</div>
			<?php } ?>
			<p>All mandatory fields marked * must be filled in correctly.</p>
			<form name="region_form" id="region_form" action="<?php echo  $this->uri->uri_string() ?>" method="post">
			
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
			<table class="layout">
				<tr>
					<td width="100">Region: *</td>
					<td width="240"><input type="text" id="region_name" name="region_name" value="<?php echo  $this->validation->region_name; ?>" class="textfield width200px required" /></td>
					<td class="error" style="color:red; display:none;" id="error1">Region Field required.</td>
					<td class="checkUser" style="color:green; display:none;">Region Name Available.</td>
					<td class="checkUser1" style="color:red; display:none;">Region Name Already Exists.</td>
					<input type="hidden" class="hiddenUrl"/>
				</tr>	
				<tr>
					<td>Status:</td>
					<td colspan="3">
						<input type="checkbox" name="inactive" value="1" <?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?>
						<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>> 
						<?php if ($cb_status != 0) echo "One or more User / Customer currently assigned for this Region. This cannot be made Inactive."; ?>
						<?php if (($this->validation->inactive == 0) && ($cb_status == 0)) echo "Check if the User need to be Inactive."; ?>
						<?php if ($this->validation->inactive != 0) echo "Uncheck if the User need to be Active."; ?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<?php if (isset($this_user) && $userdata['userid'] == $this_user) { ?>
					<td colspan="3">
						Active Region cannot be modified! Please use my account to update your details.
					</td>
					<?php } else if (isset($this_user_level) && $userdata['level'] >= $this_user_level && $userdata['level'] != 0) { ?>
					<td colspan="3">
						Your Region level cannot updated similar levels or levels above you.
					</td>
					<?php } else { ?>
					<td style="float:left;">
						<div class="buttons">
							<button type="submit" name="update_region" class="positive">								
							<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Region
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
		
		<h2>Region List</h2>
		
		<div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
		
		<table class="reg-data-tbl dashboard-heads dataTable" style="width:100%" border="0" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th>Region Name</th>
					<th>Created Date</th>
					<th>Created By</th>										
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>            
		<tbody>
		<?php if (is_array($customers) && count($customers) > 0) { ?>
			<?php foreach ($customers as $customer) {  ?>
				<tr>
					<td><?php if ($this->session->userdata('editAdmin')==1) {?><a class="editReg clrmarron" href="regionsettings/region/update/<?php echo  $customer['regionid'] ?>"><?php echo  $customer['region_name'] ; ?></a><?php } else { echo $customer['region_name']; } ?></td>
					<td><?php echo  date('d-m-Y', strtotime($customer['created'])); ?></td>
					<td><?php echo  $customer['cfnam']." ".$customer['clnam']; ?></td>
					<td>
						<?php 
						if($customer['inactive']==0){
							echo "<span class=label-success>Active</span>";
						} else { 
							echo "<span class=label-warning>Inactive</span>"; 
						}				
						?>
					</td> 
					<td class="actions">
						<?php if ($this->session->userdata('editAdmin')==1) { ?><a class="editReg clrmarron" href="regionsettings/region/update/<?php echo $customer['regionid']; ?>"><?php echo "Edit"; ?></a> <?php } else echo "Edit"; ?>
						<?php if($this->session->userdata('deleteAdmin')==1) { ?> | <a class="delete clrmarron" href="javascript:void(0)" onclick="return checkStatus(<?php echo $customer['regionid'] ?>);" ><?php echo "Delete"; ?></a><?php } ?>
						<!--<?php if ($this->session->userdata('deleteAdmin')==1) { ?> | <a class="delete clrmarron" href="regionsettings/region_delete/delete/<?php echo $customer['regionid']; ?>" onclick="return confirm('Are you sure you want to delete?')"><?php echo "Delete"; ?></a> <?php } ?>-->
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
