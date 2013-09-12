<?php
ob_start();
$userdata = $this->session->userdata('logged_in_user');
require ('tpl/header.php'); 
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
if ($userdata['level']==1) {
	$chart_title = "Leads By Region Wise";
}
else if ($userdata['level']==2) {
	$chart_title = "Leads By Country Wise";
}
else if ($userdata['level']==3) {
	$chart_title = "Leads By State Wise";
}
else if ($userdata['level']==4){
	$chart_title = "Leads By Location Wise";
}
else {
	$chart_title = "Leads By Location Wise";
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

/* over-ride tasks */
td.task {
	width:557px;
}
.great-task-table, .great-task-table td {
	border-color:#888;
}
.row-header td.user {
	font-size:12px;
}
/* end over-ride */

.choice-box {
    width:260px;
    padding:15px;
    -moz-border-radius:8px;
    -webkit-border-radius:8px;
    background:#a8cb17;
    float:left;
    margin:0 35px 30px 0;
	color:#a8cb17;
	cursor:pointer;
	position:relative;
	color:#fefffd;
	font-weight:bold;
}
.choice-box img {
	position:absolute;
	right:5px;
	top:-20px;
}
.choice-box img.quote, .choice-box img.leads {
	top:-22px;
}
.choice-box:hover {
	background:#888;
	color:#fff;
	text-decoration:none;
}
.right-edge {
    margin-right:0;
}
.bottom-edge {
    margin-bottom:0;
}
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
									<th>Lead Title</th><th>Estimated Worth (USD)</th><th>Lead Owner</th><th>Lead Assignee</th>
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
				<h5>Closed Opportunities - Cumulative Sales: $ <?php echo $totClosedOppor; ?></h5>
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
			include VIEWPATH . 'tpl/user_accounts_options.php';
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
					<option value="0">All</option>
					<?php foreach($lead_stage_pjt as $ls) {?>
					<option value="<?php echo $ls['lead_stage_id']; ?>">
					<?php if ($ls['lead_stage_name'] == 'Project Charter Approved. Convert to Projects In Progress') {
						$ls['lead_stage_name'] = 'Project - In Progress'; }
					echo $ls['lead_stage_name']; ?>
					</option>	
					<?php } ?>					
					</select> 
				</td>
				
				
				<td>
					<select style="width:230px;" multiple="multiple" id="pm_acc" name="pm_acc[]">
						<option value="0">All</option>
						<?php foreach($pm_accounts as $pm_acc) {?>
						<option value="<?php echo $pm_acc['userid']; ?>">
						<?php echo $pm_acc['first_name'].' '.$pm_acc['last_name']?></option>	
						<?php } ?>
					</select> 
				</td>
				
				<td>
					<select style="width:230px;" multiple="multiple" id="customer1" name="customer1[]">
						<option value="0">All</option>
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

//For Leads By RegionWise
$s2 = "";
foreach($LeadsRegionwise as $key => $value){
	//$reg_leads[] = "['".$getRegLead['region_name'].'('.$getRegLead['lead_values'].')'."'".','.$getRegLead['lead_values']."]";
	$s2 .= "['".$key."(".$value." USD)',".$value."],";
}

//For LeadsIndicator
/*
$s3 = "";
$lead_ind = array();
$lead_ind_name = array();
foreach($getLeadIndicator as $getInd){
	$name = explode('.',$getInd['lead_indicator']);
	$lead_ind[] = $getInd["COUNT(lead_indicator)"];
	$lead_ind_name[] = "'".$name[0].'('.$getInd["COUNT(lead_indicator)"].")'";
}

echo $s3 = implode(',', $lead_ind);
echo $s3_name = implode(',', $lead_ind_name);
*/
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
	//$reg_leads[] = "['".$getRegLead['region_name'].'('.$getRegLead['lead_values'].')'."'".','.$getRegLead['lead_values']."]";
	$s4 .= "[".$value.",'".$key."'],";
}

