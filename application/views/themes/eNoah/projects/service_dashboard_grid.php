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
					<?php
						$total_irval += isset($projects['irval'][$parr]) ? round($projects['irval'][$parr]) : 0;
						$totCM_Irval += isset($projects['cm_irval'][$parr]) ? $projects['cm_irval'][$parr] : '';
						$totEV += isset($projects['eff_var'][$parr]) ? $projects['eff_var'][$parr] : '';
						$totDC += isset($projects['dc'][$parr]) ? $projects['dc'][$parr] : '';
						$totCM_DC += isset($projects['cm_dc'][$parr]) ? $projects['cm_dc'][$parr] : '';
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
						#echo isset($projects['cm_irval'][$parr]) ? round(($projects['cm_irval'][$parr]/$totCM_DC)*100, 2) : ''; 
						$cm_irval = isset($projects['cm_irval'][$parr]) ? round(($projects['cm_irval'][$parr]/$totCM_DC)*100, 2) : '';
						if(isset($cm_irval) && ($cm_irval != 0)) {
							// echo $projects['cm_irval'][$parr];
						?>
						<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'cmirval'); return false;"><?php echo $cm_irval; ?></a>
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
					<?php echo isset($projects['irval'][$parr]) ? round(($projects['irval'][$parr]/$total_irval)*100, 2) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Effort Variance (%) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['eff_var'][$parr]) ? round(($projects['eff_var'][$parr]/$totEV)*100, 2) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Contribution for the month (%) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['cm_dc'][$parr]) ? round(($projects['cm_dc'][$parr]/$totCM_DC)*100, 2) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Contribution YTD (%) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['dc'][$parr]) ? round(($projects['dc'][$parr]/$totDC)*100, 2) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
</table>
<div class="clearfix"></div>
<div id="drilldown_data" class="" style="margin:20px 0;display:none;">

</div>