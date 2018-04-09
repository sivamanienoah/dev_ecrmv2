<table cellspacing="0" cellpadding="0" border="0" id='it_services_dash' class="data-table proj-dash-table bu-tbl">
	<tr>
		<thead>
			<th>Practice</th>
			<th>Revenue Cost</th>
			<th>Total Cost</th>
			<th>Contribution %</th>
		</thead>
	</tr>
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
					<td>
							<?php
							$dc_val = ($dashboard_det[$prac->practices]['ytd_contribution']!='-') ? round($dashboard_det[$prac->practices]['ytd_contribution']) : '-';
							$arrow_val = 'down_arrow';
							if(round($dc_val, 0) >= 45){
								$arrow_val = 'up_arrow';
							}
							if($dc_val!='-'){
							?>
								<?php if($prac->practices=='Infra Services') { 
									echo '-';
								} else {
								?>
								<span class="<?php echo "itser_".$arrow_val;?>">
									<?php echo round($dc_val, 0); ?>
								</span>
								<?php 
								}
							} else {
								echo '-';
							}
						?>
					</td>
				</tr>
				
			<?php }	?>
		<?php } ?>
			<tr>
				<td align='right'><strong>Total</strong></td>
				<td><?php echo ($dashboard_det['Total']['ytd_billing']!='-') ? round($dashboard_det['Total']['ytd_billing']) : '-'; ?></td>
				<td><?php echo ($dashboard_det['Total']['ytd_utilization_cost']!='-') ? round($dashboard_det['Total']['ytd_utilization_cost']) : '-'; ?> </td>
				<td><?php echo ($dashboard_det['Total']['ytd_contribution']!='-') ? round($dashboard_det['Total']['ytd_contribution']) : '-'; ?></td>
			</tr>
	<?php } ?>
</table>