//for closed opportunities graph.
$cls_oppo_values = array();
$months = array('Apr'=>0, 'May'=>0, 'Jun'=>0, 'Jul'=>0, 'Aug'=>0, 'Sep'=>0, 'Oct'=>0, 'Nov'=>0, 'Dec'=>0, 'Jan'=>0, 'Feb'=>0, 'Mar'=>0);
foreach ($months as $key => $val) {
	$cls_oppo_values[$key] = ($getClosedOppor[$key] == "")?($val):$getClosedOppor[$key];
}
//echo "<pre>"; print_r($cls_oppo_values);
foreach( $cls_oppo_values as $key=>$data ) {
$cls_oppr .= "['". $key ."'" . ',' . $data . "],";
}
// echo $cls_oppr; exit;

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
<?php if($this->session->userdata('viewlead')==1) { ?>
<script type="text/javascript">
	$(document).ready(function(){

		<?php if (!empty($s1)) { ?>
		plot1 = $.jqplot('funnel1', [[<?php echo $s1; ?>]], {
		//title: 'Leads - Current Pipeline',
		legend: {
		   show: true,
		   rendererOptions: {
			   // numberColumns: 2,
			   border: false,
			   fontSize: '10pt',
			   location: 'e'
		   }
		},
		seriesDefaults: {
			shadow: false,
			renderer: $.jqplot.FunnelRenderer
		},
		grid: {
				drawGridLines: true,        // wether to draw lines across the grid or not.
				gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
				background: '#ffffff',      // CSS color spec for background color of grid.
				//borderColor: '#999999',     // CSS color spec for border around grid.
				//borderWidth: 1.0,		// pixel width of border around grid.
				//backgroundColor: 'transparent', 
				drawBorder: false,
				shadow: false
		},
		seriesColors: ["#027997", "#910000", "#bfdde5", "#8bbc21", "#1aadce", "#492970", "#2f7ed8", "#0d233a", "#48596a", "#640cb1", "#eaa228", "#422460"]
		});
		$('#funnel1').bind('jqplotDataClick',function (ev, seriesIndex, pointIndex, data) {
			//alert(data);
			//$('#funnel_info').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
			
			var formdata = { 'data':data, 'type':'funnel' ,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }
			$.ajax({
				type: "POST",
				url: '<?php echo base_url(); ?>dashboard/showLeadsDetails/',
				dataType:"json",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					$('#charts_info').empty();
					$('#charts_info').show();
					$('#charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="<?php echo base_url(); ?>assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
				},
				success: function(html){
					//alert(html.html);
					$('#charts_info').empty();
					$("#charts_info").show();
					if (html.html == 'NULL') {
						$('#charts_info').html('');
					} else {
						$('#charts_info').show();
						$('#charts_info').html(html.html);
						
						$('#example_funnel').dataTable( {
							"aaSorting": [[ 0, "desc" ]],
							"iDisplayLength": 5,
							"sPaginationType": "full_numbers",
							"bInfo": true,
							"bPaginate": true,
							"bProcessing": true,
							"bServerSide": false,
							"bLengthChange": false,
							"bSort": true,
							"bAutoWidth": false,
							"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
								var cost = 0
								for ( var i=0 ; i<aaData.length ; i++ )
								{
									var TotalMarks = aaData[i][6]; 
									//var str = TotalMarks.split(" "); //for USD 1200.00
									//cost += parseFloat(str[1]);//for USD 1200.00
									cost += parseFloat(TotalMarks);
									
								}
								var nCells = nRow.getElementsByTagName('td');
								//nCells[1].innerHTML = "USD " + cost.toFixed(2); //for USD 1200.00
								nCells[1].innerHTML = cost.toFixed(2);
							}
						});
						$('html, body').animate({ scrollTop: $("#charts_info").offset().top }, 1000);
					}
				}                                                                                       
			});
			return false;
		});
		
		$( "#funnelimg" ).click(function() {
			//var imgelem = evt.data.chart.jqplotToImageElem();
			var imgelem = $('#funnel1').jqplotToImageElem();
			var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
			//alert(imageSrc);// return false;
			//open(imageSrc); // this will open the image in another tab
			var imgdata = imageSrc;
			var base_url = "<?php echo site_url(); ?>";		
		
			var url = base_url+"dashboard/savePdf/";
			var form = $('<form action="' + url + '" method="post">' +
			  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
			  '</form>');
			$('body').append(form);
			$(form).submit();
			
			
			/*
			var formdata = { 'data':data, 'type':'funnel' }
			$.ajax({
				type: "POST",
				url: '<?php echo base_url(); ?>dashboard/savePdf/',
				dataType:"json",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					//$("#loadingImage").show();
					//$('#res').hide();
				},
				success: function(html){
					
				}                                                                                       
			});*/
		});
		<?php } else { ?>
			$('#funnel1').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		<?php } ?>
	});
</script>

