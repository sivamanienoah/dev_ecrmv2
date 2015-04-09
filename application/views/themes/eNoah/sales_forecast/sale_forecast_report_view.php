<?php require (theme_url().'/tpl/header.php'); ?>
<style>
	.hide-calendar .ui-datepicker-calendar { display: none; }
	button.ui-datepicker-current { display: none; }
</style>
<?php $username = $this->session->userdata('logged_in_user'); ?>
<div id="content">
    <div class="inner">
	
<?php
	if($this->session->userdata('accesspage')==1) { ?>
	
		<div style="padding-bottom: 10px;">
			<div style="width:100%; border-bottom:1px solid #ccc;">
				<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
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
								<td class="tblheadbg">By Leads/Projects</td>
								<td class="tblheadbg">For the Month & Year</td>
							</tr>
							<tr>	
								<td>
									<select multiple="multiple" id="entity" name="entity[]" class="advfilter" style="width:150px;">
										<?php foreach($entity as $ent) { ?>
											<option value="<?php echo $ent['div_id']; ?>"><?php echo $ent['division_name']; ?></option>
										<?php } ?>					
									</select> 
								</td>
								<td>
								
									<select multiple="multiple" id="customer" name="customer[]" class="advfilter" style="width:195px;">
										<?php 
											if(!empty($customers)) {
											foreach($customers as $cust) {
										?>
												<option value="<?php echo $cust['custid']; ?>"><?php echo $cust['company'].' - '.$cust['first_name'].' '.$cust['last_name']; ?></option>
										<?php
											}
										}
										?>
									</select> 
								</td> 
								<td>
									<select multiple="multiple" id="lead_ids" name="lead_ids[]" class="advfilter" style="width: 200px;">
										<?php 
											if(!empty($leads)) {
											foreach($leads as $ld) {
										?>
												<option value="<?php echo $ld['lead_id']; ?>"><?php echo $ld['lead_title']; ?></option>
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
						<th rowspan=2>Customer</th>
						<th rowspan=2>Lead/Project Name</th>
						<th rowspan=2>Type</th>
						<?php
							$current_month = date('Y-m');
							$k = 1;
							
							for($i=$current_month; $i<=date('Y-m', strtotime($highest_month)); $i++) {
						?>
								<th colspan=2>
									<?php echo date('M', strtotime($current_month)); ?>
								</th>
								
						<?php
								$month_arr[] = date('M', strtotime($current_month));
								$current_month = date("Y-m", strtotime('+'.$k.' month'));
								$k++;
							}
						?>
					</tr>
					<tr>
						<?php for($a=0;$a<count($month_arr);$a++) { ?>
							<th>Milestone</th>
							<th>Value</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($report_data as $fc_data=>$ms_data) { ?>
					<tr>
						<td><?php echo $ms_data['customer']; ?></td>
						<td><?php echo $ms_data['lead_name']; ?></td>
						<td><?php echo $ms_data['type']; ?></td>
						<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
							<?php foreach($month_arr as $mon) { ?>
								<td>
								<?php foreach($ms_data['milestones'] as $key=>$val) {
									if($mon == $key)
									echo "$mon == $key"; ?>
									
								<?php } ?>	
							<?php } ?><!-- j for loop-->
							</td>
						<?php } ?><!-- if condition-->
					</tr>
					<?php } ?><!-- foreach loop-->
				</tbody>
			</table>
		</div>
<?php
	} else {
		echo "You have no rights to access this page";
	}
?>
	</div><!--Inner div close-->
</div><!--Content div close-->
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_report_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
