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
	$monthly_content   = '';
	
	if (is_array($pjts_data) && count($pjts_data) > 0) {
		$total_pv_amt = 0;
		$total_uc_amt = 0;
		$total_pl_amt = 0;
		foreach($pjts_data as $record){
			$title		   = character_limiter($record['lead_title'], 30);
			$complete_stat = (isset($record['complete_status'])) ? ($record['complete_status']) . ' %' : '-';
 			$project_type  = ($record['project_type']!=null) ? $record['project_type'] : '-';
			$estimate_hour = (($record['estimate_hour'])) ? $record['estimate_hour'] : '-';
			$bill_hr 	   = (isset($record['bill_hr'])) ? (round($record['bill_hr'])) : '-';
			$int_hr 	   = (isset($record['int_hr'])) ? (round($record['int_hr'])) : '-';
			$nbil_hr 	   = (isset($record['nbil_hr'])) ? (round($record['nbil_hr'])) : '-';
			$total_hours   = (isset($record['total_hours'])) ? (round($record['total_hours'])) : '-';
			$eff_variance  = round($total_hours-$estimate_hour);
			$actual_amt    = (isset($record['actual_worth_amt'])) ? (round($record['actual_worth_amt'])) : '0';
			$total_cost    = (isset($record['total_cost'])) ? (round($record['total_cost'])) : '0';
			$profitloss    = round($record['actual_worth_amt']-$total_cost);
			$profitlossPercent = round(($profitloss/$record['actual_worth_amt'])*100);
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
			
			$bill_type = $record['billing_type'];
			
			$monthly_content .= "<tr>";
			$monthly_content .= "<td class='actions' align='center'>";
			$monthly_content .= "<a title='View' href='project/view_project/".$record['lead_id']."'><img src='assets/img/view.png' alt='view' ></a> ";
			if($this->session->userdata('delete')==1) {
			$monthly_content .= "<a title='Delete' class='delete' href='javascript:void(0)' onclick='return deleteProject(".$record['lead_id']."); return false;'><img src='assets/img/trash.png' alt='delete' ></a>";
			}
			$monthly_content .= "</td>";
			$monthly_content .= "<td><a href='project/view_project/".$record['lead_id']."'>".$title."</a></td>";
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
			$monthly_content .= "<td>".$profitlossPercent." %</td>";
			$monthly_content .= "</tr>";
				$total_pv_amt += $actual_amt;
				$total_uc_amt += $total_cost;
				$total_pl_amt += $profitloss;

			$complete_stat = $project_type = $estimate_hour = '';
		}
	}
?>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%" class="data-tbl dashboard-heads dataTable" id='monthly-data'>
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
	
	<tfoot>
					<tr>
						<td colspan='10' align='right'><strong>Total: </strong></td>
						
						<td><?php echo $total_pv_amt; ?></td>
						<td><?php echo $total_uc_amt; ?></td>
						<td><?php echo $total_pl_amt; ?></td>
						<td></td>
						
					</tr>
				</tfoot>
</table>
<script type="text/javascript">
$(function() {
	dtPjtTable1();
});
function dtPjtTable1() {
	$('#monthly-data').dataTable({
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