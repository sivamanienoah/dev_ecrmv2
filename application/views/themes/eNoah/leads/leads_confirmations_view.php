<div style="width:100%;">
	<span style="float:right; cursor:pointer;" onclick="$.unblockUI();"><img src='<?php echo base_url().'assets/img/cross.png'; ?>' /></span>

<!--- Client Details End Here --->
<div class="q-init-details" style="width:42%; float:left;">
<p class="clearfix" ><h3>Client Details</h3></p>
<table style="text-align: left;">
<tr style="height:20px;"><th>Client Name</th><td>:</td><td><?php echo $customer_data['first_name']; ?> <?php echo $customer_data['last_name']; ?></td></tr>
<tr style="height:20px;"><th>Client Code</th><td>:</td><td><?php  echo ($customer_data['client_code'])?$customer_data['client_code']:'---';  ?></td></tr>
<tr style="height:20px;"><th>Position Title</th><td>:</td><td><?php  echo ($customer_data['position_title'])?$customer_data['position_title']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Company</th><td>:</td><td><?php echo  ($customer_data['company'])?$customer_data['company']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Address Line 1</th><td>:</td><td><?php  echo ($customer_data['add1_line1'])?$customer_data['add1_line1']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Address Line 2</th><td>:</td><td><?php  echo ($customer_data['add1_line2'])?$customer_data['add1_line2']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Suburb</th><td>:</td><td><?php  echo ($customer_data['add1_suburb'])?$customer_data['add1_suburb']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Post code</th><td>:</td><td><?php echo ($customer_data['add1_postcode'])?$customer_data['add1_postcode']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Region</th><td>:</td><td><?php  echo ($customer_region)?$customer_region:'---'; ?></td></tr>
<tr style="height:20px;"><th>Country</th><td>:</td><td><?php  echo ($customer_country)?$customer_country:'---'; ?></td></tr>
<tr style="height:20px;"><th>State</th><td>:</td><td><?php  echo ($customer_state)?$customer_state:'---'; ?></td></tr>
<tr style="height:20px;"><th>Location</th><td>:</td><td><?php  echo ($customer_location)?$customer_location:'---'; ?></td></tr>
<tr style="height:20px;"><th>Direct Phone</th><td>:</td><td><?php  echo ($customer_data['phone_1'])?$customer_data['phone_1']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Work Phone</th><td>:</td><td><?php  echo ($customer_data['phone_2'])?$customer_data['phone_2']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Mobile</th><td>:</td><td><?php  echo ($customer_data['phone_3'])?$customer_data['phone_3']:'---' ?></td></tr>
<tr style="height:20px;"><th>Email</th><td>:</td><td><?php  echo ($customer_data['email_1'])?$customer_data['email_1']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Sales Contact Name</th><td>:</td><td><?php  echo ($customer_data['sales_contact_name'])?$customer_data['sales_contact_name']:'---'; ?></td></tr>
<tr style="height:20px;"><th>Sales Contact Email</th><td>:</td><td><?php  echo ($customer_data['sales_contact_email'])?$customer_data['sales_contact_email']:'---'; ?></td></tr>
</table>

</div>
<!--- Client Details End Here --->

<?php //echo '<pre>'; print_r($arrDepartments);?>

					<div class="q-init-details" style="width:48%; float:left;">
					<p class="clearfix" ><h3>Project Details*</h3></p>
					<form action="" method="post" id="project-confirm-form" onsubmit="return false;">
					
					<table style="text-align: left;">
					
					
					<tr style="height:40px;"><th width="350">Departments*</th><td>:</td><td>
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
					
					
					</td></tr>
					
					
					<tr style="height:40px;"><th width="350">Resource Type*</th><td>:</td><td>
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
					
					
					</td></tr>
					
					<tr style="height:40px;"><th width="350">Project Name*</th><td>:</td><td><input type="text" name="project_name" id="project_name" class="textfield" style=" width:200px" value="<?php echo  htmlentities($quote_data['lead_title'], ENT_QUOTES) ?>" /></td></tr>
					
					<?php /*?><tr><th width="250">Project Types*</th><td>:</td><td>
					<select name="project_types" id="project_types" class="textfield width200px">
								<option value="not_select">Please Select</option>
					<?php 
						if(isset($project_types) && !empty($project_types)) {
							
							foreach ($project_types as $list_project_types) 
							{
							?>
								<option value="<?php echo $list_project_types['id'] ?>"><?php echo  $list_project_types['project_types'] ?></option>
							<?php
							}
						}
					?>
							</select>				
					</td></tr><?php */?>
					
					<tr><th width="350">Project Types*</th><td>:</td><td>
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
					</td></tr>
					
					<tr style="height:40px;"><th width="350">Project Category*</th><td>:</td><td>					
					<lable for="project_center"><input type="radio" name="project_category" onclick="change_project_category(1);" id="project_center"  value="1" /> Profit Center</lable>
					<lable for="cost_center"><input type="radio" name="project_category" id="cost_center" onclick="change_project_category(2);"  value="2" /> Cost Center</lable>				
					</td></tr>
					
					<tr id="project_center_tr" style="display:none; height:40px;"><th width="350">Profit Center*</th><td>:</td><td>
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
					</td></tr>
					
					<tr id="cost_center_tr" style="display:none; height:40px;"><th width="350">Cost Center*</th><td>:</td><td>
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
					</td></tr>
					
					<tr style="height:40px;"><th width="350">SOW Status*</th><td>:</td><td>					
					<lable for="sow_status_signed"><input type="radio" name="sow_status" id="sow_status_signed"  value="1" /> Signed</lable>
					<lable for="sow_status_unsigned"><input type="radio" name="sow_status" id="sow_status_unsigned"  checked value="0" /> Un signed</lable>				
					</td></tr>
					
					<tr style="height:40px;"><th width="350">Browse file (SOW)</th><td>:</td><td>					
					<form name="payment_ajax_file_upload">
								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
								<div id="upload-container">
									<input type="file" title='upload' class="textfield" multiple id="sow_ajax_file_uploader" name="sow_ajax_file_uploader[]" onchange="return runSOWAjaxFileUpload();"/><input type="hidden" id="exp_type" value="">									
								</div>
							</form>	
<div id="sowUploadFile"></div>							
					</td></tr>
					
					
					
					
					<tr><td colspan="2" align="center"><button type="submit" class="positive" style="float:right;" onclick="is_project(); return false;">Confirm</button></td></tr>
					
					</table>
					
				
						
						
					</div>
</div>