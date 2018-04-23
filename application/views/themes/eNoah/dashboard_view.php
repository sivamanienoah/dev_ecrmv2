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

<?php
ob_start();
$userdata = $this->session->userdata('logged_in_user');
require (theme_url().'/tpl/header.php');
//echo baseurl();
?>
<?php 
if($this->session->userdata('viewlead')==1) {
?>
	<script language="javascript" type="text/javascript" src="assets/js/jquery.jqplot.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.barRenderer.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.dateAxisRenderer.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.logAxisRenderer.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.pointLabels.min.js"></script>
	<script type="text/javascript" src="assets/js/plugins/jqplot.funnelRenderer.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.pieRenderer.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
	<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.highlighter.min.js"></script>
	
	<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
	<?php 
	// For Chart Title
	switch ($userdata['level']) {
		case 1:
			if ((!empty($filter['regionname'])) && (empty($filter['countryname'])) && (empty($filter['statename'])) && (empty($filter['locname']))) {
				$chart_title = "Leads By Country Wise";
			} else if ((!empty($filter['countryname'])) && (empty($filter['statename'])) && (empty($filter['locname']))) {
				$chart_title = "Leads By State Wise";
			} else if (!empty($filter['statename'])) {
				$chart_title = "Leads By Location Wise";
			} else {
				$chart_title = "Leads By Region Wise";
			}
		break;
		case 2:
			if ((!empty($filter['countryname'])) && (empty($filter['statename'])) && (empty($filter['locname'])))
				$chart_title = "Leads By State Wise";
			else if (!empty($filter['statename']))
				$chart_title = "Leads By Location Wise";
			else 
				$chart_title = "Leads By Country Wise";
		break;
		case 3:
			if (!empty($filter['statename']))
				$chart_title = "Leads By Location Wise";
			else
				$chart_title = "Leads By State Wise";
		break;
		case 4:
		case 5:
			$chart_title = "Leads By Location Wise";
		break;
	}
	?>
<?php 
} 
?>
<?php 
if(($this->session->userdata('viewtask')==1) && ($this->session->userdata('viewlead') != 1)) { 
?>
	<script type="text/javascript">var this_is_home = true;</script>
	<script type="text/javascript">var curr_job_id  = 0;</script>
	<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
	<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>
	<style type="text/css">

	</style>
<?php 
} 
?>
<div id="content">
	<div class="inner">
	<?php if($this->session->userdata('viewlead')==1) { ?>
		<div>
		<!--Advance filters-->
		<div class="page-title-head">
			
			<h2 class="pull-left borderBtm">Dashboard</h2>
			
			<a class="choice-box" onclick="advanced_filter();" >
				<img src="assets/img/advanced_filter.png" class="icon leads" />
				<span>Advanced Filters</span>
			</a>
			
			<div class="search-dropdown">
				<a class="saved-search-head" >
					<p>Saved Search</p>
				</a>
				<div class="saved-search-criteria" style="display: none; ">
					<img class="dpwn-arw" src="assets/img/drop-down-arrow.png" title="" alt="" />
					<ul class="search-root">
					<li class="save-search-heading"><span>Search Name</span><span>Set Default</span><span>Action</span></li>
					<?php 
					if(sizeof($saved_search)>0) {
						foreach($saved_search as $searc) {
					?>
							<li class="saved-search-res" id="item_<?php echo $searc['search_id']; ?>">
								<span><a href="javascript:void(0)" onclick="show_search_results('<?php echo $searc['search_id'] ?>')"><?php echo $searc['search_name'] ?></a></span>
								<span class='rd-set-default'><input type="radio" value="<?php echo $searc['search_id'] ?>" <?php if ($searc['is_default']==1) { echo "checked"; } ?> name="set_default_search" class="set_default_search" /></span>
								<span><a title="Delete" href="javascript:void(0)" onclick="delete_save_search('<?php echo $searc['search_id'] ?>')"><img alt="delete" src="assets/img/trash.png"></a></span>
							</li>
					<?php 
						}
					} else {
					?>
						<li id="no_record" style="text-align: center; margin: 5px;">No Save & search found</li>
					<?php
					}
					?>
					</ul>
				</div>
			</div>

			<div id="advance_search" style="float:left; margin: 0px 0px 10px;width:100%;">
				
				<!--form action="<?php #echo $this->uri->uri_string() ?>" id="advancefilterhome" name="advancefilterhome" method="post" style="width:100%;"-->
				<form action="<?php echo $this->uri->uri_string() ?>" id="advancefilterhome" name="advancefilterhome" method="post" style="width:100%;">
				
					
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<input type="hidden" name="search_type" id="search_type" value="search" />
					
					<div style="border: 1px solid #DCDCDC;">
						<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
							<tr>
								<td class="tblheadbg" style="width:228px">By Lead Stage</td>
								<td class="tblheadbg" style="width:210px;">By Customer</td>
								<td class="tblheadbg">By Lead Owner</td>
								<td class="tblheadbg">By Lead Assignee</td>
								<td class="tblheadbg">By Service Requirement</td>
								<td class="tblheadbg">By Industry</td>
							</tr>
							<tr>	
								<td>
									<select style="width:228px" multiple="multiple" id="stage" name="stage[]" class="advfilter">
										<?php foreach($lead_stage as $ls) { ?>
											<option value="<?php echo $ls['lead_stage_id']; ?>"<?php if (!empty($filter['stage'])) { echo in_array($ls['lead_stage_id'], $filter['stage']) ? 'selected="selected"' : ''; } ?>><?php echo $ls['lead_stage_name']; ?></option>
										<?php } ?>					
									</select> 
								</td>
								<td>
									<select style="width:210px;" multiple="multiple" id="customer" name="customer[]" class="advfilter">
										<?php foreach($customers as $customer) { ?>
											<option value="<?php echo $customer['companyid']; ?>"<?php if (!empty($filter['customer'])) { echo in_array($customer['companyid'], $filter['customer']) ? 'selected="selected"' : ''; } ?>><?php echo $customer['company']; ?></option>
										<?php } ?>
									</select> 
								</td> 
								<td>
									<select style="width:110px;" multiple="multiple" id="owner" name="owner[]" class="advfilter">
										<?php foreach ($lead_owner as $owner) { 
												if(!empty($owner['first_name'])) { ?>
													<option value="<?php echo $owner['userid'] ?>"<?php if (!empty($filter['owner'])) { echo in_array($owner['userid'], $filter['owner']) ? 'selected="selected"' : ''; } ?> title="<?php echo $owner['first_name'].' - '.$owner['emp_id']; ?>"><?php echo $owner['first_name']; ?></option>
										<?php	} 
											} 
										?>
									</select> 
								</td>
								<td>
									<select style="width:110px;" multiple="multiple" id="leadassignee" name="leadassignee[]" class="advfilter">
										<?php foreach ($lead_owner as $ownr) {
												if(!empty($ownr['first_name'])) { ?>
													<option value="<?php echo $ownr['userid'] ?>"<?php if (!empty($filter['leadassignee'])) { echo in_array($ownr['userid'], $filter['leadassignee']) ? 'selected="selected"' : ''; } ?> title="<?php echo $ownr['first_name'].' - '.$ownr['emp_id']; ?>"><?php echo $ownr['first_name']; ?></option>
										<?php 	}
											} 
										?>
									</select> 
								</td>
								<td>
									<select  style="width:150px;" multiple="multiple" id="ser_requ" name="ser_requ[]" class="advfilter">
										<?php if (count($serv_requ)>0) { ?>
											<?php foreach ($serv_requ as $serv) { ?>
												<option value="<?php echo $serv['sid'] ?>"<?php if (!empty($filter['ser_requ'])) { echo in_array($serv['sid'], $filter['ser_requ']) ? 'selected="selected"' : ''; } ?>><?php echo $serv['services'] ?></option>
											<?php } ?>
										<?php } ?>
									</select> 
								</td>
								<td>
									<select  style="width:150px;" multiple="multiple" id="industry" name="industry[]" class="advfilter">
										<?php if (count($industry)>0) { ?>
											<?php foreach ($industry as $ind) { ?>
												<option value="<?php echo $ind['id'] ?>"<?php if (!empty($filter['industry'])) { echo in_array($ind['id'], $filter['industry']) ? 'selected="selected"' : ''; } ?>><?php echo $ind['industry'] ?></option>
											<?php } ?>
										<?php } ?>
									</select> 
								</td>
							</tr>
							<tr>
								<td class="tblheadbg">By Lead Source</td>
								<td class="tblheadbg">By Region Wise</td>
								<td class="tblheadbg">By Country Wise</td>
								<td class="tblheadbg">By State Wise</td>
								<td class="tblheadbg">By Location Wise</td>
								<td class="tblheadbg">By Lead Indicator</td>
							</tr>
							<tr>
								<td>
									<select  style="width:228px;" multiple="multiple" id="lead_src" name="lead_src[]" class="advfilter">
										<?php if (count($lead_sourc)>0) { ?>
											<?php foreach ($lead_sourc as $srcs) { ?>
												<option value="<?php echo $srcs['lead_source_id'] ?>"<?php if (!empty($filter['lead_src'])) { echo in_array($srcs['lead_source_id'], $filter['lead_src']) ? 'selected="selected"' : ''; } ?>><?php echo $srcs['lead_source_name'] ?></option>
											<?php } ?>
										<?php } ?>
									</select> 
								</td>
								<td>
									<select style="width:210px;" multiple="multiple" id="regionname" name="regionname[]" class="advfilter">
										<?php foreach ($regions as $reg) { 
												if(!empty($reg['region_name'])) { ?>
													<option value="<?php echo $reg['regionid'] ?>"<?php if (!empty($filter['regionname'])) { echo in_array($reg['regionid'], $filter['regionname']) ? 'selected="selected"' : ''; } ?>><?php echo $reg['region_name'] ?></option>
										<?php 	} 
											}
										?>
									</select> 
								</td>
								<td id="country_row">
									<select style="width:110px;" multiple="multiple" id="countryname" name="countryname[]" class="advfilter">
									</select> 
								</td>
								<td>
									<select style="width:110px;" multiple="multiple" id="statename" name="statename[]" class="advfilter">
									</select> 
								</td>
								<td>
									<select style="width:110px;" multiple="multiple" id="locname" name="locname[]" class="advfilter">
									</select> 
								</td>
								<td>
									<select style="width:75px;" multiple ="multiple" id="lead_indi" name="lead_indi[]" class="advfilter">
										<option value="HOT"<?php if (!empty($filter['lead_indi'])) { echo in_array('HOT', $filter['lead_indi']) ? 'selected' : ''; } ?>>Hot</option>
										<option value="WARM"<?php if (!empty($filter['lead_indi'])) { echo in_array('WARM', $filter['lead_indi']) ? 'selected' : ''; } ?>>Warm</option>
										<option value="COLD"<?php if (!empty($filter['lead_indi'])) { echo in_array('COLD', $filter['lead_indi']) ? 'selected' : ''; } ?>>Cold</option>
									</select> 
								</td>
							</tr>
							<tr align="right" >
								<td colspan="6">
									<input type="reset" class="positive input-font" name="advance" id="filter_reset" value="Reset" />
									<!--input type="submit" class="positive input-font" name="advance" id="advance" value="Search" /-->
									
									<input type="submit" class="positive input-font show-ajax-loader" name="advance" id="advance" value="Search" />
									<input type="button" class="positive input-font show-ajax-loader" name="save_advance" id="save_advance" value="Save & Search" />
									
									<div id = 'load' style = 'float:right;display:none;height:1px;'>
										<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
									</div>
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
			<input type="hidden" id="val_export" name="val_export" value="<?php echo $val_export ?>" />
			<div id="advance_search_dash_res" style="clear:both" ></div>
		</div>
		
		<!--Advance filters-->
		   <div class="clearfix">
			<div class="pull-left left-canvas">
				<h5 class="dash-tlt">Leads - Current Pipeline</h5>
				<div id="funnel1" class="plot canvas100" style=""></div>
				<!--div id="funnelimg"><button type="button">PDF</button></div-->
			</div>
			<div class="pull-right right-canvas">
				<h5 class="dash-tlt"><?php echo $chart_title; ?></h5>
				<div id="pie1" class="plot canvas100" style=""></div>
				<!--div id="pieimg"><button type="button">PDF</button></div-->
			</div>
			</div>
			<div id="charts_info" class="charts-info-block" style="display:none;"></div> <!--For funnel and pie charts information-->

			<div class="clearfix">
				<div class="pull-left dash-section lead_ind_title left-canvas">
					<h5>Lead Indicator</h5>
					<div id="bar1" class="plot" ></div>
					<!--div id="barimg"><button type="button">PDF</button></div-->
				</div>

				<div class="pull-right dash-section right-canvas">
					<div class="clearfix">
						<h5>
							<span>Currently Active Leads</span>
							<select id="current-lead-report" style="width: 105px; margin:0 0 0 5px;">
								<option value="7" checked="1">Current Week</option>
								<option value="30">Current Month</option>
							</select>
						</h5>
					</div>
					<div id="tbl_grid3" class="dashbrd">
						<div id="weekly-monthly">
						<table class="dashboard-heads table_grid" cellspacing="0" cellpadding="10px;" border="0" width="100%">
							<thead>
								<tr>
									<th>Lead Title</th><th>Estimated Worth (<?php echo $default_cur_name; ?>)</th><th>Lead Owner</th><th>Lead Assignee</th>
								</tr>
							</thead>
							<tbody>
							
							<?php 
								if (count($getCurrentActivityLead)>0) { 
									foreach ($getCurrentActivityLead as $currentActLead) {
							?>
									<tr>
										<td>
											<a onclick="getCurrentLeadActivity(<?php echo $currentActLead['lead_id'];?>,<?php echo "'".$currentActLead['lead_title']."'"; ?>)"><?php echo character_limiter($currentActLead['lead_title'], 35); ?></a>
										</td>
										<td align="right">
											<?php echo number_format(round($rates[$currentActLead['expect_worth_id']][$default_cur_id] * $currentActLead['expect_worth_amount']), 2, '.', ''); ?>
										</td>
										<td><?php echo $currentActLead['ownrfname'] . " " .$currentActLead['ownrlname']; ?></td>
										<td><?php echo get_lead_assigne_names($currentActLead['lead_assign']); ?></td>
									</tr>
							<?php 
									}
								} 
							?>
							</tbody>
						</table>
						<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			<div id="leads-current-activity-list"></div>
			
			<div class="clearfix">
			<div class="pull-left dash-section left-canvas">
				<h5>Lead Aging</h5>
				<div id="line1" class="plot"></div>
				<!--div id="lineimg"><button type="button">PDF</button></div-->
			</div>
			<div class="pull-right dash-section right-canvas">
				<h5>Closed Opportunities - Cumulative Sales: <?php echo $totClosedOppor ." ".$default_cur_name; ?></h5>
				<div id="line2" class="plot"></div>
				<!--div id="line2img"><button type="button">PDF</button></div-->
			</div>
			</div>
			<div id="charts_info2" class="charts-info-block"></div>
			
			<!--For Pie2 & Pie3 charts-->
			<div class="clearfix">
				<div class="pull-left dash-section left-canvas">
					<h5 class="dash-tlt">Leads By Lead Source</h5>
					<div id="pie2" class="plot" style=""></div>
				</div>
				<div class="pull-right dash-section right-canvas">
					<h5 class="dash-tlt">Leads By Service Requirement</h5>
					<div id="pie3" class="plot canvas100" style=""></div>
				</div>
			</div>
			<div id="charts_info3" class="charts-info-block" style="display:none;"></div><!--Pie2 & pie3 charts info display here-->
			
			<div class="clearfix">
			<div class="dash-section pull-left left-canvas">
				<h5>Opportunities By Lead Owner</h5>
				<div id="tbl_grid1" class="dashbrd">
					<table class="dashboard-heads table_grid" cellspacing="0" cellpadding="10px;" border="0" width="100%">
						<thead>
							<tr>
								<th>Lead Owner Name</th><th>No. of Leads</th>
							</tr>
						</thead>
						<tbody>
						<?php if (count($getLeadByOwner)>0) { ?>
							<?php foreach ($getLeadByOwner as $leadOwners) { ?>
							<tr>
								<td>
									<a onclick="getLeadDashboardTable(<?php echo $leadOwners['userid'];?>,<?php echo "'".$leadOwners['user_name']."'"; ?>);return false;"><?php echo $leadOwners['user_name']; ?></a>
								</td>
								<td><?php echo $leadOwners['COUNT( * )']; ?></td>
							</tr>
							<?php } ?>
						<?php } ?>
						</tbody>
					</table>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="pull-right dash-section right-canvas">
				<h5>Opportunities By Lead Assignee</h5>
				<div id="tbl_grid2" class="dashbrd">
					<table class="dashboard-heads table_grid" cellspacing="0" cellpadding="10px;" border="0" width="100%">
						<thead>
							<tr>
								<th>Lead Assignee Name</th><th>No. of Leads</th>
							</tr>
						</thead>
						<tbody>
						<?php if (count($getLeadByAssignee)>0) { ?>
							<?php foreach ($getLeadByAssignee as $leadAssign) { ?>
							<tr>
								<td>
									<a id="demo-stage" onclick="getLeadAssigneeTable(<?php echo $leadAssign['userid'];?>,<?php echo "'".$leadAssign['user_name']."'"; ?>);"><?php echo $leadAssign['user_name']; ?></a>
								</td>
								<td><?php echo $leadAssign['COUNT( * )']; ?></td>
							</tr>
							<?php } ?>
						<?php } ?>
						</tbody>
					</table>
					<div class="clear"></div>
				</div>
			</div>
			</div>
			<div id="lead-dependency-list" style="display:none;"></div><!--Opportunities By Lead Owner & Opportunities By Lead Assignee Info display here -->
			
			<div class="clearfix">
				<div class="pull-left dash-section left-canvas">
					<h5 class="dash-tlt">Leads By Industry</h5>
					<div id="pie4" class="plot" style=""></div>
				</div>
			</div>
			<div id="charts_info4" class="charts-info-block" style="display:none;"></div><!--pie4 charts info display here-->

		</div>
		<div id='popupGetSearchName'></div>
	<?php } ?>
	
	<!--Task Module for Non Lead Acces users only- Start here-->
	<?php if(($this->session->userdata('viewtask')==1) && ($this->session->userdata('viewlead') != 1)) { ?>
		<h2 style="padding-bottom:4px; border-bottom:1px solid #ccc; clear:left; margin-bottom:15px;">TASKS</h2>
		<p>
			<img src="assets/img/due_today.jpg" width="10" /> Due Today
			&nbsp;&nbsp;
			<img src="assets/img/task_delayed.jpg" width="10" /> Task Delayed
			&nbsp;&nbsp;
			<img src="assets/img/task_delayed_2days.jpg" width="10" /> Task Delayed more than 2 days
			&nbsp;&nbsp;
			<img src="assets/img/task_passed_deadline.jpg" width="10" /> Deadline Passed
			&nbsp;&nbsp;
			<img src="assets/img/task_completed.jpg" width="10" /> Task Completed
		</p>
		<p><?php echo '&nbsp;'; ?></p>
			
		<div class="all-tasks random-task" style="margin-bottom:20px;" id="jv-tab-4"></div>
		<?php
			include theme_url() . '/tpl/user_accounts_options.php';
			$uio = $userdata['userid'];
			foreach($created_by as $value) {
				$b[] = $value['created_by'];					
			}
		?>

		<form id="edit-job-task" onsubmit="return false;">
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			<input type ="hidden" id="dashboard" value="1"/>
			<!-- edit task -->
			<table border="0" cellpadding="0" cellspacing="0" class="task-add task-edit">
				<tr>
					<td colspan="4">
						<strong>All fields are required!</strong>						
					</td>
				</tr>
				<tr>
					<td valign="top" width="80">
						<br /><br />Task Desc
					</td>
					<td colspan="3">
						<strong><span id="edit-task-desc-countdown">1000</span></strong> characters left.<br />
						<textarea name="job_task" class="edit-job-task-desc width420px" ></textarea>
					</td>
				</tr>
				<tr>
					<td>Task Owner</td>
					<td><input type="text" class="edit-task-owner textfield" readonly="readonly"></td>					
				</tr>
				<tr >
					<td style="padding-bottom:10px;" ><br/>Category</td>
					<td>
						<select name="task_category" data-placeholder="Choose category." class="chzn-select edit-task-category" id="taskCategory" style="width:140px;">
							<option value=""></option>
							<?php
								foreach($category_listing_ls as $ua)
								{
									echo '<option value="'.$ua['id'].'">'.$ua['task_category'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr >
					<td style="padding-bottom:10px;">Priority</td>
					<td>
						<select name="task_priority" data-placeholder="Choose Priority." class="chzn-select edit-task-priority" id="taskpriority" style="width:140px;">
							<option value=""></option>
							<option value="1">Critical</option>
							<option value="2">High</option>
							<option value="3">Medium</option>
							<option value="4">Low</option>
							
						</select>
					</td>
				</tr>
		<?php
				if(in_array($uio,$b) || $userdata['role_id'] == 1) {
				?>
				<tr>
					<td>
						Allocate to
					</td>
					<td>
						<select name="task_user" class="edit-task-allocate textfield width100px">
							<?php 
								echo $remind_options, $remind_options_all, $contractor_options; 
							?>
						</select>
					</td>
				</tr>
				<?php 
				} else { 
				?>
				<tr>
					<td>
						Allocate to
					</td>
					<td>
						<select name="task_user" class="edit-task-allocate textfield width100px" disabled >
							<?php
								echo $remind_options, $remind_options_all, $contractor_options;
							?>
						</select>
					</td>
				</tr>
				<?php 
				}
				?>
				<tr>
					<td style="padding-bottom:10px;">Status</td>
					<td>
						<select name="task_priority" data-placeholder="Choose Status." class="chzn-select edit-task-stages" id="taskstages" style="width:140px;">
							<option value=""></option>
							<?php
								foreach($task_stages as $tstag)
								{
									echo '<option value="'.$tstag['task_stage_id'].'">'.$tstag['task_stage_name'].'</option>';
								}
							?>
						</select>
						<input type="hidden" name="task_complete_status" id="edit_complete_status" class="edit-complete-status textfield width100px" />	
					</td>
				</tr>
				<tr>
					<td>
						Planned Start Date
					</td>
					<td>
						<input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" style="margin-top:5px;"/>
					</td>		
					<td>
						Planned End Date
					</td>
					<td>								
					<input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px" />	
					</td>
				</tr>
				<tr>
					<td>Actual Start Date</td>
					<td>
						<?php
						if($created_by['jobid_fk'] == $remind_options) {
						?>
							<input type="text"  name="task_actualstart_date" class="edit-actualstart textfield pick-date width100px" readonly />
						<?php 
						} else {
						?>
							<input type="text" name="task_actualstart_date" class="edit-actualstart-date textfield pick-date width100px"  />
						<?php 
						} 
						?>
					</td>
					<td>Actual End Date</td>
					<td class="actualend-date"><input type="text" class="edit-actualend-date textfield" readonly></td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td colspan="3">
						<?php 
						if($created_by['jobid_fk'] == $remind_options) {
						?>
							<textarea name="remarks" class="edit-task-remarks" width="420px" readonly ></textarea>
						<?php
						} else {
						?>
							<textarea name="remarks" class="edit-task-remarks" width="420px"  ></textarea>
						<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="buttons">
							<button type="submit" class="positive" onclick="editTask();">Update</button>
						</div>
						<div class="buttons">
							<button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button>
						</div>
					</td>
				</tr>
			</table>
			<!-- edit task end -->
		</form>
		<form style="display:none;" class="random-task-tables" onsubmit="return false;">
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		</form>
	<?php } ?>
	<!--Task Module for Non Lead Acces users only- End here-->
	
<?php if(($this->session->userdata('viewPjt')==1) && ($this->session->userdata('viewlead') != 1)) { ?>
<div class="page-title-head">
	<h2 class="pull-left borderBtm">PROJECTS - LISTS</h2>
	<a class="choice-box" onclick="advanced_filter_pjt();" >
		<span>Advanced Filters</span>
		<img src="assets/img/advanced_filter.png" class="icon leads" />
	</a>
	<div class="section-right">
		<div class="form-cont search-table">
			<form id="pjt_search_form" name="pjt_search_form" method="post">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<input type="text" name="keyword" id="keywordpjt" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Project Title, Name or Company' ?>" class="textfield width210px pjt-search" />
				<button type="submit" id="project_search" class="positive">Project Search</button>			
			</form>
		</div>
	</div>
</div>

<div id="advance_search_pjt" style="float:left; width:100%;" >
	<form name="advanceFilters_pjt" id="advanceFilters_pjt"  method="post">
	
		<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	
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
			<td>
				<select style="width:210px;" multiple="multiple" id="customer1" name="customer1[]">
					<?php foreach($customers as $customer) {?>
						<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['company'].' - '.$customer['customer_name']; ?></option>	
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
		</tbody>
		<thead>
			<tr>
			<th >By Entity</th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>	
		</thead>
		<tbody>
		<tr>
			<td colspan="6">
				<select multiple="multiple" id="divisions" name="divisions[]" class="advfilter" style="width:210px;">
					<?php foreach ($sales_divisions as $division) { ?>
						<option value="<?php echo $division['div_id'] ?>"><?php echo $division['division_name']; ?></option>
					<?php } ?>
				</select> 
			</td>
		</tr>
		<tr align="right" >
			<td colspan="5"><input type="reset" class="positive input-font" name="advance_pjt" value="Reset" />
				<input type="submit" class="positive input-font" name="advance_pjt" id="advance" value="Search" />
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
<div id="ajax_loader" style="margin:20px;display:none" align="center">
	Loading Content.<br><img alt="wait" src="<?php echo base_url().'assets/images/ajax_loader.gif'; ?>"><br>Thank you for your patience!
</div>
<div class="clearfix"></div>
<div id="advance_search_results_pjts" class="custom_dashboardfilter clear"></div>
<?php } ?>
	
	</div><!--Inner - close here -->
</div><!--Content - close here -->
<?php
//For Leads - Current Pipeline.
$lead_stage = array();
foreach($getLeads as $getLead){
	$stage_name = explode('.',$getLead['lead_stage_name']);
	$lead_stage[] = "['".$stage_name[0].'('.$getLead["COUNT( * )"].')'."'".','.$getLead["COUNT( * )"]."]";
}
$s1 = implode(',', $lead_stage);

// print $s1; exit;


//For Leads By RegionWise
$s2 = "";
foreach($LeadsRegionwise as $key => $value) {
	$s2 .= "['".$key."(".$value." ".$default_cur_name.".)',".$value."],";
}


$s3 = "";
$s3_name = "";
foreach($getLeadIndicator as $key => $value) {
	$s3 .= $value.',';
	$s3_name .= "'".$key.'('.$value.')'."'".',';
}

//defining colors for least active leads.
$coldColor = array();
$warmColor = array();
$assignColor = array("COLD"=>"#bfdde5", "WARM"=>"#910000");

foreach($getLeastLeadCount as $getClr) {
	switch($getClr['lead_indicator']){
		case "COLD":
			for($i=1;$i<=$getClr['count(j.lead_indicator)'];$i++) {
				$coldColor[] = $assignColor[$getClr['lead_indicator']];
			}
		break;
		case "WARM":
			for($j=1;$j<=$getClr['count(j.lead_indicator)'];$j++) {
				$warmColor[] = $assignColor[$getClr['lead_indicator']];
			}
		break;
	}
}
$resColors = array_merge($coldColor, $warmColor);
$resColors = '"'.@implode('","', $resColors).'"';


$s4 = "";
foreach($getLeadAging as $key => $value){
	$s4 .= "[".$value.",'".$key."'],";
}

//for closed opportunities graph.
$cls_oppo_values = array();
$cls_oppr = '';
$months = array('Apr'=>0, 'May'=>0, 'Jun'=>0, 'Jul'=>0, 'Aug'=>0, 'Sep'=>0, 'Oct'=>0, 'Nov'=>0, 'Dec'=>0, 'Jan'=>0, 'Feb'=>0, 'Mar'=>0);
foreach ($months as $key => $val) {
	$cls_oppo_values[$key] = ($getClosedOppor[$key] == "")?($val):$getClosedOppor[$key];
}

foreach( $cls_oppo_values as $key=>$data ) {
	$cls_oppr .= "['". $key ."'" . ',' . $data . "],";
}


//For Leads - Lead Source. 
$Ld_Src = array();
foreach($get_Lead_Source as $getLdSrc) {
	$Ld_Src[] = "['".$getLdSrc['lead_source_name'].'('.$getLdSrc["src"].')'."'".','.$getLdSrc["src"]."]";
}
$s7 = implode(',', $Ld_Src);

//For Leads - Industry.
$indus = array();
foreach($get_Lead_Industry as $getLdInd) {
	$indus[] = "['".$getLdInd['industry'].'('.$getLdInd["src"].')'."'".','.$getLdInd["src"]."]";
}
$s9 = implode(',', $indus);

//For Leads - Service Requirement. 
$Ser_Req = array();
foreach($get_Service_Req as $getSerReq) {
	$Ser_Req[] = "['".$getSerReq['services'].'('.$getSerReq["job_cat"].')'."'".','.$getSerReq["job_cat"]."]";
}
$s8 = implode(',', $Ser_Req);
?>
<?php
	$stgs = $custs_id = $owr_id = $assg_id = $reg_nme = $county_name = $ste_name = $loc_name = $servic_req = $lead_sour = $lead_indic = $industry = '';
	$toggle_stat =  isset($toggle_stat) ? "toggle" : "no_toggle";
	if (!empty($filter['stage']))
	$stgs = implode(",",$filter['stage']);
	if (!empty($filter['customer']))
	$custs_id = implode(",",$filter['customer']);
	if (!empty($filter['owner']))
	$owr_id = implode(",",$filter['owner']);
	if (!empty($filter['leadassignee']))
	$assg_id = implode(",",$filter['leadassignee']);
	if (!empty($filter['regionname']))
	$reg_nme = implode(",",$filter['regionname']);
	if (!empty($filter['countryname']))
	$county_name = implode(",",$filter['countryname']);
	if (!empty($filter['statename']))
	$ste_name = implode(",",$filter['statename']);
	if (!empty($filter['locname']))
	$loc_name = implode(",",$filter['locname']);
	if (!empty($filter['ser_requ']))
	$servic_req = implode(",",$filter['ser_requ']);
	if (!empty($filter['lead_src']))
	$lead_sour = implode(",",$filter['lead_src']);
	if (!empty($filter['industry'])){
		$industry = implode(",",$filter['industry']);
	}
	
	if (!empty($filter['lead_indi']))
	$lead_indic = implode(",",$filter['lead_indi']);
?>

<script class="code" type="text/javascript">
	dashboard_s1	   = [<?php echo $s1; ?>];
	dashboard_s2	   = [<?php echo @rtrim($s2, ','); ?>];
	dashboard_s3	   = [<?php echo $s3; ?>];
	dashboard_s4	   = [<?php echo @rtrim($s4, ','); ?>];
	dashboard_s7 	   = [<?php echo @rtrim($s7, ','); ?>];
	dashboard_s8 	   = [<?php echo @rtrim($s8, ','); ?>];
	dashboard_s9 	   = [<?php echo @rtrim($s9, ','); ?>];
	dashboard_s3_name  = [<?php echo $s3_name; ?>];
	dashboard_cls_oppr = [<?php echo rtrim($cls_oppr, ','); ?>];
	dashboard_userid   = "<?php echo $userdata['userid']; ?>";
	filter_toggle_stat = "<?php echo $toggle_stat ?>";
	filter_stgs		   = "<?php echo $stgs ?>";
	filter_custs_id	   = "<?php echo $custs_id ?>";
	filter_owr_id	   = "<?php echo $owr_id ?>";
	filter_assg_id     = "<?php echo $assg_id ?>";
	filter_reg_nme     = "<?php echo $reg_nme ?>";
	filter_country 	   = "<?php echo $county_name ?>";
	filter_state       = "<?php echo $ste_name ?>";
	filter_location    = "<?php echo $loc_name ?>";
	filter_servic_req  = "<?php echo $servic_req ?>";
	filter_lead_sour   = "<?php echo $lead_sour ?>";
	filter_industry    = "<?php echo $industry ?>";
	filter_lead_indic  = "<?php echo $lead_indic ?>";
</script>
<script type="text/javascript" src="assets/js/dashboard/dashboard_view.js"></script>
<?php
 require (theme_url().'/tpl/footer.php');
 ob_end_flush();
?>