<div id="content">
	<script type="text/javascript" src="assets/js/j-tip.js?q=8"></script>
	
	<form name="project-total-form" onsubmit="return false;" style="clear:right; overflow:visible;">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

		<table border="0" cellpadding="0" cellspacing="0" style="width:1200px !important;" class="data-table">
            <thead>
                <tr>
					<th width="60">Action</th>
					<th width="50">Project No.</th>
					<th width="70">Project ID</th>
					<th width="120">Project Title</th>
					<th width="120" class="cust-data">Customer</th>
					<th width="120">Project Manager</th>
					<th width="60">Planned Start Date</th>
					<th width="60">Planned End Date</th>					
					<th width="40">Project Completion</th>
					<th width="90">Project Status</th>
                </tr>
            </thead>
            
            <tbody>
				<?php
					if (is_array($pjts_data) && count($pjts_data) > 0) { 
				?>
                    <?php
						foreach ($pjts_data as $record) {
					?>
							<tr>
								<td class="actions" align="center">
									<a href="project/view_project/<?php echo  $record['jobid'], '/', $quote_section ?>">View</a>
									<?php
									echo ($this->session->userdata('deletePjt') == 1) ? ' | <a href="project/delete_quote/' . $record['jobid'] . $list_location . '" onclick="return window.confirm(\'Are you sure you want to delete\n' . str_replace("'", "\'", $record['job_title']) . '?\n\nThis will delete all the items\nand logs attached to this job.\');">Delete</a>' : ''; 
									?>
								</td>
								<td class="actions">
									<div>
										<a style="color:#A51E04; text-decoration:none;" href="project/view_project/<?php echo  $record['jobid'], '/', $quote_section ?>"><?php echo  $record['invoice_no'] ?></a>
									</div>
								</td>
								<td class="actions"><?php if (isset ($record['pjt_id'])) { echo $record['pjt_id']; } else { echo "-"; } ?></td>
								<td class="actions"><?php echo character_limiter($record['job_title'], 35) ?></td>
								<td class="cust-data">
									<span>
										<?php echo $record['cfname'] . ' ' . $record['clname']; ?>
									</span> 
									<?php echo " - " . $record['company'] ?>
								</td>
								<td class="cust-data"><?php echo $record['fnm'] . ' ' . $record['lnm']; ?></td>
								<td><?php if ($record['date_start'] == "") { echo "-"; } else { echo  date('d-m-Y', strtotime($record['date_start'])); } ?></td>
								<td><?php if ($record['date_due'] == "") echo "-"; else echo  date('d-m-Y', strtotime($record['date_due'])) ?></td>
								<td class="actions" align="center"><?php if (isset($record['complete_status'])) echo ($record['complete_status']) . " %"; else echo "-"; ?></td>
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
					} else { 
					?>
							<tr><td colspan="10" align="center">No records available to be displayed!</td></tr>
				<?php 
					} 
				?>
            </tbody>
        </table>
	</form>

</div>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']});
 $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
</script>

