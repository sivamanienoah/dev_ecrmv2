<table cellspacing="0" cellpadding="0" border="0" class="data-table proj-dash-table bu-tbl">
	<tr>
		<thead>
			<th>IT Services Dashboard</th>
			<?php if(!empty($practice_data)) { ?>
				<?php foreach($practice_data as $prac) { ?>
					<th><?php echo $prac->practices; ?></th>
					<?php $practice_arr[] = $prac->practices; ?>
				<?php } ?>
			<?php } ?>
		</thead>
	</tr>
	<tr>
		<td><b>Number of Projects currently running</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['practicewise'][$parr]) ? $projects['practicewise'][$parr] : ''; ?>
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
					<?php echo isset($projects['rag_status'][$parr]) ? $projects['rag_status'][$parr] : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>YTD Billing (USD)</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['irval'][$parr]) ? round($projects['irval'][$parr]) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Billable for the month (%)</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['cm_irval'][$parr]) ? round(($projects['cm_irval'][$parr]/$totCM_DC)*100, 2) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Billable YTD (%)</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['irval'][$parr]) ? round(($projects['irval'][$parr]/$total_irval)*100, 2) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Effort Variance (%)</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['eff_var'][$parr]) ? round(($projects['eff_var'][$parr]/$totEV)*100, 2) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Contribution for the month (%)</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['cm_dc'][$parr]) ? round(($projects['cm_dc'][$parr]/$totCM_DC)*100, 2) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<tr>
		<td><b>Contribution YTD (%)</b></td>
		<?php if(!empty($practice_arr)) { ?>
			<?php foreach($practice_arr as $parr) { ?>
				<td align='right'>
					<?php echo isset($projects['dc'][$parr]) ? round(($projects['dc'][$parr]/$totDC)*100, 2) : ''; ?>
				</td>
			<?php } ?>
		<?php } ?>
	</tr>
</table>