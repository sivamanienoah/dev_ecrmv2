<table cellspacing="0" cellpadding="0" border="0" id='it_services_dash' style="width:80%" class="data-tbl dashboard-heads dataTable it_cost_grid">
	<thead>
		<tr>
		
			<th>Practice</th>
			<th>Revenue Cost (USD)</th>
			<th>Total Cost (USD)</th>
			<th>Contribution %</th>
		
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($practice_data)) { ?>
		<?php foreach($practice_data as $prac) { ?>
			<?php if($prac->id != 7 && $prac->id != 13) { ?>
				<?php $practice_arr[] = $prac->practices; ?>
				<?php $practice_id_arr[$prac->practices] = $prac->id; ?>
				<tr>
					<td>
						<a onclick="getData('<?php echo $practice_id_arr[$prac->practices]; ?>', 'dc_value'); return false;"><?php echo $prac->practices; ?></a>
					</td>
					<td>
						<?php 
							if($prac->practices == 'Others') {
								$infra_irval 	= isset($dashboard_det['Infra Services']['ytd_billing']) ? $dashboard_det['Infra Services']['ytd_billing'] : 0;
								$other_irval 	= isset($dashboard_det['Others']['ytd_billing']) ? $dashboard_det['Others']['ytd_billing'] : 0;
								$irvalProjects 	= $infra_irval + $other_irval;
								$irvalProjects 	= isset($irvalProjects) ? $irvalProjects : '';
								$irval 			= isset($irvalProjects) ? round($irvalProjects) : '-';
							} else {
								$irval 			= ($dashboard_det[$prac->practices]['ytd_billing']!='-') ? round($dashboard_det[$prac->practices]['ytd_billing']) : '-';
							}
							if($irval!="-") {
								echo $irval;
							} else {
								echo '-';
							}
						?>
					</td>
					<td>
						<?php 
							if($prac->practices == 'Others') {
								$infra_dc_value = isset($dashboard_det['Infra Services']['ytd_utilization_cost']) ? $dashboard_det['Infra Services']['ytd_utilization_cost'] : 0;
								$other_dc_value	= isset($dashboard_det['Others']['ytd_utilization_cost']) ? $dashboard_det['Others']['ytd_utilization_cost'] : 0;
								$dc_value_Projects 	= $infra_dc_value + $other_dc_value;
								$dc_value_Projects 	= isset($dc_value_Projects) ? $dc_value_Projects : '';
								$dc_value 			= isset($dc_value_Projects) ? round($dc_value_Projects) : '-';
							} else {
								$dc_value = ($dashboard_det[$prac->practices]['ytd_utilization_cost']!='-') ? round($dashboard_det[$prac->practices]['ytd_utilization_cost']) : '-';
							}
							if($dc_value!="-") {
								echo $dc_value; 
							} else {
								echo '-';
							}
						?>
					</td>
						<?php
							$dc_val = ($dashboard_det[$prac->practices]['ytd_contribution']!='-') ? round($dashboard_det[$prac->practices]['ytd_contribution']) : '-';
							$arrow_val = 'down_arrow';
							if(round($dc_val, 0) >= 45){
								$arrow_val = 'up_arrow';
							}
							if($dc_val!='-') {
								if($prac->practices=='Infra Services') { 
									$dc_val = '-';
								} else {
									$dc_val = round($dc_val, 0); 
								}
							} else {
								$dc_val = '-';
							}
						?>
					<td>
						<?php echo $dc_val; ?>
					</td>
				</tr>
				
			<?php }	?>
		<?php } ?>
			<tfoot><tr>
				<td align='right'><strong>Total</strong></td>
				<td><?php echo ($dashboard_det['Total']['ytd_billing']!='-') ? round($dashboard_det['Total']['ytd_billing']) : '-'; ?></td>
				<td><?php echo ($dashboard_det['Total']['ytd_utilization_cost']!='-') ? round($dashboard_det['Total']['ytd_utilization_cost']) : '-'; ?> </td>
				<td><?php echo ($dashboard_det['Total']['ytd_contribution']!='-') ? round($dashboard_det['Total']['ytd_contribution']) : '-'; ?></td>
			</tr></tfoot></tbody>
	<?php } ?>
</table>
<script>
$('#it_services_dash').dataTable({
	"bInfo": false,
	"bFilter": false,
	"bPaginate": false,
	"bProcessing": false,
	"bServerSide": false,
	"bLengthChange": false,
	"bDestroy": true,
	'bAutoWidth': true,
	/* "aoColumnDefs": [
		{ 'type': 'numeric-comma', 'aTargets': [ 3 ] }
	] */
});
</script>