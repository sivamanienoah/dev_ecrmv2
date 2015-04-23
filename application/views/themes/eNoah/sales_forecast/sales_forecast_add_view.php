<?php require (theme_url().'/tpl/header.php'); ?>
<style>
	.hide-calendar .ui-datepicker-calendar { display: none; }
	button.ui-datepicker-current { display: none; }
</style>
<?php $username = $this->session->userdata('logged_in_user'); ?>
<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('add')==1) || ($this->session->userdata('edit')==1)) { ?>
	<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Sale Forecast </h2>
	<div class="content-split-left">
    	<form action="<?php echo $this->uri->uri_string() ?>" method="post" id="add_sales_forecast_form" onsubmit="return false;" class='addForm' >
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <?php if ($this->validation->error_string != '') { ?>
				<div class="form_error">
					<?php echo $this->validation->error_string; ?>
				</div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout" id="milestone-tbl">
				<tr>
					<td>Category: *</td>
					<td>
						<input type="radio" name="category" id="category_for_lead" <?php echo ($this->validation->category==1) ?" checked='checked'" : ""; ?> value="1" /> Lead
						<input type="radio" name="category" id="category_for_project" <?php echo ($this->validation->category==2) ?" checked='checked'" : ""; ?> value="2" /> Project
						<?php 
							if($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) {
						?>
								<input type="hidden" name="category" value="<?php echo $salesforecast_category; ?>" />
						<?php
							}
						?>
					</td>
				</tr>
				<tr>
					<td>Customer: * </td>
					<td width="210">
						<select name="customer_id" id="customer_id" class="textfield width160px" onchange="get_records(this.value)" >
							<option value="">Select</option>
						</select>
					</td>
				</tr>
				<tr id="lead-data">
					<td>Lead: *</td>
					<td>
						<select name="job_id" id="lead_job_id" class="textfield width160px" onchange="check_existing_add_saleforecast(this.value)"></select>
					</td>
				</tr>
				<tr id="project-data">
					<td>Project: *</td>
					<td>
						<select name="job_id" id="project_job_id" class="textfield width160px" onchange="check_existing_add_saleforecast(this.value)"></select>
					</td>
				</tr>
				<tr class="show-entity">
					<td>Entity: </td>
					<td>
						<input type="text" readonly name="show-entity" id="show-entity" value="" class="textfield width160px" />
					</td>
				</tr>
				<tr class="show-currency">
					<td>Currency Type: </td>
					<td>
						<input type="text" readonly name="show-currency" id="show-currency" value="" class="textfield width160px" />
					</td>
				</tr>
				<tr class="show-exp-worth">
					<td>Expected Worth: </td>
					<td>
						<input type="text" readonly name="show-exp-worth" id="show-exp-worth" value="" class="textfield width160px" />
					</td>
				</tr>
				<tr class="show-bill-type">
					<td>Billing Type: </td>
					<td>
						<input type="text" readonly name="show-bill-type" id="show-bill-type" value="" class="textfield width160px" />
					</td>
				</tr>
				<tr class="project-ms-detail">
					<td valign="top">Payment Milestone Details:</td>
					<td id="show-project-ms-detail"></td>
				</tr>
				<tr class="project-ms-detail" style="height:3px;"></tr>
				<tr>
					<td>Milestone Name:</td>
					<td>
						<input type="text" name="milestone_name" value="<?php echo $this->validation->milestone; ?>" class="textfield width160px" />
					</td>
				</tr>
				<tr>
					<td>Milestone Value:  </td>
					<td>
						<input type="text" name="milestone_value" autocomplete="off" value="<?php $this->validation->milestone_value; ?>" class="milestone_value textfield width160px" onkeypress="return isNumberKey(event)" />
					</td>
				</tr>
				<tr>
					<td>Month & Year: </td>
					<td>
						<input type="text" data-calendar="false" name="for_month_year" autocomplete="off" value="<?php if(!empty($this->validation->for_month_year)) echo date('F Y', strtotime($this->validation->for_month_year)); ?>" readonly class="for_month_year textfield width160px" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons" colspan="4">
                        <div class="buttons">
							<?php if($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) { ?>
							<button type="submit" onclick="add_sales_forecast(<?php echo $this->uri->segment(4) ?>); return false;" class="positive">
								Add
							</button>
						<?php } else { ?>
							<button type="submit" onclick="add_sales_forecast(); return false;" class="positive">
								Add
							</button>
						<?php } ?>
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
	</div>
	
	<!--table-->
	<div class="content-split-right">
		<div class="table-design">
		<?php if($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) { ?>
			<h4>Saleforecast Milestone Details:</h4>
			<table id="ms_list" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable">
				<thead>
					<tr>
						<th>Milestone Name</th>
						<th>Type</th>
						<th>For the Month & Year</th>
						<th>Currency</th>
						<th>Milestone Value</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php if(!empty($milestone_data)) { ?>
					<?php foreach($milestone_data as $ms_rec) { ?>
						<tr>
							<?php $milestone_month_year = date('d-m-Y', strtotime($ms_rec['for_month_year'])); ?>
							<?php $current_month_year   = date('d-m-Y'); ?>
							<td><?php echo $ms_rec['milestone_name'] ?></td>
							<td>
								<?php 
									if($ms_rec['forecast_category']==1) 
									echo "Lead";
									else if($ms_rec['forecast_category']==2)
									echo "Project";
								?>
							</td>
							<td><?php echo date('F Y', strtotime($ms_rec['for_month_year'])); ?></td>
							<td><?php echo $salesforecast_currency; ?></td>
							<td><?php echo $ms_rec['milestone_value']; ?></td>
							<td class="action">
								<?php if(strtotime($milestone_month_year) > strtotime($current_month_year)) { ?>
								<?php if($this->session->userdata('edit')==1) { ?>
									<a class="table-edit" title="Edit" onClick="editSalesForecast(<?php echo $ms_rec['milestone_id'] ?>); return false;" href="javascript:void(0)">
										<img alt="edit" src="assets/img/edit.png">
									</a>
								<?php } ?> 
									<?php if($this->session->userdata('delete')==1) { ?>
									<a class="table-delete" title="Delete" onclick="return deleteSalesForecast(<?php echo $ms_rec['milestone_id'] ?>); return false;" href="javascript:void(0)">
										<img alt="delete" src="assets/img/trash.png">
									</a>
								<?php } ?>
								<?php } ?>
									<a class="delete table-view-log" href="javascript:void(0)" onclick="return view_logs(<?php echo $ms_rec['milestone_id']; ?>);" title='View Logs'>
										<img src="assets/img/log-icon.png" alt='Logs'>
									</a>
							</td>
						</tr>
					<?php } ?>	
				<?php } ?>
				</tbody>
			</table>
		<?php } ?>
		</div>
	</div>
<!--table-->
<?php 
	} else {
		echo "You have no rights to access this page";
	}
?>
	</div><!--Inner div close-->
</div><!--Content div close-->

<div id="edit_sales_forecast_container"></div>

<div id="view-log-container"></div>

<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script>
var current_user_id = "<?php echo $username['userid'] ?>";
var cur_year 	    = "<?php echo date('Y') ?>";
var cur_month 	= "<?php echo date('m') ?>";
var ms_id       = '<?php echo isset($_GET['ms_id']) ? $_GET['ms_id'] : '' ?>';
var forecast_id = 'no_id';
// sf_categ = 'no_update';
<?php if($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) { ?>
	var job_id      = "<?php echo $salesforecast_data['job_id'] ?>";
	var customer_id = "<?php echo $salesforecast_data['customer_id'] ?>";
	var sf_categ    = "<?php echo $salesforecast_category ?>";
	forecast_id = "<?php echo $this->uri->segment(4) ?>";
<?php } ?>
</script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_add_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
