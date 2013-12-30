<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3)=="") || ($this->session->userdata('edit')==1 && $this->uri->segment(2) == 'add' && is_numeric($this->uri->segment(3)))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<input type="hidden" value="<?php echo $toDB; ?>" name="toDB">
			
            <h2><?php echo ($this->uri->segment(2) == 'add' && is_numeric($this->uri->segment(3))) ? 'Update' : 'New' ?> Package Type</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="120">Package Name: *</td>
					<td width="300">
                        <input type="text" name="package_name" value="<?php echo  (!empty($package_name)) ? $package_name : $this->validation->package_name; ?>" class="textfield width200px"/>
                        <input type="hidden" name="customer_id" id="cust_id" value="<?php echo  (isset($customer_id)) ? $customer_id : '' ?>" />
                    </td>
				</tr>
				<tr>
					<td>Package Price: *</td>
					<td><input type="text" name="package_price" value="<?php echo  (!empty($package_price)) ? $package_price : $this->validation->package_price; ?>" class="textfield width200px required" /> </td>
				</tr>
				<?php
				unset($p);
				(!empty($typeid_fk)) ? $p=$typeid_fk : $p=$this->validation->typeid_fk;
				?>
				<tr>
					<td>Package Type: *</td>
					<td><select name="typeid_fk" class="textfield width200px required">
					<option value="">Select Package Type</option>
					<?php
					foreach($type as $val){
						?>
						<option value="<?php echo $val['type_id']; ?>" <?php if($p==$val['type_id']) echo 'selected="selected"'; ?>><?php echo $val['package_name']; ?></option>
						<?php
					}
					?>
					</select></td>
				</tr>
				<?php
				unset($p);
				(!empty($duration)) ? $p=$duration : $p=$this->validation->duration;
				$duration=array(1=>'1 Month',3=>'3 Months',6=>'6 Months',12=>'12 Months',24=>'24 Months');
				
				?>
				<tr>
					<td>Duration: *</td>
					<td><select name="duration" class="textfield width200px required">
					<option value="">Select Duration</option>
					<?php
					foreach($duration as $key=>$val){
						?>
						<option value="<?php echo $key; ?>" <?php if($p==$key) echo 'selected="selected"'; ?>><?php echo $val; ?></option>
						<?php
					}
					?>
					</select></td>
                    
				</tr>
				
				<?php
				unset($p);
				(!empty($status)) ? $p=$status : $p=$this->validation->status;
				?>
				<tr>
					<td>Status: *</td>
					<td><select name="status" class="textfield width200px required">
					<option value="">Select Status</option>
					<option value="active" <?php if($p=='active') echo 'selected="selected"'; ?>>Active</option>
					<option value="inactive"<?php if($p=='inactive') echo 'selected="selected"'; ?>>Inactive</option>
					</select></td>
                    
				</tr>
				<tr>
					<td>Quotation details: </td>
					<td><textarea name="details" rows="20" cols="80"><?php echo  (!empty($details)) ? $details : $this->validation->details; ?></textarea></td>
                    
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_customer" class="positive">
								<?php echo  ($toDB=='update') ? 'Update' : 'Add' ?> Package Type
							</button>
						</div>
						<div class="buttons">
							<button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>package'">
								Cancel
							</button>
						</div>
                    </td>
                    <td>
                        <?php if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && $userdata['level'] < 1) { ?>
                        <div class="buttons">
                            <button type="submit" name="delete_account" class="negative" onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
                                Delete Account
                            </button>
                        </div>
                        <?php } else { echo "&nbsp;"; } ?>
                    </td>
                    <td>&nbsp;</td>
				</tr>
            </table>
		</form>
		<?php } else{
			echo "You have no rights to access this page";
		}?>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>
