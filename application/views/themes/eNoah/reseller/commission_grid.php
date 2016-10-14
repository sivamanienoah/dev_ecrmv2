<table class="dashboard-heads dataTable data-tbl" cellspacing="0" cellpadding="0" border="0" width="100%">
	<thead>
		<tr align="left">
			<th class="header">Title</th>
			<th class="header">Project Name</th>
			<th class="header">Payment Advice Date</th>
			<th class="header">Milestone Name</th>
			<th class="header">For The Month Year</th>
			<th class="header">Currency</th>
			<th class="header">Value</th>
			<th class="header">Attachment Documents</th>
			<th class="header">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php if(!empty($commission_data) && count($commission_data)>0) { ?>
			<?php foreach($commission_data as $cmsn_row) { ?>
				<tr id="csmn_<?php echo $cmsn_row['id']; ?>">
					<td align="left"><?php echo $cmsn_row['commission_title']; ?></td>
					<td align="left"><?php echo $cmsn_row['lead_title']; ?></td>
					<td align="left"><?php echo ($cmsn_row['payment_advice_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($cmsn_row['payment_advice_date'])) : '';?></td>
					<td align="left"><?php echo $cmsn_row['commission_milestone_name'];?></td>
					<td align="left"><?php echo ($cmsn_row['for_the_month_year']!='0000-00-00 00:00:00') ? date('M Y', strtotime($cmsn_row['for_the_month_year'])) : '';?></td>
					<td align="right"><?php echo $currency_arr[$cmsn_row['commission_currency']];?></td>
					<td align="left"><?php echo $cmsn_row['commission_value']; ?></td>
					<td align="right">
						<?php
							$com_files = array();
							$com_files = getCommissionUploadsFile($cmsn_row['id']);
							// echo "<pre>"; print_r($com_files); echo "</pre>";
						?>	
							<div id='existUploadedFile'>
								<?php if(is_array($com_files) && !empty($com_files) && count($com_files)>0) { ?>
									<?php $serial_id = 1; ?>
									<?php foreach($com_files as $rec_file) { ?>
										<div style="float: left; width: 100%; margin-top: 5px;">
											<span style="float: left;">
												<?php $file_id = base64_encode($rec_file['id']); ?>
												<a onclick="download_files('<?php echo $file_id; ?>'); return false;"><?php echo $rec_file['file_name']; ?></a>
											</span>
										</div>
									<?php $serial_id++; ?>
									<?php } ?>
								<?php } ?>
							</div>
					</td>
					<td align="left">
						<?php if($this->session->userdata('edit')==1) { ?>
							<a title="Edit" onclick="editCommissionData(<?php echo $cmsn_row['id']; ?>, <?php echo $cmsn_row['contracter_id']; ?>, 'edit'); return false;"><img src="assets/img/edit.png" alt="edit"></a>
						<?php } else { ?>
							<a title="View" onclick="editCommissionData(<?php echo $cmsn_row['id']; ?>, <?php echo $cmsn_row['contracter_id']; ?>, 'view'); return false;"><img src="assets/img/view.png" alt="edit"></a>
						<?php } ?>
						<?php if($this->session->userdata('delete')==1) { ?>
							<a title="Delete" onclick="deleteCommissionData(<?php echo $cmsn_row['id']; ?>, <?php echo $cmsn_row['contracter_id']; ?>); return false;"><img src="assets/img/trash.png" alt="delete"></a>
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