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
		<?php
		if($this->session->userdata('accesspage')==1) 
		{
		?>
		<form action="" id="pjt_search_form" name="pjt_search_form" method="post" style="float:right;">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
			<table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
					<td>
                        Project Search
                    </td>
					<td>
                        <input type="text" name="keyword" id="keywordpjt" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Project No, Project Title, Name or Company' ?>" class="textfield width210px pjt-search" />
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
		
		<a class="choice-box" onclick="advanced_filter_pjt();">
			Advanced Filters
			<img src="assets/img/advanced_filter.png" class="icon leads" />
		</a>
		
		<div id="advance_search_pjt" style="float:left; width:100%;" >
		
			<form name="advanceFilters_pjt" id="advanceFilters_pjt"  method="post">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<table border="0" cellpadding="0" cellspacing="0" class="data-table">
				<thead>
					<tr>
						<th>By Project Status Wise</th>
						<th>By Project Manager Wise</th>
						<th>By Customer Wise</th>
						<th>By Services Wise</th>
					</tr>	
				</thead>
				<tbody>
				<tr>	
					<td>
						<select style="width:200px;" multiple="multiple" id="pjt_stage" name="pjt_stage[]">
							<option value="1">Project In Progress</option>
							<option value="2">Project Completed</option>
							<option value="3">Project Onhold</option>
							<option value="4">Inactive</option>
						</select> 
					</td>
					
					<td>
						<select style="width:200px;" multiple="multiple" id="pm_acc" name="pm_acc[]">
							<?php foreach($pm_accounts as $pm_acc) {?>
							<option value="<?php echo $pm_acc['userid']; ?>">
							<?php echo $pm_acc['first_name'].' '.$pm_acc['last_name']?></option>	
							<?php } ?>
						</select> 
					</td>
					
					<td>
						<select style="width:200px;" multiple="multiple" id="customer1" name="customer1[]">
							<?php foreach($customers as $customer) {?>
							<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>	
							<?php } ?>
						</select>
					</td>
					<td>
						<select style="width:200px;" multiple="multiple" id="services" name="services[]">
							<?php foreach($services as $service) {?>
							<option value="<?php echo $service['sid']; ?>"><?php echo $service['services'];?></option>	
							<?php } ?>
						</select>
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
		
		<form name="project-total-form" onsubmit="return false;" style="clear:right; overflow:visible;">
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		<div style="text-align:right"><a id="excel" class="export-btn">Export to Excel</a></div>
		<div id="ad_filter" class="custom_dashboardfilter" style="overflow:scroll; margin-top:15px;" >
		<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:1250px !important;">
            
            <thead>
				<th width="82px;">Action</th>
				<th>Project Title</th>
				<th>Project Completion (%)</th>
				<th>Project Type</th>
				<th>Planned Hours</th>
				<th>Billable Hours</th>
				<th>Internal Hours</th>
				<th>Non-Billable Hours</th>
				<th>Total Utilized Hours (Actuals)</th>
				<th>Effort Variance</th>
				<th>Project Value (<?php echo $default_cur_name; ?>)</th>
				<th>Utilization Cost</th>
				<th>P&L </th>
				<th>P&L %</th>
				<th>RAG Status</th>
            </thead>
            
            <tbody>
				<?php
				if (is_array($project_record) && count($project_record) > 0) {
					foreach ($project_record as $record) {
						if(!empty($record['pjt_id'])) {
							$timsheetData = $this->project_model->get_timesheet_hours($record['pjt_id'], $record['lead_id']);
						} else {
							$timsheetData = '';
						}
				?>
                    <tr>
						<td class="actions" align="center">
							<a href="project/view_project/<?php echo $record['lead_id'] ?>">
								View &raquo;
							</a>
							<?php
								if($this->session->userdata('delete')==1) {
								$tle = str_replace("'", "\'", $record['lead_title']);
							?>
								| <a class="delete" href="javascript:void(0)" onclick="return deleteProject(<?php echo $record['lead_id']; ?>, '<?php echo $tle; ?>'); return false; "> Delete &raquo; </a> 
							<?php } ?>
						</td>
                        <td class="actions">							
							<div>
								<a style="color:#A51E04; text-decoration:none;" href="project/view_project/<?php echo $record['lead_id'] ?>"><?php echo character_limiter($record['lead_title'], 35); ?></a>
							</div>
						</td>
						<td class="actions" align="center">
							<?php if (isset($record['complete_status'])) echo ($record['complete_status']) . " %"; else echo "-"; ?>
						</td>
						<td class="actions" align="center">
							<?php 
								if($record['project_type'] == '1'){
									echo 'Fixed';
								}elseif($record['project_type'] == '2'){
									echo 'Internal';
								}elseif($record['project_type'] == '3'){
									echo 'T&amp;M';
								}else{
									echo '-';
								}
							?>
						</td>
						<td class="actions" align="center">
							<?php if (isset($record['estimate_hour'])) echo ($record['estimate_hour']); else echo "-"; ?>
						</td>
						<td class="actions" align="center">
							<?php if (isset($timsheetData->billable)) echo sprintf('%0.2f',$timsheetData->billable); else echo "-"; ?>
						</td>
						<td class="actions" align="center">
							<?php if (isset($timsheetData->internal)) echo sprintf('%0.2f',$timsheetData->internal); else echo "-"; ?>
						</td>
						<td class="actions" align="center">
							<?php if (isset($timsheetData->nonbillable)) echo sprintf('%0.2f',$timsheetData->nonbillable); else echo "-"; ?>
						</td>
						<td class="actions" align="center">
							<?php echo ($timsheetData->billable+$timsheetData->internal+$timsheetData->nonbillable); ?>
						</td>
						
						<td class="actions" align="center">
							<?php echo ($timsheetData->billable+$timsheetData->internal+$timsheetData->nonbillable)-$record['estimate_hour']; ?>
						</td>
						<td class="actions" align="center">
							<?php if (isset($record['actual_worth_amt'])) echo $record['actual_worth_amt']; else echo "-"; ?>
						</td>
						<td class="actions" align="center">
							<?php if (isset($timsheetData->cost)) echo sprintf('%0.2f',$timsheetData->cost); else echo "-"; ?>
						</td>
						<td class="actions" align="center">
							<?php echo ($record['actual_worth_amt']-$timsheetData->cost); ?>
						</td>
						<td class="actions" align="center">
							<?php echo ($record['actual_worth_amt']-$timsheetData->cost)/$record['actual_worth_amt']; ?>
						</td>
						<td class="actions" align="center">
							<?php 
								if (isset($record['rag_status'])) {
									switch ($record['rag_status']) {
										case 1:
											$ragStatus = '<span class=label-inactive>Red</span>';
										break;
										case 2:
											$ragStatus = '<span class=label-amber>Amber</span>';
										break;
										case 3:
											$ragStatus = '<span class=label-success>Green</span>';
										break;
										default:
											$ragStatus = "-";
									}
									echo $ragStatus;
								} else {
									echo "-";
								}
							?>
						</td>
					</tr>
					<?php
					} 
					?>
                <?php 
				} 
				?>
            </tbody>
        </table>
		</div>
		</form>
		<?php 
		} 
		else 
		{
			echo "You have no rights to access this page";
		}
		?>
	</div>
</div>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/projects/projects_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>
