<?php $this->load->helper('text'); ?>
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th>Customer</th>
			<th>Lead/Project Name</th>
			<th>Milestone</th>
			<?php
				// $from_month = $current_month;
				// $current_month = date('Y-m');
				// $k = 1;
				// for($i=$from_month; $i<=date('Y-m', strtotime($highest_month)); $i++) {
			?>
					<!--<th><?php #echo date('M', strtotime($from_month)); ?></th>-->
					
			<?php
					// $month_arr[date('Y-m', strtotime($from_month))] = date('Y-M', strtotime($from_month));
					// $month_arr[] = date('M', strtotime($current_month));
					// $current_month = date('Y-m', strtotime('+'.$k.' month'));
					// $from_month = date('Y-m', strtotime('+'.$k.' month', strtotime($from_month)));
					// if(substr($i, 5, 2) == "12")
					// $i = (date("Y", strtotime($i."-01")) + 1)."-01";
					
					// $from_month = date('Y-m', strtotime('+1 month', strtotime($from_month)));
					
				// }
			?>
			<?php
				$i = date("Y-m", strtotime($current_month)); 
				while($i <= date("Y-m", strtotime($highest_month))) {
			?>
					<th><?php echo date('M', strtotime($i)); ?></th>
			<?php
					$month_arr[date('Y-m', strtotime($i))] = date('Y-M', strtotime($i));
					$month_no_arr[] = date('Y-m', strtotime($i));
					
					if(substr($i, 5, 2) == "12")
					$i = (date("Y", strtotime($i."-01")) + 1)."-01";
					else
					$i++;
				}
			?>
		</tr>
	</thead>
	<tbody>
		<?php $tot = array(); ?>
		<?php foreach($report_data as $fc_data=>$ms_data) { ?>
			<?php ksort($ms_data); ?>
			<?php foreach($ms_data as $mon_no=>$ms_det) { ?>
				<?php foreach($ms_det as $ms=>$ms_val) { ?>
					<?php if(in_array($mon_no, $month_no_arr)) { ?>
						<tr>
							<td><?php echo $ms_val['customer']; ?></td>
							<td><?php echo $ms_val['lead_name']; ?></td>
							<td><?php echo $ms_val['ms_name']; ?></td>
							<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
								<?php foreach($month_arr as $mon_number=>$mon_val) { ?>
									<?php if($mon_no==$mon_number) { ?>
										<td align="right">
											<?php echo number_format($ms_val['ms_value'],2,'.',''); ?>
											<?php 
												$tot[$mon_no] += $ms_val['ms_value'];
											?>
										</td>
									<?php } else { ?>
										<td align="center"><?php echo '-'; ?></td>
									<?php } ?>
								<?php } ?><!-- j for loop-->
							<?php } ?><!-- if condition-->
						</tr>
					<?php } ?><!-- in_array - if condition-->
				<?php } ?><!-- ms_det foreach loop-->
			<?php } ?><!-- ms_data foreach loop-->
		<?php } ?><!-- foreach loop-->
	</tbody>
	<tfoot>
		<tr>
			<td text align=right colspan="3"><strong>Overall Total(<?php echo $default_currency; ?>):</strong></td>
			<?php if(is_array($month_arr) && count($month_arr)>0) { ?>
				<?php foreach($month_arr as $mon_number=>$mon_val) { ?>
					<td align="right">
						<?php echo ($tot[$mon_number]!='') ? number_format($tot[$mon_number],2,'.','') : ''; ?>
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
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false,	
	});
});
</script>