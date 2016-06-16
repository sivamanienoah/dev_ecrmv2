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

$td_chk = false;
$td_style = '';
if(!empty($db_fields) && count($db_fields)>0){
	$td_chk = true;
	$td_cn = $td_cp = $td_pt = $td_rag = $td_ph = $td_bh = $td_ih = $td_nbh = $td_tuh = $td_ev = $td_pv = $td_uc = $td_dc = $td_ir = $td_contrib = $td_pl = $td_plp = 'style="display: none;"';
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
			$customer_name = character_limiter($record['customer_name'], 30);
			$complete_stat = (isset($record['complete_status'])) ? ($record['complete_status']) . ' %' : '-';
 			$project_type  = ($record['project_type']!=null) ? $record['project_type'] : '-';
			$estimate_hour = (($record['estimate_hour'])) ? $record['estimate_hour'] : '-';
			$bill_hr 	   = (isset($record['bill_hr'])) ? (round($record['bill_hr'])) : '-';
			$int_hr 	   = (isset($record['int_hr'])) ? (round($record['int_hr'])) : '-';
			$nbil_hr 	   = (isset($record['nbil_hr'])) ? (round($record['nbil_hr'])) : '-';
			$total_hours   = (isset($record['total_hours'])) ? (round($record['total_hours'])) : '-';
			$total_amount_inv_raised   = (isset($record['total_amount_inv_raised'])) ? ($record['total_amount_inv_raised']) : '-';
			$eff_variance  = round($total_hours-$estimate_hour);
			$actual_amt    = (isset($record['actual_worth_amt'])) ? (round($record['actual_worth_amt'])) : '0';
			$total_cost    = (isset($record['total_cost'])) ? (round($record['total_cost'])) : '0';
			$total_dc_hours = (isset($record['total_dc_hours'])) ? (round($record['total_dc_hours'])) : '0';
			$contributePercent = round((($total_amount_inv_raised-$total_dc_hours)/$total_amount_inv_raised)*100);
			//$profitloss    = round($record['actual_worth_amt']-$total_cost);
			//$profitlossPercent = round(($profitloss/$record['actual_worth_amt'])*100);
			$profitloss    = round($total_amount_inv_raised-$total_cost);
			$profitlossPercent = round(($profitloss/$total_amount_inv_raised)*100);
			
			
			if( round($profitlossPercent, 0) <= 0 )
			$prof_clr = 'orange-clr';
			if((round($profitlossPercent, 0)>=1) && (round($profitlossPercent, 0)<=44))
			$prof_clr = 'red-clr';
			if(round($profitlossPercent, 0) >= 45)
			$prof_clr = 'green-clr';
		
			if( round($contributePercent, 0) <= 0 )
			$contri_clr = 'orange-clr';
			if((round($contributePercent, 0)>=1) && (round($contributePercent, 0)<=44))
			$contri_clr = 'red-clr';
			if(round($contributePercent, 0) >= 45)
			$contri_clr = 'green-clr';			
			
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
			if($td_chk == false) {
				$monthly_content .= "<td>".$customer_name."</td>";
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
				$monthly_content .= "<td>".$total_dc_hours."</td>";
				$monthly_content .= "<td>".$total_amount_inv_raised."</td>";
				$monthly_content .= "<td><span class=".$contri_clr.">".$contributePercent." %</span></td>";
				$monthly_content .= "<td>".$profitloss."</td>";
				$monthly_content .= "<td><span class=".$prof_clr.">".$profitlossPercent." %</span></td>";
			} else {
				if(($td_cn == true) && in_array('CN', $db_fields)) { $td_cn = 'style="display: table-cell;"'; }
				$milestone_content .= "<td ".$td_cn.">".$customer_name."</td>";
				if(($td_chk == true) && in_array('CP', $db_fields)) { $td_cp = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_cp.">".$complete_stat."</td>";
				if(($td_chk == true) && in_array('PT', $db_fields)) { $td_pt = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_pt.">".$project_type."</td>";
				if(($td_chk == true) && in_array('RAG', $db_fields)) { $td_rag = 'style="display: table-cell;"'; }		
				$monthly_content .= "<td ".$td_rag.">".$ragStatus."</td>";
				if(($td_chk == true) && in_array('PH', $db_fields)) { $td_ph = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_ph.">".$estimate_hour."</td>";
				if(($td_chk == true) && in_array('BH', $db_fields)) { $td_bh = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_bh.">".$bill_hr."</td>";
				if(($td_chk == true) && in_array('IH', $db_fields)) { $td_ih = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_ih.">".$int_hr."</td>";
				if(($td_chk == true) && in_array('NBH', $db_fields)) { $td_nbh = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_nbh.">".$nbil_hr."</td>";
				if(($td_chk == true) && in_array('TUH', $db_fields)) { $td_tuh = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_tuh.">".$total_hours."</td>";
				if(($td_chk == true) && in_array('PV', $db_fields)) { $td_pv = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_pv.">".$actual_amt."</td>";
				if(($td_chk == true) && in_array('UC', $db_fields)) { $td_uc = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_uc.">".$total_cost."</td>";
				if(($td_chk == true) && in_array('DC', $db_fields)) { $td_dc = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_dc.">".$total_dc_hours."</td>";
				if(($td_chk == true) && in_array('IR', $db_fields)) { $td_ir = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_ir.">".$total_amount_inv_raised."</td>";
				if(($td_chk == true) && in_array('Contribution %', $db_fields)) {$td_contrib = 'style="display: table-cell;"';}
				$monthly_content .= "<td ".$td_contrib."><span class=".$contri_clr.">".$contributePercent." %</span></td>";
				if(($td_chk == true) && in_array('P&L', $db_fields)) { $td_pl = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_pl.">".$profitloss."</td>";
				if(($td_chk == true) && in_array('P&L %', $db_fields)) { $td_plp = 'style="display: table-cell;"'; }
				$monthly_content .= "<td ".$td_plp."><span class=".$prof_clr.">".$profitlossPercent." %</span></td>";
			}
			$monthly_content .= "</tr>";
				$full_total_amount_inv_raised += $total_amount_inv_raised;
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
				<?php if($td_chk == false) { ?>
					<th title="Customer Name">Customer Name</th>
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
					<th title="Utilization Cost">DC(<?php echo $default_cur_name; ?>)</th>
					<th title="Invoice Raised">IR(<?php echo $default_cur_name; ?>)</th>
					<th title="Invoice Raised">Contribution %</th>
					<th title="P&L">P&L </th>
					<th title="P&L %">P&L %</th>
				<?php } else { ?>
					<th <?php echo $td_cn; ?> title="Customer Name">Customer Name</th>
					<th <?php echo $td_cp; ?> title="Completion Percentage">CP%</th>
					<th <?php echo $td_pt; ?> title="Project Type">PT</th>				
					<th <?php echo $td_rag; ?> title="RAG Status">RAG</th>
					<th <?php echo $td_ph; ?> title="Planned Hour">PH</th>
					<th <?php echo $td_bh; ?> title="Billable Hour">BH</th>
					<th <?php echo $td_ih; ?> title="Internal Hour">IH</th>
					<th <?php echo $td_nbh; ?> title="Non-Billable Hour">NBH</th>
					<th <?php echo $td_tuh; ?> title="Total Utilized Hours">TUH</th>
					<th <?php echo $td_pv; ?> title="Project Value">PV</th>
					<th <?php echo $td_uc; ?> title="Utilization Cost">UC(<?php echo $default_cur_name; ?>)</th>
					<th <?php echo $td_dc; ?> title="Direct Cost">DC(<?php echo $default_cur_name; ?>)</th>
					<th <?php echo $td_ir; ?> title="Invoice Raised">IR(<?php echo $default_cur_name; ?>)</th>
					<th <?php echo $td_contrib; ?> title="Contribution Percentage">Contribution %</th>
					<th <?php echo $td_pl; ?> title="P&L">P&L </th>
					<th <?php echo $td_plp; ?> title="P&L %">P&L %</th>
				<?php } ?>
			</tr>
		</thead>
	<tbody>
		<?php echo $monthly_content; ?>
	</tbody>
	
	<!--tfoot>
					<tr>
						<td colspan='10' align='right'><strong>Total: </strong></td>
						
						<td><?php #echo $total_pv_amt; ?></td>
						<td><?php #echo $total_uc_amt; ?></td>
						<td><?php #echo $full_total_amount_inv_raised; ?></td>
						<td><?php #echo $total_pl_amt; ?></td>
						<td></td>
						
					</tr>
				</tfoot-->
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