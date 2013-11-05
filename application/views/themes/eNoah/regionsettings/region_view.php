<div id="content">
    <div class="inner">
		<div class="in-content"> 
		<?php if(($this->session->userdata('accesspage')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('edit')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
			<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Region Details</h2>
			<?php if ($this->validation->error_string != '') { ?>
				<div class="form_error">
					<?php echo  $this->validation->error_string ?>
				</div>
			<?php } ?>
			<p>All mandatory fields marked * must be filled in correctly.</p>
			<form name="region_form" id="region_form" action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkForm();">
			
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
			<table class="layout">
				<tr>
					<td width="100">Region:</td>
					<td width="240"><input type="text" id="region_name" name="region_name" value="<?php echo  $this->validation->region_name ?>" class="textfield width200px required" /> *</td>
					<td class="error" style="color:red;" id="error1">Region Field required.</td>
					<td class="checkUser" style="color:green" >Region Name Available.</td>
					<td class="checkUser1" style="color:red" >Region Name Already Exists.</td>
					<input type="hidden" class="hiddenUrl"/>
				</tr>	
				<tr>
					<td>Status:</td>
					<td colspan="3"><input type="checkbox" name="inactive" value="1"<?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the region is inactive .</td>
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
					<td colspan="2">
						<?php if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && 1 == 1) { # 1 == 2 do not delete users ?>
						<!--div class="buttons">
						    <button type="submit" name="delete_region" class="negative" onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
							Delete Region
						    </button>
						</div-->
						<?php } else { echo "&nbsp;"; } ?>
					</td>
					<?php } ?>
				</tr>
			</table>
		</form>	
	        <h2>Region List</h2>
        
        <form action="regionsettings/region_settings/" method="post" id="cust_search_form">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
                    <td>
                        Search by Region
                    </td>
                    <td>
                        <input type="text" id="search-val" name="cust_search" class="textfield width200px" />
                    </td>
                    <td>
                        <div class="buttons">
                            <button type="submit" class="search">
                                
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
		<table id="reg-data-tbl" class="dashboard-heads dataTable" style="width:100%" border="0" cellpadding="0" cellspacing="0">
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
		<?php
		if (is_array($customers) && count($customers) > 0) { 
		?>
		<?php foreach ($customers as $customer) {  ?>
			<tr>
                        <td><?php if ($this->session->userdata('edit')==1) {?><a class="edit" href="regionsettings/region/update/<?php echo  $customer['regionid'] ?>"><?php echo  $customer['region_name'] ; ?></a><?php } else { echo $customer['region_name']; } ?></td>
                        <td><?php echo  date('d-m-Y', strtotime($customer['created'])); ?></td>
						<td><?php echo  $customer['cfnam'].$customer['clnam']; ?></td>   
						<!--<td><?php echo  $customer['mfnam']. $customer['mlnam']; ?></td>                        
                        <td><?php echo  $customer['modified'] ;?></td>-->
                        <td>
				<?php 
				if($customer['inactive']==0){
					echo "<span class=label-success>Active</span>";
				} else { echo "<span class=label-warning>Inactive</span>"; }				
				?>
			</td> 
			<td class="actions">
				<?php if ($this->session->userdata('edit')==1) { ?><a class="edit" href="regionsettings/region/update/<?php echo $customer['regionid']; ?>"><?php echo  "Edit"; ?></a> <?php } else echo "Edit"; ?>
				<?php if ($this->session->userdata('delete')==1) { ?> | <a class="delete" href="regionsettings/region_delete/delete/<?php echo $customer['regionid']; ?>" onclick="return confirm('Are you sure you want to delete?')"><?php echo "Delete"; ?></a> <?php } ?>
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
	<script type="text/javascript" src="assets/js/regionsettings/region_view.js"></script>