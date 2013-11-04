<?php require (theme_url().'/tpl/header.php'); ?>

<!--script type="text/javascript" src="assets/js/blockui.v2.js"></script-->
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript" src="assets/js/tablesort.pager.js"></script>
<script type="text/javascript">var this_is_home = true;</script>

<!--Code Added for the Pagination in Comments Section -- Starts Here-->
<script type="text/javascript">

  var project_jobid           = "<?php echo isset($quote_data['jobid']) ? $quote_data['jobid'] : 0 ?>";
  var project_edit_quotation  = "<?php echo $edit_quotation; ?>";
  var project_view_quotation  = "<?php echo $view_quotation; ?>";
  var project_user_id         = "<?php echo isset($userdata['userid']) ? $userdata['userid'] : 0 ?>";
  var project_job_title		  = "<?php echo str_replace("'", "\'", $quote_data['job_title']) ?>";
  var project_job_status      = "<?php echo (isset($quote_data['job_status'])) ? $quote_data['job_status'] : 0 ?>";
  var project_request_url     = "http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>";
  var project_assigned_to     = "<?php echo $quote_data['assigned_to']; ?>";
  var project_userdata    	  = "<?php echo $userdata; ?>";
  var project_complete_status = "<?php echo $quote_data['complete_status']; ?>";
  var proj_location			  = 'http://<?php echo $_SERVER['HTTP_HOST'], preg_replace('/[0-9]+/', '{{jobid}}', $_SERVER['REQUEST_URI']) ?>';			
  
</script>
<script type="text/javascript" src="assets/js/projects/welcome_view_project.js"></script>
<div class="comments-log-container" style= "display:none;">
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

