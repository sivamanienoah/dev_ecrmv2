<?php require (theme_url().'/tpl/header.php'); 
$p=array();
if(!empty($packageid_fk)){
	foreach($packageid_fk as $val){
		$k=$val['packageid_fk'];
		$p[$k]=$val['due_date'];
	}
}
$usernme = $this->session->userdata('logged_in_user');
?>
<style type="text/css">
#domain-expiry-date 
{
	display:none;
}
</style>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<input type="hidden" class="hiddenUrl"/>
<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3)!= 'update') || (($this->session->userdata('edit')==1) && ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Hosting Account Details</h2>
            <?php if (!$this->input->post('domain_name') && $this->uri->segment(3) != 'update') { ?>
            <p class="notice">If this is a new customer, please be sure to <a href="#" class="modal-new-cust" >add the customer</a> to the database before adding the hosting account.</p>
			<?php } ?>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="120">Customer Name:*</td>
					<td width="300">
                        <input type="text" name="customer_name" id="cust_name" value="<?php echo  (isset($customer_name)) ? $customer_name : '' ?>" class="textfield width200px" /> 
                        <input type="hidden" name="customer_id" id="cust_id" value="<?php echo  (isset($customer_id)) ? $customer_id : '' ?>" />
                    </td>
				</tr>
				<tr>
					<td>Domain Name:*</td>
					<td><input type="text" name="domain_name" value="<?php echo  $this->validation->domain_name ?>" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td>Domain Management:</td>
					<td>
						<input type="radio" name="domain_mgmt" value="ENOAH"<?php echo ((!isset($_POST['domain_mgmt']) && !is_null($this->validation->domain_expiry)) || (isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'ENOAH')) ? ' checked="checked"' : '' ?> /> eNoahiSolution &nbsp;&nbsp;
						<input type="radio" name="domain_mgmt" value="CM"<?php echo ((!isset($_POST['domain_mgmt']) && is_null($this->validation->domain_expiry)) || (isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'CM')) ? ' checked="checked"' : '' ?> /> Client Managed &nbsp;&nbsp;
					</td>
				</tr>
				<tr id="domain-expiry-date">
					<td>Domain Expiry Date:*</td>
					<td><input type="text" name="domain_expiry" value="<?php echo  $this->validation->domain_expiry ?>" class="textfield width200px pick-date" /> </td>
                    
				</tr>
				<tr>
					<td>Domain Status:*</td>
					<td>
						<select name="domain_status" class="textfield width200px">
						<?php
							foreach ($this->login_model->cfg['domain_status'] as $key => $value) {
								$selected = ($this->validation->domain_status == $key) ? ' selected="selected"' : ''; ?>
								<option value="<?php echo  $key ?>"<?php echo  $selected ?>><?php echo  $value ?></option>
						<?php	} ?>
						</select> 
					</td>
                </tr>
				<tr>
					<td>Package Name:*</td>
					<td>
						<select name="packageid_fk[]" class="textfield" size=6 multiple=multiple style="width:300px;">
						<option value="">Select Package</option>
						<?php
						if(!empty($package)){
						foreach ($package as $val) {
							if(!empty($p[$val['package_id']])) { 
								$s= ' selected="selected"'; 
								if(strtotime($p[$val['package_id']])>0) $k=' - ('.date('d-m-Y',strtotime($p[$val['package_id']])).')';
								else $k='';
							}
							else { $s=''; $k='';}
							echo '<option value="'.$val['package_id'].'"'.$s.'>'.$val['package_name'].$k.'</option>';
					 } }?>
						</select> 
					</td>
                </tr>
				<tr>
					<td>Hosting Expiry Date:</td>
					<td><input type="text" name="expiry_date" value="<?php echo  $this->validation->expiry_date ?>" class="textfield width200px pick-date" /> </td>
                    
				</tr>
				<tr>
					<td>SSL:</td>
					<td>
						<?php foreach ($this->login_model->cfg['domain_ssl_status'] as $key => $value) { ?>
						<input type="radio" name="ssl" value="<?php echo $key ?>"<?php echo ($this->validation->ssl == $key) ? ' checked="checked"' : '' ?> /> <?php echo $value ?> &nbsp;&nbsp;
						<?php } ?>
					</td>
                    
				</tr>
				<tr>
					<td>Other information:</td>
					<td><textarea name="other_info" class="textfield width200px"><?php echo  $this->validation->other_info ?></textarea></td>
                    
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_customer" class="positive">
								
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Account
							</button>
						</div>
                    </td>
                    <td>&nbsp;</td>
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
<?php require (theme_url().'/tpl/footer.php'); ?>
<script type="text/javascript">
	hosting_userid = "<?php echo $usernme['userid']; ?>";
</script>
<script type="text/javascript" src="assets/js/hosting/hosting_add_view.js"></script>