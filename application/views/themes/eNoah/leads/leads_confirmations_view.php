<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
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
?>
<div style="width:100%;">
	<div class="file-tabs-close-confirm-tab"></div>
	<div id="tabs">
		<ul class="tabs-confirm">
			<li><a href="#tabs-client">Client Details</a></li>
			<li><a href="#tabs-project">Project Details</a></li>
			<li><a href="#tabs-milestone">Milestone</a></li>
		</ul>
		<div id="tabs-client">
			<!--p class="clearfix" ><h3>Client Details</h3></p-->
			<form name="customer_detail_form" id="customer_detail_form" method="post" onsubmit="return false;">
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			<input type="hidden" name="custid" value="<?php echo $customer_data['custid'] ?>" />
			<table class="layout">
				<tr>
					<td width="100"><strong>First name:*</strong></td>
					<td width="240"><input type="text" name="first_name" value="<?php echo $customer_data['first_name']; ?>" class="textfield width200px required" /> </td>
					<td width="100"><strong>Last Name:</strong></td>
					<td width="240"><input type="text" name="last_name" value="<?php echo $customer_data['last_name']; ?>" class="textfield width200px required" /></td>
				</tr>
				<tr>
					<td><strong>Position:</strong></td>
					<td><input type="text" name="position_title" value="<?php echo $customer_data['position_title']; ?>" class="textfield width200px required" /></td>
                    <td><strong>Company:*</strong></td>
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
					<td><strong>Region:*</strong></td>
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
					<td><strong>Country:*</strong></td>
						<td id='country_row'>
							<select id="add1_country" name="add1_country" class="textfield width200px required" >
							<option value="0">Select Country</option>                           
							</select>
							<a class="addNew" id="addButton" style ="display:none;"></a>	
						</td>
				</tr>
				<tr>
					<td><strong>State:*</strong></td>
					<td id='state_row'>
						<select id="add1_state" name="add1_state" class="textfield width200px required">
							<option value="0">Select State</option>                           
						</select>
						<a id="addStButton" class="addNew" style ="display:none;"></a>
					</td>
					<td><strong>Location:*</strong></td>
					<td id='location_row'>
						<select name="add1_location" class="textfield width200px required">
						<option value="0">Select Location</option>                           
						</select>
						<a id="addLocButton" class="addNew" style ="display:none;"></a>
					</td>
				</tr>
				<tr>
					<td><strong>Direct Phone:</strong></td>
					<td><input type="text" name="phone_1" value="<?php echo $customer_data['phone_1']; ?>" class="textfield width200px" />
						</td>
                    
					<td><strong>Work Phone:</strong></td>
					<td><input type="text" name="phone_2" value="<?php echo $customer_data['phone_2']; ?>" class="textfield width200px" /></td>
				</tr>
                    <tr>
					<td><strong>Mobile Phone:</strong></td>
					<td><input type="text" name="phone_3" value="<?php echo $customer_data['phone_3']; ?>" class="textfield width200px required" />
						</td>
                    
					<td><strong>Fax Line:</strong></td>
					<td><input type="text" name="phone_4" value="<?php echo $customer_data['phone_4']; ?>" class="textfield width200px" /></td>
				</tr>
                <tr>
					<td><strong>Email:</strong></td>
					<td><input type="text" name="email_1" id="emailval" autocomplete="off" value="<?php echo $customer_data['email_1']; ?>" class="textfield width200px required" /> 
					<div class="errmsg"></div>
					<!--input type="hidden" value="<?php #echo $customer_data['custid']; ?>" name="email_1" id="email_1" /-->
					</td>
                    <td><strong>Secondary Email:</strong></td>
					<td><input type="text" name="email_2" value="<?php echo $customer_data['email_2']; ?>" class="textfield width200px required" /> 
					</td>
				</tr>
				<tr>
					<td><strong>Web:</strong></td>
					<td><input type="text" name="www_1" value="<?php echo $customer_data['www_1']; ?>" class="textfield width200px required" />
					</td>
                    <td><strong>Secondary Web:</strong></td>
					<td><input type="text" name="www_2" value="<?php echo $customer_data['www_2']; ?>" class="textfield width200px required" />
					</td>
				</tr>
				<tr>
					<td><strong>Sales Contact Name:</strong></td>
					<td>
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
						<input type="text" name="sales_contact_name" value="<?php echo $sales_contact_name; ?>" class="textfield width200px" readonly />
						<input type="hidden" name="sales_contact_userid_fk" value="<?php echo $sales_contact_userid_fk; ?>" class="textfield width200px" readonly />
					</td>
                    <td><strong>Sales Contact Email:</strong></td>
					<td>
					<input type="text" name="sales_contact_email" value="<?php echo $sales_contact_email; ?>" class="textfield width200px" readonly />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
                        <div id="subBtn" class="buttons pull-right" style="padding-right: 30px;">
							<button type="submit" class="positive" id="positiveBtn" onclick="update_customer('<?php echo $customer_data['custid'] ?>'); return false;">Update</button>
						</div>
                    </td>
				</tr>
			</table>
			</form>
		</div>
		
		<div id="tabs-project" >
			<!--p class="clearfix" ><h3>Project Details*</h3></p-->
			<form action="" method="post" id="project-confirm-form" onsubmit="return false;">
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<table class="layout" cellspacing="10">
					<tr>
						<td width="115"><strong>Departments:*</strong></td>
						<td width="200">
							<select name="department_id_fk" id="department_id_fk" class="textfield width200px">
							<option value="not_select">Please Select</option>
							<?php 
								if(isset($departments) && !empty($departments)) {
									foreach ($departments as $listDepartments) 
									{
							?>
								<option value="<?php echo $listDepartments['department_id'] ?>"><?php echo  $listDepartments['department_name'] ?></option>
							<?php
									}
								}
							?>
							</select>
						</td>
						<td class="ajx_failure_msg" id="department_err"></td>
					</tr>
					<tr>
						<td><strong>Resource Type:*</strong></td>
						<td>
							<select name="resource_type" id="resource_type" class="textfield width200px">
								<option value="not_select">Please Select</option>
								<?php 
									if(isset($billing_categories) && !empty($billing_categories)) {
										foreach ($billing_categories as $list_billing_cat) 
										{
								?>
									<option value="<?php echo $list_billing_cat['bill_id'] ?>"><?php echo  $list_billing_cat['category'] ?></option>
								<?php
										}
									}
								?>
							</select>
						</td>
						<td class="ajx_failure_msg" id="resource_type_err"></td>
					</tr>
					<tr>
						<td><strong>Project Name:*</strong></td>
						<td>
							<input type="text" name="project_name" id="project_name" class="textfield" style=" width:200px" value="<?php echo  htmlentities($quote_data['lead_title'], ENT_QUOTES) ?>" />
						</td>
						<td class="ajx_failure_msg" id="project_name_err"></td>
					</tr>
					<tr>
						<td><strong>Project Types:*</strong></td>
						<td>
							<select name="timesheet_project_types" id="timesheet_project_types" class="textfield width200px">
								<option value="not_select">Please Select</option>
								<?php 
								if(isset($timesheet_project_types) && !empty($timesheet_project_types)) {
									foreach ($timesheet_project_types as $list_timesheet_project_types) 
									{
								?>
									<option value="<?php echo $list_timesheet_project_types['project_type_id'] ?>"><?php echo  $list_timesheet_project_types['project_type_name'] ?></option>
								<?php
									}
								}
								?>
							</select>				
						</td>
						<td class="ajx_failure_msg" id="timesheet_project_types_err"></td>
					</tr>
					<tr>
						<td><strong>Project Category:*</strong></td>
						<td>					
							<lable for="project_center"><input type="radio" name="project_category" onclick="change_project_category(1);" id="project_center"  value="1" /> Profit Center</lable>
							<lable for="cost_center"><input type="radio" name="project_category" id="cost_center" onclick="change_project_category(2);"  value="2" /> Cost Center</lable>				
						</td>
						<td class="ajx_failure_msg" id="project_category_err"></td>
					</tr>
				
					<tr id="project_center_tr" style="display:none;">
						<td><strong>Profit Center:*</strong></td>
						<td>
							<select name="project_center_value" id="project_center_value" class="textfield width200px">
							<?php 
								if(isset($arr_profit_center) && !empty($arr_profit_center)) {
									
									foreach ($arr_profit_center as $list_profit_center) 
									{
									?>
									<option value="<?php echo $list_profit_center['id'] ?>|<?php echo  $list_profit_center['profit_center'] ?>"><?php echo  $list_profit_center['profit_center'] ?></option>
									<?php
									}
								}
							?>								
							</select>				
						</td>
						<td class="ajx_failure_msg" id="project_center_value_err"></td>
					</tr>
				
					<tr id="cost_center_tr" style="display:none; height:40px;">
						<td><strong>Cost Center:*</strong></td>
						<td>
							<select name="cost_center_value" id="cost_center_value" class="textfield width200px">
								<?php 
									if(isset($arr_cost_center) && !empty($arr_cost_center)) {
										
										foreach ($arr_cost_center as $list_cost_center) 
										{
										?>
										<option value="<?php echo $list_cost_center['id'] ?>|<?php echo  $list_cost_center['cost_center'] ?>"><?php echo  $list_cost_center['cost_center'] ?></option>
										<?php
										}
									}
								?>
							</select>				
						</td>
						<td class="ajx_failure_msg" id="cost_center_value_err"></td>
					</tr>
				
					<tr>
						<td><strong>SOW Status:*</strong></td>
						<td>					
							<lable for="sow_status_signed"><input type="radio" name="sow_status" id="sow_status_signed"  value="1" /> Signed</lable>
							<lable for="sow_status_unsigned"><input type="radio" name="sow_status" id="sow_status_unsigned"  checked value="0" /> Un signed</lable>				
						</td>
						<td class="ajx_failure_msg" id="sow_status_err"></td>
					</tr>
				
					<tr>
						<td><strong>Browse file (SOW):</strong></td>
						<td>					
							<form name="payment_ajax_file_upload">
								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
								<div id="upload-container">
									<input type="file" title='upload' class="textfield" multiple id="sow_ajax_file_uploader" name="sow_ajax_file_uploader[]" onchange="return runSOWAjaxFileUpload();"/><input type="hidden" id="exp_type" value="">									
								</div>
							</form>	
							<div id="sowUploadFile"></div>							
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<button type="submit" class="positive" style="float:right;" onclick="update_project_detail('<?php echo $project_id; ?>'); return false;">Update</button>
						</td>
					</tr>
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
							<th>Value</th>
							<th>Action</th>
						</tr>
					</thead>
					<tr>
						<td><input type="text" name="project_milestone_name[]" class="project_milestone_name textfield" /></td>
						<td><input type="text" data-calendar="true" name="expected_date[]" class="expected_date textfield" /></td>
						<td><input type="text" data-calendar="false" class="month_year textfield" name="month_year[]" /></td>
						<td><input type="text" class="textfield" value="<?php echo $quote_data['expect_worth_name']; ?>" readonly name="currency_type" style="width: 30px;" /></td>
						<td><input onkeypress="return isNumberKey(event)" type="text" name="amount[]" class="amount textfield" maxlength="10" /></td>
						<td>
							<a id="addMilestoneRow" class="createBtn"></a>
							<a id="deleteMilestoneRow" class="del_file" style="margin: 2px 0px 2px 3px;"></a>
						</td>
					</tr>
				</table>
				<div class="buttons" style="width: 100%; position: relative; margin-top: 10px;">
					<button type="submit" style="left: 45%; position: inherit;" class="positive" id="positiveBtn" onclick="confirm_project('<?php echo $project_id; ?>'); return false;">Confirm</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	var usr_level 		 = "<?php echo $username['level']; ?>";
	var cur_project_id   = "<?php echo $project_id; ?>";
</script>
<script type="text/javascript" src="assets/js/leads/lead_confirmation_view.js"></script>