<!--Code Added for the Pagination in Comments Section--Ends Here-->

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
					<table border="0" cellpadding="0" cellspacing="0" class="client-comm-options">
						<tr>
							<td rowspan="2" class="action-td" valign="top" align="right"><a href="#" onclick="addClientCommOptions(); $(this).blur(); return false;">Communicate<br />to Client via</td>
							<td><input type="checkbox" name="client_comm_phone" value="<?php echo (isset($quote_data['phone_1'])) ? $quote_data['phone_1'] : '' ?>"> <span>Phone</span></td>
							<td><input type="checkbox" name="client_comm_sms" value="<?php echo (isset($quote_data['phone_3'])) ? $quote_data['phone_3'] : '' ?>"> <span>SMS</span></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="client_comm_mobile" value="<?php echo (isset($quote_data['phone_3'])) ? $quote_data['phone_3'] : '' ?>"> <span>Mobile</span></td>
							<td><input type="checkbox" name="client_comm_email" value="<?php echo (isset($quote_data['email_1'])) ? $quote_data['email_1'] : '' ?>"> <span>Email</span></td>
						</tr>
					</table>

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
			
				<div class="user-addresses">
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
									'<input type="checkbox" name="email-log-' . $user_accounts[$i]['userid'] . '" id="email-log-' . $user_accounts[$i]['userid'] . '" class="' . $is_pm . '" /> <label for="email-log-' . $user_accounts[$i]['userid'] . '">' . $user_accounts[$i]['first_name'] . ' ' . $user_accounts[$i]['last_name'] . '</label>' .
									'<select name="post_profile_' . $user_accounts[$i]['userid'] . '" class="post-profile-select">' . $post_profile_options . '</select></span>'; 
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
			<span style="float:right;"> 
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

			<!--Code Changes for Pagination in Comments Section -- Starts here -->
			<?php if ($log_html != "") { ?>
			<table width="100%" id="lead_log_list" class="log-container"> 
				<thead> 
					<tr> 
						<th></th> 
					</tr> 
				</thead>
				<tbody>
				<?php 
					echo $log_html;
				?>				
				</tbody> 
			</table>
			<div id="pager">
				<a class="first"> First </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
				<a class="prev"> &laquo; Prev </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
				<input type="text" size="2" class="pagedisplay"/><?php echo '&nbsp;&nbsp;&nbsp;'; ?> <!-- this can be any element, including an input --> 
				<a class="next"> Next &raquo; </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
				<a class="last"> Last </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
				<span>No. of Records per page:<?php echo '&nbsp;'; ?> </span>
				<select class="pagesize"> 
					<option selected="selected" value="10">10</option> 
					<option value="20">20</option> 
					<option value="30">30</option> 
					<option value="40">40</option> 
				</select> 
			</div>
			<?php }	else {
				echo "No Comments Found."; 
				}
			?>
			<!--Code Changes for Pagination in Comments Section -- Ends here -->
		</div>
		
        <div class="pull-left side1"> 
			<h2 class="job-title">
				<?php
					echo htmlentities($quote_data['job_title'], ENT_QUOTES);
				?>
			</h2>
			<?php
				if (isset($quote_data['pjt_id'])) 
				{
					$varPjtId = $quote_data['pjt_id'];
				}
			?>
			<form>
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<div>
					<div style="float:left;">
						<h5><label class="project-id">Project Id</label>&nbsp;&nbsp;
						<input class="textfield" style="width: 156px;" type="text" name="pjtId" id="pjtId" maxlength="15" value="<?php if (isset($varPjtId)) echo $varPjtId; ?>" <?php if ($chge_access != 1) { ?>readonly<?php } ?> />
						<input type="hidden" class="hiddenUrl"/>
						</h5>
					</div>					
					<?php if ($chge_access == 1) { ?>
						<div class="buttons">
							<button type="submit" class="positive" id="submitid" style="margin:0 0 0 5px; width: 118px;" onclick="setProjectId(); return false;">
								Set Project ID
							</button>
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
				<div>
					<div style="float:left;">
						<h5><label class="project-val">Project Value</label>&nbsp;&nbsp;
						<input class="textfield" style="width: 25px; font-weight:bold;" type="text" name="curid" id="curid" value="<?php if (isset($quote_data['expect_worth_name'])) echo $quote_data['expect_worth_name']; ?>" readonly />
						<input class="textfield" style="width: 95px;" type="text" name="pjt_value" id="pjt_value" value="<?php if (isset($quote_data['actual_worth_amount'])) echo $quote_data['actual_worth_amount']; ?>" <?php if ($chge_access != 1) { ?>readonly<?php } ?> onkeypress="return isNumberKey(event)" />
						<input type="hidden" class="hiddenUrl"/>
						</h5>
					</div>					
					<?php if ($chge_access == 1) { ?>
					<div class="buttons">
						<button type="submit" class="positive" id="submitid" style="margin:0 0 0 5px; width: 118px;" onclick="setProjectVal(); return false;">
							Set Project Value
						</button>
					</div>
					<div class="error-msg">
						<span id="pjt_val_errormsg" style="color:red"></span>
						<span id="checkVal" style="color:green">Project Value Updated.</span>
						<span id="checkVal1" id="val-existsval" style="color:red">Project Value Already Exists.</span>
					</div>
					<?php } ?>
				</div>	
			</form>
			<form>
				<div>
					<div style="float:left;">
						<h5><label class="project-val">Project Status</label>&nbsp;&nbsp;
						<select name="pjt_status" id="pjt_status" class="textfield" style="width:138px;">
							<option value="1"  <?php if($quote_data['pjt_status'] == 1) echo 'selected="selected"'; ?>>Project In Progress</option>
							<option value="2"  <?php if($quote_data['pjt_status'] == 2) echo 'selected="selected"'; ?>>Project Completed</option>
							<option value="3"  <?php if($quote_data['pjt_status'] == 3) echo 'selected="selected"'; ?>>Project Onhold</option>
							<option value="4"  <?php if($quote_data['pjt_status'] == 4) echo 'selected="selected"'; ?>>Inactive</option>
                        </select>
						<input type="hidden" class="hiddenUrl"/>
						</h5>
					</div>					
					<?php if ($chge_access == 1) { ?>
					<div class="buttons">
						<button type="submit" class="positive" id="submitid" style="margin:0 0 0 5px; width: 124px;" onclick="setProjectStatus(); return false;">
							Set Project Status
						</button>
						<div id="resmsg" class="error-msg"></div>
					</div>
					<?php } ?>
				</div>	
			</form>
			<div class="action-buttons" style="overflow:hidden;">
				<?php
				require (theme_url().'/tpl/user_accounts_options.php');
				?>
				
				<form name="project_assign" id="project-assign">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table border="0">
						<tr>
							<td valign="top">
								<h5 class="project-lead-label">Project Manager <br /><span class="small">[ 
								<?php if (isset($quote_data['assigned_to']) and is_numeric($quote_data['assigned_to'])) { 
												if(isset($ua_id_name[$quote_data['assigned_to']])) echo $ua_id_name[$quote_data['assigned_to']];
												else echo 'Not Set';
												}
										else echo 'Not Set'; ?> 
                                ]</span>
								</h5>
								<?php
								if ($chge_access == 1) {
								?>
								<p><a href="#" onclick="$('.project-lead-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-lead-change">
									<select name="project_lead" id="project_lead" class="textfield">
										<option value="0">Please Select</option>
										<?php echo $pm_options ?>
									</select>
									<span id="pjt_lead_errormsg" style="color:red; float:left;"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectLead(); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-lead-change:visible, #pjt_lead_errormsg').hide(200); return false;">Cancel</button>
									</div>
									<input type="hidden" value="" id="previous-project-manager"/>
								</div>
								<?php
								}
								?>
							</td>
						</tr>
					</table>
				</form>
				
				<h3 class="status-title">Adjust project status <span class="small">[ current status - <em><strong>0</strong>% Complete</em> ]</span></h3>

				<p class="status-bar">
					<span class="bar"></span>
					<?php if ($chge_access == 1) { ?>
					<span class="over"></span>
					<a href="#" class="p1" rel="1"></a>
					<a href="#" class="p2" rel="2"></a>
					<a href="#" class="p3" rel="3"></a>
					<a href="#" class="p4" rel="4"></a>
					<a href="#" class="p5" rel="5"></a>
					<a href="#" class="p6" rel="6"></a>
					<a href="#" class="p7" rel="7"></a>
					<a href="#" class="p8" rel="8"></a>
					<a href="#" class="p9" rel="9"></a>
					<a href="#" class="p10" rel="10"></a>
					<?php } ?>
				</p>

				<form name="project_dates" id="project-date-assign" style="padding:15px 0 5px 0;">
					
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />				
				
					<table>
						<tr>
							<td valign="top" width="175">
								<h6 class="project-startdate-label">Planned Project Start Date &raquo; <span><?php if ($quote_data['date_start'] != '') echo date('d-m-Y', strtotime($quote_data['date_start'])); else echo 'Not Set'; ?></span></h6>
								<?php if ($chge_access == 1){ ?>
								<p><a href="#" onclick="$('.project-startdate-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-startdate-change">
									<input type="text" value="" class="textfield pick-date" id="project-start-date" />
									<span id="errmsg_start_dt" style="color:red"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectStatusDate('start'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-startdate-change:visible, #errmsg_start_dt').hide(200); return false;">Cancel</button>
									</div>
								</div>
								<?php } ?>
							</td>
							<td valign="top" width="175">
								<h6 class="project-deadline-label">Planned Project End Date &raquo; <span><?php if ($quote_data['date_due'] != '') echo date('d-m-Y', strtotime($quote_data['date_due'])); else echo 'Not Set'; ?></span></h6>
								<?php if ($chge_access == 1) {?>
								<p><a href="#" onclick="$('.project-deadline-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-deadline-change">
									<input type="text" value="" class="textfield pick-date" id="project-due-date" />
									<span id="errmsg" style="color:red"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectStatusDate('due'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-deadline-change:visible , #errmsg').hide(200); return false;">Cancel</button>
									</div>
								</div>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td valign="top" width="175">
								<h6 class="actual-project-startdate-label">Actual Project Start Date &raquo; <span><?php if ($quote_data['actual_date_start'] != '') echo date('d-m-Y', strtotime($quote_data['actual_date_start'])); else echo 'Not Set'; ?></span></h6>
								<?php if ($chge_access == 1) { ?>
								<p><a href="#" onclick="$('.actual-project-startdate-change:hidden').show(200); return false;">Change?</a></p>
								<div class="actual-project-startdate-change">
									<input type="text" value="" class="textfield pick-date" id="actual-project-start-date" />
									<span id="errmsg_actual_start_dt" style="color:red"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="actualSetProjectStatusDate('start'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.actual-project-startdate-change:visible, #errmsg_actual_start_dt').hide(200); return false;">Cancel</button>
									</div>
								</div>
								<?php } ?>
							</td>
							<td valign="top" width="175">
								<h6 class="actual-project-deadline-label">Actual Project End Date &raquo; <span><?php if ($quote_data['actual_date_due'] != '') echo date('d-m-Y', strtotime($quote_data['actual_date_due'])); else echo 'Not Set'; ?></span></h6>
								<?php if ($chge_access == 1) { ?>
								<p><a href="#" onclick="$('.actual-project-deadline-change:hidden').show(200); return false;">Change?</a></p>
								<div class="actual-project-deadline-change">
									<input type="text" value="" class="textfield pick-date" id="actual-project-due-date" />
									<span id="errmsg_actual_end_dt" style="color:red"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="actualSetProjectStatusDate('due'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.actual-project-deadline-change:visible, #errmsg_actual_end_dt').hide(200); return false;">Cancel</button>
									</div>
								</div>
								<?php } ?>
							</td>
						</tr>
					</table>
					
				</form>
				
				<form name="contractor-assign">
				
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
					<h5 class="project-lead-label">Assign Project Team</h5>
					<p><a href="javascript:void(0);" id="show-btn">Show</a></p>
										
					<div id="show-con">
						<?php if ($chge_access == 1) { ?>
							<div class="list-contractors">
								<div style="float:left;">
									<span style="padding-left: 55px;">Members</span><br />
									<select multiple="multiple" id="select1"><?php echo $contractor_list_select1 ?></select>
								</div>
								
								<div style="float:left; padding-top: 29px; padding-left: 10px; padding-right: 10px;">
									<input type="button" id="add" class="add-member" value="&gt;&gt;" /><br />
									<input type="button" id="remove" class="remove-member" value="&lt;&lt;" />
									<input type="hidden" value ="" id="project-member" name="project-member"/>
								</div>
								<div style="float:left;">
									<span style="padding-left: 45px;">Project Team</span><br />
									<select multiple="multiple" name="select2" id="select2" ><?php echo $contractor_list_select2 ?></select>
								</div>
							</div>
						<?php 
						} else { 
						?>
								<span style="padding-left: 45px;">Project Team</span><br />
								<select id="select3" multiple="multiple"><?php echo $contractor_list_select2 ?></select>
						<?php		
						}
						?>
						<?php 
							if ($chge_access == 1) { 
						?>
							<div class="buttons" style="clear:both;">
								<button type="submit" class="positive" id="positiveSelectBox" onclick="setContractorJob(); return false;">Set Project Team</button>
								<div id="errMsgPjtNulMem" class="error-msg" style="display:none; color:#FF4400;">Please assign any project member.</div>
							</div>
						<?php 
						} 
						?>
					</div>
				</form>

			</div>

  <div id="project-tabs">
	<div>
		<ul id="job-view-tabs">
			<li><a href="<?php echo current_url() ?>#jv-tab-1">Payment Milestones</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-2">Document</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-3">Files</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-4">Tasks</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-4-5">Milestones</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-5">Customer</a></li>
			<li><a href="<?php echo current_url() ?>#jv-tab-7">URLs</a></li>
		</ul>
	</div>
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
									<p>Milestone date *<input type="text" name="sp_date_2" id="sp_date_2" class="textfield width200px pick-date" /> </p>
									<p>Value *<input onkeypress="return isNumberKey(event)" type="text" name="sp_date_3" id="sp_date_3" class="textfield width200px" /> <span style="color:red;">(Numbers only)</span></p>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectPaymentTerms(); return false;">Add Payment Terms</button>
									</div>
									<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="0" />
									<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
								</td>
							</tr>
						</table>
						</form>
					</div>
					<?php
						$output = '';
						$output .= '<div class="payment-terms-mini-view1" style="display:block; float:left; margin-top:5px;">';
					    if(!empty($payment_data))
						{
							$pdi = 1;
							$pt_select_box = '';
							$pt_select_box .= '<option value="0"> &nbsp; </option>';
							$output .= "<table width='100%' class='payment_tbl'>
							<tr><td colspan='3'><h6>Agreed Payment Terms</h6></td></tr>
							<tr>
							<td><img src=assets/img/payment-received.jpg height='10' width='10' > Payment Received</td>
							<td><img src=assets/img/payment-pending.jpg height='10' width='10' > Partial Payment</td>
							<td><img src=assets/img/payment-due.jpg height='10' width='10' > Payment Due</td>
							</tr>
							</table>";
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
								if ($pd['received'] == 0)
								{
									$payment_received = '<img src="assets/img/payment-due.jpg" alt="Due" height="10" width="10" />';
								}
								else if ($pd['received'] == 1)
								{
									$payment_received = '<img src="assets/img/payment-received.jpg" alt="received" height="10" width="10" />';
								}
								else
								{
									$payment_received = '<img src="assets/img/payment-pending.jpg" alt="pending" height="10" width="10" />';
								}							
								$output .= "<tr>";
								$output .= "<td align='left'>".$pd['project_milestone_name']."</td>";
								$output .= "<td align='left'>".date('d-m-Y', strtotime($pd['expected_date']))."</td>";
								$output .= "<td align='left'> ".$pd['expect_worth_name'].' '.number_format($pd['amount'], 2, '.', ',')."</td>";
								$output .= "<td align='center'>".$payment_received."</td>";
								$output .= "<td align='left'><a class='edit' onclick='paymentProfileEdit(".$pd['expectid']."); return false;' >Edit</a> | ";
								$output .= "<a class='edit' onclick='paymentProfileDelete(".$pd['expectid'].");' >Delete</a></td>";
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
							<p>Amount Recieved *<input type="text" name="pr_date_2" onkeypress="return isNumberKey(event)" id="pr_date_2" class="textfield width200px" /><span style="color:red;">(Numbers only)</span></p>
							<p>Date Recieved *<input type="text" name="pr_date_3" id="pr_date_3" class="textfield width200px pick-date" /> </p>
							
							<?php if (isset($pt_select_box)) { ?>
							<p>Map to a payment term *<select name="deposit_map_field" class="deposit_map_field" style="width:210px;"><?php echo $pt_select_box; ?></select></p>
							<?php } 
							else { ?>
							  <p>Map to a payment term *<select name="deposit_map_field" class="deposit_map_field" style="width:210px;"><?php echo $pt_select_box; ?></select></p>
							<?php } ?>
							
							<p>Comments <textarea name="pr_date_4" id="pr_date_4" class="textfield width200px" ></textarea> </p>
							<div class="buttons">
								<button type="submit" class="positive" onclick="setPaymentRecievedTerms(); return false;">Add Payment</button>
							</div>
							<input type="hidden" name="pr_form_jobid" id="pr_form_jobid" value="0" />
							<input type="hidden" name="pr_form_invoice_total" id="pr_form_invoice_total" value="0" />
						</form>
					    </div>
						<?php 
		
						$output = '';
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
								$output .= "<td align='left'><a class='edit' onclick='paymentReceivedEdit(".$dd['depositid']."); return false;' >Edit</a> | ";
								$output .= "<a class='edit' onclick='paymentReceivedDelete(".$dd['depositid'].",".$dd['map_term'].");' >Delete</a></td>";
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
				<div class="q-top-head">
					<div class="q-cust">
						<h3 class="q-id"><em>Project</em> &nbsp; <span>#<?php echo  (isset($quote_data)) ? $quote_data['invoice_no'] : '' ?></span></h3>
						<p class="q-date"><em>Date</em> <span><?php echo  (isset($quote_data)) ? date('d-m-Y', strtotime($date_used)) : date('d-m-Y') ?></span></p>
						<p class="q-cust-company"><em>Company</em> <span><?php echo  (isset($quote_data)) ? $quote_data['company'] : '' ?></span></p>
						<p class="q-cust-name"><em>Contact</em> <span><?php echo  (isset($quote_data)) ? $quote_data['first_name'] . ' ' . $quote_data['last_name'] : '' ?></span></p>
						<p class="q-cust-email"><em>Email</em> <span><?php echo  (isset($quote_data)) ? $quote_data['email_1'] : '' ?></span></p>
						<p class="q-service-type"><em>Service</em> <span><?php echo  (isset($quote_data)) ? $quote_data['job_category'] : '' ?></span></p>
					</div>
					
					<p><img src="assets/img/qlogo.jpg?q=1" alt="" /></p>
				</div>
				<div class="q-quote-items">
					<h4 class="quote-title">Project Name : <?php echo (isset($quote_data)) ? $quote_data['job_title'] : '' ?></h4>
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
		<form id="milestone-management" onsubmit="return false;">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h3>Milestones</h3>
			<table id="milestone-clone" style="display:none;">
				<tr>
					<td class="milestone">
						<input type="text" name="milestone[]" class="textfield width250px" />
					</td>
					<td class="milestone-date">
						<input type="text" name="milestone_date[]" class="textfield width80px pick-date" />
					</td>
					<td class="milestone-status">
						<select name="milestone_status[]" class="textfield width80px">
							<option value="0">Scheduled</option>
							<option value="1">In Progress</option>
							<option value="2">Completed</option>
						</select>
					</td>
					<td class="milestone-action" valign="middle">
						&nbsp; <a href="#" onclick="removeMilestoneRow(this); return false;">Remove</a>
					</td>
				</tr>
			</table>
			
			<table id="milestone-data">
				<thead>
					<tr>
						<th align="left">Item</th>
						<th>Date</th>
						<th>Status</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			
			<div class="buttons">
				<button type="submit" class="positive" onclick="addMilestoneField();">Add New</button>
				<button type="submit" class="positive" onclick="saveMilestones();">Save List</button>
				<button type="submit" class="positive" onclick="emailMilestones();">Email Timeline</button>
			</div>
			
		</form>
		
		
	</div>
			
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
  </div>
</div>
</div><!--end of project-tabs-->
</div>

<?php require (theme_url().'/tpl/footer.php'); ?>