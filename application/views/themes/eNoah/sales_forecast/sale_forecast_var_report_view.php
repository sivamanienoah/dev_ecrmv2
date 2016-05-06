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
	
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			
			<a class="choice-box pull-right" onclick="advanced_filter();" >
				<span>Advanced Filters</span>
				<img src="assets/img/advanced_filter.png" class="icon leads" />
			</a>
			<div class="section-right">
				<div class="buttons export-to-excel">
					<button id="export_excel_variance" type="button" class="positive" onclick="location.href='javascript:void(0);'">
						Export to Excel
					</button>
				</div>
			</div>
			
		</div>
	
		<div id="filter_section">
			<div class="clear"></div>
			
			<div id="advance_search" style="padding-bottom:15px;">
				<form name="advanceFiltersSFVReport" id="advanceFiltersSFVReport" method="post">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<div style="border: 1px solid #DCDCDC;">
						<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
							<tr>
								<td class="tblheadbg">By Entity</td>
								<td class="tblheadbg">By Service</td>
								<td class="tblheadbg">By Practice</td>
								<td class="tblheadbg">By Industry</td>
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
									<select multiple="multiple" id="services" name="services[]" class="advfilter" style="width:100px;">
										<?php foreach($services as $srv) { ?>
											<option value="<?php echo $srv['sid']; ?>"><?php echo $srv['services']; ?></option>
										<?php } ?>					
									</select>
								</td>
								<td>
									<select multiple="multiple" id="practices" name="practices[]" class="advfilter" style="width:100px;">
										<?php foreach($practices as $pr) { ?>
											<option value="<?php echo $pr['id']; ?>"><?php echo $pr['practices']; ?></option>
										<?php } ?>					
									</select>
								</td>
								<td>
									<select multiple="multiple" id="industries" name="industries[]" class="advfilter" style="width:100px;">
										<?php foreach($industries as $ind) { ?>
											<option value="<?php echo $ind['id']; ?>"><?php echo $ind['industry']; ?></option>
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
								<td colspan="7">
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
		<div id='results' style="width:auto; overflow:scroll; overflow-y: hidden;">
			<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" id="var_reports" style="width:100%">
				<thead>
					<tr>
						<th rowspan=2 style="text-align:center;">Entity</th>
						<th rowspan=2 style="text-align:center;">Customer</th>
						<th rowspan=2 style="text-align:center;">Lead/Project Name</th>
						<th rowspan=2 style="text-align:center;">Milestone</th>						
						<?php
							$i = date("Y-m", strtotime($current_month)); 
							while($i <= date("Y-m", strtotime($highest_month))) {
						?>
								<th colspan=2 style="text-align:center;"><?php echo date('M', strtotime($i)); ?></th>
						<?php
								$month_arr[date('Y-m', strtotime($i))] = date('Y-M', strtotime($i));
								$month_no_arr[]                        = date('Y-m', strtotime($i));
								
								if(substr($i, 5, 2) == "12")
								$i = (date("Y", strtotime($i."-01")) + 1)."-01";
								else
								$i++;
							}
						?>
						<tr>
							<?php for($a=0;$a<count($month_arr);$a++) { ?>
								<th>Forecast</th>
								<th>Actual</th>
							<?php } ?>
						</tr>
					</tr>
				</thead>
				<tbody>
					<?php $tot = array(); ?>
					<?php #echo "<pre>"; print_r($report_data); ?>
					<?php foreach($report_data as $lead_id=>$ms_data) { ?>
						<?php foreach($ms_data as $ms_name=>$ms_value) {    ?>
							<tr>
								<td><?php echo $ms_value['entity']; ?></td>
								<td><?php echo $ms_value['customer']; ?></td>
								<td><?php echo $ms_value['lead_name']; ?></td>
								<td><?php echo $ms_name; ?></td>
								<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
									<?php foreach($month_arr as $mon_number=>$mon_val) { ?>
										<?php if(array_key_exists($mon_number, $ms_value)) { ?>
											<td align="<?php echo isset($ms_value[$mon_number]['F']) ? 'right' : 'center'; ?>">
												<?php echo isset($ms_value[$mon_number]['F']) ? number_format($ms_value[$mon_number]['F'], 2, '.', '') : '-'; ?>
												<?php $tot['F'][$mon_number] += $ms_value[$mon_number]['F']; ?>
											</td>
											<td align="<?php echo isset($ms_value[$mon_number]['A']) ? 'right' : 'center'; ?>">
												<?php echo isset($ms_value[$mon_number]['A']) ? number_format($ms_value[$mon_number]['A'], 2, '.', '') : '-'; ?>
												<?php $tot['A'][$mon_number] += $ms_value[$mon_number]['A']; ?>
											</td>
										<?php } else { ?>
											<td align="center">-</td>
											<td align="center">-</td>
										<?php } ?>
									<?php } ?><!-- month_arr foreach loop-->
								<?php } ?><!-- if condition-->
							</tr>
						<?php } ?><!-- ms_data foreach loop-->
					<?php } ?><!-- report_data foreach loop-->
				</tbody>
				<tfoot>
					<tr>
						<td text align=right colspan="4"><strong>Overall Total(<?php echo $default_currency; ?>):</strong></td>
						<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
							<?php foreach($month_arr as $mon_number=>$mon_val) { ?>
								<td align="<?php echo ($tot['F'][$mon_number]!='') ? 'right' : 'center'; ?>">
									<?php echo ($tot['F'][$mon_number]!='') ? number_format($tot['F'][$mon_number],2,'.','') : '-'; ?>
								</td>
								<td align="<?php echo ($tot['A'][$mon_number]!='') ? 'right' : 'center'; ?>">
									<?php echo ($tot['A'][$mon_number]!='') ? number_format($tot['A'][$mon_number],2,'.','') : '-'; ?>
								</td>
							<?php } ?>
						<?php } ?>
					</tr>
				</tfoot>
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
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_var_report_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
