<?php
$this->load->helper('custom_helper');
if (get_default_currency()) {
	$default_currency = get_default_currency();
	$default_cur_id = $default_currency['expect_worth_id'];
	$default_cur_name = $default_currency['expect_worth_name'];
} else {
	$default_cur_id = '1';
	$default_cur_name = 'USD';
}
?>
<div id="ad_filter" class="clear">
	<table border="0" cellpadding="0" cellspacing="0" class="data-table lead-table">
		<thead>
			<tr>
				<th>Project No.</th>
				<th>Project Title</th>
				<th>Customer</th>
				<th>Practice</th>
				<th>Entity</th>
				<th>Project Start Date</th>	
				<th>Project End Date</th>	
				<th>Project Status</th>
				<th>Actual Worth (<?php echo $default_cur_name; ?>)</th>
			</tr>
		</thead>
		
			<?php
			if(!empty($res))
			{
				$amt_converted = 0;
				$total = 0;
				foreach ($res as $proj)
				{
			?>
					<tr>
						<td><?php echo empty($proj->invoice_no)?'':$proj->invoice_no; ?></td>
						<td><?php echo empty($proj->lead_title)?'':$proj->lead_title; ?></td>
						<td><?php echo $proj->first_name.' '.$proj->last_name; ?></td>
						<td><?php echo $proj->practices; ?></td>
						<td><?php echo $proj->division_name; ?></td>
						<td><?php echo ($proj->actual_date_start!='') ? date("d-m-Y",strtotime($proj->actual_date_start)) : ''; ?></td>
						<td><?php echo ($proj->actual_date_due!='') ? date("d-m-Y",strtotime($proj->actual_date_due)) : ''; ?></td>
						<td>
							<?php
								switch ($proj->pjt_status)
								{
									case 1:
										echo $status = 'In Progress';
									break;
									case 2:
										echo $status = 'Completed';
									break;
									case 3:
										echo $status = 'Onhold';
									break;
									case 4:
										echo $status = 'Inactive';
									break;
								}
							?>									
						</td>
						<td align = 'right'>
							<?php 
								$amt_converted = conver_currency($proj->actual_worth_amount,$rates[$proj->expect_worth_id][$default_cur_id]);
								$total+=$amt_converted;
								echo empty($proj->actual_worth_amount)?'':$amt_converted;
							?>
						</td>
					</tr>
			<?php 
				}
				?>
				<tfoot>
					<tr>
						<td colspan="8" align="right"><strong>Total (<?php echo $default_cur_name; ?>)</strong></td>
						<td align = 'right'><strong><?php echo $total; ?></strong></td>
					</tr>
				</tfoot>
				<?php
				
			} else{
				?>
				<tr>
					<td colspan = '9' align="center">No result</td>
				</tr>
				<?php 
			}
			?>
	</table>
<br/>
</div>

<?php 
function conver_currency($amount,$val)
{
	return round($amount*$val);
}
?>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript" src="assets/js/report/moved_project_report_view.js"></script>