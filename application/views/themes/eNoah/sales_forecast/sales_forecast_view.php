<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata  = $this->session->userdata('logged_in_user');
$bill_info = $this->config->item('crm');
$bill_type = $bill_info['billing_type'];
?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
</style>
<div id="content">
	<div class="inner">
	<?php if($this->session->userdata('accesspage')==1) { ?>
	
	<div style="padding-bottom: 10px;">
		<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<?php if($this->session->userdata('add')==1) { ?>
				<div class="buttons pull-right">
					<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>sales_forecast/add_sale_forecast'">
						Add Sale Forecast
					</button>
				</div>
			<?php } ?>
		<div class="clearfix"></div>
		</div>
	</div>
	
	<div id="filter_section">
		<a class="choice-box" onclick="advanced_filter();" >
		Advanced Filters
		<img src="assets/img/advanced_filter.png" class="icon leads" />
		</a>
			
		<div class="clear"></div>
			
		<div id="advance_search" style="padding-bottom:15px;">
			<form name="advanceFiltersForecast" id="advanceFiltersForecast" method="post" style="width:940px;">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<?php //echo '<pre>'; print_r($sales_divisions);?>
				<div style="border: 1px solid #DCDCDC;">
					<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
						<tr>
							<td class="tblheadbg">By Entity</td>
							<td class="tblheadbg">By Customers</td>
							<td class="tblheadbg">By Projects</td>
							<td class="tblheadbg">For the Month & Year</td>
						</tr>
						<tr>	
							<td>
								<select multiple="multiple" id="entity" name="entity[]" class="advfilter" style="width:200px;">
									<?php foreach($entity as $ent) { ?>
										<option value="<?php echo $ent['div_id']; ?>"><?php echo $ent['division_name']; ?></option>
									<?php } ?>					
								</select> 
							</td>
							<?php $customer_name = array(); ?>
							<?php $lead_name 	 = array(); ?>
							<?php
								if (is_array($sales_forecast) && count($sales_forecast) > 0) {
									foreach($sales_forecast as $forecast) { 
										if(!in_array($forecast['customer_name'], $customer_name))
										$customer_name[] = $forecast['customer_name'];
										if(!in_array($forecast['lead_name'], $lead_name))
										$lead_name[]     = $forecast['lead_name'];
									}
								}
							?>
							<td>
								<select multiple="multiple" id="customer" name="customer[]" class="advfilter" style="width:200px;">
									<?php 
										if(!empty($customer_name)) {
										array_unique($customer_name);
										foreach($customer_name as $cust) {
									?>
											<option value="<?php echo $cust; ?>"><?php echo $cust; ?></option>
									<?php
										}
									}
									?>
								</select> 
							</td> 
							<td>
								<select multiple="multiple" id="lead_names" name="lead_names[]" class="advfilter">
									<?php 
										if(!empty($lead_name)) {
										array_unique($lead_name);
										foreach($lead_name as $lead) {
									?>
											<option value="<?php echo $lead; ?>"><?php echo $lead; ?></option>
									<?php
										}
									}
									?>
								</select> 
							</td>
							<td>
								From <input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" style="width:78px;" />
								<br />
								To <input type="text" data-calendar="false" name="month_year_to_date" id="month_year_to_date" class="textfield" style="width:78px; margin-left: 13px;" />
							</td>
						</tr>
						<tr align="right" >
							<td colspan="6">
								<input type="reset" class="positive input-font" name="advance" id="filter_reset" value="Reset" />
								<input type="submit" class="positive input-font" name="advance" id="advance" value="Search" />
								<div id = 'load' style = 'float:right;display:none;height:1px;'>
									<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
								</div>
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
	<div class="clear"></div>
	<div id='results'>
		<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
			<thead>
				<tr>
					<th>Entity</th>
					<th>Customer Name</th>
					<th>Lead/Project</th>
					<th>Billing Type</th>
					<th>Value</th>
					<th>Created By</th>
					<th>Created On</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php if (is_array($sales_forecast) && count($sales_forecast) > 0) { ?>
					<?php foreach($sales_forecast as $forecast) { ?>
						<tr>
							<td><?php echo $forecast['division_name']; ?></td>
							<td><?php echo $forecast['company']; ?></td>
							<td><?php echo $forecast['lead_name']; ?></td>
							<td><?php echo $bill_type[$forecast['billing_type']]; ?></td>
							<td><?php echo $forecast['lead_value']; ?></td>
							<td><?php echo $forecast['first_name']. ' ' .$forecast['last_name']; ?></td>
							<td><?php echo date('d-m-Y', strtotime($forecast['created_on'])); ?></td>
							<td class="actions">
								<?php if($this->session->userdata('edit')==1) { ?>
									<a href="sales_forecast/add_sale_forecast/update/<?php echo $forecast['forecast_id']; ?>" title='Edit' ><img src="assets/img/edit.png" alt='edit'> </a>
								<?php } ?> 
								<?php if($this->session->userdata('delete')==1) { ?>
									<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $forecast['forecast_id']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a>
								<?php } ?>
								<div class="dialog-err pull-right" id="dialog-message-<?php echo $forecast['forecast_id']; ?>" style="display:none"></div>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php 
	} else { 
		echo "You have no rights to access this page"; 
	} 
	?>
	</div><!--Inner div-close here -->
</div><!--Content div-close here -->
<script>
	var sf_updt = '';
	var sf_id = '';
</script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_view.js"></script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_add_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>