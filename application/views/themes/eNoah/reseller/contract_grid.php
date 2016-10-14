<table class="dashboard-heads dataTable data-tbl" cellspacing="0" cellpadding="0" border="0" width="100%">
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
			<th class="header">Tax %</th>
			<th class="header">Contract Document</th>
			<th class="header">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php if(!empty($contract_data) && count($contract_data)>0) { ?>
			<?php foreach($contract_data as $row) { ?>
				<tr id="contr_<?php echo $row['id']; ?>">
					<td align="left"><?php echo $row['contract_title']; ?></td>
					<td align="left">
						<?php
							$cm_name = $row['first_name'];
							if(isset($row['last_name']) && !empty($row['last_name'])){
								$cm_name .= " ".$row['last_name'];
							}
							echo $cm_name;
						?>
					</td>
					<td align="left"><?php echo ($row['contract_start_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['contract_start_date'])) : '';?></td>
					<td align="left"><?php echo ($row['contract_end_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['contract_end_date'])) : '';?></td>
					<td align="left"><?php echo ($row['renewal_reminder_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['renewal_reminder_date'])) : '';?></td>
					<td align="left"><?php echo ($row['contract_signed_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['contract_signed_date'])) : '';?></td>
					<td align="left"><?php echo $this->contract_status[$row['contract_status']]; ?></td>
					<td align="right"><?php echo $currency_arr[$row['currency']];?></td>
					<td align="right"><?php echo $row['tax']; ?></td>
					<td align="right">
						<?php
							$con_files = array();
							$con_files = getContractsUploadsFile($row['id']);
							// echo "<pre>"; print_r($con_files); echo "</pre>";
						?>	
							<div id='existUploadedFile'>
								<?php if(is_array($con_files) && !empty($con_files) && count($con_files)>0) { ?>
									<?php $serial_id = 1; ?>
									<?php foreach($con_files as $rec_file) { ?>
										<div style="float: left; width: 100%; margin-top: 5px;">
											<span style="float: left;">
												<?php $file_id = base64_encode($rec_file['id']); ?>
												<a onclick="download_contract_files('<?php echo $file_id; ?>'); return false;" title="<?php echo $rec_file['file_name']; ?>"><img src="assets/img/file-download.png">&nbsp;Download</a>
											</span>
										</div>
									<?php $serial_id++; ?>
									<?php } ?>
								<?php } ?>
							</div>
					</td>
					<td align="left">
						<?php if($this->session->userdata('edit')==1) { ?>
							<a title="Edit" onclick="editContractData(<?php echo $row['id']; ?>, <?php echo $row['contracter_id']; ?>, 'edit'); return false;"><img src="assets/img/edit.png" alt="edit"></a>
						<?php } else { ?>
							<a title="View" onclick="editContractData(<?php echo $row['id']; ?>, <?php echo $row['contracter_id']; ?>, 'view'); return false;"><img src="assets/img/view.png" alt="edit"></a>
						<?php } ?>
						<?php if($this->session->userdata('delete')==1) { ?>
							<a title="Delete" onclick="deleteContractData(<?php echo $row['id']; ?>, <?php echo $row['contracter_id']; ?>); return false;"><img src="assets/img/trash.png" alt="delete"></a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
	</tbody>
</table>
<script type="text/javascript">
$(function() {
	resellerDataTable();
});
</script>