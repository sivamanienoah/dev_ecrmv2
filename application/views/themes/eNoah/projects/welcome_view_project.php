<?php require (theme_url().'/tpl/header.php'); ?>

<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>
<script type="text/javascript">var this_is_home = true;</script>

<!--Code Added for the Pagination in Comments Section -- Starts Here-->
<script type="text/javascript">

  var project_jobid           = "<?php echo isset($quote_data['lead_id']) ? $quote_data['lead_id'] : 0 ?>";
  var project_code            = "<?php echo isset($quote_data['pjt_id']) ? $quote_data['pjt_id'] : 0 ?>";
  var expect_worth_id         = "<?php echo isset($quote_data['expect_worth_id']) ? $quote_data['expect_worth_id'] : 1 ?>";
  var project_view_quotation  = "<?php echo $view_quotation; ?>";
  var project_user_id         = "<?php echo isset($userdata['userid']) ? $userdata['userid'] : 0 ?>";
  var project_job_title		  = "<?php echo str_replace("'", "\'", $quote_data['lead_title']) ?>";
  var project_job_status      = "<?php echo (isset($quote_data['lead_stage'])) ? $quote_data['lead_stage'] : 0 ?>";
  var project_request_url     = "http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>";
  var project_assigned_to     = "<?php echo $quote_data['assigned_to']; ?>";
  var project_userdata    	  = "<?php echo $userdata; ?>";
  var project_complete_status = "<?php echo isset($quote_data['complete_status']) ? $quote_data['complete_status'] : 0 ?>";
  var proj_location			  = 'http://<?php echo $_SERVER['HTTP_HOST'], preg_replace('/[0-9]+/', '{{lead_id}}', $_SERVER['REQUEST_URI']) ?>';
  var rag_stat_id			  = "<?php echo $quote_data['rag_status']; ?>";

</script>
<script type="text/javascript" src="assets/js/projects/welcome_view_project.js"></script>
<script type="text/javascript" src="assets/js/jquery.screwdefaultbuttonsV2.js"></script>

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

<div class="comments-log-container" style="display:none;">
	<?php if ($log_html != "") { ?>
			<table width="100%" class="log-container"> 
				<tbody>
				<?php 
					echo $log_html;
				?>				
				</tbody> 
			</table>
	<?php } else { echo "No Comments Found."; }?>
</div>

