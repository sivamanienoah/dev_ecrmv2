<?php require (theme_url().'/tpl/header.php'); ?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
</style>
<?php $usernme = $this->session->userdata('logged_in_user'); ?>
<div id="content">
    <div class="inner">
	<?php if($this->session->userdata('add')==1) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" name="add_sales_forecast" >
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Sale Forecast </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
			<?php
			if($this->uri->segment(3)=='update')
			echo '<input type="hidden" name="varEdit" id="varEdit" value="update" />';
			?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td width="115">Entity: * </td>
					<td width="210">
						<select name="entity" id="entity" class="textfield width160px" >
							<option value=''>Select</option>
							<?php if(!empty($entity)) { ?>
								<?php foreach($entity as $ent) { ?>
									<option value="<?php echo $ent['div_id']; ?>" <?php echo $this->validation->entity == $ent['div_id'] ? 'selected="selected"' : ''; ?>><?php echo $ent['division_name']; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
						<?php if ($this->uri->segment(3) == 'update') { ?>
							<input type="hidden" id="forecast_id" name="forecast_id" value="<?php echo $this->uri->segment(4); ?>" />
						<?php } ?>
					</td>
					<td width="115">Customer: * </td>
					<td width="210">
						<input type="text" name="customer_name" id="customer_name" value="<?php echo $this->validation->customer_name; ?>" class="textfield width160px" />
					</td>
				</tr>
				<tr id="customer_regions">
					<td>Region: * </td>
					<?php if (($usernme['level']>=2) && ($this->uri->segment(3)!='update')) { ?>
					<td width="210" id="def_reg"></td>
					<?php } else { ?>					
					<td width="210">
						<select id="region_id" name="region_id" onchange="getCountry(this.value)" class="textfield width160px required">
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
					<?php 
						if($this->validation->region_id != 0)
						echo '<input type="hidden" name="region_update" id="region_update" value="'.$this->validation->region_id.'" />';
					?>
					<td>Country: * </td>
					<?php if (($usernme['level']>=3) && ($this->uri->segment(3)!='update')) { ?>
					<td width="210" id="def_cntry"></td>
					<?php } else { ?>
					<td width="210" id='country_row'>
						<select id="country_id" name="country_id" class="textfield width160px required" >
							<option value="0">Select Country</option>                           
						</select>
						<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2) { ?>
							<a class="addNew" id="addButton"></a> <!--Display the Add button-->
						<?php } ?>
					</td>
					<?php } ?>
					<?php
						if($this->validation->country_id != 0)
						echo '<input type="hidden" name="country_update" id="country_update" value="'.$this->validation->country_id.'" />';
					?>
				</tr>
				<tr>
					<td>State: * </td>
					<?php if (($usernme['level']>=4) && ($this->uri->segment(3)!='update')) { ?>
						<td width="210" id="def_ste"></td>
					<?php } else { ?>
					<td width="210" id='state_row'>
						<select id="state_id" name="state_id" class="textfield width160px required">
						<option value="0">Select State</option>                           
						</select>
						<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3) { ?>
							<a id="addStButton" class="addNew"></a> <!--Display the Add button-->
						<?php } ?>
					</td>
					<?php } ?>
					<?php
						if($this->validation->state_id != 0)
						echo '<input type="hidden" name="state_update" id="state_update" value="'.$this->validation->state_id.'" />';
					?>
					<td>Location: * </td>
					<?php if (($usernme['level']>=5) && ($this->uri->segment(3)!='update')) { ?>
						<td width="210" id="def_loc"></td>
					<?php } else { ?>
						<td width="210" id='location_row'>
							<select id="location_id" name="location_id" class="textfield width160px required" onchange="getSalescontactDetails(this.value)">
							<option value="0">Select Location</option>                           
							</select>
							<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4) { ?>
								<a id="addLocButton" class="addNew"></a> <!--Display the Add button-->
							<?php } ?>
						</td>
					<?php } ?>
					<?php 
						if($this->validation->location_id != 0)
						echo '<input type="hidden" name="location_update" id="location_update" value="'.$this->validation->location_id.'" />';
					?>
				</tr>
				<tr>
					<td>Lead/Project: * </td>
					<td>
						<input type="text" name="lead_name" id="lead_name" value="<?php echo $this->validation->lead_name; ?>" class="textfield width160px" />
					</td>
					<td>Billing Type:  </td>
					<td>
						<select name="billing_type" class="textfield width160px">
							<option value="milestone">Milestone Based</option>
							<option value="monthly">Monthly Based</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Currency Name: * </td>
					<td>
						<select name="currency_type" class="textfield width160px">
							<option value=""> Select </option>
							<?php if(!empty($currency_type)) { ?>
								<?php foreach($currency_type as $currency) { ?>
									<option value="<?php echo $currency['expect_worth_id']; ?>"><?php echo $currency['expect_worth_name']; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</td>
					<td>Lead/Project Value: * </td>
					<td>
						<input type="text" name="lead_name" id="lead_name" value="<?php echo $this->validation->lead_name; ?>" class="textfield width160px" />
					</td>
				</tr>
				<tr>
					<td>Milestone Name: * </td>
					<td>
						<input type="text" name="milestone" id="milestone" value="<?php echo $this->validation->milestone; ?>" class="textfield width160px" />
					</td>
					<td>Milestone Value: * </td>
					<td>
						<input type="text" name="milestone_value" autocomplete="off" id="milestone_value" value="<?php $this->validation->milestone_value; ?>" class="textfield width160px" />
					</td>
					<td>Month & Year: * </td>
					<td>
						<input type="text" data-calendar="false" name="for_month_year" autocomplete="off" id="for_month_year" value="<?php if(!empty($this->validation->for_month_year)) echo date('F Y', strtotime($this->validation->for_month_year)); ?>" class="textfield width160px" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons" colspan="2">
                        <div class="buttons">
							<button type="submit" name="update_practice" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Sale Forecast
							</button>
						</div>
						<div class="buttons">
                           <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>sales_forecast'">
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
	</div><!--Inner div close-->
</div><!--Content div close-->
<script>
	var token_name  ="<?php echo $this->security->get_csrf_token_name(); ?>";
	var token_value ="<?php echo $this->security->get_csrf_hash(); ?>";
	var customer_user_id = "<?php echo $usernme['userid']; ?>";
	var usr_level 		 = "<?php echo $usernme['level']; ?>";
	var cus_updt		 = "<?php echo ($this->uri->segment(3) == 'update') ? 'update' : 'no_update' ?>";
</script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_add_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
