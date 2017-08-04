<table cellspacing="0" cellpadding="0" border="0" class="data-table proj-dash-table bu-tbl">
	<tr>
		<thead>
			<th>IT Services Dashboard</th>
			<?php if(!empty($practice_data)) { ?>
				<?php foreach($practice_data as $prac) { ?>
					<th><?php echo $prac->practices; ?></th>
					<?php $practice_arr[] = $prac->practices; ?>
					<?php $practice_id_arr[$prac->practices] = $prac->id; ?>
				<?php } ?>
			<?php } ?>
		</thead>
	</tr>
	<tr>
		<td><b>Number of Projects currently running</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						$noProjects = isset($projects['practicewise'][$parr]) ? $projects['practicewise'][$parr] : '';
						if(isset($noProjects)) {
						?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'noprojects'); return false;"><?php echo $noProjects; ?></a>
						<?php
						} else {
							echo '';
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Number of projects in Red</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						$rag = isset($projects['rag_status'][$parr]) ? $projects['rag_status'][$parr] : '';
						if(isset($rag)) {
						?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'rag'); return false;"><?php echo $rag; ?></a>
						<?php
						} else {
							echo '';
						}  
					?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>YTD Billing (USD) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						$irval = isset($projects['irval'][$parr]) ? round($projects['irval'][$parr]) : '';
						if(isset($irval) && ($irval != 0)) {
						?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'irval'); return false;"><?php echo $irval; ?></a>
						<?php
						} else {
							echo '';
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Billable for the month (%) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						$cm_billval = '';
						// if(isset($projects['billable_month'][$parr]['Billable']['hour']) && isset($projects['billable_month'][$parr]['totalhour']))
						$cm_billval = ($projects['billable_month'][$parr]['Billable']['hour'] - $projects['billable_month'][$parr]['totalhour'])/$projects['billable_month'][$parr]['totalhour'];
						if(isset($cm_billval) && ($cm_billval != 0)) {
						?>					
						<!--a onclick="getData('<?php #echo $practice_id_arr[$parr]; ?>', 'cmirval'); return false;"><?php #echo round(($cm_billval*100), 2); ?></a-->
						<?php echo round(($cm_billval*100), 2); ?>
						<?php
						} else {
							echo '';
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Billable YTD (%) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
						$billval = '';
						if(isset($projects['billable_ytd'][$parr]['Billable']['hour']) && isset($projects['billable_ytd'][$parr]['totalhour']))
						$billval = ($projects['billable_ytd'][$parr]['Billable']['hour']-$projects['billable_ytd'][$parr]['totalhour'])/$projects['billable_ytd'][$parr]['totalhour'];
						if(isset($billval) && ($billval != 0)) {
						?>
						<!--a onclick="getData('<?php #echo $practice_id_arr[$parr]; ?>', 'cmirval'); return false;"><?php #echo round(($billval*100), 2); ?></a-->
						<?php echo round(($billval*100), 2); ?>
						<?php
						} else {
							echo '';
						}
					?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Effort Variance (%) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php
					$eff_var = '';
					if(isset($projects['eff_var'][$parr])) {
						$eff_var = (($projects['eff_var'][$parr]['total_actual_hrs'] - $projects['eff_var'][$parr]['tot_estimate_hrs'])/$projects['eff_var'][$parr]['tot_estimate_hrs'])*100;
					}
					echo round($eff_var, 2);
					?>
				</td>

			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Contribution for the month (%) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php #echo isset($projects['cm_dc'][$parr]) ? round(($projects['cm_dc'][$parr]/$totCM_DC)*100, 2) : ''; 
						#((total invoice raised - total direct cost)/total invoice raised)*100;
						$cm_dc_val = '';
						if(isset($projects['cm_irval'][$parr]) && isset($projects['cm_direct_cost'][$parr]['total_cm_direct_cost'])) {
							$cm_dc_val = (($projects['cm_irval'][$parr] - $projects['cm_direct_cost'][$parr]['total_cm_direct_cost'])/$projects['cm_irval'][$parr]) * 100;
						}
						echo round($cm_dc_val, 2);
					?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Contribution YTD (%) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php 
						// ((total invoice raised - total direct cost)/total invoice raised)*100
						$dc_val = '';
						if(isset($projects['irval'][$parr]) && isset($projects['direct_cost'][$parr]['total_direct_cost'])) {
							$dc_val = (($projects['irval'][$parr] - $projects['direct_cost'][$parr]['total_direct_cost'])/$projects['irval'][$parr]) * 100;
						}
						echo round($dc_val, 2);
					?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
</table>
