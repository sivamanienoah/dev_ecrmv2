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
					$ragStatus = '<span class=label-red></span>';
					$rag_color = '#c0504d';
				break;
				case 2:
					$ragStatus = '<span class=label-amber></span>';
					$rag_color = '#ff7e00';
				break;
				case 3:
					$ragStatus = '<span class=label-green></span>';
					$rag_color = '#468847';
				break;
				default:
					$ragStatus = "-";
					$rag_color = '';
			}
			
			$bill_type = ($record['billing_type'] != 0) ? $record['billing_type'] : 1;
			
			if($bill_type == 1) {
				$milestone_content .= '<tr bgcolor='.$rag_color.'>';
				$milestone_content .= "<td class='actions' align='center'>";
				$milestone_content .= "<a title='View' href='project/view_project/".$record['lead_id']."'><img src='assets/img/view.png' alt='view'></a>";
				if($this->session->userdata('delete')==1) {
				$milestone_content .= "<a title='Delete' class='delete' href='javascript:void(0)' onclick='return deleteProject(".$record['lead_id']."); return false;'><img src='assets/img/trash.png' alt='delete' ></a>";
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
				$monthly_content .= "<a title='View' href='project/view_project/".$record['lead_id']."'><img src='assets/img/view.png' alt='view' ></a>";
				if($this->session->userdata('delete')==1) {
				$monthly_content .= "<a title='Delete' class='delete' href='javascript:void(0)' onclick='return deleteProject(".$record['lead_id']."); return false;'><img src='assets/img/trash.png' alt='delete' ></a>";
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
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" width="100%">
	<thead>
		<tr>
			<th>Action</th>
			<th>Title</th>
			<th title="Completion Percentage">CP % </th>
			<th title="Project Type">PT</th>
			<th title="RAG Status">RAG</th>
			<th title="Planned Hour">PH</th>
			<th title="Billable Hour">BH</th>
			<th title="Internal Hour">IH</th>
			<th title="Non-Billable Hour">NBH</th>
			<th title="Total Utilized Hours">TUH</th>
			<th title="Effort Variance">EV</th>
			<th title="Project Value">PV(<?php echo $default_cur_name; ?>)</th>
			<th title="Utilization Cost">UC(<?php echo $default_cur_name; ?>)</th>
			<th title="P&L">P&L </th>
			<th title="P&L %">P&L % </th>
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
			<th>Title</th>
			<th title="Completion Percentage">CP%</th>
			<th title="Project Type">PT</th>
			<th title="RAG Status">RAG</th>
			<th title="Planned Hour">PH</th>
			<th title="Billable Hour">BH</th>
			<th title="Internal Hour">IH</th>
			<th title="Non-Billable Hour">NBH</th>
			<th title="Total Utilized Hours">TUH</th>
			<th title="Project Value">PV</th>
			<th title="Utilization Cost">UC(<?php echo $default_cur_name; ?>)</th>
			<th title="P&L">P&L </th>
			<th title="P&L %">P&L %</th>
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

