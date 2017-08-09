<table cellspacing="0" cellpadding="0" border="0" id='it_services_dash' class="data-table proj-dash-table bu-tbl">
	<tr>
		<thead>
			<th>IT Services Dashboard</th>
			<?php if(!empty($practice_data)) { ?>
				<?php foreach($practice_data as $prac) { ?>
					<?php if($prac->id != 7 && $prac->id != 13) { ?>
						<?php $practice_arr[] = $prac->practices; ?>
						<?php $practice_id_arr[$prac->practices] = $prac->id; ?>
						<th><?php echo $prac->practices; ?></th>
					<?php }	?>
				<?php } ?>
			<?php } ?>
			<th>Total</th>
		</thead>
	</tr>
	<?php // echo "<pre>"; print_r($dashboard_det); echo "</pre>"; ?>
	<tr>
		<td><b>Number of Projects currently running</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						if($parr == 'Others') {
							$infraProjects  = isset($projects['practicewise']['Infra Services']) ? $projects['practicewise']['Infra Services'] : 0;
							$testinProjects = isset($projects['practicewise']['Testing']) ? $projects['practicewise']['Testing'] : 0;
							$otherProjects  = isset($projects['practicewise']['Others']) ? $projects['practicewise']['Others'] : 0;
							$noProjects = $infraProjects+$otherProjects+$testinProjects;
							$noProjects = isset($noProjects) ? $noProjects : '';
						} else {
							$noProjects = isset($projects['practicewise'][$parr]) ? $projects['practicewise'][$parr] : '';
						}
						if($noProjects!='') {
							$total_projects += $noProjects;
						?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'noprojects'); return false;"><?php echo $noProjects; ?></a>
						<?php
						} else {
							echo '-';
						}
					?>
				</td>							
			<?php } ?>
		<?php } ?>
		<td align='right'><?php echo ($total_projects!=0) ? $total_projects : '-'; ?></td>
	</tr>
	<tr>
		<td><b>Number of projects in Red</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						if($parr == 'Others') {
							$infraRAG   = isset($projects['rag_status']['Infra Services']) ? $projects['rag_status']['Infra Services'] : 0;
							$testingRAG = isset($projects['rag_status']['Testing']) ? $projects['rag_status']['Testing'] : 0;
							$otherRAG   = isset($projects['rag_status']['Others']) ? $projects['rag_status']['Others'] : 0;
							$ragProjects = $infraRAG+$otherRAG+$testingRAG;
							$rag = isset($ragProjects) ? $ragProjects : '';
						} else {
							$rag = isset($projects['rag_status'][$parr]) ? $projects['rag_status'][$parr] : '';
						}
						if($rag!='') {
							$total_rag += $rag;
						?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'rag'); return false;"><?php echo $rag; ?></a>
						<?php
						} else {
							echo '-';
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
		<td align='right'><?php echo ($total_rag!=0) ? $total_rag : '-'; ?></td>
	</tr>
	<tr>
		<td><b>Billing for the month (USD) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						if($parr == 'Others') {
							$infraCMB   = ($dashboard_det['Infra Services']['billing_month'] != '-') ? $dashboard_det['Infra Services']['billing_month'] : 0;
							$otherCMB   = ($dashboard_det['Others']['billing_month'] != '-') ? $dashboard_det['Others']['billing_month'] : 0;
							$CMBProjects = $infraCMB + $otherCMB;
							$CMBProjects = isset($CMBProjects) ? $CMBProjects : '';
							$cm_billing  = isset($CMBProjects) ? round($CMBProjects) : '-';
						} else {
							$cm_billing = ($dashboard_det[$parr]['billing_month']!='-') ? round($dashboard_det[$parr]['billing_month']) : '-';
						}
						
						if($cm_billing!='-') {
					?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'cm_billing'); return false;"><?php echo $cm_billing; ?></a>
					<?php
						} else {
							echo "-";
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
		<td align='right'><?php echo ($dashboard_det['Total']['billing_month']!='-') ? round($dashboard_det['Total']['billing_month']) : '-'; ?></td>
	</tr>
	<tr>
		<td><b>YTD Billing (USD) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						if($parr == 'Others') {
							$infra_irval 	= isset($dashboard_det['Infra Services']['ytd_billing']) ? $dashboard_det['Infra Services']['ytd_billing'] : 0;
							$other_irval 	= isset($dashboard_det['Others']['ytd_billing']) ? $dashboard_det['Others']['ytd_billing'] : 0;
							$irvalProjects 	= $infra_irval + $other_irval;
							$irvalProjects 	= isset($irvalProjects) ? $irvalProjects : '';
							$irval 			= isset($irvalProjects) ? round($irvalProjects) : '-';
						} else {
							$irval 			= ($dashboard_det[$parr]['ytd_billing']!='-') ? round($dashboard_det[$parr]['ytd_billing']) : '-';
						}
						if($irval!="-") {
						?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'irval'); return false;"><?php echo $irval; ?></a>
						<?php
						} else {
							echo '-';
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
		<td align='right'><?php echo ($dashboard_det['Total']['ytd_billing']!='-') ? round($dashboard_det['Total']['ytd_billing']) : '-'; ?></td>
	</tr>
	<tr>
		<td><b>YTD Utilization Cost (USD) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						if($parr == 'Others') {
							$infra_dc_value = isset($dashboard_det['Infra Services']['ytd_utilization_cost']) ? $dashboard_det['Infra Services']['ytd_utilization_cost'] : 0;
							$other_dc_value	= isset($dashboard_det['Others']['ytd_utilization_cost']) ? $dashboard_det['Others']['ytd_utilization_cost'] : 0;
							$dc_value_Projects 	= $infra_dc_value + $other_dc_value;
							$dc_value_Projects 	= isset($dc_value_Projects) ? $dc_value_Projects : '';
							$dc_value 			= isset($dc_value_Projects) ? round($dc_value_Projects) : '-';
						} else {
							$dc_value = ($dashboard_det[$parr]['ytd_utilization_cost']!='-') ? round($dashboard_det[$parr]['ytd_utilization_cost']) : '-';
						}
						
						if($dc_value!="-") {
						?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'dc_value'); return false;"><?php echo $dc_value; ?></a>
						<?php
						} else {
							echo '-';
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
		<td align='right'>
			<?php
				echo ($dashboard_det['Total']['ytd_utilization_cost']!='-') ? round($dashboard_det['Total']['ytd_utilization_cost']) : '-';
			?>
		</td>
	</tr>
	<tr>
		<td><b>Billable for the month (%) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						$cm_billval = ($dashboard_det[$parr]['billable_month']!='-') ? round($dashboard_det[$parr]['billable_month']) : '-';
						if($cm_billval!="-") {
						?>					
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'cm_eff'); return false;"><?php echo $cm_billval; ?></a>
						<?php
						} else {
							echo '-';
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
		<td align='right'>
			<?php echo ($dashboard_det['Total']['billable_month']!='-') ? round($dashboard_det['Total']['billable_month']) : '-'; ?>
		</td>
	</tr>
	<tr>
		<td><b>Billable YTD (%) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						$billval = ($dashboard_det[$parr]['ytd_billable']!='-') ? round($dashboard_det[$parr]['ytd_billable']) : '-';
						if($billval != '-') {
						?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'ytd_eff'); return false;"><?php echo $billval; ?></a>
						<?php
						} else {
							echo '-';
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
		<td align='right'>
			<?php
				echo ($dashboard_det['Total']['ytd_billable']!='-') ? round($dashboard_det['Total']['ytd_billable']) : '-';
			?>
		</td>
	</tr>
	<tr>
		<td><b>Effort Variance (%) - <span class="highlight_info">For Fixed Bid projects</span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php								
					$eff_var = ($dashboard_det[$parr]['effort_variance']!='-') ? round($dashboard_det[$parr]['effort_variance']) : '-';
					if(($eff_var != '-') && (($parr!='Infra Services'))) {
					?>
					<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'fixedbid'); return false;"><?php echo round($eff_var, 0); ?></a>
					<?php
					} else {
						echo '-';
					}
					?>
				</td>
			<?php } ?>
		<?php } ?>
		<td align='right'>
			<?php
				echo ($dashboard_det['Total']['effort_variance']!='-') ? round($dashboard_det['Total']['effort_variance']) : '-';
			?>
		</td>
	</tr>
	<tr>
		<td><b>Contribution for the month (%) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						$cm_dc_val = ($dashboard_det[$parr]['contribution_month']!='-') ? round($dashboard_det[$parr]['contribution_month']) : '-';
						echo ($cm_dc_val!='-') ? $cm_dc_val : '-';
					?>
				</td>
			<?php } ?>
		<?php } ?>
		<td align='right'>
			<?php
				echo ($dashboard_det['Total']['contribution_month']!='-') ? round($dashboard_det['Total']['contribution_month']) : '-';
			?>
		</td>
	</tr>
	<tr>
		<td><b>Contribution YTD (45 %) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						$dc_val = ($dashboard_det[$parr]['ytd_contribution']!='-') ? round($dashboard_det[$parr]['ytd_contribution']) : '-';
						$arrow_val = 'down_arrow';
						if(round($dc_val, 0) >= 45){
							$arrow_val = 'up_arrow';
						}
						if($dc_val!='-'){
						?>
							<?php if($parr=='Infra Services') { 
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
			<?php } ?>
		<?php } ?>
		<td align='right'>
			<?php
				echo ($dashboard_det['Total']['ytd_contribution']!='-') ? round($dashboard_det['Total']['ytd_contribution']) : '-';
			?>
		</td>
	</tr>
</table>
<div class="clearfix"></div>
<div class="service_dash_notes">
	<span class="red"> ** </span>Infra Services & Testing Practice Values are Merged With Others Practice.
</div>