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
<?php if($this->session->userdata('viewlead')==1) { ?>
<script type="text/javascript">var this_is_home = true;</script>
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
<?php 
// For Chart Title
switch ($userdata['level']) {
    case 1:
        $chart_title = "Leads By Region Wise";
	break;
    case 2:
        $chart_title = "Leads By Country Wise";
	break;
    case 3:
        $chart_title = "Leads By State Wise";
	break;
	case 4:
	case 5:
        $chart_title = "Leads By Location Wise";
	break;
}
?>
<?php } ?>
<?php if(($this->session->userdata('viewtask')==1) && ($this->session->userdata('viewlead') != 1)) { ?>
<script type="text/javascript">var this_is_home = true;</script>
<script type="text/javascript">var curr_job_id = 0;</script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>
<style type="text/css">
@import url(assets/css/tasks.css?q=1);
</style>
<?php } ?>
<div id="content">
	<div class="inner">
	<?php if($this->session->userdata('viewlead')==1) { ?>
		<div>
		   <div class="clearfix">
			<div class="pull-left">
				<h5 class="dash-tlt">Leads - Current Pipeline</h5>
				<div id="funnel1" class="plot" style="width:450px"></div>
				<!--<div id="funnelimg"><button type="button">PDF</button></div>-->
			</div>
			<div class="pull-right">
				<h5 class="dash-tlt"><?php echo $chart_title; ?></h5>
				<div id="pie1" class="plot" style="width:450px"></div>
				<!--<div id="pieimg"><button type="button">PDF</button></div>-->
			</div>
			</div>
			<div id="charts_info" class="charts-info-block" style="display:none;"></div> <!--For funnel and pie charts information-->

			<div class="clearfix">
				<div class="pull-left dash-section lead_ind_title">
					<h5>Lead Indicator</h5>
					<div id="bar1" class="plot" ></div>
					<!--<div id="barimg"><button type="button">PDF</button></div>-->
				</div>

				<div class="pull-right dash-section">
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
							
							<?php if (count($getCurrentActivityLead)>0) { ?>
								<?php foreach ($getCurrentActivityLead as $currentActLead) { ?>
								<tr>
									<td><a onclick="getCurrentLeadActivity(<?php echo $currentActLead['jobid'];?>,<?php echo "'".$currentActLead['job_title']."'"; ?>)"><?php echo $currentActLead['job_title']; ?></a></td>
									<!--<td><?php //echo $currentActLead['expect_worth_name']." ".$currentActLead['expect_worth_amount']; ?></td>-->
									<td align="right"><?php echo number_format(round($rates[$currentActLead['expect_worth_id']][1] * $currentActLead['expect_worth_amount']), 2, '.', ''); ?></td>
									<td><?php echo $currentActLead['ownrfname'] . " " .$currentActLead['ownrlname']; ?></td>
									<td><?php echo $currentActLead['usrfname'] . " " .$currentActLead['usrlname']; ?></td>
								</tr>
								<?php } ?>
							<?php } ?>
							</tbody>
						</table>
						<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			<div id="leads-current-activity-list"></div>
			
			<div class="clearfix">
			<div class="pull-left dash-section">
				<h5>Lead Aging</h5>
				<div id="line1" class="plot"></div>
				<!--<div id="lineimg"><button type="button">PDF</button></div>-->
			</div>
			<div class="pull-right dash-section">
				<h5>Closed Opportunities - Cumulative Sales: <?php echo $totClosedOppor ." ".$default_cur_name; ?></h5>
				<div id="line2" class="plot"></div>
				<!--<div id="line2img"><button type="button">PDF</button></div>-->
			</div>
			</div>
			<div id="charts_info2" class="charts-info-block"></div>
			
			<!--For Pie2 & Pie3 charts-->
			<div class="clearfix">
				<div class="pull-left dash-section">
					<h5 class="dash-tlt">Leads By Lead Source</h5>
					<div id="pie2" class="plot" style="width:450px"></div>
				</div>
				<div class="pull-right dash-section">
					<h5 class="dash-tlt">Leads By Service Requirement</h5>
					<div id="pie3" class="plot" style="width:450px"></div>
				</div>
			</div>
			<div id="charts_info3" class="charts-info-block" style="display:none;"></div><!--Pie2 & pie3 charts info display here-->
			
			<div class="clearfix">
			<div class="dash-section pull-left">
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
								<td><a onclick="getLeadDashboardTable(<?php echo $leadOwners['userid'];?>,<?php echo "'".$leadOwners['user_name']."'"; ?>);return false;"><?php echo $leadOwners['user_name']; ?></a></td>
								<td><?php echo $leadOwners['COUNT( * )']; ?></td>
							</tr>
							<?php } ?>
						<?php } ?>
						</tbody>
					</table>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="pull-right dash-section">
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
								<td><a id="demo-stage" onclick="getLeadAssigneeTable(<?php echo $leadAssign['userid'];?>,<?php echo "'".$leadAssign['user_name']."'"; ?>);"><?php echo $leadAssign['user_name']; ?></a></td>
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
			
		</div>
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
				$b[] = $value[created_by];						
			}
		?>

		<form id="edit-job-task" onsubmit="return false;">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
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
						<textarea name="job_task" class="edit-job-task-desc width420px"></textarea>
					</td>
				</tr>
				<tr>
					<td>Task Owner</td>
					<td><input type="text" class="edit-task-owner textfield" readonly ></td>
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
					<!--<td>
						Hours
					</td>
					<td>
						<input name="task_hours" type="text" class="edit-task-hours textfield width100px" /> Hours and
						<select name="task_mins" class="edit-task-mins textfield">
							<option value="0">0</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
						</select>
						Mins
					</td>-->
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
					<td>
						Planned Start Date
					</td>
					<?php if(in_array($uio,$b) || $userdata['role_id'] == 1) {?>
					<td>
						<input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" />
					</td>
					<?php } else { ?>
					<td>
								<input type="text" name="task_start_date" class="edit-start-date textfield width100px" readonly />
							</td>
							<?php } ?>
					<td>
						Planned End Date
					</td>
					<td>
						<?php if(in_array($uio,$b) || $userdata['role_id'] == 1) { ?>
							<input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px" />									
						<?php } else { ?>
							<input type="text" name="task_end_date" class="edit-end-date textfield width100px" readonly />
						<?php } ?>
						&nbsp;
						<!--<select name="task_end_hour" class="textfield edit-end-hour">
						<?php
						/*foreach ($time_range as $k => $v)
						{
							$selected = '';
							echo "
							<option value=\"{$k}\"{$selected}>{$v}</option>";
						}*/
						?>
						</select>-->
					</td>
				</tr>
				<tr>
					<td>Actual Start Date</td>
					<td><input type="text" name="task_actualstart_date" class="edit-actualstart-date textfield pick-date width100px" /></td>
					<td>Actual End Date</td>
					<td class="actualend-date"><input type="text" class="edit-actualend-date textfield" readonly></td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td colspan="3"><textarea name="remarks" class="edit-task-remarks" width="420px"></textarea></td>
				</tr>
				<!--<tr><td colspan=3>Priority Support : <input type="checkbox" name="priority" class="priority"/></td>-->
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
<h2 style="padding-bottom:4px; border-bottom:1px solid #ccc; clear:left; margin-bottom:15px;">PROJECTS</h2>

