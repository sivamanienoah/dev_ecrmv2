<?php $this->load->helper('text'); ?>
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th rowspan=2 style="text-align:center;">Entity</th>
			<th rowspan=2 style="text-align:center;">Customer</th>
			<th rowspan=2 style="text-align:center;">Lead/Project Name</th>
			<th rowspan=2 style="text-align:center;">Milestone</th>						
			<?php
				$i = date("Y-m", strtotime($current_month)); 
				while($i <= date("Y-m", strtotime($highest_month))) {
			?>
					<th colspan=2 style="text-align:center;"><?php echo date('M', strtotime($i)); ?></th>
			<?php
					$month_arr[date('Y-m', strtotime($i))] = date('Y-M', strtotime($i));
					$month_no_arr[]                        = date('Y-m', strtotime($i));
					
					if(substr($i, 5, 2) == "12")
					$i = (date("Y", strtotime($i."-01")) + 1)."-01";
					else
					$i++;
				}
			?>
			<tr>
				<?php for($a=0;$a<count($month_arr);$a++) { ?>
					<th>Forecast</th>
					<th>Actual</th>
				<?php } ?>
			</tr>
		</tr>
	</thead>
	<tbody>
		<?php $tot = array(); ?>
		<?php #echo "<pre>"; print_r($report_data); ?>
		<?php foreach($report_data as $lead_id=>$ms_data) { ?>
			<?php foreach($ms_data as $ms_name=>$ms_value) {    ?>
				<tr>
					<td><?php echo $ms_value['entity']; ?></td>
					<td><?php echo $ms_value['customer']; ?></td>
					<td><?php echo $ms_value['lead_name']; ?></td>
					<td><?php echo $ms_name; ?></td>
					<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
						<?php foreach($month_arr as $mon_number=>$mon_val) { ?>
							<?php if(array_key_exists($mon_number, $ms_value)) { ?>
								<td align="<?php echo isset($ms_value[$mon_number]['F']) ? 'right' : 'center'; ?>">
									<?php echo isset($ms_value[$mon_number]['F']) ? number_format($ms_value[$mon_number]['F'], 2, '.', '') : '-'; ?>
									<?php $tot['F'][$mon_number] += $ms_value[$mon_number]['F']; ?>
								</td>
								<td align="<?php echo isset($ms_value[$mon_number]['A']) ? 'right' : 'center'; ?>">
									<?php echo isset($ms_value[$mon_number]['A']) ? number_format($ms_value[$mon_number]['A'], 2, '.', '') : '-'; ?>
									<?php $tot['A'][$mon_number] += $ms_value[$mon_number]['A']; ?>
								</td>
							<?php } else { ?>
								<td align="center">-</td>
								<td align="center">-</td>
							<?php } ?>
						<?php } ?><!-- month_arr foreach loop-->
					<?php } ?><!-- if condition-->
				</tr>
			<?php } ?><!-- ms_data foreach loop-->
		<?php } ?><!-- report_data foreach loop-->
	</tbody>
	<tfoot>
		<tr>
			<td text align=right colspan="4"><strong>Overall Total(<?php echo $default_currency; ?>):</strong></td>
			<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
				<?php foreach($month_arr as $mon_number=>$mon_val) { ?>
					<td align="right">
						<?php echo ($tot['F'][$mon_number]!='') ? number_format($tot['F'][$mon_number],2,'.','') : ''; ?>
					</td>
					<td align="right">
						<?php echo ($tot['A'][$mon_number]!='') ? number_format($tot['A'][$mon_number],2,'.','') : ''; ?>
					</td>
				<?php } ?>
			<?php } ?>
		</tr>
	</tfoot>
</table>
<script>
$(function(){
	$('.data-tbl').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 25,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": false,
		"bFilter": true,
		"bAutoWidth": false
	});
});
</script>