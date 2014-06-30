<div id="content">
	<script type="text/javascript" src="assets/js/j-tip.js?q=8"></script>
	
	<form name="project-total-form" onsubmit="return false;" style="clear:right; overflow:visible;">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

		<table border="0" cellpadding="0" cellspacing="0" style="width:1250px !important;" class="data-tbl dashboard-heads dataTable">
            <thead>
                <tr>
					<th width="82px;">Action</th>
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
					<th>P&L </th>
					<th>P&L %</th>
					<th>RAG Status</th>
                </tr>
            </thead>
            
            <tbody>
				<?php
					if (is_array($pjts_data) && count($pjts_data) > 0) {
						foreach ($pjts_data as $record) {
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
										<a style="color:#A51E04; text-decoration:none;" href="project/view_project/<?php echo $record['lead_id'] ?>"><?php echo character_limiter($record['lead_title'], 35); ?></a>
									</div>
								</td>
								<td class="actions" align="center"><?php if (isset($record['complete_status'])) echo ($record['complete_status']) . " %"; else echo "-"; ?></td>
								<td class="actions" align="center">
									<?php
										if(isset($record['project_type'])) {
											switch ($record['project_type']) {
												case 1:
													echo 'Fixed';
												break;
												case 2:
													echo 'Internal';
												break;
												case 3:
													echo 'T&amp;M';
												break;
												default:
													echo "-";
											}
										} else {
											echo "-";
										}
									?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($record['estimate_hour'])) echo ($record['estimate_hour']); else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($record['bill_hr'])) echo sprintf('%0.2f',$record['bill_hr']); else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($record['int_hr'])) echo sprintf('%0.2f',$record['int_hr']); else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($record['nbil_hr'])) echo sprintf('%0.2f',$record['nbil_hr']); else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php echo ($record['bill_hr']+$record['int_hr']+$record['nbil_hr']); ?>
								</td>
								<td class="actions" align="center">
									<?php echo ($record['bill_hr']+$record['int_hr']+$record['nbil_hr'])-$record['estimate_hour']; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($record['actual_worth_amt'])) echo $record['actual_worth_amt']; else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php if (isset($record['total_cost'])) echo $record['total_cost']; else echo "-"; ?>
								</td>
								<td class="actions" align="center">
									<?php echo ($record['actual_worth_amt']-$record['total_cost']); ?>
								</td>
								<td class="actions" align="center">
									<?php 
										$perc = ($record['actual_worth_amt']-$record['total_cost'])/$record['actual_worth_amt']; 
										echo sprintf('%0.2f',$perc);
									?>
								</td>
								<td class="actions" align="center">
									<?php 
										if (isset($record['rag_status'])) {
											switch ($record['rag_status'])
											{
												case 1:
													$ragStatus = '<span class=label-inactive>Red</span>';
												break;
												case 2:
													$ragStatus = '<span class=label-amber>Amber</span>';
												break;
												case 3:
													$ragStatus = '<span class=label-success>Green</span>';
												break;
												default:
													$ragStatus = "-";
											}
											echo $ragStatus;
										} else {
											echo "-";
										}
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
<!--script type="text/javascript" src="assets/js/tablesort.min.js"></script-->
<script type="text/javascript">
$(function() {
	dtPjtTable();
});	
	
function dtPjtTable() {
	$('.data-tbl').dataTable({
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

