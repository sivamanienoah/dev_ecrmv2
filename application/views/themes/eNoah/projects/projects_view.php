<?php require (theme_url().'/tpl/header.php'); ?>
<?php
$this->load->helper('custom_helper');
if (get_default_currency()) {
	$default_currency = get_default_currency();
	$default_cur_id = $default_currency['expect_worth_id'];
	$default_cur_name = $default_currency['expect_worth_name'];
} else {
	$default_cur_id = '1';
	$default_cur_name = 'USD';
}
?>
<div id="content">
	<div class="inner">
		<?php if($this->session->userdata('accesspage')==1) { ?>
			<form action="" id="pjt_search_form" name="pjt_search_form" method="post" style="float:right;">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
				<table border="0" cellpadding="0" cellspacing="0" class="search-table">
					<tr>
						<td>
							Project Search
						</td>
						<td>
							<input type="text" name="keyword" id="keywordpjt" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Project Title, Name or Company' ?>" class="textfield width210px pjt-search" />
						</td>
						<td rowspan=2>
							<div class="buttons">
								<button type="submit" class="positive" id="project_search">Search</button>
							</div>
						</td>
					</tr>
				</table>
			</form>
		
			<h2><?php echo $page_heading ?></h2>
			<div id="project_note" class="leadstg_note" style="width: 95%;">By default displays only the project(s) in "In Progress" status</div>
		
			<a class="choice-box" onclick="advanced_filter_pjt();">Advanced Filters<img src="assets/img/advanced_filter.png" class="icon leads" /></a>
		
			<div id="advance_search_pjt" style="float:left; width:100%;" >
		
				<form name="advanceFilters_pjt" id="advanceFilters_pjt"  method="post">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table border="0" cellpadding="0" cellspacing="0" class="data-table">
					<thead>
						<tr>
							<th>By Project Status Wise</th>
							<!--th>By Project Manager Wise</th-->
							<th>By Customer Wise</th>
							<th>By Services Wise</th>
							<th>By Practices</th>
							<th>By Date(Actual)</th>
						</tr>	
					</thead>
					<tbody>
					<tr>	
						<td>
							<select style="width:125px;" multiple="multiple" id="pjt_stage" name="pjt_stage[]">
								<option value="1">Project In Progress</option>
								<option value="2">Project Completed</option>
								<option value="3">Project Onhold</option>
								<option value="4">Inactive</option>
							</select>
						</td>
						
						<!--td>
							<select style="width:150px;" multiple="multiple" id="pm_acc" name="pm_acc[]">
								<?php foreach($pm_accounts as $pm_acc) {?>
								<option value="<?php echo $pm_acc['userid']; ?>">
								<?php echo $pm_acc['first_name'].' '.$pm_acc['last_name']?></option>	
								<?php } ?>
							</select> 
						</td-->
						
						<td>
							<select style="width:210px;" multiple="multiple" id="customer1" name="customer1[]">
								<?php foreach($customers as $customer) {?>
								<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>	
								<?php } ?>
							</select>
						</td>
						<td>
							<select style="width:170px;" multiple="multiple" id="services" name="services[]">
								<?php foreach($services as $service) {?>
								<option value="<?php echo $service['sid']; ?>"><?php echo $service['services'];?></option>	
								<?php } ?>
							</select>
						</td>
						<td>
							<select style="width:150px;" multiple="multiple" id="practices" name="practices[]">
								<?php foreach($practices as $pract) {?>
								<option value="<?php echo $pract['id']; ?>"><?php echo $pract['practices'];?></option>	
								<?php } ?>
							</select>
						</td>
						<td>
							<select style="width:178px;" id="datefilter" name="datefilter">
								<option value="1">All</option>
								<option value="2">Start Date</option>
								<option value="3">End Date</option>
							</select>
							<br />
							From <input type="text" name="from_date" id="from_date" class="pick-date textfield" style="width:57px;" />
							To <input type="text" name="to_date" id="to_date" class="pick-date textfield" style="width:57px;" />
						</td>
					</tr>
					<tr align="right" >
						<td colspan="5"><input type="reset" class="positive" name="advance_pjt" value="Reset" />
						<input type="submit" class="positive" name="advance_pjt" id="advance" value="Search" />
						<div id = 'load' style = 'float:right;display:none;height:1px;'>
							<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
						</div>
						</td>
					</tr>
					</tbody>
					</table>
				</form>
			</div>
			<div class="clearfix"></div>
			<div style="text-align:right"><a id="excel" class="export-btn">Export to Excel</a></div>
			
			<h2>Legend</h2>
			<div align="left" style="background: none repeat scroll 0 0 #3b5998;">
				<!--Legends-->
				<div class="legend">
					<div class="pull-left"><img src="assets/img/completion.png"><span> - Completion Percentage</span></div>
					<div class="pull-left"><img src="assets/img/type.png"><span> - Project Type</span></div>
					<div class="pull-left"><img src="assets/img/planned-hour.png"><span> - Planned Hours</span></div>
					<div class="pull-left"><img src="assets/img/billable-hour.png"><span> - Billable Hours</span></div>
					<div class="pull-left"><img src="assets/img/internal-hour.png"><span> - Internal Hours</span></div>
					<div class="pull-left"><img src="assets/img/non-billable.png"><span> - Non Billable Hours</span></div>
					<div class="pull-left"><img src="assets/img/total-hours.png"><span>- Total Utilized Hours</span></div>
					<div class="pull-left"><img src="assets/img/project_value.png"><span> - Project Value </span></div>
					<div class="pull-left"><img src="assets/img/utilization_project_value.png"><span> - Utilization Cost</span></div>
					<div class="pull-left"><img src="assets/img/profitloss.png"><span> - P&L </span></div>
				</div>
			</div>
			<div id="ajax_loader" style="margin:20px;display:none" align="center">
				Loading Content.<br><img alt="wait" src="<?php echo base_url().'assets/images/ajax_loader.gif'; ?>"><br>Thank you for your patience!
			</div>
			<div id="ad_filter" class="custom_dashboardfilter" style="margin-top:15px;">
				
			</div>
		<?php 
		} else {
			echo "You have no rights to access this page";
		}
		?>
	</div>
</div>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/projects/projects_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>
