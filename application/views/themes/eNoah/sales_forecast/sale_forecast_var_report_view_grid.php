<?php $this->load->helper('text'); ?>
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th rowspan=2>Customer</th>
			<th rowspan=2>Lead/Project Name</th>
			<th rowspan=2>Milestone</th>						
			<?php
				$i = date("Y-m", strtotime($current_month)); 
				while($i <= date("Y-m", strtotime($highest_month))) {
			?>
					<th colspan=2><?php echo date('M', strtotime($i)); ?></th>
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
		<?php #echo "<pre>"; print_r($report_data); exit; ?>
		<?php foreach($report_data as $lead_id=>$ms_data) { ?>
			<?php foreach($ms_data as $ms_name=>$ms_value) { ?>
				<?php foreach($ms_value as $ms_date=>$ms_val) { ?>
					<?php if(in_array($ms_date, $month_no_arr)) { ?>
						<tr>
							<td><?php echo $ms_val['customer']; ?></td>
							<td><?php echo $ms_val['lead_name']; ?></td>
							<td><?php echo $ms_name; ?></td>
							<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
								<?php foreach($month_arr as $mon_number=>$mon_val) { ?>
									<?php if($ms_date==$mon_number) { ?>
										<td align="<?php echo isset($ms_val['F']) ? 'right' : 'center'; ?>">
											<?php echo isset($ms_val['F']) ? number_format($ms_val['F'], 2, '.', '') : '-'; ?>
											<?php $tot['F'][$mon_number] += $ms_val['F']; ?>
										</td>
										<td align="<?php echo isset($ms_val['A']) ? 'right' : 'center'; ?>">
											<?php echo isset($ms_val['A']) ? number_format($ms_val['A'], 2, '.', '') : '-'; ?>
											<?php $tot['A'][$mon_number] += $ms_val['A']; ?>
										</td>
									<?php } else { ?>
										<td align="center">-</td>
										<td align="center">-</td>
									<?php } ?>
								<?php } ?><!-- month_arr foreach loop-->
							<?php } ?><!-- if condition-->
						</tr>
					<?php } ?><!-- in_array - if condition-->
				<?php } ?><!-- ms_value foreach loop-->
			<?php } ?><!-- ms_data foreach loop-->
		<?php } ?><!-- report_data foreach loop-->
	</tbody>
	<tfoot>
		<tr>
			<td text align=right colspan="3"><strong>Overall Total(<?php echo $default_currency; ?>):</strong></td>
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