<script class="code" type="text/javascript">
$(document).ready(function(){
   <?php if (!empty($s2)) { ?>
	$.jqplot.config.enablePlugins = true;
	var plot2 = $.jqplot('pie1', [[<?php echo rtrim($s2, ','); ?>]], {
        gridPadding: {top:25, bottom:24, left:0, right:0},
		//title:'<?php echo $chart_title; ?>',
		animate: !$.jqplot.use_excanvas,
		animateReplot: true,
        seriesDefaults:{
			shadow: false,
            renderer:$.jqplot.PieRenderer, 
            trendline:{ show:false }, 
            rendererOptions: { 
				padding: 8,
				sliceMargin: 2,
				showDataLabels: true
			},
			highlighter: {
				show: true,
				formatString:'%s',
				tooltipLocation:'sw', 
				useAxesFormatters:false
			}				 
        },
		grid: {
				drawGridLines: true,        // wether to draw lines across the grid or not.
				gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
				background: '#ffffff',      // CSS color spec for background color of grid.
				borderColor: '#ffffff',     // CSS color spec for border around grid.
				//borderWidth: 2.0,           // pixel width of border around grid.
				//backgroundColor: 'transparent', 
				drawBorder: false,
				shadow: false
		},
        legend:{
            show:true, 
			fontSize: '9pt',
			location: 'e',
			border: false
        },
		seriesColors: ["#422460", "#da7b00", "#9c1a4b", "#48596a", "#0d233a", "#2f7ed8", "#492970", "#1aadce", "#8bbc21", "#bfdde5", "#910000", "#027997"]
    });
	$('#pie1').bind('jqplotDataClick',
			function (ev, seriesIndex, pointIndex, data) {
				//alert(data);
				//$('#info1').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
				var formdata = { 'data':data, 'type':'pie1','<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }
				$.ajax({
					type: "POST",
					url: '<?php echo base_url(); ?>dashboard/showLeadsDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info').empty();
						$('#charts_info').show();
						$('#charts_info').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="<?php echo base_url(); ?>assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
					},
					success: function(html){
						//alert(html.html);
						$('#charts_info').empty();
						$("#charts_info").show();
						if (html.html == 'NULL') {
							$('#charts_info').html('');
						} else {
							$('#charts_info').show();
							$('#charts_info').html(html.html);
							
							$('#example_pie1').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
								    //alert(nRow);
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6];
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			}
		);
		$( "#pieimg" ).click(function() {
			//var imgelem = evt.data.chart.jqplotToImageElem();
			var imgelem = $('#pie1').jqplotToImageElem();
			var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
			//alert(imageSrc);// return false;
			//open(imageSrc); // this will open the image in another tab
			var imgdata = imageSrc;
			var base_url = "<?php echo site_url(); ?>";		
		
			var url = base_url+"dashboard/savePdf/";
			var form = $('<form action="' + url + '" method="post">' +
			  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
			  '</form>');
			$('body').append(form);
			$(form).submit();
			
			
			/*
			var formdata = { 'data':data, 'type':'funnel' }
			$.ajax({
				type: "POST",
				url: '<?php echo base_url(); ?>dashboard/savePdf/',
				dataType:"json",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					//$("#loadingImage").show();
					//$('#res').hide();
				},
				success: function(html){
					
				}                                                                                       
			});*/
		});
		<?php } else { ?>
			$('#pie1').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		<?php } ?>
});
</script>

<script type="text/javascript">
	$(document).ready(function(){
        $.jqplot.config.enablePlugins = true;
		var ticks = [<?php echo $s3_name; ?>];
		<?php if (!empty($s3)) { ?>
		var plot3 = $.jqplot('bar1', [[<?php echo $s3; ?>],[],[]], {
            title: {
				//text: 'Lead Indicator',   // title for the plot,
				//show: true,
			},
			// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
				shadow: false,
                //pointLabels: { show: true, ypadding:3 },
                pointLabels: { show: true },
				rendererOptions: {
					barWidth: 34,
					varyBarColor: true,
					animation: {
                        speed: 4000
                    }
                }
            },
			legend: {
				show: true,
				placement: 'insideGrid',
				labels: ticks
			},
			//seriesColors: [<?php echo $resColors; ?>],
			seriesColors: ["#910000", "#f47123", "#2c84c5"],
			axesDefaults: {
				tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
				tickOptions: {
				  //fontFamily:"Arial",
				  //textColor:'black',
				}
			},
			axesDefaults: {
				tickRenderer: $.jqplot.CanvasAxisTickRenderer ,         
				tickOptions: {
				  //angle: 10,
				  fontSize: '10pt'            
				},
				rendererOptions: {
					baselineWidth: 0.5,
					baselineColor: '#444444',
					drawBaseline: true
				}
			},
            axes: {
                xaxis: {
					label:'Lead Indicator--->',
                    renderer: $.jqplot.CategoryAxisRenderer,
					tickOptions:{
						//fontFamily:'Arial',
						//fontSize: '10pt',
						//fontWeight:"bold",
						//angle: -30,
						show: false
					}
                },
				yaxis: {
					label:'No. of Leads--->',
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				}
            },
			series: [{
				markerOptions: {
					show: true
				},
				rendererOptions: {
					smooth: false
				}
			}],
			grid: {
				drawGridLines: true,        // wether to draw lines across the grid or not.
				gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
				background: '#ffffff',      // CSS color spec for background color of grid.
				borderColor: '#ffffff',     // CSS color spec for border around grid.
				//borderWidth: 2.0,           // pixel width of border around grid.
				//backgroundColor: 'transparent', 
				drawBorder: false,
				shadow: false
			},
			highlighter: {
				show: false
			}
        });

        $('#bar1').bind('jqplotDataClick',
            function (ev, seriesIndex, pointIndex, data) {
				//alert(pointIndex);
                //$('#info1').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
				var formdata = { 'gid':pointIndex, 'type':'bar1','<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }
				$.ajax({
					type: "POST",
					url: '<?php echo base_url(); ?>dashboard/showLeadDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#leads-current-activity-list').empty();
						$('#leads-current-activity-list').show();
						$('#leads-current-activity-list').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="<?php echo base_url(); ?>assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
					},
					success: function(html){
						//alert(html.html);
						$('#leads-current-activity-list').empty();
						$("#leads-current-activity-list").show();
						if (html.html == 'NULL') {
							$('#leads-current-activity-list').html('');
						} else {
							$('#leads-current-activity-list').show();
							$('#leads-current-activity-list').html(html.html);
							
							$('#example_bar1').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
									//var TotalMarks = 0;
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6]; 
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#leads-current-activity-list").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			});
		$( "#barimg" ).click(function() {
			//var imgelem = evt.data.chart.jqplotToImageElem();
			var imgelem = $('#bar1').jqplotToImageElem();
			var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
			//alert(imageSrc);// return false;
			//open(imageSrc); // this will open the image in another tab
			var imgdata = imageSrc;
			var base_url = "<?php echo site_url(); ?>";		
		
			var url = base_url+"dashboard/savePdf/";
			var form = $('<form action="' + url + '" method="post">' +
			  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
			  '</form>');
			$('body').append(form);
			$(form).submit();
			
		});
	<?php } else { ?>
			$('#bar1').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
	<?php } ?>
    });
</script>

<script type="text/javascript">
$(document).ready(function(){
	// For horizontal bar charts, x and y values must will be "flipped"
    // from their vertical bar counterpart.
	$.jqplot.config.enablePlugins = true;
	<?php if (!empty($s4)) { ?>
    //var plot5 = $.jqplot('line1', [[[2,'a'], [4,'b'], [6,'c'], [3,'d']]], {
	var plot5 = $.jqplot('line1', [[<?php echo rtrim($s4, ','); ?>]], {
	//title:'Lead Aging',
	animate: !$.jqplot.use_excanvas,
        seriesDefaults: {
			shadow: false,
            renderer:$.jqplot.BarRenderer,
            // Show point labels to the right ('e'ast) of each bar.
            // edgeTolerance of -15 allows labels flow outside the grid
            // up to 15 pixels.  If they flow out more than that, they
            // will be hidden.
            pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
            // Rotate the bar shadow as if bar is lit from top right.
            shadowAngle: 135,
            // Here's where we tell the chart it is oriented horizontally.
            rendererOptions: {
                barDirection: 'horizontal',
				barWidth: 25,
				varyBarColor: true,
				animation: {
					speed: 4000
				}
            }
        },
		grid: {
				drawGridLines: true,        // wether to draw lines across the grid or not.
				gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
				background: '#ffffff',      // CSS color spec for background color of grid.
				borderColor: '#ffffff',     // CSS color spec for border around grid.
				borderWidth: 2.0,           // pixel width of border around grid.
				//backgroundColor: 'transparent', 
				drawBorder: false,
				shadow: false
		},
		highlighter: { 
				show: false
		},
		axesDefaults: {
			tickRenderer: $.jqplot.CanvasAxisTickRenderer ,         
			tickOptions: {
			  //angle: 10,
			  fontSize: '10pt'            
			},
			rendererOptions: {
				baselineWidth: 0.5,
				baselineColor: '#444444',
				drawBaseline: true
			}
		},
        axes: {
			xaxis: {
				label:'No. of Leads--->',
				tickOptions:{
					//fontFamily:'Arial',
					//fontSize: '10pt',
					//fontWeight:"bold",
					//angle: -30,
					show: false
				}
			},
            yaxis: {
				label:'No. of Days(from lead creation)--->',
				tickOptions:{
					fontFamily:'Arial',
					fontSize: '8pt',
					fontWeight:"bold"
					//angle: -30
					//show: false
				},
                renderer: $.jqplot.CategoryAxisRenderer,
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer
            }
        }
    });
	$('#line1').bind('jqplotDataClick',
		function (ev, seriesIndex, pointIndex, data) {
			//alert(pointIndex);
			var formdata = { 'gid':pointIndex, 'type':'line1','<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }
			$.ajax({
				type: "POST",
				url: '<?php echo base_url(); ?>dashboard/showLeadDetails/',
				dataType:"json",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					$('#charts_info2').empty();
					$('#charts_info2').show();
					$('#charts_info2').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="<?php echo base_url(); ?>assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
				},
				success: function(html){
					//alert(html.html);
					$('#charts_info2').empty();
					$("#charts_info2").show();
					if (html.html == 'NULL') {
						$('#charts_info2').html('');
					} else {
						$('#charts_info2').show();
						$('#charts_info2').html(html.html);
						
						$('#example_line1').dataTable( {
							"aaSorting": [[ 0, "desc" ]],
							"iDisplayLength": 5,
							"sPaginationType": "full_numbers",
							"bInfo": true,
							"bPaginate": true,
							"bProcessing": true,
							"bServerSide": false,
							"bLengthChange": false,
							"bSort": true,
							"bAutoWidth": false,
							"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
								//var TotalMarks = 0;
								var cost = 0
								for ( var i=0 ; i<aaData.length ; i++ )
								{
									var TotalMarks = aaData[i][6]; 
									cost += parseFloat(TotalMarks);
									
								}
								var nCells = nRow.getElementsByTagName('td');
								nCells[1].innerHTML = cost.toFixed(2);
							}
						});
						$('html, body').animate({ scrollTop: $("#charts_info2").offset().top }, 1000);
					}
				}                                                                                       
			});
			return false;
		}
	);
	$( "#lineimg" ).click(function() {
			//var imgelem = evt.data.chart.jqplotToImageElem();
			var imgelem = $('#line1').jqplotToImageElem();
			var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
			//alert(imageSrc);// return false;
			//open(imageSrc); // this will open the image in another tab
			var imgdata = imageSrc;
			var base_url = "<?php echo site_url(); ?>";		
		
			var url = base_url+"dashboard/savePdf/";
			var form = $('<form action="' + url + '" method="post">' +
			  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
			  '</form>');
			$('body').append(form);
			$(form).submit();
			
			
			/*
			var formdata = { 'data':data, 'type':'funnel' }
			$.ajax({
				type: "POST",
				url: '<?php echo base_url(); ?>dashboard/savePdf/',
				dataType:"json",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					//$("#loadingImage").show();
					//$('#res').hide();
				},
				success: function(html){
					
				}                                                                                       
			});*/
		});
	<?php } else { ?>
			$('#line1').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
	<?php } ?>
});
</script>

<script type="text/javascript">
$(document).ready(function(){
	//var inpu = [['Cup', 5000], ['Gen', 98876], ['HDTV', 15000], ['dul', 12545], ['Mod', 3987], ['Tck', 66545], ['Hai', 1809]];
	<?php if (!empty($cls_oppr)) { ?>
	var plot6 = $.jqplot('line2', [[<?php echo rtrim($cls_oppr, ','); ?>]], {
		seriesDefaults: {
			rendererOptions: {
			smooth: true
			}
		},
		
		grid: {
			drawGridLines: false,        // wether to draw lines across the grid or not.
			gridLineColor: '#C7C7C7',   // CSS color spec of the grid lines.
			background: '#ffffff',      // CSS color spec for background color of grid.
			//borderColor: '#999999',     // CSS color spec for border around grid.
			borderWidth: 1.0,		// pixel width of border around grid.
			//backgroundColor: 'transparent', 
			drawBorder: true,
			shadow: false
		},
		
		axesDefaults: {
			labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
			rendererOptions: {
				baselineWidth: 0.6,
				baselineColor: '#444444',
				drawBaseline: true
			},
			tickOptions:{
				fontWeight:"bold"
			}
		},
		axes: {
			xaxis: {
			  renderer: $.jqplot.CategoryAxisRenderer,
			  label: 'Current Financial Year-->',
			  labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
			  tickRenderer: $.jqplot.CanvasAxisTickRenderer,
			  tickOptions: {
				  //angle: -10,
				  fontFamily: 'Courier New',
				  fontSize: '10pt',
				  showGridline: true
			  }
			},
			yaxis: {
				label: 'Values(USD)-->',
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
				tickOptions: {
				  //angle: -10,
				  fontFamily: 'Courier New',
				  fontSize: '10pt',
				  showGridline: true
			  }
			}
		},
		
		highlighter: { 
			show: false,
			//tooltipAxes: 'y',
			//tooltipLocation: 'nw',
			useAxesFormatters:false
		}
	});
	
	$('#line2').bind('jqplotDataClick',
		function (ev, seriesIndex, pointIndex, data) {
			//alert(data); return false;
			var formdata = { 'gid':pointIndex, 'type':'line2','<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }
			$.ajax({
				type: "POST",
				url: '<?php echo base_url(); ?>dashboard/showLeadDetails/',
				dataType:"json",                                                                
				data: formdata,
				cache: false,
				beforeSend:function(){
					$('#charts_info2').empty();
					$('#charts_info2').show();
					$('#charts_info2').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="<?php echo base_url(); ?>assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
				},
				success: function(html){
					//alert(html.html);
					$('#charts_info2').empty();
					$("#charts_info2").show();
					if (html.html == 'NULL') {
						$('#charts_info2').html('');
					} else {
						$('#charts_info2').show();
						$('#charts_info2').html(html.html);
						
						$('#example_line2').dataTable( {
							"aaSorting": [[ 0, "desc" ]],
							"iDisplayLength": 5,
							"sPaginationType": "full_numbers",
							"bInfo": true,
							"bPaginate": true,
							"bProcessing": true,
							"bServerSide": false,
							"bLengthChange": false,
							"bSort": true,
							"bAutoWidth": false,
							"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
								//var TotalMarks = 0;
								var cost = 0
								for ( var i=0 ; i<aaData.length ; i++ )
								{
									var TotalMarks = aaData[i][6]; 
									cost += parseFloat(TotalMarks);
									
								}
								var nCells = nRow.getElementsByTagName('td');
								nCells[1].innerHTML = cost.toFixed(2);
							}
						});
						$('html, body').animate({ scrollTop: $("#charts_info2").offset().top }, 1000);
					}
				}                                                                                       
			});
			return false;
		}
	);
	$( "#line2img" ).click(function() {
		//var imgelem = evt.data.chart.jqplotToImageElem();
		var imgelem = $('#line2').jqplotToImageElem();
		var imageSrc = imgelem.src; // this stores the base64 image url in imagesrc
		//alert(imageSrc);// return false;
		//open(imageSrc); // this will open the image in another tab
		var imgdata = imageSrc;
		var base_url = "<?php echo site_url(); ?>";		
	
		var url = base_url+"dashboard/savePdf/";
		var form = $('<form action="' + url + '" method="post">' +
		  '<input type="hidden" name="img_data" value="' +imgdata+ '" />' +
		  '</form>');
		$('body').append(form);
		$(form).submit();
	});
	<?php } else { ?>
			$('#line2').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
	<?php } ?>
});
</script>

<script type="text/javascript">
function getLeadDashboardTable(userid, user_name) {
	var baseurl = '<?php echo $this->config->item('base_url') ?>';
	$.ajax({
	url : baseurl + 'dashboard/getLeadDependency/'+ userid + "/" + user_name,
		success : function(response){
			if(response != '') {
				$("#lead-dependency-list").show();
				$("#lead-dependency-list").html(response);
				$('#lead-dependency-table').dataTable( {
					"aaSorting": [[ 0, "desc" ]],
					"iDisplayLength": 5,
					"sPaginationType": "full_numbers",
					"bInfo": true,
					"bPaginate": true,
					"bProcessing": true,
					"bServerSide": false,
					"bLengthChange": false,
					"bSort": true,
					"bAutoWidth": false,
					"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
						//var TotalMarks = 0;
						var cost = 0
						for ( var i=0 ; i<aaData.length ; i++ )
						{
							var TotalMarks = aaData[i][6];
							cost += parseFloat(TotalMarks);
						}
						//$('#lead-dependency-table').append('<p>'+cost+'</p>');
						var nCells = nRow.getElementsByTagName('td');
						nCells[1].innerHTML = cost.toFixed(2);
						
					}
				});
				$('html, body').animate({ scrollTop: $("#lead-dependency-list").offset().top }, 1000);
			} 			
		}
	});
}
</script> 

<script type="text/javascript">
function getLeadAssigneeTable(userid,user_name) {
	var baseurl = '<?php echo $this->config->item('base_url') ?>';
	$.ajax({
	url : baseurl + 'dashboard/getLeadAssigneeDependency/'+ userid+'/'+user_name,
		beforeSend:function(){
			$('#lead-dependency-list').empty();
			$("#lead-dependency-list").show();
			$('#lead-dependency-list').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="<?php echo base_url(); ?>assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success : function(response){
			if(response != '') {
				$('#lead-dependency-list').empty();
				$("#lead-dependency-list").show();
				$("#lead-dependency-list").html(response);
				$('#lead-assignee-table').dataTable( {
					"aaSorting": [[ 0, "desc" ]],
					"iDisplayLength": 5,
					"sPaginationType": "full_numbers",
					"bInfo": true,
					"bPaginate": true,
					"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
						//var TotalMarks = 0;
						var cost = 0
						for ( var i=0 ; i<aaData.length ; i++ )
						{
							var TotalMarks = aaData[i][6]; 
							cost += parseFloat(TotalMarks);
							
						}
						var nCells = nRow.getElementsByTagName('td');
						nCells[1].innerHTML = cost.toFixed(2);
						
					},
					"bProcessing": true,
					"bServerSide": false,
					"bLengthChange": false,
					"bSort": true,
					"bAutoWidth": false
				});
				$('html, body').animate({ scrollTop: $("#lead-dependency-list").offset().top }, 1000);
			} 			
		}
	});
}
function getCurrentLeadActivity(jobid,lead_name)  {
	var baseurl = '<?php echo $this->config->item('base_url') ?>';
	$.ajax({
	url : baseurl + 'dashboard/getLeadsCurrentActivity/'+ jobid+'/'+lead_name,
		success : function(response){
			if(response != '') {
				$("#leads-current-activity-list").show();
				$("#leads-current-activity-list").html(response);
				$('#leads-current-activity-table').dataTable( {
					"bInfo": false,
					"bPaginate": false,
					"bSort": false,
					"bFilter": false,
					"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
						//var TotalMarks = 0;
						var cost = 0
						for ( var i=0 ; i<aaData.length ; i++ )
						{
							var TotalMarks = aaData[i][6]; 
							//var str = TotalMarks.split(" ");
							//cost += parseFloat(str[1]);
							cost += parseFloat(TotalMarks);
							
						}
						var nCells = nRow.getElementsByTagName('td');
						//nCells[1].innerHTML = "USD " + cost.toFixed(2);
						nCells[1].innerHTML = cost.toFixed(2);
					}
				});
				$('html, body').animate({ scrollTop: $("#leads-current-activity-list").offset().top }, 1000);
			} 			
		}
	});
}
</script> 

<script type="text/javascript">
$(document).ready(function(){
	$('.table_grid').dataTable({
		"iDisplayLength": 5,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": false,
		"bSort": true,
		"bAutoWidth": false
	});	
});
$('#leads-current-activity-list').delegate('.grid-close','click',function(){
	var $lead = $("#leads-current-activity-list");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});
$('#lead-dependency-list').delegate('.grid-close','click',function(){
	var $lead = $("#lead-dependency-list");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});
$('#charts_info').delegate('.grid-close','click',function(){
	var $lead = $("#charts_info");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});
$('#charts_info2').delegate('.grid-close','click',function(){
	var $lead = $("#charts_info2");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});
$('#charts_info3').delegate('.grid-close','click',function(){
	var $lead = $("#charts_info3");
	$lead.slideUp('fast', function () { $lead.css('display','none'); });
});

$('#current-lead-report').change(function() {
	var statusVar = 'statusVar='+$(this).val()+','+'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>';
	var baseurl = '<?php echo $this->config->item('base_url') ?>';
	$.ajax({
	type: 'GET',
	url : baseurl + 'dashboard/get_leads_current_weekly_monthly_report/',
	data: statusVar,
		success : function(response){
			if(response != '') {
				$("#weekly-monthly").html(response);
				$('#weekly-monthly-table').dataTable({
					"iDisplayLength": 5,
					"sPaginationType": "full_numbers",
					"bInfo": true,
					"bPaginate": true,
					"bProcessing": true,
					"bServerSide": false,
					"bLengthChange": false,
					"bSort": true,
					"bAutoWidth": false
				});
			} 			
		}
	});
	
});
/* dashboard excel report starts here */
/* Lead Owner report */
$('#lead-dependency-list').delegate('#lead-ownner-export','click',function(){
        var user_id = $('#lead-dependency-table').attr('name'); 
		var user_name = $('#lead-owner-username').val(); 
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+user_id+'/'+user_name+'/leadowner';
		document.location.href = sturl;
		return false;
});
/*lead assignee report */
$('#lead-dependency-list').delegate('#lead-assignee-export','click',function(){
        var user_id = $('#lead-assignee-table').attr('name'); 
		var user_name = $('#lead-assignee-username').val();  
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+user_id+'/'+user_name+'/assignee';
		document.location.href = sturl;
		return false;
});

/*current pipeline report */
$('#charts_info').delegate('#current-pipeline-export','click',function(){
	    var lead_stage_name = $("#current-pipeline-export").attr('name'); //alert(lead_stage_name);
		var type = $("#lead-type-name").val();  
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+"pipeline/"+lead_stage_name+"/"+type;
		document.location.href = sturl;
		return false;
});
/*lead by region report*/
$('#charts_info').delegate('#leads-by-region-export','click',function(){
	    var lead_region_name = $("#leads-by-region-export").attr('name'); 
		var type = $("#lead-by-region").val(); // alert(lead_stage_name + " " + type);
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+"leadsregion/"+lead_region_name+"/"+type;
		document.location.href = sturl;
		return false;
});
/*lead current activity report */
$('#leads-current-activity-list').delegate('#lead-current-activity-export','click',function(){
	    //var current_activity = $("#lead-current-activity-export").attr('name'); 
		var lead_no = $("#lead-no").val(); 
		var lead_name = $("#lead-no").attr('name');
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+lead_name+"/"+lead_no+"/currentactivity";
		document.location.href = sturl;
		return false;
});

/*lead aging report */
$('#charts_info2').delegate('#lead-aging-report','click',function(){
	    var lead_aging = $("#lead-aging-report").attr('name');
		var type = $("#lead-aging-type").val();   
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+lead_aging+"/"+type+"/leadsaging";
		document.location.href = sturl;
		return false;
});
$('#charts_info2').delegate('#closed-oppor-report','click',function(){
	    var gra_id = $("#closed-oppor-report").attr('name');
		var type = $("#cls-oppr-type").val();   
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+gra_id+"/"+type+"/closedopp";
		document.location.href = sturl;
		return false;
});
$('#leads-current-activity-list').delegate('#least-active-report','click',function() {
	    var lead_indi = $("#least-active-report").attr('name');
		var type = $("#least-active-type").val(); 
		//alert(type); return false;
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+lead_indi+"/"+type+"/leastactive";
		document.location.href = sturl;
		return false;
});
//for pie2 & pie3 charts export
$('#charts_info3').delegate('#leads-by-leadsource-export','click',function(){
	    var arg1 = $("#leads-by-leadsource-export").attr('name'); 
		var arg2 = $("#lead-by-leadsource").val();   
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+"pipeline/"+arg1+"/"+arg2;
		document.location.href = sturl;
		return false;
});
$('#charts_info3').delegate('#leads-by-service-req-export','click',function(){
	    var arg1 = $("#leads-by-service-req-export").attr('name');
		var arg2 = $("#lead-by-service-req").val();   
		var baseurl = '<?php echo $this->config->item('base_url') ?>';
		var sturl = baseurl+"dashboard/excel_export_lead_owner/"+"pipeline/"+arg1+"/"+arg2;
		document.location.href = sturl;
		return false;
});
/* dashboard excel report ends here */
</script>

<script class="code" type="text/javascript">
$(document).ready(function(){
   <?php if (!empty($s7)) { ?>
	$.jqplot.config.enablePlugins = true;
	var plot7 = $.jqplot('pie2', [[<?php echo rtrim($s7, ','); ?>]], {
	// var plot7 = $.jqplot('pie2', [[['Verwerkende industrie', 9],['Retail', 0], ['Primaire producent', 2], ['Out of home', 4],['Groothandel', 6], ['Grondstof', 1], ['Consument', 3], ['Bewerkende industrie', 2]]], {
        gridPadding: {top:25, bottom:24, left:0, right:0},
		//title:'<?php echo $chart_title; ?>',
		animate: !$.jqplot.use_excanvas,
		animateReplot: true,
        seriesDefaults:{
			shadow: false,
            renderer:$.jqplot.PieRenderer, 
            trendline:{ show:false }, 
            rendererOptions: { 
				padding: 8,
				sliceMargin: 2,
				showDataLabels: true
			},
			highlighter: {
				show: true,
				formatString:'%s',
				tooltipLocation:'ne', 
				useAxesFormatters:false
			}				 
        },
		grid: {
				drawGridLines: true,        // wether to draw lines across the grid or not.
				gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
				background: '#ffffff',      // CSS color spec for background color of grid.
				borderColor: '#ffffff',     // CSS color spec for border around grid.
				//borderWidth: 2.0,           // pixel width of border around grid.
				//backgroundColor: 'transparent', 
				drawBorder: false,
				shadow: false
		},
        legend:{
            show:true, 
			fontSize: '9pt',
			location: 'e',
			border: false
        },
		seriesColors: ["#eaa228", "#ff5800", "#c5b47f", "#8bbc21", "#579575", "#1aadce", "#839557", "#910000", "#027997", "#953579", "#422460", "#4b5de4", "#48596a", "#4bb2c5", "#0d233a", "#f0eded", "#492970", "#cc99cc", "#bfdde5", "#66ffcc", "#c747a3", "#ff99ff", "#ffff00", "#cc0000", "#a35b2e"]
    });
	$('#pie2').bind('jqplotDataClick',
			function (ev, seriesIndex, pointIndex, data) {
				//alert(data);
				//$('#info1').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
				var formdata = { 'data':data, 'type':'pie2','<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }
				$.ajax({
					type: "POST",
					url: '<?php echo base_url(); ?>dashboard/showLeadsDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info3').empty();
						$('#charts_info3').show();
						$('#charts_info3').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="<?php echo base_url(); ?>assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
						//$('#res').hide();
					},
					success: function(html){
						//alert(html.html);
						//$("#loadingImage").hide();
						$('#charts_info3').empty();
						$("#charts_info3").show();
						if (html.html == 'NULL') {
							$('#charts_info3').html('');
						} else {
							$('#charts_info3').show();
							$('#charts_info3').html(html.html);
							
							$('#example_pie2').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
								    //alert(nRow);
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6];
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info3").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			}
		);
		<?php } else { ?>
			$('#pie2').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		<?php } ?>
});
</script>

<script class="code" type="text/javascript">
$(document).ready(function(){
   <?php if (!empty($s8)) { ?>
	$.jqplot.config.enablePlugins = true;
	var plot8 = $.jqplot('pie3', [[<?php echo rtrim($s8, ','); ?>]], {
		gridPadding: {top:25, bottom:24, left:0, right:0},
		//title:'<?php echo $chart_title; ?>',
		animate: !$.jqplot.use_excanvas,
		animateReplot: true,
		seriesDefaults:{
			shadow: false,
			renderer:$.jqplot.PieRenderer, 
			trendline:{ show:false }, 
			rendererOptions: { 
				padding: 8,
				sliceMargin: 2,
				showDataLabels: true
			},
			highlighter: {
				show: true,
				formatString:'%s',
				tooltipLocation:'sw', 
				useAxesFormatters:false
			}				 
		},
		grid: {
				drawGridLines: true,        // wether to draw lines across the grid or not.
				gridLineColor: '#ffffff',   // CSS color spec of the grid lines.
				background: '#ffffff',      // CSS color spec for background color of grid.
				borderColor: '#ffffff',     // CSS color spec for border around grid.
				//borderWidth: 2.0,           // pixel width of border around grid.
				//backgroundColor: 'transparent', 
				drawBorder: false,
				shadow: false
		},
		legend:{
			show:true, 
			fontSize: '9pt',
			location: 'e',
			border: false
		},
		seriesColors: ["#eaa228", "#ff5800", "#c5b47f", "#8bbc21", "#579575", "#1aadce", "#839557", "#910000", "#027997", "#953579", "#422460", "#4b5de4", "#48596a", "#4bb2c5", "#0d233a", "#f0eded", "#492970", "#cc99cc", "#bfdde5", "#66ffcc", "#c747a3", "#ff99ff", "#ffff00", "#cc0000", "#a35b2e"]
    });
	$('#pie3').bind('jqplotDataClick',
			function (ev, seriesIndex, pointIndex, data) {
				//alert(data);
				//$('#info1').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
				var formdata = { 'data':data, 'type':'pie3','<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }
				$.ajax({
					type: "POST",
					url: '<?php echo base_url(); ?>dashboard/showLeadsDetails/',
					dataType:"json",                                                                
					data: formdata,
					cache: false,
					beforeSend:function(){
						$('#charts_info3').empty();
						$('#charts_info3').show();
						$('#charts_info3').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="<?php echo base_url(); ?>assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
					},
					success: function(html){
						//alert(html.html);
						$('#charts_info3').empty();
						$("#charts_info3").show();
						if (html.html == 'NULL') {
							$('#charts_info3').html('');
						} else {
							$('#charts_info3').show();
							$('#charts_info3').html(html.html);
							
							$('#example_pie3').dataTable( {
								"aaSorting": [[ 0, "desc" ]],
								"iDisplayLength": 5,
								"sPaginationType": "full_numbers",
								"bInfo": true,
								"bPaginate": true,
								"bProcessing": true,
								"bServerSide": false,
								"bLengthChange": false,
								"bSort": true,
								"bAutoWidth": false,
								"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
								    //alert(nRow);
									var cost = 0
									for ( var i=0 ; i<aaData.length ; i++ )
									{
										var TotalMarks = aaData[i][6];
										cost += parseFloat(TotalMarks);
										
									}
									var nCells = nRow.getElementsByTagName('td');
									nCells[1].innerHTML = cost.toFixed(2);
								}
							});
							$('html, body').animate({ scrollTop: $("#charts_info3").offset().top }, 1000);
						}
					}                                                                                       
				});
				return false;
			}
		);
		<?php } else { ?>
			$('#pie3').html("<div align='center' style='padding:20px; font-size: 15px; font-weight: bold; line-height: 20px;'>No Data Available...</div>");
		<?php } ?>
});
</script>
<?php } ?>

<?php if($this->session->userdata('viewPjt')==1) { ?>
<script>
//For Projects
var pjtstage = $("#pjt_stage").val(); 
var pm_acc = $("#pm_acc").val(); 
var cust = $("#customer1").val(); 
var keyword = $("#keywordpjt").val(); 
//alert(keyword);
if(keyword == "Project No, Project Title, Name or Company")
	keyword = 'null';

if (document.getElementById('advance_search_pjt'))
	document.getElementById('advance_search_pjt').style.display = 'none';	

var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
//alert(sturl);	
$('#advance_search_results_pjts').load(sturl);
	
function advanced_filter_pjt(){
	$('#advance_search_pjt').slideToggle('slow');
	var  keyword = $("#keywordpjt").val();
	var status = document.getElementById('advance_search_pjt').style.display;
	
	if(status == 'none') {
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val(); 
	}
	else   {
			
		$("#pjt_stage").val("");
		$("#pm_acc").val("");
		$("#customer1").val("");

	}
}

$('#advanceFilters_pjt').submit(function() {	
	var pjtstage = $("#pjt_stage").val(); 
	var pm_acc = $("#pm_acc").val(); 
	var cust = $("#customer1").val(); 
	var  keyword = $("#keywordpjt").val(); 
	if(keyword == "Project No, Project Title, Name or Company")
	keyword = 'null';
	document.getElementById('advance_search_results_pjts').style.display = 'block';	
	var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
	//alert(sturl);
	$('#advance_search_results_pjts').load(sturl);	
	return false;
});

$('#pjt_search_form').submit(function() {	
		var  keyword = $("#keywordpjt").val(); 
		if(keyword == "Project No, Project Title, Name or Company")
		keyword = 'null';
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val();  
		//document.getElementById('ad_filter').style.display = 'block';
		var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
		$('#advance_search_results_pjts').load(sturl);
		return false;
});
</script>
<?php } ?>
<script>
//For Tasks
/*mychanges*/
$(function(){
	$('.all-tasks').load('tasks/index/extend #task-page .task-contents', {}, loadEditTables);
	$('#set-job-task .pick-date, #search-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M'});
	
	$('#task_search_user').val('<?php echo $userdata['userid']; ?>');
	/* job tasks character limit */
	$('#job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		var remain_len = 1000 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		$('#task-desc-countdown').text(remain_len);
	});
	$('#edit-job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		
		var remain_len = 1000 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		
		$('#edit-task-desc-countdown').text(remain_len);
	});
});
function searchTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	$.post('tasks/search',$('#search-job-task').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function searchtodayTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	$.post('tasks/search','task_search_user='+$('#task_search_user').val()+'&task_search_start_date='+$('#hided').val()+'&task_search_end_date='+$('#hided').val()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function loadEditTables(){
	$('#jv-tab-4').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	var taskids = [];
	$('td.task.random-task').each(function(){
		taskids.push($(this).attr('rel'));
		
			$(this).append('<div class="buttons" style="display:none"> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit |</button> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'complete\'); return false;">Approve |</button> \
								<button type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'delete\'); return false;">Delete</button> \
							</div>');	
		
	});
	$('td.task.newrandom-task').each(function(){
		taskids.push($(this).attr('rel'));
			$(this).append('<div class="buttons" style="display:none"> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit</button> \
							</div>');
	});
	if (taskids.length < 1)	{
		$('#jv-tab-4').unblock();
		return;
	}
	$.post('ajax/request/get_random_tasks',{'id_set': taskids.join(','),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},function(data){

		if (data != '')	{
			$('form.random-task-tables').html(data);
		} 

		$('#jv-tab-4').unblock();
	});
}
</script>
<?php
require ('tpl/footer.php');
ob_end_flush();
?>