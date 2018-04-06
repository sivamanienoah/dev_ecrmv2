<link rel="stylesheet" href="assets/css/chosen.css" type="text/css" />
<script type="text/javascript" src="assets/js/chosen.jquery.js"></script>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
#ui-datepicker-div { z-index: 1082 !important; }
</style>
<?php
	if($customer_data['add1_region'] != 0) 
	echo '<input type="hidden" name="region_update" id="region_update" value="'.$customer_data['add1_region'].'" />';
	if($customer_data['add1_country'] != 0)
	echo '<input type="hidden" name="country_update" id="country_update" value="'.$customer_data['add1_country'].'" />';
	if($customer_data['add1_state'] != 0)
	echo '<input type="hidden" name="state_update" id="state_update" value="'.$customer_data['add1_state'].'" />';
	if($customer_data['add1_location'] != 0)
	echo '<input type="hidden" name="location_update" id="location_update" value="'.$customer_data['add1_location'].'" />';
	$username = $this->session->userdata('logged_in_user');
	//echo '<pre>';print_r($quote_data);exit;?>
<div style="width:100%;">
	<div class="file-tabs-close-confirm-tab"></div>
	<div id="tabs">
		<ul class="tabs-confirm">
			<li><a href="#tabs-client">Client Details</a></li>
			<li><a onclick="update_client('<?php echo $project_id; ?>','tabs-project')" href="#tabs-project">Project Details</a></li>
			<li><a onclick="update_customer_project('<?php echo $project_id; ?>','tabs-assign-users')" href="#tabs-assign-users">Assign Users</a></li>
			<li><a onclick="update_cust_proj_users('<?php echo $project_id;?>','tabs-milestone')" href="#tabs-milestone">Milestone</a></li>			
		</ul>
		<div id="tabs-client">
			<!--p class="clearfix" ><h3>Client Details</h3></p-->
			<form name="customer_detail_form" id="customer_detail_form" method="post" onsubmit="return false;">
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			<input type="hidden" name="companyid" value="<?php echo $customer_data['companyid'] ?>" />
			<?php
				if(!empty($sales_person_detail)) {
					$sales_contact_name      = $sales_person_detail['first_name'].' '.$sales_person_detail['last_name'];
					$sales_contact_userid_fk = $sales_person_detail['userid'];
					$sales_contact_email     = $sales_person_detail['email'];
				} else {
					$sales_contact_name      = $username['first_name'].' '.$username['last_name'];
					$sales_contact_userid_fk = $username['userid'];
					$sales_contact_email     = $username['email'];
				}
			?>
			<input type="hidden" name="sales_contact_userid_fk" value="<?php echo $sales_contact_userid_fk; ?>" class="textfield width200px" readonly />
			<table class="layout">
				<!--tr>
					<td width="100"><strong>First name: <span class='mandatory_asterick'>*</span></strong></td>
					<td width="240"><input type="text" name="first_name" value="<?php #echo $customer_data['first_name']; ?>" class="textfield width200px required" /> </td>
					<td width="100"><strong>Last Name:</strong></td>
					<td width="240"><input type="text" name="last_name" value="<?php #echo $customer_data['last_name']; ?>" class="textfield width200px required" /></td>
				</tr-->
				<tr>
					<!--td><strong>Position:</strong></td>
					<td><input type="text" name="position_title" value="<?php #echo $customer_data['position_title']; ?>" class="textfield width200px required" /></td-->
                    <td><strong>Company: <span class='mandatory_asterick'>*</span></strong></td>
					<td><input type="text" name="company" value="<?php echo $customer_data['company']; ?>" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td><strong>Address Line 1:</strong></td>
					<td><input type="text" name="add1_line1" value="<?php echo $customer_data['add1_line1']; ?>" class="textfield width200px" /></td>
                    <td><strong>Address Line 2:</strong></td>
					<td><input type="text" name="add1_line2" value="<?php echo $customer_data['add1_line2']; ?>" class="textfield width200px" /></td>
				</tr>
				<tr>
					<td><strong>Suburb:</strong></td>
					<td><input type="text" name="add1_suburb" value="<?php echo $customer_data['add1_suburb']; ?>" class="textfield width200px" /></td>
                    <td><strong>Post code:</strong></td>
					<td><input type="text" name="add1_postcode" value="<?php echo $customer_data['add1_postcode']; ?>" class="textfield width200px" /></td>
				</tr>
				<tr>
					<td><strong>Region: <span class='mandatory_asterick'>*</span></strong></td>
					<td>
						<select name="add1_region" id="add1_region" class="textfield width200px" onchange="getCountry(this.value)" class="textfield width200px required">
							<option value="0">Select Region</option>
							<?php
							if(count($regions>0)) {
								foreach ($regions as $region) { 
							?>
									<option value="<?php echo $region['regionid'] ?>"<?php echo ($customer_data['add1_region'] == $region['regionid']) ? ' selected="selected"' : '' ?>><?php echo $region['region_name']; ?></option>
							<?php 
								} 
							} 
							?>
						</select>
					</td>
					<td><strong>Country: <span class='mandatory_asterick'>*</span></strong></td>
					<td id='country_row'>
						<select id="add1_country" name="add1_country" class="textfield width200px required" >
						<option value="0">Select Country</option>                           
						</select>
						<a class="addNew" id="addButton" style ="display:none;"></a>	
					</td>
				</tr>
				<tr>
					<td><strong>State: <span class='mandatory_asterick'>*</span></strong></td>
					<td id='state_row'>
						<select id="add1_state" name="add1_state" class="textfield width200px required">
							<option value="0">Select State</option>                           
						</select>
						<a id="addStButton" class="addNew" style ="display:none;"></a>
					</td>
					<td><strong>Location: <span class='mandatory_asterick'>*</span></strong></td>
					<td id='location_row'>
						<select name="add1_location" class="textfield width200px required">
						<option value="0">Select Location</option>                           
						</select>
						<a id="addLocButton" class="addNew" style ="display:none;"></a>
					</td>
				</tr>
				<tr>
					<td><strong>Work Phone:</strong></td>
					<td><input type="text" name="phone" value="<?php echo $customer_data['phone']; ?>" class="textfield width200px" /></td>
					<td><strong>Fax Line:</strong></td>
					<td><input type="text" name="fax" value="<?php echo $customer_data['fax']; ?>" class="textfield width200px" /></td>
				</tr>
				<tr>
					<td><strong>Email:</strong></td>
					<td>
						<input type="text" name="email_2" id="emailval" autocomplete="off" value="<?php echo $customer_data['email_2']; ?>" class="textfield width200px required" /> 
						<div class="errmsg"></div>
					</td>
					<td><strong>Web:</strong></td>
					<td><input type="text" name="www" value="<?php echo $customer_data['www']; ?>" class="textfield width200px required" /></td>
				</tr>
				<!--tr>
					<td><strong>Sales Contact Name:</strong></td>
					<td>
						<input type="text" name="sales_contact_name" value="<?php #echo $sales_contact_name; ?>" class="textfield width200px" readonly />
					</td>
                    <td><strong>Sales Contact Email:</strong></td>
					<td><input type="text" name="sales_contact_email" value="<?php #echo $sales_contact_email; ?>" class="textfield width200px" readonly /></td>
				</tr-->
				<tr>
					<td colspan='4'>
						<table class="table websiteBrd data-tbl dashboard-heads dataTable" id="document_tbl" >
							<thead>
								<tr class="bg-blue">
									<td>Name <span class='mandatory_asterick'>*</span></td>
									<td>Email ID <span class='mandatory_asterick'>*</span></td>
									<td>Position</td>
									<td>Contact No <span class='mandatory_asterick'>*</span></td>
									<td>Skype</td>
								</tr>
							</thead>
							<tr>
								<td>
									<input type="hidden" name="custid" value="<?php echo $customer_data['custid']; ?>" class="textfield contact_id required" />
									<input type="text" name="customer_name" value="<?php echo $customer_data['customer_name']; ?>" class=" first_name textfield width150px required" />
									<span class="first_name_err_msg text-danger"></span>
								</td>
								<td>
								   <input type="text" name="email_1" value="<?php echo $customer_data['email_1']; ?>" class="textfield email width150px required" />
									<span class="position_title_err_msg text-danger"></span>
								</td>
								<td>
								   <input type="text" name="position_title" value="<?php echo $customer_data['position_title']; ?>" class="position_title textfield width80px required" />
									<span class="position_title_err_msg text-danger"></span>
								</td>
								<td>
								   <input type="text" name="phone_1" value="<?php echo $customer_data['phone_1']; ?>" class="textfield phone width150px required" />
									<span class="position_title_err_msg text-danger"></span>
								</td>
								<td>
								   <input type="text" name="skype_name" value="<?php echo $customer_data['skype_name']; ?>" class="textfield skype width110px required" />
									<span class="skype_err_msg text-danger"></span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
                        <div id="subBtn" class="buttons pull-right" style="padding-right: 30px;">
							<button type="submit" class="positive" id="positiveBtn" onclick="update_customer('<?php echo $customer_data['custid'] ?>','tabs-milestone'); return false;">Update</button>
						</div>
                    </td>
				</tr>
			</table>
			</form>
		</div>
		
		<div id="tabs-project" >
			<?php #echo "<pre>"; print_r($quote_data); exit; ?>
			<!--p class="clearfix" ><h3>Project Details<span class='mandatory_asterick'>*</span></h3></p-->
			<form action="" method="post" id="project-confirm-form" onsubmit="return false;">
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<div class="errmsg_confirm ajx_failure_msg"></div>
				<table class="layout" cellspacing="10">
					<tr>
						<td width="115"><strong>Project Name: <span class='mandatory_asterick'>*</span></strong></td>
						<td width="200">
							<input type="text" name="project_name" id="project_name" class="textfield" style=" width:200px" value="<?php echo  htmlentities($quote_data['lead_title'], ENT_QUOTES) ?>" tabindex="1" />
							<div class="ajx_failure_msg" id="project_name_err"></div>
						</td>
						<td width="115"><strong>Practice: <span class='mandatory_asterick'>*</span></strong></td>
						<td width="200">					
							<select name="practice" id="practice" class="textfield width200px" tabindex="10">
								<option value="not_select">Please Select</option>
								<?php 
								if(isset($practices) && !empty($practices)) {
									foreach ($practices as $practice) 
									{
								?>
									<option value="<?php echo $practice['id'] ?>" <?php echo ($quote_data['practice'] == $practice['id']) ? ' selected="selected"' : '' ?>><?php echo $practice['practices'] ?></option>
								<?php
									}
								}
								?>
							</select>
							<div class="ajx_failure_msg" id="practice_err"></div>							
						</td>
					</tr>
					<tr>
						<td><strong>Resource Type: <span class='mandatory_asterick'>*</span></strong></td>
						<td>
							<select name="resource_type" id="resource_type" class="textfield width200px" tabindex="2">
								<option value="not_select">Please Select</option>
								<?php 
									if(isset($billing_categories) && !empty($billing_categories)) {
										foreach ($billing_categories as $list_billing_cat) 
										{
								?>
									<option value="<?php echo $list_billing_cat['bill_id'] ?>" <?php echo ($quote_data['resource_type'] == $list_billing_cat['bill_id']) ? ' selected="selected"' : '' ?>><?php echo  $list_billing_cat['category'] ?></option>
								<?php
										}
									}
								?>
							</select>
							<div class="ajx_failure_msg" id="resource_type_err"></div>
						</td>
						<td><strong>SOW Status: <span class='mandatory_asterick'>*</span></strong></td>
						<td>					
							<label for="sow_status_signed"><input type="radio" name="sow_status" <?php echo ($quote_data['sow_status']==1) ?" checked='checked'" : "";  ?> id="sow_status_signed" value="1" tabindex="11" /> Signed</label>
							<label for="sow_status_unsigned"><input type="radio" name="sow_status" id="sow_status_unsigned" <?php echo ($quote_data['sow_status']==0) ?" checked='checked'" : "";  ?> value="0" tabindex="12" /> Un signed</label>
							<div class="ajx_failure_msg" id="sow_status_err"></div>
						</td>
					</tr>
					<tr>
						<td><strong>Departments: <span class='mandatory_asterick'>*</span></strong></td>
						<td>
							<select name="department_id_fk" id="department_id_fk" class="textfield width200px" tabindex="3">
								<option value="not_select">Please Select</option>
								<?php 
									if(isset($departments) && !empty($departments)) {
										foreach ($departments as $listDepartments) 
										{
								?>
									<option value="<?php echo $listDepartments['department_id'] ?>" <?php echo ($quote_data['department_id_fk'] == $listDepartments['department_id']) ? ' selected="selected"' : '' ?> > <?php echo $listDepartments['department_name'] ?> </option>
								<?php
										}
									}
								?>
							</select>
							<div class="ajx_failure_msg" id="department_err"></div>
						</td>
						<td width="115"><strong>SOW Value: <span class='mandatory_asterick'>*</span></strong></td>
						<td width="200">
							<input type="text" name="expect_worth_name" id="expect_worth_name" class="textfield" style=" width:23px" readonly value="<?php echo $quote_data['expect_worth_name']; ?>" />
							<input type="text" name="actual_worth_amount" id="actual_worth_amount" class="textfield" style=" width:163px" value="<?php echo $quote_data['actual_worth_amount']; ?>" tabindex="13" />
							<div class="ajx_failure_msg" id="sow_value_err"></div>
						</td>
					</tr>
					<tr>
						<td><strong>Project Billing Type: <span class='mandatory_asterick'>*</span></strong></td>
						<td>
							<select name="timesheet_project_types" id="timesheet_project_types" class="textfield width200px" tabindex="4">
								<option value="not_select">Please Select</option>
								<?php 
								if(isset($timesheet_project_types) && !empty($timesheet_project_types)) {
									foreach ($timesheet_project_types as $list_timesheet_project_types) 
									{
								?>
									<option value="<?php echo $list_timesheet_project_types['project_type_id'] ?>" <?php echo ($quote_data['project_type'] == $list_timesheet_project_types['project_type_id']) ? ' selected="selected"' : '' ?>><?php echo $list_timesheet_project_types['project_type_name'] ?></option>
								<?php
									}
								}
								?>
							</select>
							<div class="ajx_failure_msg" id="timesheet_project_types_err"></div>
						</td>
						<td width="115"><strong>Planned Start Date (SOW Start Date): <span class='mandatory_asterick'>*</span></strong></td>
						<td width="200">
							<input type="text" data-calendar="true" name="date_start" id="date_start" class="textfield" style=" width:200px" value="<?php if ($quote_data['date_start'] != '') echo date('d-m-Y', strtotime($quote_data['date_start'])); else echo ''; ?>" readonly tabindex="14" />
							<div class="ajx_failure_msg" id="date_start_err"></div>
						</td>
					</tr>
					<tr>
						<td><strong>Project Type: <span class='mandatory_asterick'>*</span></strong></td>
						<td>					
							<select name="project_types" id="project_types" class="textfield width200px" tabindex="5">
								<option value="not_select">Please Select</option>
								<?php 
								if(isset($project_types) && !empty($project_types)) {
									foreach ($project_types as $type) 
									{
								?>
									<option value="<?php echo $type['id'] ?>" <?php echo ($quote_data['project_types'] == $type['id']) ? ' selected="selected"' : '' ?>><?php echo $type['project_types'] ?></option>
								<?php
									}
								}
								?>
							</select>
							<div class="ajx_failure_msg" id="project_type_err"></div>							
						</td>
						<td width="115"><strong>Planned End Date (SOW End Date): <span class='mandatory_asterick'>*</span></strong></td>
						<td width="200">
							<input type="text" data-calendar="true" name="date_due" id="date_due" class="textfield" style=" width:200px" value="<?php if ($quote_data['date_due'] != '') echo date('d-m-Y', strtotime($quote_data['date_due'])); else echo ''; ?>" readonly tabindex="15" />
							<div class="ajx_failure_msg" id="date_due_err"></div>
						</td>
					</tr>
					<tr>
						<td><strong>Project Category: <span class='mandatory_asterick'>*</span></strong></td>
						<td>					
							<label for="project_center"><input type="radio" name="project_category" onclick="change_project_category(1);" id="project_center" <?php echo ($quote_data['project_category']==1) ? "checked='checked'" : ""; ?> value="1" tabindex="6" /> Profit Center</label>
							<label for="cost_center"><input type="radio" name="project_category" id="cost_center" onclick="change_project_category(2);" <?php echo ($quote_data['project_category']==2) ? "checked='checked'" : ""; ?> value="2" tabindex="7" /> Cost Center</label>
							<div class="ajx_failure_msg" id="project_category_err"></div>							
						</td>
						<td><strong>Browse file (SOW):</strong></td>
						<td>					
							<form name="payment_ajax_file_upload">
								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
								<div id="upload-container">
									<input type="file" title='upload' class="textfield" multiple id="sow_ajax_file_uploader" name="sow_ajax_file_uploader[]" onchange="return runSOWAjaxFileUpload();" tabindex="16" /><input type="hidden" id="exp_type" value="">									
								</div>
							</form>	
							<div id="sowUploadFile"></div>							
						</td>
					</tr>
					<tr id="project_center_tr" style="display:none;">
						<td><strong>Profit Center: <span class='mandatory_asterick'>*</span></strong></td>
						<td>
							<select name="project_center_value" id="project_center_value" class="textfield width200px" tabindex="8">
							<?php 
								if(isset($arr_profit_center) && !empty($arr_profit_center)) {
									
									foreach ($arr_profit_center as $list_profit_center) 
									{
									?>
									<option value="<?php echo $list_profit_center['id'] ?>|<?php echo $list_profit_center['profit_center'] ?>" <?php echo ($quote_data['project_center'] == $list_profit_center['id']) ? ' selected="selected"' : '' ?> ><?php echo $list_profit_center['profit_center'] ?></option>
									<?php
									}
								}
							?>								
							</select>
							<div class="ajx_failure_msg" id="project_center_value_err"></div>							
						</td>
					</tr>
				
					<tr id="cost_center_tr" style="display:none; height:40px;">
						<td><strong>Cost Center: <span class='mandatory_asterick'>*</span></strong></td>
						<td>
							<select name="cost_center_value" id="cost_center_value" class="textfield width200px" tabindex="9" >
								<?php 
									if(isset($arr_cost_center) && !empty($arr_cost_center)) {
										
										foreach ($arr_cost_center as $list_cost_center) 
										{
										?>
										<option value="<?php echo $list_cost_center['id'] ?>|<?php echo $list_cost_center['cost_center'] ?>"<?php echo ($quote_data['cost_center'] == $list_cost_center['id']) ? ' selected="selected"' : '' ?> ><?php echo $list_cost_center['cost_center'] ?></option>
										<?php
										}
									}
								?>
							</select>
						<div class="ajx_failure_msg" id="cost_center_value_err"></div>							
						</td>
					</tr>
					<tr>
						<td><strong>Customer Type: <span class='mandatory_asterick'>*</span></strong></td>
						<td>					
							<label for="int_customer_type"><input type="radio" name="customer_type" id="int_customer_type" <?php echo (isset($quote_data['customer_type']) && $quote_data['customer_type']==0) ? "checked='checked'" : ""; ?> value="0" tabindex="6" /> Internal</label>
							<label for="ext_customer_type"><input type="radio" name="customer_type" id="ext_customer_type" <?php echo (isset($quote_data['customer_type']) && $quote_data['customer_type']==1) ? "checked='checked'" : ""; ?> value="1" tabindex="7" /> External</label>
							<div class="ajx_failure_msg" id="customer_type_err"></div>							
						</td>
					</tr>

					<tr>
						<td colspan="4">
							<button type="submit" class="positive" style="float:right;" onclick="update_project_detail('<?php echo $project_id; ?>','tabs-project'); return false;" tabindex="17" >Update</button>
						</td>
					</tr>
				</table>
			</form>
		</div>
		
		<div id="tabs-assign-users" >
			<form id="set-assign-users" class="layout">
				<input type="hidden" name="project_lead_id" value='<?php echo $project_id; ?>' />
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				 <table class="payment-table">
					<thead>
					<tr>
						<th align="left"><strong>Select Project Manager:</strong></th>
						<th align="left"><strong>Select Team Members:</strong></th>
						<th align="left"><strong>Select Stake Holders:</strong></th>						
					</tr>
					</thead>
					<tbody>
					 <tr>
						<td valign="top"  width="240">
							<select class="chzn-select" id="project_manager" data-placeholder="Select Member" name="project_manager">
							<?php if(!empty($user_accounts)):?>
								<?php foreach($user_accounts as $pms):?>
									<option value=""></option>
									<option <?php echo ($quote_data['assigned_to'] == $pms['userid'])?'selected="selected"':''?> value="<?php echo $pms['userid']?>"><?php echo $pms['first_name'].' '.$pms['last_name'].'-'.$pms['emp_id'];?></option>
								<?php endforeach;?>
							<?php endif; ?>
							</select>
						</td>
						<?php 
						$team_members = array();
						if (is_array($contract_users) && count($contract_users) > 0) { 
							foreach ($contract_users as $data) {
								$team_members[] = $data['userid_fk'];
							}
						}
						?>
						<td valign="top"  width="240">
						<select  class="chzn-select" multiple="multiple" id="project_team_members" data-placeholder="Select Members" name="project_team_members[]">
						<?php if(!empty($user_accounts)):?>
							<!--option value="">Select</option-->
							<?php foreach($user_accounts as $pms):
									$selected = (in_array($pms['userid'],$team_members))?'selected="selected"':'';?>
								<option <?php echo $selected;?> value="<?php echo $pms['userid']?>"><?php echo $pms['first_name'].' '.$pms['last_name'].'-'.$pms['emp_id'];?></option>
							<?php endforeach;?>
						<?php endif; ?>
						</select>	
						</td>
						<?php
							// get stake holders 
							$stake_users_array = array();							
							if(count($stake_holders) > 0 && !empty($stake_holders)):
								foreach($stake_holders as $sh):
									$stake_users_array[] = $sh['user_id'];
								endforeach;
							endif;
						//	echo '<pre>';print_r($restrict1);exit;
						?>						
						<td valign="top"  width="150">
							<select class="chzn-select" multiple="multiple" id="stake_members" data-placeholder="Select Members" name="stake_members[]">
							<?php if(!empty($user_accounts)):?>
								<!--option value="">Select</option-->
								<?php foreach($user_accounts as $pms):
								$selected = (in_array($pms['userid'],$stake_users_array))?'selected="selected"':'';?>
								<option <?php echo $selected; ?> value="<?php echo $pms['userid']?>"><?php echo $pms['first_name'].' '.$pms['last_name'].'-'.$pms['emp_id'];?></option>
								<?php endforeach;?>
							<?php endif; ?>
							</select>	
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<button type="submit" class="positive" style="float:right;" onclick="update_project_users('tabs-assigned-users'); return false;" tabindex="17" >Update</button>
						</td>
					</tr>	
					</tbody>
				 </table>
			</form>
		</div>
		
		<div id="tabs-milestone">
			<form id="set-milestones" class="layout">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<table class="payment-table" id="milestone-tbl" >
					<thead>
						<tr>
							<th>Payment Milestone</th>
							<th>Milestone date</th>
							<th>For the Month & Year</th>
							<th>Currency</th>
							<th>Value</th>
							<th>Action</th>
						</tr>
					</thead>
					<tr>
						<td><input type="text" name="project_milestone_name[]" class="project_milestone_name textfield" /></td>
						<td><input type="text" data-calendar="true" name="expected_date[]" readonly class="expected_date textfield" /></td>
						<td><input type="text" data-calendar="false" class="month_year textfield" readonly name="month_year[]" /></td>
						<td><input type="text" class="textfield" value="<?php echo $quote_data['expect_worth_name']; ?>" readonly name="currency_type" style="width: 41px;" /></td>
						<td><input onkeypress="return isNumberKey(event)" type="text" name="amount[]" class="amount textfield" maxlength="10" /></td>
						<td>
							<a id="addMilestoneRow" class="createBtn"></a>
							<a id="deleteMilestoneRow" class="del_file" style="margin: 2px 0px 2px 3px;"></a>
						</td>
					</tr>
				</table>
				<div class="buttons" style="width: 100%; position: relative; margin-top: 10px;">
					<button type="submit" style="left: 45%; position: inherit;" class="positive" id="positiveBtn" onclick="confirm_project('<?php echo $project_id; ?>'); return false;">Generate Project</button>
				</div>
			</form>
		</div>
		
				
		
	</div>
</div>
<script type="text/javascript">
	$(function(){
		var config = {
			'.chzn-select'           : {},
			'.chzn-select-deselect'  : {allow_single_deselect:false},
			'.chzn-select-no-single' : {disable_search_threshold:10},
			'.chzn-select-no-results': {no_results_text:'Oops, nothing found!'},
			'.chzn-select-width'     : {width:"95%"}
		}
		for (var selector in config) {
			$(selector).chosen(config[selector]);
		}
	}); 
	var usr_level 		 = "<?php echo $username['level']; ?>";
	var cur_project_id   = "<?php echo $project_id; ?>";
	var project_category = "<?php echo $quote_data['project_category']; ?>";
</script>
<script type="text/javascript" src="assets/js/leads/lead_confirmation_view.js"></script>