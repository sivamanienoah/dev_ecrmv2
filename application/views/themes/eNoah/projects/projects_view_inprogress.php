<?php
$this->load->helper('custom_helper');
if (get_default_currency()) {
	$default_currency = get_default_currency();
	$default_cur_id = $default_currency['expect_worth_id'];
	$default_cur_name = $default_currency['expect_worth_name'];
} else {
	$default_cur_id = '1';
	$default_cur_name = 'USD';
}
?>

<?php
	$milestone_content = '';
	$monthly_content   = '';
	
	if (is_array($pjts_data) && count($pjts_data) > 0) {
		foreach($pjts_data as $record){
			$title		   = character_limiter($record['lead_title'], 30);
			$complete_stat = (isset($record['complete_status'])) ? ($record['complete_status']) . ' %' : '-';
 			$project_type  = ($record['project_type']!=null) ? $record['project_type'] : '-';
			$estimate_hour = (($record['estimate_hour'])) ? $record['estimate_hour'] : '-';
			$bill_hr 	   = (isset($record['bill_hr'])) ? (sprintf('%0.2f', $record['bill_hr'])) : '-';
			$int_hr 	   = (isset($record['int_hr'])) ? (sprintf('%0.2f', $record['int_hr'])) : '-';
			$nbil_hr 	   = (isset($record['nbil_hr'])) ? (sprintf('%0.2f', $record['nbil_hr'])) : '-';
			$total_hours   = (isset($record['total_hours'])) ? (sprintf('%0.2f', $record['total_hours'])) : '-';
			$eff_variance  = sprintf('%0.2f', $total_hours-$estimate_hour);
			$actual_amt    = (isset($record['actual_worth_amt'])) ? (sprintf('%0.2f', $record['actual_worth_amt'])) : '0.00';
			$total_cost    = (isset($record['total_cost'])) ? (sprintf('%0.2f', $record['total_cost'])) : '0.00';
			$profitloss    = sprintf('%0.2f', $record['actual_worth_amt']-$total_cost);
			$profitlossPercent = sprintf('%0.2f', ($profitloss/$record['actual_worth_amt']));
			switch ($record['rag_status']) {
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
			
			if($record['billing_type'] == 1) {
				$milestone_content .= "<tr>";
				$milestone_content .= "<td class='actions' align='center'>";
				$milestone_content .= "<a title='View' href='project/view_project/".$record['lead_id']."'><img src=assets/img/view.png alt='view'></a>";
				if($this->session->userdata('delete')==1) {
				$milestone_content .= "<a title='Delete' class='delete' href='javascript:void(0)' onclick='return deleteProject(".$record['lead_id']."); return false;'><img src=assets/img/trash.png alt='delete' height=15px;></a>";
				}
				$milestone_content .= "</td>";
				$milestone_content .= "<td>".$title."</td>";
				$milestone_content .= "<td>".$complete_stat."</td>";
				$milestone_content .= "<td>".$project_type."</td>";
				$milestone_content .= "<td>".$ragStatus."</td>";
				$milestone_content .= "<td>".$estimate_hour."</td>";
				$milestone_content .= "<td>".$bill_hr."</td>";
				$milestone_content .= "<td>".$int_hr."</td>";
				$milestone_content .= "<td>".$nbil_hr."</td>";
				$milestone_content .= "<td>".$total_hours."</td>";
				$milestone_content .= "<td>".$eff_variance."</td>";
				$milestone_content .= "<td>".$actual_amt."</td>";
				$milestone_content .= "<td>".$total_cost."</td>";
				$milestone_content .= "<td>".$profitloss."</td>";
				$milestone_content .= "<td>".$profitlossPercent."</td>";
				$milestone_content .= "</tr>";
			} else {
				$monthly_content .= "<tr>";
				$monthly_content .= "<td class='actions' align='center'>";
				$monthly_content .= "<a title='View' href='project/view_project/".$record['lead_id']."'><img src='assets/img/view.png' alt='view' height='16'></a>";
				if($this->session->userdata('delete')==1) {
				$monthly_content .= "<a title='Delete' class='delete' href='javascript:void(0)' onclick='return deleteProject(".$record['lead_id']."); return false;'><img src='assets/img/trash.png' alt='delete' height='15'></a>";
				}
				$monthly_content .= "</td>";
				$monthly_content .= "<td>".$title."</td>";
				$monthly_content .= "<td>".$complete_stat."</td>";
				$monthly_content .= "<td>".$project_type."</td>";
				$monthly_content .= "<td>".$ragStatus."</td>";
				$monthly_content .= "<td>".$estimate_hour."</td>";
				$monthly_content .= "<td>".$bill_hr."</td>";
				$monthly_content .= "<td>".$int_hr."</td>";
				$monthly_content .= "<td>".$nbil_hr."</td>";
				$monthly_content .= "<td>".$total_hours."</td>";
				$monthly_content .= "<td>".$actual_amt."</td>";
				$monthly_content .= "<td>".$total_cost."</td>";
				$monthly_content .= "<td>".$profitloss."</td>";
				$monthly_content .= "<td>".$profitlossPercent."</td>";
				$monthly_content .= "</tr>";
			}
			$complete_stat = $project_type = $estimate_hour = '';
		}
	}
?>
<h2>Milestone Based</h2>
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable">
	<thead>
		<tr>
			<th>Action</th>
			<th>Project Title</th>
			<th>Completion %</th>
			<th>Project Type</th>
			<th>RAG Status</th>
			<th>Planned Hours</th>
			<th>Billable Hours</th>
			<th>Internal Hours</th>
			<th>Non-Billable Hours</th>
			<th>Total Utilized Hours (Actuals)</th>
			<th>Effort Variance</th>
			<th>Project Value (<?php echo $default_cur_name; ?>)</th>
			<th>Utilization Cost (<?php echo $default_cur_name; ?>)</th>
			<th>P&L </th>
			<th>P&L %</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $milestone_content; ?>
	</tbody>
</table>

<div class="clear"></div>
<h2>Monthly Billing</h2>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%" class="data-tbl dashboard-heads dataTable">
	<thead>
		<tr>
			<th>Action</th>
			<th>Project Title</th>
			<th>Completion %</th>
			<th>Project Type</th>
			<th>RAG Status</th>
			<th>Planned Hours</th>
			<th>Billable Hours</th>
			<th>Internal Hours</th>
			<th>Non-Billable Hours</th>
			<th>Total Utilized Hours (Actuals)</th>
			<th>Project Value (<?php echo $default_cur_name; ?>)</th>
			<th>Utilization Cost (<?php echo $default_cur_name; ?>)</th>
			<th>P&L </th>
			<th>P&L %</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $monthly_content; ?>
	</tbody>
</table>
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