<form name="pjt_search_form" id="pjt_search_form" action="" method="post" style="float:right;">

	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

			<table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
					<td>
                        Project Search
                    </td>
					<td>
                        <input type="text" id="keywordpjt" name="keywordpjt" value="<?php if (isset($_POST['keywordpjt'])) echo $_POST['keywordpjt']; else echo 'Project No, Project Title, Name or Company' ?>" class="textfield width210px pjt-search" />
                    </td>
                    <td rowspan=2>
                        <div class="buttons">
                            <button type="submit" class="positive">Search</button>
                        </div>
                    </td>
                </tr>
            </table>
		</form>
<a class="choice-box" onclick="advanced_filter_pjt();" >
		Advanced Filters
		<img src="assets/img/icon_view_leads.png" class="icon leads" />
</a>
<div id="advance_search_pjt" style="float:left; width:100%;" >
		
		<form name="advanceFilters_pjt" id="advanceFilters_pjt"  method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<table border="0" cellpadding="0" cellspacing="0" class="data-table">
			<thead>
				<tr>
					<th>By Project Status Wise</th>
					<th>By Project Manager Wise</th>
					<th>By Customer Wise</th>
				</tr>	
			</thead>
			<tbody>
			<tr>	
				<td>
					<select style="width:230px;" multiple="multiple" id="pjt_stage" name="pjt_stage[]">
						<option value="1">Project In Progress</option>
						<option value="2">Project Completed</option>
						<option value="3">Project Onhold</option>
						<option value="4">Inactive</option>
					</select> 
				</td>
				
				
				<td>
					<select style="width:230px;" multiple="multiple" id="pm_acc" name="pm_acc[]">
						<?php foreach($pm_accounts as $pm_acc) {?>
						<option value="<?php echo $pm_acc['userid']; ?>">
						<?php echo $pm_acc['first_name'].' '.$pm_acc['last_name']?></option>	
						<?php } ?>
					</select> 
				</td>
				
				<td>
					<select style="width:230px;" multiple="multiple" id="customer1" name="customer1[]">
						<?php foreach($customers as $customer) {?>
						<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>	
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr align="right" >
				<td colspan="5"><input type="reset" class="positive" name="advance_pjt" value="Reset" />
				<input type="submit" class="positive" name="advance_pjt" value="Search" /></td>
			
			</tr>
			</tbody>
			</table>
		</form>
	</div>
	
<div id="advance_search_results_pjts" style="clear:both; overflow:scroll; height:400px; width:960px;" ></div>
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

//print $s1; exit;


//For Leads By RegionWise
$s2 = "";
foreach($LeadsRegionwise as $key => $value){
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
			for($i=1;$i<=$getClr['count(`lead_indicator`)'];$i++) {
				$coldColor[] = $assignColor[$getClr['lead_indicator']];
			}
		break;
		case "WARM":
			for($j=1;$j<=$getClr['count(`lead_indicator`)'];$j++) {
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
$months = array('Apr'=>0, 'May'=>0, 'Jun'=>0, 'Jul'=>0, 'Aug'=>0, 'Sep'=>0, 'Oct'=>0, 'Nov'=>0, 'Dec'=>0, 'Jan'=>0, 'Feb'=>0, 'Mar'=>0);
foreach ($months as $key => $val) {
	$cls_oppo_values[$key] = ($getClosedOppor[$key] == "")?($val):$getClosedOppor[$key];
}

foreach( $cls_oppo_values as $key=>$data ) {
$cls_oppr .= "['". $key ."'" . ',' . $data . "],";
}


//For Leads - Lead Source. 
$Ser_Req = array();
foreach($get_Lead_Source as $getLdSrc) {
	$Ld_Src[] = "['".$getLdSrc['lead_source_name'].'('.$getLdSrc["src"].')'."'".','.$getLdSrc["src"]."]";
}
$s7 = implode(',', $Ld_Src);

//For Leads - Service Requirement. 
$Ser_Req = array();
foreach($get_Service_Req as $getSerReq) {
	$Ser_Req[] = "['".$getSerReq['category'].'('.$getSerReq["job_cat"].')'."'".','.$getSerReq["job_cat"]."]";
}
$s8 = implode(',', $Ser_Req);
?>

<script class="code" type="text/javascript">
	 dashboard_s1       = [<?php echo $s1; ?>];
	 dashboard_s2       = [<?php echo @rtrim($s2, ','); ?>];
	 dashboard_s3       = [<?php echo $s3; ?>];
	 dashboard_s4       = [<?php echo @rtrim($s4, ','); ?>];
	 dashboard_s7       = [<?php echo @rtrim($s7, ','); ?>];
	 dashboard_s8       = [<?php echo @rtrim($s8, ','); ?>];
	 dashboard_s3_name  = [<?php echo $s3_name; ?>];
	 dashboard_cls_oppr = [<?php echo rtrim($cls_oppr, ','); ?>];
	 dashboard_userid   = "<?php echo $userdata['userid']; ?>"; 
</script>
<script type="text/javascript" src="assets/js/theme_js/dashboard_view.js"></script>
<?php
 require (theme_url().'/tpl/footer.php');
 ob_end_flush();
?>