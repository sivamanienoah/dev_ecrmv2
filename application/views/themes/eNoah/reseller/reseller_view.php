<?php
ob_start();
require (theme_url().'/tpl/header.php');
?>
<div id="content">
	<div class="inner">	
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
		<?php if(is_array($reseller_det) && !empty($reseller_det) && count($reseller_det)>0) { ?>
			<p>
				<label>Reseller Name :</label>
				<?php
					$reseller_name = $reseller_det[0]['first_name'];
					if(!empty($reseller_det[0]['last_name'])){
						$reseller_name .= " ". $reseller_det[0]['last_name'];
					}
					echo $reseller_name;
				?>
			</p>
			
			<p>
				<label>Contract Manager Name :</label>
				<?php
					$get_contract_manager_name = getContractManagerName($reseller_det[0]['contract_manager']);
					echo isset($get_contract_manager_name) ? $get_contract_manager_name : "";
				?>
			</p>
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
				</ul>
			</div>
			<div id="rt-tab-1">
				<div class="buttons" id="create_contract_btn">
					<a href="javascript:void(0)" class="positive" onclick="getAddContractForm('<?php echo $reseller_det[0]['userid']; ?>'); return false;">Create Contract</a>
				</div>
				<div class="clear"></div>
				<div style="margin:7px 0 0;" id="succes_add_contract_data" class="succ_err_msg"></div>
				<div id="add_contract_form"></div><!---Add Contract Form----->
				<div id="list_contract_det"><!---List Contract Details----->
					<table class="data-table" cellspacing="0" cellpadding="0" border="0">
						<thead>
							<tr align="left">
								<th class="header">Contract Title</th>
								<th class="header">Contract Manager</th>
								<th class="header">Contract Start Date</th>
								<th class="header">Contract End Date</th>
								<th class="header">Renewal Reminder Date</th>
								<th class="header">Contract Signed Date</th>
								<th class="header">Contract Status</th>
								<th class="header">Currency</th>
								<th class="header">Tax</th>
								<th class="header">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(!empty($contract_data) && count($contract_data)>0) { ?>
								<?php foreach($contract_data as $row) { ?>
									<tr id="contr_<?php echo $row['id']; ?>">
										<td align="left"><?php echo $row['contract_title']; ?></td>
										<td align="left"><?php echo $row['description']; ?></td>
										<td align="left"><?php echo ($row['contract_start_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['contract_start_date'])) : '';?></td>
										<td align="left"><?php echo ($row['contract_end_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['contract_end_date'])) : '';?></td>
										<td align="left"><?php echo ($row['renewal_reminder_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['renewal_reminder_date'])) : '';?></td>
										<td align="left"><?php echo ($row['contract_signed_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['contract_signed_date'])) : '';?></td>
										<td align="left"><?php echo $this->contract_status[$row['contract_status']]; ?></td>
										<td align="right"><?php echo $currency_arr[$row['currency']];?></td>
										<td align="right"><?php echo $row['tax']; ?></td>
										<td align="left">
											<a title="Edit" onclick="editContractData(<?php echo $row['id']; ?>, <?php echo $row['contracter_id']; ?>); return false;"><img src="assets/img/edit.png" alt="edit"></a>
											<a title="Delete" onclick="deleteContractData(<?php echo $row['id']; ?>, <?php echo $row['contracter_id']; ?>); return false;"><img src="assets/img/trash.png" alt="delete"></a>
										</td>
									</tr>
								<?php } ?>
							<?php } else { ?>
								<tr><td colspan='10'> No Records Available. </td></tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div><!--rt-tab-1 - End -->
			<div id="rt-tab-2"></div><!--rt-tab-2 - End -->
			<div id="rt-tab-3"></div><!--rt-tab-3 - End -->
			<div id="rt-tab-4"></div><!--rt-tab-4 - End -->
			<div id="rt-tab-5"></div><!--rt-tab-5 - End -->
			<div id="rt-tab-6"></div><!--rt-tab-6 - End -->
			<div id="rt-tab-7"></div><!--rt-tab-7 - End -->
		</div><!--reseller_tabs-end-->
	<?php } else { 
		echo "You have no rights to access this page";
	} 
	?>
	</div><!--/Inner div -->
</div><!--/Content div -->
<script type="text/javascript" src="assets/js/jquery.form.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/reseller/reseller_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>