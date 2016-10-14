<?php
ob_start();
require (theme_url().'/tpl/header.php');
// echo "<pre>"; print_r($reseller_det); echo "</pre>";
?>
<link rel="stylesheet" href="assets/css/chosen.css" type="text/css" />
<div id="content">
	<div class="inner">	
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
		<?php if(is_array($reseller_det) && !empty($reseller_det) && count($reseller_det)>0) { ?>
			<p>
				<label>First Name :</label>
				<?php echo ucfirst($reseller_det[0]['first_name']); ?>
			</p>
			<p>
				<label>Last Name :</label>
				<?php echo isset($reseller_det[0]['last_name']) ? ucfirst($reseller_det[0]['last_name']) : ''; ?>
			</p>
			<p>
				<label>Email :</label>
				<?php echo $reseller_det[0]['email']; ?>
			</p>
			<!--p>
				<label>Username :</label>
				<?php #echo $reseller_det[0]['username']; ?>
			</p-->
			<p>
				<label>Phone :</label>
				<?php echo (isset($reseller_det[0]['phone']) && !empty($reseller_det[0]['phone'])) ? $reseller_det[0]['phone'] : '-'; ?>
			</p>
			<p>
				<label>Mobile :</label>
				<?php echo (isset($reseller_det[0]['mobile']) && !empty($reseller_det[0]['mobile'])) ? $reseller_det[0]['mobile'] : '-'; ?>
			</p>
			<!--p>
				<label>Contract Manager Name :</label>
				<?php
					// $get_contract_manager_name = getContractManagerName($reseller_det[0]['contract_manager']);
					// echo isset($get_contract_manager_name) ? $get_contract_manager_name : "";
				?>
			</p-->
		<?php } ?> <!--If condition - end-->
		
		<!-- Tabs --->
		<div id="reseller_tabs" style="width:99.5%;float:left;margin:10px 0 0 0;">
			<div>
				<ul id="reseller_view_tabs">
					<li><a href="<?php echo current_url() ?>#rt-tab-1">Contracts</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-2">Commission History</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-3">Sales History</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-4">Leads</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-5">Projects</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-6">Contacts</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-7">Audit History</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-8">Send Email</a></li>
				</ul>
			</div>
			<div id="rt-tab-1">
				<?php if($this->session->userdata('add')==1) { ?>
					<div class="buttons" id="create_contract_btn">
						<a href="javascript:void(0)" class="custom-blue-btn" onclick="getAddContractForm('<?php echo $reseller_det[0]['userid']; ?>'); return false;">Add Contract</a>
					</div>
				<?php } ?>
				<div class="clear"></div>
				<div style="margin:7px 0 0;" id="succes_add_contract_data" class="succ_err_msg"></div>
				<div id="add_contract_form"></div><!---Add Contract Form----->
				<div id="list_contract_det"><!---List Contract Details----->
					<?php echo $this->load->view('reseller/contract_grid', $contract_data); ?>
				</div>
			</div><!--rt-tab-1 - End -->
			
			<div id="rt-tab-2">
				<?php if($this->session->userdata('add')==1) { ?>
					<div class="buttons" id="create_commission_btn">
						<a href="javascript:void(0)" class="custom-blue-btn" onclick="getAddCommissionForm('<?php echo $reseller_det[0]['userid']; ?>'); return false;">Add Commission</a>
					</div>
				<?php } ?>
				<div class="clear"></div>
				<div style="margin:7px 0 0;" id="succes_add_commission_data" class="succ_err_msg"></div>
				<div id="commission_form"></div><!---Add Contract Form----->
				<div id="list_commission_det"><!---List Contract Details----->
					<?php echo $this->load->view('reseller/commission_grid', $commission_data); ?>
				</div>
			</div><!--rt-tab-2 - End -->
			
			<div id="rt-tab-3">
				<div id='sale_filter' style="margin-top: 10px; position: relative;">
					<?php $attributes = array('id'=>'sale_history', 'name'=>'sale_history'); ?>
					<?php echo form_open_multipart("reseller/getSaleHistory", $attributes); ?>
						<table>
							<tr>
								<td><label>Financial Year:</label></td>
								<td>
									<select name="financial_year" id="financial_year" class="textfield" style="width: 90px;">
									<option value ="">Select</option>
									<?php for($i=date("Y")+1;$i>=2010;$i--) { ?>
										<option value="<?php echo $i ?>" <?php if($curFiscalYear == $i) echo "selected='selected'"; ?>><?php echo ($i-1).' - '.$i ?></option>
									<?php } ?>
									</select>
									<input type="hidden" name="reseller_id" id="reseller_id" value="<?php echo $reseller_det[0]['userid']; ?>">
									<input type="hidden" name="hidden_curFiscalYear" id="hidden_curFiscalYear" value="<?php echo $curFiscalYear; ?>">
								</td>
								<td style="padding-bottom: 5px;">
									<div class="buttons">
										<button type="submit" id="sale_history_submit" class="positive">Search</button>
									</div>
								</td>
								<td><div id="financial_year_err"></div></td>
							</tr>
						</table>
					<?php form_close(); ?>
				</div>
				<div id="sale_history_data">
					<?php echo $this->load->view('reseller/sale_history_grid', $sales); ?>
				</div>
			</div><!--rt-tab-3 - End -->
			
			<div id="rt-tab-4">
				<div id='lead_filter' style="margin-top: 10px; position: relative;">
					<?php $attributes = array('id'=>'lead_filter_form', 'name'=>'lead_filter_form'); ?>
					<?php echo form_open_multipart("reseller/getResellerLeads", $attributes); ?>
						<table>
							<tr>
								<td><input type="radio" name="filter_leads" value="active" checked="checked" /></td><td>Open Leads</td>
								<td><input type="radio" name="filter_leads" value="all"></td><td>All Leads</td>
							</tr>
						</table>
					<?php form_close(); ?>
				</div>
				<div id="reseller_lead_data"></div>
			</div><!--rt-tab-4 - End -->
			
			<div id="rt-tab-5">
				<div id='sale_filter' style="margin-top: 10px; position: relative;">
					<?php $attributes = array('id'=>'reseller_project_form', 'name'=>'reseller_project_form'); ?>
					<?php echo form_open_multipart("reseller/getResellerJobs", $attributes); ?>
						<table>
							<tr>
								<td><input type="radio" name="filter_projects" value="active" checked="checked" /></td><td>Active</td>
								<td><input type="radio" name="filter_projects" value="all"></td><td>All</td>
							</tr>
						</table>
					<?php form_close(); ?>
				</div>
				<div id="reseller_project_data"></div>
			</div><!--rt-tab-5 - End -->
			
			<div id="rt-tab-6">
				<div id="reseller_contact_data"></div>
			</div><!--rt-tab-6 - End -->
			
			<div id="rt-tab-7">
				<div id="audit_history_data"></div>
			</div><!--rt-tab-7 - End -->
			
			<div id="rt-tab-8">
				<div id="send_email_data">
					<form id="comm-log-form">
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						<table>
							<tr>
								<td><label>Email To:</label></td>
								<td class="email-list">
									<select data-placeholder="Choose User..." name="user_mail" multiple='multiple' id="user_mail" class="chzn-select" style="width:420px;">
									<?php
										if( is_array($users) && !empty($users) && count($users)>0) {
											foreach($users as $ua) {
											?>
												<option value="<?php echo 'email-log-'.$ua['userid']; ?>"><?php echo $ua['first_name'].' '.$ua['last_name']; ?></option>
											<?php
											}
										}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<td><label class="normal">Message:</label></td>
								<td><textarea name="job_log" id="job_log" class="textfield height100px" style="width:410px;"></textarea></td>
							</tr>
							<tr>
								<td><label class="normal">Signature:</label></td>
								<td><textarea name="signature" class="textfield" style="width:410px;" rows="3" style="color:#666;"><?php echo $this->userdata['signature'] ?></textarea></td>
							</tr>
							<tr>
								<td colspan=2 align="right">
									<div style="overflow:hidden;">
										<div class="button-container">
											<div class="buttons">
												<button type="submit" class="positive" onclick="addLog(); return false;" id="add-log-submit-button">Add Post</button>
											</div>
										</div>
									</div>									
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div><!--rt-tab-8 - End -->
			
		</div><!--reseller_tabs-end-->
	<?php } else { 
		echo "You have no rights to access this page";
	} 
	?>
	</div><!--/Inner div -->
</div><!--/Content div -->
<script>
var reseller_id = '<?php echo $reseller_det[0]['userid']; ?>';
var project_request_url = "http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>";
</script>
<script type="text/javascript" src="assets/js/jquery.form.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/chosen.jquery.js"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/reseller/reseller_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>