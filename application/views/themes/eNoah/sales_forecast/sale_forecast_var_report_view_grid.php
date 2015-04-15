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
		<?php foreach($report_data as $lead_id=>$ms_data) { ?>
			<?php foreach($ms_data as $ms_name=>$ms_date) { ?>
				<?php ksort($ms_date); ?>
				<?php foreach($ms_date as $ms_det=>$ms_val) { ?>
					<?php foreach($ms_val as $type=>$val) { ?>
					<?php echo "<pre>"; print_r($ms_val); ?>
						<?php if(in_array($ms_det, $month_no_arr)) { ?>
							<tr>
								<td><?php echo $val['customer']; ?></td>
								<td><?php echo $val['lead_name']; ?></td>
								<td><?php echo $val['ms_name']; ?></td>
								<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
									<?php foreach($month_arr as $mon_number=>$mon_val) { ?>
										<?php if($ms_det==$mon_number) { ?>
											<td align="right">
												<?php if($type == 'F') { ?>
													<?php echo number_format($val['ms_value'], 2, '.', ''); ?>
													<?php #$tot[$ms_date] += $ms_val['ms_value']; ?>
												<?php } ?>
											</td>
											<td align="right">
												<?php if($type == 'A') { ?>
													<?php echo number_format($val['ms_value'], 2, '.', ''); ?>
													<?php #$tot[$ms_date] += $ms_val['ms_value']; ?>
												<?php } ?>
											</td>
										<?php } else { ?>
											<td align="center"></td>
											<td align="center"></td>
										<?php } ?>
									<?php } ?><!-- j for loop-->
								<?php } ?><!-- if condition-->
							</tr>
						<?php } ?><!-- in_array - if condition-->
					<?php } ?><!-- ms_val - foreach-->
				<?php } ?><!-- ms_det foreach loop-->
			<?php } ?><!-- ms_data foreach loop-->
		<?php } ?><!-- foreach loop-->
	</tbody>
	<!--tfoot>
		<tr>
			<td text align=right colspan="3"><strong>Overall Total(<?php echo $default_currency; ?>):</strong></td>
			<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
				<?php foreach($month_arr as $mon_number=>$mon_val) { ?>
					<td align="right">
						<?php echo ($tot[$mon_number]!='') ? number_format($tot[$mon_number],2,'.','') : ''; ?>
					</td>
					<td align="center"> - </td>
				<?php } ?>
			<?php } ?>
		</tr>
	</tfoot-->
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