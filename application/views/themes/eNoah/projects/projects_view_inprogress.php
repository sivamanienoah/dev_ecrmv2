<div id="content">
	<script type="text/javascript" src="assets/js/j-tip.js?q=8"></script>
	
	<form name="project-total-form" onsubmit="return false;" style="clear:right; overflow:visible;">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

		<table border="0" cellpadding="0" cellspacing="0" style="width:1200px !important;" class="data-tbl dashboard-heads dataTable">
            <thead>
                <tr>
					<th width="90px;">Action</th>
					<th>Project No.</th>
					<th>Project ID</th>
					<th>Project Title</th>
					<th>Project Completion</th>
					
					<th>Project Type</th>
					<th>Planned Hours</th>
					<th>Billable Hours</th>
					<th>Internal Hours</th>
					<th>Non-Billable Hours</th>
					<th>Total Utilized Hours (Actuals)</th>
					<th>Effort Variance</th>
					<th>Project Value</th>
					<th>Utilization Cost</th>
					<th>P&L</th>
					<th>P&L %</th>
					<th>RAG Status</th>
					
					<th>Customer</th>
					<th>Project Manager</th>
					<th>Planned Start Date</th>
					<th>Planned End Date</th>					
					
					
					<th width="110px;">Project Status</th>
                </tr>
            </thead>
            
            <tbody>
				<?php
					if (is_array($pjts_data) && count($pjts_data) > 0) {
				?>
                    <?php
						foreach ($pjts_data as $record) {
							$timsheetData = $this->project_model->get_timesheet_hours($record['lead_id']);
					?>
							<tr>
								<td class="actions" align="center">
									<a href="project/view_project/<?php echo $record['lead_id'] ?>">View &raquo;</a>
									<?php
										if($this->session->userdata('delete')==1) { 
										$tle = str_replace("'", "\'", $record['lead_title']);
									?>
										| <a class="delete" href="javascript:void(0)" onclick="return deleteProject(<?php echo $record['lead_id']; ?>, '<?php echo $tle; ?>'); return false; "> Delete &raquo; </a> 
									<?php } ?>
								</td>
								<td class="actions">
									<div>
										<a style="color:#A51E04; text-decoration:none;" href="project/view_project/<?php echo  $record['lead_id'] ?>"><?php echo  $record['invoice_no'] ?></a>
									</div>
								</td>
								<td class="actions"><?php if (isset ($record['pjt_id'])) { echo $record['pjt_id']; } else { echo "-"; } ?></td>
								<td class="actions"><?php echo character_limiter($record['lead_title'], 35) ?></td>
								<td class="actions" align="center"><?php if (isset($record['complete_status'])) echo ($record['complete_status']) . " %"; else echo "-"; ?></td>
								<td class="actions" align="center">
									<?php 
										if($record['project_type'] =='1'){
											echo 'Fixed';
										}elseif($record['project_type'] =='2'){
											 echo 'Internal';
										}elseif($record['project_type'] =='3'){
											echo 'T&amp;M';
										}else{
											echo '-';
										}
									?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($record['estimate_hour'])) echo ($record['estimate_hour']/8); else echo "-"; ?>
								</td>
								
								
								<td class="actions" align="center">
									<?php if (isset($timsheetData->billable)) echo $timsheetData->billable; else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($timsheetData->internal)) echo $timsheetData->internal; else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($timsheetData->nonbillable)) echo $timsheetData->nonbillable; else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php echo ($timsheetData->billable+$timsheetData->internal)-$timsheetData->nonbillable; ?>
								</td>
								<td class="actions" align="center">
									<?php echo $timsheetData->total_hour-($record['estimate_hour'])/8; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($record['expect_worth_amount'])) echo $record['expect_worth_amount']; else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($timsheetData->cost)) echo $timsheetData->cost; else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php echo ($record['expect_worth_amount']-$timsheetData->cost); ?>
								</td>
								<td class="actions" align="center">
									<?php echo ($record['expect_worth_amount']-$timsheetData->cost)/$record['expect_worth_amount']; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($record['rag_status'])){ if($record['rag_status'] =='1') echo 'Red'; elseif($record['rag_status'] =='2') echo 'Amber';elseif($record['rag_status'] =='3') echo 'Green'; else echo '-';}else echo "-"; ?>
								</td>
								
								
						
								<td class="cust-data">
									<span>
										<?php echo $record['cfname'] . ' ' . $record['clname']; ?>
									</span> 
									<?php echo " - " . $record['company'] ?>
								</td>
								<td class="cust-data"><?php echo $record['fnm'] . ' ' . $record['lnm']; ?></td>
								<td><?php if ($record['date_start'] == "") { echo "-"; } else { echo  date('d-m-Y', strtotime($record['date_start'])); } ?></td>
								<td><?php if ($record['date_due'] == "") echo "-"; else echo  date('d-m-Y', strtotime($record['date_due'])) ?></td>
								
								
								<td class="actions" align="center">
									<?php
										switch ($record['pjt_status'])
										{
											case 1:
												$pjtstat = '<span class=label-wip>Project In Progress</span>';
											break;
											case 2:
												$pjtstat = '<span class=label-success>Project Completed</span>';
											break;
											case 3:
												$pjtstat = '<span class=label-warning>Project Onhold</span>';
											break;
											case 4:
												$pjtstat = '<span class=label-inactive>Inactive</span>';
											break;
										}
										echo $pjtstat;
									?>
								</td>
							</tr>
					<?php
						}
					}
					?>
            </tbody>
        </table>
	</form>

</div>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript">
$(function() {
	dtPjtTable();
});	
	
function dtPjtTable() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,
		"bDestroy": true
	});
}
</script>