<div id="content">
    <?php
		$date_used = $quote_data['date_created'];
	?>
    <div class="inner q-view">
		<div class="right-communication">		
			<form id="comm-log-form">
			
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<textarea name="job_log" id="job_log" class="textfield width99pct height100px gray-text">Click to view options</textarea>
				<div style="position:relative;">
					<textarea name="signature" class="textfield width99pct" rows="4" readonly="readonly" style="color:#666;"><?php echo $userdata['signature'] ?></textarea>
					<span style="position:absolute; top:5px; right:18px;"><a href="#comm-log-form" onclick="whatIsSignature(); return false;">What is this?</a></span>
				</div>
				
				<div style="overflow:hidden;">
					
					<p class="right" style="padding-top:5px;">Mark as a <a href="#was" onclick="whatAreStickies(); return false;">stickie</a> <input type="checkbox" name="log_stickie" id="log_stickie" /></p>
					<div class="button-container">
						<div class="buttons">
							<button type="submit" class="positive" onclick="addLog();  return false;" id="add-log-submit-button">Add Post</button>
						</div>
					</div>
				
				</div>
			
			<?php
			if (isset($userdata))
			{
			?>
				<div class="email-set-options" style="overflow:hidden;">
					<!--table border="0" cellpadding="0" cellspacing="0" class="client-comm-options">
						<tr>
							<td rowspan="2" class="action-td" valign="top" align="right"><a href="#" onclick="addClientCommOptions(); $(this).blur(); return false;">Communicate<br />to Client via</td>
							<td><input type="checkbox" name="client_comm_phone" value="<?php echo (isset($quote_data['phone_1'])) ? $quote_data['phone_1'] : '' ?>"> <span>Phone</span></td>
							<td><input type="checkbox" name="client_comm_sms" value="<?php echo (isset($quote_data['phone_3'])) ? $quote_data['phone_3'] : '' ?>"> <span>SMS</span></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="client_comm_mobile" value="<?php echo (isset($quote_data['phone_3'])) ? $quote_data['phone_3'] : '' ?>"> <span>Mobile</span></td>
							<td><input type="checkbox" name="client_comm_email" value="<?php echo (isset($quote_data['email_1'])) ? $quote_data['email_1'] : '' ?>"> <span>Email</span></td>
						</tr>
					</table-->

					<input type="checkbox" name="email_to_customer" id="email_to_customer" /> <label for="email_to_customer" class="normal">Email Client</label>
					<input type="hidden" name="client_email_address" id="client_email_address" value="<?php echo  (isset($quote_data)) ? $quote_data['email_1'] : '' ?>" />
					<input type="hidden" name="client_full_name" id="client_full_name" value="<?php echo  (isset($quote_data)) ? $quote_data['first_name'] . ' ' . $quote_data['last_name'] : '' ?>" />
					<input type="hidden" name="requesting_client_approval" id="requesting_client_approval" value="0" />
					
					<p id="multiple-client-emails">
						<input type="checkbox" name="client_emails_1" id="client_emails_1" value="<?php echo $quote_data['email_1'] ?>" /> <?php echo $quote_data['email_1'] ?>
						<?php
						if ($quote_data['email_2'] != '')
						{
							?>
							<br /><input type="checkbox" name="client_emails_2" id="client_emails_2" value="<?php echo $quote_data['email_2'] ?>" /> <?php echo $quote_data['email_2'] ?>
							<?php
						}
						if ($quote_data['email_3'] != '')
						{
							?>
							<br /><input type="checkbox" name="client_emails_3" id="client_emails_3" value="<?php echo $quote_data['email_3'] ?>" /> <?php echo $quote_data['email_3'] ?>
							<?php
						}
						if ($quote_data['email_4'] != '')
						{
							?>
							<br /><input type="checkbox" name="client_emails_4" id="client_emails_4" value="<?php echo $quote_data['email_4'] ?>" /> <?php echo $quote_data['email_4'] ?>
							<?php
						}
						?>
						<br />
						Additional Emails (separate addresses with a comma)<br />
						<input type="text" name="additional_client_emails" id="additional_client_emails" class="textfield width99pct" />
					</p>
					
				</div>
			<?php
			}
			?>
			
				<div class="email-list">
					<p><label>Email to:</label></p>
					<?php
				    $restrict1[] = 0;
					if (is_array($contract_users) && count($contract_users) > 0) { 
						foreach ($contract_users as $data) {
							$restrict1[] = $data['userid_fk'];
						}
					}
					//echo "<pre>"; print_r($restrict1);
					
					$r_users = implode(",",$list_users);
					$restrict = explode(",",$r_users);
					//print_r($restrict);
					
					//Merge the contract users, lead owner, lead sssigned_to & project Manager.
					$rest_users = array_merge_recursive($restrict, $restrict1);
					$restrict_users = array_unique($rest_users);
					
					//Re-Assign the Keys in the array.
					$final_restrict_user = array_values($restrict_users);
				
					$cnt = count($user_accounts);
					
					if (count($final_restrict_user)) {
						for($i=0; $i < $cnt; $i++)
						{	
							$usid = $user_accounts[$i]['userid'];

							for($j=0; $j<count($final_restrict_user); $j++) {
							//echo $restrict[$j];

								if($usid == $final_restrict_user[$j]) {
									echo '<span class="user">' .
									'<input type="checkbox" name="email-log-' . $user_accounts[$i]['userid'] . '" id="email-log-' . $user_accounts[$i]['userid'] . '" /> <label for="email-log-' . $user_accounts[$i]['userid'] . '">' . $user_accounts[$i]['first_name'] . ' ' . $user_accounts[$i]['last_name'] . '</label></span>';
								}	
							}
							
						}
					}
					else {
						echo "No user found";
					} 
					?>
				</div>
			</form>
			<p>&nbsp;</p>
		</div>
		
        <div class="pull-left side1 test-block"> 
			<h2 class="job-title"> <?php echo htmlentities($quote_data['lead_title'], ENT_QUOTES); ?> </h2>
			<?php
				if (isset($quote_data['pjt_id'])) {
					$varPjtId = $quote_data['pjt_id'];
				}
				
				$readonly_status = false;
				if($chge_access != 1)
				$readonly_status = true;
				if($quote_data['pjt_status'] == 2)
				$readonly_status = true;
			?>
			<form>
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<div class="pull-left">
					<label class="practices">Project Title</label>
					<input type="text" name="lead_title" id="lead_title" class="textfield" size="40" value="<?php echo isset($quote_data['lead_title']) ? $quote_data['lead_title'] : ''; ?>" <?php if ($readonly_status == true) { ?> disabled <?php } ?> />
				</div>
				<div>
				<?php if ($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?>
					<div class="buttons">
						<button type="submit" class="positive" style="margin:0 0 0 5px;" onclick="updateTitle(); return false;">Set</button>
					</div>
					<div id="resmsg_projecttitle" style="margin: 5px 0px 0px 5px; display: inline-block;"></div>
				<?php } ?>
				</div>
			</form>
			<form>
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<div class="pull-left">
					<label class="practices">Practice</label>
					<select name="practice" id="practice" class="textfield" <?php if ($readonly_status == true) { ?> disabled <?php } ?> style="width: 135px;">
						<option value="">Select Practice</option>
						<?php if(!empty($practices)) {
							foreach($practices as $pract) {
								$selectedPractice = '';
								if($pract['id'] == $quote_data['practice']) {
									$selectedPractice = 'selected="selected"';
								}
						?>
								<option value="<?php echo $pract['id']; ?>" <?php echo $selectedPractice; ?>><?php echo $pract['practices']; ?></option>
						<?php
							}
						} 
						?>
					</select>
				</div>
				<div>
				<?php if ($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?>
					<div class="buttons">
						<button type="submit" class="positive" style="margin:0 0 0 5px;" onclick="setPractices(); return false;">Set</button>
					</div>
					<div id="resmsg_practice" style="margin: 5px 0px 0px 5px; display: inline-block;"></div>
				<?php } ?>
				</div>
			</form>
			
			<label class="pull-left">Billing Type</label>
			<div style="line-height: 25px;">
				<input type="radio" name="billing_type" class="bill_type" value="1" id="milestone_driven" <?php if ($readonly_status == true) { ?> disabled <?php } ?> <?php if($quote_data['billing_type'] == 1) { echo 'checked="checked"'; } ?> > Milestone Driven
				<input type="radio" name="billing_type" value="2" class="bill_type" id="monthly_driven" <?php if ($readonly_status == true) { ?> disabled <?php } ?> <?php if($quote_data['billing_type'] == 2) { echo 'checked="checked"'; } ?> > Monthly
				<span id="errmsg_bill_type" style="color:red"></span>
			</div>
			<div class="clear"></div>
			<form>
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<div class="pull-left">
					<label class="project-id">Project ID</label>
					<input class="textfield" type="text" name="pjtId" id="pjtId" maxlength="20" value="<?php if (isset($varPjtId)) echo $varPjtId; ?>" <?php if ($readonly_status == true) { ?>readonly<?php } ?> style="width: 125px;" />
					<input type="hidden" class="hiddenUrl"/>
				</div>
				<div>
				<?php if ($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?>
					<div class="buttons">
						<button type="submit" class="positive" id="submitid" style="margin:0 0 0 5px;" onclick="setProjectId(); return false;">Set</button>
					</div>
					<div class="error-msg">
						<span id="pjt_id_errormsg" style="color:red"></span>
						<span class="checkUser" style="color:green">Project Id Saved.</span>
						<span class="checkUser1" id="id-existsval" style="color:red">Project ID Already Exists.</span>
					</div>
				<?php } ?>
				</div>
			</form>
			<form>
				<div class="pull-left">
					<label class="project-status">Project Status</label>
					<select name="pjt_status" id="pjt_status" class="textfield" <?php if ($chge_access != 1) { ?> disabled <?php } ?> style="width: 135px;">
						<option value="1"  <?php if($quote_data['pjt_status'] == 1) echo 'selected="selected"'; ?>>Project In Progress</option>
						<option value="2"  <?php if($quote_data['pjt_status'] == 2) echo 'selected="selected"'; ?>>Project Completed</option>
						<option value="3"  <?php if($quote_data['pjt_status'] == 3) echo 'selected="selected"'; ?>>Project Onhold</option>
						<option value="4"  <?php if($quote_data['pjt_status'] == 4) echo 'selected="selected"'; ?>>Inactive</option>
					</select>
					<input type="hidden" class="hiddenUrl"/>
				</div>					
				<?php if ($chge_access == 1) { ?>
				<div class="buttons">
					<button type="submit" class="positive" id="submitid" style="margin:0 0 0 5px;" onclick="setProjectStatus(); return false;">Set</button>
					<div id="resmsg" class="error-msg"></div>
				</div>
				<?php } ?>
			</form>
			<div class="pull-left"><label class="rag">RAG Status</label></div>
			<div class="rag-status">
				<input type="radio" name="rag_status" class="rag_stat" value="1" id="red" <?php if ($readonly_status == true) { ?> disabled <?php } ?> >
				<input type="radio" name="rag_status" class="rag_stat" value="2" id="amber" <?php if ($readonly_status == true) { ?> disabled <?php } ?> >
				<input type="radio" name="rag_status" class="rag_stat" value="3" id="green" <?php if ($readonly_status == true) { ?> disabled <?php } ?> >
				<span id="errmsg_rag_status" style="color:red; float: right; margin: 6px 0px 0px 5px;"></span>
			</div>

			<!-- Project Progress Thermometer - Start -->
			<div style="margin:10px 0; ">
				<h6 class="status-title">Project Completion Status &nbsp; <span class="small" style="color:#a51e04" >[ Current Status - <em><strong>0</strong>% Completed </em> ]</span></h6>
				<div class="meter-container">
					<div class="track-meter"></div>
					<div class="track-progress-left"></div>
					<div class="progress-cont">
						<div class="track-progress"></div>
					</div>
					<div class="track"></div>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tbl-scale">
						<tbody>
							<tr>
								<td>10</td>
								<td>20</td>
								<td>30</td>
								<td>40</td>
								<td>50</td>
								<td>60</td>
								<td>70</td>
								<td>80</td>
								<td>90</td>
								<td>100</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!-- Project Progress Thermometer - End -->

			<!--List the project Type from the timesheet-->
			<label class="pull-left">Project Type</label>
			<div class="displaycontent">
				<?php
					if(count($timesheetProjectType)>0) {
						echo $timesheetProjectType['project_type_name'];
					} else {
						echo '-';
					}
				?>
			</div>
			<?php require (theme_url().'/tpl/user_accounts_options.php'); ?>
			
			<!--List the project lead from the timesheet-->
			<label class="pull-left">Project Manager</label>
			<div class="displaycontent">
				<?php
					if(count($timesheetProjectLead)>0) {
						echo $timesheetProjectLead['project_lead'];
					} else {
						echo '-';
					}
				?>
			</div>
			
			<!--List the project assigned members from the timesheet-->
			<h6 class="pull-left" style="width: 115px; padding-top:8px;">Project Team Members</h6>
			<div class="team_list">
				<?php
					if(count($timesheetAssignedUsers)>0) {
						foreach($timesheetAssignedUsers as $project_assignees) {
							echo $project_assignees . "<br />";
						}
					} else {
						echo '-';
					}
				?>
			</div>
			
			<div class="clear-both"></div>

  <div id="project-tabs" style="width:930px;">
	<div>
		<ul id="job-view-tabs">
			<li><a href="<?php echo current_url() ?>#jv-tab-0">Metrics</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-1">Payment Milestones</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-2">Document</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-3">Files</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-4">Tasks</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-4-5">Milestones</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-5">Customer</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-7">URLs</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-8">Timesheet</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-9">Job History</a></li>
		</ul>
	</div>
	<div id="jv-tab-0">
		<div style="overflow: auto;">
		<div class="pull-left">
			<table id="project-date-assign" class="data-table1" cellpadding="0" cellspacing="0">
				<tr>
					<th>Project Dates</th>
					<th>Planned</th>
					<th>Actual</th>
				</tr>
				<tr>					
					<td><strong>Start Date</strong></td>
					<td>
						<input type="text" data-calendar="true" value="<?php if ($quote_data['date_start'] != '') echo date('d-m-Y', strtotime($quote_data['date_start'])); else echo ''; ?>" <?php if($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?> class="textfield pick-date width100px" <?php } else { ?> class="textfield width60px" <?php } ?> id="project-start-date" readonly />
						<?php if($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?>
							<div class="pjt-btn">
								<button type="submit" class="positive" onclick="setProjectStatusDate('start'); return false;">Set</button>
								<button type="submit" class="negative" onclick="rmProjectStatusDate('start'); return false;">Remove</button>
							</div>
						<?php } ?>
					</td>
					<td>
						<input type="text" data-calendar="true" value="<?php if ($quote_data['actual_date_start'] != '') echo date('d-m-Y', strtotime($quote_data['actual_date_start'])); else echo ''; ?>" <?php if($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?> class="textfield pick-date width100px" <?php } else { ?> class="textfield width60px" <?php } ?> id="actual-project-start-date" readonly />
						<?php if($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?>
							<div class="buttons">
								<button type="submit" class="positive" onclick="actualSetProjectStatusDate('start'); return false;">Set</button>
								<button type="submit" class="negative" onclick="rmProjectStatusDate('act-start'); return false;">Remove</button>
							</div>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td><strong>End Date</strong></td>
					<td>
						<input type="text" data-calendar="true" value="<?php if ($quote_data['date_due'] != '') echo date('d-m-Y', strtotime($quote_data['date_due'])); else echo ''; ?>" <?php if($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?> class="textfield pick-date width100px" <?php } else { ?> class="textfield width60px" <?php } ?> id="project-due-date" readonly />
						<?php if($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?>
						<div class="buttons">
							<button type="submit" class="positive buttons" onclick="setProjectStatusDate('due'); return false;">Set</button>
							<button type="submit" class="negative buttons" onclick="rmProjectStatusDate('due'); return false;">Remove</button>
						</div>
						<?php } ?>
					</td>
					<td>
						<input type="text" data-calendar="true" value="<?php if ($quote_data['actual_date_due'] != '') echo date('d-m-Y', strtotime($quote_data['actual_date_due'])); else echo ''; ?>" <?php if($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?> class="textfield pick-date width100px" <?php } else { ?> class="textfield width60px" <?php } ?> id="actual-project-due-date" readonly />
						<?php if($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?>
							<div class="buttons">
								<button type="submit" class="positive" onclick="actualSetProjectStatusDate('due'); return false;">Set</button>
								<button type="submit" class="negative" onclick="rmProjectStatusDate('act-due'); return false;">Remove</button>
							</div>
						<?php } ?>
					</td>
				</tr>
			</table>
			<div id="dates_errmsg" style="color:red; margin:5px"></div>
			</div>
		<div class="pull-left">
			<table id="project-efforts" class="data-table1" cellpadding="0" cellspacing="0">
				<tr>
					<th></th>
					<th>Budgeted</th>
					<th>Actual</th>
					<?php if($quote_data['billing_type'] != 2) { ?>
					<th>Variance</th>
					<?php } ?>
				</tr>
				<tr>					
					<td><strong>Efforts (Hours)</strong></td>
					<td>
						<input type="text" value="<?php if ($quote_data['estimate_hour'] != '') echo $quote_data['estimate_hour']; else echo ''; ?>" class="textfield width60px" id="project-estimate-hour" onkeypress="return isNumberKey(event)" maxlength="10" <?php if($chge_access != 1) { ?> readonly <?php } ?>/>
						<?php if($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?>
							<button type="submit" class="positive" onclick="setProjectEstimateHour(); return false;">Set</button>
						<?php } ?>
					</td>
					<td> 
						<input type="text" id="actualEff" value="<?php echo isset($actual_hour_data) ? sprintf('%0.2f', $actual_hour_data) : ''; ?>" class="textfield width60px" readonly />
					</td>
					<?php if($quote_data['billing_type'] != 2) { ?>
					<td>
						<?php 
							if (isset($actual_hour_data) && $actual_hour_data != '')
								$varianceProjectHour = $actual_hour_data - $quote_data['estimate_hour'];
							else
								$varianceProjectHour = '';
						?>
						<input type="text" id="varianceEff" value="<?php if (isset($varianceProjectHour)) echo sprintf('%0.2f', $varianceProjectHour); else echo ''; ?>" class="textfield width60px" readonly />
					</td>
					<?php } ?>
				</tr>
				<tr>					
					<td><strong>Project Value (<?php if (isset($quote_data['expect_worth_name'])) echo $quote_data['expect_worth_name']; ?>) </strong></td>
					<td>
						<input class="textfield" style="width: 60px;" type="text" name="pjt_value" id="pjt_value" value="<?php if (isset($quote_data['actual_worth_amount'])) echo $quote_data['actual_worth_amount']; ?>" <?php if ($chge_access != 1) { ?>readonly<?php } ?> onkeypress="return isNumberKey(event)" />
						<?php if ($chge_access == 1 && $quote_data['pjt_status'] != 2) { ?>
						<button type="submit" class="positive" onclick="setProjectVal(); return false;">Set</button>
						<?php } ?>
					</td>
					<td>
						<?php $project_cost = (!empty($project_costs)) ? $project_costs : 0; ?>
						<input type="text" id="actualValue" value="<?php echo sprintf('%0.02f', $project_cost); ?>" class="textfield width60px" readonly />
					</td>
					<?php if($quote_data['billing_type'] != 2) { ?>
					<td>
						<?php 
							if (isset($quote_data['actual_worth_amount']))
								$varianceProjectVal = $quote_data['actual_worth_amount'] - $project_cost;
							else
								$varianceProjectVal = '';
						?>
						<input type="text" id="varianceValue" value="<?php if (isset($varianceProjectVal)) echo sprintf('%0.2f', $varianceProjectVal); else echo ''; ?>" class="textfield width60px" readonly />
					</td>
					<?php } ?>
				</tr>
			</table>
			<div id="msg_project_efforts" style="margin:5px;"></div>
		</div>
		</div>
	</div><!--end of jv-tab-0 -->
	
	<div id="jv-tab-1">
				<div class="q-view-main-top">
					
					<div class="payment-buttons clearfix">
						<div class="buttons">
							<a class="payment-profile-button positive" href="#" onclick="">Payment Terms</a>
						</div>
						<div class="buttons">
						<a class="payment-received-button positive" href="#" onclick="">Payment Received</a>
						</div>
					</div>
				<div style="color:red; margin:7px 0 0;" id="rec_paymentfadeout"></div>
				<?php
				if ($quote_data['payment_terms'] == 0 || $quote_data['payment_terms'] == 1)
				{
				?>
					<div class="payment-profile-view" id="payment-profile-view" style="float:left;"><br/>
						<form id="set-payment-terms">
						
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
						<table class="payment-table">
							<tr>
								<td>
									<p>Payment Milestone *<input type="text" name="sp_date_1" id="sp_date_1" class="textfield width200px" /> </p>
									<p>Milestone date *<input type="text" name="sp_date_2" id="sp_date_2" class="textfield width200px pick-date" readonly /> </p>
									<p>Value *<input onkeypress="return isNumberKey(event)" type="text" name="sp_date_3" id="sp_date_3" class="textfield width200px" /> <span style="color:red;">(Numbers only)</span></p>
									<p>Remarks <textarea name="payment_remark" id="payment_remark" class="textfield width200px" ></textarea> </p>
									<?php if ($readonly_status == false) { ?>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectPaymentTerms(); return false;">Add Payment Terms</button>
									</div>
									<?php } ?>
									<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="0" />
									<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
								</td>
							</tr>
						</table>
						</form>
					</div>
					<?php
						$output = '';
						$total_amount_recieved = '';
						$pt_select_box = '';
						$output .= '<div class="payment-terms-mini-view1" style="display:block; float:left; margin-top:5px;">';
					    if(!empty($payment_data))
						{
							$pdi = 1;
							$pt_select_box .= '<option value="0"> &nbsp; </option>';
							
							$output .= '<div align="left" style="background: none repeat scroll 0 0;">
							<h6>Agreed Payment Terms</h6>
							<div class=payment_legend>
							<div class="pull-left"><img src=assets/img/payment-received.jpg><span>Payment Received</span></div>
							<div class="pull-left"><img src=assets/img/payment-pending.jpg><span>Partial Payment</span></div>
							<div class="pull-left"><img src=assets/img/payment-due.jpg ><span>Payment Due</span></div>
							<div class="pull-left"><img src=assets/img/generate_invoice.png><span>Generate Invoice</span></div>
							<div class="pull-left"><img src=assets/img/invoice_raised.png><span>Invoice Raised</span></div>
							</div></div>';
							$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
							$output .= "<thead>";
							$output .= "<tr align='left' >";
							$output .= "<th class='header'>Payment Milestone</th>";
							$output .= "<th class='header'>Milestone Date</th>";
							$output .= "<th class='header'>Amount</th>";
							$output .= "<th class='header'>Status</th>";
							$output .= "<th class='header'>Action</th>";
							$output .= "</tr>";
							$output .= "</thead>";
							foreach ($payment_data as $pd)
							{
								$expected_date = date('d-m-Y', strtotime($pd['expected_date']));
								$payment_amount = number_format($pd['amount'], 2, '.', ',');
								$total_amount_recieved += $pd['amount'];
								$payment_received = '';
								$invoice_stat = '';
								$raised_invoice_stat = '';
								if ($pd['invoice_status'] == 1) {
									$raised_invoice_stat = "<img src='assets/img/invoice_raised.png' alt='Invoice-raised'>";
								}
								if ($pd['received'] == 0) {
									$payment_received = $raised_invoice_stat.'&nbsp;<img src="assets/img/payment-due.jpg" alt="Due" />';
								} else if ($pd['received'] == 1) {
									$payment_received = '<img src="assets/img/payment-received.jpg" alt="received" />';
								} else {
									$payment_received = $raised_invoice_stat.'&nbsp;<img src="assets/img/payment-pending.jpg" alt="pending" />';
								}
								if ($readonly_status == false) {
									if ($pd['invoice_status'] == 0) {
										$invoice_stat = "<a title='Generate Invoice' href='javascript:void(0)' onclick='generate_inv(".$pd['expectid']."); return false;'><img src='assets/img/generate_invoice.png' alt='Generate Invoice' ></a>";
									} else if ($pd['invoice_status'] == 1) {
										$invoice_stat = "<a title='Generate Invoice' href='javascript:void(0)' class='readonly-status img-opacity'><img src='assets/img/generate_invoice.png' alt='Generate Invoice'></a>";
									}
								} else {
									$invoice_stat = "<a title='Generate Invoice' href='javascript:void(0)' class='readonly-status img-opacity'><img src='assets/img/generate_invoice.png' alt='Generate Invoice'></a>";
								}
								$output .= "<tr>";
								$output .= "<td align='left'>".$pd['project_milestone_name']."</td>";
								$output .= "<td align='left'>".date('d-m-Y', strtotime($pd['expected_date']))."</td>";
								$output .= "<td align='left'> ".$pd['expect_worth_name'].' '.number_format($pd['amount'], 2, '.', ',')."</td>";
								// $output .= "<td align='center'>".$payment_received."</td>";
								$output .= "<td align='center'>".$payment_received."</td>";
								if ($readonly_status == false) {
									$output .= "<td align='left'>
										<a title='Edit' onclick='paymentProfileEdit(".$pd['expectid']."); return false;' ><img src='assets/img/edit.png' alt='edit'></a>
										<a title='Delete' href='javascript:void(0)' onclick='paymentProfileDelete(".$pd['expectid']."); return false;'><img src='assets/img/trash.png' alt='delete' ></a>
										".$invoice_stat."
									</td>";
								} else {
									$output .= "<td align='left'>
										<a title='Edit' class='readonly-status img-opacity' href='javascript:void(0)'><img src='assets/img/edit.png' alt='edit'></a>
										<a title='Delete' class='readonly-status img-opacity' href='javascript:void(0)'><img src='assets/img/trash.png' alt='delete'></a>
										".$invoice_stat."
									</td>";
								}
								$output .= "</tr>";
								$pt_select_box .= '<option value="'. $pd['expectid'] .'">' . $pd['project_milestone_name'] ." \${$payment_amount} by {$expected_date}" . '</option>';
								$pdi ++;
							}
							$output .= "<tr>";
							$output .= "<td></td>";
							$output .= "<td colspan='0'><b>Total Milestone Payment :</b></td><td><b>".$pd['expect_worth_name'].' '.number_format($total_amount_recieved, 2, '.', ',') ."</b></td>";
							$output .= "</tr>";
							$output .= "</table>";
						}
						$output .= '</div>';
					    echo $output;
						?>
						<!--payment received starts here -->

						<div class="payment-recieved-view" id="payment-recieved-view" style="display:none;float:left;"><br/>
						<form id="payment-recieved-terms">
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
							<p>Invoice No *<input type="text" name="pr_date_1" id="pr_date_1" class="textfield width200px" /> </p>
							<p>Amount Received *<input type="text" name="pr_date_2" onkeypress="return isNumberKey(event)" id="pr_date_2" class="textfield width200px" /><span style="color:red;">(Numbers only)</span></p>
							<p>Date Received *<input type="text" name="pr_date_3" id="pr_date_3" class="textfield width200px pick-date" readonly /> </p>
							
							<?php if (isset($pt_select_box)) { ?>
								<p>Map to a payment term *<select name="deposit_map_field" class="deposit_map_field" style="width:210px;"><?php echo $pt_select_box; ?></select></p>
							<?php } else { ?>
								<p>Map to a payment term *<select name="deposit_map_field" class="deposit_map_field" style="width:210px;"><?php echo $pt_select_box; ?></select></p>
							<?php } ?>
							
							<p>Comments <textarea name="pr_date_4" id="pr_date_4" class="textfield width200px" ></textarea> </p>
							<?php if ($readonly_status == false){ ?>
							<div class="buttons">
								<button type="submit" class="positive" onclick="setPaymentRecievedTerms(); return false;">Add Payment</button>
							</div>
							<?php } ?>
							<input type="hidden" name="pr_form_jobid" id="pr_form_jobid" value="0" />
							<input type="hidden" name="pr_form_invoice_total" id="pr_form_invoice_total" value="0" />
						</form>
					    </div>
						<?php 
		
						$output = '';
						$amount_recieved = '';
						$output .= '<div class="payment-received-mini-view1" style="float:left; display:none; margin-top:5px;">';
						if(!empty($deposits_data))
						{
							$pdi = 1;
							$output .= '<option value="0"> &nbsp; </option>';
							$output .= "<p><h6>Payment History</h6></p>";
							$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
							$output .= "<thead>";
							$output .= "<tr align='left'>";
							$output .= "<th class='header'>Invoice No</th>";
							$output .= "<th class='header'>Date Received</th>";
							$output .= "<th class='header'>Amt Received</th>";
							$output .= "<th class='header'>Payment Term</th>";
							$output .= "<th class='header'>Action</th>";
							$output .= "</tr>";
							$output .= "</thead>";
							foreach ($deposits_data as $dd)
							{
								$expected_date = date('d-m-Y', strtotime($dd['deposit_date']));
								$payment_amount = number_format($dd['amount'], 2, '.', ',');
								$amount_recieved += $dd['amount'];								
								$output .= "<tr align='left'>";
								$output .= "<td>".$dd['invoice_no']."</td>";
								$output .= "<td>".date('d-m-Y', strtotime($dd['deposit_date']))."</td>";
								$output .= "<td> ".$dd['expect_worth_name'].' '.number_format($dd['amount'], 2, '.', ',')."</td>";
								$output .= "<td>".$dd['payment_term']."</td>";
								if ($readonly_status == false) {
								$output .= "<td align='left'><a class='edit' title='edit' onclick='paymentReceivedEdit(".$dd['depositid']."); return false;' ><img src='assets/img/edit.png' alt='edit'></a>";
								$output .= "<a class='edit' title='Delete' onclick='paymentReceivedDelete(".$dd['depositid'].",".$dd['map_term'].");' ><img src='assets/img/trash.png' alt='delete'></a></td>";
								} else {
								$output .= "<td align='left'> - </td>";
								}
								$output .= "</tr>";
							}
							$output .= "<tr>";
							$output .= "<td></td>";
							$output .= "<td><b>Total Payment: </b></td><td colspan='2'><b>".$dd['expect_worth_name'].' '.number_format($amount_recieved, 2, '.', ',')."</b></td>";
							$output .= "</tr>";
							$output .= "</table>";
						}
						$output .= "</div>";
						echo $output;
						?>
					<!--payment received ends here -->
				<?php
				}
				?>
	
		</div><!-- class:q-view-main-top end -->
	</div><!-- id: jv-tab-1 end -->
	
	<div id="jv-tab-2"> 
		<div class="q-container">
			<div class="q-details">
				<div class="q-quote-items">
					<h4 class="quote-title">Project Name : <?php echo (isset($quote_data)) ? $quote_data['lead_title'] : '' ?></h4>
					<ul id="q-sort-items"></ul>
				</div>
			</div>
		</div>
		<div class="q-sub-total<?php if ( ! $sensitive_information_allowed) echo ' display-none' ?>">
			<table class="width565px" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="160">Sale Amount <span id="sale_amount"></span></td>
					<td width="120" align="right">GST <span id="gst_amount"></span></td>
					<td width="20">&nbsp;</td>
					<td align="right">Total inc GST <span id="total_inc_gst"></span></td>
				</tr>
			</table>
		</div>

		<div class="q-sub-total<?php if ( ! $sensitive_information_allowed) echo ' display-none' ?>">
			<table class="width565px" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="160">&nbsp;</td>
					<td width="120" align="right">Deposits <span id="deposit_amount"></span></td>
					<td width="20">&nbsp;</td>
					<td align="right">Balance Due <span id="balance_amount"></span></td>
				</tr>
			</table>
		</div>

	</div><!-- id: jv-tab-2 end -->
			
	<div id="jv-tab-3">
		<form name="ajax_file_upload">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<div id="upload-container">
				<img src="assets/img/select_file.jpg" alt="Browse" id="upload-decoy" />
				<input type="file" class="textfield" id="ajax_file_uploader" name="ajax_file_uploader" onchange="return runAjaxFileUpload();" size="1" />
			</div>
			<ul id="job-file-list">
			<?php echo $job_files_html ?>
			</ul>
		</form>
		
	</div><!-- id: jv-tab-3 end -->
			
	<div id="jv-tab-4">
		<form id="set-job-task" onsubmit="return false;">
		
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h3>Tasks</h3>
			<table border="0" cellpadding="0" cellspacing="0" class="task-add  toggler">
				
				<tr>
					<td colspan="4">
						<strong>All fields are required!</strong>
					</td>
				</tr>
				
				<tr>
					<td valign="top">
						<br /><br />Task
					</td>
					<td colspan="3">
						<strong><span id="task-desc-countdown">240</span></strong> characters left.<br />
						<textarea name="job_task" id="job-task-desc" class="width420px"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						Allocate to
					</td>
					<td>
						<select name="task_user" class="textfield width100px">
						<?php
						//echo $remind_options, $remind_options_all, $contractor_options;
						echo $remind_options, $remind_options_all;
						?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td>
						Planned Start Date
					</td>
					<td>
						<input type="text" name="task_start_date" class="textfield pick-date width100px" />
					</td>
					<td>
						Planned End Date
					</td>
					<td>
						<input type="text" name="task_end_date" class="textfield pick-date width100px" />
					</td>
					
				</tr>
				<tr>
					<td>Remarks</td>
					<td colspan="3"><textarea name="remarks" id="task-remarks" class="task-remarks" width="420px"></textarea></td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="buttons">
							<button type="submit" class="positive" onclick="addNewTask('','<?php echo $this->security->get_csrf_token_name()?>','<?php echo $this->security->get_csrf_hash(); ?>');">Add</button>
						</div>
						<div class="buttons">
							<button type="submit" class="negative" onclick="$('.toggler').slideToggle();">Cancel</button>
						</div>
					</td>
				</tr>
			</table>
			<div class="buttons task-init  toggler">
				<button type="button" class="positive" onclick="$('.toggler').slideToggle();">Add New</button>
			</div>
			
			<div class="existing-task-list">
				<br /><br />
				<h4>Existing Tasks</h4>
			</div>
		</form>
		
		<form id="edit-job-task" onsubmit="return false;">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		<!-- edit task -->
			<table border="0" cellpadding="0" cellspacing="0" class="task-add task-edit">
				
				<tr>
					<td colspan="4">
						<strong>All fields are required!</strong>
					</td>
				</tr>
				
				<tr>
					<td valign="top" width="80">
						<br /><br />Task
					</td>
					<td colspan="3">
						<strong><span id="edit-task-desc-countdown">240</span></strong> characters left.<br />
						<textarea name="job_task" class="edit-job-task-desc width420px"></textarea>
					</td>
				</tr>
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
				
				<tr>
					<td>
						Planned Start Date
					</td>
					<td>
						<input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" />
					</td>
					<td>
						Planned End Date
					</td>
					<td>
						<input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px" />
					</td>
				</tr>
				
				<tr>
					<td>
						Actual Start Date
					</td>
					<td>
						<input type="text" name="edit-actualstart-date" class="edit-actualstart-date textfield pick-date width100px" />
					</td>
					<td>
						Actual End Date
					</td>
					<td>
						<input type="text" name="edit-actualend-date" class="edit-actualend-date textfield pick-date width100px" />
					</td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td colspan="3"><textarea name="remarks" id="edit-task-remarks" class="edit-task-remarks" width="420px"></textarea></td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="buttons">
							<button type="submit" class="positive" onclick="editTask();">Edit</button>
						</div>
						<div class="buttons">
							<button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button>
						</div>
					</td>
				</tr>
			</table>
		<!-- edit task end -->
		</form>
		
	</div><!-- id: jv-tab-4 end -->

	<div id="jv-tab-4-5">
		<div id="milestone-top-view">
		<h3>Milestones</h3>
			<div style="color:red; margin:7px 0 0;" id="msErrNotifyFadeout"></div>
			<div id="milestone-add-view">
				<form id="milestone-management" onsubmit="return false;">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<table class="milestone-table ms-toggler" style="display:none;">
						<tr>
							<td>
								<?php $jobid = isset($quote_data['lead_id']) ? $quote_data['lead_id'] : 0; ?>
								<input type="hidden" name="jobid_fk" id="jobid_fk" value=<?php echo $jobid; ?> />
								<p>
									Milestone name *
									<input type="text" name="milestone_name" id="milestone_name" class="textfield" style="width:235px;" />
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p style="float: left;">
									Planned Start Date * 
									<input type="text" name="ms_plan_st_date" id="ms_plan_st_date" autocomplete="off" class="textfield width60px pick-date" readonly />
								</p>
								<p style="float: left; margin: 0px 10px;">
									Planned End Date *
									<input type="text" name="ms_plan_end_date" id="ms_plan_end_date" autocomplete="off" class="textfield width60px pick-date" readonly />
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p style="float: left;">
									Actual Start Date
									<input type="text" name="ms_act_st_date" id="ms_act_st_date" autocomplete="off" class="textfield width60px pick-date" readonly />
								</p>
								<p style="float: left; margin: 0px 10px;">
									Actual End Date
									<input type="text" name="ms_act_end_date" id="ms_act_end_date" autocomplete="off" class="textfield width60px pick-date" readonly />
								</p>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<p>
									Efforts * (Numbers)
									<input onkeypress="return isNumberKey(event)" type="text" name="ms_effort" id="ms_effort" class="textfield width60px" maxlength="5" />
								</p>
							</td>
						</tr>
						<tr>
							<td>
							<p style="float: left;">
								Percentage of Completion
								<select name="ms_percent" id="ms_percent" class="textfield width60px">
									<?php
										foreach($this->cfg['milestones_complete_status'] as $complete_key=>$complete_val) {
											?>
												<option value="<?php echo $complete_key; ?>"><?php echo $complete_val; ?></option>
											<?php
										}
									?>
								</select>
							</p>
							<p style="float: left; margin: 0px 15px;">
								Status
								<select name="milestone_status" class="textfield width100px">
									<?php
									foreach($this->cfg['milestones_status'] as $status_key=>$status_val) {
										?>
											<option value="<?php echo $status_key; ?>"><?php echo $status_val; ?></option>
										<?php
									}
									?>
								</select>
							</p>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<p>
								<div class="buttons">
									<button type="submit" class="positive" onclick="addMilestoneTerms(); return false;">Add</button>
								</div>
								<div class="buttons">
									<button type="submit" onclick="$('.ms-toggler').slideToggle();">Cancel</button>
								</div>
								</p>
							</td>
						</tr>						
					</table>
				</form>
			</div>
			<?php if ($readonly_status == false) { ?>
			<div class="buttons task-init ms-toggler" id="addNew-ms">
				<button type="button" class="positive" onclick="$('.ms-toggler').slideToggle();" style="float:none;">Add New Milestone</button>
			</div>
			<?php } ?>
			<p></p>
			<div style="position: relative; z-index: 0;">
				<a id="milestone-email" class="export-btn" name="msE-mail" style="color:#fff !important;">E-Mail Milestone</a>
				<a id="milestone-export" class="export-btn" name="msExport" style="color:#fff !important;">Export Timeline</a>
			</div>
			<?php
			$output .= '<div class="milestone_view_det" id="milestone_view_det" style="display:block; margin-top:5px;">';
			if(!empty($milestone_data))
			{
				$output .= "<table width='100%' class='payment_tbl'><tr><td colspan='3'><h6>Milestone Terms</h6></td></tr></table>";
				$output .= "<table class='data-table' id='milestone-data' cellspacing = '0' cellpadding = '0' border = '0'>";
				$output .= "<thead>";
				$output .= "<tr align='left'>";
				$output .= "<th class='header'>Milestone Name</th>";
				$output .= "<th class='header'>Planned Start Date</th>";
				$output .= "<th class='header'>Planned End Date</th>";
				$output .= "<th class='header'>Actual Start Date</th>";
				$output .= "<th class='header'>Actual End Date</th>";
				$output .= "<th class='header'>Efforts</th>";
				$output .= "<th class='header'>Completion(%)</th>";
				$output .= "<th class='header'>Status</th>";
				$output .= "<th class='header'>Action</th>";
				$output .= "</tr>";
				$output .= "</thead>";
				foreach ($milestone_data as $ms_data)
				{
					switch($ms_data['milestone_status']){
						case 0:
						$ms_stat = 'Scheduled';
						break;
						case 1:
						$ms_stat = 'In Progress';
						break;
						case 2:
						$ms_stat = 'Completed';
						break;
					}
					$ms_act_st = ($ms_data['ms_act_st_date'] != '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($ms_data['ms_act_st_date'])) : '';
					$ms_act_end = ($ms_data['ms_act_end_date'] != '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($ms_data['ms_act_end_date'])) : '';
					$expected_date = date('d-m-Y', strtotime($pd['expected_date']));
					$output .= "<tr>";
					$output .= "<td align='left'>".$ms_data['milestone_name']."</td>";
					$output .= "<td align='left'>".date('d-m-Y', strtotime($ms_data['ms_plan_st_date']))."</td>";
					$output .= "<td align='left'>".date('d-m-Y', strtotime($ms_data['ms_plan_end_date']))."</td>";
					$output .= "<td align='left'>".$ms_act_st."</td>";
					$output .= "<td align='left'>".$ms_act_end."</td>";
					$output .= "<td align='left'>".$ms_data['ms_effort']."</td>";
					$output .= "<td align='left'>".$ms_data['ms_percent']."</td>";
					$output .= "<td align='left'>".$ms_stat."</td>";
					$output .= "<td align='left'>";
					if ($readonly_status == false) {
						$output .= "<a class='edit' title='Edit' onclick='milestoneEditTerm(".$ms_data['milestoneid']."); return false;' ><img src='assets/img/edit.png' alt='edit'></a>";
						$output .= "<a class='edit' title='Delete' onclick='milestoneDeleteTerm(".$ms_data['milestoneid'].");' ><img src='assets/img/trash.png' alt='delete'></a>";
					} else {
						$output .= "-";
					}
					$output .= "</td>";
					$output .= "</tr>";
				}
				$output .= "</table>";
			}
			$output .= '</div>';
			echo $output;
			?>
		</div> <!--end of milestone-top-view-->
	</div><!-- id: jv-tab-4-5 end -->

	<div id="jv-tab-5">
		<form id="customer-detail-read-only" onsubmit="return false;">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		<table class="tabbed-cust-layout" cellspacing="0" cellpadding="0">
			<tr>
				<td width="120"><label><b>First Name:</b></label></td>
				<td><b><?php echo $quote_data['first_name'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Last Name:</b></label></td>
				<td><b><?php echo $quote_data['last_name'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Position:</b></label></td>
				<td><b><?php echo $quote_data['position_title'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Company:</b></label></td>
				<td><b><?php echo $quote_data['company'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Address Line 1:</b></label></td>
				<td><b><?php echo $quote_data['add1_line1'] ?></b></td>
			</tr>
				
			<tr>
				<td><label><b>Address Line 2:</b></label></td>
				<td><b><?php echo $quote_data['add1_line2'] ?></b></td>
			</tr>
				
			<tr>
				<td><label><b>Suburb:</b></label></td>
				<td><b><?php echo $quote_data['add1_suburb'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Region:</b></label></td>
				<td><b><?php echo $quote_data['region_name'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Country:</b></label></td>
				<td><b><?php echo $quote_data['country_name'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>State:</b></label></td>
				<td><b><?php echo $quote_data['state_name'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Location:</b></label></td>
				<td><b><?php echo $quote_data['location_name'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Post code:</b></label></td>
				<td><b><?php echo $quote_data['add1_postcode'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Direct Phone:</b></label></td>
				<td><b><?php echo $quote_data['phone_1'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Work Phone:</b></label></td>
				<td><b><?php echo $quote_data['phone_2'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Mobile Phone:</b></label></td>
				<td><b><?php echo $quote_data['phone_3'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Fax Line:</b></label></td>
				<td><b><?php echo $quote_data['phone_4'] ?></b></td>
			</tr>

			<tr>
				<td><label><b>Email:</b></label></td>
				<td><b><?php echo $quote_data['email_1'] ?></b></td>
			</tr>

			<tr>
				<td><label><b>Secondary Email:</b></label></td>
				<td><b><?php echo $quote_data['email_2'] ?></b></td>
			</tr>

			<tr>
				<td><label><b>Email 3:</b></label></td>
				<td><b><?php echo $quote_data['email_3'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Email 4:</b></label></td>
				<td><b><?php echo $quote_data['email_4'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Web:</b></label></td>
				<td><b><?php echo $quote_data['www_1'] ?></b></td>
			</tr>
			
			<tr>
				<td><label><b>Secondary Web:</b></label></td>
				<td><b><?php echo $quote_data['www_2'] ?></b></td>
			</tr>
		</table>
		</form>
	</div><!-- id: jv-tab-5 end -->
			
	<div id="jv-tab-7">
		<form id="set-urls" style="overflow:hidden; margin-bottom:15px; zoom:1;">
		
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<p>Add URL to this job (full URL including http://)</p>
			<p><input type="text" class="textfield" id="job-add-url" style="margin:0; width:250px;" /></p>
			<p>Details (optional)</p>
			<p><textarea id="job-url-content" class="textfield" style="margin:0; width:250px;"></textarea></p>
			<div class="buttons">
				<button type="submit" class="positive" onclick="addURLtoJob(); return false;">Add</button>
			</div>
		</form>
		<ul id="job-url-list">
			<?php echo $job_urls_html ?>
		</ul>
	</div><!-- id: jv-tab-7 end -->
	<?php 
		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$cur_year = date('Y');
		$end_year = date('Y', strtotime('-15 year'));
	?>
	<div id="jv-tab-8">
		<div class="wrap_timesheet">
				<?php if($quote_data['billing_type'] == 2) { ?>
					<div id="filter_metrics_data" align="right" style="margin:0 0 10px">
						<form name="filter_metrics" id="filter_metrics"  method="post">
							<label><strong>Month & Year</strong></label>
							<select name="metrics_month" id="metrics_month">
							<?php foreach ($months as $name) { ?>
								<option value="<?php echo $name; ?>" <?php if($name == date('M')) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
							<?php } ?>
							</select>
							<select name="metrics_year" id="metrics_year">
							<?php for($yr=$cur_year; $yr>=$end_year; $yr--) { ?>
								<option value="<?php echo $yr; ?>"><?php echo $yr; ?></option>
							<?php } ?>
							</select>
							<input type="hidden" name="expect_worth_name" id="expect_worth_name" value="<?php echo $quote_data['expect_worth_name']; ?>" readonly="readonly" />
							<input id="metrics_data" class="positive" type="submit" value="Search"/>
							<span style="vertical-align: top;">
								<img src='<?php echo base_url().'assets/images/loading.gif'; ?>' id='load' style='display:none; width: 60px;' />
							</span>
						</form>
					</div>
				<?php } ?>
				<div class="inner_timesheet">
				<?php if(count($timesheet_data) >0 ) { ?>
			    <table class="head_timesheet data-table">
			        <tr>
			            <th>Resource</th>
			            <th>Month & Year</th>
			            <th>Billable Hours</th>
			            <th>Internal Hours</th>
			            <th>Non-Billable Hours</th>
			            <th>Cost Per Hour(<?php echo $quote_data['expect_worth_name']; ?>)</th>
			            <th>Cost(<?php echo $quote_data['expect_worth_name']; ?>)</th>
			        </tr>
			    </table>
				<table class="data-table">
					<?php
					$total_billable_hrs		= 0;
					$total_non_billable_hrs = 0;
					$total_internal_hrs		= 0;
					$total_cost				= 0;
					foreach($timesheet_data as $key1=>$value1) {
						$resource_name = $key1;
						foreach($value1 as $key2=>$value2) {
							$year = $key2;
							foreach($value2 as $key3=>$value3) {
								$month		 	  = $key3;
								$billable_hrs	  = 0;
								$non_billable_hrs = 0;
								$internal_hrs	  = 0;
								foreach($value3 as $key4=>$value4) {
									switch($key4) {
										case 'Billable':
											$rs_name			 = $value4['rs_name'];
											$rate				 = $value4['rateperhr'];
											$billable_hrs		 = $value4['duration'];
											$total_billable_hrs += $billable_hrs;
										break;
										case 'Non-Billable':
											$rs_name				 = $value4['rs_name'];
											$rate					 = $value4['rateperhr'];
											$non_billable_hrs		 = $value4['duration'];
											$total_non_billable_hrs += $non_billable_hrs;
										break;
										case 'Internal':
											$rs_name			 = $value4['rs_name'];
											$rate				 = $value4['rateperhr'];
											$internal_hrs 		 = $value4['duration'];
											$total_internal_hrs += $internal_hrs;
										break;
									}
								}
								echo "<tr>
									<td>".$rs_name."</td>
									<td>".substr($month, 0, 3). " " . $year."</td>
									<td align=right>".sprintf('%0.2f', $billable_hrs)."</td>
									<td align=right>".sprintf('%0.2f', $internal_hrs)."</td>
									<td align=right>".sprintf('%0.2f', $non_billable_hrs)."</td>
									<td align=right>".$rate."</td>
									<td align=right>".sprintf('%0.2f', $rate*($billable_hrs+$internal_hrs+$non_billable_hrs))."</td>
								</tr>";
								
								$total_cost += $rate*($billable_hrs+$internal_hrs+$non_billable_hrs);
							}
						}
					}
					echo "<tr>
						<td align=right><b>Total</b></td>
						<td></td>
						<td align=right><b>".sprintf('%0.2f', $total_billable_hrs)."</b></td>
						<td align=right><b>".sprintf('%0.2f', $total_internal_hrs)."</b></td>
						<td align=right><b>".sprintf('%0.2f', $total_non_billable_hrs)."</b></td>
						<td></td>
						<td align=right><b>".sprintf('%0.2f', $total_cost)."</b></td>
					</tr>";
					?>
				</table>
		    <?php 
				} else {
					if($quote_data['billing_type'] == 2) {
						echo '<div align="center" style="margin: 20px 0 0;"><b> No data available for Current Month</b></div>';
					} else {
						echo '<div align="center" style="margin: 20px 0 0;"><b> Unable to extract project hours from timesheet system </b></div>';
					}
				}
			?>
			</div>
		</div>
	</div><!-- id: jv-tab-8 end -->
	
	<div id="jv-tab-9">
		<span style="float:right;" class="job_history"> 
				<a href="#" onclick="fullScreenLogs(); return false;">View Full Screen</a>
				|
				<a href="#" onclick="$('.log > :not(.stickie), #pager').toggle(); return false;">View/Hide Stickies</a>
				<?php 
				if (isset($userdata) && $userdata['level']==1 && $userdata['role_id']==1)
				{
				?>
				|
				<a href="#" onclick="qcOKlog(); return false;">All Logs OK?</a>
				<?php 
				}
				?>
		</span>
		<h4>Job History</h4>
		<table width="100%" id="lead_log_list" class="log-container logstbl"> 
			<thead> 
				<tr> 
					<th>&nbsp;</th> 
				</tr> 
			</thead>
			<tbody>
			<?php 
				echo $log_html;
			?>				
			</tbody> 
		</table>
	</div><!-- id: jv-tab-9 end -->
	
  </div>
</div>
</div><!--end of project-tabs-->
</div>
<style>
.hide-calendar .ui-datepicker-calendar {
   display: none;
}
</style>
<?php require (theme_url().'/tpl/footer.php'); ?>